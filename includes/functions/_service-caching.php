<?php

// =============================================================================
// ANY CACHE ACTIVE?
// =============================================================================

if ( ! defined( 'FICTIONEER_KNOWN_CACHE_PLUGINS' ) ) {
  define(
    'FICTIONEER_KNOWN_CACHE_PLUGINS',
    array(
      'w3-total-cache/w3-total-cache.php' => 'W3 Total Cache',
      'wp-super-cache/wp-cache.php' => 'WP Super Cache',
      'wp-rocket/wp-rocket.php' => 'WP Rocket',
      'litespeed-cache/litespeed-cache.php' => 'LiteSpeed Cache',
      'wp-fastest-cache/wpFastestCache.php' => 'WP Fastest Cache',
      'cache-enabler/cache-enabler.php' => 'Cache Enabler',
      'hummingbird-performance/wp-hummingbird.php' => 'Hummingbird',
      'wp-optimize/wp-optimize.php' => 'WP-Optimize',
      'sg-cachepress/sg-cachepress.php' => 'SG Optimizer',
      'breeze/breeze.php' => 'Breeze',
      'nitropack/nitropack.php' => 'NitroPack'
    )
  );
}

/**
 * Return array with names of active cache plugins.
 *
 * @since 5.25.0
 *
 * @return string[] Names of active plugins.
 */

function fictioneer_get_active_cache_plugins() {
  $result = [];

  foreach ( array_keys( FICTIONEER_KNOWN_CACHE_PLUGINS ) as $plugin_slug ) {
    if ( is_plugin_active( $plugin_slug ) && isset( FICTIONEER_KNOWN_CACHE_PLUGINS[ $plugin_slug ] ) ) {
      $result[] = FICTIONEER_KNOWN_CACHE_PLUGINS[ $plugin_slug ];
    }
  }

  return $result;
}

if ( ! function_exists( 'fictioneer_caching_active' ) ) {
  /**
   * Check whether caching is active.
   *
   * Checks for a number of known caching plugins or the cache
   * compatibility option for anything not covered.
   *
   * @since 4.0.0
   *
   * @param string|null $context  Optional. Context of the check.
   *
   * @return boolean Either true (active) or false (inactive or not installed).
   */

  function fictioneer_caching_active( $context = null ) {
    // Check early
    if ( get_option( 'fictioneer_enable_cache_compatibility' ) ) {
      return apply_filters( 'fictioneer_filter_caching_active', true, $context, true );
    }

    // Check active plugins
    $active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
    $active_cache_plugins = array_intersect( $active_plugins, array_keys( FICTIONEER_KNOWN_CACHE_PLUGINS ) );

    // Any cache plugins active?
    return apply_filters( 'fictioneer_filter_caching_active', ! empty( $active_cache_plugins ), $context, false );
  }
}

