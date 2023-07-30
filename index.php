<?php
/**
 * Index
 *
 * The most basic template primarily used for the blog. Just a list of
 * full-width articles using the loop partial.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.6
 * @see partials/_loop.php
 */
?>

<?php get_header(); ?>

<main id="main" class="main index">
  <div class="observer main-observer"></div>
  <?php do_action( 'fictioneer_main' ); ?>
  <div class="main__background polygon polygon--main background-texture"></div>
  <div class="main__wrapper">
    <?php
      do_action( 'fictioneer_main_wrapper' );
      get_template_part( 'partials/_loop' );
    ?>
  </div>
</main>

<?php
  // Footer arguments
  $footer_args = array(
    'post_type' => null,
    'post_id' => null,
    'breadcrumbs' => array(
      [fcntr( 'frontpage' ), get_home_url()],
      [fcntr( 'blog' ), null]
    )
  );

  // Get footer with breadcrumbs
  get_footer( null, $footer_args );
?>
