# Customization Snippets

This is a collection of code snippets for previously solved customization issues. They are supposed to go into your child theme’s **functions.php** or a subsequently included file (replace "child_" and "x.x.x" with your child theme prefix and version respectively). Putting them into the main theme would work, but will cause them to be deleted when you update.

Alternatively, you can put the code into a [must-use plugin](https://developer.wordpress.org/advanced-administration/plugins/mu-plugins/) file. Create a new PHP file with a name of your choice in the the `/wp-content/mu-plugins/` directory (create the directory if it does not exist). Use the following example code as base:

<details>
  <summary>Example customization must-use plugin</summary>

```php
<?php
/**
 * Plugin Name: Fictioneer Customization
 * Description: Scripts to customize the theme or child theme.
 * Version: 1.0.0
 * Author: YOUR NAME
 * License: GNU General Public License v3.0 or later
 * License URI: http://www.gnu.org/licenses/gpl.html
 */


/**
 * Adds actions and filters after the theme has loaded
 * and all hooks have been registered
 */

function custom_initialize() {
  // Uncomment the following line to apply the example filter
  // add_filter( 'fictioneer_filter_post_meta_items', 'custom_modify_post_meta_items' );
}
add_action( 'after_setup_theme', 'custom_initialize', 99 );

/**
 * Example: Removes the icons from the post meta row and separates the items with a "|"
 *
 * Note: Add ".post__meta { gap: .5rem; }" under Appearance > Customize > Custom CSS.
 *
 * @param array $output  The HTML of the post meta items to be rendered.
 *
 * @return array The updated items.
 */

function custom_modify_post_meta_items( $output ) {
  // Remove icons
  $output = array_map( function( $item ) { return preg_replace( '/<i[^>]*>.*?<\/i>/', '', $item ); }, $output );
  $count = 0;
  $new_output = [];

  // Add slashes as divider
  foreach ( $output as $key => $value ) {
    if ( $count > 0 ) {
      $new_output[ $key . '_slash' ] = '<span class="divider">|</span>';
    }

    $new_output[ $key ] = $value;

    $count++;
  }

  // Continue filter
  return $new_output;
}
```
</details>

<br>

For general information on theme and style customization, please refer to the [installation guide](https://github.com/Tetrakern/fictioneer/blob/main/INSTALLATION.md#how-to-customize-the-fictioneer-theme).

## Add a secondary chapter title

Related to [this issue](https://github.com/Tetrakern/fictioneer/issues/23). Maybe you need a secondary chapter title to fulfill some arbitrary naming scheme; you do you. While not encouraged since this adds at least one new database row per chapter (plus another for each revision if you have not turned that off), which bears the danger of slowing down your site, this can be achieved by making use of an already existing meta field. Of course, you could also add a new one with more custom code.

First, check the **Enabled advanced meta fields** option under **Fictioneer > General > Compatibility**. This will, among other things, reveal the **Short Title** meta field in the chapter sidebar. This field is not actually used by the theme and only for customizations, such as this one. Just ignore the name if it does not fit, or go through the trouble of changing it.

The assumption here is that you want the normal title to be short, like "WTF 1", and the secondary title something longer. You could also reverse the order of things by changing the example code to match your requirements. The functions below will filter the titles for the chapter header and chapter lists as seen on story pages, but not on card grid shortcodes. Note that there is no styling for the secondary title provided, that is your responsibility.

**References**
* Filter: [fictioneer_filter_chapter_identity](https://github.com/Tetrakern/fictioneer/blob/main/FILTERS.md#apply_filters-fictioneer_filter_chapter_identity-output-args-)
* Filter: [fictioneer_filter_safe_title](https://github.com/Tetrakern/fictioneer/blob/main/FILTERS.md#apply_filters-fictioneer_filter_safe_title-title-post_id-context-args-)
* Include: [_story_hooks.php](https://github.com/Tetrakern/fictioneer/blob/main/includes/functions/hooks/_story_hooks.php)
* Include: [_setup-shortcodes.php](https://github.com/Tetrakern/fictioneer/blob/main/includes/functions/_setup-shortcodes.php)
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

## Add prefix to chapter index list titles

Or change them completely, if you want even depending on the chapter or associated story. Related to [this issue](https://github.com/Tetrakern/fictioneer/issues/31). Using a filter, you can rebuild the list item HTML to your liking. In the following example, the chapter prefix has been prepended (if there is one).

**References**
* Filter: [fictioneer_filter_chapter_index_item](https://github.com/Tetrakern/fictioneer/blob/main/FILTERS.md#apply_filters-fictioneer_filter_chapter_index_item-item-post-args-)
* Include: [_helpers-templates.php](https://github.com/Tetrakern/fictioneer/blob/main/includes/functions/_helpers-templates.php)

```php
/**
 * Overwrites the chapter index list item string with a prefixed title
 *
 * Note: Warning, this replaces the complete string and should be
 * executed early in case there are more, less extreme filters.
 *
 * @since x.x.x
 *
 * @param string  $item  Original HTML string
 * @param WP_Post $post  Chapter post object.
 * @param array   $args  Array of chapter data.
 *
 * @return string New HTML list of list item.
 */

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

// Priority 1 to execute the filter early
add_filter( 'fictioneer_filter_chapter_index_item', 'child_prefix_chapter_index_list_item', 1, 3 );
```

## Only show a specific advanced meta field

Maybe you want only one specific advanced meta field. You can achieve this by manually adding the desired field and saving procedure, similar to how it is done in the [_setup-meta-fields.php](https://github.com/Tetrakern/fictioneer/blob/main/includes/functions/_setup-meta-fields.php). The following example adds the Co-Authors field to stories, which can be adapted for chapters as well. Just make sure to change the {dynamic_parts} and the meta keys.

**References**
* Filter: [fictioneer_filter_metabox_{meta_box}](https://github.com/Tetrakern/fictioneer/blob/main/FILTERS.md#apply_filters-fictioneer_filter_metabox_meta_box-output-post-)
* Filter: [fictioneer_filter_metabox_updates_{type}](https://github.com/Tetrakern/fictioneer/blob/main/FILTERS.md#apply_filters-fictioneer_filter_metabox_updates_type-fields-post_id-)
* Include: [_setup-meta-fields.php](https://github.com/Tetrakern/fictioneer/blob/main/includes/functions/_setup-meta-fields.php)

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

Or any other positive number for that matter. To prevent authors from entering a whole f\*\*\*ing thesis of tags. This is not the best way, because it will just remove any tags exceeding the limit with no feedback for the author. But maybe that will teach them a lesson. Anything better needs to interfere with the Gutenberg editor and is difficult to achieve.

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

The formatting of chapters is left to the visitor. Because customizing the formatting to your preferences and needs can greatly enhance the reading experience. However, you can at least alter the defaults via a [filter](FILTERS.md#apply_filters-fictioneer_filter_chapter_default_formatting-formatting-).

**References**
* Filter: [fictioneer_filter_chapter_default_formatting](FILTERS.md#apply_filters-fictioneer_filter_chapter_default_formatting-formatting-)
* Include: [_setup-theme.php](https://github.com/Tetrakern/fictioneer/blob/main/includes/functions/_setup-theme.php)

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

If the "Next Chapter" note above the chapter list is not enough and you want to show scheduled (future) chapters in the list as well, you can alter the query via filter. You can also include other post statuses, although the sense of that is dubious. Note that this will not alter the [API responses](https://github.com/Tetrakern/fictioneer/blob/main/API.md), because they must be uniform across all sites, and scheduled chapters are still not accessible without special permission or certain plugins.

```php
/**
 * Allows scheduled (future) chapters to be queried
 *
 * @since x.x.x
 *
 * @param string[] $statuses  Statuses that are queried. Default ['publish].
 *
 * @return array The updated array of statuses.
 */

function child_query_scheduled_chapters( $statuses ) {
  $statuses[] = 'future';

  return $statuses;
}
add_filter( 'fictioneer_filter_get_story_data_queried_chapter_statuses', 'child_query_scheduled_chapters' );

/**
 * Shows scheduled (future) chapter in story chapter list
 *
 * @since x.x.x
 *
 * @param array $query_args  Chapter list query arguments.
 *
 * @return array The updated chapter list query arguments.
 */

function child_show_scheduled_chapters( $query_args ) {
  if ( ! is_array( $query_args['post_status'] ) ) {
    $query_args['post_status'] = $query_args['post_status'] ? [ $query_args['post_status'] ] : ['publish'];
  }

  if ( ! in_array( 'future', $query_args['post_status'] ) ) {
    $query_args['post_status'] = ['publish', 'future'];
  }

  return $query_args;
}
add_filter( 'fictioneer_filter_story_chapter_posts_query', 'child_show_scheduled_chapters' );

// If you want to remove the "Next Chapter" note above the list:

/**
 * Removes "Next Chapter" note above list
 *
 * @since x.x.x
 */

function child_remove_scheduled_chapter() {
  remove_action( 'fictioneer_story_after_content', 'fictioneer_story_scheduled_chapter', 41 );
}
add_action( 'wp', 'child_remove_scheduled_chapter', 11 ); // The action is added late, so you need to be even later

// If you want scheduled chapters to be considered a story update,
// which is important for the Stories page template and shortcodes:

/**
 * Adds the 'future' post status to the update allow list
 *
 * Note: Hidden chapters are still ignored.
 *
 * @since x.x.x
 *
 * @param string[] $statuses  Statuses that are queried. Default ['publish].
 *
 * @return array The updated array of statuses.
 */

function child_consider_scheduled_chapters_as_update( $statuses ) {
  $statuses[] = 'future';

  return $statuses;
}
add_action( 'fictioneer_filter_chapters_added_statuses', 'child_consider_scheduled_chapters_as_update' );

// If you want scheduled chapters to be treated like published ones,
// also add the following filters; you still need a function or plugin
// to make them accessible or they will lead to a 404 error page.

/**
 * Adds the 'future' post status to an allowed statuses array
 *
 * @since x.x.x
 *
 * @param string[] $statuses  Statuses that are queried. Default ['publish].
 *
 * @return array The updated array of statuses.
 */

function child_treat_scheduled_chapters_as_published( $statuses ) {
  $statuses[] = 'future';

  return $statuses;
}
add_filter( 'fictioneer_filter_chapter_index_list_statuses', 'child_treat_scheduled_chapters_as_published' );
add_filter( 'fictioneer_filter_chapter_nav_buttons_allowed_statuses', 'child_treat_scheduled_chapters_as_published' );
add_filter( 'fictioneer_filter_get_story_data_indexed_chapter_statuses', 'child_treat_scheduled_chapters_as_published' );
add_filter( 'fictioneer_filter_allowed_chapter_permalinks', 'child_treat_scheduled_chapters_as_published' );
```

**Alternative:** Set the `FICTIONEER_LIST_SCHEDULED_CHAPTERS` constant to `true` to automatically apply all the aforementioned filters, except for the removal of the next chapter note. If you want to make scheduled chapters as accessible as published ones, for example to put them behind a paywall, just enable the **\[Make scheduled chapters publicly accessible\]** theme setting under **Fictioneer > General**.

```php
if ( ! defined( 'FICTIONEER_LIST_SCHEDULED_CHAPTERS' ) ) {
  define( 'FICTIONEER_LIST_SCHEDULED_CHAPTERS', true );
}
```

## Modify or remove items from card footers

While you can easily hide card footer items with CSS (e.g. `.card__footer-status {display: none;}`), removing them is the better choice performance-wise. However, if you want to reorder them, change the icons, or add new ones altogether, filters have got you covered. Do not forget to clear the theme caches afterwards.

**References**
* Filter: [fictioneer_filter_shortcode_latest_updates_card_footer](FILTERS.md#apply_filters-fictioneer_filter_shortcode_latest_updates_card_footer-footer_items-post-story-args-)
* Filter: [fictioneer_filter_shortcode_latest_stories_card_footer](FILTERS.md#apply_filters-fictioneer_filter_shortcode_latest_stories_card_footer-footer_items-post-story-args-)
* Filter: [fictioneer_filter_shortcode_latest_chapters_card_footer](FILTERS.md#apply_filters-fictioneer_filter_shortcode_latest_chapters_card_footer-footer_items-post-story-args-)
* Filter: [fictioneer_filter_shortcode_article_card_footer](FILTERS.md#apply_filters-fictioneer_filter_shortcode_article_card_footer-footer_items-posts-)
* Filter: [fictioneer_filter_story_card_footer](FILTERS.md#apply_filters-fictioneer_filter_story_card_footer-footer_items-post-story-args-)
* Filter: [fictioneer_filter_post_card_footer](FILTERS.md#apply_filters-fictioneer_filter_post_card_footer-footer_items-post-args-)
* Filter: [fictioneer_filter_page_card_footer](FILTERS.md#apply_filters-fictioneer_filter_page_card_footer-footer_items-post-args-)
* Filter: [fictioneer_filter_collection_card_footer](FILTERS.md#apply_filters-fictioneer_filter_collection_card_footer-footer_items-post-args-items-)
* Filter: [fictioneer_filter_chapter_card_footer](FILTERS.md#apply_filters-fictioneer_filter_chapter_card_footer-footer_items-post-story-args-)

```php
/**
 * Removes modified date from all Latest Updates shortcode
 *
 * @since x.x.x
 *
 * @param array $footer_items  HTML of the card footer items.
 *
 * @return array Updated HTML of the card footer items.
 */

function child_remove_modified_date_from_latest_updates( $footer_items ) {
  // Remove item
  unset( $footer_items['modified_date'] );

  // Continue filter
  return $footer_items;
}
add_filter( 'fictioneer_filter_shortcode_latest_updates_card_footer', 'child_remove_modified_date_from_latest_updates' );

/**
 * Adds footer item to large story cards
 *
 * @since x.x.x
 *
 * @param array $footer_items  HTML of the card footer items.
 *
 * @return array Updated HTML of the card footer items.
 */

function child_add_item_to_story_card_footers( $footer_items ) {
  // Append item
  $footer_items['star'] = '<span class="card__footer-star"><i class="card-footer-icon fa-solid fa-star" title="Star"></i> Star</span>';

  // Continue filter
  return $footer_items;
}
add_filter( 'fictioneer_filter_story_card_footer', 'child_add_item_to_story_card_footers' );
```

## Display taxonomies on chapter pages

Chapters normally do not display taxonomies, because why bother if the story is already tagged to kingdom come? Do you really need half a page of tags? Anyway, if you want to display taxonomies on chapter pages as well, you can hook them in with some custom HTML.

**References**
* Action: [fictioneer_chapter_before_header](ACTIONS.md#do_action-fictioneer_chapter_before_header-args-)
* Action: [fictioneer_chapter_after_content](ACTIONS.md#do_action-fictioneer_chapter_after_content-args-)
* Template: [single-fcn_chapter.php](https://github.com/Tetrakern/fictioneer/blob/main/single-fcn_chapter.php)
* Include: [_chapter_hooks.php](https://github.com/Tetrakern/fictioneer/blob/main/includes/functions/hooks/_chapter_hooks.php)

```php
/**
 * Renders taxonomies on chapter page
 *
 * @since x.x.x
 *
 * @param array $args  Arguments passed to the action.
 */

function child_display_chapter_taxonomies( $args ) {
  // Get desired taxonomies
  $fandoms = wp_get_post_terms( $args['chapter_id'], 'fcn_fandom' );
  $characters = wp_get_post_terms( $args['chapter_id'], 'fcn_character' );
  // Genres, tags, etc.

  // Store the links to output here
  $output = [];

  // Fandoms
  if ( ! is_wp_error( $fandoms ) && ! empty( $fandoms ) ) {
    $output = array_merge(
      $output,
      array_map(
        function( $term ) {
          return "<a class='tag-pill _taxonomy-fandom' href='" . get_term_link( $term ) . "'>{$term->name}</a>";
        },
        $fandoms
      )
    );
  }

  // Characters
  if ( ! is_wp_error( $characters ) && ! empty( $characters ) ) {
    $output = array_merge(
      $output,
      array_map(
        function( $term ) {
          return "<a class='tag-pill _taxonomy-character' href='" . get_term_link( $term ) . "'>{$term->name}</a>";
        },
        $characters
      )
    );
  }

  // Genres, tags, etc.

  // Render (you may need some top/bottom margins)
  if ( ! empty( $output ) ) {
    echo '<div class="tag-group">' . implode( '', $output ) . '</div>';
  }
}

// Add above the chapter header and any info boxes
add_action( 'fictioneer_chapter_before_header', 'child_display_chapter_taxonomies', 3 );

// ... or add it above the top action row
add_action( 'fictioneer_chapter_before_header', 'child_display_chapter_taxonomies', 1 );

// ... or at the bottom for whatever reason
add_action( 'fictioneer_chapter_after_content', 'child_display_chapter_taxonomies', 5 );
```

## Remove theme metaboxes

Maybe you do not want the "Support Links" or "Extra" meat boxes. You can disable them by removing the actions from the respective hooks, which must be done with a priority later than 10.

**References**
* Action: [add_meta_boxes](https://developer.wordpress.org/reference/hooks/add_meta_boxes/)
* Action: [save_post](https://developer.wordpress.org/reference/hooks/save_post/)
* Include: [_setup-meta-fields.php](https://github.com/Tetrakern/fictioneer/blob/main/includes/functions/_setup-meta-fields.php)

```php
/**
 * Removes selected theme metaboxes and save actions
 *
 * @since x.x.x
 */

function child_remove_metaboxes() {
  // Remove "Extra" metabox
  remove_meta_box(
    'fictioneer-extra',
    ['post', 'page', 'fcn_story', 'fcn_chapter', 'fcn_recommendation', 'fcn_collection'],
    'side'
  );

  // Remove saving action too
  remove_action( 'save_post', 'fictioneer_save_extra_metabox' );
}
add_action( 'add_meta_boxes', 'child_remove_metaboxes', 99 );
```

## Change large story card taxonomies to pills

Unlike with shortcodes, you cannot just add parameters to change the display of taxonomies (fandoms, tags, etc.) on large cards. However, you can apply filters and custom CSS to achieve the same thing.

**References**
* Filter: [fictioneer_filter_card_story_terms](FILTERS.md#apply_filters-fictioneer_filter_card_type_terms-terms-post-args-story_data-)
* Filter: [fictioneer_filter_bullet_separator](FILTERS.md#apply_filters-fictioneer_filter_bullet_separator-separator-context-)

```php
/**
 * Replaces the '_inline' style modifier CSS class with '_pill'
 *
 * @since x.x.x
 *
 * @param array $terms  Associative array of term HTML nodes to be rendered.
 *
 * @return array Updated array of term HTML nodes.
 */

function child_card_terms_as_pills( $terms ) {
  return array_map(
    function( $node ) {
      return str_replace( '_inline', '_pill', $node );
    },
    $terms
  );
}
add_filter( 'fictioneer_filter_card_story_terms', 'child_card_terms_as_pills' );

/**
 * Removes the dot separator from large story cards.
 *
 * @since x.x.x
 *
 * @param string $separator  The default separator.
 * @param string $context    The render context.
 *
 * @return string An empty string if the correct context or the original.
 */

function child_remove_dot_separator_on_story_cards( $separator, $context ) {
  if ( $context === 'story-card' ) {
    return '';
  }

  return $separator;
}
add_filter( 'fictioneer_filter_bullet_separator', 'child_remove_dot_separator_on_story_cards', 10, 2 );
```

Custom CSS:

```css
.card._story .cell-tax {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}
```

## Add landscape image to blog post index

The blog post index is a rather plain affair by design, but maybe you want to liven it up. You can change the title and meta row via filter or add completely new elements via actions, such as a landscape image on top. Keep in mind that you need to ensure a fitting image is always available.

**References**
* Action: [fictioneer_post_article_open](ACTIONS.md#do_action-fictioneer_post_article_open-post_id-args-)
* Partial: [_post.php](https://github.com/Tetrakern/fictioneer/blob/main/partials/_post.php)
* Template: [single-post.php](https://github.com/Tetrakern/fictioneer/blob/main/single-post.php)

```php
/**
 * Adds landscape thumbnail to blog posts on the index page
 *
 * @since x.x.x
 *
 * @param int   $post_id  The post ID.
 * @param array $args     Additional arguments.
 */

function child_add_landscape_post_image( $post_id, $args ) {
  // Check render context
  if ( ( $args['context'] ?? 0 ) !== 'loop' ) {
    return;
  }

  // Output thumbnail
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

Custom CSS:

```css
.post-landscape-image {
  display: block;
  margin-bottom: 0.75rem;
}

.post-landscape-image img {
  border-radius: var(--layout-border-radius-small);
  height: 8rem;
  width: 100%;
  object-fit: cover;
}

/* If you want to remove the horizontal line: */
.blog-posts .post:not(:first-child) {
  border-top: 0;
  padding-top: 0;
}
```
