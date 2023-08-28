<?php

// =============================================================================
// ADD SEO METABOX
// =============================================================================

/**
 * Adds a SEO metabox to selected post types
 *
 * @since Fictioneer 4.0
 *
 * @param string  $post_type  Post type.
 * @param WP_Post $post       Post object.
 */

function fictioneer_add_seo_metabox( $post_type, $post ) {
  // Setup
  $for_these_types = ['post', 'page', 'fcn_story', 'fcn_chapter', 'fcn_collection', 'fcn_recommendation'];
  $not_these_slugs = ['singular-bookshelf.php', 'singular-bookmarks.php', 'user-profile.php'];

  // Add meta box
  if ( in_array( $post_type, $for_these_types ) && ! in_array( get_page_template_slug( $post ), $not_these_slugs ) ) {
    add_meta_box(
      'fictioneer-search-engine-appearance',
      __( 'SEO & Meta Tags', 'fictioneer' ),
      'fictioneer_seo_fields',
      null,
      'normal',
      'high'
    );
  }
}

if (
  get_option( 'fictioneer_enable_seo' ) &&
  ! fictioneer_seo_plugin_active() &&
  current_user_can( 'fcn_seo_meta' )
) {
  add_action( 'add_meta_boxes', 'fictioneer_add_seo_metabox', 10, 2 );
}

// =============================================================================
// SEO METABOX HTML
// =============================================================================

if ( ! function_exists( 'fictioneer_seo_fields' ) ) {
  /**
   * Output HTML for SEO metabox
   *
   * @since Fictioneer 4.0
   *
   * @param object $post The post object.
   */

  function fictioneer_seo_fields( $post ) {
    // Title
    $seo_title = get_post_meta( $post->ID, 'fictioneer_seo_title', true ) ?? '{{title}} – {{site}}';
    $seo_title_placeholder = fictioneer_get_safe_title( $post->ID ) . ' – ' . FICTIONEER_SITE_NAME;

    // Description (truncated if necessary)
    $seo_description = get_post_meta( $post->ID, 'fictioneer_seo_description', true ) ?? '{{excerpt}}';
    $seo_description_placeholder = wp_strip_all_tags( get_the_excerpt( $post ), true );
    $seo_description_placeholder = mb_strimwidth( $seo_description_placeholder, 0, 155, '…' );

    // Open Graph image...
    $seo_og_image = get_post_meta( $post->ID, 'fictioneer_seo_og_image', true );
    $seo_og_image_display = wp_get_attachment_url( $seo_og_image );
    $image_source = '';

    // ... if not set, look for thumbnail...
    if ( ! $seo_og_image_display && has_post_thumbnail( $post->ID ) ) {
      $seo_og_image_display = get_the_post_thumbnail_url( $post->ID );
      $image_source = 'thumbnail';
    }

    // ... if not found, look for parent thumbnail...
    if ( ! $seo_og_image_display && $post->post_type == 'fcn_chapter' ) {
      $story_id = fictioneer_get_field( 'fictioneer_chapter_story', $post->ID );

      if ( $story_id && has_post_thumbnail( $story_id ) ) {
        $seo_og_image_display = get_the_post_thumbnail_url( $story_id );
        $image_source = 'parent-thumbnail';
      }
    }

    // ... if not found either, try the default OG image...
    if ( ! $seo_og_image_display && get_theme_mod( 'og_image' ) ) {
      $seo_og_image_display = wp_get_attachment_url( get_theme_mod( 'og_image' ) );
      $image_source = 'default';
    }

    // ... okay, use a placeholder
    if ( ! $seo_og_image_display ) {
      $seo_og_image_display = get_template_directory_uri() . '/img/no_image_placeholder.svg';
      $image_source = 'placeholder';
    }

    // Start HTML ---> ?>
    <div class="fictioneer-metabox">

      <input type="hidden" name="fictioneer_metabox_seo_nonce" value="<?php echo wp_create_nonce( 'fictioneer-metabox-seo-save' ); ?>">

      <p class="description"><?php _ex( 'Metadata for search engine results, schema graphs, and social media embeds. If left blank, defaults will be derived from the content. You can use <code>{{title}}</code>, <code>{{site}}</code>, and <code>{{excerpt}}</code> as placeholders. Whether these services actually display the offered data is entirely up to them.', 'Do not translate {{title}}, {{site}}, and {{excerpt}}.', 'fictioneer' ); ?></p>

      <div class="fictioneer-metabox__left">

        <div class="fictioneer-metabox__row">
          <label for="fictioneer-seo-title">
            <span><?php _e( 'Title', 'fictioneer' ); ?></span>
            <span id="fictioneer-seo-title-chars" class="counter"></span>
          </label>
          <input id="fictioneer-seo-title" type="text" data-lpignore="true" name="fictioneer_seo_title" value="<?php echo esc_attr( $seo_title ); ?>" placeholder="<?php echo esc_attr( $seo_title_placeholder ); ?>">
        </div>

        <div class="fictioneer-metabox__row">
          <label for="fictioneer-seo-description">
            <span><?php _e( 'Description', 'fictioneer' ); ?></span>
          </label>
          <textarea id="fictioneer-seo-description" name="fictioneer_seo_description" rows="3" placeholder="<?php echo esc_attr( $seo_description_placeholder ); ?>"><?php echo $seo_description; ?></textarea>
        </div>

      </div>

      <div class="fictioneer-metabox__right">

        <input type="hidden" id="fictioneer-seo-og-image" name="fictioneer_seo_og_image" value="<?php echo $seo_og_image; ?>">

        <a href="#" id="fictioneer-button-seo-og-image-remove" class="og-remove <?php echo $seo_og_image ? '' : 'hidden'; ?>"><?php _e( 'Remove', 'fictioneer' ) ?></a>

        <div class="og-source <?php echo $image_source; ?>">
          <div class="thumbnail"><?php _e( 'Source: Thumbnail', 'fictioneer' ) ?></div>
          <div class="parent-thumbnail"><?php _e( 'Source: Parent', 'fictioneer' ) ?></div>
          <div class="default"><?php _e( 'Source: Site Default', 'fictioneer' ) ?></div>
        </div>

        <a href="#" id="fictioneer-button-og-upload">
          <img id="fictioneer-seo-og-display" src="<?php echo esc_url( $seo_og_image_display ); ?>" data-placeholder="<?php echo esc_url( get_template_directory_uri() . '/img/no_image_placeholder.svg' ); ?>">
          <div class="og-upload-label"><?php _e( 'Open Graph Image', 'fictioneer' ) ?></div>
        </a>

      </div>

    </div>
    <?php // <--- End HTML
  }
}

