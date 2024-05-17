<?php
/**
 * Single Post
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 1.0
 */


// Setup
$password_required = post_password_required();

// Header
get_header();

?>

<main id="main" class="main post">

  <div class="observer main-observer"></div>

  <?php do_action( 'fictioneer_main' ); ?>

  <div class="main__background polygon polygon--main background-texture"></div>

  <div class="main__wrapper">

    <?php do_action( 'fictioneer_main_wrapper' ); ?>

    <?php while ( have_posts() ) : the_post(); ?>

      <?php
        // Setup
        $title = fictioneer_get_safe_title( $post->ID, 'single-post' );
        $this_breadcrumb = [ $title, get_the_permalink() ];
      ?>

      <article id="post-<?php the_ID(); ?>" class="post__article padding-left padding-right padding-top padding-bottom">

        <header class="post__header">
          <h1 class="post__title"><?php echo $title; ?></h1>
          <div class="post__meta layout-links"><?php echo fictioneer_get_post_meta_items(); ?></div>
        </header>

        <section class="post__main content-section"><?php the_content(); ?></section>

        <?php do_action( 'fictioneer_post_after_content', $post->ID ); ?>

        <?php if ( ! $password_required && ( has_action( 'fictioneer_post_footer_left' ) || has_action( 'fictioneer_post_footer_right' ) ) ) : ?>
          <footer class="post__footer">
            <div class="post__footer-box post__footer-left">
              <?php do_action( 'fictioneer_post_footer_left', $post->ID ); ?>
            </div>
            <div class="post__footer-box post__footer-right">
              <?php do_action( 'fictioneer_post_footer_right', $post->ID ); ?>
            </div>
          </footer>
        <?php endif; ?>

      </article>

      <?php do_action( 'fictioneer_before_comments' ); ?>

      <?php if ( comments_open() && ! $password_required ) : ?>
        <section class="post__comments comment-section padding-left padding-right padding-bottom">
          <?php comments_template(); ?>
        </section>
      <?php endif; ?>

    <?php endwhile; ?>

  </div>

</main>

<?php
  // Footer arguments
  $footer_args = array(
    'post_type' => 'post',
    'post_id' => $post->ID,
    'breadcrumbs' => array(
      [fcntr( 'frontpage' ), get_home_url()]
    )
  );

  // Add current breadcrumb
  $footer_args['breadcrumbs'][] = $this_breadcrumb;

  // Get footer with breadcrumbs
  get_footer( null, $footer_args );
?>
