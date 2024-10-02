<?php
/**
 * Partial: Latest Chapters Compact
 *
 * Renders the (buffered) HTML for the [fictioneer_latest_chapters type="compact"] shortcode.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0.0
 *
 * @internal $args['type']                Type argument passed from shortcode ('compact').
 * @internal $args['count']               Number of posts provided by the shortcode.
 * @internal $args['author']              Author provided by the shortcode.
 * @internal $args['order']               Order of posts. Default 'DESC'.
 * @internal $args['orderby']             Sorting of posts. Default 'date'.
 * @internal $args['spoiler']             Whether to obscure or show chapter excerpt.
 * @internal $args['source']              Whether to show author and story.
 * @internal $args['post_ids']            Array of post IDs. Default empty.
 * @internal $args['author_ids']          Array of author IDs. Default empty.
 * @internal $args['excluded_authors']    Array of author IDs to exclude. Default empty.
 * @internal $args['excluded_cats']       Array of category IDs to exclude. Default empty.
 * @internal $args['excluded_tags']       Array of tag IDs to exclude. Default empty.
 * @internal $args['ignore_protected']    Whether to ignore protected posts. Default false.
 * @internal $args['only_protected']      Whether to query only protected posts. Default false.
 * @internal $args['taxonomies']          Array of taxonomy arrays. Default empty.
 * @internal $args['relation']            Relationship between taxonomies.
 * @internal $args['vertical']            Whether to show the vertical variant.
 * @internal $args['seamless']            Whether to render the image seamless. Default false (Customizer).
 * @internal $args['aspect_ratio']        Aspect ratio for the image. Only with vertical.
 * @internal $args['lightbox']            Whether the image is opened in the lightbox. Default true.
 * @internal $args['thumbnail']           Whether the image is rendered. Default true (Customizer).
 * @internal $args['classes']             String of additional CSS classes. Default empty.
 * @internal $args['infobox']             Whether to show the info box and toggle.
 * @internal $args['date_format']         String to override the date format. Default empty.
 * @internal $args['footer_author']       Whether to show the chapter author. Default true.
 * @internal $args['footer_words']        Whether to show the chapter word count. Default true.
 * @internal $args['footer_date']         Whether to show the chapter date. Default true.
 * @internal $args['splide']              Configuration JSON for the Splide slider. Default empty.
 */


// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$splide = $args['splide'] ?? 0;
$card_counter = 0;

// Prepare query
$query_args = array(
  'fictioneer_query_name' => 'latest_chapters_compact',
  'post_type' => 'fcn_chapter',
  'post_status' => 'publish',
  'post__in' => $args['post_ids'], // May be empty!
  'order' => $args['order'],
  'orderby' => $args['orderby'],
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
  $query_args['has_password'] = false;
}

// Only protected?
if ( $args['only_protected'] ) {
  $query_args['has_password'] = true;
}

// Apply filters
$query_args = apply_filters( 'fictioneer_filter_shortcode_latest_chapters_query_args', $query_args, $args );

// Query chapters
$entries = fictioneer_shortcode_query( $query_args );

// Extra attributes
$attributes = [];

if ( $splide ) {
  $attributes[] = "data-splide='{$splide}'";
}

?>

