<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Event;

use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Abstracts\Abstract_Event;

class Wp extends Abstract_Event{
	public function create() {
		add_action( 'wp',
			function () {
				$this->callback();
			} );
	}
}
