<?php

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

      // Look for IDs to exclude if story or chapter...
      if ( in_array( $post_type, ['fcn_story', 'fcn_chapter'] ) && FICTIONEER_QUERY_ID_ARRAY_LIMIT > 0 ) {
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
            if ( get_post_meta( $card_post_id, 'fictioneer_story_hidden', true ) ) {
              get_template_part( 'partials/_card-hidden', null, $the_card_args );
            } else {
              get_template_part( 'partials/_card-story', null, $the_card_args );
            }
            break;
          case 'fcn_chapter':
            if (
              get_post_meta( $card_post_id, 'fictioneer_chapter_hidden', true ) ||
              get_post_meta( $card_post_id, 'fictioneer_chapter_no_chapter', true )
            ) {
              get_template_part( 'partials/_card-hidden', null, $the_card_args );
            } else {
              get_template_part( 'partials/_card-chapter', null, $the_card_args );
            }
            break;
          default:
            get_template_part( 'partials/_card-' . str_replace( 'fcn_', '', $post_type ), null, $the_card_args );
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
      $orderby = fictioneer_sanitize_query_var( $_GET['orderby'] ?? 0, fictioneer_allowed_orderby(), 'modified' );
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
// LIST META QUERIES
// =============================================================================

/**
 * Add 'fictioneer_chapter_hidden' to be saved falsy.
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
  $story_id = (int) ($story_id ?: 0);

  $wpdb->query(
    $wpdb->prepare(
      "UPDATE {$wpdb->posts} SET post_parent = %d WHERE ID = %d",
      $story_id ?: 0,
      $chapter_id
    )
  );
}

// =============================================================================
// SPECIFIC SQL QUERIES
// =============================================================================

if ( ! function_exists( 'fictioneer_sql_filter_valid_chapter_ids' ) ) {
  /**
   * Filter out non-valid chapter array IDs.
   *
   * Note: This is a lot faster than using WP_Query().
   *
   * @since 5.26.0
   *
   * @global wpdb $wpdb  WordPress database object.
   *
   * @param int   $story_id     Story ID.
   * @param int[] $chapter_ids  Array of chapter IDs.
   *
   * @return int[] Filtered and validated array of IDs.
   */

  function fictioneer_sql_filter_valid_chapter_ids( $story_id, $chapter_ids ) {
    global $wpdb;

    // Prepare
    $chapter_ids = is_array( $chapter_ids ) ? $chapter_ids : [ $chapter_ids ];
    $chapter_ids = array_map( 'intval', $chapter_ids );
    $chapter_ids = array_filter( $chapter_ids, function( $value ) { return $value > 0; } );

    if ( empty( $chapter_ids ) ) {
      return [];
    }

    $chapter_ids = array_unique( $chapter_ids );

    // Prepare placeholders and values
    $placeholders = implode( ',', array_fill( 0, count( $chapter_ids ), '%d' ) );
    $values = $chapter_ids;

    // Prepare SQL query
    $sql =
      "SELECT p.ID
      FROM {$wpdb->posts} p
      LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
      WHERE p.post_type = 'fcn_chapter'
        AND p.ID IN ($placeholders)
        AND p.post_status NOT IN ('trash', 'draft', 'auto-draft', 'inherit')";

    if ( defined('FICTIONEER_FILTER_STORY_CHAPTERS') && FICTIONEER_FILTER_STORY_CHAPTERS ) {
      $sql .= " AND pm.meta_key = %s AND pm.meta_value = %d";
      $values[] = 'fictioneer_chapter_story';
      $values[] = $story_id;
    }

    $query = $wpdb->prepare( $sql, ...$values );

    // Execute
    $filtered_ids = $wpdb->get_col( $query );

    // Restore order and return
    return array_values( array_intersect( $chapter_ids, $filtered_ids ) );
  }
}

