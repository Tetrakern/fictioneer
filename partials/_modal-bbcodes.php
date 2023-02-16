<?php
/**
 * Partial: BBCodes Modal
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0
 */
?>

<div id="bbcodes-modal" class="bbcodes modal">
  <label for="modal-bbcodes-toggle" class="background-close"></label>
  <div class="modal__wrapper">
    <label class="close" for="modal-bbcodes-toggle" tabindex="0" aria-label="<?php esc_attr_e( 'Close modal', 'fictioneer' ); ?>">
      <?php fictioneer_icon( 'fa-xmark' ); ?>
    </label>
    <h4 class="modal__header drag-anchor"><?php echo fcntr( 'bbcodes_modal' ); ?></h4>

    <?php
      $preview_image = '';

      if ( has_site_icon() ) {
        $preview_image = '<img src="' . get_site_icon_url( 13 ) . '">';
      }
    ?>

    <div class="modal__row modal__description _bbcodes">
      <div><?php echo fcntr( 'bbcode_b' ); ?></div>
      <div><?php echo fcntr( 'bbcode_i' ); ?></div>
      <div><?php echo fcntr( 'bbcode_s' ); ?></div>
      <div><?php echo fcntr( 'bbcode_li' ); ?></div>
      <div><?php printf( fcntr( 'bbcode_img' ), $preview_image ); ?></div>
      <div><?php echo fcntr( 'bbcode_link' ); ?></div>
      <div><?php echo fcntr( 'bbcode_link_name' ); ?></div>
      <div><?php echo fcntr( 'bbcode_quote' ); ?></div>
      <div><?php echo fcntr( 'bbcode_spoiler' ); ?></div>
      <div><?php echo fcntr( 'bbcode_ins' ); ?></div>
      <div><?php echo fcntr( 'bbcode_del' ); ?></div>
    </div>
  </div>
</div>
