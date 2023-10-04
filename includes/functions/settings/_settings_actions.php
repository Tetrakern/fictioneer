<?php

// =============================================================================
// UTILITY
// =============================================================================

/**
 * Verify a tool action request
 *
 * @since Fictioneer 5.2.5
 *
 * @param string $action Name of the tool action.
 */

function fictioneer_verify_tool_action( $action ) {
  // Verify request
  if ( ! check_admin_referer( $action, 'fictioneer_nonce' ) ) {
    wp_die( __( 'Nonce verification failed. Please try again.', 'fictioneer' ) );
  }

  // Guard
  if ( ! current_user_can( 'manage_options' ) || ! is_admin() ) {
    wp_die( __( 'You do not have permission to access this page.', 'fictioneer' ) );
  }
}

/**
 * Finish a tool action and perform a redirect
 *
 * @since Fictioneer 5.2.5
 *
 * @param string $notice Optional. The notice message to include in the redirect URL.
 * @param string $type   Optional. The type of notice. Default 'success'.
 */

function fictioneer_finish_tool_action( $notice = '', $type = 'success' ) {
  // Setup
  $notice = empty( $notice ) ? [] : array( $type => $notice );

  // Redirect
  wp_safe_redirect( add_query_arg( $notice, wp_get_referer() ) );

  // Terminate
  exit();
}

if ( ! function_exists( 'fictioneer_get_default_genres' ) ) {
  /**
   * Return array of default genre from decoded JSON
   *
   * @since Fictioneer 4.7
   *
	 * @param array Decoded JSON with default genres and meta data.
   */

	function fictioneer_get_default_genres() {
		return json_decode( file_get_contents( get_template_directory_uri() . '/json/genres.json' ) );
	}
}

if ( ! function_exists( 'fictioneer_get_default_tags' ) ) {
  /**
   * Return array of default tags from decoded JSON
   *
   * @since Fictioneer 4.7
   *
	 * @param array Decoded JSON with default tags and meta data.
   */

	function fictioneer_get_default_tags() {
		return json_decode( file_get_contents( get_template_directory_uri() . '/json/tags.json' ) );
	}
}

// =============================================================================
// NOTICES
// =============================================================================

if ( ! defined( 'FICTIONEER_ADMIN_SETTINGS_NOTICES' ) ) {
  define(
		'FICTIONEER_ADMIN_SETTINGS_NOTICES',
		array(
			'fictioneer-story-tags-to-genres' => __( 'All story tags have been converted to genres.', 'fictioneer' ),
			'fictioneer-duplicate-tags-to-genres' => __( 'All story tags have been duplicated to genres.', 'fictioneer' ),
			'fictioneer-chapter-tags-to-genres' => __( 'All chapter tags have been converted to genres.', 'fictioneer' ),
			'fictioneer-duplicate-tags-to-genres' => __( 'All chapter tags have been duplicated to genres.', 'fictioneer' ),
			'fictioneer-append-default-genres' => __( 'Default genres have been added to the list.', 'fictioneer' ),
			'fictioneer-append-default-tags' => __( 'Default tags have been added to the list.', 'fictioneer' ),
			'fictioneer-remove-unused-tags' => __( 'All unused tags have been removed.', 'fictioneer' ),
			'fictioneer-deleted-epubs' => __( 'All ePUB files have been deleted.', 'fictioneer' ),
			'fictioneer-purge-theme-caches' => __( 'Theme caches have been purged.', 'fictioneer' ),
			'fictioneer-added-moderator-role' => __( 'Moderator role has been added.', 'fictioneer' ),
      'fictioneer-not-added-moderator-role' => __( 'Moderator role could not be added or already exists.', 'fictioneer' ),
			'fictioneer-removed-moderator-role' => __( 'Moderator role has been removed.', 'fictioneer' ),
      'fictioneer-reset-post-relationship-registry' => __( 'Post relationship registry reset.', 'fictioneer' ),
      'fictioneer-fix-users' => __( 'This function does currently not cover any issues.', 'fictioneer' ),
      'fictioneer-fix-stories' => __( 'This function does currently not cover any issues.', 'fictioneer' ),
      'fictioneer-fix-chapters' => __( 'This function does currently not cover any issues.', 'fictioneer' ),
      'fictioneer-fix-recommendations' => __( 'This function does currently not cover any issues.', 'fictioneer' ),
      'fictioneer-fix-collections' => __( 'This function does currently not cover any issues.', 'fictioneer' ),
      'fictioneer-fix-pages' => __( 'This function does currently not cover any issues.', 'fictioneer' ),
      'fictioneer-fix-posts' => __( 'This function does currently not cover any issues.', 'fictioneer' ),
      'fictioneer-updated-role-caps' => __( 'Role capabilities have been updated.', 'fictioneer' ),
      'fictioneer-updated-editor-caps' => __( 'Editor capabilities have been updated.', 'fictioneer' ),
      'fictioneer-updated-moderator-caps' => __( 'Moderator capabilities have been updated.', 'fictioneer' ),
      'fictioneer-updated-author-caps' => __( 'Author capabilities have been updated.', 'fictioneer' ),
      'fictioneer-updated-contributor-caps' => __( 'Contributor capabilities have been updated.', 'fictioneer' ),
      'fictioneer-updated-subscriber-caps' => __( 'Subscriber capabilities have been updated.', 'fictioneer' ),
      'fictioneer-roles-initialized' => __( 'Theme roles initialized.', 'fictioneer' ),
      'fictioneer-added-role' => __( 'Role added.', 'fictioneer' ),
      'fictioneer-not-added-role' => __( 'Error. Role could not be added.', 'fictioneer' ),
      'fictioneer-role-already-exists' => __( 'Error. Role (or sanitized role slug) already exists.', 'fictioneer' ),
      'fictioneer-renamed-role' => __( 'Role renamed.', 'fictioneer' ),
      'fictioneer-not-renamed-role' => __( 'Error. Role could not be renamed.', 'fictioneer' ),
      'fictioneer-removed-role' => __( 'Role removed.', 'fictioneer' ),
      'fictioneer-not-removed-role' => __( 'Error. Role could not be removed.', 'fictioneer' ),
      'fictioneer-db-optimization-preview' => __( '%s superfluous rows found. Please backup your database before performing any optimization.', 'fictioneer' ),
      'fictioneer-db-optimization' => __( '%s superfluous rows have been deleted.', 'fictioneer' ),
      'fictioneer-add-story-hidden' => __( 'The "fictioneer_story_hidden" meta field has been appended with value 0.', 'fictioneer' ),
      'fictioneer-add-story-sticky' => __( 'The "fictioneer_story_sticky" meta field has been appended with value 0.', 'fictioneer' ),
      'fictioneer-add-chapter-hidden' => __( 'The "fictioneer_chapter_hidden" meta field has been appended with value 0.', 'fictioneer' )
		)
	);
}

