<?php

// =============================================================================
// THEME SETUP
// =============================================================================

/**
 * Sets up theme defaults and registers support for various WordPress features.
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
  add_theme_support( 'custom-background', ['wp-head-callback' => 'fictioneer_custom_background'] );

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
  add_editor_style( get_template_directory_uri() . '/css/editor.css' );

  // Add support for custom logo
  add_theme_support(
    'custom-logo',
    array(
      'flex-width' => true,
      'width' => (int) get_theme_mod( 'header_image_height_max', 210 ) * 1.5,
      'flex-height' => true,
      'height' => get_theme_mod( 'header_image_height_max', 210 ),
      'header-text' => array( 'site-title', 'site-description' ),
    )
  );

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
  if ( ! in_array( $position_x, ['left', 'center', 'right'], true ) ) $position_x = 'left';
  if ( ! in_array( $position_y, ['top', 'center', 'bottom'], true ) ) $position_y = 'top';
  $position = "background-position: $position_x $position_y;";

  // Background size
  $size = get_theme_mod( 'background_size' );
  if ( ! in_array( $size, ['auto', 'contain', 'cover'], true ) ) $size = 'auto';
  $size = "background-size: $size;";

  // Background repeat
  $repeat = get_theme_mod( 'background_repeat' );
  if ( ! in_array( $repeat, ['repeat-x', 'repeat-y', 'repeat', 'no-repeat'], true ) ) $repeat = 'repeat';
  $repeat = "background-repeat: $repeat;";

  // Background scroll
  $attachment = get_theme_mod( 'background_attachment' );
  if ( 'fixed' !== $attachment ) $attachment = 'scroll';
  $attachment = "background-attachment: $attachment;";

  // Build
  $style = $image . $position . $size . $repeat . $attachment;

  // Output
  echo '<style id="custom-background-css">.site.background-texture {' . trim( $style ) . '}</style>';
}

// =============================================================================
// MODIFY ALLOWED TAGS
// =============================================================================

/**
 * Update list of allowed tags
 *
 * @since 4.7
 *
 * @global array $allowedtags Array of allowed tags.
 */

