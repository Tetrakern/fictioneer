<?php
/**
 * Partial: Latest Chapters List
 *
 * Renders the (buffered) HTML for the [fictioneer_latest_chapters type="list"] shortcode.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.23.0
 *
 * @internal $args['type']                Type argument passed from shortcode. Default 'default'.
 * @internal $args['count']               Number of posts provided by the shortcode.
 * @internal $args['author']              Author provided by the shortcode.
 * @internal $args['order']               Order of posts. Default 'DESC'.
 * @internal $args['orderby']             Sorting of posts. Default 'date'.
 * @internal $args['spoiler']             Whether to obscure or show chapter excerpt.
 * @internal $args['post_ids']            Array of post IDs. Default empty.
 * @internal $args['author_ids']          Array of author IDs. Default empty.
 * @internal $args['excluded_authors']    Array of author IDs to exclude. Default empty.
 * @internal $args['excluded_cats']       Array of category IDs to exclude. Default empty.
 * @internal $args['excluded_tags']       Array of tag IDs to exclude. Default empty.
 * @internal $args['ignore_protected']    Whether to ignore protected posts. Default false.
 * @internal $args['taxonomies']          Array of taxonomy arrays. Default empty.
 * @internal $args['relation']            Relationship between taxonomies.
 * @internal $args['spoiler']             Whether to obscure or show chapter excerpt.
 * @internal $args['source']              Whether to show author and story.
 * @internal $args['simple']              Whether to show the simple variant.
 * @internal $args['vertical']            Whether to show the vertical variant.
 * @internal $args['seamless']            Whether to render the image seamless. Default false (Customizer).
 * @internal $args['aspect_ratio']        Aspect ratio for the image. Only with vertical.
 * @internal $args['lightbox']            Whether the image is opened in the lightbox. Default true.
 * @internal $args['thumbnail']           Whether the image is rendered. Default true (Customizer).
 * @internal $args['date_format']         String to override the date format. Default empty.
 * @internal $args['footer']              Whether to show the footer. Default true.
 * @internal $args['footer_author']       Whether to show the chapter author. Default true.
 * @internal $args['footer_words']        Whether to show the chapter word count. Default true.
 * @internal $args['footer_date']         Whether to show the chapter date. Default true.
 * @internal $args['footer_status']       Whether to show the chapter story status. Default true.
 * @internal $args['footer_rating']       Whether to show the story/chapter age rating. Default true.
 * @internal $args['classes']             String of additional CSS classes. Default empty.
 */


// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$render_count = 0;
$content_list_style = get_theme_mod( 'content_list_style', 'default' );

// Prepare query
$query_args = array(
  'fictioneer_query_name' => 'latest_chapters_list',
  'post_type' => 'fcn_chapter',
  'post_status' => 'publish',
  'post__in' => $args['post_ids'], // May be empty!
  'orderby' => $args['orderby'],
  'order' => $args['order'],
  'posts_per_page' => $args['count'] + 8, // Little buffer in case of unpublished parent story
  'no_found_rows' => true,
  'update_post_term_cache' => false
);

// Use extended meta query?
if ( get_option( 'fictioneer_disable_extended_chapter_list_meta_queries' ) ) {
  $query_args['meta_key'] = 'fictioneer_chapter_hidden';
  $query_args['meta_value'] = '0';
} else {
  $query_args['meta_query'] = array(
    'relation' => 'OR',
    array(
      'key' => 'fictioneer_chapter_hidden',
      'value' => '0'
    ),
    array(
      'key' => 'fictioneer_chapter_hidden',
      'compare' => 'NOT EXISTS'
    )
  );
}

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
$query_args = apply_filters( 'fictioneer_filter_shortcode_latest_chapters_query_args', $query_args, $args );

// Query chapters
$entries = fictioneer_shortcode_query( $query_args );

// Remove temporary filters
remove_filter( 'posts_where', 'fictioneer_exclude_protected_posts' );

?>

