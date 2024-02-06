<?php
/**
 * Partial: Plugin Settings
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.9.x
 */
?>

<?php

$font_dir = get_template_directory() . '/fonts';
$fonts = [];

if ( is_dir( $font_dir ) ) {
  $all_font_folders = scandir( $font_dir );

  foreach ( $all_font_folders as $font_folder ) {
    if ( $font_folder == '.' || $font_folder == '..' ) {
      continue;
    }

    $full_path = $font_dir . '/' . $font_folder;

    if ( is_dir( $full_path ) ) {
      $fonts[] = $font_folder;
    }
  }
}

?>

<div class="fictioneer-settings">

  <?php fictioneer_settings_header( 'fonts' ); ?>

  <div class="fictioneer-settings__content">
    <div class="fictioneer-single-column">

      <?php foreach ( $fonts as $font ) : ?>
        <div class="fictioneer-card">
          <div class="fictioneer-card__wrapper">
            <h3 class="fictioneer-card__header"><?php echo $font; ?></h3>
            <div class="fictioneer-card__content">

              <div class="fictioneer-card__row">This is a font folder.</div>

            </div>
          </div>
        </div>
      <?php endforeach; ?>

      <?php do_action( 'fictioneer_admin_settings_fonts' ); ?>
    </div>
  </div>

</div>
