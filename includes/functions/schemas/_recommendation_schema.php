<?php

// =============================================================================
// REFRESH RECOMMENDATION SCHEMA
// =============================================================================

/**
 * Refresh recommendation schema
 *
 * "There are only two hard things in Computer Science: cache invalidation and
 * naming things" -- Phil Karlton.
 *
 * @since Fictioneer 4.0
 *
 * @param int     $post_id The ID of the saved post.
 * @param WP_Post $post    The saved post object.
 */

  function fictioneer_refresh_recommendation_schema( $post_id, $post ) {
  // Prevent multi-fire
  if ( fictioneer_multi_save_guard( $post_id ) ) {
    return;
  }

  // Check what was updated
  if ( $post->post_type !== 'fcn_recommendation' ) {
    return;
  }

  // Rebuild
  fictioneer_build_recommendation_schema( $post_id );
}
add_action( 'save_post', 'fictioneer_refresh_recommendation_schema', 20, 2 );

// =============================================================================
// BUILD RECOMMENDATION SCHEMA
// =============================================================================

if ( ! function_exists( 'fictioneer_build_recommendation_schema' ) ) {
  /**
   * Refresh single recommendation schema
   *
   * @since Fictioneer 4.0
   *
   * @param int $post_id The ID of the recommendation the schema is for.
   *
   * @return string The encoded JSON or an empty string.
   */

  function fictioneer_build_recommendation_schema( $post_id ) {
    // Abort if...
    if ( ! $post_id ) {
      return '';
    }

    // Setup
    $schema = fictioneer_get_schema_node_root();
    $image_data = fictioneer_get_schema_primary_image( $post_id );

    $page_description = fictioneer_get_seo_description(
      $post_id,
      array(
        'default' => fictioneer_get_field( 'fictioneer_recommendation_one_sentence', $post_id ),
        'skip_cache' => true
      )
    );

    $page_title = fictioneer_get_seo_title(
      $post_id,
      array(
        'default' => fictioneer_get_safe_title( $post_id ) . ' &ndash; ' . FICTIONEER_SITE_NAME,
        'skip_cache' => true
      )
    );

    // Website node
    $schema['@graph'][] = fictioneer_get_schema_node_website();

    // Image node
    if ( $image_data ) {
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

    // Override author node
    $article_node['author'] = array(
      '@type' => 'Person',
      'url' => fictioneer_get_field( 'fictioneer_recommendation_primary_url', $post_id ),
      'name' => addslashes( fictioneer_get_field( 'fictioneer_recommendation_author', $post_id ) )
    );

    $schema['@graph'][] = $article_node;

    // Prepare and cache for next time
    $schema = json_encode( $schema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES );
    update_post_meta( $post_id, 'fictioneer_schema', $schema );

    // Return JSON string
    return $schema;
  }
}

?>
