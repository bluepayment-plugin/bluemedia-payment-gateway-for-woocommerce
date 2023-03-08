<?php

namespace Ilabs\BM_Woocommerce\Gateway;

use Exception;
use Ilabs\BM_Woocommerce\Domain\Model\White_Label\Expandable_Group;
use Ilabs\BM_Woocommerce\Domain\Model\White_Label\Expandable_Group_Interface;
use Ilabs\BM_Woocommerce\Domain\Model\White_Label\Group;
use Ilabs\BM_Woocommerce\Domain\Service\White_Label\Group_Mapper;
use Ilabs\BM_Woocommerce\Plugin;
use SimpleXMLElement;
use WC_Order;
use WC_Payment_Gateway;

class Blue_Media_Gateway extends WC_Payment_Gateway {

	const GATEWAY_PRODUCTION = 'https://pay.bm.pl/';

	const GATEWAY_SANDBOX = 'https://pay-accept.bm.pl/';

	/**
	 * @var string
	 */
	private $gateway_url;

	/**
	 * @var string
	 */
	private $service_id;

	/**
	 * @var bool
	 */
	private $testmode;


	/**
	 *
	 * @throws Exception
	 */
	public function __construct() {
		$this->id           = 'bluemedia';
		$this->icon
		                    = blue_media()->get_plugin_images_url()
		                      . '/logo-blue-media.svg';
		$this->has_fields
		                    = true;
		$this->method_title = __( 'Instant payment',
			'bm-woocommerce' );
		$this->method_description
		                    = __( 'Description of Blue Media payment gateway',
			'bm-woocommerce' );

		$this->supports = [
			'products',
		];
		//tracking ID
		$this->init_form_fields();
		$this->init_settings();

		$this->title       = $this->get_option( 'title' );
		$this->description = $this->get_option( 'description' );
		$this->enabled     = $this->get_option( 'enabled' );
		$this->testmode    = 'yes' === $this->get_option( 'testmode', 'no' );

		$this->gateway_url     = $this->testmode
			? self::GATEWAY_SANDBOX
			: self::GATEWAY_PRODUCTION;
		$this->private_key     = $this->testmode
			? $this->get_option( 'test_private_key' )
			: $this->get_option( 'private_key' );
		$this->publishable_key = $this->testmode
			? $this->get_option( 'test_publishable_key' )
			: $this->get_option( 'publishable_key' );
		$this->service_id      = $this->testmode
			? $this->get_option( 'test_service_id' )
			: $this->get_option( 'service_id' );


		//var_dump($this->private_key);die;

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id,
			[ $this, 'process_admin_options' ] );

		if ( is_object( WC()->session ) && ! wp_doing_ajax() ) {
			if ( ! empty( WC()->session->get( 'bm_order_payment_params' ) ) ) {

				$params
					= WC()->session->get( 'bm_order_payment_params' )['params'];

				WC()->session->set( 'bm_order_payment_params', null );
				WC()->session->save_data();

				/*
				 * PlatformName (nazwa platofrmy, np. PrestaShop)

PlatformVersion (wersja platformy, np. 1.7.1.6)

PlatformPluginVersion (wersja wtyczki zainstalowanej na platformie)
				 */

				if ( is_array( $params ) ) {
					printf( "<form method='post' id='paymentForm' action='%s'>
			 <input type='hidden' name='ServiceID'  value='%s' />
			 <input type='hidden' name='OrderID'  value='%s' />
			 <input type='hidden' name='Amount'  value='%s' />
			 <input type='hidden' name='GatewayID'  value='%s' />
			 <input type='hidden' name='Currency'  value='%s' />
			 <input type='hidden' name='CustomerEmail'  value='%s' />
			 <input type='hidden' name='PlatformName'  value='%s' />
			 <input type='hidden' name='PlatformVersion'  value='%s' />
			 <input type='hidden' name='PlatformPluginVersion'  value='%s' />
			 <input type='hidden' name='Hash'  value='%s' /></form>
<script type='text/javascript'>
        document.getElementById('paymentForm').submit();
    </script>",
						$this->gateway_url . 'payment',
						$params['ServiceID'],
						$params['OrderID'],
						$params['Amount'],
						! empty( $params['GatewayID'] ) ? $params['GatewayID'] : '0',
						blue_media()->resolve_blue_media_currency_symbol(),
						$params['CustomerEmail'],
						$params['PlatformName'],
						$params['PlatformVersion'],
						$params['PlatformPluginVersion'],
						$params['Hash'] );
				}
				update_post_meta( $params['OrderID'],
					'bm_transaction_init_params', $params );
				blue_media()->update_payment_cache( 'bm_payment_start', '1' );
			}


		}


