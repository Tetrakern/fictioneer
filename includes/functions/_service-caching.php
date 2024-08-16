<?php

// =============================================================================
// ANY CACHE ACTIVE?
// =============================================================================

// List of known cache plugins
if ( ! defined( 'FICTIONEER_KNOWN_CACHE_PLUGINS' ) ) {
  define(
    'FICTIONEER_KNOWN_CACHE_PLUGINS',
    array(
      'w3-total-cache/w3-total-cache.php', // W3 Total Cache
      'wp-super-cache/wp-cache.php', // WP Super Cache
      'wp-rocket/wp-rocket.php', // WP Rocket
      'litespeed-cache/litespeed-cache.php', // LiteSpeed Cache
      'wp-fastest-cache/wpFastestCache.php', // WP Fastest Cache
      'cache-enabler/cache-enabler.php', // Cache Enabler
      'hummingbird-performance/wp-hummingbird.php', // Hummingbird – Optimize Speed, Enable Cache
      'wp-optimize/wp-optimize.php', // WP-Optimize - Clean, Compress, Cache
      'sg-cachepress/sg-cachepress.php', // SG Optimizer (SiteGround)
      'breeze/breeze.php', // Breeze (by Cloudways)
      'nitropack/nitropack.php' // NitroPack
    )
  );
}

