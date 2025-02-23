# Actions
The following [action hooks](https://developer.wordpress.org/reference/functions/do_action/) can be used to customize templates without the need to duplicate template files, greatly reducing the risk of them becoming outdated due to updates in the future. Actions can be easily added or overwritten in child themes or plugins. This list is probably incomplete. See `includes/functions/hooks/`.

**Note:** Some actions are added conditionally in the `wp` action hook (priority 10), depending on the current page template. These conditional checks are not possible earlier. If you want to remove these actions, you need to do it in the `wp` action hook with priority 11 or later. This affects most actions for stories, chapters, collections, recommendations, posts, and the user profile.

### Example: Add Discord invite link to chapter top actions
This is an example of how to add a Discord invite link to the chapter top actions via the the `fictioneer_chapter_actions_top_center` hook. The link will feature a [Font Awesome Discord icon](https://fontawesome.com/icons/discord?f=brands) and be located between the formatting modal toggle (priority: 10) and fullscreen buttons (priority: 20). Note that no arguments of the hook are used because we do not need any of them here.

```php
// Add this to your child theme’s functions.php
function child_theme_discord_invite_link() {
  // Start HTML ---> ?>
  <a href="http://www.your-discord-invite-link.com" target="_blank" rel="noopener" class="button _secondary">
    <i class="fa-brands fa-discord"></i>
  </a>
  <?php // <--- End HTML
}
add_action( 'fictioneer_chapter_actions_top_center', 'child_theme_discord_invite_link', 15 );
```

---

### `do_action( 'fictioneer_account_content', $args )`
Fires within `<article>` content section in the `user-profile.php` template. Normal page content is not rendered in the template, only what is hooked into this action. This is the frontend user profile, after all.

**Note:** Some actions are added conditionally in the `wp` action hook (priority 10).

**$args:**
* $user (WP_User) – The current user object.
* $is_admin (boolean) – True if the user is an administrator.
* $is_author (boolean) – True if the user is an author (by capabilities).
* $is_moderator (boolean) – True if the user is a moderator (by capabilities).
* $is_editor (boolean) – True if the user is an editor.

**Hooked Actions:**
* `fictioneer_account_moderation_message( $args )` – Moderation message section. Priority 5.
* `fictioneer_account_profile( $args )` – Account profile section. Priority 10.
* `fictioneer_account_oauth( $args )` – OAuth 2.0 account bindings section. Priority 20.
* `fictioneer_account_data( $args )` – Account data section. Priority 30.
* `fictioneer_account_discussions( $args )` – Account discussions section. Priority 40.
* `fictioneer_account_danger_zone( $args )` – Account termination section. Priority 100.

---

### `do_action( 'fictioneer_account_data_nodes', $user, $args )`
Fires after the last list card in the `partials/account/_data.php` partial, added via the `'fictioneer_account_content'` action. Normally contains data nodes for comments, comment subscriptions, Follows, Reminders, Checkmarks, and bookmarks.

**Parameters:**
* $user (WP_User) – The profile user object.

**$args:**
* $is_admin (boolean) – True if the profile user is an administrator.
* $is_author (boolean) – True if the profile user is an author (by capabilities).
* $is_moderator (boolean) – True if the profile user is a moderator (by capabilities).
* $is_editor (boolean) – True if the profile user is an editor.
* $follows (array) – Collection of current follows from `fictioneer_load_follows( $user )`.
* $reminders (array) – Collection of current follows from `fictioneer_load_reminders( $user )`.
* $checkmarks (array) – Collection of current follows from `fictioneer_load_checkmarks( $user )`.
* $comments_count (int) – Total count of the profile user’s comments.
* $timezone (string) – Server’s timezone string.

---

### `do_action( 'fictioneer_admin_settings_connections' )`
Fires after the last card in the `_settings_page_connections.php` partial. You can use this hook to add additional options to the Connections tab in the Fictioneer settings menu, belonging to the `fictioneer-settings-connections-group` group.

---

### `do_action( 'fictioneer_admin_settings_general' )`
Fires before the last card (Deactivation) in the `_settings_page_general.php` partial. You can use this hook to add additional options to the General tab in the Fictioneer settings menu, belonging to the `fictioneer-settings-general-group` group.

---

### `do_action( 'fictioneer_admin_settings_phrases' )`
Fires after the last card in the `_settings_page_phrases.php` partial. You can use this hook to add additional options to the Phrases tab in the Fictioneer settings menu, belonging to the `fictioneer-settings-phrases-group` group.

---

### `do_action( 'fictioneer_admin_settings_tools' )`
Fires after the last card in the `_settings_page_tools.php` partial. You can use this hook to add additional tools to the Tools tab in the Fictioneer settings menu. There is no default settings form group here.

---

### `do_action( 'fictioneer_admin_user_sections', $profile_user )`
Fires within the Fictioneer user profile section in the WordPress `wp-admin/profile.php` template, which is added to both the `'show_user_profile'` (Priority 20) and `'edit_user_profile'` (Priority 20) actions. The default hooked actions are restricted to administrators, moderators, and the profile user.

**Parameters:**
* $profile_user (WP_User) – The owner of the currently edited profile.

**Hooked Actions:**
* `fictioneer_admin_profile_fields_fingerprint( $profile_user )` – User fingerprint field. Priority 5.
* `fictioneer_admin_profile_fields_flags( $profile_user )` – User flags. Priority 6.
* `fictioneer_admin_profile_fields_oauth( $profile_user )` – User OAuth connections. Priority 7.
* `fictioneer_admin_profile_fields_skins( $profile_user )` – Local custom CSS skins. Priority 7.
* `fictioneer_admin_profile_fields_data_nodes( $profile_user )` – User data nodes. Priority 8.
* `fictioneer_admin_profile_post_unlocks( $profile_user )` - Unlock password-protected posts. Priority 9.
* `fictioneer_admin_profile_patreon( $profile_user )` – User Patreon membership data (if any). Priority 10.
* `fictioneer_admin_profile_moderation( $profile_user )` – Moderation flags and message. Priority 10.
* `fictioneer_admin_profile_author( $profile_user )` – Author page select, support message, and support links. Priority 20.
* `fictioneer_admin_profile_oauth( $profile_user )` – OAuth 2.0 account binding IDs. Priority 30.
* `fictioneer_admin_profile_badge( $profile_user )` – Override badge. Priority 40.
* `fictioneer_admin_profile_external_avatar( $profile_user )` – External avatar URL. Priority 50.

---

### `do_action( 'fictioneer_after_main', $args )`
Fires between the site’s `<main>` and `<footer>` blocks. This is the empty space before the breadcrumbs, footer menu, and copyright notice. Still within the `#site` container scope. To escape that, hook into `'fictioneer_footer'` or `'wp_footer'` instead.

**$args:**
* $post_type (string|null) – Current post type. Unsafe.
* $post_id (int|null) – Current post ID. Unsafe.
* $breadcrumbs (array) – Array of breadcrumb tuples with label (0) and link (1).

**Hooked Actions:**
* `fictioneer_mu_registration_end( $args )` – End of the wrapper HTML for wp-signup/wp-activate. Priority 999.

---

### `do_action( 'fictioneer_after_oauth_user', $user, $args )`
Fires after a user has been successfully created or logged-in via the OAuth 2.0 protocol.

**Parameters:**
* $user (WP_User) – The user object.

**$args:**
* 'channel' (string) – Either `discord`, `patreon`, `twitch`, or `google`.
* 'uid' (string) – External unique user ID from the linked account. Unsanitized.
* 'username' (string) – The external username. Unsanitized.
* 'nickname' (string) – The external nickname (or same as username). Unsanitized.
* 'email' (string) – The external email address. Unsanitized.
* 'avatar_url' (string) – The external avatar URL. Unsanitized.
* 'patreon_tiers' (array) – Associative array (Tier ID => Array) with the relevant Patreon tiers or an empty array. Unsanitized.
  * 'tier' (string) – Tier display title.
  * 'title' (string) – Tier display title.
  * 'description' (string) – Tier display description or empty.
  * 'published' (boolean) – Whether the tier is currently published.
  * 'amount_cents' (int) – Monetary amount associated with this tier (in U.S. cents).
  * 'timestamp' (int) – Unix timestamp (GMT) of the authentication in seconds.
  * 'id' (int) – Tier ID (also used as array key).
* 'patreon_membership' (array) – Array with the Patreon membership data or an empty array. Unsanitized.
  * 'lifetime_support_cents' (int) – The total amount that the member has ever paid to the campaign in the campaign’s currency. `0` if never paid.
  * 'last_charge_date' (string|null) – Datetime (UTC ISO) of last attempted charge. `null` if never charged.
  * 'last_charge_status' (string|null) – The result of the last attempted charge. The only successful status is `'Paid'`. `null` if never charged. One of `'Paid'`, `'Declined'`, `'Deleted'`, `'Pending'`, `'Refunded'`, `'Fraud'`, `'Refunded by Patreon'`, `'Other'`, `'Partially Refunded'`, `'Free Trial'`.
  * 'next_charge_date' (string|null) – Datetime (UTC ISO) of next charge. `null` if annual pledge downgrade.
  * 'patron_status' (string|null) – One of `'active_patron'`, `'declined_patron'`, `'former_patron'`. A `null` value indicates the member has never pledged.
* 'new' (boolean) – Whether this is a newly created user.
* 'merged' (boolean) – Whether the account has been newly linked to an existing user.

---

### `do_action( 'fictioneer_after_update', $new_version, $previous_version )`
Fires after the theme has been updated, once per version change.

**Parameters:**
* $new_version (string) – Tag of the newly installed version (e.g. v5.19.1).
* $previous_version (string) – Tag of the previously installed version (e.g. v5.19.0).

**Hooked Actions:**
* `fictioneer_purge_caches_after_update()` – Purges selected caches and Transients. Priority 10.

---

### `do_action( 'fictioneer_archive_loop_after', $args )`
Archive template hook. Fires right after the result loop section in any archive template.

**$args:**
* 'current_page' (int) – Current page number of pagination or 1.
* 'order' (string) – Current order query argument. Default 'desc'.
* 'orderby' (string) – Current orderby query argument. Default 'date'.
* 'ago' (int|string) – Current value for the date query. Default 0.
* 'taxonomy' (string) – Current taxonomy.

---

### `do_action( 'fictioneer_archive_loop_before', $args )`
Archive template hook. Fires right before the result loop section in any archive template.

**$args:**
* 'current_page' (int) – Current page number of pagination or 1.
* 'order' (string) – Current order query argument. Default 'desc'.
* 'orderby' (string) – Current orderby query argument. Default 'date'.
* 'ago' (int|string) – Current value for the date query. Default 0.
* 'taxonomy' (string) – Current taxonomy.

**Hooked Actions:**
* `fictioneer_sort_order_filter_interface( $args )` – Interface to sort, order, and filter. Priority 10.

---

### `do_action( 'fictioneer_before_comments' )`
Fires right before the comment section outside the `<article>` but still within the `<main>` block. Note that the wrapping block does not provide padding, which means you may use the full container width. If needed, add the `padding-left` and/or `padding-right` utility CSS classes for responsive horizontal padding.

---

### `do_action( 'fictioneer_body', $args )`
Fires after `wp_body_open()` in the `<body>` right before the notifications container element, outside the layout. Normally includes the mobile menu.

**$args:**
* 'post_id' (int|null) – Current post ID. Unsafe.
* 'post_type' (string|null) – Current post type. Unsafe.
* 'story_id' (int|null) – Current story ID (if chapter). Unsafe.
* 'header_image_url' (string|boolean) – URL of the filtered header image or false.
* 'header_image_source' (string) – Source of the header image. Either `default`, `post`, or `story`.
* 'header_args' (array) – Arguments passed to the header.php partial.

**Hooked Actions:**
* `fictioneer_output_mobile_menu()` – HTML for the mobile menu. Priority 20.

---

### `do_action( 'fictioneer_cache_purge_all' )`
Fires *before* all caches from known plugins have been purged in the `fictioneer_purge_all_caches()` function, normally triggered by a site-wide update. You can use this hook to purge additional caches.

---

### `do_action( 'fictioneer_cache_purge_post', $post_id )`
Fires *before* a post cache from known plugins has been purged in the `fictioneer_purge_post_cache( $post_id )` function, normally triggered by a specific post or page update. You can use this hook to purge additional caches.

**Parameter:**
* $post_id (int) – The ID of the post/page to purge the cache for.

---

### `do_action( 'fictioneer_contact_form_validation', $strings )`
Fires after the shortcode [contact form](DOCUMENTATION.md#contact-form) has been submitted, validated, and sanitized. Use this hook to apply additional validations and sanitation, perhaps with a plugin. Terminate the script if something bad is found.

**Parameter:**
* $strings (array) – Fields submitted in the form.

---

### `do_action( 'fictioneer_chapter_actions_top_center', $args, $location )`
Fires in the second column of top action section in the `single-fcn_chapter.php` template, the first child in the chapter `<article>` container. Normally includes the formatting modal toggle, open/close fullscreen and bookmark jump buttons.

**Note:** Some actions are added conditionally in the `wp` action hook (priority 10).

**$args:**
* 'author' (WP_User) – Author of the post.
* 'story_post' (WP_Post|null) – Post object of the story. Unsafe.
* 'story_data' (array|null) – Collection of story data. Unsafe.
* 'chapter_id' (int) – The chapter ID.
* 'chapter_title' (string) – Safe chapter title.
* 'chapter_password' (string) – Chapter password or empty string.
* 'password_required' (boolean|null) – Whether the post is unlocked or not. Unsafe.
* 'chapter_ids' (array) – IDs of visible chapters in the same story or empty array.
* 'current_index' (int) – Current index in the chapters_id array.
* 'prev_index' (int|boolean) – Index of previous chapter or false if outside bounds.
* 'next_index' (int|boolean) – Index of next chapter or false if outside bounds.

**Parameters:**
* $location (string) – Location on the chapter page, here `'top'`.

**Hooked Actions:**
* `fictioneer_chapter_formatting_button()` – Toggle to open the chapter formatting modal. Priority 10.
* `fictioneer_chapter_fullscreen_buttons()` – Buttons to open/close the fullscreen view. Priority 20.
* `fictioneer_chapter_bookmark_jump_button()` – Button to scroll to chapter bookmark (if set). Priority 30.

---

### `do_action( 'fictioneer_chapter_actions_top_left', $args, $location )`
Fires in the first column of the top action section in the `single-fcn_chapter.php` template, the first child in the chapter `<article>` container. Normally includes the font resize buttons.

**Note:** Some actions are added conditionally in the `wp` action hook (priority 10).

**$args:**
* 'author' (WP_User) – Author of the post.
* 'story_post' (WP_Post|null) – Post object of the story. Unsafe.
* 'story_data' (array|null) – Collection of story data. Unsafe.
* 'chapter_id' (int) – The chapter ID.
* 'chapter_title' (string) – Safe chapter title.
* 'chapter_password' (string) – Chapter password or empty string.
* 'password_required' (boolean|null) – Whether the post is unlocked or not. Unsafe.
* 'chapter_ids' (array) – IDs of visible chapters in the same story or empty array.
* 'current_index' (int) – Current index in the chapters_id array.
* 'prev_index' (int|boolean) – Index of previous chapter or false if outside bounds.
* 'next_index' (int|boolean) – Index of next chapter or false if outside bounds.

**Parameters:**
* $location (string) – Location on the chapter page, here `'top'`.

**Hooked Actions:**
* `fictioneer_chapter_resize_buttons()` – Buttons to decrease, reset, and increase the chapter font size. Priority 10.

---

### `do_action( 'fictioneer_chapter_actions_top_right', $args, $location )`
Fires in the third column of the top action section in the `single-fcn_chapter.php` template, the first child in the chapter `<article>` container. Normally includes the chapter navigation links and bottom anchor button.

**Note:** Some actions are added conditionally in the `wp` action hook (priority 10).

**$args:**
* 'author' (WP_User) – Author of the post.
* 'story_post' (WP_Post|null) – Post object of the story. Unsafe.
* 'story_data' (array|null) – Collection of story data. Unsafe.
* 'chapter_id' (int) – The chapter ID.
* 'chapter_title' (string) – Safe chapter title.
* 'chapter_password' (string) – Chapter password or empty string.
* 'password_required' (boolean|null) – Whether the post is unlocked or not. Unsafe.
* 'chapter_ids' (array) – IDs of visible chapters in the same story or empty array.
* 'current_index' (int) – Current index in the chapters_id array.
* 'prev_index' (int|boolean) – Index of previous chapter or false if outside bounds.
* 'next_index' (int|boolean) – Index of next chapter or false if outside bounds.

**Parameters:**
* $location (string) – Location on the chapter page, here `'top'`.

**Hooked Actions:**
* `fictioneer_chapter_nav_buttons( $args, 'top' )` – Links to the previous/next chapter, anchor to end of chapter. Priority 10.

---

### `do_action( 'fictioneer_chapter_actions_bottom_center', $args, $location )`
Fires in the second column of the bottom action section in the `single-fcn_chapter.php` template, the last child in the chapter `<article>` container. Normally includes the subscribe, story link, and bookmark jump buttons.

**Note:** Some actions are added conditionally in the `wp` action hook (priority 10).

**$args:**
* 'author' (WP_User) – Author of the post.
* 'story_post' (WP_Post|null) – Post object of the story. Unsafe.
* 'story_data' (array|null) – Collection of story data. Unsafe.
* 'chapter_id' (int) – The chapter ID.
* 'chapter_title' (string) – Safe chapter title.
* 'chapter_password' (string) – Chapter password or empty string.
* 'password_required' (boolean|null) – Whether the post is unlocked or not. Unsafe.
* 'chapter_ids' (array) – IDs of visible chapters in the same story or empty array.
* 'current_index' (int) – Current index in the chapters_id array.
* 'prev_index' (int|boolean) – Index of previous chapter or false if outside bounds.
* 'next_index' (int|boolean) – Index of next chapter or false if outside bounds.

**Parameters:**
* $location (string) – Location on the chapter page, here `'bottom'`.

**Hooked Actions:**
* `fictioneer_chapter_subscribe_button()` – Subscription popup menu. Priority 10.
* `fictioneer_chapter_index_modal_toggle()` – Toggle for the chapter index modal. Priority 20.
* `fictioneer_chapter_bookmark_jump_button()` – Button to scroll to chapter bookmark (if set). Priority 30.

---

### `do_action( 'fictioneer_chapter_actions_bottom_left', $args, $location )`
Fires in the first column of the bottom action section in the `single-fcn_chapter.php` template, the last child in the chapter `<article>` container. Normally includes the social media sharing and feed buttons.

**Note:** Some actions are added conditionally in the `wp` action hook (priority 10).

**$args:**
* 'author' (WP_User) – Author of the post.
* 'story_post' (WP_Post|null) – Post object of the story. Unsafe.
* 'story_data' (array|null) – Collection of story data. Unsafe.
* 'chapter_id' (int) – The chapter ID.
* 'chapter_title' (string) – Safe chapter title.
* 'chapter_password' (string) – Chapter password or empty string.
* 'password_required' (boolean|null) – Whether the post is unlocked or not. Unsafe.
* 'chapter_ids' (array) – IDs of visible chapters in the same story or empty array.
* 'current_index' (int) – Current index in the chapters_id array.
* 'prev_index' (int|boolean) – Index of previous chapter or false if outside bounds.
* 'next_index' (int|boolean) – Index of next chapter or false if outside bounds.

**Parameters:**
* $location (string) – Location on the chapter page, here `'bottom'`.

**Hooked Actions:**
* `fictioneer_chapter_media_buttons()` – Buttons for social media sharing and feeds. Priority 10.

---

### `do_action( 'fictioneer_chapter_actions_bottom_right', $args, $location )`
Fires in the third column of the bottom action section in the `single-fcn_chapter.php` template, the last child in the chapter `<article>` container. Normally includes the chapter navigation links and top anchor button.

**Note:** Some actions are added conditionally in the `wp` action hook (priority 10).

**$args:**
* 'author' (WP_User) – Author of the post.
* 'story_post' (WP_Post|null) – Post object of the story. Unsafe.
* 'story_data' (array|null) – Collection of story data. Unsafe.
* 'chapter_id' (int) – The chapter ID.
* 'chapter_title' (string) – Safe chapter title.
* 'chapter_password' (string) – Chapter password or empty string.
* 'password_required' (boolean|null) – Whether the post is unlocked or not. Unsafe.
* 'chapter_ids' (array) – IDs of visible chapters in the same story or empty array.
* 'current_index' (int) – Current index in the chapters_id array.
* 'prev_index' (int|boolean) – Index of previous chapter or false if outside bounds.
* 'next_index' (int|boolean) – Index of next chapter or false if outside bounds.

**Parameters:**
* $location (string) – Location on the chapter page, here `'bottom'`.

**Hooked Actions:**
* `fictioneer_chapter_nav_buttons( $args, 'bottom' )` – Links to the previous/next chapter, anchor to start of chapter. Priority 10.

---

### `do_action( 'fictioneer_chapter_after_header', $args )`
Fires right after the article header (with story, title, and authors) in the `single-fcn_chapter.php` template, inside the `<article>` container and just before the content section (or password form if the post is protected). The chapter header is unaffected by most chapter formatting options.

**$args:**
* 'author' (WP_User) – Author of the post.
* 'story_post' (WP_Post|null) – Post object of the story. Unsafe.
* 'story_data' (array|null) – Collection of story data. Unsafe.
* 'chapter_id' (int) – The chapter ID.
* 'chapter_title' (string) – Safe chapter title.
* 'chapter_password' (string) – Chapter password or empty string.
* 'password_required' (boolean|null) – Whether the post is unlocked or not. Unsafe.
* 'chapter_ids' (array) – IDs of visible chapters in the same story or empty array.
* 'current_index' (int) – Current index in the chapters_id array.
* 'prev_index' (int|boolean) – Index of previous chapter or false if outside bounds.
* 'next_index' (int|boolean) – Index of next chapter or false if outside bounds.

---

### `do_action( 'fictioneer_chapter_after_content', $args )`
Fires right after the content section in the `single-fcn_chapter.php` template, inside the `<article>` container and just before the bottom action section. Normally includes the afterword and support links.

**Note:** Some actions are added conditionally in the `wp` action hook (priority 10).

**$args:**
* 'author' (WP_User) – Author of the post.
* 'story_post' (WP_Post|null) – Post object of the story. Unsafe.
* 'story_data' (array|null) – Collection of story data. Unsafe.
* 'chapter_id' (int) – The chapter ID.
* 'chapter_title' (string) – Safe chapter title.
* 'chapter_password' (string) – Chapter password or empty string.
* 'password_required' (boolean|null) – Whether the post is unlocked or not. Unsafe.
* 'chapter_ids' (array) – IDs of visible chapters in the same story or empty array.
* 'current_index' (int) – Current index in the chapters_id array.
* 'prev_index' (int|boolean) – Index of previous chapter or false if outside bounds.
* 'next_index' (int|boolean) – Index of next chapter or false if outside bounds.

**Hooked Actions:**
* `fictioneer_chapter_afterword( $args )` – Chapter afterword. Priority 10.
* `fictioneer_chapter_support_links( $args )` – Support links set for the chapter/story/author. Priority 20.
* `fictioneer_chapter_footer( $args )` – Chapter footer. Priority 99.
* `fictioneer_chapter_micro_menu( $args )` – HTML for the micro menu. Priority 99.
* `fictioneer_output_bookmark_data( $args )` – Data JSON for the bookmark. Priority 99.

**Example:**
```php
function child_display_pw_expiration_date_in_chapter( $args ) {
  $password_expiration_date_utc = get_post_meta( $args['chapter_id'], 'fictioneer_post_password_expiration_date', true );

  if ( empty( $password_expiration_date_utc ) || empty( $args['chapter_password'] ) ) {
    return;
  }

  $local_time = get_date_from_gmt( $password_expiration_date_utc );
  $formatted_datetime = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $local_time ) );

  echo $formatted_datetime;
}
add_action( 'fictioneer_chapter_after_content', 'child_display_pw_expiration_date_in_chapter', 5 );
```

---

### `do_action( 'fictioneer_chapter_after_main', $args )`
Fires right after the `<main>` container is closed in the `single-fcn_chapter.php` template. Normally includes the micro menu, paragraph tools, and suggestion tools. Not executed if the post is locked behind a password.

**Note:** Some actions are added conditionally in the `wp` action hook (priority 10).

**$args:**
* 'author' (WP_User) – Author of the post.
* 'story_post' (WP_Post|null) – Post object of the story. Unsafe.
* 'story_data' (array|null) – Collection of story data. Unsafe.
* 'chapter_id' (int) – The chapter ID.
* 'chapter_title' (string) – Safe chapter title.
* 'chapter_password' (string) – Chapter password or empty string.
* 'password_required' (boolean|null) – Whether the post is unlocked or not. Unsafe.
* 'chapter_ids' (array) – IDs of visible chapters in the same story or empty array.
* 'current_index' (int) – Current index in the chapters_id array.
* 'prev_index' (int|boolean) – Index of previous chapter or false if outside bounds.
* 'next_index' (int|boolean) – Index of next chapter or false if outside bounds.

**Hooked Actions:**
* `fictioneer_chapter_paragraph_tools()` – Add the HTML for the chapter paragraph tools. Priority 10.
* `fictioneer_chapter_suggestion_tools()` – Add the HTML for the chapter suggestion tools. Priority 10.

---

### `do_action( 'fictioneer_chapter_before_header', $args )`
Fires between the top actions sections and chapter header (title and authors) in the `single-fcn_chapter.php` template. Normally includes the foreword and chapter warnings.

**Note:** Some actions are added conditionally in the `wp` action hook (priority 10).

**$args:**
* 'author' (WP_User) – Author of the post.
* 'story_post' (WP_Post|null) – Post object of the story. Unsafe.
* 'story_data' (array|null) – Collection of story data. Unsafe.
* 'chapter_id' (int) – The chapter ID.
* 'chapter_title' (string) – Safe chapter title.
* 'chapter_password' (string) – Chapter password or empty string.
* 'password_required' (boolean|null) – Whether the post is unlocked or not. Unsafe.
* 'chapter_ids' (array) – IDs of visible chapters in the same story or empty array.
* 'current_index' (int) – Current index in the chapters_id array.
* 'prev_index' (int|boolean) – Index of previous chapter or false if outside bounds.
* 'next_index' (int|boolean) – Index of next chapter or false if outside bounds.

**Hooked Actions:**
* `fictioneer_chapter_top_actions( $args )` – Chapter top action row. Priority 1.
* `fictioneer_chapter_global_note( $args )` – Story-wide note if provided. Priority 5.
* `fictioneer_chapter_foreword( $args )` – Chapter foreword if provided. Priority 10.
* `fictioneer_chapter_warnings( $args )` – Chapter warnings if provided. Priority 20.

---

### `do_action( 'fictioneer_chapter_before_comments', $args )`
Fires just before the comments section in the `single-fcn_chapter.php` template and before the `fictioneer_before_comments` hook. The only difference is the provided argument array with chapter data.

**$args:**
* 'author' (WP_User) – Author of the post.
* 'story_post' (WP_Post|null) – Post object of the story. Unsafe.
* 'story_data' (array|null) – Collection of story data. Unsafe.
* 'chapter_id' (int) – The chapter ID.
* 'chapter_title' (string) – Safe chapter title.
* 'chapter_password' (string) – Chapter password or empty string.
* 'password_required' (boolean|null) – Whether the post is unlocked or not. Unsafe.
* 'chapter_ids' (array) – IDs of visible chapters in the same story or empty array.
* 'current_index' (int) – Current index in the chapters_id array.
* 'prev_index' (int|boolean) – Index of previous chapter or false if outside bounds.
* 'next_index' (int|boolean) – Index of next chapter or false if outside bounds.

---

### `do_action( 'fictioneer_chapters_after_content', $args )`
List page template hook. Fires right after the content section in the `chapters.php` template. Includes the paginated card list of all visible chapters on the site.

**Note:** Some actions are added conditionally in the `wp` action hook (priority 10).

**$args:**
* 'current_page' (int) – Current page number of pagination or 1.
* 'post_id' (int) – Current post ID.
* 'chapters' (WP_Query) – Paginated query of all published chapters.
* 'queried_type' (string) – `fcn_chapter`
* 'query_args' (array) – The final query arguments used.
* 'order' (string) – Current order query argument. Default 'desc'.
* 'orderby' (string) – Current orderby query argument. Default 'modified'.
* 'ago' (int|string) – Current value for the date query. Default 0.

**Hooked Actions:**
* `fictioneer_sort_order_filter_interface( $args )` – Interface to sort, order, and filter. Priority 20.
* `fictioneer_chapters_list( $args )` – Paginated card list of all visible chapters. Priority 30.
* `fictioneer_chapter_micro_menu( $args )` – Add the HTML for the chapter micro menu. Priority 99.

---

### `do_action( 'fictioneer_chapters_end_of_results', $args )`
List page template hook. Fires right after the last list item in the `'fictioneer_chapters_list'` action.

**$args:**
* 'current_page' (int) – Current page number of pagination or 1.
* 'post_id' (int) – Current post ID.
* 'chapters' (WP_Query) – Paginated query of all published chapters.
* 'queried_type' (string) – `fcn_chapter`

---

### `do_action( 'fictioneer_chapters_no_results', $args )`
List page template hook. Fires right at the top of an empty result list in the `'fictioneer_chapters_list'` action, before the no-result message is rendered.

**$args:**
* 'current_page' (int) – Current page number of pagination or 1.
* 'post_id' (int) – Current post ID.
* 'chapters' (WP_Query) – Paginated query of all published chapters.
* 'queried_type' (string) – `fcn_chapter`

---

### `do_action( 'fictioneer_collection_after_content', $args )`
Fires right after the collection content section in the `single-fcn_collection.php` template, inside the `<article>` container and just before the footer is rendered. Normally includes the tags, content warnings, statistics, and items featured in the collection.

**Note:** Some actions are added conditionally in the `wp` action hook (priority 10).

**$args:**
* 'collection' (WP_Post) – Post object of the collection.
* 'collection_id' (int) – Post ID of the collection.
* 'title' (string) – Safe title of the collection.
* 'current_page' (int) – Number of the current page or 1.
* 'max_pages' (int) – Total number of pages or 1.
* 'featured_list' (array) – IDs of featured items in the collection.
* 'featured_query' (WP_Query) – Paginated query of featured items.

**Hooked Actions:**
* `fictioneer_collection_tags_and_warnings( $args )` – Tags and content warnings. Priority 10.
* `fictioneer_collection_statistics( $args )` – Collection statistics. Priority 20.
* `fictioneer_collection_featured_list( $args )` – Paginated items featured in the collection. Priority 30.

---

### `do_action( 'fictioneer_collection_after_header', $args )`
Fires right after the article header (title, fandom, genres, and characters) in the `single-fcn_collection.php` template, inside the `<article>` container and just before the content section (or password form if the post is protected).

**$args:**
* 'collection' (WP_Post) – Post object of the collection.
* 'collection_id' (int) – Post ID of the collection.
* 'title' (string) – Safe title of the collection.
* 'current_page' (int) – Number of the current page or 1.
* 'max_pages' (int) – Total number of pages or 1.
* 'featured_list' (array) – IDs of featured items in the collection.
* 'featured_query' (WP_Query) – Paginated query of featured items.

---

### `do_action( 'fictioneer_collections_after_content', $args )`
List page template hook. Fires right after the content section in the `collections.php` template. Includes the paginated card list of all visible collections on the site.

**Note:** Some actions are added conditionally in the `wp` action hook (priority 10).

**$args:**
* 'current_page' (int) – Current page number of pagination or 1.
* 'post_id' (int) – Current post ID.
* 'collections' (WP_Query) – Paginated query of all published collections.
* 'queried_type' (string) – `fcn_collection`
* 'query_args' (array) – The final query arguments used.
* 'order' (string) – Current order query argument. Default 'desc'.
* 'orderby' (string) – Current orderby query argument. Default 'modified'.
* 'ago' (int|string) – Current value for the date query. Default 0.

**Hooked Actions:**
* `fictioneer_sort_order_filter_interface( $args )` – Interface to sort, order, and filter. Priority 20.
* `fictioneer_collections_list( $args )` – Paginated card list of all visible collections. Priority 30.

---

### `do_action( 'fictioneer_collections_end_of_results', $args )`
List page template hook. Fires right after the last list item in the `'fictioneer_collections_list'` action.

**$args:**
* 'current_page' (int) – Current page number of pagination or 1.
* 'post_id' (int) – Current post ID.
* 'collections' (WP_Query) – Paginated query of all published collections.
* 'queried_type' (string) – `fcn_collection`

---

### `do_action( 'fictioneer_collection_footer', $args )`
Fires right after opening the article’s `<footer>` container in the `single-fcn_collection.php` template.

**$args:**
* 'collection' (WP_Post) – Post object of the collection.
* 'collection_id' (int) – Post ID of the collection.
* 'title' (string) – Safe title of the collection.
* 'current_page' (int) – Number of the current page or 1.
* 'max_pages' (int) – Total number of pages or 1.
* 'featured_list' (array) – IDs of featured items in the collection.
* 'featured_query' (WP_Query) – Paginated query of featured items.

---

### `do_action( 'fictioneer_collect_footnote', $args )`
Fires immediately after the HTML output of the tooltip is created to collect tooltip content for footnotes in the `_setup-shortcodes.php` file.

**$args:**
* 'footnote_id' (int) – Unique identifier for the footnote.
* 'content' (string) – Content of the footnote.

**Hooked Actions:**
* `fictioneer_collect_footnote( $footnote_id, $content )` – Collect a footnote to be stored in a global array for later rendering. Priority 10.

---

### `do_action( 'fictioneer_collections_no_results', $args )`
List page template hook. Fires right at the top of an empty result list in the `'fictioneer_collections_list'` action, before the no-result message is rendered.

**$args:**
* 'current_page' (int) – Current page number of pagination or 1.
* 'post_id' (int) – Current post ID.
* 'collections' (WP_Query) – Paginated query of all published collections.
* 'queried_type' (string) – `fcn_collection`

---

### `do_action( 'fictioneer_expired_post_password', $post )`
Fires after a post password has been expired, which happens when a visitor tries to access the post.

**Parameters:**
* $post (WP_Post) – The post that had its password expired.

---

### `do_action( 'fictioneer_footer', $args )`
Fires outside the `#site` container and before the `wp_footer` hook, near the end of the document. Not to be confused with the `fictioneer_site_footer` hook.

**$args:**
* 'post_id' (int|null) – Current post ID. Unsafe.
* 'post_type' (string|null) – Current post type. Unsafe.
* 'breadcrumbs' (array) – Array of breadcrumb tuples with label (0) and link (1).

**Hooked Actions:**
* `fictioneer_output_modals( $args )` – Renders modal HTML based on login status and page type. Priority 10.
* `fictioneer_render_tooltip_dialog_modal_html()` – Renders dialog HTML for the tooltip modal. Priority 10.
* `fictioneer_mu_registration_start( $args )` – Start of the wrapper HTML for wp-signup/wp-activate. Priority 999.

---

### `do_action( 'fictioneer_inner_header', $args )`
Fires right after opening the inner `<header>` container. Normally includes a background image.

**$args:**
* 'post_id' (int|null) – Current post ID. Unsafe.
* 'story_id' (int|null) – Current story ID. Unsafe.
* 'header_image_url' (string|boolean) – URL of the filtered header image or false.
* 'header_args' (array) – Arguments passed to the header.php partial.

**Hooked Actions:**
* `fictioneer_header_inner_background( $args )` – Header background image. Priority 10.

---

### `do_action( 'fictioneer_large_card_body_chapter', $post, $story_data, $args )`
Fires before the content of the card grid is rendered in the `partials/_card-chapter.php` partial. Does not allow you to change the content of the body, but you can add additional HTML and use CSS to modify the look. If you want to change significantly the cards, better overwrite the partial.

**Parameters:**
* $post (WP_Post) – Post object of the chapter.
* $story_data (array|null) – Pre-processed data of the story. Unsafe.
* $args (array) – Optional arguments passed to the card.

---

### `do_action( 'fictioneer_large_card_body_collection', $post, $items, $args )`
Fires before the content of the card grid is rendered in the `partials/_card-collection.php` partial. Does not allow you to change the content of the body, but you can add additional HTML and use CSS to modify the look. If you want to change significantly the cards, better overwrite the partial.

**Parameters:**
* $post (WP_Post) – Post object of the collection.
* $items (array) – Array of featured WP_Post objects.
* $args (array) – Optional arguments passed to the card.

---

### `do_action( 'fictioneer_large_card_body_page', $post, $args )`
Fires before the content of the card grid is rendered in the `partials/_card-page.php` partial. Does not allow you to change the content of the body, but you can add additional HTML and use CSS to modify the look. If you want to change significantly the cards, better overwrite the partial.

**Parameters:**
* $post (WP_Post) – Post object of the page.
* $args (array) – Optional arguments passed to the card.

---

### `do_action( 'fictioneer_large_card_body_post', $post, $args )`
Fires before the content of the card grid is rendered in the `partials/_card-post.php` partial. Does not allow you to change the content of the body, but you can add additional HTML and use CSS to modify the look. If you want to change significantly the cards, better overwrite the partial.

**Parameters:**
* $post (WP_Post) – Post object of the post.
* $args (array) – Optional arguments passed to the card.

---

### `do_action( 'fictioneer_large_card_body_recommendation', $post, $args )`
Fires before the content of the card grid is rendered in the `partials/_card-recommendation.php` partial. Does not allow you to change the content of the body, but you can add additional HTML and use CSS to modify the look. If you want to change significantly the cards, better overwrite the partial.

**Parameters:**
* $post (WP_Post) – Post object of the recommendation.
* $args (array) – Optional arguments passed to the card.

---

### `do_action( 'fictioneer_large_card_body_story', $post, $story_data, $args )`
Fires before the content of the card grid is rendered in the `partials/_card-story.php` partial. Does not allow you to change the content of the body, but you can add additional HTML and use CSS to modify the look. If you want to change significantly the cards, better overwrite the partial.

**Parameters:**
* $post (WP_Post) – Post object of the story.
* $story_data (array) – Pre-processed data of the story.
* $args (array) – Optional arguments passed to the card.

---

### `do_action( 'fictioneer_main', $context )`
Fires after opening the site’s `<main>` container.

**Parameters:**
* $context (string|null) – Context of the action call, typically related to the template. Unsafe.

**Hooked Actions:**
* `fictioneer_main_observer()` – Renders the main observer element. Priority 1.
* `fictioneer_page_background()` – Renders the page background. Priority 10.
* `fictioneer_sidebar() – Renders the sidebar if enabled (depends on location). Priority 10.

---

### `do_action( 'fictioneer_main_end', $context )`
Fires before closing the site’s `<main>` container.

**Parameters:**
* $context (string|null) – Context of the action call, typically related to the template. Unsafe.

**Hooked Actions:**
* `fictioneer_sidebar() – Renders the sidebar if enabled (depends on location). Priority 10.

---

### `do_action( 'fictioneer_main_wrapper' )`
Fires right after opening the site’s `.main__wrapper` container, outside the loop.

---

### `do_action( 'fictioneer_mobile_menu_bottom' )`
Fires right after opening the `.mobile-menu__bottom` container in the `partials/_mobile-menu.php` partial, the last element in the mobile menu. Normally reserved for the quick buttons.

**Hooked Actions:**
* `fictioneer_mobile_quick_buttons()` – Quick buttons for the mobile menu. Priority 10.

---

### `do_action( 'fictioneer_mobile_menu_center' )`
Fires right after the main mobile menu frame in the `partials/_mobile-menu.php` partial, which holds most of the panels. Primarily used to output additional frames.

**Hooked Actions:**
* `fictioneer_mobile_follows_frame()` – Frame for mobile updates (Follows). Priority 10.
* `fictioneer_mobile_bookmarks_frame()` – Frame for mobile bookmarks. Priority 20.

---

### `do_action( 'fictioneer_mobile_menu_main_frame_panels' )`
Fires right after opening the main mobile menu frame in the `partials/_mobile-menu.php` partial. Most panels of the mobile menu are enqueued here.

**Hooked Actions:**
* `fictioneer_mobile_navigation_panel()` – Navigation menu folded out. Priority 10.
* `fictioneer_mobile_lists_panel()` – Frame buttons for bookmarks, chapters, and Follows. Priority 20.
* `fictioneer_mobile_user_menu()` – Mobile variant of the user menu. Priority 30.

---

### `do_action( 'fictioneer_mobile_menu_top' )`
Fires right after opening the `.mobile-menu__top` container in the `partials/_mobile-menu.php` partial. Normally reserved for the mobile version of the icon menu, called with the `'in-mobile-menu'` `$location` parameter.

**Hooked Actions:**
* `fictioneer_mobile_user_icon_menu()` – Mobile variant of the icon menu. Priority 10.

---

### `do_action( 'fictioneer_modals', $args )`
Fires right after the default modals have been included in the `fictioneer_output_modals` action.

**$args:**
* 'post_id' (int|null) – Current post ID. Unsafe.
* 'story_id' (int|null) – Current story ID. Unsafe.
* 'header_image_url' (string|boolean) – URL of the filtered header image or false.
* 'header_args' (array) – Arguments passed to the header.php partial.

---

### `do_action( 'fictioneer_modal_login_option' )`
Fires inside the login modal, in the `login__options` row.

**Hooked Actions:**
* `fictioneer_render_oauth_login_options()` – Renders the OAuth login links. Priority 10.
* `fictioneer_render_wp_login_option()` – Renders the normal WordPress login link. Priority 20.

---

### `do_action( 'fictioneer_navigation_bottom', $args )`
Fires within the `#full-navigation` container in the `_navigation.php` partial, right after the main navigation wrapper is rendered.

**$args:**
* 'post_id' (int|null) – Current post ID. Unsafe.
* 'story_id' (int|null) – Current story ID (if chapter). Unsafe.
* 'header_image_url' (string|boolean) – URL of the filtered header image or false.
* 'header_args' (array) – Arguments passed to the header.php partial.
* 'tag' (string|null) – Override wrapping tag of the navigation row. Unsafe.

---

### `do_action( 'fictioneer_navigation_top', $args )`
Fires within the `#full-navigation` container in the `_navigation.php` partial, right after the navigation background and before the main navigation wrapper is rendered.

**$args:**
* 'post_id' (int|null) – Current post ID. Unsafe.
* 'story_id' (int|null) – Current story ID (if chapter). Unsafe.
* 'header_image_url' (string|boolean) – URL of the filtered header image or false.
* 'header_args' (array) – Arguments passed to the header.php partial.
* 'tag' (string|null) – Override wrapping tag of the navigation row. Unsafe.

---

### `do_action( 'fictioneer_navigation_wrapper_end', $args )`
Fires before closing the `.main-navigation__wrapper` container in the `_navigation.php` partial, right after the navigation items (menu, icons, etc.) have been rendered.

**$args:**
* 'post_id' (int|null) – Current post ID. Unsafe.
* 'story_id' (int|null) – Current story ID (if chapter). Unsafe.
* 'header_image_url' (string|boolean) – URL of the filtered header image or false.
* 'header_args' (array) – Arguments passed to the header.php partial.
* 'tag' (string|null) – Override wrapping tag of the navigation row. Unsafe.

---

### `do_action( 'fictioneer_navigation_wrapper_start', $args )`
Fires after opening the `.main-navigation__wrapper` container in the `_navigation.php` partial, right before the navigation items (menu, icons, etc.) are rendered.

**$args:**
* 'post_id' (int|null) – Current post ID. Unsafe.
* 'story_id' (int|null) – Current story ID (if chapter). Unsafe.
* 'header_image_url' (string|boolean) – URL of the filtered header image or false.
* 'header_args' (array) – Arguments passed to the header.php partial.
* 'tag' (string|null) – Override wrapping tag of the navigation row. Unsafe.

**Hooked Actions:**
* `fictioneer_wide_header_identity( $args )` – HTML for logo, site title, and tagline. Priority 10.

---

### `do_action( 'fictioneer_post_after_content', $post_id, $args )`
Fires right after the article content in the `_post.php` partial and `single-post.php` template. Mind the render context, which can be `'loop'`, `'shortcode_fictioneer_blog'`, or `'single-post'`.

**Note:** Some actions are added conditionally in the `wp` action hook (priority 10).

**Parameters:**
* $post_id (int) – Current post ID.

**$args:**
* 'context' (string) – Render context of the partial.
* 'nested' (boolean|null) – Optional. Whether the post is nested inside another query.

**Hooked Actions:**
* `fictioneer_post_tags( $post_id, $args )` – Tags of the post. Priority 10.
* `fictioneer_post_featured_list( $post_id, $args )` – Items featured in the post. Priority 20.

---

### `do_action( 'fictioneer_post_article_open', $post_id, $args )`
Fires right after the post article is opened in the `_post.php` partial and `single-post.php` template, before anything else is rendered. Mind the render context, which can be `'loop'`, `'shortcode_fictioneer_blog'`, or `'single-post'`.

**Note:** Some actions are added conditionally in the `wp` action hook (priority 10).

**Parameter:**
* $post_id (int) – The ID of the post.

**$args:**
* 'context' (string) – Render context of the partial.
* 'nested' (boolean|null) – Optional. Whether the post is nested inside another query.

**Example:**
```php
function child_add_landscape_post_image( $post_id, $args ) {
  if ( ( $args['context'] ?? 0 ) !== 'loop' ) {
    return;
  }

  fictioneer_render_thumbnail(
    array(
      'post_id' => $post_id,
      'title' => fictioneer_get_safe_title( $post_id ),
      'classes' => 'post-landscape-image',
      'lightbox' => 0,
      'vertical' => 1
    )
  );
}
add_action( 'fictioneer_post_article_open', 'child_add_landscape_post_image', 10, 2 );
```

---

### `do_action( 'fictioneer_post_footer_left', $post_id, $args )`
Fires inside the `.post__footer-left` container within the article footer in the `_post.php` partial and `single-post.php` template. Mind the render context, which can be `'loop'`, `'shortcode_fictioneer_blog'`, or `'single-post'`.

**Note:** Some actions are added conditionally in the `wp` action hook (priority 10).

**Parameters:**
* $post_id (int) – Current post ID.

**$args:**
* 'context' (string) – Render context of the partial.
* 'nested' (boolean|null) – Optional. Whether the post is nested inside another query.

**Hooked Actions:**
* `fictioneer_post_media_buttons( $post_id, $args )` – Buttons for sharing and webfeeds. Priority 10.

---

### `do_action( 'fictioneer_post_footer_right', $post_id, $args )`
Fires inside the `.post__footer-right` container within the article footer in the `_post.php` partial and `single-post.php` template. Mind the render context, which can be `'loop'`, `'shortcode_fictioneer_blog'`, or `'single-post'`.

**Note:** Some actions are added conditionally in the `wp` action hook (priority 10).

**Parameters:**
* $post_id (int) – Current post ID.

**$args:**
* 'context' (string) – Render context of the partial.
* 'nested' (boolean|null) – Optional. Whether the post is nested inside another query.

**Hooked Actions:**
* `fictioneer_post_subscribe_button( $post_id, $args )` – Subscription popup menu. Priority 10.

---

### `do_action( 'fictioneer_recommendation_after_header', $args )`
Fires right after the article header (title, fandom, genres, and characters) in the `single-fcn_recommendation.php` template, inside the `<article>` container and just before the content section.

**Note:** Some actions are added conditionally in the `wp` action hook (priority 10).

**$args:**
* 'recommendation' (WP_Post) – Post object of the recommendation.
* 'recommendation_id' (int) – Post ID of the recommendation.
* 'title' (string) – Safe title of the recommendation.

---

### `do_action( 'fictioneer_recommendation_after_content', $args )`
Fires right after the content section in the `single-fcn_recommendation.php` template, inside the `<article>` container and just before the footer is rendered. Normally includes the tags, external sources to read the recommendation, and support links for the recommendation author.

**Note:** Some actions are added conditionally in the `wp` action hook (priority 10).

**$args:**
* 'recommendation' (WP_Post) – Post object of the recommendation.
* 'recommendation_id' (int) – Post ID of the recommendation.
* 'title' (string) – Safe title of the recommendation.

**Hooked Actions:**
* `fictioneer_recommendation_tags( $args )` – Tags of the recommendation. Priority 10.
* `fictioneer_recommendation_links( $args )` – External links to read the recommendation. Priority 20.
* `fictioneer_recommendation_support_links( $args )` – Support links for the recommendation author. Priority 30.

---

### `do_action( 'fictioneer_recommendation_footer', $args )`
Fires right after opening the article’s `<footer>` container in the `single-fcn_recommendation.php` template.

**Note:** Some actions are added conditionally in the `wp` action hook (priority 10).

**$args:**
* 'recommendation' (WP_Post) – Post object of the recommendation.
* 'recommendation_id' (int) – Post ID of the recommendation.
* 'title' (string) – Safe title of the recommendation.

---

### `do_action( 'fictioneer_recommendations_after_content', $args )`
List page template hook. Fires right after the content section in the `recommendations.php` template. Includes the paginated card list of all visible recommendations on the site.

**Note:** Some actions are added conditionally in the `wp` action hook (priority 10).

**$args:**
* 'current_page' (int) – Current page number of pagination or 1.
* 'post_id' (int) – Current post ID.
* 'recommendations' (WP_Query) – Paginated query of all published recommendations.
* 'queried_type' (string) – `fcn_recommendation`
* 'query_args' (array) – The final query arguments used.
* 'order' (string) – Current order query argument. Default 'desc'.
* 'orderby' (string) – Current orderby query argument. Default 'modified'.
* 'ago' (int|string) – Current value for the date query. Default 0.

**Hooked Actions:**
* `fictioneer_sort_order_filter_interface( $args )` – Interface to sort, order, and filter. Priority 20.
* `fictioneer_recommendations_list( $args )` – Paginated card list of all visible recommendations. Priority 30.

---

### `do_action( 'fictioneer_recommendations_end_of_results', $args )`
List page template hook. Fires right after the last list item in the `'fictioneer_recommendations_list'` action.

**Note:** Some actions are added conditionally in the `wp` action hook (priority 10).

**$args:**
* 'current_page' (int) – Current page number of pagination or 1.
* 'post_id' (int) – Current post ID.
* 'recommendations' (WP_Query) – Paginated query of all published recommendations.
* 'queried_type' (string) – `fcn_recommendation`

---

### `do_action( 'fictioneer_recommendations_no_results', $args )`
List page template hook. Fires right at the top of an empty result list in the `'fictioneer_recommendations_list'` action, before the no-result message is rendered.

**Note:** Some actions are added conditionally in the `wp` action hook (priority 10).

**$args:**
* 'current_page' (int) – Current page number of pagination or 1.
* 'post_id' (int) – Current post ID.
* 'recommendations' (WP_Query) – Paginated query of all published recommendations.
* 'queried_type' (string) – `fcn_recommendation`

---

### `do_action( 'fictioneer_singular_footer' )`
Fires right after opening the article’s `<footer>` container in `singular-*.php` templates.

---

### `do_action( 'fictioneer_search_footer' )`
Fires right after opening the article’s `<footer>` container in the `search.php` template.

---

### `do_action( 'fictioneer_search_form_after' )`
Fires right after the search form has been rendered.

---

### `do_action( 'fictioneer_search_form_filters', $args )`
Fires after the search form fields for type, match, sort, and order have been rendered. This allows you to render additional fields. Note that any additional fields must also be manually added to the query, see `_module-search.php` partial.

**Parameters:**
* $args (array) – Arguments passed to the search form.

**Hooked Actions:**
* `fictioneer_add_search_for_age_rating( $args )` – HTML for age rating select. Priority 10.
* `fictioneer_add_search_for_status( $args )` – HTML for story status select. Priority 10.
* `fictioneer_add_search_for_min_words( $args )` – HTML for minimum words select. Priority 10.
* `fictioneer_add_search_for_max_words( $args )` – HTML for maximum words select. Priority 10.

---

### `do_action( 'fictioneer_search_no_params' )`
Fires right after opening the article’s no-params `<section>` container in the `search.php` template.

**Hooked Actions:**
* `fictioneer_no_search_params()` – HTML for no search params. Priority 10.

---

### `do_action( 'fictioneer_search_no_results' )`
Fires right after opening the article’s no-results `<section>` container in the `search.php` template.

**Hooked Actions:**
* `fictioneer_no_search_results()` – HTML for no search results. Priority 10.

---

### `do_action( 'fictioneer_shortcode_latest_chapters_card_body', $post, $story_data, $args )`
Fires before the content of the small card grid is rendered in the `partials/_latest-chapters*.php` partials. Does not allow you to change the content of the body, but you can add additional HTML and use CSS to modify the look. If you want to change significantly the cards, better overwrite the partial.

**Parameters:**
* $post (WP_Post) – Post object of the chapter.
* $story_data (array|null) – Pre-processed data of the story. Unsafe.
* $args (array) – Optional arguments passed to the shortcode.

---

### `do_action( 'fictioneer_shortcode_latest_recommendations_card_body', $post, $args )`
Fires before the content of the small card grid is rendered in the `partials/_latest-recommendations*.php` partials. Does not allow you to change the content of the body, but you can add additional HTML and use CSS to modify the look. If you want to change significantly the cards, better overwrite the partial.

**Parameters:**
* $post (WP_Post) – Post object of the recommendation.
* $args (array) – Optional arguments passed to the shortcode.

---

### `do_action( 'fictioneer_shortcode_latest_stories_card_body', $post, $story_data, $args )`
Fires before the content of the small card grid is rendered in the `partials/_latest-stories*.php` partials. Does not allow you to change the content of the body, but you can add additional HTML and use CSS to modify the look. If you want to change significantly the cards, better overwrite the partial.

**Parameters:**
* $post (WP_Post) – Post object of the story.
* $story_data (array) – Pre-processed data of the story.
* $args (array) – Optional arguments passed to the shortcode.

---

### `do_action( 'fictioneer_shortcode_latest_updates_card_body', $post, $story_data, $args )`
Fires before the content of the small card grid is rendered in the `partials/_latest-updates*.php` partials. Does not allow you to change the content of the body, but you can add additional HTML and use CSS to modify the look. If you want to change significantly the cards, better overwrite the partial.

**Parameters:**
* $post (WP_Post) – Post object of the story.
* $story_data (array) – Pre-processed data of the story.
* $args (array) – Optional arguments passed to the shortcode.

---

### `do_action( 'fictioneer_site', $args )`
Fires right after opening the `#site` container in the `header.php` template. Includes the navigation bar and site header with background, logo, title, etc.

**$args:**
* $post_id (int|null) – Current post ID . Unsafe.
* $post_type (string|null) – Current post type. Unsafe.
* $post_id (story_id|null) – Current story ID (if chapter). Unsafe.
* $header_image_url (string|boolean) – URL of the filtered header image or false.
* $header_image_source (string) – Source of the header image. Either `default`, `post`, or `story`.
* $header_args (array) – Arguments passed to the header.php partial.

**Hooked Actions:**
* `fictioneer_browser_notes()` – HTML for the browser compatibility notes. Priority 1.
* `fictioneer_top_header( $args )` – HTML for the top header. Priority 9.
* `fictioneer_navigation_bar( $args )` – HTML for the navigation bar. Priority 10.
* `fictioneer_inner_header( $args )` – HTML for the inner header. Priority 20.

---

### `do_action( 'fictioneer_site_footer', $args )`
Fires first inside the site’s `<footer>` container. Normally includes the breadcrumb navigation (if any), the footer menu, and the theme copyright notice.

**$args:**
* 'post_type' (string|null) – Current post type. Unsafe.
* 'post_id' (int|null) – Current post ID. Unsafe.
* 'breadcrumbs' (array) – Array of breadcrumb tuples with label (0) and link (1).

**Hooked Actions:**
* `fictioneer_breadcrumbs( $args )` – Breadcrumbs. Priority 10.
* `fictioneer_footer_menu_row( $args )` – Footer menu and theme copyright notice. Priority 20.

---

### `do_action( 'fictioneer_stories_after_content', $args )`
List page template hook. Fires right after the content section in the `stories.php` template. Includes the statistics and paginated card list of all visible stories on the site.

**Note:** Some actions are added conditionally in the `wp` action hook (priority 10).

**$args:**
* 'current_page' (int) – Current page number of pagination or 1.
* 'post_id' (int) – Current post ID.
* 'stories' (WP_Query) – Paginated query of all published stories.
* 'queried_type' (string) – `fcn_story`
* 'query_args' (array) – The final query arguments used.
* 'order' (string) – Current order query argument. Default 'desc'.
* 'orderby' (string) – Current orderby query argument. Default 'modified'.
* 'ago' (int|string) – Current value for the date query. Default 0.

**Hooked Actions:**
* `fictioneer_stories_statistics( $args )` – Compiled statistics of all stories. Priority 10.
* `fictioneer_sort_order_filter_interface( $args )` – Interface to sort, order, and filter. Priority 20.
* `fictioneer_stories_list( $args )` – Paginated card list of all visible stories. Priority 30.

---

### `do_action( 'fictioneer_stories_end_of_results', $args )`
List page template hook. Fires right after the last list item in the `'fictioneer_stories_list'` action.

**Note:** Some actions are added conditionally in the `wp` action hook (priority 10).

**$args:**
* 'current_page' (int) – Current page number of pagination or 1.
* 'post_id' (int) – Current post ID.
* 'stories' (WP_Query) – Paginated query of all published stories.
* 'queried_type' (string) – `fcn_story`

---

### `do_action( 'fictioneer_stories_no_results', $args )`
List page template hook. Fires right at the top of an empty result list in the `'fictioneer_stories_list'` action, before the no-result message is rendered.

**Note:** Some actions are added conditionally in the `wp` action hook (priority 10).

**$args:**
* 'current_page' (int) – Current page number of pagination or 1.
* 'post_id' (int) – Current post ID.
* 'stories' (WP_Query) – Paginated query of all published stories.
* 'queried_type' (string) – `fcn_story`

---

### `do_action( 'fictioneer_story_after_article', $args )`
Fires right after the `<article>` container in the `single-fcn_story.php` template, after the content and story footer has been rendered. Normally includes the story comment list.

**Note:** Some actions are added conditionally in the `wp` action hook (priority 10).

**$args:**
* 'story_data' (array) – Collection of story data.
* 'story_id' (int) – Current story (post) ID.
* 'password_required' (boolean|null) – Whether the post is unlocked or not. Unsafe.

**Hooked Actions:**
* `fictioneer_story_comments( $args )` – AJAX-loaded list of all comments for story chapters. Priority 10.

---

### `do_action( 'fictioneer_story_after_content', $args )`
Fires right after the content section in the `single-fcn_story.php` template, inside the `<article>` container and just before the footer is rendered. Normally includes the tags, content warnings, row with social media and action buttons, and finally a tabbed section with the chapters, related blog posts (by category), and custom pages.

**Note:** Some actions are added conditionally in the `wp` action hook (priority 10).

**$args:**
* 'story_data' (array) – Collection of story data.
* 'story_id' (int) – Current story (post) ID.
* 'password_required' (boolean|null) – Whether the post is unlocked or not. Unsafe.

**Hooked Actions:**
* `fictioneer_story_copyright_notice( $args )` – Copyright notice. Priority 10.
* `fictioneer_story_tags_and_warnings( $args )` – Tags and content warnings. Priority 20.
* `fictioneer_story_actions( $args )` – Row with story actions. Priority 30.
* `fictioneer_story_tabs( $args )` – Tabs for the chapters, blog, and custom pages. Priority 40.
* `fictioneer_story_scheduled_chapter( $args )` – Scheduled chapter note. Priority 41.
* `fictioneer_story_pages( $args )` – Custom story pages. Priority 42.
* `fictioneer_story_chapters( $args )` – Chapter list. Priority 43.
* `fictioneer_story_blog( $args )` – Blog posts assigned to the story. Priority 44.

---

### `do_action( 'fictioneer_story_after_header', $args )`
Fires right after the article header in the `single-fcn_story.php` template.

Fires right after the article header (cover, title, fandom, genres, and characters) in the `single-fcn_story.php` template, inside the `<article>` container and just before the content section.

**$args:**
* 'story_data' (array) – Collection of story data.
* 'story_id' (int) – Current story (post) ID.
* 'password_required' (boolean|null) – Whether the post is unlocked or not. Unsafe.

---

### `do_action( 'fictioneer_story_before_comments_list', $args )`
Fires right between the comments list and heading in the `fictioneer_story_comments()` function.

**$args:**
* 'story_data' (array) – Collection of story data.
* 'story_id' (int) – Current story (post) ID.
* 'header' (boolean|null) – Optional. Whether to show the heading with count. Default true.
* 'classes' (string|null) – Optional. Additional CSS classes, separated by whitespace.
* 'style' (string|null) – Optional. Inline style applied to the wrapper element.
* 'shortcode' (boolean|null) – Optional. Whether the render context is a shortcode.

---

### `do_action( 'fictioneer_text_center_header', $args )`
Fires right after opening the `<header>` container for the "Text (Centered)" header style.

**$args:**
* 'post_id' (int|null) – Current post ID. Unsafe.
* 'story_id' (int|null) – Current story ID. Unsafe.
* 'header_image_url' (string|boolean) – URL of the filtered header image or false.
* 'header_args' (array) – Arguments passed to the header.php partial.

**Hooked Actions:**
* `function( $args )` – Anonymous function to output the header content. Priority 10.

---

### `do_action( 'fictioneer_toggled_follow', $story_id, $force )`
Fires after a Follow has been successfully toggled and right before the JSON response is sent.

**$args:**
* 'story_id' (int) – ID of the story.
* 'force' (bool) – Whether the Follow was toggled on (true) or off (false).

---

### `do_action( 'fictioneer_toggled_reminder', $story_id, $force )`
Fires after a Reminder has been successfully toggled and right before the JSON response is sent.

**$args:**
* 'story_id' (int) – ID of the story.
* 'force' (bool) – Whether the Follow was toggled on (true) or off (false).

---

### `do_action( 'fictioneer_top_header', $args )`
Fires right after opening the top-aligned `<header>` container.

**$args:**
* 'post_id' (int|null) – Current post ID. Unsafe.
* 'story_id' (int|null) – Current story ID. Unsafe.
* 'header_image_url' (string|boolean) – URL of the filtered header image or false.
* 'header_args' (array) – Arguments passed to the header.php partial.
