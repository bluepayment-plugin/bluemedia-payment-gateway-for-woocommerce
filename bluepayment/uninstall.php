<?php

if (!defined("WP_UNINSTALL_PLUGIN"))
    exit();

delete_option('woocommerce_bluemedia_payment_gateway_settings');

global $wpdb;
$table_name = $wpdb->prefix . 'bluemedia_blik';

$wpdb->query("DROP TABLE IF EXISTS $table_name");
wp_cache_flush();
