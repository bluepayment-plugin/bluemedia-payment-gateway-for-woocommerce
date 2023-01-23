<?php

namespace Ilabs\BM_Woocommerce\Data\Remote;

use Ilabs\BM_Woocommerce\Data\Remote\Ga4\Dto\Item_DTO;
use Ilabs\BM_Woocommerce\Domain\Service\Ga4\Add_Product_To_Cart_Use_Case;
use Ilabs\BM_Woocommerce\Domain\Service\Ga4\Click_On_Product_Use_Case;
use Ilabs\BM_Woocommerce\Domain\Service\Ga4\Complete_Transation_Use_Case;
use Ilabs\BM_Woocommerce\Domain\Service\Ga4\Init_Checkout_Use_Case;
use Ilabs\BM_Woocommerce\Domain\Service\Ga4\Remove_Product_From_Cart_Use_Case;
use Ilabs\BM_Woocommerce\Domain\Service\Ga4\View_Product_On_List_Use_Case;

use Isolated\Blue_Media\Isolated_Php_ga4_mp\Br33f\Ga4\MeasurementProtocol\Dto\Event\AddToCartEvent;
use Isolated\Blue_Media\Isolated_Php_ga4_mp\Br33f\Ga4\MeasurementProtocol\Dto\Event\BeginCheckoutEvent;
use Isolated\Blue_Media\Isolated_Php_ga4_mp\Br33f\Ga4\MeasurementProtocol\Dto\Event\PurchaseEvent;
use Isolated\Blue_Media\Isolated_Php_ga4_mp\Br33f\Ga4\MeasurementProtocol\Dto\Event\RemoveFromCartEvent;
use Isolated\Blue_Media\Isolated_Php_ga4_mp\Br33f\Ga4\MeasurementProtocol\Dto\Event\ViewItemEvent;
use Isolated\Blue_Media\Isolated_Php_ga4_mp\Br33f\Ga4\MeasurementProtocol\Dto\Event\ViewItemListEvent;
use Isolated\Blue_Media\Isolated_Php_ga4_mp\Br33f\Ga4\MeasurementProtocol\Dto\Parameter\ItemParameter;
use Isolated\Blue_Media\Isolated_Php_ga4_mp\Br33f\Ga4\MeasurementProtocol\Dto\Request\BaseRequest;
use Isolated\Blue_Media\Isolated_Php_ga4_mp\Br33f\Ga4\MeasurementProtocol\Exception\ValidationException;
use Isolated\Blue_Media\Isolated_Php_ga4_mp\Br33f\Ga4\MeasurementProtocol\Service;
use Isolated\Blue_Media\Isolated_Php_ga4_mp\Br33f\Ga4\MeasurementProtocol\Exception\HydrationException;


class Ga4_Service_Client {

	static $wc_bm_settings = null;


	public function __construct() {
		if ( ! self::$wc_bm_settings ) {
			self::$wc_bm_settings = get_option( 'woocommerce_bluemedia_settings' );
		}
	}

	public function get_tracking_id() {
		return ! empty( self::$wc_bm_settings['ga4_tracking_id'] )
			? self::$wc_bm_settings['ga4_tracking_id']
			: null;
	}

	public function get_client_id() {
		return ! empty( self::$wc_bm_settings['ga4_client_id'] )
			? self::$wc_bm_settings['ga4_client_id']
			: null;
	}

	public function get_api_secret() {
		return ! empty( self::$wc_bm_settings['ga4_api_secret'] )
			? self::$wc_bm_settings['ga4_api_secret']
			: null;
	}


