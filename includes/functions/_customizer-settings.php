<?php

// =============================================================================
// WATCH FOR CUSTOMIZER UPDATES
// =============================================================================

/**
 * Watches for customizer updates to purge Transients and files
 *
 * @since 4.7.0
 * @since 5.10.1 - Extend cache purging.
 * @since 5.11.0 - Purge customize.css file
 */

function fictioneer_watch_for_customizer_updates() {
  // Transients
  fictioneer_delete_layout_transients();
  fictioneer_delete_transients_like( 'fictioneer_', true ); // Fast

  // Object cache
  wp_cache_flush();

  // Rebuild customize stylesheet
  fictioneer_build_customize_css();

  // Rebuild dynamic scripts
  fictioneer_build_dynamic_scripts();

  // Clear cached HTML
  fictioneer_clear_all_cached_partials();

  // Files
  $bundled_fonts = fictioneer_get_theme_cache_dir( 'watch_for_customizer_updates' ) . '/bundled-fonts.css';

  if ( file_exists( $bundled_fonts ) ) {
    unlink( $bundled_fonts );
  }
}
add_action( 'customize_save_after', 'fictioneer_watch_for_customizer_updates' );

// =============================================================================
// ADD CUSTOMIZER RANGE VALUE CONTROL
// =============================================================================

/**
 * Check for WP_Customizer_Control existence before adding custom control because
 * WP_Customize_Control is loaded on customizer page only.
 */

if ( class_exists( 'WP_Customize_Control' ) ) {
  require_once( get_template_directory() . '/includes/classes/class-customize-range-control.php' );
}

// =============================================================================
// COLOR SETTING HELPER
// =============================================================================

/**
 * Helper to add color theme option
 *
 * @since 5.12.0
 * @since 5.21.2 - Improved with theme colors helper function.
 *
 * @param WP_Customize_Manager $manager  The customizer instance.
 * @param array                $args     Arguments for the setting and controls.
 */

function fictioneer_add_color_theme_option( $manager, $args ) {
  $fictioneer_colors = fictioneer_get_theme_colors_array();
  $default = $args['default'] ?? $fictioneer_colors[ $args['setting'] ]['hex'] ?? '';

  $manager->add_setting(
    $args['setting'],
    array(
      'capability' => $args['capability'] ?? 'manage_options',
      'sanitize_callback' => $args['sanitize_callback'] ?? 'sanitize_hex_color',
      'default' => $default
    )
  );

  $control_args = array(
    'label' => $args['label'],
    'section' => $args['section'],
    'settings' => $args['setting'],
    'priority' => $args['priority'] ?? 10
  );

  if ( $args['description'] ?? 0 ) {
    $control_args['description'] = '<small>' . $args['description'] . '</small>';
  }

  $manager->add_control(
    new WP_Customize_Color_Control( $manager, $args['setting'], $control_args )
  );
}

// =============================================================================
// RESETS
// =============================================================================

/**
 * Add section with reset options
 *
 * @since 5.12.0
 *
 * @param WP_Customize_Manager $manager  The customizer instance.
 */

function fictioneer_add_reset_options( $manager ) {
  $manager->add_section(
    'reset',
    array(
      'title' => __( 'Reset Options', 'fictioneer' ),
      'description' => __( 'This allows you to quickly reset options to their default. However, this is irreversible! Save changes before doing this since the Customizer needs to be reloaded.', 'fictioneer' ),
      'priority' => 160,
    )
  );

  $manager->add_control(
    'reset_all_colors',
    array(
      'type' => 'button',
      'settings' => [],
      'priority' => 10,
      'section' => 'reset',
      'input_attrs' => array(
        'value' => __( 'Reset Colors', 'fictioneer' ),
        'class' => 'button button-primary'
      )
    )
  );
}

// =============================================================================
// LIGHT MODE SETTINGS
// =============================================================================

/**
 * Add light mode customizer settings
 *
 * @since 4.7.0
 *
 * @param WP_Customize_Manager $manager  The customizer instance.
 */

function fictioneer_add_light_mode_customizer_settings( $manager ) {
  $manager->add_section(
    'light_mode_colors',
    array(
      'title' => __( 'Light Mode Colors', 'fictioneer' ),
      'description' => __( 'Color scheme for the light mode. If you want to add custom CSS, this needs to be under <code>:root[data-mode="light"]</code>.', 'fictioneer' ),
      'priority' => '83'
    )
  );

  // Hue offset
  $manager->add_setting(
    'hue_offset_light',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint',
      'default' => 0
    )
  );

  $manager->add_control(
    new Fictioneer_Customize_Range_Control(
      $manager,
      'hue_offset_light',
      array(
        'type' => 'fictioneer-range',
        'priority' => 1,
        'section' => 'light_mode_colors',
        'settings' => 'hue_offset_light',
        'label' => __( 'Hue Offset', 'fictioneer' ),
        'description' => '<small>' . __( 'Adds an offset to the hue rotate in degrees, tinting the whole site. Default 0.', 'fictioneer' ) . '</small>',
        'input_attrs' => array(
          'min' => 0,
          'max' => 360,
          'step' => 1
        )
      )
    )
  );

  // Saturation offset
  $manager->add_setting(
    'saturation_offset_light',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'fictioneer_sanitize_integer',
      'default' => 0
    )
  );

  $manager->add_control(
    new Fictioneer_Customize_Range_Control(
      $manager,
      'saturation_offset_light',
      array(
        'type' => 'range-value',
        'priority' => 2,
        'section' => 'light_mode_colors',
        'settings' => 'saturation_offset_light',
        'label' => __( 'Saturation Offset', 'fictioneer' ),
        'description' => '<small>' . __( 'Adds an offset to the saturation multiplier, affecting the whole site. Default 0.', 'fictioneer' ) . '</small>',
        'input_attrs' => array(
          'min' => -100,
          'max' => 100,
          'step' => 1
        )
      )
    )
  );

  // Lightness offset
  $manager->add_setting(
    'lightness_offset_light',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'fictioneer_sanitize_integer',
      'default' => 0
    )
  );

  $manager->add_control(
    new Fictioneer_Customize_Range_Control(
      $manager,
      'lightness_offset_light',
      array(
        'type' => 'range-value',
        'priority' => 3,
        'section' => 'light_mode_colors',
        'settings' => 'lightness_offset_light',
        'label' => __( 'Lightness Offset', 'fictioneer' ),
        'description' => '<small>' . __( 'Adds an offset to the lightness multiplier, affecting the whole site. Default 0.', 'fictioneer' ) . '</small>',
        'input_attrs' => array(
          'min' => -100,
          'max' => 100,
          'step' => 1
        )
      )
    )
  );

  // Font saturation offset
  $manager->add_setting(
    'font_saturation_offset_light',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'fictioneer_sanitize_integer',
      'default' => 0
    )
  );

  $manager->add_control(
    new Fictioneer_Customize_Range_Control(
      $manager,
      'font_saturation_offset_light',
      array(
        'type' => 'range-value',
        'priority' => 4,
        'section' => 'light_mode_colors',
        'settings' => 'font_saturation_offset_light',
        'label' => __( 'Font Saturation Offset', 'fictioneer' ),
        'description' => '<small>' . __( 'Adds an offset to the font saturation multiplier, affecting the whole site. Default 0.', 'fictioneer' ) . '</small>',
        'input_attrs' => array(
          'min' => -100,
          'max' => 100,
          'step' => 1
        )
      )
    )
  );

  // Font lightness offset
  $manager->add_setting(
    'font_lightness_offset_light',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'fictioneer_sanitize_integer',
      'default' => 0
    )
  );

  $manager->add_control(
    new Fictioneer_Customize_Range_Control(
      $manager,
      'font_lightness_offset_light',
      array(
        'type' => 'range-value',
        'priority' => 5,
        'section' => 'light_mode_colors',
        'settings' => 'font_lightness_offset_light',
        'label' => __( 'Font Lightness Offset', 'fictioneer' ),
        'description' => '<small>' . __( 'Adds a fractional offset to the font lightness multiplier, affecting the whole site. Default 0.', 'fictioneer' ) . '</small>',
        'input_attrs' => array(
          'min' => -40,
          'max' => 40,
          'step' => 0.5
        )
      )
    )
  );

  // Header title color
  fictioneer_add_color_theme_option(
    $manager,
    array(
      'section' => 'light_mode_colors',
      'setting' => 'light_header_title_color',
      'label' => __( 'Light Header Title', 'fictioneer' ),
      'priority' => 6
    )
  );

  // Header tagline color
  fictioneer_add_color_theme_option(
    $manager,
    array(
      'section' => 'light_mode_colors',
      'setting' => 'light_header_tagline_color',
      'label' => __( 'Light Header Tagline', 'fictioneer' ),
      'priority' => 7
    )
  );

  $manager->add_setting(
    'use_custom_light_mode',
    array(
      'capability' => 'edit_theme_options',
      'default'=> ''
    )
  );

  $color_priority_start = 20;

  $manager->add_control(
    'use_custom_light_mode',
    array(
      'type' => 'checkbox',
      'priority' => $color_priority_start++,
      'section' => 'light_mode_colors',
      'label' => __( 'Use custom light mode colors', 'fictioneer' )
    )
  );

  // Optional colors
  $colors = array(
    'light_bg_50' => array(
      'label' => __( 'Light Background 50', 'fictioneer' ),
      'description' => __( 'Used for cards, modals, navigation, the LitRPG container, and other high elevations.', 'fictioneer' )
    ),
    'light_bg_100' => array(
      'label' => __( 'Light Background 100', 'fictioneer' ),
      'description' => __( 'Used for the page, slide-in mobile menu, password cutouts and submit label, and read ribbon.', 'fictioneer' )
    ),
    'light_bg_200' => array(
      'label' => __( 'Light Background 200', 'fictioneer' ),
      'description' => __( 'Used for the site.', 'fictioneer' )
    ),
    'light_bg_300' => array(
      'label' => __( 'Light Background 300', 'fictioneer' ),
      'description' => __( 'Used for tabs, pagination numbers, navigation item hovers, navigation submenus, overlays, secondary button hovers, &ltkbd> elements, card frame borders, and barber pole animations.', 'fictioneer' )
    ),
    'light_bg_400' => array(
      'label' => __( 'Light Background 400', 'fictioneer' ),
      'description' => __( 'Used for the &ltbody>, unset checkmarks, tab hovers, tags, loading shapes, disabled primary buttons, any secondary button borders, secondary and warning tag borders, navigation subitem hovers, pagination number hovers, and the default mobile menu.', 'fictioneer' )
    ),
    'light_bg_500' => array(
      'label' => __( 'Light Background 500', 'fictioneer' ),
      'description' => __( 'Used for navigation subitem dividers and secondary button border hovers.', 'fictioneer' )
    ),
    'light_bg_600' => array(
      'label' => __( 'Light Background 600', 'fictioneer' ),
      'description' => __( 'Used for primary buttons, mobile menu buttons, file and suggestion buttons, checked formatting toggles, tag hovers, secondary tag hovers, and input tokens.', 'fictioneer' )
    ),
    'light_bg_700' => array(
      'label' => __( 'Light Background 700', 'fictioneer' ),
      'description' => __( 'Used for active primary buttons and tabs, formatting toggle hovers, suggestion and file button hovers, mobile menu button hovers, current pagination number, and popup menu hovers/selected.', 'fictioneer' )
    ),
    'light_bg_800' => array(
      'label' => __( 'Light Background 800', 'fictioneer' ),
      'description' => __( 'Used for popup menus, chapter micro menu, spoiler codes, tooltips, default notices, chapter progress bar, and input components.', 'fictioneer' )
    ),
    'light_bg_900' => array(
      'label' => __( 'Light Background 900', 'fictioneer' ),
      'description' => __( 'Used for the isolated footer, TTS interface, image placeholders, and consent banner.', 'fictioneer' )
    ),
    'light_bg_950' => array(
      'label' => __( 'Light Background 950', 'fictioneer' ),
      'description' => __( 'Used for content lists and gradients, info boxes, overlays, stripes, borders, inputs, comments, and selection highlights with varying opacity (3-40%).', 'fictioneer' )
    ),
    'light_fg_100' => array(
      'label' => __( 'Light Foreground 100', 'fictioneer' ),
      'description' => __( 'Highest text color for contrast and hovers.', 'fictioneer' )
    ),
    'light_fg_200' => array(
      'label' => __( 'Light Foreground 200', 'fictioneer' ),
      'description' => __( 'Higher text color for contrast and hovers.', 'fictioneer' )
    ),
    'light_fg_300' => array(
      'label' => __( 'Light Foreground 300', 'fictioneer' ),
      'description' => __( 'High text color for contrast and hovers.', 'fictioneer' )
    ),
    'light_fg_400' => array(
      'label' => __( 'Light Foreground 400', 'fictioneer' ),
      'description' => __( 'Primary heavy text color for page content.', 'fictioneer' )
    ),
    'light_fg_500' => array(
      'label' => __( 'Light Foreground 500', 'fictioneer' ),
      'description' => __( 'Primary base text color for page content.', 'fictioneer' )
    ),
    'light_fg_600' => array(
      'label' => __( 'Light Foreground 600', 'fictioneer' ),
      'description' => __( 'Primary faded text color for page content.', 'fictioneer' )
    ),
    'light_fg_700' => array(
      'label' => __( 'Light Foreground 700', 'fictioneer' ),
      'description' => __( 'Secondary text color for complementary items.', 'fictioneer' )
    ),
    'light_fg_800' => array(
      'label' => __( 'Light Foreground 800', 'fictioneer' ),
      'description' => __( 'Tertiary text color for related complementary items.', 'fictioneer' )
    ),
    'light_fg_900' => array(
      'label' => __( 'Light Foreground 900', 'fictioneer' ),
      'description' => __( 'Faded color for background, decorative, and disabled items.', 'fictioneer' )
    ),
    'light_fg_950' => array(
      'label' => __( 'Light Foreground 950', 'fictioneer' ),
      'description' => __( 'Strongly faded color for background, decorative, and disabled items.', 'fictioneer' )
    ),
    'light_fg_tinted' => array(
      'label' => __( 'Light Foreground Tinted', 'fictioneer' ),
      'description' => __( 'Default color in chapters; slightly lighter and more saturated than the base text color.', 'fictioneer' )
    ),
    'light_fg_inverted' => array(
      'label' => __( 'Light Foreground Inverted', 'fictioneer' ),
      'description' => __( 'Light text color for dark backgrounds. Default contrast ~17.3:1 on BG-900.', 'fictioneer' )
    ),
    'light_card_frame' => array(
      'label' => __( 'Light Card Frame', 'fictioneer' ),
      'description' => __( 'Color for some card frame borders and divider lines. Based on BG-300 by default.', 'fictioneer' )
    ),
    'light_theme_color_base' => array(
      'label' => __( 'Light Theme Color Meta', 'fictioneer' ),
      'description' => __( 'Used by some browsers for the interface background color. Based on BG-300 by default.', 'fictioneer' )
    ),
    'light_navigation_background_sticky' => array(
      'label' => __( 'Light Navigation Background (Sticky)', 'fictioneer' ),
      'description' => __( 'Background color for the navigation bar. Based on BG-50 by default.', 'fictioneer' )
    ),
    'light_primary_400' => array(
      'label' => __( 'Light Primary 400', 'fictioneer' ),
      'description' => __( 'Lighter primary accent and content link hover color.', 'fictioneer' )
    ),
    'light_primary_500' => array(
      'label' => __( 'Light Primary 500', 'fictioneer' ),
      'description' => __( 'Base primary accent and content link color.', 'fictioneer' )
    ),
    'light_primary_600' => array(
      'label' => __( 'Light Primary 600', 'fictioneer' ),
      'description' => __( 'Darker primary accent and content link visited color.', 'fictioneer' )
    ),
    'light_red_400' => array(
      'label' => __( 'Light Red 400', 'fictioneer' ),
      'description' => __( 'Lighter alert and warning color.', 'fictioneer' )
    ),
    'light_red_500' => array(
      'label' => __( 'Light Red 500', 'fictioneer' ),
      'description' => __( 'Base alert and warning color.', 'fictioneer' )
    ),
    'light_red_600' => array(
      'label' => __( 'Light Red 600', 'fictioneer' ),
      'description' => __( 'Darker alert and warning color.', 'fictioneer' )
    ),
    'light_green_400' => array(
      'label' => __( 'Light Green 400', 'fictioneer' ),
      'description' => __( 'Lighter success and confirmation color.', 'fictioneer' )
    ),
    'light_green_500' => array(
      'label' => __( 'Light Green 500', 'fictioneer' ),
      'description' => __( 'Base success and confirmation color.', 'fictioneer' )
    ),
    'light_green_600' => array(
      'label' => __( 'Light Green 600', 'fictioneer' ),
      'description' => __( 'Darker success and confirmation color.', 'fictioneer' )
    ),
    'light_bookmark_line_color' => array(
      'label' => __( 'Light Bookmark Line', 'fictioneer' ),
      'description' => __( 'Color of the "alpha" bookmark line and button. Based on Primary-500 by default.', 'fictioneer' )
    ),
    'light_bookmark_color_alpha' => array(
      'label' => __( 'Light Bookmark Alpha', 'fictioneer' ),
      'description' => __( 'Multi-purpose color of the "alpha" bookmark.', 'fictioneer' )
    ),
    'light_bookmark_color_beta' => array(
      'label' => __( 'Light Bookmark Beta', 'fictioneer' ),
      'description' => __( 'Multi-purpose color of the "beta" bookmark.', 'fictioneer' )
    ),
    'light_bookmark_color_gamma' => array(
      'label' => __( 'Light Bookmark Gamma', 'fictioneer' ),
      'description' => __( 'Multi-purpose color of the "gamma" bookmark.', 'fictioneer' )
    ),
    'light_bookmark_color_delta' => array(
      'label' => __( 'Light Bookmark Delta', 'fictioneer' ),
      'description' => __( 'Multi-purpose color of the "delta" bookmark.', 'fictioneer' )
    ),
    'light_ins_background' => array(
      'label' => __( 'Light &ltins>', 'fictioneer' ),
      'description' => __( 'Used for the "inserted" highlight in suggestions.', 'fictioneer' )
    ),
    'light_del_background' => array(
      'label' => __( 'Light &ltdel>', 'fictioneer' ),
      'description' => __( 'Used for the "deleted" highlight in suggestions.', 'fictioneer' )
    ),
    'light_badge_generic_background' => array(
      'label' => __( 'Light Generic Badge', 'fictioneer' ),
      'description' => __( 'Background color of the generic comment badge.', 'fictioneer' )
    ),
    'light_badge_admin_background' => array(
      'label' => __( 'Light Admin Badge', 'fictioneer' ),
      'description' => __( 'Background color of the admin comment badge.', 'fictioneer' )
    ),
    'light_badge_moderator_background' => array(
      'label' => __( 'Light Moderator Badge', 'fictioneer' ),
      'description' => __( 'Background color of the moderator comment badge.', 'fictioneer' )
    ),
    'light_badge_author_background' => array(
      'label' => __( 'Light Author Badge', 'fictioneer' ),
      'description' => __( 'Background color of the author comment badge.', 'fictioneer' )
    ),
    'light_badge_supporter_background' => array(
      'label' => __( 'Light Supporter Badge', 'fictioneer' ),
      'description' => __( 'Background color of the supporter comment badge.', 'fictioneer' )
    ),
    'light_badge_override_background' => array(
      'label' => __( 'Light Override Badge', 'fictioneer' ),
      'description' => __( 'Background color of the override comment badge.', 'fictioneer' )
    )
  );

  $color_priority_start = 20;

  foreach ( $colors as $key => $color ) {
    fictioneer_add_color_theme_option(
      $manager,
      array(
        'section' => 'light_mode_colors',
        'setting' => $key,
        'label' => $color['label'],
        'description' => $color['description'],
        'priority' => $color_priority_start++
      )
    );
  }
}

