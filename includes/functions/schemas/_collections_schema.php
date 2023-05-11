<?php

// =============================================================================
// REFRESH COLLECTIONS SUMMARY SCHEMA
// =============================================================================

if ( ! function_exists( 'fictioneer_refresh_collections_schema' ) ) {
  /**
   * Refresh collections summary schemas
   *
   * "There are only two hard things in Computer Science: cache invalidation and
   * naming things" -- Phil Karlton.
   *
   * @since Fictioneer 4.0
   *
   * @param int     $post_id The ID of the saved post.
   * @param WP_Post $post    The saved post object.
   */

  function fictioneer_refresh_collections_schema( $post_id, $post ) {
    // Prevent multi-fire
    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) return;
    if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) return;

    // Check what was updated
    $sub_update = in_array( $post->post_type, ['fcn_collection', 'fcn_story', 'fcn_chapter', 'fcn_recommendation', 'post'] );
    if ( get_page_template_slug() != 'collections.php' && ! $sub_update ) return;

    // Get all pages with the collections template
    $pages = get_posts(
      array(
        'post_type' => 'page',
        'numberposts' => -1,
        'meta_key' => '_wp_page_template',
        'meta_value' => 'collections.php'
      )
    );

    // Rebuild schemas
    if ( $pages ) {
      foreach ( $pages as $page ) {
        fictioneer_build_collections_schema( $page->ID );
      }
    }
  }
}
add_action( 'save_post', 'fictioneer_refresh_collections_schema', 20, 2 );

// =============================================================================
// BUILD COLLECTIONS SUMMARY SCHEMA
// =============================================================================

if ( ! function_exists( 'fictioneer_build_collections_schema' ) ) {
  /**
   * Refresh collections summary schema
   *
   * @since Fictioneer 4.0
   *
   * @param int $post_id The ID of the page the schema is for.
   *
   * @return string The encoded JSON or an empty string.
   */

  function fictioneer_build_collections_schema( $post_id ) {
    // Abort if...
    if ( ! $post_id ) return '';

    // Prepare query arguments
    $query_args = array (
      'post_type' => array( 'fcn_collection' ),
      'post_status' => array( 'publish' ),
      'orderby' => 'modified',
      'order' => 'DESC',
      'posts_per_page' => 20,
      'no_found_rows' => true,
      'fields' => 'ids',
      'update_post_meta_cache' => false,
      'update_post_term_cache' => false
    );

    // Setup
    $list = get_posts( $query_args );
    $schema = fictioneer_get_schema_node_root();
    $image_data = fictioneer_get_schema_primary_image( $post_id );

    $page_description = fictioneer_get_seo_description( $post_id, array(
      'default' => sprintf(
        __( 'Collections on %s.', 'fictioneer' ),
        FICTIONEER_SITE_NAME
      ),
      'skip_cache' => true
    ));

    $page_title = fictioneer_get_seo_title( $post_id, array(
      'default' => sprintf(
        __( 'Collections – %s.', 'fictioneer' ),
        FICTIONEER_SITE_NAME
      ),
      'skip_cache' => true
    ));

    // Website node
    $schema['@graph'][] = fictioneer_get_schema_node_website();

    // Image node
    if ( $image_data ) {
      $schema['@graph'][] = fictioneer_get_schema_node_image( $image_data );
    }

    // Webpage node
    $schema['@graph'][] = fictioneer_get_schema_node_webpage(
      ['WebPage', 'CollectionPage'], $page_title, $page_description, $post_id, $image_data
    );

    // Article node
    $schema['@graph'][] = fictioneer_get_schema_node_article(
      'Article', $page_description, get_post( $post_id ), $image_data
    );

    // List node
    $schema['@graph'][] = fictioneer_get_schema_node_list(
      $list,
      __( 'Collections', 'fictioneer' ),
      sprintf(
        __( 'List of collections on %s.', 'fictioneer' ),
        FICTIONEER_SITE_NAME
      ),
      '#article'
    );

    // Prepare and cache for next time
    $schema = json_encode( $schema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES );
    update_post_meta( $post_id, 'fictioneer_schema', $schema );

    // Return JSON string
    return $schema;
  }
}

?>
