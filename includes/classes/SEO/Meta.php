<?php

namespace Fictioneer\SEO;

defined( 'ABSPATH' ) OR exit;

final class Meta {
  private static array $data = array(
    'context' => null,
    'site_name' => null,
    'site_description' => null,
    'locale' => null,
    'default_og_image_id' => null,
    'seo_fields' => [],
    'seo_cache' => []
  );

  /**
   * Initialize hooks.
   *
   * @since 5.34.0
   */

  public static function init() : void {
    if ( ! get_option( 'fictioneer_enable_seo' ) || fictioneer_seo_plugin_active() ) {
      return;
    }

    remove_action( 'wp_head', 'rel_canonical' );
    add_action( 'wp_head', array( self::class, 'render' ), 5 );
  }

  /**
   * SEO title.
   *
   * @since 4.0.0
   * @since 5.9.4 - Refactored meta caching.
   * @since 5.34.0 - Moved into SEO/Meta class.
   *
   * @param int|null $post_id  Optional. Post ID.
   * @param array    $args     Optional. Additional Arguments.
   *
   * @return string SEO title.
   */

  public static function title( $post_id = null, $args = [] ) : string {
    $args = wp_parse_args( $args, array( 'skip_cache' => false, 'default' => '', ) );

    $context = self::context();
    $skip_cache = (bool) $args['skip_cache'];
    $default = trim( (string) $args['default'] );

    if ( $context['title'] !== '' ) {
      return $context['title'];
    }

    $post_id = $post_id ? (int) $post_id : $context['post_id'];

    if ( ! $skip_cache ) {
      $meta_cache = self::get_cache( $post_id );

      if ( ! empty( $meta_cache['title'] ) ) {
        return $meta_cache['title'];
      }
    }

    $seo_fields = self::seo_fields( $post_id );
    $seo_title = (string) ( $seo_fields['title'] ?? '' ); // Make extra sure
    $site_name = self::site_name();
    $title = fictioneer_get_safe_title( $post_id, 'seo-title' );

    if ( $default === '' ) {
      $default = $title;
    }

    if ( $context['is_frontpage'] && $default === '' ) {
      $default = $site_name;
    }

    if ( $seo_title !== '' ) {
      $seo_title = strtr(
        $seo_title,
        array(
          '{{title}}' => $title,
          '{{site}}' => $site_name
        )
      );
    }

    if ( $seo_title === '' && $default !== '' ) {
      $seo_title = $default;
    }

    if ( $seo_title === '' ) {
      $seo_title = $site_name;
    }

    $seo_title = esc_html( trim( wp_strip_all_tags( $seo_title ) ) );

    if ( ! $skip_cache ) {
      self::update_cached_value( $post_id, 'title', $seo_title );
    }

    return $seo_title;
  }

  /**
   * SEO description.
   *
   * @since 4.0.0
   * @since 5.9.4
   * @since 5.34.0 - Moved into SEO/Meta class.
   *
   * @param int|null $post_id  Optional. Post ID.
   * @param array    $args     Optional. Additional Arguments.
   *
   * @return string SEO description.
   */

  public static function description( $post_id = null, $args = [] ) : string {
    $args = wp_parse_args( $args, array( 'skip_cache' => false, 'default' => '', ) );

    $context = self::context();
    $skip_cache = (bool) $args['skip_cache'];
    $default = trim( (string) $args['default'] );

    if ( $context['description'] !== '' ) {
      return $context['description'];
    }

    $post_id = $post_id ? (int) $post_id : $context['post_id'];

    if ( ! $skip_cache ) {
      $meta_cache = self::get_cache( $post_id );

      if ( ! empty( $meta_cache['description'] ) ) {
        return $meta_cache['description'];
      }
    }

    $seo_fields = self::seo_fields( $post_id );
    $seo_description = (string) ( $seo_fields['description'] ?? '' ); // Make extra sure
    $site_name = self::site_name();
    $title = fictioneer_get_safe_title( $post_id, 'seo-title' );
    $excerpt = wp_strip_all_tags( get_the_excerpt( $post_id ), true );
    $excerpt = fictioneer_truncate( $excerpt, 155 );

    if ( ( $context['is_frontpage'] || ! $post_id ) && $default === '' ) {
      $default = self::site_description();
    }

    if ( $default === '' ) {
      $default = $excerpt;
    }

    if ( get_post_type( $post_id ) === 'fcn_recommendation' ) {
      $one_sentence = trim( get_post_meta( $post_id, 'fictioneer_recommendation_one_sentence', true ) );

      if ( $one_sentence !== '' ) {
        $default = $one_sentence;
      }
    }

    if ( $seo_description !== '' ) {
      $seo_description = strtr(
        $seo_description,
        array(
          '{{excerpt}}' => $excerpt ?? '',
          '{{title}}' => $title,
          '{{site}}' => $site_name
        )
      );
    }

    if ( $seo_description === '' && $default !== '' ) {
      $seo_description = $default;
    }

    if ( $seo_description === '' ) {
      $seo_description = self::site_description();
    }

    $seo_description = esc_html( trim( wp_strip_all_tags( $seo_description ) ) );

    if ( ! $skip_cache ) {
      self::update_cached_value( $post_id, 'description', $seo_description );
    }

    return $seo_description;
  }

