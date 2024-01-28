<?php
/**
 * Partial: Latest Updates Compact
 *
 * Renders the (buffered) HTML for the [fictioneer_latest_updates type="compact"]
 * shortcode. Looks for the 'fictioneer_chapters_added' meta property and only
 * updates when the items in the chapters list increase. Admittedly, this can be
 * exploited by removing and re-adding chapters.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.3.0
 *
 * @internal $args['type']              Type argument passed from shortcode ('compact').
 * @internal $args['count']             Number of posts provided by the shortcode.
 * @internal $args['author']            Author provided by the shortcode.
 * @internal $args['order']             Order of posts. Default 'DESC'.
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
  'fictioneer_query_name' => 'latest_updates_compact',
  'post_type' => 'fcn_story',
  'post_status' => 'publish',
  'post__in' => $args['post_ids'], // May be empty!
  'order' => $args['order'],
  'orderby' => 'meta_value',
  'meta_key' => 'fictioneer_chapters_added',
  'posts_per_page' => $args['count'] + 4, // Account for non-eligible posts!
  'no_found_rows' => true,
  'update_post_term_cache' => false
);

// Use extended meta query?
if ( get_option( 'fictioneer_disable_extended_story_list_meta_queries' ) ) {
  $query_args['meta_query'] = array(
    array(
      'key' => 'fictioneer_story_hidden',
      'value' => '0'
    )
  );
} else {
  $query_args['meta_query'] = array(
    'relation' => 'OR',
    array(
      'key' => 'fictioneer_story_hidden',
      'value' => '0'
    ),
    array(
      'key' => 'fictioneer_story_hidden',
      'compare' => 'NOT EXISTS'
    )
  );
}

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
$query_args = apply_filters( 'fictioneer_filter_shortcode_latest_updates_query_args', $query_args, $args );

// Query stories
$entries = fictioneer_shortcode_query( $query_args );

// Remove temporary filters
remove_filter( 'posts_where', 'fictioneer_exclude_protected_posts' );

?>

<section class="small-card-block latest-updates <?php echo $args['classes']; ?>">
  <?php if ( $entries->have_posts() ) : ?>

    <ul class="grid-columns _collapse-on-mobile">
      <?php while ( $entries->have_posts() ) : $entries->the_post(); ?>

        <?php
          // Setup
          $story = fictioneer_get_story_data( $post->ID, false ); // Does not refresh comment count!
          $chapter_list = [];
          $chapter_excerpt; // Set inside inner loop
          $chapter_title; // Set inside inner loop

          // Skip if no chapters
          if ( $story['chapter_count'] < 1 ) {
            continue;
          }

          // Search for viable chapters...
          $search_list = array_reverse( $story['chapter_ids'] );

          foreach ( $search_list as $chapter_id ) {
            if ( get_post_meta( $chapter_id, 'fictioneer_chapter_hidden', true ) ) {
              continue;
            }

            $chapter_list[] = $chapter_id;
            break; // Only one needed
          }

          // No viable chapters
          if ( count( $chapter_list ) < 1 ) {
            continue;
          }

          // Count actually rendered cards to account for buffer
          if ( ++$card_counter > $args['count'] ) {
            break;
          }
        ?>

        <li class="card watch-last-clicked _small _info">
          <div class="card__body polygon">

            <button class="card__info-toggle toggle-last-clicked" aria-label="<?php esc_attr_e( 'Open info box', 'fictioneer' ); ?>"><i class="fa-solid fa-chevron-down"></i></button>

            <div class="card__main _grid _small">

              <?php do_action( 'fictioneer_shortcode_latest_updates_card_body', $post, $story, $args ); ?>

              <?php if ( has_post_thumbnail() ) : ?>
                <a href="<?php the_post_thumbnail_url( 'full' ); ?>" title="<?php echo esc_attr( sprintf( __( '%s Thumbnail', 'fictioneer' ), $story['title'] ) ); ?>" class="card__image cell-img" <?php echo fictioneer_get_lightbox_attribute(); ?>><?php echo get_the_post_thumbnail( $post, 'snippet', ['class' => 'no-auto-lightbox'] ); ?></a>
              <?php endif; ?>

              <h3 class="card__title _small cell-title"><a href="<?php the_permalink(); ?>" class="truncate _1-1"><?php
                if ( ! empty( $post->post_password ) ) {
                  echo '<i class="fa-solid fa-lock protected-icon"></i> ';
                }

                echo $story['title'];
              ?></a></h3>

              <div class="card__content _small cell-desc">
                <div class="truncate _cq-1-2">
                  <?php if ( get_option( 'fictioneer_show_authors' ) ) : ?>
                    <span class="card__by-author"><?php
                      printf( _x( 'by %s —', 'Small card: by {Author} —.', 'fictioneer' ), fictioneer_get_author_node() );
                    ?></span>
                  <?php endif; ?>
                  <span><?php
                    $short_description = fictioneer_first_paragraph_as_excerpt(
                      fictioneer_get_content_field( 'fictioneer_story_short_description', $post->ID )
                    );
                    echo strlen( $short_description ) < 230 ? get_the_excerpt() : $short_description;
                  ?></span>
                </div>
              </div>

              <ol class="card__link-list _small cell-list">
                <?php foreach ( $chapter_list as $chapter_id ) : ?>
                  <?php
                    // Chapter title
                    $list_title = get_post_meta( $chapter_id, 'fictioneer_chapter_list_title', true );
                    $list_title = trim( wp_strip_all_tags( $list_title ) );

                    if ( empty( $list_title ) ) {
                      $chapter_title = fictioneer_get_safe_title( $chapter_id );
                    } else {
                      $chapter_title = $list_title;
                    }

                    // Chapter excerpt
                    $chapter_excerpt = fictioneer_get_forced_excerpt( $chapter_id, 768 );
                  ?>
                  <li class="card__link-list-item">
                    <div class="card__left text-overflow-ellipsis">
                      <i class="fa-solid fa-caret-right"></i>
                      <a href="<?php the_permalink( $chapter_id ); ?>" class="card__link-list-link"><?php
                        echo $chapter_title;
                      ?></a>
                    </div>
                    <div class="card__right">
                      <?php
                        echo fictioneer_shorten_number( fictioneer_get_word_count( $chapter_id ) );
                        echo '<span class="separator-dot">&#8196;&bull;&#8196;</span>';
                        echo get_the_date( FICTIONEER_LATEST_UPDATES_LI_DATE, $chapter_id )
                      ?>
                    </div>
                  </li>
                <?php endforeach; ?>
              </ol>

            </div>

            <div class="card__overlay-infobox _excerpt escape-last-click">
              <div class="card__excerpt"><strong><?php echo $chapter_title; ?>:</strong> <?php echo $chapter_excerpt; ?></div>
            </div>

          </div>
        </li>

      <?php endwhile; ?>
    </ul>

  <?php else : ?>

    <div class="no-results"><?php _e( 'Nothing to show.', 'fictioneer' ); ?></div>

  <?php endif; wp_reset_postdata(); ?>
</section>
