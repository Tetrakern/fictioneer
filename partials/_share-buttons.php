<?php
/**
 * Partial: Share Buttons
 *
 * Renders a button to open the sharing modal and shows links to
 * Feedly, InoReader, and the RSS feed.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.7.0
 * @see partials/_modal-sharing.php
 */
?>

<?php

// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$post_type = get_post_type();
$post_id = get_the_ID();
$feed = fictioneer_get_rss_link( $post_type );
$show_feed = $post_type !== 'fcn_story' ||
  ( $post_type === 'fcn_story' && get_post_meta( $post_id, 'fictioneer_story_status', true ) !== 'Oneshot' );

?>

<div class="media-buttons">
  <label
    for="modal-sharing-toggle"
    class="tooltipped media-buttons__item"
    data-tooltip="<?php esc_attr_e( 'Share', 'fictioneer' ); ?>"
    tabindex="0"
  ><i class="fa-solid fa-share-nodes"></i></label>

  <?php if ( $feed && $show_feed ) : ?>

    <?php $feed_url = urlencode( $feed ); ?>

    <?php if ( $post_type === 'fcn_story' && ! get_post_meta( $post_id, 'fictioneer_story_hidden', true ) ) : ?>
      <a
        href="<?php echo $feed; ?>"
        class="rss-link tooltipped media-buttons__item"
        target="_blank"
        rel="noopener"
        data-tooltip="<?php esc_attr_e( 'Story RSS Feed', 'fictioneer' ); ?>"
        aria-label="<?php esc_attr_e( 'Story RSS Feed', 'fictioneer' ); ?>"
      ><?php fictioneer_icon( 'fa-rss' ); ?></a>
    <?php endif; ?>

    <a
      href="https://feedly.com/i/subscription/feed/<?php echo $feed_url; ?>"
      class="feedly tooltipped hide-below-640 media-buttons__item"
      target="_blank"
      rel="noopener"
      data-tooltip="<?php esc_attr_e( 'Follow on Feedly', 'fictioneer' ); ?>"
      aria-label="<?php esc_attr_e( 'Follow on Feedly', 'fictioneer' ); ?>"
    ><?php fictioneer_icon( 'feedly' ); ?></a>

    <a
      href="https://www.inoreader.com/?add_feed=<?php echo $feed_url; ?>"
      class="inoreader tooltipped hide-below-640 media-buttons__item"
      target="_blank"
      rel="noopener"
      data-tooltip="<?php esc_attr_e( 'Follow on Inoreader', 'fictioneer' ); ?>"
      aria-label="<?php esc_attr_e( 'Follow on Inoreader', 'fictioneer' ); ?>"
    ><?php fictioneer_icon( 'inoreader' ); ?></a>

  <?php endif; ?>
</div>
