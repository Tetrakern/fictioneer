<?php

namespace Fictioneer\SEO;

defined( 'ABSPATH' ) OR exit;

final class Schema_Node {
  /**
   * Schema graph root node.
   *
   * @since 4.0.0
   * @since 5.34.0 - Moved into Schema_Builder class.
   *
   * @return array Schema root node.
   */

  public static function root() : array {
    return array(
      '@context' => 'https://schema.org/',
      '@graph' => []
    );
  }

  /**
   * Primary image data.
   *
   * @since 4.0.0
   * @since 5.34.0 - Moved into Schema_Builder class.
   *
   * @param \WP_Post $post  Post object.
   * @param array    $args  Optional arguments.
   *
   * @return array|false Array with the URL (0), width (1), and height (2);
   *                     false if no image is available.
   */

  public static function primary_image_data( $post, $args = [] ) {
    $seo_image = \Fictioneer\SEO\Meta::image( $post->ID );

    if ( $seo_image ) {
      // Custom > Thumbnail > Story Thumbnail > OG Default
      return array( $seo_image['url'], $seo_image['width'], $seo_image['height'] );
    }

    $parent_id = (int) ( $post->post_parent ?: ( $args['parent_id'] ?? 0 ) );

    if ( $parent_id && has_post_thumbnail( $parent_id ) ) {
      return wp_get_attachment_image_src( get_post_thumbnail_id( $parent_id ), 'full' );
    }

    return false;
  }

  /**
   * Schema graph website node.
   *
   * @since 4.0.0
   * @since 5.34.0 - Moved into Schema_Builder class.
   *
   * @param string|null $description  Optional. Defaults to site description.
   *
   * @return array Schema website node.
   */

  public static function website( $description = null ) : array {
    $node = array(
      '@type' => 'WebSite',
      '@id' => "#website",
      'name' => get_bloginfo( 'name' ) ?: get_site_url( null, '', 'https' ),
      'url' => get_site_url( null, '', 'https' ),
      'inLanguage' => get_bloginfo( 'language' )
    );

    $description = $description ?: get_bloginfo( 'description' );

    if ( $description ) {
      $node['description'] = $description;
    }

    return $node;
  }

  /**
   * Schema graph webpage node.
   *
   * @since 4.0.0
   * @since 5.34.0 - Moved into Schema_Builder class.
   *
   * @param \WP_Post           $post         Post object.
   * @param array|boolean|null $image_data   Optional. Primary image data or falsy value.
   * @param string|array|null  $type         Optional. Type(s) of the page.
   * @param string|null        $title        Optional. Title of the page.
   * @param string|null        $description  Optional. Description of the page.
   *
   * @return array Schema webpage node.
   */

  public static function webpage( $post, $image_data = null, $type = null, $title = null, $description = null ) : array {
    $node = array(
      '@type' => $type ?: self::webpage_type( $post ),
      '@id' => "#webpage",
      'mainEntity' => array( '@id' => self::main_entity_id( $post ) ),
      'name' => $title ?: self::title( $post ),
      'description' => $description ?: self::description( $post ),
      'url' => get_permalink( $post ),
      'isPartOf' => array( '@id' => "#website" ),
      'inLanguage' => get_bloginfo( 'language' ),
      'datePublished' => get_the_date( 'c', $post ),
      'dateModified' => get_the_modified_date( 'c', $post )
    );

    if ( ! empty( $image_data ) ) {
      $node['primaryImageOfPage'] = array( '@id' => "#primaryimage" );
    }

    return $node;
  }

  /**
   * Schema graph article node.
   *
   * @since 4.0.0
   * @since 5.34.0 - Moved into Schema_Builder class.
   *
   * @param \WP_Post           $post         Post object.
   * @param array|boolean|null $image_data   Optional. Primary image data or falsy value.
   * @param string|array|null  $type         Optional. Type(s) of the article.
   * @param string|null        $title        Optional. Title of the article.
   * @param string|null        $description  Optional. Description of the article.
   *
   * @return array Schema article node.
   */

