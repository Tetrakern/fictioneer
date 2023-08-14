<?php

// =============================================================================
// AFTER LOGOUT CLEANUP
// =============================================================================

/**
 * Make sure local storage is cleared on logout
 *
 * @since Fictioneer 5.0
 */

function fictioneer_after_logout_cleanup() {
  // Start HTML ---> ?>
  <script>
    localStorage.removeItem('fcnProfileAvatar');
    localStorage.removeItem('fcnStoryFollows')
    localStorage.removeItem('fcnStoryReminders');
    localStorage.removeItem('fcnCheckmarks');
    localStorage.removeItem('fcnLoginState');
    localStorage.removeItem('fcnNonce');
    localStorage.removeItem('fcnFingerprint');
    localStorage.removeItem('fcnBookshelfContent');

    // Deliberate logout means clear bookmarks!
    localStorage.removeItem('fcnChapterBookmarks');
  </script>
  <?php // <--- End HTML
}
add_action( 'login_form', 'fictioneer_after_logout_cleanup' );

// =============================================================================
// ADD BODY CLASSES
// =============================================================================

/**
 * Add additional classes to the <body>
 *
 * @since Fictioneer 5.0
 *
 * @param array $classes Current body classes.
 */

function fictioneer_addClassesToBody( $classes ) {
  // Setup
  $user = wp_get_current_user();
  $includes = [];

  // Roles
  if ( ! get_option( 'fictioneer_enable_public_cache_compatibility' ) && ! is_admin() ) {
    $includes['is-admin'] = fictioneer_is_admin( $user->ID );
    $includes['is-moderator'] = fictioneer_is_moderator( $user->ID );
    $includes['is-author'] = fictioneer_is_author( $user->ID );
    $includes['is-editor'] = fictioneer_is_editor( $user->ID );
  }

  // Browsers
  if ( ! fictioneer_caching_active() && ! is_admin() ) {
    $includes['is-iphone'] = $GLOBALS['is_iphone'];
    $includes['is-chrome'] = $GLOBALS['is_chrome'];
    $includes['is-safari'] = $GLOBALS['is_safari'];
    $includes['is-opera'] = $GLOBALS['is_opera'];
    $includes['is-gecko'] = $GLOBALS['is_gecko'];
    $includes['is-lynx'] = $GLOBALS['is_lynx'];
    $includes['is-ie'] = $GLOBALS['is_IE'];
    $includes['is-edge'] = $GLOBALS['is_edge'];
  }

  // Add classes to defaults
  foreach ( $includes as $class => $test )  {
		if ( $test ) $classes[ $class ] = $class;
	}

  // Return classes
  return $classes;
}
add_filter( 'body_class', 'fictioneer_addClassesToBody' );

function fictioneer_addClassesToAdminBody( $classes ) {
  // Setup
  $user = wp_get_current_user();
  $includes = [];

  // Classes
  $includes['is-admin'] = fictioneer_is_admin( $user->ID );
  $includes['is-moderator'] = fictioneer_is_moderator( $user->ID );
  $includes['is-author'] = fictioneer_is_author( $user->ID );
  $includes['is-editor'] = fictioneer_is_editor( $user->ID );

  // Add classes to defaults
  if ( array_intersect( ['subscriber'], $user->roles ) ) {
    $classes .= ' is-subscriber';
  } else {
    foreach ( $includes as $class => $test )  {
      if ( $test ) $classes .= " $class";
    }
  }

  // Return classes
  return $classes;
}
add_filter( 'admin_body_class', 'fictioneer_addClassesToAdminBody' );

// =============================================================================
// UPDATE ADMIN USER PROFILE (ADMIN)
// =============================================================================

/**
 * Update restricted sections of the wp-admin user profile
 *
 * Moderators can update some of these fields, administrators can update all.
 *
 * @since Fictioneer 4.3
 *
 * @param int $updated_user_id The ID of the updated user.
 */

