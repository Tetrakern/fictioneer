<?php
/**
 * Partial: Latest Stories List
 *
 * Renders the (buffered) HTML for the [fictioneer_latest_stories type="list"] shortcode.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.23.0
 * @see fictioneer_clause_sticky_stories()
 *
 * @internal $args['uid']                 Unique ID of the shortcode. Pattern: shortcode-id-{id}.
 * @internal $args['type']                Type argument passed from shortcode.
 * @internal $args['count']               Number of posts provided by the shortcode.
 * @internal $args['author']              Author provided by the shortcode.
 * @internal $args['order']               Order of posts. Default 'DESC'.
 * @internal $args['orderby']             Sorting of posts. Default 'date'.
 * @internal $args['post_ids']            Array of post IDs. Default empty.
 * @internal $args['author_ids']          Array of author IDs. Default empty.
 * @internal $args['excluded_authors']    Array of author IDs to exclude. Default empty.
 * @internal $args['excluded_cats']       Array of category IDs to exclude. Default empty.
 * @internal $args['excluded_tags']       Array of tag IDs to exclude. Default empty.
 * @internal $args['ignore_protected']    Whether to ignore protected posts. Default false.
 * @internal $args['only_protected']      Whether to query only protected posts. Default false.
 * @internal $args['taxonomies']          Array of taxonomy arrays. Default empty.
 * @internal $args['relation']            Relationship between taxonomies.
 * @internal $args['source']              Whether to show author and story. Default true.
 * @internal $args['vertical']            Whether to show the vertical variant.
 * @internal $args['seamless']            Whether to render the image seamless. Default false (Customizer).
 * @internal $args['aspect_ratio']        Aspect ratio for the image. Only with vertical.
 * @internal $args['lightbox']            Whether the image is opened in the lightbox. Default true.
 * @internal $args['thumbnail']           Whether the image is rendered. Default true (Customizer).
 * @internal $args['terms']               Either inline, pills, none, or false. Default inline.
 * @internal $args['max_terms']           Maximum number of shown taxonomies. Default 10.
 * @internal $args['date_format']         String to override the date format. Default empty.
 * @internal $args['footer']              Whether to show the footer. Default true.
 * @internal $args['footer_author']       Whether to show the story author. Default true.
 * @internal $args['footer_words']        Whether to show the story word count. Default true.
 * @internal $args['footer_date']         Whether to show the story date. Default true.
 * @internal $args['footer_status']       Whether to show the story status. Default true.
 * @internal $args['footer_rating']       Whether to show the story age rating. Default true.
 * @internal $args['classes']             String of additional CSS classes. Default empty.
 * @internal $args['splide']              Configuration JSON for the Splide slider. Default empty.
 */


// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$splide = $args['splide'] ?? 0;
$show_terms = ! in_array( $args['terms'], ['none', 'false'] );
$content_list_style = get_theme_mod( 'content_list_style', 'default' );

// Prepare query
$query_args = array(
  'fictioneer_query_name' => 'latest_stories_list',
  'post_type' => 'fcn_story',
  'post_status' => 'publish',
  'post__in' => $args['post_ids'], // May be empty!
  'order' => $args['order'],
  'orderby' => $args['orderby'],
  'posts_per_page' => $args['count'],
  'update_post_term_cache' => $show_terms, // Improve performance
  'no_found_rows' => true
);

