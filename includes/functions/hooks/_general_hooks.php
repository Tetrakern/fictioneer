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
    $story_id = fictioneer_get_chapter_story_id( $post_id );

    if ( ! empty( $story_id ) ) {
      $custom_css .= get_post_meta( $story_id, 'fictioneer_story_css', true );
    }
  }

  if ( ! empty( $custom_css ) && ! preg_match( '#</?\w+#', $custom_css ) ) {
    echo '<style id="fictioneer-custom-styles">' . str_replace( '<', '', $custom_css ). '</style>';
  }
}
add_action( 'wp_head', 'fictioneer_add_fiction_css' );

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
        $transient_enabled = fictioneer_enable_menu_transients( 'footer_menu' );

        if ( $transient_enabled ) {
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

          if ( $transient_enabled ) {
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
    fictioneer_get_cached_partial( 'partials/_modal-formatting' );
    fictioneer_get_cached_partial( 'partials/_modal-tts-settings' );

    if (
      get_option( 'fictioneer_enable_suggestions' ) &&
      ! fictioneer_is_commenting_disabled( get_the_ID() ) &&
      comments_open()
    ) {
      fictioneer_get_cached_partial( 'partials/_modal-suggestions' );
    }
  }

  // Login
  if ( fictioneer_show_login() ) {
    get_template_part( 'partials/_modal-login' );
  }

  // Social sharing
  get_template_part( 'partials/_modal-sharing' );

  // Site settings
  fictioneer_get_cached_partial( 'partials/_modal-site-settings' );

  // BBCodes tutorial
  if (
    ! empty( $args['post_id'] ) &&
    ! post_password_required() &&
    comments_open() &&
    ! fictioneer_is_commenting_disabled( $args['post_id'] )
  ) {
    fictioneer_get_cached_partial( 'partials/_modal-bbcodes' );
  }

  // Story chapter changelog
  if (
    ! $is_archive &&
    $args['post_type'] == 'fcn_story' &&
    FICTIONEER_ENABLE_STORY_CHANGELOG &&
    get_option( 'fictioneer_show_story_changelog' )
  ) {
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
  // Return early if...
  if ( $args['header_args']['blank'] ?? 0 ) {
    return;
  }

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
  // Return early if...
  if ( $args['header_args']['blank'] ?? 0 ) {
    return;
  }

  // Abort if...
  if ( ! in_array( get_theme_mod( 'header_style', 'default' ), ['top', 'split'] ) ) {
    return;
  }

  // Render Elementor or theme template
  if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) {
    get_template_part( 'partials/_header-top', null, $args );
  }
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
  // Return early if...
  if ( $args['header_args']['blank'] ?? 0 ) {
    return;
  }

  // Setup
  $theme_mod = get_theme_mod( 'header_style', 'default' );

  // Abort if...
  if ( ! in_array( $theme_mod, ['default', 'split', 'overlay'] ) || ( $args['header_args']['no_header'] ?? 0 ) ) {
    return;
  }

  // Render Elementor or theme template
  if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) {
    switch ( $theme_mod ) {
      default:
        get_template_part( 'partials/_header-inner', null, $args );
    }
  }
}
add_action( 'fictioneer_site', 'fictioneer_inner_header', 20 );

// =============================================================================
// OUTPUT TEXT CENTER HEADER
// =============================================================================

/**
 * Outputs the HTML for the text (center) header
 *
 * @since 5.21.0
 *
 * @param int|null       $args['post_id']           Optional. Current post ID.
 * @param int|null       $args['story_id']          Optional. Current story ID (if chapter).
 * @param string|boolean $args['header_image_url']  URL of the filtered header image or false.
 * @param array          $args['header_args']       Arguments passed to the header.php partial.
 */

