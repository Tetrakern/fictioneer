<?php
/**
 * Partial: Navigation
 *
 * Renders the main navigation bar.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.0.0
 * @since 5.12.5 - Added wrapper actions and support for wide header.
 * @since 5.20.0 - Added Elementor support.
 *
 * @internal $args['post_id']           Optional. Current post ID.
 * @internal $args['story_id']          Optional. Current story ID (if chapter).
 * @internal $args['header_image_url']  URL of the filtered header image or false.
 * @internal $args['header_args']       Arguments passed to the header.php partial.
 * @internal $args['tag']               Optional. Wrapping tag.
 */


// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$header_style = get_theme_mod( 'header_style', 'default' );
$mobile_nav_style = get_theme_mod( 'mobile_nav_style', 'overflow' );
$tag = $args['tag'] ?? 'div';
$classes = [];

if ( $mobile_nav_style === 'collapse' || $header_style === 'wide' ) {
  $classes[] = '_collapse-on-mobile';
}

if ( $header_style === 'wide' ) {
  $classes[] = '_wide';
}

?>

<<?php echo $tag; ?> id="full-navigation" class="main-navigation <?php echo implode( ' ', $classes ); ?>">

  <div id="nav-observer-sticky" class="observer nav-observer"></div>

  <div class="main-navigation__background"></div>

  <?php do_action( 'fictioneer_navigation_top', $args ); ?>

  <div class="main-navigation__wrapper">

    <?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'nav_bar' ) ) : ?>

      <?php do_action( 'fictioneer_navigation_wrapper_start', $args ); ?>

      <button class="mobile-menu-button follows-alert-number" data-fictioneer-follows-target="newDisplay" data-action="click->fictioneer#toggleMobileMenu">
        <?php
          fictioneer_icon( 'fa-bars', 'off' );
          fictioneer_icon( 'fa-xmark', 'on' );
        ?>
        <span class="mobile-menu-button__label"><?php _ex( 'Menu' , 'Mobile menu label', 'fictioneer' ); ?></span>
      </button>

      <nav class="main-navigation__left" aria-label="<?php echo esc_attr__( 'Main Navigation', 'fictioneer' ); ?>">
        <?php
          if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'nav_menu' ) ) {
            if ( has_nav_menu( 'nav_menu' ) ) {
              $menu = null;
              $transient_enabled = fictioneer_enable_menu_transients( 'nav_menu' );

              if ( $transient_enabled ) {
                $menu = get_transient( 'fictioneer_main_nav_menu_html' );
              }

              if ( empty( $menu ) ) {
                $menu = wp_nav_menu(
                  array(
                    'theme_location' => 'nav_menu',
                    'menu_class' => 'main-navigation__list',
                    'container' => '',
                    'menu_id' => 'menu-navigation',
                    'items_wrap' => '<ul id="%1$s" data-menu-id="main" class="%2$s">%3$s</ul>',
                    'echo' => false
                  )
                );

                if ( $menu !== false ) {
                  $menu = str_replace( ['current_page_item', 'current-menu-item', 'aria-current="page"'], '', $menu );
                }

                if ( $transient_enabled ) {
                  set_transient( 'fictioneer_main_nav_menu_html', $menu );
                }
              }

              fictioneer_add_taxonomy_submenus( $menu );

              echo $menu;
            }
          }
        ?>
      </nav>

      <div class="main-navigation__right">
        <?php fictioneer_render_icon_menu( array( 'location' => 'in-navigation' ) );; ?>
      </div>

      <?php do_action( 'fictioneer_navigation_wrapper_end', $args ); ?>

    <?php endif; ?>

  </div>

  <?php do_action( 'fictioneer_navigation_bottom', $args ); ?>

</<?php echo $tag; ?>>
