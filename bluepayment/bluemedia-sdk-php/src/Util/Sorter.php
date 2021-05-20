<?php

namespace BlueMedia\OnlinePayments\Util;

use function array_key_exists;
use function strtolower;

class Sorter
{
    /**
     * @param array $params
     *
     * @return array
     */
    public static function sortTransactionParams(array $params)
    {
        $transactionParamsInOrder = [
            'ServiceID',
            'OrderID',
            'Amount',
            'Description',
            'GatewayID',
            'Currency',
            'CustomerEmail',
            'CustomerNRB',
            'TaxCountry',
            'CustomerIP',
            'Title',
            'ReceiverName',
            'BlikUIDKey',
            'BlikUIDLabel',
            'BlikAMKey',
            'ValidityTime',
            'LinkValidityTime',
            'receiverNRB',
            'receiverName',
            'receiverAddress',
            'remoteID',
            'bankHref',
            'AuthorizationCode',
            'ScreenType',
            'DefaultRegulationAcceptanceState',
            'DefaultRegulationAcceptanceID',
            'DefaultRegulationAcceptanceTime',
            'Hash',
        ];

        $result              = [];
        $lowercaseKeysParams = array_change_key_case($params, CASE_LOWER);

        foreach ($transactionParamsInOrder as $paramName) {
            $lowercaseParamName = strtolower($paramName);

            if (array_key_exists($lowercaseParamName, $lowercaseKeysParams)) {
                $result[$paramName] = $lowercaseKeysParams[$lowercaseParamName];
            }
        }

        return $result;
    }
}
