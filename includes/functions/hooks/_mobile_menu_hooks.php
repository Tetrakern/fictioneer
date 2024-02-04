<?php

// =============================================================================
// OUTPUT MOBILE MENU
// =============================================================================

/**
 * Outputs the HTML for the mobile menu
 *
 * Outputs the HTML for the mobile menu. The mobile menu is structured into
 * sections (top|center|bottom) > frames > panels. Only one frame per section
 * should be displayed at a time, segmenting the menu.
 *
 * @since 5.0.0
 *
 * @param int|null       $args['post_id']           Optional. Current post ID.
 * @param int|null       $args['story_id']          Optional. Current story ID (if chapter).
 * @param string|boolean $args['header_image_url']  URL of the filtered header image or false.
 * @param array          $args['header_args']       Arguments passed to the header.php partial.
 */

function fictioneer_output_mobile_menu( $args ) {
  // Setup
  $style = get_theme_mod( 'mobile_menu_style', 'minimize_to_right' );
  $classes = [];

  if ( $style === 'minimize_to_right' ) {
    $classes[] = '_advanced-mobile-menu';
  }

  // Start HTML ---> ?>
  <input id="mobile-menu-toggle" type="checkbox" autocomplete="off" tabindex="-1" hidden>
  <div class="mobile-menu <?php echo implode( ' ', $classes ); ?>">
    <div class="mobile-menu__top"><?php do_action( 'fictioneer_mobile_menu_top' ); ?></div>
    <div class="mobile-menu__center">
      <div class="mobile-menu__frame _active" data-frame="main"><?php
        do_action( 'fictioneer_mobile_menu_main_frame_panels' );
      ?></div>
      <?php do_action( 'fictioneer_mobile_menu_center' ); ?>
    </div>
    <div class="mobile-menu__bottom"><?php do_action( 'fictioneer_mobile_menu_bottom' ); ?></div>
  </div>
  <?php // <--- End HTML
}
add_action( 'fictioneer_body', 'fictioneer_output_mobile_menu', 20 );

// =============================================================================
// MOBILE MENU USER HEADER MENU
// =============================================================================

/**
 * Adds the _icon-menu.php partial to the mobile menu
 *
 * @since 5.0.0
 */

function fictioneer_mobile_user_icon_menu() {
  get_template_part( 'partials/_icon-menu', null, ['location' => 'in-mobile-menu'] );
}
add_action( 'fictioneer_mobile_menu_top', 'fictioneer_mobile_user_icon_menu', 10 );

// =============================================================================
// MOBILE MENU QUICK BUTTONS
// =============================================================================

/**
 * Adds quick buttons to the mobile menu
 *
 * @since 5.0.0
 */

function fictioneer_mobile_quick_buttons() {
  // Setup
  $post_type = is_archive() ? 'archive' : get_post_type();
  $output = [];

  // Build
  $output['minimalist'] = '<label for="site-setting-minimal" class="button _quick"><span>' . __( 'Minimalist', 'fictioneer' ) . '</span></label>';

  if ( $post_type === 'fcn_chapter' && ! is_search() ) {
    $output['darken'] = '<button class="button _quick button-change-lightness" value="-0.2">' . __( 'Darken', 'fictioneer' ) . '</button>';

    $output['brighten'] = '<button class="button _quick button-change-lightness" value="0.2">' . __( 'Brighten', 'fictioneer' ) . '</button>';

    $output['justify'] = '<label for="reader-settings-justify-toggle" class="button _quick"><span>' . __( 'Justify', 'fictioneer' ) . '</span></label>';

    $output['indent'] = '<label for="reader-settings-indent-toggle" class="button _quick"><span>' . __( 'Indent', 'fictioneer' ) . '</span></label>';

    $output['formatting'] = '<label for="modal-formatting-toggle" class="button _quick"><span>' . fcntr( 'formatting' ) . '</span></label>';

    $output['previous_font'] = '<button class="button _quick button-change-font font-stepper" value="-1" id="quick-button-prev-font">' . __( 'Prev Font', 'fictioneer' ) . '</button>';

    $output['next_font'] = '<button class="button _quick button-change-font font-stepper" value="1" id="quick-button-next-font">' . __( 'Next Font', 'fictioneer' ) . '</button>';
  } else {
    $output['covers'] = '<label for="site-setting-covers" class="button _quick"><span>' . __( 'Covers', 'fictioneer' ) . '</span></label>';

    $output['textures'] = '<label for="site-setting-background-textures" class="button _quick"><span>' . __( 'Textures', 'fictioneer' ) . '</span></label>';

    $output['polygons'] = '<label for="site-setting-polygons" class="button _quick"><span>' . __( 'Polygons', 'fictioneer' ) . '</span></label>';
  }

  // Apply filter
  $output = apply_filters( 'fictioneer_filter_mobile_quick_buttons', $output );

  // Return
  echo implode( '', $output );
}
add_action( 'fictioneer_mobile_menu_bottom', 'fictioneer_mobile_quick_buttons', 10 );