  /**
   * SEO image data array.
   *
   * @since 4.0.0
   * @since 5.9.4
   * @since 5.34.0 - Moved into SEO/Meta class.
   *
   * @param int|null $post_id  Optional. Post ID.
   *
   * @return array|null Data of the image or null if none has been found.
   */

  public static function image( $post_id = null ) {
    $context = self::context();

    if ( $context['use_default_image'] ) {
      return self::build_image_array( self::default_og_image_id() );
    }

    $post_id = $post_id ? (int) $post_id : $context['post_id'];

    if ( ! $post_id ) {
      return self::build_image_array( self::default_og_image_id() );
    }

    $meta_cache = self::get_cache( $post_id );

    if ( ! empty( $meta_cache['og_image'] ?? [] ) && is_array( $meta_cache['og_image'] ) ) {
      return $meta_cache['og_image'];
    }

    $seo_fields = self::seo_fields( $post_id );
    $image_id = $seo_fields['og_image_id'] ?? 0; // Make extra sure
    $image_id = wp_attachment_is_image( $image_id ) ? (int) $image_id : 0;

    if ( ! $image_id && has_post_thumbnail( $post_id ) ) {
      $image_id = (int) get_post_thumbnail_id( $post_id );
    }

    if ( ! $image_id && get_post_type( $post_id ) === 'fcn_chapter' ) {
      $story_id = fictioneer_get_chapter_story_id( $post_id );

      if ( $story_id && has_post_thumbnail( $story_id ) ) {
        $image_id = (int) get_post_thumbnail_id( $story_id );
      }
    }

    $default_id = self::default_og_image_id();

    if ( ! $image_id ) {
      $image_id = $default_id;
    }

    $image = self::build_image_array( $image_id );

    if ( $image && $image_id !== $default_id ) {
      self::update_cached_value( $post_id, 'og_image', $image );
    }

    return $image;
  }

  /**
   * Render HTML <head> SEO meta.
   *
   * @since 5.0.0
   * @since 5.34.0 - Moved into SEO/Meta class.
   */

