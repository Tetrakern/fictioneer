<?php

// =============================================================================
// ROLE CUSTOMIZATION ACTIONS
// =============================================================================

/**
 * Add custom moderator role
 *
 * @since 5.0
 */

function fictioneer_add_moderator_role() {
	return add_role(
    'fcn_moderator',
    __( 'Moderator', 'fictioneer' ),
    array(
      'read' => true,
      'edit_posts' => true,
      'edit_others_posts' => true,
      'edit_published_posts' => true,
      'moderate_comments' => true,
      'edit_comment' => true,
      'delete_posts' => false,
      'delete_others_posts' => false
    )
  );
}

/**
 * Upgrade author role with additional capabilities
 *
 * @since 5.0
 */

function fictioneer_upgrade_author_role() {
	$role = get_role( 'author' );
  $role->add_cap( 'delete_pages' );
  $role->add_cap( 'delete_published_pages' );
  $role->add_cap( 'edit_pages' );
  $role->add_cap( 'edit_published_pages' );
  $role->add_cap( 'publish_pages' );
}

/**
 * Reset author role to WordPress defaults
 *
 * @since 5.0
 */

function fictioneer_reset_author_role() {
	$role = get_role( 'author' );
  $role->remove_cap( 'delete_pages' );
  $role->remove_cap( 'delete_published_pages' );
  $role->remove_cap( 'edit_pages' );
  $role->remove_cap( 'edit_published_pages' );
  $role->remove_cap( 'publish_pages' );
}

/**
 * Upgrade contributor role with additional capabilities
 *
 * @since 5.0
 */

function fictioneer_upgrade_contributor_role() {
	$role = get_role( 'contributor' );
  $role->add_cap( 'delete_pages' );
  $role->add_cap( 'edit_pages' );
  $role->add_cap( 'edit_published_pages' );
}

/**
 * Reset contributor role to WordPress defaults
 *
 * @since 5.0
 */

function fictioneer_reset_contributor_role() {
	$role = get_role( 'contributor' );
  $role->remove_cap( 'delete_pages' );
  $role->remove_cap( 'edit_pages' );
  $role->remove_cap( 'edit_published_pages' );
}

/**
 * Limit editor role to less capabilities
 *
 * @since 5.0
 */

function fictioneer_limit_editor_role() {
	$role = get_role( 'editor' );
  $role->remove_cap( 'delete_pages' );
  $role->remove_cap( 'delete_published_pages' );
  $role->remove_cap( 'delete_published_posts' );
  $role->remove_cap( 'delete_others_pages' );
  $role->remove_cap( 'delete_others_posts' );
  $role->remove_cap( 'publish_pages' );
  $role->remove_cap( 'publish_posts' );
  $role->remove_cap( 'manage_categories' );
  $role->remove_cap( 'unfiltered_html' );
  $role->remove_cap( 'manage_links' );
}

/**
 * Reset editor role to WordPress defaults
 *
 * @since 5.0
 */

function fictioneer_reset_editor_role() {
	$role = get_role( 'editor' );
  $role->add_cap( 'delete_pages' );
  $role->add_cap( 'delete_published_pages' );
  $role->add_cap( 'delete_published_posts' );
  $role->add_cap( 'delete_others_pages' );
  $role->add_cap( 'delete_others_posts' );
  $role->add_cap( 'publish_pages' );
  $role->add_cap( 'publish_posts' );
  $role->add_cap( 'manage_categories' );
  $role->add_cap( 'unfiltered_html' );
  $role->add_cap( 'manage_links' );
}

// =============================================================================
// RESTRICT SUBSCRIBERS ROLE
// =============================================================================

