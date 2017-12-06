<?php
/**
 * @wordpress-plugin
 *
 * System płatności online Blue Media
 *
 * @author    Piotr Żuralski <piotr@zuralski.net>
 * @copyright 2015 Blue Media S.A.
 * @license   http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 * @see       http://english.bluemedia.pl/project/payment_gateway_on-line_payment_processing/ (English)
 * @see       http://bluemedia.pl/projekty/payment_gateway_bramka_platnicza_do_realizowania_platnosci_online (Polish)
 * @since     2015-02-28
 * @version   v1.2.0
 *
 * Plugin Name:       System płatności online Blue Media dla WooCommerce
 * Description:       Easily enable Blue Media Payment Gateway with WooCommerce
 * Version:           1.2.0
 * Author:            Piotr Żuralski
 * Author URI:        http://zuralski.net/
 * License:           GNU General Public License, version 3 (GPL-3.0)
 * License URI:       http://opensource.org/licenses/GPL-3.0
 * Domain Path:       /i18n/languages/
 */

/**
 * Exit if accessed directly.
 */
if (!defined('ABSPATH')) {
    exit();
}

/*
 * Set global parameters
 */
global $woocommerce, $blue_media_settings, $wp_version;

/*
 * Get Settings
 */
$blue_media_settings = get_option('woocommerce_bluemedia_payment_gateway_settings');

require_once dirname(__FILE__).'/../woocommerce/includes/abstracts/abstract-wc-settings-api.php';
require_once dirname(__FILE__).'/../woocommerce/includes/abstracts/abstract-wc-payment-gateway.php';
require_once dirname(__FILE__).'/classes/wc-payment-gateway-bluemedia.php';
require_once dirname(__FILE__).'/tables/wc-payment-gateway.php';
require_once dirname(__FILE__).'/classes/wc-payment-gateway.php';

