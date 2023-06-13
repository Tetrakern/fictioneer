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
        $menu = wp_nav_menu(
          array(
            'theme_location' => 'footer_menu',
            'menu_class' => 'footer__menu-list',
            'container' => '',
            'echo' => false
          )
        );

        $menu = str_replace( 'class="', 'class="footer__menu-list-item ', $menu );
        $menu = preg_replace( '/<\/li>\s*<li/', '</li><li', $menu );

        echo $menu;
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

if ( ! function_exists( 'fictioneer_sort_order_filter_interface' ) ) {
  /**
   * Renders interface to sort, order, and filter queries
   *
   * @since Fictioneer 5.4.0
   *
   * @param array $args {
   *   Array of arguments.
   *
   *   @type int        $current_page  Optional. Current page if paginated or `1`.
   *   @type int        $post_id       Optional. Current post ID.
   *   @type string     $queried_type  Optional. Queried post type.
   *   @type array      $query_args    Optional. Query arguments used.
   *   @type string     $order         Current order or `desc`.
   *   @type string     $orderby       Current orderby or `'modified'`.
   *   @type int|string $ago           Current date query argument part or `0`.
   * }
   */

  function fictioneer_sort_order_filter_interface( $args ) {
    // Setup
    $current_url = fictioneer_get_clean_url();
    $post_type = null;

    // Archive?
    if ( is_archive() ) {
      $post_type = strtolower( sanitize_text_field( $_GET['post_type'] ?? '' ) );
      $post_type = array_intersect(
        [ $post_type ],
        ['any', 'post', 'fcn_story', 'fcn_chapter', 'fcn_collection', 'fcn_recommendation']
      );
      $post_type = reset( $post_type ) ?: null;
    }

    // Post type?
    if ( ! empty( $post_type ) ) {
      $current_url = add_query_arg(
        array( 'post_type' => $post_type ),
        $current_url
      );
    }

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
      // Validate
      if ( ! is_numeric( $args['ago'] ) && strtotime( $args['ago'] ) === false ) {
        $args['ago'] = '0';
      }

      // Add if valid
      if ( $args['ago'] != '0' ) {
        $current_url = add_query_arg(
          array( 'ago' => $args['ago'] ),
          $current_url
        );
      }
    }

    // Post type menu options
    $post_type_menu = array(
      'any' => array(
        'label' => __( 'All Posts', 'fictioneer' ),
        'url' => add_query_arg( array( 'post_type' => 'any' ), $current_url ) . '#sof'
      ),
      'post' => array(
        'label' => __( 'Blog Posts', 'fictioneer' ),
        'url' => add_query_arg( array( 'post_type' => 'post' ), $current_url ) . '#sof'
      ),
      'fcn_story' => array(
        'label' => __( 'Stories', 'fictioneer' ),
        'url' => add_query_arg( array( 'post_type' => 'fcn_story' ), $current_url ) . '#sof'
      ),
      'fcn_chapter' => array(
        'label' => __( 'Chapters', 'fictioneer' ),
        'url' => add_query_arg( array( 'post_type' => 'fcn_chapter' ), $current_url ) . '#sof'
      ),
      'fcn_collection' => array(
        'label' => __( 'Collections', 'fictioneer' ),
        'url' => add_query_arg( array( 'post_type' => 'fcn_collection' ), $current_url ) . '#sof'
      ),
      'fcn_recommendation' => array(
        'label' => __( 'Recommendations', 'fictioneer' ),
        'url' => add_query_arg( array( 'post_type' => 'fcn_recommendation' ), $current_url ) . '#sof'
      )
    );

    // Filter post type options
    $post_type_menu = apply_filters( 'fictioneer_filter_sof_post_type_options', $post_type_menu, $current_url, $args );

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
        'label' => __( 'By Title', 'fictioneer' ),
        'url' => add_query_arg( array( 'orderby' => 'title' ), $current_url ) . '#sof'
      )
    );

    // Filter orderby options
    $orderby_menu = apply_filters( 'fictioneer_filter_sof_orderby_options', $orderby_menu, $current_url, $args );

    // Date menu options
    $date_menu = array(
      '0' => array(
        'label' => __( 'Any Date', 'fictioneer' ),
        'url' => remove_query_arg( 'ago', $current_url ) . '#sof'
      ),
      '1' => array(
        'label' => __( 'Past 24 Hours', 'fictioneer' ),
        'url' => add_query_arg( array( 'ago' => 1 ), $current_url ) . '#sof'
      ),
      '3' => array(
        'label' => __( 'Past 3 Days', 'fictioneer' ),
        'url' => add_query_arg( array( 'ago' => 3 ), $current_url ) . '#sof'
      ),
      '1_week_ago' => array(
        'label' => __( 'Past Week', 'fictioneer' ),
        'url' => add_query_arg( array( 'ago' => urlencode( '1 week ago' ) ), $current_url ) . '#sof'
      ),
      '1_month_ago' => array(
        'label' => __( 'Past Month', 'fictioneer' ),
        'url' => add_query_arg( array( 'ago' => urlencode( '1 month ago' ) ), $current_url ) . '#sof'
      ),
      '3_months_ago' => array(
        'label' => __( 'Past 3 Months', 'fictioneer' ),
        'url' => add_query_arg( array( 'ago' => urlencode( '3 months ago' ) ), $current_url ) . '#sof'
      ),
      '6_months_ago' => array(
        'label' => __( 'Past 6 Months', 'fictioneer' ),
        'url' => add_query_arg( array( 'ago' => urlencode( '6 months ago' ) ), $current_url ) . '#sof'
      ),
      '1_year_ago' => array(
        'label' => __( 'Past Year', 'fictioneer' ),
        'url' => add_query_arg( array( 'ago' => urlencode( '1 year ago' ) ), $current_url ) . '#sof'
      )
    );

    // Filter date options
    $date_menu = apply_filters( 'fictioneer_filter_sof_date_options', $date_menu, $current_url, $args );

    // Order toggle link
    $order_link = esc_url(
      add_query_arg(
        array( 'order' => $args['order'] === 'desc' ? 'asc' : 'desc' ),
        $current_url
      ) . '#sof'
    );

    // Start HTML ---> ?>
    <div id="sof" class="sort-order-filter">

      <?php if ( is_archive() && ! empty( $post_type_menu ) ) : ?>
        <div class="list-button _text popup-menu-toggle toggle-last-clicked" tabindex="0" role="button"><?php
          echo $post_type_menu[ $post_type ?? 'any' ]['label'] ?? __( 'Unknown', 'fictioneer' );
          echo '<div class="popup-menu _bottom _center">';
          echo '<div class="popup-heading">' . __( 'Post Type', 'fictioneer' ) . '</div>';

          foreach( $post_type_menu as $tuple ) {
            $url = esc_url( $tuple['url'] );
            echo "<a href='{$url}'>{$tuple['label']}</a>";
          }

          echo '</div>';
        ?></div>
      <?php endif; ?>

      <?php if ( ! empty( $orderby_menu ) ) : ?>
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
      <?php endif; ?>

      <?php if ( ! empty( $date_menu ) ) : ?>
        <div class="list-button _text popup-menu-toggle toggle-last-clicked" tabindex="0" role="button"><?php
          $key = str_replace( ' ', '_', $args['ago'] ?? '' );

          if ( empty( $date_menu[ $key ]['label'] ) ) {
            echo is_numeric( $args['ago'] ) ? sprintf(
              __( 'Past %s Days', 'fictioneer' ), $args['ago']
            ) : urldecode( $args['ago'] );
          } else {
            echo $date_menu[ $key ]['label'];
          }

          echo '<div class="popup-menu _bottom _center">';
          echo '<div class="popup-heading">' . __( 'Time Range', 'fictioneer' ) . '</div>';

          foreach( $date_menu as $tuple ) {
            $url = esc_url( $tuple['url'] );
            echo "<a href='{$url}'>{$tuple['label']}</a>";
          }

          echo '</div>';
        ?></div>
      <?php endif; ?>

      <a class="list-button _order  <?php echo $args['order'] === 'desc' ? '_on' : '_off'; ?>" href="<?php echo $order_link; ?>" aria-label="<?php esc_attr_e( 'Toggle between ascending and descending order', 'fictioneer' ); ?>">
        <i class="fa-solid fa-arrow-up-short-wide _off"></i><i class="fa-solid fa-arrow-down-wide-short _on"></i>
      </a>

    </div>
    <?php // <--- End HTML
  }
}
add_action( 'fictioneer_stories_after_content', 'fictioneer_sort_order_filter_interface', 20 );
add_action( 'fictioneer_chapters_after_content', 'fictioneer_sort_order_filter_interface', 20 );
add_action( 'fictioneer_collections_after_content', 'fictioneer_sort_order_filter_interface', 20 );
add_action( 'fictioneer_recommendations_after_content', 'fictioneer_sort_order_filter_interface', 20 );
add_action( 'fictioneer_archive_loop_before', 'fictioneer_sort_order_filter_interface', 10 );

?>
