<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Event;

use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Abstracts\Abstract_Event;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Wc_Order_Aware_Interface;
use WC_Order;

class Wc_New_Order extends Abstract_Event implements Wc_Order_Aware_Interface {

	/**
	 * @var WC_Order
	 */
	private $order;

	public function create() {
		add_action( 'woocommerce_new_order',
			function ( $order_id ) {
				$this->order = wc_get_order( $order_id );
				$this->callback();
			}, 10, 2 );
	}

	public function get_order(): WC_Order {
		return $this->order;
	}
}
