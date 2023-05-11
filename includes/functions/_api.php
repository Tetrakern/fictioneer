<?php

// =============================================================================
// FICTION API - SINGLE STORY
// =============================================================================

if ( ! function_exists( 'fictioneer_api_get_story_node' ) ) {
  /**
   * Returns array with story data
   *
   * @since Fictioneer 5.1
   *
   * @param int     $story_id      ID of the story.
   * @param boolean $with_chapters Whether to include chapters. Default true.
   *
   * @return array|boolean Either array with story data or false if not valid.
   */

  function fictioneer_api_get_story_node( $story_id, $with_chapters = true ) {
    // Validation
    $data = fictioneer_get_story_data( $story_id );

    // Abort if...
    if ( empty( $data ) ) return false;

    // Setup
    $author_id = get_post_field( 'post_author', $story_id );
    $author_url = get_the_author_meta( 'user_url', $author_id );
    $co_author_ids = fictioneer_get_field( 'fictioneer_story_co_authors', $story_id );
    $language = fictioneer_get_field( 'fictioneer_story_language', $story_id );
    $node = [];

    // Identity
    $node['id'] = $story_id;
    $node['guid'] = get_the_guid( $story_id );
    $node['url'] = get_permalink( $story_id );
    $node['language'] = empty( $language ) ? FICTIONEER_SITE_LANGUAGE : $language;
    $node['title'] = $data['title'];

    // Author
    if ( ! empty( $author_id ) ) {
      $node['author'] = [];
      $node['author']['name'] = get_the_author_meta( 'display_name', $author_id );
      if ( ! empty( $author_url ) ) $node['author']['url'] = $author_url;
    }

    // Co-authors
    if ( ! empty( $co_author_ids ) ) {
      $node['coAuthors'] = [];

      foreach ( $co_author_ids as $co_id ) {
        if ( $co_id != $author_id ) {
          $co_author_url = get_the_author_meta( 'user_url', $co_id );
          $co_author_node = [];

          $co_author_node['name'] = get_the_author_meta( 'display_name', $co_id );
          if ( ! empty( $co_author_url ) ) $co_author_node['url'] = $co_author_url;

          $node['coAuthors'][] = $co_author_node;
        }
      }
    }

    // Content
    $content = get_the_content( null, false, $story_id );
    $description = fictioneer_get_field( 'fictioneer_story_short_description', $story_id );
    $node['content'] = $content;
    $node['description'] = strip_shortcodes( $description );

    // Meta
    $node['words'] = intval( $data['word_count'] );
    $node['ageRating'] = $data['rating'];
    $node['status'] = $data['status'];
    $node['chapterCount'] = intval( $data['chapter_count'] );
    $node['published'] = get_post_time( 'U', true, $story_id );
    $node['modified'] = get_post_modified_time( 'U', true, $story_id );
    $node['protected'] = post_password_required( $story_id );

    // Image
    if ( FICTIONEER_API_STORYGRAPH_IMAGES ) {
      $node['images'] = array(
        'hotlinkAllowed' => FICTIONEER_API_STORYGRAPH_HOTLINK,
      );
      $cover = get_the_post_thumbnail_url( $story_id, 'full' );
      $header = fictioneer_get_field( 'fictioneer_custom_header_image', $story_id );
      $header = wp_get_attachment_image_url( $header, 'full' );

      if ( ! empty( $header ) ) {
        $node['images']['header'] = $header;
      }

      if ( ! empty( $cover ) ) {
        $node['images']['cover'] = $cover;
      }

      if ( empty( $node['images'] ) ) {
        unset( $node['images'] );
      }
    }

    // Taxonomies
    $taxonomies = fictioneer_get_taxonomy_names( $story_id );
    if ( ! empty( $taxonomies ) ) $node['taxonomies'] = $taxonomies;

    // Chapters
    if ( $with_chapters && ! empty( $data['chapter_ids'] ) ) {
      // Query chapters
      $chapter_query = new WP_Query(
        array(
          'post_type' => 'fcn_chapter',
          'post_status' => 'publish',
          'post__in' => $data['chapter_ids'],
          'ignore_sticky_posts' => true,
          'orderby' => 'post__in',
          'posts_per_page' => -1,
          'no_found_rows' => false
        )
      );

      if ( $chapter_query->have_posts() ) {
        $node['chapters'] = [];

        while( $chapter_query->have_posts() ) {
          // Setup
          $chapter_query->the_post();
          $chapter_id = get_the_ID();

          // Skip not visible chapters
          if ( fictioneer_get_field( 'fictioneer_chapter_hidden' ) ) continue;

          // Data
          $author_id = get_the_author_meta( 'id' );
          $author_url = get_the_author_meta( 'user_url' );
          $co_author_ids = fictioneer_get_field( 'fictioneer_chapter_co_authors', $chapter_id );
          $group = fictioneer_get_field( 'fictioneer_chapter_group', $chapter_id );
          $rating = fictioneer_get_field( 'fictioneer_chapter_rating', $chapter_id );
          $language = fictioneer_get_field( 'fictioneer_chapter_language', $chapter_id );
          $prefix = fictioneer_get_field( 'fictioneer_chapter_prefix', $chapter_id );
          $no_chapter = fictioneer_get_field( 'fictioneer_chapter_no_chapter', $chapter_id );
          $warning = fictioneer_get_field( 'fictioneer_chapter_warning', $chapter_id );

          // Chapter identity
          $chapter = [];
          $chapter['id'] = $chapter_id;
          $chapter['guid'] = get_the_guid( $chapter_id );
          $chapter['url'] = get_permalink();
          $chapter['language'] = empty( $language ) ? FICTIONEER_SITE_LANGUAGE : $language;
          if ( ! empty( $prefix ) ) $chapter['prefix'] = $prefix;
          $chapter['title'] = fictioneer_get_safe_title( $chapter_id );
          if ( ! empty( $group ) ) $chapter['group'] = $group;

          // Chapter author
          if ( ! empty( $author_id ) ) {
            $chapter['author'] = [];
            $chapter['author']['name'] = get_the_author_meta( 'display_name', $author_id );
            if ( ! empty( $author_url ) ) $chapter['author']['url'] = $author_url;
          }

          // Chapter co-authors
          if ( ! empty( $co_author_ids ) ) {
            $chapter['coAuthors'] = [];

            foreach ( $co_author_ids as $co_id ) {
              if ( $co_id != $author_id ) {
                $co_author_url = get_the_author_meta( 'user_url', $co_id );
                $co_author_node = [];

                $co_author_node['name'] = get_the_author_meta( 'display_name', $co_id );
                if ( ! empty( $co_author_url ) ) $co_author_node['url'] = $co_author_url;

                $chapter['coAuthors'][] = $co_author_node;
              }
            }
          }

          // Chapter meta
          $chapter['published'] = get_post_time( 'U', true );
          $chapter['modified'] = get_post_modified_time( 'U', true );
          $chapter['protected'] = post_password_required();
          $chapter['words'] = intval( get_post_meta( $chapter_id, '_word_count', true ) );
          $chapter['nonChapter'] = ! empty( $no_chapter );
          if ( ! empty( $rating ) ) $chapter['ageRating'] = $rating;
          if ( ! empty( $warning ) ) $chapter['warning'] = $warning;

          // Chapter taxonomies
          $taxonomies = fictioneer_get_taxonomy_names( $chapter_id );
          if ( ! empty( $taxonomies ) ) $chapter['taxonomies'] = $taxonomies;

          // Add to story node
          $node['chapters'][] = $chapter;
        }
      }

      wp_reset_postdata();
    }

    // Support
    $support_urls = fictioneer_get_support_links( $story_id, false, $author_id );

    if ( ! empty( $support_urls ) ) {
      $node['support'] = [];

      foreach ( $support_urls as $key => $url ) {
        $node['support'][ $key ] = $url;
      }
    }

    // Return
    return $node;
  }
}

