<?php

final class DefaultGatewayOrderSorter
{
    public function sort(array $channelsList)
    {
        $defaultGatewayOrder = explode(',', PaymentEnum::DEFAULT_GATEWAY_ORDER);

        uksort(
            $channelsList,
            function ($key1, $key2) use ($defaultGatewayOrder) {
                return (array_search($key1, $defaultGatewayOrder) > array_search($key2, $defaultGatewayOrder));
            }
        );

        return $channelsList;
    }
}