if ( ! function_exists( 'fictioneer_sql_filter_valid_page_ids' ) ) {
  /**
   * Filter out non-valid story page array IDs.
   *
   * Note: This is a lot faster than using WP_Query().
   *
   * @since 5.26.0
   *
   * @global wpdb $wpdb  WordPress database object.
   *
   * @param int   $author_id  Author ID for the pages.
   * @param int[] $page_ids   Array of page IDs.
   *
   * @return int[] Filtered and validated array of IDs.
   */

  function fictioneer_sql_filter_valid_page_ids( $author_id, $page_ids ) {
    global $wpdb;

    // Prepare
    $page_ids = is_array( $page_ids ) ? $page_ids : [ $page_ids ];
    $page_ids = array_map( 'intval', $page_ids );
    $page_ids = array_filter( $page_ids, function( $value ) { return $value > 0; } );

    if ( empty( $page_ids ) || FICTIONEER_MAX_CUSTOM_PAGES_PER_STORY < 1 ) {
      return [];
    }

    $page_ids = array_unique( $page_ids );
    $page_ids = array_slice( $page_ids, 0, FICTIONEER_MAX_CUSTOM_PAGES_PER_STORY );

    // Prepare placeholders
    $placeholders = implode( ',', array_fill( 0, count( $page_ids ), '%d' ) );

    // Prepare SQL query
    $sql =
      "SELECT p.ID
      FROM {$wpdb->posts} p
      WHERE p.post_type = 'page'
        AND p.ID IN ($placeholders)
        AND p.post_author = %d
      LIMIT %d";

    $query = $wpdb->prepare(
      $sql,
      ...array_merge( $page_ids, [ $author_id, FICTIONEER_MAX_CUSTOM_PAGES_PER_STORY ] )
    );

    // Execute
    $filtered_page_ids = $wpdb->get_col( $query );

    // Restore order and return
    return array_values( array_intersect( $page_ids, $filtered_page_ids ) );
  }
}

if ( ! function_exists( 'fictioneer_sql_filter_valid_collection_ids' ) ) {
  /**
   * Filter out non-valid story page array IDs.
   *
   * Note: This is a lot faster than using WP_Query().
   *
   * @since 5.26.0
   *
   * @global wpdb $wpdb  WordPress database object.
   *
   * @param int[] $item_ids  Array of collection item IDs.
   *
   * @return int[] Filtered and validated array of IDs.
   */
  function fictioneer_sql_filter_valid_collection_ids( $item_ids ) {
    global $wpdb;

    // Prepare
    $item_ids = is_array( $item_ids ) ? $item_ids : [ $item_ids ];
    $item_ids = array_map( 'intval', $item_ids );
    $item_ids = array_filter( $item_ids, function( $value ) { return $value > 0; } );

    if ( empty( $item_ids ) ) {
      return [];
    }

    $item_ids = array_unique( $item_ids );

    // Exclude forbidden IDs
    $forbidden = array_unique([
      get_option( 'fictioneer_user_profile_page', 0 ),
      get_option( 'fictioneer_bookmarks_page', 0 ),
      get_option( 'fictioneer_stories_page', 0 ),
      get_option( 'fictioneer_chapters_page', 0 ),
      get_option( 'fictioneer_recommendations_page', 0 ),
      get_option( 'fictioneer_collections_page', 0 ),
      get_option( 'fictioneer_bookshelf_page', 0 ),
      get_option( 'fictioneer_authors_page', 0 ),
      get_option( 'fictioneer_404_page', 0 ),
      get_option( 'page_on_front', 0 ),
      get_option( 'page_for_posts', 0 )
    ]);

    $item_ids = array_diff( $item_ids, array_map( 'intval', $forbidden ) );

    if ( empty( $item_ids ) ) {
      return [];
    }

    // Prepare placeholders
    $placeholders = implode( ',', array_fill( 0, count( $item_ids ), '%d' ) );

    // Prepare SQL query
    $sql =
      "SELECT p.ID
      FROM {$wpdb->posts} p
      WHERE p.ID IN ($placeholders)
        AND p.post_type IN ('post', 'page', 'fcn_story', 'fcn_chapter', 'fcn_collection', 'fcn_recommendation')
        AND p.post_status IN ('publish', 'private', 'future')";

    // Execute
    $filtered_item_ids = $wpdb->get_col( $wpdb->prepare( $sql, ...$item_ids ) );

    // Restore order and return
    return array_values( array_intersect( $item_ids, $filtered_item_ids ) );
  }
}

if ( ! function_exists( 'fictioneer_sql_filter_valid_featured_ids' ) ) {
  /**
   * Filter out non-valid featured array IDs.
   *
   * Note: This is a lot faster than using WP_Query().
   *
   * @since 5.26.0
   *
   * @global wpdb $wpdb  WordPress database object.
   *
   * @param int[] $post_ids  Array of featured post IDs.
   *
   * @return int[] Filtered and validated array of IDs.
   */
  function fictioneer_sql_filter_valid_featured_ids( $post_ids ) {
    global $wpdb;

    // Prepare
    $post_ids = is_array( $post_ids ) ? $post_ids : [ $post_ids ];
    $post_ids = array_map( 'intval', $post_ids );
    $post_ids = array_filter( $post_ids, function( $value ) { return $value > 0; } );

    if ( empty( $post_ids ) ) {
      return [];
    }

    $post_ids = array_unique( $post_ids );

    // Prepare placeholders
    $placeholders = implode( ',', array_fill( 0, count( $post_ids ), '%d' ) );

    // Prepare SQL query
    $sql =
      "SELECT p.ID
      FROM {$wpdb->posts} p
      WHERE p.ID IN ($placeholders)
        AND p.post_type IN ('post', 'fcn_story', 'fcn_chapter', 'fcn_collection', 'fcn_recommendation')
        AND p.post_status = 'publish'";

    // Execute
    $filtered_ids = $wpdb->get_col( $wpdb->prepare( $sql, ...$post_ids ) );

    // Restore order and return
    return array_values( array_intersect( $post_ids, $filtered_ids ) );
  }
}

