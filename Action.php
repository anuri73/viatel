<?php

class Action {
	private $viatel;

	public function __construct( Viatel $viatel ) {
		$this->viatel = $viatel;
	}

	public function init_actions() {
		add_action( 'admin_menu', [ $this, 'add_tools_menu' ] );
		add_action( 'wp_ajax_viatel_save_profile', [ $this, 'ajax_save_profile' ] );
	}

	public function add_tools_menu() {
		add_management_page(
			$this->viatel->config->get_plugin_title(),
			$this->viatel->config->get_plugin_title(),
			'manage_options',
			$this->viatel->config->get_core_slug(),
			[ $this->viatel->view, 'render_settings' ]
		);
	}

	public function ajax_save_profile() {
		$this->check_ajax_referer( 'viatel_save_profile' );
		parse_str( urldecode( $_POST['profile'] ), $profile );

		$profile = $this->set_profile( $profile['profile'] );

		$validation_errors = $this->validate_profile( $profile );

		if ( count( $validation_errors ) ) {
			wp_send_json(
				[
					'success' => false,
					'errors'  => $validation_errors,
				],
				200
			);
		} else {
			$old_settings                                = get_option(
				$this->viatel->config->get_site_option_name(),
				[]
			);
			$old_settings[ $profile->get_environment() ] = $profile->serialize();
			update_option( $this->viatel->config->get_site_option_name(), $old_settings );
			wp_send_json(
				[
					'success' => true,
					'errors'  => [],
				],
				200
			);
		}
	}

	public function check_ajax_referer( $action ) {
		$result = check_ajax_referer( $action, 'nonce', false );

		if ( false === $result ) {
			$return = array(
				'wpmdb_error' => 1,
				'body'        => sprintf( __( 'Invalid nonce for: %s', 'wp-migrate-db' ), $action ),
			);
			$this->end_ajax( json_encode( $return ) );
		}

		$cap = ( is_multisite() ) ? 'manage_network_options' : 'export';
		$cap = apply_filters( 'wpmdb_ajax_cap', $cap );

		if ( ! current_user_can( $cap ) ) {
			$return = array(
				'wpmdb_error' => 1,
				'body'        => sprintf( __( 'Access denied for: %s', 'wp-migrate-db' ), $action ),
			);
			$this->end_ajax( json_encode( $return ) );
		}
	}

	function end_ajax( $return = false ) {
		$return = apply_filters( 'wpmdb_before_response', $return );

		echo ( false === $return ) ? '' : $return;
		exit;
	}

	private function set_profile( $environment ) {
		return ( new Profile( $environment ) )
			->set_merchant_id( $environment['merchant_id'] )
			->set_api_key( $environment['api_key'] )
			->set_show_confirmation_page( sanitize_key( $environment['show_confirmation_page'] ) )
			->set_url_payment_success( esc_url_raw( $environment['url_payment_success'] ) )
			->set_url_payment_cancel( esc_url_raw( $environment['url_payment_cancel'] ) )
			->set_save_user_cookie( sanitize_key( $environment['save_user_cookie'] ) )
			->set_environment( $environment['environment'] );
	}

	private function validate_profile( Profile $profile ) {
		return array_filter_recursive( [
			'merchant_id'         => [
				'Required field'         => empty( $profile->get_merchant_id() ),
				'Please, enter a number' => is_int( $profile->get_merchant_id() ),
			],
			'api_key'             => [
				'Required field' => empty( $profile->get_api_key() ),
			],
			'url_payment_success' => [
				'Required field' => empty( $profile->get_url_payment_success() ),
			],
			'url_payment_cancel'  => [
				'Required field' => empty( $profile->get_url_payment_cancel() ),
			],
			'common'              => [
				'Invalid Merchant' => $this->validate_merchant(
					$profile
				),
			],
		] );
	}

	private function validate_merchant( Profile $profile ) {
		$nonce        = time();
		$verification = implode( '&', [
			$profile->get_merchant_id(),
			$nonce,
			$profile->get_api_key(),
		] );
		$response     = wp_remote_post( $this->viatel->config->get_validation_url( $profile->get_environment() ), [
				'method'  => 'POST',
				'headers' => [
					'Content-Type' => 'application/json',
				],
				'body'    => json_encode( [
					"MerchantId"   => (int) $profile->get_merchant_id(),
					"Verification" => base64_encode( hash( 'sha256', $verification, true ) ),
					"nonce"        => $nonce,
				] ),
			]
		);

		return 200 !== $response['response']['code'];
	}
}
