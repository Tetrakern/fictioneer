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
 * @internal $args['count']    The number of posts provided by the shortcode.
 * @internal $args['author']   The author provided by the shortcode.
 * @internal $args['order']    Order of posts. Default 'desc'.
 * @internal $args['orderby']  Sorting of posts. Default 'date'.
 * @internal $args['post_ids'] Comma-separated list of recommendation IDs. Overrides count.
 */
?>

<?php

// Setup
$show_taxonomies = ! get_option( 'fictioneer_hide_taxonomies_on_recommendation_cards' );

// Prepare query
$query_args = array (
  'post_type' => 'fcn_recommendation',
  'post_status' => array( 'publish' ),
  'post__in' => $args['post_ids'],
  'orderby' => $args['orderby'] ?? 'date',
  'order' => $args['order'] ?? 'desc',
  'posts_per_page' => $args['count'],
  'no_found_rows' => true
);

// Parameter for author?
if ( isset( $args['author'] ) && $args['author'] ) $query_args['author_name'] = $args['author'];

// Query recommendations
$entries = new WP_Query( $query_args );

?>

<section class="small-card-block latest-recommendations">

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
          if ( empty( $urls ) ) continue;

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

              <h3 class="card__title _small cell-title"><a href="<?php the_permalink(); ?>" class="truncate truncate--1-1"><?php echo $title; ?></a></h3>

              <div class="card__content _small cell-meta text-overflow-ellipsis">
                <?php
                  printf(
                    __( '<span class="author-by">by</span> <span class="author">%s</span> <span>on</span> ', 'fictioneer' ),
                    fictioneer_get_field( 'fictioneer_recommendation_author' )
                  );

                  echo '<a href="' . esc_url( $tuple[1] ) . '" rel="noopener" target="_blank" class="bold-link">' . $tuple[0] . '</a>';
                ?>
              </div>

              <div class="card__content _small cell-desc truncate truncate--3-3">
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
                  <div class="dot-separator scroll-horizontal">
                    <?php
                      if ( $fandoms || $characters || $genres || $tags ) {
                        if ( $fandoms ) {
                          foreach ( $fandoms as $fandom ) {
                            echo '<span><a href="' . get_tag_link( $fandom ) . '" class="tag-pill _inline _fandom">' . $fandom->name . '</a></span>';
                          }
                        }

                        if ( $genres ) {
                          foreach ( $genres as $genre ) {
                            echo '<span><a href="' . get_tag_link( $genre ) . '" class="tag-pill _inline _genre">' . $genre->name . '</a></span>';
                          }
                        }

                        if ( $tags ) {
                          foreach ( $tags as $tag ) {
                            echo '<span><a href="' . get_tag_link( $tag ) . '" class="tag-pill _inline">' . $tag->name . '</a></span>';
                          }
                        }

                        if ( $characters ) {
                          foreach ( $characters as $character ) {
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

          </div>
        </li>

      <?php endwhile; ?>
    </ul>

  <?php else: ?>

    <div class="no-results"><?php _e( 'No recommendations have been published yet.', 'fictioneer' ) ?></div>

  <?php endif; wp_reset_postdata(); ?>

</section>