/**
 * Output admin settings notices
 *
 * @since Fictioneer 5.2.5
 */

function fictioneer_admin_settings_notices() {
  // Get query vars
  $success = $_GET['success'] ?? null;
  $failure = $_GET['failure'] ?? null;
  $info = $_GET['info'] ?? null;
  $data = $_GET['data'] ?? '';

  // Has success notice?
  if ( ! empty( $success ) && isset( FICTIONEER_ADMIN_SETTINGS_NOTICES[ $success ] ) ) {
    echo '<div class="notice notice-success is-dismissible"><p>' . sprintf(
      FICTIONEER_ADMIN_SETTINGS_NOTICES[ $success ],
      esc_html( $data )
    ) . '</p></div>';
  }

  // Has failure notice?
  if ( ! empty( $failure ) && isset( FICTIONEER_ADMIN_SETTINGS_NOTICES[ $failure ] ) ) {
    echo '<div class="notice notice-error is-dismissible"><p>' . sprintf(
      FICTIONEER_ADMIN_SETTINGS_NOTICES[ $failure ],
      esc_html( $data )
    ) . '</p></div>';
  }

  // Has info notice?
  if ( ! empty( $info ) && isset( FICTIONEER_ADMIN_SETTINGS_NOTICES[ $info ] ) ) {
    echo '<div class="notice notice-info is-dismissible"><p>' . sprintf(
      FICTIONEER_ADMIN_SETTINGS_NOTICES[ $info ],
      esc_html( $data )
    ) . '</p></div>';
  }
}
add_action( 'admin_notices', 'fictioneer_admin_settings_notices' );

// =============================================================================
// EPUB ACTIONS
// =============================================================================

/**
 * Delete all generated ePUBs in the ./uploads/epubs/ directory
 *
 * @since Fictioneer 5.2.5
 */

