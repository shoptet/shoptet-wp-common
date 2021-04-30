<?php

namespace Shoptet;

class ShoptetExternal {

  static function init() {
    add_action( 'customize_register', [ get_called_class(), 'customize_register' ] );
    add_filter( 'get_shoptet_footer', [ get_called_class(), 'get_footer' ] );
    add_shortcode( 'shp_cta' , [ get_called_class(), 'get_cta' ] );
  }

  static function customize_register( $wp_customize ) {
    $wp_customize->add_section('shp_wp_general_settings', [
      'title' => 'Shoptet WP General Settings',
    ] );
    $wp_customize->add_setting('footer_id_setting', [
      'default' => 'shoptetcz',
    ] );
    $wp_customize->add_control('footer_id_setting', [
      'label' => 'Footer ID',
      'section' => 'shp_wp_general_settings',
      'type' => 'text',
    ] );
  }

  static function get_id() {
    return ( get_theme_mod( 'footer_id_setting' ) ?: 'shoptetcz' );
  }

  static function get_cta() {

    if ( isset($_GET['force_footer']) || false === ( $footer = get_transient( 'shoptet_cta' ) ) ) {
      $cta_url = ShoptetHelpers::get_shoptet_url( '/action/ShoptetFooter/render/?id=' . urlencode(self::get_id()) . '&cta=1' );
      $cta = file_get_contents($cta_url);
      set_transient( 'shoptet_cta', $cta, 24 * HOUR_IN_SECONDS );
    }

    return $cta;
  }

  static function get_footer() {

    if ( isset($_GET['force_footer']) || false === ( $footer = get_transient( 'shoptet_footer' ) ) ) {
      $footer_url = ShoptetHelpers::get_shoptet_url( '/action/ShoptetFooter/render/?id=' . urlencode(self::get_id()) );
      $footer = file_get_contents($footer_url);
      set_transient( 'shoptet_footer', $footer, 24 * HOUR_IN_SECONDS );
    }

    return $footer;
  }

  static function get_blog_posts( $args = [] ) {
    $url = add_query_arg( $args, 'https://blog.shoptet.cz/wp-json/wp/v2/posts' );
    $transient = 'shoptet_blog_posts_' . crc32($url);

    if ( false === ($posts = get_transient($transient)) ) {
      $response = wp_remote_get($url);
      if (is_wp_error($response)) {
        ShoptetLogger::capture_exception(new \Exception($response->get_error_message()));
        return [];
      }
      if ( 200 != $response['response']['code'] ) {
        ShoptetLogger::capture_exception(new \Exception(json_encode($response)));
        return [];
      }
      $posts = json_decode( $response['body'], true );
      set_transient( $transient, $posts, 1 * HOUR_IN_SECONDS );
    }
    
    return $posts;
  }

}