<?php

// =============================================================================
// FAST AJAX REQUESTS
// =============================================================================

if ( ! defined( 'FICTIONEER_FAST_AJAX_FUNCTIONS' ) ) {
  define(
    'FICTIONEER_FAST_AJAX_FUNCTIONS',
    array(
      // Bookmarks
      'fictioneer_ajax_save_bookmarks',
      // Follows
      'fictioneer_ajax_toggle_follow',
      'fictioneer_ajax_clear_my_follows',
      'fictioneer_ajax_mark_follows_read',
      'fictioneer_ajax_get_follows_notifications',
      'fictioneer_ajax_get_follows_list',
      // Reminders
      'fictioneer_ajax_toggle_reminder',
      'fictioneer_ajax_clear_my_reminders',
      'fictioneer_ajax_get_reminders_list',
      // Checkmarks
      'fictioneer_ajax_set_checkmark',
      'fictioneer_ajax_clear_my_checkmarks',
      'fictioneer_ajax_get_finished_checkmarks_list',
      // User
      'fictioneer_ajax_get_auth',
      'fictioneer_ajax_get_user_data',
      'fictioneer_ajax_get_avatar',
      // Admin
      'fictioneer_ajax_query_relationship_posts'
    )
  );
}

/**
 * Executes an AJAX action before initialization
 *
 * Fires after the theme constants have been defined, but skips
 * everything after that is not required by the action. Child
 * themes are not called and custom actions/filters not loaded.
 * Any customization must happen in a must-use plugin or the
 * config.php (which is bad practice).
 *
 * @since 5.6.3
 * @since 5.15.3 - Load theme textdomain.
 */

