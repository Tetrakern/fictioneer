<?php

// =============================================================================
// LOAD ACF PLUGIN FROM THEME IF NOT INSTALLED (ADMIN ONLY)
// =============================================================================

if ( ! class_exists('acf') && ( is_admin() || FICTIONEER_ENABLE_FRONTEND_ACF ) ) {
  // Define path and URL to the ACF plugin.
  define( 'FICTIONEER_ACF_PATH', get_template_directory() . '/includes/acf/' );
  define( 'FICTIONEER_ACF_URL', get_template_directory_uri() . '/includes/acf/' );

  // Include the ACF plugin.
  include_once( FICTIONEER_ACF_PATH . 'acf.php' );

  // Customize the url setting to fix incorrect asset URLs.
  function fictioneer_acf_settings_url( $url ) {
    return FICTIONEER_ACF_URL;
  }
  add_filter( 'acf/settings/url', 'fictioneer_acf_settings_url' );

  // Automatic installs should not see the admin menu
  function fictioneer_acf_settings_show_admin( $show_admin ) {
    return false;
  }
  add_filter( 'acf/settings/show_admin', 'fictioneer_acf_settings_show_admin' );
}

?>
