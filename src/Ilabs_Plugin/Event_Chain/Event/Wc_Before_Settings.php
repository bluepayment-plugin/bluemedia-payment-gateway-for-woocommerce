<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Event;

use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Abstracts\Abstract_Event;

class Wc_Before_Settings extends Abstract_Event
{

    /**
     * @var string|null
     */
    private $section_id;

    public function __construct(string $section_id)
    {
        $this->section_id = $section_id;
    }


    public function create()
    {
        add_action("woocommerce_sections_{$this->section_id}",
            function () {
                $this->callback();
            });
    }

    /**
     * @return string|null
     */
    public function getSectionId(): ?string
    {
        return $this->section_id;
    }
}
