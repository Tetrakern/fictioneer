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
 * Store word count of posts as meta data
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
add_action( 'save_post', 'fictioneer_save_word_count' );

// =============================================================================
// STORE ORIGINAL PUBLISH DATE
// =============================================================================

/**
 * Stores the original publish date of a post in post meta
 *
 * @since Fictioneer 5.6.0
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
    update_post_meta( $post_id, 'fictioneer_first_publish_date', current_time( 'mysql' ) );
  }
}
add_action( 'save_post', 'fictioneer_store_original_publish_date', 10, 2 );

// =============================================================================
// STORY CHANGELOG
// =============================================================================

/**
 * Returns story changelog
 *
 * @since Fictioneer 5.7.5
 *
 * @param int $story_id  The story post ID.
 *
 * @return array Array with logged chapter changes since initialization.
 */

function fictioneer_get_story_changelog( $story_id ) {
  // Setup
  $changelog = get_post_meta( $story_id, 'fictioneer_story_changelog', true );
  $changelog = is_array( $changelog ) ? $changelog : [];

  // Initialize
  if ( empty( $changelog ) ) {
    $changelog[] = array(
      time(),
      _x( 'Initialized.', 'Story changelog initialized.', 'fictioneer' )
    );

    update_post_meta( $story_id, 'fictioneer_story_changelog', $changelog );
  }

  // Return
  return $changelog;
}

/**
 * Logs changes to story chapters
 *
 * @since Fictioneer 5.7.5
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
        fictioneer_get_safe_title( $post_id )
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
        fictioneer_get_safe_title( $post_id )
      )
    );
  }

  // Save
  update_post_meta( $story_id, 'fictioneer_story_changelog', $changelog );
}

/**
 * Logs status changes of story chapters
 *
 * @since Fictioneer 5.7.5
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
  $story_id = get_post_meta( $post->ID, 'fictioneer_chapter_story', true );

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
        $post->ID,
        fictioneer_get_safe_title( $post->ID, true )
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
        $post->ID,
        fictioneer_get_safe_title( $post->ID, true )
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
 * @since Fictioneer 5.7.5
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
  $chapters = get_post_meta( $story_id, 'fictioneer_story_chapters', true ) ?: [];
  $previous = get_post_meta( $story_id, 'fictioneer_story_chapters', true ) ?: [];

  if ( empty( $chapters ) || ! in_array( $chapter_id, $chapters ) ) {
    return;
  }

  // Update story
  $chapters = fictioneer_unset_by_value( $chapter_id, $chapters );

  update_post_meta( $story_id, 'fictioneer_story_chapters', $chapters );
  update_post_meta( $story_id, 'fictioneer_chapters_modified', current_time( 'mysql' ) );

  // Log change
  fictioneer_log_story_chapter_changes( $story_id, $chapters, $previous );

  // Clear story data cache to ensure it gets refreshed
  delete_post_meta( $story_id, 'fictioneer_story_data_collection' );

  // Update story post to fire associated actions
  wp_update_post( array( 'ID' => $story_id ) );
}
add_action( 'trashed_post', 'fictioneer_remove_chapter_from_story' );

/**
 * Wrapper for actions when a chapter is set to draft
 *
 * @since Fictioneer 5.7.5
 *
 * @param WP_Post $post  The post object.
 */

function fictioneer_chapter_to_draft( $post ) {
  // Chapter?
  if ( $post->post_type !== 'fcn_chapter' ) {
    return;
  }

  // Remove filter
  remove_filter( 'fictioneer_filter_safe_title', 'fictioneer_prefix_draft_safe_title' );

  // Remove chapter from story
  fictioneer_remove_chapter_from_story( $post->ID );

  // Add filter
  add_filter( 'fictioneer_filter_safe_title', 'fictioneer_prefix_draft_safe_title', 10, 2 );
}
add_action( 'publish_to_draft', 'fictioneer_chapter_to_draft' );
add_action( 'private_to_draft', 'fictioneer_chapter_to_draft' );

?>
