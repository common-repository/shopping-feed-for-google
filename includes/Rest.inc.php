<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function wp_gsf_findRandom() {
    $mRandom = rand(48, 122);
    return $mRandom;
}

function wp_gsf_isRandomInRange($mRandom) {
    if(($mRandom >=58 && $mRandom <= 64) ||
            (($mRandom >=91 && $mRandom <= 96))) {
        return 0;
    } else {
        return $mRandom;
    }
}

function wp_gsf_api_key_gen(){
    $output = '';
      for($loop = 0; $loop <= 31; $loop++) {
          for($isRandomInRange = 0; $isRandomInRange === 0;){
              $isRandomInRange = wp_gsf_isRandomInRange(wp_gsf_findRandom());
          }
          $output .= html_entity_decode('&#' . $isRandomInRange . ';');
      }
      return $output;
}

function wp_gsf_get_settings($key=''){
  $wp_gsf_settings = get_option('wp_gsf_settings_meta');
  if($key){
    return $wp_gsf_settings[$key];
  }else{
    return $wp_gsf_settings;
  }
}

function wp_gsf_get_root_secret(){
  $wp_gsf_settings = gsf_get_settings();
  $root_secret = $wp_gsf_settings['root_secret'];
  return $root_secret;
}