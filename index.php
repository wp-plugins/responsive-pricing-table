<?php
/*
Plugin Name: Responsive Pricing Table
Plugin URI: http://wordpress.org/plugins/
Description: Dynamic responsive pricing table for WordPress.
Version: 1.0
Author: Sayful Islam
Author URI: http://sayful.net
License: GPLv2 or later
*/
// Include others files
include_once('pricing-packages.php');
include_once('pricing-tables.php');
include_once('pricing-shortcode.php');

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