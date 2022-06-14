<?php

namespace BmWoocommerceVendor;

if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
if (!\class_exists('WP_List_Table')) {
    require_once \ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
class WPDesk_Helper_List_Table extends \WP_List_Table
{
    public $per_page = 100;
    public $data;
    public function __construct($args = [])
    {
        global $status, $page;
        parent::__construct(['singular' => 'subscription', 'plural' => 'subscriptions', 'ajax' => \false]);
        $status = 'all';
        $page = $this->get_pagenum();
        $this->data = [];
        parent::__construct($args);
    }
    public function no_items()
    {
        echo \wpautop(\__('No WP Desk plugins found.', 'bm-woocommerce'));
    }
    public function column_default($item, $column_name)
    {
        return $item[$column_name];
    }
    public function get_sortable_columns()
    {
        return [];
    }
    public function get_columns()
    {
        $columns = ['product_name' => \__('Plugin', 'bm-woocommerce'), 'product_status' => \__('Subscription Status', 'bm-woocommerce'), 'product_license' => \__('Subscription Data', 'bm-woocommerce')];
        return $columns;
    }
    public function column_plugin_data($item)
    {
        return '<pre>' . \print_r($item, \true) . '</pre>';
    }
    public function column_product_name($item)
    {
        return \wpautop('<strong>' . $item['api_manager']->product_id . '</strong>');
    }
    public function column_product_version($item)
    {
        return \wpautop($item['api_manager']->version);
    }
    public function column_product_status($item)
    {
        $status = \__('Deactivated', 'bm-woocommerce');
        if ($item['activation_status'] == 'Activated') {
            $status = \__('Activated', 'bm-woocommerce');
        }
        return $status;
    }
    public function column_product_license($item)
    {
        $disabled = 'disabled';
        $api_key = '';
        $activation_email = '';
        if ($item['activation_status'] == 'Deactivated') {
            $disabled = '';
        } else {
            $api_key = $item['api_manager']->options[$item['api_manager']->api_key];
            $activation_email = $item['api_manager']->options[$item['api_manager']->activation_email];
        }
        $plugin = $item['plugin'];
        $activation_status = $item['activation_status'];
        \ob_start();
        include 'license-actions.php';
        $output = \ob_get_contents();
        \ob_end_clean();
        return $output;
    }
    public function get_bulk_actions()
    {
        $actions = [];
        return $actions;
    }
    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = [];
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = [$columns, $hidden, $sortable];
        $total_items = \count($this->data);
        $this->set_pagination_args(['total_items' => $total_items, 'per_page' => $total_items]);
        $this->items = $this->data;
    }
}
