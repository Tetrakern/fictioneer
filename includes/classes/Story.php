<?php

namespace Fictioneer;

defined( 'ABSPATH' ) OR exit;

class Story {
  const META_CACHE_KEY = 'fictioneer_story_data_collection';

  /**
   * Get collection of a story's data.
   *
   * @since 4.3.0
   * @since 5.25.0 - Refactored with custom SQL query.
   * @since 5.34.0 - Refactored, split up, and moved into Story class.
   *
   * @param int|string $story_id       ID of the story.
   * @param bool       $show_comments  Optional. Whether the comment count is needed.
   *                                   Default true.
   * @param array      $args           Optional. Array of additional arguments.
   *
   * @return array|bool Data of the story or false if invalid.
   */

  public static function get_data( $story_id, $show_comments = true, $args = [] ) {
    $story_id = fictioneer_validate_id( $story_id, 'fcn_story' );

    if ( ! $story_id ) {
      return false;
    }

    $now = time();

    // Cache?
    $cache = self::get_cached_data_if_fresh( $story_id );

    if ( $cache ) {
      if ( ! $show_comments ) {
        return $cache;
      }

      if ( self::comment_count_refresh_required( $cache, $now, $args ) ) {
        $cache = self::refresh_comment_count( $story_id, $cache, $now );
      }

      return $cache;
    }

    // Build fresh
    $data = self::build_story_data( $story_id, $now );

    self::persist_story_data( $story_id, $data, $show_comments );

    return $data;
  }

  /**
   * Backfill parent ID for chapters that belong to a story.
   *
   * @since 5.34.0
   *
   * @param int|string $story_id     ID of the story.
   * @param array|null $chapter_ids  Optional. Chapter IDs associated with the story.
   *                                 Default null (will be fetched).
   * @param int|null   $limit        Optional. Max number of chapters to update in one call.
   *                                 Default 300. Set to -1 for no limit.
   *
   * @return int Number of affected rows.
   */

  public static function fix_chapter_parents( $story_id, $chapter_ids = null, $limit = null ) : int {
    global $wpdb;

    $story_id = fictioneer_validate_id( $story_id, 'fcn_story' );

    if ( ! $story_id ) {
      return 0;
    }

    if ( $chapter_ids === null ) {
      $chapter_ids = fictioneer_get_story_chapter_ids( $story_id );
    }

    if ( ! $chapter_ids ) {
      return 0;
    }

    $chapter_ids = array_values( array_unique( array_map( 'intval', $chapter_ids ) ) );

    if ( ! $chapter_ids ) {
      return 0;
    }

    if ( $limit === null ) {
      $limit = 300;
    }

    $limit = max( -1, min( 5000, (int) $limit ) );

    if ( $limit > 0 && count( $chapter_ids ) > $limit ) {
      $chapter_ids = array_slice( $chapter_ids, 0, $limit );
    }

    $ids_placeholder = implode( ',', array_fill( 0, count( $chapter_ids ), '%d' ) );
    $args = array_merge( [ $story_id ], $chapter_ids, [ $story_id ] );

    $wpdb->query(
      $wpdb->prepare(
        "UPDATE {$wpdb->posts}
        SET post_parent = %d
        WHERE post_type = 'fcn_chapter'
          AND ID IN ($ids_placeholder)
          AND post_parent <> %d",
        ...$args
      )
    );

    return (int) $wpdb->rows_affected;
  }

  /**
   * Return array of chapter posts for a story.
   *
   * @since 5.9.2
   * @since 5.22.3 - Refactored.
   * @since 5.34.0 - Optimized and moved into Story class.
   *
   * @param int|string $story_id  ID of the story.
   * @param array|null $args      Optional. Additional query arguments.
   * @param bool|null  $full      Optional. Whether to not reduce the posts. Default false.
   * @param bool|null  $slow      Optional. Whether to skip the fast query (if enabled). Default false.
   *
   * @return array Array of chapter posts or empty.
   */

