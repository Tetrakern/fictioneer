<?php

// =============================================================================
// INCLUDES
// =============================================================================

require_once( __DIR__ . '/schemas/_story_schema.php');
require_once( __DIR__ . '/schemas/_stories_schema.php');
require_once( __DIR__ . '/schemas/_chapter_schema.php');
require_once( __DIR__ . '/schemas/_chapters_schema.php');
require_once( __DIR__ . '/schemas/_post_schema.php');
require_once( __DIR__ . '/schemas/_recommendation_schema.php');
require_once( __DIR__ . '/schemas/_recommendations_schema.php');
require_once( __DIR__ . '/schemas/_collections_schema.php');

// =============================================================================
// SCHEMA ROOT NODE
// =============================================================================

if ( ! function_exists( 'fictioneer_get_schema_node_root' ) ) {
  /**
   * Return schema graph root node
   *
   * @since Fictioneer 4.0
   */

  function fictioneer_get_schema_node_root() {
    return array(
      '@context' => 'https://schema.org/',
      '@graph' => []
    );
  }
}

// =============================================================================
// GET SCHEMA PRIMARY IMAGE
// =============================================================================

if ( ! function_exists( 'fictioneer_get_schema_primary_image' ) ) {
  /**
   * Returns primary image for schema graph
   *
   * @since Fictioneer 4.0
   * @link https://developer.wordpress.org/reference/functions/wp_get_attachment_image_src/
   *
   * @param int|null $post_id  The post ID the primary image is from.
   * @param array    $args     Optional arguments.
   *
   * @return array|boolean Either the array with the URL (0), width (1), and height (2),
   *                       or false if no image was found.
   */

  function fictioneer_get_schema_primary_image( $post_id = null, $args = [] ) {
    // Setup
    $post_id = empty( $post_id ) ? get_queried_object_id() : $post_id;
    $seo_image = fictioneer_get_seo_image( $post_id );

    // Try SEO image... (custom > thumbnail > story thumbnail > OG default)
    if ( $seo_image ) {
      return array( $seo_image['url'], $seo_image['width'], $seo_image['height'] );
    }

    // If no image has been found and a specific parent has been provided...
    if ( isset( $args['parent_id'] ) && has_post_thumbnail( $args['parent_id'] ) ) {
      return wp_get_attachment_image_src( get_post_thumbnail_id( $args['parent_id'] ), 'full' );
    }

    // No image found
    return false;
  }
}

// =============================================================================
// GET SCHEMA NODE - WEBSITE
// =============================================================================

if ( ! function_exists( 'fictioneer_get_schema_node_website' ) ) {
  /**
   * Returns the website node for the schema graph
   *
   * @since Fictioneer 4.0
   *
   * @param string|null $description  The website description.
   *
   * @return array Website schema node.
   */

  function fictioneer_get_schema_node_website( $description = null ) {
    // Build and return node
    return array(
      '@type' => 'WebSite',
      '@id' => "#website",
      'name' => get_bloginfo( 'name' ),
      'description' => empty( $description ) ? get_bloginfo( 'description' ) : $description,
      'url' => get_site_url( null, '', 'https' ),
      'inLanguage' => get_bloginfo( 'language' )
    );
  }
}

// =============================================================================
// GET SCHEMA NODE - PRIMARY IMAGE
// =============================================================================

if ( ! function_exists( 'fictioneer_get_schema_node_image' ) ) {
  /**
   * Returns the primary image node for the schema graph
   *
   * @since Fictioneer 4.0
   *
   * @param array|boolean|null $image_data  The image data with URL (0), width (1), and height (2).
   *
   * @return array Image schema node.
   */

  function fictioneer_get_schema_node_image( $image_data = null ) {
    // Setup
    $image_data = empty( $image_data ) ? fictioneer_get_schema_primary_image() : $image_data;

    // Build and return node
    return array(
      '@type' => 'ImageObject',
      '@id' => "#primaryimage",
      'inLanguage' => get_bloginfo( 'language' ),
      'url' => $image_data[0],
      'contentUrl' => $image_data[0],
      'height' => $image_data[2],
      'width' => $image_data[1]
    );
  }
}

// =============================================================================
// GET SCHEMA NODE - WEBPAGE
// =============================================================================

if ( ! function_exists( 'fictioneer_get_schema_node_webpage' ) ) {
  /**
   * Returns the webpage node for the schema graph
   *
   * @since Fictioneer 4.0
   *
   * @param string|array       $type         The type(s) of the page, which can be an array.
   * @param string             $name         Title of the page.
   * @param string             $description  Description of the page.
   * @param int|null           $post_id      The post ID of the page. Defaults to current ID.
   * @param array|boolean|null $image_data   The image data or falsy value.
   *
   * @return array Webpage schema node.
   */

  function fictioneer_get_schema_node_webpage( $type, $name, $description, $post_id = null, $image_data = null ) {
    // Setup
    $post_id = empty( $post_id ) ? get_queried_object_id() : $post_id;

    // Build node
    $webpage_node = array(
      '@type' => $type,
      '@id' => "#webpage",
      'name' => $name,
      'description' => $description,
      'url' => get_the_permalink( $post_id ),
      'isPartOf' => array( '@id' => "#website" ),
      'inLanguage' => get_bloginfo( 'language' ),
      'datePublished' => get_the_date( 'c', $post_id ),
      'dateModified' => get_the_modified_date( 'c', $post_id )
    );

    // Account for primary image (if any)
    if ( ! empty( $image_data ) ) {
      $webpage_node['primaryImageOfPage'] = array( '@id' => "#primaryimage" );
    }

    // Return node
    return $webpage_node;
  }
}

// =============================================================================
// GET SCHEMA NODE - ARTICLE
// =============================================================================

