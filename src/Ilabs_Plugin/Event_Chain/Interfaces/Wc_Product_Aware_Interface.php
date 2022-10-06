<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces;

use WC_Product;

interface Wc_Product_Aware_Interface {

	public function get_product(): WC_Product;
}