if ( ! function_exists( 'fictioneer_sql_filter_valid_blog_story_ids' ) ) {
  /**
   * Filter out non-valid blog story array IDs.
   *
   * Note: This is a lot faster than using WP_Query().
   *
   * @since 5.26.0
   * @since 5.30.0 - Refactored for optional author.
   *
   * @global wpdb $wpdb  WordPress database object.
   *
   * @param int[]    $story_blogs      Array of story blog IDs.
   * @param int|null $story_author_id  Optional. Author ID of the story.
   *
   * @return int[] Filtered and validated array of IDs.
   */

  function fictioneer_sql_filter_valid_blog_story_ids( $story_blogs, $story_author_id = null ) {
    global $wpdb;

    // Prepare
    $story_blogs = is_array( $story_blogs ) ? $story_blogs : [ $story_blogs ];
    $story_blogs = array_map( 'intval', $story_blogs );
    $story_blogs = array_filter( $story_blogs, function( $value ) { return $value > 0; } );

    if ( empty( $story_blogs ) ) {
      return [];
    }

    $story_blogs = array_unique( $story_blogs );

    // Prepare placeholders
    $placeholders = implode( ',', array_fill( 0, count( $story_blogs ), '%d' ) );

    // Prepare SQL query
    $where_author = $story_author_id !== null ? 'AND p.post_author = %d' : '';
    $sql = "
      SELECT p.ID
      FROM {$wpdb->posts} p
      WHERE p.ID IN ($placeholders)
        $where_author
        AND p.post_type = 'fcn_story'
        AND p.post_status IN ('publish', 'private', 'future')
    ";

    // Prepare arguments
    $args = $story_author_id !== null
      ? array_merge( $story_blogs, [ $story_author_id ] )
      : $story_blogs;

    // Execute and return
    return $wpdb->get_col( $wpdb->prepare( $sql, ...$args ) );
  }
}

if ( ! function_exists( 'fictioneer_sql_has_new_story_chapters' ) ) {
  /**
   * Check whether there any added chapters are to be considered "new".
   *
   * @since 5.26.0
   *
   * @global wpdb $wpdb  WordPress database object.
   *
   * @param int   $story_id              Story ID.
   * @param int[] $chapter_ids           Current array of chapter IDs.
   * @param int[] $previous_chapter_ids  Previous array of chapter IDs.
   *
   * @return bool True or false.
   */
  function fictioneer_sql_has_new_story_chapters( $story_id, $chapter_ids, $previous_chapter_ids ) {
    global $wpdb;

    // Any chapters added?
    $chapter_diff = array_diff( $chapter_ids, $previous_chapter_ids );

    if ( empty( $chapter_diff ) ) {
      return;
    }

    // Filter allowed statuses
    $allowed_statuses = apply_filters(
      'fictioneer_filter_chapters_added_statuses',
      ['publish'],
      $story_id
    );

    // Prepare placeholders for IN clauses
    $chapter_placeholders = implode( ',', array_fill( 0, count( $chapter_diff ), '%d' ) );
    $status_placeholders = implode( ',', array_fill( 0, count( $allowed_statuses ), '%s' ) );

    // Prepare SQL query
    $sql =
      "SELECT p.ID
      FROM {$wpdb->posts} p
      LEFT JOIN {$wpdb->postmeta} pm_hidden ON p.ID = pm_hidden.post_id
      WHERE p.post_type = 'fcn_chapter'
        AND p.ID IN ($chapter_placeholders)
        AND p.post_status IN ($status_placeholders)
        AND (pm_hidden.meta_key != 'fictioneer_chapter_hidden' OR pm_hidden.meta_value IS NULL)
      LIMIT 1";

    $query = $wpdb->prepare( $sql, array_merge( $chapter_diff, $allowed_statuses ) );

    // Execute the query to check for new valid chapters
    $new_chapters = $wpdb->get_col( $query );

    // Report
    return ! empty( $new_chapters );
  }
}

