<?php

// =============================================================================
// CUSTOM CSS
// =============================================================================

/**
   * Outputs custom CSS for stories/chapters
   *
   * @since 5.4.7
   */

function fictioneer_add_fiction_css() {
  /*
   * Most post types and pages can add individual custom CSS, which is
   * included here. This can hypothetically be used to completely change
   * the style but requires in-depth knowledge of the theme. Validated
   * using the same method as in WP_Customize_Custom_CSS_Setting(). And
   * removes any "<" for good measure since CSS does not use it.
   */

  $post_id = get_the_ID();
  $custom_css = '';
  $custom_css .= get_post_meta( $post_id, 'fictioneer_custom_css', true );

  if ( get_post_type( $post_id ) == 'fcn_story' ) {
    $custom_css .= get_post_meta( $post_id, 'fictioneer_story_css', true );
  }

  if ( get_post_type( $post_id ) == 'fcn_chapter' ) {
    $story_id = get_post_meta( $post_id, 'fictioneer_chapter_story', true );

    if ( ! empty( $story_id ) ) {
      $custom_css .= get_post_meta( $story_id, 'fictioneer_story_css', true );
    }
  }

  if ( ! empty( $custom_css ) && ! preg_match( '#</?\w+#', $custom_css ) ) {
    echo '<style id="fictioneer-custom-styles">' . str_replace( '<', '', $custom_css ). '</style>';
  }
}
add_action( 'wp_head', 'fictioneer_add_fiction_css', 10 );

// =============================================================================
// BREADCRUMBS
// =============================================================================

/**
 * Outputs the HTML for the breadcrumbs
 *
 * @since 5.0.0
 *
 * @param int|null    $args['post_id']      Optional. Post ID of the current page.
 * @param string|null $args['post_type']    Optional. Post type of the current page.
 * @param array       $args['breadcrumbs']  Breadcrumb tuples with label (0) and link (1).
 */

function fictioneer_breadcrumbs( $args ) {
  echo fictioneer_get_breadcrumbs( $args );
}
add_action( 'fictioneer_site_footer', 'fictioneer_breadcrumbs', 10 );

// =============================================================================
// FOOTER MENU ROW
// =============================================================================

/**
 * Outputs the HTML for the footer menu and theme copyright notice
 *
 * @since 5.0.0
 *
 * @param int|null    $args['post_id']      Optional. Post ID of the current page.
 * @param string|null $args['post_type']    Optional. Post type of the current page.
 * @param array       $args['breadcrumbs']  Breadcrumb tuples with label (0) and link (1).
 */

function fictioneer_footer_menu_row( $args ) {
  // Start HTML ---> ?>
  <div class="footer__split-row">
    <div class="footer__menu"><?php
      if ( has_nav_menu( 'footer_menu' ) ) {
        $menu = null;

        if ( FICTIONEER_ENABLE_MENU_TRANSIENTS ) {
          $menu = get_transient( 'fictioneer_footer_menu_html' );
        }

        if ( empty( $menu ) ) {
          $menu = wp_nav_menu(
            array(
              'theme_location' => 'footer_menu',
              'menu_class' => 'footer__menu-list',
              'container' => '',
              'echo' => false
            )
          );

          if ( $menu !== false ) {
            $menu = str_replace( 'class="', 'class="footer__menu-list-item ', $menu );
            $menu = str_replace( 'current_page_item', '', $menu );
            $menu = str_replace( 'current-menu-item', '', $menu );
            $menu = str_replace( 'aria-current="page"', '', $menu );
            $menu = preg_replace( '/<\/li>\s*<li/', '</li><li', $menu );
          }

          if ( FICTIONEER_ENABLE_MENU_TRANSIENTS ) {
            set_transient( 'fictioneer_footer_menu_html', $menu );
          }
        }

        echo $menu;
      }
    ?></div>
    <div class="footer__copyright"><?php echo fictioneer_get_footer_copyright_note( $args ); ?></div>
  </div>
  <?php // <--- End HTML
}
add_action( 'fictioneer_site_footer', 'fictioneer_footer_menu_row', 20 );

// =============================================================================
// OUTPUT MODALS
// =============================================================================

