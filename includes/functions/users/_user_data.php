<?php

// =============================================================================
// AFTER LOGOUT CLEANUP
// =============================================================================

/**
 * Make sure local storage is cleared on logout
 *
 * @since 5.0.0
 */

function fictioneer_after_logout_cleanup() {
  // Start HTML ---> ?>
  <script>
    localStorage.removeItem('fcnProfileAvatar');
    localStorage.removeItem('fcnUserData');
    localStorage.removeItem('fcnAuth');
    localStorage.removeItem('fcnBookshelfContent');
    localStorage.removeItem('fcnChapterBookmarks');
  </script>
  <?php // <--- End HTML
}
add_action( 'login_form', 'fictioneer_after_logout_cleanup' );

// =============================================================================
// UPDATE ADMIN USER PROFILE (ADMIN)
// =============================================================================

/**
 * Update restricted sections of the wp-admin user profile
 *
 * Moderators can update some of these fields, administrators can update all.
 *
 * @since 4.3.0
 *
 * @param int $updated_user_id  The ID of the updated user.
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
    if ( isset( $_POST['fictioneer_admin_disable_avatar'] ) ) {
      fictioneer_update_user_meta(
        $updated_user_id,
        'fictioneer_admin_disable_avatar',
        fictioneer_sanitize_checkbox( $_POST['fictioneer_admin_disable_avatar'] ?? 0 )
      );
    }

    // Disable reporting capability checkbox
    if ( isset( $_POST['fictioneer_admin_disable_reporting'] ) ) {
      fictioneer_update_user_meta(
        $updated_user_id,
        'fictioneer_admin_disable_reporting',
        fictioneer_sanitize_checkbox( $_POST['fictioneer_admin_disable_reporting'] ?? 0 )
      );
    }

    // Disable renaming capability checkbox
    if ( isset( $_POST['fictioneer_admin_disable_renaming'] ) ) {
      fictioneer_update_user_meta(
        $updated_user_id,
        'fictioneer_admin_disable_renaming',
        fictioneer_sanitize_checkbox( $_POST['fictioneer_admin_disable_renaming'] ?? 0 )
      );
    }

    // Disable commenting capability checkbox
    if ( isset( $_POST['fictioneer_admin_disable_commenting'] ) ) {
      fictioneer_update_user_meta(
        $updated_user_id,
        'fictioneer_admin_disable_commenting',
        fictioneer_sanitize_checkbox( $_POST['fictioneer_admin_disable_commenting'] ?? 0 )
      );
    }

    // Disable comment editing capability checkbox
    if ( isset( $_POST['fictioneer_admin_disable_comment_editing'] ) ) {
      fictioneer_update_user_meta(
        $updated_user_id,
        'fictioneer_admin_disable_comment_editing',
        fictioneer_sanitize_checkbox( $_POST['fictioneer_admin_disable_comment_editing'] ?? 0 )
      );
    }

    // Disable post comment moderation capability checkbox
    if ( isset( $_POST['fictioneer_admin_disable_post_comment_moderation'] ) ) {
      fictioneer_update_user_meta(
        $updated_user_id,
        'fictioneer_admin_disable_post_comment_moderation',
        fictioneer_sanitize_checkbox( $_POST['fictioneer_admin_disable_post_comment_moderation'] ?? 0 )
      );
    }

    // Disable comment notification capability checkbox
    if ( isset( $_POST['fictioneer_admin_disable_comment_notifications'] ) ) {
      fictioneer_update_user_meta(
        $updated_user_id,
        'fictioneer_admin_disable_comment_notifications',
        fictioneer_sanitize_checkbox( $_POST['fictioneer_admin_disable_comment_notifications'] ?? 0 )
      );
    }

    // Always hold comments for moderation
    if ( isset( $_POST['fictioneer_admin_always_moderate_comments'] ) ) {
      fictioneer_update_user_meta(
        $updated_user_id,
        'fictioneer_admin_always_moderate_comments',
        fictioneer_sanitize_checkbox( $_POST['fictioneer_admin_always_moderate_comments'] ?? 0 )
      );
    }

    // Custom moderation message shown in profile
    if ( isset( $_POST['fictioneer_admin_moderation_message'] ) ) {
      fictioneer_update_user_meta(
        $updated_user_id,
        'fictioneer_admin_moderation_message',
        sanitize_text_field( $_POST['fictioneer_admin_moderation_message'] ?? '' )
      );
    }
  }

  // Account fields...
  if ( $sender_is_admin && ! $admin_protection ) {
    // Badge override string
    fictioneer_update_user_meta(
      $updated_user_id,
      'fictioneer_badge_override',
      sanitize_text_field( $_POST['fictioneer_badge_override'] ?? '' )
    );

    // External avatar URL
    fictioneer_update_user_meta(
      $updated_user_id,
      'fictioneer_external_avatar_url',
      sanitize_url( $_POST['fictioneer_external_avatar_url'] ?? '' )
    );

    if ( FICTIONEER_SHOW_OAUTH_HASHES ) {
      // Discord OAuth ID (sensible data)
      fictioneer_update_user_meta(
        $updated_user_id,
        'fictioneer_discord_id_hash',
        sanitize_text_field( $_POST['fictioneer_discord_id_hash'] ?? '' )
      );

      // Google OAuth ID (sensible data)
      fictioneer_update_user_meta(
        $updated_user_id,
        'fictioneer_google_id_hash',
        sanitize_text_field( $_POST['fictioneer_google_id_hash'] ?? '' )
      );

      // Twitch OAuth ID (sensible data)
      fictioneer_update_user_meta(
        $updated_user_id,
        'fictioneer_twitch_id_hash',
        sanitize_text_field( $_POST['fictioneer_twitch_id_hash'] ?? '' )
      );

      // Patreon OAuth ID (sensible data)
      fictioneer_update_user_meta(
        $updated_user_id,
        'fictioneer_patreon_id_hash',
        sanitize_text_field( $_POST['fictioneer_patreon_id_hash'] ?? '' )
      );
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
 * @since 4.3.0
 *
 * @param int $updated_user_id  The ID of the updated user.
 */

