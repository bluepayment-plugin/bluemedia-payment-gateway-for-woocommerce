<?php


namespace Inspire_Labs\BM_Woocommerce;


class Alerts {

	/**
	 * @var array
	 */
	static $notice = [];

	/**
	 * @var array
	 */
	static $error = [];

	/**
	 * @var array
	 */
	static $success = [];

	/**
	 * @var bool
	 */
	static $alerts_rendered = false;

	/**
	 * Alerts constructor.
	 */
	public function __construct() {
		$this->print_alerts_once();
	}


	/**
	 * Short description
	 *
	 * @param array|string $notice
	 */
	public function add_notice( $notice, $context = '' ) {
		if ( isset( self::$notice[ $context ] ) ) {
			self::$notice[ $context ] = array_merge( self::$notice[ $context ],
				$notice );
		} else {
			self::$notice[ $context ][] = $notice;
		}
	}

	/**
	 * Short description
	 *
	 * @param array|string $success
	 */
	public function add_success( $success, $context = '' ) {
		if ( isset( self::$success[ $context ] ) ) {
			self::$success[ $context ] = array_merge( self::$success[ $context ],
				$success );
		} else {
			self::$success[ $context ][] = $success;
		}
	}

	/**
	 * Short description
	 *
	 * @param array|string $error
	 */
	public function add_error( $error, $context = 'global' ) {
		if ( isset( self::$error[ $context ] ) ) {
			self::$error[ $context ] = array_merge( self::$error[ $context ],
				$error );
		} else {
			self::$error[ $context ][] = $error;
		}
	}

	/**
	 * Short description
	 */
	public function print_alerts_once() {
		if ( ! self::$alerts_rendered ) {
			add_action(
				"admin_notices",
				function () {
					$this->print_alerts();
					self::$alerts_rendered = true;
				}

			);
		}
	}

	/**
	 * @return void
	 */
	public function print_alerts() {

		foreach ( array_keys( self::$success ) as $context ) {
			self::$success[ $context ] = array_unique( self::$success[ $context ] );

			foreach ( self::$success[ $context ] as $k => $v ) {
				if ( ! empty( $v ) ) {

					$output = sprintf( "<div class='notice notice-success'><p>%s</p></div>",
						$v );
					echo wp_kses(
						$output,
						[
							"div" => [ "class" => [] ],
							"p"   => [],
							"b"   => [],
						]
					);
				}
			}
		}


		foreach ( array_keys( self::$notice ) as $context ) {
			self::$notice[ $context ] = array_unique( self::$notice[ $context ] );

			foreach ( self::$notice[ $context ] as $k => $v ) {
				if ( ! empty( $v ) ) {

					$output = sprintf( "<div class='notice notice-info'><p>%s</p></div>",
						$v );
					echo wp_kses(
						$output,
						[
							"div" => [ "class" => [] ],
							"p"   => [],
							"b"   => [],
						]
					);
				}
			}
		}

		foreach ( array_keys( self::$error ) as $context ) {
			self::$error[ $context ] = array_unique( self::$error[ $context ] );

			foreach ( self::$error[ $context ] as $k => $v ) {
				if ( ! empty( $v ) ) {

					$output = sprintf( "<div class='notice notice-error error'><p>%s</p></div>",
						$v );
					echo wp_kses(
						$output,
						[
							"div" => [ "class" => [] ],
							"p"   => [],
							"b"   => [],
						]
					);
				}
			}
		}

		self::$error   = [];
		self::$success = [];
		self::$notice  = [];
	}


	/**
	 * @return array
	 */
	public function get_alerts_unformatted(): array {
		$success = [];
		$notices = [];
		$errors  = [];

		foreach ( array_keys( self::$success ) as $context ) {
			$success[ $context ] = array_unique( self::$success[ $context ] );
		}


		foreach ( array_keys( self::$notice ) as $context ) {
			$notices[ $context ] = array_unique( self::$notice[ $context ] );
		}

		foreach ( array_keys( self::$error ) as $context ) {
			$errors[ $context ] = array_unique( self::$error[ $context ] );
		}

		return [
			'success' => self::$success,
			'notice'  => self::$notice,
			'error'   => self::$error,
		];
	}

	/**
	 * @param $context
	 *
	 * @return array
	 */
	public function get_alerts_unformatted_by_context( $context ): array {
		$ret = [];

		foreach ( $this->get_alerts_unformatted() as $type => $v ) {
			foreach ( $v as $context_in_loop => $messages ) {
				if ( $context === $context_in_loop ) {
					$ret[ $type ][] = $messages;
				}
			}

		}

		return $ret;
	}
}