if ( ! function_exists( 'fictioneer_private_caching_active' ) ) {
  /**
   * Check whether private caching is active.
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

// =============================================================================
// ENABLE TRANSIENTS?
// =============================================================================

/**
 * Whether to enable Transients for shortcodes.
 *
 * @since 5.6.3
 * @since 5.23.1 - Do not turn off with cache plugin.
 * @since 5.25.0 - Refactored with option.
 *
 * @param string $shortcode  The shortcode in question.
 *
 * @return boolean Either true or false.
 */

function fictioneer_enable_shortcode_transients( $shortcode = null ) {
  global $pagenow;

  if ( is_customize_preview() || is_admin() || $pagenow === 'post.php' ) {
    return false;
  }

  $bool = FICTIONEER_SHORTCODE_TRANSIENT_EXPIRATION > -1 &&
    ! get_option( 'fictioneer_disable_shortcode_transients' );

  return apply_filters( 'fictioneer_filter_enable_shortcode_transients', $bool, $shortcode );
}

/**
 * Whether to enable Transients for story chapter lists.
 *
 * @since 5.25.0
 * @since 5.27.4 - Does no longer consider cache plugins.
 *
 * @param int $post_id  Post ID of the story.
 *
 * @return boolean Either true or false.
 */

function fictioneer_enable_chapter_list_transients( $post_id ) {
  if ( is_customize_preview() ) {
    return false;
  }

  return apply_filters(
    'fictioneer_filter_enable_chapter_list_transients',
    ! get_option( 'fictioneer_disable_chapter_list_transients' ),
    $post_id
  );
}

/**
 * Whether to enable Transients for menus.
 *
 * @since 5.25.0
 *
 * @param string $location  Location identifier of the menu. Possible locations are
 *                          'nav_menu', 'mobile_nav_menu', and 'footer_menu'.
 *
 * @return boolean Either true or false.
 */

function fictioneer_enable_menu_transients( $location ) {
  if ( is_customize_preview() ) {
    return false;
  }

  return apply_filters(
    'fictioneer_filter_enable_menu_transients',
    ! get_option( 'fictioneer_disable_menu_transients' ),
    $location
  );
}

// =============================================================================
// PURGE CACHES
// =============================================================================

if ( ! function_exists( 'fictioneer_purge_all_caches' ) ) {
  /**
   * Trigger the "purge all" functions of known cache plugins.
   *
   * @since 4.0.0
   * @link https://docs.litespeedtech.com/lscache/lscwp/api/#purge
   * @link https://docs.wp-rocket.me/article/494-how-to-clear-cache-via-cron-job#clear-cache
   * @link https://wp-kama.com/plugin/wp-super-cache/function/wp_cache_clean_cache
   */

  function fictioneer_purge_all_caches() {
    // Object cache
    wp_cache_flush();

    // Hook for additional purges
    do_action( 'fictioneer_cache_purge_all' );

    // LiteSpeed Cache
    do_action( 'litespeed_purge_all' );

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
    do_action( 'cache_enabler_clear_complete_cache' );

    // Hummingbird
    do_action( 'wphb_clear_page_cache' );

    // SG Optimizer
    // Insufficient or hard-to-find documentation and if the
    // developer cannot be bothered, neither can I.

    // Breeze
    do_action( 'breeze_clear_all_cache' );

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
   * Trigger the "purge post" functions of known cache plugins.
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
    do_action( 'litespeed_purge_post', $post_id );

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
    do_action( 'cache_enabler_clear_page_cache_by_post', $post_id );

    // Hummingbird
    do_action( 'wphb_clear_page_cache', $post_id );

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
// PURGE RELEVANT CACHE(S) ON POST SAVE
// =============================================================================

if ( ! function_exists( 'fictioneer_purge_template_caches' ) ) {
  /**
   * Purge all posts with the given template template.
   *
   * @since 5.5.2
   *
   * @param string $template  Page template name (without `.php`).
   */

  function fictioneer_purge_template_caches( $template ) {
    global $wpdb;

    // Setup (this should normally only yield one page or none)
    $pages = $wpdb->get_col(
      $wpdb->prepare(
        "SELECT p.ID
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
        WHERE p.post_type = %s
          AND p.post_status IN ( 'publish', 'private' )
          AND pm.meta_key = '_wp_page_template'
          AND pm.meta_value = %s",
        'page',
        "{$template}.php"
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
   * Purge relevant caches when a post is updated.
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
    fictioneer_toggle_stud_actions( 'fictioneer_refresh_post_caches', false, 20 );
    fictioneer_toggle_stud_actions( 'fictioneer_purge_transients_after_update', false, 10 );
    fictioneer_toggle_stud_actions( 'fictioneer_track_chapter_and_story_updates', false, 10 );

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

    if ( $post_type === 'fcn_recommendation' ) {
      fictioneer_purge_template_caches( 'recommendations' );
    }

    // Purge parent story (if any)...
    if ( $post_type === 'fcn_chapter' ) {
      $story_id = fictioneer_get_chapter_story_id( $post_id );

      if ( $story_id ) {
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
    if ( $post_type === 'fcn_story' ) {
      $chapters = fictioneer_get_story_chapter_ids( $post_id );

      if ( ! empty( $chapters ) ) {
        foreach ( $chapters as $chapter_id ) {
          fictioneer_purge_post_cache( $chapter_id );
        }
      }
    }

    // Purge all collections (cheapest option)
    if ( $post_type !== 'page' ) {
      fictioneer_purge_template_caches( 'collections' );

      global $wpdb;

      $collections = $wpdb->get_col(
        $wpdb->prepare(
          "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status IN ( 'publish', 'private' )",
          'fcn_collection'
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
    fictioneer_toggle_stud_actions( 'fictioneer_refresh_post_caches', true, 20 );
    fictioneer_toggle_stud_actions( 'fictioneer_purge_transients_after_update', true, 10 );
    fictioneer_toggle_stud_actions( 'fictioneer_track_chapter_and_story_updates', true, 10 );

    // End system action: restore user
    wp_set_current_user( $current_user_id );
  }
}

if ( FICTIONEER_CACHE_PURGE_ASSIST && fictioneer_caching_active( 'purge_assist' ) ) {
  fictioneer_toggle_stud_actions( 'fictioneer_refresh_post_caches', true, 20 );
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
 * Return relationship registry from options table.
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
 * Save relationship registry to options table.
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
 * Remove relationships on delete.
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
   * Update Transients and storage options when a story or chapter is updated.
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
    $story_id = $post_type === 'fcn_story' ? $post_id : fictioneer_get_chapter_story_id( $post_id );

    // Delete cached statistics for stories
    delete_transient( 'fictioneer_stories_statistics' );
    delete_transient( 'fictioneer_stories_total_word_count' );

    // If there is a story...
    if ( $story_id ) {
      // Decides when cached story/chapter data need to be refreshed (only used for Follows)
      // Beware: This is an option, not a Transient!
      update_option( 'fictioneer_story_or_chapter_updated_timestamp', time() * 1000 );

      // Clear meta caches
      delete_post_meta( $story_id, 'fictioneer_story_data_collection' );
      delete_post_meta( $story_id, 'fictioneer_story_chapter_index_html' );

      // Refresh cached HTML output
      delete_transient( 'fictioneer_story_chapter_list_html_' . $story_id );
    }
  }
}

fictioneer_toggle_stud_actions( 'fictioneer_track_chapter_and_story_updates', true, 10 );

// =============================================================================
// PURGE CACHE TRANSIENTS
// =============================================================================

/**
 * Purge layout-related Transients.
 *
 * @since 5.25.0
 */

function fictioneer_delete_layout_transients() {
  fictioneer_purge_nav_menu_transients();
  fictioneer_purge_story_card_cache();
  fictioneer_delete_transients_like( 'fictioneer_shortcode' );
  fictioneer_delete_transients_like( 'fictioneer_taxonomy_submenu_' );
  delete_transient( 'fictioneer_dynamic_scripts_version' );
}

/**
 * Purge Transients used for caching when posts are updated.
 *
 * @since 5.4.9
 * @since 5.23.0 - Refactored to purge all shortcode Transients.
 *
 * @param int $post_id  Updated post ID.
 */

function fictioneer_purge_transients_after_update( $post_id ) {
  // Prevent multi-fire
  if ( fictioneer_multi_save_guard( $post_id ) ) {
    return;
  }

  // Menus
  fictioneer_purge_nav_menu_transients();

  // Shortcodes...
  fictioneer_delete_transients_like( 'fictioneer_shortcode' );
}

fictioneer_toggle_stud_actions( 'fictioneer_purge_transients_after_update', true, 10 );

/**
 * Purge nav menu Transients.
 *
 * @since 5.6.0
 */

function fictioneer_purge_nav_menu_transients() {
  delete_transient( 'fictioneer_main_nav_menu_html' );
  delete_transient( 'fictioneer_footer_menu_html' );
  delete_transient( 'fictioneer_mobile_nav_menu_html' );
  fictioneer_delete_transients_like( 'fictioneer_taxonomy_submenu_' );
}
add_action( 'wp_update_nav_menu', 'fictioneer_purge_nav_menu_transients' );

// =============================================================================
// PARTIAL CACHING (THEME)
// =============================================================================

/**
 * Generate random salt for cache-related hashes.
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
 * Get salt for cache-related hashes.
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
 * Get static HTML of cached template partial.
 *
 * @since 5.18.3
 *
 * @param string|null $dir  Optional. Directory path to override the default.
 *
 * @return bool True if the directory exists or has been created, false on failure.
 */

function fictioneer_create_html_cache_directory( $dir = null ) {
  // Setup
  $default_dir = fictioneer_get_theme_cache_dir( 'create_html_cache_directory' ) . '/html/';
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
 * Get static HTML of cached template partial.
 *
 * @since 5.18.1
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
  $path = fictioneer_get_theme_cache_dir( 'get_cached_partial' ) . '/html/' . $static_file;

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
 * Clear static HTML of all cached partials.
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
  $cache_dir = fictioneer_get_theme_cache_dir( 'clear_all_cached_partials' ) . '/html/';

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
 * Return post content from cached HTML file.
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
  $dir = fictioneer_get_theme_cache_dir( 'get_static_content' ) . '/html/' . substr( $hash, 0, 2 );
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
 * Render post content from cached HTML file.
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
 * Clear static content HTML of post.
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
  $dir = fictioneer_get_theme_cache_dir( 'clear_cached_content' ) . '/html/' . substr( $hash, 0, 2 );
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
  fictioneer_toggle_stud_actions( 'fictioneer_clear_cached_content', true, 10 );
}

// =============================================================================
// TRANSIENT CARD CACHE
// =============================================================================

/**
 * Check whether the story card cache is locked by another process.
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
 * Return the Transient array with all cached story cards.
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
 * Add a story card to the cache.
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
 * Return the cached HTML for a specific story card.
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
 * Purge the entire story card cache.
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
 * Delete a specific card from the story card cache.
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
 * Delete a specific card from the story card cache after update.
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
    $story_id = fictioneer_get_chapter_story_id( $post_id );

    if ( $story_id ) {
      fictioneer_delete_cached_story_card( $story_id );
    }
  }
}
add_action( 'save_post', 'fictioneer_delete_cached_story_card_after_update' );

/**
 * Delete a specific card from the story card cache by comment ID.
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
    $story_id = fictioneer_get_chapter_story_id( $comment->comment_post_ID );

    if ( $story_id ) {
      fictioneer_delete_cached_story_card( $story_id );
    }
  }
}
add_action( 'wp_insert_comment', 'fictioneer_delete_cached_story_card_by_comment' );
add_action( 'delete_comment', 'fictioneer_delete_cached_story_card_by_comment' );

/**
 * Save the story card cache array as Transient.
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
