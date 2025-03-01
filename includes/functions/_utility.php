<?php

// =============================================================================
// CHECK FOR LOCAL DEV ENVIRONMENT
// =============================================================================

/**
 * Checks whether the site runs on a local environment
 *
 * @since 5.25.0
 *
 * @return bool True or false.
 */

function fictioneer_is_local_environment() {
  $local_hosts = ['localhost', '127.0.0.1', '::1'];

  if ( in_array( $_SERVER['HTTP_HOST'], $local_hosts ) || in_array( $_SERVER['SERVER_ADDR'], $local_hosts ) ) {
    return true;
  }

  return false;
}

// =============================================================================
// CHECK IF URL EXISTS
// =============================================================================

if ( ! function_exists( 'fictioneer_url_exists' ) ) {
  /**
   * Checks whether an URL exists
   *
   * @since 4.0.0
   * @link https://www.geeksforgeeks.org/how-to-check-the-existence-of-url-in-php/
   *
   * @param string $url  The URL to check.
   *
   * @return boolean True if the URL exists and false otherwise. Probably.
   */

  function fictioneer_url_exists( $url ) {
    if ( empty( $url ) ) {
      return false;
    }

    $response = wp_remote_head( $url );

    if ( is_wp_error( $response ) ) {
      return false;
    }

    $statusCode = wp_remote_retrieve_response_code( $response );

    // Check for 2xx status codes which indicate success
    return ( $statusCode >= 200 && $statusCode < 300 );
  }
}

// =============================================================================
// CHECK WHETHER VALID JSON
// =============================================================================

/**
 * Check whether a JSON is valid
 *
 * @since 4.0.0
 * @since 5.21.1 - Use json_validate() if on PHP 8.3 or higher.
 *
 * @param string $data  JSON string hopeful.
 *
 * @return boolean True if the JSON is valid, false if not.
 */

function fictioneer_is_valid_json( $data = null ) {
  if ( empty( $data ) ) {
    return false;
  }

  // PHP 8.3 or higher
  if ( function_exists( 'json_validate' ) ) {
    return json_validate( $data );
  }

  $data = @json_decode( $data, true );

  return ( json_last_error() === JSON_ERROR_NONE );
}

// =============================================================================
// CHECK FOR ACTIVE PLUGINS
// =============================================================================

/**
 * Checks whether a plugin is active for the entire network
 *
 * @since 5.20.2
 * @link https://developer.wordpress.org/reference/functions/is_plugin_active_for_network/
 *
 * @param string $path  Relative path to the plugin.
 *
 * @return boolean True if the plugin is active, otherwise false.
 */

function fictioneer_is_network_plugin_active( $path ) {
  if ( ! is_multisite() ) {
    return false;
  }

  $plugins = get_site_option( 'active_sitewide_plugins' );

  if ( isset( $plugins[ $path ] ) ) {
    return true;
  }

  return false;
}

/**
 * Checks whether a plugin is active
 *
 * @since 4.0.0
 * @since 5.20.2 - Changed to copy of is_plugin_active().
 * @link https://developer.wordpress.org/reference/functions/is_plugin_active/
 *
 * @param string $path  Relative path to the plugin.
 *
 * @return boolean True if the plugin is active, otherwise false.
 */

function fictioneer_is_plugin_active( $path ) {
  return in_array( $path, (array) get_option( 'active_plugins', [] ), true ) || fictioneer_is_network_plugin_active( $path );
}

if ( ! function_exists( 'fictioneer_seo_plugin_active' ) ) {
  /**
   * Checks whether any SEO plugin known to the theme is active
   *
   * The theme's SEO features are inherently incompatible with SEO plugins, which
   * may be more sophisticated but do not understand the theme's content structure.
   * At all. Regardless, if the user want to use a SEO plugin, it's better to turn
   * off the theme's own SEO features. This function detects some of them.
   *
   * @since 4.0.0
   *
   * @return boolean True if a known SEO is active, otherwise false.
   */

  function fictioneer_seo_plugin_active() {
    return fictioneer_is_plugin_active( 'wordpress-seo/wp-seo.php' ) ||
      fictioneer_is_plugin_active( 'wordpress-seo-premium/wp-seo-premium.php' ) ||
      function_exists( 'aioseo' );
  }
}

// =============================================================================
// GET USER BY ID OR EMAIL
// =============================================================================

if ( ! function_exists( 'fictioneer_get_user_by_id_or_email' ) ) {
  /**
   * Get user by ID or email
   *
   * @since 4.6.0
   *
   * @param int|string $id_or_email  User ID or email address.
   *
   * @return WP_User|boolean Returns the user or false if not found.
   */

  function fictioneer_get_user_by_id_or_email( $id_or_email ) {
    $user = false;

    if ( is_numeric( $id_or_email ) ) {
      $id = (int) $id_or_email;
      $user = get_user_by( 'id' , $id );
    } elseif ( is_object( $id_or_email ) ) {
      if ( ! empty( $id_or_email->user_id ) ) {
        $id = (int) $id_or_email->user_id;
        $user = get_user_by( 'id' , $id );
      }
    } else {
      $user = get_user_by( 'email', $id_or_email );
    }

    return $user;
  }
}

// =============================================================================
// GET LAST CHAPTER/STORY UPDATE
// =============================================================================

if ( ! function_exists( 'fictioneer_get_last_fiction_update' ) ) {
  /**
   * Get Unix timestamp for last story or chapter update
   *
   * @since 5.0.0
   *
   * @return int The timestamp in milliseconds.
   */

  function fictioneer_get_last_fiction_update() {
    $last_update = get_option( 'fictioneer_story_or_chapter_updated_timestamp' );

    if ( empty( $last_update ) ) {
      $last_update = time() * 1000;
      update_option( 'fictioneer_story_or_chapter_updated_timestamp', $last_update );
    }

    return $last_update;
  }
}

// =============================================================================
// GET STORY CHAPTERS
// =============================================================================

/**
 * Returns array of chapter posts for a story
 *
 * @since 5.9.2
 * @since 5.22.3 - Refactored.
 *
 * @param int   $story_id  ID of the story.
 * @param array $args      Optional. Additional query arguments.
 * @param bool  $full      Optional. Whether to not reduce the posts. Default false.
 *
 * @return array Array of chapter posts or empty.
 */

function fictioneer_get_story_chapter_posts( $story_id, $args = [], $full = false ) {
  // Static variable cache
  static $cached_results = [];

  // Setup
  $chapter_ids = fictioneer_get_story_chapter_ids( $story_id );

  // No chapters?
  if ( empty( $chapter_ids ) ) {
    return [];
  }

  // Query arguments
  $query_args = array(
    'fictioneer_query_name' => 'get_story_chapter_posts',
    'post_type' => 'fcn_chapter',
    'post_status' => 'publish',
    'ignore_sticky_posts' => true,
    'posts_per_page' => -1,
    'no_found_rows' => true, // Improve performance
    'update_post_term_cache' => false // Improve performance
  );

  // Apply filters and custom arguments
  $query_args = array_merge( $query_args, $args );
  $query_args = apply_filters( 'fictioneer_filter_story_chapter_posts_query', $query_args, $story_id, $chapter_ids );

  // Static cache key
  $cache_key = $story_id . '_' . md5( serialize( $query_args ) );

  // Static cache hit?
  if ( isset( $cached_results[ $cache_key ] ) ) {
    return $cached_results[ $cache_key ];
  }

  // Batched or one go?
  if ( count( $chapter_ids ) <= FICTIONEER_QUERY_ID_ARRAY_LIMIT ) {
    $query_args['post__in'] = $chapter_ids ?: [0];
    $chapter_query = new WP_Query( $query_args );
    $chapter_posts = $chapter_query->posts;
  } else {
    $chapter_posts = [];
    $batches = array_chunk( $chapter_ids, FICTIONEER_QUERY_ID_ARRAY_LIMIT );

    foreach ( $batches as $batch ) {
      $query_args['post__in'] = $batch ?: [0];
      $chapter_query = new WP_Query( $query_args );
      $chapter_posts = array_merge( $chapter_posts, $chapter_query->posts );
    }
  }

  // Restore order
  $chapter_positions = array_flip( $chapter_ids );

  usort( $chapter_posts, function( $a, $b ) use ( $chapter_positions ) {
    return $chapter_positions[ $a->ID ] - $chapter_positions[ $b->ID ];
  });

  // Return chapters selected in story
  return $chapter_posts;
}

// =============================================================================
// GROUP CHAPTERS
// =============================================================================

/**
 * Groups and prepares chapters for a specific story
 *
 * Note: If chapter groups are disabled, all chapters will be
 * within the 'all_chapters' group.
 *
 * @since 5.25.0
 *
 * @param int   $story_id  ID of the story.
 * @param array $chapters  Array of (reduced) WP_Post objects.
 *
 * @return array The grouped and prepared chapters.
 */

function fictioneer_prepare_chapter_groups( $story_id, $chapters ) {
  // Any chapters?
  if ( empty( $chapters ) ) {
    return [];
  }

  // Setup
  $chapter_groups = [];
  $allowed_permalinks = apply_filters( 'fictioneer_filter_allowed_chapter_permalinks', ['publish'] );
  $enable_groups = get_option( 'fictioneer_enable_chapter_groups' ) &&
    ! get_post_meta( $story_id, 'fictioneer_story_disable_groups', true );

  // Loop chapters...
  foreach ( $chapters as $post ) {
    $chapter_id = $post->ID;

    // Skip missing or not visible chapters
    if ( ! $post || get_post_meta( $chapter_id, 'fictioneer_chapter_hidden', true ) ) {
      continue;
    }

    // Data
    $group = get_post_meta( $chapter_id, 'fictioneer_chapter_group', true );
    $group = empty( $group ) ? fcntr( 'unassigned_group' ) : $group;
    $group = $enable_groups ? $group : 'all_chapters';
    $group_key = sanitize_title( $group );

    if ( ! array_key_exists( $group_key, $chapter_groups ) ) {
      $chapter_groups[ $group_key ] = array(
        'group' => $group,
        'toggle_icon' => 'fa-solid fa-chevron-down',
        'data' => [],
        'count' => 0,
        'classes' => array(
          '_group-' . sanitize_title( $group ),
          "_story-{$story_id}"
        )
      );
    }

    $chapter_groups[ $group_key ]['data'][] = array(
      'id' => $chapter_id,
      'story_id' => $story_id,
      'status' => $post->post_status,
      'link' => in_array( $post->post_status, $allowed_permalinks ) ? get_permalink( $post->ID ) : '',
      'timestamp' => get_the_time( 'U', $post ),
      'password' => ! empty( $post->post_password ),
      'list_date' => get_the_date( '', $post ),
      'grid_date' => get_the_time( get_option( 'fictioneer_subitem_date_format', "M j, 'y" ) ?: "M j, 'y", $post ),
      'icon' => fictioneer_get_icon_field( 'fictioneer_chapter_icon', $chapter_id ),
      'text_icon' => get_post_meta( $chapter_id, 'fictioneer_chapter_text_icon', true ),
      'prefix' => get_post_meta( $chapter_id, 'fictioneer_chapter_prefix', true ),
      'title' => fictioneer_get_safe_title( $chapter_id, 'story-chapter-list' ),
      'list_title' => get_post_meta( $chapter_id, 'fictioneer_chapter_list_title', true ),
      'words' => fictioneer_get_word_count( $chapter_id ),
      'warning' => get_post_meta( $chapter_id, 'fictioneer_chapter_warning', true )
    );

    $chapter_groups[ $group_key ]['count'] += 1;
  }

  return $chapter_groups;
}

// =============================================================================
// GET STORY DATA
// =============================================================================

if ( ! function_exists( 'fictioneer_get_story_data' ) ) {
  /**
   * Get collection of a story's data
   *
   * @since 4.3.0
   * @since 5.25.0 - Refactored with custom SQL query.
   *
   * @param int     $story_id       ID of the story.
   * @param boolean $show_comments  Optional. Whether the comment count is needed.
   *                                Default true.
   * @param array   $args           Optional array of arguments.
   *
   * @return array|boolean Data of the story or false if invalid.
   */

  function fictioneer_get_story_data( $story_id, $show_comments = true, $args = [] ) {
    global $wpdb;

    $story_id = fictioneer_validate_id( $story_id, 'fcn_story' );
    $meta_cache = null;

    if ( empty( $story_id ) ) {
      return false;
    }

    // Meta cache (purged on update)?
    if ( FICTIONEER_ENABLE_STORY_DATA_META_CACHE ) {
      $meta_cache = get_post_meta( $story_id, 'fictioneer_story_data_collection', true );
    }

    if ( $meta_cache && ( $meta_cache['last_modified'] ?? 0 ) >= get_the_modified_time( 'U', $story_id ) ) {
      // Return cached data without refreshing the comment count
      if ( ! $show_comments ) {
        return $meta_cache;
      }

      // Time to refresh comment count?
      $comment_count_delay = ( $meta_cache['comment_count_timestamp'] ?? 0 ) + FICTIONEER_STORY_COMMENT_COUNT_TIMEOUT;
      $refresh_comments = $comment_count_delay < time() || ( $args['refresh_comment_count'] ?? 0 );

      // Refresh comment count
      if ( $refresh_comments ) {
        // Use old count as fallback
        $comment_count = $meta_cache['comment_count'];

        if ( count( $meta_cache['chapter_ids'] ) > 0 ) {
          $comment_count = fictioneer_get_story_comment_count( $story_id, $meta_cache['chapter_ids'] );
        }

        $meta_cache['comment_count'] = $comment_count;
        $meta_cache['comment_count_timestamp'] = time();

        // Update post database comment count
        $story_comment_count = get_approved_comments( $story_id, array( 'count' => true ) ) ?: 0;
        fictioneer_sql_update_comment_count( $story_id, $comment_count + $story_comment_count );

        // Update meta cache and purge
        update_post_meta( $story_id, 'fictioneer_story_data_collection', $meta_cache );

        if ( function_exists( 'fictioneer_purge_post_cache' ) ) {
          fictioneer_purge_post_cache( $story_id );
        }
      }

      // Return cached data
      return $meta_cache;
    }

    // Setup
    $tags = get_the_tags( $story_id );
    $fandoms = get_the_terms( $story_id, 'fcn_fandom' );
    $characters = get_the_terms( $story_id, 'fcn_character' );
    $warnings = get_the_terms( $story_id, 'fcn_content_warning' );
    $genres = get_the_terms( $story_id, 'fcn_genre' );
    $status = get_post_meta( $story_id, 'fictioneer_story_status', true );
    $icon = 'fa-solid fa-circle';
    $chapter_count = 0;
    $word_count = 0;
    $comment_count = 0;
    $visible_chapter_ids = [];
    $indexed_chapter_ids = [];

    // Assign correct icon
    if ( $status != 'Ongoing' ) {
      switch ( $status ) {
        case 'Completed':
          $icon = 'fa-solid fa-circle-check';
          break;
        case 'Oneshot':
          $icon = 'fa-solid fa-circle-check';
          break;
        case 'Hiatus':
          $icon = 'fa-solid fa-circle-pause';
          break;
        case 'Canceled':
          $icon = 'fa-solid fa-ban';
          break;
      }
    }

    // Custom SQL query to count chapters, words, comments, etc.
    // This significantly faster than WP_Query (up to 15 times with 500 chapters)
    $queried_statuses = apply_filters( 'fictioneer_filter_get_story_data_queried_chapter_statuses', ['publish'], $story_id );
    $indexed_statuses = apply_filters( 'fictioneer_filter_get_story_data_indexed_chapter_statuses', ['publish'], $story_id );
    $chapter_ids = fictioneer_get_story_chapter_ids( $story_id );
    $chapters = [];

    if ( ! empty( $chapter_ids ) ) {
      $chapter_ids_placeholder = implode( ',', array_fill( 0, count( $chapter_ids ), '%d' ) );

      $status_list = array_map( function( $status ) use ( $wpdb ) {
        return $wpdb->prepare( '%s', $status );
      }, $queried_statuses );

      $status_list = implode( ',', $status_list );

      $query = $wpdb->prepare(
        "SELECT
          c.ID as chapter_id,
          c.comment_count,
          SUM(CASE WHEN pm.meta_key = '_word_count' THEN CAST(pm.meta_value AS UNSIGNED) ELSE 0 END) AS word_count,
          MAX(CASE WHEN pm.meta_key = 'fictioneer_chapter_hidden' THEN pm.meta_value ELSE '' END) AS is_hidden,
          MAX(CASE WHEN pm.meta_key = 'fictioneer_chapter_no_chapter' THEN pm.meta_value ELSE '' END) AS is_no_chapter,
          c.post_status
        FROM {$wpdb->posts} c
        LEFT JOIN {$wpdb->postmeta} pm ON pm.post_id = c.ID
        WHERE c.ID IN ($chapter_ids_placeholder)
          AND c.post_status IN ($status_list)
        GROUP BY c.ID",
        ...$chapter_ids // WHERE clause
      );

      $query = apply_filters( 'fictioneer_filter_get_story_data_sql', $query, $story_id, $chapter_ids, $queried_statuses );

      $chapters = $wpdb->get_results( $query );

      usort( $chapters, function( $a, $b ) use ( $chapter_ids ) {
        $position_a = array_search( $a->chapter_id, $chapter_ids );
        $position_b = array_search( $b->chapter_id, $chapter_ids );
        return $position_a - $position_b;
      });
    }

    foreach ( $chapters as $chapter ) {
      if ( empty( $chapter->is_hidden ) ) {
        // Do not count non-chapters...
        if ( empty( $chapter->is_no_chapter ) ) {
          $chapter_count++;
          $word_count += intval( $chapter->word_count );
        }

        // ... but they are still listed!
        $visible_chapter_ids[] = $chapter->chapter_id;

        // Indexed chapters (accounts for custom filters)
        if ( in_array( $chapter->post_status, $indexed_statuses ) ) {
          $indexed_chapter_ids[] = $chapter->chapter_id;
        }
      }

      // Count ALL comments
      $comment_count += intval( $chapter->comment_count );
    }

    // Add story word count
    $word_count += get_post_meta( $story_id, '_word_count', true );

    // Customize word count
    $modified_word_count = fictioneer_multiply_word_count( $word_count );

    // Rating
    $rating = get_post_meta( $story_id, 'fictioneer_story_rating', true ) ?: 'Everyone';

    // Prepare result
    $result = array(
      'id' => $story_id,
      'chapter_count' => $chapter_count,
      'word_count' => $modified_word_count,
      'word_count_short' => fictioneer_shorten_number( $modified_word_count ),
      'status' => $status,
      'icon' => $icon,
      'has_taxonomies' => $fandoms || $characters || $genres,
      'tags' => $tags,
      'characters' => $characters,
      'fandoms' => $fandoms,
      'warnings' => $warnings,
      'genres' => $genres,
      'title' => fictioneer_get_safe_title( $story_id, 'utility-get-story-data' ),
      'rating' => $rating,
      'rating_letter' => $rating[0],
      'chapter_ids' => $visible_chapter_ids,
      'indexed_chapter_ids' => $indexed_chapter_ids,
      'last_modified' => get_the_modified_time( 'U', $story_id ),
      'comment_count' => $comment_count,
      'comment_count_timestamp' => time(),
      'redirect' => get_post_meta( $story_id, 'fictioneer_story_redirect_link', true )
    );

    // Update meta cache if enabled
    if ( FICTIONEER_ENABLE_STORY_DATA_META_CACHE ) {
      update_post_meta( $story_id, 'fictioneer_story_data_collection', $result );
    }

    // Update story total word count
    update_post_meta( $story_id, 'fictioneer_story_total_word_count', $word_count );

    // Update post database comment count
    $story_comment_count = get_approved_comments( $story_id, array( 'count' => true ) ) ?: 0;
    fictioneer_sql_update_comment_count( $story_id, $comment_count + $story_comment_count );

    // Done
    return $result;
  }
}

