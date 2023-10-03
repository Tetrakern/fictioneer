<?php

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
// ACF THEME SETUP
// =============================================================================

// Add ACF groups and fields...
add_action( 'acf/include_fields', function() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

  acf_add_local_field_group(
    array(
      'key' => 'group_5ed437d6b421f',
      'title' => 'Featured Content',
      'fields' => array(
        array(
          'key' => 'field_5ed4382ba70b2',
          'label' => 'Featured',
          'name' => 'fictioneer_post_featured',
          'aria-label' => '',
          'type' => 'relationship',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'post_type' => array(
            0 => 'fcn_story',
            1 => 'fcn_chapter',
            2 => 'fcn_collection',
            3 => 'fcn_recommendation',
            4 => 'post',
          ),
          'post_status' => 'publish',
          'taxonomy' => '',
          'filters' => array(
            0 => 'search',
            1 => 'post_type',
            2 => 'taxonomy',
          ),
          'return_format' => 'id',
          'min' => '',
          'max' => '',
          'elements' => '',
          'bidirectional' => 0,
          'bidirectional_target' => array(
          ),
        ),
      ),
      'location' => array(
        array(
          array(
            'param' => 'post_type',
            'operator' => '==',
            'value' => 'post',
          ),
        ),
      ),
      'menu_order' => 0,
      'position' => 'normal',
      'style' => 'default',
      'label_placement' => 'top',
      'instruction_placement' => 'label',
      'hide_on_screen' => '',
      'active' => false,
      'description' => '',
      'show_in_rest' => 0
    )
  );

	acf_add_local_field_group(
    array(
      'key' => 'group_5d6303c8575d8',
      'title' => 'Story Data',
      'fields' => array(
        array(
          'key' => 'field_5d6304e4330fd',
          'label' => 'Chapters',
          'name' => 'fictioneer_story_chapters',
          'aria-label' => '',
          'type' => 'relationship',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'post_type' => array(
            0 => 'fcn_chapter',
          ),
          'post_status' => '',
          'taxonomy' => '',
          'filters' => array(
            0 => 'search',
          ),
          'return_format' => 'id',
          'min' => '',
          'max' => -1,
          'elements' => '',
          'bidirectional' => 0,
          'bidirectional_target' => array(
          ),
        ),
        array(
          'key' => 'field_5d6e33e253add',
          'label' => 'Custom Pages',
          'name' => 'fictioneer_story_custom_pages',
          'aria-label' => '',
          'type' => 'relationship',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'post_type' => array(
            0 => 'page',
          ),
          'post_status' => 'publish',
          'taxonomy' => '',
          'filters' => array(
            0 => 'search',
          ),
          'return_format' => 'id',
          'min' => '',
          'max' => 6,
          'elements' => '',
          'bidirectional' => 0,
          'bidirectional_target' => array(
          ),
        ),
      ),
      'location' => array(
        array(
          array(
            'param' => 'post_type',
            'operator' => '==',
            'value' => 'fcn_story',
          ),
        ),
      ),
      'menu_order' => 4,
      'position' => 'normal',
      'style' => 'default',
      'label_placement' => 'top',
      'instruction_placement' => 'field',
      'hide_on_screen' => '',
      'active' => false,
      'description' => '',
      'show_in_rest' => 0,
    )
  );

	acf_add_local_field_group(
    array(
      'key' => 'group_60edb914ba16c',
      'title' => 'Story ePUB',
      'fields' => array(
        array(
          'key' => 'field_62eb89244fb11',
          'label' => 'Upload Ebook',
          'name' => 'fictioneer_story_ebook_upload_one',
          'aria-label' => '',
          'type' => 'file',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'return_format' => 'id',
          'library' => 'uploadedTo',
          'min_size' => '',
          'max_size' => '',
          'mime_types' => 'epub, mobi, ibooks, azw, azw3, kf8, kfx, pdf, iba, txt, rtf',
        ),
      ),
      'location' => array(
        array(
          array(
            'param' => 'post_type',
            'operator' => '==',
            'value' => 'fcn_story',
          ),
        ),
      ),
      'menu_order' => 5,
      'position' => 'normal',
      'style' => 'default',
      'label_placement' => 'top',
      'instruction_placement' => 'label',
      'hide_on_screen' => '',
      'active' => false,
      'description' => '',
      'show_in_rest' => 0,
    )
  );

  acf_add_local_field_group(
    array(
      'key' => 'group_619fc7566ad8b',
      'title' => 'Collections Data',
      'fields' => array(
        array(
          'key' => 'field_619fc7732b611',
          'label' => 'Collection Items',
          'name' => 'fictioneer_collection_items',
          'aria-label' => '',
          'type' => 'relationship',
          'instructions' => '',
          'required' => 1,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'post_type' => array(
            0 => 'fcn_story',
            1 => 'fcn_chapter',
            2 => 'fcn_recommendation',
            3 => 'fcn_collection',
            4 => 'post',
            5 => 'page',
          ),
          'post_status' => 'publish',
          'taxonomy' => '',
          'filters' => array(
            0 => 'search',
            1 => 'post_type',
            2 => 'taxonomy',
          ),
          'return_format' => 'id',
          'min' => 1,
          'max' => '',
          'elements' => '',
          'bidirectional_target' => array(
          ),
        ),
      ),
      'location' => array(
        array(
          array(
            'param' => 'post_type',
            'operator' => '==',
            'value' => 'fcn_collection',
          ),
        ),
      ),
      'menu_order' => 0,
      'position' => 'normal',
      'style' => 'default',
      'label_placement' => 'top',
      'instruction_placement' => 'label',
      'hide_on_screen' => '',
      'active' => false,
      'description' => '',
      'show_in_rest' => 0,
    )
  );
});

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
  if ( FICTIONEER_FILTER_STORY_CHAPTERS ) {
    $args['meta_query'] = array(
      array(
        'key' => 'fictioneer_chapter_story',
        'value' => $post_id
      )
    );
  }

  // Order by date, descending, to see the newest on top
  $args['orderby'] = 'date';
  $args['order'] = 'desc';

  // Return
  return $args;
}
add_filter( 'acf/fields/relationship/query/name=fictioneer_story_chapters', 'fictioneer_acf_filter_chapters', 10, 3 );

