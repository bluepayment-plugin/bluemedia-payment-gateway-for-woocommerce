<?php
echo sprintf('<h3>%s</h3>', isset($this->method_title) ? $this->method_title : __("Settings", 'bluepayment-gateway-for-woocommerce'));
echo isset($this->method_description) ? wpautop($this->method_description) : '';
echo '<table class="form-table">';
$this->generate_settings_html();
require_once '_partials/admin/bluemedia-currency.php';
echo '</table>';