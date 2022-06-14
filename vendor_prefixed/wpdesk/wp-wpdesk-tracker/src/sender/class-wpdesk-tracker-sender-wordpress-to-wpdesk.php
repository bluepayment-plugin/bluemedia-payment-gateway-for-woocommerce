<?php

namespace BmWoocommerceVendor;

if (!\defined('ABSPATH')) {
    exit;
}
if (!\class_exists('BmWoocommerceVendor\\WPDesk_Tracker_Sender_Wordpress_To_WPDesk')) {
    class WPDesk_Tracker_Sender_Wordpress_To_WPDesk implements \WPDesk_Tracker_Sender
    {
        /**
         * URL to the WP Desk Tracker API endpoint.
         * @var string
         */
        private $api_url = 'https://data.wpdesk.org/?track=1';
        private $test_api_url = 'https://testdata.wpdesk.org/?track=1';
        private function get_api_url()
        {
            $api_url = $this->api_url;
            if (\apply_filters('wpdesk_tracker_use_testdata', \false)) {
                $api_url = $this->test_api_url;
            }
            return $api_url;
        }
        /**
         * Sends payload to predefined receiver.
         *
         * @param array $payload Payload to send.
         *
         * @throws WPDesk_Tracker_Sender_Exception_WpError Error if send failed.
         *
         * @return array If succeeded. Array containing 'headers', 'body', 'response', 'cookies', 'filename'.
         */
        public function send_payload(array $payload)
        {
            $response = \wp_remote_post($this->get_api_url(), array('method' => 'POST', 'timeout' => 5, 'redirection' => 5, 'httpversion' => '1.0', 'blocking' => \false, 'headers' => array('user-agent' => 'WPDeskTracker'), 'body' => \json_encode($payload), 'cookies' => array()));
            if ($response instanceof \WP_Error) {
                throw new \BmWoocommerceVendor\WPDesk_Tracker_Sender_Exception_WpError('Payload send error', $response);
            } else {
                return $response;
            }
        }
    }
}