	/**
	 * @throws HydrationException
	 * @throws ValidationException
	 */
	public function add_to_cart_event( Add_Product_To_Cart_Use_Case $add_product_to_cart_use_case ) {
		$ga4Service  = new Service( $this->get_api_secret(), $this->get_tracking_id() );
		$baseRequest = new BaseRequest( $this->get_client_id() );
		$baseRequest->setClientId( $this->get_user_id() );
		$addToCartEventData = new AddToCartEvent();
		$addToCartEventData
			->setValue( $add_product_to_cart_use_case->get_ga4_payload_dto()->get_value() )
			->setCurrency( $add_product_to_cart_use_case->get_ga4_payload_dto()->get_currency_symbol() );

		foreach ( $add_product_to_cart_use_case->get_ga4_payload_dto()->get_items() as $item ) {
			/**
			 * @var Item_DTO $item
			 */

			$item_param = new ItemParameter();
			$item_param->setItemId( $item->get_id() );
			$item_param->setItemName( $item->get_name() );
			$item_param->setItemBrand( $item->get_name() );
			$item_param->setItemBrand( $item->get_brand() );
			$item_param->setItemCategory( $item->get_category() );
			$item_param->setItemVariant( $item->get_variant() );
			$item_param->setPrice( $item->get_price() );
			$item_param->setQuantity( $item->get_quantity() );

			$addToCartEventData->addItem( $item_param );

		}

		$baseRequest->addEvent( $addToCartEventData );
		$guzzle = $ga4Service->getHttpClient();

		$ga4Service->send( $baseRequest );


	}

	/**
	 * @throws HydrationException
	 * @throws ValidationException
	 */
	public function remove_from_cart_event( Remove_Product_From_Cart_Use_Case $remove_product_from_cart_use_case ) {
		$ga4Service  = new Service( $this->get_api_secret(), $this->get_tracking_id() );
		$baseRequest = new BaseRequest( $this->get_client_id() );
		$baseRequest->setClientId( $this->get_user_id() );
		$remove_from_cart_event_data = new RemoveFromCartEvent();


		$remove_from_cart_event_data
			->setValue( $remove_product_from_cart_use_case->get_ga4_payload_dto()->get_value() )
			->setCurrency( $remove_product_from_cart_use_case->get_ga4_payload_dto()->get_currency_symbol() );


		foreach ( $remove_product_from_cart_use_case->get_ga4_payload_dto()->get_items() as $item ) {
			/**
			 * @var Item_DTO $item
			 */

			$item_param = new ItemParameter();
			$item_param->setItemId( $item->get_id() );
			$item_param->setItemName( $item->get_name() );
			$item_param->setItemBrand( $item->get_name() );
			$item_param->setItemBrand( $item->get_brand() );
			$item_param->setItemCategory( $item->get_category() );
			$item_param->setItemVariant( $item->get_variant() );
			$item_param->setPrice( $item->get_price() );
			$item_param->setQuantity( $item->get_quantity() );

			$remove_from_cart_event_data->addItem( $item_param );

		}

		$baseRequest->addEvent( $remove_from_cart_event_data );
		$ga4Service->send( $baseRequest );
	}

	public function purchase_event( Complete_Transation_Use_Case $complete_transaction_use_case ) {
		$ga4Service  = new Service( $this->get_api_secret(), $this->get_tracking_id() );
		$baseRequest = new BaseRequest( $this->get_client_id() );
		$baseRequest->setClientId( $this->get_user_id() );
		$purchase_event_data = new PurchaseEvent();

		$purchase_event_data
			->setValue( $complete_transaction_use_case->get_ga4_payload_dto()->get_value() )
			->setCurrency( $complete_transaction_use_case->get_ga4_payload_dto()->get_currency_symbol() )
			->setShipping( $complete_transaction_use_case->get_ga4_payload_dto()->get_shipping() )
			->setTax( $complete_transaction_use_case->get_ga4_payload_dto()->get_tax() );

		foreach ( $complete_transaction_use_case->get_ga4_payload_dto()->get_items() as $item ) {
			/**
			 * @var Item_DTO $item
			 */
			$item_param = new ItemParameter();
			$item_param->setItemId( $item->get_id() );
			$item_param->setItemName( $item->get_name() );
			$item_param->setItemBrand( $item->get_name() );
			$item_param->setItemBrand( $item->get_brand() );
			$item_param->setItemCategory( $item->get_category() );
			$item_param->setItemVariant( $item->get_variant() );
			$item_param->setPrice( $item->get_price() );
			$item_param->setQuantity( $item->get_quantity() );

			$purchase_event_data->addItem( $item_param );

		}

		$baseRequest->addEvent( $purchase_event_data );
		$ga4Service->send( $baseRequest );
	}

