<?php

// =============================================================================
// ANY CACHE ACTIVE?
// =============================================================================

if ( ! function_exists( 'fictioneer_caching_active' ) ) {
  /**
   * Checks whether caching is active
   *
   * Checks for a number of known caching plugins or the cache
   * compatibility option for anything not covered.
   *
   * @since 4.0
   *
   * @return boolean Either true (active) or false (inactive or not installed).
   */

  function fictioneer_caching_active() {
    return fictioneer_litespeed_running() ||
           get_option( 'fictioneer_enable_cache_compatibility' ) ||
           function_exists( 'wp_cache_clean_cache' ) ||
           defined( 'W3TC' ) ||
           function_exists( 'rocket_clean_domain' );
  }
}

if ( ! function_exists( 'fictioneer_private_caching_active' ) ) {
  /**
   * Checks whether private caching is active
   *
   * Checks whether the user is logged in and the private cache
   * compatibility mode is enabled. Public and private caching
   * contradict each other, with public caching given priority.
   *
   * @since 5.0
   *
   * @return boolean Either true or false.
   */

  function fictioneer_private_caching_active() {
    return is_user_logged_in() &&
           get_option( 'fictioneer_enable_private_cache_compatibility', false ) &&
           ! get_option( 'fictioneer_enable_public_cache_compatibility', false );
  }
}

// =============================================================================
// PURGE CACHES
// =============================================================================

if ( ! function_exists( 'fictioneer_purge_all_caches' ) ) {
  /**
   * Trigger the "purge all" functions of known cache plugins
   *
   * @since 4.0
   * @link https://docs.litespeedtech.com/lscache/lscwp/api/#purge
   * @link https://docs.wp-rocket.me/article/494-how-to-clear-cache-via-cron-job#clear-cache
   * @link https://wp-kama.com/plugin/wp-super-cache/function/wp_cache_clean_cache
   */

  function fictioneer_purge_all_caches() {
    // LiteSpeed Cache
    if ( class_exists( '\LiteSpeed\Purge' ) ) {
      do_action( 'litespeed_purge_all' );
    }

    // WP Rocket Cache
    if ( function_exists( 'rocket_clean_domain' ) ) {
      rocket_clean_domain();
    }

    // WP Super Cache
    if ( function_exists( 'wp_cache_clean_cache' ) ) {
      global $file_prefix;
      wp_cache_clean_cache( $file_prefix, true );
    }

    // W3 Total Cache
    if ( function_exists( 'w3tc_flush_all' ) ) {
      w3tc_flush_all();
    }

    // Hook for additional purges
    do_action( 'fictioneer_cache_purge_all' );
  }
}

if ( ! function_exists( 'fictioneer_purge_post_cache' ) ) {
  /**
   * Trigger the "purge post" functions of known cache plugins
   *
   * @since 4.7
   * @link https://docs.litespeedtech.com/lscache/lscwp/api/#purge
   * @link https://docs.wp-rocket.me/article/494-how-to-clear-cache-via-cron-job#clear-cache
   * @link https://wp-kama.com/plugin/wp-super-cache/function/wpsc_delete_post_cache
   *
   * @param int $post_id Post ID.
   */

  function fictioneer_purge_post_cache( $post_id ) {
    // Setup
    $post_id = fictioneer_validate_id( $post_id );

    // Abort if...
    if ( ! $post_id ) return;

    // LiteSpeed Cache
    if ( class_exists( '\LiteSpeed\Purge' ) ) {
      do_action( 'litespeed_purge_post', $post_id );
    }

    // WP Rocket Cache
    if ( function_exists( 'rocket_clean_post' ) ) {
      rocket_clean_post( $post_id );
    }

    // WP Super Cache
    if ( function_exists( 'wpsc_delete_post_cache' ) ) {
      wpsc_delete_post_cache( $post_id );
    }

    // W3 Total Cache
    if ( function_exists( 'w3tc_flush_post' ) ) {
      w3tc_flush_post( $post_id );
    }

    // Hook for additional purges
    do_action( 'fictioneer_cache_purge_post', $post_id );
  }
}

// =============================================================================
// LITESPEED CACHE
// =============================================================================

