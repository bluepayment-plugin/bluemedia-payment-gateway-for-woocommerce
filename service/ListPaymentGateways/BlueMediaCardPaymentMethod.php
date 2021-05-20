<?php

use BlueMedia\OnlinePayments\Model\Gateway;

final class BlueMediaCardPaymentMethod implements PaymentGatewayInterface
{
    public function canProcess($currentCurrency, array $bluemediaSettings)
    {
        return !empty($bluemediaSettings['backgorund_channels'][$currentCurrency][Gateway::GATEWAY_ID_CARD]) &&
            !empty($bluemediaSettings['card_' . $currentCurrency]) &&
            $bluemediaSettings['card_' . $currentCurrency] == 'yes';
    }

    public function process()
    {
        /** @see WC_Payment_Gateway_BlueMedia_Card */
        return 'WC_Payment_Gateway_BlueMedia_Card';
    }
}
