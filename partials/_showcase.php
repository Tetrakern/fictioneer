<?php
/**
 * Partial: Showcase
 *
 * This template part renders a showcase of posts and can be called with the
 * [fictioneer_showcase for="post_type" count order orderby author post_ids]
 * shortcode. Posts are displayed as grid with a maximum of three columns,
 * collapsing to one on mobile.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0.0
 *
 * @internal $args['post_type']         Post type if the showcase.
 * @internal $args['count']             Maximum number of items. Default 8.
 * @internal $args['order']             Order direction. Default 'DESC'.
 * @internal $args['orderby']           Order argument. Default 'date'.
 * @internal $args['author']            Author provided by the shortcode.
 * @internal $args['post_ids']          Array of post IDs. Default empty.
 * @internal $args['author_ids']        Array of author IDs. Default empty.
 * @internal $args['excluded_authors']  Array of author IDs to exclude. Default empty.
 * @internal $args['excluded_cats']     Array of category IDs to exclude. Default empty.
 * @internal $args['excluded_tags']     Array of tag IDs to exclude. Default empty.
 * @internal $args['ignore_protected']  Whether to ignore protected posts. Default false.
 * @internal $args['taxonomies']        Array of taxonomy arrays. Default empty.
 * @internal $args['relation']          Relationship between taxonomies.
 * @internal $args['classes']           String of additional CSS classes. Default empty.
 */
?>

<?php

// No direct access!
defined( 'ABSPATH' ) OR exit;

// Prepare query
$query_args = array (
  'fictioneer_query_name' => 'showcase',
  'post_type' => $args['post_type'],
  'post_status' => 'publish',
  'post__in' => $args['post_ids'], // May be empty!
  'order' => $args['order'],
  'orderby' => $args['orderby'],
  'posts_per_page' => $args['count'],
  'update_post_term_cache' => false,
  'no_found_rows' => true
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
$query_args = apply_filters( 'fictioneer_filter_shortcode_showcase_query_args', $query_args, $args );

// Query collections
$query = fictioneer_shortcode_query( $query_args );

// Remove temporary filters
remove_filter( 'posts_where', 'fictioneer_exclude_protected_posts' );

?>

<?php if ( $query->have_posts() ) : ?>
  <section class="showcase container-inline-size <?php echo $args['classes']; ?>">
    <ul class="showcase__list">
      <?php while ( $query->have_posts() ) : $query->the_post(); ?>
        <li class="showcase__list-item">
          <a class="showcase__list-item-link polygon" href="<?php the_permalink(); ?>">
            <figure class="showcase__list-item-figure">
              <?php
                // Setup
                $list_title = '';
                $story_id = null;
                $landscape_image_id = get_post_meta( $post->ID, 'fictioneer_landscape_image', true );

                // Get list title and story ID (if any)
                switch ( $args['post_type'] ) {
                  case 'fcn_collection':
                    $list_title = get_post_meta( $post->ID, 'fictioneer_collection_list_title', true );
                    break;
                  case 'fcn_chapter':
                    $list_title = get_post_meta( $post->ID, 'fictioneer_chapter_list_title', true );
                    $story_id = get_post_meta( $post->ID, 'fictioneer_chapter_story', true );

                    if ( empty( $landscape_image_id ) ) {
                      $landscape_image_id = get_post_meta( $story_id, 'fictioneer_landscape_image', true );
                    }

                    break;
                }

                // Prepare titles
                $list_title = trim( wp_strip_all_tags( $list_title ) );
                $title = empty( $list_title ) ? fictioneer_get_safe_title( $post->ID ) : $list_title;

                // Prepare image arguments
                $image_args = array(
                  'alt' => sprintf( __( '%s Cover', 'fictioneer' ), $title ),
                  'class' => 'no-auto-lightbox showcase__image'
                );

                // Output image or placeholder
                if ( ! empty( $landscape_image_id ) ) {
                  echo wp_get_attachment_image( $landscape_image_id, 'medium', false, $image_args );
                } elseif ( has_post_thumbnail() ) {
                  the_post_thumbnail( 'medium', $image_args );
                } elseif ( $story_id && has_post_thumbnail( $story_id ) ) {
                  echo get_the_post_thumbnail( $story_id, 'medium', $image_args );
                } else {
                  echo '<div class="showcase__image _no-cover"></div>';
                }
              ?>
              <?php if ( ! $args['no_cap'] ) : ?>
                <figcaption class="showcase__list-item-figcaption"><?php echo $title; ?></figcaption>
              <?php endif; ?>
            </figure>
          </a>
        </li>
      <?php endwhile; ?>
    </ul>
  </section>
<?php endif; wp_reset_postdata(); ?>
