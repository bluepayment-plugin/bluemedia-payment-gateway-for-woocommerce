<?php

namespace Ilabs\BM_Woocommerce\Domain\Service\Ga4;

use Ilabs\BM_Woocommerce\Data\Remote\Ga4\Dto\Item_In_Cart_DTO;
use Ilabs\BM_Woocommerce\Data\Remote\Ga4\Dto\Payload_DTO;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Common\Wc_Helpers;
use WC_Cart;

class Initiate_Checkout_Use_Case implements Ga4_Use_Case_Interface {

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

			$item_dto = new Item_In_Cart_DTO();
			$item_dto->set_name( $wc_product->get_name() );
			$item_dto->set_price( (float) $wc_product->get_price( null ) );
			$item_dto->set_quantity( (int) $wc_product->get_stock_quantity( null ) );
			$item_dto->set_variant( '' );//todo wyjaśnić
			$item_dto->set_category( ( function () use ( $wc_product ) {
				$term = get_term( $wc_product->get_category_ids()[0], 'product_cat' );

				return $term->name;
			} )() );
			$item_dto->set_brand( '' );
			$item_dto->set_id( $wc_product->get_id() );
			$items_dto[] = $item_dto;
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

		return $ga4_payload;
	}

	public function get_event_name(): string {
		return 'begin_checkout';
	}


}
