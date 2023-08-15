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
    'fcn_custom_page_css',
    'fcn_custom_epub_css',
    'fcn_seo_meta',
    'fcn_make_sticky',
    'fcn_show_badge',
    'fcn_edit_permalink',
    'fcn_all_blocks'
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
 * @since Fictioneer 5.6.0
 *
 * @param boolean $force  Optional. Whether to force initialization.
 */

function fictioneer_initialize_roles( $force = false ) {
  // Only do this once...
  $administrator = get_role( 'administrator' );

  // If this capability is missing, the roles have not yet been initialized
  if (
    ( $administrator && ! in_array( 'fcn_admin_panel_access', array_keys( $administrator->capabilities ) ) ||
    $force
  ) ) {
    fictioneer_setup_roles();
  }
}
add_action( 'init', 'fictioneer_initialize_roles' );

/**
 * Build user roles with custom capabilities
 *
 * @since Fictioneer 5.6.0
 */

function fictioneer_setup_roles() {
  // Capabilities
  $legacy_removal = ['delete_pages', 'delete_published_pages', 'edit_pages', 'edit_published_pages', 'publish_pages'];

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

  $administrator->remove_cap( 'fcn_edit_only_others_comments' );
  $administrator->remove_cap( 'fcn_reduced_profile' );
  $administrator->remove_cap( 'fcn_allow_self_delete' );

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
      'moderate_comments',         // Legacy restore
      'edit_comment',              // Legacy restore
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

  $editor->remove_cap( 'fcn_edit_only_others_comments' );
  $editor->remove_cap( 'fcn_reduced_profile' );
  $editor->remove_cap( 'fcn_allow_self_delete' );

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

  foreach ( $legacy_removal as $cap ) {
    $author->remove_cap( $cap ); // Legacy restore
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
    'delete_fcn_recommendations',
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

  foreach ( $legacy_removal as $cap ) {
    $contributor->remove_cap( $cap ); // Legacy restore
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
 * @since Fictioneer 5.0
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
    'fcn_edit_only_others_comments' => true,
    'fcn_upload_limit' => true,
    'fcn_upload_restrictions' => true,
    'fcn_show_badge' => true,
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
    $moderator->remove_cap( 'fcn_reduced_profile' );
    $moderator->remove_cap( 'fcn_allow_self_delete' );

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

// Add templates ('name' => true) to the array you want to allow
if ( ! defined( 'CHILD_ALLOWED_PAGE_TEMPLATES' ) ) {
  define( 'CHILD_ALLOWED_PAGE_TEMPLATES', [] );
}

// No restriction can be applied to administrators
if ( ! current_user_can( 'manage_options' ) ) {
  $post_types = ['post', 'fcn_story', 'fcn_chapter', 'fcn_collection', 'page', 'fcn_recommendation'];

  // === FCN_ADMINBAR_ACCESS ===================================================

  /**
   * Admin bar
   */

  if ( ! current_user_can( 'fcn_adminbar_access' ) ) {
    add_filter( 'show_admin_bar', '__return_false', 9999 );
  }

  // === FCN_ADMIN_PANEL_ACCESS ================================================

  /**
   * Prevent access to the admin panel
   *
   * @since Fictioneer 5.6.0
   */

  function fictioneer_restrict_admin_panel() {
    // Redirect back to Home
    if (
      is_admin() &&
      ! ( defined( 'DOING_AJAX' ) && DOING_AJAX )
    ) {
      wp_redirect( home_url() );
      exit;
    }
  }

  if ( ! current_user_can( 'fcn_admin_panel_access' ) ) {
    add_filter( 'init', 'fictioneer_restrict_admin_panel', 9999 );
  }

  // === FCN_DASHBOARD_ACCESS ==================================================

  /**
   * Remove admin dashboard widgets
   *
   * @since Fictioneer 5.6.0
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
   * @since Fictioneer 5.6.0
   */

  function fictioneer_remove_dashboard_menu() {
    remove_menu_page( 'index.php' );
    remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
  }

  /**
   * Redirect from dashboard to user profile
   *
   * @since Fictioneer 5.6.0
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
   * @since Fictioneer 5.6.0
   */

  function fictioneer_remove_dashboard_from_admin_bar() {
    global $wp_admin_bar;

    $wp_admin_bar->remove_menu( 'dashboard' );
  }

  if ( ! current_user_can( 'fcn_dashboard_access' ) ) {
    add_action( 'wp_before_admin_bar_render', 'fictioneer_remove_dashboard_from_admin_bar', 9999 );
    add_action( 'wp_dashboard_setup', 'fictioneer_remove_dashboard_widgets', 9999 );
    add_action( 'admin_menu', 'fictioneer_remove_dashboard_menu', 9999 );
    add_action( 'admin_init', 'fictioneer_skip_dashboard', 9999 );
  }

  // === FCN_SELECT_PAGE_TEMPLATE ==============================================

  /**
   * Filters the page template selection
   *
   * @since Fictioneer 5.6.0
   *
   * @param array $templates  Array of templates ('name' => true).
   *
   * @return array Array of allowed templates.
   */

  function fictioneer_disallow_page_template_select( $templates ) {
    return array_intersect_key( $templates, CHILD_ALLOWED_PAGE_TEMPLATES );
  }

  /**
   * Makes sure only allowed templates are selected
   *
   * @since Fictioneer 5.6.0
   *
   * @param int $post_id  ID of the saved post.
   */

  function fictioneer_restrict_page_templates( $post_id ) {
    // Do nothing if...
    if ( get_post_type( $post_id ) !== 'page' ) {
      return;
    }

    // Get currently selected template
    $selected_template = get_post_meta( $post_id, '_wp_page_template', true );

    // Remove if not allowed
    if ( ! in_array( $selected_template, CHILD_ALLOWED_PAGE_TEMPLATES ) ) {
      update_post_meta( $post_id, '_wp_page_template', '' );
    }
  }

  if ( ! current_user_can( 'fcn_select_page_template' ) ) {
    add_action( 'save_post', 'fictioneer_restrict_page_templates', 9999 );
    add_filter( 'theme_page_templates', 'fictioneer_disallow_page_template_select', 9999 );
  }

  // === MODERATE_COMMENTS =====================================================

  /**
   * Remove comment item from admin bar
   *
   * @since Fictioneer 5.6.0
   */

  function fictioneer_remove_comments_from_admin_bar() {
    global $wp_admin_bar;

    $wp_admin_bar->remove_node( 'comments' );
  }

  /**
   * Remove comments menu
   *
   * @since Fictioneer 5.6.0
   */

  function fictioneer_remove_comments_menu_page() {
    remove_menu_page( 'edit-comments.php' );
  }

  /**
   * Restrict menu access for non-administrators
   *
   * @since Fictioneer 5.6.0
   */

  function fictioneer_restrict_comment_edit() {
    // Setup
    $screen = get_current_screen();

    if ( $screen->id === 'edit-comments' ) {
      wp_die( __( 'Access denied.', 'fictioneer' ) );
    }
  }

  /**
   * Remove comments column
   *
   * @since Fictioneer 5.6.0
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
    add_action( 'admin_menu', 'fictioneer_remove_comments_menu_page', 9999 );
    add_action( 'admin_bar_menu', 'fictioneer_remove_comments_from_admin_bar', 9999 );
    add_action( 'current_screen', 'fictioneer_restrict_comment_edit', 9999 );
    add_filter( 'manage_posts_columns', 'fictioneer_remove_comments_column', 9999 );
    add_filter( 'manage_pages_columns', 'fictioneer_remove_comments_column', 9999 );
  }

  /**
   * Only allow editing of comments
   *
   * @since Fictioneer 5.6.0
   *
   * @param array $all_caps  An array of all the user's capabilities.
   * @param array $cap      Primitive capabilities that are being checked.
   * @param array $args     Arguments passed to the capabilities check.
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

  if ( current_user_can( 'moderate_comments' ) && current_user_can( 'fcn_edit_only_others_comments' ) ) {
    add_filter( 'user_has_cap', 'fictioneer_edit_only_comments', 9999, 3 );
  }

  // === MANAGE_OPTIONS ========================================================

  /**
   * Reduce admin panel
   *
   * @since Fictioneer 5.6.0
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
   * @since Fictioneer 5.6.0
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
      wp_die( __( 'Access denied.', 'fictioneer' ) );
    }
  }

  if ( ! current_user_can( 'manage_options' ) ) {
    add_action( 'admin_menu', 'fictioneer_reduce_admin_panel', 9999 );
    add_action( 'current_screen', 'fictioneer_restrict_admin_only_pages', 9999 );
  }

  /**
   * Removes search and filter ID input field
   *
   * @since 5.6.0
   *
   * @param array $fields  An array of fields to be rendered.
   *
   * @return array The modified array.
   */


  function fictioneer_remove_filter_search_id_input( $fields ) {
    // Fields to remove
    $field_keys = ['field_60040fa3bc4f1']; // fictioneer_filter_and_search_id

    // Remove fields from the fields array
    foreach ( $fields as $key => &$field ) {
      if ( in_array( $field['key'], $field_keys ) ) {
        unset( $fields[ $key ] );
      }
    }

    // Return modified fields array
    return $fields;
  }

  if ( ! current_user_can( 'manage_options' ) ) {
    add_filter( 'acf/update_value/name=fictioneer_filter_and_search_id', 'fictioneer_acf_prevent_value_update', 9999, 3 );
    add_filter( 'acf/pre_render_fields', 'fictioneer_remove_filter_search_id_input', 9999 );
  }

  // === UPDATE_CORE ===========================================================

  /**
   * Remove update notice
   *
   * @since Fictioneer 5.6.0
   */

  function fictioneer_remove_update_notice(){
    remove_action( 'admin_notices', 'update_nag', 3 );
  }

  if ( ! current_user_can( 'update_core' ) ) {
    add_action( 'admin_head', 'fictioneer_remove_update_notice', 9999 );
  }

  // === FCN_SHORTCODES ========================================================

  /**
   * Strip shortcodes before saving
   *
   * @since Fictioneer 5.6.0
   *
   * @param string $content  The content to be saved.
   *
   * @return string The cleaned content.
   */

  function fictioneer_strip_shortcodes( $content ) {
    return strip_shortcodes( $content );
  }

  if ( ! current_user_can( 'fcn_shortcodes' ) ) {
    add_filter( 'content_save_pre', 'fictioneer_strip_shortcodes', 9999 );
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
  add_filter( 'pre_get_posts', 'fictioneer_edit_others_fictioneer_posts', 9999 );

  // === FCN_READ_OTHERS_FILES =================================================

  /**
   * Prevent users from seeing uploaded files of others
   *
   * @since Fictioneer 5.6.0
   *
   * @param WP_Query $query  The queried attachments.
   */

  function fictioneer_read_others_files( $query ) {
    global $current_user, $pagenow;

    // Only affect media library on admin side
    if(
      'admin-ajax.php' != $pagenow ||
      $_REQUEST['action'] != 'query-attachments'
    ) {
      return;
    }

    // Limit to author (uploader)
    $query->set( 'author', $current_user->ID );
  }

  /**
   * Prevent users from seeing uploaded files of others in the list view
   *
   * @since Fictioneer 5.6.0
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

  if ( ! current_user_can( 'fcn_read_others_files' ) && is_admin() ) {
    add_action( 'pre_get_posts', 'fictioneer_read_others_files', 9999 );
    add_action( 'pre_get_posts', 'fictioneer_read_others_files_list_view', 9999 );
  }

  // === FCN_EDIT_OTHERS_FILES =================================================

  /**
   * User cannot edit the files of others
   *
   * @since Fictioneer 5.6.0
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
    add_filter( 'map_meta_cap', 'fictioneer_edit_others_files', 9999, 4 );
  }

  // === FCN_DELETE_OTHERS_FILES ===============================================

  /**
   * User cannot delete the files of others
   *
   * @since Fictioneer 5.6.0
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
   * @since Fictioneer 4.7
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
   * @since Fictioneer 4.7
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
   * @since Fictioneer 4.7
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
    add_filter( 'comment_email', '__return_false', 9999 );
    add_filter( 'get_comment_author_IP', '__return_empty_string', 9999 );
    add_filter( 'manage_users_columns', 'fictioneer_hide_users_columns', 9999 );
    add_filter( 'comment_row_actions', 'fictioneer_remove_quick_edit', 9999 );
    add_action( 'admin_enqueue_scripts', 'fictioneer_hide_private_data', 9999 );
  }

  // === FCN_REDUCED_PROFILE ===================================================

  /**
   * Hide subscriber profile blocks in admin panel
   *
   * @since 5.6.0
   */

  function fictioneer_remove_profile_blocks() {
    // Add CSS to hide blocks...
    echo '<style>.user-url-wrap, .user-description-wrap, .user-first-name-wrap, .user-last-name-wrap, .user-language-wrap, .user-admin-bar-front-wrap, .user-pass1-wrap, .user-pass2-wrap, .pw-weak, .user-generate-reset-link-wrap, #contextual-help-link-wrap, #your-profile > h2:first-of-type { display: none; }</style>';

    // Add JS to remove blocks...
    echo '<script>document.addEventListener("DOMContentLoaded", () =>{document.querySelectorAll(".user-pass1-wrap, .user-pass2-wrap, .pw-weak, .user-generate-reset-link-wrap").forEach(element => {console.log(element); element.remove();});});</script>';
  }

  if ( current_user_can( 'fcn_reduced_profile' ) ) {
    add_filter( 'wp_is_application_passwords_available', '__return_false', 9999 );
    add_filter( 'user_contactmethods', '__return_empty_array', 9999 );
    add_action( 'admin_head-profile.php', 'fictioneer_remove_profile_blocks', 9999 );
    remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
  }

  // === FCN_CUSTOM_PAGE_CSS ===================================================

  /**
   * Removes custom page/story CSS input fields
   *
   * @since 5.6.0
   *
   * @param array $fields  An array of fields to be rendered.
   *
   * @return array The modified array.
   */

  function fictioneer_remove_custom_page_css_inputs( $fields ) {
    // Fields to remove
    $field_keys = array(
      'field_636d81d34cab1', // fictioneer_story_css
      'field_621b5610818d2' // fictioneer_custom_css
    );

    // Remove fields from the fields array
    foreach ( $fields as $key => &$field ) {
      if ( in_array( $field['key'], $field_keys ) ) {
        unset( $fields[ $key ] );
      }
    }

    // Return modified fields array
    return $fields;
  }

  if ( ! current_user_can( 'fcn_custom_page_css' ) ) {
    add_filter( 'acf/update_value/name=fictioneer_story_css', 'fictioneer_acf_prevent_value_update', 9999, 3 );
    add_filter( 'acf/update_value/name=fictioneer_custom_css', 'fictioneer_acf_prevent_value_update', 9999, 3 );
    add_filter( 'acf/pre_render_fields', 'fictioneer_remove_custom_page_css_inputs', 9999 );
  }

  // === FCN_CUSTOM_EPUB_CSS ===================================================

  /**
   * Removes the custom ePUB CSS input field
   *
   * @since 5.6.0
   *
   * @param array $fields  An array of fields to be rendered.
   *
   * @return array The modified array.
   */

  function fictioneer_remove_custom_epub_css_input( $fields ) {
    // Fields to remove
    $field_keys = ['field_60edba4ff33f8']; // fictioneer_story_epub_custom_css

    // Remove fields from the fields array
    foreach ( $fields as $key => &$field ) {
      if ( in_array( $field['key'], $field_keys ) ) {
        unset( $fields[ $key ] );
      }
    }

    // Return modified fields array
    return $fields;
  }

  if ( ! current_user_can( 'fcn_custom_epub_css' ) ) {
    add_filter( 'acf/update_value/name=fictioneer_story_epub_custom_css', 'fictioneer_acf_prevent_value_update', 9999, 3 );
    add_filter( 'acf/pre_render_fields', 'fictioneer_remove_custom_epub_css_input', 9999 );
  }

  // === FCN_MAKE_STICKY =======================================================

  /**
   * Removes the custom ePUB CSS input field
   *
   * @since 5.6.0
   *
   * @param array $fields  An array of fields to be rendered.
   *
   * @return array The modified array.
   */

  function fictioneer_remove_make_sticky_input( $fields ) {
    // Fields to remove
    $field_keys = ['field_619a91f85da9d']; // fictioneer_story_sticky

    // Remove fields from the fields array
    foreach ( $fields as $key => &$field ) {
      if ( in_array( $field['key'], $field_keys ) ) {
        unset( $fields[ $key ] );
      }
    }

    // Return modified fields array
    return $fields;
  }

  if ( ! current_user_can( 'fcn_make_sticky' ) ) {
    add_filter( 'acf/update_value/name=fictioneer_story_sticky', 'fictioneer_acf_prevent_value_update', 9999, 3 );
    add_filter( 'acf/pre_render_fields', 'fictioneer_remove_make_sticky_input', 9999 );
    add_action( 'post_stuck', 'unstick_post', 9999 ); // lol
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
    add_filter( 'upload_size_limit', 'fictioneer_upload_size_limit', 9999 );
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
    add_filter( 'wp_handle_upload_prefilter', 'fictioneer_upload_restrictions', 9999 );
  }

  // === FCN_ALL_BLOCKS ========================================================

  /**
 * Restrict the use of specific Gutenberg blocks
 *
 * @since 5.6.0
 *
 * @param array $data  The array of post data being saved.
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

    // Continue with cleaned-up data
    return $data;
  }

  /**
   * Restricts the block categories available in the Gutenberg editor
   *
   * @return array Array of block categories.
   */

  function fictioneer_restrict_block_categories() {
    return array(
      array(
        'slug'  => 'text',
        'title' => _x( 'Text', 'block category' ),
        'icon'  => null,
      ),
      array(
        'slug'  => 'media',
        'title' => _x( 'Media', 'block category' ),
        'icon'  => null,
      ),
      array(
        'slug'  => 'embed',
        'title' => _x( 'Embeds', 'block category' ),
        'icon'  => null,
      )
    );
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
    add_filter( 'block_categories_all', 'fictioneer_restrict_block_categories', 9999 );
    add_filter( 'allowed_block_types_all', 'fictioneer_restrict_block_types', 9999 );
    add_filter( 'wp_insert_post_data', 'fictioneer_remove_restricted_block_content', 9999 );
  }
}

?>
