<?php

use BlueMedia\OnlinePayments\Model\Gateway;

final class SmartneyPayment implements PaymentChannelInterface
{
    public function canProcess()
    {
        return !empty($_POST) && !empty($_POST['payment_method']) && $_POST['payment_method'] == 'bluemedia_payment_gateway_smartney_popup';
    }

    public function process()
    {
        $bm_channel_id =  Gateway::GATEWAY_ID_SMARTNEY;
        WC()->session->set('bm_background_payment', $bm_channel_id);

        return $bm_channel_id;
    }
}
