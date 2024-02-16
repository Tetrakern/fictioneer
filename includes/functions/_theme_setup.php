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
  $obsolete = ['fictioneer_disable_html_in_comments', 'fictioneer_block_subscribers_from_admin', 'fictioneer_admin_restrict_menus', 'fictioneer_admin_restrict_private_data', 'fictioneer_admin_reduce_subscriber_profile', 'fictioneer_enable_subscriber_self_delete', 'fictioneer_strip_shortcodes_for_non_administrators', 'fictioneer_restrict_media_access', 'fictioneer_subscription_enabled', 'fictioneer_patreon_badge_map', 'fictioneer_patreon_tier_as_badge', 'fictioneer_patreon_campaign_ids', 'fictioneer_patreon_campaign_id', 'fictioneer_mount_wpdiscuz_theme_styles', 'fictioneer_base_site_width', 'fictioneer_comment_form_selector', 'fictioneer_featherlight_enabled', 'fictioneer_tts_enabled', 'fictioneer_log', 'fictioneer_enable_ajax_nonce', 'fictioneer_flush_object_cache'];

  // Check for most recent obsolete option...
  if ( isset( $options['fictioneer_flush_object_cache'] ) ) {
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
 * @since 1.0.0
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
// PROTECT META FIELDS
// =============================================================================

/**
 * Removes default custom fields meta box
 *
 * @since 5.8.0
 *
 * @param string $post_type  Post type of the post on Edit Post screen, 'link'
 *                           on Edit Link screen, 'dashboard' on Dashboard screen.
 * @param string $context    Meta box context. Possible values include 'normal',
 *                           'advanced', 'side'.
 */

function fictioneer_remove_custom_fields_meta_boxes( $post_type, $context ) {
  remove_meta_box( 'postcustom', $post_type, $context );
}
add_action( 'do_meta_boxes', 'fictioneer_remove_custom_fields_meta_boxes', 1, 2 );

/**
 * Removes 'custom-fields' support for posts and pages
 *
 * @since 5.8.0
 */

function fictioneer_remove_custom_fields_supports() {
  remove_post_type_support( 'post', 'custom-fields' );
  remove_post_type_support( 'page', 'custom-fields' );
}
add_action( 'init', 'fictioneer_remove_custom_fields_supports' );

/**
 * Makes theme meta fields protected
 *
 * @since 5.8.0
 *
 * @param bool   $protected  Whether the meta key is considered protected.
 * @param string $meta_key   The meta key to check.
 *
 * @return bool True if the meta key is protected, false otherwise.
 */

function fictioneer_make_theme_meta_protected( $protected, $meta_key ) {
  if ( strpos( $meta_key, 'fictioneer_' ) === 0 ) {
    return true;
  }

  return $protected;
}
add_filter( 'is_protected_meta', 'fictioneer_make_theme_meta_protected', 10, 2 );

// =============================================================================
// PROTECT PERMALINKS
// =============================================================================

/**
 * Prevents reserved slugs from being used in permalinks
 *
 * @since 5.8.0
 *
 * @param string $slug  The prospective slug.
 *
 * @return string The filtered slug.
 */

function fictioneer_protect_reserved_post_slugs( $slug ) {
  $protected = [FICTIONEER_OAUTH_ENDPOINT, FICTIONEER_EPUB_ENDPOINT, FICTIONEER_LOGOUT_ENDPOINT, 'fictioneer_sitemap'];

  // Prevent slugs from being applied to posts
  if ( in_array( $slug, $protected ) ) {
    return $slug . '-2';
  }

  // Continue filter
  return $slug;
}
add_filter( 'wp_unique_post_slug', 'fictioneer_protect_reserved_post_slugs' );

// =============================================================================
// CUSTOM BACKGROUND CALLBACK
// =============================================================================

/**
 * Fictioneer version of _custom_background_cb() for custom backgrounds
 *
 * @since 4.7.0
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
 * @since 4.7.0
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
 * @since 4.7.0
 */

function fictioneer_root_attributes() {
  global $post;

  // Setup
  $post_author_id = ( $post instanceof WP_Post ) ? $post->post_author : 0;
  $output = [];
  $classes = [];

  if ( is_archive() || is_search() || is_404() ) {
    $post_author_id = 0;
  }

  // Header inset
  if ( get_theme_mod( 'inset_header_image', false ) ) {
    $classes[] = 'inset-header-image';
  }

  // Header style
  $classes[] = 'header-style-' . get_theme_mod( 'header_style', 'default' );

  // Header image style
  if ( get_theme_mod( 'header_image_style', 'default' ) !== 'default' ) {
    $classes[] = 'header-image-style-' . get_theme_mod( 'header_image_style' );
  }

  // Page style
  $page_style = get_theme_mod( 'page_style', 'default' );

  if ( $page_style !== 'default' ) {
    if ( $page_style === 'polygon-mask-image-battered-ringbook' ) {
      $classes[] = 'page-style-polygon-battered';
      $classes[] = 'page-style-mask-image-ringbook';
    } else {
      $classes[] = 'page-style-' . $page_style;
    }

    if ( strpos( $page_style, 'polygon-' ) !== false || strpos( $page_style, 'mask-image-' ) !== false ) {
      $classes[] = 'has-polygon-or-mask';
    }
  }

  // Page shadow
  if ( ! get_theme_mod( 'page_shadow' ) ) {
    $classes[] = 'no-page-shadow';
  }

  // Prepare
  $output['class'] = implode( ' ', $classes );
  $output['data-mode-default'] = get_option( 'fictioneer_light_mode_as_default', false ) ? 'light' : 'dark';
  $output['data-site-width-default'] = get_theme_mod( 'site_width', 960 );
  $output['data-theme'] = 'default';
  $output['data-mode'] = $output['data-mode-default'];
  $output['data-font-weight'] = 'default';
  $output['data-primary-font'] = FICTIONEER_PRIMARY_FONT_CSS;

  $conditions = array(
    'data-age-confirmation' => get_option( 'fictioneer_enable_site_age_confirmation' ),
    'data-caching-active' => fictioneer_caching_active( 'root_attribute' ),
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

  // Fingerprint
  if ( $post_author_id ) {
    $output['data-author-fingerprint'] = fictioneer_get_user_fingerprint( $post_author_id );
  }

  // Filter output
  $output = apply_filters( 'fictioneer_filter_root_attributes', $output );

  // Output
  foreach ( $output as $key => $value ) {
    echo "{$key}='" . esc_attr( $value ) . "' ";
  }
}

// =============================================================================
// ADD BODY CLASSES
// =============================================================================

/**
 * Add additional classes to the frontend <body>
 *
 * @since 5.0.0
 *
 * @param array $classes  Current body classes.
 *
 * @return array The updated body classes.
 */

function fictioneer_add_classes_to_body( $classes ) {
  // Setup
  $user = wp_get_current_user();
  $includes = [];

  // Mobile menu
  if ( get_theme_mod( 'mobile_menu_style', 'minimize_to_right' ) === 'minimize_to_right' ) {
    $classes[] = 'advanced-mobile-menu';
  }

  // Roles
  if ( $user->ID > 0 && ! get_option( 'fictioneer_enable_public_cache_compatibility' ) ) {
    $includes['is-admin'] = fictioneer_is_admin( $user->ID );
    $includes['is-moderator'] = fictioneer_is_moderator( $user->ID );
    $includes['is-author'] = fictioneer_is_author( $user->ID );
    $includes['is-editor'] = fictioneer_is_editor( $user->ID );
  }

  // Browsers
  if ( ! fictioneer_caching_active( 'devise_body_classes' ) ) {
    $includes['is-iphone'] = $GLOBALS['is_iphone'];
    $includes['is-chrome'] = $GLOBALS['is_chrome'];
    $includes['is-safari'] = $GLOBALS['is_safari'];
    $includes['is-opera'] = $GLOBALS['is_opera'];
    $includes['is-gecko'] = $GLOBALS['is_gecko'];
    $includes['is-lynx'] = $GLOBALS['is_lynx'];
    $includes['is-ie'] = $GLOBALS['is_IE'];
    $includes['is-edge'] = $GLOBALS['is_edge'];
  }

  // Add classes to defaults
  foreach ( $includes as $class => $test )  {
    if ( $test ) {
      $classes[ $class ] = $class;
    }
  }

  // Customizations and settings
  if ( get_theme_mod( 'content_list_style', 'default' ) !== 'default' ) {
    $classes[] = 'content-list-style-' . get_theme_mod( 'content_list_style' );
  }

  // Continue filter
  return $classes;
}

if ( ! is_admin() ) {
  add_filter( 'body_class', 'fictioneer_add_classes_to_body' );
}

/**
 * Add additional classes to the admin <body>
 *
 * @since 5.0.0
 *
 * @param string $classes  Current body classes separated by whitespace.
 *
 * @return string The updated body classes.
 */

function fictioneer_add_classes_to_admin_body( $classes ) {
  // Setup
  $user = wp_get_current_user();
  $includes = [];

  // Classes
  $includes['is-admin'] = fictioneer_is_admin( $user->ID );
  $includes['is-moderator'] = fictioneer_is_moderator( $user->ID );
  $includes['is-author'] = fictioneer_is_author( $user->ID );
  $includes['is-editor'] = fictioneer_is_editor( $user->ID );

  // Add classes to defaults
  if ( array_intersect( ['subscriber'], $user->roles ) ) {
    $classes .= ' is-subscriber';
  } else {
    foreach ( $includes as $class => $test )  {
      if ( $test ) {
        $classes .= " $class";
      }
    }
  }

  // Continue filter
  return $classes;
}
add_filter( 'admin_body_class', 'fictioneer_add_classes_to_admin_body' );

// =============================================================================
// ENQUEUE STYLESHEETS
// =============================================================================

/**
 * Enqueues stylesheets
 *
 * @since 1.0.0
 * @since 4.7.0 - Split stylesheets into separate concerns.
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

// =============================================================================
// ENQUEUE CUSTOMIZE CSS
// =============================================================================

/**
 * Enqueues customize stylesheets <head> meta
 *
 * @since 5.11.0
 */

function fictioneer_output_customize_css() {
  // Setup
  $file_path = WP_CONTENT_DIR . '/themes/fictioneer/cache/customize.css';
  $last_built_timestamp = get_option( 'fictioneer_customize_css_timestamp', '123456789' );

  // Create file if it does not exist
  if ( ! file_exists( $file_path ) ) {
    fictioneer_build_customize_css();
  }

  // Output customize stylesheet...
  if ( file_exists( $file_path ) ) {
    wp_enqueue_style(
      'fictioneer-customize',
      get_template_directory_uri() . "/cache/customize.css?timestamp={$last_built_timestamp}",
      ['fictioneer-application'],
      FICTIONEER_VERSION
    );
  }
}

if ( ! is_customize_preview() ) {
  add_action( 'wp_enqueue_scripts', 'fictioneer_output_customize_css', 9999 );
}

/**
 * Enqueues preview customize stylesheets <head> meta
 *
 * @since 5.11.0
 */

function fictioneer_output_customize_preview_css() {
  // Setup
  $file_path = WP_CONTENT_DIR . '/themes/fictioneer/cache/customize-preview.css';

  // Create file if it does not exist
  fictioneer_build_customize_css( 'preview' );

  // Output customize stylesheet...
  if ( file_exists( $file_path ) ) {
    wp_enqueue_style(
      'fictioneer-customize',
      get_template_directory_uri() . "/cache/customize-preview.css?timestamp=" . time(),
      ['fictioneer-application'],
      FICTIONEER_VERSION
    );
  }
}

if ( is_customize_preview() ) {
  add_action( 'wp_enqueue_scripts', 'fictioneer_output_customize_preview_css', 9999 );
}

// =============================================================================
// ENQUEUE OVERRIDE STYLESHEETS
// =============================================================================

/**
 * Enqueues an "override" stylesheet that supersedes all others
 *
 * @since 2.0.0
 */

function fictioneer_style_footer_queue() {
  wp_enqueue_style( 'override', get_template_directory_uri() . '/css/override.css' );
}
add_action( 'get_footer', 'fictioneer_style_footer_queue' );

// =============================================================================
// FONT AWESOME
// =============================================================================

if ( ! function_exists( 'fictioneer_add_font_awesome_integrity' ) ) {
  /**
   * Enqueue Font Awesome
   *
   * @since 5.7.6
   * @link https://fontawesome.com/docs/web/use-with/wordpress/install-manually
   *
   * @param string $tag     The link tag for the enqueued style.
   * @param string $handle  The style's registered handle.
   *
   * @return string The modified link tag.
   */

  function fictioneer_add_font_awesome_integrity( $tag, $handle ) {
    // Abort conditions...
    if ( empty( FICTIONEER_FA_INTEGRITY ) ) {
      return $tag;
    }

    // Modify HTML
    if ( $handle === 'font-awesome-cdn-webfont-fictioneer' ) {
      $tag = preg_replace( '/\/>$/', 'integrity="' . FICTIONEER_FA_INTEGRITY . '" crossorigin="anonymous" />', $tag, 1 );
    }

    // Continue filter
    return $tag;
  }
}

if ( ! function_exists( 'fictioneer_add_font_awesome' ) ) {
  /**
   * Enqueue Font Awesome
   *
   * @since 5.7.6
   * @link https://fontawesome.com/docs/web/use-with/wordpress/install-manually
   */

  function fictioneer_add_font_awesome() {
    // Abort conditions...
    if ( empty( FICTIONEER_FA_CDN ) ) {
      return;
    }

    // Setup
    $actions = ['wp_enqueue_scripts', 'admin_enqueue_scripts', 'login_enqueue_scripts'];

    // Actions
    foreach ( $actions as $action ) {
      add_action(
        $action,
        function() {
          wp_enqueue_style( 'font-awesome-cdn-webfont-fictioneer', FICTIONEER_FA_CDN, [], null );
        }
      );
    }

    // Filters
    add_filter( 'style_loader_tag', 'fictioneer_add_font_awesome_integrity', 10, 2 );
  }

  // Initialize
  if ( ! get_option( 'fictioneer_disable_font_awesome' ) ) {
    fictioneer_add_font_awesome();
  }
}

// =============================================================================
// ENQUEUE SCRIPTS
// =============================================================================

/**
 * Enqueue scripts
 *
 * @since 1.0.0
 * @since 4.7.0 - Split scripts and made enqueuing depending on options.
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
  wp_register_script( 'fictioneer-consent-scripts', get_template_directory_uri() . '/js/laws.min.js', [ 'fictioneer-application-scripts'], FICTIONEER_VERSION, true );

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
    ! get_post_meta( get_the_ID(), 'fictioneer_disable_commenting', true ) &&
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
// ADD SCRIPTS TO LOGIN HEAD
// =============================================================================

/**
 * Print scripts to the wp-login-php
 *
 * @since 5.7.3
 */

function fictioneer_wp_login_scripts() {
  // Clear web storage in preparation of login
  echo "<script type='text/javascript'>localStorage.removeItem('fcnUserData'); localStorage.removeItem('fcnAuth');</script>";
}
add_action( 'login_head', 'fictioneer_wp_login_scripts' );

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
 * @since 5.0.0
 *
 * @param object $scripts  The loaded scripts.
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
 * @since 4.0.0
 * @link https://github.com/wp-plugins/autoptimize
 *
 * @param string $replacetag  The original replace tag.
 */

function fictioneer_replace_ao_insert_position( $replacetag ) {
  return array( '<injectjs />', 'replace' );
}
add_filter( 'autoptimize_filter_js_replacetag', 'fictioneer_replace_ao_insert_position', 10, 1 );

// /**
//  * Exclude scripts from Autoptimize (if installed)
//  *
//  * @since 4.0.0
//  * @link https://github.com/wp-plugins/autoptimize
//  *
//  * @param string $exclude  List of current excludes.
//  *
//  * @return string The updated exclusion string.
//  */

// function fictioneer_ao_exclude_scripts( $exclude ) {
//   return $exclude . '';
// }
// add_filter( 'autoptimize_filter_js_exclude', 'fictioneer_ao_exclude_scripts', 10, 1 );

/**
 * Exclude stylesheets from Autoptimize (if installed)
 *
 * @since 4.0.0
 * @link https://github.com/wp-plugins/autoptimize
 *
 * @param string $exclude  List of current excludes.
 *
 * @return string The updated exclusion string.
 */

function fictioneer_ao_exclude_css( $exclude ) {
  return $exclude . ', fonts-base.css, fonts-full.css, bundled-fonts.css';
}
add_filter( 'autoptimize_filter_css_exclude', 'fictioneer_ao_exclude_css', 10, 1 );

// =============================================================================
// OUTPUT HEAD FONTS
// =============================================================================

if ( ! function_exists( 'fictioneer_output_critical_fonts' ) ) {
  /**
   * Output critical path fonts in <head>
   *
   * Critical fonts that need to be loaded as fast as possible and are
   * therefore inlined in the <head>.
   *
   * @since 5.0.0
   */

  function fictioneer_output_critical_fonts() {
    // Setup
    $primary_font = get_theme_mod( 'primary_font_family_value', 'Open Sans' );
    $uri = get_template_directory_uri() . '/fonts/';

    // Currently, only Open Sans is supported
    if ( $primary_font !== 'Open Sans' ) {
      return;
    }

    // Start HTML ---> ?>
    <style id="inline-fonts" type="text/css">@font-face{font-display:swap;font-family:"Open Sans";font-style:normal;font-weight:300;src:local("OpenSans-Light"),url("<?php echo $uri; ?>open-sans/open-sans-v40-300.woff2") format("woff2")}@font-face{font-display:swap;font-family:"Open Sans";font-style:italic;font-weight:300;src:local("OpenSans-LightItalic"),url("<?php echo $uri; ?>open-sans/open-sans-v40-300italic.woff2") format("woff2")}@font-face{font-display:swap;font-family:"Open Sans";font-style:normal;font-weight:400;src:local("OpenSans-Regular"),url("<?php echo $uri; ?>open-sans/open-sans-v40-regular.woff2") format("woff2")}@font-face{font-display:swap;font-family:"Open Sans";font-style:italic;font-weight:400;src:local("OpenSans-Italic"),url("<?php echo $uri; ?>open-sans/open-sans-v40-italic.woff2") format("woff2")}@font-face{font-display:swap;font-family:"Open Sans";font-style:normal;font-weight:500;src:local("OpenSans-Medium"),url("<?php echo $uri; ?>open-sans/open-sans-v40-500.woff2") format("woff2")}@font-face{font-display:swap;font-family:"Open Sans";font-style:italic;font-weight:500;src:local("OpenSans-MediumItalic"),url("<?php echo $uri; ?>open-sans/open-sans-v40-500italic.woff2") format("woff2")}@font-face{font-display:swap;font-family:"Open Sans";font-style:normal;font-weight:600;src:local("OpenSans-SemiBold"),url("<?php echo $uri; ?>open-sans/open-sans-v40-600.woff2") format("woff2")}@font-face{font-display:swap;font-family:"Open Sans";font-style:italic;font-weight:600;src:local("OpenSans-SemiBoldItalic"),url("<?php echo $uri; ?>open-sans/open-sans-v40-600italic.woff2") format("woff2")}@font-face{font-display:swap;font-family:"Open Sans";font-style:normal;font-weight:700;src:local("OpenSans-Bold"),url("<?php echo $uri; ?>open-sans/open-sans-v40-700.woff2") format("woff2")}@font-face{font-display:swap;font-family:"Open Sans";font-style:italic;font-weight:700;src:local("OpenSans-BoldItalic"),url("<?php echo $uri; ?>open-sans/open-sans-v40-700italic.woff2") format("woff2")}</style>
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
   * @since 5.0.0
   * @since 5.10.0 - Split up for font manager.
   */

  function fictioneer_output_head_meta() {
    // Start HTML ---> ?>
    <meta charset="<?php echo get_bloginfo( 'charset' ); ?>">
    <meta http-equiv="content-type" content="text/html">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <meta name="format-detection" content="telephone=no">
    <meta name="theme-color" content="<?php echo '#' . get_background_color(); ?>">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <?php // <--- End HTML
  }
}

// =============================================================================
// OUTPUT HEAD FONTS
// =============================================================================

if ( ! function_exists( 'fictioneer_output_head_fonts' ) ) {
  /**
   * Output font stylesheets <head> meta
   *
   * Note: This function should be kept pluggable due to legacy reasons.
   *
   * @since 5.10.0
   */

  function fictioneer_output_head_fonts() {
    // Critical path fonts
    fictioneer_output_critical_fonts();

    // Bundled fonts
    $bundled_fonts = WP_CONTENT_DIR . '/themes/fictioneer/cache/bundled-fonts.css';
    $last_built_timestamp = get_option( 'fictioneer_bundled_fonts_timestamp', '123456789' );
    $cache_bust = '?timestamp=' . $last_built_timestamp . '&ver=' . FICTIONEER_VERSION;

    // Create file if it does not exist
    if ( ! file_exists( $bundled_fonts ) ) {
      fictioneer_build_bundled_fonts();
    }

    // Output font stylesheets...
    if ( file_exists( $bundled_fonts ) ) {
      // ... base and custom
      $base_fonts_href = get_template_directory_uri() . '/css/fonts-base.css' . $cache_bust;
      $custom_fonts_href = get_template_directory_uri() . '/cache/bundled-fonts.css' . $cache_bust;

      // Start HTML ---> ?>
      <link rel="stylesheet" id="base-fonts-stylesheet" href="<?php echo $base_fonts_href; ?>" media="print" onload="this.media='all';">
      <noscript><link rel="stylesheet" href="<?php echo $base_fonts_href; ?>"></noscript>
      <link rel="stylesheet" id="bundled-fonts-stylesheet" href="<?php echo $custom_fonts_href; ?>" media="print" onload="this.media='all';">
      <noscript><link rel="stylesheet" href="<?php echo $custom_fonts_href; ?>"></noscript>
      <?php // <--- End HTML
    } else {
      // ... all theme fonts if something goes wrong
      $full_fonts_href = get_template_directory_uri() . '/css/fonts-full.css' . $cache_bust;

      // Start HTML ---> ?>
      <link rel="stylesheet" href="<?php echo $full_fonts_href; ?>" media="print" onload="this.media='all';">
      <noscript><link rel="stylesheet" href="<?php echo $full_fonts_href; ?>"></noscript>
      <?php // <--- End HTML
    }

    // Output Google Fonts links (if any)
    $google_fonts_links = get_option( 'fictioneer_google_fonts_links' );

    if ( ! empty( $google_fonts_links ) ) {
      $google_fonts_links = explode( "\n", $google_fonts_links );

      // Start HTML ---> ?>
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <?php // <--- End HTML

      foreach ( $google_fonts_links as $link ) {
        printf( '<link href="%s" rel="stylesheet">', $link );
      }
    }
  }
}
add_action( 'wp_head', 'fictioneer_output_head_fonts', 5 );
add_action( 'admin_head', 'fictioneer_output_head_fonts', 5 );

// =============================================================================
// MODIFY ROBOTS META
// =============================================================================

/**
 * Adds noindex to robots meta.
 *
 * This is called in the header.php template.
 *
 * @since 5.0.0
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
   * @since 5.0.0
   */

  function fictioneer_output_head_critical_scripts() {
    // Start HTML ---> ?>
    <script>!function(){if("undefined"!=typeof localStorage){const t=localStorage.getItem("fcnLightmode"),e=document.documentElement;let a,o,r=localStorage.getItem("fcnSiteSettings");if(r&&(r=JSON.parse(r))&&null!==r&&"object"==typeof r){Object.entries(r).forEach((t=>{switch(t[0]){case"minimal":e.classList.toggle("minimal",t[1]);break;case"darken":a=r.darken,o=a>=0?1+Math.pow(a,2):1-Math.pow(a,2),e.style.setProperty("--darken",o);break;case"saturation":a=r.saturation,o=a>=0?1+Math.pow(a,2):1-Math.pow(a,2),e.style.setProperty("--saturation",o);break;case"font-saturation":a=r["font-saturation"],o=a>=0?1+Math.pow(a,2):1-Math.pow(a,2),e.style.setProperty("--font-saturation",o);break;case"hue-rotate":o=Number.isInteger(r["hue-rotate"])?r["hue-rotate"]:0,e.style.setProperty("--hue-rotate",`${o}deg`);break;default:e.classList.toggle(`no-${t[0]}`,!t[1])}})),e.dataset.fontWeight=r.hasOwnProperty("font-weight")?r["font-weight"]:"default",e.dataset.theme=r.hasOwnProperty("site-theme")&&!e.dataset.forceChildTheme?r["site-theme"]:"default";let t=getComputedStyle(document.documentElement).getPropertyValue("--theme-color-base").trim().split(" "),n=r.darken?r.darken:0,s=r.saturation?r.saturation:0,i=r["hue-rotate"]?r["hue-rotate"]:0,l=n>=0?1+Math.pow(n,2):1-Math.pow(n,2);r=s>=0?1+Math.pow(s,2):1-Math.pow(s,2),t=`hsl(${(parseInt(t[0])+i)%360}deg ${(parseInt(t[1])*r).toFixed(2)}% ${(parseInt(t[2])*l).toFixed(2)}%)`,document.querySelector("meta[name=theme-color]").setAttribute("content",t)}t&&(e.dataset.mode="true"==t?"light":"dark")}}();</script>
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
 * @since 5.4.0
 * @link https://developer.wordpress.org/reference/functions/paginate_links/
 *
 * @param array $args  Optional. Array of arguments for generating the pagination links.
 *
 * @return string Modified pagination links HTML.
 */

function fictioneer_paginate_links( $args = [] ) {
  $args['end_size'] = 1;
  $args['mid_size'] = 1;
  $pagination = paginate_links( $args );

  if ( ! $pagination ) {
    return '';
  }

  return str_replace(
    'class="page-numbers dots"',
    'class="page-numbers dots" tabindex="0"',
    $pagination
  );
}

// =============================================================================
// MODIFY ADMINBAR
// =============================================================================

/**
 * Adds 'Add Chapter' link to adminbar with pre-assigned chapter story
 *
 * @since 5.9.3
 *
 * @param WP_Admin_Bar $wp_admin_bar  The WP_Admin_Bar instance, passed by reference.
 */

function fictioneer_adminbar_add_chapter_link( $wp_admin_bar ) {
  if ( ( is_single() && get_post_type() === 'fcn_story' ) || ( is_admin() && get_current_screen()->id === 'fcn_story' ) ) {
    $story_id = get_the_ID();
    $story_post = get_post( $story_id );

    if ( $story_id && $story_post->post_author == get_current_user_id() ) {
      $wp_admin_bar->add_node(
        array(
          'id' => 'fictioneer-add-chapter',
          'title' => '<span class="ab-icon dashicons dashicons-text-page" style="top: 4px; font-size: 16px;"></span> ' . __( 'Add Chapter', 'fictioneer' ),
          'href' => admin_url( 'post-new.php?post_type=fcn_chapter&story_id=' . $story_id ),
          'meta' => array( 'class' => 'adminbar-add-chapter' )
        )
      );
    }
  }
}
add_action( 'admin_bar_menu', 'fictioneer_adminbar_add_chapter_link', 99 );

?>
