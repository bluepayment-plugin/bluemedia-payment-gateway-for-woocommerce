<?php

use BlueMedia\OnlinePayments\Model\Gateway as GatewayModel;
use BlueMedia\OnlinePayments\Model\ItnIn;
use BlueMedia\OnlinePayments\Gateway;

final class WC_Payment_Gateway_BlueMedia extends WC_Payment_Gateway
{
    public $logger;
    public $notifyUrl;
    public $backUrl;
    public $url_notify;
    public $bm_background_payment_channel_id;
    public $hash_key;
    public $service_id;
    public $background_payment_channels;
    public $hash_key_mode;
    private $bluepayment_settings;

    public function __construct()
	{
        $this->id = PaymentEnum::ID_PAYMENT_GATEWAY_BLUEMEDIA;
        $this->logger = new Logger($this->id);
        $this->method_title = __("Blue Media online payment system", 'bluepayment-gateway-for-woocommerce');
		$this->method_description = __("All payment forms (BLIK, payment cards, PBL and others)", 'bluepayment-gateway-for-woocommerce');
		$this->has_fields = true;

		$this->notifyUrl = $this->getNotifyUrl();
		$this->backUrl = $this->getBackUrl();

		// Load the form fields
        $this->form_fields = (new BackendMenu())->menu($this->notifyUrl, $this->backUrl);

		// Load the settings.
        $this->loadSettings();

		// Get setting values
		$this->title = $this->settings['title'];
		$this->description = $this->settings['description'];

		$this->enabled = $this->settings['enabled'];

		$this->url_notify = $this->settings['url_notify'];

		if (empty($this->url_notify)) {
			unset($this->url_notify);
			unset($this->settings['url_notify']);
			$this->url_notify = $this->settings['url_notify'] = $this->notifyUrl;
		}

		$this->url_back = $this->settings['url_back'];
		if (empty($this->url_back)) {
			unset($this->url_back);
			unset($this->settings['url_back']);
			$this->url_back = $this->settings['url_back'] = $this->backUrl;
		}

		$this->load_currency_settings();

		$this->bm_background_payment_channel_id = $this->get_bm_background_payment_channel_id();

		// Actions
		add_action('woocommerce_api_wc_payment_gateway_bluemedia', [$this, 'gateway_process'], 12);

		// Save settings
		add_action('woocommerce_update_options_payment_gateways', [$this, 'process_admin_options']);
		add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options']);
	}

    public function get_title()
    {
        return __($this->title, 'bluepayment-gateway-for-woocommerce');
    }

    public function get_description()
    {
        $currentCurrency = Utils::get_current_currency();

        if ($this->is_bluemedia_background_payment_enable($currentCurrency)) {
            $paymentChannels = $this->get_background_payment_channels($currentCurrency);
            if (!empty($paymentChannels) && !empty($currentCurrency)) {
                $startPaymentTranslation = __("Start the payment", 'bluepayment-gateway-for-woocommerce');

                require_once dirname(__FILE__) . '/../template/_partials/order/bluemedia-background-payments.tpl.php';
                return;
            }
        }

        return __($this->description, 'bluepayment-gateway-for-woocommerce');
    }

    public function get_icon()
    {
        $image_path = plugin_basename(dirname(dirname(__FILE__))) . '/assets/images/bluemedia.png';
        $icon = sprintf(
            '<img src="%s" alt="%s" />',
            WP_PLUGIN_URL . '/' . $image_path,
            __("Pay via Blue Media online payment system", 'bluepayment-gateway-for-woocommerce')
        );

        return apply_filters('woocommerce_gateway_icon', $icon, $this->id);
    }

    public function admin_options()
    {
        return require_once dirname(__FILE__) . '/../template/bluemedia-admin-options.tpl.php';
    }

    public function is_available()
    {
        $current_currency = Utils::get_current_currency();

        if (empty($this->get_currency_service_id($current_currency)) || empty($this->get_current_hash_key($current_currency))) {
            return false;
        }

        return $this->enabled == 'yes';
    }

