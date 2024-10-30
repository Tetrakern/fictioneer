<?php

// =============================================================================
// UTILITY
// =============================================================================

/**
 * Verify an admin profile action request
 *
 * @since 5.2.5
 *
 * @param string $action  Name of the admin profile action.
 */

function fictioneer_verify_admin_profile_action( $action ) {
  // Verify request
  if ( ! check_admin_referer( $action, 'fictioneer_nonce' ) ) {
    wp_die( __( 'Nonce verification failed. Please try again.', 'fictioneer' ) );
  }
}

/**
 * Finish an admin profile action and perform a redirect
 *
 * @since 5.2.5
 *
 * @param string $notice  Optional. The notice message to include in the redirect URL.
 * @param string $type    Optional. The type of notice. Default 'success'.
 */

function fictioneer_finish_admin_profile_action( $notice = '', $type = 'success' ) {
  // Setup
  $notice = empty( $notice ) ? [] : array( $type => $notice );

  // Redirect
  wp_safe_redirect( add_query_arg( $notice, wp_get_referer() ) );

  // Terminate
  exit();
}

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
  // Guard
  if ( ! check_admin_referer( 'update-user_' . $updated_user_id ) ) {
    return;
  }

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
      fictioneer_sanitize_url( $_POST['fictioneer_external_avatar_url'] ?? '' )
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
  // Guard
  if ( ! check_admin_referer( 'update-user_' . $updated_user_id ) ) {
    return;
  }

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
        fictioneer_sanitize_url( $_POST['fictioneer_user_patreon_link'] ?? '', null, '#^https://(www\.)?patreon\.#' )
      );
    }

    // Ko-Fi link (if any)
    if ( isset( $_POST['fictioneer_user_kofi_link'] ) ) {
      fictioneer_update_user_meta(
        $updated_user_id,
        'fictioneer_user_kofi_link',
        fictioneer_sanitize_url( $_POST['fictioneer_user_kofi_link'] ?? '', null, '#^https://(www\.)?ko-fi\.#' )
      );
    }

    // SubscribeStar link (if any)
    if ( isset( $_POST['fictioneer_user_subscribestar_link'] ) ) {
      fictioneer_update_user_meta(
        $updated_user_id,
        'fictioneer_user_subscribestar_link',
        fictioneer_sanitize_url( $_POST['fictioneer_user_subscribestar_link'] ?? '', null, '#^https://(www\.)?subscribestar\.#' )
      );
    }

    // PayPal link (if any)
    if ( isset( $_POST['fictioneer_user_paypal_link'] ) ) {
      fictioneer_update_user_meta(
        $updated_user_id,
        'fictioneer_user_paypal_link',
        fictioneer_sanitize_url( $_POST['fictioneer_user_paypal_link'] ?? '', null, '#^https://(www\.)?paypal\.#' )
      );
    }

    // Donation link (if any)
    if ( isset( $_POST['fictioneer_user_donation_link'] ) ) {
      fictioneer_update_user_meta(
        $updated_user_id,
        'fictioneer_user_donation_link',
        fictioneer_sanitize_url( $_POST['fictioneer_user_donation_link'] ?? '', 'https://' )
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
// ADMIN PROFILE ACTIONS
// =============================================================================

/**
 * Unset OAuth
 *
 * @since 5.2.5
 */

function fictioneer_admin_profile_unset_oauth() {
  // Setup
  $channel = sanitize_text_field( $_GET['channel'] ?? '' );

  // Verify request
  fictioneer_verify_admin_profile_action( "fictioneer_admin_oauth_unset_{$channel}" );

  // Continue setup
  $current_user_id = get_current_user_id();
  $profile_user_id = absint( $_GET['profile_user_id'] ?? 0 );
  $target_is_admin = fictioneer_is_admin( $profile_user_id );

  // Guard admins
  if ( $target_is_admin && $current_user_id !== $profile_user_id ) {
    wp_die( __( 'Insufficient permissions.', 'fictioneer' ) );
  }

  // Guard users
  if ( $current_user_id !== $profile_user_id ) {
    wp_die( __( 'Insufficient permissions.', 'fictioneer' ) );
  }

  // Unset connection
  delete_user_meta( $profile_user_id, "fictioneer_{$channel}_id_hash" );

  // Remove tiers
  if ( $channel === 'patreon' ) {
    delete_user_meta( $profile_user_id, 'fictioneer_patreon_tiers' );
  }

  // Finish
  fictioneer_finish_admin_profile_action( "admin-profile-unset-oauth-{$channel}" );
}
// Not conditional since removing your connection should always be allowed
add_action( 'admin_post_fictioneer_admin_profile_unset_oauth', 'fictioneer_admin_profile_unset_oauth' );

/**
 * Cleat data node
 *
 * @since 5.2.5
 */

function fictioneer_admin_profile_clear_data_node() {
  // Setup
  $node = sanitize_text_field( $_GET['node'] ?? '' );

  // Verify request
  fictioneer_verify_admin_profile_action( "fictioneer_admin_clear_data_node_{$node}" );

  // Continue setup
  $current_user_id = get_current_user_id();
  $profile_user_id = absint( $_GET['profile_user_id'] ?? 0 );
  $target_is_admin = fictioneer_is_admin( $profile_user_id );
  $result = false;

  // Guard admins
  if ( $target_is_admin && ( $current_user_id !== $profile_user_id ) ) {
    wp_die( __( 'Insufficient permissions.', 'fictioneer' ) );
  }

  // Guard users
  if ( $current_user_id !== $profile_user_id ) {
    wp_die( __( 'Insufficient permissions.', 'fictioneer' ) );
  }

  // Clear data
  switch ( $node ) {
    case 'comments':
      $result = fictioneer_soft_delete_user_comments( $profile_user_id );
      $result = is_array( $result ) ? $result['complete'] : $result;
      break;
    case 'comment-subscriptions':
      $result = fictioneer_update_user_meta( $profile_user_id, 'fictioneer_comment_reply_validator', time() );
      break;
    case 'follows':
      $result = delete_user_meta( $profile_user_id, 'fictioneer_user_follows' );
      delete_user_meta( $profile_user_id, 'fictioneer_user_follows_cache' );
      break;
    case 'reminders':
      $result = delete_user_meta( $profile_user_id, 'fictioneer_user_reminders' );
      break;
    case 'checkmarks':
      $result = delete_user_meta( $profile_user_id, 'fictioneer_user_checkmarks' );
      break;
    case 'bookmarks':
      // Bookmarks are only parsed client-side and stored as JSON string
      $result = fictioneer_update_user_meta( $profile_user_id, 'fictioneer_bookmarks', '{}' );
      break;
  }

  // Finish
  if ( ! empty( $result ) ) {
    fictioneer_finish_admin_profile_action( "admin-profile-cleared-data-node-{$node}" );
  } {
    fictioneer_finish_admin_profile_action( "admin-profile-not-cleared-data-node-{$node}", 'failure' );
  }
}
// Not conditional since clearing your data nodes should always be allowed
add_action( 'admin_post_fictioneer_admin_profile_clear_data_node', 'fictioneer_admin_profile_clear_data_node' );

/**
 * Self-delete account
 *
 * @since 5.6.0
 */

function fictioneer_admin_profile_self_delete() {
  // Verify request
  fictioneer_verify_admin_profile_action( 'fictioneer_admin_profile_self_delete' );

  // Setup
  $sender = absint( $_GET['profile_user_id'] ?? 0 );

  // Guard
  if ( $sender != get_current_user_id() ) {
    wp_die( __( 'Access Denied', 'fictioneer' ) );
  }

  // Delete
  if ( fictioneer_delete_my_account() ) {
    wp_redirect( home_url() );
    exit;
  } {
    fictioneer_finish_admin_profile_action( 'admin-profile-not-deleted', 'failure' );
  }
}

if ( current_user_can( 'fcn_allow_self_delete' ) ) {
  add_action( 'admin_post_fictioneer_admin_profile_self_delete', 'fictioneer_admin_profile_self_delete' );
}

// =============================================================================
// USER SELF-DELETE
// =============================================================================

/**
 * Returns unique username for deleted user discriminated by number
 *
 * @since 5.24.4
 *
 * @return string Unique username.
 */

function fictioneer_get_username_for_deleted() {
  $base_username = _x( 'DeletedUser', 'Deleted user base name.', 'fictioneer' );
  $base_username = sanitize_user( $base_username, true );
  $unique_username = $base_username;
  $suffix = 0;

  while ( username_exists( $unique_username ) ) {
    $suffix++;

    $unique_username = $base_username . $suffix;
    $unique_username = sanitize_user( $unique_username, true );
  }

  return $unique_username;
}

/**
 * Deletes the current user's account and anonymized their comments
 *
 * @since 5.6.0
 * @since 5.24.4 - Optimized with bulk SQL queries.
 *
 * @return bool True if the user was successfully deleted, false otherwise.
 */

function fictioneer_delete_my_account() {
  global $wpdb;

  // Setup
  $current_user = wp_get_current_user();
  $id_to_delete = $current_user->ID;
  $success = false;

  // Guard
  if (
    ! $current_user ||
    $id_to_delete === 1 ||
    in_array( 'administrator', $current_user->roles ) ||
    ! current_user_can( 'fcn_allow_self_delete' ) ||
    current_user_can( 'manage_options' )
  ) {
    return false;
  }

  // Prepare reassign user for posts
  $reassign_id = null;

  $user_has_posts = get_posts(
    array(
      'author' => $id_to_delete,
      'posts_per_page' => 1,
      'post_type' => 'any',
      'post_status' => 'any',
      'update_post_meta_cache' => false,
      'update_post_term_cache' => false,
      'no_found_rows' => true
    )
  );

  if ( ! empty( $user_has_posts ) ) {
    $replacement_username = fictioneer_get_username_for_deleted();
    $replacement_user = get_user_by( 'login', $replacement_username );

    if ( ! $replacement_user ) {
      $reassign_id = wp_insert_user(
        array(
          'user_login' => $replacement_username,
          'user_pass' => wp_generate_password( 32 ),
          'user_email' => 'deleted_' . $id_to_delete . '@example.com',
          'role' => 'author'
        )
      );

      if ( is_wp_error( $reassign_id ) ) {
        return false;
      }
    } else {
      $reassign_id = $replacement_user->ID;
    }
  }

  // Update comments
  $comments = get_comments( array( 'user_id' => $id_to_delete ) );

  if ( ! empty( $comments ) ) {
    $comment_ids = wp_list_pluck( $comments, 'comment_ID' );
    $placeholders = implode( ',', array_fill( 0, count( $comment_ids ), '%d' ) );

    $wpdb->query(
      $wpdb->prepare(
        "UPDATE {$wpdb->comments}
        SET user_id = 0,
          comment_author = %s,
          comment_author_email = %s,
          comment_author_IP = %s,
          comment_agent = %s,
          comment_author_url = %s
        WHERE comment_ID IN ({$placeholders})",
        fcntr( 'deleted_user' ), '', '', '', '', ...$comment_ids
      )
    );

    // Make absolutely sure no comment subscriptions remain
    $wpdb->query(
     $wpdb->prepare(
        "DELETE FROM {$wpdb->commentmeta}
        WHERE meta_key = %s
        AND comment_id IN ({$placeholders})",
        'fictioneer_send_notifications', ...$comment_ids
      )
    );
  }

  // Delete user
  if ( wp_delete_user( $id_to_delete, $reassign_id ) ) {
    $success = true;
  }

  // Multi-site?
  if ( $success && is_multisite() ) {
    if ( ! function_exists( 'wpmu_delete_user' ) ) {
      require_once ABSPATH . '/wp-admin/includes/ms.php';
    }

    $success = wpmu_delete_user( $id_to_delete );
  }

  // Failure
  return $success;
}

// =============================================================================
// OUTPUT ADMIN PROFILE NOTICES
// =============================================================================

if ( ! defined( 'FICTIONEER_ADMIN_PROFILE_NOTICES' ) ) {
  define(
    'FICTIONEER_ADMIN_PROFILE_NOTICES',
    array(
      'admin-profile-unset-oauth-patreon' => __( 'Patreon connection successfully removed.', 'fictioneer' ),
      'admin-profile-unset-oauth-google' => __( 'Google connection successfully removed.', 'fictioneer' ),
      'admin-profile-unset-oauth-twitch' => __( 'Twitch connection successfully removed.', 'fictioneer' ),
      'admin-profile-unset-oauth-discord' => __( 'Discord connection successfully removed.', 'fictioneer' ),
      'admin-profile-not-cleared-data-node-comments' => __( 'Comments could not be cleared.', 'fictioneer' ),
      'admin-profile-not-cleared-data-node-comment-subscriptions' => __( 'Comment subscriptions could not be cleared.', 'fictioneer' ),
      'admin-profile-cleared-data-node-comments' => __( 'Comments successfully cleared.', 'fictioneer' ),
      'admin-profile-cleared-data-node-comment-subscriptions' => __( 'Comment subscriptions successfully cleared.', 'fictioneer' ),
      'admin-profile-not-cleared-data-node-follows' => __( 'Follows could not be cleared.', 'fictioneer' ),
      'admin-profile-not-cleared-data-node-reminders' => __( 'Reminders could not be cleared.', 'fictioneer' ),
      'admin-profile-not-cleared-data-node-checkmarks' => __( 'Checkmarks could not be cleared.', 'fictioneer' ),
      'admin-profile-not-cleared-data-node-bookmarks' => __( 'Bookmarks could not be cleared.', 'fictioneer' ),
      'admin-profile-cleared-data-node-follows' => __( 'Follows successfully cleared.', 'fictioneer' ),
      'admin-profile-cleared-data-node-reminders' => __( 'Reminders successfully cleared.', 'fictioneer' ),
      'admin-profile-cleared-data-node-checkmarks' => __( 'Checkmarks successfully cleared.', 'fictioneer' ),
      'admin-profile-cleared-data-node-bookmarks' => __( 'Bookmarks successfully cleared.', 'fictioneer' ),
      'oauth_already_linked' => __( 'Account already linked to another profile.', 'fictioneer' ),
      'oauth_merged_discord' => __( 'Discord account successfully linked', 'fictioneer' ),
      'oauth_merged_google' => __( 'Google account successfully linked.', 'fictioneer' ),
      'oauth_merged_twitch' => __( 'Twitch account successfully linked.', 'fictioneer' ),
      'oauth_merged_patreon' => __( 'Patreon account successfully linked.', 'fictioneer' ),
      'admin-profile-not-deleted' => __( 'Database error. Account could not be deleted.', 'fictioneer' )
    )
  );
}

/**
 * Output admin settings notices
 *
 * @since 5.2.5
 */

function fictioneer_admin_profile_notices() {
  // Get performed action
  $success = sanitize_text_field( $_GET['success'] ?? '' );
  $failure = sanitize_text_field( $_GET['failure'] ?? '' );

  // Has success notice?
  if ( ! empty( $success ) && isset( FICTIONEER_ADMIN_PROFILE_NOTICES[ $success ] ) ) {
    wp_admin_notice(
      FICTIONEER_ADMIN_PROFILE_NOTICES[ $success ],
      array(
        'type' => 'success',
        'dismissible' => true
      )
    );
  }

  // Has failure notice?
  if ( ! empty( $failure ) && isset( FICTIONEER_ADMIN_PROFILE_NOTICES[ $failure ] ) ) {
    wp_admin_notice(
      FICTIONEER_ADMIN_PROFILE_NOTICES[ $failure ],
      array(
        'type' => 'error',
        'dismissible' => true
      )
    );
  }
}
add_action( 'admin_notices', 'fictioneer_admin_profile_notices' );

// =============================================================================
// SHOW CUSTOM FIELDS IN USER PROFILE
// =============================================================================

/**
 * Add custom HTML to the admin user profile page
 *
 * @since 4.0.0
 *
 * @param WP_User $profile_user  The profile user object. Not necessarily the one
 *                               currently editing the profile!
 */

function fictioneer_custom_profile_fields( $profile_user ) {
  // Setup
  $moderation_message = get_the_author_meta( 'fictioneer_admin_moderation_message', $profile_user->ID );

  // Start HTML ---> ?>
  <h2 class="fictioneer-data-heading"><?php _e( 'Fictioneer', 'fictioneer' ); ?></h2>
  <table class="form-table">
    <tbody>
      <?php

      // Display moderation message (if any)
      if ( ! empty( $moderation_message ) ) {
        echo '<p>' . $moderation_message . '</p>';
      }

      // Hook to show fields
      do_action( 'fictioneer_admin_user_sections', $profile_user );

      ?>
    </tbody>
  </table>
  <?php // <--- End HTML
}
add_action( 'show_user_profile', 'fictioneer_custom_profile_fields', 20 );
add_action( 'edit_user_profile', 'fictioneer_custom_profile_fields', 20 );

// =============================================================================
// SHOW USER ID
// =============================================================================

/**
 * Renders HTML for the user ID in the wp-admin user profile
 *
 * @since 5.7.4
 *
 * @param WP_User $profile_user  The profile user object. Not necessarily the one
 *                               currently editing the profile!
 */

function fictioneer_admin_profile_fields_user_id( $profile_user ) {
  // Setup
  $sender_is_admin = fictioneer_is_admin( get_current_user_id() );
  $sender_is_owner = $profile_user->ID === get_current_user_id();

  // Guard
  if ( ! $sender_is_admin && ! $sender_is_owner ) {
    return;
  }

  // --- Start HTML ---> ?>
  <tr class="user-fictioneer-fingerprint-wrap">
    <th><label for="fictioneer_support_message"><?php _e( 'User ID', 'fictioneer' ); ?></label></th>
    <td><span><?php echo $profile_user->ID; ?></span></td>
  </tr>
  <?php // <--- End HTML
}
add_action( 'fictioneer_admin_user_sections', 'fictioneer_admin_profile_fields_user_id', 4 );

// =============================================================================
// SHOW FINGERPRINT
// =============================================================================

/**
 * Renders HTML for the fingerprint in the wp-admin user profile
 *
 * @since 5.2.5
 *
 * @param WP_User $profile_user  The profile user object. Not necessarily the one
 *                               currently editing the profile!
 */

function fictioneer_admin_profile_fields_fingerprint( $profile_user ) {
  // Setup
  $sender_is_admin = fictioneer_is_admin( get_current_user_id() );
  $sender_is_owner = $profile_user->ID === get_current_user_id();

  // Guard
  if ( ! $sender_is_admin && ! $sender_is_owner ) {
    return;
  }

  // --- Start HTML ---> ?>
  <tr class="user-fictioneer-fingerprint-wrap">
    <th><label for="fictioneer_support_message"><?php _e( 'Fingerprint', 'fictioneer' ); ?></label></th>
    <td>
      <input type="text" value="<?php echo esc_attr( fictioneer_get_user_fingerprint( $profile_user->ID ) ); ?>" class="regular-text" disabled>
      <p class="description"><?php _e( 'Your unique hash. Used to distinguish commenters.', 'fictioneer' ); ?></p>
    </td>
  </tr>
  <?php // <--- End HTML
}
add_action( 'fictioneer_admin_user_sections', 'fictioneer_admin_profile_fields_fingerprint', 5 );

// =============================================================================
// SHOW FLAGS
// =============================================================================

/**
 * Renders HTML for the Profile Flags section in the wp-admin user profile
 *
 * @since 5.2.5
 *
 * @param WP_User $profile_user  The profile user object. Not necessarily the one
 *                               currently editing the profile!
 */

function fictioneer_admin_profile_fields_flags( $profile_user ) {
  // Setup
  $sender_is_admin = fictioneer_is_admin( get_current_user_id() );
  $sender_is_owner = $profile_user->ID === get_current_user_id();

  // Guard
  if ( ! $sender_is_admin && ! $sender_is_owner ) {
    return;
  }

  // Continue setup
  $always_gravatar = get_the_author_meta( 'fictioneer_enforce_gravatar', $profile_user->ID ) ?: false;
  $disable_avatar = get_the_author_meta( 'fictioneer_disable_avatar', $profile_user->ID ) ?: false;
  $lock_avatar = get_the_author_meta( 'fictioneer_lock_avatar', $profile_user->ID ) ?: false;
  $hide_badge = get_the_author_meta( 'fictioneer_hide_badge', $profile_user->ID ) ?: false;
  $disable_override_badge = get_the_author_meta( 'fictioneer_disable_badge_override', $profile_user->ID ) ?: false;
  $reply_notifications = get_the_author_meta( 'fictioneer_comment_reply_notifications', $profile_user->ID ) ?: false;

  // --- Start HTML ---> ?>
  <tr class="user-fictioneer-profile-flags-wrap">
    <th><?php _e( 'Profile Flags', 'fictioneer' ); ?></th>
    <td>
      <fieldset>
        <div>
          <label for="fictioneer_enforce_gravatar" class="checkbox-group">
            <input type="hidden" name="fictioneer_enforce_gravatar" value="0">
            <input
              name="fictioneer_enforce_gravatar"
              type="checkbox"
              id="fictioneer_enforce_gravatar"
              value="1"
              <?php echo checked( 1, $always_gravatar, false ); ?>
            >
            <span><?php _e( 'Always use gravatar', 'fictioneer' ); ?></span>
          </label>
        </div>
        <div>
          <label for="fictioneer_disable_avatar" class="checkbox-group">
            <input type="hidden" name="fictioneer_disable_avatar" value="0">
            <input
              name="fictioneer_disable_avatar"
              type="checkbox"
              id="fictioneer_disable_avatar"
              value="1"
              <?php echo checked( 1, $disable_avatar, false ); ?>
            >
            <span><?php _e( 'Disable avatar', 'fictioneer' ); ?></span>
          </label>
        </div>
        <div>
          <label for="fictioneer_lock_avatar" class="checkbox-group">
            <input type="hidden" name="fictioneer_lock_avatar" value="0">
            <input
              name="fictioneer_lock_avatar"
              type="checkbox"
              id="fictioneer_lock_avatar"
              value="1"
              <?php echo checked( 1, $lock_avatar, false ); ?>
            >
            <span><?php _e( 'Lock avatar', 'fictioneer' ); ?></span>
          </label>
        </div>
        <?php if ( get_option( 'fictioneer_enable_custom_badges' ) ) : ?>
          <div class="profile__input-wrapper profile__input-wrapper--checkbox">
            <label for="fictioneer_hide_badge" class="checkbox-group">
              <input type="hidden" name="fictioneer_hide_badge" value="0">
              <input
                name="fictioneer_hide_badge"
                type="checkbox"
                id="fictioneer_hide_badge"
                value="1"
                <?php echo checked( 1, $hide_badge, false ); ?>
              >
              <span><?php _e( 'Hide badge', 'fictioneer' ); ?></span>
            </label>
          </div>
          <?php if ( ! empty( get_the_author_meta( 'fictioneer_badge_override', $profile_user->ID ) ) ) : ?>
            <div class="profile__input-wrapper profile__input-wrapper--checkbox">
              <label for="fictioneer_disable_badge_override" class="checkbox-group">
                <input type="hidden" name="fictioneer_disable_badge_override" value="0">
                <input
                  name="fictioneer_disable_badge_override"
                  type="checkbox"
                  id="fictioneer_disable_badge_override"
                  value="1"
                  <?php echo checked( 1, $disable_override_badge, false ); ?>
                >
                <span><?php _e( 'Override assigned badge', 'fictioneer' ); ?></span>
              </label>
            </div>
          <?php endif; ?>
        <?php endif; ?>
        <?php if ( get_option( 'fictioneer_enable_comment_notifications' ) ) : ?>
          <div class="profile__input-wrapper profile__input-wrapper--checkbox">
            <label for="fictioneer_comment_reply_notifications" class="checkbox-group">
              <input type="hidden" name="fictioneer_comment_reply_notifications" value="0">
              <input
                name="fictioneer_comment_reply_notifications"
                type="checkbox"
                id="fictioneer_comment_reply_notifications"
                value="1"
                <?php echo checked( 1, $reply_notifications, false ); ?>
              >
              <span><?php _e( 'Always subscribe to comments', 'fictioneer' ); ?></span>
            </label>
          </div>
        <?php endif; ?>
      </fieldset>
    </td>
  </tr>
  <?php // <--- End HTML
}
add_action( 'fictioneer_admin_user_sections', 'fictioneer_admin_profile_fields_flags', 6 );

// =============================================================================
// SHOW OAUTH
// =============================================================================

/**
 * Renders HTML for the OAuth Connections section in the wp-admin user profile
 *
 * @since 5.2.5
 *
 * @param WP_User $profile_user  The profile user object. Not necessarily the one
 *                               currently editing the profile!
 */

function fictioneer_admin_profile_fields_oauth( $profile_user ) {
  // Setup
  $oauth_providers = array(
    ['discord', 'Discord'],
    ['twitch', 'Twitch'],
    ['google', 'Google'],
    ['patreon', 'Patreon']
  );
  $sender_is_owner = $profile_user->ID === get_current_user_id();
  $confirmation_string = _x( 'delete', 'Prompt confirm deletion string.', 'fictioneer' );

  // Guard (only profile owner)
  if ( ! $sender_is_owner ) {
    return;
  }

  // Start HTML ---> ?>
  <tr class="user-fictioneer-oauth-wrap">
    <th><?php _e( 'OAuth 2.0 Connections', 'fictioneer' ); ?></th>
    <td>
      <p style="margin: 0.35em 0 1em !important;">
        <?php _e( 'Your profile can be linked to one or more external accounts, such as Discord or Google. You may add or remove these accounts at your own volition, but be aware that removing all accounts can lock you out with no means of access.', 'fictioneer' ); ?>
      </p>
      <fieldset>
        <?php
        foreach ( $oauth_providers as $provider ) {
          $client_id = get_option( "fictioneer_{$provider[0]}_client_id" ) ?: null;
          $client_secret = get_option( "fictioneer_{$provider[0]}_client_secret" ) ?: null;
          $id_hash = get_the_author_meta( "fictioneer_{$provider[0]}_id_hash", $profile_user->ID ) ?: null;
          $confirmation_message = sprintf(
            __( 'Are you sure? Note that if you disconnect all accounts, you may no longer be able to log back in once you log out. Enter %s to confirm.', 'fictioneer' ),
            mb_strtoupper( $confirmation_string )
          );

          if ( $client_id && $client_secret ) {
            if ( empty( $id_hash ) ) {
              // Start HTML ---> ?>
              <div class="oauth-connection">
                <?php
                  echo fictioneer_get_oauth2_login_link(
                    $provider[0],
                    "<i class='fa-brands fa-{$provider[0]}'></i> <span>{$provider[1]}</span>",
                    array(
                      'classes' => '_disconnected button',
                      'merge' => 1,
                      'return_url' => get_edit_profile_url( $profile_user->ID )
                    )
                  );
                ?>
              </div>
              <?php // <--- End HTML
            } else {
              $unset_url = add_query_arg(
                array(
                  'profile_user_id' => $profile_user->ID,
                  'channel' => $provider[0]
                ),
                wp_nonce_url(
                  admin_url( 'admin-post.php?action=fictioneer_admin_profile_unset_oauth' ),
                  "fictioneer_admin_oauth_unset_{$provider[0]}",
                  'fictioneer_nonce'
                )
              );
              // Start HTML ---> ?>
                <div id="oauth-<?php echo $provider[0]; ?>" class="oauth-connection _<?php echo $provider[0]; ?> _connected">
                  <a
                    href="<?php echo $unset_url; ?>"
                    id="oauth-disconnect-<?php echo $provider[0]; ?>"
                    class="button"
                    data-confirm-dialog
                    data-dialog-message="<?php echo esc_attr( $confirmation_message ); ?>"
                    data-dialog-confirm="<?php echo esc_attr( $confirmation_string ); ?>"
                  >
                    <span><?php _e( 'Disconnect', 'fictioneer' ); ?></span>
                    <i class="fa-brands fa-<?php echo $provider[0]; ?>"></i>
                    <span><?php echo $provider[1]; ?></span>
                  </a>
                </div>
              <?php // <--- End HTML
            }
          }
        }
      ?></fieldset>
    </td>
  </tr>
  <?php // <--- End HTML
}

if ( get_option( 'fictioneer_enable_oauth' ) ) {
  add_action( 'fictioneer_admin_user_sections', 'fictioneer_admin_profile_fields_oauth', 7 );
}

// =============================================================================
// CUSTOM CSS SKINS
// =============================================================================

/**
 * Renders HTML for the CSS Skins section in the wp-admin user profile
 *
 * @since 5.2.5
 *
 * @param WP_User $profile_user  The profile user object. Not necessarily the one
 *                               currently editing the profile!
 */

function fictioneer_admin_profile_fields_skins( $profile_user ) {
  // Start HTML ---> ?>
  <tr class="user-fictioneer-skins-wrap">
    <th><?php _e( 'Skins', 'fictioneer' ); ?></th>
    <td>
      <p style="margin: 0.35em 0 1em !important;"><?php
        _e( 'You can upload up to three custom CSS skins (max. 200 KB each), with one active at a time. These skins apply only to your current device and browser (and may vanish at any time), but you can sync them up and down with your account. Be cautious about which skin you trust, as they can mess up the site.', 'fictioneer' );
      ?></p>
      <p style="margin: 0.35em 0 1.5em !important;"><?php
        printf(
          __( 'To create your own skin, <a href="%s" download>download this template</a>.', 'fictioneer' ),
          esc_url( get_template_directory_uri() . '/css/skin-template.css' )
        );
      ?></p>
      <input name="fictioneer_nonce" type="hidden" value="<?php echo wp_create_nonce( 'fictioneer_nonce' ); ?>">
      <script type="text/javascript" data-jetpack-boost="ignore" data-no-optimize="1" data-no-defer="1" data-no-minify="1">var fcn_skinTranslations = <?php echo json_encode( fictioneer_get_skin_translations() ); ?>;</script>
      <fieldset><?php fictioneer_render_skin_interface(); ?></fieldset>
    </td>
  </tr>
  <?php // <--- End HTML
}

if ( get_option( 'fictioneer_enable_css_skins' ) ) {
  add_action( 'fictioneer_admin_user_sections', 'fictioneer_admin_profile_fields_skins', 7 );
}

// =============================================================================
// SHOW DATA NODES
// =============================================================================

/**
 * Renders HTML for the Data Nodes section in the wp-admin user profile
 *
 * @since 5.2.5
 *
 * @param WP_User $profile_user  The profile user object. Not necessarily the one
 *                               currently editing the profile!
 */

function fictioneer_admin_profile_fields_data_nodes( $profile_user ) {
  // Setup
  $success = sanitize_text_field( $_GET['success'] ?? '' );
  $comments_count = get_comments(
    array( 'user_id' => $profile_user->ID, 'count' => true, 'update_comment_meta_cache' => false )
  );
  $checkmarks_count = 0;
  $checkmarks_chapters_count = 0;
  $follows_count = 0;
  $reminders_count = 0;
  $bookmarks = get_user_meta( $profile_user->ID, 'fictioneer_bookmarks', true ) ?: null;
  $sender_is_owner = $profile_user->ID === get_current_user_id();
  $confirmation_string = _x( 'delete', 'Prompt confirm deletion string.', 'fictioneer' );
  $notification_validator = get_user_meta( $profile_user->ID, 'fictioneer_comment_reply_validator', true ) ?: null;
  $comment_subscriptions_count = 0;
  $nodes = array(
    ['comments', '<i class="fa-solid fa-message"></i>', $comments_count]
  );

  if ( ! empty( $notification_validator ) ) {
    $comment_subscriptions_count = get_comments(
      array(
        'user_id' => $profile_user->ID,
        'meta_key' => 'fictioneer_send_notifications',
        'meta_value' => $notification_validator,
        'count' => true
      )
    );
  }

  // Follows/Reminders/Checkmarks
  if ( get_option( 'fictioneer_enable_follows' ) ) {
    $follows = fictioneer_load_follows( $profile_user );
    $follows_count = count( $follows['data'] );
  }

  if ( get_option( 'fictioneer_enable_reminders' ) ) {
    $reminders = fictioneer_load_reminders( $profile_user );
    $reminders_count = count( $reminders['data'] );
  }

  if ( get_option( 'fictioneer_enable_checkmarks' ) ) {
    $checkmarks = fictioneer_load_checkmarks( $profile_user );
    $checkmarks_count = count( $checkmarks['data'] );
    $checkmarks_chapters_count = fictioneer_count_chapter_checkmarks( $checkmarks );
  }

  // Clear local bookmarks
  if ( $success && $success === 'admin-profile-cleared-data-node-bookmarks' ) {
    echo "<script>localStorage.removeItem('fcnChapterBookmarks');</script>";
  }

  // Comment subscriptions?
  if ( get_option( 'fictioneer_enable_comment_notifications' ) && $comment_subscriptions_count > 0 ) {
    $nodes[] = ['comment-subscriptions','<i class="fa-solid fa-envelope"></i>', $comment_subscriptions_count];
  }

  // Follows?
  if ( get_option( 'fictioneer_enable_follows' ) && $follows_count > 0 ) {
    $nodes[] = ['follows', '<i class="fa-solid fa-star"></i>', $follows_count];
  }

  // Reminders?
  if ( get_option( 'fictioneer_enable_reminders' ) && $reminders_count > 0 ) {
    $nodes[] = ['reminders', '<i class="fa-solid fa-clock"></i>', $reminders_count];
  }

  // Checkmarks?
  if ( get_option( 'fictioneer_enable_checkmarks' ) && $checkmarks_count > 0 ) {
    $nodes[] = ['checkmarks', '<i class="fa-solid fa-circle-check"></i>', "{$checkmarks_count}|{$checkmarks_chapters_count}"];
  }

  // Bookmarks?
  if ( get_option( 'fictioneer_enable_bookmarks' ) && ! empty( $bookmarks ) && $bookmarks != '{}' ) {
    $nodes[] = ['bookmarks', '<i class="fa-solid fa-bookmark"></i>', 0];
  }

  // Guard (only profile owner)
  if ( ! $sender_is_owner ) {
    return;
  }

  // --- Start HTML ---> ?>
  <tr class="user-fictioneer-data-wrap">
    <th><?php _e( 'Data', 'fictioneer' ); ?></th>
    <td>
      <p style="margin-bottom: 1em !important;">
        <?php _e( 'The following items represent data nodes stored in your account. Anything submitted by yourself, such as comments. You can clear these nodes here, but be aware that this is irreversible.', 'fictioneer' ); ?>
      </p>
      <fieldset>
        <?php foreach ( $nodes as $node ) : ?>
          <?php
            $clear_url = add_query_arg(
              array(
                'profile_user_id' => $profile_user->ID,
                'node' => $node[0]
              ),
              wp_nonce_url(
                admin_url( 'admin-post.php?action=fictioneer_admin_profile_clear_data_node' ),
                "fictioneer_admin_clear_data_node_{$node[0]}",
                'fictioneer_nonce'
              )
            );

            $confirmation_message = sprintf(
              __( 'Are you sure you want to clear your %s? This action is irreversible. Enter %s to confirm.', 'fictioneer' ),
              str_replace( '-', ' ', $node[0] ),
              mb_strtoupper( $confirmation_string )
            );
          ?>
          <div class="data-node">
            <a
              href="<?php echo esc_url( $clear_url ); ?>"
              id="data-node-clear-<?php echo $node[0]; ?>"
              class="button"
              data-confirm-dialog
              data-dialog-message="<?php echo esc_attr( $confirmation_message ); ?>"
              data-dialog-confirm="<?php echo esc_attr( $confirmation_string ); ?>"
            >
              <?php echo $node[1]; ?>
              <span><?php
                if ( empty( $node[2] ) ) {
                  printf(
                    __( 'Clear %s', 'fictioneer' ),
                    ucwords( str_replace( '-', ' ', $node[0] ) )
                  );
                } else {
                  printf(
                    __( 'Clear %s (%s)', 'fictioneer' ),
                    ucwords( str_replace( '-', ' ', $node[0] ) ),
                    $node[2]
                  );
                }
              ?></span>
            </a>
          </div>
        <?php endforeach; ?>
      </fieldset>
    </td>
  </tr>
  <?php // <--- End HTML
}
add_action( 'fictioneer_admin_user_sections', 'fictioneer_admin_profile_fields_data_nodes', 8 );

// =============================================================================
// SHOW UNLOCKS SECTION
// =============================================================================

/**
 * Renders HTML for the Unlock Posts section in the wp-admin user profile
 *
 * @since 5.16.0
 *
 * @param WP_User $profile_user  The profile user object. Not necessarily the one
 *                               currently editing the profile!
 */

function fictioneer_admin_profile_post_unlocks( $profile_user ) {
  // Check permissions
  if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'fcn_unlock_posts' ) ) {
    return;
  }

  if ( ! current_user_can( 'edit_users' ) ) {
    return;
  }

  // Setup
  $unlocks = get_user_meta( $profile_user->ID, 'fictioneer_post_unlocks', true ) ?: [];
  $unlocks = is_array( $unlocks ) ? $unlocks : [];

  // Post data
  $posts = [];

  if ( $unlocks ) {
    $query = new WP_Query(
      array(
        'post_type'=> 'any',
        'post_status'=> 'any',
        'posts_per_page' => -1,
        'post__in' => $unlocks,
        'update_post_meta_cache' => false, // Improve performance
        'update_post_term_cache' => false, // Improve performance
        'no_found_rows' => true // Improve performance
      )
    );

    $posts = $query->posts;

    // Prime author cache
    if ( function_exists( 'update_post_author_caches' ) ) {
      update_post_author_caches( $posts );
    }
  }

  // Start HTML ---> ?>
  <tr class="user-unlock-posts-wrap">

    <th><?php _e( 'Unlock Posts', 'fictioneer' ); ?></th>

    <td class="unlock-posts" data-controller="fcn-unlock-posts">
      <fieldset>

        <?php wp_nonce_field( 'search_posts', 'unlock_posts_nonce' ); ?>

        <template data-target="fcn-unlock-posts-item-template">
          <div class="unlock-posts__item" title="" data-target="fcn-unlock-posts-item" data-post-id="0">
            <input type="hidden" name="fictioneer_post_unlocks[]" value="">
            <span class="unlock-posts__item-title"></span>
            <span class="unlock-posts__item-meta"></span>
            <button type="button" class="unlock-posts__delete" data-target="fcn-unlock-posts-delete"><span class="dashicons dashicons-no-alt"></span></button>
          </div>
        </template>

        <p>
          <?php _e( 'Allow the user to ignore the password of selected posts. Chapters inherit the unlock from the story.', 'fictioneer' ); ?>
        </p>

        <div class="unlock-posts__search">

          <div class="unlock-posts__search-form">
            <input type="search" class="unlock-posts__search-input regular-text" name="unlock_search" data-target="fcn-unlock-posts-search" placeholder="<?php _e( 'Search posts to unlock…', 'fictioneer' ); ?>">
            <select class="unlock-posts__search-select" name="unlock_select_type" data-target="fcn-unlock-posts-select" >
              <option value="0" selected><?php _e( 'Any', 'fictioneer' ); ?></option>
              <option value="post"><?php _e( 'Post', 'fictioneer' ); ?></option>
              <option value="page"><?php _e( 'Page', 'fictioneer' ); ?></option>
              <option value="fcn_story"><?php _e( 'Story', 'fictioneer' ); ?></option>
              <option value="fcn_chapter"><?php _e( 'Chapter', 'fictioneer' ); ?></option>
              <option value="fcn_collection"><?php _e( 'Collection', 'fictioneer' ); ?></option>
              <option value="fcn_recommendation"><?php _e( 'Recommendation', 'fictioneer' ); ?></option>
            </select>
            <i class="fa-solid fa-spinner fa-spin" style="--fa-animation-duration: .8s;"></i>
          </div>

          <div class="unlock-posts__search-results" data-target="fcn-unlock-posts-search-results"></div>

        </div>

        <div class="unlock-posts__unlocked-posts" data-target="fcn-unlock-posts-selected">

          <input type="hidden" name="fictioneer_post_unlocks" value="0">

          <?php foreach ( $posts as $post ) : ?>
            <?php
              $type = fictioneer_get_post_type_label( $post->post_type );
              $title = fictioneer_get_safe_title( $post->ID );
              $item_title = sprintf(
                _x( 'Author: %s | Title: %s', 'Unlock post item.', 'fictioneer' ),
                get_the_author_meta( 'display_name', $post->post_author ) ?: __( 'n/a', 'fictioneer' ),
                $title
              );
            ?>
            <div class="unlock-posts__item" title="<?php echo esc_attr( $item_title ); ?>" data-target="fcn-unlock-posts-item" data-post-id="<?php echo $post->ID; ?>">
              <input type="hidden" name="fictioneer_post_unlocks[]" value="<?php echo $post->ID; ?>">
              <span class="unlock-posts__item-title"><?php echo $title; ?></span>
              <span class="unlock-posts__item-meta"><?php printf( _x( '(%s | %s)', 'Unlock post item meta: Type | ID.', 'fictioneer' ), $type, $post->ID ); ?></span>
              <button type="button" class="unlock-posts__delete" data-target="fcn-unlock-posts-delete"><span class="dashicons dashicons-no-alt"></span></button>
            </div>
          <?php endforeach; ?>

        </div>

      </fieldset>
    </td>

  </tr>
  <?php // <--- End HTML
}
add_action( 'fictioneer_admin_user_sections', 'fictioneer_admin_profile_post_unlocks', 9 );

