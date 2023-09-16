<?php
/**
 * Partial: Latest Recommendations
 *
 * Renders the (buffered) HTML for the [fictioneer_latest_recommendations] shortcode.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0
 *
 * @internal $args['count']             Number of posts provided by the shortcode.
 * @internal $args['author']            Author provided by the shortcode.
 * @internal $args['order']             Order of posts. Default 'DESC'.
 * @internal $args['orderby']           Sorting of posts. Default 'date'.
 * @internal $args['post_ids']          Array of post IDs. Default empty.
 * @internal $args['excluded_cats']     Array of category IDs to exclude. Default empty.
 * @internal $args['excluded_tags']     Array of tag IDs to exclude. Default empty.
 * @internal $args['ignore_protected']  Whether to ignore protected posts. Default false.
 * @internal $args['taxonomies']        Array of taxonomy arrays. Default empty.
 * @internal $args['relation']          Relationship between taxonomies.
 * @internal $args['classes']           String of additional CSS classes. Default empty.
 */
?>

<?php

// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$show_taxonomies = ! get_option( 'fictioneer_hide_taxonomies_on_recommendation_cards' );

// Prepare query
$query_args = array (
  'fictioneer_query_name' => 'latest_recommendations',
  'post_type' => 'fcn_recommendation',
  'post_status' => 'publish',
  'post__in' => $args['post_ids'], // May be empty!
  'order' => $args['order'],
  'orderby' => $args['orderby'],
  'posts_per_page' => $args['count'],
  'no_found_rows' => true
);

// Author?
if ( isset( $args['author'] ) && $args['author'] ) {
  $query_args['author_name'] = $args['author'];
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

// Ignore protected?
if ( $args['ignore_protected'] ) {
  add_filter( 'posts_where', 'fictioneer_exclude_protected_posts' );
}

// Apply filters
$query_args = apply_filters( 'fictioneer_filter_shortcode_latest_recommendations_query_args', $query_args, $args );

// Query recommendations
$entries = fictioneer_shortcode_query( $query_args );

// Remove temporary filters
remove_filter( 'posts_where', 'fictioneer_exclude_protected_posts' );

?>

<section class="small-card-block latest-recommendations <?php echo $args['classes']; ?>">

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

          // Sources
          $urls = array_merge(
            explode( "\n", fictioneer_get_field( 'fictioneer_recommendation_urls' ) ),
            explode( "\n", fictioneer_get_field( 'fictioneer_recommendation_support' ) )
          );

          // Sanitize
          $urls = array_map( 'wp_strip_all_tags', $urls );

          // Remove empty nodes
          $urls = array_filter( $urls );

          // Abort if no links found
          if ( empty( $urls ) ) {
            continue;
          }

          // Extract first link
          $url = $urls[0];
          $tuple = explode( '|', $url );
          $tuple = array_map( 'trim', $tuple );
        ?>

        <li class="card _small">
          <div class="card__body polygon">

            <div class="card__main _grid _small">

              <?php if ( has_post_thumbnail() ) : ?>
                <a href="<?php the_post_thumbnail_url( 'full' ); ?>" title="<?php echo esc_attr( sprintf( __( '%s Thumbnail', 'fictioneer' ), $title ) ); ?>" class="card__image cell-img" <?php echo fictioneer_get_lightbox_attribute(); ?>>
                  <?php echo get_the_post_thumbnail( $post, 'snippet', ['class' => 'no-auto-lightbox'] ); ?>
                </a>
              <?php endif; ?>

              <h3 class="card__title _small cell-title"><a href="<?php the_permalink(); ?>" class="truncate _1-1"><?php echo $title; ?></a></h3>

              <div class="card__content _small cell-meta text-overflow-ellipsis">
                <span class="card__by-author"><?php
                  printf(
                    _x( 'by %s on', 'Small card: by {Author} on.', 'fictioneer' ),
                    '<span class="author">' . fictioneer_get_field( 'fictioneer_recommendation_author' ) . '</span>'
                  );
                ?></span>
                <a href="<?php echo esc_url( $tuple[1] ); ?>" rel="noopener" target="_blank" class="bold-link"><?php
                  echo $tuple[0];
                ?></a>
              </div>

              <div class="card__content _small cell-desc truncate _3-3">
                <?php
                  if ( ! empty( $one_sentence ) ) {
                    echo wp_strip_all_tags( $one_sentence, true );
                  } else {
                    echo fictioneer_first_paragraph_as_excerpt( get_the_content() );
                  }
                ?>
              </div>

              <?php if ( $show_taxonomies ) : ?>
                <div class="card__tag-list _small _scrolling cell-tax">
                  <div class="card__h-scroll">
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

          </div>
        </li>

      <?php endwhile; ?>
    </ul>

  <?php else: ?>

    <div class="no-results"><?php _e( 'Nothing to show.', 'fictioneer' ); ?></div>

  <?php endif; wp_reset_postdata(); ?>

</section>
