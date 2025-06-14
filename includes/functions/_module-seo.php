<?php

// =============================================================================
// GET SEO TITLE
// =============================================================================

if ( ! function_exists( 'fictioneer_get_seo_title' ) ) {
  /**
   * Get SEO title
   *
   * @since 4.0.0
   * @since 5.9.4 - Refactored meta caching.
   *
   * @param int   $post_id  Optional. The post ID.
   * @param array $args     Optional. Array of arguments.
   *
   * @return string The SEO title.
   */

  function fictioneer_get_seo_title( $post_id = null, $args = [] ) {
    // Setup
    $post_id = $post_id ? $post_id : get_queried_object_id();
    $skip_cache = $args['skip_cache'] ?? false;
    $default = ! empty( trim( $args['default'] ?? '' ) ) ? $args['default'] : false;

    // Search?
    if ( is_search() ) {
      return esc_html(
        sprintf(
          _x( 'Search Results', 'SEO search results title.', 'fictioneer' ),
          get_bloginfo( 'name' )
        )
      );
    }

    // Author?
    if ( is_author() ) {
      $author_id = get_queried_object_id();
      $author = get_userdata( $author_id );

      if ( $author ) {
        return esc_html(
          sprintf(
            _x( 'Author: %s', 'SEO author page title.', 'fictioneer' ),
            $author->display_name
          )
        );
      } else {
        return esc_html( _x( 'Author', 'SEO fallback title for author pages.', 'fictioneer' ) );
      }
    }

    // Archive?
    if ( is_archive() ) {
      // Category?
      if ( is_category() ) {
        return esc_html(
          sprintf(
            _x( 'Category: %s', 'SEO post category title.', 'fictioneer' ),
            single_cat_title( '', false )
          )
        );
      }

      // Tag?
      if ( is_tag() ) {
        return esc_html(
          sprintf(
            _x( 'Tag: %s', 'SEO post tag title.', 'fictioneer' ),
            single_tag_title( '', false )
          )
        );
      }

      // Character?
      if ( is_tax( 'fcn_character' ) ) {
        return esc_html(
          sprintf(
            _x( 'Character: %s', 'SEO character taxonomy title.', 'fictioneer' ),
            single_tag_title( '', false )
          )
        );
      }

      // Fandom?
      if ( is_tax( 'fcn_fandom' ) ) {
        return esc_html(
          sprintf(
            _x( 'Fandom: %s', 'SEO fandom taxonomy title.', 'fictioneer' ),
            single_tag_title( '', false )
          )
        );
      }

      // Genre?
      if ( is_tax( 'fcn_genre' ) ) {
        return esc_html(
          sprintf(
            _x( 'Genre: %s', 'SEO genre taxonomy title.', 'fictioneer' ),
            single_tag_title( '', false )
          )
        );
      }

      // Generic archive?
      return esc_html( _x( 'Archive', 'SEO fallback archive title.', 'fictioneer' ) );
    }

    // Meta cache for title (purged on update)?
    if ( ! $skip_cache ) {
      $meta_cache = get_post_meta( $post_id, 'fictioneer_seo_cache', true );

      if ( $meta_cache && is_array( $meta_cache ) && ! empty( $meta_cache['title'] ?? '' ) ) {
        return $meta_cache['title'];
      }
    }

    // Start building...
    $seo_fields = get_post_meta( $post_id, 'fictioneer_seo_fields', true );
    $seo_fields = is_array( $seo_fields ) ? $seo_fields : array( 'title' => '', 'description' => '', 'og_image_id' => 0 );
    $seo_title = $seo_fields['title'] ?? '';
    $title = fictioneer_get_safe_title( $post_id, 'seo-title' );
    $default = empty( $default ) ? $title : $default;

    // Special Case: Frontpage
    if ( is_front_page() ) {
      $default = empty( $default ) ? get_bloginfo( 'name' ) : $default;
    }

    // Placeholder: {{title}}
    if ( str_contains( $seo_title, '{{title}}' ) ) {
      $seo_title = str_replace( '{{title}}', $title, $seo_title );
    }

    // Placeholder: {{site}}
    if ( str_contains( $seo_title, '{{site}}' ) ) {
      $seo_title = str_replace( '{{site}}', get_bloginfo( 'name' ), $seo_title );
    }

    // Defaults...
    if ( ! empty( $default ) && empty( $seo_title ) ) {
      $seo_title = $default;
    }

    // Catch empty
    if ( empty( $seo_title ) ) {
      $seo_title = get_bloginfo( 'name' );
    }

    // Finalize
    $seo_title = esc_html( trim( wp_strip_all_tags( $seo_title ) ) );

    // Update meta cache
    if ( ! $skip_cache ) {
      // Ensure data structure
      if ( ! is_array( $meta_cache ) ) {
        $meta_cache = array( 'title' => '', 'description' => '', 'og_image' => [] );
      }

      $meta_cache['title'] = $seo_title;

      update_post_meta( $post_id, 'fictioneer_seo_cache', $meta_cache );
    }

    // Done
    return $seo_title;
  }
}

