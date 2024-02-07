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
// $primary_font = get_theme_mod( 'primary_font_family_value', 'Open Sans' );
// $secondary_font = get_theme_mod( 'secondary_font_family_value', 'Lato' );
// $heading_font = get_theme_mod( 'heading_font_family_value', 'Open Sans' );

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
                printf(
                  __( 'This is an overview of all installed fonts; see <a href="%s" target="_blank">Installation guide</a> on how to add custom fonts yourself. You can assign the primary, secondary, and heading fonts in the <a href="%s">Customizer</a>.', 'fictioneer' ),
                  'https://github.com/Tetrakern/fictioneer/blob/main/INSTALLATION.md#custom-fonts',
                  wp_customize_url()
                );
              ?></p>
            </div>
          </div>
        </div>
      </div>

      <?php foreach ( $fonts as $key => $font ) : ?>

        <?php
          $fallback = _x( 'n/a', 'Settings font card.', 'fictioneer' );
          $name = $font['name'] ?? $key;
          $family = $font['family'] ?? $fallback;
          $type = $font['type'] ?? '';
          $skip = $font['skip'] ?? false;
          $version = $font['version'] ?? '';
          $charsets = $font['charsets'] ?? [ $fallback ];
          $formats = $font['formats'] ?? [ $fallback ];
          $about = $font['about'] ?? _x( 'No description provided', 'Settings font card.', 'fictioneer' );
          $weights = $font['weights'] ?? [ $fallback ];
          $styles = $font['styles'] ?? [ $fallback ];
          $sources = $font['sources'] ?? [];
          $note = $font['note'] ?? '';
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

                if ( ! empty( $sources ) ) {
                  echo _nx(
                    ' <strong>Source:</strong> ',
                    ' <strong>Sources:</strong> ',
                    count( $sources ),
                    'Settings font card.',
                    'fictioneer'
                  );

                  echo implode(
                    ', ',
                    array_map(
                      function( $item ) {
                        return sprintf( '<a href="%s" target="_blank">%s</a>', $item['url'], $item['name'] );
                      },
                      $sources
                    )
                  );
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
                  <div class="fictioneer-card__box-content"><?php echo implode( ', ', $weights ); ?></div>
                </div>

                <div class="fictioneer-card__box">
                  <div class="fictioneer-card__box-title"><?php
                    _ex( 'Styles', 'Settings font card.', 'fictioneer' );
                  ?></div>
                  <div class="fictioneer-card__box-content"><?php echo implode( ', ', $styles ); ?></div>
                </div>

                <div class="fictioneer-card__box">
                  <div class="fictioneer-card__box-title"><?php
                    _ex( 'Formats', 'Settings font card.', 'fictioneer' );
                  ?></div>
                  <div class="fictioneer-card__box-content"><?php echo implode( ', ', $formats ); ?></div>
                </div>

                <div class="fictioneer-card__box">
                  <div class="fictioneer-card__box-title"><?php
                    _ex( 'Family', 'Settings font card.', 'fictioneer' );
                  ?></div>
                  <div class="fictioneer-card__box-content"><?php
                    echo fictioneer_font_family_value( $family );

                    if ( ! empty( $type ) ) {
                      echo ", {$type}";
                    }
                  ?></div>
                </div>

                <div class="fictioneer-card__box" style="flex-grow: 8;">
                  <div class="fictioneer-card__box-title"><?php
                    _ex( 'Charsets', 'Settings font card.', 'fictioneer' );
                  ?></div>
                  <div class="fictioneer-card__box-content"><?php echo implode( ', ', $charsets ); ?></div>
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
