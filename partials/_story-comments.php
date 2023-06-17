<?php
/**
 * Partial: Story Comments
 *
 * Rendered just below the article block of the story, displaying the AJAX
 * button to call a collection of all comments of all chapters. You cannot
 * comment here though.
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

// Setup
$story_id = $args['story_id'];
$story = $args['story_data'];

// Arguments for hooks and templates/etc.
$hook_args = array(
  'story_data' => $story,
  'story_id' => $story_id,
);

?>

<?php if ( $story['comment_count'] > 0 ) : ?>

  <section class="comment-section fictioneer-comments padding-left padding-right padding-bottom">

    <h2 class="fictioneer-comments__title"><?php
      printf(
        _n(
          '<span>%s</span> <span>Comment</span>',
          '<span>%s</span> <span>Comments</span>',
          $story['comment_count'],
          'fictioneer'
        ),
        $story['comment_count']
      );
    ?></h2>

    <?php do_action( 'fictioneer_story_before_comments_list', $hook_args ); ?>

    <div class="fictioneer-comments__list">
      <ul>
        <li class="load-more-list-item">
          <button class="load-more-comments-button"><?php
            $load_n = $story['comment_count'] < get_option( 'comments_per_page' ) ? $story['comment_count'] : get_option( 'comments_per_page' );

            printf(
              _n(
                'Load latest comment (may contain spoilers)',
                'Load latest %s comments (may contain spoilers)',
                $load_n,
                'fictioneer'
              ),
              $load_n
            );
          ?></button>
        </li>
        <div class="comments-loading-placeholder hidden"><i class="fa-solid fa-spinner spinner"></i></div>
      </ul>
    </div>

  </section>

<?php endif; ?>