function fictioneer_update_admin_user_profile( $updated_user_id ) {
  // Setup
  $sender_is_moderator = fictioneer_is_moderator( get_current_user_id() );
  $sender_is_admin = fictioneer_is_admin( get_current_user_id() );
  $sender_is_owner = get_current_user_id() === $updated_user_id;

  // Administrators can only be edited by themselves
  $admin_protection = fictioneer_is_admin( $updated_user_id ) && ! $sender_is_owner;

  // Moderation flags...
  if ( ( $sender_is_moderator || $sender_is_admin ) && ! $admin_protection  ) {
    // Disable avatar checkbox
    update_user_meta(
      $updated_user_id,
      'fictioneer_admin_disable_avatar',
      isset( $_POST['fictioneer_admin_disable_avatar'] )
    );

    // Disable reporting capability checkbox
    update_user_meta(
      $updated_user_id,
      'fictioneer_admin_disable_reporting',
      isset( $_POST['fictioneer_admin_disable_reporting'] )
    );

    // Disable renaming capability checkbox
    update_user_meta(
      $updated_user_id,
      'fictioneer_admin_disable_renaming',
      isset( $_POST['fictioneer_admin_disable_renaming'] )
    );

    // Disable commenting capability checkbox
    update_user_meta(
      $updated_user_id,
      'fictioneer_admin_disable_commenting',
      isset( $_POST['fictioneer_admin_disable_commenting'] )
    );

    // Disable comment editing capability checkbox
    update_user_meta(
      $updated_user_id,
      'fictioneer_admin_disable_editing',
      isset( $_POST['fictioneer_admin_disable_editing'] )
    );

    // Disable comment notification capability checkbox
    update_user_meta(
      $updated_user_id,
      'fictioneer_admin_disable_comment_notifications',
      isset( $_POST['fictioneer_admin_disable_comment_notifications'] )
    );

    // Always hold comments for moderation
    update_user_meta(
      $updated_user_id,
      'fictioneer_admin_always_moderate_comments',
      isset( $_POST['fictioneer_admin_always_moderate_comments'] )
    );

    // Custom moderation message shown in profile
    update_user_meta(
      $updated_user_id,
      'fictioneer_admin_moderation_message',
      sanitize_text_field( $_POST['fictioneer_admin_moderation_message'] )
    );
  }

  // Account fields...
  if ( $sender_is_admin && ! $admin_protection ) {
    // Badge override string
    update_user_meta( $updated_user_id, 'fictioneer_badge_override', sanitize_text_field( $_POST['fictioneer_badge_override'] ) );

    // External avatar URL
    update_user_meta( $updated_user_id, 'fictioneer_external_avatar_url', sanitize_url( $_POST['fictioneer_external_avatar_url'] ) );

    if ( FICTIONEER_SHOW_OAUTH_HASHES ) {
      // Discord OAuth ID (sensible data)
      update_user_meta( $updated_user_id, 'fictioneer_discord_id_hash', sanitize_text_field( $_POST['fictioneer_discord_id_hash'] ) );

      // Google OAuth ID (sensible data)
      update_user_meta( $updated_user_id, 'fictioneer_google_id_hash', sanitize_text_field( $_POST['fictioneer_google_id_hash'] ) );

      // Twitch OAuth ID (sensible data)
      update_user_meta( $updated_user_id, 'fictioneer_twitch_id_hash', sanitize_text_field( $_POST['fictioneer_twitch_id_hash'] ) );

      // Patreon OAuth ID (sensible data)
      update_user_meta( $updated_user_id, 'fictioneer_patreon_id_hash', sanitize_text_field( $_POST['fictioneer_patreon_id_hash'] ) );
    }
  }
}
add_action( 'personal_options_update', 'fictioneer_update_admin_user_profile' );
add_action( 'edit_user_profile_update', 'fictioneer_update_admin_user_profile' );

// =============================================================================
// UPDATE MY USER PROFILE (ADMIN PANEL)
// =============================================================================

/**
 * Update user sections of the wp-admin user profile
 *
 * Only the respective user or an administrator can update these fields.
 *
 * @since Fictioneer 4.3
 *
 * @param int $updated_user_id The ID of the updated user.
 */