// =============================================================================
// SAVE SEO METABOX
// =============================================================================

/**
 * Save SEO metabox data
 *
 * @since Fictioneer 4.0
 *
 * @param int $post_id The post ID.
 */

function fictioneer_save_seo_metabox( $post_id ) {
  // Validations
  if (
    ! isset( $_POST['fictioneer_metabox_seo_nonce'] ) ||
    ! wp_verify_nonce( $_POST['fictioneer_metabox_seo_nonce'], 'fictioneer-metabox-seo-save' ) ||
    ! current_user_can( 'edit_post', $post_id ) ||
    wp_is_post_autosave( $post_id ) ||
    wp_is_post_revision( $post_id ) ||
    in_array( get_post_status( $post_id ), ['auto-draft'] )
  ) {
    return;
  }

  // Save image
  if ( isset( $_POST['fictioneer_seo_og_image'] ) ) {
    update_post_meta( $post_id, 'fictioneer_seo_og_image', $_POST['fictioneer_seo_og_image'] );
  }

  // Save title
  if ( isset( $_POST['fictioneer_seo_title'] ) ) {
    update_post_meta( $post_id, 'fictioneer_seo_title', $_POST['fictioneer_seo_title'] );
  }

  // Save description
  if ( isset( $_POST['fictioneer_seo_description'] ) ) {
    update_post_meta( $post_id, 'fictioneer_seo_description', $_POST['fictioneer_seo_description'] );
  }

  // Clear caches
  delete_post_meta( $post_id, 'fictioneer_seo_title_cache' );
  delete_post_meta( $post_id, 'fictioneer_seo_description_cache' );
  delete_post_meta( $post_id, 'fictioneer_seo_og_image_cache' );
}

if (
  get_option( 'fictioneer_enable_seo' ) &&
  ! fictioneer_seo_plugin_active() &&
  current_user_can( 'fcn_seo_meta' )
) {
  add_action( 'save_post', 'fictioneer_save_seo_metabox', 10 );
}

// =============================================================================
// GET SEO TITLE
// =============================================================================

