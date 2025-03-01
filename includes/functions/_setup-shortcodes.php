<?php

// =============================================================================
// GET SHORTCODE TRANSIENT
// =============================================================================

if ( ! function_exists( 'fictioneer_shortcode_query' ) ) {
  /**
   * Returns query for shortcode
   *
   * @since 5.4.9
   *
   * @param array $args  Query arguments.
   *
   * @return WP_Query The query result.
   */

  function fictioneer_shortcode_query( $args ) {
    // Query
    $result = new WP_Query( $args );

    // Prime author cache
    if (
      get_option( 'fictioneer_show_authors' ) &&
      ! empty( $result->posts ) &&
      function_exists( 'update_post_author_caches' )
    ) {
      update_post_author_caches( $result->posts );
    }

    return $result;
  }
}

// =============================================================================
// SHORTCODE-BASED RELATIONSHIPS
// =============================================================================

/**
 * Register relationships for posts with certain shortcodes
 *
 * @since 5.0.0
 *
 * @param int     $post_id  The ID of the saved post.
 * @param WP_Post $post     The saved post object.
 */

function fictioneer_update_shortcode_relationships( $post_id, $post ) {
  // Prevent multi-fire
  if ( fictioneer_multi_save_guard( $post_id ) ) {
    return;
  }

  // Setup
  $registry = fictioneer_get_relationship_registry();

  // Look for blog shortcode
  if ( str_contains( $post->post_content, 'fictioneer_blog' ) ) {
    $registry['always'][ $post_id ] = 'shortcode';
  } else {
    unset( $registry['always'][ $post_id ] );
  }

  // Look for story data shortcode
  if (
    str_contains( $post->post_content, 'fictioneer_story_data' ) ||
    str_contains( $post->post_content, 'fictioneer_story_comments' ) ||
    str_contains( $post->post_content, 'fictioneer_story_actions' ) ||
    str_contains( $post->post_content, 'fictioneer_story_section' ) ||
    str_contains( $post->post_content, 'fictioneer_subscribe_button' )
  ) {
    $registry['always'][ $post_id ] = 'shortcode';
  } else {
    unset( $registry['always'][ $post_id ] );
  }

  // Look for article cards shortcode
  if ( str_contains( $post->post_content, 'fictioneer_article_cards' ) ) {
    $registry['always'][ $post_id ] = 'shortcode';
  } else {
    unset( $registry['always'][ $post_id ] );
  }

  // Look for showcase shortcode
  if ( str_contains( $post->post_content, 'fictioneer_showcase' ) ) {
    $registry['always'][ $post_id ] = 'shortcode';
  } else {
    unset( $registry['always'][ $post_id ] );
  }

  // Look for post-related shortcode
  if ( str_contains( $post->post_content, 'fictioneer_latest_posts' ) ) {
    $registry['ref_posts'][ $post_id ] = 'shortcode';
  } else {
    unset( $registry['ref_posts'][ $post_id ] );
  }

  // Look for chapter-related shortcodes
  if (
    str_contains( $post->post_content, 'fictioneer_chapter_list' ) ||
    str_contains( $post->post_content, 'fictioneer_latest_chapters' )
  ) {
    $registry['ref_chapters'][ $post_id ] = 'shortcode';
  } else {
    unset( $registry['ref_chapters'][ $post_id ] );
  }

  // Look for story-related shortcodes
  if (
    str_contains( $post->post_content, 'fictioneer_latest_stories' ) ||
    str_contains( $post->post_content, 'fictioneer_latest_updates' )
  ) {
    $registry['ref_stories'][ $post_id ] = 'shortcode';
  } else {
    unset( $registry['ref_stories'][ $post_id ] );
  }

  // Look for recommendation-related shortcodes
  if ( str_contains( $post->post_content, 'fictioneer_latest_recommendations' ) ) {
    $registry['ref_recommendations'][ $post_id ] = 'shortcode';
  } else {
    unset( $registry['ref_recommendations'][ $post_id ] );
  }

  // Update database
  fictioneer_save_relationship_registry( $registry );
}

if ( FICTIONEER_RELATIONSHIP_PURGE_ASSIST ) {
  add_action( 'save_post', 'fictioneer_update_shortcode_relationships', 10, 2 );
}

// =============================================================================
// GET SHORTCODE DEFAULT ARGS
// =============================================================================

/**
 * Returns sanitized arguments extracted from shortcode attributes
 *
 * @since 5.7.3
 *
 * @param array $attr       Attributes passed to the shortcode.
 * @param int   $def_count  Default for the 'count' argument.
 *
 * @return array The extracted arguments.
 */

function fictioneer_get_default_shortcode_args( $attr, $def_count = -1 ) {
  //--- Sanitize attributes ----------------------------------------------------

  $attr = is_array( $attr ) ?
    array_map( 'sanitize_text_field', $attr ) : sanitize_text_field( $attr );

  //--- Extract arguments ------------------------------------------------------

  $seamless_default = get_theme_mod( 'card_image_style', 'default' ) === 'seamless';
  $thumbnail_default = get_theme_mod( 'card_image_style', 'default' ) !== 'none';

  $uid = wp_unique_id( 'shortcode-id-' );

  $args = array(
    'uid' => $uid,
    'type' => $attr['type'] ?? 'default',
    'count' => max( -1, intval( $attr['count'] ?? $def_count ) ),
    'offset' => max( 0, intval( $attr['offset'] ?? 0 ) ),
    'order' => $attr['order'] ?? '',
    'orderby' => $attr['orderby'] ?? '',
    'page' => max( 1, get_query_var( 'page' ) ?: get_query_var( 'paged' ) ),
    'posts_per_page' => absint( $attr['per_page'] ?? 0 ) ?: get_option( 'posts_per_page' ),
    'post_status' => sanitize_key( $attr['post_status'] ?? 'publish' ),
    'post_ids' => fictioneer_explode_list( $attr['post_ids'] ?? '' ),
    'author' => sanitize_title( $attr['author'] ?? '' ),
    'author_ids' => fictioneer_explode_list( $attr['author_ids'] ?? '' ),
    'excluded_authors' => fictioneer_explode_list( $attr['exclude_author_ids'] ?? '' ),
    'excluded_tags' => fictioneer_explode_list( $attr['exclude_tag_ids'] ?? '' ),
    'excluded_cats' => fictioneer_explode_list( $attr['exclude_cat_ids'] ?? '' ),
    'taxonomies' => fictioneer_get_shortcode_taxonomies( $attr ),
    'relation' => strtolower( $attr['rel'] ?? 'and' ) === 'or' ? 'OR' : 'AND',
    'ignore_sticky' => filter_var( $attr['ignore_sticky'] ?? 0, FILTER_VALIDATE_BOOLEAN ),
    'ignore_protected' => filter_var( $attr['ignore_protected'] ?? 0, FILTER_VALIDATE_BOOLEAN ),
    'only_protected' => filter_var( $attr['only_protected'] ?? 0, FILTER_VALIDATE_BOOLEAN ),
    'vertical' => filter_var( $attr['vertical'] ?? 0, FILTER_VALIDATE_BOOLEAN ),
    'seamless' => filter_var( $attr['seamless'] ?? $seamless_default, FILTER_VALIDATE_BOOLEAN ),
    'aspect_ratio' => sanitize_css_aspect_ratio( $attr['aspect_ratio'] ?? '' ),
    'thumbnail' => filter_var( $attr['thumbnail'] ?? $thumbnail_default, FILTER_VALIDATE_BOOLEAN ),
    'lightbox' => filter_var( $attr['lightbox'] ?? 1, FILTER_VALIDATE_BOOLEAN ),
    'words' => filter_var( $attr['words'] ?? 1, FILTER_VALIDATE_BOOLEAN ),
    'date' => filter_var( $attr['date'] ?? 1, FILTER_VALIDATE_BOOLEAN ),
    'date_format' => sanitize_text_field( $attr['date_format'] ?? '' ),
    'nested_date_format' => sanitize_text_field( $attr['nested_date_format'] ?? '' ),
    'footer' => filter_var( $attr['footer'] ?? 1, FILTER_VALIDATE_BOOLEAN ),
    'footer_author' => filter_var( $attr['footer_author'] ?? 1, FILTER_VALIDATE_BOOLEAN ),
    'footer_chapters' => filter_var( $attr['footer_chapters'] ?? 1, FILTER_VALIDATE_BOOLEAN ),
    'footer_words' => filter_var( $attr['footer_words'] ?? 1, FILTER_VALIDATE_BOOLEAN ),
    'footer_date' => filter_var( $attr['footer_date'] ?? 1, FILTER_VALIDATE_BOOLEAN ),
    'footer_comments' => filter_var( $attr['footer_comments'] ?? 1, FILTER_VALIDATE_BOOLEAN ),
    'footer_status' => filter_var( $attr['footer_status'] ?? 1, FILTER_VALIDATE_BOOLEAN ),
    'footer_rating' => filter_var( $attr['footer_rating'] ?? 1, FILTER_VALIDATE_BOOLEAN ),
    'classes' => esc_attr( wp_strip_all_tags( $attr['classes'] ?? $attr['class'] ?? '' ) ) . " {$uid}",
    'infobox' => filter_var( $attr['infobox'] ?? 1, FILTER_VALIDATE_BOOLEAN ),
    'source' => filter_var( $attr['source'] ?? 1, FILTER_VALIDATE_BOOLEAN ),
    'splide' => sanitize_text_field( $attr['splide'] ?? '' ),
    'cache' => filter_var( $attr['cache'] ?? 1, FILTER_VALIDATE_BOOLEAN )
  );

  //--- Fixes ------------------------------------------------------------------

  // Update count if limited to post IDs
  if ( ! empty( $args['post_ids'] ) ) {
    $args['count'] = count( $args['post_ids'] );
  }

  // Prepare Splide JSON
  if ( ! empty( $args['splide'] ) ) {
    $args['splide'] = str_replace( "'", '"', $args['splide'] );

    if ( ! fictioneer_is_valid_json( $args['splide'] ) ) {
      $args['splide'] = false;
    } else {
      $splide = json_decode( $args['splide'], true );

      // Turn arrows off by default
      if ( ! preg_match( '/"arrows"\s*:\s*true/', $args['splide'] ) ) {
        $splide['arrows'] = false;
      }

      // Change default arrow SVG path
      if ( ! isset( $splide['arrowPath'] ) ) {
        $splide['arrowPath'] = 'M31.89 18.24c0.98 0.98 0.98 2.56 0 3.54l-15 15c-0.98 0.98-2.56 0.98-3.54 0s-0.98-2.56 0-3.54L26.45 20 13.23 6.76c-0.98-0.98-0.98-2.56 0-3.54s2.56-0.98 3.54 0l15 15';
      }

      $args['splide'] = json_encode( $splide );
    }
  }

  //--- Finish -----------------------------------------------------------------

  return $args;
}

// =============================================================================
// GET SHORTCODE TAXONOMIES
// =============================================================================

/**
 * Extract taxonomies from shortcode attributes
 *
 * @since 5.2.0
 *
 * @param array $attr  Attributes of the shortcode.
 *
 * @return array Array of found taxonomies.
 */

function fictioneer_get_shortcode_taxonomies( $attr ) {
  // Setup
  $taxonomies = [];

  // Tags
  if ( ! empty( $attr['tags'] ) ) {
    $taxonomies['tags'] = fictioneer_explode_list( $attr['tags'] );
  }

  // Categories
  if ( ! empty( $attr['categories'] ) ) {
    $taxonomies['categories'] = fictioneer_explode_list( $attr['categories'] );
  }

  // Fandoms
  if ( ! empty( $attr['fandoms'] ) ) {
    $taxonomies['fandoms'] = fictioneer_explode_list( $attr['fandoms'] );
  }

  // Characters
  if ( ! empty( $attr['characters'] ) ) {
    $taxonomies['characters'] = fictioneer_explode_list( $attr['characters'] );
  }

  // Genres
  if ( ! empty( $attr['genres'] ) ) {
    $taxonomies['genres'] = fictioneer_explode_list( $attr['genres'] );
  }

  // Return
  return $taxonomies;
}

// =============================================================================
// GET SHORTCODE TAX QUERY
// =============================================================================

/**
 * Get shortcode Tax Query
 *
 * @since 5.2.0
 *
 * @param array $args  Arguments of the shortcode partial.
 *
 * @return array Tax Query.
 */

function fictioneer_get_shortcode_tax_query( $args ) {
  // Setup
  $tax_query = [];

  // Are there taxonomies?
  if ( ! empty( $args['taxonomies'] ) ) {
    // Relationship?
    if ( count( $args['taxonomies'] ) > 1 ) {
      $tax_query['relation'] = $args['relation'];
    }

    // Tags?
    if ( ! empty( $args['taxonomies']['tags'] ) ) {
      $tax_query[] = array(
        'taxonomy' => 'post_tag',
        'field' => 'name',
        'terms' => $args['taxonomies']['tags']
      );
    }

    // Categories?
    if ( ! empty( $args['taxonomies']['categories'] ) ) {
      $tax_query[] = array(
        'taxonomy' => 'category',
        'field' => 'name',
        'terms' => $args['taxonomies']['categories']
      );
    }

    // Fandoms?
    if ( ! empty( $args['taxonomies']['fandoms'] ) ) {
      $tax_query[] = array(
        'taxonomy' => 'fcn_fandom',
        'field' => 'name',
        'terms' => $args['taxonomies']['fandoms']
      );
    }

    // Characters?
    if ( ! empty( $args['taxonomies']['characters'] ) ) {
      $tax_query[] = array(
        'taxonomy' => 'fcn_character',
        'field' => 'name',
        'terms' => $args['taxonomies']['characters']
      );
    }

    // Genres?
    if ( ! empty( $args['taxonomies']['genres'] ) ) {
      $tax_query[] = array(
        'taxonomy' => 'fcn_genre',
        'field' => 'name',
        'terms' => $args['taxonomies']['genres']
      );
    }
  }

  // Return
  return $tax_query;
}

