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
	$fictioneer_plugins = array_filter(
		get_option( 'active_plugins' ) ?: [],
		function( $plugin ) {
			return strpos( $plugin, 'fictioneer' ) !== false;
		}
	);

	add_menu_page(
    __( 'Fictioneer Settings', 'fictioneer' ),
    __( 'Fictioneer', 'fictioneer' ),
    'manage_options',
    'fictioneer',
    'fictioneer_settings_general',
    'dashicons-admin-generic',
    61
  );

	$general_hook = add_submenu_page(
		'fictioneer',
		__( 'General', 'fictioneer' ),
		__( 'General', 'fictioneer' ),
		'manage_options',
		'fictioneer',
		'fictioneer_settings_general'
	);

	$roles_hook = add_submenu_page(
		'fictioneer',
		__( 'Roles', 'fictioneer' ),
		__( 'Roles', 'fictioneer' ),
		'manage_options',
		'fictioneer_roles',
		'fictioneer_settings_roles'
	);

	if ( ! empty( $fictioneer_plugins ) ) {
		$plugins_hook = add_submenu_page(
			'fictioneer',
			__( 'Plugins', 'fictioneer' ),
			__( 'Plugins', 'fictioneer' ),
			'manage_options',
			'fictioneer_plugins',
			'fictioneer_settings_plugins'
		);
	}

	$connections_hook = add_submenu_page(
		'fictioneer',
		__( 'Connections', 'fictioneer' ),
		__( 'Connections', 'fictioneer' ),
		'manage_options',
		'fictioneer_connections',
		'fictioneer_settings_connections'
	);

	$phrases_hook = add_submenu_page(
		'fictioneer',
		__( 'Phrases', 'fictioneer' ),
		__( 'Phrases', 'fictioneer' ),
		'manage_options',
		'fictioneer_phrases',
		'fictioneer_settings_phrases'
	);

	$epubs_hook = add_submenu_page(
		'fictioneer',
		__( 'ePUBs', 'fictioneer' ),
		__( 'ePUBs', 'fictioneer' ),
		'manage_options',
		'fictioneer_epubs',
		'fictioneer_settings_epubs'
	);

	$seo_hook = add_submenu_page(
		'fictioneer',
		__( 'SEO', 'fictioneer' ),
		__( 'SEO', 'fictioneer' ),
		'manage_options',
		'fictioneer_seo',
		'fictioneer_settings_seo'
	);

	$tools_hook = add_submenu_page(
		'fictioneer',
		__( 'Tools', 'fictioneer' ),
		__( 'Tools', 'fictioneer' ),
		'manage_options',
		'fictioneer_tools',
		'fictioneer_settings_tools'
	);

	$logs_hook = add_submenu_page(
		'fictioneer',
		__( 'Logs', 'fictioneer' ),
		__( 'Logs', 'fictioneer' ),
		'manage_options',
		'fictioneer_logs',
		'fictioneer_settings_logs'
	);

	add_action( 'admin_init', 'fictioneer_register_settings' );

	// Add screen options
  add_action( "load-{$epubs_hook}", 'fictioneer_settings_epubs_screen_options' );
	add_action( "load-{$seo_hook}", 'fictioneer_settings_seo_screen_options' );
}
add_action( 'admin_menu', 'fictioneer_add_admin_menu' );

/**
 * Configure the screen options for the ePUBs page
 *
 * @since Fictioneer 5.7.2
 */

function fictioneer_settings_epubs_screen_options() {
  // Add pagination option
	$args = array(
		'label' => __( 'ePUBs per page', 'fcnes' ),
		'default' => 25,
		'option' => 'fictioneer_epubs_per_page'
	);
	add_screen_option( 'per_page', $args );
}

/**
 * Configure the screen options for the SEO page
 *
 * @since Fictioneer 5.7.2
 */

function fictioneer_settings_seo_screen_options() {
  // Add pagination option
	$args = array(
		'label' => __( 'Items per page', 'fcnes' ),
		'default' => 25,
		'option' => 'fictioneer_seo_items_per_page'
	);
	add_screen_option( 'per_page', $args );
}

/**
 * Save custom screen options values
 *
 * @since Fictioneer 5.7.2
 *
 * @param bool   $status  The current status of the screen option saving.
 * @param string $option  The name of the screen option being saved.
 * @param mixed  $value   The value of the screen option being saved.
 *
 * @return bool The updated status of the screen option saving.
 */

