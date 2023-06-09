<?php
/**
 * Partial: Story Card
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0
 *
 * @internal $args['show_type']    Whether to show the post type label. Unsafe.
 * @internal $args['cache']        Whether to account for active caching. Unsafe.
 * @internal $args['hide_author']  Whether to hide the author. Unsafe.
 * @internal $args['show_latest']  Whether to show (up to) the latest 3 chapters. Unsafe.
 * @internal $args['order']        Current order. Default 'desc'. Unsafe.
 * @internal $args['orderby']      Current orderby. Default 'modified'. Unsafe.
 */
?>

<?php

// Setup
$story = fictioneer_get_story_data( $post->ID );
$tags = get_option( 'fictioneer_show_tags_on_story_cards' ) ? get_the_tags() : false;
$latest = isset( $args['show_latest'] ) && $args['show_latest'];
$chapter_ids = array_slice( $story['chapter_ids'], $latest ? -3 : 0, 3, true ); // Does not include hidden or non-chapters
$classes = ['card'];

// Sticky?
if ( fictioneer_get_field( 'fictioneer_story_sticky' ) ) {
  $classes[] = '_sticky';
}

// Flags
$hide_author = isset( $args['hide_author'] ) && $args['hide_author'];
$show_taxonomies = ! get_option( 'fictioneer_hide_taxonomies_on_story_cards' ) && ( $story['has_taxonomies'] || $tags );
$show_type = isset( $args['show_type'] ) && $args['show_type'];

?>

<li
  id="story-card-<?php the_ID(); ?>"
  class="<?php echo implode( ' ', $classes ); ?>"
  data-story-id="<?php echo $post->ID; ?>"
  data-check-id="<?php echo $post->ID; ?>"
