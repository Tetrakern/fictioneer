<?php
/**
 * Partial: Account - Skins
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.26.0
 *
 * @internal $args['user']          Current user.
 * @internal $args['is_admin']      True if the user is an administrator.
 * @internal $args['is_author']     True if the user is an author (by capabilities).
 * @internal $args['is_editor']     True if the user is an editor.
 * @internal $args['is_moderator']  True if the user is a moderator (by capabilities).
 */


// No direct access!
defined( 'ABSPATH' ) OR exit;

// Cookie set?
if ( ! isset( $_COOKIE['fcnLoggedIn'] ) ) {
  return;
}

// Setup
$current_user = $args['user'];

?>

<h3 class="profile__skins-headline"><?php _e( 'Skins', 'fictioneer' ); ?></h3>

<p class="profile__description"><?php
  _e( 'You can upload up to three custom CSS skins (max. 200 KB each), with one active at a time. These skins apply only to your current device and browser (and may vanish at any time), but you can sync them up and down with your account. Be cautious about which skin you trust, as they can mess up the site.', 'fictioneer' );
?></p>

<p class="profile__description"><?php
  printf(
    __( 'To create your own skin, <a href="%s" download>download this template</a>.', 'fictioneer' ),
    esc_url( get_template_directory_uri() . '/css/skin-template.css' )
  );
?></p>

<?php fictioneer_render_skin_interface(); ?>
