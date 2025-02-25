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

  // Setup
  $logged_in = is_user_logged_in();
  $user = wp_get_current_user();
  $nonce = wp_create_nonce( 'fictioneer_nonce' );
  $data = array(
    'user_id' => $user->ID,
    'timestamp' => time() * 1000, // Compatible with Date.now() in JavaScript
    'loggedIn' => $logged_in,
    'follows' => false,
    'reminders' => false,
    'checkmarks' => false,
    'bookmarks' => '{}',
    'fingerprint' => fictioneer_get_user_fingerprint( $user->ID ),
    'avatarUrl' => '',
    'isAdmin' => false,
    'isModerator' => false,
    'isAuthor' => false,
    'isEditor' => false,
    'nonce' => $nonce,
    'nonceHtml' => '<input id="fictioneer-ajax-nonce" name="fictioneer-ajax-nonce" type="hidden" value="' . $nonce . '">'
  );

  if ( $logged_in ) {
    $data = array_merge(
      $data,
      array(
        'isAdmin' => fictioneer_is_admin( $user->ID ),
        'isModerator' => fictioneer_is_moderator( $user->ID ),
        'isAuthor' => fictioneer_is_author( $user->ID ),
        'isEditor' => fictioneer_is_editor( $user->ID ),
        'avatarUrl' => get_avatar_url( $user->ID )
      )
    );
  }

  // --- FOLLOWS ---------------------------------------------------------------

  if ( $logged_in && get_option( 'fictioneer_enable_follows' ) ) {
    $follows = fictioneer_load_follows( $user );
    $follows['new'] = false;

    // New notifications?
    if ( count( $follows['data'] ) > 0 ) {
      $latest_count = fictioneer_query_new_followed_chapters_count(
        array_keys( $follows['data'] ),
        wp_date( 'Y-m-d H:i:s', $follows['seen'] / 1000, new DateTimeZone( 'UTC' ) )
      );

      if ( $latest_count > 0 ) {
        $follows['new'] = $latest_count;
      }
    }

    $data['follows'] = $follows;
  }

  // --- REMINDERS -------------------------------------------------------------

  if ( $logged_in && get_option( 'fictioneer_enable_reminders' ) ) {
    $data['reminders'] = fictioneer_load_reminders( $user );
  }

  // --- CHECKMARKS ------------------------------------------------------------

  if ( $logged_in && get_option( 'fictioneer_enable_checkmarks' ) ) {
    $data['checkmarks'] = fictioneer_load_checkmarks( $user );
  }

  // --- BOOKMARKS -------------------------------------------------------------

  if ( $logged_in && get_option( 'fictioneer_enable_bookmarks' ) ) {
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
add_action( 'wp_ajax_nopriv_fictioneer_ajax_get_user_data', 'fictioneer_ajax_get_user_data' );

// =============================================================================
// SELF-DELETE USER - AJAX
// =============================================================================

/**
 * Delete a user's account via AJAX
 *
 * @since 4.5.0
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
        'failure' => __( 'User role too high.', 'fictioneer' ),
        'button' => __( 'Denied', 'fictioneer' )
      )
    );
  }

  // Delete user
  if ( fictioneer_delete_my_account() ) {
    wp_send_json_success();
  } else {
    wp_send_json_error(
      array(
        'failure' => __( 'Database error. Account could not be deleted.', 'fictioneer' ),
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
 */

function fictioneer_ajax_clear_my_comment_subscriptions() {
  // Setup and validations
  $user = fictioneer_get_validated_ajax_user( 'nonce', 'fictioneer_clear_comment_subscriptions' );

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => 'Request did not pass validation.' ) );
  }

  // Change validator
  if ( update_user_meta( $user->ID, 'fictioneer_comment_reply_validator', time() ) ) {
    wp_send_json_success( array( 'success' => __( 'Data has been cleared.', 'fictioneer' ) ) );
  } else {
    wp_send_json_error( array( 'failure' => __( 'Database error. Comment subscriptions could not be cleared.', 'fictioneer' ) ) );
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
 */

function fictioneer_ajax_clear_my_comments() {
  // Setup and validations
  $user = fictioneer_get_validated_ajax_user( 'nonce', 'fictioneer_clear_comments' );

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => 'Request did not pass validation.' ) );
  }

  if (
    fictioneer_is_admin( $user->ID ) ||
    fictioneer_is_author( $user->ID ) ||
    fictioneer_is_moderator( $user->ID )
  ) {
    wp_send_json_error( array( 'failure' => __( 'User role too high. Please ask an administrator.', 'fictioneer' ) ) );
  }

  // Soft-delete comments
  $result = fictioneer_soft_delete_user_comments( $user->ID );

  if ( ! $result ) {
    wp_send_json_error( array( 'error' => 'Database error. No comments found.' ) );
  }

  if ( $result['failure'] && ! $result['complete'] ) {
    wp_send_json_error(
      array(
        'failure' => sprintf(
          __( 'Partial database error. Only %1$s of %2$s comments could be cleared.', 'fictioneer' ),
          $result['updated_count'],
          $result['comment_count']
        )
      )
    );
  }

  if ( $result['failure'] ) {
    wp_send_json_error( array( 'failure' => __( 'Database error. Comments could not be cleared.', 'fictioneer' ) ) );
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
    wp_send_json_error( array( 'error' => 'Request did not pass validation.' ) );
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
    wp_send_json_error( array( 'failure' => __( 'Database error. Please ask an administrator.', 'fictioneer' ) ) );
  }
}
add_action( 'wp_ajax_fictioneer_ajax_unset_my_oauth', 'fictioneer_ajax_unset_my_oauth' );

// =============================================================================
// AJAX: CLEAR COOKIES
// =============================================================================

/**
 * Clear all cookies and log out.
 *
 * @since 5.27.0
 */

function fictioneer_ajax_clear_cookies() {
  // Setup and validations
  $user = fictioneer_get_validated_ajax_user();

  if ( ! $user ) {
    wp_send_json_error(
      array(
        'error' => 'Request did not pass validation.',
        'failure' => __( 'There has been an error. Try again later and if the problem persists, contact an administrator.', 'fictioneer' )
      )
    );
  }

  // Logout
  wp_logout();

  // Clear remaining cookies
  if ( isset( $_SERVER['HTTP_COOKIE'] ) ) {
    $cookies = explode( ';', $_SERVER['HTTP_COOKIE'] );

    foreach ($cookies as $cookie) {
      $parts = explode( '=', $cookie );
      $name = trim( $parts[0] );

      setcookie( $name, '', time() - 3600, '/' );

      unset( $_COOKIE[ $name ] );
    }
  }

  // Response
  wp_send_json_success(
    array(
      'success' => __( 'Cookies and local storage have been cleared. To keep it that way, you should leave the site.', 'fictioneer' )
    )
  );
}
add_action( 'wp_ajax_fictioneer_ajax_clear_cookies', 'fictioneer_ajax_clear_cookies' );

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
