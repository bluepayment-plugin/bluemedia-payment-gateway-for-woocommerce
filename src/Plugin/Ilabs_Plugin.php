<?php

namespace Inspire_Labs\BM_Woocommerce\Plugin;

abstract class Ilabs_Plugin {

	/**
	 * @return string
	 */
	protected function get_plugin_assets_url(): string {
		return BM_WOOCOMMERCE_PLUGIN_URL . '/' . 'assets';
	}

	/**
	 * @return string
	 */
	protected function get_plugin_file_path(): string {
		return BM_WOOCOMMERCE_PLUGIN_PATH;
	}

	protected function init() {
		$this->hooks();
	}

	private function hooks() {
		add_action( 'admin_enqueue_scripts',
			[ $this, 'admin_enqueue_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ] );
		//add_action( 'plugins_loaded', [ $this, 'load_plugin_text_domain' ] );
		add_filter( 'plugin_action_links_' . \plugin_basename( $this->get_plugin_file_path() ),
			[ $this, 'links_filter' ] );
	}

}
