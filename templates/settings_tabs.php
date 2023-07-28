<?php defined( 'ABSPATH' ) || exit; ?>


<div class="bm-settings-tabs" style="display: flex">
	<ul class="subsubsub">

		<?php if ( blue_media()
			           ->get_request()
			           ->get_by_key( 'bmtab' ) === 'channels' ): ?>
			<li>
				<a href="<?php esc_attr_e( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=bluemedia' ) ) ?>">
					<?php _e( 'Settings', 'bm-woocommerce' ) ?></a> |
			</li>
			<li>
				<span class="current"><?php _e( 'Payment gateway list',
						'bm-woocommerce' ) ?></span>
			</li>

		<?php else: ?>
			<li>
				<span class="current"><?php _e( 'Settings',
						'bm-woocommerce' ) ?></span> |
			</li>
			<li>
				<a href="<?php esc_attr_e( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=bluemedia&bmtab=channels' ) ) ?>"><?php _e( 'Payment gateway list',
						'bm-woocommerce' ) ?></a>
			</li>

		<?php endif; ?>


	</ul>

</div>
