<?php

// =============================================================================
// LIST OF ALL CHAPTERS
// =============================================================================

/**
 * Outputs the paginated card list for all chapters
 *
 * @since 5.0
 * @see chapters.php
 *
 * @param int        $args['current_page']  Current page number of pagination or 1.
 * @param int        $args['post_id']       The post ID.
 * @param WP_Query   $args['chapters']      Paginated query of all published chapters.
 * @param string     $args['queried_type']  The queried post type ('fcn_chapter').
 * @param array      $args['query_args']    The query arguments used.
 * @param string     $args['order']         Current order. Default 'desc'.
 * @param string     $args['orderby']       Current orderby. Default 'modified'.
 * @param int|string $args['ago']           Current date query argument part. Default 0.
 */

function fictioneer_chapters_list( $args ) {
  // Start HTML ---> ?>
  <section class="chapters__list spacing-top">
    <ul id="list-of-chapters" class="scroll-margin-top card-list">

      <?php if ( $args['chapters']->have_posts() ) : ?>

        <?php
          // Card arguments
          $card_args = array(
            'cache' => fictioneer_caching_active() && ! fictioneer_private_caching_active(),
            'order' => $args['order'] ?? 'desc',
            'orderby' => $args['orderby'] ?? 'modified',
            'ago' => $args['ago'] ?? 0
          );

          // Filter card arguments
          $card_args = apply_filters( 'fictioneer_filter_chapters_card_args', $card_args, $args );

          while ( $args['chapters']->have_posts() ) {
            $args['chapters']->the_post();

            if ( fictioneer_get_field( 'fictioneer_chapter_hidden' ) ) {
              get_template_part( 'partials/_card-hidden', null, $card_args );
            } else {
              get_template_part( 'partials/_card-chapter', null, $card_args );
            }
          }

          // Actions at end of results
          do_action( 'fictioneer_chapters_end_of_results', $args );
        ?>

      <?php else: ?>

        <?php do_action( 'fictioneer_chapters_no_results', $args ); ?>

        <li class="no-results">
          <span><?php _e( 'No chapters found.', 'fictioneer' ) ?></span>
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
// CHAPTER FOREWORD
// =============================================================================

/**
 * Outputs the HTML for the chapter foreword section
 *
 * @since Fictioneer 5.0
 *
 * @param int $args['chapter_id']  The chapter ID.
 */

function fictioneer_chapter_foreword( $args ) {
  // Setup
  $foreword = fictioneer_get_content_field( 'fictioneer_chapter_foreword', $args['chapter_id'] );

  // Abort conditions
  if ( empty( $foreword ) || post_password_required() ) {
    return '';
  }

  // Start HTML ---> ?>
  <section id="chapter-foreword" class="chapter__foreword infobox polygon clearfix"><?php echo $foreword; ?></section>
  <?php // <--- End HTML
}
add_action( 'fictioneer_chapter_before_header', 'fictioneer_chapter_foreword', 10 );

// =============================================================================
// CHAPTER WARNINGS
// =============================================================================

/**
 * Outputs the HTML for the chapter warnings section
 *
 * @since Fictioneer 5.0
 *
 * @param int $args['chapter_id']  The chapter ID.
 */

function fictioneer_chapter_warnings( $args ) {
  // Setup
  $warning = fictioneer_get_field( 'fictioneer_chapter_warning', $args['chapter_id'] );
  $warning_notes = fictioneer_get_field( 'fictioneer_chapter_warning_notes', $args['chapter_id'] );

  // Abort conditions
  if ( ( ! $warning && ! $warning_notes ) || post_password_required() ) {
    return '';
  }

  // Start HTML ---> ?>
  <section id="chapter-warning" class="chapter__warning infobox infobox--warning polygon clearfix">
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
 * @since Fictioneer 5.0
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
 * @since Fictioneer 5.0
 *
 * @param array       $args['chapter_ids']  IDs of visible chapters in the same story or empty array.
 * @param int|boolean $args['prev_index']   Index of previous chapter or false if outside bounds.
 * @param int|boolean $args['next_index']   Index of next chapter or false if outside bounds.
 * @param string      $location             Either 'top' or 'bottom'.
 */

function fictioneer_chapter_nav_buttons( $args, $location ) {
  $post_status = get_post_status( get_the_ID() );

  // Do not render on hidden posts
  if ( $post_status !== 'publish' ) {
    return;
  }

  // Start HTML ---> ?>
  <?php if ( $args['prev_index'] !== false ) : ?>
    <a href="<?php echo get_permalink( $args['chapter_ids'][ $args['prev_index'] ] ); ?>" title="<?php echo get_the_title( $args['chapter_ids'][ $args['prev_index'] ] ); ?>" class="button _secondary _navigation _prev"><?php echo fcntr( 'previous' ) ?></a>
  <?php endif; ?>
  <?php if ( $location === 'top' ) : ?>
    <a href="#bottom" data-block="center" aria-label="<?php _e( 'Scroll to bottom of the chapter', 'fictioneer' ); ?>" name="top" class="anchor button _secondary tooltipped" data-tooltip="<?php esc_attr_e( 'Scroll to bottom', 'fictioneer' ); ?>"><i class="fa-solid fa-caret-down"></i></a>
  <?php else : ?>
    <a href="#top" data-block="center" aria-label="<?php _e( 'Scroll to top of the chapter', 'fictioneer' ); ?>" name="bottom" class="anchor button _secondary tooltipped" data-tooltip="<?php esc_attr_e( 'Scroll to top', 'fictioneer' ); ?>"><i class="fa-solid fa-caret-up"></i></a>
  <?php endif; ?>
  <?php if ( $args['next_index'] ) : ?>
    <a href="<?php echo get_permalink( $args['chapter_ids'][ $args['next_index'] ] ); ?>" title="<?php echo get_the_title( $args['chapter_ids'][ $args['next_index'] ] ); ?>" class="button _secondary _navigation _next"><?php echo fcntr( 'next' ); ?></a>
  <?php endif; ?>
  <?php // <--- End HTML
}
add_action( 'fictioneer_chapter_actions_top_right', 'fictioneer_chapter_nav_buttons', 10, 2 );
add_action( 'fictioneer_chapter_actions_bottom_right', 'fictioneer_chapter_nav_buttons', 10, 2 );

// =============================================================================
// CHAPTER FORMATTING BUTTON
// =============================================================================

/**
 * Outputs the HTML for the chapter formatting button
 *
 * @since Fictioneer 5.0
 */

function fictioneer_chapter_formatting_button() {
  // Start HTML ---> ?>
  <label class="button _secondary open" for="modal-formatting-toggle" tabindex="0" role="button" aria-label="<?php esc_attr_e( 'Open chapter formatting modal', 'fictioneer' ); ?>">
    <?php fictioneer_icon( 'font-settings' ); ?>
    <span class="hide-below-tablet"><?php echo fcntr( 'formatting' ); ?></span>
  </label>
  <?php // <--- End HTML
}
add_action( 'fictioneer_chapter_actions_top_center', 'fictioneer_chapter_formatting_button', 10 );

// =============================================================================
// CHAPTER SUBSCRIBE BUTTON
// =============================================================================

/**
 * Outputs the HTML for the chapter subscribe button with popup menu
 *
 * @since Fictioneer 5.0
 */

function fictioneer_chapter_subscribe_button() {
  $subscribe_buttons = fictioneer_get_subscribe_options();
  $post_status = get_post_status( get_the_ID() );

  // Do not render on hidden posts
  if ( $post_status !== 'publish' ) {
    return;
  }

  if ( ! empty( $subscribe_buttons ) ) {
    // Start HTML ---> ?>
    <div class="toggle-last-clicked button _secondary popup-menu-toggle" tabindex="0" role="button" aria-label="<?php echo fcntr( 'subscribe', true ); ?>">
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
 * @since Fictioneer 5.0
 */

function fictioneer_chapter_fullscreen_buttons() {
  // Setup
  $open = esc_attr__( 'Enter fullscreen', 'fictioneer' );
  $close = esc_attr__( 'Exit fullscreen', 'fictioneer' );

  // Start HTML ---> ?>
  <button type="button" class="open-fullscreen button _secondary button--fullscreen hide-on-fullscreen hide-on-iOS tooltipped" data-tooltip="<?php echo $open; ?>" aria-label="<?php echo $open; ?>">
    <?php fictioneer_icon( 'expand' ); ?>
  </button>
  <button type="button" class="close-fullscreen button _secondary button--fullscreen show-on-fullscreen hide-on-iOS hidden tooltipped" data-tooltip="<?php echo $close; ?>" aria-label="<?php echo $close; ?>">
    <?php fictioneer_icon( 'collapse' ); ?>
  </button>
  <?php // <--- End HTML
}
add_action( 'fictioneer_chapter_actions_top_center', 'fictioneer_chapter_fullscreen_buttons', 20 );

// =============================================================================
// CHAPTER INDEX POPUP MENU
// =============================================================================

/**
 * Outputs the HTML for the index popup menu
 *
 * @since Fictioneer 5.0
 *
 * @param WP_Post|null $args['story_post']  Optional. The story the chapter belongs to.
 */

function fictioneer_chapter_index_popup_menu( $args ) {
  $post_status = get_post_status( get_the_ID() );

  // Do not render on hidden posts
  if ( $post_status !== 'publish' ) {
    return;
  }

  // Abort if there is no story assigned
  if ( ! $args['story_post'] ) {
    return;
  }

  // Start HTML ---> ?>
  <div id="chapter-list-popup-toggle" class="toggle-last-clicked button _secondary popup-menu-toggle tooltipped" tabindex="0" role="button" data-tooltip="<?php esc_attr_e( 'Index', 'fictioneer' ); ?>" aria-label="<?php esc_attr_e( 'Index', 'fictioneer' ); ?>">
    <i class="fa-solid fa-list"></i>
    <div class="popup-menu _top _center _align-items-right _v-scrolling">
      <a href="<?php echo get_permalink( $args['story_post'] ) ?>" class="">
        <i class="fa-solid fa-caret-left"></i>
        <span><?php _e( 'Back to Story', 'fictioneer' ); ?></span>
      </a>
      <div class="popup-menu__section" data-target="popup-chapter-list"></div>
    </div>
  </div>
  <?php // <--- End HTML
}
add_action( 'fictioneer_chapter_actions_bottom_center', 'fictioneer_chapter_index_popup_menu', 20 );

// =============================================================================
// CHAPTER BOOKMARK JUMP BUTTON
// =============================================================================

/**
 * Outputs the HTML for the chapter bookmark jump button
 *
 * @since Fictioneer 5.0
 */

function fictioneer_chapter_bookmark_jump_button() {
  // Check if available
  if ( ! get_option( 'fictioneer_enable_bookmarks' ) ) {
    return;
  }

  // Start HTML ---> ?>
  <button type="button" class="button _secondary button--bookmark hidden">
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
 * @since Fictioneer 5.0
 */

function fictioneer_chapter_media_buttons() {
  $post_status = get_post_status( get_the_ID() );

  // Do not render on hidden posts
  if ( $post_status !== 'publish' ) {
    return;
  }

  get_template_part( 'partials/_share-buttons' );
}
add_action( 'fictioneer_chapter_actions_bottom_left', 'fictioneer_chapter_media_buttons', 10 );

// =============================================================================
// CHAPTER AFTERWORD
// =============================================================================

/**
 * Outputs the HTML for the chapter afterword
 *
 * @since Fictioneer 5.0
 *
 * @param int $args['chapter_id']  The chapter ID.
 */

function fictioneer_chapter_afterword( $args ) {
  // Setup
  $afterword = fictioneer_get_content_field( 'fictioneer_chapter_afterword', $args['chapter_id'] ); // ACF formatted output

  // Abort conditions
  if ( empty( $afterword ) || post_password_required() ) {
    return '';
  }

  // Start HTML ---> ?>
  <section id="chapter-afterword" class="chapter__afterword infobox polygon clearfix"><?php echo $afterword; ?></section>
  <?php // <--- End HTML
}
add_action( 'fictioneer_chapter_after_content', 'fictioneer_chapter_afterword', 10 );

// =============================================================================
// CHAPTER SUPPORT BOX
// =============================================================================

/**
 * Outputs the HTML for the chapter support links
 *
 * @since Fictioneer 5.0
 *
 * @param WP_User      $args['author']      Author of the post.
 * @param WP_Post|null $args['story_post']  Optional. Post object of the story.
 * @param int          $args['chapter_id']  The chapter ID.
 */

function fictioneer_chapter_support_links( $args ) {
  // Abort conditions
  if (
    post_password_required() ||
    fictioneer_get_field( 'fictioneer_chapter_hide_support_links', $args['chapter_id'] )
  ) {
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
 * @since Fictioneer 5.0
 *
 * @param WP_Post|null $args['story_post']   Optional. Post object of the story.
 * @param int          $args['chapter_id']   The chapter ID.
 * @param array        $args['chapter_ids']  IDs of visible chapters in the same story or empty array.
 * @param int|boolean  $args['prev_index']   Index of previous chapter or false if outside bounds.
 * @param int|boolean  $args['next_index']   Index of next chapter or false if outside bounds.
 */

function fictioneer_chapter_micro_menu( $args ) {
  echo fictioneer_get_chapter_micro_menu( $args );
}
add_action( 'fictioneer_chapter_after_main', 'fictioneer_chapter_micro_menu', 10 );

// =============================================================================
// CHAPTER PARAGRAPH TOOLS
// =============================================================================

/**
 * Outputs the HTML for the chapter paragraph tools
 *
 * @since Fictioneer 5.0
 */

function fictioneer_chapter_paragraph_tools() {
  // Setup
  $can_comment = ! fictioneer_get_field( 'fictioneer_disable_commenting' ) && comments_open();

  // Start HTML ---> ?>
  <div id="paragraph-tools" class="paragraph-tools">
    <div class="paragraph-tools__actions">
      <?php if ( get_option( 'fictioneer_enable_bookmarks' ) ) : ?>
        <button id="button-set-bookmark" type="button" class="button">
          <i class="fa-solid fa-bookmark"></i>
          <span><?php _ex( 'Bookmark', 'Paragraph tools bookmark button', 'fictioneer' ) ?></span>
          <div class="paragraph-tools__bookmark-colors">
            <div data-color="default" class="paragraph-tools__bookmark-colors-field"></div>
            <div data-color="beta" class="paragraph-tools__bookmark-colors-field"></div>
            <div data-color="gamma" class="paragraph-tools__bookmark-colors-field"></div>
            <div data-color="delta" class="paragraph-tools__bookmark-colors-field"></div>
          </div>
        </button>
      <?php endif; ?>
      <?php if ( $can_comment ) : ?>
        <button id="button-comment-stack" type="button" class="button">
          <i class="fa-solid fa-quote-right"></i>
          <span><?php _ex( 'Quote', 'Paragraph tools quote button', 'fictioneer' ) ?></span>
        </button>
      <?php endif; ?>
      <?php if ( $can_comment && get_option( 'fictioneer_enable_suggestions' ) ) : ?>
        <button id="button-tools-add-suggestion" type="button" class="button">
          <i class="fa-solid fa-highlighter"></i>
          <span class="hide-below-480"><?php _ex( 'Suggestion', 'Paragraph tools suggestion button', 'fictioneer' ) ?></span>
        </button>
      <?php endif; ?>
      <?php if ( get_option( 'fictioneer_enable_tts' ) ) : ?>
        <button id="button-tts-set" type="button" class="button">
          <i class="fa-solid fa-volume-up"></i>
          <span class="hide-below-480"><?php _ex( 'TTS', 'Paragraph tools text-to-speech button', 'fictioneer' ) ?></span>
        </button>
      <?php endif; ?>
      <button id="button-get-link" type="button" class="button">
        <i class="fa-solid fa-link"></i>
        <span class="hide-below-480"><?php _ex( 'Link', 'Paragraph tools copy link button', 'fictioneer' ) ?></span>
      </button>
      <button id="button-close-paragraph-tools" type="button" class="button">
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
 * @since Fictioneer 5.0
 */

function fictioneer_chapter_suggestion_tools() {
  // Abort if...
  if ( fictioneer_get_field( 'fictioneer_disable_commenting' ) || ! comments_open() ) {
    return;
  }

  // Start HTML ---> ?>
  <div id="selection-tools" class="invisible suggestion-tools">
    <button id="button-add-suggestion" type="button" class="button button--suggestion">
      <i class="fa-solid fa-highlighter"></i>
      <span><?php _e( 'Add Suggestion', 'fictioneer' ) ?></span>
    </button>
  </div>
  <?php // <--- End HTML
}

if ( get_option( 'fictioneer_enable_suggestions' ) ) {
  add_action( 'fictioneer_chapter_after_main', 'fictioneer_chapter_suggestion_tools', 10 );
}

?>