<section class="small-card-block latest-chapters _compact <?php echo $args['classes']; ?>" <?php echo implode( ' ', $attributes ); ?>>
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
            $story_id = get_post_meta( $post_id, 'fictioneer_chapter_story', true );

            if ( get_post_status( $story_id ) !== 'publish' ) {
              continue;
            }

            $title = fictioneer_get_safe_title( $post_id, 'shortcode-latest-chapters-compact' );
            $story = $story_id ? fictioneer_get_story_data( $story_id, false ) : null; // Does not refresh comment count!
            $text_icon = get_post_meta( $post_id, 'fictioneer_chapter_text_icon', true );
            $words = fictioneer_get_word_count( $post_id );
            $grid_or_vertical = $args['vertical'] ? '_vertical' : '_grid';
            $card_classes = [];

            // Extra card classes
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

            if ( ! $args['footer_words'] ) {
              $card_classes[] = '_no-footer-words';
            }

            if ( ! $args['footer_date'] ) {
              $card_classes[] = '_no-footer-date';
            }

            if ( $splide ) {
              $card_classes[] = 'splide__slide';
            }

            // Count actually rendered cards to account for buffer
            if ( ++$card_counter > $args['count'] ) {
              break;
            }

            // Card attributes
            $attributes = [];

            if ( $args['aspect_ratio'] ) {
              $attributes['style'] = '--card-image-aspect-ratio: ' . $args['aspect_ratio'];
            }

            $attributes = apply_filters( 'fictioneer_filter_card_attributes', $attributes, $post, 'shortcode-latest-chapters-compact' );

            $card_attributes = '';

            foreach ( $attributes as $key => $value ) {
              $card_attributes .= esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
            }
          ?>

          <li class="post-<?php echo $post_id; ?> card watch-last-clicked _small _info _chapter _compact _no-footer <?php echo implode( ' ', $card_classes ); ?>" <?php echo $card_attributes; ?>>
            <div class="card__body polygon">

              <?php if ( $words > 0 && $args['infobox'] ) : ?>
                <button class="card__info-toggle toggle-last-clicked" aria-label="<?php esc_attr_e( 'Open info box', 'fictioneer' ); ?>"><i class="fa-solid fa-chevron-down"></i></button>
              <?php endif; ?>

              <div class="card__main <?php echo $grid_or_vertical; ?> _small">

                <?php
                  do_action( 'fictioneer_shortcode_latest_chapters_card_body', $post, $story, $args );

                  if ( $args['thumbnail'] ) {
                    fictioneer_render_thumbnail(
                      array(
                        'post_id' => $post_id,
                        'title' => $title,
                        'classes' => 'card__image cell-img',
                        'permalink' => get_permalink(),
                        'lightbox' => $args['lightbox'],
                        'vertical' => $args['vertical'],
                        'seamless' => $args['seamless'],
                        'aspect_ratio' => $args['aspect_ratio'],
                        'text_icon' => $text_icon
                      )
                    );
                  }
                ?>

                <h3 class="card__title _small cell-title"><a href="<?php the_permalink(); ?>" class="truncate _1-1"><?php
                  $list_title = get_post_meta( $post_id, 'fictioneer_chapter_list_title', true );
                  $list_title = trim( wp_strip_all_tags( $list_title ) );

                  if ( ! empty( $post->post_password ) ) {
                    echo '<i class="fa-solid fa-lock protected-icon"></i> ';
                  }

                  echo $list_title ? $list_title : $title;
                ?></a></h3>

                <div class="card__content _small cell-desc">
                  <div class="text-overflow-ellipsis _bottom-spacer-xs">
                    <?php if ( get_option( 'fictioneer_show_authors' ) && $args['source'] && $args['footer_author'] ) : ?>
                      <span class="card__by-author"><?php
                        printf( _x( 'by %s', 'Small card: by {Author}.', 'fictioneer' ), fictioneer_get_author_node() );
                      ?></span>
                    <?php endif; ?>
                    <?php
                      if ( $story && $args['source'] ) {
                        $story_link = get_post_meta( $story_id, 'fictioneer_story_redirect_link', true )
                          ?: get_permalink( $story_id );

                        printf(
                          _x( 'in <a href="%1$s" class="bold-link">%2$s</a>', 'Small card: in {Link to Story}.', 'fictioneer' ),
                          $story_link,
                          fictioneer_truncate( fictioneer_get_safe_title( $story_id, 'shortcode-latest-chapters-compact' ), 24 )
                        );
                      }
                    ?>
                  </div>
                  <div class="card__words-on-date text-overflow-ellipsis">
                    <?php
                      $format = $args['date_format'] ?: FICTIONEER_LATEST_CHAPTERS_FOOTER_DATE;

                      if ( $words > 0 && $args['footer_words'] && $args['footer_date'] ) {
                        printf(
                          _x( '%1$s Words on %2$s', 'Small card: {n} Words on {Date}.', 'fictioneer' ),
                          fictioneer_shorten_number( fictioneer_get_word_count( $post_id ) ),
                          get_the_time( $format )
                        );
                      } else {
                        if ( $words > 0 && $args['footer_words'] ) {
                          printf(
                            _x( '%1$s Words', 'Small card: {n} Words.', 'fictioneer' ),
                            fictioneer_shorten_number( fictioneer_get_word_count( $post_id ) )
                          );
                        } elseif ( $args['footer_date'] ) {
                          the_time( $format );
                        }
                      }
                    ?>
                  </div>
                </div>

              </div>

              <?php if ( $words > 0 && $args['infobox'] ) : ?>
                <div class="card__overlay-infobox _excerpt escape-last-click">
                  <div class="card__excerpt"><?php
                    echo fictioneer_get_forced_excerpt( $post, $args['vertical'] ? 512 : 256 );
                  ?></div>
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