// =============================================================================
// SPLIDE
// =============================================================================

/**
 * Returns inline script to initialize Splide ASAP
 *
 * Note: The script tag is only returned once in case multiple sliders
 * are active since only one is needed.
 *
 * @since 5.25.0
 * @since 5.26.1 - Use wp_print_inline_script_tag().
 *
 * @return string The inline script.
 */

function fictioneer_get_splide_inline_init() {
  static $done = null;

  if ( $done ) {
    return '';
  }

  $done = true;

  return wp_get_inline_script_tag(
    'document.addEventListener("DOMContentLoaded",()=>{document.querySelectorAll(".splide:not(.no-auto-splide, .is-initialized)").forEach(e=>{e.querySelector(".splide__list")&&"undefined"!=typeof Splide&&(e.classList.remove("_splide-placeholder"),new Splide(e).mount())})});',
    array(
      'id' => 'fictioneer-iife-splide',
      'class' => 'temp-script',
      'type' => 'text/javascript',
      'data-jetpack-boost' => 'ignore',
      'data-no-optimize' => '1',
      'data-no-defer' => '1',
      'data-no-minify' => '1'
    )
  );
}

// =============================================================================
// SHOWCASE SHORTCODE
// =============================================================================

/**
 * Shortcode to display showcase
 *
 * @since 5.0.0
 *
 * @param string      $attr['for']                 What the showcase is for. Allowed are chapters,
 *                                                 collections, recommendations, and stories.
 * @param string|null $attr['count']               Optional. Maximum number of items. Default 8.
 * @param string|null $attr['author']              Optional. Limit posts to a specific author.
 * @param string|null $attr['post_status']         Optional. Choose a valid post status. Default 'publish'.
 * @param string|null $attr['order']               Optional. Order direction. Default 'DESC'.
 * @param string|null $attr['orderby']             Optional. Order argument. Default 'date'.
 * @param string|null $attr['post_ids']            Optional. Limit posts to specific post IDs.
 * @param string|null $attr['ignore_protected']    Optional. Whether to ignore protected posts. Default false.
 * @param string|null $attr['only_protected']      Optional. Whether to query only protected posts. Default false.
 * @param string|null $attr['author_ids']          Optional. Only include posts by these author IDs.
 * @param string|null $attr['exclude_author_ids']  Optional. Exclude posts with these author IDs.
 * @param string|null $attr['exclude_tag_ids']     Optional. Exclude posts with these tags.
 * @param string|null $attr['exclude_cat_ids']     Optional. Exclude posts with these categories.
 * @param string|null $attr['categories']          Optional. Limit posts to specific category names.
 * @param string|null $attr['tags']                Optional. Limit posts to specific tag names.
 * @param string|null $attr['fandoms']             Optional. Limit posts to specific fandom names.
 * @param string|null $attr['genres']              Optional. Limit posts to specific genre names.
 * @param string|null $attr['characters']          Optional. Limit posts to specific character names.
 * @param string|null $attr['rel']                 Optional. Relationship between taxonomies. Default 'AND'.
 * @param string|null $attr['vertical']            Optional. Whether to show the vertical variant.
 * @param string|null $attr['seamless']            Optional. Whether to render the image seamless. Default false (Customizer).
 * @param string|null $attr['aspect_ratio']        Optional. Aspect ratio of the item. Default empty.
 * @param string|null $attr['height']              Optional. Override the item height. Default empty.
 * @param string|null $attr['min_width']           Optional. Override the item minimum width. Default empty.
 * @param string|null $attr['lightbox']            Optional. Whether the thumbnail is opened in the lightbox. Default true.
 * @param string|null $attr['thumbnail']           Optional. Whether to show the thumbnail. Default true (Customizer).
 * @param string|null $attr['class']               Optional. Additional CSS classes, separated by whitespace.
 * @param string|null $args['splide']              Optional. Configuration JSON for the Splide slider. Default empty.
 * @param string|null $args['quality']             Optional. Size of the images. Default 'medium'.
 *
 * @return string The captured shortcode HTML.
 */

function fictioneer_shortcode_showcase( $attr ) {
  // Abort if...
  if ( empty( $attr['for'] ) ) {
    return '';
  }

  // Defaults
  $args = fictioneer_get_default_shortcode_args( $attr, 8 );

  // Height/Width/Quality
  $args['height'] = sanitize_text_field( $attr['height'] ?? '' );
  $args['min_width'] = sanitize_text_field( $attr['min_width'] ?? '' );
  $args['quality'] = sanitize_text_field( $attr['quality'] ?? 'medium' );

  // Specifics
  $args['no_cap'] = filter_var( $attr['no_cap'] ?? 0, FILTER_VALIDATE_BOOLEAN );

  switch ( $attr['for'] ) {
    case 'collections':
      $args['post_type'] = 'fcn_collection';
      break;
    case 'chapters':
      $args['post_type'] = 'fcn_chapter';
      break;
    case 'stories':
      $args['post_type'] = 'fcn_story';
      break;
    case 'recommendations':
      $args['post_type'] = 'fcn_recommendation';
      break;
  }

  // Abort if...
  if ( ! isset( $args['post_type'] ) ) {
    return '';
  }

  // Extra classes
  if ( $args['splide'] ?? 0 ) {
    $args['classes'] .= ' splide _splide-placeholder';
  }

  // Transient?
  $transient_enabled = fictioneer_enable_shortcode_transients( 'fictioneer_showcase' );

  if ( $transient_enabled && $args['cache'] ) {
    $base = serialize( $args ) . serialize( $attr );
    $type = $args['post_type'];
    $transient_key = "fictioneer_shortcode_showcase_{$type}_html_" . md5( $base );
    $transient = get_transient( $transient_key );

    if ( ! empty( $transient ) ) {
      return $transient;
    }
  }

  // Buffer
  ob_start();

  get_template_part( 'partials/_showcase', null, $args );

  $html = fictioneer_minify_html( ob_get_clean() );

  if ( ( $args['splide'] ?? 0 ) && strpos( $args['classes'], 'no-auto-splide' ) === false ) {
    $html = str_replace( '</section>', fictioneer_get_splide_inline_init() . '</section>', $html );
  }

  if ( $transient_enabled && $args['cache'] ) {
    set_transient( $transient_key, $html, FICTIONEER_SHORTCODE_TRANSIENT_EXPIRATION );
  }

  // Return minified buffer
  return $html;
}
add_shortcode( 'fictioneer_showcase', 'fictioneer_shortcode_showcase' );

// =============================================================================
// LATEST CHAPTERS SHORTCODE
// =============================================================================

/**
 * Shortcode to show latest chapters
 *
 * @since 3.0
 *
 * @param string|null $attr['count']               Optional. Maximum number of items. Default 4.
 * @param string|null $attr['author']              Optional. Limit posts to a specific author.
 * @param string|null $attr['type']                Optional. Choose between 'default', 'simple', and 'compact'.
 * @param string|null $attr['post_status']         Optional. Choose a valid post status. Default 'publish'.
 * @param string|null $attr['order']               Optional. Order argument. Default 'DESC'.
 * @param string|null $attr['orderby']             Optional. Orderby argument. Default 'date'.
 * @param string|null $attr['spoiler']             Optional. Whether to show spoiler content.
 * @param string|null $attr['source']              Optional. Whether to show the author and story.
 * @param string|null $attr['post_ids']            Optional. Limit posts to specific post IDs.
 * @param string|null $attr['ignore_protected']    Optional. Whether to ignore protected posts. Default false.
 * @param string|null $attr['only_protected']      Optional. Whether to query only protected posts. Default false.
 * @param string|null $attr['author_ids']          Optional. Only include posts by these author IDs.
 * @param string|null $attr['exclude_author_ids']  Optional. Exclude posts with these author IDs.
 * @param string|null $attr['exclude_tag_ids']     Optional. Exclude posts with these tags.
 * @param string|null $attr['exclude_cat_ids']     Optional. Exclude posts with these categories.
 * @param string|null $attr['categories']          Optional. Limit posts to specific category names.
 * @param string|null $attr['tags']                Optional. Limit posts to specific tag names.
 * @param string|null $attr['fandoms']             Optional. Limit posts to specific fandom names.
 * @param string|null $attr['genres']              Optional. Limit posts to specific genre names.
 * @param string|null $attr['characters']          Optional. Limit posts to specific character names.
 * @param string|null $attr['rel']                 Optional. Relationship between taxonomies. Default 'AND'.
 * @param string|null $attr['vertical']            Optional. Whether to show the vertical variant.
 * @param string|null $attr['seamless']            Optional. Whether to render the image seamless. Default false (Customizer).
 * @param string|null $attr['aspect_ratio']        Optional. Aspect ratio for the image. Only with vertical.
 * @param string|null $attr['lightbox']            Optional. Whether the thumbnail is opened in the lightbox. Default true.
 * @param string|null $attr['thumbnail']           Optional. Whether to show the thumbnail. Default true (Customizer).
 * @param string|null $attr['date_format']         Optional. String to override the date format. Default empty.
 * @param string|null $attr['footer']              Optional. Whether to show the footer (if any). Default true.
 * @param string|null $attr['footer_author']       Optional. Whether to show the chapter author. Default true.
 * @param string|null $attr['footer_date']         Optional. Whether to show the chapter date. Default true.
 * @param string|null $attr['footer_words']        Optional. Whether to show the chapter word count. Default true.
 * @param string|null $attr['footer_comments']     Optional. Whether to show the chapter comment count. Default true.
 * @param string|null $attr['footer_status']       Optional. Whether to show the chapter status. Default true.
 * @param string|null $attr['footer_rating']       Optional. Whether to show the story/chapter age rating. Default true.
 * @param string|null $attr['class']               Optional. Additional CSS classes, separated by whitespace.
 * @param string|null $args['splide']              Configuration JSON for the Splide slider. Default empty.
 *
 * @return string The captured shortcode HTML.
 */

function fictioneer_shortcode_latest_chapters( $attr ) {
  // Defaults
  $args = fictioneer_get_default_shortcode_args( $attr, 4 );

  // Specifics
  $args['simple'] = false;
  $args['spoiler'] = filter_var( $attr['spoiler'] ?? 0, FILTER_VALIDATE_BOOLEAN );

  // Type
  $type = sanitize_text_field( $attr['type'] ?? 'default' );

  // Extra classes
  if ( $args['splide'] ?? 0 ) {
    $args['classes'] .= ' splide _splide-placeholder';
  }

  // Transient?
  $transient_enabled = fictioneer_enable_shortcode_transients( 'fictioneer_latest_chapters' );

  if ( $transient_enabled && $args['cache'] ) {
    $base = serialize( $args ) . serialize( $attr );
    $transient_key = "fictioneer_shortcode_latest_chapters_{$type}_html_" . md5( $base );
    $transient = get_transient( $transient_key );

    if ( ! empty( $transient ) ) {
      return $transient;
    }
  }

  // Buffer
  ob_start();

  switch ( $type ) {
    case 'compact':
      get_template_part( 'partials/_latest-chapters-compact', null, $args );
      break;
    case 'list':
      get_template_part( 'partials/_latest-chapters-list', null, $args );
      break;
    default:
      $args['simple'] = $type == 'simple';
      get_template_part( 'partials/_latest-chapters', null, $args );
  }

  $html = fictioneer_minify_html( ob_get_clean() );

  if ( ( $args['splide'] ?? 0 ) && strpos( $args['classes'], 'no-auto-splide' ) === false ) {
    $html = str_replace( '</section>', fictioneer_get_splide_inline_init() . '</section>', $html );
  }

  if ( $transient_enabled && $args['cache'] ) {
    set_transient( $transient_key, $html, FICTIONEER_SHORTCODE_TRANSIENT_EXPIRATION );
  }

  // Return minified buffer
  return $html;
}
add_shortcode( 'fictioneer_latest_chapters', 'fictioneer_shortcode_latest_chapters' );

// =============================================================================
// LATEST STORIES SHORTCODE
// =============================================================================

