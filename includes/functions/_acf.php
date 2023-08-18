<?php

// =============================================================================
// ACF THEME SETUP
// =============================================================================

/**
 * Add ACF plugin JSON endpoint
 *
 * @since Fictioneer 4.0
 * @link https://www.advancedcustomfields.com/resources/local-json/
 *
 * @param array $paths Default paths of the ACF plugin.
 */

function fictioneer_acf_loading_point( $paths ) {
  unset( $paths[0] );

  $paths[] = get_template_directory() . '/includes/acf/acf-json';

  return $paths;
}

if ( ! FICTIONEER_DISABLE_ACF_JSON_IMPORT ) {
  add_filter( 'acf/settings/load_json', 'fictioneer_acf_loading_point' );
}

// =============================================================================
// ACF THEME SETUP
// =============================================================================

/**
 * Update path to save ACF changes in JSONs (disabled outside development)
 *
 * @since Fictioneer 4.0
 * @link https://www.advancedcustomfields.com/resources/local-json/
 *
 * @param array $paths Default path of the ACF plugin.
 */

function fictioneer_acf_json_save_point( $path ) {
  $path = get_template_directory() . '/includes/acf/acf-json';
  return $path;
}
// add_filter('acf/settings/save_json', 'fictioneer_acf_json_save_point');

// =============================================================================
// LOAD ACF PLUGIN FROM THEME IF NOT INSTALLED (ADMIN ONLY)
// =============================================================================

if ( ! class_exists('acf') && ( is_admin() || FICTIONEER_ENABLE_FRONTEND_ACF ) ) {
  // Define path and URL to the ACF plugin.
  define( 'FICTIONEER_ACF_PATH', get_template_directory() . '/includes/acf/' );
  define( 'FICTIONEER_ACF_URL', get_template_directory_uri() . '/includes/acf/' );

  // Include the ACF plugin.
  include_once( FICTIONEER_ACF_PATH . 'acf.php' );

  // Customize the url setting to fix incorrect asset URLs.
  function fictioneer_acf_settings_url( $url ) {
    return FICTIONEER_ACF_URL;
  }
  add_filter( 'acf/settings/url', 'fictioneer_acf_settings_url' );

  // Automatic installs should not see the admin menu
  function fictioneer_acf_settings_show_admin( $show_admin ) {
    return false;
  }
  add_filter( 'acf/settings/show_admin', 'fictioneer_acf_settings_show_admin' );
}

// =============================================================================
// ONLY SHOW CHAPTERS THAT BELONG TO STORY (ACF)
// =============================================================================

/**
 * Only show chapters that belong to story
 *
 * @since Fictioneer 4.0
 *
 * @param array  $args     The query arguments.
 * @param string $paths    The queried field.
 * @param int    $post_id  The post ID.
 *
 * @return array Modified query arguments.
 */

function fictioneer_acf_filter_chapters( $args, $field, $post_id ) {
  // Limit to chapters set to this story
  $args['meta_query'] = array(
    array(
      'key' => 'fictioneer_chapter_story',
      'value' => $post_id
    )
  );

  // Order by date, descending, to see the newest on top
  $args['orderby'] = 'date';
  $args['order'] = 'desc';

  // Return
  return $args;
}

if ( FICTIONEER_FILTER_STORY_CHAPTERS ) {
  add_filter( 'acf/fields/relationship/query/name=fictioneer_story_chapters', 'fictioneer_acf_filter_chapters', 10, 3 );
}

// =============================================================================
// UPDATE POST FEATURED LIST RELATIONSHIP REGISTRY
// =============================================================================

/**
 * Update relationships for 'post' post types
 *
 * @since Fictioneer 5.0
 *
 * @param int $post_id The post ID.
 */

