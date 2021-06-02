<?php

final class GpayPopupValidator implements ValidatorInterface
{
    public function validate()
    {
        if ($_POST['gpay_popup_trigger_flag'] == '1' && empty($_POST['bluemediaPaymentToken'])) {
            wc_add_notice('gpay_popup_trigger_value', 'error');
            $response = [
                'orderId' => 0,
                'result' => 'failure',
                'messages' => wc_print_notices(true),
                'refresh' => false,
                'reload' => false,
            ];
            wp_send_json($response);
        }

        if (empty($_POST['bluemediaPaymentToken'])) {
            wc_add_notice(__("Please enter your Google Pay details", 'bluepayment-gateway-for-woocommerce'), 'error');
        }
    }
}