function fictioneer_save_screen_options( $status, $option, $value ) {
  // ePUBs per page
  if ( $option === 'fictioneer_epubs_per_page' ) {
    fictioneer_update_user_meta( get_current_user_id(), $option, $value );
  }

  // Updated per page
  if ( $option === 'fictioneer_seo_items_per_page' ) {
    fictioneer_update_user_meta( get_current_user_id(), $option, $value );
  }

  return $status;
}
add_filter( 'set-screen-option', 'fictioneer_save_screen_options', 10, 3 );

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
		<div class='fictioneer-pagination'>
			<?php if ( $max_items ) : ?>
				<span class="number-of-items"><?php printf( _n( '%s item', '%s items', $max_items, 'fictioneer' ), $max_items ); ?></span>
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
		$fictioneer_plugins = array_filter(
			get_option( 'active_plugins' ) ?: [],
			function( $plugin ) {
				return strpos( $plugin, 'fictioneer' ) !== false;
			}
		);

		// General tab
		$output['general'] = '<a href="?page=fictioneer" class="fictioneer-settings__nav-tab' . ( $tab == 'general' ? ' active' : '' ) . '">' . __( 'General', 'fictioneer' ) . '</a>';

		// Roles tab
		$output['roles'] = '<a href="?page=fictioneer_roles" class="fictioneer-settings__nav-tab' . ( $tab == 'roles' ? ' active' : '' ) . '">' . __( 'Roles', 'fictioneer' ) . '</a>';

		// Plugins tab
		if ( ! empty( $fictioneer_plugins ) ) {
			$output['plugins'] = '<a href="?page=fictioneer_plugins" class="fictioneer-settings__nav-tab' . ( $tab == 'plugins' ? ' active' : '' ) . '">' . __( 'Plugins', 'fictioneer' ) . '</a>';
		}

		// Connections tab
		$output['connections'] = '<a href="?page=fictioneer_connections" class="fictioneer-settings__nav-tab' . ( $tab == 'connections' ? ' active' : '' ) . '">' . __( 'Connections', 'fictioneer' ) . '</a>';

		// Phrases tab
		$output['phrases'] = '<a href="?page=fictioneer_phrases" class="fictioneer-settings__nav-tab' . ( $tab == 'phrases' ? ' active' : '' ) . '">' . __( 'Phrases', 'fictioneer' ) . '</a>';

		// EPubs tab
		$output['epubs'] = '<a href="?page=fictioneer_epubs" class="fictioneer-settings__nav-tab' . ( $tab == 'epubs' ? ' active' : '' ) . '">' . __( 'ePUBs', 'fictioneer' ) . '</a>';

		// SEO tab (if enabled)
		if ( get_option( 'fictioneer_enable_seo' ) ) {
			$output['seo'] = '<a href="?page=fictioneer_seo" class="fictioneer-settings__nav-tab' . ( $tab == 'seo' ? ' active' : '' ) . '">' . __( 'SEO', 'fictioneer' ) . '</a>';
		}

		// Tools tab
		$output['tools'] = '<a href="?page=fictioneer_tools" class="fictioneer-settings__nav-tab' . ( $tab == 'tools' ? ' active' : '' ) . '">' . __( 'Tools', 'fictioneer' ) . '</a>';

		// Logs tab
		$output['logs'] = '<a href="?page=fictioneer_logs" class="fictioneer-settings__nav-tab' . ( $tab == 'logs' ? ' active' : '' ) . '">' . __( 'Logs', 'fictioneer' ) . '</a>';

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
					<a href="https://ko-fi.com/tetrakern" target="_blank"><?php _e( 'Support me on Ko-fi', 'fictioneer' ); ?></a>
				</div>
        <?php if ( CHILD_VERSION && CHILD_NAME ) : ?>
          <div class="fictioneer-settings__header-links">
            <span><?php echo CHILD_VERSION; ?> &bull; <?php echo CHILD_NAME; ?></span>
          </div>
        <?php endif; ?>
			</div>
			<nav class="fictioneer-settings__nav"><?php echo implode( '', $output ); ?></nav>
		</header>
		<?php // <--- End HTML
	}
}

// =============================================================================
// ADMIN MENU PAGE HTML
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
 * Callback for roles settings page
 *
 * @since Fictioneer 4.7
 */

function fictioneer_settings_roles() {
	get_template_part( 'includes/functions/settings/_settings_page_roles' );
}

/**
 * Callback for plugins settings page
 *
 * @since Fictioneer 5.7.1
 */