  public static function article( $post, $image_data = null, $type = null, $title = null, $description = null ) {
    $node = array(
      '@type' => $type ?: self::article_type( $post ),
      '@id' => self::entity_id( $post ),
      'description' => $description ?: self::description( $post ),
      'url' => get_permalink( $post ),
      'mainEntityOfPage' => array( '@id' => "#webpage" ),
      'inLanguage' => get_bloginfo( 'language' ),
      'datePublished' => get_the_date( 'c', $post ),
      'dateModified' => get_the_modified_date( 'c', $post )
    );

    if ( $title = self::article_title( $post, $title ) ) {
      $node[ $title[0] ] = $title[1];
    }

    $author = array(
      '@type' => 'Person',
      '@id' => '#author',
      'url' => get_author_posts_url( $post->post_author ) ?: get_site_url( null, '', 'https' ),
      'name' => get_the_author_meta( 'display_name', $post->post_author ) ?: 'Unknown'
    );

    if ( $post->post_type === 'fcn_collection' ) {
      $node['creator'] = $author;
    } else {
      $node['author'] = $author;
    }

    if ( $image_data ) {
      $node['image'] = array( '@id' => "#primaryimage" );
      $node['thumbnailUrl'] = $image_data[0];
    }

    if ( $keywords = self::keywords( $post ) ) {
      $node['keywords'] = $keywords;
    }

    if ( $post->post_type === 'fcn_story' || $post->post_type === 'fcn_chapter' ) {
      if ( $genres = self::terms( $post, 'fcn_genre' ) ) {
        $node['genre'] = $genres;
      }
    }

    if ( $post->post_type === 'fcn_chapter' ) {
      $node['wordCount'] = fictioneer_get_word_count( $post->ID );

      if ( $post->post_parent ) {
        $node['isPartOf'] = array( '@id' => '#story' );
      }
    }

    if ( $rating = self::content_rating( $post ) ) {
      $node['contentRating'] = $rating;
    }

    return $node;
  }

  /**
   * Schema graph chapter story node.
   *
   * @since 5.34.0
   *
   * @param \WP_Post $chapter  Post object.
   *
   * @return array Schema chapter story node.
   */

  public static function chapter_story( $chapter ) : array {
    if ( $chapter->post_type !== 'fcn_chapter' || ! $chapter->post_parent ) {
      return [];
    }

    $story = get_post( $chapter->post_parent );

    if ( ! $story || $story->post_type !== 'fcn_story' ) {
      return [];
    }

    $node = array(
      '@type' => array( 'CreativeWorkSeries', 'CreativeWork' ),
      '@id' => '#story',
      'name' => fictioneer_get_safe_title( $story, 'seo-schema-story-node' ),
      'url' => get_permalink( $story )
    );

    if ( $rating = self::content_rating( $story ) ) {
      $node['contentRating'] = $rating;
    }

    if ( $genres = self::terms( $story, 'fcn_genre' ) ) {
      $node['genre'] = $genres;
    }

    return $node;
  }

  /**
   * Schema graph chapter item list node.
   *
   * @since 5.34.0
   *
   * @param \WP_Post $story  Post object.
   *
   * @return array Schema chapter item list node.
   */

  public static function chapter_list( $story ) : array {
    $title = _x( 'Chapters', 'SEO schema story chapters list node name.', 'fictioneer' );
    $description = sprintf(
      _x( 'Chapters of %s.', 'SEO schema story chapters list node description.', 'fictioneer' ),
      fictioneer_get_safe_title( $story->ID, 'seo-schema-story-chapter-list-node' )
    );

    $chapter_ids = fictioneer_get_story_chapter_ids( $story->ID );

    if ( empty( $chapter_ids ) ) {
      return [];
    }

    $chapter_ids = array_slice( $chapter_ids, 0, 10 );

    $chapters = self::query(
      'fcn_chapter',
      array(
        'post__in' => $chapter_ids,
        'orderby' => 'post__in',
        'posts_per_page' => count( $chapter_ids )
      )
    );

    if ( empty( $chapters ) ) {
      return [];
    }

    return self::item_list( $story, $chapters, $title, $description );
  }

