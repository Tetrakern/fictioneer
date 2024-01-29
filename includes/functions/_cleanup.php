<?php

// =============================================================================
// LEGACY CLEANUP
// =============================================================================

/**
 * Clean up legacy fields, files, and settings
 *
 * This only runs on hardcoded cutoff dates, once. It will only run again if the
 * date in this function is changed on an update.
 *
 * @since 5.8.7
 */

function fictioneer_legacy_cleanup() {
  global $wpdb;

  $cutoff = 202401292127; // YYYYMMDDHHMM
  $last_cleanup = absint( get_option( 'fictioneer_last_cleanup' ) );

  // Sitemap cleanup
  if ( $last_cleanup < 202401130000 ) {
    $old_sitemap = ABSPATH . '/sitemap.xml';

    if ( file_exists( $old_sitemap ) ) {
      unlink( $old_sitemap );
    }

    flush_rewrite_rules();
  }

  // Purge theme caches after update
  if ( $last_cleanup < 202419100000 ) {
    fictioneer_delete_transients_like( 'fictioneer_' );
  }

  // Migrate SEO fields
  if ( $last_cleanup < 202401292127 ) {
    // Relevant meta keys
    $meta_keys = [
      'fictioneer_seo_title',
      'fictioneer_seo_description',
      'fictioneer_seo_og_image',
      'fictioneer_seo_title_cache',
      'fictioneer_seo_description_cache',
      'fictioneer_seo_og_image_cache'
    ];

    // Look for old SEO data
    $placeholders = implode( ', ', array_fill( 0, count( $meta_keys ), '%s' ) );

    $sql = "
      SELECT COUNT(*) FROM {$wpdb->postmeta}
      WHERE meta_key IN ($placeholders)
    ";

    $meta_exists = $wpdb->get_var( $wpdb->prepare( $sql, $meta_keys ) );

    // If old SEO data exists...
    if ( $meta_exists > 0 ) {
      // Query all posts
      $args = array(
        'post_type' => 'any',
        'posts_per_page' => -1,
        'update_post_meta_cache' => true,
        'update_post_term_cache' => false,
        'no_found_rows' => true
      );

      $posts = get_posts( $args );

      // Process posts...
      foreach ( $posts as $post ) {
        // Get old SEO data
        $title = get_post_meta( $post->ID, 'fictioneer_seo_title', true );
        $description = get_post_meta( $post->ID, 'fictioneer_seo_description', true );
        $og_image = get_post_meta( $post->ID, 'fictioneer_seo_og_image', true );

        // Continue if nothing found
        if ( ! $title && ! $description && ! $og_image ) {
          continue;
        }

        // Build new field array
        $seo_fields = array(
          'title' => $title,
          'description' => $description,
          'og_image_id' => $og_image
        );

        // Update new meta fields
        update_post_meta( $post->ID, 'fictioneer_seo_fields', $seo_fields );
      }

      // Delete old meta fields
      foreach ( $meta_keys as $key ) {
        $wpdb->delete( $wpdb->postmeta, array( 'meta_key' => $key ) );
      }
    }
  }

  // Remember cleanup
  update_option( 'fictioneer_last_cleanup', $cutoff );
}
add_action( 'init', 'fictioneer_legacy_cleanup', 99 );

// =============================================================================
// THEME DEACTIVATION
// =============================================================================

/**
 * Clean up when the theme is deactivated
 *
 * Always deletes all theme-related Transients in the database. If the option to
 * delete all settings and theme mods is activated, these will be removed as
 * well but otherwise preserved.
 *
 * @since 4.7.0
 * @since 5.7.4 - Updated to use SQL queries.
 *
 * @global wpdb $wpdb  WordPress database object.
 */

function fictioneer_theme_deactivation() {
  global $wpdb;

  // Delete all theme Transients
  fictioneer_delete_transients_like( 'fictioneer_' );

  // Only continue if the user wants to delete all options/mods
  if ( get_option( 'fictioneer_delete_theme_options_on_deactivation', false ) ) {
    // Remove theme mods
    remove_theme_mods();

    // SQL to delete all options starting with "fictioneer_"
    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'fictioneer_%'" );

    // SQL to delete all user meta starting with "fictioneer_"
    $wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'fictioneer_%'" );

    // SQL to delete all comment meta starting with "fictioneer_"
    $wpdb->query("DELETE FROM {$wpdb->commentmeta} WHERE meta_key LIKE 'fictioneer_%'");

    // Reset user roles
    remove_role( 'fcn_moderator' );
  }
}
add_action( 'switch_theme', 'fictioneer_theme_deactivation' );

?>
