<?php
/**
 * Partial: Post Card
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0
 *
 * @internal $args['show_type'] Whether to show the post type label.
 * @internal $args['cache']     Whether to account for active caching.
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
        <div class="card__tag-list dot-separator cell-tax">
          <?php
            if ( $categories ) {
              foreach ( $categories as $cat ) {
                echo '<span><a href="' . get_category_link( $cat ) . '" class="tag-pill _inline _category">' . get_category( $cat )->name . '</a></span>';
              }
            }

            if ( $tags ) {
              foreach ( $tags as $tag ) {
                echo '<span><a href="' . get_tag_link( $tag ) . '" class="tag-pill _inline">' . $tag->name . '</a></span>';
              }
            }
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
        <i class="fa-solid fa-clock" title="<?php esc_attr_e( 'Publishing Date', 'fictioneer' ) ?>"></i>
        <span title="<?php esc_attr_e( 'Publishing Date', 'fictioneer' ) ?>"><?php the_time( get_option( 'fictioneer_subitem_date_format', 'M j, y' ) ); ?></span>
        <i class="fa-solid fa-message" title="<?php esc_attr_e( 'Comments', 'fictioneer' ) ?>"></i>
        <span title="<?php esc_attr_e( 'Comments', 'fictioneer' ) ?>"><?php echo get_comments_number(); ?></span>
      </div>
    </div>

  </div>
</li>
