<?php

// =============================================================================
// INCLUDES
// =============================================================================

require_once( '_register_settings.php' );
require_once( '_settings_loggers.php' );
require_once( '_settings_ajax.php' );

// =============================================================================
// ADD ADMIN MENU
// =============================================================================

/**
 * Add Fictioneer settings menu to admin panel
 *
 * @since Fictioneer 4.0
 */

function fictioneer_add_admin_menu() {
	add_menu_page(
    __( 'Fictioneer Settings', 'fictioneer' ),
    __( 'Fictioneer', 'fictioneer' ),
    'manage_options',
    'fictioneer',
    'fictioneer_settings_general',
    'dashicons-admin-generic',
    61
  );

	add_submenu_page(
		'fictioneer',
		__( 'General', 'fictioneer' ),
		__( 'General', 'fictioneer' ),
		'manage_options',
		'fictioneer',
		'fictioneer_settings_general'
	);

	add_submenu_page(
		'fictioneer',
		__( 'Connections', 'fictioneer' ),
		__( 'Connections', 'fictioneer' ),
		'manage_options',
		'fictioneer_connections',
		'fictioneer_settings_connections'
	);

	add_submenu_page(
		'fictioneer',
		__( 'Phrases', 'fictioneer' ),
		__( 'Phrases', 'fictioneer' ),
		'manage_options',
		'fictioneer_phrases',
		'fictioneer_settings_phrases'
	);

	add_submenu_page(
		'fictioneer',
		__( 'ePUBs', 'fictioneer' ),
		__( 'ePUBs', 'fictioneer' ),
		'manage_options',
		'fictioneer_epubs',
		'fictioneer_settings_epubs'
	);

	add_submenu_page(
		'fictioneer',
		__( 'SEO', 'fictioneer' ),
		__( 'SEO', 'fictioneer' ),
		'manage_options',
		'fictioneer_seo',
		'fictioneer_settings_seo'
	);

	add_submenu_page(
		'fictioneer',
		__( 'Tools', 'fictioneer' ),
		__( 'Tools', 'fictioneer' ),
		'manage_options',
		'fictioneer_tools',
		'fictioneer_settings_tools'
	);

	add_submenu_page(
		'fictioneer',
		__( 'Logs', 'fictioneer' ),
		__( 'Logs', 'fictioneer' ),
		'manage_options',
		'fictioneer_logs',
		'fictioneer_settings_logs'
	);

	add_action( 'admin_init', 'fictioneer_register_settings' );
}
add_action( 'admin_menu', 'fictioneer_add_admin_menu' );

// =============================================================================
// ADMIN MENU PAGINATION
// =============================================================================

if ( ! function_exists( 'fictioneer_admin_pagination' ) ) {
  /**
   * Pagination on admin page
   *
   * @since Fictioneer 4.7
   *
   * @param int    $per_page  Items per page.
   * @param int    $max_pages Total number of pages.
	 * @param int    $max_items Total number of items.
	 * @param string $page_var  Pagination query variable. Default 'paged'.
   */

	function fictioneer_admin_pagination( $per_page, $max_pages, $max_items = false, $page_var = 'paged' ) {
		$current_page = isset( $_GET[ $page_var ] ) ? absint( $_GET[ $page_var ] ) : 1;
		$previous_page = max( $current_page - 1, 0 );
		$next_page = $current_page + 1;
		$page_links = [];

		if ( $previous_page > 0 ) {
			$page_links[] = '<a class="first-page button" href="' . get_pagenum_link( 0 ) . '"><span>&laquo;</span></a>';
			$page_links[] = '<a class="prev-page button" href="' . get_pagenum_link( $previous_page ) . '"><span>&lsaquo;</span></a>';
		} else {
			$page_links[] = '<span class="button disabled">&laquo;</span>';
			$page_links[] = '<span class="button disabled">&lsaquo;</span>';
		}

		$page_links[] = '<span class="current-of-total">' . sprintf( _x( '%1$s of %2$s', 'Pagination', 'fictioneer' ), $current_page, $max_pages ) . '</span>';

		if ( $next_page <= $max_pages ) {
			$page_links[] = '<a class="next-page button" href="' . get_pagenum_link( $next_page ) . '"><span>&rsaquo;</span></a>';
			$page_links[] = '<a class="last-page button" href="' . get_pagenum_link( $max_pages ) . '"><span>&raquo;</span></a>';
		} else {
			$page_links[] = '<span class="button disabled">&rsaquo;</span>';
			$page_links[] = '<span class="button disabled">&raquo;</span>';
		}
		// Start HTML ---> ?>
		<div class='pagination'>
			<?php if ( $max_items ) : ?>
				<span class="number-of-items"><?php printf( _n( '%s item', '%s items', $max_items, 'fictioneer' ), $max_items ) ?></span>
			<?php endif; ?>
			<span class="pages"><?php echo implode( ' ', $page_links ); ?></span>
		</div>
		<?php // <--- End HTML
	}
}

