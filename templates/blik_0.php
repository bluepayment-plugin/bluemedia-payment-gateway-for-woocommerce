<?php

use Ilabs\BM_Woocommerce\Controller\Model\Payment_Status_Response_Value_Object;
use Ilabs\BM_Woocommerce\Controller\Payment_Status_Controller;

$generic_error_message = __( 'Payment failed.',
	'bm-woocommerce' );

?>
<div class="bm-blik-overlay">
	<p><span class="bm-blik-overlay-status" id="bm-blik-overlay-status"></span>
	</p>
</div>
<div class="bm-blik-code-wrapper">
	<label
		for="bm-blik-code"><?php
		_e( 'Podaj kod BLIK',
			'bm-woocommerce' ); ?></label>
	<input id="bluemedia_blik_code" type="text" name="bluemedia_blik_code"
	       autocomplete="off">


	<span><?php _e( 'The code has 6 digits. You\'ll find it in your banking app.',
			'bm-woocommerce' ); ?></span>


	<div class="bluemedia-simple-status-box">
		<div class="bluemedia-success-wrapper">
			<span id="bluemedia-success-msg"></span>
		</div>
		<div class="bluemedia-info-wrapper">
			<span id="bluemedia-info-msg"></span>
		</div>
		<div class="bluemedia-error-wrapper">
			<span id="bluemedia-error-msg"></span>
		</div>
	</div>


</div>


<script>
    var bm_blik0_payment_in_progress = false;
    var placeOrderBlikStarted = false;


    function bm_sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    jQuery(document).ready(function ($) {
        const originalTriggerHandler = $.fn.triggerHandler;


        $.fn.triggerHandler = function (event, data) {
            if (event === 'checkout_place_order_success') {
                if ($('#bm-gateway-id-509').is(':checked')) {
                    if (false === bm_blik0_payment_in_progress) {
                        bm_blik0_payment_in_progress = true
                        return originalTriggerHandler.apply(this, arguments);
                    }
                }

                bmCheckBlik0Status()

                return originalTriggerHandler.apply(this, arguments);
            }

            return originalTriggerHandler.apply(this, arguments);
        };


        function bmCheckBlik0Status() {
            jQuery('.bluemedia-loader').show()
            jQuery('.bluemedia-status-box').show()

            var data = {
                action: "bm_payment_get_status_action",
                nonce: "<?php echo wp_create_nonce( Payment_Status_Controller::NONCE_ACTION ) ?>"
            };


            console.log('ajax start');

            jQuery.post('<?php echo esc_url( admin_url( 'admin-ajax.php' ) )?>', data, function (response) {

                if (response !== 0) {
                    response = JSON.parse(response);
                    console.log(response.status);

                    if (response.hasOwnProperty('status')
                        && (response.status === '<?php echo Payment_Status_Response_Value_Object::STATUS_SUCCESS ?>'
                            || response.status === '<?php echo Payment_Status_Response_Value_Object::STATUS_ERROR ?>'
                            || response.status === '<?php echo Payment_Status_Response_Value_Object::STATUS_WAIT ?>'
                            ||
                            response.status === '<?php echo Payment_Status_Response_Value_Object::STATUS_CHECK_DEVICE ?>'
                        )
                    ) {
                        if (response.status === '<?php echo Payment_Status_Response_Value_Object::STATUS_SUCCESS ?>') {

                            if (response.hasOwnProperty('message')

                            ) {
                                blueMediaUpdateStatus(response.message, response.status)

                                setTimeout(function () {
                                    //bmCheckBlik0Status()
                                    window.location.href = response.order_received_url;

                                }, 3000)

                                return false
                            }
                            blueMediaUpdateStatus('<?php esc_html_e( $generic_error_message ); ?>', 'error')
                            return false
                        }

                        if (response.status === '<?php echo Payment_Status_Response_Value_Object::STATUS_WAIT ?>'
                            || response.status === '<?php echo Payment_Status_Response_Value_Object::STATUS_CHECK_DEVICE ?>') {

                            if (response.hasOwnProperty('message')

                            ) {
                                blueMediaUpdateStatus(response.message, response.status)

                                setTimeout(function () {
                                    bmCheckBlik0Status()
                                }, 3000)
                                return false
                            }
                            blueMediaUpdateStatus('<?php esc_html_e( $generic_error_message ); ?>' + JSON.stringify(response), 'error')
                            return false
                        }


                        if (response.status === '<?php echo Payment_Status_Response_Value_Object::STATUS_ERROR ?>') {
                            if (response.hasOwnProperty('message')) {
                                blueMediaUpdateStatus(response.message, response.status)
                                setTimeout(function () {
                                    //bmCheckBlik0Status()
                                    window.location.href = response.order_received_url;
                                }, 3000)
                                return false
                            }

                            blueMediaUpdateStatus('<?php esc_html_e( $generic_error_message ); ?>' + JSON.stringify(response), 'error')

                            return false

                        }
                    }
                    blueMediaUpdateStatus('<?php esc_html_e( $generic_error_message ); ?>' + JSON.stringify(response), 'error')

                    return false
                } else {
                    blueMediaUpdateStatus('<?php esc_html_e( $generic_error_message ); ?>' + JSON.stringify(response), 'error')
                }


            }).fail(function (jqXHR, textStatus, errorThrown) {
                jQuery('.bluemedia-loader').hide()
                blueMediaUpdateStatus('<?php esc_html_e( $generic_error_message ); ?>' + jqXHR.status, 'error');

                return false
            });


        }


        function blueMediaUpdateStatus(message, status) {
            $('.bm-blik-overlay').show();

            //$targetWrapper = $('.bluemedia-success-wrapper');
            $targetSpan = $('#bm-blik-overlay-status');

            if (status === '<?php echo Payment_Status_Response_Value_Object::STATUS_SUCCESS ?>') {
                $targetSpan.addClass('bm-blik-overlay-status--success').removeClass('bm-blik-overlay-status--process bm-blik-overlay-status--error');
            } else if (status === '<?php echo Payment_Status_Response_Value_Object::STATUS_CHECK_DEVICE ?>') {
                $targetSpan.addClass('bm-blik-overlay-status--process').removeClass('bm-blik-overlay-status--success bm-blik-overlay-status--error');
            } else if (status === '<?php echo Payment_Status_Response_Value_Object::STATUS_WAIT ?>') {
                $targetSpan.addClass('bm-blik-overlay-status--process').removeClass('bm-blik-overlay-status--success bm-blik-overlay-status--error');
            } else if (status === '<?php echo Payment_Status_Response_Value_Object::STATUS_ERROR ?>') {
                $targetSpan.addClass('bm-blik-overlay-status--error').removeClass('bm-blik-overlay-status--success bm-blik-overlay-status--process');
            }

            $targetSpan.text(message);
        }

    })
    ;

</script>