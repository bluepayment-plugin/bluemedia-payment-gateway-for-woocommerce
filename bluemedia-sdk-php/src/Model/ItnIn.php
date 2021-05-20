<?php

namespace BlueMedia\OnlinePayments\Model;

use BlueMedia\OnlinePayments\Action\ITN\Transformer;
use BlueMedia\OnlinePayments\Util\Formatter;
use BlueMedia\OnlinePayments\Util\Validator;
use DateTime;
use DomainException;

class ItnIn extends AbstractModel
{
    const PAYMENT_STATUS_PENDING = 'PENDING';
    const PAYMENT_STATUS_SUCCESS = 'SUCCESS';
    const PAYMENT_STATUS_FAILURE = 'FAILURE';

    const PAYMENT_STATUS_DETAILS_AUTHORIZED = 'AUTHORIZED';
    const PAYMENT_STATUS_DETAILS_ACCEPTED = 'ACCEPTED';
    const PAYMENT_STATUS_DETAILS_INCORRECT_AMOUNT = 'INCORRECT_AMOUNT';
    const PAYMENT_STATUS_DETAILS_EXPIRED = 'EXPIRED';
    const PAYMENT_STATUS_DETAILS_CONFIRMED = 'CONFIRMED';
    const PAYMENT_STATUS_DETAILS_CANCELLED = 'CANCELLED';
    const PAYMENT_STATUS_DETAILS_ANOTHER_ERROR = 'ANOTHER_ERROR';
    const PAYMENT_STATUS_DETAILS_REJECTED = 'REJECTED';
    const PAYMENT_STATUS_DETAILS_REJECTED_BY_USER = 'REJECTED_BY_USER';

    const CONFIRMATION_CONFIRMED = 'CONFIRMED';
    const CONFIRMATION_NOT_CONFIRMED = 'NOTCONFIRMED';

    const VERIFICATION_STATUS_PENDING = 'PENDING';
    const VERIFICATION_STATUS_POSITIVE = 'POSITIVE';
    const VERIFICATION_STATUS_NEGATIVE = 'NEGATIVE';

    const VERIFICATION_STATUS_REASON_NAME = 'NAME';
    const VERIFICATION_STATUS_REASON_NRB = 'NRB';
    const VERIFICATION_STATUS_REASON_TITLE = 'TITLE';
    const VERIFICATION_STATUS_REASON_STREET = 'STREET';
    const VERIFICATION_STATUS_REASON_HOUSE_NUMBER = 'HOUSE_NUMBER';
    const VERIFICATION_STATUS_REASON_STAIRCASE = 'STAIRCASE';
    const VERIFICATION_STATUS_REASON_PREMISE_NUMBER = 'PREMISE_NUMBER';
    const VERIFICATION_STATUS_REASON_POSTAL_CODE = 'POSTAL_CODE';
    const VERIFICATION_STATUS_REASON_CITY = 'CITY';
    const VERIFICATION_STATUS_REASON_BLACKLISTED = 'BLACKLISTED';
    const VERIFICATION_STATUS_REASON_SHOP_FORMAL_REQUIREMENTS = 'SHOP_FORMAL_REQUIREMENTS';
    const VERIFICATION_STATUS_REASON_NEED_FEEDBACK = 'NEED_FEEDBACK';

    const CARD_DATA_ISSUER_VISA = 'VISA';
    const CARD_DATA_ISSUER_MASTERCARD = 'MASTERCARD';
    const CARD_DATA_ISSUER_MAESTRO = 'MAESTRO';
    const CARD_DATA_ISSUER_AMERICAN_EXPRESS = 'AMERICAN EXPRESS';
    const CARD_DATA_ISSUER_DISCOVER = 'DISCOVER';
    const CARD_DATA_ISSUER_DINERS = 'DINERS';

    /**
     * Service id.
     *
     * @var int
     */
    protected $serviceId;

    /**
     * Payment order id.
     *
     * @var string
     */
    protected $orderId = '';

