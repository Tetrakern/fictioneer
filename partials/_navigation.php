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
 * @internal $args['post_id']          Current post ID or null.
 * @internal $args['story_id']         Current story ID (if chapter) or null.
 * @internal $args['header_image_url'] URL of the filtered header image or false.
 * @internal $args['header_args']      Arguments passed to the header.php partial.
 */
?>

<nav id="full-navigation" class="main-navigation" aria-label="Main Navigation">
  <div id="nav-observer-sticky" class="nav-observer"></div>
  <div class="main-navigation__background"></div>
  <div class="main-navigation__wrapper">
    <div class="main-navigation__left">
      <?php
        wp_nav_menu(
          array(
            'theme_location' => 'nav_menu',
            'menu_class' => 'main-navigation__list',
            'container' => '',
            'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>'
          )
        );
      ?>
    </div>
    <div class="main-navigation__right">
      <?php get_template_part( 'partials/_icon-menu', null, ['location' => 'in-navigation'] ); ?>
      <label for="mobile-menu-toggle" class="mobile-menu-button follows-alert-number">
        <?php fictioneer_icon( 'fa-bars', 'off' ); ?>
        <?php fictioneer_icon( 'fa-xmark', 'on' ); ?>
      </label>
    </div>
  </div>
</nav>
