<?php
/**
 * Plugin main class.
 *
 * @package Inspire_Labs\BM_Woocommerce
 */

namespace Inspire_Labs\BM_Woocommerce;

use Exception;

use Inspire_Labs\BM_Woocommerce\Gateway\Blue_Media_Gateway;
use Inspire_Labs\BM_Woocommerce\Plugin\Ilabs_Plugin;

class Plugin extends Ilabs_Plugin
	 {
	const TEXTDOMAIN = 'bm-woocommerce';

	const APP_PREFIX = 'bm_woocommerce';

	public function __construct(

	) {

	}


	public function init() {
		parent::init();
		add_filter( 'woocommerce_get_checkout_order_received_url',
			function ( $return_url, $order ) {
				update_option( 'bm_order_received_url', $return_url );

				return $return_url;
			}, 10, 2 );

		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
		require_once ABSPATH
		             . 'wp-admin/includes/class-wp-filesystem-direct.php';

		if ( is_admin() || wp_doing_ajax() ) {
			//$this->init_admin_features();
		}


		add_action( 'woocommerce_after_register_post_type', function () {
			if ( isset( $_GET['bm_gateway_return'] ) ) {

				$finish_url = get_option( sprintf( 'bm_order_id_%s_finish_url',
					$_GET['OrderID'] ) );
				delete_option( sprintf( 'bm_order_id_%s_finish_url',
					$_GET['OrderID'] ) );

				$order      = wc_get_order( $_GET['OrderID'] );
				$finish_url = $order->get_checkout_order_received_url();
				wp_redirect( $finish_url );
				exit;
			}
		} );

		if ( ! empty( get_option( 'bm_order_received_url' ) )
		     && empty( get_option( 'bm_payment_start' ) ) ) {

			update_option( 'bm_order_received_url', null );
			update_option( 'bm_payment_start', null );
			die;
		}

		$alerts       = new Alerts();
		$last_api_err = get_option( 'bm_api_last_error' );
		if ( ! empty( $last_api_err ) && defined( 'BLUE_MEDIA_DEBUG' ) ) {
			$alerts->add_error( 'Blue Media: ' . $last_api_err );
		}

		$this->init_payment_gateway();
	}

	private function init_payment_gateway() {
		add_filter( 'woocommerce_payment_gateways',
			function ( $gateways ) {
				$gateways[]
					= 'Inspire_Labs\BM_Woocommerce\Gateway\Blue_Media_Gateway';

				return $gateways;
			}
		);

	}


	/**
	 * @return string
	 */
	private function get_admin_script_id(): string {
		return self::APP_PREFIX . '_admin-js';
	}

	/**
	 * @return string
	 */
	private function get_front_script_id(): string {
		return self::APP_PREFIX . '_front-js';
	}

	/**
	 * @return string
	 */
	private function get_admin_css_id(): string {
		return self::APP_PREFIX . '_admin-css';
	}

	public function admin_enqueue_scripts() {

		wp_enqueue_script( $this->get_admin_script_id(),
			$this->get_plugin_assets_url() . '/js/admin.js',
			[ 'jquery' ],
			1.1,
			true );
	}


	/**
	 * @param $log_msg
	 */
	public static function log( $log_msg ) {
		$log_filename = BM_WOOCOMMERCE_PLUGIN_PATH . "logs";

		if ( ! defined( Plugin::APP_PREFIX . '_DEBUG' ) ) {
			return;
		}

		if ( ! file_exists( $log_filename ) ) {
			mkdir( $log_filename, 0777, true );
		}
		$log_file_data = $log_filename . '/bm-woo-log.html';
		file_put_contents( $log_file_data, $log_msg . "\n", FILE_APPEND );
	}

	/**
	 * @param $log
	 */
	public static function write_debug_log( $log ) {
		if ( is_array( $log ) || is_object( $log ) ) {
			error_log( print_r( $log, true ) );
		} else {
			error_log( $log );
		}
	}

	public function wp_enqueue_scripts() {
		wp_enqueue_style( 'bm_front_css',
			BM_PLUGIN_URL . 'assets/css/frontend.css'
		);

		wp_enqueue_script( $this->get_front_script_id(),
				$this->get_plugin_assets_url() . '/js/front.js',
			[ 'jquery' ],
			1.1,
			true );
	}
}



/**
 *  protected function hooks()
{
\add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
\add_action('wp_enqueue_scripts', [$this, 'wp_enqueue_scripts']);
\add_action('plugins_loaded', [$this, 'load_plugin_text_domain']);
\add_filter('plugin_action_links_' . \plugin_basename($this->get_plugin_file_path()), [$this, 'links_filter']);
}
 */