    public function process_admin_options()
    {
        $this->init_settings();
        $post_data = $this->get_post_data();

        if (!empty($post_data['save'])) {
            unset($post_data['save']);
        }

        if (!empty($post_data['_wpnonce'])) {
            unset($post_data['_wpnonce']);
        }

        if (!empty($post_data['_wp_http_referer'])) {
            unset($post_data['_wp_http_referer']);
        }

        $post_data = $this->get_post_data();
        if (!$this->areServiceIdFieldsEmpty($post_data)) {
            $service_id_fields_validator = new ServiceIdFieldValidator();
            $service_id_fields_validator->validate();

            $errors = $service_id_fields_validator->getErrors();
            if (!empty($errors)) {
                update_option('validation_notifications', json_encode($errors));
                foreach ($errors as $error) {
                    unset($post_data[$error[1]]);
                }
            }
        }

        foreach ($post_data as $key => $field) {
            $key = str_replace('woocommerce_bluemedia_payment_gateway_', '', $key);

            if ('title' !== $this->get_field_type($field)) {
                $this->settings[$key] = $field;
            }
        }

        return update_option($this->get_option_key(), apply_filters('woocommerce_settings_api_sanitized_fields_' . $this->id, $this->settings), 'yes');
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

        $gateway_id = empty($_POST['bm_background_payment']) ? '' : $_POST['bm_background_payment'];
        $regulation_id = empty($_POST['bluemedia_channel_regulation_id']) ? '' : $_POST['bluemedia_channel_regulation_id'];

        return [
            'result' => 'success',
            'redirect' => add_query_arg(['order_id' => $order_id, 'gateway_id' => $gateway_id, 'regulation_id' => $regulation_id], $this->url_notify),
        ];
    }

    public function is_bluemedia_background_payment_enable($currency)
    {
        return !empty($this->service_id) &&
            !empty($this->hash_key) &&
            !empty($this->settings["background_payment_$currency"]);
    }

    public function get_currency_service_id($currency)
    {
        return $this->settings["service_id_$currency"];
    }

    public function get_current_hash_key($currency)
    {
        return $this->settings["hash_key_" . $currency];
    }

    public function getNotifyUrl()
    {
        $result = add_query_arg('wc-api', __CLASS__, home_url('/'));
        if (Utils::is_ssl()) {
            $result = str_replace('https:', 'http:', $result);
        }

        return $result;
    }

    public function getBackUrl()
    {
        $result = $this->get_return_url(null);

        if (Utils::is_ssl()) {
            $result = str_replace('https:', 'http:', $result);
        }

        return $result;
    }

    private function get_bm_background_payment_channel_id()
	{
		$bm_channel_id = 0;
        $background_payment_channels = new BackgroundPaymentChannels();

		if ($this->is_bluemedia_background_payment_enable(get_woocommerce_currency())) {
            $background_payment_channels->addPaymentMethod(new BlikPBLPayment());
            $background_payment_channels->addPaymentMethod(new CardPayment());
            $background_payment_channels->addPaymentMethod(new InstallmentPayment());
            $background_payment_channels->addPaymentMethod(new SmartneyPayment());
            $background_payment_channels->addPaymentMethod(new BackgroundPayment());
            $background_payment_channels->addPaymentMethod(new BackgroundSessionPayment());
            $bm_channel_id = $background_payment_channels->handle();
		}

		return $bm_channel_id;
	}

	private function load_currency_settings()
	{
		$actualCurrency = get_woocommerce_currency();
		$currenciesList = array_keys((new CurrencyDictionary())->getAvailableCurrencies());

		// sprawdz, czy aktualna waluta jest obsługiwana przez Blue Media a także czy wprowadzone są ustawiania
        if (in_array($actualCurrency, $currenciesList)
            && !empty($this->settings["service_id_$actualCurrency"])
            && !empty($this->settings["hash_key_$actualCurrency"])
            && !empty($this->settings["hash_key_mode_$actualCurrency"])
        ) {
            $this->service_id = $this->settings["service_id_$actualCurrency"];
            $this->hash_key = $this->settings["hash_key_$actualCurrency"];
            $this->hash_key_mode = $this->settings["hash_key_mode_$actualCurrency"];
            $this->background_payment_channels = !empty($this->settings["backgorund_channels"][$actualCurrency])
                ? $this->settings["backgorund_channels"][$actualCurrency]
                : [];
        } else {
            $this->service_id = isset($this->settings['service_id_' . PaymentEnum::DEFAULT_CURRENCY]) ? $this->settings['service_id_' . PaymentEnum::DEFAULT_CURRENCY] : null;
            $this->hash_key = isset($this->settings['hash_key_' . PaymentEnum::DEFAULT_CURRENCY]) ? $this->settings['hash_key_' . PaymentEnum::DEFAULT_CURRENCY] : null;
            $this->hash_key_mode = isset($this->settings['hash_key_mode_' . PaymentEnum::DEFAULT_CURRENCY]) ? $this->settings['hash_key_mode_' . PaymentEnum::DEFAULT_CURRENCY] : null;
            $this->background_payment_channels = isset($this->settings['backgorund_channels'][PaymentEnum::DEFAULT_CURRENCY]) ? $this->settings['backgorund_channels'][PaymentEnum::DEFAULT_CURRENCY] : null;
        }
	}

