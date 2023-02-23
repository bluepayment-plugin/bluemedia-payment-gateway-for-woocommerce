<?php

namespace Ilabs\BM_Woocommerce\Domain\Service\White_Label;

use Exception;
use Ilabs\BM_Woocommerce\Domain\Model\White_Label\Config;
use Ilabs\BM_Woocommerce\Domain\Model\White_Label\Expandable_Group;
use Ilabs\BM_Woocommerce\Domain\Model\White_Label\Group;
use Ilabs\BM_Woocommerce\Domain\Model\White_Label\Item;

class Group_Mapper {

	/**
	 * @var array
	 */
	private $raw_channels_from_bm_api;

	/**
	 * @param array $raw_channels_from_bm_api
	 */
	public function __construct( array $raw_channels_from_bm_api ) {
		$this->raw_channels_from_bm_api = $raw_channels_from_bm_api;
	}


	/**
	 * @return array
	 * @throws Exception
	 */
	public function map(): array {
		$groups_from_config = ( new Config() )->get_config();

		$ids_from_config           = ( new Config() )->get_ids();
		$unknown_raw_channels      = [];
		$result                    = [];
		$unspecified_ids_group_key = [];


		foreach ( $groups_from_config as $config_item ) {
			$instance_created = false;
			if ( $config_item['ids'] === Config::UNSPECIFIED_IDS ) {
				$group = new Expandable_Group(
					[],
					$config_item['name'],
					sanitize_title( $config_item['name'] ),
					blue_media()->get_plugin_images_url() . '/logo-group.svg',
					__( 'You will be redirected to the page of the selected bank.', 'bm-woocommerce'
					)
				);

				$result[]                  = $group;
				$unspecified_ids_group_key = array_keys( $result )[ count( $result ) - 1 ];
			} else {
				foreach ( $this->raw_channels_from_bm_api as $raw_channel ) {
					if ( in_array( $raw_channel->gatewayID, $config_item['ids'] ) ) {
						if ( ! $instance_created ) {
							$group            = new Group( [], $config_item['name'],
								sanitize_title( $config_item['name'] ) );
							$instance_created = true;
						}

						$gateway_name = $config_item['extra_html'] ?? $raw_channel->gatewayName;

						$group->push_item( ( new Item( $gateway_name, $raw_channel->gatewayID,
							$raw_channel->iconURL ) ) );
					} elseif ( ! in_array( $raw_channel->gatewayID,
						$ids_from_config ) ) {
						$unknown_raw_channels[ $raw_channel->gatewayID ] = $raw_channel;
					}
				}
				if ( $instance_created ) {
					$result[] = $group;
				}
			}

		}

		if ( ! empty( $unspecified_ids_group_key ) ) {
			foreach ( $unknown_raw_channels as $raw_channel ) {
				$result[ $unspecified_ids_group_key ]->push_item( ( new Item( $raw_channel->gatewayName,
					$raw_channel->gatewayID,
					$raw_channel->iconURL ) ) );

			}
		}

		return $result;


	}
}
