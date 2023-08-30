<?php

// =============================================================================
// LEGACY CLEANUP
// =============================================================================

/**
 * Clean up obsolete database entries
 *
 * @since 5.6.0
 */

function fictioneer_bring_out_legacy_trash() {
  // Setup
  $options = wp_cache_get( 'alloptions', 'options' );
  $obsolete = ['fictioneer_disable_html_in_comments', 'fictioneer_block_subscribers_from_admin', 'fictioneer_admin_restrict_menus', 'fictioneer_admin_restrict_private_data', 'fictioneer_admin_reduce_subscriber_profile', 'fictioneer_enable_subscriber_self_delete', 'fictioneer_strip_shortcodes_for_non_administrators', 'fictioneer_restrict_media_access', 'fictioneer_subscription_enabled', 'fictioneer_patreon_badge_map', 'fictioneer_patreon_tier_as_badge', 'fictioneer_patreon_campaign_ids', 'fictioneer_patreon_campaign_id', 'fictioneer_mount_wpdiscuz_theme_styles', 'fictioneer_base_site_width', 'fictioneer_comment_form_selector', 'fictioneer_featherlight_enabled', 'fictioneer_tts_enabled', 'fictioneer_log', 'fictioneer_enable_ajax_nonce'];

  // Check for most recent obsolete option...
  if ( isset( $options['fictioneer_enable_ajax_nonce'] ) ) {
    // Looping everything is not great but it only happens once!
    foreach ( $obsolete as $trash ) {
      delete_option( $trash );
    }
  }
}
add_action( 'admin_init', 'fictioneer_bring_out_legacy_trash' );

// =============================================================================
// THEME SETUP
// =============================================================================

/**
 * Sets up theme defaults and registers support for various WordPress features
 *
 * @since 1.0
 */

function fictioneer_theme_setup() {
  // Load translations (if any)
  load_theme_textdomain( 'fictioneer', get_template_directory() . '/languages' );

  // Let WordPress handle the title tag
  add_theme_support( 'title-tag' );

  // Activate support for featured images (thumbnails)
  add_theme_support(
    'post-thumbnails',
    array(
      'post',
      'page',
      'fcn_story',
      'fcn_chapter',
      'fcn_recommendation',
      'fcn_collection'
    )
  );

  // Register navigation menus
  register_nav_menu( 'nav_menu', __( 'Navigation' ) );
  register_nav_menu( 'footer_menu', __( 'Footer Menu' ) );

  // Switch core markup to valid HTML5 (except 'comment-form' because WP adds novalidate for some reason)
  add_theme_support(
    'html5',
    array(
      'comment-list',
      'search-form',
      'gallery',
      'caption',
      'style',
      'script'
    )
  );

  // Add support for custom backgrounds (with custom callback)
  add_theme_support( 'custom-background', array( 'wp-head-callback' => 'fictioneer_custom_background' ) );

  // Add support for custom header images
  add_theme_support(
    'custom-header',
    array(
      'flex-width' => true,
      'flex-height' => true,
      'height' => get_theme_mod( 'header_image_height_max', 480 )
    )
  );

  // Add support for editor styles
  add_theme_support( 'editor-styles' );
  add_editor_style( '/css/editor.css' );

  // Add support for custom logo
  add_theme_support(
    'custom-logo',
    array(
      'flex-width' => true,
      'width' => (int) get_theme_mod( 'header_image_height_max', 210 ) * 1.5,
      'flex-height' => true,
      'height' => get_theme_mod( 'header_image_height_max', 210 ),
      'header-text' => ['site-title', 'site-description'],
    )
  );

  // Remove block patterns
  remove_theme_support( 'core-block-patterns' );

  // Disable template editor (automatically added due to theme.json!)
  remove_theme_support( 'block-templates' );

  // Remove widget support...
  if ( get_option( 'fictioneer_disable_all_widgets' ) ) {
    remove_theme_support( 'widgets' );
    remove_theme_support( 'widgets-block-editor' );
  }

  // Add new size for cover images used on story pages
  add_image_size( 'cover', 400 );

  // Add new size for cover snippets used in cards
  add_image_size( 'snippet', 0, 200 );
}
add_action( 'after_setup_theme', 'fictioneer_theme_setup' );

// =============================================================================
// CUSTOM BACKGROUND CALLBACK
// =============================================================================

/**
 * Fictioneer version of _custom_background_cb() for custom backgrounds
 *
 * @since 4.7
 * @link https://developer.wordpress.org/reference/functions/_custom_background_cb/
 */

