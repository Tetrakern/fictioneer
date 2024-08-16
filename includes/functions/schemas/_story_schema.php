<?php

// =============================================================================
// REFRESH STORY SCHEMA
// =============================================================================

/**
 * Refresh story schema
 *
 * "There are only two hard things in Computer Science: cache invalidation and
 * naming things" -- Phil Karlton.
 *
 * @since 4.0.0
 *
 * @param int     $post_id  The ID of the saved post.
 * @param WP_Post $post     The saved post object.
 */

function fictioneer_refresh_story_schema( $post_id, $post ) {
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
  if ( $post->post_type !== 'fcn_story' ) {
    return;
  }

  // Rebuild schema
  fictioneer_build_story_schema( $post_id );

  // Get chapters of story
  $chapters = fictioneer_get_story_chapter_ids( $post_id );

  // Rebuild chapter schemas (if any)
  if ( ! empty( $chapters ) ) {
    foreach ( $chapters as $chapter_id ) {
      fictioneer_build_chapter_schema( $chapter_id );
    }
  }
}
add_action( 'save_post', 'fictioneer_refresh_story_schema', 20, 2 );

// =============================================================================
// BUILD STORY SCHEMA
// =============================================================================

if ( ! function_exists( 'fictioneer_build_story_schema' ) ) {
  /**
   * Refresh single story schema
   *
   * @since 4.0.0
   *
   * @param int $post_id  The ID of the story the schema is for.
   *
   * @return string The encoded JSON or an empty string.
   */

  function fictioneer_build_story_schema( $post_id ) {
    // Abort if...
    if ( ! $post_id ) {
      return '';
    }

    // Setup
    $post = get_post( $post_id );
    $schema = fictioneer_get_schema_node_root();
    $image_data = fictioneer_get_schema_primary_image( $post_id );
    $rating = get_post_meta( $post_id, 'fictioneer_story_rating', true );
    $chapters = fictioneer_get_story_chapter_ids( $post_id );
    $page_title = fictioneer_get_seo_title( $post_id, array( 'skip_cache' => true ) );
    $default_description = fictioneer_first_paragraph_as_excerpt( fictioneer_get_content_field( 'fictioneer_story_short_description', $post_id ) );
    $chapter_count = 0;
    $chapter_list = [];

    if ( ! $post ) {
      return '';
    }

    $page_description = fictioneer_get_seo_description(
      $post_id,
      array( 'default' => $default_description, 'skip_cache' => true )
    );

    // Collect visible chapters
    if ( ! empty( $chapters ) ) {
      foreach ( $chapters as $chapter_id ) {
        if (
          ! get_post_meta( $chapter_id, 'fictioneer_chapter_no_chapter', true ) &&
          ! get_post_meta( $chapter_id, 'fictioneer_chapter_hidden', true ) &&
          get_post_status( $chapter_id ) === 'publish'
        ) {
          $chapter_count += 1;

          $chapter_list[] = array(
            'position' => $chapter_count,
            'url' => get_permalink( $chapter_id )
          );
        }
      }
    }

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
    $article_block = fictioneer_get_schema_node_article(
      'Article', $page_description, $post, $image_data, true
    );

    if ( ! empty( $rating ) ) {
      $article_block['contentRating'] = $rating;
    }

    $schema['@graph'][] = $article_block;

    // List node
    if ( ! empty( $chapter_list ) ) {
      $list_node = array(
        '@type' => 'ItemList',
        'name' => _x( 'Chapters', 'SEO schema story chapters list node name.', 'fictioneer' ),
        'description' => sprintf(
          _x( 'Chapters of %s.', 'SEO schema story chapters list node description.', 'fictioneer' ),
          fictioneer_get_safe_title( $post_id, 'seo-schema-story-chapter-list-node' )
        ),
        'mainEntityOfPage' => ['@id' => '#article'],
        'itemListElement' => []
      );

      foreach ( $chapter_list as $chapter ) {
        $list_node['itemListElement'][] = array(
          '@type' => 'ListItem',
          'position' => $chapter['position'],
          'url' => $chapter['url']
        );
      }

      $schema['@graph'][] = $list_node;
    }

    // Prepare and cache for next time
    $schema = json_encode( $schema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES );
    update_post_meta( $post_id, 'fictioneer_schema', $schema );

    // Return JSON string
    return $schema;
  }
}