function fictioneer_update_post_relationships( $post_id ) {
  // Only posts...
  if ( get_post_type( $post_id ) != 'post' ) return;

  // Setup
  $registry = fictioneer_get_relationship_registry();
  $featured = fictioneer_get_field( 'fictioneer_post_featured', $post_id );

  // Update relationships
  $registry[ $post_id ] = [];

  if ( ! empty( $featured ) ) {
    foreach ( $featured as $featured_id ) {
      $registry[ $post_id ][ $featured_id ] = 'is_featured';

      if ( ! isset( $registry[ $featured_id ] ) ) $registry[ $featured_id ] = [];

      $registry[ $featured_id ][ $post_id ] = 'featured_by';
    }
  } else {
    $featured = [];
  }

  // Check for and remove outdated direct references
  foreach ( $registry as $key => $entry ) {
    // Skip if...
    if ( absint( $key ) < 1 || ! is_array( $entry ) || in_array( $key, $featured ) ) continue;

    // Unset if in array
    unset( $registry[ $key ][ $post_id ] );

    // Remove node if empty
    if ( empty( $registry[ $key ] ) ) unset( $registry[ $key ] );
  }

  // Update database
  fictioneer_save_relationship_registry( $registry );
}

if ( FICTIONEER_RELATIONSHIP_PURGE_ASSIST ) {
  add_action( 'acf/save_post', 'fictioneer_update_post_relationships', 100 );
}

// =============================================================================
// REMEMBER WHEN CHAPTERS OF STORIES HAVE BEEN MODIFIED
// =============================================================================

/**
 * Update story ACF field when associated chapter is updated
 *
 * The difference to the modified date is that this field is only updated when
 * the chapters list changes, not when a chapter or story is updated in general.
 *
 * @since 4.0
 * @link https://www.advancedcustomfields.com/resources/acf-update_value/
 *
 * @param mixed      $value    The field value.
 * @param int|string $post_id  The post ID where the value is saved.
 *
 * @return mixed The modified value.
 */

function fictioneer_remember_chapters_modified( $value, $post_id ) {
  $previous = fictioneer_get_field( 'fictioneer_story_chapters', $post_id );
  $previous = is_array( $previous ) ? $previous : [];
  $new = is_array( $value ) ? $value : [];

  if ( $previous !== $value ) {
    update_post_meta( $post_id, 'fictioneer_chapters_modified', current_time( 'mysql' ) );
  }

  if ( count( $previous ) < count( $new ) ) {
    update_post_meta( $post_id, 'fictioneer_chapters_added', current_time( 'mysql' ) );
  }

  return $value;
}
add_filter( 'acf/update_value/name=fictioneer_story_chapters', 'fictioneer_remember_chapters_modified', 10, 2 );

// =============================================================================
// LIMIT BLOG STORIES TO AUTHOR
// =============================================================================

/**
 * Limit blog stories to author
 *
 * @since Fictioneer 5.4.8
 *
 * @param array  $args     The query arguments.
 * @param string $paths    The queried field.
 * @param int    $post_id  The post ID.
 *
 * @return array Modified query arguments.
 */

function fictioneer_acf_scope_blog_posts( $args, $field, $post_id ) {
  if ( ! current_user_can( 'manage_options' ) ) {
    $args['author'] = get_post_field( 'post_author', $post_id );
  }

  return $args;
}
add_filter( 'acf/fields/post_object/query/name=fictioneer_post_story_blogs', 'fictioneer_acf_scope_blog_posts', 10, 3 );

// =============================================================================
// LIMIT STORY CHAPTERS TO AUTHOR
// =============================================================================

/**
 * Limit chapter stories to author
 *
 * @since Fictioneer 5.4.9
 *
 * @param array  $args     The query arguments.
 * @param string $paths    The queried field.
 * @param int    $post_id  The post ID.
 *
 * @return array Modified query arguments.
 */

function fictioneer_acf_scope_chapter_story( $args, $field, $post_id ) {
  if ( ! current_user_can( 'manage_options' ) ) {
    $args['author'] = get_post_field( 'post_author', $post_id );
  }

  return $args;
}

if ( get_option( 'fictioneer_limit_chapter_stories_by_author' ) ) {
  add_filter( 'acf/fields/post_object/query/name=fictioneer_chapter_story', 'fictioneer_acf_scope_chapter_story', 10, 3 );
}

// =============================================================================
// PREVENT ACF FIELD FROM BEING SAVED
// =============================================================================

