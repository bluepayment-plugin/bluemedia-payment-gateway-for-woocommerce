jQuery(document).ready(function ($) {
    var checkout_form = $('form.checkout');
    checkout_form.on('checkout_place_order', function () {
        if ($('#gpay_popup_trigger_flag').length === 0) {
            checkout_form.append('<input type="hidden" id="gpay_popup_trigger_flag" name="gpay_popup_trigger_flag" value="1">');
        }
        var $payment_method = $('form.checkout input[name="payment_method"]:checked').val();
        if ($payment_method === 'bluemedia_payment_gateway_gpay_popup') {
            if (!checkout_form.is('.processing') && bmOnceTimeClicker === false) {
                onGooglePaymentButtonClicked();
                checkout_form.addClass('processing');
                return false;
            }
        }
        return true;
    });
    $(document.body).on('checkout_error', function () {
        $('.woocommerce-error li').each(function() {
            var errorText = $(this).text();
            if (errorText.indexOf('gpay_popup_trigger_value') !== -1) {
                $(this).remove();
                $('.woocommerce-error').remove();
                if ($('body').hasClass('woocommerce-checkout') || $('body').hasClass('woocommerce-cart')) {
                    $('html, body').stop();
                }
            }
            bmOnceTimeClicker = false;
            if (document.getElementById('bluemediaPaymentToken')) {
                document.getElementById('bluemediaPaymentToken').value = '';
            }
        });
    });
});
