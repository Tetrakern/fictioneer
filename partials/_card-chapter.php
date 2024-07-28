<?php
/**
 * Partial: Chapter Card
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0.0
 *
 * @internal $args['show_type']    Whether to show the post type label. Unsafe.
 * @internal $args['cache']        Whether to account for active caching. Unsafe.
 * @internal $args['hide_author']  Whether to hide the author. Unsafe.
 * @internal $args['order']        Current order. Default 'desc'. Unsafe.
 * @internal $args['orderby']      Current orderby. Default 'modified'. Unsafe.
 */


// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$post_id = $post->ID;
$title = fictioneer_get_safe_title( $post_id, 'card-chapter' );
$story_id = get_post_meta( $post_id, 'fictioneer_chapter_story', true );
$story_post = get_post( $story_id );
$story_unpublished = get_post_status( $story_id ) !== 'publish';
$story_data = $story_id ? fictioneer_get_story_data( $story_id, false ) : null; // Does not refresh comment count!
$chapter_rating = get_post_meta( $post_id, 'fictioneer_chapter_rating', true );
$story_thumbnail_url_full = $story_id ? get_the_post_thumbnail_url( $story_id, 'full' ) : null;
$text_icon = get_post_meta( $post_id, 'fictioneer_chapter_text_icon', true );
$excerpt = fictioneer_get_forced_excerpt( $post, 512, true );
$card_classes = [];

// Taxonomies
$tags = false;
$fandoms = false;
$characters = false;
$genres = false;

if (
  get_option( 'fictioneer_show_tags_on_chapter_cards' ) &&
  ! get_option( 'fictioneer_hide_taxonomies_on_chapter_cards' )
) {
  $tags = get_the_tags();
}

if ( ! get_option( 'fictioneer_hide_taxonomies_on_chapter_cards' ) ) {
  $fandoms = get_the_terms( $post_id, 'fcn_fandom' );
  $characters = get_the_terms( $post_id, 'fcn_character' );
  $genres = get_the_terms( $post_id, 'fcn_genre' );
}

// Flags
$hide_author = $args['hide_author'] ?? false && ! get_option( 'fictioneer_show_authors' );
$show_taxonomies = ! get_option( 'fictioneer_hide_taxonomies_on_chapter_cards' ) && ( $tags || $fandoms || $characters || $genres );

// Extra classes
if ( $story_unpublished ) {
  $card_classes[] = '_story-unpublished';
}

if ( get_theme_mod( 'card_style', 'default' ) !== 'default' ) {
  $card_classes[] = '_' . get_theme_mod( 'card_style' );
}

if ( get_theme_mod( 'card_image_style', 'default' ) !== 'default' ) {
  $card_classes[] = '_' . get_theme_mod( 'card_image_style' );
}

// Card attributes
$attributes = apply_filters( 'fictioneer_filter_card_attributes', [], $post, 'card-chapter' );
$card_attributes = '';

foreach ( $attributes as $key => $value ) {
  $card_attributes .= esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
}

// Thumbnail attributes
$thumbnail_args = array(
  'alt' => sprintf( __( '%s Cover', 'fictioneer' ), $title ),
  'class' => 'no-auto-lightbox'
);

?>

<li
  id="chapter-card-<?php echo $post_id; ?>"
  class="post-<?php echo $post_id; ?> card _large _chapter <?php echo implode( ' ', $card_classes ); ?>"
  data-story-id="<?php echo $story_id; ?>"
  data-check-id="<?php echo $post_id; ?>"
  data-unavailable="<?php esc_attr_e( 'Unavailable', 'fictioneer' ); ?>"
  <?php echo $card_attributes; ?>
