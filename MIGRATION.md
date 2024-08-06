# Migration

You can migrate an existing database through several means. The easiest, albeit most frustrating, method is to do it manually post by post, but let’s not entertain that idea here. Your approach will largely depend on what your data source looks like; specifically, whether you are a single author with one story, multiple stories, or multiple authors with multiple stories. The last scenario is the most challenging, aside from migrating from another CMS. Below are different methods you can try.

**IMPORTANT! ALWAYS MAKE A DATABASE BACKUP BEFORE YOU DO ANYTHING!**

Add the following script to your `functions.php` file, for example via the **Theme File Editor** under **Appearance**. This serves as the base for the described migration methods and as a template if you want to write your own script. Make sure to remove the code once you are done; you do not want to leave this in a production environment. You can find a list of all story and chapter meta fields in the [development guide](DEVELOPMENT.md#story-meta-fields).

```php
function fictioneer_migrate_chapters( $query_args = [], $story_args = [], $preview = true ) {
  // Security check (only allow admins to execute script)
  if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( 'Insufficient permission.' );
  }

  // Story data
  $story_id = $story_args['story_id'] ?? null;
  $story_age_rating = $story_args['age_rating'] ?? 'Everyone';
  $story_status = $story_args['status'] ?? 'Ongoing';
  $story_short_description = $story_args['description'] ?? null;
  $story_topwebfiction_link = $story_args['topwebfiction'] ?? null;

  // Query posts
  $query_args = array_merge(
    array(
      'post_type' => 'post',
      'posts_per_page' => -1,
      'post_status' => 'any',
      'orderby' => 'date',
      'order' => 'asc'
    ),
    $query_args
  );

  $query = new WP_Query( $query_args );

  // Check story
  if ( $story_id ) {
    $story = get_post( $story_id );

    if ( ! $story ) {
      wp_die( 'Story not found, please check the ID.' );
    }

    if ( $story->post_type !== 'fcn_story' ) {
      wp_die( 'Story ID does not belong to a story, please check the ID.' );
    }
  }

  // Found any?
  if ( ! $query->have_posts() ) {
    wp_die( 'No matching posts found.' );
  }

  // Preview?
  if ( ! ( $_GET['perform'] ?? 0 ) && $preview ) {
    $perform_link = esc_url(
      add_query_arg(
        array(
          'action' => 'fictioneer_migrate_chapters',
          'perform' => 1
        ),
        admin_url( 'admin-post.php' )
      )
    );

    $preview = '<style>:is(th, td){text-align:left;padding:8px 24px 8px 0;}</style>';
    $preview .= '<h1>Migration Preview</h1>';
    $preview .= "<p>The following <strong>{$query->found_posts} posts</strong> will be migrated by the script. <a href='{$perform_link}'>Start migration!</a></p>";

    if ( $story_id && $story ) {
      $preview .= "<p><strong>Target Story:</strong> {$story->post_title} (ID: {$story_id})</p>";
    }

    $preview .= '<table><thead><tr><th>ID</th><th>Date</th><th>Title</th></tr></thead><tbody>';

    foreach ( $query->posts as $post ) {
      $preview .= "<tr><td>{$post->ID}</td><td>{$post->post_date}</td><td>{$post->post_title}</td></tr>";
    }

    $preview .= '</tbody></table>';

    wp_die( $preview );
  }

  // Migrate chapters!
  $story_chapters = [];
  $latest_gmt_date = null;

  foreach ( $query->posts as $post ) {
    // If there is a target story...
    if ( $story_id && $story ) {
      // Add valid chapters to story chapters array
      if ( in_array( $post->post_status, ['publish', 'private', 'future'] ) ) {
        $story_chapters[] = $post->ID;

        // Remember the most recent chapter publish date
        if ( $latest_gmt_date === null || $post->post_date_gmt > $latest_gmt_date ) {
          $latest_gmt_date = $post->post_date_gmt;
        }
      }

      // Associate chapter with story
      update_post_meta( $post->ID, 'fictioneer_chapter_story', $story_id );
    }

    // Set default chapter icon
    update_post_meta( $post->ID, 'fictioneer_chapter_icon', 'fa-solid fa-book' );

    // Update other meta
    update_post_meta( $post->ID, 'fictioneer_first_publish_date', $post->post_date_gmt );

    // Change post type to fcn_chapter (also triggers hooked actions and updates story)
    wp_update_post(
      array(
        'ID' => $post->ID,
        'post_type' => 'fcn_chapter'
      )
    );
  }

  // Update story!
  if ( $story_chapters ) {
    // Do not forget to include current chapters
    $story_chapters = array_merge( fictioneer_get_story_chapter_ids( $story_id ), $story_chapters );

    // Append chapters in order of publish date
    update_post_meta( $story_id, 'fictioneer_story_chapters', array_map( 'strval', $story_chapters ) );

    // Update related dates
    update_post_meta( $story_id, 'fictioneer_chapters_modified', $latest_gmt_date );
    update_post_meta( $story_id, 'fictioneer_chapters_added', $latest_gmt_date );

    // Update other meta
    update_post_meta( $story_id, 'fictioneer_story_rating', $story_age_rating );
    update_post_meta( $story_id, 'fictioneer_story_status', $story_status );

    if ( $story_short_description ) {
      update_post_meta( $story_id, 'fictioneer_story_short_description', $story_short_description );
    }

    if ( $story_topwebfiction_link ) {
      update_post_meta( $story_id, 'fictioneer_story_topwebfiction_link', $story_topwebfiction_link );
    }

    // Clear meta caches to ensure they get refreshed
    delete_post_meta( $story_id, 'fictioneer_story_data_collection' );
    delete_post_meta( $story_id, 'fictioneer_story_chapter_index_html' );

    // Update story post (triggers hooked actions)
    wp_update_post(
      array(
        'ID' => $story_id
      )
    );
  }
}
```

## Simple Migration

Add the following script to your `functions.php` file. This script will query ALL posts and convert them to chapters, optionally appending them to a prepared story post. You can customize the parameters if needed. The action can be called by opening `/wp-admin/admin-post.php?action=fictioneer_migrate` on your site. You will first get a preview of which chapters are about to be migrated; if you are happy with the preview, click the "Start migration!" link.

```php
function fictioneer_migrate() {
  // Story data if you want to append the chapters
  $story_args = array(
    'story_id' => null, // Leave null if you only want to convert chapters
    'age_rating' => 'Everyone', // Chose: 'Everyone', 'Teen', 'Mature', or 'Adult' (capitalized and in English)
    'status' => 'Ongoing', // Chose: 'Ongoing', 'Completed', 'Oneshot', 'Hiatus', or 'Cancelled' (capitalized and in English)
    'description' => null, // Used on cards; falls back to excerpt
    'topwebfiction' => null, // Enter full URL here if any
  );

  // Chapter query args (see https://developer.wordpress.org/reference/classes/wp_query/#parameters)
  $query_args = array(
    'post_type' => 'post', // Either post, page, or custom post type
    'posts_per_page' => -1, // Get all posts that match the query
    'post_status' => 'any', // Convert all posts regardless of status
    'orderby' => 'date', // Affects the chapter order
    'order' => 'asc', // Affects the chapter order
    // 'category_name' => 'story-category',
    // 'tag' => 'telling-tag',
    // 'author' => 123,
    // 'tax_query' => array(
    //   array(
    //     'taxonomy' => 'book',
    //     'field' => 'slug',
    //     'terms' => 'book-name',
    //   ),
    // ),
    // 's' => 'search keyword',
  );

  // Calls the migration function with customized parameters;
  // set the third parameter to false if you want or need to
  // skip the preview (if you have too many posts, etc.)
  fictioneer_migrate_chapters( $query_args, $story_args, true );

  // Finish
  wp_die( 'Done' );
}
add_action( 'admin_post_fictioneer_migrate', 'fictioneer_migrate' );
```

## Migrate by Author(s)

Add the following script to your `functions.php` file. Unlike the simple approach, this script will loop over a predefined set of author data (created by you) to migrate specific posts to specific stories. In this case, a category is used as a discriminator, but other attributes can work too. The action can be called by opening `/wp-admin/admin-post.php?action=fictioneer_migrate_by_author` on your site. Note that you will NOT get a preview; the migration happens immediately!

```php
function fictioneer_migrate_by_author() {
  // Set individually by author
  $story_args = [];

  // Chapter query args (see https://developer.wordpress.org/reference/classes/wp_query/#parameters)
  $query_args = array(
    'post_type' => 'post', // Either post, page, or custom post type
    'posts_per_page' => -1, // Get all posts that match the query
    'post_status' => 'any', // Convert all posts regardless of status
    'orderby' => 'date', // Affects the chapter order
    'order' => 'asc' // Affects the chapter order
  );

  // Author IDs, category names, and story IDs (your responsibility to prepare this data)
  $authors = array(
    array( 'id' => 1, 'category_name' => 'author-first-story-category', 'story_id' => 1 ),
    array( 'id' => 1, 'category_name' => 'author-second-story-category', 'story_id' => 42 ),
    array( 'id' => 2, 'category_name' => 'other-author-first-story-category', 'story_id' => 69 ),
    // etc.
  );

  // Calls the migration function for each author with custom parameters
  foreach ( $authors as $author_data ) {
    // Customize query args for chapter
    $query_args['author'] = $author_data['id'];
    $query_args['category_name'] = $author_data['category_name'];

    // Customize story data
    $story_args['story_id'] = $author_data['story_id'];
    // You can also set age_rating, status, etc. if you have a data source for that

    // Call without preview since that stops the script on the first author
    fictioneer_migrate_chapters( $query_args, $story_args, false );
  }

  // Finish
  wp_die( 'Done' );
}
add_action( 'admin_post_fictioneer_migrate_by_author', 'fictioneer_migrate_by_author' );
```
