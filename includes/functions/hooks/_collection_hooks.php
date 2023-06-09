<?php

// =============================================================================
// LIST OF ALL COLLECTIONS
// =============================================================================

if ( ! function_exists( 'fictioneer_collections_list' ) ) {
  /**
   * Outputs the paginated card list for all collections
   *
   * @since 5.0
   * @see collections.php
   *
   * @param int        $args['current_page']  Current page number of pagination or 1.
   * @param int        $args['post_id']       The post ID.
   * @param WP_Query   $args['collections']   Paginated query of all published collections.
   * @param string     $args['queried_type']  The queried post type ('fcn_collection').
   * @param array      $args['query_args']    The query arguments used.
   * @param string     $args['order']         Current order. Default 'desc'.
   * @param string     $args['orderby']       Current orderby. Default 'modified'.
   * @param int|string $args['ago']           Current date query argument part. Default 0.
   */

  function fictioneer_collections_list( $args ) {
    // Start HTML ---> ?>
    <section class="collections__list spacing-top">
      <ul class="card-list" id="list-of-collections">

        <?php if ( $args['collections']->have_posts() ) : ?>

          <?php
            // Card arguments
            $card_args = array(
              'cache' => fictioneer_caching_active() && ! fictioneer_private_caching_active(),
              'order' => $args['order'] ?? 'desc',
              'orderby' => $args['orderby'] ?? 'modified',
              'ago' => $args['ago'] ?? 0
            );

            // Filter card arguments
            $card_args = apply_filters( 'fictioneer_filter_collections_card_args', $card_args, $args );

            while ( $args['collections']->have_posts() ) {
              $args['collections']->the_post();
              get_template_part( 'partials/_card-collection', null, $card_args );
            }

            // Actions at end of results
            do_action( 'fictioneer_collections_end_of_results', $args );
          ?>

        <?php else: ?>

          <?php do_action( 'fictioneer_collections_no_results', $args ); ?>

          <li class="no-results">
            <span><?php _e( 'No collections found.', 'fictioneer' ) ?></span>
          </li>

        <?php endif; wp_reset_postdata(); ?>

        <?php
          $pag_args = array(
            'base' => add_query_arg( 'paged', '%#%' ),
            'format' => '?paged=%#%',
            'current' => max( 1, get_query_var( 'paged' ) ),
            'total' => $args['collections']->max_num_pages,
            'prev_text' => fcntr( 'previous' ),
            'next_text' => fcntr( 'next' ),
            'add_fragment' => '#list-of-collections'
          );
        ?>

        <?php if ( $args['collections']->max_num_pages > 1 ) : ?>
          <li class="pagination"><?php echo fictioneer_paginate_links( $pag_args ); ?></li>
        <?php endif; ?>

      </ul>
    </section>
    <?php // <--- End HTML
  }
}
add_action( 'fictioneer_collections_after_content', 'fictioneer_collections_list', 30 );

// =============================================================================
// COLLECTION TAGS & WARNINGS
// =============================================================================

if ( ! function_exists( 'fictioneer_collection_tags_and_warnings' ) ) {
  /**
   * Outputs the HTML for the collection page tags and warnings
   *
   * @since Fictioneer 5.0
   *
   * @param WP_Post  $args['collection']     Collection post object.
   * @param int      $args['collection_id']  The collection post ID.
   * @param string   $args['title']          Safe collection title.
   * @param int      $args['current_page']   Number of the current page or 1.
   * @param int      $args['max_pages']      Total number of pages or 1.
   * @param array    $args['featured_list']  IDs of featured items in the collection.
   * @param WP_Query $args['featured_query'] Paginated query of featured items.
   */

  function fictioneer_collection_tags_and_warnings( $args ) {
    // Setup
    $tags = get_the_tags( $args['collection_id'] );
    $warnings = get_the_terms( $args['collection_id'], 'fcn_content_warning' );

    // Flags
    $tags_shown = $tags && ! get_option( 'fictioneer_hide_tags_on_pages' );
    $warnings_shown = $warnings && ! get_option( 'fictioneer_hide_content_warnings_on_pages' );

    // Abort conditions...
    if ( ! $tags_shown && ! $warnings_shown ) return;

    // Prepare
    $tag_args = [];
    if ( $tags_shown ) $tag_args[] = $tags;
    if ( $warnings_shown ) $tag_args[] = $warnings;

		// Start HTML ---> ?>
    <section class="collection__tags-and-warnings tag-group"><?php
      echo fictioneer_get_taxonomy_pills( $tag_args, '_secondary' );
    ?></section>
		<?php // <--- End HTML
  }
}
add_action( 'fictioneer_collection_after_content', 'fictioneer_collection_tags_and_warnings', 10 );

