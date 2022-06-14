<?php

namespace BmWoocommerceVendor\WPDesk\License\Changelog;

use Iterator;
/**
 * Can format changelog.
 *
 * @package WPDesk\License\Changelog
 */
class Formatter
{
    /**
     * @var Iterator
     */
    private $changes;
    /**
     * @var array
     */
    private $types;
    /**
     * Formatter constructor.
     *
     * @param Iterator $changes
     */
    public function __construct(\Iterator $changes)
    {
        $this->changes = $changes;
    }
    /**
     * @param array $types
     */
    public function set_changelog_types(array $types)
    {
        $this->types = $types;
    }
    /**
     * @return string
     */
    public function prepare_formatted_html()
    {
        $output = '';
        foreach ($this->get_changes_data() as $name => $changes) {
            if (empty($changes)) {
                continue;
            }
            $output .= \sprintf("\n\n<strong>%s</strong>: <br/>* %s", $name, \implode(' <br />* ', \array_map('esc_html', $changes)));
        }
        return \wp_kses_post(\nl2br(\trim($output)));
    }
    /**
     * @return array
     */
    private function get_changes_data()
    {
        $changes = [];
        foreach ($this->types as $type) {
            $changes[$type] = [];
        }
        foreach ($this->changes as $item) {
            foreach ($item['changes'] as $type => $change) {
                if (!isset($changes[$type])) {
                    $changes[$type] = [];
                }
                $changes[$type] = \array_merge($changes[$type], $change);
            }
        }
        return \array_filter($changes);
    }
}
