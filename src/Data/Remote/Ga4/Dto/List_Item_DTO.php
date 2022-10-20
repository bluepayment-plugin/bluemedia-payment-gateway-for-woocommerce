<?php

namespace Ilabs\BM_Woocommerce\Data\Remote\Ga4\Dto;

class List_Item_DTO implements Ga4_Dto_Interface {

	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $list_name;

	/**
	 * @var string
	 */
	private $brand;

	/**
	 * @var string
	 */
	private $category;

	/**
	 * @var string
	 */
	private $variant;

	/**
	 * @var int
	 */
	private $list_position;

	/**
	 * @var int
	 */
	private $quantity;

	/**
	 * @var float
	 */
	private $price;

	public function to_array(): array {
		return [
			'id'            => $this->id,
			'name'          => $this->name,
			'brand'         => $this->brand,
			'category'      => $this->category ?: '',
			'variant'       => $this->variant,
			'quantity'      => $this->quantity,
			'price'         => $this->price,
			'list_position' => $this->list_position,
		];
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

	/**
	 * @return string
	 */
	public function get_list_name(): string {
		return $this->list_name;
	}

	/**
	 * @param string $list_name
	 */
	public function set_list_name( string $list_name ): void {
		$this->list_name = $list_name;
	}

	/**
	 * @return string
	 */
	public function get_brand(): string {
		return $this->brand;
	}

	/**
	 * @param string|null $brand
	 */
	public function set_brand( ?string $brand ): void {
		$this->brand = $brand;
	}

	/**
	 * @return string
	 */
	public function get_category(): ?string {
		return $this->category;
	}

	/**
	 * @param string|null $category
	 */
	public function set_category( ?string $category ): void {
		$this->category = $category;
	}

	/**
	 * @return string
	 */
	public function get_variant(): string {
		return $this->variant;
	}

	/**
	 * @param string $variant
	 */
	public function set_variant( string $variant ): void {
		$this->variant = $variant;
	}

	/**
	 * @return int
	 */
	public function get_list_position(): int {
		return $this->list_position;
	}

	/**
	 * @param int $list_position
	 */
	public function set_list_position( int $list_position ): void {
		$this->list_position = $list_position;
	}

	/**
	 * @return int
	 */
	public function get_quantity(): int {
		return $this->quantity;
	}

	/**
	 * @param int $quantity
	 */
	public function set_quantity( int $quantity ): void {
		$this->quantity = $quantity;
	}

	/**
	 * @return float
	 */
	public function get_price(): float {
		return $this->price;
	}

	/**
	 * @param float $price
	 */
	public function set_price( float $price ): void {
		$this->price = $price;
	}
}