function fictioneer_custom_background() {
  // Get background image
  $background = set_url_scheme( get_background_image() );

  // Abort if no background image
  if ( ! $background ) {
    echo '';
    return;
  }

  // Assign background image to property
  $image = 'background-image: url("' . esc_url_raw( $background ) . '");';

  // Background position
  $position_x = get_theme_mod( 'background_position_x' );
  $position_y = get_theme_mod( 'background_position_y' );

  if ( ! in_array( $position_x, ['left', 'center', 'right'], true ) ) {
    $position_x = 'left';
  }

  if ( ! in_array( $position_y, ['top', 'center', 'bottom'], true ) ) {
    $position_y = 'top';
  }

  $position = "background-position: {$position_x} {$position_y};";

  // Background size
  $size = get_theme_mod( 'background_size' );

  if ( ! in_array( $size, ['auto', 'contain', 'cover'], true ) ) {
    $size = 'auto';
  }

  $size = "background-size: {$size};";

  // Background repeat
  $repeat = get_theme_mod( 'background_repeat' );

  if ( ! in_array( $repeat, ['repeat-x', 'repeat-y', 'repeat', 'no-repeat'], true ) ) {
    $repeat = 'repeat';
  }

  $repeat = "background-repeat: {$repeat};";

  // Background scroll
  $attachment = get_theme_mod( 'background_attachment' );

  if ( $attachment !== 'fixed' ) {
    $attachment = 'scroll';
  }

  $attachment = "background-attachment: {$attachment};";

  // Build
  $style = $image . $position . $size . $repeat . $attachment;

  // Output
  echo '<style id="custom-background-css" type="text/css">.custom-background {' . trim( $style ) . '}</style>';
}

// =============================================================================
// MODIFY ALLOWED TAGS
// =============================================================================

/**
 * Update list of allowed tags
 *
 * @since 4.7
 *
 * @global array $allowedtags  Array of allowed tags.
 */

function fictioneer_modify_allowed_tags() {
  global $allowedtags;

  $allowedtags['ins'] = array(
    'datetime' => []
  );

  $allowedtags['p'] = [];
  $allowedtags['span'] = [];
  $allowedtags['br'] = [];
}
add_action( 'init', 'fictioneer_modify_allowed_tags', 20 );

// =============================================================================
// HTML ROOT INJECTION
// =============================================================================

/**
 * Injects attributes and classes into the <html> root
 *
 * @since 4.7
 */

function fictioneer_root_attributes() {
  $output = [];
  $header_classes = [];

  // Add configuration classes
  if ( get_theme_mod( 'inset_header_image', false ) ) {
    $header_classes[] = 'inset-header-image';
  }

  // Prepare
  $output['class'] = implode( ' ', $header_classes );
  $output['data-mode-default'] = get_option( 'fictioneer_light_mode_as_default', false ) ? 'light' : 'dark';
  $output['data-site-width-default'] = get_theme_mod( 'site_width', 960 );
  $output['data-theme'] = 'default';
  $output['data-mode'] = '';
  $output['data-font-weight'] = 'default';
  $output['data-primary-font'] = FICTIONEER_PRIMARY_FONT_CSS;

  $conditions = array(
    'data-caching-active' => fictioneer_caching_active(),
    'data-ajax-submit' => get_option( 'fictioneer_enable_ajax_comment_submit', false ),
    'data-force-child-theme' => ! FICTIONEER_THEME_SWITCH,
    'data-public-caching' => get_option( 'fictioneer_enable_public_cache_compatibility', false ),
    'data-ajax-auth' => get_option( 'fictioneer_enable_ajax_authentication', false ),
    'data-edit-time' => get_option( 'fictioneer_enable_user_comment_editing', false ) ?
      get_option( 'fictioneer_user_comment_edit_time', 15 ) : false,
  );

  // Iterate conditions and add the truthy to the output
  foreach ( $conditions as $key => $condition ) {
    if ( $condition ) {
      $output[ $key ] = is_bool( $condition ) ? '1' : $condition;
    }
  }

  // Filter output
  $output = apply_filters( 'fictioneer_filter_root_attributes', $output );

  // Output
  foreach ( $output as $key => $value ) {
    echo "{$key}='{$value}' ";
  }
}

// =============================================================================
// ENQUEUE STYLESHEETS
// =============================================================================

/**
 * Enqueues stylesheets
 *
 * @since 1.0
 * @since 4.7 Split stylesheets into separate concerns.
 */

