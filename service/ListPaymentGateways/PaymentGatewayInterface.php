<?php

interface PaymentGatewayInterface
{
    public function canProcess($currentCurrency, array $bluemediaSettings);
    public function process();
}
