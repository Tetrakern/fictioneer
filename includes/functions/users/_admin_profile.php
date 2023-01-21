<?php

// =============================================================================
// OUTPUT ADMIN PROFILE NOTICES
// =============================================================================

/**
 * Output admin profile notices
 *
 * @since Fictioneer 5.0
 */

function fictioneer_admin_profile_notices() {
  // Setup
  $action = $_GET['action'] ?? null;
  $nonce = $_GET['fictioneer_nonce'] ?? null;
  $failure = $_GET['failure'] ?? null;
  $success = $_GET['success'] ?? null;

  // Abort if...
  if ( ! $failure && ! $success && ( ! $action || ! $nonce ) ) return;

  // OAuth already linked
  if ( $failure == 'oauth_already_linked' ) {
    echo '<div class="notice notice-error is-dismissible"><p>' . __( 'Account already linked to another profile.', 'fictioneer' ) . '</p></div>';
  }

  // Discord OAuth merged
  if ( $success == 'oauth_merged_discord' ) {
    echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Discord account successfully linked', 'fictioneer' ) . '</p></div>';
  }

  // Google OAuth merged
  if ( $success == 'oauth_merged_google' ) {
    echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Google account successfully linked.', 'fictioneer' ) . '</p></div>';
  }

  // Twitch OAuth merged
  if ( $success == 'oauth_merged_twitch' ) {
    echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Twitch account successfully linked.', 'fictioneer' ) . '</p></div>';
  }

  // Patreon OAuth merged
  if ( $success == 'oauth_merged_patreon' ) {
    echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Patreon account successfully linked.', 'fictioneer' ) . '</p></div>';
  }

  // Discord OAuth removed
  if ( $action == 'oauth_unset_discord' && $nonce && wp_verify_nonce( $nonce, 'unset_oauth' ) ) {
    echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Discord account successfully removed.', 'fictioneer' ) . '</p></div>';
  }

  // Google OAuth removed
  if ( $action == 'oauth_unset_google' && $nonce && wp_verify_nonce( $nonce, 'unset_oauth' ) ) {
    echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Google account successfully removed.', 'fictioneer' ) . '</p></div>';
  }

  // Twitch OAuth removed
  if ( $action == 'oauth_unset_twitch' && $nonce && wp_verify_nonce( $nonce, 'unset_oauth' ) ) {
    echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Twitch account successfully removed.', 'fictioneer' ) . '</p></div>';
  }

  // Patreon OAuth removed
  if ( $action == 'oauth_unset_patreon' && $nonce && wp_verify_nonce( $nonce, 'unset_oauth' ) ) {
    echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Patreon account successfully removed.', 'fictioneer' ) . '</p></div>';
  }

  // Comments cleared
  if ( $action == 'clear_comments' && $nonce && wp_verify_nonce( $nonce, 'clear_data' ) ) {
    echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Comments successfully cleared.', 'fictioneer' ) . '</p></div>';
  }

  // Comments NOT cleared
  if ( $failure == 'clear_comments' ) {
    echo '<div class="notice notice-error is-dismissible"><p>' . __( 'Comments could not be (completely) cleared. Please try again later or contact an administrator.', 'fictioneer' ) . '</p></div>';
  }

  // Comment subscriptions cleared
  if ( $action == 'clear_comment_subscriptions' && $nonce && wp_verify_nonce( $nonce, 'clear_data' ) ) {
    echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Comment subscriptions successfully cleared.', 'fictioneer' ) . '</p></div>';
  }

  // Comment subscriptions NOT cleared
  if ( $failure == 'clear_comment_subscriptions' ) {
    echo '<div class="notice notice-error is-dismissible"><p>' . __( 'Comment subscriptions could not be cleared.', 'fictioneer' ) . '</p></div>';
  }

  // Checkmarks cleared
  if ( $action == 'clear_checkmarks' && $nonce && wp_verify_nonce( $nonce, 'clear_data' ) ) {
    echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Checkmarks successfully cleared.', 'fictioneer' ) . '</p></div>';
  }

  // Checkmarks NOT cleared
  if ( $failure == 'clear_checkmarks' ) {
    echo '<div class="notice notice-error is-dismissible"><p>' . __( 'Checkmarks could not be cleared.', 'fictioneer' ) . '</p></div>';
  }

  // Follows cleared
  if ( $action == 'clear_follows' && $nonce && wp_verify_nonce( $nonce, 'clear_data' ) ) {
    echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Follows successfully cleared.', 'fictioneer' ) . '</p></div>';
  }

  // Follows NOT cleared
  if ( $failure == 'clear_follows' ) {
    echo '<div class="notice notice-error is-dismissible"><p>' . __( 'Follows could not be cleared.', 'fictioneer' ) . '</p></div>';
  }

  // Reminders cleared
  if ( $action == 'clear_reminders' && $nonce && wp_verify_nonce( $nonce, 'clear_data' ) ) {
    echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Reminders successfully cleared.', 'fictioneer' ) . '</p></div>';
  }

  // Reminders NOT cleared
  if ( $failure == 'clear_reminders' ) {
    echo '<div class="notice notice-error is-dismissible"><p>' . __( 'Reminders could not be cleared.', 'fictioneer' ) . '</p></div>';
  }

  // Bookmarks cleared
  if ( $action == 'clear_bookmarks' && $nonce && wp_verify_nonce( $nonce, 'clear_data' ) ) {
    echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Bookmarks successfully cleared.', 'fictioneer' ) . '</p></div>';
  }

  // Bookmarks NOT cleared
  if ( $failure == 'clear_bookmarks' ) {
    echo '<div class="notice notice-error is-dismissible"><p>' . __( 'Bookmarks could not be cleared.', 'fictioneer' ) . '</p></div>';
  }
}
add_action( 'admin_notices', 'fictioneer_admin_profile_notices' );

