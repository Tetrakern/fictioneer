<?php

// =============================================================================
// URI HELPERS
// =============================================================================

/**
 * Returns URI to the theme cache
 *
 * @since 5.23.1
 *
 * @param string|null $context  The context of the call. Default null.
 *
 * @return string URI to the theme cache.
 */

function fictioneer_get_theme_cache_uri( $context = null ) {
  return apply_filters(
    'fictioneer_filter_cache_uri',
    get_template_directory_uri() . '/cache',
    $context
  );
}

/**
 * Returns directory path of the theme cache
 *
 * @since 5.23.1
 *
 * @param string|null $context  The context of the call. Default null.
 *
 * @return string Path of the theme directory.
 */

function fictioneer_get_theme_cache_dir( $context = null ) {
  static $is_dir = null;

  $dir = apply_filters(
    'fictioneer_filter_cache_dir',
    WP_CONTENT_DIR . '/themes/fictioneer/cache',
    $context
  );

  if ( ! $is_dir && ! is_dir( $dir ) ) {
    $is_dir = mkdir( $dir, 0755, true );
  }

  return $dir;
}

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
  $obsolete = ['fictioneer_disable_html_in_comments', 'fictioneer_block_subscribers_from_admin', 'fictioneer_admin_restrict_menus', 'fictioneer_admin_restrict_private_data', 'fictioneer_admin_reduce_subscriber_profile', 'fictioneer_enable_subscriber_self_delete', 'fictioneer_strip_shortcodes_for_non_administrators', 'fictioneer_restrict_media_access', 'fictioneer_subscription_enabled', 'fictioneer_patreon_badge_map', 'fictioneer_patreon_tier_as_badge', 'fictioneer_patreon_campaign_ids', 'fictioneer_patreon_campaign_id', 'fictioneer_mount_wpdiscuz_theme_styles', 'fictioneer_base_site_width', 'fictioneer_featherlight_enabled', 'fictioneer_tts_enabled', 'fictioneer_log', 'fictioneer_enable_ajax_nonce', 'fictioneer_flush_object_cache', 'fictioneer_enable_all_block_styles', 'fictioneer_light_mode_as_default', 'fictioneer_remove_wp_svg_filters', 'fictioneer_update_check_timestamp', 'fictioneer_latest_version', 'fictioneer_update_notice_timestamp', 'fictioneer_theme_status', 'fictioneer_disable_anti_flicker', 'fictioneer_query_cache_registry', 'fictioneer_disable_whatsapp_share', 'fictioneer_disable_telegram_share', 'fictioneer_enable_query_result_caching', 'fictioneer_disable_all_widgets'];

  // Check for most recent obsolete option...
  if ( isset( $options['fictioneer_disable_all_widgets'] ) ) {
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
 * Get basic theme info
 *
 * @since 5.19.1
 *
 * @return array Associative array with theme info.
 */

function fictioneer_get_theme_info() {
  // Setup
  $info = get_option( 'fictioneer_theme_info' ) ?: [];

  // Set up if missing
  if ( ! $info || ! is_array( $info ) ) {
    $info = array(
      'last_update_check' => current_time( 'mysql', 1 ),
      'last_update_version' => '',
      'last_update_nag' => current_time( 'mysql', 1 ),
      'last_update_notes' => '',
      'last_version_download_url' => '',
      'setup' => 0,
      'version' => FICTIONEER_VERSION
    );

    update_option( 'fictioneer_theme_info', $info, 'yes' );
  }

  // Merge with defaults (in case of incomplete data)
  $info = array_merge(
    array(
      'last_update_check' => current_time( 'mysql', 1 ),
      'last_update_version' => '',
      'last_update_nag' => '',
      'last_update_notes' => '',
      'last_version_download_url' => '',
      'setup' => 0,
      'version' => FICTIONEER_VERSION
    ),
    $info
  );

  // Return info
  return $info;
}

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
  register_nav_menu( 'nav_menu', __( 'Navigation', 'fictioneer' ) );
  register_nav_menu( 'footer_menu', __( 'Footer Menu', 'fictioneer' ) );

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
      'height' => get_theme_mod( 'header_image_height_max', 480 ),
      'width' => (int) get_theme_mod( 'site_width', FICTIONEER_DEFAULT_SITE_WIDTH ) * 1.5
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

  // Add new size for cover images used on story pages
  add_image_size( 'cover', 400 );

  // Add new size for cover snippets used in cards
  add_image_size( 'snippet', 0, 200 );

  // After update actions
  $theme_info = fictioneer_get_theme_info();

  if ( ( $theme_info['version'] ?? 0 ) !== FICTIONEER_VERSION ) {
    require_once( 'settings/_settings.php' );

    $previous_version = $theme_info['version'];
    $theme_info['version'] = FICTIONEER_VERSION;

    update_option( 'fictioneer_theme_info', $theme_info, 'yes' );

    do_action( 'fictioneer_after_update', $theme_info['version'], $previous_version );
  }
}
add_action( 'after_setup_theme', 'fictioneer_theme_setup' );

// =============================================================================
// AFTER UPDATE
// =============================================================================

/**
 * Purges selected caches and Transients after update
 *
 * @since 5.12.2
 * @since 5.27.3 - Use fictioneer_purge_theme_caches();
 */

function fictioneer_purge_caches_after_update() {
  if ( function_exists( 'fictioneer_purge_theme_caches' ) ) {
    fictioneer_purge_theme_caches();
  }
}
add_action( 'fictioneer_after_update', 'fictioneer_purge_caches_after_update' );

/**
 * Removes default chapter icons from post meta table
 *
 * @since 5.24.1
 */

function fictioneer_remove_default_chapter_icons_from_meta() {
  global $wpdb;

  $wpdb->query(
    $wpdb->prepare(
      "
      DELETE FROM $wpdb->postmeta
      WHERE meta_key = %s
      AND meta_value = %s
      ",
      'fictioneer_chapter_icon',
      FICTIONEER_DEFAULT_CHAPTER_ICON
    )
  );
}
add_action( 'fictioneer_after_update', 'fictioneer_remove_default_chapter_icons_from_meta' );

/**
 * Removes obsolete fictioneer_first_publish_date from post meta table
 *
 * @since 5.24.1
 */

function fictioneer_remove_first_publish_date_from_meta() {
  global $wpdb;

  $wpdb->query(
    $wpdb->prepare(
      "DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s",
      'fictioneer_first_publish_date'
    )
  );
}
add_action( 'fictioneer_after_update', 'fictioneer_remove_first_publish_date_from_meta' );

/**
 * Converts user deleted comments to user_deleted type and delete meta fields
 *
 * @since 5.24.1
 */

function fictioneer_migrate_deleted_comments() {
  global $wpdb;

  $wpdb->query(
    "
    UPDATE $wpdb->comments
    SET comment_type = 'user_deleted'
    WHERE comment_ID IN (
      SELECT comment_id
      FROM $wpdb->commentmeta
      WHERE meta_key = 'fictioneer_deleted_by_user'
    )
    "
  );

  $wpdb->query(
    "
    DELETE FROM $wpdb->commentmeta
    WHERE meta_key = 'fictioneer_deleted_by_user'
    "
  );
}
add_action( 'fictioneer_after_update', 'fictioneer_migrate_deleted_comments' );

/**
 * Deletes obsolete comment meta fields
 *
 * @since 5.24.4
 */

function fictioneer_remove_obsolete_comment_meta() {
  global $wpdb;

  $meta_keys = ['fictioneer_deleted_user_id', 'fictioneer_deleted_username_substr', 'fictioneer_deleted_email_substr'];
  $placeholders = implode( ',', array_fill( 0, count( $meta_keys ), '%s' ) );

  $wpdb->query(
    $wpdb->prepare(
      "
      DELETE FROM {$wpdb->commentmeta}
      WHERE meta_key IN ($placeholders)
      ",
      ...$meta_keys
    )
  );
}
add_action( 'fictioneer_after_update', 'fictioneer_remove_obsolete_comment_meta' );

/**
 * Deletes the cached SEO meta data of the front page
 *
 * @since 5.25.0
 */

function fictioneer_purge_front_page_seo_cache() {
  if ( $font_page_id = get_option( 'page_on_front' ) ) {
    delete_post_meta( $font_page_id, 'fictioneer_seo_cache' );
  }
}
add_action( 'fictioneer_after_update', 'fictioneer_purge_front_page_seo_cache' );

/**
 * Restore disabled autoload for widgets.
 *
 * @since 5.27.3
 *
 * @param string $version  The version string after the update.
 */

function fictioneer_restore_disabled_widgets_autoload( $version ) {
  if ( version_compare( $version, '5.27.3', '<=' ) ) {
    global $wpdb;

    $wpdb->query(
      $wpdb->prepare(
        "UPDATE {$wpdb->prefix}options SET autoload=%s WHERE option_name LIKE %s",
        'yes',
        $wpdb->esc_like( 'widget_' ) . '%'
      )
    );
  }
}
add_action( 'fictioneer_after_update', 'fictioneer_restore_disabled_widgets_autoload' );

// =============================================================================
// SIDEBAR
// =============================================================================

/**
 * Registers sidebar
 *
 * @since 5.20.0
 */

