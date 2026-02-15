<?php

use Fictioneer\Utils_Admin;
use Fictioneer\Sanitizer;
use Fictioneer\Sanitizer_Admin;

// =============================================================================
// ALLOWED ORDERBY
// =============================================================================

/**
 * Return list of allowed orderby parameters for WP_Query.
 *
 * @since 5.7.0
 * @since 5.9.4 - Extended list.
 *
 * @return array List of allowed orderby parameters.
 */

function fictioneer_allowed_orderby() {
  $defaults = ['modified', 'date', 'title', 'rand', 'name', 'ID', 'comment_count', 'type', 'post__in', 'author', 'words'];

  return apply_filters( 'fictioneer_filter_allowed_orderby', $defaults );
}

// =============================================================================
// GET CARD LIST
// =============================================================================

if ( ! function_exists( 'fictioneer_get_card_list' ) ) {
  /**
   * Return the query and HTML list items for a post type.
   *
   * @since 5.0.0
   *
   * @param string $type        Either story, chapter, collection, recommendation, or post.
   * @param array  $query_args  Optional. Query arguments merged with the defaults.
   * @param string $empty       Optional. What to show as empty result. Defaults to 'No results'.
   * @param array  $card_args   Optional. Card partial arguments merged with the defaults.
   *
   * @return array|bool The query results ('query') and the cards as list items ('html').
   *                    False for impermissible parameters.
   */

  function fictioneer_get_card_list( $type, $query_args = [], $empty = '', $card_args = [] ) {
    // Setup
    $html = '';
    $empty = empty( $empty ) ? __( 'No results.', 'fictioneer' ) : $empty;
    $query = false;
    $allowed_types = ['fcn_story', 'fcn_chapter', 'fcn_collection', 'fcn_recommendation', 'post'];
    $post_type = in_array( $type, ['story', 'chapter', 'collection', 'recommendation'] ) ? "fcn_{$type}" : $type;
    $page = $query_args['paged'] ?? 1;
    $is_empty = false;
    $excluded = [];

    // Validations
    if ( ! in_array( $post_type, $allowed_types ) ) {
      return false;
    }

    // Default query arguments
    $the_query_args = array(
      'fictioneer_query_name' => 'get_card_list',
      'post_type' => $post_type,
      'post_status' => 'publish',
      'orderby' => 'modified',
      'order' => 'DESC',
      'posts_per_page' => get_option( 'posts_per_page' ),
      'no_found_rows' => $query_args['no_found_rows'] ?? false,
      'update_post_meta_cache' => true,
      'update_post_term_cache' => true
    );

    // Default card arguments
    $the_card_args = array(
      'cache' => fictioneer_caching_active( 'card_args' ) && ! fictioneer_private_caching_active()
    );

    // Merge with optional arguments
    $the_query_args = array_merge( $the_query_args, $query_args );
    $the_card_args = array_merge( $the_card_args, $card_args );

    // Query (but not if 'post__in' is set and empty)
    if ( ! ( isset( $the_query_args['post__in'] ) && empty( $the_query_args['post__in'] ) ) ) {

      $batch_limit = (int) apply_filters( 'fictioneer_filter_query_batch_limit', 800, 'card_list' );
      $batch_limit = max( 100, min( 2000, $batch_limit ) );

      // Look for IDs to exclude if story or chapter...
      if ( in_array( $post_type, ['fcn_story', 'fcn_chapter'] ) && $batch_limit > 0 ) {
        // Get complete set for filtering (required due to pagination)
        $all_query = new WP_Query(
          array_merge(
            $the_query_args,
            array(
              'posts_per_page' => -1,
              'no_found_rows' => true,
              'update_post_term_cache' => false, // Improve performance
              'suppress_filters' => true // Improve performance
            )
          )
        );
      }

      if ( ! empty( $excluded ) && count( $excluded ) <= $batch_limit ) {
        $the_query_args['post__not_in'] = array_values(
          array_unique( array_merge( $excluded, $the_query_args['post__not_in'] ?? [] ) )
        );
      }

      // Query without excluded posts
      $query = new WP_Query( $the_query_args );

      // Prime thumbnail cache
      if ( function_exists( 'update_post_thumbnail_cache' ) ) {
        update_post_thumbnail_cache( $query );
      }

      // Prime author cache
      if (
        get_option( 'fictioneer_show_authors' ) &&
        ! empty( $query->posts ) &&
        function_exists( 'update_post_author_caches' )
      ) {
        update_post_author_caches( $query->posts );
      }
    }

    // Buffer HTML output
    ob_start();

    // Loop results
    if ( $query && $query->have_posts() ) {
      while ( $query->have_posts() ) {
        $query->the_post();
        $card_post_id = get_the_ID();

        switch ( $post_type ) {
          case 'fcn_story':
            fictioneer_get_template_part( 'partials/_card-story', null, $the_card_args );
            break;
          case 'fcn_chapter':
            fictioneer_get_template_part( 'partials/_card-chapter', null, $the_card_args );
            break;
          default:
            fictioneer_get_template_part( 'partials/_card-' . str_replace( 'fcn_', '', $post_type ), null, $the_card_args );
        }
      }

      wp_reset_postdata();
    } elseif ( $empty ) {
      $is_empty = true;
      // Start HTML ---> ?>
      <li class="no-results"><?php echo $page > 1 ? __( 'No more results.', 'fictioneer' ) : $empty; ?></li>
      <?php // <--- End HTML
    }

    // Get buffered HTML
    $html = ob_get_clean();

    // Return results
    return array( 'query' => $query, 'html' => $html, 'page' => $page, 'empty' => $is_empty );
  }
}

