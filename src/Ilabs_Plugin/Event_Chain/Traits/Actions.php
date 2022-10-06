<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Traits;


use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Action\Action;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Action\Copy;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Action\Output_Template;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Event_Chain;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Readable_Interface;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Writable_Interface;

trait Actions
{

    abstract protected function get_event_chain(): Event_Chain;

    public function action_copy(
        callable $callable_arguments
    ): Event_Chain
    {

        $this->get_event_chain()
            ->add_action(new Copy($callable_arguments));

        return $this->get_event_chain();
    }

    public function action(
        callable $callable_arguments
    ): Event_Chain
    {

        $this->get_event_chain()
            ->add_action(new Action($callable_arguments));

        return $this->get_event_chain();
    }

    public function action_output_template(
        string $template
    ): Event_Chain
    {

        $this->get_event_chain()
            ->add_action(new Output_Template($template));

        return $this->get_event_chain();
    }
}
