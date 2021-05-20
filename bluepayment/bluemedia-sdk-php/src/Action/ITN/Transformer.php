<?php

namespace BlueMedia\OnlinePayments\Action\ITN;

use BlueMedia\OnlinePayments\Gateway;
use BlueMedia\OnlinePayments\Model\ItnIn;
use BlueMedia\OnlinePayments\Util\Logger;
use DateTime;
use DateTimeZone;
use SimpleXMLElement;

class Transformer
{
    /**
     * Is it clearance transaction.
     *
     * @param SimpleXMLElement $transaction
     *
     * @return bool
     */
    private static function isArrayClearanceTransaction(SimpleXMLElement $transaction)
    {
        return (
            isset($transaction->transferDate)
            || isset($transaction->transferStatus)
            || isset($transaction->transferStatusDetails)
            || isset($transaction->receiverBank)
            || isset($transaction->receiverNRB)
            || isset($transaction->receiverName)
            || isset($transaction->receiverAddress)
            || isset($transaction->senderBank)
            || isset($transaction->senderNRB)
        );
    }

    /**
     * Is it clearance transaction.
     *
     * @param ItnIn $itnModel
     *
     * @return bool
     */
    private static function isObjectClearanceTransaction(ItnIn $itnModel)
    {
        return (
            $itnModel->getTransferDate() !== null
            || !empty($itnModel->getTransferStatus())
            || !empty($itnModel->getTransferStatusDetails())
            || !empty($itnModel->getReceiverBank())
            || !empty($itnModel->getReceiverNRB())
            || !empty($itnModel->getReceiverName())
            || !empty($itnModel->getReceiverAddress())
            || !empty($itnModel->getSenderBank())
            || !empty($itnModel->getSenderNRB())
        );
    }

