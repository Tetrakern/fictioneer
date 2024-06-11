<?php
/**
 * Template Name: Canvas (Page)
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.20.0
 */


// Setup
$post_id = get_the_ID();

// Header
get_header();

?>

<main id="main" class="main canvas">

  <div class="observer main-observer"></div>

  <?php do_action( 'fictioneer_main' ); ?>

  <div class="main__background polygon polygon--main background-texture"></div>

  <div class="main__wrapper">

    <?php do_action( 'fictioneer_main_wrapper' ); ?>

    <?php while ( have_posts() ) : the_post(); ?>

      <?php
        // Setup
        $title = fictioneer_get_safe_title( $post_id, 'singular-titleless' );
        $this_breadcrumb = [ $title, get_the_permalink() ];
      ?>

      <article id="singular-<?php echo $post_id; ?>" class="singular__article">

        <section class="singular__content content-section"><?php the_content(); ?></section>

        <footer class="singular__footer"><?php do_action( 'fictioneer_singular_footer' ); ?></footer>

      </article>

    <?php endwhile; ?>

  </div>

</main>

<?php
  // Footer arguments
  $footer_args = array(
    'post_type' => 'page',
    'post_id' => $post_id,
    'breadcrumbs' => array(
      [fcntr( 'frontpage' ), get_home_url()]
    )
  );

  // Add current breadcrumb
  $footer_args['breadcrumbs'][] = $this_breadcrumb;

  // Get footer with breadcrumbs
  get_footer( null, $footer_args );
?>
