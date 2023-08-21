<?php

// =============================================================================
// GET BOOKMARKS FOR USERS - AJAX
// =============================================================================

/**
 * Get an user's bookmarks via AJAX
 *
 * @since Fictioneer 4.0
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
 * @see fictioneer_get_validated_ajax_user()
 */

function fictioneer_ajax_get_bookmarks() {
  // Enabled?
  if ( ! get_option( 'fictioneer_enable_bookmarks' ) ) {
    wp_send_json_error(
      array( 'error' => __( 'Not allowed.', 'fictioneer' ) ),
      403
    );
  }

  // Setup and validations
  $user = fictioneer_get_validated_ajax_user();

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => __( 'Request did not pass validation.', 'fictioneer' ) ) );
  }

  // Look for saved bookmarks on user...
  $bookmarks = get_user_meta( $user->ID, 'fictioneer_bookmarks', true );
  $bookmarks = $bookmarks ? $bookmarks : '{}';

  // Response
  if ( $bookmarks ) {
    wp_send_json_success( array( 'bookmarks' => $bookmarks ) );
  }
}

if ( get_option( 'fictioneer_enable_bookmarks' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_get_bookmarks', 'fictioneer_ajax_get_bookmarks' );
}

// =============================================================================
// SAVE BOOKMARKS FOR USERS - AJAX
// =============================================================================

/**
 * Save bookmarks JSON for user via AJAX
 *
 * @since Fictioneer 4.0
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
 * @see fictioneer_get_validated_ajax_user()
 */

function fictioneer_ajax_save_bookmarks() {
  // Enabled?
  if ( ! get_option( 'fictioneer_enable_bookmarks' ) ) {
    wp_send_json_error(
      array( 'error' => __( 'Not allowed.', 'fictioneer' ) ),
      403
    );
  }

  // Setup and validations
  $user = fictioneer_get_validated_ajax_user();

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => __( 'Request did not pass validation.', 'fictioneer' ) ) );
  }

  if ( empty( $_POST['bookmarks'] ) ) {
    wp_send_json_error( array( 'error' => __( 'Missing arguments.', 'fictioneer' ) ) );
  }

  // Valid?
  $bookmarks = sanitize_text_field( $_POST['bookmarks'] );

  if ( $bookmarks && fictioneer_is_valid_json( stripslashes( $bookmarks ) ) ) {
    // Inspect
    $decoded = json_decode( stripslashes( $bookmarks ), true );

    if (
      ! $decoded ||
      ! isset( $decoded['data'] ) ||
      ! isset( $decoded['lastLoaded'] ) ||
      count( $decoded ) != 2
    ) {
      wp_send_json_error( array( 'error' => __( 'Invalid JSON.', 'fictioneer' ) ) );
    }

    // Update and response
    if ( update_user_meta( $user->ID, 'fictioneer_bookmarks', $bookmarks ) ) {
      wp_send_json_success();
    } else {
      wp_send_json_error( array( 'error' => __( 'Database error. Bookmarks could not be updated', 'fictioneer' ) ) );
    }
  }

  // Something went wrong if we end up here...
  wp_send_json_error( array( 'error' => __( 'Not a valid JSON string.', 'fictioneer' ) ) );
}

if ( get_option( 'fictioneer_enable_bookmarks' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_save_bookmarks', 'fictioneer_ajax_save_bookmarks' );
}

?>
