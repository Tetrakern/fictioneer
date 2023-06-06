<?php
/**
 * Partial: Post Collection
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
$tags = get_option( 'fictioneer_show_tags_on_story_cards' ) ? get_the_tags() : false;
$fandoms = get_the_terms( $post->ID, 'fcn_fandom' );
$characters = get_the_terms( $post->ID, 'fcn_character' );
$genres = get_the_terms( $post->ID, 'fcn_genre' );

// Flags
$show_type = isset( $args['show_type'] ) && $args['show_type'];
$show_taxonomies = ! get_option( 'fictioneer_hide_taxonomies_on_story_cards' ) && ( $fandoms || $characters || $genres || $tags );

// Clean items of non-published entries
foreach ( $items as $key => $post_id ) {
  if ( get_post_status( $post_id ) != 'publish' ) unset( $items[$key] );
}

// Statistics
if ( ! empty( $items ) ) {
  foreach ( $items as $post_id ) {
    // Count by type
    switch ( get_post_type( $post_id ) ) {
      case 'fcn_chapter':
        if ( ! in_array( $post_id, $processed_ids ) ) {
          $chapter_count += 1;
          $word_count += get_post_meta( $post_id, '_word_count', true );
          $processed_ids[] = $post_id;
        }
        break;
      case 'fcn_story':
        $story_count += 1;
        $chapters = fictioneer_get_field( 'fictioneer_story_chapters', $post_id );

        // Try to rescue an empty description by using one from a story...
        if ( empty( $description ) ) {
          $description = fictioneer_first_paragraph_as_excerpt(
            fictioneer_get_content_field( 'fictioneer_story_short_description', $post_id )
          );
        }

        if ( $chapters ) {
          foreach ( $chapters as $chapter_id ) {
            if ( fictioneer_get_field( 'fictioneer_chapter_no_chapter', $chapter_id ) ) continue;
            if ( fictioneer_get_field( 'fictioneer_chapter_hidden', $chapter_id ) ) continue;

            if ( get_post_status( $chapter_id ) === 'publish' ) {
              if ( ! in_array( $chapter_id, $processed_ids ) ) {
                $chapter_count += 1;
                $word_count += get_post_meta( $chapter_id, '_word_count', true );
                $processed_ids[] = $chapter_id;
              }
            }
          }
        }
        break;
    }
  }

  // Prepare features items
  $items = array_slice( $items, -3, null, true );
}

// Required since it's possible to add chapters to collection outside of
// their stories, which would cause the comments to be counted twice.
$comment_args = array( 'post_type' => array( 'fcn_chapter' ), 'post__in' => $processed_ids, 'count' => true );
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
                  echo get_post_time( get_option( 'fictioneer_subitem_date_format', "M j, 'y" ) ?: "M j, 'y", false, $post_id );
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

        <i class="fa-regular fa-clock" title="<?php esc_attr_e( 'Last Updated', 'fictioneer' ) ?>"></i>
        <?php the_modified_date( get_option( 'fictioneer_subitem_date_format', "M j, 'y" ) ?: "M j, 'y" ); ?>

        <i class="fa-solid fa-message" title="<?php esc_attr_e( 'Comments', 'fictioneer' ) ?>"></i>
        <?php echo $comment_count; ?>

      </div>
    </div>

  </div>
</li>
