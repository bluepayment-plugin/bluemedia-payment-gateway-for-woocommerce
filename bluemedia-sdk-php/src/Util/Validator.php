<?php

namespace BlueMedia\OnlinePayments\Util;

use InvalidArgumentException;

class Validator
{
    /**
     * Validates string length.
     *
     * @param string $value
     * @param int    $maxLength
     *
     * @return bool
     */
    protected static function validateStringLength($value, $maxLength)
    {
        $length = mb_strlen($value);

        return !(is_string($value) && $length >= 1 && $length <= $maxLength);
    }

    /**
     * Validates amount.
     *
     * @param float $value
     *
     * @return void
     * @throws InvalidArgumentException
     *
     */
    public static function validateAmount($value)
    {
        if (mb_strlen(mb_substr($value, mb_strrpos($value, '.'))) > 14) {
            throw new InvalidArgumentException('Wrong Amount format, requires max 14 numbers before ".", only numbers');
        }

        $exploded = explode('.', $value);
        if (count($exploded) > 2) {
            throw new InvalidArgumentException('Wrong Amount format, only one "." is possible');
        }
    }

    /**
     * Validates currency code.
     *
     * @param string $value
     *
     * @return void
     * @throws InvalidArgumentException
     *
     */
    public static function validateCurrency($value)
    {
        if (self::validateStringLength($value, 3)) {
            throw new InvalidArgumentException('Wrong Currency format, requires max 3 characters, only letters');
        }
    }

    /**
     * @param $state
     */
    public static function validateState($state)
    {
        if (empty($state) || self::validateStringLength($state, 100)) {
            throw new InvalidArgumentException('Wrong state format, requires min 1 and max 100 characters');
        }
    }

    public static function validateAcceptanceId($id)
    {
        if (empty($id) || self::validateStringLength($id, 10)) {
            throw new InvalidArgumentException('Wrong state format, requires min 1 and max 10 characters');
        }
    }

    /**
     * @param $time
     */
    public static function validateAcceptanceTime($time)
    {
        if (empty($time) || self::validateStringLength($time, 19)) {
            throw new InvalidArgumentException('Wrong Time format, requires min 1 and max 19 characters');
        }
    }

    /**
     * Validates e-mail address.
     *
     * @param string $value
     *
     * @return void
     * @throws InvalidArgumentException
     *
     */
    public static function validateEmail($value)
    {
        if (self::validateStringLength($value, 60)) {
            throw new InvalidArgumentException('Wrong CustomerEmail format, requires max 60 characters');
        }
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Wrong CustomerEmail, given value is invalid e-mail address');
        }
    }

    /**
     * Validates IP address.
     *
     * @param string $value
     *
     * @return void
     * @throws InvalidArgumentException
     *
     */
    public static function validateIP($value)
    {
        if (self::validateStringLength($value, 15)) {
            throw new InvalidArgumentException('Wrong CustomerIP format, requires max 15 characters');
        }
        if (!filter_var($value, FILTER_VALIDATE_IP)) {
            throw new InvalidArgumentException('Wrong CustomerIP, not IP address');
        }
    }

    /**
     * Validates bank account number.
     *
     * @param string $value
     *
     * @return void
     * @throws InvalidArgumentException
     *
     */
    public static function validateNrb($value)
    {
        if (!is_numeric($value)) {
            throw new InvalidArgumentException('Wrong CustomerNRB format, requires only numbers');
        }
        if (mb_strlen($value) !== 26) {
            throw new InvalidArgumentException('Wrong CustomerNRB format, requires exactly 26 characters');
        }
    }

    /**
     * Validates tax country name.
     *
     * @param string $value
     *
     * @return void
     * @throws InvalidArgumentException
     *
     */
    public static function validateTaxCountry($value)
    {
        if (self::validateStringLength($value, 64)) {
            throw new InvalidArgumentException('Wrong TaxCountry format, requires max 64 characters');
        }
    }

    /**
     * Validates description.
     *
     * @param string $value
     *
     * @return void
     * @throws InvalidArgumentException
     *
     */
    public static function validateDescription($value)
    {
        if (self::validateStringLength($value, 79)) {
            throw new InvalidArgumentException('Wrong description format, requires max 79 characters');
        }
    }

    /**
     * Validates gateway id.
     *
     * @param string $value
     *
     * @return void
     * @throws InvalidArgumentException
     *
     */
    public static function validateGatewayId($value)
    {
        if (!is_numeric($value)) {
            throw new InvalidArgumentException('Wrong GatewayId format, requires only numbers');
        }

        $valueLength = mb_strlen($value);

        if (!($valueLength >= 1 && $valueLength <= 5)) {
            throw new InvalidArgumentException('Wrong GatewayId format, requires max 5 characters');
        }
    }

    /**
     * Validates hash.
     *
     * @param string $value
     *
     * @return void
     * @throws InvalidArgumentException
     *
     */
    public static function validateHash($value)
    {
        if (self::validateStringLength($value, 128)) {
            throw new InvalidArgumentException('Wrong hash format, requires max 128 characters');
        }
    }

    /**
     * Validates order id.
     *
     * @param string $value
     *
     * @return void
     * @throws InvalidArgumentException
     *
     */
    public static function validateOrderId($value)
    {
        if (self::validateStringLength($value, 32)) {
            throw new InvalidArgumentException('Wrong orderId format, requires max 32 characters');
        }
    }

    /**
     * Validates service id.
     *
     * @param string $value
     *
     * @return void
     * @throws InvalidArgumentException
     *
     */
    public static function validateServiceId($value)
    {
        $valueLength = mb_strlen($value);

        if (!(is_numeric($value) && $valueLength >= 1 && $valueLength <= 10)) {
            throw new InvalidArgumentException('Wrong ServiceId format, requires max 10 characters');
        }
    }

    /**
     * Validates receiver name.
     *
     * @param string $value
     *
     * @return void
     * @throws InvalidArgumentException
     *
     */
    public static function validateReceiverName($value)
    {
        if (self::validateStringLength($value, 35)) {
            throw new InvalidArgumentException('Wrong receiverName format, requires max 35 characters');
        }
    }

    /**
     * Validates title.
     *
     * @param string $value
     *
     * @return void
     * @throws InvalidArgumentException
     *
     */
    public static function validateTitle($value)
    {
        if (self::validateStringLength($value, 95)) {
            throw new InvalidArgumentException('Wrong Title format, requires max 95 characters');
        }
    }
}
