<?php
/**
 * Partial: Latest Chapters
 *
 * Renders the (buffered) HTML for the [fictioneer_latest_chapters] shortcode.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0.0
 *
 * @internal $args['type']              Type argument passed from shortcode. Default 'default'.
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
  'orderby' => $args['orderby'],
  'order' => $args['order'],
  'posts_per_page' => $args['count'] + 8, // Little buffer in case of unpublished parent story
  'no_found_rows' => true,
  'update_post_term_cache' => false
);

// Use extended meta query?
if ( get_option( 'fictioneer_disable_extended_chapter_list_meta_queries' ) ) {
  $query_args['meta_key'] = 'fictioneer_chapter_hidden';
  $query_args['meta_value'] = '0';
} else {
  $query_args['meta_query'] = array(
    'relation' => 'OR',
    array(
      'key' => 'fictioneer_chapter_hidden',
      'value' => '0'
    ),
    array(
      'key' => 'fictioneer_chapter_hidden',
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
$query_args = apply_filters( 'fictioneer_filter_shortcode_latest_chapters_query_args', $query_args, $args );

// Query chapters
$entries = fictioneer_shortcode_query( $query_args );

// Remove temporary filters
remove_filter( 'posts_where', 'fictioneer_exclude_protected_posts' );

?>

<section class="small-card-block latest-chapters <?php echo $args['classes']; ?>">
  <?php if ( $entries->have_posts() ) : ?>

    <ul class="grid-columns _collapse-on-mobile">
      <?php while ( $entries->have_posts() ) : $entries->the_post(); ?>

        <?php
          // Setup
          $story_id = get_post_meta( $post->ID, 'fictioneer_chapter_story', true );

          if ( get_post_status( $story_id ) !== 'publish' ) {
            continue;
          }

          $title = fictioneer_get_safe_title( $post->ID, 'shortcode-latest-chapters' );
          $chapter_rating = get_post_meta( $post->ID, 'fictioneer_chapter_rating', true );
          $story = $story_id ? fictioneer_get_story_data( $story_id, false ) : null; // Does not refresh comment count!
          $text_icon = get_post_meta( $post->ID, 'fictioneer_chapter_text_icon', true );
          $card_classes = [];

          // Extra card classes
          if ( ! empty( $post->post_password ) ) {
            $card_classes[] = '_password';
          }

          if ( get_theme_mod( 'card_style', 'default' ) !== 'default' ) {
            $card_classes[] = '_' . get_theme_mod( 'card_style' );
          }

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

          // Card attributes
          $attributes = apply_filters( 'fictioneer_filter_card_attributes', [], $post, 'shortcode-latest-chapters' );
          $card_attributes = '';

          foreach ( $attributes as $key => $value ) {
            $card_attributes .= esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
          }
        ?>

        <li class="card _small _chapter <?php echo implode( ' ', $card_classes ); ?>" <?php echo $card_attributes; ?>>
          <div class="card__body polygon">

            <div class="card__main _grid _small">

              <?php do_action( 'fictioneer_shortcode_latest_chapters_card_body', $post, $story, $args ); ?>

              <?php if ( $thumbnail_full ) : ?>
                <a href="<?php echo $thumbnail_full; ?>" title="<?php echo esc_attr( sprintf( __( '%s Thumbnail', 'fictioneer' ), $title ) ); ?>" class="card__image cell-img" <?php echo fictioneer_get_lightbox_attribute(); ?>><?php echo $thumbnail_snippet ?></a>
              <?php elseif ( ! empty( $text_icon ) ) : ?>
                <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( $title ); ?>" class="card__text-icon _small cell-img"><span class="text-icon"><?php echo $text_icon; ?></span></a>
              <?php endif; ?>

              <h3 class="card__title _small cell-title"><a href="<?php the_permalink(); ?>" class="truncate _1-1"><?php
                $list_title = get_post_meta( $post->ID, 'fictioneer_chapter_list_title', true );
                $list_title = trim( wp_strip_all_tags( $list_title ) );

                if ( ! empty( $post->post_password ) ) {
                  echo '<i class="fa-solid fa-lock protected-icon"></i> ';
                }

                echo $list_title ? $list_title : $title;
              ?></a></h3>

              <div class="card__content _small cell-desc">
                <div class="truncate _cq-3-4 <?php if ( ! $args['spoiler'] ) echo '_obfuscated'; ?>" data-obfuscation-target>
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
                        fictioneer_truncate( fictioneer_get_safe_title( $story_id, 'shortcode-latest-chapters' ), 24 )
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
                    fictioneer_shorten_number( fictioneer_get_word_count( $post->ID ) );

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

                  // Filter footer items
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
                  <div class="card__footer-box _right rating-letter-label tooltipped" data-tooltip="<?php echo fcntr( $chapter_rating, true ); ?>">
                    <?php echo fcntr( $chapter_rating[0] ); ?>
                  </div>
                <?php endif; ?>

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