/**
 * Outputs the HTML for the modals
 *
 * @since 5.0.0
 *
 * @param int|null    $args['post_id']      Optional. Current post ID.
 * @param string|null $args['post_type']    Optional. Current post type.
 * @param array       $args['breadcrumbs']  Array of breadcrumb tuples with label (0) and link (1).
 */

function fictioneer_output_modals( $args ) {
  $is_archive = is_search() || is_archive() || empty( $args['post_id'] );

  // Formatting and suggestions
  if ( ! $is_archive && $args['post_type'] == 'fcn_chapter' ) {
    ?><input id="modal-formatting-toggle" data-target="formatting-modal" type="checkbox" tabindex="-1" class="modal-toggle" autocomplete="off" hidden><?php
    get_template_part( 'partials/_modal-formatting' );

    ?><input id="modal-tts-settings-toggle" data-target="tts-settings-modal" type="checkbox" tabindex="-1" class="modal-toggle" autocomplete="off" hidden><?php
    get_template_part( 'partials/_modal-tts-settings' );

    if (
      get_option( 'fictioneer_enable_suggestions' ) &&
      ! get_post_meta( get_the_ID(), 'fictioneer_disable_commenting', true ) &&
      comments_open()
    ) {
      ?><input id="suggestions-modal-toggle" data-target="suggestions-modal" type="checkbox" tabindex="-1" class="modal-toggle" autocomplete="off" hidden><?php
      get_template_part( 'partials/_modal-suggestions' );
    }
  }

  // OAuth2 login
  if ( get_option( 'fictioneer_enable_oauth' ) && ! is_user_logged_in() ) {
    ?><input id="modal-login-toggle" data-target="login-modal" type="checkbox" tabindex="-1" class="modal-toggle" autocomplete="off" hidden><?php
    get_template_part( 'partials/_modal-login' );
  }

  // Social sharing
  ?><input id="modal-sharing-toggle" data-target="sharing-modal" type="checkbox" tabindex="-1" class="modal-toggle" autocomplete="off" hidden><?php
  get_template_part( 'partials/_modal-sharing' );

  // Site settings
  ?><input id="modal-site-settings-toggle" data-target="site-settings-modal" type="checkbox" tabindex="-1" class="modal-toggle" autocomplete="off" hidden><?php
  get_template_part( 'partials/_modal-site-settings' );

  // BBCodes tutorial
  if (
    ! empty( $args['post_id'] ) &&
    ! post_password_required() &&
    comments_open() &&
    ! fictioneer_is_commenting_disabled( $args['post_id'] )
  ) {
    ?><input id="modal-bbcodes-toggle" data-target="bbcodes-modal" type="checkbox" tabindex="-1" class="modal-toggle" autocomplete="off" hidden><?php
    get_template_part( 'partials/_modal-bbcodes' );
  }

  // Story chapter changelog
  if (
    ! $is_archive &&
    $args['post_type'] == 'fcn_story' &&
    FICTIONEER_ENABLE_STORY_CHANGELOG &&
    get_option( 'fictioneer_show_story_changelog' )
  ) {
    ?><input id="modal-chapter-changelog-toggle" data-target="chapter-changelog-modal" type="checkbox" tabindex="-1" class="modal-toggle" autocomplete="off" hidden><?php
    get_template_part( 'partials/_modal-chapter-changelog' );
  }

  // Age confirmation modal
  if (
    get_option( 'fictioneer_enable_site_age_confirmation' ) ||
    (
      ! $is_archive &&
      get_option( 'fictioneer_enable_post_age_confirmation' ) &&
      in_array( $args['post_type'], ['fcn_story', 'fcn_chapter'] )
    )
  ) {
    if ( ! current_user_can( 'edit_fcn_stories' ) ) {
      get_template_part( 'partials/_modal-age', null, $args );
    }
  }

  // Action to add modals
  do_action( 'fictioneer_modals' );
}
add_action( 'fictioneer_footer', 'fictioneer_output_modals' );

// =============================================================================
// OUTPUT BROWSER NOTES
// =============================================================================

/**
 * Outputs the HTML for the browser notes
 *
 * @since 5.9.1
 */

