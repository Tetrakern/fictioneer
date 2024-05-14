<?php
/**
 * Partial: Account - OAuth
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.0.0
 *
 * @internal $args['user']          Current user.
 * @internal $args['is_admin']      True if the user is an administrator.
 * @internal $args['is_author']     True if the user is an author (by capabilities).
 * @internal $args['is_editor']     True if the user is an editor.
 * @internal $args['is_moderator']  True if the user is a moderator (by capabilities).
 */
?>

<?php

// No direct access!
defined( 'ABSPATH' ) OR exit;

// Abort if OAuth is disabled
if ( ! get_option( 'fictioneer_enable_oauth' ) ) {
  return;
}

// Setup
$current_user = $args['user'];
$patreon_user_data = fictioneer_get_user_patreon_data( $current_user->ID );
$oauth_providers = [
  ['discord', 'Discord'],
  ['twitch', 'Twitch'],
  ['google', 'Google'],
  ['patreon', 'Patreon']
];
$unset_notification = __( '%s connection successfully unset.', 'fictioneer' );

// Prompts
$confirmation = _x( 'DELETE', 'Prompt deletion confirmation string.', 'fictioneer' );
$unset_oauth_prompt = sprintf(
  __( 'Are you sure? Note that if you disconnect all accounts, you may no longer be able to log back in once you log out. Enter %s to confirm.', 'fictioneer' ),
  $confirmation
);

?>

<h3 class="profile__oauth-headline" id="oauth-connections"><?php _e( 'Account Bindings', 'fictioneer' ); ?></h3>

<p class="profile__description"><?php _e( 'Your profile can be linked to one or more external accounts, such as Discord or Google. You may add or remove these accounts at your own volition, but be aware that removing all accounts can lock you out with no means of access.', 'fictioneer' ); ?></p>

<?php if ( $patreon_user_data['tiers'] ?? 0 ) : ?>

  <?php if ( ! ( $patreon_user_data['valid'] ?? 0 ) ) : ?>
    <ul class="profile__admin-notes">
      <li>
        <i class="fa-solid fa-hourglass-end"></i>
        <span><?php _e( '<b>Patreon:</b> Data expired. Log in with Patreon again to refresh.', 'fictioneer' ); ?></span>
      </li>
    </ul>
  <?php endif; ?>

  <?php if ( $patreon_user_data['valid'] ?? 0 ) : ?>
    <ul class="profile__admin-notes">
      <?php foreach ( $patreon_user_data['tiers'] as $tier ) : ?>
        <li>
          <i class="fa-solid fa-ribbon"></i>
          <span>
            <?php
              printf(
                _x( '<b>Patreon:</b> %s', 'Frontend Patreon tier list item.', 'fictioneer' ),
                $tier['tier']
              );
            ?>
          </span>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

<?php endif; ?>

<div class="profile__oauth profile__segment">
  <?php
    foreach ( $oauth_providers as $provider ) {
      $client_id = get_option( "fictioneer_{$provider[0]}_client_id" ) ?: null;
      $client_secret = get_option( "fictioneer_{$provider[0]}_client_secret" ) ?: null;
      $id_hash = get_the_author_meta( "fictioneer_{$provider[0]}_id_hash", $current_user->ID ) ?: null;

      if ( $client_id && $client_secret ) {
        if ( empty( $id_hash )) {
          echo fictioneer_get_oauth_login_link(
            $provider[0],
            "<i class='fa-brands fa-{$provider[0]}'></i> <span>{$provider[1]}</span>",
            false,
            true,
            '_disconnected'
          );
        } else {
          ?>
            <div
              id="oauth-<?php echo $provider[0]; ?>"
              data-unset="<?php printf( $unset_notification, $provider[1] ); ?>"
              class="oauth-login-link _<?php echo $provider[0]; ?> _connected"
            >
              <i class="fa-brands fa-<?php echo $provider[0]; ?>"></i>
              <span><?php echo $provider[1]; ?></span>
              <button
                class="profile__oauth-item-unset button-unset-oauth"
                data-nonce="<?php echo wp_create_nonce( 'fictioneer_unset_oauth' ); ?>"
                data-id="<?php echo $current_user->ID; ?>"
                data-channel="<?php echo $provider[0]; ?>"
                data-confirm="<?php echo $confirmation; ?>"
                data-warning="<?php echo esc_attr( $unset_oauth_prompt ); ?>"
              ><?php fictioneer_icon( 'fa-xmark' ); ?></button>
            </div>
          <?php
        }
      }
    }
  ?>
</div>
