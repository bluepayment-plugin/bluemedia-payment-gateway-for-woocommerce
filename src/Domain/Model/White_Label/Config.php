<?php

namespace Ilabs\BM_Woocommerce\Domain\Model\White_Label;

class Config {

	const UNSPECIFIED_IDS = null;


	public function get_config(): array {

		return [
			[ 'name' => 'Blik', 'position' => 0, 'ids' => [ 509 ], ],
			[
				'name'       => 'Płatność Kartą',
				'position'   => 1,
				'ids'        => [ 1500 ],
				'extra_html' => $this->get_desc_html_info( __( 'Przekierujemy Cie na stronę naszego partnera Blue Media, gdzie podasz dane swojej karty',
					'bm-woocommerce' ) ),
			],
			[
				'name'       => 'Przelew Internetowy',
				'position'   => 2,
				'extra_html' => $this->get_desc_html_info( __( 'You will be redirected to the page of the selected bank.',
					'bm-woocommerce' ) ),
				'ids'        => self::UNSPECIFIED_IDS,
			],
			//[ 'name' => 'Płatność Kartą One Clik', 'position' => 1, 'ids' => [ 1503 ], ],
			[
				'name'       => 'Visa Mobile',
				'position'   => 3,
				'ids'        => [ 1523 ],
				'extra_html' => $this->get_desc_html_info( __( 'Podaj numer telefonu i potwierdź płatność w aplikacji',
					'bm-woocommerce' ) ),
			],
			[
				'name'       => 'Google Pay',
				'position'   => 4,
				'ids'        => [ 1512 ],
				'extra_html' => $this->get_desc_html_info( __( 'Zapłać bez konieczności logowania się do bankowości internetowej',
					'bm-woocommerce' ) ),
			],
			[
				'name'         => 'Apple Pay',
				'position'     => 5,
				'ids'          => [ 1513 ],
				'extra_class'  => 'bm-apple-pay',
				'extra_script' => $this->get_applepay_check_script(),
				'extra_html'   => $this->get_desc_html_info( __( 'Zapłać bez konieczności logowania się do bankowości internetowej',
					'bm-woocommerce' ) ),
			],

			//[ 'name' => 'Wirtualny portfel', 'position' => 4, 'ids' => [ 778 ], ],


			[
				'name'       => 'Smartney',
				'position'   => 6,
				'ids'        => [ 700 ],
				'extra_html' => $this->get_smartney_html_info(),
			],
			[
				'name'       => 'Alior raty',
				'position'   => 7,
				'ids'        => [ 1506 ],
				'extra_html' => $this->get_alior_html_info(),
			],
			[
				'name'       => 'PayPo',
				'position'   => 8,
				'ids'        => [ 705 ],
				'extra_html' => $this->get_paypo_html_info(),
			],
			[
				'name'       => 'Spingo',
				'position'   => 9,
				'ids'        => [ 706 ],
				'extra_html' => $this->get_desc_html_info( __( 'Płatność odroczona dla firm',
					'bm-woocommerce' ) ),
			],
			//[ 'name' => 'Hub ratalny', 'position' => 10, 'ids' => [ 702 ], ],
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
			__( 'Odbierz zakupy, sprawdź je i zapłać później — za 30 dni lub w wygodnych ratach.',
				'bm-woocommerc' ),
			__( 'Przekierujemy Cię na stronę partnera PayPo.',
				'bm-woocommerc' ),
			__( 'Poznaj szczegóły.', 'bm-woocommerc' )
		);
	}

	private function get_smartney_html_info(): string {
		return sprintf( ' <span style="text-align: justify"><span class="payment-method-description">%s </span>
                            <a href="https://pomoc.bluemedia.pl/platnosci-online-w-e-commerce/pay-smartney" target="_blank"><span style="">%s</a></span>',
			__( 'Kup teraz i zapłać w ciągu 30 dni', 'bm-woocommerc' ),
			__( 'Dowiedz się więcej', 'bm-woocommerc' )
		);
	}

	private function get_alior_html_info(): string {
		return sprintf( ' <span style="text-align: justify"><span class="payment-method-description">%s </span>
                            <a href="https://pomoc.bluemedia.pl/platnosci-online-w-e-commerce/pay-smartney" target="_blank"><span style="">%s</a></span>',
			__( 'Raty 0% lub nawet 48 rat.', 'bm-woocommerc' ),
			__( 'Dowiedz się więcej', 'bm-woocommerc' )
		);
	}

	private function get_desc_html_info( string $text ): string {
		return sprintf( ' <span style="text-align: justify"><span class="payment-method-description">%s </span>',
			$text
		);
	}

	private function get_applepay_check_script(): string {
		return "<script>if (window.ApplePaySession) {jQuery('.bm-apple-pay').css('display', 'grid')}</script>";
	}
}