function fictioneer_delete_all_epubs() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_delete_all_epubs' );

  // Setup
  $epub_dir = wp_upload_dir()['basedir'] . '/epubs/';
  $files = list_files( $epub_dir, 1 );

  // Loop over all ePUBs in directory
  foreach ( $files as $file ) {
    $info = pathinfo( $file );

    if ( is_file( $file ) && $info['extension'] === 'epub' ) {
      wp_delete_file( $epub_dir . $info['filename'] . '.epub' );
    }
  }

  // Log
  fictioneer_log(
    __( 'Deleted all generated ePUBs from the server.', 'fictioneer' )
  );

  // Finish
  fictioneer_finish_tool_action( 'fictioneer-deleted-epubs' );
}
add_action( 'admin_post_fictioneer_delete_all_epubs', 'fictioneer_delete_all_epubs' );

// =============================================================================
// ROLE TOOLS ACTIONS
// =============================================================================

/**
 * Generate a URL with a nonce for a tool action
 *
 * @since Fictioneer 5.2.5
 *
 * @param string $action Action name.
 *
 * @return string Generated URL with the nonce.
 */

function fictioneer_tool_action( $action ) {
  return wp_nonce_url( admin_url( "admin-post.php?action={$action}" ), $action, 'fictioneer_nonce' );
}

/**
 * Add a moderator role
 *
 * @since Fictioneer 5.2.5
 */

function fictioneer_tools_add_moderator_role() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_add_moderator_role' );

  // Relay
  if ( fictioneer_add_moderator_role() ) {
    $notice = ['fictioneer-added-moderator-role', 'success'];

    // Log
    fictioneer_log( __( 'Moderator role added.', 'fictioneer' ) );
  } else {
    $notice = ['fictioneer-not-added-moderator-role', 'failure'];
  }

  // Finish
  fictioneer_finish_tool_action( $notice[0], $notice[1] );
}
add_action( 'admin_post_fictioneer_add_moderator_role', 'fictioneer_tools_add_moderator_role' );

/**
 * Initialize roles
 *
 * @since Fictioneer 5.6.0
 */

function fictioneer_tools_initialize_roles() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_initialize_roles' );

  // Force role initialization
  fictioneer_initialize_roles( true );

  // Finish
  fictioneer_finish_tool_action( 'fictioneer-roles-initialized' );
}
add_action( 'admin_post_fictioneer_initialize_roles', 'fictioneer_tools_initialize_roles' );

/**
 * Convert story tags to genres
 *
 * @since Fictioneer 5.2.5
 */

function fictioneer_tools_move_story_tags_to_genres() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_move_story_tags_to_genres' );

  // Relay
  fictioneer_convert_taxonomies( 'fcn_story', 'fcn_genre', 'post_tag', true, true );

  // Log
  fictioneer_log( __( 'Story tags converted to genres.', 'fictioneer' ) );

  // Finish
  fictioneer_finish_tool_action( 'fictioneer-story-tags-to-genres' );
}
add_action( 'admin_post_fictioneer_move_story_tags_to_genres', 'fictioneer_tools_move_story_tags_to_genres' );

// =============================================================================
// STORY TOOLS ACTIONS
// =============================================================================

/**
 * Duplicate story tags to genres
 *
 * @since Fictioneer 5.2.5
 */

function fictioneer_tools_duplicate_story_tags_to_genres() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_duplicate_story_tags_to_genres' );

  // Relay
  fictioneer_convert_taxonomies( 'fcn_story', 'fcn_genre', 'post_tag', true );

  // Log
  fictioneer_log( __( 'Story tags duplicated as genres.', 'fictioneer' ) );

  // Finish
  fictioneer_finish_tool_action( 'fictioneer-duplicate-tags-to-genres' );
}
add_action( 'admin_post_fictioneer_duplicate_story_tags_to_genres', 'fictioneer_tools_duplicate_story_tags_to_genres' );

/**
 * Purge story data caches
 *
 * @since Fictioneer 5.2.5
 */

function fictioneer_tools_purge_theme_caches() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_tools_purge_theme_caches' );

  // Setup
  $query_args = array(
    'post_type' => 'fcn_story',
    'posts_per_page' => -1,
    'fields' => 'ids',
    'update_term_meta_cache' => false
  );

  // Get all story IDs
  $stories_ids = get_posts( $query_args );

  // Loop over stories
  foreach ( $stories_ids as $story_id ) {
    delete_post_meta( $story_id, 'fictioneer_story_data_collection' );
  }

  // Transients
  fictioneer_purge_nav_menu_transients();
  fictioneer_delete_transients_like( 'fictioneer_' );

  // Log
  fictioneer_log( __( 'Purged theme caches.', 'fictioneer' ) );

  // Finish
  fictioneer_finish_tool_action( 'fictioneer-purge-theme-caches' );
}
add_action( 'admin_post_fictioneer_tools_purge_theme_caches', 'fictioneer_tools_purge_theme_caches' );

