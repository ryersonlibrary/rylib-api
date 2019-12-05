<?php
defined( 'ABSPATH' ) OR exit;
/*
 * Plugin Name: Ryerson University Library API
 * Plugin URI: https://github.com/ryersonlibrary/rylib-api
 * Author: Ryerson University Library
 * Author URI: https://github.com/ryersonlibrary
 * Description: REST API endpoints for the Ryerson University Library WordPress site.
 * GitHub Plugin URI: https://github.com/ryersonlibrary/rylib-api
 * Version: 0.0.3-alpha
 */

// Include helper functions for this plugin.
require_once plugin_dir_path( __FILE__ ).'/inc/helpers.php';
require_once plugin_dir_path( __FILE__ ).'/inc/sanitizers.php';

// Include routes.
require_once plugin_dir_path( __FILE__ ).'/inc/routes.php';

// Include endpoints.
require_once plugin_dir_path( __FILE__ ).'/endpoints/v0/menus.php';
require_once plugin_dir_path( __FILE__ ).'/endpoints/v0/licenses.php';

// Do things before WordPress processes the request. We can hijack the 
// response here if we need to.
function rylib_api_pre_dispatch($result, $server, $request) {
  // Switch to the requested blog or fail.
  try {
    $blog = filter_var( $request->get_param('blog'), FILTER_SANITIZE_STRING );
    if ( $blog ) { rylib_api_wp_switch_to_blog( $blog ); }
  } catch (Exception $e) {
    return rylib_api_error($e);
  }
  return null;
}
add_action('rest_pre_dispatch', 'rylib_api_pre_dispatch', 10, 3);