/**
 * Shortcode to show latest stories
 *
 * @since 3.0
 *
 * @param string|null $attr['count']               Optional. Maximum number of items. Default 4.
 * @param string|null $attr['author']              Optional. Limit posts to a specific author.
 * @param string|null $attr['type']                Optional. Choose between 'default' and 'compact'.
 * @param string|null $attr['post_status']         Optional. Choose a valid post status. Default 'publish'.
 * @param string|null $attr['order']               Optional. Order argument. Default 'DESC'.
 * @param string|null $attr['orderby']             Optional. Orderby argument. Default 'date'.
 * @param string|null $attr['post_ids']            Optional. Limit posts to specific post IDs.
 * @param string|null $attr['ignore_protected']    Optional. Whether to ignore protected posts. Default false.
 * @param string|null $attr['only_protected']      Optional. Whether to query only protected posts. Default false.
 * @param string|null $attr['author_ids']          Optional. Only include posts by these author IDs.
 * @param string|null $attr['exclude_author_ids']  Optional. Exclude posts with these author IDs.
 * @param string|null $attr['exclude_tag_ids']     Optional. Exclude posts with these tags.
 * @param string|null $attr['exclude_cat_ids']     Optional. Exclude posts with these categories.
 * @param string|null $attr['categories']          Optional. Limit posts to specific category names.
 * @param string|null $attr['tags']                Optional. Limit posts to specific tag names.
 * @param string|null $attr['fandoms']             Optional. Limit posts to specific fandom names.
 * @param string|null $attr['genres']              Optional. Limit posts to specific genre names.
 * @param string|null $attr['characters']          Optional. Limit posts to specific character names.
 * @param string|null $attr['rel']                 Optional. Relationship between taxonomies. Default 'AND'.
 * @param string|null $attr['vertical']            Optional. Whether to show the vertical variant.
 * @param string|null $attr['seamless']            Optional. Whether to render the image seamless. Default false (Customizer).
 * @param string|null $attr['aspect_ratio']        Optional. Aspect ratio for the image. Only with vertical.
 * @param string|null $attr['lightbox']            Optional. Whether the thumbnail is opened in the lightbox. Default true.
 * @param string|null $attr['thumbnail']           Optional. Whether to show the thumbnail. Default true (Customizer).
 * @param string|null $attr['date_format']         Optional. String to override the date format. Default empty.
 * @param string|null $attr['terms']               Optional. Either 'inline', 'pills', 'none', or 'false' (only in list type).
 *                                                 Default 'inline'.
 * @param string|null $attr['max_terms']           Optional. Maximum number of shown taxonomies. Default 10.
 * @param string|null $attr['footer']              Optional. Whether to show the footer (if any). Default true.
 * @param string|null $attr['footer_author']       Optional. Whether to show the story author. Default true.
 * @param string|null $attr['footer_date']         Optional. Whether to show the story date. Default true.
 * @param string|null $attr['footer_words']        Optional. Whether to show the story word count. Default true.
 * @param string|null $attr['footer_chapters']     Optional. Whether to show the story chapter count. Default true.
 * @param string|null $attr['footer_status']       Optional. Whether to show the story status. Default true.
 * @param string|null $attr['footer_rating']       Optional. Whether to show the story age rating. Default true.
 * @param string|null $attr['footer_comments']     Optional. Whether to show the post comment count. Default false.
 * @param string|null $attr['class']               Optional. Additional CSS classes, separated by whitespace.
 * @param string|null $args['splide']              Configuration JSON for the Splide slider. Default empty.
 *
 * @return string The captured shortcode HTML.
 */

function fictioneer_shortcode_latest_stories( $attr ) {
  // Defaults
  $args = fictioneer_get_default_shortcode_args( $attr, 4 );

  // Type
  $type = sanitize_text_field( $attr['type'] ?? 'default' );

  // Comments
  $args['footer_comments'] = filter_var( $attr['footer_comments'] ?? 0, FILTER_VALIDATE_BOOLEAN );

  // Terms
  $args['terms'] = fictioneer_sanitize_query_var( $attr['terms'] ?? 0, ['inline', 'pills', 'none', 'false'], 'inline' );
  $args['max_terms'] = absint( ( $attr['max_terms'] ?? 10 ) ?: 10 );

  // Extra classes
  if ( $args['splide'] ?? 0 ) {
    $args['classes'] .= ' splide _splide-placeholder';
  }

  // Transient?
  $transient_enabled = fictioneer_enable_shortcode_transients( 'fictioneer_latest_stories' );

  if ( $transient_enabled && $args['cache'] ) {
    $base = serialize( $args ) . serialize( $attr );
    $transient_key = "fictioneer_shortcode_latest_stories_{$type}_html_" . md5( $base );
    $transient = get_transient( $transient_key );

    if ( ! empty( $transient ) ) {
      return $transient;
    }
  }

  // Buffer
  ob_start();

  switch ( $type ) {
    case 'compact':
      get_template_part( 'partials/_latest-stories-compact', null, $args );
      break;
    case 'list':
      get_template_part( 'partials/_latest-stories-list', null, $args );
      break;
    default:
      get_template_part( 'partials/_latest-stories', null, $args );
  }

  $html = fictioneer_minify_html( ob_get_clean() );

  if ( ( $args['splide'] ?? 0 ) && strpos( $args['classes'], 'no-auto-splide' ) === false ) {
    $html = str_replace( '</section>', fictioneer_get_splide_inline_init() . '</section>', $html );
  }

  if ( $transient_enabled && $args['cache'] ) {
    set_transient( $transient_key, $html, FICTIONEER_SHORTCODE_TRANSIENT_EXPIRATION );
  }

  // Return minified buffer
  return $html;
}
add_shortcode( 'fictioneer_latest_stories', 'fictioneer_shortcode_latest_stories' );

// =============================================================================
// LATEST UPDATES SHORTCODE
// =============================================================================

/**
 * Shortcode to show latest story updates
 *
 * @since 4.3.0
 *
 * @param string|null $attr['count']               Optional. Maximum number of items. Default 4.
 * @param string|null $attr['author']              Optional. Limit posts to a specific author.
 * @param string|null $attr['type']                Optional. Choose between 'default', 'simple', 'single', and 'compact'.
 * @param string|null $attr['post_status']         Optional. Choose a valid post status. Default 'publish'.
 * @param string|null $attr['single']              Optional. Whether to show only one chapter item. Default false.
 * @param string|null $attr['post_ids']            Optional. Limit posts to specific post IDs.
 * @param string|null $attr['ignore_protected']    Optional. Whether to ignore protected posts. Default false.
 * @param string|null $attr['only_protected']      Optional. Whether to query only protected posts. Default false.
 * @param string|null $attr['exclude_tag_ids']     Optional. Exclude posts with these tags.
 * @param string|null $attr['author_ids']          Optional. Only include posts by these author IDs.
 * @param string|null $attr['exclude_author_ids']  Optional. Exclude posts with these author IDs.
 * @param string|null $attr['exclude_cat_ids']     Optional. Exclude posts with these categories.
 * @param string|null $attr['categories']          Optional. Limit posts to specific category names.
 * @param string|null $attr['tags']                Optional. Limit posts to specific tag names.
 * @param string|null $attr['fandoms']             Optional. Limit posts to specific fandom names.
 * @param string|null $attr['genres']              Optional. Limit posts to specific genre names.
 * @param string|null $attr['characters']          Optional. Limit posts to specific character names.
 * @param string|null $attr['rel']                 Optional. Relationship between taxonomies. Default 'AND'.
 * @param string|null $attr['vertical']            Optional. Whether to show the vertical variant.
 * @param string|null $attr['seamless']            Optional. Whether to render the image seamless. Default false (Customizer).
 * @param string|null $attr['aspect_ratio']        Optional. Aspect ratio for the image. Only with vertical.
 * @param string|null $attr['lightbox']            Optional. Whether the thumbnail is opened in the lightbox. Default true.
 * @param string|null $attr['thumbnail']           Optional. Whether to show the thumbnail. Default true (Customizer).
 * @param string|null $attr['words']               Optional. Whether to show the word count of chapter items. Default true.
 * @param string|null $attr['date']                Optional. Whether to show the date of chapter items. Default true.
 * @param string|null $attr['date_format']         Optional. String to override the date format. Default empty.
 * @param string|null $attr['nested_date_format']  Optional. String to override any nested date formats. Default empty.
 * @param string|null $attr['terms']               Optional. Either 'inline', 'pills', 'none', or 'false' (only in list type).
 *                                                 Default 'inline'.
 * @param string|null $attr['max_terms']           Optional. Maximum number of shown taxonomies. Default 10.
 * @param string|null $attr['footer']              Optional. Whether to show the footer (if any). Default true.
 * @param string|null $attr['footer_author']       Optional. Whether to show the story author. Default true.
 * @param string|null $attr['footer_date']         Optional. Whether to show the story date. Default true.
 * @param string|null $attr['footer_words']        Optional. Whether to show the story word count. Default true.
 * @param string|null $attr['footer_chapters']     Optional. Whether to show the story chapter count. Default true.
 * @param string|null $attr['footer_status']       Optional. Whether to show the story status. Default true.
 * @param string|null $attr['footer_rating']       Optional. Whether to show the story/chapter age rating. Default true.
 * @param string|null $attr['footer_comments']     Optional. Whether to show the post comment count. Default false.
 * @param string|null $attr['class']               Optional. Additional CSS classes, separated by whitespace.
 * @param string|null $args['splide']              Configuration JSON for the Splide slider. Default empty.
 *
 * @return string The captured shortcode HTML.
 */

function fictioneer_shortcode_latest_story_updates( $attr ) {
  // Defaults
  $args = fictioneer_get_default_shortcode_args( $attr, 4 );
  $args['single'] = filter_var( $attr['single'] ?? 0, FILTER_VALIDATE_BOOLEAN );

  // Comments
  $args['footer_comments'] = filter_var( $attr['footer_comments'] ?? 0, FILTER_VALIDATE_BOOLEAN );

  // Type
  $type = sanitize_text_field( $attr['type'] ?? 'default' );

  // Terms
  $args['terms'] = fictioneer_sanitize_query_var( $attr['terms'] ?? 0, ['inline', 'pills', 'none', 'false'], 'inline' );
  $args['max_terms'] = absint( ( $attr['max_terms'] ?? 10 ) ?: 10 );

  // Extra classes
  if ( $args['splide'] ?? 0 ) {
    $args['classes'] .= ' splide _splide-placeholder ';
  }

  // Transient?
  $transient_enabled = fictioneer_enable_shortcode_transients( 'fictioneer_latest_updates' );

  if ( $transient_enabled && $args['cache'] ) {
    $base = serialize( $args ) . serialize( $attr );
    $transient_key = "fictioneer_shortcode_latest_updates_{$type}_html_" . md5( $base );
    $transient = get_transient( $transient_key );

    if ( ! empty( $transient ) ) {
      return $transient;
    }
  }

  // Buffer
  ob_start();

  switch ( $type ) {
    case 'compact':
      get_template_part( 'partials/_latest-updates-compact', null, $args );
      break;
    case 'list':
      get_template_part( 'partials/_latest-updates-list', null, $args );
      break;
    default:
      get_template_part( 'partials/_latest-updates', null, $args );
  }

  $html = fictioneer_minify_html( ob_get_clean() );

  if ( ( $args['splide'] ?? 0 ) && strpos( $args['classes'], 'no-auto-splide' ) === false ) {
    $html = str_replace( '</section>', fictioneer_get_splide_inline_init() . '</section>', $html );
  }

  if ( $transient_enabled && $args['cache'] ) {
    set_transient( $transient_key, $html, FICTIONEER_SHORTCODE_TRANSIENT_EXPIRATION );
  }

  // Return minified buffer
  return $html;
}
add_shortcode( 'fictioneer_latest_updates', 'fictioneer_shortcode_latest_story_updates' );

// =============================================================================
// LATEST RECOMMENDATIONS SHORTCODE
// =============================================================================

/**
 * Shortcode to show latest recommendations
 *
 * @since 4.0.0
 *
 * @param string|null $attr['count']               Optional. Maximum number of items. Default 4.
 * @param string|null $attr['author']              Optional. Limit posts to a specific author.
 * @param string|null $attr['type']                Optional. Choose between 'default' and 'compact'.
 * @param string|null $attr['post_status']         Optional. Choose a valid post status. Default 'publish'.
 * @param string|null $attr['order']               Optional. Order argument. Default 'DESC'.
 * @param string|null $attr['orderby']             Optional. Orderby argument. Default 'date'.
 * @param string|null $attr['post_ids']            Optional. Limit posts to specific post IDs.
 * @param string|null $attr['ignore_protected']    Optional. Whether to ignore protected posts. Default false.
 * @param string|null $attr['only_protected']      Optional. Whether to query only protected posts. Default false.
 * @param string|null $attr['author_ids']          Optional. Only include posts by these author IDs.
 * @param string|null $attr['exclude_author_ids']  Optional. Exclude posts with these author IDs.
 * @param string|null $attr['exclude_tag_ids']     Optional. Exclude posts with these tags.
 * @param string|null $attr['exclude_cat_ids']     Optional. Exclude posts with these categories.
 * @param string|null $attr['categories']          Optional. Limit posts to specific category names.
 * @param string|null $attr['tags']                Optional. Limit posts to specific tag names.
 * @param string|null $attr['fandoms']             Optional. Limit posts to specific fandom names.
 * @param string|null $attr['genres']              Optional. Limit posts to specific genre names.
 * @param string|null $attr['characters']          Optional. Limit posts to specific character names.
 * @param string|null $attr['rel']                 Optional. Relationship between taxonomies. Default 'AND'.
 * @param string|null $attr['vertical']            Optional. Whether to show the vertical variant.
 * @param string|null $attr['seamless']            Optional. Whether to render the image seamless. Default false (Customizer).
 * @param string|null $attr['aspect_ratio']        Optional. Aspect ratio for the image. Only with vertical.
 * @param string|null $attr['lightbox']            Optional. Whether the thumbnail is opened in the lightbox. Default true.
 * @param string|null $attr['thumbnail']           Optional. Whether to show the thumbnail. Default true (Customizer).
 * @param string|null $attr['class']               Optional. Additional CSS classes, separated by whitespace.
 * @param string|null $args['splide']              Configuration JSON for the Splide slider. Default empty.
 *
 * @return string The captured shortcode HTML.
 */

