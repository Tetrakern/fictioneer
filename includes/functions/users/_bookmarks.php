<?php

// =============================================================================
// SAVE BOOKMARKS FOR USERS - AJAX
// =============================================================================

/**
 * Save bookmarks JSON for user via AJAX
 *
 * Note: Bookmarks are not evaluated server-side, only stored as JSON string.
 * Everything else happens client-side.
 *
 * @since 4.0.0
 * @see fictioneer_get_validated_ajax_user()
 */

function fictioneer_ajax_save_bookmarks() {
  // Enabled?
  if ( ! get_option( 'fictioneer_enable_bookmarks' ) ) {
    wp_send_json_error( null, 403 );
  }

  // Setup and validations
  $user = fictioneer_get_validated_ajax_user();

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => 'Request did not pass validation.' ) );
  }

  if ( empty( $_POST['bookmarks'] ) ) {
    wp_send_json_error( array( 'error' => 'Missing arguments.' ) );
  }

  // Valid?
  $bookmarks = sanitize_text_field( $_POST['bookmarks'] );

  if ( $bookmarks && fictioneer_is_valid_json( wp_unslash( $bookmarks ) ) ) {
    // Inspect
    $decoded = json_decode( wp_unslash( $bookmarks ), true );

    if ( ! $decoded || ! isset( $decoded['data'] ) ) {
      wp_send_json_error( array( 'error' => 'Invalid JSON.' ) );
    }

    // Update and response (uses wp_slash/wp_unslash internally)
    $old_bookmarks = get_user_meta( $user->ID, 'fictioneer_bookmarks', true );

    if ( wp_unslash( $bookmarks ) === $old_bookmarks ) {
      wp_send_json_success(); // Nothing to update
    }

    if ( update_user_meta( $user->ID, 'fictioneer_bookmarks', $bookmarks ) ) {
      wp_send_json_success();
    } else {
      wp_send_json_error( array( 'error' => 'Bookmarks could not be updated.' ) );
    }
  }

  // Something went wrong if we end up here...
  wp_send_json_error( array( 'error' => 'An unknown error occurred.' ) );
}

if ( get_option( 'fictioneer_enable_bookmarks' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_save_bookmarks', 'fictioneer_ajax_save_bookmarks' );
}
