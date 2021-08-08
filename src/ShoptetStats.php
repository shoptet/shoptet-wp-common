<?php

namespace Shoptet;

class ShoptetStats {

  static function init() {
    add_shortcode( 'shoptet-stats', [ get_called_class(), 'stats_shortcode' ] );
    add_shortcode( 'projectCount', [ get_called_class(), 'project_count_shortcode' ] );
  }

  static function get_stats() {
    if ( false === ( $stats = get_transient( 'shoptet_stats' ) ) ) {
      $stats_url = ShoptetHelpers::get_shoptet_url( '/action/ShoptetStatisticCounts/' );

      // Initialize CURL
      $ch = curl_init($stats_url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      // Store the data
      $json = curl_exec($ch);
      curl_close($ch);

      // Decode JSON response
      $stats = json_decode($json, true);

      if ( ! is_array( $stats ) ) {
        $stats = [];
      }
      
      set_transient( 'shoptet_stats', $stats, 1 * HOUR_IN_SECONDS );
    }

    return $stats;
  }

  static function get_stats_by_name( $name ) {
    $stats = self::get_stats();

    if ( ! isset( $stats[$name] ) ) {
      return '';
    }

    return $stats[$name];
  }

  static function stats_shortcode( $atts ) {

    if ( empty( $atts['name'] ) ) {
      return '';
    }

    return self::get_stats_by_name( $atts['name'] );
  }

  static function project_count_shortcode() {
    $project_count = self::get_stats_by_name( 'projectsCount' );
    return number_format_i18n( $project_count );
  }

}