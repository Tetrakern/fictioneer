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
 * @internal $args['count']    The number of posts provided by the shortcode.
 * @internal $args['author']   The author provided by the shortcode.
 * @internal $args['order']    Order of posts. Default 'desc'.
 * @internal $args['post_ids'] Comma-separated list of story IDs. Overrides count.
 * @internal $args['simple']   Whether to show the simple variant.
 * @internal $args['class']    Additional classes.
 */
?>

<?php

// Setup
$card_counter = 0;

// Prepare query
$query_args = array(
  'post_type' => array( 'fcn_story' ),
  'post_status' => array( 'publish' ),
  'post__in' => $args['post_ids'],
  'meta_key' => 'fictioneer_chapters_added',
  'orderby' => 'meta_value',
  'order' => $args['order'] ?? 'desc',
  'posts_per_page' => $args['count'] + 4, // Little buffer in case of no viable chapters
  'no_found_rows' => true
);

// Parameter for author?
if ( isset( $args['author'] ) && $args['author'] ) $query_args['author_name'] = $args['author'];

// Query stories
$entries = new WP_Query( $query_args );

?>

<section class="small-card-block latest-updates <?php echo implode( ' ', $args['classes'] ); ?>">
  <?php if ( $entries->have_posts() ) : ?>

    <ul class="two-columns _collapse-on-mobile">
      <?php while ( $entries->have_posts() ) : $entries->the_post(); ?>

        <?php
          // Setup
          $story = fictioneer_get_story_data( $post->ID );
          $tags = get_option( 'fictioneer_show_tags_on_story_cards' ) ? get_the_tags( $post ) : false;
          $chapter_list = [];

          // Skip if no chapters
          if ( $story['chapter_count'] < 1 ) continue;

          // Search for viable chapters...
          $search_list = array_reverse( $story['chapter_ids'] );

          foreach ( $search_list as $chapter_id ) {
            if ( fictioneer_get_field( 'fictioneer_chapter_hidden', $chapter_id ) ) continue;
            $chapter_list[] = $chapter_id;
            if ( count( $chapter_list ) > 1 ) break; // Max two
          }

          // No viable chapters
          if ( count( $chapter_list ) < 1 ) continue;

          // Count actually rendered cards to account for buffer
          if ( ++$card_counter > $args['count'] ) break;
        ?>

        <li class="card _small">
          <div class="card__body polygon">

            <div class="card__main _grid _small">

              <?php if ( has_post_thumbnail() ) : ?>
                <a href="<?php the_post_thumbnail_url( 'full' ); ?>" title="<?php echo esc_attr( sprintf( __( '%s Thumbnail', 'fictioneer' ), $story['title'] ) ); ?>" class="card__image cell-img" <?php echo fictioneer_get_lightbox_attribute(); ?>><?php echo get_the_post_thumbnail( $post, 'snippet', ['class' => 'no-auto-lightbox'] ); ?></a>
              <?php endif; ?>

              <h3 class="card__title _small cell-title"><a href="<?php the_permalink(); ?>" class="truncate truncate--1-1"><?php echo $story['title']; ?></a></h3>

              <div class="card__content _small cell-desc">
                <div class="truncate <?php echo count( $chapter_list ) > 1 ? 'truncate--1-1' : 'truncate--2-2'; ?>">
                  <?php
                    if ( get_option( 'fictioneer_show_authors' ) ) {
                      printf(
                        __( '<span class="author-by">by</span> %s <span>—</span> ', 'fictioneer' ),
                        fictioneer_get_author_node()
                      );
                    }
                  ?>
                  <span><?php
                    $short_description = fictioneer_first_paragraph_as_excerpt( fictioneer_get_content_field( 'fictioneer_story_short_description' ) );
                    echo strlen( $short_description ) < 230 ? get_the_excerpt() : $short_description;
                  ?></span>
                </div>
              </div>

              <ol class="card__link-list _small cell-list">
                <?php foreach ( $chapter_list as $chapter_id ) : ?>
                  <li>
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
                    <div class="card__right _flex dot-separator-inverse">
                      <span><?php echo fictioneer_shorten_number( get_post_meta( $chapter_id, '_word_count', true ) ); ?></span>
                      <span><?php
                        if ( strtotime( '-1 days' ) < strtotime( get_the_date( 'c', $chapter_id ) ) ) {
                          _e( 'New', 'fictioneer' );
                        } else {
                          echo get_the_date( get_option( 'fictioneer_subitem_short_date_format', 'M j' ), $chapter_id );
                        }
                      ?></span>
                    </div>
                  </li>
                <?php endforeach; ?>
              </ol>

              <?php if ( ! get_option( 'fictioneer_hide_taxonomies_on_story_cards' ) && ! $args['simple'] ) : ?>
                <div class="card__tag-list _small _scrolling cell-tax">
                  <div class="dot-separator">
                    <?php
                      if ( $story['has_taxonomies'] || $tags ) {
                        if ( $story['fandoms'] ) {
                          foreach ( $story['fandoms'] as $fandom ) {
                            echo '<span><a href="' . get_tag_link( $fandom ) . '" class="tag-pill _inline _fandom">' . $fandom->name . '</a></span>';
                          }
                        }

                        if ( $story['genres'] ) {
                          foreach ( $story['genres'] as $genre ) {
                            echo '<span><a href="' . get_tag_link( $genre ) . '" class="tag-pill _inline _genre">' . $genre->name . '</a></span>';
                          }
                        }

                        if ( $tags ) {
                          foreach ( $tags as $tag ) {
                            echo '<span><a href="' . get_tag_link( $tag ) . '" class="tag-pill _inline">' . $tag->name . '</a></span>';
                          }
                        }

                        if ( $story['characters'] ) {
                          foreach ( $story['characters'] as $character ) {
                            echo '<span><a href="' . get_tag_link( $character ) . '" class="tag-pill _inline _character">' . $character->name . '</a></span>';
                          }
                        }
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

                <div class="card__left text-overflow-ellipsis">
                  <i class="fa-solid fa-list" title="<?php esc_attr_e( 'Chapters', 'fictioneer' ); ?>"></i>
                  <span title="<?php esc_attr_e( 'Chapters', 'fictioneer' ); ?>"><?php echo $story['chapter_count']; ?></span>

                  <i class="fa-solid fa-font" title="<?php esc_attr_e( 'Total Words', 'fictioneer' ); ?>"></i>
                  <span title="<?php esc_attr_e( 'Total Words', 'fictioneer' ); ?>"><?php echo $story['word_count_short']; ?></span>

                  <i class="fa-regular fa-clock" title="<?php esc_attr_e( 'Last Updated', 'fictioneer' ); ?>"></i>
                  <span title="<?php esc_attr_e( 'Last Updated', 'fictioneer' ); ?>"><?php echo get_the_modified_date( 'M j, y', $post ); ?></span>

                  <i class="<?php echo $story['icon']; ?>"></i>
                  <span><?php echo fcntr( $story['status'] ); ?></span>
                </div>

                <div class="card__right rating-letter-label tooltipped" data-tooltip="<?php echo fcntr( $story['rating'], true ); ?>">
                  <span><?php echo fcntr( $story['rating_letter'] ); ?></span>
                </div>

              </div>
            <?php endif; ?>

          </div>
        </li>

      <?php endwhile; ?>
    </ul>

  <?php else: ?>

    <div class="no-results"><?php _e( 'No updates have been published yet.', 'fictioneer' ) ?></div>

  <?php endif; wp_reset_postdata(); ?>
</section>
