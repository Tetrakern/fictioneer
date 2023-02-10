<?php
/**
 * Partial: Page Card
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.0
 *
 * @internal $args['show_type'] Whether to show the post type label.
 * @internal $args['cache']     Whether to account for active caching.
 */
?>

<?php

// Setup
$title = fictioneer_get_safe_title( get_the_ID() );
$show_type = isset( $args['show_type'] ) && $args['show_type'];
$comments_number = get_comments_number();

?>

<li id="post-card-<?php the_ID(); ?>" class="card">
  <div class="card__body polygon">

    <div class="card__header _large">
      <?php if ( $show_type ) : ?>
        <div class="card__label"><?php _ex( 'Page', 'Blog card label.', 'fictioneer' ); ?></div>
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

    </div>

    <div class="card__footer">
      <div class="card__left text-overflow-ellipsis">
        <?php if ( get_option( 'fictioneer_show_authors' ) ) : ?>
          <?php fictioneer_icon( 'user' ); ?>
          <?php fictioneer_the_author_node( get_the_author_meta( 'ID' ) ); ?>
        <?php endif; ?>
        <i class="fa-solid fa-clock" title="<?php esc_attr_e( 'Publishing Date', 'fictioneer' ) ?>"></i>
        <span title="<?php esc_attr_e( 'Publishing Date', 'fictioneer' ) ?>"><?php the_time( get_option( 'fictioneer_subitem_date_format', 'M j, y' ) ); ?></span>
        <?php if ( $comments_number > 0 ) : ?>
          <i class="fa-solid fa-message" title="<?php esc_attr_e( 'Comments', 'fictioneer' ) ?>"></i>
          <span title="<?php esc_attr_e( 'Comments', 'fictioneer' ) ?>"><?php echo $comments_number; ?></span>
        <?php endif; ?>
      </div>
    </div>

  </div>
</li>
