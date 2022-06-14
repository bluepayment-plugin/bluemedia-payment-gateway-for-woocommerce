<?php

use WPDesk\Codeception\Tests\Acceptance\Cest;

class ActivationCest extends Cest {

	/**
	 * Deactivate plugins before tests.
	 *
	 * @param AcceptanceTester $i .
	 *
	 * @throws \Codeception\Exception\ModuleException .
	 */
	public function _before( AcceptanceTester $i ) {
		$i->loginAsAdmin();
		$i->amOnPluginsPage();
		$i->deactivatePlugin( $this->getPluginSlug() );
		$i->amOnPluginsPage();
		$i->deactivatePlugin( 'woocommerce' );
	}

	/**
	 * Plugin activation.
	 *
	 * @param AcceptanceTester $i .
	 *
	 * @throws \Codeception\Exception\ModuleException .
	 */
	public function pluginActivation( AcceptanceTester $i ) {

		$i->loginAsAdmin();

		$i->amOnPluginsPage();
		$i->seePluginDeactivated( $this->getPluginSlug() );

		$i->activateWPDeskPlugin(
			$this->getPluginSlug(),
			array( 'woocommerce' ),
			array( 'The “WP Desk Plugin Template” plugin cannot run without WooCommerce active. Please install and activate WooCommerce plugin.' )
		);

	}
}
