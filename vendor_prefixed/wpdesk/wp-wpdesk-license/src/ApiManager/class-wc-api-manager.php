<?php

namespace BmWoocommerceVendor;

// Exit if accessed directly
use BmWoocommerceVendor\WPDesk\Notice\AjaxHandler;
use BmWoocommerceVendor\WPDesk\Notice\PermanentDismissibleNotice;
if (!\defined('ABSPATH')) {
    exit;
}
if (!\class_exists('BmWoocommerceVendor\\WPDesk_API_Manager_With_Update_Flag')) {
    class WPDesk_API_Manager_With_Update_Flag
    {
        /**
         * Self Upgrade Values
         */
        // Base URL to the remote upgrade API Manager server. If not set then the Author URI is used.
        public $upgrade_url;
        /**
         * @var string
         */
        //public $version = '4.0';
        public $version;
        /**
         * @var string
         * This version is saved after an upgrade to compare this db version to $version
         */
        public $api_version_name;
        /**
         * Software Product ID is the product title string
         * This value must be unique, and it must match the API tab for the product in WooCommerce
         */
        private $software_product_id;
        public $settings_menu_title;
        public $settings_title;
        public $plugin_dir;
        /**
         * @var string
         * used to defined localization for translation, but a string literal is preferred
         *
         * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/issues/59
         * http://markjaquith.wordpress.com/2011/10/06/translating-wordpress-plugins-and-themes-dont-get-clever/
         * http://ottopress.com/2012/internationalization-youre-probably-doing-it-wrong/
         */
        public $text_domain = 'bm-woocommerce';
        /**
         * @var string
         */
        public $plugin_url;
        /**
         * Data defaults
         * @var mixed
         */
        public $data_key;
        public $api_key;
        public $activation_email;
        public $product_id_key;
        public $instance_key;
        public $deactivate_checkbox_key;
        public $activated_key;
        public $upgrade_url_key;
        public $deactivate_checkbox;
        public $activation_tab_key;
        public $deactivation_tab_key;
        public $menu_tab_activation_title;
        public $menu_tab_deactivation_title;
        public $options;
        public $plugin_name;
        public $product_id;
        public $renew_license_url;
        public $instance_id;
        public $domain;
        public $software_version;
        public $plugin_or_theme;
        public $update_version;
        public $config_uri;
        /**
         * Used to send any extra information.
         * @var mixed array, object, string, etc.
         */
        public $extra;
        /**
         */
        private $key_insance = null;
        private $update_check_insance = null;
        /**
         * Cloning is forbidden.
         *
         * @since 1.2
         */
        private function __clone()
        {
        }
        /**
         * Unserializing instances of this class is forbidden.
         *
         * @since 1.2
         */
        public function __wakeup()
        {
        }
        public function __construct($upgrade_url, $version, $name, $product_id, $menu_title, $title, $plugin_file, $plugin_dir, $config_uri, $hook_to_updates = \true)
        {
            global $wpdesk_installed_plugins;
            if (!$wpdesk_installed_plugins) {
                $wpdesk_installed_plugins = [];
            }
            $wpdesk_installed_plugins[\trailingslashit($plugin_dir) . $plugin_file] = $this;
            //
            $this->upgrade_url_key = "api_{$plugin_dir}_upgrade_url";
            $tmp_upgrade_url = \get_option($this->upgrade_url_key, '');
            if ($tmp_upgrade_url == '') {
                $tmp_upgrade_url = $upgrade_url;
                \update_option($this->upgrade_url_key, $tmp_upgrade_url);
            }
            $this->upgrade_url = $tmp_upgrade_url;
            $this->version = $version;
            $this->api_version_name = 'plugin_' . $name . '_version';
            $this->software_product_id = $product_id;
            $this->settings_menu_title = $menu_title;
            $this->settings_title = $title;
            $this->plugin_dir = $plugin_dir;
            $this->config_uri = $config_uri;
            $this->plugin_url = \plugins_url('/', $plugin_file);
            if (\is_admin()) {
                // Check for external connection blocking
                \add_action('admin_notices', [$this, 'check_external_blocking']);
                /**
                 * Set all data defaults here
                 */
                $this->data_key = 'api_' . $plugin_dir;
                $this->api_key = "api_{$plugin_dir}_key";
                $this->activation_email = "api_{$plugin_dir}_activation_email";
                $this->product_id_key = "api_{$plugin_dir}_product_id";
                $this->instance_key = "api_{$plugin_dir}_instance";
                $this->deactivate_checkbox_key = "api_{$plugin_dir}_deactivate_checkbox";
                $this->activated_key = "api_{$plugin_dir}_activated";
                /**
                 * Set all admin menu data
                 */
                $this->deactivate_checkbox = 'am_deactivate_example_checkbox';
                $this->activation_tab_key = "api_{$plugin_dir}_dashboard";
                $this->deactivation_tab_key = "api_{$plugin_dir}_deactivation";
                $this->menu_tab_activation_title = \__('Subscription Activation', $this->text_domain);
                $this->menu_tab_deactivation_title = \__('Subscription Deactivation', $this->text_domain);
                /**
                 * Set all software update data here
                 */
                $this->options = \get_option($this->data_key);
                //$this->plugin_name 			= untrailingslashit( dirname( $plugin_file) ); // same as plugin slug. if a theme use a theme name like 'twentyeleven'
                $this->plugin_name = $name;
                // same as plugin slug. if a theme use a theme name like 'twentyeleven'
                $this->product_id = $this->software_product_id;
                //get_option( $this->product_id_key ); // Software Title
                $this->renew_license_url = $this->upgrade_url . '/my-account';
                // URL to renew a license. Trailing slash in the upgrade_url is required.
                $this->instance_id = \get_option($this->instance_key);
                // Instance ID (unique to each blog activation)
                if (\get_option($this->activated_key, '0') != 'Activated') {
                    $this->inactive_notice();
                }
                /* grola */
                if (!$this->instance_id) {
                    $this->create_instance_id();
                }
                /**
                 * Some web hosts have security policies that block the : (colon) and // (slashes) in http://,
                 * so only the host portion of the URL can be sent. For example the host portion might be
                 * www.example.com or example.com. http://www.example.com includes the scheme http,
                 * and the host www.example.com.
                 * Sending only the host also eliminates issues when a client site changes from http to https,
                 * but their activation still uses the original scheme.
                 * To send only the host, use a line like the one below:
                 *
                 * $this->domain = str_ireplace( array( 'http://', 'https://' ), '', home_url() ); // blog domain name
                 */
                $this->domain = \str_ireplace(['http://', 'https://'], '', \home_url());
                // blog domain name
                $this->software_version = $this->version;
                // The software version
                $this->plugin_or_theme = 'plugin';
                // 'theme' or 'plugin'
                // Performs activations and deactivations of API License Keys
                require_once 'class-wc-key-api.php';
                // Checks for software updatess
                require_once 'class-wc-plugin-update.php';
                // Admin menu with the license key and license email form
                //				require_once( 'class-wc-api-manager-menu.php' );
                //				new WPDesk_API_MENU( $this );
                $options = \get_option($this->data_key);
                /**
                 * Check for software updates
                 */
                if ($hook_to_updates) {
                    if (!empty($options) && $options !== \false) {
                        $this->update_check($this->upgrade_url, $this->plugin_name, $this->product_id, $this->options[$this->api_key], $this->options[$this->activation_email], $this->renew_license_url, $this->instance_id, $this->domain, $this->software_version, $this->plugin_or_theme, $this->text_domain);
                    }
                    $this->add_not_possible_update_message();
                }
            }
        }
        /**
         * Adds message to plugins page when plugin is not activated with info about subscription.
         */
        private function add_not_possible_update_message()
        {
            \add_action('in_plugin_update_message-' . $this->plugin_name, function (array $plugin_data, \stdClass $response) {
                if (isset($response, $response->package) && empty($response->package)) {
                    echo \sprintf(\__(" <a target='_blank' href='%s'>Enter a valid subscription key for automatic updates.</a>", 'bm-woocommerce'), \admin_url('admin.php?page=wpdesk-licenses'));
                }
                if (isset($response->changelog) && !empty($response->changelog)) {
                    $this->display_changelog($plugin_data['Version'], $response->changelog);
                }
            }, 10, 2);
        }
        public function create_instance_id()
        {
            require_once 'class-wc-api-manager-passwords.php';
            $password_management = new \BmWoocommerceVendor\WPDesk_API_Password_Management();
            // Generate a unique installation $instance id
            $instance = $password_management->generate_password(12, \false);
            $this->instance_id = $instance;
            \update_option($this->instance_key, $instance);
            \update_option($this->activated_key, 'Deactivated');
            $this->options = [];
            $this->options[$this->api_key] = '';
            $this->options[$this->activation_email] = '';
            \update_option($this->data_key, $this->options);
        }
        /** Load Shared Classes as on-demand Instances **********************************************/
        /**
         * API Key Class.
         *
         * @return WPDesk_API_KEY
         */
        public function key()
        {
            if ($this->key_insance == null) {
                $this->key_insance = new \BmWoocommerceVendor\WPDesk_API_KEY($this);
            }
            return $this->key_insance;
        }
        /**
         * Update Check Class.
         *
         * @return WPDesk_Update_API_Check
         */
        public function update_check($upgrade_url, $plugin_name, $product_id, $api_key, $activation_email, $renew_license_url, $instance, $domain, $software_version, $plugin_or_theme, $text_domain, $extra = '')
        {
            if ($this->update_check_insance == null) {
                $this->update_check_insance = new \BmWoocommerceVendor\WPDesk_Update_API_Check($this, $upgrade_url, $plugin_name, $product_id, $api_key, $activation_email, $renew_license_url, $instance, $domain, $software_version, $plugin_or_theme, $text_domain, $extra);
            }
            return $this->update_check_insance;
        }
        public function plugin_url()
        {
            if (isset($this->plugin_url)) {
                return $this->plugin_url;
            }
            return $this->plugin_url = \plugins_url('/', __FILE__);
        }
        /**
         * Generate the default data arrays
         */
        public function activation()
        {
            $global_options = [$this->api_key => '', $this->activation_email => ''];
            \update_option($this->data_key, $global_options);
            require_once $this->plugin_dir . 'am/classes/class-wc-api-manager-passwords.php';
            $password_management = new \BmWoocommerceVendor\WPDesk_API_Password_Management();
            // Generate a unique installation $instance id
            $instance = $password_management->generate_password(12, \false);
            $single_options = [$this->product_id_key => $this->software_product_id, $this->instance_key => $instance, $this->deactivate_checkbox_key => 'on', $this->activated_key => 'Deactivated'];
            foreach ($single_options as $key => $value) {
                \update_option($key, $value);
            }
            $curr_ver = \get_option($this->api_version_name);
            // checks if the current plugin version is lower than the version being installed
            if (\version_compare($this->version, $curr_ver, '>')) {
                // update the version
                \update_option($this->api_version_name, $this->version);
            }
        }
        /**
         * Deletes all data if plugin deactivated
         * @return void
         */
        public function uninstall()
        {
            global $blog_id;
            $this->license_key_deactivation();
            // Remove options
            if (\is_multisite()) {
                \switch_to_blog($blog_id);
                foreach ([$this->data_key, $this->product_id_key, $this->instance_key, $this->deactivate_checkbox_key, $this->activated_key] as $option) {
                    \delete_option($option);
                }
                \restore_current_blog();
            } else {
                foreach ([$this->data_key, $this->product_id_key, $this->instance_key, $this->deactivate_checkbox_key, $this->activated_key] as $option) {
                    \delete_option($option);
                }
            }
        }
        /**
         * Deactivates the license on the API server
         * @return void
         */
        public function license_key_deactivation()
        {
            $activation_status = \get_option($this->activated_key);
            $api_email = $this->options[$this->activation_email];
            $api_key = $this->options[$this->api_key];
            $args = ['email' => $api_email, 'licence_key' => $api_key];
            if ($activation_status == 'Activated' && $api_key != '' && $api_email != '') {
                $this->key()->deactivate($args);
                // reset license key activation
            }
        }
        /**
         * Displays an inactive notice when the software is inactive.
         */
        public function inactive_notice()
        {
            if (!\current_user_can('manage_options')) {
                return;
            }
            if (isset($_GET['page']) && 'wpdesk-licenses' === $_GET['page']) {
                return;
            }
            if (\apply_filters('wpdesk_show_plugin_activation_notice', \true)) {
                (new \BmWoocommerceVendor\WPDesk\Notice\AjaxHandler())->hooks();
                new \BmWoocommerceVendor\WPDesk\Notice\PermanentDismissibleNotice(\sprintf(\__('The %s%s%s API Key has not been activated, so you won\'t be supported and your plugin won\'t be updated! %sClick here%s to activate the API key and the plugin.', $this->text_domain), '<strong>', $this->software_product_id, '</strong>', '<a href="' . \esc_url(\admin_url('admin.php?page=wpdesk-licenses')) . '">', '</a>'), "notice-{$this->software_product_id}", \BmWoocommerceVendor\WPDesk\Notice\PermanentDismissibleNotice::NOTICE_TYPE_WARNING);
            }
        }
        /**
         * Check for external blocking contstant
         * @return string
         */
        public function check_external_blocking()
        {
            // show notice if external requests are blocked through the WP_HTTP_BLOCK_EXTERNAL constant
            if (\defined('BmWoocommerceVendor\\WP_HTTP_BLOCK_EXTERNAL') && \BmWoocommerceVendor\WP_HTTP_BLOCK_EXTERNAL === \true) {
                // check if our API endpoint is in the allowed hosts
                $host = \parse_url($this->upgrade_url, \PHP_URL_HOST);
                if (!\defined('BmWoocommerceVendor\\WP_ACCESSIBLE_HOSTS') || \stristr(\BmWoocommerceVendor\WP_ACCESSIBLE_HOSTS, $host) === \false) {
                    ?>
                    <div class="error">
                        <p><?php 
                    \printf(\__('<b>Warning!</b> You\'re blocking external requests which means you won\'t be able to get %s updates. Please add %s to %s.', $this->text_domain), $this->software_product_id, '<strong>' . $host . '</strong>', '<code>WP_ACCESSIBLE_HOSTS</code>');
                    ?></p>
                    </div>
					<?php 
                }
            }
        }
        /**
         * @param string $plugin_data
         * @param string $response
         */
        private function display_changelog($plugin_version, $changelog)
        {
            $parser = new \BmWoocommerceVendor\WPDesk\License\Changelog\Parser($changelog);
            $parser->parse();
            $parsed_changelog = $parser->get_parsed_changelog()->getIterator();
            $changes = new \BmWoocommerceVendor\WPDesk\License\Changelog\Filter\ByVersion($parsed_changelog, $plugin_version);
            if (\iterator_count($changes) > 0) {
                $changelog = new \BmWoocommerceVendor\WPDesk\License\Changelog\Formatter($changes);
                $changelog->set_changelog_types($parser->get_types());
                $formatted_changelog = $changelog->prepare_formatted_html();
                if ($formatted_changelog) {
                    echo '<br /><br />' . $formatted_changelog;
                }
            }
        }
    }
    // class Ilabs_API_Manager
}
// if (!class_exists('Ilabs_API_Manager')) {
