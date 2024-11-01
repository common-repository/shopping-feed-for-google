<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



/** Step 1. */
function wp_gsf_add_admin_menu() {

	$filename_icon = sanitize_file_name('wp-gsf.png'); 
	$page_title = 'Shopping Feed for Google';
	$menu_title = 'Shopping Feed for Google';
	$capability = 'manage_options';
	$menu_slug = 'wp_gsf_endpoints';
	$function = 'wp_gsf_menu_callback';
	$icon_url  = plugin_dir_url( __FILE__ ) . 'assets/img/'.$filename_icon;
	$position  = 81;
	add_menu_page(  $page_title,  $menu_title,  $capability,  $menu_slug,  $function  ,$icon_url ,$position );
}

/** Step 2 (from text above). */
add_action( 'admin_menu', 'wp_gsf_add_admin_menu' );

/** Step 3. */
function wp_gsf_menu_callback() {
	
	require_once 'wp-gsf-endpoints.php';
}

