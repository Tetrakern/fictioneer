# Customization Snippets

This is a collection of code snippets for previously solved customization issues. They are supposed to go into your child theme’s **functions.php** or a subsequently included file (replace "child_" and "x.x.x" with your child theme prefix and version respectively). Putting them into the main theme will cause them to be deleted when you update. Also assume each snippet requires the latest version of Fictioneer.

For general information on theme customization, please refer to the [installation guide](https://github.com/Tetrakern/fictioneer/blob/main/INSTALLATION.md#how-to-customize-the-fictioneer-theme).

## How to add a secondary chapter title?

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

## How to only show a specific advanced meta field?

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
 * Adds Co-Authors to fields to be saved
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
