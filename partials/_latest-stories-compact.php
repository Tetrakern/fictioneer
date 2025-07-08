<?php
/**
 * Partial: Latest Stories Compact
 *
 * Renders the (buffered) HTML for the [fictioneer_latest_stories type="compact"] shortcode.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0.0
 * @see fictioneer_clause_sticky_stories()
 *
 * @internal $args['uid']                 Unique ID of the shortcode. Pattern: shortcode-id-{id}.
 * @internal $args['type']                Type argument passed from shortcode ('compact').
 * @internal $args['count']               Number of posts provided by the shortcode.
 * @internal $args['author']              Author provided by the shortcode.
 * @internal $args['order']               Order of posts. Default 'DESC'.
 * @internal $args['orderby']             Sorting of posts. Default 'date'.
 * @internal $args['post_ids']            Array of post IDs. Default empty.
 * @internal $args['post_status']         Queried post status. Default 'publish'.
 * @internal $args['author_ids']          Array of author IDs. Default empty.
 * @internal $args['excluded_authors']    Array of author IDs to exclude. Default empty.
 * @internal $args['excluded_cats']       Array of category IDs to exclude. Default empty.
 * @internal $args['excluded_tags']       Array of tag IDs to exclude. Default empty.
 * @internal $args['ignore_protected']    Whether to ignore protected posts. Default false.
 * @internal $args['only_protected']      Whether to query only protected posts. Default false.
 * @internal $args['taxonomies']          Array of taxonomy arrays. Default empty.
 * @internal $args['relation']            Relationship between taxonomies.
 * @internal $args['source']              Whether to show the author. Default true.
 * @internal $args['vertical']            Whether to show the vertical variant.
 * @internal $args['seamless']            Whether to render the image seamless. Default false (Customizer).
 * @internal $args['aspect_ratio']        Aspect ratio for the image. Only with vertical.
 * @internal $args['lightbox']            Whether the image is opened in the lightbox. Default true.
 * @internal $args['thumbnail']           Whether the image is rendered. Default true (Customizer).
 * @internal $args['classes']             String of additional CSS classes. Default empty.
 * @internal $args['infobox']             Whether to show the info box and toggle. Default true.
 * @internal $args['terms']               Either inline, pills, none, or false. Default inline.
 * @internal $args['max_terms']           Maximum number of shown taxonomies. Default 10.
 * @internal $args['footer_chapters']     Whether to show the chapter count. Default true.
 * @internal $args['footer_words']        Whether to show the word count. Default true.
 * @internal $args['footer_date']         Whether to show the date. Default true.
 * @internal $args['footer_status']       Whether to show the status. Default true.
 * @internal $args['footer_rating']       Whether to show the age rating. Default true.
 * @internal $args['footer_comments']     Whether to show the post comment count. Default false.
 * @internal $args['splide']              Configuration JSON for the Splide slider. Default empty.
 */


// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$splide = $args['splide'] ?? 0;
$show_terms = ! in_array( $args['terms'], ['none', 'false'] ) &&
  ! get_option( 'fictioneer_hide_taxonomies_on_story_cards' );