/**
 * Returns the comment count of all story chapters
 *
 * Note: Includes hidden and non-chapter posts.
 *
 * @since 5.22.2
 * @since 5.22.3 - Switched to SQL query.
 *
 * @param int        $story_id     ID of the story.
 * @param array|null $chapter_ids  Optional. Array of chapter IDs.
 *
 * @return int Number of comments.
 */

function fictioneer_get_story_comment_count( $story_id, $chapter_ids = null ) {
  // Setup
  $comment_count = 0;
  $chapter_ids = $chapter_ids ?? fictioneer_get_story_chapter_ids( $story_id );

  // No chapters?
  if ( empty( $chapter_ids ) ) {
    return 0;
  }

  // SQL
  global $wpdb;

  $chunks = array_chunk( $chapter_ids, FICTIONEER_QUERY_ID_ARRAY_LIMIT );

  foreach ( $chunks as $chunk ) {
    $placeholders = implode( ',', array_fill( 0, count( $chunk ), '%d' ) );
    $query = $wpdb->prepare("
      SELECT COUNT(comment_ID)
      FROM {$wpdb->comments} c
      INNER JOIN {$wpdb->posts} p ON c.comment_post_ID = p.ID
      WHERE p.post_type = 'fcn_chapter'
      AND p.ID IN ($placeholders)
      AND c.comment_approved = '1'
    ", ...$chunk );

    $comment_count += $wpdb->get_var( $query );
  }

  // Return result
  return $comment_count;
}

// =============================================================================
// GET AUTHOR STATISTICS
// =============================================================================

if ( ! function_exists( 'fictioneer_get_author_statistics' ) ) {
  /**
   * Returns an author's statistics.
   *
   * @since 4.6.0
   * @since 5.27.4 - Optimized.
   *
   * @param int $author_id  User ID of the author.
   *
   * @return array|false Array of statistics or false if user does not exist.
   */

  function fictioneer_get_author_statistics( $author_id ) {
    global $wpdb;

    // Validate
    $author_id = fictioneer_validate_id( $author_id );

    if ( ! $author_id || ! get_user_by( 'id', $author_id ) ) {
      return false;
    }

    // Meta cache?
    if ( FICTIONEER_ENABLE_AUTHOR_STATS_META_CACHE ) {
      $meta_cache = get_user_meta( $author_id, 'fictioneer_author_statistics', true );
      if ( $meta_cache && ( $meta_cache['valid_until'] ?? 0 ) > time() ) {
        return $meta_cache;
      }
    }

    // SQL: Fetch all required data
    $posts = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT p.ID, p.post_type, p.comment_count,
          wc.meta_value AS word_count,
          sh.meta_value AS story_hidden,
          ch.meta_value AS chapter_hidden,
          nch.meta_value AS chapter_no_chapter
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} wc  ON p.ID = wc.post_id  AND wc.meta_key = '_word_count'
        LEFT JOIN {$wpdb->postmeta} sh  ON p.ID = sh.post_id  AND sh.meta_key = 'fictioneer_story_hidden'
        LEFT JOIN {$wpdb->postmeta} ch  ON p.ID = ch.post_id  AND ch.meta_key = 'fictioneer_chapter_hidden'
        LEFT JOIN {$wpdb->postmeta} nch ON p.ID = nch.post_id AND nch.meta_key = 'fictioneer_chapter_no_chapter'
        WHERE p.post_status = 'publish'
          AND p.post_author = %d
          AND p.post_type IN ('fcn_story', 'fcn_chapter')",
        $author_id
      ),
      ARRAY_A
    );

    // Process
    $story_count = 0;
    $chapter_count = 0;
    $word_count = 0;
    $comment_count = 0;

    // Loop through posts...
    foreach ( $posts as $post ) {
      $post_id = (int) $post['ID'];

      // Check if hidden
      $is_hidden = ! empty( $post['story_hidden'] ) && $post['story_hidden'] !== '0';
      $is_chapter_hidden = ! empty( $post['chapter_hidden'] ) && $post['chapter_hidden'] !== '0';
      $is_non_chapter = ! empty( $post['chapter_no_chapter'] ) && $post['chapter_no_chapter'] !== '0';

      // Count valid items
      if ( $post['post_type'] === 'fcn_story' && ! $is_hidden ) {
        $story_count++;
      } elseif ( $post['post_type'] === 'fcn_chapter' && ! $is_chapter_hidden && ! $is_non_chapter ) {
        $chapter_count++;
        $comment_count += (int) $post['comment_count'];
      }

      // Apply filters and sum word count
      if ( ! $is_hidden && ! $is_chapter_hidden && ! $is_non_chapter ) {
        $word_count += fictioneer_get_word_count( $post_id, max( 0, intval( $post['word_count'] ) ) );
      }
    }

    // Prepare results
    $result = array(
      'story_count' => $story_count,
      'chapter_count' => $chapter_count,
      'word_count' => $word_count,
      'word_count_short' => fictioneer_shorten_number( $word_count ),
      'valid_until' => time() + HOUR_IN_SECONDS,
      'comment_count' => $comment_count
    );

    // Update meta cache and return
    if ( FICTIONEER_ENABLE_AUTHOR_STATS_META_CACHE ) {
      update_user_meta( $author_id, 'fictioneer_author_statistics', $result );
    }

    return $result;
  }
}

// =============================================================================
// GET COLLECTION STATISTICS
// =============================================================================

if ( ! function_exists( 'fictioneer_get_collection_statistics' ) ) {
  /**
   * Returns a collection's statistics
   *
   * @since 5.9.2
   * @since 5.26.0 - Refactored with custom SQL.
   *
   * @global wpdb $wpdb  WordPress database object.
   *
   * @param int $collection_id  ID of the collection.
   *
   * @return array Array of statistics.
   */

  function fictioneer_get_collection_statistics( $collection_id ) {
    global $wpdb;

    // Meta cache?
    $cache_plugin_active = fictioneer_caching_active( 'collection_statistics' );

    if ( ! $cache_plugin_active ) {
      $meta_cache = get_post_meta( $collection_id, 'fictioneer_collection_statistics', true );

      if ( $meta_cache && ( $meta_cache['valid_until'] ?? 0 ) > time() ) {
        return $meta_cache;
      }
    }

    // Setup
    $featured = get_post_meta( $collection_id, 'fictioneer_collection_items', true );
    $story_count = 0;
    $word_count = 0;
    $chapter_count = 0;
    $comment_count = 0;
    $found_chapter_ids = []; // Chapters that were already counted
    $query_chapter_ids = []; // Chapters that need to be queried

    // Empty collection?
    if ( empty( $featured ) ) {
      return array(
        'story_count' => 0,
        'word_count' => 0,
        'chapter_count' => 0,
        'comment_count' => 0
      );
    }

    // SQL query to analyze collection posts
    $placeholders = implode( ',', array_fill( 0, count( $featured ), '%d' ) );

    $sql =
      "SELECT p.ID, p.post_type
      FROM {$wpdb->posts} p
      WHERE p.ID IN ($placeholders)";

    $posts = $wpdb->get_results( $wpdb->prepare( $sql, ...$featured ) );

    foreach ( $posts as $post ) {
      // Only look at stories and chapters...
      if ( $post->post_type === 'fcn_chapter' ) {
        // ... single chapters need to be looked up separately
        $query_chapter_ids[] = $post->ID;
      } elseif ( $post->post_type === 'fcn_story' ) {
        // ... stories have pre-processed data
        $story = fictioneer_get_story_data( $post->ID, false );
        $found_chapter_ids = array_merge( $found_chapter_ids, $story['chapter_ids'] );
        $word_count += $story['word_count'];
        $chapter_count += $story['chapter_count'];
        $comment_count += $story['comment_count'];
        $story_count += 1;
      }
    }

    // Remove duplicate chapters
    $found_chapter_ids = array_unique( $found_chapter_ids );
    $query_chapter_ids = array_unique( $query_chapter_ids );

    // Do not query already counted chapters
    $query_chapter_ids = array_diff( $query_chapter_ids, $found_chapter_ids );

    // SQL query for lone chapters not belong to featured stories...
    if ( ! empty( $query_chapter_ids ) ) {
      $placeholders = implode( ',', array_fill( 0, count( $query_chapter_ids ), '%d' ) );

      $sql =
        "SELECT p.ID, p.comment_count, COALESCE(pm_word_count.meta_value, 0) AS word_count
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm_hidden
          ON (p.ID = pm_hidden.post_id AND pm_hidden.meta_key = 'fictioneer_chapter_hidden')
        LEFT JOIN {$wpdb->postmeta} pm_no_chapter
          ON (p.ID = pm_no_chapter.post_id AND pm_no_chapter.meta_key = 'fictioneer_chapter_no_chapter')
        LEFT JOIN {$wpdb->postmeta} pm_word_count
          ON (p.ID = pm_word_count.post_id AND pm_word_count.meta_key = '_word_count')
        WHERE p.ID IN ($placeholders)
          AND p.post_type = 'fcn_chapter'
          AND p.post_status = 'publish'
          AND (pm_hidden.meta_value IS NULL OR pm_hidden.meta_value = '' OR pm_hidden.meta_value = '0')
          AND (pm_no_chapter.meta_value IS NULL OR pm_no_chapter.meta_value = '' OR pm_no_chapter.meta_value = '0')";

      $chapters = $wpdb->get_results( $wpdb->prepare( $sql, ...$query_chapter_ids ) );

      foreach ( $chapters as $chapter ) {
        $comment_count += $chapter->comment_count;
        $chapter_count += 1;

        $words = (int) $chapter->word_count;
        $words = max( 0, $words );
        $words = apply_filters( 'fictioneer_filter_word_count', $words, $chapter->ID );
        $words = fictioneer_multiply_word_count( $words );

        $word_count += $words;
      }
    }

    // Statistics
    $statistics = array(
      'story_count' => $story_count,
      'word_count' => $word_count,
      'chapter_count' => $chapter_count,
      'comment_count' => $comment_count,
      'valid_until' => time() + 900 // 15 minutes
    );

    // Update meta cache
    if ( ! $cache_plugin_active ) {
      update_post_meta( $collection_id, 'fictioneer_collection_statistics', $statistics );
    }

    // Done
    return $statistics;
  }
}

// =============================================================================
// SHORTEN NUMBER WITH LETTER
// =============================================================================

if ( ! function_exists( 'fictioneer_shorten_number' ) ) {
  /**
   * Shorten a number to a fractional with a letter.
   *
   * @since 4.5.0
   * @since 5.27.4 - Localize numbers and made letters translatable.
   *
   * @param int $number     The number to be shortened.
   * @param int $precision  Precision of the fraction. Default 1.
   *
   * @return string The minified number string.
   */

  function fictioneer_shorten_number( $number, $precision = 1 ) {
    $number = intval( $number );

    // The letters are prefixed by a HAIR SPACE (&hairsp;)
    if ( $number < 1000 ) {
      return strval( $number );
    } else if ( $number < 1000000 ) {
      return sprintf(
        _x( '%s K', 'Abbreviation for thousand, separated by HAIR SPACE (&hairsp;).', 'fictioneer' ),
        number_format_i18n( $number / 1000, $precision )
      );
    } else if ( $number < 1000000000 ) {
      return sprintf(
        _x( '%s M', 'Abbreviation for million, separated by HAIR SPACE (&hairsp;).', 'fictioneer' ),
        number_format_i18n( $number / 1000000, $precision )
      );
    } else {
      return sprintf(
        _x( '%s B', 'Abbreviation for billion, separated by HAIR SPACE (&hairsp;).', 'fictioneer' ),
        number_format_i18n( $number / 1000000000, $precision )
      );
    }
  }
}

// =============================================================================
// VALIDATE ID
// =============================================================================

if ( ! function_exists( 'fictioneer_validate_id' ) ) {
  /**
   * Ensures an ID is a valid integer and positive; optionally checks whether the
   * associated post is of a certain types (or among an array of types)
   *
   * @since 4.7.0
   *
   * @param int          $id        The ID to validate.
   * @param string|array $for_type  Optional. The expected post type(s).
   *
   * @return int|boolean The validated ID or false if invalid.
   */

  function fictioneer_validate_id( $id, $for_type = [] ) {
    $safe_id = intval( $id );
    $types = is_array( $for_type ) ? $for_type : [ $for_type ];

    if ( empty( $safe_id ) || $safe_id < 0 ) {
      return false;
    }

    if ( ! empty( $for_type ) && ! in_array( get_post_type( $safe_id ), $types ) ) {
      return false;
    }

    return $safe_id;
  }
}

// =============================================================================
// VALIDATE NONCE PLAUSIBILITY
// =============================================================================

if ( ! function_exists( 'fictioneer_nonce_plausibility' ) ) {
  /**
   * Checks nonce to be plausible
   *
   * This helps to evaluate whether a nonce has been malformed, for example
   * through a dynamic update from a cache plugin not working properly.
   *
   * @since 5.9.4
   *
   * @param string $nonce  The nonce to check.
   *
   * @return boolean Whether the nonce is plausible.
   */

  function fictioneer_nonce_plausibility( $nonce ) {
    if ( preg_match( '/^[a-f0-9]{10}$/i', $nonce ) === 1 ) {
      return true;
    }

    return false;
  }
}

// =============================================================================
// GET VALIDATED AJAX USER
// =============================================================================

