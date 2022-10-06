<?php

namespace Ilabs\BM_Woocommerce;

use Exception;
use Ilabs\BM_Woocommerce\Domain\Service\Ga4\Add_Product_To_Cart_Use_Case;
use Ilabs\BM_Woocommerce\Domain\Service\Ga4\Click_On_Product_Use_Case;
use Ilabs\BM_Woocommerce\Domain\Service\Ga4\Complete_Transation_Use_Case;
use Ilabs\BM_Woocommerce\Domain\Service\Ga4\Ga4_Use_Case_Interface;
use Ilabs\BM_Woocommerce\Domain\Service\Ga4\Initiate_Checkout_Use_Case;
use Ilabs\BM_Woocommerce\Domain\Service\Ga4\Remove_Product_From_Cart_Use_Case;
use Ilabs\BM_Woocommerce\Domain\Service\Ga4\View_Product_On_List_Use_Case;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Abstract_Ilabs_Plugin;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Alerts;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Event\Wc_Add_To_Cart;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Event\Wc_Order_Status_Changed;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Event\Wc_Remove_Cart_Item;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Wc_Cart_Aware_Interface;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Wc_Order_Aware_Interface;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Wc_Product_Aware_Interface;

class Plugin extends Abstract_Ilabs_Plugin
{

    /**
     * @throws Exception
     */
    public function enqueue_frontend_scripts()
    {
        wp_enqueue_style($this->get_plugin_prefix() . '_front_css',
            $this->get_plugin_css_url() . '/frontend.css'
        );

        wp_enqueue_script($this->get_plugin_prefix() . '_front_js',
            $this->get_plugin_js_url() . '/front.js',
            ['jquery'],
            1.1,
            true);

        if (!empty(get_option($this->get_plugin_prefix() . '_ga4_tracking_id'))) {
            wp_enqueue_script($this->get_plugin_prefix() . '_ga4',
                'https://www.googletagmanager.com/gtag/js?id=' . get_option($this->get_plugin_prefix() . '_ga4_tracking_id'),
                [],
                1.1,
                true);

            wp_localize_script($this->get_plugin_prefix() . '_front_js',
                'blueMedia',
                [
                    'ga4TrackingId' => get_option($this->get_plugin_prefix() . '_ga4_tracking_id'),
                ]
            );
        }
    }


    /**
     * @throws Exception
     */
    public function enqueue_dashboard_scripts()
    {
        wp_enqueue_script($this->get_plugin_prefix() . '_admin_js',
            $this->get_plugin_js_url() . '/admin.js',
            ['jquery'],
            1.1,
            true);

        wp_enqueue_style($this->get_plugin_prefix() . '_admin_css',
            $this->get_plugin_css_url() . '/admin.css'
        );
    }

    /**
     * @throws Exception
     */
    protected function before_init()
    {
        $tracking_id = $this->get_request()->get_by_key('woocommerce_bluemedia_ga4_tracking_id');

        if ($tracking_id) {
            update_option($this->get_plugin_prefix() . '_ga4_tracking_id', $tracking_id);
        }

        $this->implement_ga4();
        $this->implement_settings_modal();
        //$this->implement_settings_banner();
    }

    /**
     * @throws Exception
     */
    private function implement_settings_banner()
    {
        $banner = blue_media()->get_event_chain();
        $banner
            ->on_wc_before_settings('checkout')
            ->when_request_value_equals('section', 'bluemedia')
            ->action_output_template('settings_banner.php')
            ->execute();
    }

    /**
     * @throws Exception
     */
    private function implement_settings_modal()
    {
        $settings_modal = blue_media()->get_event_chain();
        $settings_modal
            ->on_wp_admin_footer()
            ->action(function () {
                echo '<div class="bm-modal-content">
    <span class="bm-close">&times;</span>
    <p>Google Analytics 4</p>
<ul>
<li>' . __('Go to "Administrator" in the lower left corner.', 'bm-woocommerce') . '</li>
<li>' . __('In the "Services" section, click "Data Streams".', 'bm-woocommerce') . '</li>
<li>' . __('Click the name of the data stream.', 'bm-woocommerce') . '</li>
<li>' . __('Your measurement ID is in the upper right corner (eg G-QCX4K9GSPC).', 'bm-woocommerce') . '</li>
</ul>    

  </div><div class="bm-modal-overlay"></div>';
            })->execute();
    }