function fictioneer_style_queue() {
  // Either load separate small style files on demand or the complete one
  if ( ! get_option( 'fictioneer_bundle_stylesheets' ) ) {
    // Setup
    $post_type = get_post_type();
    $application_dependencies = [];

    // Properties
    if ( ! get_option( 'fictioneer_disable_properties' ) ) {
      wp_enqueue_style(
        'fictioneer-properties',
        get_template_directory_uri() . '/css/properties.css',
        [],
        FICTIONEER_VERSION
      );
      $application_dependencies[] = 'fictioneer-properties';
    }

    // Application
    wp_enqueue_style(
      'fictioneer-application',
      get_template_directory_uri() . '/css/application.css',
      $application_dependencies,
      FICTIONEER_VERSION
    );

    // If NOT an archive or search page...
    if ( ! is_search() && ! is_archive() ) {
      // Collections
      if ( is_page_template( 'stories.php' ) || $post_type == 'fcn_collection' ) {
        wp_enqueue_style(
          'fictioneer-collections',
          get_template_directory_uri() . '/css/collections.css',
          ['fictioneer-application'],
          FICTIONEER_VERSION
        );
      }

      // Chapter
      if ( $post_type == 'fcn_chapter' ) {
        wp_enqueue_style(
          'fictioneer-chapter',
          get_template_directory_uri() . '/css/chapter.css',
          ['fictioneer-application'],
          FICTIONEER_VERSION
        );
      }

      // Story
      if ( $post_type == 'fcn_story' ) {
        wp_enqueue_style(
          'fictioneer-story',
          get_template_directory_uri() . '/css/story.css',
          ['fictioneer-application'],
          FICTIONEER_VERSION
        );
      }

      // Recommendation
      if ( $post_type == 'fcn_recommendation' ) {
        wp_enqueue_style(
          'fictioneer-recommendation',
          get_template_directory_uri() . '/css/recommendation.css',
          ['fictioneer-application'],
          FICTIONEER_VERSION
        );
      }

      // Comments
      if ( $post_type == 'fcn_story' || comments_open() ) {
        wp_enqueue_style(
          'fictioneer-comments',
          get_template_directory_uri() . '/css/comments.css',
          ['fictioneer-application'],
          FICTIONEER_VERSION
        );
      }
    }

    // Archive
    if ( is_archive() ) {
      wp_enqueue_style(
        'fictioneer-archive',
        get_template_directory_uri() . '/css/taxonomies.css',
        ['fictioneer-application'],
        FICTIONEER_VERSION
      );
    }
  } else {
    // Complete
    wp_enqueue_style(
      'fictioneer-complete',
      get_template_directory_uri() . '/css/complete.css',
      [],
      FICTIONEER_VERSION
    );
  }

  // Remove Gutenberg default styles
  if ( ! get_option( 'fictioneer_enable_all_block_styles' ) ) {
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    wp_dequeue_style( 'wc-blocks-style' );
  }
}
add_action( 'wp_enqueue_scripts', 'fictioneer_style_queue' );

/**
 * Enqueues customizer stylesheets
 *
 * @since 5.0
 */

function fictioneer_customizer_queue() {
  fictioneer_add_customized_layout_css();
  fictioneer_add_customized_light_mode_css();
  fictioneer_add_customized_dark_mode_css();
}
add_action( 'wp_enqueue_scripts', 'fictioneer_customizer_queue', 999 ); // Make sure this is added last!

// =============================================================================
// ENQUEUE OVERRIDE STYLESHEETS
// =============================================================================

/**
 * Enqueues an "override" stylesheet that supersedes all others
 *
 * @since 2.0
 */

function fictioneer_style_footer_queue() {
  wp_enqueue_style( 'override', get_template_directory_uri() . '/css/override.css' );
}
add_action( 'get_footer', 'fictioneer_style_footer_queue' );

// =============================================================================
// FONT AWESOME 6+
// =============================================================================

