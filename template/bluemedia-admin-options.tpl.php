<?php
/**
 * System płatności online Blue Media
 * View for admin options.
 *
 * @author    Piotr Żuralski <piotr@zuralski.net>
 * @copyright 2015 Blue Media
 * @license   http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 * @since     2015-02-28
 * @version   v1.2.0
 */
echo sprintf('<h3>%s</h3>', isset($this->method_title) ? $this->method_title : __('Settings', 'bluemedia-payment-gateway-for-woocommerce'));
echo isset($this->method_description) ? wpautop($this->method_description) : '';
echo '<table class="form-table">';
$this->generate_settings_html();
echo '</table>';

?>
<script type="text/javascript">
    var bluemedia_mode = jQuery('#woocommerce_bluemedia_payment_gateway_mode');
    var bluemedia_payment_domain = jQuery('#woocommerce_bluemedia_payment_gateway_payment_domain');

    var bluemedia_mode_change = function() {
        switch(bluemedia_mode.val()) {
            case '<?php echo WC_Payment_Gateway_BlueMedia::MODE_SANDBOX; ?>':
                bluemedia_payment_domain.val('<?php echo WC_Payment_Gateway_BlueMedia::PAYMENT_DOMAIN_SANDBOX; ?>');
                break;

            case '<?php echo WC_Payment_Gateway_BlueMedia::MODE_LIVE; ?>':
                bluemedia_payment_domain.val('<?php echo WC_Payment_Gateway_BlueMedia::PAYMENT_DOMAIN_LIVE; ?>');
                break;
        }
    };

    bluemedia_mode.on('change', bluemedia_mode_change);
    bluemedia_mode_change();
</script>