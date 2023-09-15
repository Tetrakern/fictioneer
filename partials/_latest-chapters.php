<?php
/**
 * Partial: Latest Chapters
 *
 * Renders the (buffered) HTML for the [fictioneer_latest_chapters] shortcode.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0
 *
 * @internal $args['count']             Number of posts provided by the shortcode.
 * @internal $args['author']            Author provided by the shortcode.
 * @internal $args['order']             Order of posts. Default 'DESC'.
 * @internal $args['orderby']           Sorting of posts. Default 'date'.
 * @internal $args['spoiler']           Whether to obscure or show chapter excerpt.
 * @internal $args['source']            Whether to show author and story.
 * @internal $args['post_ids']          Array of post IDs. Default empty.
 * @internal $args['excluded_cats']     Array of category IDs to exclude. Default empty.
 * @internal $args['excluded_tags']     Array of tag IDs to exclude. Default empty.
 * @internal $args['ignore_protected']  Whether to ignore protected posts. Default false.
 * @internal $args['taxonomies']        Array of taxonomy arrays. Default empty.
 * @internal $args['relation']          Relationship between taxonomies.
 * @internal $args['simple']            Whether to show the simple variant.
 * @internal $args['classes']           String of additional CSS classes. Default empty.
 */
?>

<?php

// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$card_counter = 0;

// Prepare query
$query_args = array(
  'fictioneer_query_name' => 'latest_chapters',
  'post_type' => 'fcn_chapter',
  'post_status' => 'publish',
  'post__in' => $args['post_ids'], // May be empty!
  'orderby' => $args['orderby'] ?? 'date',
  'order' => $args['order'] ?? 'DESC',
  'posts_per_page' => $args['count'] + 8, // Little buffer in case of unpublished parent story
  'meta_key' => 'fictioneer_chapter_hidden',
  'meta_value' => 0,
  'no_found_rows' => true,
  'update_post_term_cache' => false
);

// Parameter for author?
if ( isset( $args['author'] ) && $args['author'] ) {
  $query_args['author_name'] = $args['author'];
}

// Taxonomies?
if ( ! empty( $args['taxonomies'] ) ) {
  $query_args['tax_query'] = fictioneer_get_shortcode_tax_query( $args );
}

// Excluded tags?
if ( ! empty( $args['excluded_tags'] ) ) {
  $query_args['tag__not_in'] = $args['excluded_tags'];
}

// Excluded categories?
if ( ! empty( $args['excluded_cats'] ) ) {
  $query_args['category__not_in'] = $args['excluded_cats'];
}

// Ignore protected?
if ( $args['ignore_protected'] ) {
  add_filter( 'posts_where', 'fictioneer_exclude_protected_posts' );
}

// Apply filters
$query_args = apply_filters( 'fictioneer_filter_shortcode_latest_chapters_query_args', $query_args, $args );

// Query chapters
$entries = fictioneer_shortcode_query( $query_args );

// Remove temporary filters
remove_filter( 'posts_where', 'fictioneer_exclude_protected_posts' );

?>