function fictioneer_modify_allowed_tags() {
  global $allowedtags;

  $allowedtags['ins'] = array(
    'datetime' => []
  );

  $allowedtags['p'] = array();
  $allowedtags['span'] = array();
  $allowedtags['br'] = array();
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

  // Data
  $site_width = get_theme_mod( 'site_width', 960 );
  $header_inset_default = get_theme_mod( 'inset_header_image', false ) ? 1 : 0;
  $mode_default = get_option( 'fictioneer_light_mode_as_default', false ) ? 'light' : 'dark';
  $ajax_submit = get_option( 'fictioneer_enable_ajax_comment_submit', false );
  $comment_edit_threshold = get_option( 'fictioneer_user_comment_edit_time', 15 );

  // Add configuration classes
  if ( $header_inset_default ) $header_classes[] = 'inset-header-image';

  // Prepare
  $output['class'] = implode( ' ', $header_classes );
  $output['data-mode-default'] = $mode_default;
  $output['data-site-width-default'] = $site_width;
  $output['data-theme'] = 'default';
  $output['data-mode'] = '';
  $output['data-font-weight'] = 'default';
  $output['data-primary-font'] = FICTIONEER_PRIMARY_FONT;
  if ( $ajax_submit ) $output['data-ajax-submit'] = 'true';
  if ( ! FICTIONEER_THEME_SWITCH ) $output['data-force-child-theme'] = '1';
  if ( get_option( 'fictioneer_enable_ajax_nonce', false ) ) $output['data-ajax-nonce'] = '1';
  if ( get_option( 'fictioneer_enable_public_cache_compatibility', false ) ) $output['data-public-caching'] = '1';
  if ( get_option( 'fictioneer_enable_ajax_authentication', false ) ) $output['data-ajax-auth'] = '1';
  if ( get_option( 'fictioneer_enable_user_comment_editing', false ) ) $output['data-edit-time'] = $comment_edit_threshold;

  // Filter output
  $output = apply_filters( 'fictioneer_filter_root_attributes', $output );

  // Output
  foreach ( $output as $key => $value ) {
    echo $key . '="' . $value . '" ';
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
  // Cache busting
  $version = FICTIONEER_VERSION;

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
        $version
      );
      $application_dependencies[] = 'fictioneer-properties';
    }

    // Application
    wp_enqueue_style(
      'fictioneer-application',
      get_template_directory_uri() . '/css/application.css',
      $application_dependencies,
      $version
    );

    // If NOT an archive or search page...
    if ( ! is_search() && ! is_archive() ) {
      // Collections
      if ( is_page_template( 'stories.php' ) || $post_type == 'fcn_collection' ) {
        wp_enqueue_style(
          'fictioneer-collections',
          get_template_directory_uri() . '/css/collections.css',
          ['fictioneer-application'],
          $version
        );
      }

      // Chapter
      if ( $post_type == 'fcn_chapter' ) {
        wp_enqueue_style(
          'fictioneer-chapter',
          get_template_directory_uri() . '/css/chapter.css',
          ['fictioneer-application'],
          $version
        );
      }

      // Story
      if ( $post_type == 'fcn_story' ) {
        wp_enqueue_style(
          'fictioneer-story',
          get_template_directory_uri() . '/css/story.css',
          ['fictioneer-application'],
          $version
        );
      }

      // Recommendation
      if ( $post_type == 'fcn_recommendation' ) {
        wp_enqueue_style(
          'fictioneer-recommendation',
          get_template_directory_uri() . '/css/recommendation.css',
          ['fictioneer-application'],
          $version
        );
      }

      // Comments
      if ( $post_type == 'fcn_story' || comments_open() ) {
        wp_enqueue_style(
          'fictioneer-comments',
          get_template_directory_uri() . '/css/comments.css',
          ['fictioneer-application'],
          $version
        );
      }
    }

    // Archive
    if ( is_archive() ) {
      wp_enqueue_style(
        'fictioneer-archive',
        get_template_directory_uri() . '/css/taxonomies.css',
        ['fictioneer-application'],
        $version
      );
    }
  } else {
    // Complete
    wp_enqueue_style(
      'fictioneer-complete',
      get_template_directory_uri() . '/css/complete.css',
      [],
      $version
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
// ENQUEUE ADMIN STYLESHEETS
// =============================================================================

/**
 * Enqueues stylesheet for the admin panel
 *
 * @since 3.0
 */

function fictioneer_admin_styles() {
  $screen = get_current_screen();

  wp_register_style( 'fictioneer-admin-panel', get_template_directory_uri() . '/css/admin.css' );

  if ( is_admin() ) {
    wp_enqueue_style( 'fictioneer-admin-panel' );
  }
}
add_action( 'admin_enqueue_scripts', 'fictioneer_admin_styles' );

// =============================================================================
// LIMIT DEFAULT BLOCKS
// =============================================================================

/**
 * Limit the available default blocks
 *
 * Fictioneer is has a particular and delicate content section, built around and
 * for the main purpose of displaying prose. Other features, such as the ePUB
 * converter heavily depend on _expected_ input or may break. There are certainly
 * more possible but the initial selection has been chosen carefully.
 *
 * @since 4.0
 */

if ( ! function_exists( 'fictioneer_allowed_block_types' ) ) {
  function fictioneer_allowed_block_types() {
    return array(
      'core/image',
      'core/paragraph',
      'core/heading',
      'core/list',
      'core/list-item',
      'core/gallery',
      'core/quote',
      'core/pullquote',
      'core/buttons',
      'core/button',
      'core/audio',
      'core/file',
      'core/video',
      'core/table',
      'core/code',
      'core/preformatted',
      'core/html',
      'core/separator',
      'core/spacer',
      'core/shortcode',
      'core/more',
      'core/embed',
      'core-embed/twitter',
      'core-embed/youtube',
      'core-embed/soundcloud',
      'core-embed/spotify',
      'core-embed/vimeo'
    );
  }
}

if ( ! get_option( 'fictioneer_enable_all_blocks' ) ) {
  add_filter( 'allowed_block_types_all', 'fictioneer_allowed_block_types' );
}

// =============================================================================
// FONT AWESOME 6+
// =============================================================================

/**
 * Add Font Awesome 6+ to the site
 *
 * @since 4.5
 * @link https://fontawesome.com/docs/web/use-with/wordpress/install-manually
 * @link https://fontawesome.com/account/cdn
 */

if ( ! function_exists( 'fa_custom_setup_cdn_webfont' ) ) {
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
  $version = FICTIONEER_VERSION; // Cache busting

  // Utility
  wp_register_script( 'fictioneer-utility-scripts', get_template_directory_uri() . '/js/utility.min.js', [], $version, true );

  // Application
  wp_register_script( 'fictioneer-application-scripts', get_template_directory_uri() . '/js/application.min.js', [ 'fictioneer-utility-scripts', 'wp-i18n'], $version, true );

  // Lightbox
  if ( get_option( 'fictioneer_enable_lightbox' ) ) {
    wp_enqueue_script( 'fictioneer-lightbox', get_template_directory_uri() . '/js/lightbox.min.js', ['fictioneer-application-scripts'], $version, true );
  }

  // Mobile menu
  wp_register_script( 'fictioneer-mobile-menu-scripts', get_template_directory_uri() . '/js/mobile-menu.min.js', [ 'fictioneer-application-scripts'], $version, true );

  // Bookmarks
  wp_register_script( 'fictioneer-bookmarks-scripts', get_template_directory_uri() . '/js/bookmarks.min.js', [ 'fictioneer-application-scripts'], $version, true );

  // Consent
  wp_register_script( 'fictioneer-consent-scripts', get_template_directory_uri() . '/js/consent.min.js', [ 'fictioneer-application-scripts'], $version, true );

  // Chapter
  wp_register_script( 'fictioneer-chapter-scripts', get_template_directory_uri() . '/js/chapter.min.js', [ 'fictioneer-application-scripts'], $version, true );

  // Suggestions
  wp_register_script( 'fictioneer-suggestion-scripts', get_template_directory_uri() . '/js/suggestion.min.js', [ 'fictioneer-chapter-scripts'], $version, true );

  // Text-To-Speech
  wp_register_script( 'fictioneer-tts-scripts', get_template_directory_uri() . '/js/tts.min.js', [ 'fictioneer-chapter-scripts'], $version, true );

  // Story
  wp_register_script( 'fictioneer-story-scripts', get_template_directory_uri() . '/js/story.min.js', [ 'fictioneer-application-scripts'], $version, true );

  // User
  wp_register_script( 'fictioneer-user-scripts', get_template_directory_uri() . '/js/user.min.js', [ 'fictioneer-application-scripts'], $version, true );

  // User Profile
  wp_register_script( 'fictioneer-user-profile-scripts', get_template_directory_uri() . '/js/user-profile.min.js', [ 'fictioneer-application-scripts'], $version, true );

  // Follows
  wp_register_script( 'fictioneer-follows-scripts', get_template_directory_uri() . '/js/follows.min.js', [ 'fictioneer-user-scripts'], $version, true );

  // Checkmarks
  wp_register_script( 'fictioneer-checkmarks-scripts', get_template_directory_uri() . '/js/checkmarks.min.js', [ 'fictioneer-application-scripts'], $version, true );

  // Reminders
  wp_register_script( 'fictioneer-reminders-scripts', get_template_directory_uri() . '/js/reminders.min.js', [ 'fictioneer-user-scripts'], $version, true );

  // Comments
  wp_register_script( 'fictioneer-comments-scripts', get_template_directory_uri() . '/js/comments.min.js', [ 'fictioneer-application-scripts'], $version, true );

  // AJAX Comments
  wp_register_script( 'fictioneer-ajax-comments-scripts', get_template_directory_uri() . '/js/ajax-comments.min.js', [ 'fictioneer-comments-scripts'], $version, true );

  // AJAX Bookshelf
  wp_register_script( 'fictioneer-ajax-bookshelf-scripts', get_template_directory_uri() . '/js/ajax-bookshelf.min.js', [ 'fictioneer-application-scripts'], $version, true );

  // Enqueue utility
  wp_enqueue_script( 'fictioneer-utility-scripts' );

  // Enqueue application
  wp_enqueue_script( 'fictioneer-application-scripts' );
  wp_localize_script( 'fictioneer-application-scripts', 'fictioneer_ajax', array(
    'ajax_url' => admin_url( 'admin-ajax.php' ),
    'ttl' => FICTIONEER_AJAX_TTL,
    'login_ttl' => FICTIONEER_AJAX_LOGIN_TTL,
    'post_debounce_rate' => FICTIONEER_AJAX_POST_DEBOUNCE_RATE
  ));

  // Enqueue mobile menu
  wp_enqueue_script( 'fictioneer-mobile-menu-scripts' );

  // Enqueue bookmarks
  if ( get_option( 'fictioneer_enable_bookmarks' ) ) {
    wp_enqueue_script( 'fictioneer-bookmarks-scripts' );
  }

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
  if ( is_user_logged_in() || ! empty( get_option( 'fictioneer_enable_ajax_authentication', false ) ) ) {
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
}
add_action( 'wp_enqueue_scripts', 'fictioneer_add_custom_scripts' );

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
  if ( ! isset( $scripts->registered['jquery'] ) ) return;

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
// ENQUEUE ADMIN SCRIPTS
// =============================================================================

/**
 * Enqueue scripts for admin panel
 *
 * @since 4.0
 *
 * @param string $hook_suffix The current admin page.
 */

function fictioneer_admin_scripts( $hook_suffix ) {
  wp_enqueue_script(
    'fictioneer-utility-scripts',
    get_template_directory_uri() . '/js/utility.min.js',
    ['jquery'],
    false,
    true
  );

  wp_enqueue_script(
    'fictioneer-admin-script',
    get_template_directory_uri() . '/js/admin.min.js',
    ['jquery', 'fictioneer-utility-scripts'],
    false,
    true
  );

  wp_localize_script(
    'fictioneer-admin-script',
    'fictioneer_ajax',
    array(
      'ajax_url' => admin_url( 'admin-ajax.php' ),
      'fictioneer_nonce' => wp_create_nonce( 'fictioneer_nonce' )
    )
  );
}
add_action( 'admin_enqueue_scripts', 'fictioneer_admin_scripts' );

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
    <style id="inline-fonts">@font-face{font-family:'Open Sans';font-style:normal;font-weight:325;font-stretch:100%;font-display:swap;src:url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-latin-extended-325-normal.woff2) format('woff2'),local("OpenSans-Light");unicode-range:U+0100-024F,U+0259,U+1E00-1EFF,U+2020,U+20A0-20AB,U+20AD-20CF,U+2113,U+2C60-2C7F,U+A720-A7FF}@font-face{font-family:'Open Sans';font-style:normal;font-weight:325;font-stretch:100%;font-display:swap;src:url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-latin-325-normal.woff2) format('woff2'),local("OpenSans-Light");unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+2000-206F,U+2074,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD}@font-face{font-family:"Open Sans";font-style:normal;font-weight:400;font-display:swap;src:local("OpenSans-Regular"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-regular.woff2) format("woff2"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-regular.woff) format("woff")}@font-face{font-family:"Open Sans";font-style:normal;font-weight:500;font-display:swap;src:local("OpenSans-Medium"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-500.woff2) format("woff2"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-500.woff) format("woff")}@font-face{font-family:"Open Sans";font-style:normal;font-weight:600;font-display:swap;src:local("OpenSans-SemiBold"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-600.woff2) format("woff2"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-600.woff) format("woff")}@font-face{font-family:"Open Sans";font-style:normal;font-weight:700;font-display:swap;src:local("OpenSans-Bold"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-700.woff2) format("woff2"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-700.woff) format("woff")}@font-face{font-family:'Open Sans';font-style:italic;font-weight:325;font-stretch:100%;font-display:swap;src:url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-latin-extended-325-italic.woff2) format('woff2'),local("OpenSans-LightItalic");unicode-range:U+0100-024F,U+0259,U+1E00-1EFF,U+2020,U+20A0-20AB,U+20AD-20CF,U+2113,U+2C60-2C7F,U+A720-A7FF}@font-face{font-family:'Open Sans';font-style:italic;font-weight:325;font-stretch:100%;font-display:swap;src:url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-latin-325-italic.woff2) format('woff2'),local("OpenSans-LightItalic");unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+2000-206F,U+2074,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD}@font-face{font-family:"Open Sans";font-style:italic;font-weight:400;font-display:swap;src:local("OpenSans-Italic"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-italic.woff2) format("woff2"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-italic.woff) format("woff")}@font-face{font-family:"Open Sans";font-style:italic;font-weight:500;font-display:swap;src:local("OpenSans-MediumItalic"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-500italic.woff2) format("woff2"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-500italic.woff) format("woff")}@font-face{font-family:"Open Sans";font-style:italic;font-weight:600;font-display:swap;src:local("OpenSans-SemiBoldItalic"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-600italic.woff2) format("woff2"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-600italic.woff) format("woff")}@font-face{font-family:"Open Sans";font-style:italic;font-weight:700;font-display:swap;src:local("OpenSans-BoldItalic"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-700italic.woff2) format("woff2"),url(<?php echo $uri; ?>open-sans-v27-latin-ext_latin/open-sans-v27-latin-ext_latin-700italic.woff) format("woff")}</style>
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
    <meta charset="<?php bloginfo( 'charset' ); ?>">
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
// CHECK FOR UPDATES
// =============================================================================

