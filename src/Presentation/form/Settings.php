<?php

use Ilabs\BM_Woocommerce\Ilabs_Plugin\Presentation\Interfaces\Group_Interface;

class Settings implements Group_Interface {

	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @param string $id
	 * @param string $name
	 */
	public function __construct( string $id, string $name ) {
		$this->id   = $id;
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function get_id(): string {
		return $this->id;
	}

	/**
	 * @param string $id
	 */
	public function set_id( string $id ): void {
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function set_name( string $name ): void {
		$this->name = $name;
	}
}