    private function get_background_payment_channels($currency = null)
    {
        $regulations_provider = new RegulationsProvider(new BlueMediaSdkHandler(
            $this->settings['payment_domain'],
            $this->get_currency_service_id($currency),
            $this->settings['hash_key_' . get_woocommerce_currency()]
        ));

        if (empty($currency)) {
            return $regulations_provider->getRegulations($this->background_payment_channels);
        }

        $channelsList = empty($this->settings['backgorund_channels'][$currency]) ? [] : $this->settings['backgorund_channels'][$currency];

        if (empty($channelsList)) {
            return [];
        }

        return $regulations_provider->getRegulations($channelsList);
    }

    public function getThankYouPage($orderId)
    {
        try {
            $order = new WC_Order((int)$orderId);
        } catch (Exception $e) {
            $order = null;
        }

        return $this->get_return_url($order);
    }

    /**
     * @see Adres na który jest wysyłany ITN:
     * @see GET ITN https://shop-example.com/?wc-api=wc_payment_gateway_bluemedia
     * @see GET RETURN https://shop-example.com/?wc-api=wc_payment_gateway_bluemedia&thank_you_page=1&
     * @see PERMALINK https://shop-example.com/wc-api/wc_payment_gateway_bluemedia - nie zawsze działa
     * @see gdy chcemy PERMALINK trzeba przestawić w panelu "/wp-admin/options-permalink.php" na coś innego niż "Plain/Prosty"
    */
    public function gateway_process()
    {
        if (!empty($_GET['thank_you_page']) && !empty($_GET['OrderID'])) {
            $thankYouPageLocation = $this->getThankYouPage($_GET['OrderID']);
            wp_redirect($thankYouPageLocation);
            exit;
        }

        if (!empty($_GET['gateway_id'])) {
            $_POST['bm_background_payment'] = $_GET['gateway_id'];
        }

        if (!empty($_GET['regulation_id'])) {
            $_POST['bluemedia_channel_regulation_id'] = $_GET['regulation_id'];
        }

        $this->logger->log('[BM Bluepayment] POST Request:' . json_encode($_POST));
        $isTransaction = isset($_POST['transactions']);
        $isCheckout = false;
        $orderId = 0;

        if (isset($_GET['order_id'])) {
            $isCheckout = true;
            $orderId = $_GET['order_id'];
        } elseif (isset($_GET['OrderID'])) {
            $isCheckout = true;
            $orderId = $_GET['OrderID'];
        }

        $this->logger->log('[BM Bluepayment] OrderID Request:' . $orderId);

        if ($isTransaction) {
            $this->gateway_process_response();
        } elseif ($isCheckout) {
            $this->logger->log('[BM Bluepayment] BackURL Request');
            $this->gateway_process_send_payment($orderId);
        } else {
            $this->logger->log('[BM Bluepayment] Empty Request');
            header('HTTP/1.1 404 Not Found');
            exit('404 Not Found');
        }

        exit();
    }