function fictioneer_browser_notes() {
  $notes = [];

  // Catch IE and other garbage!
  $notes['garbage'] = __( 'Your browser is obsolete, you need to update or switch!', 'fictioneer' );

  // Support for custom properties?
  $notes['var'] = __( 'Missing var() support!', 'fictioneer' );

  // Support for grid?
  $notes['grid'] = __( 'Missing grid support!', 'fictioneer' );

  // Support for gap?
  $notes['gap'] = __( 'Missing (flex-) gap support!', 'fictioneer' );

  // Support for aspect-ratio?
  $notes['aspect-ratio'] = __( 'Missing aspect-ratio support!', 'fictioneer' );

  // Support for container queries?
  $notes['container-queries'] = __( 'Missing container query support!', 'fictioneer' );

  // Support for clamp?
  $notes['clamp'] = __( 'Missing clamp() support!', 'fictioneer' );

  // Support for -webkit-line-clamp?
  $notes['line-clamp'] = __( 'Missing -webkit-line-clamp support!', 'fictioneer' );

  // Format output
  array_walk( $notes, function( &$value, $key ) {
    $value = '<span class="browser-notes__note browser-notes__' . $key . '">' . $value . '</span>';
  });

  // Start HTML ---> ?>
  <div id="browser-notes" class="browser-notes">
    <strong><?php _e( 'Please update your browser:', 'fictioneer' ); ?></strong>
    <?php echo implode( ' ', $notes ); ?></div>
  <?php // <--- End HTML
}

if ( FICTIONEER_ENABLE_BROWSER_NOTES ) {
  add_action( 'fictioneer_site', 'fictioneer_browser_notes', 1 );
}

// =============================================================================
// OUTPUT NAVIGATION BAR
// =============================================================================

/**
 * Outputs the HTML for the navigation bar
 *
 * @since 5.0.0
 *
 * @param int|null       $args['post_id']           Optional. Current post ID.
 * @param int|null       $args['story_id']          Optional. Current story ID (if chapter).
 * @param string|boolean $args['header_image_url']  URL of the filtered header image or false.
 * @param array          $args['header_args']       Arguments passed to the header.php partial.
 */

function fictioneer_navigation_bar( $args ) {
  // Change tag if header style 'wide'
  if ( get_theme_mod( 'header_style', 'default' ) === 'wide' ) {
    $args['tag'] = 'header';
  }

  // Render partial
  get_template_part( 'partials/_navigation', null, $args );
}
add_action( 'fictioneer_site', 'fictioneer_navigation_bar', 10 );

// =============================================================================
// OUTPUT WIDE HEADER
// =============================================================================

/**
 * Outputs site identity HTML for wide header
 *
 * @since 5.12.5
 *
 * @param int|null       $args['post_id']           Optional. Current post ID.
 * @param int|null       $args['story_id']          Optional. Current story ID (if chapter).
 * @param string|boolean $args['header_image_url']  URL of the filtered header image or false.
 * @param array          $args['header_args']       Arguments passed to the header.php partial.
 */

function fictioneer_wide_header_identity( $args ) {
  // Abort if...
  if ( get_theme_mod( 'header_style', 'default' ) !== 'wide' ) {
    return;
  }

  // Setup
  $title_tag = is_front_page() ? 'h1' : 'div';
  $logo_tag = ( display_header_text() || ! is_front_page() ) ? 'div' : 'h1';

  // Logo
  if ( has_custom_logo() ) {
    echo '<' . $logo_tag . ' class="wide-header-logo">' . get_custom_logo() . '</' . $logo_tag . '>';
  }

  // Title and tagline (if any)
  if ( ! display_header_text() ) {
    return;
  }

  $text_shadow = get_theme_mod( 'title_text_shadow', false ) ? '' : '_no-text-shadow';
  $url = esc_url( home_url() );
  $name = get_bloginfo( 'name' );
  $tagline = get_bloginfo( 'description' );

  echo "<div class='wide-header-identity {$text_shadow}'><{$title_tag} class='wide-header-identity__title'><a href='{$url}' class='wide-header-identity__title-link' rel='home'>{$name}</{$title_tag}></a>";

  if ( $tagline ) {
    echo "<div class='wide-header-identity__tagline'>{$tagline}</div>";
  }

  echo "</div>";
}
add_action( 'fictioneer_navigation_wrapper_start', 'fictioneer_wide_header_identity' );

