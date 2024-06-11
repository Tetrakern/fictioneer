<?php
/**
 * Plugin Name: Fictioneer Elementor Control
 * Description: Disables Elementor on all pages except for the Canvas templates.
 * Version: 1.0.0
 * Author: Tetrakern
 * Author URI: https://github.com/Tetrakern
 * License: GNU General Public License v3.0 or later
 * License URI: http://www.gnu.org/licenses/gpl.html
 */


/**
 * Get current post ID based on URL
 *
 * @since 1.0.0
 */

function fictioneer_mu_002_get_post_id() {
  global $wpdb;

  // Setup
  $current_url_path = $_SERVER['REQUEST_URI'];

  // Check for frontpage...
  if ( trim( $current_url_path, '/' ) === '' ) {
    return $wpdb->get_var( "SELECT option_value FROM $wpdb->options WHERE option_name = 'page_on_front'" );
  }

  // Look for ID...
  if ( preg_match( '/\/(\d+)/', $current_url_path, $matches ) ) {
    // Numeric ID in URL (lucky)
    return intval( $matches[1] );
  } else {
    // Look for page name
    $path_parts = explode( '/', trim( $current_url_path, '/' ) );
    $post_slug = end( $path_parts );

    return $wpdb->get_var(
      $wpdb->prepare(
        "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_status = 'publish'",
       sanitize_title_for_query( $post_slug )
      )
    );
  }
}

/**
 * Add filter to remove Elementor if not on a Canvas page template
 *
 * @since 1.0.0
 */

function fictioneer_elementor_control() {
  // Abort if...
  if (
    is_admin() ||
    wp_doing_ajax() ||
    strpos( $_SERVER['REQUEST_URI'], 'elementor' ) !== false
  ) {
    return;
  }

  // Get the current page template
  $template = get_page_template_slug( fictioneer_mu_002_get_post_id() );
  $allowed_templates = array(
    'singular-canvas-site.php',
    'singular-canvas-page.php',
    'singular-canvas-main.php'
  );

  // Canvas template?
  if ( empty( $template ) || ! in_array( $template, $allowed_templates ) ) {
    add_filter( 'option_active_plugins', 'fictioneer_exclude_elementor' );
  }
}
add_action( 'muplugins_loaded', 'fictioneer_elementor_control' );

/**
 * Removed Elementor from list of active plugins
 *
 * @since 1.0.0
 *
 * @param array $plugins  An array of active plugin paths.
 *
 * @return array Array of active plugins with Elementor removed.
 */

function fictioneer_exclude_elementor( $plugins ) {
  $elementor_key = array_search( 'elementor/elementor.php', $plugins );

  if ( $elementor_key !== false ) {
    unset( $plugins[ $elementor_key ] );
  }

  return $plugins;
}
