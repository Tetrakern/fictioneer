# Development

This is a quick guide to get you started if you want to contribute to the theme, fork off your own version, or create a child theme. Nothing too comprehensive, but enough to introduce the core concepts and practices.

### Table of Contents

* [Coding Standards & Documentation](#coding-standards--documentation)
* [Build Pipeline](#build-pipeline)
* [CSS/SCSS](#cssscss)
  * [CSS Custom Properties (Variables)](#css-custom-properties-variables)
  * [Functions](#functions)
* [JavaScript](#javascript)
  * [Libraries](#libraries)
  * [Shorthands](#shorthands)
  * [Custom Events](#custom-events)
  * [AJAX Requests](#ajax-requests)
* [Hooked Actions & Filters](#hooked-actions--filters)
* [Caching](#caching)
  * [References](#references)
  * [Functions](#functions-1)

### Additional Resources

* [Theme action hooks](ACTIONS.md)
* [Theme filter hooks](FILTERS.md)
* [Child theme example](https://github.com/Tetrakern/fictioneer-child-theme)
* [Liminal child theme](https://github.com/Tetrakern/fictioneer-liminal)

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
* JavaScript (ES11) does not need to mirror the PHP formatting.

## Build Pipeline

*This theme was compiled with CodeKit. I’m sick and tired of package managers. There is no dependency hell and I wish to keep it that way. Anything beyond building assets, which would be reasonable, will be rejected. I’m not sorry. If you disagree with that sentiment, you are free to branch or fork and do as you like.*

## CSS/SCSS

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

--bg-500: #{hsl_code(222, 13, 27)}; // SCSS
--bg-500: hsl(calc(220deg + var(--hue-rotate)) calc(9% * var(--saturation)) clamp(23%, 46% * var(--darken), 73%)); // CSS

/*
 * hsl_code_free(hue, saturation, lightness) returns a dynamic HSL code but without the HSL wrapper.
 */

--bg-900-free: #{hsl_code_free(222, 15, 17)}; // SCSS
--bg-900-free: calc(222deg + var(--hue-rotate)) calc(15% * var(--saturation)) clamp(8.5%, 17% * var(--darken), 58.5%) // CSS

/*
 * hsl_font_code(hue, saturation, lightness) returns a dynamic HSL font code.
 */

--fg-500: #{hsl_font_code(222, 14, 69)}; // SCSS
--fg-500: hsl(calc(215deg + var(--hue-rotate)) calc(28% * (var(--font-saturation) + var(--saturation) - 1)) 17%); // CSS
```

## JavaScript

Fictioneer is built on [Vanilla JS](http://vanilla-js.com/) without hard dependencies, *especially* not jQuery which is to be avoided like the plague. Bad enough that WordPress and most plugins insist on the performance hog. If you need something from an external library, just copy the functions and credit the source. Avoid additional overhead. There is also an argument for refactoring the JS into classes and modules. But since everything is already working, this would be a labor of passion without immediate benefit.

### Libraries

* [Diff-Match-Patch](https://github.com/google/diff-match-patch)

### Shorthands

* `_$()` for `document.querySelector()`
* `_$$()` for `document.querySelectorAll()`
* `_$$$()` for `document.getElementById()`

### Custom Events

* **scroll.rAF:** Window scroll event bound to [requestAnimationFrame](https://developer.mozilla.org/en-US/docs/Web/API/window/requestAnimationFrame).
* **resize.rAF:** Window resize event bound to [requestAnimationFrame](https://developer.mozilla.org/en-US/docs/Web/API/window/requestAnimationFrame).
* **fcnAuthReady:** Fires when the user has been successfully authenticated via AJAX.
* **fcnUserDataReady:** Fires when the user data has been successfully fetched via AJAX.
* **fcnUserDataFailed:** Fires when the user data could not be fetched via AJAX.
* **fcnUserDataError:** Fires when there was an error while fetching the user data via AJAX.

### AJAX Requests

AJAX requests can be made with the theme’s [Fetch API](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch) helper functions, returning an object parsed from a JSON response. For that to work, the server must always respond with a JSON (which can contain an HTML node, of course). You can use [wp_send_json_success()](https://developer.wordpress.org/reference/functions/wp_send_json_success/) and [wp_send_json_error()](https://developer.wordpress.org/reference/functions/wp_send_json_error/) for this, making it easier to evaluate the result client-side.

```js
fcn_ajaxGet(
  {
    'action': 'fictioneer_ajax_your_function',
    'nonce': fcn_getNonce(), // Optional
    'payload': 'foobar'
  },
  null, // Optional AJAX URL
  {} // Optional headers
)
.then((response) => {
  // Check response.success
})
.catch((error) => {
  // Check error
});
```

```js
fcn_ajaxPost(
  {
    'action': 'fictioneer_ajax_your_function',
    'nonce': fcn_getNonce(), // Optional
    'payload': 'foobar'
  },
  null, // Optional AJAX URL
  {} // Optional headers
)
.then((response) => {
  // Check response.success
})
.catch((error) => {
  // Check error
});
```

## Hooked Actions & Filters

Fictioneer customizes WordPress by using as many standard action and filter hooks as possible, keeping the theme compatible with plugins adhering to the same principles. However, the theme was not initially built for a public release and despite great efforts to refactor the code, some conflicts are unavoidable. Please make sure to also look at the theme’s custom [action hooks](ACTIONS.md) and [filter hooks](FILTERS.md). Following is a list of (not) all theme actions and filters hooked to WordPress in a not particularly easy to read at fashion. Some of them are conditional.

| WORDPRESS HOOK | FICTIONEER ACTIONS
| ---: | :--- |
| `add_meta_boxes` | `fictioneer_add_seo_metabox`, `fictioneer_restrict_classic_metaboxes`, `fictioneer_add_story_meta_metabox`, `fictioneer_add_story_data_metabox`, `fictioneer_add_story_epub_metabox`, `fictioneer_add_chapter_meta_metabox`, `fictioneer_add_chapter_data_metabox`, `fictioneer_add_advanced_metabox`, `fictioneer_add_support_links_metabox`, `fictioneer_add_featured_content_metabox`, `fictioneer_add_collection_data_metabox`, `fictioneer_add_recommendation_data_metabox`
| `add_meta_boxes_comment` | `fictioneer_add_comment_meta_box`
| `admin_enqueue_scripts` | `fictioneer_admin_scripts`, `fictioneer_admin_styles`, `fictioneer_disable_moderator_comment_edit`, `fictioneer_hide_private_data`
| `admin_footer-post.php` | `fictioneer_classic_editor_js_restrictions`, `fictioneer_hide_permalink_with_js`
| `admin_footer-post-new.php` | `fictioneer_classic_editor_js_restrictions`
| `admin_head` | `fictioneer_remove_update_notice`, `fictioneer_hide_epub_inputs`, `fictioneer_hide_story_sticky_checkbox`
| `admin_head-post.php` | `fictioneer_hide_permalink_with_css`, `fictioneer_classic_editor_css_restrictions`
| `admin_head-post-new.php` | `fictioneer_classic_editor_css_restrictions`
| `admin_head-profile.php` | `fictioneer_remove_profile_blocks`
| `admin_init` | `fictioneer_register_settings`, `fictioneer_skip_dashboard`, `fictioneer_initialize_roles`, `fictioneer_bring_out_legacy_trash`
| `admin_menu` | `fictioneer_add_admin_menu`, `fictioneer_remove_dashboard_menu`, `fictioneer_remove_comments_menu_page`, `fictioneer_remove_sub_menus`
| `admin_notices` | `fictioneer_admin_profile_notices`, `fictioneer_admin_settings_notices`, `fictioneer_admin_update_notice`
| `admin_post_*` | `fictioneer_delete_all_epubs`, `admin_post_purge_all_seo_schemas`, `fictioneer_tools_add_moderator_role`, `fictioneer_tools_move_story_tags_to_genres`, `fictioneer_tools_duplicate_story_tags_to_genres`, `fictioneer_tools_purge_story_data_caches`, `fictioneer_tools_move_chapter_tags_to_genres`, `fictioneer_tools_duplicate_chapter_tags_to_genres`, `fictioneer_tools_append_default_genres`, `fictioneer_tools_append_default_tags`, `fictioneer_tools_remove_unused_tags`, `fictioneer_tools_reset_post_relationship_registry`, `fictioneer_tools_fix_users`, `fictioneer_tools_fix_stories`, `fictioneer_tools_fix_chapters`, `fictioneer_tools_fix_collections`, `fictioneer_tools_fix_pages`, `fictioneer_tools_fix_posts`, `fictioneer_tools_fix_recommendations`, `fictioneer_admin_profile_unset_oauth`, `fictioneer_admin_profile_clear_data_node`, `fictioneer_update_frontend_profile`, `fictioneer_cancel_frontend_email_change`, `fictioneer_add_role`, `fictioneer_remove_role`, `fictioneer_rename_role`
| `after_setup_theme` | `fictioneer_theme_setup`
| `comment_post` | `fictioneer_comment_post`, `fictioneer_post_comment_to_discord`
| `current_screen` | `fictioneer_restrict_admin_only_pages`, `fictioneer_restrict_comment_edit`
| `customize_register` | `fictioneer_add_customizers`
| `customize_save_after` | `fictioneer_watch_for_customer_updates`
| `delete_post` | `fictioneer_refresh_post_caches`, `fictioneer_track_chapter_and_story_updates`, `fictioneer_update_modified_date_on_story_for_chapter`, `fictioneer_purge_transients`
| `do_feed_rss2` | `fictioneer_main_rss_template`
| `edit_comment` | `fictioneer_comment_edit`, `fictioneer_edit_comment`
| `edit_user_profile` | `fictioneer_custom_profile_fields`
| `edit_user_profile_update` | `fictioneer_update_admin_user_profile`, `fictioneer_update_my_user_profile`
| `get_header` | `fictioneer_maintenance_mode`
| `init` | `fictioneer_add_character_taxonomy`, `fictioneer_add_content_warning_taxonomy`, `fictioneer_add_epub_download_endpoint`, `fictioneer_add_fandom_taxonomy`, `fictioneer_add_genre_taxonomy`, `fictioneer_add_logout_endpoint`, `fictioneer_add_oauth2_endpoint`, `fictioneer_restrict_admin_panel`, `fictioneer_disable_heartbeat`, `fictioneer_fcn_chapter_post_type`, `fictioneer_fcn_collection_post_type`, `fictioneer_fcn_recommendation_post_type`, `fictioneer_fcn_story_post_type`, `fictioneer_modify_allowed_tags`, `fictioneer_story_rss`
| `login_form` | `fictioneer_after_logout_cleanup`
| `login_head` | `fictioneer_wp_login_scripts`
| `manage_comments_custom_column` | `fictioneer_add_comments_report_column_content`
| `personal_options_update` | `fictioneer_update_admin_user_profile`, `fictioneer_update_my_user_profile`
| `pre_get_posts` | `fictioneer_extend_search_query`, `fictioneer_read_others_files`, `fictioneer_read_others_files_list_view`, `fictioneer_filter_chapters_by_story`
| `private_to_draft` | `fictioneer_chapter_to_draft`
| `publish_to_draft` | `fictioneer_chapter_to_draft`
| `rest_api_init` | `fictioneer_register_endpoint_get_story_comments`
| `restrict_manage_posts` | `fictioneer_add_chapter_story_filter_dropdown`
| `save_post` | `fictioneer_create_sitemap`, `fictioneer_refresh_chapters_schema`, `fictioneer_refresh_chapter_schema`, `fictioneer_refresh_collections_schema`, `fictioneer_refresh_post_caches`, `fictioneer_refresh_post_schema`, `fictioneer_refresh_recommendations_schema`, `fictioneer_refresh_recommendation_schema`, `fictioneer_refresh_stories_schema`, `fictioneer_refresh_story_schema`, `fictioneer_save_seo_metabox`, `fictioneer_save_word_count`, `fictioneer_track_chapter_and_story_updates`, `fictioneer_update_modified_date_on_story_for_chapter`, `fictioneer_update_shortcode_relationships`, `fictioneer_purge_transients`, `fictioneer_post_story_to_discord`, `fictioneer_post_chapter_to_discord`, `fictioneer_save_story_metaboxes`, `fictioneer_save_chapter_metaboxes`, `fictioneer_save_advanced_metabox`, `fictioneer_save_support_links_metabox`, `fictioneer_save_collection_metaboxes`, `fictioneer_save_recommendation_metaboxes`, `fictioneer_save_post_metaboxes`
| `show_user_profile` | `fictioneer_custom_profile_fields`
| `switch_theme` | `fictioneer_theme_deactivation`
| `template_redirect` | `fictioneer_disable_date_archives`, `fictioneer_generate_epub`, `fictioneer_handle_oauth`, `fictioneer_logout`, `fictioneer_disable_attachment_pages`, `fictioneer_gate_unpublished_content`
| `transition_post_status` | `fictioneer_log_story_chapter_status_changes`
| `trashed_post` | `fictioneer_refresh_post_caches`, `fictioneer_track_chapter_and_story_updates`, `fictioneer_update_modified_date_on_story_for_chapter`, `fictioneer_purge_transients`, `fictioneer_remove_chapter_from_story`
| `untrash_post` | `fictioneer_refresh_post_caches`, `fictioneer_track_chapter_and_story_updates`, `fictioneer_update_modified_date_on_story_for_chapter`, `fictioneer_purge_transients`
| `wp_ajax_*` | `fictioneer_ajax_clear_my_checkmarks`, `fictioneer_ajax_clear_my_comments`, `fictioneer_ajax_clear_my_comment_subscriptions`, `fictioneer_ajax_clear_my_follows`, `fictioneer_ajax_clear_my_reminders`, `fictioneer_ajax_delete_epub`, `fictioneer_ajax_delete_my_account`, `fictioneer_ajax_delete_my_comment`, `fictioneer_ajax_edit_comment`, `fictioneer_ajax_get_avatar`, `fictioneer_ajax_get_comment_form`, `fictioneer_ajax_get_comment_section`, `fictioneer_ajax_get_finished_checkmarks_list`, `fictioneer_ajax_get_follows_list`, `fictioneer_ajax_get_follows_notifications`, `fictioneer_ajax_get_reminders_list`, `fictioneer_ajax_mark_follows_read`, `fictioneer_ajax_moderate_comment`, `fictioneer_ajax_report_comment`, `fictioneer_ajax_save_bookmarks`, `fictioneer_ajax_set_checkmark`, `fictioneer_ajax_submit_comment`, `fictioneer_ajax_toggle_follow`, `fictioneer_ajax_toggle_reminder`, `fictioneer_ajax_unset_my_oauth`, `fictioneer_ajax_get_user_data`, `fictioneer_ajax_get_auth`, `fictioneer_ajax_purge_schema`, `fictioneer_ajax_purge_all_schemas`
| `wp_ajax_nopriv_*` | `fictioneer_ajax_get_comment_form`, `fictioneer_ajax_get_comment_section`, `fictioneer_ajax_submit_comment`, `fictioneer_ajax_get_auth`
| `wp_before_admin_bar_render` | `fictioneer_remove_admin_bar_links`, `fictioneer_remove_dashboard_from_admin_bar`, `fictioneer_remove_comments_from_admin_bar`
| `wp_dashboard_setup` | `fictioneer_remove_dashboard_widgets`
| `wp_default_scripts` | `fictioneer_remove_jquery_migrate`
| `wp_enqueue_scripts` | `fictioneer_add_custom_scripts`, `fictioneer_customizer_queue`, `fictioneer_style_queue`
| `wp_head` | `fictioneer_output_head_seo`, `fictioneer_output_rss`, `fictioneer_output_schemas`, `fictioneer_add_fiction_css`
| `wp_update_nav_menu` | `fictioneer_purge_nav_menu_transients`

<br>

| WORDPRESS HOOK | FICTIONEER FILTERS
| ---: | :--- |
| `admin_body_class` | `fictioneer_add_classes_to_admin_body`
| `admin_comment_types_dropdown` | `fictioneer_add_private_to_comment_filter`
| `allowed_block_types_all` | `fictioneer_allowed_block_types`, `fictioneer_restrict_block_types`
| `body_class` | `fictioneer_add_classes_to_body`
| `cancel_comment_reply_link` | `__return_empty_string`
| `comment_email` | `__return_false`
| `comment_form_default_fields` | `fictioneer_change_comment_fields`
| `comment_form_defaults` | `fictioneer_comment_form_args`
| `comment_form_submit_field` | `fictioneer_change_submit_field`
| `comment_post_redirect` | `fictioneer_redirect_comment`
| `comment_reply_link` | `fictioneer_comment_login_to_reply`
| `comment_row_actions` | `fictioneer_remove_quick_edit`
| `comment_text` | `fictioneer_bbcodes`
| `excerpt_length` | `fictioneer_custom_excerpt_length`
| `excerpt_more` | `fictioneer_excerpt_ellipsis`
| `get_avatar` | `fictioneer_avatar_fallback`
| `get_avatar_url` | `fictioneer_get_avatar_url`
| `get_comment_author_IP` | `__return_empty_string`
| `get_comment_text` | `fictioneer_replace_comment_line_breaks`
| `kses_allowed_protocols` | `fictioneer_extend_allowed_protocols`
| `logout_url` | `fictioneer_logout_redirect`
| `manage_edit-comments_columns` | `fictioneer_add_comments_report_column`
| `manage_pages_columns` | `fictioneer_remove_comments_column`
| `manage_posts_columns` | `fictioneer_remove_comments_column`
| `manage_users_columns` | `fictioneer_hide_users_columns`
| `map_meta_cap` | `fcn_read_others_files`, `fictioneer_edit_others_files`, `fictioneer_delete_others_files`, `fictioneer_override_default_taxonomy_capability_check`, `fictioneer_edit_comments`
| `navigation_markup_template` | `fictioneer_pagination_markup`
| `nav_menu_link_attributes` | `fictioneer_add_menu_link_attributes`
| `post_stuck` | `fictioneer_prevent_post_sticky`
| `postbox_classes_{$screen_id}_{$box_id}` | `fictioneer_append_metabox_classes`
| `posts_where` | `fictioneer_exclude_protected_posts`
| `pre_comment_user_ip` | `__return_empty_string`
| `pre_get_posts` | `fictioneer_extend_taxonomy_pages`, `fictioneer_edit_others_fictioneer_posts`, `fictioneer_add_sof_to_taxonomy_query`
| `pre_insert_term` | `fictioneer_restrict_tag_creation`
| `preprocess_comment` | `fictioneer_preprocess_comment`, `fictioneer_validate_comment_form`
| `protected_title_format` | `fictioneer_remove_protected_text`
| `query_vars` | `fictioneer_query_vars`
| `removable_query_args` | `fictioneer_removable_args`
| `render_block` | `fictioneer_download_block_wrapper`
| `rest_authentication_errors` | `fictioneer_restrict_rest_api`
| `show_admin_bar` | `__return_false`
| `style_loader_tag` | `fictioneer_add_font_awesome_integrity`
| `the_content` | `fictioneer_embed_consent_wrappers`, `fictioneer_add_lightbox_to_post_images`, `fictioneer_add_chapter_paragraph_id`
| `the_password_form` | `fictioneer_password_form`
| `theme_templates` | `fictioneer_disallow_page_template_select`
| `update_post_metadata` | `fictioneer_prevent_page_template_update`
| `upload_size_limit` | `fictioneer_upload_size_limit`
| `use_block_editor_for_post_type` | `__return_false`
| `user_contactmethods` | `fictioneer_user_contact_methods`
| `user_has_cap` | `fictioneer_edit_only_comments`
| `wp_list_comments_args` | `fictioneer_comment_list_args`
| `wp_handle_upload_prefilter` | `fictioneer_upload_restrictions`
| `wp_insert_post_data` | `fictioneer_remove_restricted_block_content`, `fictioneer_strip_shortcodes_on_save`, `fictioneer_see_some_evil`, `fictioneer_enforce_permalink`, `fictioneer_prevent_publish_date_update`, `fictioneer_prevent_parent_and_order_update`, `fictioneer_prevent_track_and_ping_updates`
| `wp_is_application_passwords_available` | `__return_false`
| `wp_robots` | `fictioneer_add_noindex_to_robots`
| `wp_sitemaps_enabled` | `__return_false`

<br>

| ACF HOOK | FICTIONEER ACTIONS & FILTERS
| ---: | :--- |
| `acf/fields/relationship/query/name=fictioneer_collection_items` | `fictioneer_acf_filter_collection_items`
| `acf/save_post` | `fictioneer_update_post_relationships`
| `acf/settings/show_admin` | `fictioneer_acf_settings_show_admin`
| `acf/update_value/name=fictioneer_story_custom_pages` | `fictioneer_acf_scope_story_pages`

## Caching

Fictioneer is cache aware. This means the theme provides an interface to aid cache plugins in purging stale caches across the site, not just the updated post. It can even operate as static website with dynamic content being fetched via AJAX. By default, four cache plugins are considered: [WP Super Cache](https://wordpress.org/plugins/wp-super-cache/), [W3 Total Cache](https://wordpress.org/plugins/w3-total-cache/), [LiteSpeed Cache](https://wordpress.org/plugins/litespeed-cache/), and [WP Rocket](https://wp-rocket.me/). Whenever you publish or update content, a series of actions is triggered to purge anything related.

*"There are only two hard things in Computer Science: cache invalidation and naming things" — Phil Karlton.*

### References
* See [_caching_and_transients.php](includes/functions/_caching_and_transients.php)
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
