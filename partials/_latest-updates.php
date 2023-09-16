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
 * @since 4.3
 * @see fictioneer_remember_chapters_modified()
 *
 * @internal $args['count']             Number of posts provided by the shortcode.
 * @internal $args['author']            Author provided by the shortcode.
 * @internal $args['order']             Order of posts. Default 'DESC'.
 * @internal $args['post_ids']          Array of post IDs. Default empty.
 * @internal $args['author_ids']        Array of author IDs. Default empty.
 * @internal $args['excluded_authors']  Array of author IDs to exclude. Default empty.
 * @internal $args['excluded_cats']     Array of category IDs to exclude. Default empty.
 * @internal $args['excluded_tags']     Array of tag IDs to exclude. Default empty.
 * @internal $args['ignore_protected']  Whether to ignore protected posts. Default false.
 * @internal $args['taxonomies']        Array of taxonomy arrays. Default empty.
 * @internal $args['relation']          Relationship between taxonomies.
 * @internal $args['simple']            Whether to show the simple variant.
 * @internal $args['classes']           String of additional CSS classes. Default empty.
 */
?>

<?php

// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$card_counter = 0;

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
  'meta_query' => array(
    'relation' => 'OR',
    array(
      'key' => 'fictioneer_story_hidden',
      'value' => '0'
    ),
    array(
      'key' => 'fictioneer_story_hidden',
      'compare' => 'NOT EXISTS'
    ),
  ),
  'no_found_rows' => true
);

