<?php

namespace Shoptet;

class ShoptetPostCount {

  static function init() {
    add_action( 'rest_api_init', [ get_called_class(), 'register_rest_route' ] );
  }

  static function register_rest_route() {
    register_rest_route( 'post-count-api/v1', '/count', [
			'methods' => 'GET',
      'callback' => [ get_called_class(), 'rest_endpoint_callback' ],
      'permission_callback' => '__return_true',
    ] );
  }

  static function rest_endpoint_callback() {
    $items = [];
		
		$query_args = apply_filters( 'shoptet_post_count_query_args', [
			'postsCount' => [
				'post_type' => 'post',
				'post_status' => [ 'publish' ],
			],
		] );
    
		foreach( $query_args as $export_key_name => $args ) {
			$items[ $export_key_name ] = self::get_posts_count( $args );
		}

		return apply_filters( 'shoptet_post_count_result', $items );
  }

	static function get_posts_count( $args ) {
		$default_args = [
			'posts_per_page' => -1,
			'fields' => 'ids',
			'no_found_rows' => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		];
		$query = new \WP_Query( array_merge( $default_args, $args ) );
		return count( $query->posts );
	}

}