<?php

namespace Ilabs\BM_Woocommerce\Data\Remote\Ga4\Dto;

class Payload_DTO implements Ga4_Dto_Interface {

	/**
	 * @var string
	 */
	private $event_name;

	/**
	 * @var Ga4_Dto_Interface[]
	 */
	private $items;

	public function to_array(): array {
		return [
			'event_name' => $this->event_name,
			'items'      => ( function () {
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
}
