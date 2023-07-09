<?php

// =============================================================================
// OUTPUT MOBILE MENU
// =============================================================================

if ( ! function_exists( 'fictioneer_output_mobile_menu' ) ) {
  /**
   * Outputs the HTML for the mobile menu
   *
   * Outputs the HTML for the mobile menu. The mobile menu is structured into
   * sections (top|center|bottom) > frames > panels. Only one frame per section
   * should be displayed at a time, segmenting the menu.
   *
   * @since Fictioneer 5.0
   *
   * @param int|null       $args['post_id']          Current post ID or null.
   * @param int|null       $args['story_id']         Current story ID (if chapter) or null.
   * @param string|boolean $args['header_image_url'] URL of the filtered header image or false.
   * @param array          $args['header_args']      Arguments passed to the header.php partial.
   */

  function fictioneer_output_mobile_menu( $args ) {
    // Start HTML ---> ?>
    <input id="mobile-menu-toggle" type="checkbox" autocomplete="off" tabindex="-1" hidden>
    <div class="mobile-menu">
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
}
add_action( 'fictioneer_body', 'fictioneer_output_mobile_menu', 20 );

// =============================================================================
// MOBILE MENU USER HEADER MENU
// =============================================================================

if ( ! function_exists( 'fictioneer_mobile_user_icon_menu' ) ) {
  /**
   * Adds the _icon-menu.php partial to the mobile menu
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_mobile_user_icon_menu() {
    get_template_part( 'partials/_icon-menu', null, ['location' => 'in-mobile-menu'] );
  }
}
add_action( 'fictioneer_mobile_menu_top', 'fictioneer_mobile_user_icon_menu', 10 );

// =============================================================================
// MOBILE MENU QUICK BUTTONS
// =============================================================================

if ( ! function_exists( 'fictioneer_mobile_quick_buttons' ) ) {
  /**
   * Adds quick buttons to the mobile menu
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_mobile_quick_buttons() {
    // Setup
    $post_type = is_archive() ? 'archive' : get_post_type();
    $output = [];

    // Build
    ob_start();
    // Start HTML ---> ?>
    <label for="site-setting-minimal" class="button _quick"><span><?php _e( 'Minimalist', 'fictioneer' ); ?></span></label>
    <?php // <--- End HTML
    $output['minimalist'] = ob_get_clean();

    if ( $post_type === 'fcn_chapter' && ! is_search() ) {
      ob_start();
      // Start HTML ---> ?>
      <button class="button _quick button-change-lightness" value="-0.2"><?php _e( 'Darken', 'fictioneer' ) ;?></button>
      <?php // <--- End HTML
      $output['darken'] = ob_get_clean();

      ob_start();
      // Start HTML ---> ?>
      <button class="button _quick button-change-lightness" value="0.2"><?php _e( 'Brighten', 'fictioneer' ); ?></button>
      <?php // <--- End HTML
      $output['brighten'] = ob_get_clean();

      ob_start();
      // Start HTML ---> ?>
      <label for="reader-settings-justify-toggle" class="button _quick"><span><?php _e( 'Justify', 'fictioneer' ); ?></span></label>
      <?php // <--- End HTML
      $output['justify'] = ob_get_clean();

      ob_start();
      // Start HTML ---> ?>
      <label for="reader-settings-indent-toggle" class="button _quick"><span><?php _e( 'Indent', 'fictioneer' ); ?></span></label>
      <?php // <--- End HTML
      $output['indent'] = ob_get_clean();

      ob_start();
      // Start HTML ---> ?>
      <label for="modal-formatting-toggle" class="button _quick"><span><?php echo fcntr( 'formatting' ); ?></span></label>
      <?php // <--- End HTML
      $output['formatting'] = ob_get_clean();

      ob_start();
      // Start HTML ---> ?>
      <button class="button _quick button-change-font font-stepper" value="-1" id="quick-button-prev-font"><?php _e( 'Prev Font', 'fictioneer' ); ?></button>
      <?php // <--- End HTML
      $output['previous_font'] = ob_get_clean();

      ob_start();
      // Start HTML ---> ?>
      <button class="button _quick button-change-font font-stepper" value="1" id="quick-button-next-font"><?php _e( 'Next Font', 'fictioneer' ); ?></button>
      <?php // <--- End HTML
      $output['next_font'] = ob_get_clean();
    } else {
      ob_start();
      // Start HTML ---> ?>
      <label for="site-setting-covers" class="button _quick"><span><?php _e( 'Covers', 'fictioneer' ); ?></span></label>
      <?php // <--- End HTML
      $output['covers'] = ob_get_clean();

      ob_start();
      // Start HTML ---> ?>
      <label for="site-setting-background-textures" class="button _quick"><span><?php _e( 'Textures', 'fictioneer' ); ?></span></label>
      <?php // <--- End HTML
      $output['textures'] = ob_get_clean();

      ob_start();
      // Start HTML ---> ?>
      <label for="site-setting-polygons" class="button _quick"><span><?php _e( 'Polygons', 'fictioneer' ); ?></span></label>
      <?php // <--- End HTML
      $output['polygons'] = ob_get_clean();
    }

    // Apply filter
    $output = apply_filters( 'fictioneer_filter_mobile_quick_buttons', $output );

    // Return
    echo implode( '', $output );
  }
}
add_action( 'fictioneer_mobile_menu_bottom', 'fictioneer_mobile_quick_buttons', 10 );

// =============================================================================
// MOBILE MENU FOLLOWS FRAME
// =============================================================================

if ( ! function_exists( 'fictioneer_mobile_follows_frame' ) ) {
  /**
   * Adds the Follows frame to the mobile menu
   *
   * @since Fictioneer 5.0
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
}

if ( get_option( 'fictioneer_enable_follows' ) ) {
  add_action( 'fictioneer_mobile_menu_center', 'fictioneer_mobile_follows_frame', 10 );
}

// =============================================================================
// MOBILE MENU BOOKMARKS FRAME
// =============================================================================

if ( ! function_exists( 'fictioneer_mobile_bookmarks_frame' ) ) {
  /**
   * Adds the bookmarks frame to the mobile menu
   *
   * @since Fictioneer 5.0
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
}

if ( get_option( 'fictioneer_enable_bookmarks' ) ) {
  add_action( 'fictioneer_mobile_menu_center', 'fictioneer_mobile_bookmarks_frame', 20 );
}

// =============================================================================
// MOBILE MENU CHAPTERS FRAME
// =============================================================================

if ( ! function_exists( 'fictioneer_mobile_chapters_frame' ) ) {
  /**
   * Adds the chapters frame to the mobile menu
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_mobile_chapters_frame() {
    // Abort conditions
    if ( get_post_type() != 'fcn_chapter' || is_archive() || is_search() ) return;

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
}
add_action( 'fictioneer_mobile_menu_center', 'fictioneer_mobile_chapters_frame', 30 );

// =============================================================================
// MOBILE MENU NAVIGATION PANEL
// =============================================================================

if ( ! function_exists( 'fictioneer_mobile_navigation_panel' ) ) {
  /**
   * Adds the navigation panel to the mobile menu
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_mobile_navigation_panel() {
    // Start HTML ---> ?>
    <nav id="mobile-navigation" class="mobile-navigation mobile-menu__panel"><?php
      wp_nav_menu(
        array(
          'theme_location' => 'nav_menu',
          'menu_class' => 'mobile-navigation__list',
          'container' => ''
        )
      );
    ?></nav>
    <?php // <--- End HTML
  }
}
add_action( 'fictioneer_mobile_menu_main_frame_panels', 'fictioneer_mobile_navigation_panel', 10 );

// =============================================================================
// MOBILE MENU LISTS PANEL
// =============================================================================

if ( ! function_exists( 'fictioneer_mobile_lists_panel' ) ) {
  /**
   * Adds the lists panel to the mobile menu
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_mobile_lists_panel() {
    // Setup
    $post_type = is_archive() ? 'archive' : get_post_type();
    $output = [];

    // Chapters?
    if ( $post_type === 'fcn_chapter' && fictioneer_get_field( 'fictioneer_chapter_story' ) && ! is_search() ) {
      ob_start();
      // Start HTML ---> ?>
      <button class="mobile-menu__frame-button" data-frame-target="chapters">
        <i class="fa-solid fa-caret-right mobile-menu__item-icon"></i> <?php _e( 'Chapters', 'fictioneer' ); ?>
      </button>
      <?php // <--- End HTML
      $output['chapters'] = ob_get_clean();
    }

    // Bookmarks?
    if ( get_option( 'fictioneer_enable_bookmarks' ) ) {
      ob_start();
      // Start HTML ---> ?>
      <button class="mobile-menu__frame-button" data-frame-target="bookmarks">
        <i class="fa-solid fa-caret-right mobile-menu__item-icon"></i> <?php echo fcntr( 'bookmarks' ); ?>
      </button>
      <?php // <--- End HTML
      $output['bookmarks'] = ob_get_clean();
    }

    // Follows?
    if ( get_option( 'fictioneer_enable_follows' ) ) {
      ob_start();
      // Start HTML ---> ?>
      <button class="mobile-menu__frame-button hide-if-logged-out follows-alert-number" data-frame-target="follows">
        <i class="fa-solid fa-caret-right mobile-menu__item-icon"></i> <?php echo fcntr( 'follows' ); ?>
      </button>
      <?php // <--- End HTML
      $output['follows'] = ob_get_clean();
    }

    // Render (if content)
    if ( ! empty( $output ) ) {
      echo '<div class="mobile-menu__panel mobile-menu__lists-panel">' . implode( '', $output ) . '</div>';
    }
  }
}
add_action( 'fictioneer_mobile_menu_main_frame_panels', 'fictioneer_mobile_lists_panel', 20 );

// =============================================================================
// MOBILE MENU USER NAVIGATION
// =============================================================================

if ( ! function_exists( 'fictioneer_mobile_user_menu' ) ) {
  /**
   * Adds the user menu to the mobile menu
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_mobile_user_menu() {
    // Setup
    $post_type = is_archive() ? 'archive' : get_post_type();
    $profile_link = get_edit_profile_url();
    $profile_page = intval( get_option( 'fictioneer_user_profile_page', -1 ) );
    $discord_link = get_option( 'fictioneer_discord_invite_link' );
    $bookshelf_link = get_permalink( get_option( 'fictioneer_bookshelf_page' ) );
    $bookshelf_title = trim( get_the_title( get_option( 'fictioneer_bookshelf_page' ) ) );
    $bookmarks_link = get_permalink( get_option( 'fictioneer_bookmarks_page' ) );
    $can_checkmarks = get_option( 'fictioneer_enable_checkmarks' );
    $can_follows = get_option( 'fictioneer_enable_follows' );
    $can_reminders = get_option( 'fictioneer_enable_reminders' );
    $output = [];

    if ( $profile_page && $profile_page > 0 ) {
      $profile_link = get_permalink( $profile_page );
    }

    // Build
    if ( ! empty( $profile_link ) && fictioneer_show_auth_content() ) {
      ob_start();
      // Start HTML ---> ?>
      <a href="<?php echo esc_url( $profile_link ); ?>" class="hide-if-logged-out">
        <i class="fa-solid fa-circle-user mobile-menu__item-icon"></i>
        <?php echo fcntr( 'account' ); ?>
      </a>
      <?php // <--- End HTML
      $output['account'] = ob_get_clean();
    }

    if ( FICTIONEER_SHOW_SEARCH_IN_MENUS ) {
      ob_start();
      // Start HTML ---> ?>
      <a href="<?php echo esc_url( home_url( '/?s=' ) ); ?>">
        <i class="fa-solid fa-magnifying-glass mobile-menu__item-icon"></i>
        <?php _e( 'Search', 'fictioneer' ); ?>
      </a>
      <?php // <--- End HTML
      $output['search'] = ob_get_clean();
    }

    ob_start();
    // Start HTML ---> ?>
    <label for="modal-site-settings-toggle">
      <i class="fa-solid fa-tools mobile-menu__item-icon"></i>
      <?php echo fcntr( 'site_settings' ); ?>
    </label>
    <?php // <--- End HTML
    $output['site_settings'] = ob_get_clean();

    if ( ! empty( $discord_link ) ) {
      ob_start();
      // Start HTML ---> ?>
      <a href="<?php echo esc_url( $discord_link ); ?>" rel="noopener noreferrer nofollow">
        <i class="fa-brands fa-discord mobile-menu__item-icon"></i>
        <?php _e( 'Discord', 'fictioneer' ) ?>
      </a>
      <?php // <--- End HTML
      $output['discord'] = ob_get_clean();
    }

    if ( $bookshelf_link && fictioneer_show_auth_content() && ( $can_checkmarks || $can_follows || $can_reminders ) ) {
      ob_start();
      // Start HTML ---> ?>
      <a href="<?php echo esc_url( $bookshelf_link ); ?>" rel="noopener noreferrer nofollow" class="hide-if-logged-out">
        <i class="fa-solid fa-list mobile-menu__item-icon"></i>
        <?php echo empty( $bookshelf_title ) ? __( 'Bookshelf', 'fictioneer' ) : $bookshelf_title; ?>
      </a>
      <?php // <--- End HTML
      $output['bookshelf'] = ob_get_clean();
    }

    if ( ! empty( $bookmarks_link ) && get_option( 'fictioneer_enable_bookmarks' ) ) {
      ob_start();
      // Start HTML ---> ?>
      <a href="<?php echo esc_url( $bookmarks_link ); ?>" rel="noopener noreferrer nofollow">
        <i class="fa-solid fa-bookmark mobile-menu__item-icon"></i>
        <?php echo fcntr( 'bookmarks' ); ?>
      </a>
      <?php // <--- End HTML
      $output['bookmarks'] = ob_get_clean();
    }

    if ( $post_type === 'fcn_chapter' && ! is_search() ) {
      ob_start();
      // Start HTML ---> ?>
      <label for="modal-formatting-toggle">
        <?php fictioneer_icon( 'font-settings', 'mobile-menu__item-icon' ); ?>
        <?php echo fcntr( 'formatting' ); ?>
      </label>
      <?php // <--- End HTML
      $output['formatting'] = ob_get_clean();
    }

    if (
      $post_type === 'fcn_chapter' &&
      ! is_search() &&
      comments_open() &&
      ! fictioneer_is_commenting_disabled() &&
      ! post_password_required()
    ) {
      ob_start();
      // Start HTML ---> ?>
      <a id="mobile-menu-comment-jump" class="comments-toggle" rel="noopener noreferrer nofollow">
        <i class="fa-solid fa-comments mobile-menu__item-icon"></i>
        <?php echo fcntr( 'jump_to_comments' ); ?>
      </a>
      <?php // <--- End HTML
      $output['comment_jump'] = ob_get_clean();
    }

    if (
      $post_type === 'fcn_chapter' &&
      ! is_search() &&
      get_option( 'fictioneer_enable_bookmarks' ) &&
      ! post_password_required()
    ) {
      ob_start();
      // Start HTML ---> ?>
      <a id="mobile-menu-bookmark-jump" rel="noopener noreferrer nofollow" hidden>
        <i class="fa-solid fa-bookmark mobile-menu__item-icon"></i>
        <?php echo fcntr( 'jump_to_bookmark' ); ?>
      </a>
      <?php // <--- End HTML
      $output['bookmark_jump'] = ob_get_clean();
    }

    if ( fictioneer_show_auth_content() ) {
      ob_start();
      // Start HTML ---> ?>
      <a href="<?php echo fictioneer_get_logout_url(); ?>" data-click="logout" rel="noopener noreferrer nofollow" class="hide-if-logged-out">
        <?php fictioneer_icon( 'fa-logout', 'mobile-menu__item-icon', '', 'style="transform: translateY(-1px);"' ); ?>
        <?php echo fcntr( 'logout' ); ?>
      </a>
      <?php // <--- End HTML
      $output['logout'] = ob_get_clean();
    }

    if ( get_option( 'fictioneer_enable_oauth' ) && ! is_user_logged_in() ) {
      ob_start();
      // Start HTML ---> ?>
      <label for="modal-login-toggle" class="hide-if-logged-in subscriber-login">
        <?php fictioneer_icon( 'fa-login', 'mobile-menu__item-icon' ); ?>
        <?php echo fcntr( 'login' ); ?>
      </label>
      <?php // <--- End HTML
      $output['login'] = ob_get_clean();
    }

    // Apply filter
    $output = apply_filters( 'fictioneer_filter_mobile_user_menu_items', $output );

    // Return
    echo '<div id="mobile-menu-user-panel" class="mobile-menu__panel">' . implode( '', $output ) . '</div>';
  }
}
add_action( 'fictioneer_mobile_menu_main_frame_panels', 'fictioneer_mobile_user_menu', 30 );

?>