if ( ! function_exists( 'fictioneer_block_subscribers_from_admin' ) ) {
  /**
   * Prevent subscribers from accessing the admin panel
   *
   * Subscribers have a custom frontend user profile and therefore no need to
   * access the admin panel at all. Only higher roles are granted access, although
   * admin-specific actions such as AJAX requests are still permitted.
   *
   * @since 4.0
   */

  function fictioneer_block_subscribers_from_admin() {
    // Redirect back to Home
    if (
      is_admin() &&
      ! current_user_can( 'edit_posts' ) &&
      ! current_user_can( 'moderate_comments' ) &&
      ! ( defined( 'DOING_AJAX' ) && DOING_AJAX )
    ) {
      wp_redirect( home_url() );
      exit;
    }
  }
}

if ( get_option( 'fictioneer_block_subscribers_from_admin' ) ) {
  add_action( 'init', 'fictioneer_block_subscribers_from_admin' );
}

if ( ! function_exists( 'fictioneer_reduce_subscriber_dashboard_widgets' ) ) {
  /**
   * Remove admin dashboard widgets for subscribers
   *
   * @since 5.0
   * @link https://developer.wordpress.org/apis/handbook/dashboard-widgets/
   */

  function fictioneer_reduce_subscriber_dashboard_widgets() {
    global $wp_meta_boxes;

    // Remove all
    $wp_meta_boxes['dashboard']['normal']['core'] = [];
    $wp_meta_boxes['dashboard']['side']['core'] = [];

    // Remove actions
    remove_action( 'welcome_panel', 'wp_welcome_panel' );
  }
}

if ( ! function_exists( 'fictioneer_reduce_subscriber_admin_panel' ) ) {
  /**
   * Remove admin menu pages for subscribers
   *
   * @since 5.0
   */

  function fictioneer_reduce_subscriber_admin_panel() {
    // Remove menu pages
    remove_menu_page( 'index.php' ); // Dashboard
  }
}

// Apply restrictions to subscribers
if ( fictioneer_has_role( get_current_user_id(), 'subscriber' ) ) {
  add_action( 'wp_dashboard_setup', 'fictioneer_reduce_subscriber_dashboard_widgets' );
  add_action( 'admin_menu', 'fictioneer_reduce_subscriber_admin_panel' );
  add_filter( 'show_admin_bar', '__return_false' );
}

// =============================================================================
// RESTRICT FICTIONEER MODERATOR ROLE
// =============================================================================

if ( ! function_exists( 'fictioneer_reduce_moderator_admin_panel' ) ) {
  /**
   * Remove admin menu pages for moderators
   *
   * @since 5.0
   */

  function fictioneer_reduce_moderator_admin_panel() {
    // Remove menu pages
    remove_menu_page( 'edit.php' );
    remove_menu_page( 'tools.php' );
    remove_menu_page( 'plugins.php' );
    remove_menu_page( 'themes.php' );
  }
}

if ( ! function_exists( 'fictioneer_reduce_moderator_dashboard_widgets' ) ) {
  /**
   * Remove admin dashboard widgets for moderators
   *
   * @since 5.0
   * @link https://developer.wordpress.org/apis/handbook/dashboard-widgets/
   */

  function fictioneer_reduce_moderator_dashboard_widgets() {
    global $wp_meta_boxes;

    // Keep
    $activity = $wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity'];

    // Remove all
    $wp_meta_boxes['dashboard']['normal']['core'] = [];
    $wp_meta_boxes['dashboard']['side']['core'] = [];

    // Re-add kept widgets
    $wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity'] = $activity;

    // Remove actions
    remove_action( 'welcome_panel', 'wp_welcome_panel' );
  }
}

if ( ! function_exists( 'fictioneer_reduce_moderator_admin_bar' ) ) {
  /**
   * Remove items from admin bar for moderators
   *
   * @since 5.0
   */

  function fictioneer_reduce_moderator_admin_bar() {
    global $wp_admin_bar;

    // Remove all [+New] items
    $wp_admin_bar->remove_node( 'new-content' );
  }
}

