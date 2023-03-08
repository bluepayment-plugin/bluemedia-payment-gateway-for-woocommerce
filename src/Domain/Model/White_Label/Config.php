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
			[
				'name'        => 'Apple Pay',
				'position'    => 6,
				'ids'         => [ 1513 ],
				'extra_class' => 'bm-apple-pay',
				'extra_script' => $this->get_applepay_check_script()
			],
			[ 'name' => 'Smartney', 'position' => 7, 'ids' => [ 700 ], ],
			[ 'name' => 'Alior raty', 'position' => 8, 'ids' => [ 1506 ], ],
			[
				'name'       => 'PayPo',
				'position'   => 9,
				'ids'        => [ 705 ],
				'extra_html' => $this->get_paypo_html_info(),
			],
			[ 'name' => 'Hub ratalny', 'position' => 10, 'ids' => [ 702 ], ],
			[ 'name' => 'Przelew Internetowy', 'position' => 11, 'ids' => self::UNSPECIFIED_IDS ],
		];
	}

	public function get_ids(): array {
		$return = [];

		foreach ( $this->get_config() as $v ) {
			if ( $v['ids'] ) {
				$return = array_merge( $return, $v['ids'] );
			}
		}

		return $return;
	}

	private function get_paypo_html_info(): string {
		return sprintf( ' <span style="text-align: justify"><span class="payment-method-description">%s </span>
                            <span class="payment-method-help-text">%s</span><a href="https://start.paypo.pl/" target="_blank"><span style=""><br>%s</a></span>',
			__( 'Odbierz zakupy, sprawdź je i zapłać później — za 30 dni lub w wygodnych ratach.', 'bm-woocommerc' ),
			__( 'Przekierujemy Cię na stronę partnera PayPo.', 'bm-woocommerc' ),
			__( 'Poznaj szczegóły.', 'bm-woocommerc' )
		);
	}

	private function get_applepay_check_script(): string {
		return "<script>if (window.ApplePaySession) {jQuery('.bm-apple-pay').css('display', 'grid')}</script>";
	}
}
