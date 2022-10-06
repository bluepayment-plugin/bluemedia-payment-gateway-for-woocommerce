<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Condition;

use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Abstracts\Abstract_Condition;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Condition_Interface;

class When_Is_Product extends Abstract_Condition implements Condition_Interface {

	public function assert(): bool {
		return is_shop();
	}
}