// =============================================================================
// MOBILE MENU FOLLOWS FRAME
// =============================================================================

/**
 * Adds the Follows frame to the mobile menu
 *
 * @since 5.0.0
 */

function fictioneer_mobile_follows_frame() {
  // Start HTML ---> ?>
  <div class="mobile-menu__frame"  data-frame="follows">
    <div class="mobile-menu__panel mobile-menu__follows-panel">
      <div class="mobile-menu__panel-header">
        <button class="mobile-menu__back-button">
          <i class="fa-solid fa-caret-left mobile-menu__item-icon"></i> <?php _e( 'Back', 'fictioneer' ); ?>
        </button>
        <button class="mark-follows-read mobile-menu__panel-action-button">
          <i class="fa-solid fa-check"></i>
        </button>
      </div>
      <div id="mobile-menu-follows-list" class="mobile-menu__list mobile-follow-notifications">
        <div class="mobile-content-is-loading">
          <i class="fa-solid fa-spinner fa-spin" style="--fa-animation-duration: .8s;"></i>
        </div>
      </div>
    </div>
  </div>
  <?php // <--- End HTML
}

if ( get_option( 'fictioneer_enable_follows' ) ) {
  add_action( 'fictioneer_mobile_menu_center', 'fictioneer_mobile_follows_frame', 10 );
}

// =============================================================================
// MOBILE MENU BOOKMARKS FRAME
// =============================================================================

/**
 * Adds the bookmarks frame to the mobile menu
 *
 * @since 5.0.0
 */

function fictioneer_mobile_bookmarks_frame() {
  get_template_part( 'partials/_template_mobile_bookmark' );

  // Start HTML ---> ?>
  <div class="mobile-menu__frame" data-frame="bookmarks">
    <div class="mobile-menu__panel mobile-menu__bookmarks-panel" data-editing="false">
      <div class="mobile-menu__panel-header">
        <button class="mobile-menu__back-button">
          <i class="fa-solid fa-caret-left mobile-menu__item-icon"></i> <?php _e( 'Back', 'fictioneer' ); ?>
        </button>
        <button id="button-mobile-menu-toggle-bookmarks-edit" class="mobile-menu__panel-action-button">
          <i class="fa-solid fa-pen off"></i><i class="fa-solid fa-check on"></i>
        </button>
      </div>
      <ul class="mobile-menu__list mobile-menu__bookmark-list" data-empty="<?php echo fcntr( 'no_bookmarks', true ); ?>"></ul>
    </div>
  </div>
  <?php // <--- End HTML
}

if ( get_option( 'fictioneer_enable_bookmarks' ) ) {
  add_action( 'fictioneer_mobile_menu_center', 'fictioneer_mobile_bookmarks_frame', 20 );
}

// =============================================================================
// MOBILE MENU CHAPTERS FRAME
// =============================================================================

/**
 * Adds the chapters frame to the mobile menu
 *
 * @since 5.0.0
 */

function fictioneer_mobile_chapters_frame() {
  // Abort conditions
  if ( get_post_type() != 'fcn_chapter' || is_archive() || is_search() ) {
    return;
  }

  // Start HTML ---> ?>
  <div class="mobile-menu__frame" data-frame="chapters">
    <div class="mobile-menu__panel mobile-menu__chapters-panel">
      <div class="mobile-menu__panel-header">
        <button class="mobile-menu__back-button">
          <i class="fa-solid fa-caret-left mobile-menu__item-icon"></i> <?php _e( 'Back', 'fictioneer' ); ?>
        </button>
      </div>
      <div id="mobile-menu-chapters-list" class="mobile-menu__list _chapters"></div>
    </div>
  </div>
  <?php // <--- End HTML
}
add_action( 'fictioneer_mobile_menu_center', 'fictioneer_mobile_chapters_frame', 30 );