// =============================================================================
// SHOW CUSTOM FIELDS IN USER PROFILE
// =============================================================================

/**
 * Add custom HTML to the admin user profile page
 *
 * @since Fictioneer 4.0
 *
 * @param WP_User $profile_user The profile user object. Not necessarily the one
 *                              currently editing the profile!
 */

function fictioneer_custom_profile_fields( $profile_user ) {
  // Start HTML ---> ?>
  <h2 class="fictioneer-data-heading"><?php _e( 'Fictioneer', 'fictioneer' ) ?></h2>
  <table class="form-table">
    <tbody>
      <?php do_action( 'fictioneer_admin_user_sections', $profile_user ); ?>
    </tbody>
  </table>
  <?php // <--- End HTML

  // Remove action query args
  fictioneer_clean_actions_from_url();
  fictioneer_clean_failures_from_url();
  fictioneer_clean_successes_from_url();
}
add_action( 'show_user_profile', 'fictioneer_custom_profile_fields', 20 );
add_action( 'edit_user_profile', 'fictioneer_custom_profile_fields', 20 );

// =============================================================================
// SHOW THEME USER PROFILE FIELDS
// =============================================================================

/**
 * Adds HTML for the badge section to the wp-admin user profile
 *
 * @since Fictioneer 5.0
 *
 * @param WP_User $profile_user The profile user object. Not necessarily the one
 *                              currently editing the profile!
 */

