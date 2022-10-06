<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Condition;

use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Abstracts\Abstract_Condition;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Condition_Interface;

class When_Request_Key_Exist extends Abstract_Condition implements Condition_Interface {

	/**
	 * @var string
	 */
	private $key;

	public function __construct( string $key ) {
		$this->key = $key;
	}

	public function assert(): bool {
		return null !== blue_media()->get_request()->get_by_key( $this->key );
	}

	/**
	 * @return string
	 */
	public function get_key(): string {
		return $this->key;
	}
}
