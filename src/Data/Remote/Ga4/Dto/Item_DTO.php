<?php

namespace Ilabs\BM_Woocommerce\Data\Remote\Ga4\Dto;

class Item_DTO implements Ga4_Dto_Interface{
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
	 * @var float
	 */
	private $price;

	/**
	 * @var int
	 */
	private $quantity;

	public function to_array(): array {
		return [
			'id'       => $this->id,
			'name'     => $this->name,
			'brand'    => $this->brand,
			'category' => $this->category,
			'variant'  => $this->variant,
			'price'    => $this->price,
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
	public function get_brand(): ?string {
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
}
