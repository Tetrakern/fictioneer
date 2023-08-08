<?php

// =============================================================================
// LIST OF ALL RECOMMENDATIONS
// =============================================================================

/**
 * Outputs the paginated card list for all recommendations
 *
 * @since 5.0
 * @see recommendations.php
 *
 * @param int        $args['current_page']     Current page number of pagination or 1.
 * @param int        $args['post_id']          The post ID.
 * @param WP_Query   $args['recommendations']  Paginated query of all published recommendations.
 * @param string     $args['queried_type']     The queried post type ('fcn_recommendation').
 * @param array      $args['query_args']       The query arguments used.
 * @param string     $args['order']            Current order. Default 'desc'.
 * @param string     $args['orderby']          Current orderby. Default 'modified'.
 * @param int|string $args['ago']              Current date query argument part. Default 0.
 */

function fictioneer_recommendations_list( $args ) {
  // Start HTML ---> ?>
  <section class="recommendations__list spacing-top">
    <ul class="card-list" id="list-of-recommendations">

      <?php if ( $args['recommendations']->have_posts() ) : ?>

        <?php
          // Card arguments
          $card_args = array(
            'cache' => fictioneer_caching_active() && ! fictioneer_private_caching_active(),
            'order' => $args['order'] ?? 'desc',
            'orderby' => $args['orderby'] ?? 'modified',
            'ago' => $args['ago'] ?? 0
          );

          // Filter card arguments
          $card_args = apply_filters( 'fictioneer_filter_recommendations_card_args', $card_args, $args );

          while ( $args['recommendations']->have_posts() ) {
            $args['recommendations']->the_post();
            get_template_part( 'partials/_card-recommendation', null, $card_args );
          }

          // Actions at end of results
          do_action( 'fictioneer_recommendations_end_of_results', $args );
        ?>

      <?php else: ?>

        <?php do_action( 'fictioneer_recommendations_no_results', $args ); ?>

        <li class="no-results">
          <span><?php _e( 'No recommendations found.', 'fictioneer' ) ?></span>
        </li>

      <?php endif; wp_reset_postdata(); ?>

      <?php
        $pag_args = array(
          'current' => max( 1, get_query_var( 'paged' ) ),
          'total' => $args['recommendations']->max_num_pages,
          'prev_text' => fcntr( 'previous' ),
          'next_text' => fcntr( 'next' ),
          'add_fragment' => '#list-of-recommendations'
        );
      ?>

      <?php if ( $args['recommendations']->max_num_pages > 1 ) : ?>
        <li class="pagination"><?php echo fictioneer_paginate_links( $pag_args ); ?></li>
      <?php endif; ?>

    </ul>
  </section>
  <?php // <--- End HTML
}
add_action( 'fictioneer_recommendations_after_content', 'fictioneer_recommendations_list', 30 );

// =============================================================================
// RECOMMENDATION TAGS
// =============================================================================

/**
 * Outputs the HTML for the recommendation page tags
 *
 * @since Fictioneer 5.0
 *
 * @param WP_Post $args['recommendation']     The recommendation object.
 * @param int     $args['recommendation_id']  The recommendation post ID.
 * @param int     $args['title']              The safe recommendation title.
 */

function fictioneer_recommendation_tags( $args ) {
  // Setup
  $tag_args = [];

  // Show tags?
  if ( ! get_option( 'fictioneer_hide_tags_on_pages' ) ) {
    $tags = get_the_tags( $args['recommendation_id'] );

    if ( ! empty( $tags ) ) $tag_args[] = $tags;
  }

  // Show content warnings?
  if ( ! get_option( 'fictioneer_hide_content_warnings_on_pages' ) ) {
    $warnings = get_the_terms( $args['recommendation_id'], 'fcn_content_warning' );

    if ( ! empty( $warnings ) ) $tag_args[] = $warnings;
  }

  // Abort conditions...
  if ( empty( $tag_args ) ) return;

  // Start HTML ---> ?>
  <section class="recommendation__tags tag-group">
    <?php echo fictioneer_get_taxonomy_pills( $tag_args, '_secondary' ); ?>
  </section>
  <?php // <--- End HTML
}
add_action( 'fictioneer_recommendation_after_content', 'fictioneer_recommendation_tags', 10 );

// =============================================================================
// RECOMMENDATION LINKS
// =============================================================================

/**
 * Outputs the HTML for the recommendation page links
 *
 * @since Fictioneer 5.0
 *
 * @param WP_Post $args['recommendation']    The recommendation object.
 * @param int     $args['recommendation_id'] The recommendation post ID.
 * @param int     $args['title']             The safe recommendation title.
 */

function fictioneer_recommendation_links( $args ) {
  // Setup
  $links = fictioneer_get_field( 'fictioneer_recommendation_urls', $args['recommendation_id'] );

  // Abort conditions...
  if ( ! $links ) return;

  // Prepare
  $links = array_filter( explode( "\n", $links ) );
  $tuples = [];

  foreach( $links as $tuple ) {
    $tuple = explode( '|', $tuple );
    $tuple = array_map( 'trim', $tuple );
    $tuples[] = '<li><i class="fa-solid fa-external-link-square-alt"></i><a href="' . esc_url( $tuple[1] ) . '" class="link" rel="noopener" target="_blank">' . wp_strip_all_tags( $tuple[0] ) . '</a></li>';
  }

  // Start HTML ---> ?>
  <div class="recommendation__read-on">
    <h5><?php _e( 'Read on', 'fictioneer' ) ?></h5>
    <div>
      <ul class="recommendation__list"><?php echo implode( '', $tuples ); ?></ul>
    </div>
  </div>
  <?php // <--- End HTML
}
add_action( 'fictioneer_recommendation_after_content', 'fictioneer_recommendation_links', 20 );

// =============================================================================
// RECOMMENDATION AUTHOR LINKS
// =============================================================================

/**
 * Outputs the HTML for the recommendation page author links
 *
 * @since Fictioneer 5.0
 *
 * @param WP_Post $args['recommendation']    The recommendation object.
 * @param int     $args['recommendation_id'] The recommendation post ID.
 * @param int     $args['title']             The safe recommendation title.
 */

function fictioneer_recommendation_support_links( $args ) {
  // Setup
  $links = fictioneer_get_field( 'fictioneer_recommendation_support', $args['recommendation_id'] );

  // Abort conditions...
  if ( ! $links ) return;

  // Prepare
  $links = array_filter( explode( "\n", $links ) );
  $tuples = [];

  foreach( $links as $tuple ) {
    $tuple = explode( '|', $tuple );
    $tuple = array_map( 'trim', $tuple );
    $tuples[] = '<li><i class="fa-solid fa-external-link-square-alt"></i><a href="' . esc_url( $tuple[1] ) . '" class="link" rel="noopener" target="_blank">' . wp_strip_all_tags( $tuple[0] ) . '</a></li>';
  }

  // Start HTML ---> ?>
  <div class="recommendation__support">
    <h5><?php
      printf(
        _x( 'Support <em>%s</em>', 'Support _author_', 'fictioneer' ),
        fictioneer_get_field( 'fictioneer_recommendation_author', $args['recommendation_id'] )
      )
    ?></h5>
    <div>
      <ul class="recommendation__list"><?php echo implode( '', $tuples ); ?></ul>
    </div>
  </div>
  <?php // <--- End HTML
}
add_action( 'fictioneer_recommendation_after_content', 'fictioneer_recommendation_support_links', 30 );


?>
