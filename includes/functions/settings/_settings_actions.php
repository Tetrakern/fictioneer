<?php

// =============================================================================
// UTILITY
// =============================================================================

/**
 * Generate a nonce URL with for an admin action
 *
 * @since 5.2.5
 *
 * @param string $action  Name of the action.
 *
 * @return string Generated URL with the nonce.
 */

function fictioneer_admin_action( $action ) {
  return wp_nonce_url( admin_url( "admin-post.php?action={$action}" ), $action, 'fictioneer_nonce' );
}

/**
 * Verify an admin action request
 *
 * @since 5.2.5
 *
 * @param string $action  Name of the action.
 */

function fictioneer_verify_admin_action( $action ) {
  // Verify request
  if ( ! check_admin_referer( $action, 'fictioneer_nonce' ) ) {
    wp_die( 'Nonce verification failed. Please try again.' );
  }

  // Guard
  if ( ! current_user_can( 'manage_options' ) || ! is_admin() ) {
    wp_die( 'Insufficient permissions.' );
  }
}

/**
 * Finish an admin action and perform a redirect
 *
 * @since 5.2.5
 *
 * @param string $notice  Optional. The notice message to include in the redirect URL.
 * @param string $type    Optional. The type of notice. Default 'success'.
 */

function fictioneer_finish_admin_action( $notice = '', $type = 'success' ) {
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
   * @since 4.7.0
   *
   * @return array Decoded JSON with default genres and meta data.
   */

  function fictioneer_get_default_genres() {
    return json_decode( file_get_contents( get_template_directory_uri() . '/json/genres.json' ) );
  }
}