if ( ! function_exists( 'fictioneer_caching_active' ) ) {
  /**
   * Checks whether caching is active
   *
   * Checks for a number of known caching plugins or the cache
   * compatibility option for anything not covered.
   *
   * @since 4.0.0
   *
   * @param string|null $context  Context of the check. Currently unused.
   *
   * @return boolean Either true (active) or false (inactive or not installed).
   */

  function fictioneer_caching_active( $context = null ) {
    // Check early
    if ( get_option( 'fictioneer_enable_cache_compatibility' ) ) {
      return true;
    }

    // Check active plugins
    $active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
    $active_cache_plugins = array_intersect( $active_plugins, FICTIONEER_KNOWN_CACHE_PLUGINS );

    // Any cache plugins active?
    return ! empty( $active_cache_plugins );
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
   * @since 5.0.0
   *
   * @return boolean Either true or false.
   */

  function fictioneer_private_caching_active() {
    return is_user_logged_in() &&
           get_option( 'fictioneer_enable_private_cache_compatibility', false ) &&
           ! get_option( 'fictioneer_enable_public_cache_compatibility', false );
  }
}

// Boolean: Partial caching enabled?
if ( ! defined( 'FICTIONEER_ENABLE_PARTIAL_CACHING' ) ) {
  define(
    'FICTIONEER_ENABLE_PARTIAL_CACHING',
    get_option( 'fictioneer_enable_static_partials' ) &&
    ! is_customize_preview() &&
    ! fictioneer_caching_active( 'enable_partial_caching' )
  );
}

// Boolean: Story card caching enabled?
if ( ! defined( 'FICTIONEER_ENABLE_STORY_CARD_CACHING' ) ) {
  define(
    'FICTIONEER_ENABLE_STORY_CARD_CACHING',
    get_option( 'fictioneer_enable_story_card_caching' ) && ! is_customize_preview()
  );
}

// Boolean: Query result caching enabled=
if ( ! defined( 'FICTIONEER_ENABLE_QUERY_RESULT_CACHING' ) ) {
  define(
    'FICTIONEER_ENABLE_QUERY_RESULT_CACHING',
    get_option( 'fictioneer_enable_query_result_caching' ) && ! is_customize_preview()
  );
}

// =============================================================================
// ENABLE SHORTCODE TRANSIENTS?
// =============================================================================

/**
 * Return whether shortcode Transients should be enabled
 *
 * @since 5.6.3
 *
 * @return boolean Either true or false.
 */

function fictioneer_enable_shortcode_transients() {
  // Disable in Customizer
  if ( is_customize_preview() ) {
    return false;
  }

  // Check constant and caching status
  $bool = FICTIONEER_SHORTCODE_TRANSIENT_EXPIRATION > -1 && ! fictioneer_caching_active( 'shortcodes' );

  // Filter
  $bool = apply_filters( 'fictioneer_filter_enable_shortcode_transients', $bool );

  // Return
  return $bool;
}

// =============================================================================
// PURGE CACHES
// =============================================================================

if ( ! function_exists( 'fictioneer_purge_all_caches' ) ) {
  /**
   * Trigger the "purge all" functions of known cache plugins
   *
   * @since 4.0.0
   * @link https://docs.litespeedtech.com/lscache/lscwp/api/#purge
   * @link https://docs.wp-rocket.me/article/494-how-to-clear-cache-via-cron-job#clear-cache
   * @link https://wp-kama.com/plugin/wp-super-cache/function/wp_cache_clean_cache
   */

  function fictioneer_purge_all_caches() {
    // WordPress Query Cache
    wp_cache_flush();

    // Hook for additional purges
    do_action( 'fictioneer_cache_purge_all' );

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

    // Cache Enabler
    if ( has_action( 'cache_enabler_clear_complete_cache' ) ) {
      do_action( 'cache_enabler_clear_complete_cache' );
    }

    // Hummingbird
    if ( has_action( 'wphb_clear_page_cache' ) ) {
      do_action( 'wphb_clear_page_cache' );
    }

    // SG Optimizer
    // Insufficient or hard-to-find documentation and if the
    // developer cannot be bothered, neither can I.

    // Breeze
    if ( has_action( 'breeze_clear_all_cache' ) ) {
      do_action( 'breeze_clear_all_cache' );
    }

    // WP Optimize
    if ( class_exists( 'WP_Optimize' ) ) {
      $wp_optimize = WP_Optimize();

      if ( method_exists( $wp_optimize, 'get_page_cache' ) ) {
        $wp_optimize->get_page_cache()->purge();
      }
    }

    // W3 Fastest Cache
    if ( function_exists( 'wpfc_clear_all_cache' ) ) {
      wpfc_clear_all_cache();
    }

    // NitroPack
    // Insufficient or hard-to-find documentation and if the
    // developer cannot be bothered, neither can I.

    // Cached HTML partials
    fictioneer_clear_all_cached_partials();

    // Cached story cards
    if ( FICTIONEER_ENABLE_STORY_CARD_CACHING ) {
      fictioneer_purge_story_card_cache();
    }
  }
}

if ( ! function_exists( 'fictioneer_purge_post_cache' ) ) {
  /**
   * Trigger the "purge post" functions of known cache plugins
   *
   * @since 4.7.0
   * @link https://docs.litespeedtech.com/lscache/lscwp/api/#purge
   * @link https://docs.wp-rocket.me/article/494-how-to-clear-cache-via-cron-job#clear-cache
   * @link https://wp-kama.com/plugin/wp-super-cache/function/wpsc_delete_post_cache
   *
   * @param int $post_id  Post ID.
   */

  function fictioneer_purge_post_cache( $post_id ) {
    // Setup
    $post_id = fictioneer_validate_id( $post_id );

    // Abort if...
    if ( empty( $post_id ) ) {
      return;
    }

    // Hook for additional purges
    do_action( 'fictioneer_cache_purge_post', $post_id );

    // WordPress Query Cache
    clean_post_cache( $post_id );

    // LiteSpeed Cache
    if ( class_exists( '\LiteSpeed\Purge' ) ) {
      do_action( 'litespeed_purge_post', $post_id );
    }

    // WP Rocket Cache
    if ( function_exists( 'rocket_clean_post' ) ) {
      rocket_clean_post( $post_id );
    }

    // WP Super Cache
    if ( function_exists( 'wp_cache_post_change' ) ) {
      wp_cache_post_change( $post_id );
    }

    // W3 Total Cache
    if ( function_exists( 'w3tc_flush_post' ) ) {
      w3tc_flush_post( $post_id );
    }

    // Cache Enabler
    if ( has_action( 'cache_enabler_clear_page_cache_by_post' ) ) {
      do_action( 'cache_enabler_clear_page_cache_by_post', $post_id );
    }

    // Hummingbird
    if ( has_action( 'wphb_clear_page_cache' ) ) {
      do_action( 'wphb_clear_page_cache', $post_id );
    }

    // SG Optimizer
    // Insufficient or hard-to-find documentation and if the
    // developer cannot be bothered, neither can I.

    // Breeze
    // Insufficient or hard-to-find documentation and if the
    // developer cannot be bothered, neither can I.

    // WP Optimize
    if (
      class_exists( 'WPO_Page_Cache' ) &&
      method_exists( 'WPO_Page_Cache', 'delete_single_post_cache' )
    ) {
      WPO_Page_Cache::delete_single_post_cache( $post_id );
    }

    // W3 Fastest Cache
    if ( function_exists( 'wpfc_clear_post_cache_by_id' ) ) {
      wpfc_clear_post_cache_by_id( $post_id );
    }

    // NitroPack
    // Insufficient or hard-to-find documentation and if the
    // developer cannot be bothered, neither can I.

    // Cached HTML partial
    if ( FICTIONEER_ENABLE_PARTIAL_CACHING ) {
      fictioneer_clear_cached_content( $post_id );
    }

    // Cached story card
    if ( FICTIONEER_ENABLE_STORY_CARD_CACHING ) {
      fictioneer_delete_cached_story_card( $post_id );
    }
  }
}

// =============================================================================
// LITESPEED CACHE
// =============================================================================

if ( ! function_exists( 'fictioneer_litespeed_running' ) ) {
  /**
   * Checks whether LiteSpeed Cache plugin is active
   *
   * @since 4.0.0
   *
   * @return boolean Either true (active) or false (inactive or not installed)
   */

  function fictioneer_litespeed_running() {
    return in_array(
      'litespeed-cache/litespeed-cache.php',
      apply_filters( 'active_plugins', get_option( 'active_plugins' ) )
    );
  }
}

// =============================================================================
// PURGE RELEVANT CACHE(S) ON POST SAVE
// =============================================================================

if ( ! function_exists( 'fictioneer_purge_template_caches' ) ) {
  /**
   * Purges all posts with the given template template
   *
   * @since 5.5.2
   *
   * @param string $template  The page template (without `.php`)
   */

  function fictioneer_purge_template_caches( $template ) {
    // Setup (this should normally only yield one page or none)
    $pages = get_posts(
      array(
        'post_type' => 'page',
        'numberposts' => -1,
        'fields' => 'ids',
        'meta_key' => '_wp_page_template',
        'meta_value' => "{$template}.php",
        'update_post_meta_cache' => false, // Improve performance
        'update_post_term_cache' => false, // Improve performance
        'no_found_rows' => true // Improve performance
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
   * @since 5.0.0
   *
   * @param int $post_id  Updated post ID.
   */

  function fictioneer_refresh_post_caches( $post_id ) {
    static $done = null;

    // Prevent multi-fire; allow trashing to pass because
    // this is sometimes only triggered as REST request.
    if (
      ( fictioneer_multi_save_guard( $post_id ) || $done ) &&
      ( get_post_status( $post_id ) !== 'trash' || $done )
    ) {
      return;
    } else {
      $done = true;
    }

    // Purge all?
    if ( get_option( 'fictioneer_purge_all_caches' ) ) {
      fictioneer_purge_all_caches();
      return;
    }

    // Get type
    $post_type = get_post_type( $post_id );

    if ( ! $post_type ) {
      return;
    }

    // Start system action: unset user
    $current_user_id = get_current_user_id();
    wp_set_current_user( 0 );

    // Remove actions to prevent infinite loops and multi-fire
    fictioneer_toggle_refresh_hooks( false );
    fictioneer_toggle_transient_purge_hooks( false );
    fictioneer_toggle_update_tracker_hooks( false );

    // Purge updated post
    fictioneer_purge_post_cache( $post_id );

    // Purge front page (if any)
    $font_page_id = intval( get_option( 'page_on_front' ) ?: -1 );

    if ( $font_page_id != $post_id && $font_page_id > 0 ) {
      fictioneer_purge_post_cache( $font_page_id );
    }

    // Purge associated list pages
    if ( in_array( $post_type, ['fcn_chapter', 'fcn_story'] ) ) {
      fictioneer_purge_template_caches( 'chapters' );
      fictioneer_purge_template_caches( 'stories' );
    }

    if ( $post_type == 'fcn_recommendation' ) {
      fictioneer_purge_template_caches( 'recommendations' );
    }

    // Purge parent story (if any)...
    if ( $post_type == 'fcn_chapter' ) {
      $story_id = get_post_meta( $post_id, 'fictioneer_chapter_story', true );

      if ( ! empty( $story_id ) ) {
        $chapters = fictioneer_get_story_chapter_ids( $story_id );

        delete_post_meta( $story_id, 'fictioneer_story_data_collection' );
        delete_post_meta( $story_id, 'fictioneer_story_chapter_index_html' );
        fictioneer_purge_post_cache( $story_id );

        // ... and associated chapters
        if ( ! empty( $chapters ) ) {
          foreach ( $chapters as $chapter_id ) {
            fictioneer_purge_post_cache( $chapter_id );
          }
        }
      }
    }

    // Purge associated chapters
    if ( $post_type == 'fcn_story' ) {
      $chapters = fictioneer_get_story_chapter_ids( $post_id );

      if ( ! empty( $chapters ) ) {
        foreach ( $chapters as $chapter_id ) {
          fictioneer_purge_post_cache( $chapter_id );
        }
      }
    }

    // Purge all collections (cheapest option)
    if ( $post_type != 'page' ) {
      fictioneer_purge_template_caches( 'collections' );

      $collections = get_posts(
        array(
          'post_type' => 'fcn_collection',
          'numberposts' => -1,
          'fields' => 'ids',
          'update_post_meta_cache' => false, // Improve performance
          'update_post_term_cache' => false, // Improve performance
          'no_found_rows' => true // Improve performance
        )
      );

      foreach ( $collections as $collection_id ) {
        fictioneer_purge_post_cache( $collection_id );
      }
    }

    // Purge relationships
    if ( FICTIONEER_RELATIONSHIP_PURGE_ASSIST ) {
      $registry = fictioneer_get_relationship_registry();

      // Always purge...
      foreach ( $registry['always'] as $key => $entry ) {
        delete_post_meta( $key, 'fictioneer_schema' );
        fictioneer_purge_post_cache( $key );
      }

      // Direct relationships
      if ( isset( $registry[ $post_id ] ) ) {
        foreach ( $registry[ $post_id ] as $key => $entry ) {
          delete_post_meta( $key, 'fictioneer_schema' );
          fictioneer_purge_post_cache( $key );
        }
      }

      // Purge post relationships
      $relation = false;

      switch ( $post_type ) {
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
          delete_post_meta( $key, 'fictioneer_schema' );
          fictioneer_purge_post_cache( $key );
        }
      }
    }

    // Restore actions
    fictioneer_toggle_refresh_hooks();
    fictioneer_toggle_transient_purge_hooks();
    fictioneer_toggle_update_tracker_hooks();

    // End system action: restore user
    wp_set_current_user( $current_user_id );
  }
}

/**
 * Add or remove actions for `fictioneer_refresh_post_caches`
 *
 * @since 5.5.2
 *
 * @param boolean $add  Optional. Whether to add or remove the action. Default true.
 */

function fictioneer_toggle_refresh_hooks( $add = true ) {
  $hooks = ['save_post', 'untrash_post', 'trashed_post', 'delete_post'];

  if ( $add ) {
    foreach ( $hooks as $hook ) {
      add_action( $hook, 'fictioneer_refresh_post_caches', 20 );
    }
  } else {
    foreach ( $hooks as $hook ) {
      remove_action( $hook, 'fictioneer_refresh_post_caches', 20 );
    }
  }
}

if ( FICTIONEER_CACHE_PURGE_ASSIST && fictioneer_caching_active( 'purge_assist' ) ) {
  fictioneer_toggle_refresh_hooks();
}

// =============================================================================
// RELATIONSHIP REGISTRY
//
// The relationship directory array is not fail-safe. Technically, it can happen
// that while the registry is pulled for updating by one post, it is also pulled
// by another, causing the last to override the first without the first's data.
// However, this is unlikely and not the end of the world.
//
// A possible solution would be to create a new database table for registry
// updates which is processed by a worker occasionally. Cannot be bothered tho.
// =============================================================================

/**
 * Returns relationship registry from options table
 *
 * @since 5.0.0
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
    if ( ! isset( $registry[ $node ] ) ) {
      $registry[ $node ] = [];
    }

    if ( ! is_array( $registry[ $node ] ) ) {
      $registry[ $node ]  = [];
    }
  }

  // Return
  return $registry;
}

/**
 * Saves relationship registry to options table
 *
 * @since 5.0.0
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
 * @since 5.0.0
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
   * @since 4.7.0
   *
   * @param int $post_id  Updated post ID.
   */

  function fictioneer_track_chapter_and_story_updates( $post_id ) {
    static $done = null;

    // Prevent multi-fire; allow trashing to pass because
    // this is sometimes only triggered as REST request.
    if (
      ( fictioneer_multi_save_guard( $post_id ) || $done ) &&
      ( get_post_status( $post_id ) !== 'trash' || $done )
    ) {
      return;
    } else {
      $done = true;
    }

    // Get story ID from post or parent story (if any)
    $post_type = get_post_type( $post_id ); // Not all hooks get the $post object!
    $story_id = $post_type == 'fcn_story' ? $post_id : get_post_meta( $post_id, 'fictioneer_chapter_story', true );

    // Delete cached statistics for stories
    delete_transient( 'fictioneer_stories_statistics' );
    delete_transient( 'fictioneer_stories_total_word_count' );

    // If there is a story...
    if ( ! empty( $story_id ) ) {
      // Decides when cached story/chapter data need to be refreshed (only used for Follows)
      // Beware: This is an option, not a Transient!
      update_option( 'fictioneer_story_or_chapter_updated_timestamp', time() * 1000 );

      // Clear meta caches
      delete_post_meta( $story_id, 'fictioneer_story_data_collection' );
      delete_post_meta( $story_id, 'fictioneer_story_chapter_index_html' );

      // Delete cached query results
      fictioneer_delete_transients_like( "fictioneer_query_{$story_id}" );

      // Refresh cached HTML output
      delete_transient( 'fictioneer_story_chapter_list_html' . $story_id );

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

/**
 * Add or remove actions for `fictioneer_toggle_update_tracker_hooks`
 *
 * @since 5.5.2
 *
 * @param boolean $add  Optional. Whether to add or remove the action. Default true.
 */

function fictioneer_toggle_update_tracker_hooks( $add = true ) {
  $hooks = ['save_post', 'untrash_post', 'trashed_post', 'delete_post'];

  if ( $add ) {
    foreach ( $hooks as $hook ) {
      add_action( $hook, 'fictioneer_track_chapter_and_story_updates', 10 );
    }
  } else {
    foreach ( $hooks as $hook ) {
      remove_action( $hook, 'fictioneer_track_chapter_and_story_updates', 10 );
    }
  }
}

fictioneer_toggle_update_tracker_hooks();

// =============================================================================
// PURGE CACHE TRANSIENTS
// =============================================================================

/**
 * Purge Transients used for caching when posts are updated
 *
 * @since 5.4.9
 *
 * @param int $post_id  Updated post ID.
 */

function fictioneer_purge_transients( $post_id ) {
  static $done = null;

  // Prevent multi-fire; allow trashing to pass because
  // this is sometimes only triggered as REST request.
  if (
    ( fictioneer_multi_save_guard( $post_id ) || $done ) &&
    ( get_post_status( $post_id ) !== 'trash' || $done )
  ) {
    return;
  } else {
    $done = true;
  }

  // Setup
  $post_type = get_post_type( $post_id ); // Not all hooks get the $post object!

  // Menus
  fictioneer_purge_nav_menu_transients();

  // Shortcode...
  if ( fictioneer_enable_shortcode_transients() ) {
    // Recommendation?
    if ( $post_type == 'fcn_recommendation' ) {
      fictioneer_delete_transients_like( 'fictioneer_shortcode_latest_recommendations' );
      return;
    }

    // All
    fictioneer_delete_transients_like( 'fictioneer_shortcode' );
  }
}

/**
 * Add or remove actions for `fictioneer_purge_transients`
 *
 * @since 5.5.2
 *
 * @param boolean $add  Optional. Whether to add or remove the action. Default true.
 */

function fictioneer_toggle_transient_purge_hooks( $add = true ) {
  $hooks = ['save_post', 'untrash_post', 'trashed_post', 'delete_post'];

  if ( $add ) {
    foreach ( $hooks as $hook ) {
      add_action( $hook, 'fictioneer_purge_transients' );
    }
  } else {
    foreach ( $hooks as $hook ) {
      remove_action( $hook, 'fictioneer_purge_transients' );
    }
  }
}

fictioneer_toggle_transient_purge_hooks();

/**
 * Purge nav menu Transients on menu updates
 *
 * @since 5.6.0
 */

function fictioneer_purge_nav_menu_transients() {
  delete_transient( 'fictioneer_main_nav_menu_html' );
  delete_transient( 'fictioneer_footer_menu_html' );
}
add_action( 'wp_update_nav_menu', 'fictioneer_purge_nav_menu_transients' );

// =============================================================================
// PARTIAL CACHING (THEME)
// =============================================================================

/**
 * Generate random salt for cache-related hashes
 *
 * Note: Saves the salt to the option 'fictioneer_cache_salt'.
 *
 * @since 5.18.3
 *
 * @return string The generated salt.
 */

function fictioneer_generate_cache_salt() {
  $salt = bin2hex( random_bytes( 32 / 2 ) );

  update_option( 'fictioneer_cache_salt', $salt );

  return $salt;
}

/**
 * Get salt for cache-related hashes
 *
 * Note: Regenerated if no salt exists.
 *
 * @since 5.18.3
 *
 * @return string The salt.
 */

function fictioneer_get_cache_salt() {
  return get_option( 'fictioneer_cache_salt' ) ?: fictioneer_generate_cache_salt();
}

/**
 * Get static HTML of cached template partial
 *
 * @since 5.18.3
 * @see get_template_directory()
 *
 * @param string|null $dir  Optional. Directory path to override the default.
 *
 * @return bool True if the directory exists or has been created, false on failure.
 */

function fictioneer_create_html_cache_directory( $dir = null ) {
  // Setup
  $default_dir = get_template_directory() . '/cache/html/';
  $dir = $dir ?? $default_dir;
  $result = true;

  // Create cache directories if missing
  if ( ! is_dir( $dir ) ) {
    $result = mkdir( $dir, 0755, true );
  }

  // Hide files from index
  if ( $result && ! file_exists( $dir . '/index.php' ) ) {
    file_put_contents( $dir . '/index.php', "<?php\n// Silence is golden.\n" );
  }

  if ( $result && ! file_exists( $default_dir . '/index.php' ) ) {
    file_put_contents( $default_dir . '/index.php', "<?php\n// Silence is golden.\n" );
  }

  // Add .htaccess to limit access
  if ( $result && ! file_exists( $dir . '/.htaccess' ) ) {
    file_put_contents(
      $dir . '/.htaccess',
      "<Files *>\n    Order Allow,Deny\n    Deny from all\n</Files>\n"
    );
  }

  if ( $result && ! file_exists( $default_dir . '/.htaccess' ) ) {
    file_put_contents(
      $default_dir . '/.htaccess',
      "<Files *>\n    Order Allow,Deny\n    Deny from all\n</Files>\n"
    );
  }

  // Success or failure
  return $result;
}

/**
 * Get static HTML of cached template partial
 *
 * @since 5.18.1
 * @see get_template_part()
 *
 * @param string      $slug        The slug name for the generic template.
 * @param string      $identifier  Optional. Additional identifier string for the file. Default empty string.
 * @param int|null    $expiration  Optional. Seconds until the cache expires. Default 24 hours in seconds.
 * @param string|null $name        Optional. The name of the specialized template.
 * @param array       $args        Optional. Additional arguments passed to the template.
 */

function fictioneer_get_cached_partial( $slug, $identifier = '', $expiration = null, $name = null, $args = [] ) {
  // Use default function if...
  if ( ! FICTIONEER_ENABLE_PARTIAL_CACHING ) {
    get_template_part( $slug, $name, $args );

    return;
  }

  // Setup
  $args_hash = md5( serialize( $args ) . $identifier . fictioneer_get_cache_salt() );
  $static_file = $slug . ( $name ? "-{$name}" : '' ) . "-{$args_hash}.html";
  $path = get_template_directory() . '/cache/html/' . $static_file;

  // Make sure directory exists and handle failure
  if ( ! fictioneer_create_html_cache_directory( dirname( $path ) ) ) {
    get_template_part( $slug, $name, $args );
    return;
  }

  // Get static file if not expired
  if ( file_exists( $path ) ) {
    $filemtime = filemtime( $path );

    if ( time() - $filemtime < ( $expiration ?? FICTIONEER_PARTIAL_CACHE_EXPIRATION_TIME ) ) {
      echo file_get_contents( $path );
      return;
    }
  }

  // Generate and cache the new file
  ob_start();
  get_template_part( $slug, $name, $args );
  $html = ob_get_clean();

  if ( file_put_contents( $path, $html ) === false ) {
    get_template_part( $slug, $name, $args );
  } else {
    echo $html;
  }
}

/**
 * Clears static HTML of all cached partials
 *
 * Note: Only executed once per request to reduce overhead.
 * Can therefore be used with impunity.
 *
 * @since 5.18.1
 */

function fictioneer_clear_all_cached_partials() {
  // Only run this once per request
  static $done = false;

  if ( $done || ! get_option( 'fictioneer_enable_static_partials' ) ) {
    return;
  }

  $done = true;

  // Setup
  $cache_dir = get_template_directory() . '/cache/html/';

  // Regenerate cache salt
  fictioneer_generate_cache_salt();

  // Ensure the directory exists
  if ( ! file_exists( $cache_dir ) ) {
    return;
  }

  // Recursively delete files and subdirectories
  $files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator( $cache_dir, RecursiveDirectoryIterator::SKIP_DOTS ),
    RecursiveIteratorIterator::CHILD_FIRST
  );

  foreach ( $files as $fileinfo ) {
    $todo = ( $fileinfo->isDir() ? 'rmdir' : 'unlink' );

    if ( ! $todo( $fileinfo->getRealPath() ) ) {
      error_log( 'Failed to delete ' . $fileinfo->getRealPath() );
    }
  }
}

/**
 * Returns post content from cached HTML file
 *
 * @since 5.19.0
 * @link https://developer.wordpress.org/reference/functions/the_content/
 * @global WP_Post $post
 *
 * @param string|null $more_link_text  Optional. Content for when there is more text.
 * @param bool        $strip_teaser    Optional. Strip teaser content before the more text.
 *
 * @return string The post content.
 */

function fictioneer_get_static_content( $more_link_text = \null, $strip_teaser = \false ) {
  global $post;

  // Use default function if...
  if (
    ! FICTIONEER_ENABLE_PARTIAL_CACHING ||
    ! empty( $post->post_password ) ||
    $post->post_status !== 'publish' ||
    get_post_meta( $post->ID, 'fictioneer_chapter_disable_partial_caching', true ) ||
    ! apply_filters( 'fictioneer_filter_static_content_true', true, $post )
  ) {
    $content = get_the_content( $more_link_text, $strip_teaser );
    $content = apply_filters( 'the_content', $content );
	  $content = str_replace( ']]>', ']]&gt;', $content );

    return apply_filters( 'fictioneer_filter_static_content', $content, $post );
  }

  // Setup
  $hash = md5( $post->ID . fictioneer_get_cache_salt() );
  $dir = get_template_directory() . '/cache/html/' . substr( $hash, 0, 2 );
  $path = "{$dir}/{$hash}_{$post->ID}.html";

  // Make sure directory exists and handle failure
  if ( ! fictioneer_create_html_cache_directory( $dir ) ) {
    $content = get_the_content( $more_link_text, $strip_teaser );
    $content = apply_filters( 'the_content', $content );
	  $content = str_replace( ']]>', ']]&gt;', $content );

    return apply_filters( 'fictioneer_filter_static_content', $content, $post );
  }

  // Get static file if not expired
  if ( file_exists( $path ) ) {
    $filemtime = filemtime( $path );

    if ( time() - $filemtime < FICTIONEER_PARTIAL_CACHE_EXPIRATION_TIME ) {
      return apply_filters( 'fictioneer_filter_static_content', file_get_contents( $path ), $post );
    }
  }

  // Get content and apply filters
  $content = get_the_content( $more_link_text, $strip_teaser );
  $content = apply_filters( 'the_content', $content );
  $content = str_replace( ']]>', ']]&gt;', $content );

  // Cache as static file
  file_put_contents( $path, $content );

  // Return content
  return apply_filters( 'fictioneer_filter_static_content', $content, $post );
}

/**
 * Renders post content from cached HTML file
 *
 * @since 5.19.0
 *
 * @param string|null $more_link_text  Optional. Content for when there is more text.
 * @param bool        $strip_teaser    Optional. Strip teaser content before the more text.
 */

function fictioneer_the_static_content( $more_link_text = \null, $strip_teaser = \false ) {
  echo fictioneer_get_static_content( $more_link_text, $strip_teaser );
}

/**
 * Clears static content HTML of post
 *
 * @since 5.19.0
 *
 * @param int $post_id  ID of the post to clear the cache for.
 *
 * @return bool True if the cache file has been deleted, false if not found.
 */

function fictioneer_clear_cached_content( $post_id ) {
  // Setup
  $hash = md5( $post_id . fictioneer_get_cache_salt() );
  $dir = get_template_directory() . '/cache/html/' . substr( $hash, 0, 2 );
  $path = "{$dir}/{$hash}_{$post_id}.html";

  // Delete file
  if ( file_exists( $path ) ) {
    unlink( $path );

    return true;
  }

  // File not found
  return false;
}

if ( FICTIONEER_ENABLE_PARTIAL_CACHING ) {
  foreach ( ['save_post', 'untrash_post', 'trashed_post', 'delete_post'] as $hook ) {
    add_action( $hook, 'fictioneer_clear_cached_content' );
  }
}

// =============================================================================
// TRANSIENT CARD CACHE
// =============================================================================

/**
 * Checks whether the story card cache is locked by another process
 *
 * Note: Locks the Transient for 30 seconds if currently unlocked,
 * which needs to be cleared afterwards. The lock state for this
 * process is cached in a static variable.
 *
 * @since 5.22.3
 *
 * @return bool True if locked, false if free for this process.
 */

function fictioneer_story_card_cache_lock() {
  static $lock = null;

  if ( $lock !== null ) {
    return $lock;
  }

  if ( get_transient( 'fictioneer_story_card_cache_lock' ) ) {
    $lock = true;
  } else {
    set_transient( 'fictioneer_story_card_cache_lock', 1, 30 );
    $lock = false;
  }

  return $lock;
}

/**
 * Returns the Transient array with all cached story cards
 *
 * @since 5.22.2
 *
 * @return array Array of cached story cards; empty array if disabled.
 */

function fictioneer_get_story_card_cache() {
  // Abort if...
  if ( ! FICTIONEER_ENABLE_STORY_CARD_CACHING ) {
    return [];
  }

  // Initialize lock to avoid race conditions
  fictioneer_story_card_cache_lock();

  // Initialize global cache variable
  global $fictioneer_story_card_cache;

  if ( ! $fictioneer_story_card_cache ) {
    $fictioneer_story_card_cache = get_transient( 'fictioneer_card_cache' ) ?: [];
  }

  return $fictioneer_story_card_cache;
}

/**
 * Adds a story card to the cache
 *
 * @since 5.22.2
 *
 * @param string $key   The cache key of the card.
 * @param string $card  The HTML of the card.
 */

function fictioneer_cache_story_card( $key, $card ) {
  // Abort if...
  if ( ! FICTIONEER_ENABLE_STORY_CARD_CACHING || fictioneer_story_card_cache_lock() ) {
    return;
  }

  // Initialize global cache variable
  global $fictioneer_story_card_cache;

  // Setup
  if ( ! $fictioneer_story_card_cache ) {
    $fictioneer_story_card_cache = fictioneer_get_story_card_cache();
  }

  // Remove cached card (if set)...
  unset( $fictioneer_story_card_cache[ $key ] );

  // ... and append at the end
  $fictioneer_story_card_cache[ $key ] = $card;
}

/**
 * Returns the cached HTML for a specific story card
 *
 * @since 5.22.2
 *
 * @param string $key  The cache key of the card.
 *
 * @return string|null HTML of the card or null if not found/disabled.
 */

function fictioneer_get_cached_story_card( $key ) {
  // Abort if...
  if ( ! FICTIONEER_ENABLE_STORY_CARD_CACHING ) {
    return null;
  }

  // Initialize global cache variable
  global $fictioneer_story_card_cache;

  // Setup
  if ( ! $fictioneer_story_card_cache ) {
    $fictioneer_story_card_cache = fictioneer_get_story_card_cache();
  }

  // Look in currently cached cards...
  if ( $card = ( $fictioneer_story_card_cache[ $key ] ?? 0 ) ) {
    // Remove cached card...
    unset( $fictioneer_story_card_cache[ $key ] );

    // ... and append at the end
    $fictioneer_story_card_cache[ $key ] = $card;
  } else {
    return null;
  }

  // Return cached card
  return $card;
}

/**
 * Purges the entire story card cache
 *
 * @since 5.22.2
 */

function fictioneer_purge_story_card_cache() {
  // Abort if...
  if ( ! FICTIONEER_ENABLE_STORY_CARD_CACHING ) {
    return;
  }

  global $fictioneer_story_card_cache;

  $fictioneer_story_card_cache = [];

  delete_transient( 'fictioneer_card_cache' );
}

/**
 * Deletes a specific card from the story card cache
 *
 * @since 5.22.2
 *
 * @param int $post_id  Post ID of the story.
 */

function fictioneer_delete_cached_story_card( $post_id ) {
  // Abort if...
  if ( ! FICTIONEER_ENABLE_STORY_CARD_CACHING ) {
    return;
  }

  // Initialize global cache variable
  global $fictioneer_story_card_cache;

  // Setup
  if ( ! $fictioneer_story_card_cache ) {
    $fictioneer_story_card_cache = fictioneer_get_story_card_cache();
  }

  // Find matching keys
  foreach ( $fictioneer_story_card_cache as $key => $value ) {
    if ( strpos( $key, $post_id . '_' ) === 0 ) {
      unset( $fictioneer_story_card_cache[ $key ] );
    }
  }
}

/**
 * Deletes a specific card from the story card cache after update
 *
 * @since 5.22.3
 *
 * @param int $post_id  The post ID.
 */

function fictioneer_delete_cached_story_card_after_update( $post_id ) {
  // Story?
  if ( get_post_type( $post_id ) === 'fcn_story' ) {
    fictioneer_delete_cached_story_card( $post_id );
    return;
  }

  // Chapter?
  if ( get_post_type( $post_id ) === 'fcn_chapter' ) {
    $story_id = get_post_meta( $post_id, 'fictioneer_chapter_story', true );

    if ( $story_id ) {
      fictioneer_delete_cached_story_card( $story_id );
    }
  }
}
add_action( 'save_post', 'fictioneer_delete_cached_story_card_after_update' );

/**
 * Deletes a specific card from the story card cache by comment ID
 *
 * @since 5.22.2
 *
 * @param int $comment_id  ID of the comment belonging to the story
 */

function fictioneer_delete_cached_story_card_by_comment( $comment_id ) {
  // Abort if...
  if ( ! FICTIONEER_ENABLE_STORY_CARD_CACHING ) {
    return;
  }

  // Setup
  $comment = get_comment( $comment_id );

  if ( $comment ) {
    $story_id = get_post_meta( $comment->comment_post_ID, 'fictioneer_chapter_story', true );

    if ( $story_id ) {
      fictioneer_delete_cached_story_card( $story_id );
    }
  }
}
add_action( 'wp_insert_comment', 'fictioneer_delete_cached_story_card_by_comment' );
add_action( 'delete_comment', 'fictioneer_delete_cached_story_card_by_comment' );

/**
 * Saves the story card cache array as Transient
 *
 * @since 5.22.2
 */

function fictioneer_save_story_card_cache() {
  // Abort if...
  if ( ! FICTIONEER_ENABLE_STORY_CARD_CACHING || fictioneer_story_card_cache_lock() ) {
    return;
  }

  // Clear lock
  delete_transient( 'fictioneer_story_card_cache_lock' );

  // Initialize global cache variable
  global $fictioneer_story_card_cache;

  if ( ! is_array( $fictioneer_story_card_cache ) ) {
    return;
  }

  // Ensure the limit is respected
  $count = count( $fictioneer_story_card_cache );

  if ( $count > FICTIONEER_CARD_CACHE_LIMIT ) {
    $fictioneer_story_card_cache = array_slice(
      $fictioneer_story_card_cache,
      $count - FICTIONEER_CARD_CACHE_LIMIT,
      FICTIONEER_CARD_CACHE_LIMIT,
      true
    );
  }

  // Save Transient
  set_transient( 'fictioneer_card_cache', $fictioneer_story_card_cache, FICTIONEER_CARD_CACHE_EXPIRATION_TIME );
}
add_action( 'shutdown', 'fictioneer_save_story_card_cache' );

// =============================================================================
// TRANSIENT QUERY RESULT CACHE
// =============================================================================

/**
 * Checks whether the registry is locked by another process
 *
 * Note: Locks the registry for 30 seconds if currently unlocked,
 * which needs to be cleared afterwards. The lock state for this
 * process is cached in a static variable.
 *
 * @since 5.22.3
 *
 * @return bool True if locked, false if free for this process.
 */

function fictioneer_query_result_cache_lock() {
  static $lock = null;

  if ( $lock !== null ) {
    return $lock;
  }

  if ( get_transient( 'fictioneer_query_result_cache_lock' ) ) {
    $lock = true;
  } else {
    set_transient( 'fictioneer_query_result_cache_lock', 1, 30 );
    $lock = false;
  }

  return $lock;
}

/**
 * Returns the global registry array for cached query results
 *
 * Note: The registry is stored as auto-loaded options in the
 * database and set up as global variable on request. It is
 * only updated in the 'shutdown' action.
 *
 * @since 5.22.3
 * @global $fictioneer_query_result_registry
 *
 * @return array Array with Transient keys for cached query results.
 */

function fictioneer_get_query_result_cache_registry() {
  // Abort if...
  if ( ! FICTIONEER_ENABLE_QUERY_RESULT_CACHING ) {
    return [];
  }

  // Initialize lock to avoid race conditions
  fictioneer_query_result_cache_lock();

  // Initialize global cache variable
  global $fictioneer_query_result_registry;

  if ( ! $fictioneer_query_result_registry ) {
    $fictioneer_query_result_registry = get_option( 'fictioneer_query_cache_registry' );

    if ( ! is_array( $fictioneer_query_result_registry ) ) {
      $fictioneer_query_result_registry = [];
      update_option( 'fictioneer_query_cache_registry', [] );
    }
  }

  // Return array for good measure, but the global will do
  return $fictioneer_query_result_registry;
}

/**
 * Adds query result to the global query result cache
 *
 * Note: Only query results that exceed a certain threshold (75)
 * are stored in the cache and only the latest n items (50).
 *
 * @since 5.22.3
 * @global $fictioneer_query_result_registry
 * @see FICTIONEER_QUERY_RESULT_CACHE_THRESHOLD
 * @see FICTIONEER_QUERY_RESULT_CACHE_LIMIT
 *
 * @param string $key     The cache key of the query.
 * @param array  $result  The result of the query.
 */

function fictioneer_cache_query_result( $key, $result ) {
  // Abort if...
  if ( ! FICTIONEER_ENABLE_QUERY_RESULT_CACHING || fictioneer_query_result_cache_lock() ) {
    return;
  }

  if ( count( $result ) < FICTIONEER_QUERY_RESULT_CACHE_THRESHOLD ) {
    return;
  }

  // Initialize global cache variable
  global $fictioneer_query_result_registry;

  if ( ! $fictioneer_query_result_registry ) {
    $fictioneer_query_result_registry = fictioneer_get_query_result_cache_registry();
  }

  // Build key; must begin with fictioneer_query_{$post_id} for clearing purposes
  $transient_key = "fictioneer_query_{$key}";

  // Prepend result to stack
  $fictioneer_query_result_registry = array_merge( array( $key => $transient_key ), $fictioneer_query_result_registry );

  // Randomized expiration to avoid large sets of results to expire simultaneously
  set_transient( $transient_key, $result, 8 * HOUR_IN_SECONDS + rand( 0, 4 * HOUR_IN_SECONDS ) );

  // Only allow n items in the cache to avoid bloating the database
  if ( count( $fictioneer_query_result_registry ) > FICTIONEER_QUERY_RESULT_CACHE_LIMIT ) {
    array_pop( $fictioneer_query_result_registry ); // Drop oldest entry
  }
}

/**
 * Adds query result to the global query result cache
 *
 * Note: Only query results that exceed a certain threshold (75)
 * are stored in the cache and only the latest n items (50).
 *
 * @since 5.22.3
 * @global $fictioneer_query_result_registry
 *
 * @param string $key  The cache key of the query.
 *
 * @return array|null Array of WP_Post objects or null if not cached or expired.
 */

function fictioneer_get_cached_query_result( $key ) {
  // Abort if...
  if ( ! FICTIONEER_ENABLE_QUERY_RESULT_CACHING ) {
    return null;
  }

  // Initialize global cache variable
  global $fictioneer_query_result_registry;

  if ( ! $fictioneer_query_result_registry ) {
    $fictioneer_query_result_registry = fictioneer_get_query_result_cache_registry();
  }

  // Look for cached result...
  if ( isset( $fictioneer_query_result_registry[ $key ] ) ) {
    $transient = get_transient( $fictioneer_query_result_registry[ $key ] );

    if ( is_array( $transient ) ) {
      // Hit
      return $transient;
    } else {
      // Miss
      fictioneer_delete_cached_query_result( $key ); // Cleanup
    }
  }

  // Nothing cached or expired
  return null;
}

/**
 * Deletes query result from the global query result cache
 *
 * @since 5.22.3
 * @global $fictioneer_query_result_registry
 *
 * @param string $key  The cache key of the query.
 */

function fictioneer_delete_cached_query_result( $key ) {
  // Abort if...
  if ( ! FICTIONEER_ENABLE_QUERY_RESULT_CACHING ) {
    return;
  }

  // Initialize global cache variable
  global $fictioneer_query_result_registry;

  if ( ! $fictioneer_query_result_registry ) {
    $fictioneer_query_result_registry = fictioneer_get_query_result_cache_registry();
  }

  // Remove entry (if present)
  unset( $fictioneer_query_result_registry[ $key ] );
}

/**
 * Saves the final global query result cache to the database
 *
 * @since 5.22.3
 * @global $fictioneer_query_result_registry
 */

function fictioneer_save_query_result_cache_registry() {
  // Abort if...
  if ( ! FICTIONEER_ENABLE_QUERY_RESULT_CACHING || fictioneer_query_result_cache_lock() ) {
    return;
  }

  // Clear lock
  delete_transient( 'fictioneer_query_result_cache_lock' );

  // Initialize global cache variable
  global $fictioneer_query_result_registry;

  if ( is_null( $fictioneer_query_result_registry ) || ! is_array( $fictioneer_query_result_registry ) ) {
    return;
  }

  // Ensure the limit is respected
  $count = count( $fictioneer_query_result_registry );

  if ( $count > FICTIONEER_QUERY_RESULT_CACHE_LIMIT ) {
    $fictioneer_query_result_registry = array_slice(
      $fictioneer_query_result_registry,
      0,
      FICTIONEER_QUERY_RESULT_CACHE_LIMIT,
      true
    );
  }

  // Anything to save?
  if ( $fictioneer_query_result_registry !== get_option( 'fictioneer_query_cache_registry' ) ) {
    update_option( 'fictioneer_query_cache_registry', $fictioneer_query_result_registry ?: [] );
  }
}
add_action( 'shutdown', 'fictioneer_save_query_result_cache_registry' );