// =============================================================================
// APPEND DATE QUERY
// =============================================================================

if ( ! function_exists( 'fictioneer_append_date_query' ) ) {
  /**
   * Append date query to query arguments.
   *
   * @since 5.4.0
   *
   * @param array      $query_args  Query arguments to modify.
   * @param string|int $ago         Optional. Time range in days or valid date string. Default null.
   * @param string     $orderby     Optional. Current orderby. Default null.
   *
   * @return array Modified query arguments.
   */

  function fictioneer_append_date_query( $query_args, $ago = null, $orderby = null ) {
    // Ago?
    if ( empty( $ago ) ) {
      $ago = $_GET['ago'] ?? 0;
      $ago = is_numeric( $ago ) ? absint( $ago ) : sanitize_text_field( $ago );
    }

    // Orderby?
    if ( empty( $orderby ) ) {
      $orderby = Sanitizer::sanitize_query_var( $_GET['orderby'] ?? 0, fictioneer_allowed_orderby(), 'modified' );
    }

    // Validate ago argument
    if ( ! is_numeric( $ago ) && strtotime( $ago ) === false ) {
      $ago = 0;
    }

    // Build date query...
    if ( is_numeric( $ago ) && $ago > 0 ) {
      // ... for number as days
      $query_args['date_query'] = array(
        array(
          'column' => $orderby === 'modified' ? 'post_modified_gmt' : 'post_date_gmt',
          'after' => absint( $ago ) . ' days ago',
          'inclusive' => true,
        )
      );
    } elseif ( ! empty( $ago ) ) {
      // ... for valid strtotime() string
      $query_args['date_query'] = array(
        array(
          'column' => $orderby === 'modified' ? 'post_modified_gmt' : 'post_date_gmt',
          'after' => sanitize_text_field( $ago ),
          'inclusive' => true,
        )
      );
    }

    // Non-date related order?
    if ( isset( $query_args['date_query'] ) && in_array( $orderby, ['title', 'rand'] ) ) {
      // Second condition for modified date
      $modified_arg = $query_args['date_query'][0];
      $modified_arg['column'] = 'post_modified';

      // Include both publish and modified dates
      $query_args['date_query'] = array(
        'relation' => 'OR',
        $query_args['date_query'][0],
        $modified_arg
      );
    }

    // Return (maybe) modified query arguments
    return $query_args;
  }
}

// =============================================================================
// STICKY STORIES
// =============================================================================

/**
 * Filter sticky stories to the top and accounts for missing meta fields.
 *
 * @since 5.7.3
 * @since 5.9.4 - Check orderby by components, extend allow list.
 *
 * @param array    $clauses   An associative array of WP_Query SQL clauses.
 * @param WP_Query $wp_query  The WP_Query instance.
 *
 * @return string The updated or unchanged SQL clauses.
 */

