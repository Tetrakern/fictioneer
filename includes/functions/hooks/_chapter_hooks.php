<?php

// =============================================================================
// LIST OF ALL CHAPTERS
// =============================================================================

/**
 * Outputs the paginated card list for all chapters
 *
 * Note: Added conditionally in the `wp` action hook (priority 10).
 *
 * @since 5.0.0
 * @see chapters.php
 *
 * @param int        $args['current_page']  Current page number of pagination or 1.
 * @param int        $args['post_id']       The post ID of the page.
 * @param WP_Query   $args['chapters']      Paginated query of all published chapters.
 * @param string     $args['queried_type']  The queried post type ('fcn_chapter').
 * @param array      $args['query_args']    The query arguments used.
 * @param string     $args['order']         Current order. Default 'desc'.
 * @param string     $args['orderby']       Current orderby. Default 'modified'.
 * @param int|string $args['ago']           Current date query argument part. Default 0.
 */

function fictioneer_chapters_list( $args ) {
  // Start HTML ---> ?>
  <section class="chapters__list spacing-top container-inline-size">
    <ul id="list-of-chapters" class="scroll-margin-top card-list">

      <?php if ( $args['chapters']->have_posts() ) : ?>

        <?php
          // Card arguments
          $card_args = array(
            'cache' => fictioneer_caching_active( 'card_args' ) && ! fictioneer_private_caching_active(),
            'order' => $args['order'] ?? 'desc',
            'orderby' => $args['orderby'] ?? 'modified',
            'ago' => $args['ago'] ?? 0
          );

          // Filter card arguments
          $card_args = apply_filters( 'fictioneer_filter_chapters_card_args', $card_args, $args );

          while ( $args['chapters']->have_posts() ) {
            $args['chapters']->the_post();

            if ( get_post_meta( get_the_ID(), 'fictioneer_chapter_hidden', true ) ) {
              get_template_part( 'partials/_card-hidden', null, $card_args );
            } else {
              get_template_part( 'partials/_card-chapter', null, $card_args );
            }
          }

          // Actions at end of results
          do_action( 'fictioneer_chapters_end_of_results', $args );
        ?>

      <?php else : ?>

        <?php do_action( 'fictioneer_chapters_no_results', $args ); ?>

        <li class="no-results">
          <span><?php _e( 'No chapters found.', 'fictioneer' ); ?></span>
        </li>

      <?php endif; wp_reset_postdata(); ?>

      <?php
        $pag_args = array(
          'current' => max( 1, get_query_var( 'paged' ) ),
          'total' => $args['chapters']->max_num_pages,
          'prev_text' => fcntr( 'previous' ),
          'next_text' => fcntr( 'next' ),
          'add_fragment' => '#list-of-chapters'
        );
      ?>

      <?php if ( $args['chapters']->max_num_pages > 1 ) : ?>
        <li class="pagination"><?php echo fictioneer_paginate_links( $pag_args ); ?></li>
      <?php endif; ?>

    </ul>
  </section>
  <?php // <--- End HTML
}
add_action( 'fictioneer_chapters_after_content', 'fictioneer_chapters_list', 30 );

// =============================================================================
// CHAPTER ACTIONS
// =============================================================================

/**
 * Outputs the HTML for the chapter top actions
 *
 * Note: Added conditionally in the `wp` action hook (priority 10).
 *
 * @since 5.21.2
 *
 * @param array $args  Hook args passed-through.
 */

function fictioneer_chapter_top_actions( $args ) {
  // Start HTML ---> ?>
  <div class="chapter__actions chapter__actions--top" data-nosnippet>
    <div class="chapter__actions-container chapter__actions-left"><?php
      do_action( 'fictioneer_chapter_actions_top_left', $args, 'top' );
    ?></div>
    <div class="chapter__actions-container chapter__actions-center"><?php
      do_action( 'fictioneer_chapter_actions_top_center', $args, 'top' );
    ?></div>
    <div class="chapter__actions-container chapter__actions-right"><?php
      do_action( 'fictioneer_chapter_actions_top_right', $args, 'top' );
    ?></div>
  </div>
  <?php // <--- End HTML
}
add_action( 'fictioneer_chapter_before_header', 'fictioneer_chapter_top_actions', 1 );

/**
 * Outputs the HTML for the chapter footer
 *
 * Note: Added conditionally in the `wp` action hook (priority 10).
 *
 * @since 5.21.2
 *
 * @param array $args  Hook args passed-through.
 */

