<?php

// =============================================================================
// GET USER DATA - AJAX
// =============================================================================

/**
 * Get relevant user data via AJAX
 *
 * @since 5.7.0
 */

function fictioneer_ajax_get_user_data() {
  // Rate limit
  fictioneer_check_rate_limit( 'fictioneer_ajax_get_user_data', 30 );

  // Validations
  $user = fictioneer_get_validated_ajax_user();

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => __( 'Request did not pass validation.', 'fictioneer' ) ) );
  }

  // Setup
  $data = array(
    'user_id' => $user->ID,
    'timestamp' => time() * 1000, // Compatible with Date.now() in JavaScript
    'loggedIn' => true,
    'follows' => false,
    'reminders' => false,
    'checkmarks' => false,
    'bookmarks' => '{}',
    'fingerprint' => fictioneer_get_user_fingerprint( $user->ID )
  );

  // --- FOLLOWS ---------------------------------------------------------------

  if ( get_option( 'fictioneer_enable_follows' ) ) {
    $follows = fictioneer_load_follows( $user );
    $follows['new'] = false;
    $latest = 0;

    // New notifications?
    if ( count( $follows['data'] ) > 0 ) {
      $latest = fictioneer_query_followed_chapters(
        array_keys( $follows['data'] ),
        wp_date( 'c', $follows['seen'] / 1000 )
      );

      if ( $latest ) {
        $follows['new'] = count( $latest );
      }
    }

    $data['follows'] = $follows;
  }

  // --- REMINDERS -------------------------------------------------------------

  if ( get_option( 'fictioneer_enable_reminders' ) ) {
    $data['reminders'] = fictioneer_load_reminders( $user );
  }

  // --- CHECKMARKS ------------------------------------------------------------

  if ( get_option( 'fictioneer_enable_checkmarks' ) ) {
    $data['checkmarks'] = fictioneer_load_checkmarks( $user );
  }

  // --- BOOKMARKS -------------------------------------------------------------

  if ( get_option( 'fictioneer_enable_bookmarks' ) ) {
    $bookmarks = get_user_meta( $user->ID, 'fictioneer_bookmarks', true );
    $data['bookmarks'] = $bookmarks ? $bookmarks : '{}';
  }

  // --- FILTER ----------------------------------------------------------------

  $data = apply_filters( 'fictioneer_filter_ajax_get_user_data', $data, $user );

  // ---------------------------------------------------------------------------

  // Response
  wp_send_json_success( $data );
}
add_action( 'wp_ajax_fictioneer_ajax_get_user_data', 'fictioneer_ajax_get_user_data' );

// =============================================================================
// SELF-DELETE USER - AJAX
// =============================================================================

/**
 * Delete an user's account via AJAX
 *
 * @since 4.5.0
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
 * @see fictioneer_validate_id()
 * @see fictioneer_get_validated_ajax_user()
 */

function fictioneer_ajax_delete_my_account() {
  // Setup
  $sender_id = isset( $_POST['id'] ) ? fictioneer_validate_id( $_POST['id'] ) : false;
  $current_user = fictioneer_get_validated_ajax_user( 'nonce', 'fictioneer_delete_account' );

  // Extra validations
  if (
    ! $sender_id ||
    ! $current_user ||
    $sender_id !== $current_user->ID ||
    $current_user->ID === 1 ||
    in_array( 'administrator', $current_user->roles ) ||
    ! current_user_can( 'fcn_allow_self_delete' )
  ) {
    wp_send_json_error(
      array(
        'error' => __( 'User role too high.', 'fictioneer' ),
        'button' => __( 'Denied', 'fictioneer' )
      )
    );
    die(); // Just to be sure
  }

  // Delete user
  if ( fictioneer_delete_my_account() ) {
    wp_send_json_success();
  } else {
    wp_send_json_error(
      array(
        'error' => __( 'Database error. Account could not be deleted.', 'fictioneer' ),
        'button' => __( 'Failure', 'fictioneer' )
      )
    );
  }
}

if ( current_user_can( 'fcn_allow_self_delete' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_delete_my_account', 'fictioneer_ajax_delete_my_account' );
}

// =============================================================================
// SELF-CLEAR USER COMMENT SUBSCRIPTIONS - AJAX
// =============================================================================

/**
 * Clears all the user's comment subscriptions via AJAX
 *
 * Changes the comment notification validation timestamp, effectively terminating
 * all associated previous comment subscriptions. This is far cheaper than looping
 * and updating all comments.
 *
 * @since 5.0.0
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
 * @see fictioneer_get_validated_ajax_user()
 */

function fictioneer_ajax_clear_my_comment_subscriptions() {
  // Setup and validations
  $user = fictioneer_get_validated_ajax_user( 'nonce', 'fictioneer_clear_comment_subscriptions' );

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => __( 'Request did not pass validation.', 'fictioneer' ) ) );
  }

  // Change validator
  if ( update_user_meta( $user->ID, 'fictioneer_comment_reply_validator', time() ) ) {
    wp_send_json_success( array( 'success' => __( 'Data has been cleared.', 'fictioneer' ) ) );
  } else {
    wp_send_json_error( array( 'error' => __( 'Database error. Comment subscriptions could not be cleared.', 'fictioneer' ) ) );
  }
}
add_action( 'wp_ajax_fictioneer_ajax_clear_my_comment_subscriptions', 'fictioneer_ajax_clear_my_comment_subscriptions' );

