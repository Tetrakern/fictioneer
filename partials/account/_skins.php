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
$template_download = esc_url( get_template_directory_uri() . '/css/skin-template.css' );

$translations = array(
  'wrongFileType' => _x( 'Wrong file type. Please upload a valid CSS file.', 'Custom CSS skin.', 'fictioneer' ),
  'invalidCss' => _x( 'Invalid file contents. Please check for missing braces ("{}") or forbidden characters ("<").', 'Custom CSS skin.', 'fictioneer' ),
  'missingMetaData' => _x( 'File is missing the "Name" meta data.', 'Custom CSS skin.', 'fictioneer' ),
  'tooManySkins' => _x( 'You cannot upload more than 3 skins.', 'Custom CSS skin.', 'fictioneer' ),
  'wrongFingerprint' => _x( 'Wrong or missing fingerprint hash.', 'Custom CSS skin.', 'fictioneer' ),
  'fileTooLarge' => _x( 'Maximum file size of 200 KB exceeded.', 'Custom CSS skin.', 'fictioneer' ),
  'invalidJson' => _x( 'Invalid JSON.', 'Custom CSS skin.', 'fictioneer' ),
  'error' => _x( 'Error.', 'Custom CSS skin.', 'fictioneer' )
);

?>

<h3 class="profile__skins-headline"><?php _e( 'Skins', 'fictioneer' ); ?></h3>

<p class="profile__description"><?php
  _e( 'You can add up to three custom CSS skins (max. 200 KB each), with one active at a time. These skins apply only to your current device and browser (and may vanish at any time), but you can manually sync them up and down with your account. Be cautious about which skin you trust, as they can mess up the site.', 'fictioneer' );
?></p>

<p class="profile__description"><?php
  printf(
    __( 'To create your own skin, <a href="%s" download>download this template</a>.', 'fictioneer' ),
    $template_download
  );
?></p>

<template id="template-custom-skin">
  <div class="custom-skin">
    <button type="button" class="custom-skin__toggle" data-click-action="skin-toggle">
      <i class="fa-regular fa-circle off"></i>
      <i class="fa-solid fa-circle-dot on"></i>
    </button>
    <div class="custom-skin__info">
      <span class="custom-skin__name" data-finder="skin-name">&mdash;</span>
      <span class="custom-skin__spacer"></span>
      <span class="custom-skin__version" data-finder="skin-version">&mdash;</span>
      <span class="custom-skin__spacer"></span>
      <span class="custom-skin__author" data-finder="skin-author">&mdash;</span>
    </div>
    <button type="button" class="custom-skin__delete" data-click-action="skin-delete"><i class="fa-solid fa-trash-can"></i></button>
  </div>
</template>

<div class="profile__segment">

  <div class="custom-skin-list"></div>

  <form id="css-upload-form" class="custom-skin _upload">
    <script type="text/javascript" data-jetpack-boost="ignore" data-no-optimize="1" data-no-defer="1" data-no-minify="1">
      var fcn_skinTranslations = <?php echo json_encode( $translations ); ?>;
    </script>
    <input type="file" id="css-file" name="css-file" accept=".css">
    <i class="fa-solid fa-plus"></i>
  </form>

  <div class="profile__actions">
    <button type="button" class="button" data-click-action="skins-sync-up" data-disable-with="<?php esc_attr_e( 'Uploading…', 'fictioneer' ); ?>"><?php _e( 'Sync Up', 'fictioneer' ); ?></button>
    <button type="button" class="button" data-click-action="skins-sync-down" data-disable-with="<?php esc_attr_e( 'Downloading…', 'fictioneer' ); ?>"><?php _e( 'Sync Down', 'fictioneer' ); ?></button>
  </div>

</div>