function fictioneer_update_my_user_profile( $updated_user_id ) {
  // Setup
  $sender_id = get_current_user_id();
  $sender_is_admin = fictioneer_is_admin( $sender_id );
  $sender_is_owner = $sender_id === $updated_user_id;

  // Make sure the sender is the profile owner unless it's an administrator
  if ( ! $sender_is_owner && ! $sender_is_admin ) return false;

  // Profile flags...
  if ( $sender_is_owner || $sender_is_admin ) {
    // Enforce gravatar checkbox
    update_user_meta( $updated_user_id, 'fictioneer_enforce_gravatar', isset( $_POST['fictioneer_enforce_gravatar'] ) );

    // Disable avatar checkbox
    update_user_meta( $updated_user_id, 'fictioneer_disable_avatar', isset( $_POST['fictioneer_disable_avatar'] ) );

    // Hide badge checkbox
    update_user_meta( $updated_user_id, 'fictioneer_hide_badge', isset( $_POST['fictioneer_hide_badge'] ) );

    // Disable badge override checkbox
    update_user_meta( $updated_user_id, 'fictioneer_disable_badge_override', isset( $_POST['fictioneer_disable_badge_override'] ) );

    // Always subscribe to reply email notifications
    update_user_meta( $updated_user_id, 'fictioneer_comment_reply_notifications', isset( $_POST['fictioneer_comment_reply_notifications'] ) );
  }

  // Author fields (if author)...
  if ( fictioneer_is_author( $updated_user_id ) || $sender_is_admin ) {
    // Selected author page (if any)
    if ( isset( $_POST['fictioneer_author_page'] ) ) {
      // Get ID and make sure it belongs to a page
      $author_page_id = intval( $_POST['fictioneer_author_page'] );
      $author_page_id = get_post_type( $author_page_id ) == 'page' ? $author_page_id : -1;

      // Make sure the page belongs to the author
      if ( $author_page_id > 0 ) {
        $author_page = get_post( $author_page_id );
        $author_page_id = $author_page && intval( $author_page->post_author ) == $updated_user_id ? $author_page_id : -1;
      }

      // Update
      update_user_meta( $updated_user_id, 'fictioneer_author_page', $author_page_id );
    }

    // Patreon link (if any)
    if ( isset( $_POST['fictioneer_user_patreon_link'] ) ) {
      update_user_meta(
        $updated_user_id,
        'fictioneer_user_patreon_link',
        sanitize_url( $_POST['fictioneer_user_patreon_link'] )
      );
    }

    // Ko-Fi link (if any)
    if ( isset( $_POST['fictioneer_user_kofi_link'] ) ) {
      update_user_meta(
        $updated_user_id,
        'fictioneer_user_kofi_link',
        sanitize_url( $_POST['fictioneer_user_kofi_link'] )
      );
    }

    // SubscribeStar link (if any)
    if ( isset( $_POST['fictioneer_user_subscribestar_link'] ) ) {
      update_user_meta(
        $updated_user_id,
        'fictioneer_user_subscribestar_link',
        sanitize_url( $_POST['fictioneer_user_subscribestar_link'] )
      );
    }

    // PayPal link (if any)
    if ( isset( $_POST['fictioneer_user_paypal_link'] ) ) {
      update_user_meta(
        $updated_user_id,
        'fictioneer_user_paypal_link',
        sanitize_url( $_POST['fictioneer_user_paypal_link'] )
      );
    }

    // Donation link (if any)
    if ( isset( $_POST['fictioneer_user_donation_link'] ) ) {
      update_user_meta(
        $updated_user_id,
        'fictioneer_user_donation_link',
        sanitize_url( $_POST['fictioneer_user_donation_link'] )
      );
    }

    // Support message (if any)
    if ( isset( $_POST['fictioneer_support_message'] ) ) {
      update_user_meta(
        $updated_user_id,
        'fictioneer_support_message',
        sanitize_text_field( $_POST['fictioneer_support_message'] )
      );
    }
  }
}
add_action( 'personal_options_update', 'fictioneer_update_my_user_profile' );
add_action( 'edit_user_profile_update', 'fictioneer_update_my_user_profile' );

// =============================================================================
// UPDATE FRONTEND USER PROFILE
// =============================================================================

/**
 * Update profile from frontend
 *
 * @since Fictioneer 5.2.5
 */

