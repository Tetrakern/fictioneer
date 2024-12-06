<?php
/**
 * Partial: Comment Edit Template
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.4.5
 * @since 5.27.0 - Updated for Stimulus Controller.
 */


// No direct access!
defined( 'ABSPATH' ) OR exit;

?>

<template id="template-comment-inline-edit-form">
  <div class="fictioneer-comment__edit-actions" data-fictioneer-comment-target="inlineEditButtons">
    <button
      type="button"
      class="comment-inline-edit-submit button"
      data-enabled="<?php echo esc_attr_x( 'Submit Changes', 'Comment inline edit submit button.', 'fictioneer' ); ?>"
      data-disabled="<?php esc_attr_e( 'Updating…', 'fictioneer' ); ?>"
      data-fictioneer-comment-target="inlineEditSubmit"
      data-action="click->fictioneer-comment#submitEditInline"
    ><?php _ex( 'Submit Changes', 'Comment inline edit submit button.', 'fictioneer' ); ?></button>
    <button
      type="button"
      class="comment-inline-edit-cancel button _secondary"
      data-action="click->fictioneer-comment#cancelEditInline"
    ><?php _ex( 'Cancel', 'Comment inline edit cancel button.', 'fictioneer' ); ?></button>
  </div>
</template>
