<?php

// =============================================================================
// UPDATE MODIFIED DATE OF STORY WHEN CHAPTER IS UPDATED
// =============================================================================

/**
 * Update modified date of story when chapter is updated
 *
 * @since 3.0
 *
 * @param int $post_id  The post ID.
 */

function fictioneer_update_modified_date_on_story_for_chapter( $post_id ) {
  global $wpdb;

  // Prevent multi-fire
  if ( fictioneer_multi_save_guard( $post_id ) ) {
    return;
  }

  // Chapter updated?
  if ( get_post_type( $post_id ) != 'fcn_chapter' ) {
    return;
  }

  // Setup
  $story_id = get_post_meta( $post_id, 'fictioneer_chapter_story', true );

  // No linked story found
  if ( empty( $story_id ) || ! get_post_status( $story_id ?? 0 ) ) {
    return;
  }

  // Get current time for update (close enough to the chapter)
  $post_modified = current_time( 'mysql' );
  $post_modified_gmt = current_time( 'mysql', true );

  // Update database
  $wpdb->query( "UPDATE $wpdb->posts SET post_modified = '{$post_modified}', post_modified_gmt = '{$post_modified_gmt}' WHERE ID = {$story_id}" );
}
fictioneer_add_stud_post_actions( 'fictioneer_update_modified_date_on_story_for_chapter' );

// =============================================================================
// STORE WORD COUNT AS CUSTOM FIELD
// =============================================================================

/**
 * Store word count of posts
 *
 * @since 3.0
 * @see update_post_meta()
 *
 * @param int $post_id  Post ID.
 */

function fictioneer_save_word_count( $post_id ) {
  // Prevent multi-fire
  if ( fictioneer_multi_save_guard( $post_id ) ) {
    return;
  }

  // Prepare
  $content = get_post_field( 'post_content', $post_id );
  $content = strip_shortcodes( $content );
  $content = strip_tags( $content );

  // Count
  $word_count = str_word_count( $content );

  // Remember
  update_post_meta( $post_id, '_word_count', $word_count );
}

if ( ! get_option( 'fictioneer_count_characters_as_words' ) ) {
  add_action( 'save_post', 'fictioneer_save_word_count' );
}

/**
 * Store character count of posts as word count
 *
 * @since 5.9.4
 * @see update_post_meta()
 *
 * @param int $post_id  Post ID.
 */

function fictioneer_characters_as_word_count( $post_id ) {
  // Prevent multi-fire
  if ( fictioneer_multi_save_guard( $post_id ) ) {
    return;
  }

  // Prepare
  $content = get_post_field( 'post_content', $post_id );
  $content = strip_shortcodes( $content );
  $content = strip_tags( $content );

  // Count
  $word_count = mb_strlen( $content, 'UTF-8' );

  // Remember
  update_post_meta( $post_id, '_word_count', $word_count );
}

if ( get_option( 'fictioneer_count_characters_as_words' ) ) {
  add_action( 'save_post', 'fictioneer_characters_as_word_count' );
}

// =============================================================================
// STORE ORIGINAL PUBLISH DATE
// =============================================================================

/**
 * Stores the original publish date of a post in post meta
 *
 * @since 5.6.0
 *
 * @param int     $post_id  The ID of the post being saved.
 * @param WP_Post $post     The post object being saved.
 */

function fictioneer_store_original_publish_date( $post_id, $post ) {
  // Prevent miss-fire
  if (
    fictioneer_multi_save_guard( $post_id ) ||
    $post->post_status !== 'publish'
  ) {
    return;
  }

  // Get first publish date (if set)
  $first_publish_date = get_post_meta( $post_id, 'fictioneer_first_publish_date', true );

  // Set if missing
  if ( empty( $first_publish_date ) || strtotime( $first_publish_date ) === false ) {
    update_post_meta( $post_id, 'fictioneer_first_publish_date', current_time( 'mysql', true ) );
  }
}
add_action( 'save_post', 'fictioneer_store_original_publish_date', 10, 2 );

// =============================================================================
// STORY CHANGELOG
// =============================================================================

/**
 * Logs changes to story chapters
 *
 * @since 5.7.5
 *
 * @param int    $story_id  The story post ID.
 * @param array  $current   Current chapters.
 * @param array  $previous  Previous chapters.
 * @param string $verb      Optional. The verb describing the logged action.
 */