if ( ! function_exists( 'fa_custom_setup_cdn_webfont' ) ) {
  /**
   * Add Font Awesome 6+ to the site
   *
   * @since 4.5
   * @link https://fontawesome.com/docs/web/use-with/wordpress/install-manually
   * @link https://fontawesome.com/account/cdn
   */

  function fa_custom_setup_cdn_webfont( $cdn_url = '', $integrity = null ) {
    $matches = [];
    $match_result = preg_match( '|/([^/]+?)\.css$|', $cdn_url, $matches );
    $resource_handle_uniqueness = ( $match_result === 1 ) ? $matches[1] : md5( $cdn_url );
    $resource_handle = "font-awesome-cdn-webfont-$resource_handle_uniqueness";

    foreach ( ['wp_enqueue_scripts', 'admin_enqueue_scripts', 'login_enqueue_scripts'] as $action ) {
      add_action(
        $action,
        function () use ( $cdn_url, $resource_handle ) {
          wp_enqueue_style( $resource_handle, $cdn_url, [], null );
        }
      );
    }

    if ( $integrity ) {
      add_filter(
        'style_loader_tag',
        function( $html, $handle ) use ( $resource_handle, $integrity ) {
          if ( in_array( $handle, [$resource_handle], true ) ) {
            return preg_replace(
              '/\/>$/',
              'integrity="' . $integrity .
              '" crossorigin="anonymous" />',
              $html,
              1
            );
          } else {
            return $html;
          }
        },
        10,
        2
      );
    }
  }

  fa_custom_setup_cdn_webfont(
    'https://use.fontawesome.com/releases/v6.1.2/css/all.css',
    'sha384-fZCoUih8XsaUZnNDOiLqnby1tMJ0sE7oBbNk2Xxf5x8Z4SvNQ9j83vFMa/erbVrV'
  );
}

// =============================================================================
// ENQUEUE SCRIPTS
// =============================================================================

/**
 * Enqueue scripts
 *
 * @since 1.0
 * @since 4.7 Split scripts and made enqueuing depending on options.
 */