  public static function render() : void {
    $context = self::context();

    $og_image = self::image( $context['post_id'] );
    $og_title = esc_attr( self::title( $context['post_id'] ) );
    $og_description = esc_attr( self::description( $context['post_id'] ) );
    $og_url = esc_url( $context['canonical_url'] );

    $article_author = '';
    $article_twitter = '';

    if ( $context['show_author'] && $context['post_author'] ) {
      $article_author = get_the_author_meta( 'display_name', $context['post_author'] );
      $article_twitter = get_the_author_meta( 'twitter', $context['post_author'] );

      if ( $context['post_type'] === 'fcn_recommendation' ) {
        $article_author = get_post_meta( $context['post_id'], 'fictioneer_recommendation_author', true ) ?? $article_author;
      }
    }

    // Start HTML ---> ?>
    <link rel="canonical" href="<?php echo $og_url; ?>">
    <meta name="description" content="<?php echo $og_description; ?>">
    <meta property="og:locale" content="<?php echo esc_attr( self::locale() ); ?>">
    <meta property="og:type" content="<?php echo esc_attr( $context['is_article'] ? 'article' : 'website' ); ?>">
    <meta property="og:title" content="<?php echo $og_title; ?>">
    <meta property="og:description" content="<?php echo $og_description; ?>">
    <meta property="og:url" content="<?php echo $og_url; ?>">
    <meta property="og:site_name" content="<?php echo esc_attr( self::site_name() ); ?>">
    <?php // <--- End HTML
      if ( ! $context['is_aggregated'] && $context['is_article'] ) {
        echo '<meta property="article:published_time" content="', esc_attr( get_the_date( 'c' ) ), '">';
        echo '<meta property="article:modified_time" content="', esc_attr( get_the_modified_date( 'c' ) ), '">';

        if ( $context['show_author'] && $article_author !== '' ) {
          $all_authors = self::article_authors(
            $context['post_id'],
            $context['post_type'],
            $context['post_author'],
            $article_author
          );

          foreach ( $all_authors as $author_item ) {
            echo '<meta property="article:author" content="', esc_attr( $author_item ), '">';
          }
        }
      }

      if ( $context['post_type'] === 'fcn_chapter' && $context['chapter_story_id'] ) {
        $story_title = fictioneer_get_safe_title( $context['chapter_story_id'] );

        if ( $story_title ) {
          echo '<meta property="article:section" content="', esc_attr( $story_title ), '">';
        }
      }

      if ( is_array( $og_image ) && count( $og_image ) >= 4 ) {
        echo '<meta property="og:image" content="', esc_url( $og_image['url'] ), '">';
        echo '<meta property="og:image:width" content="', esc_attr( $og_image['width'] ), '">';
        echo '<meta property="og:image:height" content="', esc_attr( $og_image['height'] ), '">';
        echo '<meta property="og:image:type" content="', esc_attr( $og_image['type'] ), '">';
        echo '<meta name="twitter:image" content="', esc_url( $og_image['url'] ), '">';
      }
    // Start HTML ---> ?>
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="<?php echo $og_title; ?>">
    <meta name="twitter:description" content="<?php echo $og_description; ?>">
    <?php // <--- End HTML

    if ( $context['show_author'] && $article_twitter !== '' ) {
      $tw = '@' . ltrim( $article_twitter, '@' );

      echo '<meta name="twitter:creator" content="', esc_attr( $tw ), '">';
      echo '<meta name="twitter:site" content="', esc_attr( $tw ), '">';
    }
  }

  /**
   * Resolve current page context once per request.
   *
   * @since 5.34.0
   *
   * @return array Context data of the request.
   */

  private static function context() : array {
    if ( is_array( self::$data['context'] ) ) {
      return self::$data['context'];
    }

    global $wp;
    global $post;

    $post_id = (int) get_queried_object_id();
    $post_type = (string) get_post_type();
    $post_author = (int) ( $post->post_author ?? 0 );

    $is_frontpage = is_front_page() || is_home();
    $is_aggregated = is_archive() || is_search();
    $is_page = is_page() || $is_frontpage;
    $is_article = ( ! $is_page ) && in_array( $post_type, ['fcn_story', 'fcn_chapter', 'fcn_recommendation', 'post'], true );
    $show_author = $is_article && is_single() && ! $is_frontpage && ! $is_aggregated;

    $chapter_story_id = ( $post_type === 'fcn_chapter' && $post_id ) ? (int) fictioneer_get_chapter_story_id( $post_id ) : 0;

    $canonical_url = wp_get_canonical_url();

    if ( is_archive() ) {
      $canonical_url = home_url( $wp->request );
    } elseif ( is_search() ) {
      $canonical_url = add_query_arg( 's', '', home_url( $wp->request ) );
    }

    $canonical_url = $canonical_url ? $canonical_url : get_permalink();

    $title = '';
    $description = '';
    $use_default_image = $is_frontpage;

    if ( is_search() ) {
      $use_default_image = true;
      $title = esc_html( _x( 'Search Results', 'SEO search results title.', 'fictioneer' ) );
      $description = esc_html(
        sprintf( _x( 'Search results on %s.', 'Search page SEO description.', 'fictioneer' ), self::site_name() )
      );
    } elseif ( is_author() ) {
      $use_default_image = true;
      $author = get_userdata( (int) get_queried_object_id() );
      $title = $author
        ? esc_html( sprintf( _x( 'Author: %s', 'SEO author page title.', 'fictioneer' ), $author->display_name ) )
        : esc_html( _x( 'Author', 'SEO fallback title for author pages.', 'fictioneer' ) );

      if ( $author && ! empty( $author->user_description ) ) {
        $description = esc_html( $author->user_description );
      } else {
        $description = esc_html(
          sprintf( _x( 'Author on %s.', 'Fallback SEO description for author pages.', 'fictioneer' ), self::site_name() )
        );
      }
    } elseif ( is_archive() ) {
      $use_default_image = true;
      $title = self::archive_title();
      $description = self::archive_description();
    }

    self::$data['context'] = array(
      'post_id' => $post_id,
      'post_type' => $post_type,
      'post_author' => $post_author,
      'is_frontpage' => $is_frontpage,
      'is_aggregated' => $is_aggregated,
      'is_article' => $is_article,
      'show_author' => $show_author,
      'chapter_story_id' => $chapter_story_id,
      'canonical_url' => $canonical_url,
      'title' => $title,
      'description' => $description,
      'use_default_image' => $use_default_image
    );

    return self::$data['context'];
  }

