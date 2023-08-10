<?php

// =============================================================================
// SETUP CAPABILITIES
// =============================================================================

define(
	'FICTIONEER_BASE_CAPABILITIES',
	array(
		'fcn_read_others_files', // Fictioneer
    'fcn_edit_others_files', // Fictioneer
    'fcn_delete_others_files', // Fictioneer
    'fcn_select_page_template', // Fictioneer
    'fcn_admin_profile_access', // Fictioneer
    'fcn_adminbar_access', // Fictioneer
    'fcn_dashboard_access', // Fictioneer
    'fcn_privacy_clearance', // Fictioneer
    'fcn_shortcodes', // Fictioneer
    'unfiltered_html', // Default
    'edit_users', // Default
    'add_users', // Default
    'create_users', // Default
    'delete_users' // Default
	)
);

function fictioneer_initialize_roles() {
  // Capabilities
  $all = array_merge(
    FICTIONEER_BASE_CAPABILITIES,
    FICTIONEER_STORY_CAPABILITIES,
    FICTIONEER_CHAPTER_CAPABILITIES,
    FICTIONEER_COLLECTION_CAPABILITIES,
    FICTIONEER_RECOMMENDATION_CAPABILITIES
  );

  // Administrator
  $administrator = get_role( 'administrator' );

  foreach ( $all as $cap ) {
    $administrator->add_cap( $cap );
  }

  // Editor
  $editor = get_role( 'editor' );
  $editor_caps = array_merge(
    // Base
    array(
      'fcn_read_others_media',
      'fcn_edit_others_media',
      'fcn_delete_others_media',
      'fcn_admin_profile_access',
      'fcn_adminbar_access',
      'fcn_dashboard_access'
    ),
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
    'fcn_admin_profile_access',
    'fcn_adminbar_access',
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
    'edit_published_fcn_recommendations'
  );

  foreach ( $author_caps as $cap ) {
    $author->add_cap( $cap );
  }

  // Contributor
  $contributor = get_role( 'contributor' );
  $contributor_caps = array(
    // Base
    'fcn_admin_profile_access',
    'fcn_adminbar_access',
    // Stories
    'read_fcn_story',
    'edit_fcn_stories',
    'delete_fcn_stories',
    // Chapters
    'read_fcn_chapter',
    'edit_fcn_chapters',
    'delete_fcn_chapters',
    // Collections
    'read_fcn_collection',
    'edit_fcn_collections',
    'delete_fcn_collections',
    // Recommendations
    'read_fcn_recommendation',
    'edit_fcn_recommendations',
    'delete_fcn_recommendations'
  );

  foreach ( $contributor_caps as $cap ) {
    $contributor->add_cap( $cap );
  }

  // Moderator
  fictioneer_add_moderator_role();
  $moderator = get_role( 'fcn_moderator' );
  $moderator_caps = array(
    // Base
    'fcn_admin_profile_access',
    'fcn_adminbar_access',
    // Stories
    'read_fcn_story',
    // Chapters
    'read_fcn_chapter',
    // Collections
    'read_fcn_collection',
    // Recommendations
    'read_fcn_recommendation'
  );

  foreach ( $moderator_caps as $cap ) {
    $moderator->add_cap( $cap );
  }

  // Subscriber
  $subscriber = get_role( 'subscriber' );
  $subscriber_caps = array(
    // Base
    'fcn_admin_profile_access',
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
add_action( 'init', 'fictioneer_initialize_roles' );







// =============================================================================
// ROLE CUSTOMIZATION ACTIONS
// =============================================================================

// See ./includes/functions/_admin.php

// =============================================================================
// RESTRICT SUBSCRIBERS ROLE
// =============================================================================

/**
 * Prevent subscribers from accessing the admin panel
 *
 * Subscribers have a custom frontend user profile and therefore no need to
 * access the admin panel at all. Only higher roles are granted access, although
 * admin-specific actions such as AJAX requests are still permitted.
 *
 * @since Fictioneer 4.0
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

if ( get_option( 'fictioneer_block_subscribers_from_admin' ) ) {
  add_action( 'init', 'fictioneer_block_subscribers_from_admin' );
}

/**
 * Remove admin dashboard widgets for subscribers
 *
 * @since Fictioneer 5.0
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

/**
 * Remove admin menu pages for subscribers
 *
 * @since Fictioneer 5.0
 */

function fictioneer_reduce_subscriber_admin_panel() {
  // Remove menu pages
  remove_menu_page( 'index.php' ); // Dashboard
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

/**
 * Remove admin menu pages for moderators
 *
 * @since Fictioneer 5.0
 */

function fictioneer_reduce_moderator_admin_panel() {
  // Remove menu pages
  remove_menu_page( 'edit.php' );
  remove_menu_page( 'tools.php' );
  remove_menu_page( 'plugins.php' );
  remove_menu_page( 'themes.php' );
}

/**
 * Remove admin dashboard widgets for moderators
 *
 * @since Fictioneer 5.0
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

/**
 * Remove items from admin bar for moderators
 *
 * @since Fictioneer 5.0
 */

function fictioneer_reduce_moderator_admin_bar() {
  global $wp_admin_bar;

  // Remove all [+New] items
  $wp_admin_bar->remove_node( 'new-content' );
}

/**
 * Restrict access to admin pages/actions for moderators
 *
 * @since Fictioneer 5.0
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

// Apply restrictions to fcn_moderators
if (
  fictioneer_has_role( get_current_user_id(), 'fcn_moderator' ) &&
  ! user_can( get_current_user_id(), 'administrator' )
) {
  add_action( 'admin_menu', 'fictioneer_reduce_moderator_admin_panel' );
  add_action( 'current_screen', 'fictioneer_restrict_moderator_menu_access' );
  add_action( 'wp_dashboard_setup', 'fictioneer_reduce_moderator_dashboard_widgets' );
  add_action( 'admin_bar_menu', 'fictioneer_reduce_moderator_admin_bar', 999 );
}

// =============================================================================
// RESTRICT EDITOR ROLE
// =============================================================================

/**
 * Restrict access to admin pages/actions for editors
 *
 * @since Fictioneer 5.0
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

/**
 * Remove items from admin bar for editors
 *
 * @since Fictioneer 5.0
 */

function fictioneer_reduce_editor_admin_bar() {
  global $wp_admin_bar;

  // Remove comments
  $wp_admin_bar->remove_node( 'comments' );
}

/**
 * Remove admin dashboard widgets for editors
 *
 * @since Fictioneer 5.0
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

/**
 * Hide comments utilities in admin dashboard for editors
 *
 * @since Fictioneer 5.0
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

/**
 * Remove admin dashboard widgets for contributors
 *
 * @since Fictioneer 5.0
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

/**
 * Remove items from admin bar for contributors
 *
 * @since Fictioneer 5.0
 */

function fictioneer_reduce_contributor_admin_bar() {
  global $wp_admin_bar;

  // Remove comments
  $wp_admin_bar->remove_node( 'comments' );
}

/**
 * Hide comments utilities in admin dashboard for contributors
 *
 * @since Fictioneer 5.0
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

// Apply restrictions to editors
if (
  fictioneer_has_role( get_current_user_id(), 'contributor' ) &&
  ! user_can( get_current_user_id(), 'administrator' )
) {
  add_action( 'wp_dashboard_setup', 'fictioneer_reduce_contributor_dashboard_widgets' );
  add_action( 'admin_enqueue_scripts', 'fictioneer_hide_contributor_comments_utilities', 99 );
  add_action( 'admin_bar_menu', 'fictioneer_reduce_contributor_admin_bar', 999 );
}

// =============================================================================
// RESTRICT AUTHOR ROLE
// =============================================================================

/**
 * Remove admin dashboard widgets for authors
 *
 * @since Fictioneer 5.5.0
 * @link https://developer.wordpress.org/apis/handbook/dashboard-widgets/
 */

function fictioneer_reduce_author_dashboard_widgets() {
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

/**
 * Remove items from admin bar for authors
 *
 * @since Fictioneer 5.5.0
 */

function fictioneer_reduce_author_admin_bar() {
  global $wp_admin_bar;

  // Remove comments
  $wp_admin_bar->remove_node( 'comments' );
}

/**
 * Hide comments utilities in admin dashboard for authors
 *
 * @since Fictioneer 5.5.0
 */

function fictioneer_hide_author_comments_utilities() {
  wp_add_inline_script(
    'fictioneer-admin-script',
    "jQuery(function($) {
      $('.dashboard-comment-wrap > .row-actions').remove();
      $('#latest-comments > .subsubsub').remove();
      $('#dashboard_right_now .comment-count').remove();
    });"
  );
}

// Apply restrictions to authors
if (
  fictioneer_has_role( get_current_user_id(), 'author' ) &&
  ! user_can( get_current_user_id(), 'administrator' )
) {
  add_action( 'wp_dashboard_setup', 'fictioneer_reduce_author_dashboard_widgets' );
  add_action( 'admin_enqueue_scripts', 'fictioneer_hide_author_comments_utilities', 99 );
  add_action( 'admin_bar_menu', 'fictioneer_reduce_author_admin_bar', 999 );
}

?>
