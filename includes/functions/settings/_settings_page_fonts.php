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
$disabled_fonts = get_option( 'fictioneer_disabled_fonts', [] );
$disabled_fonts = is_array( $disabled_fonts ) ? $disabled_fonts : [];

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
                <textarea name="fictioneer_google_fonts_links" id="fictioneer_google_fonts_links" rows="4" placeholder="<?php echo FICTIONEER_OPTIONS['strings']['fictioneer_google_fonts_links']['placeholder'] ?? ''; ?>"><?php echo get_option( 'fictioneer_google_fonts_links' ); ?></textarea>
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
              __( 'See <a href="%s" target="_blank">installation guide</a> on how to add custom fonts yourself. You can assign fonts in the <a href="%s">Customizer</a>. If the fonts are listed but not displayed, purge the theme caches under <a href="%s">Tools</a> and force-refresh he page to clear the browser cache as well.', 'fictioneer' ),
              'https://github.com/Tetrakern/fictioneer/blob/main/INSTALLATION.md#custom-fonts',
              wp_customize_url(),
              '?page=fictioneer_tools'
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
            $disabled = in_array( $key, $disabled_fonts );
          ?>

          <div class="fictioneer-card <?php echo $disabled ? 'fictioneer-card--disabled' : ''; ?>">
            <div class="fictioneer-card__wrapper">
              <h3 class="fictioneer-card__header fictioneer-card__header--with-actions" id="font-<?php echo $key; ?>">
                <div><?php
                  echo $name;

                  if ( ! empty( $version ) ) {
                    printf( _x( ' (v%s)', 'Settings font card.', 'fictioneer' ), $version );
                  }

                  if ( $font['in_child_theme'] ?? 0 ) {
                    _ex( ' — Child Theme', 'Settings font card.', 'fictioneer' );
                  }

                  if ( $font['google_link'] ?? 0 ) {
                    _ex( ' — Google Fonts CDN', 'Settings font card.', 'fictioneer' );
                  }

                  if ( $disabled ) {
                    _ex( ' — Disabled', 'Settings font card.', 'fictioneer' );
                  }
                ?></div>
                <div>
                  <?php if ( ! ( $font['google_link'] ?? 0 ) ) : ?>
                    <?php if ( $disabled ) : ?>
                      <a class="button button--secondary" href="<?php echo esc_url( add_query_arg( 'font', $key, fictioneer_admin_action( 'fictioneer_enable_font' ) ) ); ?>"><?php _e( 'Enable', 'fictioneer' ); ?></a>
                    <?php else : ?>
                      <a class="button button--secondary" href="<?php echo esc_url( add_query_arg( 'font', $key, fictioneer_admin_action( 'fictioneer_disable_font' ) ) ); ?>"><?php _e( 'Disable', 'fictioneer' ); ?></a>
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
              </h3>

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

                <?php if ( ! $disabled ) : ?>
                  <div class="fictioneer-card__row" style="font-family: <?php echo $font['family'] ?? 'inherit'; ?>; font-size: 20px; text-align: center; margin: 24px 0;"><?php
                    echo $font['preview'] ?? _x( 'The quick brown fox jumps over the lazy dog.', 'Settings font fallback preview sentence.', 'fictioneer' );
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
