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
         wp_doing_ajax() &&
         current_user_can( 'manage_options' ) &&
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
    // Abort if...
    if ( empty( $_POST['name'] ?? '' ) ) {
      wp_send_json_error( array( 'notice' => __( 'Name missing', 'fictioneer' ) ) );
    }

    // Setup
    $file_name = sanitize_text_field( $_POST['name']) . '.epub';
    $path = wp_upload_dir()['basedir'] . '/epubs/' . $file_name;

    // Delete file from directory
		wp_delete_file( $path );

    // Check if deletion was successful
    if ( ! file_exists( $path ) ) {
      // Log
      fictioneer_log(
        sprintf(
          _x(
            'Deleted file from server: %s',
            'Pattern for deleted files in logs: "Deleted file from server: {Filename}".',
            'fictioneer'
          ),
          $file_name
        )
      );

      // Success
      wp_send_json_success(
        array(
          'notice' => sprintf(
            __( '%s deleted.', 'fictioneer' ),
            $file_name
          )
        )
      );
    } else {
      // Error
      wp_send_json_error(
        array(
          'notice' => sprintf(
            __( 'Error. %s could not be deleted.', 'fictioneer' ),
            $file_name
          )
        )
      );
    }
  }

  // Invalid
  wp_send_json_error( array( 'notice' => __( 'Invalid request.', 'fictioneer' ) ) );
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
    if ( empty( $_POST['id'] ?? '' ) ) {
      wp_send_json_error( array( 'notice' => __( 'ID missing', 'fictioneer' ) ) );
    }

    // Setup
		$id = fictioneer_validate_id( $_POST['id'] );

    // Delete schema stored in post meta
		if ( $id && delete_post_meta( $id, 'fictioneer_schema' ) ) {
      // Log
      fictioneer_log(
        sprintf(
          _x(
            'Purged schema graph of #%s',
            'Pattern for purged schema graphs in logs: "Purged schema graph of #{%s}".',
            'fictioneer'
          ),
          $id
        )
      );

      // Purge caches
      fictioneer_refresh_post_caches( $id );

      // Success
      wp_send_json_success(
        array(
          'notice' => sprintf(
            __( 'Schema of post #%s purged.', 'fictioneer' ),
            $id
          )
        )
      );
		} else {
      // Error
      wp_send_json_error(
        array(
          'notice' => sprintf(
            __( 'Error. Schema of post #%s could not be purged.', 'fictioneer' ),
            $id
          )
        )
      );
		}
	}

  // Invalid
  wp_send_json_error( array( 'notice' => __( 'Invalid request.', 'fictioneer' ) ) );
}
add_action( 'wp_ajax_fictioneer_ajax_purge_schema', 'fictioneer_ajax_purge_schema' );

?>
