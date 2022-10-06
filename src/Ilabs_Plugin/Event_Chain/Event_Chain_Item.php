<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain;


use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Action_Interface;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Condition_Interface;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Event_Interface;

class Event_Chain_Item {

	/**
	 * @var Event_Interface
	 */
	private $event;

	/**
	 * @var Condition_Interface[]
	 */
	private $conditions_before_event;

	/**
	 * @var Condition_Interface[]
	 */
	private $conditions_inside_event;

	/**
	 * @var Action_Interface[]
	 */
	private $actions;

	/**
	 * @return Event_Interface
	 */
	public function get_event(): ?Event_Interface {
		return $this->event;
	}

	/**
	 * @param Event_Interface $event
	 */
	public function set_event( Event_Interface $event ): void {
		$this->event = $event;
	}

	/**
	 * @return array
	 */
	public function get_conditions_before_event(): ?array {
		return $this->conditions_before_event;
	}

	/**
	 * @param array $conditions_before_event
	 */
	public function set_conditions_before_event( array $conditions_before_event ): void {
		$this->conditions_before_event = $conditions_before_event;
	}

	/**
	 * @return Action_Interface[]
	 */
	public function get_actions(): ?array {
		return $this->actions;
	}

	/**
	 * @param Action_Interface[] $actions
	 */
	public function set_actions( array $actions ): void {
		$this->actions = $actions;
	}

	/**
	 * @return Condition_Interface[]
	 */
	public function get_conditions_inside_event(): ?array {
		return $this->conditions_inside_event;
	}

	/**
	 * @param Condition_Interface[] $conditions_inside_event
	 */
	public function set_conditions_inside_event( array $conditions_inside_event
	): void {
		$this->conditions_inside_event = $conditions_inside_event;
	}
}
