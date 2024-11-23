<?php
/**
 * Partial: Sharing Modal
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0.0
 * @since 5.14.0 - Account for redirect link in story.
 * @since 5.25.0 - Added Bluesky, removed WhatsApp and Telegram, reordered buttons.
 */


// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$story_link = get_post_meta( get_the_ID(), 'fictioneer_story_redirect_link', true ) ?: get_permalink( get_the_ID() );

?>

<div id="sharing-modal" class="sharing modal" data-nosnippet hidden>
  <label for="modal-sharing-toggle" class="background-close"></label>
  <div class="modal__wrapper">
    <label class="close" for="modal-sharing-toggle" tabindex="0" aria-label="<?php esc_attr_e( 'Close modal', 'fictioneer' ); ?>">
      <?php fictioneer_icon( 'fa-xmark' ); ?>
    </label>
    <div class="modal__header drag-anchor"><?php _ex( 'Share', 'Share modal heading.', 'fictioneer' ); ?></div>

    <div class="modal__row media-buttons _modal">
      <?php if ( ! get_option( 'fictioneer_disable_bluesky_share' ) ) : ?>
        <a href="https://bsky.app/intent/compose?text=<?php echo $story_link; ?>" target="_blank" rel="noopener nofollow" class="media-buttons__item bluesky">
          <i class="fab fa-bluesky"></i>
        </a>
      <?php endif; ?>

      <?php if ( ! get_option( 'fictioneer_disable_twitter_share' ) ) : ?>
        <a href="https://twitter.com/intent/tweet/?text=<?php the_title(); ?>&url=<?php echo $story_link; ?>" target="_blank" rel="noopener nofollow" class="media-buttons__item twitter">
          <i class="fab fa-twitter"></i>
        </a>
      <?php endif; ?>

      <?php if ( ! get_option( 'fictioneer_disable_tumblr_share' ) ) : ?>
        <a href="http://tumblr.com/widgets/share/tool?canonicalUrl=<?php echo $story_link; ?>" target="_blank" rel="noopener nofollow" class="media-buttons__item tumblr">
          <i class="fa-brands fa-tumblr-square"></i>
        </a>
      <?php endif; ?>

      <?php if ( ! get_option( 'fictioneer_disable_reddit_share' ) ) : ?>
        <a href="http://www.reddit.com/submit?url=<?php echo $story_link; ?>&title=<?php echo urlencode( get_the_title() ); ?>" target="_blank" rel="noopener nofollow" class="media-buttons__item reddit">
          <i class="fa-brands fa-reddit"></i>
        </a>
      <?php endif; ?>

      <?php if ( ! get_option( 'fictioneer_disable_mastodon_share' ) ) : ?>
        <a href="https://toot.kytta.dev/?text=Check%20out%20<?php echo $story_link; ?>" target="_blank" rel="noopener nofollow" class="media-buttons__item mastodon">
          <i class="fa-brands fa-mastodon"></i>
        </a>
      <?php endif; ?>

      <?php if ( ! get_option( 'fictioneer_disable_facebook_share' ) ) : ?>
        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $story_link; ?>" target="_blank" rel="noopener nofollow" class="media-buttons__item facebook">
          <i class="fab fa-facebook"></i>
        </a>
      <?php endif; ?>
    </div>

    <div class="modal__row">
      <input type="text" value="<?php echo $story_link; ?>" data-action="click->fictioneer#copyInput" data-message="<?php _e( 'Link copied to clipboard!', 'fictioneer' ); ?>" name="permalink" readonly>
    </div>
  </div>
</div>
