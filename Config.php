<?php

class Config {
	private $core_slug = 'wp-viatel';
	private $plugin_file_path = __FILE__;
	private $plugin_version = '1.0';
	private $viatel;

	public function __construct( Viatel $viatel ) {
		$this->viatel = $viatel;
	}

	public function get_plugin_title() {
		return __( 'Viatel AgentLine Checkout Settings', 'wp-viatel' );
	}

	public function get_plugin_version() {
		return $this->plugin_version;
	}

	public function get_core_slug() {
		return $this->core_slug;
	}

	public function get_plugin_file_path() {
		return $this->plugin_file_path;
	}

	public function get_plugin_dir_path() {
		return plugin_dir_path( $this->get_plugin_file_path() );
	}

	public function get_template_dir() {
		return $this->get_dir( 'template' );
	}

	public function get_dir( $dir ) {
		return implode( DIRECTORY_SEPARATOR, [
			untrailingslashit( $this->get_plugin_dir_path() ),
			untrailingslashit( $dir ),
		] );
	}

	public function get_site_option_name() {
		return 'viatel_config';
	}

	public function get_validation_url( $env ) {
		return implode( '/', [
			trim( $this->get_api_url( $env ), '/\\' ),
			trim( 'AgentLine/ValidateCredentials', '/\\' ),
		] );
	}

	public function get_create_order_url( $env ) {
		return implode( '/', [
			trim( $this->get_api_url( $env ), '/\\' ),
			trim( 'AgentLine/CreateOrder', '/\\' ),
		] );
	}

	public function get_api_url( $env ) {
		switch ( $env ) {
			case 'Stage':
				return 'https://stage-checkout.viatel.se/api/v1';
			case 'Production':
				return 'https://checkout.viatel.se/api/AgentLine/v1';
		}
		throw new LogicException( 'Unsupported environment' );
	}
}
