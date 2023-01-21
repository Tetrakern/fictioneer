<?php
/**
 * Partial: Collection Featured List
 *
 * Renders a statistics block with the number of published stories and chapters
 * as well as word count, comments, and the estimated reading time for all. The
 * reading time divisor can be changed under Fictioneer > General (default: 200).
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.7
 * @see single-fcn_collection.php
 *
 * @internal $args['collection']     Collection post object.
 * @internal $args['collection_id']  The collection post ID.
 * @internal $args['title']          Safe collection title.
 * @internal $args['current_page']   Number of the current page or 1.
 * @internal $args['max_pages']      Total number of pages or 1.
 * @internal $args['featured_list']  IDs of featured items in the collection.
 * @internal $args['featured_query'] Paginated query of featured items.
 */
?>

<?php

// Setup
$story_count = 0;
$chapter_count = 0;
$word_count = 0;
$processed_ids = [];

// If there are featured posts...
if ( ! empty( $args['featured_list'] ) ) {
  // Filter through queried featured posts by ID...
  foreach ( $args['featured_list'] as $post_id ) {
    // Only certain post types are processed (any possible)
    $post_type = get_post_type( $post_id );

    if ( $post_type == 'fcn_chapter' ) {
      // Make sure chapters are not counted twice if added independently
      if ( ! in_array( $post_id, $processed_ids ) ) {
        // Make sure chapter is not marked as non-chapter
        if ( ! fictioneer_get_field( 'fictioneer_chapter_no_chapter', $post_id ) ) {
          $chapter_count += 1;
          $word_count += get_post_meta( $post_id, '_word_count', true );
        }
        $processed_ids[] = $post_id;
      }
    } elseif ( $post_type == 'fcn_story' ) {
      $story = fictioneer_get_story_data( $post_id );
      $story_count += 1;

      // Count all chapters in story (if any)
      if ( $story['chapter_ids'] ) {
        foreach ( $story['chapter_ids']as $chapter_id ) {
          // Make sure chapters are not counted twice if added independently
          if ( ! in_array( $chapter_id, $processed_ids ) ) {
            // Make sure chapter is not marked as non-chapter
            if ( ! fictioneer_get_field( 'fictioneer_chapter_no_chapter', $chapter_id ) ) {
              $chapter_count += 1;
              $word_count += get_post_meta( $chapter_id, '_word_count', true );
            }
            $processed_ids[] = $chapter_id;
          }
        }
      }
    }
  }
}

?>

<section class="collection__statistics spacing-top">
  <div class="statistics">
    <div class="statistics__inline-stat">
      <strong><?php _e( 'Stories', 'fictioneer' ) ?></strong>
      <span><?php echo number_format_i18n( $story_count ); ?></span>
    </div>
    <div class="statistics__inline-stat">
      <strong><?php _e( 'Chapters', 'fictioneer' ) ?></strong>
      <span><?php echo number_format_i18n( $chapter_count ); ?></span>
    </div>
    <div class="statistics__inline-stat">
      <strong><?php _e( 'Words', 'fictioneer' ) ?></strong>
      <span><?php echo fictioneer_shorten_number( $word_count ); ?></span>
    </div>
    <div class="statistics__inline-stat">
      <strong><?php _e( 'Comments', 'fictioneer' ) ?></strong>
      <span><?php
        echo number_format_i18n(
          get_comments(
            array(
              'post_type' => array( 'fcn_chapter' ),
              'post__in' => $processed_ids,
              'status' => 1,
              'count' => true
            )
          )
        );
      ?></span>
    </div>
    <div class="statistics__inline-stat">
      <strong><?php _e( 'Reading', 'fictioneer' ) ?></strong>
      <span><?php echo fictioneer_get_reading_time_nodes( $word_count ); ?></span>
    </div>
  </div>
</section>