function fictioneer_shortcode_latest_recommendations( $attr ) {
  // Defaults
  $args = fictioneer_get_default_shortcode_args( $attr, 4 );

  // Type
  $type = sanitize_text_field( $attr['type'] ?? 'default' );

  // Terms
  $args['terms'] = fictioneer_sanitize_query_var( $attr['terms'] ?? 0, ['inline', 'pills', 'none', 'false'], 'inline' );
  $args['max_terms'] = absint( ( $attr['max_terms'] ?? 10 ) ?: 10 );

  // Extra classes
  if ( $args['splide'] ?? 0 ) {
    $args['classes'] .= ' splide _splide-placeholder';
  }

  // Transient?
  $transient_enabled = fictioneer_enable_shortcode_transients( 'fictioneer_latest_recommendations' );

  if ( $transient_enabled && $args['cache'] ) {
    $base = serialize( $args ) . serialize( $attr );
    $transient_key = "fictioneer_shortcode_latest_recommendations_{$type}_html_" . md5( $base );
    $transient = get_transient( $transient_key );

    if ( ! empty( $transient ) ) {
      return $transient;
    }
  }

  // Buffer
  ob_start();

  switch ( $type ) {
    case 'compact':
      get_template_part( 'partials/_latest-recommendations-compact', null, $args );
      break;
    default:
      get_template_part( 'partials/_latest-recommendations', null, $args );
  }

  $html = fictioneer_minify_html( ob_get_clean() );

  if ( ( $args['splide'] ?? 0 ) && strpos( $args['classes'], 'no-auto-splide' ) === false ) {
    $html = str_replace( '</section>', fictioneer_get_splide_inline_init() . '</section>', $html );
  }

  if ( $transient_enabled && $args['cache'] ) {
    set_transient( $transient_key, $html, FICTIONEER_SHORTCODE_TRANSIENT_EXPIRATION );
  }

  // Return minified buffer
  return $html;
}
add_shortcode( 'fictioneer_latest_recommendations', 'fictioneer_shortcode_latest_recommendations' );

// =============================================================================
// LATEST POST SHORTCODE
// =============================================================================

/**
 * Shortcode to show the latest post
 *
 * @since 4.0.0
 *
 * @param string|null $attr['count']               Optional. Maximum number of items. Default 1.
 * @param string|null $attr['author']              Optional. Limit posts to a specific author.
 * @param string|null $attr['post_status']         Optional. Choose a valid post status. Default 'publish'.
 * @param string|null $attr['post_ids']            Optional. Limit posts to specific post IDs.
 * @param string|null $attr['ignore_protected']    Optional. Whether to ignore protected posts. Default false.
 * @param string|null $attr['only_protected']      Optional. Whether to query only protected posts. Default false.
 * @param string|null $attr['author_ids']          Optional. Only include posts by these author IDs.
 * @param string|null $attr['exclude_author_ids']  Optional. Exclude posts with these author IDs.
 * @param string|null $attr['exclude_tag_ids']     Optional. Exclude posts with these tags.
 * @param string|null $attr['exclude_cat_ids']     Optional. Exclude posts with these categories.
 * @param string|null $attr['categories']          Optional. Limit posts to specific category names.
 * @param string|null $attr['tags']                Optional. Limit posts to specific tag names.
 * @param string|null $attr['rel']                 Optional. Relationship between taxonomies. Default 'AND'.
 * @param string|null $attr['class']               Optional. Additional CSS classes, separated by whitespace.
 *
 * @return string The captured shortcode HTML.
 */

function fictioneer_shortcode_latest_posts( $attr ) {
  // Defaults
  $args = fictioneer_get_default_shortcode_args( $attr, 1 );

  // Transient?
  $transient_enabled = fictioneer_enable_shortcode_transients( 'fictioneer_latest_posts' );

  if ( $transient_enabled && $args['cache'] ) {
    $base = serialize( $args ) . serialize( $attr );
    $transient_key = "fictioneer_shortcode_latest_posts_html_" . md5( $base );
    $transient = get_transient( $transient_key );

    if ( ! empty( $transient ) ) {
      return $transient;
    }
  }

  // Buffer
  ob_start();

  get_template_part( 'partials/_latest-posts', null, $args );

  $html = fictioneer_minify_html( ob_get_clean() );

  if ( $transient_enabled && $args['cache'] ) {
    set_transient( $transient_key, $html, FICTIONEER_SHORTCODE_TRANSIENT_EXPIRATION );
  }

  // Return minified buffer
  return $html;
}
add_shortcode( 'fictioneer_latest_posts', 'fictioneer_shortcode_latest_posts' );

// =============================================================================
// BOOKMARKS SHORTCODE
// =============================================================================

/**
 * Shortcode to show bookmarks
 *
 * @since 4.0.0
 *
 * @param string|null $attr['count']       Optional. Maximum number of items. Default -1 (all).
 * @param string|null $attr['show_empty']  Optional. Whether to show the "no bookmarks" message. Default false.
 * @param string|null $attr['seamless']    Optional. Whether to render the image seamless. Default false (Customizer).
 * @param string|null $attr['thumbnail']   Optional. Whether to show the thumbnail. Default true (Customizer).
 *
 * @return string The captured shortcode HTML.
 */

function fictioneer_shortcode_bookmarks( $attr ) {
  // Sanitize attributes
  $attr = is_array( $attr ) ? array_map( 'sanitize_text_field', $attr ) : sanitize_text_field( $attr );

  // Setup
  $seamless_default = get_theme_mod( 'card_image_style', 'default' ) === 'seamless';
  $thumbnail_default = get_theme_mod( 'card_image_style', 'default' ) !== 'none';

  // Buffer
  ob_start();

  get_template_part( 'partials/_bookmarks', null, array(
    'count' => max( -1, intval( $attr['count'] ?? -1 ) ),
    'show_empty' => $attr['show_empty'] ?? false,
    'seamless' => filter_var( $attr['seamless'] ?? $seamless_default, FILTER_VALIDATE_BOOLEAN ),
    'thumbnail' => filter_var( $attr['thumbnail'] ?? $thumbnail_default, FILTER_VALIDATE_BOOLEAN )
  ));

  // Return minified buffer
  return fictioneer_minify_html( ob_get_clean() );
}
add_shortcode( 'fictioneer_bookmarks', 'fictioneer_shortcode_bookmarks' );

// =============================================================================
// COOKIES SHORTCODE
// =============================================================================

/**
 * Shortcode to show cookie consent actions
 *
 * Renders buttons to handle your consent and stored cookies.
 *
 * @since 4.7.0
 *
 * @return string The captured shortcode HTML.
 */

function fictioneer_shortcode_cookie_buttons( $attr ) {
  ob_start();

  // Start HTML ---> ?>
  <div class="cookies spacing-top spacing-bottom ">
    <button type="button" data-action="click->fictioneer#clearConsent" class="button"><?php _e( 'Reset Consent', 'fictioneer' ); ?></button>
    <button type="button" data-action="click->fictioneer#clearCookies" data-message="<?php _e( 'Cookies and local storage have been cleared. To keep it that way, you should leave the site.', 'fictioneer' ); ?>" class="button"><?php _e( 'Clear Cookies', 'fictioneer' ); ?></button>
  </div>
  <?php // <--- End HTML

  return fictioneer_minify_html( ob_get_clean() );
}
add_shortcode( 'fictioneer_cookie_buttons', 'fictioneer_shortcode_cookie_buttons' );

// =============================================================================
// CHAPTER LIST SHORTCODE
// =============================================================================

/**
 * Returns empty chapter list
 *
 * @since 5.9.4
 *
 * @param string|null $attr['heading']  Optional. Show <h5> heading above list.
 *
 * @return string The captured HTML.
 */

function fictioneer_shortcode_chapter_list_empty( $attr ) {
  ob_start();

  // Start HTML ---> ?>
  <div class="chapter-group chapter-list _standalone _empty">
    <?php if ( ! empty( $attr['heading'] ) ) : ?>
      <button class="chapter-group__name" data-action="click->fictioneer#toggleChapterGroup" aria-label="<?php echo esc_attr( sprintf( __( 'Toggle chapter group: %s', 'fictioneer' ), $attr['heading'] ) ); ?>" tabindex="0">
        <i class="fa-solid fa-chevron-down chapter-group__heading-icon"></i>
        <span><?php echo $attr['heading']; ?></span>
      </button>
    <?php endif; ?>
    <ol class="chapter-group__list">
      <li class="chapter-group__list-item _empty"><?php _e( 'No chapters published yet.', 'fictioneer' ); ?></li>
    </ol>
  </div>
  <?php // <--- End HTML

  return fictioneer_minify_html( ob_get_clean() );
}

/**
 * Shortcode to show chapter list outside of story pages
 *
 * @since 5.0.0
 *
 * @param string      $attr['story_id']     Either/Or. The ID of the story the chapters belong to.
 * @param string|null $attr['post_status']  Optional. Choose a valid post status. Default 'publish'.
 * @param string|null $attr['chapter_ids']  Either/Or. Comma-separated list of chapter IDs.
 * @param string|null $attr['count']        Optional. Maximum number of items. Default -1 (all).
 * @param string|null $attr['offset']       Optional. Skip a number of posts.
 * @param string|null $attr['group']        Optional. Only show chapters of the group.
 * @param string|null $attr['heading']      Optional. Show <h5> heading above list.
 * @param string|null $attr['class']        Optional. Additional CSS classes, separated by whitespace.
 *
 * @return string The captured shortcode HTML.
 */