    /**
     * Payment remote id.
     *
     * @var string
     */
    protected $remoteId = '';

    /**
     * Payment amount.
     *
     * @var float
     */
    protected $amount;

    /**
     * Payment currency.
     *
     * @var string
     */
    protected $currency = '';

    /**
     * Payment gateway id.
     *
     * @var int
     */
    protected $gatewayId;

    /**
     * Payment date.
     *
     * @var DateTime | null
     */
    protected $paymentDate;

    /**
     * Payment status.
     *
     * @var string
     */
    protected $paymentStatus = '';

    /**
     * Payment status details.
     *
     * @var string
     */
    protected $paymentStatusDetails = '';

    /**
     * Customer IP address.
     *
     * @var string
     */
    protected $addressIp = '';

    /**
     * Tytuł wpłaty.
     *
     * @var string
     */
    protected $title = '';

    /**
     * Imię płatnika.
     *
     * @var string
     */
    protected $customerDatafName = '';

    /**
     * Nazwisko płatnika.
     *
     * @var string
     */
    protected $customerDatalName = '';

    /**
     * Nazwa ulicy płatnika.
     *
     * @var string
     */
    protected $customerDataStreetName = '';

    /**
     * Numer domu płatnika.
     *
     * @var string
     */
    protected $customerDataStreetHouseNo = '';

    /**
     * Numer klatki płatnika.
     *
     * @var string
     */
    protected $customerDataStreetStaircaseNo = '';

    /**
     * Numer lokalu płatnika.
     *
     * @var string
     */
    protected $customerDataStreetPremiseNo = '';

    /**
     * Kod pocztowy adresu płatnika.
     *
     * @var string
     */
    protected $customerDataPostalCode = '';

    /**
     * Customer address - city.
     *
     * @var string
     */
    protected $customerDataCity = '';

    /**
     * Customer bank account number.
     *
     * @var string
     */
    protected $customerDataNrb = '';

    /**
     * Transaction authorisation date.
     *
     * @var DateTime | null
     */
    protected $transferDate;

    /**
     * Transaction authorisation status.
     *
     * @var string
     */
    protected $transferStatus = '';

    /**
     * Transaction authorisation details.
     *
     * @var string
     */
    protected $transferStatusDetails = '';

    /**
     * Transaction receiver bank.
     *
     * @var string
     */
    protected $receiverBank = '';

    /**
     * Transaction receiver bank account number.
     *
     * @var string
     */
    protected $receiverNRB = '';

    /**
     * Transaction receiver name.
     *
     * @var string
     */
    protected $receiverName = '';

    /**
     * Transaction receiver address.
     *
     * @var string
     */
    protected $receiverAddress = '';

    /**
     * Transaction sender bank.
     *
     * @var string
     */
    protected $senderBank = '';

    /**
     * Transaction sender account bank.
     *
     * @var string
     */
    protected $senderNRB = '';

    /**
     * Hash.
     *
     * @var string
     */
    protected $hash = '';

    /**
     * Payment remote out id.
     *
     * @var string
     */
    protected $remoteOutID = '';

    /**
     * Numer dokumentu finansowego w Serwisie.
     *
     * @var string
     */
    protected $invoiceNumber = '';

    /**
     * Numer Klienta w Serwisie.
     *
     * @var string
     */
    protected $customerNumber = '';

    /**
     * Adres email Klienta.
     *
     * @var string
     */
    protected $customerEmail = '';

    /**
     * Numer telefonu Klienta.
     *
     * @var string
     */
    protected $customerPhone = '';

    /**
     * Dane płatnika w postaci niepodzielonej.
     *
     * @var string
     */
    protected $customerDataSenderData = '';

    /**
     * Status weryfikacji płatnika.
     *
     * @var string
     */
    protected $verificationStatus = '';

    /**
     * Lista zawierająca powody negatywnej, lub oczekującej weryfikacji.
     *
     * @var array
     */
    protected $verificationStatusReasons = [];

    /**
     * Kwota początkowa transakcji.
     *
     * @var float
     */
    protected $startAmount;

