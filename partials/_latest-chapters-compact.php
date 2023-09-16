<?php
/**
 * Partial: Latest Chapters Compact
 *
 * Renders the (buffered) HTML for the [fictioneer_latest_chapters type="compact"] shortcode.
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
 * @internal $args['author_ids']        Array of author IDs. Default empty.
 * @internal $args['excluded_authors']  Array of author IDs to exclude. Default empty.
 * @internal $args['excluded_cats']     Array of category IDs to exclude. Default empty.
 * @internal $args['excluded_tags']     Array of tag IDs to exclude. Default empty.
 * @internal $args['ignore_protected']  Whether to ignore protected posts. Default false.
 * @internal $args['taxonomies']        Array of taxonomy arrays. Default empty.
 * @internal $args['relation']          Relationship between taxonomies.
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
  'fictioneer_query_name' => 'latest_chapters_compact',
  'post_type' => 'fcn_chapter',
  'post_status' => 'publish',
  'post__in' => $args['post_ids'], // May be empty!
  'order' => $args['order'],
  'orderby' => $args['orderby'],
  'posts_per_page' => $args['count'] + 8, // Little buffer in case of unpublished parent story
  'meta_key' => 'fictioneer_chapter_hidden',
  'meta_value' => 0,
  'no_found_rows' => true,
  'update_post_term_cache' => false
);

// Author?
if ( ! empty( $args['author'] ) ) {
  $query_args['author_name'] = $args['author'];
}

// Author IDs?
if ( ! empty( $args['author_ids'] ) ) {
  $query_args['author__in'] = $args['author_ids'];
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

// Excluded authors?
if ( ! empty( $args['excluded_authors'] ) ) {
  $query_args['author__not_in'] = $args['excluded_authors'];
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

<section class="small-card-block latest-chapters _compact <?php echo $args['classes']; ?>">
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
          if ( ++$card_counter > $args['count'] ) break;
        ?>

        <li class="card watch-last-clicked _small _info">
          <div class="card__body polygon">

          <button class="card__info-toggle toggle-last-clicked" aria-label="<?php esc_attr_e( 'Open info box', 'fictioneer' ); ?>"><i class="fa-solid fa-chevron-down"></i></button>

            <div class="card__main _grid _small">

              <?php if ( $thumbnail_full ) : ?>
                <a href="<?php echo $thumbnail_full; ?>" title="<?php echo esc_attr( sprintf( __( '%s Thumbnail', 'fictioneer' ), $title ) ); ?>" class="card__image cell-img" <?php echo fictioneer_get_lightbox_attribute(); ?>><?php echo $thumbnail_snippet ?></a>
              <?php elseif ( ! empty( $text_icon ) ) : ?>
                <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( $title ); ?>" class="card__text-icon _small cell-img"><span class="text-icon"><?php echo $text_icon; ?></span></a>
              <?php endif; ?>

              <h3 class="card__title _small cell-title"><a href="<?php the_permalink(); ?>" class="truncate _1-1"><?php
                $list_title = fictioneer_get_field( 'fictioneer_chapter_list_title' );
                echo $list_title ? wp_strip_all_tags( $list_title ) : $title;
              ?></a></h3>

              <div class="card__content _small cell-desc">
                <div class="text-overflow-ellipsis _bottom-spacer-xs">
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
                  ?>
                </div>
                <div class="text-overflow-ellipsis">
                  <?php
                    printf(
                      _x( '%1$s Words on %2$s', 'Small card: {n} Words on {Date}.', 'fictioneer' ),
                      fictioneer_shorten_number( get_post_meta( $post->ID, '_word_count', true ) ),
                      get_the_time( FICTIONEER_LATEST_CHAPTERS_FOOTER_DATE )
                    );
                  ?>
                </div>
              </div>

            </div>

            <div class="card__overlay-infobox _excerpt escape-last-click">
              <div class="truncate <?php echo $args['source'] ? '_3-3' : '_2-2'; ?>"><?php echo fictioneer_get_forced_excerpt( $post ); ?></div>
            </div>

          </div>
        </li>

      <?php endwhile; ?>
    </ul>

  <?php else: ?>

    <div class="no-results"><?php _e( 'Nothing to show.', 'fictioneer' ); ?></div>

  <?php endif; wp_reset_postdata(); ?>
</section>
