<script type="text/javascript">
    function backgroundPaymentShowHide<?php echo $currency; ?>(self) {
        if (self.val() === '1') {
            $('#bluemedia_background_payment_channels_<?php echo $currency; ?>').addClass('active');
        } else {
            $('#bluemedia_background_payment_channels_<?php echo $currency; ?>').removeClass('active');
        }
        $('.bluemedia_download_channels_button_<?php echo $currency; ?>').click();
    }
</script>
<table class="form-table">
	<tr valign="top">
		<th scope="row" class="titledesc">
			<label for="woocommerce_bluemedia_payment_gateway_background_payment"><?php echo __("Background payments", 'bluepayment-gateway-for-woocommerce'); ?></label>
		</th>
		<td class="forminp">
			<fieldset>
				<legend class="screen-reader-text"><span><?php echo __("Background payments", 'bluepayment-gateway-for-woocommerce'); ?></span></legend>
				<select class="select " name="woocommerce_bluemedia_payment_gateway_background_payment_<?php echo $currency; ?>" id="woocommerce_bluemedia_payment_gateway_background_payment_<?php echo $currency; ?>" style="" onchange="backgroundPaymentShowHide<?php echo $currency; ?>($('#woocommerce_bluemedia_payment_gateway_background_payment_<?php echo $currency; ?>'))">
					<option value="0"<?php echo (empty($this->settings["background_payment_$currency"]) || $this->settings["background_payment_$currency"] == '0') ? 'selected="selected"' : ''; ?>><?php echo __("No", 'bluepayment-gateway-for-woocommerce'); ?></option>
					<option value="1"<?php echo (!empty($this->settings["background_payment_$currency"])) ? 'selected="selected"' : '' ?>><?php echo __("Yes", 'bluepayment-gateway-for-woocommerce'); ?></option>
				</select>
				<p class="description bm-desktop-and-mobile-desc"><?php echo __("Choosing \"yes\" means that the customer is not redirected to the Blue Media payment page, instead remaining in the store’s website where different payment options are displayed.<br><br>\nChoosing \"no\" redirects the customer back to the Blue Media payment page.", 'bluepayment-gateway-for-woocommerce'); ?></p>
                <div id="bluemedia_background_payment_channels_<?php echo $currency ?>" <?php echo (!empty($this->settings["background_payment_$currency"])) ? 'class="active"' : ''; ?>>
					<?php if (!empty($this->get_currency_service_id($currency)) && !empty($this->settings["hash_key_$currency"])): ?>
						<a href="#" class="button bluemedia_download_channels_button_<?php echo $currency; ?> bm-desktop-and-mobile-desc" style="width: 175px" onclick="loadedPaymentChannelsList = false;">
							<span><?php echo __("Fetch / update channels", 'bluepayment-gateway-for-woocommerce'); ?></span>
							<span class="loader" style="display: none;"><?php echo __("Downloading...", 'bluepayment-gateway-for-woocommerce'); ?></span>
						</a>
                        <a href="#" class="bluemedia_download_channels_hide_<?php echo $currency; ?>" style="display: none;" onclick="loadedPaymentChannelsList = false;">loader</a>
                        <p class="description bm-desktop-and-mobile-desc"><?php echo __("If \"Payments in the background\" are enabled, the available payment options will appear here.\n", 'bluepayment-gateway-for-woocommerce'); ?></p>
                        <p class="description bm-desktop-and-mobile-desc">
                            <span><?php echo __("Custom sorting of payment", 'bluepayment-gateway-for-woocommerce'); ?>: </span>
                            <select class="select " name="woocommerce_bluemedia_payment_gateway_background_channels_sort_<?php echo $currency; ?>" id="woocommerce_bluemedia_payment_gateway_background_channels_sort_<?php echo $currency; ?>" onchange="$('.bluemedia_download_channels_hide_<?php echo $currency; ?>').click();">
                                <option value="0"<?php echo (empty($this->settings["background_channels_sort_$currency"]) || $this->settings["background_channels_sort_$currency"] == '0') ? 'selected="selected"' : ''; ?>><?php echo __("No", 'bluepayment-gateway-for-woocommerce'); ?></option>
                                <option value="1"<?php echo !empty($this->settings["background_channels_sort_$currency"]) ? 'selected="selected"' : ''; ?>><?php echo __("Yes", 'bluepayment-gateway-for-woocommerce'); ?></option>
                            </select>
                        </p>
						<div class="bluemedia_background_payment_channels_list_<?php echo $currency; ?> bm-desktop-and-mobile-desc">
                            <script>
                                jQuery(document).ready(function($) {
                                    <?php if ($currency == 'PLN'): ?>
                                        $('.bluemedia_download_channels_hide_<?php echo $currency; ?>').click();
                                    <?php else: ?>
                                        setTimeout(function() {
                                            $('.bluemedia_download_channels_hide_<?php echo $currency; ?>').click();
                                        }, 5000);
                                    <?php endif; ?>
                                });
                            </script>
						</div>
					<?php else: ?>
						<?php echo __("Please save the plug-in configuration with the specified serviceID and hash key\n", 'bluepayment-gateway-for-woocommerce'); ?>
					<?php endif; ?>
				</div>
			</fieldset>
		</td>
	</tr>
</table>