// =============================================================================
// MOBILE MENU NAVIGATION PANEL
// =============================================================================

/**
 * Adds the navigation panel to the mobile menu
 *
 * @since 5.0.0
 */

function fictioneer_mobile_navigation_panel() {
  if ( ! has_nav_menu( 'nav_menu' ) ) {
    return;
  }

  // Start HTML ---> ?>
  <nav id="mobile-navigation" class="mobile-navigation mobile-menu__panel"><?php
    // Cloned from the main navigation via JS
  ?></nav>
  <?php // <--- End HTML
}
add_action( 'fictioneer_mobile_menu_main_frame_panels', 'fictioneer_mobile_navigation_panel', 10 );

// =============================================================================
// MOBILE MENU LISTS PANEL
// =============================================================================

/**
 * Adds the lists panel to the mobile menu
 *
 * @since 5.0.0
 */

function fictioneer_mobile_lists_panel() {
  // Setup
  $post_type = is_archive() ? 'archive' : get_post_type();
  $output = [];

  // Chapters?
  if ( $post_type === 'fcn_chapter' && get_post_meta( get_the_ID(), 'fictioneer_chapter_story', true ) && ! is_search() ) {
    $output['chapters'] = '<button class="mobile-menu__frame-button" data-frame-target="chapters"><i class="fa-solid fa-caret-right mobile-menu__item-icon"></i> ' . __( 'Chapters', 'fictioneer' ) . '</button>';
  }

  // Bookmarks?
  if ( get_option( 'fictioneer_enable_bookmarks' ) ) {
    $output['bookmarks'] = '<button class="mobile-menu__frame-button" data-frame-target="bookmarks"><i class="fa-solid fa-caret-right mobile-menu__item-icon"></i> ' . fcntr( 'bookmarks' ) . '</button>';
  }

  // Follows?
  if ( get_option( 'fictioneer_enable_follows' ) ) {
    $output['follows'] = '<button class="mobile-menu__frame-button hide-if-logged-out follows-alert-number" data-frame-target="follows"><i class="fa-solid fa-caret-right mobile-menu__item-icon"></i> ' . fcntr( 'follows' ) . '</button>';
  }

  // Render (if content)
  if ( ! empty( $output ) ) {
    echo '<div class="mobile-menu__panel mobile-menu__lists-panel">' . implode( '', $output ) . '</div>';
  }
}
add_action( 'fictioneer_mobile_menu_main_frame_panels', 'fictioneer_mobile_lists_panel', 20 );

// =============================================================================
// MOBILE MENU USER NAVIGATION
// =============================================================================

/**
 * Adds the user menu to the mobile menu
 *
 * @since 5.0.0
 */

