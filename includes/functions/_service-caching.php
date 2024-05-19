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

    // Cached HTML by theme
    fictioneer_clear_all_cached_partials();
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
      apply_filters( 'active_plugins', get_option('active_plugins' ) )
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
    // Prevent multi-fire
    if ( fictioneer_multi_save_guard( $post_id ) ) {
      return;
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
    foreach( $hooks as $hook ) {
      add_action( $hook, 'fictioneer_refresh_post_caches', 20 );
    }
  } else {
    foreach( $hooks as $hook ) {
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
    // Prevent multi-fire
    if ( fictioneer_multi_save_guard( $post_id ) ) {
      return;
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
    foreach( $hooks as $hook ) {
      add_action( $hook, 'fictioneer_track_chapter_and_story_updates', 10 );
    }
  } else {
    foreach( $hooks as $hook ) {
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
  // Prevent multi-fire
  if ( fictioneer_multi_save_guard( $post_id ) ) {
    return;
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
    foreach( $hooks as $hook ) {
      add_action( $hook, 'fictioneer_purge_transients' );
    }
  } else {
    foreach( $hooks as $hook ) {
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
  $dir = $dir ?? ( get_template_directory() . '/cache/html/' );
  $index_file = $dir . '/index.php';
  $htaccess_file = $dir . '/.htaccess';
  $result = true;

  // Create cache directories if missing
  if ( ! is_dir( $dir ) ) {
    $result = mkdir( $dir, 0755, true );
  }

  // Hide files from index
  if ( $result && ! file_exists( $index_file ) ) {
    file_put_contents( $index_file, "<?php\n// Silence is golden.\n" );
  }

  // Add .htaccess to limit access
  if ( $result && ! file_exists( $htaccess_file ) ) {
    file_put_contents(
      $htaccess_file,
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
  if (
    ! get_option( 'fictioneer_enable_static_partials' ) ||
    is_customize_preview() ||
    fictioneer_caching_active( "get_template_part_for_{$slug}" )
  ) {
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

    if ( time() - $filemtime < ( $expiration ?? DAY_IN_SECONDS ) ) {
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
 * Clear static HTML of all cached partials
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
