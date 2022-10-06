<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Event;

use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Abstracts\Abstract_Event;

class Save_Post extends Abstract_Event {

	/**
	 * @var int
	 */
	private $post_id;

	public function create() {
		add_action( 'save_post',
			function ( $post_id ) {
				$this->arguments = [
					'post_id' => $post_id,
				];
				$this->post_id   = $post_id;
				$this->callback();
			} );
	}

	/**
	 * @return int
	 */
	public function get_post_id(): int {
		return $this->post_id;
	}
}