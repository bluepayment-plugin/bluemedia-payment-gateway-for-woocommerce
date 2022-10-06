<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Event;

use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Abstracts\Abstract_Event;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Wc_Product_Aware_Interface;
use WC_Cart;
use WC_Product;

class Wc_Remove_Cart_Item extends Abstract_Event implements Wc_Product_Aware_Interface {

	/**
	 * @var WC_Product
	 */
	private $product;

	public function create() {
		add_action( 'woocommerce_remove_cart_item',
			function ( $cart_item_key, WC_Cart $cart ) {
				$product_id    = $cart->cart_contents[ $cart_item_key ]['product_id'];
				$this->product = wc_get_product( $product_id );
				$this->callback();

			}, 100, 2 );
	}

	/**
	 * @return WC_Product
	 */
	public function get_product(): WC_Product {
		return $this->product;
	}
}