>
  <div class="card__body polygon">

    <div class="card__main _grid _large">

      <div class="card__header _large">

        <?php if ( $args['show_type'] ?? false ) : ?>
          <div class="card__label"><?php _ex( 'Chapter', 'Chapter card label.', 'fictioneer' ); ?></div>
        <?php endif; ?>

        <h3 class="card__title">
          <a href="<?php the_permalink(); ?>" class="truncate _1-1"><?php
            $list_title = wp_strip_all_tags( get_post_meta( $post_id, 'fictioneer_chapter_list_title', true ) );
            $list_title = trim( $list_title );

            if ( ! empty( $post->post_password ) ) {
              echo '<i class="fa-solid fa-lock protected-icon"></i> ';
            }

            // Make sure there are no whitespaces in-between!
            if ( $list_title ) {
              echo "<span class='cq-show-below-460'>{$list_title}</span>";
              echo "<span class='cq-hide-below-460'>{$title}</span>";
            } else {
              echo $title;
            }
          ?></a>
        </h3>

        <?php
          if ( ! empty( $story_id ) && ! empty( $story_data ) ) {
            echo fictioneer_get_card_controls( $story_id, $post_id );
          }
        ?>

      </div>

      <?php
        // Action hook
        do_action( 'fictioneer_large_card_body_chapter', $post, $story_data, $args );

        // Thumbnail
        if ( get_theme_mod( 'card_image_style', 'default' ) !== 'none' ) {
          if ( has_post_thumbnail() ) {

            printf(
              '<a href="%1$s" title="%2$s" class="card__image _chapter _chapter-image cell-img" %3$s>%4$s</a>',
              get_the_post_thumbnail_url( null, 'full' ),
              sprintf( __( '%s Thumbnail', 'fictioneer' ), $title ),
              fictioneer_get_lightbox_attribute(),
              get_the_post_thumbnail( null, 'cover', $thumbnail_args )
            );

          } elseif ( ! empty( $story_thumbnail_url_full ) ) {

            printf(
              '<a href="%1$s" title="%2$s" class="card__image _chapter _story-image cell-img" %3$s>%4$s</a>',
              $story_thumbnail_url_full,
              sprintf( __( '%s Thumbnail', 'fictioneer' ), $title ),
              fictioneer_get_lightbox_attribute(),
              get_the_post_thumbnail( $story_id, 'cover', $thumbnail_args )
            );

          } elseif ( ! empty( $text_icon ) ) {

            printf(
              '<a href="%1$s" title="%2$s" class="card__text-icon cell-img"><span class="text-icon">%3$s</span></a>',
              get_permalink(),
              esc_attr( $title ),
              get_post_meta( $post_id, 'fictioneer_chapter_text_icon', true )
            );

          }
        }

        // Content
        printf(
          '<div class="card__content cell-desc"><div class="truncate _cq-4-5">%1$s<span>%2$s</span></div></div>',
          $hide_author ? '' : sprintf(
            '<span class="card__by-author cq-show-below-640">%s</span> ',
            sprintf(
              _x( 'by %s —', 'Large card: by {Author} —.', 'fictioneer' ),
              fictioneer_get_author_node()
            )
          ),
          empty( $excerpt ) ? __( 'No description provided yet.', 'fictioneer' ) : $excerpt
        );
      ?>

      <?php if ( ! empty( $story_id ) && ! empty( $story_data ) ) : ?>
        <ol class="card__link-list cell-list">
          <?php
            // Extra classes
            $list_item_classes = [];

            if ( ! empty( $story_post->post_password ) ) {
              $list_item_classes[] = '_password';
            }
          ?>
          <li class="card__link-list-item <?php echo implode( ' ', $list_item_classes ); ?>">
            <div class="card__left text-overflow-ellipsis">
              <i class="fa-solid fa-caret-right"></i>
              <a href="<?php the_permalink( $story_id ); ?>" class="card__link-list-link"><?php
                echo $story_data['title'];
              ?></a>
            </div>
            <div class="card__right">
              <?php
                printf(
                  '<span class="words">%1$s<span class="cq-hide-below-460"> %2$s</span></span><span class="separator-dot">&#8196;&bull;&#8196;</span><span class="status">%3$s</span>',
                  $story_data['word_count_short'],
                  __( 'Words', 'fictioneer' ),
                  $story_data['status']
                );
              ?>
            </div>
          </li>
        </ol>
      <?php endif; ?>

      <?php if ( $show_taxonomies ) : ?>
        <div class="card__tag-list cell-tax">
          <?php
            $taxonomies = array_merge(
              $fandoms ? fictioneer_generate_card_tags( $fandoms, '_fandom' ) : [],
              $genres ? fictioneer_generate_card_tags( $genres, '_genre' ) : [],
              $tags ? fictioneer_generate_card_tags( $tags ) : [],
              $characters ? fictioneer_generate_card_tags( $characters, '_character' ) : []
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
          $words = fictioneer_get_word_count( $post_id );

          if ( $words ) {
            $footer_items['words'] = '<span class="card__footer-words"><i class="card-footer-icon fa-solid fa-font" title="' . esc_attr__( 'Words', 'fictioneer' ) . '"></i> ' . fictioneer_shorten_number( $words ) . '</span>';
          }

          if ( ( $args['orderby'] ?? 0 ) === 'date' ) {
            $footer_items['publish_date'] = '<span class="card__footer-publish-date"><i class="card-footer-icon fa-solid fa-clock" title="' . esc_attr__( 'Published', 'fictioneer' ) .'"></i> ' . get_the_date( FICTIONEER_CARD_CHAPTER_FOOTER_DATE ) . '</span>';
          } else {
            $footer_items['modified_date'] = '<span class="card__footer-modified-date"><i class="card-footer-icon fa-regular fa-clock" title="' . esc_attr__( 'Last Updated', 'fictioneer' ) .'"></i> ' . get_the_modified_date( FICTIONEER_CARD_CHAPTER_FOOTER_DATE ) . '</span>';
          }

          if ( get_option( 'fictioneer_show_authors' ) && ! $hide_author ) {
            $footer_items['author'] = '<span class="card__footer-author"><i class="card-footer-icon fa-solid fa-circle-user cq-hide-below-640"></i> ' . fictioneer_get_author_node( get_the_author_meta( 'ID' ), 'cq-hide-below-640' ) . '</span>';
          }

          $footer_items['comments'] = '<span class="card__footer-comments"><i class="card-footer-icon fa-solid fa-message" title="' . esc_attr__( 'Comments', 'fictioneer' ) . '"></i> ' . get_comments_number( $post ) . '</span>';

          // Filter footer items
          $footer_items = apply_filters( 'fictioneer_filter_chapter_card_footer', $footer_items, $post, $story_data, $args );

          // Implode and render footer items
          echo implode( ' ', $footer_items );
        ?></div>

        <?php if ( ! empty( $chapter_rating ) ) : ?>
          <div class="card__footer-box _right rating-letter-label _large tooltipped" data-tooltip="<?php echo fcntr( $chapter_rating, true ); ?>">
            <span class="cq-hide-below-460"><?php echo fcntr( $chapter_rating ); ?></span>
            <span class="cq-show-below-460"><?php echo fcntr( $chapter_rating[0] ); ?></span>
          </div>
        <?php endif; ?>

      </div>

    </div>

  </div>
</li>
