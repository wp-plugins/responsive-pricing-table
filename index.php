<?php
/*
Plugin Name: Responsive Pricing Table
Plugin URI: http://wordpress.org/plugins/responsive-pricing-table/
Description: Dynamic responsive pricing table for WordPress.
Version: 1.0.1
Author: Sayful Islam
Author URI: http://sayful.net
Text Domain: pricingtable
Domain Path: /languages/
License: GPLv2 or later
*/
// Include others files
include_once('pricing-packages.php');
include_once('pricing-tables.php');
include_once('pricing-shortcode.php');

/**
 * Load plugin textdomain.
 */
function sis_wp_pricing_load_textdomain() {
  load_plugin_textdomain( 'pricingtable', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'sis_wp_pricing_load_textdomain' );

/*
 * WordPress Admin Dashborad Scripts and Styles
 */

function sis_wp_pricing_admin_scripts() {
    wp_enqueue_script('pricing-admin', plugins_url('js/pricing_admin.js', __FILE__), array('jquery'));
}

add_action('admin_enqueue_scripts', 'sis_wp_pricing_admin_scripts');



/*
 * Pricing Table Frontend Scripts
 */

function sis_wp_pricing_front_scripts() {
    wp_enqueue_style('pricing-table-style', plugins_url('css/style.css', __FILE__));
}

add_action('wp_enqueue_scripts', 'sis_wp_pricing_front_scripts');