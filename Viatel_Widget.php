<?php

class Viatel_Widget {
	public $customer_name;
	public $merchant_id;
	public $api_key;
	public $order_number;
	public $account_number;
	public $amount = 100;
	public $value = 10000;
	public $currency = 'SEK';
	public $locale = "sv-se";
	public $order_text = "OrderText";
	public $cancel_url;
	public $successUrl;
	public $verification;
	public $nonce;
	public $name;
	public $email;
	public $phone_number;
	public $approve_top_up = true;
	public $approve_automatic_top_up = true;
	public $extra;
	public $vat_amount_included = 0;
	public $confirmation_page;
	public $env;
	private $validation_errors = [];

	public static function get_instance( $attrs ) {
		$result = new self();

		return $result->set_attributes( $attrs );
	}

	public function set_attributes( $attrs ) {
		foreach ( $attrs as $k => $v ) {
			$this->$k = $v;
		}

		return $this;
	}

	public function validate() {
		return $this->validate_required();
	}

	public function validate_required() {
		$required_fields = [
			'env',
		];
		$result          = true;

		foreach ( $required_fields as $required_field ) {
			if ( empty( $this->{$required_field} ) ) {
				$this->add_required_property_validation_error( $required_field );

				$result = false;
			}
		}

		return $result;
	}

	private function add_required_property_validation_error( $required_field ) {
		$this->validation_errors[] = sprintf( "Property '%s' is required", $required_field );
	}

	public function clear_validation_errors() {
		$this->validation_errors = [];
	}

	public function get_validation_errors() {
		return $this->validation_errors;
	}

	public function set_profile_values( Profile $profile ) {
		$this->merchant_id = $profile->get_merchant_id();
		$this->successUrl  = $profile->get_url_payment_success();
		$this->cancel_url  = $profile->get_url_payment_cancel();
		$this->api_key     = $profile->get_api_key();
	}
}
