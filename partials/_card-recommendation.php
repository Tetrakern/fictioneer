<?php
/**
 * Partial: Recommendation Card
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0
 *
 * @internal $args['show_type']   Whether to show the post type label.
 * @internal $args['cache']       Whether to account for active caching.
 */
?>

<?php

// Setup
$title = fictioneer_get_safe_title( get_the_ID() );
$urls = array_merge(
  explode( "\n", fictioneer_get_field( 'fictioneer_recommendation_urls' ) ),
  explode( "\n", fictioneer_get_field( 'fictioneer_recommendation_support' ) )
);

// Sanitize
$urls = array_map( 'wp_strip_all_tags', $urls );

// Taxonomies
$fandoms = get_the_terms( $post->ID, 'fcn_fandom' );
$characters = get_the_terms( $post->ID, 'fcn_character' );
$genres = get_the_terms( $post->ID, 'fcn_genre' );
$tags = get_option( 'fictioneer_show_tags_on_recommendation_cards' ) ? get_the_tags() : false;

// Flags
$show_taxonomies = ! get_option( 'fictioneer_hide_taxonomies_on_recommendation_cards' ) && ( $tags || $genres || $fandoms || $characters );
$show_type = isset( $args['show_type'] ) && $args['show_type'];

?>

<li id="recommendation-card-<?php the_ID(); ?>" class="card">
  <div class="card__body polygon">

    <div class="card__header _large">
      <?php if ( $show_type ) : ?>
        <div class="card__label"><?php _ex( 'Recommendation', 'Recommendation card label.', 'fictioneer' ); ?></div>
      <?php endif; ?>
      <h3 class="card__title"><a href="<?php the_permalink(); ?>" class="truncate truncate--1-1"><?php echo $title; ?></a></h3>
    </div>

    <div class="card__main _grid _large">

      <?php if ( has_post_thumbnail() ) : ?>
        <a
          href="<?php the_post_thumbnail_url( 'full' ); ?>"
          title="<?php printf( __( '%s Thumbnail', 'fictioneer' ), $title ) ?>"
          class="card__image cell-img"
          <?php echo fictioneer_get_lightbox_attribute(); ?>
        ><?php the_post_thumbnail( 'cover' ); ?></a>
      <?php endif; ?>

      <div class="card__content cell-desc truncate truncate--4-4">
        <?php
          printf(
            __( '<span class="author-by">by</span> <span class="author">%s</span> <span>—</span> ', 'fictioneer' ),
            fictioneer_get_field( 'fictioneer_recommendation_author' )
          );
        ?>
        <span><?php
          $excerpt = get_the_excerpt();
          $one_sentence = fictioneer_get_field( 'fictioneer_recommendation_one_sentence' ) ?? '';
          echo strlen( $one_sentence ) < strlen( $excerpt ) ? $excerpt : wp_strip_all_tags( $one_sentence, true );
        ?></span>
      </div>

      <?php if ( count( $urls ) > 0 ): ?>
        <ol class="card__link-list cell-list">
          <?php foreach ( $urls as $url ) : ?>
            <li>
              <div class="card__left text-overflow-ellipsis">
                <i class="fa-solid fa-square-up-right"></i>
                <?php
                  $tuple = explode( '|', $url );
                  $tuple = array_map( 'trim', $tuple );
                ?>
                <a href="<?php echo esc_url( $tuple[1] ); ?>" rel="noopener" target="_blank"><?php echo $tuple[0]; ?></a>
              </div>
            </li>
          <?php endforeach; ?>
        </ol>
      <?php endif; ?>

      <?php if ( $show_taxonomies ) : ?>
        <div class="card__tag-list dot-separator cell-tax">
          <?php
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
          ?>
        </div>
      <?php endif; ?>

    </div>

  </div>
</li>
