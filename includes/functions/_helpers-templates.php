<?php

// =============================================================================
// GET EXCERPTS
// =============================================================================

if ( ! function_exists( 'fictioneer_get_excerpt' ) ) {
  /**
   * Returns the excerpt with theme adjustments
   *
   * @since 5.0.0
   *
   * @return string The customized excerpt.
   */

  function fictioneer_get_excerpt() {
    $excerpt = get_the_excerpt();
    $excerpt = trim( preg_replace( '/\s+/', ' ', $excerpt ) );

    // Return result
    return $excerpt;
  }
}

if ( ! function_exists( 'fictioneer_get_limited_excerpt' ) ) {
  /**
   * Returns excerpt with maximum length in characters
   *
   * @since 3.0
   *
   * @param int    $limit   The character limit.
   * @param string $source  Either the 'content' or 'excerpt'. Default 'excerpt'.
   *
   * @return string The customized excerpt.
   */

  function fictioneer_get_limited_excerpt( $limit, $source = 'excerpt' ) {
    $excerpt = $source == 'content' ? get_the_content() : get_the_excerpt();
    $excerpt = preg_replace( ' (\[.*?\])', '', $excerpt );
    $excerpt = strip_shortcodes( $excerpt );
    $excerpt = strip_tags( $excerpt );
    $excerpt = substr( $excerpt, 0, $limit );
    $excerpt = substr( $excerpt, 0, strripos( $excerpt, ' ' ) );
    $excerpt = trim( preg_replace( '/\s+/', ' ', $excerpt ) );
    $excerpt = $excerpt . '…';
    return $excerpt;
  }
}

if ( ! function_exists( 'fictioneer_first_paragraph_as_excerpt' ) ) {
  /**
   * Returns the first paragraph of the content as excerpt
   *
   * @since 3.0
   *
   * @param string $content  The content as HTML.
   *
   * @return string First paragraph as excerpt, stripped of tags.
   */

  function fictioneer_first_paragraph_as_excerpt( $content ) {
    $excerpt = strip_shortcodes( $content );
    $excerpt = substr( $excerpt, 0, strpos( $excerpt, '</p>' ) + 4 );

    return wp_strip_all_tags( $excerpt, true );
  }
}

if ( ! function_exists( 'fictioneer_get_forced_excerpt' ) ) {
  /**
   * Returns the excerpt even if the post is protected
   *
   * @since 4.5.0
   *
   * @param int     $post_id  Post ID.
   * @param int     $limit    Maximum number of characters.
   * @param boolean $default  Whether to return the original excerpt if present.
   *
   * @return string The excerpt stripped of tags.
   */

  function fictioneer_get_forced_excerpt( $post_id, $limit = 256, $default = false ) {
    $post = get_post( $post_id );

    if ( ! $default && $post->post_excerpt != '' ) {
      return $post->post_excerpt;
    }

    $excerpt = $post->post_content;
    $excerpt = preg_replace( ' (\[.*?\])', '', $excerpt );
    $excerpt = strip_shortcodes( $excerpt );
    $excerpt = wp_strip_all_tags( $excerpt );
    $excerpt = substr( $excerpt, 0, $limit );
    $excerpt = substr( $excerpt, 0, strripos( $excerpt, ' ' ) );
    $excerpt = trim( preg_replace( '/\s+/', ' ', $excerpt ) );
    $excerpt = $excerpt . '…';

    return $excerpt;
  }
}

// =============================================================================
// GET AUTHOR NODE(S)
// =============================================================================

if ( ! function_exists( 'fictioneer_get_author_node' ) ) {
  /**
   * Returns an author's display name as link to public profile
   *
   * @since 4.0.0
   *
   * @param int    $author_id  The author's user ID. Defaults to current post author.
   * @param string $classes    Optional. String of CSS classes.
   *
   * @return string HTML with author's display name as public profile link.
   */

  function fictioneer_get_author_node( $author_id = null, $classes = '' ) {
    $author_id = $author_id ? $author_id : get_the_author_meta( 'ID' );
    $author_name = get_the_author_meta( 'display_name', $author_id );
    $author_url = get_author_posts_url( $author_id );

    if ( ! $author_name ) {
      return '';
    }

    return "<a href='{$author_url}' class='author {$classes}'>{$author_name}</a>";
  }
}

if ( ! function_exists( 'fictioneer_get_multi_author_nodes' ) ) {
  /**
   * Returns the HTML for the authors
   *
   * @since 5.0.0
   *
   * @param array $authors  Array of authors to process.
   *
   * @return string HTML for the author nodes.
   */

  function fictioneer_get_multi_author_nodes( $authors ) {
    // Setup
    $author_nodes = [];

    // If no author was found...
    if ( empty( $authors ) ) {
      return __( 'Unknown', 'No story author(s) were found.', 'fictioneer' );
    }

    // The meta field returns an array of IDs
    foreach ( $authors as $author ) {
      $author_nodes[] = fictioneer_get_author_node( $author );
    }

    // Remove empty items
    $author_nodes = array_filter( $author_nodes );

    // Build and return HTML
    return implode( ', ', array_unique( $author_nodes ) );
  }
}

if ( ! function_exists( 'fictioneer_get_story_author_nodes' ) ) {
  /**
   * Returns the HTML for the authors on story pages
   *
   * @since 5.0.0
   *
   * @param int $story_id  The story ID.
   *
   * @return string HTML for the author nodes.
   */

  function fictioneer_get_story_author_nodes( $story_id ) {
    // Setup
    $all_authors = fictioneer_get_post_author_ids( $story_id );
    $all_authors = is_array( $all_authors ) ? $all_authors : [];

    // Return author nodes
    return fictioneer_get_multi_author_nodes( $all_authors );
  }
}

if ( ! function_exists( 'fictioneer_get_chapter_author_nodes' ) ) {
  /**
   * Returns the HTML for the authors on chapter pages
   *
   * @since 5.0.0
   *
   * @param int $chapter_id  The chapter ID.
   *
   * @return string HTML for the author nodes.
   */

  function fictioneer_get_chapter_author_nodes( $chapter_id ) {
    // Setup
    $all_authors = get_post_meta( $chapter_id, 'fictioneer_chapter_co_authors', true ) ?? [];
    $all_authors = is_array( $all_authors ) ? $all_authors : [];
    array_unshift( $all_authors, get_post_field( 'post_author', $chapter_id ) );

    // Return author nodes
    return fictioneer_get_multi_author_nodes( $all_authors );
  }
}

// =============================================================================
// GET ICON HTML
// =============================================================================

if ( ! function_exists( 'fictioneer_icon' ) ) {
  /**
   * Outputs the HTML for an inline svg icon
   *
   * @since 4.0.0
   *
   * @param string $icon     Name of the icon that matches the svg.
   * @param string $classes  Optional. String of CSS classes.
   * @param string $id       Optional. An element ID.
   * @param string $inserts  Optional. Additional attributes.
   */

  function fictioneer_icon( $icon, $classes = '', $id = '', $inserts = '' ) {
    echo fictioneer_get_icon( $icon, $classes, $id, $inserts );
  }
}

if ( ! function_exists( 'fictioneer_get_icon' ) ) {
  /**
   * Returns the HTML for an inline svg icon
   *
   * Uses a similar structure to Font Awesome but inserts an external svg
   * via the <use> tag. The version is added as query variable for cache
   * busting purposes.
   *
   * @since 4.7.0
   * @since 5.9.4 - Removed output buffer.
   *
   * @param string $icon     Name of the icon that matches the svg.
   * @param string $classes  Optional. String of CSS classes.
   * @param string $id       Optional. An element ID.
   * @param string $inserts  Optional. Additional attributes.
   *
   * @return string The icon HTML.
   */

  function fictioneer_get_icon( $icon, $classes = '', $id = '', $inserts = '' ) {
    return '<svg id="' . $id . '" ' . $inserts . ' class="icon _' . $icon . ' ' . $classes . '">' . '<use xlink:href="' . esc_url( get_template_directory_uri() ) . '/img/icon-sprite.svg?ver=' . FICTIONEER_VERSION . '#icon-' . $icon . '"></use></svg>';
  }
}

// =============================================================================
// GET SAFE TITLE
// =============================================================================

