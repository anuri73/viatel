<?php
/** @var Profile $viatel_profile */
?>

<form method="post"
      class="settings-form"
      enctype="multipart/form-data"
      action="settings.php"
      data-environment="<?= $viatel_profile->get_environment() ?>">

    <div class="alert alert-danger hidden" role="alert" id="common-<?= $viatel_profile->get_environment() ?>errors">

    </div>

    <div class="form-group row">
        <label for="profile_<?= $viatel_profile->get_environment() ?>_merchant_id"
               class="col-sm-3 align-self-center col-form-label"><?= _( 'Merchant Id' ) ?></label>
        <div class="col-sm-8 offset-sm-1">
            <input type="number"
                   class="form-control"
                   id="profile_<?= $viatel_profile->get_environment() ?>_merchant_id"
                   placeholder="1 2 3 4 5" name="profile[merchant_id]"
                   value="<?= $viatel_profile->get_merchant_id() ?>"/>
        </div>
    </div>

    <div class="form-group row">
        <label for="profile_<?= $viatel_profile->get_environment() ?>_api_key"
               class="col-sm-3 align-self-center col-form-label"><?= _( 'API Key' ) ?></label>
        <div class="col-sm-8 offset-sm-1">
            <input type="password"
                   class="form-control"
                   id="profile_<?= $viatel_profile->get_environment() ?>_api_key"
                   placeholder="****************************************"
                   name="profile[api_key]"
                   value="<?= $viatel_profile->get_api_key() ?>"/>
        </div>
    </div>

    <div class="form-group">
        <div class=" form-check custom-control custom-switch">
            <input type="checkbox"
                   id="profile_<?= $viatel_profile->get_environment() ?>_show_confirmation_page"
                   name="profile[show_confirmation_page]"
                   value="1"
                   class="custom-control-input"
				<?= $viatel_profile->is_show_confirmation_page() ? 'checked' : '' ?>
            >
            <label class="custom-control-label"
                   for="profile_<?= $viatel_profile->get_environment() ?>_show_confirmation_page">
				<?= _( 'Show Confirmation Page' ) ?>
            </label>
        </div>

    </div>

    <div class="form-group">
        <label for="profile_<?= $viatel_profile->get_environment() ?>_url_payment_success"
               class="col-form-label"><?= _( 'After successful payment, redirect end-user to page' ) ?></label>
        <input type="url" class="form-control"
               id="profile_<?= $viatel_profile->get_environment() ?>_url_payment_success"
               placeholder="<?= _( 'URL when payment is successful' ) ?>"
               name="profile[url_payment_success]"
               value="<?= $viatel_profile->get_url_payment_success() ?>">
    </div>

    <div class="form-group">
        <label for="profile_<?= $viatel_profile->get_environment() ?>_url_payment_cancel"
               class="col-form-label"><?= _( 'When payment is cancelled, redirect end-user to page' ) ?></label>
        <input type="url" class="form-control"
               id="profile_<?= $viatel_profile->get_environment() ?>_url_payment_cancel"
               placeholder="<?= _( 'URL when payment is cancelled' ) ?>"
               name="profile[url_payment_cancel]"
               value="<?= $viatel_profile->get_url_payment_cancel() ?>">
    </div>

    <div class="form-group">
        <div class="form-check custom-control custom-switch">
            <input type="checkbox" id="profile_<?= $viatel_profile->get_environment() ?>_save_user_cookie"
                   name="profile[save_user_cookie]"
                   value="1"
                   class="custom-control-input"
				<?= $viatel_profile->is_save_user_cookie() ? 'checked' : '' ?>
            >
            <label class="custom-control-label"
                   for="profile_<?= $viatel_profile->get_environment() ?>_save_user_cookie">
				<?= _( 'Save end user’s information in a cookie' ) ?>
            </label>
        </div>
    </div>

    <input type="hidden" name="profile[environment]"
           value="<?= $viatel_profile->get_environment() ?>">

    <button type="submit" class="btn btn-primary">
		<?= _( 'Save' ) ?>
    </button>
</form>
