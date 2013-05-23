<?php

defined('WP_CONTENT_DIR') or define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
defined('WP_CONTENT_URL') or define('WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
defined('WP_PLUGIN_DIR') or define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');
defined('WP_PLUGIN_URL') or define('WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins');
