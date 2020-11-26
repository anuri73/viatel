<?php

class Viatel {
	public $config;
	public $action;
	public $view;
	public $asset;
	public $short_code;
	private $log;

	public function __construct() {
		$this->config     = new Config( $this );
		$this->action     = new Action( $this );
		$this->view       = new View( $this );
		$this->asset      = new Asset( $this );
		$this->short_code = new ShortCode( $this );
		$this->log        = new Log( $this );
	}

	public function init() {
		$this->action->init_actions();
		$this->short_code->init();
	}

	public function get_profiles() {
		$defaultData = [
			'Production' => '{}',
			'Stage'      => '{}',
		];

		$data = get_option(
			$this->config->get_site_option_name(),
			$defaultData
		);

		return array_map_assoc( static function ( $environment, $profile ) {
			return [
				$environment,
				( new Profile( $environment ) )->unserialize( $profile ),
			];
		}, array_merge( $defaultData, $data ) );
	}

	public function get_environment_config( $env ) {
		$profiles = $this->get_profiles();

		return $profiles[ $env ];
	}
}
