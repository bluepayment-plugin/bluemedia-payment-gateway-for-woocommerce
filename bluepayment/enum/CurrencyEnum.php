<?php

abstract class CurrencyEnum
{
    const USD = 'USD';
    const EUR = 'EUR';
    const PLN = 'PLN';
    const GBP = 'GBP';

    public static function getEnumConstants()
    {
        return [
            self::USD,
            self::EUR,
            self::PLN,
            self::GBP,
        ];
    }
}