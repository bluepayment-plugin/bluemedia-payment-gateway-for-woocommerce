<?php

namespace BmWoocommerceVendor;

if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
/**
 * Todd Lahman LLC Updater - Single Updater Class
 *
 * @package Update API Manager/Update Handler
 * @author Todd Lahman LLC
 * @copyright   Copyright (c) Todd Lahman LLC
 * @since 1.0.0
 *
 */
if (!\class_exists('BmWoocommerceVendor\\WPDesk_Update_API_Check')) {
    class WPDesk_Update_API_Check
    {
        /**
         * @var The single instance of the class
         */
        protected static $_instance = null;
        private $api_manager;
        /**
         *
         * Ensures only one instance is loaded or can be loaded.
         *
         * @static
         * @return class instance
         */
        public static function instance($upgrade_url, $plugin_name, $product_id, $api_key, $activation_email, $renew_license_url, $instance, $domain, $software_version, $plugin_or_theme, $text_domain, $extra = '')
        {
            if (\is_null(self::$_instance)) {
                self::$_instance = new self($upgrade_url, $plugin_name, $product_id, $api_key, $activation_email, $renew_license_url, $instance, $domain, $software_version, $plugin_or_theme, $text_domain, $extra);
            }
            return self::$_instance;
        }
        private $upgrade_url;
        // URL to access the Update API Manager.
        private $plugin_name;
        private $plugin_file;
        private $product_id;
        // Software Title
        private $api_key;
        // API License Key
        private $activation_email;
        // License Email
        private $renew_license_url;
        // URL to renew a license
        private $instance;
        // Instance ID (unique to each blog activation)
        private $domain;
        // blog domain name
        private $software_version;
        private $plugin_or_theme;
        // 'theme' or 'plugin'
        private $text_domain;
        // localization for translation
        private $extra;
        // Used to send any extra information.
        /**
         * Constructor.
         *
         * @access public
         * @since  1.0.0
         * @return void
         */
        public function __construct($api_manager, $upgrade_url, $plugin_name, $product_id, $api_key, $activation_email, $renew_license_url, $instance, $domain, $software_version, $plugin_or_theme, $text_domain, $extra, $free = \false)
        {
            $this->api_manager = $api_manager;
            // API data
            $this->upgrade_url = $upgrade_url;
            $this->plugin_name = $plugin_name;
            $this->product_id = $product_id;
            $this->api_key = $api_key;
            $this->activation_email = $activation_email;
            $this->renew_license_url = $renew_license_url;
            $this->instance = $instance;
            $this->domain = $domain;
            $this->software_version = $software_version;
            $this->text_domain = $text_domain;
            $this->extra = $extra;
            $this->free = $free;
            // Slug should be the same as the plugin/theme directory name
            if (\strpos($this->plugin_name, '.php') !== 0) {
                $this->slug = \dirname($this->plugin_name);
            } else {
                $this->slug = $this->plugin_name;
            }
            /**
             * Flag for plugin or theme updates
             * @access public
             * @since  1.0.0
             *
             * @param string, plugin or theme
             */
            $this->plugin_or_theme = $plugin_or_theme;
            // 'theme' or 'plugin'
            /*********************************************************************
             * The plugin and theme filters should not be active at the same time
             *********************************************************************/
            /**
             * More info:
             * function set_site_transient moved from wp-includes/functions.php
             * to wp-includes/option.php in WordPress 3.4
             *
             * set_site_transient() contains the pre_set_site_transient_{$transient} filter
             * {$transient} is either update_plugins or update_themes
             *
             * Transient data for plugins and themes exist in the Options table:
             * _site_transient_update_themes
             * _site_transient_update_plugins
             */
            // uses the flag above to determine if this is a plugin or a theme update request
            if ($this->plugin_or_theme == 'plugin') {
                /**
                 * Plugin Updates
                 */
                // Check For Plugin Updates
                \add_filter('pre_set_site_transient_update_plugins', [$this, 'update_check']);
                // Check For Plugin Information to display on the update details page
                \add_filter('plugins_api', [$this, 'request'], 10, 3);
            } elseif ($this->plugin_or_theme == 'theme') {
                /**
                 * Theme Updates
                 */
                // Check For Theme Updates
                \add_filter('pre_set_site_transient_update_themes', [$this, 'update_check']);
                // Check For Theme Information to display on the update details page
                \add_filter('themes_api', [$this, 'request'], 10, 3);
            }
        }
        // Upgrade API URL
        private function create_upgrade_api_url($args)
        {
            if ($this->free) {
                $upgrade_url = \add_query_arg('wc-api', 'wpdesk-upgrade-api', $this->upgrade_url);
            } else {
                $upgrade_url = \add_query_arg('wc-api', 'upgrade-api', $this->upgrade_url);
            }
            return $upgrade_url . '&' . \http_build_query($args);
        }
        /**
         * Check for updates against the remote server.
         *
         * @access public
         * @since  1.0.0
         *
         * @param  object $transient
         *
         * @return object $transient
         */
        public function update_check($transient)
        {
            if (empty($transient->checked)) {
                return $transient;
            }
            $args = ['request' => 'pluginupdatecheck', 'slug' => $this->slug, 'plugin_name' => $this->plugin_name, 'version' => $this->software_version, 'product_id' => $this->product_id, 'api_key' => $this->api_key, 'activation_email' => $this->activation_email, 'instance' => $this->instance, 'domain' => $this->domain, 'software_version' => $this->software_version, 'extra' => $this->extra];
            // Check for a plugin update
            $response = $this->plugin_information($args);
            // Displays an admin error message in the WordPress dashboard
            $this->check_response_for_errors($response);
            // Set version variables
            if (isset($response) && \is_object($response) && $response !== \false) {
                // New plugin version from the API
                $new_ver = (string) $response->new_version;
                // Current installed plugin version
                $curr_ver = (string) $this->software_version;
                //$curr_ver = (string)$transient->checked[$this->plugin_name];
            }
            // If there is a new version, modify the transient to reflect an update is available
            if (isset($new_ver) && isset($curr_ver)) {
                if ($response !== \false && \version_compare($new_ver, $curr_ver, '>')) {
                    if ($this->plugin_or_theme == 'plugin') {
                        $transient->response[$this->plugin_name] = $response;
                    } elseif ($this->plugin_or_theme == 'theme') {
                        $transient->response[$this->plugin_name]['new_version'] = $response->new_version;
                        $transient->response[$this->plugin_name]['url'] = $response->url;
                        $transient->response[$this->plugin_name]['package'] = $response->package;
                    }
                }
            }
            return $transient;
        }
        /**
         * Sends and receives data to and from the server API
         *
         * @access public
         * @since  1.0.0
         * @return object|bool $response
         */
        public function plugin_information($args)
        {
            $target_url = \esc_url_raw($this->create_upgrade_api_url($args));
            $target_url = \str_replace('&amp;', '&', $target_url);
            $request = \wp_safe_remote_get($target_url, ['timeout' => 30, 'sslverify' => \false]);
            if (\is_wp_error($request) || \wp_remote_retrieve_response_code($request) != 200) {
                if (\class_exists('BmWoocommerceVendor\\WPDesk_Logger_Factory')) {
                    if (\is_wp_error($request)) {
                        \BmWoocommerceVendor\WPDesk_Logger_Factory::log_wp_error($request, \debug_backtrace(\DEBUG_BACKTRACE_PROVIDE_OBJECT | \DEBUG_BACKTRACE_IGNORE_ARGS));
                    } else {
                        \BmWoocommerceVendor\WPDesk_Logger_Factory::log_message_backtrace('Response is invalid. Response: ' . \json_encode($request), \BmWoocommerceVendor\WPDesk_Logger::ERROR, \debug_backtrace(\DEBUG_BACKTRACE_PROVIDE_OBJECT | \DEBUG_BACKTRACE_IGNORE_ARGS));
                    }
                }
                return \false;
            }
            $raw_response = \wp_remote_retrieve_body($request);
            $response = @\unserialize($raw_response);
            if (\is_object($response)) {
                if (isset($response->sections)) {
                    $response->sections['description'] = \apply_filters('the_content', isset($response->sections['description_base64']) ? \base64_decode($response->sections['description_base64']) : '');
                    if (isset($response->sections['installation'])) {
                        $response->sections['installation'] = \apply_filters('the_content', $response->sections['installation']);
                    }
                    if (isset($response->sections['faq'])) {
                        $response->sections['faq'] = \apply_filters('the_content', $response->sections['faq']);
                    }
                    if (isset($response->sections['screenshots'])) {
                        $response->sections['screenshots'] = \apply_filters('the_content', $response->sections['screenshots']);
                    }
                    if (isset($response->sections['changelog'])) {
                        $response->sections['changelog'] = \apply_filters('the_content', $response->sections['changelog']);
                    }
                    if (isset($response->sections['other_notes'])) {
                        $response->sections['other_notes'] = \apply_filters('the_content', $response->sections['other_notes']);
                    }
                }
                if (isset($response->author)) {
                    $response->author = "<a href='http://www.wpdesk.pl'>{$response->author}</a>";
                }
                unset($response->sections['description_base64']);
                return $response;
            } else {
                if (\class_exists('BmWoocommerceVendor\\WPDesk_Logger_Factory')) {
                    \BmWoocommerceVendor\WPDesk_Logger_Factory::log_message_backtrace('Response is not an object', \BmWoocommerceVendor\WPDesk_Logger::DEBUG, \debug_backtrace(\DEBUG_BACKTRACE_PROVIDE_OBJECT | \DEBUG_BACKTRACE_IGNORE_ARGS));
                } else {
                    \error_log("Unserialize error. Please send this report to support@wpdesk.net. Request: {$request}. Raw Response: {$raw_response}");
                }
                return \false;
            }
        }
        /**
         * Generic request helper.
         *
         * @access public
         * @since  1.0.0
         *
         * @param  array $args
         *
         * @return object $response or boolean false
         */
        public function request($false, $action, $args)
        {
            // Is this a plugin or a theme?
            if ($this->plugin_or_theme == 'plugin') {
                $version = \get_site_transient('update_plugins');
            } elseif ($this->plugin_or_theme == 'theme') {
                $version = \get_site_transient('update_themes');
            }
            // Check if this plugins API is about this plugin
            if (isset($args->slug)) {
                //if ( $args->slug != dirname($this->slug)) {
                if ($args->slug != $this->slug) {
                    return $false;
                }
            } else {
                return $false;
            }
            $args = ['request' => 'plugininformation', 'plugin_name' => $this->plugin_name, 'slug' => $this->slug, 'version' => $this->software_version, 'product_id' => $this->product_id, 'api_key' => $this->api_key, 'activation_email' => $this->activation_email, 'instance' => $this->instance, 'domain' => $this->domain, 'software_version' => $this->software_version, 'extra' => $this->extra];
            $response = $this->plugin_information($args);
            if ($response) {
                $response->slug = $this->slug;
                $response->product_id = $this->product_id;
                // If everything is okay return the $response
                if (isset($response) && \is_object($response) && $response !== \false) {
                    return $response;
                }
            } else {
                return \false;
            }
        }
        /**
         * Displays an admin error message in the WordPress dashboard
         *
         * @param  array $response
         *
         * @return string
         */
        public function check_response_for_errors($response)
        {
            if (!empty($response)) {
                if (isset($response->errors['no_key']) && $response->errors['no_key'] == 'no_key' && isset($response->errors['no_subscription']) && $response->errors['no_subscription'] == 'no_subscription') {
                    \add_action('admin_notices', [$this, 'no_key_error_notice']);
                    \add_action('admin_notices', [$this, 'no_subscription_error_notice']);
                } elseif (isset($response->errors['exp_license']) && $response->errors['exp_license'] == 'exp_license') {
                    \add_action('admin_notices', [$this, 'expired_license_error_notice']);
                } elseif (isset($response->errors['hold_subscription']) && $response->errors['hold_subscription'] == 'hold_subscription') {
                    \add_action('admin_notices', [$this, 'on_hold_subscription_error_notice']);
                } elseif (isset($response->errors['cancelled_subscription']) && $response->errors['cancelled_subscription'] == 'cancelled_subscription') {
                    \add_action('admin_notices', [$this, 'cancelled_subscription_error_notice']);
                } elseif (isset($response->errors['exp_subscription']) && $response->errors['exp_subscription'] == 'exp_subscription') {
                    \add_action('admin_notices', [$this, 'expired_subscription_error_notice']);
                } elseif (isset($response->errors['suspended_subscription']) && $response->errors['suspended_subscription'] == 'suspended_subscription') {
                    \add_action('admin_notices', [$this, 'suspended_subscription_error_notice']);
                } elseif (isset($response->errors['pending_subscription']) && $response->errors['pending_subscription'] == 'pending_subscription') {
                    \add_action('admin_notices', [$this, 'pending_subscription_error_notice']);
                } elseif (isset($response->errors['trash_subscription']) && $response->errors['trash_subscription'] == 'trash_subscription') {
                    \add_action('admin_notices', [$this, 'trash_subscription_error_notice']);
                } elseif (isset($response->errors['no_subscription']) && $response->errors['no_subscription'] == 'no_subscription') {
                    \add_action('admin_notices', [$this, 'no_subscription_error_notice']);
                } elseif (isset($response->errors['no_activation']) && $response->errors['no_activation'] == 'no_activation') {
                    \add_action('admin_notices', [$this, 'no_activation_error_notice']);
                } elseif (isset($response->errors['no_key']) && $response->errors['no_key'] == 'no_key') {
                    \add_action('admin_notices', [$this, 'no_key_error_notice']);
                } elseif (isset($response->errors['download_revoked']) && $response->errors['download_revoked'] == 'download_revoked') {
                    \add_action('admin_notices', [$this, 'download_revoked_error_notice']);
                } elseif (isset($response->errors['switched_subscription']) && $response->errors['switched_subscription'] == 'switched_subscription') {
                    \add_action('admin_notices', [$this, 'switched_subscription_error_notice']);
                }
            }
        }
        /**
         * Display license expired error notice
         *
         * @param  string $message
         *
         * @return void
         */
        public function expired_license_error_notice($message)
        {
            echo \sprintf('<div id="message" class="error"><p>' . \__('The API key for %s has expired. You can reactivate or purchase a API key from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain) . '</p></div>', $this->product_id, $this->renew_license_url);
        }
        /**
         * Display subscription on-hold error notice
         *
         * @param  string $message
         *
         * @return void
         */
        public function on_hold_subscription_error_notice($message)
        {
            echo \sprintf('<div id="message" class="error"><p>' . \__('The subscription for %s is on-hold. You can reactivate the subscription from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain) . '</p></div>', $this->product_id, $this->renew_license_url);
        }
        /**
         * Display subscription cancelled error notice
         *
         * @param  string $message
         *
         * @return void
         */
        public function cancelled_subscription_error_notice($message)
        {
            echo \sprintf('<div id="message" class="error"><p>' . \__('The subscription for %s has been cancelled. You can renew the subscription from your account <a href="%s" target="_blank">dashboard</a>. A new API key will be emailed to you after your order has been completed.', $this->text_domain) . '</p></div>', $this->product_id, $this->renew_license_url);
        }
        /**
         * Display subscription expired error notice
         *
         * @param  string $message
         *
         * @return void
         */
        public function expired_subscription_error_notice($message)
        {
            echo \sprintf('<div id="message" class="error"><p>' . \__('The subscription for %s has expired. You can reactivate the subscription from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain) . '</p></div>', $this->product_id, $this->renew_license_url);
        }
        /**
         * Display subscription expired error notice
         *
         * @param  string $message
         *
         * @return void
         */
        public function suspended_subscription_error_notice($message)
        {
            echo \sprintf('<div id="message" class="error"><p>' . \__('The subscription for %s has been suspended. You can reactivate the subscription from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain) . '</p></div>', $this->product_id, $this->renew_license_url);
        }
        /**
         * Display subscription expired error notice
         *
         * @param  string $message
         *
         * @return void
         */
        public function pending_subscription_error_notice($message)
        {
            echo \sprintf('<div id="message" class="error"><p>' . \__('The subscription for %s is still pending. You can check on the status of the subscription from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain) . '</p></div>', $this->product_id, $this->renew_license_url);
        }
        /**
         * Display subscription expired error notice
         *
         * @param  string $message
         *
         * @return void
         */
        public function trash_subscription_error_notice($message)
        {
            echo \sprintf('<div id="message" class="error"><p>' . \__('The subscription for %s has been placed in the trash and will be deleted soon. You can purchase a new subscription from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain) . '</p></div>', $this->product_id, $this->renew_license_url);
        }
        /**
         * Display subscription expired error notice
         *
         * @param  string $message
         *
         * @return void
         */
        public function no_subscription_error_notice($message)
        {
            echo \sprintf('<div id="message" class="error"><p>' . \__('A subscription for %s could not be found. You can purchase a subscription from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain) . '</p></div>', $this->product_id, $this->renew_license_url);
        }
        /**
         * Display missing key error notice
         *
         * @param  string $message
         *
         * @return void
         */
        public function no_key_error_notice($message)
        {
            echo \sprintf('<div id="message" class="error"><p>' . \__('A API key for %s could not be found. Maybe you forgot to enter a API key when setting up %s, or the key was deactivated in your account. You can reactivate or purchase a subscription key from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain) . '</p></div>', $this->product_id, $this->product_id, $this->renew_license_url);
        }
        /**
         * Display missing download permission revoked error notice
         *
         * @param  string $message
         *
         * @return void
         */
        public function download_revoked_error_notice($message)
        {
            echo \sprintf('<div id="message" class="error"><p>' . \__('Download permission for %s has been revoked possibly due to a API key or subscription expiring. You can reactivate or purchase a API key from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain) . '</p></div>', $this->product_id, $this->renew_license_url);
        }
        /**
         * Display no activation error notice
         *
         * @param  string $message
         *
         * @return void
         */
        public function no_activation_error_notice($message)
        {
            echo \sprintf('<div id="message" class="error"><p>' . \__('%s has not been activated. Go to the settings page and enter the API key and subscription email to activate %s.', $this->text_domain) . '</p></div>', $this->product_id, $this->product_id);
        }
        /**
         * Display switched activation error notice
         *
         * @param  string $message
         *
         * @return void
         */
        public function switched_subscription_error_notice($message)
        {
            echo \sprintf('<div id="message" class="error"><p>' . \__('You changed the subscription for %s, so you will need to enter your new API Key in the settings page. The API Key should have arrived in your email inbox, if not you can get it by logging into your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain) . '</p></div>', $this->product_id, $this->renew_license_url);
        }
    }
    // class WPDesk_Update_API_Check
}
// if (!class_exists('WPDesk_Update_API_Check')) {
