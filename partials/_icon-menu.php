<?php
/**
 * Partial: Icon Menu
 *
 * Includes the icon menu on the right-side of the navigation bar
 * and on top of the mobile menu.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0.0
 *
 * @internal $args['location']  Either 'in-navigation' or 'in-mobile-menu'.
 */


// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$bookmarks_link = fictioneer_get_assigned_page_link( 'fictioneer_bookmarks_page' );
$discord_invite_link = get_option( 'fictioneer_discord_invite_link' );
$profile_link = get_edit_profile_url();
$profile_page_id = intval( get_option( 'fictioneer_user_profile_page', -1 ) ?: -1 );

if ( ! empty( $profile_page_id ) && $profile_page_id > 0 ) {
  $profile_link = fictioneer_get_assigned_page_link( 'fictioneer_user_profile_page' );
}

?>

<div class="icon-menu" data-nosnippet>
  <?php if ( fictioneer_show_login() ) : ?>
    <div class="menu-item menu-item-icon subscriber-login hide-if-logged-in">
      <label
        for="modal-login-toggle"
        title="<?php esc_attr_e( 'Login', 'fictioneer' ); ?>"
        tabindex="0"
        aria-label="<?php esc_attr_e( 'Open login modal', 'fictioneer' ); ?>"
      ><?php fictioneer_icon( 'fa-login' ); ?></label>
    </div>
  <?php endif; ?>

  <?php if ( fictioneer_show_auth_content() ) : ?>
    <div class="menu-item menu-item-icon menu-item-has-children hide-if-logged-out">
      <a
        href="<?php echo esc_url( $profile_link ); ?>"
        title="<?php esc_attr_e( 'User Profile', 'fictioneer' ); ?>"
        class="subscriber-profile"
        rel="noopener noreferrer nofollow"
        aria-label="<?php esc_attr_e( 'Link to user profile', 'fictioneer' ); ?>"
      ><i class="fa-solid fa-circle-user user-icon"></i></a>
      <ul class="sub-menu"><?php echo fictioneer_user_menu_items(); ?></ul>
    </div>
  <?php endif; ?>

  <?php if ( ! empty( $bookmarks_link ) ) : ?>
    <div class="menu-item menu-item-icon icon-menu-bookmarks hidden hide-if-logged-in">
      <a
        href="<?php echo esc_url( $bookmarks_link ); ?>"
        title="<?php esc_attr_e( 'Bookmarks Page', 'fictioneer' ); ?>"
        aria-label="<?php esc_attr_e( 'Link to bookmarks page', 'fictioneer' ); ?>"
        rel="noopener noreferrer nofollow"
      ><i class="fa-solid fa-bookmark"></i></a>
    </div>
  <?php endif; ?>

  <?php if ( ! empty( $discord_invite_link ) ) : ?>
    <div class="menu-item menu-item-icon icon-menu-discord hide-if-logged-in">
      <a
        href="<?php echo esc_url( $discord_invite_link ); ?>"
        title="<?php esc_attr_e( 'Join Discord', 'fictioneer' ); ?>"
        aria-label="<?php esc_attr_e( 'Discord invite link', 'fictioneer' ); ?>"
        rel="noopener noreferrer nofollow"
      ><i class="fa-brands fa-discord"></i></a>
    </div>
  <?php endif; ?>

  <?php if ( $args['location'] == 'in-navigation' && get_option( 'fictioneer_enable_follows' ) && fictioneer_show_auth_content() ) : ?>
    <div class="menu-item menu-item-icon menu-item-has-children hide-if-logged-out">

      <button
        id="follow-menu-button"
        class="icon-menu__item _with-submenu follow-menu-item follows-alert-number mark-follows-read"
        aria-label="<?php esc_attr_e( 'Mark follows as read', 'fictioneer' ); ?>"
      ><?php fictioneer_icon( 'fa-bell' ); ?>
        <i class="fa-solid fa-spinner fa-spin" style="--fa-animation-duration: .8s;"></i>
        <span class="follow-menu-item__read"><?php _ex( 'Read', 'Mark as read button.', 'fictioneer' ); ?></span>
      </button>

      <div class="follow-notifications sub-menu">
        <div id="follow-menu-scroll" class="follow-notifications__scroll">
          <div class="follow-item">
            <div class="follow-wrapper">
              <div class="follow-placeholder truncate _1-1"><?php _e( 'Looking for updates...', 'fictioneer' ); ?></div>
            </div>
          </div>
        </div>
      </div>

    </div>
  <?php endif; ?>

  <?php if ( FICTIONEER_SHOW_SEARCH_IN_MENUS ) : ?>
    <div class="menu-item menu-item-icon hide-in-mobile-menu">
      <a
        href="<?php echo esc_url( home_url( '/?s=' ) ); ?>"
        title="<?php esc_attr_e( 'Search Page', 'fictioneer' ); ?>"
        rel="nofollow"
        aria-label="<?php esc_attr_e( 'Link to search page', 'fictioneer' ); ?>"
      ><i class="fa-solid fa-magnifying-glass"></i></a>
    </div>
  <?php endif; ?>

  <div class="menu-item menu-item-icon toggle-light-mode">
    <button
      title="<?php esc_attr_e( 'Toggle Dark/Light Mode', 'fictioneer' ); ?>"
      role="checkbox"
      aria-checked="false"
      aria-label="<?php esc_attr_e( 'Toggle between dark mode and light mode', 'fictioneer' ); ?>"
    ><?php fictioneer_icon( 'fa-sun', 'only-darkmode' ); fictioneer_icon( 'fa-moon', 'only-lightmode' ); ?></button>
  </div>

  <div class="menu-item menu-item-icon site-setting">
    <label
      for="modal-site-settings-toggle"
      title="<?php esc_attr_e( 'Site Settings', 'fictioneer' ); ?>"
      tabindex="0"
      aria-label="<?php esc_attr_e( 'Open site settings modal', 'fictioneer' ); ?>"
    ><?php fictioneer_icon( 'fa-tools' ); ?></label>
  </div>

  <?php if ( get_option( 'fictioneer_enable_theme_rss' ) ) : ?>
    <div class="menu-item menu-item-icon rss-main-link">
      <a
        href="<?php echo esc_url( home_url( 'feed' ) ); ?>"
        title="<?php esc_attr_e( 'Site RSS', 'fictioneer' ); ?>"
        aria-label="<?php esc_attr_e( 'Link to site RSS feed', 'fictioneer' ); ?>"
      ><?php fictioneer_icon( 'fa-rss' ); ?></a>
    </div>
  <?php endif; ?>

  <?php if ( fictioneer_show_auth_content() ) : ?>
    <div class="menu-item menu-item-icon hide-if-logged-out hide-outside-mobile-menu">
      <a
        href="<?php echo fictioneer_get_logout_url(); ?>"
        title="<?php esc_attr_e( 'Logout', 'fictioneer' ); ?>"
        data-click="logout"
        rel="noopener noreferrer nofollow"
        aria-label="<?php esc_attr_e( 'Click to log out', 'fictioneer' ); ?>"
      ><?php fictioneer_icon( 'fa-logout' ); ?></a>
    </div>
  <?php endif; ?>
</div>