    private function gateway_process_response()
	{
        $this->logger->log('[BM Bluepayment] ITN Message XML: '.$this->getItnInXml());

        try {
            $transaction = Gateway::doItnIn();
        } catch (Exception $e) {
            $logHash = $this->logger->log('[BM Bluepayment] ITN Exception: '.(string) $e);
            status_header(WP_Http::BAD_REQUEST);
            echo sprintf('%s %s', $logHash, 'Blue Media Payment Error');
            exit;
        }

        $serviceId = $transaction->getServiceId();
        $serviceIdKey = array_search($serviceId, $this->settings);
        $serviceIdCurrency = str_replace('service_id_', '', $serviceIdKey);

        $serviceHashKey = 'hash_key_' . $serviceIdCurrency;
        $this->hash_key = empty($this->settings[$serviceHashKey]) ? 0 : $this->settings[$serviceHashKey];

        if (empty($this->hash_key)) {
            $serviceHashKey = 'hash_key_' . $transaction->getCurrency();
            $this->hash_key = empty($this->settings[$serviceHashKey]) ? 0 : $this->settings[$serviceHashKey];
        }

        $gateway = new Gateway(
            $serviceId,
            $this->hash_key,
            $this->bluepayment_settings->getGatewayMode()
        );

        $isDataConsistent = true;
        $orderId = $transaction->getOrderId();

        if ($transaction->getGatewayId() === GatewayModel::GATEWAY_ID_BLIK) {
            $this->handleBlikItnStatus($orderId, $transaction);
        }

        try {
            $orderInfo = new WC_Order($orderId);
            $this->service_id = $transaction->getServiceId();

            $sourceData = (new BMTransactionBuilder())->build($orderInfo, $serviceId);

            if (!empty($sourceData->getServiceId()) && $sourceData->getServiceId() != $transaction->getServiceId()) {
                $this->logger->log(
                    'ServiceID pobrany z zamówienia: '. $sourceData->getServiceId().
                    ' nie jest równy ServiceID pobranemu ze zbudowanego ITN: '. $transaction->getServiceId()
                );
                $isDataConsistent = false;
            }

            if (!empty($sourceData->getAmount()) && $sourceData->getAmount() != $transaction->getAmount()) {
                $this->logger->log(
                        'Kwota zamówienia pobrana z zamówienia: '. $sourceData->getAmount().
                        ' nie jest równa kwocie ze zbudowanego ITN: '. $transaction->getAmount()
                );
                $isDataConsistent = false;
            }

            if ($isDataConsistent && $transaction->getPaymentStatus() === ItnIn::PAYMENT_STATUS_SUCCESS) {
                $orderInfo->payment_complete();
            }
            elseif ($isDataConsistent && $transaction->getPaymentStatus() === ItnIn::PAYMENT_STATUS_FAILURE) {
                if (!$orderInfo->is_paid()) {
                    $orderInfo->update_status('failed', __("Payment failed", 'bluepayment-gateway-for-woocommerce'));
                }
            }
            elseif ($isDataConsistent && $transaction->getPaymentStatus() === ItnIn::PAYMENT_STATUS_PENDING) {
                if (!empty($this->settings['status_pending']) && $this->settings['status_pending'] === 'on-hold') {
                    $orderInfo->update_status('on-hold', __("Awaiting payment", 'bluepayment-gateway-for-woocommerce'));
                } else {
                    $orderInfo->update_status('pending', __("Order received", 'bluepayment-gateway-for-woocommerce'));
                }
            }
        } catch (Exception $e) {
            $logHash = $this->logger->log('[BM Bluepayment] ITN Exception: '.(string) $e);
            status_header(WP_Http::INTERNAL_SERVER_ERROR);
            echo sprintf('%s %s', $logHash, 'Blue Media Payment Error');
            exit;
        }

        $response = $gateway->doItnInResponse($transaction, $isDataConsistent);
        $this->logger->log('[BM Bluepayment] Response to ITN XML: '.$response);

        echo $response;
	}

