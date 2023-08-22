<?php

// =============================================================================
// CONSTANTS/SETTINGS
// =============================================================================

// Version
define( 'FICTIONEER_VERSION', '5.6.2' );
define( 'FICTIONEER_MAJOR_VERSION', '5' );
define( 'FICTIONEER_RELEASE_TAG', 'v5.6.2' );

if ( ! defined( 'CHILD_VERSION' ) ) {
  define( 'CHILD_VERSION', false );
}

if ( ! defined( 'CHILD_NAME' ) ) {
  define( 'CHILD_NAME', false );
}

/*
 * Endpoints
 */

// Endpoint: OAuth 2.0
if ( ! defined( 'FICTIONEER_OAUTH_ENDPOINT' ) ) {
  define( 'FICTIONEER_OAUTH_ENDPOINT', 'oauth2' );
}

// Endpoint: EPUBs
if ( ! defined( 'FICTIONEER_EPUB_ENDPOINT' ) ) {
  define( 'FICTIONEER_EPUB_ENDPOINT', 'download-epub' );
}

// Endpoint: Logout (disable with false)
if ( ! defined( 'FICTIONEER_LOGOUT_ENDPOINT' ) ) {
  define( 'FICTIONEER_LOGOUT_ENDPOINT', 'fictioneer-logout' );
}

/*
 * Strings
 */

// String: CSS name of the primary font
if ( ! defined( 'FICTIONEER_PRIMARY_FONT_CSS' ) ) {
  define( 'FICTIONEER_PRIMARY_FONT_CSS', 'Open Sans' );
}

// String: Display name of the primary font
if ( ! defined( 'FICTIONEER_PRIMARY_FONT_NAME' ) ) {
  define( 'FICTIONEER_PRIMARY_FONT_NAME', 'Open Sans' );
}

// String: Meta charset attribute (skip database call if truthy for better performance)
if ( ! defined( 'FICTIONEER_SITE_CHARSET' ) ) {
  define( 'FICTIONEER_SITE_CHARSET', get_bloginfo( 'charset' ) );
}

// String: Lang attribute (skip database call if truthy for better performance)
if ( ! defined( 'FICTIONEER_SITE_LANGUAGE' ) ) {
  define( 'FICTIONEER_SITE_LANGUAGE', get_bloginfo( 'language' ) );
}

// String: Site name (skip database call if truthy for better performance)
if ( ! defined( 'FICTIONEER_SITE_NAME' ) ) {
  define( 'FICTIONEER_SITE_NAME', get_bloginfo( 'name' ) );
}

// String: Site description (skip database call if truthy for better performance)
if ( ! defined( 'FICTIONEER_SITE_DESCRIPTION' ) ) {
  define( 'FICTIONEER_SITE_DESCRIPTION', get_bloginfo( 'description' ) );
}

// String: TTS regex (used to split text into sentences)
if ( ! defined( 'FICTIONEER_TTS_REGEX' ) ) {
  // Note: Because lookbehind assertions do not work in Safari, the script uses a little
  // detour to preserve punctuation marks: text.replace(regex, '$1|').split('|')
  define( 'FICTIONEER_TTS_REGEX', '([.!?:"\'\u201C\u201D])\s+(?=[A-Z"\'\u201C\u201D])' );
}

// String: Default list of allowed mime types for upload restrictions
define(
  'FICTIONEER_DEFAULT_UPLOAD_MIME_TYPE_RESTRICTIONS',
  'image/jpeg, image/png, image/webp, image/avif, image/gif, application/pdf, application/epub+zip, application/rtf, text/plain, image/svg+xml'
);

/*
 * Date Strings
 */

$short_date = get_option( 'fictioneer_subitem_short_date_format', 'M j' ) ?: 'M j';
$long_date = get_option( 'fictioneer_subitem_date_format', "M j, 'y" ) ?: "M j, 'y";