function fictioneer_chapter_footer( $args ) {
  // Start HTML ---> ?>
  <footer class="chapter__footer chapter__actions chapter__actions--bottom chapter-end" data-nosnippet>
    <div class="chapter__actions-container chapter__actions-left"><?php
      do_action( 'fictioneer_chapter_actions_bottom_left', $args, 'bottom' );
    ?></div>
    <div class="chapter__actions-container chapter__actions-center"><?php
      do_action( 'fictioneer_chapter_actions_bottom_center', $args, 'bottom' );
    ?></div>
    <div class="chapter__actions-container chapter__actions-right"><?php
      do_action( 'fictioneer_chapter_actions_bottom_right', $args, 'bottom' );
    ?></div>
  </footer>
  <?php // <--- End HTML
}
add_action( 'fictioneer_chapter_after_content', 'fictioneer_chapter_footer', 99 );

// =============================================================================
// CHAPTER GLOBAL NOTE
// =============================================================================

/**
 * Outputs the HTML for the chapter foreword section
 *
 * Note: Added conditionally in the `wp` action hook (priority 10).
 *
 * @since 5.11.1
 *
 * @param WP_Post|null $args['story_post']         The story post object.
 * @param bool|null    $args['password_required']  Whether the post is unlocked or not.
 */

function fictioneer_chapter_global_note( $args ) {
  // Guard
  if ( ! ( $args['story_post'] ?? 0 ) ) {
    return;
  }

  // Setup
  $note = fictioneer_get_content_field( 'fictioneer_story_global_note', $args['story_post']->ID );
  $password_required = $args['password_required'] ?? post_password_required();

  // Abort conditions
  if (
    empty( $note ) ||
    ( strpos( $note, '[!password]' ) !== false && $password_required )
  ) {
    return;
  }

  $note = str_replace( '[!password]', '', $note );

  // Start HTML ---> ?>
  <section id="chapter-global-note" class="chapter__global-note infobox polygon clearfix chapter-note-hideable"><?php
    echo trim( $note );
  ?></section>
  <?php // <--- End HTML
}
add_action( 'fictioneer_chapter_before_header', 'fictioneer_chapter_global_note', 5 );

// =============================================================================
// CHAPTER FOREWORD
// =============================================================================

/**
 * Outputs the HTML for the chapter foreword section
 *
 * Note: Added conditionally in the `wp` action hook (priority 10).
 *
 * @since 5.0.0
 * @since 5.11.1 - Show if started with "[!show]".
 *
 * @param int       $args['chapter_id']         The chapter ID.
 * @param bool|null $args['password_required']  Whether the post is unlocked or not.
 */

function fictioneer_chapter_foreword( $args ) {
  // Setup
  $note = fictioneer_get_content_field( 'fictioneer_chapter_foreword', $args['chapter_id'] );
  $password_required = $args['password_required'] ?? post_password_required();

  // Abort conditions
  if (
    empty( $note ) ||
    ( strpos( $note, '[!show]' ) === false && $password_required )
  ) {
    return;
  }

  $note = str_replace( '[!show]', '', $note );

  // Start HTML ---> ?>
  <section id="chapter-foreword" class="chapter__foreword infobox polygon clearfix chapter-note-hideable"><?php
    echo trim( $note );
  ?></section>
  <?php // <--- End HTML
}
add_action( 'fictioneer_chapter_before_header', 'fictioneer_chapter_foreword', 10 );

// =============================================================================
// CHAPTER WARNINGS
// =============================================================================

/**
 * Outputs the HTML for the chapter warnings section
 *
 * Note: Added conditionally in the `wp` action hook (priority 10).
 *
 * @since 5.0.0
 *
 * @param int       $args['chapter_id']         The chapter ID.
 * @param bool|null $args['password_required']  Whether the post is unlocked or not.
 */

