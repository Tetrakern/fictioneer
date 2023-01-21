<?php
/**
 * Partial: TTS Interface
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0
 */
?>

<div id="tts-interface" class="tts-interface hidden">
  <input id="tts-settings-toggle" type="checkbox" class="hidden">
  <div class="tts-interface__wrapper">
    <div class="tts-interface__controls">
      <button id="button-tts-play" type="button" class="button play">
        <i class="fa-solid fa-play"></i>
        <span><?php _e( 'Play', 'fictioneer' ) ?></span>
      </button>
      <button id="button-tts-pause" type="button" class="button pause">
        <i class="fa-solid fa-pause"></i>
        <span><?php _e( 'Pause', 'fictioneer' ) ?></span>
      </button>
      <button id="button-tts-stop" type="button" class="button stop">
        <i class="fa-solid fa-stop"></i>
        <span><?php _e( 'Stop', 'fictioneer' ) ?></span>
      </button>
      <button id="button-tts-skip" type="button" class="button skip">
        <i class="fa-solid fa-forward"></i>
        <span><?php _e( 'Skip', 'fictioneer' ) ?></span>
      </button>
      <label for="tts-settings-toggle" class="button settings">
        <i class="fa-solid fa-cog"></i>
        <span><?php _e( 'Settings', 'fictioneer' ) ?></span>
      </label>
      <button id="button-tts-scroll" type="button" class="button skip">
        <i class="fa-solid fa-arrows-alt-v"></i>
        <span><?php _e( 'Scroll', 'fictioneer' ) ?></span>
      </button>
    </div>
    <div class="tts-interface__settings">
      <div>
        <span><?php _ex( 'Pitch', 'Pitch for text-to-speech voice.', 'fictioneer' ) ?></span>
        <input type="range" value="1" min="0.2" max="1.8" step=".1" class="tts-interface__range" id="tts-pitch-range">
        <input type="number" value="1" min="0.2" max="1.8" class="tts-interface__number" id="tts-pitch-text">
      </div>
      <div>
        <span><?php _ex( 'Rate', 'Rate for text-to-speech voice.', 'fictioneer' ) ?></span>
        <input type="range" value="1" min="0.2" max="1.8" step=".1" class="tts-interface__range" id="tts-rate-range">
        <input type="number" value="1" min="0.2" max="1.8" class="tts-interface__number" id="tts-rate-text">
      </div>
      <div>
        <span><?php _ex( 'Volume', 'Volume for text-to-speech voice.', 'fictioneer' ) ?></span>
        <input type="range" value="100" min="0" max="100" step="1" class="tts-interface__range" id="tts-volume-range">
        <input type="number" value="100" min="0" max="100" class="tts-interface__number" id="tts-volume-text">
      </div>
      <div class="tts-interface__voice-selection">
        <select id="tts-voice-select"></select>
        <label for="tts-voice-select" id="selected-voice-label" class="button button--escape"><?php _e( 'Selected Voice', 'fictioneer' ) ?></label>
      </div>
    </div>
  </div>
</div>
