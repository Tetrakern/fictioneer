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
 * @internal $args['collection']      Collection post object.
 * @internal $args['collection_id']   The collection post ID.
 * @internal $args['title']           Safe collection title.
 * @internal $args['current_page']    Number of the current page or 1.
 * @internal $args['max_pages']       Total number of pages or 1.
 * @internal $args['featured_list']   IDs of featured items in the collection. Already cleaned.
 * @internal $args['featured_query']  Paginated query of featured items. Already cleaned.
 */
?>

<?php

// Setup
$story_count = 0;
$word_count = 0;
$query_chapter_ids = [];

// If there are featured posts...
if ( ! empty( $args['featured_list'] ) ) {
  // Filter through queried featured posts by ID...
  foreach ( $args['featured_list'] as $post_id ) {
    // Only certain post types are processed (any possible)
    $post_type = get_post_type( $post_id );

    if ( $post_type == 'fcn_chapter' ) {
      $query_chapter_ids[] = $post_id;
    } elseif ( $post_type == 'fcn_story' ) {
      $story = fictioneer_get_story_data( $post_id, false ); // Does not refresh comment count!
      $story_count += 1;

      // Count all chapters in story (if any)
      if ( ! empty( $story['chapter_ids'] ) ) {
        $query_chapter_ids = array_merge( $query_chapter_ids, $story['chapter_ids'] );
      }
    }
  }

  // Remove duplicates
  $query_chapter_ids = array_unique( $query_chapter_ids );

  // Query eligible chapters
  $chapter_query_args = array(
    'post_type' => 'fcn_chapter',
    'post_status' => 'publish',
    'post__in' => $query_chapter_ids,
    'posts_per_page' => -1,
    'update_post_term_cache' => false
  );

  $chapters = new WP_Query( $chapter_query_args );

  // Count words
  foreach ( $chapters->posts as $chapter ) {
    // This is about 50 times faster than using a meta query lol
    if (
      ! fictioneer_get_field( 'fictioneer_chapter_hidden', $chapter->ID ) &&
      ! fictioneer_get_field( 'fictioneer_chapter_no_chapter', $chapter->ID )
    ) {
      $words = fictioneer_get_field( '_word_count', $chapter->ID );
      $word_count += intval( $words );
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
      <span><?php echo number_format_i18n( $chapters->found_posts ); ?></span>
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
              'post_type' => 'fcn_chapter',
              'post__in' => $query_chapter_ids,
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