function fictioneer_chapter_warnings( $args ) {
  // Setup
  $warning = get_post_meta( $args['chapter_id'], 'fictioneer_chapter_warning', true );
  $warning_notes = get_post_meta( $args['chapter_id'], 'fictioneer_chapter_warning_notes', true );
  $password_required = $args['password_required'] ?? post_password_required();

  // Abort conditions
  if ( ( ! $warning && ! $warning_notes ) || $password_required ) {
    return '';
  }

  // Start HTML ---> ?>
  <section id="chapter-warning" class="chapter__warning infobox infobox--warning polygon clearfix chapter-note-hideable">
    <?php if ( $warning ) : ?>
      <p><?php
        printf(
          _x( 'Warning: %1$s! — You can hide <em>marked</em> sensitive content %2$s or with the %3$s toggle in the %4$s formatting menu. If provided, <em>alternative</em> content will be displayed instead.', 'Chapter warning (1) with sensitive content toggle (2) and icons (3-4).', 'fictioneer' ),
          $warning,
          '<label id="inline-sensitive-content-toggle" for="reader-settings-sensitive-content-toggle" tabindex="0" role="checkbox" aria-checked="false" aria-label="' . esc_attr__( 'Toggle sensitive content', 'fictioneer' ) . '"><i class="fa-solid off fa-toggle-on"></i><i class="fa-solid on fa-toggle-off"></i> <span>' . _x( 'by clicking here', ' As in hide sensitive content by clicking here.', 'fictioneer' ) . '</span></label>',
          '<i class="fa-solid fa-exclamation-circle"></i>',
          fictioneer_get_icon( 'font-settings' )
        );
      ?></p>
    <?php endif; ?>
    <?php if ( $warning_notes ) : ?>
      <details>
        <summary><strong><?php echo fcntr( 'warning_notes' ); ?></strong></summary>
        <p><?php echo $warning_notes; ?></p>
      </details>
    <?php endif; ?>
  </section>
  <?php // <--- End HTML
}
add_action( 'fictioneer_chapter_before_header', 'fictioneer_chapter_warnings', 20 );

// =============================================================================
// CHAPTER RESIZE BUTTONS
// =============================================================================

/**
 * Outputs the HTML for the chapter resize buttons
 *
 * Note: Added conditionally in the `wp` action hook (priority 10).
 *
 * @since 5.0.0
 */

function fictioneer_chapter_resize_buttons() {
  $decrease_label = esc_attr__( 'Decrease font size', 'fictioneer' );
  $reset_label = esc_attr__( 'Reset font size', 'fictioneer' );
  $increase_label = esc_attr__( 'Increase font size', 'fictioneer' );

  // Start HTML ---> ?>
  <button type="button" id="decrease-font" data-tooltip="<?php echo $decrease_label; ?>" data-modifier="-5" class="button _secondary tooltipped" aria-label="<?php echo $decrease_label; ?>">
    <?php fictioneer_icon( 'fa-minus' ); ?>
  </button>
  <button type="reset" id="reset-font" data-tooltip="<?php echo $reset_label; ?>" class="button _secondary tooltipped" aria-label="<?php echo $decrease_label; ?>">
    <?php fictioneer_icon( 'fa-square' ); ?>
  </button>
  <button type="button" id="increase-font" data-tooltip="<?php echo $increase_label; ?>" data-modifier="5" class="button _secondary tooltipped" aria-label="<?php echo $decrease_label; ?>">
    <?php fictioneer_icon( 'fa-plus' ); ?>
  </button>
  <?php // <--- End HTML
}
add_action( 'fictioneer_chapter_actions_top_left', 'fictioneer_chapter_resize_buttons', 10 );

// =============================================================================
// CHAPTER NAV BUTTONS
// =============================================================================

/**
 * Outputs the HTML for the chapter navigation buttons
 *
 * Note: Added conditionally in the `wp` action hook (priority 10).
 *
 * @since 5.0.0
 * @since 5.14.0 - Added indexed chapter IDs.
 * @since 5.27.4 - Refactored with filtered output array.
 *
 * @param array       $args['chapter_ids']          IDs of visible chapters in the same story or empty array.
 * @param array       $args['indexed_chapter_ids']  IDs of accessible chapters in the same story or empty array.
 * @param int|boolean $args['prev_index']           Index of previous chapter or false if outside bounds.
 * @param int|boolean $args['next_index']           Index of next chapter or false if outside bounds.
 * @param string      $location                     Either 'top' or 'bottom'.
 */

