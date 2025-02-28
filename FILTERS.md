# Filters
The following [filter hooks](https://developer.wordpress.org/reference/functions/add_filter/) can be used to customize the output of action hooks and other functions without having to modify said functions. Filters can be easily added or overwritten in child themes or plugins. This list is probably incomplete. See `includes/functions/hooks/`.

### Example: Add link to subscription options
This is an example of how to add a link to the subscription popup menu via the `'fictioneer_filter_subscribe_buttons'` hook. The link will feature a [Font Awesome link icon](https://fontawesome.com/icons/link?s=solid&f=classic) and be added in first place. Note how the link is pulled from the author’s meta data, which you would need to add yourself as it does not exist outside this example.

```php
// Add this to your child theme's functions.php
function child_theme_extend_watch_buttons( $buttons, $post_id, $author_id ) {
  // Get link from author meta (not a real field)
  $link = get_the_author_meta( 'your_custom_link', $author_id );
  $link_html = '<a href="' . esc_url( $link ) . '" target="_blank" rel="noopener" class="_align-left"><i class="fa-solid fa-link"></i> <span>Link</span></a>';

  // Prepend link in first place
  array_splice( $buttons, 0, 0, $link_html );

  // Return filtered buttons
  return $buttons;
}
add_filter( 'fictioneer_filter_subscribe_buttons', 'child_theme_extend_watch_buttons', 10, 3 );
```

---

### `apply_filters( 'fictioneer_filter_admin_settings_navigation', $tabs )`
Filters the intermediate output array of the tab pills inside the `fictioneer_settings_header( $tab )` function before it is imploded and rendered. The active tab is set via the `'page'` query parameter and defaults to `'general'`.

**$tabs:**
* $general (string) – Link to the general settings tab.
* $connections (string) – Link to the connections settings tab.
* $phrases (string) – Link to the phrases settings tab.
* $epubs (string) – Link to the ePUBs settings tab.
* $seo (string) – Link to the SEO settings tab (if enabled).
* $tools (string) – Link to the tools tab.
* $logs (string) – Link to the logs tab.

---

### `apply_filters( 'fictioneer_filter_ajax_get_user_data', $data, $user )`
Filters the data to be returned as JSON by the `fictioneer_ajax_get_user_data()` function, which is used to set up the user’s local environment with Follows, Checkmarks, Reminders, and so forth to get around caching. Note that this function is accelerated and skips the WordPress initialization, as well as most plugins if the optional MU plugin has been installed.

**$data:**
* $user_id (int) – The user ID.
* $timestamp (int) – Milliseconds elapsed since the epoch.
* $follows (array|false) – The user’s Follows data or false if disabled.
* $reminders (array|false) – The user’s Reminders data or false if disabled.
* $checkmarks (array|false) – The user’s Checkmarks data or false if disabled.
* $bookmarks (string) – The user’s Bookmarks JSON as string. `'{}'` if disabled.
* $fingerprint (string) – The user’s unique hash.
* 'isAdmin' (bool) - Whether the user is an admin.
* 'isModerator' (bool) - Whether the user is a moderator.
* 'isAuthor' (bool) - Whether the user is an author.
* 'isEditor' (bool) - Whether the user is an editor.
* 'nonce' (string) - The `'fictioneer_nonce'` nonce.
* 'nonceHtml' (string) - Nonce HTMl ready to be appended to the DOM.

**Parameters:**
* $user (WP_User) – The user object.

---

### `apply_filters( 'fictioneer_filter_allowed_chapter_permalinks', $statuses )`
Filters the array of chapter statuses that control whether the chapter permalink is rendered in the `fictioneer_prepare_chapter_groups()` function. By default, the statuses only include `['publish']`.

**Note:** Used by `fictioneer_story_chapters()`. If you enable the `FICTIONEER_LIST_SCHEDULED_CHAPTERS` constant, the filter will be used to treat scheduled chapters as published.

**Parameters:**
* $statuses (array) – Array of chapter statuses.

**Example:**
```php
function child_render_scheduled_chapters_permalinks( $statuses ) {
  $statuses[] = 'future';

  return $statuses;
}
add_filter( 'fictioneer_filter_allowed_chapter_permalinks', 'child_render_scheduled_chapters_permalinks' );
```

---

### `apply_filters( 'fictioneer_filter_allowed_orderby', $defaults )`
Filters the array of allowed orderby arguments for WP_Query.

**$defaults:**
* 0 (string) – `modified`
* 1 (string) – `date`
* 2 (string) – `title`
* 3 (string) – `rand`
* 4 (string) – `name`
* 5 (string) – `ID`
* 6 (string) – `comment_count`
* 7 (string) – `type`
* 8 (string) – `post__in`
* 9 (string) – `author`

---

### `apply_filters( 'fictioneer_filter_append_chapter_to_story_statuses', $statuses, $post_id, $story_id, $force )`
Filters the array of chapter statuses that can be auto-appended to a story’s `fictioneer_story_chapters` metadata in the `fictioneer_append_chapter_to_story()` function. By default, the statuses are `['publish', 'future']`.

**Parameters:**
* $statuses (array) – Array of chapter statuses.
* $post_id (int) – The chapter post ID.
* $story_id (int) – The story post ID.
* $force (boolean) – Whether to skip some guard clauses. Default false.

---

### `apply_filters( 'fictioneer_filter_archive_header', $output, $type, $term, $parent )`
Filters the intermediate output array for the article header in the category, tag, and taxonomy templates before it is imploded and rendered.

**$output:**
* 'heading' (string) – HTML of the heading with the current taxonomy, count, and parent (if any).
* 'description' (string|null) – Optional. HTML of the taxonomy description.
* 'divider' (string) – HTML for the horizontal divider.
* 'tax_cloud' (string) – HTML for the taxonomy cloud generated by `wp_tag_cloud()`.

**Parameters:**
* $type (string) – Taxonomy type of the archive.
* $term (WP_Term) – Term object.
* $parent (WP_Term|false|null) – Parent term object (if any).

**Example:**
```php
function child_remove_tax_cloud_from_genre_archive( $output, $type ) {
  if ( $type === 'fcn_genre' ) {
    unset( $output['tax_cloud'] );
  }

  return $output;
}
add_filter( 'fictioneer_filter_archive_header', 'child_remove_tax_cloud_from_genre_archive', 10, 2 );
```

---

### `apply_filters( 'fictioneer_filter_sharing_modal_links', $output, $post_id, $link, $title )`
Filters the intermediate output array in the `_modal-sharing.php` partial before it is imploded and rendered. Contains icon links to various social media platforms (unless disabled).

**$output:**
* 'bluesky' (string|null) – HTML for Bluesky.
* 'twitter' (string|null) – HTML for Twitter/X.
* 'tumblr' (string|null) – HTML for tumblr.
* 'reddit' (string|null) – HTML for Reddit.
* 'mastodon' (string|null) – HTML for Mastodon.
* 'facebook' (string|null) – HTML for Facebook.

**Parameters**
* $post_id (int) – Post ID.
* $link (int) – URL of the story.
* $title (int) – Title of the story (URL encoded).

**Example:**
```php
function child_add_sharing_button( $output, $post_id, $link, $title ) {
  $output['terminally_online'] = '<a href="http://www.terminally-online.com/touchgrass?url=' . $link . '&title=' . $title . '" target="_blank" rel="noopener nofollow" class="media-buttons__item terminally-online"><i class="fa-solid fa-truck-medical"></i></a>';

  return $output;
}
add_filter( 'fictioneer_filter_sharing_modal_links', 'child_add_sharing_button', 10, 4 );
```

---

### `apply_filters( 'fictioneer_filter_shortcode_article_card_footer', $footer_items, $posts )`
Filters the intermediate output array in the `_article-cards.php` partial before it is imploded and rendered. Contains statistics with icons such as the author, publishing or modified date, and comments.

**$footer_items:**
* 'author' (string|null) – Conditional. HTML for the author.
* 'publish_date' (string) – Conditional. HTML for the publish date.
* 'modified_date' (string) – Conditional. HTML for the modified date.
* 'comments' (string) – HTML for the number of comments.

**Parameters**
* $post (WP_Post) – Post object.

---

### `apply_filters( 'fictioneer_filter_body_attributes', $attributes, $args )`
Filters the intermediate output array of additional body attributes (except `class` which is handled by `body_class()`) before it is mapped, escaped, imploded, and rendered in the `header.php` template.

**$attributes:**
* 'data-post-id' (int) – Current post ID (or -1 if null).
* 'data-story-id' (int|null) – Current story ID (if any). Unsafe.

**$args:**
* 'post_id' (int|null) – Current post ID. Unsafe.
* 'post_type' (string|null) – Current post type. Unsafe.
* 'story_id' (int|null) – Current story ID (if chapter). Unsafe.
* 'header_image_url' (string|boolean) – URL of the filtered header image or false.
* 'header_image_source' (string) – Source of the header image. Either `'default'`, `'post'`, or `'story'`.
* 'header_args' (array) – Arguments passed to the header.php partial.

**Example:**
```php
function child_add_body_attributes( $attributes ) {
  $attributes['data-permalink'] = get_permalink();

  return $attributes;
}
add_filter( 'fictioneer_filter_body_attributes', 'child_add_body_attributes' );
```

---

### `apply_filters( 'fictioneer_filter_breadcrumbs_array', $breadcrumbs, $args )`
Filters the array of breadcrumb tuples inside the `fictioneer_get_breadcrumbs( $args )` function before the HTML is build.

**$breadcrumbs:**
* 0 (string) – Label of the breadcrumb.
* 1 (string|boolean|null) – URL of the breadcrumb or falsy.

**$args:**
* 'post_type' (string|null) – Current post type. Unsafe.
* 'post_id' (int|null) – Current post ID. Unsafe.

---

### `apply_filters( 'fictioneer_filter_bullet_separator', $separator, $context )`

Filters the HTML element used as separator between taxonomies and other items. Note that some items use the `pseudo-separator` CSS class instead and are not affected by this filter, such as the meta row of shortcode lists.

**Parameters:**
* $separator (string) – The default separator HTML element.
* $context (string) – The render context.

---

### `apply_filters( 'fictioneer_filter_card_attributes', $attributes, $post, $context )`
Filters the intermediate output array of HTML attributes inside the `.card` element before they are rendered. The keys are used as attribute names. Make sure to account for already existing attributes.

**Parameters:**
* $attributes (array) - Associative array of attributes to be rendered.
* $post (WP_Post) – The current post object.
* $context (string|null) - Context regarding where or how the card is rendered. Unsafe.

---

### `apply_filters( 'fictioneer_filter_card_control_icons', $icons, $story_id, $chapter_id )`
Filters the intermediate output array of the card control icons inside the `fictioneer_get_card_controls( $story_id, $chapter_id )` function before being rendered. Note that card controls will not be rendered without a story ID.

**$icons:**
* $sticky (string) – HTML of the sticky icon (if enabled).
* $reminder (string) – HTML of the Reminder icon (if enabled).
* $follow (string) – HTML of the Follow icon (if enabled).
* $checkmark (string) – HTML of the checkmark icon (if enabled).

**Parameters:**
* $story_id (int) – ID of the story.
* $chapter_id (int|null) – ID of the chapter (if chapter).

---

### `apply_filters( 'fictioneer_filter_card_control_menu', $menu, $story_id, $chapter_id )`
Filters the intermediate output array of the card control popup menu inside the `fictioneer_get_card_controls( $story_id, $chapter_id )` function before being rendered. Note that card controls will not be rendered without a story ID.

**$menu:**
* $follow (string) – HTML of the Follow buttons (if enabled).
* $reminder (string) – HTML of the Reminder buttons (if enabled).
* $checkmark (string) – HTML of the checkmark buttons (if enabled).

**Parameters:**
* $story_id (int) – ID of the story.
* $chapter_id (int|null) – ID of the chapter (if chapter).

---

### `apply_filters( 'fictioneer_filter_card_{type}_terms', $terms, $post, $args, $story_data )`
Filters the intermediate output array of term HTML nodes (tags, genres, fandoms, etc.) in card partials before it is imploded and rendered, with the term IDs as keys. Type can be `post`, `story`, `chapter`, `collection`, or `recommendation`.

**Parameters**
* $terms (array) – Associative array of HTML nodes to be rendered.
* $post (WP_Post) – The current post object.
* $args (array) – Arguments passed to the shortcode.
* $story_data (array|null) – Collection of story post data. Unsafe.

---

### `apply_filters( 'fictioneer_filter_chapter_card_footer', $footer_items, $post, $story, $args )`
Filters the intermediate output array in the `_card-chapter.php` partial before it is imploded and rendered. Contains statistics with icons such as the number of words, publishing date, comments, and so forth.

**$footer_items:**
* $words (string) – HTML for the word count.
* $publish_date (string) – Conditional. HTML for the publish date.
* $modified_date (string) – Conditional. HTML for the modified date.
* $author (string) – Conditional. HTML for the author.
* $comments (string) – HTML for the number of comments.

**Parameters**
* $post (WP_Post) – The post object.
* $story (array|null) – Optional. Chapter story data.
* $args (array) – Arguments passed to the partial.

---

### `apply_filters( 'fictioneer_filter_chapter_default_formatting', $formatting )`
Filters the default chapter formatting settings. The passed array is empty because the default values are also set on the frontend. You only need this filter if you want to change that. Note that the values for font-color, font-name, and site-width should not be overridden.

**$formatting:**
* $formatting\['font-saturation'] (int) – Start value of the font saturation. Default 0.
* $formatting\['font-size'] (int) – Start value of the font size. Default 100.
* $formatting\['letter-spacing'] (float) – Start value of the letter spacing. Default 0.
* $formatting\['line-height'] (float) – Start value of the line height. Default 1.7.
* $formatting\['paragraph-spacing'] (float) – Start value of the paragraph spacing. Default 1.5.
* $formatting\['indent'] (boolean) – Whether the text has an indent. Default true.
* $formatting\['justify'] (boolean) – Whether the text is justified. Default false.
* $formatting\['show-chapter-notes'] (boolean) – Whether chapter notes are shown. Default true.
* $formatting\['show-paragraph-tools'] (boolean) – Whether the paragraph tools can be toggled. Default true.
* $formatting\['show-comments'] (boolean) – Whether the comment section is shown. Default true.
* $formatting\['show-sensitive-content'] (boolean) – Whether sensitive content is shown. Default true.

**Example:**
```php
function child_modify_chapter_formatting_defaults( $formatting ) {
  $changes = array(
    'font-size' => 90,
    'letter-spacing' => 0.02,
    'line-height' => 1.8,
    'paragraph-spacing' => 2,
    'indent' => false
  );

  return array_merge( $formatting, $changes );
}
add_filter( 'fictioneer_filter_chapter_default_formatting', 'child_modify_chapter_formatting_defaults' );
```

---

### `apply_filters( 'fictioneer_filter_chapter_group', $group, $group_index, $story_id )`
Filters the array of grouped chapter data in the `fictioneer_story_chapters()` function before it is rendered in the chapter list on story pages.

**$group:**
* $group (string) – Name of the group.
* $toggle_icon (string) – CSS classes for the group toggle icon (Font Awesome).
* $data (array) – Array of chapter post data in render order.
  * $id (int) – Post ID
  * $story_id (int) – Story ID.
  * $status (string) – Status of the post.
  * $link (string) – Permalink of the post. Can be empty.
  * $timestamp (string) – Publish date of the post (Unix).
  * $password (boolean) – Whether the post has a password.
  * $list_date (string) – Publish date formatted for lists.
  * $grid_date (string) – Publish date formatted for grids.
  * $icon (string) – CSS classes for the chapter icon (Font Awesome).
  * $text_icon (string) – Text icon (if any).
  * $prefix (string) – Prefix for the title (if any).
  * $title (string) – Title of the post (never empty).
  * $list_title (string) – Alternative title of the post (used on small cards).
  * $words (int) – Word count of the post.
  * $warning (string) – Warning note of the post (if any).
* $count (int) – Number of items in the group to be rendered.
* $classes (array) – Array of CSS classes to be added to the group button.

**Parameters:**
* $group_index (int) – The current group index in render order, starting at 1.
* $story_id (int) – Story ID.

---

### `apply_filters( 'fictioneer_filter_chapter_icon', $icon_html, $chapter_id, $story_id )`
Filters the HTML of the chapter icon before it is appended or rendered. The display hierarchy is password icon > scheduled icon > text icon > chapter icon.

**Parameters:**
* $icon_html (string) – HTML for the chapter icon.
* $chapter_id (int) – Post ID of the chapter.
* $story_id (int|null) – Post ID of the story (if any). Unsafe.

---

### `apply_filters( 'fictioneer_filter_chapter_identity', $output, $args )`
Filters the intermediate output array in the `_chapter-header.php` partial before it is imploded and rendered. Contains the HTML for the story link, chapter title (safe), and author meta nodes (see `fictioneer_get_chapter_author_nodes()`). Any of these items may be missing depending on the chapter’s configuration.

**$output:**
* $link (string) – HTML for the story back link. Unsafe.
* $title (string) – HTML for the chapter title `<h1>`. Unsafe.
* $meta (string) – HTML for the chapter author(s) row. Unsafe.

**Parameters:**
* $args (array) – Array of story and chapter data. See partial.

---

### `apply_filters( 'fictioneer_filter_chapter_index_list_statuses', $statuses, $story_id )`
Filters the array of allowed chapter statuses when building the simple chapter list items in the `function fictioneer_get_chapter_list_data()` function. By default, only `['publish']` chapters are shown.

**Note:** If you enable the `FICTIONEER_LIST_SCHEDULED_CHAPTERS` constant, the filter will be used to treat scheduled chapters as published.

**Parameters:**
* $statuses (array) – Array of chapter statuses.
* $story_id (int) – Post ID of the story.

**Example:**
```php
function child_add_scheduled_chapters_to_chapter_index_list( $statuses ) {
  $statuses[] = 'future';

  return $statuses;
}
add_filter( 'fictioneer_filter_chapter_index_list_statuses', 'child_add_scheduled_chapters_to_chapter_index_list' );
```

---

### `apply_filters( 'fictioneer_filter_chapter_index_item', $item, $post, $args )`
Filters each list item HTML string used in the chapter index popup and mobile menu section (only visible on chapter pages), build inside the `fictioneer_get_chapter_index_html()` function. Not to be confused with the chapter list shown on story pages. You can either modify the string or build a new one from the given parameters.

**Parameters:**
* $item (string) – HTML for the list item with icon, ID, link, and title.
* $post (WP_Post) - The chapter post object.

**$args:**
* `title` (string) – Regular title.
* `list_title` (string) – List title or empty (precedence over title).
* `icon` (string) – HTML for the (text) icon or empty.
* `group` (string) – Name of the chapter group or empty.
* `classes` (array) – Array of CSS classes or empty.
* `position` (int) – Position in the chapter list, starting at 1.

**Example:**
```php
function child_prefix_chapter_index_list_item( $item, $post, $args ) {
  $prefix = get_post_meta( $post->ID, 'fictioneer_chapter_prefix', true );

  return sprintf(
    '<li class="%1$s" data-position="%2$s" data-id="%3$s"><a href="%4$s">%5$s<span class="truncate _1-1">%6$s%7$s</span></a></li>'
    implode( ' ', $args['classes'] ),
    $args['position'],
    $post->ID,
    get_the_permalink( $post->ID ),
    $args['icon'],
    $prefix ? $prefix . ' ' : '',
    $args['list_title'] ?: $args['title']
  );
}
add_filter( 'fictioneer_filter_chapter_index_item', 'child_prefix_chapter_index_list_item', 1, 3 );
```

---

### `apply_filters( 'fictioneer_filter_chapter_index_html', $html, $items, $story_id, $story_link )`
Filters the HTML content of the chapter index modal before it is rendered. This is primarily useful for replacing the outer structure of the chapter list. To modify individual chapter list items, better to use `fictioneer_filter_chapter_index_item`.

**Parameters:**
* $html (string) – HTML content for the chapter index modal.
* $items (string[]) - HTML of chapter list items.
* $story_id (int) – ID of the story.
* $story_link (string) – Link to the chapter’s story.

---

### `apply_filters( 'fictioneer_filter_chapter_micro_menu', $micro_menu, $args )`
Filters the intermediate output array of the chapter micro menu in the `fictioneer_get_chapter_micro_menu( $args )` function before it is imploded and rendered.

**$micro_menu:**
* $chapter_list (string) – Chapter panel in the mobile menu (if any).
* $story_link (string) – Link to the chapter’s story (if any).
* $formatting (string) – Chapter formatting modal.
* $open_fullscreen (string) – Enter fullscreen view (not on iOS).
* $close_fullscreen (string) – Exit fullscreen view (not on iOS).
* $bookmark_jump (string) – Scroll to the bookmark.
* $previous (string) – Link to the previous chapter (if any).
* $top (string) – Anchor to the top of the page.
* $next (string) – Link to the next chapter (if any).

**$args:**
* $author (WP_User) – Author of the chapter.
* $story_post (WP_Post|null) – Post object of the story. Unsafe.
* $story_data (array|null) – Collection of story data. Unsafe.
* $chapter_id (int) – Chapter ID.
* $chapter_title (string) – Safe chapter title.
* $chapter_password (string) – Chapter password or empty string.
* $chapter_ids (array) – IDs of visible chapters in the same story or empty array.
* $current_index (int) – Current index in the chapter list.
* $prev_index (int|boolean) – Index of previous chapter or false if outside bounds.
* $next_index (int|boolean) – Index of next chapter or false if outside bounds.

---

### `apply_filters( 'fictioneer_filter_chapter_nav_buttons', $output, $post_id, $args, $location )`
Filters the intermediate output array of the chapter navigation and top/bottom scroll buttons before it is imploded and rendered in the `fictioneer_chapter_actions_top_right` and `fictioneer_chapter_actions_bottom_right` action hooks.

**Parameters:**
* $output (string[]) – Array of link HTML.
  * 'previous' (string|null) – Link to indexed previous chapter (if any).
  * 'scroll' (string) – Link to scroll up or down, depending on the `$location` parameter.
  * 'next' (string|null) – Link to indexed next chapter (if any).
* $post_id (int) – Chapter post ID.
* $args (array) – Arguments passed to the action hook.
  * 'chapter_ids' (array) – IDs of visible chapters in the same story or empty array.
  * 'indexed_chapter_ids' (array) – IDs of accessible chapters in the same story or empty array.
  * 'prev_index' (int|boolean) – Index of previous chapter or false if outside bounds.
  * 'next_index' (int|boolean) – Index of next chapter or false if outside bounds.
* $location (string) – Location of the navigation. Either `'top'` or `'bottom'`.

**Example:**
```php
function child_remove_chapter_nav_scroll_buttons( $output ) {
  unset( $output['scroll'] );

  return $output;
}
add_filter( 'fictioneer_filter_chapter_nav_buttons', 'child_remove_chapter_nav_scroll_buttons' );
```

---

### `apply_filters( 'fictioneer_filter_chapter_nav_buttons_allowed_statuses', $statuses, $post_id, $args, $location )`
Filters the array of allowed post statuses for the chapter navigation buttons in the `fictioneer_chapter_nav_buttons()` function. The default is `['publish']`, but you could add `'private'` or `'future'` if you want to allow the navigation to render with these statuses.

**Note:** If you enable the `FICTIONEER_LIST_SCHEDULED_CHAPTERS` constant, the filter will be used to treat scheduled chapters as published.

**Parameters:**
* $statuses (string[]) – Array of allowed post statuses.
* $post_id (int) – Chapter post ID.
* $args (array) – Arguments passed to the function.
  * 'chapter_ids' (array) – IDs of visible chapters in the same story or empty array.
  * 'indexed_chapter_ids' (array) – IDs of accessible chapters in the same story or empty array.
  * 'prev_index' (int|boolean) – Index of previous chapter or false if outside bounds.
  * 'next_index' (int|boolean) – Index of next chapter or false if outside bounds.
* $location (string) – Location of the navigation. Either `'top'` or `'bottom'`.

**Example:**
```php
function child_allow_nav_buttons_for_scheduled_chapters( $statuses ) {
  $statuses[] = 'future';

  return $statuses;
}
add_filter( 'fictioneer_filter_chapter_nav_buttons_allowed_statuses', 'child_allow_nav_buttons_for_scheduled_chapters' );
```

---

### `apply_filters( 'fictioneer_filter_chapter_subscribe_button_statuses', $statuses, $post_id )`
Filters the array of allowed post statuses for the chapter subscribe button in the `fictioneer_chapter_subscribe_button()` function. The default is `['publish']`, but you could add `'private'` or `'future'` if you want to allow the button to render with these statuses.

**Parameters:**
* $statuses (string[]) – Array of allowed post statuses.
* $post_id (int) – Chapter post ID.

---

### `apply_filters( 'fictioneer_filter_chapter_media_buttons_statuses', $statuses, $post_id )`
Filters the array of allowed post statuses for the chapter media buttons in the `fictioneer_chapter_media_buttons()` function. The default is `['publish']`, but you could add `'private'` or `'future'` if you want to allow the buttons to render with these statuses.

**Parameters:**
* $statuses (string[]) – Array of allowed post statuses.
* $post_id (int) – Chapter post ID.

---

### `apply_filters( 'fictioneer_filter_chapter_support_links', $support_links, $args )`
Filters the intermediate output array of the chapter support links in the `fictioneer_chapter_support_links( $args )` function before it is looped and rendered. Each item is another array with `'label'`, `'icon'` markup, and `'link'`.

**$support_links:**
* $topwebfiction (array|null) – Data for www.topwebfiction.com. Unsafe.
* $patreon (array|null) – Data for the author’s Patreon page. Unsafe.
* $kofi (array|null) – Data for the author’s Ko-fi page. Unsafe.
* $subscribestar (array|null) – Data for the author’s SubscribeStar page. Unsafe.
* $paypal (array|null) – Data for the author’s PayPal. Unsafe.
* $donation (array|null) – Data for a generic donation link. Unsafe.

**$args:**
* $author (WP_User) – Author of the chapter.
* $story_post (WP_Post|null) – Post object of the story. Unsafe.
* $story_data (array|null) – Collection of story data. Unsafe.
* $chapter_id (int) – Chapter ID.
* $chapter_title (string) – Safe chapter title.
* $chapter_password (string) – Chapter password or empty string.
* $chapter_ids (array) – IDs of visible chapters in the same story or empty array.
* $current_index (int) – Current index in the chapter list.
* $prev_index (int|boolean) – Index of previous chapter or false if outside bounds.
* $next_index (int|boolean) – Index of next chapter or false if outside bounds.

---

### `apply_filters( 'fictioneer_filter_enable_chapter_list_transients', $bool, $post_id )`
Filters the boolean return value of the `fictioneer_enable_chapter_list_transients()` function. By default, this depends on the `fictioneer_caching_active()` function returning false and the `fictioneer_disable_chapter_list_transients` option being off.

**Parameter:**
* $bool (boolean) – Whether the Transients are enabled or not.
* $post_id (int) – Post ID of the story.

---

### `apply_filters( 'fictioneer_filter_enable_menu_transients', $bool, $location )`
Filters the boolean return value of the `fictioneer_enable_chapter_list_transients()` function. By default, this depends on the `fictioneer_caching_active()` function returning false and the `fictioneer_disable_chapter_list_transients` option being off. Possible locations are 'nav_menu', 'mobile_nav_menu', and 'footer_menu'.

**Parameter:**
* $bool (boolean) – Whether the Transients are enabled or not.
* $location (string) – Location identifier of the menu.

---

### `apply_filters( 'fictioneer_filter_enable_shortcode_transients', $bool, $shortcode )`
Filters the boolean return value of the `fictioneer_enable_shortcode_transients()` function. By default, this depends on the `FICTIONEER_SHORTCODE_TRANSIENT_EXPIRATION` constant being greater than -1 and the `fictioneer_disable_shortcode_transients` option being off. This can conflict with external object caches.

**Parameter:**
* $bool (boolean) – Whether the Transients are enabled or not.
* $shortcode (string|null) – The shortcode in question for reference.

**Example:**
```php
function child_enable_shortcode_transients_for_frontpage( $bool ) {
  // Specifically turn on Transients on the frontpage (if not cached in ANY way),
  // ignoring the setting option.
  if ( is_front_page() ) {
    return true;
  }

  return $bool;
}
add_filter( 'fictioneer_filter_enable_shortcode_transients', 'child_enable_shortcode_transients_for_frontpage' );
```

---

### `apply_filters( 'fictioneer_filter_get_support_links', $links, $post_id, $parent_id, $author_id )`
Filters the array of support links returned for the current post (or post ID if provided). First it checks the post meta, then the parent post meta (if any), and finally the author meta if still empty. The first non-empty value is added to the array.

**$links:**
* $topwebfiction (string|null) – Link to www.topwebfiction.com. Unsafe.
* $patreon (string|null) – Link to the author’s Patreon page. Unsafe.
* $kofi (string|null) – Link to the author’s Ko-fi page. Unsafe.
* $subscribestar (string|null) – Link to the author’s SubscribeStar page. Unsafe.
* $paypal (string|null) – Donation link to the author’s PayPal. Unsafe.
* $donation (string|null) – Generic donation link. Unsafe.

**Parameters:**
* $post_id (int|null) – Post ID. Unsafe.
* $parent_id (int|null) – Parent (story) post ID. Unsafe.
* $author_id (int|null) – Author ID. Unsafe.

---

### `apply_filters( 'fictioneer_filter_get_story_data_indexed_chapter_statuses', $statuses, $story_id )`
Filters the array of chapter statuses that can be appended to a story’s `indexed_chapter_ids` array in the `fictioneer_get_story_data()` function. These chapters are a subset of the queried chapters, which need to be filtered separately. By default, the statuses are `['publish']`.

**Note:** If you enable the `FICTIONEER_LIST_SCHEDULED_CHAPTERS` constant, the filter will be used to treat scheduled chapters as published.

**Parameters:**
* $statuses (array) – Array of chapter statuses.
* $story_id (int) – The story post ID.

**Example:**
```php
function child_index_scheduled_chapters( $statuses ) {
  $statuses[] = 'future';

  return $statuses;
}
add_filter( 'fictioneer_filter_get_story_data_indexed_chapter_statuses', 'child_index_scheduled_chapters' );
```

---

### `apply_filters( 'fictioneer_filter_get_story_data_queried_chapter_statuses', $statuses, $story_id )`
Filters the array of queried chapter statuses in the `fictioneer_get_story_data()` function. These chapters may appear in lists but cannot necessarily be navigated to (for example, `'future'` chapters). By default, the statuses are `['publish']`.

**Note:** If you enable the `FICTIONEER_LIST_SCHEDULED_CHAPTERS` constant, the filter will be used to treat scheduled chapters as published.

**Parameters:**
* $statuses (array) – Array of chapter statuses.
* $story_id (int) – The story post ID.

**Example:**
```php
function child_query_scheduled_chapters( $statuses ) {
  $statuses[] = 'future';

  return $statuses;
}
add_filter( 'fictioneer_filter_get_story_data_queried_chapter_statuses', 'child_query_scheduled_chapters' );
```

---

### `apply_filters( 'fictioneer_filter_get_story_data_sql', $sql, $story_id, $chapter_ids, $statuses )`
Filters the prepared raw SQL used to query chapters in the `fictioneer_get_story_data()` function. For performance reasons, this function does not use `WP_Query`, as it would be 10-15 times slower. There is generally little need to modify this SQL, so the filter primarily exists to handle edge cases.

**Warning:** Of all filters that can mess up your site, this one can mess up your site the most.

**Parameters:**
* $sql (string) – Prepared and safe SQL query.
* $story_id (int) – ID of the story.
* $chapter_ids (array) – IDs of all associated chapters, in order.
* $statuses (array) – Array of allowed post statuses to query (already filtered).

---

### `apply_filters( 'fictioneer_filter_chapters_added_statuses', $statuses, $story_id )`
Filters the array of chapter statuses that are eligible to update the `fictioneer_chapters_modified` story meta field in several functions. By default, the statuses are `['publish']`. Note that hidden chapters (meta flag) will still be ignored regardless of status since they are not listed.

**Note:** If you enable the `FICTIONEER_LIST_SCHEDULED_CHAPTERS` constant, the filter will be used to treat scheduled chapters as published.

**Parameters:**
* $statuses (array) – Array of chapter statuses.
* $story_id (int) – The story post ID.

**Example:**
```php
function child_consider_scheduled_chapters_as_update( $statuses ) {
  $statuses[] = 'future';

  return $statuses;
}
add_filter( 'fictioneer_filter_chapters_added_statuses', 'child_consider_scheduled_chapters_as_update' );
```

---

### `apply_filters( 'fictioneer_filter_chapters_card_args', $card_args, $args )`
Filters the arguments passed to the `partials/_card-chapter` template part in the `fictioneer_chapters_list( $args )` function, normally added via the `fictioneer_chapters_after_content` hook.

**$card_args:**
* $cache (boolean) – Return of `fictioneer_caching_active()`.
* $order (string) – Current query order argument. Default 'desc'.
* $orderby (string) – Current query orderby argument. Default 'modified'.
* $ago (int|string) – Current date query argument part. Default 0.

**$args:**
* $current_page (int) – Current page if paginated or `1`.
* $post_id (int) – Current post ID.
* $chapters (WP_Query) – Paginated query of all visible chapters.
* $queried_type (string) – `'fcn_chapter'`

---

### `apply_filters( 'fictioneer_filter_chapters_query_args', $query_args, $post_id )`
Filters the arguments to query the chapters in the `chapters.php` template.

**$query_args:**
* $fictioneer_query_name (string) – `'chapters_list'`
* $post_type (string) – `'fcn_chapter'`
* $post_status (string) – `'publish'`
* $order (string) – `'DESC'` or `'ASC'`
* $orderby (string) – `'modified'`, `'date'`, `'title'`, or `'rand'`
* $paged (int) – Current page number or `1`.
* $posts_per_page (int) – `get_option( 'posts_per_page' )`
* $update_post_term_cache – `! get_option( 'fictioneer_hide_taxonomies_on_chapter_cards' )`
* $meta_query (array)
  * $relation (string) – `'OR'`
  * (array)
    * $key – `'fictioneer_chapter_hidden'`
    * $value – `'0'`
  * (array)
    * $key – `'fictioneer_chapter_hidden'`
    * $compare – `'NOT EXISTS'`
* $date_query – `fictioneer_append_date_query()`

**Parameters:**
* $post_id (int) – Current post ID.

---

### `apply_filters( 'fictioneer_filter_collection_card_footer', $footer_items, $post, $args, $items )`
Filters the intermediate output array in the `_card-collection.php` partial before it is imploded and rendered. Contains statistics with icons such as the number of stories and chapters, publishing date, comments, and so forth.

**$footer_items:**
* $stories (string) – HTML for the number of stories.
* $chapters (string) – HTML for the number of chapters.
* $words (string) – HTML for the collective word count.
* $publish_date (string) – Conditional. HTML for the publish date.
* $modified_date (string) – Conditional. HTML for the modified date.
* $comments (string) – HTML for the number of comments.

**Parameters**
* $post (WP_Post) – The collection post object.
* $args (array) – Arguments passed to the partial.
* $items (WP_Post[]) – Array of featured posts (if any).

---

### `apply_filters( 'fictioneer_filter_collections_card_args', $card_args, $args )`
Filters the arguments passed to the `partials/_card-collection` template part in the `fictioneer_collections_list( $args )` function, normally added via the `fictioneer_collections_after_content` hook.

**$card_args:**
* 'cache' (boolean) – Return of `fictioneer_caching_active()`.
* 'order' (string) – Current query order argument. Default 'desc'.
* 'orderby' (string) – Current query orderby argument. Default 'modified'.
* 'ago' (int|string) – Current date query argument part. Default 0.

**$args:**
* 'current_page' (int) – Current page if paginated or `1`.
* 'post_id' (int) – Current post ID.
* 'collections' (WP_Query) – Paginated query of all visible collections.
* 'queried_type' (string) – `'fcn_collection'`

---

### `apply_filters( 'fictioneer_filter_collections_query_args', $query_args, $post_id )`
Filters the arguments to query the collections in the `collections.php` template.

**$query_args:**
* $fictioneer_query_name (string) – `'collections_list'`
* $post_type (string) – `'fcn_collection'`
* $post_status (string) – `'publish'`
* $order (string) – `'DESC'` or `'ASC'`
* $orderby (string) – `'modified'`, `'date'`, `'title'`, or `'rand'`
* $paged (int) – Current page number or `1`.
* $posts_per_page (int) – `get_option( 'posts_per_page' )`
* $update_post_term_cache – `! get_option( 'fictioneer_hide_taxonomies_on_collection_cards' )`
* $date_query – `fictioneer_append_date_query()`

**Parameters:**
* $post_id (int) – Current post ID.

---

### `apply_filters( 'fictioneer_filter_comments', $comments, $post_id )`
Filters the queried comments in the `comments.php` template and `fictioneer_ajax_get_comment_section()` function.

**Parameters:**
* $comments (array) – The queried comments.
* $post_id (int) – Current post ID.

**Hooked Filters:**
* `fictioneer_shift_sticky_comments( $comments )` – Shift sticky comments to the top. Priority 10.

---

### `apply_filters( 'fictioneer_filter_comment_actions', $output, $comment, $args, $depth )`
Filters the intermediate output array of comment actions before it is imploded and rendered. The `$args` parameter also includes the keys `'has_private_ancestor'` (bool), `'has_closed_ancestor'` (bool), and `'sticky'` (bool).

**Parameters:**
* $output (array) - HTML snippets of actions. Normally just `'reply'` (if allowed). Can be empty.
* $comment (WP_Comment) – The comment object.
* $args (array) – Arguments passed to `fictioneer_theme_comment()` plus extension.
* $depth (int) – Depth of the comment.

---

### `apply_filters( 'fictioneer_filter_comment_badge', $output, $user, $args )`
Filters the HTML of the `fictioneer_get_comment_badge( $user, $comment, $post_author_id )` function before it is returned for rendering. The badge class and label are inserted into `<div class="fictioneer-comment__badge CLASS"><span>LABEL</span></div>`. Possible label classes are `is-author`, `is-admin`, `is-moderator`, `is-supporter`, and `badge-override`.

**Parameters:**
* $output (string) – Complete HTML of the comment badge or empty string.
* $user (WP_User|null) – The user object. Unsafe.

**$args:**
* $badge (string) – Label of the badge or empty string.
* $class (string) – Class of the badge or empty string.
* $comment (WP_Comment|null) – Comment object or null if called outside a comment.
* $post_author_id (int) – ID of the author of the post the comment is for or `0`.

---

### `apply_filters( 'fictioneer_filter_comment_list_args', $list_args )`
Filters the modified arguments used in retrieving the comment list, both for static rendering and AJAX. This is an additional filter hooked into `'wp_list_comments_args'` before the arguments are applied to the `wp_list_comments( $args, $comments )` core function. Note that comment list arguments are somewhat peculiar and may not behave as you expect.

**$list_args:**
* $avatar_size (int) – `32`
* $style (string) – `'ol'`
* $type (string) – `'all'`
* $per_page (string) – `get_option( 'comments_per_page' )`
* $max_depth (string) – `get_option( 'thread_comments_depth' )`
* $commentcode (string|boolean) – Commentcode or `false` (only used with AJAX).
* $post_author_id (int) – The post author ID or `0` (only used with AJAX).
* $page (int|null) – Current comment page or `1` if theme comment query is enabled.
* $reverse_top_level (boolean|null) – `false` if theme comment query is enabled.
* $reverse_children (boolean|null) – `true` if theme comment query is enabled.
* $callback (string|null) – `'fictioneer_theme_comment'` if theme comment callback is enabled.

---

### `apply_filters( 'fictioneer_filter_comments_query', $args, $post_id )`
Filters the arguments passed to `WP_Comment_Query()` in the `comments.php` template and `fictioneer_ajax_get_comment_section()` function. The default arguments depend on the `'fictioneer_disable_comment_query'` option.

**$args:**
* $post_id (int) – Current post ID.
* $type (array|string) – `['comment', 'private']` or WP default.
* $order (string) – `get_option( 'comment_order' )` or WP default.
* $type__not_in (string|null) - `null` or `'private'` (theme query disabled).

**Parameters:**
* $post_id (int) – Current post ID.

---

### `apply_filters( 'fictioneer_filter_comment_reply_mail', $mail, $parent, $comment_id, $commentdata )`
Filters the content of the comment reply notification email in the `fictioneer_comment_notification( $parent, $comment_id, $commentdata )` function. The filter will not trigger if the reply is empty, the parent has no email address or is not flagged to receive notifications.

**$mail:**
* $to (string) – Author email address of the parent comment that was replied to.
* $subject (string) – Email subject.
* $message (string) – Email body.
* $headers (string) – Email headers.
* $attachments (array) – Email attachment. Empty by default.

**Parameters:**
* $parent (WP_Comment) – Comment object of the parent comment that was replied to.
* $comment_id (int) – ID of the reply comment.
* $commentdata (array) – Fields of the reply comment.

---

### `apply_filters( 'fictioneer_filter_contact_form_fields', $fields, $post_id )`
Filters the form fields of the `fictioneer_contact_form` shortcode.

**Parameters:**
* $fields (array) – HTML of the currently set fields.
* $post_id (int|null) – Current post ID. Unsafe since this may be an archive, author, etc.

---

### `apply_filters( 'fictioneer_filter_co_authored_ids', $story_ids, $author_id )`
Filters the array of story IDs co-authored by the specified author ID, as queried and returned by the `fictioneer_sql_get_co_authored_story_ids()` function. These IDs are used to query selectable story chapters and validate permissions for chapter assignment and appending.

**Parameters:**
* $story_ids (int[]) – Array of story IDs.
* $author_id (int) – Co-author ID.

---

### `apply_filters( 'fictioneer_filter_customizer_{theme_option}', $choices )`
Filters the choices arrays of select theme options. Combined with the `fictioneer_filter_pre_build_customize_css` filter, you can append own options and the requires styles. Available options: `header_image_style`, `header_style`, `page_style`, `story_cover_position`, `card_frame`, `card_image_style`, `card_style` (actually card footer), `card_shadow`, `content_list_style`, and `footer_style`.

Refer to `/includes/functions/_customizer-settings.php` to see the default choices.

---

### `apply_filters( 'fictioneer_filter_css_snippet_{snippet}', $css )`
Filters the CSS snippet for certain theme options before they are appended to the customize.css building string.

Refer to `/includes/functions/_customizer.php` to see all snippets.

---

### `apply_filters( 'fictioneer_filter_default_search_form_args', $args )`
Filters the default search form arguments for the search page (not the shortcode). Most of them do not make sense to change for the search page, likely only the preselect type ('any', 'post', 'fcn_story', 'fcn_chapter', 'fcn_collection', or 'fcn_recommendation').

**$args:**
* $simple (boolean|null) – Whether to show the simple form. Default null.
* $expanded (boolean|null) – Whether the form should be expanded. Default null.
* $placeholder (string|null) – The placeholder message. Default null.
* $preselect_type (string|null) – The preselected post type. Default null.
* $preselect_tags (string|null) – Comma-separated list of tag IDs. Default null.
* $preselect_genres (string|null) – Comma-separated list of genre IDs. Default null.
* $preselect_fandoms (string|null) – Comma-separated list of fandom IDs. Default null.
* $preselect_characters (string|null) – Comma-separated list of character IDs. Default null.
* $preselect_warnings (string|null) – Comma-separated list of content warning IDs. Default null.
* $cache (boolean|null) – Whether to account for caching. Default null.

**Example:**
```php
function child_pre_select_search_page_post_type( $args ) {
  $args['preselect_type'] = 'fcn_story';

  return $args;
}
add_filter( 'fictioneer_filter_default_search_form_args', 'child_pre_select_search_page_post_type' );
```

---

### `apply_filters( 'fictioneer_filter_discord_chapter_message', $message, $post, $story_id )`
Filters the chapter message array passed to `fictioneer_discord_send_message()` in `_module-discord.php` before it is encoded as JSON. Allows you to customize the webhook message. If made falsy, the message will not be sent.

**Parameters:**
* $message (array) – The message and fields posted to the Discord webhook.
  * 'content' (string) - The actual discord message.
  * 'embeds' (array) - Array of array with embedded items. Only uses the `0` index item.
    * `0` (array) - First item in the array of embeds.
      * 'title' (string) - Title.
      * 'description' (string) - Description.
      * 'url' (string) - URL.
      * 'color' (string) - Color integer. Defaults to `FICTIONEER_DISCORD_EMBED_COLOR` (`9692513`).
      * 'author' (array) - Array of author data.
        * 'name' (string) - Author name.
        * 'icon_url' (string) - Avatar URL.
      * 'timestamp' (string) - [ISO 8601](https://wordpress.org/documentation/article/customize-date-and-time-format/) timestamp.
      * 'footer' (string|null) - Optional. Title of the story.
      * 'thumbnail' (array|null) - Optional. Array of thumbnail data.
        * 'url' (string) - Thumbnail URL.
* $post (WP_Post) – The new chapter being published.
* $story_id (int|null) – The ID of the associated story if set. Unsafe.

**Example:**
```php
function child_remove_discord_chapter_message_embed_description( $message ) {
  $message['embeds'][0]['description'] = '';

  return $message;
}
add_filter( 'fictioneer_filter_discord_chapter_message', 'child_remove_discord_chapter_message_embed_description' );
```

---

### `apply_filters( 'fictioneer_filter_discord_comment_message', $message, $comment, $post, $user )`
Filters the comment message array passed to `fictioneer_discord_send_message()` in `_module-discord.php` before it is encoded as JSON. Allows you to customize the webhook message. If made falsy, the message will not be sent.

**Parameters:**
* $message (array) – The message and fields posted to the Discord webhook.
* $comment (WP_Comment) – The comment that triggered the webhook.
* $post (WP_Post) – The post the comment is for.
* $user (WP_User) – The user of the comment (invalid if a guest).

---

### `apply_filters( 'fictioneer_filter_discord_story_message', $message, $post )`
Filters the story message array passed to `fictioneer_discord_send_message()` in `_module-discord.php` before it is encoded as JSON. Allows you to customize the webhook message. If made falsy, the message will not be sent.

**Parameters:**
* $message (array) – The message and fields posted to the Discord webhook.
* $post (WP_Post) – The new story being published.

---

### `apply_filters( 'fictioneer_filter_discord_chapter_webhook', $webhook, $post, $story_id )`
Filters the webhook used for the Discord notification about a new chapters.

**Parameters:**
* $webhook (string) - The Discord webhook.
* $post (WP_Post) - The chapter post.
* $story_id (int|null) - The ID of the story post (if any). Unsafe.

---

### `apply_filters( 'fictioneer_filter_discord_comment_webhook', $comment, $post, $user )`
Filters the webhook used for the Discord notification about a new comment.

**Parameters:**
* $comment (WP_Comment) - The comment in question.
* $post (WP_Post) - The post the comment is for.
* $user (WP_User|null) - The user who wrote comment (if registered). Unsafe.

---

### `apply_filters( 'fictioneer_filter_discord_story_webhook', $post )`
Filters the webhook used for the Discord notification about a new stories.

**Parameters:**
* $post (WP_Post) - The story post.

---

### `apply_filters( 'fictioneer_filter_falsy_meta_allow_list', $allowed )`
Filters the array of meta keys allowed to be saved as "falsy" ("", 0, null, false, []) instead of being deleted when updated via theme functions. Applies to post, comment, and user meta fields. This does not affect the core update functions. See `fictioneer_update_user_meta(…)`, `fictioneer_update_comment_meta(…)`, and `fictioneer_update_post_meta(…)`.

**Parameters:**
* $allowed (array) – Array of allowed meta keys. Default empty.

---

### `apply_filters( 'fictioneer_filter_ffcnr_url', $url )`
Filters the URL to the FFCNR entry script.

**Parameters:**
* $url (string) – URL to the FFCNR entry script.

---

### `apply_filters( 'fictioneer_filter_fonts', $fonts )`
Filters the return array of the `fictioneer_get_fonts()` function, used to set up the chapter font options.

**Parameters:**
* $fonts (array) – Numeric array of font items with CSS name (`css`), display name (`name`), and fallbacks (`alt`).

---

### `apply_filters( 'fictioneer_filter_font_colors', $colors )`
Filters the return array of the `fictioneer_get_font_colors()` function, used to set up the chapter font color options.

**Parameters:**
* $fonts (array) – Numeric array of color items with CSS name (`css`) and display name (`name`).

---

### `apply_filters( 'fictioneer_filter_font_data', $fonts )`
Filters the font array compiled from all valid font folders and Google Fonts links in both the parent and child theme. Note that the `fictioneer_get_font_data()` function reads the file system, which is potentially slow.

**Parameters:**
* $fonts (array) – Array of font data.

---

### `apply_filters( 'fictioneer_filter_footnotes', $footnotes )`
Filters the collection array of footnotes to be rendered.

Filters the font array compiled from all valid font folders and Google Fonts links in both the parent and child theme. Note that the `fictioneer_get_font_data()` function reads the file system, which is potentially slow.

**Parameters:**
* $footnotes (array) – Array of footnotes (ID => HTML).

---

### `apply_filters( 'fictioneer_filter_header_image', $header_image_url, $post_id, $source )`
Filters the URL of the header image in the `header.php` template.

**Parameters:**
* $header_image_url (string) – URL of the current header image, either global or custom.
* $post_id (int|null) – Current post ID. Unsafe since this may be an archive, author, etc.
* $source (string) – Either `'default'`, `'post'`, or `'story'`.

---

### `apply_filters( 'fictioneer_filter_header_image_preload', $tag, $header_image_url, $post_id, $source )`
Filters the preload link tag of the header image in the `header.php` template.

**Parameters:**
* $tag (string) – Link tag for preloading the header image.
* $header_image_url (string) – URL of the current header image, either global or custom.
* $post_id (int|null) – Current post ID. Unsafe since this may be an archive, author, etc.
* $source (string) – Either `'default'`, `'post'`, or `'story'`.

**Example:**
```php
function child_append_webp_to_header_image_preload( $tag, $header_image_url ) {
  return "<link rel='preload' href='{$header_image_url}.webp' as='image' fetchPriority='high'>";
}
add_filter( 'fictioneer_filter_header_image_preload', 'child_append_webp_to_header_image_preload', 10, 2 );
```

---

### `apply_filters( 'fictioneer_filter_icon_menu_items', $items, $args )`
Filters the intermediate item array of the `fictioneer_render_icon_menu()` function before it is imploded and rendered.

**$items:**
* $login (string|null) – HTML for the login modal icon toggle.
* $profile (string|null) – HTML for the profile icon link.
* $bookmarks (string|null) – HTML for the bookmarks icon link.
* $discord (string|null) – HTML for the Discord icon link.
* $follows (string|null) – HTML for the Follows icon menu.
* $search (string|null) – HTML for the search icon link.
* $lightswitch (string) – HTML for the dark/light mode icon toggle.
* $settings (string) – HTML for the setting modal icon toggle.
* $rss (string|null) – HTML for the RSS icon link.
* $logout (string|null) – HTML for the logout icon link.

**$args:**
* $location (string) – Name of the render location, either `'in-navigation'` or `'in-mobile-menu'`.

---

### `apply_filters( 'fictioneer_filter_is_admin', $check, $user_id )`
Filters the boolean return value of the `fictioneer_is_admin( $user_id )` function. This may be handy if you want to use custom roles or account for other factors not covered by default capabilities. Beware, the result determines whether the user can perform [administrator](https://wordpress.org/support/article/roles-and-capabilities/#administrator) actions.

**Parameters:**
* $check (boolean) – True or false.
* $user_id (int) – The tested user ID.

---

### `apply_filters( 'fictioneer_filter_is_author', $check, $user_id )`
Filters the boolean return value of the `fictioneer_is_author( $user_id )` function. This may be handy if you want to use custom roles or account for other factors not covered by default capabilities. Beware, the result determines whether the user can publish posts but does _not_ necessarily reflect the author role since other roles can publish posts too.

**Parameters:**
* $check (boolean) – True or false.
* $user_id (int) – The tested user ID.

---

### `apply_filters( 'fictioneer_filter_is_moderator', $check, $user_id )`
Filters the boolean return value of the `fictioneer_is_moderator( $user_id )` function. This may be handy if you want to use custom roles or account for other factors not covered by default capabilities. Beware, the result determines whether the user can moderate comments and users but does _not_ necessarily reflect a role since there is no default moderator role.

**Parameters:**
* $check (boolean) – True or false.
* $user_id (int) – The tested user ID.

---

### `apply_filters( 'fictioneer_filter_is_editor', $check, $user_id )`
Filters the boolean return value of the `fictioneer_is_editor( $user_id )` function. This may be handy if you want to use custom roles or account for other factors not covered by default capabilities. Beware, the result determines whether the user can perform [editor](https://wordpress.org/support/article/roles-and-capabilities/#editor) actions.

**Parameters:**
* $check (boolean) – True or false.
* $user_id (int) – The tested user ID.

---

### `apply_filters( 'fictioneer_filter_list_chapter_prefix', $prefix, $chapter_id, $context )`
Filters the prefix string (if any) in front of a chapter title before it is rendered in the `_story-content.php` partial or `fictioneer_chapter_list` shortcode. Useful if you want to add a wrapper for styling purposes.

**Parameters:**
* $prefix (string) – Chapter prefix.
* $chapter_id (int) - Post ID of the chapter.
* $context (string) – Either `'story'` or `'shortcode'`.

---

### `apply_filters( 'fictioneer_filter_list_chapter_title_row', $output, $chapter_id, $prefix, $has_password, $context )`
Filters the intermediate output HTML of the chapter list title row before it is rendered in the `_story-content.php` partial or `fictioneer_chapter_list` shortcode.

**Parameters:**
* $output (string) – The chapter title row HTML.
* $chapter_id (int) - Post ID of the chapter.
* $prefix (string) – Filtered chapter prefix.
* $has_password (boolean) – Whether the chapter has a password.
* $context (string) – Either `'story'` or `'shortcode'`.

**Example:**
```php
function child_display_pw_expiration_date_in_chapter_lists( $output, $chapter_id, $prefix, $has_password ) {
  if ( ! $has_password ) {
    return $output;
  }

  $password_expiration_date_utc = get_post_meta( $chapter_id, 'fictioneer_post_password_expiration_date', true );

  if ( empty( $password_expiration_date_utc ) ) {
    return $output;
  }

  $local_time = get_date_from_gmt( $password_expiration_date_utc );
  $formatted_datetime = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $local_time ) );

  return "[{$formatted_datetime}] {$output}";
}
add_filter( 'fictioneer_filter_list_chapter_title_row', 'child_display_pw_expiration_date_in_chapter_lists', 10, 4 );
```

---

### `apply_filters( 'fictioneer_filter_list_chapter_meta_row', $output, $data, $args )`
Filters the intermediate output array in the `fictioneer_get_list_chapter_meta_row( $data, $args )` function before it is imploded and returned in the `_story-content.php` partial or `fictioneer_chapter_list` shortcode.

**output:**
* $warning (string|null) – Optional. Warning node.
* $password (string|null) – Optional. Password node.
* $date (string) – Time node.
* $words (string) – Words node.

**data:**
* $id (int) – ID of the chapter.
* $warning (string) – Warning note or empty string.
* $password (boolean) – Whether the chapter requires a password.
* $timestamp (string) – Timestamp as ISO 8601 format.
* $list_date (string) – Displayed chapter date for the list view.
* $grid_date (string|null) – Optional. Displayed chapter date for the grid view.
* $words (int) – Chapter word count.

**args:**
* $grid (boolean|null) – Optional. `true` if the row needs to account for grid view.

---

### `apply_filters( 'fictioneer_filter_metabox_{meta_box}', $output, $post )`
Filters the intermediate output array for meta box fields before it is imploded and rendered, see `_setup-meta-fields.php`. The dynamic part of the hook can be `story_meta`, `story_data`, `story_epub`, `chapter_meta`, `chapter_data`, `advanced`, `support_links`, `post_data`, `collection_data`, and `recommendation_data`.

**Parameters:**
* $output (array) – Captured HTML of meta fields to be rendered.
* $post (WP_Post) – The post object.

---

### `apply_filters( 'fictioneer_filter_metabox_updates_{type}', $fields, $post_id )`
Filters the sanitized POST meta box fields before they are saved, see `_setup-meta-fields.php`. The dynamic part of the hook can be `story`, `chapter`, `advanced`, `support_links`, `post`, `collection`, and `recommendation`.

**Parameters:**
* $fields (array) – Meta fields to be saved.
* $post_id (int) – The post ID.

---

### `apply_filters( 'fictioneer_filter_mobile_quick_buttons', $buttons )`
Filters the intermediate output array of the `fictioneer_mobile_quick_buttons()` function before it is imploded and rendered via the `fictioneer_mobile_menu_bottom` hook. By default and depending on the page, the output can contain buttons to darken and brighten the site, toggle chapter options, open the formatting modal, or change site settings.

**$buttons:**
* $minimalist (string) – Toggles the minimalist mode.
* $darken (string) – Darken the site by 20 percent.
* $brighten (string) – Brighten the site by 20 percent.
* $justify (string) – Toggle chapter text justify.
* $indent (string) – Toggle chapter line indents.
* $formatting (string) – Chapter formatting modal.
* $previous_font (string) – Previous chapter font.
* $next_font (string) – Next chapter font.
* $covers (string) – Toggle display of cover images site-wide.
* $textures (string) – Toggle display of background textures site-wide.
* $polygons (string) – Toggle display of polygons site-wide.

---

### `apply_filters( 'fictioneer_filter_mobile_user_menu_items', $items )`
Filters the intermediate output array of the `fictioneer_mobile_user_menu()` function before it is imploded and rendered via the `fictioneer_mobile_menu_main_frame_panels` hook. By default and depending on the login status, the output can contain the user’s account, reading lists and bookmarks, the site’s Discord link, modal toggles for chapter formatting and site settings, anchors to the current bookmark and comments section, as well as the logout link.

**$items:**
* $account (string) – Link to the user’s account (if set).
* $search (string) – Link to search form.
* $site_settings (string) – Site settings modal.
* $discord (string) – Link to the site’s Discord (if set).
* $bookshelf (string) – Link to the user’s reading lists (if set).
* $bookmarks (string) – Link to the user’s bookmarks page (if set).
* $formatting (string) – Chapter formatting modal. Only in chapters.
* $comment_jump (string) – Scrolls to the comment section. Only in chapters.
* $bookmark_jump (string) – Scrolls to the current bookmark. Only in chapters.
* $logout (string) – Logout link.
* $login (string) – Login modal.

---

### `apply_filters( 'fictioneer_filter_obfuscation_string', $obfuscation, $post )`
Filters the obfuscation string generated for chapter spoilers and protected posts.

**Parameters:**
* $obfuscation (string) – The obfuscation string.
* $post (WP_Post) – The post object.

---

### `apply_filters( 'fictioneer_filter_page_background', $html )`
Filters the HTML of the page background element, which is absolute positioned under the actual content.

**Parameters:**
* $html (string) – The HTML element of the page background.

---

### `apply_filters( 'fictioneer_filter_page_card_footer', $footer_items, $post, $args )`
Filters the intermediate output array in the `_card-page.php` partial before it is imploded and rendered. Contains statistics with icons such as the author, publishing date, and comments.

**$footer_items:**
* $author (string) – Conditional. HTML for the author.
* $publish_date (string) – HTML for the publish date.
* $comments (string) – HTML for the number of comments (if more than 0).

**Parameters**
* $post (WP_Post) – The page post object.
* $args (array) – Arguments passed to the partial.

---

### `apply_filters( 'fictioneer_filter_patreon_dollars', $dollar, $amount_cents_raw, $post )`
Filters the pledge threshold amount of the Patreon gate for protected posts (if any). Patreon stores tier prices in cents, so $2.50 would be 250 raw. The theme turns that back into the Dollar format, which you can intercept here. Note that Patreon has specific [rules for converting currencies](https://support.patreon.com/hc/en-us/articles/360044469871--How-tiers-are-converted-into-other-currencies).

**Parameters:**
* $dollar (string) – The pledge threshold as "$0.00".
* $amount_cents_raw (int) – The threshold in cents.
* $post (WP_Post) – The gated post.

---

### `apply_filters( 'fictioneer_filter_patreon_gate_message', $message, $options, $post )`
Filters the HTML of the message displayed below the Patreon unlock button. The message normally includes which tiers or pledge you need to unlock a post, but it can be overwritten in the settings and be completely custom.

**Parameters:**
* $message (string) – HTML for the message.
* $options (array) – HTML snippets of the unlock options, prepared for rendering.
* $post (WP_Post) – Post object of the locked post.

---

### `apply_filters( 'fictioneer_filter_patreon_tier_unlock', $required, $post, $user, $patreon_post_data )`
Filters the boolean to determine whether a post is unlocked via Patreon tier.

**Parameters:**
* $required (boolean) – Whether the post is unlocked.
* $post (WP_Post) – The post object.
* $user (WP_User) – The current user.
* $patreon_post_data (array) – Relevant Patreon data for the post.
  * 'gated' (boolean) - Whether the post has tiers or a cent amount assigned.
  * 'gate_tiers' (array) - Tiers that unlock the post.
  * 'gated' (int) - Minimum cent amount that unlocks the post.
  * 'gate_lifetime_cents' (int) - Minimum lifetime cent amount that unlocks the post.

---

### `apply_filters( 'fictioneer_filter_post_card_footer', $footer_items, $post, $args )`
Filters the intermediate output array in the `_card-post.php` partial before it is imploded and rendered. Contains statistics with icons such as the author, publishing date, and comments.

**$footer_items:**
* $author (string) – Conditional. HTML for the author.
* $publish_date (string) – HTML for the publish date.
* $comments (string) – HTML for the number of comments.

**Parameters**
* $post (WP_Post) – The post object.
* $args (array) – Arguments passed to the partial.

---

### `apply_filters( 'fictioneer_filter_post_header_items', $output, $post_id, $args )`
Filters the intermediate output array of header items in the `_post.php` partial and `single-post.php` template before it is imploded and rendered. Mind the render context, which can be `'loop'`, `'shortcode_fictioneer_blog'`, or `'single-post'`.

**$output:**
* `title` (string) – The post title.
* `meta` (string) – The post meta row.

**$args:**
* `context` (string) – Render context of the partial.
* `nested` (boolean|null) – Optional. Whether the post is nested inside another query.

---

### `apply_filters( 'fictioneer_filter_post_meta_items', $output, $args )`
Filters the intermediate output array of the `fictioneer_get_post_meta_items()` function before it is imploded and returned.

**$output:**
* $date (string) – HTML of the post date.
* $author (string) – HTML of the author name and link.
* $category (string) – HTML of the post category/categories.
* $comments (string) – HTML of the comment link with comment number.

**$args:**
* $no_date (boolean|null) – Whether to hide the date.
* $no_author (boolean|null) – Whether to hide the author.
* $no_cat (boolean|null) – Whether to hide the category.
* $no_comments (boolean|null) – Whether to hide the comments.

---

### `apply_filters( 'fictioneer_filter_pre_build_bundled_fonts', $fonts )`
Filters the font array before the bundled-fonts.css stylesheet is built in the `fictioneer_build_bundled_fonts()` function. This allows you to add, remove, or change fonts at the last opportunity. Note that the function reads the file system, which is potentially slow. The result is cached in the `fictioneer_chapter_fonts` option.

**Parameters:**
* $fonts (array) – Array of font data.

---

### `apply_filters( 'fictioneer_filter_pre_build_customize_css', $css )`
Filters the CSS compiled from settings and theme options before it is minified and saved under `/cache/customize.css`. You can potentially append your own CSS or modify the current values, although that will require some regex.

**Parameters:**
* $css (string) – The compiled customize CSS.

---

### `apply_filters( 'fictioneer_filter_profile_fields_support', $fields, $profile_user )`
Filters the intermediate output array of support profile fields in `includes/functions/users/_admin-profile.php` before it is imploded with `'<br>'` and rendered. You can use the filter to remove or add fields, but be aware that they will not be saved without further custom code. See `fictioneer_update_my_user_profile()` in the same file.

**Parameters:**
* $fields (string[]) – Prepared HTML for each support field. The default keys are `'patreon'`, `'kofi'`, `'subscribestar'`, `'paypal'`, and `'donation'`.
* $profile_user (WP_User) – The profile user object.

**Example:**
```php
function child_add_custom_profile_support_field( $fields, $profile_user ) {
  $fields['custom'] = '<input name="fictioneer_user_custom_link" type="text" id="fictioneer_user_custom_link" value="' . esc_attr( get_the_author_meta( 'fictioneer_user_custom_link', $profile_user->ID ) ) . '" class="regular-text" placeholder="https://..."><p class="description">Custom donation link</p>';

  return $fields;
}
add_filter( 'fictioneer_filter_profile_fields_support', 'child_add_custom_profile_support_field', 10, 2 );
```

---

### `apply_filters( 'fictioneer_filter_recommendations_card_args', $card_args, $args )`
Filters the arguments passed to the `partials/_card-recommendation` template part in the `fictioneer_recommendations_list( $args )` function, normally added via the `fictioneer_recommendations_after_content` hook.

**$card_args:**
* $cache (boolean) – Return of `fictioneer_caching_active()`.
* $order (string) – Current query order argument. Default 'desc'.
* $orderby (string) – Current query orderby argument. Default 'modified'.
* $ago (int|string) – Current date query argument part. Default 0.

**$args:**
* $current_page (int) – Current page if paginated or `1`.
* $post_id (int) – Current post ID.
* $recommendations (WP_Query) – Paginated query of all visible recommendations.
* $queried_type (string) – `'fcn_recommendation'`

---

### `apply_filters( 'fictioneer_filter_recommendations_query_args', $query_args, $post_id )`
Filters the arguments to query the recommendations in the `recommendations.php` template.

**$query_args:**
* $fictioneer_query_name (string) – `'recommendations_list'`
* $post_type (string) – `'fcn_recommendation'`
* $post_status (string) – `'publish'`
* $order (string) – `'DESC'` or `'ASC'`
* $orderby (string) – `'publish_date'`
* $paged (int) – Current page number or `1`.
* $posts_per_page (int) – `get_option( 'posts_per_page' )`
* $update_post_term_cache – `! get_option( 'fictioneer_hide_taxonomies_on_recommendation_cards' )`
* $date_query – `fictioneer_append_date_query()`

**Parameters:**
* $post_id (int) – Current post ID.

---

### `apply_filters( 'fictioneer_filter_removable_query_args', $query_args )`
Filters the array of query args to be removed after being parsed on page load.

**$query_args:**
* $0 – `'success'`
* $1 – `'failure'`
* $3 – `'fictioneer_nonce'`
* $4 – `'fictioneer-notice'`

---

### `apply_filters( 'fictioneer_filter_root_attributes', $attributes )`
Filters the intermediate output array of the `fictioneer_root_attributes()` function in the `header.php` template before it is looped and rendered. Note that the array keys are used as attribute names and include hyphens.

**$attributes:**
* 'class' (string) – CSS classes. Depends on options, can be empty.
* 'data-mode-default' (string) – Mode of the site, light or dark. Default 'dark'.
* 'data-site-width-default' (string) – Site width in pixels (without unit). Default '960'.
* 'data-theme' (string) – Active theme or child theme. Default 'default'.
* 'data-mode' (string) – Active theme mode. Default empty (dark).
* 'data-font-weight' (string) – Current font weight set (default, thinner, or normal). Default 'default'.
* 'data-primary-font' (string) – CSS name of primary font. Default 'Open Sans'.
* 'data-ajax-submit' (string|null) – Optional. Toggle to use AJAX comment submit.
* 'data-ajax-nonce' (string|null) – Optional. Toggle to load nonce via AJAX.
* 'data-ajax-auth' (string|null) – Optional. Toggle to load user login state via AJAX.
* 'data-force-child-theme' (string|null) – Optional. Toggle to disable parent theme switch.
* 'data-caching-active' – Optional. Whether the site is cached.
* 'data-public-caching' (string|null) – Optional. Indicates that public caches are served to all users.
* 'data-ajax-auth' (string|null) – Optional. Indicates that the logged-in status of users is set via AJAX.
* 'data-edit-time' (string|null) – Optional. Time in minutes that logged-in users can edit their comments.
* 'data-author-fingerprint' – Optional. Fingerprint hash of the post author.
* 'data-age-confirmation' – Optional. Whether the age confirmation modal should be triggered.
* 'data-fictioneer-{$key}-value' (string|null) – Optional. Stimulus controller values.

---

### `apply_filters( 'fictioneer_filter_rss_link', $feed, $post_type, $post_id )`
Filters the RSS link returned by the `fictioneer_get_rss_link( $post_type, $post_id )` function. Note that the function will always return `false` if the `fictioneer_enable_theme_rss` option is not enabled, regardless of this filter.

**Parameters:**
* $feed (int) – The escaped RSS feed URL, either the main or story (chapters) feed.
* $post_type (string) – The post type the RSS is for. This can differ from the current post type.
* $post_id (int) – The post ID the RSS is for. This can differ from the current post ID.

---

### `apply_filters( 'fictioneer_filter_rss_main_query_args', $query_args )`
Filters the query arguments for the main RSS feed in the `rss-rss-main.php` template.

**Parameters:**
* $query_args (array) – Associative array of query arguments.

**Example:**
```php
function child_exclude_protected_posts_from_main_rss( $query_args ) {
  $query_args['has_password'] = false;

  return $query_args;
}
add_filter( 'fictioneer_filter_rss_story_query_args', 'child_exclude_protected_posts_from_main_rss' );
```

---

### `apply_filters( 'fictioneer_filter_rss_story_query_args', $query_args, $story_id )`
Filters the query arguments for the story RSS feed in the `rss-rss-story.php` template.

**Parameters:**
* $query_args (array) – Associative array of query arguments.
* $story_id (int) – Post ID of the story.

**Example:**
```php
function child_exclude_protected_posts_from_story_rss( $query_args ) {
  $query_args['has_password'] = false;

  return $query_args;
}
add_filter( 'fictioneer_filter_rss_story_query_args', 'child_exclude_protected_posts_from_story_rss' );
```

---

### `apply_filters( 'fictioneer_filter_safe_title', $title, $post_id, $context, $args )`
Filters the string returned by the `fictioneer_get_safe_title( $post_id )` function, after all tags and line breaks have been stripped. No further sanitization is applied here, so you can add HTML again.

**Parameters:**
* $title (string) – The sanitized title of the post.
* $post_id (int) – The post ID.
* $context (string|null) - Context regarding where or how the title is used. Unsafe.
* $args (array) - Optional additional arguments.

**Hooked Filters:**
* `fictioneer_prefix_sticky_safe_title( $comments )` – Prepends icon to sticky blog posts.
* `fictioneer_prefix_draft_safe_title( $comments )` – Prepends "Draft:" to drafts.

**Example:**
```php
function child_modify_chapter_list_title( $title, $post_id, $context ) {
  if ( ! in_array( $context, ['story-chapter-list', 'shortcode-chapter-list'] ) ) {
    return $title;
  }

  $other_title = 'Where you get that from';

  if ( empty( $other_title ) ) {
    return $title;
  }

  $title = $title . ' — ' . $other_title;

  return $title;
}
add_filter( 'fictioneer_filter_safe_title', 'child_modify_chapter_list_title', 10, 3 );
```

---

### `apply_filters( 'fictioneer_filter_search_title', $html, $args )`
Filters the HTML for the search title before it is rendered in the `search.php` template. The default title follows the "n Search Results" pattern.

**Parameters:**
* $html (string) – The title HTML.

**$args:**
* $post_type (string|int) – Selected post type option or 0.
* $sentence (string|int) – Selected match option or 0.
* $order (string|int) – Selected sort option or 0.
* $orderby (string|int) – Selected order by option or 0.
* $queried_genres (string|int) – Comma-separated list of selected genres to include or 0.
* $queried_fandoms (string|int) – Comma-separated list of selected fandoms to include or 0.
* $queried_characters (string|int) – Comma-separated list of selected characters to include or 0.
* $queried_warnings (string|int) – Comma-separated list of selected warnings to include or 0.
* $queried_tags (string|int) – Comma-separated list of selected tags to include or 0.
* $queried_ex_genres (string|int) – Comma-separated list of selected genres to exclude or 0.
* $queried_ex_fandoms (string|int) – Comma-separated list of selected fandoms to exclude or 0.
* $queried_ex_characters (string|int) – Comma-separated list of selected characters to exclude or 0.
* $queried_ex_warnings (string|int) – Comma-separated list of selected warnings to exclude or 0.
* $queried_ex_tags (string|int) – Comma-separated list of selected tags to exclude or 0.
* $is_advanced_search (boolean) – Whether advanced search option have been selected or not.

---

### `apply_filters( 'fictioneer_filter_shortcode_article_cards_query_args', $query_args, $args )`
Filters the WP_Query arguments in the `fictioneer_article_cards` shortcode. The optional taxonomy arrays can include categories, tags, fandoms, genres, and characters.

**$query_args:**
* $fictioneer_query_name (string) – `'article_cards'`
* $post_type (array) – `'$args['post_type']'`
* $post_status (string) – `'publish'`
* $ignore_sticky_posts (boolean) – `$args['ignore_sticky']`
* $post__in (array|null) – `$args['post_ids']`
* $order (string) – `$args['order']`
* $orderby (string) – `$args['orderby']`
* $paged (int) – Current main query page number.
* $posts_per_page (int) – `$args['posts_per_page']` or `$args['count']`
* $author_name (string|null) – `$args['author']`
* $author__in (array|null) – `$args['author_ids']`
* $author__not_in (array|null) – `$args['excluded_authors']`
* $category__not_in (array|null) – `$args['excluded_cats']`
* $tag__not_in (array|null) – `$args['excluded_tags']`
* $tax_query (array|null) – `fictioneer_get_shortcode_tax_query( $args )`
* $no_found_rows (boolean|null) – `true` if `$args['count'] > 0`

**$args:**
* $post_type (array) – The post types to query. Default `\['post']`.
* $ignore_sticky (boolean) – Whether to ignore sticky posts. Default `false`.
* $ignore_protected (boolean) – Whether to ignore protected posts. Default `false`.
* $count (int) – Maximum number of posts. Default `-1`.
* $author (boolean|string) – Limit posts to a specific author. Default `false`.
* $order (string) – Order argument. Default `'DESC'`.
* $orderby (string) – Orderby argument. Default `'date'`.
* $page (int) – Current main query page number. Default `1`.
* $posts_per_page (int) – Number of posts per page. Defaults to WordPress.
* $post_ids (array) – Limit posts to specific post IDs. Default empty.
* $author_ids (array) – Limit posts to specific author IDs. Default empty.
* $excluded_authors (array) – Exclude specific author IDs. Default empty.
* $excluded_tags (array) – Exclude specific tag names. Default empty.
* $excluded_cats (array) – Exclude specific category names. Default empty.
* $taxonomies (array) – Array of arrays of required taxonomy names. Default empty.
* $relation (string) – Relationship between taxonomies. Default `'AND'`.
* $classes (string) – String of additional CSS classes. Default empty.

---

### `apply_filters( 'fictioneer_filter_shortcode_blog_query_args', $query_args, $args )`
Filters the WP_Query arguments in the `fictioneer_blog` shortcode.

**$query_args:**
* $fictioneer_query_name (string) – `'blog_shortcode'`
* $post_type (string) – `post`
* $post_status (string) – `'publish'`
* $ignore_sticky_posts (boolean) – `$args['ignore_sticky']`
* $author_name (string|null) – `$args['author']`
* $category__not_in (array|null) – `$args['excluded_cats']`
* $tag__not_in (array|null) – `$args['excluded_tags']`
* $paged (int) – Current main query page number.
* $posts_per_page (int) – `$args['posts_per_page']`
* $tax_query (array|null) – `fictioneer_get_shortcode_tax_query( $args )`

**$args:**
* $posts_per_page (int) – The number of posts per page. Defaults to WordPress.
* $page (int) – Current main query page number. Default `1`.
* $ignore_sticky (boolean) – Optional. Whether to ignore sticky posts. Default `false`.
* $ignore_protected (boolean) – Optional. Whether to ignore protected posts. Default `false`.
* $author (boolean|string) – Limit posts to a specific author. Default `false`.
* $author_ids (array) – Limit posts to specific author IDs. Default empty.
* $author_ids (array) – Limit posts to specific author IDs. Default empty.
* $excluded_authors (array) – Exclude specific author IDs. Default empty.
* $excluded_tags (array) – Exclude specific tag names. Default empty.
* $excluded_cats (array) – Exclude specific category names. Default empty.
* $relation (string) – Relationship between taxonomies. Default `'AND'`.
* $classes (string) – String of additional CSS classes. Default empty.

---

### `apply_filters( 'fictioneer_filter_shortcode_latest_chapters_card_footer', $footer_items, $post, $story, $args )`
Filters the intermediate output array in the `_latest-chapters.php` partial before it is imploded and rendered. Contains statistics with icons such as the number of chapters, words, dates, and so forth.

**$footer_items:**
* $words (string|null) – HTML for the total word count (if more than 0).
* $publish_date (string) – Conditional. HTML for the publish date.
* $modified_date (string) – Conditional. HTML for the modified date.
* $comments (string) – HTML for the number of comments.
* $status (string|null) – Conditional. HTML for the story status.

**Parameters**
* $post (WP_Post) – The post object.
* $story (array|null) – Collection of story post data. Unsafe.
* $args (array) – Arguments passed to the partial.

---

### `apply_filters( 'fictioneer_filter_shortcode_latest_chapters_query_args', $query_args, $args )`
Filters the WP_Query arguments in the `fictioneer_latest_chapters` shortcode. The optional taxonomy arrays can include categories, tags, fandoms, genres, and characters.

**$query_args:**
* $fictioneer_query_name (string) – `'latest_chapters'` or `'latest_chapters_compact'`
* $post_type (string) – `'fcn_chapter'`
* $post_status (string) – `'publish'`
* $order (string) – `$args['order']`
* $orderby (string) – `$args['orderby']`
* $posts_per_page (int) – `$args['count'] + 8` (buffer for invalid posts)
* $post__in (array) – `$args['post_ids']`
* $author_name (string|null) – `$args['author']`
* $category__not_in (array|null) – `$args['excluded_cats']`
* $tag__not_in (array|null) – `$args['excluded_tags']`
* $meta_key (string) – `'fictioneer_chapter_hidden'`
* $meta_value (int) – `0`
* $tax_query (array|null) – `fictioneer_get_shortcode_tax_query( $args )`
* $no_found_rows (boolean) – `true`
* $update_post_term_cache (boolean) – `false`

**$args:**
* $simple (boolean) – Whether to render the simple variants. Default `false`.
* $count (int) – Maximum number of posts. Default `-1`.
* $author (boolean|string) – Limit posts to a specific author. Default `false`.
* $order (string) – Order argument. Default `'DESC'`.
* $orderby (string) – Orderby argument. Default `'date'`.
* $spoiler (boolean) – Optional. Show preview un-obfuscated. Default `false`.
* $source (boolean) – Optional. Show chapter source story. Default `true`.
* $post_ids (array) – Limit posts to specific post IDs. Default empty.
* $author_ids (array) – Limit posts to specific author IDs. Default empty.
* $excluded_authors (array) – Exclude specific author IDs. Default empty.
* $excluded_tags (array) – Exclude specific tag names. Default empty.
* $excluded_cats (array) – Exclude specific category names. Default empty.
* $ignore_protected (boolean) – Whether to ignore protected posts. Default `false`.
* $taxonomies (array) – Array of arrays of required taxonomy names. Default empty.
* $relation (string) – Relationship between taxonomies. Default `'AND'`.
* $classes (string) – String of additional CSS classes. Default empty.

---

### `apply_filters( 'fictioneer_filter_shortcode_latest_posts_query_args', $query_args, $args )`
Filters the WP_Query arguments in the `fictioneer_latest_posts` shortcode. The optional taxonomy arrays can include categories and tags.

**$query_args:**
* $fictioneer_query_name (string) – `'latest_posts'`
* $post_type (string) – `'post'`
* $post_status (string) – `'publish'`
* $ignore_sticky_posts (boolean) – `true`
* $post__in (array) – `$args['post_ids']`
* $order (string) – `'DESC'`
* $orderby (string) – `'date'`
* $posts_per_page (int) – `$args['count']`
* $author_name (string|null) – `$args['author']`
* $category__not_in (array|null) – `$args['excluded_cats']`
* $tag__not_in (array|null) – `$args['excluded_tags']`
* $no_found_rows (boolean) – `true`
* $update_post_term_cache (boolean) – `false`

**$args:**
* $count (int) – Maximum number of posts. Default `-1`.
* $author (boolean|string) – Limit posts to a specific author. Default `false`.
* $post_ids (array) – Limit posts to specific post IDs. Default empty.
* $author_ids (array) – Limit posts to specific author IDs. Default empty.
* $excluded_authors (array) – Exclude specific author IDs. Default empty.
* $excluded_tags (array) – Exclude specific tag names. Default empty.
* $excluded_cats (array) – Exclude specific category names. Default empty.
* $ignore_protected (boolean) – Whether to ignore protected posts. Default `false`.
* $taxonomies (array) – Array of arrays of required taxonomy names. Default empty.
* $relation (string) – Relationship between taxonomies. Default `'AND'`.
* $classes (string) – String of additional CSS classes. Default empty.

---

### `apply_filters( 'fictioneer_filter_shortcode_latest_recommendations_query_args', $query_args, $args )`
Filters the WP_Query arguments in the `fictioneer_latest_recommendations` shortcode. The optional taxonomy arrays can include categories, tags, fandoms, genres, and characters.

**$query_args:**
* $fictioneer_query_name (string) – `'latest_recommendations'` or `'latest_recommendations_compact'`
* $post_type (string) – `'fcn_recommendation'`
* $post_status (string) – `'publish'`
* $post__in (array) – `$args['post_ids']`
* $order (string) – `$args['order']`
* $orderby (string) – `$args['orderby']`
* $posts_per_page (int) – `$args['count']`
* $author_name (string|null) – `$args['author']`
* $category__not_in (array|null) – `$args['excluded_cats']`
* $tag__not_in (array|null) – `$args['excluded_tags']`
* $tax_query (array|null) – `fictioneer_get_shortcode_tax_query( $args )`
* $no_found_rows (boolean) – `true`

**$args:**
* $count (int) – Maximum number of posts. Default `-1`.
* $author (boolean|string) – Limit posts to a specific author. Default `false`.
* $order (string) – Order argument. Default `'DESC'`.
* $orderby (string) – Orderby argument. Default `'date'`.
* $post_ids (array) – Limit posts to specific post IDs. Default empty.
* $author_ids (array) – Limit posts to specific author IDs. Default empty.
* $excluded_authors (array) – Exclude specific author IDs. Default empty.
* $excluded_tags (array) – Exclude specific tag names. Default empty.
* $excluded_cats (array) – Exclude specific category names. Default empty.
* $ignore_protected (boolean) – Whether to ignore protected posts. Default `false`.
* $taxonomies (array) – Array of arrays of required taxonomy names. Default empty.
* $relation (string) – Relationship between taxonomies. Default `'AND'`.
* $classes (string) – String of additional CSS classes. Default empty.

---

### `apply_filters( 'fictioneer_filter_shortcode_latest_stories_card_footer', $footer_items, $post, $story, $args )`
Filters the intermediate output arrays in the `_latest-stories.php` and `_latest-stories-compact.php` partials before they are imploded and rendered. Contains statistics with icons such as the number of chapters, words, dates, and so forth.

**$footer_items:**
* $chapters (string) – HTML for the number of chapters.
* $words (string|null) – HTML for the total word count (if more than 0).
* $publish_date (string) – Conditional. HTML for the publish date.
* $modified_date (string) – Conditional. HTML for the modified date.
* $status (string) – HTML for the status.

**Parameters**
* $post (WP_Post) – The post object.
* $story (array) – Collection of story post data.
* $args (array) – Arguments passed to the partial.

---

### `apply_filters( 'fictioneer_filter_shortcode_latest_stories_query_args', $query_args, $args )`
Filters the WP_Query arguments in the `fictioneer_latest_stories` shortcode. The optional taxonomy arrays can include categories, tags, fandoms, genres, and characters.

**$query_args:**
* $fictioneer_query_name (string) – `'latest_stories'` or `'latest_stories_compact'`
* $post_type (string) – `'fcn_story'`
* $post_status (string) – `'publish'`
* $post__in (array) – `$args['post_ids']`
* $order (string) – `$args['order']`
* $orderby (array)
  * $fictioneer_story_sticky – `'DESC'`
  * `$args['orderby']` – `$args['order']`
* $posts_per_page (int) – `$args['count']`
* $meta_query (array)
  * $relation (string) – `'OR'`
  * (array)
    * $key – `'fictioneer_story_hidden'`
    * $value – `'0'`
  * (array)
    * $key – `'fictioneer_story_hidden'`
    * $compare – `'NOT EXISTS'`
* $author_name (string|null) – `$args['author']`
* $category__not_in (array|null) – `$args['excluded_cats']`
* $tag__not_in (array|null) – `$args['excluded_tags']`
* $tax_query (array|null) – `fictioneer_get_shortcode_tax_query( $args )`
* $no_found_rows (boolean) – `true`

**$args:**
* $count (int) – Maximum number of posts. Default `-1`.
* $author (boolean|string) – Limit posts to a specific author. Default `false`.
* $order (string) – Order argument. Default `'DESC'`.
* $orderby (string) – Orderby argument. Default `'date'`.
* $post_ids (array) – Limit posts to specific post IDs. Default empty.
* $author_ids (array) – Limit posts to specific author IDs. Default empty.
* $excluded_authors (array) – Exclude specific author IDs. Default empty.
* $excluded_tags (array) – Exclude specific tag names. Default empty.
* $excluded_cats (array) – Exclude specific category names. Default empty.
* $ignore_protected (boolean) – Whether to ignore protected posts. Default `false`.
* $taxonomies (array) – Array of arrays of required taxonomy names. Default empty.
* $relation (string) – Relationship between taxonomies. Default `'AND'`.
* $classes (string) – String of additional CSS classes. Default empty.

---

### `apply_filters( 'fictioneer_filter_shortcode_latest_updates_card_footer', $footer_items, $post, $story, $args )`
Filters the intermediate output array in the `_latest-updates.php` partial before it is imploded and rendered. Contains statistics with icons such as the number of chapters, words, dates, and so forth.

**$footer_items:**
* $chapters (string) – HTML for the number of chapters.
* $words (string|null) – HTML for the total word count (if more than 0).
* $modified_date (string) – Conditional. HTML for the modified date.
* $status (string) – HTML for the status.

**Parameters**
* $post (WP_Post) – The post object.
* $story (array) – Collection of story post data.
* $args (array) – Arguments passed to the partial.

**Example:**
```php
function child_remove_modified_date_from_latest_updates( $footer_items ) {
  unset( $footer_items['modified_date'] );

  return $footer_items;
}
add_filter( 'fictioneer_filter_shortcode_latest_updates_card_footer', 'child_remove_modified_date_from_latest_updates' );
```

---

### `apply_filters( 'fictioneer_filter_shortcode_latest_updates_query_args', $query_args, $args )`
Filters the WP_Query arguments in the `fictioneer_latest_updates` shortcode. The optional taxonomy arrays can include categories, tags, fandoms, genres, and characters.

**$query_args:**
* $fictioneer_query_name (string) – `'latest_updates'` or `'latest_updates_compact'`
* $post_type (string) – `'fcn_story'`
* $post_status (string) – `'publish'`
* $post__in (array) – `$args['post_ids']`
* $order (string) – `$args['order']`
* $orderby (string) – `'meta_value'`
* $meta_key (string) – `'fictioneer_chapters_added'`
* $posts_per_page (int) – `$args['count'] + 4` (buffer for invalid posts)
* $meta_query (array)
  * $relation (string) – `'OR'`
  * (array)
    * $key – `'fictioneer_story_hidden'`
    * $value – `'0'`
  * (array)
    * $key – `'fictioneer_story_hidden'`
    * $compare – `'NOT EXISTS'`
* $author_name (string|null) – `$args['author']`
* $category__not_in (array|null) – `$args['excluded_cats']`
* $tag__not_in (array|null) – `$args['excluded_tags']`
* $tax_query (array|null) – `fictioneer_get_shortcode_tax_query( $args )`
* $no_found_rows (boolean) – `true`

**$args:**
* $type (string) – Either `'default'`, `'simple'`, `'single'`, and `'compact'`.
* $single (boolean) – Whether to show only one chapter item. Default `false`.
* $count (int) – Maximum number of posts. Default `-1`.
* $author (boolean|string) – Limit posts to a specific author. Default `false`.
* $order (string) – Order argument. Default `'DESC'`.
* $post_ids (array) – Limit posts to specific post IDs. Default empty.
* $author_ids (array) – Limit posts to specific author IDs. Default empty.
* $excluded_authors (array) – Exclude specific author IDs. Default empty.
* $excluded_tags (array) – Exclude specific tag names. Default empty.
* $excluded_cats (array) – Exclude specific category names. Default empty.
* $ignore_protected (boolean) – Whether to ignore protected posts. Default `false`.
* $taxonomies (array) – Array of arrays of required taxonomy names. Default empty.
* $relation (string) – Relationship between taxonomies. Default `'AND'`.
* $classes (string) – String of additional CSS classes. Default empty.

---

### `apply_filters( 'fictioneer_filter_shortcode_{type}_list_meta', $meta, $post, $story, $chapter )`
Filters the intermediate output array for the meta row in list-type shortcode partials before it is imploded and rendered. Can contain the story, chapter, word count, comment count, chapter count, publish date, author, status, and age rating depending on the shortcode and applied parameters. Type can be `latest_chapter`, `latest_stories`, or `latest_updates`.

**Parameters**
* $meta (array) – Associative array of HTML nodes to be rendered.
* $post (WP_Post) – The current post object.
* $story (array|null) – Collection of story post data. Unsafe.
* $chapter (WP_Post|null) – The chapter post object (only for Latest Updates). Unsafe.

---

### `apply_filters( 'fictioneer_filter_shortcode_{type}_terms', $terms, $post, $args, $story_data )`
Filters the intermediate output array of term HTML nodes (tags, genres, fandoms, etc.) in shortcodes before it is imploded and rendered, with the term IDs as keys. Type can be `latest_stories`, `latest_updates`, `latest_recommendations`, or `article_cards`. You can use `$args['type']` to further differentiate between shortcode variants.

**Parameters**
* $terms (array) – Associative array of HTML nodes to be rendered.
* $post (WP_Post) – The current post object.
* $args (array) – Arguments passed to the shortcode.
* $story_data (array|null) – Collection of story post data. Unsafe.

---

### `apply_filters( 'fictioneer_filter_shortcode_list_attributes', $attributes, $post, $context )`
Filters the intermediate output array of HTML attributes inside list-type shortcodes before they are rendered. The keys are used as attribute names. Make sure to account for already existing attributes.

**Parameters:**
* $attributes (array) - Associative array of attributes to be rendered.
* $post (WP_Post) – The current post object.
* $context (string) - Either `latest-chapters`, `latest-stories`, or `latest-updates`.

---

### `apply_filters( 'fictioneer_filter_shortcode_list_title', $title, $post, $context )`
Filters the intermediate output array of HTML attributes inside list-type shortcodes before they are rendered. The keys are used as attribute names. Make sure to account for already existing attributes.

**Parameters:**
* $title (string) – The current title to be rendered.
* $post (WP_Post) – The current post object.
* $context (string|null) - Either `latest-chapters`, `latest-stories`, or `latest-updates`.

---

### `apply_filters( 'fictioneer_filter_shortcode_showcase_query_args', $query_args, $args )`
Filters the WP_Query arguments in the `fictioneer_showcase` shortcode. The optional taxonomy arrays can include categories, tags, fandoms, genres, and characters.

**$query_args:**
* $fictioneer_query_name (string) – `'showcase'`
* $post_type (string) – `$args['type']`
* $post_status (string) – `'publish'`
* $post__in (array) – `$args['post_ids']`
* $order (string) – `$args['order']`
* $orderby (string) – `$args['orderby']`
* $posts_per_page (int) – `$args['count']`
* $author_name (string|null) – `$args['author']`
* $category__not_in (array|null) – `$args['excluded_cats']`
* $tag__not_in (array|null) – `$args['excluded_tags']`
* $tax_query (array|null) – `fictioneer_get_shortcode_tax_query( $args )`
* $update_post_term_cache (boolean) – `false`
* $no_found_rows (boolean) – `true`

**$args:**
* $post_type (string) – Either `'fcn_collection'`, `'fcn_story'`, `'fcn_chapter'`, or `'fcn_recommendation'`.
* $count (int) – Maximum number of posts. Default `-1`.
* $author (boolean|string) – Limit posts to a specific author. Default `false`.
* $order (string) – Order argument. Default `'DESC'`.
* $orderby (string) – Orderby argument. Default `'date'`.
* $post_ids (array) – Limit posts to specific post IDs. Default empty.
* $author_ids (array) – Limit posts to specific author IDs. Default empty.
* $excluded_authors (array) – Exclude specific author IDs. Default empty.
* $excluded_tags (array) – Exclude specific tag names. Default empty.
* $excluded_cats (array) – Exclude specific category names. Default empty.
* $ignore_protected (boolean) – Whether to ignore protected posts. Default `false`.
* $taxonomies (array) – Array of arrays of required taxonomy names. Default empty.
* $relation (string) – Relationship between taxonomies. Default `'AND'`.
* $no_cap (boolean) – Whether to hide captions. Default `false`.
* $classes (string) – String of additional CSS classes. Default empty.

---

### `apply_filters( 'fictioneer_filter_shortcode_terms_query_args', $query_args, $attr )`
Filters the WP_Term_Query arguments in the `fictioneer_terms` shortcode.

**$query_args:**
* 'fictioneer_query_name' (string) – `'terms_shortcode'`
* 'taxonomy' (string) – As specified in the shortcode. Either `'category'`, `'post_tag'`, `'fcn_genre'`, `'fcn_fandom'`, `'fcn_character'`, or `'fcn_content_warning'`.
* 'orderby' (string) – As specified in the shortcode. Default `'count'`.
* 'order' (string) – As specified in the shortcode. Default `'desc'`.
* 'number' (int|null) – As specified in the shortcode. Default `null`.
* 'hide_empty' (bool) – As specified in the shortcode. Default `true`.

**$attr:**
* 'term_type' (string|null) – Either `'category'`, `'tag'`, `'genre'`, `'fandom'`, `'character'`, or `'warning'`. Default `'tag'`.
* 'post_id' (int|null) – Limit terms to a specific post. Default `0`.
* 'count' (int|null) – Limit number of queried terms. Default `-1` (all).
* 'orderby' (string|null) – Default `'count'`.
* 'order' (string|null) – Default `'desc'`.
* 'show_empty' (boolean|null) – Whether to query unused terms. Default `false`.
* 'show_count' (boolean|null) – Whether to show the term counts. Default `false`.
* 'classes' (string|null) – String of additional outer CSS classes. Default empty.
* 'inner_classes' (string|null) – String of additional inner CSS classes. Default empty.
* 'style' (string|null) – String of additional outer inline styles. Default empty.
* 'inner_style' (string|null) – String of additional inner inline styles. Default empty.
* 'empty' (string|null) – Override message for empty query result. Default empty.

---

### `apply_filters( 'fictioneer_filter_sitemap_page_template_excludes', $excludes )`
Filters the exclusion array of page templates for the custom theme sitemap. By default, these are `'user-profile.php'`, `'singular-bookmarks.php'`, `'singular-bookshelf.php'`, and `'singular-bookshelf-ajax.php'`.

**Parameter:**
* $excludes (array) – Array of excluded page templates.

---

### `apply_filters( 'fictioneer_filter_sof_date_options', $options, $current_url, $args )`
Filters the option array of URL/label tuples for the date popup menu in the `fictioneer_sort_order_filter_interface( $args )` function before it is rendered. Can be any positive integer (days) or [strtotime](https://www.php.net/manual/en/function.strtotime.php) compatible string. See `fictioneer_append_date_query()`. Includes '0', '1', '3', '1 week ago', '1 month ago', '3 months ago', '6 months ago', and '1 year ago'.

**$options:**
* '0' (array) – Tuple of $label (Any Date) and unescaped $url (`...ago=0&...#sof`).
* '1' (array) – Tuple of $label (Past 24 Hours) and unescaped $url (`...ago=1&...#sof`).
* '3' (array) – Tuple of $label (Past 3 Days) and unescaped $url (`...ago=3&...#sof`).
* '1_week_ago' (array) – Tuple of $label (Past Week) and unescaped $url (`...ago=1+week+ago&...#sof`).
* '1_month_ago' (array) – Tuple of $label (Past Month) and unescaped $url (`...ago=1+month+ago&...#sof`).
* '3_months_ago' (array) – Tuple of $label (Past 3 Months) and unescaped $url (`...ago=3+months+ago&...#sof`).
* '6_months_ago' (array) – Tuple of $label (Past 6 Months) and unescaped $url (`...ago=6+months+ago&...#sof`).
* '1_year_ago' (array) – Tuple of $label (Past Year) and unescaped $url (`...ago=1+year+ago&...#sof`).

**Parameters:**
* $current_url (array) – Current URL with all query parameters to sort, order, and filter included. Used to build option links.

**$args:**
* $current_page (int) – Current page if paginated or `1`.
* $post_id (int) – Current post ID.
* $queried_type (string) – Queried post type.
* $query_args (array) – WP_Query arguments used.
* $order (string) – Current order. Defaults to `'DESC'`.
* $orderby (string) – Current orderby. Defaults to `'modified'` in list templates and `'date'` in archives.

---

### `apply_filters( 'fictioneer_filter_sof_orderby_options', $options, $current_url, $args )`
Filters the option array of URL/label tuples for the orderby popup menu in the `fictioneer_sort_order_filter_interface( $args )` function before it is rendered. Allows and includes 'modified', 'date', 'title', and 'rand' (excluded).

**$options:**
* 'modified' (array) – Tuple of $label (Updated) and unescaped $url (`...orderby=modified&...#sof`).
* 'date' (array) – Tuple of $label (Published) and unescaped $url (`...orderby=date&...#sof`).
* 'title' (array) – Tuple of $label (By Title) and unescaped $url (`...orderby=title&...#sof`).

**Parameters:**
* $current_url (array) – Current URL with all query parameters to sort, order, and filter included. Used to build option links.

**$args:**
* $current_page (int) – Current page if paginated or `1`.
* $post_id (int) – Current post ID.
* $queried_type (string) – Queried post type.
* $query_args (array) – WP_Query arguments used.
* $order (string) – Current order. Defaults to `'DESC'`.
* $orderby (string) – Current orderby. Defaults to `'modified'` in list templates and `'date'` in archives.

---

### `apply_filters( 'fictioneer_filter_sof_post_type_options', $options, $current_url, $args )`
Filters the option array of URL/label tuples for the post type popup menu in the `fictioneer_sort_order_filter_interface( $args )` function before it is rendered. Only rendered in archives. Includes 'any', 'post, 'fcn_story', 'fcn_chapter', 'fcn_collection', and 'fcn_recommendation'.

**$options:**
* $any (array) – Tuple of $label (All Posts) and unescaped $url (`...post_type=any&...#sof`).
* $post (array) – Tuple of $label (Blog Posts) and unescaped $url (`...post_type=post&...#sof`).
* $fcn_story (array) – Tuple of $label (Stories) and unescaped $url (`...post_type=fcn_story&...#sof`).
* $fcn_chapter (array) – Tuple of $label (Chapters) and unescaped $url (`...post_type=fcn_chapter&...#sof`).
* $fcn_collection (array) – Tuple of $label (Collections) and unescaped $url (`...post_type=fcn_collection&...#sof`).
* $fcn_recommendation (array) – Tuple of $label (Recommendations) and unescaped $url (`...post_type=fcn_recommendation&...#sof`).

**Parameters:**
* $current_url (array) – Current URL with all query parameters to sort, order, and filter included. Used to build option links.

**$args:**
* $current_page (int) – Current page if paginated or `1`.
* $post_id (int) – Current post ID.
* $queried_type (string) – Queried post type.
* $query_args (array) – WP_Query arguments used.
* $order (string) – Current order. Defaults to `'DESC'`.
* $orderby (string) – Current orderby. Defaults to `'modified'` in list templates and `'date'` in archives.

---

### `apply_filters( 'fictioneer_filter_stories_card_args', $card_args, $args )`
Filters the arguments passed to the `partials/_card-story` template part in the `fictioneer_stories_list( $args )` function, normally added via the `fictioneer_stories_after_content` hook.

**$card_args:**
* $cache (boolean) – Return of `fictioneer_caching_active()`.
* $order (string) – Current query order argument. Default 'desc'.
* $orderby (string) – Current query orderby argument. Default 'modified'.
* $ago (int|string) – Current date query argument part. Default 0.
* $show_latest (boolean|null) – Whether to show the latest chapters. Default `false`.
* $hide_author (boolean|null) – Whether to hide the author. Default `false`.

**$args:**
* $current_page (int) – Current page if paginated or `1`.
* $post_id (int) – Current post ID.
* $stories (WP_Query) – Paginated query of all published stories.
* $queried_type (string) – `'fcn_story'`

---

### `apply_filters( 'fictioneer_filter_stories_query_args', $query_args, $post_id )`
Filters the arguments to query the stories in the `stories.php` template.

**$query_args:**
* $fictioneer_query_name (string) – `'stories_list'`
* $post_type (string) – `'fcn_story'`
* $post_status (string) – `'publish'`
* $order (string) – `'DESC'` or `'ASC'`
* $orderby (array) – `'modified'`, `'date'`, `'title'`, or `'rand'`
* $paged (int) – Current page number or `1`.
* $posts_per_page (int) – `get_option( 'posts_per_page' )`
* $meta_query (array)
  * $relation (string) – `'OR'`
  * (array)
    * $key – `'fictioneer_story_hidden'`
    * $value – `'0'`
  * (array)
    * $key – `'fictioneer_story_hidden'`
    * $compare – `'NOT EXISTS'`
* $update_post_term_cache – `! get_option( 'fictioneer_hide_taxonomies_on_story_cards' )`
* $date_query – `fictioneer_append_date_query()`

**Parameters:**
* $post_id (int) – Current post ID.

---

### `apply_filters( 'fictioneer_filter_stories_statistics', $statistics, $args )`
Filters the statistics for all stories rendered by the `fictioneer_stories_statistics( $args )` function, normally added via the `fictioneer_stories_after_content` hook. Each tuple in the `$statistics` array has `$label` and `$content`.

**$statistics:**
* $stories (string) – Number of published stories.
* $words (string) – Total word count of published stories.
* $comments (string) – Total number of comments in stories/chapters.
* $reading (string) – Total reading time approximated from word count.

**$args:**
* $current_page (int) – Current page if paginated or `1`.
* $post_id (int) – Current post ID.
* $stories (WP_Query) – Paginated query of all published stories.

---

### `apply_filters( 'fictioneer_filter_story_buttons', $output, $args )`
Filters the intermediate output array of the `fictioneer_get_story_buttons( $args )` function before it is imploded and returned. Used inside `fictioneer_story_actions()` and rendered via the `fictioneer_story_after_content` hook.

**$output:**
* $subscribe (string|null) – Optional. HTML of the subscribe button and popup menu.
* $epub (string|null) – Optional. HTML for the ePUB (generated) download button.
* $ebook (string|null) – Optional. HTML for the ebook (uploaded) download button.
* $reminder (string|null) – Optional. HTML for the Read Later button.
* $follow (string|null) – Optional. HTML for the Follow button.

**$args:**
* $story_data (array) – Collection of story data.
* $story_id (int) – Current story (post) ID.

---

### `apply_filters( 'fictioneer_filter_story_header_classes', $classes, $args )`
Filters the intermediate output array of CSS classes for the story header before it is imploded and rendered. Depending on the Customizer and story settings, it can include `story__header`, `_no-tax`, `_no-thumbnail`, `padding-top`, `padding-left`, and `padding-right`.

**$classes:**
* (array) – Collection of strings depending on the Customizer and story settings; can include `story__header`, `_no-tax`, `_no-thumbnail`, `padding-top`, `padding-left`, and `padding-right`.

**$args:**
* $story_data (array) – Collection of story data.
* $story_id (int) – Current story (post) ID.
* $context (string|null) – Either `null` or `'shortcode'`. Default `null`. Unsafe.

---

### `apply_filters( 'fictioneer_filter_media_buttons', $output, $args )`
Filters the intermediate output array of the `fictioneer_filter_media_buttons( $args )` function before it is imploded and returned. Used in chapter, posts, and stories.

**$output:**
* $share (string|null) – Optional. HTML for share modal button.
* $post_rss (string|null) – Optional. HTML for the post RSS feed (if any).
* $feedly (string|null) – Optional. HTML for the Feedly link.
* $inoreader (string|null) – Optional. HTML for the Inoreader link.

**$args:**
* $post_id (int|null) – Optional. The post ID to use. Defaults to current post ID. Unsafe.
* $post_type (string|null) – Optional. The post type to use. Defaults to current post type. Unsafe.
* $share (bool|null) – Optional. Whether to show the share modal button. Default true. Unsafe.
* $rss (bool|null) – Optional. Whether to show the RSS feed buttons. Default true. Unsafe.

---

### `apply_filters( 'fictioneer_filter_splide_breakpoints', $breakpoints, $json_string, $uid )`
Filters the associative array of Splide breakpoints returned by the `fictioneer_extract_splide_breakpoints()` function. These breakpoints are used to generate dynamic placeholder styles for a specific slider. Modifying the breakpoints at this stage is generally inadvisable, the filter exists primarily for completeness and edge cases.

**Parameters:**
* $breakpoints (array) – Breakpoint data or empty.
* $json_string (string) – Stringified Splide JSON.
* $uid (string|null) – Optional. Unique ID of the target element (only for reference).

---

### `apply_filters( 'fictioneer_filter_splide_loading_style', $style, $uid, $breakpoints, $json_string )`
Filters the dynamically generated loading style for a specific Splide slider before it is minified, wrapped in a `<style>` tag, and returned by the `fictioneer_get_splide_loading_style()` function. The style is rendered within the `<body>` and is removed once the slider is initialized.

**Parameters:**
* $style (string) – The loading style or empty.
* $uid (string) – Unique ID of the target element (used as CSS class).
* $breakpoints (array) – Breakpoint data or empty.
* $json_string (string) – Stringified Splide JSON.

**Example:**
```php
function child_extend_splide_loading_style( $style, $uid ) {
  $style .= ".{$uid}._splide-placeholder {opacity: 0.5;}";

  return $style;
}
add_filter( 'fictioneer_filter_splide_loading_style', 'child_extend_splide_loading_style', 10, 2 );
```

---

### `apply_filters( 'fictioneer_filter_splide_placeholders', $html, $uid, $ttb )`
Filters the HTML of the Splide placeholders returned by the `fictioneer_get_splide_placeholders()` function (default is an empty string). These placeholders are only used in shortcodes and can inject custom markup for arrows and pagination, though additional CSS may be required. For more information, refer to the [Splide documentation](https://splidejs.com/guides/arrows/#custom-arrows).

**Parameters:**
* $html (string) – The HTML for the Splide arrows. Default empty.
* $uid (string|null) – Optional. Unique ID of the target element (only for reference).
* $ttb (bool) – Whether the arrows are in a list-type shortcode (top-to-bottom). Default `false`.

**Example:**
```php
function child_add_custom_splide_arrows( $html, $uid, $ttb ) {
  $ttb_class = $ttb ? 'splide__arrows--ttb' : '';

  return '<div class="splide__arrows ' . $ttb_class . '"><button class="splide__arrow splide__arrow--prev"><i class="fa-regular fa-circle-right"></i></button><button class="splide__arrow splide__arrow--next"><i class="fa-regular fa-circle-right"></i></button></div>';
}
add_filter( 'fictioneer_filter_splide_placeholders', 'child_add_custom_splide_arrows', 10, 3 );
```

---

### `apply_filters( 'fictioneer_filter_static_content', $content, $post )`
Filters the HTML returned by `fictioneer_get_static_content()`. Allows you to customize the cached content itself, the result not being cached. The filter is also applied if the static cache is disabled for the post, so this can be used in place for the `the_content` filter. Relies on the global post and currently only used in chapters.

**Parameters:**
* $content (string) – The post content as HTML, all normal filters applied.
* $post (WP_Post) – The global post object.

---

### `apply_filters( 'fictioneer_filter_static_content_true', $force, $post )`
Filters the last guard clause for returning the cached content, default `true`. Allows you to exclude posts based on broader conditions. Relies on the global post and currently only used in chapters.

**Parameters:**
* $force (boolean) – Whether to return the cached HTML. Default `true`.
* $post (WP_Post) – The global post object.

---

### `apply_filters( 'fictioneer_filter_story_card_footer', $footer_items, $post, $story, $args )`
Filters the intermediate output array in the `_card-story.php` partial before it is imploded and rendered. Contains statistics with icons such as the number of chapters, publishing date, comments, and so forth.

**$footer_items:**
* $chapters (string) – HTML for the number of chapters.
* $words (string) – HTML for the total word count.
* $publish_date (string) – Conditional. HTML for the publish date.
* $modified_date (string) – Conditional. HTML for the modified date.
* $author (string) – Conditional. HTML for the author.
* $comments (string) – HTML for the number of comments.
* $status (string) – HTML for the status.

**Parameters**
* $post (WP_Post) – The post object.
* $story (array) – Story data.
* $args (array) – Arguments passed to the partial.

**Example:**
```php
function child_add_item_to_story_card_footers( $footer_items ) {
  $footer_items['star'] = '<span class="card__footer-star"><i class="card-footer-icon fa-solid fa-star" title="Star"></i> Star</span>';

  return $footer_items;
}
add_filter( 'fictioneer_filter_story_card_footer', 'child_add_item_to_story_card_footers' );
```

---

### `apply_filters( 'fictioneer_filter_story_chapter_posts_query', $query_args, $story_id, $chapter_ids )`
Filters the arguments for the story chapter posts query, an utility function called on story and chapter pages. There are two query variants depending on whether the `FICTIONEER_QUERY_ID_ARRAY_LIMIT` (1000) is exceeded or not.

**$query_args:**
* $post_type (string) - Which post types to query. Default `'fcn_chapter'`.
* $post_status (string) - Which post status to query. Default `'publish'`.
* $post__in (array|null) - Array chapter IDs. Only used if the ID limit is not exceeded.
* $orderby (string) - Only used with `'post__in'`. Default `'post__in'`.
* $meta_key (string) - Only used if the ID limit is exceeded. Default `'fictioneer_chapter_story'`.
* $meta_value (int) - Only used with `'meta_key'`. Default story ID.
* $ignore_sticky_posts (boolean) - Whether to ignore sticky (blog) posts. Default `true`.
* $posts_per_page (int) - How many posts to query. Default `-1` (all).
* $no_found_rows (boolean) - Do not return number of rows. Default `true`.
* $update_post_term_cache (boolean) - Do not update posts terms cache. Default `true`.

**Parameters:**
* $story_id (int) – The story ID.
* $chapter_ids (array) – Array with chapter IDs assigned to the story.

**Example:**
```php
function child_show_scheduled_chapters( $query_args ) {
  $query_args['post_status'] = ['publish', 'future'];

  return $query_args;
}
add_filter( 'fictioneer_filter_story_chapter_posts_query', 'child_show_scheduled_chapters' );
```

---

### `apply_filters( 'fictioneer_filter_story_footer_meta', $meta_output, $args, $post )`
Filters the intermediate output array of story meta data in the `_story-footer.php` partial before it is imploded and rendered. Contains the status, publish date, word count, age rating, and checkmark (if enabled).

**meta_output:**
* $status (string) – HTML snippet for the status.
* $date (string) – HTML snippet for the date.
* $words (string) – HTML snippet for the word count.
* $rating (string) – HTML snippet for the age rating.
* $checkmark (string) – HTML snippet for the checkmark.

**Parameters:**
* $args (array) – Relevant story data: 'story_id' (int) and 'story_data' (array).
* $post (WP_Post) – The post object of the story.

---

### `apply_filters( 'fictioneer_filter_story_identity', $output, $story_id, $story )`
Filters the intermediate output array in the `_story-header.php` partial before it is imploded and rendered. Contains the HTML for the story title (safe) and author meta nodes (see `fictioneer_get_story_author_nodes()`).

**$output:**
* $title (string) – HTML for the story title `<h1>`.
* $meta (string) – HTML for the story author(s) meta row.

**Parameters:**
* $story_id (int) – The story ID.
* $story (array) – Array with story data from `fictioneer_get_story_data()`.

---

### `apply_filters( 'fictioneer_filter_story_page_cover_html', $html, $story, $args )`
Filters the HTML of the story page cover returned by the `fictioneer_get_story_page_cover()` function.

**Parameters:**
* $html (string) – The HTML to be rendered.
* $story (array) – Array of story data.
* $args (array) – Optional arguments.

**Example:**
```php
function child_add_status_to_story_page_cover( $html, $story ) {
  $extra_html = '<div>' . fcntr( $story['status'] ) . '</div></figure>'; // Does not have styling, though

  return mb_ereg_replace( '</figure>', $extra_html, $html ); // Multi-byte safe
}
add_filter( 'fictioneer_filter_story_page_cover_html', 'child_add_status_to_story_page_cover', 10, 2 );
```

---

### `apply_filters( 'fictioneer_filter_story_word_count', $word_count, $post_id )`
Filters the total word count of a story after sanitization (greater than or equal to 0) and before `fictioneer_multiply_word_count()` is applied, returning a positive integer. The word count is only calculated and updated when a post associated with the story is saved.

**Parameters:**
* $word_count (int) – The word count from the `_word_count` meta field.
* $post_id (int|null) – The post ID of the story. Unsafe.

---

### `apply_filters( 'fictioneer_filter_subscribe_buttons', $buttons, $post_id, $author_id, $feed )`
Filters the intermediate output array of the `fictioneer_get_subscribe_options( $post_id, $author_id, $feed )` function before it is imploded and returned. Normally accounts for Patreon, Ko-Fi, SubscribeStar, [Feedly](https://feedly.com/), and [Inoreader](https://www.inoreader.com/).

**$buttons:**
* $patreon (string) – Optional. Link to Patreon if set for post or author.
* $kofi (string) – Optional. Link to Ko-Fi if set for post or author.
* $subscribestar (string) – Optional. Link to SubscribeStar if set for post or author.
* $feedly (string) – RSS subscription link to Feedly.
* $inoreader (string) – RSS subscription link to Inoreader.

**Parameters:**
* $post_id (int) – The post ID. Defaults to the current post ID.
* $author_id (int) – The author ID. Defaults to the current author ID.
* $feed (string|null) – The RSS feed URL. Unsafe.

---

### `apply_filters( 'fictioneer_filter_taxonomy_pills_group', $group, $key, $context )`
Filters the groups passed to `fictioneer_get_taxonomy_pills()` before they are looped and rendered, allowing you to limit the number of taxonomies or randomize the order, for example.

**Parameters:**
* $group (array) – Array of WP_Term objects.
* $key (string) – The group type (tags, fandoms, genres, characters, or warnings).
* $context (string) – The render context or location. Can be empty.

---

### `apply_filters( 'fictioneer_filter_taxonomy_submenu_html', $html, $terms, $type, $hide_empty )`
Filters the built HTML of the taxonomy submenu before it is rendered.

**Parameters:**
* $html (string) – The current submenu HTML to be rendered.
* $terms (array) – Array of WP_Term objects.
* $type (string) – The taxonomy type.
* $hide_empty (boolean) – Whether to hide empty terms. Default `true`.

---

### `apply_filters( 'fictioneer_filter_cached_taxonomy_submenu_html', $html, $type, $hide_empty )`
Filters the Transient HTML of the taxonomy submenu before it is rendered. The difference to the non-cached filter is that the terms are not queried in this case.

**Parameters:**
* $html (string) – The current submenu HTML to be rendered.
* $type (string) – The taxonomy type.
* $hide_empty (boolean) – Whether to hide empty terms. Default `true`.

---

### `apply_filters( 'fictioneer_filter_caching_active', $bool, $context, $compatibility_mode )`
Filters the boolean return value of the check for active known cache plugins (not necessarily that the cache is enabled in the plugin).

**Parameters:**
* $bool (boolean) – Whether a know cache plugin is active.
* $context (string|null) – Optional. The context in which the check is used.
* $compatibility_mode (boolean) – Whether the cache compatibility mode option is enabled.

---

### `apply_filters( 'fictioneer_filter_taxonomy_submenu_note', $note, $taxonomy )`
Filters the string of the note rendered above the links in the taxonomy submenu. By default, that is "Choose a {taxonomy} to browse", with the dynamic part taken from the singular name of the taxonomy object.

**Parameters:**
* $note (string) – The current note to be rendered.
* $taxonomy (WP_Taxonomy) – The taxonomy object.

---

### `apply_filters( 'fictioneer_filter_taxonomy_submenu_query_args', $query_args, $type )`
Filters the query arguments used for the taxonomy submenu `get_terms()` call.

**Parameters:**
* $query_args (array) – Query arguments with 'taxonomy' and 'hide_empty' keys.
* $type (string) – The taxonomy type.

---

### `apply_filters( 'fictioneer_filter_translations', $strings )`
Filters the source array of selected translation strings used in the theme, see `fcntr()` function in `includes/functions/_utility.php`. You cannot translate the whole theme with this, but give it a personal touch.

**Parameters:**
* $strings (array) – Associative array of translation keys and values.

---

### `apply_filters( 'fictioneer_filter_translations_static', $strings )`
Same as the `fictioneer_filter_translations` filter, but only applied once when the `fcntr()` function is first called and cached in a static variable to save system resources. Using this is recommended if possible.

---

### `apply_filters( 'fictioneer_filter_user_menu_items', $items )`
Filters the intermediate output array of the `fictioneer_user_menu_items()` function before it is imploded and returned. Contains links to the user’s account, reading lists, and bookmarks as well as the site’s Discord, site settings modal toggle, and logout link. Used in the `fictioneer_render_icon_menu()` function if the user is logged in.

**$items:**
* $account (string|null) – Optional. List item with link to the user’s account.
* $site_settings (string) – List item with site settings modal toggle.
* $bookshelf (string|null) – Optional. List item with link to the user’s lists.
* $bookmarks (string|null) – Optional. List item with link to the user’s bookmarks.
* $discord (string|null) – Optional. List item with link to the site’s Discord server.
* $logout (string) – List item with link to log out of the site.

---

### `apply_filters( 'fictioneer_filter_user_patreon_validation', $valid, $user_id, $patreon_tiers )`
Filters the check result of whether the user’s Patreon data is still valid. Because there is no continuous connection to Patreon, the data expires after a set amount of time, one week in seconds by default (defined as `FICTIONEER_PATREON_EXPIRATION_TIME`).

**Parameters:**
* $valid (boolean) – Result of the check. True if valid, false if expired.
* $user_id (int) – The user the check is for. 0 if not logged in.
* $patreon_tiers (array) – The user’s Patreon tiers. Can be empty.

---

### `apply_filters( 'fictioneer_filter_verify_unpublish_access', $authorized, $post_id, $post )`
Filters the boolean return value of the `fictioneer_verify_unpublish_access()` function. By default, this verifies whether the current user has access to unpublished posts (not drafts).

**Parameters:**
* $authorized (boolean) – Whether the current user can access unpublished posts.
* $post_id (int) – The post ID.
* $post (WP_Post) – The post object.

---

### `apply_filters( 'fictioneer_filter_word_count', $word_count, $post_id )`
Filters the word count of a post after sanitization (greater than or equal to 0) and before `fictioneer_multiply_word_count()` is applied, returning a positive integer. The word count is only calculated and updated when a post is saved.

**Parameters:**
* $word_count (int) – The word count from the `fictioneer_story_total_word_count` meta field.
* $post_id (int|null) – The post ID. Unsafe.