// String: Latest Updates shortcode list item date format
if ( ! defined( 'FICTIONEER_LATEST_UPDATES_LI_DATE' ) ) {
  define( 'FICTIONEER_LATEST_UPDATES_LI_DATE', $short_date );
}

// String: Latest Updates shortcode footer date format
if ( ! defined( 'FICTIONEER_LATEST_UPDATES_FOOTER_DATE' ) ) {
  define( 'FICTIONEER_LATEST_UPDATES_FOOTER_DATE', $long_date );
}

// String: Latest Chapters shortcode footer date format
if ( ! defined( 'FICTIONEER_LATEST_CHAPTERS_FOOTER_DATE' ) ) {
  define( 'FICTIONEER_LATEST_CHAPTERS_FOOTER_DATE', $long_date );
}

// String: Latest Stories shortcode footer date format
if ( ! defined( 'FICTIONEER_LATEST_STORIES_FOOTER_DATE' ) ) {
  define( 'FICTIONEER_LATEST_STORIES_FOOTER_DATE', $long_date );
}

// String: Story card list item date format
if ( ! defined( 'FICTIONEER_CARD_STORY_LI_DATE' ) ) {
  define( 'FICTIONEER_CARD_STORY_LI_DATE', $long_date );
}

// String: Story card footer date format
if ( ! defined( 'FICTIONEER_CARD_STORY_FOOTER_DATE' ) ) {
  define( 'FICTIONEER_CARD_STORY_FOOTER_DATE', $long_date );
}

// String: Chapter card footer date format
if ( ! defined( 'FICTIONEER_CARD_CHAPTER_FOOTER_DATE' ) ) {
  define( 'FICTIONEER_CARD_CHAPTER_FOOTER_DATE', $long_date );
}

// String: Collection card list item date format
if ( ! defined( 'FICTIONEER_CARD_COLLECTION_LI_DATE' ) ) {
  define( 'FICTIONEER_CARD_COLLECTION_LI_DATE', $long_date );
}

// String: Collection card footer date format
if ( ! defined( 'FICTIONEER_CARD_COLLECTION_FOOTER_DATE' ) ) {
  define( 'FICTIONEER_CARD_COLLECTION_FOOTER_DATE', $long_date );
}

// String: Post card footer date format
if ( ! defined( 'FICTIONEER_CARD_POST_FOOTER_DATE' ) ) {
  define( 'FICTIONEER_CARD_POST_FOOTER_DATE', $long_date );
}

// String: Page card footer date format
if ( ! defined( 'FICTIONEER_CARD_PAGE_FOOTER_DATE' ) ) {
  define( 'FICTIONEER_CARD_PAGE_FOOTER_DATE', $long_date );
}

// String: Story page footer date format below 480px
if ( ! defined( 'FICTIONEER_STORY_FOOTER_B480_DATE' ) ) {
  define( 'FICTIONEER_STORY_FOOTER_B480_DATE', $long_date );
}

/*
 * Integers
 */

// Integer: Commentcode expiration timer in seconds (-1 for infinite)
if ( ! defined( 'FICTIONEER_COMMENTCODE_TTL' ) ) {
  define( 'FICTIONEER_COMMENTCODE_TTL', 600 );
}

// Integer: AJAX cache TTL in milliseconds
if ( ! defined( 'FICTIONEER_AJAX_TTL' ) ) {
  define( 'FICTIONEER_AJAX_TTL', 60000 );
}

// Integer: AJAX login cache TTL in milliseconds
if ( ! defined( 'FICTIONEER_AJAX_LOGIN_TTL' ) ) {
  define( 'FICTIONEER_AJAX_LOGIN_TTL', 15000 );
}

// Integer: AJAX POST debounce rate in milliseconds
if ( ! defined( 'FICTIONEER_AJAX_POST_DEBOUNCE_RATE' ) ) {
  define( 'FICTIONEER_AJAX_POST_DEBOUNCE_RATE', 700 );
}