/**
 * Check Github repository for a new release
 *
 * Makes a cURL request to the Github API to check the latest release and,
 * if a newer version tag than what is installed is found, updates the
 * 'fictioneer_latest_version' option. The request can only be made once
 * every 30 minutes, otherwise the stored data is checked.
 *
 * @since Fictioneer 5.0
 *
 * @return boolean True if there is a newer version, false if not.
 */

function fictioneer_check_for_updates() {
  // Setup
  $last_check = (int) get_option( 'fictioneer_update_check_timestamp', 0 );
  $latest_version = get_option( 'fictioneer_latest_version', FICTIONEER_RELEASE_TAG );

  // Only call API every 30 minutes, otherwise check database
  if ( ! empty( $latest_version ) && time() < $last_check + 1800 ) {
    return version_compare( $latest_version, FICTIONEER_RELEASE_TAG, '>' );
  }

  // Remember this check
  update_option( 'fictioneer_update_check_timestamp', time() );

  // API request to repo
  $ch = curl_init( 'https://api.github.com/repos/Tetrakern/fictioneer/releases/latest' );
  curl_setopt( $ch, CURLOPT_HEADER, 0 );
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
  curl_setopt( $ch, CURLOPT_USERAGENT, 'FICTIONEER' );
  $response = curl_exec( $ch );
  curl_close( $ch );

  // Abort if request failed
  if ( empty( $response ) ) return false;

  // Decode JSON to array
  $release = json_decode( $response, true );

  // Abort if request did not return expected data
  if ( ! isset( $release['tag_name'] ) ) return false;

  // Remember latest version
  update_option( 'fictioneer_latest_version', $release['tag_name'] );

  // Compare with currently installed version
  return version_compare( $release['tag_name'], FICTIONEER_RELEASE_TAG, '>' );
}

/**
 * Show notice when a newer version is available
 *
 * @since Fictioneer 5.0
 */

function fictioneer_admin_update_notice() {
  // Setup
  $last_notice = (int) get_option( 'fictioneer_update_notice_timestamp', 0 );

  // Abort if...
  if ( ! current_user_can( 'manage_options' ) ) return;

  // Show only once every 30 minutes
  if ( $last_notice + 1800 > time() ) return;

  // Update?
  if ( ! fictioneer_check_for_updates() ) return;

  // Render notice
  $message = sprintf(
    __( '<strong>Fictioneer %1$s</strong> is available. Please <a href="%2$s" target="_blank">download</a> and install the latest version at your next convenience.', 'fictioneer' ),
    get_option( 'fictioneer_latest_version', FICTIONEER_RELEASE_TAG ),
    'https://github.com/Tetrakern/fictioneer/releases'
  );

  echo "<div class='notice notice-warning is-dismissible'><p>$message</p></div>";

  // Remember notice
  update_option( 'fictioneer_update_notice_timestamp', time() );
}
add_action( 'admin_notices', 'fictioneer_admin_update_notice' );

?>
