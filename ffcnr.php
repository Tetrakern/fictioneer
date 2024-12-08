<?php
/**
 * Fast Request Entry Point
 *
 * This file set up a minimal WordPress environment that is many times
 * faster than regular endpoints but does not load anything beyond the
 * absolute basics. No theme functions or plugins will work by default.
 * Only use this for frequent and performance-critical requests.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.xx.x
 */

define( 'SHORTINIT', true );
define( 'FFCNR', true );

header( 'X-Robots-Tag: noindex, nofollow', true );
header( 'X-Content-Type-Options: nosniff' );
header( 'X-Frame-Options: DENY' );

header( 'Referrer-Policy: no-referrer' );
header( "Content-Security-Policy: default-src 'none'; script-src 'none'; style-src 'none'; img-src 'none'; object-src 'none'; frame-ancestors 'none'; base-uri 'none'; form-action 'none';" ); // Just because

if ( ! ( $_REQUEST['action'] ?? 0 ) ) {
  header( 'HTTP/1.1 204 No Content' );
  exit;
}

if ( isset( $_SERVER['DOCUMENT_ROOT'] ) && file_exists( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' ) ) {
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
} else {
  $load_path = dirname( __DIR__, 3 ) . '/wp-load.php';

  if ( file_exists( $load_path ) ) {
    require_once $load_path;
  } else {
    header( 'HTTP/1.1 500 Internal Server Error' );
    echo 'Critical error: Unable to locate wp-load.php.';
    exit;
  }
}

require_once __DIR__ . '/includes/functions/requests/_setup.php';

header( 'HTTP/1.1 400 Bad Request' );
exit;
