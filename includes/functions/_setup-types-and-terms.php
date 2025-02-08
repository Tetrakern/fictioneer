<?php

// =============================================================================
// CUSTOM CAPABILITIES (DO NOT CHANGE ORDER!)
// =============================================================================

define(
  'FICTIONEER_WP_CAPABILITIES',
  array(
    'edit_post',
    'read_post',
    'delete_post',
    'edit_posts',
    'edit_others_posts',
    'publish_posts',
    'read_private_posts',
    'delete_posts',
    'delete_private_posts',
    'delete_published_posts',
    'delete_others_posts',
    'edit_private_posts',
    'edit_published_posts'
  )
);

define(
  'FICTIONEER_STORY_CAPABILITIES',
  array(
    'edit_fcn_story',
    'read_fcn_story',
    'delete_fcn_story',
    'edit_fcn_stories',
    'edit_others_fcn_stories',
    'publish_fcn_stories',
    'read_private_fcn_stories',
    'delete_fcn_stories',
    'delete_private_fcn_stories',
    'delete_published_fcn_stories',
    'delete_others_fcn_stories',
    'edit_private_fcn_stories',
    'edit_published_fcn_stories'
  )
);

define(
  'FICTIONEER_CHAPTER_CAPABILITIES',
  array(
    'edit_fcn_chapter',
    'read_fcn_chapter',
    'delete_fcn_chapter',
    'edit_fcn_chapters',
    'edit_others_fcn_chapters',
    'publish_fcn_chapters',
    'read_private_fcn_chapters',
    'delete_fcn_chapters',
    'delete_private_fcn_chapters',
    'delete_published_fcn_chapters',
    'delete_others_fcn_chapters',
    'edit_private_fcn_chapters',
    'edit_published_fcn_chapters'
  )
);

define(
  'FICTIONEER_COLLECTION_CAPABILITIES',
  array(
    'edit_fcn_collection',
    'read_fcn_collection',
    'delete_fcn_collection',
    'edit_fcn_collections',
    'edit_others_fcn_collections',
    'publish_fcn_collections',
    'read_private_fcn_collections',
    'delete_fcn_collections',
    'delete_private_fcn_collections',
    'delete_published_fcn_collections',
    'delete_others_fcn_collections',
    'edit_private_fcn_collections',
    'edit_published_fcn_collections'
  )
);

define(
  'FICTIONEER_RECOMMENDATION_CAPABILITIES',
  array(
    'edit_fcn_recommendation',
    'read_fcn_recommendation',
    'delete_fcn_recommendation',
    'edit_fcn_recommendations',
    'edit_others_fcn_recommendations',
    'publish_fcn_recommendations',
    'read_private_fcn_recommendations',
    'delete_fcn_recommendations',
    'delete_private_fcn_recommendations',
    'delete_published_fcn_recommendations',
    'delete_others_fcn_recommendations',
    'edit_private_fcn_recommendations',
    'edit_published_fcn_recommendations'
  )
);

// =============================================================================
// CUSTOM POST TYPE: FCN_STORY
// =============================================================================

/**
 * Register custom post type fcn_story
 *
 * @since 1.0
 */