// Integer: Author search keyword limit (use simple text input if exceeded)
if ( ! defined( 'FICTIONEER_AUTHOR_KEYWORD_SEARCH_LIMIT' ) ) {
  define( 'FICTIONEER_AUTHOR_KEYWORD_SEARCH_LIMIT', 100 );
}

// Integer: Update check timeout
if ( ! defined( 'FICTIONEER_UPDATE_CHECK_TIMEOUT' ) ) {
  define( 'FICTIONEER_UPDATE_CHECK_TIMEOUT', 43200 ); // 12 hours
}

// Integer: Storygraph API cache TTL
if ( ! defined( 'FICTIONEER_API_STORYGRAPH_CACHE_TTL' ) ) {
  define( 'FICTIONEER_API_STORYGRAPH_CACHE_TTL', 3600 );
}

// Integer: Storygraph API number of stories per page
if ( ! defined( 'FICTIONEER_API_STORYGRAPH_STORIES_PER_PAGE' ) ) {
  define( 'FICTIONEER_API_STORYGRAPH_STORIES_PER_PAGE', 10 );
}

// Integer: Maximum number of displayed custom pages
if ( ! defined( 'FICTIONEER_MAX_CUSTOM_PAGES_PER_STORY' ) ) {
  define( 'FICTIONEER_MAX_CUSTOM_PAGES_PER_STORY', 4 );
}

// Integer: The number of chapter before and after folding in chapter lists
if ( ! defined( 'FICTIONEER_CHAPTER_FOLDING_THRESHOLD' ) ) {
  define( 'FICTIONEER_CHAPTER_FOLDING_THRESHOLD', 5 );
}

// Integer: Expiration time for shortcode Transients (0 for infinite, -1 to disable)
if ( ! defined( 'FICTIONEER_SHORTCODE_TRANSIENT_EXPIRATION' ) ) {
  define( 'FICTIONEER_SHORTCODE_TRANSIENT_EXPIRATION', 300 );
}

// Integer: Timeout between refreshes of story comment counts (0 for always)
if ( ! defined( 'FICTIONEER_STORY_COMMENT_COUNT_TIMEOUT' ) ) {
  define( 'FICTIONEER_STORY_COMMENT_COUNT_TIMEOUT', 900 );
}

/*
 * Booleans
 */

// Prevent warnings
if ( ! defined( 'WP_DEBUG' ) ) {
  define( 'WP_DEBUG', false );
}

// Boolean: Theme cache purging on post update
if ( ! defined( 'FICTIONEER_CACHE_PURGE_ASSIST' ) ) {
  define( 'FICTIONEER_CACHE_PURGE_ASSIST', true );
}

// Boolean: Theme relationship cache purging on post update
if ( ! defined( 'FICTIONEER_RELATIONSHIP_PURGE_ASSIST' ) ) {
  define( 'FICTIONEER_RELATIONSHIP_PURGE_ASSIST', true );
}

// Boolean: Cache chapter lists on story pages as Transients
if ( ! defined( 'FICTIONEER_CHAPTER_LIST_TRANSIENTS' ) ) {
  define( 'FICTIONEER_CHAPTER_LIST_TRANSIENTS', true );
}

// Boolean: Menu items for search
if ( ! defined( 'FICTIONEER_SHOW_SEARCH_IN_MENUS' ) ) {
  define( 'FICTIONEER_SHOW_SEARCH_IN_MENUS', true );
}

// Boolean: Base theme switch in site settings
if ( ! defined( 'FICTIONEER_THEME_SWITCH' ) ) {
  define( 'FICTIONEER_THEME_SWITCH', true );
}

// Boolean: Attachment pages
if ( ! defined( 'FICTIONEER_ATTACHMENT_PAGES' ) ) {
  define( 'FICTIONEER_ATTACHMENT_PAGES', false );
}

