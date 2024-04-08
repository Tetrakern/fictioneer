# Customization Snippets

This is a collection of code snippets for previously solved customization issues. They are supposed to go into your child theme’s **functions.php** or a subsequently included file (replace "child_" and "x.x.x" with your child theme prefix and version respectively). Putting them into the main theme will cause them to be deleted when you update. Also assume each snippet requires the latest version of Fictioneer.

For general information on theme and style customization, please refer to the [installation guide](https://github.com/Tetrakern/fictioneer/blob/main/INSTALLATION.md#how-to-customize-the-fictioneer-theme).

## Add a secondary chapter title

Related to [this issue](https://github.com/Tetrakern/fictioneer/issues/23). Maybe you need a secondary chapter title to fulfill some arbitrary naming scheme; you do you. While not encouraged since this adds at least one new database row per chapter (plus another for each revision if you have not turned that off), which can eventually slow down your site, this can be achieved by making use of an already existing meta field. Of course, you could also add a new one with more custom code.

First, check the **Enabled advanced meta fields** option under **Fictioneer > General > Compatibility**. This will, among other things, reveal the **Short Title** meta field in the chapter sidebar. This field is not actually used by the theme and only for customizations, such as this one. Just ignore the name if it does not fit, or go through the trouble of changing it.

The assumption here is that you want the normal title to be short, like "WTF 1", and the secondary title something longer. You could also reverse the order of things by changing the example code to match your requirements. The functions below will filter the titles for the chapter header and chapter lists as seen on story pages, but not on card grid shortcodes. Note that there is no styling for the secondary title provided, that is your responsibility.

**References**
* Filter: [fictioneer_filter_chapter_identity](https://github.com/Tetrakern/fictioneer/blob/main/FILTERS.md#apply_filters-fictioneer_filter_chapter_identity-output-args-)
* Filter: [fictioneer_filter_safe_title](https://github.com/Tetrakern/fictioneer/blob/main/FILTERS.md#apply_filters-fictioneer_filter_safe_title-title-post_id-context-args-)
* Include: [_story_hooks.php](https://github.com/Tetrakern/fictioneer/blob/main/includes/functions/hooks/_story_hooks.php)
* Include: [_shortcodes.php](https://github.com/Tetrakern/fictioneer/blob/main/includes/functions/_shortcodes.php)
* Partial: [_chapter-header.php](https://github.com/Tetrakern/fictioneer/blob/main/partials/_chapter-header.php)

```php
/**
 * Modifies the chapter header
 *
 * @since x.x.x
 *
 * @param array $output  HTML elements to be rendered.
 * @param array $args    Array of story and chapter data.
 *
 * @return array Modified HTML elements.
 */

function child_modify_chapter_header( $output, $args ) {
  // Setup
  $password_class = empty( $args['chapter_password'] ) ? '' : ' _password';
  $other_title = get_post_meta( $args['chapter_id'], 'fictioneer_chapter_short_title', true );
  $icon_classes = fictioneer_get_icon_field( 'fictioneer_chapter_icon', $args['chapter_id'] );
  $icon = '';
  $meta = $output['meta'];

  // Abort if second title is not set
  if ( empty( $other_title ) ) {
    return $output;
  }

  // Optional: Uncomment this to prepend the chapter icon (if any)
  // if ( ! empty( $icon_classes ) ) {
  //   $icon = '<i class="' . $icon_classes . '"></i> '; // Mind the space afterwards
  // }

  // Temporary remove meta row
  unset( $output['meta'] );

  // Rebuild HTML
  $output['title'] = '<h1 class="chapter__title' . $password_class . '">' . $icon . $args['chapter_title'] . '</h1>';
  $output['other_title'] = '<h2 class="chapter-second-title">' . $other_title . '</h2>';
  $output['meta'] = $meta;

  // Continue filter
  return $output;
}
add_filter( 'fictioneer_filter_chapter_identity', 'child_modify_chapter_header', 10, 2 );
```

![Secondary Chapter Title](repo/assets/secondary_chapter_title.png?raw=true)

```php
/**
 * Modifies chapter titles in chapter lists
 *
 * @since x.x.x
 *
 * @param string      $title    The sanitized title of the post.
 * @param int         $post_id  The post ID.
 * @param string|null $context  Context regarding where or how the title is used. Unsafe.
 *
 * @return array Modified HTML elements.
 */

function child_modify_chapter_list_title( $title, $post_id, $context ) {
  // Only for chapter lists (story pages and shortcode)
  if ( ! in_array( $context, ['story-chapter-list', 'shortcode-chapter-list'] ) ) {
    return $title;
  }

  // Setup
  $other_title = get_post_meta( $post_id, 'fictioneer_chapter_short_title', true );

  // Abort if second title is not set
  if ( empty( $other_title ) ) {
    return $title;
  }

  // Append to title
  $title = $title . ' — ' . $other_title;

  // Continue filter
  return $title;
}
add_filter( 'fictioneer_filter_safe_title', 'child_modify_chapter_list_title', 10, 3 );
```

![Secondary Chapter Title](repo/assets/secondary_chapter_list_title.png?raw=true)

## Only show a specific advanced meta field

Maybe you want only one specific advanced meta field. You can achieve this by manually adding the desired field and saving procedure, similar to how it is done in the [_meta_fields.php](https://github.com/Tetrakern/fictioneer/blob/main/includes/functions/_meta_fields.php). The following example adds the Co-Authors field to stories, which can be adapted for chapters as well. Just make sure to change the {dynamic_parts} and the meta keys.

**References**
* Filter: [fictioneer_filter_metabox_{meta_box}](https://github.com/Tetrakern/fictioneer/blob/main/FILTERS.md#apply_filters-fictioneer_filter_metabox_meta_box-output-post-)
* Filter: [fictioneer_filter_metabox_updates_{type}](https://github.com/Tetrakern/fictioneer/blob/main/FILTERS.md#apply_filters-fictioneer_filter_metabox_updates_type-fields-post_id-)
* Include: [_meta_fields.php](https://github.com/Tetrakern/fictioneer/blob/main/includes/functions/_meta_fields.php)

```php
/**
 * Adds the Co-Authors meta field to stories
 *
 * @since x.x.x
 *
 * @param array   $output  Captured HTML of meta fields to be rendered.
 * @param WP_Post $post    The post object.
 *
 * @return array Updated output.
 */

function child_add_co_authors_to_story( $output, $post ) {
  // Append field to output
  $output['fictioneer_story_co_authors'] = fictioneer_get_metabox_array(
    $post,
    'fictioneer_story_co_authors', // Meta key
    array(
      'label' => _x( 'Co-Authors', 'Story co-authors meta field label.', 'fictioneer' ),
      'description' => __( 'Comma-separated list of author IDs.', 'fictioneer' )
    )
  );

  // Continue filter
  return $output;
}
add_filter( 'fictioneer_filter_metabox_story_meta', 'child_add_co_authors_to_story', 10, 2 );

/**
 * Adds Co-Authors to fields to be saved for stories
 *
 * @since x.x.x
 *
 * @param array $fields  Meta fields to be saved.
 *
 * @return array Updated fields.
 */

function child_save_co_authors_of_story( $fields ) {
  // Append sanitized field content for saving (if any)
  if ( isset( $_POST['fictioneer_story_co_authors'] ) ){
    $co_authors = fictioneer_explode_list( $_POST['fictioneer_story_co_authors'] ); // Array from comma separated list
    $co_authors = array_map( 'absint', $co_authors ); // Only positive integers are allowed
    $co_authors = array_filter( $co_authors, function( $user_id ) {
      return get_userdata( $user_id ) !== false; // Filter out user IDs that do not exist
    });
    $fields['fictioneer_story_co_authors'] = array_unique( $co_authors ); // Queue for saving (duplicated removed)
  }

  // Continue filter
  return $fields;
}
add_filter( 'fictioneer_filter_metabox_updates_story', 'child_save_co_authors_of_story', 10 );
```

## Limit tags to 10

Or any other positive number for that matter. To prevent authors from entering a whole thesis of tags like a lunatic. This is not the best way, because it will just remove any tags exceeding the limit with no feedback for the author. Maybe that will teach them a lesson. But anything else needs to interfere with the Gutenberg editor and is difficult to achieve.

**References**
* Action: [save_post](https://developer.wordpress.org/reference/hooks/save_post/)

```php
/**
 * Limit post tags to a maximum of 10
 *
 * @since x.x.x
 *
 * @param int $post_id  The post ID.
 */

function child_limit_tags_per_post( $post_id ) {
  // Ignore auto saves, multi-fire, etc.
  if ( fictioneer_multi_save_guard( $post_id ) ) {
    return;
  }

  // Get current tags (already saved at this point)
  $tags = wp_get_post_tags( $post_id );

  // Check if there are more than 10 (or your number)
  if ( count( $tags ) > 10 ) {
    // Only keep the first 10
    $tags = array_slice( $tags, 0, 10 );

    // Update the tags in the database and watch your authors cry
    wp_set_post_tags( $post_id, $tags, false );
  }
}
add_action( 'save_post', 'child_limit_tags_per_post', 99 ); // Executed late
```

## Change chapter formatting defaults

The formatting of chapters is left to the reader, as it should be. Customizing the formatting to your preferences and needs can greatly enhance the reading experience. However, you can alter the defaults via a [filter](FILTERS.md#apply_filters-fictioneer_filter_chapter_default_formatting-formatting-).

**References**
* Filter: [fictioneer_filter_chapter_default_formatting](FILTERS.md#apply_filters-fictioneer_filter_chapter_default_formatting-formatting-)
* Include: [_theme_setup.php](https://github.com/Tetrakern/fictioneer/blob/main/includes/functions/_theme_setup.php)

```php
/**
 * Change default formatting
 *
 * @since x.x.x
 *
 * @param array $formatting  The formatting defaults.
 *
 * @return array The updated formatting defaults.
 */

function child_update_chapter_formatting_defaults( $formatting ) {
  // Changes (uncomment and update what you need)
  $changes = array(
    // 'font-saturation' => 0,
    // 'font-size' => 100,
    // 'letter-spacing' => 0,
    // 'line-height' => 1.7,
    // 'paragraph-spacing' => 1.5,
    // 'indent' => true,
    // 'show-sensitive-content' => true,
    // 'show-chapter-notes' => true,
    // 'justify' => false,
    // 'show-comments' => true,
    // 'show-paragraph-tools' => true
  );

  // Merge changes with defaults and continue filter
  return array_merge( $formatting, $changes );
}
add_filter( 'fictioneer_filter_chapter_default_formatting', 'child_update_chapter_formatting_defaults' );
```

## Show scheduled chapters on story pages

If the "Next Chapter" note above the chapter list is not enough and you want to show scheduled (future) chapters in the list as well, you can alter the query via filter. You can also include other post statuses, although the sense of that is dubious. Note that this will not alter the [API responses](https://github.com/Tetrakern/fictioneer/blob/main/API.md), because they needs to be uniform across all sites.

**References**
* Filter: [fictioneer_filter_story_chapter_posts_query](FILTERS.md#apply_filters-fictioneer_filter_story_chapter_posts_query-query_args-story_id-chapter_ids-)

```php
/**
 * Show scheduled (future) chapter in story chapter list
 *
 * @since x.x.x
 *
 * @param array $query_args  Chapter list query arguments.
 *
 * @return array The updated chapter list query arguments.
 */

function child_show_scheduled_chapters( $query_args ) {
  $query_args['post_status'] = ['publish', 'future'];

  return $query_args;
}
add_filter( 'fictioneer_filter_story_chapter_posts_query', 'child_show_scheduled_chapters' );

// If you want to remove the "Next Chapter" note above the list:

/**
 * Remove "Next Chapter" note above list
 *
 * @since x.x.x
 */

function child_remove_scheduled_chapter() {
  remove_action( 'fictioneer_story_after_content', 'fictioneer_story_scheduled_chapter', 41 );
}
add_action( 'wp', 'child_remove_scheduled_chapter', 11 ); // The action is added late, so you need to be even later
```

## Story page cover image on the right side

If you would rather have the story page cover image floating on the right, as demonstrated in the screenshot. Note that this solution disables the option to hide cover images on story pages globally. You can only choose not to have none.

![Floating Cover Image Example](repo/assets/example_floating_cover_image.jpg?raw=true)

```php
/**
 * Adds cover image above the content row
 *
 * @since x.x.x
 *
 * @param array $hook_args  Contains story ID and data.
 */

function child_add_floating_story_cover( $hook_args ) {
  if ( has_post_thumbnail( $hook_args['story_id'] ) ) {
    echo fictioneer_get_story_page_cover( $hook_args['story_data'] );
  }
}
add_action( 'fictioneer_story_after_header', 'child_add_floating_story_cover', 9 );

/**
 * Disables the default cover image in the article header
 *
 * Note: Alternatively, you could hide the cover image on the story. But
 * you would need to do this for every story.
 *
 * @since x.x.x
 * @link https://developer.wordpress.org/reference/hooks/get_meta_type_metadata/
 *
 * @param mixed  $value      The value to return, either a single metadata value or
 *                           an array of values depending on the value of $single.
 *                           Default null.
 * @param int    $object_id  ID of the object metadata is for.
 * @param string $meta_key   Metadata key.
 *
 * @return mixed The updated meta value.
 */

function child_disable_story_page_cover( $value, $object_id, $meta_key ) {
  // Disable 'fictioneer_story_no_thumbnail' meta field
  if ( $meta_key === 'fictioneer_story_no_thumbnail' ) {
    return true;
  }

  // Continue filter
  return $value;
}
add_filter( 'get_post_metadata', 'child_disable_story_page_cover', 10, 3 );
```

Add this to your CSS file or in **Appearance > Customize > Additional CSS**.

```css
.story__tags-and-warnings,
.story__after-summary {
  clear: both;
}

.story__thumbnail {
  float: right;
  margin-right: var(--layout-spacing-horizontal);
  margin-left: 1rem;
}
```
