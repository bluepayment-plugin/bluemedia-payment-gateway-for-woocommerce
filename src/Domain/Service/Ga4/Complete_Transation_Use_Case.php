<?php

namespace Ilabs\BM_Woocommerce\Domain\Service\Ga4;

use Ilabs\BM_Woocommerce\Data\Remote\Ga4\Dto\Event_DTO;
use Ilabs\BM_Woocommerce\Data\Remote\Ga4\Dto\Item_DTO;
use Ilabs\BM_Woocommerce\Data\Remote\Ga4\Dto\Item_In_Cart_DTO;
use Ilabs\BM_Woocommerce\Data\Remote\Ga4\Dto\Payload_DTO;
use Ilabs\BM_Woocommerce\Data\Remote\Ga4\Dto\Purchase_Event_Params_DTO;
use Isolated\BlueMedia\Ilabs\Ilabs_Plugin\Common\Wc_Helpers;
use WC_Order;

class Complete_Transation_Use_Case extends Abstract_Ga4_Use_Case implements Ga4_Use_Case_Interface {

	/**
	 * @var WC_Order
	 */
	private $order;

	/**
	 * @param WC_Order $order
	 */
	public function __construct( WC_Order $order ) {
		$this->order = $order;
	}


	public function get_ga4_payload_array(): array {
		return $this->get_ga4_payload_dto()->to_array();
	}

	public function get_ga4_payload_dto(): Payload_DTO {
		$ga4_payload = new Payload_DTO();
		$ga4_payload->set_event_name( $this->get_event_name() );

		$ga4_payload->set_currency( $this->order->get_currency() );
		$ga4_payload->set_shipping( (float) $this->order->get_shipping_total() );
		$ga4_payload->set_tax( (float) $this->order->get_shipping_tax() );
		$ga4_payload->set_transaction_id( $this->order->get_id() );
		$ga4_payload->set_value( $this->order->get_total() );

		$items = Wc_Helpers::get_products_by_order_id( $this->order->get_id() );

		$items_dto = [];
		foreach ( $items as $item ) {
			$wc_product = wc_get_product( $item['product_id'] );
			$item_dto   = new Item_DTO();
			$item_dto->set_name( $item['product_name'] );
			$item_dto->set_price( (float) $wc_product->get_price( null ) );
			$item_dto->set_quantity( (int) $item['product_quantity'] );
			$item_dto->set_variant( (string) $item['variation_id'] );
			$item_dto->set_category(Wc_Helpers::get_main_category($wc_product));
			$item_dto->set_brand( '' );
			$item_dto->set_id( (int) $item['product_id'] );
			$items_dto[] = $item_dto;
		}

		$ga4_payload->set_items( $items_dto );

		return $ga4_payload;
	}

	public function get_event_name(): string {
		return 'purchase';
	}
}
