<?php

// =============================================================================
// ALLOWED ORDERBY
// =============================================================================

/**
 * Returns list of allowed orderby parameters for WP_Query
 *
 * @since 5.7.0
 *
 * @return array List of allowed orderby parameters.
 */

function fictioneer_allowed_orderby() {
  $defaults = ['modified', 'date', 'title', 'rand'];

  return apply_filters( 'fictioneer_filter_allowed_orderby', $defaults );
}

// =============================================================================
// GET CARD LIST
// =============================================================================

if ( ! function_exists( 'fictioneer_get_card_list' ) ) {
  /**
   * Returns the query and HTML list items for a post type
   *
   * @since 5.0.0
   *
   * @param string $type        Either story, chapter, collection, recommendation, or post.
   * @param array  $query_args  Optional. Query arguments merged with the defaults.
   * @param string $empty       Optional. What to show as empty result. Defaults to 'No results'.
   * @param array  $card_args   Optional. Card partial arguments merged with the defaults.
   *
   * @return array|boolean The query results ('query') and the cards as list items ('html').
   *                       False for impermissible parameters.
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
      'cache' => fictioneer_caching_active() && ! fictioneer_private_caching_active()
    );

    // Merge with optional arguments
    $the_query_args = array_merge( $the_query_args, $query_args );
    $the_card_args = array_merge( $the_card_args, $card_args );

    // Query (but not if 'post__in' is set and empty)
    if ( ! ( isset( $the_query_args['post__in'] ) && empty( $the_query_args['post__in'] ) ) ) {

      // Look for IDs to exclude if story or chapter...
      if ( in_array( $post_type, ['fcn_story', 'fcn_chapter'] ) && FICTIONEER_QUERY_ID_ARRAY_LIMIT > 0 ) {
        // Get complete set for filtering (required due to pagination)
        $all_query = new WP_Query(
          array_merge( $the_query_args, array( 'posts_per_page' => -1, 'no_found_rows' => true ) )
        );

        // Get excluded posts (faster than meta query)
        if ( $post_type === 'fcn_story' ) {
          // Story hidden?
          foreach ( $all_query->posts as $candidate ) {
            if ( get_post_meta( $candidate->ID, 'fictioneer_story_hidden', true ) ) {
              $excluded[] = $candidate->ID;
            }
          }
        } else {
          // Chapter hidden or excluded?
          foreach ( $all_query->posts as $candidate ) {
            if (
              get_post_meta( $candidate->ID, 'fictioneer_chapter_hidden', true ) ||
              get_post_meta( $candidate->ID, 'fictioneer_chapter_no_chapter', true )
            ) {
              $excluded[] = $candidate->ID;
            }
          }
        }
      }

      if ( ! empty( $excluded ) && count( $excluded ) <= FICTIONEER_QUERY_ID_ARRAY_LIMIT ) {
        $the_query_args['post__not_in'] = array_merge( $excluded, ( $the_query_args['post__not_in'] ?? [] ) );
      }

      // Query without excluded posts
      $query = new WP_Query( $the_query_args );

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
      $card_partial = 'partials/_card-' . str_replace( 'fcn_', '', $post_type );

      while ( $query->have_posts() ) {
        $query->the_post();
        $card_post_id = get_the_ID();

        switch ( $post_type ) {
          case 'fcn_story':
            if ( get_post_meta( $card_post_id, 'fictioneer_story_hidden', true ) ) {
              get_template_part( 'partials/_card-hidden', null, $the_card_args );
            } else {
              get_template_part( $card_partial, null, $the_card_args );
            }
            break;
          case 'fcn_chapter':
            if (
              get_post_meta( $card_post_id, 'fictioneer_chapter_hidden', true ) ||
              get_post_meta( $card_post_id, 'fictioneer_chapter_no_chapter', true )
            ) {
              get_template_part( 'partials/_card-hidden', null, $the_card_args );
            } else {
              get_template_part( $card_partial, null, $the_card_args );
            }
            break;
          default:
            get_template_part( $card_partial, null, $the_card_args );
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
   * Appends date query to query arguments
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
      $orderby = array_intersect( [ strtolower( $_GET['orderby'] ?? 0 ) ], fictioneer_allowed_orderby() );
      $orderby = reset( $orderby ) ?: 'modified';
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
          'column' => $orderby === 'modified' ? 'post_modified' : 'post_date',
          'after' => absint( $ago ) . ' days ago',
          'inclusive' => true,
        )
      );
    } elseif ( ! empty( $ago ) ) {
      // ... for valid strtotime() string
      $query_args['date_query'] = array(
        array(
          'column' => $orderby === 'modified' ? 'post_modified' : 'post_date',
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
// IGNORE PROTECTED POSTS FILTER
// =============================================================================

/**
 * Filter to ignore protected posts
 *
 * Note: Filter is not added by default, only in certain places.
 *
 * @since 5.7.3
 *
 * @param string $where  WHERE statement. Default empty string.
 *
 * @return string The updated WHERE statement.
 */

function fictioneer_exclude_protected_posts( $where = '' ) {
  $where .= " AND post_password = ''";

  return $where;
}

// =============================================================================
// STICKY STORIES
// =============================================================================

/**
 * Filters sticky stories to the top and accounts for missing meta fields
 *
 * @since 5.7.3
 *
 * @param array    $clauses   An associative array of WP_Query SQL clauses.
 * @param WP_Query $wp_query  The WP_Query instance.
 *
 * @return string The updated SQL clauses.
 */

function fictioneer_clause_sticky_stories( $clauses, $wp_query ) {
  global $wpdb;

  // Setup
  $vars = $wp_query->query_vars;
  $allowed_queries = ['stories_list', 'latest_stories', 'latest_stories_compact', 'author_stories'];
  $allowed_orderby = ['date', 'modified', 'title', 'meta_value', 'meta_value date', 'meta_value modified', 'meta_value title'];

  // Return if wrong query
  if (
    ! in_array( $vars['fictioneer_query_name'] ?? 0, $allowed_queries ) ||
    ! in_array( $vars['orderby'] ?? '', $allowed_orderby )
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
// LIST META QUERIES
// =============================================================================

/**
 * Adds 'fictioneer_chapter_hidden' to be saved falsy
 *
 * @since 5.9.4
 *
 * @param array $allowed  Array of allowed falsy meta fields.
 *
 * @return array The updated array.
 */

function fictioneer_allow_falsy_chapter_hidden( $allowed ) {
  $allowed[] = 'fictioneer_chapter_hidden';
  return $allowed;
}

if ( get_option( 'fictioneer_disable_extended_chapter_list_meta_queries' ) ) {
  add_filter( 'fictioneer_filter_falsy_meta_allow_list', 'fictioneer_allow_falsy_chapter_hidden' );
}

/**
 * Adds 'fictioneer_story_hidden' to be saved falsy
 *
 * @since 5.9.4
 *
 * @param array $allowed  Array of allowed falsy meta fields.
 *
 * @return array The updated array.
 */

function fictioneer_allow_falsy_story_hidden( $allowed ) {
  $allowed[] = 'fictioneer_story_hidden';
  return $allowed;
}

if ( get_option( 'fictioneer_disable_extended_story_list_meta_queries' ) ) {
  add_filter( 'fictioneer_filter_falsy_meta_allow_list', 'fictioneer_allow_falsy_story_hidden' );
}

?>
