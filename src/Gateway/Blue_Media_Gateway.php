<?php

namespace Inspire_Labs\BM_Woocommerce\Gateway;

use Exception;
use Inspire_Labs\BM_Woocommerce\Plugin;
use SimpleXMLElement;
use WC_Order;
use WC_Payment_Gateway;

class Blue_Media_Gateway extends WC_Payment_Gateway {

	/**
	 * Whether or not logging is enabled
	 *
	 * @var bool
	 */
	public static $log_enabled = false;

	/**
	 * @var string
	 */
	private $gateway_url;

	/**
	 * @var string
	 */
	private $service_id;


	/**
	 * Class constructor, more about it in Step 3
	 *
	 * @throws Exception
	 */
	public function __construct() {


		$this->id           = 'bluemedia';
		$this->icon
		                    = BM_WOOCOMMERCE_PLUGIN_URL
		                      . 'assets/img/logo-blue-media.svg';
		$this->has_fields
		                    = true;
		$this->method_title = __( 'Blue Media',
			Plugin::TEXTDOMAIN );
		$this->method_description
		                    = __( 'Description of Blue Media payment gateway',
			Plugin::TEXTDOMAIN );

		$this->supports = [
			'products',
		];


		$this->init_form_fields();
		$this->init_settings();

		$this->gateway_url     = $this->get_option( 'gateway_host' );
		$this->title           = $this->get_option( 'title' );
		$this->description     = $this->get_option( 'description' );
		$this->enabled         = $this->get_option( 'enabled' );
		$this->testmode        = 'yes' === $this->get_option( 'testmode' );
		$this->private_key     = $this->testmode
			? $this->get_option( 'test_private_key' )
			: $this->get_option( 'private_key' );
		$this->publishable_key = $this->testmode
			? $this->get_option( 'test_publishable_key' )
			: $this->get_option( 'publishable_key' );
		$this->service_id      = $this->testmode
			? $this->get_option( 'test_service_id' )
			: $this->get_option( 'service_id' );


		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id,
			[ $this, 'process_admin_options' ] );

		if ( is_object( WC()->session ) && ! wp_doing_ajax() ) {
			if ( ! empty( WC()->session->get( 'bm_order_payment_params' ) ) ) {

				$params
					= WC()->session->get( 'bm_order_payment_params' )['params'];

				WC()->session->set( 'bm_order_payment_params', null );
				WC()->session->save_data();


				if ( is_array( $params ) ) {
					printf( "<form method='post' id='paymentForm' action='%s'>
			 <input type='hidden' name='ServiceID'  value='%s' />
			 <input type='hidden' name='OrderID'  value='%s' />
			 <input type='hidden' name='Amount'  value='%s' />
			 <input type='hidden' name='Description'  value='%s' />
			 <input type='hidden' name='GatewayID'  value='%s' />
			 <input type='hidden' name='Hash'  value='%s' /></form>
<script type='text/javascript'>
        document.getElementById('paymentForm').submit();
    </script>",
						$this->gateway_url . 'payment',
						$params['ServiceID'],
						$params['OrderID'],
						$params['Amount'],
						$params['Description'],
						! empty( $params['GatewayID'] ) ? $params['GatewayID'] : '0',
						$params['Hash'] );
				}
				update_post_meta( $params['OrderID'],
					'bm_transaction_init_params', $params );
				update_option( 'bm_payment_start', '1' );
			}


		}


