<?php
/**
 * Partial: Post Collection
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0.0
 *
 * @internal $args['show_type']  Whether to show the post type label. Unsafe.
 * @internal $args['cache']      Whether to account for active caching. Unsafe.
 * @internal $args['order']      Current order. Default 'desc'. Unsafe.
 * @internal $args['orderby']    Current orderby. Default 'modified'. Unsafe.
 */


// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$list_title = trim( get_post_meta( $post->ID, 'fictioneer_collection_list_title', true ) );
$title = empty( $list_title ) ? fictioneer_get_safe_title( $post->ID, 'card-collection' ) : $list_title;
$description = fictioneer_get_content_field( 'fictioneer_collection_description', $post->ID );
$statistics = fictioneer_get_collection_statistics( $post->ID );
$items = get_post_meta( $post->ID, 'fictioneer_collection_items', true );
$items = empty( $items ) ? [] : $items;
$card_classes = [];

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
$show_taxonomies = ! get_option( 'fictioneer_hide_taxonomies_on_collection_cards' ) && ( $fandoms || $characters || $genres || $tags );

// Query featured posts
if ( ! empty( $items ) ) {
  $items = new WP_Query(
    array(
      'post_type' => 'any',
      'post_status' => 'publish',
      'post__in' => $items ?: [0], // Must not be empty!
      'ignore_sticky_posts' => true,
      'orderby' => 'modified',
      'posts_per_page' => 3,
      'update_post_meta_cache' => false, // Improve performance
      'update_post_term_cache' => false, // Improve performance
      'no_found_rows' => true // Improve performance
    )
  );

  $items = $items->posts;
}

// Last rescue for empty description
if ( empty( $description ) ) {
  $description = __( 'No description provided yet.', 'fictioneer' );
}

// Extra classes
if ( get_theme_mod( 'card_style', 'default' ) !== 'default' ) {
  $card_classes[] = '_' . get_theme_mod( 'card_style' );
}

if ( get_theme_mod( 'card_image_style', 'default' ) !== 'default' ) {
  $card_classes[] = '_' . get_theme_mod( 'card_image_style' );
}

// Card attributes
$attributes = apply_filters( 'fictioneer_filter_card_attributes', [], $post, 'card-collection' );
$card_attributes = '';

foreach ( $attributes as $key => $value ) {
  $card_attributes .= esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
}

// Thumbnail attributes
$thumbnail_args = array(
  'alt' => sprintf( __( '%s Thumbnail', 'fictioneer' ), $title ),
  'class' => 'no-auto-lightbox'
);

?>

<li id="collection-card-<?php echo $post->ID; ?>" class="post-<?php echo $post->ID; ?> card _large _collection <?php echo implode( ' ', $card_classes ); ?>" <?php echo $card_attributes; ?>>
  <div class="card__body polygon">

    <div class="card__main _grid _large">

      <div class="card__header _large">

        <?php if ( $args['show_type'] ?? false ) : ?>
          <div class="card__label"><?php _ex( 'Collection', 'Collection card label.', 'fictioneer' ); ?></div>
        <?php endif; ?>

        <h3 class="card__title"><a href="<?php the_permalink(); ?>" class="truncate _1-1"><?php echo $title; ?></a></h3>

      </div>

      <?php
        // Action hook
        do_action( 'fictioneer_large_card_body_collection', $post, $items, $args );

        // Thumbnail
        if ( has_post_thumbnail() && get_theme_mod( 'card_image_style', 'default' ) !== 'none' ) {
          printf(
            '<a href="%1$s" title="%2$s" class="card__image cell-img" %3$s>%4$s</a>',
            get_the_post_thumbnail_url( null, 'full' ),
            sprintf( __( '%s Thumbnail', 'fictioneer' ), $title ),
            fictioneer_get_lightbox_attribute(),
            get_the_post_thumbnail( null, 'cover', $thumbnail_args )
          );
        }

        // Content
        printf(
          '<div class="card__content cell-desc"><div class="truncate %1$s"><span>%2$s</span></div></div>',
          count( $items ) > 2 ? '_cq-4-3' : '_4-4',
          fictioneer_truncate( $description, 512 )
        );
      ?>

      <?php if ( ! empty( $items ) ) : ?>
        <ol class="card__link-list cell-list">
          <?php foreach ( $items as $item ) : ?>
            <?php
              // Extra classes
              $list_item_classes = [];

              if ( ! empty( $item->post_password ) ) {
                $list_item_classes[] = '_password';
              }
            ?>
            <li class="card__link-list-item <?php echo implode( ' ', $list_item_classes ); ?>">
              <div class="card__left text-overflow-ellipsis">
                <i class="fa-solid fa-caret-right"></i>
                <a href="<?php the_permalink( $item->ID ); ?>" class="card__link-list-link"><?php
                  $list_title = $item->post_type == 'fcn_chapter' ?
                    get_post_meta( $item->ID, 'fictioneer_chapter_list_title', true ) : 0;

                  if ( $list_title ) {
                    echo wp_strip_all_tags( $list_title );
                  } else {
                    echo fictioneer_get_safe_title( $item->ID, 'card-collection-list' );
                  }
                ?></a>
              </div>
              <div class="card__right">
                <?php
                  printf(
                    '%1$s<span class="separator-dot">&#8196;&bull;&#8196;</span>%2$s',
                    get_post_type_object( $item->post_type )->labels->singular_name,
                    get_the_modified_date( FICTIONEER_CARD_COLLECTION_LI_DATE, $item->ID )
                  );
                ?>
              </div>
            </li>
          <?php endforeach; ?>
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
        <div class="card__footer-box text-overflow-ellipsis"><?php
          // Build footer items
          $footer_items = [];

          $footer_items['stories'] = '<i class="card-footer-icon fa-solid fa-book" title="' .
            esc_attr__( 'Stories', 'fictioneer' ) . '"></i> ' . $statistics['story_count'];

          $footer_items['chapters'] = '<i class="card-footer-icon fa-solid fa-list" title="' .
            esc_attr__( 'Chapters', 'fictioneer' ) . '"></i> ' . $statistics['chapter_count'];

          $footer_items['words'] = '<i class="card-footer-icon fa-solid fa-font" title="' .
            esc_attr__( 'Total Words', 'fictioneer' ) . '"></i> ' . fictioneer_shorten_number( $statistics['word_count'] );

          if ( ( $args['orderby'] ?? 0 ) === 'date' ) {
            $footer_items['publish_date'] = '<i class="card-footer-icon fa-solid fa-clock" title="' .
              esc_attr__( 'Published', 'fictioneer' ) .'"></i> ' .
              get_the_date( FICTIONEER_CARD_COLLECTION_FOOTER_DATE );
          } else {
            $footer_items['modified_date'] = '<i class="card-footer-icon fa-regular fa-clock" title="' .
              esc_attr__( 'Last Updated', 'fictioneer' ) .'"></i> ' .
              get_the_modified_date( FICTIONEER_CARD_COLLECTION_FOOTER_DATE );
          }

          $footer_items['comments'] = '<i class="card-footer-icon fa-solid fa-message" title="' .
            esc_attr__( 'Comments', 'fictioneer' ) . '"></i> ' . $statistics['comment_count'];

          // Filter footer items
          $footer_items = apply_filters( 'fictioneer_filter_collection_card_footer', $footer_items, $post, $args, $items );

          // Implode and render footer items
          echo implode( ' ', $footer_items );
        ?></div>
      </div>

    </div>

  </div>
</li>