if ( ! function_exists( 'fictioneer_restrict_moderator_menu_access' ) ) {
  /**
   * Restrict access to admin pages/actions for moderators
   *
   * @since 5.0
   */

  function fictioneer_restrict_moderator_menu_access() {
    // Setup
    $screen = get_current_screen();

    // Block access
    if (
      in_array(
        $screen->id,
        ['tools', 'export', 'import', 'site-health', 'export-personal-data', 'erase-personal-data', 'themes', 'customize', 'nav-menus', 'theme-editor', 'users', 'user-new', 'options-general', 'post-new']
      ) ||
      ! empty( $screen->post_type )
    ) {
      wp_die( __( 'Access denied.', 'fictioneer' ) );
    }
  }
}

if ( ! function_exists( 'fictioneer_disable_moderator_comment_edit' ) ) {
  /**
   * Restrict access to admin pages/actions for moderators
   *
   * Prevent moderators from editing the comment content, which is rarely a good action.
   * Doing this via JS is a terrible solution but better than nothing.
   *
   * @since 5.0
   * @todo Do this server-side to prevent savvy moderators from circumventing it.
   */

  function fictioneer_disable_moderator_comment_edit() {
    wp_add_inline_script(
      'fictioneer-admin-script',
      "jQuery(function($) {
        $('#qt_content_toolbar').remove();
        $('.wp-editor-area').prop('disabled', true);
        $('.wp-editor-area').prop('name', '');
      });"
    );
  }
}

// Apply restrictions to fcn_moderators
if (
  fictioneer_has_role( get_current_user_id(), 'fcn_moderator' ) &&
  ! user_can( get_current_user_id(), 'administrator' )
) {
  add_action( 'admin_menu', 'fictioneer_reduce_moderator_admin_panel' );
  add_action( 'current_screen', 'fictioneer_restrict_moderator_menu_access' );
  add_action( 'wp_dashboard_setup', 'fictioneer_reduce_moderator_dashboard_widgets' );
  add_action( 'admin_bar_menu', 'fictioneer_reduce_moderator_admin_bar', 999 );
  add_action( 'admin_enqueue_scripts', 'fictioneer_disable_moderator_comment_edit', 99 );
}

// =============================================================================
// RESTRICT EDITOR ROLE
// =============================================================================

if ( ! function_exists( 'fictioneer_restrict_editor_menu_access' ) ) {
  /**
   * Restrict access to admin pages/actions for editors
   *
   * @since 5.0
   */

  function fictioneer_restrict_editor_menu_access() {
    // Setup
    $screen = get_current_screen();

    // Block access
    if (
      in_array(
        $screen->id,
        ['tools', 'export', 'import', 'site-health', 'export-personal-data', 'erase-personal-data', 'themes', 'customize', 'nav-menus', 'theme-editor', 'users', 'user-new', 'options-general', 'comment']
      )
    ) {
      wp_die( __( 'Access denied.', 'fictioneer' ) );
    }
  }
}

if ( ! function_exists( 'fictioneer_reduce_editor_admin_bar' ) ) {
  /**
   * Remove items from admin bar for editors
   *
   * @since 5.0
   */

  function fictioneer_reduce_editor_admin_bar() {
    global $wp_admin_bar;

    // Remove all [+New] items
    $wp_admin_bar->remove_node( 'new-content' );

    // Remove comments
    $wp_admin_bar->remove_node( 'comments' );
  }
}

if ( ! function_exists( 'fictioneer_reduce_editor_dashboard_widgets' ) ) {
  /**
   * Remove admin dashboard widgets for editors
   *
   * @since 5.0
   * @link https://developer.wordpress.org/apis/handbook/dashboard-widgets/
   */

  function fictioneer_reduce_editor_dashboard_widgets() {
    global $wp_meta_boxes;

    // Keep
    $right_now = $wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now'];
    $activity = $wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity'];

    // Remove all
    $wp_meta_boxes['dashboard']['normal']['core'] = [];
    $wp_meta_boxes['dashboard']['side']['core'] = [];

    // Re-add kept widgets
    $wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now'] = $right_now;
    $wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity'] = $activity;

    // Remove actions
    remove_action( 'welcome_panel', 'wp_welcome_panel' );
  }
}

