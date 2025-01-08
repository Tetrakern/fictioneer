<?php
/**
 * Partial: Account - Danger Zone
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

// Do not show for roles above subscriber
if ( ! current_user_can( 'fcn_allow_self_delete' ) ) {
  return;
}

// Prompts
$confirmation = _x( 'DELETE', 'Prompt deletion confirmation string.', 'fictioneer' );
$delete_account_prompt = sprintf(
  __( 'Are you sure? Your account will be irrevocably deleted, although this will not remove your comments. Enter %s to confirm.', 'fictioneer' ),
  $confirmation
);

?>

<h3 class="profile__danger-headline"><?php _e( 'Danger Zone', 'fictioneer' ); ?></h3>

<p class="profile__description"><?php _e( 'You can delete your account and associated user data with it. Submitted <em>content</em> such as comments and posts will remain under the “Deleted User” name unless you remove them <em>prior</em>. Be aware that once you delete your account, there is no going back.', 'fictioneer' ); ?></p>

<div class="profile__actions">
  <button id="button-delete-my-account" type="button" class="button _danger" data-nonce="<?php echo wp_create_nonce( 'fictioneer_delete_account' ); ?>" data-id="<?php echo $current_user->ID; ?>" data-confirm="<?php echo $confirmation; ?>" data-warning="<?php echo esc_attr( $delete_account_prompt ); ?>" data-fictioneer-target="dcjProtected" disabled><?php _e( 'Delete Account', 'fictioneer' ); ?></button>
</div>