function fictioneer_shortcode_chapter_list( $attr ) {
  // Sanitize attributes
  $attr = is_array( $attr ) ? array_map( 'sanitize_text_field', $attr ) : sanitize_text_field( $attr );

  // Return empty case if...
  if ( empty( $attr['story_id'] ) && empty( $attr['chapter_ids'] ) ) {
    return fictioneer_shortcode_chapter_list_empty( $attr );
  }

  // Setup
  $cache = filter_var( $attr['cache'] ?? 1, FILTER_VALIDATE_BOOLEAN );
  $count = max( -1, intval( $attr['count'] ?? -1 ) );
  $offset = max( 0, intval( $attr['offset'] ?? 0 ) );
  $group = empty( $attr['group'] ) ? false : strtolower( trim( $attr['group'] ) );
  $heading = empty( $attr['heading'] ) ? false : $attr['heading'];
  $post_status = sanitize_key( $attr['post_status'] ?? 'publish' );
  $story_id = fictioneer_validate_id( $attr['story_id'] ?? -1, 'fcn_story' );
  $prefer_chapter_icon = get_option( 'fictioneer_override_chapter_status_icons' );
  $hide_icons = get_option( 'fictioneer_hide_chapter_icons' );
  $can_checkmarks = get_option( 'fictioneer_enable_checkmarks' ) && ( is_user_logged_in() || get_option( 'fictioneer_enable_ajax_authentication' ) );
  $classes = wp_strip_all_tags( $attr['class'] ?? '' );
  $chapter_ids = [];
  $chapters = [];

  // Extract chapter IDs (if any)
  if ( ! empty( $attr['chapter_ids'] ) ) {
    $chapter_ids = fictioneer_explode_list( $attr['chapter_ids'] );
  }

  // Get chapters...
  if ( $story_id && empty( $chapter_ids ) ) {
    // ... via story
    $hide_icons = $hide_icons || get_post_meta( $story_id, 'fictioneer_story_hide_chapter_icons', true );
    $story_data = fictioneer_get_story_data( $story_id, false ); // Does not refresh comment count!
    $chapters = $story_data['chapter_ids'];
  } elseif ( ! empty( $chapter_ids ) ) {
    // ... via chapter IDs
    $chapters = $chapter_ids;
  }

  // Extra classes
  if ( $hide_icons ) {
    $classes .= ' _no-icons';
  }

  if ( get_option( 'fictioneer_collapse_groups_by_default' ) && ! str_contains( $classes, 'no-auto-collapse' ) ) {
    $classes .= ' _closed';
  }

  // Apply offset and count
  if ( ! $group ) {
    $chapters = array_slice( $chapters, $offset );
    $chapters = $count > 0 ? array_slice( $chapters, 0, $count ) : $chapters;
  }

  // Return empty case if...
  if ( empty( $chapters ) ) {
    return fictioneer_shortcode_chapter_list_empty( $attr );
  }

  // Query chapters
  $query_args = array(
    'fictioneer_query_name' => 'fictioneer_shortcode_chapter_list',
    'post_type' => 'fcn_chapter',
    'post_status' => $post_status,
    'post__in' => $chapters, // Cannot be empty!
    'ignore_sticky_posts' => true,
    'orderby' => 'post__in', // Preserve order from meta box
    'posts_per_page' => -1, // Get all chapters (this can be hundreds)
    'no_found_rows' => true, // Improve performance
    'update_post_term_cache' => false // Improve performance
  );

  // Transient?
  $transient_enabled = fictioneer_enable_shortcode_transients( 'fictioneer_chapter_list' );

  if ( $transient_enabled && $cache ) {
    $base = serialize( $query_args ) . serialize( $attr ) . $classes;
    $base .= ( $hide_icons ? '1' : '0' ) . ( $can_checkmarks ? '1' : '0' );
    $transient_key = "fictioneer_shortcode_chapter_list_html_" . md5( $base );
    $transient = get_transient( $transient_key );

    if ( ! empty( $transient ) ) {
      return $transient;
    }
  }

  // Query
  $chapter_query = fictioneer_shortcode_query( $query_args );

  // Return empty case if...
  if ( ! $chapter_query->have_posts() ) {
    return fictioneer_shortcode_chapter_list_empty( $attr );
  }

  // Buffer
  ob_start();

  // Start HTML ---> ?>
  <div class="chapter-group chapter-list _standalone <?php echo esc_attr( $classes ); ?>">
    <?php if ( $heading ) : ?>
      <button class="chapter-group__name" data-action="click->fictioneer#toggleChapterGroup" aria-label="<?php echo esc_attr( sprintf( __( 'Toggle chapter group: %s', 'fictioneer' ), $heading ) ); ?>" tabindex="0">
        <i class="fa-solid fa-chevron-down chapter-group__heading-icon"></i>
        <span><?php echo $heading; ?></span>
      </button>
    <?php endif; ?>
    <ol class="chapter-group__list">
      <?php
        $render_count = 0;

        global $post;

        while( $chapter_query->have_posts() ) {
          // Setup
          $chapter_query->the_post();
          $chapter_id = get_the_ID();
          $chapter_story_id = fictioneer_get_chapter_story_id( $chapter_id );

          // Skip not visible chapters
          if ( get_post_meta( $chapter_id, 'fictioneer_chapter_hidden', true ) ) {
            continue;
          }

          // Check group (if any)
          if ( $group && $group != strtolower( trim( get_post_meta( $chapter_id, 'fictioneer_chapter_group', true ) ) ) ) {
            continue;
          }

          // Count renderings
          $render_count++;

          // Apply offset if limited to group (not working in query for 'posts_per_page' => -1)
          if ( $group && $offset > 0 && $render_count <= $offset ) {
            continue;
          }

          // Apply count if limited to group
          if ( $group && $count > 0 && $render_count > $count ) {
            break;
          }

          // Data
          $warning = get_post_meta( $chapter_id, 'fictioneer_chapter_warning', true );
          $icon = fictioneer_get_icon_field( 'fictioneer_chapter_icon', $chapter_id );
          $text_icon = get_post_meta( $chapter_id, 'fictioneer_chapter_text_icon', true );
          $prefix = get_post_meta( $chapter_id, 'fictioneer_chapter_prefix', true );
          $title = fictioneer_get_safe_title( $chapter_id, 'shortcode-chapter-list' );
          $has_password = ! empty( $post->post_password );
          $extra_classes = '_shortcode';

          if ( $warning ) {
            $extra_classes .= ' _warning';
          }

          if ( $has_password ) {
            $extra_classes .= ' _password';
          }

          // Start HTML ---> ?>
          <li class="chapter-group__list-item <?php echo $extra_classes; ?>" data-post-id="<?php echo $chapter_id; ?>">
            <?php
              if ( ! $hide_icons ) {
                // Icon hierarchy: password > scheduled > text > normal
                if ( ! $prefer_chapter_icon && $has_password ) {
                  $icon = '<i class="fa-solid fa-lock chapter-group__list-item-icon"></i>';
                } elseif ( ! $prefer_chapter_icon && get_post_status( $chapter_id ) === 'future' ) {
                  $icon = '<i class="fa-solid fa-calendar-days chapter-group__list-item-icon"></i>';
                } elseif ( $text_icon ) {
                  $icon = "<span class='chapter-group__list-item-icon _text text-icon'>{$text_icon}</span>";
                } else {
                  $icon = $icon ?: FICTIONEER_DEFAULT_CHAPTER_ICON;
                  $icon = "<i class='{$icon} chapter-group__list-item-icon'></i>";
                }

                echo apply_filters( 'fictioneer_filter_chapter_icon', $icon, $chapter_id, $story_id );
              }
            ?>

            <a
              href="<?php the_permalink( $chapter_id ); ?>"
              class="chapter-group__list-item-link truncate _1-1 <?php echo $has_password ? '_password' : ''; ?>"
            ><?php

              $title_output = '';
              $prefix = apply_filters( 'fictioneer_filter_list_chapter_prefix', $prefix, $chapter_id, 'shortcode' );

              if ( ! empty( $prefix ) ) {
                // Mind space between prefix and title
                $title_output .= $prefix . ' ';
              }

              $title_output .= $title;

              echo apply_filters(
                'fictioneer_filter_list_chapter_title_row',
                $title_output, $chapter_id, $prefix, $has_password, 'shortcode'
              );

            ?></a>

            <?php
              // Chapter subrow
              echo fictioneer_get_list_chapter_meta_row(
                array(
                  'id' => $chapter_id,
                  'warning' => $warning,
                  'password' => $has_password,
                  'timestamp' => get_the_time( 'c' ),
                  'list_date' => get_the_date( '' ),
                  'words' => fictioneer_get_word_count( $chapter_id ),
                )
              );
            ?>

            <?php if ( $can_checkmarks && ! empty( $chapter_story_id ) && get_post_status( $chapter_story_id ) === 'publish' ) : ?>
              <button
                class="checkmark chapter-group__list-item-checkmark only-logged-in"
                data-fictioneer-checkmarks-target="chapterCheck"
                data-fictioneer-checkmarks-story-param="<?php echo $chapter_story_id; ?>"
                data-fictioneer-checkmarks-chapter-param="<?php echo $chapter_id; ?>"
                data-action="click->fictioneer-checkmarks#toggleChapter"
                role="checkbox"
                aria-checked="false"
                aria-label="<?php
                  printf(
                    esc_attr__( 'Chapter checkmark for %s.', 'fictioneer' ),
                    esc_attr( wp_strip_all_tags( $title ) )
                  );
                ?>"
              ><i class="fa-solid fa-check"></i></button>
            <?php endif; ?>
          </li>
          <?php // <--- End HTML
        }

        // Restore postdata
        wp_reset_postdata();
      ?>
    </ol>
  </div>
  <?php // <--- End HTML

  // Store buffer
  $html = fictioneer_minify_html( ob_get_clean() );

  if ( $transient_enabled && $cache ) {
    set_transient( $transient_key, $html, FICTIONEER_SHORTCODE_TRANSIENT_EXPIRATION );
  }

  // Return minified buffer
  return $html;
}
add_shortcode( 'fictioneer_chapter_list', 'fictioneer_shortcode_chapter_list' );

// =============================================================================
// CONTACT FORM SHORTCODE
// =============================================================================

/**
 * Shortcode to show a contact form
 *
 * @since 5.0.0
 *
 * @param string|null $attr['title']           Optional. Title of the form.
 * @param string|null $attr['submit']          Optional. Submit button caption.
 * @param string|null $attr['privacy_policy']  Optional. Must accept privacy policy.
 * @param string|null $attr['required']        Optional. Make all fields required
 * @param string|null $attr['email']           Optional. Email field.
 * @param string|null $attr['name']            Optional. Name field.
 * @param string|null $attr["text_{$i}"]       Optional. Up to 6 extra text field(s).
 * @param string|null $attr["check_{$i}"]      Optional. Up to 6 extra checkbox field(s).
 * @param string|null $attr['class']           Optional. Additional CSS classes, separated by whitespace.
 *
 * @return string The captured shortcode HTML.
 */

function fictioneer_shortcode_contact_form( $attr ) {
  // Sanitize attributes
  $attr = is_array( $attr ) ? array_map( 'sanitize_text_field', $attr ) : sanitize_text_field( $attr );

  // Setup
  $title = $attr['title'] ?? _x( 'Nameless Form', 'Contact form.', 'fictioneer' );
  $submit = $attr['submit'] ?? __( 'Submit', 'fictioneer' );
  $privacy_policy = filter_var( $attr['privacy_policy'] ?? 0, FILTER_VALIDATE_BOOLEAN );
  $required = isset( $attr['required'] ) ? 'required' : '';
  $email = $attr['email'] ?? '';
  $name = $attr['name'] ?? '';
  $classes = wp_strip_all_tags( $attr['class'] ?? '' );
  $fields = [];

  // HTML snippets
  if ( ! empty( $email ) ) {
    $fields[] = "<input style='opacity: 0;' type='email' name='email' maxlength='100' placeholder='{$email}' {$required}>";
  }

  if ( ! empty( $name ) ) {
    $fields[] = "<input style='opacity: 0;' type='text' name='name' maxlength='100' placeholder='{$name}' {$required}>";
  }

  // Custom text fields
  for ( $i = 1; $i <= 6; $i++ ) {
    $field = $attr["text_{$i}"] ?? '';

    if ( ! empty( $field ) ) {
      $fields[] = "<input style='opacity: 0;' type='text' name='text_{$i}' maxlength='100' placeholder='{$field}' {$required}><input type='hidden' name='text_label_{$i}' value='{$field}'>";
    }
  }

  // Custom checkboxes
  for ( $i = 1; $i <= 6; $i++ ) {
    $field = $attr["check_{$i}"] ?? '';

    if ( ! empty( $field ) ) {
      $fields[] = "<label class='checkbox-label'><input class='_no-stretch' style='opacity: 0;' type='checkbox' value='1' name='check_{$i}' autocomplete='off' {$required}><span>{$field}</span></label><input type='hidden' name='check_label_{$i}' value='{$field}'>";
    }
  }

  // Privacy policy checkbox
  $privacy_policy_link = get_option( 'wp_page_for_privacy_policy' ) ? esc_url( get_privacy_policy_url() ) : false;

  if ( $privacy_policy && $privacy_policy_link ) {
    $fields[] = "<label class='checkbox-label'><input class='_no-stretch' style='opacity: 0;' type='checkbox' value='1' name='privacy_policy' autocomplete='off' required><span>" . sprintf( fcntr( 'accept_privacy_policy' ), $privacy_policy_link ) . "</span></label><input type='hidden' name='require_privacy_policy' value='1'>";
  }

  // Apply filters
  $fields = apply_filters( 'fictioneer_filter_contact_form_fields', $fields, get_the_ID() );

  // Buffer
  ob_start();

  /*
   * The inline "opacity: 0" style is a reverse bot trap. They are made visible again with CSS,
   * but dumb bots might think this is a trap for them and ignore the fields. Not a reliable
   * spam protection, but this is security in depth.
   *
   * The "phone" and "terms" inputs are honeypots.
   */

  // Start HTML ---> ?>
  <form class="fcn-contact-form <?php echo esc_attr( $classes ); ?>">
    <div class="fcn-contact-form__message">
      <textarea class="fcn-contact-form__textarea adaptive-textarea" style="opacity: 0;" name="message" maxlength="65525" placeholder="<?php _e( 'Please enter your message.', 'fictioneer' ); ?>" required></textarea>
    </div>
    <?php if ( ! empty( $fields ) ) : ?>
      <div class="fcn-contact-form__fields">
        <?php foreach ( $fields as $field ) : ?>
          <div class="fcn-contact-form__field"><?php echo $field; ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <input type="checkbox" name="terms" value="1" autocomplete="off" tabindex="-1">
    <input type="tel" name="phone" autocomplete="off" tabindex="-1">
    <input type="hidden" name="title" value="<?php echo $title; ?>">
    <div class="fcn-contact-form__actions">
      <button class="fcn-contact-form__submit button" type="button" data-enabled="<?php echo esc_attr( $submit ); ?>" data-disabled="<?php esc_attr_e( 'Sending…', 'fictioneer' ); ?>" data-done="<?php esc_attr_e( 'Message sent!', 'fictioneer' ); ?>"><?php echo $submit; ?></button>
    </div>
  </form>
  <?php // <--- End HTML

  // Return minified buffer
  return fictioneer_minify_html( ob_get_clean() );
}
add_shortcode( 'fictioneer_contact_form', 'fictioneer_shortcode_contact_form' );

// =============================================================================
// SEARCH SHORTCODE
// =============================================================================

/**
 * Shortcode to show the latest post
 *
 * @since 5.0.0
 *
 * @param string|null $attr['simple']       Optional. Hide the advanced options.
 * @param string|null $attr['placeholder']  Optional. Placeholder text.
 * @param string|null $attr['type']         Optional. Default post type to query.
 *
 * @return string The captured shortcode HTML.
 */

function fictioneer_shortcode_search( $attr ) {
  // Sanitize attributes
  $attr = is_array( $attr ) ? array_map( 'sanitize_text_field', $attr ) : sanitize_text_field( $attr );

  // Setup
  $args = array( 'cache' => true );
  $simple = isset( $attr['simple'] ) ? $attr['simple'] == 'true' || $attr['simple'] == '1' : false;
  $placeholder = $attr['placeholder'] ?? false;
  $type = $attr['type'] ?? false;
  $pre_tags = fictioneer_explode_list( $attr['tags'] ?? '' );
  $pre_genres = fictioneer_explode_list( $attr['genres'] ?? '' );
  $pre_fandoms = fictioneer_explode_list( $attr['fandoms'] ?? '' );
  $pre_characters = fictioneer_explode_list( $attr['characters'] ?? '' );
  $pre_warnings = fictioneer_explode_list( $attr['warnings'] ?? '' );

  // Prepare arguments
  $args['expanded'] = filter_var( $attr['expanded'] ?? 0, FILTER_VALIDATE_BOOLEAN );

  if ( $simple ) {
    $args['simple'] = $simple;
  }

  if ( $placeholder ) {
    $args['placeholder'] = $placeholder;
  }

  if ( $type && in_array( $type, ['any', 'story', 'chapter', 'recommendation', 'collection', 'post'] ) ) {
    $args['preselect_type'] = in_array( $type, ['story', 'chapter', 'recommendation', 'collection'] ) ? "fcn_{$type}" : $type;
  }

  if ( $pre_tags ) {
    $args['preselect_tags'] = array_map( 'absint', $pre_tags );
  }

  if ( $pre_genres ) {
    $args['preselect_genres'] = array_map( 'absint', $pre_genres );
  }

  if ( $pre_fandoms ) {
    $args['preselect_fandoms'] = array_map( 'absint', $pre_fandoms );
  }

  if ( $pre_characters ) {
    $args['preselect_characters'] = array_map( 'absint', $pre_characters );
  }

  if ( $pre_warnings ) {
    $args['preselect_warnings'] = array_map( 'absint', $pre_warnings );
  }

  // Buffer
  ob_start();

  get_search_form( $args );

  // Return minified buffer
  return fictioneer_minify_html( ob_get_clean() );
}
add_shortcode( 'fictioneer_search', 'fictioneer_shortcode_search' );

