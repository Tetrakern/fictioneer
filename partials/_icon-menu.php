<?php
/**
 * Partial: Icon Menu
 *
 * Includes the icon menu on the right-side of the navigation bar
 * and on top of the mobile menu.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0
 *
 * @internal $args['location'] Either 'in-navigation' or 'in-mobile-menu'.
 */
?>

<?php

// Setup
$bookmarks_link = get_permalink( get_option( 'fictioneer_bookmarks_page' ) );
$discord_invite_link = get_option( 'fictioneer_discord_invite_link' );
$profile_link = get_edit_profile_url();
$profile_page = intval( get_option( 'fictioneer_user_profile_page', -1 ) );

if ( $profile_page && $profile_page > 0 ) {
  $profile_link = get_permalink( $profile_page );
}

?>

<div class="icon-menu">
  <?php if ( get_option( 'fictioneer_enable_oauth' ) ) : ?>
    <label
      for="modal-login-toggle"
      title="<?php esc_attr_e( 'Login', 'fictioneer' ) ?>"
      class="subscriber-login icon-menu__item hide-if-logged-in"
      tabindex="0"
      aria-label="<?php esc_attr_e( 'Open login modal', 'fictioneer' ) ?>"
    ><?php fictioneer_icon( 'fa-login' ); ?></label>
  <?php endif; ?>

  <?php if ( fictioneer_show_auth_content() ) : ?>
    <div class="icon-menu__menu hide-if-logged-out">
      <a
        href="<?php echo esc_url( $profile_link ); ?>"
        title="<?php esc_attr_e( 'User Profile', 'fictioneer' ) ?>"
        class="subscriber-profile icon-menu__item _with-submenu"
        rel="noopener noreferrer nofollow"
        aria-label="<?php esc_attr_e( 'Link to user profile', 'fictioneer' ) ?>"
      ><?php fictioneer_icon( 'user' ); ?></a>
      <ul class="sub-menu"><?php echo fictioneer_user_menu_items(); ?></ul>
    </div>
  <?php endif; ?>

  <?php if ( $bookmarks_link ) : ?>
    <a
      href="<?php echo esc_url( $bookmarks_link ); ?>"
      title="<?php esc_attr_e( 'Bookmarks Page', 'fictioneer' ) ?>"
      aria-label="<?php esc_attr_e( 'Link to bookmarks page', 'fictioneer' ) ?>"
      class="icon-menu__item hide-if-logged-in hidden icon-menu-bookmarks"
      rel="noopener noreferrer nofollow"
    ><i class="fa-solid fa-bookmark"></i></a>
  <?php endif; ?>

  <?php if ( ! empty( $discord_invite_link ) ) : ?>
    <a
      href="<?php echo esc_url( $discord_invite_link ); ?>"
      title="<?php esc_attr_e( 'Join Discord', 'fictioneer' ) ?>"
      aria-label="<?php esc_attr_e( 'Discord invite link', 'fictioneer' ) ?>"
      class="icon-menu__item icon-menu__item--discord hide-if-logged-in"
      rel="noopener noreferrer nofollow"
    ><i class="fa-brands fa-discord"></i></a>
  <?php endif; ?>

  <?php if ( $args['location'] == 'in-navigation' && get_option( 'fictioneer_enable_follows' ) && fictioneer_show_auth_content() ) : ?>
    <div class="icon-menu__menu hide-if-logged-out">

      <button
        id="follow-menu-button"
        class="icon-menu__item _with-submenu follow-menu-item follows-alert-number mark-follows-read"
        aria-label="<?php esc_attr_e( 'Mark follows as read', 'fictioneer' ) ?>"
      ><?php fictioneer_icon( 'fa-bell' ); ?>
        <i class="fa-solid fa-spinner fa-spin" style="--fa-animation-duration: .8s;"></i>
        <span><?php _ex( 'Read', 'Mark as read button.', 'fictioneer' ) ?></span>
      </button>

      <div class="follow-notifications sub-menu">
        <div id="follow-menu-scroll" class="follow-notifications__scroll">
          <div class="follow-item">
            <div class="follow-wrapper">
              <div class="follow-placeholder truncate _1-1"><?php _e( 'Looking for updates...', 'fictioneer' ) ?></div>
            </div>
          </div>
        </div>
      </div>

    </div>
  <?php endif; ?>

  <?php if ( FICTIONEER_SHOW_SEARCH_IN_MENUS ) : ?>
    <a
      href="<?php echo esc_url( home_url( '/?s=' ) ); ?>"
      title="<?php esc_attr_e( 'Search Page', 'fictioneer' ) ?>"
      class="icon-menu__item hide-in-mobile-menu"
      rel="noopener noreferrer nofollow"
      aria-label="<?php esc_attr_e( 'Link to search page', 'fictioneer' ) ?>"
    ><i class="fa-solid fa-magnifying-glass"></i></a>
  <?php endif; ?>

  <button
    class="icon-menu__item toggle-light-mode"
    title="<?php esc_attr_e( 'Toggle Dark/Light Mode', 'fictioneer' ) ?>"
    role="checkbox"
    aria-checked="false"
    aria-label="<?php esc_attr_e( 'Toggle between dark mode and light mode', 'fictioneer' ) ?>"
  ><?php fictioneer_icon( 'fa-sun', 'only-darkmode' ); fictioneer_icon( 'fa-moon', 'only-lightmode' ); ?></button>

  <label
    for="modal-site-settings-toggle"
    title="<?php esc_attr_e( 'Site Settings', 'fictioneer' ) ?>"
    class="site-settings icon-menu__item"
    tabindex="0"
    aria-label="<?php esc_attr_e( 'Open site settings modal', 'fictioneer' ) ?>"
  ><?php fictioneer_icon( 'fa-tools' ); ?></label>

  <?php if ( get_option( 'fictioneer_enable_theme_rss' ) ) : ?>
    <a
      href="<?php echo esc_url( home_url( 'feed' ) ); ?>"
      title="<?php esc_attr_e( 'Site RSS', 'fictioneer' ) ?>"
      class="rss-main-link icon-menu__item"
      aria-label="<?php esc_attr_e( 'Link to site RSS feed', 'fictioneer' ) ?>"
    ><?php fictioneer_icon( 'fa-rss' ); ?></a>
  <?php endif; ?>

  <?php if ( fictioneer_show_auth_content() ) : ?>
    <a
      href="<?php echo fictioneer_get_logout_url(); ?>"
      title="<?php esc_attr_e( 'Logout', 'fictioneer' ) ?>"
      onclick="fcn_cleanupLocalStorage()"
      class="icon-menu__item button--logout hide-if-logged-out"
      rel="noopener noreferrer nofollow"
      aria-label="<?php esc_attr_e( 'Click to log out', 'fictioneer' ) ?>"
    ><?php fictioneer_icon( 'fa-logout' ); ?></a>
  <?php endif; ?>
</div>
