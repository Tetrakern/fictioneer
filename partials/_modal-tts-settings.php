<?php
/**
 * Partial: TTS Settings Modal
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.4.6
 */


// No direct access!
defined( 'ABSPATH' ) OR exit;

?>

<div id="tts-settings-modal" class="tts-settings modal" data-fictioneer-target="modal" data-action="click->fictioneer#backgroundCloseModals keydown.esc@document->fictioneer#closeModals" data-nosnippet hidden>
  <div class="modal__wrapper narrow-inputs">

    <button class="close" aria-label="<?php esc_attr_e( 'Close modal', 'fictioneer' ); ?>" data-action="click->fictioneer#closeModals"><?php fictioneer_icon( 'fa-xmark' ); ?></button>

    <div class="modal__header drag-anchor"><?php _ex( 'TTS Settings', 'TTS modal heading.', 'fictioneer' ); ?></div>

    <div class="modal__description modal__row _small-top">
      <p><?php _e( 'The text-to-speech engine is an experimental browser feature. It might not always work as intended. On Android, you need the following app permissions for this to work: ', 'fictioneer' ); ?></p>
      <p><?php _e( '<strong>[Microphone]</strong> and <strong>[Music and audio]</strong>', 'fictioneer' ); ?></p>
    </div>

    <hr>

    <div class="modal__row">
      <div class="modal__horizontal-input-group">
        <?php fictioneer_icon( 'soundwave', 'reset', 'tts-pitch-reset', 'title="' . esc_attr_x( 'Pitch', 'Pitch for text-to-speech voice.', 'fictioneer' ) . '"' ); ?>
        <input type="range" data-default="1" value="1" min="0.2" max="1.8" step=".1" id="tts-pitch-range">
        <input type="number" data-default="1" value="1" min="0.2" max="1.8" id="tts-pitch-text">
      </div>
      <div class="modal__horizontal-input-group">
        <?php fictioneer_icon( 'pulse', 'reset', 'tts-rate-reset', 'title="' . esc_attr_x( 'Rate', 'Rate for text-to-speech voice.', 'fictioneer' ) . '"' ); ?>
        <input type="range" value="1" min="0.2" max="1.8" step=".1" id="tts-rate-range">
        <input type="number" value="1" min="0.2" max="1.8" id="tts-rate-text">
      </div>
      <div class="modal__horizontal-input-group">
        <i class="fa-solid fa-volume-high reset" id="tts-volume-reset" title="<?php echo esc_attr_x( 'Volume', 'Volume for text-to-speech voice.', 'fictioneer' ); ?>"></i>
        <input type="range" value="100" min="0" max="100" step="1" id="tts-volume-range">
        <input type="number" value="100" min="0" max="100" id="tts-volume-text">
      </div>
      <div class="modal__horizontal-input-group tts-settings__voice-selection">
        <i class="fa-solid fa-language"></i>
        <div class="select-wrapper">
          <select id="tts-voice-select"></select>
        </div>
      </div>
    </div>

  </div>
</div>
