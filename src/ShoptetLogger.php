<?php

namespace Shoptet;

class ShoptetLogger {

  static function capture_exception( \Exception $e ) {
    if ( function_exists( 'wp_sentry_safe' ) ) {
      wp_sentry_safe( function ( \Sentry\State\HubInterface $client ) use ( $e ) {
        $client->captureException( $e );
      } );
    }
  }

}