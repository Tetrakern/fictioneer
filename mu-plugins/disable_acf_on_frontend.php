<?php
/**
 * Plugin Name: Disable ACF on Frontend
 * Description: Provides a performance boost if ACF frontend functions aren't being used
 * Version:     1.0
 * Author:      Bill Erickson
 * Author URI:  http://www.billerickson.net
 * License:     MIT
 * License URI: http://www.opensource.org/licenses/mit-license.php
 */

/**
 * Disable ACF on Frontend
 *
 */

function ea_disable_acf_on_frontend( $plugins ) {
  // Abort if in admin panel...
	if ( is_admin() ) return $plugins;

  // Remove ACF on frontend
	foreach ( $plugins as $i => $plugin ) {
    if ( 'advanced-custom-fields-pro/acf.php' == $plugin || 'advanced-custom-fields/acf.php' == $plugin ) {
      unset( $plugins[ $i ] );
    }
  }

  // Modified plugin array
	return $plugins;
}
add_filter( 'option_active_plugins', 'ea_disable_acf_on_frontend' );
