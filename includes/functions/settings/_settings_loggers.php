<?php

// =============================================================================
// UPDATE LOG HELPER
// =============================================================================

/**
 * Updates the Fictioneer log
 *
 * @since Fictioneer 5.0
 *
 * @param string 	 		 $update What has been updated
 * @param WP_User|null $user	 The user who did it. Defaults to current user.
 */

function fictioneer_update_log( $update, $user = null ) {
	// Setup
	$fictioneer_log = get_option( 'fictioneer_log' );
	$date = date( 'Y/m/d - G:i:s' );
	$user = $user ? $user : wp_get_current_user();

	if ( is_object( $user ) && $user->ID > 0 ) {
		$user_note = $user->user_login . ' #' . $user->ID;
	} elseif ( is_string( $user ) ) {
		$user_note = $user;
	} else {
    $user_note = _x( 'SYS', 'Short for system.', 'fictioneer' );
  }

	// Prepare
  $update = sprintf(
    _x( '[%1$s] %2$s (%3$s)', 'Pattern for the log entries: "[time] action (user)".', 'fictioneer' ),
    $date,
    $update,
    $user_note
  );
	$update .= "\n\n";

	// Prepend
	$fictioneer_log = $update . $fictioneer_log;

	// Limit lines to 1024
	$lines = explode( "\n\n", $fictioneer_log );
	$lines = array_slice( $lines, 0, 1024 ) ;
	$fictioneer_log = implode( "\n\n", $lines );

	// Update log
	update_option( 'fictioneer_log', $fictioneer_log );
}

// =============================================================================
// LOG POST UPDATES HELPER
// =============================================================================

/**
 * Log post update
 *
 * @since Fictioneer 5.0
 *
 * @param int 	 $post_id The post ID.
 * @param string $action 	The action performed.
 */

function fictioneer_log_post_update( $post_id, $action ) {
  // Setup
  $post_type_name = __( 'post', 'fictioneer' );

  switch( get_post_type( $post_id ) ) {
    case 'fcn_chapter':
      $post_type_name = __( 'chapter', 'fictioneer' );
      break;
    case 'fcn_story':
      $post_type_name = __( 'story', 'fictioneer' );
      break;
    case 'fcn_collection':
      $post_type_name = __( 'collection', 'fictioneer' );
      break;
    case 'fcn_recommendation':
      $post_type_name = __( 'recommendation', 'fictioneer' );
      break;
    case 'page':
      $post_type_name = __( 'page', 'fictioneer' );
      break;
    default:
      $post_type_name = __( 'post', 'fictioneer' );
  }

  $update = sprintf(
    _x( '%1$s #%2$s %3$s: %4$s', 'Pattern for post/page update logs: "type #ID action: title".', 'fictioneer' ),
    ucfirst( $post_type_name ),
    $post_id,
    $action,
    fictioneer_get_safe_title( $post_id )
  );

	// Update
	fictioneer_update_log( $update );
}

// =============================================================================
// LOG CHECKBOX OPTION UPDATES
// =============================================================================

/**
 * Log update of checkbox option
 *
 * @since Fictioneer 5.0
 *
 * @param string 	     $option 	  Name of the option.
 * @param boolean|null $old_value The previous value or null if not set.
 * @param boolean      $value 		The new value.
 */

function fictioneer_log_checkbox_update( $option, $old_value, $value ) {
	// Abort if...
	if ( ! is_null( $old_value ) && $old_value === $value ) return;

	// Setup
	$action = $value ? __( 'Checked', 'fictioneer' ) : __( 'Unchecked', 'fictioneer' );
  $update = sprintf(
    __( '%1$s: %2$s', 'fictioneer' ),
    $action,
    $option
  );

	// Update
	fictioneer_update_log( $update );
}

/**
 * Helper to log added boolean option
 *
 * @since Fictioneer 5.0
 *
 * @param string 	$option Name of the option.
 * @param boolean $value  The new value.
 */

function fictioneer_settings_checkbox_added( $option, $value ) {
  // Abort if...
  if ( ! in_array( $option, array_keys( FICTIONEER_OPTIONS['booleans'] ) ) ) return;

  // Log
  $description = FICTIONEER_OPTIONS['booleans'][ $option ]['label'];
  fictioneer_log_checkbox_update( $description, null, $value );
}
add_action( 'added_option', 'fictioneer_settings_checkbox_added', 10, 2 );

/**
 * Helper to log updated boolean option
 *
 * @since Fictioneer 5.0
 *
 * @param string 	$option 	 Name of the option.
 * @param boolean $old_value The previous value.
 * @param boolean $value 		 The new value.
 */

function fictioneer_settings_checkbox_updated( $option, $old_value, $value ) {
  // Abort if...
  if ( ! in_array( $option, array_keys( FICTIONEER_OPTIONS['booleans'] ) ) ) return;

  // Log
  $description = FICTIONEER_OPTIONS['booleans'][ $option ]['label'];
  fictioneer_log_checkbox_update( $description, $old_value, $value );
}
add_action( 'updated_option', 'fictioneer_settings_checkbox_updated', 10, 3 );

