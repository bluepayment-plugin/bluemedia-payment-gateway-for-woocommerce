<?php

namespace Ilabs\BM_Woocommerce\Data\Remote\Ga4\Dto;

class Payload_DTO implements Payload_DTO_Interface {

	/**
	 * @var string
	 */
	private $event_name;

	/**
	 * @var float
	 */
	private $value;

	/**
	 * @var string
	 */
	private $transaction_id;

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
	 * @var Item_DTO[]
	 */
	private $items;

	public function to_array(): array {
		return [
			'transaction_id' => $this->transaction_id,
			'value'          => $this->value,
			'tax'            => $this->tax,
			'shipping'       => $this->shipping,
			'currency'       => $this->currency,
			'event_name'     => $this->event_name,
			'items'          => ( function () {
				$items = [];
				foreach ( $this->items as $item ) {
					$items[] = $item->to_array();
				}

				return $items;
			} )(),
		];
	}

	public function get_currency_symbol(): string {
		return 'PLN';
	}

	/**
	 * @return string
	 */
	public function get_event_name(): string {
		return $this->event_name;
	}

	/**
	 * @param string $event_name
	 */
	public function set_event_name( string $event_name ): void {
		$this->event_name = $event_name;
	}

	/**
	 * @return array
	 */
	public function get_items(): array {
		return $this->items;
	}

	/**
	 * @param array $items
	 */
	public function set_items( array $items ): void {
		$this->items = $items;
	}

	public function get_value(): ?float {
		return $this->value;
	}

	public function get_shipping(): ?float {
		return $this->shipping;
	}

	public function get_tax(): ?float {
		return $this->tax;
	}

	public function get_transaction_id(): ?string {
		return $this->transaction_id;
	}

	/**
	 * @param float $value
	 */
	public function set_value( float $value ): void {
		$this->value = $value;
	}

	/**
	 * @param string $transaction_id
	 */
	public function set_transaction_id( string $transaction_id ): void {
		$this->transaction_id = $transaction_id;
	}

	/**
	 * @param float $tax
	 */
	public function set_tax( float $tax ): void {
		$this->tax = $tax;
	}

	/**
	 * @param float $shipping
	 */
	public function set_shipping( float $shipping ): void {
		$this->shipping = $shipping;
	}

	/**
	 * @param string $currency
	 */
	public function set_currency( string $currency ): void {
		$this->currency = $currency;
	}
}
