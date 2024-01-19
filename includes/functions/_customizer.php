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
   * @param string $input  The to be converted hex (six digits).
   *
   * @return array RGB values as array.
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
   * @param array $input      The to be converted RGB.
   * @param int   $precision  Rounding precision. Default 0.
   *
   * @return array HSL values as array.
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
   * @param string $string  The to be minified CSS string.
   *
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
   * Generate a high-precision CSS clamp
   *
   * @since Fictioneer 4.7
   *
   * @param int    $min   The minimum value.
   * @param int    $max   The maximum value.
   * @param int    $wmin  The minimum viewport value.
   * @param int    $wmax  The maximum viewport value.
   * @param string $unit  The relative clamp unit. Default 'vw'.
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
   * @param string $hex     The color as hex.
   * @param string $output  Switch output style. Default 'default'.
   *
   * @return string The converted color.
   */

  function fictioneer_hsl_code( $hex, $output = 'default' ) {
    $hsl_array = fictioneer_rgb_to_hsl( fictioneer_hex_to_rgb( $hex ), 2 );

    if ( $output == 'values' ) {
      return "$hsl_array[0] $hsl_array[1] $hsl_array[2]";
    }

    $deg = 'calc(' . $hsl_array[0] . 'deg + var(--hue-rotate))';
    $saturation = 'calc(' . $hsl_array[1] . '% * var(--saturation))';
    $min = max( 0, $hsl_array[2] * 0.5 );
    $max = $hsl_array[2] + (100 - $hsl_array[2]) / 2;
    $lightness = 'clamp('. $min . '%, ' . $hsl_array[2] . '% * var(--darken), ' . $max . '%)';

    if ( $output == 'free' ) {
      return "$deg $saturation $lightness";
    }

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
   * @param string $hex  The color as hex.
   *
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
    if ( ! get_theme_mod( 'use_custom_light_mode', false ) ) {
      return;
    }

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
    if ( ! is_customize_preview() ) {
      set_transient( 'fictioneer_customized_light_mode', $light_css );
    }

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
    if ( ! get_theme_mod( 'use_custom_dark_mode', false ) ) {
      return;
    }

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
    if ( ! is_customize_preview() ) {
      set_transient( 'fictioneer_customized_dark_mode', $dark_css );
    }

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
      --layout-header-background-height: " . fictioneer_get_css_clamp( $header_image_min, $header_image_max, 320, $site_width ) . ";
      --layout-site-header-height: calc(" . fictioneer_get_css_clamp( $header_min, $header_max, 320, $site_width ) . " - var(--layout-main-inset-top));
      --layout-site-logo-height: " . $logo_height . "px;
      --site-title-font-size: " . fictioneer_get_css_clamp( $title_min, $title_max, 320, $site_width ) . ";
      --site-title-tagline-font-size: " . fictioneer_get_css_clamp( $tagline_min, $tagline_max, 320, $site_width ) . ";
      --grid-columns-min: 308px;
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

    if ( get_theme_mod( 'dark_mode_font_weight', 'adjusted' ) === 'normal' ) {
      $layout_css .= ":root[data-font-weight=default]:is(html) {
        --font-smoothing-webkit: subpixel-antialiased;
        --font-smoothing-moz: auto;
        --font-weight-normal: 400;
        --font-weight-semi-strong: 600;
        --font-weight-strong: 600;
        --font-weight-medium: 500;
        --font-weight-heading: 700;
        --font-weight-badge: 600;
        --font-weight-post-meta: 400;
        --font-weight-ribbon: 700;
        --font-weight-navigation: 400;
        --font-letter-spacing-base: 0em;
      }";
    }

    $layout_css = fictioneer_minify_css( $layout_css );

    // Remember CSS and add it to site
    if ( ! is_customize_preview() ) {
      set_transient( 'fictioneer_customized_layout', $layout_css );
    }

    wp_add_inline_style( 'fictioneer-application', $layout_css );
  }
}

?>
