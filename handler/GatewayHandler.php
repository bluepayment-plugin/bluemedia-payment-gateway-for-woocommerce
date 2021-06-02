<?php
use BlueMedia\OnlinePayments\Gateway;

abstract class GatewayHandler
{
    protected $logger;
    protected $gateway;

    public function __construct(Gateway $gateway)
    {
        $this->logger = new Logger(PaymentEnum::ID_PAYMENT_GATEWAY_BLUEMEDIA);
        $this->gateway = $gateway;
    }
}