/**
 * Add GET route for single story by ID
 *
 * @since Fictioneer 5.1
 */

if ( get_option( 'fictioneer_enable_storygraph_api' ) ) {
  add_action(
    'rest_api_init',
    function () {
      register_rest_route(
        'storygraph/v1',
        '/story/(?P<id>\d+)',
        array(
          'methods' => 'GET',
          'callback' => 'fictioneer_api_request_story',
          'permission_callback' => '__return_true'
        )
      );
    }
  );
}

if ( ! function_exists( 'fictioneer_api_request_story' ) ) {
  /**
   * Returns JSON for a single story
   *
   * @since Fictioneer 5.1
   * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
   *
   * @param WP_REST_Request $WP_REST_Request Request object.
   *
   * @return WP_REST_Response|WP_Error Response or error.
   */

  function fictioneer_api_request_story( WP_REST_Request $data ) {
    // Setup
    $story_id = absint( $data['id'] );

    // Return cache if still valid
    if ( FICTIONEER_API_STORYGRAPH_TRANSIENTS ) {
      $cache = get_transient( 'fictioneer_storygraph_story_' . $story_id );

      if ( ! empty( $cache ) ) {
        return rest_ensure_response( $cache );
      }
    }

    // Graph from story node
    $graph = fictioneer_api_get_story_node( $story_id );

    // Return error if...
    if ( empty( $graph ) ) {
      return rest_ensure_response(
        new WP_Error(
          'invalid_story_id',
          'No valid story was found matching the ID.',
          ['status' => '400']
        )
      );
    }

    // Request meta
    $graph['timestamp'] = current_time( 'U', true );

    // Cache request
    if ( FICTIONEER_API_STORYGRAPH_TRANSIENTS ) {
      set_transient( 'fictioneer_storygraph_story_' . $story_id, $graph, FICTIONEER_API_STORYGRAPH_CACHE_TTL );
    }

    // Response
    return rest_ensure_response( $graph );
  }
}

