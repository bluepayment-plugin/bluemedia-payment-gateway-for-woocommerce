<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Abstracts;

use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Condition_Interface;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Event_Interface;

class Abstract_Condition implements Condition_Interface {

	/**
	 * @var Event_Interface
	 */
	protected $current_event;

	/**
	 * @var ?callable
	 */
	protected $callable_arguments;


	public function set_current_event( Event_Interface $event ) {
		$this->current_event = $event;
	}

	public function assert(): bool {
		$callable = $this->callable_arguments;

		if ( ! $this->current_event ) {
			return $callable();
		} else {
			return $callable( $this->current_event);
		}
	}
}
