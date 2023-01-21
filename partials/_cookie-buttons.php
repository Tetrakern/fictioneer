<?php
/**
 * Partial: Cookie Buttons
 *
 * Renders buttons to reset your consent and clear all cookies.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 3.0
 */
?>

<section class="cookies">
  <div class="cookies__actions">
    <button type="button" onclick="fcn_deleteCookie('fcn_cookie_consent'); location.reload();" class="button"><?php _e( 'Reset Consent', 'fictioneer' ); ?></button>
    <button type="button" onclick="fcn_deleteAllCookies(); alert('<?php _e( 'Cookies and local storage have been cleared. To keep it that way, you should leave the site.', 'fictioneer' ); ?>');" class="button"><?php _e( 'Clear Cookies', 'fictioneer' ); ?></button>
  </div>
</section>
