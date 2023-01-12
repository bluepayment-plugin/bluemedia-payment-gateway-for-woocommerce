<?php

namespace Ilabs\BM_Woocommerce\Domain\Service\Ga4;

use Ilabs\BM_Woocommerce\Data\Remote\Ga4\Dto\Item_DTO;
use Ilabs\BM_Woocommerce\Data\Remote\Ga4\Dto\Payload_DTO;
use Isolated\BlueMedia\Ilabs\Ilabs_Plugin\Common\Wc_Helpers;
use WC_Cart;

class Init_Checkout_Use_Case extends Abstract_Ga4_Use_Case implements Ga4_Use_Case_Interface {

	/**
	 * @var WC_Cart
	 */
	private $cart;

	/**
	 * @param WC_Cart $cart
	 */
	public function __construct( WC_Cart $cart ) {
		$this->cart = $cart;
	}

	private function create_dto_arr(): array {
		$products = Wc_Helpers::get_products_from_cart( $this->cart );

		$items_dto = [];
		foreach ( $products as $wc_product ) {
			$dto = new Item_DTO();
			$dto->set_name( $wc_product->get_name() );
			$dto->set_price( (float) $wc_product->get_price( null ) );
			$dto->set_quantity( (int) $wc_product->get_stock_quantity( null ) );
			$dto->set_variant( '' );
			$dto->set_category( Wc_Helpers::get_main_category( $wc_product ) );
			$dto->set_brand( '' );
			$dto->set_id( $wc_product->get_id() );
			$items_dto[] = $dto;
		}

		return $items_dto;
	}

	public function get_ga4_payload_array(): array {
		return $this->get_ga4_payload_dto()->to_array();
	}

	public function get_ga4_payload_dto(): Payload_DTO {
		$ga4_payload = new Payload_DTO();
		$ga4_payload->set_event_name( $this->get_event_name() );
		$ga4_payload->set_items( $this->create_dto_arr() );
		$ga4_payload->set_value( $this->recalculate_value( $ga4_payload->get_items() ) );

		return $ga4_payload;
	}

	public function get_event_name(): string {
		return 'begin_checkout';
	}


}