function fictioneer_register_sidebar() {
  register_sidebar(
    array(
      'name' => __( 'Fictioneer Sidebar', 'fictioneer' ),
      'id' => 'fictioneer-sidebar',
      'description' => __( 'To render this sidebar, enable it in the Customizer or insert it with the <code>[fictioneer_sidebar]</code> shortcode. It can also be rendered with the Elementor plugin.', 'fictioneer' ),
      'before_widget' => '<div id="%1$s" class="widget %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<h2 class="widgettitle">',
      'after_title' => '</h2>'
    )
  );
}
add_action( 'widgets_init', 'fictioneer_register_sidebar' );

/**
 * Renders sidebar
 *
 * @since 5.22.0
 *
 * @param string $context  Render context (story, chapter, etc.) of the sidebar.
 */

function fictioneer_sidebar( $context ) {
  // Do not render sidebar if...
  if ( get_theme_mod( 'sidebar_style', 'none' ) === 'none' || ! is_active_sidebar( 'fictioneer-sidebar' ) ) {
    return;
  }

  // Setup
  $post = get_post();
  $sidebar_disabled = array(
    'fcn_story' => get_theme_mod( 'sidebar_disable_in_stories' ),
    'fcn_chapter' => get_theme_mod( 'sidebar_disable_in_chapters' ),
    'fcn_collection' => get_theme_mod( 'sidebar_disable_in_collections' ),
    'fcn_recommendation' => get_theme_mod( 'sidebar_disable_in_recommendations' )
  );

  // If not archive or search and post exists...
  if ( $post && ! is_archive() && ! is_search() ) {
    // .. check specific escape conditions
    if (
      ( $sidebar_disabled[ $post->post_type ] ?? 0 ) ||
      get_post_meta( $post->ID, 'fictioneer_disable_sidebar', true )
    ) {
      return;
    }
  }

  // Classes
  $classes = [];

  if ( get_theme_mod( 'sidebar_hide_on_mobile' ) ) {
    $classes[] = '_hide-on-mobile';
  }

  // Remove filters
  remove_filter( 'excerpt_more', 'fictioneer_excerpt_ellipsis' );
  remove_filter( 'excerpt_length', 'fictioneer_custom_excerpt_length' );

  // Start HTML ---> ?>
  <aside class="fictioneer-sidebar _layout <?php echo implode( ' ', $classes ); ?>">
    <div class="fictioneer-sidebar__wrapper _layout"><?php dynamic_sidebar( 'fictioneer-sidebar' ); ?></div>
  </aside>
  <?php // <--- End HTML

  // Restore filters
  add_filter( 'excerpt_more', 'fictioneer_excerpt_ellipsis' );
  add_filter( 'excerpt_length', 'fictioneer_custom_excerpt_length' );
}

if ( get_theme_mod( 'sidebar_style' ) === 'right' ) {
  add_action( 'fictioneer_main_end', 'fictioneer_sidebar' );
} else {
  add_action( 'fictioneer_main', 'fictioneer_sidebar' );
}

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

if ( ! get_option( 'fictioneer_enable_custom_fields' ) ) {
  add_action( 'do_meta_boxes', 'fictioneer_remove_custom_fields_meta_boxes', 1, 2 );
}

/**
 * Removes 'custom-fields' support for all posts
 *
 * Note: Disable this if you need the feature support.
 *
 * @since 5.8.0
 * @since 5.21.1 - Added all post types.
 */

function fictioneer_remove_custom_fields_supports() {
  remove_post_type_support( 'post', 'custom-fields' );
  remove_post_type_support( 'page', 'custom-fields' );
  remove_post_type_support( 'fcn_story', 'custom-fields' );
  remove_post_type_support( 'fcn_chapter', 'custom-fields' );
  remove_post_type_support( 'fcn_collection', 'custom-fields' );
  remove_post_type_support( 'fcn_recommendation', 'custom-fields' );
}

