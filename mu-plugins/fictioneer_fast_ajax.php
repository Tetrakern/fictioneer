<?php
/**
 * Plugin Name: Fictioneer Fast AJAX
 * Description: Skips plugins and theme initialization for faster requests.
 * Version: 1.0
 * Author: Tetrakern
 * Author URI: https://github.com/Tetrakern
 * Donate link: https://ko-fi.com/tetrakern
 * License: GNU General Public License v3.0 or later
 * License URI: http://www.gnu.org/licenses/gpl.html
 */

// Check if AJAX request
if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
  return;
}

// Check if fast AJAX is activated
if ( ! isset( $_REQUEST['fcn_fast_ajax'] ) ) {
  return;
}

// Check if action is set and allowed
if (
  ! isset( $_REQUEST['action'] ) ||
  strpos( $_REQUEST['action'], 'fictioneer_ajax' ) !== 0
) {
  return;
}

function fictioneer_exclude_plugins( $plugins ) {
  // Setup
  $allow_list = array(
    'fictioneer-email-subscriptions/fictioneer-email-subscriptions.php'
  );

  // Remove not allowed plugins
  foreach ( $plugins as $index => $plugin ) {
    if ( ! in_array( $plugin, $allow_list ) ) {
      unset( $plugins[ $index ] );
    }
  }

  // Continue filter
  return $plugins;
}
add_filter( 'option_active_plugins', 'fictioneer_exclude_plugins' );
