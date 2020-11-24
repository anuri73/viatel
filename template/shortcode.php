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

            <div class="form-group">
                <label for="viatel-email"
                       class="col-form-label vt_text_label">
					<?= _( 'E-mail Address (optional, can be used by customer service)' ) ?>
                </label>
                <input type="email"
                       class="form-control vt_text_input"
                       id="viatel-email"
                       placeholder="roberth.nilsson@viatel.se"
                       name="viatel[email]"
                />
            </div>
            <div class="form-group">
                <label for="viatel-account_number"
                       class="col-form-label vt_text_label">
					<?= _( 'Enter your account number or leave blank' ) ?>
                </label>
                <input type="number"
                       class="form-control vt_text_input"
                       id="viatel-account_number"
                       placeholder="9 8 7 6 5 4 3 2 1"
                       name="viatel[account_number]"
                />
            </div>
            <div class="form-group form-check">
                <input class="form-check-input" type="checkbox" id="viatel-approve_top_up"
                       name="viatel[approve_top_up]">
                <label class="form-check-label" for="viatel-approve_top_up">
					<?= _( 'I approve top-up from the phone' ) ?>
                </label>
            </div>
            <div class="form-group form-check">
                <input class="form-check-input" type="checkbox" id="viatel-approve_automatic_top_up"
                       name="viatel[approve_automatic_top_up]">
                <label class="form-check-label" for="viatel-approve_automatic_top_up">
					<?= _( 'I approve automatic top-up when using the phone service' ) ?>
                </label>
            </div>
            <label class="col-form-label vt_text">
				<?= _( 'Click your preferred package to purchase:' ) ?>
            </label>
            <input type="hidden" name="viatel[_wpnonce]" value="<?= wp_create_nonce( 'viatel_create_order' ) ?>">
            <div class="btn-toolbar d-flex">
                <button type="submit" class="btn btn-rhino w-100 mr-2 p-5 vt-button" name="viatel[amount]" value="40">
                    <span class="vt_button_title row justify-content-md-center">40 minutes</span>
                    <span class="vt_button_text row justify-content-md-center">99 kr (2,48 kr/min)</span>
                </button>
                <button type="submit" class="btn btn-rhino w-100 p-5 vt-button" name="viatel[amount]" value="100">
                    <span class="vt_button_title row justify-content-md-center">100 minutes</span>
                    <span class="vt_button_text row justify-content-md-center">199 kr (1,99 kr/min)</span>
                </button>
                <button type="submit" class="btn btn-rhino w-100 ml-2 p-5 vt-button" name="viatel[amount]" value="250">
                    <span class="vt_button_title row justify-content-md-center">250 minutes</span>
                    <span class="vt_button_text row justify-content-md-center">449 kr (1,79 kr/min)</span>
                </button>
            </div>
        </form>
    </div>
</div>