// =============================================================================
// GET SEO DESCRIPTION
// =============================================================================

if ( ! function_exists( 'fictioneer_get_seo_description' ) ) {
  /**
   * Get SEO description
   *
   * @since 4.0.0
   * @since 5.9.4 - Refactored meta caching.
   *
   * @param int   $post_id  Optional. The post ID.
   * @param array $args     Optional. Array of arguments.
   *
   * @return string The SEO description.
   */

  function fictioneer_get_seo_description( $post_id = null, $args = [] ) {
    // Setup
    $post_id = $post_id ? $post_id : get_queried_object_id();
    $skip_cache = $args['skip_cache'] ?? false;
    $default = trim( $args['default'] ?? '' );

    // Special Case: Frontpage
    if ( ( is_front_page() || ! $post_id ) && empty( $default ) ) {
      $default = get_bloginfo( 'description' );
    }

    // Search?
    if ( is_search() ) {
      return esc_html(
        sprintf(
          _x( 'Search results on %s.', 'Search page SEO description.', 'fictioneer' ),
          get_bloginfo( 'name' )
        )
      );
    }

    // Author?
    if ( is_author() ) {
      $author_id = get_queried_object_id();
      $author = get_userdata( $author_id );

      if ( $author && ! empty( $author->user_description ) ) {
        return esc_html( $author->user_description );
      } else {
        return esc_html(
          sprintf(
            _x( 'Author on %s.', 'Fallback SEO description for author pages.', 'fictioneer' ),
            get_bloginfo( 'name' )
          )
        );
      }
    }

    // Archive?
    if ( is_archive() ) {
      // Category?
      if ( is_category() ) {
        return esc_html(
          sprintf(
            __( 'Archive of all posts in the %s category.', 'fictioneer' ),
            single_cat_title( '', false )
          )
        );
      }

      // Tag?
      if ( is_tag() ) {
        return esc_html(
          sprintf(
            __( 'Archive of all posts with the %s tag.', 'fictioneer' ),
            single_tag_title( '', false )
          )
        );
      }

      // Character?
      if ( is_tax( 'fcn_character' ) ) {
        return esc_html(
          sprintf(
            __( 'Archive of all posts with the character %s.', 'fictioneer' ),
            single_tag_title( '', false )
          )
        );
      }

      // Fandom?
      if ( is_tax( 'fcn_fandom' ) ) {
        return esc_html(
          sprintf(
            __( 'Archive of all posts in the %s fandom.', 'fictioneer' ),
            single_tag_title( '', false )
          )
        );
      }

      // Genre?
      if ( is_tax( 'fcn_genre' ) ) {
        return esc_html(
          sprintf(
            __( 'Archive of all posts with the %s genre.', 'fictioneer' ),
            single_tag_title( '', false )
          )
        );
      }

      // Generic archive?
      return esc_html( sprintf( __( 'Archived posts on %s.', 'fictioneer' ), get_bloginfo( 'name' ) ) );
    }

    // Meta cache for description (purged on update)?
    if ( ! $skip_cache ) {
      $meta_cache = get_post_meta( $post_id, 'fictioneer_seo_cache', true );

      if ( $meta_cache && is_array( $meta_cache ) && ! empty( $meta_cache['description'] ?? '' ) ) {
        return $meta_cache['description'];
      }
    }

    // Start building...
    $seo_fields = get_post_meta( $post_id, 'fictioneer_seo_fields', true );
    $seo_fields = is_array( $seo_fields ) ? $seo_fields : array( 'title' => '', 'description' => '', 'og_image_id' => 0 );
    $seo_description = $seo_fields['description'] ?? '';
    $title = fictioneer_get_safe_title( $post_id, 'seo-title-in_description' );
    $excerpt = wp_strip_all_tags( get_the_excerpt( $post_id ), true );
    $excerpt = fictioneer_truncate( $excerpt, 155 );
    $default = empty( $default ) ? $excerpt : $default;

    // Special Case: Recommendations
    if ( get_post_type( $post_id ) == 'fcn_recommendation' ) {
      $one_sentence = get_post_meta( $post_id, 'fictioneer_recommendation_one_sentence', true );
      $one_sentence = trim( $one_sentence );

      $default = empty( $one_sentence ) ? $default : $one_sentence;
    }

    // Placeholder: {{excerpt}}
    if ( str_contains( $seo_description, '{{excerpt}}' ) ) {
      $seo_description = str_replace( '{{excerpt}}', $excerpt ?? '', $seo_description );
    }

    // Placeholder: {{title}}
    if ( str_contains( $seo_description, '{{title}}' ) ) {
      $seo_description = str_replace( '{{title}}', $title, $seo_description );
    }

    // Placeholder: {{site}}
    if ( str_contains( $seo_description, '{{site}}' ) ) {
      $seo_description = str_replace( '{{site}}', get_bloginfo( 'name' ), $seo_description );
    }

    // Defaults...
    if ( ! empty( $default ) && empty( $seo_description ) ) {
      $seo_description = $default;
    }

    // Catch empty
    if ( empty( $seo_description ) ) {
      $seo_description = get_bloginfo( 'description' );
    }

    // Finalize
    $seo_description = esc_html( trim( wp_strip_all_tags( $seo_description ) ) );

    // Update meta cache
    if ( ! $skip_cache ) {
      // Ensure data structure
      if ( ! is_array( $meta_cache ) ) {
        $meta_cache = array( 'title' => '', 'description' => '', 'og_image' => [] );
      }

      $meta_cache['description'] = $seo_description;

      update_post_meta( $post_id, 'fictioneer_seo_cache', $meta_cache );
    }

    // Done
    return $seo_description;
  }
}

