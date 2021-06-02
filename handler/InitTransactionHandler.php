<?php

final class InitTransactionHandler extends GatewayHandler implements HandlerInterface
{
    public function handle($params = null)
    {
        try {
            $response = $this->gateway->doInitTransaction($params);

            $response = json_decode(json_encode((array) $response), true);

            $this->logger->log('[BM Bluepayment] doInitTransaction response: ');
            $this->logger->log(var_export($response, true));
        } catch (Exception $exception) {
            $response = ['reason' => $exception->getMessage()];
            $this->logger->log('[BM Bluepayment] Error when calling init transaction in BlueMedia gateway');
            $this->logger->log('[BM Bluepayment] Transaction data:' . json_encode($params->toArray()));
            $this->logger->log((string) $exception);
        }

        return $response;
    }
}
