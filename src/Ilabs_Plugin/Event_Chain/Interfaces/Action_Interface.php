<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces;

use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Event_Chain;

interface Action_Interface {

	public function run();

	public function set_current_event(Event_Interface $event);


}
