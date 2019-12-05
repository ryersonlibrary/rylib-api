<?php
defined( 'ABSPATH' ) OR exit;

// Sanitizer for strings
function rylib_api_sanitize_string( $param, $request, $key ) {
  return filter_var( $param, FILTER_SANITIZE_STRING );
}