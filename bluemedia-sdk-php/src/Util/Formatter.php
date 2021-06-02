<?php

namespace BlueMedia\OnlinePayments\Util;

class Formatter
{

    /**
     * Format amount.
     *
     * @param float|number $amount
     *
     * @return string
     */
    public static function formatAmount($amount)
    {
        $amount = str_replace(",", ".", (string) $amount);
        $amount = preg_replace('/\.(?=.*\.)/', '', $amount);
        $amount = number_format(floatval($amount), 2, '.', '');

        return $amount;
    }

    /**
     * Format description.
     *
     * @param string $value
     *
     * @return string
     */
    public static function formatDescription($value)
    {
        $value = trim($value);

        if (EnvironmentRequirements::hasPhpExtension('iconv')) {
            $return = iconv('UTF-8', 'ASCII//TRANSLIT', $value);

            return $return;
        }

        if (EnvironmentRequirements::hasPhpExtension('mbstring')) {
            $tmp = ini_get('mbstring.substitute_character');
            @ini_set('mbstring.substitute_character', 'none');

            $return = mb_convert_encoding($value, 'ASCII', 'UTF-8');
            @ini_set('mbstring.substitute_character', $tmp);

            return $return;
        }

        return $value;
    }
}