if ( ! function_exists( 'fictioneer_get_safe_title' ) ) {
  /**
   * Returns the sanitized title and accounts for empty strings
   *
   * @since 4.7.0
   * @since 5.12.0 - Added $context and $args parameters.
   * @link https://developer.wordpress.org/reference/functions/wp_strip_all_tags/
   *
   * @param int|WP_Post $post     The post or post ID to get the title for.
   * @param string|null $context  Optional. Context regarding where and how the title is used.
   * @param array       $args     Optional. Additional parameters.
   *
   * @return string The title, never empty.
   */

  function fictioneer_get_safe_title( $post, $context = null, $args = [] ) {
    // Setup
    $post_id = ( $post instanceof WP_Post ) ? $post->ID : $post;

    // Get title and remove HTML
    $title = wp_strip_all_tags( get_the_title( $post_id ) );

    // If empty, use the datetime as title
    if ( empty( $title ) ) {
      $title = sprintf(
        _x( '%1$s — %2$s', '[Date] — [Time] if post title is missing.', 'fictioneer' ),
        get_the_date( '', $post_id ),
        get_the_time( '', $post_id )
      );
    }

    // Apply filters
    if ( ! ( $args['no_filters'] ?? 0 ) ) {
      $title = apply_filters( 'fictioneer_filter_safe_title', $title, $post_id, $context, $args );
    }

    // Return final title
    return $title;
  }
}

/**
 * Prepends icon to sanitized titles of sticky blog posts
 *
 * @since 5.7.1
 *
 * @param string $title  The sanitized title of the post.
 * @param int    $id     The ID of the post.
 *
 * @return string The modified title.
 */

function fictioneer_prefix_sticky_safe_title( $title, $id ) {
  // Prepend icon to titles of sticky posts
  if ( is_sticky( $id ) && get_post_type( $id ) === 'post' ) {
    return '<i class="fa-solid fa-thumbtack sticky-pin"></i> ' . $title;
  }

  // Continue filter
  return $title;
}
add_filter( 'fictioneer_filter_safe_title', 'fictioneer_prefix_sticky_safe_title', 10, 2 );

/**
 * Prepends icon to sanitized titles of protected blog posts
 *
 * @since 5.7.3
 *
 * @param string $title  The sanitized title of the post.
 * @param int    $id     The ID of the post.
 *
 * @return string The modified title.
 */

function fictioneer_prefix_protected_safe_title( $title, $id ) {
  // Prepend icon to titles of sticky posts
  if ( post_password_required( $id ) && get_post_type( $id ) === 'post' ) {
    return '<i class="fa-solid fa-lock protected-icon"></i> ' . $title;
  }

  // Continue filter
  return $title;
}
add_filter( 'fictioneer_filter_safe_title', 'fictioneer_prefix_protected_safe_title', 10, 2 );

/**
 * Prepends "Draft:" to sanitized titles of drafts
 *
 * @since 5.7.1
 *
 * @param string $title  The sanitized title of the post.
 * @param int    $id     The ID of the post.
 *
 * @return string The modified title.
 */

function fictioneer_prefix_draft_safe_title( $title, $id ) {
  // Prepend icon to titles of drafts
  if ( get_post_status( $id ) === 'draft' ) {
    return sprintf( _x( 'Draft: %s', 'Safe title prefix.', 'fictioneer' ), $title );
  }

  // Continue filter
  return $title;
}
add_filter( 'fictioneer_filter_safe_title', 'fictioneer_prefix_draft_safe_title', 10, 2 );

// =============================================================================
// GET READING TIME NODES
// =============================================================================

if ( ! function_exists( 'fictioneer_get_reading_time_nodes' ) ) {
  /**
   * Returns the HTML for the reading time nodes
   *
   * Returns the estimated reading time for a given amount of words in two nodes,
   * a long one for desktop and a short one for mobile.
   *
   * @since 4.7.0
   *
   * @param int $word_count  The number of words.
   *
   * @return string HTML for the reading time nodes.
   */

  function fictioneer_get_reading_time_nodes( $word_count ) {
    $wpm = absint( get_option( 'fictioneer_words_per_minute', 200 ) );
    $wpm = max( 1, $wpm );
    $t = round( $word_count / $wpm * 60 );
    $long = '';
    $short = '';

    if ( $t / 86400 >= 1 ) {
      $d = floor( $t / 86400 );
      $h = floor( ( $t%86400 ) / 3600 );

      $long = sprintf(
        _x( '%1$s, %2$s', 'Long reading time statistics: [d] days, [h] hours.', 'fictioneer' ),
        sprintf( _n( '%s day', '%s days', $d, 'fictioneer' ), $d ),
        sprintf( _n( '%s hour', '%s hours', $h, 'fictioneer' ), $h )
      );

      $short = sprintf(
        _x( '%1$s, %2$s', 'Shortened reading time statistics: [d] d, [h] h.', 'fictioneer' ),
        sprintf( _n( '%s d', '%s d', $d, 'fictioneer' ), $d ),
        sprintf( _n( '%s h', '%s h', $h, 'fictioneer' ), $h )
      );
    } elseif ( $t / 3600 >= 1 ) {
      $h = floor( ( $t%86400 ) / 3600 );
      $m = floor( ( $t%3600 ) / 60 );

      $long = sprintf(
        _x( '%1$s, %2$s', 'Long reading time statistics: [h] hours, [m] minutes.', 'fictioneer' ),
        sprintf( _n( '%s hour', '%s hours', $h, 'fictioneer' ), $h ),
        sprintf( _n( '%s minute', '%s minutes', $m, 'fictioneer' ), $m )
      );

      $short = sprintf(
        _x( '%1$s, %2$s', 'Shortened reading time statistics: [h] h, [m] m.', 'fictioneer' ),
        sprintf( _n( '%s h', '%s h', $h, 'fictioneer' ), $h ),
        sprintf( _n( '%s m', '%s m', $m, 'fictioneer' ), $m )
      );
    } else {
      $m = floor( ( $t%3600 ) / 60 );

      $long = sprintf( _n( '%s minute', '%s minutes', $m, 'fictioneer' ), $m );

      $short = sprintf( _n( '%s m', '%s m', $m, 'fictioneer' ), $m );
    }

    return '<span class="hide-below-400">' . $long . '</span><span class="show-below-400">' . $short . '</span>';
  }
}

// =============================================================================
// GET FOOTER COPYRIGHT NOTE
// =============================================================================

if ( ! function_exists( 'fictioneer_get_footer_copyright_note' ) ) {
  /**
   * Returns the HTML for the footer copyright note
   *
   * @since 5.0.0
   * @since 5.9.4 - Removed output buffer.
   *
   * @param array  $args['breadcrumbs']  Breadcrumb tuples with label (0) and link (1).
   * @param string $args['post_type']    Optional. Post type of the current page.
   * @param int    $args['post_id']      Optional. Post ID of the current page.
   *
   * @return string HTML for the copyright note.
   */

  function fictioneer_get_footer_copyright_note( $args ) {
    return sprintf(
      '<span>© %s</span> <span>%s</span> <span>|</span> <a href="https://github.com/Tetrakern/fictioneer" target="_blank" rel="noreferrer">%s</a>',
      date( 'Y' ),
      get_bloginfo( 'name' ),
      fictioneer_get_version()
    );
  }
}

// =============================================================================
// GET FICTIONEER VERSION
// =============================================================================

/**
 * Returns the current Fictioneer major theme version
 *
 * Only the major version number should be displayed to avoid telling potential
 * attackers which specific version (and weak points) to target.
 *
 * @since 5.0.0
 *
 * @return string The version.
 */

function fictioneer_get_version() {
  return sprintf(
    _x( 'Fictioneer %s', 'Fictioneer theme major version notice.', 'fictioneer' ),
    FICTIONEER_MAJOR_VERSION
  );
}

// =============================================================================
// GET BREADCRUMBS
// =============================================================================

if ( ! function_exists( 'fictioneer_get_breadcrumbs' ) ) {
  /**
   * Returns the HTML for the breadcrumbs
   *
   * Renders a breadcrumbs trail supporting RDFa to be readable by search engines.
   *
   * @since 5.0.0
   * @since 5.9.4 - Removed output buffer.
   *
   * @param array  $args['breadcrumbs']  Breadcrumb tuples with label (0) and link (1).
   * @param string $args['post_type']    Optional. Post type of the current page.
   * @param int    $args['post_id']      Optional. Post ID of the current page.
   * @param string $args['template']     Optional. Name of the template file.
   *
   * @return string HTML for the breadcrumbs.
   */

  function fictioneer_get_breadcrumbs( $args ) {
    // Abort if...
    if (
      ! isset( $args['breadcrumbs'] ) ||
      ! is_array( $args['breadcrumbs'] ) ||
      count( $args['breadcrumbs'] ) < 1 ||
      is_front_page()
    ) {
      return '';
    }

    // Filter breadcrumbs array
    $breadcrumbs = apply_filters( 'fictioneer_filter_breadcrumbs_array', $args['breadcrumbs'], $args );

    // Setup
    $count = count( $breadcrumbs );

    // Abort if array does not have two or more items
    if ( $count < 2 ) {
      return '';
    }

    // Build
    $html = '<ol vocab="https://schema.org/" typeof="BreadcrumbList" class="breadcrumbs">';

    foreach ( $breadcrumbs as $key => $value ) {
      $html .= '<li class="breadcrumbs__item" property="itemListElement" typeof="ListItem">';

      if ( $count > $key + 1 && $value[1] ) {
        $html .= '<a property="item" typeof="WebPage" href="' . esc_url( $value[1] ) . '"><span property="name">' . $value[0] . '</span></a>';
      } else {
        $html .= '<span property="name">' . $value[0] . '</span>';
      }

      $html .= '<meta property="position" content="' . ( $key + 1 ) . '"></li>';
    }

    $html .= '</ol>';

    // Return HTML
    return $html;
  }
}