// =============================================================================
// DARK MODE SETTINGS
// =============================================================================

/**
 * Add dark mode customizer settings
 *
 * @since 4.7.0
 *
 * @param WP_Customize_Manager $manager  The customizer instance.
 */

function fictioneer_add_dark_mode_customizer_settings( $manager ) {
  $manager->add_section(
    'dark_mode_colors',
    array(
      'title' => __( 'Dark Mode Colors', 'fictioneer' ),
      'description' => __( 'Color scheme for the dark mode. <b>Warning:</b> Looking at light text on dark background for a prolonged period of time causes immense eye strain! Try to keep the main text contrast between 5:1 and 7:1 to alleviate the issue.', 'fictioneer' ),
      'priority' => '84'
    )
  );

  // Hue offset
  $manager->add_setting(
    'hue_offset',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint',
      'default' => 0
    )
  );

  $manager->add_control(
    new Fictioneer_Customize_Range_Control(
      $manager,
      'hue_offset',
      array(
        'type' => 'range-value',
        'priority' => 1,
        'section' => 'dark_mode_colors',
        'settings' => 'hue_offset',
        'label' => __( 'Hue Offset', 'fictioneer' ),
        'description' => '<small>' . __( 'Adds an offset to the hue rotate, tinting the whole site. Default 0.', 'fictioneer' ) . '</small>',
        'input_attrs' => array(
          'min' => 0,
          'max' => 360,
          'step' => 1
        )
      )
    )
  );

  // Saturation offset
  $manager->add_setting(
    'saturation_offset',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'fictioneer_sanitize_integer',
      'default' => 0
    )
  );

  $manager->add_control(
    new Fictioneer_Customize_Range_Control(
      $manager,
      'saturation_offset',
      array(
        'type' => 'range-value',
        'priority' => 2,
        'section' => 'dark_mode_colors',
        'settings' => 'saturation_offset',
        'label' => __( 'Saturation Offset', 'fictioneer' ),
        'description' => '<small>' . __( 'Adds an offset to the saturation multiplier, affecting the whole site. Default 0.', 'fictioneer' ) . '</small>',
        'input_attrs' => array(
          'min' => -100,
          'max' => 100,
          'step' => 1
        )
      )
    )
  );

  // Lightness offset
  $manager->add_setting(
    'lightness_offset',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'fictioneer_sanitize_integer',
      'default' => 0
    )
  );

  $manager->add_control(
    new Fictioneer_Customize_Range_Control(
      $manager,
      'lightness_offset',
      array(
        'type' => 'range-value',
        'priority' => 3,
        'section' => 'dark_mode_colors',
        'settings' => 'lightness_offset',
        'label' => __( 'Lightness Offset', 'fictioneer' ),
        'description' => '<small>' . __( 'Adds an offset to the lightness multiplier, affecting the whole site. Default 0.', 'fictioneer' ) . '</small>',
        'input_attrs' => array(
          'min' => -100,
          'max' => 100,
          'step' => 1
        )
      )
    )
  );

  // Font saturation offset
  $manager->add_setting(
    'font_saturation_offset',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'fictioneer_sanitize_integer',
      'default' => 0
    )
  );

  $manager->add_control(
    new Fictioneer_Customize_Range_Control(
      $manager,
      'font_saturation_offset',
      array(
        'type' => 'range-value',
        'priority' => 4,
        'section' => 'dark_mode_colors',
        'settings' => 'font_saturation_offset',
        'label' => __( 'Font Saturation Offset', 'fictioneer' ),
        'description' => '<small>' . __( 'Adds an offset to the font saturation multiplier, affecting the whole site. Default 0.', 'fictioneer' ) . '</small>',
        'input_attrs' => array(
          'min' => -100,
          'max' => 100,
          'step' => 1
        )
      )
    )
  );

  // Font lightness offset
  $manager->add_setting(
    'font_lightness_offset',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'fictioneer_sanitize_integer',
      'default' => 0
    )
  );

  $manager->add_control(
    new Fictioneer_Customize_Range_Control(
      $manager,
      'font_lightness_offset',
      array(
        'type' => 'range-value',
        'priority' => 5,
        'section' => 'dark_mode_colors',
        'settings' => 'font_lightness_offset',
        'label' => __( 'Font Lightness Offset', 'fictioneer' ),
        'description' => '<small>' . __( 'Adds an offset to the font lightness multiplier, affecting the whole site. Default 0.', 'fictioneer' ) . '</small>',
        'input_attrs' => array(
          'min' => -40,
          'max' => 40,
          'step' => 0.5
        )
      )
    )
  );

  // Header title color
  fictioneer_add_color_theme_option(
    $manager,
    array(
      'section' => 'dark_mode_colors',
      'setting' => 'dark_header_title_color',
      'label' => __( 'Dark Header Title', 'fictioneer' ),
      'priority' => 6
    )
  );

  // Header tagline color
  fictioneer_add_color_theme_option(
    $manager,
    array(
      'section' => 'dark_mode_colors',
      'setting' => 'dark_header_tagline_color',
      'label' => __( 'Dark Header Tagline', 'fictioneer' ),
      'priority' => 7
    )
  );

  $color_priority_start = 20;

  // Toggle use of custom dark mode colors
  $manager->add_setting(
    'use_custom_dark_mode',
    array(
      'capability' => 'edit_theme_options',
      'default'=> ''
    )
  );

  $manager->add_control(
    'use_custom_dark_mode',
    array(
      'type' => 'checkbox',
      'priority' => $color_priority_start++,
      'section' => 'dark_mode_colors',
      'label' => __( 'Use custom dark mode colors', 'fictioneer' )
    )
  );

  // Optional colors
  $colors = array(
    'dark_bg_50' => array(
      'label' => __( 'Dark Background 50', 'fictioneer' ),
      'description' => __( 'Used for suggestion and file button hovers, popup menus, tooltips, default notices, formatting toggle hovers, read ribbons, and the LitRPG container.', 'fictioneer' )
    ),
    'dark_bg_100' => array(
      'label' => __( 'Dark Background 100', 'fictioneer' ),
      'description' => __( 'Used for active buttons and tabs, suggestion and file buttons, checked formatting toggles, popup menu hovers/selected, and spoiler codes.', 'fictioneer' )
    ),
    'dark_bg_200' => array(
      'label' => __( 'Dark Background 200', 'fictioneer' ),
      'description' => __( 'Used for the chapter progress fill, dashed horizontal lines, the current pagination number, and secondary button border hovers.', 'fictioneer' )
    ),
    'dark_bg_300' => array(
      'label' => __( 'Dark Background 300', 'fictioneer' ),
      'description' => __( 'Used for loading shapes, solid horizontal lines, navigation subitem hovers, primary button hovers, secondary button borders, mobile menu button hovers, tag hovers, and secondary tag borders.', 'fictioneer' )
    ),
    'dark_bg_400' => array(
      'label' => __( 'Dark Background 400', 'fictioneer' ),
      'description' => __( 'Used for primary buttons, navigation item hovers, navigation submenus, tags, secondary tag hovers, tab hovers, and pagination number hovers.', 'fictioneer' )
    ),
    'dark_bg_500' => array(
      'label' => __( 'Dark Background 500', 'fictioneer' ),
      'description' => __( 'Used for tabs, pagination numbers, disabled primary buttons, barber pole animations, secondary button hovers, mobile menu buttons, password submit label, and input tokens.', 'fictioneer' )
    ),
    'dark_bg_600' => array(
      'label' => __( 'Dark Background 600', 'fictioneer' ),
      'description' => __( 'Used for cards, modals, and other high elevations.', 'fictioneer' )
    ),
    'dark_bg_700' => array(
      'label' => __( 'Dark Background 700', 'fictioneer' ),
      'description' => __( 'Used for the page, slide-in mobile menu, password cutouts, and navigation subitem dividers.', 'fictioneer' )
    ),
    'dark_bg_800' => array(
      'label' => __( 'Dark Background 800', 'fictioneer' ),
      'description' => __( 'Used for the site, card footers, card overlays, and card frame borders.', 'fictioneer' )
    ),
    'dark_bg_900' => array(
      'label' => __( 'Dark Background 900', 'fictioneer' ),
      'description' => __( 'Used for the &ltbody>, unset checkmarks, navigation, isolated footer, chapter micro menu, TTS interface, image placeholders, consent banner, and mixed into the default mobile menu (with 3% white).', 'fictioneer' )
    ),
    'dark_bg_950' => array(
      'label' => __( 'Dark Background 950', 'fictioneer' ),
      'description' => __( 'Used for content lists and gradients with varying opacity (10-16%).', 'fictioneer' )
    ),
    'dark_shade' => array(
      'label' => __( 'Dark Shade', 'fictioneer' ),
      'description' => __( 'Used for semi-transparent overlays with varying opacity, including inputs, info boxes, comments, blockquotes, table stripes, code blocks, &ltkbd> elements, and scroll bars.', 'fictioneer' )
    ),
    'dark_fg_100' => array(
      'label' => __( 'Dark Foreground 100', 'fictioneer' ),
      'description' => __( 'Highest text color for contrast and hovers. Default contrast ~11:1 on BG-700.', 'fictioneer' )
    ),
    'dark_fg_200' => array(
      'label' => __( 'Dark Foreground 200', 'fictioneer' ),
      'description' => __( 'Higher text color for contrast and hovers. Default contrast ~10:1 on BG-700.', 'fictioneer' )
    ),
    'dark_fg_300' => array(
      'label' => __( 'Dark Foreground 300', 'fictioneer' ),
      'description' => __( 'High text color for contrast and hovers. Default contrast ~9:1 on BG-700.', 'fictioneer' )
    ),
    'dark_fg_400' => array(
      'label' => __( 'Dark Foreground 400', 'fictioneer' ),
      'description' => __( 'Primary heavy text color for page content. Default contrast ~8:1 on BG-700.', 'fictioneer' )
    ),
    'dark_fg_500' => array(
      'label' => __( 'Dark Foreground 500', 'fictioneer' ),
      'description' => __( 'Primary base text color for page content. Default contrast ~7:3 on BG-700.', 'fictioneer' )
    ),
    'dark_fg_600' => array(
      'label' => __( 'Dark Foreground 600', 'fictioneer' ),
      'description' => __( 'Primary faded text color for page content. Default contrast ~6:1 on BG-700.', 'fictioneer' )
    ),
    'dark_fg_700' => array(
      'label' => __( 'Dark Foreground 700', 'fictioneer' ),
      'description' => __( 'Secondary text color for complementary items. Default contrast ~5:1 on BG-700.', 'fictioneer' )
    ),
    'dark_fg_800' => array(
      'label' => __( 'Dark Foreground 800', 'fictioneer' ),
      'description' => __( 'Tertiary text color for related complementary items. Default contrast ~4.5:1 on BG-700.', 'fictioneer' )
    ),
    'dark_fg_900' => array(
      'label' => __( 'Dark Foreground 900', 'fictioneer' ),
      'description' => __( 'Faded color for background, decorative, and disabled items. Default contrast ~3.5:1 on BG-700.', 'fictioneer' )
    ),
    'dark_fg_950' => array(
      'label' => __( 'Dark Foreground 950', 'fictioneer' ),
      'description' => __( 'Strongly faded color for background, decorative, and disabled items. Default contrast ~3:1 on BG-700.', 'fictioneer' )
    ),
    'dark_fg_tinted' => array(
      'label' => __( 'Dark Foreground Tinted', 'fictioneer' ),
      'description' => __( 'Default color in chapters; slightly darker and more saturated than the base text color to reduce eye strain. Default contrast ~6.9:1 on BG-700.', 'fictioneer' )
    ),
    'dark_fg_inverted' => array(
      'label' => __( 'Dark Foreground Inverted', 'fictioneer' ),
      'description' => __( 'Dark text color for bright backgrounds. Default contrast ~4.5:1 on BG-200.', 'fictioneer' )
    ),
    'dark_card_frame' => array(
      'label' => __( 'Dark Card Frame', 'fictioneer' ),
      'description' => __( 'Color for some card frame borders and divider lines. Based on BG-800 by default.', 'fictioneer' )
    ),
    'dark_theme_color_base' => array(
      'label' => __( 'Dark Theme Color Meta', 'fictioneer' ),
      'description' => __( 'Used by some browsers for the interface background color. Based on BG-800 by default.', 'fictioneer' )
    ),
    'dark_navigation_background_sticky' => array(
      'label' => __( 'Dark Navigation Background (Sticky)', 'fictioneer' ),
      'description' => __( 'Background color for the navigation bar. Based on BG-900 by default.', 'fictioneer' )
    ),
    'dark_primary_400' => array(
      'label' => __( 'Dark Primary 400', 'fictioneer' ),
      'description' => __( 'Lighter primary accent and content link hover color.', 'fictioneer' )
    ),
    'dark_primary_500' => array(
      'label' => __( 'Dark Primary 500', 'fictioneer' ),
      'description' => __( 'Base primary accent and content link color.', 'fictioneer' )
    ),
    'dark_primary_600' => array(
      'label' => __( 'Dark Primary 600', 'fictioneer' ),
      'description' => __( 'Darker primary accent and content link visited color.', 'fictioneer' )
    ),
    'dark_red_400' => array(
      'label' => __( 'Dark Red 400', 'fictioneer' ),
      'description' => __( 'Lighter alert and warning color.', 'fictioneer' )
    ),
    'dark_red_500' => array(
      'label' => __( 'Dark Red 500', 'fictioneer' ),
      'description' => __( 'Base alert and warning color.', 'fictioneer' )
    ),
    'dark_red_600' => array(
      'label' => __( 'Dark Red 600', 'fictioneer' ),
      'description' => __( 'Darker alert and warning color.', 'fictioneer' )
    ),
    'dark_green_400' => array(
      'label' => __( 'Dark Green 400', 'fictioneer' ),
      'description' => __( 'Lighter success and confirmation color.', 'fictioneer' )
    ),
    'dark_green_500' => array(
      'label' => __( 'Dark Green 500', 'fictioneer' ),
      'description' => __( 'Base success and confirmation color.', 'fictioneer' )
    ),
    'dark_green_600' => array(
      'label' => __( 'Dark Green 600', 'fictioneer' ),
      'description' => __( 'Darker success and confirmation color.', 'fictioneer' )
    ),
    'dark_bookmark_line_color' => array(
      'label' => __( 'Dark Bookmark Line', 'fictioneer' ),
      'description' => __( 'Color of the "alpha" bookmark line and button. Based on Primary-500 by default.', 'fictioneer' )
    ),
    'dark_bookmark_color_alpha' => array(
      'label' => __( 'Dark Bookmark Alpha', 'fictioneer' ),
      'description' => __( 'Multi-purpose color of the "alpha" bookmark.', 'fictioneer' )
    ),
    'dark_bookmark_color_beta' => array(
      'label' => __( 'Dark Bookmark Beta', 'fictioneer' ),
      'description' => __( 'Multi-purpose color of the "beta" bookmark.', 'fictioneer' )
    ),
    'dark_bookmark_color_gamma' => array(
      'label' => __( 'Dark Bookmark Gamma', 'fictioneer' ),
      'description' => __( 'Multi-purpose color of the "gamma" bookmark.', 'fictioneer' )
    ),
    'dark_bookmark_color_delta' => array(
      'label' => __( 'Dark Bookmark Delta', 'fictioneer' ),
      'description' => __( 'Multi-purpose color of the "delta" bookmark.', 'fictioneer' )
    ),
    'dark_ins_background' => array(
      'label' => __( 'Dark &ltins>', 'fictioneer' ),
      'description' => __( 'Used for the "inserted" highlight in suggestions.', 'fictioneer' )
    ),
    'dark_del_background' => array(
      'label' => __( 'Dark &ltdel>', 'fictioneer' ),
      'description' => __( 'Used for the "deleted" highlight in suggestions.', 'fictioneer' )
    ),
    'dark_badge_generic_background' => array(
      'label' => __( 'Dark Generic Badge', 'fictioneer' ),
      'description' => __( 'Background color of the generic comment badge.', 'fictioneer' )
    ),
    'dark_badge_admin_background' => array(
      'label' => __( 'Dark Admin Badge', 'fictioneer' ),
      'description' => __( 'Background color of the admin comment badge.', 'fictioneer' )
    ),
    'dark_badge_moderator_background' => array(
      'label' => __( 'Dark Moderator Badge', 'fictioneer' ),
      'description' => __( 'Background color of the moderator comment badge.', 'fictioneer' )
    ),
    'dark_badge_author_background' => array(
      'label' => __( 'Dark Author Badge', 'fictioneer' ),
      'description' => __( 'Background color of the author comment badge.', 'fictioneer' )
    ),
    'dark_badge_supporter_background' => array(
      'label' => __( 'Dark Supporter Badge', 'fictioneer' ),
      'description' => __( 'Background color of the supporter comment badge.', 'fictioneer' )
    ),
    'dark_badge_override_background' => array(
      'label' => __( 'Dark Override Badge', 'fictioneer' ),
      'description' => __( 'Background color of the override comment badge.', 'fictioneer' )
    )
  );

  foreach ( $colors as $key => $color ) {
    fictioneer_add_color_theme_option(
      $manager,
      array(
        'section' => 'dark_mode_colors',
        'setting' => $key,
        'label' => $color['label'],
        'description' => $color['description'],
        'priority' => $color_priority_start++
      )
    );
  }
}

