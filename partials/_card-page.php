<?php
/**
 * Partial: Page Card
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.0
 *
 * @internal $args['show_type']  Whether to show the post type label. Unsafe.
 * @internal $args['cache']      Whether to account for active caching. Unsafe.
 */
?>

<?php

// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$title = fictioneer_get_safe_title( get_the_ID() );
$show_type = $args['show_type'] ?? false;
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
      ?>

      <div class="card__content cell-desc truncate _4-4"><span><?php the_excerpt(); ?></span></div>

    </div>

    <div class="card__footer">

      <div class="card__left text-overflow-ellipsis">

        <?php if ( get_option( 'fictioneer_show_authors' ) ) : ?>
          <i class="fa-solid fa-circle-user"></i>
          <?php fictioneer_the_author_node( get_the_author_meta( 'ID' ) ); ?>
        <?php endif; ?>

        <i class="fa-solid fa-clock" title="<?php esc_attr_e( 'Published', 'fictioneer' ) ?>"></i>
        <?php the_time( FICTIONEER_CARD_PAGE_FOOTER_DATE ); ?>

        <?php if ( $comments_number > 0 ) : ?>
          <i class="fa-solid fa-message" title="<?php esc_attr_e( 'Comments', 'fictioneer' ) ?>"></i>
          <?php echo $comments_number; ?>
        <?php endif; ?>

      </div>

    </div>

  </div>
</li>
