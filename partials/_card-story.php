<?php
/**
 * Partial: Story Card
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0.0
 *
 * @internal $args['show_type']    Whether to show the post type label. Unsafe.
 * @internal $args['cache']        Whether to account for active caching. Unsafe.
 * @internal $args['hide_author']  Whether to hide the author. Unsafe.
 * @internal $args['show_latest']  Whether to show (up to) the latest 3 chapters. Unsafe.
 * @internal $args['order']        Current order. Default 'desc'. Unsafe.
 * @internal $args['orderby']      Current orderby. Default 'modified'. Unsafe.
 */


// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$story = fictioneer_get_story_data( $post->ID );
$story_link = ( $story['redirect'] ?? 0 ) ?: get_permalink( $post->ID );
$latest = $args['show_latest'] ?? false;
$chapter_ids = array_slice( $story['chapter_ids'], $latest ? -3 : 0, 3, true ); // Does not include hidden or non-chapters
$chapter_count = count( $chapter_ids );
$excerpt = fictioneer_first_paragraph_as_excerpt(
  fictioneer_get_content_field( 'fictioneer_story_short_description', $post->ID )
);
$excerpt = empty( $excerpt ) ? __( 'No description provided yet.', 'fictioneer' ) : $excerpt;
$tags = false;
$card_classes = [];

if (
  get_option( 'fictioneer_show_tags_on_story_cards' ) &&
  ! get_option( 'fictioneer_hide_taxonomies_on_story_cards' )
) {
  $tags = get_the_tags();
}

// Flags
$hide_author = $args['hide_author'] ?? false && ! get_option( 'fictioneer_show_authors' );
$show_taxonomies = ! get_option( 'fictioneer_hide_taxonomies_on_story_cards' ) && ( $story['has_taxonomies'] || $tags );
$is_sticky = FICTIONEER_ENABLE_STICKY_CARDS &&
  get_post_meta( $post->ID, 'fictioneer_story_sticky', true ) && ! is_search() && ! is_archive();

// Extra classes
if ( $is_sticky ) {
  $card_classes[] = '_sticky';
}

if ( get_theme_mod( 'card_style', 'default' ) !== 'default' ) {
  $card_classes[] = '_' . get_theme_mod( 'card_style' );
}

if ( get_theme_mod( 'card_image_style', 'default' ) !== 'default' ) {
  $card_classes[] = '_' . get_theme_mod( 'card_image_style' );
}

// Card attributes
$attributes = apply_filters( 'fictioneer_filter_card_attributes', [], $post, 'card-story' );
$card_attributes = '';

foreach ( $attributes as $key => $value ) {
  $card_attributes .= esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
}

// Thumbnail attributes
$thumbnail_args = array(
  'alt' => sprintf( __( '%s Cover', 'fictioneer' ), $story['title'] ),
  'class' => 'no-auto-lightbox'
);

?>

<li
  id="story-card-<?php echo $post->ID; ?>"
  class="post-<?php echo $post->ID; ?> card _large _story <?php echo implode( ' ', $card_classes ); ?>"
  data-story-id="<?php echo $post->ID; ?>"
  data-check-id="<?php echo $post->ID; ?>"
  <?php echo $card_attributes; ?>
