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
          '<p>Welcome and thank you for using Fictioneer!</p><p>Please make sure to read the <a href="%1$s" target="_blank" rel="noopener">installation guide</a> thoroughly. If any issues arise, refer to the <a href="%2$s" target="_blank" rel="noopener">documentation</a> and <a href="%3$s" target="_blank" rel="noopener">FAQ</a> first. If you are new to WordPress, consider looking up some guides, as this theme is not the most beginner-friendly. For further questions or commissions, feel free to join the <a href="%4$s" target="_blank" rel="noopener">Discord</a>.</p><p>The following steps will help you select some base options for an easier start, but you can skip this screen if you want. You can find all these options and much more in the <a href="%5$s" target="_blank" rel="noopener">theme settings</a> and the <a href="%6$s" target="_blank" rel="noopener">Customizer</a>.</p>',
          'https://github.com/Tetrakern/fictioneer/blob/main/INSTALLATION.md',
          'https://github.com/Tetrakern/fictioneer/blob/main/DOCUMENTATION.md',
          'https://github.com/Tetrakern/fictioneer/blob/main/FAQ.md',
          'https://discord.gg/tVfDB7EbaP',
          esc_url( admin_url( 'admin.php?page=fictioneer' ) ),
          esc_url( admin_url( 'customize.php' ) )
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

        fictioneer_settings_setup_card(
          'fictioneer_hide_large_card_chapter_list',
          __( 'Hide chapter list on large story cards?', 'fictioneer' ),
          __( 'Story cards pack a lot of information into a compact space to assist with browsing, providing everything at a glance. However, displaying the first (or latest if set) three chapters on the cards might make them appear too cluttered for your liking.', 'fictioneer' )
        );

        fictioneer_settings_setup_card(
          'fictioneer_count_characters_as_words',
          __( 'Count characters instead of words?', 'fictioneer' ),
          __( 'Counting words does not work well for logographic writing systems, so you may want to count characters instead. You can further refine this count by applying a multiplier in the theme settings.', 'fictioneer' )
        );

        fictioneer_settings_setup_card(
          '',
          __( 'Enable theme lightbox for images?', 'fictioneer' ),
          __( '', 'fictioneer' )
        );

        fictioneer_settings_setup_card(
          '',
          __( 'Enable bookmarks for chapter paragraphs?', 'fictioneer' ),
          __( '', 'fictioneer' )
        );

        fictioneer_settings_setup_card(
          '',
          __( 'Enable logged-in users to follow stories?', 'fictioneer' ),
          __( '', 'fictioneer' )
        );

        fictioneer_settings_setup_card(
          '',
          __( 'Enable logged-in users to set reminders for stories?', 'fictioneer' ),
          __( '', 'fictioneer' )
        );

        fictioneer_settings_setup_card(
          '',
          __( 'Enable logged-in users to track read chapters/stories with checkmarks?', 'fictioneer' ),
          __( '', 'fictioneer' )
        );

        fictioneer_settings_setup_card(
          '',
          __( 'Enable suggestions in comments for chapters?', 'fictioneer' ),
          __( '', 'fictioneer' )
        );

        fictioneer_settings_setup_card(
          '',
          __( '', 'fictioneer' ),
          __( '', 'fictioneer' )
        );

        fictioneer_settings_setup_card(
          '',
          __( '', 'fictioneer' ),
          __( '', 'fictioneer' )
        );

        fictioneer_settings_setup_card(
          '',
          __( '', 'fictioneer' ),
          __( '', 'fictioneer' )
        );

        fictioneer_settings_setup_card(
          '',
          __( '', 'fictioneer' ),
          __( '', 'fictioneer' )
        );

        fictioneer_settings_setup_card(
          '',
          __( '', 'fictioneer' ),
          __( '', 'fictioneer' )
        );

        fictioneer_settings_setup_card(
          '',
          __( '', 'fictioneer' ),
          __( '', 'fictioneer' )
        );

      ?>

      <div class="fictioneer-actions"><?php submit_button( __( 'Submit', 'fictioneer' ) ); ?></div>

      <h2><?php _e( 'Tips', 'fictioneer' ); ?></h2>

      <div class="fictioneer-card">
        <div class="fictioneer-card__wrapper">
          <div class="fictioneer-card__content">
            <div class="fictioneer-card__row">
              <p class="fictioneer-card__row-heading"><span class="dashicons dashicons-lightbulb"></span> <?php _e( 'Use a child theme for customization.', 'fictioneer' ); ?></p>
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

      <div class="fictioneer-card">
        <div class="fictioneer-card__wrapper">
          <div class="fictioneer-card__content">
            <div class="fictioneer-card__row">
              <p class="fictioneer-card__row-heading"><span class="dashicons dashicons-lightbulb"></span> <?php _e( 'Use constants for additional customization.', 'fictioneer' ); ?></p>
              <p><?php
                printf(
                  __( 'Fictioneer offers even more options beyond what is accessible in the settings! However, tampering with some of these options can break the theme or cause unexpected behavior. This is why they are only defined as PHP constants. You can change them in a child theme as described in the <a href="%s" target="_blank" rel="noopener">installation guide</a>.', 'fictioneer' ),
                  'https://github.com/Tetrakern/fictioneer/blob/main/INSTALLATION.md#constants'
                );
              ?></p>
            </div>
          </div>
        </div>
      </div>

      <div class="fictioneer-card">
        <div class="fictioneer-card__wrapper">
          <div class="fictioneer-card__content">
            <div class="fictioneer-card__row">
              <p class="fictioneer-card__row-heading"><span class="dashicons dashicons-lightbulb"></span> <?php _e( 'Set up a static page with shortcodes as the home page.', 'fictioneer' ); ?></p>
              <p><?php
                printf(
                  __( 'By default, newly installed WordPress sites display the blog index as the "home" page. You can change this by going to Settings > Reading and selecting a static home and blog page instead, which you need to create first. If you want to replicate the demo site layout, the process is explained in the <a href="%s" target="_blank" rel="noopener">installation guide</a>.', 'fictioneer' ),
                  'https://github.com/Tetrakern/fictioneer/blob/main/INSTALLATION.md#demo-layout'
                );
              ?></p>
            </div>
          </div>
        </div>
      </div>

    </form>
  </div>

</div>
