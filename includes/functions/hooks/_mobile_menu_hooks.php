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

  if ( $style === 'right_slide_in' ) {
    $classes[] = '_open-right';
  }

  // Start HTML ---> ?>
  <div class="mobile-menu <?php echo implode( ' ', $classes ); ?>" data-action="fictioneer:bodyClick@window->fictioneer-mobile-menu#clickOutside">
    <div class="mobile-menu__top"><?php do_action( 'fictioneer_mobile_menu_top' ); ?></div>
    <div class="mobile-menu__center">
      <div id="mobile-frame-main" class="mobile-menu__frame _active" data-fictioneer-mobile-menu-target="frame"><?php
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
 * Renders the icon menu to the mobile menu
 *
 * @since 5.0.0
 * @since 5.25.0 - Replaced partial with fictioneer_render_icon_menu().
 */

function fictioneer_mobile_user_icon_menu() {
  fictioneer_render_icon_menu( array( 'location' => 'in-mobile-menu' ) );
}
add_action( 'fictioneer_mobile_menu_top', 'fictioneer_mobile_user_icon_menu', 10 );

// =============================================================================
// MOBILE MENU QUICK BUTTONS
// =============================================================================

/**
 * Adds quick buttons to the mobile menu
 *
 * @since 5.0.0
 * @since 5.9.4 - Removed output buffer.
 */

function fictioneer_mobile_quick_buttons() {
  // Setup
  $post_type = is_archive() ? 'archive' : get_post_type();
  $output = [];

  // Build
  $output['minimalist'] = sprintf(
    '<label for="site-setting-minimal" class="button _quick"><span>%s</span></label>',
    __( 'Minimalist', 'fictioneer' )
  );

  if ( $post_type === 'fcn_chapter' && ! is_search() ) {
    $output['darken'] = sprintf(
      '<button class="button _quick" data-action="click->fictioneer-mobile-menu#changeLightness" value="-0.2">%s</button>',
      __( 'Darken', 'fictioneer' )
    );

    $output['brighten'] = sprintf(
      '<button class="button _quick" data-action="click->fictioneer-mobile-menu#changeLightness" value="0.2">%s</button>',
      __( 'Brighten', 'fictioneer' )
    );

    $output['justify'] = sprintf(
      '<label for="reader-settings-justify-toggle" class="button _quick"><span>%s</span></label>',
      __( 'Justify', 'fictioneer' )
    );

    $output['indent'] = sprintf(
      '<label for="reader-settings-indent-toggle" class="button _quick"><span>%s</span></label>',
      __( 'Indent', 'fictioneer' )
    );

    $output['formatting'] = sprintf(
      '<button class="button _quick" data-action="click->fictioneer#toggleModal" data-fictioneer-id-param="formatting-modal"><span>%s</span></button>',
      fcntr( 'formatting' )
    );

    $output['previous_font'] = sprintf(
      '<button class="button _quick button-change-font font-stepper" value="-1" id="quick-button-prev-font">%s</button>',
      __( 'Prev Font', 'fictioneer' )
    );

    $output['next_font'] = sprintf(
      '<button class="button _quick button-change-font font-stepper" value="1" id="quick-button-next-font">%s</button>',
      __( 'Next Font', 'fictioneer' )
    );
  } else {
    $output['covers'] = sprintf(
      '<label for="site-setting-covers" class="button _quick"><span>%s</span></label>',
      __( 'Covers', 'fictioneer' )
    );

    $output['textures'] = sprintf(
      '<label for="site-setting-background-textures" class="button _quick"><span>%s</span></label>',
      __( 'Textures', 'fictioneer' )
    );

    $output['polygons'] = sprintf(
      '<label for="site-setting-polygons" class="button _quick"><span>%s</span></label>',
      __( 'Polygons', 'fictioneer' )
    );
  }

  // Apply filter
  $output = apply_filters( 'fictioneer_filter_mobile_quick_buttons', $output );

  // Implode and return HTML
  echo implode( '', $output );
}
add_action( 'fictioneer_mobile_menu_bottom', 'fictioneer_mobile_quick_buttons', 10 );

