<?php

final class PaywayListHandler extends GatewayHandler implements HandlerInterface
{
    public function handle($params = null)
    {
        try {
            $response = $this->gateway
                ->doPaywayList()
                ->getGateways();
        } catch (Exception $exception) {
            $response = [];
            $this->logger->log('[BM Bluepayment] Error when fetching payway list from BlueMedia API');
            $this->logger->log((string) $exception);
        }

        return $response;
    }
}
