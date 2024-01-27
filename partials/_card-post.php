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
?>

<?php

// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$title = fictioneer_get_safe_title( $post->ID );
$tags = get_the_tags();
$categories = wp_get_post_categories( $post->ID );

?>

<li id="post-card-<?php echo $post->ID; ?>" class="card">
  <div class="card__body polygon">

    <div class="card__header _large">
      <?php if ( $args['show_type'] ?? false ) : ?>
        <div class="card__label"><?php _ex( 'Blog', 'Blog card label.', 'fictioneer' ); ?></div>
      <?php endif; ?>
      <h3 class="card__title"><a href="<?php the_permalink(); ?>" class="truncate _1-1"><?php echo $title; ?></a></h3>
    </div>

    <div class="card__main _grid _large">

      <?php
        // Action hook
        do_action( 'fictioneer_large_card_body_post', $post, $args );

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
      ?>

      <div class="card__content cell-desc"><div class="truncate _4-4"><span><?php the_excerpt(); ?></span></div></div>

      <?php if ( $categories || $tags ) : ?>
        <div class="card__tag-list cell-tax">
          <?php
            $output = [];

            if ( $categories ) {
              foreach ( $categories as $cat ) {
                $output[] = '<a href="' . get_category_link( $cat ) . '" class="tag-pill _inline _category">' . get_category( $cat )->name . '</a>';
              }
            }

            if ( $tags ) {
              foreach ( $tags as $tag ) {
                $output[] = "<a href='" . get_tag_link( $tag ) . "' class='tag-pill _inline'>{$tag->name}</a>";
              }
            }

            // Implode with three-per-em spaces around a bullet
            echo implode( '&#8196;&bull;&#8196;', $output );
          ?>
        </div>
      <?php endif; ?>

    </div>

    <div class="card__footer">

      <div class="card__footer-box text-overflow-ellipsis"><?php
        // Build footer items
        $footer_items = [];

        if ( get_option( 'fictioneer_show_authors' ) ) {
          $footer_items['author'] = '<i class="card-footer-icon fa-solid fa-circle-user"></i> ' .
            fictioneer_get_author_node( get_the_author_meta( 'ID' ) );
        }

        $footer_items['publish_date'] = '<i class="card-footer-icon fa-solid fa-clock" title="' .
          esc_attr__( 'Published', 'fictioneer' ) .'"></i> ' . get_the_date( FICTIONEER_CARD_POST_FOOTER_DATE );

        $footer_items['comments'] = '<i class="card-footer-icon fa-solid fa-message" title="' .
          esc_attr__( 'Comments', 'fictioneer' ) . '"></i> ' . get_comments_number( $post );

        // Filer footer items
        $footer_items = apply_filters( 'fictioneer_filter_post_card_footer', $footer_items, $post, $args );

        // Implode and render footer items
        echo implode( ' ', $footer_items );
      ?></div>

    </div>

  </div>
</li>
