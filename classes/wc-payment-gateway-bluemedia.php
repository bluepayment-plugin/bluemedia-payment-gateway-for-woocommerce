<?php

/**
 * System płatności online Blue Media.
 *
 * @author    Piotr Żuralski <piotr@zuralski.net>
 * @copyright 2015 Blue Media
 * @license   http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 * @since     2015-02-28
 * @version   v1.2.1
 */

require_once dirname(__FILE__).'/wc-payment-gateway.php';


class WC_Payment_Gateway_BlueMedia extends WC_Payment_Gateway
{
    const MODE_SANDBOX = 'sandbox';
    const MODE_LIVE = 'live';

    const PAYMENT_DOMAIN_SANDBOX = 'pay-accept.bm.pl';
    const PAYMENT_DOMAIN_LIVE = 'pay.bm.pl';

    const PAYMENT_ACTON_SECURE = '/secure?';
    const PAYMENT_ACTON_PAYMENT = '/payment?';
    const PAYMENT_ACTON_CANCEL = '/transactionCancel?';
    const PAYMENT_ACTON_START_TRAN = '/startTran';

    const PAYMENT_STATUS_PENDING = 'PENDING';
    const PAYMENT_STATUS_SUCCESS = 'SUCCESS';
    const PAYMENT_STATUS_FAILURE = 'FAILURE';

    const STATUS_CONFIRMED = 'CONFIRMED';
    const STATUS_NOT_CONFIRMED = 'NOTCONFIRMED';

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->id = 'bluemedia_payment_gateway';
        $this->method_title = __('System płatności online Blue Media', 'bluemedia-payment-gateway-for-woocommerce');
        $this->method_description = '';
        $this->has_fields = true;
        
        $this->notifyUrl = $this->getNotifyUrl();
        $this->backUrl   = $this->getBackUrl();

        // Load the form fields
        $this->init_form_fields();

        // Load the settings.
        $this->init_settings();

        // Get setting values
        $this->title = $this->settings['title'];
        $this->description = $this->get_html();

        $this->enabled = $this->settings['enabled'];
        $this->mode = $this->settings['mode'];

        $this->url_notify = $this->settings['url_notify'] = $this->notifyUrl;

        $this->url_back = $this->settings['url_back'] = $this->backUrl;


        $this->service_id = $this->settings['service_id'];
        $this->hash_key = $this->settings['hash_key'];

        // Actions
        add_action('woocommerce_api_wc_payment_gateway_bluemedia', array($this, 'gateway_process'), 12);

