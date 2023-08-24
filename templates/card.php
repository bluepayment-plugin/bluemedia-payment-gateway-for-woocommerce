<?php

use Ilabs\BM_Woocommerce\Controller\Model\Payment_Status_Response_Value_Object;
use Ilabs\BM_Woocommerce\Controller\Payment_Status_Controller;

?>
<div class="bm-blik-overlay">
	<p><span class="bm-blik-overlay-status" id="bm-blik-overlay-status"></span>
	</p>
</div>


<script>
    var bm_blik0_payment_in_progress = false;
    var placeOrderCardStarted = false;


    function bm_sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    jQuery(document).ready(function ($) {
        const originalTriggerHandler = $.fn.triggerHandler;


        $.fn.triggerHandler = function (event, data) {
            if (event === 'checkout_place_order_success') {
                if ($('#bm-gateway-id-1500').is(':checked')) {
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


        function bmInitCardIframe(continueTransactionRedirectUrl) {
            if (placeOrderCardStarted === true){
                return
            }

            placeOrderCardStarted = true;

            ////console.log('continue URL: ' + continueTransactionRedirectUrl);

            PayBmCheckout.transactionStartByUrl(continueTransactionRedirectUrl);
        }

        function bmCheckBlik0Status() {
            jQuery('.bluemedia-loader').show()
            jQuery('.bluemedia-status-box').show()

            var data = {
                action: "bm_payment_get_status_action",
                nonce: "<?php echo wp_create_nonce( Payment_Status_Controller::NONCE_ACTION ) ?>"
            };


            //console.log('ajax start');

            jQuery.post('<?php echo esc_url( admin_url( 'admin-ajax.php' ) )?>', data, function (response) {

                if (response !== 0) {
                    response = JSON.parse(response);
                    //console.log(response.status);

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
                                    bmCheckBlik0Status()
                                }, 3000)

                                return false
                            }
                            blueMediaUpdateStatus('<?php _e( 'The response format is invalid. Please copy this message and send it to our technical support: ',
								'bm-woocommerce' ) ?>' + JSON.stringify(response), 'error')
                            return false
                        }

                        if (response.status === '<?php echo Payment_Status_Response_Value_Object::STATUS_WAIT ?>'
                            || response.status === '<?php echo Payment_Status_Response_Value_Object::STATUS_CHECK_DEVICE ?>') {

                            if (response.hasOwnProperty('message')

                            ) {
                                blueMediaUpdateStatus(response.message, response.status)

                                if (response.continue_transaction_redirect_url !== '') {
                                    bmInitCardIframe(response.continue_transaction_redirect_url)
                                } else {
                                    setTimeout(function () {
                                        bmCheckBlik0Status()
                                    }, 3000)
                                }

                                return false
                            }
                            blueMediaUpdateStatus('<?php _e( 'The response format is invalid. Please copy this message and send it to our technical support: ',
								'bm-woocommerce' ) ?>' + JSON.stringify(response), 'error')
                            return false
                        }


                        if (response.status === '<?php echo Payment_Status_Response_Value_Object::STATUS_ERROR ?>') {
                            if (response.hasOwnProperty('message')) {
                                blueMediaUpdateStatus(response.message, response.status)
                                setTimeout(function () {
                                    bmCheckBlik0Status()
                                }, 3000)
                                return false
                            }

                            blueMediaUpdateStatus('<?php _e( 'The response format is invalid. Please copy this message and send it to our technical support: ',
								'bm-woocommerce' ) ?>' + JSON.stringify(response), 'error')

                            return false

                        }
                    }
                    blueMediaUpdateStatus('<?php _e( 'The response format is invalid. Please copy this message and send it to our technical support: ',
						'bm-woocommerce' ) ?>' + JSON.stringify(response), 'error')

                    return false
                } else {
                    blueMediaUpdateStatus('<?php _e( 'The response format is invalid. Please copy this message and send it to our technical support: ',
						'bm-woocommerce' ) ?>' + JSON.stringify(response), 'error')
                }


            }).fail(function (jqXHR, textStatus, errorThrown) {
                jQuery('.bluemedia-loader').hide()
                blueMediaUpdateStatus('<?php _e( 'Invalid response. Code: ',
					'bm-woocommerce' ) ?>' + jqXHR.status, 'error');

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