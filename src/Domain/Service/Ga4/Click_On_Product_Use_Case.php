<?php

namespace Ilabs\BM_Woocommerce\Domain\Service\Ga4;

use Ilabs\BM_Woocommerce\Data\Remote\Ga4\Dto\Item_DTO;
use Ilabs\BM_Woocommerce\Data\Remote\Ga4\Dto\Payload_DTO;
use Isolated\BlueMedia\Ilabs\Ilabs_Plugin\Common\Wc_Helpers;
use WC_Product;

class Click_On_Product_Use_Case extends Abstract_Ga4_Use_Case implements Ga4_Use_Case_Interface {

	/**
	 * @var WC_Product
	 */
	private $product;

	/**
	 * @param WC_Product $product
	 */
	public function __construct( WC_Product $product ) {
		$this->product = $product;
	}

	/**
	 * @return Item_DTO
	 */
	private function create_dto(): Item_DTO {
		$dto = new Item_DTO();
		$dto->set_id( (string) $this->product->get_id() );
		$dto->set_name( (string) $this->product->get_name() );
		$dto->set_brand( '' );
		$dto->set_category(Wc_Helpers::get_main_category($this->product));
		$dto->set_variant( '' );
		$dto->set_price( (float) wc_get_price_including_tax( $this->product ) );
		$dto->set_quantity(1);
		return $dto;
	}

	public function get_ga4_payload_array(): array {
		return $this->get_ga4_payload_dto()->to_array();
	}

	public function get_ga4_payload_dto(): Payload_DTO {
		$ga4_payload = new Payload_DTO();

		$ga4_payload->set_event_name( $this->get_event_name() );
		$ga4_payload->set_items( [ $this->create_dto() ] );
		$ga4_payload->set_value( $this->recalculate_value( $ga4_payload->get_items() ) );

		return $ga4_payload;
	}

	public function get_event_name(): string {
		return 'view_item';
	}
}