// Use extended meta query?
if ( get_option( 'fictioneer_disable_extended_story_list_meta_queries' ) ) {
  $query_args['meta_key'] = 'fictioneer_story_hidden';
  $query_args['meta_value'] = '0';
} else {
  $query_args['meta_query'] = array(
    'relation' => 'OR',
    array(
      'key' => 'fictioneer_story_hidden',
      'value' => '0'
    ),
    array(
      'key' => 'fictioneer_story_hidden',
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
  $query_args['has_password'] = false;
}

// Only protected?
if ( $args['only_protected'] ) {
  $query_args['has_password'] = true;
}

// Apply filters
$query_args = apply_filters( 'fictioneer_filter_shortcode_latest_stories_query_args', $query_args, $args );

// Query stories
$entries = fictioneer_shortcode_query( $query_args );

// Extra attributes
$attributes = [];

if ( $splide ) {
  $attributes[] = "data-splide='{$splide}'";
}

?>

<section class="latest-stories _list <?php echo $args['classes']; ?>" <?php echo implode( ' ', $attributes ); ?>>
  <?php
    if ( $args['splide'] === false ) {
      echo '<div class="shortcode-json-invalid">' . __( 'Splide JSON is invalid and has been ignored.', 'fictioneer' ) . '</div>';
    }

    if ( $splide ) {
      echo fictioneer_get_splide_breakpoint_style( $splide, $args['uid'] );
      echo fictioneer_get_splide_arrows( $args['uid'], true );
      echo '<div class="splide__track">';
    }
  ?>

    <?php if ( $entries->have_posts() ) : ?>

      <ul class="post-list _latest-stories <?php if ( $splide ) { echo 'splide__list'; } ?>">
        <?php while ( $entries->have_posts() ) : $entries->the_post(); ?>

          <?php
            // Setup
            $post_id = $post->ID;
            $story = fictioneer_get_story_data( $post_id, false ); // Does not refresh comment count!
            $permalink = get_post_meta( $post_id, 'fictioneer_story_redirect_link', true ) ?: get_permalink( $post_id );
            $tags = ( $show_terms && get_option( 'fictioneer_show_tags_on_story_cards' ) ) ? get_the_tags( $post ) : false;
            $is_sticky = FICTIONEER_ENABLE_STICKY_CARDS && get_post_meta( $post_id, 'fictioneer_story_sticky', true );

            // Thumbnail
            $thumbnail = null;

            if ( $args['thumbnail'] ) {
              $thumbnail = fictioneer_render_thumbnail(
                array(
                  'post_id' => $post_id,
                  'title' => $story['title'],
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

            if ( $is_sticky ) {
              $classes[] = '_sticky';
            }

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

            if ( ! $show_terms || ! ( $story['has_taxonomies'] || $tags ) ) {
              $classes[] = '_no-tax';
            }

            if ( ! $args['footer'] ) {
              $classes[] = '_no-footer';
            }

            if ( ! $args['footer_author'] && ! $args['source'] ) {
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

            if ( $splide ) {
              $classes[] = 'splide__slide';
            }

            // Meta
            $meta = [];

            if ( $story['word_count'] > 0 && $args['footer_words'] ) {
              $meta['words'] = '<span class="post-item-item__meta-words">' . sprintf(
                _x( '%s&nbsp;Words', 'Word count in Latest * shortcode (type: list).', 'fictioneer' ),
                number_format_i18n( $story['word_count'] )
              ) . '</span>';
            }

            if ( $args['footer_status'] ) {
              $meta['status'] = '<span class="post-item-item__meta-status">' . fcntr( $story['status'] ) . '</span>';
            }

            if ( $story['rating'] && $args['footer_rating'] ) {
              $meta['rating'] = '<span class="post-item-item__meta-rating">' . fcntr( $story['rating'] ) . '</span>';
            }

            if ( get_option( 'fictioneer_show_authors' ) && $args['footer_author'] && $args['source'] ) {
              $author = fictioneer_get_author_node( $post->post_author, 'post-item-item__meta-author' );

              if ( $author ) {
                $meta['author'] = $author;
              }
            }

            if ( $args['footer_date'] ) {
              $meta['publish_date'] = '<span class="post-item-item__meta-publish-date _floating-right">' . get_the_date( $args['date_format'], $post ) . '</span>';
            }

            $meta = apply_filters( 'fictioneer_filter_shortcode_latest_stories_list_meta', $meta, $post, $story );

            // Attributes
            $attributes = [];

            if ( $args['aspect_ratio'] ) {
              $attributes['style'] = '--post-item-image-aspect-ratio: ' . $args['aspect_ratio'];
            }

            $attributes = apply_filters( 'fictioneer_filter_shortcode_list_attributes', $attributes, $post, 'latest-stories' );

            $output_attributes = '';

            foreach ( $attributes as $key => $value ) {
              $output_attributes .= esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
            }

          ?>

          <li class="post-<?php echo $post_id; ?> post-list-item _latest-stories <?php echo implode( ' ', $classes ); ?>" <?php echo $output_attributes; ?>>

            <?php
              if ( $thumbnail ) {
                echo $thumbnail;
              }
            ?>

            <a href="<?php echo $permalink; ?>" class="post-list-item__title _link"><?php
              $html_title = empty( $post->post_password ) ? '' : '<i class="fa-solid fa-lock protected-icon"></i> ';
              $html_title .= $story['title'];

              echo apply_filters( 'fictioneer_filter_shortcode_list_title', $story['title'], $post, 'shortcode-latest-updates-list' );
            ?></a>

            <?php if ( $args['footer'] ) : ?>
              <div class="post-list-item__meta pseudo-separator"><?php echo implode( ' ', $meta ); ?></div>
            <?php endif; ?>

            <?php if ( $show_terms && ( $story['has_taxonomies'] || $tags ) ) : ?>
              <div class="post-list-item__tax <?php echo $args['terms'] === 'pills' ? '_pills' : ''; ?>"><?php
                $variant = $args['terms'] === 'pills' ? '' : '_inline';

                $terms = array_merge(
                  $story['fandoms'] ? fictioneer_get_term_nodes( $story['fandoms'], "{$variant} _fandom" ) : [],
                  $story['genres'] ? fictioneer_get_term_nodes( $story['genres'], "{$variant} _genre" ) : [],
                  $tags ? fictioneer_get_term_nodes( $tags, "{$variant} _tag" ) : [],
                  $story['characters'] ? fictioneer_get_term_nodes( $story['characters'], "{$variant} _character" ) : []
                );

                $terms = apply_filters(
                  'fictioneer_filter_shortcode_latest_stories_terms',
                  $terms, $post, $args, $story
                );

                // Implode with separator
                echo implode(
                  fictioneer_get_bullet_separator( 'latest-stories-list', $args['terms'] === 'pills' ),
                  array_slice( $terms, 0, $args['max_terms'] )
                );
              ?></div>
            <?php endif; ?>

          </li>

        <?php endwhile; ?>
      </ul>

    <?php else : ?>

      <div class="no-results"><?php _e( 'Nothing to show.', 'fictioneer' ); ?></div>

    <?php endif; wp_reset_postdata(); ?>

  <?php if ( $splide ) { echo '</div>'; } ?>
</section>