  public static function get_chapter_posts( $story_id, $args = [], $full = false, $slow = false ) : array {
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
      'update_post_meta_cache' => true, // Required
      'update_post_term_cache' => false, // Improve performance
      'no_found_rows' => true // Improve performance
    );

    // Apply filters and custom arguments
    $query_args = array_merge( $query_args, $args );
    $query_args = apply_filters( 'fictioneer_filter_story_chapter_posts_query', $query_args, $story_id, $chapter_ids );

    $query_args['orderby'] = 'none'; // Not documented, prevents orderby from being used at all
    unset( $query_args['order'] );

    // Faster query?
    if ( ! $slow && get_option( 'fictioneer_enable_fast_chapter_posts' ) ) {
      return self::get_fast_chapter_posts( $story_id, $query_args, $full );
    }

    // Static cache key
    $cache_key = $story_id . '_' . md5( wp_json_encode( [ $query_args, $full ] ) );

    // Static cache hit?
    if ( isset( $cached_results[ $cache_key ] ) ) {
      return $cached_results[ $cache_key ];
    }

    // Batched or one go?
    $batch_limit = (int) apply_filters( 'fictioneer_filter_query_batch_limit', 800, 'story_chapter_posts' );
    $batch_limit = max( 100, min( 2000, $batch_limit ) );

    $by_id = [];

    foreach ( array_chunk( $chapter_ids, $batch_limit ) as $batch ) {
      $batch = $batch ?: [ 0 ];

      $query_args['post__in'] = $batch;
      $query_args['posts_per_page'] = count( $batch );

      $chapter_query = new \WP_Query( $query_args );

      foreach ( $chapter_query->posts as $chapter_post ) {
        $by_id[ $chapter_post->ID ] = $chapter_post;
      }
    }

    // Restore order
    $ordered_chapter_posts = [];

    foreach ( $chapter_ids as $cid ) {
      $cid = (int) $cid;

      if ( isset( $by_id[ $cid ] ) ) {
        $ordered_chapter_posts[] = $by_id[ $cid ];
      }
    }

    // Cache for subsequent calls (non-persistent)
    $cached_results[ $cache_key ] = $ordered_chapter_posts;