function fictioneer_settings_plugins() {
	get_template_part( 'includes/functions/settings/_settings_page_plugins' );
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

// =============================================================================
// ADMIN CONTENT HELPERS
// =============================================================================

/**
 * Renders a role settings capability card
 *
 * @since Fictioneer 5.6.0
 *
 * @param string $title  The title of the card.
 * @param array  $caps   An array of capabilities.
 * @param array  $role   The WP_Role or equivalent array for the role being checked.
 */

function fictioneer_admin_capability_card( $title, $caps, $role ) {
	// Start HTML ---> ?>
	<div class="fictioneer-card">
		<div class="fictioneer-card__wrapper">
			<h3 class="fictioneer-card__header"><?php echo $title; ?></h3>
			<div class="fictioneer-card__content">
				<div class="fictioneer-card__row fictioneer-card__row--capabilities">
					<?php
						foreach ( $caps as $cap ) {
							$role_caps = $role['capabilities'];
							$set = in_array( $cap, $role_caps ) && ( $role_caps[ $cap ] ?? 0 );
							$name = str_replace( '_', ' ', $cap );
							$name = str_replace( 'fcn ', '', $name );
							$name = ucwords( $name );

							// Special cases
							$name = $name == 'Unfiltered Html' ? 'Unfiltered HTML' : $name;
							$name = str_replace( 'Recommendations', 'Recommend.', $name );
							$name = str_replace( 'Custom Css', 'Custom CSS', $name );
							$name = str_replace( 'Seo Meta', 'SEO Meta', $name );
							$name = str_replace( 'Custom Page Css', 'Custom Page CSS', $name );
							$name = str_replace( 'Custom Epub Css', 'Custom ePUB CSS', $name );

							fictioneer_capability_checkbox( $cap, $name, $set );
						}
					?>
				</div>
			</div>
		</div>
	</div>
	<?php // <--- End HTML
}

/**
 * Renders a checkbox for a specific capability
 *
 * @since Fictioneer 5.6.0
 *
 * @param string $cap   The capability slug/string.
 * @param string $name  Human-readable name for the capability.
 * @param bool   $set   Whether the checkbox should be checked or not.
 */

function fictioneer_capability_checkbox( $cap, $name, $set ) {
  // Start HTML ---> ?>
  <label class="fictioneer-capability-checkbox">
    <input type="hidden" name="caps[<?php echo $cap; ?>]" value="0">
    <input class="fictioneer-capability-checkbox__input" name="caps[<?php echo $cap; ?>]" type="checkbox" <?php echo $set ? 'checked' : ''; ?> value="1">
    <div class="fictioneer-capability-checkbox__name"><?php echo $name; ?></div>
  </label>
  <?php // <--- End HTML
}

/**
 * Renders a label-wrapped setting checkbox
 *
 * @since Fictioneer 5.7.2
 *
 * @param string $option     The name of the setting option.
 * @param string $sub_label  Optional. The description below the label.
 */

function fictioneer_label_checkbox( $option, $sub_label = null ) {
	// Start HTML ---> ?>
	<label class="fictioneer-label-checkbox" for="<?php echo $option; ?>">
		<input name="<?php echo $option; ?>" type="checkbox" id="<?php echo $option; ?>" <?php echo checked( 1, get_option( $option ), false ); ?> value="1">
		<div>
			<span><?php echo FICTIONEER_OPTIONS['booleans'][ $option ]['label']; ?></span>
			<?php if ( ! empty( $sub_label ) ) : ?>
				<p class="fictioneer-sub-label"><?php echo $sub_label; ?></p>
			<?php endif; ?>
		</div>
	</label>
	<?php // <--- End HTML
}

/**
 * Renders a label-wrapped setting text field
 *
 * @since Fictioneer 5.7.2
 *
 * @param string $option  The name of the setting option.
 * @param string $type    Optional. The field type, default 'text'.
 */

function fictioneer_text_input( $option, $type = 'text' ) {
	// Start HTML ---> ?>
	<label class="fictioneer-label-textfield" for="<?php echo $option; ?>">
		<input name="<?php echo $option; ?>" placeholder="<?php echo FICTIONEER_OPTIONS['strings'][ $option ]['placeholder']; ?>" type="<?php echo $type; ?>" id="<?php echo $option; ?>" value="<?php echo esc_attr( get_option( $option ) ); ?>">
		<p class="fictioneer-sub-label"><?php echo FICTIONEER_OPTIONS['strings'][ $option ]['label']; ?></p>
	</label>
	<?php // <--- End HTML
}

/**
 * Renders a setting textarea
 *
 * @since Fictioneer 5.7.2
 *
 * @param string $option  The name of the setting option.
 * @param string $height  Optional. The height with unit, default 'auto'.
 */

function fictioneer_textarea( $option, $height = 'auto' ) {
	// Start HTML ---> ?>
	<textarea class="fictioneer-textarea" name="<?php echo $option; ?>" id="<?php echo $option; ?>" rows="4" style="height: <?php echo $height; ?>;"><?php echo get_option( $option ); ?></textarea>
	<p class="fictioneer-sub-label"><?php echo FICTIONEER_OPTIONS['strings'][ $option ]['label']; ?></p>
	<?php // <--- End HTML
}

/**
 * Renders a setting page assignment select field
 *
 * @since Fictioneer 5.7.2
 *
 * @param string $option  The name of the setting option.
 */

function fictioneer_page_assignment( $option ) {
	wp_dropdown_pages(
		array(
			'name' => $option,
			'id' => $option,
			'option_none_value' => _x( 'None', 'No selection page assignment.', 'fictioneer' ),
			'show_option_no_change' => _x( 'None', 'No selection page assignment.', 'fictioneer' ),
			'selected' => get_option( $option )
		)
	);

	echo '<p class="fictioneer-sub-label">' . FICTIONEER_OPTIONS['integers'][ $option ]['label'] . '</p>';
}
