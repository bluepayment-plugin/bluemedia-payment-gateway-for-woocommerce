<?php

namespace Ilabs\BM_Woocommerce\Controller\Model;

use Ilabs\BM_Woocommerce\Gateway\Blue_Media_Gateway;

class Payment_Status_Response_Value_Object {

	const STATUS_SUCCESS = 'payment_success';

	const STATUS_ERROR = 'error';

	const STATUS_CHECK_DEVICE = 'check_device';

	const STATUS_WAIT = 'wait';

	/**
	 * @var string
	 */
	private $status;

	/**
	 * @var string
	 */
	private $message;

	/**
	 * @var string | null
	 */
	private $order_received_url;

	/**
	 * @var string
	 */
	private $continue_transaction_redirect_url;


	/**
	 * @param string $status
	 * @param string $message
	 * @param string $order_received_url
	 * @param string|null $continue_transaction_redirect_url
	 */
	public function __construct(
		string $status,
		string $message,
		string $order_received_url,
		?string $continue_transaction_redirect_url
	) {
		$this->status                            = $status;
		$this->message                           = $message;
		$this->order_received_url                = $order_received_url;
		$this->continue_transaction_redirect_url = $continue_transaction_redirect_url;
	}


	/**
	 * @return string
	 */
	public function get_status(): string {
		return $this->status;
	}

	/**
	 * @return string
	 */
	public function get_message(): string {
		return $this->message;
	}

	/**
	 * @return string
	 */
	public function get_order_received_url(): string {
		return $this->order_received_url;
	}

	/**
	 * @return string|null
	 */
	public function get_continue_transaction_redirect_url(): ?string {
		return $this->continue_transaction_redirect_url;
	}

	public function to_array(): array {
		return [
			'status'                            => $this->status,
			'message'                           => $this->message,
			'order_received_url'                => $this->order_received_url,
			'continue_transaction_redirect_url' => (string) $this->continue_transaction_redirect_url,
		];
	}

	public static function get_message_by_itn_status_id( string $itn_status_id
	): string {
		switch ( $itn_status_id ) {
			case Blue_Media_Gateway::ITN_SUCCESS_STATUS_ID:
				return __( 'Payment successful.',
					'bm-woocommerce' );

			case Blue_Media_Gateway::ITN_PENDING_STATUS_ID:
				return __( 'Check your device.',
					'bm-woocommerce' );

			case Blue_Media_Gateway::ITN_FAILURE_STATUS_ID:
				return __( 'Payment failed.',
					'bm-woocommerce' );

			default:
				return __( 'Waiting for transaction confirmation.',
					'bm-woocommerce' );
		}
	}
}
