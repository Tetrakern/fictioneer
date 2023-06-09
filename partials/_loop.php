<?php
/**
 * Partial: Loop
 *
 * Renders a list of full-width posts paginated using the "Blog pages show at most"
 * (posts_per_page) option under Settings > Reading.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 3.0
 * @see partials/_post.php
 */
?>

<?php if ( have_posts() ) : ?>
  <section class="blog">
    <?php
      while ( have_posts() ) {
        the_post();
        get_template_part( 'partials/_post' );
      }

      $pag_args = array(
        'prev_text' => fcntr( 'previous' ),
        'next_text' => fcntr( 'next' )
      );
    ?>
  </section>

  <nav class="pagination _padding-top padding-bottom"><?php echo fictioneer_paginate_links( $pag_args ); ?></nav>

<?php else: ?>

  <article class="post _empty padding-top padding-bottom padding-left padding-right">
    <span><?php _e( 'No (more) posts found.', 'fictioneer' ) ?></span>
  </article>

<?php endif; ?>
