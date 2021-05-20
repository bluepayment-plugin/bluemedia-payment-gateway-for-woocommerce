<?php

final class InstallmentPaymentAmountValidator implements ValidatorInterface
{
    private $order_minimal_amount;

    public function __construct($order_minimal_amount = 100)
    {
        $this->order_minimal_amount = $order_minimal_amount;
    }

    public function validate()
    {
        if (WC()->cart->get_totals()['total'] <= $this->order_minimal_amount) {
            wc_add_notice(
                __("In the case of an installment payment, the minimum order amount should be higher than 100", 'bluepayment-gateway-for-woocommerce') . get_woocommerce_currency(),
                'error'
            );
        }
    }
}