    /**
     * Akcja w procesie płatności automatycznej.
     *
     * @var string
     */
    protected $recurringDataRecurringAction = '';

    /**
     * Identyfikator płatności automatycznej generowany przez BM.
     *
     * @var string
     */
    protected $recurringDataClientHash = '';

    /**
     * Index karty.
     *
     * @var string
     */
    protected $cardDataIndex = '';

    /**
     * Ważność karty w formacie YYYY.
     *
     * @var string
     */
    protected $cardDataValidityYear = '';

    /**
     * Ważność karty w formacie mm.
     *
     * @var string
     */
    protected $cardDataValidityMonth = '';

    /**
     * Typ karty.
     *
     * @var string
     */
    protected $cardDataIssuer = '';

    /**
     * Pierwsze 6 cyfr numeru karty.
     *
     * @var string
     */
    protected $cardDataBin = '';

    /**
     * Ostatnie 4 cyfry numeru karty.
     *
     * @var string
     */
    protected $cardDataMask = '';

    /**
     * Sets addressIp.
     *
     * @param string $addressIp
     *
     * @return $this
     */
    public function setAddressIp($addressIp)
    {
        Validator::validateIP($addressIp);
        $this->addressIp = (string)$addressIp;

        return $this;
    }

    /**
     * Returns addressIp.
     *
     * @return string
     */
    public function getAddressIp()
    {
        return $this->addressIp;
    }

    /**
     * Sets amount.
     *
     * @param float $amount
     *
     * @return $this
     */
    public function setAmount($amount)
    {
        Validator::validateAmount($amount);
        $this->amount = $amount;

        return $this;
    }

    /**
     * Returns amount.
     *
     * @return string
     */
    public function getAmount()
    {
        return Formatter::formatAmount($this->amount);
    }

    /**
     * Sets currency.
     *
     * @param string $currency
     *
     * @return $this
     */
    public function setCurrency($currency)
    {
        Validator::validateCurrency($currency);
        $this->currency = (string)$currency;

        return $this;
    }

    /**
     * Returns currency.
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Sets customerDataCity.
     *
     * @param string $customerDataCity
     *
     * @return $this
     */
    public function setCustomerDataCity($customerDataCity)
    {
        $this->customerDataCity = (string)$customerDataCity;

        return $this;
    }

    /**
     * Returns customerDataCity.
     *
     * @return string
     */
    public function getCustomerDataCity()
    {
        return $this->customerDataCity;
    }

    /**
     * Sets customerDataNrb.
     *
     * @param string $customerDataNrb
     *
     * @return $this
     */
    public function setCustomerDataNrb($customerDataNrb)
    {
        Validator::validateNrb($customerDataNrb);
        $this->customerDataNrb = (string)$customerDataNrb;

        return $this;
    }

    /**
     * Returns customerDataNrb.
     *
     * @return string
     */
    public function getCustomerDataNrb()
    {
        return $this->customerDataNrb;
    }

    /**
     * Sets customerDataPostalCode.
     *
     * @param string $customerDataPostalCode
     *
     * @return $this
     */
    public function setCustomerDataPostalCode($customerDataPostalCode)
    {
        $this->customerDataPostalCode = (string)$customerDataPostalCode;

        return $this;
    }

    /**
     * Returns customerDataPostalCode.
     *
     * @return string
     */
    public function getCustomerDataPostalCode()
    {
        return $this->customerDataPostalCode;
    }

    /**
     * Sets customerDataStreetHouseNo.
     *
     * @param string $customerDataStreetHouseNo
     *
     * @return $this
     */
    public function setCustomerDataStreetHouseNo($customerDataStreetHouseNo)
    {
        $this->customerDataStreetHouseNo = (string)$customerDataStreetHouseNo;

        return $this;
    }

    /**
     * Returns customerDataStreetHouseNo.
     *
     * @return string
     */
    public function getCustomerDataStreetHouseNo()
    {
        return $this->customerDataStreetHouseNo;
    }