// =============================================================================
// COLLECTION STATISTICS
// =============================================================================

if ( ! function_exists( 'fictioneer_collection_statistics' ) ) {
  /**
   * Outputs the HTML for the collection page statistics
   *
   * @since Fictioneer 5.0
   *
   * @param WP_Post  $args['collection']     Collection post object.
   * @param int      $args['collection_id']  The collection post ID.
   * @param string   $args['title']          Safe collection title.
   * @param int      $args['current_page']   Number of the current page or 1.
   * @param int      $args['max_pages']      Total number of pages or 1.
   * @param array    $args['featured_list']  IDs of featured items in the collection.
   * @param WP_Query $args['featured_query'] Paginated query of featured items.
   */

  function fictioneer_collection_statistics( $args ) {
    // Abort if...
    if ( post_password_required() ) return;

    // Render template
    get_template_part( 'partials/_collection-statistics', null, $args );
  }
}
add_action( 'fictioneer_collection_after_content', 'fictioneer_collection_statistics', 20 );

// =============================================================================
// COLLECTION FEATURED LIST
// =============================================================================

if ( ! function_exists( 'fictioneer_collection_featured_list' ) ) {
  /**
   * Outputs the HTML for the collection page featured list
   *
   * @since Fictioneer 5.0
   *
   * @param WP_Post  $args['collection']     Collection post object.
   * @param int      $args['collection_id']  The collection post ID.
   * @param string   $args['title']          Safe collection title.
   * @param int      $args['current_page']   Number of the current page or 1.
   * @param int      $args['max_pages']      Total number of pages or 1.
   * @param array    $args['featured_list']  IDs of featured items in the collection.
   * @param WP_Query $args['featured_query'] Paginated query of featured items.
   */

  function fictioneer_collection_featured_list( $args ) {
    // Abort if...
    if ( post_password_required() ) return;

    // Start HTML ---> ?>
    <section class="collection__list spacing-top">
      <ul id="featured-list" class="card-list">
        <?php
          // Render cards...
          if ( $args['featured_query']->have_posts() ) {
            // Loop through featured posts...
            while ( $args['featured_query']->have_posts() ) {
              // Setup
              $args['featured_query']->the_post();
              $card_args = array( 'show_type' => true );

              // Cached?
              if ( fictioneer_caching_active() && ! fictioneer_private_caching_active() ) $card_args['cache'] = true;

              // Echo correct card
              fictioneer_echo_card( $card_args );
            }

            // Restore global query
            wp_reset_postdata();

            // Setup pagination
            $pag_args = array(
              'base' => add_query_arg( 'pg', '%#%' ),
              'format' => '?pg=%#%',
              'current' => $args['current_page'],
              'total' => $args['max_pages'] ?? 1,
              'prev_text' => fcntr( 'previous' ),
              'next_text' => fcntr( 'next' ),
              'add_fragment' => '#featured-list'
            );

            // Render pagination if necessary
            if ( $args['max_pages'] > 1 ) {
              ?><li class="pagination"><?php echo fictioneer_paginate_links( $pag_args ); ?></li><?php
            }
          }
        ?>
      </ul>
    </section>
		<?php // <--- End HTML
  }
}
add_action( 'fictioneer_collection_after_content', 'fictioneer_collection_featured_list', 30 );


?>
