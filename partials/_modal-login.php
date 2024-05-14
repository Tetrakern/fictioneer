<?php
/**
 * Partial: Login Modal
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0.0
 */
?>

<?php

// No direct access!
defined( 'ABSPATH' ) OR exit;

?>

<div id="login-modal" class="login modal" data-nosnippet hidden>
  <label for="modal-login-toggle" class="background-close"></label>
  <div class="modal__wrapper">
    <label class="close" for="modal-login-toggle" tabindex="0" aria-label="<?php esc_attr_e( 'Close modal', 'fictioneer' ); ?>">
      <?php fictioneer_icon( 'fa-xmark' ); ?>
    </label>
    <div class="modal__header drag-anchor"><?php echo fcntr( 'login_modal' ); ?></div>

    <?php
      $info = fictioneer_replace_key_value(
        wp_kses_post( get_option( 'fictioneer_phrase_login_modal' ) ),
        array(
          '[[privacy_policy_url]]' => get_privacy_policy_url()
        ),
        __( '<p>Log in with a social media account to set up a profile. You can change your nickname later.</p>', 'fictioneer' )
      );
    ?>

    <div class="modal__row modal__description"><?php echo $info; ?></div>

    <div class="modal__row login__options"><?php do_action( 'fictioneer_modal_login_option' ); ?></div>
  </div>
</div>