    /**
     * Sets customerDataStreetName.
     *
     * @param string $customerDataStreetName
     *
     * @return $this
     */
    public function setCustomerDataStreetName($customerDataStreetName)
    {
        $this->customerDataStreetName = (string)$customerDataStreetName;

        return $this;
    }

    /**
     * Returns customerDataStreetName.
     *
     * @return string
     */
    public function getCustomerDataStreetName()
    {
        return $this->customerDataStreetName;
    }

    /**
     * Sets customerDataStreetPremiseNo.
     *
     * @param string $customerDataStreetPremiseNo
     *
     * @return $this
     */
    public function setCustomerDataStreetPremiseNo($customerDataStreetPremiseNo)
    {
        $this->customerDataStreetPremiseNo = (string)$customerDataStreetPremiseNo;

        return $this;
    }

    /**
     * Returns customerDataStreetPremiseNo.
     *
     * @return string
     */
    public function getCustomerDataStreetPremiseNo()
    {
        return $this->customerDataStreetPremiseNo;
    }

    /**
     * Sets customerDataStreetStaircaseNo.
     *
     * @param string $customerDataStreetStaircaseNo
     *
     * @return $this
     */
    public function setCustomerDataStreetStaircaseNo($customerDataStreetStaircaseNo)
    {
        $this->customerDataStreetStaircaseNo = (string)$customerDataStreetStaircaseNo;

        return $this;
    }

    /**
     * Returns customerDataStreetStaircaseNo.
     *
     * @return string
     */
    public function getCustomerDataStreetStaircaseNo()
    {
        return $this->customerDataStreetStaircaseNo;
    }

    /**
     * Sets customerDatafName.
     *
     * @param string $customerDatafName
     *
     * @return $this
     */
    public function setCustomerDatafName($customerDatafName)
    {
        $this->customerDatafName = (string)$customerDatafName;

        return $this;
    }

    /**
     * Returns customerDatafName.
     *
     * @return string
     */
    public function getCustomerDatafName()
    {
        return $this->customerDatafName;
    }

    /**
     * Sets customerDatalName.
     *
     * @param string $customerDatalName
     *
     * @return $this
     */
    public function setCustomerDatalName($customerDatalName)
    {
        $this->customerDatalName = (string)$customerDatalName;

        return $this;
    }

    /**
     * Returns customerDatalName.
     *
     * @return string
     */
    public function getCustomerDatalName()
    {
        return $this->customerDatalName;
    }

    /**
     * Sets gatewayId.
     *
     * @param int $gatewayId
     *
     * @return $this
     */
    public function setGatewayId($gatewayId)
    {
        Validator::validateGatewayId($gatewayId);
        $this->gatewayId = (int)$gatewayId;

        return $this;
    }

    /**
     * Returns gatewayId.
     *
     * @return int
     */
    public function getGatewayId()
    {
        return $this->gatewayId;
    }

    /**
     * Sets hash.
     *
     * @param string $hash
     *
     * @return $this
     */
    public function setHash($hash)
    {
        Validator::validateHash($hash);
        $this->hash = (string)$hash;

        return $this;
    }

    /**
     * Returns hash.
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Sets orderId.
     *
     * @param string $orderId
     *
     * @return $this
     */
    public function setOrderId($orderId)
    {
        Validator::validateOrderId($orderId);
        $this->orderId = (string)$orderId;

        return $this;
    }

    /**
     * Returns orderId.
     *
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Sets paymentDate.
     *
     * @param DateTime $paymentDate
     *
     * @return $this
     */
    public function setPaymentDate(DateTime $paymentDate)
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    /**
     * Returns paymentDate.
     *
     * @return DateTime | null
     */
    public function getPaymentDate()
    {
        return $this->paymentDate;
    }

    /**
     * Sets paymentStatus.
     *
     * @param string $paymentStatus
     *
     * @return $this
     */
    public function setPaymentStatus($paymentStatus)
    {
        $this->paymentStatus = (string)$paymentStatus;

        return $this;
    }

