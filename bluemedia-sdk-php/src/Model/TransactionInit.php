<?php

namespace BlueMedia\OnlinePayments\Model;

use BlueMedia\OnlinePayments\Util\Formatter;
use BlueMedia\OnlinePayments\Util\Sorter;
use BlueMedia\OnlinePayments\Util\Validator;
use DateTime;
use DomainException;

/**
 * Model for init transaction
 *
 * @package BlueMedia\OnlinePayments\Model
 */
class TransactionInit extends AbstractModel
{
    /**
     * Service id.
     *
     * @var int
     */
    protected $serviceId;

    /**
     * Transaction order id.
     *
     * @var string
     */
    protected $orderId ='';

    /**
     * Transaction amount.
     *
     * @var float
     */
    protected $amount;

    /**
     * Transaction description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Transaction gateway id.
     *
     * @var int | null
     */
    protected $gatewayId;

    /**
     * @var
     */
    protected $acceptanceTime;

    /**
     * @var
     */
    protected $acceptanceState;

    /**
     * @var
     */
    protected $regulationId;

    /**
     * Transaction currency.
     *
     * @var string
     */
    protected $currency = '';

    /**
     * Transaction customer e-mail address.
     *
     * @var string
     */
    protected $customerEmail = '';

    /**
     * Customer IP address.
     *
     * @var string
     */
    protected $customerIp = '';

    /**
     * Transaction title.
     *
     * @var string
     */
    protected $title = '';

    /**
     * Hash.
     *
     * @var string
     */
    protected $hash = '';

    /**
     * Transaction validity time.
     *
     * @var DateTime
     */
    protected $validityTime;

    /**
     * Transaction link validity time.
     *
     * @var DateTime
     */
    protected $linkValidityTime;

    /**
     * Transaction authorization code.
     *
     * @var string
     */
    protected $authorizationCode = '';


    /**
     * @var string
     */
    protected $screenType = '';

    /**
     * @return string
     */
    public function getScreenType()
    {
        return $this->screenType;
    }

