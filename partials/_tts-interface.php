<?php
/**
 * Partial: TTS Interface
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0.0
 */


// No direct access!
defined( 'ABSPATH' ) OR exit;

?>

<div id="tts-interface" class="tts-interface hidden" data-show-settings="false" data-regex="<?php echo esc_attr( FICTIONEER_TTS_REGEX ); ?>" data-nosnippet>
  <div class="tts-interface__wrapper">
    <div class="tts-interface__controls">
      <button id="button-tts-play" type="button" class="button play">
        <i class="fa-solid fa-play"></i>
        <?php _e( 'Play', 'fictioneer' ); ?>
      </button>
      <button id="button-tts-pause" type="button" class="button pause">
        <i class="fa-solid fa-pause"></i>
        <?php _e( 'Pause', 'fictioneer' ); ?>
      </button>
      <button id="button-tts-stop" type="button" class="button stop">
        <i class="fa-solid fa-stop"></i>
        <?php _e( 'Stop', 'fictioneer' ); ?>
      </button>
      <button id="button-tts-skip" type="button" class="button skip">
        <i class="fa-solid fa-forward"></i>
        <span class="hide-below-375"><?php _e( 'Skip', 'fictioneer' ); ?></span>
      </button>
      <button id="button-tts-scroll" type="button" class="button skip">
        <i class="fa-solid fa-arrows-alt-v"></i>
        <span class="hide-below-375"><?php _e( 'Scroll', 'fictioneer' ); ?></span>
      </button>
      <button class="button settings" type="button" tabindex="0" data-action="click->fictioneer#toggleModal" data-fictioneer-id-param="tts-settings-modal">
        <i class="fa-solid fa-cog"></i>
        <span class="hide-below-480"><?php _e( 'Settings', 'fictioneer' ); ?></span>
      </button>
    </div>
  </div>
</div>
