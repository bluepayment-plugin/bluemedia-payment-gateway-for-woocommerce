jQuery(document).ready(function () {
	let checkbox = jQuery('#woocommerce_bluemedia_testmode');

	setupView('checked' === checkbox.attr('checked'));

	jQuery(checkbox.change(function () {
		setupView(this.checked);
	}));

	function setupView(isChecked) {
		if (true === isChecked) {
			jQuery('#woocommerce_bluemedia_service_id').addClass('bm-disabled').prop("disabled", true);
			jQuery('#woocommerce_bluemedia_private_key').addClass('bm-disabled').prop("disabled", true);
			jQuery('#woocommerce_bluemedia_test_service_id').addClass('bm-disabled').prop("disabled", false);
			jQuery('#woocommerce_bluemedia_test_private_key').addClass('bm-disabled').prop("disabled", false);
		} else {
			jQuery('#woocommerce_bluemedia_service_id').removeClass('bm-disabled').prop("disabled", false);
			jQuery('#woocommerce_bluemedia_private_key').removeClass('bm-disabled').prop("disabled", false);
			jQuery('#woocommerce_bluemedia_test_service_id').removeClass('bm-disabled').prop("disabled", true);
			jQuery('#woocommerce_bluemedia_test_private_key').removeClass('bm-disabled').prop("disabled", true);
		}
	}
});
