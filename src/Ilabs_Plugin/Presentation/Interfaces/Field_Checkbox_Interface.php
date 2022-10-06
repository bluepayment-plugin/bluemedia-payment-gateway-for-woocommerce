<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Presentation\Interfaces;

interface Field_Checkbox_Interface {
	public function get_value(): string;

	public function set_value(string $values);
}
