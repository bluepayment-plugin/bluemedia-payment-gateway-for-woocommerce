<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Traits;

use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Condition\Is_Admin;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Condition\When;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Condition\When_Is_Not_Ajax;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Condition\When_Is_Product;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Condition\When_Is_Shop;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Condition\When_Request_Key_Exist;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Condition\When_Request_Value_Equals;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Event_Chain;

trait Conditions
{

    abstract protected function get_event_chain(): Event_Chain;

    public function when_is_admin(): Event_Chain
    {

        $this->get_event_chain()->add_condition(new Is_Admin());

        return $this->get_event_chain();
    }

    public function when(callable $callable_arguments = null): Event_Chain
    {
        $this->get_event_chain()
            ->add_condition(new When($callable_arguments));

        return $this->get_event_chain();
    }

    public function when_request_key_exist(string $key): Event_Chain
    {
        $this->get_event_chain()
            ->add_condition(new When_Request_Key_Exist($key));

        return $this->get_event_chain();
    }

    public function when_request_value_equals(string $key, $test_value): Event_Chain
    {
        $this->get_event_chain()
            ->add_condition(new When_Request_Value_Equals($key, $test_value));

        return $this->get_event_chain();
    }

    public function when_is_shop(): Event_Chain
    {
        $this->get_event_chain()
            ->add_condition(new When_Is_Shop());

        return $this->get_event_chain();
    }

    public function when_is_product(): Event_Chain
    {
        $this->get_event_chain()
            ->add_condition(new When_Is_Product());

        return $this->get_event_chain();
    }

    public function when_is_not_ajax(): Event_Chain
    {
        $this->get_event_chain()
            ->add_condition(new When_Is_Not_Ajax());

        return $this->get_event_chain();
    }
}