// =============================================================================
// GET SEO IMAGE
// =============================================================================

if ( ! function_exists( 'fictioneer_get_seo_image' ) ) {
  /**
   * Get SEO image data array
   *
   * @since 4.0.0
   * @since 5.9.4 - Refactored meta caching.
   *
   * @param int $post_id Optional. The post ID.
   *
   * @return array|null Data of the image or null if none has been found.
   */

  function fictioneer_get_seo_image( $post_id = null ) {
    // Setup
    $post_id = $post_id ? $post_id : get_queried_object_id();
    $use_default = is_archive() || is_search() || is_author();
    $default_id = get_theme_mod( 'og_image' );
    $image_id = false;
    $image = null;
    $meta_cache = null;

    // Page with site default OG image?
    if ( $use_default ) {
      // Get default ID from settings
      $image_id = $default_id;
    }

    // Meta cache for image (purged on update)? Except for site default, which can globally change!
    if ( ! $use_default ) {
      $meta_cache = get_post_meta( $post_id, 'fictioneer_seo_cache', true );

      if (
        $meta_cache &&
        is_array( $meta_cache ) &&
        is_array( $meta_cache['og_image'] ?? 0 ) &&
        ! empty( $meta_cache['og_image'] ?? [] )
      ) {
        return $meta_cache['og_image'];
      }
    }

    // Get image ID if not yet set
    if ( ! $image_id ) {
      $seo_fields = get_post_meta( $post_id, 'fictioneer_seo_fields', true );
      $seo_fields = is_array( $seo_fields ) ? $seo_fields : array( 'title' => '', 'description' => '', 'og_image_id' => 0 );
      $image_id = $seo_fields['og_image_id'] ?? '';
      $image_id = wp_attachment_is_image( $image_id ) ? $image_id : false;
    }

    // No image found...
    if ( ! $image_id ) {
      // Try thumbnail...
      if ( has_post_thumbnail( $post_id ) ) {
        $image_id = get_post_thumbnail_id( $post_id );
      }

      // Try story thumbnail if chapter...
      if ( ! $image_id && get_post_type( $post_id ) == 'fcn_chapter' ) {
        $story_id = fictioneer_get_chapter_story_id( $post_id );
        $image_id = has_post_thumbnail( $story_id ) ? get_post_thumbnail_id( $story_id ) : $image_id;
      }

      // Try default if still nothing...
      if ( ! $image_id && get_theme_mod( 'og_image' ) ) {
        // Get default ID from settings
        $image_id = $default_id;
      }
    }

    // If an image has been found...
    if ( $image_id ) {
      $meta = wp_get_attachment_metadata( $image_id );

      if ( $meta ) {
        $image = array(
          'url' => wp_get_attachment_url( $image_id ),
          'height' => $meta['height'],
          'width' => $meta['width'],
          'type' => get_post_mime_type( $image_id ),
          'id' => $image_id
        );
      }
    }

    // Update meta cache (if image has been found)
    if ( $image ) {
      // Ensure data structure
      if ( ! is_array( $meta_cache ) ) {
        $meta_cache = array( 'title' => '', 'description' => '', 'og_image' => [] );
      }

      $meta_cache['og_image'] = $image;

      update_post_meta( $post_id, 'fictioneer_seo_cache', $meta_cache );
    }

    // Done
    return $image;
  }
}

