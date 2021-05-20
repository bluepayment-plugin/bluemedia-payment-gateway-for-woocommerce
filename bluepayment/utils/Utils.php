<?php

final class Utils
{
    public static function is_ssl()
    {
        if (is_ssl() || get_option('woocommerce_force_ssl_checkout') == 'yes' || class_exists('WordPressHTTPS')) {
            return true;
        }

        return false;
    }

    public static function get_current_currency()
    {
        $currency = get_woocommerce_currency();
        $currencies_dictionary = new CurrencyDictionary();

        return in_array(strtoupper($currency), $currencies_dictionary->getAvailableCurrencies())
            ? $currency
            : PaymentEnum::DEFAULT_CURRENCY;
    }
}
