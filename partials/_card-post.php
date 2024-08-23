<?php
/**
 * Partial: Post Card
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0.0
 *
 * @internal $args['show_type']  Whether to show the post type label. Unsafe.
 * @internal $args['cache']      Whether to account for active caching. Unsafe.
 */


// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$post_id = $post->ID;
$title = fictioneer_get_safe_title( $post_id, 'card-post' );
$tags = get_the_tags();
$categories = get_the_terms( $post_id, 'category' );
$card_classes = [];

// Extra classes
if ( get_theme_mod( 'card_style', 'default' ) !== 'default' ) {
  $card_classes[] = '_' . get_theme_mod( 'card_style' );
}

if ( get_theme_mod( 'card_image_style', 'default' ) !== 'default' ) {
  $card_classes[] = '_' . get_theme_mod( 'card_image_style' );
}

// Card attributes
$attributes = apply_filters( 'fictioneer_filter_card_attributes', [], $post, 'card-post' );
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

<li id="post-card-<?php echo $post_id; ?>" class="post-<?php echo $post_id; ?> card _large _post <?php echo implode( ' ', $card_classes ); ?>" <?php echo $card_attributes; ?>>
  <div class="card__body polygon">

    <div class="card__main _grid _large">

      <div class="card__header _large">
        <?php if ( $args['show_type'] ?? false ) : ?>
          <div class="card__label"><?php _ex( 'Blog', 'Blog card label.', 'fictioneer' ); ?></div>
        <?php endif; ?>
        <h3 class="card__title"><a href="<?php the_permalink(); ?>" class="truncate _1-1"><?php echo $title; ?></a></h3>
      </div>

      <?php
        // Action hook
        do_action( 'fictioneer_large_card_body_post', $post, $args );

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
      ?>

      <div class="card__content cell-desc"><div class="truncate _4-4"><span><?php the_excerpt(); ?></span></div></div>

      <?php
        $terms = [];

        if ( $categories || $tags ) {
          $terms = array_merge(
            $categories ? fictioneer_get_term_nodes( $categories, '_inline _category' ) : [],
            $tags ? fictioneer_get_term_nodes( $tags, '_inline _tag' ) : []
          );

          $terms = apply_filters(
            'fictioneer_filter_card_post_terms',
            $terms, $post, $args, null
          );
        }
      ?>

      <?php if ( $terms ) : ?>
        <div class="card__tag-list cell-tax"><?php
          // Implode with separator
          echo implode( fictioneer_get_bullet_separator( 'post-card' ), $terms );
        ?></div>
      <?php endif; ?>

      <div class="card__footer cell-footer">

        <div class="card__footer-box text-overflow-ellipsis"><?php
          // Build footer items
          $footer_items = [];

          if ( get_option( 'fictioneer_show_authors' ) ) {
            $footer_items['author'] = '<span class="card__footer-author"><i class="card-footer-icon fa-solid fa-circle-user"></i> ' . fictioneer_get_author_node( get_the_author_meta( 'ID' ) ) . '</span>';
          }

          $footer_items['publish_date'] = '<span class="card__footer-publish-date"><i class="card-footer-icon fa-solid fa-clock" title="' . esc_attr__( 'Published', 'fictioneer' ) .'"></i> ' . get_the_date( FICTIONEER_CARD_POST_FOOTER_DATE ) . '</span>';

          $footer_items['comments'] = '<span class="card__footer-comments"><i class="card-footer-icon fa-solid fa-message" title="' . esc_attr__( 'Comments', 'fictioneer' ) . '"></i> ' . get_comments_number( $post ) . '</span>';

          // Filter footer items
          $footer_items = apply_filters( 'fictioneer_filter_post_card_footer', $footer_items, $post, $args );

          // Implode and render footer items
          echo implode( ' ', $footer_items );
        ?></div>

      </div>

    </div>

  </div>
</li>