if ( ! function_exists( 'fictioneer_hide_editor_comments_utilities' ) ) {
  /**
   * Hide comments utilities in admin dashboard for editors
   *
   * @since 5.0
   */

  function fictioneer_hide_editor_comments_utilities() {
    wp_add_inline_script(
      'fictioneer-admin-script',
      "jQuery(function($) {
        $('.dashboard-comment-wrap > .row-actions').remove();
        $('#latest-comments > .subsubsub').remove();
        $('#dashboard_right_now .comment-count').remove();
      });"
    );
  }
}

// Apply restrictions to editors
if (
  fictioneer_has_role( get_current_user_id(), 'editor' ) &&
  ! user_can( get_current_user_id(), 'administrator' )
) {
  add_action( 'current_screen', 'fictioneer_restrict_editor_menu_access' );
  add_action( 'wp_dashboard_setup', 'fictioneer_reduce_editor_dashboard_widgets' );
  add_action( 'admin_enqueue_scripts', 'fictioneer_hide_editor_comments_utilities', 99 );
  add_action( 'admin_bar_menu', 'fictioneer_reduce_editor_admin_bar', 999 );
}

// =============================================================================
// RESTRICT CONTRIBUTOR ROLE
// =============================================================================

if ( ! function_exists( 'fictioneer_reduce_contributor_dashboard_widgets' ) ) {
  /**
   * Remove admin dashboard widgets for contributors
   *
   * @since 5.0
   * @link https://developer.wordpress.org/apis/handbook/dashboard-widgets/
   */

  function fictioneer_reduce_contributor_dashboard_widgets() {
    global $wp_meta_boxes;

    // Keep
    $right_now = $wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now'];
    $activity = $wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity'];

    // Remove all
    $wp_meta_boxes['dashboard']['normal']['core'] = [];
    $wp_meta_boxes['dashboard']['side']['core'] = [];

    // Re-add kept widgets
    $wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now'] = $right_now;
    $wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity'] = $activity;

    // Remove actions
    remove_action( 'welcome_panel', 'wp_welcome_panel' );
  }
}

if ( ! function_exists( 'fictioneer_reduce_contributor_admin_bar' ) ) {
  /**
   * Remove items from admin bar for contributors
   *
   * @since 5.0
   */

  function fictioneer_reduce_contributor_admin_bar() {
    global $wp_admin_bar;

    // Remove all [+New] items
    $wp_admin_bar->remove_node( 'new-content' );

    // Remove comments
    $wp_admin_bar->remove_node( 'comments' );
  }
}

if ( ! function_exists( 'fictioneer_hide_contributor_comments_utilities' ) ) {
  /**
   * Hide comments utilities in admin dashboard for contributors
   *
   * @since 5.0
   */

  function fictioneer_hide_contributor_comments_utilities() {
    wp_add_inline_script(
      'fictioneer-admin-script',
      "jQuery(function($) {
        $('.dashboard-comment-wrap > .row-actions').remove();
        $('#latest-comments > .subsubsub').remove();
        $('#dashboard_right_now .comment-count').remove();
      });"
    );
  }
}

// Apply restrictions to editors
if (
  fictioneer_has_role( get_current_user_id(), 'contributor' ) &&
  ! user_can( get_current_user_id(), 'administrator' )
) {
  add_action( 'wp_dashboard_setup', 'fictioneer_reduce_contributor_dashboard_widgets' );
  add_action( 'admin_enqueue_scripts', 'fictioneer_hide_contributor_comments_utilities', 99 );
  add_action( 'admin_bar_menu', 'fictioneer_reduce_contributor_admin_bar', 999 );
}

?>
