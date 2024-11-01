<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

require plugin_dir_path( __FILE__ ) . 'includes/functions.php';
global $wpdb, $wp_version;
$prefix = $wpdb->prefix.WP_GSF_DB;


// Tables.
$wpdb->query( "DROP TABLE IF EXISTS {$prefix}api_webhooks" );
$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}woocommerce_api_keys" );

$parameters = array(
'shop_url' => WP_BASE_URL,
'app_status' => 0
);

//Update Plugin Status
callAPI("POST","plugin_status",$parameters);