>
  <div class="card__body polygon">

    <div class="card__header _large">

      <?php if ( $show_type ) : ?>
        <div class="card__label"><?php _ex( 'Story', 'Story card label.', 'fictioneer' ); ?></div>
      <?php endif; ?>

      <h3 class="card__title"><a href="<?php the_permalink(); ?>" class="truncate _1-1"><?php echo $story['title']; ?></a></h3>

      <?php if ( fictioneer_get_field( 'fictioneer_story_sticky' ) && ! is_search() && ! is_archive() ) : ?>
        <div class="card__sticky-icon" title="<?php echo esc_attr__( 'Sticky', 'fictioneer' ); ?>"><i class="fa-solid fa-thumbtack"></i></div>
      <?php endif; ?>

      <?php echo fictioneer_get_card_controls( get_the_ID() ); ?>

    </div>

    <div class="card__main _grid _large">

      <?php if ( has_post_thumbnail() ) : ?>
        <a
          href="<?php the_post_thumbnail_url( 'full' ); ?>"
          title="<?php printf( __( '%s Thumbnail', 'fictioneer' ), $story['title'] ) ?>"
          class="card__image cell-img"
          <?php echo fictioneer_get_lightbox_attribute(); ?>
        ><?php the_post_thumbnail( 'cover' ); ?></a>
      <?php endif; ?>

      <div class="card__content cell-desc truncate <?php echo count( $chapter_ids ) > 2 ? '_3-4' : '_4-4'; ?>">
        <?php if ( get_option( 'fictioneer_show_authors' ) && ! $hide_author ) : ?>
          <span class="card__by-author show-below-desktop"><?php
            printf( _x( 'by %s —', 'Small card: by {Author} —.', 'fictioneer' ), fictioneer_get_author_node() );
          ?></span>
        <?php endif; ?>
        <span><?php
          $excerpt = fictioneer_first_paragraph_as_excerpt( fictioneer_get_content_field( 'fictioneer_story_short_description' ) );
          echo empty( $excerpt ) ? __( 'No description provided yet.', 'fictioneer' ) : $excerpt;
        ?></span>
      </div>

      <?php if ( count( $chapter_ids ) > 0 ): ?>
        <ol class="card__link-list cell-list">
          <?php foreach ( $chapter_ids as $id ) : ?>
            <li>
              <div class="card__left text-overflow-ellipsis">
                <i class="fa-solid fa-caret-right"></i>
                <a href="<?php the_permalink( $id ); ?>"><?php
                  $list_title = fictioneer_get_field( 'fictioneer_chapter_list_title', $id );

                  if ( ! empty( $list_title ) ) {
                    echo wp_strip_all_tags( $list_title );
                  } else {
                    echo fictioneer_get_safe_title( $id );
                  }
                ?></a>
              </div>
              <div class="card__right">
                <?php
                  echo fictioneer_shorten_number( get_post_meta( $id, '_word_count', true ) );
                  echo '<span class="hide-below-480"> ';
                  echo __( 'Words', 'fictioneer' );
                  echo '</span><span class="separator-dot">&#8196;&bull;&#8196;</span>';

                  if ( strtotime( '-1 days' ) < strtotime( get_the_date( '', $id ) ) ) {
                    _e( 'New', 'fictioneer' );
                  } else {
                    echo get_the_time( FICTIONEER_CARD_STORY_LI_DATE, $id );
                  }
                ?>
              </div>
            </li>
          <?php endforeach; ?>
        </ol>
      <?php endif; ?>

      <?php if ( $show_taxonomies ) : ?>
        <div class="card__tag-list cell-tax">
          <?php
            $output = [];

            if ( $story['fandoms'] ) {
              foreach ( $story['fandoms'] as $fandom ) {
                $output[] = "<a href='" . get_tag_link( $fandom ) . "' class='tag-pill _inline _fandom'>{$fandom->name}</a>";
              }
            }

            if ( $story['genres'] ) {
              foreach ( $story['genres'] as $genre ) {
                $output[] = "<a href='" . get_tag_link( $genre ) . "' class='tag-pill _inline _genre'>{$genre->name}</a>";
              }
            }

            if ( $tags ) {
              foreach ( $tags as $tag ) {
                $output[] = "<a href='" . get_tag_link( $tag ) . "' class='tag-pill _inline'>{$tag->name}</a>";
              }
            }

            if ( $story['characters'] ) {
              foreach ( $story['characters'] as $character ) {
                $output[] = "<a href='" . get_tag_link( $character ) . "' class='tag-pill _inline _character'>{$character->name}</a>";
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

        <i class="fa-solid fa-list" title="<?php esc_attr_e( 'Chapters', 'fictioneer' ) ?>"></i>
        <?php echo $story['chapter_count']; ?>

        <i class="fa-solid fa-font" title="<?php esc_attr_e( 'Total Words', 'fictioneer' ) ?>"></i>
        <?php echo $story['word_count_short']; ?>

        <?php if ( ( $args['orderby'] ?? 0 ) === 'date' ) : ?>
          <i class="fa-solid fa-clock" title="<?php esc_attr_e( 'Published', 'fictioneer' ) ?>"></i>
          <?php the_date( FICTIONEER_CARD_STORY_FOOTER_DATE ); ?>
        <?php else : ?>
          <i class="fa-regular fa-clock" title="<?php esc_attr_e( 'Last Updated', 'fictioneer' ) ?>"></i>
          <?php the_modified_date( FICTIONEER_CARD_STORY_FOOTER_DATE ); ?>
        <?php endif; ?>

        <?php if ( get_option( 'fictioneer_show_authors' ) && ! $hide_author ) : ?>
          <?php fictioneer_icon( 'user', 'hide-below-desktop' ); ?>
          <?php fictioneer_the_author_node( get_the_author_meta( 'ID' ), 'hide-below-desktop' ); ?>
        <?php endif; ?>

        <i class="fa-solid fa-message hide-below-480" title="<?php esc_attr_e( 'Comments', 'fictioneer' ) ?>"></i>
        <span class="hide-below-480" title="<?php esc_attr_e( 'Comments', 'fictioneer' ) ?>"><?php echo $story['comment_count']; ?></span>

        <i class="<?php echo $story['icon']; ?>"></i>
        <?php echo fcntr( $story['status'] ); ?>

      </div>

      <div class="card__right rating-letter-label _large tooltipped" data-tooltip="<?php echo fcntr( $story['rating'], true ); ?>">
        <span class="hide-below-480"><?php echo fcntr( $story['rating'] ); ?></span>
        <span class="show-below-480"><?php echo fcntr( $story['rating_letter'] ); ?></span>
      </div>

    </div>

  </div>
</li>
