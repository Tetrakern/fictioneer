<?php

// =============================================================================
// FICTION API - GET ALL STORIES
// =============================================================================

if ( TRUE ) {
  add_action(
    'rest_api_init',
    function () {
      register_rest_route(
        'storygraph/v1',
        '/stories',
        array(
          'methods' => 'GET',
          'callback' => 'fictioneer_api_get_stories',
          'permission_callback' => '__return_true'
        )
      );
    }
  );
}

if ( ! function_exists( 'fictioneer_api_get_stories' ) ) {
  /**
   * Returns JSON with all stories plus meta data
   *
   * @since Fictioneer 5.1
   * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
   *
   * @param WP_REST_Request $WP_REST_Request Request object.
   */

  function fictioneer_api_get_stories( WP_REST_Request $data ) {
    // Setup
    $graph = [];

    // Prepare query
    $query_args = array (
      'post_type' => 'fcn_story',
      'post_status' => 'publish',
      'orderby' => 'date',
      'order' => 'DESC',
      'posts_per_page' => -1
    );

    // Query stories
    $query = new WP_Query( $query_args );
    $stories = $query->posts;

    // Add meta to graph
    $graph['url'] = get_home_url( null, '', 'rest' );
    $graph['timestamp'] = current_time( 'U', true );
    $graph['lastPublished'] = get_post_time( 'U', true, $stories[0] );
    $graph['lastModified'] = strtotime( get_lastpostmodified( 'gmt', 'fcn_story' ) );
    $graph['storyCount'] = count( $stories );
    $graph['chapterCount'] = 0;
    $graph['cached'] = false;

    // Add stories
    $graph['stories'] = [];

    foreach ( $stories as $story ) {
      // Get pre-made data collection
      $data = fictioneer_get_story_data( $story->ID );
      $content = [];

      // Identity
      $content['id'] = $story->ID;
      $content['guid'] = get_the_guid( $story->ID );
      $content['url'] = get_permalink( $story->ID );
      $content['title'] = $data['title'];
      $content['words'] = $data['word_count'];
      $content['ageRating'] = $data['rating'];
      $content['status'] = $data['status'];
      $content['image'] = get_the_post_thumbnail_url( $story->ID, 'full' );

      // Terms

      // Count chapters
      $graph['chapterCount'] = $graph['chapterCount'] + $data['chapter_count'];

      // Add to graph
      $graph['stories'][ $story->ID ] = $content;
    }


    return rest_ensure_response( $graph );
  }
}

?>
