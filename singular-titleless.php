<?php
/**
 * Template Name: No Title Page
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 1.0
 * @see comments.php
 */


// Setup
$post_id = get_the_ID();

// Header
get_header();

?>

<main id="main" class="main singular">

  <?php do_action( 'fictioneer_main', 'singular-titleless' ); ?>

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

      <?php do_action( 'fictioneer_before_comments' ); ?>

      <?php if ( comments_open() && ! post_password_required() ) : ?>
        <section class="singular__comments comment-section"><?php
          // Allows :empty CSS selector
          comments_template();
        ?></section>
      <?php endif; ?>

    <?php endwhile; ?>

  </div>

  <?php do_action( 'fictioneer_main_end', 'singular-titleless' ); ?>

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
