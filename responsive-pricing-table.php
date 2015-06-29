<?php
/*
Plugin Name: 	Responsive Pricing Table
Plugin URI: 	http://wordpress.org/plugins/responsive-pricing-table/
Description: 	Dynamic responsive pricing table for WordPress.
Version: 		1.1.0
Author: 		Sayful Islam
Author URI: 	http://sayfulit.com
Text Domain: 	pricingtable
Domain Path: 	/languages/
License: 		GPLv2 or later
*/

if ( !class_exists('Responsive_Pricing_Table') ):

class Responsive_Pricing_Table {

	protected static $instance = null;

	public function __construct(){
		add_action( 'plugins_loaded', array( $this, 'textdomain') );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts') );
		add_action( 'wp_enqueue_scripts', array( $this, 'front_scripts') );
		add_action( 'wp_enqueue_scripts', array( $this, 'front_scripts') );
		add_action( 'admin_menu', array( $this, 'admin_menu') );

		$this->includes();
	}

	public static function get_instance(){
		if (null == self::$instance) {
			$instance = new self;
		}

		return $instance;
	}

	public function includes(){
		include_once('pricing-tables.php');
		include_once('pricing-packages.php');
		include_once('pricing-shortcode.php');
	}

	public function textdomain() {
	  load_plugin_textdomain( 'pricingtable', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	public function admin_scripts() {
	    wp_enqueue_script('pricing-admin', plugins_url('js/pricing_admin.js', __FILE__), array('jquery'));
	}

	public function front_scripts() {
	    wp_enqueue_style('pricing-table-style', plugins_url('css/style.css', __FILE__));
	}

	public function admin_menu(){
		add_menu_page(
			__('Responsive Pricing Table', 'pricingtable'),
			__('Responsive Pricing Table', 'pricingtable'),
			'manage_options',
			'responsive-pricing-table',
			array( $this, 'admin_menu_callback' ),
			plugins_url( 'img/table.png' , __FILE__ ),
			35
		);
	}

	public function admin_menu_callback(){}

}

Responsive_Pricing_Table::get_instance();

function responsive_pricing_table_activation_deactivation() {
	Pricing_Package::custom_post();
	Pricing_Table::custom_post();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'responsive_pricing_table_activation_deactivation' );
register_deactivation_hook( __FILE__, 'responsive_pricing_table_activation_deactivation' );

endif;