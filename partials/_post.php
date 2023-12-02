<?php
/**
 * Partial: Post
 *
 * Renders a list of full-width posts paginated using the "Blog pages show at most"
 * (posts_per_page) option under Settings > Reading.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 2.0
 * @see partials/_loop.php
 *
 * @internal $args['nested']  Whether the post is nested inside another query. Default null.
 */
?>

<?php

// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$post_id = get_the_ID();
$title = fictioneer_get_safe_title( $post_id );
$label = esc_attr( sprintf( _x( 'Continue reading %s', 'Read more link aria label', 'fictioneer' ), $title ) );
$nested = $args['nested'] ?? false;

if (
  ! get_option( 'fictioneer_show_full_post_content' ) &&
  ! strpos( $post->post_content, '<!--more-->' )
) {
  $content = '<p>' . fictioneer_get_excerpt() . '</p><a class="more-link" href="' . get_permalink() . '" title="' . $label . '" aria-label="' . $label . '">' . fcntr( 'read_more' ) . '</a>';
} else {
  $content = apply_filters( 'the_content', get_the_content( fcntr( 'read_more' ) ) );
}

?>

<article id="post-<?php the_ID(); ?>" class="post <?php if ( ! $nested ) echo 'padding-left padding-right'; ?>">

  <header class="post__header">
    <h2 class="post__title"><a href="<?php the_permalink(); ?>"><?php echo $title; ?></a></h2>
    <div class="post__meta layout-links"><?php echo fictioneer_get_post_meta_items(); ?></div>
  </header>

  <section class="post__main content-section"><?php echo $content; ?></section>

  <?php if ( has_action( 'fictioneer_blog_posts_footers_left' ) || has_action( 'fictioneer_blog_posts_footers_right' ) ) : ?>
    <footer class="post__footer">
      <div class="post__footer-box post__footer-left">
        <?php do_action( 'fictioneer_blog_posts_footers_left', $post_id ); ?>
      </div>
      <div class="post__footer-box post__footer-right">
        <?php do_action( 'fictioneer_blog_posts_footers_right', $post_id ); ?>
      </div>
    </footer>
  <?php endif; ?>

</article>
