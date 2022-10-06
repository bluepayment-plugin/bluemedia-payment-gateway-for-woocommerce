<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Event;

use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Abstracts\Abstract_Event;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Wc_Cart_Aware_Interface;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Wc_Order_Aware_Interface;
use WC_Cart;
use WC_Order;

class Wc_Before_Checkout_Form extends Abstract_Event implements Wc_Cart_Aware_Interface {

	/**
	 * @var WC_Cart
	 */
	private $cart;

	public function create() {
		add_action( 'woocommerce_before_checkout_form',
			function () {
				$this->cart = WC()->cart;
				$this->callback();

			}, 100, 3 );
	}

	public function get_cart(): WC_Cart {
		return $this->cart;
	}
}
