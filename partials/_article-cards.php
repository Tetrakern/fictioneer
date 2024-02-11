<?php
/**
 * Partial: Article Cards
 *
 * Renders the (buffered) HTML for the [fictioneer_article_cards] shortcode.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.7.3
 *
 * @internal $args['post_type']         Array of post types to query. Default 'post'.
 * @internal $args['ignore_sticky']     Whether to ignore sticky flags. Default false.
 * @internal $args['ignore_protected']  Whether to ignore protected posts. Default false.
 * @internal $args['count']             Number of posts to display. Default -1.
 * @internal $args['author']            Author to query posts for. Default empty.
 * @internal $args['order']             Order of posts. Default 'DESC'.
 * @internal $args['orderby']           Sorting of posts. Default 'date'.
 * @internal $args['page']              The current page. Default 1.
 * @internal $args['post_ids']          Array of post IDs. Default empty.
 * @internal $args['author_ids']        Array of author IDs. Default empty.
 * @internal $args['excluded_authors']  Array of author IDs to exclude. Default empty.
 * @internal $args['excluded_cats']     Array of category IDs to exclude. Default empty.
 * @internal $args['excluded_tags']     Array of tag IDs to exclude. Default empty.
 * @internal $args['taxonomies']        Array of taxonomy arrays. Default empty.
 * @internal $args['relation']          Relationship between taxonomies. Default 'AND'.
 * @internal $args['classes']           String of additional CSS classes. Default empty.
 */
?>

<?php

// No direct access!
defined( 'ABSPATH' ) OR exit;

// Arguments
$query_args = array(
  'fictioneer_query_name' => 'article_cards',
  'post_type' => $args['post_type'],
  'post_status' => 'publish',
  'post__in' => $args['post_ids'], // May be empty!
  'order' => $args['order'],
  'orderby' => $args['orderby'],
  'ignore_sticky_posts' => $args['ignore_sticky']
);

// Pagination or count?
if ( $args['count'] > 0 ) {
  $query_args['posts_per_page'] = $args['count'];
  $query_args['no_found_rows'] = true;
} else {
  $query_args['paged'] = $args['page'];
  $query_args['posts_per_page'] = $args['posts_per_page'];
}

// Author?
if ( ! empty( $args['author'] ) ) {
  $query_args['author_name'] = $args['author'];
}

// Author IDs?
if ( ! empty( $args['author_ids'] ) ) {
  $query_args['author__in'] = $args['author_ids'];
}

// Excluded authors?
if ( ! empty( $args['excluded_authors'] ) ) {
  $query_args['author__not_in'] = $args['excluded_authors'];
}

// Taxonomies?
if ( ! empty( $args['taxonomies'] ) ) {
  $query_args['tax_query'] = fictioneer_get_shortcode_tax_query( $args );
}

// Excluded categories?
if ( ! empty( $args['excluded_cats'] ) ) {
  $query_args['category__not_in'] = $args['excluded_cats'];
}

// Excluded tags?
if ( ! empty( $args['excluded_tags'] ) ) {
  $query_args['tag__not_in'] = $args['excluded_tags'];
}

// Ignore protected?
if ( ! $args['ignore_protected'] ) {
  $obfuscation = str_repeat( _x( '&#183; ', 'Protected post content obfuscation character.', 'fictioneer' ), 256 );
  $obfuscation = apply_filters( 'fictioneer_filter_obfuscation_string', $obfuscation, $post );
} else {
  add_filter( 'posts_where', 'fictioneer_exclude_protected_posts' );
}

// Apply filters
$query_args = apply_filters( 'fictioneer_filter_shortcode_article_cards_query_args', $query_args, $args );

// Query
$query = fictioneer_shortcode_query( $query_args );

// Remove temporary filters
remove_filter( 'posts_where', 'fictioneer_exclude_protected_posts' );

// Unique ID
$unique_id = wp_unique_id( 'article-block-' );

// Pagination
$pag_args = array(
  'current' => max( 1, $args['page'] ),
  'total' => $query->max_num_pages,
  'prev_text' => fcntr( 'previous' ),
  'next_text' => fcntr( 'next' ),
  'add_fragment' => "#{$unique_id}"
);

?>

