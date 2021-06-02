<?php

final class BlueMediaPaywallPaymentMethod implements PaymentGatewayInterface
{
    public function canProcess($currentCurrency, array $bluemediaSettings)
    {
        return true;
    }

    public function process()
    {
        /** @see WC_Payment_Gateway_BlueMedia */
        return 'WC_Payment_Gateway_BlueMedia';
    }
}