if ( ! function_exists( 'fictioneer_admin_user_fields' ) ) {
  function fictioneer_admin_user_fields( $profile_user ) {
    // Setup...
    $action = $_GET['action'] ?? null;
    $nonce = $_GET['fictioneer_nonce'] ?? null;
    $is_owner = $profile_user->ID === get_current_user_id();
    $editing_user_is_admin = fictioneer_is_admin( get_current_user_id() );
    $confirm_string = _x( 'delete', 'Prompt confirm deletion string.', 'fictioneer' );

    // ... abort conditions...
    if ( ! $editing_user_is_admin && ! $is_owner ) return;

    // ... continue setup
    $moderation_message = get_the_author_meta( 'fictioneer_admin_moderation_message', $profile_user->ID );

    // Display moderation message (if any)
    if ( ! empty( $moderation_message ) ) {
      echo '<p>' . $moderation_message . '</p>';
    }

    // Display fingerprint --- Start HTML ---> ?>
    <tr class="user-fictioneer-fingerprint-wrap">
      <th><label for="fictioneer_support_message"><?php _e( 'Fingerprint', 'fictioneer' ) ?></label></th>
      <td>
        <input type="text" value="<?php echo esc_attr( fictioneer_get_user_fingerprint( $profile_user->ID ) ); ?>" class="regular-text" disabled>
        <p class="description"><?php _e( 'Your unique hash. Used to distinguish commenters.', 'fictioneer' ) ?></p>
      </td>
    </tr>
    <?php // <--- End HTML

    // Display profile flags --- Start HTML ---> ?>
    <tr class="user-fictioneer-profile-flags-wrap">
      <th><?php _e( 'Profile Flags', 'fictioneer' ) ?></th>
      <td>
        <fieldset>
          <div>
            <label for="fictioneer_enforce_gravatar" class="checkbox-group">
              <input name="fictioneer_enforce_gravatar" type="checkbox" id="fictioneer_enforce_gravatar" <?php echo checked( 1, get_the_author_meta( 'fictioneer_enforce_gravatar', $profile_user->ID ), false ); ?> value="1">
              <span><?php _e( 'Always use gravatar', 'fictioneer' ) ?></span>
            </label>
          </div>
          <div>
            <label for="fictioneer_disable_avatar" class="checkbox-group">
              <input name="fictioneer_disable_avatar" type="checkbox" id="fictioneer_disable_avatar" <?php echo checked( 1, get_the_author_meta( 'fictioneer_disable_avatar', $profile_user->ID ), false ); ?> value="1">
              <span><?php _e( 'Disable avatar', 'fictioneer' ) ?></span>
            </label>
          </div>
          <?php if ( get_option( 'fictioneer_enable_custom_badges' ) ) : ?>
            <div class="profile__input-wrapper profile__input-wrapper--checkbox">
              <label for="fictioneer_hide_badge" class="checkbox-group">
                <input name="fictioneer_hide_badge" type="checkbox" id="fictioneer_hide_badge" <?php echo checked( 1, get_the_author_meta( 'fictioneer_hide_badge', $profile_user->ID ), false ); ?> value="1">
                <span><?php _e( 'Hide badge', 'fictioneer' ) ?></span>
              </label>
            </div>
            <?php if ( ! empty( get_the_author_meta( 'fictioneer_badge_override', $profile_user->ID ) ) ) : ?>
              <div class="profile__input-wrapper profile__input-wrapper--checkbox">
                <label for="fictioneer_disable_badge_override" class="checkbox-group">
                  <input name="fictioneer_disable_badge_override" type="checkbox" id="fictioneer_disable_badge_override" <?php echo checked( 1, get_the_author_meta( 'fictioneer_disable_badge_override', $profile_user->ID ), false ); ?> value="1">
                  <span><?php _e( 'Override assigned badge', 'fictioneer' ) ?></span>
                </label>
              </div>
            <?php endif; ?>
          <?php endif; ?>
          <?php if ( get_option( 'fictioneer_enable_comment_notifications' ) ) : ?>
            <div class="profile__input-wrapper profile__input-wrapper--checkbox">
              <label for="fictioneer_comment_reply_notifications" class="checkbox-group">
                <input name="fictioneer_comment_reply_notifications" type="checkbox" id="fictioneer_comment_reply_notifications" <?php echo checked( 1, get_the_author_meta( 'fictioneer_comment_reply_notifications', $profile_user->ID ), false ); ?> value="1">
                <span><?php _e( 'Always subscribe to comment reply notifications', 'fictioneer' ) ?></span>
              </label>
            </div>
          <?php endif; ?>
        </fieldset>
      </td>
    </tr>
    <?php // <--- End HTML

    // =============================================================================
    // OAUTH 2.0 (ONLY PROFILE OWNER)
    // =============================================================================

    if ( get_option( 'fictioneer_enable_oauth' ) && $is_owner ) {
      // Inner setup
      $oauth_providers = [
        ['discord', 'Discord'],
        ['twitch', 'Twitch'],
        ['google', 'Google'],
        ['patreon', 'Patreon']
      ];

      // Resolve unset action (if any)
      if ( $is_owner && $action && $nonce && wp_verify_nonce( $nonce, 'unset_oauth' ) ) {
        switch ( $action ) {
          case 'oauth_unset_discord':
            delete_user_meta( $profile_user->ID, 'fictioneer_discord_id_hash' );
            break;
          case 'oauth_unset_google':
            delete_user_meta( $profile_user->ID, 'fictioneer_google_id_hash' );
            break;
          case 'oauth_unset_twitch':
            delete_user_meta( $profile_user->ID, 'fictioneer_twitch_id_hash' );
            break;
          case 'oauth_unset_patreon':
            delete_user_meta( $profile_user->ID, 'fictioneer_patreon_id_hash' );
            break;
        }
      }

      // Start HTML ---> ?>
      <tr class="user-fictioneer-oauth-wrap">
        <th><?php _e( 'OAuth 2.0 Connections', 'fictioneer' ) ?></th>
        <td>
          <p style="margin: .35em 0 1em !important;"><?php _e( 'Your profile can be linked to one or more external accounts, such as Discord or Google. You may add or remove these accounts at your own volition, but be aware that removing all accounts will lock you out with no means of access.', 'fictioneer' ); ?></p>
          <fieldset><?php
            foreach ( $oauth_providers as $provider ) {
              if (
                get_option( "fictioneer_{$provider[0]}_client_id" ) &&
                get_option( "fictioneer_{$provider[0]}_client_secret" )
              ) {
                if ( ! get_the_author_meta( "fictioneer_{$provider[0]}_id_hash", $profile_user->ID ) ) {
                  echo '<div class="oauth-connection">' . fictioneer_get_oauth_login_link(
                    $provider[0],
                    '<i class="fa-brands fa-' . $provider[0] . '"></i> <span>' . $provider[1] . '</span>',
                    false,
                    true,
                    '_disconnected button',
                    0,
                    get_edit_profile_url( $profile_user->ID )
                  ) . '</div>';
                } else {
                   $confirm_message = sprintf(
                    __( 'Are you sure? Note that if you disconnect all accounts, you may no longer be able to log back in once you log out. Enter %s to confirm.', 'fictioneer' ),
                    strtoupper( $confirm_string )
                  );

                  $unset_url = add_query_arg(
                    array(
                      'action' => 'oauth_unset_' . $provider[0]
                    ),
                    wp_nonce_url(
                      get_edit_profile_url( $profile_user->ID ),
                      'unset_oauth',
                      'fictioneer_nonce'
                    )
                  );
                  ?>
                    <div id="oauth-<?php echo $provider[0]; ?>" class="oauth-connection _<?php echo $provider[0]; ?> _connected">
                      <a href="<?php echo $unset_url; ?>" id="oauth-disconnect-<?php echo $provider[0]; ?>" class="button confirm-dialog" data-dialog-message="<?php echo esc_attr( $confirm_message ); ?>" data-dialog-confirm="<?php echo esc_attr( $confirm_string ); ?>">
                        <span><?php _e( 'Disconnect', 'fictioneer' ); ?></span>
                        <i class="fa-brands fa-<?php echo $provider[0]; ?>"></i>
                        <span><?php echo $provider[1]; ?></span>
                      </a>
                    </div>
                  <?php
                }
              }
            }
          ?></fieldset>
        </td>
      </tr>
      <?php // <--- End HTML
    }

    // =============================================================================
    // DATA NODES
    // =============================================================================

    // Resolve clear action (if any)
    if ( $is_owner && $action && $nonce && wp_verify_nonce( $nonce, 'clear_data' ) ) {
      switch ( $action ) {
        case 'clear_comments':
          $result = fictioneer_soft_delete_user_comments( $profile_user->ID );
          $result = is_array( $result ) ? $result['complete'] : $result;
          break;
        case 'clear_comment_subscriptions':
          $result = update_user_meta( $profile_user->ID, 'fictioneer_comment_reply_validator', time() );
          break;
        case 'clear_follows':
          $result = update_user_meta( $profile_user->ID, 'fictioneer_user_follows', [] );
          update_user_meta( $profile_user->ID, 'fictioneer_user_follows_cache', false );
          break;
        case 'clear_reminders':
          $result = update_user_meta( $profile_user->ID, 'fictioneer_user_reminders', [] );
          break;
        case 'clear_checkmarks':
          $result = update_user_meta( $profile_user->ID, 'fictioneer_user_checkmarks', [] );
          break;
        case 'clear_bookmarks':
          // Bookmarks are only parsed client-side and stored as JSON string
          $result = update_user_meta( $profile_user->ID, 'fictioneer_bookmarks', '{}' );
          if ( $result ) echo "<script>localStorage.removeItem('fcnChapterBookmarks');</script>";
          break;
      }

      // Reload with error message on failure
      if ( ! $result ) {
        wp_safe_redirect( admin_url( sprintf( 'profile.php?%s', http_build_query( ['failure' => $action] ) ) ) );
      }
    }

    $comments_count = get_comments( ['user_id' => $profile_user->ID, 'count' => true] );
    $checkmarks = fictioneer_load_checkmarks( $profile_user );
    $checkmarks_count = count( $checkmarks['data'] );
    $checkmarks_chapters_count = fictioneer_count_chapter_checkmarks( $checkmarks );
    $follows = fictioneer_load_follows( $profile_user );
    $follows_count = count( $follows['data'] );
    $reminders = fictioneer_load_reminders( $profile_user );
    $reminders_count = count( $reminders['data'] );
    $bookmarks = get_user_meta( $profile_user->ID, 'fictioneer_bookmarks', true );
    $notification_validator = get_user_meta( $profile_user->ID, 'fictioneer_comment_reply_validator', true );
    $comment_subscriptions_count = 0;

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

    // Start HTML ---> ?>
    <tr class="user-fictioneer-data-wrap">
      <th><?php _e( 'Data', 'fictioneer' ) ?></th>
      <td>
        <p style="margin: .35em 0 1em !important;"><?php _e( 'The following items represent data nodes stored in your account. Anything submitted by yourself, such as comments. You can clear these nodes here, but be aware that this is irreversible.', 'fictioneer' ); ?></p>
        <fieldset>

          <?php if ( $comments_count > 0 ) : ?>
            <?php
              $confirm_message = sprintf(
                __( 'Are you sure? Comments will be irrevocably deleted. Enter %s to confirm.', 'fictioneer' ),
                strtoupper( $confirm_string )
              );

              $clear_url = add_query_arg(
                array(
                  'action' => 'clear_comments'
                ),
                wp_nonce_url(
                  get_edit_profile_url( $profile_user->ID ),
                  'clear_data',
                  'fictioneer_nonce'
                )
              );
            ?>
            <div class="data-node">
              <a href="<?php echo $clear_url; ?>" id="data-node-clear-comments" class="button confirm-dialog" data-dialog-message="<?php echo esc_attr( $confirm_message ); ?>" data-dialog-confirm="<?php echo esc_attr( $confirm_string ); ?>">
                <i class="fa-solid fa-message"></i>
                <span><?php printf( __( 'Clear Comments (%s)', 'fictioneer' ), $comments_count ); ?></span>
              </a>
            </div>
          <?php endif; ?>

          <?php if ( $comment_subscriptions_count > 0 ) : ?>
            <?php
              $confirm_message = sprintf(
                __( 'Are you sure? Comment subscriptions will be irrevocably deleted. Enter %s to confirm.', 'fictioneer' ),
                strtoupper( $confirm_string )
              );

              $clear_url = add_query_arg(
                array(
                  'action' => 'clear_comment_subscriptions'
                ),
                wp_nonce_url(
                  get_edit_profile_url( $profile_user->ID ),
                  'clear_data',
                  'fictioneer_nonce'
                )
              );
            ?>
            <div class="data-node">
              <a href="<?php echo $clear_url; ?>" id="data-node-clear-comments" class="button confirm-dialog" data-dialog-message="<?php echo esc_attr( $confirm_message ); ?>" data-dialog-confirm="<?php echo esc_attr( $confirm_string ); ?>">
                <i class="fa-solid fa-envelope"></i>
                <span><?php printf( __( 'Clear Comment Subscriptions (%s)', 'fictioneer' ), $comment_subscriptions_count ); ?></span>
              </a>
            </div>
          <?php endif; ?>

          <?php if ( get_option( 'fictioneer_enable_follows' ) && $follows_count > 0 ) : ?>
            <?php
              $confirm_message = sprintf(
                __( 'Are you sure? Follows will be irrevocably deleted. Enter %s to confirm.', 'fictioneer' ),
                strtoupper( $confirm_string )
              );

              $clear_url = add_query_arg(
                array(
                  'action' => 'clear_follows'
                ),
                wp_nonce_url(
                  get_edit_profile_url( $profile_user->ID ),
                  'clear_data',
                  'fictioneer_nonce'
                )
              );
            ?>
            <div class="data-node">
              <a href="<?php echo $clear_url; ?>" id="data-node-clear-follows" class="button confirm-dialog" data-dialog-message="<?php echo esc_attr( $confirm_message ); ?>" data-dialog-confirm="<?php echo esc_attr( $confirm_string ); ?>">
                <i class="fa-solid fa-star"></i>
                <span><?php printf( __( 'Clear Follows (%s)', 'fictioneer' ), $follows_count ); ?></span>
              </a>
            </div>
          <?php endif; ?>

          <?php if ( get_option( 'fictioneer_enable_reminders' ) && $reminders_count > 0 ) : ?>
            <?php
              $confirm_message = sprintf(
                __( 'Are you sure? Reminders will be irrevocably deleted. Enter %s to confirm.', 'fictioneer' ),
                strtoupper( $confirm_string )
              );

              $clear_url = add_query_arg(
                array(
                  'action' => 'clear_reminders'
                ),
                wp_nonce_url(
                  get_edit_profile_url( $profile_user->ID ),
                  'clear_data',
                  'fictioneer_nonce'
                )
              );
            ?>
            <div class="data-node">
              <a href="<?php echo $clear_url; ?>" id="data-node-clear-reminders" class="button confirm-dialog" data-dialog-message="<?php echo esc_attr( $confirm_message ); ?>" data-dialog-confirm="<?php echo esc_attr( $confirm_string ); ?>">
                <i class="fa-solid fa-clock"></i>
                <span><?php printf( __( 'Clear Reminders (%s)', 'fictioneer' ), $reminders_count ); ?></span>
              </a>
            </div>
          <?php endif; ?>

          <?php if ( get_option( 'fictioneer_enable_checkmarks' ) && $checkmarks_count > 0 ) : ?>
            <?php
              $confirm_message = sprintf(
                __( 'Are you sure? Checkmarks will be irrevocably deleted. Enter %s to confirm.', 'fictioneer' ),
                strtoupper( $confirm_string )
              );

              $clear_url = add_query_arg(
                array(
                  'action' => 'clear_checkmarks'
                ),
                wp_nonce_url(
                  get_edit_profile_url( $profile_user->ID ),
                  'clear_data',
                  'fictioneer_nonce'
                )
              );
            ?>
            <div class="data-node">
              <a href="<?php echo $clear_url; ?>" id="data-node-clear-checkmarks" class="button confirm-dialog" data-dialog-message="<?php echo esc_attr( $confirm_message ); ?>" data-dialog-confirm="<?php echo esc_attr( $confirm_string ); ?>">
                <i class="fa-solid fa-circle-check"></i>
                <span><?php printf( __( 'Clear Checkmarks (%1$s | %2$s)', 'fictioneer' ), $checkmarks_count, $checkmarks_chapters_count ); ?></span>
              </a>
            </div>
          <?php endif; ?>

          <?php if ( get_option( 'fictioneer_enable_bookmarks' ) && ! empty( $bookmarks ) && $bookmarks != '{}' ) : ?>
            <?php
              $confirm_message = sprintf(
                __( 'Are you sure? Bookmarks will be irrevocably deleted. Enter %s to confirm.', 'fictioneer' ),
                strtoupper( $confirm_string )
              );

              $clear_url = add_query_arg(
                array(
                  'action' => 'clear_bookmarks'
                ),
                wp_nonce_url(
                  get_edit_profile_url( $profile_user->ID ),
                  'clear_data',
                  'fictioneer_nonce'
                )
              );

              // Bookmarks are never evaluated server-side, so there is no count
            ?>
            <div class="data-node">
              <a href="<?php echo $clear_url; ?>" id="data-node-clear-bookmarks" class="button confirm-dialog" data-dialog-message="<?php echo esc_attr( $confirm_message ); ?>" data-dialog-confirm="<?php echo esc_attr( $confirm_string ); ?>">
                <i class="fa-solid fa-bookmark"></i>
                <span><?php _e( 'Clear Bookmarks', 'fictioneer' ); ?></span>
              </a>
            </div>
          <?php endif; ?>

        </fieldset>
      </td>
    </tr>
    <?php // <--- End HTML
  }
}
add_action( 'fictioneer_admin_user_sections', 'fictioneer_admin_user_fields', 5 );

