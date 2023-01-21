<?php

// =============================================================================
// HEX TO RGB
// =============================================================================

if ( ! function_exists( 'fictioneer_hex_to_rgb' ) ) {
  /**
   * Convert hex colors to RGB
   *
   * @license MIT
   * @author Simon Waldherr https://github.com/SimonWaldherr
   *
   * @since Fictioneer 4.7
   * @link https://github.com/SimonWaldherr/ColorConverter.php
   *
   * @param  string $input The to be converted hex (six digits).
   * @return array  RGB values as array.
   */

  function fictioneer_hex_to_rgb( $input ) {
    if ( substr( trim( $input ), 0, 1 ) === '#' ) {
      $input = substr( $input, 1 );
    }

    if ( ( strlen( $input ) < 2) || ( strlen( $input ) > 6 ) ) {
      return false;
    }

    $values = str_split( $input );

    if ( strlen( $input ) === 2 ) {
      $r = intval( $values[0] . $values[1], 16 );
      $g = $r;
      $b = $r;
    } else if ( strlen( $input ) === 3 ) {
      $r = intval( $values[0], 16 );
      $g = intval( $values[1], 16 );
      $b = intval( $values[2], 16 );
    } else if ( strlen( $input ) === 6 ) {
      $r = intval( $values[0] . $values[1], 16 );
      $g = intval( $values[2] . $values[3], 16 );
      $b = intval( $values[4] . $values[5], 16 );
    } else {
      return false;
    }

    return array( $r, $g, $b );
  }
}

// =============================================================================
// RGB TO HSL
// =============================================================================

if ( ! function_exists( 'fictioneer_rgb_to_hsl' ) ) {
  /**
   * Convert RGB colors to HSL
   *
   * @license MIT
   * @author Simon Waldherr https://github.com/SimonWaldherr
   *
   * @since Fictioneer 4.7
   * @link https://github.com/SimonWaldherr/ColorConverter.php
   *
   * @param  array  $input     The to be converted RGB.
   * @param  int    $precision Rounding precision. Default 0.
   * @return array  HSL values as array.
   */

  function fictioneer_rgb_to_hsl( $input, $precision = 0 ) {
    $r = max( min( intval( $input[0], 10 ) / 255, 1 ), 0 );
    $g = max( min( intval( $input[1], 10 ) / 255, 1 ), 0 );
    $b = max( min( intval( $input[2], 10 ) / 255, 1 ), 0 );
    $max = max( $r, $g, $b );
    $min = min( $r, $g, $b );
    $l = ( $max + $min ) / 2;

    if ( $max !== $min ) {
      $d = $max - $min;
      $s = $l > 0.5 ? $d / ( 2 - $max - $min ) : $d / ( $max + $min );
      if ( $max === $r ) {
        $h = ( $g - $b ) / $d + ( $g < $b ? 6 : 0 );
      } else if ( $max === $g ) {
        $h = ( $b - $r ) / $d + 2;
      } else {
        $h = ( $r - $g ) / $d + 4;
      }
      $h = $h / 6;
    } else {
      $h = $s = 0;
    }
    return [round( $h * 360, $precision ), round( $s * 100, $precision ), round( $l * 100, $precision )];
  }
}

// =============================================================================
// MINIFY FOR CSS
// =============================================================================

