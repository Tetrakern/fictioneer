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
  if ( ! current_user_can( 'administrator' ) || ! is_admin() ) {
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
			'fictioneer-purged-epubs' => __( 'All ePUB files have been purged.', 'fictioneer' ),
      'fictioneer-purged-seo-schemas' => __( 'All SEO schemas have been purged.', 'fictioneer' ),
      'fictioneer-purged-seo-meta-caches' => __( 'All SEO meta caches have been purged.', 'fictioneer' ),
			'fictioneer-purge-story-data-caches' => __( 'Story data caches have been purged.', 'fictioneer' ),
			'fictioneer-added-moderator-role' => __( 'Moderator role has been added.', 'fictioneer' ),
      'fictioneer-not-added-moderator-role' => __( 'Moderator role could not be added or already exists.', 'fictioneer' ),
			'fictioneer-removed-moderator-role' => __( 'Moderator role has been removed.', 'fictioneer' ),
			'fictioneer-upgraded-author-role' => __( 'Author role has been upgraded.', 'fictioneer' ),
			'fictioneer-reset-author-role' => __( 'Author role has been reset.', 'fictioneer' ),
			'fictioneer-upgraded-contributor-role' => __( 'Contributor role has been upgraded.', 'fictioneer' ),
			'fictioneer-reset-contributor-role' => __( 'Contributor role has been reset.', 'fictioneer' ),
      'fictioneer-reset-post-relationship-registry' => __( 'Post relationship registry reset.', 'fictioneer' ),
      'fictioneer-fix-users' => __( 'This function does currently not cover any issues.', 'fictioneer' ),
      'fictioneer-fix-stories' => __( 'This function does currently not cover any issues.', 'fictioneer' ),
      'fictioneer-fix-chapters' => __( 'This function does currently not cover any issues.', 'fictioneer' ),
      'fictioneer-fix-recommendations' => __( 'This function does currently not cover any issues.', 'fictioneer' ),
      'fictioneer-fix-collections' => __( 'This function does currently not cover any issues.', 'fictioneer' ),
      'fictioneer-fix-pages' => __( 'This function does currently not cover any issues.', 'fictioneer' ),
      'fictioneer-fix-posts' => __( 'This function does currently not cover any issues.', 'fictioneer' )
		)
	);
}

/**
 * Output admin settings notices
 *
 * @since Fictioneer 5.2.5
 */

function fictioneer_admin_settings_notices() {
  // Get performed action
  $success = $_GET['success'] ?? null;
  $failure = $_GET['failure'] ?? null;

  // Has success notice?
  if ( ! empty( $success ) && isset( FICTIONEER_ADMIN_SETTINGS_NOTICES[ $success ] ) ) {
    echo '<div class="notice notice-success is-dismissible"><p>' . FICTIONEER_ADMIN_SETTINGS_NOTICES[ $success ] . '</p></div>';
  }

  // Has failure notice?
  if ( ! empty( $failure ) && isset( FICTIONEER_ADMIN_SETTINGS_NOTICES[ $failure ] ) ) {
    echo '<div class="notice notice-error is-dismissible"><p>' . FICTIONEER_ADMIN_SETTINGS_NOTICES[ $failure ] . '</p></div>';
  }
}
add_action( 'admin_notices', 'fictioneer_admin_settings_notices' );

// =============================================================================
// EPUB ACTIONS
// =============================================================================

/**
 * Delete all generated ePUBs from the server
 *
 * @since Fictioneer 5.2.5
 */

function fictioneer_purge_all_epubs() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_purge_all_epubs' );

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
    __( 'Purged all generated ePUBs from the server.', 'fictioneer' )
  );

  // Finish
  fictioneer_finish_tool_action( 'fictioneer-purged-epubs' );
}
add_action( 'admin_post_fictioneer_purge_all_epubs', 'fictioneer_purge_all_epubs' );

// =============================================================================
// SEO ACTIONS
// =============================================================================

/**
 * Purge all SEO schemas
 *
 * This function deletes the "fictioneer_schema" meta data from all posts and pages
 * of specified post types.
 *
 * @since Fictioneer 5.2.5
 */