// =============================================================================
// SHOW MODERATION SECTION
// =============================================================================

/**
 * Adds HTML for the moderation section to the wp-admin user profile
 *
 * @since Fictioneer 5.0
 *
 * @param WP_User $profile_user The profile user object. Not necessarily the one
 *                              currently editing the profile!
 */

if ( ! function_exists( 'fictioneer_admin_profile_moderation' ) ) {
  function fictioneer_admin_profile_moderation( $profile_user ) {
    // Setup
    $editing_user_is_admin = fictioneer_is_admin( get_current_user_id() );
    $editing_user_is_moderator = fictioneer_is_moderator( get_current_user_id() );

    // Abort conditions...
    if ( ! $editing_user_is_admin && ! $editing_user_is_moderator ) return;

    // Start HTML ---> ?>
    <tr class="user-moderation-flags-wrap">
      <th><?php _e( 'Moderation Flags', 'fictioneer' ) ?></th>
      <td>
        <fieldset>
          <div>
            <label for="fictioneer_admin_disable_avatar" class="checkbox-group">
              <input name="fictioneer_admin_disable_avatar" type="checkbox" id="fictioneer_admin_disable_avatar" <?php echo checked( 1, get_the_author_meta( 'fictioneer_admin_disable_avatar', $profile_user->ID ), false ); ?> value="1">
              <span><?php _e( 'Disable user avatar', 'fictioneer' ) ?></span>
            </label>
          </div>
          <div>
            <label for="fictioneer_admin_disable_reporting" class="checkbox-group">
              <input name="fictioneer_admin_disable_reporting" type="checkbox" id="fictioneer_admin_disable_reporting" <?php echo checked( 1, get_the_author_meta( 'fictioneer_admin_disable_reporting', $profile_user->ID ), false ); ?> value="1">
              <span><?php _e( 'Disable reporting capability', 'fictioneer' ) ?></span>
            </label>
          </div>
          <div>
            <label for="fictioneer_admin_disable_renaming" class="checkbox-group">
              <input name="fictioneer_admin_disable_renaming" type="checkbox" id="fictioneer_admin_disable_renaming" <?php echo checked( 1, get_the_author_meta( 'fictioneer_admin_disable_renaming', $profile_user->ID ), false ); ?> value="1">
              <span><?php _e( 'Disable renaming capability', 'fictioneer' ) ?></span>
            </label>
          </div>
          <div>
            <label for="fictioneer_admin_disable_commenting" class="checkbox-group">
              <input name="fictioneer_admin_disable_commenting" type="checkbox" id="fictioneer_admin_disable_commenting" <?php echo checked( 1, get_the_author_meta( 'fictioneer_admin_disable_commenting', $profile_user->ID ), false ); ?> value="1">
              <span><?php _e( 'Disable commenting capability', 'fictioneer' ) ?></span>
            </label>
          </div>
          <div>
            <label for="fictioneer_admin_disable_editing" class="checkbox-group">
              <input name="fictioneer_admin_disable_editing" type="checkbox" id="fictioneer_admin_disable_editing" <?php echo checked( 1, get_the_author_meta( 'fictioneer_admin_disable_editing', $profile_user->ID ), false ); ?> value="1">
              <span><?php _e( 'Disable comment editing capability', 'fictioneer' ) ?></span>
            </label>
          </div>
          <div>
            <label for="fictioneer_admin_disable_comment_notifications" class="checkbox-group">
              <input name="fictioneer_admin_disable_comment_notifications" type="checkbox" id="fictioneer_admin_disable_comment_notifications" <?php echo checked( 1, get_the_author_meta( 'fictioneer_admin_disable_comment_notifications', $profile_user->ID ), false ); ?> value="1">
              <span><?php _e( 'Disable comment reply notifications', 'fictioneer' ) ?></span>
            </label>
          </div>
          <div>
            <label for="fictioneer_admin_always_moderate_comments" class="checkbox-group">
              <input name="fictioneer_admin_always_moderate_comments" type="checkbox" id="fictioneer_admin_always_moderate_comments" <?php echo checked( 1, get_the_author_meta( 'fictioneer_admin_always_moderate_comments', $profile_user->ID ), false ); ?> value="1">
              <span><?php _e( 'Always hold comments for moderation', 'fictioneer' ) ?></span>
            </label>
          </div>
        </fieldset>
      </td>
    </tr>
    <tr class="user-moderation-message-wrap">
      <th><label for="fictioneer_admin_moderation_message"><?php _e( 'Moderation Message', 'fictioneer' ) ?></label></th>
      <td>
        <textarea name="fictioneer_admin_moderation_message" id="fictioneer_admin_moderation_message" rows="8" cols="30"><?php echo esc_attr( get_the_author_meta( 'fictioneer_admin_moderation_message', $profile_user->ID ) ); ?></textarea>
        <p class="description"><?php _e( 'Custom message in the user profile, which may be a nice thing as well.', 'fictioneer' ) ?></p>
      </td>
    </tr>
    <?php // <--- End HTML
  }
}
add_action( 'fictioneer_admin_user_sections', 'fictioneer_admin_profile_moderation', 10 );

