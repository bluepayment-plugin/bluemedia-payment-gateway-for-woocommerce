<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Presentation\Interfaces;

interface Group_Interface {

	public function get_id(): string;

	public function get_name(): string;

	public function set_name( string $name );

	public function set_id( string $id );
}