if ( ! function_exists( 'fictioneer_get_default_tags' ) ) {
  /**
   * Return array of default tags from decoded JSON
   *
   * @since 4.7.0
   *
   * @return array Decoded JSON with default tags and meta data.
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
      'fictioneer-db-optimization-preview' => __( '%s superfluous and %s orphaned post meta rows found. %s superfluous comment meta rows found. %s superfluous option rows found. Please backup your database before performing any optimization.', 'fictioneer' ),
      'fictioneer-db-optimization' => __( '%s superfluous or orphaned rows have been deleted.', 'fictioneer' ),
      'fictioneer-add-story-hidden' => __( 'The "fictioneer_story_hidden" meta field has been appended with value 0.', 'fictioneer' ),
      'fictioneer-add-story-sticky' => __( 'The "fictioneer_story_sticky" meta field has been appended with value 0.', 'fictioneer' ),
      'fictioneer-add-chapter-hidden' => __( 'The "fictioneer_chapter_hidden" meta field has been appended with value 0.', 'fictioneer' ),
      'fictioneer-chapters-appended' => __( '%s chapters have been appended.', 'fictioneer' ),
      'fictioneer-legacy-cleanup' => __( 'Performed legacy cleanups: %s.', 'fictioneer' ),
      'fictioneer-disabled-font' => __( 'Disabled font key "%s".', 'fictioneer' ),
      'fictioneer-enabled-font' => __( 'Enabled font key "%s".', 'fictioneer' ),
      'fictioneer-patreon-not-connected' => __( 'OAuth 2.0 authentication not enabled or Patreon client not set up.', 'fictioneer' ),
      'fictioneer-patreon-token-missing' => __( 'The Patreon access token could not be retrieved.', 'fictioneer' ),
      'fictioneer-patreon-tiers-pulled' => __( 'Patreon tiers have been pulled successfully.', 'fictioneer' ),
      'fictioneer-patreon-tiers-deleted' => __( 'Patreon tiers have been deleted.', 'fictioneer' ),
      'fictioneer-mu-plugin-copy-success' => __( 'MU-Plugin enabled successfully.', 'fictioneer' ),
      'fictioneer-mu-plugin-copy-failure' => __( 'Error. MU-Plugin could not be enabled.', 'fictioneer' ),
      'fictioneer-mu-plugin-delete-success' => __( 'MU-Plugin disabled successfully.', 'fictioneer' ),
      'fictioneer-mu-plugin-delete-failure' => __( 'Error. MU-Plugin could not be disabled..', 'fictioneer' ),
      'fictioneer-fix-line-breaks-success' => __( '%s successful post update(s). %s failure(s).', 'fictioneer' )
    )
  );
}

/**
 * Output admin settings notices
 *
 * @since 5.2.5
 */

function fictioneer_admin_settings_notices() {
  // Abort if normal form submit
  if ( $_GET['settings-updated'] ?? 0 ) {
    return;
  }

  // Get query vars
  $success = sanitize_text_field( $_GET['success'] ?? '' );
  $failure = sanitize_text_field( $_GET['failure'] ?? '' );
  $info = sanitize_text_field( $_GET['info'] ?? '' );
  $data = explode( ',', sanitize_text_field( $_GET['data'] ?? '' ) );
  $data = is_array( $data ) ? $data : [];
  $data = array_map( 'esc_html', $data );

  // Has success notice?
  if ( ! empty( $success ) && isset( FICTIONEER_ADMIN_SETTINGS_NOTICES[ $success ] ) ) {
    wp_admin_notice(
      vsprintf( FICTIONEER_ADMIN_SETTINGS_NOTICES[ $success ], $data ),
      array(
        'type' => 'success',
        'dismissible' => true
      )
    );
  }

  // Has failure notice?
  if ( ! empty( $failure ) && isset( FICTIONEER_ADMIN_SETTINGS_NOTICES[ $failure ] ) ) {
    wp_admin_notice(
      vsprintf( FICTIONEER_ADMIN_SETTINGS_NOTICES[ $failure ], $data ),
      array(
        'type' => 'error',
        'dismissible' => true
      )
    );
  }

  // Has info notice?
  if ( ! empty( $info ) && isset( FICTIONEER_ADMIN_SETTINGS_NOTICES[ $info ] ) ) {
    wp_admin_notice(
      vsprintf( FICTIONEER_ADMIN_SETTINGS_NOTICES[ $info ], $data ),
      array(
        'type' => 'info',
        'dismissible' => true
      )
    );
  }
}
add_action( 'admin_notices', 'fictioneer_admin_settings_notices' );

// =============================================================================
// EPUB ACTIONS
// =============================================================================

/**
 * Delete all generated ePUBs in the ./uploads/epubs/ directory
 *
 * @since 5.2.5
 */

function fictioneer_delete_all_epubs() {
  // Verify request
  fictioneer_verify_admin_action( 'fictioneer_delete_all_epubs' );

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
  fictioneer_finish_admin_action( 'fictioneer-deleted-epubs' );
}
add_action( 'admin_post_fictioneer_delete_all_epubs', 'fictioneer_delete_all_epubs' );

// =============================================================================
// ROLE TOOLS ACTIONS
// =============================================================================

/**
 * Add a moderator role
 *
 * @since 5.2.5
 */

function fictioneer_tools_add_moderator_role() {
  // Verify request
  fictioneer_verify_admin_action( 'fictioneer_add_moderator_role' );

  // Relay
  if ( fictioneer_add_moderator_role() ) {
    $notice = ['fictioneer-added-moderator-role', 'success'];

    // Log
    fictioneer_log( __( 'Moderator role added.', 'fictioneer' ) );
  } else {
    $notice = ['fictioneer-not-added-moderator-role', 'failure'];
  }

  // Finish
  fictioneer_finish_admin_action( $notice[0], $notice[1] );
}
add_action( 'admin_post_fictioneer_add_moderator_role', 'fictioneer_tools_add_moderator_role' );

/**
 * Initialize roles
 *
 * @since 5.6.0
 */

function fictioneer_tools_initialize_roles() {
  // Verify request
  fictioneer_verify_admin_action( 'fictioneer_initialize_roles' );

  // Force role initialization
  fictioneer_initialize_roles( true );

  // Log
  fictioneer_log(
    __( 'Initialized roles.', 'fictioneer' )
  );

  // Finish
  fictioneer_finish_admin_action( 'fictioneer-roles-initialized' );
}
add_action( 'admin_post_fictioneer_initialize_roles', 'fictioneer_tools_initialize_roles' );

/**
 * Convert story tags to genres
 *
 * @since 5.2.5
 */

function fictioneer_tools_move_story_tags_to_genres() {
  // Verify request
  fictioneer_verify_admin_action( 'fictioneer_move_story_tags_to_genres' );

  // Relay
  fictioneer_convert_taxonomies( 'fcn_story', 'fcn_genre', 'post_tag', true, true );

  // Log
  fictioneer_log( __( 'Story tags converted to genres.', 'fictioneer' ) );

  // Finish
  fictioneer_finish_admin_action( 'fictioneer-story-tags-to-genres' );
}
add_action( 'admin_post_fictioneer_move_story_tags_to_genres', 'fictioneer_tools_move_story_tags_to_genres' );

// =============================================================================
// FONT ACTIONS
// =============================================================================

/**
 * Disables a font by key
 *
 * @since 5.10.0
 */

function fictioneer_tools_disable_font() {
  // Verify request
  fictioneer_verify_admin_action( 'fictioneer_disable_font' );

  // Setup
  $font_key = sanitize_key( $_GET['font'] );
  $disabled_fonts = get_option( 'fictioneer_disabled_fonts', [] );
  $disabled_fonts = is_array( $disabled_fonts ) ? $disabled_fonts : [];

  // Abort if...
  if ( empty( $font_key ) ) {
    fictioneer_finish_admin_action( 'fictioneer-font-not-found' );
  }

  // Disable
  $disabled_fonts[ $font_key ] = $font_key;
  $disabled_fonts = array_unique( $disabled_fonts );
  update_option( 'fictioneer_disabled_fonts', $disabled_fonts );

  // Rebuild stylesheet
  fictioneer_build_bundled_fonts();

  // Clear cached HTML for good measure
  fictioneer_clear_all_cached_partials();

  // Log
  fictioneer_log(
    sprintf(
      __( 'Disabled "%s" font key. Currently disabled keys: %s.', 'fictioneer' ),
      $font_key,
      implode( ', ', $disabled_fonts ) ?: __( 'no keys disabled', 'fictioneer' )
    )
  );

  // Finish
  wp_safe_redirect(
    add_query_arg(
      array(
        'success' => 'fictioneer-disabled-font',
        'data' => $font_key
      ),
      wp_get_referer()
    )
  );
}
add_action( 'admin_post_fictioneer_disable_font', 'fictioneer_tools_disable_font' );

/**
 * Re-enables a font by key
 *
 * @since 5.10.0
 */

function fictioneer_tools_enable_font() {
  // Verify request
  fictioneer_verify_admin_action( 'fictioneer_enable_font' );

  // Setup
  $font_key = sanitize_key( $_GET['font'] );
  $disabled_fonts = get_option( 'fictioneer_disabled_fonts', [] );
  $disabled_fonts = is_array( $disabled_fonts ) ? $disabled_fonts : [];

  // Abort if...
  if ( empty( $font_key ) ) {
    fictioneer_finish_admin_action( 'fictioneer-font-not-found' );
  }

  // Disable
  unset( $disabled_fonts[ $font_key ] );
  update_option( 'fictioneer_disabled_fonts', $disabled_fonts );

  // Rebuild stylesheet
  fictioneer_build_bundled_fonts();

  // Clear cached HTML for good measure
  fictioneer_clear_all_cached_partials();

  // Log
  fictioneer_log(
    sprintf(
      __( 'Enabled "%s" font key. Currently disabled keys: %s.', 'fictioneer' ),
      $font_key,
      implode( ', ', $disabled_fonts ) ?: __( 'no keys disabled', 'fictioneer' )
    )
  );

  // Finish
  wp_safe_redirect(
    add_query_arg(
      array(
        'success' => 'fictioneer-enabled-font',
        'data' => $font_key
      ),
      wp_get_referer()
    )
  );
}
add_action( 'admin_post_fictioneer_enable_font', 'fictioneer_tools_enable_font' );

// =============================================================================
// STORY TOOLS ACTIONS
// =============================================================================

/**
 * Duplicate story tags to genres
 *
 * @since 5.2.5
 */

function fictioneer_tools_duplicate_story_tags_to_genres() {
  // Verify request
  fictioneer_verify_admin_action( 'fictioneer_duplicate_story_tags_to_genres' );

  // Relay
  fictioneer_convert_taxonomies( 'fcn_story', 'fcn_genre', 'post_tag', true );

  // Log
  fictioneer_log( __( 'Story tags duplicated as genres.', 'fictioneer' ) );

  // Finish
  fictioneer_finish_admin_action( 'fictioneer-duplicate-tags-to-genres' );
}
add_action( 'admin_post_fictioneer_duplicate_story_tags_to_genres', 'fictioneer_tools_duplicate_story_tags_to_genres' );

// =============================================================================
// CHAPTER TOOLS ACTIONS
// =============================================================================

/**
 * Convert chapter tags to genres
 *
 * @since 5.2.5
 */

function fictioneer_tools_move_chapter_tags_to_genres() {
  // Verify request
  fictioneer_verify_admin_action( 'fictioneer_move_chapter_tags_to_genres' );

  // Relay
  fictioneer_convert_taxonomies( 'fcn_chapter', 'fcn_genre', 'post_tag', true, true );

  // Log
  fictioneer_log( __( 'Chapter tags converted to genres.', 'fictioneer' ) );

  // Finish
  fictioneer_finish_admin_action( 'fictioneer-chapter-tags-to-genres' );
}
add_action( 'admin_post_fictioneer_move_chapter_tags_to_genres', 'fictioneer_tools_move_chapter_tags_to_genres' );

/**
 * Duplicate chapter tags to genres
 *
 * @since 5.2.5
 */

function fictioneer_tools_duplicate_chapter_tags_to_genres() {
  // Verify request
  fictioneer_verify_admin_action( 'fictioneer_duplicate_chapter_tags_to_genres' );

  // Relay
  fictioneer_convert_taxonomies( 'fcn_chapter', 'fcn_genre', 'post_tag', true );

  // Log
  fictioneer_log( __( 'Chapter tags duplicated as genres.', 'fictioneer' ) );

  // Finish
  fictioneer_finish_admin_action( 'fictioneer-duplicate-tags-to-genres' );
}
add_action( 'admin_post_fictioneer_duplicate_chapter_tags_to_genres', 'fictioneer_tools_duplicate_chapter_tags_to_genres' );

/**
 * Append default genres
 *
 * @since 5.2.5
 */

function fictioneer_tools_append_default_genres() {
  // Verify request
  fictioneer_verify_admin_action( 'fictioneer_append_default_genres' );

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
  fictioneer_finish_admin_action( 'fictioneer-append-default-genres' );
}
add_action( 'admin_post_fictioneer_append_default_genres', 'fictioneer_tools_append_default_genres' );

// =============================================================================
// TAG TOOLS ACTIONS
// =============================================================================

/**
 * Append default tags
 *
 * @since 5.2.5
 */

function fictioneer_tools_append_default_tags() {
  // Verify request
  fictioneer_verify_admin_action( 'fictioneer_append_default_tags' );

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
  fictioneer_finish_admin_action( 'fictioneer-append-default-tags' );
}
add_action( 'admin_post_fictioneer_append_default_tags', 'fictioneer_tools_append_default_tags' );

/**
 * Remove unused tags
 *
 * @since 5.2.5
 */

function fictioneer_tools_remove_unused_tags() {
  // Verify request
  fictioneer_verify_admin_action( 'fictioneer_remove_unused_tags' );

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
  fictioneer_finish_admin_action( 'fictioneer-remove-unused-tags' );
}
add_action( 'admin_post_fictioneer_remove_unused_tags', 'fictioneer_tools_remove_unused_tags' );

// =============================================================================
// REPAIR TOOLS ACTIONS
// =============================================================================

/**
 * Reset post relationship registry
 *
 * @since 5.2.5
 */

function fictioneer_tools_reset_post_relationship_registry() {
  // Verify request
  fictioneer_verify_admin_action( 'fictioneer_reset_post_relationship_registry' );

  // Relay
  fictioneer_save_relationship_registry( [] );

  // Log
  fictioneer_log( __( 'Post relationship registry reset.', 'fictioneer' ) );

  // Finish
  fictioneer_finish_admin_action( 'fictioneer-reset-post-relationship-registry' );
}
add_action( 'admin_post_fictioneer_reset_post_relationship_registry', 'fictioneer_tools_reset_post_relationship_registry' );

// =============================================================================
// UPDATE ROLE
// =============================================================================

/**
 * Update role
 *
 * @since 5.6.0
 */

function fictioneer_update_role() {
  // Verify request
  fictioneer_verify_admin_action( 'fictioneer_update_role' );

  // Permissions?
  if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( 'Insufficient permissions.' );
  }

  // Setup
  $role_name = $_REQUEST['role'] ?? '';
  $role = get_role( $role_name );
  $notice = 'fictioneer-updated-role-caps';

  // Guard administrators
  if ( $role_name == 'administrator' ) {
    wp_die( 'Insufficient permissions.' );
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

  // Log
  fictioneer_log(
    sprintf(
      __( 'Updated "%s" role.', 'fictioneer' ),
      $role_name
    )
  );

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
 * @since 5.6.0
 */

function fictioneer_add_role() {
  // Verify request
  fictioneer_verify_admin_action( 'fictioneer_add_role' );

  // Permissions?
  if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( 'Insufficient permissions.' );
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
    // Log
    fictioneer_log(
      sprintf(
        __( 'Added "%s" role.', 'fictioneer' ),
        $name
      )
    );

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
 * @since 5.6.0
 */

function fictioneer_remove_role() {
  // Verify request
  fictioneer_verify_admin_action( 'fictioneer_remove_role' );

  // Permissions?
  if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( 'Insufficient permissions.' );
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

  // Log
  fictioneer_log(
    sprintf(
      __( 'Removed "%s" role.', 'fictioneer' ),
      $role
    )
  );

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
 * @since 5.6.0
 */

function fictioneer_rename_role() {
  // Verify request
  fictioneer_verify_admin_action( 'fictioneer_rename_role' );

  // Permissions?
  if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( 'Insufficient permissions.' );
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
 * Legacy cleanup
 *
 * @since 5.9.4
 * @global wpdb $wpdb  WordPress database object.
 */

function fictioneer_tools_legacy_cleanup() {
  // Verify request
  fictioneer_verify_admin_action( 'fictioneer_tools_legacy_cleanup' );

  global $wpdb;

  // Get install date from first (existing) user registration date
  $users = get_users( array( 'orderby' => 'registered', 'order' => 'ASC', 'number' => 1 ) );
  $install_date = date( 'YmdHi', strtotime( $users[0]->user_registered ) ); // YYYYMMDDHHMM

  // Setup
  $cutoff = 202402060000; // YYYYMMDDHHMM
  $last_cleanup = absint( get_option( 'fictioneer_last_database_cleanup' ) );
  $last_cleanup = $last_cleanup < $install_date ? $install_date : $last_cleanup;
  $count = 0;

  // Sitemap cleanup
  if ( $last_cleanup < 202402060000 ) {
    $old_sitemap = ABSPATH . '/sitemap.xml';

    if ( file_exists( $old_sitemap ) ) {
      unlink( $old_sitemap );
    }

    flush_rewrite_rules();

    delete_option( 'fictioneer_database_cleanup' ); // Was previously saved wrongly far into the future

    fictioneer_log( __( 'Legacy cleanup removed old sitemap and flushed the permalinks.', 'fictioneer' ) );

    $count++;
  }

  // Migrate SEO fields
  if ( $last_cleanup < 202402060000 ) {
    // Relevant meta keys
    $meta_keys = [
      'fictioneer_seo_title',
      'fictioneer_seo_description',
      'fictioneer_seo_og_image',
      'fictioneer_seo_title_cache',
      'fictioneer_seo_description_cache',
      'fictioneer_seo_og_image_cache'
    ];

    // Look for old SEO data
    $placeholders = implode( ', ', array_fill( 0, count( $meta_keys ), '%s' ) );

    $sql = "
      SELECT COUNT(*) FROM {$wpdb->postmeta}
      WHERE meta_key IN ($placeholders)
    ";

    $meta_rows = $wpdb->get_var( $wpdb->prepare( $sql, $meta_keys ) );

    // If old SEO data exists...
    if ( $meta_rows > 0 ) {
      fictioneer_log( sprintf( __( 'Legacy cleanup removed %s SEO rows.', 'fictioneer' ), $meta_rows ) );

      // Query all posts
      $args = array(
        'post_type' => ['post', 'page', 'fcn_story', 'fcn_chapter', 'fcn_collection', 'fcn_recommendation'],
        'post_status' => ['publish', 'private', 'future'],
        'posts_per_page' => -1,
        'update_post_meta_cache' => true,
        'update_post_term_cache' => false,
        'no_found_rows' => true
      );

      $posts = get_posts( $args );

      // Process posts...
      foreach ( $posts as $post ) {
        // Get old SEO data
        $title = get_post_meta( $post->ID, 'fictioneer_seo_title', true );
        $description = get_post_meta( $post->ID, 'fictioneer_seo_description', true );
        $og_image = get_post_meta( $post->ID, 'fictioneer_seo_og_image', true );

        // Continue if nothing found
        if ( ! $title && ! $description && ! $og_image ) {
          continue;
        }

        // Build new field array
        $seo_fields = array(
          'title' => $title,
          'description' => $description,
          'og_image_id' => $og_image
        );

        // Update new meta fields
        update_post_meta( $post->ID, 'fictioneer_seo_fields', $seo_fields );
      }

      // Delete old meta fields
      foreach ( $meta_keys as $key ) {
        $wpdb->delete( $wpdb->postmeta, array( 'meta_key' => $key ) );
      }
    }

    $count++;
  }

  // Purge theme caches after update
  fictioneer_delete_transients_like( 'fictioneer_' );

  $sql = "
    DELETE FROM {$wpdb->postmeta}
    WHERE meta_key IN (%s, %s)
  ";

  $wpdb->query( $wpdb->prepare( $sql, 'fictioneer_story_data_collection', 'fictioneer_story_chapter_index_html' ) );

  // Remember cleanup
  update_option( 'fictioneer_last_database_cleanup', $cutoff );

  // Redirect
  wp_safe_redirect(
    add_query_arg(
      array(
        'success' => 'fictioneer-legacy-cleanup',
        'data' => $count
      ),
      wp_get_referer()
    )
  );

  exit();
}
add_action( 'admin_post_fictioneer_tools_legacy_cleanup', 'fictioneer_tools_legacy_cleanup' );

/**
 * Optimize database
 *
 * @since 5.7.4
 * @global wpdb $wpdb  WordPress database object.
 */

function fictioneer_tools_optimize_database() {
  // Verify request
  fictioneer_verify_admin_action( 'fictioneer_tools_optimize_database' );

  global $wpdb;

  // Clean up deleted comments
  fictioneer_migrate_deleted_comments();

  // Delete post meta
  $allowed_meta_keys = fictioneer_get_falsy_meta_allow_list();
  $not_like_sql = '';

  if ( ! empty( $allowed_meta_keys ) ) {
    $not_like_statements = [];

    foreach ( $allowed_meta_keys as $key ) {
      $not_like_statements[] = "meta_key NOT LIKE '{$key}'";
    }

    $not_like_sql = " AND " . implode( ' AND ', $not_like_statements );
  }

  $post_meta_count = $wpdb->query("
    DELETE FROM $wpdb->postmeta
    WHERE meta_key LIKE '_fictioneer_%'
    OR (
      meta_key LIKE 'fictioneer%'
      AND meta_key NOT LIKE '%_cache'
      AND (meta_value = '' OR meta_value IS NULL OR meta_value = '0')
      $not_like_sql
    )
    OR meta_key IN ('_edit_last', '_edit_lock')
  ");

  $orphaned_post_meta_count = $wpdb->query("
    DELETE pm
    FROM $wpdb->postmeta pm
    LEFT JOIN $wpdb->posts p ON pm.post_id = p.ID
    WHERE p.ID IS NULL
  ");

  // Delete comment meta (legacy)
  $comment_meta_count = $wpdb->query("
    DELETE FROM $wpdb->commentmeta
    WHERE
    meta_key = 'fictioneer_visibility_code'
    OR (
      meta_key LIKE 'fictioneer%'
      AND (meta_value = '' OR meta_value IS NULL OR meta_value = '0')
    )
  ");

  // Delete options
  $options_meta_count = $wpdb->query("
    DELETE FROM $wpdb->options
    WHERE option_name LIKE 'fictioneer_%'
    AND (option_value IS NULL OR option_value = '')
    AND autoload = 'no'
  ");

  // Total rows
  $total = $post_meta_count + $orphaned_post_meta_count + $comment_meta_count + $options_meta_count;

  // Log
  fictioneer_log(
    sprintf(
      __( 'Optimized database and removed %s superfluous or orphaned rows.', 'fictioneer' ),
      $total
    )
  );

  // Redirect
  wp_safe_redirect(
    add_query_arg(
      array(
        'success' => 'fictioneer-db-optimization',
        'data' => $total
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
 * @since 5.7.4
 * @global wpdb $wpdb  WordPress database object.
 */

function fictioneer_tools_optimize_database_preview() {
  // Verify request
  fictioneer_verify_admin_action( 'fictioneer_tools_optimize_database_preview' );

  global $wpdb;

  // Post meta
  $allowed_meta_keys = fictioneer_get_falsy_meta_allow_list();
  $not_like_sql = '';

  if ( ! empty( $allowed_meta_keys ) ) {
    $not_like_statements = [];

    foreach ( $allowed_meta_keys as $key ) {
      $not_like_statements[] = "meta_key NOT LIKE '{$key}'";
    }

    $not_like_sql = " AND " . implode( ' AND ', $not_like_statements );
  }

  $post_meta_count = $wpdb->get_var("
    SELECT COUNT(*) FROM $wpdb->postmeta
    WHERE meta_key LIKE '_fictioneer_%'
    OR (
      meta_key LIKE 'fictioneer%'
      AND meta_key NOT LIKE '%_cache'
      AND (meta_value = '' OR meta_value IS NULL OR meta_value = '0')
      $not_like_sql
    )
    OR meta_key IN ('_edit_last', '_edit_lock')
  ");

  $orphaned_post_meta_count = $wpdb->get_var("
    SELECT COUNT(*)
    FROM $wpdb->postmeta pm
    LEFT JOIN $wpdb->posts p ON pm.post_id = p.ID
    WHERE p.ID IS NULL
  ");

  // Comment meta (legacy)
  $comment_meta_count = $wpdb->get_var("
    SELECT COUNT(*) FROM $wpdb->commentmeta
    WHERE
    meta_key = 'fictioneer_visibility_code'
    OR (
      meta_key LIKE 'fictioneer%'
      AND (meta_value = '' OR meta_value IS NULL OR meta_value = '0')
    )
  ");

  // Options
  $options_meta_count = $wpdb->get_var("
    SELECT COUNT(*) FROM $wpdb->options
    WHERE option_name LIKE 'fictioneer_%'
    AND (option_value IS NULL OR option_value = '')
    AND autoload = 'no'
  ");

  // Redirect
  wp_safe_redirect(
    add_query_arg(
      array(
        'info' => 'fictioneer-db-optimization-preview',
        'data' => "{$post_meta_count},{$orphaned_post_meta_count},{$comment_meta_count},{$options_meta_count}"
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
 * @since 5.7.4
 */

function fictioneer_tools_add_story_hidden_fields() {
  // Verify request
  fictioneer_verify_admin_action( 'fictioneer_tools_add_story_hidden_fields' );

  // Append 'fictioneer_story_hidden'
  fictioneer_append_meta_fields( 'fcn_story', 'fictioneer_story_hidden', 0 );

  // Log
  fictioneer_log( __( 'Appended missing "fictioneer_story_hidden" meta fields with value 0.', 'fictioneer' ) );

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
 * @since 5.7.4
 */

function fictioneer_tools_add_story_sticky_fields() {
  // Verify request
  fictioneer_verify_admin_action( 'fictioneer_tools_add_story_sticky_fields' );

  // Append 'fictioneer_story_sticky'
  fictioneer_append_meta_fields( 'fcn_story', 'fictioneer_story_sticky', 0 );

  // Log
  fictioneer_log( __( 'Appended missing "fictioneer_story_sticky" meta fields with value 0.', 'fictioneer' ) );

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
 * @since 5.7.4
 */

function fictioneer_tools_add_chapter_hidden_fields() {
  // Verify request
  fictioneer_verify_admin_action( 'fictioneer_tools_add_chapter_hidden_fields' );

  // Append 'fictioneer_chapter_hidden'
  fictioneer_append_meta_fields( 'fcn_chapter', 'fictioneer_chapter_hidden', 0 );

  // Log
  fictioneer_log( __( 'Appended missing "fictioneer_chapter_hidden" meta fields with value 0.', 'fictioneer' ) );

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
 * Purge theme caches
 *
 * @since 5.19.0
 */

function fictioneer_purge_theme_caches() {
  global $wpdb;

  // SQL to delete story meta caches
  $sql = "
    DELETE FROM {$wpdb->postmeta}
    WHERE meta_key IN (%s, %s)
  ";

  $wpdb->query( $wpdb->prepare( $sql, 'fictioneer_story_data_collection', 'fictioneer_story_chapter_index_html' ) );

  // Delete cached files
  $files = glob( trailingslashit( fictioneer_get_theme_cache_dir( 'purge_theme_caches' ) ) . '*' );

  foreach ( $files as $file ) {
    if ( is_file( $file ) ) {
      unlink( $file );
    }
  }

  fictioneer_clear_all_cached_partials();

  // Cached story cards
  fictioneer_purge_story_card_cache();

  // Cache busting string
  fictioneer_regenerate_cache_bust();

  // Transients (fast)
  fictioneer_delete_layout_transients();
  fictioneer_delete_transients_like( 'fictioneer_shortcode' );
  fictioneer_delete_transients_like( 'fictioneer_', true ); // Fast, but not safe for external object caches

  // Object cache
  wp_cache_flush();

  // Log
  fictioneer_log( __( 'Purged theme caches.', 'fictioneer' ) );
}

/**
 * Action wrapper to purge theme caches
 *
 * @since 5.2.5
 * @since 5.19.0 - Split up into two functions.
 */

function fictioneer_tools_purge_theme_caches() {
  // Verify request
  fictioneer_verify_admin_action( 'fictioneer_tools_purge_theme_caches' );

  // Purge and log
  fictioneer_purge_theme_caches();

  // Finish
  fictioneer_finish_admin_action( 'fictioneer-purge-theme-caches' );
}
add_action( 'admin_post_fictioneer_tools_purge_theme_caches', 'fictioneer_tools_purge_theme_caches' );

// =============================================================================
// MIGRATION TOOLS ACTIONS
// =============================================================================

/**
 * Append chapters to a story by ID
 *
 * @since 5.8.6
 */

function fictioneer_tools_append_chapters() {
  // Verify request
  fictioneer_verify_admin_action( 'fictioneer_tools_append_chapters' );

  // Story ID
  $story_id = absint( $_REQUEST['story_id'] ?? 0 );
  $story_post = get_post( $story_id );
  $action = $_REQUEST['preview'] ?? 0 ? 'preview' : 'perform';
  $back_link = '<a href="' . wp_get_referer() . '">' . __( '← Back to tools', 'fictioneer' ) . '</a>';

  // Abort if...
  if ( empty( $story_id ) || ! $story_post || get_post_type( $story_id ) !== 'fcn_story' ) {
    wp_die(
      sprintf(
        _x(
          '<p>Invalid or missing story ID.</p><p style="margin-top: -10px;">%s</p>',
          'Migration tools invalid story ID.',
          'fictioneer'
        ),
        $back_link
      ),
      400
    );
  }

  // Query chapters to append
  $query_args = array(
    'post_type' => 'fcn_chapter',
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'asc',
    'posts_per_page' => -1,
    'meta_key' => 'fictioneer_chapter_story',
    'meta_value' => $story_id,
    'update_post_meta_cache' => false, // Improve performance
    'update_post_term_cache' => false, // Improve performance
    'no_found_rows' => true // Improve performance
  );

  if ( $action === 'perform' ) {
    $query_args['fields'] = 'ids';
  }

  $chapters_to_append = new WP_Query( $query_args );

  // Show preview and exit...
  if ( $action === 'preview' ) {
    $previews = [];
    $story_data = fictioneer_get_story_data( $story_id, false );
    $description = sprintf(
      "<p>The chapters considered for appending to <strong>%s</strong> (#%s), in order of publication. Chapters which are already listed will be skipped. Beware that all post ownership restrictions will be ignored.</p><p style='margin-top: -10px;'>%s</p>",
      $story_data['title'],
      $story_id,
      $back_link
    );
    $thead = '<tr><th style="padding-right: 24px;">' . __( 'ID', 'fictioneer' ) . '</th><th style="padding-right: 24px;">'
      . __( 'Datetime', 'fictioneer' ) . '</th><th>' . __( 'Chapter', 'fictioneer' ) . '</th></tr>';

    foreach ( $chapters_to_append->posts as $chapter ) {
      $previews[] = "<tr><td style='padding-right: 24px;'>{$chapter->ID}</td><td style='padding-right: 24px;'>{$chapter->post_date}</td><td>{$chapter->post_title}</td></tr>";
    }

    wp_die(
      '<h1>' . __( 'Preview', 'fictioneer' ) . '</h1>' . $description .
        '<table style="text-align: left;"><thead>' . $thead . '</thead><tbody>' . implode( '', $previews ) . '</tbody></table>',
      __( 'Preview', 'fictioneer' ),
      200
    );
  }

  // ... or append chapters
  $chapter_ids_to_append = array_map( 'strval', $chapters_to_append->posts );

  if ( ! empty( $chapter_ids_to_append ) ) {
    // Prepare
    $story_chapters = fictioneer_get_story_chapter_ids( $story_id );
    $count = 0;

    // Append missing chapters
    foreach ( $chapter_ids_to_append as $chapter_id ) {
      if ( ! in_array( $chapter_id, $story_chapters ) ) {
        $story_chapters[] = $chapter_id;
        $count++;
      }
    }

    // Anything added?
    if ( $count > 0 ) {
      // Make double sure IDs are unique
      $story_chapters = array_unique( $story_chapters );

      // Save updated list
      update_post_meta( $story_id, 'fictioneer_story_chapters', $story_chapters );

      // Remember when chapter list has been last updated
      update_post_meta( $story_id, 'fictioneer_chapters_modified', current_time( 'mysql', true ) );
      update_post_meta( $story_id, 'fictioneer_chapters_added', current_time( 'mysql', true ) );

      // Clear meta caches to ensure they get refreshed
      delete_post_meta( $story_id, 'fictioneer_story_data_collection' );
      delete_post_meta( $story_id, 'fictioneer_story_chapter_index_html' );

      // Update story post to fire associated actions
      wp_update_post( array( 'ID' => $story_id ) );
    }

    // Redirect
    wp_safe_redirect(
      add_query_arg(
        array(
          'success' => 'fictioneer-chapters-appended',
          'data' => $count
        ),
        wp_get_referer()
      )
    );

    exit;
  }

  // Nothing found
  wp_die(
    sprintf(
      __( '<p>No associated published chapters found.</p><p style="margin-top: -10px;">%s</p>', 'fictioneer' ),
      $back_link
    ),
    400
  );
}
add_action( 'admin_post_fictioneer_tools_append_chapters', 'fictioneer_tools_append_chapters' );

// =============================================================================
// CONNECTION ACTIONS
// =============================================================================

/**
 * Get tiers from Patreon via OAuth
 *
 * @since 5.15.0
 */

function fictioneer_connection_get_patreon_tiers() {
  // Verify request
  fictioneer_verify_admin_action( 'fictioneer_connection_get_patreon_tiers' );

  // Setup
  $client_id = get_option( 'fictioneer_patreon_client_id' );
  $client_secret = get_option( 'fictioneer_patreon_client_secret' );

  // OAuth working?
  if ( ! get_option( 'fictioneer_enable_oauth' ) || ! $client_id || ! $client_secret ) {
    wp_safe_redirect(
      add_query_arg(
        array( 'failure' => 'fictioneer-patreon-not-connected' ),
        admin_url( 'admin.php?page=fictioneer_connections' )
      )
    );

    exit;
  }

  // Prepare request
  $params = array(
    'response_type' => 'code',
    'client_id' => $client_id,
    'state' => hash( 'sha256', microtime( TRUE ) . random_bytes( 15 ) . $_SERVER['REMOTE_ADDR'] ),
    'scope' => urlencode( 'campaigns' ),
    'redirect_uri' => get_site_url( null, FICTIONEER_OAUTH_ENDPOINT ),
    'force_verify' => 'true',
    'prompt' => 'consent',
    'access_type' => 'offline'
  );

  // Set cookie
  $value = fictioneer_encrypt(
    array(
      'state' => $params['state'],
      'action' => 'fictioneer_connection_get_patreon_tiers'
    )
  );

  if ( $value ) {
    setcookie(
      'fictioneer_oauth',
      $value,
      array(
        'expires' => time() + 300,
        'path' => '/',
        'domain' => '',
        'secure' => is_ssl(),
        'httponly' => true,
        'samesite' => 'Lax' // Necessary because of redirects
      )
    );
  }

  // Finish
  wp_redirect( add_query_arg( $params, 'https://www.patreon.com/oauth2/authorize' ) );
  exit();
}

if ( get_option( 'fictioneer_enable_oauth' ) ) {
  add_action( 'admin_post_fictioneer_connection_get_patreon_tiers', 'fictioneer_connection_get_patreon_tiers' );
}

/**
 * Delete Patreon tiers
 *
 * @since 5.15.0
 */

function fictioneer_connection_delete_patreon_tiers() {
  // Verify request
  fictioneer_verify_admin_action( 'fictioneer_connection_delete_patreon_tiers' );

  // Delete option
  delete_option( 'fictioneer_connection_patreon_tiers' );

  // Log
  fictioneer_log( __( 'Deleted global Patreon tiers.', 'fictioneer' ) );

  // Redirect
  wp_safe_redirect(
    add_query_arg(
      array( 'success' => 'fictioneer-patreon-tiers-deleted' ),
      admin_url( 'admin.php?page=fictioneer_connections' )
    )
  );

  exit;
}
add_action( 'admin_post_fictioneer_connection_delete_patreon_tiers', 'fictioneer_connection_delete_patreon_tiers' );

// =============================================================================
// PLUGINS ACTIONS
// =============================================================================

/**
 * Copies mu-plugin from theme to wp-content/mu-plugins
 *
 * @since 5.20.2
 *
 * @param string $plugin  File name of the plugin to copy.
 *
 * @return string True on success or false on failure.
 */

function fictioneer_copy_mu_plugin( $plugin ) {
  // Make sure directory exists
  fictioneer_initialize_mu_plugins();

  // Setup
  $source = get_template_directory() . '/mu-plugins/';
  $source_path = $source . $plugin;
  $destination_path = WPMU_PLUGIN_DIR . "/$plugin";

  // Source?
  if ( ! is_dir( $source ) ) {
    return false;
  }

  // File?
  if ( ! $plugin || ! file_exists( $source_path ) ) {
    return false;
  }

  // Copy file
  return copy( $source_path, $destination_path );
}

/**
 * Adds mu-plugin
 *
 * @since 5.20.1
 */

function fictioneer_enable_mu_plugin() {
  // Verify request
  fictioneer_verify_admin_action( 'fictioneer_enable_mu_plugin' );

  // Make sure directory exists
  fictioneer_initialize_mu_plugins();

  // Setup
  $source = get_template_directory() . '/mu-plugins/';
  $plugin = sanitize_text_field( $_GET['mu_plugin'] ?? 0 );
  $source_path = $source . $plugin;

  // Source?
  if ( ! is_dir( $source ) ) {
    wp_die( 'The mu-plugins subdirectory is missing in the theme directory.' );
  }

  // File?
  if ( ! $plugin || ! file_exists( $source_path ) ) {
    wp_die( 'File not found.' );
  }

  // Copy file
  if ( fictioneer_copy_mu_plugin( $plugin ) ) {
    $result = array( 'success' => 'fictioneer-mu-plugin-copy-success' );

    // Log
    fictioneer_log(
      sprintf(
        __( 'Enabled mu-plugin: %s', 'fictioneer' ),
        $plugin
      )
    );
  } else {
    $result = array( 'failure' => 'fictioneer-mu-plugin-copy-failure' );
  }

  // Redirect
  wp_safe_redirect( add_query_arg( $result, admin_url( 'admin.php?page=fictioneer_plugins' ) ) );

  exit;
}
add_action( 'admin_post_fictioneer_enable_mu_plugin', 'fictioneer_enable_mu_plugin' );

/**
 * Deletes mu-plugin
 *
 * @since 5.20.1
 */

function fictioneer_disable_mu_plugin() {
  // Verify request
  fictioneer_verify_admin_action( 'fictioneer_disable_mu_plugin' );

  // Make sure directory exists
  fictioneer_initialize_mu_plugins();

  // Setup
  $plugin = sanitize_text_field( $_GET['mu_plugin'] ?? 0 );
  $plugin_path = WPMU_PLUGIN_DIR . "/$plugin";

  // File?
  if ( ! $plugin || ! file_exists( $plugin_path ) ) {
    wp_die( 'File not found.' );
  }

  // Delete file
  if ( unlink( $plugin_path ) ) {
    $result = array( 'success' => 'fictioneer-mu-plugin-delete-success' );

    // Log
    fictioneer_log(
      sprintf(
        __( 'Disabled mu-plugin: %s', 'fictioneer' ),
        $plugin
      )
    );
  } else {
    $result = array( 'failure' => 'fictioneer-mu-plugin-delete-failure' );
  }

  // Redirect
  wp_safe_redirect( add_query_arg( $result, admin_url( 'admin.php?page=fictioneer_plugins' ) ) );

  exit;
}
add_action( 'admin_post_fictioneer_disable_mu_plugin', 'fictioneer_disable_mu_plugin' );

// =============================================================================
// THEME SETUP ACTIONS
// =============================================================================

/**
 * Saves the after-install setup form and redirects to settings page
 *
 * Note: Might be extended in the future to include more than general options.
 *
 * @since 5.21.0
 */

function fictioneer_after_install_setup() {
  // Verify request
  fictioneer_verify_admin_action( 'fictioneer_after_install_setup' );

  // Setup
  $booleans = array(
    'fictioneer_dark_mode_as_default', 'fictioneer_show_authors', 'fictioneer_enable_chapter_groups',
    'fictioneer_disable_default_formatting_indent', 'fictioneer_hide_large_card_chapter_list',
    'fictioneer_count_characters_as_words', 'fictioneer_enable_lightbox', 'fictioneer_enable_bookmarks',
    'fictioneer_enable_follows', 'fictioneer_enable_reminders', 'fictioneer_enable_checkmarks',
    'fictioneer_enable_suggestions', 'fictioneer_do_not_save_comment_ip', 'fictioneer_consent_wrappers',
    'fictioneer_cookie_banner', 'fictioneer_rewrite_chapter_permalinks', 'fictioneer_enable_all_blocks'
  );

  // Update options
  foreach ( $booleans as $option ) {
    update_option(
      $option,
      filter_var( $_POST[ $option ] ?? 0, FILTER_VALIDATE_BOOLEAN ),
      'yes'
    );
  }

  // Redirect to general theme settings
  wp_safe_redirect( admin_url( 'admin.php?page=fictioneer' ) );

  exit;
}
add_action( 'admin_post_fictioneer_after_install_setup', 'fictioneer_after_install_setup' );

// =============================================================================
// FIX LINE BREAKS
// =============================================================================

/**
 * Converts single new lines into double new lines
 *
 * @since 5.25.0
 */

function fictioneer_convert_line_breaks() {
  // Verify request
  fictioneer_verify_admin_action( 'fictioneer_convert_line_breaks' );

  // Setup
  $action = $_REQUEST['preview'] ?? 0 ? 'preview' : 'perform';
  $post_ids = fictioneer_explode_list( $_REQUEST['post_id'] ?? '' );
  $post_ids = array_map( 'absint', $post_ids );
  $successes = 0;
  $failures = 0;

  // Empty?
  if ( empty( $post_ids ) ) {
    wp_safe_redirect( admin_url( 'admin.php?page=fictioneer_tools' ) );

    exit;
  }

  // Preview?
  if ( $action === 'preview' ) {
    $output = [];

    foreach ( $post_ids as $post_id ) {
      $post = get_post( $post_id );

      if ( $post ) {
        $title = fictioneer_get_safe_title( $post );
        $type = fictioneer_get_post_type_label( $post->post_type );

        $output[] = "<tr><td style='padding-right: 24px;'>{$post_id}</td><td style='padding-right: 24px;'>{$type}</td><td>{$title}</td></tr>";
      }
    }

    if ( empty( $output ) ) {
      wp_die( __( 'No matching posts found.', 'fictioneer' ) );
    } else {
      $thead = '<tr><th style="padding-right: 24px;">' . __( 'ID', 'fictioneer' ) . '</th><th style="padding-right: 24px;">'
      . __( 'Post Type', 'fictioneer' ) . '</th><th>' . __( 'Title', 'fictioneer' ) . '</th></tr>';

      wp_die(
        '<h1>' . __( 'Preview', 'fictioneer' ) . '</h1><br><table style="text-align: left;">' .
        "<thead>{$thead}</thead>" .
        '<tbody>' . implode( '', $output ) . '</tbody></table>'
      );
    }
  }

  // Fix new lines...
  foreach ( $post_ids as $post_id ) {
    $post = get_post( $post_id );

    if ( ! $post ) {
      continue;
    }

    $content = $post->post_content;

    // Normalize line endings
    $content = str_replace( ["\r\n", "\r"], "\n", $content );

    // Convert any single line breaks to double line breaks
    $content = preg_replace( '/([^\n])\n([^\n])/', "$1\n\n$2", $content );

    // Update post
    $result = wp_update_post(
      array(
        'ID' => $post_id,
        'post_content' => $content
      )
    );

    if ( $result && ! is_wp_error( $result ) ) {
      $successes++;
    } else {
      $failures++;
    }
  }

  // Redirect
  wp_safe_redirect(
    add_query_arg(
      array(
        'success' => 'fictioneer-fix-line-breaks-success',
        'data' => "{$successes},{$failures}"
      ),
      admin_url( 'admin.php?page=fictioneer_tools' )
    )
  );

  exit;
}
add_action( 'admin_post_fictioneer_convert_line_breaks', 'fictioneer_convert_line_breaks' );