// =============================================================================
// FICTION API - ALL STORIES
// =============================================================================

/**
 * Add GET route for all stories
 *
 * @since Fictioneer 5.1
 */

if ( get_option( 'fictioneer_enable_storygraph_api' ) ) {
  add_action(
    'rest_api_init',
    function () {
      register_rest_route(
        'storygraph/v1',
        '/stories(?:/(?P<page>\d+))?',
        array(
          'methods' => 'GET',
          'callback' => 'fictioneer_api_request_stories',
          'permission_callback' => '__return_true'
        )
      );
    }
  );
}

if ( ! function_exists( 'fictioneer_api_request_stories' ) ) {
  /**
   * Returns JSON with all stories plus meta data
   *
   * @since Fictioneer 5.1
   * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
   *
   * @param WP_REST_Request $WP_REST_Request Request object.
   */

  function fictioneer_api_request_stories( WP_REST_Request $data ) {
    // Setup
    $page = max( absint( $data['page'] ) ?? 1, 1 );
    $graph = [];

    // Return cache if still valid
    if ( FICTIONEER_API_STORYGRAPH_TRANSIENTS ) {
      $cache = get_transient( 'fictioneer_storygraph_stories_' . $page );

      if ( ! empty( $cache ) ) {
        return rest_ensure_response( $cache );
      }
    }

    // Prepare query
    $query_args = array (
      'post_type' => 'fcn_story',
      'post_status' => 'publish',
      'orderby' => 'date',
      'order' => 'DESC',
      'posts_per_page' => FICTIONEER_API_STORYGRAPH_STORIES_PER_PAGE,
      'paged' => $page
    );

    // Query stories
    $query = new WP_Query( $query_args );
    $stories = $query->posts;

    // Return error if...
    if ( $page > $query->max_num_pages ) {
      return rest_ensure_response(
        new WP_Error(
          'invalid_story_id',
          'Requested page number exceeds maximum pages.',
          ['status' => '400']
        )
      );
    }

    // Site meta
    $graph['url'] = get_home_url( null, '', 'rest' );
    $graph['language'] = FICTIONEER_SITE_LANGUAGE;
    $graph['storyCount'] = $query->found_posts;
    $graph['chapterCount'] = intval( wp_count_posts( 'fcn_chapter' )->publish );

    // Stories
    if ( ! empty( $stories ) ) {
      $graph['lastPublished'] = get_post_time( 'U', true, $stories[0] );
      $graph['lastModified'] = strtotime( get_lastpostmodified( 'gmt', 'fcn_story' ) );
      $graph['stories'] = [];

      foreach ( $stories as $story ) {
        // Get node
        $node = fictioneer_api_get_story_node( $story->ID, FICTIONEER_API_STORYGRAPH_CHAPTERS );

        // Add to graph
        $graph['stories'][ $story->ID ] = $node;

        // Last modified story?
        if ( $node['modified'] == $graph['lastModified'] ) {
          $graph['lastModifiedStory'] = $story->ID;
        }
      }
    }

    if ( FICTIONEER_API_STORYGRAPH_CHAPTERS ) {
      $graph['separateChapters'] = false;
    } else {
      $graph['separateChapters'] = true;
    }

    // Request meta
    $graph['page'] = $page;
    $graph['perPage'] = FICTIONEER_API_STORYGRAPH_STORIES_PER_PAGE;
    $graph['maxPages'] = $query->max_num_pages;
    $graph['timestamp'] = current_time( 'U', true );

    // Cache request
    if ( FICTIONEER_API_STORYGRAPH_TRANSIENTS ) {
      set_transient( 'fictioneer_storygraph_stories_' . $page, $graph, FICTIONEER_API_STORYGRAPH_CACHE_TTL );
    }

    return rest_ensure_response( $graph );
  }
}

?>