if ( ! function_exists( 'fictioneer_litespeed_running' ) ) {
  /**
   * Checks whether LiteSpeed Cache plugin is active
   *
   * @since 4.0
   *
   * @return boolean Either true (active) or false (inactive or not installed)
   */

  function fictioneer_litespeed_running() {
    return in_array(
      'litespeed-cache/litespeed-cache.php',
      apply_filters( 'active_plugins', get_option('active_plugins' ) )
    );
  }
}

// =============================================================================
// PURGE RELEVANT CACHE(S) ON POST SAVE
// =============================================================================

if ( ! function_exists( 'fictioneer_purge_story_list_caches' ) ) {
  /**
   * Purges all posts with the Stories template
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_purge_story_list_caches() {
    // Setup (this should normally only yield on page or none)
    $pages = get_posts(
      array(
        'post_type' => 'page',
        'numberposts' => -1,
        'fields' => 'ids',
        'meta_key' => '_wp_page_template',
        'meta_value' => 'stories.php',
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false
      )
    );

    // Purge
    if ( $pages ) {
      foreach ( $pages as $page_id ) {
        fictioneer_purge_post_cache( $page_id );
      }
    }
  }
}

if ( ! function_exists( 'fictioneer_purge_chapter_list_caches' ) ) {
  /**
   * Purges all posts with the Chapters template
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_purge_chapter_list_caches() {
    // Setup (this should normally only yield on page or none)
    $pages = get_posts(
      array(
        'post_type' => 'page',
        'numberposts' => -1,
        'fields' => 'ids',
        'meta_key' => '_wp_page_template',
        'meta_value' => 'chapters.php',
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false
      )
    );

    // Purge
    if ( $pages ) {
      foreach ( $pages as $page_id ) {
        fictioneer_purge_post_cache( $page_id );
      }
    }
  }
}

if ( ! function_exists( 'fictioneer_purge_collection_list_caches' ) ) {
  /**
   * Purges all posts with the Collections template
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_purge_collection_list_caches() {
    // Setup (this should normally only yield on page or none)
    $pages = get_posts(
      array(
        'post_type' => 'page',
        'numberposts' => -1,
        'fields' => 'ids',
        'meta_key' => '_wp_page_template',
        'meta_value' => 'collections.php',
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false
      )
    );

    // Purge
    if ( $pages ) {
      foreach ( $pages as $page_id ) {
        fictioneer_purge_post_cache( $page_id );
      }
    }
  }
}

if ( ! function_exists( 'fictioneer_purge_recommendation_list_caches' ) ) {
  /**
   * Purges all posts with the Recommendations template
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_purge_recommendation_list_caches() {
    // Setup (this should normally only yield on page or none)
    $pages = get_posts(
      array(
        'post_type' => 'page',
        'numberposts' => -1,
        'fields' => 'ids',
        'meta_key' => '_wp_page_template',
        'meta_value' => 'recommendations.php',
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false
      )
    );

    // Purge
    if ( $pages ) {
      foreach ( $pages as $page_id ) {
        fictioneer_purge_post_cache( $page_id );
      }
    }
  }
}

if ( ! function_exists( 'fictioneer_refresh_post_caches' ) ) {
  /**
   * Purges relevant caches when a post is updated
   *
   * "There are only two hard things in Computer Science: cache invalidation and
   * naming things" -- Phil Karlton.
   *
   * @since Fictioneer 5.0
   *
   * @param int $post_id Updated post ID.
   */

  function fictioneer_refresh_post_caches( $post_id ) {
    // Prevent multi-fire
    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) return;
    if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) return;

    // Purge all?
    if ( get_option( 'fictioneer_purge_all_caches' ) ) {
      fictioneer_purge_all_caches();
      return;
    }

    // Purge updated post
    fictioneer_purge_post_cache( $post_id );

    // Purge front page (if any)
    fictioneer_purge_post_cache( get_option( 'page_on_front' ) );

    // Purge parent story (if any)
    fictioneer_purge_post_cache(
      fictioneer_get_field( 'fictioneer_chapter_story', $post_id )
    );

    // Purge associated list pages
    if ( in_array( get_post_type( $post_id ), ['fcn_chapter', 'fcn_story'] ) ) {
      fictioneer_purge_chapter_list_caches();
      fictioneer_purge_story_list_caches();
    }

    if ( get_post_type( $post_id ) == 'fcn_recommendation' ) {
      fictioneer_purge_recommendation_list_caches();
    }

    // Purge associated chapters
    if ( get_post_type( $post_id ) == 'fcn_story' ) {
      $chapters = fictioneer_get_field( 'fictioneer_story_chapters', $post_id );

      if ( ! empty( $chapters ) ) {
        foreach ( $chapters as $chapter_id ) {
          fictioneer_purge_post_cache( $chapter_id );
        }
      }
    }

    // Purge relationships
    if ( FICTIONEER_RELATIONSHIP_PURGE_ASSIST ) {
      $registry = fictioneer_get_relationship_registry();

      // Always purge...
      foreach ( $registry['always'] as $key => $entry ) {
        fictioneer_purge_post_cache( $key );
        delete_post_meta( $key, 'fictioneer_schema' );
      }

      // Direct relationships
      if ( isset( $registry[ $post_id ] ) ) {
        foreach ( $registry[ $post_id ] as $key => $entry ) {
          fictioneer_purge_post_cache( $key );
          delete_post_meta( $key, 'fictioneer_schema' );
        }
      }

      // Purge post relationships
      $relation = false;

      switch ( get_post_type( $post_id ) ) {
        case 'post':
          $relation = 'ref_posts';
          break;
        case 'fcn_chapter':
          $relation = 'ref_chapters';
          break;
        case 'fcn_story':
          $relation = 'ref_stories';
          break;
        case 'fcn_recommendation':
          $relation = 'ref_recommendations';
          break;
      }

      if ( $relation ) {
        foreach ( $registry[ $relation ] as $key => $entry ) {
          fictioneer_purge_post_cache( $key );
          delete_post_meta( $key, 'fictioneer_schema' );
        }
      }
    }

    // Purge all collections (cheapest option)
    if ( get_post_type( $post_id ) != 'page' ) {
      fictioneer_purge_collection_list_caches();

      $collections = get_posts(
        array(
          'post_type' => 'fcn_collection',
          'numberposts' => -1,
          'fields' => 'ids',
          'update_post_meta_cache' => false,
          'update_post_term_cache' => false
        )
      );

      foreach ( $collections as $collection_id ) {
        fictioneer_purge_post_cache( $collection_id );
      }
    }
  }
}

