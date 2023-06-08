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
      ?><input id="modal-formatting-toggle" type="checkbox" tabindex="-1" class="modal-toggle" autocomplete="off" hidden><?php
      get_template_part( 'partials/_modal-formatting' );

      if (
        get_option( 'fictioneer_enable_suggestions' ) &&
        ! fictioneer_get_field( 'fictioneer_disable_commenting' ) &&
        comments_open()
      ) {
        ?><input id="modal-suggestions-toggle" type="checkbox" tabindex="-1" class="modal-toggle" autocomplete="off" hidden><?php
        get_template_part( 'partials/_modal-suggestions' );
      }
    }

    // OAuth2 login
    if ( get_option( 'fictioneer_enable_oauth' ) && ! is_user_logged_in() ) {
      ?><input id="modal-login-toggle" type="checkbox" tabindex="-1" class="modal-toggle" autocomplete="off" hidden><?php
      get_template_part( 'partials/_modal-login' );
    }

    // Social sharing
    ?><input id="modal-sharing-toggle" type="checkbox" tabindex="-1" class="modal-toggle" autocomplete="off" hidden><?php
    get_template_part( 'partials/_modal-sharing' );

    // Site settings
    ?><input id="modal-site-settings-toggle" type="checkbox" tabindex="-1" class="modal-toggle" autocomplete="off" hidden><?php
    get_template_part( 'partials/_modal-site-settings' );

    // BBCodes tutorial
    if ( ! post_password_required() && comments_open() && ! fictioneer_is_commenting_disabled() ) {
      ?><input id="modal-bbcodes-toggle" type="checkbox" tabindex="-1" class="modal-toggle" autocomplete="off" hidden><?php
      get_template_part( 'partials/_modal-bbcodes' );
    }

    // Action to add modals
    do_action( 'fictioneer_modals' );
  }
}
add_action( 'wp_footer', 'fictioneer_output_modals' );

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

// =============================================================================
// SORT, ORDER & FILTER QUERIES
// =============================================================================

/**
 * Renders interface to sort, order, and filter queries
 *
 * @since Fictioneer 5.4.0
 *
 * @param array $args {
 *   Array of arguments.
 *
 *   @type int        $current_page  Current page if paginated or `1`.
 *   @type int        $post_id       Current post ID.
 *   @type string     $queried_type  Queried post type.
 *   @type array      $query_args    Query arguments used.
 *   @type string     $order         Current order or `desc`.
 *   @type string     $orderby       Current orderby or `'modified'`.
 *   @type int|string $ago           Current date query argument part or `0`.
 * }
 */

function fictioneer_sort_order_filter_interface( $args ) {
  // Setup
  $current_url = get_permalink();

  // Order?
  if ( ! empty( $args['order'] ) ) {
    $current_url = add_query_arg(
      array( 'order' => $args['order'] ),
      $current_url
    );
  }

  // Orderby?
  if ( ! empty( $args['orderby'] ) ) {
    $current_url = add_query_arg(
      array( 'orderby' => $args['orderby'] ),
      $current_url
    );
  }

  // Ago?
  if ( ! empty( $args['ago'] ) ) {
    $current_url = add_query_arg(
      array( 'ago' => $args['ago'] ),
      $current_url
    );
  }

  // Order menu options
  $orderby_menu = array(
    'modified' => array(
      'label' => __( 'Updated', 'fictioneer' ),
      'url' => add_query_arg( array( 'orderby' => 'modified' ), $current_url ) . '#sof'
    ),
    'date' => array(
      'label' => __( 'Published', 'fictioneer' ),
      'url' => add_query_arg( array( 'orderby' => 'date' ), $current_url ) . '#sof'
    ),
    'title' => array(
      'label' => __( 'Title', 'fictioneer' ),
      'url' => add_query_arg( array( 'orderby' => 'title' ), $current_url ) . '#sof'
    )
  );

  // Date menu options
  $date_menu = array(
    '0' => array(
      'label' => __( 'Any Date', 'fictioneer' ),
      'url' => remove_query_arg( 'ago', $current_url ) . '#sof'
    ),
    '1' => array(
      'label' => __( 'Last Day', 'fictioneer' ),
      'url' => add_query_arg( array( 'ago' => 1 ), $current_url ) . '#sof'
    ),
    '3' => array(
      'label' => __( 'Last 3 Days', 'fictioneer' ),
      'url' => add_query_arg( array( 'ago' => 3 ), $current_url ) . '#sof'
    ),
    'week' => array(
      'label' => __( 'Last Week', 'fictioneer' ),
      'url' => add_query_arg( array( 'ago' => 'week' ), $current_url ) . '#sof'
    ),
    'month' => array(
      'label' => __( 'Last Month', 'fictioneer' ),
      'url' => add_query_arg( array( 'ago' => 'month' ), $current_url ) . '#sof'
    ),
    'year' => array(
      'label' => __( 'Last Year', 'fictioneer' ),
      'url' => add_query_arg( array( 'ago' => 'year' ), $current_url ) . '#sof'
    )
  );

  // Order toggle link
  $order_link = esc_url(
    add_query_arg(
      array( 'order' => $args['order'] === 'desc' ? 'asc' : 'desc' ),
      $current_url
    ) . '#sof'
  );

  // Apply filters
  $orderby_menu = apply_filters( 'fictioneer_filter_orderby_popup_menu', $orderby_menu, $args );

  // Start HTML ---> ?>
  <div id="sof" class="sort-order-filter">

    <div class="list-button _text popup-menu-toggle toggle-last-clicked" tabindex="0" role="button"><?php
      echo $orderby_menu[ $args['orderby'] ]['label'] ?? __( 'Custom', 'fictioneer' );
      echo '<div class="popup-menu _bottom _center">';
      echo '<div class="popup-heading">' . __( 'Order By', 'fictioneer' ) . '</div>';

      foreach( $orderby_menu as $tuple ) {
        $url = esc_url( $tuple['url'] );
        echo "<a href='{$url}'>{$tuple['label']}</a>";
      }

      echo '</div>';
    ?></div>

    <div class="list-button _text popup-menu-toggle toggle-last-clicked" tabindex="0" role="button"><?php
      echo $date_menu[ $args['ago'] ]['label'] ?? __( 'Custom', 'fictioneer' );
      echo '<div class="popup-menu _bottom _center">';
      echo '<div class="popup-heading">' . __( 'Time Range', 'fictioneer' ) . '</div>';

      foreach( $date_menu as $tuple ) {
        $url = esc_url( $tuple['url'] );
        echo "<a href='{$url}'>{$tuple['label']}</a>";
      }

      echo '</div>';
    ?></div>

    <a class="list-button _order  <?php echo $args['order'] === 'desc' ? '_on' : '_off'; ?>" href="<?php echo $order_link; ?>">
      <i class="fa-solid fa-arrow-up-short-wide _off"></i><i class="fa-solid fa-arrow-down-wide-short _on"></i>
    </a>

  </div>
  <?php // <--- End HTML
}
add_action( 'fictioneer_stories_after_content', 'fictioneer_sort_order_filter_interface', 20 );
add_action( 'fictioneer_chapters_after_content', 'fictioneer_sort_order_filter_interface', 20 );
add_action( 'fictioneer_collections_after_content', 'fictioneer_sort_order_filter_interface', 20 );
add_action( 'fictioneer_recommendations_after_content', 'fictioneer_sort_order_filter_interface', 20 );

?>
