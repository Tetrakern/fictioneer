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


// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$post_id = $post->ID;
$title = fictioneer_get_safe_title( $post_id, 'card-recommendation' );
$links = array_merge(
  fictioneer_url_list_to_array( get_post_meta( $post_id, 'fictioneer_recommendation_urls', true ) ),
  fictioneer_url_list_to_array( get_post_meta( $post_id, 'fictioneer_recommendation_support', true ) )
);
$excerpt = get_the_excerpt();
$one_sentence = get_post_meta( $post_id, 'fictioneer_recommendation_one_sentence', true );
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
  $fandoms = get_the_terms( $post_id, 'fcn_fandom' );
  $characters = get_the_terms( $post_id, 'fcn_character' );
  $genres = get_the_terms( $post_id, 'fcn_genre' );
}

// Flags
$show_terms = ! get_option( 'fictioneer_hide_taxonomies_on_recommendation_cards' ) && ( $tags || $genres || $fandoms || $characters );

// Extra classes
if ( get_theme_mod( 'card_style', 'default' ) !== 'default' ) {
  $card_classes[] = '_' . get_theme_mod( 'card_style' );
}

if ( get_theme_mod( 'card_image_style', 'default' ) !== 'default' ) {
  $card_classes[] = '_' . get_theme_mod( 'card_image_style' );
}

if ( ! $show_terms ) {
  $card_classes[] = '_no-tax';
}

// Card attributes
$attributes = apply_filters( 'fictioneer_filter_card_attributes', [], $post, 'card-recommendation' );
$card_attributes = '';

foreach ( $attributes as $key => $value ) {
  $card_attributes .= esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
}

// Thumbnail attributes
$thumbnail_args = array(
  'alt' => sprintf( __( '%s Thumbnail', 'fictioneer' ), $title ),
  'class' => 'no-auto-lightbox'
);

?>

<li id="recommendation-card-<?php echo $post_id; ?>" class="post-<?php echo $post_id; ?> card _recommendation _large _no-footer <?php echo implode( ' ', $card_classes ); ?>" <?php echo $card_attributes; ?>>
  <div class="card__body polygon">

    <div class="card__main _grid _large">

      <div class="card__header _large">
        <?php if ( $args['show_type'] ?? false ) : ?>
          <div class="card__label"><?php _ex( 'Recommendation', 'Recommendation card label.', 'fictioneer' ); ?></div>
        <?php endif; ?>
        <h3 class="card__title"><a href="<?php the_permalink(); ?>" class="truncate _1-1"><?php echo $title; ?></a></h3>
      </div>

      <?php
        // Action hook
        do_action( 'fictioneer_large_card_body_recommendation', $post, $args );

        // Thumbnail
        if ( has_post_thumbnail() && get_theme_mod( 'card_image_style', 'default' ) !== 'none' ) {
          printf(
            '<a href="%1$s" title="%2$s" class="card__image cell-img" %3$s>%4$s</a>',
            get_the_post_thumbnail_url( null, 'full' ),
            sprintf( __( '%s Thumbnail', 'fictioneer' ), $title ),
            fictioneer_get_lightbox_attribute(),
            get_the_post_thumbnail( null, 'cover', $thumbnail_args )
          );
        }

        // Content
        printf(
          '<div class="card__content cell-desc"><div class="truncate %s"><span class="card__by-author">%s</span> <span>%s</span></div></div>',
          count( $links ) > 3 ? '_3-4' : '_4-4',
          sprintf(
            _x( 'by %s —', 'Large card: by {Author} —.', 'fictioneer' ),
            get_post_meta( $post_id, 'fictioneer_recommendation_author', true )
          ),
          mb_strlen( $one_sentence ) < mb_strlen( $excerpt ) ? $excerpt : wp_strip_all_tags( $one_sentence, true )
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

      <?php
        if ( $show_terms ) {
          $terms = array_merge(
            $fandoms ? fictioneer_get_term_nodes( $fandoms, '_inline _fandom' ) : [],
            $genres ? fictioneer_get_term_nodes( $genres, '_inline _genre' ) : [],
            $tags ? fictioneer_get_term_nodes( $tags, '_inline _tag' ) : [],
            $characters ? fictioneer_get_term_nodes( $characters, '_inline _character' ) : []
          );

          $terms = apply_filters(
            'fictioneer_filter_card_recommendation_terms',
            $terms, $post, $args, null
          );
        }
      ?>

      <?php if ( $show_terms && $terms ) : ?>
        <div class="card__tag-list cell-tax"><?php
          // Implode with separator
          echo implode( fictioneer_get_bullet_separator( 'recommendation-card' ), $terms );
        ?></div>
      <?php endif; ?>

    </div>

  </div>
</li>