  /**
   * Schema graph item list node.
   *
   * @since 5.34.0
   *
   * @param \WP_Post    $post         Post object.
   * @param array       $item         Optional. Array of WP_Post objects.
   * @param string|null $title        Optional. Title of the list.
   * @param string|null $description  Optional. Description of the list.
   *
   * @return array Schema item list node.
   */

  public static function item_list( $post, $items = [], $title = null, $description = null ) : array {
    $template = (string) get_page_template_slug( $post->ID );

    if ( $template && empty( $items ) ) {
      $post_type = null;

      switch ( $template ) {
        case 'chapters.php':
          $post_type = 'fcn_chapter';
          break;
        case 'stories.php':
          $post_type = 'fcn_story';
          break;
        case 'recommendations.php':
          $post_type = 'fcn_recommendation';
          break;
        case 'collections.php':
          $post_type = 'fcn_collection';
          break;
      }

      if ( $post_type ) {
        $items = self::query( $post_type );
      }
    }

    if ( empty( $items ) ) {
      return [];
    }

    $node = array(
      '@type' => 'ItemList',
      '@id' => "#list",
      'name' => $title ?: self::list_title( $post ),
      'description' => $description ?: self::list_description( $post ),
      'mainEntityOfPage' => array( '@id' => '#webpage' ),
      'itemListElement' => []
    );

    $position = 0;

    foreach ( $items as $item ) {
      $node['itemListElement'][] = array(
        '@type' => 'ListItem',
        'position' => ++$position,
        'url' => get_permalink( $item->ID )
      );
    }

    $node['numberOfItems'] = $position;

    return $node;
  }

  /**
   * Schema graph primary image node.
   *
   * @since 4.0.0
   * @since 5.34.0 - Moved into Schema_Builder class.
   *
   * @param \WP_Post         $post        Post object.
   * @param array|bool|null  $image_data  Image data with URL (0), width (1), and height (2).
   *
   * @return array|false Schema image node or false on failure.
   */

