<?php

// =============================================================================
// BREADCRUMBS
// =============================================================================

if ( ! function_exists( 'fictioneer_breadcrumbs' ) ) {
  /**
   * Outputs the HTML for the breadcrumbs
   *
   * @since Fictioneer 5.0
   *
   * @param array  $args['breadcrumbs'] Breadcrumb tuples with label (0) and link (1).
   * @param string $args['post_type']   Optional. Post type of the current page.
   * @param int    $args['post_id']     Optional. Post ID of the current page.
   */

  function fictioneer_breadcrumbs( $args ) {
    echo fictioneer_get_breadcrumbs( $args );
  }
}
add_action( 'fictioneer_site_footer', 'fictioneer_breadcrumbs', 10 );

// =============================================================================
// FOOTER MENU ROW
// =============================================================================

if ( ! function_exists( 'fictioneer_footer_menu_row' ) ) {
  /**
   * Outputs the HTML for the breadcrumbs
   *
   * @since Fictioneer 5.0
   *
   * @param array  $args['breadcrumbs'] Breadcrumb tuples with label (0) and link (1).
   * @param string $args['post_type']   Optional. Post type of the current page.
   * @param int    $args['post_id']     Optional. Post ID of the current page.
   */

  function fictioneer_footer_menu_row( $args ) {
    // Start HTML ---> ?>
    <div class="footer__split-row">
      <div class="footer__menu"><?php
        wp_nav_menu(
          array(
            'theme_location' => 'footer_menu',
            'menu_class' => 'footer__menu-list dot-separator',
            'container' => ''
          )
        );
      ?></div>
      <div class="footer__copyright"><?php echo fictioneer_get_footer_copyright_note( $args ); ?></div>
    </div>
    <?php // <--- End HTML
  }
}
add_action( 'fictioneer_site_footer', 'fictioneer_footer_menu_row', 20 );

// =============================================================================
// OUTPUT MODALS
// =============================================================================

if ( ! function_exists( 'fictioneer_output_modals' ) ) {
  /**
   * Outputs the HTML for the modals
   *
   * @since Fictioneer 5.0
   *
   * @param int|null       $args['post_id']          Current post ID or null.
   * @param int|null       $args['story_id']         Current story ID (if chapter) or null.
   * @param string|boolean $args['header_image_url'] URL of the filtered header image or false.
   * @param array          $args['header_args']      Arguments passed to the header.php partial.
   */

  function fictioneer_output_modals( $args ) {
    // Formatting and suggestions
    if ( get_post_type() == 'fcn_chapter' ) {
      ?><input id="modal-formatting-toggle" type="checkbox" tabindex="-1" class="modal-toggle" hidden><?php
      get_template_part( 'partials/_modal-formatting' );

      if (
        get_option( 'fictioneer_enable_suggestions' ) &&
        ! fictioneer_get_field( 'fictioneer_disable_commenting' ) &&
        comments_open()
      ) {
        ?><input id="modal-suggestions-toggle" type="checkbox" tabindex="-1" class="modal-toggle" hidden><?php
        get_template_part( 'partials/_modal-suggestions' );
      }
    }

    // OAuth2 login
    if ( get_option( 'fictioneer_enable_oauth' ) ) {
      ?><input id="modal-login-toggle" type="checkbox" tabindex="-1" class="modal-toggle" hidden><?php
      get_template_part( 'partials/_modal-login' );
    }

    // Social sharing
    ?><input id="modal-sharing-toggle" type="checkbox" tabindex="-1" class="modal-toggle" hidden><?php
    get_template_part( 'partials/_modal-sharing' );

    // Site settings
    ?><input id="modal-site-settings-toggle" type="checkbox" tabindex="-1" class="modal-toggle" hidden><?php
    get_template_part( 'partials/_modal-site-settings' );

    // BBCodes tutorial
    if ( ! post_password_required() && comments_open() && ! fictioneer_is_commenting_disabled() ) {
      ?><input id="modal-bbcodes-toggle" type="checkbox" tabindex="-1" class="modal-toggle" hidden><?php
      get_template_part( 'partials/_modal-bbcodes' );
    }

    // Action to add modals
    do_action( 'fictioneer_modals' );
  }
}
add_action( 'fictioneer_body', 'fictioneer_output_modals', 10 );

// =============================================================================
// OUTPUT NAVIGATION BAR
// =============================================================================

if ( ! function_exists( 'fictioneer_navigation_bar' ) ) {
  /**
   * Outputs the HTML for the navigation bar
   *
   * @since Fictioneer 5.0
   *
   * @param int|null       $args['post_id']          Current post ID or null.
   * @param int|null       $args['story_id']         Current story ID (if chapter) or null.
   * @param string|boolean $args['header_image_url'] URL of the filtered header image or false.
   * @param array          $args['header_args']      Arguments passed to the header.php partial.
   */

  function fictioneer_navigation_bar( $args ) {
    get_template_part( 'partials/_navigation', null, $args );
  }
}
add_action( 'fictioneer_site', 'fictioneer_navigation_bar', 10 );

// =============================================================================
// OUTPUT SITE HEADER
// =============================================================================

if ( ! function_exists( 'fictioneer_site_header' ) ) {
  /**
   * Outputs the HTML for the site header
   *
   * @since Fictioneer 5.0
   *
   * @param int|null       $args['post_id']          Current post ID or null.
   * @param int|null       $args['story_id']         Current story ID (if chapter) or null.
   * @param string|boolean $args['header_image_url'] URL of the filtered header image or false.
   * @param array          $args['header_args']      Arguments passed to the header.php partial.
   */

  function fictioneer_site_header( $args ) {
    get_template_part( 'partials/_site-header', null, $args );
  }
}
add_action( 'fictioneer_site', 'fictioneer_site_header', 20 );


// =============================================================================
// OUTPUT HEADER BACKGROUND
// =============================================================================

if ( ! function_exists( 'fictioneer_header_background' ) ) {
  /**
   * Outputs the HTML for the header background
   *
   * @since Fictioneer 5.0
   *
   * @param int|null       $args['post_id']          Current post ID or null.
   * @param int|null       $args['story_id']         Current story ID (if chapter) or null.
   * @param string|boolean $args['header_image_url'] URL of the filtered header image or false.
   * @param array          $args['header_args']      Arguments passed to the header.php partial.
   */

  function fictioneer_header_background( $args ) {
    // Abort if...
    if ( ! isset( $args['header_image_url'] ) || ! $args['header_image_url'] ) return;

    // Start HTML ---> ?>
    <div class="header-background hide-on-fullscreen">
      <div class="header-background__wrapper">
        <img src="<?php echo $args['header_image_url']; ?>" alt="Header Background Image" class="header-background__image">
      </div>
    </div>
    <?php // <--- End HTML
  }
}
add_action( 'fictioneer_header', 'fictioneer_header_background', 10 );

?>
