<?php

use BlueMedia\OnlinePayments\Gateway;

final class WC_Payment_Gateway_BlueMedia_GPay_Popup extends WC_Payment_Gateway
{
    const ID_PAYMENT_GATEWAY_GPAY_POPUP = 'bluemedia_payment_gateway_gpay_popup';

    protected $paymentGatewayBlueMedia;
    private $logger;

    public function __construct()
    {
        $this->id = self::ID_PAYMENT_GATEWAY_GPAY_POPUP;
        $this->paymentGatewayBlueMedia = new WC_Payment_Gateway_BlueMedia();
        $this->logger = new Logger(PaymentEnum::ID_PAYMENT_GATEWAY_BLUEMEDIA);

        $this->title = __("Google Pay", 'bluepayment-gateway-for-woocommerce');
        $this->method_title = __("Blue Media online payment system", 'bluepayment-gateway-for-woocommerce');
        $this->method_description = __("Payment via Blue Media online payment system with Google Pay", 'bluepayment-gateway-for-woocommerce');
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
            WP_PLUGIN_URL . '/' . plugin_basename(dirname(dirname(__FILE__))) . '/assets/images/gpay.png',
            __("Pay via Blue Media online payment system", 'bluepayment-gateway-for-woocommerce')
        );

        return apply_filters('woocommerce_gateway_icon', $icon, $this->id);
    }

    public function get_description()
    {
        $bmGateway = 'bluemedia';
        $bmEnvironment = 'TEST';
        $bmMerchantDomain = $_SERVER['HTTP_HOST'];

        $paymentDomain = $this->paymentGatewayBlueMedia->settings['payment_domain'];
        if ($paymentDomain == 'pay.bm.pl') {
            $bmEnvironment = 'PRODUCTION';
        }

        $bmCurrencyCode = Utils::get_current_currency();

        $bmServiceId = $this->paymentGatewayBlueMedia->get_currency_service_id($bmCurrencyCode);

        if (WC()->cart->prices_include_tax) {
            $bmTotalPrice = WC()->cart->cart_contents_total + WC()->cart->tax_total;
        } else {
            $bmTotalPrice = WC()->cart->cart_contents_total;
        }

        $merchantDebugParams = [
            'bmGateway'        => $bmGateway,
            'bmMerchantDomain' => $bmMerchantDomain,
            'paymentDomain'    => $paymentDomain,
            'bmEnvironment'    => $bmEnvironment,
            'bmCurrencyCode'   => $bmCurrencyCode,
            'bmServiceId'      => $bmServiceId,
            'bmTotalPrice'     => $bmTotalPrice,
        ];

        $this->logger->log('[BM Bluepayment] GPay Merchant Params:' . json_encode($merchantDebugParams));

        $gateway = new Gateway(
            $bmServiceId,
            $this->paymentGatewayBlueMedia->hash_key,
            $this->paymentGatewayBlueMedia->settings['payment_domain'] === Gateway::PAYMENT_DOMAIN_LIVE ? Gateway::MODE_LIVE : Gateway::MODE_SANDBOX
        );

        $paymentParameters = [
            'ServiceID'      => $bmServiceId,
            'MerchantDomain' => $bmMerchantDomain,
        ];

        $paymentParameters['Hash'] = Gateway::generateHash($paymentParameters);

        try {
            $fields = is_array($paymentParameters) ? http_build_query($paymentParameters) : $paymentParameters;

            $curl = curl_init($gateway::getActionUrl($gateway::GET_MERCHANT_INFO));
            curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($curl, CURLOPT_HTTPHEADER, ['BmHeader: pay-bm', 'Content-Type: application/x-www-form-urlencoded']);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            $curlResponse = curl_exec($curl);

            curl_close($curl);
            if ($curlResponse === 'ERROR') {
                return false;
            }

            $merchantInfo = json_decode($curlResponse);
        } catch (Exception $e) {
            $this->logger->log('[BM Bluepayment] BlueMedia google pay error:');
            $this->logger->log((string) $e);
        }

        $bmGatewayMerchantId = $merchantInfo->acceptorId;
        $bmMerchantId = $merchantInfo->merchantId;
        $bmMerchantOrigin = $merchantInfo->merchantOrigin;
        $bmMerchantName = $merchantInfo->merchantName;
        $bmAuthJwt = $merchantInfo->authJwt;

        $merchantDebugResponse = [
            'bmGatewayMerchantId' => $bmGatewayMerchantId,
            'bmMerchantId' => $bmMerchantId,
            'bmMerchantOrigin' => $bmMerchantOrigin,
            'bmMerchantName' => $bmMerchantName,
            'bmAuthJwt' => $bmAuthJwt,
        ];

        $this->logger->log('[BM Bluepayment] GPay Merchant Response:' . json_encode($merchantDebugResponse));

        require_once dirname(__FILE__) . '/../template/_partials/order/bluemedia-gpay-popup.tpl.php';
        return;
    }

    public function process_payment($order_id)
    {
        global $woocommerce;

        $order = new WC_Order($order_id);
        $data = $order->data;

        $bmCurrencyCode = Utils::get_current_currency();
        $bmServiceId = $this->paymentGatewayBlueMedia->get_currency_service_id($bmCurrencyCode);
        $customerEmail = empty($data['billing']['email']) ? '' : $data['billing']['email'];

        $paymentToken = $_POST['bluemediaPaymentToken'];
        $paymentToken = stripslashes($paymentToken);
        $paymentToken = stripslashes($paymentToken);
        $paymentToken = trim($paymentToken,'"');
        $paymentToken = base64_encode($paymentToken);

        $gateway = new Gateway(
            $bmServiceId,
            $this->paymentGatewayBlueMedia->hash_key,
            $this->paymentGatewayBlueMedia->settings['payment_domain'] === Gateway::PAYMENT_DOMAIN_LIVE ? Gateway::MODE_LIVE : Gateway::MODE_SANDBOX
        );

        $paymentParameters = [
            'ServiceID'     => $bmServiceId,
            'OrderID'       => $order_id,
            'Amount'        => $data['total'],
            'Description'   => 'Google Pay Order ID ' . $order_id,
            'GatewayID'     => \BlueMedia\OnlinePayments\Model\Gateway::GATEWAY_ID_GOOGLE_PAY,
            'Currency'      => $data['currency'],
            'CustomerEmail' => $customerEmail,
            'CustomerIP'    => $data['customer_ip_address'],
            'Title'         => 'Google Pay Order ID ' . $order_id,
            'PaymentToken'  => $paymentToken,
        ];

        $paymentParameters['Hash'] = Gateway::generateHash($paymentParameters);

        $this->logger->log('[BM Bluepayment] GPay Payment Params:' . json_encode($paymentParameters));

        try {
            $fields = is_array($paymentParameters) ? http_build_query($paymentParameters) : $paymentParameters;

            $curl = curl_init($gateway::getActionUrl($gateway::PAYMENT_ACTON_PAYMENT));
            curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($curl, CURLOPT_HTTPHEADER, ['BmHeader: pay-bm-continue-transaction-url']);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            $curlResponse = curl_exec($curl);

            curl_close($curl);
            if ($curlResponse === 'ERROR') {
                return false;
            }

            $response_bm = simplexml_load_string($curlResponse, 'SimpleXMLElement', LIBXML_NOCDATA);
            $result = $response_bm = json_decode(json_encode((array) $response_bm), true);
        } catch (Exception $e) {
            $this->logger->log('[BM Bluepayment] BlueMedia gpay error:');
            $this->logger->log((string) $e);
        }

        $this->logger->log('[BM Bluepayment] GPay Payment Response:' . json_encode($result));

        if (!empty($this->paymentGatewayBlueMedia->settings['status_pending']) && $this->paymentGatewayBlueMedia->settings['status_pending'] == 'on-hold') {
            $order->update_status('on-hold', __("Awaiting payment", 'bluepayment-gateway-for-woocommerce'));
        } else {
            $order->update_status('pending', __("Order received", 'bluepayment-gateway-for-woocommerce'));
        }

        $woocommerce->cart->empty_cart();

        if ($result['status'] == 'SUCCESS') {
            $redirect = $this->paymentGatewayBlueMedia->getThankYouPage($order_id);
        } else {
            $redirect = $result['redirecturl'];
        }

        return [
            'result' => 'success',
            'redirect' => $redirect,
        ];
    }
}
