<?php

use BlueMedia\OnlinePayments\Model\Gateway;

final class BlikPblPaymentMethod implements PaymentGatewayInterface
{
    public function canProcess($currentCurrency, array $bluemediaSettings)
    {
        return !empty($bluemediaSettings['backgorund_channels'][$currentCurrency][Gateway::GATEWAY_ID_BLIK])
            && !empty($bluemediaSettings['blik_pbl_' . $currentCurrency])
            && $bluemediaSettings['blik_pbl_' . $currentCurrency] == 'yes';
    }

    public function process()
    {
        /** @see WC_Payment_Gateway_BlueMedia_Blik_Pbl */
        return 'WC_Payment_Gateway_BlueMedia_Blik_Pbl';
    }
}
