<?php

// =============================================================================
// EPUB ACTIONS
// =============================================================================

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

?>