<section class="latest-chapters _list <?php echo $args['classes']; ?>">
  <?php if ( $entries->have_posts() ) : ?>

    <ul class="post-list _latest-chapters">
      <?php while ( $entries->have_posts() ) : $entries->the_post(); ?>

        <?php
          // Setup
          $post_id = $post->ID;
          $story_id = get_post_meta( $post_id, 'fictioneer_chapter_story', true );
          $story = $story_id ? fictioneer_get_story_data( $story_id, false ) : null; // Does not refresh comment count!

          if ( get_post_status( $story_id ) !== 'publish' || ! $story ) {
            continue;
          }

          // Count actually rendered items to account for buffer
          if ( ++$render_count > $args['count'] ) {
            break;
          }

          // Continue setup
          $title = fictioneer_get_safe_title( $post_id, 'shortcode-latest-chapters-list' );
          $permalink = get_permalink( $post_id );
          $chapter_rating = get_post_meta( $post_id, 'fictioneer_chapter_rating', true );
          $words = fictioneer_get_word_count( $post_id );

          // Thumbnail
          $thumbnail = null;

          if ( $args['thumbnail'] ) {
            $thumbnail = fictioneer_render_thumbnail(
              array(
                'post_id' => $post_id,
                'title' => $title,
                'permalink' => $permalink,
                'size' => 'snippet',
                'classes' => 'post-list-item__image',
                'lightbox' => $args['lightbox'],
                'aspect_ratio' => $args['aspect_ratio'] ?? '2/2.5'
              ),
              false
            );
          }

          // Extra classes
          $classes = [];

          if ( ! empty( $post->post_password ) ) {
            $classes[] = '_password';
          }

          if ( $args['seamless'] ) {
            $classes[] = '_seamless';
          }

          if ( $content_list_style !== 'default' ) {
            $classes[] = "_{$content_list_style}";
          }

          if ( ! $thumbnail ) {
            $classes[] = '_no-thumbnail';
          }

          if ( ! $args['footer'] ) {
            $classes[] = '_no-footer';
          }

          if ( ! $args['footer_author'] ) {
            $classes[] = '_no-footer-author';
          }

          if ( ! $args['footer_words'] ) {
            $classes[] = '_no-footer-words';
          }

          if ( ! $args['footer_date'] ) {
            $classes[] = '_no-footer-date';
          }

          if ( ! $args['footer_status'] ) {
            $classes[] = '_no-footer-status';
          }

          if ( ! $args['footer_rating'] ) {
            $classes[] = '_no-footer-rating';
          }

          // Meta
          $meta = [];

          if ( $args['source'] ) {
            $meta['story'] = sprintf(
              _x(
                '<span class="post-list-item__meta-in-story"><span>in </span><a href="%1$s">%2$s</a></span>',
                'Story in Latest * shortcode (type: list).',
                'fictioneer'
              ),
              get_the_permalink( $story_id ),
              $story['title']
            );
          }

          if ( $words > 0 && $args['footer_words'] ) {
            $meta['words'] = '<span class="post-list-item__meta-words">' . sprintf(
              _x( '%s&nbsp;Words', 'Word count in Latest * shortcode (type: list).', 'fictioneer' ),
              number_format_i18n( $words )
            ) . '</span>';
          }

          if ( $args['footer_status'] ) {
            $meta['status'] = '<span class="post-item-item__meta-status">' . fcntr( $story['status'] ) . '</span>';
          }

          if ( $chapter_rating && $args['footer_rating'] ) {
            $meta['rating'] = '<span class="post-list-item__meta-rating">' . fcntr( $chapter_rating ) . '</span>';
          }

          if ( get_option( 'fictioneer_show_authors' ) && $args['footer_author'] ) {
            $author = fictioneer_get_author_node( null, 'post-list-item__meta-author' );

            if ( $author ) {
              $meta['author'] = $author;
            }
          }

          if ( $args['footer_date'] ) {
            $meta['publish_date'] = '<span class="post-list-item__meta-publish-date _floating-right">' . get_the_date( $args['date_format'], $post ) . '</span>';
          }

          $meta = apply_filters( 'fictioneer_filter_shortcode_latest_chapter_list_meta', $meta, $post, $story );

          // Attributes
          $attributes = [];

          if ( $args['aspect_ratio'] ) {
            $attributes['style'] = '--post-item-image-aspect-ratio: ' . $args['aspect_ratio'];
          }

          $attributes = apply_filters( 'fictioneer_filter_shortcode_list_attributes', $attributes, $post, 'latest-chapters' );

          $output_attributes = '';

          foreach ( $attributes as $key => $value ) {
            $output_attributes .= esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
          }

        ?>

        <li class="post-<?php echo $post_id; ?> post-list-item _latest-chapters <?php echo implode( ' ', $classes ); ?>" <?php echo $output_attributes; ?>>

          <?php
            if ( $thumbnail ) {
              echo $thumbnail;
            }
          ?>

          <a href="<?php echo $permalink; ?>" class="post-list-item__title _link"><?php
            $html_title = empty( $post->post_password ) ? '' : '<i class="fa-solid fa-lock protected-icon"></i> ';
            $html_title .= $title;

            echo apply_filters( 'fictioneer_filter_shortcode_list_title', $html_title, $post, 'shortcode-latest-chapters-list' );
          ?></a>

          <?php if ( $args['footer'] ) : ?>
            <div class="post-list-item__meta _pseudo-separator"><?php echo implode( ' ', $meta ); ?></div>
          <?php endif; ?>

        </li>

      <?php endwhile; ?>
    </ul>

  <?php else : ?>

    <div class="no-results"><?php _e( 'Nothing to show.', 'fictioneer' ); ?></div>

  <?php endif; wp_reset_postdata(); ?>
</section>
