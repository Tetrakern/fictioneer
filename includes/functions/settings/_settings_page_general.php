<?php
/**
 * Partial: General Settings
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.7
 */
?>

<div class="fictioneer-settings">

	<?php fictioneer_settings_header( 'general' ); ?>

	<div class="fictioneer-settings__content">
    <form method="post" action="options.php" class="fictioneer-form">
      <?php settings_fields( 'fictioneer-settings-general-group' ); ?>
      <?php do_settings_sections( 'fictioneer-settings-general-group' ); ?>

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
                  fictioneer_label_checkbox(
                    'fictioneer_enable_maintenance_mode',
                    __( 'Locks down the site with a maintenance notice.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_light_mode_as_default',
                    __( 'Overridden by personal site settings.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_show_authors',
                    __( 'When you have multiple publishing authors.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_show_full_post_content',
                    __( 'You can still use the [More] block for shortened previews.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_disable_contact_forms',
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
                <?php fictioneer_text_input( 'fictioneer_system_email_address', 'email' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_text_input( 'fictioneer_system_email_name' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_textarea( 'fictioneer_contact_email_addresses', '205px' ); ?>
              </div>

            </div>
          </div>
        </div>

        <div class="fictioneer-card">
          <div class="fictioneer-card__wrapper">
            <h3 class="fictioneer-card__header"><?php _e( 'Chapters & Stories', 'fictioneer' ); ?></h3>
            <div class="fictioneer-card__content">

              <div class="fictioneer-card__row">
                <p><?php _e( 'Updating these settings may require the story caches to be purged. You can do that under Tools.', 'fictioneer' ); ?></p>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_hide_chapter_icons',
                    __( 'Hides the icons on story pages and in the mobile menu.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_chapter_groups',
                    __( 'Display chapters in groups on story pages (if set).', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_disable_chapter_collapsing',
                    __( 'Do not collapse long chapter lists.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_chapter_appending',
                    __( 'Only once when the chapter is first saved.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_limit_chapter_stories_by_author',
                    __( 'Also disables cross-posting as guest author.', 'fictioneer' )
                  );
                ?>
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
                <?php fictioneer_label_checkbox( 'fictioneer_hide_taxonomies_on_story_cards' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_label_checkbox( 'fictioneer_hide_taxonomies_on_chapter_cards' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_label_checkbox( 'fictioneer_hide_taxonomies_on_recommendation_cards' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_label_checkbox( 'fictioneer_hide_taxonomies_on_collection_cards' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <p><?php _e( 'Tags are normally not shown on cards since they tend to be numerous, making the cards unsightly. But this is up to you.', 'fictioneer' ); ?></p>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_label_checkbox( 'fictioneer_show_tags_on_story_cards' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_label_checkbox( 'fictioneer_show_tags_on_chapter_cards' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_label_checkbox( 'fictioneer_show_tags_on_recommendation_cards' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_label_checkbox( 'fictioneer_show_tags_on_collection_cards' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <p><?php _e( 'Taxonomies on pages are displayed above the title, tags and content warnings get their own rows below the description.', 'fictioneer' ); ?></p>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_label_checkbox( 'fictioneer_hide_taxonomies_on_pages' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_label_checkbox( 'fictioneer_hide_tags_on_pages' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_label_checkbox( 'fictioneer_hide_content_warnings_on_pages' ); ?>
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
                <?php fictioneer_page_assignment( 'fictioneer_user_profile_page' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_page_assignment( 'fictioneer_bookmarks_page' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_page_assignment( 'fictioneer_stories_page' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_page_assignment( 'fictioneer_chapters_page' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_page_assignment( 'fictioneer_recommendations_page' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_page_assignment( 'fictioneer_collections_page' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_page_assignment( 'fictioneer_bookshelf_page' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_page_assignment( 'fictioneer_404_page' ); ?>
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
                  fictioneer_label_checkbox(
                    'fictioneer_enable_storygraph_api',
                    __( 'Reach a larger audience by allowing external services to index and search your stories (meta data only).', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_oauth',
                    sprintf(
                      __( 'Register/Login with social media accounts. This requires you to <a href="%s" target="_blank">set up an application</a> for each service provider.', 'fictioneer' ),
                      'https://github.com/Tetrakern/fictioneer/blob/main/DOCUMENTATION.md#users--oauth'
                    )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_lightbox',
                    __( 'Enlarge images in floating container on click.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_bookmarks',
                    __( 'Bookmark paragraphs in chapters. No account needed.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_follows',
                    __( 'Follow stories and get on-site alerts for updates.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_checkmarks',
                    __( 'Mark chapters and stories as "read".', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_reminders',
                    __( 'Remember stories to be "read later".', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_suggestions',
                    __( 'Suggest color-coded text changes in the comments.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_theme_rss',
                    __( 'Replace the default WordPress RSS feeds with theme feeds built for publishing your stories and chapters.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_seo',
                    __( 'Open Graph images, chat embeds, meta tags, and schema graphs for rich snippets. Incompatible with SEO plugins.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_sitemap',
                    __( 'Customized sitemap for search engines to crawl. Respects unlisted flags and custom post types.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_tts',
                    __( 'Sometimes wonky browser-based text-to-speech engine. Extend and availability depends on the browser and OS.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_epubs',
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
                  fictioneer_label_checkbox(
                    'fictioneer_disable_commenting',
                    __( 'Still shows the current comments.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_require_js_to_comment',
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
                  fictioneer_label_checkbox(
                    'fictioneer_enable_ajax_comment_submit',
                    __( 'Does not reload the page on submit.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_ajax_comment_moderation',
                    __( 'Moderation actions directly in the comment section.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_fast_ajax_comments',
                    sprintf(
                      __( 'Accelerate AJAX comment requests by skipping plugins and theme initialization. Actions and filters will be ignored unless applied by core or a <a href="%s" target="_blank">must-use plugin</a>.', 'fictioneer' ),
                      'https://wordpress.org/documentation/article/must-use-plugins/'
                    )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_comment_toolbar',
                    __( 'Quick buttons to wrap text selection in BBCodes.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_custom_badges',
                    __( 'Assign custom badges (but users can disable them).', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_patreon_badges',
                    sprintf(
                      __( 'Show supporter badge for the <a href="%s">linked Patreon client</a>. This only works for the owner of the linked client. You cannot add campaigns for individual authors.', 'fictioneer' ),
                      '?page=fictioneer_connections'
                    )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row fictioneer-card__row--checkbox-inset">
                <?php fictioneer_text_input( 'fictioneer_patreon_label' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_comment_notifications',
                    __( 'Notify commenters about replies via email (if provided).', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_sticky_comments',
                    __( 'Always on top of the first page.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_private_commenting',
                    __( 'Private comments are invisible to the public.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_comment_reporting',
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
                <?php fictioneer_textarea( 'fictioneer_comments_notice', '158px' ); ?>
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
                  fictioneer_label_checkbox(
                    'fictioneer_disable_heartbeat',
                    __( 'No more continuous requests for near real-time updates and autosaves, which can strain the server.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_remove_head_clutter',
                    __( 'Less meta tags, scripts, and styles in the &#60;head&#62;.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_remove_wp_svg_filters',
                    __( 'If not used, these are just clutter in the &#60;body&#62;. This will prevent duotone filters from working.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_reduce_admin_bar',
                    __( 'Less menu items, links, and icons in the admin bar.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_disable_all_widgets',
                    __( 'The theme does not use widgets by default and removing them slightly boosts performance.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_bundle_stylesheets',
                    __( 'Faster if HTTP/2 (+) is not available to load multiple smaller files in parallel, but increases the initial payload.', 'fictioneer' )
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
                  fictioneer_label_checkbox(
                    'fictioneer_do_not_save_comment_ip',
                    __( 'IP addresses are personal data.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_restrict_rest_api',
                    __( 'Disables API for guests and low-permission users.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_rate_limits',
                    sprintf(
                      __( 'Simple session-based rate limiting, allowing %s requests per minute for selected actions (per action).', 'fictioneer' ),
                      FICTIONEER_REQUESTS_PER_MINUTE
                    )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_disable_application_passwords',
                    __( 'If you do not need them, you should disable them.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_logout_redirects_home',
                    __( 'Prevents users from ending up on the login page.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_consent_wrappers',
                    __( 'External content not loaded until deliberately clicked.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_see_some_evil',
                    __( 'Sends an admin email if suspicious strings are found, which could be an attempted attack or <strong>false positive.</strong>', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_cookie_banner',
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
                    FICTIONEER_OPTIONS['integers']['fictioneer_upload_size_limit']['label'],
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
                  FICTIONEER_OPTIONS['strings']['fictioneer_upload_mime_types']['label'],
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
                  fictioneer_label_checkbox(
                    'fictioneer_disable_properties',
                    __( 'Only do this if you define everything yourself.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_jquery_migrate',
                    __( 'Some older plugins might require this.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_purge_all_caches',
                    __( 'Inefficient but makes sure everything is up-to-date.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_cache_compatibility',
                    __( 'Make the theme aware of unknown caching plugins.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_public_cache_compatibility',
                    __( 'For serving public caches to logged-in users. Exceptions for administrators and moderators are recommended.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_private_cache_compatibility',
                    __( 'For serving private caches to logged-in users. Also useful if you do not serve caches to logged-in users at all.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_all_blocks',
                    __( 'No guarantee these blocks work with the theme.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_all_block_styles',
                    __( 'This might interfere with theme styles.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_disable_theme_logout',
                    __( 'Return to the default WordPress logout with nonce.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_ajax_comment_form',
                    __( 'Load the comment form via AJAX to circumvent caching.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_ajax_comments',
                    __( 'Load the comment section and form via AJAX. More server work but circumvents caching.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_enable_ajax_authentication',
                    __( 'Check for user login state after the page has been loaded to get around anonymizing caching strategies.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_disable_theme_search',
                    __( 'Return to the default search query and form.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_disable_comment_bbcodes',
                    __( 'This will disable the comment toolbar as well.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_disable_comment_callback',
                    __( 'Return to the default WordPress markup.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_disable_comment_query',
                    __( 'Return to the default WordPress query.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_disable_comment_form',
                    __( 'Return to the default WordPress form.', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_label_checkbox(
                    'fictioneer_disable_comment_pagination',
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
                <?php fictioneer_label_checkbox( 'fictioneer_disable_facebook_share' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_label_checkbox( 'fictioneer_disable_twitter_share' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_label_checkbox( 'fictioneer_disable_tumblr_share' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_label_checkbox( 'fictioneer_disable_reddit_share' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_label_checkbox( 'fictioneer_disable_mastodon_share' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_label_checkbox( 'fictioneer_disable_telegram_share' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_label_checkbox( 'fictioneer_disable_whatsapp_share' ); ?>
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
                  fictioneer_label_checkbox(
                    'fictioneer_delete_theme_options_on_deactivation',
                    __( 'This will also remove all additional user meta data, such as bookmarks or Follows. Stories, chapters, collections, and recommendations remain but you will need to register their custom post types in order to access them.', 'fictioneer' )
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
