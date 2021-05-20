<?php

/**
 * @wordpress-plugin
 *
 * System płatności online Blue Media
 *
 * @author    Blue Media https://bluemedia.pl/?utm_source=woocommerce_backend
 * @copyright Blue Media S.A.
 * @license   http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 * @see       https://bluemedia.pl/oferta/platnosci/platnosci-online/wtyczki
 * @version   v2.2.2
 *
 * Plugin Name:           Blue Media online payment system for WooCommerce
 * Description:           Easily enable Blue Media Payment Gateway with WooCommerce
 * Author:                Blue Media
 * Author URI:            https://bluemedia.pl/?utm_source=woocommerce_backend
 * License:               GNU General Public License, version 3 (GPL-3.0)
 * License URI:           http://opensource.org/licenses/GPL-3.0
 * Domain Path:           /i18n/languages/
 * Text Domain:           bluepayment-gateway-for-woocommerce
 * Version:               2.2.2
 * WC tested up to:       4.0
 * WC requires at least:  2.1
 * Tested up to:          5.4
*/

use BlueMedia\OnlinePayments\Gateway;

if (!defined('ABSPATH')) {
    exit();
}

global $woocommerce, $blue_media_settings, $wp_version;
$blue_media_settings = get_option('woocommerce_bluemedia_payment_gateway_settings');

$dir_name = dirname(__FILE__);

$files = [
    'bluemedia-sdk-php/index.php',
    'dictionary/CurrencyDictionary.php',
    'dictionary/OrderStatusMessageDictionary.php',
    'enum/PaymentEnum.php',
    'enum/BlikEnum.php',
    'enum/CurrencyEnum.php',
    'service/Logger.php',
    'service/ListPaymentGateways/PaymentGateways.php',
    'service/BackgroundPaymentChannels/BackgroundPaymentChannels.php',
    'utils/BackendMenu.php',
    'utils/Utils.php',
    'utils/CustomGatewayOrderSorter.php',
    'utils/DefaultGatewayOrderSorter.php',
    'validator/ValidatorInterface.php',
    'validator/InstallmentPaymentAmountValidator.php',
    'validator/ForeignCurrencyAmountValidator.php',
    'validator/BlikFieldCodeValidator.php',
    'validator/BlikTransactionValidator.php',
    'validator/GpayPopupValidator.php',
    'validator/admin/ServiceIdFieldValidator.php',
    'handler/BlueMediaSdkHandler.php',
    'provider/PaywayListProvider.php',
    'provider/RegulationsProvider.php',
    'builder/BlikTransactionBuilder.php',
    'builder/BMTransactionBuilder.php',
    '../woocommerce/includes/abstracts/abstract-wc-settings-api.php',
    '../woocommerce/includes/abstracts/abstract-wc-payment-gateway.php',
    'classes/wc-payment-gateway-bluemedia.php',
    'classes/wc-payment-gateway-bluemedia-blik-pbl.php',
    'classes/wc-payment-gateway-bluemedia-blik-zero.php',
    'classes/wc-payment-gateway-bluemedia-gpay-popup.php',
    'classes/wc-payment-gateway-bluemedia-smartney-popup.php',
    'classes/wc-payment-gateway-bluemedia-installment.php',
    'classes/wc-payment-gateway-bluemedia-card.php',
    'ValueObject/BluePaymentSettings.php'
];

foreach ($files as $file) {
    require_once $dir_name . DIRECTORY_SEPARATOR . $file;
}

