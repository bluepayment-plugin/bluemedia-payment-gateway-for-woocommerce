<?php

use BlueMedia\OnlinePayments\Model\Gateway;

final class BlueMediaInstallmentPaymentMethod implements PaymentGatewayInterface
{
    public function canProcess($currentCurrency, array $bluemediaSettings)
    {
        return !empty($bluemediaSettings['backgorund_channels'][$currentCurrency][Gateway::GATEWAY_ID_IFRAME])
            && !empty($bluemediaSettings['installment_' . $currentCurrency])
            && $bluemediaSettings['installment_' . $currentCurrency] == 'yes';
    }

    public function process()
    {
        /** @see WC_Payment_Gateway_BlueMedia_Installment */
        return 'WC_Payment_Gateway_BlueMedia_Installment';
    }
}
