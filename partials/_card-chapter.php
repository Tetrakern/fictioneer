<?php
/**
 * Partial: Chapter Card
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0
 *
 * @internal $args['show_type']   Whether to show the post type label.
 * @internal $args['cache']       Whether to account for active caching.
 * @internal $args['hide_author'] Whether to hide the author.
 */
?>

<?php

// Setup
$title = fictioneer_get_safe_title( get_the_ID() );
$story_id = fictioneer_get_field( 'fictioneer_chapter_story' );
$story_data = $story_id ? fictioneer_get_story_data( $story_id ) : null;
$chapter_rating = fictioneer_get_field( 'fictioneer_chapter_rating' );
$story_thumbnail_url_full = $story_id ? get_the_post_thumbnail_url( $story_id, 'full' ) : null;
$text_icon = fictioneer_get_field( 'fictioneer_chapter_text_icon' );

// Taxonomies
$tags = get_option( 'fictioneer_show_tags_on_chapter_cards' ) ? get_the_tags() : false;
$fandoms = get_the_terms( $post->ID, 'fcn_fandom' );
$characters = get_the_terms( $post->ID, 'fcn_character' );
$genres = get_the_terms( $post->ID, 'fcn_genre' );

// Flags
$hide_author = isset( $args['hide_author'] ) && $args['hide_author'];
$show_taxonomies = ! get_option( 'fictioneer_hide_taxonomies_on_chapter_cards' ) && ( $tags || $fandoms || $characters || $genres );
$show_type = isset( $args['show_type'] ) && $args['show_type'];

?>

<li id="chapter-card-<?php the_ID(); ?>" class="card">
  <div class="card__body polygon">

    <div class="card__header _large">

      <?php if ( $show_type ) : ?>
        <div class="card__label"><?php _ex( 'Chapter', 'Chapter card label.', 'fictioneer' ); ?></div>
      <?php endif; ?>

      <h3 class="card__title">
        <a href="<?php the_permalink(); ?>" class="truncate _1-1"><?php
          // Make sure there are no whitespaces in-between!
          if ( fictioneer_get_field( 'fictioneer_chapter_list_title' ) ) {
            echo '<span class="show-below-480">' . wp_strip_all_tags( fictioneer_get_field( 'fictioneer_chapter_list_title' ) ) . '</span>';
            echo '<span class="hide-below-480">' . $title . '</span>';
          } else {
            echo $title;
          }
        ?></a>
      </h3>

      <?php
        if ( ! empty( $story_id ) && ! empty( $story_data ) ) {
          echo fictioneer_get_card_controls( $story_id, get_the_ID() );
        }
      ?>

    </div>

    <div class="card__main _grid _large">

      <?php
        if ( has_post_thumbnail() ) {

          // Start HTML ---> ?>
          <a
            href="<?php the_post_thumbnail_url( 'full' ); ?>"
            title="<?php echo esc_attr( sprintf( __( '%s Thumbnail', 'fictioneer' ), $title ) ) ?>"
            class="card__image cell-img"
            <?php echo fictioneer_get_lightbox_attribute(); ?>
          ><?php the_post_thumbnail( 'cover' ); ?></a>
          <?php // <--- End HTML

        } elseif ( ! empty( $story_thumbnail_url_full ) ) {

          // Start HTML ---> ?>
          <a
            href="<?php echo $story_thumbnail_url_full; ?>"
            title="<?php echo esc_attr( sprintf( __( '%s Thumbnail', 'fictioneer' ), $title ) ) ?>"
            class="card__image cell-img"
            <?php echo fictioneer_get_lightbox_attribute(); ?>
          ><?php echo get_the_post_thumbnail( $story_id, 'cover' ); ?></a>
          <?php // <--- End HTML

        } elseif ( ! empty( $text_icon ) ) {

          // Start HTML ---> ?>
          <a
            href="<?php the_permalink(); ?>"
            title="<?php echo esc_attr( $title ) ?>"
            class="card__text-icon cell-img"
          ><span class="text-icon"><?php echo fictioneer_get_field( 'fictioneer_chapter_text_icon' ) ?></span></a>
          <?php // <--- End HTML

        }
      ?>

      <div class="card__content cell-desc truncate _4-4">
        <?php if ( get_option( 'fictioneer_show_authors' ) && ! $hide_author ) : ?>
          <span class="card__by-author show-below-desktop"><?php
            printf( _x( 'by %s —', 'Small card: by {Author} —.', 'fictioneer' ), fictioneer_get_author_node() );
          ?></span>
        <?php endif; ?>
        <span><?php
          $excerpt = fictioneer_get_forced_excerpt( $post, 512, true );
          echo empty( $excerpt ) ? __( 'No description provided yet.', 'fictioneer' ) : $excerpt;
        ?></span>
      </div>

      <?php if ( ! empty( $story_id ) && ! empty( $story_data ) ) : ?>
        <ul class="card__link-list cell-list">
          <li>
            <div class="card__left text-overflow-ellipsis">
              <i class="fa-solid fa-caret-right"></i>
              <a href="<?php echo get_the_permalink( $story_id ); ?>"><?php echo $story_data['title']; ?></a>
            </div>
            <div class="card__right _flex dot-separator-inverse">
              <span><?php
                printf(
                  __( '<span>%s</span><span class="hide-below-480"> Words</span>', 'fictioneer' ),
                  $story_data['word_count_short']
                );
              ?></span>
              <span><?php echo $story_data['status']; ?></span>
            </div>
          </li>
        </ul>
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

        <i class="fa-solid fa-font" title="<?php esc_attr_e( 'Words', 'fictioneer' ) ?>"></i>
        <?php echo fictioneer_shorten_number( get_post_meta( get_the_ID(), '_word_count', true ) ); ?>

        <i class="fa-regular fa-clock" title="<?php esc_attr_e( 'Last Updated', 'fictioneer' ) ?>"></i>
        <?php the_modified_date( get_option( 'fictioneer_subitem_date_format', "M j, 'y" ) ?: "M j, 'y" ); ?>

        <?php if ( get_option( 'fictioneer_show_authors' ) && ! $hide_author ) : ?>
          <?php fictioneer_icon( 'user', 'hide-below-desktop' ); ?>
          <?php fictioneer_the_author_node( get_the_author_meta( 'ID' ), 'hide-below-desktop' ); ?>
        <?php endif; ?>

        <i class="fa-solid fa-message" title="<?php esc_attr_e( 'Comments', 'fictioneer' ) ?>"></i>
        <?php echo get_comments_number( $post ); ?>

      </div>

      <?php if ( ! empty( $chapter_rating ) ) : ?>
        <div class="card__right rating-letter-label _large tooltipped" data-tooltip="<?php echo fcntr( $chapter_rating, true ); ?>">
          <span class="hide-below-480"><?php echo fcntr( $chapter_rating ); ?></span>
          <span class="show-below-480"><?php echo fcntr( $chapter_rating[0] ); ?></span>
        </div>
      <?php endif; ?>

    </div>

  </div>
</li>