/**
 * Searches for posts to unlock via AJAX
 *
 * @since 5.16.0
 */

function fictioneer_ajax_search_posts_to_unlock() {
  // Validations
  $user = fictioneer_get_validated_ajax_user( 'nonce', 'search_posts' );

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => 'Request did not pass validation.' ) );
  }

  if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'fcn_unlock_posts' ) ) {
    wp_send_json_error( array( 'error' => 'Insufficient permissions.' ) );
  }

  // Setup
  $search = sanitize_text_field( $_REQUEST['search'] ?? '' );
  $type = sanitize_key( $_REQUEST['type'] ?? '' );
  $output = [];

  // Empty?
  if ( empty( $search ) ) {
    wp_send_json_success( array( 'html' => '' ) );
  }

  // Query
  $posts = new WP_Query(
    array(
      'post_type' => $type ?: ['post', 'page', 'fcn_story', 'fcn_chapter', 'fcn_collection', 'fcn_recommendation'],
      'post_status' => 'any',
      'orderby' => 'date',
      'order' => 'desc',
      'posts_per_page' => 20,
      's' => $search,
      'update_post_meta_cache' => false, // Improve performance
      'update_post_term_cache' => false, // Improve performance
      'no_found_rows' => true // Improve performance
    )
  );

  // Prime author cache
  if ( function_exists( 'update_post_author_caches' ) ) {
    update_post_author_caches( $posts->posts );
  }

  // Search result
  if ( ! $posts->posts ) {
    wp_send_json_success( array( 'html' => '' ) );
  } else {
    foreach ( $posts->posts as $post ) {
      $type = fictioneer_get_post_type_label( $post->post_type );
      $title = fictioneer_get_safe_title( $post->ID );
      $item_title = sprintf(
        _x( 'Author: %s | Title: %s', 'Unlock post item.', 'fictioneer' ),
        get_the_author_meta( 'display_name', $post->post_author ) ?: __( 'n/a', 'fictioneer' ),
        $title
      );

      $output[] = '<div class="unlock-posts__item _searched" title="' . esc_attr( $item_title ) . '" data-target="fcn-unlock-posts-search-item" data-post-id="' . $post->ID . '"><span class="unlock-posts__item-title">' . $title . '</span> <span class="unlock-posts__item-meta">' . sprintf( _x( '(%s | %s)', 'Unlock post item meta: Type | ID.', 'fictioneer' ), $type, $post->ID ) . '</span></div>';
    }
  }

  // Response
  wp_send_json_success( array( 'html' => implode( '', $output ) ) );
}
add_action( 'wp_ajax_fictioneer_ajax_search_posts_to_unlock', 'fictioneer_ajax_search_posts_to_unlock' );