function fictioneer_text_center_header( $args ) {
  // Return early if...
  if ( ( $args['header_args']['blank'] ?? 0 ) || ! display_header_text() ) {
    return;
  }

  // Setup
  $theme_mod = get_theme_mod( 'header_style', 'default' );

  // Abort if...
  if ( $theme_mod !== 'text_center' || ( $args['header_args']['no_header'] ?? 0 ) ) {
    return;
  }

  // Render Elementor or theme template
  if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) {
    // Start HTML ---> ?>
    <header class="text-center-header hide-on-fullscreen"><?php
      // Add header content as action
      add_action(
        'fictioneer_text_center_header',
        function ( $args ) {
          $title_tag = is_front_page() ? 'h1' : 'div';
          $text_shadow = get_theme_mod( 'title_text_shadow', false ) ? '' : '_no-text-shadow';

          printf(
            "<div class='text-center-header__content {$text_shadow}'><{$title_tag} class='text-center-header__title'><a href='%s' class='text-center-header__link' rel='home'>%s</a></{$title_tag}>%s</div>",
            esc_url( home_url() ),
            get_bloginfo( 'name' ),
            get_bloginfo( 'description' ) ?
              '<div class="text-center-header__tagline">' . get_bloginfo( 'description' ) . '</div>' : ''
          );
        }
      );

      // Render everything via action
      do_action( 'fictioneer_text_center_header', $args );
    ?></header>
    <?php // <--- End HTML
  }
}
add_action( 'fictioneer_site', 'fictioneer_text_center_header', 20 );

// =============================================================================
// OUTPUT POST CONTENT HEADER
// =============================================================================

/**
 * Outputs the HTML for the post content header
 *
 * @since 5.21.0
 *
 * @param int|null       $args['post_id']           Optional. Current post ID.
 * @param int|null       $args['story_id']          Optional. Current story ID (if chapter).
 * @param string|boolean $args['header_image_url']  URL of the filtered header image or false.
 * @param array          $args['header_args']       Arguments passed to the header.php partial.
 */

function fictioneer_post_content_header( $args ) {
  // Return early if...
  if ( ( $args['header_args']['blank'] ?? 0 ) ) {
    return;
  }

  // Setup
  $post_id = get_theme_mod( 'header_post_content_id', 0 );
  $theme_mod = get_theme_mod( 'header_style', 'default' );

  // Abort if...
  if ( $theme_mod !== 'post_content' || ! $post_id || ( $args['header_args']['no_header'] ?? 0 ) ) {
    return;
  }

  // Get post
  global $post;

  $original_post = $post;
  $post = get_post( $post_id );

  if ( ! $post ) {
    $post = $original_post;
    return;
  }

  // Render Elementor or theme template
  if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) {
    setup_postdata( $post );
    $content = apply_filters( 'get_the_content', $post->post_content );
    $content = apply_filters( 'the_content', $content );
    wp_reset_postdata();
    $post = $original_post;

    echo '<header class="post-content-header content-section hide-on-fullscreen">' . $content . '</header>';
  }
}
add_action( 'fictioneer_site', 'fictioneer_post_content_header', 20 );

// =============================================================================
// OUTPUT HEADER BACKGROUND
// =============================================================================

/**
 * Outputs the HTML for the header background
 *
 * @since 5.0.0
 * @since 5.24.1 - Considered additional arguments.
 *
 * @param int|null       $args['post_id']              Optional. Current post ID.
 * @param string|null    $args['post_type']            Optional. Current post type.
 * @param int|null       $args['story_id']             Optional. Current story ID (if chapter).
 * @param string|boolean $args['header_image_url']     URL of the filtered header image or false.
 * @param string         $args['header_image_source']  Either default, post, or story.
 * @param array          $args['header_args']          Arguments passed to the header.php partial.
 */

