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
 * @internal $args['type']              Type argument passed from shortcode ('compact').
 * @internal $args['count']             Number of posts provided by the shortcode.
 * @internal $args['author']            Author provided by the shortcode.
 * @internal $args['order']             Order of posts. Default 'DESC'.
 * @internal $args['orderby']           Sorting of posts. Default 'date'.
 * @internal $args['post_ids']          Array of post IDs. Default empty.
 * @internal $args['author_ids']        Array of author IDs. Default empty.
 * @internal $args['excluded_authors']  Array of author IDs to exclude. Default empty.
 * @internal $args['excluded_cats']     Array of category IDs to exclude. Default empty.
 * @internal $args['excluded_tags']     Array of tag IDs to exclude. Default empty.
 * @internal $args['ignore_protected']  Whether to ignore protected posts. Default false.
 * @internal $args['taxonomies']        Array of taxonomy arrays. Default empty.
 * @internal $args['relation']          Relationship between taxonomies.
 * @internal $args['vertical']          Whether to show the vertical variant.
 * @internal $args['seamless']          Whether to render the image seamless. Default false (Customizer).
 * @internal $args['aspect_ratio']      Aspect ratio for the image. Only with vertical.
 * @internal $args['lightbox']          Whether the image is opened in the lightbox. Default true.
 * @internal $args['thumbnail']         Whether the image is rendered. Default true (Customizer).
 * @internal $args['classes']           String of additional CSS classes. Default empty.
 */


// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$show_taxonomies = ! get_option( 'fictioneer_hide_taxonomies_on_story_cards' );

// Prepare query
$query_args = array(
  'fictioneer_query_name' => 'latest_stories_compact',
  'post_type' => 'fcn_story',
  'post_status' => 'publish',
  'post__in' => $args['post_ids'], // May be empty!
  'order' => $args['order'],
  'orderby' => $args['orderby'],
  'posts_per_page' => $args['count'],
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
  add_filter( 'posts_where', 'fictioneer_exclude_protected_posts' );
}

// Apply filters
$query_args = apply_filters( 'fictioneer_filter_shortcode_latest_stories_query_args', $query_args, $args );

// Query stories
$entries = fictioneer_shortcode_query( $query_args );

// Remove temporary filters
remove_filter( 'posts_where', 'fictioneer_exclude_protected_posts' );

?>

