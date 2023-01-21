<?php
/**
 * Partial: Share Buttons
 *
 * Renders a button to open the sharing modal and shows links to
 * Feedly, InoReader, and the RSS feed.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.7
 * @see partials/_modal-sharing.php
 */
?>

<?php

// Setup
$feed = fictioneer_get_rss_link();

?>

<div class="media-buttons">
  <label for="modal-sharing-toggle" class="tooltipped" data-tooltip="<?php esc_attr_e( 'Share', 'fictioneer' ); ?>"><i class="fa-solid fa-share-nodes"></i></label>

  <?php if ( $feed ) : ?>

    <?php $feed_url = urlencode( $feed ); ?>

    <?php if ( get_post_type() == 'fcn_story' ) : ?>
      <a
        href="<?php echo $feed; ?>"
        class="rss-link tooltipped"
        target="_blank"
        rel="noopener"
        data-tooltip="<?php echo esc_attr_x( 'Story&nbsp;RSS&nbsp;Feed', 'Story RSS Feed tooltip; use nbsp to achieve desired look.', 'fictioneer' ); ?>"
      ><?php fictioneer_icon( 'fa-rss' ); ?></a>
    <?php endif; ?>

    <a
      href="https://feedly.com/i/subscription/feed/<?php echo $feed_url; ?>"
      class="feedly tooltipped hide-below-640"
      target="_blank"
      rel="noopener"
      data-tooltip="<?php echo esc_attr_x( 'Follow&nbsp;on&nbsp;Feedly', 'Follow on Feedly tooltip; use nbsp to achieve desired look.', 'fictioneer' ); ?>"
    ><?php fictioneer_icon( 'feedly' ); ?></a>

    <a
      href="https://www.inoreader.com/?add_feed=<?php echo $feed_url; ?>"
      class="inoreader tooltipped hide-below-640"
      target="_blank"
      rel="noopener"
      data-tooltip="<?php echo esc_attr_x( 'Follow&nbsp;on&nbsp;Inoreader', 'Follow on Inoreader tooltip; use nbsp to achieve desired look.', 'fictioneer' ); ?>"
    ><?php fictioneer_icon( 'inoreader' ); ?></a>

  <?php endif; ?>
</div>