function fictioneer_update_frontend_profile() {
  // Verify request
  if ( ! check_admin_referer( 'fictioneer_update_frontend_profile', 'fictioneer_nonce' ) ) {
    wp_die( __( 'Nonce verification failed. Please try again.', 'fictioneer' ) );
  }

  // Setup
  $user = wp_get_current_user();
  $user_id = absint( $_POST['user_id'] );
  $email = sanitize_email( $_POST['email'] ?? '' );
  $nickname = sanitize_user( $_POST['nickname'] ?? '' );

  // Guard
  if ( $user->ID !== $user_id ) {
    wp_die( __( 'Access denied.', 'fictioneer' ) );
  }

  // Email?
  if ( ! empty( $email ) ) {
    send_confirmation_on_profile_email();
  }

  // Nickname?
  if ( ! empty( $nickname  ) && ! $user->fictioneer_admin_disable_renaming ) {
    wp_update_user(
      array(
        'ID' => $user_id ,
        'nickname' => $nickname,
        'display_name' => $nickname
      )
    );
  }

  // Hide custom badge?
  $checkbox_value = fictioneer_sanitize_checkbox( $_POST['fictioneer_hide_badge'] ?? false );
  update_user_meta( $user_id, 'fictioneer_hide_badge', $checkbox_value );

  // Always use gravatar?
  $checkbox_value = fictioneer_sanitize_checkbox( $_POST['fictioneer_enforce_gravatar'] ?? false );
  update_user_meta( $user_id, 'fictioneer_enforce_gravatar', $checkbox_value );

  // Disable avatar?
  $checkbox_value = fictioneer_sanitize_checkbox( $_POST['fictioneer_disable_avatar'] ?? false );
  update_user_meta( $user_id, 'fictioneer_disable_avatar', $checkbox_value );

  // Override assigned badge?
  $checkbox_value = fictioneer_sanitize_checkbox( $_POST['fictioneer_disable_badge_override'] ?? false );
  update_user_meta( $user_id, 'fictioneer_disable_badge_override', $checkbox_value );

  // Always subscribe to comments?
  $checkbox_value = fictioneer_sanitize_checkbox( $_POST['fictioneer_comment_reply_notifications'] ?? false );
  update_user_meta( $user_id, 'fictioneer_comment_reply_notifications', $checkbox_value );

  // Redirect
  wp_safe_redirect(
    add_query_arg(
      array(
        'fictioneer-notice' => __( 'Profile updated.', 'fictioneer' ),
        'success' => 1
      ),
      wp_get_referer() . '#profile'
    )
  );

  // Terminate
  exit();
}
add_action( 'admin_post_fictioneer_update_frontend_profile', 'fictioneer_update_frontend_profile' );

/**
 * Cancel email change from frontend
 *
 * @since Fictioneer 5.2.5
 */

function fictioneer_cancel_frontend_email_change() {
  // Verify request
  if ( ! check_admin_referer( 'fictioneer_cancel_frontend_email_change', 'fictioneer_nonce' ) ) {
    wp_die( __( 'Nonce verification failed. Please try again.', 'fictioneer' ) );
  }

  // Cancel change
  delete_user_meta( get_current_user_id(), '_new_email' );

  // Redirect
  wp_safe_redirect(
    add_query_arg(
      array(
        'fictioneer-notice' => __( 'Email change cancelled.', 'fictioneer' )
      ),
      wp_get_referer() . '#profile'
    )
  );

  // Terminate
  exit();
}
add_action( 'admin_post_fictioneer_cancel_frontend_email_change', 'fictioneer_cancel_frontend_email_change' );

// =============================================================================
// GET CUSTOM BADGE
// =============================================================================

if ( ! function_exists( 'fictioneer_get_override_badge' ) ) {
  /**
   * Get an user's custom badge (if any)
   *
   * @since Fictioneer 4.0
   *
   * @param WP_User        $user     The user.
   * @param string|boolean $default  Default value or false.
   *
   * @return string|boolean The badge label, default, or false.
   */

  function fictioneer_get_override_badge( $user, $default = false ) {
    // Abort conditions...
    if (
      ! $user ||
      get_the_author_meta( 'fictioneer_hide_badge', $user->ID )
    ) {
      return $default;
    }

    // Setup
    $badge = get_the_author_meta( 'fictioneer_badge_override', $user->ID );

    // Return badge of found and not disabled
    if ( $badge && ! get_the_author_meta( 'fictioneer_disable_badge_override', $user->ID ) ) {
      return $badge;
    }

    // Return default
    return $default;
  }
}

// =============================================================================
// GET PATREON BADGE
// =============================================================================

if ( ! function_exists( 'fictioneer_get_patreon_badge' ) ) {
  /**
   * Get an user's Patreon badge (if any)
   *
   * @since Fictioneer 5.0
   *
   * @param WP_User        $user     The user.
   * @param string|boolean $default  Default value or false.
   *
   * @return string|boolean The badge label, default, or false.
   */

  function fictioneer_get_patreon_badge( $user, $default = false ) {
    // Abort conditions...
    if ( ! $user ) {
      return $default;
    }

    // Setup
    $patreon_tiers = get_user_meta( $user->ID, 'fictioneer_patreon_tiers', true );
    $last_updated = $patreon_tiers[0]['timestamp'];

    // Check if still valid (two weeks since last login) if not empty
    if ( ! empty( $patreon_tiers ) && time() <= $last_updated + WEEK_IN_SECONDS * 2 ) {
      $label = get_option( 'fictioneer_patreon_label' );
      return empty( $label ) ? _x( 'Patron', 'Default Patreon supporter badge label.', 'fictioneer' ) : $label;
    }

    return $default;
  }
}