function fictioneer_add_custom_scripts() {
  // Setup
  $post_type = get_post_type();

  // Utility
  wp_register_script( 'fictioneer-utility-scripts', get_template_directory_uri() . '/js/utility.min.js', [], FICTIONEER_VERSION, true );

  // Application
  wp_register_script( 'fictioneer-application-scripts', get_template_directory_uri() . '/js/application.min.js', [ 'fictioneer-utility-scripts', 'wp-i18n'], FICTIONEER_VERSION, true );

  // Lightbox
  if ( get_option( 'fictioneer_enable_lightbox' ) ) {
    wp_enqueue_script( 'fictioneer-lightbox', get_template_directory_uri() . '/js/lightbox.min.js', ['fictioneer-application-scripts'], FICTIONEER_VERSION, true );
  }

  // Mobile menu
  wp_register_script( 'fictioneer-mobile-menu-scripts', get_template_directory_uri() . '/js/mobile-menu.min.js', [ 'fictioneer-application-scripts'], FICTIONEER_VERSION, true );

  // Consent
  wp_register_script( 'fictioneer-consent-scripts', get_template_directory_uri() . '/js/consent.min.js', [ 'fictioneer-application-scripts'], FICTIONEER_VERSION, true );

  // Chapter
  wp_register_script( 'fictioneer-chapter-scripts', get_template_directory_uri() . '/js/chapter.min.js', [ 'fictioneer-application-scripts'], FICTIONEER_VERSION, true );

  // Suggestions
  wp_register_script( 'fictioneer-suggestion-scripts', get_template_directory_uri() . '/js/suggestion.min.js', [ 'fictioneer-chapter-scripts'], FICTIONEER_VERSION, true );

  // Text-To-Speech
  wp_register_script( 'fictioneer-tts-scripts', get_template_directory_uri() . '/js/tts.min.js', [ 'fictioneer-chapter-scripts'], FICTIONEER_VERSION, true );

  // Story
  wp_register_script( 'fictioneer-story-scripts', get_template_directory_uri() . '/js/story.min.js', [ 'fictioneer-application-scripts'], FICTIONEER_VERSION, true );

  // User
  wp_register_script( 'fictioneer-user-scripts', get_template_directory_uri() . '/js/user.min.js', [ 'fictioneer-application-scripts'], FICTIONEER_VERSION, true );

  // User Profile
  wp_register_script( 'fictioneer-user-profile-scripts', get_template_directory_uri() . '/js/user-profile.min.js', [ 'fictioneer-application-scripts', 'fictioneer-user-scripts'], FICTIONEER_VERSION, true );

  // Bookmarks
  wp_register_script( 'fictioneer-bookmarks-scripts', get_template_directory_uri() . '/js/bookmarks.min.js', [ 'fictioneer-application-scripts', 'fictioneer-user-scripts'], FICTIONEER_VERSION, true );

  // Follows
  wp_register_script( 'fictioneer-follows-scripts', get_template_directory_uri() . '/js/follows.min.js', [ 'fictioneer-application-scripts', 'fictioneer-user-scripts'], FICTIONEER_VERSION, true );

  // Checkmarks
  wp_register_script( 'fictioneer-checkmarks-scripts', get_template_directory_uri() . '/js/checkmarks.min.js', [ 'fictioneer-application-scripts', 'fictioneer-user-scripts'], FICTIONEER_VERSION, true );

  // Reminders
  wp_register_script( 'fictioneer-reminders-scripts', get_template_directory_uri() . '/js/reminders.min.js', [ 'fictioneer-application-scripts', 'fictioneer-user-scripts'], FICTIONEER_VERSION, true );

  // Comments
  wp_register_script( 'fictioneer-comments-scripts', get_template_directory_uri() . '/js/comments.min.js', [ 'fictioneer-application-scripts'], FICTIONEER_VERSION, true );

  // AJAX Comments
  wp_register_script( 'fictioneer-ajax-comments-scripts', get_template_directory_uri() . '/js/ajax-comments.min.js', [ 'fictioneer-comments-scripts'], FICTIONEER_VERSION, true );

  // AJAX Bookshelf
  wp_register_script( 'fictioneer-ajax-bookshelf-scripts', get_template_directory_uri() . '/js/ajax-bookshelf.min.js', [ 'fictioneer-application-scripts'], FICTIONEER_VERSION, true );

  // Enqueue utility
  wp_enqueue_script( 'fictioneer-utility-scripts' );

  // Enqueue application
  wp_enqueue_script( 'fictioneer-application-scripts' );

  // Localize application
  wp_localize_script( 'fictioneer-application-scripts', 'fictioneer_ajax', array(
    'ajax_url' => admin_url( 'admin-ajax.php' ),
    'rest_url' => get_rest_url( null, 'fictioneer/v1/' ),
    'ttl' => FICTIONEER_AJAX_TTL,
    'login_ttl' => FICTIONEER_AJAX_LOGIN_TTL,
    'post_debounce_rate' => FICTIONEER_AJAX_POST_DEBOUNCE_RATE
  ));
  wp_localize_script( 'fictioneer-application-scripts', 'fictioneer_fonts', fictioneer_get_fonts() );
  wp_localize_script( 'fictioneer-application-scripts', 'fictioneer_font_colors', fictioneer_get_font_colors() );

  // Append JS snippets
  $removable_query_args = ['success', 'failure', 'fictioneer_nonce', 'fictioneer-notice'];
  $removable_query_args = apply_filters( 'fictioneer_filter_removable_query_args', $removable_query_args );

  $extra_scripts = "
    function fcn_removeQueryArgs() {
      history.replaceState && history.replaceState(null, '', location.pathname + location.search.replace(/[?&](" . implode( '|', $removable_query_args ) . ")=[^&]+/g, '').replace(/^[?&]/, '?') + location.hash);
    }
  ";

  // Localize the script
  wp_add_inline_script( 'fictioneer-application-scripts', $extra_scripts );

  // Enqueue mobile menu
  wp_enqueue_script( 'fictioneer-mobile-menu-scripts' );

  // Enqueue consent
  if ( get_option( 'fictioneer_cookie_banner' ) ) {
    wp_enqueue_script( 'fictioneer-consent-scripts' );
  }

  // Enqueue chapter
  if ( $post_type == 'fcn_chapter' && ! is_archive() && ! is_search() ) {
    wp_enqueue_script( 'fictioneer-chapter-scripts' );
  }

  // Enqueue Diff-Match-Patch + suggestions
  if (
    $post_type == 'fcn_chapter' &&
    get_option( 'fictioneer_enable_suggestions' ) &&
    ! is_archive() &&
    ! is_search() &&
    ! post_password_required() &&
    ! fictioneer_get_field( 'fictioneer_disable_commenting' ) &&
    comments_open()
  ) {
    wp_enqueue_script( 'dmp', get_template_directory_uri() . '/js/diff-match-patch.js', array(), null, true );
    wp_enqueue_script( 'fictioneer-suggestion-scripts' );
  }

  // Enqueue TTS
  if (
    $post_type == 'fcn_chapter' &&
    get_option( 'fictioneer_enable_tts' ) &&
    ! is_archive() &&
    ! is_search() &&
    ! post_password_required()
  ) {
    wp_enqueue_script( 'fictioneer-tts-scripts' );
  }

  // Enqueue story
  if ( $post_type == 'fcn_story' && ! is_archive() && ! is_search() ) {
    wp_enqueue_script( 'fictioneer-story-scripts' );
  }

  // Enqueue users + user profile + follows + checkmarks + reminders
  if ( is_user_logged_in() || get_option( 'fictioneer_enable_ajax_authentication' ) ) {
    wp_enqueue_script( 'fictioneer-user-scripts' );

    if ( get_option( 'fictioneer_enable_checkmarks' ) ) {
      wp_enqueue_script( 'fictioneer-checkmarks-scripts' );
    }

    if ( get_option( 'fictioneer_enable_reminders' ) ) {
      wp_enqueue_script( 'fictioneer-reminders-scripts' );
    }

    if ( get_option( 'fictioneer_enable_follows' ) ) {
      wp_enqueue_script( 'fictioneer-follows-scripts' );
    }

    if ( is_page_template( 'user-profile.php' ) ) {
      wp_enqueue_script( 'fictioneer-user-profile-scripts' );
    }
  }

  // Enqueue bookmarks
  if ( get_option( 'fictioneer_enable_bookmarks' ) ) {
    wp_enqueue_script( 'fictioneer-bookmarks-scripts' );
  }

  // Enqueue WordPress comment-reply
  if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
    wp_enqueue_script( 'comment-reply' );
  }

  // Enqueue comments
  if ( is_singular() && comments_open() ) {
    wp_enqueue_script( 'fictioneer-comments-scripts' );

    if ( get_option( 'fictioneer_enable_ajax_comments' ) ) {
      wp_enqueue_script( 'fictioneer-ajax-comments-scripts' );
    }
  }

  // Enqueue bookshelf
  if ( is_page_template( 'singular-bookshelf-ajax.php' ) ) {
    wp_enqueue_script( 'fictioneer-ajax-bookshelf-scripts' );
  }

  // DEV Utilities
  if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
    wp_register_script( 'fictioneer-dev-scripts', get_template_directory_uri() . '/js/dev-tools.min.js', [ 'fictioneer-application-scripts'], FICTIONEER_VERSION, true );
    wp_enqueue_script( 'fictioneer-dev-scripts' );
  }
}
add_action( 'wp_enqueue_scripts', 'fictioneer_add_custom_scripts' );