    /**
     * @return void
     * @throws Exception
     */
    private function implement_ga4()
    {
        $ga4 = blue_media()->get_event_chain();
        $ga4_task_queue = $ga4->get_wc_session_cache('ga_tasks');

        $ga4
            ->on_wc_before_shop_loop_item()
            ->when_is_shop()
            ->action(function (Wc_Product_Aware_Interface $product_aware_interface) use ($ga4_task_queue) {
                $ga4_task_queue->push(
                    new View_Product_On_List_Use_Case($product_aware_interface->get_product()));
            })
            ->on_wc_before_single_product()
            ->action(function (Wc_Product_Aware_Interface $product_aware_interface) use ($ga4_task_queue) {
                $ga4_task_queue->push(
                    new Click_On_Product_Use_Case($product_aware_interface->get_product()));
            })
            ->on_wc_add_to_cart()
            ->action(function (Wc_Add_To_Cart $event) use ($ga4_task_queue) {
                $ga4_task_queue->push(
                    new Add_Product_To_Cart_Use_Case($event->get_product(), $event->get_quantity()));
            })
            ->on_wc_remove_cart_item()
            ->action(function (Wc_Remove_Cart_Item $event) use ($ga4_task_queue) {
                $ga4_task_queue->push(
                    new Remove_Product_From_Cart_Use_Case($event->get_product()));
            })
            ->on_wc_checkout_page()
            ->when_is_not_ajax()
            ->action(function (Wc_Cart_Aware_Interface $cart_aware_interface) use ($ga4_task_queue) {
                $ga4_task_queue->push(
                    new Initiate_Checkout_Use_Case($cart_aware_interface->get_cart()));
            })
            ->on_wc_order_status_changed()
            ->when(function (Wc_Order_Status_Changed $event) {
                return $event->get_new_status() === 'completed';
            })
            ->action(function (Wc_Order_Aware_Interface $order_aware_interface) use ($ga4_task_queue) {
                $ga4_task_queue->push(
                    new Complete_Transation_Use_Case($order_aware_interface->get_order()));
            })
            ->on_wp_footer()
            ->when_is_not_ajax()
            ->action(function () use ($ga4_task_queue) {
                $tasks = [];
                if ($ga4_task_queue->get()) {
                    foreach ($ga4_task_queue->get() as $ga4_Use_Case_Interface) /* @var $ga4_Use_Case_Interface Ga4_Use_Case_Interface */ {
                        $tasks[] = $ga4_Use_Case_Interface->get_ga4_payload_array();
                    }
                    echo "<script>var blue_media_ga4_tasks = '" . wp_json_encode($tasks) . "'</script>";
                    $ga4_task_queue->clear();
                }
            })->execute();
    }

    public function test_form()
    {

    }

    protected function plugins_loaded_hooks()
    {
        // TODO: Implement plugins_loaded_hooks() method.
    }


    public function init()
    {

        add_filter('woocommerce_get_checkout_order_received_url',
            function ($return_url, $order) {
                update_option('bm_order_received_url', $return_url);

                return $return_url;
            }, 10, 2);

        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
        require_once ABSPATH
            . 'wp-admin/includes/class-wp-filesystem-direct.php';

        add_action('template_redirect', [$this, 'return_redirect_handler']);

        add_filter('woocommerce_cancel_unpaid_order',
            [$this, 'bm_woocommerce_cancel_unpaid_order_filter'], 10, 2);


        if (!empty(get_option('bm_order_received_url'))
            && empty(get_option('bm_payment_start'))) {

            update_option('bm_order_received_url', null);
            update_option('bm_payment_start', null);
            die;
        }

        $alerts = new Alerts();
        $last_api_err = get_option('bm_api_last_error');
        if (!empty($last_api_err) && defined('BLUE_MEDIA_DEBUG')) {
            $alerts->add_error('Blue Media: ' . $last_api_err);
        }

        $this->init_payment_gateway();
    }

    private function init_payment_gateway()
    {
        add_filter('woocommerce_payment_gateways',
            function ($gateways) {
                $gateways[]
                    = 'Ilabs\BM_Woocommerce\Gateway\Blue_Media_Gateway';

                return $gateways;
            }
        );

    }


    /**
     * [JIRA] (WOOCOMERCE-17) Błąd przekierowania
     */
    public function return_redirect_handler()
    {
        if (isset($_GET['bm_gateway_return'])) {

            $order = null;

            if (isset($_GET['OrderID'])) {
                $order = wc_get_order($_GET['OrderID']);
            }

            if (isset($_GET['key'])) {
                $order_id = wc_get_order_id_by_order_key($_GET['key']);
                $order = wc_get_order($order_id);
            }

            if ($order) {
                $finish_url = $order->get_checkout_order_received_url();
                wp_redirect($finish_url);
                exit;
            }
        }
    }

    /**
     * [JIRA] (WOOCOMERCE-26) Błędnie przydzielane statusy dla transkacji nie
     * opłaconych w ciągu godziny.
     */
    public function bm_woocommerce_cancel_unpaid_order_filter(
        $string,
        $order
    )
    {
        if ('bluemedia' === $order->get_payment_method()) {
            return false;
        }

        return $string;
    }
}
