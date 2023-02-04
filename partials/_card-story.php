<?php
/**
 * Partial: Story Card
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0
 *
 * @internal $args['show_type']   Whether to show the post type label.
 * @internal $args['cache']       Whether to account for active caching.
 * @internal $args['hide_author'] Whether to hide the author.
 * @internal $args['show_latest'] Whether to show (up to) the latest 3 chapters.
 */
?>

<?php

// Setup
$story = fictioneer_get_story_data( $post->ID );
$tags = get_option( 'fictioneer_show_tags_on_story_cards' ) ? get_the_tags() : false;
$latest = isset( $args['show_latest'] ) && $args['show_latest'];
$chapter_ids = array_slice( $story['chapter_ids'], $latest ? -3 : 0, 3, true ); // Does not include hidden or non-chapters

// Flags
$hide_author = isset( $args['hide_author'] ) && $args['hide_author'];
$show_taxonomies = ! get_option( 'fictioneer_hide_taxonomies_on_story_cards' ) && ( $story['has_taxonomies'] || $tags );
$show_type = isset( $args['show_type'] ) && $args['show_type'];

?>

<li id="story-card-<?php the_ID(); ?>" class="card <?php if ( fictioneer_get_field( 'fictioneer_story_sticky' ) ) echo 'sticky'; ?>">
  <div class="card__body polygon">

    <div class="card__header _large">

      <?php if ( $show_type ) : ?>
        <div class="card__label"><?php _ex( 'Story', 'Story card label.', 'fictioneer' ); ?></div>
      <?php endif; ?>

      <h3 class="card__title"><a href="<?php the_permalink(); ?>" class="truncate truncate--1-1"><?php echo $story['title']; ?></a></h3>

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

      <div class="card__content cell-desc truncate <?php echo count( $chapter_ids ) > 2 ? 'truncate--3-4' : 'truncate--4-4'; ?>">
        <?php if ( get_option( 'fictioneer_show_authors' ) && ! $hide_author ) : ?>
          <span class="show-below-desktop"><?php
            printf(
              __( '<span class="author-by">by</span> %s <span>—</span> ', 'fictioneer' ),
              fictioneer_get_author_node()
            );
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
              <div class="card__right _flex dot-separator-inverse">
                <span><?php
                  printf(
                    __( '<span>%s</span><span class="hide-below-480"> Words</span>', 'fictioneer' ),
                    fictioneer_shorten_number( get_post_meta( $id, '_word_count', true ) )
                  );
                ?></span>
                <span><?php
                  if ( strtotime( '-1 days' ) < strtotime( get_the_date( '', $id ) ) ) {
                    _e( 'New', 'fictioneer' );
                  } else {
                    echo get_the_time( get_option( 'fictioneer_subitem_date_format', 'M j, y' ), $id );
                  }
                ?></span>
              </div>
            </li>
          <?php endforeach; ?>
        </ol>
      <?php endif; ?>

      <?php if ( $show_taxonomies ) : ?>
        <div class="card__tag-list dot-separator cell-tax">
          <?php
            if ( $story['fandoms'] ) {
              foreach ( $story['fandoms'] as $fandom ) {
                echo '<span><a href="' . get_tag_link( $fandom ) . '" class="tag-pill _inline _fandom">' . $fandom->name . '</a></span>';
              }
            }

            if ( $story['genres'] ) {
              foreach ( $story['genres'] as $genre ) {
                echo '<span><a href="' . get_tag_link( $genre ) . '" class="tag-pill _inline _genre">' . $genre->name . '</a></span>';
              }
            }

            if ( $tags ) {
              foreach ( $tags as $tag ) {
                echo '<span><a href="' . get_tag_link( $tag ) . '" class="tag-pill _inline">' . $tag->name . '</a></span>';
              }
            }

            if ( $story['characters'] ) {
              foreach ( $story['characters'] as $character ) {
                echo '<span><a href="' . get_tag_link( $character ) . '" class="tag-pill _inline _character">' . $character->name . '</a></span>';
              }
            }
          ?>
        </div>
      <?php endif; ?>

    </div>

    <div class="card__footer">

      <div class="card__left text-overflow-ellipsis">
        <i class="fa-solid fa-list" title="<?php esc_attr_e( 'Chapters', 'fictioneer' ) ?>"></i>
        <span title="<?php esc_attr_e( 'Chapters', 'fictioneer' ) ?>"><?php echo $story['chapter_count']; ?></span>

        <i class="fa-solid fa-font" title="<?php esc_attr_e( 'Total Words', 'fictioneer' ) ?>"></i>
        <span title="<?php esc_attr_e( 'Total Words', 'fictioneer' ) ?>"><?php echo $story['word_count_short']; ?></span>

        <i class="fa-regular fa-clock" title="<?php esc_attr_e( 'Last Updated', 'fictioneer' ) ?>"></i>
        <span title="<?php esc_attr_e( 'Last Updated', 'fictioneer' ) ?>"><?php the_modified_date( get_option( 'fictioneer_subitem_date_format', 'M j, y' ) ); ?></span>

        <?php if ( get_option( 'fictioneer_show_authors' ) && ! $hide_author ) : ?>
          <?php fictioneer_icon( 'user', 'hide-below-desktop' ); ?>
          <?php fictioneer_the_author_node( get_the_author_meta( 'ID' ), 'hide-below-desktop' ); ?>
        <?php endif; ?>

        <i class="fa-solid fa-message hide-below-480" title="<?php esc_attr_e( 'Comments', 'fictioneer' ) ?>"></i>
        <span class="hide-below-480" title="<?php esc_attr_e( 'Comments', 'fictioneer' ) ?>"><?php echo $story['comment_count']; ?></span>

        <i class="<?php echo $story['icon']; ?>"></i>
        <span><?php echo fcntr( $story['status'] ); ?></span>
      </div>

      <div class="card__right rating-letter-label _large tooltipped" data-tooltip="<?php echo fcntr( $story['rating'], true ); ?>">
        <span class="hide-below-480"><?php echo fcntr( $story['rating'] ); ?></span>
        <span class="show-below-480"><?php echo fcntr( $story['rating_letter'] ); ?></span>
      </div>

    </div>

  </div>
</li>
