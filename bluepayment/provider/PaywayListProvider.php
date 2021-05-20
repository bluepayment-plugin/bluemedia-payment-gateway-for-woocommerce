<?php

use BlueMedia\OnlinePayments\Model\Gateway;

final class PaywayListProvider
{
    private $bluemedia_sdk_handler;

    public function __construct(BlueMediaSdkHandler $bluemedia_sdk_handler)
    {
        $this->bluemedia_sdk_handler = $bluemedia_sdk_handler;
    }

    public function getPaywayList()
    {
        $payment_gateways = $this->bluemedia_sdk_handler->call(PaywayListHandler::class);

        return $this->transformToArray($payment_gateways);
    }

    public function transformToArray($payment_gateways)
    {
        $channels_list = [];

        /** @var Gateway $paymentGateway */
        foreach ($payment_gateways as $payment_gateway) {
            $payment_gateway_id = $payment_gateway->getGatewayId();

            $channels_list[$payment_gateway_id]['gatewayID']   = $payment_gateway->getGatewayId();
            $channels_list[$payment_gateway_id]['gatewayName'] = $payment_gateway->getGatewayName();
            $channels_list[$payment_gateway_id]['gatewayType'] = $payment_gateway->getGatewayType();
            $channels_list[$payment_gateway_id]['bankName']    = $payment_gateway->getBankName();
            $channels_list[$payment_gateway_id]['iconURL']     = $payment_gateway->getIconUrl();
            $channels_list[$payment_gateway_id]['statusDate']  = $payment_gateway->getStatusDate()->format('Y-m-d H:i:s');
        }

        return $channels_list;
    }
}
