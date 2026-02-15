<?php

namespace Fictioneer;

defined( 'ABSPATH' ) OR exit;

final class Utils_Admin {
  /**
   * Return array of adjectives for randomized username generation.
   *
   * @since 5.19.0
   * @since 5.34.0 - Moved into Utils_Admin class.
   *
   * @return string[] Array of adjectives.
   */

  public static function get_username_adjectives() : array {
    static $adjectives = array(
      'Radical', 'Tubular', 'Gnarly', 'Epic', 'Electric', 'Neon', 'Bodacious', 'Rad',
      'Totally', 'Funky', 'Wicked', 'Fresh', 'Chill', 'Groovy', 'Vibrant', 'Flashy',
      'Buff', 'Hella', 'Motor', 'Cyber', 'Pixel', 'Holo', 'Stealth', 'Synthetic',
      'Enhanced', 'Synth', 'Bio', 'Laser', 'Virtual', 'Analog', 'Mega', 'Wave', 'Solo',
      'Retro', 'Quantum', 'Robotic', 'Digital', 'Hyper', 'Punk', 'Giga', 'Electro',
      'Chrome', 'Fusion', 'Vivid', 'Stellar', 'Galactic', 'Turbo', 'Atomic', 'Cosmic',
      'Artificial', 'Kinetic', 'Binary', 'Hypersonic', 'Runic', 'Data', 'Knightly',
      'Cryonic', 'Nebular', 'Golden', 'Silver', 'Red', 'Crimson', 'Augmented', 'Vorpal',
      'Ascended', 'Serious', 'Solid', 'Master', 'Prism', 'Spinning', 'Masked', 'Hardcore',
      'Somber', 'Celestial', 'Arcane', 'Luminous', 'Ionized', 'Lunar', 'Uncanny', 'Subatomic',
      'Luminary', 'Radiant', 'Ultra', 'Starship', 'Space', 'Starlight', 'Interstellar', 'Metal',
      'Bionic', 'Machine', 'Isekai', 'Warp', 'Neo', 'Alpha', 'Power', 'Unhinged', 'Ash',
      'Savage', 'Silent', 'Screaming', 'Misty', 'Rending', 'Horny', 'Dreadful', 'Bizarre',
      'Chaotic', 'Wacky', 'Twisted', 'Manic', 'Crystal', 'Infernal', 'Ruthless', 'Grim',
      'Mortal', 'Forsaken', 'Heretical', 'Cursed', 'Blighted', 'Scarlet', 'Delightful',
      'Nuclear', 'Azure', 'Emerald', 'Amber', 'Mystic', 'Ethereal', 'Enchanted', 'Valiant',
      'Fierce', 'Obscure', 'Enigmatic', 'Blazing', 'Velocity', 'Phantom', 'Razor', 'Spectral',
      'Overclocked', 'Flux', 'Pulse', 'Limitless', 'Neural', 'Ciphered', 'Encrypted',
      'Void', 'Abyssal', 'Harrowed', 'Doomed', 'Nightbound', 'Umbral', 'Tenebrous', 'Dire',
      'Baleful', 'Malevolent', 'Graveborn', 'Ashen', 'Mythic', 'Legendary', 'Chosen', 'Exalted',
      'Anointed', 'Venerated', 'Hallowed', 'Imperial', 'Regal', 'Oathbound', 'Valorous', 'Eminent',
      'Unstable', 'Dubious', 'Dreaming', 'Wandering', 'Starry', 'Moonlit', 'Petalsoft', 'Fated',
      'Forgotten', 'Humming', 'Echoing', 'Velvet', 'Federal', 'Awesome', 'Succulent'
    );

    return apply_filters( 'fictioneer_random_username_adjectives', $adjectives );
  }

  /**
   * Return array of nouns for randomized username generation.
   *
   * @since 5.19.0
   * @since 5.34.0 - Moved into Utils_Admin class.
   *
   * @return string[] Array of nouns.
   */

  public static function get_username_nouns() : array {
    static $nouns = array(
      'Avatar', 'Cassette', 'Rubiks', 'Gizmo', 'Synthwave', 'Tron', 'Replicant', 'Warrior',
      'Hacker', 'Samurai', 'Cyborg', 'Runner', 'Mercenary', 'Shogun', 'Maverick', 'Glitch',
      'Byte', 'Matrix', 'Motion', 'Shinobi', 'Circuit', 'Droid', 'Virus', 'Vortex', 'Mech',
      'Codex', 'Hologram', 'Specter', 'Intelligence', 'Technomancer', 'Rider', 'Ghost',
      'Hunter', 'Hound', 'Wizard', 'Knight', 'Rogue', 'Scout', 'Ranger', 'Paladin', 'Sorcerer',
      'Mage', 'Artificer', 'Cleric', 'Tank', 'Fighter', 'Pilot', 'Necromancer', 'Neuromancer',
      'Barbarian', 'Streetpunk', 'Phantom', 'Shaman', 'Druid', 'Dragon', 'Dancer', 'Captain',
      'Pirate', 'Snake', 'Rebel', 'Kraken', 'Spark', 'Blitz', 'Alchemist', 'Dragoon', 'Geomancer',
      'Neophyte', 'Terminator', 'Tempest', 'Enigma', 'Automaton', 'Daemon', 'Juggernaut',
      'Paragon', 'Sentinel', 'Viper', 'Velociraptor', 'Spirit', 'Punk', 'Synth', 'Biomech',
      'Engineer', 'Pentagoose', 'Vampire', 'Soldier', 'Chimera', 'Lobotomy', 'Mutant',
      'Revenant', 'Wraith', 'Chupacabra', 'Banshee', 'Fae', 'Leviathan', 'Cenobite', 'Bob',
      'Ketchum', 'Collector', 'Student', 'Lover', 'Chicken', 'Alien', 'Titan', 'Sinner',
      'Nightmare', 'Bioplague', 'Annihilation', 'Elder', 'Priest', 'Guardian', 'Quagmire',
      'Berserker', 'Oblivion', 'Decimator', 'Devastation', 'Calamity', 'Doom', 'Ruin', 'Abyss',
      'Heretic', 'Armageddon', 'Obliteration', 'Inferno', 'Torment', 'Carnage', 'Purgatory',
      'Chastity', 'Angel', 'Raven', 'Star', 'Trinity', 'Idol', 'Eidolon', 'Havoc', 'Nirvana',
      'Digitron', 'Phoenix', 'Lantern', 'Warden', 'Falcon', 'Mainframe', 'Datacore', 'Proxy',
      'Netrunner', 'Assembler', 'Drone', 'Striker', 'Breaker', 'Enforcer', 'Vanguard', 'Reaper',
      'Bulwark', 'Skirmisher', 'Blademaster', 'Gunrunner', 'Sharpshooter', 'Interceptor', 'Anvil',
      'Hammer', 'Spellblade', 'Battlemage', 'Archon', 'Invoker', 'Thaumaturge', 'Loremaster',
      'Runesmith', 'Arcanist', 'Spellweaver', 'Chronomancer', 'Voidcaller', 'Soulbinder',
      'Starborn', 'Oathkeeper', 'Harbinger', 'Endbringer', 'Bloodmoon', 'Behemoth', 'Colossus',
      'Direwolf', 'Hellkite', 'Wyvern', 'Griffon', 'Hydra', 'Manticore', 'Basilisk', 'Watcher',
      'Crawler', 'Stalker', 'Aberration', 'Parasite', 'Husk', 'Fixer', 'Operator', 'Wildcard',
      'Outlaw', 'Renegade', 'Freerunner', 'Troublemaker', 'Instigator', 'Disruptor', 'Provocateur',
      'Saboteur', 'Firestarter', 'Anarch', 'Nightowl', 'Dreamer', 'Drifter', 'Wanderer', 'Stargazer',
      'Moonchild', 'Menace', 'Mistake', 'Liability', 'Disaster', 'Raccoon', 'Cryptid', 'Placeholder'
    );

    return apply_filters( 'fictioneer_random_username_nouns', $nouns );
  }