// =============================================================================
// SHOW AUTHOR SECTION
// =============================================================================

/**
 * Adds HTML for the author section to the wp-admin user profile
 *
 * @since Fictioneer 5.0
 *
 * @param WP_User $profile_user The profile user object. Not necessarily the one
 *                              currently editing the profile!
 */

if ( ! function_exists( 'fictioneer_admin_profile_author' ) ) {
  function fictioneer_admin_profile_author( $profile_user ) {
    // Setup
    $is_owner = $profile_user->ID === get_current_user_id();
    $profile_user_is_author = fictioneer_is_author( $profile_user->ID );
    $editing_user_is_admin = fictioneer_is_admin( get_current_user_id() );

    // Abort conditions...
    if ( ! $profile_user_is_author ) return;
    if ( ! $is_owner && ! $editing_user_is_admin ) return;

    // Has pages?
    $profile_user_has_pages = get_pages(
      array(
        'authors' => $profile_user->ID,
        'number' => 1
      )
    );

    // Start HTML ---> ?>
    <tr class="user-author-page-wrap">
      <th><label for="fictioneer_author_page"><?php _e( 'Author Page', 'fictioneer' ) ?></label></th>
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
        <p class="description"><?php _e( 'Rendered inside your public author profile. This will override your biographical info.', 'fictioneer' ) ?></p>
      </td>
    </tr>
    <tr class="user-support-message-wrap">
      <th><label for="fictioneer_support_message"><?php _e( 'Support Message', 'fictioneer' ) ?></label></th>
      <td>
        <input name="fictioneer_support_message" type="text" id="fictioneer_support_message" value="<?php echo esc_attr( get_the_author_meta( 'fictioneer_support_message', $profile_user->ID ) ); ?>" class="regular-text">
        <p class="description"><?php _e( 'Rendered at the bottom of your chapters. This will override "You can support the author on".', 'fictioneer' ) ?></p>
      </td>
    </tr>
    <tr class="user-support-links-wrap">
      <th><?php _e( 'Support Links', 'fictioneer' ) ?></th>
      <td>
        <fieldset>
          <input name="fictioneer_user_patreon_link" type="text" id="fictioneer_user_patreon_link" value="<?php echo esc_attr( get_the_author_meta( 'fictioneer_user_patreon_link', $profile_user->ID ) ); ?>" class="regular-text">
          <p class="description"><?php _e( 'Patreon link', 'fictioneer' ) ?></p>
          <br>
          <input name="fictioneer_user_kofi_link" type="text" id="fictioneer_user_kofi_link" value="<?php echo esc_attr( get_the_author_meta( 'fictioneer_user_kofi_link', $profile_user->ID ) ); ?>" class="regular-text">
          <p class="description"><?php _e( 'Ko-Fi link', 'fictioneer' ) ?></p>
          <br>
          <input name="fictioneer_user_subscribestar_link" type="text" id="fictioneer_user_subscribestar_link" value="<?php echo esc_attr( get_the_author_meta( 'fictioneer_user_subscribestar_link', $profile_user->ID ) ); ?>" class="regular-text">
          <p class="description"><?php _e( 'SubscribeStar link', 'fictioneer' ) ?></p>
          <br>
          <input name="fictioneer_user_paypal_link" type="text" id="fictioneer_user_paypal_link" value="<?php echo esc_attr( get_the_author_meta( 'fictioneer_user_paypal_link', $profile_user->ID ) ); ?>" class="regular-text">
          <p class="description"><?php _e( 'PayPal link', 'fictioneer' ) ?></p>
          <br>
          <input name="fictioneer_user_donation_link" type="text" id="fictioneer_user_donation_link" value="<?php echo esc_attr( get_the_author_meta( 'fictioneer_user_donation_link', $profile_user->ID ) ); ?>" class="regular-text">
          <p class="description"><?php _e( 'Generic donation link', 'fictioneer' ) ?></p>
        </fieldset>
      </td>
    </tr>
    <?php // <--- End HTML
  }
}
add_action( 'fictioneer_admin_user_sections', 'fictioneer_admin_profile_author', 20 );

