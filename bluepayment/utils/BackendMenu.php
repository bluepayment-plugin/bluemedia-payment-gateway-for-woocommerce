<?php

final class BackendMenu
{
    public function menu($notifyUrl, $backUrl)
    {
        return [
            'enabled' => [
                'title' => __("On/Off", 'bluepayment-gateway-for-woocommerce'),
                'label' => __("Turn on the Blue Media online payment system", 'bluepayment-gateway-for-woocommerce'),
                'type' => 'select',
                'description' => '<a href="https://platnosci.bm.pl/rejestracja?utm_source=woocommerce_backend_signup" target="_blank"> ' .__("Register an account in the Blue Media system", 'bluepayment-gateway-for-woocommerce') . '</a>',
                'options' => [
                    'no' => __("Off", 'bluepayment-gateway-for-woocommerce'),
                    'yes' => __("On", 'bluepayment-gateway-for-woocommerce'),
                ],
            ],
            'title' => [
                'title' => __("Title", 'bluepayment-gateway-for-woocommerce'),
                'type' => 'text',
                'description' => __("The title that the Store user sees", 'bluepayment-gateway-for-woocommerce'),
                'default' => __("Blue Media online payment system", 'bluepayment-gateway-for-woocommerce'),
            ],
            'description' => [
                'title' => __("Description", 'bluepayment-gateway-for-woocommerce'),
                'type' => 'textarea',
                'description' => __("Description of the gateway that the user sees when creating the order", 'bluepayment-gateway-for-woocommerce'),
                'default' => __("Pay via Blue Media online payment system: payment by credit card, online bank transfer or fast bank transfer\n", 'bluepayment-gateway-for-woocommerce'),
            ],
            'payment_domain' => [
                'title' => __("Blue Media online payment system domain", 'bluepayment-gateway-for-woocommerce'),
                'type' => 'text',
                'description' => __("When testing, enter: pay-accept.bm.pl; when you want to work in production: pay.bm.pl", 'bluepayment-gateway-for-woocommerce'),
                'css' => 'width: 100%',
                'default' => 'pay-accept.bm.pl'
            ],
            'status_pending' => [
                'title' => __("Payment pending status", 'bluepayment-gateway-for-woocommerce'),
                'label' => __("Payment pending status", 'bluepayment-gateway-for-woocommerce'),
                'type' => 'select',
                'description' => __("Payment pending status determines the manner of reserving the goods", 'bluepayment-gateway-for-woocommerce'),
                'options' => [
                    'pending' => __("Pending payment - Order received, payment has not started. Pending payment (unpaid)", 'bluepayment-gateway-for-woocommerce'),
                    'on-hold' => __("On-Hold - Waiting for payment - stock level is reduced, but you must confirm the payment", 'bluepayment-gateway-for-woocommerce'),
                ],
            ],
            'url_notify' => [
                'type' => 'hidden',
                'default' => $notifyUrl,

            ],
            'url_back' => [
                'type' => 'hidden',
                'description' => '',
                'default' => $backUrl,
            ]
        ];
    }
}