// =============================================================================
// CHAPTER TOOLS ACTIONS
// =============================================================================

/**
 * Convert chapter tags to genres
 *
 * @since Fictioneer 5.2.5
 */

function fictioneer_tools_move_chapter_tags_to_genres() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_move_chapter_tags_to_genres' );

  // Relay
  fictioneer_convert_taxonomies( 'fcn_chapter', 'fcn_genre', 'post_tag', true, true );

  // Log
  fictioneer_log( __( 'Chapter tags converted to genres.', 'fictioneer' ) );

  // Finish
  fictioneer_finish_tool_action( 'fictioneer-chapter-tags-to-genres' );
}
add_action( 'admin_post_fictioneer_move_chapter_tags_to_genres', 'fictioneer_tools_move_chapter_tags_to_genres' );

/**
 * Duplicate chapter tags to genres
 *
 * @since Fictioneer 5.2.5
 */

function fictioneer_tools_duplicate_chapter_tags_to_genres() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_duplicate_chapter_tags_to_genres' );

  // Relay
  fictioneer_convert_taxonomies( 'fcn_chapter', 'fcn_genre', 'post_tag', true );

  // Log
  fictioneer_log( __( 'Chapter tags duplicated as genres.', 'fictioneer' ) );

  // Finish
  fictioneer_finish_tool_action( 'fictioneer-duplicate-tags-to-genres' );
}
add_action( 'admin_post_fictioneer_duplicate_chapter_tags_to_genres', 'fictioneer_tools_duplicate_chapter_tags_to_genres' );

/**
 * Append default genres
 *
 * @since Fictioneer 5.2.5
 */

function fictioneer_tools_append_default_genres() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_append_default_genres' );

  // Setup
  $genres = fictioneer_get_default_genres();

  // Loop over genres
  foreach ( $genres as $genre ) {
    fictioneer_add_or_update_term(
      $genre->name,
      'fcn_genre',
      array(
        'parent' => isset( $genre->parent ) ? $genre->parent : 0,
        'alias_of' => isset( $genre->aliasOf ) ? $genre->aliasOf : '',
        'description' => isset( $genre->description ) ? $genre->description : ''
      )
    );
  }

  // Log
  fictioneer_log( __( 'Added default genres.', 'fictioneer' ) );

  // Finish
  fictioneer_finish_tool_action( 'fictioneer-append-default-genres' );
}
add_action( 'admin_post_fictioneer_append_default_genres', 'fictioneer_tools_append_default_genres' );

// =============================================================================
// TAG TOOLS ACTIONS
// =============================================================================

/**
 * Append default tags
 *
 * @since Fictioneer 5.2.5
 */

function fictioneer_tools_append_default_tags() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_append_default_tags' );

  // Setup
  $tags = fictioneer_get_default_tags();

  // Loop over tags
  foreach ( $tags as $tag ) {
    fictioneer_add_or_update_term(
      $tag->name,
      'post_tag',
      array(
        'description' => isset( $tag->description ) ? $tag->description : ''
      )
    );
  }

  // Log
  fictioneer_log( __( 'Added default tags.', 'fictioneer' ) );

  // Finish
  fictioneer_finish_tool_action( 'fictioneer-append-default-tags' );
}
add_action( 'admin_post_fictioneer_append_default_tags', 'fictioneer_tools_append_default_tags' );

/**
 * Remove unused tags
 *
 * @since Fictioneer 5.2.5
 */

function fictioneer_tools_remove_unused_tags() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_remove_unused_tags' );

  // Setup
  $all_tags = get_tags( array( 'hide_empty' => false ) );

  // Loop over tags
  if ( $all_tags ) {
    foreach ( $all_tags as $tag ) {
      if ( $tag->count < 1 ) {
        wp_delete_term( $tag->term_id, 'post_tag' );
      }
    }
  }

  // Log
  fictioneer_log( __( 'Removed unused tags.', 'fictioneer' ) );

  // Finish
  fictioneer_finish_tool_action( 'fictioneer-remove-unused-tags' );
}
add_action( 'admin_post_fictioneer_remove_unused_tags', 'fictioneer_tools_remove_unused_tags' );

// =============================================================================
// REPAIR TOOLS ACTIONS
// =============================================================================

/**
 * Reset post relationship registry
 *
 * @since Fictioneer 5.2.5
 */

function fictioneer_tools_reset_post_relationship_registry() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_reset_post_relationship_registry' );

  // Relay
  fictioneer_save_relationship_registry( [] );

  // Log
  fictioneer_log( __( 'Post relationship registry reset.', 'fictioneer' ) );

  // Finish
  fictioneer_finish_tool_action( 'fictioneer-reset-post-relationship-registry' );
}
add_action( 'admin_post_fictioneer_reset_post_relationship_registry', 'fictioneer_tools_reset_post_relationship_registry' );

