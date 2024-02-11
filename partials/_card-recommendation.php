<?php
/**
 * Partial: Recommendation Card
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0.0
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
$title = fictioneer_get_safe_title( $post->ID );
$links = array_merge(
  fictioneer_url_list_to_array( get_post_meta( $post->ID, 'fictioneer_recommendation_urls', true ) ),
  fictioneer_url_list_to_array( get_post_meta( $post->ID, 'fictioneer_recommendation_support', true ) )
);
$excerpt = get_the_excerpt();
$one_sentence = get_post_meta( $post->ID, 'fictioneer_recommendation_one_sentence', true );
$card_classes = [];

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

?>

<li id="recommendation-card-<?php the_ID(); ?>" class="card _large _recommendation <?php echo implode( ' ', $card_classes ); ?>">
  <div class="card__body polygon">

    <div class="card__header _large">
      <?php if ( $args['show_type'] ?? false ) : ?>
        <div class="card__label"><?php _ex( 'Recommendation', 'Recommendation card label.', 'fictioneer' ); ?></div>
      <?php endif; ?>
      <h3 class="card__title"><a href="<?php the_permalink(); ?>" class="truncate _1-1"><?php echo $title; ?></a></h3>
    </div>

    <div class="card__main _grid _large">

      <?php
        // Action hook
        do_action( 'fictioneer_large_card_body_recommendation', $post, $args );

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
          '<div class="card__content cell-desc"><div class="truncate _4-4"><span class="card__by-author">%1$s</span> <span>%2$s</span></div></div>',
          sprintf(
            _x( 'by %s —', 'Large card: by {Author} —.', 'fictioneer' ),
            get_post_meta( $post->ID, 'fictioneer_recommendation_author', true )
          ),
          strlen( $one_sentence ) < strlen( $excerpt ) ? $excerpt : wp_strip_all_tags( $one_sentence, true )
        );
      ?>

      <?php if ( count( $links ) > 0 ): ?>
        <ol class="card__link-list cell-list">
          <?php foreach ( $links as $link ) : ?>
            <li class="card__link-list-item">
              <div class="card__left text-overflow-ellipsis">
                <i class="fa-solid fa-square-up-right"></i>
                <a href="<?php echo esc_url( $link['url'] ); ?>" rel="noopener" target="_blank" class="card__link-list-link"><?php echo $link['name']; ?></a>
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
