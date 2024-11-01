<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require plugin_dir_path( __FILE__ ) . 'const.php';

function callAPI($method = "GET", $url, $parameters = []){
    $request_url = WP_GSF_API_URL."/".$url;
    $request = wp_remote_post($request_url, array(
    'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
    'body'        => json_encode($parameters, true),
    'method'      => $method,
    'data_format' => 'body',
    ));
    
  if( is_wp_error( $request ) ) {
        $error_string = $request->get_error_message();
      echo '<div id="message" class="error"><p>' . $error_string . '</p></div>';
    return false; 
  } else {
    return $request;
  }
}

/* is plugin active to check If a woocommerce Plugin is Activate */
function is_plugin_active_for_wp_gsf(){
    $active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );

    if ( in_array( 'woocommerce/woocommerce.php', $active_plugins ) ) {
      return true;
    } else {
      deactivate_plugins( plugin_basename( __FILE__ ) );
      $link = admin_url( 'plugin-install.php?s=woocommerce&tab=search&type=term' );
      wp_die( "Error: The Shopping Feed for Google plugin can not activate because the following required plugins are not installed or active: WooCommerce. Please activate this plugin if available or <a href='".$link."'>Install WooCommerce!</a>" );
      return false;
    }

}

/* wordpress redirect after plugin activation */
function wp_gsf_activation_redirect( $plugin ) {
      exit( wp_redirect( admin_url( 'admin.php?page=wp_gsf_endpoints' ) ) );    
}

function wp_gsf_save_shops_data()
{
  global $wpdb;
  $consumer_key    = 'ck_' . wc_rand_hash1();
  $consumer_secret = 'cs_' . wc_rand_hash1();

    if(is_check_active_woocommerce_rest_api()){
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

    $array_with_parameters = array(
    'user_id' => wp_get_current_user()->ID,
    'shop_url' => WP_BASE_URL,
    'description'     => "wp_gsf_".substr( $consumer_key, -7 ),
    'permissions'     => "read_write",
    'consumer_key'    => $consumer_key,
    'consumer_secret' => $consumer_secret,
    'shop_email' => wp_get_current_user()->user_email,
    'shop_owner' => wp_get_current_user()->display_name,
    'shop_name' => get_bloginfo('name')
    );

    $request = callAPI("POST","saveshopdata",$array_with_parameters);

    $body = wp_remote_retrieve_body( $request );
    $data = json_decode( $body );

    if($data){
        update_option('wp_gsf_shop_secret', serialize($data->shop_secret));
        update_option('wp_gsf_auth_id', serialize($data->auth_id));
    }

    update_option('woocommerce_api_enabled', 'yes');
    send_shop_data_information();
}

function wp_gsf_save_shops_deactivate(){
  global $wpdb;
    $array_with_parameters = array(
    'user_id' => wp_get_current_user()->ID,
    'shop_url' => WP_BASE_URL,
    'is_activated' => 0
    );

    callAPI("POST","is_activated_plugin",$array_with_parameters);

    $wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}woocommerce_api_keys" );
}

function wp_gsf_default_save_webhooks($data){
  global $wpdb;
  $prefix = $wpdb->prefix.WP_GSF_DB;

    $webhooks = array();
    if(isset($data['create'])){
      $webhooks = $data['create'];
    }

    $webhooks_query = "INSERT INTO ".$prefix."api_webhooks (status, name, user_id, topic, delivery_url) VALUES ";

    foreach ( $webhooks as $an_item ) {
      $webhooks_query .= $wpdb->prepare(
        "(%d, %s, %d, %s, %s),",
        1, $an_item['name'], 1, $an_item['topic'], $an_item['delivery_url']
      );
    }

    $webhooks_query = rtrim( $webhooks_query, ',' ) . ';';

    if($wpdb->query( $webhooks_query ))
    {
      return true;
    }
    else
    {
      return false;
    }
}

function get_wp_gsf_shop_secret(){
    global $wpdb;
    $wp_gsf_shop_secret = get_option('wp_gsf_shop_secret');
    return unserialize($wp_gsf_shop_secret);
}


/* woocommerce API to check if API KEY is Active or Not */
function is_check_active_woocommerce_rest_api(){
    global $wpdb;
      $table = $wpdb->prefix . 'woocommerce_api_keys';

      $array_with_parameters = array(
        'shop_url' => WP_BASE_URL
      );

      $request = callAPI("POST","ischeckrestapi",$array_with_parameters);

      $body = wp_remote_retrieve_body( $request );
      $data = json_decode( $body );
      $get_server_key = $data->wp_consumer_secret;

      $consumer_secret_list = $wpdb->get_results("select * from $table where consumer_secret='".$get_server_key."'");
      if(empty($consumer_secret_list)){
        return true;
      } else {
       return false;
      }
}

function wc_rand_hash1() {
  if ( ! function_exists( 'openssl_random_pseudo_bytes' ) ) {
    return sha1( wp_rand() );
  }

  return bin2hex( openssl_random_pseudo_bytes( 20 ) ); 
}

function wc_api_hash1( $data ) {
  return hash_hmac( 'sha256', $data, 'wc-api' );
}


function get_wp_shop_details(){
  $all_options = get_alloptions();
  $my_options = array();
  foreach( $all_options as $name => $value ) {
    if(!stristr($name, 'woocommerce_') && !stristr($name, 'widget_') && !stristr($name, '_transient')) $my_options[$name] = $value;
  }
  return $my_options;
}

function send_shop_data_information(){
      $shop_details = get_wp_shop_details();
      callAPI("POST","shop_information_hook",$shop_details);
}