// =============================================================================
// GET COVERS (THUMBNAIL)
// =============================================================================

if ( ! function_exists( 'fictioneer_get_story_page_cover' ) ) {
  /**
   * Returns the HTML for cover image on story pages
   *
   * @since 5.0.0
   * @since 5.9.4 - Removed output buffer.
   *
   * @param array $story  Collection of story data.
   * @param array $args   Optional. Additional arguments.
   *
   * @return string HTML for the cover image.
   */

  function fictioneer_get_story_page_cover( $story, $args = [] ) {
    // Setup
    $classes = $args['classes'] ?? '';

    // Build and return
    return sprintf(
      '<figure class="story__thumbnail ' . $classes . '"><a href="%s" %s>%s<div id="ribbon-read" class="story__thumbnail-ribbon hidden"><div class="ribbon">%s</div></div></a></figure>',
      get_the_post_thumbnail_url( $story['id'], 'full' ),
      fictioneer_get_lightbox_attribute(),
      get_the_post_thumbnail( $story['id'], array( 200, 300 ), array(
        'alt' => esc_attr( sprintf( __( 'Cover of %s', 'fictioneer' ), $story['title'] ) ),
        'class' => 'webfeedsFeaturedVisual story__thumbnail-image'
      )),
      _x( 'Read', 'Caption of the _read_ ribbon.', 'fictioneer' )
    );
  }
}

if ( ! function_exists( 'fictioneer_get_recommendation_page_cover' ) ) {
  /**
   * Returns the HTML for cover image on recommendation pages
   *
   * @since 5.0.0
   * @since 5.9.4 - Removed output buffer.
   *
   * @param WP_Post $recommendation  The post object.
   *
   * @return string HTML for the cover image.
   */

  function fictioneer_get_recommendation_page_cover( $recommendation ) {
    return sprintf(
      '<figure class="recommendation__thumbnail"><a href="%s" %s>%s</a></figure>',
      get_the_post_thumbnail_url( $recommendation->ID, 'full' ),
      fictioneer_get_lightbox_attribute(),
      get_the_post_thumbnail( $recommendation->ID, array( 200, 300 ), array(
        'alt' => esc_attr(
          sprintf(
            __( '%s Cover', 'fictioneer' ),
            fictioneer_get_safe_title( $recommendation->ID )
          )
        ),
        'class' => 'webfeedsFeaturedVisual recommendation__thumbnail-image'
      ))
    );
  }
}

/**
 * Returns the small card thumbnails
 *
 * Checks for the normal thumbnail and landscape image. If not found,
 * it will recursively try the parent post if there is one.
 *
 * @since 5.14.0
 *
 * @param int   $post_id  The post ID.
 * @param array $args {
 *   Optional. An array of additional arguments.
 *
 *   @type int|null     $parent_id  ID of a parent post, usually a story.
 *   @type string|null  $title  Title of the post.
 *   @type string|null  $aspect_ratio  Aspect ratio CSS value.
 *   @type bool|null    $vertical  Whether the card is rendered vertical.
 *   @type bool|null    $seamless  Whether the card is rendered seamless (if vertical).
 * }
 *
 * @return array The thumbnail HTML, the thumbnail URL, and the full thumbnail URL.
 */

function fictioneer_get_small_card_thumbnail( $post_id, $args = [] ) {
  // Setup
  $parent_id = $args['parent_id'] ?? get_post_meta( $post_id, 'fictioneer_chapter_story', true );
  $landscape_image_id = get_post_meta( $post_id, 'fictioneer_landscape_image', true );
  $thumbnail_size = ( $args['vertical'] ?? 0 ) ? 'large' : 'snippet';
  $thumbnail_args = array(
    'alt' => sprintf( __( '%s Cover', 'fictioneer' ), $args['title'] ?? '' ),
    'class' => 'no-auto-lightbox'
  );
  $aspect_ratio = $args['aspect_ratio'] ?? 0;
  $thumbnail = null;
  $thumbnail_url = null;
  $thumbnail_full_url = null;

  if ( ! $aspect_ratio && ( $args['vertical'] ?? 0 ) ) {
    $aspect_ratio = '3/1';
  }

  // Landscape thumbnail?
  if ( $landscape_image_id && $aspect_ratio ) {
    $ratio = fictioneer_get_split_aspect_ratio( $aspect_ratio );

    if ( $ratio[0] - $ratio[1] > 1 ) {
      $thumbnail = wp_get_attachment_image( $landscape_image_id, 'large', false, $thumbnail_args );
      $thumbnail_url = wp_get_attachment_url( $landscape_image_id, 'large', false, $thumbnail_args );
      $thumbnail_full_url = wp_get_attachment_url( $landscape_image_id, 'full', false, $thumbnail_args );
    }
  }

  // Normal thumbnail?
  if ( ! $thumbnail ) {
    $thumbnail = get_the_post_thumbnail( $post_id, $thumbnail_size, $thumbnail_args );
    $thumbnail_url = get_the_post_thumbnail_url( $landscape_image_id, $thumbnail_size, false, $thumbnail_args );
    $thumbnail_full_url = get_the_post_thumbnail_url( $post_id, 'full' );
  }

  // Parent thumbnail?
  if ( ! $thumbnail && $parent_id )  {
    unset( $args['parent_id'] ); // Prevent infinite loop

    return fictioneer_get_small_card_thumbnail( $parent_id, $args );
  }

  // Landscape thumbnail fallback?
  if ( ! $thumbnail && $landscape_image_id ) {
    $thumbnail = wp_get_attachment_image( $landscape_image_id, 'large', false, $thumbnail_args );
    $thumbnail_url = wp_get_attachment_url( $landscape_image_id, 'large', false, $thumbnail_args );
    $thumbnail_full_url = wp_get_attachment_url( $landscape_image_id, 'full', false, $thumbnail_args );
  }

  // Return result
  return array(
    'thumbnail' => $thumbnail,
    'thumbnail_url' => $thumbnail_url,
    'thumbnail_full_url' => $thumbnail_full_url
  );
}

// =============================================================================
// GET SUBSCRIBE BUTTONS
// =============================================================================