// =============================================================================
// SHOW OAUTH ID HASHES SECTION
// =============================================================================

/**
 * Adds HTML for the OAuth section to the wp-admin user profile
 *
 * @since Fictioneer 5.0
 *
 * @param WP_User $profile_user The profile user object. Not necessarily the one
 *                              currently editing the profile!
 */

if ( ! function_exists( 'fictioneer_admin_profile_oauth' ) ) {
  function fictioneer_admin_profile_oauth( $profile_user ) {
    // Setup
    $editing_user_is_admin = fictioneer_is_admin( get_current_user_id() );

    // Abort conditions...
    if ( ! $editing_user_is_admin || ! get_option( 'fictioneer_enable_oauth' ) ) return;

    // Start HTML ---> ?>
    <tr class="user-oauth-connections-wrap">
      <th><?php _e( 'Account Bindings', 'fictioneer' ) ?></th>
      <td>
        <fieldset>
          <input name="fictioneer_discord_id_hash" type="text" id="fictioneer_discord_id_hash" value="<?php echo esc_attr( get_the_author_meta( 'fictioneer_discord_id_hash', $profile_user->ID ) ); ?>" class="regular-text">
          <p class="description"><?php _e( 'Discord ID Hash', 'fictioneer' ) ?></p>
          <br>
          <input name="fictioneer_twitch_id_hash" type="text" id="fictioneer_twitch_id_hash" value="<?php echo esc_attr( get_the_author_meta( 'fictioneer_twitch_id_hash', $profile_user->ID ) ); ?>" class="regular-text">
          <p class="description"><?php _e( 'Twitch ID Hash', 'fictioneer' ) ?></p>
          <br>
          <input name="fictioneer_google_id_hash" type="text" id="fictioneer_google_id_hash" value="<?php echo esc_attr( get_the_author_meta( 'fictioneer_google_id_hash', $profile_user->ID ) ); ?>" class="regular-text">
          <p class="description"><?php _e( 'Google ID Hash', 'fictioneer' ) ?></p>
          <br>
          <input name="fictioneer_patreon_id_hash" type="text" id="fictioneer_patreon_id_hash" value="<?php echo esc_attr( get_the_author_meta( 'fictioneer_patreon_id_hash', $profile_user->ID ) ); ?>" class="regular-text">
          <p class="description"><?php _e( 'Patreon ID Hash', 'fictioneer' ) ?></p>
        </fieldset>
      </td>
    </tr>
    <?php // <--- End HTML
  }
}

