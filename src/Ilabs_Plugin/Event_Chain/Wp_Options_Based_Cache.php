<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain;

use Ilabs\BM_Woocommerce\Ilabs_Plugin\Abstract_Ilabs_Plugin;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Cache_Interface;

class Wp_Options_Based_Cache
{

    /**
     * @var string
     */
    private $id;

    /**
     * @var Event_Chain
     */
    private $plugin;

    /**
     * @var int
     */
    private $max_items;


    public function __construct(string $id, Abstract_Ilabs_Plugin $plugin, int $max_items = null)
    {
        $this->id = $id;
        $this->plugin = $plugin;
        $this->max_items = $max_items;
    }

    public function push($value)
    {
        $data = get_option($this->get_options_key());
        if (!is_array($data)) {
            $data = [];
        } else {
            if (null !== $this->max_items && count($data) === $this->max_items) {
                $data = [];
            }
        }

        $data[] = $value;
        update_option($this->get_options_key(), $data);
    }

    /**
     * @return array|null
     */
    public function get(): ?array
    {
        $value = get_option($this->get_options_key());

        return empty($value) ? null : $value;
    }

    public function get_single(int $key)
    {
        $data = get_option($this->get_options_key());
        if (!is_array($data)) {
            return null;
        }

        return $data[$key];
    }

    public function clear()
    {
        delete_option($this->get_options_key());
    }

    private function get_options_key(): string
    {
        return $this->plugin->get_plugin_prefix() . '_' . $this->id;
    }
}