// =============================================================================
// MOBILE MENU ALERTS FRAME
// =============================================================================

/**
 * Add the alerts frame to the mobile menu.
 *
 * @since 5.0.0
 */

function fictioneer_mobile_alerts_frame() {
  // Start HTML ---> ?>
  <div id="mobile-frame-alerts" class="mobile-menu__frame" data-fictioneer-mobile-menu-target="frame">
    <div class="mobile-menu__panel mobile-menu__alerts-panel">
      <div class="mobile-menu__panel-header">
        <button class="mobile-menu__back-button" data-action="click->fictioneer-mobile-menu#back">
          <i class="fa-solid fa-caret-left mobile-menu__item-icon"></i> <?php _e( 'Back', 'fictioneer' ); ?>
        </button>
      </div>
      <div id="mobile-menu-alerts-list" class="mobile-menu__list mobile-alerts"  data-fictioneer-alerts-target="mobileAlertList">
        <div class="alert _no-alerts"><?php _e( 'You have no alerts.', 'fictioneer' ); ?></div>
      </div>
    </div>
  </div>
  <?php // <--- End HTML
}

if ( get_option( 'fictioneer_enable_alerts' ) ) {
  add_action( 'fictioneer_mobile_menu_center', 'fictioneer_mobile_alerts_frame', 10 );
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
  fictioneer_get_cached_partial( 'partials/_template_mobile_bookmark' );

  // Start HTML ---> ?>
  <div id="mobile-frame-bookmarks" class="mobile-menu__frame" data-fictioneer-mobile-menu-target="frame">
    <div class="mobile-menu__panel mobile-menu__bookmarks-panel" data-editing="false" data-fictioneer-mobile-menu-target="panelBookmarks">
      <div class="mobile-menu__panel-header">
        <button class="mobile-menu__back-button" data-action="click->fictioneer-mobile-menu#back">
          <i class="fa-solid fa-caret-left mobile-menu__item-icon"></i> <?php _e( 'Back', 'fictioneer' ); ?>
        </button>
        <button id="button-mobile-menu-toggle-bookmarks-edit" class="mobile-menu__panel-action-button" data-action="click->fictioneer-mobile-menu#toggleBookmarksEdit">
          <i class="fa-solid fa-pen off"></i><i class="fa-solid fa-check on"></i>
        </button>
      </div>
      <ul class="mobile-menu__list mobile-menu__bookmark-list" data-empty="<?php echo fcntr( 'no_bookmarks', true ); ?>" data-fictioneer-mobile-menu-target="bookmarks"></ul>
    </div>
  </div>
  <?php // <--- End HTML
}

if ( get_option( 'fictioneer_enable_bookmarks' ) ) {
  add_action( 'fictioneer_mobile_menu_center', 'fictioneer_mobile_bookmarks_frame', 20 );
}

// =============================================================================
// MOBILE MENU NAVIGATION PANEL
// =============================================================================

/**
 * Adds the navigation panel to the mobile menu
 *
 * @since 5.0.0
 * @since 5.20.0 - Added Elementor support.
 */

function fictioneer_mobile_navigation_panel() {
  // Start HTML ---> ?>
  <nav id="mobile-navigation" class="mobile-navigation mobile-menu__panel"><?php
    if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'mobile_nav_menu' ) ) {
      if ( has_nav_menu( 'nav_menu' ) ) {
        $menu = null;
        $transient_enabled = fictioneer_enable_menu_transients( 'mobile_nav_menu' );

        if ( $transient_enabled ) {
          $menu = get_transient( 'fictioneer_mobile_nav_menu_html' );
        }

        if ( empty( $menu ) ) {
          $menu = wp_nav_menu(
            array(
              'theme_location' => 'nav_menu',
              'menu_class' => 'mobile-navigation__list',
              'container' => '',
              'menu_id' => 'mobile-menu-navigation',
              'items_wrap' => '<ul id="%1$s" data-menu-id="mobile" class="%2$s">%3$s</ul>',
              'echo' => false
            )
          );

          if ( $menu !== false ) {
            $menu = str_replace( ['current_page_item', 'current-menu-item', 'aria-current="page"'], '', $menu );
          }

          if ( $transient_enabled ) {
            set_transient( 'fictioneer_mobile_nav_menu_html', $menu );
          }
        }

        echo $menu;
      }
    }
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
 * @since 5.9.4 - Removed output buffer.
 */

