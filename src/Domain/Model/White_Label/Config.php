<?php

namespace Ilabs\BM_Woocommerce\Domain\Model\White_Label;

class Config {
	const UNSPECIFIED_IDS = null;
	static $config_json = '{"1500":"karta-platnicza","509":"blik","106":"pbl","1899":"pbl","21":"szybki-przelew","35":"szybki-przelew","9":"szybki-przelew","1513":"portfel-elektroniczny","1512":"portfel-elektroniczny","700":"raty-online"}';


	public function get_config(): array {

		return [
			[ 'name' => 'Płatność Kartą', 'position' => 0, 'ids' => [ 1500 ], ],
			[ 'name' => 'Płatność Kartą One Clik', 'position' => 1, 'ids' => [ 1503 ], ],
			[ 'name' => 'Visa Mobile', 'position' => 2, 'ids' => [ 1523 ], ],
			[ 'name' => 'Blik', 'position' => 3, 'ids' => [ 509 ], ],
			[ 'name' => 'Wirtualny portfel', 'position' => 4, 'ids' => [ 778 ], ],
			[ 'name' => 'Google Pay', 'position' => 5, 'ids' => [ 1512 ], ],
			[ 'name' => 'Apple Pay', 'position' => 6, 'ids' => [ 1513 ], ],
			[ 'name' => 'Smartney', 'position' => 7, 'ids' => [ 700 ], ],
			[ 'name' => 'Alior raty', 'position' => 8, 'ids' => [ 1506 ], ],
			[ 'name' => 'PayPo', 'position' => 9, 'ids' => [ 705 ], ],
			[ 'name' => 'Hub ratalny', 'position' => 10, 'ids' => [ 702 ], ],
			[ 'name' => 'Przelew Internetowy', 'position' => 11, 'ids' => self::UNSPECIFIED_IDS ],
		];
	}

	public function get_ids():array {
		$return = [];

		foreach ( $this->get_config() as $v ) {
			if ( $v['ids'] ) {
				$return = array_merge( $return, $v['ids'] );
			}
		}

		return $return;
	}
}