function fictioneer_chapter_nav_buttons( $args, $location ) {
  // Setup
  $post_id = get_the_ID();
  $post_status = get_post_status( $post_id );
  $unlisted = get_post_meta( $post_id, 'fictioneer_chapter_hidden', true );
  $output = [];

  // Filter allowed status
  $allowed_statuses = apply_filters(
    'fictioneer_filter_chapter_nav_buttons_allowed_statuses',
    ['publish'],
    $post_id,
    $args,
    $location
  );

  $show_nav = in_array( $post_status, $allowed_statuses );

  // Previous
  if ( $show_nav && ! $unlisted && $args['prev_index'] !== false ) {
    $output['previous'] = sprintf(
      '<a href="%s#start" title="%s" class="button _secondary _navigation _prev">%s</a>',
      get_permalink( $args['indexed_chapter_ids'][ $args['prev_index'] ] ),
      get_the_title( $args['indexed_chapter_ids'][ $args['prev_index'] ] ),
      fcntr( 'previous' )
    );
  }

  // Scroll top/bottom
  if ( $location === 'top' ) {
    $output['scroll'] = sprintf(
      '<a href="#bottom" data-block="center" aria-label="%s" name="top" class="anchor button _secondary tooltipped" data-tooltip="%s"><i class="fa-solid fa-caret-down"></i></a>',
      __( 'Scroll to bottom of the chapter', 'fictioneer' ),
      esc_attr__( 'Scroll to bottom', 'fictioneer' )
    );
  } else {
    $output['scroll'] = sprintf(
      '<a href="#top" data-block="center" aria-label="%s" name="bottom" class="anchor button _secondary tooltipped" data-tooltip="%s"><i class="fa-solid fa-caret-up"></i></a>',
      __( 'Scroll to top of the chapter', 'fictioneer' ),
      esc_attr__( 'Scroll to top', 'fictioneer' )
    );
  }

  // Next
  if ( $show_nav && ! $unlisted && $args['next_index'] ) {
    $output['next'] = sprintf(
      '<a href="%s#start" title="%s" class="button _secondary _navigation _next">%s</a>',
      get_permalink( $args['indexed_chapter_ids'][ $args['next_index'] ] ),
      get_the_title( $args['indexed_chapter_ids'][ $args['next_index'] ] ),
      fcntr( 'next' )
    );
  }

  echo implode( '', apply_filters( 'fictioneer_filter_chapter_nav_buttons', $output, $post_id, $args, $location ) );
}
add_action( 'fictioneer_chapter_actions_top_right', 'fictioneer_chapter_nav_buttons', 10, 2 );
add_action( 'fictioneer_chapter_actions_bottom_right', 'fictioneer_chapter_nav_buttons', 10, 2 );

// =============================================================================
// CHAPTER FORMATTING BUTTON
// =============================================================================

/**
 * Outputs the HTML for the chapter formatting button
 *
 * Note: Added conditionally in the `wp` action hook (priority 10).
 *
 * @since 5.0.0
 */

function fictioneer_chapter_formatting_button() {
  // Start HTML ---> ?>
  <button class="button _secondary open" data-action="click->fictioneer#toggleModal" data-fictioneer-id-param="formatting-modal" aria-label="<?php esc_attr_e( 'Open chapter formatting modal', 'fictioneer' ); ?>">
    <?php fictioneer_icon( 'font-settings' ); ?>
    <span class="hide-below-tablet"><?php echo fcntr( 'formatting' ); ?></span>
  </button>
  <?php // <--- End HTML
}
add_action( 'fictioneer_chapter_actions_top_center', 'fictioneer_chapter_formatting_button', 10 );

// =============================================================================
// CHAPTER SUBSCRIBE BUTTON
// =============================================================================

/**
 * Outputs the HTML for the chapter subscribe button with popup menu
 *
 * Note: Added conditionally in the `wp` action hook (priority 10).
 *
 * @since 5.0.0
 */

function fictioneer_chapter_subscribe_button() {
  $subscribe_buttons = fictioneer_get_subscribe_options();
  $post_status = get_post_status( get_the_ID() );

  $allowed_statuses = apply_filters( 'fictioneer_filter_chapter_subscribe_button_statuses', ['publish'], get_the_ID() );

  // Do not render on hidden posts
  if ( ! in_array( $post_status, $allowed_statuses ) ) {
    return;
  }

  if ( ! empty( $subscribe_buttons ) ) {
    // Start HTML ---> ?>
    <div class="button _secondary popup-menu-toggle" tabindex="0" role="button" aria-label="<?php echo fcntr( 'subscribe', true ); ?>" data-fictioneer-last-click-target="toggle" data-action="click->fictioneer-last-click#toggle">
      <i class="fa-solid fa-bell"></i> <span class="hide-below-tablet"><?php echo fcntr( 'subscribe' ); ?></span>
      <div class="popup-menu _top _center"><?php echo $subscribe_buttons; ?></div>
    </div>
    <?php // <--- End HTML
  }
}
add_action( 'fictioneer_chapter_actions_bottom_center', 'fictioneer_chapter_subscribe_button', 10 );

// =============================================================================
// CHAPTER FULLSCREEN BUTTONS
// =============================================================================

/**
 * Outputs the HTML for the chapter fullscreen button
 *
 * Note: Added conditionally in the `wp` action hook (priority 10).
 *
 * @since 5.0.0
 */

