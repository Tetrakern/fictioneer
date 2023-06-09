<?php
/**
 * Partial: Archive Loop
 *
 * Renders a list of post type specific cards.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 3.0
 * @see category.php
 * @see tag.php
 * @see taxonomy-fcn_character.php
 * @see taxonomy-fcn_content_warning.php
 * @see taxonomy-fcn_fandom.php
 * @see taxonomy-fcn_genre.php
 */
?>

<?php if ( have_posts() ) : ?>
  <section class="archive__posts">
    <ul class="card-list _no-mutation-observer" id="archive-list">
      <?php
        while ( have_posts() ) {
          the_post();

          // Setup
          $type = get_post_type();
          $card_args = ['show_type' => true];

          // Cached?
          if ( fictioneer_caching_active() && ! fictioneer_private_caching_active() ) $card_args['cache'] = true;

          // Special conditions for chapters...
          if ( $type == 'fcn_chapter' ) {
            $no_chapter = fictioneer_get_field( 'fictioneer_chapter_no_chapter' );
            $hidden_chapter = fictioneer_get_field( 'fictioneer_chapter_hidden' );

            if ( $no_chapter || $hidden_chapter ) continue;
          }

          // Echo correct card
          fictioneer_echo_card( $card_args );
        }

        // Pagination
        $pag_args = array(
          'prev_text' => fcntr( 'previous' ),
          'next_text' => fcntr( 'next' ),
          'add_fragment' => '#archive-list'
        );

        if ( $GLOBALS['wp_query']->found_posts > get_option( 'posts_per_page' ) ) {
          ?><li class="pagination"><?php echo fictioneer_paginate_links( $pag_args ); ?></li><?php
        }
      ?>
    </ul>
  </section>
<?php endif; ?>
