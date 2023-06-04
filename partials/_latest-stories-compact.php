<?php
/**
 * Partial: Latest Stories Compact
 *
 * Renders the (buffered) HTML for the [fictioneer_latest_stories type="compact"] shortcode.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0
 *
 * @internal $args['count']         Number of posts provided by the shortcode.
 * @internal $args['author']        Author provided by the shortcode.
 * @internal $args['order']         Order of posts. Default 'desc'.
 * @internal $args['orderby']       Sorting of posts. Default 'date'.
 * @internal $args['post_ids']      Array of post IDs. Default empty.
 * @internal $args['excluded_cats'] Array of category IDs to exclude. Default empty.
 * @internal $args['excluded_tags'] Array of tag IDs to exclude. Default empty.
 * @internal $args['taxonomies']    Array of taxonomy arrays. Default empty.
 * @internal $args['relation']      Relationship between taxonomies.
 * @internal $args['classes']       Array of additional CSS classes. Default empty.
 */
?>

<?php

// Prepare query
$query_args = array(
  'post_type' => 'fcn_story',
  'post_status' => 'publish',
  'post__in' => $args['post_ids'],
  'meta_key' => 'fictioneer_story_sticky',
  'orderby' => 'meta_value ' . $args['orderby'],
  'order' => $args['order'] ?? 'desc',
  'posts_per_page' => $args['count'],
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

// Parameter for author?
if ( isset( $args['author'] ) && $args['author'] ) $query_args['author_name'] = $args['author'];

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

// Apply filters
$query_args = apply_filters( 'fictioneer_filter_shortcode_latest_stories_query_args', $query_args, $args );

// Query stories
$entries = new WP_Query( $query_args );

?>

<section class="small-card-block latest-stories _compact <?php echo implode( ' ', $args['classes'] ); ?>">
  <?php if ( $entries->have_posts() ) : ?>

    <ul class="two-columns _collapse-on-mobile">
      <?php while ( $entries->have_posts() ) : $entries->the_post(); ?>

        <?php
          // Setup
          $story = fictioneer_get_story_data( $post->ID );
          $tags = get_option( 'fictioneer_show_tags_on_story_cards' ) ? get_the_tags( $post ) : false;
        ?>

        <li class="card _small">
          <div class="card__body polygon">

            <?php if ( ! get_option( 'fictioneer_hide_taxonomies_on_story_cards' ) ) : ?>
              <button class="card__info-toggle toggle-last-clicked"><i class="fa-solid fa-chevron-down"></i></button>
            <?php endif; ?>

            <div class="card__main _grid _small">

              <?php if ( has_post_thumbnail() ) : ?>
                <a href="<?php the_post_thumbnail_url( 'full' ); ?>" title="<?php echo esc_attr( sprintf( __( '%s Thumbnail', 'fictioneer' ), $story['title'] ) ); ?>" class="card__image cell-img" <?php echo fictioneer_get_lightbox_attribute(); ?>>
                  <?php echo get_the_post_thumbnail( $post, 'snippet', ['class' => 'no-auto-lightbox'] ); ?>
                </a>
              <?php endif; ?>

              <h3 class="card__title _small cell-title"><a href="<?php the_permalink(); ?>" class="truncate _1-1"><?php echo $story['title']; ?></a></h3>

              <div class="card__content _small cell-desc truncate _3-3">
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

            <div class="card__overlay-infobox escape-last-click">

              <?php if ( ! get_option( 'fictioneer_hide_taxonomies_on_story_cards' ) ) : ?>
                <div class="card__tag-list _small truncate _2-2">
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

                <div class="card__left text-overflow-ellipsis">

                  <i class="fa-solid fa-list" title="<?php esc_attr_e( 'Chapters', 'fictioneer' ); ?>"></i>
                  <?php echo $story['chapter_count']; ?>

                  <i class="fa-solid fa-font" title="<?php esc_attr_e( 'Total Words', 'fictioneer' ); ?>"></i>
                  <?php echo $story['word_count_short']; ?>

                  <i class="fa-regular fa-clock" title="<?php esc_attr_e( 'Last Updated', 'fictioneer' ); ?>"></i>
                  <?php
                    echo get_the_modified_date( get_option( 'fictioneer_subitem_date_format', "M j, 'y" ) ?: "M j, 'y", $post );
                  ?>

                  <i class="<?php echo $story['icon']; ?>"></i>
                  <?php echo fcntr( $story['status'] ); ?>

                </div>

                <div class="card__right rating-letter-label tooltipped" data-tooltip="<?php echo fcntr( $story['rating'], true ); ?>">
                  <?php echo fcntr( $story['rating_letter'] ); ?>
                </div>

              </div>

            </div>

          </div>
        </li>

      <?php endwhile; ?>
    </ul>

  <?php else: ?>

    <div class="no-results"><?php _e( 'No stories have been published yet.', 'fictioneer' ) ?></div>

  <?php endif; wp_reset_postdata(); ?>
</section>
