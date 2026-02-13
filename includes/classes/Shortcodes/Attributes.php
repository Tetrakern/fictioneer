<?php
namespace Fictioneer\Shortcodes;

use Fictioneer\Sanitizer;
use Fictioneer\Utils;

defined( 'ABSPATH' ) || exit;

final class Attributes {
  private static $defaults = null;
  private static $card_image_style = null;

  /**
   * Return the card image style theme mod (cached).
   *
   * @since 5.34.0
   *
   * @return string Card image style.
   */

  private static function card_image_style() : string {
    if ( self::$card_image_style === null ) {
      self::$card_image_style = (string) get_theme_mod( 'card_image_style', 'default' );
    }

    return self::$card_image_style;
  }

  /**
   * Parse and validate Splide configuration JSON.
   *
   * @since 5.34.0
   *
   * @param string $raw  Raw Splide JSON string.
   *
   * @return string|false JSON-encoded configuration or false on failure.
   */

  private static function parse_splide( $raw ) {
    $raw = trim( $raw );

    if ( $raw === '' ) {
      return '';
    }

    $raw = str_replace( "'", '"', $raw );

    if ( ! Utils::json_validate( $raw ) ) {
      return false;
    }

    $splide = json_decode( $raw, true );

    if ( ! is_array( $splide ) ) {
      return false;
    }

    if ( ! isset( $splide['arrows'] ) ) {
      $splide['arrows'] = false;
    }

    if ( ! isset( $splide['arrowPath'] ) ) {
      $splide['arrowPath'] =
        'M31.89 18.24c0.98 0.98 0.98 2.56 0 3.54l-15 15c-0.98 0.98-2.56 0.98-3.54 0s-0.98-2.56 0-3.54L26.45 20 13.23 6.76c-0.98-0.98-0.98-2.56 0-3.54s2.56-0.98 3.54 0l15 15';
    }

    return wp_json_encode( $splide );
  }

  /**
   * Default attribute pairs for shortcode_atts().
   *
   * @since 5.34.0
   *
   * @return array Default attribute pairs.
   */

  public static function defaults() : array {
    if ( self::$defaults !== null ) {
      return self::$defaults;
    }

    self::$defaults = array(
      'uid' => '',
      'type' => 'default',
      'simple' => false,
      'single' => false,
      'spoiler' => false,
      'spotlight' => false,
      'count' => -1,
      'offset' => 0,
      'order' => '',
      'orderby' => '',
      'page' => 1,
      'per_page' => (int) get_option( 'posts_per_page' ),
      'posts_per_page' => (int) get_option( 'posts_per_page' ),
      'post_status' => 'publish',
      'post_ids' => '',
      'author' => '',
      'author_ids' => '',
      'excluded_authors' => '',
      'excluded_tags' => '',
      'excluded_cats' => '',
      'tags' => '',
      'categories' => '',
      'fandoms' => '',
      'genres' => '',
      'characters' => '',
      'taxonomies' => '',
      'rel' => 'AND',
      'relation' => 'AND',
      'ignore_sticky' => false,
      'ignore_protected' => false,
      'only_protected' => false,
      'vertical' => false,
      'seamless' => ( self::card_image_style() === 'seamless' ),
      'aspect_ratio' => '',
      'thumbnail' => ( self::card_image_style() !== 'none' ),
      'lightbox' => true,
      'words' => true,
      'date' => true,
      'date_format' => '',
      'nested_date_format' => '',
      'footer' => true,
      'footer_author' => true,
      'footer_chapters' => true,
      'footer_words' => true,
      'footer_date' => true,
      'footer_comments' => true,
      'footer_status' => true,
      'footer_rating' => true,
      'classes' => '',
      'class' => '',
      'infobox' => true,
      'source' => true,
      'splide' => '',
      'cache' => true,
      'terms' => 'inline',
      'max_terms' => 10,
      'height' => '',
      'min_width' => '',
      'quality' => '',
      'no_cap' => '',
      'for' => ''
    );

    return self::$defaults;
  }

  /**
   * Extract taxonomies from shortcode attributes.
   *
   * @since 5.2.0
   * @since 5.34.0 - Refactored and moved into Attributes class.
   *
   * @param array $attr  Raw shortcode attributes.
   *
   * @return array Array of found taxonomies.
   */

  public static function get_shortcode_taxonomies( $attr ) : array {
    $taxonomies = [];

    foreach ( ['tags', 'categories', 'fandoms', 'characters', 'genres'] as $key ) {
      if ( ! empty( $attr[ $key ] ) ) {
        $taxonomies[ $key ] = Utils::parse_list( $attr[ $key ], 'sanitize_text_field', 'comma' );
      }
    }

    return $taxonomies;
  }

  /**
   * Parse, sanitize, and normalize shortcode attributes.
   *
   * @since 5.7.3
   * @since 5.34.0 - Refactored and moved into Attributes class.
   *
   * @param array  $attr       Raw shortcode attributes.
   * @param string $shortcode  Shortcode name for context.
   * @param int    $count      Optional fallback. Default -1 (all).
   *
   * @return array Parsed and sanitized arguments.
   */

