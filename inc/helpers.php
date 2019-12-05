<?php
defined( 'ABSPATH' ) OR exit;

function rylib_api_response($callback_function, $callback_args, $response_object_name) {
  try {
    if ( is_array($callback_args) ) {
      $response_object = call_user_func_array($callback_function, $callback_args);
    } else {
      $response_object = call_user_func($callback_function, $callback_args);
    }
    $rest_response = new WP_REST_Response( [ $response_object_name => $response_object ], 200 );
    $rest_response->set_headers(array('Cache-Control' => 'max-age=3600, public'));
    return $rest_response;
  } catch ( Exception $e ) {
    $rest_response = new WP_REST_Response( 
      [
        'code' => rylib_api_get_error_code($e->getCode()),
        'message' => $e->getMessage(),
        'data' => [
          'status' => $e->getCode()
        ]
      ], 
      $e->getCode() 
    );
    $rest_response->set_headers(array('Cache-Control' => 'max-age=3600, public'));
    return $rest_response;
  }
}

function rylib_api_wp_switch_to_blog( $blog ) {
  if ( is_multisite() && $blog ) {  
    $site = get_sites( array('path' => '/' . $blog . '/') )[0]; 
    if ( !$site ) { 
      throw new Exception( "Could not find blog with name: '${blog}'" ); 
    }
    return switch_to_blog( $site->blog_id );
  } 
  return false;
}

function rylib_api_get_error_code($code) {
  switch ($code) {
    case 404:
      return 'not_found';
    default:
      return 'error';
  }
}
