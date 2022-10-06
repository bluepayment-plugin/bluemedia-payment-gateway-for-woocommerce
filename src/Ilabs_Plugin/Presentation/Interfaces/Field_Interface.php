<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Presentation\Interfaces;

interface Field_Interface {

	public function get_id(): string;

	public function set_id( string $id );
}