function fictioneer_mobile_user_menu() {
  // Setup
  $post_type = is_archive() ? 'archive' : get_post_type();
  $bookmarks_link = fictioneer_get_assigned_page_link( 'fictioneer_bookmarks_page' );
  $bookshelf_link = fictioneer_get_assigned_page_link( 'fictioneer_bookshelf_page' );
  $discord_link = get_option( 'fictioneer_discord_invite_link' );
  $can_checkmarks = get_option( 'fictioneer_enable_checkmarks' );
  $can_follows = get_option( 'fictioneer_enable_follows' );
  $can_reminders = get_option( 'fictioneer_enable_reminders' );
  $profile_link = get_edit_profile_url();
  $profile_page_id = intval( get_option( 'fictioneer_user_profile_page', -1 ) ?: -1 );
  $output = [];

  if ( $profile_page_id && $profile_page_id > 0 ) {
    $profile_link = fictioneer_get_assigned_page_link( 'fictioneer_user_profile_page' );
  }

  // Build
  if ( ! empty( $profile_link ) && fictioneer_show_auth_content() ) {
    $output['account'] = '<a href="' . esc_url( $profile_link ) . '" class="hide-if-logged-out"><i class="fa-solid fa-circle-user mobile-menu__item-icon"></i> ' . fcntr( 'account' ) . '</a>';
  }

  if ( FICTIONEER_SHOW_SEARCH_IN_MENUS ) {
    $output['search'] = '<a href="' . esc_url( home_url( '/?s=' ) ) . '" class="hide-if-logged-out"><i class="fa-solid fa-magnifying-glass mobile-menu__item-icon"></i> ' . __( 'Search', 'fictioneer' ) . '</a>';
  }

  $output['site_settings'] = '<label for="modal-site-settings-toggle"><i class="fa-solid fa-tools mobile-menu__item-icon"></i> ' . fcntr( 'site_settings' ) . '</label>';

  if ( ! empty( $discord_link ) ) {
    $output['discord'] = '<a href="' . esc_url( $discord_link ) . '" rel="noopener noreferrer nofollow"><i class="fa-brands fa-discord mobile-menu__item-icon"></i> ' . __( 'Discord', 'fictioneer' ) . '</a>';
  }

  if ( $bookshelf_link && fictioneer_show_auth_content() && ( $can_checkmarks || $can_follows || $can_reminders ) ) {
    $output['bookshelf'] = '<a href="' . esc_url( $bookshelf_link ) . '" rel="noopener noreferrer nofollow"><i class="fa-solid fa-list mobile-menu__item-icon"></i> ' . fcntr( 'bookshelf' ) . '</a>';
  }

  if ( ! empty( $bookmarks_link ) && get_option( 'fictioneer_enable_bookmarks' ) ) {
    $output['bookmarks'] = '<a href="' . esc_url( $bookmarks_link ) . '" rel="noopener noreferrer nofollow"><i class="fa-solid fa-bookmark mobile-menu__item-icon"></i> ' . fcntr( 'bookmarks' ) . '</a>';
  }

  if ( $post_type === 'fcn_chapter' && ! is_search() ) {
    $output['formatting'] = '<label for="modal-formatting-toggle">' . fictioneer_get_icon( 'font-settings', 'mobile-menu__item-icon' ) . ' ' . fcntr( 'formatting' ) . '</label>';
  }

  if (
    $post_type === 'fcn_chapter' &&
    ! is_search() &&
    comments_open() &&
    ! fictioneer_is_commenting_disabled() &&
    ! post_password_required()
  ) {
    $output['comment_jump'] = '<a id="mobile-menu-comment-jump" class="comments-toggle" rel="noopener noreferrer nofollow"><i class="fa-solid fa-comments mobile-menu__item-icon"></i> ' . fcntr( 'jump_to_comments' ) . '</a>';
  }

  if (
    $post_type === 'fcn_chapter' &&
    ! is_search() &&
    get_option( 'fictioneer_enable_bookmarks' ) &&
    ! post_password_required()
  ) {
    $output['bookmark_jump'] = '<a id="mobile-menu-bookmark-jump" rel="noopener noreferrer nofollow" hidden><i class="fa-solid fa-bookmark mobile-menu__item-icon"></i> ' . fcntr( 'jump_to_bookmark' ) . '</a>';
  }

  if ( fictioneer_show_auth_content() ) {
    $output['logout'] = '<a href="' . fictioneer_get_logout_url() . '" data-click="logout" rel="noopener noreferrer nofollow" class="hide-if-logged-out">' . fictioneer_get_icon( 'fa-logout', 'mobile-menu__item-icon', '', 'style="transform: translateY(-1px);"' ) . ' ' . fcntr( 'logout' ) . '</a>';
  }

  if ( get_option( 'fictioneer_enable_oauth' ) && ! is_user_logged_in() ) {
    $output['login'] = '<label for="modal-login-toggle" class="hide-if-logged-in subscriber-login">' . fictioneer_get_icon( 'fa-login', 'mobile-menu__item-icon' ) . ' ' . fcntr( 'login' ) . '</label>';
  }

  // Apply filter
  $output = apply_filters( 'fictioneer_filter_mobile_user_menu_items', $output );

  // Return
  echo '<div id="mobile-menu-user-panel" class="mobile-menu__panel">' . implode( '', $output ) . '</div>';
}
add_action( 'fictioneer_mobile_menu_main_frame_panels', 'fictioneer_mobile_user_menu', 30 );

?>