// =============================================================================
// LOG PAGE ASSIGNMENT OPTION UPDATES
// =============================================================================

define( 'FICTIONEER_PAGE_ASSIGNMENTS', array(
  'fictioneer_user_profile_page',
  'fictioneer_bookmarks_page',
  'fictioneer_stories_page',
  'fictioneer_chapters_page',
  'fictioneer_recommendations_page',
  'fictioneer_bookshelf_page',
  'fictioneer_404_page'
));

/**
 * Log update of page assignment
 *
 * @since Fictioneer 5.0
 *
 * @param string 	 $option 	  Name of the option.
 * @param int|null $old_value The previous value or null if not set.
 * @param int      $value 		The new value.
 */

function fictioneer_log_page_assignment_update( $option, $old_value, $value ) {
	// Abort if...
	if ( $old_value && $old_value == $value ) return;

	// Setup
  $update = sprintf(
    __( '%1$s page assignment changed from #%2$s to #%3$s', 'fictioneer' ),
    $option,
    $old_value,
    $value
  );

	// Update
	fictioneer_update_log( $update );
}

/**
 * Helper to log added page assignment option
 *
 * @since Fictioneer 5.0
 *
 * @param string $option Name of the option.
 * @param int    $value  The new value.
 */

function fictioneer_settings_page_assignment_added( $option, $value ) {
  // Abort if...
  if ( ! in_array( $option, FICTIONEER_PAGE_ASSIGNMENTS ) ) return;

  // Log
  $description = FICTIONEER_OPTIONS['integers'][ $option ]['label'];
  fictioneer_log_page_assignment_update( $description, null, $value );
}
add_action( 'added_option', 'fictioneer_settings_page_assignment_added', 10, 2 );

/**
 * Helper to log updated page assignment option
 *
 * @since Fictioneer 5.0
 *
 * @param string 	$option 	 Name of the option.
 * @param int     $old_value The previous value.
 * @param int     $value 		 The new value.
 */

function fictioneer_settings_page_assignment_updated( $option, $old_value, $value ) {
  // Abort if...
  if ( ! in_array( $option, FICTIONEER_PAGE_ASSIGNMENTS ) ) return;

  // Log
  $description = FICTIONEER_OPTIONS['integers'][ $option ]['label'];
  fictioneer_log_page_assignment_update( $description, $old_value, $value );
}
add_action( 'updated_option', 'fictioneer_settings_page_assignment_updated', 10, 3 );

// =============================================================================
// LOG PHRASE UPDATES
// =============================================================================

define( 'FICTIONEER_PHRASES', array(
  'fictioneer_phrase_maintenance',
  'fictioneer_phrase_login_modal',
  'fictioneer_phrase_cookie_consent_banner',
  'fictioneer_phrase_comment_reply_notification'
));

/**
 * Log update of phrase option
 *
 * @since Fictioneer 5.0
 *
 * @param string 	    $option 	  Name of the option.
 * @param string|null $old_value The previous value or null if not set.
 * @param string      $value 		The new value.
 */

function fictioneer_log_phrase_update( $option, $old_value, $value ) {
	// Abort if...
	if ( $old_value && $old_value == $value ) return;

	// Setup
  $update = sprintf(
    __( 'Phrase for the %1$s changed to "%2$s"', 'fictioneer' ),
    $option,
    str_replace( ["\r", "\n"], ' ', $value )
  );

	// Update
	fictioneer_update_log( $update );
}

/**
 * Helper to log added phrase option
 *
 * @since Fictioneer 5.0
 *
 * @param string $option Name of the option.
 * @param string $value  The new value.
 */

function fictioneer_settings_phrase_added( $option, $value ) {
  // Abort if...
  if ( ! in_array( $option, FICTIONEER_PHRASES ) ) return;

  // Log
  $description = FICTIONEER_OPTIONS['strings'][ $option ]['label'];
  fictioneer_log_phrase_update( $description, null, $value );
}
add_action( 'added_option', 'fictioneer_settings_phrase_added', 10, 2 );

/**
 * Helper to log updated page assignment option
 *
 * @since Fictioneer 5.0
 *
 * @param string $option 	  Name of the option.
 * @param string $old_value The previous value.
 * @param string $value 		The new value.
 */

function fictioneer_settings_phrase_updated( $option, $old_value, $value ) {
  // Abort if...
  if ( ! in_array( $option, FICTIONEER_PHRASES ) ) return;

  // Log
  $description = FICTIONEER_OPTIONS['strings'][ $option ]['label'];
  fictioneer_log_phrase_update( $description, $old_value, $value );
}
add_action( 'updated_option', 'fictioneer_settings_phrase_updated', 10, 3 );

// =============================================================================
// LOG CONNECTION UPDATES
// =============================================================================

define( 'FICTIONEER_CONNECTIONS', array(
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
));

/**
 * Log update of connection option
 *
 * @since Fictioneer 5.0
 *
 * @param string 	 $connection Name of the connection option.
 * @param int|null $old_value  ID of the old page or null if unset.
 * @param int			 $value 		 ID of the new page.
 */