/**
 * Update unlocked posts for user
 *
 * @since 5.16.0
 *
 * @param int $updated_user_id  The ID of the updated user.
 */

function fictioneer_update_admin_unlocked_posts( $updated_user_id ) {
  // Guard
  if ( ! check_admin_referer( 'update-user_' . $updated_user_id ) ) {
    return;
  }

  // Check permissions
  if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'fcn_unlock_posts' ) ) {
    return;
  }

  if ( ! current_user_can( 'edit_users' ) ) {
    return;
  }

  // Setup
  $admin_protection = fictioneer_is_admin( $updated_user_id ) && get_current_user_id() !== $updated_user_id;

  // Check if protected, sanitize, validate, and update...
  if ( ! $admin_protection && isset( $_POST['fictioneer_post_unlocks'] )  ) {
    $post_ids = $_POST['fictioneer_post_unlocks'];
    $post_ids = is_array( $post_ids ) ? $post_ids : [];
    $post_ids = array_map( 'absint', $post_ids );
    $post_ids = array_unique( $post_ids );

    $query = new WP_Query(
      array(
        'post_type'=> ['post', 'page', 'fcn_story', 'fcn_chapter', 'fcn_collection', 'fcn_recommendation'],
        'post_status'=> 'any',
        'posts_per_page' => -1,
        'post__in' => $post_ids ?: [0],
        'fields' => 'ids',
        'update_post_meta_cache' => false, // Improve performance
        'update_post_term_cache' => false, // Improve performance
        'no_found_rows' => true // Improve performance
      )
    );

    $post_ids = array_map( 'strval', $query->posts );

    fictioneer_update_user_meta( $updated_user_id, 'fictioneer_post_unlocks', $post_ids );
  }
}
add_action( 'personal_options_update', 'fictioneer_update_admin_unlocked_posts' );
add_action( 'edit_user_profile_update', 'fictioneer_update_admin_unlocked_posts' );

