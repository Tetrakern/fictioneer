<?php
/**
 * Partial: Account - Profile
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


// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$current_user = $args['user'];
$fingerprint = fictioneer_get_user_fingerprint( $current_user->ID );
$new_email = get_user_meta( $current_user->ID, '_new_email', true );
$submit_url = admin_url( 'admin-post.php?action=fictioneer_update_frontend_profile' );
$email_change_cancel_url = wp_nonce_url(
  admin_url( 'admin-post.php?action=fictioneer_cancel_frontend_email_change' ),
  'fictioneer_cancel_frontend_email_change',
  'fictioneer_nonce'
);

// Flags
$renaming_disabled = $current_user->fictioneer_admin_disable_renaming;

?>

<h3 id="profile" class="profile__account-headline"><?php _e( 'Profile', 'fictioneer' ); ?></h3>

<form method="post" action="<?php echo esc_url( $submit_url ); ?>" class="profile__account profile__segment">

  <div class="profile__identity">

    <div class="profile__input-group">
      <div class="profile__input-label"><?php
        printf(
          _x( 'Username (ID: %s)', 'Username profile label with user ID.', 'fictioneer' ),
          $current_user->ID
        );
      ?></div>
      <div class="profile__input-wrapper">
        <input
          type="text"
          value="<?php echo esc_attr( $current_user->user_login ); ?>"
          class="profile__input-field profile__nickname"
          disabled
        >
        <p class="profile__input-note"><?php _e( 'Your unique name in the system. Cannot be changed.', 'fictioneer' ); ?></p>
      </div>
    </div>

    <div class="profile__input-group">
      <div class="profile__input-label"><?php _e( 'Fingerprint', 'fictioneer' ); ?></div>
      <div class="profile__input-wrapper">
        <input
          type="text"
          value="<?php echo esc_attr( $fingerprint ); ?>"
          class="profile__input-field profile__fingerprint"
          disabled
        >
        <p class="profile__input-note"><?php _e( 'Your unique hash. Used to distinguish commenters.', 'fictioneer' ); ?></p>
      </div>
    </div>

    <div class="profile__input-group">
      <div class="profile__input-label"><?php _e( 'Nickname', 'fictioneer' ); ?></div>
      <div class="profile__input-wrapper">
        <input
          name="nickname"
          type="text"
          value="<?php echo esc_attr( $current_user->nickname ); ?>"
          class="profile__input-field profile__nickname"
          <?php if ( $renaming_disabled ) echo 'disabled'; ?>
        >
        <p class="profile__input-note"><?php _e( 'Your nickname will be displayed instead of your username. Not unique!', 'fictioneer' ); ?></p>
      </div>
    </div>

    <?php if ( $renaming_disabled ) : ?>
      <ul class="profile__admin-notes">
        <li>
          <i class="fa-solid fa-bolt"></i>
          <span><?php _e( 'Renaming capability disabled.', 'fictioneer' ); ?></span>
        </li>
      </ul>
    <?php endif; ?>

    <div class="profile__input-group">
      <div class="profile__input-label"><?php _e( 'Email Address', 'fictioneer' ); ?></div>
      <div class="profile__input-wrapper">
        <input
          name="email"
          type="text"
          value="<?php echo esc_attr( $current_user->user_email ); ?>"
          class="profile__input-field profile__email"
        >
        <?php if ( $new_email && $new_email['newemail'] !== $current_user->user_email ) : ?>
          <p class="profile__input-note">
            <?php
              printf(
                __( 'Update to <strong>%1$s</strong> pending. Please check your previous email address to confirm. <a href="%2$s">Cancel.</a>', 'fictioneer' ),
                $new_email['newemail'],
                esc_url( $email_change_cancel_url )
              );
            ?>
          </p>
        <?php else : ?>
          <p class="profile__input-note"><?php _e( 'Changed email addresses will not become active until confirmed.', 'fictioneer' ); ?></p>
        <?php endif; ?>
      </div>
    </div>

  </div>

  <div class="profile__flags">

    <div class="profile__input-wrapper _checkbox" title="<?php esc_attr_e( 'Ignore site avatar and always pull from the Gravatar service.', 'fictioneer' ); ?>">
      <input type="hidden" name="fictioneer_enforce_gravatar" value="0">
      <input
        id="fictioneer_enforce_gravatar"
        name="fictioneer_enforce_gravatar"
        type="checkbox"
        value="1"
        <?php echo checked( 1, get_the_author_meta( 'fictioneer_enforce_gravatar', $current_user->ID ), false ); ?>
      >
      <label for="fictioneer_enforce_gravatar"><?php _e( 'Always use gravatar', 'fictioneer' ); ?></label>
    </div>

    <div class="profile__input-wrapper _checkbox" title="<?php esc_attr_e( 'Hide your avatar.', 'fictioneer' ); ?>">
      <input type="hidden" name="fictioneer_disable_avatar" value="0">
      <input
        id="fictioneer_disable_avatar"
        name="fictioneer_disable_avatar"
        type="checkbox"
        value="1"
        <?php echo checked( 1, get_the_author_meta( 'fictioneer_disable_avatar', $current_user->ID ), false ); ?>
      >
      <label for="fictioneer_disable_avatar"><?php _e( 'Disable avatar', 'fictioneer' ); ?></label>
    </div>

    <div class="profile__input-wrapper _checkbox" title="<?php esc_attr_e( 'Prevent avatar from being overwritten with the latest OAuth account login.', 'fictioneer' ); ?>">
      <input type="hidden" name="fictioneer_lock_avatar" value="0">
      <input
        id="fictioneer_lock_avatar"
        name="fictioneer_lock_avatar"
        type="checkbox"
        value="1"
        <?php echo checked( 1, get_the_author_meta( 'fictioneer_lock_avatar', $current_user->ID ), false ); ?>
      >
      <label for="fictioneer_lock_avatar"><?php _e( 'Lock avatar', 'fictioneer' ); ?></label>
    </div>

    <?php if ( get_option( 'fictioneer_enable_custom_badges' ) ) : ?>

      <div class="profile__input-wrapper _checkbox" title="<?php esc_attr_e( 'Hide your badge on comments.', 'fictioneer' ); ?>">
        <input type="hidden" name="fictioneer_hide_badge" value="0">
        <input
          id="fictioneer_hide_badge"
          name="fictioneer_hide_badge"
          type="checkbox"
          value="1"
          <?php echo checked( 1, get_the_author_meta( 'fictioneer_hide_badge', $current_user->ID ), false ); ?>
        >
        <label for="fictioneer_hide_badge"><?php _e( 'Hide badge', 'fictioneer' ); ?></label>
      </div>

      <?php if ( ! empty( get_the_author_meta( 'fictioneer_badge_override', $current_user->ID ) ) ) : ?>
        <div class="profile__input-wrapper _checkbox" title="<?php esc_attr_e( 'Disable custom badge overrides set by administrators.', 'fictioneer' ); ?>">
          <input type="hidden" name="fictioneer_disable_badge_override" value="0">
          <input
            id="fictioneer_disable_badge_override"
            name="fictioneer_disable_badge_override"
            type="checkbox"
            value="1"
            <?php echo checked( 1, get_the_author_meta( 'fictioneer_disable_badge_override', $current_user->ID ), false ); ?>
          >
          <label for="fictioneer_disable_badge_override"><?php _e( 'Override assigned badge', 'fictioneer' ); ?></label>
        </div>
      <?php endif; ?>

    <?php endif; ?>

    <?php if ( get_option( 'fictioneer_enable_comment_notifications' ) ) : ?>
      <div class="profile__input-wrapper _checkbox" title="<?php esc_attr_e( 'Toggle the subscription checkbox on comments by default.', 'fictioneer' ); ?>">
        <input type="hidden" name="fictioneer_comment_reply_notifications" value="0">
        <input
          id="fictioneer_comment_reply_notifications"
          name="fictioneer_comment_reply_notifications"
          type="checkbox"
          value="1"
          <?php echo checked( 1, get_the_author_meta( 'fictioneer_comment_reply_notifications', $current_user->ID ), false ); ?>
        >
        <label for="fictioneer_comment_reply_notifications"><?php _e( 'Always subscribe to comments', 'fictioneer' ); ?></label>
      </div>
    <?php endif; ?>

  </div>

  <?php wp_nonce_field( 'fictioneer_update_frontend_profile', 'fictioneer_nonce' ); ?>

  <input name="user_id" type="hidden" value="<?php echo $current_user->ID; ?>">

  <div class="profile__actions">
    <input name="submit" type="submit" value="<?php esc_attr_e( 'Update Profile', 'fictioneer' ); ?>" class="button">
  </div>

</form>
