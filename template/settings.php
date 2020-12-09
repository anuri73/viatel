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
                    <h3 class="font-weight-normal">
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
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="card">
                                    <div class="card-body">
										<?php
										$this->viatel->view->render( 'profile', '', [
											'viatel_profile' => $viatel_profile,
										] );
										?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3><?= _( 'Send logs' ) ?></h3>
                                    </div>
                                    <div class="card-body">
                                        <form method="post" name="send-log">
                                            <div class="form-inline form-group">
                                                <div class="input-daterange input-group" id="log-datepicker">
                                                    <input type="text" class="input-sm form-control"
                                                           name="send-log[start]"/>
                                                    <span class="input-group-addon">
                                                        to
                                                    </span>
                                                    <input type="text" class="input-sm form-control"
                                                           name="send-log[end]"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <input type="hidden" name="send-log[env]" value="<?= $env ?>"/>
                                                <button type="submit" class="btn btn-primary">
							                        <?= _( 'Send logs' ) ?>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3><?= _( 'Last transactions' ) ?></h3>
                                    </div>

                                    <div class="card-body">
                                        <table class="table table-responsive">
                                            <thead>
                                            <tr>
                                                <td><?= _( 'Transaction id' ) ?></td>
                                                <td><?= _( 'Customer' ) ?></td>
                                                <td><?= _( 'Account number' ) ?></td>
                                                <td><?= _( 'Order' ) ?></td>
                                                <td><?= _( 'Amount' ) ?></td>
                                                <td><?= _( 'Result' ) ?></td>
                                                <td><?= _( 'SessionId' ) ?></td>
                                            </tr>
                                            </thead>
                                            <tbody>
				                            <?php foreach ( $this->viatel->log->get_transactions( $env ) as $transaction ): ?>
                                                <tr>
                                                    <td><?= $transaction['id'] ?></td>
                                                    <td><?= $transaction['customer'] ?></td>
                                                    <td><?= $transaction['account'] ?></td>
                                                    <td><?= $transaction['order'] ?></td>
                                                    <td><?= $transaction['amount'] ?></td>
                                                    <td><?= $transaction['result'] ?></td>
                                                    <td><?= $transaction['sessionId'] ?></td>
                                                </tr>
				                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
				<?php endforeach; ?>
            </div>
        </div>
    </div>

</div>