// Boolean: Show OAuth ID Hashes (admin only)
if ( ! defined( 'FICTIONEER_SHOW_OAUTH_HASHES' ) ) {
  define( 'FICTIONEER_SHOW_OAUTH_HASHES', false );
}

// Boolean: Show AJAX comment submission disallowed content keys
if ( ! defined( 'FICTIONEER_DISALLOWED_KEY_NOTICE' ) ) {
  define( 'FICTIONEER_DISALLOWED_KEY_NOTICE', true );
}

// Boolean: Only allow assigned chapters in stories
// Warning: The chapter navigation always refers to the parent story!
if ( ! defined( 'FICTIONEER_FILTER_STORY_CHAPTERS' ) ) {
  define( 'FICTIONEER_FILTER_STORY_CHAPTERS', true );
}

// Boolean: Only show the full comment form after clicking into it
if ( ! defined( 'FICTIONEER_COLLAPSE_COMMENT_FORM' ) ) {
  define( 'FICTIONEER_COLLAPSE_COMMENT_FORM', true );
}

// Boolean: Storygraph API image nodes
if ( ! defined( 'FICTIONEER_API_STORYGRAPH_IMAGES' ) ) {
  define( 'FICTIONEER_API_STORYGRAPH_IMAGES', true );
}

// Boolean: Storygraph API hotlink permission
if ( ! defined( 'FICTIONEER_API_STORYGRAPH_HOTLINK' ) ) {
  define( 'FICTIONEER_API_STORYGRAPH_HOTLINK', false );
}

// Boolean: Storygraph API with chapters in /stories
if ( ! defined( 'FICTIONEER_API_STORYGRAPH_CHAPTERS' ) ) {
  define( 'FICTIONEER_API_STORYGRAPH_CHAPTERS', true );
}

// Boolean: Storygraph API Transients caching
if ( ! defined( 'FICTIONEER_API_STORYGRAPH_TRANSIENTS' ) ) {
  define( 'FICTIONEER_API_STORYGRAPH_TRANSIENTS', true );
}

// Boolean: Disable ACF JSON import
if ( ! defined( 'FICTIONEER_DISABLE_ACF_JSON_IMPORT' ) ) {
  define( 'FICTIONEER_DISABLE_ACF_JSON_IMPORT', false );
}

// Boolean: Enable sticky cards
if ( ! defined( 'FICTIONEER_ENABLE_STICKY_CARDS' ) ) {
  define( 'FICTIONEER_ENABLE_STICKY_CARDS', true );
}

// Boolean: Enable story data meta cache
if ( ! defined( 'FICTIONEER_ENABLE_STORY_DATA_META_CACHE' ) ) {
  define( 'FICTIONEER_ENABLE_STORY_DATA_META_CACHE', true );
}

// Boolean: Enable ACF on frontend
if ( ! defined( 'FICTIONEER_ENABLE_FRONTEND_ACF' ) ) {
  define( 'FICTIONEER_ENABLE_FRONTEND_ACF', false );
}

// =============================================================================
// FAST AJAX REQUESTS
//
// This is significantly faster than even the REST API because it skips most of
// the theme initialization and the init() action. However, this also means you
// cannot use any actions and filters not loaded at this point, ignoring child
// themes altogether. Customization is still possible with MU plugins and in
// the config.php, although the latter is bad practice.
// =============================================================================

