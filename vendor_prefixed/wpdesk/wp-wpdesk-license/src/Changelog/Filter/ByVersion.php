<?php

namespace BmWoocommerceVendor\WPDesk\License\Changelog\Filter;

use FilterIterator;
use Iterator;
/**
 * Filters items by version.
 *
 * @package WPDesk\License\Changelog
 */
class ByVersion extends \FilterIterator
{
    /**
     * @var string
     */
    private $version;
    /**
     * Updates constructor.
     *
     * @param Iterator $changes
     * @param string   $version
     */
    public function __construct(\Iterator $changes, string $version)
    {
        parent::__construct($changes);
        $this->version = $version;
    }
    /**
     * @return bool
     */
    public function accept()
    {
        $change = $this->getInnerIterator()->current();
        return (bool) \version_compare($change['version'], $this->version, '>');
    }
}
