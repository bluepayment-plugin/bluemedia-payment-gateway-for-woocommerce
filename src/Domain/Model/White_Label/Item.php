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
	 * @var string
	 */
	private $class;

	/**
	 * @var string
	 */
	private $script;


	/**
	 * @param string $name
	 * @param string $id
	 * @param string $icon
	 * @param string|null $extra_class
	 * @param string|null $script
	 */
	public function __construct( string $name, string $id, string $icon, ?string $extra_class, ?string $script ) {
		$this->name   = $name;
		$this->id     = $id;
		$this->icon   = $icon;
		$this->class  = $extra_class;
		$this->script = $script;
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

	/**
	 * @return string
	 */
	public function get_class(): ?string {
		return $this->class;
	}

	/**
	 * @param string|null $class
	 */
	public function set_class( ?string $class ): void {
		$this->class = $class;
	}

	/**
	 * @return string
	 */
	public function get_script(): ?string {
		return $this->script;
	}

	/**
	 * @param string|null $script
	 */
	public function set_script( ?string $script ): void {
		$this->script = $script;
	}
}
