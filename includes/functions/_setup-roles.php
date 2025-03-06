<?php

// =============================================================================
// SETUP CAPABILITIES
// =============================================================================

define(
  'FICTIONEER_BASE_CAPABILITIES',
  array(
    'fcn_read_others_files',
    'fcn_edit_others_files',
    'fcn_delete_others_files',
    'fcn_select_page_template',
    'fcn_admin_panel_access',
    'fcn_adminbar_access',
    'fcn_dashboard_access',
    'fcn_privacy_clearance',
    'fcn_shortcodes',
    'fcn_simple_comment_html',
    'fcn_custom_page_header',
    'fcn_custom_page_css',
    'fcn_custom_epub_css',
    'fcn_custom_epub_upload',
    'fcn_seo_meta',
    'fcn_make_sticky',
    'fcn_show_badge',
    'fcn_edit_permalink',
    'fcn_all_blocks',
    'fcn_story_pages',
    'fcn_edit_date',
    'fcn_assign_patreon_tiers',
    'fcn_moderate_post_comments',
    'fcn_ignore_post_passwords',
    'fcn_ignore_page_passwords',
    'fcn_ignore_fcn_story_passwords',
    'fcn_ignore_fcn_chapter_passwords',
    'fcn_ignore_fcn_collection_passwords',
    'fcn_unlock_posts',
    'fcn_expire_passwords',
    'fcn_crosspost'
  )
);

define(
  'FICTIONEER_TAXONOMY_CAPABILITIES',
  array(
    // Categories
    'manage_categories',
    'edit_categories',
    'delete_categories',
    'assign_categories',
    // Tags
    'manage_post_tags',
    'edit_post_tags',
    'delete_post_tags',
    'assign_post_tags',
    // Genres
    'manage_fcn_genres',
    'edit_fcn_genres',
    'delete_fcn_genres',
    'assign_fcn_genres',
    // Fandoms
    'manage_fcn_fandoms',
    'edit_fcn_fandoms',
    'delete_fcn_fandoms',
    'assign_fcn_fandoms',
    // Characters
    'manage_fcn_characters',
    'edit_fcn_characters',
    'delete_fcn_characters',
    'assign_fcn_characters',
    // Warnings
    'manage_fcn_content_warnings',
    'edit_fcn_content_warnings',
    'delete_fcn_content_warnings',
    'assign_fcn_content_warnings'
  )
);

/**
 * Initialize user roles if not already done
 *
 * @since 5.6.0
 *
 * @param boolean $force  Optional. Whether to force initialization.
 */

function fictioneer_initialize_roles( $force = false ) {
  // Only do this once...
  $administrator = get_role( 'administrator' );

  // If this capability is missing, the roles have not yet been initialized
  if (
    ( $administrator && ! in_array( 'fcn_edit_date', array_keys( $administrator->capabilities ) ) ) ||
    $force
  ) {
    fictioneer_setup_roles();
  }

  // If this capability is missing, the roles need to be updated
  if ( $administrator && ! in_array( 'fcn_crosspost', array_keys( $administrator->capabilities ) ) ) {
    get_role( 'administrator' )->add_cap( 'fcn_custom_page_header' );
    get_role( 'administrator' )->add_cap( 'fcn_custom_epub_upload' );
    get_role( 'administrator' )->add_cap( 'fcn_unlock_posts' );
    get_role( 'administrator' )->add_cap( 'fcn_expire_passwords' );
    get_role( 'administrator' )->add_cap( 'fcn_crosspost' );

    if ( $editor = get_role( 'editor' ) ) {
      $editor->add_cap( 'fcn_custom_page_header' );
      $editor->add_cap( 'fcn_custom_epub_upload' );
    }

    if ( $moderator = get_role( 'fcn_moderator' ) ) {
      $moderator->add_cap( 'fcn_only_moderate_comments' );
      $moderator->add_cap( 'fcn_custom_epub_upload' );
    }

    if ( $author = get_role( 'author' ) ) {
      $author->add_cap( 'fcn_custom_epub_upload' );
    }
  }
}
add_action( 'admin_init', 'fictioneer_initialize_roles' );

/**
 * Build user roles with custom capabilities
 *
 * @since 5.6.0
 */

