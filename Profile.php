<?php

class Profile implements Serializable {
	private $merchant_id;
	private $api_key;
	private $show_confirmation_page;
	private $url_payment_success;
	private $url_payment_cancel;
	private $save_user_cookie;
	private $environment;

	public function __construct( $environment ) {
		$this->set_environment( $environment );
	}

	public function get_merchant_id() {
		return $this->merchant_id;
	}

	public function set_merchant_id( $merchant_id ) {
		$this->merchant_id = $merchant_id;

		return $this;
	}

	public function get_api_key() {
		return $this->api_key;
	}

	public function set_api_key( $api_key ) {
		$this->api_key = $api_key;

		return $this;
	}

	public function get_show_confirmation_page() {
		return $this->show_confirmation_page;
	}

	public function is_show_confirmation_page() {
		return $this->show_confirmation_page === '1';
	}

	public function set_show_confirmation_page( $show_confirmation_page ) {
		$this->show_confirmation_page = $show_confirmation_page;

		return $this;
	}

	public function get_url_payment_success() {
		return $this->url_payment_success;
	}

	public function set_url_payment_success( $url_payment_success ) {
		$this->url_payment_success = $url_payment_success;

		return $this;
	}

	public function get_url_payment_cancel() {
		return $this->url_payment_cancel;
	}

	public function set_url_payment_cancel( $url_payment_cancel ) {
		$this->url_payment_cancel = $url_payment_cancel;

		return $this;
	}

	public function get_save_user_cookie() {
		return $this->save_user_cookie;
	}

	public function is_save_user_cookie() {
		return $this->save_user_cookie === '1';
	}

	public function set_save_user_cookie( $save_user_cookie ) {
		$this->save_user_cookie = $save_user_cookie;

		return $this;
	}

	public function get_environment() {
		return $this->environment;
	}

	public function set_environment( $environment ) {
		$this->environment = $environment;

		return $this;
	}

	public function serialize() {
		return json_encode( get_object_vars( $this ) );
	}

	public function unserialize( $serialized ) {
		$data = json_decode( $serialized, true );
		foreach ( $data as $key => $value ) {
			$this->{$key} = $value;
		}

		return $this;
	}
}
