<?php
/**
 * Partial: TTS Settings Modal
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.4.6
 */
?>

<div id="tts-settings-modal" class="tts-settings modal">
  <label for="modal-tts-settings-toggle" class="background-close"></label>
  <div class="modal__wrapper narrow-inputs">
    <label
      class="close"
      for="modal-tts-settings-toggle"
      tabindex="0"
      aria-label="<?php esc_attr_e( 'Close modal', 'fictioneer' ); ?>"
    >
      <?php fictioneer_icon( 'fa-xmark' ); ?>
    </label>

    <h4 class="modal__header drag-anchor"><?php _ex( 'TTS Settings', 'TTS modal heading.', 'fictioneer' ); ?></h4>

    <div class="modal__description modal__row">
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
