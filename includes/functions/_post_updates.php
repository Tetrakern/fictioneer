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
  $story_id = fictioneer_get_field( 'fictioneer_chapter_story', $post_id );

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
  $first_publish_date = fictioneer_get_field( 'fictioneer_first_publish_date', $post_id );

  // Set if missing
  if ( empty( $first_publish_date ) || strtotime( $first_publish_date ) === false ) {
    update_post_meta( $post_id, 'fictioneer_first_publish_date', current_time( 'mysql' ) );
  }
}
add_action( 'save_post', 'fictioneer_store_original_publish_date', 10, 2 );

?>
