<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class WP_GSF_Rest_Controller extends WP_REST_Controller {
 
    public function hook_wp_gsf_server(){
      //Update Shop Settings
      add_action( 'updated_option', array( $this, 'action_update_site_option'), 10, 3 ); 
      
     //api
      add_action( 'rest_api_init', array($this,'wp_gsf_register_api_endpoints') );
    }
    
    function action_update_site_option( $option_name, $old_value, $option_value ) { 
          send_shop_data_information();
          remove_action( 'updated_option', array( $this, 'action_update_site_option' ), 10, 3 );
    }
    
    function wp_gsf_register_api_endpoints() {
      //for testing
      register_rest_route( 'gsf/v1', '/reset', array(
        'methods' => 'POST',
        'callback' => array($this,'wp_gsf_reset_api'),
      ) );
    }
    
    public function wp_gsf_reset_api(){
        global $wpdb;
        
        $table = $wpdb->prefix . 'woocommerce_api_keys';
        
       if (!empty($_POST)){
           
          
           if(isset($_POST['shop_secret'])){
        
                    $wp_gsf_shop_secret = get_option('wp_gsf_shop_secret', null);
                    if ($wp_gsf_shop_secret !==  null) { 
                        $wp_gsf_shop_secret = unserialize($wp_gsf_shop_secret); 
                    }
                    
                    if($wp_gsf_shop_secret != $_POST['shop_secret']){
                        update_option('wp_gsf_shop_secret', serialize($_POST['shop_secret']));
                    }
           }
           if(isset($_POST['wp_consumer_key']) && isset($_POST['wp_consumer_secret'])){
               
               $consumer_key = $_POST['wp_consumer_key'];
               $consumer_secret = $_POST['wp_consumer_secret'];
               
               $consumer_secret_list = $wpdb->get_results("select * from $table where consumer_secret='".$consumer_secret."'");
               
                if(empty($consumer_secret_list)){
                            $data = array(
                              'user_id'         => wp_get_current_user()->ID,
                              'description'     => "wp_gsf_".substr( $consumer_key, -7 ),
                              'permissions'     => "read_write",
                              'consumer_key'    => wc_api_hash1( $consumer_key ),
                              'consumer_secret' => $consumer_secret,
                              'truncated_key'   => substr( $consumer_key, -7 ),
                            );
                    
                            $wpdb->insert(
                                $wpdb->prefix . 'woocommerce_api_keys',
                                $data,
                                array(
                                  '%d',
                                  '%s',
                                  '%s',
                                  '%s',
                                  '%s',
                                  '%s',
                                )
                      );
                }
           }
           echo "Shopping Feed for Google successfully reset.";
       } else {
           echo "Unauthorized user!";
       }
    }

}