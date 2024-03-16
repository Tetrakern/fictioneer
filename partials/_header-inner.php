<?php
/**
 * Partial: Inner Header
 *
 * Renders the inner header.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.0.0
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
$header_style = get_theme_mod( 'header_style', 'default' );
$show_title_shadow = get_theme_mod( 'title_text_shadow', false );
$tag = in_array( $header_style, ['default', 'overlay'] ) ? 'header' : 'div';
$logo_tag = display_header_text() ? 'div' : 'h1';

?>

<<?php echo $tag; ?> class="header hide-on-fullscreen">

  <?php do_action( 'fictioneer_inner_header', $args ); ?>

  <?php if ( in_array( $header_style, ['default', 'overlay'] ) ) : ?>
    <div class="header__content">

      <?php if ( has_custom_logo() ) : ?>
        <<?php echo $logo_tag; ?> class="header__logo"><?php the_custom_logo(); ?></<?php echo $logo_tag; ?>>
      <?php endif; ?>

      <?php if ( display_header_text() ) : ?>
        <div class="header__title <?php if ( ! $show_title_shadow ) echo '_no-text-shadow'; ?>">
          <h1 class="header__title-heading"><a href="<?php echo esc_url( home_url() ); ?>" class="header__title-link" rel="home"><?php echo get_bloginfo( 'name' ); ?></a></h1>
          <?php if ( ! empty( get_bloginfo( 'description' ) ) ) : ?>
            <div class="header__title-tagline"><?php echo get_bloginfo( 'description' ); ?></div>
          <?php endif; ?>
        </div>
      <?php endif; ?>

    </div>
  <?php endif; ?>

</<?php echo $tag; ?>>
