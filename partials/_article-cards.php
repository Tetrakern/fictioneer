<?php
/**
 * Partial: Article Cards
 *
 * Renders the (buffered) HTML for the [fictioneer_article_cards] shortcode.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.7.3
 *
 * @internal $args['uid']                 Unique ID of the shortcode. Pattern: shortcode-id-{id}.
 * @internal $args['post_type']           Array of post types to query. Default 'post'.
 * @internal $args['ignore_sticky']       Whether to ignore sticky flags. Default false.
 * @internal $args['ignore_protected']    Whether to ignore protected posts. Default false.
 * @internal $args['only_protected']      Whether to query only protected posts. Default false.
 * @internal $args['count']               Number of posts to display. Default -1.
 * @internal $args['author']              Author to query posts for. Default empty.
 * @internal $args['order']               Order of posts. Default 'DESC'.
 * @internal $args['orderby']             Sorting of posts. Default 'date'.
 * @internal $args['page']                The current page. Default 1.
 * @internal $args['post_ids']            Array of post IDs. Default empty.
 * @internal $args['author_ids']          Array of author IDs. Default empty.
 * @internal $args['excluded_authors']    Array of author IDs to exclude. Default empty.
 * @internal $args['excluded_cats']       Array of category IDs to exclude. Default empty.
 * @internal $args['excluded_tags']       Array of tag IDs to exclude. Default empty.
 * @internal $args['taxonomies']          Array of taxonomy arrays. Default empty.
 * @internal $args['relation']            Relationship between taxonomies. Default 'AND'.
 * @internal $args['lightbox']            Whether the image is opened in the lightbox. Default true.
 * @internal $args['thumbnail']           Whether the image is rendered. Default true.
 * @internal $args['terms']               Either inline, pills, none, or false. Default inline.
 * @internal $args['max_terms']           Maximum number of shown taxonomies. Default 10.
 * @internal $args['date_format']         String to override the date format. Default empty.
 * @internal $args['footer']              Whether to show the footer. Default true.
 * @internal $args['footer_author']       Whether to show the post author. Default true.
 * @internal $args['footer_date']         Whether to show the post date. Default true.
 * @internal $args['footer_comments']     Whether to show the post comment count. Default true.
 * @internal $args['classes']             String of additional CSS classes. Default empty.
 * @internal $args['splide']              Configuration JSON for the Splide slider. Default empty.
 */


// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$splide = $args['splide'] ?? 0;
$show_terms = ! in_array( $args['terms'], ['none', 'false'] );

// Arguments
$query_args = array(
  'fictioneer_query_name' => 'article_cards',
  'post_type' => $args['post_type'],
  'post_status' => 'publish',
  'post__in' => $args['post_ids'], // May be empty!
  'order' => $args['order'],
  'orderby' => $args['orderby'],
  'ignore_sticky_posts' => $args['ignore_sticky'],
  'update_post_term_cache' => $show_terms // Improve performance
);

// Pagination or count?
if ( $args['count'] > 0 ) {
  $query_args['posts_per_page'] = $args['count'];
  $query_args['no_found_rows'] = true;
} else {
  $query_args['paged'] = $args['page'];
  $query_args['posts_per_page'] = $args['posts_per_page'];
}

// Author?
if ( ! empty( $args['author'] ) ) {
  $query_args['author_name'] = $args['author'];
}

// Author IDs?
if ( ! empty( $args['author_ids'] ) ) {
  $query_args['author__in'] = $args['author_ids'];
}

// Excluded authors?
if ( ! empty( $args['excluded_authors'] ) ) {
  $query_args['author__not_in'] = $args['excluded_authors'];
}

// Taxonomies?
if ( ! empty( $args['taxonomies'] ) ) {
  $query_args['tax_query'] = fictioneer_get_shortcode_tax_query( $args );
}

// Excluded categories?
if ( ! empty( $args['excluded_cats'] ) ) {
  $query_args['category__not_in'] = $args['excluded_cats'];
}

// Excluded tags?
if ( ! empty( $args['excluded_tags'] ) ) {
  $query_args['tag__not_in'] = $args['excluded_tags'];
}

// Ignore protected?
if ( ! $args['ignore_protected'] || $args['only_protected'] ) {
  $obfuscation = str_repeat( _x( '&#183; ', 'Protected post content obfuscation character.', 'fictioneer' ), 256 );
  $obfuscation = apply_filters( 'fictioneer_filter_obfuscation_string', $obfuscation, $post );
} else {
  $query_args['has_password'] = false;
}