    // Return chapters selected in story
    return $ordered_chapter_posts;
  }

  /**
   * Return array of chapter post-like objects for a story using fast SQL.
   *
   * @since 5.34.0
   *
   * @param int|string $story_id  ID of the story.
   * @param array      $args      Optional. Additional query arguments (WP_Query-like).
   * @param bool       $full      Optional. Whether to include post_content. Default false.
   *
   * @return array Array of post-like objects in order (keyed by ID).
   */

  public static function get_fast_chapter_posts( $story_id, $args = [], $full = false ) : array {
    global $wpdb;

    $story_id = fictioneer_validate_id( $story_id, 'fcn_story' );

    if ( ! $story_id ) {
      return [];
    }

    $chapter_ids = fictioneer_get_story_chapter_ids( $story_id );

    if ( ! $chapter_ids ) {
      return [];
    }

    if ( ! empty( $args['post__in'] ) && is_array( $args['post__in'] ) ) {
      $allowed = array_fill_keys( array_map( 'intval', $args['post__in'] ), true );

      $chapter_ids = array_values(
        array_filter(
          array_map( 'intval', $chapter_ids ),
          static function( $id ) use ( $allowed ) { return isset( $allowed[ $id ] ); }
        )
      );

      if ( ! $chapter_ids ) {
        return [];
      }
    } else {
      $chapter_ids = array_values( array_unique( array_map( 'intval', $chapter_ids ) ) );
    }

    $statuses = $args['post_status'] ?? ['publish'];
    $statuses = is_array( $statuses ) ? $statuses : [ (string) $statuses ];
    $statuses = array_values( array_unique( array_filter( array_map( 'sanitize_key', $statuses ) ) ) );

    if ( ! $statuses ) {
      return [];
    }

    $posts_per_page = isset( $args['posts_per_page'] ) ? (int) $args['posts_per_page'] : -1;

    if ( $posts_per_page > 0 && count( $chapter_ids ) > $posts_per_page ) {
      $chapter_ids = array_slice( $chapter_ids, 0, $posts_per_page );
    }

    $batch_limit = (int) apply_filters( 'fictioneer_filter_query_batch_limit', 800, 'fast_story_chapter_posts' );
    $batch_limit = max( 100, min( 2000, $batch_limit ) );

    $fields = $full
      ? 'p.ID, p.post_author, p.post_date, p.post_date_gmt, p.post_modified, p.post_modified_gmt,
        p.post_title, p.post_content, p.post_excerpt, p.post_status, p.post_password, p.post_name,
        p.post_parent, p.menu_order, p.post_type, p.comment_count'
      : 'p.ID, p.post_author, p.post_date, p.post_date_gmt, p.post_modified, p.post_modified_gmt,
        p.post_title, p.post_excerpt, p.post_status, p.post_password, p.post_name,
        p.post_parent, p.menu_order, p.post_type, p.comment_count';

    $meta_keys = array(
      'fictioneer_chapter_group',
      'fictioneer_chapter_icon',
      'fictioneer_chapter_text_icon',
      'fictioneer_chapter_prefix',
      'fictioneer_chapter_list_title',
      'fictioneer_chapter_warning',
      '_word_count'
    );

    $meta_keys = apply_filters( 'fictioneer_filter_fast_story_chapter_posts_meta_keys', $meta_keys, $story_id, $args, $full );

    // === WP CACHE ===

    $cache_group = 'fictioneer_fast_chapter_posts';

    $cache_args = array(
      'post__in' => $args['post__in'] ?? null,
      'post_status' => $statuses,
      'posts_per_page' => $posts_per_page
    );

    $cache_version = self::get_story_chapter_cache_version( (int) $story_id );

    $cache_key = sprintf(
      'story:%d:v:%d:full:%d:a:%s:m:%s',
      $story_id,
      $cache_version,
      (int) $full,
      md5( wp_json_encode( $cache_args ) ),
      md5( implode( ',', $meta_keys ) )
    );

    $cached = wp_cache_get( $cache_key, $cache_group );

    if ( $cached !== false ) {
      return $cached;
    }

    // ================

    $by_id = [];

    foreach ( array_chunk( $chapter_ids, $batch_limit ) as $batch ) {
      if ( ! $batch ) {
        continue;
      }

      $id_placeholders = implode( ',', array_fill( 0, count( $batch ), '%d' ) );
      $status_placeholders = implode( ',', array_fill( 0, count( $statuses ), '%s' ) );
      $query_args = array_merge( $batch, $statuses );

      $sql = $wpdb->prepare(
        "SELECT {$fields}
        FROM {$wpdb->posts} p
        WHERE p.ID IN ({$id_placeholders})
          AND p.post_type = 'fcn_chapter'
          AND p.post_status IN ({$status_placeholders})",
        ...$query_args
      );

      $rows = $wpdb->get_results( $sql ) ?: [];

      if ( ! $rows ) {
        continue;
      }

      $batch_map = [];

      foreach ( $rows as $row ) {
        $row->meta = array_fill_keys( $meta_keys, '' );
        $id = (int) $row->ID;

        $by_id[ $id ] = $row;
        $batch_map[ $id ] = $row;
      }

      self::attach_meta_for_posts( $batch_map, $meta_keys );
    }

    if ( ! $by_id ) {
      return [];
    }

    $ordered = [];

    foreach ( $chapter_ids as $cid ) {
      if ( isset( $by_id[ $cid ] ) ) {
        $ordered[ $cid ] = $by_id[ $cid ];
      }
    }

    // === WP CACHE ===

    wp_cache_set( $cache_key, $ordered, $cache_group, self::get_chapter_posts_cache_ttl() );

    // ================

    return $ordered;
  }

  /**
   * Attach selected post meta to post-like objects as `->meta[key]`.
   *
   * @since 5.34.0
   *
   * @param array $by_id      Chapter objects keyed by ID.
   * @param array $meta_keys  Meta keys to load and attach.
   */

  protected static function attach_meta_for_posts( $by_id, $meta_keys ) : void {
    global $wpdb;

    $ids = array_values( array_map( 'intval', array_keys( $by_id ) ) );

    if ( ! $ids || ! $meta_keys ) {
      return;
    }

    $id_placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
    $meta_key_placeholders = implode( ',', array_fill( 0, count( $meta_keys ), '%s' ) );
    $query_args = array_merge( $ids, $meta_keys );

    $sql = $wpdb->prepare(
      "SELECT post_id, meta_key, meta_value
      FROM {$wpdb->postmeta}
      WHERE post_id IN ({$id_placeholders})
        AND meta_key IN ({$meta_key_placeholders})",
      ...$query_args
    );

    $rows = $wpdb->get_results( $sql ) ?: [];

    foreach ( $rows as $r ) {
      $pid = (int) $r->post_id;

      if ( isset( $by_id[ $pid ] ) ) {
        $by_id[ $pid ]->meta[ $r->meta_key ] = $r->meta_value;
      }
    }
  }

  /**
   * Get cached story data if fresh.
   *
   * @since 5.34.0
   *
   * @param int $story_id  ID of the story.
   *
   * @return array|null Story data or null.
   */

  protected static function get_cached_data_if_fresh( $story_id ) : ?array {
    if ( ! defined( 'FICTIONEER_ENABLE_STORY_DATA_META_CACHE' ) || ! FICTIONEER_ENABLE_STORY_DATA_META_CACHE ) {
      return null;
    }

    $meta_cache = get_post_meta( $story_id, self::META_CACHE_KEY, true );

    if ( empty( $meta_cache ) || ! is_array( $meta_cache ) ) {
      return null;
    }

    $last_modified = strtotime(
      get_post_field( 'post_modified_gmt', $story_id, 'raw' ) ?: get_post_field( 'post_modified', $story_id, 'raw' )
    );

    if ( (int) ( $meta_cache['last_modified'] ?? 0 ) >= (int) $last_modified ) {
      return $meta_cache;
    }

    return null;
  }

  /**
   * Check whether comment count must be updated.
   *
   * @since 5.34.0
   *
   * @param array $cache  Cached story data (if any).
   * @param int   $now    Unix timestamp in seconds.
   * @param array $args   Array of additional arguments.
   *
   * @return bool True if refresh is required, false otherwise.
   */

  protected static function comment_count_refresh_required( $cache, $now, $args ) : bool {
    $delay = (int) ( $cache['comment_count_timestamp'] ?? 0 ) + (int) FICTIONEER_STORY_COMMENT_COUNT_TIMEOUT;
    return $delay < $now || ! empty( $args['refresh_comment_count'] );
  }

  /**
   * Refresh comment count.
   *
   * @since 5.34.0
   *
   * @param int   $story_id  ID of the story.
   * @param array $cache     Cached story data (if any).
   * @param int   $now       Unix timestamp in seconds.
   *
   * @return array Cached story data with updated comment count.
   */

  protected static function refresh_comment_count( $story_id, $cache, $now ) : array {
    $comment_count = (int) ( $cache['comment_count'] ?? 0 );
    $chapter_ids = $cache['chapter_ids'] ?? [];

    if ( $chapter_ids ) {
      $comment_count = self::get_story_comment_count( $story_id, $chapter_ids );
    }

    $cache['comment_count'] = $comment_count;
    $cache['comment_count_timestamp'] = $now;

    $story_comment_count = (int) ( get_approved_comments( $story_id, array( 'count' => true ) ) ?: 0 );
    $new_total_comment_count = (int) $comment_count + $story_comment_count;
    $old_total_comment_count = (int) get_post_field( 'comment_count', $story_id, 'raw' );

    if ( $old_total_comment_count !== $new_total_comment_count ) {
      Utils_Admin::update_comment_count( $story_id, $new_total_comment_count );
    }

    if ( defined( 'FICTIONEER_ENABLE_STORY_DATA_META_CACHE' ) && FICTIONEER_ENABLE_STORY_DATA_META_CACHE ) {
      update_post_meta( $story_id, self::META_CACHE_KEY, $cache );
    }

    if ( function_exists( 'fictioneer_purge_post_cache' ) ) {
      fictioneer_purge_post_cache( $story_id );
    }

    return $cache;
  }

  /**
   * Returns the comment count of all story chapters
   *
   * Note: Includes comments from hidden and non-chapter chapters.
   *
   * @since 5.22.2
   * @since 5.22.3 - Switched to SQL query.
   * @since 5.34.0 - Mnd moved into Story class.
   *
   * @param int        $story_id     ID of the story.
   * @param array|null $chapter_ids  Optional. Array of chapter IDs.
   *
   * @return int Number of comments.
   */

  public static function get_story_comment_count( $story_id, $chapter_ids = null ) : int {
    $comment_count = 0;
    $chapter_ids = $chapter_ids ?? fictioneer_get_story_chapter_ids( $story_id );

    if ( empty( $chapter_ids ) ) {
      return 0;
    }

    global $wpdb;

    $batch_limit = (int) apply_filters( 'fictioneer_filter_query_batch_limit', 800, 'story_chapter_posts' );
    $batch_limit = max( 100, min( 2000, $batch_limit ) );

    foreach ( array_chunk( $chapter_ids, $batch_limit ) as $chunk ) {
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

    return $comment_count;
  }

  /**
   * Aggregate story data.
   *
   * @since 5.34.0
   *
   * @param int $story_id  ID of the story.
   * @param int $now       Unix timestamp in seconds.
   *
   * @return array Story data.
   */

  protected static function build_story_data( $story_id, $now ) : array {
    $status = get_post_meta( $story_id, 'fictioneer_story_status', true );
    $icon = Utils::get_story_status_icon( $status );
    $chapter_ids = fictioneer_get_story_chapter_ids( $story_id );

    $queried_statuses = apply_filters( 'fictioneer_filter_get_story_data_queried_chapter_statuses', ['publish'], $story_id );
    $indexed_statuses = apply_filters( 'fictioneer_filter_get_story_data_indexed_chapter_statuses', ['publish'], $story_id );

    $chapters = $chapter_ids
      ? self::query_chapter_aggregates( $chapter_ids, $queried_statuses, $story_id )
      : [];

    $chapter_count = 0;
    $word_count = 0;
    $comment_count = 0;

    $visible_ids = [];
    $indexed_ids = [];

    foreach ( $chapter_ids as $cid ) {
      if ( empty( $chapters[ $cid ] ) ) {
        continue;
      }

      $c = $chapters[ $cid ];

      $chapter_count++;
      $word_count += (int) ( $c->word_count ?? 0 );
      $visible_ids[] = (int) $cid;

      if ( in_array( $c->post_status, $indexed_statuses, true ) ) {
        $indexed_ids[] = (int) $cid;
      }

      $comment_count += (int) ( $c->comment_count ?? 0 );
    }

    $word_count += (int) get_post_meta( $story_id, '_word_count', true );
    $modified_word_count = fictioneer_multiply_word_count( $word_count );

    $rating = get_post_meta( $story_id, 'fictioneer_story_rating', true ) ?: 'Everyone';

    $tags = get_the_tags( $story_id );
    $fandoms = get_the_terms( $story_id, 'fcn_fandom' );
    $characters = get_the_terms( $story_id, 'fcn_character' );
    $warnings = get_the_terms( $story_id, 'fcn_content_warning' );
    $genres = get_the_terms( $story_id, 'fcn_genre' );

    $last_modified = strtotime(
      get_post_field( 'post_modified_gmt', $story_id, 'raw' ) ?: get_post_field( 'post_modified', $story_id, 'raw' )
    );

    return array(
      'id' => $story_id,
      'chapter_count' => $chapter_count,
      'word_count_raw' => $word_count,
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
      'title' => fictioneer_get_safe_title( $story_id, 'utility-get-story-data' ), // Legacy context
      'rating' => $rating,
      'rating_letter' => $rating !== '' ? $rating[0] : '',
      'chapter_ids' => $visible_ids,
      'indexed_chapter_ids' => $indexed_ids,
      'last_modified' => $last_modified,
      'comment_count' => $comment_count,
      'comment_count_timestamp' => $now,
      'redirect' => get_post_meta( $story_id, 'fictioneer_story_redirect_link', true )
    );
  }

  /**
   * Query aggregated data of chapters (delegate).
   *
   * @since 5.34.0
   *
   * @param array $chapter_ids       Chapter IDs.
   * @param array $queried_statuses  Statuses to be queried.
   * @param int   $story_id          ID of the story.
   *
   * @return array Chapter data keyed by ID (unordered).
   */

  protected static function query_chapter_aggregates( $chapter_ids, $queried_statuses, $story_id ) : array {
    if ( ! $queried_statuses ) {
      return [];
    }

    $threshold = (int) apply_filters( 'fictioneer_filter_get_story_data_switch_threshold', 225, $story_id );

    if ( count( $chapter_ids ) <= $threshold ) {
      return self::query_chapter_aggregates_small( $chapter_ids, $queried_statuses, $story_id );
    }

    return self::query_chapter_aggregates_large( $chapter_ids, $queried_statuses, $story_id );
  }

  /**
   * Query aggregated chapter data using multi-joins.
   *
   * @since 5.34.0
   *
   * @param array $chapter_ids       Chapter IDs.
   * @param array $queried_statuses  Statuses to be queried.
   * @param int   $story_id          ID of the story.
   *
   * @return array Chapter data keyed by ID (unordered).
   */

  protected static function query_chapter_aggregates_small( $chapter_ids, $queried_statuses, $story_id ) : array {
    global $wpdb;

    $ids_placeholder = implode( ',', array_fill( 0, count( $chapter_ids ), '%d' ) );
    $status_placeholders = implode( ',', array_fill( 0, count( $queried_statuses ), '%s' ) );

    // Select c.ID first so OBJECT_K keys by ID
    $sql = $wpdb->prepare(
      "SELECT
        c.ID,
        c.comment_count,
        c.post_status,
        CAST(wc.meta_value AS UNSIGNED) AS word_count
      FROM {$wpdb->posts} c
      LEFT JOIN {$wpdb->postmeta} wc ON wc.post_id = c.ID AND wc.meta_key = '_word_count'
      WHERE c.ID IN ($ids_placeholder)
        AND c.post_status IN ($status_placeholders)",
      ...$chapter_ids,
      ...$queried_statuses
    );

    $sql = apply_filters( 'fictioneer_filter_chapter_aggregates_sql', $sql, $story_id, $chapter_ids, $queried_statuses, 'small' );

    return $wpdb->get_results( $sql, OBJECT_K ) ?: [];
  }

  /**
   * Query aggregated chapter data using a chunked, ID-scoped query.
   *
   * @since 5.34.0
   *
   * @param array $chapter_ids       Chapter IDs.
   * @param array $queried_statuses  Statuses to be queried.
   * @param int   $story_id          ID of the story.
   *
   * @return array Chapter data keyed by ID (unordered).
   */

  protected static function query_chapter_aggregates_large( $chapter_ids, $statuses, $story_id ) : array {
    global $wpdb;

    $limit = (int) apply_filters( 'fictioneer_filter_get_story_data_batch_limit', 800, $story_id );
    $limit = max( 100, min( 2000, $limit ) );

    $fetch_for_ids = static function( array $ids ) use ( $wpdb, $story_id, $statuses ) : array {
      if ( ! $ids ) {
        return [];
      }

      $ids = array_values( array_unique( array_map( 'intval', $ids ) ) );
      $ids_placeholder = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
      $status_placeholders = implode( ',', array_fill( 0, count( $statuses ), '%s' ) );
      $args = array_merge( $ids, $ids, $statuses );

      // Select c.ID first so OBJECT_K keys by ID
      $sql = $wpdb->prepare(
        "SELECT
          c.ID,
          c.comment_count,
          c.post_status,
          CAST(pm.word_count AS UNSIGNED) AS word_count
        FROM {$wpdb->posts} c
        LEFT JOIN (
          SELECT
            post_id,
            MAX(meta_value) AS word_count
          FROM {$wpdb->postmeta}
          WHERE post_id IN ($ids_placeholder)
            AND meta_key = '_word_count'
          GROUP BY post_id
        ) pm ON pm.post_id = c.ID
        WHERE c.ID IN ($ids_placeholder)
          AND c.post_status IN ($status_placeholders)",
        ...$args
      );

      $sql = apply_filters( 'fictioneer_filter_chapter_aggregates_sql', $sql, $story_id, $ids, $statuses, 'large' );

      return $wpdb->get_results( $sql, OBJECT_K ) ?: [];
    };

    if ( count( $chapter_ids ) <= $limit ) {
      return $fetch_for_ids( $chapter_ids );
    }

    $result = [];

    foreach ( array_chunk( $chapter_ids, $limit ) as $chunk ) {
      $result += $fetch_for_ids( $chunk );
    }

    return $result;
  }

  /**
   * Group and prepares chapters for a specific story.
   *
   * Note: If chapter groups are disabled, all chapters will be
   * within the 'all_chapters' group.
   *
   * @since 5.25.0
   * @since 5.34.0 - Moved into Story class.
   *
   * @param int   $story_id  ID of the story.
   * @param array $chapters  Array of WP_Post or post-like objects.
   *
   * @return array The grouped and prepared chapters.
   */

  public static function prepare_chapter_groups( $story_id, $chapters ) : array {
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

      // Skip missing chapters
      if ( ! $post ) {
        continue;
      }

      // Data
      $group = Utils::get_meta( $post, 'fictioneer_chapter_group' );
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

      if ( get_option( 'fictioneer_enable_fast_chapter_posts' ) ) {
        $title = $post->post_title ?: get_the_date( '', $post );
      } else {
        $title = fictioneer_get_safe_title( $chapter_id, 'story-chapter-list' );
      }

      $chapter_groups[ $group_key ]['data'][] = array(
        'id' => $chapter_id,
        'story_id' => $story_id,
        'status' => $post->post_status,
        'link' => in_array( $post->post_status, $allowed_permalinks )
          ? Utils::get_permalink( $post, $story_id )
          : '',
        'timestamp' => get_the_time( 'U', $post ),
        'password' => ! empty( $post->post_password ),
        'list_date' => get_the_date( '', $post ),
        'grid_date' => get_the_time( get_option( 'fictioneer_subitem_date_format', "M j, 'y" ) ?: "M j, 'y", $post ),
        'icon' => fictioneer_get_icon_field(
          'fictioneer_chapter_icon',
          $chapter_id,
          Utils::get_meta( $post, 'fictioneer_chapter_icon' )
        ),
        'text_icon' => Utils::get_meta( $post, 'fictioneer_chapter_text_icon' ),
        'prefix' => Utils::get_meta( $post, 'fictioneer_chapter_prefix' ),
        'title' => $title,
        'list_title' => Utils::get_meta( $post, 'fictioneer_chapter_list_title' ),
        'words' => fictioneer_get_word_count( $chapter_id, Utils::get_meta( $post, '_word_count' ) ),
        'warning' => Utils::get_meta( $post, 'fictioneer_chapter_warning' )
      );

      $chapter_groups[ $group_key ]['count'] += 1;
    }

    return $chapter_groups;
  }

  /**
   * Cache and save story data.
   *
   * @since 5.34.0
   *
   * @param int   $story_id       ID of the story.
   * @param array $data           Story data.
   * @param bool  $show_comments  Whether the comment count is needed.
   */

  protected static function persist_story_data( $story_id, $data, $show_comments ) : void {
    // Cache
    if ( defined( 'FICTIONEER_ENABLE_STORY_DATA_META_CACHE' ) && FICTIONEER_ENABLE_STORY_DATA_META_CACHE ) {
      update_post_meta( $story_id, self::META_CACHE_KEY, $data );
    }

    // Update story total word count
    $new_total_words = (int) ( $data['word_count_raw'] ?? 0 );
    $old_total_words = (int) get_post_meta( $story_id, 'fictioneer_story_total_word_count', true );

    if ( $old_total_words !== $new_total_words ) {
      update_post_meta( $story_id, 'fictioneer_story_total_word_count', $new_total_words );
    }

    // Update story total comment count
    if ( $show_comments ) {
      $comment_count = (int) ( $data['comment_count'] ?? 0 );
      $story_comment_count = (int) ( get_approved_comments( $story_id, array( 'count' => true ) ) ?: 0 );
      $new_total_comment_count = (int) $comment_count + $story_comment_count;
      $old_total_comment_count = (int) get_post_field( 'comment_count', $story_id, 'raw' );

      if ( $old_total_comment_count !== $new_total_comment_count ) {
        Utils_Admin::update_comment_count( $story_id, $new_total_comment_count );
      }
    }
  }

  /**
   * Return cache TTL for chapter posts.
   *
   * @since 5.34.0
   *
   * @return int TTL in seconds.
   */

  protected static function get_chapter_posts_cache_ttl() : int {
    return defined( 'FICTIONEER_WP_CACHE_TTL' ) ? max( 0, (int) FICTIONEER_WP_CACHE_TTL ) : 0;
  }

  /**
   * Return cache TTL for chapter posts cache version keys (2x TTL).
   *
   * @since 5.34.0
   *
   * @return int TTL in seconds.
   */

  protected static function get_chapter_posts_cache_version_ttl() : int {
    $ttl = self::get_chapter_posts_cache_ttl();

    return $ttl > 0 ? $ttl * 4 : 0;
  }

  /**
   * Bump cache version for a story's chapter posts cache.
   *
   * @since 5.34.0
   *
   * @param int $story_id  Story ID.
   */

  public static function bump_story_chapter_cache_version( $story_id ) : void {
    if ( ! $story_id ) {
      return;
    }

    $group = 'fictioneer_fast_chapter_posts';
    $key = 'v:' . $story_id;

    $version = wp_cache_get( $key, $group );
    $version = $version === false ? 1 : (int) $version;
    $version = $version > 0 ? $version + 1 : 2;

    wp_cache_set( $key, $version, $group, self::get_chapter_posts_cache_version_ttl() );
  }

  /**
   * Return cache version for a story's chapter posts cache.
   *
   * @since 5.34.0
   *
   * @param int $story_id  Story ID.
   *
   * @return int Cache version.
   */

  protected static function get_story_chapter_cache_version( $story_id ) : int {
    $group = 'fictioneer_fast_chapter_posts';
    $key = 'v:' . $story_id;

    $version = wp_cache_get( $key, $group );

    if ( $version === false ) {
      $version = 1;

      wp_cache_set( $key, $version, $group, self::get_chapter_posts_cache_version_ttl() );
    }

    return (int) $version;
  }
}