if ( ! function_exists( 'fictioneer_get_validated_ajax_user' ) ) {
  /**
   * Get the current user after performing AJAX validations
   *
   * @since 5.0.0
   *
   * @param string $nonce_name   Optional. The name of the nonce. Default 'nonce'.
   * @param string $nonce_value  Optional. The value of the nonce. Default 'fictioneer_nonce'.
   *
   * @return boolean|WP_User False if not valid, the current user object otherwise.
   */

  function fictioneer_get_validated_ajax_user( $nonce_name = 'nonce', $nonce_value = 'fictioneer_nonce' ) {
    // Setup
    $user = wp_get_current_user();

    // Validate
    if (
      ! $user->exists() ||
      ! check_ajax_referer( $nonce_value, $nonce_name, false )
    ) {
      return false;
    }

    return $user;
  }
}

// =============================================================================
// RATE LIMIT
// =============================================================================

/**
 * Checks rate limit globally or for an action via the session
 *
 * @since 5.7.1
 *
 * @param string   $action  The action to check for rate-limiting.
 *                          Defaults to 'fictioneer_global'.
 * @param int|null $max     Optional. Maximum number of requests.
 *                          Defaults to FICTIONEER_REQUESTS_PER_MINUTE.
 */

function fictioneer_check_rate_limit( $action = 'fictioneer_global', $max = null ) {
  if ( ! get_option( 'fictioneer_enable_rate_limits' ) ) {
    return;
  }

  // Start session if not already done
  if ( session_status() == PHP_SESSION_NONE ) {
    session_start();
  }

  // Initialize if not set
  if ( ! isset( $_SESSION[ $action ]['request_times'] ) ) {
    $_SESSION[ $action ]['request_times'] = [];
  }

  // Setup
  $current_time = microtime( true );
  $time_window = 60;
  $max = $max ? absint( $max ) : FICTIONEER_REQUESTS_PER_MINUTE;
  $max = max( 1, $max );

  // Filter out old timestamps
  $_SESSION[ $action ]['request_times'] = array_filter(
    $_SESSION[ $action ]['request_times'],
    function ( $time ) use ( $current_time, $time_window ) {
      return ( $current_time - $time ) < $time_window;
    }
  );

  // Limit exceeded?
  if ( count( $_SESSION[ $action ]['request_times'] ) >= $max ) {
    http_response_code( 429 ); // Too many requests
    exit;
  }

  // Record the current request time
  $_SESSION[ $action ]['request_times'][] = $current_time;
}

// =============================================================================
// KEY/VALUE STRING REPLACEMENT
// =============================================================================

if ( ! function_exists( 'fictioneer_replace_key_value' ) ) {
  /**
   * Replaces key/value pairs in a string
   *
   * @since 5.0.0
   *
   * @param string $text     Text that has key/value pairs to be replaced.
   * @param array  $args     The key/value pairs.
   * @param string $default  Optional. To be used if the text is empty.
   *                         Default is an empty string.
   *
   * @return string The modified text.
   */

  function fictioneer_replace_key_value( $text, $args, $default = '' ) {
    // Check if text exists
    if ( empty( $text ) ) {
      $text = $default;
    }

    // Check args
    $args = is_array( $args ) ? $args : [];

    // Filter args
    $args = array_filter( $args, 'is_scalar' );

    // Return modified text
    return trim( strtr( $text, $args ) );
  }
}

// =============================================================================
// CHECK USER CAPABILITIES
// =============================================================================

if ( ! function_exists( 'fictioneer_is_admin' ) ) {
  /**
   * Checks if a user is an administrator
   *
   * @since 5.0.0
   *
   * @param int $user_id  The user ID to check.
   *
   * @return boolean To be or not to be.
   */

  function fictioneer_is_admin( $user_id ) {
    // Abort conditions
    if ( ! $user_id ) {
      return false;
    }

    // Check capabilities
    $check = user_can( $user_id, 'administrator' );

    // Filter
    $check = apply_filters( 'fictioneer_filter_is_admin', $check, $user_id );

    // Return result
    return $check;
  }
}

if ( ! function_exists( 'fictioneer_is_author' ) ) {
  /**
   * Checks if a user is an author
   *
   * @since 5.0.0
   *
   * @param int $user_id  The user ID to check.
   *
   * @return boolean To be or not to be.
   */

  function fictioneer_is_author( $user_id ) {
    // Abort conditions
    if ( ! $user_id ) {
      return false;
    }

    // Check capabilities
    $check = user_can( $user_id, 'publish_posts' ) ||
      user_can( $user_id, 'publish_fcn_stories' ) ||
      user_can( $user_id, 'publish_fcn_chapters' ) ||
      user_can( $user_id, 'publish_fcn_collections' );

    // Filter
    $check = apply_filters( 'fictioneer_filter_is_author', $check, $user_id );

    // Return result
    return $check;
  }
}

if ( ! function_exists( 'fictioneer_is_moderator' ) ) {
  /**
   * Checks if a user is a moderator
   *
   * @since 5.0.0
   *
   * @param int $user_id  The user ID to check.
   *
   * @return boolean To be or not to be.
   */

  function fictioneer_is_moderator( $user_id ) {
    // Abort conditions
    if ( ! $user_id ) {
      return false;
    }

    // Check capabilities
    $check = user_can( $user_id, 'moderate_comments' );

    // Filter
    $check = apply_filters( 'fictioneer_filter_is_moderator', $check, $user_id );

    // Return result
    return $check;
  }
}

if ( ! function_exists( 'fictioneer_is_editor' ) ) {
  /**
   * Checks if a user is an editor
   *
   * @since 5.0.0
   *
   * @param int $user_id  The user ID to check.
   *
   * @return boolean To be or not to be.
   */

  function fictioneer_is_editor( $user_id ) {
    // Abort conditions
    if ( ! $user_id ) {
      return false;
    }

    // Check capabilities
    $check = user_can( $user_id, 'editor' ) || user_can( $user_id, 'administrator' );

    // Filter
    $check = apply_filters( 'fictioneer_filter_is_editor', $check, $user_id );

    // Return result
    return $check;
  }
}

// =============================================================================
// GET META FIELDS
// =============================================================================

if ( ! function_exists( 'fictioneer_get_story_chapter_ids' ) ) {
  /**
   * Wrapper for get_post_meta() to get story chapter IDs
   *
   * @since 5.8.2
   *
   * @param int $post_id  Optional. The ID of the post the field belongs to.
   *                      Defaults to current post ID.
   *
   * @return array Array of chapter post IDs or an empty array.
   */

  function fictioneer_get_story_chapter_ids( $post_id = null ) {
    // Setup
    $chapter_ids = get_post_meta( $post_id ?? get_the_ID(), 'fictioneer_story_chapters', true );

    // Always return an array
    return is_array( $chapter_ids ) ? $chapter_ids : [];
  }
}

if ( ! function_exists( 'fictioneer_count_words' ) ) {
  /**
   * Returns word count of a post
   *
   * @since 5.25.0
   *
   * @param int         $post_id  ID of the post to count the words of.
   * @param string|null $content  Optional. The post content. Queries the field by default.
   *
   * @return int The word count.
   */

  function fictioneer_count_words( $post_id, $content = null ) {
    // Prepare
    $content = $content ?? get_post_field( 'post_content', $post_id );
    $content = strip_shortcodes( $content );
    $content = strip_tags( $content );
    $content = preg_replace( ['/--/', "/['’‘-]/"], ['—', ''], $content );

    // Count and return result
    return count( preg_split( '/\s+/', $content ) ?: [] );
  }
}

if ( ! function_exists( 'fictioneer_get_word_count' ) ) {
  /**
   * Wrapper for get_post_meta() to get word count
   *
   * @since 5.8.2
   * @since 5.9.4 - Add logic for optional multiplier.
   * @since 5.22.3 - Moved multiplier to fictioneer_multiply_word_count()
   * @since 5.23.1 - Added filter and additional sanitization.
   * @since 5.27.4 - Added $word_count parameter.
   *
   * @param int|null $post_id     Optional. The ID of the post the field belongs to.
   *                              Defaults to current post ID.
   * @param int|null $word_count  Optional. Prepared word count for custom SQL solutions.
   *
   * @return int The word count or 0.
   */

  function fictioneer_get_word_count( $post_id = null, $word_count = null ) {
    // Get word count
    $words = $word_count ?? get_post_meta( $post_id ?? get_the_ID(), '_word_count', true ) ?: 0;
    $words = max( 0, intval( $words ) );

    // Filter
    $words = apply_filters( 'fictioneer_filter_word_count', $words, $post_id );

    // Apply multiplier and return
    return fictioneer_multiply_word_count( $words );
  }
}

if ( ! function_exists( 'fictioneer_get_story_word_count' ) ) {
  /**
   * Returns word count for the whole story
   *
   * @since 5.22.3
   * @since 5.23.1 - Added filter and additional sanitization.
   * @since 5.27.4 - Added $word_count parameter.
   *
   * @param int|null $post_id     Optional. Post ID of the story.
   * @param int|null $word_count  Optional. Prepared word count for custom SQL solutions.
   *
   * @return int The word count or 0.
   */

  function fictioneer_get_story_word_count( $post_id = null, $word_count = null ) {
    // Get word count
    $words = $word_count ?? get_post_meta( $post_id, 'fictioneer_story_total_word_count', true ) ?: 0;
    $words = max( 0, intval( $words ) );

    // Filter
    $words = apply_filters( 'fictioneer_filter_story_word_count', $words, $post_id );

    // Apply multiplier and return
    return fictioneer_multiply_word_count( $words );
  }
}

if ( ! function_exists( 'fictioneer_multiply_word_count' ) ) {
  /**
   * Multiplies word count with factor from options
   *
   * @since 5.22.3
   *
   * @param int $words  Word count.
   *
   * @return int The updated word count.
   */

  function fictioneer_multiply_word_count( $words ) {
    // Setup
    $multiplier = floatval( get_option( 'fictioneer_word_count_multiplier', 1.0 ) );

    // Multiply
    if ( $multiplier !== 1.0 ) {
      $words = intval( $words * $multiplier );
    }

    // Always return an integer greater or equal 0
    return max( 0, $words );
  }
}

if ( ! function_exists( 'fictioneer_get_content_field' ) ) {
  /**
   * Wrapper for get_post_meta() with content filters applied
   *
   * @since 5.0.0
   *
   * @param string $field    Name of the meta field to retrieve.
   * @param int    $post_id  Optional. The ID of the post the field belongs to.
   *                         Defaults to current post ID.
   *
   * @return string The single field value formatted as content.
   */

  function fictioneer_get_content_field( $field, $post_id = null ) {
    // Setup
    $content = get_post_meta( $post_id ?? get_the_ID(), $field, true );

    // Apply default filter functions from the_content (but nothing else)
    $content = do_blocks( $content );
    $content = wptexturize( $content );
    $content = convert_chars( $content );
    $content = wpautop( $content );
    $content = shortcode_unautop( $content );
    $content = prepend_attachment( $content );
    $content = wp_replace_insecure_home_url( $content );
    $content = wp_filter_content_tags( $content );
    $content = convert_smilies( $content );

    // Return formatted/filtered content
    return $content;
  }
}

if ( ! function_exists( 'fictioneer_get_icon_field' ) ) {
  /**
   * Wrapper for get_post_meta() to get Font Awesome icon class
   *
   * @since 5.0.0
   * @since 5.26.0 - Add $icon parameter as override.
   *
   * @param string      $field    Name of the meta field to retrieve.
   * @param int|null    $post_id  Optional. The ID of the post the field belongs to.
   *                              Defaults to current post ID.
   * @param string|null $icon     Optional. Pre-retrieved icon string as override. Default null.
   *
   * @return string The Font Awesome class.
   */

  function fictioneer_get_icon_field( $field, $post_id = null, $icon = null ) {
    // Setup
    $icon = $icon ?? get_post_meta( $post_id ?? get_the_ID(), $field, true );
    $icon_object = json_decode( $icon ); // Check for ACF Font Awesome plugin

    // Valid?
    if ( ! $icon_object && ( empty( $icon ) || strpos( $icon, 'fa-' ) !== 0 ) ) {
      return FICTIONEER_DEFAULT_CHAPTER_ICON;
    }

    if ( $icon_object && ( ! property_exists( $icon_object, 'style' ) || ! property_exists( $icon_object, 'id' ) ) ) {
      return FICTIONEER_DEFAULT_CHAPTER_ICON;
    }

    // Return
    if ( $icon_object && property_exists( $icon_object, 'style' ) && property_exists( $icon_object, 'id' ) ) {
      return 'fa-' . $icon_object->style . ' fa-' . $icon_object->id;
    } else {
      return esc_attr( $icon );
    }
  }
}

// =============================================================================
// UPDATE META FIELDS
// =============================================================================

if ( ! function_exists( 'fictioneer_update_user_meta' ) ) {
  /**
   * Wrapper to update user meta
   *
   * If the meta value is truthy, the meta field is updated as normal.
   * If not, the meta field is deleted instead to keep the database tidy.
   *
   * @since 5.7.3
   *
   * @param int    $user_id     The ID of the user.
   * @param string $meta_key    The meta key to update.
   * @param mixed  $meta_value  The new meta value. If empty, the meta key will be deleted.
   * @param mixed  $prev_value  Optional. If specified, only updates existing metadata with this value.
   *                            Otherwise, update all entries. Default empty.
   *
   * @return int|bool Meta ID if the key didn't exist on update, true on successful update or delete,
   *                  false on failure or if the value passed to the function is the same as the one
   *                  that is already in the database.
   */

  function fictioneer_update_user_meta( $user_id, $meta_key, $meta_value, $prev_value = '' ) {
    if ( empty( $meta_value ) && ! in_array( $meta_key, fictioneer_get_falsy_meta_allow_list() ) ) {
      return delete_user_meta( $user_id, $meta_key );
    } else {
      return update_user_meta( $user_id, $meta_key, $meta_value, $prev_value );
    }
  }
}

if ( ! function_exists( 'fictioneer_update_comment_meta' ) ) {
  /**
   * Wrapper to update comment meta
   *
   * If the meta value is truthy, the meta field is updated as normal.
   * If not, the meta field is deleted instead to keep the database tidy.
   *
   * @since 5.7.3
   *
   * @param int    $comment_id  The ID of the comment.
   * @param string $meta_key    The meta key to update.
   * @param mixed  $meta_value  The new meta value. If empty, the meta key will be deleted.
   * @param mixed  $prev_value  Optional. If specified, only updates existing metadata with this value.
   *                            Otherwise, update all entries. Default empty.
   *
   * @return int|bool Meta ID if the key didn't exist on update, true on successful update or delete,
   *                  false on failure or if the value passed to the function is the same as the one
   *                  that is already in the database.
   */

  function fictioneer_update_comment_meta( $comment_id, $meta_key, $meta_value, $prev_value = '' ) {
    if ( empty( $meta_value ) && ! in_array( $meta_key, fictioneer_get_falsy_meta_allow_list() ) ) {
      return delete_comment_meta( $comment_id, $meta_key );
    } else {
      return update_comment_meta( $comment_id, $meta_key, $meta_value, $prev_value );
    }
  }
}

if ( ! function_exists( 'fictioneer_update_post_meta' ) ) {
  /**
   * Wrapper to update post meta
   *
   * If the meta value is truthy, the meta field is updated as normal.
   * If not, the meta field is deleted instead to keep the database tidy.
   *
   * @since 5.7.4
   *
   * @param int    $post_id     The ID of the post.
   * @param string $meta_key    The meta key to update.
   * @param mixed  $meta_value  The new meta value. If empty, the meta key will be deleted.
   * @param mixed  $prev_value  Optional. If specified, only updates existing metadata with this value.
   *                            Otherwise, update all entries. Default empty.
   *
   * @return int|bool Meta ID if the key didn't exist on update, true on successful update or delete,
   *                  false on failure or if the value passed to the function is the same as the one
   *                  that is already in the database.
   */

  function fictioneer_update_post_meta( $post_id, $meta_key, $meta_value, $prev_value = '' ) {
    if ( empty( $meta_value ) && ! in_array( $meta_key, fictioneer_get_falsy_meta_allow_list() ) ) {
      return delete_post_meta( $post_id, $meta_key );
    } else {
      return update_post_meta( $post_id, $meta_key, $meta_value, $prev_value );
    }
  }
}

/**
 * Return allow list for falsy meta fields
 *
 * @since 5.7.4
 *
 * @return array Meta fields allowed to be saved falsy and not be deleted.
 */

function fictioneer_get_falsy_meta_allow_list() {
  return apply_filters( 'fictioneer_filter_falsy_meta_allow_list', [] );
}