    /**
     * Transforms model into an array.
     *
     * @param ItnIn $model
     *
     * @return array
     */
    public static function modelToArray(ItnIn $model)
    {
        $model->validate();
        $isClearanceTransaction = self::isObjectClearanceTransaction($model);

        $result = [];

        if ($model->getServiceId()) {
            $result['serviceID'] = $model->getServiceId();
        }
        if ($model->getOrderId()) {
            $result['orderID'] = $model->getOrderId();
        }
        if ($model->getRemoteId()) {
            $result['remoteID'] = $model->getRemoteId();
        }
        if ($model->getAmount()) {
            $result['amount'] = $model->getAmount();
        }
        if ($model->getCurrency()) {
            $result['currency'] = $model->getCurrency();
        }
        if ($model->getGatewayId()) {
            $result['gatewayID'] = $model->getGatewayId();
        }
        if ($model->getPaymentDate()) {
            $result['paymentDate'] = (($model->getPaymentDate() instanceof DateTime) ?
                $model->getPaymentDate()->format(Gateway::DATETIME_FORMAT) : ''
            );
        }
        if ($model->getPaymentStatus()) {
            $result['paymentStatus'] = $model->getPaymentStatus();
        }
        if ($model->getPaymentStatusDetails()) {
            $result['paymentStatusDetails'] = $model->getPaymentStatusDetails();
        }
        if ($model->getInvoiceNumber()) {
            $result['invoiceNumber'] = $model->getInvoiceNumber();
        }
        if ($model->getCustomerNumber()) {
            $result['customerNumber'] = $model->getCustomerNumber();
        }
        if ($model->getCustomerEmail()) {
            $result['customerEmail'] = $model->getCustomerEmail();
        }
        if ($model->getAddressIp()) {
            $result['addressIP'] = $model->getAddressIp();
        }

        if (!$isClearanceTransaction && !empty($model->getTitle())) {
            $result['title'] = $model->getTitle();
        }

        if ($model->getCustomerDatafName()) {
            $result['customerData']['fName'] = $model->getCustomerDatafName();
        }
        if ($model->getCustomerDatalName()) {
            $result['customerData']['lName'] = $model->getCustomerDatalName();
        }
        if ($model->getCustomerDataStreetName()) {
            $result['customerData']['streetName'] = $model->getCustomerDataStreetName();
        }
        if ($model->getCustomerDataStreetHouseNo()) {
            $result['customerData']['streetHouseNo'] = $model->getCustomerDataStreetHouseNo();
        }
        if ($model->getCustomerDataStreetStaircaseNo()) {
            $result['customerData']['streetStaircaseNo'] = $model->getCustomerDataStreetStaircaseNo();
        }
        if ($model->getCustomerDataStreetPremiseNo()) {
            $result['customerData']['streetPremiseNo'] = $model->getCustomerDataStreetPremiseNo();
        }

        if ($model->getCustomerDataPostalCode()) {
            $result['customerData']['postalCode'] = $model->getCustomerDataPostalCode();
        }
        if ($model->getCustomerDataCity()) {
            $result['customerData']['city'] = $model->getCustomerDataCity();
        }
        if ($model->getCustomerDataNrb()) {
            $result['customerData']['nrb'] = $model->getCustomerDataNrb();
        }
        if ($model->getCustomerDataSenderData()) {
            $result['customerData']['senderData'] = $model->getCustomerDataSenderData();
        }
        if ($model->getVerificationStatus()) {
            $result['verificationStatus'] = $model->getVerificationStatus();
        }
        if ($model->getStartAmount()) {
            $result['startAmount'] = $model->getStartAmount();
        }
        if ($model->getTransferDate()) {
            $result['transferDate'] = (($model->getTransferDate() instanceof DateTime) ?
                $model->getTransferDate()->format(Gateway::DATETIME_FORMAT) : ''
            );
        }
        if ($model->getTransferStatus()) {
            $result['transferStatus'] = $model->getTransferStatus();
        }
        if ($model->getTransferStatusDetails()) {
            $result['transferStatusDetails'] = $model->getTransferStatusDetails();
        }
        if ($model->getReceiverBank()) {
            $result['receiverBank'] = $model->getReceiverBank();
        }
        if ($model->getReceiverNRB()) {
            $result['receiverNRB'] = $model->getReceiverNRB();
        }
        if ($model->getReceiverName()) {
            $result['receiverName'] = $model->getReceiverName();
        }
        if ($model->getReceiverAddress()) {
            $result['receiverAddress'] = $model->getReceiverAddress();
        }
        if ($model->getSenderBank()) {
            $result['senderBank'] = $model->getSenderBank();
        }
        if ($model->getSenderNRB()) {
            $result['senderNRB'] = $model->getSenderNRB();
        }
        if ($model->getRecurringDataRecurringAction()) {
            $result['recurringData']['recurringAction'] = $model->getRecurringDataRecurringAction();
        }
        if ($model->getRecurringDataClientHash()) {
            $result['recurringData']['clientHash'] = $model->getRecurringDataClientHash();
        }
        if ($model->getCardDataIndex()) {
            $result['cardData']['index'] = $model->getCardDataIndex();
        }
        if ($model->getCardDataValidityYear()) {
            $result['cardData']['validityYear'] = $model->getCardDataValidityYear();
        }

        if ($model->getCardDataValidityMonth()) {
            $result['cardData']['validityMonth'] = $model->getCardDataValidityMonth();
        }
        if ($model->getCardDataIssuer()) {
            $result['cardData']['issuer'] = $model->getCardDataIssuer();
        }

        if ($model->getCardDataBin()) {
            $result['cardData']['bin'] = $model->getCardDataBin();
        }
        if ($model->getCardDataMask()) {
            $result['cardData']['mask'] = $model->getCardDataMask();
        }

        if ($model->getHash()) {
            $result['Hash'] = $model->getHash();
        }

        return $result;
    }

