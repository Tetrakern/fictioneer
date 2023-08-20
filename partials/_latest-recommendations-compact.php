<?php
/**
 * Partial: Latest Recommendations Compact
 *
 * Renders the (buffered) HTML for the [fictioneer_latest_recommendations type="compact"] shortcode.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0
 *
 * @internal $args['count']          Number of posts provided by the shortcode.
 * @internal $args['author']         Author provided by the shortcode.
 * @internal $args['order']          Order of posts. Default 'desc'.
 * @internal $args['orderby']        Sorting of posts. Default 'date'.
 * @internal $args['post_ids']       Array of post IDs. Default empty.
 * @internal $args['excluded_cats']  Array of category IDs to exclude. Default empty.
 * @internal $args['excluded_tags']  Array of tag IDs to exclude. Default empty.
 * @internal $args['taxonomies']     Array of taxonomy arrays. Default empty.
 * @internal $args['relation']       Relationship between taxonomies.
 */
?>

<?php

// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$show_taxonomies = ! get_option( 'fictioneer_hide_taxonomies_on_recommendation_cards' );

// Prepare query
$query_args = array (
  'post_type' => 'fcn_recommendation',
  'post_status' => 'publish',
  'post__in' => $args['post_ids'], // May be empty!
  'orderby' => $args['orderby'] ?? 'date',
  'order' => $args['order'] ?? 'desc',
  'posts_per_page' => $args['count'],
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
$query_args = apply_filters( 'fictioneer_filter_shortcode_latest_recommendations_query_args', $query_args, $args );

// Query chapters
$entries = fictioneer_shortcode_query( $query_args );

?>

<section class="small-card-block latest-recommendations _compact <?php echo implode( ' ', $args['classes'] ); ?>">
  <?php if ( $entries->have_posts() ) : ?>

    <ul class="two-columns _collapse-on-mobile">
      <?php while ( $entries->have_posts() ) : $entries->the_post(); ?>

        <?php
          // Setup
          $title = fictioneer_get_safe_title( get_the_ID() );
          $one_sentence = fictioneer_get_field( 'fictioneer_recommendation_one_sentence' );
          $fandoms = get_the_terms( $post, 'fcn_fandom' );
          $characters = get_the_terms( $post, 'fcn_character' );
          $genres = get_the_terms( $post, 'fcn_genre' );
          $tags = get_option( 'fictioneer_show_tags_on_recommendation_cards' ) ? get_the_tags( $post ) : false;
        ?>

        <li class="card watch-last-clicked _small <?php echo $show_taxonomies ? '_info' : ''; ?>">
          <div class="card__body polygon">

            <?php if ( $show_taxonomies ) : ?>
              <button class="card__info-toggle toggle-last-clicked" aria-label="<?php esc_attr_e( 'Open info box', 'fictioneer' ); ?>"><i class="fa-solid fa-chevron-down"></i></button>
            <?php endif; ?>

            <div class="card__main _grid _small">

              <?php if ( has_post_thumbnail() ) : ?>
                <a href="<?php the_post_thumbnail_url( 'full' ); ?>" title="<?php echo esc_attr( sprintf( __( '%s Thumbnail', 'fictioneer' ), $title ) ); ?>" class="card__image cell-img" <?php echo fictioneer_get_lightbox_attribute(); ?>>
                  <?php echo get_the_post_thumbnail( $post, 'snippet', ['class' => 'no-auto-lightbox'] ); ?>
                </a>
              <?php endif; ?>

              <h3 class="card__title _small cell-title"><a href="<?php the_permalink(); ?>" class="truncate _1-1"><?php echo $title; ?></a></h3>

              <div class="card__content _small cell-desc truncate _3-3">
                <span class="card__by-author"><?php
                  printf(
                    _x( 'by %s —', 'Small card: by {Author} —.', 'fictioneer' ),
                    '<span class="author">' . fictioneer_get_field( 'fictioneer_recommendation_author' ) . '</span>'
                  );
                ?></span>
                <span><?php
                  if ( ! empty( $one_sentence ) ) {
                    echo wp_strip_all_tags( $one_sentence, true );
                  } else {
                    echo fictioneer_first_paragraph_as_excerpt( get_the_content() );
                  }
                ?></span>
              </div>

            </div>

            <?php if ( $show_taxonomies ) : ?>
              <div class="card__overlay-infobox escape-last-click">
                <div class="card__tag-list _small truncate _3-3">
                  <?php
                    if ( $fandoms || $characters || $genres || $tags ) {
                      $output = [];

                      if ( $fandoms ) {
                        foreach ( $fandoms as $fandom ) {
                          $output[] = '<a href="' . get_tag_link( $fandom ) . '" class="tag-pill _inline _fandom">' . $fandom->name . '</a>';
                        }
                      }

                      if ( $genres ) {
                        foreach ( $genres as $genre ) {
                          $output[] = '<a href="' . get_tag_link( $genre ) . '" class="tag-pill _inline _genre">' . $genre->name . '</a>';
                        }
                      }

                      if ( $tags ) {
                        foreach ( $tags as $tag ) {
                          $output[] = '<a href="' . get_tag_link( $tag ) . '" class="tag-pill _inline">' . $tag->name . '</a>';
                        }
                      }

                      if ( $characters ) {
                        foreach ( $characters as $character ) {
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
        </li>

      <?php endwhile; ?>
    </ul>

  <?php else: ?>

    <div class="no-results"><?php _e( 'No recommendations have been published yet.', 'fictioneer' ) ?></div>

  <?php endif; wp_reset_postdata(); ?>
</section>
