<?php
/**
 * Partial: Account - Danger Zone
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.0
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

// Setup
$current_user = $args['user'];

// Do not show for roles above subscriber
if ( ! current_user_can( 'fcn_allow_self_delete' ) ) return;

?>

<h3 class="profile__danger-headline"><?php _e( 'Danger Zone', 'fictioneer' ); ?></h3>

<p class="profile__description"><?php _e( 'You can delete your account and associated data with it. Submitted <em>content</em> such as comments will remain under the “Deleted User” name unless you clear that <em>prior</em>. Be aware that once you delete your account, there is no going back.', 'fictioneer' ); ?></p>

<div class="profile__actions">
  <button id="button-delete-my-account" type="button" data-nonce="<?php echo wp_create_nonce( 'fictioneer_delete_account' ); ?>" data-warning="<?php esc_attr_e( 'Are you sure? Your account will be irrevocably deleted, although this will not remove your comments. Enter %s to confirm.', 'fictioneer' ); ?>" class="button _danger" data-id="<?php echo $current_user->ID; ?>"><?php _e( 'Delete Account', 'fictioneer' ); ?></button>
</div>
