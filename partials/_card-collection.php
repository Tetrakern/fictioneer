<?php
/**
 * Partial: Post Collection
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0
 *
 * @internal $args['show_type']  Whether to show the post type label. Unsafe.
 * @internal $args['cache']      Whether to account for active caching. Unsafe.
 * @internal $args['order']      Current order. Default 'desc'. Unsafe.
 * @internal $args['orderby']    Current orderby. Default 'modified'. Unsafe.
 */
?>

<?php

// Setup
$list_title = trim( fictioneer_get_field( 'fictioneer_collection_list_title' ) );
$title = empty( $list_title ) ? fictioneer_get_safe_title( get_the_ID() ) : $list_title;
$description = fictioneer_get_content_field( 'fictioneer_collection_description' );
$items = fictioneer_get_field( 'fictioneer_collection_items' );
$items = empty( $items ) ? [] : $items;
$story_count = 0;
$chapter_count = 0;
$word_count = 0;
$processed_ids = [];

// Taxonomies
$tags = false;
$fandoms = false;
$characters = false;
$genres = false;

if (
  get_option( 'fictioneer_show_tags_on_collection_cards' ) &&
  ! get_option( 'fictioneer_hide_taxonomies_on_collection_cards' )
) {
  $tags = get_the_tags();
}

if ( ! get_option( 'fictioneer_hide_taxonomies_on_collection_cards' ) ) {
  $fandoms = get_the_terms( $post->ID, 'fcn_fandom' );
  $characters = get_the_terms( $post->ID, 'fcn_character' );
  $genres = get_the_terms( $post->ID, 'fcn_genre' );
}

// Flags
$show_type = $args['show_type'] ?? false;
$show_taxonomies = ! get_option( 'fictioneer_hide_taxonomies_on_collection_cards' ) && ( $fandoms || $characters || $genres || $tags );

// Query featured posts
if ( ! empty( $items ) ) {
  $items = new WP_Query(
    array(
      'post_type' => 'any',
      'post_status' => 'publish',
      'post__in' => $items,
      'ignore_sticky_posts' => true,
      'orderby' => 'modified',
      'posts_per_page' => -1,
      'update_post_meta_cache' => false, // Improve performance
      'update_post_term_cache' => false, // Improve performance
      'no_found_rows' => true // Improve performance
    )
  );

  $items = $items->posts;
}

// Statistics
if ( ! empty( $items ) ) {
  foreach ( $items as $item ) {
    // Count by type
    switch ( $item->post_type ) {
      case 'fcn_chapter':
        if ( ! in_array( $item->ID, $processed_ids ) ) {
          $chapter_count += 1;
          $word_count += get_post_meta( $item->ID, '_word_count', true );
          $processed_ids[] = $item->ID;
        }
        break;
      case 'fcn_story':
        $story_count += 1;
        $chapter_ids = fictioneer_get_field( 'fictioneer_story_chapters', $item->ID );

        // Try to rescue an empty description by using one from a story...
        if ( empty( $description ) ) {
          $description = fictioneer_first_paragraph_as_excerpt(
            fictioneer_get_content_field( 'fictioneer_story_short_description', $item->ID )
          );
        }

        // Query eligible chapters
        $chapter_query_args = array(
          'post_type' => 'fcn_chapter',
          'post_status' => 'publish',
          'post__in' => $chapter_ids,
          'posts_per_page' => -1,
          'update_post_term_cache' => false, // Improve performance
          'no_found_rows' => true // Improve performance
        );

        $chapters = new WP_Query( $chapter_query_args );
        $chapters = $chapters->posts;

        if ( ! empty( $chapters ) ) {
          foreach ( $chapters as $chapter ) {
            if ( fictioneer_get_field( 'fictioneer_chapter_no_chapter', $chapter->ID ) ) continue;
            if ( fictioneer_get_field( 'fictioneer_chapter_hidden', $chapter->ID ) ) continue;

            if ( ! in_array( $chapter->ID, $processed_ids ) ) {
              $chapter_count += 1;
              $word_count += get_post_meta( $chapter->ID, '_word_count', true );
              $processed_ids[] = $chapter->ID;
            }
          }
        }
        break;
    }
  }

  // Prepare features items
  $items = array_slice( $items, 0, 3 );
}

// Required since it's possible to add chapters to collection outside of
// their stories, which would cause the comments to be counted twice.
$comment_args = array(
  'post_type' => 'fcn_chapter',
  'post__in' => $processed_ids,
  'count' => true,
  'update_comment_meta_cache' => false
);

