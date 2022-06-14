#!/bin/bash

export WPDESK_PLUGIN_SLUG=wp-desk-empik-woocommerce
export WPDESK_PLUGIN_TITLE="WP Desk Empik Woocommerce"

export WOOTESTS_IP=${WOOTESTS_IP:wootests}

sh ./vendor/wpdesk/wp-codeception/scripts/common_bootstrap.sh