if ( ! defined( 'FICTIONEER_FAST_AJAX_FUNCTIONS' ) ) {
  define(
    'FICTIONEER_FAST_AJAX_FUNCTIONS',
    array(
      // System
      'fictioneer_ajax_is_user_logged_in',
      'fictioneer_ajax_get_nonce',
      'fictioneer_ajax_get_avatar',
      // Comments
      'fictioneer_ajax_delete_my_comment',
      'fictioneer_ajax_moderate_comment',
      'fictioneer_ajax_report_comment',
      // Bookmarks
      'fictioneer_ajax_save_bookmarks',
      'fictioneer_ajax_get_bookmarks',
      // Follows
      'fictioneer_ajax_get_follows',
      'fictioneer_ajax_toggle_follow',
      'fictioneer_ajax_clear_my_follows',
      'fictioneer_ajax_mark_follows_read',
      'fictioneer_ajax_get_follows_notifications',
      'fictioneer_ajax_get_follows_list',
      // Reminders
      'fictioneer_ajax_get_reminders',
      'fictioneer_ajax_toggle_reminder',
      'fictioneer_ajax_clear_my_reminders',
      'fictioneer_ajax_get_reminders_list',
      // Checkmarks
      'fictioneer_ajax_get_checkmarks',
      'fictioneer_ajax_set_checkmark',
      'fictioneer_ajax_clear_my_checkmarks',
      'fictioneer_ajax_get_finished_checkmarks_list',
      // User
      'fictioneer_ajax_get_fingerprint'
    )
  );
}

if (
  defined( 'DOING_AJAX' ) && DOING_AJAX &&
  isset( $_REQUEST['fcn_fast_ajax'] ) &&
  isset( $_REQUEST['action'] ) &&
  ! ( defined('REST_REQUEST') && REST_REQUEST ) &&
  defined( 'FICTIONEER_FAST_AJAX_FUNCTIONS' ) &&
  is_array( FICTIONEER_FAST_AJAX_FUNCTIONS )
) {
  fictioneer_do_fast_ajax();
}

function fictioneer_do_fast_ajax() {
  $action = sanitize_text_field( $_REQUEST['action'] );

  // Allowed action?
  if (
    ! in_array( $action, FICTIONEER_FAST_AJAX_FUNCTIONS, true ) ||
    strpos( $action, 'fictioneer_ajax' ) !== 0
  ) {
    return;
  }

  // Include required files
  require_once __DIR__ . '/includes/functions/_utility.php';
  require_once __DIR__ . '/includes/functions/_query_helpers.php';
  require_once __DIR__ . '/includes/functions/_caching_and_transients.php';

  if ( get_option( 'fictioneer_enable_follows' ) && strpos( $action, '_follow' ) !== false ) {
    require_once __DIR__ . '/includes/functions/_content_helpers.php';
    require_once __DIR__ . '/includes/functions/users/_follows.php';
  }

  if ( get_option( 'fictioneer_enable_reminders' ) && strpos( $action, '_reminder' ) !== false ) {
    require_once __DIR__ . '/includes/functions/_content_helpers.php';
    require_once __DIR__ . '/includes/functions/users/_reminders.php';
  }

  if ( get_option( 'fictioneer_enable_checkmarks' ) && strpos( $action, '_checkmark' ) !== false ) {
    require_once __DIR__ . '/includes/functions/_content_helpers.php';
    require_once __DIR__ . '/includes/functions/users/_checkmarks.php';
  }

  if ( get_option( 'fictioneer_enable_bookmarks' ) && strpos( $action, '_bookmarks' ) !== false ) {
    require_once __DIR__ . '/includes/functions/users/_bookmarks.php';
  }

  if ( strpos( $action, '_list' ) !== false ) {
    require_once __DIR__ . '/includes/functions/_wordpress_mods.php';
  }

  if ( strpos( $action, '_comment' ) !== false ) {
    require_once __DIR__ . '/includes/functions/comments/_comments_controller.php';
    require_once __DIR__ . '/includes/functions/comments/_comments_ajax.php';
  }

  if ( strpos( $action, '_moderate_comment' ) !== false ) {
    require_once __DIR__ . '/includes/functions/comments/_comments_moderation.php';
  }

  if ( strpos( $action, '_fingerprint' ) !== false ) {
    require_once __DIR__ . '/includes/functions/users/_user_data.php';
  }

  if ( strpos( $action, '_avatar' ) !== false ) {
    require_once __DIR__ . '/includes/functions/users/_avatars.php';
  }

  // Skip cache checks
  if ( ! function_exists( 'fictioneer_caching_active' ) ) {
    function fictioneer_caching_active() { return false; };
  }

  if ( ! function_exists( 'fictioneer_private_caching_active' ) ) {
    function fictioneer_private_caching_active() { return false; };
  }

  // Skip cache purging
  if ( ! function_exists( 'fictioneer_refresh_post_caches' ) ) {
    function fictioneer_refresh_post_caches() { return; };
  }

  // Function exists?
  if ( ! function_exists( $action ) ) {
    return;
  }

  // Call function
  call_user_func( $action );

  // Terminate in case something goes wrong
  die();
}

