<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WP_GSF_Controller {


	protected $loader;
	protected $plugin_name;
	protected $version;

	public function __construct() {
		if ( defined( 'WP_GSF_PLUGIN_VERSION' ) ) {
			$this->version = WP_GSF_PLUGIN_VERSION;
		} else {
			$this->version = '1.0.1';
		}
		$this->plugin_name = 'shopping-feed-for-google';

	}

	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-gsf-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-gsf-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rest-controller.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Rest.inc.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/settings.php';
		$this->loader = new WP_GSF_Loader();
		$Custom_Rest = new WP_GSF_Rest_Controller();
		$Custom_Rest->hook_wp_gsf_server();
	}


	public function run() {
		$this->load_classes();
		$this->create_instances();

		try {
			$this->dependency_checker->check();
		} catch ( WP_GSF_Missing_Dependencies_Exception $e ) {
			// The exception contains the names of missing plugins.
			$this->report_missing_dependencies( $e->get_missing_plugin_names() );
			return;
		}
		$this->load_dependencies();
		//$this->define_product_hooks();
		$this->loader->run();
	}

	private function define_product_hooks() {

	}


	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}


	private function load_classes() {
		// Exceptions
		require_once dirname( __FILE__ ) . '/exceptions/Exception.php';
		require_once dirname( __FILE__ ) . '/exceptions/Missing_Dependencies_Exception.php';

		// Dependency checker
		require_once dirname( __FILE__ ) . '/Dependency_Checker.php';
		require_once dirname( __FILE__ ) . '/Missing_Dependency_Reporter.php';
	}

	private function create_instances() {
		$this->dependency_checker = new WP_GSF_Dependency_Checker();
	}
	
	/**
	 * @param string[] $missing_plugin_names
	 */
	private function report_missing_dependencies( $missing_plugin_names ) {
		$missing_dependency_reporter = new WP_GSF_Missing_Dependency_Reporter( $missing_plugin_names );
		$missing_dependency_reporter->bind_to_admin_hooks();
	}

}
