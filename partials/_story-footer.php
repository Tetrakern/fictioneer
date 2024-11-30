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


// No direct access!
defined( 'ABSPATH' ) OR exit;

// Render nothing if password is required
if ( post_password_required() ) {
  return;
}

// Setup
$story_id = $args['story_id'];
$story = $args['story_data'];
$post = get_post( $story_id );
$show_log = $story['chapter_count'] > 0 && FICTIONEER_ENABLE_STORY_CHANGELOG && get_option( 'fictioneer_show_story_changelog' );
$meta_output = [];

// Status
$meta_output['status'] = '<span class="story__meta-item story__status _' . strtolower( $story['status'] ) . '"><i class="' . $story['icon'] . '"></i> ' . fcntr( $story['status'] ) . '</span>';

if ( get_option( 'fictioneer_show_story_modified_date' ) ) {
  // Modified
  $meta_output['modified'] = '<span class="story__meta-item story__date _modified" title="' . esc_attr__( 'Updated', 'fictioneer' ) . '"><i class="fa-regular fa-clock"></i> <span class="hide-below-480">' . get_the_modified_date( get_option( 'date_format' ), $post ) . '</span><span class="show-below-480">' . get_the_modified_date( FICTIONEER_STORY_FOOTER_B480_DATE, $post ) . '</span></span>';
} else {
  // Publish
  $meta_output['date'] = '<span class="story__meta-item story__date _published" title="' . esc_attr__( 'Published', 'fictioneer' ) . '"><i class="fa-solid fa-clock"></i> <span class="hide-below-480">' . get_the_time( get_option( 'date_format' ), $post ) . '</span><span class="show-below-480">' . get_the_time( FICTIONEER_STORY_FOOTER_B480_DATE, $post ) . '</span></span>';
}

// Words
$meta_output['words'] = '<span class="story__meta-item story__words" title="' . esc_attr__( 'Total Words', 'fictioneer' ) . '"><i class="fa-solid fa-font"></i> ' . $story['word_count_short'] . '</span>';

// Rating
$meta_output['rating'] = '<span class="story__meta-item story__rating" title="' . esc_attr__( 'Rating', 'fictioneer' ) . '"><i class="fa-solid fa-exclamation-circle"></i> ' . fcntr( $story['rating'] ) . '</span>';

// Checkmark
if ( $story['chapter_count'] > 0 ) {
  $meta_output['checkmark'] = '<button class="story__meta-item checkmark story__meta-checkmark" data-fictioneer-checkmarks-target="storyCheck" data-fictioneer-checkmarks-story-param="' . $story_id . '" data-action="click->fictioneer-checkmarks#toggleStory" data-status="' . esc_attr( $story['status'] ) . '" role="checkbox" aria-checked="false" aria-label="' . sprintf( esc_attr__( 'Story checkmark for %s.', 'fictioneer' ), $story['title'] ) . '"><i class="fa-solid fa-check"></i></button>';
}

// Filter
$meta_output = apply_filters( 'fictioneer_filter_story_footer_meta', $meta_output, $args, $post );

?>

<footer class="story__footer">
  <div class="story__extra">
    <?php if ( $show_log ) : ?>
      <button class="story__changelog hide-below-400" data-action="click->fictioneer#toggleModal" data-fictioneer-id-param="chapter-changelog-modal">
        <i class="fa-solid fa-clock-rotate-left"></i>
      </button>
    <?php endif; ?>
  </div>
  <div class="story__meta"><?php echo implode( '', $meta_output ); ?></div>
</footer>