if ( FICTIONEER_SHOW_OAUTH_HASHES ) {
  add_action( 'fictioneer_admin_user_sections', 'fictioneer_admin_profile_oauth', 30 );
}

// =============================================================================
// SHOW BADGE SECTION
// =============================================================================

/**
 * Adds HTML for the badge section to the wp-admin user profile
 *
 * @since Fictioneer 5.0
 *
 * @param WP_User $profile_user The profile user object. Not necessarily the one
 *                              currently editing the profile!
 */

if ( ! function_exists( 'fictioneer_admin_profile_badge' ) ) {
  function fictioneer_admin_profile_badge( $profile_user ) {
    // Setup
    $editing_user_is_admin = fictioneer_is_admin( get_current_user_id() );

    // Abort conditions...
    if ( ! $editing_user_is_admin ) return;

    // Start HTML ---> ?>
    <tr class="user-badge-override-wrap">
      <th><label for="fictioneer_badge_override"><?php _e( 'Badge Override', 'fictioneer' ) ?></label></th>
      <td>
        <input name="fictioneer_badge_override" type="text" id="fictioneer_badge_override" value="<?php echo esc_attr( get_the_author_meta( 'fictioneer_badge_override', $profile_user->ID ) ); ?>" class="regular-text">
        <p class="description"><?php _e( 'Override the user’s badge.', 'fictioneer' ) ?></p>
      </td>
    </tr>
    <?php // <--- End HTML
  }
}
add_action( 'fictioneer_admin_user_sections', 'fictioneer_admin_profile_badge', 40 );

