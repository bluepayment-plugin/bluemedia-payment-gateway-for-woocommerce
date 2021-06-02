<?php

abstract class OrderStatusMessageDictionary
{
    private const ONHOLD = 'on-hold';

    private const ORDER_STATUS_MESSAGE = [
        self::ONHOLD => 'payment_in_progress',
    ];

    public static function getMessage($order_status): ?string
    {

        return self::hasKey($order_status) ? self::ORDER_STATUS_MESSAGE[$order_status] : null;
    }

    private static function hasKey($order_status): bool
    {
        return array_key_exists($order_status, self::ORDER_STATUS_MESSAGE);
    }
}
