<?php
/**
 * Created by PhpStorm.
 * User: tkapusta
 * Date: 05.12.2017
 * Time: 23:58
 */

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/screen.php' );
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


class BluemediaGateway_List_Table extends WP_List_Table
{

    public function get_columns() {
        $table_columns = array(
            'gateway_id'	=> __( 'GatewayID', $this->plugin_text_domain ),
            'gateway_status'	=> __('Status', $this->plugin_text_domain ),
            'gateway_name'	=> __( 'Nazwa', $this->plugin_text_domain ),
            'gateway_logo_url'	=> __( 'Logo', $this->plugin_text_domain ),
        );
        return $table_columns;
    }

    public function no_items()
    {
        _e('Nie znaleziono kanałów płatności. Zaktualizuj kanały płatności.', $this->plugin_text_domain);
    }

    public function prepare_items()
    {

        // code to handle bulk actions

        //used by WordPress to build and fetch the _column_headers property
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        $table_data = $this->fetch_table_data();

        // code to handle data operations like sorting and filtering

        // start by assigning your data to the items variable
        $this->items = $table_data;

        // code to handle pagination
    }

    public function fetch_table_data()
    {
        global $wpdb;

        $wpdb_table = $wpdb->prefix . 'blue_gateways';
        $query = "SELECT * FROM $wpdb_table";
        // query output_type will be an associative array with ARRAY_A.
        $query_results = $wpdb->get_results($query, ARRAY_A);

        // return result array to prepare_items.
        return $query_results;
    }

    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'gateway_logo_url':
                return "<img src='" . $item[$column_name] . "'/>";
            default:
                return $item[$column_name];
        }
    }

    function column_gateway_status($item) {
        $status_display = 'Aktywny';
        $status = 'Deaktywuj';
        $action = 'deactivate';
        if ($item['gateway_status'] == 1){
            $status = 'Aktywuj';
            $status_display = 'Nieaktywny';
            $action = 'activate';
        }
        $actions = array(
            'edit' => sprintf('<a href="?page=%s&action=%s&gateway_id=%s">%s</a>',
                $_REQUEST['page'], $action, $item['gateway_id'], $status),
        );

        return sprintf('%1$s %2$s', $status_display, $this->row_actions($actions) );
    }
}