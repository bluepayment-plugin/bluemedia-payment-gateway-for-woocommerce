<?php

namespace Ilabs\BM_Woocommerce\Domain\Model\White_Label;

class Expandable_Group {

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
	 * @var string
	 */
	private $icon;

	/**
	 * @var string
	 */
	private $subtitle;

	/**
	 * @param Item[] $items
	 * @param string $name
	 * @param string $slug
	 * @param string $icon
	 * @param string $subtitle
	 */
	public function __construct( array $items, string $name, string $slug, string $icon, string $subtitle ) {
		$this->items    = $items;
		$this->name     = $name;
		$this->slug     = $slug;
		$this->icon     = $icon;
		$this->subtitle = $subtitle;
	}


	/**
	 * @return string
	 */
	public function get_title(): string {
		return $this->title;
	}

	/**
	 * @return string
	 */
	public function get_icon(): string {
		return $this->icon;
	}

	/**
	 * @return string
	 */
	public function get_subtitle(): string {
		return $this->subtitle;
	}

	/**
	 * @return Item[]
	 */
	public function get_items(): array {
		return $this->items;
	}

	/**
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function get_slug(): string {
		return $this->slug;
	}

	public function push_item( Item $item ) {
		$items       = $this->items;
		$items[]     = $item;
		$this->items = $items;
	}


}
