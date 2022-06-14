jQuery( document ).on( 'click', '.notice-dismiss', function() {
    var notice_name = jQuery(this).closest('div.notice').data('notice-name');
    var source = jQuery(this).closest('div.notice').data('source');
    if ('' !== notice_name) {
        jQuery.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                action: 'wpdesk_notice_dismiss',
                notice_name: notice_name,
                source: source,
            },
            success: function (response) {
            }
        });
    }
});

jQuery( document ).on( 'click', '.notice-dismiss-link', function() {
    jQuery(this).closest('div.notice').data('source',jQuery(this).data('source'));
    jQuery(this).closest('div.notice').find('.notice-dismiss').click();
});