// =============================================================================
// HEADER SETTINGS
// =============================================================================

/**
 * Add header customizer settings
 *
 * @since 5.11.0
 *
 * @param WP_Customize_Manager $manager  The customizer instance.
 */

function fictioneer_add_header_customizer_settings( $manager ) {
  // Logo min height
  $manager->add_setting(
    'logo_min_height',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint',
      'default' => absint( get_theme_mod( 'logo_height', 210 ) )
    )
  );

  $manager->add_control(
    'logo_min_height',
    array(
      'type' => 'number',
      'priority' => 8,
      'section' => 'title_tagline',
      'label' => __( 'Logo Height - Minimum', 'fictioneer' ),
      'description' => __( 'Minimum height of the logo in pixels on 320px viewports, which some header types limit further. Default 210.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => '210',
        'style' => 'width: 80px',
        'min' => 0
      )
    )
  );

  // Logo max height
  $manager->add_setting(
    'logo_height',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint',
      'default' => 210
    )
  );

  $manager->add_control(
    'logo_height',
    array(
      'type' => 'number',
      'priority' => 9,
      'section' => 'title_tagline',
      'label' => __( 'Logo Height - Maximum', 'fictioneer' ),
      'description' => __( 'Maximum height of the logo in pixels on [site-width] viewports, which some header types limit further. Default 210.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => '210',
        'style' => 'width: 80px',
        'min' => 0
      )
    )
  );

  // Clamp minimum for site title font size
  $manager->add_setting(
    'site_title_font_size_min',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint',
      'default' => 32
    )
  );

  $manager->add_control(
    'site_title_font_size_min',
    array(
      'type' => 'number',
      'priority' => 40,
      'section' => 'title_tagline',
      'label' => __( 'Site Title - Minimum Size', 'fictioneer' ),
      'description' => __( 'Minimum font size in pixels of the site title on 320px viewports. Default 32.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => '32',
        'style' => 'width: 80px',
        'min' => 0
      )
    )
  );

  // Clamp maximum for site title font size
  $manager->add_setting(
    'site_title_font_size_max',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint',
      'default' => 60
    )
  );

  $manager->add_control(
    'site_title_font_size_max',
    array(
      'type' => 'number',
      'priority' => 41,
      'section' => 'title_tagline',
      'label' => __( 'Site Title - Maximum Size', 'fictioneer' ),
      'description' => __( 'Maximum font size in pixels of the site title on [site-width] viewports. Default 60.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => '60',
        'style' => 'width: 80px',
        'min' => 0
      )
    )
  );

  // Clamp minimum for tagline font size
  $manager->add_setting(
    'site_tagline_font_size_min',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint',
      'default' => 13
    )
  );

  $manager->add_control(
    'site_tagline_font_size_min',
    array(
      'type' => 'number',
      'priority' => 42,
      'section' => 'title_tagline',
      'label' => __( 'Tagline - Minimum Size', 'fictioneer' ),
      'description' => __( 'Minimum font size in pixels of the tagline on 320px viewports. Default 13.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => '13',
        'style' => 'width: 80px',
        'min' => 0
      )
    )
  );

  // Clamp maximum for tagline font size
  $manager->add_setting(
    'site_tagline_font_size_max',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint',
      'default' => 18
    )
  );

  $manager->add_control(
    'site_tagline_font_size_max',
    array(
      'type' => 'number',
      'priority' => 43,
      'section' => 'title_tagline',
      'label' => __( 'Tagline - Maximum Size', 'fictioneer' ),
      'description' => __( 'Maximum font size in pixels of the tagline on [site-width] viewports. Default 18.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => '18',
        'style' => 'width: 80px',
        'min' => 0
      )
    )
  );

  // Header image style
  $manager->add_setting(
    'header_image_style',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'default'
    )
  );

  $header_image_styles = array(
    'default' => _x( 'Plain (Default)', 'Customizer header image style option.', 'fictioneer' ),
    'polygon-battered' => _x( 'Battered', 'Customizer header image style option.', 'fictioneer' ),
    'polygon-chamfered' => _x( 'Chamfered', 'Customizer header image style option.', 'fictioneer' ),
    'mask-grunge-frame-a-small' => _x( 'Grunge Frame (Small)', 'Customizer header image style option.', 'fictioneer' ),
    'mask-grunge-frame-a-large' => _x( 'Grunge Frame (Large)', 'Customizer header image style option.', 'fictioneer' ),
    'polygon-mask-image-custom-css' => _x( 'Custom CSS', 'Customizer header image style option.', 'fictioneer' )
  );

  $manager->add_control(
    'header_image_style',
    array(
      'type' => 'select',
      'priority' => 20,
      'section' => 'header_image',
      'label' => __( 'Header Image Style', 'fictioneer' ),
      'description' => __( 'Choose the style for your header image.', 'fictioneer' ),
      'choices' => apply_filters( 'fictioneer_filter_customizer_header_image_style', $header_image_styles )
    )
  );

  // Header image shadow
  $manager->add_setting(
    'header_image_shadow',
    array(
      'capability' => 'edit_theme_options',
      'default'=> 1
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'header_image_shadow',
      array(
        'type' => 'checkbox',
        'priority' => 21,
        'label' => __( 'Show header image shadow', 'fictioneer' ),
        'section' => 'header_image',
        'settings' => 'header_image_shadow'
      )
    )
  );

  // Inset header image
  $manager->add_setting(
    'inset_header_image',
    array(
      'capability' => 'edit_theme_options',
      'default'=> ''
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'inset_header_image',
      array(
        'type' => 'checkbox',
        'priority' => 22,
        'label' => __( 'Inset header image', 'fictioneer' ),
        'section' => 'header_image',
        'settings' => 'inset_header_image',
        'description' => __( 'Inset the header image with a limit of 1.5 times the site width. For smaller images that do not work on widescreens.', 'fictioneer' )
      )
    )
  );

  // Header image fading start
  $manager->add_setting(
    'header_image_fading_start',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint',
      'default' => 0
    )
  );

  $manager->add_control(
    'header_image_fading_start',
    array(
      'type' => 'number',
      'priority' => 23,
      'section' => 'header_image',
      'label' => __( 'Fade Out - Start', 'fictioneer' ),
      'description' => __( 'If set to above 0, the header image will fade to transparent, with the value in percent as offset from the top (1-99). Default 0.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => '0',
        'min' => 0,
        'style' => 'width: 80px'
      )
    )
  );

  // Header image fading breakpoint
  $manager->add_setting(
    'header_image_fading_breakpoint',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint',
      'default' => 0
    )
  );

  $manager->add_control(
    'header_image_fading_breakpoint',
    array(
      'type' => 'number',
      'priority' => 24,
      'section' => 'header_image',
      'label' => __( 'Fade Out - Breakpoint', 'fictioneer' ),
      'description' => __( 'If set to above 320, the fade-out will only be applied to viewports equal to or wider than the value in pixels. Default 0.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => '0',
        'min' => 0,
        'style' => 'width: 80px'
      )
    )
  );

  // Header light color
  $manager->add_setting(
    'header_color_light',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => ''
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'header_color_light',
      array(
        'label' => __( 'Header Color - Light Mode', 'fictioneer' ),
        'description' => __( 'Used in light mode if no header image has been selected. Default none.', 'fictioneer' ),
        'section' => 'header_image',
        'settings' => 'header_color_light',
        'priority' => 30
      )
    )
  );

  // Header dark color
  $manager->add_setting(
    'header_color_dark',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => ''
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'header_color_dark',
      array(
        'label' => __( 'Header Color - Dark Mode', 'fictioneer' ),
        'description' => __( 'Used in dark mode if no header image has been selected. Default none.', 'fictioneer' ),
        'section' => 'header_image',
        'settings' => 'header_color_dark',
        'priority' => 31
      )
    )
  );

  // Clamp minimum for header image height
  $manager->add_setting(
    'header_image_height_min',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint',
      'default' => 210
    )
  );

  $manager->add_control(
    'header_image_height_min',
    array(
      'type' => 'number',
      'priority' => 40,
      'section' => 'header_image',
      'label' => __( 'Minimum Height', 'fictioneer' ),
      'description' => __( 'Min. height in pixels of the header image on 320px viewports. Default 210.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => '210',
        'style' => 'width: 80px',
        'min' => 0
      )
    )
  );

  // Clamp maximum for header image height
  $manager->add_setting(
    'header_image_height_max',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint',
      'default' => 480
    )
  );

  $manager->add_control(
    'header_image_height_max',
    array(
      'type' => 'number',
      'priority' => 50,
      'section' => 'header_image',
      'label' => __( 'Maximum Height', 'fictioneer' ),
      'description' => __( 'Max. height in pixels of the header image on [site-width] viewports. Default 480.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => '480',
        'style' => 'width: 80px',
        'min' => 0
      )
    )
  );
}

