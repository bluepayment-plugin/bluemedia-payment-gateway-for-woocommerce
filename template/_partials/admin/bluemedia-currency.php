<tr valign="top">
	<th scope="row" class="titledesc">
		<label for="woocommerce_bluemedia_payment_gateway_currency"><?php echo __("Currency Settings", 'bluepayment-gateway-for-woocommerce'); ?></label>
	</th>
	<td class="forminp">
		<style type="text/css">
			#bluemedia-currencies-menu {margin:0; padding:0}
			.bluemedia-currency-content-PLN:not(.active) {display:none}
			.bluemedia-currency-content-EUR:not(.active) {display:none}
			.bluemedia-currency-content-USD:not(.active) {display:none}
			.bluemedia-currency-content-GBP:not(.active) {display:none}
		</style>
		<script type="text/javascript">

		</script>
		<style type="text/css">
			#bluemedia_background_payment_channels_PLN {margin-top:24px;}
            #bluemedia_background_payment_channels_EUR {margin-top:24px;}
            #bluemedia_background_payment_channels_USD {margin-top:24px;}
            #bluemedia_background_payment_channels_GBP {margin-top:24px;}

			#bluemedia_background_payment_channels_PLN:not(.active) {display:none}
			#bluemedia_background_payment_channels_EUR:not(.active) {display:none}
			#bluemedia_background_payment_channels_USD:not(.active) {display:none}
			#bluemedia_background_payment_channels_GBP:not(.active) {display:none}

            .bluemedia_background_payment_channels_list_PLN {margin-top:24px;}
            .bluemedia_background_payment_channels_list_EUR {margin-top:24px;}
            .bluemedia_background_payment_channels_list_USD {margin-top:24px;}
            .bluemedia_background_payment_channels_list_GBP {margin-top:24px;}

			.bluemedia_background_payment_channels_list_PLN ul {padding:0; margin:0}
            .bluemedia_background_payment_channels_list_PLN li {display: inline-block; width: 15%; text-align: center; line-height: 180%;}
            .bluemedia_background_payment_channels_list_PLN li a {cursor: move; margin-top:6px; display:inline-block;}

            .bluemedia_background_payment_channels_list_EUR ul {padding:0; margin:0}
            .bluemedia_background_payment_channels_list_EUR li {display: inline-block; width: 15%; text-align: center; line-height: 180%;}
            .bluemedia_background_payment_channels_list_EUR li a {cursor: move; margin-top:6px; display:inline-block;}

            .bluemedia_background_payment_channels_list_USD ul {padding:0; margin:0}
            .bluemedia_background_payment_channels_list_USD li {display: inline-block; width: 15%; text-align: center; line-height: 180%;}
            .bluemedia_background_payment_channels_list_USD li a {cursor: move; margin-top:6px; display:inline-block;}

            .bluemedia_background_payment_channels_list_GBP ul {padding:0; margin:0}
            .bluemedia_background_payment_channels_list_GBP li {display: inline-block; width: 15%; text-align: center; line-height: 180%;}
            .bluemedia_background_payment_channels_list_GBP li a {cursor: move; margin-top:6px; display:inline-block;}

            .bm-desktop-and-mobile-desc {
                position: relative;
                z-index: 999;
                display: block !important;
            }
            @media only screen and (max-width: 768px) {
                .bm-desktop-and-mobile-desc {
                    margin-left: 0 !important;
                }
            }
            @media only screen and (min-width: 768px) {
                .bm-desktop-and-mobile-desc {
                    margin-left: -235px !important;
                }
            }
		</style>
		<script type="text/javascript">
            var loadedPaymentChannelsList = false;
			jQuery(document).ready(function($)
			{
                $('#bluemedia-currencies-menu a').click(function (e) {
                    e.preventDefault();
                    bluemedia_show_currency_content($(this));
                });

                function bluemedia_show_currency_content(el) {
                    var new_currency = el.attr('data-currency');
                    loadedPaymentChannelsList = false;
                    updatePaymentsChannelsList(new_currency);
                    $('.bluemedia-currency-content-all').removeClass('active');
                    $('#bluemedia-currencies-menu a').removeClass('nav-tab-active');
                    $('#bluemedia-currencies-menu a[data-currency=' + new_currency + ']').addClass('nav-tab-active');
                    $('.bluemedia-currency-content-' + new_currency).addClass('active');
                }

				function init_sortable(currency)
				{
                    var bluemedia_background_payment_channels_list = $(".bluemedia_background_payment_channels_list_" + currency + " ul");
					if (bluemedia_background_payment_channels_list.length > 0) {
                        bluemedia_background_payment_channels_list.sortable({
							placeholder: "ui-state-highlight",
							helper: 'clone',
							handle: '.bm-channel-move'
						}).disableSelection();
					}
				}
				function bluemedia_toggle_backgorund_payments_channels(el, currency)
				{
					if (el.val() > 0) {
						el.closest('.bluemedia-currency-content-' + currency).find('.bluemedia_background_payment_channels_' + currency).fadeIn();
					} else {
						el.closest('.bluemedia-currency-content-' + currency).find('.bluemedia_background_payment_channels_' + currency).fadeOut();
					}
				}

				$('select[name=woocommerce_bluemedia_payment_gateway_background_payment_PLN]').on('change', function () {
					bluemedia_toggle_backgorund_payments_channels($(this), 'PLN')
				});
                $('select[name=woocommerce_bluemedia_payment_gateway_background_payment_EUR]').on('change', function () {
                    bluemedia_toggle_backgorund_payments_channels($(this), 'EUR')
                });
                $('select[name=woocommerce_bluemedia_payment_gateway_background_payment_USD]').on('change', function () {
                    bluemedia_toggle_backgorund_payments_channels($(this), 'USD')
                });
                $('select[name=woocommerce_bluemedia_payment_gateway_background_payment_GBP]').on('change', function () {
                    bluemedia_toggle_backgorund_payments_channels($(this), 'GBP')
                });

				$('.bluemedia_download_channels_hide_PLN').click(function (e) {
                    e.preventDefault();
                    updatePaymentsChannelsList('PLN');
                });
                $('.bluemedia_download_channels_button_PLN').click(function (e) {
                    e.preventDefault();
                    updatePaymentsChannelsList('PLN');
                });

                $('.bluemedia_download_channels_hide_EUR').click(function (e) {
                    e.preventDefault();
                    updatePaymentsChannelsList('EUR');
                });
                $('.bluemedia_download_channels_button_EUR').click(function (e) {
                    e.preventDefault();
                    updatePaymentsChannelsList('EUR');
                });

                $('.bluemedia_download_channels_hide_USD').click(function (e) {
                    e.preventDefault();
                    updatePaymentsChannelsList('USD');
                });
                $('.bluemedia_download_channels_button_USD').click(function (e) {
                    e.preventDefault();
                    updatePaymentsChannelsList('USD');
                });

                $('.bluemedia_download_channels_hide_GBP').click(function (e) {
                    e.preventDefault();
                    updatePaymentsChannelsList('GBP');
                });
                $('.bluemedia_download_channels_button_GBP').click(function (e) {
                    e.preventDefault();
                    updatePaymentsChannelsList('GBP');
                });

				function updatePaymentsChannelsList(currencyCode) {
					if (loadedPaymentChannelsList) {
					    return true;
                    }

                    loadedPaymentChannelsList = true;

					var el = $('.bluemedia_download_channels_button_' + currencyCode);
					el.find('span').hide();
					el.find('span.loader').show();

					var currency = currencyCode;
					var channels_sort = $('#woocommerce_bluemedia_payment_gateway_background_channels_sort_' + currency).val();

                    var blik_pbl = $('#woocommerce_bluemedia_payment_gateway_blik_pbl_' + currency).val();
                    var blik_zero = $('#woocommerce_bluemedia_payment_gateway_blik_zero_' + currency).val();
                    var card = $('#woocommerce_bluemedia_payment_gateway_card_' + currency).val();
                    var installment = $('#woocommerce_bluemedia_payment_gateway_installment_' + currency).val();
                    var smartney = $('#woocommerce_bluemedia_payment_gateway_smartney_' + currency).val();

                    var show_gateways = "&blik_pbl=" + blik_pbl + "&blik_zero=" + blik_zero + "&card=" + card + "&installment=" + installment + "&smartney=" + smartney  ;

					$.ajax({
						type: "POST",
						url: "admin-ajax.php",
						data: "action=bluemedia_edit_background_payments_channels&currency=" + currency + "&channels_sort=" + channels_sort + show_gateways,
						success: function (msg)
						{
							el.find('span').hide();
							el.find('span').not('.loader').show();
							el.closest('.bluemedia-currency-content-' + currency).find('.bluemedia_background_payment_channels_list_' + currency).html(msg);
                            if (channels_sort == '1') {
                                $('.bm-channel-move').show();
                            } else {
                                $('.bm-channel-move').hide();
                            }
                            loadedPaymentChannelsList = false;
						},
						complete: function (r)
						{
							init_sortable(currency);
						}
					});
				}
			});
		</script>
		<nav id="bluemedia-currencies-menu" class="nav-tab-wrapper woo-nav-tab-wrapper">
			<?php $i = 0; foreach ((new CurrencyDictionary())->getAvailableCurrencies() as $currency => $currency_desc): $i++ ?>
				<a href="#" data-currency="<?php echo $currency; ?>" class="nav-tab<?php echo ($i == 1) ? ' nav-tab-active' : '' ?>"><?php echo $currency; ?></a>
			<?php endforeach; ?>
		</nav>
		<?php $i = 0; foreach ((new CurrencyDictionary())->getAvailableCurrencies() as $currency => $currency_desc): $i++ ?>
			<fieldset class="bluemedia-currency-content-all bluemedia-currency-content-<?php echo $currency ?><?php echo ($i == 1) ? ' active' : '' ?>" data-currency="<?php echo $currency; ?>">
				<legend class="screen-reader-text"><span><?php echo $currency; ?></span></legend>
				<table class="form-table">
					<tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="woocommerce_bluemedia_payment_gateway_service_id_<?php echo $currency; ?>"><?php echo __("ServiceID", 'bluepayment-gateway-for-woocommerce'); ?></label>
                        </th>
                        <td class="forminp">
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php echo __("ServiceID", 'bluepayment-gateway-for-woocommerce');   ?></span></legend>
                                <input class="input-text regular-input" type="text" name="woocommerce_bluemedia_payment_gateway_service_id_<?php echo $currency ?>" id="woocommerce_bluemedia_payment_gateway_service_id_<?php echo $currency ?>" style="" value="<?php echo !empty($this->settings["service_id_$currency"]) ? $this->settings["service_id_$currency"] : '' ?>" placeholder="" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');"/>
                                <p class="description bm-desktop-and-mobile-desc"><?php echo __("ServiceID received from Blue Media", 'bluepayment-gateway-for-woocommerce'); ?></p>
                            </fieldset>
                        </td>
                    </tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="woocommerce_bluemedia_payment_gateway_hash_key_<?php echo $currency; ?>"><?php echo __("Hash key", 'bluepayment-gateway-for-woocommerce'); ?></label>
                        </th>
						<td class="forminp">
							<fieldset>
								<legend class="screen-reader-text"><span><?php echo __("Hash key", 'bluepayment-gateway-for-woocommerce'); ?></span></legend>
								<input class="input-text regular-input " type="text" name="woocommerce_bluemedia_payment_gateway_hash_key_<?php echo $currency; ?>" id="woocommerce_bluemedia_payment_gateway_hash_key_<?php echo $currency; ?>" value="<?php echo !empty($this->settings["hash_key_$currency"]) ? $this->settings["hash_key_$currency"] : '' ?>" placeholder="" />
								<p class="description bm-desktop-and-mobile-desc"><?php echo __("The key to hash data received from Blue Media", 'bluepayment-gateway-for-woocommerce'); ?></p>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="woocommerce_bluemedia_payment_gateway_hash_key_mode_<?php echo $currency; ?>"><?php echo __("Hash encryption method", 'bluepayment-gateway-for-woocommerce'); ?></label>
						</th>
						<td class="forminp">
							<fieldset>
								<legend class="screen-reader-text"><span><?php echo __("Hash encryption method", 'bluepayment-gateway-for-woocommerce') ?></span></legend>
								<select class="select " name="woocommerce_bluemedia_payment_gateway_hash_key_mode_<?php echo $currency ?>" id="woocommerce_bluemedia_payment_gateway_hash_key_mode_<?php echo $currency; ?>" style="">
									<option value="sha256"<?php echo (!empty($this->settings["hash_key_mode_$currency"]) && $this->settings["hash_key_mode_$currency"] == 'sha256') ? 'selected="selected"' : ''; ?>>SHA256</option>
									<option value="md5"<?php echo (!empty($this->settings["hash_key_mode_$currency"]) && $this->settings["hash_key_mode_$currency"] == 'md5') ? 'selected="selected"' : ''; ?>>MD5</option>
								</select>
							</fieldset>
						</td>
					</tr>
					<?php if ($currency == 'PLN'): ?>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="woocommerce_bluemedia_payment_gateway_blik_<?php echo $currency; ?>"><?php echo __("BLIK PBL payment", 'bluepayment-gateway-for-woocommerce'); ?></label>
                            </th>
                            <td class="forminp">
                                <fieldset>
                                    <legend class="screen-reader-text"><span><?php echo __("BLIK PBL payment", 'bluepayment-gateway-for-woocommerce') ?></span></legend>
                                    <select class="select " name="woocommerce_bluemedia_payment_gateway_blik_pbl_<?php echo $currency ?>" id="woocommerce_bluemedia_payment_gateway_blik_pbl_<?php echo $currency ?>" style="" onchange="loadedPaymentChannelsList = false; $('.bluemedia_download_channels_hide_<?php echo $currency ?>').click();" >
                                        <option value="no"<?php echo (!empty($this->settings["blik_pbl_$currency"]) && $this->settings["blik_pbl_$currency"] == 'no') ? 'selected="selected"' : '' ?>><?php echo __("Off", 'bluepayment-gateway-for-woocommerce') ?></option>
                                        <option value="yes"<?php echo (!empty($this->settings["blik_pbl_$currency"]) && $this->settings["blik_pbl_$currency"] == 'yes') ? 'selected="selected"' : '' ?>><?php echo __("On", 'bluepayment-gateway-for-woocommerce') ?></option>
                                    </select>
                                    <p class="description bm-desktop-and-mobile-desc"><?php echo __("Payment displayed as a separate radio button on the Store's website with redirection to the BLIK processor\n", 'bluepayment-gateway-for-woocommerce') ?></p>
                                </fieldset>
                            </td>
                        </tr>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="woocommerce_bluemedia_payment_gateway_blik_zero_<?php echo $currency ?>"><?php echo __("BLIK payment 0", 'bluepayment-gateway-for-woocommerce') ?></label>
							</th>
							<td class="forminp">
								<fieldset>
									<legend class="screen-reader-text"><span><?php echo __("BLIK payment 0", 'bluepayment-gateway-for-woocommerce') ?></span></legend>
									<select class="select " name="woocommerce_bluemedia_payment_gateway_blik_zero_<?php echo $currency ?>" id="woocommerce_bluemedia_payment_gateway_blik_zero_<?php echo $currency ?>" style="" onchange="loadedPaymentChannelsList = false; $('.bluemedia_download_channels_hide_<?php echo $currency ?>').click();"  >
										<option value="no"<?php echo (!empty($this->settings["blik_zero_$currency"]) && $this->settings["blik_zero_$currency"] == 'no') ? 'selected="selected"' : '' ?>><?php echo __('', 'bluepayment-gateway-for-woocommerce') ?><?php echo __("Off", 'bluepayment-gateway-for-woocommerce'); ?></option>
										<option value="yes"<?php echo (!empty($this->settings["blik_zero_$currency"]) && $this->settings["blik_zero_$currency"] == 'yes') ? 'selected="selected"' : '' ?>><?php echo __('', 'bluepayment-gateway-for-woocommerce') ?><?php echo __("On", 'bluepayment-gateway-for-woocommerce'); ?></option>
									</select>
                                    <p class="description bm-desktop-and-mobile-desc"><?php echo __("Payment displayed as a separate radio button on the Store's website with the BLIK processor form", 'bluepayment-gateway-for-woocommerce') ?></p>
								</fieldset>
							</td>
						</tr>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="woocommerce_bluemedia_payment_gateway_card_<?php echo $currency ?>"><?php echo __("Card payment", 'bluepayment-gateway-for-woocommerce') ?></label>
                            </th>
                            <td class="forminp">
                                <fieldset>
                                    <legend class="screen-reader-text"><span><?php echo __("Card payment", 'bluepayment-gateway-for-woocommerce') ?></span></legend>
                                    <select class="select " name="woocommerce_bluemedia_payment_gateway_card_<?php echo $currency ?>" id="woocommerce_bluemedia_payment_gateway_card_<?php echo $currency ?>" style="" onchange="loadedPaymentChannelsList = false; $('.bluemedia_download_channels_hide_<?php echo $currency ?>').click();"  >
                                        <option value="no"<?php echo (!empty($this->settings["card_$currency"]) && $this->settings["card_$currency"] == 'no') ? 'selected="selected"' : '' ?>><?php echo __("Off", 'bluepayment-gateway-for-woocommerce') ?></option>
                                        <option value="yes"<?php echo (!empty($this->settings["card_$currency"]) && $this->settings["card_$currency"] == 'yes') ? 'selected="selected"' : '' ?>><?php echo __("On", 'bluepayment-gateway-for-woocommerce') ?></option>
                                    </select>
                                    <p class="description bm-desktop-and-mobile-desc"><?php echo __("Payment by card displayed as a separate radio button on the Store's website", 'bluepayment-gateway-for-woocommerce') ?></p>
                                </fieldset>
                            </td>
                        </tr>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="woocommerce_bluemedia_payment_gateway_installment_<?php echo $currency ?>"><?php echo __("Installment payment", 'bluepayment-gateway-for-woocommerce') ?></label>
							</th>
							<td class="forminp">
								<fieldset>
									<legend class="screen-reader-text"><span><?php echo __("Installment payment", 'bluepayment-gateway-for-woocommerce'); ?></span></legend>
									<select class="select " name="woocommerce_bluemedia_payment_gateway_installment_<?php echo $currency ?>" id="woocommerce_bluemedia_payment_gateway_installment_<?php echo $currency ?>" style="" onchange="loadedPaymentChannelsList = false; $('.bluemedia_download_channels_hide_<?php echo $currency ?>').click();" >
										<option value="no"<?php echo (!empty($this->settings["installment_$currency"]) && $this->settings["installment_$currency"] == 'no') ? 'selected="selected"' : '' ?>><?php echo __("Off", 'bluepayment-gateway-for-woocommerce') ?></option>
										<option value="yes"<?php echo (!empty($this->settings["installment_$currency"]) && $this->settings["installment_$currency"] == 'yes') ? 'selected="selected"' : '' ?>><?php echo __("On", 'bluepayment-gateway-for-woocommerce') ?></option>
									</select>
                                    <p class="description bm-desktop-and-mobile-desc"><?php echo __("Installment payment displayed as a separate radio button on the Store's website\n", 'bluepayment-gateway-for-woocommerce'); ?></p>
								</fieldset>
							</td>
						</tr>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="woocommerce_bluemedia_payment_gateway_smartney_<?php echo $currency ?>"><?php echo __('Smartney payment', 'bluepayment-gateway-for-woocommerce') ?></label>
                            </th>
                            <td class="forminp">
                                <fieldset>

                                    <legend class="screen-reader-text"><span><?php echo __('Smartney payment', 'bluepayment-gateway-for-woocommerce'); ?></span></legend>
                                    <select class="select " name="woocommerce_bluemedia_payment_gateway_smartney_<?php echo $currency ?>" id="woocommerce_bluemedia_payment_gateway_smartney_<?php echo $currency ?>" style="" onchange="loadedPaymentChannelsList = false; $('.bluemedia_download_channels_hide_<?php echo $currency ?>').click();" >
                                        <option value="no"<?php echo (!empty($this->settings["smartney_$currency"]) && $this->settings["smartney_$currency"] == 'no') ? 'selected="selected"' : '' ?>><?php echo __('Off', 'bluepayment-gateway-for-woocommerce') ?></option>
                                        <option value="yes"<?php echo (!empty($this->settings["smartney_$currency"]) && $this->settings["smartney_$currency"] == 'yes') ? 'selected="selected"' : '' ?>><?php echo __('On', 'bluepayment-gateway-for-woocommerce') ?></option>
                                    </select>
                                    <p class="description bm-desktop-and-mobile-desc"><?php echo __("Deferred Payment - Buy now and pay within 30 days. Option button Visible as a separate one on the store's page.", 'bluepayment-gateway-for-woocommerce'); ?></p>
                                </fieldset>
                            </td>
                        </tr>
					<?php endif; ?>
				</table>
				<?php require 'bluemedia-background-payment.php'; ?>
			</fieldset>
		<?php endforeach; ?>
	</td>
</tr>