// =============================================================================
// OUTPUT TOP HEADER
// =============================================================================

/**
 * Outputs the HTML for the top header
 *
 * @since 5.8.1
 *
 * @param int|null       $args['post_id']           Optional. Current post ID.
 * @param int|null       $args['story_id']          Optional. Current story ID (if chapter).
 * @param string|boolean $args['header_image_url']  URL of the filtered header image or false.
 * @param array          $args['header_args']       Arguments passed to the header.php partial.
 */

function fictioneer_top_header( $args ) {
  // Abort if...
  if ( ! in_array( get_theme_mod( 'header_style', 'default' ), ['top', 'split'] ) ) {
    return;
  }

  get_template_part( 'partials/_header-top', null, $args );
}
add_action( 'fictioneer_site', 'fictioneer_top_header', 9 );

// =============================================================================
// OUTPUT INNER HEADER
// =============================================================================

/**
 * Outputs the HTML for the inner header
 *
 * @since 5.0.0
 *
 * @param int|null       $args['post_id']           Optional. Current post ID.
 * @param int|null       $args['story_id']          Optional. Current story ID (if chapter).
 * @param string|boolean $args['header_image_url']  URL of the filtered header image or false.
 * @param array          $args['header_args']       Arguments passed to the header.php partial.
 */

function fictioneer_inner_header( $args ) {
  $theme_mod = get_theme_mod( 'header_style', 'default' );

  // Abort if...
  if ( ! in_array( $theme_mod, ['default', 'split', 'overlay'] ) ) {
    return;
  }

  switch ( $theme_mod ) {
    default:
      get_template_part( 'partials/_header-inner', null, $args );
  }
}
add_action( 'fictioneer_site', 'fictioneer_inner_header', 20 );

// =============================================================================
// OUTPUT HEADER BACKGROUND
// =============================================================================

/**
 * Outputs the HTML for the header background
 *
 * @since 5.0.0
 *
 * @param int|null       $args['post_id']           Optional. Current post ID.
 * @param int|null       $args['story_id']          Optional. Current story ID (if chapter).
 * @param string|boolean $args['header_image_url']  URL of the filtered header image or false.
 * @param array          $args['header_args']       Arguments passed to the header.php partial.
 */

function fictioneer_inner_header_background( $args ) {
  // Abort if...
  if ( ! ( $args['header_image_url'] ?? 0 ) ) {
    return;
  }

  // Setup
  $header_image_style = get_theme_mod( 'header_image_style', 'default' );
  $extra_classes = [ "_style-{$header_image_style}" ];

  if ( absint( get_theme_mod( 'header_image_fading_start', 0 ) ) > 0 ) {
    $extra_classes[] = '_fading-bottom';
  }

  if ( get_theme_mod( 'header_image_shadow', true ) ) {
    $extra_classes[] = '_shadow';
  }

  // Start HTML ---> ?>
  <div class="header-background hide-on-fullscreen polygon <?php echo implode( ' ', $extra_classes ); ?>">
    <div class="header-background__wrapper polygon">
      <img src="<?php echo $args['header_image_url']; ?>" alt="Header Background Image" class="header-background__image">
    </div>
  </div>
  <?php // <--- End HTML
}
add_action( 'fictioneer_inner_header', 'fictioneer_inner_header_background', 10 );

// =============================================================================
// SORT, ORDER & FILTER QUERIES
// =============================================================================

/**
 * Renders interface to sort, order, and filter queries
 *
 * @since 5.4.0
 *
 * @param array $args {
 *   Array of arguments.
 *
 *   @type int        $current_page   Optional. Current page if paginated or `1`.
 *   @type int        $post_id        Optional. Current post ID.
 *   @type string     $queried_type   Optional. Queried post type.
 *   @type array      $query_args     Optional. Query arguments used.
 *   @type string     $order          Current order or `desc`.
 *   @type string     $orderby        Current orderby or `'modified'`.
 *   @type int|string $ago            Current date query argument part or `0`.
 * }
 */

