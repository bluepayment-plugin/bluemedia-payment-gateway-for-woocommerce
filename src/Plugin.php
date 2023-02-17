<?php

namespace Ilabs\BM_Woocommerce;

use Exception;
use Ilabs\BM_Woocommerce\Data\Remote\Ga4_Service_Client;
use Ilabs\BM_Woocommerce\Domain\Service\Ga4\Add_Product_To_Cart_Use_Case;
use Ilabs\BM_Woocommerce\Domain\Service\Ga4\Click_On_Product_Use_Case;
use Ilabs\BM_Woocommerce\Domain\Service\Ga4\Complete_Transation_Use_Case;
use Ilabs\BM_Woocommerce\Domain\Service\Ga4\Ga4_Use_Case_Interface;
use Ilabs\BM_Woocommerce\Domain\Service\Ga4\Init_Checkout_Use_Case;
use Ilabs\BM_Woocommerce\Domain\Service\Ga4\Remove_Product_From_Cart_Use_Case;
use Ilabs\BM_Woocommerce\Domain\Service\Ga4\View_Product_On_List_Use_Case;
use Isolated\BlueMedia\Ilabs\Ilabs_Plugin\Abstract_Ilabs_Plugin;
use Isolated\BlueMedia\Ilabs\Ilabs_Plugin\Alerts;
use Isolated\BlueMedia\Ilabs\Ilabs_Plugin\Event_Chain\Event\Wc_Add_To_Cart;
use Isolated\BlueMedia\Ilabs\Ilabs_Plugin\Event_Chain\Event\Wc_Order_Status_Changed;
use Isolated\BlueMedia\Ilabs\Ilabs_Plugin\Event_Chain\Event\Wc_Remove_Cart_Item;
use Isolated\BlueMedia\Ilabs\Ilabs_Plugin\Event_Chain\Interfaces\Wc_Cart_Aware_Interface;
use Isolated\BlueMedia\Ilabs\Ilabs_Plugin\Event_Chain\Interfaces\Wc_Order_Aware_Interface;
use Isolated\BlueMedia\Ilabs\Ilabs_Plugin\Event_Chain\Interfaces\Wc_Product_Aware_Interface;
use WC_Session_Handler;

class Plugin extends Abstract_Ilabs_Plugin {

	/**
	 * @var string
	 */
	private $blue_media_currency;


	/**
	 * @throws Exception
	 */
	protected function before_init() {
		$this->implement_ga4();
		$this->implement_settings_modal();
		$this->implement_settings_banner();

		add_action( 'bm_cancel_failed_pending_order_after_one_hour', function ( $order_id ) {
			$order = wc_get_order( $order_id );
			wp_clear_scheduled_hook( 'bm_cancel_failed_pending_order_after_one_hour', [ $order_id ] );
			if ( $order->has_status( [ 'pending' ] ) ) {
				$order->update_status( 'cancelled' );
				$order->add_order_note( __( 'Unpaid order cancelled - time limit reached.', 'bm-woocommerce' ) );
				$order->save();
			}

		} );


	}

	/**
	 * @throws Exception
	 */
	public function enqueue_frontend_scripts() {
		wp_enqueue_style( $this->get_plugin_prefix() . '_front_css',
			$this->get_plugin_css_url() . '/frontend.css'
		);

		wp_enqueue_script( $this->get_plugin_prefix() . '_front_js',
			$this->get_plugin_js_url() . '/front.js',
			[ 'jquery' ],
			1.1,
			true );

		$ga4_tracking_id = ( new Ga4_Service_Client() )->get_tracking_id();

		if ( $ga4_tracking_id ) {
			wp_enqueue_script( $this->get_plugin_prefix() . '_ga4',
				"https://www.googletagmanager.com/gtag/js?id=$ga4_tracking_id",
				[],
				1.1,
				true );

			wp_localize_script( $this->get_plugin_prefix() . '_front_js',
				'blueMedia',
				[
					'ga4TrackingId' => $ga4_tracking_id,
				]
			);
		}
	}


