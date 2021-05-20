<?php

final class BackgroundPayment implements PaymentChannelInterface
{
    public function canProcess()
    {
        return !empty($_POST) && !empty($_POST['bm_background_payment']);
    }

    // Płatności w tle
    public function process()
    {
        $bm_channel_id = (int) $_POST['bm_background_payment'];
        WC()->session->set('bm_background_payment', $bm_channel_id);

        return $bm_channel_id;
    }
}
