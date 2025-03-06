<?php

// =============================================================================
// LOG POST UPDATES HELPER
// =============================================================================

/**
 * Logs post update
 *
 * @since 5.0.0
 * @since 5.24.1 - Make dependant on option.
 *
 * @param int    $post_id  The post ID.
 * @param string $action   The action performed.
 */

function fictioneer_log_post_update( $post_id, $action ) {
  // Log posts?
  if ( ! get_option( 'fictioneer_log_posts' ) ) {
    return;
  }

  // Setup
  $type_object = get_post_type_object( get_post_type( $post_id ) );
  $post_type_name = $type_object->labels->singular_name;

  // Build message
  $message = sprintf(
    _x( '%1$s #%2$s %3$s: %4$s', 'Pattern for post/page in logs: {Type} #{ID} {Action}: {Title}.', 'fictioneer' ),
    $post_type_name,
    $post_id,
    $action,
    fictioneer_get_safe_title( $post_id, 'admin-log-post-update' )
  );

  // Log
  fictioneer_log( $message );
}

// =============================================================================
// LOG CHECKBOX OPTION UPDATES
// =============================================================================

/**
 * Logs update of checkbox option
 *
 * @since 5.0.0
 *
 * @param string        $option    Name of the option.
 * @param boolean|null $old_value  The previous value or null if not set.
 * @param boolean      $value      The new value.
 */

function fictioneer_log_checkbox_update( $option, $old_value, $value ) {
  // Abort if...
  if ( ! is_null( $old_value ) && $old_value === $value ) {
    return;
  }

  // Build message
  $update = sprintf(
    _x( '%1$s: %2$s', 'Pattern for checkbox option in logs.', 'fictioneer' ),
    $value ? _x( 'Checked', 'State of checkbox option in logs.', 'fictioneer' ) : _x( 'Unchecked', 'State of checkbox option in logs.', 'fictioneer' ),
    $option
  );

  // Log
  fictioneer_log( $update );
}

/**
 * Helper to log added theme boolean option
 *
 * @since 5.0.0
 *
 * @param string  $option  Name of the option.
 * @param boolean $value   The new value.
 */

function fictioneer_settings_checkbox_added( $option, $value ) {
  // Abort if...
  if ( ! in_array( $option, array_keys( FICTIONEER_OPTIONS['booleans'] ) ) ) {
    return;
  }

  // Clear cached files
  fictioneer_clear_all_cached_partials();

  // Relay
  fictioneer_log_checkbox_update(
    fictioneer_get_option_label( $option ),
    null,
    $value
  );
}
add_action( 'added_option', 'fictioneer_settings_checkbox_added', 10, 2 );

/**
 * Helper to log updated theme boolean option
 *
 * @since 5.0.0
 *
 * @param string  $option     Name of the option.
 * @param boolean $old_value  The previous value.
 * @param boolean $value      The new value.
 */

function fictioneer_settings_checkbox_updated( $option, $old_value, $value ) {
  // Abort if...
  if ( ! in_array( $option, array_keys( FICTIONEER_OPTIONS['booleans'] ) ) ) {
    return;
  }

  // Clear cached files
  fictioneer_clear_all_cached_partials();

  // Purge theme caches
  static $purged = null;

  if ( ! $purged ) {
    $cache_purge_options = ['fictioneer_hide_chapter_icons', 'fictioneer_enable_chapter_groups',
      'fictioneer_collapse_groups_by_default', 'fictioneer_disable_chapter_collapsing',
      'fictioneer_count_characters_as_words', 'fictioneer_override_chapter_status_icons',
      'fictioneer_enable_ffcnr_auth', 'fictioneer_show_scheduled_chapters'];

    if ( in_array( $option, $cache_purge_options ) ) {
      fictioneer_purge_theme_caches();
      $purged = true;
    }
  }

  if ( $option === 'fictioneer_hide_large_card_chapter_list' ) {
    fictioneer_purge_story_card_cache();
  }

  // Relay
  fictioneer_log_checkbox_update(
    fictioneer_get_option_label( $option ),
    $old_value,
    $value
  );
}
add_action( 'updated_option', 'fictioneer_settings_checkbox_updated', 10, 3 );

// =============================================================================
// LOG PAGE ASSIGNMENT OPTION UPDATES
// =============================================================================

define(
  'FICTIONEER_PAGE_ASSIGNMENTS',
  array(
    'fictioneer_user_profile_page',
    'fictioneer_bookmarks_page',
    'fictioneer_stories_page',
    'fictioneer_chapters_page',
    'fictioneer_recommendations_page',
    'fictioneer_collections_page',
    'fictioneer_bookshelf_page',
    'fictioneer_404_page',
    'fictioneer_authors_page'
  )
);

/**
 * Logs update of page assignment
 *
 * @since 5.0.0
 *
 * @param string   $option     Name of the option.
 * @param int|null $old_value  The previous value or null if not set.
 * @param int      $value      The new value.
 */

