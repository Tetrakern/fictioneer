<?php
/**
 * Partial: Account - Profile
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

// Setup
$current_user = $args['user'];
$fingerprint = fictioneer_get_user_fingerprint( $current_user->ID );
$new_email = get_user_meta( $current_user->ID, '_new_email', true );

// Flags
$renaming_disabled = $current_user->fictioneer_admin_disable_renaming;

// Action: Cancel email change
if (
  is_user_logged_in() &&
  ! empty( $_GET['dismiss'] ) &&
  $current_user->ID . '_new_email' === $_GET['dismiss']
) {
  delete_user_meta( $current_user->ID, '_new_email' );
  wp_safe_redirect( get_permalink() );
  exit();
}

// Action: Update profile
if (
  is_user_logged_in() &&
  $_SERVER['REQUEST_METHOD'] == 'POST' &&
  ! empty( $_POST['action'] ) &&
  $_POST['action'] == 'update_profile' &&
  isset( $_POST['fictioneer_frontend_user_profile_nonce'] ) &&
  wp_verify_nonce(
    $_POST['fictioneer_frontend_user_profile_nonce'],
    'fictioneer_update_frontend_user_profile'
  )
) {
  fictioneer_update_frontend_user_profile();
  wp_safe_redirect( get_permalink() . '#profile' );
  exit();
}

?>

<h3 id="profile" class="profile__account-headline"><?php _e( 'Profile', 'fictioneer' ) ?></h3>

<form method="post" action="<?php the_permalink(); ?>" class="profile__account profile__segment">

  <div class="profile__identity">

    <div class="profile__input-group">
      <div class="profile__input-label"><?php _e( 'Username', 'fictioneer' ) ?></div>
      <div class="profile__input-wrapper">
        <input type="text" value="<?php echo esc_attr( $current_user->user_login ); ?>" class="profile__input-field profile__nickname" disabled>
        <p class="profile__input-note"><?php _e( 'Your unique name in the system. Cannot be changed.', 'fictioneer' ) ?></p>
      </div>
    </div>

    <div class="profile__input-group">
      <div class="profile__input-label"><?php _e( 'Fingerprint', 'fictioneer' ) ?></div>
      <div class="profile__input-wrapper">
        <input type="text" value="<?php echo esc_attr( $fingerprint ); ?>" class="profile__input-field profile__fingerprint" disabled>
        <p class="profile__input-note"><?php _e( 'Your unique hash. Used to distinguish commenters.', 'fictioneer' ) ?></p>
      </div>
    </div>

    <div class="profile__input-group">
      <div class="profile__input-label"><?php _e( 'Nickname', 'fictioneer' ) ?></div>
      <div class="profile__input-wrapper">
        <input data-lpignore="true" name="nickname" type="text" value="<?php echo esc_attr( $current_user->nickname ); ?>" class="profile__input-field profile__nickname" <?php if ( $renaming_disabled ) echo 'disabled'; ?>>
        <p class="profile__input-note"><?php _e( 'Your nickname will be displayed instead of your username. Not unique!', 'fictioneer' ) ?></p>
        <?php if ( $renaming_disabled ) : ?>
          <ul class="profile__admin-notes">
            <li>
              <i class="fa-solid fa-bolt"></i>
              <span><?php _e( 'Renaming capability disabled.', 'fictioneer' ) ?></span>
            </li>
          </ul>
        <?php endif; ?>
      </div>
    </div>

    <div class="profile__input-group">
      <div class="profile__input-label"><?php _e( 'Email Address', 'fictioneer' ) ?></div>
      <div class="profile__input-wrapper">
        <input data-lpignore="true" name="email" type="text" value="<?php echo esc_attr( $current_user->user_email ); ?>" class="profile__input-field profile__email">
        <?php if ( $new_email && $new_email['newemail'] !== $current_user->user_email ) : ?>
          <p class="profile__input-note">
            <?php
              printf(
                __( 'Update to <b>%1$s</b> pending. Please check your previous email address to confirm. <a href="%2$s">Cancel.</a>', 'fictioneer' ),
                $new_email['newemail'],
                esc_url( get_the_permalink() . '?dismiss=' . $current_user->ID . '_new_email#profile' )
              )
            ?>
          </p>
        <?php else : ?>
          <p class="profile__input-note"><?php _e( 'You will get an email at your new address. <b>The new address will not become active until confirmed.</b>', 'fictioneer' ) ?></p>
        <?php endif; ?>
      </div>
    </div>

    <div class="profile__input-group" hidden>
      <div class="profile__input-label"><?php _e( 'Timezone', 'fictioneer' ) ?></div>
      <div class="profile__input-wrapper">
        <div class="select-wrapper profile__timezone">
          <select id="fictioneer_user_timezone_string" name="fictioneer_user_timezone_string" class="profile__input-field"><?php echo wp_timezone_choice( $timezone ); ?></select>
        </div>
        <p class="profile__input-note"><?php _e( 'Choose either a city or a UTC time offset.', 'fictioneer' ) ?></p>
      </div>
    </div>

  </div>

  <div class="profile__flags">

    <div class="profile__input-wrapper _checkbox">
      <input id="fictioneer_enforce_gravatar" name="fictioneer_enforce_gravatar" type="checkbox" value="1" <?php echo checked( 1, get_the_author_meta( 'fictioneer_enforce_gravatar', $current_user->ID ), false ); ?>>
      <label for="fictioneer_enforce_gravatar"><?php _e( 'Always use gravatar', 'fictioneer' ) ?></label>
    </div>

    <div class="profile__input-wrapper _checkbox">
      <input id="fictioneer_disable_avatar" name="fictioneer_disable_avatar" type="checkbox" value="1" <?php echo checked( 1, get_the_author_meta( 'fictioneer_disable_avatar', $current_user->ID ), false ); ?>>
      <label for="fictioneer_disable_avatar"><?php _e( 'Disable avatar', 'fictioneer' ) ?></label>
    </div>

    <?php if ( get_option( 'fictioneer_enable_custom_badges' ) ) : ?>

      <div class="profile__input-wrapper _checkbox">
        <input id="fictioneer_hide_badge" name="fictioneer_hide_badge" type="checkbox" <?php echo checked( 1, get_the_author_meta( 'fictioneer_hide_badge', $current_user->ID ), false ); ?> value="1">
        <label for="fictioneer_hide_badge"><?php _e( 'Hide badge', 'fictioneer' ) ?></label>
      </div>

      <?php if ( ! empty( get_the_author_meta( 'fictioneer_badge_override', $current_user->ID ) ) ) : ?>
        <div class="profile__input-wrapper _checkbox">
          <input id="fictioneer_disable_badge_override" name="fictioneer_disable_badge_override" type="checkbox" <?php echo checked( 1, get_the_author_meta( 'fictioneer_disable_badge_override', $current_user->ID ), false ); ?> value="1">
          <label for="fictioneer_disable_badge_override"><?php _e( 'Override assigned badge', 'fictioneer' ) ?></label>
        </div>
      <?php endif; ?>

    <?php endif; ?>

    <?php if ( get_option( 'fictioneer_enable_comment_notifications' ) ) : ?>
      <div class="profile__input-wrapper _checkbox">
        <input id="fictioneer_comment_reply_notifications" name="fictioneer_comment_reply_notifications" type="checkbox" <?php echo checked( 1, get_the_author_meta( 'fictioneer_comment_reply_notifications', $current_user->ID ), false ); ?> value="1">
        <label for="fictioneer_comment_reply_notifications"><?php _e( 'Always subscribe to comment reply notifications', 'fictioneer' ) ?></label>
      </div>
    <?php endif; ?>

  </div>

  <?php
    wp_nonce_field(
      'fictioneer_update_frontend_user_profile',
      'fictioneer_frontend_user_profile_nonce'
    );
  ?>

  <input name="action" id="action" type="hidden" value="update_profile">

  <input name="user_id" id="user_id" type="hidden" value="<?php echo $current_user->ID; ?>">

  <div class="profile__actions">
    <input name="submit" id="submit" type="submit" value="<?php esc_attr_e( 'Update Profile', 'fictioneer' ) ?>" class="button">
  </div>

</form>