// =============================================================================
// GET UNIQUE USER FINGERPRINT
// =============================================================================

if ( ! function_exists( 'fictioneer_get_user_fingerprint' ) ) {
  /**
   * Returns an unique MD5 hash for the user
   *
   * In order to differentiate users on the frontend even if they have the same
   * display name (which is possible) but without exposing any sensitive data,
   * a simple cryptic hash is calculated.
   *
   * @since Fictioneer 4.7
   *
   * @param int $user_id  User ID to get the hash for.
   *
   * @return string The unique fingerprint hash or empty string if not found.
   */

  function fictioneer_get_user_fingerprint( $user_id ) {
    // Setup
    $user = get_user_by( 'ID', $user_id );

    if ( ! $user ) {
      return '';
    }

    $fingerprint = get_user_meta( $user_id, 'fictioneer_user_fingerprint', true );

    // If hash does not yet exist, create one
    if ( empty( $fingerprint ) ) {
      $fingerprint = md5( $user->user_login . $user_id );

      update_user_meta(
        $user_id,
        'fictioneer_user_fingerprint',
        $fingerprint
      );
    }

    // Return hash
    return $fingerprint;
  }
}

// =============================================================================
// SELF-UNSET OAUTH CONNECTIONS - AJAX
// =============================================================================

/**
 * Unset one of the user's OAuth bindings via AJAX
 *
 * @since Fictioneer 4.0
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
 */

function fictioneer_ajax_unset_my_oauth() {
  // Setup
  $sender_id = isset( $_POST['id'] ) ? fictioneer_validate_id( $_POST['id'] ) : false;
  $channel = isset( $_POST['channel'] ) ? sanitize_text_field( $_POST['channel'] ) : false;
  $user = fictioneer_get_validated_ajax_user( 'nonce', 'fictioneer_unset_oauth' );

  // Validations
  if (
    ! $user ||
    ! $sender_id ||
    ! $channel ||
    ! in_array( $channel, ['discord', 'twitch', 'patreon', 'google'] ) ||
    $sender_id !== $user->ID
  ) {
    wp_send_json_error( ['error' => __( 'Request did not pass validation.', 'fictioneer' )] );
  }

  // Delete connection
  if ( delete_user_meta( $user->ID, "fictioneer_{$channel}_id_hash" ) ) {
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
    wp_send_json_error( ['error' => __( 'Database error. Please ask an administrator.', 'fictioneer' )] );
  }
}
add_action( 'wp_ajax_fictioneer_ajax_unset_my_oauth', 'fictioneer_ajax_unset_my_oauth' );

// =============================================================================
// DELETE USER COMMENTS
// =============================================================================

if ( ! function_exists( 'fictioneer_soft_delete_user_comments' ) ) {
  /**
   * Soft delete a user's comments
   *
   * Replace the content and meta data of a user's comments with junk
   * but leave the comment itself in the database. This preserves the
   * structure of comment threads.
   *
   * @since Fictioneer 5.0
   *
   * @param int $user_id  User ID to soft delete the comments for.
   *
   * @return array|false Detailed results about the database update. Accounts
   *                     for completeness, partial success, and errors. Includes
   *                     'complete' (boolean), 'failure' (boolean), 'success' (boolean),
   *                     'comment_count' (int), and 'updated_count' (int). Or false.
   */

  function fictioneer_soft_delete_user_comments( $user_id ) {
    // Setup
    $comments = get_comments( ['user_id' => $user_id] );
    $comment_count = count( $comments );
    $count = 0;
    $complete_one = true;
    $complete_two = true;

    // Not found or empty
    if ( empty( $comments ) ) return false;

    // Loop and update comments
    foreach ( $comments as $comment ) {
      $result_one = wp_update_comment(
        array(
          'user_ID' => 0,
          'comment_author' => __( 'Deleted', 'fictioneer' ),
          'comment_ID' => $comment->comment_ID,
          'comment_content' => __( 'Comment has been deleted by user.', 'fictioneer' ),
          'comment_author_email' => '',
          'comment_author_IP' => '',
          'comment_agent' => '',
          'comment_author_url' => ''
        )
      );

      $result_two = update_comment_meta( $comment->comment_ID, 'fictioneer_deleted_by_user', true );

      // Keep track of updated comments
      if ( $result_one && $result_two ) {
        $count++;
      }

      // Check whether one or more updates failed
      if ( ! $result_one || is_wp_error( $result_one ) ) {
        $complete_one = false;
      }

      if ( ! $result_two || is_wp_error( $result_two ) ) {
        $complete_two = false;
      }
    }

    // Report result
    return array(
      'complete' => $complete_one && $complete_two,
      'failure' => $count == 0,
      'success' => $count == $comment_count && $complete_one && $complete_two,
      'comment_count' => $comment_count,
      'updated_count' => $count
    );
  }
}

