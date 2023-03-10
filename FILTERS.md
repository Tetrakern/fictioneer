# Filters
The following [filters hooks](https://developer.wordpress.org/reference/functions/add_filter/) can be used to customize the output of action hooks and other functions without having to modify said functions. Filters can be easily added or overwritten in child themes or plugins. See `includes/functions/hooks/`.

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

### `apply_filters( 'fictioneer_filter_breadcrumbs_array', $breadcrumbs, $args )`
Filters the array of breadcrumb tuples inside the `fictioneer_get_breadcrumbs( $args )` function before the HTML is build.

**$breadcrumbs:**
* $0 (string) – Label of the breadcrumb.
* $1 (string|boolean|null) – URL of the breadcrumb or falsy.

**$args:**
* $post_type (string|null) – Current post type. Unsafe.
* $post_id (int|null) – Current post ID. Unsafe.

---

### `apply_filters( 'fictioneer_filter_card_control_icons', $icons, $story_id, $chapter_id )`
Filters the intermediate output array of the card control icons inside the `fictioneer_get_card_controls( $story_id, $chapter_id )` function before being rendered. Note that card controls will not be rendered without a story ID.

**$icons:**
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

### `apply_filters( 'fictioneer_filter_list_chapter_meta_row', $output, $data, $args )`
Filters the intermediate output array in the `fictioneer_get_list_chapter_meta_row( $data, $args )` function before it is imploded and returned in the `_story-content.php` or `fictioneer_chapter_list` shortcode.

**output:**
* $warning (string|null) – Optional. Warning node.
* $bullet-after-warning (string|null) – Optional. `&bull;` node with `list-view` class.
* $password (string|null) – Optional. Password node.
* $bullet-after-password (string|null) – Optional. `&bull;` node with `list-view` class.
* $date (string) – Time node.
* $bullet-after-date (string) – `&bull;` node.
* $words (string) – Words node.

**data:**
* $id (int) – ID of the chapter.
* $warning (string) – Warning note or empty string.
* $warning_color (string) – CSS color declaration or empty string.
* $password (boolean) – Whether the chapter requires a password.
* $timestamp (string) – Timestamp as ISO 8601 format.
* $list_date (string) – Displayed chapter date for the list view.
* $grid_date (string|null) – Optional. Displayed chapter date for the grid view.
* $words (int) – Chapter word count.

**args:**
* $grid (boolean|null) – Optional. `true` if the row needs to account for grid view.

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
* $prev_index (int|boolean) – Index of previous chapter or false.
* $next_index (int|boolean) – Index of next chapter or false.

---

### `apply_filters( 'fictioneer_filter_chapter_support_links', $support_links, $args )`
Filters the intermediate output array of the chapter support links in the `fictioneer_chapter_support_links( $args )` function before it is looped and rendered. Each item is another array with `'label'`, `'icon'` markup, and `'link'`.

**$support_links:**
* $topwebfiction (string|null) – Link to www.topwebfiction.com for the story. Unsafe.
* $patreon (string|null) – Link to the author’s Patreon page. Unsafe.
* $kofi (string|null) – Link to the author’s Ko-Fi page. Unsafe.
* $subscribestar (string|null) – Link to the author’s SubscribeStar page. Unsafe.
* $paypal (string|null) – Donation link to the author’s PayPal. Unsafe.
* $donation (string|null) – Generic donation link. Unsafe.

**$args:**
* $author (WP_User) – Author of the chapter.
* $story_post (WP_Post|null) – Post object of the story. Unsafe.
* $story_data (array|null) – Collection of story data. Unsafe.
* $chapter_id (int) – Chapter ID.
* $chapter_title (string) – Safe chapter title.
* $chapter_password (string) – Chapter password or empty string.
* $chapter_ids (array) – IDs of visible chapters in the same story or empty array.
* $current_index (int) – Current index in the chapter list.
* $prev_index (int|boolean) – Index of previous chapter or false.
* $next_index (int|boolean) – Index of next chapter or false.

---

### `apply_filters( 'fictioneer_filter_chapters_card_args', $card_args, $args )`
Filters the arguments passed to the `partials/_card-chapter` template part in the `fictioneer_chapters_list( $args )` function, normally added via the `fictioneer_chapters_after_content` hook.

**$card_args:**
* $cache (boolean) – Return of `fictioneer_caching_active()`.

**$args:**
* $current_page (int) – Current page number of pagination or `1`.
* $post_id (int) – Current post ID.
* $chapters (WP_Query) – Paginated query of all visible chapters.
* $queried_type (string) – `'fcn_chapter'`

---

### `apply_filters( 'fictioneer_filter_chapters_query_args', $query_args, $post_id )`
Filters the arguments to query the chapters in the `chapters.php` template.

**$query_args:**
* $post_type (string) – `'fcn_chapter'`
* $post_status (string) – `'publish'`
* $orderby (string) – `'modified'`
* $order (string) – `'DESC'`
* $paged (int) – Current page if paginated or `1`.
* $posts_per_page (int) – `get_option( 'posts_per_page' )`

**Parameters:**
* $post_id (int) – Current post ID.

---

### `apply_filters( 'fictioneer_filter_collections_card_args', $card_args, $args )`
Filters the arguments passed to the `partials/_card-collection` template part in the `fictioneer_collections_list( $args )` function, normally added via the `fictioneer_collections_after_content` hook.

**$card_args:**
* $cache (boolean) – Return of `fictioneer_caching_active()`

**$args:**
* $current_page (int) – Current page number of pagination or `1`.
* $post_id (int) – Current post ID.
* $collections (WP_Query) – Paginated query of all visible collections.
* $queried_type (string) – `'fcn_collection'`

---

### `apply_filters( 'fictioneer_filter_collections_query_args', $query_args, $post_id )`
Filters the arguments to query the collections in the `collections.php` template.

**$query_args:**
* $post_type (string) – `'fcn_collection'`
* $post_status (string) – `'publish'`
* $orderby (string) – `'modified'`
* $order (string) – `'DESC'`
* $paged (int) – Current page if paginated or `1`.
* $posts_per_page (int) – `get_option( 'posts_per_page' )`

**Parameters:**
* $post_id (int) – Current post ID.

---

### `apply_filters( 'fictioneer_filter_comments', $comments, $post_id )`
Filters the queried comments in the `comments.php` template and `fictioneer_ajax_get_comment_section()` function.

**Parameters:**
* $comments (array) – The queried comments.
* $post_id (int) – Current post ID.

**Hooked filters:**
* `fictioneer_shift_sticky_comments( $comments )` – Shift sticky comments to the top. Priority 10.

---

### `apply_filters( 'fictioneer_filter_comment_badge', $output, $badge, $badge_class )`
Filters the HTML of the `fictioneer_get_comment_badge( $user, $comment, $post_author_id )` function before it is returned for rendering. The badge class and label are inserted into `<div class="fictioneer-comment__badge CLASS"><span>LABEL</span></div>`. Possible label classes are `is-author`, `is-admin`, `is-moderator`, `is-supporter`, and `badge-override`.

**Parameters:**
* $output (string) – Complete HTML of the comment badge.
* $badge (string) – Label of the badge.
* $badge_class (string) – Class of the badge.

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

### `apply_filters( 'fictioneer_filter_fonts', $fonts )`
Filters the return array of the `fictioneer_get_fonts()` function, used to set up the chapter font options.

**Parameters:**
* $fonts (array) – Numeric array of fonts items with CSS name (`css`), display name (`name`), and fallbacks (`alt`).

---

### `apply_filters( 'fictioneer_filter_font_colors', $colors )`
Filters the return array of the `fictioneer_get_font_colors()` function, used to set up the chapter font color options.

**Parameters:**
* $fonts (array) – Numeric array of color items with CSS name (`css`) and display name (`name`).

---

### `apply_filters( 'fictioneer_filter_header_image', $header_image_url, $post_id )`
Filters the URL of the header image in the `header.php` template.

**Parameters:**
* $header_image_url (string) – URL of the current header image, either global or custom.
* $post_id (int|null) – Current post ID. Unsafe since this may be an archive, author, etc.

---

### `apply_filters( 'fictioneer_filter_inline_storage', $attributes )`
Filters the intermediate output array (tuples) for the `#inline-storage` data attributes before it is mapped, imploded, and rendered in the `header.php` template. These values are important for several scripts and stored in the `fcn_inlineStorage` JavaScript constant.

**attributes:**
* $permalink (['data-permalink', string]) – Escaped current URL.
* $homelink (['data-homelink', string]) – Escaped home URL.
* $post_id (['data-post-id', int|null]) – Current post ID. Unsafe since this may be an archive, author, etc.
* $story_id (['data-story-id', int|null]) – Current story ID (if story or chapter). Unsafe.

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

### `apply_filters( 'fictioneer_filter_recommendations_card_args', $card_args, $args )`
Filters the arguments passed to the `partials/_card-recommendation` template part in the `fictioneer_recommendations_list( $args )` function, normally added via the `fictioneer_recommendations_after_content` hook.

**$card_args:**
* $cache (boolean) – Return of `fictioneer_caching_active()`

**$args:**
* $current_page (int) – Current page number of pagination or `1`.
* $post_id (int) – Current post ID.
* $recommendations (WP_Query) – Paginated query of all visible recommendations.
* $queried_type (string) – `'fcn_recommendation'`

---

### `apply_filters( 'fictioneer_filter_recommendations_query_args', $query_args, $post_id )`
Filters the arguments to query the recommendations in the `recommendations.php` template.

**$query_args:**
* $post_type (string) – `'fcn_recommendation'`
* $post_status (string) – `'publish'`
* $orderby (string) – `'publish_date'`
* $order (string) – `'DESC'`
* $paged (int) – Current page if paginated or `1`.
* $posts_per_page (int) – `get_option( 'posts_per_page' )`

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
* 'data-public-caching' (string|null) – Optional. Indicates that public caches are served to all users.
* 'data-ajax-auth' (string|null) – Optional. Indicates that the logged-in status of users is set via AJAX.
* 'data-edit-time' (string|null) – Optional. Time in minutes that logged-in users can edit their comments.

---

### `apply_filters( 'fictioneer_filter_rss_link', $feed, $post_type, $post_id )`
Filters the RSS link returned by the `fictioneer_get_rss_link( $post_type, $post_id )` function. Note that the function will always return `false` if the `fictioneer_enable_theme_rss` option is not enabled, regardless of this filter.

**Parameters:**
* $feed (int) – The escaped RSS feed URL, either the main or story (chapters) feed.
* $post_type (string) – The post type the RSS is for. This can differ from the current post type.
* $post_id (int) – The post ID the RSS is for. This can differ from the current post ID.

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

### `apply_filters( 'fictioneer_filter_shortcode_blog_query_args', $query_args, $args )`
Filters the query arguments in the `fictioneer_blog` shortcode.

**$query_args:**
* $post_type (string) – `post`
* $post_status (string) – `'publish'`
* $author_name (string|null) – `$args['author']`
* $tag__not_in (\[string]|null) – Array extracted from `$args['exclude_tag_ids']`.
* $category__not_in (\[string]|null) – Array extracted from `$args['exclude_cat_ids']`.
* $paged (int) – Current main query page.
* $posts_per_page (int) – `$args['per_page']` or theme default.
* $tax_query (array|null) – Query arguments for taxonomies.

**$args:**
* $per_page (string|null) – Optional. The number of posts per page.
* $author (string|null) – Optional. The author provided by the shortcode.
* $exclude_tag_ids (string|null) – Optional. Comma-separated list of tag IDs.
* $exclude_cat_ids (string|null) – Optional. Comma-separated list of category IDs.
* $categories (string|null) – Optional. Comma-separated list of category names.
* $tags (string|null) – Optional. Comma-separated list of tag names.
* $rel (string|null) – Optional. Relationship between taxonomies (`AND` or `OR`).
* $class (string|null) – Optional. Additional CSS classes.

---

### `apply_filters( 'fictioneer_filter_shortcode_latest_chapters_query_args', $query_args, $args )`
Filters the query arguments in the `fictioneer_latest_chapters` shortcode. The optional taxonomy arrays can include categories, tags, fandoms, genres, and characters.

**$query_args:**
* $post_type (string) – `'fcn_chapter'`
* $post_status (string) – `'publish'`
* $author_name (string|null) – `$args['author']`
* $post__in (array) – `$args['post_ids']`
* $meta_key (string) – `'fictioneer_chapter_hidden'`
* $meta_value (int) – `0`
* $orderby (string) – `$args['orderby']`
* $order (string) – `$args['order']`
* $posts_per_page (int) – `$args['count']`
* $no_found_rows (boolean) – `true`
* $update_post_term_cache (boolean) – `false`

**$args:**
* $simple (boolean) – Whether to render the simple variants. Default `false`.
* $author (boolean|string) – The author provided by the shortcode. Default `false`.
* $count (int) – The number of posts provided by the shortcode. Default `1`.
* $orderby (string) – Optional. Default `'date'`.
* $order (string) – Optional. Default `'desc'`.
* $spoiler (boolean) – Optional. Show preview un-obfuscated. Default `false`.
* $source (boolean) – Optional. Show chapter source story. Default `true`.
* $post_ids (\[string]) – Array of post IDs. Default empty.
* $taxonomies (\[array]) – Array of taxonomy arrays (names). Default empty.
* $relation (string) – Relationship between taxonomies. Default `'AND'`.
* $classes (\[string]) – Array of additional CSS classes. Default empty.

---

### `apply_filters( 'fictioneer_filter_shortcode_latest_posts_query_args', $query_args, $args )`
Filters the query arguments in the `fictioneer_latest_posts` shortcode. The optional taxonomy arrays can include categories and tags.

**$query_args:**
* $post_type (string) – `'post'`
* $post_status (string) – `'publish'`
* $post__in (array) – `$args['post_ids']`
* $author_name (string|null) – `$args['author']`
* $has_password (boolean) – `false`
* $orderby (string) – `'date'`
* $order (string) – `'desc'`
* $posts_per_page (int) – `$args['count']`
* $ignore_sticky_posts (boolean) – `true`
* $no_found_rows (boolean) – `true`

**$args:**
* $author (boolean|string) – The author provided by the shortcode. Default `false`.
* $count (int) – The number of posts provided by the shortcode. Default `1`.
* $post_ids (\[string]) – Array of post IDs. Default empty.
* $taxonomies (\[array]) – Array of taxonomy arrays (names). Default empty.
* $relation (string) – Relationship between taxonomies. Default `'AND'`.
* $classes (\[string]) – Array of additional CSS classes. Default empty.

---

### `apply_filters( 'fictioneer_filter_shortcode_latest_recommendations_query_args', $query_args, $args )`
Filters the query arguments in the `fictioneer_latest_recommendations` shortcode. The optional taxonomy arrays can include categories, tags, fandoms, genres, and characters.

**$query_args:**
* $post_type (string) – `'fcn_recommendation'`
* $post_status (string) – `'publish'`
* $author_name (string|null) – `$args['author']`
* $post__in (array) – `$args['post_ids']`
* $orderby (string) – `$args['orderby']`
* $order (string) – `$args['order']`
* $posts_per_page (int) – `$args['count']`
* $no_found_rows (boolean) – `true`

**$args:**
* $author (boolean|string) – The author provided by the shortcode. Default `false`.
* $count (int) – The number of posts provided by the shortcode. Default `1`.
* $orderby (string) – Optional. Default `'date'`.
* $order (string) – Optional. Default `'desc'`.
* $post_ids (\[string]) – Array of post IDs. Default empty.
* $taxonomies (\[array]) – Array of taxonomy arrays (names). Default empty.
* $relation (string) – Relationship between taxonomies. Default `'AND'`.
* $classes (\[string]) – Array of additional CSS classes. Default empty.

---

### `apply_filters( 'fictioneer_filter_shortcode_latest_stories_query_args', $query_args, $args )`
Filters the query arguments in the `fictioneer_latest_stories` shortcode. The optional taxonomy arrays can include categories, tags, fandoms, genres, and characters.

**$query_args:**
* $post_type (string) – `'fcn_story'`
* $post_status (string) – `'publish'`
* $author_name (string|null) – `$args['author']`
* $post__in (array) – `$args['post_ids']`
* $meta_key (string) – `'fictioneer_story_sticky'`
* $orderby (string) – `'meta_value ' . $args['orderby']`
* $order (string) – `$args['order']`
* $posts_per_page (int) – `$args['count']`
* $no_found_rows (boolean) – `true`

**$args:**
* $author (boolean|string) – The author provided by the shortcode. Default `false`.
* $count (int) – The number of posts provided by the shortcode. Default `1`.
* $orderby (string) – Optional. Default `'date'`.
* $order (string) – Optional. Default `'desc'`.
* $post_ids (\[string]) – Array of post IDs. Default empty.
* $taxonomies (\[array]) – Array of taxonomy arrays (names). Default empty.
* $relation (string) – Relationship between taxonomies. Default `'AND'`.
* $classes (\[string]) – Array of additional CSS classes. Default empty.

---

### `apply_filters( 'fictioneer_filter_shortcode_latest_updates_query_args', $query_args, $args )`
Filters the query arguments in the `fictioneer_latest_updates` shortcode. The optional taxonomy arrays can include categories, tags, fandoms, genres, and characters.

**$query_args:**
* $post_type (string) – `'fcn_story'`
* $post_status (string) – `'publish'`
* $author_name (string|null) – `$args['author']`
* $post__in (array) – `$args['post_ids']`
* $meta_key (string) – `'fictioneer_chapters_added'`
* $orderby (string) – `'meta_value'`
* $order (string) – `$args['order']`
* $posts_per_page (int) – `$args['count'] + 4`
* $no_found_rows (boolean) – `true`

**$args:**
* $simple (boolean) – Whether to render the simple variants. Default `false`.
* $author (boolean|string) – The author provided by the shortcode. Default `false`.
* $count (int) – The number of posts provided by the shortcode. Default `1`.
* $order (string) – Optional. Default `'desc'`.
* $post_ids (\[string]) – Array of post IDs. Default empty.
* $taxonomies (\[array]) – Array of taxonomy arrays (names). Default empty.
* $relation (string) – Relationship between taxonomies. Default `'AND'`.
* $classes (\[string]) – Array of additional CSS classes. Default empty.

---

### `apply_filters( 'fictioneer_filter_shortcode_showcase_query_args', $query_args, $args )`
Filters the query arguments in the `fictioneer_showcase` shortcode. The optional taxonomy arrays can include categories, tags, fandoms, genres, and characters.

**$query_args:**
* $post_type (string) – `$args['type']`
* $post_status (string) – `'publish'`
* $author_name (string|null) – `$args['author']`
* $post__in (array) – `$args['post_ids']`
* $orderby (string) – `$args['orderby']`
* $order (string) – `$args['order']`
* $posts_per_page (int) – `$args['count']`
* $update_post_term_cache (boolean) – `false`
* $no_found_rows (boolean) – `true`

**$args:**
* $type (string) – Either `'fcn_collection'`, `'fcn_story'`, `'fcn_chapter'`, or `'fcn_recommendation'`.
* $author (boolean|string) – The author provided by the shortcode. Default `false`.
* $count (int) – The number of posts provided by the shortcode. Default `1`.
* $orderby (string) – Optional. Default `'date'`.
* $order (string) – Optional. Default `'desc'`.
* $post_ids (\[string]) – Array of post IDs. Default empty.
* $taxonomies (\[array]) – Array of taxonomy arrays (names). Default empty.
* $relation (string) – Relationship between taxonomies. Default `'AND'`.
* $no_cap (boolean) – Whether to hide captions. Default `false`.
* $classes (\[string]) – Array of additional CSS classes. Default empty.

---

### `apply_filters( 'fictioneer_filter_stories_card_args', $card_args, $args )`
Filters the arguments passed to the `partials/_card-story` template part in the `fictioneer_stories_list( $args )` function, normally added via the `fictioneer_stories_after_content` hook.

**$card_args:**
* $cache (boolean) – `fictioneer_caching_active()`

**$args:**
* $current_page (int) – Current page number of pagination or `1`.
* $post_id (int) – Current post ID.
* $stories (WP_Query) – Paginated query of all published stories.
* $queried_type (string) – `'fcn_story'`

---

### `apply_filters( 'fictioneer_filter_stories_query_args', $query_args, $post_id )`
Filters the arguments to query the stories in the `stories.php` template.

**$query_args:**
* $post_type (string) – `'fcn_story'`
* $post_status (string) – `'publish'`
* $meta_key (string) – `'fictioneer_story_sticky'`
* $orderby (string) – `'meta_value modified'`
* $order (string) – `'DESC'`
* $paged (int) – Current page if paginated or `1`.
* $posts_per_page (int) – `get_option( 'posts_per_page' )`

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
* $current_page (int) – Current page number of pagination or `1`.
* $post_id (int) – Current post ID.
* $stories (WP_Query) – Paginated query of all published stories.

---

### `apply_filters( 'fictioneer_filter_story_buttons', $output, $args )`
Filters the intermediate output array of the `fictioneer_get_story_buttons( $args )` function before it is imploded and returned. Used inside `fictioneer_story_actions( $args )` and rendered via the `fictioneer_story_after_content` hook.

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

### `apply_filters( 'fictioneer_filter_translations', $strings )`
Filters the source array of selected translation strings used in the theme, see `fcntr( $key, $escape )` function in `includes/functions/_utility.php`. You cannot translate the whole theme with this, but give it a personal touch.

**Parameters:**
* $strings (array) – Associative array of translation keys and values.

---

### `apply_filters( 'fictioneer_filter_user_menu_items', $items )`
Filters the intermediate output array of the `fictioneer_user_menu_items()` function before it is imploded and returned. Contains links to the user’s account, reading lists, and bookmarks as well as the site’s Discord, site settings modal toggle, and logout link. Used in the `partials/_icon-menu.php` partial if the user is logged in.

**$items:**
* $account (string|null) – Optional. List item with link to the user’s account.
* $site_settings (string) – List item with site settings modal toggle.
* $bookshelf (string|null) – Optional. List item with link to the user’s lists.
* $bookmarks (string|null) – Optional. List item with link to the user’s bookmarks.
* $discord (string|null) – Optional. List item with link to the site’s Discord server.
* $logout (string) – List item with link to log out of the site.
