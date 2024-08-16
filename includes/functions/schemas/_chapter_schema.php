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
 * @since 4.0.0
 *
 * @param int     $post_id  The ID of the saved post.
 * @param WP_Post $post     The saved post object.
 */

function fictioneer_refresh_chapter_schema( $post_id, $post ) {
  static $done = null;

  // Prevent multi-fire; allow trashing to pass because
  // this is sometimes only triggered as REST request.
  if (
    ( fictioneer_multi_save_guard( $post_id ) || $done ) &&
    ( get_post_status( $post_id ) !== 'trash' || $done )
  ) {
    return;
  } else {
    $done = true;
  }

  // Delete schema if post is not published
  if ( $post->post_status !== 'publish' ) {
    delete_post_meta( $post_id, 'fictioneer_schema' );
  }

  // Check what was updated
  if ( $post->post_type !== 'fcn_chapter' ) {
    return;
  }

  // Setup
  $story_id = get_post_meta( $post_id, 'fictioneer_chapter_story', true );

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
   * @since 4.0.0
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
    $post = get_post( $post_id );
    $schema = fictioneer_get_schema_node_root();
    $story_id = get_post_meta( $post_id, 'fictioneer_chapter_story', true );
    $image_data = fictioneer_get_schema_primary_image( $post_id );
    $word_count = fictioneer_get_word_count( $post_id );
    $page_description = fictioneer_get_seo_description( $post_id );
    $page_title = fictioneer_get_seo_title( $post_id, array( 'skip_cache' => true ) );

    if ( ! $post ) {
      return '';
    }

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
      'Article', $page_description, $post, $image_data, true
    );

    if ( $word_count > 0 ) {
      $article_node['wordCount'] = $word_count;
    }

    if ( ! empty( $story_id ) ) {
      $article_node['contentRating'] = get_post_meta( $story_id, 'fictioneer_story_rating', true );
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
