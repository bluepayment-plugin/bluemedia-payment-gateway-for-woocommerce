<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces;


interface Event_Interface {

	/**
	 * @param Action_Interface[]
	 *
	 * @return mixed
	 */
	public function set_actions( array $callback );


	/**
	 * @param Condition_Interface[] $conditions
	 *
	 * @return mixed
	 */
	public function set_conditions( array $conditions );


	/**
	 * @return Condition_Interface[]
	 */
	public function get_conditions(): ?array;

	/**
	 * @param Action_Interface[]
	 *
	 * @return mixed
	 */
	public function get_actions(): array;

	function create();

}