    private function gateway_process_send_payment($orderId)
	{
		if (!empty($_GET) && isset($_GET['order-received']) && $_GET['order-received'] === '') {
			wp_redirect($this->backUrl);
			exit;
		}

        $this->logger->log('[BM Bluepayment] Start gateway_process_send_payment');

		$orderInfo = new WC_Order($orderId);
        if (!empty($this->settings['status_pending']) && $this->settings['status_pending'] == 'on-hold') {
            $orderInfo->update_status('on-hold', __("Awaiting payment", 'bluepayment-gateway-for-woocommerce'));
        } else {
            $orderInfo->update_status('pending', __("Order received", 'bluepayment-gateway-for-woocommerce'));
        }

        $this->logger->log('[BM Bluepayment] Updated gateway_process_send_payment');
		$is_installment_payment = WC()->session->get('chosen_payment_method') == 'bluemedia_payment_gateway_installment';
        $actual_currency = get_woocommerce_currency();

        if ($this->is_bluemedia_background_payment_enable($actual_currency) || $is_installment_payment) {
            if ($is_installment_payment) {
                $this->bm_background_payment_channel_id = GatewayModel::GATEWAY_ID_IFRAME;
            }
        }

        $gatewayId = empty($this->bm_background_payment_channel_id) ? 0 : $this->bm_background_payment_channel_id;
        if (empty($gatewayId) && !empty($_POST['bm_background_payment'])) {
            $gatewayId = $_POST['bm_background_payment'];
        }

        if ($this->bluepayment_settings->isTestDomain() && isset($_GET['blik_mode_pbl'])) {
            $gatewayId = GatewayModel::GATEWAY_ID_PG_TEST;
        }

        $gateway = new Gateway(
            $this->service_id,
            $this->hash_key,
            $this->settings['payment_domain'] === Gateway::PAYMENT_DOMAIN_LIVE ? Gateway::MODE_LIVE : Gateway::MODE_SANDBOX,
            empty($this->hash_key_mode) ? Gateway::HASH_SHA256 : $this->hash_key_mode
        );

        $transaction_data = (new BMTransactionBuilder())->build($orderInfo, $this->service_id, $gatewayId);

        $this->logger->log('[BM Bluepayment] Data gateway_process_send_payment:' . json_encode($transaction_data->toArray()));

        echo $gateway->doTransactionStandard($transaction_data);
	}

    private function handleBlikItnStatus($orderId, ItnIn $transaction)
    {
        global $wpdb;

        $blikTransactionData = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT status, date_start FROM {$wpdb->prefix}bluemedia_blik WHERE hash_cart = %d AND status = 0",
                $orderId
            )
        );

        $blikTransactionData = empty($blikTransactionData) ? [] : reset($blikTransactionData);

        $status = null;
        if ($transaction->getPaymentStatus() !== ItnIn::PAYMENT_STATUS_SUCCESS) {
            if ($transaction->getPaymentStatus() === ItnIn::PAYMENT_STATUS_FAILURE) {
                $status = BlikEnum::BLIK_STATUS_FAILURE;
            }
            // jeżeli jest timeout, robię update statusu na 4
            else if ($transaction->getPaymentStatus() === ItnIn::PAYMENT_STATUS_PENDING
                && isset($blikTransactionData->date_start)
                && time() >= strtotime('+2 minutes', strtotime($blikTransactionData->date_start))
            ) {
                $status = BlikEnum::BLIK_STATUS_EXPIRED;
            }
        } elseif (isset($blikTransactionData->status) && $blikTransactionData->status == 0) {
            $status = BlikEnum::BLIK_STATUS_SUCCESS;
        }

        if ($status !== null) {
            $wpdb->update(
                $wpdb->prefix . 'bluemedia_blik',
                ['status' => $status],
                ['hash_cart' => $orderId, 'status' => BlikEnum::BLIK_STATUS_PENDING]
            );
        }
    }

    /**
     * @return mixed|string
    */
    private function getItnInXml()
    {
        $xml = Gateway::getItnInXml();
        if ($xml instanceof SimpleXMLElement) {
            $xml = $xml->asXML();
        }
        return $xml;
    }

    private function loadSettings()
    {
        $this->init_settings();
        $this->bluepayment_settings = new BluePaymentSettings($this->settings);
    }

    private function areServiceIdFieldsEmpty($post_data)
    {
        foreach ((new CurrencyDictionary())->getAvailableCurrencies() as $currency) {
            if (!$this->isServiceIdFieldEmpty($post_data, $currency)) {
                return false;
            }
        }
        return true;
    }

    private function isServiceIdFieldEmpty($post_data, $currency)
    {
        return empty($post_data['woocommerce_bluemedia_payment_gateway_service_id_' . $currency]);
    }
}
