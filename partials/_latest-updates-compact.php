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
 * @internal $args['single']            Whether to show only one chapter item. Default false.
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
 * @internal $args['source']            Whether to show author and story.
 * @internal $args['vertical']          Whether to show the vertical variant.
 * @internal $args['seamless']          Whether to render the image seamless. Default false (Customizer).
 * @internal $args['aspect_ratio']      Aspect ratio for the image. Only with vertical.
 * @internal $args['lightbox']          Whether the image is opened in the lightbox. Default true.
 * @internal $args['thumbnail']         Whether the image is rendered. Default true (Customizer).
 * @internal $args['words']             Whether to show the word count of chapter items. Default true.
 * @internal $args['date']              Whether to show the date of chapter items. Default true.
 * @internal $args['classes']           String of additional CSS classes. Default empty.
 * @internal $args['infobox']           Whether to show the info box and toggle.
 */


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
  'no_found_rows' => true, // Improve performance
  'update_post_term_cache' => false // Improve performance
);

// Use extended meta query?
if ( get_option( 'fictioneer_disable_extended_story_list_meta_queries' ) ) {
  // Extended syntax necessary due to 'fictioneer_chapters_added'
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
          $post_id = $post->ID;
          $story = fictioneer_get_story_data( $post_id, false ); // Does not refresh comment count!
          $story_link = get_post_meta( $post_id, 'fictioneer_story_redirect_link', true ) ?: get_permalink( $post_id );
          $grid_or_vertical = $args['vertical'] ? '_vertical' : '_grid';
          $chapter_list = [];
          $chapter_excerpt; // Set inside inner loop
          $chapter_title; // Set inside inner loop
          $card_classes = [];

          // Skip if no chapters
          if ( $story['chapter_count'] < 1 ) {
            continue;
          }

          // Extra classes
          if ( ! empty( $post->post_password ) ) {
            $card_classes[] = '_password';
          }

          if ( get_theme_mod( 'card_style', 'default' ) !== 'default' ) {
            $card_classes[] = '_' . get_theme_mod( 'card_style' );
          }

          if ( $args['vertical'] ) {
            $card_classes[] = '_vertical';
          }

          if ( $args['seamless'] ) {
            $card_classes[] = '_seamless';
          }

          if ( ! $args['words'] ) {
            $card_classes[] = '_no-chapter-words';
          }

          if ( ! $args['date'] ) {
            $card_classes[] = '_no-chapter-dates';
          }

          // Search for viable chapters...
          $search_list = array_reverse( $story['chapter_ids'] );

          foreach ( $search_list as $chapter_id ) {
            $chapter_post = get_post( $chapter_id );

            if ( $chapter_post->post_password || get_post_meta( $chapter_id, 'fictioneer_chapter_hidden', true ) ) {
              continue;
            }

            $chapter_list[] = $chapter_post;
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

          // Chapter excerpt
          $chapter_excerpt = fictioneer_get_forced_excerpt( $chapter_list[0]->ID, 768 );
          $show_excerpt = strlen( str_replace( '…', '', $chapter_excerpt ) ) > 2;

          // Truncate factor
          $truncate_factor = $args['vertical'] ? '_2-2' : '_cq-1-2';

          // Card attributes
          $attributes = [];

          if ( $args['aspect_ratio'] ) {
            $attributes['style'] = '--card-image-aspect-ratio: ' . $args['aspect_ratio'];
          }

          $attributes = apply_filters( 'fictioneer_filter_card_attributes', $attributes, $post, 'shortcode-latest-updates-compact' );

          $card_attributes = '';

          foreach ( $attributes as $key => $value ) {
            $card_attributes .= esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
          }
        ?>

        <li class="post-<?php echo $post_id; ?> card watch-last-clicked _small _info _story-update _compact _no-footer <?php echo implode( ' ', $card_classes ); ?>" <?php echo $card_attributes; ?>>
          <div class="card__body polygon">

            <?php if ( $show_excerpt && $args['infobox'] ) :  ?>
              <button class="card__info-toggle toggle-last-clicked" aria-label="<?php esc_attr_e( 'Open info box', 'fictioneer' ); ?>"><i class="fa-solid fa-chevron-down"></i></button>
            <?php endif; ?>

            <div class="card__main <?php echo $grid_or_vertical; ?> _small">

              <?php
                do_action( 'fictioneer_shortcode_latest_updates_card_body', $post, $story, $args );

                if ( $args['thumbnail'] ) {
                  fictioneer_output_small_card_thumbnail(
                    array(
                      'post_id' => $post_id,
                      'title' => $story['title'],
                      'classes' => 'card__image cell-img',
                      'permalink' => $story_link,
                      'lightbox' => $args['lightbox'],
                      'vertical' => $args['vertical'],
                      'seamless' => $args['seamless'],
                      'aspect_ratio' => $args['aspect_ratio']
                    )
                  );
                }
              ?>

              <h3 class="card__title _small cell-title"><a href="<?php echo $story_link; ?>" class="truncate _1-1"><?php
                if ( ! empty( $post->post_password ) ) {
                  echo '<i class="fa-solid fa-lock protected-icon"></i> ';
                }

                echo $story['title'];
              ?></a></h3>

              <div class="card__content _small cell-desc">
                <div class="truncate <?php echo $truncate_factor; ?>">
                  <?php if ( get_option( 'fictioneer_show_authors' ) && $args['source'] ) : ?>
                    <span class="card__by-author"><?php
                      printf( _x( 'by %s —', 'Small card: by {Author} —.', 'fictioneer' ), fictioneer_get_author_node() );
                    ?></span>
                  <?php endif; ?>
                  <span><?php
                    $short_description = fictioneer_first_paragraph_as_excerpt(
                      fictioneer_get_content_field( 'fictioneer_story_short_description', $post_id )
                    );
                    echo mb_strlen( $short_description, 'UTF-8' ) < 30 ? get_the_excerpt() : $short_description;
                  ?></span>
                </div>
              </div>

              <ol class="card__link-list _small cell-list">
                <?php foreach ( $chapter_list as $chapter ) : ?>
                  <?php
                    // Chapter title
                    $list_title = get_post_meta( $chapter->ID, 'fictioneer_chapter_list_title', true );
                    $list_title = trim( wp_strip_all_tags( $list_title ) );

                    if ( empty( $list_title ) ) {
                      $chapter_title = fictioneer_get_safe_title( $chapter->ID, 'shortcode-latest-updates-compact' );
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
                        $words = $args['words'] ? fictioneer_get_word_count( $chapter->ID ) : 0;

                        if ( $words ) {
                          echo '<span class="words _words-' . $words . '">' . fictioneer_shorten_number( $words ) . '</span>';
                        }

                        if ( $words && $args['date'] ) {
                          echo '<span class="separator-dot">&#8196;&bull;&#8196;</span>';
                        }

                        if ( $args['date'] ) {
                          echo '<span class="date">' .
                            get_the_date( FICTIONEER_LATEST_UPDATES_LI_DATE, $chapter->ID ) . '</span>';
                        }
                      ?>
                    </div>
                  </li>
                <?php endforeach; ?>
              </ol>

            </div>

            <?php if ( $show_excerpt && $args['infobox'] ) :  ?>
              <div class="card__overlay-infobox _excerpt escape-last-click">
                <div class="card__excerpt"><strong><?php echo $chapter_title; ?>:</strong> <?php echo $chapter_excerpt; ?></div>
              </div>
            <?php endif; ?>

          </div>
        </li>

      <?php endwhile; ?>
    </ul>

  <?php else : ?>

    <div class="no-results"><?php _e( 'Nothing to show.', 'fictioneer' ); ?></div>

  <?php endif; wp_reset_postdata(); ?>
</section>
