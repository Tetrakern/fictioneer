<?php

// =============================================================================
// LOGS
// =============================================================================

/**
 * Log a message to the theme log file
 *
 * @since Fictioneer 5.0
 *
 * @param string 	 		 $message  What has been updated
 * @param WP_User|null $user	   The user who did it. Defaults to current user.
 */

function fictioneer_log( $message, $current_user = null ) {
	// Setup
  $current_user = $current_user ?? wp_get_current_user();
  $username = _x( 'System', 'Default name in logs.', 'fictioneer' );
  $log_file = WP_CONTENT_DIR . '/fictioneer-logs.log';
  $log_limit = 5000;
  $date = date( 'Y-m-d H:i:s' );

  if ( is_object( $current_user ) && $current_user->ID > 0 ) {
    $username = $current_user->user_login . ' #' . $current_user->ID;
  }

  if ( empty( $current_user ) && wp_doing_cron() ) {
    $username = 'WP Cron';
  }

  if ( empty( $current_user ) && wp_doing_ajax() ) {
    $username = 'AJAX';
  }

  $username = empty( $username ) ? __( 'Anonymous', 'fictioneer' ) : $username;

  // Make sure the log file exists
  if ( ! file_exists( $log_file ) ) {
    file_put_contents( $log_file, '' );
  }

  // Read
  $log_contents = file_get_contents( $log_file );

  // Parse
  $log_entries = explode( "\n", $log_contents );

  // Limit (if too large)
  $log_entries = array_slice( $log_entries, -($log_limit + 1) );

  // Add new entry
  $log_entries[] = "[{$date}] [{$username}] $message";

  // Concatenate and save
  file_put_contents( $log_file, implode( "\n", $log_entries ) );
}

/**
 * Retrieves the log entries and returns an HTML representation
 *
 * @return string The HTML representation of the log entries.
 */

function fictioneer_get_log() {
  // Setup
  $log_file = WP_CONTENT_DIR . '/fictioneer-logs.log';
  $output = '';

  // Check whether log file exists
  if ( ! file_exists( $log_file ) ) {
    return '<ul class="fictioneer-log"><li>No log entries yet.</li></ul>';
  }

  // Read
  $log_contents = file_get_contents( $log_file );

  // Parse
  $log_entries = explode( "\n", $log_contents );

  // Limit display to 250
  $log_entries = array_slice( $log_entries, -250 );

  // Reverse
  $log_entries = array_reverse( $log_entries );

  // Build list items
  foreach ( $log_entries as $entry ) {
    $output .= '<li class="fictioneer-log__item">' . $entry . '</li>';
  }

  // Return HTML
  return '<ul class="fictioneer-log">' . $output . '</ul>';
}

// =============================================================================
// LOG POST UPDATES HELPER
// =============================================================================

/**
 * Log post update
 *
 * @since Fictioneer 5.0
 *
 * @param int 	 $post_id  The post ID.
 * @param string $action 	 The action performed.
 */

function fictioneer_log_post_update( $post_id, $action ) {
  // Setup
  $type_object = get_post_type_object( get_post_type( $post_id ) );
  $post_type_name = $type_object->labels->singular_name;

  // Build message
  $message = sprintf(
    _x( '%1$s #%2$s %3$s: %4$s', 'Pattern for post/page in logs: "{Type} #{ID} {Action}: {Title}".', 'fictioneer' ),
    $post_type_name,
    $post_id,
    $action,
    fictioneer_get_safe_title( $post_id )
  );

	// Log
	fictioneer_log( $message );
}

// =============================================================================
// LOG CHECKBOX OPTION UPDATES
// =============================================================================

/**
 * Log update of checkbox option
 *
 * @since Fictioneer 5.0
 *
 * @param string 	     $option 	   Name of the option.
 * @param boolean|null $old_value  The previous value or null if not set.
 * @param boolean      $value 		 The new value.
 */