  /**
   * Return site name.
   *
   * @since 5.34.0
   *
   * @return string Site name.
   */

  private static function site_name() : string {
    if ( self::$data['site_name'] === null ) {
      self::$data['site_name'] = (string) get_bloginfo( 'name' );
    }

    return self::$data['site_name'];
  }

  /**
   * Return site description.
   *
   * @since 5.34.0
   *
   * @return string Site description.
   */

  private static function site_description() : string {
    if ( self::$data['site_description'] === null ) {
      self::$data['site_description'] = (string) get_bloginfo( 'description' )
        ?: sprintf( _x( 'Read stories on %s.', 'Frontpage default description.', 'fictioneer' ), self::site_name() );
    }

    return self::$data['site_description'];
  }

  /**
   * Return site locale.
   *
   * @since 5.34.0
   *
   * @return string Site locale.
   */

  private static function locale() : string {
    if ( self::$data['locale'] === null ) {
      self::$data['locale'] = (string) get_locale();
    }

    return self::$data['locale'];
  }

  /**
   * Return default OG image ID.
   *
   * @since 5.34.0
   *
   * @return int Default OG image ID.
   */

  private static function default_og_image_id() : int {
    if ( self::$data['default_og_image_id'] === null ) {
      self::$data['default_og_image_id'] = (int) get_theme_mod( 'og_image' );
    }

    return self::$data['default_og_image_id'];
  }

  /**
   * Return current SEO meta fields.
   *
   * @since 5.34.0
   *
   * @param int $post_id  Post ID.
   *
   * @return array SEO meta fields (title, description, og_image_id).
   */

  private static function seo_fields( $post_id ) : array {
    if ( isset( self::$data['seo_fields'][ $post_id ] ) ) {
      return self::$data['seo_fields'][ $post_id ];
    }

    $seo_fields = get_post_meta( $post_id, 'fictioneer_seo_fields', true );

    if (
      ! is_array( $seo_fields ) ||
      ! isset( $seo_fields['title'], $seo_fields['description'], $seo_fields['og_image'] )
    ) {
      $seo_fields = array( 'title' => '', 'description' => '', 'og_image' => [] );
    }

    self::$data['seo_fields'][ $post_id ] = $seo_fields;

    return $seo_fields;
  }

  /**
   * Return singular archive title.
   *
   * @since 5.34.0
   *
   * @return string Singular archive title.
   */

  private static function archive_title() : string {
    if ( is_category() ) {
      return esc_html(
        sprintf( _x( 'Category: %s', 'SEO post category title.', 'fictioneer' ), single_cat_title( '', false ) )
      );
    }

    if ( is_tag() ) {
      return esc_html(
        sprintf( _x( 'Tag: %s', 'SEO post tag title.', 'fictioneer' ), single_tag_title( '', false ) )
      );
    }

    if ( is_tax( 'fcn_character' ) ) {
      return esc_html(
        sprintf( _x( 'Character: %s', 'SEO character taxonomy title.', 'fictioneer' ), single_term_title( '', false ) )
      );
    }

    if ( is_tax( 'fcn_fandom' ) ) {
      return esc_html(
        sprintf( _x( 'Fandom: %s', 'SEO fandom taxonomy title.', 'fictioneer' ), single_term_title( '', false ) )
      );
    }

    if ( is_tax( 'fcn_genre' ) ) {
      return esc_html(
        sprintf( _x( 'Genre: %s', 'SEO genre taxonomy title.', 'fictioneer' ), single_term_title( '', false ) )
      );
    }

    return esc_html( _x( 'Archive', 'SEO fallback archive title.', 'fictioneer' ) );
  }

  /**
   * Return singular archive description.
   *
   * @since 5.34.0
   *
   * @return string Singular archive description.
   */