>
  <div class="card__body polygon">

    <div class="card__main _grid _large">

      <div class="card__header _large">

        <?php if ( $args['show_type'] ?? false ) : ?>
          <div class="card__label"><?php _ex( 'Story', 'Story card label.', 'fictioneer' ); ?></div>
        <?php endif; ?>

        <h3 class="card__title"><a href="<?php echo $story_link; ?>" class="truncate _1-1"><?php
          if ( ! empty( $post->post_password ) ) {
            echo '<i class="fa-solid fa-lock protected-icon"></i> ';
          }

          echo $story['title'];
        ?></a></h3>

        <?php echo fictioneer_get_card_controls( $post->ID ); ?>

      </div>

      <?php
        // Action hook
        do_action( 'fictioneer_large_card_body_story', $post, $story, $args );

        // Thumbnail
        if ( has_post_thumbnail() && get_theme_mod( 'card_image_style', 'default' ) !== 'none' ) {
          printf(
            '<a href="%1$s" title="%2$s" class="card__image cell-img" %3$s>%4$s</a>',
            get_the_post_thumbnail_url( null, 'full' ),
            sprintf( __( '%s Thumbnail', 'fictioneer' ), $story['title'] ),
            fictioneer_get_lightbox_attribute(),
            get_the_post_thumbnail( null, 'cover', $thumbnail_args )
          );
        }

        // Content
        printf(
          '<div class="card__content cell-desc"><div class="truncate %1$s">%2$s<span>%3$s</span></div></div>',
          $chapter_count > 2 ? '_3-4' : '_4-4',
          $hide_author ? '' : sprintf(
            '<span class="card__by-author cq-show-below-640">%s</span> ',
            sprintf(
              _x( 'by %s —', 'Large card: by {Author} —.', 'fictioneer' ),
              fictioneer_get_author_node()
            )
          ),
          $excerpt
        );
      ?>

      <?php if ( $chapter_count > 0 && ! get_option( 'fictioneer_hide_large_card_chapter_list' ) ) : ?>
        <ol class="card__link-list cell-list">
          <?php
            // Prepare
            $chapter_query_args = array(
              'post_type' => 'fcn_chapter',
              'post_status' => 'publish',
              'post__in' => $chapter_ids ?: [0], // Must not be empty!
              'orderby' => 'post__in',
              'posts_per_page' => -1,
              'no_found_rows' => true, // Improve performance
              'update_post_term_cache' => false // Improve performance
            );

            $chapters = new WP_Query( $chapter_query_args );
          ?>
          <?php foreach ( $chapters->posts as $chapter ) : ?>
            <?php
              // Chapter title
              $list_title = get_post_meta( $chapter->ID, 'fictioneer_chapter_list_title', true );
              $list_title = trim( wp_strip_all_tags( $list_title ) );

              if ( empty( $list_title ) ) {
                $chapter_title = fictioneer_get_safe_title( $chapter->ID, 'card-story-chapter-list' );
              } else {
                $chapter_title = $list_title;
              }

              // Extra classes
              $list_item_classes = [];

              if ( ! empty( $chapter->post_password ) ) {
                $list_item_classes[] = '_password';
              }
            ?>
            <li class="card__link-list-item <?php echo implode( ' ', $list_item_classes ); ?>">
              <div class="card__left text-overflow-ellipsis">
                <i class="fa-solid fa-caret-right"></i>
                <a href="<?php the_permalink( $chapter->ID ); ?>" class="card__link-list-link"><?php
                  echo $chapter_title;
                ?></a>
              </div>
              <div class="card__right">
                <?php
                  printf(
                    '%1$s<span class="cq-hide-below-460"> %2$s</span><span class="separator-dot">&#8196;&bull;&#8196;</span>%3$s',
                    fictioneer_shorten_number( fictioneer_get_word_count( $chapter->ID ) ),
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

      <div class="card__footer cell-footer">

        <div class="card__footer-box _left text-overflow-ellipsis"><?php
          // Build footer items
          $footer_items = [];

          if ( $story['status'] !== 'Oneshot' || $story['chapter_count'] > 1 ) {
            $footer_items['chapters'] = '<i class="card-footer-icon fa-solid fa-list" title="' .
              esc_attr__( 'Chapters', 'fictioneer' ) . '"></i> ' . $story['chapter_count'];
          }

          $footer_items['words'] = '<i class="card-footer-icon fa-solid fa-font" title="' .
            esc_attr__( 'Total Words', 'fictioneer' ) . '"></i> ' . $story['word_count_short'];

          if ( ( $args['orderby'] ?? 0 ) === 'date' ) {
            $footer_items['publish_date'] = '<i class="card-footer-icon fa-solid fa-clock" title="' .
              esc_attr__( 'Published', 'fictioneer' ) .'"></i> ' . get_the_date( FICTIONEER_CARD_STORY_FOOTER_DATE );
          } else {
            $footer_items['modified_date'] = '<i class="card-footer-icon fa-regular fa-clock" title="' .
              esc_attr__( 'Last Updated', 'fictioneer' ) .'"></i> ' . get_the_modified_date( FICTIONEER_CARD_STORY_FOOTER_DATE );
          }

          if ( ! $hide_author ) {
            $footer_items['author'] = '<i class="card-footer-icon fa-solid fa-circle-user cq-hide-below-640"></i> ' .
              fictioneer_get_author_node( get_the_author_meta( 'ID' ), 'cq-hide-below-640' );
          }

          $footer_items['comments'] = '<i class="card-footer-icon fa-solid fa-message cq-hide-below-460" title="' .
            esc_attr__( 'Comments', 'fictioneer' ) . '"></i> <span class="cq-hide-below-460" title="' .
            esc_attr__( 'Comments', 'fictioneer' ) . '">' . $story['comment_count'] . '</span>';

          $footer_items['status'] = '<i class="card-footer-icon ' . $story['icon'] . '"></i> ' . fcntr( $story['status'] );

          // Filter footer items
          $footer_items = apply_filters( 'fictioneer_filter_story_card_footer', $footer_items, $post, $story, $args );

          // Implode and render footer items
          echo implode( ' ', $footer_items );
        ?></div>

        <div class="card__footer-box _right rating-letter-label _large tooltipped" data-tooltip="<?php echo fcntr( $story['rating'], true ); ?>">
          <span class="cq-hide-below-460"><?php echo fcntr( $story['rating'] ); ?></span>
          <span class="cq-show-below-460"><?php echo fcntr( $story['rating_letter'] ); ?></span>
        </div>

      </div>

    </div>

  </div>
</li>
