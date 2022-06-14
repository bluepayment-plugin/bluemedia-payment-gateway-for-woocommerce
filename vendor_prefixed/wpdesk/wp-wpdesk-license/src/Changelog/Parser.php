<?php

namespace BmWoocommerceVendor\WPDesk\License\Changelog;

use ArrayObject;
use BmWoocommerceVendor\WPDesk\License\Changelog\Parser\Line;
/**
 * Can parse changelog.
 *
 * @package FSVendor\WPDesk\License\Changelog
 */
class Parser
{
    /**
     * @var string
     */
    private $changelog;
    /**
     * @var string
     */
    private $changelog_parsed_data;
    /**
     * @var array
     */
    private $types = [];
    /**
     * Parser constructor.
     *
     * @param string $changelog
     */
    public function __construct(string $changelog)
    {
        $this->changelog = $changelog;
    }
    /**
     * @return ArrayObject
     */
    public function get_parsed_changelog()
    {
        return new \ArrayObject($this->changelog_parsed_data);
    }
    /**
     * @return Parser $this
     */
    public function parse()
    {
        $this->changelog_parsed_data = [];
        $version = $type = null;
        foreach ($this->get_lines() as $line) {
            if (!$this->types && ($types = $line->get_types())) {
                $this->types = $types;
                continue;
            }
            if ($release = $line->get_release_details()) {
                $version = $release['version'];
                $type = null;
                continue;
            }
            if ($type_details = $line->get_type_details()) {
                $type = $type_details;
                continue;
            }
            if (!$version || !$type) {
                continue;
            }
            if (!isset($this->changelog_parsed_data[$version])) {
                $this->changelog_parsed_data[$version] = ['version' => $version, 'changes' => []];
            }
            $this->changelog_parsed_data[$version]['changes'][$type][] = $line->get_value();
        }
        return $this;
    }
    /**
     * @return array
     */
    public function get_types()
    {
        return $this->types;
    }
    /**
     * @return Line[]
     */
    private function get_lines()
    {
        $content = \base64_decode($this->changelog);
        if (!$content) {
            return [];
        }
        return \array_map(function ($line) {
            return new \BmWoocommerceVendor\WPDesk\License\Changelog\Parser\Line($line);
        }, \array_filter(\preg_split("/\r\n|\n|\r/", \wp_kses_post($content))));
    }
}