function fictioneer_inner_header_background( $args ) {
  // Abort if...
  if ( ( $args['header_args']['no_header'] ?? 0 ) ) {
    return;
  }

  // Setup
  $header_image_url = $args['header_image_url'] ?? 0;
  $header_image_style = get_theme_mod( 'header_image_style', 'default' );
  $extra_classes = array(
    "_style-{$header_image_style}",
    "_image-source-{$args['header_image_source']}",
    "_post-{$args['post_id']}"
  );

  if ( absint( get_theme_mod( 'header_image_fading_start', 0 ) ) > 0 ) {
    $extra_classes[] = '_fading-bottom';
  }

  if ( get_theme_mod( 'header_image_shadow', true ) ) {
    $extra_classes[] = '_shadow';
  }

  if ( $args['post_type'] ) {
    $extra_classes[] = '_' . $args['post_type'];
  }

  if ( ! $header_image_url ) {
    $extra_classes[] = '_no-image';
  }

  // Start HTML ---> ?>
  <div class="header-background hide-on-fullscreen polygon <?php echo implode( ' ', $extra_classes ); ?>">
    <div class="header-background__wrapper polygon">
      <?php if ( $header_image_url) : ?>
        <img src="<?php echo $header_image_url; ?>" alt="Header Background Image" class="header-background__image">
      <?php endif; ?>
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
  $template = get_page_template_slug();
  $current_url = fictioneer_get_clean_url();
  $post_type = null;

  // Archive?
  if ( is_archive() ) {
    $post_type = fictioneer_sanitize_query_var(
      sanitize_key( $_GET['post_type'] ?? '' ),
      ['any', 'post', 'fcn_story', 'fcn_chapter', 'fcn_collection', 'fcn_recommendation']
    );
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

  if ( ! in_array( $template, ['recommendations.php', 'collections.php'] ) ) {
    $orderby_menu['comment_count'] = array(
      'label' => _x( 'Comments', 'Sort and filter option.', 'fictioneer' ),
      'url' => add_query_arg( array( 'orderby' => 'comment_count' ), $current_url ) . '#sof'
    );
  }

  if ( ! is_archive() && ! in_array( $template, ['recommendations.php', 'collections.php'] ) ) {
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
      <div class="list-button _text popup-menu-toggle" tabindex="0" role="button" data-fictioneer-last-click-target="toggle" data-action="click->fictioneer-last-click#toggle"><?php
        echo $post_type_menu[ $post_type ?? 'any' ]['label'] ?? __( 'Unknown', 'fictioneer' );
        echo '<div class="popup-menu _bottom _center _fixed-position">';
        echo '<div class="popup-heading">' . __( 'Post Type', 'fictioneer' ) . '</div>';

        foreach ( $post_type_menu as $tuple ) {
          $url = esc_url( $tuple['url'] );
          echo "<a href='{$url}' rel='nofollow'>{$tuple['label']}</a>";
        }

        echo '</div>';
      ?></div>
    <?php endif; ?>

    <?php if ( ! empty( $orderby_menu ) ) : ?>
      <div class="list-button _text popup-menu-toggle" tabindex="0" role="button" data-fictioneer-last-click-target="toggle" data-action="click->fictioneer-last-click#toggle"><?php
        echo $orderby_menu[ $args['orderby'] ]['label'] ?? __( 'Custom', 'fictioneer' );
        echo '<div class="popup-menu _bottom _center _fixed-position">';
        echo '<div class="popup-heading">' . __( 'Order By', 'fictioneer' ) . '</div>';

        foreach ( $orderby_menu as $tuple ) {
          $url = esc_url( $tuple['url'] );
          echo "<a href='{$url}' rel='nofollow'>{$tuple['label']}</a>";
        }

        echo '</div>';
      ?></div>
    <?php endif; ?>

    <?php if ( ! empty( $date_menu ) ) : ?>
      <div class="list-button _text popup-menu-toggle" tabindex="0" role="button" data-fictioneer-last-click-target="toggle" data-action="click->fictioneer-last-click#toggle"><?php
        $key = str_replace( ' ', '_', $args['ago'] ?? '' );

        if ( empty( $date_menu[ $key ]['label'] ) ) {
          echo is_numeric( $args['ago'] ) ? sprintf(
            __( 'Past %s Days', 'fictioneer' ), $args['ago']
          ) : urldecode( $args['ago'] );
        } else {
          echo $date_menu[ $key ]['label'];
        }

        echo '<div class="popup-menu _bottom _center _fixed-position">';
        echo '<div class="popup-heading">' . __( 'Time Range', 'fictioneer' ) . '</div>';

        foreach ( $date_menu as $tuple ) {
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
  // Setup
  $age_rating = fictioneer_sanitize_query_var(
    $_GET['age_rating'] ?? 0,
    ['Everyone', 'Teen', 'Mature', 'Adult'],
    0,
    array( 'keep_case' => 1 )
  );

  // Start HTML ---> ?>
  <div class="search-form__select-wrapper select-wrapper _age-rating">
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
  // Setup
  $story_status = fictioneer_sanitize_query_var(
    $_GET['story_status'] ?? 0,
    ['Completed', 'Ongoing', 'Oneshot', 'Hiatus', 'Canceled'],
    0,
    array( 'keep_case' => 1 )
  );

  // Start HTML ---> ?>
  <div class="search-form__select-wrapper select-wrapper _status">
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
  $words = absint( $_GET['miw'] ?? 0 );

  // Start HTML ---> ?>
  <div class="search-form__select-wrapper select-wrapper _min-words">
    <div class="search-form__select-title"><?php _ex( 'Min Words', 'Advanced search heading.', 'fictioneer' ); ?></div>
    <select name="miw" class="search-form__select" autocomplete="off" data-default="0">
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
  $words = absint( $_GET['maw'] ?? 0 );

  // Start HTML ---> ?>
  <div class="search-form__select-wrapper select-wrapper _max-words">
    <div class="search-form__select-title"><?php _ex( 'Max Words', 'Advanced search heading.', 'fictioneer' ); ?></div>
    <select name="maw" class="search-form__select" autocomplete="off" data-default="0">
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

// =============================================================================
// OUTPUT MODAL LOGIN OPTIONS
// =============================================================================

/**
 * Outputs the HTML for the OAuth 2.0 login links
 *
 * @since 5.18.0
 */

function fictioneer_render_oauth_login_options() {
  echo fictioneer_get_oauth2_login_links( fcntr( 'login_with' ), 'button _secondary' );
}
add_action( 'fictioneer_modal_login_option', 'fictioneer_render_oauth_login_options' );

/**
 * Outputs the HTML for the wp-admin login link
 *
 * @since 5.18.0
 */

function fictioneer_render_wp_login_option() {
  // Setup
  global $wp;

  // Start HTML ---> ?>
  <a href="<?php echo esc_url( wp_login_url( home_url( $wp->request ) ) ); ?>" rel="noopener noreferrer nofollow" class="button _secondary _wp-login">
    <span><i class="fa-solid fa-right-to-bracket"></i> <?php _e( 'Login/Register', 'fictioneer' ); ?></span>
  </a>
  <?php // <--- End HTML
}

if ( get_option( 'fictioneer_show_wp_login_link' ) ) {
  add_action( 'fictioneer_modal_login_option', 'fictioneer_render_wp_login_option', 20 );
}

// =============================================================================
// OUTPUT WP-SIGNUP AND WP-ACTIVE "PAGE"
// =============================================================================

/**
 * Outputs the start of the page wrapper HTML for the wp-signup.php and wp-activate.php
 *
 * @since 5.18.1
 *
 * @param int|null       $args['post_id']           Optional. Current post ID.
 * @param int|null       $args['story_id']          Optional. Current story ID (if chapter).
 * @param string|boolean $args['header_image_url']  URL of the filtered header image or false.
 * @param array          $args['header_args']       Arguments passed to the header.php partial.
 */

function fictioneer_mu_registration_start( $args ) {
  // Return early if...
  if ( $args['header_args']['blank'] ?? 0 ) {
    return;
  }

  // Start HTML ---> ?>
  <main id="main" class="main singular wp-registration">
    <div class="observer main-observer"></div>
    <?php do_action( 'fictioneer_main' ); ?>
    <div class="main__background polygon polygon--main background-texture"></div>
    <div class="main__wrapper">
      <?php do_action( 'fictioneer_main_wrapper' ); ?>
      <article id="singular-wp-registration" class="singular__article">
        <section class="singular__content content-section">
  <?php // <--- End HTML
}

if ( FICTIONEER_MU_REGISTRATION ) {
  add_action( 'fictioneer_site', 'fictioneer_mu_registration_start', 999 );
}

/**
 * Outputs the end of the page wrapper HTML for the wp-signup.php and wp-activate.php
 *
 * @since 5.18.1
 *
 * @param int|null $args['post_id']  Optional. Current post ID.
 */

function fictioneer_mu_registration_end( $args ) {
  // Start HTML ---> ?>
        </section>
        <footer class="singular__footer"><?php do_action( 'fictioneer_singular_footer' ); ?></footer>
      </article>
    </div>
  </main>
  <?php // <--- End HTML
}

if ( FICTIONEER_MU_REGISTRATION ) {
  add_action( 'fictioneer_after_main', 'fictioneer_mu_registration_end', 999 );
}

// =============================================================================
// TAXONOMY SUBMENU (HOOKED IN NAVIGATION PARTIAL)
// =============================================================================

/**
 * Outputs the <template> HTML for the taxonomy submenu
 *
 * Note: The ID of the template element is "term-submenu-{$type}".
 *
 * @since 5.22.1
 * @since 5.24.1 - Added Transient.
 *
 * @param string $type        Optional. The taxonomy type to build the menu for. Default 'fcn_genre'.
 * @param bool   $hide_empty  Optional. Whether to include empty taxonomies. Default true.
 */

function fictioneer_render_taxonomy_submenu( $type = 'fcn_genre', $hide_empty = true ) {
  // Transient cache
  $key = "fictioneer_taxonomy_submenu_{$type}" . ( $hide_empty ? '_1' : '' );
  $transient = get_transient( $key );

  if ( $transient ) {
    echo apply_filters( 'fictioneer_filter_cached_taxonomy_submenu_html', $transient, $type, $hide_empty );
    return;
  }

  // Query terms
  $terms = get_terms(
    apply_filters(
      'fictioneer_filter_taxonomy_submenu_query_args',
      array( 'taxonomy' => $type, 'hide_empty' => $hide_empty ),
      $type
    )
  );

  // All good?
  if ( is_wp_error( $terms ) ) {
    return;
  }

  // Setup
  $nice_type = str_replace( 'fcn_', '', $type );
  $taxonomy = get_taxonomy( $type );
  $html = '';

  // Build HTML
  $html = "<template id='term-submenu-{$type}'><div class='sub-menu nav-terms-submenu _{$nice_type}'>";
  $html .= '<div class="nav-terms-submenu__note">';
  $html .= apply_filters(
    'fictioneer_filter_taxonomy_submenu_note',
    sprintf( __( 'Choose a %s to browse', 'fictioneer' ), $taxonomy->labels->singular_name ),
    $taxonomy
  );
  $html .= '</div><div class="nav-terms-submenu__wrapper">';

  foreach ( $terms as $term ) {
    $link = esc_url( get_term_link( $term ) );
    $name = esc_html( $term->name );

    $html .= "<a href='{$link}' class='nav-term-item _{$nice_type} _no-menu-item-style' data-type='{$type}' data-term-id='{$term->term_id}'>{$name}</a>";
  }

  $html .= '</div></div></template>';

  // Apply filters
  $html = apply_filters( 'fictioneer_filter_taxonomy_submenu_html', $html, $terms, $type, $hide_empty );

  // Cache as Transient
  set_transient( $key, $html, HOUR_IN_SECONDS );

  // Render
  echo $html;
}

/**
 * Adds actions to render taxonomy submenus as needed
 *
 * @since 5.22.1
 *
 * @param string $menu  The menu HTML to be rendered.
 */

function fictioneer_add_taxonomy_submenus( $menu ) {
  $submenu_callbacks = array(
    'trigger-term-menu-categories' => 'fictioneer_render_category_submenu',
    'trigger-term-menu-tags' => 'fictioneer_render_tag_submenu',
    'trigger-term-menu-genres' => 'fictioneer_render_genre_submenu',
    'trigger-term-menu-fandoms' => 'fictioneer_render_fandom_submenu',
    'trigger-term-menu-characters' => 'fictioneer_render_character_submenu',
    'trigger-term-menu-warning' => 'fictioneer_render_warning_submenu'
  );

  foreach ( $submenu_callbacks as $trigger => $callback ) {
    if ( strpos( $menu, $trigger ) !== false ) {
      add_action( 'wp_footer', $callback );
    }
  }
}

/**
 * Action wrapper for the category submenu
 *
 * @since 5.22.1
 */

function fictioneer_render_category_submenu() {
  fictioneer_render_taxonomy_submenu( 'category' );
}

/**
 * Action wrapper for the tag submenu
 *
 * @since 5.22.1
 */

function fictioneer_render_tag_submenu() {
  fictioneer_render_taxonomy_submenu( 'post_tag' );
}

/**
 * Action wrapper for the genre submenu
 *
 * @since 5.22.1
 */

function fictioneer_render_genre_submenu() {
  fictioneer_render_taxonomy_submenu( 'fcn_genre' );
}

/**
 * Action wrapper for the fandom submenu
 *
 * @since 5.22.1
 */

function fictioneer_render_fandom_submenu() {
  fictioneer_render_taxonomy_submenu( 'fcn_fandom' );
}

/**
 * Action wrapper for the character submenu
 *
 * @since 5.22.1
 */

function fictioneer_render_character_submenu() {
  fictioneer_render_taxonomy_submenu( 'fcn_character' );
}

/**
 * Action wrapper for the warning submenu
 *
 * @since 5.22.1
 */

function fictioneer_render_warning_submenu() {
  fictioneer_render_taxonomy_submenu( 'fcn_content_warning' );
}

// =============================================================================
// DISCORD META FIELD CLEANUP
// =============================================================================

/**
 * Removes Discord trigger meta field if outdated
 *
 * @since 5.24.1
 * @global WP_Post $post
 */

function fictioneer_cleanup_discord_meta() {
  global $post;

  if ( ! is_singular() || ! $post || ! get_post_meta( $post->ID, 'fictioneer_discord_post_trigger' ) ) {
    return;
  }

  $post_timestamp = get_post_time( 'U', true, $post->ID );
  $current_timestamp = current_time( 'U', true );

  if ( $current_timestamp - $post_timestamp > DAY_IN_SECONDS ) {
    delete_post_meta( $post->ID, 'fictioneer_discord_post_trigger' );
  }
}
add_action( 'wp_head', 'fictioneer_cleanup_discord_meta' );

// =============================================================================
// TOOLTIP DIALOG
// =============================================================================

/**
 * Outputs the HTML for the tooltip dialog modal
 *
 * @since 5.25.0
 */

function fictioneer_render_tooltip_dialog_modal_html() {
  // Start HTML ---> ?>
  <dialog class="dialog-modal _tooltip" id="fictioneer-tooltip-dialog">
    <div class="dialog-modal__wrapper">
      <button class="dialog-modal__close" data-click-action="close-dialog-modal" aria-label="<?php esc_attr_e( 'Close modal', 'fictioneer' ); ?>"><?php fictioneer_icon( 'fa-xmark' ); ?></button>
      <div class="dialog-modal__header" data-finder="tooltip-dialog-header"><?php _ex( 'Note', 'Default tooltip modal header.', 'fictioneer' ); ?></div>
      <div class="dialog-modal__row dialog-modal__description _tooltip _small-top" data-finder="tooltip-dialog-content"></div>
    </div>
  </dialog>
  <?php // <--- End HTML
}
add_action( 'fictioneer_footer', 'fictioneer_render_tooltip_dialog_modal_html' );