    /**
     * Transforms ITN request into model.
     *
     * @param SimpleXMLElement $xml
     *
     * @return ItnIn
     * @throws \Exception
     */
    public static function toModel(SimpleXMLElement $xml)
    {
        $transaction            = $xml->transactions->transaction;
        $customerData           = $transaction->customerData;
        $recurringData          = $transaction->recurringData;
        $cardData               = $transaction->cardData;
        $isClearanceTransaction = self::isArrayClearanceTransaction($transaction);

        $model = new ItnIn();
        if (isset($xml->serviceID)) {
            $model->setServiceId((string)$xml->serviceID);
        }

        if (isset($transaction->orderID)) {
            $model->setOrderId((string)$transaction->orderID);
        }
        if (isset($transaction->remoteID)) {
            $model->setRemoteId((string)$transaction->remoteID);
        }
        if (isset($transaction->remoteOutID)) {
            $model->setRemoteOutID((string)$transaction->remoteOutID);
        }

        if (isset($transaction->amount)) {
            $model->setAmount((string)$transaction->amount);
        }
        if (isset($transaction->currency)) {
            $model->setCurrency((string)$transaction->currency);
        }
        if (isset($transaction->gatewayID)) {
            $model->setGatewayId((string)$transaction->gatewayID);
        }
        if (isset($transaction->paymentDate)) {
            $paymentDate = DateTime::createFromFormat(
                Gateway::DATETIME_FORMAT,
                (string)$transaction->paymentDate,
                new DateTimeZone(Gateway::DATETIME_TIMEZONE)
            );
            $model->setPaymentDate($paymentDate);
            if ($paymentDate > new DateTime('now', new DateTimeZone(Gateway::DATETIME_TIMEZONE))) {
                Logger::log(
                    Logger::WARNING,
                    sprintf('paymentDate "%s" is in future', $paymentDate->format($paymentDate::ATOM)),
                    ['itn' => $xml]
                );
            }
        }
        if (isset($transaction->paymentStatus)) {
            switch ((string)$transaction->paymentStatus) {
                case ItnIn::PAYMENT_STATUS_PENDING:
                case ItnIn::PAYMENT_STATUS_SUCCESS:
                case ItnIn::PAYMENT_STATUS_FAILURE:
                    $model->setPaymentStatus((string)$transaction->paymentStatus);
                    break;

                default:
                    Logger::log(
                        Logger::EMERGENCY,
                        sprintf('Not supported paymentStatus="%s"', (string)$transaction->paymentStatus),
                        ['itn' => $xml]
                    );
                    break;
            }
        }
        if (isset($transaction->paymentStatusDetails)) {
            switch ((string)$transaction->paymentStatusDetails) {
                case ItnIn::PAYMENT_STATUS_DETAILS_AUTHORIZED:
                case ItnIn::PAYMENT_STATUS_DETAILS_ACCEPTED:
                case ItnIn::PAYMENT_STATUS_DETAILS_REJECTED:
                case ItnIn::PAYMENT_STATUS_DETAILS_INCORRECT_AMOUNT:
                case ItnIn::PAYMENT_STATUS_DETAILS_EXPIRED:
                case ItnIn::PAYMENT_STATUS_DETAILS_CANCELLED:
                case ItnIn::PAYMENT_STATUS_DETAILS_ANOTHER_ERROR:
                case ItnIn::PAYMENT_STATUS_DETAILS_REJECTED_BY_USER:
                    $model->setPaymentStatusDetails((string)$transaction->paymentStatusDetails);
                    break;

                default:
                    Logger::log(
                        Logger::EMERGENCY,
                        sprintf('Not supported paymentStatusDetails="%s"', (string)$transaction->paymentStatusDetails),
                        ['itn' => $xml]
                    );
                    break;
            }
        }
        if (isset($transaction->invoiceNumber)) {
            $model->setInvoiceNumber((string)$transaction->invoiceNumber);
        }
        if (isset($transaction->customerNumber)) {
            $model->setCustomerNumber((string)$transaction->customerNumber);
        }
        if (isset($transaction->customerEmail)) {
            $model->setCustomerEmail((string)$transaction->customerEmail);
        }

        if (isset($transaction->addressIP)) {
            $model->setAddressIp((string)$transaction->addressIP);
        }
        if (isset($transaction->title) && !$isClearanceTransaction) {
            $model->setTitle((string)$transaction->title);
        }
        if (isset($customerData->fName)) {
            $model->setCustomerDatafName((string)$customerData->fName);
        }
        if (isset($customerData->lName)) {
            $model->setCustomerDatalName((string)$customerData->lName);
        }
        if (isset($customerData->streetName)) {
            $model->setCustomerDataStreetName((string)$customerData->streetName);
        }
        if (isset($customerData->streetHouseNo)) {
            $model->setCustomerDataStreetHouseNo((string)$customerData->streetHouseNo);
        }
        if (isset($customerData->streetStaircaseNo)) {
            $model->setCustomerDataStreetStaircaseNo((string)$customerData->streetStaircaseNo);
        }
        if (isset($customerData->streetPremiseNo)) {
            $model->setCustomerDataStreetPremiseNo((string)$customerData->streetPremiseNo);
        }
        if (isset($customerData->postalCode)) {
            $model->setCustomerDataPostalCode((string)$customerData->postalCode);
        }
        if (isset($customerData->city)) {
            $model->setCustomerDataCity((string)$customerData->city);
        }
        if (isset($customerData->nrb)) {
            $model->setCustomerDataNrb((string)$customerData->nrb);
        }
        if (isset($customerData->senderData)) {
            $model->setCustomerDataSenderData((string)$customerData->senderData);
        }
        if (isset($transaction->verificationStatus)) {
            switch ((string)$transaction->verificationStatus) {
                case ItnIn::VERIFICATION_STATUS_NEGATIVE:
                case ItnIn::VERIFICATION_STATUS_PENDING:
                case ItnIn::VERIFICATION_STATUS_POSITIVE:
                    $model->setVerificationStatus((string)$transaction->verificationStatus);
                    break;

                default:
                    Logger::log(
                        Logger::EMERGENCY,
                        sprintf('Not supported verificationStatus="%s"', (string)$transaction->verificationStatus),
                        ['itn' => $xml]
                    );
                    break;
            }
        }
        if (isset($transaction->startAmount)) {
            $model->setStartAmount((float)$transaction->startAmount);
        }

        if (isset($transaction->transferDate)) {
            $transferDate = DateTime::createFromFormat(
                Gateway::DATETIME_FORMAT,
                (string)$transaction->transferDate,
                new DateTimeZone(Gateway::DATETIME_TIMEZONE)
            );
            $model->setTransferDate($transferDate);
            if ($transferDate > new DateTime('now', new DateTimeZone(Gateway::DATETIME_TIMEZONE))) {
                Logger::log(
                    Logger::WARNING,
                    sprintf('transferDate "%s" is in future', $transferDate->format($transferDate::ATOM)),
                    ['itn' => $xml]
                );
            }
        }
        if (isset($transaction->transferStatus)) {
            switch ((string)$transaction->transferStatus) {
                case ItnIn::PAYMENT_STATUS_PENDING:
                case ItnIn::PAYMENT_STATUS_SUCCESS:
                case ItnIn::PAYMENT_STATUS_FAILURE:
                    $model->setTransferStatus((string)$transaction->transferStatus);
                    break;

                default:
                    Logger::log(
                        Logger::EMERGENCY,
                        sprintf('Not supported transferStatus="%s"', (string)$transaction->transferStatus),
                        ['itn' => $xml]
                    );
                    break;
            }
        }
        if (isset($transaction->transferStatusDetails)) {
            switch ((string)$transaction->transferStatusDetails) {
                case ItnIn::PAYMENT_STATUS_DETAILS_AUTHORIZED:
                case ItnIn::PAYMENT_STATUS_DETAILS_CANCELLED:
                case ItnIn::PAYMENT_STATUS_DETAILS_CONFIRMED:
                case ItnIn::PAYMENT_STATUS_DETAILS_ANOTHER_ERROR:
                    $model->setTransferStatusDetails((string)$transaction->transferStatusDetails);
                    break;

                default:
                    Logger::log(
                        Logger::EMERGENCY,
                        sprintf(
                            'Not supported transferStatusDetails="%s"',
                            (string)$transaction->transferStatusDetails
                        ),
                        ['itn' => $xml]
                    );
                    break;
            }
        }
        if (isset($transaction->title) && $isClearanceTransaction) {
            $model->setTitle((string)$transaction->title);
        }
        if (isset($transaction->receiverBank)) {
            $model->setReceiverBank((string)$transaction->receiverBank);
        }
        if (isset($transaction->receiverNRB)) {
            $model->setReceiverNRB((string)$transaction->receiverNRB);
        }
        if (isset($transaction->receiverName)) {
            $model->setReceiverName((string)$transaction->receiverName);
        }
        if (isset($transaction->receiverAddress)) {
            $model->setReceiverAddress((string)$transaction->receiverAddress);
        }
        if (isset($transaction->senderBank)) {
            $model->setSenderBank((string)$transaction->senderBank);
        }
        if (isset($transaction->senderNRB)) {
            $model->setSenderNRB((string)$transaction->senderNRB);
        }

        if (isset($recurringData->recurringAction)) {
            $model->setRecurringDataRecurringAction((string)$recurringData->recurringAction);
        }
        if (isset($recurringData->clientHash)) {
            $model->setRecurringDataClientHash((string)$recurringData->clientHash);
        }
        if (isset($cardData->index)) {
            $model->setCardDataIndex((string)$cardData->index);
        }
        if (isset($cardData->validityYear)) {
            $model->setCardDataValidityYear((string)$cardData->validityYear);
        }
        if (isset($cardData->validityMonth)) {
            $model->setCardDataValidityMonth((string)$cardData->validityMonth);
        }
        if (isset($cardData->issuer)) {
            $model->setCardDataIssuer((string)$cardData->issuer);
        }
        if (isset($cardData->bin)) {
            $model->setCardDataBin((string)$cardData->bin);
        }
        if (isset($cardData->mask)) {
            $model->setCardDataMask((string)$cardData->mask);
        }

        if (isset($xml->hash)) {
            $model->setHash((string)$xml->hash);
        }

        $model->validate();

        return $model;
    }
}