/**
 * Enqueue block editor scripts
 *
 * @since 5.6.0
 */

function fictioneer_enqueue_block_editor_scripts() {
  $current_user = wp_get_current_user();

  wp_register_script(
    'fictioneer-block-editor-scripts',
    get_template_directory_uri() . '/js/block-editor.min.js',
    ['wp-dom-ready', 'wp-edit-post', 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-data', 'jquery'],
    FICTIONEER_VERSION,
    true
  );

  wp_enqueue_script( 'fictioneer-block-editor-scripts' );

  wp_localize_script( 'fictioneer-block-editor-scripts', 'fictioneerData', array(
    'userCapabilities' => $current_user->allcaps
  ));
}
add_action( 'enqueue_block_editor_assets', 'fictioneer_enqueue_block_editor_scripts' );

// =============================================================================
// LOAD TRANSLATIONS FOR JAVASCRIPT
// =============================================================================

function fictioneer_load_script_translations() {
  wp_set_script_translations(
    'fictioneer-application-scripts',
    'fictioneer',
    get_template_directory() . '/languages'
  );
}
add_action( 'wp_enqueue_scripts', 'fictioneer_load_script_translations', 99 );

// =============================================================================
// DISABLE JQUERY MIGRATE
// =============================================================================

/**
 * Removed the jQuery migrate script
 *
 * @since 5.0
 *
 * @param object $scripts The loaded scripts.
 */

function fictioneer_remove_jquery_migrate( $scripts ) {
  // Abort if...
  if ( ! isset( $scripts->registered['jquery'] ) ) {
    return;
  }

  // Setup
  $script = $scripts->registered['jquery'];

  // Remove migrate script
  if ( $script->deps ) {
    $script->deps = array_diff( $script->deps, array( 'jquery-migrate' ) );
  }
}

if ( ! is_admin() && ! get_option( 'fictioneer_enable_jquery_migrate' ) ) {
  add_action( 'wp_default_scripts', 'fictioneer_remove_jquery_migrate' );
}

// =============================================================================
// AUTOPTIMIZE CONFIGURATION
// =============================================================================

/**
 * Change replace tag for Autoptimize (if installed)
 *
 * @since 4.0
 * @link https://github.com/wp-plugins/autoptimize
 *
 * @param string $replacetag The original replace tag.
 */

function fictioneer_replace_ao_insert_position( $replacetag ) {
  return array( '<injectjs />', 'replace' );
}
add_filter( 'autoptimize_filter_js_replacetag', 'fictioneer_replace_ao_insert_position', 10, 1 );

/**
 * Exclude scripts from Autoptimize (if installed)
 *
 * @since 4.0
 * @link https://github.com/wp-plugins/autoptimize
 *
 * @param string $exclude List of default excludes.
 */

function fictioneer_ao_exclude_scripts( $exclude ) {
	return $exclude . '';
}
add_filter( 'autoptimize_filter_js_exclude', 'fictioneer_ao_exclude_scripts', 10, 1 );

/**
 * Exclude stylesheets from Autoptimize (if installed)
 *
 * @since 4.0
 * @link https://github.com/wp-plugins/autoptimize
 *
 * @param string $exclude List of default excludes.
 */

function fictioneer_ao_exclude_css( $exclude ) {
	return $exclude . ', fonts.css';
}
add_filter( 'autoptimize_filter_css_exclude', 'fictioneer_ao_exclude_css', 10, 1 );

// =============================================================================
// OUTPUT HEAD FONTS
// =============================================================================

if ( ! function_exists( 'fictioneer_output_head_fonts' ) ) {
  /**
   * Output fonts in <head>
   *
   * Critical fonts that need to be loaded as fast as possible and are
   * therefore inlined in the <head>.
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_output_head_fonts() {
    // Setup
    $uri = get_template_directory_uri() . '/fonts/';

    // Start HTML ---> ?>
    <style id="inline-fonts" type="text/css">@font-face{font-family:'Open Sans';font-style:normal;font-weight:325;font-stretch:100%;font-display:swap;src:url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-latin-extended-325-normal.woff2) format('woff2'),local("OpenSans-Light");unicode-range:U+0100-024F,U+0259,U+1E00-1EFF,U+2020,U+20A0-20AB,U+20AD-20CF,U+2113,U+2C60-2C7F,U+A720-A7FF}@font-face{font-family:'Open Sans';font-style:normal;font-weight:325;font-stretch:100%;font-display:swap;src:url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-latin-325-normal.woff2) format('woff2'),local("OpenSans-Light");unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+2000-206F,U+2074,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD}@font-face{font-family:"Open Sans";font-style:normal;font-weight:400;font-display:swap;src:local("OpenSans-Regular"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-regular.woff2) format("woff2"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-regular.woff) format("woff")}@font-face{font-family:"Open Sans";font-style:normal;font-weight:500;font-display:swap;src:local("OpenSans-Medium"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-500.woff2) format("woff2"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-500.woff) format("woff")}@font-face{font-family:"Open Sans";font-style:normal;font-weight:600;font-display:swap;src:local("OpenSans-SemiBold"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-600.woff2) format("woff2"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-600.woff) format("woff")}@font-face{font-family:"Open Sans";font-style:normal;font-weight:700;font-display:swap;src:local("OpenSans-Bold"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-700.woff2) format("woff2"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-700.woff) format("woff")}@font-face{font-family:'Open Sans';font-style:italic;font-weight:325;font-stretch:100%;font-display:swap;src:url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-latin-extended-325-italic.woff2) format('woff2'),local("OpenSans-LightItalic");unicode-range:U+0100-024F,U+0259,U+1E00-1EFF,U+2020,U+20A0-20AB,U+20AD-20CF,U+2113,U+2C60-2C7F,U+A720-A7FF}@font-face{font-family:'Open Sans';font-style:italic;font-weight:325;font-stretch:100%;font-display:swap;src:url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-latin-325-italic.woff2) format('woff2'),local("OpenSans-LightItalic");unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+2000-206F,U+2074,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD}@font-face{font-family:"Open Sans";font-style:italic;font-weight:400;font-display:swap;src:local("OpenSans-Italic"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-italic.woff2) format("woff2"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-italic.woff) format("woff")}@font-face{font-family:"Open Sans";font-style:italic;font-weight:500;font-display:swap;src:local("OpenSans-MediumItalic"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-500italic.woff2) format("woff2"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-500italic.woff) format("woff")}@font-face{font-family:"Open Sans";font-style:italic;font-weight:600;font-display:swap;src:local("OpenSans-SemiBoldItalic"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-600italic.woff2) format("woff2"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-600italic.woff) format("woff")}@font-face{font-family:"Open Sans";font-style:italic;font-weight:700;font-display:swap;src:local("OpenSans-BoldItalic"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-700italic.woff2) format("woff2"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-700italic.woff) format("woff")}</style>
    <?php // <--- End HTML
  }
}

// =============================================================================
// OUTPUT HEAD META
// =============================================================================

if ( ! function_exists( 'fictioneer_output_head_meta' ) ) {
  /**
   * Output HTML <head> meta
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_output_head_meta() {
    // Setup
    $font_link = get_template_directory_uri() . '/css/fonts.css?ver=' . FICTIONEER_VERSION;

    // Start HTML ---> ?>
    <meta charset="<?php echo FICTIONEER_SITE_CHARSET; ?>">
    <meta http-equiv="content-type" content="text/html">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <meta name="format-detection" content="telephone=no">
    <meta name="theme-color" content="<?php echo '#' . get_background_color(); ?>">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <?php fictioneer_output_head_fonts(); ?>
    <link rel="stylesheet" href="<?php echo $font_link; ?>" media="print" onload="this.media='all';">
    <noscript><link rel="stylesheet" href="<?php echo $font_link; ?>"></noscript>
    <?php // <--- End HTML
  }
}

// =============================================================================
// MODIFY ROBOTS META
// =============================================================================

/**
 * Adds noindex to robots meta.
 *
 * This is called in the header.php template.
 *
 * @since Fictioneer 5.0
 */

function fictioneer_add_noindex_to_robots( $robots ) {
  $robots['noindex'] = true;
  return $robots;
}

// =============================================================================
// OUTPUT HEAD CRITICAL SCRIPTS
// =============================================================================

if ( ! function_exists( 'fictioneer_output_head_critical_scripts' ) ) {
  /**
   * Output critical path scripts in <head>
   *
   * Critical path scripts executed in the <head> before the rest of the DOM
   * is loaded. This is necessary for the light/dark switch and site settings
   * to work without causing color flickering or layout shifts. This is achieved
   * by adding configuration classes directly into the <html> root node, which
   * is the only one available at this point.
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_output_head_critical_scripts() {
    // Start HTML ---> ?>
    <script>let _t,_m,_s=localStorage.getItem("fcnSiteSettings"),_r=document.documentElement;if(_s&&(_s=JSON.parse(_s))&&null!==_s&&"object"==typeof _s){Object.entries(_s).forEach((t=>{switch(t[0]){case"minimal":_r.classList.toggle("minimal",t[1]);break;case"darken":_t=_s.darken,_m=_t>=0?1+Math.pow(_t,2):1-Math.pow(_t,2),_r.style.setProperty("--darken",_m);break;case"saturation":_t=_s.saturation,_m=_t>=0?1+Math.pow(_t,2):1-Math.pow(_t,2),_r.style.setProperty("--saturation",_m);break;case"font-saturation":_t=_s["font-saturation"],_m=_t>=0?1+Math.pow(_t,2):1-Math.pow(_t,2),_r.style.setProperty("--font-saturation",_m);break;case"hue-rotate":_m=Number.isInteger(_s["hue-rotate"])?_s["hue-rotate"]:0,_r.style.setProperty("--hue-rotate",`${_m}deg`);break;default:_r.classList.toggle(`no-${t[0]}`,!t[1])}})),_r.dataset.fontWeight=_s.hasOwnProperty("font-weight")?_s["font-weight"]:"default",_r.dataset.theme=_s.hasOwnProperty("site-theme")&&!_r.dataset.forceChildTheme?_s["site-theme"]:"default";let t=getComputedStyle(document.documentElement).getPropertyValue("--theme-color-base").trim().split(" "),e=_s.darken?_s.darken:0,a=_s.saturation?_s.saturation:0,s=_s["hue-rotate"]?_s["hue-rotate"]:0,r=e>=0?1+Math.pow(e,2):1-Math.pow(e,2);_s=a>=0?1+Math.pow(a,2):1-Math.pow(a,2),t=`hsl(${(parseInt(t[0])+s)%360}deg ${(parseInt(t[1])*_s).toFixed(2)}% ${(parseInt(t[2])*r).toFixed(2)}%)`,document.querySelector("meta[name=theme-color]").setAttribute("content",t)}localStorage.getItem("fcnLightmode")&&(_r.dataset.mode="true"==localStorage.getItem("fcnLightmode")?"light":"");</script>
    <?php // <--- End HTML
  }
}

// =============================================================================
// ADD EXCERPTS TO PAGES
// =============================================================================

add_post_type_support( 'page', 'excerpt' );

// =============================================================================
// PAGINATION
// =============================================================================

/**
 * Modifies the pagination links output for the theme
 *
 * @since Fictioneer 5.4.0
 * @link https://developer.wordpress.org/reference/functions/paginate_links/
 *
 * @param array $args Optional. Array of arguments for generating the pagination links.
 *
 * @return string Modified pagination links HTML.
 */

function fictioneer_paginate_links( $args = [] ) {
  $pagination = paginate_links( $args );

  return str_replace(
    'class="page-numbers dots"',
    'class="page-numbers dots" tabindex="0"',
    $pagination
  );
}

?>
