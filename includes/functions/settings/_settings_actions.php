<?php

// =============================================================================
// EPUB ACTIONS
// =============================================================================

/**
 * Delete all generated ePUBs from the server
 *
 * @since Fictioneer 5.2.5
 */

function fictioneer_purge_all_epubs() {
  // Verify request
  if ( ! check_admin_referer( 'purge_all_epubs', 'fictioneer_nonce' ) ) {
    wp_die( __( 'Nonce verification failed. Please try again.', 'fcnes' ) );
  }

  // Guard
  if ( ! current_user_can( 'administrator' ) || ! is_admin() ) {
    wp_die( __( 'You do not have permission to access this page.', 'fcnes' ) );
  }

  // Setup
  $epub_dir = wp_upload_dir()['basedir'] . '/epubs/';
  $files = list_files( $epub_dir, 1 );

  // Loop over all ePUBs in directory
  foreach ( $files as $file ) {
    $info = pathinfo( $file );

    if ( is_file( $file ) && $info['extension'] === 'epub' ) {
      wp_delete_file( $epub_dir . $info['filename'] . '.epub' );
    }
  }

  // Log
  fictioneer_log(
    __( 'Purged all generated ePUBs from the server.', 'fictioneer' )
  );

  // Redirect
  wp_safe_redirect(
    add_query_arg(
      ['success' => 'fictioneer-purged-epubs'],
      wp_get_referer()
    )
  );

  // Terminate
  exit();
}
add_action( 'admin_post_purge_all_epubs', 'fictioneer_purge_all_epubs' );

// =============================================================================
// SEO ACTIONS
// =============================================================================

/**
 * Purge all SEO schemas
 *
 * This function deletes the "fictioneer_schema" meta data from all posts and pages
 * of specified post types.
 *
 * @since Fictioneer 5.2.5
 */

function fictioneer_purge_all_seo_schemas() {
  // Verify request
  if ( ! check_admin_referer( 'purge_all_seo_schemas', 'fictioneer_nonce' ) ) {
    wp_die( __( 'Nonce verification failed. Please try again.', 'fcnes' ) );
  }

  // Guard
  if ( ! current_user_can( 'administrator' ) || ! is_admin() ) {
    wp_die( __( 'You do not have permission to access this page.', 'fcnes' ) );
  }

  // Setup
  $all_ids = get_posts(
    array(
	    'post_type' => ['page', 'post', 'fcn_story', 'fcn_chapter', 'fcn_recommendation', 'fcn_collection'],
	    'numberposts' => -1,
	    'fields' => 'ids',
	    'update_post_meta_cache' => false,
	    'update_post_term_cache' => false
	  )
  );

  // If IDs were found...
  if ( ! empty( $all_ids ) ) {
    // Loop all IDs to delete schemas
    foreach ( $all_ids as $id ) {
      delete_post_meta( $id, 'fictioneer_schema' );
    }

    // Log
    fictioneer_log(
      __( 'Purged all schema graphs.', 'fictioneer' )
    );

    // Purge caches
    fictioneer_purge_all_caches();
  }

  // Redirect
  wp_safe_redirect(
    add_query_arg(
      ['success' => 'fictioneer-purged-seo-schemas'],
      wp_get_referer()
    )
  );

  // Terminate
  exit();
}
add_action( 'admin_post_purge_all_seo_schemas', 'fictioneer_purge_all_seo_schemas' );

/**
 * Purge all SEO meta caches
 *
 * This function deletes the cached SEO meta values for all posts and custom post types
 * specified in the post_type parameter.
 *
 * @since Fictioneer 5.2.5
 */

function fictioneer_purge_seo_meta_caches() {
  // Verify request
  if ( ! check_admin_referer( 'purge_seo_meta_caches', 'fictioneer_nonce' ) ) {
    wp_die( __( 'Nonce verification failed. Please try again.', 'fcnes' ) );
  }

  // Guard
  if ( ! current_user_can( 'administrator' ) || ! is_admin() ) {
    wp_die( __( 'You do not have permission to access this page.', 'fcnes' ) );
  }

  // Setup
  $all_ids = get_posts(
    array(
	    'post_type' => ['page', 'post', 'fcn_story', 'fcn_chapter', 'fcn_recommendation', 'fcn_collection'],
	    'numberposts' => -1,
	    'fields' => 'ids',
	    'update_post_meta_cache' => false,
	    'update_post_term_cache' => false
	  )
  );

  // If IDs were found...
  if ( $all_ids ) {
    // Loop all IDs to delete SEO meta caches
    foreach ( $all_ids as $id ) {
      delete_post_meta( $id, 'fictioneer_seo_title_cache' );
      delete_post_meta( $id, 'fictioneer_seo_description_cache' );
      delete_post_meta( $id, 'fictioneer_seo_og_image_cache' );
    }

    // Log
    fictioneer_log(
      __( 'Purged all SEO meta caches.', 'fictioneer' )
    );

    // Purge caches
    fictioneer_purge_all_caches();
  }

  // Redirect
  wp_safe_redirect(
    add_query_arg(
      ['success' => 'fictioneer-purged-seo-meta-caches'],
      wp_get_referer()
    )
  );

  // Terminate
  exit();
}
add_action( 'admin_post_purge_seo_meta_caches', 'fictioneer_purge_seo_meta_caches' );

?>