function fictioneer_setup_roles() {
  // Capabilities
  $all = array_merge(
    FICTIONEER_BASE_CAPABILITIES,
    FICTIONEER_TAXONOMY_CAPABILITIES,
    FICTIONEER_STORY_CAPABILITIES,
    FICTIONEER_CHAPTER_CAPABILITIES,
    FICTIONEER_COLLECTION_CAPABILITIES,
    FICTIONEER_RECOMMENDATION_CAPABILITIES
  );

  // Administrator
  $administrator = get_role( 'administrator' );

  $administrator->remove_cap( 'fcn_only_moderate_comments' );
  $administrator->remove_cap( 'fcn_reduced_profile' );
  $administrator->remove_cap( 'fcn_allow_self_delete' );
  $administrator->remove_cap( 'fcn_upload_limit' );
  $administrator->remove_cap( 'fcn_upload_restrictions' );

  foreach ( $all as $cap ) {
    $administrator->add_cap( $cap );
  }

  // Editor
  $editor = get_role( 'editor' );
  $editor_caps = array_merge(
    // Base
    array(
      'fcn_read_others_files',
      'fcn_edit_others_files',
      'fcn_delete_others_files',
      'fcn_admin_panel_access',
      'fcn_adminbar_access',
      'fcn_dashboard_access',
      'fcn_seo_meta',
      'fcn_make_sticky',
      'fcn_edit_permalink',
      'fcn_all_blocks',
      'fcn_story_pages',
      'fcn_edit_date',
      'fcn_custom_page_header',
      'fcn_custom_epub_upload',
      'moderate_comments',         // Legacy restore
      'edit_comment',              // Legacy restore
      'edit_pages',                // Legacy restore
      'delete_pages',              // Legacy restore
      'delete_published_pages',    // Legacy restore
      'delete_published_posts',    // Legacy restore
      'delete_others_pages',       // Legacy restore
      'delete_others_posts',       // Legacy restore
      'publish_pages',             // Legacy restore
      'publish_posts',             // Legacy restore
      'manage_categories',         // Legacy restore
      'unfiltered_html',           // Legacy restore
      'manage_links',              // Legacy restore
    ),
    FICTIONEER_TAXONOMY_CAPABILITIES,
    FICTIONEER_STORY_CAPABILITIES,
    FICTIONEER_CHAPTER_CAPABILITIES,
    FICTIONEER_COLLECTION_CAPABILITIES,
    FICTIONEER_RECOMMENDATION_CAPABILITIES
  );

  foreach ( $editor_caps as $cap ) {
    $editor->add_cap( $cap );
  }

  // Author
  $author = get_role( 'author' );
  $author_caps = array(
    // Base
    'fcn_admin_panel_access',
    'fcn_adminbar_access',
    'fcn_allow_self_delete',
    'fcn_upload_limit',
    'fcn_upload_restrictions',
    'fcn_story_pages',
    'fcn_custom_epub_upload',
    // Stories
    'read_fcn_story',
    'edit_fcn_stories',
    'publish_fcn_stories',
    'delete_fcn_stories',
    'delete_published_fcn_stories',
    'edit_published_fcn_stories',
    // Chapters
    'read_fcn_chapter',
    'edit_fcn_chapters',
    'publish_fcn_chapters',
    'delete_fcn_chapters',
    'delete_published_fcn_chapters',
    'edit_published_fcn_chapters',
    // Collections
    'read_fcn_collection',
    'edit_fcn_collections',
    'publish_fcn_collections',
    'delete_fcn_collections',
    'delete_published_fcn_collections',
    'edit_published_fcn_collections',
    // Recommendations
    'read_fcn_recommendation',
    'edit_fcn_recommendations',
    'publish_fcn_recommendations',
    'delete_fcn_recommendations',
    'delete_published_fcn_recommendations',
    'edit_published_fcn_recommendations',
    // Taxonomies
    'manage_categories',
    'manage_post_tags',
    'manage_fcn_genres',
    'manage_fcn_fandoms',
    'manage_fcn_characters',
    'manage_fcn_content_warnings',
    'assign_categories',
    'assign_post_tags',
    'assign_fcn_genres',
    'assign_fcn_fandoms',
    'assign_fcn_characters',
    'assign_fcn_content_warnings'
  );

  $author->remove_cap( 'fcn_reduced_profile' );

  foreach ( $author_caps as $cap ) {
    $author->add_cap( $cap );
  }

  // Contributor
  $contributor = get_role( 'contributor' );
  $contributor_caps = array(
    // Base
    'fcn_admin_panel_access',
    'fcn_adminbar_access',
    'fcn_allow_self_delete',
    'fcn_upload_limit',
    'fcn_upload_restrictions',
    'fcn_story_pages',
    // Stories
    'read_fcn_story',
    'edit_fcn_stories',
    'delete_fcn_stories',
    'edit_published_fcn_stories',
    // Chapters
    'read_fcn_chapter',
    'edit_fcn_chapters',
    'delete_fcn_chapters',
    'edit_published_fcn_chapters',
    // Collections
    'read_fcn_collection',
    'edit_fcn_collections',
    'delete_fcn_collections',
    'edit_published_fcn_collections',
    // Recommendations
    'read_fcn_recommendation',
    'edit_fcn_recommendations',
    'delete_fcn_recommendations',
    'edit_published_fcn_recommendations',
    // Taxonomies
    'manage_categories',
    'manage_post_tags',
    'manage_fcn_genres',
    'manage_fcn_fandoms',
    'manage_fcn_characters',
    'manage_fcn_content_warnings',
    'assign_categories',
    'assign_post_tags',
    'assign_fcn_genres',
    'assign_fcn_fandoms',
    'assign_fcn_characters',
    'assign_fcn_content_warnings'
  );

  $contributor->remove_cap( 'fcn_reduced_profile' );

  foreach ( $contributor_caps as $cap ) {
    $contributor->add_cap( $cap );
  }

  // Moderator
  fictioneer_add_moderator_role();

  // Subscriber
  $subscriber = get_role( 'subscriber' );
  $subscriber_caps = array(
    // Base
    'fcn_admin_panel_access',
    'fcn_reduced_profile',
    'fcn_allow_self_delete',
    'fcn_upload_limit',
    'fcn_upload_restrictions',
    // Stories
    'read_fcn_story',
    // Chapters
    'read_fcn_chapter',
    // Collections
    'read_fcn_collection',
    // Recommendations
    'read_fcn_recommendation'
  );

  foreach ( $subscriber_caps as $cap ) {
    $subscriber->add_cap( $cap );
  }
}

/**
 * Add custom moderator role
 *
 * @since 5.0.0
 */

function fictioneer_add_moderator_role() {
  $moderator = get_role( 'fcn_moderator' );

  $caps = array(
    // Base
    'read' => true,
    'edit_posts' => true,
    'edit_others_posts' => true,
    'edit_published_posts' => true,
    'moderate_comments' => true,
    'edit_comment' => true,
    'delete_posts' => true,
    'delete_others_posts' => true,
    'fcn_admin_panel_access' => true,
    'fcn_adminbar_access' => true,
    'fcn_only_moderate_comments' => true,
    'fcn_upload_limit' => true,
    'fcn_upload_restrictions' => true,
    'fcn_show_badge' => true,
    'fcn_story_pages' => true,
    'fcn_custom_epub_upload' => true,
    // Stories
    'read_fcn_story' => true,
    'edit_fcn_stories' => true,
    'publish_fcn_stories' => true,
    'delete_fcn_stories' => true,
    'delete_published_fcn_stories' => true,
    'edit_published_fcn_stories' => true,
    'edit_others_fcn_stories' => true,
    // Chapters
    'read_fcn_chapter' => true,
    'edit_fcn_chapters' => true,
    'publish_fcn_chapters' => true,
    'delete_fcn_chapters' => true,
    'delete_published_fcn_chapters' => true,
    'edit_published_fcn_chapters' => true,
    'edit_others_fcn_chapters' => true,
    // Collections
    'read_fcn_collection' => true,
    'edit_fcn_collections' => true,
    'publish_fcn_collections' => true,
    'delete_fcn_collections' => true,
    'delete_published_fcn_collections' => true,
    'edit_published_fcn_collections' => true,
    'edit_others_fcn_collections' => true,
    // Recommendations
    'read_fcn_recommendation' => true,
    'edit_fcn_recommendations' => true,
    'publish_fcn_recommendations' => true,
    'delete_fcn_recommendations' => true,
    'delete_published_fcn_recommendations' => true,
    'edit_published_fcn_recommendations' => true,
    'edit_others_fcn_recommendations' => true,
    // Taxonomies
    'manage_categories' => true,
    'manage_post_tags' => true,
    'manage_fcn_genres' => true,
    'manage_fcn_fandoms' => true,
    'manage_fcn_characters' => true,
    'manage_fcn_content_warnings',
    'assign_categories' => true,
    'assign_post_tags' => true,
    'assign_fcn_genres' => true,
    'assign_fcn_fandoms' => true,
    'assign_fcn_characters' => true,
    'assign_fcn_content_warnings' => true
  );

  if ( $moderator ) {
    $caps = array_keys( $caps );

    foreach ( $caps as $cap ) {
      $moderator->add_cap( $cap );
    }

    // Already exists
    return null;
  }

  // Add
  return add_role(
    'fcn_moderator',
    __( 'Moderator', 'fictioneer' ),
    $caps
  );
}