if ( FICTIONEER_CACHE_PURGE_ASSIST && fictioneer_caching_active() ) {
  add_action( 'save_post', 'fictioneer_refresh_post_caches' );
  add_action( 'untrash_post', 'fictioneer_refresh_post_caches' );
  add_action( 'trashed_post', 'fictioneer_refresh_post_caches' );
  add_action( 'delete_post', 'fictioneer_refresh_post_caches' );
}

// =============================================================================
// RELATIONSHIP REGISTRY
//
// The relationship directory array is not fail-safe. Technically, it can happen
// that while the registry is pulled for updating by one post, it is also pulled
// by another, causing the last to override the first without the first's data.
// However, this is unlikely and not the end of the world.
// =============================================================================

/**
 * Returns relationship registry from options table
 *
 * @since 5.0
 *
 * @return array The balanced array.
 */

function fictioneer_get_relationship_registry() {
  // Setup
  $registry = get_option( 'fictioneer_relationship_registry', [] );
  $registry = is_array( $registry ) ? $registry : [];

  // Important nodes
  $nodes = ['ref_posts', 'ref_chapters', 'ref_stories', 'ref_recommendations', 'always'];

  foreach ( $nodes as $node ) {
    if ( ! isset( $registry[ $node ] ) ) $registry[ $node ] = [];
    if ( ! is_array( $registry[ $node ] ) ) $registry[ $node ]  = [];
  }

  // Return
  return $registry;
}

/**
 * Saves relationship registry to options table
 *
 * @since 5.0
 *
 * @param array $registry Current registry to override the stored option.
 *
 * @return array True if the value was updated, false otherwise.
 */

function fictioneer_save_relationship_registry( $registry ) {
  return update_option( 'fictioneer_relationship_registry', $registry );
}

/**
 * Remove relationships on delete
 *
 * @since Fictioneer 5.0
 *
 * @param int $post_id The deleted post ID.
 */

