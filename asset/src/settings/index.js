import './index.scss';
import 'bootstrap';
import 'jquery-form';
import 'bootstrap-datepicker';

$(function () {

    function addValidationError($dom, $message) {
        $dom.addClass('is-invalid');
        let $id = $dom.attr('id') + '-feedback';
        if ($("#" + $id).length === 0) {
            $dom.after("<div class=\"invalid-feedback\" id='" + $id + "'>" + $message + "</div");
        }
    }

    function removeAllValidationErrors() {
        $(".invalid-feedback").remove();
        $(".is-invalid").removeClass('is-invalid');
    }

    function hideCommonErrors($environment) {
        let $dom = $("#common-" + $environment + "errors");
        $dom.hide();
    }

    function showCommonErrors($errors, $environment) {
        if (!$.isEmptyObject($errors)) {
            let $dom = $("#common-" + $environment + "errors");
            $dom.show().html(Object.keys($errors).join('<br>'));
        }
    }

    let $forms = document.getElementsByClassName('settings-form');

    for (let i = 0; i < $forms.length; i++) {
        let $form = $forms[i];

        $form.addEventListener('submit', function (event) {

            event.preventDefault();

            event.stopPropagation();

            let $environment = $($form).data('environment');

            hideCommonErrors($environment);

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                dataType: 'json',
                cache: false,
                data: {
                    action: 'viatel_save_profile',
                    nonce: viatel_data.nonce,
                    profile: $($form).serialize()
                },
                success: function ($response) {
                    if (!$response.success) {
                        removeAllValidationErrors();
                        showCommonErrors($response.errors.common, $environment);
                        $.each($response.errors, ($property, $errors) => {
                            $.each($errors, function ($message, $isInvalid) {
                                if ($isInvalid) {
                                    addValidationError($("#profile_" + $environment + "_" + $property), $message)
                                }
                            })
                        });
                    }
                },
                error: function ($response) {

                }
            });

        }, false);
    }
    $('#profiles-tabs a:first').tab('show');

    $('#log-datepicker').datepicker({
        format: "yyyy-mm-dd",
        weekStart: 1
    });
});