  public static function parse( $attr, $shortcode, $count = -1 ) : array {
    $defaults = self::defaults();
    $attr = is_array( $attr ) ? $attr : [];
    $uid = wp_unique_id( 'shortcode-id-' );

    $sanitized = array(
      'uid' => $uid,
      'type' => sanitize_key( $attr['type'] ?? 'default' ),
      'simple' => Utils::bool( $attr['simple'] ?? null ),
      'single' => Utils::bool( $attr['single'] ?? null ),
      'count' => max( -1, (int) ( $attr['count'] ?? $count ) ),
      'offset' => max( 0, (int) ( $attr['offset'] ?? 0 ) ),
      'order' => sanitize_key( $attr['order'] ?? '' ),
      'orderby' => sanitize_key( $attr['orderby'] ?? '' ),
      'page' => max( 1, absint( get_query_var( 'page' ) ) ?: absint( get_query_var( 'paged' ) ) ?: 1 ),
      'posts_per_page' => absint( $attr['posts_per_page'] ?? 0 )
        ?: absint( $attr['per_page'] ?? 0 ) ?: (int) get_option( 'posts_per_page' ),
      'post_status' => Utils::parse_list( $attr['post_status'] ?? 'publish', 'sanitize_key' ),
      'post_ids' => wp_parse_id_list( $attr['post_ids'] ?? '' ),
      'author' => sanitize_title( $attr['author'] ?? '' ),
      'author_ids' => wp_parse_id_list( $attr['author_ids'] ?? '' ),
      'excluded_authors' => wp_parse_id_list( $attr['exclude_author_ids'] ?? '' ),
      'excluded_tags' => wp_parse_id_list( $attr['exclude_tag_ids'] ?? '' ),
      'excluded_cats' => wp_parse_id_list( $attr['exclude_cat_ids'] ?? '' ),
      'taxonomies' => self::get_shortcode_taxonomies( $attr ),
      'relation' => strtolower( (string) ( $attr['rel'] ?? $attr['relation'] ?? 'and' ) ) === 'or' ? 'OR' : 'AND',
      'ignore_sticky' => Utils::bool( $attr['ignore_sticky'] ?? null ),
      'ignore_protected' => Utils::bool( $attr['ignore_protected'] ?? null ),
      'only_protected' => Utils::bool( $attr['only_protected'] ?? null ),
      'vertical' => Utils::bool( $attr['vertical'] ?? null ),
      'seamless' => Utils::bool( $attr['seamless'] ?? null, $defaults['seamless'] ),
      'thumbnail' => Utils::bool( $attr['thumbnail'] ?? null, $defaults['thumbnail'] ),
      'aspect_ratio' => Sanitizer::sanitize_css_aspect_ratio( $attr['aspect_ratio'] ?? '' ),
      'spoiler' => Utils::bool( $attr['spoiler'] ?? null ),
      'lightbox' => Utils::bool( $attr['lightbox'] ?? null, true ),
      'terms' => Sanitizer::sanitize_query_var( $attr['terms'] ?? 'inline', ['inline', 'pills', 'none', 'false'], 'inline' ),
      'max_terms' => max( 1, absint( $attr['max_terms'] ?? 10 ) ),
      'words' => Utils::bool( $attr['words'] ?? null, true ),
      'date' => Utils::bool( $attr['date'] ?? null, true ),
      'date_format' => Sanitizer::sanitize_date_format( $attr['date_format'] ?? '' ),
      'nested_date_format' => Sanitizer::sanitize_date_format( $attr['nested_date_format'] ?? '' ),
      'footer' => Utils::bool( $attr['footer'] ?? null, true ),
      'footer_author' => Utils::bool( $attr['footer_author'] ?? null, true ),
      'footer_chapters' => Utils::bool( $attr['footer_chapters'] ?? null, true ),
      'footer_comments' => Utils::bool( $attr['footer_comments'] ?? null, true ),
      'footer_date' => Utils::bool( $attr['footer_date'] ?? null, true ),
      'footer_rating' => Utils::bool( $attr['footer_rating'] ?? null, true ),
      'footer_status' => Utils::bool( $attr['footer_status'] ?? null, true ),
      'footer_words' => Utils::bool( $attr['footer_words'] ?? null, true ),
      'classes' => esc_attr( wp_strip_all_tags( $attr['classes'] ?? $attr['class'] ?? '' ) ) . " {$uid}",
      'infobox' => Utils::bool( $attr['infobox'] ?? null, true ),
      'source' => Utils::bool( $attr['source'] ?? null, true ),
      'splide' => self::parse_splide( (string) ( $attr['splide'] ?? '' ) ),
      'cache' => Utils::bool( $attr['cache'] ?? null, true ),
      'spotlight' => Utils::bool( $attr['spotlight'] ?? null )
    );

    if ( ! empty( $sanitized['post_ids'] ) ) {
      $sanitized['count'] = count( $sanitized['post_ids'] );
    }

    if ( is_array( $sanitized['post_status'] ) && count( $sanitized['post_status'] ) === 1 ) {
      $sanitized['post_status'] = reset( $sanitized['post_status'] ); // Legacy support
    }

    return apply_filters(
      'fictioneer_filter_default_shortcode_args',
      shortcode_atts( $defaults, $sanitized + $attr, $shortcode ),
      $count
    );
  }
}