// =============================================================================
// OUTPUT HEAD SEO
// =============================================================================

/**
 * Output HTML <head> SEO meta
 *
 * @since 5.0.0
 */

function fictioneer_output_head_seo() {
  global $wp;
  global $post;

  // Setup
  $post_id = get_queried_object_id(); // In archives and search, this is the first post
  $post_type = get_post_type(); // In archives and search, this is the first post
  $post_author = $post->post_author ?? 0;
  $chapter_story_id = fictioneer_get_chapter_story_id( $post_id );
  $canonical_url = wp_get_canonical_url(); // Only on singular pages

  // Flags
  $is_page = is_page() || is_front_page() || is_home();
  $is_aggregated = is_archive() || is_search();
  $is_article = ! $is_page && in_array( $post_type, ['fcn_story', 'fcn_chapter', 'fcn_recommendation', 'post'] );
  $show_author = $is_article && is_single() && ! is_front_page() && ! is_home() && ! $is_aggregated;

  // Derived
  $og_type = $is_article ? 'article' : 'website';
  $og_image = fictioneer_get_seo_image( $post_id );
  $og_title = fictioneer_get_seo_title( $post_id );
  $og_description = fictioneer_get_seo_description( $post_id );
  $article_author = $post_author && $show_author ? get_the_author_meta( 'display_name', $post_author ) : false;
  $article_twitter = $post_author && $show_author ? get_the_author_meta( 'twitter', $post_author ) : false;

  // Special Case: Recommendation author
  if ( $post_author && $show_author && $post_type == 'fcn_recommendation' ) {
    $article_author = get_post_meta( $post_id, 'fictioneer_recommendation_author', true ) ?? $article_author;
  }

  // Special Case: Archives
  if ( is_archive() ) {
    $canonical_url = home_url( $wp->request );
  }

  // Special Case: Search
  if ( is_search() ) {
    $canonical_url = add_query_arg( 's', '', home_url( $wp->request ) );
  }

  // Start HTML ---> ?>
  <link rel="canonical" href="<?php echo $canonical_url ? $canonical_url : get_permalink(); ?>">
  <meta name="description" content="<?php echo $og_description; ?>">
  <meta property="og:locale" content="<?php echo get_locale(); ?>">
  <meta property="og:type" content="<?php echo $og_type; ?>">
  <meta property="og:title" content="<?php echo $og_title; ?>">
  <meta property="og:description" content="<?php echo $og_description; ?>">
  <meta property="og:url" content="<?php echo $canonical_url ? $canonical_url : get_permalink(); ?>">
  <meta property="og:site_name" content="<?php echo get_bloginfo( 'name' ); ?>">

  <?php if ( ! $is_aggregated && $is_article ) : ?>

    <meta property="article:published_time" content="<?php echo get_the_date( 'c' ); ?>">
    <meta property="article:modified_time" content="<?php echo get_the_modified_date( 'c' ); ?>">

    <?php if ( $show_author && $article_author ) : ?>
      <?php
        // Get URL for primary author (if any)
        $article_author_url = get_the_author_meta( 'url', $post_author );
        $article_author_url = empty( $article_author_url ) ? get_author_posts_url( $post_author ) : $article_author_url;

        // Prepare author array with either URL (required) or name (better than nothing)
        $all_authors = empty( $article_author_url ) ? [ $article_author ] : [ $article_author_url ];

        // Get co-authors (if any)
        $co_authors = $post_type == 'fcn_story' ?
          get_post_meta( $post_id, 'fictioneer_story_co_authors', true ) :
          get_post_meta( $post_id, 'fictioneer_chapter_co_authors', true );

        // Add co-authors URL or name
        if ( is_array( $co_authors ) && ! empty( $co_authors ) ) {
          foreach ( $co_authors as $co_author_id ) {
            $co_author_name = get_the_author_meta( 'display_name', $co_author_id );
            $co_author_url = get_the_author_meta( 'url', $co_author_id );
            $co_author_url = empty( $co_author_url ) ? get_author_posts_url( $co_author_id ) : $co_author_url;
            $author_item = empty( $co_author_url ) ? $co_author_name : $co_author_url;

            if ( ! empty( $author_item ) && ! in_array( $author_item, $all_authors ) ) {
              $all_authors[] = $author_item;
            }
          }
        }

        // Output og array
        foreach ( $all_authors as $author_name ) {
          echo "<meta property='article:author' content='$author_name'>";
        }
      ?>
    <?php endif; ?>

    <?php if ( $post_type == 'fcn_chapter' && ! empty( $chapter_story_id ) ) : ?>
      <meta property="article:section" content="<?php echo get_the_title( $chapter_story_id ); ?>">
    <?php endif; ?>

  <?php endif; ?>

  <?php if ( is_array( $og_image ) && count( $og_image ) >= 4 ) : ?>
    <meta property="og:image" content="<?php echo $og_image['url']; ?>">
    <meta property="og:image:width" content="<?php echo $og_image['width']; ?>">
    <meta property="og:image:height" content="<?php echo $og_image['height']; ?>">
    <meta property="og:image:type" content="<?php echo $og_image['type']; ?>">
    <meta name="twitter:image" content="<?php echo $og_image['url']; ?>">
  <?php endif; ?>

  <meta name="twitter:card" content="summary">
  <meta name="twitter:title" content="<?php echo $og_title; ?>">
  <meta name="twitter:description" content="<?php echo $og_description; ?>">

  <?php if ( $show_author && $article_twitter ) : ?>
    <meta name="twitter:creator" content="<?php echo '@' . $article_twitter; ?>">
    <meta name="twitter:site" content="<?php echo '@' . $article_twitter; ?>">
  <?php endif; ?>
  <?php // <--- End HTML
}

if ( get_option( 'fictioneer_enable_seo' ) && ! fictioneer_seo_plugin_active() ) {
  remove_action( 'wp_head', 'rel_canonical' ); // Re-added in the new action
  add_action( 'wp_head', 'fictioneer_output_head_seo', 5 );
}
