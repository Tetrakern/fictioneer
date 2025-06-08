# Development

This is a quick guide to get you started if you want to contribute to the theme, fork off your own version, or create a child theme. Nothing too comprehensive, but enough to introduce the core concepts and practices.

## Additional Resources

* [Theme action hooks](ACTIONS.md)
* [Theme filter hooks](FILTERS.md)
* [Child theme example](https://github.com/Tetrakern/fictioneer-child-theme)
* [Plugin example](https://github.com/Tetrakern/fictioneer-base-plugin)

## Coding Standards & Documentation

You can follow the WordPress [coding](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/) and [documentation](https://developer.wordpress.org/coding-standards/inline-documentation-standards/) standards, but they are not imperative here. Since the theme was originally not meant to be publicly released, no thought was spent on collaboration. Which does not mean no standards apply, just not the official ones. Great effort has been put into refactoring and documenting, making everything orderly and clean. Contributors are to uphold this effort and improve what is lacking. Either by following the official handbook or mimicking the existing code.

However, there are a few guidelines:

* Indentation tabs are two spaces wide; not four and especially not eight or more.
* PHP tags for multi-line snippets may share lines to avoid errant spaces in the HTML.
* Braces may be omitted if not necessary, e.g. `if ( condition ) return;` to safe space.
* Arrays may be declared using the short syntax `[]`, except for multi-line definitions.
* CSS may use `rgb()` and `hsl()`, which is even required for some features.
* CSS is compiled from SCSS (Dart Sass) and minified, never edit the processed styles.
* You may `return` from within a case statement, but still add the `break` afterwards.
* JavaScript (ES13) does not need to mirror the PHP formatting.

## Build Pipeline

*This theme was compiled with CodeKit. I’m sick and tired of package managers. There is no dependency hell and I wish to keep it that way. Anything beyond building assets, which would be reasonable, will be rejected. I’m not sorry. If you disagree with that sentiment, you are free to branch or fork and do as you like.*

## CSS/SCSS

**Required feature support:** Container Queries

Fictioneer was originally styled with [BEM](https://getbem.com/) — and still is, although with some modifications. While BEM offers great structure and prevents cascading issues, this comes with a bag of redundancy and heavy markup. You easily end up with extremely long class signatures and lists that break several lines. BEM also does not work well with utility classes which escape the idea of scoped concerns, causing cascading issues again. But adding redundant modifiers for each and every block is not great either.

To alleviate these drawbacks, Fictioneer makes two changes to the default BEM methodology. Modifiers are no longer suffixes but classes that start with an underscore (`._modifier`), always within a block scope. Modifiers are chained to the block parent (`.block._modifier` or `.block__element._modifier`), granting them a higher specificity to override parent styles and single-specificity utility classes. Of course this is no silver bullet either, as it is slightly less efficient.

```html
<section class="block">
  <ul class="block__element _modifier">
    <li class="block__other-element _modifier bad-utility good-utility">Italic coral with 1rem margin bottom</li>
  </ul>
</section>
```

```scss
.block {
  color: black;

  &__element {
    &._modifier {
      color: coral; // Overrides parent block and element styles
    }
  }

  &__element {
    color: navy; // Overridden despite being lower (for demonstration, do not actually do this)
  }

  &__other-element {
    &._modifier {
      font-style: italic; // Same name, different effect (but better use specific names)
    }
  }
}

/*
 * Utility classes still follow the cascade! They are not modifiers and should not be
 * used as such. If possible, only apply properties via utilities that are not innate
 * to the element. Notable exceptions include display modes and visibility.
 */

.bad-utility {
  font-style: normal; // Tries (and fails) to modify existing target element property
}

.good-utility {
  margin-bottom: 1rem; // Target element does not have the property
}
```

### CSS Custom Properties (Variables)

Most of the theme’s "skin" — colors, spacing, borders, shadows, etc. — is controlled by an extensive set of [custom properties](https://developer.mozilla.org/en-US/docs/Web/CSS/--*) located in **[_properties.scss](src/scss/common/_properties.scss)** and **[_lightmode.scss](src/scss/common/_lightmode.scss)**. A few of these properties can be overwritten with the Customizer, as noted. This setup makes global changes easy but can cause render issues if handled without care. You can learn more about that in this [article by Lisi Linhart](https://lisilinhart.info/posts/css-variables-performance/).

*There was once a naive developer who thought updating a custom property with the current scroll position would be a great way to add parallax effects with only one line of JavaScript. He was right, except it also set his CPU on fire.*

### Functions

The responsive spacing and customizable colors are generated by functions. You will immediately see why that is necessary. Not using these functions (or replicating their results) can break the theme. Located in **[_functions.scss](src/scss/common/_functions.scss)**.

```scss
/*
 * get_clamp(min, max, wmin, wmax, unit: 'vw') returns a high-precision clamp.
 */

--layout-spacing-horizontal-small: #{get_clamp(10, 20, 320, 375, '%')}; // SCSS
--layout-spacing-horizontal-small: clamp(10px, 18.1818181818% - 48.1818181818px, 20px); // CSS

/*
 * get_relative_clamp(min, max, wmin, wmax, unit: 'vw', rel: 'rem') returns a high-precision clamp relative to 16px.
 */

font-size: get_relative_clamp(1.35, 1.75, 320, 960, 'vw', 'em'); // SCSS
font-size: clamp(1.35em, 1vw + 18.4px, 1.75em); // CSS

/*
 * hsl_code(hue, saturation, lightness) returns a dynamic HSL code.
 */

--bg-500: #{hsl_code(222, 13, 26.5)}; // SCSS
--bg-500: hsl(calc(222deg + var(--hue-rotate)) calc(13% * var(--saturation)) clamp(13.25%, 26.5% * var(--darken), 53%)); // CSS

/*
 * hsl_code_free(hue, saturation, lightness) returns a dynamic HSL code but without the HSL wrapper.
 */

--bg-900-free: #{hsl_code_free(219, 15, 17)}; // SCSS
--bg-900-free: calc(219deg + var(--hue-rotate)) calc(15% * var(--saturation)) clamp(8.5%, 17% * var(--darken), 58.5%) // CSS

/*
 * hsl_font_code(hue, saturation, lightness) returns a dynamic HSL font code.
 */

--fg-500: #{hsl_font_code(222, 18, 77)}; // SCSS
--fg-500: hsl(calc(222deg + var(--hue-rotate)) max(calc(18% * (var(--font-saturation) + var(--saturation) - 1)), 0%) clamp(0%, 77%, 100%)); // CSS
```

## JavaScript

Fictioneer is built on [Vanilla JS](http://vanilla-js.com/) and [Stimulus](https://stimulus.hotwired.dev/) (5.27.0+). It has no other hard dependencies, *especially* not jQuery which is to be avoided like the plague. Bad enough that WordPress and most plugins insist on the performance hog. If you need something from an external library, just copy the functions and credit the source. Avoid additional overhead. There is also an argument for refactoring the JS into classes and modules. But since everything is already working, this would be a labor of passion without immediate benefit.

### Libraries

* [Diff-Match-Patch](https://github.com/google/diff-match-patch)
* [Stimulus](https://stimulus.hotwired.dev/)

### Shorthands & Objects

* `_$()` for `document.querySelector()`
* `_$$()` for `document.querySelectorAll()`
* `_$$$()` for `document.getElementById()`
* `FcnUtils` contains utility functions
* `FcnGlobals` contains global variables
* `window.FictioneerApp.Controllers` contains singleton Stimulus Controllers

### Custom Events

* **scroll.rAF:** Window scroll event bound to [requestAnimationFrame](https://developer.mozilla.org/en-US/docs/Web/API/window/requestAnimationFrame).
* **resize.rAF:** Window resize event bound to [requestAnimationFrame](https://developer.mozilla.org/en-US/docs/Web/API/window/requestAnimationFrame).
* **fcnUserDataReady:** Fires when the user data has been successfully fetched via AJAX.
* **fcnUserDataFailed:** Fires when the user data could not be fetched via AJAX.
* **fcnUserDataError:** Fires when there was an error while fetching the user data via AJAX.

### AJAX Requests

AJAX requests can be made with the theme’s [Fetch API](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch) helper functions, returning an object parsed from a JSON response. For that to work, the server must always respond with a JSON (which can contain an HTML node, of course). You can use [wp_send_json_success()](https://developer.wordpress.org/reference/functions/wp_send_json_success/) and [wp_send_json_error()](https://developer.wordpress.org/reference/functions/wp_send_json_error/) for this, making it easier to evaluate the result client-side.

```js
FcnUtils.aGet(
  {
    'action': 'fictioneer_ajax_your_function',
    'nonce': FcnUtils.nonce(), // Optional
    'payload': 'foobar'
  },
  null, // Optional AJAX URL
  {} // Optional headers
)
.then(response => {
  // Check response.success
})
.catch(error => {
  // Check error
});
```

```js
FcnUtils.aPost(
  {
    'action': 'fictioneer_ajax_your_function',
    'nonce': FcnUtils.nonce(), // Optional
    'payload': 'foobar'
  },
  null, // Optional AJAX URL
  {} // Optional headers
)
.then(response => {
  // Check response.success
})
.catch(error => {
  // Check error
});
```

## Story Meta Fields

These are the protected meta fields used specifically for the **fcn_story** post type. Some field values are verbatim due to legacy reasons (the theme previously used the ACF plugin), so they rely on the `fcntr()` translation helper. Falsy values are not stored; instead, the meta field gets deleted from the database to save rows.

| META KEY | TYPE | PURPOSE
| --- | :---: | ---
| fictioneer_story_short_description | string | Short description used on cards.
| fictioneer_story_chapters | array | Array of associated chapter IDs (stringified).
| fictioneer_story_custom_pages | array | Array of associated custom page IDs (stringified).
| fictioneer_story_global_note | string | Content of the global story note displayed in all associated chapters.
| fictioneer_story_password_note | string | Content of the note shown if the story is password protected.
| fictioneer_story_ebook_upload_one | int | Attachment ID of the uploaded ebook (if any).
| fictioneer_story_epub_preface | string | Content for the generated ePUB preface section.
| fictioneer_story_epub_afterword | string | Content for the generated ePUB afterword section.
| fictioneer_story_epub_custom_css | string | Custom CSS injected into the generated ePUB.
| fictioneer_story_status | string | Either Ongoing, Completed, Oneshot, Hiatus, or Canceled due to legacy reasons.
| fictioneer_story_rating | string | Either Everyone, Teen, Mature, or Adult due to legacy reasons.
| fictioneer_story_copyright_notice | string | Small copyright notice on the story page.
| fictioneer_story_topwebfiction_link | string | Link to the story on [www.topwebfiction.com](https://www.topwebfiction.com/).
| fictioneer_story_redirect_link | string | Redirect link away from the story page (use with care).
| fictioneer_story_co_authors | array | Array of author IDs.
| fictioneer_story_sticky | boolean | Whether the story is sticky in (most) lists.
| fictioneer_story_hidden | boolean | Whether the story is unlisted but still reachable with the link.
| fictioneer_story_no_thumbnail | boolean | Whether to hide the thumbnail (cover) on the story page.
| fictioneer_story_no_tags | boolean | Whether to hide all taxonomies on the story page.
| fictioneer_story_no_epub | boolean | Whether to disable the ePUB generation and download.
| fictioneer_story_disable_collapse | boolean | Whether to disable the collapsing of long chapter lists.
| fictioneer_story_hide_chapter_icons | boolean | Whether to hide the chapter icons.
| fictioneer_story_disable_groups | boolean | Whether to disable the grouping of chapters.
| fictioneer_story_css | string | Custom CSS injected into the story page and all chapter pages.
| fictioneer_chapters_modified | datetime | Datetime (GMT) of when the chapter list was last modified.
| fictioneer_chapters_added | datetime | Datetime (GMT) of the last time a chapter was added to the chapter list.
| fictioneer_story_data_collection | array | Array of prepared story data (database cache).
| fictioneer_story_chapter_index_html | array | Array of the chapter index HTML with timestamp (database cache).

## Chapter Meta Fields

These are the protected meta fields used specifically for the **fcn_chapter** post type. Some field values are verbatim due to legacy reasons (the theme previously used the ACF plugin), so they rely on the `fcntr()` translation helper. Falsy values are not stored; instead, the meta field gets deleted from the database to save rows.

| META KEY | TYPE | PURPOSE
| --- | :---: | ---
| fictioneer_chapter_story | int | ID of the story the chapter belongs to.
| fictioneer_chapter_list_title | string | Alternative title for lists with little space.
| fictioneer_chapter_group | string | Chapter group name (case-sensitive).
| fictioneer_chapter_foreword | string | Content of the chapter foreword.
| fictioneer_chapter_afterword | string | Content of the chapter afterword.
| fictioneer_chapter_password_note | string | Content of the note shown if the story is password protected.
| fictioneer_chapter_icon | string | Font Awesome icon class string. Empty if default icon.
| fictioneer_chapter_text_icon | string | Text string in place of an icon.
| fictioneer_chapter_short_title | string | Shorter variant title. Not used by default, intended for child themes.
| fictioneer_chapter_prefix | string | Prefix for titles in chapter lists.
| fictioneer_chapter_co_authors | array | Array of author IDs.
| fictioneer_chapter_rating | string | Either Everyone, Teen, Mature, or Adult due to legacy reasons.
| fictioneer_chapter_warning | string | Short warning shown in chapter lists.
| fictioneer_chapter_warning_notes | string | Content for warning note in chapters.
| fictioneer_chapter_hidden | boolean | Whether the chapter is unlisted but still reachable with the link.
| fictioneer_chapter_no_chapter | boolean | Whether the chapter should not count as chapter.
| fictioneer_chapter_hide_title | boolean | Whether to hide the title at the top of the chapter.
| fictioneer_chapter_hide_support_links | boolean | Whether to hide the support links at the bottom of the chapter.
| fictioneer_chapter_disable_partial_caching | boolean | Whether to disable partial caching for the chapter.

## Hooked Actions & Filters

Fictioneer customizes WordPress by using as many standard action and filter hooks as possible, keeping the theme compatible with plugins adhering to the same principles. However, the theme was not initially built for a public release and despite great efforts to refactor the code, some conflicts are unavoidable. Please make sure to also look at the theme’s custom [action hooks](ACTIONS.md) and [filter hooks](FILTERS.md). Following is a list of (not) all theme actions and filters hooked to WordPress in a not particularly easy to read at fashion. Some of them are conditional.

| WORDPRESS HOOK | FICTIONEER ACTIONS (PRIORITY)
| ---: | :--- |
| `add_meta_boxes` | `fictioneer_add_seo_metabox` (10), `fictioneer_restrict_classic_metaboxes` (10), `fictioneer_add_story_meta_metabox` (10), `fictioneer_add_story_data_metabox` (10), `fictioneer_add_story_epub_metabox` (10), `fictioneer_add_chapter_meta_metabox` (10), `fictioneer_add_chapter_data_metabox` (10), `fictioneer_add_extra_metabox` (10), `fictioneer_add_support_links_metabox` (10), `fictioneer_add_featured_content_metabox` (10), `fictioneer_add_collection_data_metabox` (10), `fictioneer_add_recommendation_data_metabox` (10)
| `add_meta_boxes_comment` | `fictioneer_add_comment_meta_box` (10)
| `admin_bar_menu` | `fictioneer_adminbar_add_chapter_link` (99)
| `admin_enqueue_scripts` | `fictioneer_admin_scripts` (10), `fictioneer_admin_styles` (10), `fictioneer_hide_private_data` (20)
| `admin_footer.php` | `fictioneer_classic_editor_js_restrictions` (10), `fictioneer_hide_permalink_with_js` (10), `fictioneer_classic_editor_js_restrictions` (10)
| `admin_head` | `fictioneer_remove_update_notice` (10), `fictioneer_output_head_fonts` (5), `fictioneer_hide_permalink_with_css` (10), `fictioneer_hide_inserter_media_tab_with_css` (10), `fictioneer_classic_editor_css_restrictions` (10), `fictioneer_classic_editor_css_restrictions` (10), `fictioneer_remove_profile_blocks` (10)
| `admin_init` | `fictioneer_register_settings` (10), `fictioneer_skip_dashboard` (10), `fictioneer_initialize_roles` (10), `fictioneer_bring_out_legacy_trash` (10)
| `admin_menu` | `fictioneer_add_admin_menu` (10), `fictioneer_remove_dashboard_menu` (10), `fictioneer_remove_comments_menu_page` (10), `fictioneer_remove_sub_menus` (10)
| `admin_notices` | `fictioneer_admin_profile_notices` (10), `fictioneer_admin_settings_notices` (10), `fictioneer_admin_update_notice` (10)
| `admin_post_*` | `fictioneer_delete_all_epubs` (10), `fictioneer_tools_add_moderator_role` (10), `fictioneer_tools_move_story_tags_to_genres` (10), `fictioneer_tools_duplicate_story_tags_to_genres` (10), `fictioneer_tools_purge_theme_caches` (10), `fictioneer_tools_move_chapter_tags_to_genres` (10), `fictioneer_tools_duplicate_chapter_tags_to_genres` (10), `fictioneer_tools_append_default_genres` (10), `fictioneer_tools_append_default_tags` (10), `fictioneer_tools_remove_unused_tags` (10), `fictioneer_tools_reset_post_relationship_registry` (10), `fictioneer_admin_profile_unset_oauth` (10), `fictioneer_admin_profile_clear_data_node` (10), `fictioneer_update_frontend_profile` (10), `fictioneer_cancel_frontend_email_change` (10), `fictioneer_add_role` (10), `fictioneer_remove_role` (10), `fictioneer_rename_role` (10), `fictioneer_connection_get_patreon_tiers` (10), `fictioneer_connection_delete_patreon_tiers` (10)
| `after_setup_theme` | `fictioneer_theme_setup` (10)
| `bulk_edit_custom_box` | `fictioneer_add_patreon_bulk_edit_tiers` (10), `fictioneer_add_patreon_bulk_edit_amount` (10)
| `bulk_edit_posts` | `fictioneer_save_chapter_bulk_edit` (10)
| `comment_form_top` | `fictioneer_fix_comment_form_stimulus_controller` (10)
| `comment_post` | `fictioneer_comment_post` (20), `fictioneer_post_comment_to_discord` (99)
| `current_screen` | `fictioneer_restrict_admin_only_pages` (10), `fictioneer_restrict_comment_edit` (10)
| `customize_controls_enqueue_scripts` | `fictioneer_enqueue_customizer_scripts` (10)
| `customize_register` | `fictioneer_add_customizers` (20)
| `customize_save_after` | `fictioneer_watch_for_customizer_updates` (10)
| `delete_comment` | `fictioneer_delete_cached_story_card_by_comment` (10), `fictioneer_decrement_story_comment_count` (10)
| `delete_post` | `fictioneer_refresh_post_caches` (20), `fictioneer_track_chapter_and_story_updates` (10), `fictioneer_update_modified_date_on_story_for_chapter` (10), `fictioneer_purge_transients_after_update` (10)
| `do_feed_rss2` | `fictioneer_main_rss_template` (10)
| `do_meta_boxes` | `fictioneer_remove_custom_fields_meta_boxes` (1)
| `edit_comment` | `fictioneer_comment_edit` (20), `fictioneer_edit_comment` (10)
| `edit_user_profile` | `fictioneer_custom_profile_fields` (20)
| `edit_user_profile_update` | `fictioneer_update_admin_user_profile` (10), `fictioneer_update_my_user_profile` (10), `fictioneer_update_admin_unlocked_posts` (10)
| `get_header` | `fictioneer_maintenance_mode` (10)
| `get_template_part` | `fictioneer_legacy_icon_menu_partial` (10)
| `init` | `fictioneer_add_character_taxonomy` (0), `fictioneer_add_content_warning_taxonomy` (0), `fictioneer_add_fandom_taxonomy` (0), `fictioneer_add_genre_taxonomy` (0), `fictioneer_add_logout_endpoint`, `fictioneer_fcn_chapter_post_type` (0), `fictioneer_fcn_collection_post_type` (0), `fictioneer_fcn_recommendation_post_type` (0), `fictioneer_fcn_story_post_type` (0), `fictioneer_add_epub_download_endpoint` (10), `fictioneer_add_oauth2_endpoint` (10), `fictioneer_restrict_admin_panel` (10), `fictioneer_disable_heartbeat` (1), `fictioneer_modify_allowed_tags` (20), `fictioneer_story_rss` (10), `fictioneer_remove_custom_fields_supports` (10), `fictioneer_add_sitemap_rewrite_rule` (10), `fictioneer_fast_ajax` (99999), `fictioneer_add_chapter_rewrite_tags` (10), `fictioneer_add_chapter_rewrite_rules` (10), `fictioneer_rewrite_chapter_permalink` (10), `fictioneer_disable_emojis` (10), `fictioneer_disable_page_optimize_plugin` (10), `fictioneer_fix_logged_in_cookie` (10)
| `login_form` | `fictioneer_after_logout_cleanup` (10)
| `login_head` | `fictioneer_wp_login_scripts` (10)
| `manage_comments_custom_column` | `fictioneer_add_comments_report_column_content` (10)
| `manage_{$type}_posts_custom_column` | `fictioneer_manage_posts_column_patreon` (10), `fictioneer_manage_posts_column_chapter_story` (10)
| `personal_options_update` | `fictioneer_update_admin_user_profile` (10), `fictioneer_update_my_user_profile` (10), `fictioneer_update_admin_unlocked_posts` (10)
| `pre_get_posts` | `fictioneer_extend_search_query` (11), `fictioneer_read_others_files` (10), `fictioneer_read_others_files_list_view` (10), `fictioneer_filter_chapters_by_story` (10), `fictioneer_extend_taxonomy_pages` (10), `fictioneer_edit_others_fictioneer_posts` (10), `fictioneer_add_sof_to_taxonomy_query` (10)
| `private_to_draft` | `fictioneer_chapter_to_draft` (10)
| `profile_update` | `fictioneer_on_profile_change` (10)
| `publish_to_draft` | `fictioneer_chapter_to_draft` (10)
| `rest_api_init` | `fictioneer_register_endpoint_get_story_comments` (10)
| `restrict_manage_posts` | `fictioneer_add_chapter_story_filter_dropdown` (10)
| `save_post` | `fictioneer_refresh_chapters_schema` (20), `fictioneer_refresh_chapter_schema` (20), `fictioneer_refresh_collections_schema` (20), `fictioneer_refresh_post_caches` (20), `fictioneer_refresh_post_schema` (20), `fictioneer_refresh_recommendations_schema` (20), `fictioneer_refresh_recommendation_schema` (20), `fictioneer_refresh_stories_schema` (20), `fictioneer_refresh_story_schema` (20), `fictioneer_save_seo_metabox` (10), `fictioneer_save_word_count` (10), `fictioneer_track_chapter_and_story_updates` (10), `fictioneer_update_modified_date_on_story_for_chapter` (10), `fictioneer_update_shortcode_relationships` (10), `fictioneer_purge_transients_after_update` (10), `fictioneer_save_story_metaboxes` (10), `fictioneer_save_chapter_metaboxes` (10), `fictioneer_save_extra_metabox` (10), `fictioneer_save_support_links_metabox` (10), `fictioneer_save_collection_metaboxes` (10), `fictioneer_save_recommendation_metaboxes` (10), `fictioneer_save_post_metaboxes` (10), `fictioneer_delete_cached_story_card_after_update` (10), `fictioneer_post_chapter_to_discord` (99), `fictioneer_post_story_to_discord` (99), `fictioneer_clean_up_post_password_expiration` (99)
| `send_headers` | `fictioneer_block_pages_from_indexing` (10)
| `set_logged_in_cookie` | `fictioneer_set_logged_in_cookie` (10)
| `show_user_profile` | `fictioneer_custom_profile_fields` (20)
| `shutdown` | `fictioneer_save_story_card_cache` (10)
| `switch_theme` | `fictioneer_theme_deactivation` (10)
| `template_redirect` | `fictioneer_generate_epub` (10), `fictioneer_oauth2_process` (10), `fictioneer_logout` (10), `fictioneer_disable_attachment_pages` (10), `fictioneer_gate_unpublished_content` (10), `fictioneer_serve_sitemap` (10), `fictioneer_redirect_story` (10), `fictioneer_redirect_scheduled_chapter_404` (10)
| `transition_post_status` | `fictioneer_log_story_chapter_status_changes` (10), `fictioneer_chapter_future_to_publish` (10)
| `trashed_post` | `fictioneer_refresh_post_caches` (20), `fictioneer_track_chapter_and_story_updates` (10), `fictioneer_update_modified_date_on_story_for_chapter` (10), `fictioneer_purge_transients_after_update` (10), `fictioneer_remove_chapter_from_story` (10)
| `untrash_post` | `fictioneer_refresh_post_caches` (20), `fictioneer_track_chapter_and_story_updates` (10), `fictioneer_update_modified_date_on_story_for_chapter` (10), `fictioneer_purge_transients_after_update` (10)
| `update_option_*` | `fictioneer_update_option_disable_extended_chapter_list_meta_queries` (10), `fictioneer_update_option_disable_extended_story_list_meta_queries` (10)
| `wp` | `fictioneer_conditional_require_frontend_hooks` (10)
| `wp_ajax_*` | `fictioneer_ajax_clear_my_checkmarks` (10), `fictioneer_ajax_clear_my_comments` (10), `fictioneer_ajax_clear_my_comment_subscriptions` (10), `fictioneer_ajax_clear_my_follows` (10), `fictioneer_ajax_clear_my_reminders` (10), `fictioneer_ajax_delete_epub` (10), `fictioneer_ajax_delete_my_account` (10), `fictioneer_ajax_delete_my_comment` (10), `fictioneer_ajax_edit_comment` (10), `fictioneer_ajax_get_comment_form` (10), `fictioneer_ajax_get_comment_section` (10), `fictioneer_ajax_get_finished_checkmarks_list` (10), `fictioneer_ajax_get_follows_list` (10), `fictioneer_ajax_get_follows_notifications` (10), `fictioneer_ajax_get_reminders_list` (10), `fictioneer_ajax_mark_follows_read` (10), `fictioneer_ajax_moderate_comment` (10), `fictioneer_ajax_report_comment` (10), `fictioneer_ajax_save_bookmarks` (10), `fictioneer_ajax_set_checkmark` (10), `fictioneer_ajax_submit_comment` (10), `fictioneer_ajax_toggle_follow` (10), `fictioneer_ajax_toggle_reminder` (10), `fictioneer_ajax_unset_my_oauth` (10), `fictioneer_ajax_get_user_data` (10), `fictioneer_ajax_purge_schema` (10), `fictioneer_ajax_purge_all_schemas` (10), `fictioneer_ajax_reset_theme_colors` (10), `fictioneer_ajax_search_posts_to_unlock` (10), `fictioneer_ajax_get_chapter_group_options` (10), `fictioneer_ajax_clear_cookies` (10)
| `wp_ajax_nopriv_*` | `fictioneer_ajax_get_comment_form` (10), `fictioneer_ajax_get_comment_section` (10), `fictioneer_ajax_submit_comment` (10), `fictioneer_ajax_get_user_data` (10)
| `wp_before_admin_bar_render` | `fictioneer_remove_admin_bar_links` (10), `fictioneer_remove_dashboard_from_admin_bar` (10), `fictioneer_remove_comments_from_admin_bar` (10)
| `wp_dashboard_setup` | `fictioneer_remove_dashboard_widgets` (10)
| `wp_default_scripts` | `fictioneer_remove_jquery_migrate` (10)
| `wp_enqueue_scripts` | `fictioneer_add_custom_scripts` (10), `fictioneer_style_queue` (10), `fictioneer_output_customize_css` (9999), `fictioneer_output_customize_preview_css` (9999), `fictioneer_elementor_override_styles` (9999)
| `wp_footer` | `fictioneer_render_category_submenu` (10), `fictioneer_render_tag_submenu` (10), `fictioneer_render_genre_submenu` (10), `fictioneer_render_fandom_submenu` (10), `fictioneer_render_character_submenu` (10), `fictioneer_render_warning_submenu` (10)
| `wp_head` | `fictioneer_output_head_seo` (5), `fictioneer_output_rss` (10), `fictioneer_output_schemas` (10), `fictioneer_add_fiction_css` (10), `fictioneer_output_head_fonts` (5), `fictioneer_output_head_translations` (10), `fictioneer_remove_mu_registration_styles` (1), `fictioneer_output_mu_registration_style` (10), `fictioneer_output_head_meta` (1), `fictioneer_output_head_critical_scripts` (9999). `fictioneer_output_head_anti_flicker` (10), `fictioneer_cleanup_discord_meta` (10), `fictioneer_output_critical_skin_scripts` (9999), `fictioneer_output_stimulus` (5)
| `wp_insert_comment` | `fictioneer_delete_cached_story_card_by_comment` (10), `fictioneer_increment_story_comment_count` (10)
| `wp_logout` | `fictioneer_remove_logged_in_cookie` (10)
| `wp_update_nav_menu` | `fictioneer_purge_nav_menu_transients` (10)

<br>

| WORDPRESS HOOK | FICTIONEER FILTERS (PRIORITY)
| ---: | :--- |
| `admin_body_class` | `fictioneer_add_classes_to_admin_body` (10)
| `admin_comment_types_dropdown` | `fictioneer_add_private_to_comment_filter` (10), `fictioneer_add_user_deleted_to_comment_filter` (10)
| `allowed_block_types_all` | `fictioneer_allowed_block_types` (10), `fictioneer_restrict_block_types` (20)
| `block_editor_settings_all` | `fictioneer_disable_font_library` (10)
| `body_class` | `fictioneer_add_classes_to_body` (10)
| `cancel_comment_reply_link` | `__return_empty_string` (10)
| `comment_email` | `__return_false` (10)
| `comment_form_default_fields` | `fictioneer_change_comment_fields` (10)
| `comment_form_defaults` | `fictioneer_comment_form_args` (10)
| `comment_form_submit_field` | `fictioneer_change_submit_field` (10)
| `comment_post_redirect` | `fictioneer_redirect_comment` (10)
| `comment_reply_link` | `fictioneer_comment_login_to_reply` (10)
| `comment_row_actions` | `fictioneer_remove_quick_edit` (10)
| `comment_text` | `fictioneer_bbcodes` (10)
| `customize_refresh_nonces` | `fictioneer_add_customizer_refresh_nonces` (10)
| `default_hidden_columns` | `fictioneer_default_hide_patreon_posts_columns` (10), `fictioneer_default_hide_chapter_story_column` (10)
| `excerpt_length` | `fictioneer_custom_excerpt_length` (10)
| `excerpt_more` | `fictioneer_excerpt_ellipsis` (10)
| `feed_links_show_comments_feed` | `__return_false` (10)
| `get_avatar` | `fictioneer_avatar_fallback` (1)
| `get_avatar_url` | `fictioneer_get_avatar_url` (10)
| `get_comment_author_IP` | `__return_empty_string` (10)
| `get_comment_text` | `fictioneer_replace_comment_line_breaks` (10)
| `get_the_excerpt` | `fictioneer_fix_excerpt` (10)
| `is_protected_meta` | `fictioneer_make_theme_meta_protected` (10)
| `kses_allowed_protocols` | `fictioneer_extend_allowed_protocols` (10)
| `logout_url` | `fictioneer_logout_redirect` (10)
| `manage_edit-comments_columns` | `fictioneer_add_comments_report_column` (10)
| `manage_{$type}_posts_columns` | `fictioneer_add_posts_columns_patreon` (10), `fictioneer_add_posts_column_chapter_story` (10)
| `manage_pages_columns` | `fictioneer_remove_comments_column` (10)
| `manage_posts_columns` | `fictioneer_remove_comments_column` (10)
| `manage_users_columns` | `fictioneer_hide_users_columns` (10)
| `map_meta_cap` | `fictioneer_edit_others_files` (10), `fictioneer_delete_others_files` (9999), `fictioneer_override_default_taxonomy_capability_check` (9999), `fictioneer_edit_comments` (10)
| `navigation_markup_template` | `fictioneer_pagination_markup` (10)
| `nav_menu_link_attributes` | `fictioneer_add_menu_link_attributes` (10)
| `post_comments_feed_link` | `__return_empty_string` (10)
| `post_password_required` | `fictioneer_bypass_password` (10), `fictioneer_expire_post_password` (5)
| `post_stuck` | `fictioneer_prevent_post_sticky` (10)
| `postbox_classes_{$screen_id}_{$box_id}` | `fictioneer_append_metabox_classes` (10)
| `pre_comment_user_ip` | `__return_empty_string` (10)
| `pre_get_lastpostmodified` | `fictioneer_cache_lastpostmodified` (10)
| `pre_insert_term` | `fictioneer_restrict_tag_creation` (9999)
| `preprocess_comment` | `fictioneer_preprocess_comment` (30), `fictioneer_validate_comment_form` (20)
| `protected_title_format` | `fictioneer_remove_protected_text` (10)
| `query_vars` | `fictioneer_query_vars` (10)
| `removable_query_args` | `fictioneer_removable_args` (10)
| `render_block` | `fictioneer_download_block_wrapper` (10)
| `rest_authentication_errors` | `fictioneer_restrict_rest_api` (10)
| `script_loader_tag` | `fictioneer_data_jetpack_boost_tag` (10)
| `show_admin_bar` | `__return_false` (10)
| `strip_shortcodes_tagnames` | `fictioneer_exempt_shortcodes_from_removal` (10)
| `style_loader_tag` | `fictioneer_add_font_awesome_integrity` (10)
| `the_content` | `fictioneer_append_footnotes_to_content` (20), `fictioneer_embed_consent_wrappers` (20), `fictioneer_add_lightbox_to_post_images` (15), `fictioneer_add_chapter_paragraph_id` (10), `fictioneer_replace_br_with_whitespace` (6), `fictioneer_fix_line_breaks` (1)
| `the_password_form` | `fictioneer_password_form` (10), `fictioneer_unlock_with_patreon` (20)
| `the_content_more_link` | `fictioneer_wrap_read_more_link` (10)
| `the_excerpt_rss` | `fictioneer_filter_rss_excerpt` (10)
| `theme_templates` | `fictioneer_disallow_page_template_select` (1)
| `tiny_mce_plugins` | `fictioneer_disable_tiny_mce_emojis` (10)
| `update_post_metadata` | `fictioneer_prevent_page_template_update` (1)
| `upload_size_limit` | `fictioneer_upload_size_limit` (10)
| `use_block_editor_for_post_type` | `__return_false` (10)
| `user_contactmethods` | `fictioneer_user_contact_methods` (10)
| `user_has_cap` | `fictioneer_edit_only_comments` (10)
| `wp_list_comments_args` | `fictioneer_comment_list_args` (10)
| `wp_handle_upload_prefilter` | `fictioneer_upload_restrictions` (10)
| `wp_insert_post_data` | `fictioneer_remove_restricted_block_content` (1), `fictioneer_strip_shortcodes_on_save` (1), `fictioneer_see_some_evil` (1), `fictioneer_prevent_publish_date_update` (1), `fictioneer_prevent_parent_and_order_update` (1), `fictioneer_prevent_track_and_ping_updates` (1)
| `wp_is_application_passwords_available` | `__return_false` (10)
| `wp_resource_hints` | `fictioneer_remove_emoji_resource_hint` (10)
| `wp_robots` | `wp_robots_no_robots` (10)
| `wp_sitemaps_enabled` | `__return_false` (10)
| `wp_unique_post_slug` | `fictioneer_protect_reserved_post_slugs` (10)
| `xmlrpc_enabled` | `__return_false` (10)

## Caching

Fictioneer is cache aware. This means the theme provides an interface to aid cache plugins in purging stale caches across the site, not just the updated post. It can even operate as static website with dynamic content being fetched via AJAX. By default, four cache plugins are considered: [WP Super Cache](https://wordpress.org/plugins/wp-super-cache/), [W3 Total Cache](https://wordpress.org/plugins/w3-total-cache/), [LiteSpeed Cache](https://wordpress.org/plugins/litespeed-cache/), and [WP Rocket](https://wp-rocket.me/). Whenever you publish or update content, a series of actions is triggered to purge anything related.

*"There are only two hard things in Computer Science: cache invalidation and naming things" — Phil Karlton.*

### References
* See [_service-caching.php](includes/functions/_service-caching.php)
* [Cache Purge All Hook](ACTIONS.md#do_action-fictioneer_cache_purge_all-): `fictioneer_cache_purge_all`
* [Cache Purge Post Hook](ACTIONS.md#do_action-fictioneer_cache_purge_post-post_id-): `fictioneer_cache_purge_post`

### Functions

#### `fictioneer_caching_active(): bool`
Returns true or false depending on whether the site is currently cached. Either because a known cache plugin is active or the **Enable cache compatibility mode** option is checked.

#### `fictioneer_private_caching_active(): bool`
Returns true or false depending on whether *private* caching is active. Private caching creates individual cache files for each user, which can result in high disk usage. Always false if **Enable public cache compatibility mode** is checked.

#### `fictioneer_purge_all_caches()`
Helper to trigger the "purge all" functions of known cache plugins. Can be extended with the `fictioneer_cache_purge_all` hook.

#### `fictioneer_purge_post_cache( $post_id )`
Helper to trigger the "purge post" functions of known cache plugins. Can be extended with the `fictioneer_cache_purge_post` hook.

#### `fictioneer_purge_template_caches( $template )`
Purges all caches for posts with the given template (without the `.php`). This is by default done for `stories`, `chapters`, `collections`, and `recommendations`.

#### `fictioneer_refresh_post_caches( $post_id )`
Hooked if caching is active and called whenever a post is published, updated, trashed, restored, or deleted. Purges the cache for the given post and any other that has or may have a relationship with it. This includes the front page, the associated story, all associated chapters, relevant list pages, collections, featured lists, and theme shortcodes.

#### `fictioneer_get_relationship_registry(): Array`
Returns an array with maintained relationships between posts. This "registry" is updated whenever a post is published, updated, trashed, restored, or deleted — with the post in question. If the registry is cleared or becomes corrupted, old relationships cannot be restored unless each post is opened and re-saved.
