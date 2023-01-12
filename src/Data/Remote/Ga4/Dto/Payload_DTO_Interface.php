<?php

namespace Ilabs\BM_Woocommerce\Data\Remote\Ga4\Dto;

interface Payload_DTO_Interface {
	public function get_value(): ?float;
	public function get_currency_symbol(): string;
	public function get_items(): array;
	public function get_shipping(): ?float;
	public function get_tax(): ?float;
	public function get_transaction_id(): ?string;


}