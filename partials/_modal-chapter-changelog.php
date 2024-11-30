<?php
/**
 * Partial: Chapter Changelog
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.7.5
 */


// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$changelog = array_reverse( fictioneer_get_story_changelog( $post->ID ) );
$output = '';

// Prepare changelog
foreach ( $changelog as $entry ) {
  $output .= '[' . date_i18n( 'Y/m/d H:i:s', $entry[0] ) . '] ' . $entry[1] . "\n";
}

?>

<div id="chapter-changelog-modal" class="chapter-changelog modal" data-fictioneer-target="modal" data-action="click->fictioneer#backgroundCloseModals keydown.esc@document->fictioneer#closeModals" data-nosnippet hidden>
  <div class="modal__wrapper">

    <button class="close" aria-label="<?php esc_attr_e( 'Close modal', 'fictioneer' ); ?>" data-action="click->fictioneer#closeModals"><?php fictioneer_icon( 'fa-xmark' ); ?></button>

    <div class="modal__header drag-anchor"><?php _e( 'Changelog', 'fictioneer' ); ?></div>

    <div class="modal__row _textarea _small-top">
      <textarea class="modal__textarea _changelog" readonly><?php echo $output; ?></textarea>
    </div>

  </div>
</div>