function fictioneer_fcn_story_post_type() {
  $labels = array(
    'name'                  => __( 'Stories', 'fictioneer' ),
    'singular_name'         => __( 'Story', 'fictioneer' ),
    'archives'              => __( 'Story Archives', 'fictioneer' ),
    'attributes'            => __( 'Story Attributes', 'fictioneer' ),
    'all_items'             => __( 'All Stories', 'fictioneer' ),
    'add_new_item'          => __( 'Add New Story', 'fictioneer' ),
    'new_item'              => __( 'New Story', 'fictioneer' ),
    'edit_item'             => __( 'Edit Story', 'fictioneer' ),
    'update_item'           => __( 'Update Story', 'fictioneer' ),
    'view_item'             => __( 'View Story', 'fictioneer' ),
    'view_items'            => __( 'View Stories', 'fictioneer' ),
    'search_items'          => __( 'Search Stories', 'fictioneer' ),
    'not_found'             => __( 'No stories found', 'fictioneer' ),
    'not_found_in_trash'    => __( 'No stories found in Trash', 'fictioneer' ),
    'featured_image'        => __( 'Story Cover Image', 'fictioneer' ),
    'set_featured_image'    => __( 'Set cover image', 'fictioneer' ),
    'remove_featured_image' => __( 'Remove cover image', 'fictioneer' ),
    'use_featured_image'    => __( 'Use as cover image', 'fictioneer' ),
    'insert_into_item'      => __( 'Insert into story', 'fictioneer' ),
    'uploaded_to_this_item' => __( 'Uploaded to this story', 'fictioneer' ),
    'items_list'            => __( 'Stories list', 'fictioneer' ),
    'items_list_navigation' => __( 'Stories list navigation', 'fictioneer' ),
    'filter_items_list'     => __( 'Filter stories list', 'fictioneer' ),
  );

  $args = array(
    'label'               => __( 'Story', 'fictioneer' ),
    'labels'              => $labels,
    'menu_icon'           => 'dashicons-book',
    'supports'            => ['title', 'author', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields'],
    'taxonomies'          => ['category', 'post_tag', 'fcn_fandom', 'fcn_character', 'fcn_genre', 'fcn_content_warning'],
    'hierarchical'        => false,
    'public'              => true,
    'rewrite'             => array( 'slug' => 'story' ),
    'show_in_rest'        => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'menu_position'       => 5,
    'show_in_admin_bar'   => true,
    'show_in_nav_menus'   => true,
    'can_export'          => true,
    'has_archive'         => false,
    'exclude_from_search' => false,
    'publicly_queryable'  => true,
    'capability_type'     => ['fcn_story', 'fcn_stories'],
    'capabilities'        => array_combine( FICTIONEER_WP_CAPABILITIES, FICTIONEER_STORY_CAPABILITIES ),
    'map_meta_cap'        => true
  );

  register_post_type( 'fcn_story', $args );
}
add_action( 'init', 'fictioneer_fcn_story_post_type', 0 );

// =============================================================================
// CUSTOM POST TYPE: FCN_CHAPTER
// =============================================================================

/**
 * Register custom post type fcn_chapter
 *
 * @since 1.0
 */

function fictioneer_fcn_chapter_post_type() {
  $labels = array(
    'name'                  => __( 'Chapters', 'fictioneer' ),
    'singular_name'         => __( 'Chapter', 'fictioneer' ),
    'archives'              => __( 'Chapter Archives', 'fictioneer' ),
    'attributes'            => __( 'Chapter Attributes', 'fictioneer' ),
    'all_items'             => __( 'All Chapters', 'fictioneer' ),
    'add_new_item'          => __( 'Add New Chapter', 'fictioneer' ),
    'new_item'              => __( 'New Chapter', 'fictioneer' ),
    'edit_item'             => __( 'Edit Chapter', 'fictioneer' ),
    'update_item'           => __( 'Update Chapter', 'fictioneer' ),
    'view_item'             => __( 'View Chapter', 'fictioneer' ),
    'view_items'            => __( 'View Chapters', 'fictioneer' ),
    'search_items'          => __( 'Search Chapters', 'fictioneer' ),
    'not_found'             => __( 'No chapters found', 'fictioneer' ),
    'not_found_in_trash'    => __( 'No chapters found in Trash', 'fictioneer' ),
    'featured_image'        => __( 'Chapter Cover Image', 'fictioneer' ),
    'set_featured_image'    => __( 'Set cover image', 'fictioneer' ),
    'remove_featured_image' => __( 'Remove cover image', 'fictioneer' ),
    'use_featured_image'    => __( 'Use as cover image', 'fictioneer' ),
    'insert_into_item'      => __( 'Insert into chapter', 'fictioneer' ),
    'uploaded_to_this_item' => __( 'Uploaded to this chapter', 'fictioneer' ),
    'items_list'            => __( 'Chapters list', 'fictioneer' ),
    'items_list_navigation' => __( 'Chapters list navigation', 'fictioneer' ),
    'filter_items_list'     => __( 'Filter chapters list', 'fictioneer' ),
  );

  $args = array(
    'label'               => __( 'Chapter', 'fictioneer' ),
    'labels'              => $labels,
    'menu_icon'           => 'dashicons-text-page',
    'supports'            => ['title', 'author', 'editor', 'excerpt', 'thumbnail', 'comments', 'revisions', 'custom-fields'],
    'taxonomies'          => ['category', 'post_tag', 'fcn_fandom', 'fcn_character', 'fcn_genre', 'fcn_content_warning'],
    'hierarchical'        => false, // Does not work without further adjustments
    'public'              => true,
    'rewrite'             => array( 'slug' => 'chapter' ),
    'show_in_rest'        => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'menu_position'       => 6,
    'show_in_admin_bar'   => true,
    'show_in_nav_menus'   => true,
    'can_export'          => true,
    'has_archive'         => false,
    'exclude_from_search' => false,
    'publicly_queryable'  => true,
    'capability_type'     => ['fcn_chapter', 'fcn_chapters'],
    'capabilities'        => array_combine( FICTIONEER_WP_CAPABILITIES, FICTIONEER_CHAPTER_CAPABILITIES ),
    'map_meta_cap'        => true
  );

  if ( get_option( 'fictioneer_rewrite_chapter_permalinks' ) ) {
    $args['rewrite'] = array( 'slug' => 'story/%story_slug%', 'with_front' => false );
  }

  register_post_type( 'fcn_chapter', $args );
}
add_action( 'init', 'fictioneer_fcn_chapter_post_type', 0 );

/**
 * Adds chapter rewrite tags
 *
 * @since 5.19.1
 */

function fictioneer_add_chapter_rewrite_tags() {
  add_rewrite_tag( '%story_slug%', '([^/]+)', 'story_slug=' );
}

if ( get_option( 'fictioneer_rewrite_chapter_permalinks' ) ) {
  add_action( 'init', 'fictioneer_add_chapter_rewrite_tags' );
}

/**
 * Adds chapter rewrite rules
 *
 * @since 5.19.1
 */

function fictioneer_add_chapter_rewrite_rules() {
  // Rule for chapters with story
  add_rewrite_rule(
    '^story/([^/]+)/([^/]+)/?$',
    'index.php?post_type=fcn_chapter&name=$matches[2]',
    'top'
  );

  // Rule for standalone chapters
  add_rewrite_rule(
    '^chapter/([^/]+)/?$',
    'index.php?post_type=fcn_chapter&name=$matches[1]',
    'top'
  );
}

if ( get_option( 'fictioneer_rewrite_chapter_permalinks' ) ) {
  add_action( 'init', 'fictioneer_add_chapter_rewrite_rules' );
}

/**
 * Adds chapter rewrite rules
 *
 * @since 5.19.1
 *
 * @param string  $post_link  The post’s permalink.
 * @param WP_Post $post       The post in question.
 *
 * @return string The modified permalink.
 */

function fictioneer_rewrite_chapter_permalink( $post_link, $post ) {
  // Only for chapters...
  if ( $post->post_type === 'fcn_chapter' ) {
    $story_id = fictioneer_get_chapter_story_id( $post->ID );

    if ( $story_id ) {
      // Chapter with story
      $post_link = str_replace( '%story_slug%', get_post_field( 'post_name', $story_id ), $post_link );
    } else {
      // Standalone chapter
      $post_link = str_replace( 'story/%story_slug%', 'chapter', $post_link );
    }
  }

  // Continue filter
  return $post_link;
}

if ( get_option( 'fictioneer_rewrite_chapter_permalinks' ) ) {
  add_filter( 'post_type_link', 'fictioneer_rewrite_chapter_permalink', 10, 2 );
}

// =============================================================================
// CUSTOM POST TYPE: FCN_COLLECTION
// =============================================================================

/**
 * Register custom post type fcn_collection
 *
 * @since 4.0.0
 */

function fictioneer_fcn_collection_post_type() {
  $labels = array(
    'name'                  => __( 'Collections', 'fictioneer' ),
    'singular_name'         => __( 'Collection', 'fictioneer' ),
    'archives'              => __( 'Collection Archives', 'fictioneer' ),
    'attributes'            => __( 'Collection Attributes', 'fictioneer' ),
    'all_items'             => __( 'All Collections', 'fictioneer' ),
    'add_new_item'          => __( 'Add New Collection', 'fictioneer' ),
    'new_item'              => __( 'New Collection', 'fictioneer' ),
    'edit_item'             => __( 'Edit Collection', 'fictioneer' ),
    'update_item'           => __( 'Update Collection', 'fictioneer' ),
    'view_item'             => __( 'View Collection', 'fictioneer' ),
    'view_items'            => __( 'View Collections', 'fictioneer' ),
    'search_items'          => __( 'Search Collections', 'fictioneer' ),
    'not_found'             => __( 'No collections found', 'fictioneer' ),
    'not_found_in_trash'    => __( 'No collections found in Trash', 'fictioneer' ),
    'featured_image'        => __( 'Collection Cover Image', 'fictioneer' ),
    'set_featured_image'    => __( 'Set cover image', 'fictioneer' ),
    'remove_featured_image' => __( 'Remove cover image', 'fictioneer' ),
    'use_featured_image'    => __( 'Use as cover image', 'fictioneer' ),
    'insert_into_item'      => __( 'Insert into collection', 'fictioneer' ),
    'uploaded_to_this_item' => __( 'Uploaded to this collection', 'fictioneer' ),
    'items_list'            => __( 'Collections list', 'fictioneer' ),
    'items_list_navigation' => __( 'Collections list navigation', 'fictioneer' ),
    'filter_items_list'     => __( 'Filter collections list', 'fictioneer' ),
  );

  $args = array(
    'label'               => __( 'Collection', 'fictioneer' ),
    'labels'              => $labels,
    'menu_icon'           => 'dashicons-category',
    'supports'            => ['title', 'author', 'editor', 'thumbnail', 'custom-fields'],
    'taxonomies'          => ['category', 'post_tag', 'fcn_fandom', 'fcn_character', 'fcn_genre', 'fcn_content_warning'],
    'hierarchical'        => false,
    'public'              => true,
    'rewrite'             => array( 'slug' => 'collection' ),
    'show_in_rest'        => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'menu_position'       => 7,
    'show_in_admin_bar'   => true,
    'show_in_nav_menus'   => true,
    'can_export'          => true,
    'has_archive'         => false,
    'exclude_from_search' => false,
    'publicly_queryable'  => true,
    'capability_type'     => ['fcn_collection', 'fcn_collections'],
    'capabilities'        => array_combine( FICTIONEER_WP_CAPABILITIES, FICTIONEER_COLLECTION_CAPABILITIES ),
    'map_meta_cap'        => true
  );

  register_post_type( 'fcn_collection', $args );
}
add_action( 'init', 'fictioneer_fcn_collection_post_type', 0 );

// =============================================================================
// CUSTOM POST TYPE: FCN_RECOMMENDATION
// =============================================================================

/**
 * Register custom post type fcn_recommendation
 *
 * @since 3.0
 */

function fictioneer_fcn_recommendation_post_type() {
  $labels = array(
    'name'                  => __( 'Recommendations', 'fictioneer' ),
    'singular_name'         => __( 'Recommendation', 'fictioneer' ),
    'menu_name'             => __( 'Recommend.', 'fictioneer' ),
    'archives'              => __( 'Recommendation Archives', 'fictioneer' ),
    'attributes'            => __( 'Recommendation Attributes', 'fictioneer' ),
    'all_items'             => __( 'All Recommend.', 'fictioneer' ),
    'add_new_item'          => __( 'Add New Recommendation', 'fictioneer' ),
    'new_item'              => __( 'New Recommendation', 'fictioneer' ),
    'edit_item'             => __( 'Edit Recommendation', 'fictioneer' ),
    'update_item'           => __( 'Update Recommendation', 'fictioneer' ),
    'view_item'             => __( 'View Recommendation', 'fictioneer' ),
    'view_items'            => __( 'View Recommendations', 'fictioneer' ),
    'search_items'          => __( 'Search Recommendations', 'fictioneer' ),
    'not_found'             => __( 'No recommendations found', 'fictioneer' ),
    'not_found_in_trash'    => __( 'No recommendations found in Trash', 'fictioneer' ),
    'featured_image'        => __( 'Recommendation Cover Image', 'fictioneer' ),
    'set_featured_image'    => __( 'Set cover image', 'fictioneer' ),
    'remove_featured_image' => __( 'Remove cover image', 'fictioneer' ),
    'use_featured_image'    => __( 'Use as cover image', 'fictioneer' ),
    'insert_into_item'      => __( 'Insert into recommendation', 'fictioneer' ),
    'uploaded_to_this_item' => __( 'Uploaded to this recommendation', 'fictioneer' ),
    'items_list'            => __( 'Recommendations list', 'fictioneer' ),
    'items_list_navigation' => __( 'Recommendations list navigation', 'fictioneer' ),
    'filter_items_list'     => __( 'Filter recommendations list', 'fictioneer' ),
  );

  $args = array(
    'label'               => __( 'Recommendation', 'fictioneer' ),
    'labels'              => $labels,
    'menu_icon'           => 'dashicons-star-filled',
    'supports'            => ['title', 'author', 'editor', 'excerpt', 'thumbnail', 'custom-fields'],
    'taxonomies'          => ['category', 'post_tag', 'fcn_fandom', 'fcn_character', 'fcn_genre', 'fcn_content_warning'],
    'hierarchical'        => false,
    'public'              => true,
    'rewrite'             => array( 'slug' => 'recommendation' ),
    'show_in_rest'        => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'menu_position'       => 8,
    'show_in_admin_bar'   => true,
    'show_in_nav_menus'   => false,
    'can_export'          => true,
    'has_archive'         => false,
    'exclude_from_search' => false,
    'publicly_queryable'  => true,
    'capability_type'     => ['fcn_recommendation', 'fcn_recommendations'],
    'capabilities'        => array_combine( FICTIONEER_WP_CAPABILITIES, FICTIONEER_RECOMMENDATION_CAPABILITIES ),
    'map_meta_cap'        => true
  );

  register_post_type( 'fcn_recommendation', $args );
}
add_action( 'init', 'fictioneer_fcn_recommendation_post_type', 0 );

// =============================================================================
// CUSTOM TAXONOMY: FCN_GENRE
// =============================================================================

/**
 * Register custom taxonomy fcn_genre
 *
 * @since 4.3.0
 */

function fictioneer_add_genre_taxonomy() {
  $labels = array(
    'name'              => _x( 'Genres', 'Taxonomy general name.', 'fictioneer' ),
    'singular_name'     => _x( 'Genre', 'Taxonomy singular name.', 'fictioneer' ),
    'search_items'      => __( 'Search Genres', 'fictioneer' ),
    'all_items'         => __( 'All Genres', 'fictioneer' ),
    'parent_item'       => __( 'Parent Genre', 'fictioneer' ),
    'parent_item_colon' => __( 'Parent Genre:', 'fictioneer' ),
    'edit_item'         => __( 'Edit Genre', 'fictioneer' ),
    'update_item'       => __( 'Update Genre', 'fictioneer' ),
    'add_new_item'      => __( 'Add New Genre', 'fictioneer' ),
    'new_item_name'     => __( 'New Genre Name', 'fictioneer' )
  );

  $args = array(
    'hierarchical'      => true,
    'labels'            => $labels,
    'show_ui'           => true,
    'show_admin_column' => true,
    'show_in_rest'      => true,
    'query_var'         => true,
    'rewrite'           => array( 'slug' => 'genre' ),
    'capabilities'      => array(
      'manage_terms'    => 'manage_fcn_genres',
      'edit_terms'      => 'edit_fcn_genres',
      'delete_terms'    => 'delete_fcn_genres',
      'assign_terms'    => 'assign_fcn_genres'
    )
  );

  register_taxonomy( 'fcn_genre', ['fcn_chapter', 'fcn_story', 'fcn_collection', 'fcn_recommendation'], $args );
}
add_action( 'init', 'fictioneer_add_genre_taxonomy', 0 );

// =============================================================================
// CUSTOM TAXONOMY: FCN_FANDOM
// =============================================================================

/**
 * Register custom taxonomy fcn_fandom
 *
 * @since 4.0.0
 */

function fictioneer_add_fandom_taxonomy() {
  $labels = array(
    'name'              => _x( 'Fandoms', 'Taxonomy general name.', 'fictioneer' ),
    'singular_name'     => _x( 'Fandom', 'Taxonomy singular name.', 'fictioneer' ),
    'search_items'      => __( 'Search Fandoms', 'fictioneer' ),
    'all_items'         => __( 'All Fandoms', 'fictioneer' ),
    'parent_item'       => __( 'Parent Fandom', 'fictioneer' ),
    'parent_item_colon' => __( 'Parent Fandom:', 'fictioneer' ),
    'edit_item'         => __( 'Edit Fandom', 'fictioneer' ),
    'update_item'       => __( 'Update Fandom', 'fictioneer' ),
    'add_new_item'      => __( 'Add New Fandom', 'fictioneer' ),
    'new_item_name'     => __( 'New Fandom Name', 'fictioneer' )
  );

  $args = array(
    'hierarchical'      => true,
    'labels'            => $labels,
    'show_ui'           => true,
    'show_admin_column' => false,
    'show_in_rest'      => true,
    'query_var'         => true,
    'rewrite'           => array( 'slug' => 'fandom' ),
    'capabilities'      => array(
      'manage_terms'    => 'manage_fcn_fandoms',
      'edit_terms'      => 'edit_fcn_fandoms',
      'delete_terms'    => 'delete_fcn_fandoms',
      'assign_terms'    => 'assign_fcn_fandoms'
    )
  );

  register_taxonomy( 'fcn_fandom', ['fcn_chapter', 'fcn_story', 'fcn_collection', 'fcn_recommendation'], $args );
}
add_action( 'init', 'fictioneer_add_fandom_taxonomy', 0 );

// =============================================================================
// CUSTOM TAXONOMY: FCN_CHARACTER
// =============================================================================

/**
 * Register custom taxonomy fcn_character
 *
 * @since 4.3.0
 */

function fictioneer_add_character_taxonomy() {
  $labels = array(
    'name'              => _x( 'Characters', 'Taxonomy general name.', 'fictioneer' ),
    'singular_name'     => _x( 'Character', 'Taxonomy singular name.', 'fictioneer' ),
    'search_items'      => __( 'Search Characters', 'fictioneer' ),
    'all_items'         => __( 'All Characters', 'fictioneer' ),
    'parent_item'       => __( 'Parent Character', 'fictioneer' ),
    'parent_item_colon' => __( 'Parent Character:', 'fictioneer' ),
    'edit_item'         => __( 'Edit Character', 'fictioneer' ),
    'update_item'       => __( 'Update Character', 'fictioneer' ),
    'add_new_item'      => __( 'Add New Character', 'fictioneer' ),
    'new_item_name'     => __( 'New Character Name', 'fictioneer' )
  );

  $args = array(
    'hierarchical'      => true,
    'labels'            => $labels,
    'show_ui'           => true,
    'show_admin_column' => false,
    'show_in_rest'      => true,
    'query_var'         => true,
    'rewrite'           => array( 'slug' => 'character' ),
    'capabilities'      => array(
      'manage_terms'    => 'manage_fcn_characters',
      'edit_terms'      => 'edit_fcn_characters',
      'delete_terms'    => 'delete_fcn_characters',
      'assign_terms'    => 'assign_fcn_characters'
    )
  );

  register_taxonomy( 'fcn_character', ['fcn_chapter', 'fcn_story', 'fcn_collection', 'fcn_recommendation'], $args );
}
add_action( 'init', 'fictioneer_add_character_taxonomy', 0 );

// =============================================================================
// CUSTOM TAXONOMY: FCN_CONTENT_WARNING
// =============================================================================

/**
 * Register custom taxonomy fcn_content_warning
 *
 * @since 4.7.0
 */

function fictioneer_add_content_warning_taxonomy() {
  $labels = array(
    'name'              => _x( 'Content Warnings', 'Taxonomy general name.', 'fictioneer' ),
    'singular_name'     => _x( 'Content Warning', 'Taxonomy singular name.', 'fictioneer' ),
    'search_items'      => __( 'Search Content Warnings', 'fictioneer' ),
    'all_items'         => __( 'All Content Warnings', 'fictioneer' ),
    'parent_item'       => __( 'Parent Content Warning', 'fictioneer' ),
    'parent_item_colon' => __( 'Parent Content Warning:', 'fictioneer' ),
    'edit_item'         => __( 'Edit Content Warning', 'fictioneer' ),
    'update_item'       => __( 'Update Content Warning', 'fictioneer' ),
    'add_new_item'      => __( 'Add New Content Warning', 'fictioneer' ),
    'new_item_name'     => __( 'New Content Warning Name', 'fictioneer' )
  );

  $args = array(
    'hierarchical'      => true,
    'labels'            => $labels,
    'show_ui'           => true,
    'show_admin_column' => false,
    'show_in_rest'      => true,
    'query_var'         => true,
    'rewrite'           => array( 'slug' => 'content-warning' ),
    'capabilities'      => array(
      'manage_terms'    => 'manage_fcn_content_warnings',
      'edit_terms'      => 'edit_fcn_content_warnings',
      'delete_terms'    => 'delete_fcn_content_warnings',
      'assign_terms'    => 'assign_fcn_content_warnings'
    )
  );

  register_taxonomy( 'fcn_content_warning', ['fcn_chapter', 'fcn_story', 'fcn_collection', 'fcn_recommendation'], $args );
}
add_action( 'init', 'fictioneer_add_content_warning_taxonomy', 0 );

// =============================================================================
// MODIFY DEFAULT CATEGORIES AND POST TAGS
// =============================================================================

/**
 * Overrides the default taxonomy capability check for categories and post tags
 *
 * @since 5.6.0
 *
 * @param array  $caps  Primitive capabilities required of the user.
 * @param string $cap   Capability being checked.
 *
 * @return array Filtered primitive capabilities.
 */

function fictioneer_override_default_taxonomy_capability_check( $caps, $cap ) {
  $targets = [
    'manage_categories',
    'edit_categories',
    'delete_categories',
    'assign_categories',
    'manage_post_tags',
    'edit_post_tags',
    'delete_post_tags',
    'assign_post_tags',
  ];

  if ( in_array( $cap, $targets ) ) {
    $caps = [ $cap ];
  }

  return $caps;
}
add_filter( 'map_meta_cap', 'fictioneer_override_default_taxonomy_capability_check', 9999, 2 );

/**
 * Restricts tag creation for the 'post_tag' taxonomy
 *
 * @since 5.6.0
 *
 * @param mixed  $term      The term to be added.
 * @param string $taxonomy  The taxonomy type of the term.
 *
 * @return mixed Either the original term or a WP_Error object.
 */

function fictioneer_restrict_tag_creation( $term, $taxonomy ) {
  if ( $taxonomy == 'post_tag' ) {
    return new WP_Error( 'term_addition_blocked', __( 'You are unauthorized to add new terms.' ) );
  }

  return $term;
}

if ( ! current_user_can( 'edit_post_tags' ) && ! current_user_can( 'manage_options' ) ) {
  add_filter( 'pre_insert_term', 'fictioneer_restrict_tag_creation', 9999, 2 );
}

// =============================================================================
// REMOVE UNDESIRED SUB-MENUS
// =============================================================================

/**
 * Removes undesired sub-menu items for taxonomies
 *
 * @since 5.6.0
 */

function fictioneer_remove_sub_menus() {
  // Setup
  $current_user = wp_get_current_user();
  $user_caps = array_keys( array_filter( $current_user->allcaps ) );
  $taxonomies = ['category', 'post_tag', 'fcn_genre', 'fcn_fandom', 'fcn_character', 'fcn_content_warning'];
  $post_types = ['post', 'page', 'fcn_story', 'fcn_chapter', 'fcn_collection', 'fcn_recommendation'];

  // Filter out undesired menus
  foreach ( $post_types as $type ) {
    $plural = $type == 'fcn_story' ? 'fcn_stories' : "{$type}s";

    if ( ! in_array( "edit_{$plural}", $user_caps ) ) {
      foreach ( $taxonomies as $tax ) {
        if ( ! in_array( $tax, $user_caps ) ) {
          remove_submenu_page(
            "edit-tags.php?taxonomy=category&amp;post_type={$type}",
            "edit-tags.php?taxonomy={$tax}&amp;post_type={$type}"
          );
        }
      }
    }
  }
}
add_action( 'admin_menu', 'fictioneer_remove_sub_menus' );

// =============================================================================
// DROPDOWN STORY FILTER FOR CHAPTER LIST TABLE
// =============================================================================

/**
 * Adds a filter dropdown to the chapters list table
 *
 * @since 5.6.0
 *
 * @param string $post_type  The current post type being viewed.
 */

function fictioneer_add_chapter_story_filter_dropdown( $post_type ) {
  $screen = get_current_screen();

  // Make sure this is the chapter list table view
  if (
    empty( $screen ) ||
    $screen->id !== 'edit-fcn_chapter' ||
    $post_type !== 'fcn_chapter'
  ) {
    return;
  }

  // Setup
  $current_user = wp_get_current_user();
  $user_caps = array_keys( array_filter( $current_user->allcaps ) );
  $args = array(
    'post_type' => 'fcn_story',
    'post_status' => 'any',
    'posts_per_page' => 200,
    'orderby' => 'modified',
    'order' => 'DESC',
    'no_found_rows' => true,
    'update_post_meta_cache' => false,
    'update_post_term_cache' => false
  );

  // If not an editor, only show the author's own stories
  if ( ! in_array( 'edit_others_fcn_stories', $user_caps ) ) {
    $args['author'] = get_current_user_id();
  }

  // Get stories
  $posts = get_posts( $args );

  // Do not display nothing or one
  if ( count( $posts ) < 2 ) {
    return;
  }

  // Restore alphabetic order
  usort( $posts, function( $a, $b ) {
    return strcmp( $a->post_title, $b->post_title );
  });

  // Build dropdown
  $selected = absint( $_GET['filter_story'] ?? 0 );

  // Open
  echo '<select name="filter_story"><option value="">' . __( 'All Stories', 'fictioneer' ) . '</option>';

  // Note if there are too many stories
  if ( count( $posts ) >= 200 ) {
    echo '<option value="" disabled>' . __( '200 MOST RECENTLY UPDATED', 'fictioneer' ) . '</option>';
  }

  // Items
  foreach ( $posts as $post ) {
    echo sprintf(
      '<option value="%s" %s>%s</option>',
      $post->ID,
      $selected == $post->ID ? 'selected' : '',
      $post->post_title
    );
  }

  // Close
  echo '</select>';
}
add_action( 'restrict_manage_posts', 'fictioneer_add_chapter_story_filter_dropdown' );

/**
 * Filters chapters in the chapter list table by story
 *
 * @param WP_Query $query  The WP_Query instance.
 */

function fictioneer_filter_chapters_by_story( $query ) {
  global $post_type;

  // Abort if...
  if ( ! function_exists( 'get_current_screen' ) ) {
    return;
  }

  $screen = get_current_screen();

  // Make damn sure this is the chapter list table query
  if (
    ! is_admin() ||
    ! $query->is_main_query() ||
    $query->query_vars['post_type'] === 'fcn_story' ||
    $post_type !== 'fcn_chapter' ||
    empty( $_GET['filter_story'] ?? 0 ) ||
    empty( $screen ) ||
    $screen->id !== 'edit-fcn_chapter'
  ) {
    return;
  }

  // Prepare meta query
  $meta_query = array(
    array(
      'key' => 'fictioneer_chapter_story',
      'value' => absint( $_GET['filter_story'] ?? 0 ),
      'compare' => '='
    )
  );

  // Apply to query
  $query->set( 'meta_query', $meta_query );
}

if ( is_admin() ) {
  add_action( 'pre_get_posts', 'fictioneer_filter_chapters_by_story' );
}