// =============================================================================
// SHOW PATREON SECTION
// =============================================================================

/**
 * Renders HTML for the Patreon section in the wp-admin user profile
 *
 * @since 5.17.0
 *
 * @param WP_User $profile_user  The profile user object. Not necessarily the one
 *                               currently editing the profile!
 */

function fictioneer_admin_profile_patreon( $profile_user ) {
  // Setup
  $editing_user_is_owner = $profile_user->ID === get_current_user_id();
  $editing_user_is_admin = fictioneer_is_admin( get_current_user_id() );
  $data = fictioneer_get_user_patreon_data( $profile_user );

  // Abort conditions...
  if ( ! $editing_user_is_admin && ! $editing_user_is_owner ) {
    return;
  }

  if ( ! $data ) {
    return;
  }

  // Values
  $yes = _x( 'Yes', 'Admin profile Patreon section.', 'fictioneer' );
  $valid = ( $data['valid'] ?? 0 ) ?
    $yes : _x( 'Expired (reauthenticate with Patreon)', 'Admin profile Patreon section.', 'fictioneer' );
  $active = ( $data['patron_status'] ?? 0 ) ? $yes : _x( 'No', 'Admin profile Patreon section.', 'fictioneer' );
  $lifetime_cents = $data['lifetime_support_cents'] ?? 0;
  $last_charge = _x( 'Never', 'Admin profile Patreon section.', 'fictioneer' );
  $last_charge_status = $data['last_charge_status'] ?? __( 'n/a', 'fictioneer' );
  $next_charge = _x( 'Never', 'Admin profile Patreon section.', 'fictioneer' );

  if ( $data['last_charge_date'] ?? 0 ) {
    $last_charge = new DateTime( $data['last_charge_date'], new DateTimeZone( 'UTC' ) );
    $last_charge->setTimezone( new DateTimeZone( get_option( 'timezone_string' ) ) );
    $last_charge = $last_charge->format( 'Y-m-d H:i:s' );
  }

  if ( $data['next_charge_date'] ?? 0 ) {
    $next_charge = new DateTime( $data['next_charge_date'], new DateTimeZone( 'UTC' ) );
    $next_charge->setTimezone( new DateTimeZone( get_option( 'timezone_string' ) ) );
    $next_charge = $next_charge->format( 'Y-m-d H:i:s' );
  }

  // Start HTML ---> ?>
  <tr class="user-patreon-wrap">
    <th><?php _e( 'Patreon Membership', 'fictioneer' ); ?></th>

    <td class="">
      <fieldset>

        <p><?php
          printf(
            _x( '<strong>Data Valid:</strong> %s', 'Admin profile Patreon section.', 'fictioneer' ),
            $valid
          );
        ?></p>

        <p><?php
          printf(
            _x( '<strong>Active Patron:</strong> %s', 'Admin profile Patreon section.', 'fictioneer' ),
            $active
          );
        ?></p>

        <?php if ( current_user_can( 'manage_options' ) ) : ?>

          <p><?php
            printf(
              _x( '<strong>Lifetime Support Cents:</strong> %s', 'Admin profile Patreon section.', 'fictioneer' ),
              $lifetime_cents
            );
          ?></p>

          <p><?php
            printf(
              _x( '<strong>Last Charge Date:</strong> %s', 'Admin profile Patreon section.', 'fictioneer' ),
              $last_charge
            );
          ?></p>

          <p><?php
            printf(
              _x( '<strong>Last Charge Status:</strong> %s', 'Admin profile Patreon section.', 'fictioneer' ),
              $last_charge_status
            );
          ?></p>

          <p><?php
            printf(
              _x( '<strong>Next Charge Date:</strong> %s', 'Admin profile Patreon section.', 'fictioneer' ),
              $next_charge
            );
          ?></p>

        <?php endif; ?>

        <p><?php
          $tiers = [];

          foreach ( ( $data['tiers'] ?? [] ) as $tier ) {
            $tiers[] = sprintf(
              _x( '%s (%s)', 'Patreon tier meta field token (title and amount_cents).', 'fictioneer' ),
              $tier['title'],
              $tier['amount_cents']
            );
          }

          printf(
            _x( '<strong>Tier:</strong> %s', 'Admin profile Patreon section.', 'fictioneer' ),
            implode( ', ', $tiers ) ?: __( 'n/a', 'fictioneer' )
          )
        ?></p>

      </fieldset>
    </td>
  </tr>
  <?php // <--- End HTML
}
add_action( 'fictioneer_admin_user_sections', 'fictioneer_admin_profile_patreon', 10 );