function fictioneer_log_checkbox_update( $option, $old_value, $value ) {
	// Abort if...
	if ( ! is_null( $old_value ) && $old_value === $value ) return;

	// Build message
  $update = sprintf(
    _x( '%1$s: %2$s', 'Pattern for checkbox option in logs: "{Checked} {Option}".', 'fictioneer' ),
    $value ? _x( 'Checked', 'State of checkbox option in logs.', 'fictioneer' ) : _x( 'Unchecked', 'State of checkbox option in logs.', 'fictioneer' ),
    $option
  );

	// Log
	fictioneer_log( $update );
}

/**
 * Helper to log added theme boolean option
 *
 * @since Fictioneer 5.0
 *
 * @param string 	$option  Name of the option.
 * @param boolean $value   The new value.
 */

function fictioneer_settings_checkbox_added( $option, $value ) {
  // Abort if...
  if ( ! in_array( $option, array_keys( FICTIONEER_OPTIONS['booleans'] ) ) ) return;

  // Relay
  fictioneer_log_checkbox_update(
    FICTIONEER_OPTIONS['booleans'][ $option ]['label'],
    null,
    $value
  );
}
add_action( 'added_option', 'fictioneer_settings_checkbox_added', 10, 2 );

/**
 * Helper to log updated theme boolean option
 *
 * @since Fictioneer 5.0
 *
 * @param string 	$option 	  Name of the option.
 * @param boolean $old_value  The previous value.
 * @param boolean $value 		  The new value.
 */

function fictioneer_settings_checkbox_updated( $option, $old_value, $value ) {
  // Abort if...
  if ( ! in_array( $option, array_keys( FICTIONEER_OPTIONS['booleans'] ) ) ) return;

  // Relay
  fictioneer_log_checkbox_update(
    FICTIONEER_OPTIONS['booleans'][ $option ]['label'],
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
    'fictioneer_bookshelf_page',
    'fictioneer_404_page'
  )
);

/**
 * Log update of page assignment
 *
 * @since Fictioneer 5.0
 *
 * @param string 	 $option 	   Name of the option.
 * @param int|null $old_value  The previous value or null if not set.
 * @param int      $value 		 The new value.
 */