        //Save settings
        add_action('woocommerce_update_options_payment_gateways', array($this, 'process_admin_options'));
        add_action('woocommerce_update_options_payment_gateways_'.$this->id, array($this, 'process_admin_options'));
    }

    /**
     * Maps payment mode to service payment domain.
     *
     * @param string $mode
     *
     * @return string
     */
    final public function mapModeToDomain($mode)
    {
        switch ($mode) {
            case self::MODE_LIVE:
                return self::PAYMENT_DOMAIN_LIVE;
                break;

            default:
                return self::PAYMENT_DOMAIN_SANDBOX;
                break;
        }
    }

    /**
     * Maps payment mode to service payment URL.
     *
     * @param string $mode
     *
     * @return string
     */
    final public function mapModeToUrl($mode)
    {
        $domain = $this->mapModeToDomain($mode);

        return sprintf('https://%s', $domain);
    }

    /**
     * Returns payment service action URL.
     *
     * @param string $mode
     * @param string $action
     *
     * @return string
     */
    final public function getActionUrl($mode, $action)
    {
        $domain = $this->mapModeToDomain($mode);

        switch ($action) {
            case self::PAYMENT_ACTON_CANCEL:
            case self::PAYMENT_ACTON_PAYMENT:
            case self::PAYMENT_ACTON_SECURE:
            case self::PAYMENT_ACTON_START_TRAN:
                break;

            /* if any other value */
            default:
                $action = self::PAYMENT_ACTON_SECURE;
                break;
        }

        return sprintf('https://%s%s', $domain, $action);
    }

    /**
     * Returns notify URL.
     *
     * @return string
     */
    final public function getNotifyUrl()
    {
        $result = add_query_arg('wc-api', __CLASS__, home_url('/'));
        if ($this->is_ssl()) {
            $result = str_replace('https:', 'http:', $result);
        }

        return $result;
    }

    /**
     * Returns back URL.
     *
     * @return string
     */
    final public function getBackUrl()
    {
        $result = $this->getNotifyUrl() . (parse_url($this->getNotifyUrl(), PHP_URL_QUERY) ? '&' : '?') . 'order-received';
        
        return $result;
    }

    /**
     * Generates hash.
     *
     * @param string $hashKey
     * @param array  $formData
     *
     * @return array
     */
    final public function generateHash($hashKey, array &$formData)
    {
        $result = '';
        foreach ($formData as $name => $value) {
            if (mb_strtolower($name) == 'hash') {
                continue;
            }
            $result .= $value.'|';
        }
        $formData['Hash'] = hash('sha256', $result.$hashKey);

        return $formData;
    }

    final public function readNotifyRequest($transactionXml)
    {
        $data = array();
        $xmlReader = new XMLReader();
        $xmlReader->XML($transactionXml, 'UTF-8', (LIBXML_NOERROR | LIBXML_NOWARNING));
        while ($xmlReader->read()) {
            switch ($xmlReader->nodeType) {
                case XMLREADER::ELEMENT:
                    $nodeName = ucfirst($xmlReader->name);
                    $xmlReader->read();
                    $nodeValue = trim($xmlReader->value);
                    if (!empty($nodeName) && !empty($nodeValue)) {
                        $data[$nodeName] = $nodeValue;
                    }
                    break;
            }
        }
        $xmlReader->close();

        return $data;
    }

    final public function buildTransactionData(WC_Order $orderInfo, $gateway_id=null)
    {
        $result = array(
            'ServiceID' => $this->service_id,
            'OrderID' => ((!empty($orderInfo->id)) ? $orderInfo->id : 0),
            'Amount' => ((!empty($orderInfo->order_total)) ? number_format($orderInfo->order_total, 2, '.', '') : 0),
        );

        if ($gateway_id){
            $result['GatewayID'] = $gateway_id;
        }

        $result['CustomerEmail'] = ((!empty($orderInfo->billing_email)) ? $orderInfo->billing_email : '');

        self::generateHash($this->hash_key, $result);

        return $result;
    }

    final public function returnNotifyStatus(array $data)
    {
        $xml = new XMLWriter();
        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('confirmationList');
        $xml->writeElement('serviceID', $data['ServiceID']);
        $xml->startElement('transactionsConfirmations');
        $xml->startElement('transactionConfirmed');
        $xml->writeElement('orderID', $data['OrderID']);
        $xml->writeElement('confirmation', $data['Status']);
        $xml->endElement();
        $xml->endElement();
        $xml->writeElement('hash', $data['Hash']);
        $xml->endElement();

        return $xml->outputMemory();
    }

    /**
     * get_icon function.
     *
     * @return string
     */
    public function get_icon()
    {
        $image_path = plugin_basename(dirname(dirname(__FILE__))).'/assets/images/bluemedia.png';
        $icon = sprintf(
            '<img src="%s" alt="%s" />',
            WP_PLUGIN_URL.'/'.$image_path,
            __('Zapłać poprzez System płatności online Blue Media', 'bluemedia-payment-gateway-for-woocommerce')
        );

        return apply_filters('woocommerce_gateway_icon', $icon, $this->id);
    }

    /**
     * Override this method so this gateway does not appear on checkout page.
     *
     * @since 1.2.1
     */
    public function admin_options()
    {
        return require_once dirname(__FILE__).'/../template/bluemedia-admin-options.tpl.php';
    }

    public function is_available()
    {
        return $this->enabled == 'yes';
    }

    /**
     * Use WooCommerce logger.
     */
    protected function add_log($message)
    {
        if (empty($this->log)) {
            $this->log = new WC_Logger();
        }
        $this->log->add($this->id, $message);
    }

    /**
     * Check if site is SSL ready.
     */
    public static function is_ssl()
    {
        if (is_ssl() || get_option('woocommerce_force_ssl_checkout') == 'yes' || class_exists('WordPressHTTPS')) {
            return true;
        }

        return false;
    }

    /**
     * Initialize Gateway Settings Form Fields.
     */
    public function init_form_fields()
    {
        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Włącz/Wyłącz', 'bluemedia-payment-gateway-for-woocommerce'),
                'label' => __('Włącz System płatności online Blue Media', 'bluemedia-payment-gateway-for-woocommerce'),
                'type' => 'select',
                'description' => '',
                'options' => array(
                    'no' => __('wyłączony', 'bluemedia-payment-gateway-for-woocommerce'),
                    'yes' => __('włączony', 'bluemedia-payment-gateway-for-woocommerce'),
                ),
            ),
            'title' => array(
                'title' => __('Tytuł', 'bluemedia-payment-gateway-for-woocommerce'),
                'type' => 'text',
                'description' => __('Tytuł, który widzi użytkownik.', 'bluemedia-payment-gateway-for-woocommerce'),
                'default' => __('System płatności online Blue Media', 'bluemedia-payment-gateway-for-woocommerce'),
            ),
            'description' => array(
                'title' => __('Opis', 'bluemedia-payment-gateway-for-woocommerce'),
                'type' => 'textarea',
                'description' => 'Opis bramki, który widzi użytkownik przy tworzeniu zamówienia',
                'default' => __('Zapłać poprzez System płatności online Blue Media: płatność kartą płatniczą, przelewem bankowym on-line lub szybkim przelewem bankowym', 'bluemedia-payment-gateway-for-woocommerce'),
            ),
            'mode' => array(
                'title' => __('Tryb działania', 'bluemedia-payment-gateway-for-woocommerce'),
                'type' => 'select',
                'description' => 'Przełączanie się między środowiskiem "Systemu płatności online Blue Media", jeśli testy zakończyły się, należy ustawić wartość "produkcyjny"',
                'options' => array(
                    self::MODE_SANDBOX => __('testowy', 'bluemedia-payment-gateway-for-woocommerce'),
                    self::MODE_LIVE => __('produkcyjny', 'bluemedia-payment-gateway-for-woocommerce'),
                ),
            ),
            'payment_domain' => array(
                'title' => __('Domena Systemu płatności online Blue Media', 'bluemedia-payment-gateway-for-woocommerce'),
                'type' => 'text',
                'description' => 'wartość tylko do podglądu, wartość w tym polu nie jest możliwa do zmiany',
                'css' => 'width: 100%',
                'default' => '',
                'custom_attributes' => array(
                    'readonly' => 'readonly',
                ),
            ),
            'url_notify' => array(
                'title' => __('Adres do powiadomień (ITN URL)', 'bluemedia-payment-gateway-for-woocommerce'),
                'type' => 'text',
                'description' => 'wartość tylko do podglądu, wartość w tym polu nie jest możliwa do zmiany - adres ten należy przekazać "Blue Media"',
                'css' => 'width: 100%',
                'default' => $this->notifyUrl,
                'custom_attributes' => array(
                    'readonly' => 'readonly',
                ),
            ),
            'url_back' => array(
                'title' => __('Adres powrotny (Back URL)', 'bluemedia-payment-gateway-for-woocommerce'),
                'type' => 'text',
                'css' => 'width: 100%',
                'description' => 'wartość tylko do podglądu, wartość w tym polu nie jest możliwa do zmiany - adres ten należy przekazać "Blue Media"',
                'default' => $this->backUrl,
                'custom_attributes' => array(
                    'readonly' => 'readonly',
                ),
            ),
            'service_id' => array(
                'title' => __('ServiceID', 'bluemedia-payment-gateway-for-woocommerce'),
                'type' => 'text',
                'description' => 'pole obowiązkowe, ServiceID (ID usługi) otrzymane od "Blue Media"',
                'default' => '',
                'custom_attributes' => array(
                    'required' => 'required',
                ),
            ),
            'hash_key' => array(
                'title' => __('Hash key', 'bluemedia-payment-gateway-for-woocommerce'),
                'type' => 'text',
                'description' => 'pole obowiązkowe, klucz do hashowania danych otrzymane od "Blue Media"',
                'default' => '',
                'custom_attributes' => array(
                    'required' => 'required',
                ),
            ),
            'enabled_gateway' => array(
                'title' => __('Wybór kanałów płatności', 'bluemedia-payment-gateway-for-woocommerce'),
                'label' => __('Wybór kanałów płatności', 'bluemedia-payment-gateway-for-woocommerce'),
                'type' => 'select',
                'description' => '',
                'options' => array(
                    'no' => __('wyłączony', 'bluemedia-payment-gateway-for-woocommerce'),
                    'yes' => __('włączony', 'bluemedia-payment-gateway-for-woocommerce'),
                ),
            ),
        );
    }

    public function gateway_process()
    {
        $isTransaction = (isset($_POST['transactions']));
        $isBackUrl = (isset($_GET['order-received']));

        $isCheckout    = false;
        $orderId       = 0;

        if (isset($_GET['order_id'])) {
            $isCheckout    = true;
            $orderId       = $_GET['order_id'];
            $gateway_id    = isset($_GET['gateway_id']) ? $_GET['gateway_id'] : null;
        } elseif (isset($_GET['OrderID'])) {
            $isCheckout    = true;
            $orderId       = $_GET['OrderID'];
            $gateway_id    = null;
        }
        if ($isBackUrl){
            $order = new WC_Order($orderId);
            wp_redirect($this->get_return_url($order));
        }
        elseif ($isTransaction) {
            $this->add_log('ITN');
            $this->gateway_process_response();
        } elseif ($isCheckout) {
            $this->add_log('backURL');
            $this->gateway_process_send_payment($orderId, $gateway_id);
        } else {
            $this->add_log('empty request');
            header('HTTP/1.1 404 Not Found');
            exit('404 Not Found');
        }
        exit();
    }

    protected function gateway_process_response()
    {
        $transactions = $_POST['transactions'];
        $transactionXml = base64_decode($transactions);
        $log = '';

        $transactionHashed = $transaction = array();
        if (!empty($transactionXml)) {
            $transactionHashed = $transaction = self::readNotifyRequest($transactionXml);
            self::generateHash($this->hash_key, $transactionHashed);
        }
        $orderId = ((!empty($transaction['OrderID'])) ? $transaction['OrderID'] : 0);
        $orderInfo = new WC_Order($orderId);

        $sourceData = $this->buildTransactionData($orderInfo);
        $isDataConsistent = true;

        $log .= sprintf('Got from BlueMedia XML: "%s"', $transactionXml).PHP_EOL;

        if (empty($sourceData['ServiceID']) || empty($transaction['ServiceID'])) {
            $log .= 'Got from BlueMedia blank ServiceID'.PHP_EOL;
            $isDataConsistent = false;
        }
        if (!empty($sourceData['ServiceID']) && !empty($transaction['ServiceID'])
            && ($sourceData['ServiceID'] != $transaction['ServiceID'])
        ) {
            $log .= sprintf('Got from BlueMedia wrong ServiceID BlueMedia="%d", mine="%d"', $transaction['ServiceID'], $sourceData['ServiceID']).PHP_EOL;
            $isDataConsistent = false;
        }
        if (empty($sourceData['Amount']) || empty($transaction['Amount'])) {
            $log .= 'Got from BlueMedia blank Amount'.PHP_EOL;
            $isDataConsistent = false;
        }
        if (!empty($sourceData['Amount']) && !empty($transaction['Amount'])
            && ($sourceData['Amount'] != $transaction['Amount'])
        ) {
            $log .= sprintf('Got from BlueMedia wrong Amount BlueMedia="%d", mine="%d"', $transaction['Amount'], $sourceData['Amount']).PHP_EOL;
            $isDataConsistent = false;
        }
        if (empty($transactionHashed['Hash']) || empty($transaction['Hash'])) {
            $log .= 'Got from BlueMedia blank Hash'.PHP_EOL;
            $isDataConsistent = false;
        }
        if (!empty($transactionHashed['Hash']) && !empty($transaction['Hash'])
            && ($transactionHashed['Hash'] != $transaction['Hash'])
        ) {
            $log .= sprintf('Got from BlueMedia wrong Hash BlueMedia="%s", mine="%s"', $transactionHashed['Hash'], $transaction['Hash']).PHP_EOL;
            $isDataConsistent = false;
        }

        if ($isDataConsistent && $transaction['PaymentStatus'] == self::PAYMENT_STATUS_SUCCESS) {
            $log .= sprintf('Payment: marked as SUCCESS, previous status="%s"', $orderInfo->get_status());
            $orderInfo->payment_complete();
            $log .= sprintf(', payment current status="%s"', $orderInfo->get_status()).PHP_EOL;

        } elseif ($isDataConsistent && $transaction['PaymentStatus'] == self::PAYMENT_STATUS_FAILURE) {
            $log .= sprintf('Payment: marked as FAILURE, previous status="%s"', $orderInfo->get_status());
            $orderInfo->update_status('failed', __('Payment failed', 'bluemedia-payment-gateway-for-woocommerce'));

            $log .= sprintf(', payment current status="%s"', $orderInfo->get_status()).PHP_EOL;
        } elseif ($isDataConsistent && $transaction['PaymentStatus'] == self::PAYMENT_STATUS_PENDING) {
            $log .= sprintf('Payment status is PENDING').PHP_EOL;
            $orderInfo->update_status('pending', __('Awaiting payment', 'bluemedia-payment-gateway-for-woocommerce'));
        } else {
            $log .= sprintf('Payment status not changed isDataConsistent: "%d", PaymentStatus: "%s"', $isDataConsistent, $transaction['PaymentStatus']).PHP_EOL;
        }

        $returnData = array(
            'ServiceID' => $sourceData['ServiceID'],
            'OrderID' => $sourceData['OrderID'],
            'Status' => (($isDataConsistent) ? self::STATUS_CONFIRMED : self::STATUS_NOT_CONFIRMED),
        );
        $log .= sprintf('RESPONSE: "%s"', var_export($returnData, true)).PHP_EOL;
        $this->add_log($log);

        self::generateHash($this->hash_key, $returnData);

        header('Content-Type: application/xml; charset: UTF-8');
        echo self::returnNotifyStatus($returnData);
    }

    protected function gateway_process_send_payment($orderId, $gateway_id=null)
    {

        $orderInfo = new WC_Order($orderId);
        $orderInfo->update_status('pending', __('Awaiting payment', 'bluemedia-payment-gateway-for-woocommerce'));

        wp_redirect(self::getActionUrl($this->mode, self::PAYMENT_ACTON_PAYMENT) .
            http_build_query($this->buildTransactionData($orderInfo, $gateway_id)));
    }

    /**
     * Process a refund if supported.
     *
     * @param int    $order_id
     * @param float  $amount
     * @param string $reason
     *
     * @return bool|wp_error True or false based on success, or a WP_Error object
     */
    public function process_refund($order_id, $amount = null, $reason = '')
    {
        return false;
    }

    public function validate_fields()
    {
        $currency = get_woocommerce_currency();
        if ($currency == 'PLN') {
            return true;
        } else {
            echo __('Sorry, this payment gateway supports payment only in PLN.', 'bluemedia-payment-gateway-for-woocommerce');
            exit;
        }
    }

    /**
     * Processes payment for the order - see WC_Payment_Gateway.
     *
     * @param $order_id ; id of an order
     *
     * @return array
     */
    public function process_payment($order_id)
    {
        global $woocommerce;

        // Post data and redirect
        if ($this->settings['enabled_gateway'] == 'yes' && empty($_POST['payment_method_bluemedia_payment_gateway_id'])){
            wc_add_notice( __('Błąd: ', 'woothemes') . 'Proszę wybrać kanał płatności', 'error' );
            return;
        }
        $order = new WC_Order($order_id);
        $order->update_status('pending', __('Awaiting payment', 'bluemedia-payment-gateway-for-woocommerce'));
        // Clear cart
        $woocommerce->cart->empty_cart();

        if (isset($_POST['payment_method_bluemedia_payment_gateway_id'])){
            return array(
                'result' => 'success',
                'redirect' => add_query_arg(array('order_id' => $order_id,
                    'gateway_id' => $_POST['payment_method_bluemedia_payment_gateway_id']), $this->url_notify),
            );
        } else {
            return array(
                'result' => 'success',
                'redirect' => add_query_arg(array('order_id' => $order_id), $this->url_notify),
            );
        }
    }

    function get_html(){
        $class = new WC_Bluepayment_gateway($this->settings);
        $gateways = $class->getSimpleGatewaysList();

        ob_start();
        include dirname(__FILE__).'/../template/bluemedia-send-payment.tpl.php';
        return ob_get_clean();
    }
}