// =============================================================================
// GLOBAL
// =============================================================================

/**
 * Provides various utility functions.
 */

require_once __DIR__ . '/includes/functions/_utility.php';

/**
 * Modify WordPress functions via hooks.
 */

require_once __DIR__ . '/includes/functions/_wordpress_mods.php';

/**
 * Actions whenever a post is created or updated.
 */

require_once __DIR__ . '/includes/functions/_post_updates.php';

/**
 * Set up the theme customizer.
 */

require_once __DIR__ . '/includes/functions/_customizer.php';

if ( is_customize_preview() ) {
  require_once __DIR__ . '/includes/functions/_customizer-settings.php';
}

/**
 * Set up the theme.
 */

require_once __DIR__ . '/includes/functions/_theme_setup.php';

/**
 * Configuration for the Advanced Custom Fields plugin.
 */

require_once __DIR__ . '/includes/functions/_acf.php';

/**
 * Add custom post types.
 */

require_once __DIR__ . '/includes/functions/_cpt_and_taxonomies.php';

/**
 * Add custom shortcodes.
 */

require_once __DIR__ . '/includes/functions/_shortcodes.php';

/**
 * Caches and Transients.
 */

require_once __DIR__ . '/includes/functions/_caching_and_transients.php';

/**
 * Generate sitemap.
 */

require_once __DIR__ . '/includes/functions/_sitemap.php';

/**
 * Add SEO features to the site.
 */

require_once __DIR__ . '/includes/functions/_seo.php';

/**
 * Communicate with the Discord API.
 */

require_once __DIR__ . '/includes/functions/_discord.php';

/**
 * Generate ePUBs for stories.
 */

if ( get_option( 'fictioneer_enable_epubs' ) ) {
  require_once __DIR__ . '/includes/functions/_epub.php';
}

/**
 * Log-in and register subscribers via OAuth 2.0.
 */

if ( get_option( 'fictioneer_enable_oauth' ) ) {
  require_once __DIR__ . '/includes/functions/_oauth.php';
}

/**
 * Handle comments.
 */

require_once __DIR__ . '/includes/functions/comments/_comments_controller.php'; // Used in REST
require_once __DIR__ . '/includes/functions/comments/_story_comments.php'; // Used in REST

if ( is_admin() ) {
  // Required for AJAX
  require_once __DIR__ . '/includes/functions/comments/_comments_ajax.php';
  require_once __DIR__ . '/includes/functions/comments/_comments_form.php';
  require_once __DIR__ . '/includes/functions/comments/_comments_threads.php';
  require_once __DIR__ . '/includes/functions/comments/_comments_moderation.php';
}

function fictioneer_conditional_require_comments() {
  if ( comments_open() ) {
    require_once __DIR__ . '/includes/functions/comments/_comments_form.php';
    require_once __DIR__ . '/includes/functions/comments/_comments_threads.php';
    require_once __DIR__ . '/includes/functions/comments/_comments_moderation.php';
  }
}
add_action( 'wp', 'fictioneer_conditional_require_comments' );

/**
 * Add functions for users.
 */