// =============================================================================
// SELF-CLEAR USER COMMENTS - AJAX
// =============================================================================

/**
 * Clears all the user's comments via AJAX
 *
 * Queries all comments of the user and overrides the comment data with
 * garbage, preserving the comment thread integrity.
 *
 * @since 5.0.0
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
 * @see fictioneer_get_validated_ajax_user()
 */

function fictioneer_ajax_clear_my_comments() {
  // Setup and validations
  $user = fictioneer_get_validated_ajax_user( 'nonce', 'fictioneer_clear_comments' );

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => __( 'Request did not pass validation.', 'fictioneer' ) ) );
  }

  if (
    fictioneer_is_admin( $user->ID ) ||
    fictioneer_is_author( $user->ID ) ||
    fictioneer_is_moderator( $user->ID )
  ) {
    wp_send_json_error( array( 'error' => __( 'User role too high. Please ask an administrator.', 'fictioneer' ) ) );
  }

  // Soft-delete comments
  $result = fictioneer_soft_delete_user_comments( $user->ID );

  if ( ! $result ) {
    wp_send_json_error( array( 'error' => __( 'Database error. No comments found.', 'fictioneer' ) ) );
  }

  if ( $result['failure'] && ! $result['complete'] ) {
    wp_send_json_error(
      array(
        'error' => sprintf(
          __( 'Partial database error. Only %1$s of %2$s comments could be cleared.', 'fictioneer' ),
          $result['updated_count'],
          $result['comment_count']
        )
      )
    );
  }

  if ( $result['failure'] ) {
    wp_send_json_error( array( 'error' => __( 'Database error. Comments could not be cleared.', 'fictioneer' ) ) );
  }

  // Report success
  wp_send_json_success(
    array(
      'success' => sprintf(
        __( '%s comments have been cleared.', 'fictioneer' ),
        $result['updated_count']
      )
    )
  );
}
add_action( 'wp_ajax_fictioneer_ajax_clear_my_comments', 'fictioneer_ajax_clear_my_comments' );

// =============================================================================
// SELF-UNSET OAUTH CONNECTIONS - AJAX
// =============================================================================

/**
 * Unset one of the user's OAuth bindings via AJAX
 *
 * @since 4.0.0
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
 */

function fictioneer_ajax_unset_my_oauth() {
  // Setup
  $sender_id = fictioneer_validate_id( $_POST['id'] ?? 0 ) ?: false;
  $channel = sanitize_text_field( $_POST['channel'] ?? 0 ) ?: false;
  $user = fictioneer_get_validated_ajax_user( 'nonce', 'fictioneer_unset_oauth' );

  // Validations
  if (
    ! $user ||
    ! $sender_id ||
    ! $channel ||
    ! in_array( $channel, ['discord', 'twitch', 'patreon', 'google'] ) ||
    $sender_id !== $user->ID
  ) {
    wp_send_json_error( array( 'error' => __( 'Request did not pass validation.', 'fictioneer' ) ) );
  }

  // Delete connection
  if ( delete_user_meta( $user->ID, "fictioneer_{$channel}_id_hash" ) ) {
    // Remove tiers
    if ( $channel === 'patreon' ) {
      delete_user_meta( $user->ID, 'fictioneer_patreon_tiers' );
    }

    // Success response
    wp_send_json_success(
      array(
        'channel' => $channel,
        'label' => sprintf(
          __( '%s disconnected', 'fictioneer' ),
          ucfirst( $channel )
        )
      )
    );
  } else {
    // Failure response
    wp_send_json_error( array( 'error' => __( 'Database error. Please ask an administrator.', 'fictioneer' ) ) );
  }
}
add_action( 'wp_ajax_fictioneer_ajax_unset_my_oauth', 'fictioneer_ajax_unset_my_oauth' );

// =============================================================================
// GET USER AVATAR URL - AJAX
// =============================================================================

/**
 * Get user avatar URL via AJAX
 *
 * @since 4.0.0
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
 * @see fictioneer_get_validated_ajax_user()
 */

function fictioneer_ajax_get_avatar() {
  // Setup and validations
  $user = fictioneer_get_validated_ajax_user();

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => __( 'Request did not pass validation.', 'fictioneer' ) ) );
  }

  // Response
  wp_send_json_success( array( 'url' => get_avatar_url( $user->ID ) ) );
}
add_action( 'wp_ajax_fictioneer_ajax_get_avatar', 'fictioneer_ajax_get_avatar' );
