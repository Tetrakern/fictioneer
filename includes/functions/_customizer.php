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
   * @since 4.7.0
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
    $saturation = 'calc(' . $hsl_array[1] . '% * (var(--font-saturation) + var(--saturation) - 1))';
    $lightness = $hsl_array[2] . '%';

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
 * @since 5.10.0
 *
 * @return array Array of font data.
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
  $bundled_fonts = WP_CONTENT_DIR . '/themes/fictioneer/cache/bundled-fonts.css';
  $fonts = fictioneer_get_font_data();
  $disabled_fonts = get_option( 'fictioneer_disabled_fonts', [] );
  $disabled_fonts = is_array( $disabled_fonts ) ? $disabled_fonts : [];
  $combined_font_css = '';
  $font_stack = [];

  // Apply filters
  $fonts = apply_filters( 'fictioneer_filter_pre_build_bundled_fonts', $fonts );

  // Make sure directory exists
  if ( ! file_exists( dirname( $bundled_fonts ) ) ) {
    mkdir( dirname( $bundled_fonts ), 0755, true );
  }

  // Build
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
      }

      $combined_font_css .= $css;
    }
  }

  // Update options
  update_option( 'fictioneer_chapter_fonts', $font_stack, true );
  update_option( 'fictioneer_bundled_fonts_timestamp', time(), true );

  // Save
  file_put_contents( $bundled_fonts, $combined_font_css );
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
 * Builds customization stylesheet
 *
 * @since 5.11.0
 *
 * @param string|null $content  Optional. In which context the stylesheet created,
 *                              for example 'preview' for the Customizer.
 */

