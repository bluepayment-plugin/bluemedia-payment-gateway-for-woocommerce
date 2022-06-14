<?php

namespace BmWoocommerceVendor\WPDesk\PluginBuilder\Storage;

class StorageFactory
{
    /**
     * @return PluginStorage
     */
    public function create_storage()
    {
        return new \BmWoocommerceVendor\WPDesk\PluginBuilder\Storage\WordpressFilterStorage();
    }
}
