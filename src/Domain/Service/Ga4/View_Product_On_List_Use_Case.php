<?php

namespace Ilabs\BM_Woocommerce\Domain\Service\Ga4;

use Ilabs\BM_Woocommerce\Data\Remote\Ga4\Dto\Item_In_Cart_DTO;
use Ilabs\BM_Woocommerce\Data\Remote\Ga4\Dto\List_Item_DTO;
use Ilabs\BM_Woocommerce\Data\Remote\Ga4\Dto\Payload_DTO;
use Isolated\BlueMedia\Ilabs\Ilabs_Plugin\Common\Wc_Helpers;
use WC_Product;

class View_Product_On_List_Use_Case extends Abstract_Ga4_Use_Case implements Ga4_Use_Case_Interface {

	/**
	 * @var WC_Product
	 */
	private $product;

	/**
	 * @var Payload_DTO
	 */
	private $payload;

	/**
	 * @param WC_Product|null $product
	 */
	public function __construct( ?WC_Product $product ) {
		$this->product = $product;
	}

	public function create_dto(): List_Item_DTO {
		$dto = new List_Item_DTO();
		$dto->set_id( (string) $this->product->get_id() );
		$dto->set_name( (string) $this->product->get_name() );
		$dto->set_brand( '' );
		$dto->set_category( Wc_Helpers::get_main_category( $this->product ) );
		$dto->set_variant( '' );
		$dto->set_quantity( 1 );
		$dto->set_price( (float) wc_get_price_including_tax( $this->product ) );

		return $dto;
	}

	public function get_ga4_payload_array(): array {
		return $this->get_ga4_payload_dto()->to_array();
	}

	public function get_ga4_payload_dto(): Payload_DTO {
		if ( empty( $this->payload ) ) {
			$ga4_payload = new Payload_DTO();
			$ga4_payload->set_event_name( $this->get_event_name() );

			return $ga4_payload;
		} else {
			$this->payload->set_value( $this->recalculate_value( $this->payload->get_items() ) );

			return $this->payload;
		}
	}

	public function get_event_name(): string {
		return 'view_item_list';
	}

	/**
	 * @param WC_Product $product
	 */
	public function set_product( WC_Product $product ): void {
		$this->product = $product;
	}

	/**
	 * @param Payload_DTO $payload
	 */
	public function set_payload( Payload_DTO $payload ): void {
		$this->payload = $payload;
	}

	/**
	 * @return Payload_DTO
	 */
	public function get_payload(): Payload_DTO {
		return $this->payload;
	}
}
