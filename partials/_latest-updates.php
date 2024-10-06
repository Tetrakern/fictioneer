<?php
/**
 * Partial: Latest Updates (+ Simple)
 *
 * Renders the (buffered) HTML for the [fictioneer_latest_updates] or
 * [fictioneer_latest_updates type="simple"] shortcodes. Looks for the
 * 'fictioneer_chapters_added' meta property and only updates when the
 * items in the chapters list increase. Admittedly, this can be exploited
 * by removing and re-adding chapters.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.3.0
 *
 * @internal $args['uid']                 Unique ID of the shortcode. Pattern: shortcode-id-{id}.
 * @internal $args['type']                Type argument passed from shortcode. Default 'default'.
 * @internal $args['single']              Whether to show only one chapter item. Default false.
 * @internal $args['count']               Number of posts provided by the shortcode.
 * @internal $args['author']              Author provided by the shortcode.
 * @internal $args['order']               Order of posts. Default 'DESC'.
 * @internal $args['post_ids']            Array of post IDs. Default empty.
 * @internal $args['author_ids']          Array of author IDs. Default empty.
 * @internal $args['excluded_authors']    Array of author IDs to exclude. Default empty.
 * @internal $args['excluded_cats']       Array of category IDs to exclude. Default empty.
 * @internal $args['excluded_tags']       Array of tag IDs to exclude. Default empty.
 * @internal $args['ignore_protected']    Whether to ignore protected posts. Default false.
 * @internal $args['only_protected']      Whether to query only protected posts. Default false.
 * @internal $args['taxonomies']          Array of taxonomy arrays. Default empty.
 * @internal $args['relation']            Relationship between taxonomies.
 * @internal $args['source']              Whether to show author and story.
 * @internal $args['vertical']            Whether to show the vertical variant.
 * @internal $args['seamless']            Whether to render the image seamless. Default false (Customizer).
 * @internal $args['aspect_ratio']        Aspect ratio for the image. Only with vertical.
 * @internal $args['lightbox']            Whether the image is opened in the lightbox. Default true.
 * @internal $args['thumbnail']           Whether the image is rendered. Default true (Customizer).
 * @internal $args['words']               Whether to show the word count of chapter items. Default true.
 * @internal $args['date']                Whether to show the date of chapter items. Default true.
 * @internal $args['terms']               Either inline, pills, none, or false. Default inline.
 * @internal $args['max_terms']           Maximum number of shown taxonomies. Default 10.
 * @internal $args['date_format']         String to override the date format. Default empty.
 * @internal $args['nested_date_format']  String to override the date format of nested items. Default empty.
 * @internal $args['footer']              Whether to show the footer. Default true.
 * @internal $args['footer_chapters']     Whether to show the story chapter count. Default true.
 * @internal $args['footer_words']        Whether to show the story word count. Default true.
 * @internal $args['footer_date']         Whether to show the modified date. Default true.
 * @internal $args['footer_status']       Whether to show the story status. Default true.
 * @internal $args['footer_rating']       Whether to show the story age rating. Default true.
 * @internal $args['classes']             String of additional CSS classes. Default empty.
 * @internal $args['splide']              Configuration JSON for the Splide slider. Default empty.
 */


// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$splide = $args['splide'] ?? 0;
$card_counter = 0;
$show_terms = ! get_option( 'fictioneer_hide_taxonomies_on_story_cards' ) &&
  ! in_array( $args['type'], ['simple', 'single'] ) &&
  ! in_array( $args['terms'], ['none', 'false'] );

// Prepare query
$query_args = array(
  'fictioneer_query_name' => 'latest_updates',
  'post_type' => 'fcn_story',
  'post_status' => 'publish',
  'post__in' => $args['post_ids'], // May be empty!
  'order' => $args['order'],
  'orderby' => 'meta_value',
  'meta_key' => 'fictioneer_chapters_added',
  'posts_per_page' => $args['count'] + 4, // Account for non-eligible posts!
  'update_post_term_cache' => $show_terms, // Improve performance
  'no_found_rows' => true // Improve performance
);

