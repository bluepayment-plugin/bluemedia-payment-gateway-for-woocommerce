<?php
use BlueMedia\OnlinePayments\Model\Gateway;
use BlueMedia\OnlinePayments\Gateway as Gateway_Settings;

?>
<style type="text/css">
    ul.payment_methods li img {
        max-height: 24px !important;
    }
    #bm-channel-list {
        margin: 0;
        padding: 0;
        background-color: #fff;
    }
    #bm-channel-list li {
        display: inline-block;
        width: 24.4%;
        text-align: center;
        vertical-align: top;
        margin-bottom: 24px;
    }
    #bm-channel-list li .bm-channel-list-img {
        text-align: center;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        padding: 5px
    }
    #bm-channel-list li .bm-channel-list-img img {
        width: 40%;
        margin: 0 auto !important;
        max-height: none !important;
    }
    #bm-channel-list li label {
        font-size: 13px;
    }
    .bm-channel-list-img-radio {
        display: none;
    }
    .bm-channel-list-img-radio-name {
        width: 100%
    }
    .bm-channel-list-img-radio:checked + label {
        border: 2px solid #075EBC;
    }
    @media screen and (max-width: 600px) {
        #bm-channel-list li {
            width: 49%;
        }
    }
    .bm-psd2-description {
        padding: 10px;
        font-size: 13px;
    }
    #bluemedia_enclosure {
        font-size: 13px;
    }
</style>
<p><?php echo __($this->description, 'bluepayment-gateway-for-woocommerce') ?></p>
<ul id="bm-channel-list">
    <p class="bm-psd2-description">
        <?php echo __("The payment order is submitted to your bank via Blue Media S.A. based in Sopot and will be processed in accordance with the terms and conditions specified by your bank. Having selected the bank, the payment will be authorised.", 'bluepayment-gateway-for-woocommerce');?>
    </p><?php
    $excluded_gateway = array();
    $excluded_gateway[] = Gateway::GATEWAY_ID_GOOGLE_PAY;

    /** @var string $currentCurrency */
    if ((!empty($this->settings["blik_pbl_$currentCurrency"]) && $this->settings["blik_pbl_$currentCurrency"] === 'yes')
        || (!empty($this->settings["blik_zero_$currentCurrency"]) && $this->settings["blik_zero_$currentCurrency"] === 'yes')
    ) {
        $excluded_gateway[] = Gateway::GATEWAY_ID_BLIK;
    }
    if (!empty($this->settings["installment_$currentCurrency"]) && $this->settings["installment_$currentCurrency"] === 'yes') {
        $excluded_gateway[] = Gateway::GATEWAY_ID_IFRAME;
    }
    if (isset($this->settings["card_" . $currentCurrency])) {
        if (!empty($this->settings["card_$currentCurrency"]) && $this->settings["card_$currentCurrency"] === 'yes') {
            $excluded_gateway[] = Gateway::GATEWAY_ID_CARD;
        }
    }
    if (isset($this->settings["smartney_" . $currentCurrency])) {
        if (isset($wp->query_vars['order-pay'])) {
            $orderId = $wp->query_vars['order-pay'];
            $order = wc_get_order( $orderId );
            $total = $order->get_total();
        } elseif (!empty(WC()->cart)) {
            $total = WC()->cart->total;
        }
        if ($this->settings["smartney_$currentCurrency"] === 'yes' ||
            (isset($total) && $total>=Gateway_Settings::GATEWAY_SMARTNEY_MIN && $total<Gateway_Settings::GATEWAY_SMARTNEY_MAX)) {

            $excluded_gateway[] = Gateway::GATEWAY_ID_SMARTNEY;
        }
    }

    ?>
    <?php $i = 0;
    /** @var array $paymentChannels */
    foreach ($paymentChannels as $bm_channel): $i++; ?>
        <?php if (!in_array($bm_channel['gatewayID'], $excluded_gateway)): ?>
            <li>
                <input class="bm-channel-list-img-radio" type="radio" name="bm_background_payment" value="<?php echo $bm_channel['gatewayID'] ?>"
                       id="bm-payment-channel-<?php echo $bm_channel['gatewayID'] ?>" <?php echo ($i == 1) ? ' checked="checked"' : '' ?> />
                <label class="bm-channel-list-img" for="bm-payment-channel-<?php echo $bm_channel['gatewayID'] ?>" onclick="bluemedia_payment_channel_click(<?php echo $bm_channel['gatewayID'] ?>)">
                    <img src="<?php echo $bm_channel['iconURL'] ?>" alt="<?php echo $bm_channel['gatewayName'] ?>"/>
                    <br/>
                    <p class="bm-channel-list-img-radio-name" for="bm-payment-channel-<?php echo $bm_channel['gatewayID'] ?>">
                        <?php echo $bm_channel['gatewayName'] ?>
                    </p>
                </label>
                <span id="bm-regulation-input-label-<?php echo $bm_channel['gatewayID'] ?>" style="display: none;"><?php echo $bm_channel['regulationInputLabel'] ?></span>
                <input type="hidden" id="bm-regulation-input-id-<?php echo $bm_channel['gatewayID'] ?>" value="<?php echo !empty($bm_channel['regulationID']) ? $bm_channel['regulationID'] : 0; ?>">
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>
<input type="hidden" name="bluemedia_channel_regulation_id" value="0">
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $(".wc_payment_methods").after('<div id="bluemedia_enclosure" style="display:none;" class="form-row place-order"></div>');
        var placeOrder = $("#place_order");
        placeOrder.attr("bluemedia-place-order-text", placeOrder.html());

        jQuery('#payment_method_bluemedia_payment_gateway').click(function () {
            var bm_background_payment_channel_id = $("input[name='bm_background_payment']:checked").val();
            if (typeof bm_background_payment_channel_id !== 'undefined') {
                bluemedia_payment_channel_click(bm_background_payment_channel_id);
            }
        });

        jQuery('[class^="wc_payment_method payment_method_bluemedia_payment_gateway_"]').click(function () {
            jQuery('#bluemedia_enclosure').hide();
            $("#payment").get(0).scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    function bluemedia_payment_channel_click(gatewayID)
    {
        var button = jQuery("#place_order");
        var regulation = jQuery('#bluemedia_enclosure');
        var regulation_input = jQuery('input[name="bluemedia_channel_regulation_id"]');

        if (gatewayID >= 1800) {
            var regulationLabel = jQuery('#bm-regulation-input-label-' + gatewayID);
            regulation.html(regulationLabel.html());
            regulation.show();
            button.html('<?php echo $startPaymentTranslation; ?>');
            regulation_input.val(jQuery('#bm-regulation-input-id-' + gatewayID).val());
        } else {
            regulation_input.val(0);
            regulation.hide();
            button.html(button.attr("bluemedia-place-order-text"));
        }
    }
</script>
