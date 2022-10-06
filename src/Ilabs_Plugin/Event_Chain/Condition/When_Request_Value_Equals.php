<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Condition;

use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Abstracts\Abstract_Condition;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Condition_Interface;
use Ilabs\BM_Woocommerce\Plugin;

class When_Request_Value_Equals extends Abstract_Condition implements Condition_Interface
{
    /**
     * @var string
     */
    private $key;
    private $test_value;

    public function __construct(string $key, $test_value)
    {
        $this->key = $key;
        $this->test_value = $test_value;
    }

    public function assert(): bool
    {
        $value = (new Plugin())->get_request()->get_by_key($this->key);
        return $this->test_value === $value;
    }

    /**
     * @return string
     */
    public function get_key(): string
    {
        return $this->key;
    }
}
