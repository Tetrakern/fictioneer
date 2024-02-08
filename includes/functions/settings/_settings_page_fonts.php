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
$primary_font = get_theme_mod( 'primary_font_family_value', 'Open Sans' );
$secondary_font = get_theme_mod( 'secondary_font_family_value', 'Lato' );
$heading_font = get_theme_mod( 'heading_font_family_value', 'Open Sans' );

?>

<div class="fictioneer-settings">

  <?php fictioneer_settings_header( 'fonts' ); ?>

  <div class="fictioneer-settings__content">

    <form method="post" action="options.php" class="fictioneer-form">
      <?php settings_fields( 'fictioneer-settings-fonts-group' ); ?>

      <div class="fictioneer-single-column">

        <div class="fictioneer-card">
          <div class="fictioneer-card__wrapper">
            <h3 class="fictioneer-card__header"><?php _e( 'Google Fonts', 'fictioneer' ); ?></h3>
            <div class="fictioneer-card__content">

              <div class="fictioneer-card__row">
                <p><?php
                  printf(
                    __( 'You can install <a href="%s" target="_blank">Google Fonts</a> by adding the embed link below, which you can find in the href attribute provided on the right after selecting the font styles. One font per link, but be aware that the more fonts you load, the slower your site becomes. Also note that using Google Fonts violates the GDPR.', 'fictioneer' ),
                    'https://fonts.google.com/'
                  );
                ?></p>
              </div>

              <div class="fictioneer-card__row">
                <textarea name="fictioneer_google_fonts_links" id="fictioneer_google_fonts_links" rows="4" placeholder="<?php echo FICTIONEER_OPTIONS['strings']['fictioneer_google_fonts_links']['placeholder']; ?>"><?php echo get_option( 'fictioneer_google_fonts_links' ); ?></textarea>
                <p class="fictioneer-sub-label"><?php _e( 'Only one font per link per line.', 'fictioneer' ); ?></p>
              </div>

            </div>
          </div>
        </div>

        <div class="fictioneer-actions"><?php submit_button(); ?></div>

        <div>
          <h1 style="margin-bottom: 12px;"><?php _e( 'Installed Fonts', 'fictioneer' ); ?></h1>
          <p><?php
            printf(
              __( 'See <a href="%s" target="_blank">installation guide</a> on how to add custom fonts yourself. You can assign the primary, secondary, and heading fonts in the <a href="%s">Customizer > Layout</a>.', 'fictioneer' ),
              'https://github.com/Tetrakern/fictioneer/blob/main/INSTALLATION.md#custom-fonts',
              wp_customize_url()
            );
          ?></p>
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

                if ( $primary_font === $font['family'] ) {
                  _ex( ' — Primary Font', 'Settings font card.', 'fictioneer' );
                }

                if ( $secondary_font === $font['family'] ) {
                  _ex( ' — Secondary Font', 'Settings font card.', 'fictioneer' );
                }

                if ( $heading_font === $font['family'] ) {
                  _ex( ' — Heading Font', 'Settings font card.', 'fictioneer' );
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
                    <div class="fictioneer-card__box-content"><?php echo implode( ', ', $weights ) ?: $fallback; ?></div>
                  </div>

                  <div class="fictioneer-card__box">
                    <div class="fictioneer-card__box-title"><?php
                      _ex( 'Styles', 'Settings font card.', 'fictioneer' );
                    ?></div>
                    <div class="fictioneer-card__box-content"><?php echo implode( ', ', $styles ) ?: $fallback; ?></div>
                  </div>

                  <div class="fictioneer-card__box">
                    <div class="fictioneer-card__box-title"><?php
                      _ex( 'Formats', 'Settings font card.', 'fictioneer' );
                    ?></div>
                    <div class="fictioneer-card__box-content"><?php echo implode( ', ', $formats ) ?: $fallback; ?></div>
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
                    <div class="fictioneer-card__box-content"><?php echo implode( ', ', $charsets ) ?: $fallback; ?></div>
                  </div>

                </div>

              </div>
            </div>
          </div>
        <?php endforeach; ?>

        <?php do_action( 'fictioneer_admin_settings_fonts' ); ?>
      </div>

    </form>
  </div>
</div>