  private static function archive_description() : string {
    if ( is_category() ) {
      return esc_html(
        sprintf( __( 'Archive of all posts in the %s category.', 'fictioneer' ), single_cat_title( '', false ) )
      );
    }

    if ( is_tag() ) {
      return esc_html(
        sprintf( __( 'Archive of all posts with the %s tag.', 'fictioneer' ), single_tag_title( '', false ) )
      );
    }

    if ( is_tax( 'fcn_character' ) ) {
      return esc_html(
        sprintf( __( 'Archive of all posts with the character %s.', 'fictioneer' ), single_term_title( '', false ) )
      );
    }

    if ( is_tax( 'fcn_fandom' ) ) {
      return esc_html(
        sprintf( __( 'Archive of all posts in the %s fandom.', 'fictioneer' ), single_term_title( '', false ) )
      );
    }

    if ( is_tax( 'fcn_genre' ) ) {
      return esc_html(
        sprintf( __( 'Archive of all posts with the %s genre.', 'fictioneer' ), single_term_title( '', false ) )
      );
    }

    return esc_html( sprintf( __( 'Archived posts on %s.', 'fictioneer' ), self::site_name() ) );
  }

  /**
   * Return all article authors.
   *
   * @since 5.34.0
   *
   * @param int    $post_id         Post ID.
   * @param string $post_type       Post type.
   * @param int    $post_author_id  Post author ID.
   * @param string $article_author  Name of article author.
   *
   * @return array Array of all article author names or URls (if available).
   */

  private static function article_authors( $post_id, $post_type, $post_author_id, $article_author ) : array {
    $author_url = get_the_author_meta( 'url', $post_author_id ) ?: get_author_posts_url( $post_author_id );
    $all_authors = $author_url ? [ $author_url ] : [ $article_author ];

    $co_authors = $post_type === 'fcn_story'
      ? get_post_meta( $post_id, 'fictioneer_story_co_authors', true )
      : get_post_meta( $post_id, 'fictioneer_chapter_co_authors', true );

    if ( is_array( $co_authors ) && ! empty( $co_authors ) ) {
      foreach ( $co_authors as $co_author_id ) {
        $co_author_id = (int) $co_author_id;

        if ( ! $co_author_id ) {
          continue;
        }

        $co_author = get_the_author_meta( 'url', $co_author_id ) ?: get_author_posts_url( $co_author_id );

        if ( empty( $co_author ) ) {
          $co_author = get_the_author_meta( 'display_name', $co_author_id );
        }

        if ( ! empty( $co_author ) && ! in_array( $co_author, $all_authors, true ) ) {
          $all_authors[] = $co_author;
        }
      }
    }

    return $all_authors;
  }

  /**
   * Return image data.
   *
   * @since 5.34.0
   *
   * @param int $post_id  Post ID.
   *
   * @return array|null Image data or null on failure
   */

  private static function build_image_array( $image_id ) {
    $image_id = (int) $image_id;

    if ( ! $image_id ) {
      return null;
    }

    $meta = wp_get_attachment_metadata( $image_id );

    if ( ! $meta || empty( $meta['width'] ) || empty( $meta['height'] ) ) {
      return null;
    }

    $url = wp_get_attachment_url( $image_id );

    if ( ! $url ) {
      return null;
    }

    return array(
      'id' => $image_id,
      'url' => $url,
      'height' => (int) $meta['height'],
      'width' => (int) $meta['width'],
      'type' => (string) get_post_mime_type( $image_id )
    );
  }

  /**
   * Get cached SEO data.
   *
   * @since 5.34.0
   *
   * @return array Cached SEO data or defaults.
   */

  private static function get_cache( $post_id ) : array {
    if ( isset( self::$data['seo_cache'][ $post_id ] ) && is_array( self::$data['seo_cache'][ $post_id ] ) ) {
      return self::$data['seo_cache'][ $post_id ];
    }

    $cache = get_post_meta( $post_id, 'fictioneer_seo_cache', true );

    if (
      ! is_array( $cache ) ||
      ! isset( $cache['title'], $cache['description'], $cache['og_image'], $cache['v'] )
    ) {
      $cache = array( 'title' => '', 'description' => '', 'og_image' => [], 'v' => 2 );
    }

    self::$data['seo_cache'][ $post_id ] = $cache;

    return $cache;
  }

  /**
   * Update cached SEO meta value (local and DB).
   *
   * @since 5.34.0
   *
   * @param int    $post_id  Post ID.
   * @param string $key      Meta key.
   * @param mixed  $value    Meta value.
   */

  private static function update_cached_value( $post_id, $key, $value ) : void {
    $meta_cache = self::get_cache( $post_id );

    if ( isset( $meta_cache[ $key ] ) && $meta_cache[ $key ] === $value ) {
      return;
    }

    $meta_cache[ $key ] = $value;

    self::$data['seo_cache'][ $post_id ] = $meta_cache;

    update_post_meta( $post_id, 'fictioneer_seo_cache', $meta_cache );
  }
}
