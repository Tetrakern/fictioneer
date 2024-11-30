<?php
/**
 * Partial: BBCodes Modal
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0.0
 */


// No direct access!
defined( 'ABSPATH' ) OR exit;

?>

<div id="bbcodes-modal" class="bbcodes modal" data-fictioneer-target="modal" data-action="click->fictioneer#backgroundCloseModals keydown.esc@document->fictioneer#closeModals" data-nosnippet hidden>
  <div class="modal__wrapper">

    <button class="close" aria-label="<?php esc_attr_e( 'Close modal', 'fictioneer' ); ?>" data-action="click->fictioneer#closeModals"><?php fictioneer_icon( 'fa-xmark' ); ?></button>

    <div class="modal__header drag-anchor"><?php echo fcntr( 'bbcodes_modal' ); ?></div>

    <div class="modal__row modal__description _bbcodes _small-top">
      <div><?php echo fcntr( 'bbcode_b' ); ?></div>
      <div><?php echo fcntr( 'bbcode_i' ); ?></div>
      <div><?php echo fcntr( 'bbcode_s' ); ?></div>
      <div><?php echo fcntr( 'bbcode_li' ); ?></div>
      <div><?php printf( fcntr( 'bbcode_img' ), '' ); ?></div>
      <div><?php echo fcntr( 'bbcode_link' ); ?></div>
      <div><?php echo fcntr( 'bbcode_link_name' ); ?></div>
      <?php echo fcntr( 'bbcode_quote' ); ?>
      <div><?php echo fcntr( 'bbcode_spoiler' ); ?></div>
      <div><?php echo fcntr( 'bbcode_ins' ); ?></div>
      <div><?php echo fcntr( 'bbcode_del' ); ?></div>
    </div>
  </div>
</div>
