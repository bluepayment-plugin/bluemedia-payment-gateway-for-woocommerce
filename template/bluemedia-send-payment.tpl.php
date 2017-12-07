<p><?php echo $this->settings['description']; ?></p>
<?php if($this->settings['enabled_gateway'] == 'yes'): ?>
<h4 style="margin-top: 15px;">Wybierz kanał płatności</h4>
<ul style="list-style: none; margin: 0;">
    <?php foreach ($gateways as $row): ?>
        <li style="display: inline-block; margin-left: 7px; margin-right: 7px;">
            <div class='bluepayment_select_gateway_id' data-id="<?php echo $row['gateway_id']; ?>" style="cursor: pointer;">
                <img src="<?php echo $row['gateway_logo_url']; ?>" width="50" height="50" style="max-height: 100%;" title="<?php echo $row['gateway_type']; ?>">
            </div>
        </li>
    <?php endforeach; ?>
</ul>
<input type="hidden" value="" name="payment_method_bluemedia_payment_gateway_id" id="payment_method_bluemedia_payment_gateway_id"/>
<script> jQuery(document).ready(function(){jQuery('.bluepayment_select_gateway_id').on('click', function(e){ jQuery('#payment_method_bluemedia_payment_gateway_id').val(jQuery(this).data('id')); jQuery('.bluepayment_select_gateway_id img').css({'border': 'none'}); jQuery(this).find('img').css({'border': '1px solid red'}); })});</script>
<?php endif; ?>



