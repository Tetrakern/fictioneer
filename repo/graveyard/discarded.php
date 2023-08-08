<?php

/**
 * This is a discard pile for functions and snippets that are no longer in use
 * but may prove useful in the future (or were removed by mistake). Keeping them
 * in a searchable file is easier that sorting through my poorly written commits.
*/

// =============================================================================
// RENDER TIME
// =============================================================================

function fictioneer_measure_render_time() {
  global $render_start_time;
  $render_start_time = microtime( true );
}
// add_action( 'template_redirect', 'fictioneer_measure_render_time' );

function fictioneer_display_render_time() {
  global $render_start_time;
  echo '<!-- Render Time: ' . microtime( true ) - $render_start_time . ' seconds -->';
}
// add_action( 'shutdown', 'fictioneer_display_render_time' );

// =============================================================================
// SHOW PATREON TIERS
// =============================================================================

/**
 * Adds HTML for the Patreon tiers section to the wp-admin user profile
 *
 * DISABLED!
 *
 * @since Fictioneer 5.0
 *
 * @param WP_User $profile_user The profile user object. Not necessarily the one
 *                              currently editing the profile!
 */

function fictioneer_admin_profile_patreon_tiers( $profile_user ) {
  // Setup
  $editing_user_is_admin = fictioneer_is_admin( get_current_user_id() );

  // Abort conditions...
  if ( ! $editing_user_is_admin ) return;

  // Start HTML ---> ?>
  <tr class="user-patreon-tiers-wrap">
    <th><label for="fictioneer_patreon_tiers"><?php _e( 'Patreon Tiers', 'fictioneer' ) ?></label></th>
    <td>
      <input name="fictioneer_patreon_tiers" type="text" id="fictioneer_patreon_tiers" value="<?php echo esc_attr( json_encode( get_the_author_meta( 'fictioneer_patreon_tiers', $profile_user->ID ) ) ); ?>" class="regular-text" readonly>
      <p class="description"><?php _e( 'Patreon tiers for the linked Patreon client. Valid for two weeks after last login.', 'fictioneer' ) ?></p>
    </td>
  </tr>
  <?php // <--- End HTML
}
// add_action( 'fictioneer_admin_user_sections', 'fictioneer_admin_profile_patreon_tiers', 60 );

// =============================================================================
// SHOW BOOKMARKS SECTION
// =============================================================================

/**
 * Adds HTML for the bookmarks section to the wp-admin user profile
 *
 * DISABLED!
 *
 * @since Fictioneer 5.0
 *
 * @param WP_User $profile_user The profile user object. Not necessarily the one
 *                              currently editing the profile!
 */

function fictioneer_admin_profile_bookmarks( $profile_user ) {
  // Setup
  $editing_user_is_admin = fictioneer_is_admin( get_current_user_id() );

  // Abort conditions...
  if ( ! $editing_user_is_admin ) return;

  // Start HTML ---> ?>
  <tr class="user-bookmarks-wrap">
    <th><label for="user_bookmarks"><?php _e( 'Bookmarks JSON', 'fictioneer' ) ?></label></th>
    <td>
      <textarea name="user_bookmarks" id="user_bookmarks" rows="12" cols="30" style="font-family: monospace; font-size: 12px; word-break: break-all;" disabled><?php echo esc_attr( get_the_author_meta( 'fictioneer_bookmarks', $profile_user->ID ) ); ?></textarea>
    </td>
  </tr>
  <?php // <--- End HTML
}
// add_action( 'fictioneer_admin_user_sections', 'fictioneer_admin_profile_bookmarks', 70 );

?>