function fictioneer_clause_sticky_stories( $clauses, $wp_query ) {
  global $wpdb;

  // Setup
  $vars = $wp_query->query_vars;
  $allowed_queries = ['stories_list', 'latest_stories', 'latest_stories_compact', 'author_stories'];
  $allowed_orderby = ['', 'date', 'modified', 'title', 'meta_value', 'name', 'ID', 'post__in'];
  $given_orderby = $vars['orderby'] ?? [''];
  $given_orderby = is_array( $given_orderby ) ? $given_orderby : explode( ' ', $vars['orderby'] );

  // Return if query is not allowed
  if (
    ! in_array( $vars['fictioneer_query_name'] ?? 0, $allowed_queries ) ||
    ! empty( array_diff( $given_orderby, $allowed_orderby ) )
  ) {
    return $clauses;
  }

  // Update clauses to set missing meta key to 0
  $clauses['join'] .= " LEFT JOIN $wpdb->postmeta AS m ON ($wpdb->posts.ID = m.post_id AND m.meta_key = 'fictioneer_story_sticky')";
  $clauses['orderby'] = "COALESCE(m.meta_value+0, 0) DESC, " . $clauses['orderby'];
  $clauses['groupby'] = "$wpdb->posts.ID";

  // Pass to query
  return $clauses;
}

if ( FICTIONEER_ENABLE_STICKY_CARDS ) {
  add_filter( 'posts_clauses', 'fictioneer_clause_sticky_stories', 10, 2 );
}

// =============================================================================
// CHAPTER STORY ID
// =============================================================================

/**
 * Return ID of the chapter story or empty string.
 *
 * @since 5.26.0
 *
 * @param int $chapter_id  Chapter ID.
 *
 * @return int|string Story ID or empty string if not set.
 */

function fictioneer_get_chapter_story_id( $chapter_id ) {
  $chapter = get_post( $chapter_id );

  if ( $chapter && $chapter->post_type === 'fcn_chapter' && $chapter->post_parent ) {
    return $chapter->post_parent ?: get_post_meta( $chapter_id, 'fictioneer_chapter_story', true );
  }

  return get_post_meta( $chapter_id, 'fictioneer_chapter_story', true );
}

/**
 * Set the chapter parent ID to the story ID.
 *
 * @since 5.26.0
 *
 * @param int $chapter_id  Chapter ID.
 * @param int $story_id    Story ID.
 */

function fictioneer_set_chapter_story_parent( $chapter_id, $story_id ) {
  global $wpdb;

  $chapter_id = (int) $chapter_id;
  $story_id = (int) ( $story_id ?: 0 );

  $wpdb->query(
    $wpdb->prepare(
      "UPDATE {$wpdb->posts} SET post_parent = %d WHERE ID = %d",
      $story_id ?: 0,
      $chapter_id
    )
  );
}

// =============================================================================
// SPOTLIGHT QUERY
// =============================================================================

/**
 * Query weighted random spotlight selection for given post type.
 *
 * @since 5.33.5
 *
 * @param string|null $post_type           Optional. Post type to spotlight. Default 'fcn_story'.
 * @param int|null    $args['count']       Optional. How many posts to query. Default 6.
 * @param int|null    $args['new_days']    Optional. How many days a post is considered new. Default 14.
 * @param int|null    $args['new_weight']  Optional. How much weight new posts have. Default calculated.
 * @param array|null  $args['query_args']  Optional. Additional query args for internal WP_Query.
 * @param string|null $args['pool']        Optional. Change the pool with a custom string. Default empty.
 * @param string|null $args['return']      Optional. Either 'query' or 'args'. Default 'query'.
 *
 * @return WP_Query|array Query result or query arguments for later use.
 */

