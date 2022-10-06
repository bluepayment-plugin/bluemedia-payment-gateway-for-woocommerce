<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Event;

use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Abstracts\Abstract_Event;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Wc_Product_Aware_Interface;
use WC_Product;

class Wc_Before_Shop_Loop_Item extends Abstract_Event implements Wc_Product_Aware_Interface {

	/**
	 * @var WC_Product
	 */
	private $product;

	public function create() {
		add_action( 'woocommerce_before_shop_loop_item',
			function () {
				global $product;
				$this->product   = $product;
				$this->callback();

			}, 100 );
	}

	/**
	 * @return WC_Product
	 */
	public function get_product(): WC_Product {
		return $this->product;
	}
}
