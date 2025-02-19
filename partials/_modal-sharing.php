<?php
/**
 * Partial: Sharing Modal
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0.0
 * @since 5.14.0 - Account for redirect link in story.
 * @since 5.25.0 - Added Bluesky, removed WhatsApp and Telegram, reordered buttons.
 * @since 5.27.4 - Refactored output of links and added filter.
 */


// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$post_id = get_the_ID();
$title = fictioneer_get_safe_title( $post_id );
$story_link = get_post_meta( $post_id, 'fictioneer_story_redirect_link', true ) ?: get_permalink( $post_id );
$output = [];

// Links
if ( ! get_option( 'fictioneer_disable_bluesky_share' ) ) {
  $output['bluesky'] = '<a href="https://bsky.app/intent/compose?text=' . $story_link . '" target="_blank" rel="noopener nofollow" class="media-buttons__item bluesky"><i class="fab fa-bluesky"></i></a>';
}

if ( ! get_option( 'fictioneer_disable_twitter_share' ) ) {
  $output['twitter'] = '<a href="https://twitter.com/intent/tweet/?text=' . urlencode( $title ) . '&url=' . $story_link . '" target="_blank" rel="noopener nofollow" class="media-buttons__item twitter"><i class="fab fa-twitter"></i></a>';
}

if ( ! get_option( 'fictioneer_disable_tumblr_share' ) ) {
  $output['tumblr'] = '<a href="http://tumblr.com/widgets/share/tool?canonicalUrl=' . $story_link . '" target="_blank" rel="noopener nofollow" class="media-buttons__item tumblr"><i class="fa-brands fa-tumblr-square"></i></a>';
}

if ( ! get_option( 'fictioneer_disable_reddit_share' ) ) {
  $output['reddit'] = '<a href="http://www.reddit.com/submit?url=' . $story_link . '&title=' . urlencode( $title ) . '" target="_blank" rel="noopener nofollow" class="media-buttons__item reddit"><i class="fa-brands fa-reddit"></i></a>';
}

if ( ! get_option( 'fictioneer_disable_mastodon_share' ) ) {
  $output['mastodon'] = '<a href="https://toot.kytta.dev/?text=Check%20out%20' . $story_link . '" target="_blank" rel="noopener nofollow" class="media-buttons__item mastodon"><i class="fa-brands fa-mastodon"></i></a>';
}

if ( ! get_option( 'fictioneer_disable_facebook_share' ) ) {
  $output['facebook'] = '<a href="https://www.facebook.com/sharer/sharer.php?u=' . $story_link . '" target="_blank" rel="noopener nofollow" class="media-buttons__item facebook"><i class="fab fa-facebook"></i></a>';
}

?>

<div id="sharing-modal" class="sharing modal" data-fictioneer-target="modal" data-action="click->fictioneer#backgroundCloseModals keydown.esc@document->fictioneer#closeModals" data-nosnippet hidden>
  <div class="modal__wrapper">

    <button class="close" aria-label="<?php esc_attr_e( 'Close modal', 'fictioneer' ); ?>" data-action="click->fictioneer#closeModals"><?php fictioneer_icon( 'fa-xmark' ); ?></button>

    <div class="modal__header drag-anchor"><?php _ex( 'Share', 'Share modal heading.', 'fictioneer' ); ?></div>

    <?php
      if ( ! empty( $output ) ) {
        echo '<div class="modal__row media-buttons _modal">'
          . implode( '', apply_filters( 'fictioneer_filter_sharing_modal_links', $output, $post_id, $story_link, urlencode( $title ) ) )
          . '</div>';
      }
    ?>

    <div class="modal__row">
      <input type="text" value="<?php echo $story_link; ?>" data-action="click->fictioneer#copyInput" data-message="<?php _e( 'Link copied to clipboard!', 'fictioneer' ); ?>" name="permalink" readonly>
    </div>

  </div>
</div>
