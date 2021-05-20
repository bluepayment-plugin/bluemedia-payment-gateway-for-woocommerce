<?php

require_once 'PaymentGatewayInterface.php';
require_once 'BlikPblPaymentMethod.php';
require_once 'BlikZeroPaymentMethod.php';
require_once 'BlueMediaCardPaymentMethod.php';
require_once 'BlueMediaInstallmentPaymentMethod.php';
require_once 'BlueMediaPaywallPaymentMethod.php';
require_once 'GpayPopupPaymentMethod.php';
require_once 'SmartneyPopupPaymentMethod.php';

final class PaymentGateways
{
    private $paymentMethods = [];

    public function addPaymentMethod(PaymentGatewayInterface $paymentMethod)
    {
        $this->paymentMethods[] = $paymentMethod;
    }

    public function handle(array $bluemediaSettings)
    {
        $methods = [];

        $currentCurrency = Utils::get_current_currency();

        foreach ($this->paymentMethods as $paymentMethod) {
            /** @var $paymentMethod PaymentGatewayInterface */
            if ($paymentMethod->canProcess($currentCurrency, $bluemediaSettings)) {
                 $methods[] = $paymentMethod->process();
            }
        }

        return $methods;
    }
}