if ( ! function_exists( 'fictioneer_get_schema_node_article' ) ) {
  /**
   * Returns the article node for the schema graph
   *
   * @since Fictioneer 4.0
   *
   * @param string|array       $type         The type(s) of the article, which can be an array.
   * @param string             $description  Description of the article.
   * @param WP_Post|null       $post         The post object of the article. Defaults to current post.
   * @param array|boolean|null $image_data   The image data or falsy value.
   * @param boolean            $no_cat       Whether to include the category or not.
   *
   * @return array Article schema node.
   */

  function fictioneer_get_schema_node_article( $type, $description, $post = null, $image_data = null, $no_cat = false ) {
    // Setup
    $post = empty( $post ) ? get_queried_object() : $post;
    $author = get_the_author_meta( 'display_name', $post->post_author );
    $tags = get_the_tags( $post->ID );
    $categories = $no_cat ? [] : get_the_category( $post->ID );
    $taxonomies = is_array( $tags ) ? array_merge( $categories, $tags ) : $categories;
    $keywords = [];

    // Collect keywords (if any)
    if ( is_array( $taxonomies ) ) {
      foreach ( $taxonomies as $tax ) {
        $keywords[] = $tax->name;
      }
    }

    // Build node
    $article_node = array(
      '@type' => $type,
      '@id' => "#article",
      'headline' => fictioneer_get_safe_title( $post->ID ),
      'description' => $description,
      'url' => get_the_permalink( $post->ID ),
      'author' => array(
        '@type' => 'Person',
        'url' => get_site_url( null, '', 'https' ),
        'name' => $author
      ),
      'isPartOf' => array( '@id' => "#webpage" ),
      'mainEntityOfPage' => array( '@id' => "#webpage" ),
      'inLanguage' => get_bloginfo( 'language' ),
      'datePublished' => get_the_date( 'c', $post->ID ),
      'dateModified' => get_the_modified_date( 'c', $post->ID )
    );

    // Account for primary image (if any)
    if ( $image_data ) {
      $article_node['image'] = array(
        '@id' => "#primaryimage"
      );

      $article_node['thumbnailUrl'] = $image_data[0];
    }

    // Add keywords (if any)
    if ( ! empty( $keywords ) ) {
      $article_node['keywords'] = $keywords;
    }

    // Return node
    return $article_node;
  }
}

// =============================================================================
// GET SCHEMA NODE - LIST
// =============================================================================

if ( ! function_exists( 'fictioneer_get_schema_node_list' ) ) {
  /**
   * Returns the list node for the schema graph
   *
   * @since Fictioneer 4.0
   *
   * @param array  $list         List of WP_Post objects.
   * @param string $name         Name of the list.
   * @param string $description  Description of the list.
   * @param string $part_of      The node ID the list belongs to. Defaults to '#webpage'.
   *
   * @return array|null List schema node.
   */

  function fictioneer_get_schema_node_list( $list, $name, $description, $part_of = '#webpage' ) {
    // Setup
    $list_node = null;

    // Add list node if there are items to loop
    if ( ! empty( $list ) ) {
      // Counter
      $position = 0;

      // Build list node
      $list_node = array(
        '@type' => 'ItemList',
        'name' => $name,
        'description' => $description,
        'mainEntityOfPage' => array( '@id' => $part_of ),
        'itemListElement' => []
      );

      foreach ( $list as $post_object ) {
        $position += 1;

        $list_node['itemListElement'][] = array(
          '@type' => 'ListItem',
          'position' => $position,
          'url' => get_the_permalink( $post_object->ID )
        );
      }

      // Add number of items
      $list_node['numberOfItems'] = $position;
    }

    // Return node
    return $list_node;
  }
}

// =============================================================================
// OUTPUT SCHEMA
// =============================================================================

/**
 * Output schema graph on selected pages
 *
 * @since Fictioneer 5.0
 *
 * @param int|null $post_id  Optional. The current post ID.
 */

function fictioneer_output_schemas( $post_id = null ) {
  // Abort if...
  if ( is_archive() || is_search() || is_home() || is_front_page() ) {
    return;
  }

  // Setup
  $post_id = empty( $post_id ) ? get_queried_object_id() : $post_id;
  $schema = get_post_meta( $post_id, 'fictioneer_schema', true ) ?? false;

  // No schema found...
  if ( ! $schema ) {
    // Look at template first...
    switch ( get_page_template_slug() ) {
      case 'chapters.php':
        $schema = fictioneer_build_chapters_schema( $post_id );
        break;
      case 'stories.php':
        $schema = fictioneer_build_stories_schema( $post_id );
        break;
      case 'recommendations.php':
        $schema = fictioneer_build_recommendations_schema( $post_id );
        break;
      case 'collections.php':
        $schema = fictioneer_build_collections_schema( $post_id );
        break;
    }

    // Look at post type if still no schema...
    if ( ! $schema ) {
      switch ( get_post_type() ) {
        case 'post':
          $schema = fictioneer_build_post_schema( $post_id );
          break;
        case 'fcn_story':
          $schema = fictioneer_build_story_schema( $post_id );
          break;
        case 'fcn_chapter':
          $schema = fictioneer_build_chapter_schema( $post_id );
          break;
        case 'fcn_recommendation':
          $schema = fictioneer_build_recommendation_schema( $post_id );
          break;
      }
    }
  }

  // If schema has been found, echo for selected post types
  if ( $schema ) {
    echo $schema ? '<script type="application/ld+json">' . $schema . '</script>' : '';
  }
}

if ( get_option( 'fictioneer_enable_seo' ) && ! fictioneer_seo_plugin_active() ) {
  add_action( 'wp_head', 'fictioneer_output_schemas' );
}

?>