// =============================================================================
// SHOW MODERATION SECTION
// =============================================================================

/**
 * Renders HTML for the moderation section in the wp-admin user profile
 *
 * @since 5.0.0
 *
 * @param WP_User $profile_user  The profile user object. Not necessarily the one
 *                               currently editing the profile!
 */

function fictioneer_admin_profile_moderation( $profile_user ) {
  // Setup
  $editing_user_is_admin = fictioneer_is_admin( get_current_user_id() );
  $editing_user_is_moderator = fictioneer_is_moderator( get_current_user_id() );

  // Abort conditions...
  if ( ! $editing_user_is_admin && ! $editing_user_is_moderator ) {
    return;
  }

  // Start HTML ---> ?>
  <tr class="user-moderation-flags-wrap">
    <th><?php _e( 'Moderation Flags', 'fictioneer' ); ?></th>
    <td>
      <fieldset>
        <div>
          <label for="fictioneer_admin_disable_avatar" class="checkbox-group">
            <input type="hidden" name="fictioneer_admin_disable_avatar" value="0">
            <input name="fictioneer_admin_disable_avatar" type="checkbox" id="fictioneer_admin_disable_avatar" <?php echo checked( 1, get_the_author_meta( 'fictioneer_admin_disable_avatar', $profile_user->ID ), false ); ?> value="1">
            <span><?php _e( 'Disable user avatar', 'fictioneer' ); ?></span>
          </label>
        </div>
        <div>
          <label for="fictioneer_admin_disable_reporting" class="checkbox-group">
            <input type="hidden" name="fictioneer_admin_disable_reporting" value="0">
            <input name="fictioneer_admin_disable_reporting" type="checkbox" id="fictioneer_admin_disable_reporting" <?php echo checked( 1, get_the_author_meta( 'fictioneer_admin_disable_reporting', $profile_user->ID ), false ); ?> value="1">
            <span><?php _e( 'Disable reporting capability', 'fictioneer' ); ?></span>
          </label>
        </div>
        <div>
          <label for="fictioneer_admin_disable_renaming" class="checkbox-group">
            <input type="hidden" name="fictioneer_admin_disable_renaming" value="0">
            <input name="fictioneer_admin_disable_renaming" type="checkbox" id="fictioneer_admin_disable_renaming" <?php echo checked( 1, get_the_author_meta( 'fictioneer_admin_disable_renaming', $profile_user->ID ), false ); ?> value="1">
            <span><?php _e( 'Disable renaming capability', 'fictioneer' ); ?></span>
          </label>
        </div>
        <div>
          <label for="fictioneer_admin_disable_commenting" class="checkbox-group">
            <input type="hidden" name="fictioneer_admin_disable_commenting" value="0">
            <input name="fictioneer_admin_disable_commenting" type="checkbox" id="fictioneer_admin_disable_commenting" <?php echo checked( 1, get_the_author_meta( 'fictioneer_admin_disable_commenting', $profile_user->ID ), false ); ?> value="1">
            <span><?php _e( 'Disable commenting capability', 'fictioneer' ); ?></span>
          </label>
        </div>
        <div>
          <label for="fictioneer_admin_disable_comment_editing" class="checkbox-group">
            <input type="hidden" name="fictioneer_admin_disable_comment_editing" value="0">
            <input name="fictioneer_admin_disable_comment_editing" type="checkbox" id="fictioneer_admin_disable_comment_editing" <?php echo checked( 1, get_the_author_meta( 'fictioneer_admin_disable_comment_editing', $profile_user->ID ), false ); ?> value="1">
            <span><?php _e( 'Disable comment editing capability', 'fictioneer' ); ?></span>
          </label>
        </div>
        <div>
          <label for="fictioneer_admin_disable_post_comment_moderation" class="checkbox-group">
            <input type="hidden" name="fictioneer_admin_disable_post_comment_moderation" value="0">
            <input name="fictioneer_admin_disable_post_comment_moderation" type="checkbox" id="fictioneer_admin_disable_post_comment_moderation" <?php echo checked( 1, get_the_author_meta( 'fictioneer_admin_disable_post_comment_moderation', $profile_user->ID ), false ); ?> value="1">
            <span><?php _e( 'Disable post comment moderation capability', 'fictioneer' ); ?></span>
          </label>
        </div>
        <div>
          <label for="fictioneer_admin_disable_comment_notifications" class="checkbox-group">
            <input type="hidden" name="fictioneer_admin_disable_comment_notifications" value="0">
            <input name="fictioneer_admin_disable_comment_notifications" type="checkbox" id="fictioneer_admin_disable_comment_notifications" <?php echo checked( 1, get_the_author_meta( 'fictioneer_admin_disable_comment_notifications', $profile_user->ID ), false ); ?> value="1">
            <span><?php _e( 'Disable comment reply notifications', 'fictioneer' ); ?></span>
          </label>
        </div>
        <div>
          <label for="fictioneer_admin_always_moderate_comments" class="checkbox-group">
            <input type="hidden" name="fictioneer_admin_always_moderate_comments" value="0">
            <input name="fictioneer_admin_always_moderate_comments" type="checkbox" id="fictioneer_admin_always_moderate_comments" <?php echo checked( 1, get_the_author_meta( 'fictioneer_admin_always_moderate_comments', $profile_user->ID ), false ); ?> value="1">
            <span><?php _e( 'Always hold comments for moderation', 'fictioneer' ); ?></span>
          </label>
        </div>
      </fieldset>
    </td>
  </tr>
  <tr class="user-moderation-message-wrap">
    <th><label for="fictioneer_admin_moderation_message"><?php _e( 'Moderation Message', 'fictioneer' ); ?></label></th>
    <td>
      <textarea name="fictioneer_admin_moderation_message" id="fictioneer_admin_moderation_message" rows="8" cols="30"><?php echo esc_attr( get_the_author_meta( 'fictioneer_admin_moderation_message', $profile_user->ID ) ); ?></textarea>
      <p class="description"><?php _e( 'Custom message in the user profile, which may be a nice thing as well.', 'fictioneer' ); ?></p>
    </td>
  </tr>
  <?php // <--- End HTML
}
add_action( 'fictioneer_admin_user_sections', 'fictioneer_admin_profile_moderation', 10 );

