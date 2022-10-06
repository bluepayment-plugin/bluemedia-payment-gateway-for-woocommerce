<?php
/**
 * @version 1.0.1
 */

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin;

use Exception;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Event_Chain;

//boilerplate main class
abstract class Abstract_Ilabs_Plugin {

	use Tools, Environment;


	private static $config;

	/**
	 * @param array $config
	 *
	 * @return void
	 * @throws Exception
	 */
	public function execute( array $config ) {
		self::$config = $config;
		$this->init_request();
		$this->init_translations();
		$this->before_init();

		add_action( 'init', function () {
			$this->enqueue_scripts();
			$this->init();
		} );

		add_action( 'plugins_loaded', function () use ( $config ) {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				$this->require_wp_core_file( 'wp-admin/includes/plugin.php' );
			}

			$this->plugins_loaded_hooks();
		} );

	}

	/**
	 * @return Request
	 */
	public function get_request(): Request {
		return new Request();
	}

	private function init_request() {
		$request = new Request();
		$request->register_request_filter( new Security_Request_Filter() );

		foreach ( $this->register_request_filters() as $filter ) {
			$request->register_request_filter( $filter );
		}

		$request->build();
	}

	/**
	 * @return Request_Filter_Interface[]
	 */
	protected function register_request_filters(): array {
		return [];
	}

	/**
	 * @return void
	 * @throws Exception
	 */
	private function init_translations() {
		$lang_dir = $this->get_from_config( 'lang_dir' );

		add_action( 'plugins_loaded', function () use ( $lang_dir ) {
			load_plugin_textdomain( $this->get_text_domain(), false,
				$this->get_plugin_basename() . "/$lang_dir/" );
		} );
	}

	private function enqueue_scripts() {
		add_action( 'admin_enqueue_scripts',
			[ $this, 'enqueue_dashboard_scripts' ] );
		add_action( 'wp_enqueue_scripts',
			[ $this, 'enqueue_frontend_scripts' ] );
	}

	/**
	 * @return Alerts
	 */
	public function alerts(): Alerts {
		return new Alerts();
	}

	public function get_event_chain(): Event_Chain {
		return new Event_Chain( $this );
	}

	abstract public function enqueue_frontend_scripts();

	abstract public function enqueue_dashboard_scripts();

	abstract protected function before_init();

	abstract protected function plugins_loaded_hooks();
}
