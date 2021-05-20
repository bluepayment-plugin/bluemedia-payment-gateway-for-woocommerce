<?php

use BlueMedia\OnlinePayments\Model\Gateway;

final class BlikZeroPaymentMethod implements PaymentGatewayInterface
{
    public function canProcess($currentCurrency, array $bluemediaSettings)
    {
        return !empty($bluemediaSettings['backgorund_channels'][$currentCurrency][Gateway::GATEWAY_ID_BLIK])
            && !empty($bluemediaSettings['blik_zero_' . $currentCurrency])
            && $bluemediaSettings['blik_zero_' . $currentCurrency] == 'yes';
    }

    public function process()
    {
        /** @see WC_Payment_Gateway_BlueMedia_Blik_Zero */
        return 'WC_Payment_Gateway_BlueMedia_Blik_Zero';
    }
}
