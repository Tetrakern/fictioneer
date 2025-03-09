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
   * @since 4.7.0
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
   * @since 4.7.0
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
// GENERATE CSS CLAMP
// =============================================================================

if ( ! function_exists( 'fictioneer_get_css_clamp' ) ) {
  /**
   * Generate a high-precision CSS clamp
   *
   * @since 4.7.0
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
   * @since 4.7.0
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
   * @since 4.7.0
   * @see fictioneer_hsl_code( $hex, $output )
   *
   * @param string $hex  The color as hex.
   *
   * @return string The converted color.
   */

  function fictioneer_hsl_font_code( $hex ) {
    $hsl_array = fictioneer_rgb_to_hsl( fictioneer_hex_to_rgb( $hex ), 2 );

    $deg = 'calc(' . $hsl_array[0] . 'deg + var(--hue-rotate))';
    $saturation = 'max(calc(' . $hsl_array[1] . '% * (var(--font-saturation) + var(--saturation) - 1)), 0%)';
    $lightness = 'clamp(0%, calc(' . $hsl_array[2] . '% * var(--font-lightness, 1)), 100%)';

    return "hsl($deg $saturation $lightness)";
  }
}

// =============================================================================
// VALIDATE GOOGLE FONTS LINK
// =============================================================================

/**
 * Validates Google Fonts link
 *
 * @since 5.10.0
 *
 * @param string $link  The Google Fonts link.
 *
 * @return boolean Whether the link is valid or not.
 */

function fictioneer_validate_google_fonts_link( $link ) {
  return preg_match( '/^https:\/\/fonts\.googleapis\.com\/css2/', $link ) === 1;
}

// =============================================================================
// EXTRACT FONT DATA FROM GOOGLE FONTS LINK
// =============================================================================

/**
 * Returns fonts data from a Google Fonts link
 *
 * @since 5.10.0
 *
 * @param string $link  The Google Fonts link.
 *
 * @return array|false|null The font data if successful, false if malformed,
 *                          null if not a valid Google Fonts link.
 */

function fictioneer_extract_font_from_google_link( $link ) {
  // Validate
  if ( ! fictioneer_validate_google_fonts_link( $link ) ) {
    // Not Google Fonts link
    return null;
  }

  // Setup
  $font = array(
    'google_link' => $link,
    'skip' => true,
    'chapter' => true,
    'version' => '',
    'key' => '',
    'name' => '',
    'family' => '',
    'type' => '',
    'styles' => ['normal'],
    'weights' => [],
    'charsets' => [],
    'formats' => [],
    'about' => __( 'This font is loaded via the Google Fonts CDN, see source for additional information.', 'fictioneer' ),
    'note' => '',
    'sources' => array(
      'googleFontsCss' => array(
        'name' => 'Google Fonts CSS File',
        'url' => $link
      )
    )
  );

  // Name?
  preg_match( '/family=([^:]+)/', $link, $name_matches );

  if ( ! empty( $name_matches ) ) {
    $font['name'] = str_replace( '+', ' ', $name_matches[1] );
    $font['family'] = $font['name'];
    $font['key'] = sanitize_title( $font['name'] );
  } else {
    // Link malformed
    return false;
  }

  // Italic? Weights?
  preg_match( '/ital,wght@([0-9,;]+)/', $link, $ital_weight_matches );

  if ( ! empty( $ital_weight_matches ) ) {
    $specifications = explode( ';', $ital_weight_matches[1] );
    $weights = [];
    $is_italic = false;

    foreach ( $specifications as $spec ) {
      list( $ital, $weight ) = explode( ',', $spec );

      if ( $ital == '1' ) {
        $is_italic = true;
      }

      $weights[ $weight ] = true;
    }

    if ( $is_italic ) {
      $font['styles'][] = 'italic';
    }

    $font['weights'] = array_keys( $weights );
  }

  // Done
  return $font;
}

// =============================================================================
// GET FONT DATA
// =============================================================================

/**
 * Returns fonts included by the theme
 *
 * Note: If a font.json contains a { "remove": true } node, the font will not
 * be added to the result array and therefore removed from the site.
 *
 * @since 5.10.0
 *
 * @return array Array of font data. Keys: skip, chapter, version, key, name,
 *               family, type, styles, weights, charsets, formats, about, note,
 *               sources, css_path, css_file, and in_child_theme.
 */

