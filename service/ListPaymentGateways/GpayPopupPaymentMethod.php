<?php

use BlueMedia\OnlinePayments\Model\Gateway;

final class GpayPopupPaymentMethod implements PaymentGatewayInterface
{
    public function canProcess($currentCurrency, array $bluemediaSettings)
    {
        return $this->isConfigurationSet($currentCurrency, $bluemediaSettings) &&
            $this->isGpayChannelEnabled($currentCurrency, $bluemediaSettings) &&
            $this->isCardChannelEnabled($currentCurrency, $bluemediaSettings);
    }

    public function process()
    {
        /** @see WC_Payment_Gateway_BlueMedia_GPay_Popup */
        return 'WC_Payment_Gateway_BlueMedia_GPay_Popup';
    }

    private function isConfigurationSet($currentCurrency, array $bluemediaSettings) {
        return !empty($bluemediaSettings['service_id_' . $currentCurrency]) &&
            !empty($bluemediaSettings['hash_key_' . $currentCurrency]);
    }

    private function isGpayChannelEnabled($currentCurrency, $bluemediaSettings)
    {
         return !empty($bluemediaSettings['backgorund_channels'][$currentCurrency][Gateway::GATEWAY_ID_GOOGLE_PAY]);
    }

    private function isCardChannelEnabled($currentCurrency, $bluemediaSettings)
    {
        return !empty($bluemediaSettings['backgorund_channels'][$currentCurrency][Gateway::GATEWAY_ID_CARD]) &&
            !empty($bluemediaSettings['card_' . $currentCurrency]) &&
            $bluemediaSettings['card_' . $currentCurrency] === 'yes';
    }
}
