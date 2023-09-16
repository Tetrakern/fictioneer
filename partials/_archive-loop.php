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

<?php

// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$page = get_query_var( 'paged', 1 ) ?: 1; // Main query
$order = array_intersect( [sanitize_key( $_GET['order'] ?? 0 )], ['desc', 'asc'] );
$order = reset( $order ) ?: 'desc';
$orderby = array_intersect( [sanitize_key( $_GET['orderby'] ?? 0 )], fictioneer_allowed_orderby() );
$orderby = reset( $orderby ) ?: 'date';
$ago = $_GET['ago'] ?? 0;
$ago = is_numeric( $ago ) ? absint( $ago ) : sanitize_text_field( $ago );

// Prepare sort-order-filter arguments
$hook_args = array(
  'page' => $page,
  'order' => $order,
  'orderby' => $orderby,
  'ago' => $ago
);

?>

<?php do_action( 'fictioneer_archive_loop_before', $hook_args ); ?>

<?php if ( have_posts() ) : ?>

  <section class="archive__posts">
    <ul id="archive-list" class="scroll-margin-top card-list _no-mutation-observer">
      <?php
        while ( have_posts() ) {
          the_post();

          // Setup
          $type = get_post_type();
          $card_args = array(
            'cache' => fictioneer_caching_active() && ! fictioneer_private_caching_active(),
            'show_type' => true,
            'order' => $order,
            'orderby' => $orderby,
            'ago' => $ago
          );

          // Cached?
          if ( fictioneer_caching_active() && ! fictioneer_private_caching_active() ) {
            $card_args['cache'] = true;
          }

          // Special conditions for chapters...
          if ( $type == 'fcn_chapter' ) {
            if (
              fictioneer_get_field( 'fictioneer_chapter_no_chapter' ) ||
              fictioneer_get_field( 'fictioneer_chapter_hidden' )
            ) {
              continue;
            }
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

<?php else : ?>

  <section class="archive__posts">
    <ul id="archive-list" class="scroll-margin-top card-list _no-mutation-observer">
      <li class="no-results">
        <span><?php _e( 'No matching posts found.', 'fictioneer' ); ?></span>
      </li>
    </ul>
  </section>

<?php endif; ?>

<?php do_action( 'fictioneer_archive_loop_after', $hook_args ); ?>
