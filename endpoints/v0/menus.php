<?php
defined( 'ABSPATH' ) OR exit;

// GET /menus/
// Example: /wp-json/rylib-api/v0/menus/
rylib_api_add_route('/menus', [
  array(
    'methods' => 'GET',
    'callback' => 'rylib_api_menus_callback',
  ),
], 'v0');

// GET /menus/[MENU_SLUG]
// Example: /wp-json/rylib-api/v0/menus/main-navigation
rylib_api_add_route('/menus/(?P<menu>[a-zA-Z0-9-_]+)', [
  array(
    'methods' => 'GET',
    'callback' => 'rylib_api_menus_callback',
    'args' => [
      'menu' => [
        'sanitize_callback' => 'rylib_api_sanitize_string'
      ]
    ]
  ),
], 'v0');

// GET /menus/[MENU_SLUG]/items
// Example: /wp-json/rylib-api/v0/menus/main-navigation/items
rylib_api_add_route('/menus/(?P<menu>[a-zA-Z0-9-_]+)/items', [
  array(
    'methods' => 'GET',
    'callback' => 'rylib_api_menus_items_callback',
    'args' => [
      'menu' => [
        'sanitize_callback' => 'rylib_api_sanitize_string'
      ]
    ]
  ),
], 'v0');

// Callback for routes: 
// - /menus/
// - /menus/[MENU_SLUG]
function rylib_api_menus_callback( WP_REST_Request $req ) {
  if ( $req->get_param('menu') ) {
    return rylib_api_response(
      'rylib_api_get_menu',
      $req->get_param('menu'),
      'menu'
    );
  } else {
    return rylib_api_response(
      'rylib_api_get_menus',
      [],
      'menus'
    );
  }
}

// Callback for route: GET /menus/[MENU_SLUG]/items
function rylib_api_menus_items_callback( WP_REST_Request $req ) {
  return rylib_api_response(
    'rylib_api_get_menu_items',
    $req->get_param('menu'),
    'menu_items'
  );
}

// Returns all the menu objects
function rylib_api_get_menus( $blog = null ) {
  $wp_menus = wp_get_nav_menus();
  $menus = [];
  foreach ( $wp_menus as $wp_menu ) {
    $menus[] = [
      'id' => $wp_menu->term_id,
      'name' => $wp_menu->name,
      'slug' => $wp_menu->slug,
      'items' => rylib_api_get_menu_items( $wp_menu )
    ];
  }
  return $menus;
}

// Returns the specified menu or 404
function rylib_api_get_menu( $menu, $blog = null ) {
  $wp_menu = wp_get_nav_menu_object( $menu );
  if ( !$wp_menu ) { 
    throw new Exception( "No menu was found matching id, slug, or name: '${menu}'", '404'); 
  }
  $menu = [
    'id' => $wp_menu->term_id,
    'name' => $wp_menu->name,
    'slug' => $wp_menu->slug,
    'items' => rylib_api_get_menu_items( $wp_menu )
  ];
  return $menu;
}

// Gets the menu items for the specified menu or 404
function rylib_api_get_menu_items( $menu, $blog = null, $args = [] ) {
  $wp_menu = wp_get_nav_menu_object( $menu );
  if ( !$wp_menu ) { 
    throw new Exception( "No menu was found matching id, slug, or name: '${menu}'", '404'); 
  }
  $nav_menu_items = wp_get_nav_menu_items( $wp_menu, $args );
  $menu_items = [];
  foreach ( $nav_menu_items as $menu_item ) {
    array_push( $menu_items, array( 
      'title' => $menu_item->title, 
      'url' => $menu_item->url,
    ) );
  }
  return $menu_items;
}