if (!class_exists('BlueMedia_Payment_Gateway')) {
    /**
     * @copyright Blue Media S.A.
     */
    class BlueMedia_Payment_Gateway
    {
        public function __construct()
        {
            $woo_version = $this->get_woo_version_number();

            if (version_compare($woo_version, '2.1', '<')) {
                exit(__("Blue Media Online Payment System for WooCommerce requires WooCommerce version 2.1 or higher. Please backup your site files and database, update WooCommerce, and try again.", 'bluepayment-gateway-for-woocommerce'));
            }

            if (!extension_loaded('xmlwriter') || !class_exists('XMLWriter')) {
                exit(__("This plugin requires <a href=\"http://php.net/manual/en/book.xmlwriter.php\">XMLWriter</a>", 'bluepayment-gateway-for-woocommerce'));
            }

            if (!extension_loaded('xmlreader') || !class_exists('XMLReader')) {
                exit(__("This plugin requires <a href=\"http://php.net/manual/en/book.xmlreader.php\">XMLReader</a>", 'bluepayment-gateway-for-woocommerce'));
            }

            add_action('plugins_loaded', [$this, 'init']);
            register_activation_hook(__FILE__, [$this, 'activate']);
            add_action('admin_notices', [$this, 'admin_notices']);
            add_action('admin_init', [$this, 'set_ignore_tag']);
            add_filter('woocommerce_product_title', [$this, 'woocommerce_product_title']);

            // http://stackoverflow.com/questions/22577727/problems-adding-action-links-to-wordpress-plugin
            $basename = plugin_basename(__FILE__);
            $prefix = is_network_admin() ? 'network_admin_' : '';
            add_filter($prefix . 'plugin_action_links_' . $basename, [$this, 'plugin_action_links'], 10, 4);
            add_action('admin_enqueue_scripts', [$this, 'add_admin_scripts']);
            add_action('wp_enqueue_scripts', [$this, 'add_gateway_scripts']);

            // Background Payments
            add_action('wp_ajax_bluemedia_edit_background_payments_channels', [$this, 'bluemedia_edit_background_payments_channels']);

            // Installment - validate minimum amount
            if (!empty($_POST['payment_method']) && $_POST['payment_method'] == 'bluemedia_payment_gateway_installment') {
                add_action('woocommerce_checkout_process', function () {
                    (new InstallmentPaymentAmountValidator(100))->validate();
                });
            }

            add_action('woocommerce_checkout_process', [$this, 'blikOrderHandler']);
            add_action('wc_ajax_bluemedia_blik_validate_code', [BlikTransactionValidator::class, 'validate'], 10, 1);
            if (isset($_POST['payment_method']) && $_POST['payment_method'] == 'bluemedia_payment_gateway_blik') {
                add_action('woocommerce_after_checkout_validation', [BlikFieldCodeValidator::class, 'validate']);
                add_action('woocommerce_checkout_order_processed', [$this, 'bluemedia_blik_transaction_processed']);
            }

            if (isset($_POST['payment_method']) && $_POST['payment_method'] == 'bluemedia_payment_gateway_gpay_popup') {
                add_action('woocommerce_after_checkout_validation', [GpayPopupValidator::class, 'validate']);
            }

            add_action('before_woocommerce_pay', [$this, 'clearUnfinishedCheckoutPage']);
            add_action('woocommerce_pay_order_before_submit', [$this, 'setOrderSession']);
            add_filter('woocommerce_add_to_cart_product_id', [$this, 'cleanOrderedCart']);
            add_filter('woocommerce_thankyou_order_received_text', [$this, 'woo_change_order_received_text']);
            add_action('woocommerce_thankyou', [$this, 'woo_display_order_status_message'], 1);
        }

        /**
         * Get WooCommerce Version Number
         * http://wpbackoffice.com/get-current-woocommerce-version-number/.
         */
        private function get_woo_version_number()
        {
            // If get_plugins() isn't available, require it
            if (!function_exists('get_plugins')) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            // Create the plugins folder and file variables
            $plugin_folder = get_plugins('/' . 'woocommerce');
            $plugin_file = 'woocommerce.php';

            // If the plugin version number is set, return it
            if (isset($plugin_folder[$plugin_file]['Version'])) {
                return $plugin_folder[$plugin_file]['Version'];
            } else {
                // Otherwise return null
                return;
            }
        }

        public function add_admin_scripts()
        {
            wp_enqueue_media();
            wp_enqueue_script('jquery');
        }

        public function add_gateway_scripts()
        {
            $blik_path = plugins_url('/assets/js/bluemedia-blik.js', __FILE__);
            $gpay_path = plugins_url('/assets/js/bluemedia-gpay-popup.js', __FILE__);

            wp_enqueue_script('blik', $blik_path, ['jquery'], 1, true);
            wp_enqueue_script('gpay', $gpay_path, ['jquery'], 1, true);
        }

        public function plugin_action_links($actions, $plugin_file, $plugin_data, $context)
        {
            $custom_actions = [
                'configure' => sprintf(
                    '<a href="%s">%s</a>',
                    admin_url('admin.php?page=wc-settings&tab=checkout&section=bluemedia_payment_gateway'),
                    __("Configuration", 'bluepayment-gateway-for-woocommerce') // Configuration
                ),
                'support' => sprintf(
                    '<a href="https://bluemedia.pl/oferta/platnosci/platnosci-online/integracja/integracja-platnosci-woocommerce" target="_blank">%s</a>',
                    __("Help", 'bluepayment-gateway-for-woocommerce') // Help
                ),
                'wc-status' => sprintf('<a href="%s">%s</a>', admin_url('admin.php?page=wc-status'), __("Status", 'bluepayment-gateway-for-woocommerce')), // Status
                'wc-logs' => sprintf(
                    '<a href="%s">%s</a>',
                    admin_url('admin.php?page=wc-status&tab=logs'),
                    __("Logs", 'bluepayment-gateway-for-woocommerce') // Logs
                ),
            ];

            return array_merge($custom_actions, $actions);
        }

        public function woocommerce_product_title($title)
        {
            $title = str_replace(['&#8211;', '&#8211'], ['-'], $title);

            return $title;
        }

        public function set_ignore_tag()
        {
            global $current_user;
            $plugin = plugin_basename(__FILE__);
            $plugin_data = get_plugin_data(__FILE__, false);

            if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))
                && !is_plugin_active_for_network('woocommerce/woocommerce.php')
            ) {
                if (isset($_GET['action'])
                    && !in_array($_GET['action'], ['activate-plugin', 'upgrade-plugin', 'activate', 'do-plugin-upgrade'])
                    && is_plugin_active($plugin)
                ) {
                    deactivate_plugins($plugin);
                    wp_die(
                        sprintf(
                            '<strong>%s</strong> requires <strong>WooCommerce</strong> plugin to work normally. Please activate it or <a href="http://wordpress.org/plugins/woocommerce/" target="_blank">install</a>.<br /><br />Back to the WordPress <a href="%s">Plugins page</a>.',
                            $plugin_data['Name'],
                            get_admin_url(null, 'plugins.php')
                        )
                    );
                }
            }

            /* If user clicks to ignore the notice, add that to their user meta */
            $user_id = $current_user->ID;
            $notices = ['ignore_bm_ssl', 'ignore_bm_mode', 'ignore_bm_woo'];
            foreach ($notices as $notice) {
                if (isset($_GET[$notice]) && '0' == $_GET[$notice]) {
                    add_user_meta($user_id, $notice, 'true', true);
                }
            }
        }

        public function admin_notices()
        {
            global $current_user, $blue_media_settings;
            $user_id = $current_user->ID;

            if (get_current_screen()->parent_base !== 'woocommerce') {
                return;
            }

            if (isset($blue_media_settings['enabled']) && $blue_media_settings['enabled'] == 'yes') {
                // Show message if enabled and FORCE SSL is disabled and WordpressHTTPS plugin is not detected
                if (get_option('woocommerce_force_ssl_checkout') == 'no' && !class_exists('WordPressHTTPS') && !get_user_meta($user_id, 'ignore_bm_ssl')) {
                    echo '<div class="error"><p>' . sprintf(
                            __("WooCommerce Blue Media Online Payment System requires that the <a href=\"%s\">Force secure checkout</a> option is enabled; your checkout may not be secure! Please enable SSL and ensure your server has a valid SSL certificate. | <a href=\"%s\">%s</a>", 'bluepayment-gateway-for-woocommerce'),
                            admin_url('admin.php?page=wc-settings&tab=advanced'),
                            add_query_arg('ignore_bm_ssl', 0),
                            __("Hide this notice", 'bluepayment-gateway-for-woocommerce')
                        ) . '</p></div>';
                }

                if (isset($blue_media_settings['payment_domain']) && $blue_media_settings['payment_domain'] == Gateway::PAYMENT_DOMAIN_SANDBOX && !get_user_meta($user_id, 'ignore_bm_mode')) {
                    echo '<div class="error"><p>' . sprintf(
                            __("WooCommerce Blue Media Online Payment System is currently running in Sandbox mode and will NOT process any actual payments. | <a href=\"%s\">%s</a>", 'bluepayment-gateway-for-woocommerce'),
                            add_query_arg('ignore_bm_mode', 0),
                            __("Hide this notice", 'bluepayment-gateway-for-woocommerce')
                        ) . '</p></div>';
                }
            }

            if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) && !get_user_meta(
                    $user_id,
                    'ignore_pp_woo'
                ) && !is_plugin_active_for_network('woocommerce/woocommerce.php')
            ) {
                echo '<div class="error"><p>' . sprintf(
                        __("WooCommerce Blue Media Online Payment System requires WooCommerce plugin to work normally. Please activate it or <a href=\"http://wordpress.org/plugins/woocommerce/\" target=\"_blank\">install it</a>. | <a href=\"%s\">%s</a>", 'bluepayment-gateway-for-woocommerce'),
                        add_query_arg('ignore_bm_woo', 0),
                        __("Hide this notice", 'bluepayment-gateway-for-woocommerce')
                    ) . '</p></div>';
            }
            $validation_notifications = get_option('validation_notifications');
            if (!empty($validation_notifications)) {
                $validation_notifications = json_decode($validation_notifications);
                foreach ($validation_notifications as $notification) {
                    echo '<div class="'.$notification[0].'"><p>' . sprintf(
                            __($notification[2], 'bluepayment-gateway-for-woocommerce'),
                            '',
                            '',
                            __("Hide this notice", 'bluepayment-gateway-for-woocommerce')
                        ) . '</p></div>';
                }
                update_option('validation_notifications', false);
            }
        }

        public function init()
        {
            if (!class_exists('WC_Payment_Gateway')) {
                return;
            }

            load_plugin_textdomain('bluepayment-gateway-for-woocommerce', false, dirname(plugin_basename(__FILE__)) . '/i18n/languages/');
            add_filter('woocommerce_payment_gateways', [$this, 'addPaymentGatewayList'], 1000);
        }

        public function activate()
        {
            if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))
                && !is_plugin_active_for_network('woocommerce/woocommerce.php')
            ) {
                deactivate_plugins(plugin_basename(__FILE__));
            }

            $this->addSqlTableForBlik();
        }

        private function addSqlTableForBlik()
        {
            global $wpdb;
            $table_name = $wpdb->prefix . 'bluemedia_blik';

            if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
                $sql = "CREATE TABLE wp_bluemedia_blik ( hash_cart varchar(200) NOT NULL, blik_code int(6) NOT NULL, wrong_code int(11) NOT NULL DEFAULT '0', status int(11) NOT NULL COMMENT '0 - nowy, 1- ok, 3- bledny kod, 4 - timeout', date_start datetime NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
                $wpdb->query($sql);

                $sql = "ALTER TABLE wp_bluemedia_blik ADD KEY hash_cart (hash_cart), ADD KEY blik_code (blik_code), ADD KEY status (status), ADD KEY wrong_code (wrong_code);";
                $wpdb->query($sql);
            }
        }

        public function addPaymentGatewayList($methods): array
        {
            global $blue_media_settings;

            $paymentGateways = new PaymentGateways();
            $paymentGateways->addPaymentMethod(new BlueMediaPaywallPaymentMethod());

            if ($blue_media_settings) {
                $paymentGateways->addPaymentMethod(new GpayPopupPaymentMethod());
                $paymentGateways->addPaymentMethod(new BlikPblPaymentMethod());
                $paymentGateways->addPaymentMethod(new BlikZeroPaymentMethod());
                $paymentGateways->addPaymentMethod(new BlueMediaCardPaymentMethod());
                $paymentGateways->addPaymentMethod(new BlueMediaInstallmentPaymentMethod());
                if ($this->smartney_should_be_visible()) {
                    $paymentGateways->addPaymentMethod(new SmartneyPopupPaymentMethod());
                }


                return  array_merge(
                    $methods,
                    $paymentGateways->handle($blue_media_settings)
                );
            }

            return array_merge(
                $methods,
                $paymentGateways->handle([])
            );
        }

        public function smartney_should_be_visible(): bool
        {
            global $wp;
            $shouldBeAdded = false;

            if (isset($wp->query_vars['order-pay'])) {
                $orderId = $wp->query_vars['order-pay'];
                $order = wc_get_order( $orderId );
                $total = $order->get_total();
            } elseif (!empty(WC()->cart)) {
                $total = WC()->cart->total;
            }

            if (isset($total) && $total>=Gateway::GATEWAY_SMARTNEY_MIN && $total<Gateway::GATEWAY_SMARTNEY_MAX) {
                $shouldBeAdded = true;
            }

            return $shouldBeAdded;

        }

        public function bluemedia_edit_background_payments_channels()
        {
            global $blue_media_settings;

            $paymentGatewayBlueMedia = new WC_Payment_Gateway_BlueMedia;

            if (!empty($_POST) && !empty($_POST['currency'])) {
                $currency = in_array(strtoupper($_POST['currency']), (new CurrencyDictionary())->getAvailableCurrencies())
                    ? $_POST['currency']
                    : PaymentEnum::DEFAULT_CURRENCY;

                $postData = $_POST; // Used in template

                $paymentChannels = (new PaywayListProvider(
                    new BlueMediaSdkHandler(
                        $blue_media_settings['payment_domain'],
                        $blue_media_settings['service_id_' . $currency],
                        $blue_media_settings['hash_key_' . get_woocommerce_currency()]
                    )
                ))->getPaywayList();

                $channels_sort = (int) $_POST['channels_sort'];
                if ($channels_sort === 1) {
                    $channels = isset($blue_media_settings['backgorund_channels'][$currency]) ? $blue_media_settings['backgorund_channels'][$currency] : [];

                    // Used in template
                    $paymentChannels = (new CustomGatewayOrderSorter())->sort(
                        $channels,
                        $paymentChannels
                    );
                } elseif ($channels_sort === 0 && empty($blue_media_settings["background_channels_sort_$currency"])) {
                    $paymentChannels = (new DefaultGatewayOrderSorter())->sort($paymentChannels);
                }

                require_once 'template/_partials/admin/_partials/bluemedia-channel-list.php';

            } else {
                echo __("Please save the plug-in configuration with the specified serviceID and hash key.\n", 'bluepayment-gateway-for-woocommerce');
            }
            wp_die();
        }

        public function bluemedia_blik_transaction_processed($orderId)
        {
            $blikOrder = WC()->session->get('bluemedia_blik_order');
            if ($_POST['blik_trigger_flag'] == '1' && empty($blikOrder)) {
                wc_add_notice('blik_trigger_value', 'error');
                WC()->session->set('next_bm_blik_order', $orderId);
                WC()->session->set('order_id_validation', $orderId);
                wp_send_json([
                    'orderId' => $orderId,
                    'result' => 'failure',
                    'messages' => wc_print_notices(true),
                    'refresh' => false,
                    'reload' => false,
                ]);
            }
        }

        public function blikOrderHandler()
        {
            $is_blik_payment = isset($_POST['payment_method']) && $_POST['payment_method'] === 'bluemedia_payment_gateway_blik';
            $orderId = WC()->session->get('order_id_validation');

            if (!empty($orderId)) {
                $order = wc_get_order($orderId);
                if ($is_blik_payment && $order->has_cart_hash(WC()->cart->get_cart_hash())) {
                    $this->bluemedia_blik_transaction_processed($orderId);

                    exit;
                }

                $this->restoreOrder($order);
            }
        }

        public function restoreOrder($order)
        {
            if ($order->has_cart_hash(WC()->cart->get_cart_hash())) {
                WC()->session->set('order_awaiting_payment', $order->get_id());
            }

            WC()->session->set('order_id_validation', 0);
        }

        public function setOrderSession()
        {
            global $wp;

            if (!empty($wp->query_vars['pagename']) && $wp->query_vars['pagename'] === 'checkout') {
                WC()->session->set('next_bm_blik_order', $wp->query_vars['order-pay']);
                WC()->session->set('order_id_validation', $wp->query_vars['order-pay']);
            }
        }

        public function cleanOrderedCart($product_id)
        {
            $orderId = WC()->session->get('order_id_validation');
            if (!empty($orderId)) {
                $order = wc_get_order($orderId);

                if ($order->has_cart_hash(WC()->cart->get_cart_hash())) {
                    wc_empty_cart();
                }
            }

            return $product_id;
        }

        public function clearUnfinishedCheckoutPage()
        {
            WC()->cart->empty_cart();
        }

        public function woo_change_order_received_text($str) {
            $waiting_for_payment_text = __("We are waiting for payment. If you have interrupted it for any reason, please order it again.", 'bluepayment-gateway-for-woocommerce');
            return $str . ' ' . $waiting_for_payment_text;
        }

        public function woo_display_order_status_message($order_id) {
            $order = wc_get_order($order_id);
            $order_status = $order->get_status();

            $message = OrderStatusMessageDictionary::getMessage($order_status);

            $order_status_message = sprintf(
                '%s: %s',
                __("Order status", 'bluepayment-gateway-for-woocommerce'),
                $message ? __($message.'.order.thankyou', 'bluepayment-gateway-for-woocommerce') : $order_status
            );

            require_once dirname(__FILE__) . '/template/_partials/order/thank-you-order-status-message.tpl.php';
            return;
        }
    }
}

new BlueMedia_Payment_Gateway();
