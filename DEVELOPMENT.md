# Development

This is a quick guide to get you started if you want to contribute to the theme, fork off your own version, or create a child theme. Nothing too comprehensive, but enough to introduce the core concepts and practices.

### Table of Contents

* [Coding Standards and Documentation](#coding-standards-and-documentation)
* [Build Pipeline](#build-pipeline)
* [CSS/SCSS](#cssscss)
  * [CSS Custom Properties (Variables)](#css-custom-properties-variables)
  * [Functions](#functions)
* [JavaScript](#javascript)
  * [Libraries](#libraries)
  * [Shorthands](#shorthands)
  * [Custom Events](#custom-events)
  * [AJAX Requests](#ajax-requests)
* [Hooked Actions](#hooked-actions)
* [Caching](#caching)
  * [References](#references)
  * [Functions](#functions-1)

### Additional Resources

* [Theme actions](ACTIONS.md)
* [Theme filters](FILTERS.md)
* [Child theme example](https://github.com/Tetrakern/fictioneer-child-theme)
* [Liminal child theme](https://github.com/Tetrakern/fictioneer-liminal)

## Coding Standards and Documentation

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

To alleviate these drawbacks, Fictioneer makes two changes to the default BEM methodology. Modifiers are no longer suffixes but classes that start with an underscore (`._modifier`), always within a block scope. Modifiers are chained to the block parent (`.block._modifier` or `.block__element._modifier`), granting them a higher specificity to override parent styles and single-specificity utility classes. Of course this is no silver bullet, but it has proven itself to work flawlessly during the refactoring.

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

Fictioneer is built on [Vanilla JS](http://vanilla-js.com/) without hard dependencies, *especially* not jQuery which is to be avoided like the plague. Bad enough that WordPress and most plugins insist on the performance hog. If you need something from an external library, just copy the functions and credit the source. Avoid additional overhead. There is also an argument for refactoring the JS into classes, modules, and TypeScript. But since everything is already working, this would be a labor of passion without immediate benefit.

### Libraries

* [Diff-Match-Patch](https://github.com/google/diff-match-patch)

### Shorthands

* `_$()` for `document.querySelector()`
* `_$$()` for `document.querySelectorAll()`
* `_$$$()` for `document.getElementById()`

### Custom Events

* **scroll.rAF:** Window scroll event bound to [requestAnimationFrame](https://developer.mozilla.org/en-US/docs/Web/API/window/requestAnimationFrame).
* **resize.rAF:** Window resize event bound to [requestAnimationFrame](https://developer.mozilla.org/en-US/docs/Web/API/window/requestAnimationFrame).
* **nonceReady:** Fires when the nonce has been fetched in AJAX authentication mode.

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

## Hooked Actions

Fictioneer customizes WordPress by using as many standard action hooks as possible, keeping the theme compatible with plugins adhering to the same principles. However, the theme was not initially built for a public release and despite great efforts to refactor the code, some conflicts are unavoidable. Please make sure to also look at the theme’s [custom actions](ACTIONS.md). Following is a list of (probably) all theme actions hooked to WordPress in a not particularly easy to read at fashion.

| WORDPRESS HOOK | FICTIONEER ACTIONS
| ---: | :--- |
| `add_meta_boxes` | `fictioneer_add_seo_metabox`
| `add_meta_boxes_comment` | `fictioneer_add_comment_meta_box`
| `admin_enqueue_scripts` | `fictioneer_admin_scripts`, `fictioneer_admin_styles`, `fictioneer_disable_moderator_comment_edit`, `fictioneer_hide_contributor_comments_utilities`, `fictioneer_hide_editor_comments_utilities`, `fictioneer_hide_private_data`
| `admin_head-profile.php` | `fictioneer_hide_subscriber_profile_blocks`
| `admin_init` | `fictioneer_reduce_subscriber_profile`, `fictioneer_register_settings`
| `admin_menu` | `fictioneer_add_admin_menu`, `fictioneer_reduce_moderator_admin_panel`, `fictioneer_reduce_subscriber_admin_panel`, `fictioneer_remove_menu_pages`
| `admin_notices` | `fictioneer_admin_profile_notices`, `fictioneer_admin_success_notices`, `fictioneer_admin_update_notice`
| `after_setup_theme` | `fictioneer_theme_setup`
| `comment_post` | `fictioneer_comment_post`, `fictioneer_post_comment_to_discord`
| `comment_reply_link` | `fictioneer_comment_login_to_reply`
| `current_screen` | `fictioneer_restrict_editor_menu_access`, `fictioneer_restrict_menu_access`, `fictioneer_restrict_moderator_menu_access`
| `customize_register` | `fictioneer_add_customizers`
| `customize_save_after` | `fictioneer_watch_for_customer_updates`
| `delete_post` | `fictioneer_refresh_post_caches`, `fictioneer_track_chapter_and_story_updates`, `fictioneer_update_modified_date_on_story_for_chapter`
| `do_feed_rss2` | `fictioneer_main_rss_template`
| `edit_comment` | `fictioneer_comment_edit`, `fictioneer_edit_comment`
| `edit_user_profile` | `fictioneer_custom_profile_fields`
| `edit_user_profile_update` | `fictioneer_update_admin_user_profile`, `fictioneer_update_my_user_profile`
| `get_header` | `fictioneer_maintenance_mode`
| `init` | `fictioneer_add_character_taxonomy`, `fictioneer_add_content_warning_taxonomy`, `fictioneer_add_epub_download_endpoint`, `fictioneer_add_fandom_taxonomy`, `fictioneer_add_genre_taxonomy`, `fictioneer_add_logout_endpoint`, `fictioneer_add_oauth2_endpoint`, `fictioneer_block_subscribers_from_admin`, `fictioneer_disable_heartbeat`, `fictioneer_fcn_chapter_post_type`, `fictioneer_fcn_collection_post_type`, `fictioneer_fcn_recommendation_post_type`, `fictioneer_fcn_story_post_type`, `fictioneer_modify_allowed_tags`, `fictioneer_story_rss`
| `kses_allowed_protocols` | `fictioneer_extend_allowed_protocols`
| `login_form` | `fictioneer_after_logout_cleanup`
| `manage_comments_custom_column` | `fictioneer_add_comments_report_column_content`
| `personal_options_update` | `fictioneer_update_admin_user_profile`, `fictioneer_update_my_user_profile`
| `pre_get_posts` | `fictioneer_extend_search_query`, `fictioneer_extend_taxonomy_pages`, `fictioneer_limit_authors_to_own_posts_and_pages`, `fictioneer_remove_unlisted_chapter_from_search`
| `preprocess_comment` | `fictioneer_preprocess_comment`
| `save_post` | `fictioneer_create_sitemap`, `fictioneer_refresh_chapters_schema`, `fictioneer_refresh_chapter_schema`, `fictioneer_refresh_collections_schema`, `fictioneer_refresh_post_caches`, `fictioneer_refresh_post_schema`, `fictioneer_refresh_recommendations_schema`, `fictioneer_refresh_recommendation_schema`, `fictioneer_refresh_stories_schema`, `fictioneer_refresh_story_schema`, `fictioneer_save_seo_metabox`, `fictioneer_save_word_count`, `fictioneer_track_chapter_and_story_updates`, `fictioneer_update_modified_date_on_story_for_chapter`, `fictioneer_update_shortcode_relationships`
| `show_user_profile` | `fictioneer_custom_profile_fields`
| `switch_theme` | `fictioneer_theme_deactivation`
| `template_redirect` | `fictioneer_disable_date_archives`, `fictioneer_generate_epub`, `fictioneer_handle_oauth`, `fictioneer_logout`
| `trashed_post` | `fictioneer_refresh_post_caches`, `fictioneer_track_chapter_and_story_updates`, `fictioneer_update_modified_date_on_story_for_chapter`
| `untrash_post` | `fictioneer_refresh_post_caches`, `fictioneer_track_chapter_and_story_updates`, `fictioneer_update_modified_date_on_story_for_chapter`
| `wp_ajax_*` | `fictioneer_ajax_clear_my_checkmarks`, `fictioneer_ajax_clear_my_comments`, `fictioneer_ajax_clear_my_comment_subscriptions`, `fictioneer_ajax_clear_my_follows`, `fictioneer_ajax_clear_my_reminders`, `fictioneer_ajax_delete_epub`, `fictioneer_ajax_delete_my_account`, `fictioneer_ajax_delete_my_comment`, `fictioneer_ajax_edit_comment`, `fictioneer_ajax_get_avatar`, `fictioneer_ajax_get_bookmarks`, `fictioneer_ajax_get_checkmarks`, `fictioneer_ajax_get_comment_form`, `fictioneer_ajax_get_comment_section`, `fictioneer_ajax_get_fingerprint`, `fictioneer_ajax_get_finished_list`, `fictioneer_ajax_get_follows`, `fictioneer_ajax_get_follows_list`, `fictioneer_ajax_get_follows_notifications`, `fictioneer_ajax_get_nonce`, `fictioneer_ajax_get_reminders`, `fictioneer_ajax_get_reminders_list`, `fictioneer_ajax_is_user_logged_in`, `fictioneer_ajax_mark_follows_read`, `fictioneer_ajax_moderate_comment`, `fictioneer_ajax_purge_schema`, `fictioneer_ajax_report_comment`, `fictioneer_ajax_save_bookmarks`, `fictioneer_ajax_set_checkmark`, `fictioneer_ajax_submit_comment`, `fictioneer_ajax_toggle_follow`, `fictioneer_ajax_toggle_reminder`, `fictioneer_ajax_unset_my_oauth`, `fictioneer_request_story_comments`
| `wp_ajax_nopriv_*` | `fictioneer_ajax_get_comment_form`, `fictioneer_ajax_get_comment_section`, `fictioneer_ajax_get_nonce`, `fictioneer_ajax_is_user_logged_in`, `fictioneer_ajax_submit_comment`, `fictioneer_request_story_comments`
| `wp_before_admin_bar_render` | `fictioneer_remove_admin_bar_links`
| `wp_dashboard_setup` | `fictioneer_reduce_contributor_dashboard_widgets`, `fictioneer_reduce_editor_dashboard_widgets`, `fictioneer_reduce_moderator_dashboard_widgets`, `fictioneer_reduce_subscriber_dashboard_widgets`
| `wp_default_scripts` | `fictioneer_remove_jquery_migrate`
| `wp_enqueue_scripts` | `fictioneer_add_custom_scripts`, `fictioneer_customizer_queue`, `fictioneer_style_queue`
| `wp_head` | `fictioneer_output_head_seo`, `fictioneer_output_rss`, `fictioneer_output_schemas`

| ACF HOOK | FICTIONEER ACTIONS
| ---: | :--- |
| `acf/save_post` | `fictioneer_update_post_relationships`

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

#### `fictioneer_purge_story_list_caches()`
Purges all caches for posts with the Stories (stories.php) template.

#### `fictioneer_purge_chapter_list_caches()`
Purges all caches for posts with the Chapters (chapters.php) template.

#### `fictioneer_purge_collection_list_caches()`
Purges all caches for posts with the Collections (collections.php) template.

#### `fictioneer_purge_recommendation_list_caches()`
Purges all caches for posts with the Recommendations (recommendations.php) template.

#### `fictioneer_refresh_post_caches( $post_id )`
Hooked if caching is active and called whenever a post is published, updated, trashed, restored, or deleted. Purges the cache for the given post and any other that has or may have a relationship with it. This includes the front page, the associated story, all associated chapters, relevant list pages, collections, featured lists, and theme shortcodes.

#### `fictioneer_get_relationship_registry(): Array`
Returns an array with maintained relationships between posts. This "registry" is updated whenever a post is published, updated, trashed, restored, or deleted — with the post in question. If the registry is cleared or becomes corrupted, old relationships cannot be restored unless each post is opened and re-saved.