if ( ! function_exists( 'fictioneer_bulk_update_post_meta' ) ) {
  /**
   * Update post meta fields in bulk for a post.
   *
   * If the meta value is truthy, the meta field is updated as normal.
   * If not, the meta field is deleted instead to keep the database tidy.
   * Fires default WP hooks where possible.
   *
   * @since 5.27.4
   * @link https://developer.wordpress.org/reference/functions/update_metadata/
   * @link https://developer.wordpress.org/reference/functions/add_metadata/
   * @link https://developer.wordpress.org/reference/functions/delete_metadata/
   *
   * @param int   $post_id  Post ID.
   * @param array $fields   Associative array of field keys and sanitized (!) values.
   */

  function fictioneer_bulk_update_post_meta( $post_id, $fields ) {
    if ( empty( $fields ) ) {
      return;
    }

    global $wpdb;

    // Setup
    $existing_meta = [];
    $update_parts = [];
    $update_keys = [];
    $update_values = [];
    $insert_parts = [];
    $insert_values = [];
    $delete_keys = [];
    $deleted_meta_fields = [];

    // Fetch existing meta keys and values
    $meta_results = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT meta_id, meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id = %d",
        $post_id
      )
    );

    foreach ( $meta_results as $meta ) {
      $existing_meta[ $meta->meta_key ] = array(
        'meta_id' => $meta->meta_id,
        'meta_value' => $meta->meta_value
      );
    }

    // Prepare
    foreach ( $fields as $key => $value ) {
      // Mark for deletion...
      if ( empty( $value ) && ! in_array( $key, fictioneer_get_falsy_meta_allow_list() ) ) {
        $delete_keys[] = $key;

        if ( isset( $existing_meta[ $key ] ) ) {
          $deleted_meta_fields[ $existing_meta[ $key ]['meta_id'] ] = [ $key, $value ];

          do_action( 'delete_post_meta', [ $existing_meta[ $key ]['meta_id'] ], $post_id, $key, $value );
          do_action( 'delete_postmeta', [ $existing_meta[ $key ]['meta_id'] ] );
        }

        continue;
      }

      // Serialize if necessary
      $prepared_value = is_array( $value ) ? maybe_serialize( $value ) : $value;

      if ( isset( $existing_meta[ $key ] ) ) {
        // Mark for updating...
        if ( $existing_meta[ $key ]['meta_value'] !== $prepared_value ) {
          $update_parts[] = "WHEN meta_key = %s THEN %s";
          $update_keys[] = $key;
          $update_values[] = $key;
          $update_values[] = $prepared_value;

          do_action( 'update_post_meta', $existing_meta[ $key ]['meta_id'], $post_id, $key, $value );
          do_action( 'update_postmeta', $existing_meta[ $key ]['meta_id'], $post_id, $key, $prepared_value );
        }
      } else {
        // Mark for insertion...
        $insert_parts[] = "(%d, %s, %s)";
        $insert_values[] = $post_id;
        $insert_values[] = $key;
        $insert_values[] = $prepared_value;

        do_action( 'add_post_meta', $post_id, $key, $value );
      }
    }

    // DELETE
    if ( ! empty( $delete_keys ) ) {
      $delete_query =
        "DELETE FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key IN (" .
        implode( ', ', array_fill( 0, count( $delete_keys ), '%s' ) ) . ")";

      $wpdb->query( $wpdb->prepare( $delete_query, $post_id, ...$delete_keys ) );

      if ( ! empty( $deleted_meta_fields ) ) {
        foreach ( $deleted_meta_fields as $key => $tuple ) {
          do_action( 'deleted_post_meta', [ $key ], $post_id, $tuple[0], $tuple[1] );
          do_action( 'deleted_postmeta', [ $key ] );
        }
      }
    }

    // UPDATE
    if ( ! empty( $update_parts ) ) {
      $update_query =
        "UPDATE {$wpdb->postmeta}
        SET meta_value = CASE " . implode( ' ', $update_parts ) . " END
        WHERE post_id = %d AND meta_key IN (" . implode( ', ', array_fill( 0, count( $update_keys ), '%s' ) ) . ")";

      $update_values[] = $post_id;
      $update_values = array_merge( $update_values, $update_keys );

      $wpdb->query( $wpdb->prepare( $update_query, ...$update_values ) );

      foreach ( $fields as $key => $value ) {
        if ( in_array( $key, $update_keys ) && isset( $existing_meta[ $key ] ) ) {
          $prepared_value = is_array( $value ) ? maybe_serialize( $value ) : $value;

          do_action( 'updated_post_meta', $existing_meta[ $key ]['meta_id'], $post_id, $key, $value );
          do_action( 'updated_postmeta', $existing_meta[ $key ]['meta_id'], $post_id, $key, $prepared_value );
        }
      }
    }

    // INSERT
    if ( ! empty( $insert_parts ) ) {
      $insert_query =
        "INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value)
        VALUES " . implode( ', ', $insert_parts );

      $wpdb->query( $wpdb->prepare( $insert_query, ...$insert_values ) );

      // Does not return the meta IDs, added_post_meta cannot be fired.
    }

    // Cache cleanup
    wp_cache_delete( $post_id, 'post_meta' );
  }
}

// =============================================================================
// APPEND CHAPTER
// =============================================================================

/**
 * Appends new chapters to story list
 *
 * @since 5.4.9
 * @since 5.7.4 - Updated
 * @since 5.8.6 - Added $force param and moved function.
 * @since 5.19.1 - Always append chapter to story.
 *
 * @param int  $post_id   The chapter post ID.
 * @param int  $story_id  The story post ID.
 * @param bool $force     Optional. Whether to skip some guard clauses. Default false.
 */

function fictioneer_append_chapter_to_story( $post_id, $story_id, $force = false ) {
  $allowed_statuses = apply_filters(
    'fictioneer_filter_append_chapter_to_story_statuses',
    ['publish', 'future'],
    $post_id,
    $story_id,
    $force
  );

  // Abort if chapter status is not allowed
  if ( ! in_array( get_post_status( $post_id ), $allowed_statuses ) ) {
    return;
  }

  // Setup
  $story = get_post( $story_id );

  // Abort if story not found or not a story
  if ( ! $story || $story->post_type !== 'fcn_story' ) {
    return;
  }

  // Setup, continued
  $chapter_author_id = get_post_field( 'post_author', $post_id );
  $story_author_id = get_post_field( 'post_author', $story_id );
  $co_authored_story_ids = fictioneer_sql_get_co_authored_story_ids( $chapter_author_id );

  // Abort if the author IDs do not match
  if (
    $chapter_author_id != $story_author_id &&
    ! in_array( $story_id, $co_authored_story_ids ) &&
    ! $force
  ) {
    return;
  }

  // Get current story chapters
  $story_chapters = fictioneer_get_story_chapter_ids( $story_id );

  // Append chapter (if not already included) and save to database
  if ( ! in_array( $post_id, $story_chapters ) ) {
    $previous_chapters = $story_chapters;
    $story_chapters[] = $post_id;
    $story_chapters = array_unique( $story_chapters );

    // Save updated list
    update_post_meta( $story_id, 'fictioneer_story_chapters', $story_chapters );

    // Remember when chapters have been changed
    update_post_meta( $story_id, 'fictioneer_chapters_modified', current_time( 'mysql', true ) );

    // Remember when chapters have been added
    $allowed_statuses = apply_filters(
      'fictioneer_filter_chapters_added_statuses',
      ['publish'],
      $post_id
    );

    if ( in_array( get_post_status( $post_id ), $allowed_statuses ) ) {
      if ( ! get_post_meta( $post_id, 'fictioneer_chapter_hidden', true ) ) {
        update_post_meta( $story_id, 'fictioneer_chapters_added', current_time( 'mysql', true ) );
      }
    }

    // Log changes
    fictioneer_log_story_chapter_changes( $story_id, $story_chapters, $previous_chapters );

    // Clear meta caches to ensure they get refreshed
    delete_post_meta( $story_id, 'fictioneer_story_data_collection' );
    delete_post_meta( $story_id, 'fictioneer_story_chapter_index_html' );
  } else {
    // Nothing to do
    return;
  }

  // Update story post to fire associated actions
  wp_update_post( array( 'ID' => $story_id ) );
}

// =============================================================================
// GET COOKIE CONSENT
// =============================================================================

if ( ! function_exists( 'fictioneer_get_consent' ) && get_option( 'fictioneer_cookie_banner' ) ) {
  /**
   * Get cookie consent
   *
   * Checks the current user’s consent cookie for their preferences, either 'full'
   * or 'necessary' by default. Returns false if no consent cookie is set.
   *
   * @since 4.7.0
   *
   * @return boolean|string Either false or a string describing the level of consent.
   */

  function fictioneer_get_consent() {
    if ( ! isset( $_COOKIE['fcn_cookie_consent'] ) || $_COOKIE['fcn_cookie_consent'] === '' ) {
      return false;
    }

    return strval( $_COOKIE['fcn_cookie_consent'] );
  }
}

// =============================================================================
// SANITIZE INTEGER
// =============================================================================

/**
 * Sanitizes an integer with options for default, minimum, and maximum
 *
 * @since 4.0.0
 *
 * @param mixed $value    The value to be sanitized.
 * @param int   $default  Default value if an invalid integer is provided. Default 0.
 * @param int   $min      Optional. Minimum value for the integer. Default is no minimum.
 * @param int   $max      Optional. Maximum value for the integer. Default is no maximum.
 *
 * @return int The sanitized integer.
 */

function fictioneer_sanitize_integer( $value, $default = 0, $min = null, $max = null ) {
  // Ensure $value is numeric in the first place
  if ( ! is_numeric( $value ) ) {
    return $default;
  }

  // Cast to integer
  $value = (int) $value;

  // Apply minimum limit if specified
  if ( $min !== null && $value < $min ) {
    return $min;
  }

  // Apply maximum limit if specified
  if ( $max !== null && $value > $max ) {
    return $max;
  }

  return $value;
}

// =============================================================================
// SANITIZE POSITIVE FLOAT
// =============================================================================

/**
 * Sanitizes a float as positive number
 *
 * @since 5.9.4
 *
 * @param mixed $value    The value to be sanitized.
 * @param int   $default  Default value if an invalid float is provided. Default 0.0.
 *
 * @return float The sanitized float.
 */

function fictioneer_sanitize_positive_float( $value, $default = 0.0 ) {
  // Ensure $value is numeric in the first place
  if ( ! is_numeric( $value ) ) {
    return $default;
  }

  // Cast to float
  $value = (float) $value;

  // Return positive float
  return $value < 0 ? $default : $value;
}

/**
 * Sanitize callback with positive float or default 1.0
 *
 * @since 5.10.1
 *
 * @param mixed $value  The value to be sanitized.
 *
 * @return float The sanitized positive float.
 */

function fictioneer_sanitize_positive_float_def1( $value ) {
  // Ensure $value is numeric in the first place
  if ( ! is_numeric( $value ) ) {
    return 1.0;
  }

  // Call general sanitizer with params
  return fictioneer_sanitize_positive_float( $value, 1.0 );
}

/**
 * Sanitize callback with float or default 0
 *
 * @since 5.19.0
 *
 * @param mixed $value  The value to be sanitized.
 *
 * @return float The sanitized float.
 */

function fictioneer_sanitize_float( $value ) {
  // Ensure $value is numeric in the first place
  if ( ! is_numeric( $value ) ) {
    return 0.0;
  }

  // Cast to float
  return (float) $value;
}

// =============================================================================
// SANITIZE CHECKBOX
// =============================================================================

/**
 * Sanitizes a checkbox value into true or false
 *
 * @since 4.7.0
 * @link https://www.php.net/manual/en/function.filter-var.php
 *
 * @param string|boolean $value  The checkbox value to be sanitized.
 *
 * @return boolean True or false.
 */

function fictioneer_sanitize_checkbox( $value ) {
  $value = filter_var( $value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );

  return empty( $value ) ? 0 : 1;
}

// =============================================================================
// SANITIZE SELECT OPTION
// =============================================================================

/**
 * Sanitizes a selected option
 *
 * @since 5.7.4
 *
 * @param mixed $value            The selected value to be sanitized.
 * @param array $allowed_options  The allowed values to be checked against.
 * @param mixed $default          Optional. The default value as fallback.
 *
 * @return mixed The sanitized value or default, null if not provided.
 */

function fictioneer_sanitize_selection( $value, $allowed_options, $default = null ) {
  $value = sanitize_text_field( $value );
  $value = is_numeric( $value ) ? intval( $value ) : $value;

  return in_array( $value, $allowed_options ) ? $value : $default;
}

// =============================================================================
// SANITIZE CSS
// =============================================================================

/**
 * Sanitizes a CSS string
 *
 * @since 5.7.4
 * @since 5.27.4 - Unslash string.
 *
 * @param string $css  The CSS string to be sanitized. Expects slashed string.
 *
 * @return string The sanitized string.
 */

function fictioneer_sanitize_css( $css ) {
  $css = sanitize_textarea_field( wp_unslash( $css ) );
  $css = preg_match( '/<\/?\w+/', $css ) ? '' : $css;

  $opening_braces = substr_count( $css, '{' );
  $closing_braces = substr_count( $css, '}' );

  if ( $opening_braces < 1 || $opening_braces !== $closing_braces ) {
    $css = '';
  }

  return $css;
}

// =============================================================================
// SANITIZE LIST INTO ARRAY
// =============================================================================

/**
 * Sanitizes (and transforms) a comma-separated list into array
 *
 * @since 5.15.0
 *
 * @param string $input  The comma-separated list.
 * @param array $args {
 *   Optional. An array of additional arguments.
 *
 *   @type bool $unique  Run array through array_unique(). Default false.
 *   @type bool $absint  Run all elements through absint(). Default false.
 * }
 *
 * @return array The comma-separated list turned array.
 */

function fictioneer_sanitize_list_into_array( $input, $args = [] ) {
  $input = fictioneer_explode_list( sanitize_textarea_field( $input ) );

  if ( $args['absint'] ?? 0 ) {
    $input = array_map( 'absint', $input );
  }

  if ( $args['unique'] ?? 0 ) {
    $input = array_unique( $input );
  }

  return $input;
}

// =============================================================================
// SANITIZE QUERY VARIABLE
// =============================================================================

/**
 * Sanitizes a query variable
 *
 * @since 5.14.0
 *
 * @param string      $var      Query variable to sanitize.
 * @param array       $allowed  Array of allowed string (lowercase).
 * @param string|null $default  Optional default value.
 * @param array       $args {
 *   Optional. An array of additional arguments.
 *
 *   @type bool $keep_case  Whether to transform the variable to lowercase. Default false.
 * }
 *
 *
 * @return string The sanitized (lowercase) query variable.
 */

function fictioneer_sanitize_query_var( $var, $allowed, $default = null, $args = [] ) {
  if ( isset( $args['keep_case'] ) ) {
    $sanitized = array_intersect( [ $var ?? 0 ], $allowed );
  } else {
    $sanitized = array_intersect( [ strtolower( $var ?? 0 ) ], $allowed );
  }

  return reset( $sanitized ) ?: $default;
}

// =============================================================================
// SANITIZE URL
// =============================================================================

/**
 * Sanitizes an URL
 *
 * @since 5.19.1
 *
 * @param string      $url         The URL entered.
 * @param string|null $match       Optional. URL must start with this string.
 * @param string|null $preg_match  Optional. String for a preg_match() test.
 *
 * @return string The sanitized URL or an empty string if invalid.
 */

function fictioneer_sanitize_url( $url, $match = null, $preg_match = null ) {
  $url = sanitize_url( $url );
  $url = filter_var( $url, FILTER_VALIDATE_URL ) ? $url : '';

  if ( $match && is_string( $match ) ) {
    $url = strpos( $url, $match ) === 0 ? $url : '';
  }

  if ( $preg_match && is_string( $preg_match ) ) {
    $url = preg_match( $preg_match, $url ) ? $url : '';
  }

  return $url;
}

// =============================================================================
// ASPECT RATIO CSS
// =============================================================================

/**
 * Sanitizes a CSS aspect ratio value
 *
 * @since 5.14.0
 * @since 5.23.0 - Refactored to accept fractional values.
 *
 * @param string $css      The CSS value to be sanitized.
 * @param string $default  Optional default value.
 *
 * @return string|bool The sanitized value or default if invalid.
 */

