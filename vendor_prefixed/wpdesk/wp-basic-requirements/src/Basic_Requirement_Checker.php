<?php

namespace BmWoocommerceVendor;

if (!\interface_exists('BmWoocommerceVendor\\WPDesk_Requirement_Checker')) {
    require_once 'Requirement_Checker.php';
}
if (!\class_exists('BmWoocommerceVendor\\WPDesk_Basic_Requirement_Checker')) {
    /**
     * Checks requirements for plugin
     * have to be compatible with PHP 5.3.x
     */
    class WPDesk_Basic_Requirement_Checker implements \BmWoocommerceVendor\WPDesk_Requirement_Checker
    {
        const EXTENSION_NAME_OPENSSL = 'openssl';
        const HOOK_ADMIN_NOTICES_ACTION = 'admin_notices';
        const HOOK_PLUGIN_DEACTIVATED_ACTION = 'deactivated_plugin';
        const HOOK_PLUGIN_ACTIVATED_ACTION = 'activated_plugin';
        const PLUGIN_INFO_KEY_NICE_NAME = 'nice_name';
        const PLUGIN_INFO_KEY_NAME = 'name';
        const PLUGIN_INFO_VERSION = 'version';
        const PLUGIN_INFO_FAKE_REQUIRED_MINIMUM_VERSION = '0.0';
        const PLUGIN_INFO_APPEND_PLUGIN_DATA = 'required_version';
        const PLUGIN_INFO_TRANSIENT_NAME = 'require_plugins_data';
        const PLUGIN_INFO_TRANSIENT_EXPIRATION_TIME = 16;
        /** @var string */
        protected $plugin_name;
        /** @var string */
        private $plugin_file;
        /** @var string */
        private $min_php_version;
        /** @var string */
        private $min_wp_version;
        /** @var string|null */
        private $min_wc_version = null;
        /** @var int|null */
        private $min_openssl_version = null;
        /** @var array */
        protected $plugin_require;
        /** @var bool */
        protected $should_check_plugin_versions = \false;
        /** @var array */
        private $module_require;
        /** @var array */
        private $setting_require;
        /** @var array */
        protected $notices;
        /** @var @string */
        private $text_domain;
        /**
         * @param string $plugin_file
         * @param string $plugin_name
         * @param string $text_domain
         * @param string $php_version
         * @param string $wp_version
         */
        public function __construct($plugin_file, $plugin_name, $text_domain, $php_version, $wp_version)
        {
            $this->plugin_file = $plugin_file;
            $this->plugin_name = $plugin_name;
            $this->text_domain = $text_domain;
            $this->set_min_php_require($php_version);
            $this->set_min_wp_require($wp_version);
            $this->plugin_require = array();
            $this->module_require = array();
            $this->setting_require = array();
            $this->notices = array();
        }
        /**
         * @param string $version
         *
         * @return $this
         */
        public function set_min_php_require($version)
        {
            $this->min_php_version = $version;
            return $this;
        }
        /**
         * @param string $version
         *
         * @return $this
         */
        public function set_min_wp_require($version)
        {
            $this->min_wp_version = $version;
            return $this;
        }
        /**
         * @param string $version
         *
         * @return $this
         */
        public function set_min_wc_require($version)
        {
            $this->min_wc_version = $version;
            return $this;
        }
        /**
         * @param $version
         *
         * @return $this
         */
        public function set_min_openssl_require($version)
        {
            $this->min_openssl_version = $version;
            return $this;
        }
        /**
         * @param string $plugin_name Name in wp format dir/file.php
         * @param string $nice_plugin_name Nice plugin name for better looks in notice
         * @param string $plugin_require_version required plugin minimum version
         *
         * @return $this
         */
        public function add_plugin_require($plugin_name, $nice_plugin_name = null, $plugin_require_version = null)
        {
            if ($plugin_require_version) {
                $this->should_check_plugin_versions = \true;
            }
            $this->plugin_require[$plugin_name] = array(self::PLUGIN_INFO_KEY_NAME => $plugin_name, self::PLUGIN_INFO_KEY_NICE_NAME => $nice_plugin_name === null ? $plugin_name : $nice_plugin_name, self::PLUGIN_INFO_VERSION => $plugin_require_version === null ? self::PLUGIN_INFO_FAKE_REQUIRED_MINIMUM_VERSION : $plugin_require_version);
            return $this;
        }
        /**
         * Add plugin to require list. Plugin is from repository so we can ask for installation.
         *
         * @param string $plugin_name Name in wp format dir/file.php
         * @param string $version Required version of the plugin.
         * @param string $nice_plugin_name Nice plugin name for better looks in notice
         *
         * @return $this
         */
        public function add_plugin_repository_require($plugin_name, $version, $nice_plugin_name = null)
        {
            $this->plugin_require[$plugin_name] = array(self::PLUGIN_INFO_KEY_NAME => $plugin_name, self::PLUGIN_INFO_VERSION => $version, 'repository_url' => 'http://downloads.wordpress.org/plugin/' . \dirname($plugin_name) . '.latest-stable.zip', self::PLUGIN_INFO_KEY_NICE_NAME => $nice_plugin_name === null ? $plugin_name : $nice_plugin_name);
            return $this;
        }
        /**
         * @param string $module_name
         * @param string $nice_name Nice module name for better looks in notice
         *
         * @return $this
         */
        public function add_php_module_require($module_name, $nice_name = null)
        {
            if ($nice_name === null) {
                $this->module_require[$module_name] = $module_name;
            } else {
                $this->module_require[$module_name] = $nice_name;
            }
            return $this;
        }
        /**
         * @param string $setting
         * @param mixed $value
         *
         * @return $this
         */
        public function add_php_setting_require($setting, $value)
        {
            $this->setting_require[$setting] = $value;
            return $this;
        }
        /**
         * Returns true if are requirements are met.
         *
         * @return bool
         */
        public function are_requirements_met()
        {
            $this->notices = $this->prepare_requirement_notices();
            return \count($this->notices) === 0;
        }
        /**
         * @return array
         */
        private function prepare_requirement_notices()
        {
            $notices = array();
            if (!self::is_php_at_least($this->min_php_version)) {
                $notices[] = $this->prepare_notice_message(\sprintf(\__('The &#8220;%s&#8221; plugin cannot run on PHP versions older than %s. Please contact your host and ask them to upgrade.', $this->get_text_domain()), \esc_html($this->plugin_name), $this->min_php_version));
            }
            if (!self::is_wp_at_least($this->min_wp_version)) {
                $notices[] = $this->prepare_notice_message(\sprintf(\__('The &#8220;%s&#8221; plugin cannot run on WordPress versions older than %s. Please update WordPress.', $this->get_text_domain()), \esc_html($this->plugin_name), $this->min_wp_version));
            }
            if ($this->min_wc_version !== null && $this->can_check_plugin_version() && !self::is_wc_at_least($this->min_wc_version)) {
                $notices[] = $this->prepare_notice_message(\sprintf(\__('The &#8220;%s&#8221; plugin cannot run on WooCommerce versions older than %s. Please update WooCommerce.', $this->get_text_domain()), \esc_html($this->plugin_name), $this->min_wc_version));
            }
            if ($this->min_openssl_version !== null && !self::is_open_ssl_at_least($this->min_openssl_version)) {
                $notices[] = $this->prepare_notice_message(\sprintf(\__('The &#8220;%s&#8221; plugin cannot run without OpenSSL module version at least %s. Please update OpenSSL module.', $this->get_text_domain()), \esc_html($this->plugin_name), '0x' . \dechex($this->min_openssl_version)));
            }
            $notices = $this->append_plugin_require_notices($notices);
            $notices = $this->append_module_require_notices($notices);
            $notices = $this->append_settings_require_notices($notices);
            if ($this->should_check_plugin_versions) {
                $notices = $this->check_minimum_require_plugins_version_and_append_notices($notices);
            }
            return $notices;
        }
        /**
         * @param $min_version
         *
         * @return mixed
         */
        public static function is_php_at_least($min_version)
        {
            return \version_compare(\PHP_VERSION, $min_version, '>=');
        }
        /**
         * Prepares message in html format
         *
         * @param string $message
         *
         * @return string
         */
        protected function prepare_notice_message($message)
        {
            return '<div class="error"><p>' . $message . '</p></div>';
        }
        public function get_text_domain()
        {
            return $this->text_domain;
        }
        /**
         * @param string $min_version
         *
         * @return bool
         */
        public static function is_wp_at_least($min_version)
        {
            return \version_compare(\get_bloginfo('version'), $min_version, '>=');
        }
        /**
         * Are plugins loaded so we can check the version
         *
         * @return bool
         */
        private function can_check_plugin_version()
        {
            return \did_action('plugins_loaded') > 0;
        }
        /**
         * Checks if plugin is active and have designated version. Needs to be enabled in deferred way.
         *
         * @param string $min_version
         *
         * @return bool
         */
        public static function is_wc_at_least($min_version)
        {
            return \defined('WC_VERSION') && \version_compare(\WC_VERSION, $min_version, '>=');
        }
        /**
         * Checks if ssl version is valid
         *
         * @param int $required_version Version in hex. Version 9.6 is 0x000906000
         *
         * @return bool
         * @see https://www.openssl.org/docs/man1.1.0/crypto/OPENSSL_VERSION_NUMBER.html
         *
         */
        public static function is_open_ssl_at_least($required_version)
        {
            return \defined('OPENSSL_VERSION_NUMBER') && \OPENSSL_VERSION_NUMBER > (int) $required_version;
        }
        /**
         * @param $notices array
         *
         * @return array
         */
        private function check_minimum_require_plugins_version_and_append_notices($notices)
        {
            $required_plugins = $this->retrieve_required_plugins_data();
            if (\count($required_plugins) > 0) {
                foreach ($required_plugins as $plugin) {
                    if (\version_compare($plugin['Version'], $plugin[self::PLUGIN_INFO_APPEND_PLUGIN_DATA], '<')) {
                        $notices[] = $this->prepare_notice_message(\sprintf(\__('The &#8220;%1$s&#8221; plugin requires at least %2$s version of %3$s to work correctly. Please update it to its latest release.', $this->get_text_domain()), \esc_html($this->plugin_name), $plugin[self::PLUGIN_INFO_APPEND_PLUGIN_DATA], $plugin['Name']));
                    }
                }
            }
            return $notices;
        }
        /**
         * Check the plugins directory and retrieve all plugin files with plugin data.
         *
         * @return array In format [ 'plugindir/pluginfile.php' => ['Name' => 'Plugin Name', 'Version' => '1.0.1', ...],  ]
         */
        private static function retrieve_plugins_data_in_transient()
        {
            static $never_executed = \true;
            if ($never_executed) {
                $never_executed = \false;
                /** Required when WC starts later and these data should be in cache */
                \add_filter('extra_plugin_headers', function ($headers = array()) {
                    $headers[] = 'WC tested up to';
                    $headers[] = 'WC requires at least';
                    $headers[] = 'Woo';
                    return \array_unique($headers);
                });
            }
            $plugins = \get_transient(self::PLUGIN_INFO_TRANSIENT_NAME);
            if ($plugins === \false) {
                if (!\function_exists('get_plugins')) {
                    require_once \ABSPATH . '/wp-admin/includes/plugin.php';
                }
                $plugins = \function_exists('get_plugins') ? \get_plugins() : array();
                \set_transient(self::PLUGIN_INFO_TRANSIENT_NAME, $plugins, self::PLUGIN_INFO_TRANSIENT_EXPIRATION_TIME);
            }
            return $plugins;
        }
        /**
         * Check the plugins directory and retrieve all required plugin files with plugin data.
         *
         * @return array In format [ 'plugindir/pluginfile.php' => ['Name' => 'Plugin Name', 'Version' => '1.0.1', 'required_version' => '1.0.2']...  ]
         */
        private function retrieve_required_plugins_data()
        {
            $require_plugins = array();
            $plugins = self::retrieve_plugins_data_in_transient();
            if (\is_array($plugins)) {
                if (\count($plugins) > 0) {
                    if (!empty($this->plugin_require)) {
                        foreach ($this->plugin_require as $plugin) {
                            $plugin_file_name = $plugin[self::PLUGIN_INFO_KEY_NAME];
                            $plugin_version = $plugin[self::PLUGIN_INFO_VERSION];
                            if (self::is_wp_plugin_active($plugin_file_name)) {
                                $require_plugins[$plugin_file_name] = $plugins[$plugin_file_name];
                                $require_plugins[$plugin_file_name][self::PLUGIN_INFO_APPEND_PLUGIN_DATA] = $plugin_version;
                            }
                        }
                    }
                }
            }
            return $require_plugins;
        }
        /**
         * @param array $notices
         *
         * @return array
         */
        private function append_plugin_require_notices($notices)
        {
            if (\count($this->plugin_require) > 0) {
                foreach ($this->plugin_require as $plugin_name => $plugin_info) {
                    $notice = null;
                    if (isset($plugin_info['repository_url'])) {
                        $notice = $this->prepare_plugin_repository_require_notice($plugin_info);
                    } elseif (!self::is_wp_plugin_active($plugin_name)) {
                        $notice = $this->prepare_notice_message(\sprintf(\__('The &#8220;%s&#8221; plugin cannot run without %s active. Please install and activate %s plugin.', $this->get_text_domain()), \esc_html($this->plugin_name), \esc_html(\basename($plugin_info[self::PLUGIN_INFO_KEY_NICE_NAME])), \esc_html(\basename($plugin_info[self::PLUGIN_INFO_KEY_NICE_NAME]))));
                    }
                    if ($notice !== null) {
                        $notices[] = $notice;
                    }
                }
            }
            return $notices;
        }
        /**
         * Prepares WP install url and injects info about plugin to the WP update engine.
         *
         * @param array $plugin_info
         *
         * @return string
         */
        private function prepare_plugin_repository_install_url($plugin_info)
        {
            $slug = \basename($plugin_info[self::PLUGIN_INFO_KEY_NAME]);
            $install_url = \self_admin_url('update.php?action=install-plugin&plugin=' . $slug);
            if (\function_exists('wp_nonce_url') && \function_exists('wp_create_nonce')) {
                $install_url = \wp_nonce_url($install_url, 'install-plugin_' . $slug);
            }
            \add_filter('plugins_api', function ($api, $action, $args) use($plugin_info, $slug) {
                if ('plugin_information' !== $action || \false !== $api || !isset($args->slug) || $slug !== $args->slug) {
                    return $api;
                }
                $api = new \stdClass();
                $api->name = $plugin_info['nice_name'];
                // self in closures requires 5.4
                $api->version = '';
                $api->download_link = \esc_url($plugin_info['repository_url']);
                // self in closures requires 5.4
                return $api;
            }, 10, 3);
            return $install_url;
        }
        /**
         * @param array $plugin_info Internal required plugin info data.
         *
         * @return string|null Return null if no notice is needed.
         */
        private function prepare_plugin_repository_require_notice($plugin_info)
        {
            $name = $plugin_info[self::PLUGIN_INFO_KEY_NAME];
            $nice_name = $plugin_info[self::PLUGIN_INFO_KEY_NICE_NAME];
            if (!self::is_wp_plugin_active($name)) {
                if (!self::is_wp_plugin_installed($name)) {
                    $install_url = $this->prepare_plugin_repository_install_url($plugin_info);
                    return $this->prepare_notice_message(\sprintf(\wp_kses(\__('The &#8220;%s&#8221; plugin requires free %s plugin. <a href="%s">Install %s →</a>', $this->get_text_domain()), array('a' => array('href' => array()))), $this->plugin_name, $nice_name, \esc_url($install_url), $nice_name));
                }
                $activate_url = 'plugins.php?action=activate&plugin=' . \urlencode($plugin_info[self::PLUGIN_INFO_KEY_NAME]) . '&plugin_status=all&paged=1&s';
                if (\function_exists('wp_create_nonce')) {
                    $activate_url .= '&_wpnonce=' . \urlencode(\wp_create_nonce('activate-plugin_' . $name));
                }
                return $this->prepare_notice_message(\sprintf(\wp_kses(\__('The &#8220;%s&#8221; plugin requires activating %s plugin. <a href="%s">Activate %s →</a>', $this->get_text_domain()), array('a' => array('href' => array()))), $this->plugin_name, $nice_name, \esc_url(\admin_url($activate_url)), $nice_name));
            }
            return null;
        }
        /**
         * Checks if plugin is active. Needs to be used in deferred way.
         *
         * @param string $plugin_file
         *
         * @return bool
         */
        public static function is_wp_plugin_active($plugin_file)
        {
            $active_plugins = (array) \get_option('active_plugins', array());
            if (\is_multisite()) {
                $active_plugins = \array_merge($active_plugins, \get_site_option('active_sitewide_plugins', array()));
            }
            return \in_array($plugin_file, $active_plugins) || \array_key_exists($plugin_file, $active_plugins);
        }
        /**
         * Checks if plugin is installed. Needs to be enabled in deferred way.
         *
         * @param string $plugin_file
         *
         * @return bool
         */
        public static function is_wp_plugin_installed($plugin_file)
        {
            $plugins_data = self::retrieve_plugins_data_in_transient();
            return \array_key_exists($plugin_file, (array) $plugins_data);
        }
        /**
         * @param array $notices
         *
         * @return array
         */
        private function append_module_require_notices($notices)
        {
            if (\count($this->module_require) > 0) {
                foreach ($this->module_require as $module_name => $nice_module_name) {
                    if (!self::is_module_active($module_name)) {
                        $notices[] = $this->prepare_notice_message(\sprintf(\__('The &#8220;%s&#8221; plugin cannot run without %s php module installed. Please contact your host and ask them to install %s.', $this->get_text_domain()), \esc_html($this->plugin_name), \esc_html(\basename($nice_module_name)), \esc_html(\basename($nice_module_name))));
                    }
                }
            }
            return $notices;
        }
        /**
         * @param string $name
         *
         * @return bool
         */
        public static function is_module_active($name)
        {
            return \extension_loaded($name);
        }
        /**
         * @param array $notices
         *
         * @return array
         */
        private function append_settings_require_notices($notices)
        {
            if (\count($this->setting_require) > 0) {
                foreach ($this->setting_require as $setting => $value) {
                    if (!self::is_setting_set($setting, $value)) {
                        $notices[] = $this->prepare_notice_message(\sprintf(\__('The &#8220;%s&#8221; plugin cannot run without %s php setting set to %s. Please contact your host and ask them to set %s.', $this->get_text_domain()), \esc_html($this->plugin_name), \esc_html(\basename($setting)), \esc_html(\basename($value)), \esc_html(\basename($setting))));
                    }
                }
            }
            return $notices;
        }
        /**
         * @param string $name
         * @param mixed $value
         *
         * @return bool
         */
        public static function is_setting_set($name, $value)
        {
            return \ini_get($name) === (string) $value;
        }
        /**
         * @return void
         *
         * @deprecated use render_notices or disable_plugin
         */
        public function disable_plugin_render_notice()
        {
            \add_action(self::HOOK_ADMIN_NOTICES_ACTION, array($this, 'handle_render_notices_action'));
        }
        /**
         * Renders requirement notices in admin panel
         *
         * @return void
         */
        public function render_notices()
        {
            \add_action(self::HOOK_ADMIN_NOTICES_ACTION, array($this, 'handle_render_notices_action'));
        }
        /**
         * Renders requirement notices in admin panel
         *
         * @return void
         */
        public function disable_plugin()
        {
            \add_action(self::HOOK_ADMIN_NOTICES_ACTION, array($this, 'handle_deactivate_action'));
        }
        /**
         * @return void
         * @internal Do not use as public. Public only for wp action.
         *
         */
        public function handle_deactivate_action()
        {
            if (isset($this->plugin_file)) {
                \deactivate_plugins(\plugin_basename($this->plugin_file));
                \delete_transient(self::PLUGIN_INFO_TRANSIENT_NAME);
            }
        }
        /**
         * Triggers the transient delete after plugin deactivated
         *
         * @return void
         */
        public function transient_delete_on_plugin_version_changed()
        {
            \add_action(self::HOOK_PLUGIN_DEACTIVATED_ACTION, array($this, 'handle_transient_delete_action'));
            \add_action(self::HOOK_PLUGIN_ACTIVATED_ACTION, array($this, 'handle_transient_delete_action'));
        }
        /**
         * Handles the transient delete
         *
         * @return void
         */
        public function handle_transient_delete_action()
        {
            \delete_transient(self::PLUGIN_INFO_TRANSIENT_NAME);
        }
        /**
         * Should be called as WordPress action
         *
         * @return void
         * @internal Do not use as public. Public only for wp action.
         *
         */
        public function handle_render_notices_action()
        {
            foreach ($this->notices as $notice) {
                echo $notice;
            }
        }
    }
}
