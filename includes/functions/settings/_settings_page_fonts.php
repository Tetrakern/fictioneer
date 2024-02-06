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
    $info_file = $full_path . '/info.txt';

    if ( is_dir( $full_path ) && file_exists( $info_file ) ) {
      $info = [];
      $lines = file( $info_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );

      foreach ( $lines as $line ) {
        list( $key, $value ) = explode( ':', $line, 2 );
        $info[ trim( strtolower( $key ) ) ] = trim( $value );
      }

      $fonts[ $info_file ] = $info;
    }
  }
}

?>

<div class="fictioneer-settings">

  <?php fictioneer_settings_header( 'fonts' ); ?>

  <div class="fictioneer-settings__content">
    <div class="fictioneer-single-column">

      <?php foreach ( $fonts as $key => $font ) : ?>

        <?php
          $name = $font['name'] ?? $key;
          $css = $font['css'] ?? _x( 'n/a', 'Settings font card.', 'fictioneer' );
          $version = $font['version'] ?? 'XX';
          $charsets = $font['charsets'] ?? _x( 'n/a', 'Settings font card.', 'fictioneer' );
          $formats = $font['formats'] ?? _x( 'n/a', 'Settings font card.', 'fictioneer' );
          $about = $font['about'] ?? _x( 'No description provided', 'Settings font card.', 'fictioneer' );
          $weights = $font['weights'] ?? _x( 'n/a', 'Settings font card.', 'fictioneer' );
          $styles = $font['styles'] ?? _x( 'n/a', 'Settings font card.', 'fictioneer' );
          $sources = explode( '|||', $font['sources'] ?? '' );
          $links = [];
          $note = $font['note'] ?? '';

          if ( ! empty( $sources ) ) {
            $links = array_map(
              function( $source ) {
                $parts = explode( '|', $source );

                if ( count( $parts ) < 2 ) {
                  $parts = [ _x( 'Link', 'Settings font card.', 'fictioneer' ), $parts[0] ];
                }

                return sprintf( '<a href="%s" target="_blank">%s</a>', trim( $parts[1] ), trim( $parts[0] ) );
              },
              $sources
            );
          }
        ?>

        <div class="fictioneer-card">
          <div class="fictioneer-card__wrapper">
            <h3 class="fictioneer-card__header"><?php printf( '%s (v%d)', $name, $version ); ?></h3>

            <div class="fictioneer-card__content">

              <div class="fictioneer-card__row"><?php
                echo $about;

                if ( ! empty( $links ) ) {
                  echo _nx(
                    ' <strong>Source:</strong> ',
                    ' <strong>Sources:</strong> ',
                    count( $sources ),
                    'Settings font card.',
                    'fictioneer'
                  );

                  echo implode( ', ', $links );
                }
              ?></div>

              <?php if ( ! empty( $note ) ): ?>
                <div class="fictioneer-card__row"><?php
                  printf( _x( '<strong>Note:</strong> %s', 'Settings font card.', 'fictioneer' ), $note );
                ?></div>
              <?php endif; ?>

              <div class="fictioneer-card__row fictioneer-card__row--boxes">

                <div class="fictioneer-card__box" style="flex-grow: 2;">
                  <div class="fictioneer-card__box-title"><?php
                    _ex( 'Weights', 'Settings font card.', 'fictioneer' );
                  ?></div>
                  <div class="fictioneer-card__box-content"><?php echo $weights; ?></div>
                </div>

                <div class="fictioneer-card__box">
                  <div class="fictioneer-card__box-title"><?php
                    _ex( 'Styles', 'Settings font card.', 'fictioneer' );
                  ?></div>
                  <div class="fictioneer-card__box-content"><?php echo $styles; ?></div>
                </div>

                <div class="fictioneer-card__box">
                  <div class="fictioneer-card__box-title"><?php
                    _ex( 'Formats', 'Settings font card.', 'fictioneer' );
                  ?></div>
                  <div class="fictioneer-card__box-content"><?php echo $formats; ?></div>
                </div>

                <div class="fictioneer-card__box">
                  <div class="fictioneer-card__box-title"><?php
                    _ex( 'CSS', 'Settings font card.', 'fictioneer' );
                  ?></div>
                  <div class="fictioneer-card__box-content"><?php echo $css; ?></div>
                </div>

                <div class="fictioneer-card__box" style="flex-grow: 8;">
                  <div class="fictioneer-card__box-title"><?php
                    _ex( 'Charsets', 'Settings font card.', 'fictioneer' );
                  ?></div>
                  <div class="fictioneer-card__box-content"><?php echo $charsets; ?></div>
                </div>

              </div>

            </div>
          </div>
        </div>
      <?php endforeach; ?>

      <?php do_action( 'fictioneer_admin_settings_fonts' ); ?>
    </div>
  </div>

</div>
