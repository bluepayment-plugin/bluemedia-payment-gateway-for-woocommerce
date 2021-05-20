<?php

use BlueMedia\OnlinePayments\Model\Gateway;

final class SmartneyPopupPaymentMethod implements PaymentGatewayInterface
{
    public function canProcess($currentCurrency, array $bluemediaSettings)
    {
       return !empty($bluemediaSettings['service_id_' . $currentCurrency]) &&
            !empty($bluemediaSettings['hash_key_' . $currentCurrency]) &&
            !empty($bluemediaSettings['backgorund_channels'][$currentCurrency][Gateway::GATEWAY_ID_SMARTNEY]) &&
            !empty($bluemediaSettings['smartney_' . $currentCurrency]) &&
            $currentCurrency == CurrencyEnum::PLN &&
            $bluemediaSettings['smartney_' . $currentCurrency] == 'yes';
    }

    public function process()
    {
        /** @see WC_Payment_Gateway_BlueMedia_Smartney_Popup */
        return 'WC_Payment_Gateway_BlueMedia_Smartney_Popup';
    }
}
