<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Presentation\Interfaces;

interface Field_Decimal_Interface {
	public function get_value(): float;

	public function set_value(float $value);

	public function get_precision():int;

	public function set_precision(int $precision);

}