if ( ! function_exists( 'fictioneer_get_subscribe_options' ) ) {
  /**
   * Returns the HTML for the subscribe options (links)
   *
   * @since 5.0.0
   * @since 5.9.4 - Removed output buffer.
   *
   * @param int|null    $post_id    The post ID the links are for. Defaults to current.
   * @param int|null    $author_id  The author ID the links are for. Defaults to current.
   * @param string|null $feed       RSS feed link. Default determined by post type.
   *
   * @return string HTML for subscribe links.
   */

  function fictioneer_get_subscribe_options( $post_id = null, $author_id = null, $feed = null ) {
    // Setup
    $post_id = $post_id ? $post_id : get_the_ID();
    $story_id = get_post_meta( $post_id, 'fictioneer_chapter_story', true );
    $author_id = $author_id ? $author_id : get_the_author_meta( 'ID' );
    $patreon_link = get_post_meta( $post_id, 'fictioneer_patreon_link', true );
    $kofi_link = get_post_meta( $post_id, 'fictioneer_kofi_link', true );
    $subscribestar_link = get_post_meta( $post_id, 'fictioneer_subscribestar_link', true );
    $feed = get_option( 'fictioneer_enable_theme_rss' ) ? $feed : null;
    $output = [];

    // Look for story support links if none provided by post
    if ( ! empty( $story_id ) ) {
      if ( empty( $patreon_link ) ) {
        $patreon_link = get_post_meta( $story_id, 'fictioneer_patreon_link', true );
      }

      if ( empty( $kofi_link ) ) {
        $kofi_link = get_post_meta( $story_id, 'fictioneer_kofi_link', true );
      }

      if ( empty( $subscribestar_link ) ) {
        $subscribestar_link = get_post_meta( $story_id, 'fictioneer_subscribestar_link', true );
      }
    }

    // Look for author support links if none provided by post or story
    if ( empty( $patreon_link ) ) {
      $patreon_link = get_the_author_meta( 'fictioneer_user_patreon_link', $author_id );
    }

    if ( empty( $kofi_link ) ) {
      $kofi_link = get_the_author_meta( 'fictioneer_user_kofi_link', $author_id );
    }

    if ( empty( $subscribestar_link ) ) {
      $subscribestar_link = get_the_author_meta( 'fictioneer_user_subscribestar_link', $author_id );
    }

    // Get right RSS link if not provided
    if ( ! $feed && get_option( 'fictioneer_enable_theme_rss' ) ) {
      $feed = fictioneer_get_rss_link( get_post_type(), $post_id );
    }

    // Patreon link
    if ( $patreon_link ) {
      $output['patreon'] = sprintf(
        '<a href="%s" target="_blank" rel="noopener" class="_align-left"><i class="fa-brands fa-patreon"></i> <span>%s</span></a>',
        esc_url( $patreon_link ),
        __( 'Follow on Patreon', 'fictioneer' )
      );
    }

    // Ko-fi link
    if ( $kofi_link ) {
      $output['kofi'] = sprintf(
        '<a href="%s" target="_blank" rel="noopener" class="_align-left">%s <span>%s</span></a>',
        esc_url( $kofi_link ),
        fictioneer_get_icon( 'kofi' ),
        __( 'Follow on Ko-Fi', 'fictioneer' )
      );
    }

    // SubscribeStar link
    if ( $subscribestar_link ) {
      $output['subscribestar'] = sprintf(
        '<a href="%s" target="_blank" rel="noopener" class="_align-left"><i class="fa-solid fa-s"></i> <span>%s</span></a>',
        esc_url( $subscribestar_link ),
        __( 'Follow on SubscribeStar', 'fictioneer' )
      );
    }

    // Feed reader links
    if ( $feed ) {
      $output['feedly'] = sprintf(
        '<a href="https://feedly.com/i/subscription/feed/%s" target="_blank" rel="noopener" class="_align-left" aria-label="%s">%s <span>%s</span></a>',
        urlencode( $feed ),
        esc_attr__( 'Follow on Feedly', 'fictioneer' ),
        fictioneer_get_icon( 'feedly' ),
        __( 'Follow on Feedly', 'fictioneer' )
      );

      $output['inoreader'] = sprintf(
        '<a href="https://www.inoreader.com/?add_feed=%s" target="_blank" rel="noopener" class="_align-left" aria-label="%s">%s <span>%s</span></a>',
        urlencode( $feed ),
        esc_attr__( 'Follow on Inoreader', 'fictioneer' ),
        fictioneer_get_icon( 'inoreader' ),
        __( 'Follow on Inoreader', 'fictioneer' )
      );
    }

    // Apply filter
    $output = apply_filters( 'fictioneer_filter_subscribe_buttons', $output, $post_id, $author_id, $feed );

    // Implode and return HTML
    return implode( '', $output );
  }
}

// =============================================================================
// GET STORY BUTTONS
// =============================================================================

if ( ! function_exists( 'fictioneer_get_story_buttons' ) ) {
  /**
   * Returns the HTML for the story buttons
   *
   * @since 5.0.0
   * @since 5.9.4 - Removed output buffer.
   *
   * @param array     $args['story_data']  Collection of story data.
   * @param int       $args['story_id']    The story post ID.
   * @param bool|null $args['follow']      Optional. Only available in some calls and for filters.
   * @param bool|null $args['reminder']    Optional. Only available in some calls and for filters.
   * @param bool|null $args['subscribe']   Optional. Only available in some calls and for filters.
   * @param bool|null $args['download']    Optional. Only available in some calls and for filters.
   *
   * @return string HTML for the story buttons.
   */

  function fictioneer_get_story_buttons( $args ) {
    // Setup
    $story_data = $args['story_data'];
    $story_id = $args['story_id'];
    $ebook_upload = get_post_meta( $story_id, 'fictioneer_story_ebook_upload_one', true ); // Attachment ID
    $subscribe_buttons = fictioneer_get_subscribe_options();
    $output = [];

    // Flags
    $show_epub_download = $story_data['chapter_count'] > 0 && get_post_meta( $story_id, 'fictioneer_story_epub_preface', true ) && get_option( 'fictioneer_enable_epubs' ) && ! get_post_meta( $story_id, 'fictioneer_story_no_epub', true );
    $show_login = get_option( 'fictioneer_enable_oauth' ) && ! is_user_logged_in();

    // Subscribe
    if ( ! empty( $subscribe_buttons ) ) {
      $output['subscribe'] = sprintf(
        '<div class="toggle-last-clicked subscribe-menu-toggle button _secondary popup-menu-toggle _popup-right-if-last" tabindex="0" role="button" aria-label="%s"><div><i class="fa-solid fa-bell"></i> %s</div><div class="popup-menu _bottom _center">%s</div></div>',
        fcntr( 'subscribe', true ),
        fcntr( 'subscribe' ),
        $subscribe_buttons
      );
    }

    // File download
    if ( $show_epub_download && ! $ebook_upload ) {
      $output['epub'] = sprintf(
        '<a href="%s" class="button _secondary" rel="noreferrer noopener nofollow" data-action="download-epub" data-story-id="%d" aria-label="%s" download><i class="fa-solid fa-cloud-download-alt"></i><span class="span-epub hide-below-640">%s</span></a>',
        esc_url( home_url( 'download-epub/' . $args['story_id'] ) ),
        $args['story_id'],
        esc_attr__( 'Download ePUB', 'fictioneer' ),
        __( 'ePUB', 'fictioneer' )
      );
    } elseif ( wp_get_attachment_url( $ebook_upload ) ) {
      $output['ebook'] = sprintf(
        '<a href="%s" class="button _secondary" rel="noreferrer noopener nofollow" aria-label="%s" download><i class="fa-solid fa-cloud-download-alt"></i><span class="span-epub hide-below-640">%s</span></a>',
        esc_url( wp_get_attachment_url( $ebook_upload ) ),
        esc_attr__( 'Download eBook', 'fictioneer' ),
        __( 'eBook', 'fictioneer' )
      );
    }

    // Reminder
    if ( get_option( 'fictioneer_enable_reminders' ) ) {
      $output['reminder'] = sprintf(
        '<button class="button _secondary button-read-later hide-if-logged-out" data-story-id="%d"><i class="fa-solid fa-clock"></i><span class="span-follow hide-below-480">%s</span></button>',
        $story_id,
        fcntr( 'read_later' )
      );

      if ( $show_login ) {
        $output['reminder'] .= sprintf(
          '<label for="modal-login-toggle" class="button _secondary button-read-later-notice hide-if-logged-in tooltipped" tabindex="0" data-tooltip="%s"><i class="fa-solid fa-clock"></i><span class="span-follow hide-below-480">%s</span></label>',
          esc_attr__( 'Log in to set Reminders', 'fictioneer' ),
          fcntr( 'read_later' )
        );
      }
    }

    // Follow
    if ( get_option( 'fictioneer_enable_follows' ) ) {
      $output['follow'] = sprintf(
        '<button class="button _secondary button-follow-story hide-if-logged-out" data-story-id="%d"><i class="fa-solid fa-star"></i><span class="span-follow hide-below-400">%s</span></button>',
        $story_id,
        fcntr( 'follow' )
      );

      if ( $show_login ) {
        $output['follow'] .= sprintf(
          '<label for="modal-login-toggle" class="button _secondary button-follow-login-notice hide-if-logged-in tooltipped" tabindex="0" data-tooltip="%s"><i class="fa-regular fa-star off"></i><span class="span-follow hide-below-400">%s</span></label>',
          esc_attr__( 'Log in to Follow', 'fictioneer' ),
          fcntr( 'follow' )
        );
      }
    }

    // Apply filters
    $output = apply_filters( 'fictioneer_filter_story_buttons', $output, $args );

    // Implode and return HTML
    return implode( '', $output );
  }
}

// =============================================================================
// GET MEDIA BUTTONS
// =============================================================================

/**
 * Returns the HTML for the media buttons
 *
 * @since 5.14.0
 * @see fictioneer_get_rss_link()
 *
 * @param int|null    $args['post_id']    Optional. The post ID to use. Defaults to current post ID.
 * @param string|null $args['post_type']  Optional. The post type to use. Defaults to current post type.
 * @param bool|null   $args['share']      Optional. Whether to show the share modal button. Default true.
 * @param bool|null   $args['rss']        Optional. Whether to show the RSS feed buttons. Default true.
 *
 * @return string HTML for the media buttons.
 */