function sanitize_css_aspect_ratio( $css, $default = false ) {
  // Remove unwanted white spaces
  $css = trim( $css );

  // Validate
  if ( preg_match( '/^\d+(\.\d+)?\/\d+(\.\d+)?$/', $css ) ) {
    // Split based on the slash '/'
    list( $numerator, $denominator ) = explode( '/', $css, 2 );

    // Sanitize parts
    $numerator = max( 0, floatval( $numerator ) );
    $denominator = max( 0, floatval( $denominator ) );

    // Nonsense?
    if ( $numerator == 0 || $denominator == 0 ) {
      return $default;
    }

    // Combine and return
    return $numerator . '/' . $denominator;
  }

  // Default if invalid
  return $default;
}

/**
 * Returns aspect ratio values as tuple
 *
 * @since 5.14.0
 *
 * @param string $css  The aspect-ratio CSS value.
 *
 * @return array Tuple of aspect-ratio values.
 */

function fictioneer_get_split_aspect_ratio( $css ) {
  // Split based on the slash '/'
  list( $numerator, $denominator ) = explode( '/', $css, 2 );

  // Return tuple
  return array( (int) ( $numerator ?? 1 ), (int) ( $denominator ?? 1 ) );
}

// =============================================================================
// SHOW LOGIN
// =============================================================================

/**
 * Checks whether the login should be rendered
 *
 * @since 5.18.1
 *
 * @return boolean True or false.
 */

function fictioneer_show_login() {
  $enabled = get_option( 'fictioneer_enable_oauth' ) || get_option( 'fictioneer_show_wp_login_link' );

  return ( $enabled && ! is_user_logged_in() ) || get_option( 'fictioneer_enable_public_cache_compatibility' );
}

// =============================================================================
// SHOW NON-PUBLIC CONTENT
// =============================================================================

/**
 * Wrapper for is_user_logged_in() with global public cache consideration
 *
 * If public caches are served to all users, including logged-in users, it is
 * necessary to render items that would normally be skipped for logged-out
 * users, such as the profile link. They are hidden via CSS, which works as
 * long as the 'logged-in' class is still set on the <body>.
 *
 * @since 5.0.0
 *
 * @return boolean True or false.
 */

function fictioneer_show_auth_content() {
  return is_user_logged_in() ||
    get_option( 'fictioneer_enable_public_cache_compatibility' ) ||
    get_option( 'fictioneer_enable_ajax_authentication' );
}

// =============================================================================
// FICTIONEER TRANSLATIONS
// =============================================================================

/**
 * Returns selected translations
 *
 * Adding theme options for all possible translations would be a pain,
 * so this is done with a function that can be filtered as needed. For
 * giving your site a personal touch.
 *
 * @since 5.0.0
 *
 * @param string  $key     Key for requested translation.
 * @param boolean $escape  Optional. Escape the string for safe use in
 *                         attributes. Default false.
 *
 * @return string The translation or an empty string if not found.
 */

function fcntr( $key, $escape = false ) {
  static $strings = null;

  // Define default translations
  if ( $strings === null ) {
    $strings = array(
      'account' => __( 'Account', 'fictioneer' ),
      'bookshelf' => __( 'Bookshelf', 'fictioneer' ),
      'admin' => _x( 'Admin', 'Caption for administrator badge label.', 'fictioneer' ),
      'anonymous_guest' => __( 'Anonymous Guest', 'fictioneer' ),
      'author' => _x( 'Author', 'Caption for author badge label.', 'fictioneer' ),
      'bbcodes_modal' => _x( 'BBCodes', 'Heading for BBCodes tutorial modal.', 'fictioneer' ),
      'blog' => _x( 'Blog', 'Blog page name, mainly used in breadcrumbs.', 'fictioneer' ),
      'bookmark' => __( 'Bookmark', 'fictioneer' ),
      'bookmarks' => __( 'Bookmarks', 'fictioneer' ),
      'warning_notes' => __( 'Warning Notes', 'fictioneer' ),
      'deleted_user' => __( 'Deleted User', 'fictioneer' ),
      'follow' => _x( 'Follow', 'Follow a story.', 'fictioneer' ),
      'follows' => _x( 'Follows', 'List of followed stories.', 'fictioneer' ),
      'forget' => _x( 'Forget', 'Forget story set to be read later.', 'fictioneer' ),
      'formatting' => _x( 'Formatting', 'Toggle for chapter formatting modal.', 'fictioneer' ),
      'formatting_modal' => _x( 'Formatting', 'Chapter formatting modal heading.', 'fictioneer' ),
      'frontpage' => _x( 'Home', 'Frontpage page name, mainly used in breadcrumbs.', 'fictioneer' ),
      'is_followed' => _x( 'Followed', 'Story is followed.', 'fictioneer' ),
      'is_read' => _x( 'Read', 'Story or chapter is marked as read.', 'fictioneer' ),
      'is_read_later' => _x( 'Read later', 'Story is marked to be read later.', 'fictioneer' ),
      'jump_to_comments' => __( 'Jump: Comments', 'fictioneer' ),
      'jump_to_bookmark' => __( 'Jump: Bookmark', 'fictioneer' ),
      'login' => __( 'Login', 'fictioneer' ),
      'login_modal' => _x( 'Login', 'Login modal heading.', 'fictioneer' ),
      'login_with' => _x( 'Log in with', 'OAuth 2.0 login option plus appended icon.', 'fictioneer' ),
      'logout' => __( 'Logout', 'fictioneer' ),
      'mark_read' => _x( 'Mark Read', 'Mark story as read.', 'fictioneer' ),
      'mark_unread' => _x( 'Mark Unread', 'Mark story as unread.', 'fictioneer' ),
      'moderator' => _x( 'Mod', 'Caption for moderator badge label', 'fictioneer' ),
      'next' => __( '<span class="on">Next</span><span class="off"><i class="fa-solid fa-caret-right"></i></span>', 'fictioneer' ),
      'no_bookmarks' => __( 'No bookmarks.', 'fictioneer' ),
      'password' => __( 'Password', 'fictioneer' ),
      'previous' => __( '<span class="off"><i class="fa-solid fa-caret-left"></i></span><span class="on">Previous</span>', 'fictioneer' ),
      'read_later' => _x( 'Read Later', 'Remember a story to be read later.', 'fictioneer' ),
      'read_more' => _x( 'Read More', 'Read more of a post.', 'fictioneer' ),
      'reminders' => _x( 'Reminders', 'List of stories to read later.', 'fictioneer' ),
      'site_settings' => __( 'Site Settings', 'fictioneer' ),
      'story_blog' => _x( 'Blog', 'Blog tab of the story.', 'fictioneer' ),
      'subscribe' => _x( 'Subscribe', 'Subscribe to a story.', 'fictioneer' ),
      'unassigned_group' => _x( 'Unassigned', 'Chapters not assigned to group.', 'fictioneer' ),
      'unfollow' => _x( 'Unfollow', 'Stop following a story.', 'fictioneer' ),
      'E' => _x( 'E', 'Age rating E for Everyone.', 'fictioneer' ),
      'T' => _x( 'T', 'Age rating T for Teen.', 'fictioneer' ),
      'M' => _x( 'M', 'Age rating M for Mature.', 'fictioneer' ),
      'A' => _x( 'A', 'Age rating A for Adult.', 'fictioneer' ),
      'Everyone' => _x( 'Everyone', 'Age rating Everyone.', 'fictioneer' ),
      'Teen' => _x( 'Teen', 'Age rating Teen.', 'fictioneer' ),
      'Mature' => _x( 'Mature', 'Age rating Mature.', 'fictioneer' ),
      'Adult' => _x( 'Adult', 'Age rating Adult.', 'fictioneer' ),
      'Completed' => _x( 'Completed', 'Completed story status.', 'fictioneer' ),
      'Ongoing' => _x( 'Ongoing', 'Ongoing story status', 'fictioneer' ),
      'Hiatus' => _x( 'Hiatus', 'Hiatus story status', 'fictioneer' ),
      'Oneshot' => _x( 'Oneshot', 'Oneshot story status', 'fictioneer' ),
      'Canceled' => _x( 'Canceled', 'Canceled story status', 'fictioneer' ),
      'comment_anchor' => _x( '<i class="fa-solid fa-link"></i>', 'Text or icon for paragraph anchor in comments.', 'fictioneer' ),
      'bbcode_b' => _x( '<code>[b]</code><strong>Bold</strong><code>[/b]</code> of you to assume I have a plan.', 'BBCode example.', 'fictioneer' ),
      'bbcode_i' => _x( 'Deathbringer, emphasis on <code>[i]</code><em>death</em><code>[/i]</code>.', 'BBCode example.', 'fictioneer' ),
      'bbcode_s' => _x( 'I’m totally <code>[s]</code><strike>crossed out</strike><code>[/s]</code> by this.', 'BBCode example.', 'fictioneer' ),
      'bbcode_li' => _x( '<ul><li class="comment-list-item">Listless I’m counting my <code>[li]</code>bullets<code>[/li]</code>.</li></ul>', 'BBCode example.', 'fictioneer' ),
      'bbcode_img' => _x( '<code>[img]</code>https://www.agine.this<code>[/img]</code> %s', 'BBCode example.', 'fictioneer' ),
      'bbcode_link' => _x( '<code>[link]</code><a href="http://topwebfiction.com/" target="_blank" class="link">http://topwebfiction.com</a><code>[/link]</code>.', 'BBCode example.', 'fictioneer' ),
      'bbcode_link_name' => _x( '<code>[link=https://www.n.ot]</code><a href="http://topwebfiction.com/" class="link">clickbait</a><code>[/link]</code>.', 'BBCode example.', 'fictioneer' ),
      'bbcode_quote' => _x( '<blockquote><code>[quote]</code>… me like my landlord!<code>[/quote]</code></blockquote>', 'BBCode example.', 'fictioneer' ),
      'bbcode_spoiler' => _x( '<code>[spoiler]</code><span class="spoiler">Spanish Inquisition!</span><code>[/spoiler]</code>', 'BBCode example.', 'fictioneer' ),
      'bbcode_ins' => _x( '<code>[ins]</code><ins>Insert</ins><code>[/ins]</code> more bad puns!', 'BBCode example.', 'fictioneer' ),
      'bbcode_del' => _x( '<code>[del]</code><del>Delete</del><code>[/del]</code> your browser history!', 'BBCode example.', 'fictioneer' ),
      'log_in_with' => _x( 'Enter your details or log in with:', 'Comment form login note.', 'fictioneer' ),
      'logged_in_as' => _x( '<span>Logged in as <strong><a href="%1$s">%2$s</a></strong>. <a class="logout-link" href="%3$s" data-action="click->fictioneer#logout">Log out?</a></span>', 'Comment form logged-in note.', 'fictioneer' ),
      'accept_privacy_policy' => _x( 'I accept the <b><a class="link" href="%s" target="_blank">privacy policy</a></b>.', 'Comment form privacy checkbox.', 'fictioneer' ),
      'save_in_cookie' => _x( 'Save in cookie for next time.', 'Comment form cookie checkbox.', 'fictioneer' ),
      'future_prefix' => _x( 'Scheduled:', 'Chapter list status prefix.', 'fictioneer' ),
      'trashed_prefix' => _x( 'Trashed:', 'Chapter list status prefix.', 'fictioneer' ),
      'private_prefix' => _x( 'Private:', 'Chapter list status prefix.', 'fictioneer' ),
      'free_patreon_tier' => _x( 'Follower (Free)', 'Free Patreon tier (follower).', 'fictioneer' ),
      'private_comment' => _x( 'Private Comment', 'Comment type translation.', 'fictioneer' ),
      'comment_comment' => _x( 'Public Comment', 'Comment type translation.', 'fictioneer' ),
      'approved_comment_status' => _x( 'Approved', 'Comment status translation.', 'fictioneer' ),
      'hold_comment_status' => _x( 'Hold', 'Comment status translation.', 'fictioneer' ),
      'unapproved_comment_status' => _x( 'Unapproved', 'Comment status translation.', 'fictioneer' ),
      'spam_comment_status' => _x( 'Spam', 'Comment status translation.', 'fictioneer' ),
      'trash_comment_status' => _x( 'Trash', 'Comment status translation.', 'fictioneer' ),
    );

    // Filter static translations
    $strings = apply_filters( 'fictioneer_filter_translations_static', $strings );
  }

  // Filter translations
  $strings = apply_filters( 'fictioneer_filter_translations', $strings );

  // Return requested translation if defined...
  if ( array_key_exists( $key, $strings ) ) {
    return $escape ? esc_attr( $strings[ $key ] ) : $strings[ $key ];
  }

  // ... otherwise return empty string
  return '';
}

// =============================================================================
// BALANCE PAGINATION ARRAY
// =============================================================================

/**
 * Balances pagination array
 *
 * Takes an number array of pagination pages and balances the items around the
 * current page number, replacing anything above the keep threshold with ellipses.
 * E.g. 1 … 7, 8, [9], 10, 11 … 20.
 *
 * @since 5.0.0
 *
 * @param array|int $pages     Array of pages to balance. If an integer is provided,
 *                             it is converted to a number array.
 * @param int       $current   Current page number.
 * @param int       $keep      Optional. Balancing factor to each side. Default 2.
 * @param string    $ellipses  Optional. String for skipped numbers. Default '…'.
 *
 * @return array The balanced array.
 */

function fictioneer_balance_pagination_array( $pages, $current, $keep = 2, $ellipses = '…' ) {
  // Setup
  $max_pages = is_array( $pages ) ? count( $pages ) : $pages;
  $steps = is_array( $pages ) ? $pages : [];

  if ( ! is_array( $pages ) ) {
    for ( $i = 1; $i <= $max_pages; $i++ ) {
      $steps[] = $i;
    }
  }

  // You know, I wrote this but don't really get it myself...
  if ( $max_pages - $keep * 2 > $current ) {
    $start = $current + $keep;
    $end = $max_pages - $keep + 1;

    for ( $i = $start; $i < $end; $i++ ) {
      unset( $steps[ $i ] );
    }

    array_splice( $steps, count( $steps ) - $keep + 1, 0, $ellipses );
  }

  // It certainly does math...
  if ( $current - $keep * 2 >= $keep ) {
    $start = $keep - 1;
    $end = $current - $keep - 1;

    for ( $i = $start; $i < $end; $i++ ) {
      unset( $steps[ $i ] );
    }

    array_splice( $steps, $keep - 1, 0, $ellipses );
  }

  return $steps;
}

// =============================================================================
// CHECK WHETHER COMMENTING IS DISABLED
// =============================================================================

if ( ! function_exists( 'fictioneer_is_commenting_disabled' ) ) {
  /**
   * Check whether commenting is disabled
   *
   * Differs from comments_open() in the regard that it does not hide the whole
   * comment section but does not allow new comments to be posted.
   *
   * @since 5.0.0
   *
   * @param int|null $post_id  Post ID the comments are for. Defaults to current post ID.
   *
   * @return boolean True or false.
   */

  function fictioneer_is_commenting_disabled( $post_id = null ) {
    // Setup
    $post_id = $post_id ?? get_the_ID();

    // Return immediately if...
    if (
      get_option( 'fictioneer_disable_commenting' ) ||
      get_post_meta( $post_id, 'fictioneer_disable_commenting', true )
    ) {
      return true;
    }

    // Check parent story if chapter...
    if ( get_post_type( $post_id ) === 'fcn_chapter' ) {
      $story_id = fictioneer_get_chapter_story_id( $post_id );

      if ( $story_id ) {
        return get_post_meta( $story_id, 'fictioneer_disable_commenting', true ) == true;
      }
    }

    return false;
  }
}

// =============================================================================
// CHECK DISALLOWED KEYS WITH OFFENSES RETURNED
// =============================================================================

if ( ! function_exists( 'fictioneer_check_comment_disallowed_list' ) ) {
  /**
   * Checks whether a comment contains disallowed characters or words and
   * returns the offenders within the comment content
   *
   * @since 5.0.0
   *
   * @param string $author      The author of the comment.
   * @param string $email       The email of the comment.
   * @param string $url         The url used in the comment.
   * @param string $comment     The comment content
   * @param string $user_ip     The comment author's IP address.
   * @param string $user_agent  The author's browser user agent.
   *
   * @return array Tuple of true/false [0] and offenders [1] as array.
   */

  function fictioneer_check_comment_disallowed_list( $author, $email, $url, $comment, $user_ip, $user_agent ) {
    // Implementation is the same as wp_check_comment_disallowed_list(...)
    $mod_keys = trim( get_option( 'disallowed_keys' ) );

    if ( '' === $mod_keys ) {
      return [false, []]; // If moderation keys are empty.
    }

    // Ensure HTML tags are not being used to bypass the list of disallowed characters and words.
    $comment_without_html = wp_strip_all_tags( $comment );

    $words = explode( "\n", $mod_keys );

    foreach ( (array) $words as $word ) {
      $word = trim( $word );

      // Skip empty lines.
      if ( empty( $word ) ) {
        continue;
      }

      // Do some escaping magic so that '#' chars in the spam words don't break things:
      $word = preg_quote( $word, '#' );
      $matches = false;

      $pattern = "#$word#i";
      if ( preg_match( $pattern, $author )
        || preg_match( $pattern, $email )
        || preg_match( $pattern, $url )
        || preg_match( $pattern, $comment, $matches )
        || preg_match( $pattern, $comment_without_html, $matches )
        || preg_match( $pattern, $user_ip )
        || preg_match( $pattern, $user_agent )
      ) {
        return [true, $matches];
      }
    }

    return [false, []];
  }
}

