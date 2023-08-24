<?php

namespace Ilabs\BM_Woocommerce\Controller;

use Exception;
use Ilabs\BM_Woocommerce\Controller\Model\Payment_Status_Response_Value_Object;
use Ilabs\BM_Woocommerce\Gateway\Blue_Media_Gateway;
use function GuzzleHttp\Psr7\str;


class Payment_Status_Controller extends Abstract_Controller implements Controller_Interface {

	const ACTION_NAME = 'payment_get_status';

	const NONCE_ACTION = 'bluemedia_payment';

	const GENERIC_ERROR_MESSAGE = 'Wystąpił błąd. Płatność zakończona niepowodzeniem';

	public function execute_request() {
		$order_id                = WC()->session->get( 'bm_wc_order_id' );
		$transaction_start_error = WC()->session->get( 'bm_continue_transaction_start_error' );

		$nonce = $_POST['nonce'];

		if ( ! wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) {
			$this->send_response(
				Payment_Status_Response_Value_Object::STATUS_ERROR,
				self::GENERIC_ERROR_MESSAGE,
				WC()->session->get( 'bm_original_order_received_url' ),
				null
			);
		}

		if ( '' !== $transaction_start_error ) {
			$this->send_response(
				Payment_Status_Response_Value_Object::STATUS_ERROR,
				$transaction_start_error,
				WC()->session->get( 'bm_original_order_received_url' ),
				null
			);
		}

		$itn_status = (string) get_post_meta( $order_id,
			'bm_order_itn_status',
			true );

		$continue_transaction_redirect_url = WC()->session->get( 'bm_continue_transaction_redirect_url' );

		if ( ! wc_is_valid_url( $continue_transaction_redirect_url ) ) {
			$continue_transaction_redirect_url = null;
		}

		switch ( $itn_status ) {
			case Blue_Media_Gateway::ITN_SUCCESS_STATUS_ID:
				$status = Payment_Status_Response_Value_Object::STATUS_SUCCESS;
				break;

			case Blue_Media_Gateway::ITN_PENDING_STATUS_ID:
				$status = Payment_Status_Response_Value_Object::STATUS_CHECK_DEVICE;
				break;

			case Blue_Media_Gateway::ITN_FAILURE_STATUS_ID:
				$status = Payment_Status_Response_Value_Object::STATUS_ERROR;
				break;

			default:
				$status = Payment_Status_Response_Value_Object::STATUS_WAIT;
		}

		$this->send_response(
			$status,
			Payment_Status_Response_Value_Object::get_message_by_itn_status_id( $itn_status ),
			WC()->session->get( 'bm_original_order_received_url' ),
			$continue_transaction_redirect_url
		);
	}

	public function handle() {
		add_action( $this->get_ajax_action_name( self::ACTION_NAME ),
			function () {
				$this->execute_request();
			} );

		add_action( $this->get_ajax_action_name_nopriv( self::ACTION_NAME ),
			function () {
				$this->execute_request();
			} );
	}
}
