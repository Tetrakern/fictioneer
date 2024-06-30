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
    <form action="<?php echo esc_url( fictioneer_admin_action( 'fictioneer_after_install_setup' ) ); ?>" method="post" class="fictioneer-single-column fictioneer-single-column--setup">

      <h1><?php _e( 'Fictioneer Setup', 'fictioneer' ); ?></h1>

      <div><?php
        printf(
          '<p>Welcome and thank you for using Fictioneer!</p><p>Please make sure to read the installation guide thoroughly. If any issues arise, refer to the <a href="%1$s" target="_blank" rel="noopener">documentation</a> and <a href="%1$s" target="_blank" rel="noopener">FAQ</a> first. If you are new to WordPress, consider looking up some guides, as this theme is not the most beginner-friendly. For further questions or commissions, feel free to join the Discord.</p><p>The following steps will help you select some base options for an easier start, but you can skip this screen if you want. You can find all these options and much more in the Fictioneer menu and the Customizer under Appearance.</p>',
          'https://github.com/Tetrakern/fictioneer/blob/main/DOCUMENTATION.md',
          'https://github.com/Tetrakern/fictioneer/blob/main/FAQ.md'
        );
      ?>

      </div>

      <?php

        fictioneer_settings_setup_card(
          'fictioneer_dark_mode_as_default',
          __( 'Enable dark mode by default?', 'fictioneer' ),
          __( 'The theme is set to light mode by default, but you can switch to dark mode. Visitors can override the display mode with the sun/moon icon toggle on the frontend.', 'fictioneer' )
        );

        fictioneer_settings_setup_card(
          'fictioneer_show_authors',
          __( 'Display authors on cards and posts?', 'fictioneer' ),
          __( 'Fictioneer was primarily developed for single authors to publish their web serials, and therefore omits the author to save space. However, if you have multiple authors writing on your site, you can choose to display their names.', 'fictioneer' )
        );

        fictioneer_settings_setup_card(
          'fictioneer_enable_chapter_groups',
          __( 'Enable chapter groups?', 'fictioneer' ),
          __( 'You can group chapters into separate sections, for example, to better compartmentalize story arcs. Groups are defined as verbatim strings set per chapter — they are case-sensitive and must match exactly. Groups will only show up if you have more than one, and any chapters without a group will be collected under "Unassigned".', 'fictioneer' )
        );

        fictioneer_settings_setup_card(
          'fictioneer_disable_default_formatting_indent',
          __( 'Disable default indentation of chapter paragraphs?', 'fictioneer' ),
          __( 'Paragraphs in chapters have a first-line indentation by default to guide the reading eye. However, you may find this unaesthetic and prefer to disable it. Note that readers can still enable indentation through the chapter formatting modal.', 'fictioneer' )
        );

        // fictioneer_settings_setup_card(
        //   '',
        //   __( '', 'fictioneer' ),
        //   __( '', 'fictioneer' )
        // );

      ?>

      <div class="fictioneer-actions"><?php submit_button( __( 'Submit', 'fictioneer' ) ); ?></div>

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
