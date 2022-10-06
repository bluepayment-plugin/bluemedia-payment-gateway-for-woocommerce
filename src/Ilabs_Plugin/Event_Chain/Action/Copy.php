<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Action;


use Exception;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Abstracts\Abstract_Action;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Post_Readable_Interface;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Post_Writable_Interface;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Readable_Interface;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Writable_Interface;


class Copy extends Abstract_Action {

	/**
	 * @var Readable_Interface
	 */
	private $source;

	/**
	 * @var Writable_Interface
	 */
	private $destination;



	public function __construct(
		callable $callable_arguments
	) {

		$this->callable_arguments = $callable_arguments;
	}


	/**
	 * @throws Exception
	 */
	public function run() {
		parent::run();

		if ( $this->source instanceof Post_Readable_Interface
		     && ! $this->source->get_post_id() && ! $this->is_event_provide_post_id() ) {
			throw new Exception( 'No source post_id defined.' );
		} elseif ( $this->source instanceof Post_Readable_Interface
		           && ! $this->source->get_post_id() && $this->is_event_provide_post_id() ) {
			$obtained_post_id = $this->get_post_id_from_event();
			$this->source->set_post_id( $obtained_post_id );
		}

		if ( $this->destination instanceof Post_Writable_Interface
		     && ! $this->destination->get_post_id() ) {
			$this->destination->set_post_id( $obtained_post_id );
		}

		$value = $this->source->read();

		if ( ! $this->destination->get_key() ) {
			$dest_key = $this->source->get_key();
		} else {
			$dest_key = $this->destination->get_key();
		}

		$this->destination->write( $dest_key, $value );
	}


	public function get_source(): Readable_Interface {
		return $this->source;
	}

	/**
	 * @param Readable_Interface $source
	 */
	public function set_source( Readable_Interface $source ): void {
		$this->source = $source;
	}

	/**
	 * @param Writable_Interface $destination
	 */
	public function set_destination( Writable_Interface $destination ): void {
		$this->destination = $destination;
	}
}