// =============================================================================
// BBCODES
// =============================================================================

if ( ! function_exists( 'fictioneer_bbcodes' ) ) {
  /**
   * Interprets BBCodes into HTML
   *
   * Note: Spoilers do not work properly if wrapping multiple lines or other codes.
   *
   * @since 4.0.0
   * @link https://stackoverflow.com/a/17508056/17140970
   *
   * @param string $content  The content.
   *
   * @return string The content with interpreted BBCodes.
   */

  function fictioneer_bbcodes( $content ) {
    // Setup
    $img_search = '\s*https:[^\"\'|;<>\[\]]+?\.(?:png|jpg|jpeg|gif|webp|svg|avif|tiff).*?\s*';
    $url_search = '\s*(http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?\s*';

    // Deal with some multi-line spoiler issues
    if ( preg_match_all( '/\[spoiler](.+?)\[\/spoiler]/is', $content, $spoilers, PREG_PATTERN_ORDER ) ) {
      foreach ( $spoilers[0] as $spoiler ) {
        $replace = str_replace( '<p></p>', ' ', $spoiler );
        $replace = preg_replace( '/\[quote](.+?)\[\/quote]/is', '<blockquote class="spoiler">$1</blockquote>', $replace );

        $content = str_replace( $spoiler, $replace, $content );
      }
    }

    // Possible patterns
    $patterns = array(
      '/\[spoiler]\[quote](.+?)\[\/quote]\[\/spoiler]/is',
      '/\[spoiler](.+?)\[\/spoiler]/i',
      '/\[spoiler](.+?)\[\/spoiler]/is',
      '/\[b](.+?)\[\/b]/i',
      '/\[i](.+?)\[\/i]/i',
      '/\[s](.+?)\[\/s]/i',
      '/\[quote](.+?)\[\/quote]/is',
      '/\[ins](.+?)\[\/ins]/is',
      '/\[del](.+?)\[\/del]/is',
      '/\[li](.+?)\[\/li]/i',
      "/\[link.*]\[img]\s*($img_search)\s*\[\/img]\[\/link]/i",
      "/\[img]\s*($img_search)\s*\[\/img]/i",
      "/\[link\]\s*($url_search)\s*\[\/link\]/i",
      "/\[link=\s*[\"']?\s*($url_search)\s*[\"']?\s*\](.+?)\[\/link\]/i",
      '/\[anchor]\s*([^\"\'|;<>\[\]]+?)\s*\[\/anchor]/i'
    );

    // HTML replacements
    $replacements = array(
      '<blockquote class="spoiler">$1</blockquote>',
      '<span class="spoiler">$1</span>',
      '<div class="spoiler">$1</div>',
      '<strong>$1</strong>',
      '<em>$1</em>',
      '<strike>$1</strike>',
      '<blockquote>$1</blockquote>',
      '<ins>$1</ins>',
      '<del>$1</del>',
      '<div class="comment-list-item">$1</div>',
      '<span class="comment-image-consent-wrapper"><button type="button" class="button _secondary consent-button" title="$1">' . _x( '<i class="fa-solid fa-image"></i> Show Image', 'Comment image consent wrapper button.', 'fictioneer' ) . '</button><a href="$1" class="comment-image-link" rel="noreferrer noopener nofollow" target="_blank"><img class="comment-image" data-src="$1"></a></span>',
      '<span class="comment-image-consent-wrapper"><button type="button" class="button _secondary consent-button" title="$1">' . _x( '<i class="fa-solid fa-image"></i> Show Image', 'Comment image consent wrapper button.', 'fictioneer' ) . '</button><img class="comment-image" data-src="$1"></span>',
      "<a href=\"$1\" rel=\"noreferrer noopener nofollow\">$1</a>",
      "<a href=\"$1\" rel=\"noreferrer noopener nofollow\">$5</a>",
      '<a href="#$1" rel="noreferrer noopener nofollow" data-block="start" class="comment-anchor">:anchor:</a>'
    );

    // Pattern replace
    $content = preg_replace( $patterns, $replacements, $content );

    // Icons
    $content = str_replace( ':anchor:', fcntr( 'comment_anchor' ), $content );

    return $content;
  }
}

// =============================================================================
// GET TAXONOMY NAMES
// =============================================================================

if ( ! function_exists( 'fictioneer_get_taxonomy_names' ) ) {
  /**
   * Get all taxonomies of a post
   *
   * @since 5.0.20
   *
   * @param int     $post_id  ID of the post to get the taxonomies of.
   * @param boolean $flatten  Whether to flatten the result. Default false.
   *
   * @return array Array with all taxonomies.
   */

  function fictioneer_get_taxonomy_names( $post_id, $flatten = false ) {
    // Setup
    $t = [];
    $t['tags'] = get_the_tags( $post_id );
    $t['fandoms'] = get_the_terms( $post_id, 'fcn_fandom' );
    $t['characters'] = get_the_terms( $post_id, 'fcn_character' );
    $t['warnings'] = get_the_terms( $post_id, 'fcn_content_warning' );
    $t['genres'] = get_the_terms( $post_id, 'fcn_genre' );

    // Validate
    foreach ( $t as $key => $tax ) {
      $t[ $key ] = is_array( $t[ $key ] ) ? $t[ $key ] : [];
    }

    // Extract
    foreach ( $t as $key => $tax ) {
      $t[ $key ] = array_map( function( $a ) { return $a->name; }, $t[ $key ] );
    }

    // Return flattened
    if ( $flatten ) {
      return array_merge( $t['tags'], $t['fandoms'], $t['characters'], $t['warnings'], $t['genres'] );
    }

    // Return without empty arrays
    return array_filter( $t, 'count' );
  }
}

// =============================================================================
// GET FONTS
// =============================================================================

if ( ! function_exists( 'fictioneer_get_fonts' ) ) {
  /**
   * Returns array of font items
   *
   * Note: The css string can contain quotes in case of multiple words,
   * such as "Roboto Mono".
   *
   * @since 5.1.1
   * @since 5.10.0 - Refactor for font manager.
   * @since 5.12.5 - Add theme mod for chapter body font.
   *
   * @return array Font items (css, name, and alt).
   */

  function fictioneer_get_fonts() {
    // Make sure fonts are set up!
    if (
      ! get_option( 'fictioneer_chapter_fonts' ) ||
      ! is_array( get_option( 'fictioneer_chapter_fonts' ) )
    ) {
      fictioneer_build_bundled_fonts();
    }

    // Setup
    $custom_fonts = get_option( 'fictioneer_chapter_fonts' );
    $primary_chapter_font = get_theme_mod( 'chapter_chapter_body_font_family_value', 'default' );
    $fonts = array(
      array( 'css' => fictioneer_font_family_value( FICTIONEER_PRIMARY_FONT_CSS ), 'name' => FICTIONEER_PRIMARY_FONT_NAME ),
      array( 'css' => '', 'name' => _x( 'System Font', 'Font name.', 'fictioneer' ) )
    );

    // Build final font array
    foreach ( $custom_fonts as $custom_font ) {
      if (
        ! in_array( $custom_font, $fonts ) &&
        $custom_font['name'] !== FICTIONEER_PRIMARY_FONT_NAME &&
        $custom_font['css'] !== FICTIONEER_PRIMARY_FONT_CSS
      ) {
        if (
          $primary_chapter_font !== 'default' &&
          strpos( $custom_font['css'], $primary_chapter_font ) !== false
        ) {
          array_unshift( $fonts, $custom_font );
        } else {
          $fonts[] = $custom_font;
        }
      }
    }

    // Apply filters and return
    return apply_filters( 'fictioneer_filter_fonts', $fonts );
  }
}

// =============================================================================
// WRAP MULTI-WORD FONTS INTO QUOTES
// =============================================================================

/**
 * Returns font family value with quotes if required
 *
 * @since 5.10.0
 *
 * @param string $font_value  The font family value.
 * @param string $quote       Optional. The wrapping character. Default '"'.
 *
 * @return string Ready to use font family value.
 */

function fictioneer_font_family_value( $font_value, $quote = '"' ) {
  if ( preg_match( '/\s/', $font_value ) ) {
    return $quote . $font_value . $quote;
  } else {
    return $font_value;
  }
}

// =============================================================================
// GET FONT COLORS
// =============================================================================

if ( ! function_exists( 'fictioneer_get_font_colors' ) ) {
  /**
   * Returns array of font color items
   *
   * @since 5.1.1
   *
   * @return array Font items (css and name).
   */

  function fictioneer_get_font_colors() {
    // Setup default font colors
    $colors = array(
      array( 'css' => 'var(--fg-tinted)', 'name' => _x( 'Tinted', 'Chapter font color name.', 'fictioneer' ) ),
      array( 'css' => 'var(--fg-500)', 'name' => _x( 'Baseline', 'Chapter font color name.', 'fictioneer' ) ),
      array( 'css' => 'var(--fg-600)', 'name' => _x( 'Low', 'Chapter font color name.', 'fictioneer' ) ),
      array( 'css' => 'var(--fg-700)', 'name' => _x( 'Lower', 'Chapter font color name.', 'fictioneer' ) ),
      array( 'css' => 'var(--fg-800)', 'name' => _x( 'Lowest', 'Chapter font color name.', 'fictioneer' ) ),
      array( 'css' => 'var(--fg-400)', 'name' => _x( 'High', 'Chapter font color name.', 'fictioneer' ) ),
      array( 'css' => 'var(--fg-300)', 'name' => _x( 'Higher', 'Chapter font color name.', 'fictioneer' ) ),
      array( 'css' => 'var(--fg-200)', 'name' => _x( 'Highest', 'Chapter font color name.', 'fictioneer' ) ),
      array( 'css' => '#fff', 'name' => _x( 'White', 'Chapter font color name.', 'fictioneer' ) ),
      array( 'css' => '#999', 'name' => _x( 'Gray', 'Chapter font color name.', 'fictioneer' ) ),
      array( 'css' => '#000', 'name' => _x( 'Black', 'Chapter font color name.', 'fictioneer' ) )
    );

    // Apply filters and return
    return apply_filters( 'fictioneer_filter_font_colors', $colors );
  }
}

// =============================================================================
// ARRAY FROM COMMA SEPARATED STRING
// =============================================================================

/**
 * Explodes string into an array
 *
 * Strips lines breaks, trims whitespaces, and removes empty elements.
 * Values might not be unique.
 *
 * @since 5.1.3
 *
 * @param string $string  The string to explode.
 *
 * @return array The string content as array.
 */

function fictioneer_explode_list( $string ) {
  if ( empty( $string ) ) {
    return [];
  }

  $string = str_replace( ["\n", "\r"], '', $string ); // Remove line breaks
  $array = explode( ',', $string );
  $array = array_map( 'trim', $array ); // Remove extra whitespaces
  $array = array_filter( $array, 'strlen' ); // Remove empty elements
  $array = is_array( $array ) ? $array : [];

  return $array;
}

// =============================================================================
// BUILD FRONTEND NOTICE
// =============================================================================

if ( ! function_exists( 'fictioneer_notice' ) ) {
  /**
   * Render or return a frontend notice element
   *
   * @since 5.2.5
   *
   * @param string $message   The notice to show.
   * @param string $type      Optional. The notice type. Default 'warning'.
   * @param bool   $display   Optional. Whether to render or return. Default true.
   *
   * @return void|string The build HTML or nothing if rendered.
   */

  function fictioneer_notice( $message, $type = 'warning', $display = true ) {
    $output = '<div class="notice _' . esc_attr( $type ) . '">';

    if ( $type === 'warning' ) {
      $output .= '<i class="fa-solid fa-triangle-exclamation"></i>';
    }

    $output .= "<div>{$message}</div></div>";

    if ( $display ) {
      echo $output;
    } else {
      return $output;
    }
  }
}

// =============================================================================
// MINIFY HTML
// =============================================================================

if ( ! function_exists( 'fictioneer_minify_html' ) ) {
  /**
   * Minifies a HTML string
   *
   * This is not safe for `<pre>` or `<code>` tags!
   *
   * @since 5.4.0
   *
   * @param string $html  The HTML string to be minified.
   *
   * @return string The minified HTML string.
   */

  function fictioneer_minify_html( $html ) {
    return preg_replace( '/\s+/', ' ', trim( $html ) );
  }
}

// =============================================================================
// MINIFY CSS
// =============================================================================

if ( ! function_exists( 'fictioneer_minify_css' ) ) {
  /**
   * Minify CSS.
   *
   * @license CC BY-SA 4.0
   * @author Qtax https://stackoverflow.com/users/107152/qtax
   * @author lots0logs https://stackoverflow.com/users/2639936/lots0logs
   *
   * @since 4.7.0
   * @link https://stackoverflow.com/a/15195752/17140970
   * @link https://stackoverflow.com/a/44350195/17140970
   *
   * @param string $string  The to be minified CSS string.
   *
   * @return string The minified CSS string.
   */

  function fictioneer_minify_css( $string = '' ) {
    $comments = <<<'EOS'
    (?sx)
        # don't change anything inside of quotes
        ( "(?:[^"\\]++|\\.)*+" | '(?:[^'\\]++|\\.)*+' )
    |
        # comments
        /\* (?> .*? \*/ )
    EOS;

    $everything_else = <<<'EOS'
    (?six)
        # don't change anything inside of quotes
        ( "(?:[^"\\]++|\\.)*+" | '(?:[^'\\]++|\\.)*+' )
    |
        # spaces before and after ; and }
        \s*+ ; \s*+ ( } ) \s*+
    |
        # all spaces around meta chars/operators (excluding + and -)
        \s*+ ( [*$~^|]?+= | [{};,>~] | !important\b ) \s*+
    |
        # all spaces around + and - (in selectors only!)
        \s*([+-])\s*(?=[^}]*{)
    |
        # spaces right of ( [ :
        ( [[(:] ) \s++
    |
        # spaces left of ) ]
        \s++ ( [])] )
    |
        # spaces left (and right) of : (but not in selectors)!
        \s+(:)(?![^\}]*\{)
    |
        # spaces at beginning/end of string
        ^ \s++ | \s++ \z
    |
        # double spaces to single
        (\s)\s+
    EOS;

    $search_patterns  = array( "%{$comments}%", "%{$everything_else}%" );
    $replace_patterns = array( '$1', '$1$2$3$4$5$6$7$8' );

    return preg_replace( $search_patterns, $replace_patterns, $string );
  }
}

// =============================================================================
// GET CLEAN CURRENT URL
// =============================================================================

if ( ! function_exists( 'fictioneer_get_clean_url' ) ) {
  /**
   * Returns URL without query arguments or page number
   *
   * @since 5.4.0
   *
   * @return string The clean URL.
   */

  function fictioneer_get_clean_url() {
    global $wp;

    // Setup
    $url = home_url( $wp->request );

    // Remove page (if any)
    $url = preg_replace( '/\/page\/\d+\/$/', '', $url );
    $url = preg_replace( '/\/page\/\d+$/', '', $url );

    // Return cleaned URL
    return $url;
  }
}

// =============================================================================
// GET AUTHOR IDS OF POST
// =============================================================================

if ( ! function_exists( 'fictioneer_get_post_author_ids' ) ) {
  /**
   * Returns array of author IDs for a post ID
   *
   * @since 5.4.8
   *
   * @param int $post_id  The post ID.
   *
   * @return array The author IDs.
   */

  function fictioneer_get_post_author_ids( $post_id ) {
    $author_ids = get_post_meta( $post_id, 'fictioneer_story_co_authors', true ) ?: [];
    $author_ids = is_array( $author_ids ) ? $author_ids : [];
    array_unshift( $author_ids, get_post_field( 'post_author', $post_id ) );

    return array_unique( $author_ids );
  }
}

// =============================================================================
// DELETE TRANSIENTS THAT INCLUDE A STRING
// =============================================================================