function fictioneer_random_spotlight_query( $post_type = 'fcn_story', $args = [] ) {
  global $wpdb;

  // Pre-filter
  $args = apply_filters( 'fictioneer_filter_spotlight_args', $args, $post_type );

  // Setup
  $post_type = Sanitizer::sanitize_post_type( $post_type );
  $option_key = 'fictioneer_spotlight_' . ( $args['pool'] ?? '' ) . '_' . $post_type;
  $count = max( 1, (int) ( $args['count'] ?? 6 ) );
  $all_post_ids = [];
  $selected_ids = [];
  $now = current_time( 'timestamp', 1 );
  $new_period = max( 1, (int) ( $args['new_days'] ?? 14 ) ) * DAY_IN_SECONDS;
  $return = $args['return'] ?? 'query';

  $previous_ids = get_option( $option_key, [] ) ?: [];
  $previous_ids = is_array( $previous_ids ) ? $previous_ids : [];

  if ( $previous_ids ) {
    $previous_ids = array_map( 'intval', $previous_ids );
    $previous_ids = array_filter( $previous_ids, fn( $v ) => $v > 0 );
  }

  // Fetch all published posts
  $all_post_ids = $wpdb->get_col(
    $wpdb->prepare(
      "SELECT ID FROM {$wpdb->posts}
        WHERE post_type = %s AND post_status = 'publish'",
      $post_type
    )
  );

  if ( empty( $all_post_ids ) ) {
    return [];
  }

  // Determine available IDs and pre-select remainder
  $available_ids = array_values( array_diff( $all_post_ids, $previous_ids ) );

  if ( count( $available_ids ) < $count ) {
    $previous_ids = [];
    $selected_ids = $available_ids;
    $available_ids = array_values( array_diff( $all_post_ids, $selected_ids ) );
  }

  // Short-circuit if already enough IDs are selected
  if ( count( $selected_ids ) >= $count ) {
    update_option( $option_key, $selected_ids, false );

    $query_args = array_merge(
      array(
        'post_type' => $post_type,
        'post__in' => array_slice( $selected_ids, 0, $count ),
        'orderby' => 'post__in',
        'posts_per_page' => $count,
        'no_found_rows' => true,
        'cache_results' => false
      ),
      $args['query_args'] ?? []
    );

    $query_args = apply_filters( 'fictioneer_filter_spotlight_query_args', $query_args, $args );

    if ( $return === 'query' ) {
      return new WP_Query( $query_args );
    }

    return $query_args;
  }

  // If no more IDs are available (reset)
  if ( empty( $available_ids ) ) {
    $previous_ids = [];
    $available_ids = array_values( array_diff( $all_post_ids, $selected_ids ) );
  }

  // Query available story IDs with GMT date
  $placeholders = implode( ',', array_fill( 0, count( $available_ids ), '%d' ) );

  $available_stories = $available_ids ? $wpdb->get_results(
    $wpdb->prepare(
      "SELECT ID, post_date_gmt FROM {$wpdb->posts}
      WHERE ID IN ({$placeholders})",
      $available_ids
    ),
    OBJECT_K
  ) : [];

  // Build weighted pot
  $pot = [];
  $total_stories = count( $all_post_ids );
  $new_story_weight = max( 1, (int) ( $args['new_weight'] ?? ceil( $total_stories / $count ) ) );

  foreach ( $available_stories as $id => $story ) {
    $age = $now - strtotime( $story->post_date_gmt . ' GMT' );
    $weight = ( $age <= $new_period ) ? $new_story_weight : 1;

    for ( $i = 0; $i < $weight; $i++ ) {
      $pot[] = (int) $id;
    }
  }

  // Random draw
  shuffle( $pot );

  $selected_lookup = [];

  foreach ( $selected_ids as $id ) {
    $selected_lookup[ $id ] = true;
  }

  $need = $count - count( $selected_ids );

  foreach ( $pot as $id ) {
    if ( ! isset( $selected_lookup[ $id ] ) ) {
      $selected_ids[] = $id;
      $selected_lookup[ $id ] = true;

      if ( --$need === 0 ) {
        break;
      }
    }
  }

  // Update previously drawn IDs
  $previous_ids = array_values( array_unique( array_merge( $previous_ids, $selected_ids ) ) );
  update_option( $option_key, $previous_ids, false );

  // Query posts
  $query_args = array_merge(
    array(
      'post_type' => $post_type,
      'post__in' => array_slice( $selected_ids, 0, $count ),
      'orderby' => 'post__in',
      'posts_per_page' => $count,
      'no_found_rows' => true,
      'cache_results' => false
    ),
    $args['query_args'] ?? []
  );

  $query_args = apply_filters( 'fictioneer_filter_spotlight_query_args', $query_args, $args );

  if ( $return === 'query' ) {
    return new WP_Query( $query_args );
  }

  return $query_args;
}
