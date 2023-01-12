<?php

namespace Ilabs\BM_Woocommerce\Domain\Service\Ga4;

use Ilabs\BM_Woocommerce\Data\Remote\Ga4\Dto\Item_DTO;
use Ilabs\BM_Woocommerce\Data\Remote\Ga4\Dto\Item_In_Cart_DTO;

abstract class Abstract_Ga4_Use_Case {

	/**
	 * @param Item_DTO[] $items
	 *
	 * @return void
	 */
	protected function recalculate_value( array $items ): float {
		$value = 0;
		foreach ( $items as $item ) {
			$value += $item->get_price() * $item->get_quantity();
		}

		return $value;
	}
}