if ( ! function_exists( 'fictioneer_sql_get_co_authored_story_ids' ) ) {
  /**
   * Return story IDs where the user is a co-author.
   *
   * @since 5.26.0
   *
   * @global wpdb $wpdb  WordPress database object.
   *
   * @param int $author_id  User ID.
   *
   * @return int[] Array of story IDs.
   */

  function fictioneer_sql_get_co_authored_story_ids( $author_id ) {
    static $cache = [];

    if ( isset( $cache[ $author_id ] ) ) {
      return $cache[ $author_id ];
    }

    global $wpdb;

    $story_ids = $wpdb->get_col(
      $wpdb->prepare(
        "SELECT post_id
        FROM {$wpdb->postmeta}
        WHERE meta_key = 'fictioneer_story_co_authors'
        AND meta_value LIKE %s",
        '%:"' . $author_id . '";%'
      )
    );

    $story_ids = apply_filters( 'fictioneer_filter_co_authored_ids', $story_ids, $author_id );

    $cache[ $author_id ] = $story_ids;

    return $story_ids;
  }
}

if ( ! function_exists( 'fictioneer_sql_get_chapter_story_selection' ) ) {
  /**
   * Return selectable stories for chapter assignments.
   *
   * @since 5.26.0
   *
   * @global wpdb $wpdb  WordPress database object.
   *
   * @param int $post_author_id     Author ID of the current post.
   * @param int $current_story_id   ID of the currently selected story (if any).
   *
   * @return array Associative array with 'stories' (array) and 'other_author' (bool).
   */

  function fictioneer_sql_get_chapter_story_selection( $post_author_id, $current_story_id = 0 ) {
    global $wpdb;

    // Setup
    $stories = array( '0' => _x( '— Unassigned —', 'Chapter story select option.', 'fictioneer' ) );
    $co_authored_stories = [];
    $other_author = false;
    $co_author = false;

    // Prepare SQL query
    $values = [];

    $sql =
      "SELECT p.ID, p.post_title, p.post_status, p.post_date, p.post_author
      FROM {$wpdb->posts} p
      WHERE p.post_type = 'fcn_story'
        AND p.post_status IN ('publish', 'private')";

    if ( get_option( 'fictioneer_limit_chapter_stories_by_author' ) ) {
      $sql .= " AND p.post_author = %d";
      $values[] = $post_author_id;

      $co_authored_stories = fictioneer_sql_get_co_authored_story_ids( $post_author_id );

      if ( ! empty( $co_authored_stories ) ) {
        $placeholders = implode( ',', array_fill( 0, count( $co_authored_stories ), '%d' ) );
        $sql .= " OR p.ID IN ($placeholders)";
        $values = array_merge( $values, $co_authored_stories );
        $co_author = true;
      }
    }

    $sql .= " ORDER BY p.post_date DESC";

    // Execute
    if ( empty( $values ) ) {
      $results = $wpdb->get_results( $sql );
    } else {
      $results = $wpdb->get_results( $wpdb->prepare( $sql, ...$values ) );
    }

    // Populate the stories array
    foreach ( $results as $story ) {
      $title = fictioneer_sanitize_safe_title(
        $story->post_title,
        mysql2date( get_option( 'date_format' ), $story->post_date ),
        mysql2date( get_option( 'time_format' ), $story->post_date )
      );
      $suffix = [];

      if ( $story->post_status !== 'publish' ) {
        $suffix['status'] = fictioneer_get_post_status_label( $story->post_status );
      }

      if ( in_array( $story->ID, $co_authored_stories ) ) {
        $suffix['co-authored'] = __( 'Co-Author', 'fictioneer' );
      }

      if ( empty( $suffix ) ) {
        $stories[ $story->ID ] = $title;
      } else {
        $stories[ $story->ID ] = sprintf(
          _x( '%1$s (%2$s)', 'Chapter story meta field option with notes.', 'fictioneer' ),
          $title,
          implode( ' | ', $suffix )
        );
      }
    }

    // Check for deviating assignment...
    if ( $current_story_id && ! array_key_exists( $current_story_id, $stories ) ) {
      $other_author_id = get_post_field( 'post_author', $current_story_id );
      $suffix = [];

      // Other author
      if ( $other_author_id != $post_author_id ) {
        $other_author = true;
        $suffix['author'] = get_the_author_meta( 'display_name', $other_author_id );
      }

      // Other status
      if ( get_post_status( $current_story_id ) !== 'publish' ) {
        $suffix['status'] = fictioneer_get_post_status_label( get_post_status( $current_story_id ) );
      }

      // Append to selection
      $stories[ $current_story_id ] = sprintf(
        _x( '%1$s (%2$s)', 'Chapter story meta field mismatched option with notes.', 'fictioneer' ),
        fictioneer_get_safe_title( $current_story_id, 'admin-render-chapter-data-metabox-current-suffix' ),
        ! empty( $suffix ) ? implode( ' | ', $suffix ) : ''
      );
    }

    return array(
      'stories' => $stories,
      'other_author' => $other_author,
      'co_author' => $co_author
    );
  }
}

