<?php
defined( 'ABSPATH' ) OR exit;

global $rylib_api_routes;
$rylib_api_routes = [];

// Helper function to add routes.
function rylib_api_add_route($route, $route_args, $version) {
  global $rylib_api_routes;
  $rylib_api_routes[$version][$route] = $route_args;
}

// Register our routes
add_action('rest_api_init', 'rylib_api_register_routes');
function rylib_api_register_routes() {
  global $rylib_api_routes;
  foreach ($rylib_api_routes as $version => $routes) {
    $namespace = "rylib-api/{$version}";
    foreach ( $routes as $route => $args ) {
      register_rest_route($namespace, $route, $args);
    }
  }
}
