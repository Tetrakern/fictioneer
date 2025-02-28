<?php
/**
 * Partial: General Settings
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.7.0
 */


// Setup
$issues = fictioneer_look_for_issues();
$images = get_template_directory_uri() . '/img/documentation/';

?>

<div class="fictioneer-settings">

  <?php fictioneer_settings_header( 'general' ); ?>

  <div class="fictioneer-settings__content">
    <form method="post" action="options.php" class="fictioneer-form">
      <?php settings_fields( 'fictioneer-settings-general-group' ); ?>

      <div class="fictioneer-columns">

        <?php if ( ! empty( $issues ) ) : ?>
          <div class="fictioneer-card fictioneer-card--issues">
            <div class="fictioneer-card__wrapper">
              <h3 class="fictioneer-card__header"><?php _e( 'Potential Issues', 'fictioneer' ); ?></h3>
              <div class="fictioneer-card__content">

                <div class="fictioneer-card__row fictioneer-card__row--issues">
                  <ul><?php
                    foreach ( $issues as $issue ) {
                      echo "<li>{$issue}</li>";
                    }
                  ?></ul>
                </div>

              </div>
            </div>
          </div>
        <?php endif; ?>

        <?php if ( fictioneer_check_for_updates() ) : ?>
          <div class="fictioneer-card">
            <div class="fictioneer-card__wrapper">
              <h3 class="fictioneer-card__header"><?php _e( 'Update', 'fictioneer' ); ?></h3>
              <div class="fictioneer-card__content">

                <div class="fictioneer-card__row">
                  <p><?php
                    $theme_info = fictioneer_get_theme_info();

                    printf(
                      __( '<strong>Fictioneer %1$s</strong> is available. Please <a href="%2$s" target="_blank">download</a> and install the latest version at your next convenience.', 'fictioneer' ),
                      $theme_info['last_update_version'],
                      'https://github.com/Tetrakern/fictioneer/releases'
                    );
                  ?></p>
                </div>

              </div>
            </div>
          </div>
        <?php endif; ?>

        <div class="fictioneer-card">
          <div class="fictioneer-card__wrapper">
            <h3 class="fictioneer-card__header"><?php _e( 'General Settings', 'fictioneer' ); ?></h3>
            <div class="fictioneer-card__content">

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_maintenance_mode',
                    __( 'Enable maintenance mode', 'fictioneer' ),
                    __( 'Locks down the site with a maintenance notice.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_dark_mode_as_default',
                    __( 'Enable dark mode as default', 'fictioneer' ),
                    __( 'Overridden by personal site settings.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_site_age_confirmation',
                    __( 'Enable age confirmation modal for site', 'fictioneer' ),
                    __( 'Require age confirmation for the whole site.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_contact_forms',
                    __( 'Disable theme contact forms', 'fictioneer' ),
                    __( 'Emergency stop with an error notice.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <p><?php
                  printf(
                    __( 'Compact <a href="%s" target="_blank">date formats</a>. Some page items, such as story cards, use more compact date formats than the general setting of WordPress. Space is an issue here.', 'fictioneer' ),
                    'https://wordpress.org/support/article/formatting-date-and-time/'
                  );
                ?></p>
              </div>

              <div class="fictioneer-card__row fictioneer-card__row--inline-input">
                <p class="fictioneer-inline-text-input"><?php
                  printf(
                    __( '<span>Use</span> %s <span>for long date formats (<code>M j, \'y</code>).</span>', 'fictioneer' ),
                    '<input name="fictioneer_subitem_date_format" type="text" id="fictioneer_subitem_date_format" value="' . esc_attr( get_option( 'fictioneer_subitem_date_format', "M j, 'y" ) ?: "M j, 'y" ) . '" style="font-family: Consolas, Monaco, monospace; font-size: 87.5%; text-align: center;" size="8" placeholder="M j, \'y">'
                  );
                ?></p>
              </div>

              <div class="fictioneer-card__row fictioneer-card__row--inline-input">
                <p class="fictioneer-inline-text-input"><?php
                  printf(
                    __( '<span>Use</span> %s <span>for short date formats (<code>M j</code>).</span>', 'fictioneer' ),
                    '<input name="fictioneer_subitem_short_date_format" type="text" id="fictioneer_subitem_short_date_format" value="' . esc_attr( get_option( 'fictioneer_subitem_short_date_format', 'M j' ) ) . '" style="font-family: Consolas, Monaco, monospace; font-size: 87.5%; text-align: center;" size="8" placeholder="M j">'
                  );
                ?></p>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_text_input(
                    'fictioneer_system_email_address',
                    __( 'System email address', 'fictioneer' ),
                    'noreply@example.com',
                    'email'
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_text_input(
                    'fictioneer_system_email_name',
                    __( 'System email name', 'fictioneer' ),
                    get_bloginfo( 'name' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_textarea(
                    'fictioneer_contact_email_addresses',
                    __( 'Contact form receivers (one email address per line)', 'fictioneer' ),
                    '205px'
                  );
                ?>
              </div>

            </div>
          </div>
        </div>

        <div class="fictioneer-card">
          <div class="fictioneer-card__wrapper">
            <h3 class="fictioneer-card__header"><?php _e( 'Tweaks', 'fictioneer' ); ?></h3>
            <div class="fictioneer-card__content">

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_show_authors',
                    __( 'Display authors on cards and posts', 'fictioneer' ),
                    __( 'When you have multiple publishing authors.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_hide_categories',
                    __( 'Hide categories on posts', 'fictioneer' ),
                    __( 'If you do not need them anyway.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_show_full_post_content',
                    __( 'Display full posts instead of excerpts', 'fictioneer' ),
                    __( 'You can still use the [More] block for shortened previews.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_show_protected_excerpt',
                    __( 'Show excerpt on password-protected posts', 'fictioneer' ),
                    __( 'This may be beneficial for search engines.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_exclude_protected_from_rss',
                    __( 'Exclude password-protected posts from RSS', 'fictioneer' ),
                    __( 'Only works if the theme RSS feeds are enabled.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_exclude_non_stories_from_cloud_counts',
                    __( 'Only count stories in taxonomy clouds', 'fictioneer' ),
                    __( 'Taxonomy pages still list all posts in the results.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_all_blocks',
                    __( 'Enable all Gutenberg blocks', 'fictioneer' ),
                    __( 'No guarantee these blocks work with the theme.', 'fictioneer' ),
                    __( 'Many blocks in the editor are hidden by default due to compatibility concerns. The theme is not built for full-site editing, and blocks like Row, Stack, Group, along with widgets and content items, may lack styling or not work as expected.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_advanced_meta_fields',
                    __( 'Enable advanced meta fields', 'fictioneer' ),
                    __( 'Additional options you most likely do not need.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_custom_fields',
                    __( 'Enable custom fields', 'fictioneer' ),
                    __( 'Show custom fields form on post edit screen.', 'fictioneer' )
                  );
                ?>
              </div>

            </div>
          </div>
        </div>

        <div class="fictioneer-card">
          <div class="fictioneer-card__wrapper">
            <h3 class="fictioneer-card__header"><?php _e( 'Features', 'fictioneer' ); ?></h3>
            <div class="fictioneer-card__content">

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_storygraph_api',
                    __( 'Enable Storygraph API', 'fictioneer' ),
                    __( 'Reach a larger audience by allowing external services to index and search your stories (metadata only).', 'fictioneer' ),
                    sprintf(
                      __( '<p>With this feature enabled, the <a href="%s" target="_blank">Storygraph API</a> provides endpoints to query excerpts and metadata of your public stories and chapters, but not their content. This allows others to aggregate indexes for filtering and searching across multiple sites, like a distributed archive, while you remain in control.</p><p>However, enabling this feature does not guarantee that someone will immediately make use of it. This only provides the possibility.</p>', 'fictioneer' ),
                      'https://github.com/Tetrakern/fictioneer/blob/main/API.md'
                    ),
                    '<div class="helper-modal-image"><img src="' . $images . 'fictioneer_enable_storygraph_api.jpg"></div>'
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_oauth',
                    __( 'Enable OAuth 2.0 authentication', 'fictioneer' ),
                    sprintf(
                      __( 'Register/Login with an external account. This requires you to <a href="%s" target="_blank">set up an application</a> with the respective service providers.', 'fictioneer' ),
                      'https://github.com/Tetrakern/fictioneer/blob/main/DOCUMENTATION.md#users--oauth'
                    ),
                    sprintf(
                      __( '<p>With this feature enabled, users can skip the default login form and authenticate with an external account, such as Discord or Google. This method is fast, secure, convenient, and enables other features such as the Patreon gate. Users can still change their email address and nickname in their profile afterwards</p><p>You need to <a href="%s" target="_blank">set up an application</a> with the chosen providers, which is normally easy. Remember to flush your permalink structure by saving <strong>Settings > Permalinks</strong> when you toggle this setting to avoid 404s.</p>', 'fictioneer' ),
                      'https://github.com/Tetrakern/fictioneer/blob/main/INSTALLATION.md#connections-tab'
                    ),
                    '<div class="helper-modal-image"><img src="' . $images . 'fictioneer_enable_oauth.jpg"></div>'
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_patreon_locks',
                    __( 'Enable Patreon content gate', 'fictioneer' ),
                    __( 'Requires OAuth 2.0 application with Patreon. Allows patrons to ignore passwords for selected posts.', 'fictioneer' ),
                    __( '<p>With this feature enabled, users authenticated via Patreon OAuth can access protected posts without the password. Just assign the tiers or a pledge threshold, per post or globally.</p><p>Requires an OAuth 2.0 application with Patreon. Users retain their access for a week by default, after which they need to re-login to refresh their membership data.</p>', 'fictioneer' ),
                    '<div class="helper-modal-image"><img src="' . $images . 'fictioneer_enable_patreon_locks.jpg"></div>'
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_lightbox',
                    __( 'Enable lightbox', 'fictioneer' ),
                    __( 'Enlarge images in floating container on click.', 'fictioneer' ),
                    __( 'With this feature enabled, clicking on images in the page content will open a window overlay popup. This blocks some content and dims and disables the background to present the image more prominently.', 'fictioneer' ),
                    '<div class="helper-modal-image"><img src="' . $images . 'fictioneer_enable_lightbox.jpg"></div>'
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_generate_footnotes_from_tooltips',
                    __( 'Enable footnotes from tooltips', 'fictioneer' ),
                    __( 'Generate and show footnotes at the end of the post content based on the tooltips.', 'fictioneer' ),
                    __( '<p>With this feature enabled, tooltips added with the <code>[fcnt]text[/fcnt]</code> shortcode are collected and appended to the content as footnotes, complete with anchor links.</p>', 'fictioneer' ),
                    '<code class="helper-modal-code">' .
                    _x(
                      '[fcnt header="Optional" content="Tooltip content."]text[/fcnt]',
                      'Settings helper modal tooltip shortcode figure.',
                    'fictioneer' ) .
                    '</code>'
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_bookmarks',
                    __( 'Enable Bookmarks', 'fictioneer' ),
                    __( 'Bookmark paragraphs in chapters. No account needed.', 'fictioneer' ),
                    sprintf(
                      __( '<p>With this feature enabled, users can click on chapter paragraphs to set a bookmark, which is stored in the local browser. Logged-in users will also benefit from bookmark synchronization across browsers and devices, though an account is not necessary for basic functionality.</p><p>While the mobile menu has a panel for bookmarks, for the desktop view you will need to set up a page with the Bookmarks template or add the <code>fictioneer_bookmarks</code> shortcode to another page. Please refer to the <a href="%s" target="_blank">documentation</a> on GitHub for more information.</p>', 'fictioneer' ),
                      'https://github.com/Tetrakern/fictioneer/blob/main/DOCUMENTATION.md'
                    ),
                    '<div class="helper-modal-image"><img src="' . $images . 'fictioneer_enable_bookmarks.jpg"></div>'
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_follows',
                    __( 'Enable Follows (requires account)', 'fictioneer' ),
                    __( 'Follow stories and get on-site alerts for updates.', 'fictioneer' ),
                    __( 'With this feature enabled, logged-in users can "follow" a story to receive on-site update notifications and track the story in their bookshelf (if set up). This feature is more useful if you host many ongoing stories. Otherwise, you may want to save server resources by keeping it disabled.', 'fictioneer' ),
                    '<div class="helper-modal-image"><img src="' . $images . 'fictioneer_enable_follows.jpg"></div>'
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_reminders',
                    __( 'Enable Reminders (requires account)', 'fictioneer' ),
                    __( 'Remember stories to be "read later".', 'fictioneer' ),
                    __( 'With this feature enabled, logged-in users can set reminders for stories to keep track of them for a later date in their bookshelf (if set up). This feature is more useful if you host many stories. Otherwise, you may want to save server resources by keeping it disabled.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_checkmarks',
                    __( 'Enable Checkmarks (requires account)', 'fictioneer' ),
                    __( 'Mark chapters and stories as "read".', 'fictioneer' ),
                    __( 'With this feature enabled, logged-in users can mark chapters and stories as "read" and keep track of them in their bookshelf (if set up). This feature is more useful if you host many stories. Otherwise, you may want to save server resources by keeping it disabled.', 'fictioneer' ),
                    '<div class="helper-modal-image"><img src="' . $images . 'fictioneer_enable_checkmarks.jpg"></div>'
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_css_skins',
                    __( 'Enable CSS skins (requires account)', 'fictioneer' ),
                    __( 'Allows logged-in users to apply custom styles.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_suggestions',
                    __( 'Enable Suggestions', 'fictioneer' ),
                    __( 'Suggest color-coded text changes in the comments.', 'fictioneer' ),
                    __( 'With this feature enabled, users can leave color-coded suggestions for improvements in the comments. To do so, they can simply click on a paragraph or highlight some text, then click the button to open the suggestion modal, make changes, and append the result to the comment form.', 'fictioneer' ),
                    '<div class="helper-modal-image"><img src="' . $images . 'fictioneer_enable_suggestions.jpg"></div>'
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_theme_rss',
                    __( 'Enable theme RSS feeds and buttons', 'fictioneer' ),
                    __( 'Replace the default WordPress RSS feeds with theme feeds built for publishing your stories and chapters.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_seo',
                    __( 'Enable SEO features', 'fictioneer' ),
                    __( 'Open Graph images, chat embeds, meta tags, and schema graphs for rich snippets. Incompatible with SEO plugins.', 'fictioneer' ),
                    sprintf(
                      __( 'With this feature enabled, the editor displays a meta box where you can enter a title, <a href="%s" target="_blank" rel="noopener">description</a>, and image. The theme will then render relevant meta tags used by search engines, social media sites, and chat embeds. Whether these services actually use the provided data is ultimately up to them.', 'fictioneer' ),
                      'https://developers.google.com/search/docs/appearance/snippet'
                    ),
                    '<div class="helper-modal-image"><img src="' . $images . 'fictioneer_enable_seo.jpg"></div>'
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_sitemap',
                    __( 'Enable theme sitemap generation', 'fictioneer' ),
                    __( 'Customized sitemap for search engines to crawl. Respects unlisted flags and custom post types. Can be slow.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_tts',
                    __( 'Enable Text-To-Speech (experimental)', 'fictioneer' ),
                    __( 'Sometimes wonky browser-based text-to-speech engine. Extend and availability depends on the browser and OS.', 'fictioneer' ),
                    sprintf(
                      __( '<p>With this feature enabled, users can click on a paragraph to engage the browser’s <a href="%s" target="_blank">Web Speech API</a> — an experimental tool that is not a replacement for a screen reader. The interface bar with controls and options will be displayed at the bottom.</p><p>Whether this works depends on the browser and operating system; additional system permissions and settings may be required. The script should automatically select a voice model that fits the site language, but manual selection is possible in the settings.</p>', 'fictioneer' ),
                      'https://developer.mozilla.org/en-US/docs/Web/API/Web_Speech_API'
                    ),
                    '<div class="helper-modal-image"><img src="' . $images . 'fictioneer_enable_tts.jpg"></div>'
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_epubs',
                    __( 'Enable ePUB converter (experimental)', 'fictioneer' ),
                    __( 'Automatically generated ePUBs. Can take a while and may fail due to non-conform content or excessive size. You can alternatively upload manually created ebooks as well.', 'fictioneer' )
                  );
                ?>
              </div>

            </div>
          </div>
        </div>

        <div class="fictioneer-card">
          <div class="fictioneer-card__wrapper">
            <h3 class="fictioneer-card__header"><?php _e( 'Page Assignments', 'fictioneer' ); ?></h3>
            <div class="fictioneer-card__content">

              <div class="fictioneer-card__row">
                <p><?php _e( 'The theme expects certain pages to exist. Just create a normal page, assign the specified page template, and select it here. Clear caches afterwards.', 'fictioneer' ); ?></p>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_page_assignment(
                    'fictioneer_user_profile_page',
                    __( 'Account page (Template: User Profile) &bull; Do not cache!', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_page_assignment(
                    'fictioneer_bookmarks_page',
                    __( 'Bookmarks page (Template: Bookmarks)', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_page_assignment(
                    'fictioneer_stories_page',
                    __( 'Stories page (Template: Stories)', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_page_assignment(
                    'fictioneer_chapters_page',
                    __( 'Chapters page (Template: Chapters)', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_page_assignment(
                    'fictioneer_recommendations_page',
                    __( 'Recommendations page (Template: Recommendations)', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_page_assignment(
                    'fictioneer_collections_page',
                    __( 'Collections page (Template: Collections)', 'fictioneer' ),
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_page_assignment(
                    'fictioneer_authors_page',
                    __( 'Author index page (Template: Author Index)', 'fictioneer' ),
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_page_assignment(
                    'fictioneer_bookshelf_page',
                    __( 'Bookshelf page (Template: Bookshelf) &bull; Do not cache!', 'fictioneer' ),
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_page_assignment(
                    'fictioneer_404_page',
                  __( '404 page &bull; Add content to your 404 page, make it useful!', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <p><?php _e( '<strong>Note:</strong> Some of these pages are highly dynamic, such as the account and bookshelf templates. The former must never be cached. The latter can be cached if you apply the AJAX template. Private caching (e.g. one per user) would work for both if you have the disk space.', 'fictioneer' ); ?></p>
              </div>

            </div>
          </div>
        </div>

        <div class="fictioneer-card">
          <div class="fictioneer-card__wrapper">
            <h3 class="fictioneer-card__header"><?php _e( 'Social Media' ); ?></h3>
            <div class="fictioneer-card__content">

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_facebook_share',
                    __( 'Disable Facebook share button', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_bluesky_share',
                    __( 'Disable Bluesky share button', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_twitter_share',
                    __( 'Disable Twitter share button', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_tumblr_share',
                    __( 'Disable Tumblr share button', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_reddit_share',
                    __( 'Disable Reddit share button', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_mastodon_share',
                    __( 'Disable Mastodon share button', 'fictioneer' )
                  );
                ?>
              </div>

            </div>
          </div>
        </div>

        <div class="fictioneer-card">
          <div class="fictioneer-card__wrapper">
            <h3 class="fictioneer-card__header"><?php _e( 'Chapters & Stories', 'fictioneer' ); ?></h3>
            <div class="fictioneer-card__content">

              <div class="fictioneer-card__row">
                <p><?php _e( 'Updating these settings may require the theme caches to be purged. You can do that under Tools.', 'fictioneer' ); ?></p>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_post_age_confirmation',
                    __( 'Enable age confirmation modal for posts', 'fictioneer' ),
                    __( 'Require age confirmation for <b>adult-rated</b> content.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_line_break_fix',
                    __( 'Enable fixing of chapter paragraphs', 'fictioneer' ),
                    __( 'If you messed up copy/paste and all paragraphs are one.', 'fictioneer' ),
                    __( 'If you copy and paste chapters into the editor, particularly the classic editor, paragraphs may be lost. This occurs because WordPress uses double new lines to separate content into paragraphs. If your content only has single new lines, you will get a single line break instead of a paragraph. While it is better to fix this in the content, this option can smooth things out. However, keep in mind that single line breaks will no longer be possible.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_hide_chapter_icons',
                    __( 'Hide chapter icons', 'fictioneer' ),
                    __( 'Hides the icons on story pages and in the mobile menu.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_override_chapter_status_icons',
                    __( 'Override chapter status icons', 'fictioneer' ),
                    __( 'Always renders the chapter icons instead of locks/etc.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_chapter_groups',
                    __( 'Enable chapter groups', 'fictioneer' ),
                    __( 'Renders chapters in groups on story pages (if set).', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_collapse_groups_by_default',
                    __( 'Collapse chapter groups by default', 'fictioneer' ),
                    __( 'Chapter groups (if used) are collapsed on page load.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_chapter_collapsing',
                    __( 'Disable collapsing of chapters', 'fictioneer' ),
                    __( 'Do not collapse long chapter lists.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_default_formatting_indent',
                    __( 'Disable default indentation of chapter paragraphs', 'fictioneer' ),
                    __( 'This can still be overwritten by the user.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_chapter_appending',
                    __( 'Append new chapters to story', 'fictioneer' ),
                    __( 'Whenever you change the chapter story and save.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_redirect_scheduled_chapter_404',
                    __( 'Redirect scheduled chapters to story or home', 'fictioneer' ),
                    __( 'Without user permission, they show a 404 error page.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_limit_chapter_stories_by_author',
                    __( 'Restrict chapter stories by author', 'fictioneer' ),
                    __( 'Also disables cross-posting as guest author.', 'fictioneer' )
                  );
                ?>
              </div>

              <?php
                $changelog_classes = '';

                if ( ! FICTIONEER_ENABLE_STORY_CHANGELOG ) {
                  $changelog_classes = 'fictioneer-card__row--disabled';
                }
              ?>

              <div class="fictioneer-card__row <?php echo $changelog_classes; ?>">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_show_story_changelog',
                    __( 'Show story changelog button', 'fictioneer' ),
                    __( 'Opens modal with timestamped chapter changes.', 'fictioneer' ),
                    __( 'Adds a small clock button at the bottom-left of the story chapter list, which opens a modal with a changelog. This is primarily intended for sites with user-contributed content to help expose any manipulation of update listings, such as removing and re-adding chapters. The author cannot to edit this log.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_show_story_modified_date',
                    __( 'Show the modified date on story pages', 'fictioneer' ),
                    __( 'Below the chapter list and instead of the publish date.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_hide_large_card_chapter_list',
                    __( 'Hide chapter list on large story cards', 'fictioneer' ),
                    __( 'Less informative but also less cluttered.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_show_story_cards_latest_chapters',
                    __( 'Show latest chapters on large story cards', 'fictioneer' ),
                    __( 'Instead of the earliest ones to avoid spoilers.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_count_characters_as_words',
                    __( 'Count characters instead of words', 'fictioneer' ),
                    __( 'For logographic writing systems. Use the word count multiplier to better approximate the number.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row fictioneer-card__row--inline-input">
                <p class="fictioneer-inline-text-input"><?php
                  printf(
                    __( '<span>Multiply the displayed word counts with</span> %s<span>.</span>', 'fictioneer' ),
                    '<input name="fictioneer_word_count_multiplier" type="text" id="fictioneer_word_count_multiplier" value="' . esc_attr( get_option( 'fictioneer_word_count_multiplier', 1.0 ) ) . '" style="text-align: center;" size="4" placeholder="1.0">'
                  );
                ?></p>
              </div>

              <div class="fictioneer-card__row fictioneer-card__row--inline-input">
                <p class="fictioneer-inline-text-input"><?php
                  printf(
                    __( '<span>Calculate reading time with</span> %s <span>words per minute.</span>', 'fictioneer' ),
                    '<input name="fictioneer_words_per_minute" type="text" id="fictioneer_words_per_minute" value="' . esc_attr( get_option( 'fictioneer_words_per_minute', 200 ) ) . '" style="text-align: center;" size="4" placeholder="200">'
                  );
                ?></p>
              </div>

            </div>
          </div>
        </div>

        <div class="fictioneer-card">
          <div class="fictioneer-card__wrapper">
            <h3 class="fictioneer-card__header"><?php _e( 'Tags & Taxonomies', 'fictioneer' ); ?></h3>
            <div class="fictioneer-card__content">

              <div class="fictioneer-card__row">
                <p><?php _e( 'Taxonomies on cards include fandoms, genres, tags (if shown), and characters in that order.', 'fictioneer' ); ?></p>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_hide_taxonomies_on_story_cards',
                    __( 'Hide taxonomies on story cards', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_hide_taxonomies_on_chapter_cards',
                    __( 'Hide taxonomies on chapter cards', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_hide_taxonomies_on_recommendation_cards',
                    __( 'Hide taxonomies on recommendation cards', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_hide_taxonomies_on_collection_cards',
                    __( 'Hide taxonomies on collection cards', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <p><?php _e( 'Tags are normally not shown on cards since they tend to be numerous, making the cards unsightly. But this is up to you.', 'fictioneer' ); ?></p>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_show_tags_on_story_cards',
                    __( 'Show tags on story cards', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_show_tags_on_chapter_cards',
                    __( 'Show tags on chapter cards', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_show_tags_on_recommendation_cards',
                    __( 'Show tags on recommendation cards', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_show_tags_on_collection_cards',
                    __( 'Show tags on collection cards', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <p><?php _e( 'Taxonomies on pages are displayed above the title, tags and content warnings get their own rows below the description.', 'fictioneer' ); ?></p>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_hide_taxonomies_on_pages',
                    __( 'Hide taxonomies on pages', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_hide_tags_on_pages',
                    __( 'Hide tags on pages', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_hide_content_warnings_on_pages',
                    __( 'Hide content warnings on pages', 'fictioneer' )
                  );
                ?>
              </div>

            </div>
          </div>
        </div>

        <div class="fictioneer-card">
          <div class="fictioneer-card__wrapper">
            <h3 class="fictioneer-card__header"><?php _e( 'Comments', 'fictioneer' ); ?></h3>
            <div class="fictioneer-card__content">

              <div class="fictioneer-card__row">
                <p><?php _e( 'Some of these options will only work properly with the theme comment system enabled.', 'fictioneer' ); ?></p>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_text_input(
                    'fictioneer_comment_form_selector',
                    __( 'CSS selector for the comment form. Attempts to make scripts work with a comment plugin. Clear theme cache after updating.', 'fictioneer' ),
                    '#comment'
                );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_commenting',
                    __( 'Disable commenting across the site', 'fictioneer' ),
                    __( 'Still shows the current comments.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_require_js_to_comment',
                    __( 'Require JavaScript to comment', 'fictioneer' ),
                    __( 'Simple but effective spam protection.', 'fictioneer' ),
                    __( 'Most spam bots run on minimal resources because operating them costs time and money. This is why they usually do not run JavaScript when attacking a site — it is too expensive, and your site likely is not worth the extra effort. Therefore, making JavaScript a requirement typically stops all spam until you become a more attractive target or someone specifically targets you.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <label class="fictioneer-label-checkbox" for="fictioneer_enable_comment_link_limit">
                  <input name="fictioneer_enable_comment_link_limit" type="checkbox" id="fictioneer_enable_comment_link_limit" <?php echo checked( 1, get_option( 'fictioneer_enable_comment_link_limit' ), false ); ?> value="1">
                  <div>
                    <span><?php
                      printf(
                        __( '<span>Limit comments to</span> %s <span>link(s) or less</span>', 'fictioneer' ),
                        '<input name="fictioneer_comment_link_limit_threshold" type="text" id="fictioneer_comment_link_limit_threshold" value="' . esc_attr( get_option( 'fictioneer_comment_link_limit_threshold', 3 ) ) . '" style="text-align: center;" size="3" placeholder="3">'
                      );
                    ?></span>
                    <p class="fictioneer-sub-label"><?php _e( 'Reliable spam protection but can lead to false positives.', 'fictioneer' ); ?></p>
                  </div>
                </label>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_ajax_comment_submit',
                    __( 'Enable AJAX comment submission', 'fictioneer' ),
                    __( 'Does not reload the page on submit.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_ajax_comment_moderation',
                    __( 'Enable AJAX comment moderation', 'fictioneer' ),
                    __( 'Moderation actions directly in the comment section.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_fast_ajax_comments',
                    __( 'Enable fast AJAX for comments', 'fictioneer' ),
                    sprintf(
                      __( 'Accelerate comments by skipping most plugins. Requires the <a href="%s" target="_blank">Fast Requests</a> mu-plugin to be installed.', 'fictioneer' ),
                      'https://github.com/Tetrakern/fictioneer/blob/main/INSTALLATION.md#recommended-must-use-plugins'
                    )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_comment_toolbar',
                    __( 'Enable comment toolbar', 'fictioneer' ),
                    __( 'Quick buttons to wrap text selection in BBCodes.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_custom_badges',
                    __( 'Enable custom badges', 'fictioneer' ),
                    __( 'Assign custom badges (but users can disable them).', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_comment_notifications',
                    __( 'Enable comment reply notifications', 'fictioneer' ),
                    __( 'Notify commenters about replies via email (if provided).', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_sticky_comments',
                    __( 'Enable sticky comments', 'fictioneer' ),
                    __( 'Always on top of the first page.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_private_commenting',
                    __( 'Enable private commenting', 'fictioneer' ),
                    __( 'Private comments are invisible to the public.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_comment_reporting',
                    __( 'Enable comment reporting', 'fictioneer' ),
                    __( 'Logged-in users can report comments via flag button.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <label class="fictioneer-label-checkbox" for="fictioneer_enable_user_comment_editing">
                  <input name="fictioneer_enable_user_comment_editing" type="checkbox" id="fictioneer_enable_user_comment_editing" <?php echo checked( 1, get_option( 'fictioneer_enable_user_comment_editing' ), false ); ?> value="1">
                  <div>
                    <span><?php
                      printf(
                        __( '<span>Enable comment editing for</span> %s <span>minute(s)</span>', 'fictioneer' ),
                        '<input name="fictioneer_user_comment_edit_time" type="text" id="fictioneer_user_comment_edit_time" value="' . esc_attr( get_option( 'fictioneer_user_comment_edit_time', 15 ) ) . '" style="text-align: center;" size="3" placeholder="15">'
                      );
                    ?></span>
                    <p class="fictioneer-sub-label"><?php _e( 'Logged-in users can edit their comments for a number of minutes; enter -1 for no time limit.', 'fictioneer' ); ?></p>
                  </div>
                </label>
              </div>

              <div class="fictioneer-card__row fictioneer-card__row--inline-input">
                <p class="fictioneer-inline-text-input"><?php
                  printf(
                    __( '<span>Automatically moderate comments with</span> %s <span>reports</span>. This will only trigger once but the comments will remain hidden unless set to ignore reports.', 'fictioneer' ),
                    '<input name="fictioneer_comment_report_threshold" type="text" id="fictioneer_comment_report_threshold" value="' . esc_attr( get_option( 'fictioneer_comment_report_threshold', 10 ) ) . '" style="text-align: center;" size="3" placeholder="10">'
                  );
                ?></p>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_textarea(
                    'fictioneer_comments_notice',
                    __( 'Notice above comments. Leave empty to hide. HTML allowed.', 'fictioneer' ),
                    '152px'
                  );
                ?>
              </div>

            </div>
          </div>
        </div>

        <div class="fictioneer-card">
          <div class="fictioneer-card__wrapper">
            <h3 class="fictioneer-card__header"><?php _e( 'Performance', 'fictioneer' ); ?></h3>
            <div class="fictioneer-card__content">

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_ffcnr_auth',
                    __( 'Enable FFCNR user authentication', 'fictioneer' ),
                    sprintf(
                      __( 'Significantly faster frontend user setup, but might not work in some cases — <a href="%s" target="_blank" rel="noopener">test it here</a>. Not safe for multi-sites.', 'fictioneer' ),
                      fictioneer_get_ffcnr_url() . '?action=test'
                    ),
                    sprintf(
                      __( 'FFCNR stands for <em>Fast Fictioneer Requests</em>, an alternative entry point to WordPress with minimal environment. In this mode, almost nothing is loaded: no themes, no plugins, and only a tiny subset of WordPress itself. This reduced environment significantly improves performance for certain tasks, such as fetching user data for the frontend. Refer to the <a href="%s" target="_blank" rel="noopener noreferrer">installation guide</a> for details on interacting with FFCNR.', 'fictioneer' ),
                      'https://github.com/Tetrakern/fictioneer/blob/main/INSTALLATION.md#ffcnr-fast-fictioneer-requests'
                    )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_heartbeat',
                    __( 'Disable Heartbeat API', 'fictioneer' ),
                    __( 'No more continuous requests for near real-time updates and autosaves, which can strain the server.', 'fictioneer' ),
                    sprintf(
                      __( 'The <a href="%s" target="_blank" rel="noopener noreferrer">Heartbeat API</a> polls the server every 15-120 seconds for updates, increasing CPU usage and impacting performance. Fictioneer does not use it by default, but some plugins do. When enabled, it allows for near real-time notifications, periodic auto-saves, user activity tracking, eCommerce sales updates, and more.', 'fictioneer' ),
                      'https://developer.wordpress.org/plugins/javascript/heartbeat-api/'
                    )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_remove_head_clutter',
                    __( 'Remove clutter from HTML head', 'fictioneer' ),
                    __( 'Less meta tags, scripts, and styles in the &#60;head&#62;.', 'fictioneer' ),
                    __( 'By default, WordPress adds many tags of dubious utility to the site’s head. Not only does this needlessly impact performance, but it may also leave your site vulnerable to attacks. This setting removes the RSD link (for blog clients), the wlwmanifest.xml (for Windows Live Writer), and the generator tag (version number).', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_emojis',
                    __( 'Disable WordPress emojis', 'fictioneer' ),
                    __( 'Removes superfluous CSS and JavaScript.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_reduce_admin_bar',
                    __( 'Reduce admin bar items', 'fictioneer' ),
                    __( 'Less menu items, links, and icons in the admin bar.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_bundle_stylesheets',
                    __( 'Bundle CSS files into one', 'fictioneer' ),
                    __( 'Faster if HTTP/2 (+) is not available to load multiple smaller files in parallel, but increases the initial payload.', 'fictioneer' ),
                    __( 'Modern browsers and servers can handle multiple stylesheets in parallel, eliminating the need to bundle them into one large file. This allows the theme to selectively load styles as needed. Unless you are sure of what you are doing, it is best to leave this setting off.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_bundle_scripts',
                    __( 'Bundle JavaScript files into one', 'fictioneer' ),
                    __( 'Deferred script file that should be ignored by aggregation plugins to avoid errors (fictioneer/js/complete.min.js and fictioneer/cache/dynamic-scripts.js).', 'fictioneer' ),
                    __( 'Modern browsers and servers can handle multiple scripts in parallel, eliminating the need to bundle them into one large file. This allows the theme to selectively load scripts as needed. Unless you are sure of what you are doing, it is best to leave this setting off.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_extended_story_list_meta_queries',
                    __( 'Disable extended story list meta queries', 'fictioneer' ),
                    __( 'Faster, but adds one row per story to your database, which can slow down your site if you have thousands.', 'fictioneer' ),
                    __( 'Unless you are a developer customizing the theme, leave this setting off. Extended meta queries check not only for the value of meta fields but also their existence, as Fictioneer does not keep "falsy" values in the database. If you disable this, all stories will have the missing meta fields added and exempted from cleanup.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_extended_chapter_list_meta_queries',
                    __( 'Disable extended chapter list meta queries', 'fictioneer' ),
                    __( 'Faster, but adds one row per chapter to your database, which can slow down your site if you have thousands.', 'fictioneer' ),
                    __( 'Unless you are a developer customizing the theme, leave this setting off. Extended meta queries check not only for the value of meta fields but also their existence, as Fictioneer does not keep "falsy" values in the database. If you disable this, all chapters will have the missing meta fields added and exempted from cleanup.', 'fictioneer' )
                  );
                ?>
              </div>

            </div>
          </div>
        </div>

        <div class="fictioneer-card">
          <div class="fictioneer-card__wrapper">
            <h3 class="fictioneer-card__header"><?php _e( 'Security & Privacy', 'fictioneer' ); ?></h3>
            <div class="fictioneer-card__content">

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_show_wp_login_link',
                    __( 'Show default WordPress login in modal', 'fictioneer' ),
                    __( 'The default login/registration form is not spam safe.', 'fictioneer' ),
                    __( 'Fictioneer has been built for OAuth 2.0 authentication with external accounts to avoid the vulnerable WordPress login/registration form. However, if you want, you can link it in the login modal regardless.', 'fictioneer' ),
                    '<div class="helper-modal-image"><img src="' . $images . 'fictioneer_show_wp_login_link.jpg"></div>'
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_randomize_oauth_usernames',
                    __( 'Randomize OAuth 2.0 usernames', 'fictioneer' ),
                    __( 'Some providers use real names. Default for Google.', 'fictioneer' ),
                    __( 'Users registered with an external account normally get the username of said account, with suffix if necessary. But some providers use real names, such as Google, and that can be a privacy concern. Since the display name is independent of the username, assigning a randomly generated one is a simple solution.', 'fictioneer' ),
                    '<div class="helper-modal-image"><img src="' . $images . 'fictioneer_randomize_oauth_usernames.jpg"></div>'
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_do_not_save_comment_ip',
                    __( 'Do not save comment IP addresses', 'fictioneer' ),
                    __( 'IP addresses are personal data.', 'fictioneer' ),
                    __( 'Saving IP addresses when visitors leave comments can conflict with data privacy laws, depending on how the IP addresses are used and associated with other data. Given the sensitivity of this topic, it is generally better to avoid saving IP addresses.', 'fictioneer' ),
                    '<div class="helper-modal-image"><img src="' . $images . 'fictioneer_do_not_save_comment_ip.jpg"></div>'
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_log_posts',
                    __( 'Log all post updates', 'fictioneer' ),
                    __( 'If you want to track any and all changes.', 'fictioneer' ),
                    'Enable this option if you want to track every action made to any post: publishing, updating, trashing, and deleting. This may be useful if you have concerns about content changes, but it is not recommended for sites with a high throughput of posts.',
                    '<div class="helper-modal-image"><img src="' . $images . 'fictioneer_log_posts.jpg"></div>'
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_restrict_rest_api',
                    __( 'Restrict default REST API', 'fictioneer' ),
                    __( 'Disables API for guests and low-permission users.', 'fictioneer' ),
                    sprintf(
                      __( 'The <a href="%s" target="_blank" rel="noopener">WordPress REST API</a> has many endpoints available even to unauthenticated users, such as <code>/wp-json/wp/v2/posts</code>, which returns all blog posts on your site. This also applies to stories, chapters, and so forth. Since the response includes the full post content if it is not protected, you may want to restrict access to hamper content theft by aggregator sites.', 'fictioneer' ),
                      'https://learn.wordpress.org/tutorial/using-the-wordpress-rest-api/'
                    )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_rate_limits',
                    __( 'Enable rate limiting for AJAX requests', 'fictioneer' ),
                    sprintf(
                      __( 'Simple session-based rate limiting, allowing %s requests per minute for selected actions (per action).', 'fictioneer' ),
                      FICTIONEER_REQUESTS_PER_MINUTE
                    ),
                    __( 'Some frontend actions, such as following a story or setting a chapter checkmark, send small requests for processing. If someone were to spam these actions, it could cause stress on the server. By enabling the rate limit, these actions are restricted to a sensible number of requests per minute and blocked if the limit is exceeded.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_application_passwords',
                    __( 'Disable application passwords', 'fictioneer' ),
                    __( 'If you do not need them, you should disable them.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_logout_redirects_home',
                    __( 'Logout redirects Home', 'fictioneer' ),
                    __( 'Prevents users from ending up on the login page.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_consent_wrappers',
                    __( 'Add consent wrappers to embedded content', 'fictioneer' ),
                    __( 'External content not loaded until deliberately clicked.', 'fictioneer' ),
                    __( 'Embedding external content, such as YouTube videos, automatically loads scripts from the source, which can violate data privacy laws by including tracking, advertising, analytics, and more. With the consent wrappers enabled, embeds are quarantined until deliberately clicked, informing the visitor about the third-party consent requirements.', 'fictioneer' ),
                    '<div class="helper-modal-image"><img src="' . $images . 'fictioneer_consent_wrappers.jpg"></div>'
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_see_some_evil',
                    __( 'Monitor posts for suspicious content', 'fictioneer' ),
                    __( 'Sends an admin email if suspicious strings are found, which could be an attempted attack or <strong>false positive.</strong>', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_cookie_banner',
                    __( 'Enable cookie banner and consent function', 'fictioneer' ),
                    __( 'Shows a generic cookie consent banner and activates the <code>fictioneer_get_consent()</code> theme function.', 'fictioneer' ),
                    __( 'Cookie banners are often necessary to comply with data privacy laws and meet visitor expectations. It is better to be safe than sorry, even if the theme does not require one in its base configuration. Enabling this also activates the <code>fictioneer_get_consent()</code> function, which returns either <code>false</code>, <code>"necessary"</code>, or <code>"full"</code>.', 'fictioneer' )
                  );
                ?>
              </div>

            </div>
          </div>
        </div>

        <div class="fictioneer-card">
          <div class="fictioneer-card__wrapper">
            <h3 class="fictioneer-card__header"><?php _e( 'File Uploads', 'fictioneer' ); ?></h3>
            <div class="fictioneer-card__content">

              <div class="fictioneer-card__row fictioneer-card__row--inline-input">
                <p class="fictioneer-inline-text-input"><?php
                  printf(
                    __( '<span>Limit file uploads to</span> %s <span>MB or less for user roles with the "Upload Limit" restriction.</span>', 'fictioneer' ),
                    '<input name="fictioneer_upload_size_limit" type="text" id="fictioneer_upload_size_limit" value="' . esc_attr( get_option( 'fictioneer_upload_size_limit', 5 ) ?: 5 ) . '" style="font-family: Consolas, Monaco, monospace; font-size: 87.5%; text-align: center;" size="5" placeholder="5">'
                  );
                ?></p>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  $mime_types = get_option( 'fictioneer_upload_mime_types', FICTIONEER_DEFAULT_UPLOAD_MIME_TYPE_RESTRICTIONS )
                    ?: FICTIONEER_DEFAULT_UPLOAD_MIME_TYPE_RESTRICTIONS;
                ?>
                <textarea class="fictioneer-textarea" name="fictioneer_upload_mime_types" id="fictioneer_upload_mime_types" rows="4" style="height: 100px;" placeholder="<?php echo FICTIONEER_DEFAULT_UPLOAD_MIME_TYPE_RESTRICTIONS; ?>"><?php echo $mime_types; ?></textarea>
                <p class="fictioneer-sub-label"><?php printf(
                  __( 'Comma-separated list of allowed <a href="%s" target="_blank" rel="noreferrer">mime types</a> for user roles with the "Upload Restriction". Must be among the allowed mime type and file extensions of WordPress.', 'fictioneer' ),
                  'https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types/Common_types'
                ); ?></p>
              </div>

            </div>
          </div>
        </div>

        <div class="fictioneer-card">
          <div class="fictioneer-card__wrapper">
            <h3 class="fictioneer-card__header"><?php _e( 'Caching', 'fictioneer' ); ?></h3>
            <div class="fictioneer-card__content">

              <?php
                $active_cache_plugins = fictioneer_get_active_cache_plugins();

                if ( $active_cache_plugins ) {
                  echo '<div class="fictioneer-card__row fictioneer-card__row--boxed">';
                  echo '<h4>' . __( 'Active Known Cache Plugins', 'fictioneer' ) . '</h4><p>';
                  echo implode( ', ', $active_cache_plugins );
                  echo '</p></div>';
                }
              ?>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_static_partials',
                    __( 'Enable caching of partials', 'fictioneer' ),
                    __( 'Caches parts of the page as static HTML files to speed up loading. Do not use this together with a cache plugin.', 'fictioneer' ),
                    __( '<p>With this feature enabled, the theme caches parts of the page that never change as static HTML files, pulling them up on subsequent requests to save resources. Unlike full caching, this only affects some modals, menus, and chapter contents. Use this only if cache plugins are not an option.</p><p>Plugins that change the content of chapters may not work without further adjustments. If you need to apply dynamic chapters content, which are only refreshed on update or when the cache expires, you can still do so with the <code>fictioneer_filter_static_content</code> filter.</p><p>Administrators can disable the content caching per chapter.</p>', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_story_card_caching',
                    __( 'Enable caching of story cards', 'fictioneer' ),
                    sprintf(
                      __( 'Caches the latest %d story cards in the database to speed up loading. May or may not work with cache plugins.', 'fictioneer' ),
                      FICTIONEER_CARD_CACHE_LIMIT
                    ),
                    __( '<p>With this feature enabled, the HTML of the last rendered story cards is cached in the database, mitigating the slowdown caused by their complex queries. The more cards you display at once, the slower the page would typically load, but caching helps to speed up the loading times for the most recent stories.</p><p>You can use the <code>FICTIONEER_CARD_CACHE_LIMIT</code> constant to change the number of cached cards (default is 40). Be aware that increasing this number will result in higher RAM consumption.</p>', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_lastpostmodified_caching',
                    __( 'Cache last post modified date as Transient', 'fictioneer' ),
                    __( 'The responsible query can be extremely slow.', 'fictioneer' ),
                    sprintf(
                      __( '<p>The query responsible for <a href="%s" target="_blank">finding the last modified post date</a> can be extremely slow if you have a large database, potentially causing your site to crash under heavy traffic.</p><p>While persistent object caching, if available, should prevent this, that has proven unreliable. This solution caches the date string in a simple database row for 5 minutes, which is a fast and safe approach.</p>', 'fictioneer' ),
                      'https://developer.wordpress.org/reference/functions/_get_last_post_time/'
                    )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_purge_all_caches',
                    __( 'Purge all caches on content updates', 'fictioneer' ),
                    __( 'Inefficient but makes sure everything is up-to-date.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_cache_compatibility',
                    __( 'Enable cache compatibility mode', 'fictioneer' ),
                    __( 'Make the theme aware of unknown caching plugins.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_public_cache_compatibility',
                    __( 'Enable public cache compatibility mode', 'fictioneer' ),
                    __( 'For serving public caches to logged-in users. Exceptions for administrators and moderators are recommended.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_private_cache_compatibility',
                    __( 'Enable private cache compatibility mode', 'fictioneer' ),
                    __( 'For serving private caches to logged-in users. Also useful if you do not serve caches to logged-in users at all.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_menu_transients',
                    __( 'Disable menu Transient caching', 'fictioneer' ),
                    __( 'Only do this if you render dynamic content in the menus.', 'fictioneer' ),
                    __( 'Compiling navigation menus is an extensive operation that involves multiple database queries. If you are not displaying dynamic content, such as points or other constantly changing data, it is best to cache the menus to avoid repeating the same queries for identical results.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_chapter_list_transients',
                    __( 'Disable chapter list Transient caching', 'fictioneer' ),
                    __( 'Only do this if lists on story pages are outdated.', 'fictioneer' ),
                    __( 'Compiling grouped chapter lists on story pages can place significant strain on your server, especially if you have hundreds of chapters per story that all need to be queried. Only disable this if your stories are small (fewer than 50 chapters) or if you have another solution, such as a caching plugin.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_shortcode_transients',
                    __( 'Disable shortcode Transient caching', 'fictioneer' ),
                    __( 'Only do this if shortcodes are not properly updated.', 'fictioneer' ),
                    __( 'Shortcodes rely on complex queries with many optional parameters, which can be slow and resource-intensive if you use too many or have a very large database. Caching them in Transients for a period (default is 300 seconds) significantly reduces server strain but may conflict with other caching solutions.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_ajax_comment_form',
                    __( 'Enable AJAX comment form', 'fictioneer' ),
                    __( 'Load the comment form via AJAX to circumvent caching.', 'fictioneer' ),
                    __( 'The AJAX comment form avoids conflicts with caching, especially issues with dynamic security tokens that are unique to each user. By loading the form after the page has fully loaded, it circumvents the cache, though it may require more server resources.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_ajax_comments',
                    __( 'Enable AJAX comment section', 'fictioneer' ),
                    __( 'Load the comment section and form via AJAX. More server work but circumvents caching.', 'fictioneer' ),
                    __( 'The AJAX comment section avoids conflicts with caching, including the AJAX comment form, ensuring that comments are always up-to-date. By loading the comments after the page has fully loaded, it circumvents the cache, though it may require more server resources.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_ajax_authentication',
                    __( 'Enable AJAX user authentication', 'fictioneer' ),
                    __( 'Check for user login state after the page has been loaded to get around anonymizing caching strategies.', 'fictioneer' ),
                    __( 'This is a <em>last-resort measure</em> for aggressive caching strategies, such as options to serve public caches to logged-in users. It queries for the user’s login state after the page has loaded. This requires more server resources, but can help when everything else fails.', 'fictioneer' )
                  );
                ?>
              </div>

            </div>
          </div>
        </div>

        <div class="fictioneer-card">
          <div class="fictioneer-card__wrapper">
            <h3 class="fictioneer-card__header"><?php _e( 'Compatibility', 'fictioneer' ); ?></h3>
            <div class="fictioneer-card__content">

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_header_image_preload',
                    __( 'Disable preloading of header image', 'fictioneer' ),
                    __( 'Not by default compatible with all image CDNs.', 'fictioneer' ),
                    __( 'Preloading the header image can speed up your LCP time (Largest Contentful Paint), resulting in a better user experience and search rank. However, this typically clashes with image CDNs because the preload URL can differ from the CDN-provided URL, resulting in an incorrect preload, making your site actually slower.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_allow_rest_save_actions',
                    __( 'Allow REST requests to trigger save actions', 'fictioneer' ),
                    __( 'They are blocked because they are redundant.', 'fictioneer' ),
                    __( '<p>When you save in the editor, the theme runs many actions to update everything related — stories, lists, shortcodes, caches, and so forth. Due to how the Gutenberg editor works, these actions are triggered multiple times, which can slow down the saving process. To improve performance, some actions are blocked.</p><p>However, if you are adding or editing content via the REST API, you may need these actions to fire to ensure that related posts are properly updated. If that’s the case, enable this setting.</p>', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_rewrite_chapter_permalinks',
                    __( 'Rewrite chapter permalinks to include story', 'fictioneer' ),
                    __( 'Becomes <code>/story/story-slug/chapter-slug[-n]</code>. You must flush your permalinks and purge the theme caches.', 'fictioneer' ),
                    __( 'Stories and chapters are separate posts that are associated but not hierarchically linked. As a result, the permalink of a chapter does not include the story by default. Enabling this setting adds an alias to include the story in the chapter’s permalink. However, chapter slugs must still be globally unique, and a number will be appended in case of a conflict (e.g. <code>.../story/story-slug/chapter-slug-1</code>).', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_anti_flicker',
                    __( 'Enable anti-flicker script', 'fictioneer' ),
                    __( 'Hides the page until the document is fully parsed.', 'fictioneer' ),
                    __( '<p>Rendering of the site begins while the HTML document is still being parsed, from top to bottom. This can cause the layout to flicker and shift for a split second if styles are applied to grouped elements that are still missing their counterparts. Not great, not terrible.</p><p>This script prevents such flickers by making the site invisible until the entire document has been parsed, which means the site technically appears a split second later. However, if the script is blocked, the site will remain invisible. This is extremely unlikely, though.</p>', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_global_splide',
                    __( 'Enable Splide slider globally', 'fictioneer' ),
                    __( 'Instead of limiting it to shortcodes.', 'fictioneer' ),
                    sprintf(
                      __( 'Normally, the <a href="%s" target="_blank" rel="noopener noreferrer">Splide slider</a> script and style are only loaded when a shortcode with the required parameter is found in the post content. However, if you want to use Splide in other ways, you can enable the assets globally here.', 'fictioneer' ),
                      'https://splidejs.com/guides/options/'
                    )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_properties',
                    __( 'Disable Fictioneer CSS properties', 'fictioneer' ),
                    __( 'Only do this if you define everything yourself.', 'fictioneer' ),
                    __( 'If you do not understand what this option does, you have no business enabling it. This is generally a poor life choice.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_font_awesome',
                    __( 'Disable Font Awesome integration', 'fictioneer' ),
                    __( 'Only do this if you integrate it yourself.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_jquery_migrate',
                    __( 'Enable jQuery migrate script', 'fictioneer' ),
                    __( 'Some older plugins might require this.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_xmlrpc',
                    __( 'Enable XML-RPC', 'fictioneer' ),
                    __( 'Security risk. Only enable this if you actually need it.', 'fictioneer' ),
                    __( 'XML-RPC allows remote applications to interact with your site but can be a security risk, enabling brute-force and DDoS attacks. This is why it is disabled by default.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_theme_logout',
                    __( 'Disable theme logout without nonce', 'fictioneer' ),
                    __( 'Return to the default WordPress logout with nonce.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_theme_search',
                    __( 'Disable advanced search', 'fictioneer' ),
                    __( 'Return to the default search query and form.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_comment_bbcodes',
                    __( 'Disable comment BBCodes', 'fictioneer' ),
                    __( 'This will disable the comment toolbar as well.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_comment_callback',
                    __( 'Disable theme comment style (callback)', 'fictioneer' ),
                    __( 'Return to the default WordPress markup.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_comment_query',
                    __( 'Disable theme comment query', 'fictioneer' ),
                    __( 'Return to the default WordPress query.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_comment_form',
                    __( 'Disable theme comment form', 'fictioneer' ),
                    __( 'Return to the default WordPress form.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_comment_pagination',
                    __( 'Disable theme comment pagination', 'fictioneer' ),
                    __( 'Return to the default WordPress pagination (no-AJAX).', 'fictioneer' )
                  );
                ?>
              </div>

            </div>
          </div>
        </div>

        <?php do_action( 'fictioneer_admin_settings_general' ); ?>

        <div class="fictioneer-card">
          <div class="fictioneer-card__wrapper">
            <h3 class="fictioneer-card__header"><?php _e( 'Deactivation (DANGER ZONE)', 'fictioneer' ); ?></h3>
            <div class="fictioneer-card__content">

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_delete_theme_options_on_deactivation',
                    __( 'Delete all settings and theme mods on deactivation', 'fictioneer' ),
                    __( 'This will also remove all theme-related comment and user metadata, such as bookmarks or follows. Stories, chapters, collections, and recommendations remain, but you will need to register their custom post types to access them.', 'fictioneer' )
                  );
                ?>
              </div>

            </div>
          </div>
        </div>

      </div>

      <div class="fictioneer-actions"><?php submit_button(); ?></div>

    </form>
  </div>

  <dialog class="fictioneer-dialog" id="fcn-help-modal">
    <div class="fictioneer-dialog__header">
      <span data-target="fcn-help-modal-header"><?php _e( 'Help', 'fictioneer' ); ?></span>
    </div>
    <div class="fictioneer-dialog__content">
      <form>
        <div class="fictioneer-dialog__row" data-target="fcn-help-modal-content"></div>
        <div class="fictioneer-dialog__actions">
          <button value="cancel" formmethod="dialog" class="button"><?php _e( 'Close', 'fictioneer' ); ?></button>
        </div>
      </form>
    </div>
  </dialog>
</div>
