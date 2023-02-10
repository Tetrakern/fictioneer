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
add_filter( 'acf/settings/load_json', 'fictioneer_acf_loading_point' );

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

if ( ! class_exists('acf') && is_admin() ) {
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
 * @param array  $args    The query arguments.
 * @param string $paths   The queried field.
 * @param int    $post_id The post ID.
 */

function fictioneer_acf_filter_chapters( $args, $field, $post_id ) {
  $args[ 'meta_query' ] = array(
    array(
      'key' => 'fictioneer_chapter_story',
      'value' => $post_id
    )
  );
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

?>
