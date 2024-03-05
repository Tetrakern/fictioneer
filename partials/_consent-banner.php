<?php
/**
 * Partial: Consent Banner
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

<div id="consent-banner" class="consent-banner hidden" role="banner" hidden data-nosnippet>
  <div class="consent-banner__wrapper">
    <?php
      $info = fictioneer_replace_key_value(
        get_option( 'fictioneer_phrase_cookie_consent_banner' ),
        array(
          '[[privacy_policy_url]]' => get_privacy_policy_url()
        ),
        __( 'We use cookies to enhance your browsing experience, serve personalized content, and analyze our traffic. Some features are not available without, but you can limit the site to strictly necessary cookies only. See <a href="[[privacy_policy_url]]" target="_blank" tabindex="1">Privacy Policy</a>.', 'fictioneer' )
      );
    ?>
    <p class="consent-banner__notice"><?php echo $info; ?></p>
    <div class="consent-banner__actions">
      <button type="button" id="consent-accept-button" class="button button--cookie-banner" tabindex="2"><?php _e( 'Accept', 'fictioneer' ); ?></button>
      <button type="button" id="consent-reject-button" class="button button--cookie-banner" tabindex="3"><?php _e( 'Reject Non-Essential', 'fictioneer' ); ?></button>
    </div>
  </div>
</div>
