<?php

use BlueMedia\OnlinePayments\Model\Gateway;

final class WC_Payment_Gateway_BlueMedia_Installment extends WC_Payment_Gateway
{
    const ID_PAYMENT_GATEWAY_BLUEMEDIA_INSTALLMENT = 'bluemedia_payment_gateway_installment';

	protected $paymentGatewayBlueMedia;
	
	public function __construct() 
	{
		$this->id = self::ID_PAYMENT_GATEWAY_BLUEMEDIA_INSTALLMENT;
		$this->paymentGatewayBlueMedia = new WC_Payment_Gateway_BlueMedia();

		$this->title =  __("Installments", 'bluepayment-gateway-for-woocommerce');
		$this->method_title = __("Pay via Blue Media online payment system", 'bluepayment-gateway-for-woocommerce');
        $this->method_description = __("Payment in installments", 'bluepayment-gateway-for-woocommerce');
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
		$image_path = $this->paymentGatewayBlueMedia->settings['backgorund_channels'][Utils::get_current_currency()][Gateway::GATEWAY_ID_IFRAME]['iconURL'];
		$icon = sprintf(
			'<img src="%s" alt="%s" />',
            $image_path,
            __("Pay via Blue Media online payment system", 'bluepayment-gateway-for-woocommerce')
		);

		return apply_filters('woocommerce_gateway_icon', $icon, $this->id);
	}

    public function process_payment($order_id)
    {
        global $woocommerce;

        $order = new WC_Order($order_id);

        if (!empty($this->paymentGatewayBlueMedia->settings['status_pending']) && $this->paymentGatewayBlueMedia->settings['status_pending'] == 'on-hold') {
            $order->update_status('on-hold', __("Awaiting payment", 'bluepayment-gateway-for-woocommerce'));
        } else {
            $order->update_status('pending', __("Order received", 'bluepayment-gateway-for-woocommerce'));
        }

        $woocommerce->cart->empty_cart();

        return [
            'result'   => 'success',
            'redirect' => add_query_arg(
                ['order_id' => $order_id, 'gateway_id' => (int) WC()->session->get('bm_background_payment')],
                $this->paymentGatewayBlueMedia->url_notify
            )
        ];
    }
}