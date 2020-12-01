<?php

namespace Shoptet;

class ShoptetHelpers {

  static function get_shoptet_tld() {
    $locale = get_locale(); // cs_CZ, sk_SK, hu_HU
    $tld = 'cz';
    switch( $locale ) {
      case 'sk_SK': $tld = 'sk'; break; 
      case 'hu_HU': $tld = 'hu'; break; 
    }
    return $tld;
  }

  static function get_shoptet_url( $path = '' ) {
    $url_format = 'https://www.shoptet.%s/%s';
    $tld = self::get_shoptet_tld();
    return sprintf( $url_format, $tld, ltrim( $path, '/' ) );
  }

  static function load_template_mo_file( $domain = 'shoptet' ) {
    $locale = apply_filters( 'theme_locale', determine_locale(), $domain );
    $mofile = $domain . '-' . $locale . '.mo';
    $path = get_template_directory() . '/languages/' . $mofile;
    load_textdomain( $domain, $path );
  }

}