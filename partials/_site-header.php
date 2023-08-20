<?php
/**
 * Partial: Site Header
 *
 * Renders the site header.
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

<header class="header hide-on-fullscreen">

  <?php do_action( 'fictioneer_header', $args ); ?>

  <div class="header__content">
    <?php if ( has_custom_logo() ) : ?>

      <div class="header__logo"><?php the_custom_logo(); ?></div>

    <?php elseif ( display_header_text() ) : ?>

      <div class="header__title">
        <h1 class="header__title-heading"><a href="<?php echo esc_url( home_url() ); ?>" class="header__title-link" rel="home"><?php echo FICTIONEER_SITE_NAME; ?></a></h1>
        <?php if ( ! empty( FICTIONEER_SITE_DESCRIPTION ) ) : ?>
          <div class="header__title-tagline"><?php echo FICTIONEER_SITE_DESCRIPTION; ?></div>
        <?php endif; ?>
      </div>

    <?php endif; ?>
  </div>

</header>
