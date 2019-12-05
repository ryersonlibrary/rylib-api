<?php
defined( 'ABSPATH' ) OR exit;

define('OURDB_BASE_URL', 'https://ryerson.scholarsportal.info/licenses');

// TODO: Set up a caching layer just in case scholarsportal is down for whatever reason

// GET /licenses/
// Example: /wp-json/rylib-api/v0/licenses/
rylib_api_add_route('/licenses', [
  array(
    'methods' => 'GET',
    'callback' => 'rylib_api_licenses_callback',
    'args' => [
      'provider' => [
        'sanitize_callback' => 'rylib_api_sanitize_string'
      ]
    ]
  ),
], 'v0');

// GET /licenses/[LICENSE_TAG]
// Example: /wp-json/rylib-api/v0/licenses/Open_access_generic
rylib_api_add_route('/licenses/(?P<license_tag>[a-zA-Z0-9-_]+)', [
  array(
    'methods' => 'GET',
    'callback' => 'rylib_api_license_callback',
    'args' => [
      'license_tag' => [
        'sanitize_callback' => 'rylib_api_sanitize_string'
      ]
    ]
  ),
], 'v0');


// Callback for routes: 
// - /licenses/
function rylib_api_licenses_callback( WP_REST_Request $req ) {
  return rylib_api_response(
    'rylib_api_find_license',
    $req->get_param('provider'),
    'license'
  );
}

// Callback for routes: 
// - /licenses/[LICENSE_TAG]
function rylib_api_license_callback( WP_REST_Request $req ) {
  return rylib_api_response(
    'rylib_api_get_license',
    $req->get_param('license_tag'),
    'license'
  );
}

// Returns the mapped license for the specified resource name from OURdb 
// if it exists, otherwise returns 404.
function rylib_api_find_license($provider) {
  $request_url = OURDB_BASE_URL . "/json/?name={$provider}";
  $response = wp_remote_get( $request_url );
  $json = json_decode(wp_remote_retrieve_body( $response ), true );
  if ( !$json ) { 
    throw new Exception( "No license was found for provider: '${provider}'", '404'); 
  }
  $json['license-uri'] = OURDB_BASE_URL . "/{$json['license-tag']}";
  
  return $json;
}

// Returns the mapped license for the specified resource name from OURdb 
// if it exists, otherwise returns 404.
function rylib_api_get_license($license_tag) {
  $request_url = OURDB_BASE_URL . "/json/{$license_tag}";
  $response = wp_remote_get( $request_url );
  $json = json_decode(wp_remote_retrieve_body( $response ), true );
  if ( !$json ) { 
    throw new Exception( "No license was found with the name: '${license_tag}'", '404'); 
  }
  $json['license-uri'] = OURDB_BASE_URL . "/{$json['license-tag']}";
  
  return $json;
}