function fictioneer_log_page_assignment_update( $option, $old_value, $value ) {
  // Abort if...
  if ( ! is_null( $old_value ) && $old_value === $value ) {
    return;
  }

  // Build message
  $message = sprintf(
    _x(
      '%1$s changed from "%2$s" to "%3$s"',
      'Pattern for page assignment updates in logs.',
      'fictioneer'
    ),
    $option,
    $old_value ?? 'NULL',
    $value
  );

  // Log
  fictioneer_log( $message );
}

/**
 * Helper to log added page assignment option
 *
 * @since 5.0.0
 *
 * @param string $option  Name of the option.
 * @param int    $value   The new value.
 */

function fictioneer_settings_page_assignment_added( $option, $value ) {
  // Abort if...
  if ( ! in_array( $option, FICTIONEER_PAGE_ASSIGNMENTS ) ) {
    return;
  }

  // Relay
  fictioneer_log_page_assignment_update(
    fictioneer_get_option_label( $option ),
    null,
    $value
  );
}
add_action( 'added_option', 'fictioneer_settings_page_assignment_added', 10, 2 );

/**
 * Helper to log updated page assignment option
 *
 * @since 5.0.0
 *
 * @param string $option     Name of the option.
 * @param int    $old_value  The previous value.
 * @param int    $value      The new value.
 */

function fictioneer_settings_page_assignment_updated( $option, $old_value, $value ) {
  // Abort if...
  if ( ! in_array( $option, FICTIONEER_PAGE_ASSIGNMENTS ) ) {
    return;
  }

  // Relay
  fictioneer_log_page_assignment_update(
    fictioneer_get_option_label( $option ),
    $old_value,
    $value
  );
}
add_action( 'updated_option', 'fictioneer_settings_page_assignment_updated', 10, 3 );

// =============================================================================
// LOG PHRASE UPDATES
// =============================================================================

define(
  'FICTIONEER_PHRASES',
  array(
    'fictioneer_phrase_maintenance',
    'fictioneer_phrase_login_modal',
    'fictioneer_phrase_cookie_consent_banner',
    'fictioneer_phrase_comment_reply_notification',
    'fictioneer_phrase_site_age_confirmation',
    'fictioneer_phrase_post_age_confirmation'
  )
);

/**
 * Logs update of phrase option
 *
 * @since 5.0.0
 *
 * @param string      $option     Name of the option.
 * @param string|null $old_value  The previous value or null if not set.
 * @param string      $value      The new value.
 */

function fictioneer_log_phrase_update( $option, $old_value, $value ) {
  // Abort if...
  if ( ! is_null( $old_value ) && $old_value === $value ) {
    return;
  }

  // Build message
  $message = sprintf(
    _x(
      '%1$s phrase changed to "%2$s"',
      'Pattern for phrase updates in logs.',
      'fictioneer'
    ),
    $option,
    preg_replace( '/\s+/', ' ', $value )
  );

  // Log
  fictioneer_log( $message );
}

/**
 * Helper to log added phrase option
 *
 * @since 5.0.0
 *
 * @param string $option  Name of the option.
 * @param string $value   The new value.
 */

function fictioneer_settings_phrase_added( $option, $value ) {
  // Abort if...
  if ( ! in_array( $option, FICTIONEER_PHRASES ) ) {
    return;
  }

  // Clear cached files
  fictioneer_clear_all_cached_partials();

  // Relay
  fictioneer_log_phrase_update(
    fictioneer_get_option_label( $option ),
    null,
    $value
  );
}
add_action( 'added_option', 'fictioneer_settings_phrase_added', 10, 2 );

/**
 * Helper to log updated phrase option
 *
 * @since 5.0.0
 *
 * @param string $option     Name of the option.
 * @param string $old_value  The previous value.
 * @param string $value      The new value.
 */

function fictioneer_settings_phrase_updated( $option, $old_value, $value ) {
  // Abort if...
  if ( ! in_array( $option, FICTIONEER_PHRASES ) ) {
    return;
  }

  // Clear cached files
  fictioneer_clear_all_cached_partials();

  // Replay
  fictioneer_log_phrase_update(
    fictioneer_get_option_label( $option ),
    $old_value,
    $value
  );
}
add_action( 'updated_option', 'fictioneer_settings_phrase_updated', 10, 3 );

// =============================================================================
// LOG CONNECTION UPDATES
// =============================================================================

define(
  'FICTIONEER_CONNECTIONS',
  array(
    'fictioneer_discord_client_id',
    'fictioneer_discord_client_secret',
    'fictioneer_discord_invite_link',
    'fictioneer_discord_channel_comments_webhook',
    'fictioneer_twitch_client_id',
    'fictioneer_twitch_client_secret',
    'fictioneer_google_client_id',
    'fictioneer_google_client_secret',
    'fictioneer_patreon_client_id',
    'fictioneer_patreon_client_secret',
    'fictioneer_patreon_global_lock_tiers',
    'fictioneer_patreon_global_lock_amount',
    'fictioneer_patreon_global_lock_unlock_amount'
  )
);

/**
 * Logs update of connection option
 *
 * @since 5.0.0
 *
 * @param string   $connection  Name of the connection option.
 * @param int|null $old_value   ID of the old page or null if unset.
 * @param int      $value       ID of the new page.
 */

