<?php
/**
 * Partial: Sharing Modal
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0.0
 */
?>

<?php

// No direct access!
defined( 'ABSPATH' ) OR exit;

?>

<div id="sharing-modal" class="sharing modal" data-nosnippet hidden>
  <label for="modal-sharing-toggle" class="background-close"></label>
  <div class="modal__wrapper">
    <label class="close" for="modal-sharing-toggle" tabindex="0" aria-label="<?php esc_attr_e( 'Close modal', 'fictioneer' ); ?>">
      <?php fictioneer_icon( 'fa-xmark' ); ?>
    </label>
    <div class="modal__header drag-anchor"><?php _ex( 'Share', 'Share modal heading.', 'fictioneer' ); ?></div>

    <div class="modal__row media-buttons _modal">
      <?php if ( ! get_option( 'fictioneer_disable_facebook_share' ) ) : ?>
        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>" target="_blank" rel="noopener" class="media-buttons__item facebook">
          <i class="fab fa-facebook"></i>
        </a>
      <?php endif; ?>

      <?php if ( ! get_option( 'fictioneer_disable_twitter_share' ) ) : ?>
        <a href="https://twitter.com/intent/tweet/?text=<?php the_title(); ?>&url=<?php the_permalink(); ?>" target="_blank" rel="noopener" class="media-buttons__item twitter">
          <i class="fab fa-twitter"></i>
        </a>
      <?php endif; ?>

      <?php if ( ! get_option( 'fictioneer_disable_tumblr_share' ) ) : ?>
        <a href="http://tumblr.com/widgets/share/tool?canonicalUrl=<?php the_permalink(); ?>" target="_blank" rel="noopener" class="media-buttons__item tumblr">
          <i class="fa-brands fa-tumblr-square"></i>
        </a>
      <?php endif; ?>

      <?php if ( ! get_option( 'fictioneer_disable_reddit_share' ) ) : ?>
        <a href="http://www.reddit.com/submit?url=<?php the_permalink(); ?>&title=<?php echo urlencode( get_the_title() ); ?>" target="_blank" rel="noopener" class="media-buttons__item reddit">
          <i class="fa-brands fa-reddit"></i>
        </a>
      <?php endif; ?>

      <?php if ( ! get_option( 'fictioneer_disable_mastodon_share' ) ) : ?>
        <a href="https://toot.kytta.dev/?text=Check%20out%20<?php the_permalink(); ?>" target="_blank" rel="noopener" class="media-buttons__item mastodon">
          <i class="fa-brands fa-mastodon"></i>
        </a>
      <?php endif; ?>

      <?php if ( ! get_option( 'fictioneer_disable_telegram_share' ) ) : ?>
        <a href="https://t.me/share/url?url=<?php the_permalink(); ?>&text=Check%20out%20<?php echo urlencode( get_the_title() ); ?>" target="_blank" rel="noopener" class="media-buttons__item telegram">
          <i class="fa-brands fa-telegram"></i>
        </a>
      <?php endif; ?>

      <?php if ( ! get_option( 'fictioneer_disable_whatsapp_share' ) ) : ?>
        <a href="https://web.whatsapp.com/send/?text=<?php the_permalink(); ?>" target="_blank" rel="noopener" class="media-buttons__item whatsapp" data-action="share/whatsapp/share" data-original-title="whatsapp">
          <i class="fa-brands fa-whatsapp"></i>
        </a>
      <?php endif; ?>
    </div>

    <div class="modal__row">
      <input type="text" value="<?php the_permalink(); ?>" data-click="copy-to-clipboard" data-message="<?php _e( 'Link copied to clipboard!', 'fictioneer' ); ?>" name="permalink" readonly>
    </div>
  </div>
</div>
