<?php
/**
 * Partial: Age Confirmation Modal
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.9.0
 *
 * @internal $args['post_id']    The current post ID.
 * @internal $args['post_type']  The current post type.
 */


// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$site_modal = get_option( 'fictioneer_enable_site_age_confirmation' );

$redirect_url = ! $site_modal ?
  get_option( 'fictioneer_age_confirmation_redirect_site' ) : get_option( 'fictioneer_age_confirmation_redirect_post' );

if ( empty( $redirect_url ) ) {
  $redirect_url = FICTIONEER_AGE_CONFIRMATION_REDIRECT;
}

?>

<div id="age-confirmation-modal" class="modal age-confirmation" data-nosnippet hidden>

  <div class="modal__wrapper">

    <div class="modal__header"><?php _e( 'Age Confirmation', 'fictioneer' ); ?></div>

    <?php if ( $site_modal ) : ?>
      <div class="modal__row modal__description _large"><?php
        echo get_option( 'fictioneer_phrase_site_age_confirmation' ) ?: __( 'This website is intended for an adult audience. Please confirm that you are of legal age (18+) or leave the website.', 'fictioneer' );
      ?></div>
    <?php else : ?>
      <div class="modal__row modal__description _large _small-top"><?php
        echo get_option( 'fictioneer_phrase_post_age_confirmation' ) ?: __( 'This content is intended for an adult audience. Please confirm that you are of legal age (18+) or leave the website.', 'fictioneer' );
      ?></div>
    <?php endif; ?>

    <div class="modal__actions _age-confirmation">
      <button type="reset" id="age-confirmation-leave" data-redirect="<?php echo esc_url( $redirect_url ); ?>" class="button"><?php _ex( 'Leave', 'Age confirmation modal button.', 'fictioneer' ); ?></button>
      <button type="submit" id="age-confirmation-confirm" class="button"><?php _ex( 'Confirm', 'Age confirmation modal button.', 'fictioneer' ); ?></button>
    </div>

  </div>

</div>