		$this->webhook();


	}

	/**
	 * Logging method.
	 *
	 * @param string $message Log message.
	 * @param string $level Optional. Default 'info'. Possible values:
	 *                        emergency|alert|critical|error|warning|notice|info|debug.
	 */
	public static function log( $message, $level = 'info' ) {
		if ( self::$log_enabled ) {
			if ( empty( self::$log ) ) {
				self::$log = wc_get_logger();
			}
			self::$log->log( $level, $message, [ 'source' => 'paypal' ] );
		}
	}


	/**
	 * @return void
	 */
	public function init_form_fields() {
		$this->form_fields = [
			'whitelabel'       => [
				'title'       => __( 'Gateway mode',
					Plugin::TEXTDOMAIN ),
				'label'       => __( 'Enable whitelabel mode',
					Plugin::TEXTDOMAIN ),
				'type'        => 'checkbox',
				'description' => __( 'Gateway mode',
					Plugin::TEXTDOMAIN ),
				'default'     => 'no',
				'desc_tip'    => true,
			],
			'title'            => [
				'title'    => __( 'Title',
					Plugin::TEXTDOMAIN ),
				'type'     => 'text',
				'default'  => __( 'Blue Media gateway',
					Plugin::TEXTDOMAIN ),
				'desc_tip' => true,
			],
			'description'      => [
				'title'   => __( 'Description',
					Plugin::TEXTDOMAIN ),
				'type'    => 'textarea',
				'default' => __( 'Lorem Ipsum',
					Plugin::TEXTDOMAIN ),
			],
			'testmode'         => [
				'title'       => __( 'Sandbox mode',
					Plugin::TEXTDOMAIN ),
				'label'       => __( 'Enable Sandbox mode',
					Plugin::TEXTDOMAIN ),
				'type'        => 'checkbox',
				'description' => __( 'If you do not select this the shop will run on production',
					Plugin::TEXTDOMAIN ),
				'default'     => 'yes',
				'desc_tip'    => true,
			],
			'test_service_id'  => [
				'title'       => __( 'Test Service ID',
					Plugin::TEXTDOMAIN ),
				'description' => __( 'It contains only numbers. It is different for each shop',
					Plugin::TEXTDOMAIN ),
				'type'        => 'text',
			],
			'test_private_key' => [
				'title'       => __( 'Test Private Key',
					Plugin::TEXTDOMAIN ),
				'description' => __( 'Contains numbers and lowercase letters. It is used to verify communication with the payment gateway. It should not be made available to the public',
					Plugin::TEXTDOMAIN ),
				'type'        => 'password',
			],
			'service_id'       => [
				'title'       => __( 'Service ID',
					Plugin::TEXTDOMAIN ),
				'description' => __( 'It contains only numbers. It is different for each shop',
					Plugin::TEXTDOMAIN ),
				'type'        => 'text',
			],
			'private_key'      => [
				'title'       => __( 'Production private Key',
					Plugin::TEXTDOMAIN ),
				'description' => __( 'Contains numbers and lowercase letters. It is used to verify communication with the payment gateway. It should not be made available to the public',
					Plugin::TEXTDOMAIN ),
				'type'        => 'password',
			],
			'gateway_host'     => [
				'title'       => __( 'Gateway host',
					Plugin::TEXTDOMAIN ),
				'description' => __( 'For a store operating in production mode the address is: https://pay.bm.pl/ and for test mode: https://pay-accept.bm.pl/',
					Plugin::TEXTDOMAIN ),
				'type'        => 'text',
			],
		];

	}


	/**
	 * @return void
	 * @throws Exception
	 */
	public function payment_fields() {
		if ( 'yes' === $this->get_option( 'whitelabel' ) ) {
			$this->render_channels( $this->gateway_list() );
		}
	}

	/**
	 * @param $order_id
	 *
	 * @return array
	 */
	public function process_payment( $order_id ) {
		global $woocommerce;

		$payment_channel = (int) $_POST['bm-payment-channel'] ?? null;

		$params = [
			'params' => $this->prepare_initial_transaction_parameters(
				wc_get_order( $order_id ), $payment_channel
			),
		];


		WC()->session->set( 'bm_order_payment_params', $params );


		$order = wc_get_order( $order_id );

		return [
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		];


	}

	/**
	 * @return void
	 */
	public function webhook() {

		add_action( 'woocommerce_api_wc_gateway_bluemedia', function () {
			try {

				if ( ! empty( $_POST ) ) {
					$posted                  = wp_unslash( $_POST );
					$posted_xml              = simplexml_load_string( base64_decode( $posted['transactions'] ) );
					$all_fields_itn          = [];
					$all_fields_reponse      = [];
					$order_success_to_update = [];
					$order_failure_to_update = [];
					$order_pending_to_update = [];

					$xw = xmlwriter_open_memory();
					xmlwriter_set_indent( $xw, 1 );
					$res = xmlwriter_set_indent_string( $xw, ' ' );

					xmlwriter_start_document( $xw, '1.0', 'UTF-8' );


					xmlwriter_start_element( $xw, 'confirmationList' );
					xmlwriter_start_element( $xw, 'serviceID' );
					xmlwriter_text( $xw, $this->service_id );
					xmlwriter_end_element( $xw ); // serviceID
					xmlwriter_start_element( $xw, 'transactionsConfirmations' );

					foreach (
						$posted_xml->xpath( '/transactionList/transactions/transaction' )
						as $transaction
					) {

						/**
						 * @var SimpleXMLElement $field
						 */
						foreach ( $transaction as $field ) {

							$fieldString = ( (string) $field );
							if ( ! empty( $field )
							) {
								if ( $field->getName() == 'customerData' ) {
									$customer_data_fields = (array) $field;
									foreach ( $customer_data_fields as $value ) {
										$all_fields_itn[] = $value;
									}
								} else {
									$all_fields_itn[] = $fieldString;
								}
							}
						}


						$wc_order_id     = (int) (string) $transaction->orderID;
						$bm_order_status = (string) $transaction->paymentStatus;
						$init_params     = get_post_meta( $wc_order_id,
							'bm_transaction_init_params', true );

						if ( ! is_array( $init_params ) ) {
							throw new Exception(
								'Blue Media Woocommerce webhook error - transaction (ID: '
								. $wc_order_id . ') does not contain BlueMedia gateway data' );
						}

						/**
						 * @var $bm_order_status SimpleXMLElement
						 */
						if ( ! self::validate_itn_params( $transaction,
							$init_params ) ) {
							ob_start();
							header( 'HTTP/1.0 401 Unauthorized' );
							echo __( 'validate_itn_params - not valid',
								Plugin::APP_PREFIX );
							exit;
						}

						xmlwriter_start_element( $xw, 'transactionConfirmed' );
						xmlwriter_start_element( $xw, 'orderID' );
						xmlwriter_text( $xw, $wc_order_id );
						$all_fields_reponse[] = $wc_order_id;
						xmlwriter_end_element( $xw ); // orderID
						xmlwriter_start_element( $xw, 'confirmation' );
						xmlwriter_text( $xw, 'CONFIRMED' );
						$all_fields_reponse[] = 'CONFIRMED';
						xmlwriter_end_element( $xw ); // confirmation
						xmlwriter_end_element( $xw ); // transactionConfirmed
						$wc_order = wc_get_order( $wc_order_id );

						if ( 'SUCCESS' === $bm_order_status ) {
							$order_success_to_update[] = $wc_order;
						}

						if ( 'PENDING' === $bm_order_status ) {
							$order_pending_to_update[] = $wc_order;
						}

						if ( 'FAILURE' === $bm_order_status ) {
							$order_failure_to_update[] = $wc_order;
						}

					}

					$hash_from_itn = $posted_xml->xpath( '/transactionList/hash' );
					$hash_from_itn = (string) $hash_from_itn[0];
					$is_hash_valid = $this->validate_itn_hash( $all_fields_itn,
						$hash_from_itn );

					if ( ! $is_hash_valid ) {
						ob_start();
						header( 'HTTP/1.0 401 Unauthorized' );
						echo __( 'validate_itn_hash - not valid',
							Plugin::APP_PREFIX );
						exit;
					}

					foreach ( $order_success_to_update as $wc_order ) {
						$wc_order->set_status( 'completed' );
						$wc_order->add_order_note( 'PayBM ITN: paymentStatus SUCCESS' );
						$wc_order->save();
					}

					foreach ( $order_pending_to_update as $wc_order ) {
						$wc_order->set_status( 'pending' );
						$wc_order->add_order_note( 'PayBM ITN: paymentStatus PENDING' );
						$wc_order->save();
					}

					foreach ( $order_failure_to_update as $wc_order ) {
						$wc_order->set_status( 'failed' );
						$wc_order->add_order_note( 'PayBM ITN: paymentStatus FAILURE' );
						$wc_order->save();
					}


					xmlwriter_end_element( $xw ); // transactionsConfirmations
					xmlwriter_start_element( $xw, 'hash' );
					xmlwriter_text( $xw,
						$this->generate_response_xml_hash( $all_fields_reponse ) );
					xmlwriter_end_element( $xw ); // hash
					xmlwriter_end_document( $xw );
					echo xmlwriter_output_memory( $xw );

					exit;//exit with 200
				}
			} catch ( Exception $e ) {
				error_log( print_r( $e->getMessage(), true ) );
				die( 'Message: ' . $e->getMessage() . ' Code: ' . $e->getCode() );
			}
		} );

	}


	/**
	 * @param object $transaction_params_from_itn
	 * @param array $transaction_params_from_shop
	 *
	 * @return bool
	 */
	static private function validate_itn_params(
		object $transaction_params_from_itn,
		array $transaction_params_from_shop
	): bool {
		if ( (string) $transaction_params_from_shop['OrderID'] !== (string) $transaction_params_from_itn->orderID ) {
			return false;
		}
		if ( (string) $transaction_params_from_shop['Amount'] !== (string) $transaction_params_from_itn->amount ) {
			return false;
		}

		if ( ! empty( $params['GatewayID'] ) && (string) $transaction_params_from_itn->gatewayID !== (string) $params['GatewayID'] ) {
			return false;

		}

		return true;

	}

	/**
	 * @param array $all_fields_reponse
	 *
	 * @return string
	 */
	private function generate_response_xml_hash( array $all_fields_reponse
	): string {
		array_unshift( $all_fields_reponse, $this->service_id );

		return $this->hash_transaction_parameters( $all_fields_reponse );
	}

	/**
	 * @param array $transactions_from_itn
	 * @param $hash_from_itn
	 *
	 * @return bool
	 */
	private function validate_itn_hash(
		array $transactions_from_itn,
		$hash_from_itn
	): bool {
		array_unshift( $transactions_from_itn,
			$this->service_id );
		$itn_values_based_hash = $this->hash_transaction_parameters( $transactions_from_itn );

		return $hash_from_itn === $itn_values_based_hash;
	}

	/**
	 * @param WC_Order $wc_order
	 * @param int $payment_channel
	 *
	 * @return array
	 */
	private
	function prepare_initial_transaction_parameters(
		WC_Order $wc_order,
		int $payment_channel = 0
	): array {
		$params = [
			'ServiceID'   => $this->service_id,
			'OrderID'     => $wc_order->get_id(),
			'Amount'      => $wc_order->get_total(),
			'Description' => 'description test',
		];
		$params['GatewayID'] = $payment_channel;

		$params_hash = $this->hash_transaction_parameters(
			$params
		);

		return array_merge( $params, [ 'Hash' => $params_hash ] );
	}

	/**
	 * @param array $params
	 *
	 * @return string
	 */
	private
	function hash_transaction_parameters(
		array $params
	): string {
		return hash( 'sha256', implode( '|', $params ) . '|'
		                       . $this->get_private_key() );
	}

	/**
	 * @return string
	 */
	private
	function get_private_key() {
		return $this->private_key;
	}

	/**
	 * @throws Exception
	 */
	public
	function gateway_list(): array {

		if ( time()
		     - (int) get_option( 'bm_gateway_list_cache_time' )
		     > 600//10 minutes cache
		) {
			$gateway_list_cache = $this->api_get_gateway_list();

			update_option( 'bm_gateway_list_cache', $gateway_list_cache );
			update_option( 'bm_gateway_list_cache_time', time() );
		} else {
			$gateway_list_cache = get_option( 'bm_gateway_list_cache' );
			if ( empty( $gateway_list_cache ) ) {
				$gateway_list_cache = $this->api_get_gateway_list();
				update_option( 'bm_gateway_list_cache', $gateway_list_cache );
				update_option( 'bm_gateway_list_cache_time', time() );
			} else {
				update_option( 'bm_api_last_error', '' );
			}
		}

		return $gateway_list_cache;
	}

	/**
	 * @throws Exception
	 */
	private
	function api_get_gateway_list(): ?array {
		$service_id = $this->service_id;
		$message_id = substr( bin2hex( random_bytes( 32 ) ), 32 );
		$currencies = 'PLN';

		$params = [
			'ServiceID'  => $service_id,
			'MessageID'  => $message_id,
			'Currencies' => $currencies,
		];


		$params_hash = $this->hash_transaction_parameters(
			$params
		);

		$params = array_merge( $params, [ 'Hash' => $params_hash ] );

		$url = $this->gateway_url . 'gatewayList/v2';

		$result = wp_remote_post(
			$url,
			[
				'headers' => [
					'content-type' => 'application/json',
				],
				'body'    => json_encode( $params ),
			]
		);

		if ( is_wp_error( $result ) ) {
			update_option( 'bm_api_last_error',
				sprintf( '[%s] [BlueMedia debug] [URL: %s] [Message: %s]',
					date( "Y-m-d H:i:s", time() ), $url,
					$result->get_error_message()
				)
			);
		}

		$result_decoded = json_decode( wp_remote_retrieve_body( $result ) );

		if ( is_object( $result_decoded )
		     && property_exists( $result_decoded,
				'result' )
		     && $result_decoded->result === 'ERROR' ) {
			update_option( 'bm_api_last_error',
				sprintf( '[%s server time] [BlueMedia debug] [URL: %s] [Message: %s]',
					date( "Y-m-d H:i:s", time() ), $url,
					$result_decoded->description
				)
			);

			return [];
		}

		if ( is_object( $result_decoded ) && property_exists( $result_decoded,
				'gatewayList' ) ) {

			if ( empty( $result_decoded->gatewayList ) ) {
				update_option( 'bm_api_last_error',
					sprintf( '[%s server time] [BlueMedia debug] [URL: %s] [Empty results: %s]',
						date( "Y-m-d H:i:s", time() ), $url,
						serialize( $result_decoded )
					)
				);

				return [];
			}

			return $result_decoded->gatewayList;
		}

		update_option( 'bm_api_last_error',
			sprintf( '[%s server time] [BlueMedia debug] [URL: %s] [Failed decode results: %s]',
				date( "Y-m-d H:i:s", time() ), $url,
				serialize( $result )
			)
		);

		return [];
	}

	/**
	 * @param array $channels
	 *
	 * @return void
	 * @throws Exception
	 */
	private
	function render_channels(
		array $channels
	) {

		echo '<div class="bm-payment-channels-wrapper">';
		echo '<ul id="shipping_method" class="woocommerce-shipping-methods">';

		$output  = '';
		$grouped = [];
		foreach ( $channels as $channel ) {
			$channelHtml = sprintf( '
<li class="bm-payment-channel-item"><input
type="radio"
name="bm-payment-channel"
onclick="addCurrentClass()"
data-index="0" id="bm-gateway-id-%s" class="shipping_method" value="%s">
<span class="easypack-shipping-method-logo">
		<img style="" src="%s">
		</span>
<label for="bm-gateway-id-%s">%s</label>
		</li>',
				$channel->gatewayID,
				$channel->gatewayID,
				$channel->iconURL,
				$channel->gatewayID,
				$channel->gatewayName

			);

			$grouped[ $channel->gatewayType ][] = $channelHtml;
		}
		$other_groups_key = 6;
		$grouped_sorted   = $grouped;
		foreach ( $grouped as $k => $group ) {
			if ( $k === 'BLIK' ) {
				$this->repositionArrayElement( $grouped_sorted, $k, 0 );
			} elseif ( $k === 'Karta płatnicza' ) {
				$this->repositionArrayElement( $grouped_sorted, $k, 1 );
			} elseif ( $k === 'Portfel elektroniczny' ) {
				$this->repositionArrayElement( $grouped_sorted, $k, 2 );
			} elseif ( $k === 'FR' ) {
				$this->repositionArrayElement( $grouped_sorted, $k, 3 );
			} elseif ( $k === 'Raty online' ) {
				$this->repositionArrayElement( $grouped_sorted, $k, 4 );
			}
		}

		foreach ( $grouped_sorted as $k => $group ) {
			printf( "<div class='bm-group-%s'><h5>%s</h5><li><ul>",
				sanitize_title( $k ), $k );
			foreach ( $group as $item ) {
				printf( $item );
			}
			printf( "</li></ul></div>" );
		}
		echo '</ul></div>';
	}

	/**
	 * Reposition an array element by its key.
	 *
	 * @param array $array The array being reordered.
	 * @param string|int $key They key of the element you want to reposition.
	 * @param int $order The position in the array you want to move the element
	 *     to. (0 is first)
	 *
	 * @throws \Exception
	 */
	private function repositionArrayElement(
		array &$array,
		$key,
		int $order
	): void {
		if ( ( $a = array_search( $key, array_keys( $array ) ) ) === false ) {
			throw new \Exception( "The {$key} cannot be found in the given array." );
		}
		$p1    = array_splice( $array, $a, 1 );
		$p2    = array_splice( $array, 0, $order );
		$array = array_merge( $p2, $p1, $array );
	}
}
