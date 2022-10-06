<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces;

interface Post_Writable_Interface {

	public function get_post_id();

	public function get_key(): string;
}
