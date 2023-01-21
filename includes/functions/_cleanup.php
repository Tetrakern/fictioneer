<?php

// =============================================================================
// THEME DEACTIVATION
// =============================================================================

/**
 * Clean up when the theme is deactivated
 *
 * Always deletes all theme-related transients in the database. If the option to
 * delete all settings and theme mods is activated, these will be removed as
 * well but otherwise preserved.
 *
 * @since Fictioneer 4.7
 */

function fictioneer_theme_deactivation() {
  // Delete Transients without expiration value
  delete_transient( 'fictioneer_chapter_updated_timestamp' );
  delete_transient( 'fictioneer_customized_light_mode' );
  delete_transient( 'fictioneer_customized_dark_mode' );
  delete_transient( 'fictioneer_customized_layout' );
  delete_transient( 'fictioneer_sitemap_pages' );
  delete_transient( 'fictioneer_sitemap_posts' );
  delete_transient( 'fictioneer_sitemap_collections' );
  delete_transient( 'fictioneer_sitemap_stories' );
  delete_transient( 'fictioneer_sitemap_chapters' );
  delete_transient( 'fictioneer_sitemap_recommendations' );
  delete_transient( 'fictioneer_stories_total_word_count' );

  // Only continue if the user wants to delete all options/mods
  if ( get_option( 'fictioneer_delete_theme_options_on_deactivation', false ) ) {
    // Remove theme mods
    remove_theme_mods();

    // Delete boolean options
    foreach ( FICTIONEER_OPTIONS['booleans'] as $setting ) {
      delete_option( $setting['name'] );
    }

    // Delete integer options
    foreach ( FICTIONEER_OPTIONS['integers'] as $setting ) {
      delete_option( $setting['name'] );
    }

    // Delete string options
    foreach ( FICTIONEER_OPTIONS['strings'] as $setting ) {
      delete_option( $setting['name'] );
    }

    // Delete other options
    delete_option( 'fictioneer_story_or_chapter_updated_timestamp' );
    delete_option( 'fictioneer_update_check_timestamp' );
    delete_option( 'fictioneer_update_notice_timestamp' );
    delete_option( 'fictioneer_latest_version' );

    // Delete user meta
    $users = get_users( array( 'fields' => 'ID' ) );

    foreach ( $users as $user ) {
      delete_user_meta( $user->ID, 'fictioneer_author_statistics' );
      delete_user_meta( $user->ID, 'fictioneer_bookmarks' );
      delete_user_meta( $user->ID, 'fictioneer_badge_override' );
      delete_user_meta( $user->ID, 'fictioneer_external_avatar_url' );
      delete_user_meta( $user->ID, 'fictioneer_discord_id_hash' );
      delete_user_meta( $user->ID, 'fictioneer_google_id_hash' );
      delete_user_meta( $user->ID, 'fictioneer_twitch_id_hash' );
      delete_user_meta( $user->ID, 'fictioneer_patreon_id_hash' );
      delete_user_meta( $user->ID, 'fictioneer_comment_reply_notifications' );
      delete_user_meta( $user->ID, 'fictioneer_comment_reply_validator' );
      delete_user_meta( $user->ID, 'fictioneer_admin_disable_avatar' );
      delete_user_meta( $user->ID, 'fictioneer_admin_disable_reporting' );
      delete_user_meta( $user->ID, 'fictioneer_admin_disable_renaming' );
      delete_user_meta( $user->ID, 'fictioneer_admin_disable_commenting' );
      delete_user_meta( $user->ID, 'fictioneer_admin_disable_editing' );
      delete_user_meta( $user->ID, 'fictioneer_admin_disable_comment_notifications' );
      delete_user_meta( $user->ID, 'fictioneer_admin_always_moderate_comments' );
      delete_user_meta( $user->ID, 'fictioneer_admin_moderation_message' );
      delete_user_meta( $user->ID, 'fictioneer_author_page' );
      delete_user_meta( $user->ID, 'fictioneer_user_patreon_link' );
      delete_user_meta( $user->ID, 'fictioneer_user_kofi_link' );
      delete_user_meta( $user->ID, 'fictioneer_user_subscribestar_link' );
      delete_user_meta( $user->ID, 'fictioneer_user_paypal_link' );
      delete_user_meta( $user->ID, 'fictioneer_user_donation_link' );
      delete_user_meta( $user->ID, 'fictioneer_support_message' );
      delete_user_meta( $user->ID, 'fictioneer_user_timezone_string' );
      delete_user_meta( $user->ID, 'fictioneer_hide_badge' );
      delete_user_meta( $user->ID, 'fictioneer_enforce_gravatar' );
      delete_user_meta( $user->ID, 'fictioneer_disable_avatar' );
      delete_user_meta( $user->ID, 'fictioneer_disable_badge_override' );
      delete_user_meta( $user->ID, 'fictioneer_patreon_tiers' );
      delete_user_meta( $user->ID, 'fictioneer_user_fingerprint' );
      delete_user_meta( $user->ID, 'fictioneer_user_reminders' );
      delete_user_meta( $user->ID, 'fictioneer_user_follows' );
      delete_user_meta( $user->ID, 'fictioneer_user_checkmarks' );
    }

    // Reset user roles
    remove_role( 'fcn_moderator' );
    fictioneer_reset_author_role();
    fictioneer_reset_editor_role();
    fictioneer_reset_contributor_role();
  }
}
add_action( 'switch_theme', 'fictioneer_theme_deactivation' );

?>
