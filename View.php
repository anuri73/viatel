<?php

class View {
	private $viatel;

	public function __construct( Viatel $viatel ) {
		$this->viatel = $viatel;
	}

	public function render_settings() {
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
}
