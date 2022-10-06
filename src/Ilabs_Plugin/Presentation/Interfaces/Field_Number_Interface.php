<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Presentation\Interfaces;

interface Field_Number_Interface {
	public function get_value(): int;

	public function set_value(int $value);
}
