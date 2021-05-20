<?php

use BlueMedia\OnlinePayments\Model\TransactionStandard;

final class BMTransactionBuilder
{
    /**
     * Since version 2.7.0, there is different way of getting properties from object
    */
    const WC_BREAKING_VERSION = '2.7.0';

    public function build(WC_Order $orderInfo, $service_id, $gatewayId = 0)
    {
        $order = $this->getOrderData($orderInfo);

        $transactionData = new TransactionStandard();
        $transactionData->setServiceId($service_id)
            ->setOrderId((string)(empty($order->id) ? 0 : $order->id))
            ->setAmount(empty($order->total) ? 0 : number_format($order->total, 2, '.', ''))
            ->setDescription(empty($order->id) ? 0 : $order->id)
            ->setCurrency(empty($order->currency) ? '' : $order->currency)
            ->setCustomerEmail(empty($order->billingEmail) ? '' : $order->billingEmail);

        if (!empty($gatewayId)) {
            $transactionData->setGatewayId($gatewayId);
        }

        if (empty($_POST['bluemedia_channel_regulation_id']) === false) {
            $transactionData->setDefaultRegulationAcceptanceID($_POST['bluemedia_channel_regulation_id'])
                ->setDefaultRegulationAcceptanceState('ACCEPTED')
                ->setDefaultRegulationAcceptanceTime(date('Y-m-d H:i:s'));
        }

        return $transactionData;
    }

    private function getOrderData(WC_Order $orderInfo)
    {
        $order = new stdClass();

        if (WC()->version < self::WC_BREAKING_VERSION) {
            $order->id = isset($orderInfo->id) ? $orderInfo->id : 0;
            $order->total = $orderInfo->order_total;
            $order->currency = $orderInfo->order_currency;
            $order->billingEmail = $orderInfo->billing_email;
        } else {
            $order->id = $orderInfo->get_id();
            $order->total = $orderInfo->get_total();
            $order->currency = $orderInfo->get_currency();
            $order->billingEmail = $orderInfo->get_billing_email();
        }

        return $order;
    }
}
