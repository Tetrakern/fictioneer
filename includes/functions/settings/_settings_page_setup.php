<?php
/**
 * Partial: Setup Settings
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.20.3
 */


// Setup

?>

<div class="fictioneer-settings">

  <div class="fictioneer-settings__content">
    <form class="fictioneer-single-column fictioneer-single-column--setup">

      <h1><?php _e( 'Fictioneer Setup', 'fictioneer' ); ?></h1>

      <div><?php
        printf(
          '<p>Welcome and thank you for using Fictioneer!</p><p>Please make sure to read the installation guide thoroughly. If any issues arise, refer to the <a href="%1$s" target="_blank" rel="noopener">documentation</a> and <a href="%1$s" target="_blank" rel="noopener">FAQ</a> first. If you are new to WordPress, consider looking up some guides, as this theme is not the most beginner-friendly. For further questions or commissions, feel free to join the Discord.</p><p>The following steps will help you select some base options for an easier start, but you can skip this screen if you want. You can find all these options and much more in the Fictioneer menu and the Customizer under Appearance.</p>',
          'https://github.com/Tetrakern/fictioneer/blob/main/DOCUMENTATION.md',
          'https://github.com/Tetrakern/fictioneer/blob/main/FAQ.md'
        );
      ?>

      </div>

      <div class="fictioneer-card">
        <div class="fictioneer-card__wrapper">
          <div class="fictioneer-card__content">
            <div class="fictioneer-card__row">
              <p><strong><span class="dashicons dashicons-admin-generic"></span> <?php _e( 'Enable dark mode by default?', 'fictioneer' ); ?></strong></p>
              <p><?php _e( 'The theme is set to light mode by default, but you can switch to dark mode. Visitors can override the display mode with the sun/moon icon toggle on the frontend.', 'fictioneer' ); ?></p>
            </div>
            <div class="fictioneer-card__row">
              <?php fictioneer_settings_toggle( 'fictioneer_dark_mode_as_default' ); ?>
            </div>
          </div>
        </div>
      </div>

      <h2><?php _e( 'Tips', 'fictioneer' ); ?></h2>

      <div class="fictioneer-card">
        <div class="fictioneer-card__wrapper">
          <div class="fictioneer-card__content">
            <div class="fictioneer-card__row">
              <p><span class="dashicons dashicons-lightbulb"></span> <strong><?php _e( 'Use child theme for customization.', 'fictioneer' ); ?></strong></p>
              <p><?php
                printf(
                  __( 'Child themes allow you to overwrite any part of the parent theme without actually modifying it. Otherwise, any changes you make would be removed once you update the theme. While creating a child theme is not difficult, it does require a bit of technical skill. You can start with the prepared <a href="%s" target="_blank" rel="noopener">base child theme</a>.', 'fictioneer' ),
                  'https://github.com/Tetrakern/fictioneer-child-theme'
                );
              ?></p>
            </div>
          </div>
        </div>
      </div>

    </form>
  </div>

</div>
