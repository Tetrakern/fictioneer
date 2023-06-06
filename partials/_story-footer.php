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
 * @since 4.7
 * @see single-fcn_story.php
 *
 * @internal $args['story_data'] Story data from fictioneer_get_story_data().
 * @internal $args['story_id']   Current story and post ID.
 */
?>

<?php

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

?>

<footer class="story__footer padding-left padding-right">
  <div class="story__meta truncate _1-1">
    <span class="story__status">
      <i class="<?php echo $story['icon']; ?>"></i>
      <?php echo $story['status']; ?>
    </span>
    <span class="story__date _published" title="<?php esc_attr_e( 'Published', 'fictioneer' ) ?>">
      <i class="fa-solid fa-clock"></i>
      <span class="hide-below-480"><?php the_time( get_option( 'date_format' ) ); ?></span>
      <span class="show-below-480"><?php the_time( FICTIONEER_STORY_FOOTER_B480_DATE ); ?></span>
    </span>
    <span class="story__words" title="<?php esc_attr_e( 'Total Words', 'fictioneer' ) ?>">
      <i class="fa-solid fa-font"></i>
      <?php echo $story['word_count_short']; ?>
    </span>
    <span class="story__rating" title="<?php esc_attr_e( 'Rating', 'fictioneer' ) ?>">
      <i class="fa-solid fa-exclamation-circle"></i>
      <?php echo $story['rating']; ?>
    </span>
    <?php if ( $story['chapter_count'] > 0 ): ?>
      <button
      class="checkmark story__meta-checkmark"
      data-type="story"
      data-story-id="<?php echo $story_id; ?>"
      data-id="<?php echo $story_id; ?>"
      data-status="<?php echo esc_attr( $story['status'] ); ?>"
      role="checkbox"
      aria-checked="false"
      aria-label="<?php printf( esc_attr__( 'Story checkmark for %s.', 'fictioneer' ), $story['title'] ); ?>"
    ><i class="fa-solid fa-check"></i></button>
    <?php endif; ?>
  </div>
</footer>