function fictioneer_log_connection_update( $connection, $old_value, $value ) {
  // Abort if...
  if ( ! is_null( $old_value ) && $old_value === $value ) {
    return;
  }

  // Build message
  $message = sprintf(
    _x(
      '%s updated.',
      'Pattern for connection updates in logs.',
      'fictioneer'
    ),
    $connection
  );

  // Log
  fictioneer_log( $message );
}

/**
 * Helper to log added connection option
 *
 * @since 5.0.0
 *
 * @param string $option  Name of the option.
 * @param string $value   The new value.
 */

function fictioneer_settings_connection_added( $option, $value ) {
  // Abort if...
  if ( ! in_array( $option, FICTIONEER_CONNECTIONS ) ) {
    return;
  }

  // Relay
  fictioneer_log_connection_update(
    fictioneer_get_option_label( $option ),
    null,
    $value
  );
}
add_action( 'added_option', 'fictioneer_settings_connection_added', 10, 2 );

/**
 * Helper to log updated connection option
 *
 * @since 5.0.0
 *
 * @param string $option     Name of the option.
 * @param string $old_value  The previous value.
 * @param string $value      The new value.
 */

function fictioneer_settings_connection_updated( $option, $old_value, $value ) {
  // Abort if...
  if ( ! in_array( $option, FICTIONEER_CONNECTIONS ) ) {
    return;
  }

  // Relay
  fictioneer_log_connection_update(
    fictioneer_get_option_label( $option ),
    $old_value,
    $value
  );
}
add_action( 'updated_option', 'fictioneer_settings_connection_updated', 10, 3 );

// =============================================================================
// LOG TRASHED/RESTORED POSTS
// =============================================================================

add_action( 'trashed_post', function( $post_id ) {
  // Relay
  fictioneer_log_post_update( $post_id, __( 'trashed', 'fictioneer' ) );
});

add_action( 'untrashed_post', function( $post_id ) {
  // Relay
  fictioneer_log_post_update( $post_id, __( 'restored', 'fictioneer' ) );
});

// =============================================================================
// LOG PUBLISHED POSTS
// =============================================================================

/**
 * Helper to log published posts
 *
 * @since 5.0.0
 *
 * @param int $post_id  The post ID.
 */

function fictioneer_log_published_posts( $post_id ) {
  // Prevent multi-fire
  if ( fictioneer_multi_save_guard( $post_id ) ) {
    return;
  }

  // Updated or first published?
  if ( strtotime( '-5 seconds' ) < strtotime( get_the_date( 'c', $post_id ) ) ) {
    $action = __( 'published', 'fictioneer' );
  } else {
    $action = __( 'updated', 'fictioneer' );
  }

  // Relay
  fictioneer_log_post_update( $post_id, $action );
}
add_action( 'publish_post', 'fictioneer_log_published_posts' );
add_action( 'publish_page', 'fictioneer_log_published_posts' );
add_action( 'publish_fcn_story', 'fictioneer_log_published_posts' );
add_action( 'publish_fcn_chapter', 'fictioneer_log_published_posts' );
add_action( 'publish_fcn_collection', 'fictioneer_log_published_posts' );
add_action( 'publish_fcn_recommendation', 'fictioneer_log_published_posts' );

// =============================================================================
// LOG PENDING POSTS
// =============================================================================

/**
 * Helper to log pending posts
 *
 * @since 5.0.0
 *
 * @param int $post_id  The post ID.
 */

function fictioneer_log_pending_posts( $post_id ) {
  // Prevent multi-fire
  if ( fictioneer_multi_save_guard( $post_id ) ) {
    return;
  }

  // Relay
  fictioneer_log_post_update( $post_id, __( 'submitted for review', 'fictioneer' ) );
}

add_action( 'pending_post', 'fictioneer_log_pending_posts' );
add_action( 'pending_page', 'fictioneer_log_pending_posts' );
add_action( 'pending_fcn_story', 'fictioneer_log_published_posts' );
add_action( 'pending_fcn_chapter', 'fictioneer_log_published_posts' );
add_action( 'pending_fcn_collection', 'fictioneer_log_published_posts' );
add_action( 'pending_fcn_recommendation', 'fictioneer_log_published_posts' );

// =============================================================================
// LOG DELETED POSTS
// =============================================================================

/**
 * Helper to log deleted posts
 *
 * @since 5.0.0
 *
 * @param int $post_id  The post ID.
 */

function fictioneer_log_deleted_posts( $post_id ) {
  // Prevent multi-fire
  if (
    ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ||
    did_action( 'before_delete_post' ) != 1
  ) {
    return;
  }

  // Setup
  $post_type = get_post_type( $post_id );
  $allowed_post_types = ['fcn_story', 'fcn_chapter', 'fcn_recommendation', 'fcn_collection', 'post', 'page'];

  // Only log certain post types
  if ( ! in_array( $post_type, $allowed_post_types ) ) {
    return;
  }

  // Relay
  fictioneer_log_post_update( $post_id, __( 'deleted', 'fictioneer' ) );
}
add_action( 'before_delete_post', 'fictioneer_log_deleted_posts' );
