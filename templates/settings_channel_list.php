<?php defined( 'ABSPATH' ) || exit; ?>

<div class="bm-settings-channel-list" style="display: flex">

	<?php
	$channels = blue_media()->get_blue_media_gateway()->gateway_list( true );

	blue_media()
		->get_blue_media_gateway()
		->render_channels_for_admin_panel( $channels );

	?>


</div>
