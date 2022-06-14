<?php

namespace BmWoocommerceVendor;

/**
 * Admin Menu Class
 *
 * @package Update API Manager/Admin
 * @author Todd Lahman LLC, WPDesk
 * @copyright   Copyright (c) Todd Lahman LLC
 * @since 1.3
 *
 */
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
if (!\class_exists('BmWoocommerceVendor\\WPDesk_API_MENU')) {
    class WPDesk_API_MENU
    {
        private $api_manager;
        // Load admin menu
        public function __construct($api_manager)
        {
            $this->api_manager = $api_manager;
        }
        // Draw option page
        public function config_page()
        {
            $settings_tabs = [$this->api_manager->activation_tab_key => \__($this->api_manager->menu_tab_activation_title, $this->api_manager->text_domain), $this->api_manager->deactivation_tab_key => \__($this->api_manager->menu_tab_deactivation_title, $this->api_manager->text_domain)];
            $current_tab = isset($_GET['tab']) ? \sanitize_key($_GET['tab']) : $this->api_manager->activation_tab_key;
            $tab = isset($_GET['tab']) ? \sanitize_key($_GET['tab']) : $this->api_manager->activation_tab_key;
            ?>
            <div class='wrap'>
				<?php 
            /* screen_icon(); */
            ?>
                <h2><?php 
            \_e($this->api_manager->settings_title, $this->api_manager->text_domain);
            ?></h2>
				<?php 
            \settings_errors();
            ?>
                <h2 class="nav-tab-wrapper">
					<?php 
            foreach ($settings_tabs as $tab_page => $tab_name) {
                $active_tab = $current_tab == $tab_page ? 'nav-tab-active' : '';
                echo '<a class="nav-tab ' . $active_tab . '" href="?page=' . $this->api_manager->activation_tab_key . '&tab=' . $tab_page . '">' . $tab_name . '</a>';
            }
            ?>
                </h2>
                <form action='options.php' method='post'>
                    <div class="main">
						<?php 
            if ($tab == $this->api_manager->activation_tab_key) {
                \settings_fields($this->api_manager->data_key);
                \do_settings_sections($this->api_manager->activation_tab_key);
                //if (get_option( $this->api_manager->activated_key, '0' ) != 'Activated') {
                \submit_button(\__('Save Changes', $this->api_manager->text_domain));
                //}
            } else {
                \settings_fields($this->api_manager->deactivate_checkbox);
                \do_settings_sections($this->api_manager->deactivation_tab_key);
                \submit_button(\__('Save Changes', $this->api_manager->text_domain));
            }
            ?>
                    </div>
                </form>
            </div>
			<?php 
        }
        // Register settings
        public function load_settings()
        {
            \register_setting($this->api_manager->data_key, $this->api_manager->data_key, [$this, 'validate_options']);
            // API Key
            \add_settings_section($this->api_manager->api_key, \__('API Key Activation', $this->api_manager->text_domain), [$this, 'wc_am_api_key_text'], $this->api_manager->activation_tab_key);
            \add_settings_field('status', \__('API Key Status', $this->api_manager->text_domain), [$this, 'wc_am_api_key_status'], $this->api_manager->activation_tab_key, $this->api_manager->api_key);
            \add_settings_field($this->api_manager->api_key, \__('API Subscription Key', $this->api_manager->text_domain), [$this, 'wc_am_api_key_field'], $this->api_manager->activation_tab_key, $this->api_manager->api_key);
            \add_settings_field($this->api_manager->activation_email, \__('API Subscription email', $this->api_manager->text_domain), [$this, 'wc_am_api_email_field'], $this->api_manager->activation_tab_key, $this->api_manager->api_key);
            // Activation settings
            \register_setting($this->api_manager->deactivate_checkbox, $this->api_manager->deactivate_checkbox, [$this, 'wc_am_license_key_deactivation']);
            \add_settings_section('deactivate_button', \__('API Key Deactivation', $this->api_manager->text_domain), [$this, 'wc_am_deactivate_text'], $this->api_manager->deactivation_tab_key);
            \add_settings_field('deactivate_button', \__('Deactivate API Key', $this->api_manager->text_domain), [$this, 'wc_am_deactivate_textarea'], $this->api_manager->deactivation_tab_key, 'deactivate_button');
        }
        // Provides text for api key section
        public function wc_am_api_key_text()
        {
            //
        }
        // Returns the API License Key status from the WooCommerce API Manager on the server
        public function wc_am_api_key_status()
        {
            $license_status = $this->license_key_status();
            $license_status_check = !empty($license_status['status_check']) && $license_status['status_check'] == 'active' ? 'Activated' : 'Deactivated';
            if (!empty($license_status_check)) {
                echo $license_status_check;
            }
        }
        // Returns API License text field
        public function wc_am_api_key_field()
        {
            echo "<input id='api_key' name='" . $this->api_manager->data_key . "[" . $this->api_manager->api_key . "]' size='55' type='text' value='" . $this->api_manager->options[$this->api_manager->api_key] . "' />";
            if ($this->api_manager->options[$this->api_manager->api_key]) {
                echo "<span class='icon-pos'><img src='" . \plugins_url("wpdesk-helper/assets/images/complete.png") . "' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
            } else {
                echo "<span class='icon-pos'><img src='" . \plugins_url("wpdesk-helper/assets/images/warn.png") . "' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
            }
        }
        // Returns API License email text field
        public function wc_am_api_email_field()
        {
            echo "<input id='activation_email' name='" . $this->api_manager->data_key . "[" . $this->api_manager->activation_email . "]' size='55' type='text' value='" . $this->api_manager->options[$this->api_manager->activation_email] . "' />";
            if ($this->api_manager->options[$this->api_manager->activation_email]) {
                echo "<span class='icon-pos'><img src='" . \plugins_url("wpdesk-helper/assets/images/complete.png") . "' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
            } else {
                echo "<span class='icon-pos'><img src='" . \plugins_url("wpdesk-helper/assets/images/warn.png") . "' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
            }
        }
        // Sanitizes and validates all input and output for Dashboard
        public function validate_options($input)
        {
            // Load existing options, validate, and update with changes from input before returning
            $options = $this->api_manager->options;
            $options[$this->api_manager->api_key] = \trim($input[$this->api_manager->api_key]);
            $options[$this->api_manager->activation_email] = \trim($input[$this->api_manager->activation_email]);
            /**
             * Plugin Activation
             */
            $api_email = \trim($input[$this->api_manager->activation_email]);
            $api_key = \trim($input[$this->api_manager->api_key]);
            $activation_status = \get_option($this->api_manager->activated_key);
            $checkbox_status = \get_option($this->api_manager->deactivate_checkbox);
            $current_api_key = $this->api_manager->options[$this->api_manager->api_key];
            // Should match the settings_fields() value
            if ($_REQUEST['option_page'] != $this->api_manager->deactivate_checkbox) {
                \update_option($this->api_manager->options[$this->api_manager->activated_key], 'Deactivated');
                if ($activation_status == 'Deactivated' || $activation_status == '' || $api_key == '' || $api_email == '' || $checkbox_status == 'on' || $current_api_key != $api_key) {
                    /**
                     * If this is a new key, and an existing key already exists in the database,
                     * deactivate the existing key before activating the new key.
                     */
                    if ($current_api_key != $api_key) {
                        $this->replace_license_key($current_api_key);
                    }
                    $args = ['email' => $api_email, 'licence_key' => $api_key];
                    $activate_results = \json_decode($this->api_manager->key()->activate($args), \true);
                    if ($activate_results['activated'] === \true) {
                        \add_settings_error('activate_text', 'activate_msg', \__('Plugin activated. ', $this->api_manager->text_domain) . "{$activate_results['message']}.", 'updated');
                        \update_option($this->api_manager->activated_key, 'Activated');
                        \update_option($this->api_manager->deactivate_checkbox, 'off');
                    }
                    if ($activate_results == \false) {
                        \add_settings_error('api_key_check_text', 'api_key_check_error', \__('Connection failed to the API Key server. Try again later.', $this->api_manager->text_domain), 'error');
                        $options[$this->api_manager->api_key] = '';
                        $options[$this->api_manager->activation_email] = '';
                        \update_option($this->api_manager->options[$this->api_manager->activated_key], 'Deactivated');
                    }
                    if (isset($activate_results['code'])) {
                        switch ($activate_results['code']) {
                            case '100':
                                \add_settings_error('api_email_text', 'api_email_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error');
                                $options[$this->api_manager->activation_email] = '';
                                $options[$this->api_manager->api_key] = '';
                                \update_option($this->api_manager->options[$this->api_manager->activated_key], 'Deactivated');
                                break;
                            case '101':
                                \add_settings_error('api_key_text', 'api_key_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error');
                                $options[$this->api_manager->api_key] = '';
                                $options[$this->api_manager->activation_email] = '';
                                \update_option($this->api_manager->options[$this->api_manager->activated_key], 'Deactivated');
                                break;
                            case '102':
                                \add_settings_error('api_key_purchase_incomplete_text', 'api_key_purchase_incomplete_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error');
                                $options[$this->api_manager->api_key] = '';
                                $options[$this->api_manager->activation_email] = '';
                                \update_option($this->api_manager->options[$this->api_manager->activated_key], 'Deactivated');
                                break;
                            case '103':
                                \add_settings_error('api_key_exceeded_text', 'api_key_exceeded_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error');
                                $options[$this->api_manager->api_key] = '';
                                $options[$this->api_manager->activation_email] = '';
                                \update_option($this->api_manager->options[$this->api_manager->activated_key], 'Deactivated');
                                break;
                            case '104':
                                \add_settings_error('api_key_not_activated_text', 'api_key_not_activated_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error');
                                $options[$this->api_manager->api_key] = '';
                                $options[$this->api_manager->activation_email] = '';
                                \update_option($this->api_manager->options[$this->api_manager->activated_key], 'Deactivated');
                                break;
                            case '105':
                                \add_settings_error('api_key_invalid_text', 'api_key_invalid_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error');
                                $options[$this->api_manager->api_key] = '';
                                $options[$this->api_manager->activation_email] = '';
                                \update_option($this->api_manager->options[$this->api_manager->activated_key], 'Deactivated');
                                break;
                            case '106':
                                \add_settings_error('sub_not_active_text', 'sub_not_active_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error');
                                $options[$this->api_manager->api_key] = '';
                                $options[$this->api_manager->activation_email] = '';
                                \update_option($this->api_manager->options[$this->api_manager->activated_key], 'Deactivated');
                                break;
                        }
                    }
                }
                // End Plugin Activation
            }
            return $options;
        }
        // Returns the API License Key status from the WooCommerce API Manager on the server
        public function license_key_status()
        {
            $activation_status = \get_option($this->api_manager->activated_key);
            $args = ['email' => $this->api_manager->options[$this->api_manager->activation_email], 'licence_key' => $this->api_manager->options[$this->api_manager->api_key]];
            return \json_decode($this->api_manager->key()->status($args), \true);
        }
        // Deactivate the current license key before activating the new license key
        public function replace_license_key($current_api_key)
        {
            $args = ['email' => $this->api_manager->options[$this->api_manager->activation_email], 'licence_key' => $current_api_key];
            $reset = $this->api_manager->key()->deactivate($args);
            // reset license key activation
            if ($reset == \true) {
                return \true;
            }
            return \add_settings_error('not_deactivated_text', 'not_deactivated_error', \__('The subscription could not be deactivated. Use the Subscription Deactivation tab to manually deactivate the subscription before activating a new subscription.', $this->api_manager->text_domain), 'updated');
        }
        // Deactivates the license key to allow key to be used on another blog
        public function wc_am_license_key_deactivation($input)
        {
            $activation_status = \get_option($this->api_manager->activated_key);
            $args = ['email' => $this->api_manager->options[$this->api_manager->activation_email], 'licence_key' => $this->api_manager->options[$this->api_manager->api_key]];
            // For testing activation status_extra data
            // $activate_results = json_decode( $this->api_manager->key()->status( $args ), true );
            // print_r($activate_results); exit;
            $options = $input == 'on' ? 'on' : 'off';
            if ($options == 'on' && $activation_status == 'Activated' && $this->api_manager->options[$this->api_manager->api_key] != '' && $this->api_manager->options[$this->api_manager->activation_email] != '') {
                // deactivates license key activation
                $activate_results = \json_decode($this->api_manager->key()->deactivate($args), \true);
                // Used to display results for development
                //print_r($activate_results); exit();
                if ($activate_results['deactivated'] === \true) {
                    $update = [$this->api_manager->api_key => '', $this->api_manager->activation_email => ''];
                    $merge_options = \array_merge($this->api_manager->options, $update);
                    \update_option($this->api_manager->data_key, $merge_options);
                    \update_option($this->api_manager->activated_key, 'Deactivated');
                    \add_settings_error('wc_am_deactivate_text', 'deactivate_msg', \__('Plugin subscription deactivated. ', $this->api_manager->text_domain) . "{$activate_results['activations_remaining']}.", 'updated');
                    return $options;
                }
                if (isset($activate_results['code'])) {
                    switch ($activate_results['code']) {
                        case '100':
                            \add_settings_error('api_email_text', 'api_email_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error');
                            $options[$this->api_manager->activation_email] = '';
                            $options[$this->api_manager->api_key] = '';
                            \update_option($this->api_manager->options[$this->api_manager->activated_key], 'Deactivated');
                            break;
                        case '101':
                            \add_settings_error('api_key_text', 'api_key_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error');
                            $options[$this->api_manager->api_key] = '';
                            $options[$this->api_manager->activation_email] = '';
                            \update_option($this->api_manager->options[$this->api_manager->activated_key], 'Deactivated');
                            break;
                        case '102':
                            \add_settings_error('api_key_purchase_incomplete_text', 'api_key_purchase_incomplete_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error');
                            $options[$this->api_manager->api_key] = '';
                            $options[$this->api_manager->activation_email] = '';
                            \update_option($this->api_manager->options[$this->api_manager->activated_key], 'Deactivated');
                            break;
                        case '103':
                            \add_settings_error('api_key_exceeded_text', 'api_key_exceeded_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error');
                            $options[$this->api_manager->api_key] = '';
                            $options[$this->api_manager->activation_email] = '';
                            \update_option($this->api_manager->options[$this->api_manager->activated_key], 'Deactivated');
                            break;
                        case '104':
                            \add_settings_error('api_key_not_activated_text', 'api_key_not_activated_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error');
                            $options[$this->api_manager->api_key] = '';
                            $options[$this->api_manager->activation_email] = '';
                            \update_option($this->api_manager->options[$this->api_manager->activated_key], 'Deactivated');
                            break;
                        case '105':
                            \add_settings_error('api_key_invalid_text', 'api_key_invalid_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error');
                            $options[$this->api_manager->api_key] = '';
                            $options[$this->api_manager->activation_email] = '';
                            \update_option($this->api_manager->options[$this->api_manager->activated_key], 'Deactivated');
                            break;
                        case '106':
                            \add_settings_error('sub_not_active_text', 'sub_not_active_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error');
                            $options[$this->api_manager->api_key] = '';
                            $options[$this->api_manager->activation_email] = '';
                            \update_option($this->api_manager->options[$this->api_manager->activated_key], 'Deactivated');
                            break;
                    }
                }
            } else {
                return $options;
            }
        }
        public function wc_am_deactivate_text()
        {
        }
        public function wc_am_deactivate_textarea()
        {
            echo '<input type="checkbox" id="' . $this->api_manager->deactivate_checkbox . '" name="' . $this->api_manager->deactivate_checkbox . '" value="on"';
            echo \checked(\get_option($this->api_manager->deactivate_checkbox), 'on');
            echo '/>';
            ?><span class="description"><?php 
            \_e('Deactivates an API Key so it can be used on another blog.', $this->api_manager->text_domain);
            ?></span>
			<?php 
        }
    }
    // class WPDesk_API_MENU
}
// if (!class_exists('WPDesk_API_MENU'))