// =============================================================================
// LAYOUT SETTINGS
// =============================================================================

/**
 * Add layout customizer settings
 *
 * @since 4.7.0
 *
 * @param WP_Customize_Manager $manager  The customizer instance.
 */

function fictioneer_add_layout_customizer_settings( $manager ) {
  // Add layout section
  $manager->add_section(
    'layout',
    array(
      'title' => __( 'Layout', 'fictioneer' ),
      'priority' => '80'
    )
  );

  // Placeholder image
  $manager->add_setting(
    'placeholder_image',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint'
    )
  );

  $manager->add_control(
    new WP_Customize_Cropped_Image_Control(
      $manager,
      'placeholder_image',
      array(
        'label' => __( 'Placeholder Image', 'fictioneer' ),
        'description' => __( 'Fallback if no thumbnail has been provided, for example on vertical cards.', 'fictioneer' ),
        'priority' => 0,
        'section' => 'layout',
        'settings' => 'placeholder_image',
        'flex_width' => true,
        'flex_height' => true,
        'button_labels' => array(
          'select' => __( 'Select Placeholder Image', 'fictioneer' ),
          'remove' => __( 'Remove', 'fictioneer' ),
          'change' => __( 'Change Placeholder Image', 'fictioneer' )
        )
      )
    )
  );

  // Default story cover
  $manager->add_setting(
    'default_story_cover',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint'
    )
  );

  $manager->add_control(
    new WP_Customize_Cropped_Image_Control(
      $manager,
      'default_story_cover',
      array(
        'label' => __( 'Default Story Cover', 'fictioneer' ),
        'description' => __( 'Default cover for stories if none has been provided; inherited by chapters.', 'fictioneer' ),
        'priority' => 1,
        'section' => 'layout',
        'settings' => 'default_story_cover',
        'flex_width' => true,
        'flex_height' => true,
        'button_labels' => array(
          'select' => __( 'Select Default Cover', 'fictioneer' ),
          'remove' => __( 'Remove', 'fictioneer' ),
          'change' => __( 'Change Default Cover', 'fictioneer' )
        )
      )
    )
  );

  // Site width
  $manager->add_setting(
    'site_width',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint',
      'default' => apply_filters( 'fictioneer_filter_customizer_site_width_default', 960 )
    )
  );

  $manager->add_control(
    'site_width',
    array(
      'type' => 'number',
      'priority' => 2,
      'section' => 'layout',
      'label' => __( 'Site Width', 'fictioneer' ),
      'description' => __( 'Maximum site width in pixels, should not be less than 896. Default 960.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => strval( apply_filters( 'fictioneer_filter_customizer_site_width_default', 960 ) ),
        'min' => 896,
        'style' => 'width: 80px'
      )
    )
  );

  // Main offset
  $manager->add_setting(
    'main_offset',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'fictioneer_sanitize_integer',
      'default' => 0
    )
  );

  $manager->add_control(
    'main_offset',
    array(
      'type' => 'number',
      'priority' => 4,
      'section' => 'layout',
      'label' => __( 'Main Offset', 'fictioneer' ),
      'description' => __( 'Add an offset in pixels to the main container, positive or negative. Default 0.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => '0',
        'style' => 'width: 80px'
      )
    )
  );

  // Sidebar style
  $manager->add_setting(
    'sidebar_style',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'none'
    )
  );

  $sidebar_styles = array(
    'none' => _x( 'None (Default)', 'Customizer sidebar style option.', 'fictioneer' ),
    'left' => _x( 'Left', 'Customizer header sidebar option.', 'fictioneer' ),
    'right' => _x( 'Right', 'Customizer header sidebar option.', 'fictioneer' )
  );

  $manager->add_control(
    'sidebar_style',
    array(
      'type' => 'select',
      'priority' => 6,
      'section' => 'layout',
      'label' => __( 'Sidebar', 'fictioneer' ),
      'description' => __( 'Choose whether and where to render the sidebar. You can set it up under Widgets; it will not appear before that.', 'fictioneer' ),
      'choices' => apply_filters( 'fictioneer_filter_customizer_sidebar_style', $sidebar_styles )
    )
  );

  // Disable mobile sidebar
  $manager->add_setting(
    'sidebar_hide_on_mobile',
    array(
      'capability' => 'edit_theme_options',
      'default'=> 0
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'sidebar_hide_on_mobile',
      array(
        'type' => 'checkbox',
        'priority' => 7,
        'label' => __( 'Hide sidebar on mobile', 'fictioneer' ),
        'section' => 'layout',
        'settings' => 'sidebar_hide_on_mobile'
      )
    )
  );

  // Sidebar width
  $manager->add_setting(
    'sidebar_width',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint',
      'default' => 256
    )
  );

  $manager->add_control(
    'sidebar_width',
    array(
      'type' => 'number',
      'priority' => 8,
      'section' => 'layout',
      'label' => __( 'Sidebar Width', 'fictioneer' ),
      'description' => __( 'Width of the sidebar in pixels. Default 256.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => '256',
        'style' => 'width: 80px',
        'min' => 100
      )
    )
  );

  // Sidebar gap
  $manager->add_setting(
    'sidebar_gap',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint',
      'default' => 48
    )
  );

  $manager->add_control(
    'sidebar_gap',
    array(
      'type' => 'number',
      'priority' => 10,
      'section' => 'layout',
      'label' => __( 'Sidebar Gap', 'fictioneer' ),
      'description' => __( 'Gap between sidebar and content in pixels. Default 48.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => '48',
        'style' => 'width: 80px',
        'min' => 0
      )
    )
  );

  // Disable story sidebar
  $manager->add_setting(
    'sidebar_disable_in_stories',
    array(
      'capability' => 'edit_theme_options',
      'default'=> 0
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'sidebar_disable_in_stories',
      array(
        'type' => 'checkbox',
        'priority' => 12,
        'label' => __( 'Disable sidebar in stories', 'fictioneer' ),
        'section' => 'layout',
        'settings' => 'sidebar_disable_in_stories'
      )
    )
  );

  // Disable chapter sidebar
  $manager->add_setting(
    'sidebar_disable_in_chapters',
    array(
      'capability' => 'edit_theme_options',
      'default'=> 0
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'sidebar_disable_in_chapters',
      array(
        'type' => 'checkbox',
        'priority' => 14,
        'label' => __( 'Disable sidebar in chapters', 'fictioneer' ),
        'section' => 'layout',
        'settings' => 'sidebar_disable_in_chapters'
      )
    )
  );

  // Disable collection sidebar
  $manager->add_setting(
    'sidebar_disable_in_collections',
    array(
      'capability' => 'edit_theme_options',
      'default'=> 0
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'sidebar_disable_in_collections',
      array(
        'type' => 'checkbox',
        'priority' => 16,
        'label' => __( 'Disable sidebar in collections', 'fictioneer' ),
        'section' => 'layout',
        'settings' => 'sidebar_disable_in_collections'
      )
    )
  );

  // Disable recommendation sidebar
  $manager->add_setting(
    'sidebar_disable_in_recommendations',
    array(
      'capability' => 'edit_theme_options',
      'default'=> 0
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'sidebar_disable_in_recommendations',
      array(
        'type' => 'checkbox',
        'priority' => 18,
        'label' => __( 'Disable sidebar in recommendations', 'fictioneer' ),
        'section' => 'layout',
        'settings' => 'sidebar_disable_in_recommendations'
      )
    )
  );

  // Header style
  $manager->add_setting(
    'header_style',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'default'
    )
  );

  $header_styles = array(
    'default' => _x( 'Default (Image)', 'Customizer header style option.', 'fictioneer' ),
    'split' => _x( 'Split (Image)', 'Customizer header style option.', 'fictioneer' ),
    'overlay' => _x( 'Overlay (Image)', 'Customizer header style option.', 'fictioneer' ),
    'text_center' => _x( 'Text (Center)', 'Customizer header style option.', 'fictioneer' ),
    'post_content' => _x( 'Post Content (ID)', 'Customizer header style option.', 'fictioneer' ),
    'top' => _x( 'Top', 'Customizer header style option.', 'fictioneer' ),
    'wide' => _x( 'Wide', 'Customizer header style option.', 'fictioneer' ),
    'none' => _x( 'None', 'Customizer header style option.', 'fictioneer' )
  );

  $manager->add_control(
    'header_style',
    array(
      'type' => 'select',
      'priority' => 20,
      'section' => 'layout',
      'label' => __( 'Header Style', 'fictioneer' ),
      'description' => __( 'Choose the style for your header. This can affect or disable other settings.', 'fictioneer' ),
      'choices' => apply_filters( 'fictioneer_filter_customizer_header_style', $header_styles )
    )
  );

  // Title shadow
  $manager->add_setting(
    'title_text_shadow',
    array(
      'capability' => 'edit_theme_options',
      'default'=> 0
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'title_text_shadow',
      array(
        'type' => 'checkbox',
        'priority' => 22,
        'label' => __( 'Show title text shadow', 'fictioneer' ),
        'section' => 'layout',
        'settings' => 'title_text_shadow'
      )
    )
  );

  // Post content header iD
  $manager->add_setting(
    'header_post_content_id',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint',
      'default' => ''
    )
  );

  $manager->add_control(
    'header_post_content_id',
    array(
      'type' => 'number',
      'priority' => 24,
      'section' => 'layout',
      'label' => __( 'Post Content Header ID', 'fictioneer' ),
      'description' => __( 'Only used by the Post Content header style. Enter the ID of the post or page you want to render as header. Can impact performance.', 'fictioneer' ),
      'input_attrs' => array(
        'style' => 'width: 80px',
        'min' => 0
      )
    )
  );

  // Clamp minimum for header height
  $manager->add_setting(
    'header_height_min',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint',
      'default' => 190
    )
  );

  $manager->add_control(
    'header_height_min',
    array(
      'type' => 'number',
      'priority' => 26,
      'section' => 'layout',
      'label' => __( 'Header Height - Minimum', 'fictioneer' ),
      'description' => __( 'Minimum height of the header section in pixels on the smallest viewport. Default 190.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => '190',
        'style' => 'width: 80px',
        'min' => 0
      )
    )
  );

  // Clamp maximum for header height
  $manager->add_setting(
    'header_height_max',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint',
      'default' => 380
    )
  );

  $manager->add_control(
    'header_height_max',
    array(
      'type' => 'number',
      'priority' => 28,
      'section' => 'layout',
      'label' => __( 'Header Height - Maximum', 'fictioneer' ),
      'description' => __( 'Maximum height of the header section in pixels on the largest viewport. Default 380.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => '380',
        'style' => 'width: 80px',
        'min' => 0
      )
    )
  );

  // Mobile navigation style
  $manager->add_setting(
    'mobile_nav_style',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => apply_filters( 'fictioneer_filter_customizer_mobile_nav_style_default', 'overflow' )
    )
  );

  $manager->add_control(
    'mobile_nav_style',
    array(
      'type' => 'select',
      'priority' => 30,
      'section' => 'layout',
      'label' => __( 'Mobile Navigation Style', 'fictioneer' ),
      'description' => __( 'Choose the style for your mobile navigation.', 'fictioneer' ),
      'choices' => array(
        'overflow' => _x( 'Overflow (Default)', 'Customizer mobile navigation style option.', 'fictioneer' ),
        'collapse' => _x( 'Collapse', 'Customizer mobile navigation style option.', 'fictioneer' )
      )
    )
  );

  // Mobile menu style
  $manager->add_setting(
    'mobile_menu_style',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => apply_filters( 'fictioneer_filter_customizer_mobile_menu_style_default', 'minimize_to_right' )
    )
  );

  $manager->add_control(
    'mobile_menu_style',
    array(
      'type' => 'select',
      'priority' => 32,
      'section' => 'layout',
      'label' => __( 'Mobile Menu Style', 'fictioneer' ),
      'description' => __( 'Choose the style for your mobile menu.', 'fictioneer' ),
      'choices' => array(
        'minimize_to_right' => _x( 'Minimize site to right (Default)', 'Customizer mobile menu style option.', 'fictioneer' ),
        'left_slide_in' => _x( 'Slide in from left', 'Customizer mobile menu style option.', 'fictioneer' ),
        'right_slide_in' => _x( 'Slide in from right', 'Customizer mobile menu style option.', 'fictioneer' )
      )
    )
  );

  // Page style
  $manager->add_setting(
    'page_style',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'default'
    )
  );

  $page_styles = array(
    'default' => _x( 'Plain (Default)', 'Customizer page style option.', 'fictioneer' ),
    'polygon-battered' => _x( 'Battered', 'Customizer page style option.', 'fictioneer' ),
    'mask-image-ringbook' => _x( 'Ringbook', 'Customizer page style option.', 'fictioneer' ),
    'polygon-mask-image-battered-ringbook' => _x( 'Battered Ringbook', 'Customizer page style option.', 'fictioneer' ),
    'polygon-chamfered' => _x( 'Chamfered', 'Customizer page style option.', 'fictioneer' ),
    'polygon-interface-a' => _x( 'Interface', 'Customizer page style option.', 'fictioneer' ),
    'mask-image-wave-a' => _x( 'Wave', 'Customizer page style option.', 'fictioneer' ),
    'mask-image-layered-steps-a' => _x( 'Layered Steps', 'Customizer page style option.', 'fictioneer' ),
    'mask-image-layered-peaks-a' => _x( 'Layered Peaks', 'Customizer page style option.', 'fictioneer' ),
    'mask-image-grunge-a' => _x( 'Grunge', 'Customizer page style option.', 'fictioneer' ),
    'polygon-mask-image-custom-css' => _x( 'Custom CSS', 'Customizer page style option.', 'fictioneer' ),
    'none' => _x( 'None (Hide)', 'Customizer page style option.', 'fictioneer' )
  );

  $manager->add_control(
    'page_style',
    array(
      'type' => 'select',
      'priority' => 34,
      'section' => 'layout',
      'label' => __( 'Page Style', 'fictioneer' ),
      'description' => __( 'Choose the style for your pages.', 'fictioneer' ),
      'choices' => apply_filters( 'fictioneer_filter_customizer_page_style', $page_styles )
    )
  );

  // Page shadow
  $manager->add_setting(
    'page_shadow',
    array(
      'capability' => 'edit_theme_options',
      'default'=> 1
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'page_shadow',
      array(
        'type' => 'checkbox',
        'priority' => 36,
        'label' => __( 'Show page shadow', 'fictioneer' ),
        'section' => 'layout',
        'settings' => 'page_shadow'
      )
    )
  );

  // Story cover position
  $manager->add_setting(
    'story_cover_position',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => apply_filters( 'fictioneer_filter_customizer_story_cover_position_default', 'top-left-overflow' )
    )
  );

  $story_cover_positions = array(
    'top-left-overflow' => _x( 'Top-Left Overflow (Default)', 'Customizer story cover position option.', 'fictioneer' ),
    'float-top-left' => _x( 'Floating Top-Left', 'Customizer story cover position option.', 'fictioneer' ),
    'float-top-right' => _x( 'Floating Top-Right', 'Customizer story cover position option.', 'fictioneer' ),
    'float-left' => _x( 'Floating Left', 'Customizer story cover position option.', 'fictioneer' ),
    'float-right' => _x( 'Floating Right', 'Customizer story cover position option.', 'fictioneer' ),
    'hide' => _x( 'Hide', 'Customizer story cover position option.', 'fictioneer' )
  );

  $manager->add_control(
    'story_cover_position',
    array(
      'type' => 'select',
      'priority' => 38,
      'section' => 'layout',
      'label' => __( 'Story Page Cover Position', 'fictioneer' ),
      'description' => __( 'Choose where to display the cover.', 'fictioneer' ),
      'choices' => apply_filters( 'fictioneer_filter_customizer_story_cover_position', $story_cover_positions )
    )
  );

  // Story cover shadow
  $manager->add_setting(
    'story_cover_shadow',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => apply_filters( 'fictioneer_filter_customizer_story_cover_shadow_default', 'var(--box-shadow-xl)' )
    )
  );

  $cover_shadows = array(
    'none' => _x( 'No Shadow', 'Customizer story cover shadow option.', 'fictioneer' ),
    'var(--box-shadow-border)' => _x( 'Border Shadow', 'Customizer story cover shadow option.', 'fictioneer' ),
    'var(--box-shadow-xs)' => _x( 'Shadow Thin', 'Customizer story cover shadow option.', 'fictioneer' ),
    'var(--box-shadow-s)' => _x( 'Shadow Small', 'Customizer story cover shadow option.', 'fictioneer' ),
    'var(--box-shadow)' => _x( 'Shadow Normal', 'Customizer story cover shadow option.', 'fictioneer' ),
    'var(--box-shadow-m)' => _x( 'Shadow Medium', 'Customizer story cover shadow option.', 'fictioneer' ),
    'var(--box-shadow-l)' => _x( 'Shadow Large', 'Customizer story cover shadow option.', 'fictioneer' ),
    'var(--box-shadow-xl)' => _x( 'Shadow Huge (Default)', 'Customizer story cover shadow option.', 'fictioneer' )
  );

  $manager->add_control(
    'story_cover_shadow',
    array(
      'type' => 'select',
      'priority' => 38.5,
      'section' => 'layout',
      'label' => __( 'Story Page Cover Shadow', 'fictioneer' ),
      'description' => __( 'Choose the shadow for your cover.', 'fictioneer' ),
      'choices' => apply_filters( 'fictioneer_filter_customizer_story_cover_shadow', $cover_shadows )
    )
  );

  // Story cover width offset
  $manager->add_setting(
    'story_cover_width_offset',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'fictioneer_sanitize_float',
      'default' => '0'
    )
  );

  $manager->add_control(
    new Fictioneer_Customize_Range_Control(
      $manager,
      'story_cover_width_offset',
      array(
        'type' => 'range-value',
        'priority' => 39,
        'section' => 'layout',
        'settings' => 'story_cover_width_offset',
        'label' => __( 'Story Cover Width Offset', 'fictioneer' ),
        'description' => '<small>' . __( 'Offset for the maximum width of the story page cover image in pixels. Default 0. Does not work on the "Top-Left Overflow" position.', 'fictioneer' ) . '</small>',
        'input_attrs' => array(
          'min' => -100,
          'max' => 100,
          'step' => 1
        )
      )
    )
  );

  // Content list item style
  $manager->add_setting(
    'content_list_style',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => apply_filters( 'fictioneer_filter_customizer_content_list_style_default', 'default' )
    )
  );

  $content_list_styles = array(
    'default' => _x( 'Gradient (Default)', 'Customizer content list style option.', 'fictioneer' ),
    'full' => _x( 'Full', 'Customizer content list style option.', 'fictioneer' ),
    'lines' => _x( 'Lines', 'Customizer content list style option.', 'fictioneer' ),
    'free' => _x( 'Free', 'Customizer content list style option.', 'fictioneer' )
  );

  $manager->add_control(
    'content_list_style',
    array(
      'type' => 'select',
      'priority' => 40,
      'section' => 'layout',
      'label' => __( 'Content List Style', 'fictioneer' ),
      'description' => __( 'Choose the style for your content lists.', 'fictioneer' ),
      'choices' => apply_filters( 'fictioneer_filter_customizer_content_list_style', $content_list_styles )
    )
  );

  // Content list collapse style
  $manager->add_setting(
    'content_list_collapse_style',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => apply_filters( 'fictioneer_filter_customizer_content_list_collapse_default', 'default' )
    )
  );

  $collapse_styles = array(
    'default' => _x( 'Boxed/Large (Default)', 'Customizer content group style option.', 'fictioneer' ),
    'edge' => _x( 'Edge/Narrow', 'Customizer content list group option.', 'fictioneer' )
  );

  $manager->add_control(
    'content_list_collapse_style',
    array(
      'type' => 'select',
      'priority' => 41,
      'section' => 'layout',
      'label' => __( 'Content List Collapse Style', 'fictioneer' ),
      'description' => __( 'Choose the collapsed style for your content lists. This also depends on your list style.', 'fictioneer' ),
      'choices' => apply_filters( 'fictioneer_filter_customizer_content_list_collapse_style', $collapse_styles )
    )
  );

  // Footer style
  $manager->add_setting(
    'footer_style',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => apply_filters( 'fictioneer_filter_customizer_footer_style_default', 'default' )
    )
  );

  $footer_styles = array(
    'default' => _x( 'Floating (Default)', 'Customizer footer style option.', 'fictioneer' ),
    'isolated' => _x( 'Isolated', 'Customizer footer style option.', 'fictioneer' )
  );

  $manager->add_control(
    'footer_style',
    array(
      'type' => 'select',
      'priority' => 42,
      'section' => 'layout',
      'label' => __( 'Footer Style', 'fictioneer' ),
      'description' => __( 'Choose the style for your footer.', 'fictioneer' ),
      'choices' =>
      apply_filters( 'fictioneer_filter_customizer_footer_style', $footer_styles )
    )
  );

  // Custom layout toggle
  $manager->add_setting(
    'use_custom_layout',
    array(
      'capability' => 'edit_theme_options',
      'default'=> ''
    )
  );

  $manager->add_control(
    'use_custom_layout',
    array(
      'type' => 'checkbox',
      'priority' => 44,
      'section' => 'layout',
      'label' => __( 'Use custom layout properties', 'fictioneer' )
    )
  );

  // Clamp minimum for vertical spacing
  $manager->add_setting(
    'vertical_spacing_min',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint',
      'default' => apply_filters( 'fictioneer_filter_customizer_vertical_spacing_min_default', 24 )
    )
  );

  $manager->add_control(
    'vertical_spacing_min',
    array(
      'type' => 'number',
      'priority' => 46,
      'section' => 'layout',
      'label' => __( 'Vertical Spacing - Minimum', 'fictioneer' ),
      'description' => __( 'Minimum of the vertical spacing in pixels on the smallest viewport. Default 24.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => strval( apply_filters( 'fictioneer_filter_customizer_vertical_spacing_min_default', 24 ) ),
        'style' => 'width: 80px',
        'min' => 0
      )
    )
  );

  // Clamp maximum for vertical spacing
  $manager->add_setting(
    'vertical_spacing_max',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint',
      'default' => apply_filters( 'fictioneer_filter_customizer_vertical_spacing_max_default', 48 )
    )
  );

  $manager->add_control(
    'vertical_spacing_max',
    array(
      'type' => 'number',
      'priority' => 48,
      'section' => 'layout',
      'label' => __( 'Vertical Spacing - Maximum', 'fictioneer' ),
      'description' => __( 'Maximum of the vertical spacing in pixels on [site-width] viewports. Default 48.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => strval( apply_filters( 'fictioneer_filter_customizer_vertical_spacing_max_default', 48 ) ),
        'style' => 'width: 80px',
        'min' => 0
      )
    )
  );

  // Clamp minimum for horizontal spacing
  $manager->add_setting(
    'horizontal_spacing_min',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint',
      'default' => apply_filters( 'fictioneer_filter_customizer_horizontal_spacing_min_default', 20 )
    )
  );

  $manager->add_control(
    'horizontal_spacing_min',
    array(
      'type' => 'number',
      'priority' => 50,
      'section' => 'layout',
      'label' => __( 'Horizontal Spacing - Minimum', 'fictioneer' ),
      'description' => __( 'Minimum of the horizontal spacing in pixels on 480px viewports. Default 20.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => strval( apply_filters( 'fictioneer_filter_customizer_horizontal_spacing_min_default', 20 ) ),
        'style' => 'width: 80px',
        'min' => 0
      )
    )
  );

  // Clamp maximum for horizontal spacing
  $manager->add_setting(
    'horizontal_spacing_max',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint',
      'default' => apply_filters( 'fictioneer_filter_customizer_horizontal_spacing_max_default', 80 )
    )
  );

  $manager->add_control(
    'horizontal_spacing_max',
    array(
      'type' => 'number',
      'priority' => 52,
      'section' => 'layout',
      'label' => __( 'Horizontal Spacing - Maximum', 'fictioneer' ),
      'description' => __( 'Maximum of the horizontal spacing in pixels on [site-width] viewport. Default 80.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => strval( apply_filters( 'fictioneer_filter_customizer_horizontal_spacing_max_default', 80 ) ),
        'style' => 'width: 80px',
        'min' => 0
      )
    )
  );

  // Clamp minimum for horizontal spacing
  $manager->add_setting(
    'horizontal_spacing_small_min',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint',
      'default' => apply_filters( 'fictioneer_filter_customizer_horizontal_spacing_small_min_default', 10 )
    )
  );

  $manager->add_control(
    'horizontal_spacing_small_min',
    array(
      'type' => 'number',
      'priority' => 54,
      'section' => 'layout',
      'label' => __( 'Small Horizontal Spacing - Minimum', 'fictioneer' ),
      'description' => __( 'Minimum of the small horizontal spacing in pixels on 320px viewports. Default 10.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => strval( apply_filters( 'fictioneer_filter_customizer_horizontal_spacing_small_min_default', 10 ) ),
        'style' => 'width: 80px',
        'min' => 0
      )
    )
  );

  // Clamp maximum for horizontal spacing
  $manager->add_setting(
    'horizontal_spacing_small_max',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint',
      'default' => apply_filters( 'fictioneer_filter_customizer_horizontal_spacing_small_max_default', 20 )
    )
  );

  $manager->add_control(
    'horizontal_spacing_small_max',
    array(
      'type' => 'number',
      'priority' => 56,
      'section' => 'layout',
      'label' => __( 'Small Horizontal Spacing - Maximum', 'fictioneer' ),
      'description' => __( 'Maximum of the small horizontal spacing in pixels below 375px viewports. Default 20.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => strval( apply_filters( 'fictioneer_filter_customizer_horizontal_spacing_small_max_default', 20 ) ),
        'style' => 'width: 80px',
        'min' => 0
      )
    )
  );

  // Large border radius
  $manager->add_setting(
    'large_border_radius',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint',
      'default' => apply_filters( 'fictioneer_filter_customizer_large_border_radius_default', 4 )
    )
  );

  $manager->add_control(
    new Fictioneer_Customize_Range_Control(
      $manager,
      'large_border_radius',
      array(
        'type' => 'range-value',
        'priority' => 58,
        'section' => 'layout',
        'settings' => 'large_border_radius',
        'label' => __( 'Large Border Radius', 'fictioneer' ),
        'description' => __( 'Border radius of large containers in pixels, such as the main content section. Default 4.', 'fictioneer' ),
        'input_attrs' => array(
          'placeholder' => strval( apply_filters( 'fictioneer_filter_customizer_large_border_radius_default', 4 ) ),
          'min' => 0,
          'max' => 30,
          'step' => 1
        )
      )
    )
  );

  // Small border radius
  $manager->add_setting(
    'small_border_radius',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint',
      'default' => apply_filters( 'fictioneer_filter_customizer_small_border_radius_default', 2 )
    )
  );

  $manager->add_control(
    new Fictioneer_Customize_Range_Control(
      $manager,
      'small_border_radius',
      array(
        'type' => 'range-value',
        'priority' => 60,
        'section' => 'layout',
        'settings' => 'small_border_radius',
        'label' => __( 'Small Border Radius', 'fictioneer' ),
        'description' => __( 'Border radius of small containers in pixels, such as story cards and inputs. Default 2.', 'fictioneer' ),
        'input_attrs' => array(
          'placeholder' => strval( apply_filters( 'fictioneer_filter_customizer_small_border_radius_default', 2 ) ),
          'min' => 0,
          'max' => 30,
          'step' => 1
        )
      )
    )
  );

  // Nested border radius multiplier
  $manager->add_setting(
    'nested_border_radius_multiplier',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'fictioneer_sanitize_positive_float_def1',
      'default' => apply_filters( 'fictioneer_filter_customizer_nested_border_radius_multiplier_default', 1 )
    )
  );

  $manager->add_control(
    new Fictioneer_Customize_Range_Control(
      $manager,
      'nested_border_radius_multiplier',
      array(
        'type' => 'range-value',
        'priority' => 61,
        'section' => 'layout',
        'settings' => 'nested_border_radius_multiplier',
        'label' => __( 'Nested Border Radius Multiplier', 'fictioneer' ),
        'description' => __( 'Nested border-radii can look bad if too large, which you can offset here. Default 1.', 'fictioneer' ),
        'input_attrs' => array(
          'placeholder' => apply_filters( 'fictioneer_filter_customizer_nested_border_radius_multiplier_default', 1 ),
          'min' => 0,
          'max' => 2,
          'step' => 0.05
        )
      )
    )
  );

  // Content list gap
  $manager->add_setting(
    'content_list_gap',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint',
      'default' => apply_filters( 'fictioneer_filter_customizer_content_list_gap_default', 4 )
    )
  );

  $manager->add_control(
    'content_list_gap',
    array(
      'type' => 'number',
      'priority' => 62,
      'section' => 'layout',
      'label' => __( 'Content List Gap', 'fictioneer' ),
      'description' => __( 'The gap between content list items in pixels (not grid view). Default 4.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => strval( apply_filters( 'fictioneer_filter_customizer_content_list_gap_default', 4 ) ),
        'style' => 'width: 80px',
        'min' => 0
      )
    )
  );
}

