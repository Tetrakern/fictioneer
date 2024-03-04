<?php
/**
 * Partial: Hidden Card
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.5.2
 *
 * @internal $args['show_type']    Whether to show the post type label. Unsafe.
 * @internal $args['cache']        Whether to account for active caching. Unsafe.
 * @internal $args['hide_author']  Whether to hide the author. Unsafe.
 * @internal $args['order']        Current order. Default 'desc'. Unsafe.
 * @internal $args['orderby']      Current orderby. Default 'modified'. Unsafe.
 */
?>

<?php

// No direct access!
defined( 'ABSPATH' ) OR exit;

// Extra classes
$card_classes = [];

if ( get_theme_mod( 'card_style', 'default' ) !== 'default' ) {
  $card_classes[] = '_' . get_theme_mod( 'card_style' );
}

// Card attributes
$attributes = apply_filters( 'fictioneer_filter_card_attributes', [], $post, 'card-hidden' );
$card_attributes = '';

foreach ( $attributes as $key => $value ) {
  $card_attributes .= esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
}

?>

<li class="card _large _hidden-result <?php echo implode( ' ', $card_classes ); ?>" <?php echo $card_attributes; ?>>
  <div class="card__body polygon">
    <div class="card__main _hidden-result">
      <div class="card__content _hidden-result"><?php echo _x( 'Hidden Result', 'Hidden card.', 'fictioneer' ); ?></div>
    </div>
  </div>
</li>
