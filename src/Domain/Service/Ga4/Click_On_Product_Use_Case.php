<?php

namespace Ilabs\BM_Woocommerce\Domain\Service\Ga4;

use Ilabs\BM_Woocommerce\Data\Remote\Ga4\Dto\Item_DTO;
use Ilabs\BM_Woocommerce\Data\Remote\Ga4\Dto\Payload_DTO;
use WC_Product;

class Click_On_Product_Use_Case implements Ga4_Use_Case_Interface {

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
		$dto->set_brand( '' );//todo nie ma jak uniwersalnie mapować
		$dto->set_category( ( function () {
			$term = get_term( $this->product->get_category_ids()[0], 'product_cat' );

			return $term->name;
		} )() );//todo nie ma jak uniwersalnie mapować
		$dto->set_variant( '' );//todo nie ma jak uniwersalnie mapować
		$dto->set_price( (float) $this->product->get_price(), null );

		return $dto;
	}

	public function get_ga4_payload_array(): array {
		return $this->get_ga4_payload_dto()->to_array();
	}

	public function get_ga4_payload_dto(): Payload_DTO {
		$ga4_payload = new Payload_DTO();
		$ga4_payload->set_event_name( $this->get_event_name() );
		$ga4_payload->set_items( [ $this->create_dto() ] );

		return $ga4_payload;
	}

	public function get_event_name(): string {
		return 'view_item';
	}
}