  public static function primary_image( $post, $image_data = null ) {
    $image_data = empty( $image_data ) ? self::primary_image_data( $post ) : $image_data;

    if ( ! $image_data ) {
      return false;
    }

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

  /**
   * Query WP_Post objects.
   *
   * @since 5.34.0
   *
   * @param string $post_type  Post type.
   * @param array  $args       Optional. Additional query arguments.
   *
   * @return array Array of WP_Post objects.
   */

  private static function query( $post_type, $args = [] ) : array {
    $query_args = array (
      'post_type' => $post_type,
      'post_status' => 'publish',
      'orderby' => 'modified',
      'order' => 'DESC',
      'posts_per_page' => min( get_option( 'posts_per_page', 10 ), 20 ),
      'update_post_term_cache' => false, // Improve performance
      'no_found_rows' => true // Improve performance
    );

    return get_posts( array_merge( $query_args, $args ) );
  }

  /**
   * Object title.
   *
   * @since 5.34.0
   *
   * @param \WP_Post $post  Post object.
   *
   * @return string Object title or default.
   */

  private static function title( $post ) : string {
    return \Fictioneer\SEO\Meta::title( $post->ID, array( 'skip_cache' => true ) );
  }

  /**
   * List title.
   *
   * @since 5.34.0
   *
   * @param \WP_Post $post  Post object.
   *
   * @return string List title or default.
   */

  private static function list_title( $post ) : string {
    switch ( (string) get_page_template_slug( $post->ID ) ) {
      case 'chapters.php':
        return _x( 'Chapters', 'SEO schema chapter list node name.', 'fictioneer' );
      case 'stories.php':
        return _x( 'Stories', 'SEO schema story list node name.', 'fictioneer' );
      case 'recommendations.php':
        return _x( 'Recommendations', 'SEO schema recommendation list node name.', 'fictioneer' );
      case 'collections.php':
        return _x( 'Collections', 'SEO schema collection list node name.', 'fictioneer' );
      default:
        return _x( 'Items', 'SEO schema generic list node name.', 'fictioneer' );
    }
  }

  /**
   * Object description.
   *
   * @since 5.34.0
   *
   * @param \WP_Post $post  Post object.
   *
   * @return string Object description or default.
   */

  private static function description( $post ) : string {
    return \Fictioneer\SEO\Meta::description( $post->ID, array( 'skip_cache' => true ) );
  }

  /**
   * List description.
   *
   * @since 5.34.0
   *
   * @param \WP_Post $post  Post object.
   *
   * @return string List description or default.
   */

  private static function list_description( $post ) : string {
    switch ( (string) get_page_template_slug( $post->ID ) ) {
      case 'chapters.php':
        return sprintf(
          _x( 'List of chapters on %s.', 'SEO schema chapter list node description.', 'fictioneer' ),
          get_bloginfo( 'name' )
        );
      case 'stories.php':
        return sprintf(
          _x( 'List of stories on %s.', 'SEO schema story list node description.', 'fictioneer' ),
          get_bloginfo( 'name' )
        );
      case 'recommendations.php':
        return sprintf(
          _x( 'List of recommendations on %s.', 'SEO schema recommendation list node description.', 'fictioneer' ),
          get_bloginfo( 'name' )
        );
      case 'collections.php':
        return sprintf(
          _x( 'List of collections on %s.', 'SEO schema collection list node description.', 'fictioneer' ),
          get_bloginfo( 'name' )
        );
      default:
        return sprintf(
          _x( 'Items on %s.', 'SEO schema generic list node description.', 'fictioneer' ),
          get_bloginfo( 'name' )
        );
    }
  }

  /**
   * Webpage node type.
   *
   * @since 5.34.0
   *
   * @param \WP_Post $post  Post object.
   *
   * @return string|array Webpage node type(s).
   */

  private static function webpage_type( $post ) {
    $template = (string) get_page_template_slug( $post->ID );

    switch ( $template ) {
      case 'chapters.php':
      case 'stories.php':
      case 'recommendations.php':
      case 'collections.php':
        return ['CollectionPage', 'WebPage'];
    }

    if ( $post->post_type === 'fcn_story' || $post->post_type === 'fcn_collection' ) {
      return ['CollectionPage', 'WebPage'];
    }

    return 'WebPage';
  }

  /**
   * Article node type.
   *
   * @since 5.34.0
   *
   * @param \WP_Post $post  Post object.
   *
   * @return string|array Article node type(s).
   */

  private static function article_type( $post ) {
    if ( $post->post_type === 'fcn_story' ) {
      return ['CreativeWorkSeries', 'CreativeWork'];
    }

    if ( $post->post_type === 'fcn_chapter' ) {
      return ['Chapter', 'CreativeWork'];
    }

    if ( $post->post_type === 'fcn_collection' ) {
      return ['Collection', 'CreativeWork'];
    }

    return 'Article';
  }

  /**
   * Article name or headline.
   *
   * @since 5.34.0
   *
   * @param \WP_Post $post      Post object.
   * @param string|null $title  Optional. Title to be used.
   *
   * @return array Tuple with a node key (0) and value (1).
   */

  private static function article_title( $post, $title = null ) : array {
    $title = $title ?: fictioneer_get_safe_title( $post, 'seo-schema-article-node' );
    $key = 'headline';

    if ( in_array( $post->post_type, ['fcn_story', 'fcn_chapter', 'fcn_collection'], true ) ) {
      $key = 'name';
    }

    return [ $key, $title ];
  }

  /**
   * Main entity ID.
   *
   * @since 5.34.0
   *
   * @param \WP_Post $post  Post object.
   *
   * @return string Main entity ID.
   */

  private static function main_entity_id( $post ) : string {
    $template = (string) get_page_template_slug( $post->ID );

    switch ( $template ) {
      case 'chapters.php':
      case 'stories.php':
      case 'recommendations.php':
      case 'collections.php':
        return '#list';
    }

    if ( $post->post_type === 'fcn_story' ) {
      return '#story';
    }

    if ( $post->post_type === 'fcn_chapter' ) {
      return '#chapter';
    }

    if ( $post->post_type === 'fcn_collection' ) {
      return '#collection';
    }

    return '#article';
  }

  /**
   * Entity ID.
   *
   * @since 5.34.0
   *
   * @param \WP_Post $post  Post object.
   *
   * @return string Entity ID.
   */

  private static function entity_id( $post ) : string {
    if ( $post->post_type === 'fcn_story' ) {
      return '#story';
    }

    if ( $post->post_type === 'fcn_chapter' ) {
      return '#chapter';
    }

    if ( $post->post_type === 'fcn_collection' ) {
      return '#collection';
    }

    return '#article';
  }

  /**
   * Post content rating.
   *
   * @since 5.34.0
   *
   * @param \WP_Post $post  Post object.
   *
   * @return string Post content rating.
   */

  private static function content_rating( $post ) {
    if ( $post->post_type === 'fcn_chapter' ) {
      $rating = get_post_meta( $post->ID, 'fictioneer_chapter_rating', true );

      if ( $rating ) {
        return $rating;
      }

      if ( $post->post_parent ) {
        return get_post_meta( $post->post_parent, 'fictioneer_story_rating', true );
      }

      return '';
    }

    if ( $post->post_type === 'fcn_story' ) {
      return get_post_meta( $post->ID, 'fictioneer_story_rating', true );
    }

    return '';
  }

  /**
   * Keywords derived from post categories and tags (max. 16).
   *
   * @since 5.34.0
   *
   * @param \WP_Post $post  Post object.
   *
   * @return string[] Keywords.
   */

  private static function keywords( $post ) : array {
    $keywords = [];

    $post_ids = [ $post->ID ];

    if ( $post->post_parent > 0 ) {
      $post_ids[] = $post->post_parent;
    }

    $default_category = (int) get_option( 'default_category' );

    foreach ( $post_ids as $id ) {
      $categories = get_the_category( $id );

      if ( is_array( $categories ) ) {
        foreach ( $categories as $cat ) {
          if ( (int) $cat->term_id === $default_category ) {
            continue;
          }

          $keywords[] = $cat->name;
        }
      }

      $tags = get_the_tags( $id );

      if ( is_array( $tags ) ) {
        $keywords = array_merge( $keywords, wp_list_pluck( $tags, 'name' ) );
      }
    }

    $keywords = array_unique( $keywords );

    $filter_title = mb_strtolower( trim( $post->post_title ) );

    $keywords = array_filter(
      $keywords,
      function( $keyword ) use ( $filter_title ) {
        return mb_strtolower( trim( $keyword ) ) !== $filter_title;
      }
    );

    sort( $keywords, SORT_STRING | SORT_FLAG_CASE );

    return array_slice( array_values( $keywords ), 0, 16 );
  }

  /**
   * Terms of a type.
   *
   * @since 5.34.0
   *
   * @param \WP_Post $post      Post object.
   * @param string   $taxonomy  Taxonomy name.
   *
   * @return string[] Term names.
   */

  private static function terms( $post, $taxonomy ) : array {
    if ( $post->post_type === 'fcn_chapter' && $post->post_parent ) {
      $post = get_post( $post->post_parent );

      if ( ! $post ) {
        return [];
      }
    }

    $terms = get_the_terms( $post, $taxonomy );

    if ( ! is_array( $terms ) || is_wp_error( $terms ) ) {
      return [];
    }

    $terms = array_unique( wp_list_pluck( $terms, 'name' ) );

    sort( $terms, SORT_STRING | SORT_FLAG_CASE );

    return array_values( $terms );
  }
}
