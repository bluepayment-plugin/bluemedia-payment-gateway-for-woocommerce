<?php

use BlueMedia\OnlinePayments\Gateway;

final class WC_Payment_Gateway_BlueMedia_Smartney_Popup extends WC_Payment_Gateway
{
    const ID_PAYMENT_GATEWAY_SMARTNEY_POPUP = 'bluemedia_payment_gateway_smartney_popup';

    protected $paymentGatewayBlueMedia;
    private $logger;

    public function __construct()
    {
        $this->id = self::ID_PAYMENT_GATEWAY_SMARTNEY_POPUP;
        $this->paymentGatewayBlueMedia = new WC_Payment_Gateway_BlueMedia();
        $this->logger = new Logger(PaymentEnum::ID_PAYMENT_GATEWAY_BLUEMEDIA);

        $this->title = __("Smartney", 'bluepayment-gateway-for-woocommerce');
        $this->method_title = __("Buy now, pay later", 'bluepayment-gateway-for-woocommerce');
        $this->method_description = __("Pay via Smartney online payment system", 'bluepayment-gateway-for-woocommerce');
    }

    public function is_available()
    {
        return $this->paymentGatewayBlueMedia->is_available();
    }

    public function admin_options()
    {
        wp_redirect(admin_url('admin.php?page=wc-settings&tab=checkout&section=' . $this->paymentGatewayBlueMedia->id));
    }

    public function get_icon()
    {
        $icon = sprintf(
            '<img src="%s" alt="%s" />',
            WP_PLUGIN_URL . '/' . plugin_basename(dirname(dirname(__FILE__))) . '/assets/images/smartney.png',
            __("Pay via Smartney online payment system", 'bluepayment-gateway-for-woocommerce')
        );

        return apply_filters('woocommerce_gateway_icon', $icon, $this->id);
    }

    public function get_description()
    {
        require_once dirname(__FILE__) . '/../template/_partials/order/bluemedia-smartney-popup.tpl.php';
        return;
    }

    public function process_payment($order_id)
    {
        global $woocommerce;

        $order = new WC_Order($order_id);
        (new ForeignCurrencyAmountValidator($order))->validate();

        if (!empty($this->settings['status_pending']) && $this->settings['status_pending'] == 'on-hold') {
            $order->update_status('on-hold', __("Awaiting payment", 'bluepayment-gateway-for-woocommerce'));
        } else {
            $order->update_status('pending', __("Order received", 'bluepayment-gateway-for-woocommerce'));
        }

        $woocommerce->cart->empty_cart();

        return [
            'result' => 'success',
            'redirect' => add_query_arg([
                'order_id' => $order_id,
                'gateway_id' => \BlueMedia\OnlinePayments\Model\Gateway::GATEWAY_ID_SMARTNEY,
                'regulation_id' => ''
            ], $this->getNotifyUrl()),
        ];
    }

    public function getNotifyUrl()
    {
        $result = add_query_arg('wc-api', 'WC_Payment_Gateway_BlueMedia', home_url('/'));
        if (Utils::is_ssl()) {
            $result = str_replace('https:', 'http:', $result);
        }

        return $result;
    }
}
