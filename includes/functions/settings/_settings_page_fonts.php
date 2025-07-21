<?php
/**
 * Partial: Plugin Settings
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.9.4
 */


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
            <h3 class="fictioneer-card__header"><?php _e( 'Preload Fonts (Advanced)', 'fictioneer' ); ?></h3>
            <div class="fictioneer-card__content">

              <div class="fictioneer-card__row">
                <p><?php
                  printf(
                    __( 'To improve the loading time of your site and reduce FOUT (Flash of Unstyled Text), you can preload fonts in your <a href="%s" target="_blank">HTTP headers</a>. This is also beneficial to your search rank. You need to either provide the font file path relative to the WordPress root directory or the full URL if external. The font file can be located anywhere in your WordPress directory as long as the relative path is correct. Only preload font files (charsets, styles, weights, etc.) that are immediately visible on page load (above the fold).', 'fictioneer' ),
                    'https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers'
                  );
                ?></p>
              </div>

              <div class="fictioneer-card__row">
                <textarea name="fictioneer_http_headers_link_fonts" id="fictioneer_http_headers_link_fonts" rows="4" placeholder="<?php echo "https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700\n/wp-content/themes/fictioneer/fonts/open-sans/open-sans-v40-regular.woff2"; ?>"><?php
                  echo get_option( 'fictioneer_http_headers_link_fonts' );
                ?></textarea>
                <p class="fictioneer-sub-label"><?php _e( 'Only one link per line.', 'fictioneer' ); ?></p>
              </div>

              <div class="fictioneer-card__row">
                <code><pre><?php echo "/wp-content/themes/fictioneer/fonts/open-sans/open-sans-v40-regular.woff2\n/wp-content/themes/fictioneer/fonts/open-sans/open-sans-v40-300.woff2\n/wp-content/themes/fictioneer/fonts/open-sans/open-sans-v40-600.woff2\n/wp-content/themes/fictioneer/fonts/open-sans/open-sans-v40-700.woff2"; ?></pre></code>
                <p class="fictioneer-sub-label"><?php _e( 'This should work for the default theme "Open Sans" font, both in dark and light mode. Depending on the fonts or styles/weights/etc. used above the fold, you may need more.', 'fictioneer' ); ?></p>
              </div>

              <div class="fictioneer-card__row">
                <details>
                  <summary><?php _e( 'Reference for theme fonts', 'fictioneer' ); ?></summary>
                  <div><?php
                    $valid_extensions = ['woff', 'woff2', 'ttf', 'otf', 'eot', 'svg', 'fon'];

                    foreach ( $fonts as $key => $data ) {
                      if ( ! isset( $data['dir'] ) ) {
                        continue;
                      }

                      $root = $data['in_child_theme'] ? get_stylesheet_directory() : get_template_directory();
                      $dir = $root . $data['dir'];
                      $files = scandir( $dir, SCANDIR_SORT_DESCENDING );
                      $font_files = [];

                      if ( ! $files ) {
                        continue;
                      }

                      foreach ( $files as $file ) {
                        $path = $dir . DIRECTORY_SEPARATOR . $file;

                        if ( ! is_file( $path ) ) {
                          continue;
                        }

                        $extension = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );

                        if ( in_array( $extension, $valid_extensions, true ) ) {
                          $normalized = str_replace( '\\', '/', $path );
                          $shortened = strstr( $normalized, '/wp-content' );
                          $font_files[] = $shortened ?: $normalized;
                        }
                      }

                      if ( empty( $font_files ) ) {
                        continue;
                      }

                      printf(
                        '<details style="margin: 12px 0 0 12px;"><summary>%s</summary><div><code><pre>%s</pre></code></div></details>',
                        $data['name'],
                        implode( '<br>', array_map( 'esc_html', $font_files ) )
                      );
                    }
                  ?></div>
                </details>
              </div>

            </div>
          </div>
        </div>

        <div class="fictioneer-card">
          <div class="fictioneer-card__wrapper">
            <h3 class="fictioneer-card__header"><?php _e( 'Critical Font CSS (Advanced)', 'fictioneer' ); ?></h3>
            <div class="fictioneer-card__content">

              <div class="fictioneer-card__row">
                <p><?php
                  _e( 'If you use a different font than "Open Sans" and want to reduce FOUT (Flash of Unstyled Text), you need to inline the <code>@font</code> CSS definitions into the <code>&lt;head&gt;</code> of your site, ensuring they are loaded before the content is rendered. This is in addition to the preloading of the font files. For theme fonts, you can find these definitions under <code>/{theme-folder}/fonts/{font-folder}/</code> in the respective <code>font.css</code> file. If you load fonts by other means, you need to find the CSS yourself. Just add the CSS in the box below.', 'fictioneer' );
                ?></p>
              </div>

              <div class="fictioneer-card__row">
                <textarea name="fictioneer_critical_font_css" id="fictioneer_critical_font_css" rows="4" placeholder="<?php echo esc_attr( '@font-face{font-display:swap;font-family:"Crimson Text";font-style:normal;font-weight:400;size-adjust:115%;src:url("../fonts/crimson-text/crimson-text-v19-regular.woff2") format("woff2")} ...' ); ?>"><?php echo get_option( 'fictioneer_critical_font_css' ); ?></textarea>
              </div>

              <div class="fictioneer-card__row">
                <details>
                  <summary><?php _e( 'Reference for theme fonts', 'fictioneer' ); ?></summary>
                  <div><?php
                    foreach ( $fonts as $key => $data ) {
                      if ( ! isset( $data['dir'] ) ) {
                        continue;
                      }

                      $root = $data['in_child_theme'] ? get_stylesheet_directory() : get_template_directory();
                      $dir = $root . $data['dir'];
                      $files = scandir( $dir, SCANDIR_SORT_DESCENDING );
                      $css = '';

                      if ( ! $files ) {
                        continue;
                      }

                      foreach ( $files as $file ) {
                        $path = $dir . DIRECTORY_SEPARATOR . $file;

                        if ( ! is_file( $path ) ) {
                          continue;
                        }

                        $extension = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );

                        if ( $extension === 'css' ) {
                          $css = file_get_contents( str_replace( '\\', '/', $path ) );
                        }
                      }

                      if ( empty( $css ) ) {
                        continue;
                      }

                      printf(
                        '<details style="margin: 12px 0 0 12px;"><summary>%s</summary><div><code><pre>%s</pre></code></div></details>',
                        $data['name'],
                        esc_html( $css )
                      );
                    }
                  ?></div>
                </details>
              </div>

            </div>
          </div>
        </div>

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
                <textarea name="fictioneer_google_fonts_links" id="fictioneer_google_fonts_links" rows="4" placeholder="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"><?php echo get_option( 'fictioneer_google_fonts_links' ); ?></textarea>
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
              __( 'See <a href="%s" target="_blank">installation guide</a> on how to add custom fonts yourself. You can assign fonts in the <a href="%s">Customizer</a>. If the fonts are listed but not displayed, purge the theme caches under <a href="%s">Tools</a> and force-refresh the page to clear the browser cache as well.', 'fictioneer' ),
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
