<?php

class Log {

	private $viatel;

	public function __construct( Viatel $viatel ) {
		$this->viatel = $viatel;
	}

	function init() {
		add_action( 'init', [ $this, 'register_post_type' ] );
	}

	public function register_post_type() {
		register_post_type(
			'viatel_log_production',
			// CPT Options
			[
				'labels'      => [
					'name'          => __( 'Viatel production log' ),
					'singular_name' => __( 'Viatel production log' ),
				],
				'public'      => false,
				'has_archive' => false,
				'rewrite'     => [ 'slug' => 'viatel_log_production' ],
			]
		);
		register_post_type(
			'viatel_log_stage',
			// CPT Options
			[
				'labels'      => [
					'name'          => __( 'Viatel stage log' ),
					'singular_name' => __( 'Viatel stage log' ),
				],
				'public'      => false,
				'has_archive' => false,
				'rewrite'     => [ 'slug' => 'viatel_log_stage' ],
			]
		);
	}

	public function log( $data, $env ) {
		wp_insert_post(
			[
				'post_content'      => base64_encode( wp_json_encode(
					$data
				) ),
				'post_type'         => 'viatel_log_' . $env,
				'post_date_gmt'     => gmdate( DATE_ATOM ),
				'post_modified_gmt' => gmdate( DATE_ATOM ),
				'post_status'       => 'closed',
				'comment_status'    => 'closed',
			]
		);
	}

	public function get_transactions( $env ) {
		$logs = get_posts( [
			'numberposts'      => 250,
			'post_type'        => 'viatel_log_' . $env,
			'post_status'      => 'closed',
			'suppress_filters' => false,
		] );

		return array_map( static function ( $log ) {
			$log_content = json_decode( base64_decode( $log->post_content ), true );

			$request = json_decode( $log_content['request'], true );

			$response = preg_replace(
				'/\s+/',
				'',
				htmlspecialchars_decode(
					$log_content['response']['body']
				)
			);

			$response = json_decode( $response, true );

			return [
				'id'        => $log->ID,
				'customer'  => implode( array_filter( [
					isset( $request['Name'] ) ? $request['Name'] : null,
					isset( $request['Email'] ) ? $request['Email'] : null,
				] ) ),
				'account'   => isset( $request['AccountNumber'] ) ? $request['AccountNumber'] : null,
				'order'     => isset( $request['OrderNumber'] ) ? $request['OrderNumber'] : null,
				'amount'    => isset( $request['Amount'] ) ? $request['Amount'] : null,
				'result'    => isset( $response['resultCode'] ) ? $response['resultCode'] : $log_content['response']['response']['code'],
				'sessionId' => isset( $response['sessionId'] ) ? $response['sessionId'] : null,
			];
		}, $logs );
	}
}