function fictioneer_delete_relationship( $post_id ) {
  // Setup
  $registry = fictioneer_get_relationship_registry();

  // Remove node (if set)
  unset( $registry[ $post_id ] );

  // Remove references (if any)
  foreach ( $registry as $key => $entry ) {
    unset( $registry[ $key ][ $post_id ] );
  }

  unset( $registry['ref_posts'][ $post_id ] );
  unset( $registry['ref_chapters'][ $post_id ] );
  unset( $registry['ref_stories'][ $post_id ] );
  unset( $registry['ref_recommendations'][ $post_id ] );
  unset( $registry['always'][ $post_id ] );

  // Update database
  fictioneer_save_relationship_registry( $registry );
}

if ( FICTIONEER_RELATIONSHIP_PURGE_ASSIST ) {
  add_action( 'delete_post', 'fictioneer_delete_relationship', 100 );
}

// =============================================================================
// TRACK CHAPTER & STORY UPDATES
// =============================================================================

if ( ! function_exists( 'fictioneer_track_chapter_and_story_updates' ) ) {
  /**
   * Updates Transients and storage options when a story or chapter is updated
   *
   * Whenever a story or chapter is saved, trashed, restored, or permanently
   * deleted, this function will update or purge respective Transients and
   * storage options to reflect the current state of content. This is mainly
   * used for caching purposes and there is no harm if these values vanish.
   *
   * @since Fictioneer 4.7
   *
   * @param int $post_id Updated post ID.
   */

  function fictioneer_track_chapter_and_story_updates( $post_id ) {
    // Prevent multi-fire
    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) return;
    if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) return;

    // Get story ID from post or parent story (if any)
    $post_type = get_post_type( $post_id );
    $story_id = $post_type == 'fcn_story' ? $post_id : fictioneer_get_field( 'fictioneer_chapter_story', $post_id );

    // Since every chapter needs to be attached to a story...
    if ( ! empty( $story_id ) ) {
      // Decides when cached story/chapter data need to be refreshed
      // Beware: This is an option, not a Transient!
      update_option( 'fictioneer_story_or_chapter_updated_timestamp', time() * 1000 );

      // Refresh cached HTML output
      delete_transient( 'fictioneer_story_chapter_list_' . $story_id );

      // Delete cached stories total word count
      delete_transient( 'fictioneer_stories_total_word_count' );

      // Delete cached API response
      if ( FICTIONEER_API_STORYGRAPH_TRANSIENTS ) {
        $count_stories = wp_count_posts( 'fcn_story' );
        $pages = $count_stories->publish / FICTIONEER_API_STORYGRAPH_STORIES_PER_PAGE + 1;

        delete_transient( 'fictioneer_api_story_' . $story_id );

        for ( $i = 1; $i <= $pages; $i++ ) {
          delete_transient( 'fictioneer_storygraph_stories_' . $i );
        }
      }
    }
  }
}
add_action( 'save_post', 'fictioneer_track_chapter_and_story_updates' );
add_action( 'untrash_post', 'fictioneer_track_chapter_and_story_updates' );
add_action( 'trashed_post', 'fictioneer_track_chapter_and_story_updates' );
add_action( 'delete_post', 'fictioneer_track_chapter_and_story_updates' );

// =============================================================================
// GET LAST CHAPTER/STORY UPDATE
// =============================================================================

if ( ! function_exists( 'fictioneer_get_last_story_or_chapter_update' ) ) {
  /**
   * Get Unix timestamp for last story or chapter update
   *
   * @since Fictioneer 5.0
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
// PURGE SHORTCODE TRANSIENTS
// =============================================================================

/**
 * Purge all shortcode Transients
 *
 * @since Fictioneer 5.4.9
 *
 * @param int $post_id  Updated post ID.
 */

function fictioneer_purge_shortcode_transients( $post_id ) {
  // Prevent multi-fire
  if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) return;
  if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) return;

  // Delete Transients (fast)
  fictioneer_delete_transients_like( 'fictioneer_shortcode' );
}

if ( FICTIONEER_SHORTCODE_TRANSIENT_EXPIRATION > 0 ) {
  add_action( 'save_post', 'fictioneer_purge_shortcode_transients' );
  add_action( 'untrash_post', 'fictioneer_purge_shortcode_transients' );
  add_action( 'trashed_post', 'fictioneer_purge_shortcode_transients' );
  add_action( 'delete_post', 'fictioneer_purge_shortcode_transients' );
}

?>