    /**
     * Returns paymentStatus.
     *
     * @return string
     */
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    /**
     * Sets paymentStatusDetails.
     *
     * @param string $paymentStatusDetails
     *
     * @return $this
     */
    public function setPaymentStatusDetails($paymentStatusDetails)
    {
        $this->paymentStatusDetails = (string)$paymentStatusDetails;

        return $this;
    }

    /**
     * Returns paymentStatusDetails.
     *
     * @return string
     */
    public function getPaymentStatusDetails()
    {
        return $this->paymentStatusDetails;
    }

    /**
     * Sets remoteId.
     *
     * @param string $remoteId
     *
     * @return $this
     */
    public function setRemoteId($remoteId)
    {
        $this->remoteId = (string)$remoteId;

        return $this;
    }

    /**
     * Returns remoteId.
     *
     * @return string
     */
    public function getRemoteId()
    {
        return $this->remoteId;
    }

    /**
     * Sets serviceId.
     *
     * @param int $serviceId
     *
     * @return $this
     */
    public function setServiceId($serviceId)
    {
        Validator::validateServiceId($serviceId);
        $this->serviceId = (int)$serviceId;

        return $this;
    }

    /**
     * Returns serviceId.
     *
     * @return int
     */
    public function getServiceId()
    {
        return $this->serviceId;
    }

    /**
     * Sets tytuł wpłaty.
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        Validator::validateTitle($title);
        $this->title = (string)$title;

        return $this;
    }

    /**
     * Returns tytuł wpłaty.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets receiverAddress.
     *
     * @param string $receiverAddress
     *
     * @return $this
     */
    public function setReceiverAddress($receiverAddress)
    {
        $this->receiverAddress = (string)$receiverAddress;

        return $this;
    }

    /**
     * Returns receiverAddress.
     *
     * @return string
     */
    public function getReceiverAddress()
    {
        return $this->receiverAddress;
    }

    /**
     * Sets receiverBank.
     *
     * @param string $receiverBank
     *
     * @return $this
     */
    public function setReceiverBank($receiverBank)
    {
        $this->receiverBank = (string)$receiverBank;

        return $this;
    }

    /**
     * Returns receiverBank.
     *
     * @return string
     */
    public function getReceiverBank()
    {
        return $this->receiverBank;
    }

    /**
     * Sets receiverNRB.
     *
     * @param string $receiverNRB
     *
     * @return $this
     */
    public function setReceiverNRB($receiverNRB)
    {
        $this->receiverNRB = (string)$receiverNRB;

        return $this;
    }

    /**
     * Returns receiverNRB.
     *
     * @return string
     */
    public function getReceiverNRB()
    {
        return $this->receiverNRB;
    }

    /**
     * Sets receiverName.
     *
     * @param string $receiverName
     *
     * @return $this
     */
    public function setReceiverName($receiverName)
    {
        $this->receiverName = (string)$receiverName;

        return $this;
    }

    /**
     * Returns receiverName.
     *
     * @return string
     */
    public function getReceiverName()
    {
        return $this->receiverName;
    }

    /**
     * Sets senderBank.
     *
     * @param string $senderBank
     *
     * @return $this
     */
    public function setSenderBank($senderBank)
    {
        $this->senderBank = (string)$senderBank;

        return $this;
    }

    /**
     * Returns senderBank.
     *
     * @return string
     */
    public function getSenderBank()
    {
        return $this->senderBank;
    }

    /**
     * Sets senderNRB.
     *
     * @param string $senderNRB
     *
     * @return $this
     */
    public function setSenderNRB($senderNRB)
    {
        $this->senderNRB = (string)$senderNRB;

        return $this;
    }

    /**
     * Returns senderNRB.
     *
     * @return string
     */
    public function getSenderNRB()
    {
        return $this->senderNRB;
    }

    /**
     * Sets transferDate.
     *
     * @param DateTime $transferDate
     *
     * @return $this
     */
    public function setTransferDate(DateTime $transferDate)
    {
        $this->transferDate = $transferDate;

        return $this;
    }

