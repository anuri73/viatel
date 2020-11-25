<?php
/** @var View $this */
/** @var Profile[] $viatel_profiles */
$viatel_profiles = $this->viatel->get_profiles();

wp_enqueue_style(
	$this->viatel->asset->get_asset_name( 'setting' ),
	$this->viatel->asset->get_asset_path( 'setting.css' ),
	[],
	$this->viatel->config->get_plugin_version()
);
wp_enqueue_script(
	$this->viatel->asset->get_asset_name( 'setting' ),
	$this->viatel->asset->get_asset_path( 'setting.js' ),
	[],
	$this->viatel->config->get_plugin_version()
);
wp_localize_script(
	$this->viatel->asset->get_asset_name( 'setting' ),
	'viatel_data', [
		'nonce' => wp_create_nonce( 'viatel_save_profile' ),
	]
);

?>

<div class="viatel p-3 mb-2 bg-white text-dark">

    <div class="row">
        <div class="col-12 col-md-8">
            <div class="row">
                <div class="col-12 col-md-9 d-flex justify-content-left align-items-center flex-wrap">
                    <h3>
						<?= esc_html( $this->viatel->config->get_plugin_title() ); ?>
                    </h3>
                </div>
                <div class="col-12 col-md-3 d-flex justify-content-center align-items-center flex-wrap">
                    <img src="<?= $this->viatel->asset->get_asset_path( 'img/viatel-logga.png' ) ?>" alt="logo"
                         class="img-fluid" style="width:10rem;"/>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-8">
            <div class="row">
                <div class="col-12">
                    <ul class="nav nav-tabs" role="tablist" id="profiles-tabs">
						<?php foreach ( $viatel_profiles as $env => $viatel_profile ) : ?>
                            <li class="nav-item">
                                <a class="nav-link"
                                   data-toggle="tab"
                                   href="#<?= $env ?>"
                                   role="tab">
									<?= _( $env ) ?>
                                </a>
                            </li>
						<?php endforeach; ?>
                    </ul>
                    <div class="tab-content">
						<?php foreach ( $viatel_profiles as $env => $viatel_profile ) : ?>
                            <div class="tab-pane fade"
                                 role="tabpanel"
                                 id="<?= $env ?>"
                                 aria-labelledby="<?= $env ?>-tab">
								<?php
								$this->viatel->view->render( 'profile', '', [
									'viatel_profile' => $viatel_profile,
								] );
								?>
                            </div>
						<?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
