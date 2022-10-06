<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin;

use Exception;

trait Environment {

	private static $__FILE__;
	private static $plugin_dir;
	private static $plugin_basename;
	private static $plugin_url;
	private static $plugin_assets_url;
	private static $plugin_js_url;
	private static $plugin_css_url;
	private static $plugin_images_url;
	private static $plugin_templates_dir;
	private static $plugin_prefix;
	private static $text_domain;


	/**
	 * @param string $key
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function get_from_config( string $key ) {
		if ( isset( self::$config[ $key ] ) ) {
			return self::$config[ $key ];
		}

		throw new Exception(
			"[Ilabs_Plugin] [get_from_config] Key: '$key' not exists"
		);
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public function get_plugin_url(): string {
		if ( ! self::$plugin_url ) {
			self::$plugin_url = plugin_dir_url( $this->get__file__() );
		}

		return self::$plugin_url;
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public function get_plugin_prefix(): string {
		if ( ! self::$plugin_prefix ) {
			self::$plugin_prefix = $this->get_from_config( 'slug' );
		}

		return self::$plugin_prefix;
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public function get_text_domain(): string {
		if ( ! self::$text_domain ) {
			self::$text_domain = $this->get_from_config( 'text_domain' );
		}

		return self::$text_domain;
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public function get_plugin_dir(): string {
		if ( ! self::$plugin_dir ) {
			self::$plugin_dir = plugin_dir_path( $this->get__file__() );
		}

		return self::$plugin_dir;
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public function get_plugin_basename(): string {
		if ( ! self::$plugin_basename ) {
			self::$plugin_basename = basename( dirname( $this->get__file__() ) );
		}

		return self::$plugin_basename;
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public function get_plugin_assets_url(): string {
		if ( ! self::$plugin_assets_url ) {
			self::$plugin_assets_url = $this->get_plugin_url() . 'assets';
		}

		return self::$plugin_assets_url;
	}

	/**
	 * @throws Exception
	 */
	public function get_plugin_js_url(): string {
		if ( ! self::$plugin_js_url ) {
			self::$plugin_js_url = $this->get_plugin_assets_url() . '/js';
		}

		return self::$plugin_js_url;
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public function get_plugin_css_url(): string {
		if ( ! self::$plugin_css_url ) {
			self::$plugin_css_url = $this->get_plugin_assets_url() . '/css';
		}

		return self::$plugin_css_url;
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public function get_plugin_images_url(): string {
		if ( ! self::$plugin_images_url ) {
			self::$plugin_images_url = $this->get_plugin_assets_url() . '/img';
		}

		return self::$plugin_images_url;
	}

	/**
	 * @param bool $add_directory_separator
	 *
	 * @return string
	 * @throws Exception
	 */
	public function get_plugin_templates_dir(
		bool $add_directory_separator = false
	): string {
		if ( ! self::$plugin_templates_dir ) {
			$directory_separator        = $add_directory_separator
				? DIRECTORY_SEPARATOR
				: '';
			self::$plugin_templates_dir = $this->get_plugin_dir()
			                              . DIRECTORY_SEPARATOR
			                              . 'templates';
		}

		return self::$plugin_templates_dir;
	}


	/**
	 * @throws Exception
	 */
	private function get__file__() {
		return self::$__FILE__
			?: $this->get_from_config( '__FILE__' );
	}

	public function get_uploads_dir(): string {
		return wp_upload_dir()['basedir'];
	}
}
