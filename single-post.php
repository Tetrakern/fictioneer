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

  <?php do_action( 'fictioneer_main', 'post' ); ?>

  <div class="main__wrapper">

    <?php do_action( 'fictioneer_main_wrapper' ); ?>

    <?php while ( have_posts() ) : the_post(); ?>

      <?php
        // Setup
        $title = fictioneer_get_safe_title( $post->ID, 'single-post' );
        $this_breadcrumb = [ $title, get_the_permalink() ];
      ?>

      <article id="post-<?php the_ID(); ?>" class="post__article">

        <?php do_action( 'fictioneer_post_article_open', $post->ID, array( 'context' => 'single-post' ) ); ?>

        <header class="post__header">
          <?php
            $header_items = array(
              'title' => '<h1 class="post__title">' . $title . '</h1>',
              'meta' => '<div class="post__meta layout-links">' . fictioneer_get_post_meta_items() . '</div>'
            );

            $header_items = apply_filters(
              'fictioneer_filter_post_header_items',
              $header_items,
              $post->ID,
              array( 'context' => 'single-post' )
            );

            echo implode( '', $header_items );
          ?>
        </header>

        <section class="post__main content-section"><?php the_content(); ?></section>

        <?php do_action( 'fictioneer_post_after_content', $post->ID, array( 'context' => 'single-post' ) ); ?>

        <?php if ( ! $password_required && ( has_action( 'fictioneer_post_footer_left' ) || has_action( 'fictioneer_post_footer_right' ) ) ) : ?>
          <footer class="post__footer">
            <div class="post__footer-box post__footer-left">
              <?php do_action( 'fictioneer_post_footer_left', $post->ID, array( 'context' => 'single-post' ) ); ?>
            </div>
            <div class="post__footer-box post__footer-right">
              <?php do_action( 'fictioneer_post_footer_right', $post->ID, array( 'context' => 'single-post' ) ); ?>
            </div>
          </footer>
        <?php endif; ?>

      </article>

      <?php do_action( 'fictioneer_before_comments' ); ?>

      <?php if ( comments_open() && ! $password_required ) : ?>
        <section class="post__comments comment-section"><?php
          // Allows :empty CSS selector
          comments_template();
        ?></section>
      <?php endif; ?>

    <?php endwhile; ?>

  </div>

  <?php do_action( 'fictioneer_main_end', 'post' ); ?>

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