function fictioneer_sort_order_filter_interface( $args ) {
  // Setup
  $current_url = fictioneer_get_clean_url();
  $post_type = null;

  // Archive?
  if ( is_archive() ) {
    $post_type = array_intersect(
      [ sanitize_key( $_GET['post_type'] ?? '' ) ],
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
      'label' => _x( 'Updated', 'Sort and filter option.', 'fictioneer' ),
      'url' => add_query_arg( array( 'orderby' => 'modified' ), $current_url ) . '#sof'
    ),
    'date' => array(
      'label' => _x( 'Published', 'Sort and filter option.', 'fictioneer' ),
      'url' => add_query_arg( array( 'orderby' => 'date' ), $current_url ) . '#sof'
    ),
    'title' => array(
      'label' => _x( 'Title', 'Sort and filter option.', 'fictioneer' ),
      'url' => add_query_arg( array( 'orderby' => 'title' ), $current_url ) . '#sof'
    )
  );

  if ( ! is_archive() ) {
    $orderby_menu['words'] = array(
      'label' => _x( 'Words', 'Sort and filter option.', 'fictioneer' ),
      'url' => add_query_arg( array( 'orderby' => 'words' ), $current_url ) . '#sof'
    );
  }

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
          echo "<a href='{$url}' rel='nofollow'>{$tuple['label']}</a>";
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
          echo "<a href='{$url}' rel='nofollow'>{$tuple['label']}</a>";
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
          echo "<a href='{$url}' rel='nofollow'>{$tuple['label']}</a>";
        }

        echo '</div>';
      ?></div>
    <?php endif; ?>

    <a class="list-button _order  <?php echo $args['order'] === 'desc' ? '_on' : '_off'; ?>" href="<?php echo $order_link; ?>" rel='nofollow' aria-label="<?php esc_attr_e( 'Toggle between ascending and descending order', 'fictioneer' ); ?>">
      <i class="fa-solid fa-arrow-up-short-wide _off"></i><i class="fa-solid fa-arrow-down-wide-short _on"></i>
    </a>

  </div>
  <?php // <--- End HTML
}
add_action( 'fictioneer_stories_after_content', 'fictioneer_sort_order_filter_interface', 20 );
add_action( 'fictioneer_chapters_after_content', 'fictioneer_sort_order_filter_interface', 20 );
add_action( 'fictioneer_collections_after_content', 'fictioneer_sort_order_filter_interface', 20 );
add_action( 'fictioneer_recommendations_after_content', 'fictioneer_sort_order_filter_interface', 20 );
add_action( 'fictioneer_archive_loop_before', 'fictioneer_sort_order_filter_interface', 10 );

// =============================================================================
// SEARCH FORM & RESULTS
// =============================================================================

/**
 * Adds age rating select to advanced search form
 *
 * @since 5.12.5
 *
 * @param array $args  Arguments passed to the search form.
 */

function fictioneer_add_search_for_age_rating( $args ) {
  $age_rating = array_intersect(
    [ $_GET['age_rating'] ?? 0 ],
    ['Everyone', 'Teen', 'Mature', 'Adult']
  );
  $age_rating = reset( $age_rating ) ?: 0;

  // Start HTML ---> ?>
  <div class="search-form__select-wrapper select-wrapper">
    <div class="search-form__select-title"><?php _ex( 'Age Rating', 'Advanced search heading.', 'fictioneer' ); ?></div>
    <select name="age_rating" class="search-form__select" autocomplete="off" data-default="Any">
      <option value="Any" <?php echo ! $age_rating ? 'selected' : ''; ?>><?php _ex( 'Any', 'Advanced search option.', 'fictioneer' ); ?></option>
      <option value="Everyone" <?php echo $age_rating === 'Everyone' ? 'selected' : ''; ?>><?php echo fcntr( 'Everyone' ); ?></option>
      <option value="Teen" <?php echo $age_rating === 'Teen' ? 'selected' : ''; ?>><?php echo fcntr( 'Teen' ); ?></option>
      <option value="Mature" <?php echo $age_rating === 'Mature' ? 'selected' : ''; ?>><?php echo fcntr( 'Mature' ); ?></option>
      <option value="Adult" <?php echo $age_rating === 'Adult' ? 'selected' : ''; ?>><?php echo fcntr( 'Adult' ); ?></option>
    </select>
  </div>
  <?php // <--- End HTML
}
add_action( 'fictioneer_search_form_filters', 'fictioneer_add_search_for_age_rating' );

