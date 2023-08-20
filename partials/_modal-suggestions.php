<?php
/**
 * Partial: Suggestion Modal
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0
 */
?>

<?php

// No direct access!
defined( 'ABSPATH' ) OR exit;

?>

<div id="suggestions-modal" class="suggestions modal">

  <label for="suggestions-modal-toggle" class="background-close"></label>

  <div class="modal__wrapper suggestions__wrapper">

    <label class="close" for="suggestions-modal-toggle" tabindex="0" aria-label="<?php esc_attr_e( 'Close modal', 'fictioneer' ); ?>">
      <?php fictioneer_icon( 'fa-xmark' ); ?>
    </label>

    <h4 class="modal__header drag-anchor"><?php _ex( 'Suggestion', 'Suggestion modal heading.', 'fictioneer' ) ?></h4>

    <div class="modal__row suggestions__display">
      <div class="suggestions__original">
        <div id="suggestions-modal-original" class="suggestions__text"></div>
      </div>
    </div>

    <div class="modal__row suggestions__input">
      <div id="suggestions-modal-edit" class="suggestions__edit">
        <textarea id="suggestions-modal-input" rows="1"></textarea>
      </div>
    </div>

    <div class="modal__row suggestions__display">
      <div class="suggestions__diff">
        <div id="suggestions-modal-diff" class="suggestions__text"></div>
      </div>
    </div>

    <div class="modal__actions suggestions__actions">
      <button type="reset" id="button-suggestion-reset" class="button"><?php _e( 'Reset', 'fictioneer' ) ?></button>
      <button type="submit" id="button-suggestion-submit" class="button"><?php _e( 'Append to Comment', 'fictioneer' ) ?></button>
    </div>

  </div>

</div>
