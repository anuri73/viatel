<?php

class View {
	private $viatel;

	public function __construct( Viatel $viatel ) {
		$this->viatel = $viatel;
	}

	public function render_settings() {
		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			if ( isset( $_POST['send-log'] ) ) {
				$this->send_logs();
			}
		}
		$this->render( 'settings' );
	}

	public function render_shortcode( $args ) {
		$this->render( 'shortcode', '', $args );
	}

	public function render( $template, $dir = '', $args = [] ) {
		extract( $args, EXTR_OVERWRITE );
		$dir = ( ! empty( $dir ) ) ? untrailingslashit( $dir ) : $dir;
		include implode( DIRECTORY_SEPARATOR, [
			untrailingslashit( $this->viatel->config->get_template_dir() ),
			untrailingslashit( $dir ),
			untrailingslashit( $template . '.php' ),
		] );
	}

	private function send_logs() {
		$env            = $_POST['send-log']['env'];
		$transactions   = get_posts( [
			'numberposts' => 250,
			'post_type'   => 'viatel_log_' . strtolower( $env ),
			'post_status' => 'closed',
		] );
		$temp_file_name = tempnam( "/tmp", "viatel_log" );

		$handle = fopen( $temp_file_name, "a" );
		foreach ( $transactions as $transaction ) {
			fwrite( $handle, implode( '|', [
				$transaction
			] ) );
		}
		fclose( $handle );
		wp_mail(
			'api-support@viatel.se',
			'Logs',
			'asdasd'
		);
	}
}
