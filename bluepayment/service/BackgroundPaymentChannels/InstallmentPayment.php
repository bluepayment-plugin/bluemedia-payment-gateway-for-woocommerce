<?php

use BlueMedia\OnlinePayments\Model\Gateway;

final class InstallmentPayment implements PaymentChannelInterface
{
    public function canProcess()
    {
        return !empty($_POST) && !empty($_POST['payment_method']) && $_POST['payment_method'] == 'bluemedia_payment_gateway_installment';
    }

    // Płatność ratalna
    public function process()
    {
        $bm_channel_id = Gateway::GATEWAY_ID_IFRAME;
        WC()->session->set('bm_background_payment', $bm_channel_id);

        return $bm_channel_id;
    }
}