/**
 * Adds story status select to advanced search form
 *
 * @since 5.11.0
 *
 * @param array $args  Arguments passed to the search form.
 */

function fictioneer_add_search_for_status( $args ) {
  $story_status = array_intersect(
    [ $_GET['story_status'] ?? 0 ],
    ['Completed', 'Ongoing', 'Oneshot', 'Hiatus', 'Canceled']
  );
  $story_status = reset( $story_status ) ?: 0;

  // Start HTML ---> ?>
  <div class="search-form__select-wrapper select-wrapper">
    <div class="search-form__select-title"><?php _ex( 'Status', 'Advanced search heading.', 'fictioneer' ); ?></div>
    <select name="story_status" class="search-form__select" autocomplete="off" data-default="Any">
      <option value="Any" <?php echo ! $story_status ? 'selected' : ''; ?>><?php _ex( 'Any', 'Advanced search option.', 'fictioneer' ); ?></option>
      <option value="Completed" <?php echo $story_status === 'Completed' ? 'selected' : ''; ?>><?php echo fcntr( 'Completed' ); ?></option>
      <option value="Ongoing" <?php echo $story_status === 'Ongoing' ? 'selected' : ''; ?>><?php echo fcntr( 'Ongoing' ); ?></option>
      <option value="Oneshot" <?php echo $story_status === 'Oneshot' ? 'selected' : ''; ?>><?php echo fcntr( 'Oneshot' ); ?></option>
      <option value="Hiatus" <?php echo $story_status === 'Hiatus' ? 'selected' : ''; ?>><?php echo fcntr( 'Hiatus' ); ?></option>
      <option value="Canceled" <?php echo $story_status === 'Canceled' ? 'selected' : ''; ?>><?php echo fcntr( 'Canceled' ); ?></option>
    </select>
  </div>
  <?php // <--- End HTML
}
add_action( 'fictioneer_search_form_filters', 'fictioneer_add_search_for_status' );

/**
 * Adds min words select to advanced search form
 *
 * @since 5.13.1
 *
 * @param array $args  Arguments passed to the search form.
 */

function fictioneer_add_search_for_min_words( $args ) {
  $words = absint( $_GET['min_words'] ?? 0 );

  // Start HTML ---> ?>
  <div class="search-form__select-wrapper select-wrapper">
    <div class="search-form__select-title"><?php _ex( 'Min Words', 'Advanced search heading.', 'fictioneer' ); ?></div>
    <select name="min_words" class="search-form__select" autocomplete="off" data-default="0">
      <option value="0" <?php echo ! $words ? 'selected' : ''; ?>><?php _ex( 'Minimum', 'Advanced search option.', 'fictioneer' ); ?></option>
      <option value="1000" <?php echo $words == 1000 ? 'selected' : ''; ?>><?php _ex( '1,000 Words', 'Advanced search option.', 'fictioneer' ); ?></option>
      <option value="5000" <?php echo $words == 5000 ? 'selected' : ''; ?>><?php _ex( '5,000 Words', 'Advanced search option.', 'fictioneer' ); ?></option>
      <option value="10000" <?php echo $words == 10000 ? 'selected' : ''; ?>><?php _ex( '10,000 Words', 'Advanced search option.', 'fictioneer' ); ?></option>
      <option value="25000" <?php echo $words == 25000 ? 'selected' : ''; ?>><?php _ex( '25,000 Words', 'Advanced search option.', 'fictioneer' ); ?></option>
      <option value="50000" <?php echo $words == 50000 ? 'selected' : ''; ?>><?php _ex( '50,000 Words', 'Advanced search option.', 'fictioneer' ); ?></option>
      <option value="100000" <?php echo $words == 100000 ? 'selected' : ''; ?>><?php _ex( '100,000 Words', 'Advanced search option.', 'fictioneer' ); ?></option>
      <option value="250000" <?php echo $words == 250000 ? 'selected' : ''; ?>><?php _ex( '250,000 Words', 'Advanced search option.', 'fictioneer' ); ?></option>
      <option value="500000" <?php echo $words == 500000 ? 'selected' : ''; ?>><?php _ex( '500,000 Words', 'Advanced search option.', 'fictioneer' ); ?></option>
      <option value="1000000" <?php echo $words == 1000000 ? 'selected' : ''; ?>><?php _ex( '1,000,000 Words', 'Advanced search option.', 'fictioneer' ); ?></option>
    </select>
  </div>
  <?php // <--- End HTML
}
add_action( 'fictioneer_search_form_filters', 'fictioneer_add_search_for_min_words' );