/**
 * Fix users
 *
 * @since Fictioneer 5.2.5
 */

function fictioneer_tools_fix_users() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_fix_users' );

  // Pending implementation
  // $users = get_users( array( 'fields' => 'ID' ) );

  // Finish
  fictioneer_finish_tool_action( 'fictioneer-fix-users' );
}
add_action( 'admin_post_fictioneer_fix_users', 'fictioneer_tools_fix_users' );

/**
 * Fix stories
 *
 * @since Fictioneer 5.2.5
 */

function fictioneer_tools_fix_stories() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_fix_stories' );

  // Pending implementation
  // $stories = get_posts( array( 'post_type' => 'fcn_story', 'numberposts' => -1 ) );

  // Finish
  fictioneer_finish_tool_action( 'fictioneer-fix-stories' );
}
add_action( 'admin_post_fictioneer_fix_stories', 'fictioneer_tools_fix_stories' );

/**
 * Fix chapters
 *
 * @since Fictioneer 5.2.5
 */

function fictioneer_tools_fix_chapters() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_fix_chapters' );

  // Pending implementation
  // $chapters = get_posts( array( 'post_type' => 'fcn_chapter', 'numberposts' => -1 ) );

  // Finish
  fictioneer_finish_tool_action( 'fictioneer-fix-chapters' );
}
add_action( 'admin_post_fictioneer_fix_chapters', 'fictioneer_tools_fix_chapters' );

/**
 * Fix collections
 *
 * @since Fictioneer 5.2.5
 */

function fictioneer_tools_fix_collections() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_fix_collections' );

  // Pending implementation
  // $collections = get_posts( array( 'post_type' => 'fcn_collection', 'numberposts' => -1 ) );

  // Finish
  fictioneer_finish_tool_action( 'fictioneer-fix-collections' );
}
add_action( 'admin_post_fictioneer_fix_collections', 'fictioneer_tools_fix_collections' );

/**
 * Fix pages
 *
 * @since Fictioneer 5.2.5
 */

function fictioneer_tools_fix_pages() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_fix_pages' );

  // Pending implementation
  // $pages = get_posts( array( 'post_type' => 'page', 'numberposts' => -1 ) );

  // Finish
  fictioneer_finish_tool_action( 'fictioneer-fix-pages' );
}
add_action( 'admin_post_fictioneer_fix_pages', 'fictioneer_tools_fix_pages' );

/**
 * Fix posts
 *
 * @since Fictioneer 5.2.5
 */

function fictioneer_tools_fix_posts() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_fix_posts' );

  // Pending implementation
  // $posts = get_posts( array( 'post_type' => 'post', 'numberposts' => -1 ) );

  // Finish
  fictioneer_finish_tool_action( 'fictioneer-fix-posts' );
}
add_action( 'admin_post_fictioneer_fix_posts', 'fictioneer_tools_fix_posts' );

/**
 * Fix recommendations
 *
 * @since Fictioneer 5.2.5
 */

function fictioneer_tools_fix_recommendations() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_fix_recommendations' );

  // Pending implementation
  // $recommendations = get_posts( array( 'post_type' => 'fcn_recommendation', 'numberposts' => -1 ) );

  // Finish
  fictioneer_finish_tool_action( 'fictioneer-fix-recommendations' );
}
add_action( 'admin_post_fictioneer_fix_recommendations', 'fictioneer_tools_fix_recommendations' );

// =============================================================================
// UPDATE ROLE
// =============================================================================

/**
 * Update role
 *
 * @since Fictioneer 5.6.0
 */

