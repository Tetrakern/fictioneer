<?php
/**
 * Partial: Account - OAuth
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.0
 *
 * @internal $args['user']         Current user.
 * @internal $args['is_admin']     True if the user is an administrator.
 * @internal $args['is_author']    True if the user is an author (by capabilities).
 * @internal $args['is_editor']    True if the user is an editor.
 * @internal $args['is_moderator'] True if the user is a moderator (by capabilities).
 */
?>

<?php

// Abort if OAuth is disabled
if ( ! get_option( 'fictioneer_enable_oauth' ) ) return;

// Setup
$current_user = $args['user'];
$patreon_tiers = get_user_meta( $current_user->ID, 'fictioneer_patreon_tiers', true );
$oauth_providers = [
  ['discord', 'Discord'],
  ['twitch', 'Twitch'],
  ['google', 'Google'],
  ['patreon', 'Patreon']
];
$unset_notification = __( '%s connection successfully unset.', 'fictioneer' )

?>

<h3 class="profile__oauth-headline" id="oauth-connections"><?php _e( 'Account Bindings', 'fictioneer' ) ?></h3>

<p class="profile__description"><?php _e( 'Your profile can be linked to one or more external accounts, such as Discord or Google. You may add or remove these accounts at your own volition, but be aware that removing all accounts will lock you out with no means of access.', 'fictioneer' ) ?></p>

<ul class="profile__admin-notes">
  <?php if ( ! empty( $patreon_tiers ) ) : ?>
    <?php foreach ( $patreon_tiers as $tier ) : ?>
      <li>
        <i class="fa-solid fa-ribbon"></i>
        <span>
          <?php
            printf(
              __( '<b>Patreon:</b> %1$s', 'fictioneer' ),
              $tier['tier'],
              $tier['timestamp']
            )
          ?>
        </span>
      </li>
    <?php endforeach; ?>
  <?php endif; ?>
</ul>

<div class="profile__oauth profile__segment">
  <?php
    foreach ( $oauth_providers as $provider ) {
      if (
        get_option( "fictioneer_{$provider[0]}_client_id" ) &&
        get_option( "fictioneer_{$provider[0]}_client_secret" )
      ) {
        if ( ! get_the_author_meta( "fictioneer_{$provider[0]}_id_hash", $current_user->ID ) ) {
          echo fictioneer_get_oauth_login_link(
            $provider[0],
            '<i class="fa-brands fa-' . $provider[0] . '"></i><span>' . $provider[1] . '</span>',
            false,
            true,
            '_disconnected'
          );
        } else {
          ?>
            <div id="oauth-<?php echo $provider[0]; ?>" data-unset="<?php printf( $unset_notification, $provider[1] ); ?>" class="oauth-login-link _<?php echo $provider[0]; ?> _connected">
              <i class="fa-brands fa-<?php echo $provider[0]; ?>"></i>
              <span><?php echo $provider[1]; ?></span>
              <button
                class="profile__oauth-item-unset button-unset-oauth"
                data-nonce="<?php echo wp_create_nonce( 'fictioneer_unset_oauth' ); ?>"
                data-id="<?php echo $current_user->ID; ?>"
                data-channel="<?php echo $provider[0]; ?>"
              ><?php fictioneer_icon( 'fa-xmark' ); ?></button>
            </div>
          <?php
        }
      }
    }
  ?>
</div>
