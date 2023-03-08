<?php

namespace Ilabs\BM_Woocommerce\Domain\Model\White_Label;

class Group {

	/**
	 * @var Item[]
	 */
	private $items;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $slug;


	/**
	 * @param Item[] $items
	 * @param string $name
	 * @param string $slug
	 */
	public function __construct( array $items, string $name, string $slug = '' ) {
		$this->items = $items;
		$this->name  = $name;
		$this->slug  = $slug;
	}

	/**
	 * @return Item[]
	 */
	public function get_items(): array {
		return $this->items;
	}

	/**
	 * @param Item[] $items
	 */
	public function set_items( array $items ): void {
		$this->items = $items;
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
	public function get_slug(): string {
		return $this->slug;
	}

	/**
	 * @param string $slug
	 */
	public function set_slug( string $slug ): void {
		$this->slug = $slug;
	}

	public function push_item( Item $item ) {
		$items       = $this->items;
		$items[]     = $item;
		$this->items = $items;
	}


}
