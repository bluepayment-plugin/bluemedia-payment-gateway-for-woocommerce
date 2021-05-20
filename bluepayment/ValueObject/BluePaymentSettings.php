<?php

use BlueMedia\OnlinePayments\Gateway;

final class BluePaymentSettings
{
    public function __construct($fields)
    {
        foreach ($fields as $key => $value) {
            $this->$key = $value;
        }
    }

    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }

        throw new Exception("$name property does not exists");
    }

    public function isTestDomain()
    {
        return $this->payment_domain === Gateway::PAYMENT_DOMAIN_SANDBOX;
    }

    public function getGatewayMode()
    {
        return $this->isTestDomain() ? Gateway::MODE_SANDBOX : Gateway::MODE_LIVE;
    }
}