// =============================================================================
// SELF-CLEAR USER COMMENTS - AJAX
// =============================================================================

/**
 * Clears all the user's comments via AJAX
 *
 * Queries all comments of the user and overrides the comment data with
 * garbage, preserving the comment thread integrity.
 *
 * @since Fictioneer 5.0
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
 * @see fictioneer_get_validated_ajax_user()
 */

function fictioneer_ajax_clear_my_comments() {
  // Setup and validations
  $user = fictioneer_get_validated_ajax_user( 'nonce', 'fictioneer_clear_comments' );
  if ( ! $user ) wp_send_json_error( ['error' => __( 'Request did not pass validation.', 'fictioneer' )] );

  if (
    fictioneer_is_admin( $user->ID ) ||
    fictioneer_is_author( $user->ID ) ||
    fictioneer_is_moderator( $user->ID ) )
  {
    wp_send_json_error( ['error' => __( 'User role too high. Please ask an administrator.', 'fictioneer' )] );
  }

  // Soft-delete comments
  $result = fictioneer_soft_delete_user_comments( $user->ID );

  if ( ! $result ) {
    wp_send_json_error( ['error' => __( 'Database error. No comments found.', 'fictioneer' )] );
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
    wp_send_json_error( ['error' => __( 'Database error. Comments could not be cleared.', 'fictioneer' )] );
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
// SELF-CLEAR USER COMMENT SUBSCRIPTIONS - AJAX
// =============================================================================

/**
 * Clears all the user's comment subscriptions via AJAX
 *
 * Changes the comment notification validation timestamp, effectively terminating
 * all associated previous comment subscriptions. This is far cheaper than looping
 * and updating all comments.
 *
 * @since Fictioneer 5.0
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
 * @see fictioneer_get_validated_ajax_user()
 */

function fictioneer_ajax_clear_my_comment_subscriptions() {
  // Setup and validations
  $user = fictioneer_get_validated_ajax_user( 'nonce', 'fictioneer_clear_comment_subscriptions' );
  if ( ! $user ) wp_send_json_error( ['error' => __( 'Request did not pass validation.', 'fictioneer' )] );

  // Change validator
  if ( update_user_meta( $user->ID, 'fictioneer_comment_reply_validator', time() ) ) {
    wp_send_json_success( ['success' => __( 'Data has been cleared.', 'fictioneer' )] );
  } else {
    wp_send_json_error( ['error' => __( 'Database error. Comment subscriptions could not be cleared.', 'fictioneer' )] );
  }
}
add_action( 'wp_ajax_fictioneer_ajax_clear_my_comment_subscriptions', 'fictioneer_ajax_clear_my_comment_subscriptions' );

// =============================================================================
// SELF-DELETE USER - AJAX
// =============================================================================

/**
 * Delete an user's account via AJAX
 *
 * @since Fictioneer 4.5
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
// FETCH FINGERPRINT - AJAX
// =============================================================================

/**
 * Sends the user's fingerprint via AJAX
 *
 * @since Fictioneer 5.0
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
 * @see fictioneer_get_validated_ajax_user()
 */

function fictioneer_ajax_get_fingerprint() {
  // Setup and validations
  $user = fictioneer_get_validated_ajax_user();
  if ( ! $user ) wp_send_json_error( ['error' => __( 'Request did not pass validation.', 'fictioneer' )] );

  // Response
  wp_send_json_success( ['fingerprint' => fictioneer_get_user_fingerprint( $user->ID )] );
}
add_action( 'wp_ajax_fictioneer_ajax_get_fingerprint', 'fictioneer_ajax_get_fingerprint' );

?>
