<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain;

use Exception;

use Ilabs\BM_Woocommerce\Ilabs_Plugin\Abstract_Ilabs_Plugin;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Action_Interface;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Cache_Interface;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Condition_Interface;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Event_Interface;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Traits\Actions;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Traits\Conditions;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Traits\Events;

class Event_Chain
{

    use Actions;
    use Events;
    use Conditions;

    /**
     * @var Event_Chain_Item
     */
    private $current_event_chain_item;

    /**
     * @var Event_Chain_Item[]
     */
    private $event_chain_items;

    /**
     * @var int
     */
    private $current_post_id;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var Wc_Session_Cache
     */
    private $wc_session_cache;

    /**
     * @var Abstract_Ilabs_Plugin
     */
    private $ilabs_plugin;


    public function __construct(Abstract_Ilabs_Plugin $ilabs_plugin)
    {
        $this->ilabs_plugin = $ilabs_plugin;
        $this->current_event_chain_item = new Event_Chain_Item();
    }

    protected function get_event_chain(): self
    {
        return $this;
    }

    private function increment_event_chain_item()
    {
        $this->event_chain_items[] = $this->current_event_chain_item;
        $this->current_event_chain_item = new Event_Chain_Item();
    }

    /**
     * @throws Exception
     */
    protected function add_event(Event_Interface $event)
    {
        if ($this->current_event_chain_item->get_event() && !$this->current_event_chain_item->get_actions()) {
            throw new Exception("Don't add nex Event before Action call");
        } elseif ($this->current_event_chain_item->get_event() && $this->current_event_chain_item->get_actions()) {
            $this->increment_event_chain_item();
            $this->current_event_chain_item->set_event($event);
        } else {
            $this->current_event_chain_item->set_event($event);
        }
    }

    protected function add_action(Action_Interface $action)
    {
        $actions = $this->current_event_chain_item->get_actions();
        $actions[] = $action;
        $this->current_event_chain_item->set_actions($actions);
    }

    protected function add_condition(Condition_Interface $condition)
    {
        if (!$this->current_event_chain_item->get_event()) {
            $conditions = $this->current_event_chain_item->get_conditions_before_event();
            if (!$conditions) {
                $conditions = [];
            }
            $conditions[] = $condition;
            $this->current_event_chain_item->set_conditions_before_event($conditions);
        } else {
            $conditions = $this->current_event_chain_item->get_conditions_inside_event();
            if (!$conditions) {
                $conditions = [];
            }
            $conditions[] = $condition;

            $this->current_event_chain_item->set_conditions_inside_event($conditions);
        }
    }

    /**
     * @throws Exception
     */
    public function execute()
    {
        $this->increment_event_chain_item();

        if (!$this->event_chain_items) {
            throw new Exception("You must add at least one event chain item");
        }

        foreach ($this->event_chain_items as $event_chain_item) {
            if (!$this->assert_conditions_before_event($event_chain_item->get_conditions_before_event())) {
                continue;
            }

            $event = $event_chain_item->get_event();
            $this->register_event($event, $event_chain_item->get_actions(),
                $event_chain_item->get_conditions_inside_event());
        }

    }

    /**
     * @param Condition_Interface[] $conditions
     *
     * @return bool
     */
    private function assert_conditions_before_event(?array $conditions
    ): bool
    {
        if (null === $conditions) {
            return true;
        }

        foreach ($conditions as $condition) {
            if (!$condition->assert()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param Event_Interface $event
     * @param Action_Interface[] $actions
     * @param array|null $conditions_inside_event
     *
     * @return void
     */
    private function register_event(
        Event_Interface $event,
        array           $actions,
        array           $conditions_inside_event = null
    )
    {
        $event->set_actions($actions);
        if ($conditions_inside_event) {
            $event->set_conditions($conditions_inside_event);
        }

        $event->create();
    }

    /**
     * @return int
     */
    public function get_current_post_id(): int
    {
        return $this->current_post_id;
    }

    public function get_cache(): Cache_Interface
    {
        if (!$this->cache) {
            $this->cache = new Cache();
        }

        return $this->cache;
    }

    public function get_wc_session_cache($key = null): Cache_Interface
    {
        if (!$this->wc_session_cache) {
            $this->wc_session_cache = new Wc_Session_Cache($key);
        }

        return $this->wc_session_cache;
    }

    public function get_wp_options_based_cache($key, int $max_items = null): Wp_Options_Based_Cache
    {
        return new Wp_Options_Based_Cache($key, $this->ilabs_plugin, $max_items);
    }
}
