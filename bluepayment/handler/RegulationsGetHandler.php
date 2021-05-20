<?php

final class RegulationsGetHandler extends GatewayHandler implements HandlerInterface
{
    public function handle($params = null)
    {
        try {
            $response = $this->gateway->doPaymentRegulations();
        } catch (Exception $exception) {
            $response = [];
            $this->logger->log('[BM Bluepayment] Error when fetching payment regulations from BlueMedia API');
            $this->logger->log((string) $exception);
        }

        return $response;
    }
}