if ( ! function_exists( 'fictioneer_minify_css' ) ) {
  /**
   * Minify CSS.
   *
   * @license CC BY-SA 4.0
   * @author Qtax https://stackoverflow.com/users/107152/qtax
   * @author lots0logs https://stackoverflow.com/users/2639936/lots0logs
   *
   * @since Fictioneer 4.7
   * @link https://stackoverflow.com/a/15195752/17140970
   * @link https://stackoverflow.com/a/44350195/17140970
   *
   * @param  string $string The to be minified CSS string.
   * @return string The minified CSS string.
   */

  function fictioneer_minify_css( $string = '' ) {
    $comments = <<<'EOS'
  (?sx)
      # don't change anything inside of quotes
      ( "(?:[^"\\]++|\\.)*+" | '(?:[^'\\]++|\\.)*+' )
  |
      # comments
      /\* (?> .*? \*/ )
  EOS;

    $everything_else = <<<'EOS'
  (?six)
      # don't change anything inside of quotes
      ( "(?:[^"\\]++|\\.)*+" | '(?:[^'\\]++|\\.)*+' )
  |
      # spaces before and after ; and }
      \s*+ ; \s*+ ( } ) \s*+
  |
      # all spaces around meta chars/operators (excluding + and -)
      \s*+ ( [*$~^|]?+= | [{};,>~] | !important\b ) \s*+
  |
      # all spaces around + and - (in selectors only!)
      \s*([+-])\s*(?=[^}]*{)
  |
      # spaces right of ( [ :
      ( [[(:] ) \s++
  |
      # spaces left of ) ]
      \s++ ( [])] )
  |
      # spaces left (and right) of : (but not in selectors)!
      \s+(:)(?![^\}]*\{)
  |
      # spaces at beginning/end of string
      ^ \s++ | \s++ \z
  |
      # double spaces to single
      (\s)\s+
  EOS;

    $search_patterns  = array( "%{$comments}%", "%{$everything_else}%" );
    $replace_patterns = array( '$1', '$1$2$3$4$5$6$7$8' );

    return preg_replace( $search_patterns, $replace_patterns, $string );
  }
}

// =============================================================================
// GENERATE CSS CLAMP
// =============================================================================

if ( ! function_exists( 'fictioneer_get_css_clamp' ) ) {
  /**
   * Generate a high-precision CSS clamp.
   *
   * @since Fictioneer 4.7
   *
   * @param  int    $min  The minimum value.
   * @param  int    $max  The maximum value.
   * @param  int    $wmin The minimum viewport value.
   * @param  int    $wmax The maximum viewport value.
   * @param  string $unit The relative clamp unit. Default 'vw'.
   *
   * @return string The calculated clamp.
   */

  function fictioneer_get_css_clamp( $min, $max, $wmin, $wmax, $unit = 'vw' ) {
    $vw = ( $min - $max ) / ( ( $wmin / 100 ) - ( $wmax / 100 ) );
    $offset = $min - $vw * ( $wmin / 100 );

    return "clamp({$min}px, {$vw}{$unit} + ({$offset}px), {$max}px)";
  }
}

// =============================================================================
// GENERATE CSS CUSTOM PROPERTY HSL CODE
// =============================================================================

if ( ! function_exists( 'fictioneer_hsl_code' ) ) {
  /**
   * Convert a hex color to a Fictioneer HSL code.
   *
   * Example: #fcfcfd =>
   * hsl(calc(240deg + var(--hue-rotate)) calc(20% * var(--saturation)) clamp(49.5%, 99% * var(--darken), 99.5%))
   *
   * @since Fictioneer 4.7
   *
   * @param  string $hex    The color as hex.
   * @param  string $output Switch output style. Default 'default'.
   * @return string The converted color.
   */

  function fictioneer_hsl_code( $hex, $output = 'default' ) {
    $hsl_array = fictioneer_rgb_to_hsl( fictioneer_hex_to_rgb( $hex ), 2 );

    if ( $output == 'values' ) return "$hsl_array[0] $hsl_array[1] $hsl_array[2]";

    $deg = 'calc(' . $hsl_array[0] . 'deg + var(--hue-rotate))';
    $saturation = 'calc(' . $hsl_array[1] . '% * var(--saturation))';
    $min = max( 0, $hsl_array[2] * 0.5 );
    $max = $hsl_array[2] + (100 - $hsl_array[2]) / 2;
    $lightness = 'clamp('. $min . '%, ' . $hsl_array[2] . '% * var(--darken), ' . $max . '%)';

    if ( $output == 'free' ) return "$deg $saturation $lightness";

    return "hsl($deg $saturation $lightness)";
  }
}

// =============================================================================
// GENERATE CSS CUSTOM PROPERTY HSL FONT CODE
// =============================================================================

if ( ! function_exists( 'fictioneer_hsl_font_code' ) ) {
  /**
   * Convert a hex color to a Fictioneer HSL font code.
   *
   * This is a reduced variant of the regular HSL font code.
   *
   * @since Fictioneer 4.7
   * @see fictioneer_hsl_code( $hex, $output )
   *
   * @param  string $hex The color as hex.
   * @return string The converted color.
   */

  function fictioneer_hsl_font_code( $hex ) {
    $hsl_array = fictioneer_rgb_to_hsl( fictioneer_hex_to_rgb( $hex ), 2 );

    $deg = 'calc(' . $hsl_array[0] . 'deg + var(--hue-rotate))';
    $saturation = 'calc(' . $hsl_array[1] . '% * (var(--font-saturation) + var(--saturation) - 1))';
    $lightness = $hsl_array[2] . '%';

    return "hsl($deg $saturation $lightness)";
  }
}

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
 * @since 4.7
 *
 * @param WP_Customize_Manager $manager The customizer instance.
 */