/**
 * Adds max words select to advanced search form
 *
 * @since 5.13.1
 *
 * @param array $args  Arguments passed to the search form.
 */

function fictioneer_add_search_for_max_words( $args ) {
  $words = absint( $_GET['max_words'] ?? 0 );

  // Start HTML ---> ?>
  <div class="search-form__select-wrapper select-wrapper">
    <div class="search-form__select-title"><?php _ex( 'Max Words', 'Advanced search heading.', 'fictioneer' ); ?></div>
    <select name="max_words" class="search-form__select" autocomplete="off" data-default="0">
      <option value="0" <?php echo ! $words ? 'selected' : ''; ?>><?php _ex( 'Maximum', 'Advanced search option.', 'fictioneer' ); ?></option>
      <option value="1000" <?php echo $words == 1000 ? 'selected' : ''; ?>><?php _ex( '1,000 Words', 'Advanced search option.', 'fictioneer' ); ?></option>
      <option value="5000" <?php echo $words == 5000 ? 'selected' : ''; ?>><?php _ex( '5,000 Words', 'Advanced search option.', 'fictioneer' ); ?></option>
      <option value="10000" <?php echo $words == 10000 ? 'selected' : ''; ?>><?php _ex( '10,000 Words', 'Advanced search option.', 'fictioneer' ); ?></option>
      <option value="25000" <?php echo $words == 25000 ? 'selected' : ''; ?>><?php _ex( '25,000 Words', 'Advanced search option.', 'fictioneer' ); ?></option>
      <option value="50000" <?php echo $words == 50000 ? 'selected' : ''; ?>><?php _ex( '50,000 Words', 'Advanced search option.', 'fictioneer' ); ?></option>
      <option value="100000" <?php echo $words == 100000 ? 'selected' : ''; ?>><?php _ex( '100,000 Words', 'Advanced search option.', 'fictioneer' ); ?></option>
      <option value="250000" <?php echo $words == 250000 ? 'selected' : ''; ?>><?php _ex( '250,000 Words', 'Advanced search option.', 'fictioneer' ); ?></option>
      <option value="500000" <?php echo $words == 500000 ? 'selected' : ''; ?>><?php _ex( '500,000 Words', 'Advanced search option.', 'fictioneer' ); ?></option>
      <option value="1000000" <?php echo $words == 1000000 ? 'selected' : ''; ?>><?php _ex( '1,000,000 Words', 'Advanced search option.', 'fictioneer' ); ?></option>
    </select>
  </div>
  <?php // <--- End HTML
}
add_action( 'fictioneer_search_form_filters', 'fictioneer_add_search_for_max_words' );

/**
 * Outputs the HTML for no search params
 *
 * @since 5.5.2
 */

function fictioneer_no_search_params() {
  echo __( 'Please enter search parameters.', 'fictioneer' );
}
add_action( 'fictioneer_search_no_params', 'fictioneer_no_search_params' );

/**
 * Outputs the HTML for no search results
 *
 * @since 5.5.2
 */

function fictioneer_no_search_results() {
  echo __( 'No results.', 'fictioneer' );
}
add_action( 'fictioneer_search_no_results', 'fictioneer_no_search_results' );

?>