	public function view_item_list_event_export_array( View_Product_On_List_Use_Case $view_product_on_list_use_case
	): array {
		$base_request              = new BaseRequest( $this->get_client_id() );
		$view_item_list_event_data = new ViewItemListEvent();

		$view_item_list_event_data
			->setValue( $view_product_on_list_use_case->get_ga4_payload_dto()->get_value() )
			->setCurrency( $view_product_on_list_use_case->get_ga4_payload_dto()->get_currency_symbol() );

		foreach ( $view_product_on_list_use_case->get_ga4_payload_dto()->get_items() as $item ) {
			/**
			 * @var Item_DTO $item
			 */

			$item_param = new ItemParameter();
			$item_param->setItemId( $item->get_id() );
			$item_param->setItemName( $item->get_name() );
			$item_param->setItemBrand( $item->get_name() );
			$item_param->setItemBrand( $item->get_brand() );
			$item_param->setItemCategory( $item->get_category() );
			$item_param->setItemVariant( $item->get_variant() );
			$item_param->setPrice( $item->get_price() );

			$view_item_list_event_data->addItem( $item_param );

		}

		$base_request->addEvent( $view_item_list_event_data );

		return $base_request->export();

	}

	public function view_item_event_export_array( Click_On_Product_Use_Case $click_on_product_use_case ): array {
		$base_request         = new BaseRequest( $this->get_client_id() );
		$view_item_event_data = new ViewItemEvent();

		$view_item_event_data
			->setValue( $click_on_product_use_case->get_ga4_payload_dto()->get_value() )
			->setCurrency( $click_on_product_use_case->get_ga4_payload_dto()->get_currency_symbol() );

		foreach ( $click_on_product_use_case->get_ga4_payload_dto()->get_items() as $item ) {
			/**
			 * @var Item_DTO $item
			 */

			$item_param = new ItemParameter();
			$item_param->setItemId( $item->get_id() );
			$item_param->setItemName( $item->get_name() );
			$item_param->setItemBrand( $item->get_name() );
			$item_param->setItemBrand( $item->get_brand() );
			$item_param->setItemCategory( $item->get_category() );
			$item_param->setItemVariant( $item->get_variant() );
			$item_param->setPrice( $item->get_price() );

			$view_item_event_data->addItem( $item_param );

		}

		$base_request->addEvent( $view_item_event_data );

		return $base_request->export();

	}

	public function init_checkout_event_export_array( Init_Checkout_Use_Case $init_checkout_use_case ): array {
		$base_request             = new BaseRequest( $this->get_client_id() );
		$init_checkout_event_data = new BeginCheckoutEvent();

		$init_checkout_event_data
			->setValue( $init_checkout_use_case->get_ga4_payload_dto()->get_value() )
			->setCurrency( $init_checkout_use_case->get_ga4_payload_dto()->get_currency_symbol() );

		foreach ( $init_checkout_use_case->get_ga4_payload_dto()->get_items() as $item ) {
			/**
			 * @var Item_DTO $item
			 */

			$item_param = new ItemParameter();
			$item_param->setItemId( $item->get_id() );
			$item_param->setItemName( $item->get_name() );
			$item_param->setItemBrand( $item->get_name() );
			$item_param->setItemBrand( $item->get_brand() );
			$item_param->setItemCategory( $item->get_category() );
			$item_param->setItemVariant( $item->get_variant() );
			$item_param->setPrice( $item->get_price() );
			$item_param->setQuantity( $item->get_quantity() );

			$init_checkout_event_data->addItem( $item_param );

		}
		$base_request->addEvent( $init_checkout_event_data );

		return $base_request->export();
	}

	private function get_user_id(): string {
		$from_cookie = $_COOKIE['_ga'];
		$exploded    = explode( '.', $from_cookie );

		if ( $exploded ) {
			return $exploded[2] . '.' . $exploded[3];
		}

		return (string) $from_cookie;
	}
}
