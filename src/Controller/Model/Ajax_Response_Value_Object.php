<?php

namespace Ilabs\BM_Woocommerce\Controller\Model;

class Ajax_Response_Value_Object {

	const STATUS_SUCCESS = 'success';

	const STATUS_ERROR = 'error';

	/**
	 * @var string
	 */
	private $status;

	/**
	 * @var string
	 */
	private $message;

	/**
	 * @var string
	 */
	private $content;


	/**
	 * @param string $status
	 * @param string $message
	 * @param string $content
	 */
	public function __construct(
		string $status,
		string $message,
		string $content
	) {
		$this->status       = $status;
		$this->message      = $message;
		$this->content      = $content;
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
	public function get_content(): string {
		return $this->content;
	}

	public function to_array(): array {
		return [
			'status'       => $this->status,
			'message'      => $this->message,
			'content'      => $this->content,
		];
	}
}
