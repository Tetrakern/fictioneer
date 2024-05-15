<?php
/**
 * Partial: Bookmarks
 *
 * Renders the HTML required for the script-generated bookmark cards.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0.0
 *
 * @internal $args['show_empty']  Whether to render a note when empty or nothing at all.
 * @internal $args['count']       Maximum number of bookmarks to render.
 * @internal $args['seamless']    Whether to render the image seamless. Default false (Customizer).
 * @internal $args['thumbnail']   Whether the image is rendered. Default true (Customizer).
 */
?>

<?php

// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$show_empty = isset( $args['show_empty'] ) && $args['show_empty'];
$count = isset( $args['count'] ) ? intval( $args['count'] ) : -1;

get_template_part( 'partials/_template_bookmark', null, $args );

?>

<section class="small-card-block bookmarks-block <?php echo $show_empty ? '' : 'hidden' ?>" data-count="<?php echo $count; ?>">
  <?php if ( $show_empty ) : ?>
    <div class="bookmarks-block__no-bookmarks no-results"><?php echo fcntr( 'no_bookmarks' ); ?></div>
  <?php endif; ?>
  <ul class="grid-columns"></ul>
</section>