<section class="small-card-block latest-stories _compact <?php echo $args['classes']; ?>">
  <?php if ( $entries->have_posts() ) : ?>

    <ul class="grid-columns _collapse-on-mobile">
      <?php while ( $entries->have_posts() ) : $entries->the_post(); ?>

        <?php
          // Setup
          $story = fictioneer_get_story_data( $post->ID, false ); // Does not refresh comment count!
          $story_link = get_post_meta( $post->ID, 'fictioneer_story_redirect_link', true ) ?: get_permalink( $post->ID );
          $tags = get_option( 'fictioneer_show_tags_on_story_cards' ) ? get_the_tags( $post ) : false;
          $is_sticky = FICTIONEER_ENABLE_STICKY_CARDS && get_post_meta( $post->ID, 'fictioneer_story_sticky', true );
          $grid_or_vertical = $args['vertical'] ? '_vertical' : '_grid';
          $card_classes = [];

          // Extra classes
          if ( $show_taxonomies ) {
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

        <li class="post-<?php echo $post->ID; ?> card watch-last-clicked _small _story _compact _no-footer <?php echo implode( ' ', $card_classes ); ?>" <?php echo $card_attributes; ?>>
          <div class="card__body polygon">

            <?php if ( $show_taxonomies ) : ?>
              <button class="card__info-toggle toggle-last-clicked" aria-label="<?php esc_attr_e( 'Open info box', 'fictioneer' ); ?>"><i class="fa-solid fa-chevron-down"></i></button>
            <?php endif; ?>

            <div class="card__main <?php echo $grid_or_vertical; ?> _small">

              <?php
                do_action( 'fictioneer_shortcode_latest_stories_card_body', $post, $story, $args );

                if ( $args['thumbnail'] ) {
                  fictioneer_output_small_card_thumbnail(
                    array(
                      'post_id' => $post->ID,
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
                  <?php if ( get_option( 'fictioneer_show_authors' ) ) : ?>
                    <span class="card__by-author"><?php
                      printf( _x( 'by %s —', 'Small card: by {Author} —.', 'fictioneer' ), fictioneer_get_author_node() );
                    ?></span>
                  <?php endif; ?>
                  <span><?php
                    $short_description = fictioneer_first_paragraph_as_excerpt(
                      fictioneer_get_content_field( 'fictioneer_story_short_description', $post->ID )
                    );
                    echo strlen( $short_description ) < 230 ? get_the_excerpt() : $short_description;
                  ?></span>
                </div>
              </div>

            </div>

            <div class="card__overlay-infobox escape-last-click">

              <?php if ( $show_taxonomies ) : ?>
                <div class="card__tag-list _small">
                  <?php
                    if ( $story['has_taxonomies'] || $tags ) {
                      $output = [];

                      if ( $story['fandoms'] ) {
                        foreach ( $story['fandoms'] as $fandom ) {
                          $output[] = '<a href="' . get_tag_link( $fandom ) . '" class="tag-pill _inline _fandom">' . $fandom->name . '</a>';
                        }
                      }

                      if ( $story['genres'] ) {
                        foreach ( $story['genres'] as $genre ) {
                          $output[] = '<a href="' . get_tag_link( $genre ) . '" class="tag-pill _inline _genre">' . $genre->name . '</a>';
                        }
                      }

                      if ( $tags ) {
                        foreach ( $tags as $tag ) {
                          $output[] = '<a href="' . get_tag_link( $tag ) . '" class="tag-pill _inline">' . $tag->name . '</a>';
                        }
                      }

                      if ( $story['characters'] ) {
                        foreach ( $story['characters'] as $character ) {
                          $output[] = '<a href="' . get_tag_link( $character ) . '" class="tag-pill _inline _character">' . $character->name . '</a>';
                        }
                      }

                      // Implode with three-per-em spaces around a bullet
                        echo implode( '&#8196;&bull;&#8196;', $output );
                    } else {
                      ?><span class="card__no-taxonomies"><?php _e( 'No taxonomies specified yet.', 'fictioneer' ); ?></span><?php
                    }
                  ?>
                </div>
              <?php endif; ?>

              <div class="card__footer _small">

                <div class="card__footer-box _left text-overflow-ellipsis"><?php

                  // Build footer items
                  $footer_items = [];

                  if ( $story['status'] !== 'Oneshot' || $story['chapter_count'] > 1 ) {
                    $footer_items['chapters'] = '<i class="card-footer-icon fa-solid fa-list" title="' .
                      esc_attr__( 'Chapters', 'fictioneer' ) . '"></i> ' . $story['chapter_count'];
                  }

                  $footer_items['words'] = '<i class="card-footer-icon fa-solid fa-font" title="' .
                    esc_attr__( 'Total Words', 'fictioneer' ) . '"></i> ' . $story['word_count_short'];

                  if ( $args['orderby'] == 'modified' ) {
                    $footer_items['modified_date'] = '<i class="card-footer-icon fa-regular fa-clock" title="' .
                      esc_attr__( 'Last Updated', 'fictioneer' ) . '"></i> ' .
                      get_the_modified_date( FICTIONEER_LATEST_STORIES_FOOTER_DATE, $post );
                  } else {
                    $footer_items['publish_date'] = '<i class="card-footer-icon fa-solid fa-clock" title="' .
                      esc_attr__( 'Published', 'fictioneer' ) . '"></i> ' .
                      get_the_date( FICTIONEER_LATEST_STORIES_FOOTER_DATE, $post );
                  }

                  $footer_items['status'] = '<i class="card-footer-icon ' . $story['icon'] . '"></i> ' .
                    fcntr( $story['status'] );

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

                <div class="card__footer-box _right rating-letter-label tooltipped" data-tooltip="<?php echo fcntr( $story['rating'], true ); ?>">
                  <?php echo fcntr( $story['rating_letter'] ); ?>
                </div>

              </div>

            </div>

          </div>
        </li>

      <?php endwhile; ?>
    </ul>

  <?php else : ?>

    <div class="no-results"><?php _e( 'Nothing to show.', 'fictioneer' ); ?></div>

  <?php endif; wp_reset_postdata(); ?>
</section>
