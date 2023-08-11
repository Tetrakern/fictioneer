<?php
/**
 * Partial: General Settings
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.7
 */
?>

<div class="fictioneer-ui fictioneer-settings">

	<?php fictioneer_settings_header( 'general' ); ?>

	<div class="fictioneer-settings__content">

    <div class="tab-content">
      <form method="post" action="options.php" class="form">
        <?php settings_fields( 'fictioneer-settings-general-group' ); ?>
        <?php do_settings_sections( 'fictioneer-settings-general-group' ); ?>

        <div class="columns-layout">

          <?php if ( fictioneer_check_for_updates() ) : ?>
            <div class="card">
              <div class="card-wrapper">
                <h3 class="card-header"><?php _e( 'Update', 'fictioneer' ) ?></h3>
                <div class="card-content">

                  <p class="description row"><?php
                    printf(
                      __( '<strong>Fictioneer %1$s</strong> is available. Please <a href="%2$s" target="_blank">download</a> and install the latest version at your next convenience.', 'fictioneer' ),
                      get_option( 'fictioneer_latest_version', FICTIONEER_RELEASE_TAG ),
                      'https://github.com/Tetrakern/fictioneer/releases'
                    );
                  ?></p>

                </div>
              </div>
            </div>
          <?php endif; ?>

    			<div class="card">
    				<div class="card-wrapper">
    					<h3 class="card-header"><?php _e( 'General Settings', 'fictioneer' ) ?></h3>
    					<div class="card-content">

  							<label class="label-wrapped-checkbox row" for="fictioneer_enable_maintenance_mode">
                  <input name="fictioneer_enable_maintenance_mode" type="checkbox" id="fictioneer_enable_maintenance_mode" <?php echo checked( 1, get_option( 'fictioneer_enable_maintenance_mode' ), false ); ?> value="1">
                  <div>
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_maintenance_mode']['label']; ?></span>
                    <p class="sub-label"><?php _e( 'Locks down the site with a maintenance notice.', 'fictioneer' ) ?></p>
                  </div>
                </label>

  							<label class="label-wrapped-checkbox row" for="fictioneer_light_mode_as_default">
                  <input name="fictioneer_light_mode_as_default" type="checkbox" id="fictioneer_light_mode_as_default" <?php echo checked( 1, get_option( 'fictioneer_light_mode_as_default' ), false ); ?> value="1">
                  <div>
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_light_mode_as_default']['label']; ?></span>
                    <p class="sub-label"><?php _e( 'Overridden by personal site settings.', 'fictioneer' ) ?></p>
                  </div>
                </label>

                <label class="label-wrapped-checkbox row" for="fictioneer_show_authors">
                  <input name="fictioneer_show_authors" type="checkbox" id="fictioneer_show_authors" <?php echo checked( 1, get_option( 'fictioneer_show_authors' ), false ); ?> value="1">
                  <div>
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_show_authors']['label']; ?></span>
                    <p class="sub-label"><?php _e( 'When you have multiple publishing authors.', 'fictioneer' ) ?></p>
                  </div>
                </label>

                <label class="label-wrapped-checkbox row" for="fictioneer_show_full_post_content">
                  <input name="fictioneer_show_full_post_content" type="checkbox" id="fictioneer_show_full_post_content" <?php echo checked( 1, get_option( 'fictioneer_show_full_post_content' ), false ); ?> value="1">
                  <div>
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_show_full_post_content']['label']; ?></span>
                    <p class="sub-label"><?php _e( 'You can still use the [More] block for shortened previews.', 'fictioneer' ) ?></p>
                  </div>
                </label>

                <label class="label-wrapped-checkbox row" for="fictioneer_disable_contact_forms">
                  <input name="fictioneer_disable_contact_forms" type="checkbox" id="fictioneer_disable_contact_forms" <?php echo checked( 1, get_option( 'fictioneer_disable_contact_forms' ), false ); ?> value="1">
                  <div>
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_disable_contact_forms']['label']; ?></span>
                    <p class="sub-label"><?php _e( 'Emergency stop with an error notice.', 'fictioneer' ) ?></p>
                  </div>
                </label>

                <p class="description row"><?php
                  printf(
                    __( 'Compact <a href="%s" target="_blank">date formats</a>. Some page items, such as story cards, use more compact date formats than the general setting of WordPress. Space is an issue here.', 'fictioneer' ),
                    'https://wordpress.org/support/article/formatting-date-and-time/'
                  );
                ?></p>

                <div class="text-input-inline row"><?php
                  printf(
                    __( '<span>Use</span> %s <span>for long date formats (<code>M j, \'y</code>).</span>', 'fictioneer' ),
                    '<input name="fictioneer_subitem_date_format" type="text" id="fictioneer_subitem_date_format" value="' . esc_attr( get_option( 'fictioneer_subitem_date_format', "M j, 'y" ) ?: "M j, 'y" ) . '" style="font-family: Consolas, Monaco, monospace; font-size: 87.5%;" class="text-center" size="8" placeholder="M j, \'y">'
                  )
                ?></div>

                <div class="text-input-inline row"><?php
                  printf(
                    __( '<span>Use</span> %s <span>for short date formats (<code>M j</code>).</span>', 'fictioneer' ),
                    '<input name="fictioneer_subitem_short_date_format" type="text" id="fictioneer_subitem_short_date_format" value="' . esc_attr( get_option( 'fictioneer_subitem_short_date_format', 'M j' ) ) . '" style="font-family: Consolas, Monaco, monospace; font-size: 87.5%;" class="text-center" size="8" placeholder="M j">'
                  )
                ?></div>

  							<div class="text-input row">
  								<label for="fictioneer_system_email_address">
  									<input name="fictioneer_system_email_address" placeholder="<?php echo FICTIONEER_OPTIONS['strings']['fictioneer_system_email_address']['placeholder']; ?>" type="email" id="fictioneer_system_email_address" value="<?php echo esc_attr( get_option( 'fictioneer_system_email_address' ) ); ?>">
                    <div class="popup-note floating invalid dismissable">
    									<div>
                        <span><?php _e( 'Please enter a valid email address or none', 'fictioneer' ) ?></span>
                        <span class="dismiss" data-click="dismiss-popup-note"><i class="fa-solid fa-xmark"></i></span>
                      </div>
    								</div>
  									<p class="sub-label"><?php echo FICTIONEER_OPTIONS['strings']['fictioneer_system_email_address']['label']; ?></p>
  								</label>
  							</div>

                <div class="text-input row">
  								<label for="fictioneer_system_email_name">
  									<input name="fictioneer_system_email_name" placeholder="<?php echo FICTIONEER_OPTIONS['strings']['fictioneer_system_email_name']['placeholder']; ?>" type="text" id="fictioneer_system_email_name" value="<?php echo esc_attr( get_option( 'fictioneer_system_email_name' ) ); ?>">
  									<p class="sub-label"><?php echo FICTIONEER_OPTIONS['strings']['fictioneer_system_email_name']['label']; ?></p>
  								</label>
  							</div>

                <div class="textarea row">
                  <textarea name="fictioneer_contact_email_addresses" id="fictioneer_contact_email_addresses" rows="4" style="height: 205px;"><?php echo get_option( 'fictioneer_contact_email_addresses' ); ?></textarea>
                  <p class="sub-label"><?php echo FICTIONEER_OPTIONS['strings']['fictioneer_contact_email_addresses']['label']; ?></p>
                </div>

    					</div>
    				</div>
    			</div>

          <div class="card">
    				<div class="card-wrapper">
    					<h3 class="card-header"><?php _e( 'Chapters & Stories', 'fictioneer' ) ?></h3>
    					<div class="card-content">

                <p class="description row"><?php _e( 'Updating these settings may require the story caches to be purged. You can do that under Tools.', 'fictioneer' ); ?></p>

                <label class="label-wrapped-checkbox row" for="fictioneer_hide_chapter_icons">
                  <input name="fictioneer_hide_chapter_icons" type="checkbox" id="fictioneer_hide_chapter_icons" <?php echo checked( 1, get_option( 'fictioneer_hide_chapter_icons' ), false ); ?> value="1">
                  <div>
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_hide_chapter_icons']['label']; ?></span>
                    <p class="sub-label"><?php _e( 'Hides the icons on story pages and in the mobile menu.', 'fictioneer' ) ?></p>
                  </div>
                </label>

                <label class="label-wrapped-checkbox row" for="fictioneer_enable_chapter_groups">
                  <input name="fictioneer_enable_chapter_groups" type="checkbox" id="fictioneer_enable_chapter_groups" <?php echo checked( 1, get_option( 'fictioneer_enable_chapter_groups' ), false ); ?> value="1">
                  <div>
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_chapter_groups']['label']; ?></span>
                    <p class="sub-label"><?php _e( 'Display chapters in groups on story pages (if set).', 'fictioneer' ) ?></p>
                  </div>
                </label>

                <label class="label-wrapped-checkbox row" for="fictioneer_disable_chapter_collapsing">
                  <input name="fictioneer_disable_chapter_collapsing" type="checkbox" id="fictioneer_disable_chapter_collapsing" <?php echo checked( 1, get_option( 'fictioneer_disable_chapter_collapsing' ), false ); ?> value="1">
                  <div>
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_disable_chapter_collapsing']['label']; ?></span>
                    <p class="sub-label"><?php _e( 'Do not collapse long lists (13+ chapters).', 'fictioneer' ) ?></p>
                  </div>
                </label>

                <label class="label-wrapped-checkbox row" for="fictioneer_enable_chapter_appending">
                  <input name="fictioneer_enable_chapter_appending" type="checkbox" id="fictioneer_enable_chapter_appending" <?php echo checked( 1, get_option( 'fictioneer_enable_chapter_appending' ), false ); ?> value="1">
                  <div>
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_chapter_appending']['label']; ?></span>
                    <p class="sub-label"><?php _e( 'Only once when the chapter is first saved.', 'fictioneer' ) ?></p>
                  </div>
                </label>

                <label class="label-wrapped-checkbox row" for="fictioneer_limit_chapter_stories_by_author">
                  <input name="fictioneer_limit_chapter_stories_by_author" type="checkbox" id="fictioneer_limit_chapter_stories_by_author" <?php echo checked( 1, get_option( 'fictioneer_limit_chapter_stories_by_author' ), false ); ?> value="1">
                  <div>
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_limit_chapter_stories_by_author']['label']; ?></span>
                    <p class="sub-label"><?php _e( 'Also disables cross-posting as guest author.', 'fictioneer' ) ?></p>
                  </div>
                </label>

  							<div class="text-input-inline row"><?php
                  printf(
                    __( '<span>Calculate reading time with</span> %s <span>words per minute.</span>', 'fictioneer' ),
                    '<input name="fictioneer_words_per_minute" type="text" id="fictioneer_words_per_minute" value="' . esc_attr( get_option( 'fictioneer_words_per_minute', 200 ) ) . '" class="text-center" size="4" placeholder="200">'
                  )
                ?></div>

    					</div>
    				</div>
    			</div>

    			<div class="card">
    				<div class="card-wrapper">
    					<h3 class="card-header"><?php _e( 'Tags & Taxonomies', 'fictioneer' ) ?></h3>
    					<div class="card-content">
    						<div class="overflow-horizontal">

    							<p class="description row"><?php _e( 'Taxonomies on cards include fandoms, genres, tags (if shown), and characters in that order.', 'fictioneer' ) ?></p>

                  <label for="fictioneer_hide_taxonomies_on_story_cards" class="label-wrapped-checkbox row">
                    <input name="fictioneer_hide_taxonomies_on_story_cards" type="checkbox" id="fictioneer_hide_taxonomies_on_story_cards" <?php echo checked( 1, get_option( 'fictioneer_hide_taxonomies_on_story_cards' ), false ); ?> value="1">
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_hide_taxonomies_on_story_cards']['label']; ?></span>
                  </label>

                  <label for="fictioneer_hide_taxonomies_on_chapter_cards" class="label-wrapped-checkbox row">
                    <input name="fictioneer_hide_taxonomies_on_chapter_cards" type="checkbox" id="fictioneer_hide_taxonomies_on_chapter_cards" <?php echo checked( 1, get_option( 'fictioneer_hide_taxonomies_on_chapter_cards' ), false ); ?> value="1">
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_hide_taxonomies_on_chapter_cards']['label']; ?></span>
                  </label>

                  <label for="fictioneer_hide_taxonomies_on_recommendation_cards" class="label-wrapped-checkbox row">
                    <input name="fictioneer_hide_taxonomies_on_recommendation_cards" type="checkbox" id="fictioneer_hide_taxonomies_on_recommendation_cards" <?php echo checked( 1, get_option( 'fictioneer_hide_taxonomies_on_recommendation_cards' ), false ); ?> value="1">
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_hide_taxonomies_on_recommendation_cards']['label']; ?></span>
                  </label>

                  <label for="fictioneer_hide_taxonomies_on_collection_cards" class="label-wrapped-checkbox row">
                    <input name="fictioneer_hide_taxonomies_on_collection_cards" type="checkbox" id="fictioneer_hide_taxonomies_on_collection_cards" <?php echo checked( 1, get_option( 'fictioneer_hide_taxonomies_on_collection_cards' ), false ); ?> value="1">
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_hide_taxonomies_on_collection_cards']['label']; ?></span>
                  </label>

    							<p class="description row"><?php _e( 'Tags are normally not shown on cards since they tend to be numerous, making the cards unsightly. But this is up to you.', 'fictioneer' ) ?></p>

                  <label for="fictioneer_show_tags_on_story_cards" class="label-wrapped-checkbox row">
                    <input name="fictioneer_show_tags_on_story_cards" type="checkbox" id="fictioneer_show_tags_on_story_cards" <?php echo checked( 1, get_option( 'fictioneer_show_tags_on_story_cards' ), false ); ?> value="1">
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_show_tags_on_story_cards']['label']; ?></span>
                  </label>

                  <label for="fictioneer_show_tags_on_chapter_cards" class="label-wrapped-checkbox row">
                      <input name="fictioneer_show_tags_on_chapter_cards" type="checkbox" id="fictioneer_show_tags_on_chapter_cards" <?php echo checked( 1, get_option( 'fictioneer_show_tags_on_chapter_cards' ), false ); ?> value="1">
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_show_tags_on_chapter_cards']['label']; ?></span>
                  </label>

                  <label for="fictioneer_show_tags_on_recommendation_cards" class="label-wrapped-checkbox row">
                    <input name="fictioneer_show_tags_on_recommendation_cards" type="checkbox" id="fictioneer_show_tags_on_recommendation_cards" <?php echo checked( 1, get_option( 'fictioneer_show_tags_on_recommendation_cards' ), false ); ?> value="1">
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_show_tags_on_recommendation_cards']['label']; ?></span>
                  </label>

                  <label for="fictioneer_show_tags_on_collection_cards" class="label-wrapped-checkbox row">
                    <input name="fictioneer_show_tags_on_collection_cards" type="checkbox" id="fictioneer_show_tags_on_collection_cards" <?php echo checked( 1, get_option( 'fictioneer_show_tags_on_collection_cards' ), false ); ?> value="1">
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_show_tags_on_collection_cards']['label']; ?></span>
                  </label>

    							<p class="description row"><?php _e( 'Taxonomies on pages are displayed above the title, tags and content warnings get their own rows below the description.', 'fictioneer' ) ?></p>

                  <label for="fictioneer_hide_taxonomies_on_pages" class="label-wrapped-checkbox row">
                    <input name="fictioneer_hide_taxonomies_on_pages" type="checkbox" id="fictioneer_hide_taxonomies_on_pages" <?php echo checked( 1, get_option( 'fictioneer_hide_taxonomies_on_pages' ), false ); ?> value="1">
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_hide_taxonomies_on_pages']['label']; ?></span>
                  </label>

                  <label for="fictioneer_hide_tags_on_pages" class="label-wrapped-checkbox row">
                    <input name="fictioneer_hide_tags_on_pages" type="checkbox" id="fictioneer_hide_tags_on_pages" <?php echo checked( 1, get_option( 'fictioneer_hide_tags_on_pages' ), false ); ?> value="1">
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_hide_tags_on_pages']['label']; ?></span>
                  </label>

                  <label for="fictioneer_hide_content_warnings_on_pages" class="label-wrapped-checkbox row">
                    <input name="fictioneer_hide_content_warnings_on_pages" type="checkbox" id="fictioneer_hide_content_warnings_on_pages" <?php echo checked( 1, get_option( 'fictioneer_hide_content_warnings_on_pages' ), false ); ?> value="1">
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_hide_content_warnings_on_pages']['label']; ?></span>
                  </label>

    						</div>
    					</div>
    				</div>
    			</div>

    			<div class="card">
    				<div class="card-wrapper">
    					<h3 class="card-header"><?php _e( 'Page Assignments', 'fictioneer' ) ?></h3>
    					<div class="card-content">
    						<div class="overflow-horizontal">

    							<p class="description row"><?php _e( 'The theme expects certain pages to exist. Just create a normal page, assign the specified page template, and select it here. Clear caches afterwards.', 'fictioneer' ) ?></p>

    							<div class="select row">
    	              <?php
    	                wp_dropdown_pages(
    	                  array(
    	                    'name' => 'fictioneer_user_profile_page',
    	                    'id' => 'fictioneer_user_profile_page',
    	                    'option_none_value' => __( 'None', 'fictioneer' ),
    	                    'show_option_no_change' => __( 'None', 'fictioneer' ),
    	                    'selected' => get_option( 'fictioneer_user_profile_page' )
    	                  )
    	                );
    	              ?>
    								<p class="sub-label"><?php _e( 'Account Page (Template: User Profile) &bull; Do not cache!', 'fictioneer' ) ?></p>
    	            </div>

                  <div class="select row">
    	              <?php
                      wp_dropdown_pages(
                        array(
                          'name' => 'fictioneer_bookmarks_page',
                          'id' => 'fictioneer_bookmarks_page',
                          'option_none_value' => __( 'None' ),
                          'show_option_no_change' => __( 'None' ),
                          'selected' => get_option( 'fictioneer_bookmarks_page' )
                        )
                      );
    	              ?>
    								<p class="sub-label"><?php _e( 'Bookmarks Page (Template: Bookmarks)', 'fictioneer' ) ?></p>
    	            </div>

                  <div class="select row">
    	              <?php
                      wp_dropdown_pages(
                        array(
                          'name' => 'fictioneer_stories_page',
                          'id' => 'fictioneer_stories_page',
                          'option_none_value' => __( 'None' ),
                          'show_option_no_change' => __( 'None' ),
                          'selected' => get_option( 'fictioneer_stories_page' )
                        )
                      );
    	              ?>
    								<p class="sub-label"><?php _e( 'Stories Page (Template: Stories)', 'fictioneer' ) ?></p>
    	            </div>

                  <div class="select row">
    	              <?php
                      wp_dropdown_pages(
                        array(
                          'name' => 'fictioneer_chapters_page',
                          'id' => 'fictioneer_chapters_page',
                          'option_none_value' => __( 'None' ),
                          'show_option_no_change' => __( 'None' ),
                          'selected' => get_option( 'fictioneer_chapters_page' )
                        )
                      );
    	              ?>
    								<p class="sub-label"><?php _e( 'Chapters Page (Template: Chapters)', 'fictioneer' ) ?></p>
    	            </div>

                  <div class="select row">
    	              <?php
                      wp_dropdown_pages(
                        array(
                          'name' => 'fictioneer_recommendations_page',
                          'id' => 'fictioneer_recommendations_page',
                          'option_none_value' => __( 'None' ),
                          'show_option_no_change' => __( 'None' ),
                          'selected' => get_option( 'fictioneer_recommendations_page' )
                        )
                      );
    	              ?>
    								<p class="sub-label"><?php _e( 'Recommendations Page (Template: Recommendations)', 'fictioneer' ) ?></p>
    	            </div>

                  <div class="select row">
    	              <?php
                      wp_dropdown_pages(
                        array(
                          'name' => 'fictioneer_collections_page',
                          'id' => 'fictioneer_collections_page',
                          'option_none_value' => __( 'None' ),
                          'show_option_no_change' => __( 'None' ),
                          'selected' => get_option( 'fictioneer_collections_page' )
                        )
                      );
    	              ?>
    								<p class="sub-label"><?php _e( 'Collections Page (Template: Collections)', 'fictioneer' ) ?></p>
    	            </div>

                  <div class="select row">
    	              <?php
                      wp_dropdown_pages(
                        array(
                          'name' => 'fictioneer_bookshelf_page',
                          'id' => 'fictioneer_bookshelf_page',
                          'option_none_value' => __( 'None' ),
                          'show_option_no_change' => __( 'None' ),
                          'selected' => get_option( 'fictioneer_bookshelf_page' )
                        )
                      );
    	              ?>
    								<p class="sub-label"><?php _e( 'Bookshelf Page (Template: Bookshelf) &bull; Do not cache!', 'fictioneer' ) ?></p>
    	            </div>

                  <div class="select row">
    	              <?php
                      wp_dropdown_pages(
                        array(
                          'name' => 'fictioneer_404_page',
                          'id' => 'fictioneer_404_page',
                          'option_none_value' => __( 'None' ),
                          'show_option_no_change' => __( 'None' ),
                          'selected' => get_option( 'fictioneer_404_page' )
                        )
                      );
    	              ?>
    								<p class="sub-label"><?php _e( '404 Page &bull; Add content to your 404 page, make it useful!', 'fictioneer' ) ?></p>
    	            </div>

                  <p class="description row"><?php _e( '<strong>Note:</strong> Some of these pages are highly dynamic, such as the account and bookshelf templates. The former must never be cached. The latter can be cached if you apply the AJAX template. Private caching (e.g. one per user) would work for both if you have the disk space.', 'fictioneer' ) ?></p>

    						</div>
    					</div>
    				</div>
    			</div>

          <div class="card">
    				<div class="card-wrapper">
    					<h3 class="card-header"><?php _e( 'Features', 'fictioneer' ) ?></h3>
    					<div class="card-content">
    						<div class="overflow-horizontal">

                  <label for="fictioneer_enable_storygraph_api" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_storygraph_api" type="checkbox" id="fictioneer_enable_storygraph_api" <?php echo checked( 1, get_option( 'fictioneer_enable_storygraph_api' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_storygraph_api']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Reach a larger audience by allowing external services to index and search your stories (meta data only).', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_oauth" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_oauth" type="checkbox" id="fictioneer_enable_oauth" <?php echo checked( 1, get_option( 'fictioneer_enable_oauth' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_oauth']['label']; ?></span>
                      <p class="sub-label"><?php
                        printf(
                          __( 'Register/Login with social media accounts. This requires you to <a href="%s" target="_blank">set up an application</a> for each service provider.', 'fictioneer' ),
                          'https://github.com/Tetrakern/fictioneer/blob/main/DOCUMENTATION.md#users--oauth'
                        );
                      ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_lightbox" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_lightbox" type="checkbox" id="fictioneer_enable_lightbox" <?php echo checked( 1, get_option( 'fictioneer_enable_lightbox' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_lightbox']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Enlarge images in floating container on click.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_bookmarks" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_bookmarks" type="checkbox" id="fictioneer_enable_bookmarks" <?php echo checked( 1, get_option( 'fictioneer_enable_bookmarks' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_bookmarks']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Bookmark paragraphs in chapters. No account needed.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_follows" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_follows" type="checkbox" id="fictioneer_enable_follows" <?php echo checked( 1, get_option( 'fictioneer_enable_follows' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_follows']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Follow stories and get on-site alerts for updates.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_checkmarks" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_checkmarks" type="checkbox" id="fictioneer_enable_checkmarks" <?php echo checked( 1, get_option( 'fictioneer_enable_checkmarks' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_checkmarks']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Mark chapters and stories as "read".', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_reminders" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_reminders" type="checkbox" id="fictioneer_enable_reminders" <?php echo checked( 1, get_option( 'fictioneer_enable_reminders' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_reminders']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Remember stories to be "read later".', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_suggestions" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_suggestions" type="checkbox" id="fictioneer_enable_suggestions" <?php echo checked( 1, get_option( 'fictioneer_enable_suggestions' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_suggestions']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Suggest color-coded text changes in the comments.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_theme_rss" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_theme_rss" type="checkbox" id="fictioneer_enable_theme_rss" <?php echo checked( 1, get_option( 'fictioneer_enable_theme_rss' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_theme_rss']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Replace the default WordPress RSS feeds with theme feeds built for publishing your stories and chapters.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_seo" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_seo" type="checkbox" <?php echo fictioneer_seo_plugin_active() ? 'disabled' : ''; ?> id="fictioneer_enable_seo" <?php echo checked( 1, get_option( 'fictioneer_enable_seo' ) && ! fictioneer_seo_plugin_active(), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_seo']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Open Graph images, chat embeds, meta tags, and schema graphs for rich snippets. Incompatible with SEO plugins.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_sitemap" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_sitemap" type="checkbox" <?php echo fictioneer_seo_plugin_active() ? 'disabled' : ''; ?> id="fictioneer_enable_sitemap" <?php echo checked( 1, get_option( 'fictioneer_enable_sitemap' ) && ! fictioneer_seo_plugin_active(), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_sitemap']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Customized sitemap for search engines to crawl. Respects unlisted flags and custom post types.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_tts" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_tts" type="checkbox" id="fictioneer_enable_tts" <?php echo checked( 1, get_option( 'fictioneer_enable_tts' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_tts']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Sometimes wonky browser-based text-to-speech engine. Extend and availability depends on the browser and OS.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_epubs" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_epubs" type="checkbox" id="fictioneer_enable_epubs" <?php echo checked( 1, get_option( 'fictioneer_enable_epubs' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_epubs']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Automatically generated ePUBs. Can take a while and may fail due to non-conform content or excessive size. You can alternatively upload manually created ebooks as well.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

    						</div>
    					</div>
    				</div>
    			</div>

          <div class="card">
    				<div class="card-wrapper">
    					<h3 class="card-header"><?php _e( 'Comments', 'fictioneer' ) ?></h3>
    					<div class="card-content">
    						<div class="overflow-horizontal">

                  <p class="description row"><?php _e( 'Some of these options will only work properly with the theme comment system enabled.', 'fictioneer' ) ?></p>

                  <label for="fictioneer_disable_commenting" class="label-wrapped-checkbox row">
                    <input name="fictioneer_disable_commenting" type="checkbox" id="fictioneer_disable_commenting" <?php echo checked( 1, get_option( 'fictioneer_disable_commenting' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_disable_commenting']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Still shows the current comments.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_require_js_to_comment" class="label-wrapped-checkbox row">
                    <input name="fictioneer_require_js_to_comment" type="checkbox" id="fictioneer_require_js_to_comment" <?php echo checked( 1, get_option( 'fictioneer_require_js_to_comment' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_require_js_to_comment']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Simple but effective spam protection.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_comment_link_limit" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_comment_link_limit" type="checkbox" id="fictioneer_enable_comment_link_limit" <?php echo checked( 1, get_option( 'fictioneer_enable_comment_link_limit' ), false ); ?> value="1">
                    <div>
                      <span><?php
                        printf(
                          __( '<span>Limit comments to</span> %s <span>link(s) or less</span>', 'fictioneer' ),
                          '<input name="fictioneer_comment_link_limit_threshold" type="text" id="fictioneer_comment_link_limit_threshold" value="' . esc_attr( get_option( 'fictioneer_comment_link_limit_threshold', 3 ) ) . '" class="text-center" size="3" placeholder="3">'
                        )
                      ?></span>
                      <p class="sub-label"><?php _e( 'Reliable spam protection but can lead to false positives.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_ajax_comment_submit" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_ajax_comment_submit" type="checkbox" id="fictioneer_enable_ajax_comment_submit" <?php echo checked( 1, get_option( 'fictioneer_enable_ajax_comment_submit' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_ajax_comment_submit']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Does not reload the page on submit.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_ajax_comment_moderation" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_ajax_comment_moderation" type="checkbox" id="fictioneer_enable_ajax_comment_moderation" <?php echo checked( 1, get_option( 'fictioneer_enable_ajax_comment_moderation' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_ajax_comment_moderation']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Moderation actions directly in the comment section.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_comment_toolbar" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_comment_toolbar" type="checkbox" id="fictioneer_enable_comment_toolbar" <?php echo checked( 1, get_option( 'fictioneer_enable_comment_toolbar' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_comment_toolbar']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Quick buttons to wrap text selection in BBCodes.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_custom_badges" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_custom_badges" type="checkbox" id="fictioneer_enable_custom_badges" <?php echo checked( 1, get_option( 'fictioneer_enable_custom_badges' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_custom_badges']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Assign custom badges (but users can disable them).', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_patreon_badges" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_patreon_badges" type="checkbox" id="fictioneer_enable_patreon_badges" <?php echo checked( 1, get_option( 'fictioneer_enable_patreon_badges' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_patreon_badges']['label']; ?></span>
                      <p class="sub-label"><?php
                        printf(
                          __( 'Show supporter badge for the <a href="%s">linked Patreon client</a>. This only works for the owner of the linked client. You cannot add campaigns for individual authors.', 'fictioneer' ),
                          '?page=fictioneer_connections'
                        );
                      ?></p>
                    </div>
                  </label>

                  <div class="text-input row checkbox-inset">
                    <label for="fictioneer_patreon_label">
                      <input name="fictioneer_patreon_label" placeholder="<?php _ex( 'Patron', 'Default Patreon supporter badge label.', 'fictioneer' ); ?>" type="text" id="fictioneer_patreon_label" value="<?php echo esc_attr( get_option( 'fictioneer_patreon_label' ) ); ?>">
                      <p class="sub-label"><?php _e( 'Patreon badge label (default: Patron)', 'fictioneer' ) ?></p>
                    </label>
                  </div>

                  <label for="fictioneer_enable_comment_notifications" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_comment_notifications" type="checkbox" id="fictioneer_enable_comment_notifications" <?php echo checked( 1, get_option( 'fictioneer_enable_comment_notifications' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_comment_notifications']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Notify commenters about replies via email (if provided).', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_sticky_comments" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_sticky_comments" type="checkbox" id="fictioneer_enable_sticky_comments" <?php echo checked( 1, get_option( 'fictioneer_enable_sticky_comments' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_sticky_comments']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Always on top of the first page.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_private_commenting" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_private_commenting" type="checkbox" id="fictioneer_enable_private_commenting" <?php echo checked( 1, get_option( 'fictioneer_enable_private_commenting' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_private_commenting']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Private comments are invisible to the public.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_comment_reporting" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_comment_reporting" type="checkbox" id="fictioneer_enable_comment_reporting" <?php echo checked( 1, get_option( 'fictioneer_enable_comment_reporting' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_comment_reporting']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Logged-in users can report comments via flag button.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_user_comment_editing" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_user_comment_editing" type="checkbox" id="fictioneer_enable_user_comment_editing" <?php echo checked( 1, get_option( 'fictioneer_enable_user_comment_editing' ), false ); ?> value="1">
                    <div>
                      <span><?php
                        printf(
                          __( '<span>Enable comment editing for</span> %s <span>minute(s)</span>', 'fictioneer' ),
                          '<input name="fictioneer_user_comment_edit_time" type="text" id="fictioneer_user_comment_edit_time" value="' . esc_attr( get_option( 'fictioneer_user_comment_edit_time', 15 ) ) . '" class="text-center" size="3" placeholder="15">'
                        )
                      ?></span>
                      <p class="sub-label"><?php _e( 'Logged-in users can edit their comments for a number of minutes; enter -1 for no time limit.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <div class="text-input-inline row">
                    <?php
                      printf(
                        __( '<span>Automatically moderate comments with</span> %s <span>reports</span>. This will only trigger once but the comments will remain hidden unless set to ignore reports.', 'fictioneer' ),
                        '<input name="fictioneer_comment_report_threshold" type="text" id="fictioneer_comment_report_threshold" value="' . esc_attr( get_option( 'fictioneer_comment_report_threshold', 10 ) ) . '" class="text-center" size="3" placeholder="10">'
                      )
                    ?>
    							</div>

                  <div class="textarea row">
                    <textarea name="fictioneer_comments_notice" id="fictioneer_comments_notice" rows="4" style="height: 277px;"><?php echo get_option( 'fictioneer_comments_notice' ); ?></textarea>
                    <p class="sub-label"><?php _e( 'Notice above comments. Leave empty to hide. HTML allowed.', 'fictioneer' ) ?></p>
                  </div>

    						</div>
    					</div>
    				</div>
    			</div>

          <div class="card">
    				<div class="card-wrapper">
    					<h3 class="card-header"><?php _e( 'Performance', 'fictioneer' ) ?></h3>
    					<div class="card-content">
    						<div class="overflow-horizontal">

                  <label for="fictioneer_disable_heartbeat" class="label-wrapped-checkbox row">
                    <input name="fictioneer_disable_heartbeat" type="checkbox" id="fictioneer_disable_heartbeat" <?php echo checked( 1, get_option( 'fictioneer_disable_heartbeat' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_disable_heartbeat']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'No more continuous requests for near real-time updates and autosaves, which can strain the server.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_remove_head_clutter" class="label-wrapped-checkbox row">
                    <input name="fictioneer_remove_head_clutter" type="checkbox" id="fictioneer_remove_head_clutter" <?php echo checked( 1, get_option( 'fictioneer_remove_head_clutter' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_remove_head_clutter']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Less meta tags, scripts, and styles in the &#60;head&#62;.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_remove_wp_svg_filters" class="label-wrapped-checkbox row">
                    <input name="fictioneer_remove_wp_svg_filters" type="checkbox" id="fictioneer_remove_wp_svg_filters" <?php echo checked( 1, get_option( 'fictioneer_remove_wp_svg_filters' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_remove_wp_svg_filters']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'If not used, these are just clutter in the &#60;body&#62;. This will prevent duotone filters from working.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_reduce_admin_bar" class="label-wrapped-checkbox row">
                    <input name="fictioneer_reduce_admin_bar" type="checkbox" id="fictioneer_reduce_admin_bar" <?php echo checked( 1, get_option( 'fictioneer_reduce_admin_bar' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_reduce_admin_bar']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Less menu items, links, and icons in the admin bar.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_disable_all_widgets" class="label-wrapped-checkbox row">
                    <input name="fictioneer_disable_all_widgets" type="checkbox" id="fictioneer_disable_all_widgets" <?php echo checked( 1, get_option( 'fictioneer_disable_all_widgets' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_disable_all_widgets']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'The theme does not use widgets by default and removing them slightly boosts performance.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_bundle_stylesheets" class="label-wrapped-checkbox row">
                    <input name="fictioneer_bundle_stylesheets" type="checkbox" id="fictioneer_bundle_stylesheets" <?php echo checked( 1, get_option( 'fictioneer_bundle_stylesheets' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_bundle_stylesheets']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Faster if HTTP/2 is not available to load multiple smaller files in parallel, but increases the initial payload.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

    						</div>
    					</div>
    				</div>
    			</div>

          <div class="card">
    				<div class="card-wrapper">
    					<h3 class="card-header"><?php _e( 'Security & Privacy', 'fictioneer' ) ?></h3>
    					<div class="card-content">

                <label for="fictioneer_do_not_save_comment_ip" class="label-wrapped-checkbox row">
                  <input name="fictioneer_do_not_save_comment_ip" type="checkbox" id="fictioneer_do_not_save_comment_ip" <?php echo checked( 1, get_option( 'fictioneer_do_not_save_comment_ip' ), false ); ?> value="1">
                  <div>
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_do_not_save_comment_ip']['label']; ?></span>
                    <p class="sub-label"><?php _e( 'IP addresses are personal data.', 'fictioneer' ) ?></p>
                  </div>
                </label>

                <label for="fictioneer_restrict_rest_api" class="label-wrapped-checkbox row">
                  <input name="fictioneer_restrict_rest_api" type="checkbox" id="fictioneer_restrict_rest_api" <?php echo checked( 1, get_option( 'fictioneer_restrict_rest_api' ), false ); ?> value="1">
                  <div>
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_restrict_rest_api']['label']; ?></span>
                    <p class="sub-label"><?php _e( 'Disables API for guests and low-permission users.', 'fictioneer' ) ?></p>
                  </div>
                </label>

                <label for="fictioneer_enable_subscriber_self_delete" class="label-wrapped-checkbox row">
                  <input name="fictioneer_enable_subscriber_self_delete" type="checkbox" id="fictioneer_enable_subscriber_self_delete" <?php echo checked( 1, get_option( 'fictioneer_enable_subscriber_self_delete' ), false ); ?> value="1">
                  <div>
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_subscriber_self_delete']['label']; ?></span>
                    <p class="sub-label"><?php _e( 'Convenient compliance with the “right to erasure”. Roles with higher privileges still need manual deletion.', 'fictioneer' ) ?></p>
                  </div>
                </label>

                <label for="fictioneer_admin_reduce_subscriber_profile" class="label-wrapped-checkbox row">
                  <input name="fictioneer_admin_reduce_subscriber_profile" type="checkbox" id="fictioneer_admin_reduce_subscriber_profile" <?php echo checked( 1, get_option( 'fictioneer_admin_reduce_subscriber_profile' ), false ); ?> value="1">
                  <div>
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_admin_reduce_subscriber_profile']['label']; ?></span>
                    <p class="sub-label"><?php _e( 'Removes superfluous blocks from subscriber profile, such as personal options and application passwords.', 'fictioneer' ) ?></p>
                  </div>
                </label>

                <label for="fictioneer_restrict_media_access" class="label-wrapped-checkbox row">
                  <input name="fictioneer_restrict_media_access" type="checkbox" id="fictioneer_restrict_media_access" <?php echo checked( 1, get_option( 'fictioneer_restrict_media_access' ), false ); ?> value="1">
                  <div>
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_restrict_media_access']['label']; ?></span>
                    <p class="sub-label"><?php _e( 'Users can only see and edit their own uploads in the media library unless they have the "edit_users" capability.', 'fictioneer' ) ?></p>
                  </div>
                </label>

                <label for="fictioneer_strip_shortcodes_for_non_administrators" class="label-wrapped-checkbox row">
                  <input name="fictioneer_strip_shortcodes_for_non_administrators" type="checkbox" id="fictioneer_strip_shortcodes_for_non_administrators" <?php echo checked( 1, get_option( 'fictioneer_strip_shortcodes_for_non_administrators' ), false ); ?> value="1">
                  <div>
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_strip_shortcodes_for_non_administrators']['label']; ?></span>
                    <p class="sub-label"><?php _e( 'Beware! Strips shortcodes from the content when a post is saved by a non-administrator.', 'fictioneer' ) ?></p>
                  </div>
                </label>

                <label for="fictioneer_disable_application_passwords" class="label-wrapped-checkbox row">
                  <input name="fictioneer_disable_application_passwords" type="checkbox" id="fictioneer_disable_application_passwords" <?php echo checked( 1, get_option( 'fictioneer_disable_application_passwords' ), false ); ?> value="1">
                  <div>
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_disable_application_passwords']['label']; ?></span>
                    <p class="sub-label"><?php _e( 'If you do not need them, you should disable them.', 'fictioneer' ) ?></p>
                  </div>
                </label>

                <label for="fictioneer_disable_html_in_comments" class="label-wrapped-checkbox row">
                  <input name="fictioneer_disable_html_in_comments" type="checkbox" id="fictioneer_disable_html_in_comments" <?php echo checked( 1, get_option( 'fictioneer_disable_html_in_comments' ), false ); ?> value="1">
                  <div>
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_disable_html_in_comments']['label']; ?></span>
                    <p class="sub-label"><?php _e( 'BBCodes still work and are easier to use anyway.', 'fictioneer' ) ?></p>
                  </div>
                </label>

                <label for="fictioneer_logout_redirects_home" class="label-wrapped-checkbox row">
                  <input name="fictioneer_logout_redirects_home" type="checkbox" id="fictioneer_logout_redirects_home" <?php echo checked( 1, get_option( 'fictioneer_logout_redirects_home' ), false ); ?> value="1">
                  <div>
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_logout_redirects_home']['label']; ?></span>
                    <p class="sub-label"><?php _e( 'Prevents users from ending up on the login page.', 'fictioneer' ) ?></p>
                  </div>
                </label>

                <label for="fictioneer_consent_wrappers" class="label-wrapped-checkbox row">
                  <input name="fictioneer_consent_wrappers" type="checkbox" id="fictioneer_consent_wrappers" <?php echo checked( 1, get_option( 'fictioneer_consent_wrappers' ), false ); ?> value="1">
                  <div>
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_consent_wrappers']['label']; ?></span>
                    <p class="sub-label"><?php _e( 'External content not loaded until deliberately clicked.', 'fictioneer' ) ?></p>
                  </div>
                </label>

                <label for="fictioneer_admin_restrict_private_data" class="label-wrapped-checkbox row">
                  <input name="fictioneer_admin_restrict_private_data" type="checkbox" id="fictioneer_admin_restrict_private_data" <?php echo checked( 1, get_option( 'fictioneer_admin_restrict_private_data' ), false ); ?> value="1">
                  <div>
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_admin_restrict_private_data']['label']; ?></span>
                    <p class="sub-label"><?php _e( 'Hides names, emails, IPs, comment quick edit, and more privacy sensitive data sources.', 'fictioneer' ) ?></p>
                  </div>
                </label>

                <label for="fictioneer_cookie_banner" class="label-wrapped-checkbox row">
                  <input name="fictioneer_cookie_banner" type="checkbox" id="fictioneer_cookie_banner" <?php echo checked( 1, get_option( 'fictioneer_cookie_banner' ), false ); ?> value="1">
                  <div>
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_cookie_banner']['label']; ?></span>
                    <p class="sub-label"><?php _e( "Shows a generic cookie consent banner and activates the <code>fictioneer_get_consent()</code> theme function that returns either false, 'necessary', or 'full'.", 'fictioneer' ) ?></p>
                  </div>
                </label>

    					</div>
    				</div>
    			</div>

          <div class="card">
    				<div class="card-wrapper">
    					<h3 class="card-header"><?php _e( 'Compatibility', 'fictioneer' ) ?></h3>
    					<div class="card-content">
    						<div class="overflow-horizontal">

                  <label for="fictioneer_disable_properties" class="label-wrapped-checkbox row">
                    <input name="fictioneer_disable_properties" type="checkbox" id="fictioneer_disable_properties" <?php echo checked( 1, get_option( 'fictioneer_disable_properties' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_disable_properties']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Only do this if you define everything yourself.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_jquery_migrate" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_jquery_migrate" type="checkbox" id="fictioneer_enable_jquery_migrate" <?php echo checked( 1, get_option( 'fictioneer_enable_jquery_migrate' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_jquery_migrate']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Some older plugins might require this.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_purge_all_caches" class="label-wrapped-checkbox row">
                    <input name="fictioneer_purge_all_caches" type="checkbox" id="fictioneer_purge_all_caches" <?php echo checked( 1, get_option( 'fictioneer_purge_all_caches' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_purge_all_caches']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Inefficient but makes sure everything is up-to-date.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_cache_compatibility" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_cache_compatibility" type="checkbox" id="fictioneer_enable_cache_compatibility" <?php echo checked( 1, get_option( 'fictioneer_enable_cache_compatibility' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_cache_compatibility']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Make the theme aware of unknown caching plugins.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_public_cache_compatibility" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_public_cache_compatibility" type="checkbox" id="fictioneer_enable_public_cache_compatibility" <?php echo checked( 1, get_option( 'fictioneer_enable_public_cache_compatibility' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_public_cache_compatibility']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'For serving public caches to logged-in users. Exceptions for administrators and moderators are recommended.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_private_cache_compatibility" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_private_cache_compatibility" type="checkbox" id="fictioneer_enable_private_cache_compatibility" <?php echo checked( 1, get_option( 'fictioneer_enable_private_cache_compatibility' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_private_cache_compatibility']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'For serving private caches to logged-in users. Also useful if you do not serve caches to logged-in users at all.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_all_blocks" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_all_blocks" type="checkbox" id="fictioneer_enable_all_blocks" <?php echo checked( 1, get_option( 'fictioneer_enable_all_blocks' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_all_blocks']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'No guarantee these blocks work with the theme.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_all_block_styles" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_all_block_styles" type="checkbox" id="fictioneer_enable_all_block_styles" <?php echo checked( 1, get_option( 'fictioneer_enable_all_block_styles' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_all_block_styles']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'This might interfere with theme styles.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_disable_theme_logout" class="label-wrapped-checkbox row">
                    <input name="fictioneer_disable_theme_logout" type="checkbox" id="fictioneer_disable_theme_logout" <?php echo checked( 1, get_option( 'fictioneer_disable_theme_logout' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_disable_theme_logout']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Return to the default WordPress logout with nonce.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_ajax_comment_form" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_ajax_comment_form" type="checkbox" id="fictioneer_enable_ajax_comment_form" <?php echo checked( 1, get_option( 'fictioneer_enable_ajax_comment_form' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_ajax_comment_form']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Load the comment form via AJAX to circumvent caching.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_ajax_comments" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_ajax_comments" type="checkbox" id="fictioneer_enable_ajax_comments" <?php echo checked( 1, get_option( 'fictioneer_enable_ajax_comments' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_ajax_comments']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Load the comment section and form via AJAX. More server work but circumvents caching.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_ajax_nonce" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_ajax_nonce" type="checkbox" id="fictioneer_enable_ajax_nonce" <?php echo checked( 1, get_option( 'fictioneer_enable_ajax_nonce' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_ajax_nonce']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Fetch the nonce via AJAX after the page has been loaded to get around aggressive caching strategies.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_enable_ajax_authentication" class="label-wrapped-checkbox row">
                    <input name="fictioneer_enable_ajax_authentication" type="checkbox" id="fictioneer_enable_ajax_authentication" <?php echo checked( 1, get_option( 'fictioneer_enable_ajax_authentication' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_enable_ajax_authentication']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Check for user login state after the page has been loaded to get around anonymizing caching strategies.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_disable_theme_search" class="label-wrapped-checkbox with-description row">
                    <input name="fictioneer_disable_theme_search" type="checkbox" id="fictioneer_disable_theme_search" <?php echo checked( 1, get_option( 'fictioneer_disable_theme_search' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_disable_theme_search']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Return to the default search query and form.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_disable_comment_bbcodes" class="label-wrapped-checkbox with-description row">
                    <input name="fictioneer_disable_comment_bbcodes" type="checkbox" id="fictioneer_disable_comment_bbcodes" <?php echo checked( 1, get_option( 'fictioneer_disable_comment_bbcodes' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_disable_comment_bbcodes']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'This will disable the comment toolbar as well.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_disable_comment_callback" class="label-wrapped-checkbox with-description row">
                    <input name="fictioneer_disable_comment_callback" type="checkbox" id="fictioneer_disable_comment_callback" <?php echo checked( 1, get_option( 'fictioneer_disable_comment_callback' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_disable_comment_callback']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Return to the default WordPress markup.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_disable_comment_query" class="label-wrapped-checkbox with-description row">
                    <input name="fictioneer_disable_comment_query" type="checkbox" id="fictioneer_disable_comment_query" <?php echo checked( 1, get_option( 'fictioneer_disable_comment_query' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_disable_comment_query']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Return to the default WordPress query.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_disable_comment_form" class="label-wrapped-checkbox with-description row">
                    <input name="fictioneer_disable_comment_form" type="checkbox" id="fictioneer_disable_comment_form" <?php echo checked( 1, get_option( 'fictioneer_disable_comment_form' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_disable_comment_form']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Return to the default WordPress form.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

                  <label for="fictioneer_disable_comment_pagination" class="label-wrapped-checkbox with-description row">
                    <input name="fictioneer_disable_comment_pagination" type="checkbox" id="fictioneer_disable_comment_pagination" <?php echo checked( 1, get_option( 'fictioneer_disable_comment_pagination' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_disable_comment_pagination']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'Return to the default WordPress pagination (no-AJAX).', 'fictioneer' ) ?></p>
                    </div>
                  </label>

    						</div>
    					</div>
    				</div>
    			</div>

          <div class="card">
    				<div class="card-wrapper">
    					<h3 class="card-header"><?php _e( 'Social Media' ) ?></h3>
    					<div class="card-content">
    						<div class="overflow-horizontal">

                  <label for="fictioneer_disable_facebook_share" class="label-wrapped-checkbox row">
                    <input name="fictioneer_disable_facebook_share" type="checkbox" id="fictioneer_disable_facebook_share" <?php echo checked( 1, get_option( 'fictioneer_disable_facebook_share' ), false ); ?> value="1">
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_disable_facebook_share']['label']; ?></span>
                  </label>

                  <label for="fictioneer_disable_twitter_share" class="label-wrapped-checkbox row">
                    <input name="fictioneer_disable_twitter_share" type="checkbox" id="fictioneer_disable_twitter_share" <?php echo checked( 1, get_option( 'fictioneer_disable_twitter_share' ), false ); ?> value="1">
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_disable_twitter_share']['label']; ?></span>
                  </label>

                  <label for="fictioneer_disable_tumblr_share" class="label-wrapped-checkbox row">
                    <input name="fictioneer_disable_tumblr_share" type="checkbox" id="fictioneer_disable_tumblr_share" <?php echo checked( 1, get_option( 'fictioneer_disable_tumblr_share' ), false ); ?> value="1">
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_disable_tumblr_share']['label']; ?></span>
                  </label>

                  <label for="fictioneer_disable_reddit_share" class="label-wrapped-checkbox row">
                    <input name="fictioneer_disable_reddit_share" type="checkbox" id="fictioneer_disable_reddit_share" <?php echo checked( 1, get_option( 'fictioneer_disable_reddit_share' ), false ); ?> value="1">
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_disable_reddit_share']['label']; ?></span>
                  </label>

                  <label for="fictioneer_disable_mastodon_share" class="label-wrapped-checkbox row">
                    <input name="fictioneer_disable_mastodon_share" type="checkbox" id="fictioneer_disable_mastodon_share" <?php echo checked( 1, get_option( 'fictioneer_disable_mastodon_share' ), false ); ?> value="1">
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_disable_mastodon_share']['label']; ?></span>
                  </label>

                  <label for="fictioneer_disable_telegram_share" class="label-wrapped-checkbox row">
                    <input name="fictioneer_disable_telegram_share" type="checkbox" id="fictioneer_disable_telegram_share" <?php echo checked( 1, get_option( 'fictioneer_disable_telegram_share' ), false ); ?> value="1">
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_disable_telegram_share']['label']; ?></span>
                  </label>

                  <label for="fictioneer_disable_whatsapp_share" class="label-wrapped-checkbox row">
                    <input name="fictioneer_disable_whatsapp_share" type="checkbox" id="fictioneer_disable_whatsapp_share" <?php echo checked( 1, get_option( 'fictioneer_disable_whatsapp_share' ), false ); ?> value="1">
                    <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_disable_whatsapp_share']['label']; ?></span>
                  </label>

    						</div>
    					</div>
    				</div>
    			</div>

          <?php do_action( 'fictioneer_admin_settings_general' ); ?>

          <div class="card">
    				<div class="card-wrapper">
    					<h3 class="card-header"><?php _e( 'Deactivation (DANGER ZONE)', 'fictioneer' ) ?></h3>
    					<div class="card-content">
    						<div class="overflow-horizontal">

                  <label for="fictioneer_delete_theme_options_on_deactivation" class="label-wrapped-checkbox row">
                    <input name="fictioneer_delete_theme_options_on_deactivation" type="checkbox" id="fictioneer_delete_theme_options_on_deactivation" <?php echo checked( 1, get_option( 'fictioneer_delete_theme_options_on_deactivation' ), false ); ?> value="1">
                    <div>
                      <span><?php echo FICTIONEER_OPTIONS['booleans']['fictioneer_delete_theme_options_on_deactivation']['label']; ?></span>
                      <p class="sub-label"><?php _e( 'This will also remove all additional user meta data, such as bookmarks or Follows. Stories, chapters, collections, and recommendations remain but you will need to register their custom post types in order to access them.', 'fictioneer' ) ?></p>
                    </div>
                  </label>

    						</div>
    					</div>
    				</div>
    			</div>

    		</div>

        <div class="flex"><?php submit_button(); ?></div>
      </form>
    </div>
  </div>
</div>
