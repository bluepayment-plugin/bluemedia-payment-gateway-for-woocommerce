<?php

namespace Ilabs\BM_Woocommerce\Domain\Service\Ga4;

use Ilabs\BM_Woocommerce\Data\Remote\Ga4\Dto\Payload_DTO;

interface Ga4_Use_Case_Interface {

	public function get_ga4_payload_array(): array;

	public function get_ga4_payload_dto(): Payload_DTO;

	public function get_event_name(): string;
}
