<?php
/**
 * Partial: Plugin Settings
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.9.4
 */
?>

<?php

// Setup
$fonts = fictioneer_get_font_data();

?>

<div class="fictioneer-settings">

  <?php fictioneer_settings_header( 'fonts' ); ?>

  <div class="fictioneer-settings__content">
    <div class="fictioneer-single-column">

      <div class="fictioneer-card">
        <div class="fictioneer-card__wrapper">
          <div class="fictioneer-card__content">
            <div class="fictioneer-card__row">
              <p><?php
                _e( 'Currently, this is only for your information, you cannot actually do anything here yet. Future updates may extend the functionality.', 'fictioneer' );
              ?></p>
            </div>
          </div>
        </div>
      </div>

      <?php foreach ( $fonts as $key => $font ) : ?>

        <?php
          $name = $font['name'] ?? $key;
          $family = $font['family'] ?? _x( 'n/a', 'Settings font card.', 'fictioneer' );
          $type = $font['type'] ?? '';
          $skip = $font['skip'] ?? false;
          $version = $font['version'] ?? '';
          $charsets = $font['charsets'] ?? _x( 'n/a', 'Settings font card.', 'fictioneer' );
          $formats = $font['formats'] ?? _x( 'n/a', 'Settings font card.', 'fictioneer' );
          $about = $font['about'] ?? _x( 'No description provided', 'Settings font card.', 'fictioneer' );
          $weights = $font['weights'] ?? _x( 'n/a', 'Settings font card.', 'fictioneer' );
          $styles = $font['styles'] ?? _x( 'n/a', 'Settings font card.', 'fictioneer' );
          $sources = empty( $font['sources'] ?? '' ) ? '' : explode( '|||', $font['sources'] );
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
            <h3 class="fictioneer-card__header"><?php
              echo $name;

              if ( ! empty( $version ) ) {
                printf( _x( ' (v%s)', 'Settings font card.', 'fictioneer' ), $version );
              }
            ?></h3>

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
                    _ex( 'Family', 'Settings font card.', 'fictioneer' );
                  ?></div>
                  <div class="fictioneer-card__box-content"><?php
                    echo $family;

                    if ( ! empty( $type ) ) {
                      echo ", {$type}";
                    }
                  ?></div>
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
