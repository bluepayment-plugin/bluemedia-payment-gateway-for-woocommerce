<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces;

use WC_Cart;

interface Wc_Cart_Aware_Interface {

	public function get_cart(): WC_Cart;

}
