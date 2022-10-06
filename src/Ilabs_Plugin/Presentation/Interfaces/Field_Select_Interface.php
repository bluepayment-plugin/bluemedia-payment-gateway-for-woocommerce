<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Presentation\Interfaces;

interface Field_Select_Interface {
	public function get_value(): string;
	public function get_options(): array;


	public function set_values(array $values);
}
