<?php

class ShortCode {
	private $viatel;

	private $errors = [];

	public function __construct( Viatel $viatel ) {
		$this->viatel = $viatel;
	}

	public function render_shortcode( $attrs = [], $content = null, $tag = '' ) {

		$attributes = shortcode_atts(
			[
				'name_label'                     => null,
				'email_label'                    => null,
				'phone_label'                    => null,
				'account_number_label'           => null,
				'approve_top_up_label'           => null,
				'approve_automatic_top_up_label' => null,
				'packages_label'                 => null,
				'packages'                       => null,
				'currency'                       => null,
				'locale'                         => null,
				'env'                            => null,
			],
			$attrs
		);

		$attributes = array_change_key_case( $attributes, CASE_LOWER );

		$widget = Viatel_Widget::get_instance( $attributes );

		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			$response = $this->create_order( $widget );
			if ( array_key_exists( 'response', $response ) && $response['response']['code'] === 200 ) {
				$body = json_decode( $response['body'], true );
				echo $body['htmlSnippet'];

				return;
			}
		}

		if ( ! $widget->validate() ) {
			$this->errors = array_merge( $this->errors, $widget->get_validation_errors() );
		}

		$this->viatel->view->render_shortcode( [
			'widget' => $widget,
			'errors' => $this->errors,
		] );
	}

	public function init() {
		add_shortcode( 'viatel', [ $this, 'render_shortcode' ] );
		add_action( 'pre_post_update', [ $this, 'validate_post_short_code_attributes' ], 10, 2 );
	}

	private function create_order( Viatel_Widget $viatel_widget ) {

		$viatel = $_POST['viatel'];
		if ( ! wp_verify_nonce( $viatel['_wpnonce'], 'viatel_create_order' ) ) {
			$this->errors[] = _( 'Did not save because your form seemed to be invalid.' );
		}

		$viatel_widget->set_attributes( $viatel );

		if ( ! $viatel_widget->validate() ) {

			$this->errors = array_merge( $this->errors, $viatel_widget->get_validation_errors() );

			return [];
		}

		$profile = $this->viatel->get_environment_config( $viatel_widget->env );

		$viatel_widget->set_profile_values( $profile );

		return $this->make_create_order_request( $viatel_widget );
	}

	private function make_create_order_request( Viatel_Widget $viatel_widget ) {
		$nonce                = time();
		$viatel_widget->nonce = $nonce;
		$package              = $viatel_widget->get_selected_package();
		$verification         = implode( '&', [
			(int) $viatel_widget->merchant_id,
			(string) $viatel_widget->account_number,
			(int) $package->amount,
			(int) $package->value,
			(string) $viatel_widget->currency,
			$viatel_widget->approve_top_up ? 'true' : 'false',
			$viatel_widget->approve_automatic_top_up ? 'true' : 'false',
			(string) $viatel_widget->success_url,
			(string) $viatel_widget->cancel_url,
			(string) $viatel_widget->nonce,
			(string) $viatel_widget->api_key,
		] );
		$request              = json_encode( [
			"MerchantId"            => (int) $viatel_widget->merchant_id,
			"OrderNumber"           => ! empty( $viatel_widget->order_number ) ? (string) $viatel_widget->order_number : null,
			"AccountNumber"         => ! empty( $viatel_widget->account_number ) ? (string) $viatel_widget->account_number : null,
			"Amount"                => (int) $package->amount,
			"Value"                 => (int) $package->value,
			"Currency"              => (string) $viatel_widget->currency,
			"Locale"                => (string) $viatel_widget->locale,
			"OrderText"             => (string) $package->order_text,
			"CancelUrl"             => ! empty( $viatel_widget->cancel_url ) ? (string) $viatel_widget->cancel_url : null,
			"SuccessUrl"            => (string) $viatel_widget->success_url,
			"Verification"          => base64_encode( hash( 'sha256', $verification, true ) ),
			"Nonce"                 => $nonce,
			"Name"                  => ! empty( $viatel_widget->name ) ? (string) $viatel_widget->name : null,
			"Email"                 => ! empty( $viatel_widget->email ) ? (string) $viatel_widget->email : null,
			"PhoneNumber"           => ! empty( $viatel_widget->phone ) ? (string) $viatel_widget->phone : null,
			"ApproveTopUp"          => $viatel_widget->approve_top_up,
			"ApproveAutomaticTopUp" => $viatel_widget->approve_automatic_top_up,
			"Extra"                 => ! empty( $package->extra ) ? (string) $package->extra : null,
			"VATAmountIncluded"     => $viatel_widget->vat_amount_included,
			"ConfirmationPage"      => $viatel_widget->show_confirmation_page === '1' ? "true" : "false",
		] );

		return wp_remote_post( $this->viatel->config->get_create_order_url( $viatel_widget->env ),
			[
				'method'  => 'POST',
				'headers' => [
					'Content-Type' => 'application/json',
				],
				'body'    => $request,
			]
		);
	}

	public function get_mandatory_attributes() {
		return [
			'packages',
			'currency',
			'locale',
			'env',
		];
	}

	public function get_mandatory_package_attributes() {
		return [
			'amount',
			'main_text',
		];
	}

	public function validate_post_short_code_attributes( $post_id, $post_data ) {

		# If this is just a revision, don't do anything.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		preg_match_all( '/(\[viatel\s([\S\s]+(?R)?)])/', $post_data['post_content'], $matches );

		if ( array_key_exists( 2, $matches ) ) {
			$attrs = shortcode_parse_atts( $matches[2][0] );
			try {
				$this->validate_required_attributes( $attrs );
				$this->validate_packages( $attrs['packages'] );
			} catch ( LogicException $exception ) {
				# Add a notification
				update_option(
					'viatel_notifications',
					json_encode( [ 'error', $exception->getMessage() ] )
				);
				# And redirect
				if ( wp_safe_redirect( get_edit_post_link( $post_id, 'redirect' ) ) ) {
					exit;
				}
			}
		}
	}

	private function validate_required_attributes( array $attrs ) {
		$attrs          = array_merge( array_fill_keys( $this->get_mandatory_attributes(), null ), $attrs );
		$required_attrs = array_diff( $attrs, array_filter( $attrs ) );
		if ( count( $required_attrs ) ) {
			throw new LogicException( sprintf(
				"Viatel attributes '%s' are mandatory",
				implode( ', ', array_keys( $required_attrs ) )
			) );
		}
	}

	private function validate_packages( $packages ) {
		$packages = explode( ')(', trim( $packages, ')(' ) );
		foreach ( $packages as $package ) {
			$attrs = shortcode_parse_atts( $package );
			$this->validate_package_required_attributes( $attrs );
		}
	}

	private function validate_package_required_attributes( array $attrs ) {
		$attrs          = array_merge( array_fill_keys( $this->get_mandatory_package_attributes(), null ), $attrs );
		$required_attrs = array_diff( $attrs, array_filter( $attrs ) );
		if ( count( $required_attrs ) ) {
			throw new LogicException( sprintf(
				"Package attributes '%s' are mandatory",
				implode( ', ', array_keys( $required_attrs ) )
			) );
		}
	}
}