// =============================================================================
// BLOG SHORTCODE
// =============================================================================

/**
 * Shortcode to show blog with pagination
 *
 * @since 5.2.0
 *
 * @param string|null $attr['per_page']            Optional. Number of posts per page.
 * @param string|null $attr['ignore_sticky']       Optional. Whether to ignore sticky posts. Default false.
 * @param string|null $attr['ignore_protected']    Optional. Whether to ignore protected posts. Default false.
 * @param string|null $attr['only_protected']      Optional. Whether to query only protected posts. Default false.
 * @param string|null $attr['author']              Optional. Limit posts to a specific author.
 * @param string|null $attr['post_status']         Optional. Choose a valid post status. Default 'publish'.
 * @param string|null $attr['author_ids']          Optional. Only include posts by these author IDs.
 * @param string|null $attr['exclude_author_ids']  Optional. Exclude posts with these author IDs.
 * @param string|null $attr['exclude_tag_ids']     Optional. Exclude posts with these tags.
 * @param string|null $attr['exclude_cat_ids']     Optional. Exclude posts with these categories.
 * @param string|null $attr['categories']          Optional. Limit posts to specific category names.
 * @param string|null $attr['tags']                Optional. Limit posts to specific tag names.
 * @param string|null $attr['rel']                 Optional. Relationship between taxonomies. Default 'AND'.
 * @param string|null $attr['class']               Optional. Additional CSS classes, separated by whitespace.
 *
 * @return string The captured shortcode HTML.
 */

function fictioneer_shortcode_blog( $attr ) {
  // Defaults
  $args = fictioneer_get_default_shortcode_args( $attr );

  // Query arguments
  $query_args = array(
    'fictioneer_query_name' => 'blog_shortcode',
    'post_type' => 'post',
    'post_status' => $args['post_status'],
    'paged' => $args['page'],
    'posts_per_page' => $args['posts_per_page'],
    'ignore_sticky_posts' => $args['ignore_sticky']
  );

  // Author?
  if ( ! empty( $args['author'] ) ) {
    $query_args['author_name'] = $args['author'];
  }

  // Author IDs?
  if ( ! empty( $args['author_ids'] ) ) {
    $query_args['author__in'] = $args['author_ids'];
  }

  // Taxonomies?
  if ( ! empty( $args['taxonomies'] ) ) {
    $query_args['tax_query'] = fictioneer_get_shortcode_tax_query( $args );
  }

  // Excluded tags?
  if ( ! empty( $args['excluded_tags'] ) ) {
    $query_args['tag__not_in'] = $args['excluded_tags'];
  }

  // Excluded categories?
  if ( ! empty( $args['excluded_cats'] ) ) {
    $query_args['category__not_in'] = $args['excluded_cats'];
  }

  // Excluded authors?
  if ( ! empty( $args['excluded_authors'] ) ) {
    $query_args['author__not_in'] = $args['excluded_authors'];
  }

  // Exclude protected
  if ( $args['ignore_protected'] ) {
    $query_args['has_password'] = false;
  }

  // Apply filters
  $query_args = apply_filters( 'fictioneer_filter_shortcode_blog_query_args', $query_args, $args );

  // Transient?
  $transient_enabled = fictioneer_enable_shortcode_transients( 'fictioneer_blog' );

  if ( $transient_enabled && $args['cache'] ) {
    $base = serialize( $query_args ) . serialize( $args ) . serialize( $attr );
    $transient_key = 'fictioneer_shortcode_blog_html_' . md5( $base );
    $transient = get_transient( $transient_key );

    if ( ! empty( $transient ) ) {
      return $transient;
    }
  }

  // Query
  $blog_query = new WP_Query( $query_args );

  // Prime author cache
  if ( function_exists( 'update_post_author_caches' ) ) {
    update_post_author_caches( $blog_query->posts );
  }

  // Pagination
  $pag_args = array(
    'current' => $args['page'],
    'total' => $blog_query->max_num_pages,
    'prev_text' => fcntr( 'previous' ),
    'next_text' => fcntr( 'next' ),
    'add_fragment' => '#blog'
  );

  // Buffer
  ob_start();

  if ( $blog_query->have_posts() ) {
    // Start HTML ---> ?>
    <section id="blog" class="scroll-margin-top blog-posts _nested <?php echo $args['classes']; ?>">
      <?php
        while ( $blog_query->have_posts() ) {
          $blog_query->the_post();
          get_template_part( 'partials/_post', null, array( 'nested' => true, 'context' => 'shortcode_fictioneer_blog' ) );
        }

        wp_reset_postdata();

        if ( $blog_query->max_num_pages > 1 ) {
          echo '<nav class="pagination">' . fictioneer_paginate_links( $pag_args ) . '</nav>';
        }
      ?>
    </section>
    <?php // <--- End HTML
  } else {
    // Start HTML ---> ?>
    <article class="post _empty">
      <span><?php _e( 'No (more) posts found.', 'fictioneer' ); ?></span>
    </article>
    <?php // <--- End HTML
  }

  // Store buffer
  $html = fictioneer_minify_html( ob_get_clean() );

  if ( $transient_enabled && $args['cache'] ) {
    set_transient( $transient_key, $html, FICTIONEER_SHORTCODE_TRANSIENT_EXPIRATION );
  }

  // Return minified buffer
  return $html;
}
add_shortcode( 'fictioneer_blog', 'fictioneer_shortcode_blog' );

// =============================================================================
// ARTICLE CARDS SHORTCODE
// =============================================================================

/**
 * Shortcode to show article cards with pagination
 *
 * @since 5.7.3
 *
 * @param string|null $attr['post_type']           Optional. The post types to query. Default 'post'.
 * @param string|null $attr['post_status']         Optional. Choose a valid post status. Default 'publish'.
 * @param string|null $attr['post_ids']            Optional. Limit posts to specific post IDs.
 * @param string|null $attr['per_page']            Optional. Number of posts per page.
 * @param string|null $attr['count']               Optional. Maximum number of posts. Default -1.
 * @param string|null $attr['order']               Optional. Order argument. Default 'DESC'.
 * @param string|null $attr['orderby']             Optional. Orderby argument. Default 'date'.
 * @param string|null $attr['ignore_sticky']       Optional. Whether to ignore sticky posts. Default false.
 * @param string|null $attr['ignore_protected']    Optional. Whether to ignore protected posts. Default false.
 * @param string|null $attr['only_protected']      Optional. Whether to query only protected posts. Default false.
 * @param string|null $attr['author']              Optional. Limit posts to a specific author.
 * @param string|null $attr['author_ids']          Optional. Only include posts by these author IDs.
 * @param string|null $attr['exclude_author_ids']  Optional. Exclude posts with these author IDs.
 * @param string|null $attr['exclude_tag_ids']     Optional. Exclude posts with these tags.
 * @param string|null $attr['exclude_cat_ids']     Optional. Exclude posts with these categories.
 * @param string|null $attr['categories']          Optional. Limit posts to specific category names.
 * @param string|null $attr['tags']                Optional. Limit posts to specific tag names.
 * @param string|null $attr['fandoms']             Optional. Limit posts to specific fandom names.
 * @param string|null $attr['genres']              Optional. Limit posts to specific genre names.
 * @param string|null $attr['characters']          Optional. Limit posts to specific character names.
 * @param string|null $attr['rel']                 Optional. Relationship between taxonomies. Default 'AND'.
 * @param string|null $attr['seamless']            Optional. Whether to render the image seamless. Default false (Customizer).
 * @param string|null $attr['aspect_ratio']        Optional. Aspect ratio for the image.
 * @param string|null $attr['lightbox']            Optional. Whether the thumbnail is opened in the lightbox. Default true.
 * @param string|null $attr['thumbnail']           Optional. Whether to show the thumbnail. Default true (Customizer).
 * @param string|null $attr['date_format']         Optional. String to override the date format. Default empty.
 * @param string|null $attr['footer']              Optional. Whether to show the footer (if any). Default true.
 * @param string|null $attr['footer_author']       Optional. Whether to show the post author. Default true.
 * @param string|null $attr['footer_date']         Optional. Whether to show the post date. Default true.
 * @param string|null $attr['footer_comments']     Optional. Whether to show the post comment count. Default true.
 * @param string|null $attr['class']               Optional. Additional CSS classes, separated by whitespace.
 * @param string|null $args['splide']              Configuration JSON for the Splide slider. Default empty.
 *
 * @return string The captured shortcode HTML.
 */

function fictioneer_shortcode_article_cards( $attr ) {
  // Defaults
  $args = fictioneer_get_default_shortcode_args( $attr );

  // Terms
  $args['terms'] = fictioneer_sanitize_query_var( $attr['terms'] ?? 0, ['inline', 'pills', 'none', 'false'], 'inline' );
  $args['max_terms'] = absint( ( $attr['max_terms'] ?? 10 ) ?: 10 );

  // Post type(s)...
  $post_types = sanitize_text_field( $attr['post_type'] ?? 'post' );
  $post_types = fictioneer_explode_list( $post_types );

  $allowed_post_types = array(
    'post' => 'post',
    'posts' => 'post',
    'page' => 'page',
    'pages' => 'page',
    'story' => 'fcn_story',
    'stories' => 'fcn_story',
    'chapter' => 'fcn_chapter',
    'chapters' => 'fcn_chapter',
    'collection' => 'fcn_collection',
    'collections' => 'fcn_collection',
    'recommendation' => 'fcn_recommendation',
    'recommendations' => 'fcn_recommendation'
  );

  // ... must be in array
  $query_post_types = array_map( function( $item ) use ( $allowed_post_types ) {
    return $allowed_post_types[ $item ] ?? null;
  }, $post_types);

  // ... remove null values
  $query_post_types = array_filter( $query_post_types, function( $value ) {
    return ! is_null( $value );
  });

  // ... fix array
  $query_post_types = array_unique( $query_post_types );
  $query_post_types = array_values( $query_post_types );
  $query_post_types = empty( $query_post_types ) ? ['post'] : $query_post_types;

  // ... add to args
  $args['post_type'] = $query_post_types;

  // Extra classes
  if ( $args['splide'] ?? 0 ) {
    $args['classes'] .= ' splide _splide-placeholder';
  }

  // Transient?
  $transient_enabled = fictioneer_enable_shortcode_transients( 'fictioneer_article_cards' );

  if ( $transient_enabled && $args['cache'] ) {
    $base = serialize( $args ) . serialize( $attr );
    $transient_key = "fictioneer_shortcode_article_cards_html_" . md5( $base );
    $transient = get_transient( $transient_key );

    if ( ! empty( $transient ) ) {
      return $transient;
    }
  }

  // Buffer
  ob_start();

  get_template_part( 'partials/_article-cards', null, $args );

  $html = fictioneer_minify_html( ob_get_clean() );

  if ( ( $args['splide'] ?? 0 ) && strpos( $args['classes'], 'no-auto-splide' ) === false ) {
    $html = str_replace( '</section>', fictioneer_get_splide_inline_init() . '</section>', $html );
  }

  if ( $transient_enabled && $args['cache'] ) {
    set_transient( $transient_key, $html, FICTIONEER_SHORTCODE_TRANSIENT_EXPIRATION );
  }

  // Return minified buffer
  return $html;
}
add_shortcode( 'fictioneer_article_cards', 'fictioneer_shortcode_article_cards' );

// =============================================================================
// STORY SECTION SHORTCODE
// =============================================================================

/**
 * Shortcode to show story section
 *
 * @since 5.14.0
 *
 * @param string      $attr['story_id']   The ID of the story.
 * @param string|null $attr['tabs']       Optional. Whether to show the tabs above chapters. Default false.
 * @param string|null $attr['blog']       Optional. Whether to show the blog tab. Default false.
 * @param string|null $attr['pages']      Optional. Whether to show the custom page tabs. Default false.
 * @param string|null $attr['scheduled']  Optional. Whether to show the scheduled chapter note. Default false.
 * @param string|null $attr['class']      Optional. Additional CSS classes, separated by whitespace.
 *
 * @return string The captured shortcode HTML.
 */