function fictioneer_get_font_data() {
  // Setup
  $parent_font_dir = get_template_directory() . '/fonts';
  $child_font_dir = get_stylesheet_directory() . '/fonts';
  $parent_fonts = [];
  $child_fonts = [];
  $google_fonts = [];

  // Helper function
  $extract_font_data = function( $font_dir, &$fonts, $theme = 'parent' ) {
    if ( is_dir( $font_dir ) ) {
      $font_folders = array_diff( scandir( $font_dir ), ['..', '.'] );

      foreach ( $font_folders as $folder ) {
        $full_path = "{$font_dir}/{$folder}";
        $json_file = "$full_path/font.json";
        $css_file = "$full_path/font.css";

        if ( is_dir( $full_path ) && file_exists( $json_file ) && file_exists( $css_file ) ) {
          $folder_name = basename( $folder );
          $data = @json_decode( file_get_contents( $json_file ), true );

          if ( $data && json_last_error() === JSON_ERROR_NONE ) {
            if ( ! ( $data['remove'] ?? 0 ) ) {
              $data['css_path'] = "/fonts/{$folder_name}/font.css";
              $data['css_file'] = $css_file;
              $data['in_child_theme'] = $theme === 'child';

              $fonts[ $data['key'] ] = $data;
            }
          }
        }
      }

      return $fonts;
    }
  };

  // Parent theme
  $extract_font_data( $parent_font_dir, $parent_fonts );

  // Child theme (if any)
  if ( $parent_font_dir !== $child_font_dir ) {
    $extract_font_data( $child_font_dir, $child_fonts, 'child' );
  }

  // Google Fonts links (if any)
  $google_fonts_links = get_option( 'fictioneer_google_fonts_links' );

  if ( $google_fonts_links ) {
    $google_fonts_links = explode( "\n", $google_fonts_links );

    foreach ( $google_fonts_links as $link ) {
      $font = fictioneer_extract_font_from_google_link( $link );

      if ( $font ) {
        $google_fonts[] = $font;
      }
    }
  }

  // Merge finds
  $fonts = array_merge( $parent_fonts, $child_fonts, $google_fonts );

  // Apply filters
  $fonts = apply_filters( 'fictioneer_filter_font_data', $fonts );

  // Return complete font list
  return $fonts;
}

// =============================================================================
// BUILD BUNDLED FONT CSS FILE
// =============================================================================

/**
 * Build bundled font stylesheet
 *
 * @since 5.10.0
 */

function fictioneer_build_bundled_fonts() {
  // Setup
  $base_fonts = WP_CONTENT_DIR . '/themes/fictioneer/css/fonts-base.css';
  $fonts = fictioneer_get_font_data();
  $disabled_fonts = get_option( 'fictioneer_disabled_fonts', [] );
  $disabled_fonts = is_array( $disabled_fonts ) ? $disabled_fonts : [];
  $combined_font_css = '';
  $font_stack = [];

  // Apply filters
  $fonts = apply_filters( 'fictioneer_filter_pre_build_bundled_fonts', $fonts );

  // Build
  if ( file_exists( $base_fonts ) ) {
    $css = file_get_contents( $base_fonts );
    $css = str_replace( '../fonts/', get_template_directory_uri() . '/fonts/', $css );

    $combined_font_css .= $css;
  }

  foreach ( $fonts as $key => $font ) {
    if ( in_array( $key, $disabled_fonts ) ) {
      continue;
    }

    if ( $font['chapter'] ?? 0 ) {
      $font_stack[ $font['key'] ] = array(
        'css' => fictioneer_font_family_value( $font['family'] ?? '' ),
        'name' => $font['name'] ?? '',
        'alt' => $font['alt'] ?? ''
      );
    }

    if ( ! ( $font['skip'] ?? 0 ) && ! ( $font['google_link'] ?? 0 ) ) {
      $css = file_get_contents( $font['css_file'] );

      if ( $font['in_child_theme'] ?? 0 ) {
        $css = str_replace( '../fonts/', get_stylesheet_directory_uri() . '/fonts/', $css );
      } else {
        $css = str_replace( '../fonts/', get_template_directory_uri() . '/fonts/', $css );
      }

      $combined_font_css .= $css;
    }
  }

  // Update options
  update_option( 'fictioneer_chapter_fonts', $font_stack, true );
  update_option( 'fictioneer_bundled_fonts_timestamp', time(), true );

  // Save
  file_put_contents(
    fictioneer_get_theme_cache_dir( 'build_bundled_fonts' ) . '/bundled-fonts.css',
    $combined_font_css
  );
}

// =============================================================================
// GET CUSTOM FONT CSS
// =============================================================================

/**
 * Helper that returns a font family value
 *
 * @since 5.10.0
 *
 * @param string $option        Name of the theme mod.
 * @param string $font_default  Fallback font.
 * @param string $mod_default   Default for get_theme_mod().
 *
 * @return string Ready to use font family value.
 */

function fictioneer_get_custom_font( $option, $font_default, $mod_default ) {
  $selection = get_theme_mod( $option, $mod_default );
  $family = $font_default;

  switch ( $selection ) {
    case 'system':
      $family = 'var(--ff-system)';
      break;
    case 'default':
      $family = $font_default;
      break;
    default:
      $family = "'{$selection}', {$font_default}";
  }

  return $family;
}

// =============================================================================
// BUILD BUNDLED CUSTOMIZE CSS FILE
// =============================================================================

/**
 * Returns the CSS loaded from a snippet file
 *
 * @since 5.11.1
 *
 * @param string $snippet      Name of the snippet file without file ending.
 * @param string|null $filter  Optional. Part of the generated filter, defaulting
 *                             to the snippet name (lower case, underscores).
 *
 * @return string The CSS string from the file.
 */

function fictioneer_get_customizer_css_snippet( $snippet, $filter = null ) {
  // Setup
  $snippets_file_path = WP_CONTENT_DIR . '/themes/fictioneer/css/customize/';
  $filter = $filter ? $filter : strtolower( str_replace( '-', '_', $snippet ) );
  $css = file_get_contents( $snippets_file_path . $snippet . '.css' );

  // Get file contents
  if ( $css !== false ) {
    return apply_filters( "fictioneer_filter_css_snippet_{$filter}", $css );
  } else {
    error_log( 'File not found: ' . $snippets_file_path . $snippet . '.css' );
  }

  // Graceful error
  return '';
}