require_once __DIR__ . '/includes/functions/users/_user_data.php';

/**
 * Add functions to handle avatars.
 */

require_once __DIR__ . '/includes/functions/users/_avatars.php';

/**
 * Add the follow feature.
 */

if ( get_option( 'fictioneer_enable_follows' ) ) {
  require_once __DIR__ . '/includes/functions/users/_follows.php';
}

/**
 * Add the reminders feature.
 */

if ( get_option( 'fictioneer_enable_reminders' ) ) {
  require_once __DIR__ . '/includes/functions/users/_reminders.php';
}

/**
 * Add the checkmarks features.
 */

if ( get_option( 'fictioneer_enable_checkmarks' ) ) {
  require_once __DIR__ . '/includes/functions/users/_checkmarks.php';
}

/**
 * Add the bookmarks feature.
 */

if ( get_option( 'fictioneer_enable_bookmarks' ) && is_admin() ) {
  // Only used for AJAX
  require_once __DIR__ . '/includes/functions/users/_bookmarks.php';
}

/**
 * Add content helper functions.
 */

require_once __DIR__ . '/includes/functions/_content_helpers.php';

/**
 * Add content query functions.
 */

require_once __DIR__ . '/includes/functions/_query_helpers.php';

/**
 * Add content helper functions.
 */

require_once __DIR__ . '/includes/functions/_roles.php';

/**
 * Add forms.
 */

require_once __DIR__ . '/includes/functions/_forms.php';

/**
 * Add API.
 */

if ( get_option( 'fictioneer_enable_storygraph_api' ) ) {
  require_once __DIR__ . '/includes/functions/_api.php';
}

/**
 * Add search.
 */

require_once __DIR__ . '/includes/functions/_search.php';

// =============================================================================
// ADMIN ONLY
// =============================================================================

if ( is_admin() ) {

  /**
   * Functions only required in the admin panel.
   */

  require_once __DIR__ . '/includes/functions/_admin.php';

}

// =============================================================================
// FRONTEND ONLY
// =============================================================================

if ( ! is_admin() ) {

  /**
   * Add general hooks.
   */

  require_once __DIR__ . '/includes/functions/hooks/_general_hooks.php';

  /**
   * Add post and page hooks.
   */

  function fictioneer_conditional_require_frontend_hooks() {
    if ( is_page_template( 'stories.php' ) || is_singular( 'fcn_story' ) ) {
      require_once __DIR__ . '/includes/functions/hooks/_story_hooks.php';
    }

    if ( is_page_template( 'chapters.php' ) || is_singular( 'fcn_chapter' ) ) {
      require_once __DIR__ . '/includes/functions/hooks/_chapter_hooks.php';
    }

    if ( is_page_template( 'recommendations.php' ) || is_singular( 'fcn_recommendation' ) ) {
      require_once __DIR__ . '/includes/functions/hooks/_recommendation_hooks.php';
    }

    if ( is_page_template( 'collections.php' ) || is_singular( 'fcn_collection' ) ) {
      require_once __DIR__ . '/includes/functions/hooks/_collection_hooks.php';
    }

    if ( is_page_template( 'user-profile.php' ) ) {
      require_once __DIR__ . '/includes/functions/hooks/_profile_hooks.php';
    }

    if ( is_singular( 'post' ) ) {
      require_once __DIR__ . '/includes/functions/hooks/_post_hooks.php';
    }
  }
  add_action( 'wp', 'fictioneer_conditional_require_frontend_hooks' );

  /**
   * Add mobile menu hooks.
   */

  require_once __DIR__ . '/includes/functions/hooks/_mobile_menu_hooks.php';

  /**
   * Generate SEO schema graphs.
   */

  if ( get_option( 'fictioneer_enable_seo' ) && ! fictioneer_seo_plugin_active() ) {
    require_once __DIR__ . '/includes/functions/_schemas.php';
  }
}

?>