function fictioneer_update_my_user_profile( $updated_user_id ) {
  // Setup
  $sender_id = get_current_user_id();
  $sender_is_admin = fictioneer_is_admin( $sender_id );
  $sender_is_owner = $sender_id === $updated_user_id;

  // Make sure the sender is the profile owner unless it's an administrator
  if ( ! $sender_is_owner && ! $sender_is_admin ) {
    return false;
  }

  // Profile flags...
  if ( $sender_is_owner || $sender_is_admin ) {
    // Enforce gravatar checkbox
    if ( isset( $_POST['fictioneer_enforce_gravatar'] ) ) {
      fictioneer_update_user_meta(
        $updated_user_id,
        'fictioneer_enforce_gravatar',
        fictioneer_sanitize_checkbox( $_POST['fictioneer_enforce_gravatar'] ?? 0 )
      );
    }

    // Disable avatar checkbox
    if ( isset( $_POST['fictioneer_disable_avatar'] ) ) {
      fictioneer_update_user_meta(
        $updated_user_id,
        'fictioneer_disable_avatar',
        fictioneer_sanitize_checkbox( $_POST['fictioneer_disable_avatar'] ?? 0 )
      );
    }

    // Lock avatar checkbox
    if ( isset( $_POST['fictioneer_lock_avatar'] ) ) {
      fictioneer_update_user_meta(
        $updated_user_id,
        'fictioneer_lock_avatar',
        fictioneer_sanitize_checkbox( $_POST['fictioneer_lock_avatar'] ?? 0 )
      );
    }

    // Hide badge checkbox
    if ( isset( $_POST['fictioneer_hide_badge'] ) ) {
      fictioneer_update_user_meta(
        $updated_user_id,
        'fictioneer_hide_badge',
        fictioneer_sanitize_checkbox( $_POST['fictioneer_hide_badge'] ?? 0 )
      );
    }

    // Disable badge override checkbox
    if ( isset( $_POST['fictioneer_disable_badge_override'] ) ) {
      fictioneer_update_user_meta(
        $updated_user_id,
        'fictioneer_disable_badge_override',
        fictioneer_sanitize_checkbox( $_POST['fictioneer_disable_badge_override'] ?? 0 )
      );
    }

    // Always subscribe to reply email notifications
    if ( isset( $_POST['fictioneer_comment_reply_notifications'] ) ) {
      fictioneer_update_user_meta(
        $updated_user_id,
        'fictioneer_comment_reply_notifications',
        fictioneer_sanitize_checkbox( $_POST['fictioneer_comment_reply_notifications'] ?? 0 )
      );
    }
  }

  // Author fields (if author)...
  if ( fictioneer_is_author( $updated_user_id ) || $sender_is_admin ) {
    // Selected author page (if any)
    if ( isset( $_POST['fictioneer_author_page'] ) ) {
      if ( intval( $_POST['fictioneer_author_page'] ?? 0 ) > 0 ) {
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
      } else {
        // Delete
        delete_user_meta( $updated_user_id, 'fictioneer_author_page' );
      }
    }

    // Patreon link (if any)
    if ( isset( $_POST['fictioneer_user_patreon_link'] ) ) {
      fictioneer_update_user_meta(
        $updated_user_id,
        'fictioneer_user_patreon_link',
        sanitize_url( $_POST['fictioneer_user_patreon_link'] ?? '' )
      );
    }

    // Ko-Fi link (if any)
    if ( isset( $_POST['fictioneer_user_kofi_link'] ) ) {
      fictioneer_update_user_meta(
        $updated_user_id,
        'fictioneer_user_kofi_link',
        sanitize_url( $_POST['fictioneer_user_kofi_link'] ?? '' )
      );
    }

    // SubscribeStar link (if any)
    if ( isset( $_POST['fictioneer_user_subscribestar_link'] ) ) {
      fictioneer_update_user_meta(
        $updated_user_id,
        'fictioneer_user_subscribestar_link',
        sanitize_url( $_POST['fictioneer_user_subscribestar_link'] ?? '' )
      );
    }

    // PayPal link (if any)
    if ( isset( $_POST['fictioneer_user_paypal_link'] ) ) {
      fictioneer_update_user_meta(
        $updated_user_id,
        'fictioneer_user_paypal_link',
        sanitize_url( $_POST['fictioneer_user_paypal_link'] ?? '' )
      );
    }

    // Donation link (if any)
    if ( isset( $_POST['fictioneer_user_donation_link'] ) ) {
      fictioneer_update_user_meta(
        $updated_user_id,
        'fictioneer_user_donation_link',
        sanitize_url( $_POST['fictioneer_user_donation_link'] ?? '' )
      );
    }

    // Support message (if any)
    if ( isset( $_POST['fictioneer_support_message'] ) ) {
      fictioneer_update_user_meta(
        $updated_user_id,
        'fictioneer_support_message',
        sanitize_text_field( $_POST['fictioneer_support_message'] ?? '' )
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
 * @since 5.2.5
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
  if ( ! empty( $nickname ) && ! $user->fictioneer_admin_disable_renaming ) {
    wp_update_user(
      array(
        'ID' => $user_id,
        'nickname' => $nickname,
        'display_name' => $nickname
      )
    );
  }

  // Hide custom badge?
  if ( isset( $_POST['fictioneer_hide_badge'] ) ) {
    $checkbox_value = fictioneer_sanitize_checkbox( $_POST['fictioneer_hide_badge'] ?? 0 );
    fictioneer_update_user_meta( $user_id, 'fictioneer_hide_badge', $checkbox_value );
  }

  // Always use gravatar?
  if ( isset( $_POST['fictioneer_enforce_gravatar'] ) ) {
    $checkbox_value = fictioneer_sanitize_checkbox( $_POST['fictioneer_enforce_gravatar'] ?? 0 );
    fictioneer_update_user_meta( $user_id, 'fictioneer_enforce_gravatar', $checkbox_value );
  }

  // Disable avatar?
  if ( isset( $_POST['fictioneer_disable_avatar'] ) ) {
    $checkbox_value = fictioneer_sanitize_checkbox( $_POST['fictioneer_disable_avatar'] ?? 0 );
    fictioneer_update_user_meta( $user_id, 'fictioneer_disable_avatar', $checkbox_value );
  }

  // Lock avatar?
  if ( isset( $_POST['fictioneer_lock_avatar'] ) ) {
    $checkbox_value = fictioneer_sanitize_checkbox( $_POST['fictioneer_lock_avatar'] ?? 0 );
    fictioneer_update_user_meta( $user_id, 'fictioneer_lock_avatar', $checkbox_value );
  }

  // Override assigned badge?
  if ( isset( $_POST['fictioneer_disable_badge_override'] ) ) {
    $checkbox_value = fictioneer_sanitize_checkbox( $_POST['fictioneer_disable_badge_override'] ?? 0 );
    fictioneer_update_user_meta( $user_id, 'fictioneer_disable_badge_override', $checkbox_value );
  }

  // Always subscribe to comments?
  if ( isset( $_POST['fictioneer_comment_reply_notifications'] ) ) {
    $checkbox_value = fictioneer_sanitize_checkbox( $_POST['fictioneer_comment_reply_notifications'] ?? 0 );
    fictioneer_update_user_meta( $user_id, 'fictioneer_comment_reply_notifications', $checkbox_value );
  }

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
 * @since 5.2.5
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
// UPDATE ASSOCIATED DATA ON USER PROFILE CHANGE
// =============================================================================

/**
 * Trigger delegated functions on user profile update
 *
 * @since 5.8.0
 *
 * @param int     $user_id        The ID of the updated user.
 * @param WP_User $old_user_data  Object containing user's data prior to update.
 */

function fictioneer_on_profile_change( $user_id, $old_user_data ) {
  // Setup
  $user_data = get_userdata( $user_id );

  // Display name changed?
  if ( $old_user_data->display_name !== $user_data->display_name ) {
    fictioneer_update_user_comments_nickname( $user_id, $user_data->display_name );
  }

  // Email changed?
  if ( $old_user_data->user_email !== $user_data->user_email ) {
    fictioneer_update_user_comments_email( $user_id, $user_data->user_email );
  }
}
add_filter( 'profile_update', 'fictioneer_on_profile_change', 10, 2 );

/**
 * Updates the display name on all comments for a given user
 *
 * @since 5.8.0
 *
 * @global wpdb $wpdb  WordPress database object.
 *
 * @param int    $user_id           The ID of the user whose comments should be updated.
 * @param string $new_display_name  The new name to be used.
 */

function fictioneer_update_user_comments_nickname( $user_id, $new_display_name ) {
  global $wpdb;

  // Update all comments with the new nickname
  $data = array( 'comment_author' => $new_display_name );
  $where = array( 'user_id' => $user_id );
  $wpdb->update( $wpdb->comments, $data, $where );
}

/**
 * Updates the email on all comments for a given user
 *
 * Note: Will not update comments that have no 'comment_author_email' set,
 * because this might be a privacy issue.
 *
 * @since 5.8.0
 *
 * @global wpdb $wpdb  WordPress database object.
 *
 * @param int    $user_id         The ID of the user whose comments should be updated.
 * @param string $new_user_email  The new email to be used.
 */

function fictioneer_update_user_comments_email( $user_id, $new_user_email ) {
  global $wpdb;

  // SQL to update all comments with the new email (unless unset)
  $sql = "
    UPDATE $wpdb->comments
    SET comment_author_email = %s
    WHERE user_id = %d
    AND comment_author_email IS NOT NULL
    AND comment_author_email != ''
  ";

  // Prepare
  $sql = $wpdb->prepare( $sql, $new_user_email, $user_id );

  // Execute
  $wpdb->query( $sql );
}

// =============================================================================
// GET COMMENT BADGE
// =============================================================================

if ( ! function_exists( 'fictioneer_get_comment_badge' ) ) {
  /**
   * Get HTML for comment badge
   *
   * @since 5.0.0
   *
   * @param WP_User|null    $user            The comment user.
   * @param WP_Comment|null $comment         Optional. The comment object.
   * @param int             $post_author_id  Optional. ID of the author of the post
   *                                         the comment is for.
   *
   * @return string Badge HTML or empty string.
   */

  function fictioneer_get_comment_badge( $user, $comment = null, $post_author_id = 0 ) {
    // Pre-setup
    $user_id = $user ? $user->ID : 0;
    $filter_args = array(
      'comment' => $comment,
      'post_author_id' => $post_author_id
    );

    // Abort conditions...
    if ( empty( $user_id ) || get_the_author_meta( 'fictioneer_hide_badge', $user_id ) ) {
      $filter_args['class'] = 'is-guest';
      $filter_args['badge'] = '';

      return apply_filters( 'fictioneer_filter_comment_badge', '', $user, $filter_args );
    }

    // Setup
    $is_post_author = empty( $comment ) ? false : $comment->user_id == $post_author_id;
    $is_moderator = fictioneer_is_moderator( $user_id );
    $is_admin = fictioneer_is_admin( $user_id );
    $badge_body = '<div class="fictioneer-comment__badge %1$s">%2$s</div>';
    $badge_class = '';
    $badge = '';
    $role_has_badge = user_can( $user_id, 'fcn_show_badge' );

    // Role badge
    if ( $role_has_badge ) {
      if ( $is_post_author ) {
        $badge = fcntr( 'author' );
        $badge_class = 'is-author';
      } elseif ( $is_admin ) {
        $badge = fcntr( 'admin' );
        $badge_class = 'is-admin';
      } elseif ( $is_moderator ) {
        $badge = fcntr( 'moderator' );
        $badge_class = 'is-moderator';
      } elseif ( ! empty( $user->roles ) ) {
        global $wp_roles;

        $role_slug = $user->roles[0] ?? '';
        $role = $wp_roles->roles[ $role_slug ];

        if ( ! empty( $role_slug ) ) {
          $badge = $role['name'];
          $badge_class = "is-{$role_slug}";
        }
      }
    }

    // Patreon badge (if no higher badge set yet)
    if ( empty( $badge ) ) {
      $badge = fictioneer_get_patreon_badge( $user );
      $badge_class = 'is-supporter';
    }

    // Custom badge (can override all)
    if (
      get_option( 'fictioneer_enable_custom_badges' ) &&
      ! get_the_author_meta( 'fictioneer_disable_badge_override', $user_id )
    ) {
      $custom_badge = fictioneer_get_override_badge( $user );

      if ( $custom_badge ) {
        $badge = $custom_badge;
        $badge_class = 'badge-override';
      }
    }

    // Apply filters
    $output = empty( $badge ) ? '' : sprintf( $badge_body, $badge_class, $badge );

    $filter_args['class'] = $badge_class;
    $filter_args['badge'] = $badge;

    $output = apply_filters( 'fictioneer_filter_comment_badge', $output, $user, $filter_args );

    // Return badge or empty sting
    return $output;
  }
}

// =============================================================================
// GET CUSTOM BADGE
// =============================================================================

if ( ! function_exists( 'fictioneer_get_override_badge' ) ) {
  /**
   * Get an user's custom badge (if any)
   *
   * @since 4.0.0
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
   * @since 5.0.0
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

    // Check if still valid if not empty
    if ( fictioneer_patreon_tiers_valid( $user ) ) {
      $label = get_option( 'fictioneer_patreon_label' );

      return empty( $label ) ? _x( 'Patron', 'Default Patreon supporter badge label.', 'fictioneer' ) : $label;
    }

    return $default;
  }
}

/**
 * Checks whether the user's Patreon data still valid
 *
 * Note: Patreon data expires after a set amount of time, one week
 * by default defined as FICTIONEER_PATREON_EXPIRATION_TIME.
 *
 * @since 5.15.0
 *
 * @param WP_User $user  The user.
 *
 * @return boolean True if still valid, false if expired.
 */

function fictioneer_patreon_tiers_valid( $user ) {
  // Abort conditions...
  if ( ! $user ) {
    return apply_filters( 'fictioneer_filter_user_patreon_validation', false, $user, [] );
  }

  // Setup
  $patreon_tiers = get_user_meta( $user->ID, 'fictioneer_patreon_tiers', true );
  $patreon_tiers = is_array( $patreon_tiers ) ? $patreon_tiers : [];
  $last_updated = empty( $patreon_tiers ) ? 0 : ( $patreon_tiers[0]['timestamp'] ?? 0 );

  // Check
  $valid = time() <= $last_updated + FICTIONEER_PATREON_EXPIRATION_TIME;

  // Filter and return
  return apply_filters( 'fictioneer_filter_user_patreon_validation', $valid, $user, $patreon_tiers );
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
   * @since 4.7.0
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
   * @since 5.0.0
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
    $comments = get_comments( array( 'user_id' => $user_id ) );
    $comment_count = count( $comments );
    $count = 0;
    $complete_one = true;
    $complete_two = true;

    // Not found or empty
    if ( empty( $comments ) ) {
      return false;
    }

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

      $result_two = fictioneer_update_comment_meta( $comment->comment_ID, 'fictioneer_deleted_by_user', true );

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

?>
