<?php

namespace Fictioneer;

defined( 'ABSPATH' ) OR exit;

final class Stats {
  /**
   * Total word count of all published stories.
   *
   * Note: Does not include standalone chapters.
   *
   * @since 4.0.0
   * @since 5.22.3 - Refactored with SQL query for better performance.
   * @since 5.34.0 - Moved into Stats class.
   *
   * @return int Word count of all published stories.
   */

  public static function get_stories_total_word_count() : int {
    $transient = get_transient( 'fictioneer_stories_total_word_count' );

    if ( $transient ) {
      return (int) $transient;
    }

    global $wpdb;

    $words = $wpdb->get_var(
      $wpdb->prepare(
        "SELECT COALESCE(SUM(CAST(pm.meta_value AS UNSIGNED)), 0)
        FROM {$wpdb->postmeta} AS pm
        INNER JOIN {$wpdb->posts} AS p ON pm.post_id = p.ID
        WHERE pm.meta_key = %s
        AND p.post_type = %s
        AND p.post_status = %s",
        'fictioneer_story_total_word_count',
        'fcn_story',
        'publish'
      )
    );

    $words = fictioneer_multiply_word_count( (int) $words );

    set_transient( 'fictioneer_stories_total_word_count', $words, DAY_IN_SECONDS );

    return $words;
  }

  /**
   * Author's statistics.
   *
   * @since 4.6.0
   * @since 5.27.4 - Optimized.
   * @since 5.34.0 - Moved into Stats class.
   *
   * @param int $author_id  User ID of the author.
   *
   * @return array|false Array of statistics or false if user does not exist.
   */

  public static function get_author_statistics( $author_id ) {
    global $wpdb;

    $author_id = fictioneer_validate_id( $author_id );

    if ( ! $author_id || ! get_user_by( 'ID', $author_id ) ) {
      return false;
    }

    $now = time();

    if ( FICTIONEER_ENABLE_AUTHOR_STATS_META_CACHE ) {
      $meta_cache = get_user_meta( $author_id, 'fictioneer_author_statistics', true );

      if ( is_array( $meta_cache ) && ( $meta_cache['valid_until'] ?? 0 ) > $now ) {
        return $meta_cache;
      }
    }

    $posts = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT p.ID, p.post_type, p.comment_count,
          wc.meta_value AS word_count
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} wc  ON p.ID = wc.post_id  AND wc.meta_key = '_word_count'
        WHERE p.post_status = 'publish'
          AND p.post_author = %d
          AND p.post_type IN ('fcn_story', 'fcn_chapter')",
        $author_id
      ),
      ARRAY_A
    ) ?: [];

    $story_count = 0;
    $chapter_count = 0;
    $word_count = 0;
    $comment_count = 0;

    foreach ( $posts as $post ) {
      if ( $post['post_type'] === 'fcn_story' ) {
        $story_count++;
      } elseif ( $post['post_type'] === 'fcn_chapter' ) {
        $chapter_count++;
        $comment_count += (int) $post['comment_count'];
      }

      $word_count += fictioneer_get_word_count( (int) $post['ID'], max( 0, (int) $post['word_count'] ) );
    }

    $result = array(
      'story_count' => $story_count,
      'chapter_count' => $chapter_count,
      'word_count' => $word_count,
      'word_count_short' => fictioneer_shorten_number( $word_count ),
      'valid_until' => $now + HOUR_IN_SECONDS,
      'comment_count' => $comment_count
    );

    if ( FICTIONEER_ENABLE_AUTHOR_STATS_META_CACHE ) {
      update_user_meta( $author_id, 'fictioneer_author_statistics', $result );
    }

    return $result;
  }

  /**
   * Collection's statistics.
   *
   * @since 5.9.2
   * @since 5.26.0 - Refactored with custom SQL.
   * @since 5.34.0 - Moved into Stats class.
   *
   * @global wpdb $wpdb  WordPress database object.
   *
   * @param int $collection_id  ID of the collection.
   *
   * @return array Array of statistics.
   */

  public static function get_collection_statistics( $collection_id ) : array {
    global $wpdb;

    // Meta cache?
    $cache_plugin_active = fictioneer_caching_active( 'collection_statistics' );

    if ( ! $cache_plugin_active ) {
      $meta_cache = get_post_meta( $collection_id, 'fictioneer_collection_statistics', true );

      if ( $meta_cache && ( $meta_cache['valid_until'] ?? 0 ) > time() ) {
        return $meta_cache;
      }
    }

    // Empty collection?
    $featured = get_post_meta( $collection_id, 'fictioneer_collection_items', true );

    if ( empty( $featured ) || ! is_array( $featured ) ) {
      return array(
        'story_count' => 0,
        'word_count' => 0,
        'chapter_count' => 0,
        'comment_count' => 0
      );
    }

    // Setup
    $story_count = 0;
    $word_count = 0;
    $chapter_count = 0;
    $comment_count = 0;

    $counted_chapters = [];
    $query_chapter_ids = [];

    // SQL
    $placeholders = implode( ',', array_fill( 0, count( $featured ), '%d' ) );

    $sql =
      "SELECT p.ID, p.post_type, p.post_status
      FROM {$wpdb->posts} p
      WHERE p.ID IN ({$placeholders}) AND p.post_status = 'publish'";

    $posts = $wpdb->get_results( $wpdb->prepare( $sql, ...$featured ) ) ?: [];

    // Analyze
    foreach ( $posts as $post ) {
      if ( $post->post_type === 'fcn_chapter' ) {
        $query_chapter_ids[] = $post->ID;

        continue;
      }

      if ( $post->post_type === 'fcn_story' ) {
        $story = Story::get_data( $post->ID, false );

        foreach ( $story['chapter_ids'] as $cid ) {
          $counted_chapters[ $cid ] = true;
        }

        $word_count += $story['word_count']; // Already multiplied
        $chapter_count += $story['chapter_count'];
        $comment_count += $story['comment_count'];
        $story_count++;
      }
    }

    $query_chapter_ids = array_values( array_unique( $query_chapter_ids ) );

    if ( ! empty( $query_chapter_ids ) && ! empty( $counted_chapters ) ) {
      $query_chapter_ids = array_values(
        array_filter(
          $query_chapter_ids,
          function( $id ) use ( $counted_chapters ) {
            return empty( $counted_chapters[ $id ] );
          }
        )
      );
    }

    // SQL query for lone chapters not belong to featured stories...
    if ( ! empty( $query_chapter_ids ) ) {
      $placeholders = implode( ',', array_fill( 0, count( $query_chapter_ids ), '%d' ) );

      $sql =
        "SELECT p.ID, p.comment_count, COALESCE(pm_word_count.meta_value, 0) AS word_count
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm_word_count
          ON (p.ID = pm_word_count.post_id AND pm_word_count.meta_key = '_word_count')
        WHERE p.ID IN ($placeholders)
          AND p.post_type = 'fcn_chapter'
          AND p.post_status = 'publish'";

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

    // Prepare stats
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

    return $statistics;
  }
}
