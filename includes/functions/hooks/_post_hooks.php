<?php

// =============================================================================
// POST TAGS
// =============================================================================

/**
 * Outputs the HTML for the post tags
 *
 * Note: Added conditionally in the `wp` action hook (priority 10).
 *
 * @since 5.0.0
 * @since 5.25.0 - Added $args.
 *
 * @param int   $post_id  The post ID.
 * @param array $args     Additional arguments.
 */

function fictioneer_post_tags( $post_id, $args ) {
  // Check render context
  if ( ( $args['context'] ?? 0 ) !== 'single-post' ) {
    return;
  }

  // Setup
  $tags = get_the_tags( $post_id );

  // Abort if...
  if ( ! $tags || get_option( 'fictioneer_hide_tags_on_pages' ) ) {
    return;
  }

  // Start HTML ---> ?>
  <section class="post__tags tag-group">
    <?php echo fictioneer_get_taxonomy_pills( array( 'tags' => $tags ), 'post_after_content', '_secondary' ); ?>
  </section>
  <?php // <--- End HTML
}
add_action( 'fictioneer_post_after_content', 'fictioneer_post_tags', 10, 2 );

// =============================================================================
// FEATURED
// =============================================================================

/**
 * Outputs the HTML for featured items in the post
 *
 * Note: Added conditionally in the `wp` action hook (priority 10).
 *
 * @since 5.0.0
 * @since 5.25.0 - Added $args.
 *
 * @param int   $post_id  The post ID.
 * @param array $args     Additional arguments.
 */

function fictioneer_post_featured_list( $post_id, $args ) {
  // Check render context
  if ( ( $args['context'] ?? 0 ) !== 'single-post' ) {
    return;
  }

  global $post;

  // Abort if...
  if ( post_password_required() ) {
    return;
  }

  // Setup
  $featured = get_post_meta( $post_id, 'fictioneer_post_featured', true );

  // Abort if...
  if ( ! is_array( $featured ) || empty( $featured ) ) {
    return;
  }

  // Query
  $query_args = array(
    'post_type' => ['post', 'page', 'fcn_story', 'fcn_chapter', 'fcn_collection', 'fcn_recommendation'],
    'post_status' => 'publish',
    'post__in' => $featured ?: [0], // Must not be empty!
    'ignore_sticky_posts' => 1,
    'orderby' => 'post__in',
    'posts_per_page' => -1,
    'no_found_rows' => true // Improve performance
  );

  $featured_query = new WP_Query( $query_args );

  // Abort if...
  if ( empty( $featured_query->posts ) ) {
    return;
  }

  // Prime author cache
  if ( function_exists( 'update_post_author_caches' ) ) {
    update_post_author_caches( $featured_query->posts );
  }

  // Start HTML ---> ?>
  <section class="post__featured container-inline-size">
    <ul class="card-list _no-mutation-observer">
      <?php
        // Loop featured content
        foreach ( $featured_query->posts as $post ) {
          // Setup inner loop
          setup_postdata( $post );
          $card_args = array( 'show_type' => true );

          // Cached?
          if ( fictioneer_caching_active( 'card_args' ) && ! fictioneer_private_caching_active() ) {
            $card_args['cache'] = true;
          }

          // Echo correct card
          fictioneer_echo_card( $card_args );
        }

        // Restore outer loop
        wp_reset_postdata();
      ?>
    </ul>
  </section>
  <?php // <--- End HTML
}
add_action( 'fictioneer_post_after_content', 'fictioneer_post_featured_list', 20, 2 );

// =============================================================================
// POST MEDIA BUTTONS
// =============================================================================

/**
 * Outputs the HTML for the post media buttons
 *
 * Note: Added conditionally in the `wp` action hook (priority 10).
 *
 * @since 5.0.0
 * @since 5.25.0 - Added parameters.
 *
 * @param int   $post_id  The post ID.
 * @param array $args     Additional arguments.
 */

function fictioneer_post_media_buttons( $post_id, $args ) {
  // Abort if...
  if ( post_password_required() || ( $args['context'] ?? 0 ) !== 'single-post' ) {
    return;
  }

  // Render media buttons
  echo fictioneer_get_media_buttons();
}
add_action( 'fictioneer_post_footer_left', 'fictioneer_post_media_buttons', 10, 2 );

// =============================================================================
// POST SUBSCRIBE BUTTONS
// =============================================================================

/**
 * Outputs the HTML for the post subscribe button with popup menu
 *
 * Note: Added conditionally in the `wp` action hook (priority 10).
 *
 * @since 5.0.0
 * @since 5.25.0 - Added parameters.
 *
 * @param int   $post_id  The post ID.
 * @param array $args     Additional arguments.
 */

function fictioneer_post_subscribe_button( $post_id, $args ) {
  // Abort if...
  if ( post_password_required() || ( $args['context'] ?? 0 ) !== 'single-post' ) {
    return;
  }

  // Setup
  $subscribe_buttons = fictioneer_get_subscribe_options();

  if ( ! empty( $subscribe_buttons ) ) {
    // Start HTML ---> ?>
    <div class="button _secondary popup-menu-toggle" tabindex="0" role="button" aria-label="<?php echo fcntr( 'subscribe', true ); ?>" data-fictioneer-last-click-target="toggle" data-action="click->fictioneer-last-click#toggle">
      <i class="fa-solid fa-bell"></i> <span><?php echo fcntr( 'subscribe' ); ?></span>
      <div class="popup-menu _top _justify-right"><?php echo $subscribe_buttons; ?></div>
    </div>
    <?php // <--- End HTML
  }
}
add_action( 'fictioneer_post_footer_right', 'fictioneer_post_subscribe_button', 10, 2 );
