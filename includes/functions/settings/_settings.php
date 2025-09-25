<?php

// =============================================================================
// INCLUDES
// =============================================================================

require_once( '_register_settings.php' );
require_once( '_settings_actions.php' );

if ( wp_doing_ajax() ) {
  require_once( '_settings_ajax.php' );
}

// =============================================================================
// ADD ADMIN MENUS
// =============================================================================

/**
 * Add Fictioneer settings menu to admin panel
 *
 * @since 4.0.0
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

  $plugins_hook = add_submenu_page(
    'fictioneer',
    __( 'Plugins', 'fictioneer' ),
    __( 'Plugins', 'fictioneer' ),
    'manage_options',
    'fictioneer_plugins',
    'fictioneer_settings_plugins'
  );

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

  $fonts_hook = add_submenu_page(
    'fictioneer',
    __( 'Fonts', 'fictioneer' ),
    __( 'Fonts', 'fictioneer' ),
    'manage_options',
    'fictioneer_fonts',
    'fictioneer_settings_fonts'
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

  $setup_hook = add_submenu_page(
    'fictioneer',
    __( 'Setup', 'fictioneer' ),
    __( 'Setup', 'fictioneer' ),
    'manage_options',
    'fictioneer_setup',
    'fictioneer_settings_setup'
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
 * @since 5.7.2
 */

function fictioneer_settings_epubs_screen_options() {
  // Add pagination option
  $args = array(
    'label' => __( 'ePUBs per page', 'fictioneer' ),
    'default' => 25,
    'option' => 'fictioneer_epubs_per_page'
  );
  add_screen_option( 'per_page', $args );
}

/**
 * Configure the screen options for the SEO page
 *
 * @since 5.7.2
 */

function fictioneer_settings_seo_screen_options() {
  // Add pagination option
  $args = array(
    'label' => __( 'Items per page', 'fictioneer' ),
    'default' => 25,
    'option' => 'fictioneer_seo_items_per_page'
  );
  add_screen_option( 'per_page', $args );
}

