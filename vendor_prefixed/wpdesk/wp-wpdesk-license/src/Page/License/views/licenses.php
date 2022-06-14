<?php

namespace BmWoocommerceVendor;

if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
?>
<style>
    #product_license {
        width: 500px;
    }

    .wpdesk_helper_key_table,
    .wpdesk_helper_input {
        width: 100%;
    }
</style>

<div class="wrap">
	<?php 
/* screen_icon(); */
?>

    <h1><?php 
\_e('WP Desk Subscriptions', 'bm-woocommerce');
?></h1>

    <p class="mb0">
		<?php 
if (\get_locale() === 'pl_PL') {
    $url = 'https://www.wpdesk.pl/moje-konto/';
} else {
    $url = 'https://www.wpdesk.net/my-account/';
}
$link = \sprintf(\__('Get your subscription keys <a href="%s" target="_blank">here</a>. You can activate/deactivate API keys <strong>unlimited times on different domains</strong> as long as you have an active subscription.', 'bm-woocommerce'), \esc_url($url));
echo $link;
?>
    </p>

	<?php 
\settings_errors();
?>

	<?php 
$list_table = new \BmWoocommerceVendor\WPDesk_Helper_List_Table();
$list_table->data = $plugins;
$list_table->prepare_items();
$list_table->display();
?>
</div> <!-- class="wrap" -->
<?php 
