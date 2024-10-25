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
  _e( 'You can add up to three custom CSS skins (max. 200 KB each), with one active at a time. These skins apply only to your current device and browser (and may vanish at any time), but you can manually sync them up and down with your account. Be cautious about which skin you trust, as they can mess up the site.', 'fictioneer' );
?></p>

<p class="profile__description"><?php
  printf(
    __( 'To create your own skin, <a href="%s" download>download this template</a>.', 'fictioneer' ),
    esc_url( get_template_directory_uri() . '/css/skin-template.css' )
  );
?></p>

<div class="profile__segment" data-controller="css-skin">

  <template id="template-custom-skin" data-css-skin-target="template">
    <div class="custom-skin" data-css-skin-finder="skin-item">
      <button type="button" class="custom-skin__toggle" data-action="click->css-skin#toggle">
        <i class="fa-regular fa-circle off"></i>
        <i class="fa-solid fa-circle-dot on"></i>
      </button>
      <div class="custom-skin__info">
        <span class="custom-skin__name" data-css-skin-finder="name">&mdash;</span>
        <span class="custom-skin__spacer"></span>
        <span class="custom-skin__version" data-css-skin-finder="version">&mdash;</span>
        <span class="custom-skin__spacer"></span>
        <span class="custom-skin__author" data-css-skin-finder="author">&mdash;</span>
      </div>
      <button type="button" class="custom-skin__delete" data-action="click->css-skin#delete"><i class="fa-solid fa-trash-can"></i></button>
    </div>
  </template>

  <div class="custom-skin-list" data-css-skin-target="list"></div>

  <form class="custom-skin _upload" data-css-skin-target="form">
    <script type="text/javascript" data-jetpack-boost="ignore" data-no-optimize="1" data-no-defer="1" data-no-minify="1">var fcn_skinTranslations = <?php echo json_encode( fictioneer_get_skin_translations() ); ?>;</script>
    <input type="file" name="css-file" accept=".css" data-css-skin-target="file">
    <i class="fa-solid fa-plus"></i>
  </form>

  <div class="profile__actions">
    <button type="button" class="button" data-action="click->css-skin#upload" data-disable-with="<?php esc_attr_e( 'Uploading…', 'fictioneer' ); ?>"><?php _e( 'Sync Up', 'fictioneer' ); ?></button>
    <button type="button" class="button" data-action="click->css-skin#download" data-disable-with="<?php esc_attr_e( 'Downloading…', 'fictioneer' ); ?>"><?php _e( 'Sync Down', 'fictioneer' ); ?></button>
  </div>

</div>
