<?php

use BlueMedia\OnlinePayments\Model\TransactionInit;
use BlueMedia\OnlinePayments\Model\Gateway;

final class BlikTransactionBuilder
{
    const WINDOW_FULL_SIZE_MODE = 'FULL';

    public function build(array $blik_data, $service_id)
    {
        $transaction = new TransactionInit();
        $transaction->setServiceId($service_id)
            ->setGatewayId(Gateway::GATEWAY_ID_BLIK)
            ->setScreenType(self::WINDOW_FULL_SIZE_MODE)
            ->setCustomerIp($_SERVER['REMOTE_ADDR'])
            ->setOrderId($blik_data['bluemedia_blik_order'])
            ->setTitle(__("BLIK payment", 'bluepayment-gateway-for-woocommerce'))
            ->setDescription($blik_data['bluemedia_blik_order'])
            ->setAmount(!empty($blik_data['total']) ? $blik_data['total'] : 0)
            ->setCurrency(!empty($blik_data['currency']) ? $blik_data['currency'] : '')
            ->setAuthorizationCode($blik_data['bluemedia_blik_code']);

            if (!empty($_POST['order_email'])) {
                $transaction->setCustomerEmail($_POST['order_email']);
            }

        return $transaction;
    }
}
