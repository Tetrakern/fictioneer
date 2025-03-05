<?php

// =============================================================================
// FICTION API - SINGLE STORY
// =============================================================================

if ( ! function_exists( 'fictioneer_api_get_story_node' ) ) {
  /**
   * Returns array with story data
   *
   * @since 5.1.0
   * @since 5.27.5 - Refactored.
   *
   * @param int  $story_id               ID of the story.
   * @param bool $args['list_chapters']  Whether to include the full chapter list.
   *
   * @return array|boolean Either array with story data or false if not valid.
   */

  function fictioneer_api_get_story_node( $story_id, $args = [] ) {
    // Validation
    $data = fictioneer_get_story_data( $story_id, false ); // Does not refresh comment count!

    // Abort if...
    if ( empty( $data ) ) {
      return false;
    }

    // Setup
    $story_post = get_post( $story_id );
    $author_id = get_post_field( 'post_author', $story_id );
    $author_url = get_the_author_meta( 'user_url', $author_id );
    $co_author_ids = get_post_meta( $story_id, 'fictioneer_story_co_authors', true );
    $language = get_post_meta( $story_id, 'fictioneer_story_language', true );
    $searchable = get_post_meta( $story_id, 'fictioneer_story_searchable', true );
    $node = [];

    $tax_node_names = array(
      'category' => 'categories',
      'post_tag' => 'tags',
      'fcn_fandom' => 'fandoms',
      'fcn_character' => 'characters',
      'fcn_content_warning' => 'warnings',
      'fcn_genre' => 'genres'
    );

    // Identity
    $node['id'] = $story_id;
    $node['guid'] = get_the_guid( $story_id );
    $node['language'] = empty( $language ) ? get_bloginfo( 'language' ) : $language;
    $node['title'] = $data['title'];
    $node['url'] = get_post_meta( $story_id, 'fictioneer_story_redirect_link', true ) ?: get_permalink( $story_id );

    if ( $searchable ) {
      $node['searchable'] = $searchable;
    }

    // Author
    if ( ! empty( $author_id ) ) {
      $node['author'] = [];
      $node['author']['name'] = get_the_author_meta( 'display_name', $author_id );

      if ( ! empty( $author_url ) ) {
        $node['author']['url'] = $author_url;
      }
    }

    // Co-authors
    if ( is_array( $co_author_ids ) && ! empty( $co_author_ids ) ) {
      $node['coAuthors'] = [];

      foreach ( $co_author_ids as $co_id ) {
        $co_author_name = get_the_author_meta( 'display_name', $co_id );

        if ( $co_id != $author_id && ! empty( $co_author_name ) ) {
          $co_author_url = get_the_author_meta( 'user_url', $co_id );
          $co_author_node = array(
            'name' => $co_author_name
          );

          if ( ! empty( $co_author_url ) ) {
            $co_author_node['url'] = $co_author_url;
          }

          $node['coAuthors'][] = $co_author_node;
        }
      }

      if ( empty( $node['coAuthors'] ) ) {
        unset( $node['coAuthors'] );
      }
    }

    // Content
    $content = '';

    if ( $data['status'] === 'Oneshot' && empty( $data['chapter_ids'] ) ) {
      $content = get_the_excerpt( $story_id ); // Story likely misused as chapter
    } else {
      $content = get_the_content( null, false, $story_id );
      $content = apply_filters( 'the_content', $content );
    }

    $description = get_post_meta( $story_id, 'fictioneer_story_short_description', true );
    $node['content'] = $content;
    $node['description'] = strip_shortcodes( $description );

    // Meta
    $node['words'] = intval( $data['word_count'] );
    $node['ageRating'] = $data['rating'];
    $node['status'] = $data['status'];
    $node['chapterCount'] = intval( $data['chapter_count'] );
    $node['published'] = get_post_time( 'U', true, $story_id );
    $node['modified'] = get_post_modified_time( 'U', true, $story_id );
    $node['protected'] = $story_post->post_password ? true : false;

    // Image
    if ( FICTIONEER_API_STORYGRAPH_IMAGES ) {
      $node['images'] = array(
        'hotlinkAllowed' => FICTIONEER_API_STORYGRAPH_HOTLINK,
      );

      $cover = get_the_post_thumbnail_url( $story_id, 'full' );
      $header = get_post_meta( $story_id, 'fictioneer_custom_header_image', true );
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

    if ( ! empty( $taxonomies ) ) {
      $node['taxonomies'] = $taxonomies;
    }

    // Chapter IDs
    if ( ! empty( $data['chapter_ids'] ) ) {
      $node['chapter_ids'] = array_map( 'intval', $data['chapter_ids'] ); // Already sanitized
    }

    // Chapter list (with meta)
    if ( ( $args['list_chapters'] ?? 0 ) && ! empty( $data['chapter_ids'] ) ) {
      global $wpdb;

      // Fast and more memory-efficient custom SQL
      $allowed_meta_keys = array(
        'fictioneer_chapter_language',
        'fictioneer_chapter_searchable',
        'fictioneer_chapter_prefix',
        'fictioneer_chapter_group',
        'fictioneer_chapter_rating',
        'fictioneer_chapter_warning',
        'fictioneer_chapter_no_chapter',
        'fictioneer_chapter_co_authors',
        'fictioneer_chapter_hidden',
        '_word_count'
      );

      $placeholders = implode( ',', array_fill( 0, count( $data['chapter_ids'] ), '%d' ) );
      $meta_placeholders = implode( ',', array_fill( 0, count( $allowed_meta_keys ), '%s' ) );

      $query = $wpdb->prepare(
        "SELECT p.ID, p.post_title, p.post_date_gmt, p.post_modified_gmt, p.post_password, p.guid,
                u.ID AS author_id, u.display_name AS author_name, u.user_url AS author_url,
                pm.meta_key, pm.meta_value
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
        LEFT JOIN {$wpdb->users} u ON p.post_author = u.ID
        WHERE p.post_type = 'fcn_chapter'
        AND p.post_status = 'publish'
        AND p.ID IN ({$placeholders})
        AND pm.meta_key IN ({$meta_placeholders})",
        array_merge( $data['chapter_ids'], $allowed_meta_keys )
      );

      $raw_results = $wpdb->get_results( $query );

      // Process...
      $chapters = [];
      $hidden_chapters = [];

      // ... loop rows
      foreach ( $raw_results as $row ) {
        // ... each row contains the post data
        if ( ! isset( $chapters[ $row->ID ] ) ) {
          $chapters[ $row->ID ] = array(
            'id' => intval( $row->ID ),
            'guid' => $row->guid,
            'url' => get_permalink( $row->ID ),
            'title' => $row->post_title,
            'published' => strtotime( $row->post_date_gmt ),
            'modified' => strtotime( $row->post_modified_gmt ),
            'protected' => ! empty( $row->post_password ),
            'nonChapter' => false, // Default
            'language' => get_bloginfo( 'language' ), // Default
            'taxonomies' => [], // Default
            'author' => array(
              'name' => $row->author_name,
              'url'  => $row->author_url
            )
          );
        }

        if ( ! empty( $row->taxonomies ) ) {
          $taxonomy_pairs = explode( '||', $row->taxonomies );

          foreach ( $taxonomy_pairs as $pair ) {
            list( $taxonomy, $term ) = explode( '::', $pair, 2 );

            if ( ! isset( $chapters[ $row->ID ]['taxonomies'][ $taxonomy ] ) ) {
              $chapters[ $row->ID ]['taxonomies'][ $taxonomy ] = [];
            }

            if ( ! in_array( $term, $chapters[ $row->ID ]['taxonomies'][ $taxonomy ], true ) ) {
              $chapters[ $row->ID ]['taxonomies'][ $taxonomy ][] = $term;
            }
          }
        }

        // ... get meta value
        $meta_value = maybe_unserialize( $row->meta_value );

        // ... remember hidden chapters
        if (
          $row->meta_key === 'fictioneer_chapter_hidden' &&
          ! empty( $meta_value ) &&
          $meta_value !== '0' &&
          $meta_value !== 'false'
        ) {
          $hidden_chapters[ $row->ID ] = true;
        }

        // ... add meta data to node
        switch ( $row->meta_key ) {
          case 'fictioneer_chapter_language':
            $chapters[ $row->ID ]['language'] = ! empty( $meta_value ) ? $meta_value : get_bloginfo( 'language' );
            break;
          case 'fictioneer_chapter_searchable':
            $chapters[ $row->ID ]['searchable'] = $meta_value;
            break;
          case 'fictioneer_chapter_prefix':
            $chapters[ $row->ID ]['prefix'] = $meta_value;
            break;
          case 'fictioneer_chapter_group':
            $chapters[ $row->ID ]['group'] = $meta_value;
            break;
          case 'fictioneer_chapter_rating':
            $chapters[ $row->ID ]['ageRating'] = $meta_value;
            break;
          case 'fictioneer_chapter_warning':
            $chapters[ $row->ID ]['warning'] = $meta_value;
            break;
          case 'fictioneer_chapter_no_chapter':
            $chapters[ $row->ID ]['nonChapter'] = ! empty( $meta_value );
            break;
          case '_word_count':
            $chapters[ $row->ID ]['words'] = fictioneer_get_word_count( $row->ID, intval( $meta_value ) );
            break;
          case 'fictioneer_chapter_co_authors':
            $co_author_ids = maybe_unserialize( $meta_value );

            if ( is_array( $co_author_ids ) && ! empty( $co_author_ids ) ) {
              $chapters[ $row->ID ]['coAuthors'] = [];

              foreach ( $co_author_ids as $co_id ) {
                if ( $co_id != $row->author_id ) {
                  $co_author_name = get_the_author_meta( 'display_name', $co_id );

                  if ( ! empty( $co_author_name ) ) {
                    $co_author_url = get_the_author_meta( 'user_url', $co_id );
                    $co_author_node = array( 'name' => $co_author_name );

                    if ( ! empty( $co_author_url ) ) {
                      $co_author_node['url'] = $co_author_url;
                    }

                    $chapters[ $row->ID ]['coAuthors'][] = $co_author_node;
                  }
                }
              }
            }
            break;
          // No default case
        }
      }

      // ... taxonomies
      $taxonomy_query = $wpdb->prepare(
        "SELECT tr.object_id, tt.taxonomy, t.name
        FROM {$wpdb->term_relationships} tr
        INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        INNER JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
        WHERE tr.object_id IN ({$placeholders})",
        $data['chapter_ids']
      );

      $taxonomy_results = $wpdb->get_results( $taxonomy_query );

      foreach ( $taxonomy_results as $row ) {
        if ( isset( $chapters[ $row->object_id ] ) ) {
          $key = $tax_node_names[ $row->taxonomy ] ?? $row->taxonomy;

          if ( ! isset( $chapters[ $row->object_id ]['taxonomies'][ $key ] ) ) {
            $chapters[ $row->object_id ]['taxonomies'][ $key ] = [];
          }

          if ( ! in_array( $row->name, $chapters[ $row->object_id ]['taxonomies'][ $key ], true ) ) {
            $chapters[ $row->object_id ]['taxonomies'][ $key ][] = $row->name;
          }
        }
      }

      // ... remove hidden chapters
      $chapters = array_diff_key( $chapters, $hidden_chapters );

      // ... restore original order
      $order_map = array_flip( $data['chapter_ids'] );

      usort( $chapters, function ( $a, $b ) use ( $order_map ) {
        return $order_map[ $a['id'] ] - $order_map[ $b['id'] ];
      });

      // ... cleanup
      foreach ( $chapters as $key => $chapter ) {
        if ( empty( $chapter['taxonomies'] ) ) {
          unset( $chapters[ $key ]['taxonomies'] );
        }
      }

      // ... add to story node
      $node['chapters'] = array_values( $chapters );
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
 * Add GET route for single story by ID.
 *
 * @since 5.1.0
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
   * @since 5.1.0
   * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
   *
   * @param WP_REST_Request $request  Request object.
   *
   * @return WP_REST_Response|WP_Error Response or error.
   */

  function fictioneer_api_request_story( WP_REST_Request $request ) {
    // Setup
    $story_id = absint( $request['id'] );

    // Graph from story node
    $graph = fictioneer_api_get_story_node( $story_id, array( 'list_chapters' => true ) );

    // Return error if...
    if ( empty( $graph ) ) {
      return rest_ensure_response(
        new WP_Error(
          'invalid_story_id',
          'No valid story was found matching the ID.',
          array( 'status' => '400' )
        )
      );
    }

    // Request meta
    $graph['timestamp'] = current_time( 'U', true );

    // Response
    return rest_ensure_response( $graph );
  }
}

// =============================================================================
// FICTION API - ALL STORIES
// =============================================================================

/**
 * Add GET route for all stories.
 *
 * @since 5.1.0
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
   * Return JSON with all stories plus meta data.
   *
   * @since 5.1.0
   * @since 5.27.5 - Added short Transient caching for last modified date.
   * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
   *
   * @param WP_REST_Request $request  Request object.
   */

  function fictioneer_api_request_stories( WP_REST_Request $request ) {
    global $wpdb;

    // Setup
    $page = max( absint( $request['page'] ) ?? 1, 1 );
    $graph = [];

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

    // Prime author cache
    if ( ! empty( $query->posts ) && function_exists( 'update_post_author_caches' ) ) {
      update_post_author_caches( $query->posts );
    }

    // Return error if...
    if ( $page > $query->max_num_pages ) {
      return rest_ensure_response(
        new WP_Error(
          'invalid_story_id',
          'Requested page number exceeds maximum pages.',
          array( 'status' => '400' )
        )
      );
    }

    // Site meta
    $graph['url'] = get_home_url( null, '', 'rest' );
    $graph['language'] = get_bloginfo( 'language' );
    $graph['storyCount'] = $query->found_posts;
    $graph['chapterCount'] = intval( wp_count_posts( 'fcn_chapter' )->publish );

    // Stories
    if ( ! empty( $stories ) ) {
      $graph['lastPublished'] = get_post_time( 'U', true, $stories[0] );

      if ( get_option( 'fictioneer_enable_lastpostmodified_caching' ) ) {
        $graph['lastModified'] = strtotime( get_lastpostmodified( 'gmt', 'fcn_story' ) );
      } else {
        $graph['lastModified'] = get_transient( 'fictioneer_api_lastpostmodified_story_gmt' );

        if ( ! $graph['lastModified'] ) {
          $graph['lastModified'] = strtotime( get_lastpostmodified( 'gmt', 'fcn_story' ) );

          set_transient( 'fictioneer_api_lastpostmodified_story_gmt', $graph['lastModified'], 30 );
        }
      }

      $graph['stories'] = [];

      foreach ( $stories as $story ) {
        $graph['stories'][ $story->ID ] = fictioneer_api_get_story_node( $story->ID );
      }
    }

    $graph['lastModifiedStory'] = intval(
      $wpdb->get_var(
        "SELECT ID FROM {$wpdb->posts} WHERE post_status = 'publish' ORDER BY post_modified DESC LIMIT 1"
      )
    );

    // Request meta
    $graph['page'] = $page;
    $graph['perPage'] = FICTIONEER_API_STORYGRAPH_STORIES_PER_PAGE;
    $graph['maxPages'] = $query->max_num_pages;
    $graph['timestamp'] = current_time( 'U', true );

    return rest_ensure_response( $graph );
  }
}
