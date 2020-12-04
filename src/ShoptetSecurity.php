<?php

namespace Shoptet;

class ShoptetSecurity {
  
  static function init() {
    // Disable plugins/themes files editing
    define( 'DISALLOW_FILE_EDIT', true );

    // Disable XML-RPC
    add_filter( 'xmlrpc_enabled', '__return_false', PHP_INT_MAX );
    add_filter( 'xmlrpc_methods', '__return_empty_array', PHP_INT_MAX );

    // Disable user enumeration
    add_action( 'init', [ get_called_class(), 'reject_user_enumeration' ], PHP_INT_MAX );
    add_filter( 'rest_endpoints', [ get_called_class(), 'remove_users_from_rest' ], PHP_INT_MAX );
  }

  static function reject_user_enumeration() {
    if ( !is_admin() && !empty($_GET['author']) ) {
      wp_redirect(home_url());
      exit();
    }
  }

  static function remove_users_from_rest( $endpoints ) {
    if ( current_user_can('list_users') ) return $endpoints;

    if ( isset( $endpoints['/wp/v2/users'] ) ) {
      unset( $endpoints['/wp/v2/users'] );
    }
    if ( isset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] ) ) {
      unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
    }
    if ( isset( $endpoints['/oembed/1.0/embed'] ) ) {
      unset( $endpoints['/oembed/1.0/embed'] );
    }
    return $endpoints;
  }

}