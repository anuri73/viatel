<?php

class Log {
	
	private $viatel;

	public function __construct( Viatel $viatel ) {
		$this->viatel = $viatel;
	}

	function init() {
		add_action( 'init', [ $this, 'register_post_type' ] );
	}

	private function register_post_type() {
		register_post_type(
			'viatel_log',
			// CPT Options
			[
				'labels'      => [
					'name'          => __( 'Viatel log' ),
					'singular_name' => __( 'Viatel log' ),
				],
				'public'      => false,
				'has_archive' => false,
				'rewrite'     => [ 'slug' => 'viatel_log' ],
			]
		);
	}
}
