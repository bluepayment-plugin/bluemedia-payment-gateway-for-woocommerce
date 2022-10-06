<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Event;

use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Abstracts\Abstract_Event;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Wc_Product_Aware_Interface;
use WC_Product;

class Wc_Add_To_Cart extends Abstract_Event implements Wc_Product_Aware_Interface {

	/**
	 * @var WC_Product
	 */
	private $product;

	/**
	 * @var int
	 */
	private $quantity;


	public function create() {
		add_action( 'woocommerce_add_to_cart',
			function ( $cart_item_key, $product_id, $quantity ) {
				$this->product  = wc_get_product( $product_id );
				$this->quantity = $quantity;
				$this->callback();

			}, 100, 3 );
	}

	/**
	 * @return WC_Product
	 */
	public function get_product(): WC_Product {
		return $this->product;
	}

	/**
	 * @return int
	 */
	public function get_quantity(): int {
		return $this->quantity;
	}
}
