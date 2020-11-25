<?php

class Package {
	public $order_text;
	public $amount;
	public $value;
	public $main_text;
	public $sub_text;
	public $extra;

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
}
