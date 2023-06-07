<?php
/**
 * Partial: Post Card
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0
 *
 * @internal $args['show_type']  Whether to show the post type label. Unsafe.
 * @internal $args['cache']      Whether to account for active caching. Unsafe.
 */
?>

<?php

// Setup
$title = fictioneer_get_safe_title( get_the_ID() );
$tags = get_the_tags();
$categories = wp_get_post_categories( get_the_ID() );
$show_type = isset( $args['show_type'] ) && $args['show_type'];

?>

<li id="post-card-<?php the_ID(); ?>" class="card">
  <div class="card__body polygon">

    <div class="card__header _large">
      <?php if ( $show_type ) : ?>
        <div class="card__label"><?php _ex( 'Blog', 'Blog card label.', 'fictioneer' ); ?></div>
      <?php endif; ?>
      <h3 class="card__title"><a href="<?php the_permalink(); ?>" class="truncate _1-1"><?php echo $title; ?></a></h3>
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

      <div class="card__content cell-desc truncate _4-4"><span><?php echo get_the_excerpt(); ?></span></div>

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
      <div class="card__left text-overflow-ellipsis">
        <?php if ( get_option( 'fictioneer_show_authors' ) ) : ?>
          <?php fictioneer_icon( 'user' ); ?>
          <?php fictioneer_the_author_node( get_the_author_meta( 'ID' ) ); ?>
        <?php endif; ?>
        <i class="fa-solid fa-clock" title="<?php esc_attr_e( 'Published', 'fictioneer' ) ?>"></i>
        <?php the_time( FICTIONEER_CARD_POST_FOOTER_DATE ); ?>
        <i class="fa-solid fa-message" title="<?php esc_attr_e( 'Comments', 'fictioneer' ) ?>"></i>
        <?php echo get_comments_number(); ?>
      </div>
    </div>

  </div>
</li>