if ( ! function_exists( 'fictioneer_sql_get_story_chapter_relationship_data' ) ) {
  /**
   * Return chapter objects for a story.
   *
   * @since 5.26.0
   *
   * @global wpdb $wpdb  WordPress database object.
   *
   * @param int $story_id  Story ID.
   *
   * @return object[] Array of chapter data object similar to WP_Post.
   */

  function fictioneer_sql_get_story_chapter_relationship_data( $story_id ) {
    global $wpdb;

    // Setup
    $chapter_ids = fictioneer_get_story_chapter_ids( $story_id );

    // Empty?
    if ( empty( $chapter_ids ) ) {
      return [];
    }

    // Prepare SQL query
    $placeholders = implode( ',', array_fill( 0, count( $chapter_ids ), '%d' ) );
    $values = array_merge( $chapter_ids, [ $story_id ] );

    $sql = $wpdb->prepare(
      "SELECT p.ID as ID, p.post_title as post_title, p.post_status as post_status, p.post_date_gmt as post_date_gmt,
        pm_text_icon.meta_value as fictioneer_chapter_text_icon,
        pm_icon.meta_value as fictioneer_chapter_icon,
        pm_rating.meta_value as fictioneer_chapter_rating,
        pm_warning.meta_value as fictioneer_chapter_warning,
        pm_group.meta_value as fictioneer_chapter_group,
        pm_hidden.meta_value as fictioneer_chapter_hidden,
        pm_no_chapter.meta_value as fictioneer_chapter_no_chapter
      FROM {$wpdb->posts} p
      LEFT JOIN {$wpdb->postmeta} pm_text_icon ON (p.ID = pm_text_icon.post_id AND pm_text_icon.meta_key = 'fictioneer_chapter_text_icon')
      LEFT JOIN {$wpdb->postmeta} pm_icon ON (p.ID = pm_icon.post_id AND pm_icon.meta_key = 'fictioneer_chapter_icon')
      LEFT JOIN {$wpdb->postmeta} pm_rating ON (p.ID = pm_rating.post_id AND pm_rating.meta_key = 'fictioneer_chapter_rating')
      LEFT JOIN {$wpdb->postmeta} pm_warning ON (p.ID = pm_warning.post_id AND pm_warning.meta_key = 'fictioneer_chapter_warning')
      LEFT JOIN {$wpdb->postmeta} pm_group ON (p.ID = pm_group.post_id AND pm_group.meta_key = 'fictioneer_chapter_group')
      LEFT JOIN {$wpdb->postmeta} pm_hidden ON (p.ID = pm_hidden.post_id AND pm_hidden.meta_key = 'fictioneer_chapter_hidden')
      LEFT JOIN {$wpdb->postmeta} pm_no_chapter ON (p.ID = pm_no_chapter.post_id AND pm_no_chapter.meta_key = 'fictioneer_chapter_no_chapter')
      WHERE p.post_type = 'fcn_chapter'
        AND p.ID IN ($placeholders)
        AND EXISTS (
          SELECT 1
          FROM {$wpdb->postmeta} pm
          WHERE pm.post_id = p.ID AND pm.meta_key = 'fictioneer_chapter_story' AND pm.meta_value = %d
        )
      ",
      ...$values
    );

    // Execute
    $results = $wpdb->get_results( $sql );

    // Restore order and return
    $chapter_map = array_flip( $chapter_ids );

    usort( $results, function( $a, $b ) use ( $chapter_map ) {
      return $chapter_map[ $a->ID ] <=> $chapter_map[ $b->ID ];
    });

    return $results;
  }
}

/**
 * Update the comment count of a post.
 *
 * @since 5.26.0
 *
 * @global wpdb $wpdb  WordPress database object.
 *
 * @param int $post_id  Post ID.
 * @param int $count    Comment count.
 */

function fictioneer_sql_update_comment_count( $post_id, $count ) {
  global $wpdb;

  $wpdb->update(
    $wpdb->posts,
    array( 'comment_count' => $count ),
    array( 'ID' => $post_id ),
    ['%d'],
    ['%d']
  );
}