if ( ! function_exists( 'fictioneer_delete_transients_like' ) ) {
  /**
   * Delete Transients with a like key and return the count deleted
   *
   * Note: The fast variant of this function is not compatible with
   * external object caches such as Memcached, because without the
   * help of delete_transient(), it won't know about the change.
   *
   * @since 5.4.9
   *
   * @param string  $partial_key  String that is part of the key.
   * @param boolean $fast         Optional. Whether to delete with a single SQL query or
   *                              loop each Transient with delete_transient(), which can
   *                              trigger hooked actions (if any). Default false.
   *
   * @return int Count of deleted Transients.
   */

  function fictioneer_delete_transients_like( $partial_key, $fast = false ) {
    // Globals
    global $wpdb;

    // Setup
    $count = 0;

    // Fast?
    if ( $fast ) {
      // Prepare SQL
      $sql = $wpdb->prepare(
        "DELETE FROM $wpdb->options WHERE `option_name` LIKE %s OR `option_name` LIKE %s",
        "%_transient%{$partial_key}%",
        "%_transient_timeout%{$partial_key}%"
      );

      // Query
      $count = $wpdb->query( $sql );
    } else {
      // Prepare SQL
      $sql = $wpdb->prepare(
        "SELECT `option_name` AS `name` FROM $wpdb->options WHERE `option_name` LIKE %s",
        "_transient%{$partial_key}%"
      );

      // Query
      $transients = $wpdb->get_col( $sql );

      // Build full keys and delete
      foreach ( $transients as $transient ) {
        $key = str_replace( '_transient_', '', $transient );
        $count += delete_transient( $key ) ? 1 : 0;
      }
    }

    // Return count
    return $count;
  }
}

// =============================================================================
// GET OPTION PAGE LINK
// =============================================================================

if ( ! function_exists( 'fictioneer_get_assigned_page_link' ) ) {
  /**
   * Returns permalink for an assigned page or null
   *
   * @since 5.4.9
   *
   * @param string $option  The option name of the page assignment.
   *
   * @return string|null The permalink or null.
   */

  function fictioneer_get_assigned_page_link( $option ) {
    // Setup
    $page_id = get_option( $option );

    // Null if no page has been selected (null or -1)
    if ( empty( $page_id ) || $page_id < 0 ) {
      return null;
    }

    // Get permalink from options or post
    $link = get_option( "{$option}_link" );

    if ( empty( $link ) ) {
      $link = get_permalink( $page_id );
      update_option( "{$option}_link", $link, true ); // Save for next time
    }

    // Return
    return $link;
  }
}

// =============================================================================
// MULTI SAVE GUARD
// =============================================================================

if ( ! function_exists( 'fictioneer_multi_save_guard' ) ) {
  /**
   * Prevents multi-fire in update hooks
   *
   * Unfortunately, the block editor always fires twice: once as REST request and
   * followed by WP_POST. Only the first will have the correct parameters such as
   * $update set, the second is technically no longer an update. Since blocking
   * the follow-up WP_POST would block programmatically triggered actions, there
   * is no other choice but to block the REST request and live with it.
   *
   * @since 5.5.2
   *
   * @param int $post_id  The ID of the updated post.
   *
   * @return boolean True if NOT allowed, false otherwise.
   */

  function fictioneer_multi_save_guard( $post_id ) {
    // Always allow trash action to pass
    if ( get_post_status( $post_id ) === 'trash' ) {
      return false;
    }

    // Block REST requests and unnecessary triggers
    if (
      ( defined( 'REST_REQUEST' ) && REST_REQUEST && ! get_option( 'fictioneer_allow_rest_save_actions' ) ) ||
      wp_is_post_autosave( $post_id ) ||
      wp_is_post_revision( $post_id ) ||
      get_post_status( $post_id ) === 'auto-draft'
    ) {
      return true;
    }

    // Pass
    return false;
  }
}

// =============================================================================
// GET TOTAL WORD COUNT FOR ALL STORIES
// =============================================================================

if ( ! function_exists( 'fictioneer_get_stories_total_word_count' ) ) {
  /**
   * Returns the total word count of all published stories
   *
   * Note: Does not include standalone chapters for performance reasons.
   *
   * @since 4.0.0
   * @since 5.22.3 - Refactored with SQL query for better performance.
   *
   * @return int The word count of all published stories.
   */

  function fictioneer_get_stories_total_word_count() {
    // Look for cached value (purged after each update, should never be stale)
    $transient_word_count_cache = get_transient( 'fictioneer_stories_total_word_count' );

    // Return cached value if found
    if ( $transient_word_count_cache ) {
      return $transient_word_count_cache;
    }

    global $wpdb;

    // Setup
    $words = 0;

    // Sum of all word counts
    $words = $wpdb->get_var(
      $wpdb->prepare(
        "
        SELECT SUM(CAST(pm.meta_value AS UNSIGNED))
        FROM $wpdb->postmeta AS pm
        INNER JOIN $wpdb->posts AS p ON pm.post_id = p.ID
        WHERE pm.meta_key = %s
        AND p.post_type = %s
        AND p.post_status = %s
        ",
        'fictioneer_story_total_word_count',
        'fcn_story',
        'publish'
      )
    );

    // Customize
    $words = fictioneer_multiply_word_count( $words );

    // Cache for next time
    set_transient( 'fictioneer_stories_total_word_count', $words, DAY_IN_SECONDS );

    // Return newly calculated value
    return $words;
  }
}

// =============================================================================
// REDIRECT TO 404
// =============================================================================

if ( ! function_exists( 'fictioneer_redirect_to_404' ) ) {
  /**
   * Redirects the current request to the WordPress 404 page
   *
   * @since 5.6.0
   *
   * @global WP_Query $wp_query  The main WP_Query instance.
   */

  function fictioneer_redirect_to_404() {
    global $wp_query;

    // Remove scripts to avoid errors
    add_action( 'wp_print_scripts', function() {
      wp_dequeue_script( 'fictioneer-chapter-scripts' );
      wp_dequeue_script( 'fictioneer-suggestion-scripts' );
      wp_dequeue_script( 'fictioneer-tts-scripts' );
      wp_dequeue_script( 'fictioneer-story-scripts' );
    }, 99 );

    // Set query to 404
    $wp_query->set_404();
    status_header( 404 );
    nocache_headers();
    get_template_part( 404 );

    // Terminate
    exit();
  }
}

// =============================================================================
// PREVIEW ACCESS VERIFICATION
// =============================================================================

if ( ! function_exists( 'fictioneer_verify_unpublish_access' ) ) {
  /**
   * Verifies access to unpublished posts (not drafts)
   *
   * @since 5.6.0
   *
   * @return boolean True if access granted, false otherwise.
   */

  function fictioneer_verify_unpublish_access( $post_id ) {
    // Setup
    $post = get_post( $post_id );
    $authorized = false;

    // Always let owner to pass
    if ( get_current_user_id() === absint( $post->post_author ) ) {
      $authorized = true;
    }

    // Always let administrators pass
    if ( current_user_can( 'manage_options' ) ) {
      $authorized = true;
    }

    // Check capability for post type
    if ( $post->post_status === 'private' ) {
      switch ( $post->post_type ) {
        case 'post':
          $authorized = current_user_can( 'edit_private_posts' );
          break;
        case 'page':
          $authorized = current_user_can( 'edit_private_pages' );
          break;
        case 'fcn_chapter':
          $authorized = current_user_can( 'read_private_fcn_chapters' );
          break;
        case 'fcn_story':
          $authorized = current_user_can( 'read_private_fcn_stories' );
          break;
        case 'fcn_recommendation':
          $authorized = current_user_can( 'read_private_fcn_recommendations' );
          break;
        case 'fcn_collection':
          $authorized = current_user_can( 'read_private_fcn_collections' );
          break;
        default:
          $authorized = current_user_can( 'edit_others_posts' );
      }
    }

    // Drafts are handled by WordPress
    return apply_filters( 'fictioneer_filter_verify_unpublish_access', $authorized, $post_id, $post );
  }
}

// =============================================================================
// TOGGLE ACTION TO SAVE/TRASH/UNTRASH/DELETE HOOKS WITH POST ID
// =============================================================================

/**
 * Toggle callback to save, trash, untrash, and delete hooks (1 argument).
 *
 * This helper saves some time/space adding or removing a callback action to
 * all four default post operations. But only with the first argument: post_id.
 *
 * @since 5.6.3
 *
 * @param callable $function  The callback function to be added.
 * @param bool     $add       Optional. Whether to add or remove. Default add.
 * @param int      $priority  Optional. Used to specify the order in which the
 *                            functions associated with a particular action are
 *                            executed. Default 10. Lower numbers correspond with
 *                            earlier execution, and functions with the same
 *                            priority are executed in the order in which they
 *                            were added to the action.
 */

function fictioneer_toggle_stud_actions( $function, $add = true, $priority = 10 ) {
  $hooks = ['save_post', 'trashed_post', 'delete_post', 'untrash_post'];

  foreach ( $hooks as $hook ) {
    $add ? add_action( $hook, $function, $priority ) : remove_action( $hook, $function, $priority );
  }
}

// =============================================================================
// CONVERT URL LIST TO ARRAY
// =============================================================================

/**
 * Turn line-break separated list into array of links
 *
 * @since 5.7.4
 *
 * @param string $list  The list of links
 *
 * @return array The array of links.
 */

function fictioneer_url_list_to_array( $list ) {
  // Already array?
  if ( is_array( $list ) ) {
    return $list;
  }

  // Catch falsy values
  if ( empty( $list ) ) {
    return [];
  }

  // Prepare URLs
  $urls = [];
  $lines = explode( "\n", $list );

  // Extract
  foreach ( $lines as $line ) {
    $tuple = explode( '|', $line );
    $tuple = array_map( 'trim', $tuple );

    $urls[] = array(
      'name' => wp_strip_all_tags( $tuple[0] ),
      'url' => fictioneer_sanitize_url( $tuple[1] )
    );
  }

  // Return
  return $urls;
}

// =============================================================================
// ARRAY OPERATIONS
// =============================================================================

/**
 * Unset array element by value
 *
 * @since 5.7.5
 *
 * @param mixed $value  The value to look for.
 * @param array $array  The array to be modified.
 *
 * @return array The modified array.
 */

function fictioneer_unset_by_value( $value, $array ) {
  if ( ( $key = array_search( $value, $array ) ) !== false ) {
    unset( $array[ $key ] );
  }

  return $array;
}

// =============================================================================
// RETURN NO FORMAT STRING
// =============================================================================

/**
 * Returns an unformatted replacement string
 *
 * @since 5.7.5
 *
 * @return string Just a simple '%s'.
 */

function fictioneer__return_no_format() {
  return '%s';
}

// =============================================================================
// TRUNCATE STRING
// =============================================================================

/**
 * Returns a truncated string without tags
 *
 * @since 5.9.0
 *
 * @param string      $string    The string to truncate.
 * @param int         $length    Maximum length in characters.
 * @param string|null $ellipsis  Optional. Truncation indicator suffix.
 *
 * @return string The truncated string without tags.
 */

function fictioneer_truncate( string $string, int $length, string $ellipsis = null ) {
  // Setup
  $string = wp_strip_all_tags( $string ); // Prevent tags from being cut off
  $ellipsis = $ellipsis ?? FICTIONEER_TRUNCATION_ELLIPSIS;

  // Return truncated string
  if ( function_exists( 'mb_strimwidth' ) ) {
    return mb_strimwidth( $string, 0, $length, $ellipsis );
  } else {
    return strlen( $string ) > $length ? substr( $string, 0, $length ) . $ellipsis : $string;
  }
}

// =============================================================================
// COMPARE WORDPRESS VERSION
// =============================================================================

/**
 * Compare installed WordPress version against version string
 *
 * @since 5.12.2
 * @global wpdb $wp_version  Current WordPress version string.
 *
 * @param string $version   The version string to test against.
 * @param string $operator  Optional. How to compare. Default '>='.
 *
 * @return boolean True or false.
 */

function fictioneer_compare_wp_version( $version, $operator = '>=' ) {
  global $wp_version;

  return version_compare( $wp_version, $version, $operator );
}

// =============================================================================
// CSS LOADING PATTERN
// =============================================================================

if ( ! function_exists( 'fictioneer_get_async_css_loading_pattern' ) ) {
  /**
   * Returns the media attribute and loading strategy for stylesheets
   *
   * @since 5.12.2
   *
   * @return string Media attribute script for stylesheet links.
   */

  function fictioneer_get_async_css_loading_pattern() {
    if ( FICTIONEER_ENABLE_ASYNC_ONLOAD_PATTERN ) {
      return 'media="print" onload="this.media=\'all\'; this.onload=null;"';
    }

    return 'media="all"';
  }
}

// =============================================================================
// GENERATE PLACEHOLDER
// =============================================================================

if ( ! function_exists( 'fictioneer_generate_placeholder' ) ) {
  /**
   * Dummy implementation (currently used a CSS background)
   *
   * @since 5.14.0
   *
   * @param array|null $args  Optional arguments to generate a placeholder.
   *
   * @return string The placeholder URL or data URI.
   */

  function fictioneer_generate_placeholder( $args = [] ) {
    return '';
  }
}

// =============================================================================
// FIND USER(S)
// =============================================================================

/**
 * Find (first) user by display name
 *
 * @since 5.14.1
 *
 * @param string $display_name  The display name to search for.
 *
 * @return WP_User|null The first matching user or null if not found.
 */

function fictioneer_find_user_by_display_name( $display_name ) {
  // No choice but to query all because the display name is not unique
  $users = get_users(
    array(
      'search' => sanitize_user( $display_name ),
      'search_columns' => ['display_name'],
      'number' => 1
    )
  );

  // If found, return first
  if ( ! empty( $users ) ) {
    return $users[0];
  }

  // Not found
  return null;
}

// =============================================================================
// JOIN ARRAYS IN SPECIAL WAYS
// =============================================================================

if ( ! function_exists( 'fictioneer_get_human_readable_list' ) ) {
  /**
   * Join string in an array as human readable list.
   *
   * @since 5.15.0
   * @link https://gist.github.com/SleeplessByte/4514697
   *
   * @param array $array  Array of strings.
   *
   * @return string The human readable list.
   */

  function fictioneer_get_human_readable_list( $array ) {
    // Setup
    $comma = _x( ', ', 'Human readable list joining three or more items except the last two.', 'fictioneer' );
    $double = _x( ' or ', 'Human readable list joining two items.', 'fictioneer' );
    $final = _x( ', or ', 'Human readable list joining the last two of three or more items.', 'fictioneer' );

    // One or two items
    if ( count( $array ) < 3 ) {
      return implode( $double, $array );
    }

    // Three or more items
    array_splice( $array, -2, 2, implode( $final, array_slice( $array, -2, 2 ) ) );

    // Finish
    return implode( $comma , $array );
  }
}

// =============================================================================
// PATREON UTILITIES
// =============================================================================

/**
 * Get Patreon data for a post
 *
 * Note: Considers both the post meta and global settings.
 *
 * @since 5.15.0
 *
 * @param WP_Post|null $post  The post to check. Default to global $post.
 *
 * @return array|null Array with 'gated' (bool), 'gate_tiers' (array), 'gate_cents' (int),
 *                    and 'gate_lifetime_cents' (int). Null if the post could not be found.
 */

function fictioneer_get_post_patreon_data( $post = null ) {
  // Static cache
  static $cache = [];

  // Post?
  $post = $post ?? get_post();
  $post_id = $post->ID;

  if ( ! $post ) {
    return null;
  }

  // Check cache
  if ( isset( $cache[ $post_id ] ) ) {
    return $cache[ $post_id ];
  }

  // Setup
  $global_tiers = get_option( 'fictioneer_patreon_global_lock_tiers', [] ) ?: [];
  $global_amount_cents = get_option( 'fictioneer_patreon_global_lock_amount', 0 ) ?: 0;
  $global_lifetime_amount_cents = get_option( 'fictioneer_patreon_global_lock_lifetime_amount', 0 ) ?: 0;

  $post_tiers = get_post_meta( $post_id, 'fictioneer_patreon_lock_tiers', true );
  $post_tiers = is_array( $post_tiers ) ? $post_tiers : [];
  $post_amount_cents = absint( get_post_meta( $post_id, 'fictioneer_patreon_lock_amount', true ) );

  $parent_tiers = [];
  $parent_amount_cents = 0;

  if ( $post->post_type === 'fcn_chapter' ) {
    $parent_id = fictioneer_get_chapter_story_id( $post_id );

    if ( $parent_id && get_post_meta( $parent_id, 'fictioneer_patreon_inheritance', true ) ) {
      $parent_tiers = get_post_meta( $parent_id, 'fictioneer_patreon_lock_tiers', true );
      $parent_tiers = is_array( $parent_tiers ) ? $parent_tiers : [];
      $parent_amount_cents = absint( get_post_meta( $parent_id, 'fictioneer_patreon_lock_amount', true ) );
    }
  }

  $check_tiers = array_merge( $global_tiers, $parent_tiers, $post_tiers );
  $check_tiers = array_unique( $check_tiers );

  $check_amount_cents = $post_amount_cents > 0 ? $post_amount_cents : $parent_amount_cents;
  $check_amount_cents = $check_amount_cents > 0 ? $check_amount_cents : $global_amount_cents;
  $check_amount_cents = $post_amount_cents > 0 && $global_amount_cents > 0
    ? min( $post_amount_cents, $global_amount_cents ) : $check_amount_cents;
  $check_amount_cents = max( $check_amount_cents, 0 );

  // Compile
  $data = array(
    'gated' => $check_tiers || $check_amount_cents > 0,
    'gate_tiers' => $check_tiers,
    'gate_cents' => $check_amount_cents,
    'gate_lifetime_cents' => $global_lifetime_amount_cents
  );

  // Cache
  $cache[ $post_id ] = $data;

  // Return
  return $data;
}