// Prepare query
$query_args = array(
  'fictioneer_query_name' => 'latest_stories_compact',
  'post_type' => 'fcn_story',
  'post_status' => $args['post_status'],
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

<section class="small-card-block latest-stories _compact <?php echo $args['classes']; ?>" <?php echo implode( ' ', $attributes ); ?>>
  <?php
    if ( $args['splide'] === false ) {
      echo '<div class="shortcode-json-invalid">' . __( 'Splide JSON is invalid and has been ignored.', 'fictioneer' ) . '</div>';
    }

    if ( $splide ) {
      echo fictioneer_get_splide_loading_style( $splide, $args['uid'] );
      echo fictioneer_get_splide_placeholders( $args['uid'] );
      echo '<div class="splide__track">';
    }
  ?>
    <?php if ( $entries->have_posts() ) : ?>

      <ul class="grid-columns <?php if ( $splide ) { echo 'splide__list'; } ?>">
        <?php while ( $entries->have_posts() ) : $entries->the_post(); ?>

          <?php
            // Setup
            $post_id = $post->ID;
            $story = fictioneer_get_story_data( $post_id, false ); // Does not refresh comment count!
            $story_link = get_post_meta( $post_id, 'fictioneer_story_redirect_link', true ) ?: get_permalink( $post_id );
            $tags = ( $show_terms && get_option( 'fictioneer_show_tags_on_story_cards' ) ) ? get_the_tags( $post ) : false;
            $is_sticky = FICTIONEER_ENABLE_STICKY_CARDS && get_post_meta( $post_id, 'fictioneer_story_sticky', true );
            $grid_or_vertical = $args['vertical'] ? '_vertical' : '_grid';
            $card_classes = [];

            // Description
            $short_description = fictioneer_first_paragraph_as_excerpt(
              fictioneer_get_content_field( 'fictioneer_story_short_description', $post_id )
            );
            $short_description = mb_strlen( $short_description, 'UTF-8' ) < 30 ? get_the_excerpt() : $short_description;

            // Extra card classes
            $card_classes[] = '_' . $args['post_status'];

            if ( $show_terms ) {
              $card_classes[] = '_info';
            }

            if ( $is_sticky ) {
              $card_classes[] = '_sticky';
            }

            if ( ! empty( $post->post_password ) ) {
              $card_classes[] = '_password';
            }

            if ( get_theme_mod( 'card_style', 'default' ) !== 'default' ) {
              $card_classes[] = '_' . get_theme_mod( 'card_style' );
            }

            if ( $args['vertical'] ) {
              $card_classes[] = '_vertical';
            }

            if ( $args['seamless'] ) {
              $card_classes[] = '_seamless';
            }

            if ( ! $show_terms || ! ( $story['has_taxonomies'] || $tags ) ) {
              $card_classes[] = '_no-tax';
            }

            if ( ! $args['footer'] ) {
              $card_classes[] = '_no-footer';
            }

            if ( ! $args['footer_chapters'] ) {
              $card_classes[] = '_no-footer-chapters';
            }

            if ( ! $args['footer_words'] ) {
              $card_classes[] = '_no-footer-words';
            }

            if ( ! $args['footer_date'] ) {
              $card_classes[] = '_no-footer-date';
            }

            if ( ! $args['footer_status'] ) {
              $card_classes[] = '_no-footer-status';
            }

            if ( ! $args['footer_rating'] ) {
              $card_classes[] = '_no-footer-rating';
            }

            if ( ! $args['footer_comments'] ) {
              $card_classes[] = '_no-footer-comments';
            }

            if ( $splide ) {
              $card_classes[] = 'splide__slide';
            }

            // Truncate factor
            $truncate_factor = $args['vertical'] ? '_4-4' : '_cq-3-4';

            // Card attributes
            $attributes = [];

            if ( $args['aspect_ratio'] ) {
              $attributes['style'] = '--card-image-aspect-ratio: ' . $args['aspect_ratio'];
            }

            $attributes = apply_filters( 'fictioneer_filter_card_attributes', $attributes, $post, 'shortcode-latest-stories-compact' );

            $card_attributes = '';

            foreach ( $attributes as $key => $value ) {
              $card_attributes .= esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
            }
          ?>

          <li class="post-<?php echo $post_id; ?> card watch-last-clicked _small _story _compact _no-footer <?php echo implode( ' ', $card_classes ); ?>" <?php echo $card_attributes; ?>>
            <div class="card__body polygon">

              <?php if ( $args['infobox'] ) : ?>
                <button class="card__info-toggle" aria-label="<?php esc_attr_e( 'Open info box', 'fictioneer' ); ?>" data-fictioneer-last-click-target="toggle" data-action="click->fictioneer-last-click#toggle"><i class="fa-solid fa-chevron-down"></i></button>
              <?php endif; ?>

              <div class="card__main <?php echo $grid_or_vertical; ?> _small">

                <?php
                  do_action( 'fictioneer_shortcode_latest_stories_card_body', $post, $story, $args );

                  if ( $args['thumbnail'] ) {
                    fictioneer_render_thumbnail(
                      array(
                        'post_id' => $post_id,
                        'title' => $story['title'],
                        'classes' => 'card__image cell-img',
                        'permalink' => $story_link,
                        'lightbox' => $args['lightbox'],
                        'vertical' => $args['vertical'],
                        'seamless' => $args['seamless'],
                        'aspect_ratio' => $args['aspect_ratio']
                      )
                    );
                  }
                ?>

                <h3 class="card__title _small cell-title"><a href="<?php echo $story_link; ?>" class="truncate _1-1"><?php
                  if ( ! empty( $post->post_password ) ) {
                    echo '<i class="fa-solid fa-lock protected-icon"></i> ';
                  }

                  echo $story['title'];
                ?></a></h3>

                <div class="card__content _small cell-desc">
                  <div class="truncate <?php echo $truncate_factor; ?>">
                    <?php if ( get_option( 'fictioneer_show_authors' ) && $args['source'] ) : ?>
                      <span class="card__by-author"><?php
                        printf( _x( 'by %s —', 'Small card: by {Author} —.', 'fictioneer' ), fictioneer_get_author_node() );
                      ?></span>
                    <?php endif; ?>
                    <span><?php echo $short_description; ?></span>
                  </div>
                </div>

              </div>

              <?php if ( $args['infobox'] ) : ?>
                <div class="card__overlay-infobox escape-last-click">

                  <?php if ( $show_terms ) : ?>
                    <div class="card__tag-list _small <?php echo $args['terms'] === 'pills' ? '_pills' : ''; ?>">
                      <?php
                        if ( $story['has_taxonomies'] || $tags ) {
                          $variant = $args['terms'] === 'pills' ? '_pill' : '_inline';

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
                            fictioneer_get_bullet_separator( 'latest-stories-compact', $args['terms'] === 'pills' ),
                            array_slice( $terms, 0, $args['max_terms'] )
                          );
                        } else {
                          ?><span class="card__no-taxonomies"><?php _e( 'No taxonomies specified yet.', 'fictioneer' ); ?></span><?php
                        }
                      ?>
                    </div>
                  <?php else : ?>
                    <div class="card__overlay-description _small"><?php echo $short_description; ?></div>
                  <?php endif; ?>

                  <?php if ( $args['footer'] ) : ?>
                    <div class="card__footer _small">

                      <div class="card__footer-box _left text-overflow-ellipsis"><?php

                        // Build footer items
                        $footer_items = [];

                        if ( $args['footer_chapters'] && ( $story['status'] !== 'Oneshot' || $story['chapter_count'] > 1 ) ) {
                          $footer_items['chapters'] = '<span class="card__footer-chapters"><i class="card-footer-icon fa-solid fa-list" title="' . esc_attr__( 'Chapters', 'fictioneer' ) . '"></i> ' . $story['chapter_count'] . '</span>';
                        }

                        if ( $args['footer_words'] && ( $story['word_count'] > 2000 || $story['status'] === 'Oneshot' ) ) {
                          $footer_items['words'] = '<span class="card__footer-words"><i class="card-footer-icon fa-solid fa-font" title="' . esc_attr__( 'Total Words', 'fictioneer' ) . '"></i> ' . $story['word_count_short'] . '</span>';
                        }

                        if ( $args['footer_comments'] ) {
                          $footer_items['comments'] = '<span class="card__footer-comments"><i class="card-footer-icon fa-solid fa-message" title="' . esc_attr__( 'Comments', 'fictioneer' ) . '"></i> ' . get_comments_number( $post ) . '</span>';
                        }

                        if ( $args['footer_date'] ) {
                          $format = $args['date_format'] ?: FICTIONEER_LATEST_STORIES_FOOTER_DATE;

                          if ( $args['orderby'] === 'modified' ) {
                            $footer_items['modified_date'] = '<span class="card__footer-modified-date"><i class="card-footer-icon fa-regular fa-clock" title="' . esc_attr__( 'Last Updated', 'fictioneer' ) . '"></i> ' . get_the_modified_date( $format, $post ) . '</span>';
                          } else {
                            $footer_items['publish_date'] = '<span class="card__footer-publish-date"><i class="card-footer-icon fa-solid fa-clock" title="' . esc_attr__( 'Published', 'fictioneer' ) . '"></i> ' . get_the_date( $format, $post ) . '</span>';
                          }
                        }

                        if ( $args['footer_status'] ) {
                          $footer_items['status'] = '<span class="card__footer-status _' . strtolower( $story['status'] ) . '"><i class="card-footer-icon ' . $story['icon'] . '"></i> ' . fictioneer_get_story_status_label( $story['id'], $story['status'] ) . '</span>';
                        }

                        // Filter footer items
                        $footer_items = apply_filters(
                          'fictioneer_filter_shortcode_latest_stories_card_footer',
                          $footer_items,
                          $post,
                          $story,
                          $args
                        );

                        // Implode and render footer items
                        echo implode( ' ', $footer_items );

                      ?></div>

                      <?php if ( $args['footer_rating'] ) : ?>
                        <div class="card__footer-box _right rating-letter-label tooltipped _<?php echo strtolower( $story['rating'] ); ?>" data-tooltip="<?php echo fcntr( $story['rating'], true ); ?>">
                          <?php echo fcntr( $story['rating_letter'] ); ?>
                        </div>
                      <?php endif; ?>

                    </div>
                  <?php endif; ?>

                </div>
              <?php endif; ?>

            </div>
          </li>

        <?php endwhile; ?>
      </ul>

    <?php else : ?>

      <div class="no-results"><?php _e( 'Nothing to show.', 'fictioneer' ); ?></div>

    <?php endif; wp_reset_postdata(); ?>

  <?php if ( $splide ) { echo '</div>'; } ?>
</section>
