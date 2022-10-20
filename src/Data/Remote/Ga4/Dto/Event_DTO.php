<?php

namespace Ilabs\BM_Woocommerce\Data\Remote\Ga4\Dto;

class Event_DTO {

	/**
	 * @var
	 */
	private $name;

	/**
	 * @var Purchase_Event_Params_DTO
	 */
	private $params;

	/**
	 * @return mixed
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @param mixed $name
	 */
	public function set_name( $name ): void {
		$this->name = $name;
	}

	/**
	 * @return Purchase_Event_Params_DTO
	 */
	public function get_params(): Purchase_Event_Params_DTO {
		return $this->params;
	}

	/**
	 * @param Purchase_Event_Params_DTO $params
	 */
	public function set_params( Purchase_Event_Params_DTO $params ): void {
		$this->params = $params;
	}

	public function to_array(): array {
		return [
			'name'   => $this->name,
			'params' => $this->params->to_array(),
		];
	}


}
