<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Event;

use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Abstracts\Abstract_Event;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Wc_Order_Aware_Interface;
use WC_Order;

class Wc_Order_Status_Changed extends Abstract_Event implements Wc_Order_Aware_Interface {

	/**
	 * @var WC_Order
	 */
	private $order;

	/**
	 * @var String
	 */
	private $old_status;

	/**
	 * @var String
	 */
	private $new_status;


	public function create() {
		add_action( 'woocommerce_order_status_changed',
			function ( $order_id, $old_status, $new_status, WC_Order $order ) {
				$this->order      = $order;
				$this->old_status = $old_status;
				$this->new_status = $new_status;

				$this->callback();

			}, 100, 4 );
	}

	public function get_order(): WC_Order {
		return $this->order;
	}

	/**
	 * @return string
	 */
	public function get_old_status(): string {
		return $this->old_status;
	}

	/**
	 * @return string
	 */
	public function get_new_status(): string {
		return $this->new_status;
	}
}
