<?php
/**
 * Partial: Latest Post
 *
 * This template part renders the HTML for the [fictioneer_latest_post] shortcode.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0.0
 *
 * @internal $args['type']              Type argument passed from shortcode. Default 'default'.
 * @internal $args['author']            The author provided by the shortcode. Default false.
 * @internal $args['count']             The number of posts provided by the shortcode. Default 1.
 * @internal $args['post_ids']          Array of post IDs. Default empty.
 * @internal $args['author_ids']        Array of author IDs. Default empty.
 * @internal $args['excluded_authors']  Array of author IDs to exclude. Default empty.
 * @internal $args['excluded_cats']     Array of category IDs to exclude. Default empty.
 * @internal $args['excluded_tags']     Array of tag IDs to exclude. Default empty.
 * @internal $args['ignore_protected']  Whether to ignore protected posts. Default false.
 * @internal $args['taxonomies']        Array of taxonomy arrays. Default empty.
 * @internal $args['relation']          Relationship between taxonomies.
 * @internal $args['classes']           Array of additional CSS classes. Default empty.
 */
?>

<?php

// No direct access!
defined( 'ABSPATH' ) OR exit;

// Prepare query
$query_args = array(
  'fictioneer_query_name' => 'latest_posts',
  'post_type' => 'post',
  'post_status' => 'publish',
  'post__in' => $args['post_ids'], // May be empty!
  'order' => 'DESC',
  'orderby' => 'date',
  'posts_per_page' => $args['count'],
  'ignore_sticky_posts' => 1,
  'no_found_rows' => true, // Improve performance
  'update_post_term_cache' => false // Improve performance
);

// Author?
if ( ! empty( $args['author'] ) ) {
  $query_args['author_name'] = $args['author'];
}

// Author IDs?
if ( ! empty( $args['author_ids'] ) ) {
  $query_args['author__in'] = $args['author_ids'];
}

// Taxonomies?
if ( ! empty( $args['taxonomies'] ) ) {
  $query_args['tax_query'] = fictioneer_get_shortcode_tax_query( $args );
}

// Excluded tags?
if ( ! empty( $args['excluded_tags'] ) ) {
  $query_args['tag__not_in'] = $args['excluded_tags'];
}

// Excluded categories?
if ( ! empty( $args['excluded_cats'] ) ) {
  $query_args['category__not_in'] = $args['excluded_cats'];
}

// Excluded authors?
if ( ! empty( $args['excluded_authors'] ) ) {
  $query_args['author__not_in'] = $args['excluded_authors'];
}

// Ignore protected?
if ( $args['ignore_protected'] ) {
  add_filter( 'posts_where', 'fictioneer_exclude_protected_posts' );
}

// Apply filters
$query_args = apply_filters( 'fictioneer_filter_shortcode_latest_posts_query_args', $query_args, $args );

// Query post
$latest_entries = fictioneer_shortcode_query( $query_args );

// Remove temporary filters
remove_filter( 'posts_where', 'fictioneer_exclude_protected_posts' );

?>

<section class="latest-posts <?php echo $args['classes']; ?>">
  <?php if ( $latest_entries->have_posts() ) : ?>

    <?php while ( $latest_entries->have_posts() ) : $latest_entries->the_post(); ?>

      <?php
        // Setup
        $title = fictioneer_get_safe_title( $post->ID, 'shortcode-latest-posts' );
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

      <article id="post-<?php the_ID(); ?>" class="post">

      <header class="post__header">
        <h2 class="post__title"><a href="<?php the_permalink(); ?>"><?php echo $title; ?></a></h2>
        <div class="post__meta layout-links"><?php echo fictioneer_get_post_meta_items( array( 'no_cat' => true ) ); ?></div>
      </header>

      <section class="post__main content-section">
        <div class="post__excerpt"><?php echo $content; ?></div>
      </section>

      <?php if ( has_action( 'fictioneer_latest_posts_footers_left' ) || has_action( 'fictioneer_latest_posts_footers_right' ) ) : ?>
        <footer class="post__footer">
          <div class="post__footer-box post__footer-left">
            <?php do_action( 'fictioneer_latest_posts_footers_left', $post->ID ); ?>
          </div>
          <div class="post__footer-box post__footer-right">
            <?php do_action( 'fictioneer_latest_posts_footers_right', $post->ID ); ?>
          </div>
        </footer>
      <?php endif; ?>

      </article>

    <?php endwhile; ?>

  <?php else : ?>

    <div class="no-results"><?php _e( 'Nothing to show.', 'fictioneer' ); ?></div>

  <?php endif; wp_reset_postdata(); ?>
</section>