function fictioneer_get_media_buttons( $args = [] ) {
  // Setup
  $post_type = ( $args['post_type'] ?? '' ) ?: get_post_type();
  $post_id = ( $args['post_id'] ?? 0 ) ?: get_the_ID();
  $feed = fictioneer_get_rss_link( $post_type, $post_id );
  $show_feed = $post_type !== 'fcn_story' ||
    ( $post_type === 'fcn_story' && get_post_meta( $post_id, 'fictioneer_story_status', true ) !== 'Oneshot' );
  $output = [];

  // Share modal button
  if ( $args['share'] ?? 1 ) {
    $output['share'] = '<label for="modal-sharing-toggle" class="tooltipped media-buttons__item" data-tooltip="' . esc_attr__( 'Share', 'fictioneer' ) . '" tabindex="0"><i class="fa-solid fa-share-nodes"></i></label>';
  }

  // Feed buttons
  if ( $feed && $show_feed && ( $args['rss'] ?? 1 ) ) {
    $feed_url = urlencode( $feed );

    // Post feed
    if ( $post_type === 'fcn_story' && ! get_post_meta( $post_id, 'fictioneer_story_hidden', true ) ) {
      $output['post_rss'] = '<a href="' . $feed . '" class="rss-link tooltipped media-buttons__item" target="_blank" rel="noopener" data-tooltip="' . esc_attr__( 'Story RSS Feed', 'fictioneer' ) . '" aria-label="' . esc_attr__( 'Story RSS Feed', 'fictioneer' ) . '">' . fictioneer_get_icon( 'fa-rss' ) . '</a>';
    }

    // Feedly
    $output['feedly'] = '<a href="https://feedly.com/i/subscription/feed/' . $feed_url . '" class="feedly tooltipped hide-below-640 media-buttons__item" target="_blank" rel="noopener" data-tooltip="' . esc_attr__( 'Follow on Feedly', 'fictioneer' ) . '" aria-label="' . esc_attr__( 'Follow on Feedly', 'fictioneer' ) . '">' . fictioneer_get_icon( 'feedly' ) . '</a>';

    // Inoreader
    $output['inoreader'] = '<a href="https://www.inoreader.com/?add_feed=' . $feed_url . '" class="inoreader tooltipped hide-below-640 media-buttons__item" target="_blank" rel="noopener" data-tooltip="' . esc_attr__( 'Follow on Inoreader', 'fictioneer' ) . '" aria-label="' . esc_attr__( 'Follow on Inoreader', 'fictioneer' ) . '">' . fictioneer_get_icon( 'inoreader' ) . '</a>';
  }

  // Apply filters
  $output = apply_filters( 'fictioneer_filter_media_buttons', $output, $args );

  // Implode and return HTML
  return '<div class="media-buttons">' . implode( '', $output ) . '</div>';
}

// =============================================================================
// GET CHAPTER MICRO MENU
// =============================================================================

if ( ! function_exists( 'fictioneer_get_chapter_micro_menu' ) ) {
  /**
   * Returns the HTML for the chapter warning section
   *
   * @since 5.0.0
   * @since 5.9.4 - Removed output buffer.
   *
   * @param WP_Post|null $args['story_post']   Optional. Post object of the story.
   * @param int          $args['chapter_id']   The chapter ID.
   * @param array        $args['chapter_ids']  IDs of visible chapters in the same story or empty array.
   * @param int|boolean  $args['prev_index']   Index of previous chapter or false if outside bounds.
   * @param int|boolean  $args['next_index']   Index of next chapter or false if outside bounds.
   *
   * @return string The chapter micro menu HTML.
   */

  function fictioneer_get_chapter_micro_menu( $args ) {
    // Setup
    $micro_menu = [];

    // Only if there is a story...
    if ( ! empty( $args['story_post'] ) ) {
      // Mobile menu chapter list
      $micro_menu['chapter_list'] = '<label id="micro-menu-label-open-chapter-list" for="mobile-menu-toggle" class="micro-menu__item micro-menu__chapter-list show-below-desktop" tabindex="-1"><i class="fa-solid fa-list"></i></label>';

      // Story link
      $story_link = get_post_meta( $args['story_post']->ID ?? 0, 'fictioneer_story_redirect_link', true )
        ?: get_permalink( $args['story_post'] );

      // Link to story
      $micro_menu['story_link'] = sprintf(
        '<a href="%s" title="%s" class="micro-menu__item" tabindex="-1"><i class="fa-solid fa-book"></i></a>',
        $story_link,
        esc_attr( get_the_title( $args['story_post']->ID ) )
      );
    }

    // Open formatting modal
    $micro_menu['formatting'] = sprintf(
      '<label for="modal-formatting-toggle" class="micro-menu__item micro-menu__modal-formatting" tabindex="-1">%s</label>',
      fictioneer_get_icon( 'font-settings' )
    );

    // Open fullscreen
    $micro_menu['open_fullscreen'] = sprintf(
      '<button type="button" title="%s" class="micro-menu__item micro-menu__enter-fullscreen open-fullscreen hide-on-iOS hide-on-fullscreen" tabindex="-1">%s</button>',
      esc_attr__( 'Enter fullscreen', 'fictioneer' ),
      fictioneer_get_icon( 'expand' )
    );

    // Close fullscreen
    $micro_menu['close_fullscreen'] = sprintf(
      '<button type="button" title="%s" class="micro-menu__item micro-menu__close-fullscreen close-fullscreen hide-on-iOS show-on-fullscreen hidden" tabindex="-1">%s</button>',
      esc_attr__( 'Exit fullscreen', 'fictioneer' ),
      fictioneer_get_icon( 'collapse' )
    );

    // Scroll to bookmark
    $micro_menu['bookmark_jump'] = sprintf(
      '<button type="button" title="%s" class="micro-menu__item micro-menu__bookmark button--bookmark hidden" tabindex="-1"><i class="fa-solid fa-bookmark"></i></button>',
      fcntr( 'jump_to_bookmark', true )
    );

    // Navigate to previous chapter
    if ($args['prev_index'] !== false) {
      $micro_menu['previous'] = sprintf(
        '<a href="%s" title="%s" class="micro-menu__item micro-menu__previous previous" tabindex="-1"><i class="fa-solid fa-caret-left"></i></a>',
        get_permalink( $args['chapter_ids'][ $args['prev_index'] ] ),
        esc_attr( get_the_title( $args['chapter_ids'][ $args['prev_index'] ] ) )
      );
    }

    // Scroll to top
    $micro_menu['top'] = sprintf(
      '<a href="#top" data-block="center" aria-label="%s" class="micro-menu__item micro-menu__up up" tabindex="-1"><i class="fa-solid fa-caret-up"></i></a>',
      esc_attr__( 'Scroll to top of the chapter', 'fictioneer' )
    );

    // Navigate to next chapter
    if ($args['next_index']) {
      $micro_menu['next'] = sprintf(
        '<a href="%s" title="%s" class="micro-menu__item micro-menu__next next" tabindex="-1"><i class="fa-solid fa-caret-right"></i></a>',
        get_permalink( $args['chapter_ids'][ $args['next_index'] ] ),
        esc_attr( get_the_title( $args['chapter_ids'][ $args['next_index'] ] ) )
      );
    }

    // Filter micro menu array
    $micro_menu = apply_filters( 'fictioneer_filter_chapter_micro_menu', $micro_menu, $args );

    // Implode and return HTML
    return '<div id="micro-menu" class="micro-menu">' . implode( '', $micro_menu ) . '</div>';
  }
}

// =============================================================================
// GET SIMPLE CHAPTER LIST
// =============================================================================