<section class="small-card-block latest-chapters <?php echo $args['classes']; ?>">
  <?php if ( $entries->have_posts() ) : ?>

    <ul class="two-columns _collapse-on-mobile">
      <?php while ( $entries->have_posts() ) : $entries->the_post(); ?>

        <?php
          // Setup
          $story_id = fictioneer_get_field( 'fictioneer_chapter_story' );

          if ( get_post_status( $story_id ) !== 'publish' ) {
            continue;
          }

          $title = fictioneer_get_safe_title( get_the_ID() );
          $chapter_rating = fictioneer_get_field( 'fictioneer_chapter_rating' );
          $story = $story_id ? fictioneer_get_story_data( $story_id, false ) : false; // Does not refresh comment count!
          $text_icon = fictioneer_get_field( 'fictioneer_chapter_text_icon' );

          // Chapter images
          $thumbnail_full = get_the_post_thumbnail_url( $post, 'full' );
          $thumbnail_snippet = get_the_post_thumbnail( $post, 'snippet', ['class' => 'no-auto-lightbox'] );

          // Story images
          if ( ! $thumbnail_full && $story ) {
            $thumbnail_full = get_the_post_thumbnail_url( $story_id, 'full' );
            $thumbnail_snippet = get_the_post_thumbnail( $story_id, 'snippet', ['class' => 'no-auto-lightbox']);
          }

          // Count actually rendered cards to account for buffer
          if ( ++$card_counter > $args['count'] ) {
            break;
          }
        ?>

        <li class="card _small">
          <div class="card__body polygon">

            <div class="card__main _grid _small">

              <?php if ( $thumbnail_full ) : ?>
                <a href="<?php echo $thumbnail_full; ?>" title="<?php echo esc_attr( sprintf( __( '%s Thumbnail', 'fictioneer' ), $title ) ); ?>" class="card__image cell-img" <?php echo fictioneer_get_lightbox_attribute(); ?>><?php echo $thumbnail_snippet ?></a>
              <?php elseif ( ! empty( $text_icon ) ) : ?>
                <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( $title ) ?>" class="card__text-icon _small cell-img"><span class="text-icon"><?php echo $text_icon; ?></span></a>
              <?php endif; ?>

              <h3 class="card__title _small cell-title"><a href="<?php the_permalink(); ?>" class="truncate _1-1"><?php
                $list_title = fictioneer_get_field( 'fictioneer_chapter_list_title' );
                echo $list_title ? wp_strip_all_tags( $list_title ) : $title;
              ?></a></h3>

              <div class="card__content _small cell-desc">
                <div class="truncate _3-3 <?php if ( ! $args['spoiler'] ) echo '_obfuscated'; ?>" data-obfuscation-target>
                  <?php if ( get_option( 'fictioneer_show_authors' ) && $args['source'] ) : ?>
                    <span class="card__by-author"><?php
                      printf( _x( 'by %s', 'Small card: by {Author}.', 'fictioneer' ), fictioneer_get_author_node() );
                    ?></span>
                  <?php endif; ?>
                  <?php
                    if ( $story && $args['source'] ) {
                      printf(
                        _x( 'in <a href="%1$s" class="bold-link">%2$s</a>', 'Small card: in {Link to Story}.', 'fictioneer' ),
                        get_permalink( $story_id ),
                        mb_strimwidth( fictioneer_get_safe_title( $story_id ), 0, 24, '…' )
                      );
                    }

                    $excerpt = fictioneer_get_forced_excerpt( $post );
                    $spoiler_note = str_repeat(
                      _x( '&#183; ', 'Spoiler obfuscation character.', 'fictioneer' ), intval( strlen( $excerpt ) )
                    );
                    $spoiler_note = apply_filters( 'fictioneer_filter_obfuscation_string', $spoiler_note, $post );
                  ?>
                  <?php if ( ! $args['spoiler'] ) : ?>
                    <span data-click="toggle-obfuscation" tabindex="0">
                      <span class="obfuscated">&nbsp;<?php echo $spoiler_note; ?></span>
                      <span class="clean">— <?php echo $excerpt; ?></span>
                    </span>
                  <?php else : ?>
                    <span><span class="clean">— <?php echo $excerpt; ?></span></span>
                  <?php endif; ?>
                </div>
              </div>

            </div>

            <?php if ( ! $args['simple'] ) : ?>
              <div class="card__footer _small">

                <div class="card__footer-box _left text-overflow-ellipsis"><?php

                  // Build footer items
                  $footer_items = [];

                  $footer_items['words'] = '<i class="card-footer-icon fa-solid fa-font" title="' .
                    esc_attr__( 'Words', 'fictioneer' ) . '"></i> ' .
                    fictioneer_shorten_number( get_post_meta( $post->ID, '_word_count', true ) );

                  if ( $args['orderby'] == 'modified' ) {
                    $footer_items['modified_date'] = '<i class="card-footer-icon fa-regular fa-clock" title="' .
                      esc_attr__( 'Last Updated', 'fictioneer' ) . '"></i> ' .
                      get_the_modified_date( FICTIONEER_LATEST_CHAPTERS_FOOTER_DATE, $post );
                  } else {
                    $footer_items['publish_date'] = '<i class="card-footer-icon fa-solid fa-clock" title="' .
                      esc_attr__( 'Published', 'fictioneer' ) . '"></i> ' .
                      get_the_date( FICTIONEER_LATEST_CHAPTERS_FOOTER_DATE, $post );
                  }

                  $footer_items['comments'] = '<i class="card-footer-icon fa-solid fa-message" title="' .
                    esc_attr__( 'Comments', 'fictioneer' ) . '"></i> ' . get_comments_number();

                  if ( $story ) {
                    $footer_items['status'] = '<i class="card-footer-icon ' . $story['icon'] . '"></i> ' .
                      fcntr( $story['status'] );
                  }

                  // Filer footer items
                  $footer_items = apply_filters(
                    'fictioneer_filter_shortcode_latest_chapters_card_footer',
                    $footer_items,
                    $post,
                    $args,
                    $story
                  );

                  // Implode and render footer items
                  echo implode( ' ', $footer_items );

                ?></div>

                <?php if ( ! empty( $chapter_rating ) ) : ?>
                  <div class="card__footer-box _right rating-letter-label tooltipped" data-tooltip="<?php echo fcntr( $chapter_rating, true ) ?>">
                    <?php echo fcntr( $chapter_rating[0] ); ?>
                  </div>
                <?php endif; ?>

              </div>
            <?php endif; ?>

          </div>
        </li>

      <?php endwhile; ?>
    </ul>

  <?php else: ?>

    <div class="no-results"><?php _e( 'Nothing to show.', 'fictioneer' ); ?></div>

  <?php endif; wp_reset_postdata(); ?>
</section>
