<?php

/**
 * Plugin Name: HTML Pages
 * Plugin URI: https://inboundlatino.com/htmlpages
 * Description: Create pure HTML pages without any of the WordPress code.
 * Version: 1.0.0
 * Author: Jose Sotelo
 * Author URI: https://inboundlatino.com
 * Requires at least: 4.1
 *
 * Text Domain: htmlpages
 *
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
function run_html_pages() {
    require_once plugin_dir_path( __FILE__ ) . 'htmlPages.php';
    $plugin = new htmlPages();
}
run_html_pages();