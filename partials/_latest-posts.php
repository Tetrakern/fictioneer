<?php
/**
 * Partial: Latest Post
 *
 * This template part renders the HTML for the [fictioneer_latest_post] shortcode.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0
 *
 * @internal $args['author']     The author provided by the shortcode. Default false.
 * @internal $args['count']      The number of posts provided by the shortcode. Default 1.
 * @internal $args['post_ids']   Array of post IDs. Default empty.
 * @internal $args['taxonomies'] Array of taxonomy arrays. Default empty.
 * @internal $args['relation']   Relationship between taxonomies.
 * @internal $args['classes']    Array of additional CSS classes. Default empty.
 */
?>

<?php

// Prepare query
$query_args = array(
  'post_type' => 'post',
  'post_status' => 'publish',
  'post__in' => $args['post_ids'],
  'has_password' => false,
  'orderby' => 'date',
  'order' => 'DESC',
  'numberposts' => $args['count'],
  'posts_per_page' => $args['count'],
  'ignore_sticky_posts' => 1,
  'no_found_rows' => true
);

// Parameter for author?
if ( isset( $args['author'] ) && $args['author'] ) $query_args['author_name'] = $args['author'];

// Taxonomies?
if ( ! empty( $args['taxonomies'] ) ) {
  $query_args['tax_query'] = fictioneer_get_shortcode_tax_query( $args );
}

// Apply filters
$query_args = apply_filters( 'fictioneer_filter_latest_posts_query_args', $query_args, $args );

// Query post
$latest_entries = new WP_Query( $query_args );

?>

<section class="latest-posts <?php echo implode( ' ', $args['classes'] ); ?>">
  <?php if ( $latest_entries->have_posts() ) : ?>

    <?php while ( $latest_entries->have_posts() ) : $latest_entries->the_post(); ?>

      <?php
        // Setup
        $title = fictioneer_get_safe_title( get_the_ID() );
        $content = apply_filters( 'the_content', get_the_content( fcntr( 'read_more' ) ) );
        $label = esc_attr( sprintf( _x( 'Continue reading %s', 'Read more link aria label', 'fictioneer' ), $title ) );

        if (
          ! get_option( 'fictioneer_show_full_post_content' ) &&
          ! strpos( $post->post_content, '<!--more-->' )
        ) {
          $content = '<p>' . fictioneer_get_excerpt() . '</p><a class="more-link" href="' . get_permalink() . '" title="' . $label . '" aria-label="' . $label . '">' . fcntr( 'read_more' ) . '</a>';
        }
      ?>

      <article id="post-<?php the_ID(); ?>" class="post">

      <header class="post__header">
        <h2 class="post__title"><a href="<?php the_permalink(); ?>"><?php echo $title; ?></a></h2>
        <div class="post__meta layout-links"><?php echo fictioneer_get_post_meta_items( ['no_cat' => true] ); ?></div>
      </header>

      <section class="post__main content-section">
        <div class="post__excerpt"><?php echo $content; ?></div>
      </section>

      <?php if ( has_action( 'fictioneer_latest_posts_footers_left' ) || has_action( 'fictioneer_latest_posts_footers_right' ) ) : ?>
        <footer class="post__footer">
          <div class="post__footer-left">
            <?php do_action( 'fictioneer_latest_posts_footers_left', get_the_ID() ); ?>
          </div>
          <div class="post__footer-right">
            <?php do_action( 'fictioneer_latest_posts_footers_right', get_the_ID() ); ?>
          </div>
        </footer>
      <?php endif; ?>

      </article>

    <?php endwhile; ?>

  <?php else: ?>

    <div class="no-results"><?php _e( 'No posts have been published yet.', 'fictioneer' ) ?></div>

  <?php endif; wp_reset_postdata(); ?>
</section>