		$this->webhook();
	}


	/**
	 * @return void
	 * @throws Exception
	 */
	public function init_form_fields() {
		$this->form_fields = [
			'whitelabel'      => [
				'title'       => __( 'Gateway mode',
					'bm-woocommerce' ),
				'label'       => __( 'Enable whitelabel mode',
					'bm-woocommerce' ),
				'type'        => 'checkbox',
				'description' => __( 'Gateway mode',
					'bm-woocommerce' ),
				'default'     => 'no',
				'desc_tip'    => true,
			],
			'title'           => [
				'title'    => __( 'Title',
					'bm-woocommerce' ),
				'type'     => 'text',
				'default'  => __( 'Blue Media gateway',
					'bm-woocommerce' ),
				'desc_tip' => true,
			],
			'description'     => [
				'title'   => __( 'Description',
					'bm-woocommerce' ),
				'type'    => 'textarea',
				'default' => __( 'Lorem Ipsum',
					'bm-woocommerce' ),
			],
			'testmode_header' => [
				'title' => __( 'Test environment',
					'bm-woocommerce' ),
				'type'  => 'title',
			],
			'testmode'        => [
				'title'       => __( 'Sandbox mode',
					'bm-woocommerce' ),
				'label'       => __( 'Enable Sandbox mode',
					'bm-woocommerce' ),
				'type'        => 'radio',
				'default'     => 'no',
				'options'     => [
					'yes' => __( 'Yes', 'bm-woocommerce' ),
					'no'  => __( 'No', 'bm-woocommerce' ),
				],
				'description' => __( 'It allows you to check the module\'s operation without having to pay for the order (no order fees are charged in the test mode).',
					'bm-woocommerce' ),
				'desc_tip'    => true,
			],

			'testmode_info' => [
				'title'       => __( '',
					'bm-woocommerce' ),
				'description' => "<span class='p-info'>" . __( 'The service ID and shared key for the test environment are different from production data.',
						'bm-woocommerce' )
				                 . '<br>' . __( 'To get the data for the test environment,',
						'bm-woocommerce' ) . '<a href="https://developers.bluemedia.pl/kontakt?mtm_campaign=woocommerce_developers_formularz&mtm_source=woocommerce_backoffice&mtm_medium=hiperlink">'
				                 . ' ' . __( 'please contact us.', 'bm-woocommerce' ) . '</a></span>',
				'type'        => 'title',
			],


			'test_service_id'  => [
				'title'       => __( 'Test Service ID',
					'bm-woocommerce' ),
				'description' => __( 'It contains only numbers. It is different for each shop.', 'bm-woocommerce' ),
				'type'        => 'text',
			],
			'test_private_key' => [
				'title'       => __( 'Test Private Key',
					'bm-woocommerce' ),
				'description' => __( 'Contains numbers and lowercase letters. It is used to verify communication with the payment gateway. It should not be made available to the public',
					'bm-woocommerce' ),
				'type'        => 'password',
			],
			'service_id'       => [
				'title'       => __( 'Service ID',
					'bm-woocommerce' ),
				'description' => __( 'It contains only numbers. It is different for each shop',
					'bm-woocommerce' ),
				'type'        => 'text',
			],
			'private_key'      => [
				'title'       => __( 'Production private Key',
					'bm-woocommerce' ),
				'description' => __( 'Contains numbers and lowercase letters. It is used to verify communication with the payment gateway. It should not be made available to the public',
					'bm-woocommerce' ),
				'type'        => 'password',
			],


			'ga4_tracking_id'                 => [
				'title'       => __( 'Google Analytics Tracking ID',
					'bm-woocommerce' ),
				'description' => ( function () {
					$desc           = __( 'The identifier is in the format G-XXXXXXX.', 'bm-woocommerce' );
					$desc_link_text = __( 'Where can I find the Measurement ID?', 'bm-woocommerce' );

					return "$desc <a class='bm_ga_help_modal' href='#'>$desc_link_text</a>";
				} )(),
				'type'        => 'text',
			],
			'ga4_api_secret'                  => [
				'title'       => __( 'Google Analytics Api secret',
					'bm-woocommerce' ),
				'description' => ( function () {
					$desc           = __( 'The identifier is in the format G-XXXXXXX.', 'bm-woocommerce' );
					$desc_link_text = __( 'Where can I find the Measurement ID?', 'bm-woocommerce' );

					return "$desc <a class='bm_ga_help_modal' href='#'>$desc_link_text</a>";
				} )(),
				'type'        => 'password',
			],
			'ga4_client_id'                   => [
				'title'       => __( 'Google Analytics Client ID',
					'bm-woocommerce' ),
				'description' => ( function () {
					$desc           = __( 'The identifier is in the format G-XXXXXXX.', 'bm-woocommerce' );
					$desc_link_text = __( 'Where can I find the Measurement ID?', 'bm-woocommerce' );

					return "$desc <a class='bm_ga_help_modal' href='#'>$desc_link_text</a>";
				} )(),
				'type'        => 'text',
			],
			'wc_payment_statuses'             => [
				'title'       => __( 'Payment statuses',
					'bm-woocommerce' ),
				'description' => __( '',
					'bm-woocommerce' ),
				'type'        => 'title',
			],
			'wc_payment_status_on_bm_pending' => [
				'title'       => __( 'Payment pending',
					'bm-woocommerce' ),
				'description' => __( '',
					'bm-woocommerce' ),
				'type'        => 'select',
				'options'     => wc_get_order_statuses(),
				'default'     => 'wc-pending',
			],
			'wc_payment_status_on_bm_success' => [
				'title'       => __( 'Payment success',
					'bm-woocommerce' ),
				'description' => __( '',
					'bm-woocommerce' ),
				'type'        => 'select',
                'options'     => array_merge(array('' => 'Auto'), wc_get_order_statuses()),
                'default'     => '',
			],
			'wc_payment_status_on_bm_failure' => [
				'title'       => __( 'Payment failure',
					'bm-woocommerce' ),
				'description' => __( '',
					'bm-woocommerce' ),
				'type'        => 'select',
				'options'     => wc_get_order_statuses(),
				'default'     => 'wc-failed',
			],

		];

	}


	/**
	 * Generate Select HTML.
	 *
	 * @param string $key Field key.
	 * @param array $data Field data.
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function generate_radio_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$defaults  = [
			'title'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => [],
			'options'           => [],
		];

		$data  = wp_parse_args( $data, $defaults );
		$value = $this->get_option( $key );

		ob_start();
		?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?><?php echo $this->get_tooltip_html( $data ); // WPCS: XSS ok. ?></label>
            </th>
            <td class="forminp">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span>
                    </legend>
					<?php foreach ( (array) $data['options'] as $option_key => $option_value ) : ?>
				<?php if ( is_array( $option_value ) ) : ?>

                    <optgroup label="<?php echo esc_attr( $option_key ); ?>">
						<?php foreach ( $option_value as $option_key_inner => $option_value_inner ) : ?>
                            <label for="<?php echo esc_attr( $option_key ); ?>"><?php echo esc_html( $option_value_inner ); ?></label>
                            <input id="<?php echo esc_attr( $option_key ); ?>" type="radio"
                                   name="<?php echo esc_attr( $field_key ); ?>"
                                   value="<?php echo esc_attr( $option_key_inner ); ?>" <?php checked( (string) $option_key_inner,
								esc_attr( $value ) ); ?>>
						<?php endforeach; ?>
                    </optgroup>
				<?php else : ?>
                    <label for="<?php echo esc_attr( $option_key ); ?>"><?php echo esc_html( $option_value ); ?></label>
                    <input id="<?php echo esc_attr( $option_key ); ?>" type="radio"
                           name="<?php echo esc_attr( $field_key ); ?>"
                           value="<?php echo esc_attr( $option_key ); ?>" <?php checked( (string) $option_key,
						esc_attr( $value ) ); ?>>
						<?php endif; ?>
						<?php endforeach; ?>
                    </input>
					<?php echo $this->get_description_html( $data ); // WPCS: XSS ok. ?>
                </fieldset>
            </td>
        </tr>
		<?php

		return ob_get_clean();
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
	public function process_payment( $order_id ): array {
		global $woocommerce;

		$payment_channel = (int) $_POST['bm-payment-channel'] ?? null;

		$params = [
			'params' => $this->prepare_initial_transaction_parameters(
				wc_get_order( $order_id ), $payment_channel
			),
		];


		WC()->session->set( 'bm_order_payment_params', $params );

		$this->schedule_remove_unpaid_orders( $order_id );

		$order = wc_get_order( $order_id );
		$order->set_status( 'pending' );
		$order->save();

		return [
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		];

	}

	private function schedule_remove_unpaid_orders( int $order_id ) {
		$woocommerce_hold_stock_minutes = (int) get_option( 'woocommerce_hold_stock_minutes' );

		if ( $woocommerce_hold_stock_minutes > 0 ) {
			$woocommerce_hold_stock_minutes *= 60;
			if ( ! wp_next_scheduled( 'bm_cancel_failed_pending_order_after_one_hour', [ $order_id ] ) ) {
				wp_schedule_single_event( time() + $woocommerce_hold_stock_minutes,
					'bm_cancel_failed_pending_order_after_one_hour',
					[ $order_id ] );
			}
		}
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
						$bm_remote_id = (string) $transaction->remoteID;




						$init_params     = get_post_meta( $wc_order_id,
							'bm_transaction_init_params', true );

						update_option( 'bm_api_last_error',
							sprintf( '[%s server time] [BlueMedia ITN debug] [Transaction from ITN: %s] [Init params meta: %s]',
								date( "Y-m-d H:i:s", time() ),
								json_encode( $transaction ),
								json_encode( $init_params )
							)
						);


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
								esc_attr( blue_media()->get_from_config( 'slug' ) ) );
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
							$order_success_to_update[$bm_remote_id] = $wc_order;
						}

						if ( 'PENDING' === $bm_order_status ) {
							$order_pending_to_update[$bm_remote_id] = $wc_order;
						}

						if ( 'FAILURE' === $bm_order_status ) {
							$order_failure_to_update[$bm_remote_id] = $wc_order;
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
							'bm-woocommerce' );
						exit;
					}

					foreach ( $order_success_to_update as $k => $wc_order ) {
						$new_status = $this->get_option( 'wc_payment_status_on_bm_success' );
						$wc_order->payment_complete($k);
                        if( $new_status ) {
                            $wc_order->set_status( $new_status );
                        }
						$wc_order->add_order_note( 'PayBM ITN: paymentStatus SUCCESS' );

						$wc_order->save();
					}

					foreach ( $order_pending_to_update as $k => $wc_order ) {
						$new_status = $this->get_option( 'wc_payment_status_on_bm_pending', 'pending' );
						$wc_order->set_status( $new_status );
						$wc_order->add_order_note( 'PayBM ITN: paymentStatus PENDING' );
						$wc_order->save();
					}

					foreach ( $order_failure_to_update as $k => $wc_order ) {
						$new_status = $this->get_option( 'wc_payment_status_on_bm_failure', 'failed' );
						$wc_order->set_status( $new_status );
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
	 * @throws Exception
	 */
	private
	function prepare_initial_transaction_parameters(
		WC_Order $wc_order,
		int $payment_channel = 0
	): array {

		// get price (type "string") and check if it has dot in it
		// because some shops in Woocommerce settings
		// set rounded prices
		$price = $wc_order->get_total();
		if ( stripos( $price, "." ) === false ) {
			$price = sprintf( "%.2f", $price );
		}

		$params = [
			'ServiceID'             => $this->service_id,
			'OrderID'               => $wc_order->get_id(),
			'Amount'                => $price,
			'GatewayID'             => $payment_channel,
			'Currency'              => blue_media()->resolve_blue_media_currency_symbol(),
			'CustomerEmail'         => $wc_order->get_billing_email(),
			'PlatformName'          => 'Woocommerce',
			'PlatformVersion'       => WC_VERSION,
			'PlatformPluginVersion' => blue_media()->get_plugin_version(),
		];


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
		if ( defined( 'BLUE_MEDIA_DISABLE_CACHE' ) || time()
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
		$currencies = blue_media()->resolve_blue_media_currency_symbol();

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

		$group_arr = ( new Group_Mapper( $channels ) )->map();

		echo '<div class="bm-payment-channels-wrapper">';
		echo '<ul id="shipping_method" class="woocommerce-shipping-methods">';

		/**
		 * @var Group[] $group_arr
		 */
		foreach ( $group_arr as $group ) {
			$expandable_Group = $group instanceof Expandable_Group;

			printf( "<div class='bm-group-%s%s'><li><ul>",
				$group->get_slug(), $expandable_Group ? ' bm-group-expandable' : '' );


			if ( $expandable_Group ) {
				// add radio before "PRZELEW INTERNETOWY" logo to add possibility
				// show-hide list of banks
				printf( "<li class='bm-payment-channel-group-item'>
                                <input type='radio' name='bm-payment-channel-group' id='bm-gateway-bank-group'>
                                <span class='bm-payment-channel-group-method-logo'>
                                <img src='%s'></span>
								<label for='bm-gateway-bank-group'>%s</label>
								</li>
                                ",
					$group->get_icon(), $group->get_name() );

				echo "<div class='bm-group-expandable-wrapper'>";
			}


			foreach ( $group->get_items() as $item ) {
				printf( '
                    <li class="bm-payment-channel-item %s">
                    <input
                    type="radio"
                    name="bm-payment-channel"
                    onclick="addCurrentClass(this)"
                    data-index="0" id="bm-gateway-id-%s" class="shipping_method" value="%s">
                    <span class="bm-payment-channel-method-logo">
                            <img style="" src="%s">
                            </span>
                    <label for="bm-gateway-id-%s">%s</label>
                            </li>',
					(string) $item->get_class(),
					$item->get_id(),
					$item->get_id(),
					$item->get_icon(),
					$item->get_id(),
					$item->get_name()
				);
				$script = $item->get_script();
				if ( $script ) {
					echo $item->get_script();
				}
			}
			if ( $expandable_Group ) {
				echo '</div>';
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
			throw new Exception( "The {$key} cannot be found in the given array." );
		}
		$p1    = array_splice( $array, $a, 1 );
		$p2    = array_splice( $array, 0, $order );
		$array = array_merge( $p2, $p1, $array );
	}
}
