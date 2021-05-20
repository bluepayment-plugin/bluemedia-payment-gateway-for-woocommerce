<?php

final class BackgroundSessionPayment implements PaymentChannelInterface
{
    public function canProcess()
    {
        return !empty(WC()->session) && !empty(WC()->session->get('bm_background_payment'));
    }

    // Płatności w tle
    public function process()
    {
        return WC()->session->get('bm_background_payment');
    }
}
