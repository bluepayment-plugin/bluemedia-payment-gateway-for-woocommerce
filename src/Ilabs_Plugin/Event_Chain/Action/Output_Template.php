<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Action;

use Exception;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Abstracts\Abstract_Action;
use Ilabs\BM_Woocommerce\Plugin;

class Output_Template extends Abstract_Action
{

    /**
     * @var string
     */
    private $template;

    public function __construct(
        string $template
    )
    {
        $this->template = $template;
    }

    /**
     * @throws Exception
     */
    public function run()
    {
        $path = (new Plugin())->get_plugin_templates_dir() .
            DIRECTORY_SEPARATOR . $this->template;

        include($path);
    }

    /**
     * @return string
     */
    public function get_template(): string
    {
        return $this->template;
    }
}