// =============================================================================
// CARD SETTINGS
// =============================================================================

/**
 * Add card customizer settings
 *
 * @since 5.19.0
 *
 * @param WP_Customize_Manager $manager  The customizer instance.
 */

function fictioneer_add_card_customizer_settings( $manager ) {
  // Add cards section
  $manager->add_section(
    'cards',
    array(
      'title' => __( 'Cards', 'fictioneer' ),
      'priority' => '80'
    )
  );

  // Card frame
  $manager->add_setting(
    'card_frame',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'default'
    )
  );

  $card_frames = array(
    'default' => _x( 'None (Default)', 'Customizer card frame option.', 'fictioneer' ),
    'stacked_right' => _x( 'Stacked (Right)', 'Customizer card frame option.', 'fictioneer' ),
    'stacked_left' => _x( 'Stacked (Left)', 'Customizer card frame option.', 'fictioneer' ),
    'stacked_random' => _x( 'Stacked (Random)', 'Customizer card frame option.', 'fictioneer' ),
    'border_2px' => _x( 'Border (2px)', 'Customizer card frame option.', 'fictioneer' ),
    'border_3px' => _x( 'Border (3px)', 'Customizer card frame option.', 'fictioneer' ),
    'chamfered' => _x( 'Chamfered', 'Customizer card frame option.', 'fictioneer' ),
    'battered' => _x( 'Battered', 'Customizer card frame option.', 'fictioneer' )
  );

  $manager->add_control(
    'card_frame',
    array(
      'type' => 'select',
      'priority' => 10,
      'section' => 'cards',
      'label' => __( 'Card Frame', 'fictioneer' ),
      'description' => __( 'Choose the frame for your cards. Turn off the card shadow if borders get blurry.', 'fictioneer' ),
      'choices' => apply_filters( 'fictioneer_filter_customizer_card_frame', $card_frames )
    )
  );

  // Card image style
  $manager->add_setting(
    'card_image_style',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'default'
    )
  );

  $card_image_styles = array(
    'default' => _x( 'Embedded (Default)', 'Customizer card image option.', 'fictioneer' ),
    'seamless' => _x( 'Seamless', 'Customizer card image option.', 'fictioneer' ),
    'none' => _x( 'None', 'Customizer card image option.', 'fictioneer' )
  );

  $manager->add_control(
    'card_image_style',
    array(
      'type' => 'select',
      'priority' => 12,
      'section' => 'cards',
      'label' => __( 'Card Image Style', 'fictioneer' ),
      'description' => __( 'Choose the image style for your cards. Can be overridden in shortcodes.', 'fictioneer' ),
      'choices' => apply_filters( 'fictioneer_filter_customizer_card_image_style', $card_image_styles )
    )
  );

  // Card footer style (called card_style due to legacy reasons)
  $manager->add_setting(
    'card_style',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'default'
    )
  );

  $card_footer_styles = array(
    'default' => _x( 'Embedded (Default)', 'Customizer card style option.', 'fictioneer' ),
    'unfolded' => _x( 'Unfolded', 'Customizer card style option.', 'fictioneer' ),
    'combined' => _x( 'Combined', 'Customizer card style option.', 'fictioneer' )
  );

  $manager->add_control(
    'card_style',
    array(
      'type' => 'select',
      'priority' => 14,
      'section' => 'cards',
      'label' => __( 'Card Footer Style', 'fictioneer' ),
      'description' => __( 'Choose the footer style for your cards.', 'fictioneer' ),
      'choices' => apply_filters( 'fictioneer_filter_customizer_card_style', $card_footer_styles )
    )
  );

  // Card shadow
  $manager->add_setting(
    'card_shadow',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'var(--box-shadow-m)'
    )
  );

  $card_shadows = array(
    'none' => _x( 'No Shadow', 'Customizer card shadow option.', 'fictioneer' ),
    'var(--box-shadow-border)' => _x( 'Border Shadow', 'Customizer card shadow option.', 'fictioneer' ),
    'var(--box-shadow-xs)' => _x( 'Shadow Thin', 'Customizer card shadow option.', 'fictioneer' ),
    'var(--box-shadow-s)' => _x( 'Shadow Small', 'Customizer card shadow option.', 'fictioneer' ),
    'var(--box-shadow)' => _x( 'Shadow Normal', 'Customizer card shadow option.', 'fictioneer' ),
    'var(--box-shadow-m)' => _x( 'Shadow Medium (Default)', 'Customizer card shadow option.', 'fictioneer' ),
    'var(--box-shadow-l)' => _x( 'Shadow Large', 'Customizer card shadow option.', 'fictioneer' ),
    'var(--box-shadow-xl)' => _x( 'Shadow Huge', 'Customizer card shadow option.', 'fictioneer' )
  );

  $manager->add_control(
    'card_shadow',
    array(
      'type' => 'select',
      'priority' => 16,
      'section' => 'cards',
      'label' => __( 'Card Shadow', 'fictioneer' ),
      'description' => __( 'Choose the shadow for your cards.', 'fictioneer' ),
      'choices' => apply_filters( 'fictioneer_filter_customizer_card_shadow', $card_shadows )
    )
  );

  // Card grid column minimum width
  $manager->add_setting(
    'card_grid_column_min',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint',
      'default' => 308
    )
  );

  $manager->add_control(
    'card_grid_column_min',
    array(
      'type' => 'number',
      'priority' => 18,
      'section' => 'cards',
      'label' => __( 'Card Width', 'fictioneer' ),
      'description' => __( 'Minimum card width in pixels (still limited by space); affects card scale. Default 308.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => '308',
        'min' => 308,
        'style' => 'width: 80px'
      )
    )
  );

  // Card cover width modifier
  $manager->add_setting(
    'card_cover_width_mod',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'fictioneer_sanitize_positive_float_def1',
      'default' => '1'
    )
  );

  $manager->add_control(
    new Fictioneer_Customize_Range_Control(
      $manager,
      'card_cover_width_mod',
      array(
        'type' => 'range-value',
        'priority' => 20,
        'section' => 'cards',
        'settings' => 'card_cover_width_mod',
        'label' => __( 'Card Cover Multiplier', 'fictioneer' ),
        'description' => '<small>' . __( 'Multiplier for the card cover width. Default 1.', 'fictioneer' ) . '</small>',
        'input_attrs' => array(
          'min' => 0,
          'max' => 5,
          'step' => 0.05
        )
      )
    )
  );

  // Card row gap modifier
  $manager->add_setting(
    'card_grid_row_gap_mod',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'fictioneer_sanitize_positive_float_def1',
      'default' => '1'
    )
  );

  $manager->add_control(
    new Fictioneer_Customize_Range_Control(
      $manager,
      'card_grid_row_gap_mod',
      array(
        'type' => 'range-value',
        'priority' => 22,
        'section' => 'cards',
        'settings' => 'card_grid_row_gap_mod',
        'label' => __( 'Card Row Gap Multiplier', 'fictioneer' ),
        'description' => '<small>' . __( 'Multiplier for the grid row gap. Default 1.', 'fictioneer' ) . '</small>',
        'input_attrs' => array(
          'min' => 0,
          'max' => 10,
          'step' => 0.05
        )
      )
    )
  );

  // Card column gap modifier
  $manager->add_setting(
    'card_grid_column_gap_mod',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'fictioneer_sanitize_positive_float_def1',
      'default' => '1'
    )
  );

  $manager->add_control(
    new Fictioneer_Customize_Range_Control(
      $manager,
      'card_grid_column_gap_mod',
      array(
        'type' => 'range-value',
        'priority' => 24,
        'section' => 'cards',
        'settings' => 'card_grid_column_gap_mod',
        'label' => __( 'Card Column Gap Multiplier', 'fictioneer' ),
        'description' => '<small>' . __( 'Multiplier for the grid column gap. Default 1.', 'fictioneer' ) . '</small>',
        'input_attrs' => array(
          'min' => 0,
          'max' => 10,
          'step' => 0.05
        )
      )
    )
  );

  // Card font size min modifier
  $manager->add_setting(
    'card_font_size_min_mod',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'fictioneer_sanitize_float',
      'default' => '0'
    )
  );

  $manager->add_control(
    new Fictioneer_Customize_Range_Control(
      $manager,
      'card_font_size_min_mod',
      array(
        'type' => 'range-value',
        'priority' => 26,
        'section' => 'cards',
        'settings' => 'card_font_size_min_mod',
        'label' => __( 'Card Font Size Minimum Offset', 'fictioneer' ),
        'description' => '<small>' . __( 'Modifies the lower end of the dynamic card font sizes in fractions of pixels. Default 0.', 'fictioneer' ) . '</small>',
        'input_attrs' => array(
          'min' => -4,
          'max' => 4,
          'step' => 0.1
        )
      )
    )
  );

  // Card font size grow modifier
  $manager->add_setting(
    'card_font_size_grow_mod',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'fictioneer_sanitize_float',
      'default' => '0'
    )
  );

  $manager->add_control(
    new Fictioneer_Customize_Range_Control(
      $manager,
      'card_font_size_grow_mod',
      array(
        'type' => 'range-value',
        'priority' => 28,
        'section' => 'cards',
        'settings' => 'card_font_size_grow_mod',
        'label' => __( 'Card Font Size Grow Offset', 'fictioneer' ),
        'description' => '<small>' . __( 'Modifies the calculation of the dynamic card font sizes in fractions of pixels. Default 0.', 'fictioneer' ) . '</small>',
        'input_attrs' => array(
          'min' => -4,
          'max' => 4,
          'step' => 0.1
        )
      )
    )
  );

  // Card font size max modifier
  $manager->add_setting(
    'card_font_size_max_mod',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'fictioneer_sanitize_float',
      'default' => '0'
    )
  );

  $manager->add_control(
    new Fictioneer_Customize_Range_Control(
      $manager,
      'card_font_size_max_mod',
      array(
        'type' => 'range-value',
        'priority' => 30,
        'section' => 'cards',
        'settings' => 'card_font_size_max_mod',
        'label' => __( 'Card Font Size Maximum Offset', 'fictioneer' ),
        'description' => '<small>' . __( 'Modifies the upper end of the dynamic card font sizes in fractions of pixels. Default 0.', 'fictioneer' ) . '</small>',
        'input_attrs' => array(
          'min' => -4,
          'max' => 4,
          'step' => 0.1
        )
      )
    )
  );
}

