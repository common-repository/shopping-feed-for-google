<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
  if (!current_user_can('manage_options'))  {
    wp_die( __('You do not have sufficient pilchards to access this page.')    );
  }
  // Check whether the button has been pressed AND also check the nonce
  

  
  if (isset($_POST['wp_gsf_app_redirect']) && check_admin_referer('wp_gsf_app_redirect_app_button_clicked')) {

  $wp_gsf_shop_secret = get_option('wp_gsf_shop_secret', null);
  if ($wp_gsf_shop_secret !==  null) { $wp_gsf_shop_secret = unserialize($wp_gsf_shop_secret); }

  $wp_gsf_auth_id = get_option('wp_gsf_auth_id', null);
  if ($wp_gsf_auth_id !==  null) { $wp_gsf_auth_id = unserialize($wp_gsf_auth_id); }

    $array_with_parameters = array(
    'shop_secret' => $wp_gsf_shop_secret,
    'shop_url' => WP_BASE_URL
    );
    
    $request = callAPI("POST","verify_api_token",$array_with_parameters);

    if( is_wp_error( $request ) ) {
              wp_die( __( 'Please install and Activate WooCommerce.', 'woocommerce-addon-slug' ), 'Plugin dependency check', array( 'back_link' => true ) );

    }

    $body = wp_remote_retrieve_body( $request );
    $data = json_decode( $body );

    if(!empty($data) && isset($data->auth_url)){
       wp_redirect($data->auth_url);
    } else {
      if(!empty($data) && isset($data->message)){
          echo '<div id="message" class="error"><p>' . $data->message . '</p></div>';
      } else {
          echo '<div id="message" class="error"><p>Error Message</p></div>';
      }
    }


    exit();
  }
  
  
  function getRemoteDataContentHtml(){
    $shop_url = array('shop_url' => WP_BASE_URL);
    $request_api = callAPI("POST","is_check_app_status",$shop_url);
    $results = wp_remote_retrieve_body( $request_api );
    return json_decode( $results );
  }
  
 ?>