// =============================================================================
// SHOW AUTHOR SECTION
// =============================================================================

/**
 * Renders HTML for the author section in the wp-admin user profile
 *
 * @since 5.0.0
 *
 * @param WP_User $profile_user  The profile user object. Not necessarily the one
 *                               currently editing the profile!
 */

function fictioneer_admin_profile_author( $profile_user ) {
  // Setup
  $is_owner = $profile_user->ID === get_current_user_id();
  $profile_user_is_author = fictioneer_is_author( $profile_user->ID );
  $editing_user_is_admin = fictioneer_is_admin( get_current_user_id() );

  // Abort conditions...
  if ( ! $profile_user_is_author ) {
    return;
  }

  if ( ! $is_owner && ! $editing_user_is_admin ) {
    return;
  }

  // Has pages?
  $profile_user_has_pages = get_pages(
    array(
      'authors' => $profile_user->ID,
      'number' => 1
    )
  );

  // Start HTML ---> ?>
  <?php if ( current_user_can( 'edit_pages' ) ) : ?>
    <tr class="user-author-page-wrap">
      <th><label for="fictioneer_author_page"><?php _e( 'Author Page', 'fictioneer' ); ?></label></th>
      <td>
        <?php if ( $profile_user_has_pages ) : ?>
          <?php
            wp_dropdown_pages(
              array(
                'name' => 'fictioneer_author_page',
                'id' => 'fictioneer_author_page',
                'option_none_value' => __( 'None', 'fictioneer' ),
                'show_option_no_change' => __( 'None', 'fictioneer' ),
                'selected' => get_the_author_meta( 'fictioneer_author_page', $profile_user->ID ),
                'authors' => $profile_user->ID
              )
            );
          ?>
        <?php else : ?>
          <select disabled>
            <option value="-1"><?php _e( 'You have no published pages yet.', 'fictioneer' ); ?></option>
          </select>
        <?php endif; ?>
        <p class="description"><?php _e( 'Rendered inside your public author profile. This will override your biographical info.', 'fictioneer' ); ?></p>
      </td>
    </tr>
  <?php endif; ?>
  <tr class="user-support-message-wrap">
    <th><label for="fictioneer_support_message"><?php _e( 'Support Message', 'fictioneer' ); ?></label></th>
    <td>
      <input name="fictioneer_support_message" type="text" id="fictioneer_support_message" value="<?php echo esc_attr( get_the_author_meta( 'fictioneer_support_message', $profile_user->ID ) ); ?>" class="regular-text">
      <p class="description"><?php _e( 'Rendered at the bottom of your chapters. This will override "You can support the author on".', 'fictioneer' ); ?></p>
    </td>
  </tr>
  <tr class="user-support-links-wrap">
    <th><?php _e( 'Support Links', 'fictioneer' ); ?></th>
    <td>
      <fieldset><?php
        $fields = [];

        $fields['patreon'] = '<input name="fictioneer_user_patreon_link" type="text" id="fictioneer_user_patreon_link" value="' . esc_attr( get_the_author_meta( 'fictioneer_user_patreon_link', $profile_user->ID ) ) . '" class="regular-text" placeholder="https://..."><p class="description">' . __( 'Patreon link', 'fictioneer' ) . '</p>';

        $fields['kofi'] = '<input name="fictioneer_user_kofi_link" type="text" id="fictioneer_user_kofi_link" value="' . esc_attr( get_the_author_meta( 'fictioneer_user_kofi_link', $profile_user->ID ) ) . '" class="regular-text" placeholder="https://..."><p class="description">' . __( 'Ko-Fi link', 'fictioneer' ) . '</p>';

        $fields['subscribestar'] = '<input name="fictioneer_user_subscribestar_link" type="text" id="fictioneer_user_subscribestar_link" value="' . esc_attr( get_the_author_meta( 'fictioneer_user_subscribestar_link', $profile_user->ID ) ) . '" class="regular-text" placeholder="https://...">
        <p class="description">' . __( 'SubscribeStar link', 'fictioneer' ) . '</p>';

        $fields['paypal'] = '<input name="fictioneer_user_paypal_link" type="text" id="fictioneer_user_paypal_link" value="' . esc_attr( get_the_author_meta( 'fictioneer_user_paypal_link', $profile_user->ID ) ) . '" class="regular-text" placeholder="https://..."><p class="description">' . __( 'PayPal link', 'fictioneer' ). '</p>';

        $fields['donation'] = '<input name="fictioneer_user_donation_link" type="text" id="fictioneer_user_donation_link" value="' . esc_attr( get_the_author_meta( 'fictioneer_user_donation_link', $profile_user->ID ) ) . '" class="regular-text" placeholder="https://..."><p class="description">' . __( 'Generic donation link', 'fictioneer' ) . '</p>';

        $fields = apply_filters( 'fictioneer_filter_profile_fields_support', $fields, $profile_user );

        echo implode( '<br>', $fields );
      ?></fieldset>
    </td>
  </tr>
  <?php // <--- End HTML
}
add_action( 'fictioneer_admin_user_sections', 'fictioneer_admin_profile_author', 20 );