// Only protected?
if ( $args['only_protected'] ) {
  $query_args['has_password'] = true;
}

// Apply filters
$query_args = apply_filters( 'fictioneer_filter_shortcode_article_cards_query_args', $query_args, $args );

// Query
$query = fictioneer_shortcode_query( $query_args );

// Unique ID
$unique_id = wp_unique_id( 'article-block-' );

// Pagination
$pag_args = array(
  'current' => max( 1, $args['page'] ),
  'total' => $query->max_num_pages,
  'prev_text' => fcntr( 'previous' ),
  'next_text' => fcntr( 'next' ),
  'add_fragment' => "#{$unique_id}"
);

// Extra attributes
$attributes = [];

if ( $splide ) {
  $attributes[] = "data-splide='{$splide}'";
}

?>

<section id="<?php echo $unique_id; ?>" class="scroll-margin-top article-card-block <?php echo esc_attr( $args['classes'] ); ?>" <?php echo implode( ' ', $attributes ); ?>>
  <?php
    if ( $args['splide'] === false ) {
      echo '<div class="shortcode-json-invalid">' . __( 'Splide JSON is invalid and has been ignored.', 'fictioneer' ) . '</div>';
    }

    if ( $splide ) {
      echo '<div class="splide__track">';
    }
  ?>

    <?php if ( $query->have_posts() ) : ?>

      <ul class="grid-columns <?php if ( $splide ) { echo 'splide__list'; } ?>">
        <?php
          while ( $query->have_posts() ) {
            $query->the_post();

            // Setup
            $post_id = $post->ID;
            $story_id = ( $post->post_type === 'fcn_story' ) ? $post_id : null;
            $title = fictioneer_get_safe_title( $post_id, 'card-article' );
            $permalink = get_permalink();
            $categories = $show_terms ? get_the_terms( $post_id, 'category' ) : [];
            $tags = $show_terms ? get_the_tags() : [];
            $fandoms = $show_terms ? get_the_terms( $post, 'fcn_fandom' ) : [];
            $characters = $show_terms ? get_the_terms( $post, 'fcn_character' ) : [];
            $genres = $show_terms ? get_the_terms( $post, 'fcn_genre' ) : [];
            $card_classes = [];

            // Chapter story?
            if ( $post->post_type === 'fcn_chapter' ) {
              $story_id = get_post_meta( $post_id, 'fictioneer_chapter_story', true );
            }

            // Extra classes
            if ( get_theme_mod( 'card_style', 'default' ) !== 'default' ) {
              $card_classes[] = '_' . get_theme_mod( 'card_style' );
            }

            if ( $args['seamless'] ) {
              $card_classes[] = '_seamless';
            }

            if ( ! $show_terms ) {
              $card_classes[] = '_no-tax';
            }

            if ( ! $args['footer'] ) {
              $card_classes[] = '_no-footer';
            }

            if ( ! $args['footer_author'] ) {
              $card_classes[] = '_no-footer-author';
            }

            if ( ! $args['footer_date'] ) {
              $card_classes[] = '_no-footer-date';
            }

            if ( ! $args['footer_comments'] ) {
              $card_classes[] = '_no-footer-comments';
            }

            if ( $splide ) {
              $card_classes[] = 'splide__slide';
            }

            // Card attributes
            $attributes = [];

            if ( $args['aspect_ratio'] ) {
              $attributes['style'] = '--card-image-aspect-ratio: ' . $args['aspect_ratio'];
            }

            $attributes = apply_filters( 'fictioneer_filter_card_attributes', $attributes, $post, 'shortcode-article-cards' );

            $card_attributes = '';

            foreach ( $attributes as $key => $value ) {
              $card_attributes .= esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
            }

            // Start HTML ---> ?>
            <li class="post-<?php echo $post_id; ?> card _article <?php echo implode( ' ', $card_classes ); ?>" <?php echo $card_attributes; ?>>
              <article class="card__body _article polygon">

                <div class="card__main _article">

                  <?php
                    if ( $args['thumbnail'] ) {
                      fictioneer_render_thumbnail(
                        array(
                          'post_id' => $post_id,
                          'title' => $title,
                          'classes' => 'card__image _article cell-img',
                          'permalink' => $permalink,
                          'lightbox' => $args['lightbox'],
                          'vertical' => 1,
                          'seamless' => $args['seamless'],
                          'aspect_ratio' => $args['aspect_ratio'] ?: '3/1'
                        )
                      );
                    }
                  ?>

                  <h3 class="card__title cell-title _article _small"><a href="<?php the_permalink(); ?>" class="truncate _1-1"><?php echo $title; ?></a></h3>

                  <?php if ( post_password_required() ) : ?>
                    <div class="card__content _small _article cell-desc"><div class="truncate _5-5"><span><?php echo $obfuscation; ?></span></div></div>
                  <?php else : ?>
                    <div class="card__content _small _article cell-desc"><div class="truncate _5-5"><span><?php
                      echo wp_strip_all_tags( fictioneer_get_excerpt() );
                    ?></span></div></div>
                  <?php endif; ?>

                  <?php if ( $categories || $tags || $genres || $fandoms || $characters ) : ?>
                    <div class="card__tag-list cell-tax _small _scrolling">
                      <div class="card__h-scroll">
                        <?php
                          $variant = $args['terms'] === 'pills' ? '_pill' : '_inline';

                          $terms = array_merge(
                            $categories ? fictioneer_get_term_nodes( $categories, '_inline _category' ) : [],
                            $fandoms ? fictioneer_get_term_nodes( $fandoms, "{$variant} _fandom" ) : [],
                            $genres ? fictioneer_get_term_nodes( $genres, "{$variant} _genre" ) : [],
                            $tags ? fictioneer_get_term_nodes( $tags, "{$variant} _tag" ) : [],
                            $characters ? fictioneer_get_term_nodes( $characters, "{$variant} _character" ) : []
                          );

                          $terms = apply_filters(
                            'fictioneer_filter_shortcode_article_cards_terms',
                            $terms, $post, $args, null
                          );

                          // Implode with separator
                          echo implode(
                            fictioneer_get_bullet_separator( 'article-cards', $args['terms'] === 'pills' ),
                            array_slice( $terms, 0, $args['max_terms'] )
                          );
                        ?>
                      </div>
                    </div>
                  <?php endif; ?>

                  <?php if ( $args['footer'] ) : ?>
                    <div class="card__footer cell-footer _article _small">
                      <div class="card__footer-box text-overflow-ellipsis"><?php
                        // Build footer items
                        $footer_items = [];

                        if ( $args['footer_author'] && get_option( 'fictioneer_show_authors' ) ) {
                          $footer_items['author'] = '<span class="card__footer-author"><i class="card-footer-icon fa-solid fa-circle-user"></i> ' . fictioneer_get_author_node( get_the_author_meta( 'ID' ) ) . '</span>';
                        }

                        if ( $args['footer_date'] ) {
                          $format = $args['date_format'] ?: FICTIONEER_CARD_ARTICLE_FOOTER_DATE;

                          if ( $args['orderby'] === 'modified' ) {
                            $footer_items['modified_date'] = '<span class="card__footer-modified-date"><i class="card-footer-icon fa-regular fa-clock" title="' . esc_attr__( 'Last Updated', 'fictioneer' ) . '"></i> ' . get_the_modified_date( $format, $post ) . '</span>';
                          } else {
                            $footer_items['publish_date'] = '<span class="card__footer-publish-date"><i class="card-footer-icon fa-solid fa-clock" title="' . esc_attr__( 'Published', 'fictioneer' ) .'"></i> ' . get_the_date( $format ) . '</span>';
                          }
                        }

                        if ( $args['footer_comments'] ) {
                          $footer_items['comments'] = '<span class="card__footer-comments"><i class="card-footer-icon fa-solid fa-message" title="' . esc_attr__( 'Comments', 'fictioneer' ) . '"></i> ' . get_comments_number( $post ) . '</span>';
                        }

                        // Filter footer items
                        $footer_items = apply_filters( 'fictioneer_filter_shortcode_article_card_footer', $footer_items, $post );

                        // Implode and render footer items
                        echo implode( ' ', $footer_items );
                      ?></div>
                    </div>
                  <?php endif; ?>

                </div>

              </article>
            </li>
            <?php // <--- End HTML
          }
          wp_reset_postdata();
        ?>
      </ul>

      <?php if ( $splide ) { echo '<ul class="splide__pagination"></ul>'; } ?>

      <?php if ( $args['count'] < 1 && $query->max_num_pages > 1 ) : ?>
        <nav class="pagination"><?php echo fictioneer_paginate_links( $pag_args ); ?></nav>
      <?php endif; ?>

    <?php else : ?>

      <div class="no-results"><?php _e( 'Nothing to show.', 'fictioneer' ); ?></div>

    <?php endif; ?>

  <?php if ( $splide ) { echo '</div>'; } ?>
</section>
