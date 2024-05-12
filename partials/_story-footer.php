<?php
/**
 * Partial: Story Footer
 *
 * Rendered in the single-fcn_story.php template below the content section with
 * chapters, blog posts, and custom pages if any. The last block inside the
 * <article> block.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.7.0
 * @since 5.17.0 - Turned meta data into filterable array.
 * @see single-fcn_story.php
 *
 * @internal $args['story_data']  Story data from fictioneer_get_story_data().
 * @internal $args['story_id']    Current story and post ID.
 */
?>

<?php

// No direct access!
defined( 'ABSPATH' ) OR exit;

// Only render to bottom padding if behind a password
if ( post_password_required() ) {
  // Start HTML ---> ?>
  <footer class="padding-bottom"></footer>
  <?php // <--- End HTML
  return;
}

// Setup
$story_id = $args['story_id'];
$story = $args['story_data'];
$post = get_post( $story_id );
$show_log = $story['chapter_count'] > 0 && FICTIONEER_ENABLE_STORY_CHANGELOG && get_option( 'fictioneer_show_story_changelog' );
$meta_output = [];

// Status
$meta_output['status'] = '<span class="story__meta-item story__status"><i class="' . $story['icon'] . '"></i> ' . $story['status'] . '</span>';

// Date
$meta_output['date'] = '<span class="story__meta-item story__date _published" title="' . esc_attr__( 'Published', 'fictioneer' ) . '"><i class="fa-solid fa-clock"></i> <span class="hide-below-480">' . get_the_time( get_option( 'date_format' ), $post ) . '</span><span class="show-below-480">' . get_the_time( FICTIONEER_STORY_FOOTER_B480_DATE, $post ) . '</span></span>';

// Words
$meta_output['words'] = '<span class="story__meta-item story__words" title="' . esc_attr__( 'Total Words', 'fictioneer' ) . '"><i class="fa-solid fa-font"></i> ' . $story['word_count_short'] . '</span>';

// Rating
$meta_output['rating'] = '<span class="story__meta-item story__rating" title="' . esc_attr__( 'Rating', 'fictioneer' ) . '"><i class="fa-solid fa-exclamation-circle"></i> ' . $story['rating'] . '</span>';

// Checkmark
if ( $story['chapter_count'] > 0 ) {
  $meta_output['checkmark'] = '<button class="story__meta-item checkmark story__meta-checkmark" data-type="story" data-story-id="' . $story_id . '" data-id="' . $story_id . '" data-status="' . esc_attr( $story['status'] ) . '" role="checkbox" aria-checked="false" aria-label="' . sprintf( esc_attr__( 'Story checkmark for %s.', 'fictioneer' ), $story['title'] ) . '"><i class="fa-solid fa-check"></i></button>';
}

// Filter
$meta_output = apply_filters( 'fictioneer_filter_story_footer_meta', $meta_output, $args, $post );

?>

<footer class="story__footer padding-left padding-right">
  <div class="story__extra">
    <?php if ( $show_log ) : ?>
      <label class="story__changelog hide-below-400" for="modal-chapter-changelog-toggle" tabindex="-1">
        <i class="fa-solid fa-clock-rotate-left"></i>
      </label>
    <?php endif; ?>
  </div>
  <div class="story__meta"><?php echo implode( '', $meta_output ); ?></div>
</footer>