    /**
     * Returns transferDate.
     *
     * @return DateTime | null
     */
    public function getTransferDate()
    {
        return $this->transferDate;
    }

    /**
     * Sets transferStatus.
     *
     * @param string $transferStatus
     *
     * @return $this
     */
    public function setTransferStatus($transferStatus)
    {
        $this->transferStatus = (string)$transferStatus;

        return $this;
    }

    /**
     * Returns transferStatus.
     *
     * @return string
     */
    public function getTransferStatus()
    {
        return $this->transferStatus;
    }

    /**
     * Sets transferStatusDetails.
     *
     * @param string $transferStatusDetails
     *
     * @return $this
     */
    public function setTransferStatusDetails($transferStatusDetails)
    {
        $this->transferStatusDetails = (string)$transferStatusDetails;

        return $this;
    }

    /**
     * Returns transferStatusDetails.
     *
     * @return string
     */
    public function getTransferStatusDetails()
    {
        return $this->transferStatusDetails;
    }

    /**
     * @return string
     */
    public function getRemoteOutID()
    {
        return $this->remoteOutID;
    }

    /**
     * @param string $remoteOutID
     *
     * @return ItnIn
     */
    public function setRemoteOutID($remoteOutID)
    {
        $this->remoteOutID = $remoteOutID;

        return $this;
    }

    /**
     * @return string
     */
    public function getInvoiceNumber()
    {
        return $this->invoiceNumber;
    }

    /**
     * @param string $invoiceNumber
     *
     * @return ItnIn
     */
    public function setInvoiceNumber($invoiceNumber)
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerNumber()
    {
        return $this->customerNumber;
    }

