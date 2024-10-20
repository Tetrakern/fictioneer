<?php

// =============================================================================
// UPDATE MODIFIED DATE OF STORY WHEN CHAPTER IS UPDATED
// =============================================================================

/**
 * Update modified date of story when chapter is updated
 *
 * @since 3.0.0
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
  if ( get_post_type( $post_id ) !== 'fcn_chapter' || get_post_status( $post_id ) !== 'publish' ) {
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
 * @since 3.0.0
 * @since 5.23.0 - Account for non-Latin scripts.
 * @since 5.25.0 - Split into action and utility function.
 * @see fictioneer_count_words()
 * @see update_post_meta()
 *
 * @param int $post_id  Post ID.
 */

function fictioneer_save_word_count( $post_id ) {
  // Prevent multi-fire
  if ( fictioneer_multi_save_guard( $post_id ) ) {
    return;
  }

  // Count
  $word_count = fictioneer_count_words( $post_id );

  // Save
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
  if (
    $post->post_type !== 'fcn_chapter' ||
    $old_status !== 'future' ||
    $new_status !== 'publish' ||
    get_post_meta( $post->ID, 'fictioneer_chapter_hidden', true )
  ) {
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

// =============================================================================
// STORY COMMENT COUNT
// =============================================================================

/**
 * Increments comment count of story by 1
 *
 * @since 5.22.3
 *
 * @param int $comment_id  ID of the comment belonging to the post
 */

function fictioneer_increment_story_comment_count( $comment_id ) {
  // Setup
  $comment = get_comment( $comment_id );

  if ( ! $comment ) {
    return;
  }

  $story_id = get_post_meta( $comment->comment_post_ID, 'fictioneer_chapter_story', true );
  $story_data = $story_id ? fictioneer_get_story_data( $story_id ) : null;

  // Increment comment count (will be recounted at some later point)
  if ( $story_data ) {
    $story_data['comment_count'] = intval( $story_data['comment_count'] ) + 1;
    update_post_meta( $story_id, 'fictioneer_story_data_collection', $story_data );
  }
}

/**
 * Decrements comment count of story by 1
 *
 * @since 5.22.3
 *
 * @param int $comment_id  ID of the comment belonging to the post
 */

function fictioneer_decrement_story_comment_count( $comment_id ) {
  // Setup
  $comment = get_comment( $comment_id );

  if ( ! $comment ) {
    return;
  }

  $story_id = get_post_meta( $comment->comment_post_ID, 'fictioneer_chapter_story', true );
  $story_data = $story_id ? fictioneer_get_story_data( $story_id ) : null;

  // Decrement comment count (will be recounted at some later point)
  if ( $story_data ) {
    $story_data['comment_count'] = max( 0, intval( $story_data['comment_count'] ) - 1 );
    update_post_meta( $story_id, 'fictioneer_story_data_collection', $story_data );
  }
}

if ( FICTIONEER_ENABLE_STORY_DATA_META_CACHE ) {
  add_action( 'wp_insert_comment', 'fictioneer_increment_story_comment_count' );
  add_action( 'delete_comment', 'fictioneer_decrement_story_comment_count' );
}

// =============================================================================
// TOOLTIP FOOTNOTES
// =============================================================================

/**
 * Collects a footnote to be stored for later rendering
 *
 * @since 5.25.0
 *
 * @param int    $footnote_id  Unique identifier for the footnote.
 * @param string $content      Content of the footnote.
 */

function fictioneer_collect_footnote( $footnote_id, $content ) {
  global $fictioneer_footnotes;

  // Initialize footnotes array if it doesn't exist
  if ( ! is_array( $fictioneer_footnotes ) ) {
    $fictioneer_footnotes = [];
  }

  // Store footnote content with its ID
  $fictioneer_footnotes[ $footnote_id ] = $content;
}

/**
 * Renders collected footnotes at the end of the content
 *
 * @since 5.25.0
 *
 * @param string $content  The post content.
 *
 * @return string The post content with appended footnotes.
 */

function fictioneer_append_footnotes_to_content( $content ) {
  global $fictioneer_footnotes;

  // Only proceed for single posts/pages with footnotes and
  // check the post ID in case of multiple content calls.
  if (
    ! is_singular() ||
    empty( $fictioneer_footnotes ) ||
    get_queried_object_id() != get_the_ID()
  ) {
    return $content;
  }

  // Allow modifications to the collected footnotes
  $fictioneer_footnotes = apply_filters( 'fictioneer_filter_footnotes', $fictioneer_footnotes );

  // Generate the HTML for footnotes section
  $html = sprintf(
    '<div class="footnotes"><h3>%s</h3>',
    esc_html( __( 'Footnotes', 'fictioneer' ) )
  );

  $html .= '<ol class="footnotes__list list">';

  foreach ( $fictioneer_footnotes as $id => $footnote ) {
    $html .= sprintf(
    '<li id="footnote-%1$d" class="footnotes__item">%2$s <a href="#tooltip-%1$d" class="footnotes__link-up"><i class="fa-solid fa-arrow-turn-up"></i></a></li>',
    $id,
    wp_kses_post( $footnote )
    );
  }

  $html .= '</ol></div>';

  // Reset the footnotes array
  $fictioneer_footnotes = [];

  // Append footnotes to the content
  return $content . $html;
}

if ( get_option( 'fictioneer_generate_footnotes_from_tooltips' ) ) {
  add_action( 'fictioneer_collect_footnote', 'fictioneer_collect_footnote', 10, 2 );
  add_filter( 'the_content', 'fictioneer_append_footnotes_to_content', 20 );
}