  /**
   * Return randomized username.
   *
   * @since 5.19.0
   * @since 5.34.0 - Moved into Utils_Admin class.
   *
   * @param bool $unique  Optional. Whether the username must be unique. Default true.
   *
   * @return string Sanitized random username.
   */

  public static function get_random_username( $unique = true ) : string {
    $adjectives = self::get_username_adjectives();
    $nouns = self::get_username_nouns();

    do {
      $username = $adjectives[ array_rand( $adjectives ) ] . $nouns[ array_rand( $nouns ) ] . wp_rand( 1000, 9999 );
      $username = sanitize_user( $username, true );

      if ( ! $unique ) {
        break;
      }
    } while ( username_exists( $username ) );

    return $username;
  }

  /**
   * Return associative array of theme colors.
   *
   * Notes: Considers both parent and child theme.
   *
   * @since 5.21.2
   * @since 5.34.0 - Refactored and moved into Utils_Admin class.
   *
   * @return array Associative array of theme colors.
   */

  public static function get_theme_colors() : array {
    static $colors = null;

    if ( $colors !== null ) {
      return $colors;
    }

    $read_json = static function( string $file ) : array {
      if ( ! is_readable( $file ) ) {
        return [];
      }

      $raw = file_get_contents( $file );

      if ( $raw === false || $raw === '' ) {
        return [];
      }

      $data = json_decode( $raw, true );

      return is_array( $data ) ? $data : [];
    };

    $parent_file = get_parent_theme_file_path( 'includes/colors.json' );
    $child_file  = get_theme_file_path( 'includes/colors.json' );

    $parent = $read_json( $parent_file );

    if ( $child_file && $child_file !== $parent_file ) {
      $child = $read_json( $child_file );
    } else {
      $child = [];
    }

    $colors = array_merge( $parent, $child );

    return $colors;
  }

  /**
   * Return theme color mod with default fallback.
   *
   * @since 5.12.0
   * @since 5.21.2 - Refactored with theme colors helper function.
   * @since 5.34.0 - Moved into Utils_Admin class.
   *
   * @param string      $mod      Requested theme color.
   * @param string|null $default  Optional. Default color code.
   *
   * @return string Requested color code or '#ff6347' (tomato) if not found.
   */

  public static function get_theme_color( $mod, $default = null ) : string {
    $colors = self::get_theme_colors();
    $default = $default ?? $colors[ $mod ]['hex'] ?? '#ff6347'; // Tomato

    return get_theme_mod( $mod, $default );
  }

  /**
   * Convert hex color to RGB array.
   *
   * @license MIT
   * @author Simon Waldherr https://github.com/SimonWaldherr
   *
   * @since 4.7.0
   * @since 5.34.0 - Moved into Utils_Admin class.
   * @link https://github.com/SimonWaldherr/ColorConverter.php
   *
   * @param string $value  The to be converted hex (six digits).
   *
   * @return array|bool RGB values as array or false on failure.
   */

  public static function hex_to_rgb( $value ) {
    if ( substr( trim( $value ), 0, 1 ) === '#' ) {
      $value = substr( $value, 1 );
    }

    if ( ( strlen( $value ) < 2) || ( strlen( $value ) > 6 ) ) {
      return false;
    }

    $values = str_split( $value );

    if ( strlen( $value ) === 2 ) {
      $r = intval( $values[0] . $values[1], 16 );
      $g = $r;
      $b = $r;
    } else if ( strlen( $value ) === 3 ) {
      $r = intval( $values[0], 16 );
      $g = intval( $values[1], 16 );
      $b = intval( $values[2], 16 );
    } else if ( strlen( $value ) === 6 ) {
      $r = intval( $values[0] . $values[1], 16 );
      $g = intval( $values[2] . $values[3], 16 );
      $b = intval( $values[4] . $values[5], 16 );
    } else {
      return false;
    }

    return array( $r, $g, $b );
  }

  /**
   * Convert RGB color array to HSL.
   *
   * @license MIT
   * @author Simon Waldherr https://github.com/SimonWaldherr
   *
   * @since 4.7.0
   * @since 5.34.0 - Moved into Utils_Admin class.
   * @link https://github.com/SimonWaldherr/ColorConverter.php
   *
   * @param array $value      To be converted RGB array (r, g, b).
   * @param int   $precision  Optional. Rounding precision. Default 0.
   *
   * @return array HSL values as array.
   */

