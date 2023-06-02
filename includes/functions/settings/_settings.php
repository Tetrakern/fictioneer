<?php

// =============================================================================
// INCLUDES
// =============================================================================

require_once( '_register_settings.php' );
require_once( '_settings_loggers.php' );
require_once( '_settings_ajax.php' );
require_once( '_settings_actions.php' );

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
					<a href="https://github.com/Tetrakern/fictioneer/releases" target="_blank"><?php echo FICTIONEER_VERSION; ?></a>
					&bull;
					<a href="https://github.com/Tetrakern/fictioneer#readme" target="_blank"><?php
            _e( 'Documentation on Github', 'fictioneer' );
          ?></a>
					&bull;
					<a href="https://ko-fi.com/tetrakern" target="_blank"><?php _e( 'Support on Ko-fi', 'fictioneer' ); ?></a>
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
}

/**
 * Callback for general settings page
 *
 * @since Fictioneer 4.7
 */

function fictioneer_settings_general() {
	get_template_part( 'includes/functions/settings/_settings_page_general' );
}

/**
 * Callback for epubs settings page
 *
 * @since Fictioneer 4.7
 */

function fictioneer_settings_epubs() {
	get_template_part( 'includes/functions/settings/_settings_page_epubs' );
}

/**
 * Callback for SEO settings page
 *
 * @since Fictioneer 4.7
 */

function fictioneer_settings_seo() {
	get_template_part( 'includes/functions/settings/_settings_page_seo' );
}

/**
 * Callback for phrases settings page
 *
 * @since Fictioneer 4.7
 */

function fictioneer_settings_phrases() {
	get_template_part( 'includes/functions/settings/_settings_page_phrases' );
}

/**
 * Callback for tools settings page
 *
 * @since Fictioneer 4.7
 */

function fictioneer_settings_tools() {
	get_template_part( 'includes/functions/settings/_settings_page_tools' );
}

/**
 * Callback for logs settings page
 *
 * @since Fictioneer 4.7
 */

function fictioneer_settings_logs() {
	get_template_part( 'includes/functions/settings/_settings_page_logs' );
}