$comment_count = get_comments( $comment_args );

?>

<li id="collection-card-<?php the_ID(); ?>" class="card">
  <div class="card__body polygon">

    <div class="card__header _large">
      <?php if ( $show_type ) : ?>
        <div class="card__label"><?php _ex( 'Collection', 'Collection card label.', 'fictioneer' ); ?></div>
      <?php endif; ?>
      <h3 class="card__title"><a href="<?php the_permalink(); ?>" class="truncate _1-1"><?php echo $title; ?></a></h3>
    </div>

    <div class="card__main _grid _large">

      <?php if ( has_post_thumbnail() ) : ?>
        <a href="<?php the_post_thumbnail_url( 'full' ); ?>" title="<?php printf( __( '%s Thumbnail', 'fictioneer' ), $title ) ?>" class="card__image cell-img" <?php echo fictioneer_get_lightbox_attribute(); ?>><?php the_post_thumbnail( 'cover' ); ?></a>
      <?php endif; ?>

      <div class="card__content cell-desc truncate <?php echo count( $items ) > 2 ? '_3-4' : '_4-4'; ?>"><span><?php echo $description; ?></span></div>

      <?php if ( ! empty( $items ) ): ?>
        <ol class="card__link-list cell-list">
          <?php foreach ( $items as $post_id ) : ?>
            <li>
              <div class="card__left text-overflow-ellipsis">
                <i class="fa-solid fa-caret-right"></i>
                <a href="<?php the_permalink( $post_id ); ?>"><?php
                  $list_title = fictioneer_get_field( 'fictioneer_chapter_list_title', $post_id );

                  if ( $list_title ) {
                    echo wp_strip_all_tags( $list_title );
                  } else {
                    echo fictioneer_get_safe_title( $post_id );
                  }
                ?></a>
              </div>
              <div class="card__right">
                <?php
                  echo get_post_type_object( get_post_type( $post_id ) )->labels->singular_name;
                  echo '<span class="separator-dot hide-below-480">&#8196;&bull;&#8196;</span><span class="hide-below-480">';
                  echo get_the_modified_date( FICTIONEER_CARD_COLLECTION_LI_DATE, $post_id );
                  echo '</span>';
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

            if ( $fandoms ) {
              foreach ( $fandoms as $fandom ) {
                $output[] = "<a href='" . get_tag_link( $fandom ) . "' class='tag-pill _inline _fandom'>{$fandom->name}</a>";
              }
            }

            if ( $genres ) {
              foreach ( $genres as $genre ) {
                $output[] = "<a href='" . get_tag_link( $genre ) . "' class='tag-pill _inline _genre'>{$genre->name}</a>";
              }
            }

            if ( $tags ) {
              foreach ( $tags as $tag ) {
                $output[] = "<a href='" . get_tag_link( $tag ) . "' class='tag-pill _inline'>{$tag->name}</a>";
              }
            }

            if ( $characters ) {
              foreach ( $characters as $character ) {
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

        <i class="fa-solid fa-book" title="<?php esc_attr_e( 'Stories', 'fictioneer' ) ?>"></i>
        <?php echo $story_count; ?>

        <i class="fa-solid fa-list" title="<?php esc_attr_e( 'Chapters', 'fictioneer' ) ?>"></i>
        <?php echo $chapter_count; ?>

        <i class="fa-solid fa-font"></i>
        <?php echo fictioneer_shorten_number( $word_count ); ?>

        <?php if ( ( $args['orderby'] ?? 0 ) === 'date' ) : ?>
          <i class="fa-solid fa-clock" title="<?php esc_attr_e( 'Published', 'fictioneer' ) ?>"></i>
          <?php echo get_the_date( FICTIONEER_CARD_CHAPTER_FOOTER_DATE ); ?>
        <?php else : ?>
          <i class="fa-regular fa-clock" title="<?php esc_attr_e( 'Last Updated', 'fictioneer' ) ?>"></i>
          <?php the_modified_date( FICTIONEER_CARD_COLLECTION_FOOTER_DATE ); ?>
        <?php endif; ?>

        <i class="fa-solid fa-message" title="<?php esc_attr_e( 'Comments', 'fictioneer' ) ?>"></i>
        <?php echo $comment_count; ?>

      </div>
    </div>

  </div>
</li>
