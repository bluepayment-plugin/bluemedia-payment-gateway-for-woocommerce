<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Abstracts;

use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Event_Chain;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Action_Interface;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Event_Interface;

abstract class Abstract_Action implements Action_Interface {

	/**
	 * @var Event_Interface
	 */
	protected $current_event;

	/**
	 * @var callable
	 */
	protected $callable_arguments;

	/**
	 * @depracated
	 * @return bool
	 */
	protected function is_event_provide_post_id(): bool {
		return false;
	}

	/**
	 * @return int
	 * @depracated
	 */
	public function get_post_id_from_event(): int {
		return 0;
	}

	public function run() {
		$callable = $this->callable_arguments;
		$callable( $this->current_event );
	}

	public function set_current_event( Event_Interface $event ) {
		$this->current_event = $event;
	}
}