if ( ! function_exists( 'fictioneer_get_chapter_list_items' ) ) {
  /**
   * Returns the HTML for chapter list items with icon and link
   *
   * @since 5.0.0
   * @since 5.9.3 - Added meta field caching.
   * @since 5.9.4 - Removed output buffer.
   * @since 5.12.2 - Use permalinks instead of page ID.
   *
   * @param int   $story_id       ID of the story.
   * @param array $data           Prepared data of the story.
   * @param int   $current_index  Index in chapter ID array.
   *
   * @return string HTML list of chapters.
   */

  function fictioneer_get_chapter_list_items( $story_id, $data, $current_index ) {
    // Meta cache?
    $cache_plugin_active = fictioneer_caching_active( 'chapter_list_items' );

    if ( ! $cache_plugin_active ) {
      $last_story_update = get_post_modified_time( 'U', true, $story_id );
      $meta_cache = get_post_meta( $story_id, 'fictioneer_story_chapter_index_html', true );

      if ( $meta_cache && is_array( $meta_cache ) && array_key_exists( 'html', $meta_cache ) ) {
        // ... still up-to-date and valid?
        if (
          ( $meta_cache['timestamp'] ?? 0 ) == $last_story_update &&
          ( $meta_cache['valid_until'] ?? 0 ) > time()
        ) {
          return $meta_cache['html'];
        }
      }
    }

    // Setup
    $chapters = fictioneer_get_story_chapter_posts( $story_id );
    $hide_icons = get_post_meta( $story_id, 'fictioneer_story_hide_chapter_icons', true ) || get_option( 'fictioneer_hide_chapter_icons' );
    $html = '';

    // Loop chapters...
    foreach ( $chapters as $chapter ) {
      // Prepare
      $classes = [];
      $title = trim( $chapter->post_title );
      $list_title = get_post_meta( $chapter->ID, 'fictioneer_chapter_list_title', true );
      $list_title = trim( wp_strip_all_tags( $list_title ) );
      $text_icon = get_post_meta( $chapter->ID, 'fictioneer_chapter_text_icon', true );
      $icon = '';

      // Check for empty title
      if ( empty( $title ) && empty( $list_title ) ) {
        $title = sprintf(
          _x( '%1$s — %2$s', '[Date] — [Time] if chapter title is missing.', 'fictioneer' ),
          get_the_date( '', $chapter->ID ),
          get_the_time( '', $chapter->ID )
        );
      }

      // Mark for password
      if ( ! empty( $chapter->post_password ) ) {
        $classes[] = 'has-password';
      }

      // Chapter icon
      if ( empty( $text_icon ) && ! $hide_icons ) {
        $icon = '<i class="' . fictioneer_get_icon_field('fictioneer_chapter_icon', $chapter->ID) . '"></i>';
      } elseif ( ! $hide_icons ) {
        $icon = '<span class="text-icon">' . $text_icon . '</span>';
      }

      // HTML
      $html .= sprintf(
        '<li class="%s" data-id="%d"><a href="%s">%s<span>%s</span></a></li>',
        implode( ' ', $classes ),
        $chapter->ID,
        get_the_permalink( $chapter->ID ),
        $icon,
        $list_title ?: $title
      );
    }

    // Update meta cache
    if ( ! $cache_plugin_active ) {
      update_post_meta(
        $story_id,
        'fictioneer_story_chapter_index_html',
        array( 'html' => $html, 'timestamp' => $last_story_update, 'valid_until' => time() + HOUR_IN_SECONDS )
      );
    }

    // Return
    return $html;
  }
}

// =============================================================================
// GET CHAPTER LIST META ROW
// =============================================================================

if ( ! function_exists( 'fictioneer_get_list_chapter_meta_row' ) ) {
  /**
   * Returns HTML for list chapter meta row
   *
   * @since 5.1.2
   * @since 5.9.4 - Removed output buffer.
   *
   * @param array $data  Chapter data for the meta row.
   * @param array $args  Optional arguments.
   *
   * @return string HTML of the list chapter meta row.
   */

  function fictioneer_get_list_chapter_meta_row( $data, $args = [] ) {
    // Setup
    $output = [];
    $has_grid_view = ! empty( $args['grid'] );

    // Password
    if ( ! empty( $data['password'] ) ) {
      $output['protected'] = '<i class="fa-solid fa-lock chapter-group__list-item-protected list-view"></i>';
    }

    // Warning
    if ( ! empty( $data['warning'] ) ) {
      $output['warning'] = sprintf(
        '<span class="chapter-group__list-item-warning list-view">%s</span>',
        sprintf( __( '<b>Warning:</b> %s', 'fictioneer' ), $data['warning'] )
      );
    }

    // Date
    if ( $has_grid_view ) {
      $output['date'] = sprintf(
        '<time datetime="%s" class="chapter-group__list-item-date"><span class="list-view">%s</span><span class="grid-view">%s</span></time>',
        esc_attr( $data['timestamp'] ),
        $data['list_date'],
        $data['grid_date']
      );
    } else {
      $output['date'] = sprintf(
        '<time datetime="%s" class="chapter-group__list-item-date">%s</time>',
        esc_attr( $data['timestamp'] ),
        $data['list_date']
      );
    }

    // Words
    if ( $has_grid_view ) {
      $short_words = fictioneer_shorten_number( $data['words'] );

      $output['words'] = sprintf(
        '<span class="chapter-group__list-item-words" data-number-switch="%s">%s</span>',
        esc_attr( $short_words ),
        sprintf(
          _x( '%s Words', 'Word count in chapter list.', 'fictioneer' ),
          number_format_i18n( $data['words'] )
        )
      );
    } else {
      $output['words'] = sprintf(
        '<span class="chapter-group__list-item-words">%s</span>',
        sprintf(
          _x( '%s Words', 'Word count in chapter list.', 'fictioneer' ),
          number_format_i18n( $data['words'] )
        )
      );
    }

    // Apply filters
    $output = apply_filters( 'fictioneer_filter_list_chapter_meta_row', $output, $data, $args );

    // Implode and return HTML
    return '<div class="chapter-group__list-item-subrow truncate _1-1">' . implode( ' ', $output ) . '</div>';
  }
}

// =============================================================================
// GET TAXONOMY PILLS
// =============================================================================

if ( ! function_exists( 'fictioneer_get_taxonomy_pills' ) ) {
  /**
   * Returns the HTML for taxonomy tags
   *
   * @since 5.0.0
   * @since 5.9.4 - Removed output buffer.
   *
   * @param array  $taxonomy_groups  Arrays of WP_Term objects.
   * @param string $context          Optional. The render context or location.
   * @param string $classes          Optional. Additional CSS classes.
   *
   * @return string HTML for the taxonomy tags.
   */

  function fictioneer_get_taxonomy_pills( $taxonomy_groups, $context = '', $classes = '' ) {
    // Abort conditions
    if ( ! is_array( $taxonomy_groups ) || count( $taxonomy_groups ) < 1 ) {
      return '';
    }

    // Setup
    $html = '';

    // Loop over all groups...
    foreach ( $taxonomy_groups as $key => $group ) {
      // Check for empty group
      if ( ! $group || ! is_array( $group ) || count( $group ) < 1 ) {
        continue;
      }

      // Filter group
      $group = apply_filters( 'fictioneer_filter_taxonomy_pills_group', $group, $key, $context );

      // Process group
      foreach ( $group as $taxonomy ) {
        $html .= sprintf(
          '<a href="%s" class="tag-pill _taxonomy-%s _taxonomy-slug-%s %s">%s</a>',
          get_tag_link( $taxonomy ),
          str_replace( 'fcn_', '', $taxonomy->taxonomy ),
          $taxonomy->slug,
          $classes,
          $taxonomy->name
        );
      }
    }

    // Return HTML
    return $html;
  }
}

// =============================================================================
// GET RSS LINK
// =============================================================================

if ( ! function_exists( 'fictioneer_get_rss_link' ) ) {
  /**
   * Returns the escaped RSS link
   *
   * @since 5.0.0
   *
   * @param string|null $post_type  The post type. Defaults to current post type.
   * @param int|null    $post_id    The post ID. Defaults to current post ID.
   *
   * @return string|boolean Escaped RSS url or false if no feed found.
   */

  function fictioneer_get_rss_link( $post_type = null, $post_id = null ) {
    // Abort conditions
    if ( ! get_option( 'fictioneer_enable_theme_rss' ) ) {
      return false;
    }

    // Setup
    $post_type = $post_type ? $post_type : get_post_type();
    $post_id = $post_id ? $post_id : get_the_ID();
    $feed = false;

    // Pick correct RSS link
    switch ( $post_type ) {
      case 'fcn_story':
        $feed = esc_url( add_query_arg( 'story_id', $post_id, home_url( 'feed/rss-chapters' ) ) );
        break;
      case 'fcn_chapter':
        $story_id = get_post_meta( $post_id, 'fictioneer_chapter_story', true );
        if ( $story_id ) {
          $feed = esc_url( add_query_arg( 'story_id', $story_id, home_url( 'feed/rss-chapters' ) ) );
        };
        break;
      case 'post':
        $feed = esc_url( home_url( 'feed' ) );
        break;
    }

    // Apply filter
    $feed = apply_filters( 'fictioneer_filter_rss_link', $feed, $post_type, $post_id );

    // Return result
    return $feed;
  }
}

// =============================================================================
// GET USER SUBMENU ITEMS
// =============================================================================

