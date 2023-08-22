<?php

// =============================================================================
// SHORTCODE TRANSIENTS ENABLED?
// =============================================================================

if ( ! defined( 'FICTIONEER_SHORTCODE_TRANSIENTS_ENABLED' ) ) {
  define(
    'FICTIONEER_SHORTCODE_TRANSIENTS_ENABLED',
    fictioneer_enable_shortcode_transients()
  );
}

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
 * @since Fictioneer 5.0
 */

function fictioneer_update_shortcode_relationships( $post_id, $post ) {
  // Prevent multi-fire
  if ( fictioneer_multi_save_guard( $post_id ) ) {
    return;
  }

  // Setup
  $registry = fictioneer_get_relationship_registry();

  // Look for showcase shortcode
  if ( str_contains( $post->post_content, 'fictioneer_showcase' ) ) {
    $registry['always'][ $post_id ] = 'shortcode';
  } else {
    unset( $registry['always'][ $post_id ] );
  }

  // Look for post-related shortcode
  if ( str_contains( $post->post_content, 'fictioneer_latest_post' ) ) {
    $registry['ref_posts'][ $post_id ] = 'shortcode';
  } else {
    unset( $registry['ref_posts'][ $post_id ] );
  }

  // Look for chapter-related shortcodes
  if (
    str_contains( $post->post_content, 'fictioneer_chapter_list' ) ||
    str_contains( $post->post_content, 'fictioneer_latest_chapter' ) ||
    str_contains( $post->post_content, 'fictioneer_chapter_cards' )
  ) {
    $registry['ref_chapters'][ $post_id ] = 'shortcode';
  } else {
    unset( $registry['ref_chapters'][ $post_id ] );
  }

  // Look for story-related shortcodes
  if (
    str_contains( $post->post_content, 'fictioneer_latest_stor' ) ||
    str_contains( $post->post_content, 'fictioneer_story_cards' ) ||
    str_contains( $post->post_content, 'fictioneer_latest_update' ) ||
    str_contains( $post->post_content, 'fictioneer_update_cards' )
  ) {
    $registry['ref_stories'][ $post_id ] = 'shortcode';
  } else {
    unset( $registry['ref_stories'][ $post_id ] );
  }

  // Look for recommendation-related shortcodes
  if (
    str_contains( $post->post_content, 'fictioneer_latest_recommendation' ) ||
    str_contains( $post->post_content, 'fictioneer_recommendation_cards' )
  ) {
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
// GET SHORTCODE TAXONOMIES
// =============================================================================

/**
 * Extract taxonomies from shortcode attributes
 *
 * @since Fictioneer 5.2.0
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
 * @since Fictioneer 5.2.0
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
// SHOWCASE SHORTCODE
// =============================================================================

/**
 * Shortcode to display showcase
 *
 * @since 5.0
 *
 * @param string      $attr['for']              What the showcase is for. Allowed are chapters,
 *                                              collections, recommendations, and stories.
 * @param string|null $attr['count']            Optional. Maximum number of items. Default 9.
 * @param string|null $attr['author']           Optional. Limit items to a specific author.
 * @param string|null $attr['order']            Optional. Order direction. Default 'DESC'.
 * @param string|null $attr['orderby']          Optional. Order argument. Default 'date'.
 * @param string|null $attr['post_ids']         Optional. Limit items to specific post IDs.
 * @param string|null $attr['exclude_tag_ids']  Optional. Exclude posts with these tags.
 * @param string|null $attr['exclude_cat_ids']  Optional. Exclude posts with these categories.
 * @param string|null $attr['categories']       Optional. Limit items to specific category names.
 * @param string|null $attr['tags']             Optional. Limit items to specific tag names.
 * @param string|null $attr['fandoms']          Optional. Limit items to specific fandom names.
 * @param string|null $attr['genres']           Optional. Limit items to specific genre names.
 * @param string|null $attr['characters']       Optional. Limit items to specific character names.
 * @param string|null $attr['rel']              Optional. Relationship between taxonomies. Default 'AND'.
 * @param string|null $attr['class']            Optional. Additional CSS classes, separated by whitespace.
 *
 * @return string The rendered shortcode HTML.
 */

function fictioneer_shortcode_showcase( $attr ) {
  // Abort if...
  if ( empty( $attr['for'] ) ) return '';

  // Setup
  $count = max( 1, intval( $attr['count'] ?? 8 ) );
  $author = $attr['author'] ?? false;
  $order = $attr['order'] ?? 'DESC';
  $orderby = $attr['orderby'] ?? 'date';
  $no_cap = $attr['no_cap'] ?? false;
  $post_ids = [];
  $rel = 'AND';
  $classes = [];

  // Post IDs
  if ( ! empty( $attr['post_ids'] ) ) {
    $post_ids = fictioneer_explode_list( $attr['post_ids'] );
  }

  // Relation
  if ( ! empty( $attr['rel'] ) ) {
    $rel = strtolower( $attr['rel'] ) == 'or' ? 'OR' : $rel;
  }

  // Extra classes
  if ( ! empty( $attr['class'] ) ) {
    $classes[] = esc_attr( wp_strip_all_tags( $attr['class'] ) );
  }

  // Prepare arguments
  $args = array(
    'count' => $count,
    'author' => $author,
    'orderby' => $orderby,
    'order' => $order,
    'post_ids' => $post_ids,
    'excluded_tags' => fictioneer_explode_list( $attr['exclude_tag_ids'] ?? '' ),
    'excluded_cats' => fictioneer_explode_list( $attr['exclude_cat_ids'] ?? '' ),
    'taxonomies' => fictioneer_get_shortcode_taxonomies( $attr ),
    'relation' => $rel,
    'no_cap' => $no_cap == 'true' || $no_cap == '1',
    'classes' => $classes
  );

  switch ( $attr['for'] ) {
    case 'collections':
      $args['type'] = 'fcn_collection';
      break;
    case 'chapters':
      $args['type'] = 'fcn_chapter';
      break;
    case 'stories':
      $args['type'] = 'fcn_story';
      break;
    case 'recommendations':
      $args['type'] = 'fcn_recommendation';
      break;
  }

  // Abort if...
  if ( ! isset( $args['type'] ) ) {
    return '';
  }

  // Transient?
  if ( FICTIONEER_SHORTCODE_TRANSIENTS_ENABLED ) {
    $base = serialize( $args ) . serialize( $attr ) . $count;
    $type = $args['type'];
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

  if ( FICTIONEER_SHORTCODE_TRANSIENTS_ENABLED ) {
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
 * @param string|null $attr['count']            Optional. Maximum number of items. Default 4.
 * @param string|null $attr['author']           Optional. Limit items to a specific author.
 * @param string|null $attr['orderby']          Optional. Order argument. Default 'date'.
 * @param string|null $attr['type']             Optional. Choose between 'default', 'simple', and 'compact'.
 * @param string|null $attr['spoiler']          Optional. Whether to show spoiler content.
 * @param string|null $attr['source']           Optional. Whether to show author and story.
 * @param string|null $attr['post_ids']         Optional. Limit items to specific post IDs.
 * @param string|null $attr['exclude_tag_ids']  Optional. Exclude posts with these tags.
 * @param string|null $attr['exclude_cat_ids']  Optional. Exclude posts with these categories.
 * @param string|null $attr['categories']       Optional. Limit items to specific category names.
 * @param string|null $attr['tags']             Optional. Limit items to specific tag names.
 * @param string|null $attr['fandoms']          Optional. Limit items to specific fandom names.
 * @param string|null $attr['genres']           Optional. Limit items to specific genre names.
 * @param string|null $attr['characters']       Optional. Limit items to specific character names.
 * @param string|null $attr['rel']              Optional. Relationship between taxonomies. Default 'AND'.
 * @param string|null $attr['class']            Optional. Additional CSS classes, separated by whitespace.
 *
 * @return string The rendered shortcode HTML.
 */

function fictioneer_shortcode_latest_chapters( $attr ) {
  // Setup
  $count = max( 1, intval( $attr['count'] ?? 4 ) );
  $type = $attr['type'] ?? 'default';
  $author = $attr['author'] ?? false;
  $order = $attr['order'] ?? 'desc';
  $orderby = $attr['orderby'] ?? 'date';
  $spoiler = $attr['spoiler'] ?? false;
  $source = $attr['source'] ?? 'true';
  $post_ids = [];
  $rel = 'AND';
  $classes = [];

  // Post IDs
  if ( ! empty( $attr['post_ids'] ) ) {
    $post_ids = fictioneer_explode_list( $attr['post_ids'] );
    $count = count( $post_ids );
  }

  // Relation
  if ( ! empty( $attr['rel'] ) ) {
    $rel = strtolower( $attr['rel'] ) == 'or' ? 'OR' : $rel;
  }

  // Extra classes
  if ( ! empty( $attr['class'] ) ) {
    $classes[] = esc_attr( wp_strip_all_tags( $attr['class'] ) );
  }

  // Args
  $args = array(
    'simple' => false,
    'count' => $count,
    'author' => $author,
    'order' => $order,
    'orderby' => $orderby,
    'spoiler' => $spoiler == 'true',
    'source' => $source == 'true',
    'post_ids' => $post_ids,
    'excluded_tags' => fictioneer_explode_list( $attr['exclude_tag_ids'] ?? '' ),
    'excluded_cats' => fictioneer_explode_list( $attr['exclude_cat_ids'] ?? '' ),
    'taxonomies' => fictioneer_get_shortcode_taxonomies( $attr ),
    'relation' => $rel,
    'classes' => $classes
  );

  // Transient?
  if ( FICTIONEER_SHORTCODE_TRANSIENTS_ENABLED ) {
    $base = serialize( $args ) . serialize( $attr ) . $count;
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
    default:
      $args['simple'] = $type == 'simple';
      get_template_part( 'partials/_latest-chapters', null, $args );
  }

  $html = fictioneer_minify_html( ob_get_clean() );

  if ( FICTIONEER_SHORTCODE_TRANSIENTS_ENABLED ) {
    set_transient( $transient_key, $html, FICTIONEER_SHORTCODE_TRANSIENT_EXPIRATION );
  }

  // Return minified buffer
  return $html;
}
add_shortcode( 'fictioneer_latest_chapters', 'fictioneer_shortcode_latest_chapters' );
add_shortcode( 'fictioneer_latest_chapter', 'fictioneer_shortcode_latest_chapters' ); // Alias
add_shortcode( 'fictioneer_chapter_cards', 'fictioneer_shortcode_latest_chapters' ); // Alias

// =============================================================================
// LATEST STORIES SHORTCODE
// =============================================================================

/**
 * Shortcode to show latest stories
 *
 * @since 3.0
 *
 * @param string|null $attr['count']            Optional. Maximum number of items. Default 4.
 * @param string|null $attr['author']           Optional. Limit items to a specific author.
 * @param string|null $attr['type']             Optional. Choose between 'default' and 'compact'.
 * @param string|null $attr['orderby']          Optional. Order argument. Default 'date'.
 * @param string|null $attr['post_ids']         Optional. Limit items to specific post IDs.
 * @param string|null $attr['exclude_tag_ids']  Optional. Exclude posts with these tags.
 * @param string|null $attr['exclude_cat_ids']  Optional. Exclude posts with these categories.
 * @param string|null $attr['categories']       Optional. Limit items to specific category names.
 * @param string|null $attr['tags']             Optional. Limit items to specific tag names.
 * @param string|null $attr['fandoms']          Optional. Limit items to specific fandom names.
 * @param string|null $attr['genres']           Optional. Limit items to specific genre names.
 * @param string|null $attr['characters']       Optional. Limit items to specific character names.
 * @param string|null $attr['rel']              Optional. Relationship between taxonomies. Default 'AND'.
 * @param string|null $attr['class']            Optional. Additional CSS classes, separated by whitespace.
 *
 * @return string The rendered shortcode HTML.
 */

function fictioneer_shortcode_latest_stories( $attr ) {
  // Setup
  $count = max( 1, intval( $attr['count'] ?? 4 ) );
  $type = $attr['type'] ?? 'default';
  $author = $attr['author'] ?? false;
  $order = $attr['order'] ?? 'desc';
  $orderby = $attr['orderby'] ?? 'date';
  $post_ids = [];
  $rel = 'AND';
  $classes = [];

  // Post IDs
  if ( ! empty( $attr['post_ids'] ) ) {
    $post_ids = fictioneer_explode_list( $attr['post_ids'] );
    $count = count( $post_ids );
  }

  // Relation
  if ( ! empty( $attr['rel'] ) ) {
    $rel = strtolower( $attr['rel'] ) == 'or' ? 'OR' : $rel;
  }

  // Extra classes
  if ( ! empty( $attr['class'] ) ) {
    $classes[] = esc_attr( wp_strip_all_tags( $attr['class'] ) );
  }

  // Args
  $args = array(
    'count' => $count,
    'author' => $author,
    'order' => $order,
    'orderby' => $orderby,
    'post_ids' => $post_ids,
    'excluded_tags' => fictioneer_explode_list( $attr['exclude_tag_ids'] ?? '' ),
    'excluded_cats' => fictioneer_explode_list( $attr['exclude_cat_ids'] ?? '' ),
    'taxonomies' => fictioneer_get_shortcode_taxonomies( $attr ),
    'relation' => $rel,
    'classes' => $classes
  );

  // Transient?
  if ( FICTIONEER_SHORTCODE_TRANSIENTS_ENABLED ) {
    $base = serialize( $args ) . serialize( $attr ) . $count;
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
    default:
      get_template_part( 'partials/_latest-stories', null, $args );
  }

  $html = fictioneer_minify_html( ob_get_clean() );

  if ( FICTIONEER_SHORTCODE_TRANSIENTS_ENABLED ) {
    set_transient( $transient_key, $html, FICTIONEER_SHORTCODE_TRANSIENT_EXPIRATION );
  }

  // Return minified buffer
  return $html;
}
add_shortcode( 'fictioneer_latest_stories', 'fictioneer_shortcode_latest_stories' );
add_shortcode( 'fictioneer_latest_story', 'fictioneer_shortcode_latest_stories' ); // Alias
add_shortcode( 'fictioneer_story_cards', 'fictioneer_shortcode_latest_stories' ); // Alias

// =============================================================================
// LATEST UPDATES SHORTCODE
// =============================================================================

/**
 * Shortcode to show latest story updates
 *
 * @since 4.3
 *
 * @param string|null $attr['count']            Optional. Maximum number of items. Default 4.
 * @param string|null $attr['author']           Optional. Limit items to a specific author.
 * @param string|null $attr['type']             Optional. Choose between 'default', 'simple', and 'compact'.
 * @param string|null $attr['post_ids']         Optional. Limit items to specific post IDs.
 * @param string|null $attr['exclude_tag_ids']  Optional. Exclude posts with these tags.
 * @param string|null $attr['exclude_cat_ids']  Optional. Exclude posts with these categories.
 * @param string|null $attr['categories']       Optional. Limit items to specific category names.
 * @param string|null $attr['tags']             Optional. Limit items to specific tag names.
 * @param string|null $attr['fandoms']          Optional. Limit items to specific fandom names.
 * @param string|null $attr['genres']           Optional. Limit items to specific genre names.
 * @param string|null $attr['characters']       Optional. Limit items to specific character names.
 * @param string|null $attr['rel']              Optional. Relationship between taxonomies. Default 'AND'.
 * @param string|null $attr['class']            Optional. Additional CSS classes, separated by whitespace.
 *
 * @return string The rendered shortcode HTML.
 */

function fictioneer_shortcode_latest_story_updates( $attr ) {
  // Setup
  $count = max( 1, intval( $attr['count'] ?? 4 ) );
  $type = $attr['type'] ?? 'default';
  $author = $attr['author'] ?? false;
  $order = $attr['order'] ?? 'desc';
  $post_ids = [];
  $rel = 'AND';
  $classes = [];

  // Post IDs
  if ( ! empty( $attr['post_ids'] ) ) {
    $post_ids = fictioneer_explode_list( $attr['post_ids'] );
    $count = count( $post_ids );
  }

  // Relation
  if ( ! empty( $attr['rel'] ) ) {
    $rel = strtolower( $attr['rel'] ) == 'or' ? 'OR' : $rel;
  }

  // Extra classes
  if ( ! empty( $attr['class'] ) ) {
    $classes[] = esc_attr( wp_strip_all_tags( $attr['class'] ) );
  }

  // Args
  $args = array(
    'count' => $count,
    'author' => $author,
    'order' => $order,
    'post_ids' => $post_ids,
    'excluded_tags' => fictioneer_explode_list( $attr['exclude_tag_ids'] ?? '' ),
    'excluded_cats' => fictioneer_explode_list( $attr['exclude_cat_ids'] ?? '' ),
    'taxonomies' => fictioneer_get_shortcode_taxonomies( $attr ),
    'relation' => $rel,
    'classes' => $classes
  );

  // Transient?
  if ( FICTIONEER_SHORTCODE_TRANSIENTS_ENABLED ) {
    $base = serialize( $args ) . serialize( $attr ) . $count;
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
    default:
      $args['simple'] = $type == 'simple';
      get_template_part( 'partials/_latest-updates', null, $args );
  }

  $html = fictioneer_minify_html( ob_get_clean() );

  if ( FICTIONEER_SHORTCODE_TRANSIENTS_ENABLED ) {
    set_transient( $transient_key, $html, FICTIONEER_SHORTCODE_TRANSIENT_EXPIRATION );
  }

  // Return minified buffer
  return $html;
}
add_shortcode( 'fictioneer_latest_updates', 'fictioneer_shortcode_latest_story_updates' );
add_shortcode( 'fictioneer_latest_update', 'fictioneer_shortcode_latest_story_updates' ); // Alias
add_shortcode( 'fictioneer_update_cards', 'fictioneer_shortcode_latest_story_updates' ); // Alias

// =============================================================================
// LATEST RECOMMENDATIONS SHORTCODE
// =============================================================================

/**
 * Shortcode to show latest recommendations
 *
 * @since 4.0
 *
 * @param string|null $attr['count']            Optional. Maximum number of items. Default 4.
 * @param string|null $attr['author']           Optional. Limit items to a specific author.
 * @param string|null $attr['type']             Optional. Choose between 'default' and 'compact'.
 * @param string|null $attr['post_ids']         Optional. Limit items to specific post IDs.
 * @param string|null $attr['exclude_tag_ids']  Optional. Exclude posts with these tags.
 * @param string|null $attr['exclude_cat_ids']  Optional. Exclude posts with these categories.
 * @param string|null $attr['categories']       Optional. Limit items to specific category names.
 * @param string|null $attr['tags']             Optional. Limit items to specific tag names.
 * @param string|null $attr['fandoms']          Optional. Limit items to specific fandom names.
 * @param string|null $attr['genres']           Optional. Limit items to specific genre names.
 * @param string|null $attr['characters']       Optional. Limit items to specific character names.
 * @param string|null $attr['rel']              Optional. Relationship between taxonomies. Default 'AND'.
 * @param string|null $attr['class']            Optional. Additional CSS classes, separated by whitespace.
 *
 * @return string The rendered shortcode HTML.
 */

function fictioneer_shortcode_latest_recommendations( $attr ) {
  // Setup
  $count = max( 1, intval( $attr['count'] ?? 4 ) );
  $type = $attr['type'] ?? 'default';
  $author = $attr['author'] ?? false;
  $order = $attr['order'] ?? 'desc';
  $orderby = $attr['orderby'] ?? 'date';
  $post_ids = [];
  $rel = 'AND';
  $classes = [];

  // Post IDs
  if ( ! empty( $attr['post_ids'] ) ) {
    $post_ids = fictioneer_explode_list( $attr['post_ids'] );
    $count = count( $post_ids );
  }

  // Relation
  if ( ! empty( $attr['rel'] ) ) {
    $rel = strtolower( $attr['rel'] ) == 'or' ? 'OR' : $rel;
  }

  // Extra classes
  if ( ! empty( $attr['class'] ) ) {
    $classes[] = esc_attr( wp_strip_all_tags( $attr['class'] ) );
  }

  // Args
  $args = array(
    'count' => $count,
    'author' => $author,
    'order' => $order,
    'orderby' => $orderby,
    'post_ids' => $post_ids,
    'excluded_tags' => fictioneer_explode_list( $attr['exclude_tag_ids'] ?? '' ),
    'excluded_cats' => fictioneer_explode_list( $attr['exclude_cat_ids'] ?? '' ),
    'taxonomies' => fictioneer_get_shortcode_taxonomies( $attr ),
    'relation' => $rel,
    'classes' => $classes
  );

  // Transient?
  if ( FICTIONEER_SHORTCODE_TRANSIENTS_ENABLED ) {
    $base = serialize( $args ) . serialize( $attr ) . $count;
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

  if ( FICTIONEER_SHORTCODE_TRANSIENTS_ENABLED ) {
    set_transient( $transient_key, $html, FICTIONEER_SHORTCODE_TRANSIENT_EXPIRATION );
  }

  // Return minified buffer
  return $html;
}
add_shortcode( 'fictioneer_latest_recommendations', 'fictioneer_shortcode_latest_recommendations' );
add_shortcode( 'fictioneer_latest_recommendation', 'fictioneer_shortcode_latest_recommendations' ); // Alias
add_shortcode( 'fictioneer_recommendation_cards', 'fictioneer_shortcode_latest_recommendations' ); // Alias

// =============================================================================
// LATEST POST SHORTCODE
// =============================================================================

/**
 * Shortcode to show the latest post
 *
 * @since 4.0
 *
 * @param string|null $attr['count']            Optional. Maximum number of items. Default 1.
 * @param string|null $attr['author']           Optional. Limit items to a specific author.
 * @param string|null $attr['post_ids']         Optional. Limit items to specific post IDs.
 * @param string|null $attr['exclude_tag_ids']  Optional. Exclude posts with these tags.
 * @param string|null $attr['exclude_cat_ids']  Optional. Exclude posts with these categories.
 * @param string|null $attr['categories']       Optional. Limit items to specific category names.
 * @param string|null $attr['tags']             Optional. Limit items to specific tag names.
 * @param string|null $attr['rel']              Optional. Relationship between taxonomies. Default 'AND'.
 * @param string|null $attr['class']            Optional. Additional CSS classes, separated by whitespace.
 *
 * @return string The rendered shortcode HTML.
 */

function fictioneer_shortcode_latest_posts( $attr ) {
  // Setup
  $author = $attr['author'] ?? false;
  $count = max( 1, intval( $attr['count'] ?? 1 ) );
  $post_ids = [];
  $rel = 'AND';
  $classes = [];

  // Post IDs
  if ( ! empty( $attr['post_ids'] ) ) {
    $post_ids = fictioneer_explode_list( $attr['post_ids'] );
    $count = count( $post_ids );
  }

  // Relation
  if ( ! empty( $attr['rel'] ) ) {
    $rel = strtolower( $attr['rel'] ) == 'or' ? 'OR' : $rel;
  }

  // Extra classes
  if ( ! empty( $attr['class'] ) ) {
    $classes[] = esc_attr( wp_strip_all_tags( $attr['class'] ) );
  }

  // Args
  $args = array(
    'count' => $count,
    'author' => $author,
    'post_ids' => $post_ids,
    'excluded_tags' => fictioneer_explode_list( $attr['exclude_tag_ids'] ?? '' ),
    'excluded_cats' => fictioneer_explode_list( $attr['exclude_cat_ids'] ?? '' ),
    'taxonomies' => fictioneer_get_shortcode_taxonomies( $attr ),
    'relation' => $rel,
    'classes' => $classes
  );

  // Transient?
  if ( FICTIONEER_SHORTCODE_TRANSIENTS_ENABLED ) {
    $base = serialize( $args ) . serialize( $attr ) . $count;
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

  if ( FICTIONEER_SHORTCODE_TRANSIENTS_ENABLED ) {
    set_transient( $transient_key, $html, FICTIONEER_SHORTCODE_TRANSIENT_EXPIRATION );
  }

  // Return minified buffer
  return $html;
}
add_shortcode( 'fictioneer_latest_posts', 'fictioneer_shortcode_latest_posts' );
add_shortcode( 'fictioneer_latest_post', 'fictioneer_shortcode_latest_posts' ); // Alias

// =============================================================================
// BOOKMARKS SHORTCODE
// =============================================================================

/**
 * Shortcode to show bookmarks
 *
 * @since 4.0
 *
 * @param string|null $attr['count']       Optional. Maximum number of items. Default -1 (all).
 * @param string|null $attr['show_empty']  Optional. Whether to show the "no bookmarks" message. Default false.
 *
 * @return string The rendered shortcode HTML.
 */

function fictioneer_shortcode_bookmarks( $attr ) {
  // Setup
  $count = max( -1, intval( $attr['count'] ?? -1 ) );
  $show_empty = $attr['show_empty'] ?? false;

  // Buffer
  ob_start();

  get_template_part( 'partials/_bookmarks', null, array(
    'count' => $count,
    'show_empty' => $show_empty
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
 * @since 4.7
 *
 * @return string The rendered shortcode HTML.
 */

function fictioneer_shortcode_cookie_buttons( $attr ) {
  ob_start();
  get_template_part( 'partials/_cookie-buttons' );
  return fictioneer_minify_html( ob_get_clean() );
}
add_shortcode( 'fictioneer_cookie_buttons', 'fictioneer_shortcode_cookie_buttons' );

// =============================================================================
// CHAPTER LIST SHORTCODE
// =============================================================================

/**
 * Shortcode to show chapter list outside of story pages
 *
 * @since 5.0
 * @see fictioneer_validate_id()
 * @see fictioneer_get_story_data()
 *
 * @param string      $attr['story_id']     Either/Or. The ID of the story the chapters belong to.
 * @param string|null $attr['chapter_ids']  Either/Or. Comma-separated list of chapter IDs.
 * @param string|null $attr['count']        Optional. Maximum number of items. Default -1 (all).
 * @param string|null $attr['offset']       Optional. Skip a number of posts.
 * @param string|null $attr['group']        Optional. Only show chapters of the group.
 * @param string|null $attr['heading']      Optional. Show <h5> heading above list.
 * @param string|null $attr['class']        Optional. Additional CSS classes, separated by whitespace.
 *
 * @return string The rendered shortcode HTML.
 */

function fictioneer_shortcode_chapter_list( $attr ) {
  // Build empty case

  ob_start();
  // Start HTML ---> ?>
  <div class="chapter-group">
    <?php if ( ! empty( $attr['heading'] ) ) : ?>
      <button class="chapter-group__name" aria-label="<?php esc_attr_e( 'Toggle chapter group collapse', 'fictioneer' ); ?>" tabindex="0">
        <i class="fa-solid fa-chevron-down chapter-group__heading-icon"></i>
        <span><?php echo $attr['heading']; ?></span>
      </button>
    <?php endif; ?>
    <ol class="chapter-group__list">
      <li class="chapter-group__list-item _empty"><?php _e( 'No chapters published yet.', 'fictioneer' ) ?></li>
    </ol>
  </div>
  <?php // <--- End HTML
  $empty = fictioneer_minify_html( ob_get_clean() );

  // Abort if...
  if ( empty( $attr['story_id'] ) && empty( $attr['chapter_ids'] ) ) {
    return $empty;
  }

  // Setup
  $count = max( -1, intval( $attr['count'] ?? -1 ) );
  $offset = max( 0, intval( $attr['offset'] ?? 0 ) );
  $group = empty( $attr['group'] ) ? false : strtolower( trim( $attr['group'] ) );
  $heading = empty( $attr['heading'] ) ? false : $attr['heading'];
  $story_id = fictioneer_validate_id( $attr['story_id'] ?? -1, 'fcn_story' );
  $hide_icons = get_option( 'fictioneer_hide_chapter_icons' );
  $can_checkmarks = get_option( 'fictioneer_enable_checkmarks' ) && ( is_user_logged_in() || get_option( 'fictioneer_enable_ajax_authentication' ) );
  $chapter_ids = [];
  $chapters = [];
  $classes = [];

  // Extract chapter IDs (if any)
  if ( ! empty( $attr['chapter_ids'] ) ) {
    $chapter_ids = fictioneer_explode_list( $attr['chapter_ids'] );
  }

  // Get chapters...
  if ( $story_id && empty( $chapter_ids ) ) {
    // ... via story
    $hide_icons = $hide_icons || fictioneer_get_field( 'fictioneer_story_hide_chapter_icons', $story_id );
    $story_data = fictioneer_get_story_data( $story_id, false ); // Does not refresh comment count!
    $chapters = $story_data['chapter_ids'];
  } elseif ( ! empty( $chapter_ids ) ) {
    // ... via chapter IDs
    $chapters = $chapter_ids;
  }

  // Extra classes
  if ( $hide_icons ) {
    $classes[] = '_no-icons';
  }

  if ( ! empty( $attr['class'] ) ) {
    $classes[] = esc_attr( wp_strip_all_tags( $attr['class'] ) );
  }

  // Apply offset and count
  if ( ! $group ) {
    $chapters = array_slice( $chapters, $offset );
    $chapters = $count > 0 ? array_slice( $chapters, 0, $count ) : $chapters;
  }

  // Check array for items
  if ( empty( $chapters ) ) {
    return $empty;
  }

  // Query chapters
  $query_args = array(
    'post_type' => 'fcn_chapter',
    'post_status' => 'publish',
    'post__in' => $chapters, // Cannot be empty!
    'ignore_sticky_posts' => true,
    'orderby' => 'post__in', // Preserve order from meta box
    'posts_per_page' => -1, // Get all chapters (this can be hundreds)
    'no_found_rows' => true, // Improve performance
    'update_post_term_cache' => false // Improve performance
  );

  // Transient?
  if ( FICTIONEER_SHORTCODE_TRANSIENTS_ENABLED ) {
    $base = serialize( $query_args ) . serialize( $attr ) . serialize( $classes );
    $base .= ( $hide_icons ? '1' : '0' ) . ( $can_checkmarks ? '1' : '0' );
    $transient_key = "fictioneer_shortcode_chapter_list_html_" . md5( $base );
    $transient = get_transient( $transient_key );

    if ( ! empty( $transient ) ) {
      return $transient;
    }
  }

  // Query
  $chapter_query = fictioneer_shortcode_query( $query_args );

  // Check query for items
  if ( ! $chapter_query->have_posts() ) {
    return $empty;
  }

  // Buffer
  ob_start();

  // Start HTML ---> ?>
  <div class="chapter-group <?php echo implode( ' ', $classes ); ?>">
    <?php if ( $heading ) : ?>
      <button class="chapter-group__name" aria-label="<?php esc_attr_e( 'Toggle chapter group collapse', 'fictioneer' ); ?>" tabindex="0">
        <i class="fa-solid fa-chevron-down chapter-group__heading-icon"></i>
        <span><?php echo $heading; ?></span>
      </button>
    <?php endif; ?>
    <ol class="chapter-group__list">
      <?php
        $render_count = 0;

        while( $chapter_query->have_posts() ) {
          // Setup
          $chapter_query->the_post();
          $chapter_id = get_the_ID();
          $chapter_story_id = fictioneer_get_field( 'fictioneer_chapter_story' );

          // Skip not visible chapters
          if ( fictioneer_get_field( 'fictioneer_chapter_hidden' ) ) {
            continue;
          }

          // Check group (if any)
          if ( $group && $group != strtolower( trim( fictioneer_get_field( 'fictioneer_chapter_group' ) ) ) ) {
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
          $warning = fictioneer_get_field( 'fictioneer_chapter_warning' );
          $icon = fictioneer_get_icon_field( 'fictioneer_chapter_icon' );
          $text_icon = fictioneer_get_field( 'fictioneer_chapter_text_icon' );
          $prefix = fictioneer_get_field( 'fictioneer_chapter_prefix' );
          $words = get_post_meta( $chapter_id, '_word_count', true );
          $title = fictioneer_get_safe_title( $chapter_id );

          // Start HTML ---> ?>
          <li class="chapter-group__list-item<?php echo $warning ? ' _warning' : ''; ?>" data-post-id="<?php echo $chapter_id; ?>">
            <?php if ( ! empty( $text_icon ) && ! $hide_icons ) : ?>
              <span class="chapter-group__list-item-icon _text text-icon"><?php echo $text_icon; ?></span>
            <?php elseif ( ! $hide_icons ) : ?>
              <i class="<?php echo empty( $icon ) ? 'fa-solid fa-book' : $icon; ?> chapter-group__list-item-icon"></i>
            <?php endif; ?>

            <a href="<?php the_permalink( $chapter_id ); ?>" class="chapter-group__list-item-link truncate _1-1">
              <?php
                if ( ! empty( $prefix ) ) {
                  echo apply_filters( 'fictioneer_filter_list_chapter_prefix', $prefix );
                }
                echo $title;
              ?>
            </a>

            <?php
              // Chapter subrow
              $chapter_data = [];
              $chapter_data['id'] = $chapter_id;
              $chapter_data['warning'] = $warning;
              $chapter_data['password'] = post_password_required();
              $chapter_data['timestamp'] = get_the_time( 'c' );
              $chapter_data['list_date'] = get_the_date( '' );
              $chapter_data['words'] = $words;

              echo fictioneer_get_list_chapter_meta_row( $chapter_data );
            ?>

            <?php if ( $can_checkmarks && ! empty( $chapter_story_id ) && get_post_status( $chapter_story_id ) === 'publish' ) : ?>
              <button
                class="checkmark chapter-group__list-item-checkmark"
                data-type="chapter"
                data-story-id="<?php echo $chapter_story_id; ?>"
                data-id="<?php echo $chapter_id; ?>"
                role="checkbox"
                aria-checked="false"
                aria-label="<?php printf( esc_attr__( 'Chapter checkmark for %s.', 'fictioneer' ), $title ); ?>"
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

  if ( FICTIONEER_SHORTCODE_TRANSIENTS_ENABLED ) {
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
 * @since 5.0
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
 * @return string The rendered shortcode HTML.
 */

function fictioneer_shortcode_contact_form( $attr ) {
  // Setup
  $title = $attr['title'] ?? _x( 'Nameless Form', 'Contact form.', 'fictioneer' );
  $submit = $attr['submit'] ?? __( 'Submit', 'fictioneer' );
  $privacy_policy = filter_var( $attr['privacy_policy'] ?? 0, FILTER_VALIDATE_BOOLEAN );
  $required = isset( $attr['required'] ) ? 'required' : '';
  $email = $attr['email'] ?? '';
  $name = $attr['name'] ?? '';
  $fields = [];
  $classes = [];

  // Extra classes
  if ( ! empty( $attr['class'] ) ) {
    $classes[] = esc_attr( wp_strip_all_tags( $attr['class'] ) );
  }

  // HTML snippets
  if ( ! empty( $email ) ) {
    $fields[] = "<input style='opacity: 0;' type='email' name='email' maxlength='100' placeholder='$email' $required>";
  }

  if ( ! empty( $name ) ) {
    $fields[] = "<input style='opacity: 0;' type='text' name='name' maxlength='100' placeholder='$name' $required>";
  }

  // Custom text fields
  for ( $i = 1; $i <= 6; $i++ ) {
    $field = $attr["text_$i"] ?? '';

    if ( ! empty( $field ) ) {
      $fields[] = "<input style='opacity: 0;' type='text' name='text_$i' maxlength='100' placeholder='$field' $required><input type='hidden' name='text_label_$i' value='$field'>";
    }
  }

  // Custom checkboxes
  for ( $i = 1; $i <= 6; $i++ ) {
    $field = $attr["check_$i"] ?? '';

    if ( ! empty( $field ) ) {
      $fields[] = "<label class='checkbox-label'><input class='_no-stretch' style='opacity: 0;' type='checkbox' value='1' name='check_$i' autocomplete='off' $required><span>$field</span></label><input type='hidden' name='check_label_$i' value='$field'>";
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
  <form class="fcn-contact-form <?php echo implode( ' ', $classes ); ?>">
    <div class="fcn-contact-form__message">
      <textarea class="fcn-contact-form__textarea adaptive-textarea" style="opacity: 0;" name="message" maxlength="65525" placeholder="<?php _e( 'Please enter your message.', 'fictioneer' ); ?>" required></textarea>
    </div>
    <?php if ( ! empty( $fields ) ) : ?>
      <div class="fcn-contact-form__fields">
        <?php foreach( $fields as $field ) : ?>
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
 * @since 5.0
 *
 * @param string|null $attr['simple']       Optional. Hide the advanced options.
 * @param string|null $attr['placeholder']  Optional. Placeholder text.
 * @param string|null $attr['type']         Optional. Default post type to query.
 *
 * @return string The rendered shortcode HTML.
 */

function fictioneer_shortcode_search( $attr ) {
  // Setup
  $args = array( 'cache' => true );
  $simple = isset( $attr['simple'] ) ? $attr['simple'] == 'true' || $attr['simple'] == '1' : false;
  $placeholder = $attr['placeholder'] ?? false;
  $type = $attr['type'] ?? false;

  // Prepare arguments
  if ( $simple ) {
    $args['simple'] = $simple;
  }

  if ( $placeholder ) {
    $args['placeholder'] = $placeholder;
  }

  if ( $type && in_array( $type, ['any', 'story', 'chapter', 'recommendation', 'collection', 'post'] ) ) {
    $args['preselect_type'] = in_array( $type, ['story', 'chapter', 'recommendation', 'collection'] ) ? "fcn_$type" : $type;
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
 * @param string|null $attr['author']              Optional. Limit items to a specific author.
 * @param string|null $attr['author_ids']          Optional. Only include posts by these author IDs.
 * @param string|null $attr['exclude_author_ids']  Optional. Exclude posts with these author IDs.
 * @param string|null $attr['exclude_tag_ids']     Optional. Exclude posts with these tags.
 * @param string|null $attr['exclude_cat_ids']     Optional. Exclude posts with these categories.
 * @param string|null $attr['categories']          Optional. Limit items to specific category names.
 * @param string|null $attr['tags']                Optional. Limit items to specific tag names.
 * @param string|null $attr['rel']                 Optional. Relationship between taxonomies. Default 'AND'.
 * @param string|null $attr['class']               Optional. Additional CSS classes, separated by whitespace.
 *
 * @return string The rendered shortcode HTML.
 */

function fictioneer_shortcode_blog( $attr ) {
  // Setup
  $author = $attr['author'] ?? false;
  $taxonomies = fictioneer_get_shortcode_taxonomies( $attr );
  $exclude_tag_ids = fictioneer_explode_list( $attr['exclude_tag_ids'] ?? '' );
  $exclude_cat_ids = fictioneer_explode_list( $attr['exclude_cat_ids'] ?? '' );
  $exclude_author_ids = fictioneer_explode_list( $attr['exclude_author_ids'] ?? '' );
  $author_ids = fictioneer_explode_list( $attr['author_ids'] ?? '' );
  $rel = 'AND';
  $classes = '';

  // Page
  $page = get_query_var( 'page' );
  $page = empty( $page ) ? get_query_var( 'paged' ) : $page;
  $page = empty( $page ) ? 1 : $page;

  // Arguments
  $query_args = array(
    'post_type' => 'post',
    'post_status' => 'publish',
    'paged' => max( 1, $page ),
    'posts_per_page' => $attr['per_page'] ?? get_option( 'posts_per_page' )
  );

  // Author?
  if ( ! empty( $author ) ) {
    $query_args['author_name'] = $author;
  }

  // Author IDs?
  if ( ! empty( $author_ids ) ) {
    $query_args['author__in'] = $author_ids;
  }

  // Relation?
  if ( ! empty( $attr['rel'] ) ) {
    $rel = strtolower( $attr['rel'] ) == 'or' ? 'OR' : $rel;
  }

  // Taxonomies?
  if ( ! empty( $taxonomies ) ) {
    $taxonomies['relation'] = $rel;
    $taxonomies['taxonomies'] = $taxonomies;
    $query_args['tax_query'] = fictioneer_get_shortcode_tax_query( $taxonomies );
  }

  // Excluded tags?
  if ( ! empty( $exclude_tag_ids ) ) {
    $query_args['tag__not_in'] = $exclude_tag_ids;
  }

  // Excluded categories?
  if ( ! empty( $exclude_cat_ids ) ) {
    $query_args['category__not_in'] = $exclude_cat_ids;
  }

  // Excluded authors?
  if ( ! empty( $exclude_author_ids ) ) {
    $query_args['author__not_in'] = $exclude_author_ids;
  }

  // Extra classes
  if ( ! empty( $attr['class'] ) ) {
    $classes = esc_attr( wp_strip_all_tags( $attr['class'] ) );
  }

  // Apply filters
  $query_args = apply_filters( 'fictioneer_filter_shortcode_blog_query_args', $query_args, $attr );

  // Transient?
  if ( FICTIONEER_SHORTCODE_TRANSIENTS_ENABLED ) {
    $base = serialize( $query_args ) . serialize( $attr ) . serialize( $classes ) . $page;
    $transient_key = "fictioneer_shortcode_chapter_list_html_" . md5( $base );
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

  // Buffer
  ob_start();

  if ( $blog_query->have_posts() ) {
    // Start HTML ---> ?>
    <section class="blog-posts _nested <?php echo $classes; ?>" id="blog">
      <?php
        while ( $blog_query->have_posts() ) {
          $blog_query->the_post();
          get_template_part( 'partials/_post', null, ['nested' => true] );

          $pag_args = array(
            'current' => max( 1, $page ),
            'total' => $blog_query->max_num_pages,
            'prev_text' => fcntr( 'previous' ),
            'next_text' => fcntr( 'next' ),
            'add_fragment' => '#blog'
          );
        }
        wp_reset_postdata();
      ?>
      <nav class="pagination"><?php echo fictioneer_paginate_links( $pag_args ); ?></nav>
    </section>
    <?php // <--- End HTML
  } else {
    // Start HTML ---> ?>
    <article class="post _empty">
      <span><?php _e( 'No (more) posts found.', 'fictioneer' ) ?></span>
    </article>
    <?php // <--- End HTML
  }

  // Store buffer
  $html = fictioneer_minify_html( ob_get_clean() );

  if ( FICTIONEER_SHORTCODE_TRANSIENTS_ENABLED ) {
    set_transient( $transient_key, $html, FICTIONEER_SHORTCODE_TRANSIENT_EXPIRATION );
  }

  // Return minified buffer
  return $html;
}
add_shortcode( 'fictioneer_blog', 'fictioneer_shortcode_blog' );

?>