function fictioneer_chapter_fullscreen_buttons() {
  // Setup
  $open = esc_attr__( 'Enter fullscreen', 'fictioneer' );
  $close = esc_attr__( 'Exit fullscreen', 'fictioneer' );

  // Start HTML ---> ?>
  <button type="button" class="open-fullscreen button _secondary button--fullscreen hide-on-fullscreen hide-on-iOS tooltipped" data-tooltip="<?php echo $open; ?>" aria-label="<?php echo $open; ?>" data-action="click->fictioneer-chapter#openFullscreen">
    <?php fictioneer_icon( 'expand' ); ?>
  </button>
  <button type="button" class="close-fullscreen button _secondary button--fullscreen show-on-fullscreen hide-on-iOS hidden tooltipped" data-tooltip="<?php echo $close; ?>" aria-label="<?php echo $close; ?>" data-action="click->fictioneer-chapter#closeFullscreen">
    <?php fictioneer_icon( 'collapse' ); ?>
  </button>
  <?php // <--- End HTML
}
add_action( 'fictioneer_chapter_actions_top_center', 'fictioneer_chapter_fullscreen_buttons', 20 );

// =============================================================================
// CHAPTER INDEX DIALOG TOGGLE
// =============================================================================

/**
 * Outputs the HTML for the chapter index dialog modal toggle
 *
 * Note: Added conditionally in the `wp` action hook (priority 10).
 *
 * @since 5.0.0
 *
 * @param WP_Post|null $args['story_post']  Optional. The story the chapter belongs to.
 */

function fictioneer_chapter_index_modal_toggle( $args ) {
  // Abort if there is no story assigned
  if ( ! $args['story_post'] ) {
    return;
  }

  // Start HTML ---> ?>
  <button class="button _secondary tooltipped" data-tooltip="<?php esc_attr_e( 'Index', 'fictioneer' ); ?>" aria-label="<?php esc_attr_e( 'Index', 'fictioneer' ); ?>" data-click-action="open-dialog-modal" data-click-target="#fictioneer-chapter-index-dialog">
    <i class="fa-solid fa-list"></i>
  </button>
  <?php // <--- End HTML
}
add_action( 'fictioneer_chapter_actions_bottom_center', 'fictioneer_chapter_index_modal_toggle', 20 );

// =============================================================================
// CHAPTER INDEX DIALOG
// =============================================================================

/**
 * Outputs the HTML for the chapter index dialog modal
 *
 * Note: Added conditionally in the `wp` action hook (priority 10).
 *
 * @since 5.25.0
 *
 * @param int $story_id  ID of the story.
 */

function fictioneer_render_chapter_index_modal_html( $story_id ) {
  // Start HTML ---> ?>
  <dialog class="dialog-modal _chapter-index" id="fictioneer-chapter-index-dialog" data-post-id="<?php the_ID(); ?>">
    <div class="dialog-modal__wrapper">
      <button class="dialog-modal__close" data-click-action="close-dialog-modal" aria-label="<?php esc_attr_e( 'Close modal', 'fictioneer' ); ?>"><?php fictioneer_icon( 'fa-xmark' ); ?></button>
      <div class="dialog-modal__header"><?php _ex( 'Chapter Index', 'Chapter index modal header.', 'fictioneer' ); ?></div>
      <div class="dialog-modal__row _chapter-index"><?php
        echo fictioneer_get_chapter_index_html( $story_id );
      ?></div>
    </div>
  </dialog>
  <?php // <--- End HTML
}

// =============================================================================
// CHAPTER BOOKMARK JUMP BUTTON
// =============================================================================

/**
 * Outputs the HTML for the chapter bookmark jump button
 *
 * Note: Added conditionally in the `wp` action hook (priority 10).
 *
 * @since 5.0.0
 */

function fictioneer_chapter_bookmark_jump_button() {
  // Check if available
  if ( ! get_option( 'fictioneer_enable_bookmarks' ) ) {
    return;
  }

  // Start HTML ---> ?>
  <button type="button" class="button _secondary button--bookmark" data-fictioneer-chapter-target="bookmarkScroll" data-fictioneer-bookmarks-target="bookmarkScroll" data-action="click->fictioneer-chapter#scrollToBookmark" hidden>
    <i class="fa-solid fa-bookmark"></i>
    <span class="hide-below-tablet"><?php echo fcntr( 'bookmark' ); ?></span>
  </button>
  <?php // <--- End HTML
}
add_action( 'fictioneer_chapter_actions_top_center', 'fictioneer_chapter_bookmark_jump_button', 30 );
add_action( 'fictioneer_chapter_actions_bottom_center', 'fictioneer_chapter_bookmark_jump_button', 30 );

// =============================================================================
// CHAPTER MEDIA BUTTONS
// =============================================================================

/**
 * Outputs the HTML for the chapter media buttons
 *
 * Note: Added conditionally in the `wp` action hook (priority 10).
 *
 * @since 5.0.0
 */