// =============================================================================
// SHOW EXTERNAL AVATAR SECTION
// =============================================================================

/**
 * Adds HTML for the external avatar section to the wp-admin user profile
 *
 * @since Fictioneer 5.0
 *
 * @param WP_User $profile_user The profile user object. Not necessarily the one
 *                              currently editing the profile!
 */

if ( ! function_exists( 'fictioneer_admin_profile_external_avatar' ) ) {
  function fictioneer_admin_profile_external_avatar( $profile_user ) {
    // Setup
    $editing_user_is_admin = fictioneer_is_admin( get_current_user_id() );

    // Abort conditions...
    if ( ! $editing_user_is_admin ) return;

    // Start HTML ---> ?>
    <tr class="user-external-avatar-wrap">
      <th><label for="fictioneer_external_avatar_url"><?php _e( 'External Avatar URL', 'fictioneer' ) ?></label></th>
      <td>
        <input name="fictioneer_external_avatar_url" type="text" id="fictioneer_external_avatar_url" value="<?php echo esc_attr( get_the_author_meta( 'fictioneer_external_avatar_url', $profile_user->ID ) ); ?>" class="regular-text">
        <p class="description"><?php _e( 'There is no guarantee this URL remains valid!', 'fictioneer' ) ?></p>
      </td>
    </tr>
    <?php // <--- End HTML
  }
}
add_action( 'fictioneer_admin_user_sections', 'fictioneer_admin_profile_external_avatar', 50 );

// =============================================================================
// SHOW PATREON TIERS
// =============================================================================

/**
 * Adds HTML for the Patreon tiers section to the wp-admin user profile
 *
 * DISABLED!
 *
 * @since Fictioneer 5.0
 *
 * @param WP_User $profile_user The profile user object. Not necessarily the one
 *                              currently editing the profile!
 */

if ( ! function_exists( 'fictioneer_admin_profile_patreon_tiers' ) ) {
  function fictioneer_admin_profile_patreon_tiers( $profile_user ) {
    // Setup
    $editing_user_is_admin = fictioneer_is_admin( get_current_user_id() );

    // Abort conditions...
    if ( ! $editing_user_is_admin ) return;

    // Start HTML ---> ?>
    <tr class="user-patreon-tiers-wrap">
      <th><label for="fictioneer_patreon_tiers"><?php _e( 'Patreon Tiers', 'fictioneer' ) ?></label></th>
      <td>
        <input name="fictioneer_patreon_tiers" type="text" id="fictioneer_patreon_tiers" value="<?php echo esc_attr( json_encode( get_the_author_meta( 'fictioneer_patreon_tiers', $profile_user->ID ) ) ); ?>" class="regular-text" readonly>
        <p class="description"><?php _e( 'Patreon tiers for the linked Patreon client. Valid for two weeks after last login.', 'fictioneer' ) ?></p>
      </td>
    </tr>
    <?php // <--- End HTML
  }
}
// add_action( 'fictioneer_admin_user_sections', 'fictioneer_admin_profile_patreon_tiers', 60 );

// =============================================================================
// SHOW BOOKMARKS SECTION
// =============================================================================

/**
 * Adds HTML for the bookmarks section to the wp-admin user profile
 *
 * DISABLED!
 *
 * @since Fictioneer 5.0
 *
 * @param WP_User $profile_user The profile user object. Not necessarily the one
 *                              currently editing the profile!
 */

if ( ! function_exists( 'fictioneer_admin_profile_bookmarks' ) ) {
  function fictioneer_admin_profile_bookmarks( $profile_user ) {
    // Setup
    $editing_user_is_admin = fictioneer_is_admin( get_current_user_id() );

    // Abort conditions...
    if ( ! $editing_user_is_admin ) return;

    // Start HTML ---> ?>
    <tr class="user-bookmarks-wrap">
      <th><label for="user_bookmarks"><?php _e( 'Bookmarks JSON', 'fictioneer' ) ?></label></th>
      <td>
        <textarea name="user_bookmarks" id="user_bookmarks" rows="12" cols="30" style="font-family: monospace; font-size: 12px; word-break: break-all;" disabled><?php echo esc_attr( get_the_author_meta( 'fictioneer_bookmarks', $profile_user->ID ) ); ?></textarea>
      </td>
    </tr>
    <?php // <--- End HTML
  }
}
// add_action( 'fictioneer_admin_user_sections', 'fictioneer_admin_profile_bookmarks', 70 );

?>
