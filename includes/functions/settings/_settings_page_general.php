<?php
/**
 * Partial: General Settings
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.7.0
 */
?>

<div class="fictioneer-settings">

  <?php fictioneer_settings_header( 'general' ); ?>

  <div class="fictioneer-settings__content">
    <form method="post" action="options.php" class="fictioneer-form">
      <?php settings_fields( 'fictioneer-settings-general-group' ); ?>

      <div class="fictioneer-columns">

        <?php if ( fictioneer_check_for_updates() ) : ?>
          <div class="fictioneer-card">
            <div class="fictioneer-card__wrapper">
              <h3 class="fictioneer-card__header"><?php _e( 'Update', 'fictioneer' ); ?></h3>
              <div class="fictioneer-card__content">

                <div class="fictioneer-card__row">
                  <p><?php
                    printf(
                      __( '<strong>Fictioneer %1$s</strong> is available. Please <a href="%2$s" target="_blank">download</a> and install the latest version at your next convenience.', 'fictioneer' ),
                      get_option( 'fictioneer_latest_version', FICTIONEER_RELEASE_TAG ),
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
                    'fictioneer_show_authors',
                    __( 'Display authors on cards and posts', 'fictioneer' ),
                    __( 'When you have multiple publishing authors.', 'fictioneer' )
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
                    'fictioneer_show_protected_excerpt',
                    __( 'Show excerpt on password-protected posts', 'fictioneer' ),
                    __( 'This may be beneficial for search engines.', 'fictioneer' )
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
                    'fictioneer_enable_chapter_groups',
                    __( 'Enable chapter groups', 'fictioneer' ),
                    __( 'Display chapters in groups on story pages (if set).', 'fictioneer' )
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
                    'fictioneer_enable_chapter_appending',
                    __( 'Append new chapters to story', 'fictioneer' ),
                    __( 'Only once when the chapter is first saved.', 'fictioneer' )
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
                    __( 'Opens modal with timestamped chapter changes.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_hide_large_card_chapter_list',
                    __( 'Hide latest chapter list on large story cards', 'fictioneer' ),
                    __( 'Less informative but also less cluttered.', 'fictioneer' )
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
            <h3 class="fictioneer-card__header"><?php _e( 'Features', 'fictioneer' ); ?></h3>
            <div class="fictioneer-card__content">

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_storygraph_api',
                    __( 'Enable Storygraph API', 'fictioneer' ),
                    __( 'Reach a larger audience by allowing external services to index and search your stories (meta data only).', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_oauth',
                    __( 'Enable OAuth 2.0 authentication', 'fictioneer' ),
                    sprintf(
                      __( 'Register/Login with social media accounts. This requires you to <a href="%s" target="_blank">set up an application</a> for each service provider.', 'fictioneer' ),
                      'https://github.com/Tetrakern/fictioneer/blob/main/DOCUMENTATION.md#users--oauth'
                    )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_patreon_locks',
                    __( 'Enable Patreon content gate', 'fictioneer' ),
                    __( 'Requires OAuth 2.0 application for Patreon. Allows eligible logged-in members to ignore post passwords.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_lightbox',
                    __( 'Enable lightbox', 'fictioneer' ),
                    __( 'Enlarge images in floating container on click.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_bookmarks',
                    __( 'Enable Bookmarks', 'fictioneer' ),
                    __( 'Bookmark paragraphs in chapters. No account needed.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_follows',
                    __( 'Enable Follows (requires account)', 'fictioneer' ),
                    __( 'Follow stories and get on-site alerts for updates.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_checkmarks',
                    __( 'Enable Checkmarks (requires account)', 'fictioneer' ),
                    __( 'Mark chapters and stories as "read".', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_reminders',
                    __( 'Enable Reminders (requires account)', 'fictioneer' ),
                    __( 'Remember stories to be "read later".', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_suggestions',
                    __( 'Enable Suggestions', 'fictioneer' ),
                    __( 'Suggest color-coded text changes in the comments.', 'fictioneer' )
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
                    __( 'Open Graph images, chat embeds, meta tags, and schema graphs for rich snippets. Incompatible with SEO plugins.', 'fictioneer' )
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
                    __( 'Sometimes wonky browser-based text-to-speech engine. Extend and availability depends on the browser and OS.', 'fictioneer' )
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
            <h3 class="fictioneer-card__header"><?php _e( 'Comments', 'fictioneer' ); ?></h3>
            <div class="fictioneer-card__content">

              <div class="fictioneer-card__row">
                <p><?php _e( 'Some of these options will only work properly with the theme comment system enabled.', 'fictioneer' ); ?></p>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_text_input(
                    'fictioneer_comment_form_selector',
                    __( 'CSS selector for the comment form. Attempts to make scripts work with comment plugin. Clear theme cache after updating.', 'fictioneer' ),
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
                    __( 'Simple but effective spam protection.', 'fictioneer' )
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
                      __( 'Accelerate AJAX comment requests by skipping plugins and theme initialization. Actions and filters will be ignored unless applied by core or a <a href="%s" target="_blank">must-use plugin</a>.', 'fictioneer' ),
                      'https://wordpress.org/documentation/article/must-use-plugins/'
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
                    'fictioneer_enable_patreon_badges',
                    __( 'Enable Patreon badges', 'fictioneer' ),
                    sprintf(
                      __( 'Show supporter badge for the <a href="%s">linked Patreon client</a>. This only works for the owner of the linked client. You cannot add campaigns for individual authors.', 'fictioneer' ),
                      '?page=fictioneer_connections'
                    )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row fictioneer-card__row--checkbox-inset">
                <?php
                  fictioneer_settings_text_input(
                    'fictioneer_patreon_label',
                    __( 'Patreon badge label', 'fictioneer' ),
                    _x( 'Patron', 'Default Patreon supporter badge label.', 'fictioneer' )
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
                    '214px'
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
                    'fictioneer_disable_heartbeat',
                    __( 'Disable Heartbeat API', 'fictioneer' ),
                    __( 'No more continuous requests for near real-time updates and autosaves, which can strain the server.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_remove_head_clutter',
                    __( 'Remove clutter from HTML head', 'fictioneer' ),
                    __( 'Less meta tags, scripts, and styles in the &#60;head&#62;.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_remove_wp_svg_filters',
                    __( 'Remove global WordPress SVG filters', 'fictioneer' ),
                    __( 'If not used, these are just clutter in the &#60;body&#62;. This will prevent duotone filters from working.', 'fictioneer' )
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
                    'fictioneer_disable_all_widgets',
                    __( 'Disable all widgets', 'fictioneer' ),
                    __( 'The theme does not use widgets by default and removing them slightly boosts performance.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_bundle_stylesheets',
                    __( 'Bundle CSS files into one', 'fictioneer' ),
                    __( 'Faster if HTTP/2 (+) is not available to load multiple smaller files in parallel, but increases the initial payload.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_bundle_scripts',
                    __( 'Bundle JavaScript files into one', 'fictioneer' ),
                    __( 'Deferred script file that should be ignored by aggregation plugins to avoid errors (fictioneer/js/complete.min.js and fictioneer/cache/dynamic-scripts.js).', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_extended_story_list_meta_queries',
                    __( 'Disable extended story list meta queries', 'fictioneer' ),
                    __( 'Faster, but adds one row per story to your database, which can slow down your site if you have thousands.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_extended_chapter_list_meta_queries',
                    __( 'Disable extended chapter list meta queries', 'fictioneer' ),
                    __( 'Faster, but adds one row per chapter to your database, which can slow down your site if you have thousands.', 'fictioneer' )
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
                    'fictioneer_do_not_save_comment_ip',
                    __( 'Do not save comment IP addresses', 'fictioneer' ),
                    __( 'IP addresses are personal data.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_restrict_rest_api',
                    __( 'Restrict default REST API', 'fictioneer' ),
                    __( 'Disables API for guests and low-permission users.', 'fictioneer' )
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
                    )
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
                    __( 'External content not loaded until deliberately clicked.', 'fictioneer' )
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
                    __( 'Shows a generic cookie consent banner and activates the <code>fictioneer_get_consent()</code> theme function that returns either false, "necessary", or "full".', 'fictioneer' )
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
            <h3 class="fictioneer-card__header"><?php _e( 'Compatibility', 'fictioneer' ); ?></h3>
            <div class="fictioneer-card__content">

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_properties',
                    __( 'Disable Fictioneer CSS properties', 'fictioneer' ),
                    __( 'Only do this if you define everything yourself.', 'fictioneer' )
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
                    'fictioneer_enable_all_blocks',
                    __( 'Enable all Gutenberg blocks', 'fictioneer' ),
                    __( 'No guarantee these blocks work with the theme.', 'fictioneer' )
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
                    'fictioneer_disable_theme_logout',
                    __( 'Disable theme logout without nonce', 'fictioneer' ),
                    __( 'Return to the default WordPress logout with nonce.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_ajax_comment_form',
                    __( 'Enable AJAX comment form', 'fictioneer' ),
                    __( 'Load the comment form via AJAX to circumvent caching.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_ajax_comments',
                    __( 'Enable AJAX comment section', 'fictioneer' ),
                    __( 'Load the comment section and form via AJAX. More server work but circumvents caching.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_enable_ajax_authentication',
                    __( 'Enable AJAX user authentication', 'fictioneer' ),
                    __( 'Check for user login state after the page has been loaded to get around anonymizing caching strategies.', 'fictioneer' )
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

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_telegram_share',
                    __( 'Disable Telegram share button', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_disable_whatsapp_share',
                    __( 'Disable Whatsapp share button', 'fictioneer' )
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
                    __( 'This will also remove all theme-related comment and user meta data, such as bookmarks or follows. Stories, chapters, collections, and recommendations remain, but you will need to register their custom post types to access them.', 'fictioneer' )
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
</div>