// =============================================================================
// FILTER POSSIBLE COLLECTION ITEMS
// =============================================================================

/**
 * Filter possible collection items
 *
 * @since Fictioneer 5.7.4
 *
 * @param array  $args     The query arguments.
 * @param string $paths    The queried field.
 * @param int    $post_id  The post ID.
 *
 * @return array Modified query arguments.
 */

function fictioneer_acf_filter_collection_items( $args, $field, $post_id ) {
  $forbidden = array(
    get_option( 'fictioneer_user_profile_page', 0 ),
    get_option( 'fictioneer_bookmarks_page', 0 ),
    get_option( 'fictioneer_stories_page', 0 ),
    get_option( 'fictioneer_chapters_page', 0 ),
    get_option( 'fictioneer_recommendations_page', 0 ),
    get_option( 'fictioneer_collections_page', 0 ),
    get_option( 'fictioneer_bookshelf_page', 0 ),
    get_option( 'fictioneer_404_page', 0 ),
    get_option( 'page_on_front', 0 ),
    get_option( 'page_for_posts', 0 )
  );

  // Exclude post IDs
  $args['post__not_in'] = array_map( 'strval', $forbidden );

  // Return
  return $args;
}
add_filter( 'acf/fields/relationship/query/name=fictioneer_collection_items', 'fictioneer_acf_filter_collection_items', 10, 3 );

// =============================================================================
// UPDATE POST FEATURED LIST RELATIONSHIP REGISTRY
// =============================================================================

/**
 * Update relationships for 'post' post types
 *
 * @since Fictioneer 5.0
 *
 * @param int $post_id  The post ID.
 */

function fictioneer_update_post_relationships( $post_id ) {
  // Only posts...
  if ( get_post_type( $post_id ) != 'post' ) {
    return;
  }

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
    if ( absint( $key ) < 1 || ! is_array( $entry ) || in_array( $key, $featured ) ) {
      continue;
    }

    // Unset if in array
    unset( $registry[ $key ][ $post_id ] );

    // Remove node if empty
    if ( empty( $registry[ $key ] ) ) {
      unset( $registry[ $key ] );
    }
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
// LIMIT STORY PAGES TO AUTHOR
// =============================================================================

/**
 * Restrict story pages to author
 *
 * @since Fictioneer 5.6.3
 *
 * @param array      $args     The query arguments.
 * @param array      $field    The queried field.
 * @param int|string $post_id  The post ID.
 *
 * @return array Modified query arguments.
 */

function fictioneer_acf_scope_story_pages( $args, $field, $post_id ) {
  $args['author'] = get_post_field( 'post_author', $post_id );

  return $args;
}
add_filter( 'acf/fields/relationship/query/name=fictioneer_story_custom_pages', 'fictioneer_acf_scope_story_pages', 10, 3 );

?>