function fictioneer_build_customize_css( $content = null ) {
  // --- Setup -----------------------------------------------------------------

  $file_path = WP_CONTENT_DIR . '/themes/fictioneer/cache/customize.css';
  $site_width = (int) get_theme_mod( 'site_width', 960 );
  $header_image_style = get_theme_mod( 'header_image_style', 'default' );
  $header_style = get_theme_mod( 'header_style', 'default' );
  $content_list_style = get_theme_mod( 'content_list_style', 'default' );
  $page_style = get_theme_mod( 'page_style', 'default' );
  $css = '';

  if ( $content === 'preview' ) {
    $file_path = WP_CONTENT_DIR . '/themes/fictioneer/cache/customize-preview.css';
  }

  // --- Assets ----------------------------------------------------------------

  if (
    $header_image_style === 'polygon-battered' ||
    in_array( $page_style, ['polygon-battered', 'polygon-mask-image-battered-ringbook'] )
  ) {
    $css .= ":root {
      --polygon-battered: polygon(8px 3px, 12px 2px, 16px 0, 10% 2px, 10.5% 0px, 11% 1px, 11.5% 0px, 18% 1.5px, 21% 0, 28% 1px, 30% 4px, 30.5% 1px, 31% 2px, 31.5% 1px, 33% 3px, 34% 0, 35.5% 1px, 36.5% 2px, 38% 0.5px, 48% 0, 49% 4px, 50% 1.5px, 57% 1px, 59% 0, 61% 1.5px, 63% 0, 67% 1px, 70% 0, 70.5% 4px, 71% 1px, 71.5% 2px, 72% 0, 72.5% 3px, 75% 0, 78% 2px, 85% 3px, 93% 1px, 96% 0, calc(100% - 8px) 0, calc(100% - 4px) 2px, calc(100% - 3px) 10px, 100% 14px, calc(100% - 1px) 15%, calc(100% - 2px) 16.5%, 100% 17%, calc(100% - 1px) 17.5%, 100% 18%, calc(100% - 1px) 21%, 100% 22%, calc(100% - 4px) 23%, calc(100% - 1px) 23.5%, calc(100% - 3px) 24%, calc(100% - 2px) 24.5%, 100% 25%, calc(100% - 1px) 25.5%, 100% 26%, calc(100% - 2px) 26.5%, calc(100% - 1px) 27%, calc(100% - 1.5px) 28%, 100% 29%, calc(100% - 1px) 45%, calc(100% - 3px) 45.5%, calc(100% - 2px) 46%, 100% 47%, calc(100% - 1px) 48%, 100% 49%, calc(100% - 2px) 49.5%, calc(100% - 1px) 50%, calc(100% - 1.5px) 50.5%, 100% 51%, calc(100% - 3px) 53%, 100% 53.5%, calc(100% - 2px) 55%, calc(100% - 1px) 56%, 100% 57%, 100% 65%, calc(100% - 4px) 66%, calc(100% - 2px) 67%, 100% 69%, calc(100% - 2px) 78%, calc(100% - 1.5px) 85%, calc(100% - 3px) 85.5%, calc(100% - 8px) 86%, calc(100% - 2px) 86.5%, 100% 100%, 98% calc(100% - 2px), 97% calc(100% - 4px), 96% calc(100% - 1px), 93% 100%, 92.5% calc(100% - 3px), 91% 100%, 87% calc(100% - 1px), 80% 100%, 79.5% calc(100% - 6px), 79.2% calc(100% - 3px), 78.5% 100%, 70% calc(100% - 1px), 69.5% 100%, 69% calc(100% - 5px), 68.5% 100%, 68% calc(100% - 3px), 67.5% calc(100% - 2px), 67% 100%, 66.5% calc(100% - 2px), 66% 100%, 65.6% calc(100% - 2px), 65.3% 100%, 65% calc(100% - 1px), 64.7% 100%, 64.4% calc(100% - 1px), 64% 100%, 60% calc(100% - 1px), 52% 100%, 45% calc(100% - 3px), 40% calc(100% - 2px), 39% calc(100% - 3px), 38% calc(100% - 4px), 37% calc(100% - 2px), 36% calc(100% - 4px), 35% calc(100% - 1px), 19% 100%, 13% 100%, 12.8% calc(100% - 4px), 12.5% calc(100% - 6px), 12% calc(100% - 3px), 11.5% calc(100% - 4px), 11% calc(100% - 3px), 10.5% calc(100% - 5px), 10% calc(100% - 3px), 9.5% calc(100% - 2px), 9% calc(100% - 3px), 8.5% calc(100% - 6px), 5% calc(100% - 2px), 8px calc(100% - 2px), 6px calc(100% - 3px), 4px calc(100% - 4px), 0 calc(100% - 3px), 0 89%, 2px 87%, 4px 86%, 0 85%, 0 77%, 1px 76%, 2px 75%, 0 73.5%, 3px 73%, 0 71%, 1.5px 70.5%, 1px 70%, 2px 69.5%, 0 69%, 1px 68%, 0 67%, 2px 66%, 3px 65.5%, 1px 65%, 0 64%, 1px 50%, 5px 49.5%, 7px 49%, 0 48.5%, 3px 38%, 0 20%, 1px 16%, 3px 15.5%, 4px 14%, 2.5px 12%, 1px 11%, 0 10%, 1px 5%, 0 6px, 2px 4px);
      --polygon-battered-half: polygon(8px 3px, 12px 2px, 16px 0, 10% 2px, 10.5% 0px, 11% 1px, 11.5% 0px, 18% 1.5px, 21% 0, 28% 1px, 30% 4px, 30.5% 1px, 31% 2px, 31.5% 1px, 33% 3px, 34% 0, 35.5% 1px, 36.5% 2px, 38% 0.5px, 48% 0, 49% 4px, 50% 1.5px, 57% 1px, 59% 0, 61% 1.5px, 63% 0, 67% 1px, 70% 0, 70.5% 4px, 71% 1px, 71.5% 2px, 72% 0, 72.5% 3px, 75% 0, 78% 2px, 85% 3px, 93% 1px, 96% 0, calc(100% - 8px) 0, 100% 0, 100% 100%, 98% calc(100% - 2px), 97% calc(100% - 4px), 96% calc(100% - 1px), 93% 100%, 92.5% calc(100% - 3px), 91% 100%, 87% calc(100% - 1px), 80% 100%, 79.5% calc(100% - 6px), 79.2% calc(100% - 3px), 78.5% 100%, 70% calc(100% - 1px), 69.5% 100%, 69% calc(100% - 5px), 68.5% 100%, 68% calc(100% - 3px), 67.5% calc(100% - 2px), 67% 100%, 66.5% calc(100% - 2px), 66% 100%, 65.6% calc(100% - 2px), 65.3% 100%, 65% calc(100% - 1px), 64.7% 100%, 64.4% calc(100% - 1px), 64% 100%, 60% calc(100% - 1px), 52% 100%, 45% calc(100% - 3px), 40% calc(100% - 2px), 39% calc(100% - 3px), 38% calc(100% - 4px), 37% calc(100% - 2px), 36% calc(100% - 4px), 35% calc(100% - 1px), 19% 100%, 13% 100%, 12.8% calc(100% - 4px), 12.5% calc(100% - 6px), 12% calc(100% - 3px), 11.5% calc(100% - 4px), 11% calc(100% - 3px), 10.5% calc(100% - 5px), 10% calc(100% - 3px), 9.5% calc(100% - 2px), 9% calc(100% - 3px), 8.5% calc(100% - 6px), 5% calc(100% - 2px), 8px calc(100% - 2px), 6px calc(100% - 3px), 4px calc(100% - 4px), 0 calc(100% - 3px), 0 6px, 2px 4px);
    }";
  }

  // --- Header image style ----------------------------------------------------

  if ( $header_image_style === 'polygon-battered' ) {
    $css .= ':root.header-image-style-polygon-battered .header-background {
      border-radius: 0 !important;
    }
    :root.header-image-style-polygon-battered .header-background__wrapper {
      border-radius: 0 !important;
      clip-path: var(--polygon-battered-half);
    }
    @media only screen and (min-width: 768px) {
      :root.header-image-style-polygon-battered .header-background__wrapper {
        margin-left: 4px;
        margin-right: 4px;
        clip-path: var(--polygon-battered);
      }
    }
    :root.header-image-style-polygon-battered:not(.inset-header-image) .header-background__wrapper {
      --polygon-battered: var(--polygon-battered-half);
      margin-left: 0;
      margin-right: 0;
    }';
  }

  if ( $header_image_style === 'mask-grunge-frame-a-large' ) {
    $css .= ':root.header-image-style-mask-grunge-frame-a-large .header-background {
      border-radius: 0 !important;
    }
    :root.header-image-style-mask-grunge-frame-a-large .header-background__wrapper {
      --f: clamp(48px, 4.0712468193vw + 32.7328244275px, 64px);
      --mi: url("../img/grunge-frame-a/top.png"), url("../img/grunge-frame-a/bottom.png"), var(--data-image-2x2-black);
      --ms: 100% var(--f), 100% var(--f), 100% calc(100% - calc(var(--f) * 2 - 2px));
      --mp: top left, bottom left, top calc(var(--f) - 1px) left 0;
      border-radius: 0 !important;
    }
    @media only screen and (min-width: 1024px) {
      :root.header-image-style-mask-grunge-frame-a-large .header-background__wrapper {
        --mi: url("../img/grunge-frame-a/top-left.png"), url("../img/grunge-frame-a/top.png"), url("../img/grunge-frame-a/top-right.png"), url("../img/grunge-frame-a/right.png"), url("../img/grunge-frame-a/bottom-right.png"), url("../img/grunge-frame-a/bottom.png"), url("../img/grunge-frame-a/bottom-left.png"), url("../img/grunge-frame-a/left.png"), var(--data-image-2x2-black);
        --ms: var(--f) var(--f), calc(100% - calc(var(--f) * 2 - 2px)) var(--f), var(--f) var(--f), var(--f) calc(100% - calc(var(--f) * 2 - 2px)), var(--f) var(--f), calc(100% - calc(var(--f) * 2 - 2px)) var(--f), var(--f) var(--f), var(--f) calc(100% - calc(var(--f) * 2 - 2px)), calc(100% - calc(var(--f) * 2 - 2px)) calc(100% - calc(var(--f) * 2 - 2px));
        --mp: top left, top 0 left calc(var(--f) - 1px), top right, top calc(var(--f) - 1px) right 0, bottom right, bottom 0 right calc(var(--f) - 1px), bottom left, top calc(var(--f) - 1px) left 0, top calc(var(--f) - 1px) left calc(var(--f) - 1px);
      }
    }
    :root.header-image-style-mask-grunge-frame-a-large:not(.inset-header-image) .header-background__wrapper {
      --mi: url("../img/grunge-frame-a/top.png"), url("../img/grunge-frame-a/bottom.png"), var(--data-image-2x2-black);
      --ms: 100% var(--f), 100% var(--f), 100% calc(100% - calc(var(--f) * 2 - 2px));
      --mp: top left, bottom left, top calc(var(--f) - 1px) left 0;
    }';
  }

  if ( $header_image_style === 'mask-grunge-frame-a-small' ) {
    $css .= ':root.header-image-style-mask-grunge-frame-a-small .header-background {
      border-radius: 0 !important;
    }
    :root.header-image-style-mask-grunge-frame-a-small .header-background__wrapper {
      --mi: url("../img/grunge-frame-a/top.png"), url("../img/grunge-frame-a/bottom.png"), var(--data-image-2x2-black);
      --ms: 100% 32px, 100% 32px, 100% calc(100% - 62px);
      --mp: top left, bottom left, top 31px left 0;
      border-radius: 0 !important;
    }
    @media only screen and (min-width: 1024px) {
      :root.header-image-style-mask-grunge-frame-a-small .header-background__wrapper {
        --mi: url("../img/grunge-frame-a/top-left.png"), url("../img/grunge-frame-a/top.png"), url("../img/grunge-frame-a/top-right.png"), url("../img/grunge-frame-a/right.png"), url("../img/grunge-frame-a/bottom-right.png"), url("../img/grunge-frame-a/bottom.png"), url("../img/grunge-frame-a/bottom-left.png"), url("../img/grunge-frame-a/left.png"), var(--data-image-2x2-black);
        --ms: 32px 32px, calc(100% - 62px) 32px, 32px 32px, 32px calc(100% - 62px), 32px 32px, calc(100% - 62px) 32px, 32px 32px, 32px calc(100% - 62px), calc(100% - 62px) calc(100% - 62px);
        --mp: top left, top 0 left 31px, top right, top 31px right 0, bottom right, bottom 0 right 31px, bottom left, top 31px left 0, top 31px left 31px;
      }
    }
    :root.header-image-style-mask-grunge-frame-a-small:not(.inset-header-image) .header-background__wrapper {
      --mi: url("../img/grunge-frame-a/top.png"), url("../img/grunge-frame-a/bottom.png"), var(--data-image-2x2-black);
      --ms: 100% 32px, 100% 32px, 100% calc(100% - 62px);
      --mp: top left, bottom left, top 31px left 0;
    }';
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
  }

  // --- Inset header image ----------------------------------------------------

  if ( get_theme_mod( 'inset_header_image' ) ) {
    $css .= ':root.inset-header-image .header-background {
      left: 50%;
      right: unset;
      width: 100%;
      transform: translate3d(-50%, 0, 0);
    }
    @media only screen and (min-width: 1024px) {
      :root.inset-header-image .header-background {
        border-radius: var(--layout-border-radius-large);
        width: calc(var(--site-width) * 1.5);
        max-width: min(100vw - 2rem, 1980px);
      }
    }
    @media only screen and (min-width: 1024px) {
      :root.inset-header-image .header-background__wrapper {
        border-radius: var(--layout-border-radius-large);
      }
    }';
  }

  // --- Base layout -----------------------------------------------------------

  $hue_offset_dark = (int) get_theme_mod( 'hue_offset', 0 );
  $saturation_offset_dark = (int) get_theme_mod( 'saturation_offset', 0 );
  $lightness_offset_dark = (int) get_theme_mod( 'lightness_offset', 0 );
  $hue_offset_light = (int) get_theme_mod( 'hue_offset_light', 0 );
  $saturation_offset_light = (int) get_theme_mod( 'saturation_offset_light', 0 );
  $lightness_offset_light = (int) get_theme_mod( 'lightness_offset_light', 0 );
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
  $card_grid_column_min = (int) get_theme_mod( 'card_grid_column_min', 308 );
  $card_cover_width_mod = get_theme_mod( 'card_cover_width_mod', 1 );
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

  $css .= ":root {
    --site-width: " . $site_width . "px;
    --hue-offset: " . $hue_offset_dark . "deg;
    --saturation-offset: " . $saturation_offset_dark / 100 . ";
    --lightness-offset: " . $lightness_offset_dark / 100 . ";
    --layout-header-background-height: " . fictioneer_get_css_clamp( $header_image_min, $header_image_max, 320, $site_width ) . ";
    --layout-site-header-height: calc(" . fictioneer_get_css_clamp( $header_min, $header_max, 320, $site_width ) . " - var(--layout-main-inset-top));
    --layout-site-logo-height: " . $logo_height . "px;
    --site-title-font-size: " . fictioneer_get_css_clamp( $title_min, $title_max, 320, $site_width ) . ";
    --site-title-tagline-font-size: " . fictioneer_get_css_clamp( $tagline_min, $tagline_max, 320, $site_width ) . ";
    --grid-columns-min: " . $card_grid_column_min . "px;
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
  }";

  // Only light mode
  $css .= ":root[data-mode=light] {
    --hue-offset: " . $hue_offset_light . "deg;
    --saturation-offset: " . $saturation_offset_light / 100 . ";
    --lightness-offset: " . $lightness_offset_light / 100 . ";
  }";

  // --- Custom layout ---------------------------------------------------------

  if ( get_theme_mod( 'use_custom_layout', false ) ) {
    $vertical_min = (int) get_theme_mod( 'vertical_spacing_min', 24 );
    $vertical_max = (int) get_theme_mod( 'vertical_spacing_max', 48 );
    $horizontal_min = (int) get_theme_mod( 'horizontal_spacing_min', 20 );
    $horizontal_max = (int) get_theme_mod( 'horizontal_spacing_max', 80 );
    $horizontal_small_min = (int) get_theme_mod( 'horizontal_spacing_small_min', 10 );
    $horizontal_small_max = (int) get_theme_mod( 'horizontal_spacing_small_max', 20 );
    $large_border_radius = (int) get_theme_mod( 'large_border_radius', 4 );
    $small_border_radius = (int) get_theme_mod( 'small_border_radius', 2 );

    $css .= ":root, :root[data-theme=base] {
      --layout-spacing-vertical: " . fictioneer_get_css_clamp( $vertical_min, $vertical_max, 480, $site_width ) . ";
      --layout-spacing-horizontal: " . fictioneer_get_css_clamp( $horizontal_min, $horizontal_max, 480, $site_width, '%' ) . ";
      --layout-spacing-horizontal-small: " . fictioneer_get_css_clamp( $horizontal_small_min, $horizontal_small_max, 320, 400, '%' ) . ";
      --layout-border-radius-large: " . $large_border_radius . "px;
      --layout-border-radius-small: " . $small_border_radius . "px;
    }";
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
      --font-weight-ribbon: 700;
      --font-weight-navigation: 400;
      --font-letter-spacing-base: 0em;
    }";
  }

  // --- Dark mode colors ------------------------------------------------------

  $css .= ":root {
    --site-title-heading-color: " . fictioneer_hsl_code( get_theme_mod( 'dark_header_title_color', '#dadde2' ) ) . ";
    --site-title-tagline-color: " . fictioneer_hsl_code( get_theme_mod( 'dark_header_tagline_color', '#dadde2' ) ) . ";
  }";

  if ( get_theme_mod( 'use_custom_dark_mode', false ) ) {
    $css .= ":root, :root[data-theme=base] {
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
  }

  // --- Light mode colors -----------------------------------------------------

  $title_color_light = get_theme_mod( 'light_header_title_color', '#fcfcfd' );
  $tag_color_light = get_theme_mod( 'light_header_tagline_color', '#f3f5f7' );

  if ( in_array( $header_style, ['default', 'overlay'] ) ) {
    $css .= ":root {
      --site-title-heading-color: " . fictioneer_hsl_code( $title_color_light ) . ";
      --site-title-tagline-color: " . fictioneer_hsl_code( $tag_color_light ) . ";
    }";
  } else {
    if ( $title_color_light !== '#fcfcfd' ) {
      $css .= ":root[data-mode=light] {
        --site-title-heading-color: " . fictioneer_hsl_code( $title_color_light ) . ";
      }";
    } else {
      $css .= ":root[data-mode=light] {
        --site-title-heading-color: var(--fg-400);
      }";
    }

    if ( $tag_color_light !== '#f3f5f7' ) {
      $css .= ":root[data-mode=light] {
        --site-title-tagline-color: " . fictioneer_hsl_code( $tag_color_light ) . ";
      }";
    } else {
      $css .= ":root[data-mode=light] {
        --site-title-tagline-color: var(--fg-400);
      }";
    }
  }

  if ( get_theme_mod( 'use_custom_light_mode', false ) ) {
    $css .= ":root[data-mode=light] {
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
      --secant: " . fictioneer_hsl_code( get_theme_mod( 'light_secant', '#e5e7eb' ) ) . ";
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
  }

  // --- Page styles -----------------------------------------------------------

  if ( $page_style === 'polygon-mask-image-battered-ringbook' || $page_style === 'polygon-battered' ) {
    $css .= ':root.page-style-polygon-battered:not(.minimal) .main__background {
      filter: var(--layout-main-drop-shadow);
    }
    :root.page-style-polygon-battered:not(.minimal) .main__background::before {
      clip-path: var(--polygon-battered-half);
    }
    @media only screen and (min-width: 768px) {
      :root.page-style-polygon-battered:not(.minimal) .main__background::before {
        left: 4px;
        right: 4px;
        clip-path: var(--polygon-battered);
      }
    }
    @media only screen and (min-width: 1024px) {
      :root.page-style-polygon-battered:not(.minimal) .main__background::before {
        left: 0;
        right: 0;
      }
    }';
  }

  if ( $page_style === 'polygon-mask-image-battered-ringbook' || $page_style === 'mask-image-ringbook' ) {
    $css .= ':root.page-style-mask-image-ringbook:not(.minimal) .main__background {
      filter: var(--layout-main-drop-shadow);
    }
    @media only screen and (min-width: 375px) {
      :root.page-style-mask-image-ringbook:not(.minimal) .main__background::before {
        --this-left: clamp(-14px, 3.5623409669vw - 27.358778626px, 0px);
        --this-width: clamp(22px, 1.0178117048vw + 18.1832061069px, 26px);
        --mi: url("../img/mask_ringbook.png"), var(--data-image-2x2-black);
        --mp: top 0 left var(--this-left), top 0 left calc(var(--this-width) + var(--this-left) - 1px);
        --ms: var(--this-width) auto, calc(100% - calc(var(--this-width) + var(--this-left) - 1px)) 100%;
        --mr: repeat-y, no-repeat;
        left: 0 !important;
      }
    }
    @media only screen and (min-width: 375px) and (min-width: 640px) {
      :root.page-style-mask-image-ringbook:not(.minimal) .main__background::before {
        border-radius: 0 var(--layout-border-radius-large) var(--layout-border-radius-large) 1.5rem;
      }
    }';
  }

  if ( $page_style === 'polygon-chamfered' ) {
    $css .= ':root.page-style-polygon-chamfered:not(.minimal) .main__background {
      filter: var(--layout-main-drop-shadow);
    }
    :root.page-style-polygon-chamfered:not(.minimal) .main__background::before {
      --m: clamp(6px, 1.3392857143vw + 1.7142857143px, 12px);
      clip-path: polygon(0% var(--m), var(--m) 0%, calc(100% - var(--m)) 0%, 100% var(--m), 100% calc(100% - var(--m)), calc(100% - var(--m)) 100%, var(--m) 100%, 0% calc(100% - var(--m)));
    }';
  }

  if ( $page_style === 'polygon-interface-a' ) {
    $css .= ':root.page-style-polygon-interface-a:not(.minimal) .main__background {
      filter: var(--layout-main-drop-shadow);
    }
    :root.page-style-polygon-interface-a:not(.minimal) .main__background::before {
      --o: clamp(0px, 1.2326656394vw - 4.6224961479px, 8px);
      --m: clamp(6px, 0.4464285714vw + 4.5714285714px, 8px);
      --g: calc(100% - clamp(140px, 40.1785714286vw + 11.4285714286px, 320px));
      --s: clamp(0px, 7.1428571429vw - 22.8571428571px, 32px);
      --p: clamp(92px, 30.8035714286vw - 6.5714285714px, 230px);
      --i: 0px;
      clip-path: polygon(0% calc(var(--o) + var(--m)), var(--o) var(--m), calc(var(--s)) var(--m), calc(var(--m) + var(--s)) 0%, calc(var(--m) + var(--s) + var(--p)) 0%, calc(2 * var(--m) + var(--s) + var(--p)) var(--m), calc(50% - 48px) var(--m), calc(50% - 44px) calc(var(--m) + var(--i)), calc(50% - 36px) calc(var(--m) + var(--i)), calc(50% - 32px) var(--m), calc(50% - 16px) var(--m), calc(50% - 12px) calc(var(--m) + var(--i)), calc(50% - 4px) calc(var(--m) + var(--i)), 50% var(--m), calc(50% + 16px) var(--m), calc(50% + 20px) calc(var(--m) + var(--i)), calc(50% + 28px) calc(var(--m) + var(--i)), calc(50% + 32px) var(--m), var(--g) var(--m), calc(var(--g) + 1 * var(--m)) 0%, calc(var(--g) + 3 * var(--m)) 0%, calc(var(--g) + 2 * var(--m)) var(--m), calc(var(--g) + 2.5 * var(--m)) var(--m), calc(var(--g) + 3.5 * var(--m)) 0%, calc(var(--g) + 5.5 * var(--m)) 0%, calc(var(--g) + 4.5 * var(--m)) var(--m), calc(var(--g) + 5 * var(--m)) var(--m), calc(var(--g) + 6 * var(--m)) 0%, calc(var(--g) + 8 * var(--m)) 0%, calc(var(--g) + 7 * var(--m)) var(--m), calc(var(--g) + 7.5 * var(--m)) var(--m), calc(var(--g) + 8.5 * var(--m)) 0%, calc(var(--g) + 11 * var(--m)) 0%, calc(var(--g) + 11.5 * var(--m)) calc(0.5 * var(--m)), calc(100% - 5.5 * var(--m)) calc(0.5 * var(--m)), calc(100% - 5 * var(--m)) 0%, calc(100% - 1 * var(--m)) 0%, 100% var(--m), 100% calc(100% - 1 * var(--m)), calc(100% - 1 * var(--m)) 100%, var(--m) 100%, 0% calc(100% - 1 * var(--m)));
    }
    @media only screen and (min-width: 1024px) {
      :root.page-style-polygon-interface-a:not(.minimal) .main__background::before {
        --i: 4px;
      }
    }';
  }

  if ( $page_style === 'mask-image-wave-a' ) {
    $css .= ':root.page-style-mask-image-wave-a:not(.minimal) .main__background {
      filter: var(--layout-main-drop-shadow);
    }
    :root.page-style-mask-image-wave-a:not(.minimal) .main__background::before {
      --mp: top calc(-1 * clamp(5px, 0.7633587786vw + 2.1374045802px, 8px)) left 0, top clamp(22px, 2.2900763359vw + 13.4122137405px, 31px) left 0;
      --ms: 100% clamp(28px, 3.3078880407vw + 15.5954198473px, 41px), 100% calc(100% - clamp(22px, 2.2900763359vw + 13.4122137405px, 31px));
      --mr: repeat-x, no-repeat;
      --mi: url(\'data:image/svg+xml,%3Csvg width="100%25" height="100%25" id="svg" viewBox="0 0 1440 690" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" class="transition duration-300 ease-in-out delay-150"%3E%3Cpath d="M 0,700 L 0,262 C 85.6267942583732,192.2488038277512 171.2535885167464,122.49760765550238 260,138 C 348.7464114832536,153.50239234449762 440.6124401913876,254.25837320574163 541,315 C 641.3875598086124,375.74162679425837 750.2966507177035,396.46889952153106 854,374 C 957.7033492822965,351.53110047846894 1056.200956937799,285.86602870813397 1153,260 C 1249.799043062201,234.133971291866 1344.8995215311006,248.06698564593302 1440,262 L 1440,700 L 0,700 Z" stroke="none" stroke-width="0" fill="%23000000" fill-opacity="1" class="transition-all duration-300 ease-in-out delay-150 path-0"%3E%3C/path%3E%3C/svg%3E\'), var(--data-image-2x2-black);
      border-radius: 56px/clamp(6px, 1.5267175573vw + 0.2748091603px, 12px);
    }';
  }

  if ( $page_style === 'mask-image-layered-steps-a' ) {
    $css .= ':root.page-style-mask-image-layered-steps-a:not(.minimal) .main__background {
      filter: var(--layout-main-drop-shadow);
    }
    :root.page-style-mask-image-layered-steps-a:not(.minimal) .main__background::before {
      --mp: top left, top clamp(15px, 2.0356234097vw + 7.3664122137px, 23px) left 0;
      --ms: 100% clamp(16px, 2.0356234097vw + 8.3664122137px, 24px), 100% calc(100% - clamp(15px, 2.0356234097vw + 7.3664122137px, 23px));
      --mr: repeat-x, no-repeat;
      --mi: url(\'data:image/svg+xml,%3Csvg id="visual" viewBox="0 0 900 24" width="900" height="24" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"%3E%3Cpath d="M0 12L39 12L39 4L78 4L78 2L117 2L117 8L157 8L157 9L196 9L196 17L235 17L235 7L274 7L274 15L313 15L313 13L352 13L352 7L391 7L391 19L430 19L430 11L470 11L470 13L509 13L509 19L548 19L548 14L587 14L587 10L626 10L626 6L665 6L665 17L704 17L704 11L743 11L743 2L783 2L783 19L822 19L822 19L861 19L861 2L900 2L900 6L900 25L900 25L861 25L861 25L822 25L822 25L783 25L783 25L743 25L743 25L704 25L704 25L665 25L665 25L626 25L626 25L587 25L587 25L548 25L548 25L509 25L509 25L470 25L470 25L430 25L430 25L391 25L391 25L352 25L352 25L313 25L313 25L274 25L274 25L235 25L235 25L196 25L196 25L157 25L157 25L117 25L117 25L78 25L78 25L39 25L39 25L0 25Z" fill="rgba(0,0,0,0.5)"%3E%3C/path%3E%3Cpath d="M0 10L39 10L39 11L78 11L78 12L117 12L117 16L157 16L157 13L196 13L196 20L235 20L235 16L274 16L274 7L313 7L313 16L352 16L352 12L391 12L391 8L430 8L430 9L470 9L470 12L509 12L509 10L548 10L548 20L587 20L587 12L626 12L626 12L665 12L665 9L704 9L704 11L743 11L743 14L783 14L783 8L822 8L822 13L861 13L861 20L900 20L900 14L900 25L900 25L861 25L861 25L822 25L822 25L783 25L783 25L743 25L743 25L704 25L704 25L665 25L665 25L626 25L626 25L587 25L587 25L548 25L548 25L509 25L509 25L470 25L470 25L430 25L430 25L391 25L391 25L352 25L352 25L313 25L313 25L274 25L274 25L235 25L235 25L196 25L196 25L157 25L157 25L117 25L117 25L78 25L78 25L39 25L39 25L0 25Z" fill="rgba(0,0,0,0.75)"%3E%3C/path%3E%3Cpath d="M0 14L39 14L39 18L78 18L78 20L117 20L117 20L157 20L157 19L196 19L196 15L235 15L235 18L274 18L274 18L313 18L313 15L352 15L352 21L391 21L391 13L430 13L430 20L470 20L470 21L509 21L509 17L548 17L548 18L587 18L587 12L626 12L626 14L665 14L665 21L704 21L704 17L743 17L743 18L783 18L783 14L822 14L822 17L861 17L861 15L900 15L900 16L900 25L900 25L861 25L861 25L822 25L822 25L783 25L783 25L743 25L743 25L704 25L704 25L665 25L665 25L626 25L626 25L587 25L587 25L548 25L548 25L509 25L509 25L470 25L470 25L430 25L430 25L391 25L391 25L352 25L352 25L313 25L313 25L274 25L274 25L235 25L235 25L196 25L196 25L157 25L157 25L117 25L117 25L78 25L78 25L39 25L39 25L0 25Z" fill="%23000000"%3E%3C/path%3E%3C/svg%3E%0A\'), var(--data-image-2x2-black);
    }';
  }

  if ( $page_style === 'mask-image-layered-peaks-a' ) {
    $css .= ':root.page-style-mask-image-layered-peaks-a:not(.minimal) .main__background {
      filter: var(--layout-main-drop-shadow);
    }
    :root.page-style-mask-image-layered-peaks-a:not(.minimal) .main__background::before {
      --mp: top left, top clamp(15px, 2.0356234097vw + 7.3664122137px, 23px) left 0;
      --ms: 100% clamp(16px, 2.0356234097vw + 8.3664122137px, 24px), 100% calc(100% - clamp(15px, 2.0356234097vw + 7.3664122137px, 23px));
      --mr: repeat-x, no-repeat;
      --mi: url(\'data:image/svg+xml,%3Csvg id="visual" viewBox="0 0 900 24" width="900" height="24" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"%3E%3Cpath d="M0 6L39 0L78 0L117 17L157 3L196 14L235 9L274 19L313 14L352 12L391 3L430 6L470 19L509 3L548 12L587 19L626 7L665 12L704 2L743 8L783 17L822 5L861 9L900 1L900 25L861 25L822 25L783 25L743 25L704 25L665 25L626 25L587 25L548 25L509 25L470 25L430 25L391 25L352 25L313 25L274 25L235 25L196 25L157 25L117 25L78 25L39 25L0 25Z" fill="rgba(0,0,0,0.5)"%3E%3C/path%3E%3Cpath d="M0 8L39 17L78 20L117 6L157 13L196 17L235 17L274 7L313 17L352 18L391 21L430 19L470 14L509 15L548 17L587 11L626 18L665 8L704 19L743 17L783 13L822 7L861 6L900 9L900 25L861 25L822 25L783 25L743 25L704 25L665 25L626 25L587 25L548 25L509 25L470 25L430 25L391 25L352 25L313 25L274 25L235 25L196 25L157 25L117 25L78 25L39 25L0 25Z" fill="rgba(0,0,0,0.75)"%3E%3C/path%3E%3Cpath d="M0 12L39 14L78 17L117 14L157 16L196 17L235 19L274 20L313 20L352 16L391 16L430 12L470 22L509 22L548 17L587 13L626 11L665 18L704 13L743 23L783 21L822 16L861 17L900 21L900 25L861 25L822 25L783 25L743 25L704 25L665 25L626 25L587 25L548 25L509 25L470 25L430 25L391 25L352 25L313 25L274 25L235 25L196 25L157 25L117 25L78 25L39 25L0 25Z" fill="%23000000"%3E%3C/path%3E%3C/svg%3E%0A\'), var(--data-image-2x2-black);
    }';
  }

  if ( $page_style === 'mask-image-grunge-a' ) {
    $css .= '.main__background::before {
      --c: clamp(17px, 2.0356234097vw + 9.3664122137px, 25px);
      --d: clamp(20px, 2.0356234097vw + 12.3664122137px, 28px);
      --mi: url("../img/grunge-top-a.svg"), var(--data-image-2x2-black), url("../img/flipped-grunge-top-a.svg");
      --ms: 100% var(--d), 100% calc(100% - var(--c) * 2), 100% var(--d);
      --mp: top calc(-1 * clamp(0px, 0.5089058524vw - 1.9083969466px, 2px)) left 0, top var(--c) left 0, bottom left;
    }';
  }

  // --- Page shadow -----------------------------------------------------------

  if ( ! get_theme_mod( 'page_shadow' ) ) {
    $css .= ':root.no-page-shadow {
      --layout-main-box-shadow: none;
      --minimal-main-box-shadow: none;
      --layout-main-drop-shadow: none;
    }';
  }

  // --- Content list style ----------------------------------------------------

  if ( $content_list_style === 'full' ) {
    $css .= ':root:not(.minimal) .content-list-style-full {
      --content-list-item-background: var(--content-list-item-background-full);
    }
    :root:not(.minimal) .content-list-style-full .chapter-group__list-item {
      padding: 0.5rem 8px 0.5rem 6px;
    }
    :root:not(.minimal) .content-list-style-full .story__blog-list-item {
      padding: 0.5rem 8px;
    }
    :root:not(.minimal) .content-list-style-full .card__link-list-item {
      padding-left: 0.35em;
    }';
  }

  if ( $content_list_style === 'free' ) {
    $css .= ':is(:root, :root.minimal) .content-list-style-free {
      --content-list-item-background: none;
    }
    :is(:root, :root.minimal) .content-list-style-free :is(.chapter-group__list-item, .story__blog-list-item) {
      border-radius: 0;
      border-bottom: none;
    }';
  }

  if ( $content_list_style === 'lines' ) {
    $css .= ':is(:root, :root.minimal) .content-list-style-lines {
      --content-list-item-background: none;
    }
    :is(:root, :root.minimal) .content-list-style-lines :is(.chapter-group__list-item, .story__blog-list-item, .card__link-list-item) {
      border-radius: 0;
      border-bottom: 1.5px solid var(--layout-color-separator);
    }
    :is(:root, :root.minimal) .content-list-style-lines .card__link-list-item:last-child {
      border-bottom: none;
    }';
  }

  // --- Filters ---------------------------------------------------------------

  $css = apply_filters( 'fictioneer_filter_pre_build_customize_css', $css );

  // --- Minify ----------------------------------------------------------------

  $css = fictioneer_minify_css( $css );

  // --- Update options --------------------------------------------------------

  if ( $content !== 'preview' ) {
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

?>