function fictioneer_chapter_media_buttons() {
  $post_status = get_post_status( get_the_ID() );

  $allowed_statuses = apply_filters( 'fictioneer_filter_chapter_media_buttons_statuses', ['publish'], get_the_ID() );

  // Do not render on hidden posts
  if ( ! in_array( $post_status, $allowed_statuses ) ) {
    return;
  }

  // Render media buttons
  echo fictioneer_get_media_buttons();
}
add_action( 'fictioneer_chapter_actions_bottom_left', 'fictioneer_chapter_media_buttons', 10 );

// =============================================================================
// CHAPTER AFTERWORD
// =============================================================================

/**
 * Outputs the HTML for the chapter afterword
 *
 * Note: Added conditionally in the `wp` action hook (priority 10).
 *
 * @since 5.0.0
 * @since 5.11.1 - Show if started with "[!show]".
 *
 * @param int       $args['chapter_id']         The chapter ID.
 * @param bool|null $args['password_required']  Whether the post is unlocked or not.
 */

function fictioneer_chapter_afterword( $args ) {
  // Setup
  $note = fictioneer_get_content_field( 'fictioneer_chapter_afterword', $args['chapter_id'] );
  $password_required = $args['password_required'] ?? post_password_required();

  // Abort conditions
  if (
    empty( $note ) ||
    ( strpos( $note, '[!show]' ) === false && $password_required )
  ) {
    return;
  }

  $note = str_replace( '[!show]', '', $note );

  // Start HTML ---> ?>
  <section id="chapter-afterword" class="chapter__afterword infobox polygon clearfix chapter-note-hideable"><?php
    echo trim( $note );
  ?></section>
  <?php // <--- End HTML
}
add_action( 'fictioneer_chapter_after_content', 'fictioneer_chapter_afterword', 10 );

// =============================================================================
// CHAPTER SUPPORT BOX
// =============================================================================

/**
 * Outputs the HTML for the chapter support links
 *
 * Note: Added conditionally in the `wp` action hook (priority 10).
 *
 * @since 5.0.0
 * @since 5.11.1
 *
 * @param WP_User      $args['author']      Author of the post.
 * @param WP_Post|null $args['story_post']  Optional. Post object of the story.
 * @param int          $args['chapter_id']  The chapter ID.
 */

function fictioneer_chapter_support_links( $args ) {
  // Abort conditions
  if ( get_post_meta( $args['chapter_id'], 'fictioneer_chapter_hide_support_links', true ) ) {
    return;
  }

  // Setup
  $author_id = $args['author']->ID;
  $story_id = $args['story_post']->ID ?? null;
  $support_urls = fictioneer_get_support_links( $args['chapter_id'], $story_id, $author_id );
  $support_links = [];

  // Topwebfiction?
  if ( array_key_exists( 'topwebfiction', $support_urls ) ) {
    $support_links['topwebfiction'] = array(
      'label' => __( 'Top Web Fiction', 'fictioneer' ),
      'icon' => '<i class="fa-solid fa-circle-up"></i>',
      'link' => $support_urls['topwebfiction']
    );
  }

  // Patreon?
  if ( array_key_exists( 'patreon', $support_urls ) ) {
    $support_links['patreon'] = array(
      'label' => __( 'Patreon', 'fictioneer' ),
      'icon' => '<i class="fa-brands fa-patreon"></i>',
      'link' => $support_urls['patreon']
    );
  }

  // Ko-Fi?
  if ( array_key_exists( 'kofi', $support_urls ) ) {
    $support_links['kofi'] = array(
      'label' => __( 'Ko-Fi', 'fictioneer' ),
      'icon' => fictioneer_get_icon( 'kofi' ),
      'link' => $support_urls['kofi']
    );
  }

  // SubscribeStar?
  if ( array_key_exists( 'subscribestar', $support_urls ) ) {
    $support_links['subscribestar'] = array(
      'label' => __( 'SubscribeStar', 'fictioneer' ),
      'icon' => '<i class="fa-solid fa-s"></i>',
      'link' => $support_urls['subscribestar']
    );
  }

  // PayPal?
  if ( array_key_exists( 'paypal', $support_urls ) ) {
    $support_links['paypal'] = array(
      'label' => __( 'PayPal', 'fictioneer' ),
      'icon' => '<i class="fa-brands fa-paypal"></i>',
      'link' => $support_urls['paypal']
    );
  }

  // Donation?
  if ( array_key_exists( 'donation', $support_urls ) ) {
    $support_links['donation'] = array(
      'label' => __( 'Donation', 'fictioneer' ),
      'icon' => '<i class="fa-solid fa-hand-holding-heart"></i>',
      'link' => $support_urls['donation']
    );
  }

  // Apply filter
  $support_links = apply_filters( 'fictioneer_filter_chapter_support_links', $support_links, $args );

  // Abort if no support links
  if ( count( $support_links ) < 1 ) {
    return;
  }

  // Support message
  $support_message = get_the_author_meta( 'fictioneer_support_message', $author_id ) ? get_the_author_meta( 'fictioneer_support_message', $author_id ) : __( 'You can support the author on', 'fictioneer' );

  // Start HTML ---> ?>
  <section class="chapter__support clearfix">
    <div class="chapter__support-message"><?php echo $support_message; ?></div>
      <div class="chapter__support-links">
      <?php foreach ( $support_links as $link ) : ?>
        <a href="<?php echo $link['link']; ?>" rel="noopener" target="_blank">
          <?php echo $link['icon']; ?>
          <span><?php echo $link['label']; ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </section>
  <?php // <--- End HTML
}
add_action( 'fictioneer_chapter_after_content', 'fictioneer_chapter_support_links', 20 );

