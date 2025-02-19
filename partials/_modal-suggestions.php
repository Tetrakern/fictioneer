<?php
/**
 * Partial: Suggestion Modal
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0.0
 */


// No direct access!
defined( 'ABSPATH' ) OR exit;

?>

<div id="suggestions-modal" class="suggestions modal" data-fictioneer-target="modal" data-action="click->fictioneer#backgroundCloseModals keydown.esc@document->fictioneer#closeModals" data-nosnippet hidden>
  <div class="modal__wrapper suggestions__wrapper">

    <button class="close" aria-label="<?php esc_attr_e( 'Close modal', 'fictioneer' ); ?>" data-action="click->fictioneer#closeModals"><?php fictioneer_icon( 'fa-xmark' ); ?></button>

    <div class="modal__header drag-anchor"><?php _ex( 'Suggestion', 'Suggestion modal heading.', 'fictioneer' ); ?></div>

    <div class="modal__row suggestions__display _small-top">
      <div class="suggestions__box suggestions__original">
        <div id="suggestions-modal-original" class="suggestions__text"></div>
      </div>
    </div>

    <div class="modal__row suggestions__input">
      <div id="suggestions-modal-edit" class="suggestions__box suggestions__edit">
        <textarea id="suggestions-modal-input" rows="1"></textarea>
      </div>
    </div>

    <div class="modal__row suggestions__display">
      <div class="suggestions__box suggestions__diff">
        <div id="suggestions-modal-diff" class="suggestions__text"></div>
      </div>
    </div>

    <div class="modal__actions suggestions__actions">
      <button type="reset" id="button-suggestion-reset" class="button"><?php _e( 'Reset', 'fictioneer' ); ?></button>
      <button type="submit" id="button-suggestion-submit" class="button"><?php _e( 'Append to Comment', 'fictioneer' ); ?></button>
    </div>

  </div>
</div>