if (!class_exists('BlueMedia_Payment_Gateway')) {

    /**
     * System płatności online Blue Media.
     *
     * @author    Piotr Żuralski <piotr@zuralski.net>
     * @copyright 2015 Blue Media S.A.
     * @license   http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
     *
     * @see       http://english.bluemedia.pl/project/payment_gateway_on-line_payment_processing/ (English)
     * @see       http://bluemedia.pl/projekty/payment_gateway_bramka_platnicza_do_realizowania_platnosci_online (Polish)
     * @since     2015-02-28
     *
     * @version   v1.2.0
     */
    class BlueMedia_Payment_Gateway
    {
        /**
         * constructor.
         */
        public function __construct()
        {
            /*
             * Check current WooCommerce version to ensure compatibility.
             */
            $woo_version = $this->get_woo_version_number();
            if (version_compare($woo_version, '2.1', '<')) {
                exit(__('Blue Media Online Payment System for WooCommerce requires WooCommerce version 2.1 or higher. Please backup your site files and database, update WooCommerce, and try again.', 'bluemedia-payment-gateway-for-woocommerce'));
            }
            if (!extension_loaded('xmlwriter') || !class_exists('XMLWriter')) {
                exit(__('This plugin requires <a href="http://php.net/manual/en/book.xmlreader.php">XMLReader</a>', 'bluemedia-payment-gateway-for-woocommerce'));
            }
            if (!extension_loaded('xmlreader') || !class_exists('XMLReader')) {
                exit(__('This plugin requires <a href="http://php.net/manual/en/book.xmlwriter.php">XMLWriter</a>', 'bluemedia-payment-gateway-for-woocommerce'));
            }

            add_action('plugins_loaded', array($this, 'init'));
            register_activation_hook(__FILE__, array($this, 'activate'));
            register_activation_hook(__FILE__, array($this, 'update_db'));
            add_action('admin_notices', array($this, 'admin_notices'));
            add_action('admin_init', array($this, 'set_ignore_tag'));
            add_filter('woocommerce_product_title', array($this, 'woocommerce_product_title'));

            // http://stackoverflow.com/questions/22577727/problems-adding-action-links-to-wordpress-plugin
            $basename = plugin_basename(__FILE__);
            $prefix = ((is_network_admin()) ? 'network_admin_' : '');
            add_filter($prefix . 'plugin_action_links_' . $basename, array($this, 'plugin_action_links'), 10, 4);
            add_action('admin_enqueue_scripts', array($this, 'add_admin_scripts'));

            add_action('admin_menu', array($this, 'add_admin_page_menu'));

        }


        public function add_admin_page_menu(){
            add_menu_page("Blue Media", "Blue media Kanały płatności", 'manage_options', 'bluepayment_manage_gateway',
                array($this, 'manage_gatway'));
            add_submenu_page(null, "Blue Media", "Aktualizuj Kanały płatności", 'manage_options',
                'bluepayment_manage_gateway_update', array($this, 'update_gateway'));

        }

        function update_gateway(){
            global $blue_media_settings;

            if ( !current_user_can( 'manage_options' ) )  {
                wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
            }
            $gatway_objects = new WC_Bluepayment_gateway($blue_media_settings);
            $gatway_objects->syncGateways();
            wp_redirect(admin_url('admin.php?page=bluepayment_manage_gateway'));
        }

        function manage_gatway() {
            if ( !current_user_can( 'manage_options' ) )  {
                wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
            }
            global $wpdb;
            $table_name = $wpdb->prefix . 'blue_gateways';

            if (isset($_GET['action']) ) {
                if ($_GET['action'] == 'deactivate'){
                    $wpdb->update($table_name, array('gateway_status'=> 1), array('gateway_id'=>$_GET['gateway_id']));
                }
                if ($_GET['action'] == 'activate'){
                    $wpdb->update($table_name, array('gateway_status'=> 0), array('gateway_id'=>$_GET['gateway_id']));
                }
                wp_redirect(admin_url('admin.php?page=bluepayment_manage_gateway'));
            }

            $this->gateway_list_table = new BluemediaGateway_List_Table($this->plugin_text_domain);
            $this->gateway_list_table->prepare_items();

            $this->url_to_update = admin_url('admin.php?page=bluepayment_manage_gateway_update');

            include_once dirname(__FILE__).'/template/bluemedia-admin-manage-gateway.tpl.php';
        }

        public function update_db()
        {
            global $wpdb;

            $version = get_option("blue_gateways_db_version", '1.0');
            $charset_collate = $wpdb->get_charset_collate();

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            if (version_compare($version, '2.0' ) < 0) {
                $table_name = $wpdb->prefix . 'blue_gateways';
                $sql = "CREATE TABLE 
                    $table_name  (
                        entity_id INT NOT NULL AUTO_INCREMENT,
                        gateway_status INT NOT NULL,
                        gateway_id INT NOT NULL,
                        bank_name VARCHAR(100) CHARACTER SET utf8 NOT NULL,
                        gateway_name VARCHAR(100) CHARACTER SET utf8 NOT NULL,
                        gateway_description VARCHAR(1000) CHARACTER SET utf8,
                        gateway_sort_order INT,
                        gateway_type VARCHAR(50) CHARACTER SET utf8 NOT NULL,
                        gateway_logo_url VARCHAR(500) CHARACTER SET utf8,
                        status_date TIMESTAMP NOT NULL,
                        mode VARCHAR( 32 ) NOT NULL,
                        PRIMARY KEY(entity_id)
                    ) $charset_collate;";
                dbDelta($sql);
                update_option("blue_gateways_db_version", '2.0');
            }

        }
        /**
         * Get WooCommerce Version Number
         * http://wpbackoffice.com/get-current-woocommerce-version-number/.
         */
        public function get_woo_version_number()
        {
            // If get_plugins() isn't available, require it
            if (!function_exists('get_plugins')) {
                require_once ABSPATH.'wp-admin/includes/plugin.php';
            }

            // Create the plugins folder and file variables
            $plugin_folder = get_plugins('/'.'woocommerce');
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

        /**
         * Return the plugin action links. This will only be called if the plugin
         * is active.
         *
         * @param $actions
         * @param $plugin_file
         * @param $plugin_data
         * @param $context
         *
         * @return array
         */
        public function plugin_action_links($actions, $plugin_file, $plugin_data, $context)
        {
            $custom_actions = array(
                'configure' => sprintf('<a href="%s">%s</a>', admin_url('admin.php?page=wc-settings&tab=checkout&section=bluemedia_payment_gateway'), __('Konfiguracja', 'bluemedia-payment-gateway-for-woocommerce')),
                'support'   => sprintf('<a href="mailto:info@bm.pl" target="_blank">%s</a>', __('Pomoc', 'bluemedia-payment-gateway-for-woocommerce')),
                'wc-status' => sprintf('<a href="%s">%s</a>', admin_url('admin.php?page=wc-status'), __('Status', 'bluemedia-payment-gateway-for-woocommerce')),
                'wc-logs'   => sprintf('<a href="%s">%s</a>', admin_url('admin.php?page=wc-status&tab=logs'), __('Logi', 'bluemedia-payment-gateway-for-woocommerce')),
            );

            // add the links to the front of the actions list
            return array_merge($custom_actions, $actions);
        }

        public function woocommerce_product_title($title)
        {
            $title = str_replace(array('&#8211;', '&#8211'), array('-'), $title);

            return $title;
        }

        public function set_ignore_tag()
        {
            global $current_user;
            $plugin = plugin_basename(__FILE__);
            $plugin_data = get_plugin_data(__FILE__, false);

            if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) && !is_plugin_active_for_network('woocommerce/woocommerce.php')) {
                if (isset($_GET['action']) && !in_array($_GET['action'], array('activate-plugin', 'upgrade-plugin', 'activate', 'do-plugin-upgrade')) && is_plugin_active($plugin)) {
                    deactivate_plugins($plugin);
                    wp_die(sprintf('<strong>%s</strong> requires <strong>WooCommerce</strong> plugin to work normally. Please activate it or <a href="http://wordpress.org/plugins/woocommerce/" target="_blank">install</a>.<br /><br />Back to the WordPress <a href="%s">Plugins page</a>.', $plugin_data['Name'], get_admin_url(null, 'plugins.php')));
                }
            }
            $user_id = $current_user->ID;
            /* If user clicks to ignore the notice, add that to their user meta */
            $notices = array('ignore_pp_ssl', 'ignore_bm_mode', 'ignore_bm_woo');
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

//            $blue_media_settings = get_option('woocommerce_bluemedia_payment_gateway_settings');

            if (isset($blue_media_settings['enabled']) && $blue_media_settings['enabled'] == 1) {
                // Show message if enabled and FORCE SSL is disabled and WordpressHTTPS plugin is not detected
                if (get_option('woocommerce_force_ssl_checkout') == 'no' && !class_exists('WordPressHTTPS') && !get_user_meta($user_id, 'ignore_pp_ssl')) {
                    echo '<div class="error"><p>'.sprintf(__('WooCommerce Blue Media Online Payment System requires that the <a href="%s">Force secure checkout</a> option is enabled; your checkout may not be secure! Please enable SSL and ensure your server has a valid SSL certificate. | <a href="%s">%s</a>', 'bluemedia-payment-gateway-for-woocommerce'), admin_url('admin.php?page=woocommerce'), add_query_arg('ignore_bm_ssl', 0), __('Hide this notice', 'bluemedia-payment-gateway-for-woocommerce')).'</p></div>';
                }
                if ((isset($blue_media_settings['mode']) && $blue_media_settings['mode'] == WC_Payment_Gateway_BlueMedia::MODE_SANDBOX)) {
                    echo '<div class="error"><p>'.sprintf(__('WooCommerce Blue Media Online Payment System is currently running in Sandbox mode and will NOT process any actual payments. | <a href="%s">%s</a>', 'bluemedia-payment-gateway-for-woocommerce'), add_query_arg('ignore_bm_mode', 0), __('Hide this notice', 'bluemedia-payment-gateway-for-woocommerce')).'</p></div>';
                }
            }

            if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) && !get_user_meta($user_id, 'ignore_pp_woo') && !is_plugin_active_for_network('woocommerce/woocommerce.php')) {
                echo '<div class="error"><p>'.sprintf(__('WooCommerce Blue Media Online Payment System requires WooCommerce plugin to work normally. Please activate it or <a href="http://wordpress.org/plugins/woocommerce/" target="_blank">install it</a>. | <a href="%s">%s</a>', 'bluemedia-payment-gateway-for-woocommerce'), add_query_arg('ignore_bm_woo', 0), __('Hide this notice', 'bluemedia-payment-gateway-for-woocommerce')).'</p></div>';
            }
        }

        //init function
        public function init()
        {
            if (!class_exists('WC_Payment_Gateway')) {
                return;
            }
            load_plugin_textdomain('bluemedia-payment-gateway-for-woocommerce', false, dirname(plugin_basename(__FILE__)).'/i18n/languages/');
            add_filter('woocommerce_payment_gateways', array($this, 'gateway_add'), 1000);
        }

        /**
         * Run when plugin is activated.
         */
        public function activate()
        {
            // If WooCommerce is not enabled, deactivate plugin.
            if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) && !is_plugin_active_for_network('woocommerce/woocommerce.php')) {
                deactivate_plugins(plugin_basename(__FILE__));
            }
        }

        /**
         * Adds gateway options for into the WooCommerce checkout settings.
         */
        public function gateway_add($methods)
        {
            $methods[] = 'WC_Payment_Gateway_BlueMedia';

            return $methods;
        }
    }
}
new BlueMedia_Payment_Gateway();
