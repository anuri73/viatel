<?php
/** @var View $this */
/** @var array $errors */
/** @var Viatel_Widget $widget */

wp_enqueue_style(
	$this->viatel->asset->get_asset_name( 'widget' ),
	$this->viatel->asset->get_asset_path( 'widget.css' ),
	[],
	$this->viatel->config->get_plugin_version()
);
wp_enqueue_script(
	$this->viatel->asset->get_asset_name( 'widget' ),
	$this->viatel->asset->get_asset_path( 'widget.js' ),
	[],
	$this->viatel->config->get_plugin_version()
);
?>

<div id="viatel-container">
    <div class="p-3 mb-2 bg-white text-dark container">
        <form method="post"
              class="viatel-form"
        >
			<?php if ( count( $errors ) ): ?>
				<?php foreach ( $errors as $error ): ?>
                    <div class="alert alert-danger hidden" role="alert">
						<?= $error ?>
                    </div>
				<?php endforeach; ?>
			<?php endif ?>

			<?php if ( ! empty( $widget->name_label ) ) : ?>
                <div class="form-group">
                    <label for="viatel-customer_name"
                           class="col-form-label vt_text_label">
						<?= $widget->name_label ?>
                    </label>
                    <input type="text"
                           class="form-control vt_text_input"
                           id="viatel-customer_name"
                           name="viatel[customer_name]"
                    />
                </div>
			<?php endif; ?>

			<?php if ( ! empty( $widget->email_label ) ) : ?>
                <div class="form-group">
                    <label for="viatel-email"
                           class="col-form-label vt_text_label">
						<?= $widget->email_label ?>
                    </label>
                    <input type="text"
                           class="form-control vt_text_input"
                           id="viatel-email"
                           name="viatel[email]"
                    />
                </div>
			<?php endif; ?>

			<?php if ( ! empty( $widget->phone_label ) ) : ?>
                <div class="form-group">
                    <label for="viatel-phone"
                           class="col-form-label vt_text_label">
						<?= $widget->phone_label ?>
                    </label>
                    <input type="text"
                           class="form-control vt_text_input"
                           id="viatel-phone"
                           name="viatel[phone]"
                    />
                </div>
			<?php endif; ?>

			<?php if ( ! empty( $widget->account_number_label ) ) : ?>
                <div class="form-group">
                    <label for="viatel-account_number"
                           class="col-form-label vt_text_label">
						<?= $widget->account_number_label ?>
                    </label>
                    <input type="text"
                           class="form-control vt_text_input"
                           id="viatel-account_number"
                           name="viatel[account_number]"
                    />
                </div>
			<?php endif; ?>

			<?php if ( ! empty( $widget->approve_top_up_label ) ) : ?>
                <div class="form-group form-check">
                    <input class="form-check-input" type="checkbox" id="viatel-approve_top_up"
                           name="viatel[approve_top_up]">
                    <label class="form-check-label" for="viatel-approve_top_up">
						<?= $widget->approve_top_up_label ?>
                    </label>
                </div>
			<?php endif; ?>

			<?php if ( ! empty( $widget->approve_automatic_top_up_label ) ) : ?>
                <div class="form-group form-check">
                    <input class="form-check-input" type="checkbox" id="viatel-approve_automatic_top_up"
                           name="viatel[approve_automatic_top_up]">
                    <label class="form-check-label" for="viatel-approve_automatic_top_up">
						<?= $widget->approve_automatic_top_up_label ?>
                    </label>
                </div>
			<?php endif; ?>
            <label class="col-form-label vt_text">
				<?= $widget->packages_label ?>
            </label>
            <div class="btn-toolbar d-flex">
				<?php foreach ( $widget->packages as $package ) : ?>
                    <button type="submit"
                            class="btn btn-rhino w-100 mr-2 p-5 vt-button"
                            name="viatel[amount]"
                            value="<?= $package->amount ?>"
                    >
                        <span class="vt_button_title row justify-content-md-center"><?= $package->main_text ?></span>
                        <span class="vt_button_text row justify-content-md-center"><?= $package->sub_text ?></span>
                    </button>
				<?php endforeach; ?>
            </div>
            <input type="hidden" name="viatel[_wpnonce]" value="<?= wp_create_nonce( 'viatel_create_order' ) ?>">
        </form>
    </div>
</div>
