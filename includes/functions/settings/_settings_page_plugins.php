<?php
/**
 * Partial: Plugin Settings
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.7.1
 */


// Setup
$mu_plugins = get_mu_plugins();
$fictioneer_mu_plugin_data = fictioneer_get_mu_plugin_data();

?>

<div class="fictioneer-settings">

  <?php fictioneer_settings_header( 'plugins' ); ?>

  <div class="fictioneer-settings__content">
    <div class="fictioneer-single-column">
      <?php

        // MU-Plugins
        foreach ( $fictioneer_mu_plugin_data as $plugin_data ) {
          // Start HTML ---> ?>
          <div class="fictioneer-card fictioneer-card--plugin <?php echo $plugin_data['active'] ? '' : 'fictioneer-card--disabled'; ?>">
            <div class="fictioneer-card__wrapper">
              <div class="fictioneer-card__header fictioneer-card__header--with-actions">
                <h3><?php
                  echo $plugin_data['name'];

                  if ( ! $plugin_data['active'] ) {
                    _ex( ' — Disabled', 'Settings plugin card.', 'fictioneer' );
                  } elseif ( $plugin_data['update'] ) {
                    _ex( ' — Update', 'Settings plugin card.', 'fictioneer' );
                  }
                ?></h3>
                <div>
                  <?php if ( $plugin_data['active'] && ! $plugin_data['update'] ) : ?>
                    <a class="button button--secondary" href="<?php echo esc_url( add_query_arg( 'mu_plugin', $plugin_data['filename'], fictioneer_admin_action( 'fictioneer_disable_mu_plugin' ) ) ); ?>"><?php _e( 'Disable', 'fictioneer' ); ?></a>
                  <?php else : ?>
                    <a class="button button--secondary" href="<?php echo esc_url( add_query_arg( 'mu_plugin', $plugin_data['filename'], fictioneer_admin_action( 'fictioneer_enable_mu_plugin' ) ) ); ?>"><?php
                      $plugin_data['update'] ? _e( 'Update', 'fictioneer' ) : _e( 'Enable', 'fictioneer' );
                    ?></a>
                  <?php endif; ?>
                </div>
              </div>
              <div class="fictioneer-card__content">
                <div class="fictioneer-card__row">
                  <p><?php echo $plugin_data['description']; ?></p>
                </div>
                <div class="fictioneer-card__row fictioneer-card__row--meta"><?php
                  printf( __( 'Version %s', 'fictioneer' ), $plugin_data['version'] ?? 'n/a' );
                  echo ' | ';
                  printf(
                    __( '<a href="%s" target="_blank" rel="noopener">MU-Plugin</a>', 'fictioneer' ),
                    'https://github.com/Tetrakern/fictioneer/blob/main/INSTALLATION.md#recommended-must-use-plugins'
                  );
                  echo ' | ';
                  printf(
                    __( 'By <a href="%s" target="_blank" rel="noopener">Tetrakern</a>', 'fictioneer' ),
                    'https://github.com/Tetrakern'
                  );
                ?></div>
              </div>
            </div>
          </div>
          <?php // <--- End HTML
        }

        // Hook for plugins
        do_action( 'fictioneer_admin_settings_plugins' );

      ?>
    </div>
  </div>

</div>
