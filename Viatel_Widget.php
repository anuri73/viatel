<?php

class Viatel_Widget {
	public $name;
	public $name_label;
	public $email;
	public $email_label;
	public $phone;
	public $phone_label;
	public $account_number;
	public $account_number_label;
	public $approve_top_up = true;
	public $approve_top_up_label;
	public $approve_automatic_top_up = true;
	public $approve_automatic_top_up_label;
	public $packages_label;
	public $packages;
	public $currency;
	public $locale;
	public $env;

	public $merchant_id;
	public $success_url;
	public $cancel_url;
	public $api_key;
	public $order_number;
	public $show_confirmation_page;

	public $verification;
	public $nonce;

	public $amount;

	public $vat_amount_included = 0;

	private $validation_errors = [];

	public static function get_instance( $attrs ) {
		$result = new self();
		$result->set_attributes( $attrs );
		if ( array_key_exists( 'packages', $attrs ) ) {
			$result->set_packages_from_str( $attrs['packages'] );
		}

		return $result;
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
		$this->merchant_id            = $profile->get_merchant_id();
		$this->success_url            = $profile->get_url_payment_success();
		$this->cancel_url             = $profile->get_url_payment_cancel();
		$this->api_key                = $profile->get_api_key();
		$this->show_confirmation_page = $profile->get_show_confirmation_page();
	}

	public function set_packages_from_str( $packages ) {
		$packages       = explode( ')(', trim( $packages, ')(' ) );
		$this->packages = [];
		foreach ( $packages as $package ) {
			$attrs            = shortcode_parse_atts( $package );
			$package          = Package::get_instance( $attrs );
			$this->packages[] = $package;
		}

		return $this;
	}

	public function get_selected_package() {
		foreach ( $this->packages as $package ) {
			if ( $package->amount === $this->amount ) {
				return $package;
			}
		}

		return null;
	}
}
