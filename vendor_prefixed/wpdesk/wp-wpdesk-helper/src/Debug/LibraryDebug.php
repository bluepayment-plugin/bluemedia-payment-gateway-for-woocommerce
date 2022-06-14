<?php

namespace BmWoocommerceVendor\WPDesk\Helper\Debug;

/**
 * Can gather info about used libraries
 *
 * @package WPDesk\Helper\Debug
 */
class LibraryDebug
{
    const COMPOSER_KEY_VERSION = 'version';
    const COMPOSER_KEY_NAME = 'name';
    const COMPOSER_KEY_PACKAGES = 'packages';
    const REPORT_KEY_LIBRARY_NAME = 'library_name';
    const REPORT_KEY_LIBRARY_VERSION = 'library_version';
    const LIBRARY_VERSION_UNKNOWN = 'unknown';
    /**
     * Prepares array with report about used libraries
     *
     * @param array $vendor_files_report
     *
     * @return array
     */
    public function get_libraries_report(array $vendor_files_report)
    {
        $libraries = [];
        foreach ($vendor_files_report as $file) {
            $library_name = $file[self::REPORT_KEY_LIBRARY_NAME];
            if (isset($libraries[$library_name])) {
                $libraries[$library_name][] = $file[self::REPORT_KEY_LIBRARY_VERSION];
            } else {
                $libraries[$file[self::REPORT_KEY_LIBRARY_NAME]] = [$file[self::REPORT_KEY_LIBRARY_VERSION]];
            }
            $libraries[$library_name] = \array_unique($libraries[$library_name]);
        }
        return $libraries;
    }
    /**
     * Returns array with used wpdesk files from vendor dir
     *
     * @return array
     */
    private function get_used_wpdesk_vendor_files()
    {
        $all_files = \get_included_files();
        $vendor_files = \array_filter($all_files, function ($filename) {
            return \preg_match('/\\/vendor\\/wpdesk\\//', $filename);
        });
        return $vendor_files;
    }
    /**
     * Returns array with report about used files
     *
     * @return array
     */
    public function get_wpdesk_vendor_files_report()
    {
        $vendor_files = $this->get_used_wpdesk_vendor_files();
        $loaded = [];
        foreach ($vendor_files as $vendor_file) {
            $library_name = \preg_match('/\\/wpdesk\\/([^\\/]+)/', $vendor_file, $matches) ? $matches[1] : '';
            $plugin_path = \preg_match('/(.+\\/plugins\\/[^\\/]+)/', $vendor_file, $matches) ? $matches[1] : '';
            $loaded[] = ['plugin_name' => \preg_match('/\\/plugins\\/([^\\/]+)/', $vendor_file, $matches) ? $matches[1] : '', 'plugin_path' => $plugin_path, self::REPORT_KEY_LIBRARY_NAME => $library_name, self::REPORT_KEY_LIBRARY_VERSION => $this->get_library_version($plugin_path, $library_name), 'file' => $vendor_file];
        }
        return $loaded;
    }
    /**
     * @param string $plugin_path  Path to the plugin that loads the library
     * @param string $library_name Library name without vendor ie. wp-logs
     *
     * @return string Returns 'unknown' when version can't be found
     */
    private function get_library_version($plugin_path, $library_name)
    {
        $lock_file = $plugin_path . '/composer.lock';
        static $cache;
        if (\file_exists($lock_file)) {
            if (!\is_array($cache)) {
                $cache = [];
            }
            if (!isset($cache[$lock_file])) {
                $cache[$lock_file] = \json_decode(\file_get_contents($lock_file), \true);
            }
            $library_version = \array_reduce($cache[$lock_file][self::COMPOSER_KEY_PACKAGES], function ($carry, $package_info) use($library_name) {
                if ($package_info[self::COMPOSER_KEY_NAME] === 'wpdesk/' . $library_name) {
                    return $package_info[self::COMPOSER_KEY_VERSION];
                }
                return $carry;
            });
            if ($library_version) {
                return $library_version;
            }
        }
        return self::LIBRARY_VERSION_UNKNOWN;
    }
}
