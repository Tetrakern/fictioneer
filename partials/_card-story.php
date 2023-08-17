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
$latest = $args['show_latest'] ?? false;
$chapter_ids = array_slice( $story['chapter_ids'], $latest ? -3 : 0, 3, true ); // Does not include hidden or non-chapters
$chapter_count = count( $chapter_ids );
$excerpt = fictioneer_first_paragraph_as_excerpt( fictioneer_get_content_field( 'fictioneer_story_short_description' ) );
$excerpt = empty( $excerpt ) ? __( 'No description provided yet.', 'fictioneer' ) : $excerpt;
$tags = false;

if (
  get_option( 'fictioneer_show_tags_on_story_cards' ) &&
  ! get_option( 'fictioneer_hide_taxonomies_on_story_cards' )
) {
  $tags = get_the_tags();
}

// Flags
$hide_author = $args['hide_author'] ?? false && ! get_option( 'fictioneer_show_authors' );
$show_taxonomies = ! get_option( 'fictioneer_hide_taxonomies_on_story_cards' ) && ( $story['has_taxonomies'] || $tags );
$show_type = $args['show_type'] ?? false;
$is_sticky = FICTIONEER_ENABLE_STICKY_CARDS &&
  fictioneer_get_field( 'fictioneer_story_sticky' ) && ! is_search() && ! is_archive();

?>

<li
  id="story-card-<?php the_ID(); ?>"
  class="card <?php echo $is_sticky ? '_sticky' : ''; ?>"
  data-story-id="<?php echo $post->ID; ?>"
  data-check-id="<?php echo $post->ID; ?>"
>
  <div class="card__body polygon">

    <div class="card__header _large">

      <?php if ( $show_type ) : ?>
        <div class="card__label"><?php _ex( 'Story', 'Story card label.', 'fictioneer' ); ?></div>
      <?php endif; ?>

      <h3 class="card__title"><a href="<?php the_permalink(); ?>" class="truncate _1-1"><?php echo $story['title']; ?></a></h3>

      <?php if ( $is_sticky ) : ?>
        <div class="card__sticky-icon" title="<?php echo esc_attr__( 'Sticky', 'fictioneer' ); ?>"><i class="fa-solid fa-thumbtack"></i></div>
      <?php endif; ?>

      <?php echo fictioneer_get_card_controls( get_the_ID() ); ?>

    </div>

    <div class="card__main _grid _large">

      <?php
        // Thumbnail
        if ( has_post_thumbnail() ) {
          printf(
            '<a href="%1$s" title="%2$s" class="card__image cell-img" %3$s>%4$s</a>',
            get_the_post_thumbnail_url( null, 'full' ),
            sprintf( __( '%s Thumbnail', 'fictioneer' ), $story['title'] ),
            fictioneer_get_lightbox_attribute(),
            get_the_post_thumbnail( null, 'cover' )
          );
        }

        // Content
        printf(
          '<div class="card__content cell-desc truncate %1$s">%2$s<span>%3$s</span></div>',
          $chapter_count > 2 ? '_3-4' : '_4-4',
          $hide_author ? '' : sprintf(
            '<span class="card__by-author show-below-desktop">%s</span> ',
            sprintf(
              _x( 'by %s —', 'Large card: by {Author} —.', 'fictioneer' ),
              fictioneer_get_author_node()
            )
          ),
          $excerpt
        );
      ?>

      <?php if ( $chapter_count > 0 ): ?>
        <ol class="card__link-list cell-list">
          <?php
            // Prepare
            $chapter_query_args = array(
              'post_type' => 'fcn_chapter',
              'post_status' => 'publish',
              'post__in' => fictioneer_save_array_zero( $chapter_ids ),
              'posts_per_page' => -1,
              'no_found_rows' => true, // Improve performance
              'update_post_term_cache' => false // Improve performance
            );

            $chapters = new WP_Query( $chapter_query_args );
          ?>
          <?php foreach ( $chapters->posts as $chapter ) : ?>
            <li>
              <div class="card__left text-overflow-ellipsis">
                <i class="fa-solid fa-caret-right"></i>
                <a href="<?php the_permalink( $chapter->ID ); ?>"><?php
                  $list_title = fictioneer_get_field( 'fictioneer_chapter_list_title', $chapter->ID );

                  if ( empty( $list_title ) ) {
                    echo fictioneer_get_safe_title( $chapter->ID );
                  } else {
                    echo wp_strip_all_tags( $list_title );
                  }
                ?></a>
              </div>
              <div class="card__right">
                <?php
                  printf(
                    '%1$s<span class="hide-below-480"> %2$s</span><span class="separator-dot">&#8196;&bull;&#8196;</span>%3$s',
                    fictioneer_shorten_number( get_post_meta( $chapter->ID, '_word_count', true ) ),
                    __( 'Words', 'fictioneer' ),
                    strtotime( '-1 days' ) < strtotime( get_the_date( '', $chapter->ID ) ) ?
                      __( 'New', 'fictioneer' ) : get_the_time( FICTIONEER_CARD_STORY_LI_DATE, $chapter->ID )
                  );
                ?>
              </div>
            </li>
          <?php endforeach; ?>
        </ol>
      <?php endif; ?>

      <?php if ( $show_taxonomies ) : ?>
        <div class="card__tag-list cell-tax">
          <?php
            $taxonomies = array_merge(
              $story['fandoms'] ? fictioneer_generate_card_tags( $story['fandoms'], '_fandom' ) : [],
              $story['genres'] ? fictioneer_generate_card_tags( $story['genres'], '_genre' ) : [],
              $tags ? fictioneer_generate_card_tags( $tags ) : [],
              $story['characters'] ? fictioneer_generate_card_tags( $story['characters'], '_character' ) : []
            );

            // Implode with three-per-em spaces around a bullet
            echo implode( '&#8196;&bull;&#8196;', $taxonomies );
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
          <?php echo get_the_date( FICTIONEER_CARD_CHAPTER_FOOTER_DATE ); ?>
        <?php else : ?>
          <i class="fa-regular fa-clock" title="<?php esc_attr_e( 'Last Updated', 'fictioneer' ) ?>"></i>
          <?php the_modified_date( FICTIONEER_CARD_STORY_FOOTER_DATE ); ?>
        <?php endif; ?>

        <?php if ( ! $hide_author ) : ?>
          <i class="fa-solid fa-circle-user hide-below-desktop"></i>
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
