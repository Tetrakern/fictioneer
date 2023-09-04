<?php

// =============================================================================
// REFRESH CHAPTER SCHEMA
// =============================================================================

/**
 * Refresh chapter schemas
 *
 * "There are only two hard things in Computer Science: cache invalidation and
 * naming things" -- Phil Karlton.
 *
 * @since Fictioneer 4.0
 *
 * @param int     $post_id  The ID of the saved post.
 * @param WP_Post $post     The saved post object.
 */

function fictioneer_refresh_chapter_schema( $post_id, $post ) {
  // Prevent multi-fire
  if ( fictioneer_multi_save_guard( $post_id ) ) {
    return;
  }

  // Check what was updated
  if ( $post->post_type !== 'fcn_chapter' ) {
    return;
  }

  // Setup
  $story_id = fictioneer_get_field( 'fictioneer_chapter_story', $post_id );

  // Rebuild schema
  fictioneer_build_chapter_schema( $post_id );

  // Also rebuild story schema (if any)
  if ( ! empty( $story_id ) ) {
    fictioneer_build_story_schema( $story_id );
  }
}
add_action( 'save_post', 'fictioneer_refresh_chapter_schema', 20, 2 );

// =============================================================================
// BUILD CHAPTER SCHEMA
// =============================================================================

if ( ! function_exists( 'fictioneer_build_chapter_schema' ) ) {
  /**
   * Refresh single chapter schema
   *
   * @since Fictioneer 4.0
   *
   * @param int $post_id The ID of the chapter the schema is for.
   *
   * @return string The encoded JSON or an empty string.
   */

  function fictioneer_build_chapter_schema( $post_id ) {
    // Abort if...
    if ( ! $post_id ) {
      return '';
    }

    // Setup
    $schema = fictioneer_get_schema_node_root();
    $story_id = fictioneer_get_field( 'fictioneer_chapter_story', $post_id );
    $image_data = fictioneer_get_schema_primary_image( $post_id );
    $word_count = intval( get_post_meta( $post_id, '_word_count', true ) );
    $page_description = fictioneer_get_seo_description( $post_id );
    $page_title = fictioneer_get_seo_title( $post_id, array( 'skip_cache' => true ) );

    // Website node
    $schema['@graph'][] = fictioneer_get_schema_node_website();

    // Image node
    if ( ! empty( $image_data ) ) {
      $schema['@graph'][] = fictioneer_get_schema_node_image( $image_data );
    }

    // Webpage node
    $schema['@graph'][] = fictioneer_get_schema_node_webpage(
      'WebPage', $page_title, $page_description, $post_id, $image_data
    );

    // Article node
    $article_node = fictioneer_get_schema_node_article(
      'Article', $page_description, get_post( $post_id ), $image_data, true
    );

    if ( $word_count > 0 ) {
      $article_node['wordCount'] = $word_count;
    }

    if ( ! empty( $story_id ) ) {
      $article_node['contentRating'] = fictioneer_get_field( 'fictioneer_story_rating', $story_id );
      $article_node['articleSection'] = get_the_title( $story_id );
    }

    $schema['@graph'][] = $article_node;

    // Prepare and cache for next time
    $schema = json_encode( $schema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES );
    update_post_meta( $post_id, 'fictioneer_schema', $schema );

    // Return JSON string
    return $schema;
  }
}

?>