// =============================================================================
// FONT SETTINGS
// =============================================================================

/**
 * Add fonts customizer settings
 *
 * @since 5.10.0
 *
 * @param WP_Customize_Manager $manager  The customizer instance.
 */

function fictioneer_add_fonts_customizer_settings( $manager ) {
  // Setup
  $fonts = fictioneer_get_font_data();
  $font_options = array( 'system' => __( 'System Font', 'fictioneer' ) );
  $disabled_fonts = get_option( 'fictioneer_disabled_fonts', [] );
  $disabled_fonts = is_array( $disabled_fonts ) ? $disabled_fonts : [];

  foreach ( $fonts as $key => $font ) {
    $name = $font['name'];

    if ( in_array( $key, $disabled_fonts ) ) {
      $name = sprintf(
        _x( '%s — Disabled', 'Disabled Customizer font option.', 'fictioneer' ),
        $name
      );
    }

    $font_options[ $font['family'] ] = $name;
  }

  $font_sub_options = array_merge(
    array( 'default' => __( 'Default', 'fictioneer' ) ),
    $font_options
  );

  // Add layout section
  $manager->add_section(
    'fictioneer_fonts',
    array(
      'title' => __( 'Fonts', 'fictioneer' ),
      'priority' => '81'
    )
  );

  // Primary font
  $manager->add_setting(
    'primary_font_family_value',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'Open Sans'
    )
  );

  $manager->add_control(
    'primary_font_family_value',
    array(
      'type' => 'select',
      'priority' => 10,
      'section' => 'fictioneer_fonts',
      'label' => __( 'Primary Font', 'fictioneer' ),
      'description' => __( 'Used for most of the content and as default chapter font. Default "Open Sans".', 'fictioneer' ),
      'choices' => $font_options
    )
  );

  // Secondary font
  $manager->add_setting(
    'secondary_font_family_value',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'Lato'
    )
  );

  $manager->add_control(
    'secondary_font_family_value',
    array(
      'type' => 'select',
      'priority' => 12,
      'section' => 'fictioneer_fonts',
      'label' => __( 'Secondary Font', 'fictioneer' ),
      'description' => __( 'Used for small cards, tags, alongside icons, and in meta rows. Default "Lato".', 'fictioneer' ),
      'choices' => $font_options
    )
  );

  // Heading font
  $manager->add_setting(
    'heading_font_family_value',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'Open Sans'
    )
  );

  $manager->add_control(
    'heading_font_family_value',
    array(
      'type' => 'select',
      'priority' => 14,
      'section' => 'fictioneer_fonts',
      'label' => __( 'Heading Font', 'fictioneer' ),
      'description' => __( 'Used for the site title plus articles, chapters, and cards. Default "Open Sans".', 'fictioneer' ),
      'choices' => $font_options
    )
  );

  // Site title font
  $manager->add_setting(
    'site_title_font_family_value',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'default'
    )
  );

  $manager->add_control(
    'site_title_font_family_value',
    array(
      'type' => 'select',
      'priority' => 16,
      'section' => 'fictioneer_fonts',
      'label' => __( 'Site Title Font', 'fictioneer' ),
      'description' => __( 'Used for the site title and tagline. Defaults to heading font.', 'fictioneer' ),
      'choices' => $font_sub_options
    )
  );

  // Navigation item font
  $manager->add_setting(
    'nav_item_font_family_value',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'default'
    )
  );

  $manager->add_control(
    'nav_item_font_family_value',
    array(
      'type' => 'select',
      'priority' => 18,
      'section' => 'fictioneer_fonts',
      'label' => __( 'Navigation Item Font', 'fictioneer' ),
      'description' => __( 'Used for the items in the navigation bar. Defaults to primary font.', 'fictioneer' ),
      'choices' => $font_sub_options
    )
  );

  // Story title font
  $manager->add_setting(
    'story_title_font_family_value',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'default'
    )
  );

  $manager->add_control(
    'story_title_font_family_value',
    array(
      'type' => 'select',
      'priority' => 20,
      'section' => 'fictioneer_fonts',
      'label' => __( 'Story Title Font', 'fictioneer' ),
      'description' => __( 'Used for the title on story pages. Defaults to heading font.', 'fictioneer' ),
      'choices' => $font_sub_options
    )
  );

  // Chapter title font
  $manager->add_setting(
    'chapter_title_font_family_value',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'default'
    )
  );

  $manager->add_control(
    'chapter_title_font_family_value',
    array(
      'type' => 'select',
      'priority' => 22,
      'section' => 'fictioneer_fonts',
      'label' => __( 'Chapter Title Font', 'fictioneer' ),
      'description' => __( 'Used for the title on chapter pages. Defaults to heading font.', 'fictioneer' ),
      'choices' => $font_sub_options
    )
  );

  // Chapter list title font
  $manager->add_setting(
    'chapter_list_title_font_family_value',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'default'
    )
  );

  $manager->add_control(
    'chapter_list_title_font_family_value',
    array(
      'type' => 'select',
      'priority' => 24,
      'section' => 'fictioneer_fonts',
      'label' => __( 'Chapter List Title Font', 'fictioneer' ),
      'description' => __( 'Used for the title row in chapter lists. Defaults to primary font.', 'fictioneer' ),
      'choices' => $font_sub_options
    )
  );

  // Chapter body font
  $manager->add_setting(
    'chapter_chapter_body_font_family_value',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'default'
    )
  );

  $manager->add_control(
    'chapter_chapter_body_font_family_value',
    array(
      'type' => 'select',
      'priority' => 26,
      'section' => 'fictioneer_fonts',
      'label' => __( 'Chapter Body Font', 'fictioneer' ),
      'description' => __( 'Pre-selected font used in chapters. Defaults to primary font.', 'fictioneer' ),
      'choices' => $font_sub_options
    )
  );

  // Card title font
  $manager->add_setting(
    'card_title_font_family_value',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'default'
    )
  );

  $manager->add_control(
    'card_title_font_family_value',
    array(
      'type' => 'select',
      'priority' => 28,
      'section' => 'fictioneer_fonts',
      'label' => __( 'Card Title Font', 'fictioneer' ),
      'description' => __( 'Used for the card titles. Defaults to heading font.', 'fictioneer' ),
      'choices' => $font_sub_options
    )
  );

  // Card body font
  $manager->add_setting(
    'card_body_font_family_value',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'default'
    )
  );

  $manager->add_control(
    'card_body_font_family_value',
    array(
      'type' => 'select',
      'priority' => 30,
      'section' => 'fictioneer_fonts',
      'label' => __( 'Card Body Font', 'fictioneer' ),
      'description' => __( 'Used for the card content. Defaults to secondary font.', 'fictioneer' ),
      'choices' => $font_sub_options
    )
  );

  // Card list link font
  $manager->add_setting(
    'card_list_link_font_family_value',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'default'
    )
  );

  $manager->add_control(
    'card_list_link_font_family_value',
    array(
      'type' => 'select',
      'priority' => 32,
      'section' => 'fictioneer_fonts',
      'label' => __( 'Card List Link Font', 'fictioneer' ),
      'description' => __( 'Used for the links in card lists. Defaults to secondary font.', 'fictioneer' ),
      'choices' => $font_sub_options
    )
  );

  // Dark mode font weight adjustment
  $manager->add_setting(
    'dark_mode_font_weight',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'adjusted'
    )
  );

  $manager->add_control(
    'dark_mode_font_weight',
    array(
      'type' => 'select',
      'priority' => 34,
      'section' => 'fictioneer_fonts',
      'label' => __( 'Dark Mode Font Weight', 'fictioneer' ),
      'description' => __( 'Fonts are rendered thinner in dark mode to offset text bleeding; you can turn that off.', 'fictioneer' ),
      'choices' => array(
        'adjusted' => _x( 'Adjusted', 'Customizer dark mode font weight option.', 'fictioneer' ),
        'normal' => _x( 'Normal', 'Customizer dark mode font weight option.', 'fictioneer' )
      ),
    )
  );
}

