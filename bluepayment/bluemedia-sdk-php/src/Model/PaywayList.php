<?php

namespace BlueMedia\OnlinePayments\Model;

use BlueMedia\OnlinePayments\Util\Validator;
use DomainException;

class PaywayList extends AbstractModel
{

    /**
     * Service id.
     *
     * @var int
     */
    private $serviceId = 0;

    /**
     * Message id.
     *
     * @var string
     */
    private $messageId = '';

    /**
     * Gateways.
     *
     * @var array
     */
    private $gateway = [];

    /**
     * Hash.
     *
     * @var string
     */
    private $hash = '';

    /**
     * Sets serviceID.
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
     * Returns serviceID.
     *
     * @return int
     */
    public function getServiceId()
    {
        return $this->serviceId;
    }

    /**
     * Sets messageID.
     *
     * @param int $messageId
     *
     * @return $this
     */
    public function setMessageId($messageId)
    {
        $this->messageId = (string)$messageId;

        return $this;
    }

    /**
     * Returns messageID.
     *
     * @return string
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    public function addGateway(Gateway $gateway)
    {
        $this->gateway[$gateway->getGatewayId()] = $gateway;

        return $this;
    }

    public function getGateways()
    {
        return $this->gateway;
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
     * Validates model.
     *
     * @param string $serviceId
     * @param string $messageId
     *
     * @throws DomainException
     */
    public function validate($serviceId = '', $messageId = '')
    {
        if (empty($this->serviceId)) {
            throw new DomainException('ServiceId cannot be empty');
        }
        if (empty($this->messageId)) {
            throw new DomainException('MessageId cannot be empty');
        }
        if (empty($this->hash)) {
            throw new DomainException('Hash cannot be empty');
        }
        if ($this->serviceId !== $serviceId) {
            throw new DomainException(
                sprintf('Not equal ServiceId, $this->serviceId: "%s", $serviceId: "%s"', $this->serviceId, $serviceId)
            );
        }
        if ($this->messageId !== $messageId) {
            throw new DomainException(
                sprintf('Not equal MessageId, $this->messageId: "%s", $messageId: "%s"', $this->messageId, $messageId)
            );
        }
    }
}
