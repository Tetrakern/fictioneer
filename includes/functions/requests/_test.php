<?php

// No direct access!
defined( 'ABSPATH' ) OR exit;

// Try to get current user
$user = ffcnr_get_current_user();

// Report
header( 'Content-Type: text/html; charset=utf-8' );
header( 'HTTP/1.1 200 OK' );

if ( $user === 0 ) {
  echo 'FFCNR authentication is not working for unknown reasons, turn it off or try to fix it.';
} else {
  echo 'FFCNR authentication is working, all good.';
}

exit;