// =============================================================================
// SHOW OAUTH ID HASHES SECTION
// =============================================================================

/**
 * Renders HTML for the OAuth section in the wp-admin user profile
 *
 * @since 5.0.0
 *
 * @param WP_User $profile_user  The profile user object. Not necessarily the one
 *                               currently editing the profile!
 */

function fictioneer_admin_profile_oauth( $profile_user ) {
  // Setup
  $editing_user_is_admin = fictioneer_is_admin( get_current_user_id() );

  // Abort conditions...
  if ( ! $editing_user_is_admin || ! get_option( 'fictioneer_enable_oauth' ) ) {
    return;
  }

  // Start HTML ---> ?>
  <tr class="user-oauth-connections-wrap">
    <th><?php _e( 'Account Bindings', 'fictioneer' ); ?></th>
    <td>
      <fieldset>
        <input name="fictioneer_discord_id_hash" type="text" id="fictioneer_discord_id_hash" value="<?php echo esc_attr( get_the_author_meta( 'fictioneer_discord_id_hash', $profile_user->ID ) ); ?>" class="regular-text">
        <p class="description"><?php _e( 'Discord ID Hash', 'fictioneer' ); ?></p>
        <br>
        <input name="fictioneer_twitch_id_hash" type="text" id="fictioneer_twitch_id_hash" value="<?php echo esc_attr( get_the_author_meta( 'fictioneer_twitch_id_hash', $profile_user->ID ) ); ?>" class="regular-text">
        <p class="description"><?php _e( 'Twitch ID Hash', 'fictioneer' ); ?></p>
        <br>
        <input name="fictioneer_google_id_hash" type="text" id="fictioneer_google_id_hash" value="<?php echo esc_attr( get_the_author_meta( 'fictioneer_google_id_hash', $profile_user->ID ) ); ?>" class="regular-text">
        <p class="description"><?php _e( 'Google ID Hash', 'fictioneer' ); ?></p>
        <br>
        <input name="fictioneer_patreon_id_hash" type="text" id="fictioneer_patreon_id_hash" value="<?php echo esc_attr( get_the_author_meta( 'fictioneer_patreon_id_hash', $profile_user->ID ) ); ?>" class="regular-text">
        <p class="description"><?php _e( 'Patreon ID Hash', 'fictioneer' ); ?></p>
      </fieldset>
    </td>
  </tr>
  <?php // <--- End HTML
}

