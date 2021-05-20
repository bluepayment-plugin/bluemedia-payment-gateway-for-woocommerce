<?php

use BlueMedia\OnlinePayments\Gateway;

require_once 'HandlerInterface.php';
require_once 'GatewayHandler.php';
require_once 'PaywayListHandler.php';
require_once 'RegulationsGetHandler.php';
require_once 'InitTransactionHandler.php';

final class BlueMediaSdkHandler
{
    private $service_id;
    private $payment_domain;
    private $hash_key;

    public function __construct($payment_domain, $service_id, $hash_key)
    {
        $this->payment_domain = $payment_domain;
        $this->service_id = $service_id;
        $this->hash_key = $hash_key;
    }

    public function call($className, $data = null)
    {
        if (class_exists($className) === false) {
            throw new InvalidArgumentException(sprintf('Handler class "%s" is not found: ', $className));
        }

        /** @var $client HandlerInterface */
        return (new $className($this->getGateway()))->handle($data);
    }

    private function getGateway()
    {
        return new Gateway(
            $this->service_id,
            $this->hash_key,
            $this->getGatewayMode(),
            Gateway::HASH_SHA256
        );
    }

    private function getGatewayMode()
    {
        return $this->payment_domain === Gateway::PAYMENT_DOMAIN_LIVE
            ? Gateway::MODE_LIVE
            : Gateway::MODE_SANDBOX;
    }
}
