<?php

final class BlikTransactionValidator implements ValidatorInterface
{
    public function validate()
    {
        if (empty($_POST) || empty($_POST['bluemedia_blik_code'])) {
            echo __("Please insert BLIK code.", 'bluepayment-gateway-for-woocommerce');
        }

        if (empty($_POST['order_email'])) {
            $order_id =  WC()->session->get('next_bm_blik_order');
            $order_email = WC()->cart->get_customer()->get_billing_email();
            $total = wc_get_order($order_id)->get_total();
        } else {
            $order_email = $_POST['order_email'];
            $total = WC()->cart->total;
        }

        $paymentGatewayBlueMediaBlikZero = new WC_Payment_Gateway_BlueMedia_Blik_Zero();
        $paymentGatewayBlueMediaBlikZero->validate_blik_transaction([
            'bluemedia_blik_code' => $_POST['bluemedia_blik_code'],
            'order_email' => $order_email,
            'total' => $total,
            'currency' => get_woocommerce_currency(),
        ]);
    }
}
