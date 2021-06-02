<div class="row">
    <div class="col-md-6">
        <div id="blik_content_form_entry">
            <label>
                <?php echo __("Please insert BLIK code.", 'bluepayment-gateway-for-woocommerce') ?>:
                <input
                    type="text"
                    name="bluemedia_blik_code"
                    id="blik_code"
                    onkeyup="this.value = this.value.replace(/\D/g, '')"
                    maxlength="6"
                    style="margin-bottom: 12px; color: #666; background: #fff; border: 1px solid #bbb; border-radius: 3px; display: inline-block; padding: 0.7em; width: 150px; margin-left: 20px;"/>
            </label>
        </div>
        <div id="bm-spinner" class="bm-spinner" style="display: none;">
            <div class="rect1"></div>
            <div class="rect2"></div>
            <div class="rect3"></div>
            <div class="rect4"></div>
            <div class="rect5"></div>
        </div>
        <div id="blik_content_check_status"></div>
    </div>
</div>