<section id="<?php echo $unique_id; ?>" class="scroll-margin-top article-card-block <?php echo esc_attr( $args['classes'] ); ?>">

  <?php if ( $query->have_posts() ) : ?>

    <ul class="grid-columns _collapse-on-mobile">
      <?php
        while ( $query->have_posts() ) {
          $query->the_post();

          // Setup
          $story_id = ( $post->post_type === 'fcn_story' ) ? $post->ID : null;
          $title = fictioneer_get_safe_title( $post->ID );
          $permalink = get_permalink();
          $categories = wp_get_post_categories( $post->ID );
          $tags = get_the_tags();
          $fandoms = get_the_terms( $post, 'fcn_fandom' );
          $characters = get_the_terms( $post, 'fcn_character' );
          $genres = get_the_terms( $post, 'fcn_genre' );
          $card_classes = [];

          // Thumbnail
          $landscape_image_id = get_post_meta( $post->ID, 'fictioneer_landscape_image', true );
          $thumbnail = null;

          $image_args = array(
            'alt' => sprintf( __( '%s Thumbnail', 'fictioneer' ), $title ),
            'class' => 'no-auto-lightbox'
          );

          if ( empty( $landscape_image_id ) ) {
            $thumbnail = get_the_post_thumbnail( $post, 'medium', $image_args );
          }

          // Chapter story?
          if ( $post->post_type === 'fcn_chapter' ) {
            $story_id = get_post_meta( $post->ID, 'fictioneer_chapter_story', true );
          }

          // Extra classes
          if ( get_theme_mod( 'card_style', 'default' ) === 'unfolded' ) {
            $card_classes[] = '_unfolded';
          }

          // Start HTML ---> ?>
          <li id="article-card-<?php the_ID(); ?>" class="card _article <?php echo implode( ' ', $card_classes ); ?>">
            <article class="card__body _article polygon">

              <div class="card__main _article">

                <?php
                  // Try parent thumbnail (if any)
                  if ( ! $landscape_image_id && ! $thumbnail && $story_id ) {
                    $landscape_image_id = get_post_meta( $story_id, 'fictioneer_landscape_image', true );
                    $thumbnail = get_the_post_thumbnail( $story_id, 'medium', $image_args );
                  }

                  if ( ! empty( $landscape_image_id ) ) {
                    $thumbnail = wp_get_attachment_image( $landscape_image_id, 'medium', false, $image_args );
                    echo "<a href='{$permalink}' class='card__image _article cell-img'>{$thumbnail}</a>";
                  } elseif ( ! empty( $thumbnail ) ) {
                    echo "<a href='{$permalink}' class='card__image _article cell-img'>{$thumbnail}</a>";
                  } else {
                    echo "<a href='{$permalink}'  class='card__image _article cell-img _default'></a>";
                  }
                ?>

                <h3 class="card__title _article _small"><a href="<?php the_permalink(); ?>" class="truncate _1-1"><?php echo $title; ?></a></h3>

                <?php if ( post_password_required() ) : ?>
                  <div class="card__content _small _article cell-desc truncate _5-5"><span><?php echo $obfuscation; ?></span></div>
                <?php else : ?>
                  <div class="card__content _small _article cell-desc truncate _5-5"><span><?php the_excerpt(); ?></span></div>
                <?php endif; ?>

              </div>

              <?php if ( $categories || $tags || $genres || $fandoms || $characters ) : ?>
                <div class="card__tag-list _small _scrolling cell-tax">
                  <div class="card__h-scroll">
                    <?php
                      $output = [];

                      if ( $categories ) {
                        foreach ( $categories as $cat ) {
                          $output[] = '<a href="' . get_category_link( $cat ) . '" class="tag-pill _inline _category">' . get_category( $cat )->name . '</a>';
                        }
                      }

                      if ( $fandoms ) {
                        foreach ( $fandoms as $fandom ) {
                          $output[] = '<a href="' . get_tag_link( $fandom ) . '" class="tag-pill _inline _fandom">' . $fandom->name . '</a>';
                        }
                      }

                      if ( $genres ) {
                        foreach ( $genres as $genre ) {
                          $output[] = '<a href="' . get_tag_link( $genre ) . '" class="tag-pill _inline _genre">' . $genre->name . '</a>';
                        }
                      }

                      if ( $tags ) {
                        foreach ( $tags as $tag ) {
                          $output[] = '<a href="' . get_tag_link( $tag ) . '" class="tag-pill _inline">' . $tag->name . '</a>';
                        }
                      }

                      if ( $characters ) {
                        foreach ( $characters as $character ) {
                          $output[] = '<a href="' . get_tag_link( $character ) . '" class="tag-pill _inline _character">' . $character->name . '</a>';
                        }
                      }

                      // Implode with three-per-em spaces around a bullet
                      echo implode( '&#8196;&bull;&#8196;', $output );
                    ?>
                  </div>
                </div>
              <?php endif; ?>

              <div class="card__footer _article _small">
                <div class="card__footer-box text-overflow-ellipsis"><?php
                  // Build footer items
                  $footer_items = [];

                  if ( get_option( 'fictioneer_show_authors' ) ) {
                    $footer_items['author'] = '<i class="card-footer-icon fa-solid fa-circle-user"></i> ' .
                      fictioneer_get_author_node( get_the_author_meta( 'ID' ) );
                  }

                  if ( $args['orderby'] == 'modified' ) {
                    $footer_items['modified_date'] = '<i class="card-footer-icon fa-regular fa-clock" title="' .
                      esc_attr__( 'Last Updated', 'fictioneer' ) . '"></i> ' .
                      get_the_modified_date( FICTIONEER_CARD_ARTICLE_FOOTER_DATE, $post );
                  } else {
                    $footer_items['publish_date'] = '<i class="card-footer-icon fa-solid fa-clock" title="' .
                      esc_attr__( 'Published', 'fictioneer' ) .'"></i> ' . get_the_date( FICTIONEER_CARD_ARTICLE_FOOTER_DATE );
                  }

                  $footer_items['comments'] = '<i class="card-footer-icon fa-solid fa-message" title="' .
                    esc_attr__( 'Comments', 'fictioneer' ) . '"></i> ' . get_comments_number( $post );

                  // Filter footer items
                  $footer_items = apply_filters( 'fictioneer_filter_article_card_footer', $footer_items, $post );

                  // Implode and render footer items
                  echo implode( ' ', $footer_items );
                ?></div>
              </div>

            </article>
          </li>
          <?php // <--- End HTML
        }
        wp_reset_postdata();
      ?>
    </ul>

    <?php if ( $args['count'] < 1 && $query->max_num_pages > 1 ) : ?>
      <nav class="pagination"><?php echo fictioneer_paginate_links( $pag_args ); ?></nav>
    <?php endif; ?>

  <?php else : ?>

    <div class="no-results"><?php _e( 'Nothing to show.', 'fictioneer' ); ?></div>

  <?php endif; ?>

</section>
