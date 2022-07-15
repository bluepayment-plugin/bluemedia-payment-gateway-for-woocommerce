<?php
/**
 * Plugin main class.
 *
 * @package Inspire_Labs\BM_Woocommerce
 */

namespace Inspire_Labs\BM_Woocommerce;

use BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin\Deactivateable;
use \Exception;
use Inspire_Labs\BM_Woocommerce\Api_Client\Ping_Machine_Api;
use Inspire_Labs\BM_Woocommerce\Api_Server\Ping_Export_Offers_Factory;
use Inspire_Labs\BM_Woocommerce\Api_Server\Ping_Export_Products_Factory;
use Inspire_Labs\BM_Woocommerce\Api_Server\Ping_Import_Orders_Factory;
use Inspire_Labs\BM_Woocommerce\Gateway\Blue_Media_Gateway;
use Inspire_Labs\BM_Woocommerce\Offer\Offer_Actions_Factory;
use Inspire_Labs\BM_Woocommerce\Order\Order_Details_Factory;
use Inspire_Labs\BM_Woocommerce\Wp_Admin\Connection_Test_Factory;
use Inspire_Labs\BM_Woocommerce\Offer\Offers_Exporter_Factory;
use Inspire_Labs\BM_Woocommerce\Order\Order_Importer_Factory as Orders_Importer_Factory;
use Inspire_Labs\BM_Woocommerce\Order\Status_Factory;
use Inspire_Labs\BM_Woocommerce\Product\Product_Exporter_Factory;
use Inspire_Labs\BM_Woocommerce\Wp_Admin\Products_List_Mod_Factory;
use Inspire_Labs\BM_Woocommerce\Wp_Admin\Settings;
use Inspire_Labs\BM_Woocommerce\Wp_Admin\Settings_Ids;
use Inspire_Labs\BM_Woocommerce\Wp_Admin\Settings_Static_Factory;
use Inspire_Labs\BM_Woocommerce\Utils\Settings_Helper;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;


use BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin\AbstractPlugin;
use BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin\HookableCollection;
use BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin\HookableParent;
use BmWoocommerceVendor\WPDesk_Plugin_Info;
use function False\true;
use const Automattic\Jetpack\Creative_Mail\PLUGIN_FILE;


/**
 * Main plugin class. The most important flow decisions are made here.
 *
 * @package WPDesk\PluginTemplate
 */
class Plugin extends AbstractPlugin
	implements LoggerAwareInterface, HookableCollection, Deactivateable {

	use LoggerAwareTrait;
	use HookableParent;

	const TEXTDOMAIN = 'bm-woocommerce';

	const APP_PREFIX = 'bm_woocommerce';

	public static $plugin_dir;

	/**
	 * Plugin constructor.
	 *
	 * @param WPDesk_Plugin_Info $plugin_info Plugin info.
	 */
	public function __construct(
		\BmWoocommerceVendor\WPDesk_Plugin_Info $plugin_info
	) {
		parent::__construct( $plugin_info );
		$this->setLogger( new NullLogger() );

		$this->plugin_url       = $this->plugin_info->get_plugin_url();
		$this->plugin_namespace = $this->plugin_info->get_text_domain();

		self::$plugin_dir = $this->get_plugin_file_path();
	}

	/**
	 * Initializes plugin external state.
	 *
	 * The plugin internal state is initialized in the constructor and the
	 * plugin should be internally consistent after creation. The external
	 * state includes hooks execution, communication with other plugins,
	 * integration with WC etc.
	 *
	 * @return void
	 */
	public function init() {
		parent::init();

		add_filter( 'woocommerce_get_checkout_order_received_url',
			function ( $return_url, $order ) {
				update_option( 'bm_order_received_url', $return_url );

				return $return_url;

			}, 10, 2 );


		$this->customize_wpdesk_boilerplate();

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

	private function customize_wpdesk_boilerplate() {
		add_action( 'admin_menu', function () {
			remove_menu_page( 'wpdesk-helper' );
		}, 100 );

		add_filter( 'wpdesk_show_plugin_activation_notice', function ( $bool ) {
			return false;
		} );

	}

	/**
	 * @throws Exception
	 */
	public function hooks() {
		parent::hooks();
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
	 * Short description
	 *
	 * @return Settings_Helper
	 */
	public static function get_settings(): Settings_Helper {
		return new Settings_Helper();
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

	public function deactivate() {
		update_option( Plugin::APP_PREFIX . '_is_registered', false );
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
