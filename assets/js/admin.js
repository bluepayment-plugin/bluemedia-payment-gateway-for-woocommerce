jQuery(document).ready(function () {
    //var radio = jQuery('#woocommerce_bluemedia_testmode');

    function api_fields_logic(radio_val) {
        switch (radio_val) {
            case 'yes':
                setupView(true);
                break;
            default:
                setupView(false);
                break;
        }
    }

    api_fields_logic(jQuery('input[type=radio][name=woocommerce_bluemedia_testmode]:checked').val());

    jQuery('input[type=radio][name=woocommerce_bluemedia_testmode]').on('change', function () {
        api_fields_logic(jQuery(this).val());
    });


    function setupView(isChecked) {
        if (true === isChecked) {
            jQuery('#woocommerce_bluemedia_service_id').addClass('bm-disabled').prop("disabled", true);
            jQuery('#woocommerce_bluemedia_private_key').addClass('bm-disabled').prop("disabled", true);
            jQuery('#woocommerce_bluemedia_test_service_id').addClass('bm-disabled').prop("disabled", false);
            jQuery('#woocommerce_bluemedia_test_private_key').addClass('bm-disabled').prop("disabled", false);
        } else {
            jQuery('#woocommerce_bluemedia_service_id').removeClass('bm-disabled').prop("disabled", false);
            jQuery('#woocommerce_bluemedia_private_key').removeClass('bm-disabled').prop("disabled", false);
            jQuery('#woocommerce_bluemedia_test_service_id').removeClass('bm-disabled').prop("disabled", true);
            jQuery('#woocommerce_bluemedia_test_private_key').removeClass('bm-disabled').prop("disabled", true);
        }
    }


    jQuery('.bm_ga_help_modal').click(function (e) {
        e.preventDefault()
        jQuery('.bm-modal-content').show();
        jQuery('body').toggleClass('bm-modalbackground');
        jQuery('.bm-modal-overlay').toggleClass('active');
    });

    jQuery('.bm-modal-content .bm-close').click(function () {
        jQuery('.bm-modal-content').hide();
        jQuery('body').toggleClass('bm-modalbackground');
        jQuery('.bm-modal-overlay').toggleClass('active');
    });

});