// =============================================================================
// APPLY CAPABILITY RULES
// =============================================================================

// Add templates ('name' => 'Display Name') to the array you want to allow
if ( ! defined( 'FICTIONEER_ALLOWED_PAGE_TEMPLATES' ) ) {
  define( 'FICTIONEER_ALLOWED_PAGE_TEMPLATES', [] );
}

/**
 * Exceptions for post passwords
 *
 * @since 5.12.3
 * @since 5.15.0 - Add Patreon checks.
 * @since 5.16.0 - Add Patreon unlock checks and static variable cache.
 *
 * @param bool    $required  Whether the user needs to supply a password.
 * @param WP_Post $post      Post object.
 *
 * @return bool True or false.
 */

function fictioneer_bypass_password( $required, $post ) {
  // Already unlocked
  if ( ! $required ) {
    return $required;
  }

  // Ensure to skip search, list pages, and nested loops
  if (
    ( ! wp_doing_ajax() || ! ( $_GET['post_id'] ?? 0 ) ) &&
    ( $_REQUEST['action'] ?? 0 ) !== 'fictioneer_ajax_submit_comment' &&
    ! ( $_REQUEST['comment_post_ID'] ?? 0 )
  ) {
    if ( ! is_singular() || get_queried_object_id() != $post->ID ) {
      return $required;
    }
  }

  // Static variable cache
  static $cache = [];

  $cache_key = $post->ID . '_' . get_current_user_id() . '_' . (int) $required;

  if ( isset( $cache[ $cache_key ] ) ) {
    return $cache[ $cache_key ];
  }

  // Default
  remove_filter( 'post_password_required', 'fictioneer_bypass_password' );
  $required = post_password_required( $post );
  add_filter( 'post_password_required', 'fictioneer_bypass_password', 10, 2 );

  // Notify cache plugins to NOT cache the page regardless of access
  if ( $required ) {
    fictioneer_disable_caching( 'protected_post' );
  }

  // Always allow admins
  if ( current_user_can( 'manage_options' ) ) {
    $cache[ $cache_key ] = false;

    return false;
  }

  // Setup
  $user = wp_get_current_user();
  $patreon_user_data = fictioneer_get_user_patreon_data( $user->ID ); // Can be an empty array

  // Check capability per post type...
  switch ( $post->post_type ) {
    case 'post':
      $required = current_user_can( 'fcn_ignore_post_passwords' ) ? false : $required;
      break;
    case 'page':
      $required = current_user_can( 'fcn_ignore_page_passwords' ) ? false : $required;
      break;
    case 'fcn_story':
      $required = current_user_can( 'fcn_ignore_fcn_story_passwords' ) ? false : $required;
      break;
    case 'fcn_chapter':
      $required = current_user_can( 'fcn_ignore_fcn_chapter_passwords' ) ? false : $required;
      break;
    case 'fcn_collection':
      $required = current_user_can( 'fcn_ignore_fcn_collection_passwords' ) ? false : $required;
      break;
  }

  // Check Patreon tiers
  if ( $user && $required && get_option( 'fictioneer_enable_patreon_locks' ) && ( $patreon_user_data['valid'] ?? 0 ) ) {
    $patreon_post_data = fictioneer_get_post_patreon_data( $post );

    // If there is anything to check...
    if ( $patreon_post_data['gated'] ) {
      $patreon_check_amount_cents =
        $patreon_post_data['gate_cents'] < 1 ? 999999999999 : $patreon_post_data['gate_cents'];

      $patreon_check_lifetime_amount_cents =
        $patreon_post_data['gate_lifetime_cents'] < 1 ? 999999999999 : $patreon_post_data['gate_lifetime_cents'];

      foreach ( $patreon_user_data['tiers'] as $tier ) {
        $required = ! (
          in_array( $tier['id'], $patreon_post_data['gate_tiers'] ) ||
          ( $tier['amount_cents'] ?? -1 ) >= $patreon_check_amount_cents ||
          ( $patreon_user_data['lifetime_support_cents'] ?? -1 ) >= $patreon_check_lifetime_amount_cents
        );

        $required = apply_filters( 'fictioneer_filter_patreon_tier_unlock', $required, $post, $user, $patreon_post_data );

        if ( ! $required ) {
          break;
        }
      }
    }
  }

  // Check unlocked posts
  if ( $user && $required ) {
    $story_id = fictioneer_get_chapter_story_id( $post->ID );
    $unlocks = get_user_meta( $user->ID, 'fictioneer_post_unlocks', true ) ?: [];
    $unlocks = is_array( $unlocks ) ? $unlocks : [];

    $lock_gate_amount = get_option( 'fictioneer_patreon_global_lock_unlock_amount', 0 ) ?: 0;
    $allow_unlocks = ! ( get_option( 'fictioneer_enable_patreon_locks' ) && $lock_gate_amount );

    // Check Patreon unlock gate
    if ( ! $allow_unlocks && ( $patreon_user_data['valid'] ?? 0 ) ) {
      foreach ( $patreon_user_data['tiers'] as $tier ) {
        if ( ( $tier['amount_cents'] ?? -1 ) >= $lock_gate_amount ) {
          $allow_unlocks = true;
          break;
        }
      }
    }

    if ( $allow_unlocks && $unlocks && array_intersect( [ $post->ID, $story_id ], $unlocks ) ) {
      $required = false;
    }
  }

  // Cache
  $cache[ $cache_key ] = $required;

  // Continue filter
  return $required;
}
add_filter( 'post_password_required', 'fictioneer_bypass_password', 10, 2 );