  public static function rgb_to_hsl( $value, $precision = 0 ) : array {
    $r = max( min( intval( $value[0], 10 ) / 255, 1 ), 0 );
    $g = max( min( intval( $value[1], 10 ) / 255, 1 ), 0 );
    $b = max( min( intval( $value[2], 10 ) / 255, 1 ), 0 );
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

  /**
   * Convert a hex color to a Fictioneer HSL code.
   *
   * @since 4.7.0
   * @since 5.34.0 - Moved into Utils_Admin class.
   *
   * @param string $hex     Hex color.
   * @param string $output  Switch output style. Default 'default'.
   *
   * @return string Converted HSL code.
   */

  public static function get_hsl_code( $hex, $output = 'default' ) : string {
    if ( ! is_string( $hex ) || ! preg_match( '/^#?(?:[a-fA-F0-9]{3}|[a-fA-F0-9]{6}|[a-fA-F0-9]{8})$/', $hex ) ) {
      return $hex;
    }

    $hsl_array = self::rgb_to_hsl( self::hex_to_rgb( $hex ) ?: [0, 0, 0], 2 );

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

  /**
   * Convert a hex color to an HSL font code.
   *
   * @since 4.7.0
   * @since 5.34.0 - Moved into Utils_Admin class.
   *
   * @param string $hex  Hex color.
   *
   * @return string Converted HSL font code.
   */

  public static function get_hsl_font_code( $hex ) : string {
    $hsl_array = self::rgb_to_hsl( self::hex_to_rgb( $hex ) ?: [0, 0, 0], 2 );

    $deg = 'calc(' . $hsl_array[0] . 'deg + var(--hue-rotate))';
    $saturation = 'max(calc(' . $hsl_array[1] . '% * (var(--font-saturation) + var(--saturation) - 1)), 0%)';
    $lightness = 'clamp(0%, calc(' . $hsl_array[2] . '% * var(--font-lightness, 1)), 100%)';

    return "hsl($deg $saturation $lightness)";
  }

  /**
   * Return a font family value.
   *
   * @since 5.10.0
   * @since 5.34.0 - Moved into Utils_Admin class.
   *
   * @param string $option        Name of the theme mod.
   * @param string $font_default  Fallback font.
   * @param string $mod_default   Default for get_theme_mod().
   *
   * @return string Ready to use font family value.
   */

  public static function get_font_family( $option, $font_default, $mod_default ) : string {
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

  /**
   * Return fonts data from a Google Fonts link.
   *
   * @since 5.10.0
   * @since 5.34.0 - Moved into Utils_Admin class.
   *
   * @param string $link  Google Fonts link.
   *
   * @return array|false|null Font data if successful, false if malformed,
   *                          null if not a valid Google Fonts link.
   */

  public static function extract_font_from_google_link( $link ) {
    $link = trim( $link );

    if ( preg_match( '#^https://fonts\.googleapis\.com/css2(?:\?|$)#i', $link ) !== 1 ) {
      return null; // Not a Google Fonts link
    }

    $parts = wp_parse_url( $link );

    if ( ! is_array( $parts ) || empty( $parts['query'] ) ) {
      return false;
    }

    if ( preg_match_all( '/(?:^|&)family=([^&]+)/', (string) $parts['query'], $m ) !== 1 ) {
      return false; // Reject multiple 'family='
    }

    $family_raw = trim( (string) $m[1][0] );

    if ( $family_raw === '' ) {
      return false;
    }

    $family_decoded = rawurldecode( str_replace( '+', ' ', $family_raw ) );

    $name = trim( strtok( $family_decoded, ':' ) );

    if ( $name === '' ) {
      return false;
    }

    $font = array(
      'google_link' => $link,
      'skip' => true,
      'chapter' => true,
      'version' => '',
      'key' => sanitize_title( $name ),
      'name' => $name,
      'family' => $name,
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

    $weights = [];
    $is_italic = false;

    if ( preg_match( '/:ital,wght@([0-9,;]+)/', $family_decoded, $ital_weight_matches ) === 1 ) {
      foreach ( explode( ';', $ital_weight_matches[1] ) as $spec ) {
        $pair = explode( ',', $spec, 2 );

        if ( count( $pair ) !== 2 ) {
          continue;
        }

        list( $ital, $weight ) = $pair;

        if ( $ital === '1' ) {
          $is_italic = true;
        }

        $weights[ (string) $weight ] = true;
      }
    } elseif ( preg_match( '/:wght@([0-9;]+)/', $family_decoded, $ital_weight_matches ) === 1 ) {
      foreach ( explode( ';', $ital_weight_matches[1] ) as $weight ) {
        $weights[ (string) $weight ] = true;
      }
    }

    if ( $is_italic ) {
      $font['styles'][] = 'italic';
    }

    if ( $weights ) {
      $font['weights'] = array_keys( $weights );
    }

    return $font;
  }

  /**
   * Return fonts included by the theme.
   *
   * Note: If a font.json contains a { "remove": true } node, the font will not
   * be added to the result array and therefore removed from the site.
   *
   * @since 5.10.0
   * @since 5.34.0 - Moved into Utils_Admin class.
   *
   * @return array Array of font data. Keys: skip, chapter, version, key, name,
   *               family, type, styles, weights, charsets, formats, about, note,
   *               sources, css_path, css_file, and in_child_theme.
   */

  public static function get_font_data() : array {
    $extract_font_data = static function( string $font_dir, bool $in_child_theme ) : array {
      if ( ! is_dir( $font_dir ) ) {
        return [];
      }

      $out = [];

      foreach ( array_diff( scandir( $font_dir ), [ '.', '..' ] ) as $folder ) {
        $full_path = $font_dir . '/' . $folder;

        if ( ! is_dir( $full_path ) ) {
          continue;
        }

        $json_file = $full_path . '/font.json';
        $css_file = $full_path . '/font.css';

        if ( ! is_readable( $json_file ) || ! is_readable( $css_file ) ) {
          continue;
        }

        $raw = file_get_contents( $json_file );

        if ( $raw === false || $raw === '' ) {
          continue;
        }

        $data = json_decode( $raw, true );

        if ( ! is_array( $data ) || ! empty( $data['remove'] ) ) {
          continue;
        }

        if ( empty( $data['key'] ) || ! is_string( $data['key'] ) ) {
          continue;
        }

        $key = sanitize_key( $data['key'] );

        if ( $key === '' ) {
          continue;
        }

        $folder_name = basename( (string) $folder );

        $data['dir'] = "/fonts/{$folder_name}";
        $data['css_path'] = "/fonts/{$folder_name}/font.css";
        $data['css_file'] = $css_file;
        $data['in_child_theme'] = $in_child_theme;

        $out[ $key ] = $data;
      }

      return $out;
    };

    $parent_font_dir = trailingslashit( get_template_directory() ) . 'fonts';
    $child_font_dir = trailingslashit( get_stylesheet_directory() ) . 'fonts';

    $fonts = $extract_font_data( $parent_font_dir, false );

    if ( $child_font_dir !== $parent_font_dir ) {
      $fonts = array_merge( $fonts, $extract_font_data( $child_font_dir, true ) );
    }

    $google_fonts_links = get_option( 'fictioneer_google_fonts_links' );

    if ( is_string( $google_fonts_links ) && trim( $google_fonts_links ) !== '' ) {
      foreach ( preg_split( "/\R/u", trim( $google_fonts_links ) ) ?: [] as $link ) {
        $link = trim( $link );

        if ( $link === '' ) {
          continue;
        }

        $font = Utils::extract_font_from_google_link( $link );

        if ( is_array( $font ) && ! empty( $font['key'] ) ) {
          $fonts[ $font['key'] ] = $font;
        }
      }
    }

    return apply_filters( 'fictioneer_filter_font_data', $fonts );
  }

  /**
   * Build bundled font stylesheet.
   *
   * @since 5.10.0
   * @since 5.34.0 - Moved into Utils_Admin class.
   *
   * @return array Font stack.
   */

  public static function bundle_fonts() : array {
    $fonts = apply_filters( 'fictioneer_filter_pre_build_bundled_fonts', self::get_font_data() );

    $disabled_fonts = Utils::get_disabled_fonts();
    $parent_uri = trailingslashit( get_template_directory_uri() );
    $child_uri = trailingslashit( get_stylesheet_directory_uri() );
    $combined_css = '';
    $font_stack = [];

    $base_file = get_parent_theme_file_path( 'css/fonts-base.css' );

    if ( is_readable( $base_file ) ) {
      $css = file_get_contents( $base_file );

      if ( $css !== false && $css !== '' ) {
        $combined_css .= str_replace( '../fonts/', $parent_uri . 'fonts/', $css );
      }
    }

    foreach ( $fonts as $key => $font ) {
      if ( in_array( $key, $disabled_fonts, true ) ) {
        continue;
      }

      if ( ! empty( $font['chapter'] ) ) {
        $font_stack[ $font['key'] ?? $key ] = array(
          'css' => Utils::get_font_family_value( $font['family'] ?? '' ),
          'name' => $font['name'] ?? '',
          'alt' => $font['alt'] ?? ''
        );
      }

      if ( ! empty( $font['skip'] ) || ! empty( $font['google_link'] ) ) {
        continue;
      }

      $css_file = $font['css_file'] ?? '';

      if ( ! is_string( $css_file ) || $css_file === '' || ! is_readable( $css_file ) ) {
        continue;
      }

      $css = file_get_contents( $css_file );

      if ( empty( $css ) ) {
        continue;
      }

      $uri = empty( $font['in_child_theme'] ) ? $parent_uri : $child_uri;
      $combined_css .= str_replace( '../fonts/', $uri . 'fonts/', $css );
    }

    update_option( 'fictioneer_chapter_fonts', $font_stack, true );
    update_option( 'fictioneer_bundled_fonts_timestamp', time(), true );

    $save_path = Utils::get_cache_dir( 'build_bundled_fonts' ) . 'bundled-fonts.css';

    if ( file_put_contents( $save_path, $combined_css ) === false ) {
      error_log( '[Fictioneer] Failed to write bundled fonts CSS: ' . $save_path );
    }

    return $font_stack;
  }

  /**
   * Check whether an URL exists.
   *
   * @since 4.0.0
   * @since 5.34.0 - Moved into Utils_Admin class.
   *
   * @param string $url  The URL to check.
   *
   * @return bool True if the URL exists and false otherwise. Probably.
   */

  public static function url_exists( $url ) : bool {
    if ( empty( $url ) ) {
      return false;
    }

    $response = wp_remote_head( $url, array( 'timeout' => 2, 'redirection' => 0 ) );

    if ( is_wp_error( $response ) ) {
      return false;
    }

    $statusCode = wp_remote_retrieve_response_code( $response );

    return ( $statusCode >= 200 && $statusCode < 300 );
  }

  /**
   * Return word count of a post.
   *
   * @since 5.25.0
   * @since 5.30.0 - Fixed for accuracy (hopefully).
   * @since 5.34.0 - Moved into Utils_Admin class.
   *
   * @param int         $post_id  ID of the post to count the words of.
   * @param string|null $content  Optional. The post content. Queries the field by default.
   *
   * @return int The word count.
   */

  public static function count_words( $post_id, $content = null ) : int {
    // Prepare
    $content = $content ?? get_post_field( 'post_content', $post_id ) ?: '';
    $content = strip_shortcodes( $content );
    $content = strip_tags( $content );
    $content = html_entity_decode( $content, ENT_QUOTES | ENT_HTML5 );
    $content = preg_replace( '/[‐–—―‒−⁃]/u', ' - ', $content );

    preg_match_all(
      "/\b\p{L}[\p{L}\p{N}'’]*(?:-\p{L}[\p{L}\p{N}'’]*)*\b/u",
      $content,
      $matches
    );

    return count( $matches[0] );
  }

  /**
   * Get the current user after performing AJAX validations.
   *
   * @since 5.0.0
   * @since 5.34.0 - Moved into Utils_Admin class.
   *
   * @param string $nonce_name   Optional. The name of the nonce. Default 'nonce'.
   * @param string $nonce_value  Optional. The value of the nonce. Default 'fictioneer_nonce'.
   *
   * @return boolean|WP_User False if not valid, the current user object otherwise.
   */

  public static function get_validated_ajax_user( $nonce_name = 'nonce', $nonce_value = 'fictioneer_nonce' ) {
    $user = wp_get_current_user();

    if (
      ! $user->exists() ||
      ! check_ajax_referer( $nonce_value, $nonce_name, false )
    ) {
      return false;
    }

    return $user;
  }

  /**
   * Update post meta fields in bulk for a post.
   *
   * If the meta value is truthy, the meta field is updated as normal.
   * If not, the meta field is deleted instead to keep the database tidy.
   * Fires default WP hooks where possible.
   *
   * @since 5.27.4
   * @since 5.34.0 - Moved into Utils_Admin class.
   * @link https://developer.wordpress.org/reference/functions/update_metadata/
   * @link https://developer.wordpress.org/reference/functions/add_metadata/
   * @link https://developer.wordpress.org/reference/functions/delete_metadata/
   *
   * @param int   $post_id  Post ID.
   * @param array $fields   Associative array of field keys and sanitized (!) values.
   */

  public static function bulk_update_post_meta( $post_id, $fields ) : void {
    if ( empty( $fields ) ) {
      return;
    }

    global $wpdb;

    // Deal with magic quotes
    if ( fictioneer_has_magic_quotes() ) {
      $fields = array_map( 'wp_unslash', $fields );
    }

    // Setup
    $existing_meta = [];
    $update_parts = [];
    $update_keys = [];
    $update_values = [];
    $insert_parts = [];
    $insert_values = [];
    $delete_keys = [];
    $deleted_meta_fields = [];

    // Fetch existing meta keys and values
    $meta_results = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT meta_id, meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id = %d",
        $post_id
      )
    );

    foreach ( $meta_results as $meta ) {
      $existing_meta[ $meta->meta_key ] = array(
        'meta_id' => $meta->meta_id,
        'meta_value' => $meta->meta_value
      );
    }

    // Prepare
    $allowed_meta_keys = self::get_falsy_meta_allow_list();

    foreach ( $fields as $key => $value ) {
      // Mark for deletion...
      if ( empty( $value ) && ! in_array( $key, $allowed_meta_keys ) ) {
        $delete_keys[] = $key;

        if ( isset( $existing_meta[ $key ] ) ) {
          $deleted_meta_fields[ $existing_meta[ $key ]['meta_id'] ] = [ $key, $value ];

          do_action( 'delete_post_meta', [ $existing_meta[ $key ]['meta_id'] ], $post_id, $key, $value );
          do_action( 'delete_postmeta', [ $existing_meta[ $key ]['meta_id'] ] );
        }

        continue;
      }

      // Serialize if necessary
      $prepared_value = is_array( $value ) ? maybe_serialize( $value ) : $value;

      if ( isset( $existing_meta[ $key ] ) ) {
        // Mark for updating...
        if ( $existing_meta[ $key ]['meta_value'] !== $prepared_value ) {
          $update_parts[] = "WHEN meta_key = %s THEN %s";
          $update_keys[] = $key;
          $update_values[] = $key;
          $update_values[] = $prepared_value;

          do_action( 'update_post_meta', $existing_meta[ $key ]['meta_id'], $post_id, $key, $value );
          do_action( 'update_postmeta', $existing_meta[ $key ]['meta_id'], $post_id, $key, $prepared_value );
        }
      } else {
        // Mark for insertion...
        $insert_parts[] = "(%d, %s, %s)";
        $insert_values[] = $post_id;
        $insert_values[] = $key;
        $insert_values[] = $prepared_value;

        do_action( 'add_post_meta', $post_id, $key, $value );
      }
    }

    // DELETE
    if ( ! empty( $delete_keys ) ) {
      $delete_query =
        "DELETE FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key IN (" .
        implode( ', ', array_fill( 0, count( $delete_keys ), '%s' ) ) . ")";

      $wpdb->query( $wpdb->prepare( $delete_query, $post_id, ...$delete_keys ) );

      if ( ! empty( $deleted_meta_fields ) ) {
        foreach ( $deleted_meta_fields as $key => $tuple ) {
          do_action( 'deleted_post_meta', [ $key ], $post_id, $tuple[0], $tuple[1] );
          do_action( 'deleted_postmeta', [ $key ] );
        }
      }
    }

    // UPDATE
    if ( ! empty( $update_parts ) ) {
      $update_query =
        "UPDATE {$wpdb->postmeta}
        SET meta_value = CASE " . implode( ' ', $update_parts ) . " END
        WHERE post_id = %d AND meta_key IN (" . implode( ', ', array_fill( 0, count( $update_keys ), '%s' ) ) . ")";

      $update_values[] = $post_id;
      $update_values = array_merge( $update_values, $update_keys );

      $wpdb->query( $wpdb->prepare( $update_query, ...$update_values ) );

      foreach ( $fields as $key => $value ) {
        if ( in_array( $key, $update_keys ) && isset( $existing_meta[ $key ] ) ) {
          $prepared_value = is_array( $value ) ? maybe_serialize( $value ) : $value;

          do_action( 'updated_post_meta', $existing_meta[ $key ]['meta_id'], $post_id, $key, $value );
          do_action( 'updated_postmeta', $existing_meta[ $key ]['meta_id'], $post_id, $key, $prepared_value );
        }
      }
    }

    // INSERT
    if ( ! empty( $insert_parts ) ) {
      $insert_query =
        "INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value)
        VALUES " . implode( ', ', $insert_parts );

      $wpdb->query( $wpdb->prepare( $insert_query, ...$insert_values ) );

      // Does not return the meta IDs, added_post_meta cannot be fired.
    }

    // Cache cleanup
    wp_cache_delete( $post_id, 'post_meta' );
  }

  /**
   * Check whether there any added chapters are to be considered "new".
   *
   * @since 5.26.0
   * @since 5.34.0 - Moved into Utils_Admin class.
   *
   * @global wpdb $wpdb  WordPress database object.
   *
   * @param int   $story_id              Story ID.
   * @param int[] $chapter_ids           Current array of chapter IDs.
   * @param int[] $previous_chapter_ids  Previous array of chapter IDs.
   *
   * @return bool True if new chapters, false otherwise.
   */

  public static function has_new_story_chapters( $story_id, $chapter_ids, $previous_chapter_ids ) : bool {
    global $wpdb;

    $chapter_diff = array_diff( $chapter_ids, $previous_chapter_ids );

    if ( empty( $chapter_diff ) ) {
      return false;
    }

    $allowed_statuses = apply_filters(
      'fictioneer_filter_chapters_added_statuses',
      ['publish'],
      $story_id
    );

    $chapter_placeholders = implode( ',', array_fill( 0, count( $chapter_diff ), '%d' ) );
    $status_placeholders = implode( ',', array_fill( 0, count( $allowed_statuses ), '%s' ) );

    $sql =
      "SELECT p.ID
      FROM {$wpdb->posts} p
      WHERE p.post_type = 'fcn_chapter'
        AND p.ID IN ($chapter_placeholders)
        AND p.post_status IN ($status_placeholders)
      LIMIT 1";

    $query = $wpdb->prepare( $sql, ...$chapter_diff, ...$allowed_statuses );

    $new_chapters = $wpdb->get_col( $query );

    return ! empty( $new_chapters );
  }

  /**
   * Return story IDs where the user is a co-author.
   *
   * @since 5.26.0
   * @since 5.34.0 - Moved into Utils_Admin class.
   *
   * @global wpdb $wpdb  WordPress database object.
   *
   * @param int $author_id  User ID.
   *
   * @return int[] Array of story IDs.
   */

  public static function get_co_authored_story_ids( $author_id ) : array {
    static $cache = [];

    if ( isset( $cache[ $author_id ] ) ) {
      return $cache[ $author_id ];
    }

    global $wpdb;

    $story_ids = $wpdb->get_col(
      $wpdb->prepare(
        "SELECT post_id
        FROM {$wpdb->postmeta}
        WHERE meta_key = 'fictioneer_story_co_authors'
        AND meta_value LIKE %s",
        '%:"' . $author_id . '";%'
      )
    );

    $story_ids = apply_filters( 'fictioneer_filter_co_authored_ids', $story_ids, $author_id );

    $cache[ $author_id ] = $story_ids;

    return $story_ids;
  }

  /**
   * Return selectable stories for chapter assignments.
   *
   * @since 5.26.0
   * @since 5.34.0 - Moved into Utils_Admin class.
   *
   * @global wpdb $wpdb  WordPress database object.
   *
   * @param int $post_author_id     Author ID of the current post.
   * @param int $current_story_id   ID of the currently selected story (if any).
   *
   * @return array Associative array with 'stories' (array), 'other_author' (bool), 'co_author' (bool).
   */

  public static function get_chapter_story_selection( $post_author_id, $current_story_id = 0 ) : array {
    global $wpdb;

    $stories = array( '0' => _x( '— Unassigned —', 'Chapter story select option.', 'fictioneer' ) );
    $co_authored_stories = [];
    $other_author = false;
    $co_author = false;

    $values = [];

    $sql =
      "SELECT p.ID, p.post_title, p.post_status, p.post_date, p.post_author
      FROM {$wpdb->posts} p
      WHERE p.post_type = 'fcn_story'
        AND p.post_status IN ('publish', 'private')";

    if ( get_option( 'fictioneer_limit_chapter_stories_by_author' ) ) {
      $sql .= " AND p.post_author = %d";
      $values[] = $post_author_id;

      $co_authored_stories = self::get_co_authored_story_ids( $post_author_id );

      if ( ! empty( $co_authored_stories ) ) {
        $placeholders = implode( ',', array_fill( 0, count( $co_authored_stories ), '%d' ) );
        $sql .= " OR p.ID IN ($placeholders)";
        $values = array_merge( $values, $co_authored_stories );
        $co_author = true;
      }
    }

    $sql .= " ORDER BY p.post_date DESC";

    if ( empty( $values ) ) {
      $results = $wpdb->get_results( $sql );
    } else {
      $results = $wpdb->get_results( $wpdb->prepare( $sql, ...$values ) );
    }

    foreach ( $results as $story ) {
      $title = Sanitizer::sanitize_safe_title(
        $story->post_title,
        mysql2date( get_option( 'date_format' ), $story->post_date ),
        mysql2date( get_option( 'time_format' ), $story->post_date )
      );
      $suffix = [];

      if ( $story->post_status !== 'publish' ) {
        $suffix['status'] = self::get_post_status_label( $story->post_status );
      }

      if ( in_array( $story->ID, $co_authored_stories ) ) {
        $suffix['co-authored'] = __( 'Co-Author', 'fictioneer' );
      }

      if ( empty( $suffix ) ) {
        $stories[ $story->ID ] = $title;
      } else {
        $stories[ $story->ID ] = sprintf(
          _x( '%1$s (%2$s)', 'Chapter story meta field option with notes.', 'fictioneer' ),
          $title,
          implode( ' | ', $suffix )
        );
      }
    }

    if ( $current_story_id && ! array_key_exists( $current_story_id, $stories ) ) {
      $other_author_id = get_post_field( 'post_author', $current_story_id );
      $suffix = [];

      if ( $other_author_id != $post_author_id ) {
        $other_author = true;
        $suffix['author'] = get_the_author_meta( 'display_name', $other_author_id );
      }

      if ( get_post_status( $current_story_id ) !== 'publish' ) {
        $suffix['status'] = self::get_post_status_label( get_post_status( $current_story_id ) );
      }

      $stories[ $current_story_id ] = sprintf(
        _x( '%1$s (%2$s)', 'Chapter story meta field mismatched option with notes.', 'fictioneer' ),
        fictioneer_get_safe_title( $current_story_id, 'admin-render-chapter-data-metabox-current-suffix' ),
        ! empty( $suffix ) ? implode( ' | ', $suffix ) : ''
      );
    }

    return array(
      'stories' => $stories,
      'other_author' => $other_author,
      'co_author' => $co_author
    );
  }

  /**
   * Return chapter objects for a story.
   *
   * @since 5.26.0
   * @since 5.34.0 - Moved into Utils_Admin class.
   *
   * @global wpdb $wpdb  WordPress database object.
   *
   * @param int $story_id  Story ID.
   *
   * @return object[] Array of chapter data object similar to WP_Post.
   */

  public static function get_story_chapter_relationship_data( $story_id ) : array {
    global $wpdb;

    $chapter_ids = fictioneer_get_story_chapter_ids( $story_id );

    if ( empty( $chapter_ids ) ) {
      return [];
    }

    $placeholders = implode( ',', array_fill( 0, count( $chapter_ids ), '%d' ) );
    $values = array_merge( $chapter_ids, [ $story_id ] );

    $sql = $wpdb->prepare(
      "SELECT p.ID as ID, p.post_title as post_title, p.post_status as post_status, p.post_date_gmt as post_date_gmt,
        pm_text_icon.meta_value as fictioneer_chapter_text_icon,
        pm_icon.meta_value as fictioneer_chapter_icon,
        pm_rating.meta_value as fictioneer_chapter_rating,
        pm_warning.meta_value as fictioneer_chapter_warning,
        pm_group.meta_value as fictioneer_chapter_group
      FROM {$wpdb->posts} p
      LEFT JOIN {$wpdb->postmeta} pm_text_icon ON (p.ID = pm_text_icon.post_id AND pm_text_icon.meta_key = 'fictioneer_chapter_text_icon')
      LEFT JOIN {$wpdb->postmeta} pm_icon ON (p.ID = pm_icon.post_id AND pm_icon.meta_key = 'fictioneer_chapter_icon')
      LEFT JOIN {$wpdb->postmeta} pm_rating ON (p.ID = pm_rating.post_id AND pm_rating.meta_key = 'fictioneer_chapter_rating')
      LEFT JOIN {$wpdb->postmeta} pm_warning ON (p.ID = pm_warning.post_id AND pm_warning.meta_key = 'fictioneer_chapter_warning')
      LEFT JOIN {$wpdb->postmeta} pm_group ON (p.ID = pm_group.post_id AND pm_group.meta_key = 'fictioneer_chapter_group')
      WHERE p.post_type = 'fcn_chapter'
        AND p.ID IN ($placeholders)
        AND EXISTS (
          SELECT 1
          FROM {$wpdb->postmeta} pm
          WHERE pm.post_id = p.ID AND pm.meta_key = 'fictioneer_chapter_story' AND pm.meta_value = %d
        )
      ",
      ...$values
    );

    $results = $wpdb->get_results( $sql );

    $chapter_map = array_flip( $chapter_ids );

    usort( $results, function( $a, $b ) use ( $chapter_map ) {
      return $chapter_map[ $a->ID ] <=> $chapter_map[ $b->ID ];
    });

    return $results;
  }

  /**
   * Update the comment count of a post.
   *
   * @since 5.26.0
   * @since 5.34.0 - Moved into Utils_Admin class.
   *
   * @global wpdb $wpdb  WordPress database object.
   *
   * @param int $post_id  Post ID.
   * @param int $count    Comment count.
   */

  public static function update_comment_count( $post_id, $count ) : void {
    global $wpdb;

    $wpdb->update(
      $wpdb->posts,
      array( 'comment_count' => $count ),
      array( 'ID' => $post_id ),
      ['%d'],
      ['%d']
    );
  }

  /**
   * Soft delete a user's comments.
   *
   * Replace the content and meta data of a user's comments with junk
   * but leave the comment itself in the database. This preserves the
   * structure of comment threads.
   *
   * @since 5.0.0
   * @since 5.34.0 - Moved into Utils_Admin class.
   *
   * @param int $user_id  User ID to soft delete the comments for.
   *
   * @return array|false Detailed results about the database update. Accounts
   *                     for completeness, partial success, and errors. Includes
   *                     'complete' (boolean), 'failure' (boolean), 'success' (boolean),
   *                     'comment_count' (int), and 'updated_count' (int). Or false.
   */

  public static function soft_delete_user_comments( $user_id ) {
    $comments = get_comments( array( 'user_id' => $user_id ) );
    $comment_count = count( $comments );
    $count = 0;
    $complete_one = true;

    if ( empty( $comments ) ) {
      return false;
    }

    foreach ( $comments as $comment ) {
      $result_one = wp_update_comment(
        array(
          'user_ID' => 0,
          'comment_type' => 'user_deleted',
          'comment_author' => _x( 'Deleted', 'Deleted comment author name.', 'fictioneer' ),
          'comment_ID' => $comment->comment_ID,
          'comment_content' => __( 'Comment has been deleted by user.', 'fictioneer' ),
          'comment_author_email' => '',
          'comment_author_IP' => '',
          'comment_agent' => '',
          'comment_author_url' => ''
        )
      );

      if ( $result_one ) {
        $count++;
      }

      if ( ! $result_one || is_wp_error( $result_one ) ) {
        $complete_one = false;
      }
    }

    return array(
      'complete' => $complete_one,
      'failure' => $count == 0,
      'success' => $count == $comment_count && $complete_one,
      'comment_count' => $comment_count,
      'updated_count' => $count
    );
  }

  /**
   * Translated label of post status.
   *
   * @since 5.24.5
   * @since 5.34.0 - Moved into Utils_Admin class.
   *
   * @param string $status  Post status.
   *
   * @return string Translated label of the post status or the post status if custom.
   */

  public static function get_post_status_label( $status ) : string {
    static $labels = null;

    if ( $labels === null ) {
      $labels = array(
        'draft' => get_post_status_object( 'draft' )->label,
        'pending' => get_post_status_object( 'pending' )->label,
        'publish' => get_post_status_object( 'publish' )->label,
        'private' => get_post_status_object( 'private' )->label,
        'future' => get_post_status_object( 'future' )->label,
        'trash' => get_post_status_object( 'trash' )->label,
        'fcn_hidden' => get_post_status_object( 'fcn_hidden' )->label
      );
    }

    return $labels[ $status ] ?? $status;
  }

  /**
   * Translated label of post type.
   *
   * @since 5.25.0
   * @since 5.34.0 - Moved into Utils_Admin class.
   *
   * @param string $type  Post type.
   *
   * @return string Translated label of the post type or the post type if custom.
   */

  public static function get_post_type_label( $type ) : string {
    static $labels = null;

    if ( $labels === null ) {
      $labels = array(
        'post' => _x( 'Post', 'Post type label.', 'fictioneer' ),
        'page' => _x( 'Page', 'Post type label.', 'fictioneer' ),
        'fcn_story' => _x( 'Story', 'Post type label.', 'fictioneer' ),
        'fcn_chapter' => _x( 'Chapter', 'Post type label.', 'fictioneer' ),
        'fcn_collection' => _x( 'Collection', 'Post type label.', 'fictioneer' ),
        'fcn_recommendation' => _x( 'Rec', 'Post type label.', 'fictioneer' )
      );
    }

    return $labels[ $type ] ?? $type;
  }

  /**
   * Wrapper to update comment meta.
   *
   * Note: If the meta value is truthy, the meta field is updated as normal.
   * If not, the meta field is deleted instead to keep the database tidy.
   *
   * @since 5.7.3
   * @since 5.34.0 - Moved into Utils_Admin class.
   *
   * @param int    $comment_id  The ID of the comment.
   * @param string $meta_key    The meta key to update.
   * @param mixed  $meta_value  The new meta value. If empty, the meta key will be deleted.
   * @param mixed  $prev_value  Optional. If specified, only updates existing metadata with this value.
   *                            Otherwise, update all entries. Default empty.
   *
   * @return int|bool Meta ID if the key didn't exist on update, true on successful update or delete,
   *                  false on failure or if the value passed to the function is the same as the one
   *                  that is already in the database.
   */

  public static function update_comment_meta( $comment_id, $meta_key, $meta_value, $prev_value = '' ) {
    if ( empty( $meta_value ) && ! in_array( $meta_key, self::get_falsy_meta_allow_list() ) ) {
      return delete_comment_meta( $comment_id, $meta_key );
    } else {
      return update_comment_meta( $comment_id, $meta_key, $meta_value, $prev_value );
    }
  }

  /**
   * Wrapper to update user meta.
   *
   * Note: If the meta value is truthy, the meta field is updated as normal.
   * If not, the meta field is deleted instead to keep the database tidy.
   *
   * @since 5.7.3
   * @since 5.34.0 - Moved into Utils_Admin class.
   *
   * @param int    $user_id     The ID of the user.
   * @param string $meta_key    The meta key to update.
   * @param mixed  $meta_value  The new meta value. If empty, the meta key will be deleted.
   * @param mixed  $prev_value  Optional. If specified, only updates existing metadata with this value.
   *                            Otherwise, update all entries. Default empty.
   *
   * @return int|bool Meta ID if the key didn't exist on update, true on successful update or delete,
   *                  false on failure or if the value passed to the function is the same as the one
   *                  that is already in the database.
   */

  public static function update_user_meta( $user_id, $meta_key, $meta_value, $prev_value = '' ) {
    if ( empty( $meta_value ) && ! in_array( $meta_key, self::get_falsy_meta_allow_list() ) ) {
      return delete_user_meta( $user_id, $meta_key );
    } else {
      return update_user_meta( $user_id, $meta_key, $meta_value, $prev_value );
    }
  }

  /**
   * Wrapper to update post meta.
   *
   * Note: If the meta value is truthy, the meta field is updated as normal.
   * If not, the meta field is deleted instead to keep the database tidy.
   *
   * @since 5.7.4
   * @since 5.34.0 - Moved into Utils_Admin class.
   *
   * @param int    $post_id     The ID of the post.
   * @param string $meta_key    The meta key to update.
   * @param mixed  $meta_value  The new meta value. If empty, the meta key will be deleted.
   * @param mixed  $prev_value  Optional. If specified, only updates existing metadata with this value.
   *                            Otherwise, update all entries. Default empty.
   *
   * @return int|bool Meta ID if the key didn't exist on update, true on successful update or delete,
   *                  false on failure or if the value passed to the function is the same as the one
   *                  that is already in the database.
   */

  public static function update_post_meta( $post_id, $meta_key, $meta_value, $prev_value = '' ) {
    if ( empty( $meta_value ) && ! in_array( $meta_key, self::get_falsy_meta_allow_list() ) ) {
      return delete_post_meta( $post_id, $meta_key );
    } else {
      return update_post_meta( $post_id, $meta_key, $meta_value, $prev_value );
    }
  }

  /**
   * Return allow list for falsy meta fields.
   *
   * @since 5.7.4
   * @since 5.34.0 - Moved into Utils_Admin class.
   *
   * @return string[] Meta fields allowed to be saved falsy and not be deleted.
   */

  public static function get_falsy_meta_allow_list() : array {
    static $allow_list = null;

    if ( $allow_list === null ) {
      $allow_list = (array) apply_filters( 'fictioneer_filter_falsy_meta_allow_list', [] );
    }

    return $allow_list;
  }

  /**
   * Check rate limit globally or for an action via the session.
   *
   * @since 5.7.1
   * @since 5.34.0 - Moved into Utils_Admin class.
   *
   * @param string   $action  The action to check for rate-limiting.
   *                          Defaults to 'fictioneer_global'.
   * @param int|null $max     Optional. Maximum number of requests.
   *                          Defaults to FICTIONEER_REQUESTS_PER_MINUTE.
   */

  public static function check_rate_limit( $action = 'fictioneer_global', $max = null ) : void {
    if ( ! get_option( 'fictioneer_enable_rate_limits' ) ) {
      return;
    }

    if ( session_status() == PHP_SESSION_NONE ) {
      session_start();
    }

    if ( ! isset( $_SESSION[ $action ]['request_times'] ) ) {
      $_SESSION[ $action ]['request_times'] = [];
    }

    $current_time = microtime( true );
    $time_window = 60;
    $max = $max ? absint( $max ) : FICTIONEER_REQUESTS_PER_MINUTE;
    $max = max( 1, $max );

    $_SESSION[ $action ]['request_times'] = array_filter(
      $_SESSION[ $action ]['request_times'],
      function ( $time ) use ( $current_time, $time_window ) {
        return ( $current_time - $time ) < $time_window;
      }
    );

    if ( count( $_SESSION[ $action ]['request_times'] ) >= $max ) {
      http_response_code( 429 ); // Too many requests
      exit;
    }

    $_SESSION[ $action ]['request_times'][] = $current_time;
  }

  /**
   * Balance pagination array.
   *
   * Takes an number array of pagination pages and balances the items around the
   * current page number, replacing anything above the keep threshold with ellipses.
   * E.g. 1 … 7, 8, [9], 10, 11 … 20.
   *
   * @since 5.0.0
   * @since 5.34.0 - Moved into Utils_Admin class.
   *
   * @param array|int $pages     Array of pages to balance. If an integer is provided,
   *                             it is converted to a number array.
   * @param int       $current   Current page number.
   * @param int       $keep      Optional. Balancing factor to each side. Default 2.
   * @param string    $ellipses  Optional. String for skipped numbers. Default '…'.
   *
   * @return array The balanced array.
   */

  public static function balance_pagination_array( $pages, $current, $keep = 2, $ellipses = '…' ) : array {
    $max_pages = is_array( $pages ) ? count( $pages ) : $pages;
    $steps = is_array( $pages ) ? $pages : [];

    if ( ! is_array( $pages ) ) {
      for ( $i = 1; $i <= $max_pages; $i++ ) {
        $steps[] = $i;
      }
    }

    // You know, I wrote this but don't really get it myself...
    if ( $max_pages - $keep * 2 > $current ) {
      $start = $current + $keep;
      $end = $max_pages - $keep + 1;

      for ( $i = $start; $i < $end; $i++ ) {
        unset( $steps[ $i ] );
      }

      array_splice( $steps, count( $steps ) - $keep + 1, 0, $ellipses );
    }

    // It certainly does math...
    if ( $current - $keep * 2 >= $keep ) {
      $start = $keep - 1;
      $end = $current - $keep - 1;

      for ( $i = $start; $i < $end; $i++ ) {
        unset( $steps[ $i ] );
      }

      array_splice( $steps, $keep - 1, 0, $ellipses );
    }

    return $steps;
  }

  /**
   * Check whether a comment contains disallowed characters or words and
   * returns the offenders within the comment content.
   *
   * @since 5.0.0
   * @since 5.34.0 - Refactored and moved into Utils_Admin class.
   *
   * @param string $author      The author of the comment.
   * @param string $email       The email of the comment.
   * @param string $url         The url used in the comment.
   * @param string $comment     The comment content
   * @param string $user_ip     The comment author's IP address.
   * @param string $user_agent  The author's browser user agent.
   *
   * @return array Tuple of true/false [0] and offenders [1] as array.
   */

  public static function check_comment_disallowed_list( $author, $email, $url, $comment, $user_ip, $user_agent ) : array {
    $mod_keys = trim( get_option( 'disallowed_keys' ) );

    if ( '' === $mod_keys ) {
      return [false, []]; // If moderation keys are empty.
    }

    $comment = (string) $comment;
    $comment_without_html = wp_strip_all_tags( $comment );

    $fields = array(
      (string) $author,
      (string) $email,
      (string) $url,
      $comment,
      $comment_without_html,
      (string) $user_ip,
      (string) $user_agent
    );

    $words = (array) preg_split( '/\r\n|\r|\n/', $mod_keys );

    $offenders = [];

    foreach ( $words as $word ) {
      $word = trim( (string) $word );

      if ( empty( $word ) ) {
        continue;
      }

      $pattern = '#' . preg_quote( $word, '#' ) . '#i';

      foreach ( $fields as $field ) {
        if ( $field === '' ) {
          continue;
        }

        if ( preg_match( $pattern, $field, $matches ) ) {
          $offenders[] = $matches[0];

          break;
        }
      }
    }

    if ( empty( $offenders ) ) {
      return [false, []];
    }

    $offenders = array_values( array_unique( $offenders ) );

    return [true, $offenders];
  }

  /**
   * Append new chapters to story list.
   *
   * @since 5.4.9
   * @since 5.7.4 - Updated.
   * @since 5.8.6 - Added $force param and moved function.
   * @since 5.19.1 - Always append chapter to story.
   * @since 5.34.0 - Moved into Utils_Admin class.
   *
   * @param int  $post_id   The chapter post ID.
   * @param int  $story_id  The story post ID.
   * @param bool $force     Optional. Whether to skip some guard clauses. Default false.
   */

  public static function append_chapter_to_story( $post_id, $story_id, $force = false ) : void {
    $allowed_statuses = apply_filters(
      'fictioneer_filter_append_chapter_to_story_statuses',
      ['publish', 'future', 'fcn_hidden'],
      $post_id,
      $story_id,
      $force
    );

    // Abort if chapter status is not allowed
    if ( ! in_array( get_post_status( $post_id ), $allowed_statuses ) ) {
      return;
    }

    // Setup
    $story = get_post( $story_id );

    // Abort if story not found or not a story
    if ( ! $story || $story->post_type !== 'fcn_story' ) {
      return;
    }

    // Setup, continued
    $chapter_author_id = get_post_field( 'post_author', $post_id );
    $story_author_id = get_post_field( 'post_author', $story_id );
    $co_authored_story_ids = Utils_Admin::get_co_authored_story_ids( $chapter_author_id );

    // Abort if the author IDs do not match
    if (
      $chapter_author_id != $story_author_id &&
      ! in_array( $story_id, $co_authored_story_ids ) &&
      ! $force
    ) {
      return;
    }

    // Get current story chapters
    $story_chapters = fictioneer_get_story_chapter_ids( $story_id );

    // Append chapter (if not already included) and save to database
    if ( ! in_array( $post_id, $story_chapters ) ) {
      $previous_chapters = $story_chapters;
      $story_chapters[] = $post_id;
      $story_chapters = array_unique( $story_chapters );

      // Save updated list
      update_post_meta( $story_id, 'fictioneer_story_chapters', $story_chapters );

      // Remember when chapters have been changed
      update_post_meta( $story_id, 'fictioneer_chapters_modified', current_time( 'mysql', true ) );

      // Remember when chapters have been added
      $allowed_statuses = apply_filters(
        'fictioneer_filter_chapters_added_statuses',
        ['publish'],
        $post_id
      );

      if ( in_array( get_post_status( $post_id ), $allowed_statuses ) ) {
        update_post_meta( $story_id, 'fictioneer_chapters_added', current_time( 'mysql', true ) );
      }

      // Log changes
      fictioneer_log_story_chapter_changes( $story_id, $story_chapters, $previous_chapters );

      // Clear meta caches to ensure they get refreshed
      delete_post_meta( $story_id, 'fictioneer_story_data_collection' );
      delete_post_meta( $story_id, 'fictioneer_story_chapter_index_html' );
    } else {
      // Nothing to do
      return;
    }

    // Update story post to fire associated actions
    wp_update_post( array( 'ID' => $story_id ) );
  }
}