if ( FICTIONEER_SHOW_OAUTH_HASHES ) {
  add_action( 'fictioneer_admin_user_sections', 'fictioneer_admin_profile_oauth', 30 );
}

// =============================================================================
// SHOW BADGE SECTION
// =============================================================================

/**
 * Renders HTML for the badge section in the wp-admin user profile
 *
 * @since 5.0.0
 *
 * @param WP_User $profile_user  The profile user object. Not necessarily the one
 *                               currently editing the profile!
 */

function fictioneer_admin_profile_badge( $profile_user ) {
  // Setup
  $editing_user_is_admin = fictioneer_is_admin( get_current_user_id() );

  // Abort conditions...
  if ( ! $editing_user_is_admin ) {
    return;
  }

  // Start HTML ---> ?>
  <tr class="user-badge-override-wrap">
    <th><label for="fictioneer_badge_override"><?php _e( 'Badge Override', 'fictioneer' ); ?></label></th>
    <td>
      <input name="fictioneer_badge_override" type="text" id="fictioneer_badge_override" value="<?php echo esc_attr( get_the_author_meta( 'fictioneer_badge_override', $profile_user->ID ) ); ?>" class="regular-text">
      <p class="description"><?php _e( 'Override the user’s badge.', 'fictioneer' ); ?></p>
    </td>
  </tr>
  <?php // <--- End HTML
}
add_action( 'fictioneer_admin_user_sections', 'fictioneer_admin_profile_badge', 40 );

// =============================================================================
// SHOW EXTERNAL AVATAR SECTION
// =============================================================================

/**
 * Renders HTML for the external avatar section in the wp-admin user profile
 *
 * @since 5.0.0
 *
 * @param WP_User $profile_user  The profile user object. Not necessarily the one
 *                               currently editing the profile!
 */

function fictioneer_admin_profile_external_avatar( $profile_user ) {
  // Setup
  $editing_user_is_admin = fictioneer_is_admin( get_current_user_id() );

  // Abort conditions...
  if ( ! $editing_user_is_admin ) {
    return;
  }

  // Start HTML ---> ?>
  <tr class="user-external-avatar-wrap">
    <th><label for="fictioneer_external_avatar_url"><?php _e( 'External Avatar URL', 'fictioneer' ); ?></label></th>
    <td>
      <input name="fictioneer_external_avatar_url" type="text" id="fictioneer_external_avatar_url" value="<?php echo esc_attr( get_the_author_meta( 'fictioneer_external_avatar_url', $profile_user->ID ) ); ?>" class="regular-text">
      <p class="description"><?php _e( 'There is no guarantee this URL remains valid!', 'fictioneer' ); ?></p>
    </td>
  </tr>
  <?php // <--- End HTML
}
add_action( 'fictioneer_admin_user_sections', 'fictioneer_admin_profile_external_avatar', 50 );

// =============================================================================
// DANGER ZONE SECTION
// =============================================================================

/**
 * Renders HTML for the danger zone section in the wp-admin user profile
 *
 * @since 5.6.0
 *
 * @param WP_User $profile_user  The profile user object. Not necessarily the one
 *                               currently editing the profile!
 */

function fictioneer_admin_danger_zone( $profile_user ) {
  // Setup
  $is_owner = $profile_user->ID === get_current_user_id();

  // Only for the profile user
  if ( ! $is_owner ) {
    return;
  }

  // Action
  $action = 'fictioneer_admin_profile_self_delete';
  $confirmation_string = _x( 'delete', 'Prompt confirm deletion string.', 'fictioneer' );
  $confirmation_message = sprintf(
    __( 'Are you sure? Your account will be irrevocably deleted, although this will not remove your comments. Enter %s to confirm.', 'fictioneer' ),
    mb_strtoupper( $confirmation_string )
  );

  $deletion_url = add_query_arg(
    array( 'profile_user_id' => $profile_user->ID ),
    wp_nonce_url( admin_url( "admin-post.php?action={$action}" ), $action, 'fictioneer_nonce' )
  );

  // Start HTML ---> ?>
  <tr class="user-danger-zone-wrap">
    <th><label for=""><?php _e( 'Delete Account', 'fictioneer' ); ?></label></th>
    <td>
      <p style="margin: 0.35em 0 1em !important;"><?php _e( 'You can delete your account and associated user data with it. Submitted <em>content</em> such as comments and posts will remain under the “Deleted User” name unless you remove them <em>prior</em>. Be aware that once you delete your account, there is no going back.', 'fictioneer' ); ?></p>
      <fieldset>
        <div>
          <a
            href="<?php echo $deletion_url; ?>"
            id="delete-my-account"
            class="button button-danger"
            data-confirm-dialog
            data-dialog-message="<?php echo esc_attr( $confirmation_message ); ?>"
            data-dialog-confirm="<?php echo esc_attr( $confirmation_string ); ?>"
          ><?php _e( 'Delete Account', 'fictioneer' ); ?></a>
        </div>
      </fieldset>
    </td>
  </tr>
  <?php // <--- End HTML
}

if ( current_user_can( 'fcn_allow_self_delete' ) && ! current_user_can( 'manage_options' ) ) {
  add_action( 'fictioneer_admin_user_sections', 'fictioneer_admin_danger_zone', 9999 );
}