function fictioneer_mobile_lists_panel() {
  // Setup
  $post_type = is_archive() ? 'archive' : get_post_type();
  $output = [];

  // Chapters?
  if ( $post_type === 'fcn_chapter' && fictioneer_get_chapter_story_id( get_the_ID() ) && ! is_search() ) {
    $output['chapters'] = sprintf(
      '<button class="mobile-menu__frame-button mobile-frame-button-chapters" data-click-action="open-dialog-modal" data-click-target="#fictioneer-chapter-index-dialog"><i class="fa-solid fa-caret-right mobile-menu__item-icon"></i> %s</button>',
      __( 'Chapters', 'fictioneer' )
    );
  }

  // Bookmarks?
  if ( get_option( 'fictioneer_enable_bookmarks' ) ) {
    $output['bookmarks'] = sprintf(
      '<button class="mobile-menu__frame-button mobile-frame-button-bookmarks" data-action="click->fictioneer-mobile-menu#openFrame click->fictioneer-mobile-menu#setMobileBookmarks" data-fictioneer-mobile-menu-frame-param="bookmarks"><i class="fa-solid fa-caret-right mobile-menu__item-icon"></i> %s</button>',
      fcntr( 'bookmarks' )
    );
  }

  // Alerts?
  if ( get_option( 'fictioneer_enable_alerts' ) ) {
    $output['alerts'] = sprintf(
      '<button class="mobile-menu__frame-button hide-if-logged-out mobile-frame-button-alerts" data-fictioneer-alerts-target="newAlertsDisplay" data-frame-target="alerts" data-action="click->fictioneer-mobile-menu#openFrame" data-fictioneer-mobile-menu-frame-param="alerts"><i class="fa-solid fa-caret-right mobile-menu__item-icon"></i> %s</button>',
      __( 'Alerts', 'fictioneer' )
    );
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
 * @since 5.9.4 - Removed output buffer.
 */

function fictioneer_mobile_user_menu() {
  // Setup
  $post_type = is_archive() ? 'archive' : get_post_type();
  $bookmarks_link = fictioneer_get_assigned_page_link( 'fictioneer_bookmarks_page' );
  $bookshelf_link = fictioneer_get_assigned_page_link( 'fictioneer_bookshelf_page' );
  $discord_link = get_option( 'fictioneer_discord_invite_link' );
  $profile_link = get_edit_profile_url();
  $profile_page_id = intval( get_option( 'fictioneer_user_profile_page', -1 ) ?: -1 );
  $password_required = post_password_required();
  $output = [];

  if ( $profile_page_id && $profile_page_id > 0 ) {
    $profile_link = fictioneer_get_assigned_page_link( 'fictioneer_user_profile_page' );
  }

  // Build
  if ( ! empty( $profile_link ) && fictioneer_show_auth_content() ) {
    $output['account'] = sprintf(
      '<a href="%s" class="hide-if-logged-out"><i class="fa-solid fa-circle-user mobile-menu__item-icon"></i> %s</a>',
      esc_url( $profile_link ),
      fcntr( 'account' )
    );
  }

  if ( FICTIONEER_SHOW_SEARCH_IN_MENUS ) {
    $output['search'] = sprintf(
      '<a href="%s" rel="nofollow"><i class="fa-solid fa-magnifying-glass mobile-menu__item-icon"></i> %s</a>',
      esc_url( home_url( '/?s' ) ),
      __( 'Search', 'fictioneer' )
    );
  }

  $output['site_settings'] = sprintf(
    '<button data-action="click->fictioneer#toggleModal" data-fictioneer-id-param="site-settings-modal"><i class="fa-solid fa-tools mobile-menu__item-icon"></i> %s</button>',
    fcntr( 'site_settings' )
  );

  if ( ! empty( $discord_link ) ) {
    $output['discord'] = sprintf(
      '<a href="%s" rel="noopener noreferrer nofollow"><i class="fa-brands fa-discord mobile-menu__item-icon"></i> %s</a>',
      esc_url( $discord_link ),
      __( 'Discord', 'fictioneer' )
    );
  }

  if (
    $bookshelf_link &&
    fictioneer_show_auth_content() &&
    (
      get_option( 'fictioneer_enable_checkmarks' ) ||
      get_option( 'fictioneer_enable_follows' ) ||
      get_option( 'fictioneer_enable_reminders' )
    )
  ) {
    $output['bookshelf'] = sprintf(
      '<a href="%s" class="hide-if-logged-out" rel="noopener noreferrer nofollow"><i class="fa-solid fa-list mobile-menu__item-icon"></i> %s</a>',
      esc_url( $bookshelf_link ),
      fcntr( 'bookshelf' )
    );
  }

  if ( ! empty( $bookmarks_link ) && get_option( 'fictioneer_enable_bookmarks' ) ) {
    $output['bookmarks'] = sprintf(
      '<a href="%s" rel="noopener noreferrer nofollow"><i class="fa-solid fa-bookmark mobile-menu__item-icon"></i> %s</a>',
      esc_url( $bookmarks_link ),
      fcntr( 'bookmarks' )
    );
  }

  if ( $post_type === 'fcn_chapter' && ! is_search() ) {
    $output['formatting'] = sprintf(
      '<button data-action="click->fictioneer#toggleModal" data-fictioneer-id-param="formatting-modal">%s %s</button>',
      fictioneer_get_icon( 'font-settings', 'mobile-menu__item-icon' ),
      fcntr( 'formatting' )
    );
  }

  if (
    $post_type === 'fcn_chapter' &&
    ! is_search() &&
    comments_open() &&
    ! fictioneer_is_commenting_disabled() &&
    ! $password_required
  ) {
    $output['comment_jump'] = sprintf(
      '<a id="mobile-menu-comment-jump" class="comments-toggle" data-action="click->fictioneer-mobile-menu#scrollToComments"><i class="fa-solid fa-comments mobile-menu__item-icon"></i> %s</a>',
      fcntr( 'jump_to_comments' )
    );
  }

  if (
    $post_type === 'fcn_chapter' &&
    ! is_search() &&
    get_option( 'fictioneer_enable_bookmarks' ) &&
    ! $password_required
  ) {
    $output['bookmark_jump'] = sprintf(
      '<a id="mobile-menu-bookmark-jump" data-fictioneer-bookmarks-target="bookmarkScroll" data-action="click->fictioneer-mobile-menu#scrollToBookmark" hidden><i class="fa-solid fa-bookmark mobile-menu__item-icon"></i> %s</a>',
      fcntr( 'jump_to_bookmark' )
    );
  }

  if ( fictioneer_show_auth_content() ) {
    $output['logout'] = sprintf(
      '<a href="%s" data-action="click->fictioneer#logout" rel="noopener noreferrer nofollow" class="hide-if-logged-out">%s %s</a>',
      fictioneer_get_logout_url(),
      fictioneer_get_icon( 'fa-logout', 'mobile-menu__item-icon', '', 'style="transform: translateY(-1px);"' ),
      fcntr( 'logout' )
    );
  }

  if ( fictioneer_show_login() ) {
    $output['login'] = sprintf(
      '<button class="hide-if-logged-in subscriber-login" data-action="click->fictioneer#toggleModal" data-fictioneer-id-param="login-modal">%s %s</button>',
      fictioneer_get_icon( 'fa-login', 'mobile-menu__item-icon' ),
      fcntr( 'login' )
    );
  }

  // Apply filter
  $output = apply_filters( 'fictioneer_filter_mobile_user_menu_items', $output );

  // Return
  echo '<div id="mobile-menu-user-panel" class="mobile-menu__panel">' . implode( '', $output ) . '</div>';
}
add_action( 'fictioneer_mobile_menu_main_frame_panels', 'fictioneer_mobile_user_menu', 30 );
