<?php

namespace BmWoocommerceVendor\WPDesk\License\Changelog\Parser;

/**
 * Can parse single changelog line.
 *
 * @package WPDesk\License\Changelog\Parser
 */
class Line
{
    /**
     * @var string
     */
    private $line;
    /**
     * Line constructor.
     *
     * @param string $line
     */
    public function __construct(string $line)
    {
        $this->line = $line;
    }
    /**
     * @return array
     */
    public function get_release_details()
    {
        \preg_match('/## \\[(.*)\\] - (.*)/', $this->line, $output_array);
        if (!isset($output_array[1], $output_array[2])) {
            return [];
        }
        return ['version' => $output_array[1], 'date' => $output_array[2]];
    }
    /**
     * @return string
     */
    public function get_type_details()
    {
        \preg_match('/### (.*)/', $this->line, $output_array);
        if (!isset($output_array[1])) {
            return '';
        }
        return $output_array[1];
    }
    /**
     * @return array
     */
    public function get_types()
    {
        \preg_match('/##### (.*)/', $this->line, $output_array);
        if (!isset($output_array[1])) {
            return [];
        }
        return \wp_parse_list($output_array[1]);
    }
    /**
     * @return string
     */
    public function get_value()
    {
        return \ltrim($this->line, '- ');
    }
}