if ( ! function_exists( 'fictioneer_user_menu_items' ) ) {
  /**
   * Returns the HTML for the user submenu in the navigation bar
   *
   * @since 5.0.0
   * @since 5.9.4 - Removed output buffer.
   *
   * @return string The HTML for the user submenu.
   */

  function fictioneer_user_menu_items() {
    // Setup
    $bookmarks_link = fictioneer_get_assigned_page_link( 'fictioneer_bookmarks_page' );
    $bookshelf_link = fictioneer_get_assigned_page_link( 'fictioneer_bookshelf_page' );
    $discord_link = get_option( 'fictioneer_discord_invite_link' );
    $profile_link = get_edit_profile_url( 0 ); // Make sure this is always the default link
    $profile_page_id = intval( get_option( 'fictioneer_user_profile_page', -1 ) ?: -1 );
    $output = [];

    if ( ! empty( $profile_page_id ) && $profile_page_id > 0 ) {
      $profile_link = fictioneer_get_assigned_page_link( 'fictioneer_user_profile_page' );
    }

    // Profile link
    if ( ! empty( $profile_link ) && fictioneer_show_auth_content() ) {
      $output['account'] = '<li class="menu-item hide-if-logged-out"><a href="' . esc_url( $profile_link ) . '" rel="noopener noreferrer nofollow">' . fcntr( 'account' ) . '</a></li>';
    }

    // Site settings
    $output['site_settings'] = '<li class="menu-item"><label for="modal-site-settings-toggle" tabindex="0">' . fcntr( 'site_settings' ) . '</label></li>';

    // Discord link
    if ( ! empty( $discord_link ) ) {
      $output['discord'] = '<li class="menu-item"><a href="' . esc_url( $discord_link ) . '" target="_blank" rel="noopener noreferrer nofollow">' . __( 'Discord', 'fictioneer' ) . '</a></li>';
    }

    // Bookshelf
    if (
      $bookshelf_link &&
      fictioneer_show_auth_content() &&
      (
        get_option( 'fictioneer_enable_checkmarks' ) ||
        get_option( 'fictioneer_enable_follows' ) ||
        get_option( 'fictioneer_enable_reminders' )
      )
    ) {
      $output['bookshelf'] = '<li class="menu-item hide-if-logged-out"><a href="' . esc_url( $bookshelf_link ) . '" rel="noopener noreferrer nofollow">' . fcntr( 'bookshelf' ) . '</a></li>';
    }

    // Bookmarks
    if ( $bookmarks_link && get_option( 'fictioneer_enable_bookmarks' ) ) {
      $output['bookmarks'] = '<li class="menu-item"><a href="' . esc_url( $bookmarks_link ) . '" rel="noopener noreferrer nofollow">' . fcntr( 'bookmarks' ) . '</a></li>';
    }

    // Logout
    if ( fictioneer_show_auth_content() ) {
      $output['logout'] = '<li class="menu-item hide-if-logged-out"><a href="' . fictioneer_get_logout_url() . '" data-click="logout" rel="noopener noreferrer nofollow">' . fcntr( 'logout' ) . '</a></li>';
    }

    // Apply filters
    $output = apply_filters( 'fictioneer_filter_user_menu_items', $output );

    // Implode and return HTML
    return implode( '', $output );
  }
}

// =============================================================================
// GET POST META ITEMS
// =============================================================================

if ( ! function_exists( 'fictioneer_get_post_meta_items' ) ) {
  /**
   * Returns the HTML for the blog post meta row
   *
   * @since 5.0.0
   * @since 5.9.4 - Removed output buffer.
   *
   * @param array $args  Optional arguments.
   *
   * @return string The HTML for the blog post meta row.
   */

  function fictioneer_get_post_meta_items( $args = [] ) {
    // Setup
    $comments_number = get_comments_number( get_the_ID() );
    $output = [];

    // Date node
    if ( ! ( $args['no_date'] ?? 0 ) ) {
      $output['date'] = sprintf(
        '<time datetime="%s" class="post__date"><i class="fa-solid fa-clock" title="%s"></i> %s</time>',
        esc_attr( get_the_time( 'c' ) ),
        esc_attr__( 'Published', 'fictioneer' ),
        get_the_date()
      );
    }

    // Author node
    if ( ! ( $args['no_author'] ?? 0 ) && get_option( 'fictioneer_show_authors' ) ) {
      $output['author'] = sprintf(
        '<div class="post__author"><i class="fa-solid fa-circle-user"></i> %s</div>',
        fictioneer_get_author_node( get_the_author_meta( 'ID' ) )
      );
    }

    // Category node
    if ( ! ( $args['no_cat'] ?? 0 ) ) {
      $output['category'] = sprintf(
        '<div class="post__categories"><i class="fa-solid fa-tags"></i> %s</div>',
        get_the_category_list(', ')
      );
    }

    // Comments node
    if ( ! ( $args['no_comments'] ?? 0 ) && $comments_number > 0 ) {
      $comments_link = is_single() ? '#comments' : get_the_permalink() . '#comments';

      $output['comments'] = sprintf(
        '<div class="post__comments-number"><i class="fa-solid fa-message"></i> <a href="%s">%s</a></div>',
        $comments_link,
        sprintf(
          _n( '%s Comment', '%s Comments', $comments_number, 'fictioneer' ),
          number_format_i18n( $comments_number )
        )
      );
    }

    // Apply filters
    $output = apply_filters( 'fictioneer_filter_post_meta_items', $output, $args );

    // Implode and return HTML
    return implode( '', $output );
  }
}

// =============================================================================
// GET CARD CONTROLS
// =============================================================================

if ( ! function_exists( 'fictioneer_get_card_controls' ) ) {
  /**
   * Returns the HTML for the card controls
   *
   * @since 5.0.0
   * @since 5.9.4 - Removed output buffer.
   *
   * @param int $story_id    The story ID.
   * @param int $chapter_id  Optional. The chapter ID (use only for chapters).
   *
   * @return string The HTML for the card controls.
   */

  function fictioneer_get_card_controls( $story_id, $chapter_id = null ) {
    // Setup
    $can_login = is_user_logged_in() || get_option( 'fictioneer_enable_oauth' );
    $can_checkmarks = $can_login && get_option( 'fictioneer_enable_checkmarks' );
    $can_follows = $can_login && get_option( 'fictioneer_enable_follows' );
    $can_reminders = $can_login && get_option( 'fictioneer_enable_reminders' );
    $is_sticky = FICTIONEER_ENABLE_STICKY_CARDS &&
      get_post_meta( $story_id, 'fictioneer_story_sticky', true ) && ! is_search() && ! is_archive();
    $type = $chapter_id ? 'chapter' : 'story';
    $icons = [];
    $menu = [];

    // Sticky icon
    if ( $is_sticky && ! $chapter_id ) {
      $icons['sticky'] = sprintf(
        '<div class="card__sticky-icon" title="%s"><i class="fa-solid fa-thumbtack"></i></div>',
        esc_attr__( 'Sticky', 'fictioneer' )
      );
    }

    // Reminder icon
    if ( $can_reminders ) {
      $icons['reminder'] = sprintf(
        '<i class="fa-solid fa-clock card__reminder-icon" title="%s" data-story-id="%d"></i>',
        esc_attr( fcntr( 'is_read_later' ) ),
        $story_id
      );
    }

    // Follows icon
    if ( $can_follows ) {
      $icons['follow'] = sprintf(
        '<i class="fa-solid fa-star card__followed-icon" title="%s" data-follow-id="%d"></i>',
        esc_attr( fcntr( 'is_followed' ) ),
        $story_id
      );
    }

    // Checkmark icon
    if ( $can_checkmarks ) {
      $icons['checkmark'] = sprintf(
        '<i class="fa-solid fa-%s card__checkmark-icon" title="%s" data-story-id="%d" data-check-id="%d"></i>',
        empty( $chapter_id ) ? 'check-double' : 'check',
        esc_attr( fcntr( 'is_read' ) ),
        $story_id,
        empty( $chapter_id ) ? $story_id : $chapter_id
      );
    }

    // Follows menu item
    if ( $can_follows ) {
      $menu['follow'] = sprintf(
        '<button class="popup-action-follow" data-click="card-toggle-follow" data-story-id="%1$s">%2$s</button>' .
        '<button class="popup-action-unfollow" data-click="card-toggle-follow" data-story-id="%1$s">%3$s</button>',
        $story_id,
        fcntr( 'follow' ),
        fcntr( 'unfollow' )
      );
    }

    // Reminders menu item
    if ( $can_reminders ) {
      $menu['reminder'] = sprintf(
        '<button class="popup-action-reminder" data-click="card-toggle-reminder" data-story-id="%1$s">%2$s</button>' .
        '<button class="popup-action-forget" data-click="card-toggle-reminder" data-story-id="%1$s">%3$s</button>',
        $story_id,
        fcntr( 'read_later' ),
        fcntr( 'forget' )
      );
    }

    // Checkmark menu item
    if ( $can_checkmarks ) {
      $menu['checkmark'] = sprintf(
        '<button class="popup-action-mark-read" data-click="card-toggle-checkmarks" data-story-id="%1$s" data-type="%2$s" data-chapter-id="%3$s" data-mode="set">%4$s</button>' .
        '<button class="popup-action-mark-unread" data-click="card-toggle-checkmarks" data-story-id="%1$s" data-type="%2$s" data-chapter-id="%3$s" data-mode="unset">%5$s</button>',
        $story_id,
        $type,
        $chapter_id ?: 'null',
        fcntr( 'mark_read' ),
        fcntr( 'mark_unread' )
      );
    }

    // Apply filters
    $icons = apply_filters( 'fictioneer_filter_card_control_icons', $icons, $story_id, $chapter_id );
    $menu = apply_filters( 'fictioneer_filter_card_control_menu', $menu, $story_id, $chapter_id );

    // Abort if...
    if ( count( $icons ) < 1 || count( $menu ) < 1 ) {
      return '';
    }

    // Build menu
    $menu_html = '';

    if ( count( $menu ) > 0 ) {
      $menu_html = sprintf(
        '<i class="fa-solid fa-ellipsis-vertical card__popup-menu-toggle" tabindex="0"></i>' .
        '<div class="popup-menu _bottom">%s</div>',
        implode( '', $menu )
      );
    }

    $output = sprintf(
      '<div class="card__controls %s">%s%s</div>',
      count( $menu ) > 0 ? 'popup-menu-toggle toggle-last-clicked' : '',
      implode( '', $icons ),
      $menu_html
    );

    // Return HTML
    return $output;
  }
}