// =============================================================================
// CHAPTER MICRO MENU
// =============================================================================

/**
 * Outputs the HTML for the chapter micro menu
 *
 * Note: Added conditionally in the `wp` action hook (priority 10).
 *
 * @since 5.0.0
 * @since 5.28.0 - Check whether password is required.
 *
 * @param WP_Post|null $args['story_post']           Optional. Post object of the story.
 * @param int          $args['chapter_id']           The chapter ID.
 * @param array        $args['chapter_ids']          IDs of visible chapters in the same story or empty array.
 * @param array        $args['indexed_chapter_ids']  IDs of accessible chapters in the same story or empty array.
 * @param int|boolean  $args['prev_index']           Index of previous chapter or false if outside bounds.
 * @param int|boolean  $args['next_index']           Index of next chapter or false if outside bounds.
 */

function fictioneer_chapter_micro_menu( $args ) {
  if ( post_password_required() ) {
    return;
  }

  echo fictioneer_get_chapter_micro_menu( $args );
}
add_action( 'fictioneer_chapter_after_content', 'fictioneer_chapter_micro_menu', 99 );

// =============================================================================
// CHAPTER PARAGRAPH TOOLS
// =============================================================================

/**
 * Output the HTML for the chapter paragraph tools.
 *
 * Note: Added conditionally in the `wp` action hook (priority 10).
 *
 * @since 5.0.0
 */

function fictioneer_chapter_paragraph_tools() {
  // Setup
  $can_comment = ! fictioneer_is_commenting_disabled( get_the_ID() ) && comments_open();
  $hide_if_logged_out = get_option( 'comment_registration' ) ? 'hide-if-logged-out' : ''; // Safer for cached site

  $scheduled_commenting = get_post_status() !== 'future' ? true :
    ( get_option( 'fictioneer_enable_scheduled_chapter_commenting' ) && is_user_logged_in() );

  // Start HTML ---> ?>
  <div id="paragraph-tools" class="paragraph-tools" data-fictioneer-chapter-target="tools" data-nosnippet>
    <div class="paragraph-tools__actions">
      <?php if ( get_option( 'fictioneer_enable_bookmarks' ) ) : ?>
        <button id="button-set-bookmark" type="button" class="button" data-action="click->fictioneer-chapter#toggleBookmark">
          <i class="fa-solid fa-bookmark"></i>
          <span><?php _ex( 'Bookmark', 'Paragraph tools bookmark button', 'fictioneer' ); ?></span>
          <div class="paragraph-tools__bookmark-colors">
            <div data-color="default" class="paragraph-tools__bookmark-colors-field"></div>
            <div data-color="beta" class="paragraph-tools__bookmark-colors-field"></div>
            <div data-color="gamma" class="paragraph-tools__bookmark-colors-field"></div>
            <div data-color="delta" class="paragraph-tools__bookmark-colors-field"></div>
          </div>
        </button>
      <?php endif; ?>
      <?php if ( $can_comment && $scheduled_commenting ) : ?>
        <button id="button-comment-stack" type="button" class="button <?php echo $hide_if_logged_out ?>" data-action="click->fictioneer-chapter#quote">
          <i class="fa-solid fa-quote-right"></i>
          <span><?php _ex( 'Quote', 'Paragraph tools quote button', 'fictioneer' ); ?></span>
        </button>
      <?php endif; ?>
      <?php if ( $can_comment && $scheduled_commenting && get_option( 'fictioneer_enable_suggestions' ) ) : ?>
        <button id="button-tools-add-suggestion" type="button" class="button <?php echo $hide_if_logged_out ?>" data-action="click->fictioneer-suggestion#toggleModalViaParagraph">
          <i class="fa-solid fa-highlighter"></i>
          <span class="hide-below-480"><?php _ex( 'Suggestion', 'Paragraph tools suggestion button', 'fictioneer' ); ?></span>
        </button>
      <?php endif; ?>
      <?php if ( get_option( 'fictioneer_enable_tts' ) ) : ?>
        <button id="button-tts-set" type="button" class="button">
          <i class="fa-solid fa-volume-up"></i>
          <span class="hide-below-480"><?php _ex( 'TTS', 'Paragraph tools text-to-speech button', 'fictioneer' ); ?></span>
        </button>
      <?php endif; ?>
      <button id="button-get-link" type="button" class="button" data-action="click->fictioneer-chapter#copyLink">
        <i class="fa-solid fa-link"></i>
        <span class="hide-below-480"><?php _ex( 'Link', 'Paragraph tools copy link button', 'fictioneer' ); ?></span>
      </button>
      <button id="button-close-paragraph-tools" type="button" class="button" data-action="click->fictioneer-chapter#closeTools">
        <i class="fa-solid fa-times"></i>
      </button>
    </div>
  </div>
  <?php // <--- End HTML
}
add_action( 'fictioneer_chapter_after_main', 'fictioneer_chapter_paragraph_tools', 10 );

