<?php
/**
 * Partial: Recommendation Card
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0
 *
 * @internal $args['show_type']  Whether to show the post type label. Unsafe.
 * @internal $args['cache']      Whether to account for active caching. Unsafe.
 * @internal $args['order']      Current order. Default 'desc'. Unsafe.
 * @internal $args['orderby']    Current orderby. Default 'modified'. Unsafe.
 */
?>

<?php

// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$title = fictioneer_get_safe_title( get_the_ID() );
$urls = array_merge(
  explode( "\n", fictioneer_get_field( 'fictioneer_recommendation_urls' ) ),
  explode( "\n", fictioneer_get_field( 'fictioneer_recommendation_support' ) )
);
$excerpt = get_the_excerpt();
$one_sentence = fictioneer_get_field( 'fictioneer_recommendation_one_sentence' ) ?? '';

// Sanitize
$urls = array_map( 'wp_strip_all_tags', $urls );

// Taxonomies
$tags = false;
$fandoms = false;
$characters = false;
$genres = false;

if (
  get_option( 'fictioneer_show_tags_on_recommendation_cards' ) &&
  ! get_option( 'fictioneer_hide_taxonomies_on_recommendation_cards' )
) {
  $tags = get_the_tags();
}

if ( ! get_option( 'fictioneer_hide_taxonomies_on_recommendation_cards' ) ) {
  $fandoms = get_the_terms( $post->ID, 'fcn_fandom' );
  $characters = get_the_terms( $post->ID, 'fcn_character' );
  $genres = get_the_terms( $post->ID, 'fcn_genre' );
}

// Flags
$show_taxonomies = ! get_option( 'fictioneer_hide_taxonomies_on_recommendation_cards' ) && ( $tags || $genres || $fandoms || $characters );
$show_type = $args['show_type'] ?? false;

?>

<li id="recommendation-card-<?php the_ID(); ?>" class="card">
  <div class="card__body polygon">

    <div class="card__header _large">
      <?php if ( $show_type ) : ?>
        <div class="card__label"><?php _ex( 'Recommendation', 'Recommendation card label.', 'fictioneer' ); ?></div>
      <?php endif; ?>
      <h3 class="card__title"><a href="<?php the_permalink(); ?>" class="truncate _1-1"><?php echo $title; ?></a></h3>
    </div>

    <div class="card__main _grid _large">

      <?php
        // Thumbnail
        if ( has_post_thumbnail() ) {
          printf(
            '<a href="%1$s" title="%2$s" class="card__image cell-img" %3$s>%4$s</a>',
            get_the_post_thumbnail_url( null, 'full' ),
            sprintf( __( '%s Thumbnail', 'fictioneer' ), $title ),
            fictioneer_get_lightbox_attribute(),
            get_the_post_thumbnail( null, 'cover' )
          );
        }

        // Content
        printf(
          '<div class="card__content cell-desc truncate _4-4"><span class="card__by-author">%1$s</span> <span>%2$s</span></div>',
          sprintf(
            _x( 'by %s —', 'Large card: by {Author} —.', 'fictioneer' ),
            fictioneer_get_field( 'fictioneer_recommendation_author' )
          ),
          strlen( $one_sentence ) < strlen( $excerpt ) ? $excerpt : wp_strip_all_tags( $one_sentence, true )
        );
      ?>

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
        <div class="card__tag-list cell-tax">
          <?php
            $taxonomies = array_merge(
              $fandoms ? fictioneer_generate_card_tags( $fandoms, '_fandom' ) : [],
              $genres ? fictioneer_generate_card_tags( $genres, '_genre' ) : [],
              $tags ? fictioneer_generate_card_tags( $tags ) : [],
              $characters ? fictioneer_generate_card_tags( $characters, '_character' ) : []
            );

            // Implode with three-per-em spaces around a bullet
            echo implode( '&#8196;&bull;&#8196;', $taxonomies );
          ?>
        </div>
      <?php endif; ?>

    </div>

  </div>
</li>