function fictioneer_shortcode_story_section( $attr ) {
  global $post;

  // Abort if...
  if ( ! is_page_template( 'singular-story.php' ) ) {
    return fictioneer_notice(
      __( 'The [fictioneer_story_section] shortcode requires the "Story Page" template.' ),
      'warning',
      false
    );
  }

  // Setup
  $cache = filter_var( $attr['cache'] ?? 1, FILTER_VALIDATE_BOOLEAN );
  $story_id = fictioneer_validate_id( $attr['story_id'] ?? 0, 'fcn_story' );
  $post = get_post( $story_id );
  $story_data = fictioneer_get_story_data( $story_id );
  $classes = wp_strip_all_tags( $attr['class'] ?? '' );
  $show_tabs = filter_var( $attr['tabs'] ?? 0, FILTER_VALIDATE_BOOLEAN );
  $show_pages = filter_var( $attr['pages'] ?? 0, FILTER_VALIDATE_BOOLEAN );
  $show_blog = filter_var( $attr['blog'] ?? 0, FILTER_VALIDATE_BOOLEAN );
  $show_scheduled = filter_var( $attr['scheduled'] ?? 0, FILTER_VALIDATE_BOOLEAN );
  $hook_args = array( 'story_id' => $story_id, 'story_data' => $story_data, 'password_required' => post_password_required() );

  // Abort if...
  if ( ! $story_data ) {
    return '';
  }

  // Prepare classes
  if ( ! get_option( 'fictioneer_enable_checkmarks' ) ) {
    $classes .= ' _no-checkmarks';
  }

  if ( ! $show_pages || ! $show_tabs ) {
    $classes .= ' _no-pages';
  }

  if ( ! $show_blog || ! $show_tabs ) {
    $classes .= ' _no-blog';
  }

  // Transient?
  $transient_enabled = fictioneer_enable_shortcode_transients( 'fictioneer_story_section' );

  if ( $transient_enabled && $cache ) {
    $base = serialize( $attr ) . $classes;
    $transient_key = "fictioneer_shortcode_story_section_html_" . md5( $base );
    $transient = get_transient( $transient_key );

    if ( ! empty( $transient ) ) {
      return $transient;
    }
  }

  // Require functions (necessary in post editor for some reason)
  require_once __DIR__ . '/hooks/_story_hooks.php';

  // Setup post data
  setup_postdata( $post );

  // Buffer
  ob_start();

  echo '<div class="story-section-shortcode story _shortcode ' . esc_attr( $classes ) . '">';

  if ( $show_tabs ) {
    fictioneer_story_tabs( $hook_args );
  }

  if ( $show_scheduled ) {
    fictioneer_story_scheduled_chapter( $hook_args );
  }

  if ( $show_tabs && $show_pages ) {
    fictioneer_story_pages( $hook_args );
  }

  fictioneer_story_chapters( $hook_args );

  if ( $show_tabs && $show_blog ) {
    fictioneer_story_blog( $hook_args );
  }

  echo '</div>';

  // Store buffer
  $html = fictioneer_minify_html( ob_get_clean() );

  // Reset post data
  wp_reset_postdata();

  // Cache in Transient
  if ( $transient_enabled && $cache ) {
    set_transient( $transient_key, $html, FICTIONEER_SHORTCODE_TRANSIENT_EXPIRATION );
  }

  // Return minified buffer
  return $html;
}
add_shortcode( 'fictioneer_story_section', 'fictioneer_shortcode_story_section' );

// =============================================================================
// STORY ACTIONS SHORTCODE
// =============================================================================

/**
 * Shortcode to show story actions
 *
 * @since 5.14.0
 *
 * @param string      $attr['story_id']   The ID of the story.
 * @param string|null $attr['class']      Optional. Additional CSS classes, separated by whitespace.
 * @param string|null $attr['follow']     Optional. Whether to show the Follow button if enabled. Default true.
 * @param string|null $attr['reminder']   Optional. Whether to show the Reminder button if enabled. Default true.
 * @param string|null $attr['subscribe']  Optional. Whether to show the Subscribe button if enabled. Default true.
 * @param string|null $attr['download']   Optional. Whether to show the Download button if enabled. Default true.
 * @param string|null $attr['rss']        Optional. Whether to show the RSS links if enabled. Default true.
 * @param string|null $attr['share']      Optional. Whether to show the Share buttons if enabled. Default true.
 *
 * @return string The captured shortcode HTML.
 */

function fictioneer_shortcode_story_actions( $attr ) {
  // Abort if...
  if ( ! is_page_template( 'singular-story.php' ) ) {
    return fictioneer_notice(
      __( 'The [fictioneer_story_actions] shortcode requires the "Story Page" template.' ),
      'warning',
      false
    );
  }

  // Setup
  $cache = filter_var( $attr['cache'] ?? 1, FILTER_VALIDATE_BOOLEAN );
  $story_id = fictioneer_validate_id( $attr['story_id'] ?? 0, 'fcn_story' );
  $story_data = fictioneer_get_story_data( $story_id );
  $classes = wp_strip_all_tags( $attr['class'] ?? '' );
  $follow = filter_var( $attr['follow'] ?? 1, FILTER_VALIDATE_BOOLEAN );
  $reminder = filter_var( $attr['reminder'] ?? 1, FILTER_VALIDATE_BOOLEAN );
  $subscribe = filter_var( $attr['subscribe'] ?? 1, FILTER_VALIDATE_BOOLEAN );
  $download = filter_var( $attr['download'] ?? 1, FILTER_VALIDATE_BOOLEAN );
  $rss = filter_var( $attr['rss'] ?? 1, FILTER_VALIDATE_BOOLEAN );
  $share = filter_var( $attr['share'] ?? 1, FILTER_VALIDATE_BOOLEAN );

  // Abort if...
  if ( ! $story_data ) {
    return '';
  }

  // Prepare hook arguments
  $hook_args = array(
    'post_id' => $story_id,
    'post_type' => 'fcn_story',
    'story_id' => $story_id,
    'story_data' => $story_data,
    'follow' => $follow,
    'reminder' => $reminder,
    'subscribe' => $subscribe,
    'download' => $download,
    'rss' => $rss,
    'share' => $share
  );

  // Transient?
  $transient_enabled = fictioneer_enable_shortcode_transients( 'fictioneer_story_actions' );

  if ( $transient_enabled && $cache ) {
    $base = serialize( $attr ) . $classes;
    $transient_key = "fictioneer_shortcode_story_actions_html_" . md5( $base );
    $transient = get_transient( $transient_key );

    if ( ! empty( $transient ) ) {
      return $transient;
    }
  }

  // Add filter for buttons
  add_filter( 'fictioneer_filter_story_buttons', 'fictioneer_shortcode_remove_story_buttons', 99, 2 );

  // Build HTML
  $html = '<section class="story-actions story__after-summary ' . esc_attr( $classes ) . '">';
  $html .= fictioneer_get_media_buttons( $hook_args );
  $html .= '<div class="story__actions">' . fictioneer_get_story_buttons( $hook_args ) . '</div></section>';

  // Remove filter for buttons
  remove_filter( 'fictioneer_shortcode_remove_story_buttons', 99, 2 );

  // Minify HTML
  $html = fictioneer_minify_html( $html );

  // Cache in Transient
  if ( $transient_enabled && $cache ) {
    set_transient( $transient_key, $html, FICTIONEER_SHORTCODE_TRANSIENT_EXPIRATION );
  }

  // Return minified HTML
  return $html;
}
add_shortcode( 'fictioneer_story_actions', 'fictioneer_shortcode_story_actions' );

/**
 * Removes buttons for the [fictioneer_story_actions] shortcode
 *
 * This function only works if boolean arguments are provided for
 * 'follow', 'reminder', 'subscribe', and/or 'download'.
 *
 * @since 5.14.0
 *
 * @param array $output  Associative array with HTML for buttons to be rendered.
 * @param array $args    Arguments passed to filter.
 *
 * @return array The filtered output.
 */

function fictioneer_shortcode_remove_story_buttons( $output, $args ) {
  if ( ! ( $args['follow'] ?? 1 ) ) {
    unset( $output['follow'] );
  }

  if ( ! ( $args['reminder'] ?? 1 ) ) {
    unset( $output['reminder'] );
  }

  if ( ! ( $args['subscribe'] ?? 1 ) ) {
    unset( $output['subscribe'] );
  }

  if ( ! ( $args['download'] ?? 1 ) ) {
    unset( $output['epub'] );
    unset( $output['ebook'] );
  }

  return $output;
};

// =============================================================================
// SUBSCRIBE BUTTON SHORTCODE
// =============================================================================

/**
 * Shortcode to show subscribe button
 *
 * @since 5.14.0
 *
 * @param string|null $attr['post_id']   Optional. Post ID to subscribe to (for plugins). Defaults to current ID.
 * @param string|null $attr['story_id']  Optional. Story ID to subscribe to (for plugins). Defaults to current ID.
 * @param string|null $attr['inline']    Optional. Whether the button should be wrapped in a block. Default true.
 * @param string|null $attr['class']     Optional. Additional CSS classes, separated by whitespace.
 *
 * @return string The captured shortcode HTML.
 */

function fictioneer_shortcode_subscribe_button( $attr ) {
  // Setup
  $post_id = absint( $attr['post_id'] ?? $attr['story_id'] ?? get_the_ID() );
  $classes = wp_strip_all_tags( $attr['class'] ?? '' );
  $subscribe_buttons = fictioneer_get_subscribe_options( $post_id );

  if ( filter_var( $attr['inline'] ?? 0, FILTER_VALIDATE_BOOLEAN ) ) {
    $classes .= ' _inline';
  }

  // Build and return button
  if ( ! empty( $subscribe_buttons ) ) {
    return sprintf(
      '<div class="subscribe-menu-toggle button _secondary popup-menu-toggle _popup-right-if-last ' . esc_attr( $classes ) . '" tabindex="0" role="button" aria-label="%s" data-fictioneer-last-click-target="toggle" data-action="click->fictioneer-last-click#toggle"><div><i class="fa-solid fa-bell"></i> %s</div><div class="popup-menu _bottom _center">%s</div></div>',
      fcntr( 'subscribe', true ),
      fcntr( 'subscribe' ),
      $subscribe_buttons
    );
  }

  // Return nothing if empty
  return '';
}
add_shortcode( 'fictioneer_subscribe_button', 'fictioneer_shortcode_subscribe_button' );

// =============================================================================
// STORY COMMENTS SHORTCODE
// =============================================================================

/**
 * Shortcode to show story comments
 *
 * @since 5.14.0
 *
 * @param string|null $attr['story_id']  Optional. Story ID to get comments for. Defaults to current ID.
 * @param string|null $attr['header']    Optional. Whether to show the heading with count. Default true.
 * @param string|null $attr['class']     Optional. Additional CSS classes, separated by whitespace.
 *
 * @return string The captured shortcode HTML.
 */

function fictioneer_shortcode_story_comments( $attr ) {
  // Abort if...
  if ( ! is_page_template( 'singular-story.php' ) ) {
    return fictioneer_notice(
      __( 'The [fictioneer_story_comments] shortcode requires the "Story Page" template.' ),
      'warning',
      false
    );
  }

  // Setup
  $story_id = fictioneer_validate_id( $attr['story_id'] ?? get_the_ID(), 'fcn_story' );
  $story_data = fictioneer_get_story_data( $story_id ?: 0 );
  $header = filter_var( $attr['header'] ?? 1, FILTER_VALIDATE_BOOLEAN );
  $classes = wp_strip_all_tags( $attr['classes'] ?? $attr['class'] ?? '' );
  $style = esc_attr( wp_strip_all_tags( $attr['style'] ?? '' ) );

  if ( ! $story_data ) {
    return '';
  }

  // Require functions (necessary in post editor for some reason)
  require_once __DIR__ . '/hooks/_story_hooks.php';

  // Buffer
  ob_start();

  // Output story comment section
  fictioneer_story_comments(
    array(
      'story_id' => $story_id,
      'story_data' => $story_data,
      'shortcode' => 1,
      'classes' => $classes,
      'header' => $header,
      'style' => $style
    )
  );

  // Capture and return buffer
  return fictioneer_minify_html( ob_get_clean() );
}
add_shortcode( 'fictioneer_story_comments', 'fictioneer_shortcode_story_comments' );

// =============================================================================
// STORY DATA SHORTCODE
// =============================================================================

/**
 * Shortcode to show selected story data
 *
 * Data: id, word_count, chapter_count, icon, age_rating, rating_letter, comment_count,
 * datetime, date, time, categories, tags, genres, fandoms, characters, or warnings.
 *
 * Format: default, raw, or short. Not all of them apply to all data.
 *
 * @since 5.14.0
 *
 * @param string|null $attr['data']         The requested data, one at a time. See description.
 * @param string|null $attr['story_id']     Optional. Story ID to get comments for. Defaults to current ID.
 * @param string|null $attr['format']       Optional. Special formatting for selected data. See description.
 * @param string|null $attr['date_format']  Optional. Format for date string. Defaults to WP settings.
 * @param string|null $attr['time_format']  Optional. Format for time string. Defaults to WP settings.
 * @param string|null $attr['separator']    Optional. Separator string for tags, genres, fandoms, etc.
 * @param string|null $attr['tag']          Optional. The wrapper HTML tag. Defaults to 'span'.
 * @param string|null $attr['class']        Optional. Additional CSS classes, separated by whitespace.
 * @param string|null $attr['inner_class']  Optional. Additional CSS classes for nested items, separated by whitespace.
 * @param string|null $attr['style']        Optional. Inline style applied to wrapper element.
 * @param string|null $attr['inner_style']  Optional. Inline style applied to nested items.
 *
 * @return string The shortcode HTML.
 */

