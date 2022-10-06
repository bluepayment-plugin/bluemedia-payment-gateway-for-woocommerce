<?php

namespace Ilabs\BM_Woocommerce\Data\Remote\Ga4\Dto;

class Purchase_Event_Params_DTO implements Ga4_Dto_Interface {

	/**
	 * @var string
	 */
	private $transaction_id;

	/**
	 * @var float
	 */
	private $value;

	/**
	 * @var float
	 */
	private $tax;

	/**
	 * @var float
	 */
	private $shipping;

	/**
	 * @var string
	 */
	private $currency;

	/**
	 * @var Item_In_Cart_DTO[]
	 */
	private $items;

	public function to_array(): array {
		return [
			'transaction_id' => $this->transaction_id,
			'value'          => $this->transaction_id,
			'tax'            => $this->transaction_id,
			'shipping'       => $this->transaction_id,
			'currency'       => $this->transaction_id,
			'items'          => ( function () {
				$items = [];
				foreach ( $this->items as $item ) {
					$items[] = $item->to_array();
				}

				return $items;
			} )(),
		];
	}


	/**
	 * @return string
	 */
	public function get_transaction_id(): string {
		return $this->transaction_id;
	}

	/**
	 * @param string $transaction_id
	 */
	public function set_transaction_id( string $transaction_id ): void {
		$this->transaction_id = $transaction_id;
	}

	/**
	 * @return float
	 */
	public function get_value(): float {
		return $this->value;
	}

	/**
	 * @param float $value
	 */
	public function set_value( float $value ): void {
		$this->value = $value;
	}

	/**
	 * @return float
	 */
	public function get_tax(): float {
		return $this->tax;
	}

	/**
	 * @param float $tax
	 */
	public function set_tax( float $tax ): void {
		$this->tax = $tax;
	}

	/**
	 * @return float
	 */
	public function get_shipping(): float {
		return $this->shipping;
	}

	/**
	 * @param float $shipping
	 */
	public function set_shipping( float $shipping ): void {
		$this->shipping = $shipping;
	}

	/**
	 * @return string
	 */
	public function get_currency(): string {
		return $this->currency;
	}

	/**
	 * @param string $currency
	 */
	public function set_currency( string $currency ): void {
		$this->currency = $currency;
	}

	/**
	 * @return Item_In_Cart_DTO[]
	 */
	public function get_items(): array {
		return $this->items;
	}

	/**
	 * @param Item_In_Cart_DTO[] $items
	 */
	public function set_items( array $items ): void {
		$this->items = $items;
	}
}