function fictioneer_log_story_chapter_changes( $story_id, $current, $previous, $verb = null ) {
  if ( ! FICTIONEER_ENABLE_STORY_CHANGELOG ) {
    return;
  }

  // Setup
  $changelog = fictioneer_get_story_changelog( $story_id );
  $current = is_array( $current ) ? $current : [];
  $previous = is_array( $previous ) ? $previous : [];

  // Check for changes
  $added = array_diff( $current, $previous );
  $removed = array_diff( $previous, $current );

  // Log
  foreach ( $added as $post_id ) {
    $changelog[] = array(
      time(),
      sprintf(
        _x( '#%s %s: %s.', 'Story changelog chapter added.', 'fictioneer' ),
        $post_id,
        $verb ? $verb : _x( 'added', 'Story changelog verb.', 'fictioneer' ),
        fictioneer_get_safe_title( $post_id, 'admin-log-added-story-chapter' )
      )
    );
  }

  foreach ( $removed as $post_id ) {
    $changelog[] = array(
      time(),
      sprintf(
        _x( '#%s %s: %s.', 'Story changelog chapter removed.', 'fictioneer' ),
        $post_id,
        $verb ? $verb : _x( 'removed', 'Story changelog verb.', 'fictioneer' ),
        fictioneer_get_safe_title( $post_id, 'admin-log-removed-story-chapter' )
      )
    );
  }

  // Save
  update_post_meta( $story_id, 'fictioneer_story_changelog', $changelog );
}

/**
 * Logs status changes of story chapters
 *
 * @since 5.7.5
 *
 * @param string  $new_status  The old status.
 * @param string  $old_status  The new status.
 * @param WP_Post $post        The post object.
 */

function fictioneer_log_story_chapter_status_changes( $new_status, $old_status, $post ) {
  // Changed?
  if ( $old_status == $new_status ) {
    return;
  }

  // Chapter?
  if ( $post->post_type !== 'fcn_chapter' ) {
    return;
  }

  // Story?
  $post_id = $post->ID;
  $story_id = get_post_meta( $post_id, 'fictioneer_chapter_story', true );

  if ( empty( $story_id ) ) {
    return;
  }

  // Setup
  $changelog = fictioneer_get_story_changelog( $story_id );

  // Add filters
  add_filter( 'private_title_format', 'fictioneer__return_no_format', 99 );

  // Publish -> Private?
  if ( $old_status == 'publish' && $new_status == 'private' ) {
    $changelog[] = array(
      time(),
      sprintf(
        _x( '#%s privated: %s.', 'Story changelog chapter removed.', 'fictioneer' ),
        $post_id,
        fictioneer_get_safe_title( $post_id, true, 'admin-log-status-change-publish_to_private' )
      )
    );

    update_post_meta( $story_id, 'fictioneer_story_changelog', $changelog );
  }

  // Private -> Publish?
  if ( $new_status == 'publish' && $old_status == 'private' ) {
    $changelog[] = array(
      time(),
      sprintf(
        _x( '#%s unprivated: %s.', 'Story changelog chapter removed.', 'fictioneer' ),
        $post_id,
        fictioneer_get_safe_title( $post_id, true, 'admin-log-status-change-private_to_publish' )
      )
    );

    update_post_meta( $story_id, 'fictioneer_story_changelog', $changelog );
  }

  // Remove filters
  remove_filter( 'private_title_format', 'fictioneer__return_no_format', 99 );
}

if ( FICTIONEER_ENABLE_STORY_CHANGELOG ) {
  add_action( 'transition_post_status', 'fictioneer_log_story_chapter_status_changes', 10, 3 );
}

// =============================================================================
// STORY CHAPTER LIST
// =============================================================================

/**
 * Removes chapter from story
 *
 * @since 5.7.5
 *
 * @param int $chapter_id  The chapter post ID.
 */