// =============================================================================
// CUSTOMIZER SETTINGS SETUP
// =============================================================================

/**
 * Set up the theme customizer
 *
 * @since 3.0
 * @since 4.7 Expanded settings to customize color scheme.
 *
 * @param WP_Customize_Manager $manager  The customizer instance.
 */

function fictioneer_add_customizers( $manager ) {
  // Remove obsolete/redundant default settings
  $manager->remove_setting( 'background_color' );
  $manager->remove_section( 'colors' );

  // Dark mode
  fictioneer_add_dark_mode_customizer_settings( $manager );

  // Light mode
  fictioneer_add_light_mode_customizer_settings( $manager );

  // Header
  fictioneer_add_header_customizer_settings( $manager );

  // Layout
  fictioneer_add_layout_customizer_settings( $manager );

  // Cards
  fictioneer_add_card_customizer_settings( $manager );

  // Fonts
  fictioneer_add_fonts_customizer_settings( $manager );

  // Resets
  fictioneer_add_reset_options( $manager );

  // Open Graph image
  $manager->add_setting(
    'og_image',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint'
    )
  );

  $manager->add_control(
    new WP_Customize_Cropped_Image_Control(
      $manager,
      'og_image_control',
      array(
        'label' => __( 'Open Graph Default Image', 'fictioneer' ),
        'description' => __( 'Used in rich snippets. Don’t forget to purge all SEO schemas after updating.', 'fictioneer' ),
        'priority' => 9,
        'section' => 'title_tagline',
        'settings' => 'og_image',
        'height' => 630,
        'width' => 1200,
        'flex_width' => true,
        'flex_height' => true,
        'button_labels' => array(
          'select' => __( 'Select Open Graph image', 'fictioneer' ),
          'remove' => __( 'Remove', 'fictioneer' ),
          'change' => __( 'Change Open Graph image', 'fictioneer' )
        )
      )
    )
  );
}
add_action( 'customize_register', 'fictioneer_add_customizers', 20 );