// No restriction can be applied to administrators
if ( ! current_user_can( 'manage_options' ) ) {
  // === FCN_ADMINBAR_ACCESS ===================================================

  /**
   * Admin bar
   */

  if ( ! current_user_can( 'fcn_adminbar_access' ) ) {
    add_filter( 'show_admin_bar', '__return_false' );
  }

  // === FCN_ADMIN_PANEL_ACCESS ================================================

  /**
   * Prevent access to the admin panel
   *
   * @since 5.6.0
   */

  function fictioneer_restrict_admin_panel() {
    // Allow all AJAX requests
    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
      return;
    }

    // Allow all admin_post actions
    if (
      isset( $_REQUEST['action'] ) &&
      ( $_SERVER['PHP_SELF'] == '/wp-admin/admin-post.php' || $_SERVER['PHP_SELF'] == '/wp-admin/admin-ajax.php' )
    ) {
      return;
    }

    // Redirect back to Home
    if ( is_admin() && ! current_user_can( 'fcn_admin_panel_access' ) ) {
      wp_redirect( home_url() );
      exit;
    }
  }
  add_filter( 'init', 'fictioneer_restrict_admin_panel' );

  // === FCN_DASHBOARD_ACCESS ==================================================

  /**
   * Remove admin dashboard widgets
   *
   * @since 5.6.0
   */

  function fictioneer_remove_dashboard_widgets() {
    global $wp_meta_boxes;

    // Remove all
    $wp_meta_boxes['dashboard']['normal']['core'] = [];
    $wp_meta_boxes['dashboard']['side']['core'] = [];

    // Remove actions
    remove_action( 'welcome_panel', 'wp_welcome_panel' );
  }

  /**
   * Remove the dashboard menu page
   *
   * @since 5.6.0
   */

  function fictioneer_remove_dashboard_menu() {
    remove_menu_page( 'index.php' );
    remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
  }

  /**
   * Redirect from dashboard to user profile
   *
   * @since 5.6.0
   */

  function fictioneer_skip_dashboard() {
    global $pagenow;

    // Unless it's AJAX or an administrator...
    if (
      $pagenow == 'index.php' &&
      ! ( defined( 'DOING_AJAX' ) && DOING_AJAX )
    ) {
      // Skip dashboard, go to user profile
      wp_redirect( home_url( '/wp-admin/profile.php' ) );
      exit;
    }
  }

  /**
   * Remove dashboard from admin bar dropdown
   *
   * @since 5.6.0
   */

  function fictioneer_remove_dashboard_from_admin_bar() {
    global $wp_admin_bar;

    $wp_admin_bar->remove_menu( 'dashboard' );
  }

  if ( ! current_user_can( 'fcn_dashboard_access' ) ) {
    add_action( 'wp_before_admin_bar_render', 'fictioneer_remove_dashboard_from_admin_bar' );
    add_action( 'wp_dashboard_setup', 'fictioneer_remove_dashboard_widgets' );
    add_action( 'admin_menu', 'fictioneer_remove_dashboard_menu' );
    add_action( 'admin_init', 'fictioneer_skip_dashboard' );
  }

  // === FCN_SELECT_PAGE_TEMPLATE ==============================================

  /**
   * Prevents parent and order from being updated
   *
   * @param array $data  An array of slashed, sanitized, and processed post data.
   *
   * @return array The potentially modified post data.
   */

  function fictioneer_prevent_parent_and_order_update( $data ) {
    // Remove items
    unset( $data['post_parent'] );
    unset( $data['menu_order'] );

    // Continue filter
    return $data;
  }

  /**
   * Filters the page template selection
   *
   * @since 5.6.0
   *
   * @param array $templates  Array of templates ('name' => 'Display Name').
   *
   * @return array Array of allowed templates.
   */

  function fictioneer_disallow_page_template_select( $templates ) {
    // Trim default template selection
    $templates = array_intersect_key( $templates, FICTIONEER_ALLOWED_PAGE_TEMPLATES ) ?: [];

    // Continue filter
    return $templates;
  }

  /**
   * Prevents update of page template based on conditions
   *
   * If the user lacks permission and the page template is not listed
   * as allowed for everyone, the meta update is stopped.
   *
   * @since 5.6.2
   *
   * @param null|mixed $check       Whether to allow updating metadata for the given type.
   * @param int        $object_id   ID of the object metadata is for.
   * @param string     $meta_key    Metadata key.
   * @param mixed      $meta_value  Metadata value. Must be serializable if non-scalar.
   *
   * @return mixed Null if allowed (yes), literally anything else if not.
   */

  function fictioneer_prevent_page_template_update( $check, $object_id, $meta_key, $meta_value ) {
    // Page template update?
    if ( $meta_key !== '_wp_page_template' ) {
      return $check;
    }

    // Permission to change the page template?
    if (
      ! current_user_can( 'fcn_select_page_template' ) &&
      ! in_array( $meta_value, array_keys( FICTIONEER_ALLOWED_PAGE_TEMPLATES ) )
    ) {
      return false;
    }

    // Continue filter
    return $check;
  }

  // Run before other hooks or bad things will happen!
  if ( ! current_user_can( 'fcn_select_page_template' ) ) {
    add_filter( 'update_post_metadata', 'fictioneer_prevent_page_template_update', 1, 4 );
    add_filter( 'theme_templates', 'fictioneer_disallow_page_template_select', 1 );
    add_filter( 'wp_insert_post_data', 'fictioneer_prevent_parent_and_order_update', 1 );
  }

  // === MODERATE_COMMENTS =====================================================

  /**
   * Remove comment item from admin bar
   *
   * @since 5.6.0
   */

  function fictioneer_remove_comments_from_admin_bar() {
    global $wp_admin_bar;

    $wp_admin_bar->remove_node( 'comments' );
  }

  /**
   * Remove comments menu
   *
   * @since 5.6.0
   */

  function fictioneer_remove_comments_menu_page() {
    remove_menu_page( 'edit-comments.php' );
  }

  /**
   * Restrict menu access for non-administrators
   *
   * @since 5.6.0
   */

  function fictioneer_restrict_comment_edit() {
    // Setup
    $screen = get_current_screen();

    if ( $screen->id === 'edit-comments' ) {
      wp_die( 'Access denied.' );
    }
  }

  /**
   * Remove comments column
   *
   * @since 5.6.0
   *
   * @param array $columns  The table columns.
   *
   * @return array Modified table column.
   */

  function fictioneer_remove_comments_column( $columns ) {
    if ( isset( $columns['comments'] ) ) {
      unset( $columns['comments'] );
    }

    return $columns;
  }

  if ( ! current_user_can( 'moderate_comments' ) ) {
    add_action( 'admin_menu', 'fictioneer_remove_comments_menu_page' );
    add_action( 'wp_before_admin_bar_render', 'fictioneer_remove_comments_from_admin_bar' );
    add_action( 'current_screen', 'fictioneer_restrict_comment_edit' );
    add_filter( 'manage_posts_columns', 'fictioneer_remove_comments_column' );
    add_filter( 'manage_pages_columns', 'fictioneer_remove_comments_column' );
  }

  /**
   * Only allow editing of comments
   *
   * @since 5.6.0
   *
   * @param array $all_caps  An array of all the user's capabilities.
   * @param array $cap       Primitive capabilities that are being checked.
   * @param array $args      Arguments passed to the capabilities check.
   *
   * @return array Modified capabilities array.
   */

  function fictioneer_edit_only_comments( $all_caps, $cap, $args ) {
    if ( ( $args[0] ?? 0 ) == 'edit_post' && isset( $args[2] ) ) {
      $post = get_post( $args[2] );
      $current_user_id = get_current_user_id();

      // Remove capability if not a comment
      if (
        ! empty( $post ) &&
        $post->post_type !== 'comment' &&
        $post->post_author != $current_user_id
      ) {
        $all_caps[ $cap[0] ] = false;
      }
    }

    return $all_caps;
  }

  if ( current_user_can( 'moderate_comments' ) && current_user_can( 'fcn_only_moderate_comments' ) ) {
    add_filter( 'user_has_cap', 'fictioneer_edit_only_comments', 10, 3 );
  }

  /**
   * Restrict comment editing
   *
   * @since 5.7.3
   *
   * @param array  $caps     Primitive capabilities required of the user.
   * @param string $cap      Capability being checked.
   * @param int    $user_id  The user ID.
   *
   * @return array The still allowed primitive capabilities of the user.
   */

  function fictioneer_edit_comments( $caps, $cap, $user_id ) {
    // Skip unrelated capabilities
    if ( $cap !== 'edit_comment' ) {
      return $caps;
    }

    // Get user
    $user = get_userdata( $user_id );

    // Check capabilities
    if ( $user && $user->has_cap( 'moderate_comments' ) ) {
      return $caps;
    }

    // Disallow
    return ['do_not_allow'];
  }

  if ( ! current_user_can( 'moderate_comments' ) ) {
    add_filter( 'map_meta_cap', 'fictioneer_edit_comments', 10, 3 );
  }

  // === MANAGE_OPTIONS ========================================================

  /**
   * Reduce admin panel
   *
   * @since 5.6.0
   */

  function fictioneer_reduce_admin_panel() {
    // Remove menu pages
    remove_menu_page( 'tools.php' );
    remove_menu_page( 'plugins.php' );
    remove_menu_page( 'themes.php' );
  }

  /**
   * Restrict menu access for non-administrators
   *
   * @since 5.6.0
   */

  function fictioneer_restrict_admin_only_pages() {
    // Setup
    $screen = get_current_screen();

    // No access for non-administrators...
    if (
      in_array(
        $screen->id,
        ['tools', 'export', 'import', 'site-health', 'export-personal-data', 'erase-personal-data', 'themes', 'customize', 'nav-menus', 'theme-editor', 'options-general']
      )
    ) {
      wp_die( 'Access denied.' );
    }
  }

  if ( ! current_user_can( 'manage_options' ) ) {
    add_action( 'admin_menu', 'fictioneer_reduce_admin_panel' );
    add_action( 'current_screen', 'fictioneer_restrict_admin_only_pages' );
  }

  // === UPDATE_CORE ===========================================================

  /**
   * Remove update notice
   *
   * @since 5.6.0
   */

  function fictioneer_remove_update_notice(){
    remove_action( 'admin_notices', 'update_nag', 3 );
  }

  if ( ! current_user_can( 'update_core' ) ) {
    add_action( 'admin_head', 'fictioneer_remove_update_notice' );
  }

  // === FCN_SHORTCODES ========================================================

  /**
   * Strip shortcodes from content before saving to database
   *
   * Note: The user can still use shortcodes on pages that already have them.
   * This is not ideal, but an edge case. Someone who cannot use shortcodes
   * usually also cannot edit others posts.
   *
   * @since 5.6.0
   *
   * @param array $data  An array of slashed, sanitized, and processed post data.
   *
   * @return array Modified post data with shortcodes removed.
   */

  function fictioneer_strip_shortcodes_on_save( $data ) {
    // Check permissions
    if (
      current_user_can( 'fcn_shortcodes' ) ||
      get_current_user_id() !== (int) $data['post_author']
    ) {
      return $data;
    }

    // Exempt certain shortcodes
    add_filter( 'strip_shortcodes_tagnames', 'fictioneer_exempt_shortcodes_from_removal' );

    // Strip the shortcodes
    $data['post_content'] = strip_shortcodes( $data['post_content'] );

    // Only do this for the trigger post or bad things can happen!
    remove_filter( 'wp_insert_post_data', 'fictioneer_strip_shortcodes_on_save', 1 );
    remove_filter( 'strip_shortcodes_tagnames', 'fictioneer_exempt_shortcodes_from_removal' );

    // Continue filter
    return $data;
  }

  if ( ! current_user_can( 'fcn_shortcodes' ) ) {
    add_filter( 'wp_insert_post_data', 'fictioneer_strip_shortcodes_on_save', 1 );
  }

  /**
   * Exempts shortcodes from being removed by strip_shortcodes()
   *
   * @since 5.14.0
   * @since 5.25.0 - Allowed 'fcnt' shortcode to pass.
   *
   * @param array $tags_to_remove  Tags to be removed.
   *
   * @return array Updated tags to be removed.
   */


  function fictioneer_exempt_shortcodes_from_removal( $tags_to_remove ) {
    // Remove 'fictioneer_fa' shortcode from tags
    if ( ( $key = array_search( 'fictioneer_fa', $tags_to_remove ) ) !== false ) {
      unset( $tags_to_remove[ $key ] );
    }

    // Remove 'fcnt' shortcode from tags
    if ( ( $key = array_search( 'fcnt', $tags_to_remove ) ) !== false ) {
      unset( $tags_to_remove[ $key ] );
    }

    // Continue filter
    return $tags_to_remove;
  }

  // === FCN_EDIT_OTHERS_{POST_TYPE} ===========================================

  /**
   * Limit users to their own fiction posts
   *
   * @since 5.6.0
   *
   * @param WP_Query $query  The WP_Query instance (passed by reference).
   */

  function fictioneer_edit_others_fictioneer_posts( $query ) {
    global $pagenow;

    // Abort conditions...
    if ( ! $query->is_admin || $pagenow != 'edit.php' ) {
      return;
    }

    // Setup
    $post_type = $query->get( 'post_type' );

    // Abort if wrong post type
    if ( ! in_array( $post_type, ['fcn_story', 'fcn_chapter', 'fcn_collection', 'fcn_recommendation'] ) ) {
      return;
    }

    // Prepare
    $post_type_plural = $post_type == 'fcn_story' ? 'fcn_stories' : "{$post_type}s";

    // Add author to query unless user can edit the fiction of others
    if ( ! current_user_can( "edit_others_{$post_type_plural}" ) ) {
      $query->set( 'author', get_current_user_id() );
    }
  }
  add_action( 'pre_get_posts', 'fictioneer_edit_others_fictioneer_posts' );

  // === FCN_READ_OTHERS_FILES =================================================

  /**
   * Prevent users from seeing uploaded files of others
   *
   * @since 5.6.0
   *
   * @param WP_Query $query  The queried attachments.
   */

  function fictioneer_read_others_files( $query ) {
    global $current_user, $pagenow;

    // Only affect media library on admin side
    if (
      'admin-ajax.php' !== $pagenow ||
      ( $_REQUEST['action'] ?? 0 ) !== 'query-attachments'
    ) {
      return;
    }

    // Limit to author (uploader)
    $query->set( 'author', $current_user->ID );
  }

  /**
   * Prevent users from seeing uploaded files of others in the list view
   *
   * @since 5.6.0
   *
   * @param WP_Query $wp_query  The current WP_Query.
   */

  function fictioneer_read_others_files_list_view( $wp_query ) {
    // Safety check
    if ( ! function_exists( 'get_current_screen' ) ) {
      return;
    }

    $screen = get_current_screen();

    if (
      $wp_query->is_main_query() &&
      $wp_query->query['post_type'] === 'attachment' &&
      $screen && $screen->base == 'upload' &&
      $screen->id == 'upload' &&
      $screen->post_type == 'attachment'
    ) {
      $wp_query->set( 'author', get_current_user_id() );
    }
  }

  /**
   * Hide lock inserter media tab.
   *
   * @since 5.27.3
   */

  function fictioneer_hide_inserter_media_tab_with_css() {
    global $pagenow;

    if ( $pagenow !== 'post.php' && $pagenow !== 'post-new.php' ) {
      return;
    }

    echo '<style type="text/css">.block-editor-tabbed-sidebar #tabs-1-media {display: none !important;}</style>';
  }

  if ( ! current_user_can( 'fcn_read_others_files' ) && is_admin() ) {
    add_action( 'admin_head', 'fictioneer_hide_inserter_media_tab_with_css' );
    add_action( 'pre_get_posts', 'fictioneer_read_others_files' );
    add_action( 'pre_get_posts', 'fictioneer_read_others_files_list_view' );
  }

  // === FCN_EDIT_OTHERS_FILES =================================================

  /**
   * User cannot edit the files of others
   *
   * @since 5.6.0
   *
   * @param array  $caps     Primitive capabilities required of the user.
   * @param string $cap      Capability being checked.
   * @param int    $user_id  The user ID.
   * @param array  $args     Adds context to the capability check, typically
   *                         starting with an object ID.
   *
   * @return array The still allowed primitive capabilities of the user.
   */

  function fictioneer_edit_others_files( $caps, $cap, $user_id, $args ) {
    // Skip unrelated capabilities
    if ( $cap != 'edit_post' ) {
      return $caps;
    }

    // Get the post in question.
    $post = get_post( $args[0] ?? 0 );

    // Check if an attachment and whether the user is the author (uploader)
    if (
      empty( $post ) ||
      $post->post_type != 'attachment' ||
      $post->post_author == $user_id
    ) {
      return $caps;
    }

    // Disallow
    return ['do_not_allow'];
  }

  if ( ! current_user_can( 'fcn_edit_others_files' ) ) {
    add_filter( 'map_meta_cap', 'fictioneer_edit_others_files', 10, 4 );
  }

  // === FCN_DELETE_OTHERS_FILES ===============================================

  /**
   * User cannot delete the files of others
   *
   * @since 5.6.0
   *
   * @param array  $caps     Primitive capabilities required of the user.
   * @param string $cap      Capability being checked.
   * @param int    $user_id  The user ID.
   * @param array  $args     Adds context to the capability check, typically
   *                         starting with an object ID.
   *
   * @return array The still allowed primitive capabilities of the user.
   */

  function fictioneer_delete_others_files( $caps, $cap, $user_id, $args ) {
    // Skip unrelated capabilities
    if ( $cap != 'delete_post' ) {
      return $caps;
    }

    // Get the post in question.
    $post = get_post( $args[0] ?? 0 );

    // Check if an attachment and whether the user is the author (uploader)
    if (
      empty( $post ) ||
      $post->post_type != 'attachment' ||
      $post->post_author == $user_id
    ) {
      return $caps;
    }

    // Disallow
    return ['do_not_allow'];
  }

  if ( ! current_user_can( 'fcn_delete_others_files' ) ) {
    add_filter( 'map_meta_cap', 'fictioneer_delete_others_files', 9999, 4 );
  }

  // === FCN_PRIVACY_CLEARANCE =================================================

  /**
   * Remove email and name columns from user table
   *
   * @since 4.7.0
   *
   * @param array $column_headers  Columns to show in the user table.
   *
   * @return array Reduced columns to show in the user table.
   */

  function fictioneer_hide_users_columns( $column_headers ) {
    unset( $column_headers['email'] );
    unset( $column_headers['name'] );

    return $column_headers;
  }

  /**
   * Remove quick edit from comments table
   *
   * The quick edit form for comments shows unfortunately private data that
   * we want to hide if that setting is enabled.
   *
   * @since 4.7.0
   *
   * @param array $actions  Actions per row in the comments table.
   *
   * @return array Restricted actions per row in the comments table.
   */

  function fictioneer_remove_quick_edit( $actions ) {
    unset( $actions['quickedit'] );

    return $actions;
  }

  /**
   * Remove URL and email fields from comment edit page
   *
   * Since these are not normally accessible, we need to quickly hide them
   * with JavaScript. This is not a great solution but better than nothing.
   *
   * @since 4.7.0
   */

  function fictioneer_hide_private_data() {
    wp_add_inline_script(
      'fictioneer-admin-script',
      "jQuery(function($) {
        $('.editcomment tr:nth-child(3)').remove();
        $('.editcomment tr:nth-child(2)').remove();
      });"
    );
  }

  if ( ! current_user_can( 'fcn_privacy_clearance' ) ) {
    add_filter( 'comment_email', '__return_false' );
    add_filter( 'get_comment_author_IP', '__return_empty_string' );
    add_filter( 'manage_users_columns', 'fictioneer_hide_users_columns' );
    add_filter( 'comment_row_actions', 'fictioneer_remove_quick_edit' );
    add_action( 'admin_enqueue_scripts', 'fictioneer_hide_private_data', 20 );
  }

  // === FCN_REDUCED_PROFILE ===================================================

  /**
   * Hide subscriber profile blocks in admin panel
   *
   * @since 5.6.0
   * @since 5.26.1 - Use wp_print_inline_script_tag().
   */

  function fictioneer_remove_profile_blocks() {
    global $pagenow;

    if ( $pagenow !== 'profile.php' ) {
      return;
    }

    // Add CSS to hide blocks...
    echo '<style type="text/css">.user-url-wrap, .user-description-wrap, .user-first-name-wrap, .user-last-name-wrap, .user-language-wrap, .user-admin-bar-front-wrap, #contextual-help-link-wrap, #your-profile > h2:first-of-type { display: none; }</style>';

    // Remove password options
    if ( ! get_option( 'fictioneer_show_wp_login_link' ) ) {
      wp_print_inline_script_tag(
        'document.addEventListener("DOMContentLoaded", () => {document.querySelectorAll(".user-pass1-wrap, .user-pass2-wrap, .pw-weak, .user-generate-reset-link-wrap").forEach(element => {element.remove();});});',
        array(
          'id' => 'fictioneer-iife-remove-admin-profile-blocks',
          'type' => 'text/javascript',
          'data-jetpack-boost' => 'ignore',
          'data-no-optimize' => '1',
          'data-no-defer' => '1',
          'data-no-minify' => '1'
        )
      );
    }
  }

  if ( current_user_can( 'fcn_reduced_profile' ) ) {
    add_filter( 'wp_is_application_passwords_available', '__return_false' );
    add_filter( 'user_contactmethods', '__return_empty_array' );
    add_action( 'admin_head', 'fictioneer_remove_profile_blocks' );
    remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
  }

  // === FCN_MAKE_STICKY =======================================================

  /**
   * Unstick a post
   *
   * @since 5.6.0
   *
   * @param int $post_id  The post ID.
   */

  function fictioneer_prevent_post_sticky( $post_id ) {
    unstick_post( $post_id );

    // Only do this for the trigger post or bad things can happen!
    remove_action( 'post_stuck', 'fictioneer_prevent_post_sticky' );
  }

  if ( ! current_user_can( 'fcn_make_sticky' ) ) {
    add_action( 'post_stuck', 'fictioneer_prevent_post_sticky' );
  }

  // === FCN_UPLOAD_LIMIT ======================================================

  /**
   * Limit the default upload size in MB (minimum 1 MB)
   *
   * @since 5.6.0
   *
   * @param int $bytes  Default limit value in bytes.
   *
   * @return int Modified maximum upload file size in bytes.
   */

  function fictioneer_upload_size_limit( $bytes ) {
    // Setup
    $mb = absint( get_option( 'fictioneer_upload_size_limit', 5 ) ?: 5 );
    $mb = max( $mb, 1 ); // 1 MB minimum

    // Return maximum upload file size
    return 1024 * 1024 * $mb;
  }

  if ( current_user_can( 'fcn_upload_limit' ) ) {
    add_filter( 'upload_size_limit', 'fictioneer_upload_size_limit' );
  }

  // === FCN_UPLOAD_RESTRICTION ================================================

  /**
   * Restrict uploaded file types based on allowed MIME types
   *
   * @since 5.6.0
   *
   * @param array $file  An array of data for a single uploaded file. Has keys
   *                     for 'name', 'type', 'tmp_name', 'error', and 'size'.
   *
   * @return array Modified array with error message if the MIME type is not allowed.
   */

  function fictioneer_upload_restrictions( $file ) {
    // Setup
    $filetype = wp_check_filetype( $file['name'] );
    $mime_type = $filetype['type'];
    $allowed = get_option( 'fictioneer_upload_mime_types', FICTIONEER_DEFAULT_UPLOAD_MIME_TYPE_RESTRICTIONS ) ?:
      FICTIONEER_DEFAULT_UPLOAD_MIME_TYPE_RESTRICTIONS;
    $allowed = fictioneer_explode_list( $allowed );

    // Limit upload file types
    if ( ! in_array( $mime_type, $allowed ) ){
      $file['error'] = __( 'You are not allowed to upload files of this type.', 'fictioneer' );
    }

    // Continue filter
    return $file;
  }

  if ( current_user_can( 'fcn_upload_restrictions' ) ) {
    add_filter( 'wp_handle_upload_prefilter', 'fictioneer_upload_restrictions' );
  }

  // === FCN_ALL_BLOCKS ========================================================

  /**
   * Restrict the use of specific Gutenberg blocks
   *
   * @since 5.6.0
   *
   * @param array $data  An array of slashed, sanitized, and processed post data.
   *
   * @return array Modified post data with unwanted blocks removed.
   */

  function fictioneer_remove_restricted_block_content( $data ) {
    // Regular expression to match forbidden blocks
    $forbidden_patterns = array(
      '/<!-- wp:buttons\/?.*?-->(.*?)<!-- \/wp:buttons\/?.*? -->/s',
      '/<!-- wp:button\/?.*?-->(.*?)<!-- \/wp:button\/?.*? -->/s',
      '/<!-- wp:audio\/?.*?-->(.*?)<!-- \/wp:audio\/?.*? -->/s',
      '/<!-- wp:video\/?.*?-->(.*?)<!-- \/wp:video\/?.*? -->/s',
      '/<!-- wp:file\/?.*?-->(.*?)<!-- \/wp:file\/?.*? -->/s',
      '/<!-- wp:jetpack\/?.*?-->(.*?)<!-- \/wp:jetpack\/?.*? -->/s' // Because it's common enough
    );

    // Remove matching blocks
    foreach ( $forbidden_patterns as $pattern ) {
      $data['post_content'] = preg_replace( $pattern, '', $data['post_content'] );
    }

    // Only do this for the trigger post or bad things can happen!
    remove_filter( 'wp_insert_post_data', 'fictioneer_remove_restricted_block_content', 1 );

    // Continue with cleaned-up data
    return $data;
  }

  /**
   * Restricts the block types available in the Gutenberg editor
   *
   * @return string Array of allowed block types.
   */

  function fictioneer_restrict_block_types() {
    $allowed = array(
      'core/image',
      'core/paragraph',
      'core/heading',
      'core/list',
      'core/list-item',
      'core/gallery',
      'core/quote',
      'core/pullquote',
      'core/table',
      'core/code',
      'core/preformatted',
      'core/html',
      'core/separator',
      'core/spacer',
      'core/more',
      'core/embed',
      'core-embed/youtube',
      'core-embed/soundcloud',
      'core-embed/spotify',
      'core-embed/vimeo',
      'core-embed/twitter'
    );

    if ( current_user_can( 'fcn_shortcodes' ) ) {
      $allowed[] = 'core/shortcode';
    }

    return $allowed;
  }

  if ( ! current_user_can( 'fcn_all_blocks' ) ) {
    add_filter( 'allowed_block_types_all', 'fictioneer_restrict_block_types', 20 ); // Run after global filter
    add_filter( 'wp_insert_post_data', 'fictioneer_remove_restricted_block_content', 1 );
  }

  // === FCN_EDIT_PERMALINK ====================================================

  /**
   * Prevents user edit of permalink
   *
   * @since 5.6.0
   * @since 5.8.6 - Fixed duplicate permalinks.
   *
   * @param array $data     An array of slashed, sanitized, and processed post data.
   * @param array $postarr  An array of sanitized (and slashed) but otherwise unmodified post data.
   *
   * @return array The post data with the permalink enforced.
   */

  function fictioneer_prevent_permalink_edit( $data, $postarr ) {
    // Continue filter if empty or no change
    if ( empty( $data['post_name'] ) || $data['post_name'] === get_post_field( 'post_name', $postarr['ID'] ) ) {
      return $data;
    }

    // Generate slug from title
    $slug = sanitize_title( $data['post_title'] );

    // Ensure unique slug
    $data['post_name'] = wp_unique_post_slug(
      $slug,
      $postarr['ID'],
      $data['post_status'],
      $data['post_type'],
      $data['post_parent'] ?? 0
    );

    // Continue filter
    return $data;
  }

  /**
   * Hide the permalink field with CSS
   *
   * @since 5.6.0
   */

  function fictioneer_hide_permalink_with_css() {
    global $pagenow;

    if ( $pagenow !== 'post.php' && $pagenow !== 'post-new.php' ) {
      return;
    }

    echo '<style type="text/css" id="foobar">.edit-post-post-url, #edit-slug-buttons {display: none !important;}</style>';
  }

  /**
   * Hide the permalink field with JS
   *
   * @since 5.6.2
   * @since 5.26.1 - Use wp_print_inline_script_tag().
   *
   */

  function fictioneer_hide_permalink_with_js() {
    global $pagenow;

    if ( $pagenow !== 'post.php' && $pagenow !== 'post-new.php' ) {
      return;
    }

    wp_print_inline_script_tag(
      'document.querySelectorAll("#edit-slug-buttons").forEach(element => {element.remove();});',
      array(
        'id' => 'fictioneer-iife-hide-permalink-in-editor',
        'type' => 'text/javascript',
        'data-jetpack-boost' => 'ignore',
        'data-no-optimize' => '1',
        'data-no-defer' => '1',
        'data-no-minify' => '1'
      )
    );
  }

  if ( ! current_user_can( 'fcn_edit_permalink' ) ) {
    add_action( 'admin_head', 'fictioneer_hide_permalink_with_css' );
    add_action( 'admin_footer', 'fictioneer_hide_permalink_with_js' );
    add_filter( 'wp_insert_post_data', 'fictioneer_prevent_permalink_edit', 99, 2 );
  }

  // === FCN_EDIT_DATE =========================================================

  /**
   * Prevents the update of the publish date
   *
   * Note: The date can be edited until the post has been published once, so you
   * can still schedule a post or change the target date. But once it is published,
   * the date cannot be changed.
   *
   * @param array $data     An array of slashed, sanitized, and processed post data.
   * @param array $postarr  An array of sanitized (and slashed) but otherwise unmodified post data.
   *
   * @return array The potentially modified post data.
   */

  function fictioneer_prevent_publish_date_update( $data, $postarr ) {
    // New post?
    if ( empty( $postarr['ID'] ) || $postarr['post_status'] === 'auto-draft' || empty( $postarr['post_date_gmt'] ) ) {
      return $data;
    }

    // Setup
    $current_post_date_gmt = get_post_time( 'Y-m-d H:i:s', 1, $postarr['ID'] );

    // Remove from update array if already published once
    if ( $current_post_date_gmt !== $data['post_date_gmt'] ) {
      unset( $data['post_date'] );
      unset( $data['post_date_gmt'] );
    }

    // Continue filter
    return $data;
  }

  if ( ! current_user_can( 'fcn_edit_date' ) ) {
    add_filter( 'wp_insert_post_data', 'fictioneer_prevent_publish_date_update', 1, 2 );
  }

  // === FCN_CLASSIC_EDITOR ====================================================

  /**
   * Inject CSS for the classic editor
   *
   * @since 5.6.2
   */

  function fictioneer_classic_editor_css_restrictions() {
    global $pagenow;

    if ( $pagenow !== 'post.php' && $pagenow !== 'post-new.php' ) {
      return;
    }

    echo '<style type="text/css">.selectit[for="ping_status"], #add-new-comment {display: none !important;}</style>';
  }

  /**
   * Inject JavaScript for the classic editor
   *
   * @since 5.6.2
   * @since 5.26.1 - Use wp_print_inline_script_tag().
   */

  function fictioneer_classic_editor_js_restrictions() {
    global $pagenow;

    if ( $pagenow !== 'post.php' && $pagenow !== 'post-new.php' ) {
      return;
    }

    wp_print_inline_script_tag(
      'document.querySelectorAll(".selectit[for=ping_status], #add-new-comment").forEach(element => {element.remove();});',
      array(
        'id' => 'fictioneer-iife-classic-editor-restrictions',
        'type' => 'text/javascript',
        'data-jetpack-boost' => 'ignore',
        'data-no-optimize' => '1',
        'data-no-defer' => '1',
        'data-no-minify' => '1'
      )
    );
  }

  /**
   * Restrict metaboxes in the classic editor
   *
   * @since 5.6.2
   */

  function fictioneer_restrict_classic_metaboxes() {
    $post_types = ['post', 'page', 'fcn_story', 'fcn_chapter', 'fcn_collection', 'fcn_recommendation'];

    // Trackbacks
    remove_meta_box( 'trackbacksdiv', $post_types, 'normal' );

    // Tags
    if ( ! current_user_can( 'assign_post_tags' ) ) {
      remove_meta_box( 'tagsdiv-post_tag', $post_types, 'side' );
    }

    // Categories
    if ( ! current_user_can( 'assign_categories' ) ) {
      remove_meta_box( 'categorydiv', $post_types, 'side' );
    }

    // Genres
    if ( ! current_user_can( 'assign_fcn_genres' ) ) {
      remove_meta_box( 'fcn_genrediv', $post_types, 'side' );
    }

    // Fandoms
    if ( ! current_user_can( 'assign_fcn_fandoms' ) ) {
      remove_meta_box( 'fcn_fandomdiv', $post_types, 'side' );
    }

    // Characters
    if ( ! current_user_can( 'assign_fcn_characters' ) ) {
      remove_meta_box( 'fcn_characterdiv', $post_types, 'side' );
    }

    // Content Warnings
    if ( ! current_user_can( 'assign_fcn_content_warnings' ) ) {
      remove_meta_box( 'fcn_content_warningdiv', $post_types, 'side' );
    }

    // Permalink
    if ( ! current_user_can( 'fcn_edit_permalink' ) ) {
      remove_meta_box( 'slugdiv', $post_types, 'normal' );
    }

    // Page template
    if ( ! current_user_can( 'fcn_select_page_template' ) ) {
      remove_meta_box( 'pageparentdiv', $post_types, 'side' );
    }
  }

  if ( current_user_can( 'fcn_classic_editor' ) ) {
    add_filter( 'use_block_editor_for_post_type', '__return_false' );
    add_action( 'add_meta_boxes', 'fictioneer_restrict_classic_metaboxes' );
    add_action( 'admin_head', 'fictioneer_classic_editor_css_restrictions' );
    add_action( 'admin_footer', 'fictioneer_classic_editor_js_restrictions' );
  }
}
