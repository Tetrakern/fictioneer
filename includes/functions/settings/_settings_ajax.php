<?php

// =============================================================================
// VALIDATION
// =============================================================================

/**
 * Validate settings AJAX request
 *
 * Checks whether the request comes from the admin panel, the nonce, and the
 * current user’s capabilities.
 *
 * @since Fictioneer 4.0
 *
 * @return boolean True if valid, false if not.
 */

function fictioneer_validate_settings_ajax() {
	return is_admin() &&
         current_user_can( 'administrator' ) &&
         ! empty( $_POST['nonce'] ) &&
         check_admin_referer( 'fictioneer_settings_actions', 'nonce' );
}

// =============================================================================
// DELETE EPUB AJAX
// =============================================================================

/**
 * AJAX: Delete an ePUB file from the server
 *
 * @since Fictioneer 4.0
 */

function fictioneer_ajax_delete_epub() {
  if ( fictioneer_validate_settings_ajax() ) {
    // Setup
    $name = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : null;
    $epub_dir = wp_upload_dir()['basedir'] . '/epubs/';

    // Abort if...
    if ( ! $name ) wp_send_json_error();

    // Delete file from directory
		wp_delete_file( $epub_dir . $name . '.epub' );

    // Check if deletion was successful
    if ( ! file_exists( $epub_dir . $name . '.epub' ) ) {
      // Update log
      $update = sprintf(
        __( 'Deleted file from server: %s', 'fictioneer' ),
        $name . '.epub'
      );
      fictioneer_update_log( $update );

      // Success response
      wp_send_json_success();
    } else {
      // Error response
      wp_send_json_error();
    }
  }

  // Invalid
  wp_send_json_error();
}
add_action( 'wp_ajax_fictioneer_ajax_delete_epub', 'fictioneer_ajax_delete_epub' );

// =============================================================================
// PURGE SCHEMA AJAX
// =============================================================================

/**
 * AJAX: Purge schema for a post/page
 *
 * @since Fictioneer 4.0
 */

function fictioneer_ajax_purge_schema() {
	if ( fictioneer_validate_settings_ajax() ) {
    // Abort if...
		if ( ! isset( $_POST['id'] ) ) wp_send_json_error();

    // Setup
		$id = fictioneer_validate_id( $_POST['id'] );

    // Delete schema stored in post meta
		if ( $id && delete_post_meta( $id, 'fictioneer_schema' ) ) {
      // Update log
      $update = sprintf(
        __( 'Purged schema graph of #%s', 'fictioneer' ),
        $id
      );
      fictioneer_update_log( $update );

      // Purge caches
      fictioneer_refresh_post_caches( $id );

      // Success response
			wp_send_json_success();
		} else {
      // Error response
			wp_send_json_error();
		}
	}

  // Invalid
  wp_send_json_error();
}
add_action( 'wp_ajax_fictioneer_ajax_purge_schema', 'fictioneer_ajax_purge_schema' );

?>
