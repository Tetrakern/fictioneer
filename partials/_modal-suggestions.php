<?php
/**
 * Partial: Suggestion Modal
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0
 */
?>

<div id="modal-suggestions" class="suggestions modal">

  <label for="modal-suggestions-toggle" class="background-close"></label>

  <div class="modal__wrapper suggestions__wrapper">

    <label class="close" for="modal-suggestions-toggle" tabindex="0" aria-label="<?php esc_attr_e( 'Close modal', 'fictioneer' ); ?>">
      <?php fictioneer_icon( 'fa-xmark' ); ?>
    </label>

    <h4 class="modal__header drag-anchor"><?php _ex( 'Suggestion', 'Suggestion modal heading.', 'fictioneer' ) ?></h4>

    <div class="modal__row suggestions__display">
      <div class="suggestions__original">
        <div id="modal-suggestions-original" class="suggestions__text"></div>
      </div>
    </div>

    <div class="modal__row suggestions__input">
      <div id="modal-suggestions-edit" class="suggestions__edit">
        <textarea id="modal-suggestions-input" rows="1"></textarea>
      </div>
    </div>

    <div class="modal__row suggestions__display">
      <div class="suggestions__diff">
        <div id="modal-suggestions-diff" class="suggestions__text"></div>
      </div>
    </div>

    <div class="modal__actions suggestions__actions">
      <button type="reset" id="button-suggestion-reset" class="button"><?php _e( 'Reset', 'fictioneer' ) ?></button>
      <button type="submit" id="button-suggestion-submit" class="button"><?php _e( 'Append to Comment', 'fictioneer' ) ?></button>
    </div>

  </div>

</div>