function fictioneer_log_connection_update( $connection, $old_value, $value ) {
	// Abort if...
	if ( $old_value && $old_value == $value ) return;

	// Setup
  $update = sprintf(
    __( '%s updated', 'fictioneer' ),
    $connection
  );

	// Update
	fictioneer_update_log( $update );
}

/**
 * Helper to log added connection option
 *
 * @since Fictioneer 5.0
 *
 * @param string $option Name of the option.
 * @param string $value  The new value.
 */

function fictioneer_settings_connection_added( $option, $value ) {
  // Abort if...
  if ( ! in_array( $option, FICTIONEER_CONNECTIONS ) ) return;

  // Log
  $description = FICTIONEER_OPTIONS['strings'][ $option ]['label'];
  fictioneer_log_connection_update( $description, null, $value );
}
add_action( 'added_option', 'fictioneer_settings_connection_added', 10, 2 );

/**
 * Helper to log updated connection option
 *
 * @since Fictioneer 5.0
 *
 * @param string $option 	  Name of the option.
 * @param string $old_value The previous value.
 * @param string $value 		The new value.
 */

function fictioneer_settings_connection_updated( $option, $old_value, $value ) {
  // Abort if...
  if ( ! in_array( $option, FICTIONEER_CONNECTIONS ) ) return;

  // Log
  $description = FICTIONEER_OPTIONS['strings'][ $option ]['label'];
  fictioneer_log_connection_update( $description, $old_value, $value );
}
add_action( 'updated_option', 'fictioneer_settings_connection_updated', 10, 3 );

// =============================================================================
// LOG TRASHED/RESTORED POSTS
// =============================================================================

add_action( 'trashed_post', function( $post_id ) {
  // Setup
  $action = __( 'trashed', 'fictioneer' );

  // Log
	fictioneer_log_post_update( $post_id, $action );
});

add_action( 'untrashed_post', function( $post_id ) {
  // Setup
  $action = __( 'restored', 'fictioneer' );

  // Log
	fictioneer_log_post_update( $post_id, $action );
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
 * @param int 	  $post_id    The post ID.
 * @param WP_Post $post 	    The post object.
 * @param string  $old_status The old status.
 */

function fictioneer_log_published_posts( $post_id, $post, $old_status ) {
  // Prevent multi-fire
  if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) return;

  // Setup
  $action = __( 'updated', 'fictioneer' );

  if ( strtotime( '-5 seconds' ) < strtotime( get_the_date( 'c', $post_id ) ) ) {
    $action = __( 'published', 'fictioneer' );
  }

  // Log
	fictioneer_log_post_update( $post_id, $action );
}

add_action( 'publish_post', 'fictioneer_log_published_posts', 10, 3);
add_action( 'publish_page', 'fictioneer_log_published_posts', 10, 3);
add_action( 'publish_fcn_story', 'fictioneer_log_published_posts', 10, 3);
add_action( 'publish_fcn_chapter', 'fictioneer_log_published_posts', 10, 3);
add_action( 'publish_fcn_collection', 'fictioneer_log_published_posts', 10, 3);
add_action( 'publish_fcn_recommendation', 'fictioneer_log_published_posts', 10, 3);

// =============================================================================
// LOG PENDING POSTS
// =============================================================================

/**
 * Helper to log pending posts
 *
 * @since Fictioneer 5.0
 * @see fictioneer_log_post_update()
 *
 * @param int 	  $post_id    The post ID.
 * @param WP_Post $post 	    The post object.
 * @param string  $old_status The old status.
 */

function fictioneer_log_pending_posts( $post_id, $post, $old_status ) {
  // Prevent multi-fire
  if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) return;

  // Setup
  $action = __( 'submitted for review', 'fictioneer' );

  // Log
	fictioneer_log_post_update( $post_id, $action );
}

add_action( 'pending_post', 'fictioneer_log_pending_posts', 10, 3);
add_action( 'pending_page', 'fictioneer_log_pending_posts', 10, 3);
add_action( 'pending_fcn_story', 'fictioneer_log_published_posts', 10, 3);
add_action( 'pending_fcn_chapter', 'fictioneer_log_published_posts', 10, 3);
add_action( 'pending_fcn_collection', 'fictioneer_log_published_posts', 10, 3);
add_action( 'pending_fcn_recommendation', 'fictioneer_log_published_posts', 10, 3);

// =============================================================================
// LOG DELETED POSTS
// =============================================================================

add_action( 'before_delete_post', function( $post_id ) {
  // Prevent multi-fire
  if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) return;
  if ( did_action( 'before_delete_post' ) != 1 ) return;

  // Setup
  $action = __( 'deleted', 'fictioneer' );
  $post_type = get_post_type( $post_id );
  $allowed_post_types = ['fcn_story', 'fcn_chapter', 'fcn_recommendation', 'fcn_collection', 'post', 'page'];

  // Only log certain post types
  if ( ! in_array( $post_type, $allowed_post_types ) ) return;

  // Log
  fictioneer_log_post_update( $post_id, $action );
});

?>