/**
 * Prevents updating the value of an ACF field
 *
 * When an attempt is made to update an ACF field, this function returns the
 * current value of the field, effectively discarding the new one.
 *
 * @since 5.6.0
 *
 * @param mixed $value    The new value to be saved,
 * @param int   $post_id  The post ID.
 * @param array $field    The ACF field settings array.
 *
 * @return mixed The current value of the ACF field.
 */

function fictioneer_acf_prevent_value_update( $value, $post_id, $field ) {
  return get_field( $field['name'], $post_id );
}

// =============================================================================
// REDUCE TINYMCE TOOLBAR
// =============================================================================

/**
 * Reduce items in the TinyMCE toolbar
 *
 * @since 5.6.0
 *
 * @param array $toolbars  The toolbar configuration.
 *
 * @return array The modified toolbar configuration.
 */

function fictioneer_acf_reduce_wysiwyg( $toolbars ) {
  unset( $toolbars['Full'][1][0] ); // Formselect
  unset( $toolbars['Full'][1][10] ); // WP More
  unset( $toolbars['Full'][1][12] ); // Fullscreen
  unset( $toolbars['Full'][1][13] ); // WP Adv.

  return $toolbars;
}
add_filter( 'acf/fields/wysiwyg/toolbars', 'fictioneer_acf_reduce_wysiwyg' );

// =============================================================================
// APPEND NEW CHAPTERS TO STORY
// =============================================================================

/**
 * Append new chapters to story list
 *
 * @since Fictioneer 5.4.9
 *
 * @param int $post_id  The post ID.
 */

function fictioneer_append_chapter_to_story( $post_id ) {
  // Prevent miss-fire (REST_REQUEST undefined!)
  if (
    fictioneer_multi_save_guard( $post_id ) ||
    get_post_status( $post_id ) !== 'publish' ||
    get_post_type( $post_id ) !== 'fcn_chapter'
  ) {
    return;
  }

  // Only do this for newly published chapters (not more than 5 seconds ago),
  // which means this can be triggered again by resetting the publish date.
  $publishing_time = get_post_time( 'U', true, $post_id, true );
  $current_time = current_time( 'timestamp', true );

  if ( $current_time - $publishing_time > 5 ) {
    return;
  }

  // Setup
  $story_id = get_post_meta( $post_id, 'fictioneer_chapter_story', true );
  $story = get_post( $story_id );

  // Abort if story not found
  if ( empty( $story ) ) {
    return;
  }

  // Setup, continued
  $chapter_author_id = get_post_field( 'post_author', $post_id );
  $story_author_id = get_post_field( 'post_author', $story_id );

  // Abort if the author IDs do not match unless it's an administrator
  if ( ! current_user_can( 'manage_options' ) && $chapter_author_id != $story_author_id ) {
    return;
  }

  // Get current story chapters
  $story_chapters = get_post_meta( $story_id, 'fictioneer_story_chapters', true );

  // Prepare chapter array if null (or broken)
  if ( ! is_array( $story_chapters ) ) {
    $story_chapters = [];
  }

  // Append chapter (if not already included) and save to database
  if ( ! in_array( $post_id, $story_chapters ) ) {
    $story_chapters[] = $post_id;

    // Append chapter to field
    update_post_meta( $story_id, 'fictioneer_story_chapters', array_unique( $story_chapters ) );

    // Remember when chapter list has been last updated
    update_post_meta( $story_id, 'fictioneer_chapters_modified', current_time( 'mysql' ) );
    update_post_meta( $story_id, 'fictioneer_chapters_added', current_time( 'mysql' ) );

    // Clear story data cache to ensure it gets refreshed
    delete_post_meta( $story_id, 'fictioneer_story_data_collection' );
  } else {
    return;
  }

  // Update story post to fire associated actions
  wp_update_post( array( 'ID' => $story_id ) );
}

if ( get_option( 'fictioneer_enable_chapter_appending' ) ) {
  // WP save_post may not have the field yet!
  add_action( 'acf/save_post', 'fictioneer_append_chapter_to_story', 99 );
}

?>