// =============================================================================
// GENERATE CARD TAGS
// =============================================================================

if ( ! function_exists( 'fictioneer_generate_card_tags' ) ) {
  /**
   * Returns array of card tags with HTML markup
   *
   * @since 5.5.3
   *
   * @param array  $items    Array of terms.
   * @param string $classes  Optional. CSS classes to add.
   *
   * @return array Array of output-ready tags.
   */

  function fictioneer_generate_card_tags( $items, $classes = '' ) {
    $tags = [];

    foreach ( $items as $item ) {
      $link = get_tag_link( $item );

      $tags[] = "<a href='{$link}' class='tag-pill _inline {$classes}'>{$item->name}</a>";
    }

    return $tags;
  }
}

// =============================================================================
// ECHO CARD
// =============================================================================

if ( ! function_exists( 'fictioneer_echo_card' ) ) {
  /**
   * Echo large card for post in loop
   *
   * @since 5.0.0
   *
   * @param array $args  Optional. Card arguments.
   */

  function fictioneer_echo_card( $args = [] ) {
    // Setup
    $type = get_post_type();
    $post_id = get_the_ID();

    // Echo correct card by post type
    switch ( $type ) {
      case 'fcn_chapter':
        if (
          get_post_meta( $post_id, 'fictioneer_chapter_hidden', true ) ||
          get_post_meta( $post_id, 'fictioneer_chapter_no_chapter', true )
        ) {
          get_template_part( 'partials/_card-hidden', null, $args );
        } else {
          get_template_part( 'partials/_card-chapter', null, $args );
        }
        break;
      case 'fcn_story':
        if ( get_post_meta( $post_id, 'fictioneer_story_hidden', true ) ) {
          get_template_part( 'partials/_card-hidden', null, $args );
        } else {
          get_template_part( 'partials/_card-story', null, $args );
        }
        break;
      case 'fcn_recommendation':
        get_template_part( 'partials/_card-recommendation', null, $args );
        break;
      case 'fcn_collection':
        get_template_part( 'partials/_card-collection', null, $args );
        break;
      case 'post':
        get_template_part( 'partials/_card-post', null, $args );
        break;
      case 'page':
        get_template_part( 'partials/_card-page', null, $args );
        break;
    }
  }
}

// =============================================================================
// GET SUPPORT LINKS
// =============================================================================

/**
 * Returns support links for the post or author
 *
 * @since 5.0.19
 * @since 5.13.1 - Improved and simplified.
 *
 * @param int|null $post_id    The post ID. Defaults to global post.
 * @param int|null $parent_id  The parent ID. Default null.
 * @param int|null $author_id  The author ID. Defaults to post author ID.
 *
 * @return array Array of support links.
 */

function fictioneer_get_support_links( $post_id = null, $parent_id = null, $author_id = null ) {
  global $post;

  // Return early if post ID not available
  if ( ! $post_id && empty( $post ) ) {
    return [];
  }

  // Setup
  $post_id = $post_id ?? $post->ID;
  $author_id = $author_id ?? get_post_field( 'post_author', $post_id );
  $meta_keys = ['topwebfiction', 'patreon', 'kofi', 'subscribestar', 'paypal', 'donation'];
  $links = [];

  // Get story ID if chapter and parent ID not given
  if ( $parent_id === null && get_post_type( $post_id ) == 'fcn_chapter' ) {
    $parent_id = get_post_meta( $post_id, 'fictioneer_chapter_story', true );
  }

  // Iterate over keys of interest
  foreach ( $meta_keys as $key ) {
    // Try chapter meta...
    $meta = get_post_meta( $post_id, "fictioneer_{$key}_link", true );

    // ... if empty, try story meta (if any)...
    $meta = $meta ?: ( $parent_id ? get_post_meta( $parent_id, "fictioneer_{$key}_link", true ) : '' );

    // ... if empty, try author meta...
    $meta = $meta ?: ( $author_id ? get_the_author_meta( "fictioneer_user_{$key}_link", $author_id ) : '' );

    // ... add if a non-empty value was found
    if ( ! empty( $meta ) ) {
      $links[ $key ] = $meta;
    }
  }

  // Apply filters and return links
  return apply_filters( 'fictioneer_filter_get_support_links', $links, $post_id, $parent_id, $author_id );
}

// =============================================================================
// GET STORY BLOG POSTS
// =============================================================================

if ( ! function_exists( 'fictioneer_get_story_blog_posts' ) ) {
  /**
   * Returns WP_Query with blog posts associated with the story
   *
   * @since 5.4.8
   *
   * @param int $story_id  The story ID.
   *
   * @return WP_Query Queried blog posts.
   */

  function fictioneer_get_story_blog_posts( $story_id ) {
    // Setup
    $category = implode( ', ', wp_get_post_categories( $story_id ) );
    $blog_posts = new WP_Query();

    // Query by category
    $blog_category_query_args = array(
      'ignore_sticky_posts' => 1,
      'author__in'  => fictioneer_get_post_author_ids( $story_id ),
      'nopaging' => false,
      'posts_per_page' => 10,
      'cat' => empty( $category ) ? '99999999' : $category,
      'no_found_rows' => true,
      'update_post_meta_cache' => false,
      'update_post_term_cache' => false
    );

    $blog_category_posts = new WP_Query( $blog_category_query_args );

    // Query by relationship
    $blog_relationship_query_args = array(
      'ignore_sticky_posts' => 1,
      'author__in'  => fictioneer_get_post_author_ids( $story_id ),
      'nopaging' => false,
      'posts_per_page' => 10,
      'no_found_rows' => true,
      'update_post_meta_cache' => false,
      'update_post_term_cache' => false,
      'meta_query' => array(
        array(
          'key' => 'fictioneer_post_story_blogs',
          'value' => '"' . $story_id . '"',
          'compare' => 'LIKE',
        )
      )
    );

    $blog_associated_posts = new WP_Query( $blog_relationship_query_args );

    // Merge results
    $merged_blog_posts = array_merge( $blog_category_posts->posts, $blog_associated_posts->posts );

    // Make sure posts are unique
    $unique_blog_posts = [];

    foreach ( $merged_blog_posts as $blog_post ) {
      if ( ! in_array( $blog_post, $unique_blog_posts ) ) {
        $unique_blog_posts[] = $blog_post;
      }
    }

    // Sort by date
    usort( $unique_blog_posts, function( $a, $b ) {
      return strcmp( $b->post_date, $a->post_date );
    });

    // Limit to 10 posts
    $unique_blog_posts = array_slice( $unique_blog_posts, 0, 10 );

    // Set up query object
    $blog_posts->posts = $unique_blog_posts;
    $blog_posts->post_count = count( $unique_blog_posts );

    // Return merged query
    return $blog_posts;
  }
}

// =============================================================================
// GET STORY CHANGELOG
// =============================================================================

/**
 * Returns story changelog
 *
 * @since 5.7.5
 *
 * @param int $story_id  The story post ID.
 *
 * @return array Array with logged chapter changes since initialization.
 */

function fictioneer_get_story_changelog( $story_id ) {
  $story_id = absint( $story_id );

  if ( empty( $story_id ) ) {
    return [];
  }

  // Setup
  $changelog = get_post_meta( $story_id, 'fictioneer_story_changelog', true );
  $changelog = is_array( $changelog ) ? $changelog : [];

  // Initialize
  if ( empty( $changelog ) ) {
    $changelog[] = array(
      time(),
      _x( 'Initialized.', 'Story changelog initialized.', 'fictioneer' )
    );

    update_post_meta( $story_id, 'fictioneer_story_changelog', $changelog );
  }

  // Return
  return $changelog;
}
