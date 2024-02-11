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
?>

<li class="card _large _hidden-result <?php echo implode( ' ', $card_classes ); ?>">
  <div class="card__body polygon">
    <div class="card__main _hidden-result">
      <div class="card__content _hidden-result"><?php echo _x( 'Hidden Result', 'Hidden card.', 'fictioneer' ); ?></div>
    </div>
  </div>
</li>
