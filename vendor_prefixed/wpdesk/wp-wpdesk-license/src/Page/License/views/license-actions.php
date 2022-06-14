<?php

namespace BmWoocommerceVendor;

if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
?>
<form method="POST">
    <table class="wpdesk_helper_key_table">
        <tr>
            <td><?php 
\_e('Key:', 'bm-woocommerce');
?></td>
            <td><input class="wpdesk_helper_input" name="api_key" type="text"
                       value="<?php 
echo $api_key;
?>" <?php 
echo $disabled;
?> /></td>
        </tr>
        <tr>
            <td><?php 
\_e('Email:', 'bm-woocommerce');
?></td>
            <td><input class="wpdesk_helper_input" name="activation_email" type="email"
                       value="<?php 
echo $activation_email;
?>" <?php 
echo $disabled;
?> /></td>
        </tr>
        <tr>
            <td></td>
            <td>
				<?php 
if ($activation_status == 'Deactivated') {
    ?>
                    <button class="wpdesk_helper_button button button-primary"><?php 
    \_e('Activate', 'bm-woocommerce');
    ?></button>
				<?php 
} else {
    ?>
                    <button class="wpdesk_helper_button button"><?php 
    \_e('Deactivate', 'bm-woocommerce');
    ?></button>
				<?php 
}
?>
            </td>
        </tr>
    </table>
    <input type="hidden" name="plugin" value="<?php 
echo $plugin;
?>"/>
	<?php 
if ($activation_status == 'Deactivated') {
    ?>
        <input type="hidden" name="action" value="activate"/>
	<?php 
} else {
    ?>
        <input type="hidden" name="action" value="deactivate"/>
	<?php 
}
?>
</form>
<?php 