if ( ! get_option( 'fictioneer_enable_custom_fields' ) ) {
  add_action( 'init', 'fictioneer_remove_custom_fields_supports' );
}

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
  $classes = ['fictioneer-theme', 'no-js'];

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
  if ( ! get_theme_mod( 'page_shadow', true ) ) {
    $classes[] = 'no-page-shadow';
  }

  // Stimulus
  $output['data-controller'] = 'fictioneer';

  // Prepare
  $output['class'] = implode( ' ', $classes );
  $output['data-mode-default'] = get_option( 'fictioneer_dark_mode_as_default', false ) ? 'dark' : 'light';
  $output['data-site-width-default'] = get_theme_mod( 'site_width', FICTIONEER_DEFAULT_SITE_WIDTH );
  $output['data-theme'] = 'default';
  $output['data-mode'] = $output['data-mode-default'];
  $output['data-font-weight'] = 'default';
  $output['data-primary-font'] = FICTIONEER_PRIMARY_FONT_CSS;

  if ( get_post_type( $post ) === 'fcn_chapter' ) {
    // Also set on the frontend, this is only for customization.
    $formatting = array(
      // 'font-saturation' => 0,
      // 'font-size' => 100,
      // 'letter-spacing' => 0,
      // 'line-height' => 1.7,
      // 'paragraph-spacing' => 1.5,
      'indent' => get_option( 'fictioneer_disable_default_formatting_indent', 0 ) ? false : true,
      // 'show-sensitive-content' => true,
      // 'show-chapter-notes' => true,
      // 'justify' => false,
      // 'show-comments' => true,
      // 'show-paragraph-tools' => true
    );

    $formatting = apply_filters( 'fictioneer_filter_chapter_default_formatting', $formatting );

    if ( ! empty( $formatting ) ) {
      $output['data-default-formatting'] = json_encode( $formatting );
    }
  }

  $conditions = array(
    'age-confirmation' => get_option( 'fictioneer_enable_site_age_confirmation' ),
    'caching-active' => fictioneer_caching_active( 'root_attribute' ),
    'ajax-submit' => get_option( 'fictioneer_enable_ajax_comment_submit', false ),
    'force-child-theme' => ! FICTIONEER_THEME_SWITCH,
    'public-caching' => get_option( 'fictioneer_enable_public_cache_compatibility', false ),
    'ajax-auth' => get_option( 'fictioneer_enable_ajax_authentication', false ),
    'edit-time' => get_option( 'fictioneer_enable_user_comment_editing', false ) ?
      get_option( 'fictioneer_user_comment_edit_time', 15 ) : false,
  );

  // Iterate conditions and add the truthy to the output
  foreach ( $conditions as $key => $condition ) {
    if ( $condition ) {
      $value = is_bool( $condition ) ? '1' : $condition;

      $output["data-{$key}"] = $value;
      $output["data-fictioneer-{$key}-value"] = $value;
    }
  }

  // Fingerprint
  if ( $post_author_id ) {
    $output['data-author-fingerprint'] = fictioneer_get_user_fingerprint( $post_author_id );
    $output['data-fictioneer-fingerprint-value'] = $output['data-author-fingerprint'];
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
  $post = get_post();
  $user = wp_get_current_user();
  $template = get_page_template_slug();
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

  // Sidebar
  $sidebar_style = get_theme_mod( 'sidebar_style', 'none' );

  if (
    $sidebar_style !== 'none' &&
    is_active_sidebar( 'fictioneer-sidebar' )
  ) {
    $sidebar_disabled = array(
      'fcn_story' => get_theme_mod( 'sidebar_disable_in_stories' ),
      'fcn_chapter' => get_theme_mod( 'sidebar_disable_in_chapters' ),
      'fcn_collection' => get_theme_mod( 'sidebar_disable_in_collections' ),
      'fcn_recommendation' => get_theme_mod( 'sidebar_disable_in_recommendations' )
    );
    $is_sidebar_disabled = false;

    // If not archive or search and post exists...
    if ( $post && ! is_archive() && ! is_search() ) {
      // .. check specific escape conditions
      if (
        ( $sidebar_disabled[ $post->post_type ] ?? 0 ) ||
        get_post_meta( $post->ID, 'fictioneer_disable_sidebar', true ) ||
        in_array( $template, ['singular-canvas-main.php', 'singular-canvas-site.php'] )
      ) {
        $is_sidebar_disabled = true;
      }
    }

    if ( ! $is_sidebar_disabled ) {
      $classes[] = 'has-sidebar';

      if ( $sidebar_style === 'right' ) {
        $classes[] = 'has-sidebar-right';
      } else {
        $classes[] = 'has-sidebar-left';
      }
    }
  }

  // Page padding
  if ( $post && get_post_meta( $post->ID, 'fictioneer_disable_page_padding', true ) ) {
    $classes[] = 'no-page-padding';
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
// CACHE BUSTING
// =============================================================================

/**
 * Returns saved random cache busting string
 *
 * @since 5.12.5
 *
 * @return string Cache busting string.
 */

function fictioneer_get_cache_bust() {
  $cache_bust = get_option( 'fictioneer_cache_bust' );

  if ( empty( $cache_bust ) ) {
    $cache_bust = fictioneer_regenerate_cache_bust();
  }

  return $cache_bust;
}

/**
 * Regenerate cache busting string
 *
 * @since 5.12.5
 */

function fictioneer_regenerate_cache_bust() {
  $cache_bust = time();

  update_option( 'fictioneer_cache_bust', $cache_bust, '', true );

  return $cache_bust;
}
add_action( 'customize_save_after', 'fictioneer_regenerate_cache_bust' );

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
  global $post;

  // Setup
  $cache_bust = fictioneer_get_cache_bust();

  // Either load separate small style files on demand or the complete one
  if ( ! get_option( 'fictioneer_bundle_stylesheets' ) ) {
    // Setup
    $post_type = get_post_type();
    $template_slug = get_page_template_slug();
    $application_dependencies = [];

    // Properties
    if ( ! get_option( 'fictioneer_disable_properties' ) ) {
      wp_enqueue_style(
        'fictioneer-properties',
        get_template_directory_uri() . '/css/properties.css',
        [],
        $cache_bust
      );
      $application_dependencies[] = 'fictioneer-properties';
    }

    // Application
    wp_enqueue_style(
      'fictioneer-application',
      get_template_directory_uri() . '/css/application.css',
      $application_dependencies,
      $cache_bust
    );

    // If NOT an archive or search page...
    if ( ! is_search() && ! is_archive() ) {
      // Collections
      if ( is_page_template( 'stories.php' ) || $post_type == 'fcn_collection' ) {
        wp_enqueue_style(
          'fictioneer-collections',
          get_template_directory_uri() . '/css/collections.css',
          ['fictioneer-application'],
          $cache_bust
        );
      }

      // Chapter
      if ( $post_type == 'fcn_chapter' ) {
        wp_enqueue_style(
          'fictioneer-chapter',
          get_template_directory_uri() . '/css/chapter.css',
          ['fictioneer-application'],
          $cache_bust
        );
      }

      // Story
      if ( $post_type == 'fcn_story' || is_page_template( 'singular-story.php' ) ) {
        wp_enqueue_style(
          'fictioneer-story',
          get_template_directory_uri() . '/css/story.css',
          ['fictioneer-application'],
          $cache_bust
        );
      }

      // Recommendation
      if ( $post_type == 'fcn_recommendation' ) {
        wp_enqueue_style(
          'fictioneer-recommendation',
          get_template_directory_uri() . '/css/recommendation.css',
          ['fictioneer-application'],
          $cache_bust
        );
      }

      // Comments
      if (
        $post_type == 'fcn_story' ||
        $template_slug === 'user-profile.php' ||
        comments_open() ||
        is_page_template( 'singular-story.php' )
      ) {
        wp_enqueue_style(
          'fictioneer-comments',
          get_template_directory_uri() . '/css/comments.css',
          ['fictioneer-application'],
          $cache_bust
        );
      }

      // Shortcodes
      if (
        ( $post && preg_match( '/\[fictioneer_latest_[^\]]*type="list"[^\]]*\]/', $post->post_content ) ) ||
        strpos( $_SERVER['REQUEST_URI'], 'elementor' ) !== false // Accounts for page editors like Elementor
      ) {
        wp_enqueue_style(
          'fictioneer-post-list',
          get_template_directory_uri() . '/css/post-list.css',
          ['fictioneer-application'],
          $cache_bust
        );
      }
    }

    // Archive
    if ( is_archive() ) {
      wp_enqueue_style(
        'fictioneer-archive',
        get_template_directory_uri() . '/css/taxonomies.css',
        ['fictioneer-application'],
        $cache_bust
      );
    }
  } else {
    // Complete
    wp_enqueue_style(
      'fictioneer-complete',
      get_template_directory_uri() . '/css/complete.css',
      [],
      $cache_bust
    );
  }

  // Enqueue Splide CSS
  if (
    get_option( 'fictioneer_enable_global_splide' ) ||
    ( $post && preg_match( '/\[fictioneer_[a-zA-Z0-9_]*[^\]]*splide=["\']([^"\']+)["\'][^\]]*\]/', $post->post_content ) ) ||
    strpos( $_SERVER['REQUEST_URI'], 'elementor' ) !== false // Accounts for page editors like Elementor
  ) {
    wp_enqueue_style(
      'fictioneer-splide',
      get_template_directory_uri() . '/css/splide.css',
      [],
      $cache_bust
    );
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
  $file_path = fictioneer_get_theme_cache_dir( 'output_customize_css' ) . '/customize.css';

  // Create file if it does not exist
  if ( ! file_exists( $file_path ) ) {
    fictioneer_build_customize_css();
  }

  // Output customize stylesheet...
  if ( file_exists( $file_path ) ) {
    wp_enqueue_style(
      'fictioneer-customize',
      fictioneer_get_theme_cache_uri( 'output_customize_css' ) . "/customize.css",
      get_option( 'fictioneer_bundle_stylesheets' ) ? ['fictioneer-complete'] : ['fictioneer-application'],
      fictioneer_get_cache_bust()
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
  $file_path = fictioneer_get_theme_cache_dir( 'output_customize_preview_css' ) . '/customize-preview.css';

  // Create file if it does not exist
  fictioneer_build_customize_css( 'preview' );

  // Output customize stylesheet...
  if ( file_exists( $file_path ) ) {
    wp_enqueue_style(
      'fictioneer-customize',
      fictioneer_get_theme_cache_uri( 'output_customize_preview_css' ) . "/customize-preview.css",
      ['fictioneer-application'],
      time() // Prevents caching in preview
    );
  }
}

if ( is_customize_preview() ) {
  add_action( 'wp_enqueue_scripts', 'fictioneer_output_customize_preview_css', 9999 );
}

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
      $tag = preg_replace( '/\/>$/', 'integrity="' . FICTIONEER_FA_INTEGRITY . '" data-jetpack-boost="ignore" data-no-defer="1" data-no-optimize="1" data-no-minify="1" crossorigin="anonymous" />', $tag, 1 );
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
 * Returns the filtered FFCNR URL.
 *
 * @since 5.27.3
 */

function fictioneer_get_ffcnr_url() {
  return apply_filters( 'fictioneer_filter_ffcnr_url', get_template_directory_uri() . '/ffcnr.php' );
}

/**
 * Builds dynamic script file
 *
 * @since 5.12.2
 */

function fictioneer_build_dynamic_scripts() {
  // --- Setup -----------------------------------------------------------------

  $file_path = fictioneer_get_theme_cache_dir( 'build_dynamic_scripts' ) . '/dynamic-scripts.js';
  $last_version = get_transient( 'fictioneer_dynamic_scripts_version' );
  $scripts = '';

  // Rebuild necessary?
  if ( file_exists( $file_path ) && $last_version === FICTIONEER_VERSION ) {
    return;
  }

  // --- AJAX Settings ---------------------------------------------------------

  $scripts .= "var fictioneer_ajax = " . json_encode( array(
    'ajax_url' => admin_url( 'admin-ajax.php' ),
    'rest_url' => get_rest_url( null, 'fictioneer/v1/' ),
    'ffcnr_url' => fictioneer_get_ffcnr_url(),
    'ttl' => FICTIONEER_AJAX_TTL,
    'post_debounce_rate' => FICTIONEER_AJAX_POST_DEBOUNCE_RATE,
    'ffcnr_auth' => get_option( 'fictioneer_enable_ffcnr_auth', 0 ) ? 1 : 0
  )) . ";";

  // --- Removable query args --------------------------------------------------

  $removable_query_args = ['success', 'failure', 'fictioneer_nonce', 'fictioneer-notice'];
  $removable_query_args = apply_filters( 'fictioneer_filter_removable_query_args', $removable_query_args );

  $scripts .=
    "function fcn_removeQueryArgs(){history.replaceState && history.replaceState(null, '', location.pathname + location.search.replace(/[?&](" . implode( '|', $removable_query_args ) . ")=[^&]+/g, '').replace(/^[?&]/, '?') + location.hash);}";

  // --- Fonts -----------------------------------------------------------------

  $scripts .= "var fictioneer_fonts = " . json_encode( fictioneer_get_fonts() ) . ";";

  // --- Colors ----------------------------------------------------------------

  $scripts .= "var fictioneer_font_colors = " . json_encode( fictioneer_get_font_colors() ) . ";";

  // --- Comments --------------------------------------------------------------

  $scripts .= "var fictioneer_comments = " . json_encode( array(
    'form_selector' => get_option( 'fictioneer_comment_form_selector', '#comment' ) ?: '#comment'
  )) . ";";

  // --- Save ------------------------------------------------------------------

  set_transient( 'fictioneer_dynamic_scripts_version', FICTIONEER_VERSION );
  file_put_contents( $file_path, $scripts );
}

/**
 * Enqueue scripts
 *
 * @since 1.0.0
 * @since 4.7.0 - Split scripts and made enqueuing depending on options.
 * @since 5.12.2 - Added defer loading strategy; added dynamic script file.
 */

function fictioneer_add_custom_scripts() {
  global $post;

  // Setup
  $post_type = get_post_type();
  $cache_bust = fictioneer_get_cache_bust();
  $password_required = post_password_required();
  $strategy = fictioneer_compare_wp_version( '6.3' ) && FICTIONEER_DEFER_SCRIPTS
    ? array( 'strategy'  => 'defer' ) : true; // Defer or load in footer

  // Dynamic scripts
  fictioneer_build_dynamic_scripts();

  wp_register_script(
    'fictioneer-dynamic-scripts',
    fictioneer_get_theme_cache_uri( 'add_custom_scripts' ) . '/dynamic-scripts.js',
    [],
    $cache_bust,
    $strategy
  );

  // Register and enqueue single scripts or complete script
  if ( ! get_option( 'fictioneer_bundle_scripts' ) ) {
    // Utility
    wp_register_script( 'fictioneer-utility-scripts', get_template_directory_uri() . '/js/utility.min.js', ['fictioneer-dynamic-scripts'], $cache_bust, $strategy );

    // Application
    wp_register_script( 'fictioneer-application-scripts', get_template_directory_uri() . '/js/application.min.js', [ 'fictioneer-utility-scripts', 'fictioneer-dynamic-scripts'], $cache_bust, $strategy );

    // Mobile menu
    wp_register_script( 'fictioneer-mobile-menu-scripts', get_template_directory_uri() . '/js/mobile-menu.min.js', [ 'fictioneer-application-scripts'], $cache_bust, $strategy );

    // Chapter
    wp_register_script( 'fictioneer-chapter-scripts', get_template_directory_uri() . '/js/chapter.min.js', [ 'fictioneer-application-scripts'], $cache_bust, $strategy );

    // Diff-Match-Patch
    wp_register_script( 'fictioneer-dmp', get_template_directory_uri() . '/js/diff-match-patch.js', [ 'fictioneer-chapter-scripts'], $cache_bust, $strategy );

    // Suggestions
    wp_register_script( 'fictioneer-suggestion-scripts', get_template_directory_uri() . '/js/suggestion.min.js', [ 'fictioneer-chapter-scripts', 'fictioneer-dmp'], $cache_bust, $strategy );

    // Text-To-Speech
    wp_register_script( 'fictioneer-tts-scripts', get_template_directory_uri() . '/js/tts.min.js', [ 'fictioneer-chapter-scripts'], $cache_bust, $strategy );

    // Story
    wp_register_script( 'fictioneer-story-scripts', get_template_directory_uri() . '/js/story.min.js', [ 'fictioneer-application-scripts'], $cache_bust, $strategy );

    // User Profile
    wp_register_script( 'fictioneer-user-profile-scripts', get_template_directory_uri() . '/js/user-profile.min.js', [ 'fictioneer-application-scripts'], $cache_bust, $strategy );

    // CSS Skins
    wp_register_script( 'fictioneer-css-skins-scripts', get_template_directory_uri() . '/js/css-skins.min.js', [ 'fictioneer-application-scripts'], $cache_bust, $strategy );

    // Bookmarks
    wp_register_script( 'fictioneer-bookmarks-scripts', get_template_directory_uri() . '/js/bookmarks.min.js', [ 'fictioneer-application-scripts'], $cache_bust, $strategy );

    // Follows
    wp_register_script( 'fictioneer-follows-scripts', get_template_directory_uri() . '/js/follows.min.js', [ 'fictioneer-application-scripts'], $cache_bust, $strategy );

    // Checkmarks
    wp_register_script( 'fictioneer-checkmarks-scripts', get_template_directory_uri() . '/js/checkmarks.min.js', [ 'fictioneer-application-scripts'], $cache_bust, $strategy );

    // Reminders
    wp_register_script( 'fictioneer-reminders-scripts', get_template_directory_uri() . '/js/reminders.min.js', [ 'fictioneer-application-scripts'], $cache_bust, $strategy );

    // Comments
    wp_register_script( 'fictioneer-comments-scripts', get_template_directory_uri() . '/js/comments.min.js', [ 'fictioneer-application-scripts'], $cache_bust, $strategy );

    // AJAX Comments
    wp_register_script( 'fictioneer-ajax-comments-scripts', get_template_directory_uri() . '/js/ajax-comments.min.js', [ 'fictioneer-comments-scripts'], $cache_bust, $strategy );

    // AJAX Bookshelf
    wp_register_script( 'fictioneer-ajax-bookshelf-scripts', get_template_directory_uri() . '/js/ajax-bookshelf.min.js', [ 'fictioneer-application-scripts'], $cache_bust, $strategy );

    // Enqueue utility
    wp_enqueue_script( 'fictioneer-utility-scripts' );

    // Enqueue application
    wp_enqueue_script( 'fictioneer-application-scripts' );

    // Enqueue mobile menu
    wp_enqueue_script( 'fictioneer-mobile-menu-scripts' );

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
      ! $password_required &&
      ! get_post_meta( get_the_ID(), 'fictioneer_disable_commenting', true ) &&
      comments_open()
    ) {
      wp_enqueue_script( 'fictioneer-dmp' );
      wp_enqueue_script( 'fictioneer-suggestion-scripts' );
    }

    // Enqueue TTS
    if (
      $post_type == 'fcn_chapter' &&
      get_option( 'fictioneer_enable_tts' ) &&
      ! is_archive() &&
      ! is_search() &&
      ! $password_required
    ) {
      wp_enqueue_script( 'fictioneer-tts-scripts' );
    }

    // Enqueue story
    if ( ( $post_type == 'fcn_story' || is_page_template( 'singular-story.php' ) ) && ! is_archive() && ! is_search() ) {
      wp_enqueue_script( 'fictioneer-story-scripts' );
    }

    // Enqueue user profile + follows + checkmarks + reminders
    if ( is_user_logged_in() || get_option( 'fictioneer_enable_ajax_authentication' ) ) {
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

        if ( get_option( 'fictioneer_enable_css_skins' ) ) {
          wp_enqueue_script( 'fictioneer-css-skins-scripts' );
        }
      }
    }

    // Enqueue bookmarks
    if ( get_option( 'fictioneer_enable_bookmarks' ) ) {
      wp_enqueue_script( 'fictioneer-bookmarks-scripts' );
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
  } else {
    // Complete
    wp_enqueue_script( 'fictioneer-complete-scripts', get_template_directory_uri() . '/js/complete.min.js', ['fictioneer-dynamic-scripts'], $cache_bust, $strategy );
  }

  // Enqueue WordPress comment-reply
  if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
    wp_enqueue_script( 'comment-reply' );
  }

  // Enqueue Splide
  if (
    get_option( 'fictioneer_enable_global_splide' ) ||
    ( $post && preg_match( '/\[fictioneer_[a-zA-Z0-9_]*[^\]]*splide=["\']([^"\']+)["\'][^\]]*\]/', $post->post_content ) ) ||
    strpos( $_SERVER['REQUEST_URI'], 'elementor' ) !== false // Accounts for page editors like Elementor
  ) {
    wp_enqueue_script( 'fictioneer-splide', get_template_directory_uri() . '/js/splide.min.js', [], $cache_bust, false );
  }

  // DEV Utilities
  if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
    wp_register_script( 'fictioneer-dev-scripts', get_template_directory_uri() . '/js/dev-tools.min.js', [], $cache_bust, $strategy );
    wp_enqueue_script( 'fictioneer-dev-scripts' );
  }
}
add_action( 'wp_enqueue_scripts', 'fictioneer_add_custom_scripts' );

/**
 * Exclude theme scripts from Jetpack Boost
 *
 * @since 5.24.1
 * @since 5.24.2 - Remove filter if Jetpack is not running.
 *
 * @param string $tag     The <script> tag for the enqueued script.
 * @param string $handle  The script’s registered handle.
 *
 * @return string The filtered <script> tag.
 */

function fictioneer_data_jetpack_boost_tag( $tag, $handle ) {
  if ( ! fictioneer_is_plugin_active( 'jetpack/jetpack.php' ) ) {
    remove_filter( 'script_loader_tag', 'fictioneer_data_jetpack_boost_tag' );

    return $tag;
  }

  if ( strpos( $handle, 'fictioneer-' ) !== 0 ) {
    return $tag;
  }

  return str_replace( ' src', ' data-jetpack-boost="ignore" src', $tag );
}
add_filter( 'script_loader_tag', 'fictioneer_data_jetpack_boost_tag', 10, 2 );

/**
 * Enqueue block editor scripts
 *
 * @since 5.6.0
 */

function fictioneer_enqueue_block_editor_scripts() {
  // Setup
  $current_screen = get_current_screen();

  if ( $current_screen->base === 'post' ) {
    $current_user = wp_get_current_user();

    wp_register_script(
      'fictioneer-block-editor-scripts',
      get_template_directory_uri() . '/js/block-editor.min.js',
      ['wp-dom-ready', 'wp-edit-post', 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-data', 'jquery'],
      fictioneer_get_cache_bust(),
      true
    );

    wp_enqueue_script( 'fictioneer-block-editor-scripts' );

    wp_localize_script( 'fictioneer-block-editor-scripts', 'fictioneerData', array(
      'userCapabilities' => $current_user->allcaps
    ));
  }
}
add_action( 'enqueue_block_editor_assets', 'fictioneer_enqueue_block_editor_scripts' );

/**
 * Enqueue customizer scripts
 *
 * @since 5.12.0
 */

function fictioneer_enqueue_customizer_scripts() {
  wp_enqueue_script(
    'fictioneer-customizer-scripts',
    get_template_directory_uri() . '/js/customizer.min.js',
    ['jquery', 'customize-preview'],
    fictioneer_get_cache_bust(),
    true
  );

  wp_localize_script( 'fictioneer-customizer-scripts', 'fictioneerData', array(
    'confirmationDialog' => __( 'Are you sure?', 'fictioneer' )
  ));
}
add_action( 'customize_controls_enqueue_scripts', 'fictioneer_enqueue_customizer_scripts' );

/**
 * Add nonce for Customizer actions
 *
 * @since 5.12.0
 *
 * @param array $nonces  Array of refreshed nonces for save and preview actions.
 *
 * @return array Updated array of nonces.
 */

function fictioneer_add_customizer_refresh_nonces( $nonces ) {
  $nonces['fictioneer-reset-colors'] = wp_create_nonce( 'fictioneer-reset-colors' );

  return $nonces;
}
add_filter( 'customize_refresh_nonces', 'fictioneer_add_customizer_refresh_nonces' );

// =============================================================================
// ADD SCRIPTS TO LOGIN HEAD
// =============================================================================

/**
 * Print scripts to the wp-login-php
 *
 * @since 5.7.3
 * @since 5.26.1 - Use wp_print_inline_script_tag().
 */

function fictioneer_wp_login_scripts() {
  // Clear web storage in preparation of login
  wp_print_inline_script_tag(
    'localStorage.removeItem("fcnUserData");',
    array(
      'id' => 'fictioneer-login-scripts',
      'type' => 'text/javascript',
      'data-jetpack-boost' => 'ignore',
      'data-no-optimize' => '1',
      'data-no-defer' => '1',
      'data-no-minify' => '1'
    )
  );
}
add_action( 'login_head', 'fictioneer_wp_login_scripts' );

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
  return $exclude . ', fonts-base.css, fonts-full.css, bundled-fonts.css, fontawesome';
}
add_filter( 'autoptimize_filter_css_exclude', 'fictioneer_ao_exclude_css' );

/**
 * Exclude scripts from Autoptimize (if installed)
 *
 * @since 5.23.1
 * @link https://github.com/wp-plugins/autoptimize
 *
 * @param string $exclude  List of current excludes.
 *
 * @return string The updated exclusion string.
 */

function fictioneer_ao_exclude_js( $exclude ) {
  return $exclude . ', fictioneer/js/splide.min.js, fictioneer/js/stimulus.umd.min.js';
}
add_filter( 'autoptimize_filter_js_exclude', 'fictioneer_ao_exclude_js' );

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
    <style id="inline-fonts" type="text/css" data-no-optimize="1" data-no-defer="1" data-no-minify="1">@font-face{font-display:swap;font-family:"Open Sans";font-style:normal;font-weight:300;src:local("OpenSans-Light"),url("<?php echo $uri; ?>open-sans/open-sans-v40-300.woff2") format("woff2")}@font-face{font-display:swap;font-family:"Open Sans";font-style:italic;font-weight:300;src:local("OpenSans-LightItalic"),url("<?php echo $uri; ?>open-sans/open-sans-v40-300italic.woff2") format("woff2")}@font-face{font-display:swap;font-family:"Open Sans";font-style:normal;font-weight:400;src:local("OpenSans-Regular"),url("<?php echo $uri; ?>open-sans/open-sans-v40-regular.woff2") format("woff2")}@font-face{font-display:swap;font-family:"Open Sans";font-style:italic;font-weight:400;src:local("OpenSans-Italic"),url("<?php echo $uri; ?>open-sans/open-sans-v40-italic.woff2") format("woff2")}@font-face{font-display:swap;font-family:"Open Sans";font-style:normal;font-weight:500;src:local("OpenSans-Medium"),url("<?php echo $uri; ?>open-sans/open-sans-v40-500.woff2") format("woff2")}@font-face{font-display:swap;font-family:"Open Sans";font-style:italic;font-weight:500;src:local("OpenSans-MediumItalic"),url("<?php echo $uri; ?>open-sans/open-sans-v40-500italic.woff2") format("woff2")}@font-face{font-display:swap;font-family:"Open Sans";font-style:normal;font-weight:600;src:local("OpenSans-SemiBold"),url("<?php echo $uri; ?>open-sans/open-sans-v40-600.woff2") format("woff2")}@font-face{font-display:swap;font-family:"Open Sans";font-style:italic;font-weight:600;src:local("OpenSans-SemiBoldItalic"),url("<?php echo $uri; ?>open-sans/open-sans-v40-600italic.woff2") format("woff2")}@font-face{font-display:swap;font-family:"Open Sans";font-style:normal;font-weight:700;src:local("OpenSans-Bold"),url("<?php echo $uri; ?>open-sans/open-sans-v40-700.woff2") format("woff2")}@font-face{font-display:swap;font-family:"Open Sans";font-style:italic;font-weight:700;src:local("OpenSans-BoldItalic"),url("<?php echo $uri; ?>open-sans/open-sans-v40-700italic.woff2") format("woff2")}</style>
    <?php // <--- End HTML
  }
}