/**
 * Helper to get theme color mod with default fallback
 *
 * @since 5.12.0
 * @since 5.21.2 - Refactored with theme colors helper function.
 *
 * @param string $mod           The requested theme color.
 * @param string|null $default  Optional. Default color code.
 *
 * @return string The requested color code or '#ff6347' (tomato) if not found.
 */

function fictioneer_get_theme_color( $mod, $default = null ) {
  $fictioneer_colors = fictioneer_get_theme_colors_array();
  $default = $default ?? $fictioneer_colors[ $mod ]['hex'] ?? '#ff6347'; // Tomato

  return get_theme_mod( $mod, $default );
}

/**
 * Builds customization stylesheet
 *
 * @since 5.11.0
 *
 * @param string|null $context  Optional. In which context the stylesheet created,
 *                              for example 'preview' for the Customizer.
 */

function fictioneer_build_customize_css( $context = null ) {
  // --- Setup -----------------------------------------------------------------

  $file_path = fictioneer_get_theme_cache_dir( 'build_customize_css' ) . '/customize.css';
  $site_width = (int) get_theme_mod( 'site_width', FICTIONEER_DEFAULT_SITE_WIDTH );
  $header_image_style = get_theme_mod( 'header_image_style', 'default' );
  $header_style = get_theme_mod( 'header_style', 'default' );
  $content_list_style = get_theme_mod( 'content_list_style', 'default' );
  $content_list_collapse = get_theme_mod( 'content_list_collapse_style', 'default' );
  $page_style = get_theme_mod( 'page_style', 'default' );
  $card_style = get_theme_mod( 'card_style', 'default' );
  $card_frame = get_theme_mod( 'card_frame', 'default' );
  $footer_style = get_theme_mod( 'footer_style', 'default' );
  $sidebar_style = get_theme_mod( 'sidebar_style', 'none' );
  $css = '';

  if ( $context === 'preview' ) {
    $file_path = fictioneer_get_theme_cache_dir( 'preview' ) . '/customize-preview.css';
  }

  // --- Assets ----------------------------------------------------------------

  if (
    $header_image_style === 'polygon-battered' ||
    in_array( $page_style, ['polygon-battered', 'polygon-mask-image-battered-ringbook'] )
  ) {
    $css .= fictioneer_get_customizer_css_snippet( 'polygon-battered' );
  }

  // --- Header image style ----------------------------------------------------

  if ( $header_image_style === 'polygon-battered' ) {
    $css .= fictioneer_get_customizer_css_snippet( 'header-image-style-battered' );
  }

  if ( $header_image_style === 'polygon-chamfered' ) {
    $css .= fictioneer_get_customizer_css_snippet( 'header-image-style-chamfered' );
  }

  if ( $header_image_style === 'mask-grunge-frame-a-large' ) {
    $css .= fictioneer_get_customizer_css_snippet( 'header-image-style-grunge-frame-a-large' );
  }

  if ( $header_image_style === 'mask-grunge-frame-a-small' ) {
    $css .= fictioneer_get_customizer_css_snippet( 'header-image-style-grunge-frame-a-small' );
  }

  // --- Fading header image ---------------------------------------------------

  $header_image_fading_start = fictioneer_sanitize_integer( get_theme_mod( 'header_image_fading_start', 0 ), 0, 0, 99 );
  $header_image_fading_breakpoint = (int) get_theme_mod( 'header_image_fading_breakpoint', 0 );

  if ( $header_image_fading_start > 0 ) {
    if ( $header_image_fading_breakpoint > 320 ) {
      $css .= "@media only screen and (min-width: {$header_image_fading_breakpoint}px) {
        :root {
          --header-fading-mask-image: " . fictioneer_get_fading_gradient( 100, $header_image_fading_start, 100, 'var(--header-fading-mask-image-rotation, 180deg)' ) . ";
        }
      }";
    } else {
      $css .= ":root {
        --header-fading-mask-image: " . fictioneer_get_fading_gradient( 100, $header_image_fading_start, 100, 'var(--header-fading-mask-image-rotation, 180deg)' ) . ";
      }";
    }

    $css .= '@media only screen and (min-width: 1024px) {
      .inset-header-image .header-background._style-default._fading-bottom._shadow .header-background__wrapper {
        margin-left: 4px;
        margin-right: 4px;
      }
    }';
  }

  // --- Inset header image ----------------------------------------------------

  if ( get_theme_mod( 'inset_header_image' ) ) {
    $css .= fictioneer_get_customizer_css_snippet( 'inset-header-image' );
  }

  // --- Base layout -----------------------------------------------------------

  $hue_offset_dark = (int) get_theme_mod( 'hue_offset', 0 );
  $saturation_offset_dark = (int) get_theme_mod( 'saturation_offset', 0 );
  $lightness_offset_dark = (int) get_theme_mod( 'lightness_offset', 0 );
  $font_saturation_offset_dark = (int) get_theme_mod( 'font_saturation_offset', 0 );
  $font_lightness_offset_dark = (int) get_theme_mod( 'font_lightness_offset', 0 );
  $hue_offset_light = (int) get_theme_mod( 'hue_offset_light', 0 );
  $saturation_offset_light = (int) get_theme_mod( 'saturation_offset_light', 0 );
  $lightness_offset_light = (int) get_theme_mod( 'lightness_offset_light', 0 );
  $font_saturation_offset_light = (int) get_theme_mod( 'font_saturation_offset_light', 0 );
  $font_lightness_offset_light = (int) get_theme_mod( 'font_lightness_offset_light', 0 );
  $site_width = (int) get_theme_mod( 'site_width', FICTIONEER_DEFAULT_SITE_WIDTH );
  $main_offset = (int) get_theme_mod( 'main_offset', 0 );
  $sidebar_width = (int) get_theme_mod( 'sidebar_width', 256 );
  $sidebar_gap = (int) get_theme_mod( 'sidebar_gap', 48 );
  $logo_min_height = (int) get_theme_mod( 'logo_min_height', 210 );
  $logo_max_height = (int) get_theme_mod( 'logo_height', 210 );
  $title_min = (int) get_theme_mod( 'site_title_font_size_min', 32 );
  $title_max = (int) get_theme_mod( 'site_title_font_size_max', 60 );
  $tagline_min = (int) get_theme_mod( 'site_tagline_font_size_min', 13 );
  $tagline_max = (int) get_theme_mod( 'site_tagline_font_size_max', 18 );
  $header_image_min = (int) get_theme_mod( 'header_image_height_min', 210 );
  $header_image_max = (int) get_theme_mod( 'header_image_height_max', 480 );
  $header_bg_color_light = fictioneer_get_theme_color( 'header_color_light', '' );
  $header_bg_color_dark = fictioneer_get_theme_color( 'header_color_dark', '' );
  $header_min = (int) get_theme_mod( 'header_height_min', 190 );
  $header_max = (int) get_theme_mod( 'header_height_max', 380 );
  $story_cover_width_offset = (int) get_theme_mod( 'story_cover_width_offset', 0 );
  $story_cover_box_shadow = get_theme_mod( 'story_cover_shadow', 'var(--box-shadow-xl)' );
  $card_grid_column_min = (int) get_theme_mod( 'card_grid_column_min', 308 );
  $card_cover_width_mod = get_theme_mod( 'card_cover_width_mod', 1 );
  $card_grid_column_gap_mod = get_theme_mod( 'card_grid_column_gap_mod', 1 );
  $card_grid_row_gap_mod = get_theme_mod( 'card_grid_row_gap_mod', 1 );
  $card_font_size_min_mod = get_theme_mod( 'card_font_size_min_mod', 0 );
  $card_font_size_grow_mod = get_theme_mod( 'card_font_size_grow_mod', 0 );
  $card_font_size_max_mod = get_theme_mod( 'card_font_size_max_mod', 0 );
  $card_box_shadow = get_theme_mod( 'card_shadow', 'var(--box-shadow-m)' );
  $font_primary = fictioneer_get_custom_font( 'primary_font_family_value', 'var(--ff-system)', 'Open Sans' );
  $font_secondary = fictioneer_get_custom_font( 'secondary_font_family_value', 'var(--ff-base)', 'Lato' );
  $font_heading = fictioneer_get_custom_font( 'heading_font_family_value', 'var(--ff-base)', 'Open Sans' );
  $font_site_title = fictioneer_get_custom_font( 'site_title_font_family_value', 'var(--ff-heading)', 'default' );
  $font_nav_item = fictioneer_get_custom_font( 'nav_item_font_family_value', 'var(--ff-base)', 'default' );
  $font_story_title = fictioneer_get_custom_font( 'story_title_font_family_value', 'var(--ff-heading)', 'default' );
  $font_chapter_title = fictioneer_get_custom_font( 'chapter_title_font_family_value', 'var(--ff-heading)', 'default' );
  $font_chapter_list_title = fictioneer_get_custom_font( 'chapter_list_title_font_family_value', 'var(--ff-base)', 'default' );
  $font_card_title = fictioneer_get_custom_font( 'card_title_font_family_value', 'var(--ff-heading)', 'default' );
  $font_card_body = fictioneer_get_custom_font( 'card_body_font_family_value', 'var(--ff-note)', 'default' );
  $font_card_list_link = fictioneer_get_custom_font( 'card_list_link_font_family_value', 'var(--ff-note)', 'default' );

  $dark_shade = fictioneer_hex_to_rgb( get_theme_mod( 'dark_shade', '000000' ) );
  $dark_shade = is_array( $dark_shade ) ? $dark_shade : [0, 0, 0];

  if ( $logo_min_height < $logo_max_height ) {
    $logo_height = fictioneer_get_css_clamp( $logo_min_height, $logo_max_height, 320, $site_width );
  } else {
    $logo_height = $logo_max_height . 'px';
  }

  $css .= ":root {
    --site-width: {$site_width}px;
    --main-offset: {$main_offset}px;
    --sidebar-width: {$sidebar_width}px;
    --sidebar-gap: {$sidebar_gap}px;
    --hue-offset: {$hue_offset_dark}deg;
    --saturation-offset: " . $saturation_offset_dark / 100 . ";
    --lightness-offset: " . $lightness_offset_dark / 100 . ";
    --font-saturation-offset: " . $font_saturation_offset_dark / 100 . ";
    --font-lightness-offset: " . $font_lightness_offset_dark / 100 . ";
    --header-image-height: " . fictioneer_get_css_clamp( $header_image_min, $header_image_max, 320, $site_width ) . ";
    --header-height: calc(" . fictioneer_get_css_clamp( $header_min, $header_max, 320, $site_width ) . " - var(--page-inset-top, 0px));
    --header-logo-height: {$logo_height};
    --header-logo-min-height: {$logo_min_height};
    --header-logo-max-height: {$logo_max_height};
    --site-title-font-size: " . fictioneer_get_css_clamp( $title_min, $title_max, 320, $site_width ) . ";
    --site-title-tagline-font-size: " . fictioneer_get_css_clamp( $tagline_min, $tagline_max, 320, $site_width ) . ";
    --grid-columns-min: {$card_grid_column_min}px;
    --grid-columns-row-gap-multiplier: {$card_grid_row_gap_mod};
    --grid-columns-col-gap-multiplier: {$card_grid_column_gap_mod};
    --card-font-size-min-mod: {$card_font_size_min_mod}px;
    --card-font-size-grow-mod: {$card_font_size_grow_mod}px;
    --card-font-size-max-mod: {$card_font_size_max_mod}px;
    --ff-base: {$font_primary};
    --ff-note: {$font_secondary};
    --ff-heading: {$font_heading};
    --ff-site-title: {$font_site_title};
    --ff-story-title: {$font_story_title};
    --ff-chapter-title: {$font_chapter_title};
    --ff-chapter-list-title: {$font_chapter_list_title};
    --ff-card-title: {$font_card_title};
    --ff-card-body: {$font_card_body};
    --ff-card-list-link: {$font_card_list_link};
    --ff-nav-item: {$font_nav_item};
    --card-cover-width-mod: {$card_cover_width_mod};
    --card-box-shadow: {$card_box_shadow};
    --card-drop-shadow: " . str_replace( 'box-', 'drop-', $card_box_shadow ) . ";
    --story-cover-box-shadow: {$story_cover_box_shadow};
    --recommendation-cover-box-shadow: {$story_cover_box_shadow};
    --floating-cover-image-width: " . fictioneer_get_css_clamp( 56, 200 + $story_cover_width_offset, 320, 768 ) . ";
    --in-content-cover-image-width: " . fictioneer_get_css_clamp( 100, 200 + $story_cover_width_offset, 375, 768 ) . ";
    --chapter-group-background-after: " . ( $content_list_collapse === 'edge' ? 'none' : "''" ) . ";
  }";

  if ( $card_box_shadow === 'none' ) {
    $css .= ".card{box-shadow:none!important;}";
  }

  // Only light mode
  $css .= ":root[data-mode=light] {
    --hue-offset: {$hue_offset_light}deg;
    --saturation-offset: " . $saturation_offset_light / 100 . ";
    --lightness-offset: " . $lightness_offset_light / 100 . ";
    --font-saturation-offset: " . $font_saturation_offset_light / 100 . ";
    --font-lightness-offset: " . $font_lightness_offset_light / 100 . ";
  }";

  // --- Custom layout ---------------------------------------------------------

  if ( $sidebar_style !== 'none' ) {
    $css .= ":root, :root[data-theme=base] {
      --layout-spacing-horizontal: " . fictioneer_get_css_clamp( 20, 48, 480, $site_width ) . ";
      --layout-spacing-horizontal-small: " . fictioneer_get_css_clamp( 10, 20, 320, 400 ) . ";
    }";
  }

  if ( get_theme_mod( 'use_custom_layout', false ) ) {
    $vertical_min = (int) get_theme_mod( 'vertical_spacing_min', 24 );
    $vertical_max = (int) get_theme_mod( 'vertical_spacing_max', 48 );
    $horizontal_min = (int) get_theme_mod( 'horizontal_spacing_min', 20 );
    $horizontal_max = (int) get_theme_mod( 'horizontal_spacing_max', 80 );
    $horizontal_small_min = (int) get_theme_mod( 'horizontal_spacing_small_min', 10 );
    $horizontal_small_max = (int) get_theme_mod( 'horizontal_spacing_small_max', 20 );
    $large_border_radius = (int) get_theme_mod( 'large_border_radius', 4 );
    $small_border_radius = (int) get_theme_mod( 'small_border_radius', 2 );
    $nested_border_radius_multiplier = max( 0, get_theme_mod( 'nested_border_radius_multiplier', 1 ) );
    $content_list_gap = (int) get_theme_mod( 'content_list_gap', 4 );

    $css .= ":root, :root[data-theme=base] {
      --layout-spacing-vertical: " . fictioneer_get_css_clamp( $vertical_min, $vertical_max, 480, $site_width ) . ";
      --layout-spacing-horizontal: " . fictioneer_get_css_clamp( $horizontal_min, $horizontal_max, 480, $site_width, '%' ) . ";
      --layout-spacing-horizontal-small: " . fictioneer_get_css_clamp( $horizontal_small_min, $horizontal_small_max, 320, 400, '%' ) . ";
      --layout-border-radius-large: {$large_border_radius}px;
      --layout-border-radius-small: {$small_border_radius}px;
      --layout-nested-border-radius-multiplier: {$nested_border_radius_multiplier};
      --chapter-list-gap: {$content_list_gap}px;
      --content-list-gap: {$content_list_gap}px;
    }";

    if ( $sidebar_style !== 'none' ) {
      $css .= ".has-sidebar {
        --layout-spacing-horizontal: " . fictioneer_get_css_clamp( $horizontal_min, $horizontal_max, 480, $site_width ) . ";
        --layout-spacing-horizontal-small: " . fictioneer_get_css_clamp( $horizontal_small_min, $horizontal_small_max, 320, 400 ) . ";
      }";
    }
  }

  // --- Dark mode font weight adjustment --------------------------------------

  if ( get_theme_mod( 'dark_mode_font_weight', 'adjusted' ) === 'normal' ) {
    $css .= ":root[data-font-weight=default]:is(html) {
      --font-smoothing-webkit: subpixel-antialiased;
      --font-smoothing-moz: auto;
      --font-weight-normal: 400;
      --font-weight-semi-strong: 600;
      --font-weight-strong: 600;
      --font-weight-medium: 500;
      --font-weight-heading: 700;
      --font-weight-badge: 600;
      --font-weight-post-meta: 400;
      --font-weight-read-ribbon: 700;
      --font-weight-card-label: 600;
      --font-weight-navigation: 400;
      --font-letter-spacing-base: 0em;
    }";
  }

  // --- Dark mode colors ------------------------------------------------------

  $css .= ":root {
    --site-title-heading-color: " . fictioneer_hsl_font_code( fictioneer_get_theme_color( 'dark_header_title_color' ) ) . ";
    --site-title-tagline-color: " . fictioneer_hsl_font_code( fictioneer_get_theme_color( 'dark_header_tagline_color' ) ) . ";
  }";

  if ( $header_bg_color_dark ) {
    $css .= ":root {
      --header-background-color: " . fictioneer_hsl_code( $header_bg_color_dark ) . ";
    }";
  }

  if ( get_theme_mod( 'use_custom_dark_mode', false ) ) {
    $css .= ":root, :root[data-theme=base] {"
      .
      implode( '', array_map( function( $prop ) {
        return "--bg-{$prop}-free: " . fictioneer_hsl_code( fictioneer_get_theme_color( "dark_bg_{$prop}" ), 'free' ) . ';';
      }, ['50', '100', '200', '300', '400', '500', '600', '700', '800', '900', '950'] ) )
      .
      "
      --card-frame-border-color: " . fictioneer_hsl_code( fictioneer_get_theme_color( 'dark_card_frame' ) ) . ";
      --dark-shade-rgb:" . implode( ' ', $dark_shade ) . ";
      --primary-400: " . fictioneer_get_theme_color( 'dark_primary_400' ) . ";
      --primary-500: " . fictioneer_get_theme_color( 'dark_primary_500' ) . ";
      --primary-600: " . fictioneer_get_theme_color( 'dark_primary_600' ) . ";
      --red-400: " . fictioneer_get_theme_color( 'dark_red_400' ) . ";
      --red-500: " . fictioneer_get_theme_color( 'dark_red_500' ) . ";
      --red-600: " . fictioneer_get_theme_color( 'dark_red_600' ) . ";
      --green-400: " . fictioneer_get_theme_color( 'dark_green_400' ) . ";
      --green-500: " . fictioneer_get_theme_color( 'dark_green_500' ) . ";
      --green-600: " . fictioneer_get_theme_color( 'dark_green_600' ) . ";
      --theme-color-base: " . fictioneer_hsl_code( fictioneer_get_theme_color( 'dark_theme_color_base' ), 'values' ) . ";
      --navigation-background: " . fictioneer_hsl_code( fictioneer_get_theme_color( 'dark_navigation_background_sticky' ) ) . ";
      --bookmark-color-alpha: " . fictioneer_get_theme_color( 'dark_bookmark_color_alpha' ) . ";
      --bookmark-color-beta: " . fictioneer_get_theme_color( 'dark_bookmark_color_beta' ) . ";
      --bookmark-color-gamma: " . fictioneer_get_theme_color( 'dark_bookmark_color_gamma' ) . ";
      --bookmark-color-delta: " . fictioneer_get_theme_color( 'dark_bookmark_color_delta' ) . ";
      --bookmark-line: " . fictioneer_get_theme_color( 'dark_bookmark_line_color' ) . ";
      --ins-background: " . fictioneer_get_theme_color( 'dark_ins_background' ) . ";
      --del-background: " . fictioneer_get_theme_color( 'dark_del_background' ) . ";"
      .
      implode( '', array_map( function( $prop ) {
        return "--badge-{$prop}-background: " . fictioneer_get_theme_color( "dark_badge_{$prop}_background" ) . ';';
      }, ['generic', 'moderator', 'admin', 'author', 'supporter', 'override'] ) )
      .
    "}
    :root, :root[data-theme=base], :root .chapter-formatting, :root[data-theme=base] .chapter-formatting {"
      .
      implode( '', array_map( function( $prop ) {
        return "--fg-{$prop}: " . fictioneer_hsl_font_code( fictioneer_get_theme_color( "dark_fg_{$prop}" ) ) . ';';
      }, ['100', '200', '300', '400', '500', '600', '700', '800', '900', '950', 'tinted', 'inverted'] ) )
      .
    "}";
  }

  // --- Light mode colors -----------------------------------------------------

  $css .= ":root[data-mode=light] {
    --site-title-heading-color: " . fictioneer_hsl_font_code( fictioneer_get_theme_color( 'light_header_title_color' ) ) . ";
    --site-title-tagline-color: " . fictioneer_hsl_font_code( fictioneer_get_theme_color( 'light_header_tagline_color' ) ) . ";
  }";

  if ( $header_bg_color_light ) {
    $css .= ":root[data-mode=light] {
      --header-background-color: " . fictioneer_hsl_code( $header_bg_color_light ) . ";
    }";
  }

  if ( get_theme_mod( 'use_custom_light_mode', false ) ) {
    $css .= ":root[data-mode=light] {"
      .
      implode( '', array_map( function( $prop ) {
        return "--bg-{$prop}-free: " . fictioneer_hsl_code( fictioneer_get_theme_color( "light_bg_{$prop}" ), 'free' ) . ';';
      }, ['50', '100', '200', '300', '400', '500', '600', '700', '800', '900', '950'] ) )
      .
      "
      --card-frame-border-color: " . fictioneer_hsl_code( fictioneer_get_theme_color( 'light_card_frame' ) ) . ";
      --primary-400: " . fictioneer_get_theme_color( 'light_primary_400' ) . ";
      --primary-500: " . fictioneer_get_theme_color( 'light_primary_500' ) . ";
      --primary-600: " . fictioneer_get_theme_color( 'light_primary_600' ) . ";
      --red-400: " . fictioneer_get_theme_color( 'light_red_400' ) . ";
      --red-500: " . fictioneer_get_theme_color( 'light_red_500' ) . ";
      --red-600: " . fictioneer_get_theme_color( 'light_red_600' ) . ";
      --green-400: " . fictioneer_get_theme_color( 'light_green_400' ) . ";
      --green-500: " . fictioneer_get_theme_color( 'light_green_500' ) . ";
      --green-600: " . fictioneer_get_theme_color( 'light_green_600' ) . ";
      --theme-color-base: " . fictioneer_hsl_code( fictioneer_get_theme_color( 'light_theme_color_base' ), 'values' ) . ";
      --navigation-background: " . fictioneer_hsl_code( fictioneer_get_theme_color( 'light_navigation_background_sticky' ) ) . ";
      --bookmark-color-alpha: " . fictioneer_get_theme_color( 'light_bookmark_color_alpha' ) . ";
      --bookmark-color-beta: " . fictioneer_get_theme_color( 'light_bookmark_color_beta' ) . ";
      --bookmark-color-gamma: " . fictioneer_get_theme_color( 'light_bookmark_color_gamma' ) . ";
      --bookmark-color-delta: " . fictioneer_get_theme_color( 'light_bookmark_color_delta' ) . ";
      --bookmark-line: " . fictioneer_get_theme_color( 'light_bookmark_line_color' ) . ";
      --ins-background: " . fictioneer_get_theme_color( 'light_ins_background' ) . ";
      --del-background: " . fictioneer_get_theme_color( 'light_del_background' ) . ";"
      .
      implode( '', array_map( function( $prop ) {
        return "--badge-{$prop}-background: " . fictioneer_get_theme_color( "light_badge_{$prop}_background" ) . ';';
      }, ['generic', 'moderator', 'admin', 'author', 'supporter', 'override'] ) )
      .
    "}
    :root[data-mode=light], :root[data-mode=light] .chapter-formatting {"
      .
      implode( '', array_map( function( $prop ) {
        return "--fg-{$prop}: " . fictioneer_hsl_font_code( fictioneer_get_theme_color( "light_fg_{$prop}" ) ) . ';';
      }, ['100', '200', '300', '400', '500', '600', '700', '800', '900', '950', 'tinted', 'inverted'] ) )
      .
    "}";
  }

  // --- Header styles ---------------------------------------------------------

  if ( in_array( $header_style, ['top', 'split'] ) ) {
    $css .= fictioneer_get_customizer_css_snippet( 'header-style-top-split' );
  }

  if ( $header_style === 'wide' ) {
    $css .= fictioneer_get_customizer_css_snippet( 'header-style-wide' );
  }

  if ( $header_style === 'text_center' ) {
    $css .= fictioneer_get_customizer_css_snippet( 'header-style-text-center' );
  }

  if ( $header_style === 'post_content' ) {
    $css .= fictioneer_get_customizer_css_snippet( 'header-style-post-content' );
  }

  // --- Page styles -----------------------------------------------------------

  if ( $page_style === 'polygon-mask-image-battered-ringbook' || $page_style === 'polygon-battered' ) {
    $css .= fictioneer_get_customizer_css_snippet( 'page-style-battered' );
  }

  if ( $page_style === 'polygon-mask-image-battered-ringbook' || $page_style === 'mask-image-ringbook' ) {
    $css .= fictioneer_get_customizer_css_snippet( 'page-style-ringbook' );
  }

  if ( $page_style === 'polygon-chamfered' ) {
    $css .= fictioneer_get_customizer_css_snippet( 'page-style-chamfered' );
  }

  if ( $page_style === 'polygon-interface-a' ) {
    $css .= fictioneer_get_customizer_css_snippet( 'page-style-interface-a' );
  }

  if ( $page_style === 'mask-image-wave-a' ) {
    $css .= fictioneer_get_customizer_css_snippet( 'page-style-wave-a' );
  }

  if ( $page_style === 'mask-image-layered-steps-a' ) {
    $css .= fictioneer_get_customizer_css_snippet( 'page-style-layered-steps-a' );
  }

  if ( $page_style === 'mask-image-layered-peaks-a' ) {
    $css .= fictioneer_get_customizer_css_snippet( 'page-style-layered-peaks-a' );
  }

  if ( $page_style === 'mask-image-grunge-a' ) {
    $css .= fictioneer_get_customizer_css_snippet( 'page-style-grunge-a' );
  }

  if ( $page_style === 'none' ) {
    $css .= '.main__background { display:none !important; }';
  }

  // --- Page shadow -----------------------------------------------------------

  if ( ! get_theme_mod( 'page_shadow', true ) ) {
    $css .= ':root.no-page-shadow {
      --minimal-page-box-shadow: none;
      --page-box-shadow: none;
      --page-drop-shadow: none;
    }';
  }

  // --- Card styles -----------------------------------------------------------

  if ( in_array( $card_style, ['unfolded', 'combined'] ) ) {
    $css .= fictioneer_get_customizer_css_snippet( 'card-style-unfolded-combined' );
  }

  if ( $card_style === 'combined' ) {
    $css .= fictioneer_get_customizer_css_snippet( 'card-style-combined' );
  }

  // --- Card frames -----------------------------------------------------------

  if ( $card_frame !== 'default' ) {
    $css .= ':root:not(.minimal) .card{filter:var(--card-drop-shadow);}';
  }

  if ( in_array( $card_frame, ['stacked_right', 'stacked_left', 'stacked_random'] ) ) {
    $css .= fictioneer_get_customizer_css_snippet( 'card-frame-stacked' );

    if ( $card_frame === 'stacked_left' ) {
      $css .= '.card{--this-rotation-mod:-1;}';
    }
  }

  if ( $card_frame === 'border_2px' ) {
    $css .= ':root:not(.minimal) .card{--card-style-border-width: 2px;box-shadow: 0 0 0 var(--card-frame-border-thickness, 2px) var(--card-frame-border-color);}';
  }

  if ( $card_frame === 'border_3px' ) {
    $css .= ':root:not(.minimal) .card{--card-style-border-width: 3px;box-shadow: 0 0 0 var(--card-frame-border-thickness, 3px) var(--card-frame-border-color);}';
  }

  if ( $card_frame === 'chamfered' ) {
    $css .= fictioneer_get_customizer_css_snippet( 'card-frame-chamfered' );
  }

  if ( $card_frame === 'battered' ) {
    $css .= fictioneer_get_customizer_css_snippet( 'card-frame-battered' );
  }

  // --- Content list style ----------------------------------------------------

  if ( $content_list_style === 'full' ) {
    $css .= fictioneer_get_customizer_css_snippet( 'content-list-style-full' );
  }

  if ( $content_list_style === 'free' ) {
    $css .= fictioneer_get_customizer_css_snippet( 'content-list-style-free' );
  }

  if ( $content_list_style === 'lines' ) {
    $css .= fictioneer_get_customizer_css_snippet( 'content-list-style-lines' );
  }

  // --- Content list collapse style -------------------------------------------

  if ( $content_list_collapse === 'edge' ) {
    $css .= ".chapter-group._closed .chapter-group__list{opacity:1;}.chapter-group__name{padding:12px 6px;}";
  }

  // --- Footer style ----------------------------------------------------------

  if ( $footer_style === 'isolated' ) {
    $css .= fictioneer_get_customizer_css_snippet( 'footer-style-isolated' );
  }

  // --- Filters ---------------------------------------------------------------

  $css = apply_filters( 'fictioneer_filter_pre_build_customize_css', $css );

  // --- Minify ----------------------------------------------------------------

  $css = fictioneer_minify_css( $css );

  // --- Update options --------------------------------------------------------

  if ( $context !== 'preview' ) {
    update_option( 'fictioneer_customize_css_timestamp', time(), true );
  }

  // --- Save ------------------------------------------------------------------

  file_put_contents( $file_path, $css );
}

