<?php

// =============================================================================
// POST TAGS
// =============================================================================

if ( ! function_exists( 'fictioneer_post_tags' ) ) {
  /**
   * Outputs the HTML for the post tags
   *
   * @since Fictioneer 5.0
   *
   * @param int $post_id The post ID.
   */

  function fictioneer_post_tags( $post_id ) {
    // Setup
    $tags = get_the_tags( $post_id );

    // Abort if...
    if ( ! $tags || get_option( 'fictioneer_hide_tags_on_pages' ) ) return;

		// Start HTML ---> ?>
    <section class="post__tags tag-group">
      <?php echo fictioneer_get_taxonomy_pills( [$tags], '_secondary' ); ?>
    </section>
		<?php // <--- End HTML
  }
}
add_action( 'fictioneer_post_after_content', 'fictioneer_post_tags', 10 );

// =============================================================================
// FEATURED
// =============================================================================

if ( ! function_exists( 'fictioneer_post_featured_list' ) ) {
  /**
   * Outputs the HTML for featured items in the post
   *
   * @since Fictioneer 5.0
   *
   * @param int $post_id The post ID.
   */

  function fictioneer_post_featured_list( $post_id ) {
    global $post;

    // Abort if...
    if ( post_password_required() ) return;

    // Setup
    $featured = fictioneer_get_field( 'fictioneer_post_featured', $post_id );

    // Abort if...
    if ( empty( $featured ) ) return;

    // Clean items of non-published or hidden entries
    foreach ( $featured as $key => $post_id ) {
      if (
        get_post_status( $post_id ) != 'publish' ||
        fictioneer_get_field( 'fictioneer_chapter_hidden', $post_id )
      ) unset( $featured[$key] );
    }

    // Abort again if...
    if ( empty( $featured ) ) return;

    // Start HTML ---> ?>
    <section class="post__featured">
      <ul class="card-list _no-mutation-observer">
        <?php
          // Loop featured content
          foreach ( $featured as $post_id ) {
            // Setup inner loop
            $post = get_post( $post_id );
            $card_args = ['show_type' => true];
            setup_postdata( $post );

            // Cached?
            if ( fictioneer_caching_active() && ! fictioneer_private_caching_active() ) $card_args['cache'] = true;

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
}
add_action( 'fictioneer_post_after_content', 'fictioneer_post_featured_list', 20 );

// =============================================================================
// POST MEDIA BUTTONS
// =============================================================================

if ( ! function_exists( 'fictioneer_post_media_buttons' ) ) {
  /**
   * Outputs the HTML for the post media buttons
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_post_media_buttons() {
    // Abort if...
    if ( post_password_required() ) return;

    // Render template
		get_template_part( 'partials/_share-buttons' );
  }
}
add_action( 'fictioneer_post_footer_left', 'fictioneer_post_media_buttons', 10 );

// =============================================================================
// POST SUBSCRIBE BUTTONS
// =============================================================================

if ( ! function_exists( 'fictioneer_post_subscribe_button' ) ) {
  /**
   * Outputs the HTML for the post subscribe button with popup menu
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_post_subscribe_button() {
    // Abort if...
    if ( post_password_required() ) return;

    // Setup
    $subscribe_buttons = fictioneer_get_subscribe_options();

		if ( ! empty( $subscribe_buttons ) ) {
      // Start HTML ---> ?>
      <div class="toggle-last-clicked button _secondary popup-menu-toggle" tabindex="0" role="button" aria-label="<?php echo fcntr( 'subscribe', true ); ?>">
        <i class="fa-solid fa-bell"></i> <span><?php echo fcntr( 'subscribe' ); ?></span>
        <div class="popup-menu _top _justify-right"><?php echo $subscribe_buttons; ?></div>
      </div>
      <?php // <--- End HTML
    }
  }
}
add_action( 'fictioneer_post_footer_right', 'fictioneer_post_subscribe_button', 10 );

?>
