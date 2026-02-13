<?php

namespace Fictioneer\Shortcodes;

use Fictioneer\Shortcodes\Shortcode;

defined( 'ABSPATH' ) OR exit;

class Latest_Updates {
  /**
   * Shortcode callback.
   *
   * @since 4.3.0
   * @since 5.34.0 - Moved into class.
   *
   * @param array|string $attr     Raw shortcode attributes.
   * @param string       $content  The enclosed content (if any).
   * @param string       $tag      The shortcode tag (name).
   *
   * @return string Shortcode HTML.
   */

  public static function render( $attr, $content = '', $tag = '' ) : string {
    $shortcode = $tag ?: 'fictioneer_latest_updates';

    $attr['footer_comments'] = \Fictioneer\Utils::bool( $attr['footer_comments'] ?? null );
    $attr['post_status'] = \Fictioneer\Utils::parse_list( $attr['post_status'] ?? ['publish', 'future'], 'sanitize_key' );

    $args = Attributes::parse( $attr, $shortcode, 4 );

    $args['content'] = $content;

    if ( ! empty( $args['splide'] ) ) {
      $args['classes'] .= ' splide _splide-placeholder';
    }

    $transient_enabled = ! empty( $args['cache'] ) && Shortcode::transients_enabled( $shortcode );

    if ( $transient_enabled ) {
      $transient_key = Shortcode::transient_key( $shortcode, $args, $attr );
      $cached = get_transient( $transient_key );

      if ( is_string( $cached ) && $cached !== '' ) {
        return $cached;
      }
    }

    ob_start();

    switch ( $args['type'] ?? 'default' ) {
      case 'compact':
        fictioneer_get_template_part( 'partials/_latest-updates-compact', null, $args );
        break;
      case 'list':
        fictioneer_get_template_part( 'partials/_latest-updates-list', null, $args );
        break;
      default:
        fictioneer_get_template_part( 'partials/_latest-updates', null, $args );
    }

    $html = fictioneer_minify_html( (string) ob_get_clean() );

    if (
      ! empty( $args['splide'] ) &&
      strpos( $args['classes'] ?? '', 'no-auto-splide' ) === false
    ) {
      $html = str_replace( '</section>', Shortcode::splide_inline_script() . '</section>', $html );
    }

    if ( $transient_enabled ) {
      set_transient( $transient_key, $html, FICTIONEER_SHORTCODE_TRANSIENT_EXPIRATION );
    }

    return $html;
  }
}
