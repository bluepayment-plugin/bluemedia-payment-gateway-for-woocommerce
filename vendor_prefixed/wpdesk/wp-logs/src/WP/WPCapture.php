<?php

namespace BmWoocommerceVendor\WPDesk\Logger\WP;

class WPCapture
{
    /** @var string */
    private $filename;
    const LOG_DIR = 'wpdesk-logs';
    public function __construct($filename)
    {
        $this->filename = $filename;
    }
    /**
     * Add notice for directory.
     *
     * @param string $dir Directory.
     */
    private function add_notice_for_dir($dir)
    {
        new \BmWoocommerceVendor\WPDesk\Notice\Notice(\sprintf(
            // Translators: directory.
            \__('Can not enable WP Desk Debug log! Cannot create directory %s or this directory is not writeable!', 'wpdesk-helper'),
            $dir
        ), \BmWoocommerceVendor\WPDesk\Notice\Notice::NOTICE_TYPE_ERROR);
    }
    /**
     * Add notice for file.
     *
     * @param string $file File..
     */
    private function add_notice_for_file($file)
    {
        new \BmWoocommerceVendor\WPDesk\Notice\Notice(\sprintf(
            // Translators: directory.
            \__('Can not enable WP Desk Debug log! Cannot create file %s!', 'wpdesk-helper'),
            $file
        ), \BmWoocommerceVendor\WPDesk\Notice\Notice::NOTICE_TYPE_ERROR);
    }
    /**
     * Is debug log writable.
     *
     * @return bool
     */
    private function is_debug_log_writable_or_show_notice()
    {
        $log_dir = $this->get_log_dir();
        $log_file = $this->get_log_file();
        $index_file = $this->get_index_file();
        if (!\file_exists($log_dir)) {
            if (!\mkdir($log_dir, 0777, \true)) {
                $this->add_notice_for_dir($log_dir);
                return \false;
            }
        }
        if (!\file_exists($index_file)) {
            $index_html = \fopen($index_file, 'w');
            if (\false === $index_html) {
                $this->add_notice_for_file($index_file);
                return \false;
            } else {
                \fclose($index_html);
            }
        }
        if (!\file_exists($log_file)) {
            $log = \fopen($log_file, 'w');
            if (\false === $log) {
                $this->add_notice_for_file($log_file);
                return \false;
            } else {
                \fclose($log);
            }
        }
        if (!\is_writable($log_file)) {
            $this->add_notice_for_file($log_file);
            return \false;
        }
        return \true;
    }
    /**
     * Init debug log file.
     */
    public function init_debug_log_file()
    {
        if ($this->is_debug_log_writable_or_show_notice()) {
            \ini_set('log_errors', 1);
            \ini_set('error_log', $this->get_log_file());
        }
    }
    /**
     * Get uploads dir.
     *
     * @return string
     */
    private function get_uploads_dir()
    {
        $upload_dir = \wp_upload_dir();
        return \untrailingslashit($upload_dir['basedir']);
    }
    /**
     * Get log dir.
     *
     * @return string
     */
    private function get_log_dir()
    {
        return \trailingslashit($this->get_uploads_dir()) . self::LOG_DIR;
    }
    /**
     * Get log file.
     *
     * @return string
     */
    public function get_log_file()
    {
        return \trailingslashit($this->get_log_dir()) . $this->filename;
    }
    /**
     * Get log file.
     *
     * @return string
     */
    private function get_index_file()
    {
        return \trailingslashit($this->get_log_dir()) . 'index.html';
    }
}
