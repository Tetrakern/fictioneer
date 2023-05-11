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
 * @internal $args['post_id']          Current post ID or null.
 * @internal $args['story_id']         Current story ID (if chapter) or null.
 * @internal $args['header_image_url'] URL of the filtered header image or false.
 * @internal $args['header_args']      Arguments passed to the header.php partial.
 */
?>

<?php

$site_name = FICTIONEER_SITE_NAME ?: get_bloginfo( 'name' );
$site_description = FICTIONEER_SITE_DESCRIPTION ?: get_bloginfo( 'description' );

?>

<header class="header hide-on-fullscreen">

  <?php do_action( 'fictioneer_header', $args ); ?>

  <div class="header__content">
    <?php if ( has_custom_logo() ) : ?>

      <div class="header__logo"><?php the_custom_logo(); ?></div>

    <?php elseif ( display_header_text() ) : ?>

      <div class="header__title">
        <h1 class="header__title-heading"><a href="<?php echo esc_url( home_url() ); ?>" class="header__title-link" rel="home"><?php echo $site_name; ?></a></h1>
        <?php if ( ! empty( $site_description ) ) : ?>
          <div class="header__title-tagline"><?php echo $site_description; ?></div>
        <?php endif; ?>
      </div>

    <?php endif; ?>
  </div>

</header>
