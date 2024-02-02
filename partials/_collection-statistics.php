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
 * @since 4.7.0
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

// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$statistics = fictioneer_get_collection_statistics( $args['collection_id'] );

?>

<section class="collection__statistics spacing-top">
  <div class="statistics">
    <div class="statistics__inline-stat">
      <strong><?php _e( 'Stories', 'fictioneer' ); ?></strong>
      <span><?php echo number_format_i18n( $statistics['story_count'] ); ?></span>
    </div>
    <div class="statistics__inline-stat">
      <strong><?php _e( 'Chapters', 'fictioneer' ); ?></strong>
      <span><?php echo number_format_i18n( $statistics['chapter_count']); ?></span>
    </div>
    <div class="statistics__inline-stat">
      <strong><?php _ex( 'Words', 'Word count caption in statistics.', 'fictioneer' ); ?></strong>
      <span><?php echo fictioneer_shorten_number( $statistics['word_count'] ); ?></span>
    </div>
    <div class="statistics__inline-stat">
      <strong><?php _e( 'Comments', 'fictioneer' ); ?></strong>
      <span><?php
        echo number_format_i18n( $statistics['comment_count'] );
      ?></span>
    </div>
    <div class="statistics__inline-stat">
      <strong><?php _e( 'Reading', 'fictioneer' ); ?></strong>
      <span><?php echo fictioneer_get_reading_time_nodes( $statistics['word_count'] ); ?></span>
    </div>
  </div>
</section>
