<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class WP_GSF_Deactivator {

	public static function deactivate() {
		if (is_plugin_active_for_wp_gsf()) {
		    wp_gsf_save_shops_deactivate();
		} else {
			flush_rewrite_rules();
		}	
	}

}

 