// =============================================================================
// GET DEFAULT GENRES
// =============================================================================

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

// =============================================================================
// GET DEFAULT TAGS
// =============================================================================

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
// SETTINGS ACTION: CONVERT STORY TAGS TO GENRES
// =============================================================================

if ( ! function_exists( 'fictioneer_move_story_tags_to_genres' ) ) {
  /**
   * Convert story tags to genres
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_move_story_tags_to_genres() {
    fictioneer_convert_taxonomies( 'fcn_story', 'fcn_genre', 'post_tag', true, true );
    $update = __( 'Story tags converted to genres.', 'fictioneer' );
    fictioneer_update_log( $update );
  }
}

// =============================================================================
// SETTINGS ACTION: DUPLICATE STORY TAGS TO GENRES
// =============================================================================

if ( ! function_exists( 'fictioneer_duplicate_story_tags_to_genres' ) ) {
  /**
   * Duplicate story tags to genres
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_duplicate_story_tags_to_genres() {
    fictioneer_convert_taxonomies( 'fcn_story', 'fcn_genre', 'post_tag', true );
    $update = __( 'Story tags duplicated as genres.', 'fictioneer' );
    fictioneer_update_log( $update );
  }
}

// =============================================================================
// SETTINGS ACTION: CONVERT CHAPTER TAGS TO GENRES
// =============================================================================

if ( ! function_exists( 'fictioneer_move_chapter_tags_to_genres' ) ) {
  /**
   * Convert chapter tags to genres
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_move_chapter_tags_to_genres() {
    fictioneer_convert_taxonomies( 'fcn_chapter', 'fcn_genre', 'post_tag', true, true );
    $update = __( 'Chapter tags converted to genres.', 'fictioneer' );
    fictioneer_update_log( $update );
  }
}

// =============================================================================
// SETTINGS ACTION: DUPLICATE CHAPTER TAGS TO GENRES
// =============================================================================

if ( ! function_exists( 'fictioneer_duplicate_chapter_tags_to_genres' ) ) {
  /**
   * Duplicate chapter tags to genres
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_duplicate_chapter_tags_to_genres() {
    fictioneer_convert_taxonomies( 'fcn_chapter', 'fcn_genre', 'post_tag', true );
    $update = __( 'Chapter tags duplicated as genres.', 'fictioneer' );
    fictioneer_update_log( $update );
  }
}

// =============================================================================
// SETTINGS ACTION: APPEND DEFAULT GENRES
// =============================================================================

if ( ! function_exists( 'fictioneer_append_default_genres' ) ) {
  /**
   * Append default genres
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_append_default_genres() {
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
    $update = __( 'Added default genres', 'fictioneer' );
    fictioneer_update_log( $update );
  }
}

// =============================================================================
// SETTINGS ACTION: APPEND DEFAULT TAGS
// =============================================================================

if ( ! function_exists( 'fictioneer_append_default_tags' ) ) {
  /**
   * Append default tags
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_append_default_tags() {
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
    $update = __( 'Added default tags', 'fictioneer' );
    fictioneer_update_log( $update );
  }
}

// =============================================================================
// SETTINGS ACTION: REMOVE UNUSED TAGS
// =============================================================================

if ( ! function_exists( 'fictioneer_remove_unused_tags' ) ) {
  /**
   * Remove unused tags
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_remove_unused_tags() {
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
    $update = __( 'Removed unused tags', 'fictioneer' );
    fictioneer_update_log( $update );
  }
}

// =============================================================================
// SETTINGS ACTION: PURGE STORY DATA CACHES
// =============================================================================

if ( ! function_exists( 'fictioneer_purge_story_data_caches' ) ) {
  /**
   * Purge story data caches
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_purge_story_data_caches() {
    // Prepare query
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
    $update = __( 'Purged story data caches', 'fictioneer' );
    fictioneer_update_log( $update );
  }
}

// =============================================================================
// SETTINGS ACTION: FIX DATABASE
// =============================================================================

if ( ! function_exists( 'fictioneer_fix_users' ) ) {
  /**
   * Fix users in database as far as possible
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_fix_users() {
    // Setup
    // $users = get_users( array( 'fields' => 'ID' ) );

    // foreach( $users as $user_id ) {
    // }
  }
}

if ( ! function_exists( 'fictioneer_fix_chapters' ) ) {
  /**
   * Fix chapters in database as far as possible
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_fix_chapters() {
    // Setup
    // $chapters = get_posts( array( 'post_type' => 'fcn_chapter', 'numberposts' => -1 ) );

    // foreach( $chapters as $post ) {
    // }
  }
}

if ( ! function_exists( 'fictioneer_fix_collections' ) ) {
  /**
   * Fix collections in database as far as possible
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_fix_collections() {
    // Setup
    // $collections = get_posts( array( 'post_type' => 'fcn_collection', 'numberposts' => -1 ) );

    // foreach ( $collections as $post ) {
    // }
  }
}

if ( ! function_exists( 'fictioneer_fix_pages' ) ) {
  /**
   * Fix pages in database as far as possible
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_fix_pages() {
    // Setup
    // $pages = get_posts( array( 'post_type' => 'page', 'numberposts' => -1 ) );

    // foreach ( $pages as $post ) {
    // }
  }
}

if ( ! function_exists( 'fictioneer_fix_posts' ) ) {
  /**
   * Fix posts in database as far as possible
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_fix_posts() {
    // Setup
    // $posts = get_posts( array( 'post_type' => 'post', 'numberposts' => -1 ) );

    // foreach ( $posts as $post ) {
    // }
  }
}

if ( ! function_exists( 'fictioneer_fix_recommendations' ) ) {
  /**
   * Fix collections in database as far as possible
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_fix_recommendations() {
    // Setup
    // $recommendations = get_posts( array( 'post_type' => 'fcn_recommendation', 'numberposts' => -1 ) );

    // foreach ( $recommendations as $post ) {
    // }
  }
}

if ( ! function_exists( 'fictioneer_fix_stories' ) ) {
  /**
   * Fix collections in database as far as possible
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_fix_stories() {
    // Setup
    // $stories = get_posts( array( 'post_type' => 'fcn_story', 'numberposts' => -1 ) );

    // foreach ( $stories as $post ) {
    // }
  }
}

// =============================================================================
// RENDER SETTINGS SUCCESS NOTICE
// =============================================================================

// Success notices
if ( ! defined( 'FICTIONEER_ADMIN_SUCCESS_NOTICES' ) ) {
  define(
		'FICTIONEER_ADMIN_SUCCESS_NOTICES',
		array(
			'move_story_tags_to_genres' => __( 'All story tags have been converted to genres.', 'fictioneer' ),
			'duplicate_story_tags_to_genres' => __( 'All story tags have been duplicated to genres.', 'fictioneer' ),
			'move_chapter_tags_to_genres' => __( 'All chapter tags have been converted to genres.', 'fictioneer' ),
			'duplicate_chapter_tags_to_genres' => __( 'All chapter tags have been duplicated to genres.', 'fictioneer' ),
			'append_default_genres' => __( 'Default genres have been added to the list.', 'fictioneer' ),
			'append_default_tags' => __( 'Default tags have been added to the list.', 'fictioneer' ),
			'remove_unused_tags' => __( 'All unused tags have been removed.', 'fictioneer' ),
			'purge_all_epubs' => __( 'All ePUB files have been purged.', 'fictioneer' ),
      'purge_all_schemas' => __( 'All SEO schemas have been purged.', 'fictioneer' ),
      'purge_seo_meta_caches' => __( 'All SEO meta caches have been purged.', 'fictioneer' ),
			'purge_story_data_caches' => __( 'Story data caches have been purged.', 'fictioneer' ),
			'add_moderator_role' => __( 'Moderator role has been added.', 'fictioneer' ),
			'remove_moderator_role' => __( 'Moderator role has been removed.', 'fictioneer' ),
			'upgrade_author_role' => __( 'Author role has been upgraded.', 'fictioneer' ),
			'reset_author_role' => __( 'Author role has been reset.', 'fictioneer' ),
			'limit_editor_role' => __( 'Editor role has been limited.', 'fictioneer' ),
			'reset_editor_role' => __( 'Editor role has been reset.', 'fictioneer' ),
			'upgrade_contributor_role' => __( 'Contributor role has been upgraded.', 'fictioneer' ),
			'reset_contributor_role' => __( 'Contributor role has been reset.', 'fictioneer' ),
      'reset_post_relationship_registry' => __( 'Post relationship registry reset.', 'fictioneer' )
		)
	);
}

/**
 * Output admin success notices
 *
 * @since Fictioneer 5.0
 */

function fictioneer_admin_success_notices() {
  // Get performed action
  $action = $_GET['action'] ?? null;

  // Has action a notice?
  if ( $action && isset( FICTIONEER_ADMIN_SUCCESS_NOTICES[ $action ] ) ) {
    echo '<div class="notice notice-success is-dismissible"><p>' . FICTIONEER_ADMIN_SUCCESS_NOTICES[ $action ] . '</p></div>';
  }
}
add_action( 'admin_notices', 'fictioneer_admin_success_notices' );

// =============================================================================
// SETTINGS PAGE HEADER
// =============================================================================

if ( ! function_exists( 'fictioneer_settings_header' ) ) {
	/**
	 * Output HTML for the settings page header
	 *
	 * @since Fictioneer 4.7
	 *
	 * @param string $tab The current tab to highlight. Defaults to 'general'.
	 */

	function fictioneer_settings_header( $tab = 'general' ) {
		// Setup
		$output = [];

		// General tab
		$output['general'] = '<a href="?page=fictioneer" class="tab' . ( $tab == 'general' ? ' active' : '' ) . '">' . __( 'General', 'fictioneer' ) . '</a>';

		// Connections tab
		$output['connections'] = '<a href="?page=fictioneer_connections" class="tab' . ( $tab == 'connections' ? ' active' : '' ) . '">' . __( 'Connections', 'fictioneer' ) . '</a>';

		// Phrases tab
		$output['phrases'] = '<a href="?page=fictioneer_phrases" class="tab' . ( $tab == 'phrases' ? ' active' : '' ) . '">' . __( 'Phrases', 'fictioneer' ) . '</a>';

		// EPubs tab
		$output['epubs'] = '<a href="?page=fictioneer_epubs" class="tab' . ( $tab == 'epubs' ? ' active' : '' ) . '">' . __( 'ePUBs', 'fictioneer' ) . '</a>';

		// SEO tab (if enabled)
		if ( get_option( 'fictioneer_enable_seo' ) ) {
			$output['seo'] = '<a href="?page=fictioneer_seo" class="tab' . ( $tab == 'seo' ? ' active' : '' ) . '">' . __( 'SEO', 'fictioneer' ) . '</a>';
		}

		// Tools tab
		$output['tools'] = '<a href="?page=fictioneer_tools" class="tab' . ( $tab == 'tools' ? ' active' : '' ) . '">' . __( 'Tools', 'fictioneer' ) . '</a>';

		// Logs tab
		$output['logs'] = '<a href="?page=fictioneer_logs" class="tab' . ( $tab == 'logs' ? ' active' : '' ) . '">' . __( 'Logs', 'fictioneer' ) . '</a>';

		// Apply filters
		$output = apply_filters( 'fictioneer_filter_admin_settings_navigation', $output );

		// Start HTML ---> ?>
		<header class="fictioneer-settings__header">
			<div>
				<h1><?php _e( 'Fictioneer', 'fictioneer' ); ?></h1>
				<div class="fictioneer-settings__header-links">
					<a href="https://github.com/Tetrakern/fictioneer#readme" target="_blank"><?php
            echo FICTIONEER_VERSION;
            echo ' &bull; ';
            _e( 'Documentation on Github', 'fictioneer' );
          ?> <span class="dashicons dashicons-external"></span></a>
				</div>
        <?php if ( CHILD_VERSION && CHILD_NAME ) : ?>
          <div class="fictioneer-settings__header-links">
            <span><?php echo CHILD_VERSION; ?> &bull; <?php echo CHILD_NAME; ?></span>
          </div>
        <?php endif; ?>
			</div>
			<nav class="pill-nav"><?php echo implode( '', $output ); ?></nav>
		</header>
		<?php // <--- End HTML
	}
}

// =============================================================================
// ADMIN HTML
// =============================================================================

/**
 * Callback for connections settings page
 *
 * @since Fictioneer 4.7
 */

function fictioneer_settings_connections() {
	get_template_part( 'includes/functions/settings/_settings_page_connections' );
	fictioneer_clean_actions_from_url();
}

/**
 * Callback for general settings page
 *
 * @since Fictioneer 4.7
 */

function fictioneer_settings_general() {
	get_template_part( 'includes/functions/settings/_settings_page_general' );
	fictioneer_clean_actions_from_url();
}

/**
 * Callback for epubs settings page
 *
 * @since Fictioneer 4.7
 */

function fictioneer_settings_epubs() {
	get_template_part( 'includes/functions/settings/_settings_page_epubs' );
	fictioneer_clean_actions_from_url();
}

/**
 * Callback for SEO settings page
 *
 * @since Fictioneer 4.7
 */

function fictioneer_settings_seo() {
	get_template_part( 'includes/functions/settings/_settings_page_seo' );
	fictioneer_clean_actions_from_url();
}

/**
 * Callback for phrases settings page
 *
 * @since Fictioneer 4.7
 */

function fictioneer_settings_phrases() {
	get_template_part( 'includes/functions/settings/_settings_page_phrases' );
	fictioneer_clean_actions_from_url();
}

/**
 * Callback for tools settings page
 *
 * @since Fictioneer 4.7
 */

function fictioneer_settings_tools() {
	get_template_part( 'includes/functions/settings/_settings_page_tools' );
	fictioneer_clean_actions_from_url();
}

/**
 * Callback for logs settings page
 *
 * @since Fictioneer 4.7
 */

function fictioneer_settings_logs() {
	get_template_part( 'includes/functions/settings/_settings_page_logs' );
	fictioneer_clean_actions_from_url();
}