function fictioneer_update_role() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_update_role' );

  // Permissions?
  if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( __( 'Insufficient permissions.', 'fictioneer' ) );
  }

  // Setup
  $role_name = $_REQUEST['role'] ?? '';
  $role = get_role( $role_name );
  $notice = 'fictioneer-updated-role-caps';

  // Guard administrators
  if ( $role_name == 'administrator' ) {
    wp_die( __( 'Insufficient permissions.', 'fictioneer' ) );
  }

  // Role not found?
  if ( empty( $role ) ) {
    wp_safe_redirect( add_query_arg( array( 'fictioneer-subnav' => $role_name ), wp_get_referer() ) );
    exit();
  }

  // Forbidden capabilities
  $forbidden = ['create_sites', 'delete_sites', 'manage_network', 'manage_sites', 'manage_network_users', 'manage_network_plugins', 'manage_network_themes', 'manage_network_options', 'upgrade_network', 'setup_network', 'activate_plugins', 'edit_dashboard', 'export', 'import', 'manage_options', 'promote_users', 'customize', 'delete_site', 'update_core', 'update_plugins', 'update_themes', 'install_plugins', 'install_themes', 'delete_themes', 'delete_plugins', 'edit_plugins', 'unfiltered_upload'];

  // Update capabilities
  foreach ( ( $_POST['caps'] ?? [] ) as $cap => $val ) {
    if ( in_array( $cap, $forbidden ) ) {
      continue;
    }

    if ( empty( $val ) ) {
      $role->remove_cap( $cap );
    } else {
      $role->add_cap( $cap );
    }
  }

  if ( in_array( $role_name, ['editor', 'moderator', 'author', 'contributor', 'subscriber'] ) ) {
    $notice = "fictioneer-updated-{$role_name}-caps";
  }

  // Redirect
  wp_safe_redirect(
    add_query_arg( array( 'success' => $notice, 'fictioneer-subnav' => $role_name ), wp_get_referer() )
  );

  exit();
}
add_action( 'admin_post_fictioneer_update_role', 'fictioneer_update_role' );

// =============================================================================
// ROLES
// =============================================================================

/**
 * Add role
 *
 * @since Fictioneer 5.6.0
 */

function fictioneer_add_role() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_add_role' );

  // Permissions?
  if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( __( 'Insufficient permissions.', 'fictioneer' ) );
  }

  // Setup
  $new_role = $_REQUEST['new_role'] ?? '';

  // Name missing
  if ( empty( $new_role ) ) {
    wp_safe_redirect(
      add_query_arg( array( 'failure' => 'fictioneer-not-added-role' ), wp_get_referer() )
    );

    exit();
  }

  // Prepare strings
  $name = sanitize_text_field( $new_role );
  $name = wp_strip_all_tags( $new_role );
  $slug = strtolower( $name );
  $slug = str_replace( ' ', '_', $slug );
  $slug = preg_replace( '/[^a-zA-Z0-9 _]/', '', $slug );

  $existing_role = get_role( $slug );
  $existing_fictioneer_role = get_role( "fcn_{$slug}" );

  // Role already exists
  if ( ! empty( $existing_role ) || ! empty( $existing_fictioneer_role ) ) {
    wp_safe_redirect(
      add_query_arg( array( 'failure' => 'fictioneer-role-already-exists' ), wp_get_referer() )
    );

    exit();
  }

  // Params faulty
  if ( empty( $name ) || empty( $slug ) ) {
    wp_safe_redirect(
      add_query_arg( array( 'failure' => 'fictioneer-not-added-role' ), wp_get_referer() )
    );

    exit();
  }

  // Add role
  $role = add_role( "fcn_{$slug}", $name );

  // Redirect
  if ( empty( $role ) ) {
    wp_safe_redirect(
      add_query_arg( array( 'failure' => 'fictioneer-not-added-role' ), wp_get_referer() )
    );
  } else {
    wp_safe_redirect(
      add_query_arg( array( 'success' => 'fictioneer-added-role', 'fictioneer-subnav' => "fcn_{$slug}" ), wp_get_referer() )
    );
  }

  exit();
}
add_action( 'admin_post_fictioneer_add_role', 'fictioneer_add_role' );

/**
 * Remove role
 *
 * @since Fictioneer 5.6.0
 */

function fictioneer_remove_role() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_remove_role' );

  // Permissions?
  if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( __( 'Insufficient permissions.', 'fictioneer' ) );
  }

  // Setup
  $role = $_REQUEST['role'] ?? '';
  $role = sanitize_text_field( $role );
  $role = wp_strip_all_tags( $role );
  $role = strtolower( $role );
  $role = str_replace( ' ', '_', $role );
  $role = preg_replace( '/[^a-zA-Z0-9 _]/', '', $role );

  // Guard
  if ( in_array( $role, ['administrator', 'editor', 'author', 'contributor', 'subscriber'] ) ) {
    wp_die( __( 'This role cannot be removed.', 'fictioneer' ) );
  }

  // Get users with role
  $role_holders = new WP_User_Query( array( 'role' => $role ) );

  // Change role to subscriber
  if ( ! empty( $role_holders->results )) {
    foreach ( $role_holders->results as $user ) {
      $user->remove_role( $role );
      $user->add_role( 'subscriber' );
    }
  }

  // Remove role
  remove_role( $role );

  // Redirect
  wp_safe_redirect(
    add_query_arg( array( 'success' => 'fictioneer-removed-role', 'fictioneer-subnav' => '' ), wp_get_referer() )
  );

  exit();
}
add_action( 'admin_post_fictioneer_remove_role', 'fictioneer_remove_role' );