// Author?
if ( isset( $args['author'] ) && $args['author'] ) {
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
$query_args = apply_filters( 'fictioneer_filter_shortcode_latest_updates_query_args', $query_args, $args );

// Query stories
$entries = fictioneer_shortcode_query( $query_args );

// Remove temporary filters
remove_filter( 'posts_where', 'fictioneer_exclude_protected_posts' );

?>

<section class="small-card-block latest-updates <?php echo $args['classes']; ?>">
  <?php if ( $entries->have_posts() ) : ?>

    <ul class="two-columns _collapse-on-mobile">
      <?php while ( $entries->have_posts() ) : $entries->the_post(); ?>

        <?php
          // Setup
          $story = fictioneer_get_story_data( $post->ID, false ); // Does not refresh comment count!
          $tags = get_option( 'fictioneer_show_tags_on_story_cards' ) ? get_the_tags( $post ) : false;
          $chapter_list = [];

          // Skip if no chapters
          if ( $story['chapter_count'] < 1 ) {
            continue;
          }

          // Search for viable chapters...
          $search_list = array_reverse( $story['chapter_ids'] );

          foreach ( $search_list as $chapter_id ) {
            if ( fictioneer_get_field( 'fictioneer_chapter_hidden', $chapter_id ) ) {
              continue;
            }

            $chapter_list[] = $chapter_id;

            if ( count( $chapter_list ) > 1 ) {
              break; // Max two
            }
          }

          // No viable chapters
          if ( count( $chapter_list ) < 1 ) {
            continue;
          }

          // Count actually rendered cards to account for buffer
          if ( ++$card_counter > $args['count'] ) {
            break;
          }
        ?>

        <li class="card _small">
          <div class="card__body polygon">

            <div class="card__main _grid _small">

              <?php if ( has_post_thumbnail() ) : ?>
                <a href="<?php the_post_thumbnail_url( 'full' ); ?>" title="<?php echo esc_attr( sprintf( __( '%s Thumbnail', 'fictioneer' ), $story['title'] ) ); ?>" class="card__image cell-img" <?php echo fictioneer_get_lightbox_attribute(); ?>><?php echo get_the_post_thumbnail( $post, 'snippet', ['class' => 'no-auto-lightbox'] ); ?></a>
              <?php endif; ?>

              <h3 class="card__title _small cell-title"><a href="<?php the_permalink(); ?>" class="truncate _1-1"><?php echo $story['title']; ?></a></h3>

              <div class="card__content _small cell-desc">
                <div class="truncate <?php echo count( $chapter_list ) > 1 ? '_1-1' : '_2-2'; ?>">
                  <?php if ( get_option( 'fictioneer_show_authors' ) ) : ?>
                    <span class="card__by-author"><?php
                      printf( _x( 'by %s —', 'Small card: by {Author} —.', 'fictioneer' ), fictioneer_get_author_node() );
                    ?></span>
                  <?php endif; ?>
                  <span><?php
                    $short_description = fictioneer_first_paragraph_as_excerpt( fictioneer_get_content_field( 'fictioneer_story_short_description' ) );
                    echo strlen( $short_description ) < 230 ? get_the_excerpt() : $short_description;
                  ?></span>
                </div>
              </div>

              <ol class="card__link-list _small cell-list">
                <?php foreach ( $chapter_list as $chapter_id ) : ?>
                  <li class="card__link-list-item">
                    <div class="card__left text-overflow-ellipsis">
                      <i class="fa-solid fa-caret-right"></i>
                      <a href="<?php the_permalink( $chapter_id ); ?>"><?php
                        // Chapter title
                        $list_title = fictioneer_get_field( 'fictioneer_chapter_list_title', $chapter_id );

                        if ( empty( $list_title ) ) {
                          echo fictioneer_get_safe_title( $chapter_id );
                        } else {
                          echo wp_strip_all_tags( $list_title );
                        }
                      ?></a>
                    </div>
                    <div class="card__right">
                      <?php
                        echo fictioneer_shorten_number( get_post_meta( $chapter_id, '_word_count', true ) );
                        echo '<span class="separator-dot">&#8196;&bull;&#8196;</span>';
                        echo get_the_date( FICTIONEER_LATEST_UPDATES_LI_DATE, $chapter_id )
                      ?>
                    </div>
                  </li>
                <?php endforeach; ?>
              </ol>

              <?php if ( ! get_option( 'fictioneer_hide_taxonomies_on_story_cards' ) && ! $args['simple'] ) : ?>
                <div class="card__tag-list _small _scrolling cell-tax">
                  <div class="card__h-scroll">
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
                </div>
              <?php endif; ?>

            </div>

            <?php if ( ! $args['simple'] ) : ?>
              <div class="card__footer _small">

                <div class="card__footer-box _left text-overflow-ellipsis"><?php

                  // Build footer items
                  $footer_items = [];

                  $footer_items['chapters'] = '<i class="card-footer-icon fa-solid fa-list" title="' .
                    esc_attr__( 'Chapters', 'fictioneer' ) . '"></i> ' . $story['chapter_count'];

                  $footer_items['words'] = '<i class="card-footer-icon fa-solid fa-font" title="' .
                    esc_attr__( 'Total Words', 'fictioneer' ) . '"></i> ' . $story['word_count_short'];

                  $footer_items['modified_date'] = '<i class="card-footer-icon fa-regular fa-clock" title="' .
                    esc_attr__( 'Last Updated', 'fictioneer' ) . '"></i> ' .
                    get_the_modified_date( FICTIONEER_LATEST_UPDATES_FOOTER_DATE, $post );

                  $footer_items['status'] = '<i class="card-footer-icon ' . $story['icon'] . '"></i> ' .
                    fcntr( $story['status'] );

                  // Filer footer items
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

                <div class="card__footer-box _right rating-letter-label tooltipped" data-tooltip="<?php echo fcntr( $story['rating'], true ); ?>">
                  <?php echo fcntr( $story['rating_letter'] ); ?>
                </div>

              </div>
            <?php endif; ?>

          </div>
        </li>

      <?php endwhile; ?>
    </ul>

  <?php else: ?>

    <div class="no-results"><?php _e( 'Nothing to show.', 'fictioneer' ); ?></div>

  <?php endif; wp_reset_postdata(); ?>
</section>
