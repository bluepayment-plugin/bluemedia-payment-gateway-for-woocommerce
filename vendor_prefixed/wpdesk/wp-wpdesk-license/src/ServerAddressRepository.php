<?php

namespace BmWoocommerceVendor\WPDesk\License;

/**
 * Provides server urls to check for upgrade/activation
 *
 * @package WPDesk\License
 */
class ServerAddressRepository
{
    /** @var string */
    private $product_id;
    /**
     * @param string $product_id Product if of a plugin. Retrieve from plugin_info
     */
    public function __construct($product_id)
    {
        $this->product_id = $product_id;
    }
    /**
     * Returns default server to ping ie. when checking if upgrade is available but is not yet activated
     *
     * @return string
     */
    public function get_default_update_url()
    {
        $urls = $this->get_server_urls();
        return \reset($urls);
    }
    /**
     * Return list of servers to check for update
     *
     * @return string[] Full URL with protocol. Without ending /
     */
    public function get_server_urls()
    {
        // PL version should be default for most plugins
        $servers = ['https://www.wpdesk.pl', 'https://www.wpdesk.net'];
        $servers[] = 'https://flexibleinvoices.com';
        if ($this->is_invoice_product($this->product_id)) {
            $servers = \array_reverse($servers);
            // set invoice server as first to check
        }
        $servers[] = 'https://shopmagic.app';
        if ($this->is_magic_plugin($this->product_id)) {
            $servers = \array_reverse($servers);
            // set magic server as first to check
        }
        $servers[] = 'https://flexibleshipping.com';
        if ($this->is_shipping_plugin($this->product_id)) {
            $servers = \array_reverse($servers);
            // set fs server as first to check
        }
        return $servers;
    }
    /**
     * Is product id of a ShopMagic Plugin?
     *
     * @param string $product_id
     *
     * @return bool
     */
    private function is_magic_plugin($product_id)
    {
        return \stripos($product_id, 'ShopMagic') !== \false;
    }
    /**
     * Is product id of a ShopMagic Plugin?
     *
     * @param string $product_id
     *
     * @return bool
     */
    private function is_invoice_product($product_id)
    {
        return \stripos($product_id, 'Invoices') !== \false;
    }
    /**
     * Is product id of a Flexible Shipping plugin?
     *
     * @param string $product_id
     *
     * @return bool
     */
    private function is_shipping_plugin($product_id)
    {
        $plugins = ['DHL', 'UPS', 'FedEx', 'Royal', 'Hermes', 'DPD', 'Flexible Shipping'];
        // if one of the $plugins phrase found in product_id
        return \count(\array_filter($plugins, static function ($plugin) use($product_id) {
            return \stripos($product_id, $plugin) !== \false;
        })) > 0;
    }
}