function fictioneer_remove_chapter_from_story( $chapter_id ) {
  // Chapter?
  if ( get_post_type( $chapter_id ) !== 'fcn_chapter' ) {
    return;
  }

  // Story?
  $story_id = get_post_meta( $chapter_id, 'fictioneer_chapter_story', true );

  if ( empty( $story_id ) ) {
    return;
  }

  // Check chapter list
  $chapters = fictioneer_get_story_chapter_ids( $story_id );
  $previous = $chapters;

  if ( empty( $chapters ) || ! in_array( $chapter_id, $chapters ) ) {
    return;
  }

  // Update story
  $chapters = fictioneer_unset_by_value( $chapter_id, $chapters );

  update_post_meta( $story_id, 'fictioneer_story_chapters', $chapters );
  update_post_meta( $story_id, 'fictioneer_chapters_modified', current_time( 'mysql', true ) );

  // Log change
  fictioneer_log_story_chapter_changes( $story_id, $chapters, $previous );

  // Clear meta caches to ensure they get refreshed
  delete_post_meta( $story_id, 'fictioneer_story_data_collection' );
  delete_post_meta( $story_id, 'fictioneer_story_chapter_index_html' );

  // Update story post to fire associated actions
  wp_update_post( array( 'ID' => $story_id ) );
}
add_action( 'trashed_post', 'fictioneer_remove_chapter_from_story' );

/**
 * Wrapper for actions when a chapter is set to draft
 *
 * @since 5.7.5
 *
 * @param WP_Post $post  The post object.
 */

function fictioneer_chapter_to_draft( $post ) {
  // Chapter?
  if ( $post->post_type !== 'fcn_chapter' ) {
    return;
  }

  // Temporarily remove filter
  remove_filter( 'fictioneer_filter_safe_title', 'fictioneer_prefix_draft_safe_title' );

  // Remove chapter from story
  fictioneer_remove_chapter_from_story( $post->ID );

  // Re-add filter
  add_filter( 'fictioneer_filter_safe_title', 'fictioneer_prefix_draft_safe_title', 10, 2 );
}
add_action( 'publish_to_draft', 'fictioneer_chapter_to_draft' );
add_action( 'private_to_draft', 'fictioneer_chapter_to_draft' );

/**
 * Perform updates when a chapter goes from future to publish
 *
 * @since 5.21.0
 *
 * @param string  $new_status  New post status.
 * @param string  $old_status  Old post status.
 * @param WP_Post $post        Post object.
 */

function fictioneer_chapter_future_to_publish( $new_status, $old_status, $post ) {
  // Validate transition...
  if ( $post->post_type !== 'fcn_chapter' || $old_status !== 'future' || $new_status !== 'publish' ) {
    return;
  }

  // Update fictioneer_chapters_added field of story (if any)
  $story_id = get_post_meta( $post->ID, 'fictioneer_chapter_story', true );

  if ( $story_id ) {
    update_post_meta( $story_id, 'fictioneer_chapters_added', $post->post_date_gmt );
  }
}
add_action( 'transition_post_status', 'fictioneer_chapter_future_to_publish', 10, 3 );

// =============================================================================
// POST PASSWORD EXPIRATION
// =============================================================================

/**
 * Expire post password
 *
 * Note: This just hijacks the password check to remove the password.
 * The actual password requirement is not affected.
 *
 * @since 5.17.0
 *
 * @param bool    $required  Whether the user needs to supply a password.
 * @param WP_Post $post      Post object.
 *
 * @return bool True or false.
 */

function fictioneer_expire_post_password( $required, $post ) {
  // Already unlocked
  if ( ! $required ) {
    return $required;
  }

  // Static variable cache
  static $cache = [];

  $cache_key = $post->ID . '_' . get_current_user_id() . '_' . (int) $required;

  if ( isset( $cache[ $cache_key ] ) ) {
    return $cache[ $cache_key ];
  }

  // Setup
  $password_expiration_date_utc = get_post_meta( $post->ID, 'fictioneer_post_password_expiration_date', true );

  if ( $password_expiration_date_utc ) {
    $current_date_utc = current_time( 'mysql', true );

    if ( strtotime( $current_date_utc ) > strtotime( $password_expiration_date_utc ) ) {
      delete_post_meta( $post->ID, 'fictioneer_post_password_expiration_date' );
      fictioneer_refresh_post_caches( $post->ID );
      wp_update_post( array( 'ID' => $post->ID, 'post_password' => '' ) );

      $required = false;
    }
  }

  // Cache
  $cache[ $cache_key ] = $required;

  // Continue filter
  return $required;
}
add_filter( 'post_password_required', 'fictioneer_expire_post_password', 5, 2 );
