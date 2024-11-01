<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WP_GSF_Activator {

	public static function activate() {
		
		if (is_plugin_active_for_wp_gsf()) {
				wp_gsf_save_shops_data();
		        add_action( 'activated_plugin', 'wp_gsf_activation_redirect' );
		}
		
	}

}
