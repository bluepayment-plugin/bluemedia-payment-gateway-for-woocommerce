<?php

namespace BlueMedia\OnlinePayments\Model;

use DateTime;
use DomainException;

class Gateway extends AbstractModel
{
    const GATEWAY_ID_CARD = 1500;
    const GATEWAY_ID_MTRANSFER = 3;
    const GATEWAY_ID_MULTITRANSFER = 17;
    const GATEWAY_ID_BZWBK = 27;
    const GATEWAY_ID_BPH = 33;
    const GATEWAY_ID_PEKAO24PRZELEW = 52;
    const GATEWAY_ID_PEOPAY = 1037;
    const GATEWAY_ID_CA_ONLINE = 59;
    const GATEWAY_ID_R_PRZELEW = 76;
    const GATEWAY_ID_EUROBANK = 79;
    const GATEWAY_ID_ING = 68;
    const GATEWAY_ID_MILLENNIUM = 85;
    const GATEWAY_ID_BOS = 86;
    const GATEWAY_ID_MERITUM_BANK = 87;
    const GATEWAY_ID_CITI_HANDLOWY = 90;
    const GATEWAY_ID_ALIOR_BANK = 95;
    const GATEWAY_ID_PBS_BANK = 98;
    const GATEWAY_ID_NETBANK = 99;
    const GATEWAY_ID_POCZTOWY24 = 108;
    const GATEWAY_ID_TOYOTA_BANK = 117;
    const GATEWAY_ID_PLUS_BANK = 131;
    const GATEWAY_ID_GETIN_BANK = 513;
    const GATEWAY_ID_DEUTSCHE_BANK = 1002;
    const GATEWAY_ID_BNP_PARIBAS = 1035;
    const GATEWAY_ID_IPKO = 1063;
    const GATEWAY_ID_INTELIGO = 1064;
    const GATEWAY_ID_IKO = 1065;
    const GATEWAY_ID_VOLKSWAGEN_BANK = 21;
    const GATEWAY_ID_SPOLDZIELCZA_GRUPA_BANKOWA = 35;
    const GATEWAY_ID_BGZ = 71;
    const GATEWAY_ID_OTHER = 9;
    const GATEWAY_ID_BLIK = 509;
    const GATEWAY_ID_VISA_CHECKOUT = 1511;
    const GATEWAY_ID_GOOGLE_PAY = 1512;
    const GATEWAY_ID_IFRAME = 1506;
    const GATEWAY_ID_PG_TEST = 106;
    const GATEWAY_ID_SMARTNEY = 700;

    const GATEWAY_TYPE_PBL = 'PBL';
    const GATEWAY_TYPE_FAST_TRANSFER = 'Szybki przelew';

    /**
     * Cards gateways.
     *
     * @var array
     */
    private $gatewayTypesCard
        = [
            self::GATEWAY_ID_CARD => 1,
        ];

    /**
     * PBL gateways.
     *
     * @var array
     */
    private $gatewayTypesPbl
        = [
            self::GATEWAY_ID_MTRANSFER      => 1,
            self::GATEWAY_ID_MULTITRANSFER  => 1,
            self::GATEWAY_ID_BZWBK          => 1,
            self::GATEWAY_ID_BPH            => 1,
            self::GATEWAY_ID_PEKAO24PRZELEW => 1,
            self::GATEWAY_ID_PEOPAY         => 1,
            self::GATEWAY_ID_CA_ONLINE      => 1,
            self::GATEWAY_ID_R_PRZELEW      => 1,
            self::GATEWAY_ID_EUROBANK       => 1,
            self::GATEWAY_ID_ING            => 1,
            self::GATEWAY_ID_MILLENNIUM     => 1,
            self::GATEWAY_ID_BOS            => 1,
            self::GATEWAY_ID_MERITUM_BANK   => 1,
            self::GATEWAY_ID_CITI_HANDLOWY  => 1,
            self::GATEWAY_ID_ALIOR_BANK     => 1,
            self::GATEWAY_ID_PBS_BANK       => 1,
            self::GATEWAY_ID_NETBANK        => 1,
            self::GATEWAY_ID_POCZTOWY24     => 1,
            self::GATEWAY_ID_TOYOTA_BANK    => 1,
            self::GATEWAY_ID_PLUS_BANK      => 1,
            self::GATEWAY_ID_GETIN_BANK     => 1,
            self::GATEWAY_ID_DEUTSCHE_BANK  => 1,
            self::GATEWAY_ID_BNP_PARIBAS    => 1,
            self::GATEWAY_ID_IPKO           => 1,
            self::GATEWAY_ID_INTELIGO       => 1,
            self::GATEWAY_ID_IKO            => 1,
            self::GATEWAY_ID_VISA_CHECKOUT  => 1,
            self::GATEWAY_ID_GOOGLE_PAY     => 1,
            self::GATEWAY_ID_SMARTNEY       => 1,
        ];

