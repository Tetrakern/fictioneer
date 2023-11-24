<?php
/**
 * Partial: Navigation
 *
 * Renders the main navigation bar.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.0
 *
 * @internal $args['post_id']           Optional. Current post ID.
 * @internal $args['story_id']          Optional. Current story ID (if chapter).
 * @internal $args['header_image_url']  URL of the filtered header image or false.
 * @internal $args['header_args']       Arguments passed to the header.php partial.
 */
?>

<?php

// No direct access!
defined( 'ABSPATH' ) OR exit;

?>

<nav id="full-navigation" class="main-navigation" aria-label="Main Navigation">
  <div id="nav-observer-sticky" class="observer nav-observer"></div>
  <div class="main-navigation__background"></div>

  <?php do_action( 'fictioneer_navigation_top', $args ); ?>

  <div class="main-navigation__wrapper">

    <label for="mobile-menu-toggle" class="mobile-menu-button follows-alert-number"><?php
      fictioneer_icon( 'fa-bars', 'off' );
      fictioneer_icon( 'fa-xmark', 'on' );
    ?></label>

    <div class="main-navigation__left">
      <?php
        if ( has_nav_menu( 'nav_menu' ) ) {
          $menu = null;

          if ( FICTIONEER_ENABLE_MENU_TRANSIENTS ) {
            $menu = get_transient( 'fictioneer_main_nav_menu' );
          }

          if ( empty( $menu ) ) {
            $menu = wp_nav_menu(
              array(
                'theme_location' => 'nav_menu',
                'menu_class' => 'main-navigation__list',
                'container' => '',
                'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                'echo' => false
              )
            );

            if ( $menu !== false ) {
              $menu = str_replace( 'class="', 'data-menu-id="main" class="', $menu );
              $menu = str_replace( 'current_page_item', '', $menu );
              $menu = str_replace( 'current-menu-item', '', $menu );
              $menu = str_replace( 'aria-current="page"', '', $menu );
            }

            if ( FICTIONEER_ENABLE_MENU_TRANSIENTS ) {
              set_transient( 'fictioneer_main_nav_menu', $menu );
            }
          }

          echo $menu;
        }
      ?>
    </div>

    <div class="main-navigation__right">
      <?php get_template_part( 'partials/_icon-menu', null, array( 'location' => 'in-navigation' ) ); ?>
    </div>

  </div>

  <?php do_action( 'fictioneer_navigation_bottom', $args ); ?>

</nav>