/**
 * Save custom screen options values
 *
 * @since 5.7.2
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
// SETTINGS PAGE HEADER
// =============================================================================

if ( ! function_exists( 'fictioneer_settings_header' ) ) {
  /**
   * Output HTML for the settings page header
   *
   * @since 4.7
   *
   * @param string $tab  The current tab to highlight. Defaults to 'general'.
   */

  function fictioneer_settings_header( $tab = 'general' ) {
    // Ensure notifications table exists
    fictioneer_initialize_alerts();

    // Setup
    $output = [];

    $tabs = array(
      'general' => array( 'fictioneer', __( 'General', 'fictioneer' ) ),
      'roles' => array( 'fictioneer_roles', __( 'Roles', 'fictioneer' ) ),
      'plugins' => array( 'fictioneer_plugins', __( 'Plugins', 'fictioneer' ) ),
      'connections' => array( 'fictioneer_connections', __( 'Connections', 'fictioneer' ) ),
      'phrases' => array( 'fictioneer_phrases', __( 'Phrases', 'fictioneer' ) ),
      'fonts' => array( 'fictioneer_fonts', __( 'Fonts', 'fictioneer' ) ),
      'epubs' => array( 'fictioneer_epubs', __( 'ePUBs', 'fictioneer' ) ),
      'seo' => array( 'fictioneer_seo', __( 'SEO', 'fictioneer' ) ),
      'tools' => array( 'fictioneer_tools', __( 'Tools', 'fictioneer' ) ),
      'logs' => array( 'fictioneer_logs', __( 'Logs', 'fictioneer' ) )
    );

    // Build tabs
    foreach ( $tabs as $key => $tab_data ) {
      list( $page, $label ) = $tab_data;

      if ( $key === 'seo' && ! get_option( 'fictioneer_enable_seo' ) ) {
        continue;
      }

      $output[ $key ] = sprintf(
        '<a href="?page=%s" class="fictioneer-settings__nav-tab%s">%s</a>',
        esc_attr( $page ),
        $tab === $key ? ' active' : '',
        $label
      );
    }

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
            _e( 'Documentation on GitHub', 'fictioneer' );
          ?></a>
          &bull;
          <a data-fcn-dialog-target="fcn-sponsor-modal"><?php _e( 'Support the Development', 'fictioneer' ); ?></a>
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
// SETTINGS MENU PAGE HTML
// =============================================================================

/**
 * Callback for connections settings page.
 *
 * @since 4.7.0
 */

function fictioneer_settings_connections() {
  get_template_part( 'includes/functions/settings/_settings_page_connections' );
}

/**
 * Callback for general settings page.
 *
 * @since 4.7.0
 */

function fictioneer_settings_general() {
  get_template_part( 'includes/functions/settings/_settings_page_general' );
}

/**
 * Callback for roles settings page.
 *
 * @since 4.7.0
 */

function fictioneer_settings_roles() {
  get_template_part( 'includes/functions/settings/_settings_page_roles' );
}

/**
 * Callback for plugins settings page.
 *
 * @since 5.7.1
 */

function fictioneer_settings_plugins() {
  get_template_part( 'includes/functions/settings/_settings_page_plugins' );
}

/**
 * Callback for epubs settings page.
 *
 * @since 4.7.0
 */

function fictioneer_settings_epubs() {
  get_template_part( 'includes/functions/settings/_settings_page_epubs' );
}

/**
 * Callback for SEO settings page.
 *
 * @since 4.7.0
 */

function fictioneer_settings_seo() {
  get_template_part( 'includes/functions/settings/_settings_page_seo' );
}

/**
 * Callback for phrases settings page.
 *
 * @since 4.7.0
 */

function fictioneer_settings_phrases() {
  get_template_part( 'includes/functions/settings/_settings_page_phrases' );
}

/**
 * Callback for fonts settings page.
 *
 * @since 4.9.4
 */

function fictioneer_settings_fonts() {
  get_template_part( 'includes/functions/settings/_settings_page_fonts' );
}

/**
 * Callback for tools settings page.
 *
 * @since 4.7.0
 */

function fictioneer_settings_tools() {
  get_template_part( 'includes/functions/settings/_settings_page_tools' );
}

/**
 * Callback for logs settings page.
 *
 * @since 4.7.0
 */

function fictioneer_settings_logs() {
  get_template_part( 'includes/functions/settings/_settings_page_logs' );
}

/**
 * Callback for setup settings page.
 *
 * @since 5.20.3
 */

function fictioneer_settings_setup() {
  get_template_part( 'includes/functions/settings/_settings_page_setup' );
}

// =============================================================================
// SETTINGS CONTENT HELPERS
// =============================================================================

/**
 * Renders a role settings capability card
 *
 * @since 5.6.0
 *
 * @param string $title  The title of the card.
 * @param array  $caps   An array of capabilities.
 * @param array  $role   The WP_Role or equivalent array for the role being checked.
 */

function fictioneer_settings_capability_card( $title, $caps, $role ) {
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
              $name = fcntr_admin( $cap );

              if ( empty( $name ) ) {
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
                $name = str_replace( 'Custom Epub Upload', 'Custom ePUB Upload', $name );
              }

              fictioneer_settings_capability_checkbox( $cap, $name, $set );
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
 * @since 5.6.0
 *
 * @param string $cap   The capability slug/string.
 * @param string $name  Human-readable name for the capability.
 * @param bool   $set   Whether the checkbox should be checked or not.
 */

function fictioneer_settings_capability_checkbox( $cap, $name, $set ) {
  // Start HTML ---> ?>
  <label class="fictioneer-capability-checkbox">
    <input type="hidden" name="caps[<?php echo $cap; ?>]" value="0">
    <input class="fictioneer-capability-checkbox__input" name="caps[<?php echo $cap; ?>]" type="checkbox" <?php echo $set ? 'checked' : ''; ?> value="1" autocomplete="off">
    <div class="fictioneer-capability-checkbox__name"><?php echo $name; ?></div>
  </label>
  <?php // <--- End HTML
}

/**
 * Renders a label-wrapped setting checkbox
 *
 * @since 5.7.2
 * @since 5.23.1 - Added $image parameter.
 *
 * @param string      $option       The name of the setting option.
 * @param string      $label        Label of the setting.
 * @param string|null $description  Optional. The description below the label.
 * @param string|null $help         Optional. The text for the helper modal.
 * @param string      $figure       Optional. Figure (image, etc.) for the helper modal.
 */

function fictioneer_settings_label_checkbox( $option, $label, $description = null, $help = null, $figure = '' ) {
  // Setup
  if ( is_string( $help ) ) {
    $help = strpos( $help, '<p>' ) !== false ? $help : "<p>{$help}</p>";
  }

  $help = ! is_string( $help ) ? '' :
    '<i class="fa-regular fa-circle-question fcn-help" data-action="fcn-show-help" data-label="' . esc_attr( $label ) . '" data-help="' . esc_attr( $figure ) . esc_attr( $help ) . '" data-fcn-dialog-target="fcn-help-modal"></i>';

  // Start HTML ---> ?>
  <label class="fictioneer-label-checkbox" for="<?php echo $option; ?>">
    <input name="<?php echo $option; ?>" type="checkbox" id="<?php echo $option; ?>" <?php echo checked( 1, get_option( $option ), false ); ?> value="1" autocomplete="off">
    <div>
      <span><?php echo "{$label} {$help}"; ?></span>
      <?php if ( ! empty( $description ) ) : ?>
        <p class="fictioneer-sub-label"><?php echo $description; ?></p>
      <?php endif; ?>
    </div>
  </label>
  <?php // <--- End HTML
}

/**
 * Renders a label-wrapped setting text field
 *
 * @since 5.7.2
 *
 * @param string $option       The name of the setting option.
 * @param string $label        Label of the setting.
 * @param string $placeholder  Optional. Placeholder of the input, default empty.
 * @param string $type         Optional. The field type, default 'text'.
 */

function fictioneer_settings_text_input( $option, $label, $placeholder = '', $type = 'text' ) {
  // Start HTML ---> ?>
  <label class="fictioneer-label-textfield" for="<?php echo $option; ?>">
    <input name="<?php echo $option; ?>" placeholder="<?php echo $placeholder; ?>" type="<?php echo $type; ?>" id="<?php echo $option; ?>" value="<?php echo esc_attr( get_option( $option ) ); ?>" autocomplete="off">
    <p class="fictioneer-sub-label"><?php echo $label; ?></p>
  </label>
  <?php // <--- End HTML
}

/**
 * Renders a label-wrapped setting text field for comma-separated values
 *
 * @since 5.15.0
 *
 * @param string $option       The name of the setting option.
 * @param string $label        Label of the setting.
 * @param string $placeholder  Optional. Placeholder of the input, default empty.
 */

function fictioneer_settings_array_input( $option, $label, $placeholder = '' ) {
  // Setup
  $value = get_option( $option, [] ) ?: [];
  $value = is_array( $value ) ? $value : [];
  $value = implode( ', ', $value );

  // Start HTML ---> ?>
  <label class="fictioneer-label-textfield" for="<?php echo $option; ?>">
    <input name="<?php echo $option; ?>" placeholder="<?php echo $placeholder; ?>" type="text" id="<?php echo $option; ?>" value="<?php echo esc_attr( $value ); ?>" autocomplete="off">
    <p class="fictioneer-sub-label"><?php echo $label; ?></p>
  </label>
  <?php // <--- End HTML
}

/**
 * Renders a setting textarea
 *
 * @since 5.7.2
 *
 * @param string $option  The name of the setting option.
 * @param string $label   Label of the setting.
 * @param string $height  Optional. The height with unit, default 'auto'.
 */

function fictioneer_settings_textarea( $option, $label, $height = 'auto' ) {
  // Start HTML ---> ?>
  <textarea class="fictioneer-textarea" name="<?php echo $option; ?>" id="<?php echo $option; ?>" rows="4" style="height: <?php echo $height; ?>;"><?php echo get_option( $option ); ?></textarea>
  <p class="fictioneer-sub-label"><?php echo $label; ?></p>
  <?php // <--- End HTML
}

/**
 * Renders a setting page assignment select field
 *
 * @since 5.7.2
 *
 * @param string $option  The name of the setting option.
 * @param string $label   Label of the setting.
 */

function fictioneer_settings_page_assignment( $option, $label ) {
  wp_dropdown_pages(
    array(
      'name' => $option,
      'id' => $option,
      'option_none_value' => _x( 'None', 'No selection page assignment.', 'fictioneer' ),
      'show_option_no_change' => _x( 'None', 'No selection page assignment.', 'fictioneer' ),
      'selected' => get_option( $option )
    )
  );

  echo
    '<p class="fictioneer-sub-label">',
    $label,
    '</p>';
}

/**
 * Renders a label-wrapped setting toggle checkbox
 *
 * @since 5.21.0
 *
 * @param string $option  The name of the setting option.
 */

function fictioneer_settings_toggle( $option ) {
  // Start HTML ---> ?>
  <label class="checkbox-toggle" for="<?php echo $option; ?>">
    <input type="hidden" name="<?php echo $option; ?>" value="0">
    <input type="checkbox" id="<?php echo $option; ?>" name="<?php echo $option; ?>" value="1" autocomplete="off" <?php echo checked( 1, get_option( $option ), false ); ?>>
  </label>
  <?php // <--- End HTML
}

/**
 * Renders a card for the after-install setup
 *
 * @since 5.21.0
 *
 * @param string $option   The name of the setting option.
 * @param string $title    The card title
 * @param string $content  The card content.
 */

function fictioneer_settings_setup_card( $option, $title, $content ) {
  // Start HTML ---> ?>
  <div class="fictioneer-card">
    <div class="fictioneer-card__wrapper">
      <div class="fictioneer-card__content">
        <div class="fictioneer-card__row">
          <p class="fictioneer-card__row-heading"><?php echo $title; ?></p>
          <p><?php echo $content; ?></p>
        </div>
        <div class="fictioneer-card__row">
          <?php fictioneer_settings_toggle( $option ); ?>
        </div>
      </div>
    </div>
  </div>
  <?php // <--- End HTML
}