function fictioneer_shortcode_story_data( $attr ) {
  // Setup
  $story_id = fictioneer_validate_id( $attr['story_id'] ?? get_the_ID(), 'fcn_story' );
  $story_data = fictioneer_get_story_data( $story_id ?: 0 );
  $data = $attr['data'] ?? '';
  $format = $attr['format'] ?? '';
  $separator = wp_strip_all_tags( $attr['separator'] ?? '' ) ?: ', ';
  $classes = esc_attr( wp_strip_all_tags( $attr['class'] ?? '' ) );
  $inner_classes = esc_attr( wp_strip_all_tags( $attr['inner_class'] ?? '' ) );
  $style = esc_attr( wp_strip_all_tags( $attr['style'] ?? '' ) );
  $inner_style = esc_attr( wp_strip_all_tags( $attr['inner_style'] ?? '' ) );
  $tag = wp_strip_all_tags( $attr['tag'] ?? 'span' );
  $output = '';

  if ( ! $story_data ) {
    return '';
  }

  // Get requested data
  switch ( $data ) {
    case 'word_count':
      $output = $story_data['word_count'] ?? 0;
      $output = $format !== 'short' ? $output : fictioneer_shorten_number( $output );
      $output = ( $format === 'raw' || $format === 'short' ) ? $output : number_format_i18n( $output );
      break;
    case 'chapter_count':
      $output = $story_data['chapter_count'] ?? 0;
      $output = $format === 'raw' ? $output : number_format_i18n( $output );
      break;
    case 'status':
      $output = $story_data['status'] ?? '';
      break;
    case 'icon':
      $output = esc_attr( $story_data['icon'] ?? '' );
      $output = $output ? "<i class='{$output}'></i>" : '';
      break;
    case 'age_rating':
      $output = $story_data['rating'] ?? '';
      break;
    case 'rating_letter':
      $output = $story_data['rating_letter'] ?? '';
      break;
    case 'comment_count':
      $output = $story_data['comment_count'] ?? 0;
      $output = $format === 'raw' ? $output : number_format_i18n( $output );
      break;
    case 'id':
      $output = $story_data['id'] ?? '';
      break;
    case 'datetime':
      $date = get_the_date( $attr['date_format'] ?? '', $story_id );
      $time = get_the_time( $attr['time_format'] ?? '', $story_id );
      $output = sprintf( $format ? $format : '%s at %s', $date, $time );
      break;
    case 'date':
      $output = get_the_date( $attr['date_format'] ?? '', $story_id );
      break;
    case 'time':
      $output = get_the_time( $attr['time_format'] ?? '', $story_id );
      break;
  }

  // Get requested terms
  if ( in_array( $data, ['categories', 'tags', 'genres', 'fandoms', 'characters', 'warnings'] ) ) {
    $terms = $data === 'categories' ? get_the_category( $story_id ) : ( $story_data[ $data ] ?? [] );
    $terms = is_array( $terms ) ? $terms : [];
    $output = [];

    foreach ( $terms as $term ) {
      $link = get_tag_link( $term );
      $output[] = "<a href='{$link}' class='story-page-terms _{$data} {$inner_classes}' style='{$inner_style}'>{$term->name}</a>";
    }

    $output = implode( $separator, $output );
  }

  // Build and return output
  return $output ? "<{$tag} class='story-data {$classes}' style='{$style}'>{$output}</{$tag}>" : '';
}
add_shortcode( 'fictioneer_story_data', 'fictioneer_shortcode_story_data' );

// =============================================================================
// FONT AWESOME SHORTCODE
// =============================================================================

/**
 * Shortcode to show a Font Awesome icon
 *
 * @since 5.14.0
 *
 * @param string|null $attr['class']  The icon classes, separated by whitespace.
 *
 * @return string The shortcode HTML.
 */

function fictioneer_shortcode_font_awesome( $attr ) {
  // Setup
  $classes = esc_attr( trim( wp_strip_all_tags( $attr['class'] ?? '' ) ) );

  if ( empty( $classes ) ) {
    return '';
  }

  // Build and return output
  return "<i class='{$classes}'></i>";
}
add_shortcode( 'fictioneer_fa', 'fictioneer_shortcode_font_awesome' );

// =============================================================================
// SIDEBAR SHORTCODE
// =============================================================================

/**
 * Shortcode to show sidebar.
 *
 * @since 5.20.0
 * @since 5.27.3 - Added guard clause to prevent recursive loops.
 *
 * @param string|null $attr['name']  Optional. Name of the sidebar. Default 'fictioneer_sidebar'.
 *
 * @return string The captured shortcode HTML.
 */

function fictioneer_shortcode_sidebar( $attr ) {
  // Prevent recursive loops
  if ( did_action( 'dynamic_sidebar' ) > 0 ) {
    return 'Error: Don’t use [fictioneer_sidebar] inside sidebars!';
  }

  // Setup
  $name = sanitize_text_field( $attr['name'] ?? '' ) ?: 'fictioneer-sidebar';

  // Buffer
  ob_start();

  // Does sidebar exist?
  if ( ! is_active_sidebar( $name ) ) {
    return;
  }

  // Remove filters
  remove_filter( 'excerpt_more', 'fictioneer_excerpt_ellipsis' );
  remove_filter( 'excerpt_length', 'fictioneer_custom_excerpt_length' );

  // Start HTML ---> ?>
  <div class="fictioneer-sidebar _shortcode">
    <div class="fictioneer-sidebar__wrapper">
      <?php dynamic_sidebar( $name ); ?>
    </div>
  </div>
  <?php // <--- End HTML

  // Restore filters
  add_filter( 'excerpt_more', 'fictioneer_excerpt_ellipsis' );
  add_filter( 'excerpt_length', 'fictioneer_custom_excerpt_length' );

  // Capture and return buffer
  return ob_get_clean();
}
add_shortcode( 'fictioneer_sidebar', 'fictioneer_shortcode_sidebar' );

// =============================================================================
// TOOLTIP AND FOOTNOTE SHORTCODE
// =============================================================================

/**
 * Shortcode to add a tooltip with an associated footnote
 *
 * @since 5.25.0
 *
 * @param string  $atts['header']    Optional. Header of the tooltip.
 * @param string  $atts['content']   Content of the tooltip.
 * @param bool    $atts['footnote']  Optional. Whether to show the footnote. Default true.
 * @param string  $content           Shortcode content.
 *
 * @return string HTML for the tooltip and associated footnote link.
 */

function fictioneer_shortcode_tooltip( $atts, $content = null ) {
  // Initialize a static counter for unique tooltip/footnote IDs
  static $tooltip_id_counter = 0;

  // Setup
  $default_atts = array(
    'header' => '',
    'content' => '',
    'footnote' => true,
  );

  $atts = shortcode_atts( $default_atts, $atts, 'fcnt' );
  $footnote_allowed = get_option( 'fictioneer_generate_footnotes_from_tooltips' ) && $atts['footnote'];
  $footnote_link = '';

  // Sanitize user inputs
  $tooltip_header = trim( wp_kses_post( $atts[ 'header' ] ) );
  $tooltip_content = trim( wp_kses_post( $atts[ 'content' ] ) );

  // Bail if no content
  if ( empty( $tooltip_content ) ) {
    return $content;
  }

  // Increment counter
  $tooltip_id_counter++;

  // Prepare footnote if allowed
  if ( $footnote_allowed ) {
    // Create a footnote link to be appended to the tooltip content
    $footnote_link = sprintf(
      '<sup class="tooltip-counter"><a href="#footnote-%1$d" class="footnote-link">%1$d</a></sup>',
      $tooltip_id_counter
    );
  }

  // Prepare data attributes for the tooltip
  $tooltip_data = array(
    'dialog-header' => $tooltip_header,
    'dialog-content' => $tooltip_content . $footnote_link,
  );

  // Convert data array to HTML attributes
  $tooltip_data_attributes = array_map(
    function ( $key, $value ) {
      return sprintf( 'data-%s="%s"', esc_attr( $key ), esc_attr( $value ) );
    },
    array_keys( $tooltip_data ),
    $tooltip_data
  );

  $tooltip_title = _x( 'Click to see note', 'Tooltip shortcode.', 'fictioneer' );

  // Construct the HTML for the tooltip
  $html = sprintf(
    '<span><a id="tooltip-%1$d" class="modal-tooltip" title="%2$s" %3$s data-click-action="open-tooltip-modal">%4$s</a>%5$s</span>',
    $tooltip_id_counter,
    esc_attr( $tooltip_title ),
    implode( ' ', $tooltip_data_attributes ),
    $content,
    $footnote_link
  );

  // Collect footnote if allowed
  if ( $footnote_allowed ) {
    do_action( 'fictioneer_collect_footnote', $tooltip_id_counter, $tooltip_content );
  }

  return $html;
}
add_shortcode( 'fcnt', 'fictioneer_shortcode_tooltip' );

// =============================================================================
// TERMS SHORTCODE
// =============================================================================

/**
 * Shortcode to show taxonomies.
 *
 * @since 5.27.2
 *
 * @param string|null $attr['term_type']      Term type. Default 'post_tag'.
 * @param int|null    $attr['post_id']        Post ID. Default 0.
 * @param int|null    $attr['count']          Maximum number of terms. Default -1 (all).
 * @param string|null $attr['orderby']        Orderby argument. Default 'count'.
 * @param string|null $attr['order']          Order argument. Default 'DESC'.
 * @param bool|null   $attr['show_empty']     Whether to show empty terms. Default false.
 * @param bool|null   $attr['show_count']     Whether to show term counts. Default false.
 * @param string|null $attr['classes']        Additional section CSS classes. Default empty.
 * @param string|null $attr['inner_classes']  Additional term CSS classes. Default empty.
 * @param string|null $attr['style']          Inline section CSS style. Default empty.
 * @param string|null $attr['inner_style']    Inline term CSS style. Default empty.
 * @param string|null $attr['empty']          Override message for empty query result.
 *
 * @return string The shortcode HTML.
 */

function fictioneer_shortcode_terms( $attr ) {
  // Setup
  $type = sanitize_key( $attr['type'] ?? 'tag' );
  $post_id = absint( $attr['post_id'] ?? 0 );
  $count = max( -1, intval( $attr['count'] ?? -1 ) );
  $orderby = sanitize_text_field( $attr['orderby'] ?? 'count' );
  $order = sanitize_text_field( $attr['order'] ?? 'DESC' );
  $show_empty = filter_var( $attr['show_empty'] ?? 0, FILTER_VALIDATE_BOOLEAN );
  $show_count = filter_var( $attr['show_count'] ?? 0, FILTER_VALIDATE_BOOLEAN );
  $classes = esc_attr( wp_strip_all_tags( $attr['classes'] ?? $attr['class'] ?? '' ) );
  $inner_classes = esc_attr( wp_strip_all_tags( $attr['inner_classes'] ?? $attr['inner_class'] ?? '' ) );
  $style = esc_attr( wp_strip_all_tags( $attr['style'] ?? '' ) );
  $inner_style = esc_attr( wp_strip_all_tags( $attr['inner_style'] ?? '' ) );
  $empty = sanitize_text_field( $attr['empty'] ?? __( 'No taxonomies specified yet.', 'fictioneer' ) );

  $term_map = array(
    'tag' => 'post_tag',
    'genre' => 'fcn_genre',
    'fandom' => 'fcn_fandom',
    'character' => 'fcn_character',
    'warning' => 'fcn_content_warning'
  );

  $term_type = $term_map[ $type ] ?? $type;

  // Term exists?
  if ( ! taxonomy_exists( $term_type ) ) {
    return 'Error: Taxonomy does not exist.';
  }

  // Post exists?
  if ( $post_id && ! get_post( $post_id ) ) {
    return 'Error: Post not found.';
  }

  // Prepare query args
  $args = array(
    'fictioneer_query_name' => 'terms_shortcode',
    'taxonomy' => $term_type,
    'orderby' => $orderby,
    'order' => $order,
    'hide_empty' => ! $show_empty
  );

  if ( $count > 0 ) {
    $args['number'] = $count;
  }

  // Apply filters
  $args = apply_filters( 'fictioneer_filter_shortcode_terms_query_args', $args, $attr );

  // Query terms
  if ( $post_id ) {
    $terms = wp_get_post_terms( $post_id, $term_type, $args );
  } else {
    $terms = get_terms( $args );
  }

  // All good?
  if ( is_wp_error( $terms ) ) {
    return 'Error: ' . $terms->get_error_message();
  }

  // Build and return HTML
  $html = '';

  if ( ! empty( $terms ) ) {
    foreach ( $terms as $term ) {
      $html .= sprintf(
        '<a href="%s" class="tag-pill _taxonomy-%s _taxonomy-slug-%s %s" style="%s">%s</a>',
        get_tag_link( $term ),
        str_replace( 'fcn_', '', $term->taxonomy ),
        $term->slug,
        'tag-pill ' . $inner_classes,
        $inner_style,
        $show_count
          ? sprintf( _x( '%1$s (%2$s)', 'Terms shortcode with count.', 'fictioneer' ), $term->name, $term->count )
          : $term->name
      );
    }
  } else {
    $html = $empty;
  }

  return sprintf(
    '<section class="terms-shortcode tag-group %s" style="%s">%s</section>',
    "_{$term_type} {$classes} " . wp_unique_id( 'shortcode-id-' ),
    $style,
    $html
  );
}
add_shortcode( 'fictioneer_terms', 'fictioneer_shortcode_terms' );
