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

<div id="chapter-changelog-modal" class="chapter-changelog modal" data-nosnippet hidden>
  <label for="modal-chapter-changelog-toggle" class="background-close"></label>
  <div class="modal__wrapper">
    <label class="close" for="modal-chapter-changelog-toggle" tabindex="0" aria-label="<?php esc_attr_e( 'Close modal', 'fictioneer' ); ?>">
      <?php fictioneer_icon( 'fa-xmark' ); ?>
    </label>
    <div class="modal__header drag-anchor"><?php _e( 'Changelog', 'fictioneer' ); ?></div>

    <div class="modal__row _textarea _small-top">
      <textarea class="modal__textarea _changelog" readonly><?php echo $output; ?></textarea>
    </div>
  </div>
</div>
