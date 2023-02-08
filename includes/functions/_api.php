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
    return rest_ensure_response( ['foo' => 'bar'] );
  }
}

?>
