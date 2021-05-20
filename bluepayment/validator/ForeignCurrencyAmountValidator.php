<?php

final class ForeignCurrencyAmountValidator implements ValidatorInterface
{
    private $order;

    public function __construct(WC_Order $order)
    {
        $this->order = $order;
    }

    /**
     * For currency other than PLN amount must not be lower than 1 EUR|GPB|USD
     *
     * @throws Exception
    */
    public function validate()
    {
        $data = $this->order->data;

        if ($data['currency'] !== PaymentEnum::DEFAULT_CURRENCY && $data['total'] < 2) {
            throw new Exception(__("The amount may not be less than 2", 'bluepayment-gateway-for-woocommerce') . get_woocommerce_currency());
        }
    }
}
