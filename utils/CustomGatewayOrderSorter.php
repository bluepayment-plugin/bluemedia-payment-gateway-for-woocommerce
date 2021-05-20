<?php

final class CustomGatewayOrderSorter
{
    public function sort(array $channels, array $channelsList)
    {
        $gatewayOrder = [];

        if (empty($channels)) {
            return $channelsList;
        }

        foreach ($channels as $channel) {
            $gatewayOrder[] = $channel['gatewayID'];
        }

        if (empty($gatewayOrder)) {
            return $channelsList;
        }

        uksort(
            $channelsList,
            function ($key1, $key2) use ($gatewayOrder) {
                return (array_search($key1, $gatewayOrder) > array_search($key2, $gatewayOrder));
            }
        );

        return $channelsList;
    }
}