	/**
	 * @throws Exception
	 */
	public function enqueue_dashboard_scripts() {

		$current_screen = get_current_screen();

		if ( is_a( $current_screen, 'WP_Screen' ) && 'woocommerce_page_wc-settings' === $current_screen->id ) {
			if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'checkout' ) {
				if ( isset( $_GET['section'] ) && $_GET['section'] == 'bluemedia' ) {

					wp_enqueue_script( $this->get_plugin_prefix() . '_admin_js',
						$this->get_plugin_js_url() . '/admin.js',
						[ 'jquery' ],
						1.1,
						true );

					wp_enqueue_style( $this->get_plugin_prefix() . '_admin_css',
						$this->get_plugin_css_url() . '/admin.css'
					);
				}
			}
		}
	}



	/**
	 * @throws Exception
	 */
	private function implement_settings_banner() {
		$banner = blue_media()->get_event_chain();
		$banner
			->on_wc_before_settings( 'checkout' )
			->when_request_value_equals( 'section', 'bluemedia' )
			->action_output_template( 'settings_banner.php' )
			->execute();
	}

	/**
	 * @throws Exception
	 */
	private function implement_settings_modal() {
		$settings_modal = blue_media()->get_event_chain();
		$settings_modal
			->on_wp_admin_footer()
			->action( function () {
				$current_screen = get_current_screen();

				if ( is_a( $current_screen, 'WP_Screen' ) && 'woocommerce_page_wc-settings' === $current_screen->id ) {
					if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'checkout' ) {
						if ( isset( $_GET['section'] ) && $_GET['section'] == 'bluemedia' ) {
							echo '<div class="bm-modal-content">
                                    <span class="bm-close">&times;</span>
                                    <p>Google Analytics 4</p>
                                <ul>
                                <li>' . __( 'Go to "Administrator" in the lower left corner.', 'bm-woocommerce' ) . '</li>
                                <li>' . __( 'In the "Services" section, click "Data Streams".', 'bm-woocommerce' ) . '</li>
                                <li>' . __( 'Click the name of the data stream.', 'bm-woocommerce' ) . '</li>
                                <li>' . __( 'Your measurement ID is in the upper right corner (eg G-QCX4K9GSPC).',
									'bm-woocommerce' ) . '</li>
                                </ul>    
                                
                                  </div><div class="bm-modal-overlay"></div>';
						}
					}
				}
			} )->execute();

	}

	/**
	 * @return void
	 * @throws Exception
	 */
	private function implement_ga4() {

		$ga4_Service_Client = new Ga4_Service_Client();

		if ( ! $ga4_Service_Client->get_tracking_id()
		     || ! $ga4_Service_Client->get_client_id()
		     || ! $ga4_Service_Client->get_api_secret() ) {
			return;
		}

		$ga4                      = blue_media()->get_event_chain();
		$ga4_task_queue           = $ga4->get_wc_session_cache( 'ga4_tasks' );
		$ga4_list_items_dto_queue = $ga4->get_wc_session_cache( 'ga4_list_items_dto_queue' );

		$ga4
			->on_wp()
			->when_is_frontend()
			->action( function () use ( $ga4_task_queue ) {
				$ga4_task_queue->clear();
			} )
			->on_wc_before_shop_loop_item()
			->when_is_shop()
			->action( function ( Wc_Product_Aware_Interface $product_aware_interface ) use ( $ga4_list_items_dto_queue
			) {
				//view_item_list
				$ga4_list_items_dto_queue->push(
					( new View_Product_On_List_Use_Case( $product_aware_interface->get_product() ) )->create_dto() );

			} )
			->on_wc_before_single_product()
			->action( function ( Wc_Product_Aware_Interface $product_aware_interface ) use ( $ga4_task_queue ) {
				//view_item
				$ga4_task_queue->push(
					( new Ga4_Service_Client )->view_item_event_export_array(
						( new Click_On_Product_Use_Case( $product_aware_interface->get_product() ) )
					) );
			} )
			->on_wc_add_to_cart()
			->action( function ( Wc_Add_To_Cart $event ) use ( $ga4_task_queue ) {
				//add_to_cart
				( new Ga4_Service_Client() )->add_to_cart_event( new Add_Product_To_Cart_Use_Case( $event->get_product(),
					$event->get_quantity() ) );
			} )
			->on_wc_remove_cart_item()
			->action( function ( Wc_Remove_Cart_Item $event ) use ( $ga4_task_queue ) {
				//remove_from_cart
				( new Ga4_Service_Client() )->remove_from_cart_event( new Remove_Product_From_Cart_Use_Case
				( $event->get_product(), $event->get_quantity() ) );
			} )
			->on_wc_checkout_page()
			->when_is_not_ajax()
			->action( function ( Wc_Cart_Aware_Interface $cart_aware_interface ) use ( $ga4_task_queue ) {
				//begin_checkout
				if ( $cart_aware_interface->get_cart()->get_cart_contents_count() > 0 ) {
					$ga4_task_queue->push(
						( new Ga4_Service_Client )->init_checkout_event_export_array(
							( new Init_Checkout_Use_Case( $cart_aware_interface->get_cart() ) )
						) );
				}
			} )
			->on_wc_order_status_changed()
			->when( function ( Wc_Order_Status_Changed $event ) {
				return $event->get_new_status() === 'completed';
			} )
			->action( function ( Wc_Order_Aware_Interface $order_aware_interface ) {
				//purchase
				( new Ga4_Service_Client() )->purchase_event( new Complete_Transation_Use_Case( $order_aware_interface->get_order() ) );
			} )
			->on_wp_footer()
			->when_is_not_ajax()
			->when_is_frontend()
			->action( function () use ( $ga4_task_queue, $ga4_list_items_dto_queue ) {
				if ( $ga4_list_items_dto_queue->get() ) {
					$view_Product_On_List_Use_Case = new View_Product_On_List_Use_Case( null );
					$payload                       = $view_Product_On_List_Use_Case->get_ga4_payload_dto();
					$payload->set_items( $ga4_list_items_dto_queue->get() );
					$view_Product_On_List_Use_Case->set_payload( $payload );
					$ga4_task_queue->push( ( new Ga4_Service_Client() )->view_item_list_event_export_array( $view_Product_On_List_Use_Case ) );
					$ga4_list_items_dto_queue->clear();
				}

				if ( $ga4_task_queue->get() ) {
					echo "<script>var blue_media_ga4_tasks = '" . wp_json_encode( $ga4_task_queue->get() ) . "'</script>";
					$ga4_task_queue->clear();
				}

			} )->execute();
	}

	public function test_form() {

	}

	protected function plugins_loaded_hooks() {

	}


	/**
	 * @throws Exception
	 */
	public function init() {
		$this->blue_media_currency = $this->resolve_blue_media_currency_symbol();

		if ( ! $this->blue_media_currency ) {
			$alerts = new Alerts();
			$msg    = sprintf(
				__( 'The selected currency is not supported by the Blue Media payment gateway. The gateway has been disabled',
					'bm-woocommerce' )
			);
			$alerts->add_error( 'Blue Media: ' . $msg );

			return;
		}

		add_filter( 'woocommerce_get_checkout_order_received_url',
			function ( $return_url, $order ) {
				$this->update_payment_cache( 'bm_order_received_url', $return_url );

				return $return_url;
			}, 10, 2 );

		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
		require_once ABSPATH
		             . 'wp-admin/includes/class-wp-filesystem-direct.php';

		add_action( 'template_redirect', [ $this, 'return_redirect_handler' ] );

		add_filter( 'woocommerce_cancel_unpaid_order',
			[ $this, 'bm_woocommerce_cancel_unpaid_order_filter' ], 10, 2 );


		if (!is_admin()){
			if ( ! empty( $this->get_from_payment_cache( 'bm_order_received_url' ) )
			     && empty( $this->get_from_payment_cache( 'bm_payment_start' ) ) ) {

				$this->update_payment_cache( 'bm_order_received_url', null );
				$this->update_payment_cache( 'bm_payment_start', null );
			}
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
					= 'Ilabs\BM_Woocommerce\Gateway\Blue_Media_Gateway';

				return $gateways;
			}
		);

	}


	/**
	 * [JIRA] (WOOCOMERCE-17) Błąd przekierowania
	 */
	public function return_redirect_handler() {
		if ( isset( $_GET['bm_gateway_return'] ) ) {

			$order = null;

			if ( isset( $_GET['OrderID'] ) ) {
				$order = wc_get_order( $_GET['OrderID'] );
			}

			if ( isset( $_GET['key'] ) ) {
				$order_id = wc_get_order_id_by_order_key( $_GET['key'] );
				$order    = wc_get_order( $order_id );
			}

			if ( $order ) {
				$finish_url = $order->get_checkout_order_received_url();
				wp_redirect( $finish_url );
				exit;
			}
		}
	}

	/**
	 * [JIRA] (WOOCOMERCE-26) Błędnie przydzielane statusy dla transkacji nie
	 * opłaconych w ciągu godziny.
	 */
	public function bm_woocommerce_cancel_unpaid_order_filter(
		$string,
		$order
	) {
		if ( 'bluemedia' === $order->get_payment_method() ) {
			return false;
		}

		return $string;
	}

	/**
	 * @throws Exception
	 */
	public function update_payment_cache( string $key, $value ) {
		$session = WC()->session;
		if ( ! $session ) {
			$session = new WC_Session_Handler();
			$session->init();
		}
		$session->set( $this->get_from_config( 'slug' ) . '_' . $key, $value );
	}

	/**
	 * @throws Exception
	 */
	public function get_from_payment_cache( string $key ) {
		$session = WC()->session;
		if ( ! $session ) {
			$session = new WC_Session_Handler();
			$session->init();
		}

		return $session->get( $this->get_from_config( 'slug' ) . '_' . $key );
	}

	public function resolve_blue_media_currency_symbol(): ?string {
		switch ( get_woocommerce_currency() ) {
			case 'EUR':
				return 'EUR';
			case 'RON':
				return 'RON';
			case 'HUF':
				return 'HUF';
			case 'CZK':
				return 'CZK';
			case 'PLN':
				return 'PLN';
			default:
				return null;
		}
	}

	/**
	 * @return string
	 */
	public function get_blue_media_currency(): string {
		return $this->blue_media_currency;
	}
}
