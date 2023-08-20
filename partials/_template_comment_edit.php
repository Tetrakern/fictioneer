<?php
/**
 * Partial: Comment Edit Template
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.4.5
 */
?>

<?php

// No direct access!
defined( 'ABSPATH' ) OR exit;

?>

<template class="comment-edit-actions-template">
  <div class="fictioneer-comment__edit-actions">
    <button
      type="button"
      class="comment-inline-edit-submit button"
      data-enabled="<?php echo esc_attr_x( 'Submit Changes', 'Comment inline edit submit button.', 'fictioneer' ); ?>"
      data-disabled="<?php esc_attr_e( 'Updating…', 'fictioneer' ); ?>"
      data-click="submit-inline-comment-edit"
    ><?php _ex( 'Submit Changes', 'Comment inline edit submit button.', 'fictioneer' ); ?></button>
    <button type="button" class="comment-inline-edit-cancel button _secondary" data-click="cancel-inline-comment-edit"><?php _ex( 'Cancel', 'Comment inline edit cancel button.', 'fictioneer' ); ?></button>
  </div>
</template>
