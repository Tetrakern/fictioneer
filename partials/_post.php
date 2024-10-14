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
 * @internal $args['nested']   Whether the post is nested inside another query. Default null.
 * @internal $args['context']  Render context of the partial ('loop' or 'shortcode_fictioneer_blog').
 */


// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$post_id = get_the_ID();
$title = fictioneer_get_safe_title( $post_id, 'post' );
$label = esc_attr( sprintf( _x( 'Continue reading %s', 'Read more link aria label', 'fictioneer' ), $title ) );

if (
  ! get_option( 'fictioneer_show_full_post_content' ) &&
  ! strpos( $post->post_content, '<!--more-->' )
) {
  $content = '<p>' . fictioneer_get_excerpt() . '</p><div class="more-link-wrapper"><a class="more-link" href="' . get_permalink() . '" title="' . $label . '" aria-label="' . $label . '">' . fcntr( 'read_more' ) . '</a></div>';
} else {
  $content = apply_filters( 'the_content', get_the_content( fcntr( 'read_more' ) ) );
}

?>

<article id="post-<?php echo $post_id; ?>" class="post">

  <?php do_action( 'fictioneer_post_article_open', $post_id, $args ); ?>

  <header class="post__header">
    <?php do_action( 'fictioneer_post_header_open', $post_id, $args ); ?>
    <h2 class="post__title h1"><a href="<?php the_permalink(); ?>"><?php echo $title; ?></a></h2>
    <div class="post__meta layout-links"><?php echo fictioneer_get_post_meta_items(); ?></div>
  </header>

  <section class="post__main content-section"><?php echo $content; ?></section>

  <?php do_action( 'fictioneer_post_after_content', $post_id, $args ); ?>

  <?php if ( has_action( 'fictioneer_post_footer_left' ) || has_action( 'fictioneer_post_footer_right' ) ) : ?>
    <footer class="post__footer">
      <div class="post__footer-box post__footer-left">
        <?php do_action( 'fictioneer_post_footer_left', $post_id, $args ); ?>
      </div>
      <div class="post__footer-box post__footer-right">
        <?php do_action( 'fictioneer_post_footer_right', $post_id, $args ); ?>
      </div>
    </footer>
  <?php endif; ?>

</article>
