<?php
/**
 * Partial: Top Header
 *
 * Renders the top header.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.8.1
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

// Setup
$classes = [];

if ( ! display_header_text() ) {
  $classes[] = '_no-title';
}

if ( empty( get_bloginfo( 'description' ) ) ) {
  $classes[] = '_no-tagline';
}

if ( ! has_custom_logo() ) {
  $classes[] = '_no-logo';
}

if ( ! get_theme_mod( 'title_text_shadow', true ) ) {
  $classes[] = '_no-text-shadow';
}

?>

<header class="top-header <?php echo implode( ' ', $classes ); ?> hide-on-fullscreen">

  <?php do_action( 'fictioneer_top_header', $args ); ?>

  <div class="top-header__content">

    <?php if ( has_custom_logo() ) : ?>
      <?php the_custom_logo(); ?>
    <?php endif; ?>

    <?php if ( display_header_text() ) : ?>
      <div class="top-header__title">
        <h1 class="top-header__heading">
          <a href="<?php echo esc_url( home_url() ); ?>" class="top-header__link" rel="home"><?php
            echo get_bloginfo( 'name' );
          ?></a>
        </h1>
        <?php if ( ! empty( get_bloginfo( 'description' ) ) ) : ?>
          <div class="top-header__tagline"><?php echo get_bloginfo( 'description' ); ?></div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

  </div>
</header>
