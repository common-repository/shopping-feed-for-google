<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/*
Plugin Name: Shopping Feed for Google
Plugin URI: http://wordpress.org/extend/plugins/shopping-feed-for-google/
Description: Improve Google Shopping Ad Spend By Shopping Feed for Google.
Version: 1.0
Author: Simprosys InfoMedia
Author URI: https://simprosys.com/
*/

require plugin_dir_path( __FILE__ ) . 'includes/functions.php';


function wp_gsf_activate_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-gsf-activator.php';
	WP_GSF_Activator::activate();
}


function wp_gsf_deactivate_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-gsf-deactivator.php';
	WP_GSF_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'wp_gsf_activate_plugin' );
register_deactivation_hook( __FILE__, 'wp_gsf_deactivate_plugin' );

require plugin_dir_path( __FILE__ ) . 'includes/class-wp-gsf.php';


function wp_gsf_run_api() {
	$wp_gsf_plugin = new WP_GSF_Controller();
	$wp_gsf_plugin->run();
}
wp_gsf_run_api();