    /**
     * @param string $screenType
     *
     * @return  $this
     */
    public function setScreenType(string $screenType)
    {
        $this->screenType = $screenType;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorizationCode()
    {
        return $this->authorizationCode;
    }

    /**
     * @param string $authorizationCode
     *
     * @return $this
     */
    public function setAuthorizationCode($authorizationCode)
    {
        $this->authorizationCode = $authorizationCode;

        return $this;
    }

    /**
     * Set amount.
     *
     * @param float|number $amount
     *
     * @return $this
     */
    public function setAmount($amount)
    {
        $amount = (float)Formatter::formatAmount($amount);
        Validator::validateAmount($amount);
        $this->amount = (float)$amount;

        return $this;
    }

    /**
     * Return amount.
     *
     * @return string
     */
    public function getAmount()
    {
        return Formatter::formatAmount($this->amount);
    }

    /**
     * Set currency.
     *
     * @param string $currency
     *
     * @return $this
     */
    public function setCurrency($currency)
    {
        Validator::validateCurrency($currency);
        $this->currency = (string)mb_strtoupper($currency);

        return $this;
    }

    public function setDefaultRegulationAcceptanceState($state)
    {
        Validator::validateState($state);
        $this->acceptanceState = (string)mb_strtoupper($state);

        return $this;
    }

    public function getDefaultRegulationAcceptanceState()
    {
        return $this->acceptanceState;
    }

    public function setDefaultRegulationAcceptanceID($state)
    {
        Validator::validateAcceptanceId($state);
        $this->regulationId = (string)mb_strtoupper($state);

        return $this;
    }

    public function getDefaultRegulationAcceptanceID()
    {
        return $this->regulationId;
    }

    public function setDefaultRegulationAcceptanceTime($time)
    {
        Validator::validateAcceptanceTime($time);
        $this->acceptanceTime = (string)mb_strtoupper($time);

        return $this;
    }

    public function getDefaultRegulationsAcceptanceTime()
    {
        return $this->acceptanceTime;
    }

    /**
     * Return currency.
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set customerEmail.
     *
     * @param string $customerEmail
     *
     * @return $this
     */
    public function setCustomerEmail($customerEmail)
    {
        Validator::validateEmail($customerEmail);
        $this->customerEmail = (string)mb_strtolower($customerEmail);

        return $this;
    }

    /**
     * Return customerEmail.
     *
     * @return string
     */
    public function getCustomerEmail()
    {
        return $this->customerEmail;
    }

    /**
     * Set customerIp.
     *
     * @param string $customerIp
     *
     * @return $this
     */
    public function setCustomerIp($customerIp)
    {
        Validator::validateIP($customerIp);
        $this->customerIp = (string)$customerIp;

        return $this;
    }

    /**
     * Return customerIp.
     *
     * @return string
     */
    public function getCustomerIp()
    {
        return $this->customerIp;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $description = Formatter::formatDescription($description);
        Validator::validateDescription($description);
        $this->description = (string)$description;

        return $this;
    }

    /**
     * Return description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set gatewayId.
     *
     * @param string $gatewayId
     *
     * @return $this
     */
    public function setGatewayId($gatewayId)
    {
        Validator::validateGatewayId($gatewayId);
        $this->gatewayId = (string)$gatewayId;

        return $this;
    }

    /**
     * Return gatewayId.
     *
     * @return string | null
     */
    public function getGatewayId()
    {
        return $this->gatewayId;
    }

    /**
     * Set hash.
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
     * Return hash.
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set orderId.
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
     * Return orderId.
     *
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set serviceId.
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
     * Return serviceId.
     *
     * @return int | null
     */
    public function getServiceId()
    {
        return $this->serviceId;
    }

    /**
     * Set title.
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
     * Return title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set linkValidityTime.
     *
     * @param DateTime $linkValidityTime
     *
     * @return $this
     */
    public function setLinkValidityTime(DateTime $linkValidityTime)
    {
        $this->linkValidityTime = $linkValidityTime;

        return $this;
    }

    /**
     * Return linkValidityTime.
     *
     * @return DateTime
     */
    public function getLinkValidityTime()
    {
        return $this->linkValidityTime;
    }

    /**
     * Set validityTime.
     *
     * @param DateTime $validityTime
     *
     * @return $this
     */
    public function setValidityTime(DateTime $validityTime)
    {
        $this->validityTime = $validityTime;

        return $this;
    }

    /**
     * Return validityTime.
     *
     * @return DateTime
     */
    public function getValidityTime()
    {
        return $this->validityTime;
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
        if (empty($this->amount)) {
            throw new DomainException('Amount cannot be empty');
        }
        if (empty($this->hash)) {
            throw new DomainException('Hash cannot be empty');
        }
    }

    /**
     * Return object data as array.
     *
     * @return array
     */
    public function toArray()
    {
        $result              = [];
        $result['ServiceID'] = $this->getServiceId();
        $result['OrderID']   = $this->getOrderId();
        $result['Amount']    = $this->getAmount();

        if (!empty($this->getDescription())) {
            $result['Description'] = $this->getDescription();
        }

        if ($this->getGatewayId() !== null) {
            $result['GatewayID'] = $this->getGatewayId();
        }

        if (!empty($this->getCurrency())) {
            $result['Currency'] = $this->getCurrency();
        }

        if (!empty($this->getCustomerEmail())) {
            $result['CustomerEmail'] = $this->getCustomerEmail();
        }

        if (!empty($this->getCustomerIp())) {
            $result['CustomerIP'] = $this->getCustomerIp();
        }

        if (!empty($this->getTitle())) {
            $result['Title'] = $this->getTitle();
        }

        if (!empty($this->getAuthorizationCode())) {
            $result['AuthorizationCode'] = $this->getAuthorizationCode();
        }

        if (!empty($this->getScreenType())) {
            $result['ScreenType'] = $this->getScreenType();
        }

        if (!empty($this->getDefaultRegulationAcceptanceID())) {
            $result['DefaultRegulationAcceptanceID'] = $this->getDefaultRegulationAcceptanceID();
        }

        if (!empty($this->getDefaultRegulationAcceptanceState())) {
            $result['DefaultRegulationAcceptanceState'] = $this->getDefaultRegulationAcceptanceState();
        }

        if (!empty($this->getDefaultRegulationsAcceptanceTime())) {
            $result['DefaultRegulationAcceptanceTime'] = $this->getDefaultRegulationsAcceptanceTime();
        }

        $result['Hash'] = $this->getHash();

        return Sorter::sortTransactionParams($result);
    }
}