    /**
     * @param string $customerNumber
     *
     * @return ItnIn
     */
    public function setCustomerNumber($customerNumber)
    {
        $this->customerNumber = $customerNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerEmail()
    {
        return $this->customerEmail;
    }

    /**
     * @param string $customerEmail
     *
     * @return ItnIn
     */
    public function setCustomerEmail($customerEmail)
    {
        $this->customerEmail = $customerEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerPhone()
    {
        return $this->customerPhone;
    }

    /**
     * @param string $customerPhone
     *
     * @return ItnIn
     */
    public function setCustomerPhone($customerPhone)
    {
        $this->customerPhone = $customerPhone;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerDataSenderData()
    {
        return $this->customerDataSenderData;
    }

    /**
     * @param string $customerDataSenderData
     *
     * @return ItnIn
     */
    public function setCustomerDataSenderData($customerDataSenderData)
    {
        $this->customerDataSenderData = $customerDataSenderData;

        return $this;
    }

    /**
     * @return string
     */
    public function getVerificationStatus()
    {
        return $this->verificationStatus;
    }

    /**
     * @param string $verificationStatus
     *
     * @return ItnIn
     */
    public function setVerificationStatus($verificationStatus)
    {
        $this->verificationStatus = $verificationStatus;

        return $this;
    }

    /**
     * @return array
     */
    public function getVerificationStatusReasons()
    {
        return $this->verificationStatusReasons;
    }

    /**
     * @param array $verificationStatusReasons
     *
     * @return ItnIn
     */
    public function setVerificationStatusReasons($verificationStatusReasons)
    {
        $this->verificationStatusReasons = $verificationStatusReasons;

        return $this;
    }

    /**
     * Returns kwotę początkową transakcji.
     *
     * @return float | null
     */
    public function getStartAmount()
    {
        return $this->startAmount;
    }

    /**
     * Sets kwotę początkową transakcji.
     *
     * @param float $startAmount
     *
     * @return ItnIn
     */
    public function setStartAmount($startAmount)
    {
        $this->startAmount = $startAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getRecurringDataRecurringAction()
    {
        return $this->recurringDataRecurringAction;
    }

    /**
     * @param string $recurringDataRecurringAction
     *
     * @return ItnIn
     */
    public function setRecurringDataRecurringAction($recurringDataRecurringAction)
    {
        $this->recurringDataRecurringAction = $recurringDataRecurringAction;

        return $this;
    }

    /**
     * @return string
     */
    public function getRecurringDataClientHash()
    {
        return $this->recurringDataClientHash;
    }

    /**
     * @param string $recurringDataClientHash
     *
     * @return ItnIn
     */
    public function setRecurringDataClientHash($recurringDataClientHash)
    {
        $this->recurringDataClientHash = $recurringDataClientHash;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardDataIndex()
    {
        return $this->cardDataIndex;
    }

    /**
     * @param string $cardDataIndex
     *
     * @return ItnIn
     */
    public function setCardDataIndex($cardDataIndex)
    {
        $this->cardDataIndex = $cardDataIndex;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardDataValidityYear()
    {
        return $this->cardDataValidityYear;
    }

    /**
     * @param string $cardDataValidityYear
     *
     * @return ItnIn
     */
    public function setCardDataValidityYear($cardDataValidityYear)
    {
        $this->cardDataValidityYear = $cardDataValidityYear;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardDataValidityMonth()
    {
        return $this->cardDataValidityMonth;
    }

    /**
     * @param string $cardDataValidityMonth
     *
     * @return ItnIn
     */
    public function setCardDataValidityMonth($cardDataValidityMonth)
    {
        $this->cardDataValidityMonth = $cardDataValidityMonth;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardDataIssuer()
    {
        return $this->cardDataIssuer;
    }

    /**
     * @param string $cardDataIssuer
     *
     * @return ItnIn
     */
    public function setCardDataIssuer($cardDataIssuer)
    {
        $this->cardDataIssuer = $cardDataIssuer;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardDataBin()
    {
        return $this->cardDataBin;
    }

    /**
     * @param string $cardDataBin
     *
     * @return ItnIn
     */
    public function setCardDataBin($cardDataBin)
    {
        $this->cardDataBin = $cardDataBin;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardDataMask()
    {
        return $this->cardDataMask;
    }

    /**
     * @param string $cardDataMask
     *
     * @return ItnIn
     */
    public function setCardDataMask($cardDataMask)
    {
        $this->cardDataMask = $cardDataMask;

        return $this;
    }

    /**
     * Validates model.
     *
     * @return void
     */
    public function validate()
    {
        if (empty($this->serviceId)) {
            throw new DomainException('ServiceId cannot be empty');
        }
        if (empty($this->orderId)) {
            throw new DomainException('OrderId cannot be empty');
        }
        if (empty($this->remoteId)) {
            throw new DomainException('RemoteId cannot be empty');
        }
        if (empty($this->amount)) {
            throw new DomainException('Amount cannot be empty');
        }
        if (!($this->amount === $this->getAmount())) {
            throw new DomainException('Amount in wrong format');
        }
        if (empty($this->currency)) {
            throw new DomainException('Currency cannot be empty');
        }
        if (empty($this->paymentDate)) {
            throw new DomainException('PaymentDate cannot be empty');
        }
        if (empty($this->paymentStatus)) {
            throw new DomainException('PaymentStatus cannot be empty');
        }
        switch ($this->paymentStatus) {
            case self::PAYMENT_STATUS_PENDING:
            case self::PAYMENT_STATUS_SUCCESS:
            case self::PAYMENT_STATUS_FAILURE:
                break;

            default:
                throw new DomainException(sprintf('PaymentStatus="%s" not supported', $this->paymentStatus));
                break;
        }
        if (empty($this->hash)) {
            throw new DomainException('Hash cannot be empty');
        }
    }

    /**
     * Returns object data as array.
     *
     * @return array
     * @deprecated Use Transformer::objectToArray()
     */
    public function toArray()
    {
        trigger_error(
            __METHOD__.'() is deprecated. Use Transformer::objectToArray() instead.',
            E_USER_DEPRECATED
        );

        return Transformer::modelToArray($this);
    }
}