// =============================================================================
// GENERATE LINEAR GRADIENT CSS WITH APPROXIMATED CUBIC-BEZIER
// =============================================================================

if ( ! function_exists( 'fictioneer_get_fading_gradient' ) ) {
  /**
   * Returns an eased fading linear-gradient CSS
   *
   * This is an approximated cubic-bezier transition based on a pattern.
   *
   * @param float  $start_opacity  The starting opacity of the gradient in percentage.
   * @param int    $start          The starting point of the gradient in percentage.
   * @param int    $end            The ending point of the gradient in percentage.
   * @param int    $direction      The direction of the gradient with unit (e.g. '180deg').
   * @param string $hsl            The HSL string used as color. Default '0 0% 0%'.
   *
   * @return string The linear-gradient CSS.
   */

  function fictioneer_get_fading_gradient( $start_opacity, $start, $end, $direction, $hsl = '0 0% 0%' ) {
    // Setup
    $alpha_values = [0.987, 0.951, 0.896, 0.825, 0.741, 0.648, 0.55, 0.45, 0.352, 0.259, 0.175, 0.104, 0.049, 0.013, 0];
    $num_stops = count( $alpha_values );

    // Calculate positions
    $positions = array_map(
      function( $index ) use ( $start, $end, $num_stops ) {
        return $start + ( ( $end - $start ) / ( $num_stops - 1 ) * $index );
      },
      array_keys( $alpha_values )
    );


    // Open...
    $gradient = "linear-gradient({$direction}, ";

    // ... add color stops...
    foreach ( $alpha_values as $index => $alpha ) {
      $position = round( $positions[ $index ], 2 );
      $adjusted_alpha = round( $alpha * $start_opacity, 3 );
      $gradient .= "hsl({$hsl} / {$adjusted_alpha}%) {$position}%";

      if ( $index < $num_stops - 1 ) {
        $gradient .= ', ';
      }
    }

    // ... close
    $gradient .= ');';

    // Return
    return $gradient;
  }
}
