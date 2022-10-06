<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces;

interface Condition_Interface {

	public function assert(): bool;

	public function set_current_event(Event_Interface $event);

}