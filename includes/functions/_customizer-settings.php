<?php

// =============================================================================
// ADD CUSTOMIZER RANGE VALUE CONTROL
// =============================================================================

/**
 * Check for WP_Customizer_Control existence before adding custom control because
 * WP_Customize_Control is loaded on customizer page only.
 *
 * @author Sean Stopnik
 * @author Per Soderlind
 * @see _wp_customize_include()
 */

if ( class_exists( 'WP_Customize_Control' ) ) {
  require_once( __DIR__ . '/class-customizer-range-value-control/class-customizer-range-value-control.php' );
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
      'priority' => '84'
    )
  );

  // Hue offset
  $manager->add_setting(
    'hue_offset_light',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'absint',
      'default' => 0
    )
  );

  $manager->add_control(
    new Customizer_Range_Value_Control(
      $manager,
      'hue_offset_light',
      array(
        'type' => 'range-value',
        'priority' => 10,
        'section' => 'light_mode_colors',
        'settings' => 'hue_offset_light',
        'label' => __( 'Hue Offset', 'fictioneer' ),
        'description' => __( 'Adds an offset to the hue rotate, tinting the whole site. Default 0.', 'fictioneer' ),
        'input_attrs' => array(
          'min' => 0,
          'max' => 360,
          'step' => 1,
          'suffix' => ' °'
        )
      )
    )
  );

  // Saturation offset
  $manager->add_setting(
    'saturation_offset_light',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'fictioneer_sanitize_integer',
      'default' => 0
    )
  );

  $manager->add_control(
    new Customizer_Range_Value_Control(
      $manager,
      'saturation_offset_light',
      array(
        'type' => 'range-value',
        'priority' => 10,
        'section' => 'light_mode_colors',
        'settings' => 'saturation_offset_light',
        'label' => __( 'Saturation Offset', 'fictioneer' ),
        'description' => __( 'Adds an offset to the saturation, affecting the whole site. Default 0.', 'fictioneer' ),
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
      'capability' => 'manage_options',
      'sanitize_callback' => 'fictioneer_sanitize_integer',
      'default' => 0
    )
  );

  $manager->add_control(
    new Customizer_Range_Value_Control(
      $manager,
      'lightness_offset_light',
      array(
        'type' => 'range-value',
        'priority' => 10,
        'section' => 'light_mode_colors',
        'settings' => 'lightness_offset_light',
        'label' => __( 'Lightness Offset', 'fictioneer' ),
        'description' => __( 'Adds an offset to the lightness, affecting the whole site. Default 0.', 'fictioneer' ),
        'input_attrs' => array(
          'min' => -100,
          'max' => 100,
          'step' => 1
        )
      )
    )
  );

  // Header title color
  $manager->add_setting(
    'light_header_title_color',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#fcfcfd'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_header_title_color',
      array(
        'label' => __( 'Light Header Title', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_header_title_color'
      )
    )
  );

  $manager->add_setting(
    'light_header_tagline_color',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#f3f5f7'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_header_tagline_color',
      array(
        'label' => __( 'Light Header Tagline', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_header_tagline_color'
      )
    )
  );

  $manager->add_setting(
    'use_custom_light_mode',
    array(
      'capability' => 'manage_options',
      'default'=> ''
    )
  );

  $manager->add_control(
    'use_custom_light_mode',
    array(
      'type' => 'checkbox',
      'priority' => 10,
      'section' => 'light_mode_colors',
      'label' => __( 'Use custom light mode colors', 'fictioneer' )
    )
  );

  $manager->add_setting(
    'light_bg_10',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#fcfcfd'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_bg_10',
      array(
        'label' => __( 'Light Background 10', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_bg_10'
      )
    )
  );

  $manager->add_setting(
    'light_bg_50',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#f9fafb'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_bg_50',
      array(
        'label' => __( 'Light Background 50', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_bg_50'
      )
    )
  );

  $manager->add_setting(
    'light_bg_100',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#f3f4f6'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_bg_100',
      array(
        'label' => __( 'Light Background 100', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_bg_100'
      )
    )
  );

  $manager->add_setting(
    'light_bg_200',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#e5e7eb'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_bg_200',
      array(
        'label' => __( 'Light Background 200', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_bg_200'
      )
    )
  );

  $manager->add_setting(
    'light_bg_300',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#d1d5db'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_bg_300',
      array(
        'label' => __( 'Light Background 300', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_bg_300'
      )
    )
  );

  $manager->add_setting(
    'light_bg_400',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#9ca3b0'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_bg_400',
      array(
        'label' => __( 'Light Background 400', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_bg_400'
      )
    )
  );

  $manager->add_setting(
    'light_bg_500',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#6b7280'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_bg_500',
      array(
        'label' => __( 'Light Background 500', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_bg_500'
      )
    )
  );

  $manager->add_setting(
    'light_bg_600',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#4b5563'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_bg_600',
      array(
        'label' => __( 'Light Background 600', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_bg_600'
      )
    )
  );

  $manager->add_setting(
    'light_bg_700',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#384252'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_bg_700',
      array(
        'label' => __( 'Light Background 700', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_bg_700'
      )
    )
  );

  $manager->add_setting(
    'light_bg_800',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#1f2937'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_bg_800',
      array(
        'label' => __( 'Light Background 800', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_bg_800'
      )
    )
  );

  $manager->add_setting(
    'light_bg_900',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#111827'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_bg_900',
      array(
        'label' => __( 'Light Background 900', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_bg_900'
      )
    )
  );

  $manager->add_setting(
    'light_bg_1000',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#111827'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_bg_1000',
      array(
        'label' => __( 'Light Background 1000', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_bg_1000'
      )
    )
  );

  $manager->add_setting(
    'light_fg_100',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#010204'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_fg_100',
      array(
        'label' => __( 'Light Foreground 100', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_fg_100'
      )
    )
  );

  $manager->add_setting(
    'light_fg_200',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#04060b'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_fg_200',
      array(
        'label' => __( 'Light Foreground 200', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_fg_200'
      )
    )
  );

  $manager->add_setting(
    'light_fg_300',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#0a0f1a'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_fg_300',
      array(
        'label' => __( 'Light Foreground 300', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_fg_300'
      )
    )
  );

  $manager->add_setting(
    'light_fg_400',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#111827'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_fg_400',
      array(
        'label' => __( 'Light Foreground 400', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_fg_400'
      )
    )
  );

  $manager->add_setting(
    'light_fg_500',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#1f2937'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_fg_500',
      array(
        'label' => __( 'Light Foreground 500', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_fg_500'
      )
    )
  );

  $manager->add_setting(
    'light_fg_600',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#384252'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_fg_600',
      array(
        'label' => __( 'Light Foreground 600', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_fg_600'
      )
    )
  );

  $manager->add_setting(
    'light_fg_700',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#4b5563'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_fg_700',
      array(
        'label' => __( 'Light Foreground 700', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_fg_700'
      )
    )
  );

  $manager->add_setting(
    'light_fg_800',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#6b7280'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_fg_800',
      array(
        'label' => __( 'Light Foreground 800', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_fg_800'
      )
    )
  );

  $manager->add_setting(
    'light_fg_900',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#9ca3b0'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_fg_900',
      array(
        'label' => __( 'Light Foreground 900', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_fg_900'
      )
    )
  );

  $manager->add_setting(
    'light_fg_1000',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#f3f5f7'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_fg_1000',
      array(
        'label' => __( 'Light Foreground 1000', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_fg_1000'
      )
    )
  );

  $manager->add_setting(
    'light_fg_tinted',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#2b3546'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_fg_tinted',
      array(
        'label' => __( 'Light Foreground Tinted', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_fg_tinted'
      )
    )
  );

  $manager->add_setting(
    'light_theme_color_base',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#f3f4f6'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_theme_color_base',
      array(
        'label' => __( 'Light Theme Color Meta', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_theme_color_base'
      )
    )
  );

  $manager->add_setting(
    'light_secant',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#e5e7eb'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_secant',
      array(
        'label' => __( 'Light Secant', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_secant'
      )
    )
  );

  $manager->add_setting(
    'light_elevation_overlay',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#191b1f'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_elevation_overlay',
      array(
        'label' => __( 'Light Overlays', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_elevation_overlay'
      )
    )
  );

  $manager->add_setting(
    'light_navigation_background_sticky',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#fcfcfd'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_navigation_background_sticky',
      array(
        'label' => __( 'Light Navigation', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_navigation_background_sticky'
      )
    )
  );

  $manager->add_setting(
    'light_primary_400',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#4287f5'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_primary_400',
      array(
        'label' => __( 'Light Primary 400', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_primary_400'
      )
    )
  );

  $manager->add_setting(
    'light_primary_500',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#3c83f6'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_primary_500',
      array(
        'label' => __( 'Light Primary 500', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_primary_500'
      )
    )
  );

  $manager->add_setting(
    'light_primary_600',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#1e3fae'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_primary_600',
      array(
        'label' => __( 'Light Primary 600', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_primary_600'
      )
    )
  );

  $manager->add_setting(
    'light_warning_color',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#eb5247'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_warning_color',
      array(
        'label' => __( 'Light Warning', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_warning_color'
      )
    )
  );

  $manager->add_setting(
    'light_bookmark_line_color',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#3c83f6'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_bookmark_line_color',
      array(
        'label' => __( 'Light Bookmark Line', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_bookmark_line_color'
      )
    )
  );

  $manager->add_setting(
    'light_bookmark_color_alpha',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#9ca3b0'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_bookmark_color_alpha',
      array(
        'label' => __( 'Light Bookmark Alpha', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_bookmark_color_alpha'
      )
    )
  );

  $manager->add_setting(
    'light_bookmark_color_beta',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#f59e0b'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_bookmark_color_beta',
      array(
        'label' => __( 'Light Bookmark Beta', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_bookmark_color_beta'
      )
    )
  );

  $manager->add_setting(
    'light_bookmark_color_gamma',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#77bfa3'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_bookmark_color_gamma',
      array(
        'label' => __( 'Light Bookmark Gamma', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_bookmark_color_gamma'
      )
    )
  );

  $manager->add_setting(
    'light_bookmark_color_delta',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#dd5960'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_bookmark_color_delta',
      array(
        'label' => __( 'Light Bookmark Delta', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_bookmark_color_delta'
      )
    )
  );

  $manager->add_setting(
    'light_ins_background',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#7ec945'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_ins_background',
      array(
        'label' => __( 'Light &ltins>', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_ins_background'
      )
    )
  );

  $manager->add_setting(
    'light_del_background',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#e96c63'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_del_background',
      array(
        'label' => __( 'Light &ltdel>', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_del_background'
      )
    )
  );

  $manager->add_setting(
    'light_vote_down',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#dc2626'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_vote_down',
      array(
        'label' => __( 'Light Vote Down', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_vote_down'
      )
    )
  );

  $manager->add_setting(
    'light_vote_up',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#16a34a'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_vote_up',
      array(
        'label' => __( 'Light Vote Up', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_vote_up'
      )
    )
  );

  $manager->add_setting(
    'light_badge_generic_background',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#9ca3b0'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_badge_generic_background',
      array(
        'label' => __( 'Light Generic Badge', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_badge_generic_background'
      )
    )
  );

  $manager->add_setting(
    'light_badge_moderator_background',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#5369ac'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_badge_moderator_background',
      array(
        'label' => __( 'Light Moderator Badge', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_badge_moderator_background'
      )
    )
  );

  $manager->add_setting(
    'light_badge_admin_background',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#384252'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_badge_admin_background',
      array(
        'label' => __( 'Light Admin Badge', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_badge_admin_background'
      )
    )
  );

  $manager->add_setting(
    'light_badge_author_background',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#384252'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_badge_author_background',
      array(
        'label' => __( 'Light Author Badge', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_badge_author_background'
      )
    )
  );

  $manager->add_setting(
    'light_badge_supporter_background',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#ed5e76'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_badge_supporter_background',
      array(
        'label' => __( 'Light Supporter Badge', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_badge_supporter_background'
      )
    )
  );

  $manager->add_setting(
    'light_badge_override_background',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#9ca3b0'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'light_badge_override_background',
      array(
        'label' => __( 'Light Override Badge', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'light_badge_override_background'
      )
    )
  );
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
      'priority' => '83'
    )
  );

  // Hue offset
  $manager->add_setting(
    'hue_offset',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'absint',
      'default' => 0
    )
  );

  $manager->add_control(
    new Customizer_Range_Value_Control(
      $manager,
      'hue_offset',
      array(
        'type' => 'range-value',
        'priority' => 10,
        'section' => 'dark_mode_colors',
        'settings' => 'hue_offset',
        'label' => __( 'Hue Offset', 'fictioneer' ),
        'description' => __( 'Adds an offset to the hue rotate, tinting the whole site. Default 0.', 'fictioneer' ),
        'input_attrs' => array(
          'min' => 0,
          'max' => 360,
          'step' => 1,
          'suffix' => ' °'
        )
      )
    )
  );

  // Saturation offset
  $manager->add_setting(
    'saturation_offset',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'fictioneer_sanitize_integer',
      'default' => 0
    )
  );

  $manager->add_control(
    new Customizer_Range_Value_Control(
      $manager,
      'saturation_offset',
      array(
        'type' => 'range-value',
        'priority' => 10,
        'section' => 'dark_mode_colors',
        'settings' => 'saturation_offset',
        'label' => __( 'Saturation Offset', 'fictioneer' ),
        'description' => __( 'Adds an offset to the saturation, affecting the whole site. Default 0.', 'fictioneer' ),
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
      'capability' => 'manage_options',
      'sanitize_callback' => 'fictioneer_sanitize_integer',
      'default' => 0
    )
  );

  $manager->add_control(
    new Customizer_Range_Value_Control(
      $manager,
      'lightness_offset',
      array(
        'type' => 'range-value',
        'priority' => 10,
        'section' => 'dark_mode_colors',
        'settings' => 'lightness_offset',
        'label' => __( 'Lightness Offset', 'fictioneer' ),
        'description' => __( 'Adds an offset to the lightness, affecting the whole site. Default 0.', 'fictioneer' ),
        'input_attrs' => array(
          'min' => -100,
          'max' => 100,
          'step' => 1
        )
      )
    )
  );

  // Header title color
  $manager->add_setting(
    'dark_header_title_color',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#dadde2'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_header_title_color',
      array(
        'label' => __( 'Dark Header Title', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_header_title_color'
      )
    )
  );

  // Header tagline color
  $manager->add_setting(
    'dark_header_tagline_color',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#dadde2'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_header_tagline_color',
      array(
        'label' => __( 'Dark Header Tagline', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_header_tagline_color'
      )
    )
  );

  // Header title color
  $manager->add_setting(
    'use_custom_dark_mode',
    array(
      'capability' => 'manage_options',
      'default'=> ''
    )
  );

  $manager->add_control(
    'use_custom_dark_mode',
    array(
      'type' => 'checkbox',
      'priority' => 10,
      'section' => 'dark_mode_colors',
      'label' => __( 'Use custom dark mode colors', 'fictioneer' )
    )
  );

  $manager->add_setting(
    'dark_bg_50',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#4b505d'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_bg_50',
      array(
        'label' => __( 'Dark Background 50', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_bg_50'
      )
    )
  );

  $manager->add_setting(
    'dark_bg_100',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#464c58'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_bg_100',
      array(
        'label' => __( 'Dark Background 100', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_bg_100'
      )
    )
  );

  $manager->add_setting(
    'dark_bg_200',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#434956'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_bg_200',
      array(
        'label' => __( 'Dark Background 200', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_bg_200'
      )
    )
  );

  $manager->add_setting(
    'dark_bg_300',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#414653'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_bg_300',
      array(
        'label' => __( 'Dark Background 300', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_bg_300'
      )
    )
  );

  $manager->add_setting(
    'dark_bg_400',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#3e4451'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_bg_400',
      array(
        'label' => __( 'Dark Background 400', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_bg_400'
      )
    )
  );

  $manager->add_setting(
    'dark_bg_500',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#3c414e'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_bg_500',
      array(
        'label' => __( 'Dark Background 500', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_bg_500'
      )
    )
  );

  $manager->add_setting(
    'dark_bg_600',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#373c49'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_bg_600',
      array(
        'label' => __( 'Dark Background 600', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_bg_600'
      )
    )
  );

  $manager->add_setting(
    'dark_bg_700',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#323743'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_bg_700',
      array(
        'label' => __( 'Dark Background 700', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_bg_700'
      )
    )
  );

  $manager->add_setting(
    'dark_bg_800',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#2b303b'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_bg_800',
      array(
        'label' => __( 'Dark Background 800', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_bg_800'
      )
    )
  );

  $manager->add_setting(
    'dark_bg_900',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#252932'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_bg_900',
      array(
        'label' => __( 'Dark Background 900', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_bg_900'
      )
    )
  );

  $manager->add_setting(
    'dark_bg_1000',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#121317'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_bg_1000',
      array(
        'label' => __( 'Dark Background 1000', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_bg_1000'
      )
    )
  );

  $manager->add_setting(
    'dark_fg_100',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#dddfe3'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_fg_100',
      array(
        'label' => __( 'Dark Foreground 100', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_fg_100'
      )
    )
  );

  $manager->add_setting(
    'dark_fg_200',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#d2d4db'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_fg_200',
      array(
        'label' => __( 'Dark Foreground 200', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_fg_200'
      )
    )
  );

  $manager->add_setting(
    'dark_fg_300',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#c4c7cf'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_fg_300',
      array(
        'label' => __( 'Dark Foreground 300', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_fg_300'
      )
    )
  );

  $manager->add_setting(
    'dark_fg_400',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#b4bac5'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_fg_400',
      array(
        'label' => __( 'Dark Foreground 400', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_fg_400'
      )
    )
  );

  $manager->add_setting(
    'dark_fg_500',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#a5acbb'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_fg_500',
      array(
        'label' => __( 'Dark Foreground 500', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_fg_500'
      )
    )
  );

  $manager->add_setting(
    'dark_fg_600',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#9aa1b1'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_fg_600',
      array(
        'label' => __( 'Dark Foreground 600', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_fg_600'
      )
    )
  );

  $manager->add_setting(
    'dark_fg_700',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#929aaa'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_fg_700',
      array(
        'label' => __( 'Dark Foreground 700', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_fg_700'
      )
    )
  );

  $manager->add_setting(
    'dark_fg_800',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#9298a5'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_fg_800',
      array(
        'label' => __( 'Dark Foreground 800', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_fg_800'
      )
    )
  );

  $manager->add_setting(
    'dark_fg_900',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#787f91'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_fg_900',
      array(
        'label' => __( 'Dark Foreground 900', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_fg_900'
      )
    )
  );

  $manager->add_setting(
    'dark_fg_1000',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#121416'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_fg_1000',
      array(
        'label' => __( 'Dark Foreground 1000', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_fg_1000'
      )
    )
  );

  $manager->add_setting(
    'dark_fg_tinted',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#a4a9b7'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_fg_tinted',
      array(
        'label' => __( 'Dark Foreground Tinted', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_fg_tinted'
      )
    )
  );

  $manager->add_setting(
    'dark_theme_color_base',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#252932'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_theme_color_base',
      array(
        'label' => __( 'Dark Theme Color Meta', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_theme_color_base'
      )
    )
  );

  $manager->add_setting(
    'dark_secant',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#252932'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_secant',
      array(
        'label' => __( 'Dark Secant', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_secant'
      )
    )
  );

  $manager->add_setting(
    'dark_elevation_overlay',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#13151b'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_elevation_overlay',
      array(
        'label' => __( 'Dark Overlays', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_elevation_overlay'
      )
    )
  );

  $manager->add_setting(
    'dark_navigation_background_sticky',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#13151b'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_navigation_background_sticky',
      array(
        'label' => __( 'Dark Navigation', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_navigation_background_sticky'
      )
    )
  );

  $manager->add_setting(
    'dark_primary_400',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#f7dd88'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_primary_400',
      array(
        'label' => __( 'Dark Primary 400', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_primary_400'
      )
    )
  );

  $manager->add_setting(
    'dark_primary_500',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#f4d171'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_primary_500',
      array(
        'label' => __( 'Dark Primary 500', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_primary_500'
      )
    )
  );

  $manager->add_setting(
    'dark_primary_600',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#f1bb74'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_primary_600',
      array(
        'label' => __( 'Dark Primary 600', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_primary_600'
      )
    )
  );

  $manager->add_setting(
    'dark_warning_color',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#f66055'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_warning_color',
      array(
        'label' => __( 'Dark Warning', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_warning_color'
      )
    )
  );

  $manager->add_setting(
    'dark_bookmark_line_color',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#f4d171'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_bookmark_line_color',
      array(
        'label' => __( 'Dark Bookmark Line', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_bookmark_line_color'
      )
    )
  );

  $manager->add_setting(
    'dark_bookmark_color_alpha',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#767d8f'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_bookmark_color_alpha',
      array(
        'label' => __( 'Dark Bookmark Alpha', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_bookmark_color_alpha'
      )
    )
  );

  $manager->add_setting(
    'dark_bookmark_color_beta',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#e06552'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_bookmark_color_beta',
      array(
        'label' => __( 'Dark Bookmark Beta', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_bookmark_color_beta'
      )
    )
  );

  $manager->add_setting(
    'dark_bookmark_color_gamma',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#77BFA3'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_bookmark_color_gamma',
      array(
        'label' => __( 'Dark Bookmark Gamma', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_bookmark_color_gamma'
      )
    )
  );

  $manager->add_setting(
    'dark_bookmark_color_delta',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#3C91E6'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_bookmark_color_delta',
      array(
        'label' => __( 'Dark Bookmark Delta', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_bookmark_color_delta'
      )
    )
  );

  $manager->add_setting(
    'dark_ins_background',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#7ebb4e'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_ins_background',
      array(
        'label' => __( 'Dark &ltins>', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_ins_background'
      )
    )
  );

  $manager->add_setting(
    'dark_del_background',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#f66055'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_del_background',
      array(
        'label' => __( 'Dark &ltdel>', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_del_background'
      )
    )
  );

  $manager->add_setting(
    'dark_vote_down',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#f66055'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_vote_down',
      array(
        'label' => __( 'Dark Vote Down', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_vote_down'
      )
    )
  );

  $manager->add_setting(
    'dark_vote_up',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#7ebb4e'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_vote_up',
      array(
        'label' => __( 'Dark Vote Up', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_vote_up'
      )
    )
  );

  $manager->add_setting(
    'dark_badge_generic_background',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#3a3f4b'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_badge_generic_background',
      array(
        'label' => __( 'Dark Generic Badge', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_badge_generic_background'
      )
    )
  );

  $manager->add_setting(
    'dark_badge_moderator_background',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#4d628f'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_badge_moderator_background',
      array(
        'label' => __( 'Dark Moderator Badge', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_badge_moderator_background'
      )
    )
  );

  $manager->add_setting(
    'dark_badge_admin_background',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#505062'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_badge_admin_background',
      array(
        'label' => __( 'Dark Admin Badge', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_badge_admin_background'
      )
    )
  );

  $manager->add_setting(
    'dark_badge_author_background',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#505062'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_badge_author_background',
      array(
        'label' => __( 'Dark Author Badge', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_badge_author_background'
      )
    )
  );

  $manager->add_setting(
    'dark_badge_supporter_background',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#e64c66'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_badge_supporter_background',
      array(
        'label' => __( 'Dark Supporter Badge', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_badge_supporter_background'
      )
    )
  );

  $manager->add_setting(
    'dark_badge_override_background',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#3a3f4b'
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'dark_badge_override_background',
      array(
        'label' => __( 'Dark Override Badge', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'dark_badge_override_background'
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
  // Logo height (limited by header height)
  $manager->add_setting(
    'logo_height',
    array(
      'capability' => 'manage_options',
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
      'label' => __( 'Logo Height', 'fictioneer' ),
      'description' => __( 'Height of the logo in pixel, which will be limited by the header height. Default 210. Further adjustments require custom CSS.', 'fictioneer' ),
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
      'capability' => 'manage_options',
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
      'capability' => 'manage_options',
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
      'capability' => 'manage_options',
      'sanitize_callback' => 'absint',
      'default' => 13
    )
  );

  $manager->add_control(
    'site_tagline_font_size_min',
    array(
      'type' => 'number',
      'priority' => 41,
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
      'capability' => 'manage_options',
      'sanitize_callback' => 'absint',
      'default' => 18
    )
  );

  $manager->add_control(
    'site_tagline_font_size_max',
    array(
      'type' => 'number',
      'priority' => 42,
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

  // Header image fading start
  $manager->add_setting(
    'header_image_fading_start',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'absint',
      'default' => 0
    )
  );

  $manager->add_control(
    'header_image_fading_start',
    array(
      'type' => 'number',
      'priority' => 10,
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
      'capability' => 'manage_options',
      'sanitize_callback' => 'absint',
      'default' => 0
    )
  );

  $manager->add_control(
    'header_image_fading_breakpoint',
    array(
      'type' => 'number',
      'priority' => 10,
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

  // Clamp minimum for header image height
  $manager->add_setting(
    'header_image_height_min',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'absint',
      'default' => 210
    )
  );

  $manager->add_control(
    'header_image_height_min',
    array(
      'type' => 'number',
      'priority' => 10,
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
      'capability' => 'manage_options',
      'sanitize_callback' => 'absint',
      'default' => 480
    )
  );

  $manager->add_control(
    'header_image_height_max',
    array(
      'type' => 'number',
      'priority' => 10,
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
        'priority' => 10,
        'label' => __( 'Inset header image', 'fictioneer' ),
        'section' => 'header_image',
        'settings' => 'inset_header_image',
        'description' => __( 'Inset the header image with a limit of 1.5 times the site width. For smaller images that do not work on widescreens.', 'fictioneer' )
      )
    )
  );

  // Add layout section
  $manager->add_section(
    'layout',
    array(
      'title' => __( 'Layout', 'fictioneer' ),
      'priority' => '80'
    )
  );

  // Site width
  $manager->add_setting(
    'site_width',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'absint',
      'default' => 960
    )
  );

  $manager->add_control(
    'site_width',
    array(
      'type' => 'number',
      'priority' => 10,
      'section' => 'layout',
      'label' => __( 'Site Width', 'fictioneer' ),
      'description' => __( 'Maximum site width in pixels, should not be less than 896. Default 960.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => '960',
        'min' => 896,
        'style' => 'width: 80px'
      )
    )
  );

  // Page style
  $manager->add_setting(
    'page_style',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'default'
    )
  );

  $manager->add_control(
    'page_style',
    array(
      'type' => 'select',
      'priority' => 10,
      'section' => 'layout',
      'label' => __( 'Page Style', 'fictioneer' ),
      'description' => __( 'Choose the general style for your pages.', 'fictioneer' ),
      'choices' => array(
        'default' => _x( 'Default', 'Customizer page style option.', 'fictioneer' ),
        'borderless' => _x( 'Borderless', 'Customizer page style option.', 'fictioneer' ),
        'open' => _x( 'Open', 'Customizer page style option.', 'fictioneer' ),
        'worn-paper' => _x( 'Worn Paper (Polygon)', 'Customizer page style option.', 'fictioneer' )
      ),
    )
  );

  // Header style
  $manager->add_setting(
    'header_style',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'default'
    )
  );

  $manager->add_control(
    'header_style',
    array(
      'type' => 'select',
      'priority' => 10,
      'section' => 'layout',
      'label' => __( 'Header Style', 'fictioneer' ),
      'description' => __( 'Choose the style for your header. This may affect or disable other settings.', 'fictioneer' ),
      'choices' => array(
        'default' => _x( 'Default', 'Customizer header style option.', 'fictioneer' ),
        'top' => _x( 'Top', 'Customizer header style option.', 'fictioneer' ),
        'split' => _x( 'Split', 'Customizer header style option.', 'fictioneer' ),
        'overlay' => _x( 'Overlay', 'Customizer header style option.', 'fictioneer' ),
        'none' => _x( 'None', 'Customizer header style option.', 'fictioneer' )
      ),
    )
  );

  // Clamp minimum for header height
  $manager->add_setting(
    'header_height_min',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'absint',
      'default' => 190
    )
  );

  $manager->add_control(
    'header_height_min',
    array(
      'type' => 'number',
      'priority' => 10,
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
      'capability' => 'manage_options',
      'sanitize_callback' => 'absint',
      'default' => 380
    )
  );

  $manager->add_control(
    'header_height_max',
    array(
      'type' => 'number',
      'priority' => 10,
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
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'overflow'
    )
  );

  $manager->add_control(
    'mobile_nav_style',
    array(
      'type' => 'select',
      'priority' => 10,
      'section' => 'layout',
      'label' => __( 'Mobile Navigation Style', 'fictioneer' ),
      'description' => __( 'Choose the style for your mobile navigation.', 'fictioneer' ),
      'choices' => array(
        'overflow' => _x( 'Overflow', 'Customizer mobile navigation style option.', 'fictioneer' ),
        'collapse' => _x( 'Collapse', 'Customizer mobile navigation style option.', 'fictioneer' )
      )
    )
  );

  // Mobile menu style
  $manager->add_setting(
    'mobile_menu_style',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'minimize_to_right'
    )
  );

  $manager->add_control(
    'mobile_menu_style',
    array(
      'type' => 'select',
      'priority' => 10,
      'section' => 'layout',
      'label' => __( 'Mobile Menu Style', 'fictioneer' ),
      'description' => __( 'Choose the style for your mobile menu.', 'fictioneer' ),
      'choices' => array(
        'minimize_to_right' => _x( 'Minimize site to right', 'Customizer mobile menu style option.', 'fictioneer' ),
        'left_slide_in' => _x( 'Slide in from left', 'Customizer mobile menu style option.', 'fictioneer' )
      )
    )
  );

  // Card style
  $manager->add_setting(
    'card_style',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'default'
    )
  );

  $manager->add_control(
    'card_style',
    array(
      'type' => 'select',
      'priority' => 10,
      'section' => 'layout',
      'label' => __( 'Card Style', 'fictioneer' ),
      'description' => __( 'Choose the style for your cards.', 'fictioneer' ),
      'choices' => array(
        'default' => _x( 'Default', 'Customizer card style option.', 'fictioneer' ),
        'unfolded' => _x( 'Unfolded', 'Customizer card style option.', 'fictioneer' ),
        'combined' => _x( 'Combined', 'Customizer card style option.', 'fictioneer' )
      )
    )
  );

  // Card cover width modifier
  $manager->add_setting(
    'card_cover_width_mod',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'fictioneer_sanitize_float_field',
      'default' => '1.0'
    )
  );

  $manager->add_control(
    'card_cover_width_mod',
    array(
      'type' => 'text',
      'priority' => 10,
      'section' => 'layout',
      'label' => __( 'Card Cover Multiplier', 'fictioneer' ),
      'description' => __( 'Multiplier for the card cover width. Default 1.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => '1.0',
        'style' => 'width: 80px'
      )
    )
  );

  // Card grid column minimum width
  $manager->add_setting(
    'card_grid_column_min',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'absint',
      'default' => 308
    )
  );

  $manager->add_control(
    'card_grid_column_min',
    array(
      'type' => 'number',
      'priority' => 10,
      'section' => 'layout',
      'label' => __( 'Card Width', 'fictioneer' ),
      'description' => __( 'Minimum card width in pixels (still limited by space); affects card scale. Default 308.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => '308',
        'min' => 308,
        'style' => 'width: 80px'
      )
    )
  );

  // Content list item style
  $manager->add_setting(
    'content_list_style',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'default'
    )
  );

  $manager->add_control(
    'content_list_style',
    array(
      'type' => 'select',
      'priority' => 10,
      'section' => 'layout',
      'label' => __( 'Content List Style', 'fictioneer' ),
      'description' => __( 'Choose the style for your content lists.', 'fictioneer' ),
      'choices' => array(
        'default' => _x( 'Default', 'Customizer content list style option.', 'fictioneer' ),
        'full' => _x( 'Full', 'Customizer content list style option.', 'fictioneer' ),
        'lines' => _x( 'Lines', 'Customizer content list style option.', 'fictioneer' ),
        'free' => _x( 'Free', 'Customizer content list style option.', 'fictioneer' )
      )
    )
  );

  // Footer style
  $manager->add_setting(
    'footer_style',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'default'
    )
  );

  $manager->add_control(
    'footer_style',
    array(
      'type' => 'select',
      'priority' => 10,
      'section' => 'layout',
      'label' => __( 'Footer Style', 'fictioneer' ),
      'description' => __( 'Choose the style for your footer.', 'fictioneer' ),
      'choices' => array(
        'default' => _x( 'Default', 'Customizer footer style option.', 'fictioneer' ),
        'isolated' => _x( 'Isolated', 'Customizer footer style option.', 'fictioneer' )
      )
    )
  );

  // Custom layout toggle
  $manager->add_setting(
    'use_custom_layout',
    array(
      'capability' => 'manage_options',
      'default'=> ''
    )
  );

  $manager->add_control(
    'use_custom_layout',
    array(
      'type' => 'checkbox',
      'priority' => 10,
      'section' => 'layout',
      'label' => __( 'Use custom layout properties', 'fictioneer' )
    )
  );

  // Clamp minimum for vertical spacing
  $manager->add_setting(
    'vertical_spacing_min',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'absint',
      'default' => 24
    )
  );

  $manager->add_control(
    'vertical_spacing_min',
    array(
      'type' => 'number',
      'priority' => 10,
      'section' => 'layout',
      'label' => __( 'Vertical Spacing - Minimum', 'fictioneer' ),
      'description' => __( 'Minimum of the vertical spacing in pixels on the smallest viewport. Default 24.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => '24',
        'style' => 'width: 80px',
        'min' => 0
      )
    )
  );

  // Clamp maximum for vertical spacing
  $manager->add_setting(
    'vertical_spacing_max',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'absint',
      'default' => 48
    )
  );

  $manager->add_control(
    'vertical_spacing_max',
    array(
      'type' => 'number',
      'priority' => 10,
      'section' => 'layout',
      'label' => __( 'Vertical Spacing - Maximum', 'fictioneer' ),
      'description' => __( 'Maximum of the vertical spacing in pixels on [site-width] viewports. Default 48.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => '48',
        'style' => 'width: 80px',
        'min' => 0
      )
    )
  );

  // Clamp minimum for horizontal spacing
  $manager->add_setting(
    'horizontal_spacing_min',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'absint',
      'default' => 20
    )
  );

  $manager->add_control(
    'horizontal_spacing_min',
    array(
      'type' => 'number',
      'priority' => 10,
      'section' => 'layout',
      'label' => __( 'Horizontal Spacing - Minimum', 'fictioneer' ),
      'description' => __( 'Minimum of the horizontal spacing in pixels on 480px viewports. Default 20.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => '20',
        'style' => 'width: 80px',
        'min' => 0
      )
    )
  );

  // Clamp maximum for horizontal spacing
  $manager->add_setting(
    'horizontal_spacing_max',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'absint',
      'default' => 80
    )
  );

  $manager->add_control(
    'horizontal_spacing_max',
    array(
      'type' => 'number',
      'priority' => 10,
      'section' => 'layout',
      'label' => __( 'Horizontal Spacing - Maximum', 'fictioneer' ),
      'description' => __( 'Maximum of the horizontal spacing in pixels on [site-width] viewport. Default 80.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => '80',
        'style' => 'width: 80px',
        'min' => 0
      )
    )
  );

  // Clamp minimum for horizontal spacing
  $manager->add_setting(
    'horizontal_spacing_small_min',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'absint',
      'default' => 10
    )
  );

  $manager->add_control(
    'horizontal_spacing_small_min',
    array(
      'type' => 'number',
      'priority' => 10,
      'section' => 'layout',
      'label' => __( 'Small Horizontal Spacing - Minimum', 'fictioneer' ),
      'description' => __( 'Minimum of the small horizontal spacing in pixels on 320px viewports. Default 10.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => '10',
        'style' => 'width: 80px',
        'min' => 0
      )
    )
  );

  // Clamp maximum for horizontal spacing
  $manager->add_setting(
    'horizontal_spacing_small_max',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'absint',
      'default' => 20
    )
  );

  $manager->add_control(
    'horizontal_spacing_small_max',
    array(
      'type' => 'number',
      'priority' => 10,
      'section' => 'layout',
      'label' => __( 'Small Horizontal Spacing - Maximum', 'fictioneer' ),
      'description' => __( 'Maximum of the small horizontal spacing in pixels below 375px viewports. Default 20.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => '20',
        'style' => 'width: 80px',
        'min' => 0
      )
    )
  );

  // Large border radius
  $manager->add_setting(
    'large_border_radius',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'absint',
      'default' => 4
    )
  );

  $manager->add_control(
    'large_border_radius',
    array(
      'type' => 'number',
      'priority' => 10,
      'section' => 'layout',
      'label' => __( 'Large Border Radius', 'fictioneer' ),
      'description' => __( 'Border radius of large containers, such as the main content section. Default 4.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => '4',
        'style' => 'width: 80px',
        'min' => 0
      )
    )
  );

  // Small border radius
  $manager->add_setting(
    'small_border_radius',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'absint',
      'default' => 2
    )
  );

  $manager->add_control(
    'small_border_radius',
    array(
      'type' => 'number',
      'priority' => 10,
      'section' => 'layout',
      'label' => __( 'Small Border Radius', 'fictioneer' ),
      'description' => __( 'Border radius of small containers, such as story cards and inputs. Default 2.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => '2',
        'style' => 'width: 80px',
        'min' => 0
      )
    )
  );
}

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
      'capability' => 'manage_options',
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
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'Lato'
    )
  );

  $manager->add_control(
    'secondary_font_family_value',
    array(
      'type' => 'select',
      'priority' => 10,
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
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'Open Sans'
    )
  );

  $manager->add_control(
    'heading_font_family_value',
    array(
      'type' => 'select',
      'priority' => 10,
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
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'default'
    )
  );

  $manager->add_control(
    'site_title_font_family_value',
    array(
      'type' => 'select',
      'priority' => 10,
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
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'default'
    )
  );

  $manager->add_control(
    'nav_item_font_family_value',
    array(
      'type' => 'select',
      'priority' => 10,
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
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'default'
    )
  );

  $manager->add_control(
    'story_title_font_family_value',
    array(
      'type' => 'select',
      'priority' => 10,
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
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'default'
    )
  );

  $manager->add_control(
    'chapter_title_font_family_value',
    array(
      'type' => 'select',
      'priority' => 10,
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
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'default'
    )
  );

  $manager->add_control(
    'chapter_list_title_font_family_value',
    array(
      'type' => 'select',
      'priority' => 10,
      'section' => 'fictioneer_fonts',
      'label' => __( 'Chapter List Title Font', 'fictioneer' ),
      'description' => __( 'Used for the title row in chapter lists. Defaults to primary font.', 'fictioneer' ),
      'choices' => $font_sub_options
    )
  );

  // Card title font
  $manager->add_setting(
    'card_title_font_family_value',
    array(
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'default'
    )
  );

  $manager->add_control(
    'card_title_font_family_value',
    array(
      'type' => 'select',
      'priority' => 10,
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
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'default'
    )
  );

  $manager->add_control(
    'card_body_font_family_value',
    array(
      'type' => 'select',
      'priority' => 10,
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
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'default'
    )
  );

  $manager->add_control(
    'card_list_link_font_family_value',
    array(
      'type' => 'select',
      'priority' => 10,
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
      'capability' => 'manage_options',
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'adjusted'
    )
  );

  $manager->add_control(
    'dark_mode_font_weight',
    array(
      'type' => 'select',
      'priority' => 10,
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
// CUSTOMIZER SETTINGS
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

  // Layout
  fictioneer_add_layout_customizer_settings( $manager );

  // Fonts
  fictioneer_add_fonts_customizer_settings( $manager );

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

?>