function fictioneer_log_page_assignment_update( $option, $old_value, $value ) {
	// Abort if...
	if ( ! is_null( $old_value ) && $old_value === $value ) return;

	// Build message
  $message = sprintf(
    _x(
      '%1$s page assignment changed from #%2$s to #%3$s',
      'Pattern for page assignment updates in logs: "{Option} page assignment changed from {Old ID} to {New ID}.".',
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
 * @since Fictioneer 5.0
 *
 * @param string $option  Name of the option.
 * @param int    $value   The new value.
 */

function fictioneer_settings_page_assignment_added( $option, $value ) {
  // Abort if...
  if ( ! in_array( $option, FICTIONEER_PAGE_ASSIGNMENTS ) ) return;

  // Relay
  fictioneer_log_page_assignment_update(
    FICTIONEER_OPTIONS['integers'][ $option ]['label'],
    null,
    $value
  );
}
add_action( 'added_option', 'fictioneer_settings_page_assignment_added', 10, 2 );

/**
 * Helper to log updated page assignment option
 *
 * @since Fictioneer 5.0
 *
 * @param string $option 	   Name of the option.
 * @param int    $old_value  The previous value.
 * @param int    $value 		 The new value.
 */

function fictioneer_settings_page_assignment_updated( $option, $old_value, $value ) {
  // Abort if...
  if ( ! in_array( $option, FICTIONEER_PAGE_ASSIGNMENTS ) ) return;

  // Relay
  fictioneer_log_page_assignment_update(
    FICTIONEER_OPTIONS['integers'][ $option ]['label'],
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
    'fictioneer_phrase_comment_reply_notification'
  )
);

/**
 * Log update of phrase option
 *
 * @since Fictioneer 5.0
 *
 * @param string 	    $option 	  Name of the option.
 * @param string|null $old_value  The previous value or null if not set.
 * @param string      $value 		  The new value.
 */

function fictioneer_log_phrase_update( $option, $old_value, $value ) {
	// Abort if...
  if ( ! is_null( $old_value ) && $old_value === $value ) return;

	// Build message
  $message = sprintf(
    _x(
      'Phrase for the %1$s changed to \'%2$s\'',
      'Pattern for phrase updates in logs: "Phrase for the {Option} changed to: \'{New String}\'".',
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
 * @since Fictioneer 5.0
 *
 * @param string $option  Name of the option.
 * @param string $value   The new value.
 */

function fictioneer_settings_phrase_added( $option, $value ) {
  // Abort if...
  if ( ! in_array( $option, FICTIONEER_PHRASES ) ) return;

  // Relay
  fictioneer_log_phrase_update(
    FICTIONEER_OPTIONS['strings'][ $option ]['label'],
    null,
    $value
  );
}
add_action( 'added_option', 'fictioneer_settings_phrase_added', 10, 2 );

/**
 * Helper to log updated phrase option
 *
 * @since Fictioneer 5.0
 *
 * @param string $option 	   Name of the option.
 * @param string $old_value  The previous value.
 * @param string $value 		 The new value.
 */

function fictioneer_settings_phrase_updated( $option, $old_value, $value ) {
  // Abort if...
  if ( ! in_array( $option, FICTIONEER_PHRASES ) ) return;

  // Replay
  fictioneer_log_phrase_update(
    FICTIONEER_OPTIONS['strings'][ $option ]['label'],
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
    'fictioneer_patreon_client_secret'
  )
);

/**
 * Log update of connection option
 *
 * @since Fictioneer 5.0
 *
 * @param string 	 $connection  Name of the connection option.
 * @param int|null $old_value   ID of the old page or null if unset.
 * @param int			 $value 		  ID of the new page.
 */

function fictioneer_log_connection_update( $connection, $old_value, $value ) {
	// Abort if...
  if ( ! is_null( $old_value ) && $old_value === $value ) return;

	// Build message
  $message = sprintf(
    _x(
      '%s updated.',
      'Pattern for connection updates in logs: "{Option} updated".',
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
 * @since Fictioneer 5.0
 *
 * @param string $option  Name of the option.
 * @param string $value   The new value.
 */

function fictioneer_settings_connection_added( $option, $value ) {
  // Abort if...
  if ( ! in_array( $option, FICTIONEER_CONNECTIONS ) ) return;

  // Relay
  fictioneer_log_connection_update(
    FICTIONEER_OPTIONS['strings'][ $option ]['label'],
    null,
    $value
  );
}
add_action( 'added_option', 'fictioneer_settings_connection_added', 10, 2 );

/**
 * Helper to log updated connection option
 *
 * @since Fictioneer 5.0
 *
 * @param string $option 	   Name of the option.
 * @param string $old_value  The previous value.
 * @param string $value 		 The new value.
 */

function fictioneer_settings_connection_updated( $option, $old_value, $value ) {
  // Abort if...
  if ( ! in_array( $option, FICTIONEER_CONNECTIONS ) ) return;

  // Relay
  fictioneer_log_connection_update(
    FICTIONEER_OPTIONS['strings'][ $option ]['label'],
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
 * @since Fictioneer 5.0
 * @see fictioneer_log_post_update()
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
 * @since Fictioneer 5.0
 * @see fictioneer_log_post_update()
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
 * @since Fictioneer 5.0
 * @see fictioneer_log_post_update()
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
  if ( ! in_array( $post_type, $allowed_post_types ) ) return;

  // Relay
  fictioneer_log_post_update( $post_id, __( 'deleted', 'fictioneer' ) );
}
add_action( 'before_delete_post', 'fictioneer_log_deleted_posts' );

?>