function fictioneer_add_light_mode_customizer_settings( $manager ) {
  $manager->add_section(
    'light_mode_colors',
    array(
      'title' => __( 'Light Mode Colors', 'fictioneer' ),
      'description' => __( 'Color scheme for the light mode. If you want to add custom CSS, this needs to be under <code>:root[data-mode=light]</code>.', 'fictioneer' ),
      'priority' => '84'
    )
  );

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
			'default'=> ''
		)
	);

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'use_custom_light_mode',
      array(
        'type' => 'checkbox',
        'label' => __( 'Use custom theme colors', 'fictioneer' ),
        'section' => 'light_mode_colors',
        'settings' => 'use_custom_light_mode'
      )
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
      'default' => '#242c38'
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
 * @since 4.7
 *
 * @param WP_Customize_Manager $manager The customizer instance.
 */

function fictioneer_add_dark_mode_customizer_settings( $manager ) {
  $manager->add_section(
    'dark_mode_colors',
    array(
      'title' => __( 'Dark Mode Colors', 'fictioneer' ),
      'description' => __( 'Color scheme for the dark mode. <b>Warning:</b> Looking at light text on dark background for a prolonged period of time causes immense eye strain! Try to keep the main text contrast between 5:1 and 6:1 to alleviate the issue.', 'fictioneer' ),
      'priority' => '83'
    )
  );

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

  $manager->add_setting(
    'use_custom_dark_mode',
    array(
      'default'=> ''
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'use_custom_dark_mode',
      array(
        'type' => 'checkbox',
        'label' => __( 'Use custom theme colors', 'fictioneer' ),
        'section' => 'dark_mode_colors',
        'settings' => 'use_custom_dark_mode'
      )
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
 * @since 4.7
 *
 * @param WP_Customize_Manager $manager The customizer instance.
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
      'description' => __( 'Minimum font size in pixel of the site title on 320px viewports. Default 32.', 'fictioneer' ),
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
      'description' => __( 'Maximum font size in pixel of the site title on [site-width] viewports. Default 60.', 'fictioneer' ),
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
      'description' => __( 'Minimum font size in pixel of the tagline on 320px viewports. Default 13.', 'fictioneer' ),
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
      'description' => __( 'Maximum font size in pixel of the tagline on [site-width] viewports. Default 18.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => '18',
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
        'priority' => 50,
        'label' => __( 'Inset header image', 'fictioneer' ),
        'section' => 'header_image',
        'settings' => 'inset_header_image',
        'description' => __( 'Instead of having the header image go from side to side, you can limit it to 1.5 times the site width. Good for smaller images that do not work on widescreens.', 'fictioneer' )
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
      'priority' => 40,
      'section' => 'header_image',
      'label' => __( 'Minimum Height', 'fictioneer' ),
      'description' => __( 'Minimum height in pixel of the header image on 320px viewports. Default 210.', 'fictioneer' ),
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
      'priority' => 41,
      'section' => 'header_image',
      'label' => __( 'Maximum Height', 'fictioneer' ),
      'description' => __( 'Maximum height in pixel of the header image on [site-width] viewports. Default 480.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => '480',
        'style' => 'width: 80px',
        'min' => 0
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
        'section' => 'layout',
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
        'section' => 'layout',
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
        'section' => 'layout',
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
      'description' => __( 'Maximum site width in pixel, should not be less than 896. Default 960.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => '960',
        'min' => 896,
        'style' => 'width: 80px'
      )
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
      'description' => __( 'Minimum height of the header section in pixel on the smallest viewport. Default 190.', 'fictioneer' ),
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
      'description' => __( 'Maximum height of the header section in pixel on the largest viewport. Default 380.', 'fictioneer' ),
      'input_attrs' => array(
        'placeholder' => '380',
        'style' => 'width: 80px',
        'min' => 0
      )
    )
  );

  $manager->add_setting(
    'use_custom_layout',
    array(
      'default'=> ''
    )
  );

  $manager->add_control(
    new WP_Customize_Color_Control(
      $manager,
      'use_custom_layout',
      array(
        'type' => 'checkbox',
        'label' => __( 'Use custom layout properties', 'fictioneer' ),
        'section' => 'layout',
        'settings' => 'use_custom_layout'
      )
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
      'description' => __( 'Minimum of the vertical spacing in pixel on the smallest viewport. Default 24.', 'fictioneer' ),
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
      'description' => __( 'Maximum of the vertical spacing in pixel on [site-width] viewports. Default 48.', 'fictioneer' ),
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
      'description' => __( 'Minimum of the horizontal spacing in pixel on 480px viewports. Default 20.', 'fictioneer' ),
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
      'description' => __( 'Maximum of the horizontal spacing in pixel on [site-width] viewport. Default 80.', 'fictioneer' ),
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
      'description' => __( 'Minimum of the small horizontal spacing in pixel on 320px viewports. Default 10.', 'fictioneer' ),
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
      'description' => __( 'Maximum of the small horizontal spacing in pixel below 375px viewports. Default 20.', 'fictioneer' ),
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

// =============================================================================
// CUSTOMIZER SETTINGS
// =============================================================================

/**
 * Set up the theme customizer
 *
 * @since 3.0
 * @since 4.7 Expanded settings to customize color scheme.
 *
 * @param WP_Customize_Manager $manager The customizer instance.
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

// =============================================================================
// WATCH FOR CUSTOMIZER UPDATES
// =============================================================================

/**
 * Watches for customizer updates to delete Transients
 *
 * @since Fictioneer 4.7
 */

function fictioneer_watch_for_customer_updates() {
  delete_transient( 'fictioneer_customized_light_mode' );
  delete_transient( 'fictioneer_customized_dark_mode' );
  delete_transient( 'fictioneer_customized_layout' );
}
add_action( 'customize_save_after', 'fictioneer_watch_for_customer_updates' );

// =============================================================================
// ADD CUSTOMIZER LIGHT MODE CSS
// =============================================================================

if ( ! function_exists( 'fictioneer_add_customized_light_mode_css' ) ) {
  /**
   * Add customized light mode CSS
   *
   * @since Fictioneer 4.7
   */

  function fictioneer_add_customized_light_mode_css() {
    // Always include these...
    $light_header_css = ":root[data-mode=light] .header__title {
      --site-title-heading-color: " . get_theme_mod( 'light_header_title_color', '#fcfcfd' ) . ";
      --site-title-tagline-color: " . get_theme_mod( 'light_header_tagline_color', '#f3f5f7' ) . ";
    }";
    wp_add_inline_style( 'fictioneer-application', $light_header_css );

    // ...but abort if custom light mode colors are not enabled
    if ( ! get_theme_mod( 'use_custom_light_mode', false ) ) return;

    // Look for pre-compiled CSS in Transients (except when in Customizer)
    if (
      ! is_customize_preview() &&
      $last_update = get_transient( 'fictioneer_customized_light_mode' )
    ) {
      wp_add_inline_style( 'fictioneer-application', $last_update );
      return;
    }

    // Build CSS
    $light_css = ":root[data-mode=light] {
      --bg-200-free: " . fictioneer_hsl_code( get_theme_mod( 'light_bg_200', '#e5e7eb' ), 'free' ) . ";
      --bg-1000-free: " . fictioneer_hsl_code( get_theme_mod( 'light_bg_1000', '#111827' ), 'free' ) . ";
      --bg-10: " . fictioneer_hsl_code( get_theme_mod( 'light_bg_10', '#fcfcfd' ) ) . ";
      --bg-50: " . fictioneer_hsl_code( get_theme_mod( 'light_bg_50', '#f9fafb' ) ) . ";
      --bg-100: " . fictioneer_hsl_code( get_theme_mod( 'light_bg_100', '#f3f4f6' ) ) . ";
      --bg-300: " . fictioneer_hsl_code( get_theme_mod( 'light_bg_300', '#d1d5db' ) ) . ";
      --bg-400: " . fictioneer_hsl_code( get_theme_mod( 'light_bg_400', '#9ca3b0' ) ) . ";
      --bg-500: " . fictioneer_hsl_code( get_theme_mod( 'light_bg_500', '#6b7280' ) ) . ";
      --bg-600: " . fictioneer_hsl_code( get_theme_mod( 'light_bg_600', '#4b5563' ) ) . ";
      --bg-700: " . fictioneer_hsl_code( get_theme_mod( 'light_bg_700', '#384252' ) ) . ";
      --bg-800: " . fictioneer_hsl_code( get_theme_mod( 'light_bg_800', '#1f2937' ) ) . ";
      --bg-900: " . fictioneer_hsl_code( get_theme_mod( 'light_bg_900', '#111827' ) ) . ";
      --theme-color-base: " . fictioneer_hsl_code( get_theme_mod( 'light_theme_color_base', '#f3f4f6' ), 'values' ) . ";
      --secant: " . fictioneer_hsl_code( get_theme_mod( 'light_secant', '#d1d5db' ) ) . ";
      --e-overlay: " . fictioneer_hsl_code( get_theme_mod( 'light_elevation_overlay', '#191b1f' ) ) . ";
      --navigation-background-sticky: " . fictioneer_hsl_code( get_theme_mod( 'light_navigation_background_sticky', '#fcfcfd' ) ) . ";
      --primary-400: " . get_theme_mod( 'light_primary_400', '#4287f5' ) . ";
      --primary-500: " . get_theme_mod( 'light_primary_500', '#3c83f6' ) . ";
      --primary-600: " . get_theme_mod( 'light_primary_600', '#1e3fae' ) . ";
      --warning: " . get_theme_mod( 'light_warning_color', '#eb5247' ) . ";
      --bookmark-color-alpha: " . get_theme_mod( 'light_bookmark_color_alpha', '#9ca3b0' ) . ";
      --bookmark-color-beta: " . get_theme_mod( 'light_bookmark_color_beta', '#f59e0b' ) . ";
      --bookmark-color-gamma: " . get_theme_mod( 'light_bookmark_color_gamma', '#77bfa3' ) . ";
      --bookmark-color-delta: " . get_theme_mod( 'light_bookmark_color_delta', '#dd5960' ) . ";
      --bookmark-line: " . get_theme_mod( 'light_bookmark_line_color', '#3c83f6' ) . ";
      --ins-background: " . get_theme_mod( 'light_ins_background', '#7ec945' ) . ";
      --del-background: " . get_theme_mod( 'light_del_background', '#e96c63' ) . ";
      --vote-down: " . get_theme_mod( 'light_vote_down', '#dc2626' ) . ";
      --vote-up: " . get_theme_mod( 'light_vote_up', '#16a34a' ) . ";
      --badge-generic-background: " . get_theme_mod( 'light_badge_generic_background', '#9ca3b0' ) . ";
      --badge-moderator-background: " . get_theme_mod( 'light_badge_moderator_background', '#5369ac' ) . ";
      --badge-admin-background: " . get_theme_mod( 'light_badge_admin_background', '#384252' ) . ";
      --badge-author-background: " . get_theme_mod( 'light_badge_author_background', '#384252' ) . ";
      --badge-supporter-background: " . get_theme_mod( 'light_badge_supporter_background', '#ed5e76' ) . ";
      --badge-override-background: " . get_theme_mod( 'light_badge_override_background', '#9ca3b0' ) . ";
    }
    :root[data-mode=light],
    :root[data-mode=light] .chapter-formatting {
      --fg-100: " . fictioneer_hsl_font_code( get_theme_mod( 'light_fg_100', '#010204' ) ) . ";
      --fg-200: " . fictioneer_hsl_font_code( get_theme_mod( 'light_fg_200', '#04060b' ) ) . ";
      --fg-300: " . fictioneer_hsl_font_code( get_theme_mod( 'light_fg_300', '#0a0f1a' ) ) . ";
      --fg-400: " . fictioneer_hsl_font_code( get_theme_mod( 'light_fg_400', '#111827' ) ) . ";
      --fg-500: " . fictioneer_hsl_font_code( get_theme_mod( 'light_fg_500', '#1f2937' ) ) . ";
      --fg-600: " . fictioneer_hsl_font_code( get_theme_mod( 'light_fg_600', '#384252' ) ) . ";
      --fg-700: " . fictioneer_hsl_font_code( get_theme_mod( 'light_fg_700', '#4b5563' ) ) . ";
      --fg-800: " . fictioneer_hsl_font_code( get_theme_mod( 'light_fg_800', '#6b7280' ) ) . ";
      --fg-900: " . fictioneer_hsl_font_code( get_theme_mod( 'light_fg_900', '#9ca3b0' ) ) . ";
      --fg-1000: " . fictioneer_hsl_font_code( get_theme_mod( 'light_fg_1000', '#f3f5f7' ) ) . ";
      --fg-tinted: " . fictioneer_hsl_font_code( get_theme_mod( 'light_fg_tinted', '#2b3546' ) ) . ";
    }";

    $light_css = fictioneer_minify_css( $light_css );

    // Remember CSS and add it to site
    if ( ! is_customize_preview() ) set_transient( 'fictioneer_customized_light_mode', $light_css );
    wp_add_inline_style( 'fictioneer-application', $light_css );
  }
}

// =============================================================================
// ADD CUSTOMIZER DARK MODE CSS
// =============================================================================

if ( ! function_exists( 'fictioneer_add_customized_dark_mode_css' ) ) {
  /**
   * Add customized dark mode CSS
   *
   * @since Fictioneer 4.7
   */

  function fictioneer_add_customized_dark_mode_css() {
    // Always include these...
    $dark_header_css = ".header__title {
      --site-title-heading-color: " . get_theme_mod( 'dark_header_title_color', '#eaeef6' ) . ";
      --site-title-tagline-color: " . get_theme_mod( 'dark_header_tagline_color', '#eaeef6' ) . ";
    }";
    wp_add_inline_style( 'fictioneer-application', $dark_header_css );

    // ...but abort if custom light mode colors are not enabled
    if ( ! get_theme_mod( 'use_custom_dark_mode', false ) ) return;

    // Look for pre-compiled CSS in Transients (except when in Customizer)
    if (
      ! is_customize_preview() &&
      $last_update = get_transient( 'fictioneer_customized_dark_mode' )
    ) {
      wp_add_inline_style( 'fictioneer-application', $last_update );
      return;
    }

    // Build CSS
    $dark_css = ":root, :root[data-theme=base] {
      --bg-900-free: " . fictioneer_hsl_code( get_theme_mod( 'dark_bg_900', '#252932' ), 'free' ) . ";
      --bg-1000-free: " . fictioneer_hsl_code( get_theme_mod( 'dark_bg_1000', '#121317' ), 'free' ) . ";
      --bg-50: " . fictioneer_hsl_code( get_theme_mod( 'dark_bg_50', '#4b505d' ) ) . ";
      --bg-100: " . fictioneer_hsl_code( get_theme_mod( 'dark_bg_100', '#464c58' ) ) . ";
      --bg-200: " . fictioneer_hsl_code( get_theme_mod( 'dark_bg_200', '#434956' ) ) . ";
      --bg-300: " . fictioneer_hsl_code( get_theme_mod( 'dark_bg_300', '#414653' ) ) . ";
      --bg-400: " . fictioneer_hsl_code( get_theme_mod( 'dark_bg_400', '#3e4451' ) ) . ";
      --bg-500: " . fictioneer_hsl_code( get_theme_mod( 'dark_bg_500', '#3c414e' ) ) . ";
      --bg-600: " . fictioneer_hsl_code( get_theme_mod( 'dark_bg_600', '#373c49' ) ) . ";
      --bg-700: " . fictioneer_hsl_code( get_theme_mod( 'dark_bg_700', '#323743' ) ) . ";
      --bg-800: " . fictioneer_hsl_code( get_theme_mod( 'dark_bg_800', '#2b303b' ) ) . ";
      --theme-color-base: " . fictioneer_hsl_code( get_theme_mod( 'dark_theme_color_base', '#252932' ), 'values' ) . ";
      --secant: " . fictioneer_hsl_code( get_theme_mod( 'dark_secant', '#252932' ) ) . ";
      --e-overlay: " . fictioneer_hsl_code( get_theme_mod( 'dark_elevation_overlay', '#121317' ) ) . ";
      --navigation-background-sticky: " . fictioneer_hsl_code( get_theme_mod( 'dark_navigation_background_sticky', '#121317' ) ) . ";
      --primary-400: " . get_theme_mod( 'dark_primary_400', '#f7dd88' ) . ";
      --primary-500: " . get_theme_mod( 'dark_primary_500', '#f4d171' ) . ";
      --primary-600: " . get_theme_mod( 'dark_primary_600', '#f1bb74' ) . ";
      --warning: " . get_theme_mod( 'dark_warning_color', '#f66055' ) . ";
      --bookmark-color-alpha: " . get_theme_mod( 'dark_bookmark_color_alpha', '#767d8f' ) . ";
      --bookmark-color-beta: " . get_theme_mod( 'dark_bookmark_color_beta', '#e06552' ) . ";
      --bookmark-color-gamma: " . get_theme_mod( 'dark_bookmark_color_gamma', '#77BFA3' ) . ";
      --bookmark-color-delta: " . get_theme_mod( 'dark_bookmark_color_delta', '#3C91E6' ) . ";
      --bookmark-line: " . get_theme_mod( 'dark_bookmark_line_color', '#f4d171' ) . ";
      --ins-background: " . get_theme_mod( 'dark_ins_background', '#7ebb4e' ) . ";
      --del-background: " . get_theme_mod( 'dark_del_background', '#f66055' ) . ";
      --vote-down: " . get_theme_mod( 'dark_vote_down', '#f66055' ) . ";
      --vote-up: " . get_theme_mod( 'dark_vote_up', '#7ebb4e' ) . ";
      --badge-generic-background: " . get_theme_mod( 'dark_badge_generic_background', '#3a3f4b' ) . ";
      --badge-moderator-background: " . get_theme_mod( 'dark_badge_moderator_background', '#4d628f' ) . ";
      --badge-admin-background: " . get_theme_mod( 'dark_badge_admin_background', '#505062' ) . ";
      --badge-author-background: " . get_theme_mod( 'dark_badge_author_background', '#505062' ) . ";
      --badge-supporter-background: " . get_theme_mod( 'dark_badge_supporter_background', '#e64c66' ) . ";
      --badge-override-background: " . get_theme_mod( 'dark_badge_override_background', '#3a3f4b' ) . ";
    }
    :root,
    :root[data-theme=base],
    :root .chapter-formatting,
    :root[data-theme=base] .chapter-formatting {
      --fg-100: " . fictioneer_hsl_font_code( get_theme_mod( 'dark_fg_100', '#dddfe3' ) ) . ";
      --fg-200: " . fictioneer_hsl_font_code( get_theme_mod( 'dark_fg_200', '#d2d4db' ) ) . ";
      --fg-300: " . fictioneer_hsl_font_code( get_theme_mod( 'dark_fg_300', '#c4c7cf' ) ) . ";
      --fg-400: " . fictioneer_hsl_font_code( get_theme_mod( 'dark_fg_400', '#b4bac5' ) ) . ";
      --fg-500: " . fictioneer_hsl_font_code( get_theme_mod( 'dark_fg_500', '#a5acbb' ) ) . ";
      --fg-600: " . fictioneer_hsl_font_code( get_theme_mod( 'dark_fg_600', '#9aa1b1' ) ) . ";
      --fg-700: " . fictioneer_hsl_font_code( get_theme_mod( 'dark_fg_700', '#929aaa' ) ) . ";
      --fg-800: " . fictioneer_hsl_font_code( get_theme_mod( 'dark_fg_800', '#9298a5' ) ) . ";
      --fg-900: " . fictioneer_hsl_font_code( get_theme_mod( 'dark_fg_900', '#787f91' ) ) . ";
      --fg-1000: " . fictioneer_hsl_font_code( get_theme_mod( 'dark_fg_1000', '#121416' ) ) . ";
      --fg-tinted: " . fictioneer_hsl_font_code( get_theme_mod( 'dark_fg_tinted', '#a4a9b7' ) ) . ";
    }";

    $dark_css = fictioneer_minify_css( $dark_css );

    // Remember CSS and add it to site
    if ( ! is_customize_preview() ) set_transient( 'fictioneer_customized_dark_mode', $dark_css );
    wp_add_inline_style( 'fictioneer-application', $dark_css );
  }
}

// =============================================================================
// ADD CUSTOMIZER LAYOUT CSS
// =============================================================================

if ( ! function_exists( 'fictioneer_add_customized_layout_css' ) ) {
  /**
   * Add customized layout CSS
   *
   * @since Fictioneer 4.7
   */

  function fictioneer_add_customized_layout_css() {
    // Look for pre-compiled CSS in Transients (except when in Customizer)
    $last_update = get_transient( 'fictioneer_customized_layout' );

    if ( ! is_customize_preview() && $last_update ) {
      wp_add_inline_style( 'fictioneer-application', $last_update );
      return;
    }

    // Setup
    $hue_offset = (int) get_theme_mod( 'hue_offset', 0 );
    $saturation_offset = (int) get_theme_mod( 'saturation_offset', 0 );
    $lightness_offset = (int) get_theme_mod( 'lightness_offset', 0 );
    $site_width = (int) get_theme_mod( 'site_width', 960 );
    $logo_height = (int) get_theme_mod( 'logo_height', 210 );
    $title_min = (int) get_theme_mod( 'site_title_font_size_min', 32 );
    $title_max = (int) get_theme_mod( 'site_title_font_size_max', 60 );
    $tagline_min = (int) get_theme_mod( 'site_tagline_font_size_min', 13 );
    $tagline_max = (int) get_theme_mod( 'site_tagline_font_size_max', 18 );
    $header_image_min = (int) get_theme_mod( 'header_image_height_min', 210 );
    $header_image_max = (int) get_theme_mod( 'header_image_height_max', 480 );
    $header_min = (int) get_theme_mod( 'header_height_min', 190 );
    $header_max = (int) get_theme_mod( 'header_height_max', 380 );
    $vertical_min = (int) get_theme_mod( 'vertical_spacing_min', 24 );
    $vertical_max = (int) get_theme_mod( 'vertical_spacing_max', 48 );
    $horizontal_min = (int) get_theme_mod( 'horizontal_spacing_min', 20 );
    $horizontal_max = (int) get_theme_mod( 'horizontal_spacing_max', 80 );
    $horizontal_small_min = (int) get_theme_mod( 'horizontal_spacing_small_min', 10 );
    $horizontal_small_max = (int) get_theme_mod( 'horizontal_spacing_small_max', 20 );
    $large_border_radius = (int) get_theme_mod( 'large_border_radius', 4 );
    $small_border_radius = (int) get_theme_mod( 'small_border_radius', 2 );

    // Build CSS
    $layout_css = ":root {
      --site-width: " . $site_width . "px;
      --hue-offset: " . $hue_offset . "deg;
      --saturation-offset: " . $saturation_offset / 100 . ";
      --lightness-offset: " . $lightness_offset / 100 . ";
    }";

    if ( get_theme_mod( 'use_custom_layout', false ) ) {
      $layout_css .= ":root, :root[data-theme=base] {
        --layout-spacing-vertical: " . fictioneer_get_css_clamp( $vertical_min, $vertical_max, 480, $site_width ) . ";
        --layout-spacing-horizontal: " . fictioneer_get_css_clamp( $horizontal_min, $horizontal_max, 480, $site_width, '%' ) . ";
        --layout-spacing-horizontal-small: " . fictioneer_get_css_clamp( $horizontal_small_min, $horizontal_small_max, 320, 400, '%' ) . ";
        --layout-border-radius-large: " . $large_border_radius . "px;
        --layout-border-radius-small: " . $small_border_radius . "px;
      }";
    }

    $layout_css .= ".header-background {
      --layout-header-background-height: " . fictioneer_get_css_clamp( $header_image_min, $header_image_max, 320, $site_width ) . ";
    }
    .header {
      --layout-site-header-height: calc(" . fictioneer_get_css_clamp( $header_min, $header_max, 320, $site_width ) . " - var(--layout-main-inset-top));
    }
    .header__logo {
      --layout-site-logo-height: " . $logo_height . "px;
    }
    .header__title {
      --site-title-font-size: " . fictioneer_get_css_clamp( $title_min, $title_max, 320, $site_width ) . ";
      --site-title-tagline-font-size: " . fictioneer_get_css_clamp( $tagline_min, $tagline_max, 320, $site_width ) . ";
    }";

    $layout_css = fictioneer_minify_css( $layout_css );

    // Remember CSS and add it to site
    if ( ! is_customize_preview() ) set_transient( 'fictioneer_customized_layout', $layout_css );
    wp_add_inline_style( 'fictioneer-application', $layout_css );
  }
}

?>