/**
 * Rename role
 *
 * @since Fictioneer 5.6.0
 */

function fictioneer_rename_role() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_rename_role' );

  // Permissions?
  if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( __( 'Insufficient permissions.', 'fictioneer' ) );
  }

  // Setup
  $role_slug = $_REQUEST['role'] ?? '';
  $new_name = $_REQUEST['new_name'] ?? '';

  // Params missing
  if ( empty( $role_slug ) || empty( $new_name ) ) {
    wp_safe_redirect(
      add_query_arg( array( 'failure' => 'fictioneer-not-renamed-role' ), wp_get_referer() )
    );

    exit();
  }

  // Prepare strings
  $new_name = sanitize_text_field( $new_name );
  $new_name = wp_strip_all_tags( $new_name );
  $role_slug = strtolower( $role_slug );
  $role_slug = str_replace( ' ', '_', $role_slug );
  $role_slug = preg_replace( '/[^a-zA-Z0-9 _]/', '', $role_slug );

  // Guard
  if ( in_array( $role_slug, ['administrator', 'editor', 'author', 'contributor', 'subscriber'] ) ) {
    wp_die( __( 'This role cannot be renamed.', 'fictioneer' ) );
  }

  // Name or slug faulty
  if ( empty( $new_name ) || empty( $role_slug ) ) {
    wp_safe_redirect(
      add_query_arg( array( 'failure' => 'fictioneer-not-renamed-role' ), wp_get_referer() )
    );

    exit();
  }

  // Get role to be renamed
  $target_role = get_role( $role_slug );

  // Role does not exist
  if ( empty( $target_role ) ) {
    wp_safe_redirect(
      add_query_arg( array( 'failure' => 'fictioneer-not-renamed-role' ), wp_get_referer() )
    );

    exit();
  }

  // Remove old role with old name
  remove_role( $role_slug );

  // Add new role with new name but old slug
  $role = add_role( $role_slug, $new_name, $target_role->capabilities );

  // Redirect
  if ( empty( $role ) ) {
    // Try to restore old role
    add_role( $role_slug, $target_role->name, $target_role->capabilities );

    wp_safe_redirect(
      add_query_arg( array( 'failure' => 'fictioneer-not-renamed-role' ), wp_get_referer() )
    );
  } else {
    wp_safe_redirect(
      add_query_arg( array( 'success' => 'fictioneer-renamed-role', 'fictioneer-subnav' => $role_slug ), wp_get_referer() )
    );
  }

  exit();
}
add_action( 'admin_post_fictioneer_rename_role', 'fictioneer_rename_role' );

// =============================================================================
// DATABASE TOOLS ACTIONS
// =============================================================================

/**
 * Optimize database
 *
 * @since Fictioneer 5.7.4
 * @global wpdb $wpdb  WordPress database object.
 */

function fictioneer_tools_optimize_database() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_tools_optimize_database' );

  global $wpdb;

  // Allowed
  $allowed_meta_keys = fictioneer_get_falsy_meta_allow_list();
  $not_like_sql = '';

  if ( ! empty( $allowed_meta_keys ) ) {
    $not_like_statements = [];

    foreach ( $allowed_meta_keys as $key ) {
      $not_like_statements[] = "meta_key NOT LIKE '{$key}'";
    }

    $not_like_sql = " AND " . implode( ' AND ', $not_like_statements );
  }

  // Delete and return number of rows
  $count = $wpdb->query("
    DELETE FROM $wpdb->postmeta
    WHERE meta_key LIKE '_fictioneer_%'
    OR (
      meta_key LIKE 'fictioneer%'
      AND meta_key NOT LIKE '%_cache'
      AND (meta_value = '' OR meta_value IS NULL OR meta_value = '0')
      $not_like_sql
    )
  ");

  // Redirect
  wp_safe_redirect(
    add_query_arg(
      array(
        'success' => 'fictioneer-db-optimization',
        'data' => $count
      ),
      wp_get_referer()
    )
  );

  exit();

}
add_action( 'admin_post_fictioneer_tools_optimize_database', 'fictioneer_tools_optimize_database' );

/**
 * Optimize database preview
 *
 * @since Fictioneer 5.7.4
 * @global wpdb $wpdb  WordPress database object.
 */

