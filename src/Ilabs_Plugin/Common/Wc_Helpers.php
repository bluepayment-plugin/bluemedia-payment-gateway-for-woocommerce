<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Common;

use WC_Cart;
use WC_Product;

class Wc_Helpers {


	/**
	 * @return WC_Product[]|null
	 */
	public static function get_products_from_cart( WC_Cart $cart ): ?array {
		$return = [];
		foreach ( $cart->get_cart() as $cart_item ) {
			$product = $cart_item['data'];
			if ( $product instanceof WC_Product ) {
				$return[] = $product;
			}
		}

		return $return ?: null;
	}

	/**
	 * Get product IDs, names, and quantity from order ID.
	 *
	 * @param array $order_id ID of order.
	 *
	 * @return array
	 */
	public static function get_products_by_order_id( int $order_id ) {
		global $wpdb;
		$order_items_table    = $wpdb->prefix . 'woocommerce_order_items';
		$order_itemmeta_table = $wpdb->prefix . 'woocommerce_order_itemmeta';
		$products             = $wpdb->get_results(
			$wpdb->prepare(
			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT
				order_id,
				order_itemmeta.meta_value as product_id,
				order_itemmeta_2.meta_value as product_quantity,
				order_itemmeta_3.meta_value as variation_id,
				{$wpdb->posts}.post_title as product_name
			FROM {$order_items_table} order_items
			    LEFT JOIN {$order_itemmeta_table} order_itemmeta on order_items.order_item_id = order_itemmeta.order_item_id
			    LEFT JOIN {$order_itemmeta_table} order_itemmeta_2 on order_items.order_item_id = order_itemmeta_2.order_item_id
			    LEFT JOIN {$order_itemmeta_table} order_itemmeta_3 on order_items.order_item_id = order_itemmeta_3.order_item_id
			    LEFT JOIN {$wpdb->posts} on {$wpdb->posts}.ID = order_itemmeta.meta_value
			WHERE
				order_id = ( %d )
			    AND order_itemmeta.meta_key = '_product_id'
				AND order_itemmeta_2.meta_key = '_qty'
			  	AND order_itemmeta_3.meta_key = '_variation_id'
			GROUP BY product_id
			", // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$order_id
			),
			ARRAY_A
		);

		return $products;

		/**
		 * array(2) {
		 * [0]=>
		 * array(5) {
		 * ["order_id"]=>
		 * string(4) "7453"
		 * ["product_id"]=>
		 * string(2) "10"
		 * ["product_quantity"]=>
		 * string(1) "9"
		 * ["variation_id"]=>
		 * string(1) "0"
		 * ["product_name"]=>
		 * string(6) "Beanie"
		 * }
		 * [1]=>
		 * array(5) {
		 * ["order_id"]=>
		 * string(4) "7453"
		 * ["product_id"]=>
		 * string(3) "700"
		 * ["product_quantity"]=>
		 * string(1) "1"
		 * ["variation_id"]=>
		 * string(1) "0"
		 * ["product_name"]=>
		 * string(5) "Album"
		 * }
		 * }
		 */
	}
}
