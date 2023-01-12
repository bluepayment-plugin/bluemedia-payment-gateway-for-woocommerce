<?php
namespace Ilabs\BM_Woocommerce\Domain\Model\White_Label;

class Item {

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var string
	 */
	private $icon;

	/**
	 * @param string $name
	 * @param string $id
	 * @param string $icon
	 */
	public function __construct( string $name, string $id, string $icon ) {
		$this->name = $name;
		$this->id   = $id;
		$this->icon = $icon;
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
	public function get_icon(): string {
		return $this->icon;
	}

	/**
	 * @param string $icon
	 */
	public function set_icon( string $icon ): void {
		$this->icon = $icon;
	}
}