if ( ! function_exists( 'fictioneer_get_seo_title' ) ) {
  /**
   * Get SEO title
   *
   * @since Fictioneer 4.0
   *
   * @param int   $post_id Optional. The post ID.
   * @param array $args    Optional. Array of arguments.
   *
   * @return string The SEO title.
   */

  function fictioneer_get_seo_title( $post_id = null, $args = [] ) {
    // Setup
    $post_id = $post_id ? $post_id : get_queried_object_id();
    $page = get_query_var( 'paged', 0 ); // Only default pagination is considered
    $page_note = $page > 1 ? sprintf( __( ' – Page %s', 'fictioneer' ), $page ) : '';
    $site_name = FICTIONEER_SITE_NAME;
    $skip_cache = isset( $args['skip_cache'] ) && $args['skip_cache'];
    $default = isset( $args['default'] ) && ! empty( $args['default'] ) ? $args['default'] : false;

    // Search?
    if ( is_search() ) {
      return esc_html(
        sprintf(
          _x( 'Search – %s', 'Search – Site Name.', 'fictioneer' ),
          $site_name
        )
      );
    }

    // Author?
    if ( is_author() ) {
      $author_id = get_queried_object_id();
      $author = get_userdata( $author_id );

      if ( $author ) {
        return $author->display_name;
      } else {
        return _x( 'Author', 'Fallback SEO title for author pages.', 'fictioneer' );
      }
    }

    // Archive?
    if ( is_archive() ) {
      // Category?
      if ( is_category() ) {
        return esc_html(
          sprintf(
            _x( 'Category: %1$s%2$s – %3$s', 'Category: Category[ – Page n] – Site Name. The page is optional.', 'fictioneer' ),
            single_cat_title( '', false ),
            $page_note,
            $site_name
          )
        );
      }

      // Tag?
      if ( is_tag() ) {
        return esc_html(
          sprintf(
            _x( 'Tag: %1$s%2$s – %3$s', 'Tag: Tag[ – Page n] – Site Name. The page is optional.', 'fictioneer' ),
            single_tag_title( '', false ),
            $page_note,
            $site_name
          )
        );
      }

      // Character?
      if ( is_tax( 'fcn_character' ) ) {
        return esc_html(
          sprintf(
            _x( 'Character: %1$s%2$s – %3$s', 'Character: Character[ – Page n] – Site Name. The page is optional.', 'fictioneer' ),
            single_tag_title( '', false ),
            $page_note,
            $site_name
          )
        );
      }

      // Fandom?
      if ( is_tax( 'fcn_fandom' ) ) {
        return esc_html(
          sprintf(
            _x( 'Fandom: %1$s%2$s – %3$s', 'Fandom: Fandom[ – Page n] – Site Name. The page is optional.', 'fictioneer' ),
            single_tag_title( '', false ),
            $page_note,
            $site_name
          )
        );
      }

      // Genre?
      if ( is_tax( 'fcn_genre' ) ) {
        return esc_html(
          sprintf(
            _x( 'Genre: %1$s%2$s – %3$s', 'Genre: Genre[ – Page n] – Site Name. The page is optional.', 'fictioneer' ),
            single_tag_title( '', false ),
            $page_note,
            $site_name
          )
        );
      }

      // Generic archive?
      return esc_html( sprintf( __( 'Archive – %1$s', 'fictioneer' ), $site_name ) );
    }

    // Cached title?
    $cache = get_post_meta( $post_id, 'fictioneer_seo_title_cache', true );

    if ( ! empty( $cache ) && ! $skip_cache ) {
      return $cache;
    }

    // Start building...
    $seo_title = get_post_meta( $post_id, 'fictioneer_seo_title', true );
    $seo_title = empty( $seo_title ) ? false : $seo_title;

    // Frontpage case...
    if ( is_front_page() && $seo_title == '{{title}} – {{site}}' ) {
      $seo_title = $site_name;
    }

    // Defaults...
    if ( $default && ( ! $seo_title || $seo_title == '{{title}} – {{site}}' ) ) {
      $seo_title = $default;
    }

    // Replace {{title}} placeholder...
    if ( str_contains( $seo_title, '{{title}}' ) ) {
      $seo_title = str_replace( '{{title}}', fictioneer_get_safe_title( $post_id ), $seo_title );
    }

    // Replace {{site}} placeholder...
    if ( str_contains( $seo_title, '{{site}}' ) ) {
      $seo_title = str_replace( '{{site}}', $site_name, $seo_title );
    }

    // Catch empty
    if ( empty( $seo_title ) ) {
      $seo_title = sprintf(
        _x( '%1$s%2$s – %3$s', 'Post Title[ – Page n] – Site Name. The page is optional.', 'fictioneer' ),
        get_the_title( $post_id ),
        $page_note,
        $site_name
      );
    }

    // Finalize
    $seo_title = esc_html( trim( wp_strip_all_tags( $seo_title ) ) );

    // Cache for next time
    if ( ! $skip_cache ) {
      update_post_meta( $post_id, 'fictioneer_seo_title_cache', $seo_title );
    }

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
   * @since Fictioneer 4.0
   *
   * @param int   $post_id Optional. The post ID.
   * @param array $args    Optional. Array of arguments.
   *
   * @return string The SEO description.
   */

  function fictioneer_get_seo_description( $post_id = null, $args = [] ) {
    // Setup
    $post_id = $post_id ? $post_id : get_queried_object_id();
    $site_name = FICTIONEER_SITE_NAME;
    $skip_cache = isset( $args['skip_cache'] ) && $args['skip_cache'];
    $default = isset( $args['default'] ) && ! empty( $args['default'] ) ? $args['default'] : false;

    // Search?
    if ( is_search() ) {
      return esc_html(
        sprintf(
          _x( 'Search results on %s.', 'Search page SEO description.', 'fictioneer' ),
          $site_name
        )
      );
    }

    // Author?
    if ( is_author() ) {
      $author_id = get_queried_object_id();
      $author = get_userdata( $author_id );

      if ( $author && ! empty( $author->user_description ) ) {
        return $author->user_description;
      } else {
        return esc_html(
          sprintf(
            _x( 'Author on %s.', 'Fallback SEO description for author pages.', 'fictioneer' ),
            $site_name
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
      return esc_html( sprintf( __( 'Archived posts on %s.', 'fictioneer' ), $site_name ) );
    }

    // Cached description?
    $cache = get_post_meta( $post_id, 'fictioneer_seo_description_cache', true );

    if ( ! empty( $cache ) && ! $skip_cache ) {
      return $cache;
    }

    // Start building...
    $seo_description = get_post_meta( $post_id, 'fictioneer_seo_description', true );
    $seo_description = empty( $seo_description ) ? false : $seo_description;
    $excerpt = '';

    // Special Case: Recommendations
    if ( get_post_type( $post_id ) == 'fcn_recommendation' ) {
      $default = fictioneer_get_field( 'fictioneer_recommendation_one_sentence', $post_id );
    }

    // Defaults...
    if ( $default && ( ! $seo_description || $seo_description == '{{excerpt}}' ) ) {
      $seo_description = $default;
    }

    // Get description from excerpt if necessary...
    if ( ! $seo_description || str_contains( $seo_description, '{{excerpt}}' ) ) {
      $excerpt = wp_strip_all_tags( get_the_excerpt( $post_id ), true );
      $excerpt = mb_strimwidth( $excerpt, 0, 155, '…' );

      if ( ! $seo_description ) $seo_description = $excerpt;
    }

    // Replace {{excerpt}} placeholder...
    if ( str_contains( $seo_description, '{{excerpt}}' ) ) {
      $seo_description = str_replace( '{{excerpt}}', $excerpt, $seo_description );
    }

    // Catch empty
    if ( empty( $seo_description ) ) {
      $seo_description = $default ? $default : FICTIONEER_SITE_DESCRIPTION;
    }

    // Finalize
    $seo_description = esc_html( trim( wp_strip_all_tags( $seo_description ) ) );

    // Cache for next time
    if ( ! $skip_cache ) {
      update_post_meta( $post_id, 'fictioneer_seo_description_cache', $seo_description );
    }

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
   * @since Fictioneer 4.0
   *
   * @param int $post_id Optional. The post ID.
   *
   * @return array|boolean Data of the image or false if none has been found.
   */

  function fictioneer_get_seo_image( $post_id = null ) {
    // Setup
    $post_id = $post_id ? $post_id : get_queried_object_id();
    $use_default = is_archive() || is_search() || is_author();
    $default_id = get_theme_mod( 'og_image' );
    $image_id = false;
    $image = false;

    // Page with site default OG image?
    if ( $use_default ) {
      // Get default ID from settings
      $image_id = $default_id;
    }

    // Cached image? Except for site default, which can globally change!
    if ( ! $use_default ) {
      $cache = get_post_meta( $post_id, 'fictioneer_seo_og_image_cache', true );

      if ( $cache ) {
        return $cache;
      }
    }

    // Get image ID if not yet set
    if ( ! $image_id ) {
      $image_id = get_post_meta( $post_id, 'fictioneer_seo_og_image', true );
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
        $story_id = fictioneer_get_field( 'fictioneer_chapter_story', $post_id );
        $image_id = has_post_thumbnail( $story_id ) ? get_post_thumbnail_id( $story_id ) : $image_id;
      }

      // Try default if still nothing...
      if ( ! $image_id && get_theme_mod( 'og_image') ) {
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

    // Cache for next time
    if ( ! $use_default ) {
      update_post_meta( $post_id, 'fictioneer_seo_og_image_cache', $image );
    }

    return $image;
  }
}

// =============================================================================
// OUTPUT HEAD SEO
// =============================================================================

/**
 * Output HTML <head> SEO meta
 *
 * @since Fictioneer 5.0
 */

function fictioneer_output_head_seo() {
  global $wp;
  global $post;

  // Setup
  $post_id = get_queried_object_id(); // In archives and search, this is the first post
  $post_type = get_post_type(); // In archives and search, this is the first post
  $post_author = $post->post_author ?? 0;
  $chapter_story_id = fictioneer_get_field( 'fictioneer_chapter_story' ) ?? 0;
  $canonical_url = wp_get_canonical_url(); // Unreliable on aggregated pages

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
    $article_author = fictioneer_get_field( 'fictioneer_recommendation_author', $post_id ) ?? $article_author;
  }

  // Special Case: Archives
  if ( is_archive() ) $canonical_url = home_url( $wp->request );

  // Special Case: Search
  if ( is_search() ) $canonical_url = add_query_arg( 's', '', home_url( $wp->request ) );

  // Start HTML ---> ?>
  <link rel="canonical" href="<?php echo $canonical_url; ?>">
  <meta name="description" content="<?php echo $og_description; ?>">
  <meta property="og:locale" content="<?php echo get_locale(); ?>">
  <meta property="og:type" content="<?php echo $og_type; ?>">
  <meta property="og:title" content="<?php echo $og_title; ?>">
  <meta property="og:description" content="<?php echo $og_description; ?>">
  <meta property="og:url" content="<?php echo $canonical_url; ?>">
  <meta property="og:site_name" content="<?php echo FICTIONEER_SITE_NAME; ?>">

  <?php if ( ! $is_aggregated && $is_article ) : ?>

    <meta property="article:published_time" content="<?php echo get_the_date( 'c' ); ?>">
    <meta property="article:modified_time" content="<?php echo get_the_modified_date( 'c' ); ?>">

    <?php if ( $show_author && $article_author ) : ?>
      <?php
        // Get URL for primary author (if any)
        $article_author_url = get_the_author_meta( 'url', $post_author );
        $article_author_url = empty( $article_author_url ) ? get_author_posts_url( $post_author ) : $article_author_url;

        // Prepare author array with either URL (required) or name (better than nothing)
        $all_authors = empty( $article_author_url ) ? [$article_author] : [$article_author_url];

        // Get co-authors (if any)
        $co_authors = $post_type == 'fcn_story' ?
          fictioneer_get_field( 'fictioneer_story_co_authors', $post_id ) ?? [] :
          fictioneer_get_field( 'fictioneer_chapter_co_authors', $post_id ) ?? [];

        // Add co-authors URL or name
        if ( ! empty( $co_authors ) ) {
          foreach ( $co_authors as $co_author_id ) {
            $co_author_name = get_the_author_meta( 'display_name', intval( $co_author_id ) );
            $co_author_url = get_the_author_meta( 'url', intval( $co_author_id ) );
            $co_author_url = empty( $co_author_url ) ? get_author_posts_url( $co_author_id ) : $co_author_url;
            $author_item = empty( $co_author_url ) ? $co_author_name : $co_author_url;
            if ( ! empty( $author_item ) && ! in_array( $author_item, $all_authors ) ) $all_authors[] = $author_item;
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
  add_action( 'wp_head', 'fictioneer_output_head_seo', 5 );
}

?>