// =============================================================================
// OUTPUT HEAD META
// =============================================================================

/**
 * Output HTML <head> meta
 *
 * @since 5.0.0
 * @since 5.10.0 - Split up for font manager.
 * @since 5.18.1 - No longer pluggable, hooked into wp_head
 */

function fictioneer_output_head_meta() {
  // Start HTML ---> ?>
  <meta charset="<?php echo get_bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
  <meta name="format-detection" content="telephone=no">
  <meta name="theme-color" content="<?php echo '#' . get_background_color(); ?>">
  <meta name="referrer" content="strict-origin-when-cross-origin">
  <?php // <--- End HTML
}
add_action( 'wp_head', 'fictioneer_output_head_meta', 1 );

// =============================================================================
// OUTPUT HEAD FONTS
// =============================================================================

if ( ! function_exists( 'fictioneer_output_head_fonts' ) ) {
  /**
   * Outputs font stylesheets in the <head>
   *
   * Note: This function should be kept pluggable due to legacy reasons.
   *
   * @since 5.10.0
   */

  function fictioneer_output_head_fonts() {
    // Critical path fonts
    fictioneer_output_critical_fonts();

    // Setup
    $bundled_fonts = fictioneer_get_theme_cache_dir( 'output_head_fonts' ) . '/bundled-fonts.css';
    $last_built_timestamp = get_option( 'fictioneer_bundled_fonts_timestamp', '123456789' );
    $cache_bust = "?timestamp={$last_built_timestamp}";
    $loading_pattern = fictioneer_get_async_css_loading_pattern();

    // Create file if it does not exist
    if ( ! file_exists( $bundled_fonts ) ) {
      fictioneer_build_bundled_fonts();
    }

    // Output font stylesheets...
    if ( file_exists( $bundled_fonts ) ) {
      // ... base and custom
      $custom_fonts_href = fictioneer_get_theme_cache_uri( 'output_head_fonts' ) . '/bundled-fonts.css' . $cache_bust;

      // Start HTML ---> ?>
      <link rel="stylesheet" id="fictioneer-bundled-fonts-stylesheet" href="<?php echo $custom_fonts_href; ?>" data-no-optimize="1" data-no-minify="1" <?php echo $loading_pattern; ?>>
      <noscript><link rel="stylesheet" href="<?php echo $custom_fonts_href; ?>"></noscript>
      <?php // <--- End HTML
    } else {
      // ... all theme fonts if something goes wrong
      $full_fonts_href = get_template_directory_uri() . '/css/fonts-full.css' . $cache_bust;

      // Start HTML ---> ?>
      <link rel="stylesheet" href="<?php echo $full_fonts_href; ?>" data-no-optimize="1" data-no-minify="1" <?php echo $loading_pattern; ?>>
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
add_action( 'elementor/editor/after_enqueue_scripts', 'fictioneer_output_head_fonts', 5 );

// =============================================================================
// OUTPUT HEAD ANTI FLICKER
// =============================================================================

/**
 * Outputs script to prevent flickering of layout on page load
 *
 * @since 5.22.1
 * @since 5.26.1 - Use wp_print_inline_script_tag().
 */

function fictioneer_output_head_anti_flicker() {
  // Start HTML ---> ?>
  <style>:root:not(.no-js) body{visibility: hidden;}</style>
  <noscript>
    <style>body {visibility: visible !important;}</style>
  </noscript>
  <?php // <--- End HTML

  wp_print_inline_script_tag(
    'document.addEventListener("readystatechange", () => {if (document.readyState === "interactive") document.body.style.visibility = "visible";});',
    array(
      'id' => 'fictioneer-anti-flicker',
      'type' => 'text/javascript',
      'data-jetpack-boost' => 'ignore',
      'data-no-optimize' => '1',
      'data-no-defer' => '1',
      'data-no-minify' => '1'
    )
  );
}

if ( get_option( 'fictioneer_enable_anti_flicker' ) ) {
  add_action( 'wp_head', 'fictioneer_output_head_anti_flicker' );
}

// =============================================================================
// OUTPUT CRITICAL SCRIPTS
// =============================================================================

/**
 * Outputs critical path scripts in the <head>
 *
 * Critical path scripts executed in the <head> before the rest of the DOM
 * is loaded. This is necessary for the light/dark switch and site settings
 * to work without causing color flickering or layout shifts. This is achieved
 * by adding configuration classes directly into the <html> root node, which
 * is the only one available at this point.
 *
 * @since 5.0.0
 * @since 5.18.1 - No longer pluggable, hooked into wp_head
 * @since 5.26.1 - Use wp_print_inline_script_tag().
 */

function fictioneer_output_head_critical_scripts() {
  wp_print_inline_script_tag(
    '!function(){if("undefined"!=typeof localStorage){const e=localStorage.getItem("fcnLightmode"),t=document.documentElement;let a,o=localStorage.getItem("fcnSiteSettings");if(o&&(o=JSON.parse(o))&&null!==o&&"object"==typeof o){Object.entries(o).forEach((([e,s])=>{switch(e){case"minimal":t.classList.toggle("minimal",s);break;case"taxonomies":t.classList.toggle("no-taxonomies",!s);break;case"darken":a=s>=0?1+s**2:1-s**2,t.style.setProperty("--darken",`(${a} + var(--lightness-offset))`);break;case"saturation":case"font-lightness":case"font-saturation":a=s>=0?1+s**2:1-s**2,t.style.setProperty(`--${e}`,`(${a} + var(--${e}-offset))`);break;case"hue-rotate":a=Number.isInteger(o["hue-rotate"])?o["hue-rotate"]:0,t.style.setProperty("--hue-rotate",`(${a}deg + var(--hue-offset))`);break;default:t.classList.toggle(`no-${e}`,!s)}})),t.dataset.fontWeight=o["font-weight"]?o["font-weight"]:"default",t.dataset.theme=o["site-theme"]&&!t.dataset.forceChildTheme?o["site-theme"]:"default";let e=getComputedStyle(document.documentElement).getPropertyValue("--theme-color-base").trim().split(" ");const s=o.darken?o.darken:0,r=o.saturation?o.saturation:0,n=o["hue-rotate"]?o["hue-rotate"]:0,l=s>=0?1+s**2:1-s**2;o=r>=0?1+r**2:1-r**2,e=`hsl(${(parseInt(e[0])+n)%360}deg ${(parseInt(e[1])*o).toFixed(2)}% ${(parseInt(e[2])*l).toFixed(2)}%)`,document.querySelector("meta[name=theme-color]").setAttribute("content",e)}e&&(t.dataset.mode="true"==e?"light":"dark")}}(),document.documentElement.classList.remove("no-js");',
    array(
      'id' => 'fictioneer-critical-scripts',
      'type' => 'text/javascript',
      'data-jetpack-boost' => 'ignore',
      'data-no-optimize' => '1',
      'data-no-defer' => '1',
      'data-no-minify' => '1'
    )
  );
}
add_action( 'wp_head', 'fictioneer_output_head_critical_scripts', 9999 );

/**
 * Outputs critical skin scripts in the <head>
 *
 * @since 5.26.0
 * @since 5.26.1 - Use wp_print_inline_script_tag().
 */

function fictioneer_output_critical_skin_scripts() {
  wp_print_inline_script_tag(
    '!function(){const e="fcnLoggedIn=",t=document.cookie.split(";");let n=null;for(var c=0;c<t.length;c++){const i=t[c].trim();if(0==i.indexOf(e)){n=decodeURIComponent(i.substring(12,i.length));break}}if(!n)return;const i=JSON.parse(localStorage.getItem("fcnSkins"))??{data:{},active:null,fingerprint:n};if(i?.data?.[i.active]?.css&&n===i?.fingerprint){const e=document.createElement("style");e.textContent=i.data[i.active].css,e.id="fictioneer-active-custom-skin",document.querySelector("head").appendChild(e)}}();',
    array(
      'id' => 'fictioneer-skin-scripts',
      'type' => 'text/javascript',
      'data-jetpack-boost' => 'ignore',
      'data-no-optimize' => '1',
      'data-no-defer' => '1',
      'data-no-minify' => '1'
    )
  );
}

if ( get_option( 'fictioneer_enable_css_skins' ) ) {
  add_action( 'wp_head', 'fictioneer_output_critical_skin_scripts', 9999 );
}

// =============================================================================
// STIMULUS
// =============================================================================

/**
 * Outputs Stimulus in <head>
 *
 * @since 5.27.0
 */

function fictioneer_output_stimulus() {
  $link = get_template_directory_uri() . '/js/stimulus.umd.min.js?bust=' . fictioneer_get_cache_bust();

  // Start HTML ---> ?>
  <script id="fictioneer-stimulus-umd" src="<?php echo esc_url( $link ); ?>" type="text/javascript" data-jetpack-boost="ignore" data-no-optimize="1" data-no-defer="1" data-no-minify="1"></script>
  <script id="fictioneer-stimulus-setup" type="text/javascript" data-jetpack-boost="ignore" data-no-optimize="1" data-no-defer="1" data-no-minify="1">const application = Stimulus.Application.start();</script>
  <?php // <--- End HTML
}
add_action( 'wp_head', 'fictioneer_output_stimulus', 5 );

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
 * @since 5.27.4 - Added guard clauses and link on chapter edit screen.
 *
 * @param WP_Admin_Bar $wp_admin_bar  The WP_Admin_Bar instance, passed by reference.
 */

function fictioneer_adminbar_add_chapter_link( $wp_admin_bar ) {
  $post_id = get_the_ID();

  if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
    return;
  }

  // Story edit screen
  if ( ( is_single() && get_post_type() === 'fcn_story' ) || ( is_admin() && get_current_screen()->id === 'fcn_story' ) ) {
    $story_post = get_post( $post_id );

    if (
      $story_post &&
      ! in_array( $story_post->post_status, ['draft', 'auto-draft', 'trash'] ) &&
      $story_post->post_author == get_current_user_id()
    ) {
      $wp_admin_bar->add_node(
        array(
          'id' => 'fictioneer-add-chapter',
          'title' => '<span class="ab-icon dashicons dashicons-text-page" style="top: 4px; font-size: 16px;"></span> ' . __( 'Add Chapter', 'fictioneer' ),
          'href' => admin_url( 'post-new.php?post_type=fcn_chapter&story_id=' . $post_id ),
          'meta' => array( 'class' => 'adminbar-add-chapter' )
        )
      );
    }
  }

  // Chapter edit screen
  if ( is_admin() && get_current_screen()->id === 'fcn_chapter' ) {
    $story_id = fictioneer_get_chapter_story_id( $post_id );
    $story_post = $story_id ? get_post( $story_id ) : null;

    if ( $story_post && $story_post->post_author == get_current_user_id() ) {
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

// =============================================================================
// CUSTOMIZATION
// =============================================================================

/**
 * Adds '--this-flip' (0|1) to card style attribute
 *
 * @since 5.12.1
 *
 * @param array $attributes  Card attributes to be rendered.
 *
 * @return array Updated attributes.
 */

function fictioneer_add_flip_property_to_cards( $attributes ) {
  $attributes['style'] = isset( $attributes['style'] ) ? $attributes['style'] : '';
  $attributes['style'] .= ' --this-flip: ' . rand( 0, 1 ) * 2 - 1 . ';';

  return $attributes;
}

if ( get_theme_mod( 'card_frame', 'default' ) === 'stacked_random' ) {
  add_filter( 'fictioneer_filter_card_attributes', 'fictioneer_add_flip_property_to_cards' );
}

// =============================================================================
// SCRIPT TRANSLATIONS
// =============================================================================

/**
 * Returns array with translations to be used as JSON
 *
 * @since 5.12.2
 *
 * @return array Updated attributes.
 */

function fictioneer_get_js_translations() {
  return array(
    'notification' => array(
      'enterPageNumber' => _x( 'Enter page number:', 'Pagination jump prompt.', 'fictioneer' ),
      'slowDown' => _x( 'Slow down.', 'Rate limit reached notification.', 'fictioneer' ),
      'error' => _x( 'Error', 'Generic error notification.', 'fictioneer' ),
      'suggestionAppendedToComment' => __( 'Suggestion appended to comment!<br><a style="font-weight: 700;" href="#comments">Go to comment section.</a>', 'fictioneer' ),
      'quoteAppendedToComment' => __( 'Quote appended to comment!<br><a style="font-weight: 700;" href="#comments">Go to comment section.</a>', 'fictioneer' ),
      'linkCopiedToClipboard' => __( 'Link copied to clipboard!', 'fictioneer' ),
      'copiedToClipboard' => __( 'Copied to clipboard!', 'fictioneer' ),
      'oauthEmailTaken' => __( 'The associated email address is already taken. You can link additional accounts in your profile.', 'fictioneer' ),
      'oauthAccountAlreadyLinked' => __( 'Account already linked to another profile.', 'fictioneer' ),
      'oauthNew' => __( 'Your account has been successfully linked. <strong>Hint:</strong> You can change your display name in your profile and link additional accounts.', 'fictioneer' ),
      'oauthAccountLinked' => __( 'Account has been successfully linked.', 'fictioneer' )
    ),
    'partial' => array(
      'quoteFragmentPrefix' => _x( '[…] ', 'Prefix for partial quotes', 'fictioneer' ),
      'quoteFragmentSuffix' => _x( ' […]', 'Suffix for partial quotes', 'fictioneer' )
    )
  );
}

/**
 * Outputs JSON with translation into the <head>
 *
 * @since 5.12.2
 * @since 5.26.1 - Use wp_print_inline_script_tag().
 */

function fictioneer_output_head_translations() {
  wp_print_inline_script_tag(
    'const fictioneer_tl = ' . json_encode( fictioneer_get_js_translations() ) . ';',
    array(
      'id' => 'fictioneer-translations',
      'type' => 'text/javascript',
      'data-jetpack-boost' => 'ignore',
      'data-no-optimize' => '1',
      'data-no-defer' => '1',
      'data-no-minify' => '1'
    )
  );
}
add_action( 'wp_head', 'fictioneer_output_head_translations' );

// =============================================================================
// STORY REDIRECT
// =============================================================================

/**
 * Directs story if a redirect link is set
 *
 * @since 5.14.0
 */

function fictioneer_redirect_story() {
  global $post;

  // Abort if...
  if ( ! is_single() || get_post_type( $post ) !== 'fcn_story' ) {
    return;
  }

  // Setup
  $redirect = get_post_meta( $post->ID ?? 0, 'fictioneer_story_redirect_link', true );

  if ( $redirect ) {
    wp_redirect( $redirect, 301 );
    exit;
  }
}
add_action( 'template_redirect', 'fictioneer_redirect_story' );

// =============================================================================
// WP-SIGNUP AND WP-ACTIVATE
// =============================================================================

/**
 * Remove default styles of wp-signup.php and wp-activate.php
 *
 * @since 5.18.1
 */

function fictioneer_remove_mu_registration_styles() {
  remove_action( 'wp_head', 'wpmu_signup_stylesheet' );
  remove_action( 'wp_head', 'wpmu_activate_stylesheet' );
}

/**
 * Output styles for wp-signup.php and wp-activate.php
 *
 * @since 5.18.1
 */

function fictioneer_output_mu_registration_style() {
  // Start HTML ---> ?>
  <style type="text/css">
    :is(.wp-signup-container, .wp-activate-container) label[for] {
      display: block;
      color: var(--fg-500);
      font-size: var(--fs-xs);
      font-weight: var(--font-weight-medium);
      padding: 0 0 2px;
    }
    :is(.wp-signup-container, .wp-activate-container) :where(input[type="text"], input[type="email"], input[type="password"], input[type="tel"]) {
      display: block;
      width: 100%;
    }
    :is(.wp-signup-container, .wp-activate-container) p {
      margin-bottom: 1.5rem;
    }
    :is(.wp-signup-container, .wp-activate-container) p[id*="-description"] {
      color: var(--fg-900);
      font-size: var(--fs-xxs);
      line-height: 1.3;
      padding: .5rem 2px 0;
    }
    :is(.wp-signup-container, .wp-activate-container) p[id*="-error"] {
      display: flex;
      align-items: flex-start;
      gap: .5rem;
      background: var(--notice-warning-background);
      color: var(--notice-warning-color);
      font-size: var(--fs-xs);
      font-weight: 400;
      line-height: 1.3;
      letter-spacing: 0;
      padding: .5rem;
      margin: .5rem 0 1rem;
      border-radius: var(--layout-border-radius-small);
    }
  </style>
  <?php // <--- End HTML
}

if ( FICTIONEER_MU_REGISTRATION ) {
  add_action( 'wp_head', 'fictioneer_remove_mu_registration_styles', 1 );
  add_action( 'wp_head', 'fictioneer_output_mu_registration_style' );
}

// =============================================================================
// ADD LINK TO THEME SETTINGS IN ADMIN BAR
// =============================================================================

/**
 * Add theme settings link to admin bar
 *
 * @since 5.19.1
 *
 * @param WP_Admin_Bar $wp_admin_bar  The WP_Admin_Bar instance, passed by reference.
 */

function fictioneer_adminbar_add_theme_settings_link( $wp_admin_bar ) {
  if ( ! current_user_can( 'manage_options' ) || is_admin() ) {
    return;
  }

  $args = array(
    'id' => 'fictioneer_theme_settings',
    'parent' => 'site-name',
    'title' => __( 'Fictioneer', 'fictioneer' ),
    'href' => esc_url( admin_url( 'admin.php?page=fictioneer' ) ),
    'meta' => array(
      'title' => __( 'Fictioneer Theme Settings', 'fictioneer' )
    )
  );

  $wp_admin_bar->add_node( $args );
}
add_action( 'admin_bar_menu', 'fictioneer_adminbar_add_theme_settings_link', 999 );

// =============================================================================
// ELEMENTOR (IF YOU ABSOLUTELY HAVE TO)
// =============================================================================

/**
 * Register Elementor locations
 *
 * @since 5.20.0
 *
 * @param object $elementor_theme_manager  The Elementor manager object.
 */

function fictioneer_elementor_register_locations( $elementor_theme_manager ) {
  $elementor_theme_manager->register_location( 'header' );
  $elementor_theme_manager->register_location( 'footer' );

  $elementor_theme_manager->register_location(
    'nav_bar',
    array(
      'label' => esc_html__( 'Navigation Bar', 'fictioneer' ),
      'multiple' => false,
      'edit_in_content' => true
    )
  );

  $elementor_theme_manager->register_location(
    'nav_menu',
    array(
      'label' => esc_html__( 'Navigation Menu', 'fictioneer' ),
      'multiple' => false,
      'edit_in_content' => true
    )
  );

  $elementor_theme_manager->register_location(
    'mobile_nav_menu',
    array(
      'label' => esc_html__( 'Mobile Navigation Menu', 'fictioneer' ),
      'multiple' => false,
      'edit_in_content' => true
    )
  );

  $elementor_theme_manager->register_location(
    'story_header',
    array(
      'label' => esc_html__( 'Story Header', 'fictioneer' ),
      'multiple' => false,
      'edit_in_content' => true
    )
  );

  $elementor_theme_manager->register_location(
    'page_background',
    array(
      'label' => esc_html__( 'Page Background', 'fictioneer' ),
      'multiple' => false,
      'edit_in_content' => false
    )
  );
}

/**
 * Adds override frontend styles for Elementor
 *
 * @since 5.20.0
 */

function fictioneer_elementor_override_styles() {
  // Dummy style
  wp_register_style( 'fictioneer-elementor-override', false );
  wp_enqueue_style( 'fictioneer-elementor-override', false );

  // Setup
  $kit_id = get_option( 'elementor_active_kit' );
  $css = "body.elementor-kit-{$kit_id} {
    --e-global-color-primary: var(--primary-500);
    --e-global-color-secondary: var(--fg-300);
    --e-global-color-text: var(--fg-500);
    --e-global-color-accent: var(--fg-700);
    --swiper-pagination-color: var(--fg-700);
    --swiper-pagination-bullet-inactive-opacity: .25;
  }";

  // Output
  wp_add_inline_style( 'fictioneer-elementor-override', fictioneer_minify_css( $css ) );
}

add_action(
  'wp',
  function() {
    if ( fictioneer_is_plugin_active( 'elementor/elementor.php' ) ) {
      add_action( 'elementor/theme/register_locations', 'fictioneer_elementor_register_locations' );
      add_action( 'wp_enqueue_scripts', 'fictioneer_elementor_override_styles', 9999 );
    }
  }
);

/**
 * Adds override editor styles for Elementor
 *
 * @since 5.20.0
 */

function fictioneer_override_elementor_editor_styles() {
  // Dummy style
  wp_register_style( 'fictioneer-elementor-editor-override', false );
  wp_enqueue_style( 'fictioneer-elementor-editor-override', false );

  // Setup
  $css = '
    body {
      --primary-500: ' . fictioneer_get_theme_color( 'light_primary_500' ) . ';
      --fg-300: ' . fictioneer_get_theme_color( 'light_fg_300' ) . ';
      --fg-500: ' . fictioneer_get_theme_color( 'light_fg_500' ) . ';
      --fg-700: ' . fictioneer_get_theme_color( 'light_fg_700' ) . ';
    }

    .e-global__color[data-global-id="primary"] .e-global__color-preview-color {
      background-color: var(--primary-500) !important;
    }

    .e-global__color[data-global-id="primary"] .e-global__color-title::after {
      content: " ' . _x( '(--primary-500)', 'Elementor color override hint.', 'fictioneer' ) . '";
    }

    .e-global__popover-toggle--active + .pickr .pcr-button[style="--pcr-color: rgba(110, 193, 228, 1);"]::after {
      background: var(--primary-500) !important;
    }

    .e-global__color[data-global-id="secondary"] .e-global__color-preview-color {
      background-color: var(--fg-300) !important;
    }

    .e-global__color[data-global-id="secondary"] .e-global__color-title::after {
      content: " ' . _x( '(--fg-300)', 'Elementor color override hint.', 'fictioneer' ) . '";
    }

    .e-global__popover-toggle--active + .pickr .pcr-button[style="--pcr-color: rgba(84, 89, 95, 1);"]::after {
      background: var(--fg-300) !important;
    }

    .e-global__color[data-global-id="text"] .e-global__color-preview-color {
      background-color: var(--fg-500) !important;
    }

    .e-global__color[data-global-id="text"] .e-global__color-title::after {
      content: " ' . _x( '(--fg-500)', 'Elementor color override hint.', 'fictioneer' ) . '";
    }

    .e-global__popover-toggle--active + .pickr .pcr-button[style="--pcr-color: rgba(122, 122, 122, 1);"]::after {
      background: var(--fg-500) !important;
    }

    .e-global__color[data-global-id="accent"] .e-global__color-preview-color {
      background-color: var(--fg-700) !important;
    }

    .e-global__color[data-global-id="accent"] .e-global__color-title::after {
      content: " ' . _x( '(--fg-700)', 'Elementor color override hint.', 'fictioneer' ) . '";
    }

    .e-global__popover-toggle--active + .pickr .pcr-button[style="--pcr-color: rgba(97, 206, 112, 1);"]::after {
      background: var(--fg-700) !important;
    }

    .e-global__color .e-global__color-hex {
      display: none;
    }
  ';

  // Output
  wp_add_inline_style( 'fictioneer-elementor-editor-override', fictioneer_minify_css( $css ) );
}
add_action( 'elementor/editor/after_enqueue_styles', 'fictioneer_override_elementor_editor_styles', 9999 );

/**
 * Adds Fictioneer font group
 *
 * @since 5.20.0
 *
 * @param array $groups  Array of font groups.
 *
 * @return array Updated font groups.
 */

function fictioneer_elementor_add_font_group( $groups ) {
  $new_groups = array(
    'fictioneer' => __( 'Fictioneer', 'fictioneer' )
  );

  return array_merge( $new_groups, $groups );
}
add_filter( 'elementor/fonts/groups', 'fictioneer_elementor_add_font_group' );

/**
 * Adds Fictioneer fonts to font group
 *
 * @since 5.20.0
 *
 * @param array $fonts  Array of fonts.
 *
 * @return array Updated fonts.
 */

function fictioneer_elementor_add_additional_fonts( $fonts ) {
  $theme_fonts = fictioneer_get_font_data();

  foreach ( $theme_fonts as $font ) {
    if ( $font['family'] ?? 0 ) {
      $fonts[ $font['family'] ] = 'fictioneer';
    }
  }

  return $fonts;
}
add_filter( 'elementor/fonts/additional_fonts', 'fictioneer_elementor_add_additional_fonts' );

// =============================================================================
// GET COLORS JSON
// =============================================================================

/**
 * Returns associative array of theme colors
 *
 * Notes: Considers both parent and child theme.
 *
 * @since 5.21.2
 *
 * @return array Associative array of theme colors.
 */

function fictioneer_get_theme_colors_array() {
  static $fictioneer_colors = null;

  if ( $fictioneer_colors !== null ) {
    return $fictioneer_colors;
  }

  // Setup
  $parent_colors = [];
  $child_colors = [];

  // Get parent theme colors
  $parent_colors_file = get_template_directory() . '/includes/colors.json';

  if ( file_exists( $parent_colors_file ) ) {
    $parent_colors_content = file_get_contents( $parent_colors_file );
    $parent_colors = json_decode( $parent_colors_content, true );

    if ( ! is_array( $parent_colors ) ) {
      $parent_colors = [];
    }
  }

  // Get child theme colors
  $child_colors_file = get_stylesheet_directory() . '/includes/colors.json';

  if ( file_exists( $child_colors_file ) ) {
    $child_colors_content = file_get_contents( $child_colors_file );
    $child_colors = json_decode( $child_colors_content, true );

    if ( ! is_array( $child_colors ) ) {
      $child_colors = [];
    }
  }

  // Update static cache
  $fictioneer_colors = array_merge( $parent_colors, $child_colors );

  // Merge and return colors, child overriding parent
  return $fictioneer_colors;
}

// =============================================================================
// CALENDAR BLOCK
// =============================================================================

/**
 * Changes the navigation icons of the calendar block
 *
 * @since 5.22.0
 *
 * @param string $calendar_output  HTML of the calendar block.
 *
 * @return array Updated HTML of the calendar block.
 */

function fictioneer_modify_calender_nav( $calendar_output ) {
  // Replace previous icon
  $calendar_output = str_replace( '>&laquo;', '><i class="fa-solid fa-caret-left"></i>', $calendar_output );

  // Replace next icon
  $calendar_output = str_replace( '&raquo;<', '<i class="fa-solid fa-caret-right"></i><', $calendar_output );

  return $calendar_output;
}
add_filter( 'get_calendar', 'fictioneer_modify_calender_nav' );

// =============================================================================
// DEFAULT STORY THUMBNAIL
// =============================================================================

/**
 * Returns default cover image (if any)
 *
 * Note: Exclude from editor to prevent the default to be
 * set as actual post thumbnail!
 *
 * @since 5.23.0
 *
 * @param int         $thumbnail_id  Post thumbnail ID.
 * @param int|WP_Post $post          Post ID or WP_Post object. Default is global $post.
 *
 * @return int Post thumbnail ID (which can be 0 if the thumbnail is not set),
 *             or false if the post does not exist.
 */

function fictioneer_default_cover( $thumbnail_id, $post ) {
  if ( $thumbnail_id || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
    return $thumbnail_id;
  }

  if ( $post->post_type === 'fcn_story' ) {
    return get_theme_mod( 'default_story_cover', 0 );
  }

  return $thumbnail_id;
}

if ( ! is_admin() && get_theme_mod( 'default_story_cover' ) ) {
  add_filter( 'post_thumbnail_id', 'fictioneer_default_cover', 10, 2 );
}

// =============================================================================
// DISABLE INCOMPATIBLE PLUGINS
// =============================================================================

/**
 * Disables the incompatible Page Optimize plugin
 *
 * @since 5.24.1
 */

function fictioneer_disable_page_optimize_plugin() {
  if ( is_plugin_active( 'page-optimize/page-optimize.php' ) ) {
    deactivate_plugins( 'page-optimize/page-optimize.php' );
    add_action( 'admin_notices', 'show_page_optimize_deactivated_notice' );
  }
}
add_action( 'admin_init', 'fictioneer_disable_page_optimize_plugin' );

/**
 * Shows notice when the Page Optimize plugin is disabled
 *
 * @since 5.24.1
 */

function show_page_optimize_deactivated_notice() {
  wp_admin_notice(
    __( 'The Page Optimize plugin has been disabled due to compatibility issues.', 'fictioneer' ),
    array(
      'type' => 'info',
      'dismissible' => true,
      'additional_classes' => ['fictioneer-disabled-plugin-notice']
    )
  );
}

// =============================================================================
// LOGIN CHECK COOKIE
// =============================================================================

/**
 * Sets the fictioneer login check cookie
 *
 * @since 5.26.0
 * @link https://developer.wordpress.org/reference/functions/wp_set_auth_cookie/
 *
 * @param string $logged_in_cookie  The logged-in cookie value.
 * @param int    $expire            The time the login grace period expires as a UNIX timestamp.
 *                                  Default is 12 hours past the cookie's expiration time.
 * @param int    $expiration        The time when the logged-in authentication cookie expires as
 *                                  a UNIX timestamp. Default is 14 days from now.
 * @param int    $user_id           User ID.
 */

function fictioneer_set_logged_in_cookie( $logged_in_cookie, $expire, $expiration, $user_id ) {
  $cookie_domain = defined( 'COOKIE_DOMAIN' ) ? COOKIE_DOMAIN : $_SERVER['HTTP_HOST'];
  $fingerprint = fictioneer_get_user_fingerprint( $user_id );

  setcookie( 'fcnLoggedIn', $fingerprint, $expire, COOKIEPATH, $cookie_domain, is_ssl(), false );
}
add_action( 'set_logged_in_cookie', 'fictioneer_set_logged_in_cookie', 10, 4 );

/**
 * Removes the fictioneer login check cookie
 *
 * @since 5.26.0
 */

function fictioneer_remove_logged_in_cookie() {
  $cookie_domain = defined( 'COOKIE_DOMAIN' ) ? COOKIE_DOMAIN : $_SERVER['HTTP_HOST'];

  setcookie( 'fcnLoggedIn', '', time() - 3600, COOKIEPATH, $cookie_domain, is_ssl(), false );
}
add_action( 'wp_logout', 'fictioneer_remove_logged_in_cookie' );

/**
 * Cleans up the fictioneer login check cookie
 *
 * @since 5.26.0
 */

function fictioneer_fix_logged_in_cookie() {
  $user_id = get_current_user_id();

  if ( ! $user_id && isset( $_COOKIE['fcnLoggedIn'] ) ) {
    fictioneer_remove_logged_in_cookie();
  }

  if ( $user_id && ! isset( $_COOKIE['fcnLoggedIn'] ) ) {
    // Unknown how long WP login cookie still lasts, so just take an hour
    fictioneer_set_logged_in_cookie( null, time() + HOUR_IN_SECONDS, null, $user_id );
  }
}
add_action( 'init', 'fictioneer_fix_logged_in_cookie' );