// =============================================================================
// CHAPTER SUGGESTION TOOLS
// =============================================================================

/**
 * Outputs the HTML for the chapter suggestion tools
 *
 * Note: Added conditionally in the `wp` action hook (priority 10).
 *
 * @since 5.0.0
 */

function fictioneer_chapter_suggestion_tools() {
  // Abort if...
  if ( get_post_meta( get_the_ID(), 'fictioneer_disable_commenting', true ) || ! comments_open() ) {
    return;
  }

  // Setup
  $hide_if_logged_out = get_option( 'comment_registration' ) ? 'hide-if-logged-out' : ''; // Safer for cached site

  // Start HTML ---> ?>
  <div id="selection-tools" class="invisible suggestion-tools <?php echo $hide_if_logged_out; ?>" data-nosnippet>
    <button id="button-add-suggestion" type="button" class="button button--suggestion">
      <i class="fa-solid fa-highlighter"></i>
      <span><?php _e( 'Add Suggestion', 'fictioneer' ); ?></span>
    </button>
  </div>
  <?php // <--- End HTML
}

if ( get_option( 'fictioneer_enable_suggestions' ) ) {
  add_action( 'fictioneer_chapter_after_main', 'fictioneer_chapter_suggestion_tools', 10 );
}

// =============================================================================
// BOOKMARK DATA
// =============================================================================

/**
 * Outputs the JSON for the chapter bookmark data.
 *
 * Note: Added conditionally in the `wp` action hook (priority 10).
 *
 * @since 5.27.3
 *
 * @param int   $args['chapter_id']  The chapter ID.
 * @param array $args['story_data']  Optional. Post object of the story.
 */

function fictioneer_output_bookmark_data( $args ) {
  // Setup
  $title = get_post_meta( $args['chapter_id'], 'fictioneer_chapter_list_title', true );
  $title = trim( wp_strip_all_tags( $title ) );
  $title = $title ?: fictioneer_get_safe_title( $args['chapter_id'], 'chapter-bookmark' );
  $story_title = '';
  $snippet_thumbnail_url = get_the_post_thumbnail_url( $args['chapter_id'], 'snippet' );
  $full_thumbnail_url = get_the_post_thumbnail_url( $args['chapter_id'], 'full' );

  if ( $args['story_data'] ) {
    $story_title = $args['story_data']['title'];

    if ( ! $snippet_thumbnail_url ) {
      $snippet_thumbnail_url = get_the_post_thumbnail_url( $args['story_data']['id'], 'snippet' );
      $full_thumbnail_url = get_the_post_thumbnail_url( $args['story_data']['id'], 'full' );
    }
  }

  // Data
  $data = array(
    'thumbnail' => esc_url( $snippet_thumbnail_url ),
    'cover' => esc_url( $full_thumbnail_url ),
    'link' => get_the_permalink( $args['chapter_id'] ),
    'title' => $title,
    'storyTitle' => $story_title,
  );

  // Echo
  wp_print_inline_script_tag(
    wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ),
    array(
      'id' => 'fictioneer-bookmark-data',
      'type' => 'application/json',
      'data-jetpack-boost' => 'ignore',
      'data-no-optimize' => '1',
      'data-no-defer' => '1',
      'data-no-minify' => '1'
    )
  );
}

if ( get_option( 'fictioneer_enable_bookmarks' ) ) {
  add_action( 'fictioneer_chapter_after_content', 'fictioneer_output_bookmark_data', 99 );
}