function fictioneer_purge_all_seo_schemas() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_purge_all_seo_schemas' );

  // Setup
  $all_ids = get_posts(
    array(
	    'post_type' => ['page', 'post', 'fcn_story', 'fcn_chapter', 'fcn_recommendation', 'fcn_collection'],
	    'numberposts' => -1,
	    'fields' => 'ids',
	    'update_post_meta_cache' => false,
	    'update_post_term_cache' => false
	  )
  );

  // If IDs were found...
  if ( ! empty( $all_ids ) ) {
    // Loop all IDs to delete schemas
    foreach ( $all_ids as $id ) {
      delete_post_meta( $id, 'fictioneer_schema' );
    }

    // Log
    fictioneer_log(
      __( 'Purged all schema graphs.', 'fictioneer' )
    );

    // Purge caches
    fictioneer_purge_all_caches();
  }

  // Finish
  fictioneer_finish_tool_action( 'fictioneer-purged-seo-schemas' );
}
add_action( 'admin_post_fictioneer_purge_all_seo_schemas', 'fictioneer_purge_all_seo_schemas' );

/**
 * Purge all SEO meta caches
 *
 * This function deletes the cached SEO meta values for all posts and custom post types
 * specified in the post_type parameter.
 *
 * @since Fictioneer 5.2.5
 */

function fictioneer_purge_seo_meta_caches() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_purge_seo_meta_caches' );

  // Setup
  $all_ids = get_posts(
    array(
	    'post_type' => ['page', 'post', 'fcn_story', 'fcn_chapter', 'fcn_recommendation', 'fcn_collection'],
	    'numberposts' => -1,
	    'fields' => 'ids',
	    'update_post_meta_cache' => false,
	    'update_post_term_cache' => false
	  )
  );

  // If IDs were found...
  if ( $all_ids ) {
    // Loop all IDs to delete SEO meta caches
    foreach ( $all_ids as $id ) {
      delete_post_meta( $id, 'fictioneer_seo_title_cache' );
      delete_post_meta( $id, 'fictioneer_seo_description_cache' );
      delete_post_meta( $id, 'fictioneer_seo_og_image_cache' );
    }

    // Log
    fictioneer_log(
      __( 'Purged all SEO meta caches.', 'fictioneer' )
    );

    // Purge caches
    fictioneer_purge_all_caches();
  }

  // Finish
  fictioneer_finish_tool_action( 'fictioneer-purged-seo-meta-caches' );
}
add_action( 'admin_post_fictioneer_purge_seo_meta_caches', 'fictioneer_purge_seo_meta_caches' );

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
 * Remove a moderator role
 *
 * @since Fictioneer 5.2.5
 */

function fictioneer_tools_remove_moderator_role() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_remove_moderator_role' );

  // Remove role
  remove_role( 'fcn_moderator' );

  // Log
  fictioneer_log( __( 'Moderator role removed.', 'fictioneer' ) );

  // Finish
  fictioneer_finish_tool_action( 'fictioneer-removed-moderator-role' );
}
add_action( 'admin_post_fictioneer_remove_moderator_role', 'fictioneer_tools_remove_moderator_role' );

/**
 * Upgrade author role
 *
 * @since Fictioneer 5.2.5
 */

function fictioneer_tools_upgrade_author_role() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_upgrade_author_role' );

  // Upgrade role
  fictioneer_upgrade_author_role();

  // Log
  fictioneer_log( __( 'Author role upgraded.', 'fictioneer' ) );

  // Finish
  fictioneer_finish_tool_action( 'fictioneer-upgraded-author-role' );
}
add_action( 'admin_post_fictioneer_upgrade_author_role', 'fictioneer_tools_upgrade_author_role' );

/**
 * Reset author role
 *
 * @since Fictioneer 5.2.5
 */

function fictioneer_tools_reset_author_role() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_reset_author_role' );

  // Reset role
  fictioneer_reset_author_role();

  // Log
  fictioneer_log( __( 'Author role reset.', 'fictioneer' ) );

  // Finish
  fictioneer_finish_tool_action( 'fictioneer-reset-author-role' );
}
add_action( 'admin_post_fictioneer_reset_author_role', 'fictioneer_tools_reset_author_role' );

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

function fictioneer_tools_purge_story_data_caches() {
  // Verify request
  fictioneer_verify_tool_action( 'fictioneer_purge_story_data_caches' );

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
    update_post_meta( $story_id, 'fictioneer_story_data_collection', false );
    delete_transient( 'fictioneer_story_chapter_list_' . $story_id );
  }

  // Log
  fictioneer_log( __( 'Purged story data caches.', 'fictioneer' ) );

  // Finish
  fictioneer_finish_tool_action( 'fictioneer-purge-story-data-caches' );
}
add_action( 'admin_post_fictioneer_purge_story_data_caches', 'fictioneer_tools_purge_story_data_caches' );

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

?>
