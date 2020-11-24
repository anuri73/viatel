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
				'customer_name_label'    => null,
				'customer_email_label'   => null,
				'customer_phone_label'   => null,
				'account_number_label'   => null,
				'top_up_label'           => null,
				'automatic_top_up_label' => null,
				'package_label'          => null,
				'package_count'          => null,
				'packages'               => null,
				'currency'               => null,
				'locale'                 => null,
				'env'                    => null,
			],
			$attrs
		);

		$this->validate_required_attributes( $attrs );

		$attrs = array_change_key_case( (array) $attrs, CASE_LOWER );

		$widget = Viatel_Widget::get_instance( $attrs );

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
		$verification         = implode( '&', [
			(int) $viatel_widget->merchant_id,
			(string) $viatel_widget->account_number,
			(int) $viatel_widget->amount,
			(int) $viatel_widget->value,
			(string) $viatel_widget->currency,
			$viatel_widget->approve_top_up ? 'true' : 'false',
			$viatel_widget->approve_automatic_top_up ? 'true' : 'false',
			(string) $viatel_widget->successUrl,
			(string) $viatel_widget->cancel_url,
			(string) $viatel_widget->nonce,
			(string) $viatel_widget->api_key,
		] );
		$request              = json_encode( [
			"MerchantId"            => (int) $viatel_widget->merchant_id,
			"OrderNumber"           => ! empty( $viatel_widget->order_number ) ? (string) $viatel_widget->order_number : null,
			"AccountNumber"         => ! empty( $viatel_widget->account_number ) ? (string) $viatel_widget->account_number : null,
			"Amount"                => (int) $viatel_widget->amount,
			"Value"                 => (int) $viatel_widget->value,
			"Currency"              => (string) $viatel_widget->currency,
			"Locale"                => (string) $viatel_widget->locale,
			"OrderText"             => (string) $viatel_widget->order_text,
			"CancelUrl"             => ! empty( $viatel_widget->cancel_url ) ? (string) $viatel_widget->cancel_url : null,
			"SuccessUrl"            => (string) $viatel_widget->successUrl,
			"Verification"          => base64_encode( hash( 'sha256', $verification, true ) ),
			"Nonce"                 => $nonce,
			"Name"                  => ! empty( $viatel_widget->name ) ? (string) $viatel_widget->name : null,
			"Email"                 => ! empty( $viatel_widget->email ) ? (string) $viatel_widget->email : null,
			"PhoneNumber"           => ! empty( $viatel_widget->phone_number ) ? (string) $viatel_widget->phone_number : null,
			"ApproveTopUp"          => $viatel_widget->approve_top_up,
			"ApproveAutomaticTopUp" => $viatel_widget->approve_automatic_top_up,
			"Extra"                 => ! empty( $viatel_widget->extra ) ? (string) $viatel_widget->extra : null,
			"VATAmountIncluded"     => $viatel_widget->vat_amount_included,
			"ConfirmationPage"      => $viatel_widget->confirmation_page,
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

	private function validate_required_attributes( array $attrs ) {

	}

	public function get_mandatory_attributes() {
		return [
			'package_count',
			'packages',
			'currency',
			'locale',
			'env',
		];
	}
}
