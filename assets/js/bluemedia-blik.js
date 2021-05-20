jQuery(document).ready(function ($) {
    $(document).ready(function () {
        var order_pay_form = $('form#order_review'),
            order_pay_button = $('button#place_order'),
            checkout_form = $('form.checkout'),
            payment_method = '';

        $('input[name=payment_method]').on('click', function () {
            payment_method = $(this).val();
        })

        order_pay_button.on('click', function(e) {
            if (payment_method === 'bluemedia_payment_gateway_blik') {
                e.preventDefault();
                bluemedia_blik_verify_code(order_pay_form.find('#blik_code').val());
            }
        });

        checkout_form.on('checkout_place_order', function () {
            if ($('#blik_trigger_flag').length === 0) {
                checkout_form.append('<input type="hidden" id="blik_trigger_flag" name="blik_trigger_flag" value="1">');
            }
            return true;
        });

        $(document.body).on('checkout_error', function () {
            $('.woocommerce-error li').each(function() {
                var errorText = $(this).text();
                if (errorText.indexOf('blik_trigger_value') !== -1) {
                    $(this).remove();
                    $('.woocommerce-error').remove();
                    if ($('body').hasClass('woocommerce-checkout') || $('body').hasClass('woocommerce-cart')) {
                        $('html, body').stop();
                    }
                    bluemedia_blik_verify_code($('form[name=checkout]').find('#blik_code').val());
                }
            });
        });
    });

    function bluemedia_blik_put_content(content) {
        $('#blik_content_check_status').html(content);
    }

    function bluemedia_blik_verify_code(blik_code) {
        var form = $('form[name=checkout]'),
            order_email = form.find('#billing_email').val(),
            email_query = '';

        if (blik_code && blik_code !== '') {
            bluemedia_blik_content_form_entry_disable();
            bluemedia_payment_methods_toggle(true);

            if (order_email !== undefined) {
                email_query = '&order_email=' + order_email;
            }

            $.ajax({
                type: "POST",
                url: get_ajax_url('bluemedia_blik_validate_code'),
                data: "bluemedia_blik_code=" + blik_code + email_query,
                dataType: 'json',
                success: function (msg) {
                    if (msg) {
                        if (msg.type === 'error') {
                            bluemedia_blick_content_form_entry_enable();
                            bluemedia_payment_methods_toggle(false);
                            bluemedia_blik_put_content('');
                            alert(msg.msg);
                            return;
                        }
                        if (msg.type === 'pending') {
                            bluemedia_blik_put_content(msg.msg);
                            var blik_verify_check = setInterval(bluemedia_blik_verify_code(blik_code), 1000);
                            return;
                        }
                        if (msg.type === 'success') {
                            clearInterval(blik_verify_check);
                            window.location.href = msg.redirect;
                        }
                    }
                }
            });
        } else {
            alert('Proszę wprowadzić kod BLIK');
        }
    }

    function bluemedia_blik_content_form_entry_disable() {
        $('#bm-spinner').show();
        $('#blik_content_form_entry').hide();
    }

    function bluemedia_blick_content_form_entry_enable() {
        $('#bm-spinner').hide();
        $('#blik_content_form_entry').show();
    }

    function bluemedia_payment_methods_toggle(toggle) {
        var opacity = toggle ? 0.3 : 1;
        $('#payment_method_bluemedia_payment_gateway_blik_pbl').prop('disabled', toggle);
        $("label[for='payment_method_bluemedia_payment_gateway_blik_pbl']").css('opacity', opacity);
        $('#payment_method_bluemedia_payment_gateway_card').prop('disabled', toggle);
        $("label[for='payment_method_bluemedia_payment_gateway_card']").css('opacity', opacity);
        $('#payment_method_bluemedia_payment_gateway').prop('disabled', toggle);
        $("label[for='payment_method_bluemedia_payment_gateway']").css('opacity', opacity);
        $('#payment_method_bluemedia_payment_gateway_installment').prop('disabled', toggle);
        $("label[for='payment_method_bluemedia_payment_gateway_installment']").css('opacity', opacity);
        $('#payment_method_bluemedia_payment_gateway_gpay_popup').prop('disabled', toggle);
        $("label[for='payment_method_bluemedia_payment_gateway_gpay_popup']").css('opacity', opacity);
    }

    function get_ajax_url(action) {
        var url = woocommerce_params.wc_ajax_url;
        return url.replace('%%endpoint%%', action)
    }
});
