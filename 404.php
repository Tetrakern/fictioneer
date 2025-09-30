<?php
/**
 * 404 - FOUR OH FOUR
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.7.0
 */


// Setup
$custom_404 = (int) ( get_option( 'fictioneer_404_page', -1 ) ?: -1 );

// Header
get_header( null, array( 'no_index' => 1 ) );

?>

<main id="main" class="main singular the-404">

  <?php do_action( 'fictioneer_main', '404' ); ?>

  <div class="main__wrapper">

    <?php do_action( 'fictioneer_main_wrapper' ); ?>

    <article class="singular__article">
      <section class="singular__content the-404__content content-section">
        <?php if ( $custom_404 > 0 ) : ?>
          <?php echo apply_filters( 'the_content', get_the_content( null, null, $custom_404 ) ); ?>
        <?php else : ?>
          <div class="the-404__message">
            <h1><?php _ex( '404', 'Heading for the 404 page.', 'fictioneer' ); ?></h1>
          </div>
        <?php endif; ?>
      </section>
    </article>

  </div>

  <?php do_action( 'fictioneer_main_end', '404' ); ?>

</main>

<?php
  // Footer arguments
  $footer_args = array(
    'post_type' => null,
    'post_id' => null,
    'template' => '404.php',
    'breadcrumbs' => array(
      [fcntr( 'frontpage' ), get_home_url()],
      [_x( '404', '404 breadcrumb for the 404 page.', 'fictioneer' ), null]
    )
  );

  // Get footer with breadcrumbs
  get_footer( null, $footer_args );
?>