function fictioneer_tools_optimize_database_preview() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_tools_optimize_database_preview' );

  global $wpdb;

  // Allowed
  $allowed_meta_keys = fictioneer_get_falsy_meta_allow_list();
  $not_like_sql = '';

  if ( ! empty( $allowed_meta_keys ) ) {
    $not_like_statements = [];

    foreach ( $allowed_meta_keys as $key ) {
      $not_like_statements[] = "meta_key NOT LIKE '{$key}'";
    }

    $not_like_sql = " AND " . implode( ' AND ', $not_like_statements );
  }

  // Return number of rows affected
  $count = $wpdb->get_var("
    SELECT COUNT(*) FROM $wpdb->postmeta
    WHERE meta_key LIKE '_fictioneer_%'
    OR (
      meta_key LIKE 'fictioneer%'
      AND meta_key NOT LIKE '%_cache'
      AND (meta_value = '' OR meta_value IS NULL OR meta_value = '0')
      $not_like_sql
    )
  ");

  // Redirect
  wp_safe_redirect(
    add_query_arg(
      array(
        'info' => 'fictioneer-db-optimization-preview',
        'data' => $count
      ),
      wp_get_referer()
    )
  );

  exit();
}
add_action( 'admin_post_fictioneer_tools_optimize_database_preview', 'fictioneer_tools_optimize_database_preview' );

/**
 * Append missing 'fictioneer_story_hidden' post meta
 *
 * @since Fictioneer 5.7.4
 */

function fictioneer_tools_add_story_hidden_fields() {
  // Append 'fictioneer_story_hidden'
  fictioneer_append_meta_fields( 'fcn_story', 'fictioneer_story_hidden', 0 );

  // Redirect
  wp_safe_redirect(
    add_query_arg(
      array(
        'success' => 'fictioneer-add-story-hidden'
      ),
      wp_get_referer()
    )
  );

  exit();
}
add_action( 'admin_post_fictioneer_tools_add_story_hidden_fields', 'fictioneer_tools_add_story_hidden_fields' );

/**
 * Append missing 'fictioneer_story_sticky' post meta
 *
 * @since Fictioneer 5.7.4
 */

function fictioneer_tools_add_story_sticky_fields() {
  // Append 'fictioneer_story_sticky'
  fictioneer_append_meta_fields( 'fcn_story', 'fictioneer_story_sticky', 0 );

  // Redirect
  wp_safe_redirect(
    add_query_arg(
      array(
        'success' => 'fictioneer-add-story-sticky'
      ),
      wp_get_referer()
    )
  );

  exit();
}
add_action( 'admin_post_fictioneer_tools_add_story_sticky_fields', 'fictioneer_tools_add_story_sticky_fields' );

/**
 * Append missing 'fictioneer_chapter_hidden' post meta
 *
 * @since Fictioneer 5.7.4
 */

function fictioneer_tools_add_chapter_hidden_fields() {
  // Append 'fictioneer_chapter_hidden'
  fictioneer_append_meta_fields( 'fcn_chapter', 'fictioneer_chapter_hidden', 0 );

  // Redirect
  wp_safe_redirect(
    add_query_arg(
      array(
        'success' => 'fictioneer-add-chapter-hidden'
      ),
      wp_get_referer()
    )
  );

  exit();
}
add_action( 'admin_post_fictioneer_tools_add_chapter_hidden_fields', 'fictioneer_tools_add_chapter_hidden_fields' );

/**
 * Append a missing meta field to selected posts
 *
 * @since Fictioneer 5.7.4
 * @global wpdb $wpdb  WordPress database object.
 *
 * @param string $post_type   The post type to append to.
 * @param string $meta_key    The meta key to append.
 * @param mixed  $meta_value  The value to assign.
 */

function fictioneer_append_meta_fields( $post_type, $meta_key, $meta_value ) {
  global $wpdb;

  // Setup
  $values = [];

  // Get posts with missing meta field
  $posts = $wpdb->get_col("
    SELECT p.ID
    FROM {$wpdb->posts} p
    LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '{$meta_key}'
    WHERE p.post_type = '{$post_type}' AND pm.meta_id IS NULL
  ");

  // Prepare values
  foreach ( $posts as $post_id ) {
    $values[] = $wpdb->prepare( "(%d, %s, %d)", $post_id, $meta_key, $meta_value );
  }

  $chunks = array_chunk( $values, 1000 );

  // Query
  foreach ( $chunks as $chunk ) {
    $values_sql = implode( ', ', $chunk );

    if( ! empty( $values_sql ) ) {
      $wpdb->query("
        INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value)
        VALUES {$values_sql};
      ");
    }
  }
}

?>