function fictioneer_do_fast_ajax() {
  $action = sanitize_text_field( $_REQUEST['action'] );

  // Allowed action?
  if (
    ! in_array( $action, FICTIONEER_FAST_AJAX_FUNCTIONS, true ) ||
    strpos( $action, 'fictioneer_ajax' ) !== 0
  ) {
    return;
  }

  // Translations
  load_theme_textdomain( 'fictioneer', get_template_directory() . '/languages' );

  // Include required files
  require_once __DIR__ . '/_utility.php';
  require_once __DIR__ . '/_helpers-query.php';
  require_once __DIR__ . '/_service-caching.php';
  require_once __DIR__ . '/_setup-types-and-terms.php';

  if ( get_option( 'fictioneer_enable_follows' ) && strpos( $action, '_follow' ) !== false ) {
    require_once __DIR__ . '/_helpers-templates.php';
    require_once __DIR__ . '/users/_follows.php';
  }

  if ( get_option( 'fictioneer_enable_reminders' ) && strpos( $action, '_reminder' ) !== false ) {
    require_once __DIR__ . '/_helpers-templates.php';
    require_once __DIR__ . '/users/_reminders.php';
  }

  if ( get_option( 'fictioneer_enable_checkmarks' ) && strpos( $action, '_checkmark' ) !== false ) {
    require_once __DIR__ . '/_helpers-templates.php';
    require_once __DIR__ . '/users/_checkmarks.php';
  }

  if ( get_option( 'fictioneer_enable_bookmarks' ) && strpos( $action, '_bookmarks' ) !== false ) {
    require_once __DIR__ . '/users/_bookmarks.php';
  }

  if ( strpos( $action, 'get_user_data' ) !== false ) {
    require_once __DIR__ . '/users/_user_data.php';
    require_once __DIR__ . '/users/_follows.php';
    require_once __DIR__ . '/users/_reminders.php';
    require_once __DIR__ . '/users/_checkmarks.php';
    require_once __DIR__ . '/users/_bookmarks.php';
    require_once __DIR__ . '/_helpers-templates.php';
  }

  if ( strpos( $action, '_list' ) !== false ) {
    require_once __DIR__ . '/_setup-wordpress.php';
  }

  if ( strpos( $action, '_fingerprint' ) !== false ) {
    require_once __DIR__ . '/users/_user_data.php';
  }

  if ( strpos( $action, '_avatar' ) !== false ) {
    require_once __DIR__ . '/users/_avatars.php';
  }

  // Register custom post types and taxonomies
  fictioneer_register_cpt_and_tax();

  // Skip cache checks
  if ( ! function_exists( 'fictioneer_caching_active' ) ) {
    function fictioneer_caching_active( $context = null ) { return false; };
  }

  if ( ! function_exists( 'fictioneer_private_caching_active' ) ) {
    function fictioneer_private_caching_active() { return false; };
  }

  // Skip cache purging
  if ( ! function_exists( 'fictioneer_refresh_post_caches' ) ) {
    function fictioneer_refresh_post_caches() { return; };
  }

  if ( $action === 'fictioneer_ajax_query_relationship_posts' ) {
    require_once __DIR__ . '/_helpers-templates.php';
    require_once __DIR__ . '/_setup-admin.php';
  }

  if ( $action === 'fictioneer_ajax_fcnen_search_content' ) {
    require_once __DIR__ . '/_helpers-templates.php';
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

if (
  defined( 'DOING_AJAX' ) && DOING_AJAX &&
  isset( $_REQUEST['fcn_fast_ajax'] ) &&
  isset( $_REQUEST['action'] )
) {
  fictioneer_do_fast_ajax();
}

// =============================================================================
// FAST AJAX COMMENT REQUESTS
// =============================================================================

if ( ! defined( 'FICTIONEER_FAST_AJAX_COMMENT_FUNCTIONS' ) ) {
  define(
    'FICTIONEER_FAST_AJAX_COMMENT_FUNCTIONS',
    array(
      'fictioneer_ajax_delete_my_comment',
      'fictioneer_ajax_moderate_comment',
      'fictioneer_ajax_report_comment',
      'fictioneer_ajax_get_comment_form',
      'fictioneer_ajax_get_comment_section',
      'fictioneer_ajax_submit_comment',
      'fictioneer_ajax_edit_comment'
    )
  );
}

/**
 * Executes an AJAX comment action before initialization
 *
 * Fires after the theme constants have been defined, but skips
 * everything after that is not required by the action. Child
 * themes are not called and custom actions/filters not loaded.
 * Any customization must happen in a must-use plugin or the
 * config.php (which is bad practice).
 *
 * @since 5.6.3
 * @since 5.15.3 - Load theme textdomain.
 */

function fictioneer_do_fast_comment_ajax() {
  $action = sanitize_text_field( $_REQUEST['action'] );

  // Allowed action?
  if ( ! in_array( $action, FICTIONEER_FAST_AJAX_COMMENT_FUNCTIONS, true ) ) {
    return;
  }

  // Translations
  load_theme_textdomain( 'fictioneer', get_template_directory() . '/languages' );

  // Include required files
  require_once __DIR__ . '/_utility.php'; // Obviously
  require_once __DIR__ . '/_setup-wordpress.php'; // Depends
  require_once __DIR__ . '/_setup-roles.php'; // Depends
  require_once __DIR__ . '/_service-caching.php'; // Maybe?
  require_once __DIR__ . '/_helpers-templates.php'; // Safe title
  require_once __DIR__ . '/comments/_comments_controller.php'; // Obviously
  require_once __DIR__ . '/comments/_comments_moderation.php'; // Obviously
  require_once __DIR__ . '/comments/_comments_ajax.php'; // Obviously

  // Moderating needs less, otherwise include everything related to comments
  if ( strpos( $action, '_moderate_comment' ) === false ) {
    require_once __DIR__ . '/_module-discord.php'; // Notifications
    require_once __DIR__ . '/_setup-types-and-terms.php'; // Depends
    require_once __DIR__ . '/_module-oauth.php'; // Login buttons
    require_once __DIR__ . '/users/_user_data.php'; // Obviously
    require_once __DIR__ . '/users/_avatars.php'; // Obviously
    require_once __DIR__ . '/comments/_comments_form.php'; // Obviously
    require_once __DIR__ . '/comments/_comments_threads.php'; // Obviously

    // Register custom post types and taxonomies
    fictioneer_register_cpt_and_tax();
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

if (
  get_option( 'fictioneer_enable_fast_ajax_comments' ) &&
  defined( 'DOING_AJAX' ) && DOING_AJAX &&
  strpos( $_REQUEST['action'] ?? '', 'fictioneer_ajax' ) === 0 &&
  strpos( $_REQUEST['action'] ?? '', 'comment' ) !== false
) {
  fictioneer_do_fast_comment_ajax();
}

// =============================================================================
// FAST AJAX STORY COMMENTS REQUESTS
// =============================================================================

// Fast REST request to get story comments
if (
  get_option( 'fictioneer_enable_fast_ajax_comments' ) &&
  strpos( $_SERVER['REQUEST_URI'], 'wp-json/fictioneer/v1/get_story_comments' ) !== false
) {
  // Translations
  load_theme_textdomain( 'fictioneer', get_template_directory() . '/languages' );

  // Include required files
  require_once __DIR__ . '/_utility.php';
  require_once __DIR__ . '/_setup-wordpress.php';
  require_once __DIR__ . '/_setup-roles.php';
  require_once __DIR__ . '/users/_user_data.php';
  require_once __DIR__ . '/users/_avatars.php';
  require_once __DIR__ . '/comments/_comments_controller.php';
  require_once __DIR__ . '/comments/_comments_threads.php';
  require_once __DIR__ . '/comments/_story_comments.php';

  // Build fake REST request
  $rest_request = new WP_REST_Request( 'GET', 'fictioneer/v1/get_story_comments' );
  $rest_request->set_param( 'post_id', absint( $_REQUEST['post_id'] ?? 0 ) );
  $rest_request->set_param( 'page', absint( $_REQUEST['page'] ?? 1 ) );

  // Get comments with fake REST request
  $response = fictioneer_rest_get_story_comments( $rest_request );

  // Extract data from WP_REST_Response
  wp_send_json( $response->get_data(), $response->get_status() );

  // Terminate in case something goes wrong
  die();
}