    /**
     * Transfer types.
     *
     * @var array
     */
    private $gatewayTypesTransfer
        = [
            self::GATEWAY_ID_VOLKSWAGEN_BANK            => 1,
            self::GATEWAY_ID_SPOLDZIELCZA_GRUPA_BANKOWA => 1,
            self::GATEWAY_ID_BGZ                        => 1,
            self::GATEWAY_ID_OTHER                      => 1,
        ];

    /**
     * Gateway id.
     *
     * @var int
     */
    private $gatewayId = 0;

    /**
     * Gateway name.
     *
     * @var string
     */
    private $gatewayName = '';

    /**
     * Gateway type.
     *
     * @var string
     */
    private $gatewayType = '';

    /**
     * Bank name.
     *
     * @var string
     */
    private $bankName = '';

    /**
     * Icon URL.
     *
     * @var string
     */
    private $iconUrl = '';

    /**
     * Status date.
     *
     * @var DateTime
     */
    private $statusDate;

    /**
     * Returns gateway id.
     *
     * @return int
     */
    public function getGatewayId()
    {
        return $this->gatewayId;
    }

    /**
     * Sets gateway id.
     *
     * @param int $gatewayId
     *
     * @return $this
     */
    public function setGatewayId($gatewayId)
    {
        $this->gatewayId = (int)$gatewayId;

        return $this;
    }

    /**
     * Returns gateway name.
     *
     * @return string
     */
    public function getGatewayName()
    {
        return $this->gatewayName;
    }

    /**
     * Sets gateway name.
     *
     * @param string $gatewayName
     *
     * @return $this
     */
    public function setGatewayName($gatewayName)
    {
        $this->gatewayName = (string)$gatewayName;

        return $this;
    }

    /**
     * Returns gateway type.
     *
     * @return string
     */
    public function getGatewayType()
    {
        return $this->gatewayType;
    }

    /**
     * Sets gateway type.
     *
     * @param string $gatewayType
     *
     * @return $this
     */
    public function setGatewayType($gatewayType)
    {
        $this->gatewayType = (string)$gatewayType;

        return $this;
    }

    /**
     * Returns bank name.
     *
     * @return string
     */
    public function getBankName()
    {
        return $this->bankName;
    }

    /**
     * Sets bank name.
     *
     * @param string $bankName
     *
     * @return $this
     */
    public function setBankName($bankName)
    {
        $this->bankName = (string)$bankName;

        return $this;
    }

    /**
     * Returns icon URL.
     *
     * @return string
     */
    public function getIconUrl()
    {
        return $this->iconUrl;
    }

    /**
     * Sets icon URL.
     *
     * @param string $iconUrl
     *
     * @return $this
     */
    public function setIconUrl($iconUrl)
    {
        $this->iconUrl = (string)$iconUrl;

        return $this;
    }

    /**
     * Returns status date.
     *
     * @return DateTime | null
     */
    public function getStatusDate()
    {
        return $this->statusDate;
    }

    /**
     * Sets status date.
     *
     * @param DateTime $statusDate
     *
     * @return $this
     */
    public function setStatusDate(DateTime $statusDate)
    {
        $this->statusDate = $statusDate;

        return $this;
    }

    /**
     * Is gateway a card.
     *
     * @return bool
     */
    public function isCard()
    {
        return array_key_exists($this->gatewayId, $this->gatewayTypesCard);
    }

    /**
     * Is gateway an PBL.
     *
     * @return bool
     */
    public function isPbl()
    {
        return array_key_exists($this->gatewayId, $this->gatewayTypesPbl);
    }

    /**
     * Is gateway a transfer.
     *
     * @return bool
     */
    public function isTransfer()
    {
        return array_key_exists($this->gatewayId, $this->gatewayTypesTransfer);
    }

    /**
     * Returns information if gateway is given gateway id.
     *
     * @param int $gatewayId
     *
     * @return bool
     */
    public function isGateway($gatewayId)
    {
        return $this->gatewayId === $gatewayId;
    }

    /**
     * Validates model.
     *
     * @throws DomainException
     */
    public function validate()
    {
        if (empty($this->gatewayId)) {
            throw new DomainException('GatewayId cannot be empty');
        }
        if (empty($this->gatewayName)) {
            throw new DomainException('GatewayName cannot be empty');
        }
    }
}
