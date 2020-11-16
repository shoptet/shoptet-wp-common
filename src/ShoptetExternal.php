<?php

namespace Shoptet;

class ShoptetExternal {

  static function init() {
    add_filter( 'get_shoptet_footer', [ get_called_class(), 'get_footer' ] );
    add_shortcode( 'shp_cta' , [ get_called_class(), 'get_cta' ] );
  }

  static function get_id() {
    return ( get_theme_mod( 'footer_id_setting' ) ?: 'shoptetcz' );
  }

  static function get_tld() {
    $locale = get_locale(); // cs_CZ, sk_SK, hu_HU
    $tld = 'cz';
    switch( $locale ) {
      case 'sk_SK': $tld = 'sk'; break; 
      case 'hu_HU': $tld = 'hu'; break; 
    }
    return $tld;
  }

  static function get_cta_url() {
    return 'https://www.shoptet.' . urlencode(self::get_tld()) . '/action/ShoptetFooter/render/?id=' . urlencode(self::get_id()) . '&cta=1';
  }

  static function get_footer_url() {
    return 'https://www.shoptet.' . urlencode(self::get_tld()) . '/action/ShoptetFooter/render/?id=' . urlencode(self::get_id());
  }

  static function get_cta() {

    if ( isset($_GET['force_footer']) || false === ( $footer = get_transient( 'shoptet_cta' ) ) ) {
      $cta_url = self::get_cta_url();
      $cta = file_get_contents($cta_url);
      set_transient( 'shoptet_cta', $cta, 24 * HOUR_IN_SECONDS );
    }

    return $cta;
  }

  static function get_footer() {

    if ( isset($_GET['force_footer']) || false === ( $footer = get_transient( 'shoptet_footer' ) ) ) {
      $footer_url = self::get_footer_url();
      $footer = file_get_contents($footer_url);
      set_transient( 'shoptet_footer', $footer, 24 * HOUR_IN_SECONDS );
    }

    return $footer;
  }

}