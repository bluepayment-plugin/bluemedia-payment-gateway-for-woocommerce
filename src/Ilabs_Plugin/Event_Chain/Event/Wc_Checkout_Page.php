<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Event;

use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Abstracts\Abstract_Event;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Wc_Cart_Aware_Interface;
use WC_Cart;

class Wc_Checkout_Page extends Abstract_Event implements Wc_Cart_Aware_Interface {

	/**
	 * @var WC_Cart|null
	 */
	private $cart;

	public function create() {
		add_action( 'wp',
			function () {
				if ( is_checkout() ) {
					$this->cart = WC()->cart;
					$this->callback();
				}
			} );
	}

	/**
	 * @return WC_Cart
	 */
	public function get_cart(): WC_Cart {
		return $this->cart;
	}
}
