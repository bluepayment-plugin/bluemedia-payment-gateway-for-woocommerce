<?php

namespace BmWoocommerceVendor;

if (!\defined('ABSPATH')) {
    exit;
}
?>
<script type="text/javascript">
	var wpdesk_track_deactivation_plugins = <?php 
echo \json_encode($plugins);
?>;
	jQuery("span.deactivate a").click(function(e){
	    var is_tracked = false;
	    var data_plugin = jQuery(this).closest('tr').attr('data-plugin');
	    var href = jQuery(this).attr('href');
        jQuery.each( wpdesk_track_deactivation_plugins, function( key, value ) {
            if ( value == data_plugin ) {
                is_tracked = true;
            }
        });
        if ( is_tracked ) {
            e.preventDefault();
            window.location.href = '<?php 
echo \admin_url('admin.php?page=wpdesk_tracker_deactivate&plugin=');
?>' + '&plugin=' + data_plugin;
        }
	})
</script>
<?php 