// =============================================================================
// ENCRYPTION/DECRYPTION
// =============================================================================

/**
 * Encrypt data
 *
 * @since 5.19.0
 *
 * @param mixed $data  The data to encrypt.
 *
 * @return string|false The encrypted data or false on failure.
 */

function fictioneer_encrypt( $data ) {
  $key = wp_salt();

  $encrypted = openssl_encrypt(
    json_encode( $data ),
    'aes-256-cbc',
    $key,
    0,
    substr( hash( 'sha256', $key ), 0, 16 )
  );

  if ( $encrypted === false ) {
    return false;
  }

  return base64_encode( $encrypted );
}

/**
 * Decrypt data
 *
 * @since 5.19.0
 *
 * @param string $data  The data to decrypt.
 *
 * @return mixed The decrypted data.
 */

function fictioneer_decrypt( $data ) {
  $key = wp_salt();

  $decrypted = openssl_decrypt(
    base64_decode( $data ),
    'aes-256-cbc',
    $key,
    0,
    substr( hash( 'sha256', $key ), 0, 16 )
  );

  return json_decode( $decrypted, true );
}

// =============================================================================
// RANDOM USERNAME
// =============================================================================

/**
 * Returns array of adjectives for randomized username generation
 *
 * @since 5.19.0
 *
 * @return array Array of nouns.
 */

function fictioneer_get_username_adjectives() {
  $adjectives = array(
    'Radical', 'Tubular', 'Gnarly', 'Epic', 'Electric', 'Neon', 'Bodacious', 'Rad',
    'Totally', 'Funky', 'Wicked', 'Fresh', 'Chill', 'Groovy', 'Vibrant', 'Flashy',
    'Buff', 'Hella', 'Motor', 'Cyber', 'Pixel', 'Holo', 'Stealth', 'Synthetic',
    'Enhanced', 'Synth', 'Bio', 'Laser', 'Virtual', 'Analog', 'Mega', 'Wave', 'Solo',
    'Retro', 'Quantum', 'Robotic', 'Digital', 'Hyper', 'Punk', 'Giga', 'Electro',
    'Chrome', 'Fusion', 'Vivid', 'Stellar', 'Galactic', 'Turbo', 'Atomic', 'Cosmic',
    'Artificial', 'Kinetic', 'Binary', 'Hypersonic', 'Runic', 'Data', 'Knightly',
    'Cryonic', 'Nebular', 'Golden', 'Silver', 'Red', 'Crimson', 'Augmented', 'Vorpal',
    'Ascended', 'Serious', 'Solid', 'Master', 'Prism', 'Spinning', 'Masked', 'Hardcore',
    'Somber', 'Celestial', 'Arcane', 'Luminous', 'Ionized', 'Lunar', 'Uncanny', 'Subatomic',
    'Luminary', 'Radiant', 'Ultra', 'Starship', 'Space', 'Starlight', 'Interstellar', 'Metal',
    'Bionic', 'Machine', 'Isekai', 'Warp', 'Neo', 'Alpha', 'Power', 'Unhinged', 'Ash',
    'Savage', 'Silent', 'Screaming', 'Misty', 'Rending', 'Horny', 'Dreadful', 'Bizarre',
    'Chaotic', 'Wacky', 'Twisted', 'Manic', 'Crystal', 'Infernal', 'Ruthless', 'Grim',
    'Mortal', 'Forsaken', 'Heretical', 'Cursed', 'Blighted', 'Scarlet', 'Delightful',
    'Nuclear', 'Azure', 'Emerald', 'Amber', 'Mystic', 'Ethereal', 'Enchanted', 'Valiant',
    'Fierce', 'Obscure', 'Enigmatic'
  );

  return apply_filters( 'fictioneer_random_username_adjectives', $adjectives );
}

/**
 * Returns array of nouns for randomized username generation
 *
 * @since 5.19.0
 *
 * @return array Array of nouns.
 */

function fictioneer_get_username_nouns() {
  $nouns = array(
    'Avatar', 'Cassette', 'Rubiks', 'Gizmo', 'Synthwave', 'Tron', 'Replicant', 'Warrior',
    'Hacker', 'Samurai', 'Cyborg', 'Runner', 'Mercenary', 'Shogun', 'Maverick', 'Glitch',
    'Byte', 'Matrix', 'Motion', 'Shinobi', 'Circuit', 'Droid', 'Virus', 'Vortex', 'Mech',
    'Codex', 'Hologram', 'Specter', 'Intelligence', 'Technomancer', 'Rider', 'Ghost',
    'Hunter', 'Hound', 'Wizard', 'Knight', 'Rogue', 'Scout', 'Ranger', 'Paladin', 'Sorcerer',
    'Mage', 'Artificer', 'Cleric', 'Tank', 'Fighter', 'Pilot', 'Necromancer', 'Neuromancer',
    'Barbarian', 'Streetpunk', 'Phantom', 'Shaman', 'Druid', 'Dragon', 'Dancer', 'Captain',
    'Pirate', 'Snake', 'Rebel', 'Kraken', 'Spark', 'Blitz', 'Alchemist', 'Dragoon', 'Geomancer',
    'Neophyte', 'Terminator', 'Tempest', 'Enigma', 'Automaton', 'Daemon', 'Juggernaut',
    'Paragon', 'Sentinel', 'Viper', 'Velociraptor', 'Spirit', 'Punk', 'Synth', 'Biomech',
    'Engineer', 'Pentagoose', 'Vampire', 'Soldier', 'Chimera', 'Lobotomy', 'Mutant',
    'Revenant', 'Wraith', 'Chupacabra', 'Banshee', 'Fae', 'Leviathan', 'Cenobite', 'Bob',
    'Ketchum', 'Collector', 'Student', 'Lover', 'Chicken', 'Alien', 'Titan', 'Sinner',
    'Nightmare', 'Bioplague', 'Annihilation', 'Elder', 'Priest', 'Guardian', 'Quagmire',
    'Berserker', 'Oblivion', 'Decimator', 'Devastation', 'Calamity', 'Doom', 'Ruin', 'Abyss',
    'Heretic', 'Armageddon', 'Obliteration', 'Inferno', 'Torment', 'Carnage', 'Purgatory',
    'Chastity', 'Angel', 'Raven', 'Star', 'Trinity', 'Idol', 'Eidolon', 'Havoc', 'Nirvana',
    'Digitron', 'Phoenix', 'Lantern', 'Warden', 'Falcon'
  );

  return apply_filters( 'fictioneer_random_username_nouns', $nouns );
}

/**
 * Returns randomized username
 *
 * @since 5.19.0
 *
 * @param bool $unique  Optional. Whether the username must be unique. Default true.
 *
 * @return string Sanitized random username.
 */

function fictioneer_get_random_username( $unique = true ) {
  // Setup
  $adjectives = fictioneer_get_username_adjectives();
  $nouns = fictioneer_get_username_nouns();

  // Shuffle the arrays to ensure more randomness
  shuffle( $adjectives );
  shuffle( $nouns );

  // Build username
  do {
    $username = $adjectives[ array_rand( $adjectives ) ] . $nouns[ array_rand( $nouns ) ] . rand( 1000, 9999 );
    $username = sanitize_user( $username, true );
  } while ( username_exists( $username ) && $unique );

  // Return username
  return $username;
}


// =============================================================================
// STRING LENGTH
// =============================================================================

if ( ! function_exists( 'mb_strlen' ) ) {
  /**
   * Fallback function for mb_strlen
   *
   * @param string $string    The string to being measured.
   * @param string $encoding  The character encoding. Default UTF-8.
   *
   * @return int The number of characters in the string.
   */

  function mb_strlen( $string, $encoding = 'UTF-8' ) {
    if ( $encoding !== 'UTF-8' ) {
      return strlen( $string );
    }

    $converted_string = iconv( $encoding, 'UTF-16', $string );

    if ( $converted_string === false ) {
      return strlen( $string );
    } else {
      return strlen( $converted_string ) / 2; // Each character is 2 bytes in UTF-16
    }
  }
}

// =============================================================================
// GET ALL PUBLISHING AUTHORS
// =============================================================================

/**
 * Returns all authors with published posts
 *
 * Note: Qualified post types are fcn_story, fcn_chapter, fcn_recommendation,
 * and post. The result is cached for 12 hours as Transient.
 *
 * @since 5.24.0
 * @link https://developer.wordpress.org/reference/functions/get_users/
 *
 * @param array $args  Optional. Array of additional query arguments.
 *
 * @return array Array of WP_User object, stdClass objects, or IDs.
 */

function fictioneer_get_publishing_authors( $args = [] ) {
  static $authors = null;

  $key = 'fictioneer_publishing_authors_' . md5( serialize( $args ) );

  if ( ! $authors && $transient = get_transient( $key ) ) {
    $authors = $transient;
  }

  if ( $authors ) {
    return $authors;
  }

  $authors = get_users(
    array_merge(
      array( 'has_published_posts' => ['fcn_story', 'fcn_chapter', 'fcn_recommendation', 'post'] ),
      $args
    )
  );

  set_transient( $key, $authors, 12 * HOUR_IN_SECONDS );

  return $authors;
}

// =============================================================================
// GET POST LABELS
// =============================================================================

/**
 * Returns the translated label of the post status
 *
 * @since 5.24.5
 *
 * @param string $status  Post status.
 *
 * @return string Translated label of the post status or the post status if custom.
 */

function fictioneer_get_post_status_label( $status ) {
  static $labels = null;

  if ( ! $labels ) {
    $labels = array(
      'draft' => get_post_status_object( 'draft' )->label,
      'pending' => get_post_status_object( 'pending' )->label,
      'publish' => get_post_status_object( 'publish' )->label,
      'private' => get_post_status_object( 'private' )->label,
      'future' => get_post_status_object( 'future' )->label,
      'trash' => get_post_status_object( 'trash' )->label
    );
  }

  return $labels[ $status ] ?? $status;
}

/**
 * Returns the translated label of the post type
 *
 * @since 5.25.0
 *
 * @param string $type  Post type.
 *
 * @return string Translated label of the post type or the post type if custom.
 */

function fictioneer_get_post_type_label( $type ) {
  static $labels = null;

  if ( ! $labels ) {
    $labels = array(
      'post' => _x( 'Post', 'Post type label.', 'fictioneer' ),
      'page' => _x( 'Page', 'Post type label.', 'fictioneer' ),
      'fcn_story' => _x( 'Story', 'Post type label.', 'fictioneer' ),
      'fcn_chapter' => _x( 'Chapter', 'Post type label.', 'fictioneer' ),
      'fcn_collection' => _x( 'Collection', 'Post type label.', 'fictioneer' ),
      'fcn_recommendation' => _x( 'Rec', 'Post type label.', 'fictioneer' )
    );
  }

  return $labels[ $type ] ?? $type;
}

// =============================================================================
// LOGS
// =============================================================================

/**
 * Returns (or creates) secret log hash used to obscure the log file name
 *
 * @since 5.24.1
 *
 * @return string  The log hash.
 */

function fictioneer_get_log_hash() {
  $hash = strval( get_option( 'fictioneer_log_hash' ) );

  if ( ! empty( $hash ) ) {
    return $hash;
  }

  $hash = wp_generate_password( 32, false );

  update_option( 'fictioneer_log_hash', $hash, 'no' );

  return $hash;
}

/**
 * Log a message to the theme log file.
 *
 * @since 5.0.0
 *
 * @param string       $message  What has been updated
 * @param WP_User|null $user     The user who did it. Defaults to current user.
 */

function fictioneer_log( $message, $current_user = null ) {
  // Setup
  $current_user = $current_user ?? wp_get_current_user();
  $username = _x( 'System', 'Default name in logs.', 'fictioneer' );
  $log_hash = fictioneer_get_log_hash();
  $log_file = WP_CONTENT_DIR . "/fictioneer-{$log_hash}-log.log";
  $log_limit = 5000;
  $date = current_time( 'mysql', true );

  if ( is_object( $current_user ) && $current_user->ID > 0 ) {
    $username = $current_user->user_login . ' #' . $current_user->ID;
  }

  if ( empty( $current_user ) && wp_doing_cron() ) {
    $username = 'WP Cron';
  }

  if ( empty( $current_user ) && wp_doing_ajax() ) {
    $username = 'AJAX';
  }

  $username = empty( $username ) ? __( 'Anonymous', 'fictioneer' ) : $username;

  // Make sure the log file exists
  if ( ! file_exists( $log_file ) ) {
    file_put_contents( $log_file, '' );
  }

  // Read
  $log_contents = file_get_contents( $log_file );

  // Parse
  $log_entries = explode( "\n", $log_contents );

  // Limit (if too large)
  $log_entries = array_slice( $log_entries, -($log_limit + 1) );

  // Add new entry
  $log_entries[] = "[{$date} UTC] [{$username}] $message";

  // Concatenate and save
  file_put_contents( $log_file, implode( "\n", $log_entries ) );

  // Set file permissions
  chmod( $log_file, 0600 );

  // Security
  $silence = WP_CONTENT_DIR . '/index.php';

  if ( ! file_exists( $silence ) ) {
    file_put_contents( $silence, "<?php\n// Silence is golden.\n" );
    chmod( $silence, 0600 );
  }
}

/**
 * Retrieve the log entries and returns an HTML representation.
 *
 * @since 5.0.0
 *
 * @return string The HTML representation of the log entries.
 */

function fictioneer_get_log() {
  // Setup
  $log_hash = fictioneer_get_log_hash();
  $log_file = WP_CONTENT_DIR . "/fictioneer-{$log_hash}-log.log";
  $output = '';

  // Check whether log file exists
  if ( ! file_exists( $log_file ) ) {
    return '<ul class="fictioneer-log"><li class="fictioneer-log__item">No log entries yet.</li></ul>';
  }

  // Read
  $log_contents = file_get_contents( $log_file );

  // Parse
  $log_entries = explode( "\n", $log_contents );

  // Limit display to 250
  $log_entries = array_slice( $log_entries, -250 );

  // Reverse
  $log_entries = array_reverse( $log_entries );

  // Build list items
  foreach ( $log_entries as $entry ) {
    $output .= '<li class="fictioneer-log__item">' . esc_html( $entry ) . '</li>';
  }

  // Return HTML
  return '<ul class="fictioneer-log">' . $output . '</ul>';
}

/**
 * Retrieve the debug log entries and returns an HTML representation.
 *
 * @since 5.0.0
 *
 * @return string The HTML representation of the log entries.
 */

function fictioneer_get_wp_debug_log() {
  // Setup
  $log_file = WP_CONTENT_DIR . '/debug.log';
  $output = '';

  // Check whether log file exists
  if ( ! file_exists( $log_file ) ) {
    return '<ul class="fictioneer-log _wp-debug-log"><li class="fictioneer-log__item">No log entries yet.</li></ul>';
  }

  // Read
  $log_contents = file_get_contents( $log_file );

  // Parse
  $log_entries = explode( "\n", $log_contents );

  // Limit display to 250
  $log_entries = array_slice( $log_entries, -250 );

  // Reverse
  $log_entries = array_reverse( $log_entries );

  // Build list items
  foreach ( $log_entries as $entry ) {
    $output .= '<li class="fictioneer-log__item _wp-debug-log">' . esc_html( $entry ) . '</li>';
  }

  // Return HTML
  return '<ul class="fictioneer-log _wp-debug-log">' . $output . '</ul>';
}