// Use extended meta query?
if ( get_option( 'fictioneer_disable_extended_story_list_meta_queries' ) ) {
  // Extended syntax necessary due to 'fictioneer_chapters_added'
  $query_args['meta_query'] = array(
    array(
      'key' => 'fictioneer_story_hidden',
      'value' => '0'
    )
  );
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
$query_args = apply_filters( 'fictioneer_filter_shortcode_latest_updates_query_args', $query_args, $args );

// Query stories
$entries = fictioneer_shortcode_query( $query_args );

// Extra attributes
$attributes = [];

if ( $splide ) {
  $attributes[] = "data-splide='{$splide}'";
}

?>

<section class="small-card-block latest-updates <?php echo $args['classes']; ?>" <?php echo implode( ' ', $attributes ); ?>>
  <?php
    if ( $args['splide'] === false ) {
      echo '<div class="shortcode-json-invalid">' . __( 'Splide JSON is invalid and has been ignored.', 'fictioneer' ) . '</div>';
    }

    if ( $splide ) {
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
            $grid_or_vertical = $args['vertical'] ? '_vertical' : '_grid';
            $chapter_list = [];
            $card_classes = [];

            // Skip if no chapters
            if ( $story['chapter_count'] < 1 ) {
              continue;
            }

            // Extra classes
            if ( ! empty( $post->post_password ) ) {
              $card_classes[] = '_password';
            }

            if ( ! $args['footer'] || in_array( $args['type'], ['simple', 'single'] ) ) {
              $card_classes[] = '_no-footer';
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

            if ( ! $args['words'] ) {
              $card_classes[] = '_no-chapter-words';
            }

            if ( ! $args['date'] ) {
              $card_classes[] = '_no-chapter-dates';
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

            if ( $splide ) {
              $card_classes[] = 'splide__slide';
            }

            // Search for viable chapters...
            $search_list = array_reverse( $story['chapter_ids'] );

            foreach ( $search_list as $chapter_id ) {
              $chapter_post = get_post( $chapter_id );

              if ( ! $chapter_post ) {
                continue;
              }

              if ( get_post_meta( $chapter_id, 'fictioneer_chapter_hidden', true ) ) {
                continue;
              }

              if ( $args['ignore_protected'] && $chapter_post->post_password ) {
                continue;
              }

              $chapter_list[] = $chapter_post;

              if ( count( $chapter_list ) > 1 ) {
                break; // Max one or two
              }
            }

            // Slice chapter list down
            $chapter_list = array_slice( $chapter_list, 0, ( $args['single'] || $args['type'] === 'single' ) ? 1 : 2 );

            // No viable chapters
            if ( count( $chapter_list ) < 1 ) {
              continue;
            }

            // Count actually rendered cards to account for buffer
            if ( ++$card_counter > $args['count'] ) {
              break;
            }

            // Truncate factor
            $truncate_factor = '_1-1';

            if ( $args['vertical'] ) {
              $truncate_factor = count( $chapter_list ) > 1 ? '_2-2' : '_3-3';
            } else {
              $truncate_factor = count( $chapter_list ) > 1 ? '_1-1' : '_2-2';
            }

            // Card attributes
            $attributes = [];

            if ( $args['aspect_ratio'] ) {
              $attributes['style'] = '--card-image-aspect-ratio: ' . $args['aspect_ratio'];
            }

            $attributes = apply_filters( 'fictioneer_filter_card_attributes', $attributes, $post, 'shortcode-latest-updates' );

            $card_attributes = '';

            foreach ( $attributes as $key => $value ) {
              $card_attributes .= esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
            }
          ?>

          <li class="post-<?php echo $post_id; ?> card _small _story-update <?php echo implode( ' ', $card_classes ); ?>" <?php echo $card_attributes; ?>>
            <div class="card__body polygon">

              <div class="card__main <?php echo $grid_or_vertical; ?> _small">

                <?php
                  do_action( 'fictioneer_shortcode_latest_updates_card_body', $post, $story, $args );

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
                    <span><?php
                      $short_description = fictioneer_first_paragraph_as_excerpt(
                        fictioneer_get_content_field( 'fictioneer_story_short_description', $post_id )
                      );
                      echo mb_strlen( $short_description, 'UTF-8' ) < 30 ? get_the_excerpt() : $short_description;
                    ?></span>
                  </div>
                </div>

                <ol class="card__link-list _small cell-list">
                  <?php foreach ( $chapter_list as $chapter ) : ?>
                    <?php
                      // Chapter title
                      $list_title = get_post_meta( $chapter->ID, 'fictioneer_chapter_list_title', true );
                      $list_title = trim( wp_strip_all_tags( $list_title ) );

                      if ( empty( $list_title ) ) {
                        $chapter_title = fictioneer_get_safe_title( $chapter->ID, 'shortcode-latest-updates' );
                      } else {
                        $chapter_title = $list_title;
                      }

                      // Extra classes
                      $list_item_classes = [];

                      if ( ! empty( $chapter->post_password ) ) {
                        $list_item_classes[] = '_password';
                      }
                    ?>
                    <li class="card__link-list-item <?php echo implode( ' ', $list_item_classes ); ?>">
                      <div class="card__left text-overflow-ellipsis">
                        <i class="fa-solid fa-caret-right"></i>
                        <a href="<?php the_permalink( $chapter->ID ); ?>" class="card__link-list-link"><?php
                          echo $chapter_title;
                        ?></a>
                      </div>
                      <?php if ( $args['words'] || $args['date'] ) : ?>
                        <div class="card__right">
                          <?php
                            $words = $args['words'] ? fictioneer_get_word_count( $chapter->ID ) : 0;

                            if ( $words ) {
                              echo '<span class="words _words-' . $words . '">' . fictioneer_shorten_number( $words ) . '</span>';
                            }

                            if ( $words && $args['date'] ) {
                              echo '<span class="separator-dot">&#8196;&bull;&#8196;</span>';
                            }

                            if ( $args['date'] ) {
                              echo '<span class="date">' . get_the_date(
                                $args['nested_date_format'] ?: FICTIONEER_LATEST_UPDATES_LI_DATE, $chapter->ID
                              ) . '</span>';
                            }
                          ?>
                        </div>
                      <?php endif; ?>
                    </li>
                  <?php endforeach; ?>
                </ol>

                <?php if ( $show_terms ) : ?>
                  <div class="card__tag-list _small _scrolling cell-tax">
                    <div class="card__h-scroll <?php echo $args['terms'] === 'pills' ? '_pills' : ''; ?>">
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
                            'fictioneer_filter_shortcode_latest_updates_terms',
                            $terms, $post, $args, $story
                          );

                          // Implode with separator
                          echo implode(
                            fictioneer_get_bullet_separator( 'latest-updates', $args['terms'] === 'pills' ),
                            array_slice( $terms, 0, $args['max_terms'] )
                          );
                        } else {
                          ?><span class="card__no-taxonomies"><?php _e( 'No taxonomies specified yet.', 'fictioneer' ); ?></span><?php
                        }
                      ?>
                    </div>
                  </div>
                <?php endif; ?>

                <?php if ( $args['footer'] && ! in_array( $args['type'], ['simple', 'single'] ) ) : ?>
                  <div class="card__footer cell-footer _small">

                    <div class="card__footer-box _left text-overflow-ellipsis"><?php

                      // Build footer items
                      $footer_items = [];

                      if ( $args['footer_chapters'] ) {
                        $footer_items['chapters'] = '<span class="card__footer-chapters"><i class="card-footer-icon fa-solid fa-list" title="' . esc_attr__( 'Chapters', 'fictioneer' ) . '"></i> ' . $story['chapter_count'] . '</span>';
                      }

                      if ( $story['word_count'] > 0 && $args['footer_words'] ) {
                        $footer_items['words'] = '<span class="card__footer-words"><i class="card-footer-icon fa-solid fa-font" title="' . esc_attr__( 'Total Words', 'fictioneer' ) . '"></i> ' . $story['word_count_short'] . '</span>';
                      }

                      if ( $args['footer_date'] ) {
                        $format = $args['date_format'] ?: FICTIONEER_LATEST_UPDATES_FOOTER_DATE;

                        $footer_items['modified_date'] = '<span class="card__footer-modified-date"><i class="card-footer-icon fa-regular fa-clock" title="' . esc_attr__( 'Last Updated', 'fictioneer' ) . '"></i> ' . get_the_modified_date( $format, $post ) . '</span>';
                      }

                      if ( $args['footer_status'] ) {
                        $footer_items['status'] = '<span class="card__footer-status"><i class="card-footer-icon ' . $story['icon'] . '"></i> ' . fcntr( $story['status'] ) . '</span>';
                      }

                      // Filter footer items
                      $footer_items = apply_filters(
                        'fictioneer_filter_shortcode_latest_updates_card_footer',
                        $footer_items,
                        $post,
                        $story,
                        $args
                      );

                      // Implode and render footer items
                      echo implode( ' ', $footer_items );

                    ?></div>

                    <?php if ( $args['footer_rating'] ) : ?>
                      <div class="card__footer-box _right rating-letter-label tooltipped" data-tooltip="<?php echo fcntr( $story['rating'], true ); ?>">
                        <?php echo fcntr( $story['rating_letter'] ); ?>
                      </div>
                    <?php endif; ?>

                  </div>
                <?php endif; ?>

              </div>

            </div>
          </li>

        <?php endwhile; ?>
      </ul>

    <?php else : ?>

      <div class="no-results"><?php _e( 'Nothing to show.', 'fictioneer' ); ?></div>

    <?php endif; wp_reset_postdata(); ?>

  <?php if ( $splide ) { echo '</div>'; } ?>
</section>
