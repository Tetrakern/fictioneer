<?php

// =============================================================================
// CHECK IF URL EXISTS
// =============================================================================

if ( ! function_exists( 'fictioneer_url_exists' ) ) {
  /**
   * Checks whether an URL exists
   *
   * @since Fictioneer 4.0
   * @link https://www.geeksforgeeks.org/how-to-check-the-existence-of-url-in-php/
   *
   * @param string $url  The URL to check.
   *
   * @return boolean True if the URL exists and false otherwise. Probably.
   */

  function fictioneer_url_exists( $url ) {
    if ( ! $url ) {
      return false;
    }

    $curl = curl_init( $url );
    curl_setopt( $curl, CURLOPT_NOBODY, true );

    if ( curl_exec( $curl ) ) {
      $statusCode = curl_getinfo( $curl, CURLINFO_HTTP_CODE );

      if ( $statusCode == 404 ) {
        return false;
      }

      return true;
    }

    return false;
  }
}

// =============================================================================
// CHECK WHETHER VALID JSON
// =============================================================================

if ( ! function_exists( 'fictioneer_is_valid_json' ) ) {
  /**
   * Check whether a JSON is valid
   *
   * @since Fictioneer 4.0
   *
   * @param string $data  JSON string hopeful.
   *
   * @return boolean True if the JSON is valid, false if not.
   */

  function fictioneer_is_valid_json( $data = null ) {
    if ( empty( $data ) ) {
      return false;
    }

    $data = @json_decode( $data, true );

    return ( json_last_error() === JSON_ERROR_NONE );
  }
}

// =============================================================================
// CHECK FOR ACTIVE PLUGINS
// =============================================================================

if ( ! function_exists( 'fictioneer_is_plugin_active' ) ) {
  /**
   * Checks whether a plugin is active
   *
   * @since Fictioneer 4.0
   *
   * @param string $path  Relative path to the plugin.
   *
   * @return boolean True if the plugin is active, otherwise false.
   */

  function fictioneer_is_plugin_active( $path ) {
    return in_array( $path, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
  }
}

if ( ! function_exists( 'fictioneer_seo_plugin_active' ) ) {
  /**
   * Checks whether any SEO plugin known to the theme is active
   *
   * The theme's SEO features are inherently incompatible with SEO plugins, which
   * may be more sophisticated but do not understand the theme's content structure.
   * At all. Regardless, if the user want to use a SEO plugin, it's better to turn
   * off the theme's own SEO features. This function detects some of them.
   *
   * @since Fictioneer 4.0
   *
   * @return boolean True if a known SEO is active, otherwise false.
   */

  function fictioneer_seo_plugin_active() {
    $bool = fictioneer_is_plugin_active( 'wordpress-seo/wp-seo.php' ) ||
      fictioneer_is_plugin_active( 'wordpress-seo-premium/wp-seo-premium.php' ) ||
      function_exists( 'aioseo' );

    return $bool;
  }
}

// =============================================================================
// GET USER BY ID OR EMAIL
// =============================================================================

if ( ! function_exists( 'fictioneer_get_user_by_id_or_email' ) ) {
  /**
   * Get user by ID or email
   *
   * @since Fictioneer 4.6
   *
   * @param int|string $id_or_email  User ID or email address.
   *
   * @return WP_User|boolean Returns the user or false if not found.
   */

  function fictioneer_get_user_by_id_or_email( $id_or_email ) {
    $user = false;

    if ( is_numeric( $id_or_email ) ) {
      $id = (int) $id_or_email;
      $user = get_user_by( 'id' , $id );
    } elseif ( is_object( $id_or_email ) ) {
      if ( ! empty( $id_or_email->user_id ) ) {
        $id = (int) $id_or_email->user_id;
        $user = get_user_by( 'id' , $id );
      }
    } else {
      $user = get_user_by( 'email', $id_or_email );
    }

    return $user;
  }
}

// =============================================================================
// GET LAST CHAPTER/STORY UPDATE
// =============================================================================

if ( ! function_exists( 'fictioneer_get_last_story_or_chapter_update' ) ) {
  /**
   * Get Unix timestamp for last story or chapter update
   *
   * @since Fictioneer 5.0
   *
   * @return int The timestamp in milliseconds.
   */

  function fictioneer_get_last_fiction_update() {
    $last_update = get_option( 'fictioneer_story_or_chapter_updated_timestamp' );

    if ( empty( $last_update ) ) {
      $last_update = time() * 1000;
      update_option( 'fictioneer_story_or_chapter_updated_timestamp', $last_update );
    }

    return $last_update;
  }
}

// =============================================================================
// GET STORY DATA
// =============================================================================

if ( ! function_exists( 'fictioneer_get_story_data' ) ) {
  /**
   * Get collection of a story's data
   *
   * @since Fictioneer 4.3
   *
   * @param int     $story_id       ID of the story.
   * @param boolean $show_comments  Optional. Whether the comment count is needed.
   *                                Default true.
   * @param array   $args           Optional array og arguments.
   *
   * @return array|boolean Data of the story or false if invalid.
   */

  function fictioneer_get_story_data( $story_id, $show_comments = true, $args = [] ) {
    $story_id = fictioneer_validate_id( $story_id, 'fcn_story' );
    $old_data = false;

    if ( empty( $story_id ) ) {
      return false;
    }

    // Check cache
    if ( FICTIONEER_ENABLE_STORY_DATA_META_CACHE ) {
      $old_data = get_post_meta( $story_id, 'fictioneer_story_data_collection', true );
    }

    if ( ! empty( $old_data ) && $old_data['last_modified'] >= get_the_modified_time( 'U', $story_id ) ) {
      // Return cached data without refreshing the comment count
      if ( ! $show_comments ) {
        return $old_data;
      }

      // Time to refresh comment count?
      $comment_count_delay = ( $old_data['comment_count_timestamp'] ?? 0 ) + FICTIONEER_STORY_COMMENT_COUNT_TIMEOUT;
      $refresh_comments = $comment_count_delay < time() ||( $args['refresh_comments'] ?? 0 ) || fictioneer_caching_active();

      // Refresh comment count
      if ( $refresh_comments ) {
        // Use old count as fallback
        $comment_count = $old_data['comment_count'];

        if ( count( $old_data['chapter_ids'] ) > 0 ) {
          // Counting the stored comment count of chapters is typically
          // faster than querying and counting all comments.
          $chapters = new WP_Query(
            array(
              'fictioneer_query_name' => 'story_chapters',
              'post_type' => 'fcn_chapter',
              'post_status' => 'publish',
              'post__in' => fictioneer_rescue_array_zero( $old_data['chapter_ids'] ),
              'posts_per_page' => -1,
              'no_found_rows' => true, // Improve performance
              'update_post_meta_cache' => false, // Improve performance
              'update_post_term_cache' => false // Improve performance
            )
          );

          if ( $chapters->have_posts() ) {
            $comment_count = 0; // Reset

            foreach ( $chapters->posts as $chapter ) {
               $comment_count += $chapter->comment_count;
            }
          }
        }

        /*

        This approach would be faster if the number of chapters vastly exceeds
        the comments. However, typically it's the other way around.

        $comment_count = count( $old_data['chapter_ids'] ) < 1 ? 0 : get_comments(
          array(
            'status' => 'approve',
            'post_type' => 'fcn_chapter',
            'post__in' => fictioneer_rescue_array_zero( $old_data['chapter_ids'] ),
            'count' => true,
            'update_comment_meta_cache' => false
          )
        );

        */

        $comment_count_changed = $comment_count != absint( $old_data['comment_count'] );
        $old_data['comment_count'] = $comment_count;
        $old_data['comment_count_timestamp'] = time();

        // Update meta if there is something to update
        if (
          $comment_count_changed &&
          FICTIONEER_STORY_COMMENT_COUNT_TIMEOUT > 0 &&
          ! ( $args['refresh_comments'] ?? 0 )
        ) {
          update_post_meta( $story_id, 'fictioneer_story_data_collection', $old_data );
          fictioneer_purge_post_cache( $story_id );
        }
      }

      // Return cached data
      return $old_data;
    }

    // Setup
    $chapters = fictioneer_get_field( 'fictioneer_story_chapters', $story_id );
    $tags = get_the_tags( $story_id );
    $fandoms = get_the_terms( $story_id, 'fcn_fandom' );
    $characters = get_the_terms( $story_id, 'fcn_character' );
    $warnings = get_the_terms( $story_id, 'fcn_content_warning' );
    $genres = get_the_terms( $story_id, 'fcn_genre' );
    $status = fictioneer_get_field( 'fictioneer_story_status', $story_id );
    $icon = 'fa-solid fa-circle';
    $chapter_count = 0;
    $word_count = 0;
    $comment_count = 0;
    $chapter_ids = [];

    // Assign correct icon
    if ( $status != 'Ongoing' ) {
      switch ( $status ) {
        case 'Completed':
          $icon = 'fa-solid fa-circle-check';
          break;
        case 'Oneshot':
          $icon = 'fa-solid fa-circle-check';
          break;
        case 'Hiatus':
          $icon = 'fa-solid fa-circle-pause';
          break;
        case 'Canceled':
          $icon = 'fa-solid fa-ban';
          break;
      }
    }

    // Query chapters
    $chapters = empty( $chapters ) ? $chapters : new WP_Query(
      array(
        'fictioneer_query_name' => 'story_chapters',
        'post_type' => 'fcn_chapter',
        'post_status' => 'publish',
        'post__in' => fictioneer_rescue_array_zero( $chapters ),
        'ignore_sticky_posts' => true,
        'orderby' => 'post__in', // Preserve order from meta box
        'posts_per_page' => -1, // Get all chapters (this can be hundreds)
        'no_found_rows' => true // Improve performance
      )
    );

    // Count chapters and words
    if ( ! empty( $chapters ) && $chapters->have_posts() ) {
      foreach ( $chapters->posts as $chapter ) {
        // This is about 50 times faster than using a meta query lol
        if ( ! fictioneer_get_field( 'fictioneer_chapter_hidden', $chapter->ID ) ) {
          // Do not count non-chapters...
          if ( ! fictioneer_get_field( 'fictioneer_chapter_no_chapter', $chapter->ID ) ) {
            $chapter_count += 1;
            $word_count += fictioneer_get_field( '_word_count', $chapter->ID );
          }

          // ... but they are still listed!
          $chapter_ids[] = $chapter->ID;
        }

        // Count ALL comments
        $comment_count += $chapter->comment_count;
      }
    }

    $result = array(
      'id' => $story_id,
      'chapter_count' => $chapter_count,
      'word_count' => $word_count,
      'word_count_short' => fictioneer_shorten_number( $word_count ),
      'status' => $status,
      'icon' => $icon,
      'has_taxonomies' => $fandoms || $characters || $genres,
      'tags' => $tags,
      'characters' => $characters,
      'fandoms' => $fandoms,
      'warnings' => $warnings,
      'genres' => $genres,
      'title' => fictioneer_get_safe_title( $story_id ),
      'rating' => fictioneer_get_field( 'fictioneer_story_rating', $story_id ),
      'rating_letter' => fictioneer_get_field( 'fictioneer_story_rating', $story_id )[0],
      'chapter_ids' => $chapter_ids,
      'last_modified' => get_the_modified_time( 'U', $story_id ),
      'comment_count' => $comment_count,
      'comment_count_timestamp' => time()
    );

    if ( FICTIONEER_ENABLE_STORY_DATA_META_CACHE ) {
      update_post_meta( $story_id, 'fictioneer_story_data_collection', $result );
    }

    return $result;
  }
}

// =============================================================================
// GET AUTHOR STATISTICS
// =============================================================================

if ( ! function_exists( 'fictioneer_get_author_statistics' ) ) {
  /**
   * Get collection of an author's statistics
   *
   * @since Fictioneer 4.6
   *
   * @param int $author_id  User ID of the author.
   *
   * @return array|boolean Statistics or false if user does not exist.
   */

  function fictioneer_get_author_statistics( $author_id ) {
    // Setup
    $author_id = fictioneer_validate_id( $author_id );

    if ( ! $author_id ) {
      return false;
    }

    $author = get_user_by( 'id', $author_id );

    if ( ! $author ) {
      return false;
    }

    // Check cache
    $old_data = $author->fictioneer_author_statistics;
    $last_update = fictioneer_get_last_fiction_update();

    if (
      ! empty( $last_update ) &&
      $old_data &&
      $old_data['last_modified'] >= $last_update
    ) {
      return $old_data;
    }

    $stories = get_posts(
      array(
        'post_type' => array( 'fcn_story' ),
        'post_status' => array( 'publish' ),
        'author' => $author_id,
        'numberposts' => -1,
        'orderby' => 'modified',
        'order' => 'DESC',
        'meta_query' => array(
          'relation' => 'OR',
          array(
            'key' => 'fictioneer_story_hidden',
            'value' => '0'
          ),
          array(
            'key' => 'fictioneer_story_hidden',
            'compare' => 'NOT EXISTS'
          ),
        ),
        'update_post_term_cache' => false
      )
    );

    $chapters = get_posts(
      array(
        'post_type' => array( 'fcn_chapter' ),
        'post_status' => array( 'publish' ),
        'author' => $author_id,
        'numberposts' => -1,
        'orderby' => 'modified',
        'order' => 'DESC',
        'meta_query' => array(
          'relation' => 'AND',
          array(
            'key'   => 'fictioneer_chapter_hidden',
            'value' => '0'
          ),
          array(
            'key'   => 'fictioneer_chapter_no_chapter',
            'value' => '0'
          )
        ),
        'update_post_term_cache' => false
      )
    );

    $word_count = 0;
    $comment_count = 0;

    foreach ( $chapters as $chapter ) {
      $word_count += get_post_meta( $chapter->ID, '_word_count', true );
      $comment_count += get_comments_number( $chapter );
    }

    $result = array(
      'story_count' => count( $stories ),
      'chapter_count' => count( $chapters ),
      'word_count' => $word_count,
      'word_count_short' => fictioneer_shorten_number( $word_count ),
      'last_modified' => time() * 1000,
      'comment_count' => $comment_count
    );

    update_user_meta( $author_id, 'fictioneer_author_statistics', $result );

    return $result;
  }
}

// =============================================================================
// SHORTEN NUMBER WITH LETTER
// =============================================================================

if ( ! function_exists( 'fictioneer_shorten_number' ) ) {
  /**
   * Shortens a number to a fractional with a letter
   *
   * @since Fictioneer 4.5
   *
   * @param int $number     The number to be shortened.
   * @param int $precision  Precision of the fraction. Default 1.
   *
   * @return string The minified number string.
   */

  function fictioneer_shorten_number( $number, $precision = 1 ) {
    // The letters are prefixed by a HAIR SPACE (&hairsp;)
    if ( $number < 1000 ) {
      return strval( $number );
    } else if ( $number < 1000000 ) {
      return number_format( $number / 1000, $precision ) . ' K';
    } else if ( $number < 1000000000 ) {
      return number_format( $number / 1000000, $precision ) . ' M';
    } else {
      return number_format( $number / 1000000000, $precision ) . ' B';
    }
  }
}

// =============================================================================
// VALIDATE ID
// =============================================================================

if ( ! function_exists( 'fictioneer_validate_id' ) ) {
  /**
   * Ensures an ID is a valid integer and positive; optionally checks whether the
   * associated post is of a certain types (or among an array of types)
   *
   * @since Fictioneer 4.7
   *
   * @param int          $id        The ID to validate.
   * @param string|array $for_type  Optional. The expected post type(s).
   *
   * @return int|boolean The validated ID or false if invalid.
   */

  function fictioneer_validate_id( $id, $for_type = [] ) {
    $safe_id = intval( $id );
    $types = is_array( $for_type ) ? $for_type : [ $for_type ];

    if ( empty( $safe_id ) || $safe_id < 0 ) {
      return false;
    }

    if ( ! empty( $for_type ) && ! in_array( get_post_type( $safe_id ), $types ) ) {
      return false;
    }

    return $safe_id;
  }
}

// =============================================================================
// GET VALIDATED AJAX USER
// =============================================================================

if ( ! function_exists( 'fictioneer_get_validated_ajax_user' ) ) {
  /**
   * Get the current user after performing AJAX validations
   *
   * @since Fictioneer 5.0
   *
   * @param string $nonce_name   Optional. The name of the nonce. Default 'nonce'.
   * @param string $nonce_value  Optional. The value of the nonce. Default 'fictioneer_nonce'.
   *
   * @return boolean|WP_User False if not valid, the current user object otherwise.
   */

  function fictioneer_get_validated_ajax_user( $nonce_name = 'nonce', $nonce_value = 'fictioneer_nonce' ) {
    // Setup
    $user = wp_get_current_user();

    // Validate
    if (
      ! $user->exists() ||
      ! check_ajax_referer( $nonce_value, $nonce_name, false )
    ) {
      return false;
    }

    return $user;
  }
}

// =============================================================================
// RATE LIMIT
// =============================================================================

/**
 * Checks rate limit globally or for an action via the session
 *
 * @since Fictioneer 5.7.1
 *
 * @param string   $action  The action to check for rate-limiting.
 *                          Defaults to 'fictioneer_global'.
 * @param int|null $max     Optional. Maximum number of requests.
 *                          Defaults to FICTIONEER_REQUESTS_PER_MINUTE.
 */

function fictioneer_check_rate_limit( $action = 'fictioneer_global', $max = null ) {
  if ( ! get_option( 'fictioneer_enable_rate_limits' ) ) {
    return;
  }

  // Start session if not already done
  if ( session_status() == PHP_SESSION_NONE ) {
    session_start();
  }

  // Initialize if not set
  if ( ! isset( $_SESSION[ $action ]['request_times'] ) ) {
    $_SESSION[ $action ]['request_times'] = [];
  }

  // Setup
  $current_time = microtime( true );
  $time_window = 60;
  $max = $max ? absint( $max ) : FICTIONEER_REQUESTS_PER_MINUTE;
  $max = max( 1, $max );

  // Filter out old timestamps
  $_SESSION[ $action ]['request_times'] = array_filter(
    $_SESSION[ $action ]['request_times'],
    function ( $time ) use ( $current_time, $time_window ) {
      return ( $current_time - $time ) < $time_window;
    }
  );

  // Limit exceeded?
  if ( count( $_SESSION[ $action ]['request_times'] ) >= $max ) {
    http_response_code( 429 ); // Too many requests
    exit;
  }

  // Record the current request time
  $_SESSION[ $action ]['request_times'][] = $current_time;
}

// =============================================================================
// KEY/VALUE STRING REPLACEMENT
// =============================================================================

if ( ! function_exists( 'fictioneer_replace_key_value' ) ) {
  /**
   * Replaces key/value pairs in a string
   *
   * @since Fictioneer 5.0
   *
   * @param string $text     Text that has key/value pairs to be replaced.
   * @param array  $args     The key/value pairs.
   * @param string $default  Optional. To be used if the the $text is empty or
   *                         if any key/value pair is invalid. Default ''.
   *
   * @return string The modified text.
   */

  function fictioneer_replace_key_value( $text, $args, $default = '' ) {
    // Setup
    $invalid = false;

    // Check if text exists
    if ( empty( $text ) ) {
      $text = $default;
    }

    // Replace key/value pairs
    foreach( $args as $key => $value ) {
      $value = (string) $value;

      // Abort if any value is invalid
      if ( empty( $value ) ) {
        $invalid = true;
        break;
      }

      // Replace string
      $text = str_replace( $key, $value, $text );
    }

    // Return default a key/value pair was invalid
    if ( $invalid ) {
      return $default;
    }

    // Return modified text
    return trim( $text );
  }
}

// =============================================================================
// CHECK USER CAPABILITIES
// =============================================================================

if ( ! function_exists( 'fictioneer_is_admin' ) ) {
  /**
   * Checks if an user is an administrator
   *
   * @since Fictioneer 5.0
   *
   * @param int|null $user_id  The user ID to check. Default to current user ID.
   *
   * @return boolean To be or not to be.
   */

  function fictioneer_is_admin( $user_id = null ) {
    // Setup
    $user_id = $user_id ? $user_id : get_current_user_id();

    // Abort conditions
    if ( ! $user_id ) {
      return false;
    }

    // Check capabilities
    $check = user_can( $user_id, 'administrator' );

    // Filter
    $check = apply_filters( 'fictioneer_filter_is_admin', $check, $user_id );

    // Return result
    return $check;
  }
}

if ( ! function_exists( 'fictioneer_is_author' ) ) {
  /**
   * Checks if an user is an author
   *
   * @since Fictioneer 5.0
   *
   * @param int|null $user_id  The user ID to check. Default to current user ID.
   *
   * @return boolean To be or not to be.
   */

  function fictioneer_is_author( $user_id = null ) {
    // Setup
    $user_id = $user_id ? $user_id : get_current_user_id();

    // Abort conditions
    if ( ! $user_id ) {
      return false;
    }

    // Check capabilities
    $check = user_can( $user_id, 'publish_posts' );

    // Filter
    $check = apply_filters( 'fictioneer_filter_is_author', $check, $user_id );

    // Return result
    return $check;
  }
}

if ( ! function_exists( 'fictioneer_is_moderator' ) ) {
  /**
   * Checks if an user is a moderator
   *
   * @since Fictioneer 5.0
   *
   * @param int|null $user_id  The user ID to check. Default to current user ID.
   *
   * @return boolean To be or not to be.
   */

  function fictioneer_is_moderator( $user_id = null ) {
    // Setup
    $user_id = $user_id ? $user_id : get_current_user_id();

    // Abort conditions
    if ( ! $user_id ) {
      return false;
    }

    // Check capabilities
    $check = user_can( $user_id, 'moderate_comments' );

    // Filter
    $check = apply_filters( 'fictioneer_filter_is_moderator', $check, $user_id );

    // Return result
    return $check;
  }
}

if ( ! function_exists( 'fictioneer_is_editor' ) ) {
  /**
   * Checks if an user is an editor
   *
   * @since Fictioneer 5.0
   *
   * @param int|null $user_id  The user ID to check. Default to current user ID.
   *
   * @return boolean To be or not to be.
   */

  function fictioneer_is_editor( $user_id = null ) {
    // Setup
    $user_id = $user_id ? $user_id : get_current_user_id();

    // Abort conditions
    if ( ! $user_id ) {
      return false;
    }

    // Check capabilities
    $check = user_can( $user_id, 'editor' ) || user_can( $user_id, 'administrator' );

    // Filter
    $check = apply_filters( 'fictioneer_filter_is_editor', $check, $user_id );

    // Return result
    return $check;
  }
}

// =============================================================================
// GET META FIELDS
// =============================================================================

if ( ! function_exists( 'fictioneer_get_field' ) ) {
  /**
   * Wrapper for get_post_meta
   *
   * @since Fictioneer 5.0
   *
   * @param string $field    Name of the meta field to retrieve.
   * @param int    $post_id  Optional. The ID of the post the field belongs to.
   *                         Defaults to current post ID.
   *
   * @return mixed The single field value.
   */

  function fictioneer_get_field( $field, $post_id = null ) {
    // Setup
    $post_id = absint( $post_id );
    $post_id = $post_id ?: get_the_ID();

    // Retrieve post meta
    return get_post_meta( $post_id, $field, true );
  }
}

if ( ! function_exists( 'fictioneer_get_content_field' ) ) {
  /**
   * Wrapper for fictioneer_get_field with content filers applied
   *
   * @since Fictioneer 5.0
   *
   * @param string $field    Name of the meta field to retrieve.
   * @param int    $post_id  Optional. The ID of the post the field belongs to.
   *                         Defaults to current post ID.
   *
   * @return string The single field value formatted as content.
   */

  function fictioneer_get_content_field( $field, $post_id = null ) {
    // Setup
    $content = fictioneer_get_field( $field, $post_id );

    // Apply default filter functions from the_content (but nothing else)
    $content = wptexturize( $content );
    $content = convert_chars( $content );
    $content = wpautop( $content );
    $content = shortcode_unautop( $content );
    $content = prepend_attachment( $content );

    // Return formatted/filtered content
    return $content;
  }
}

if ( ! function_exists( 'fictioneer_get_icon_field' ) ) {
  /**
   * Wrapper for fictioneer_get_field to get Font Awesome icon class
   *
   * @since Fictioneer 5.0
   *
   * @param string $field    Name of the meta field to retrieve.
   * @param int    $post_id  Optional. The ID of the post the field belongs to.
   *                         Defaults to current post ID.
   *
   * @return string The Font Awesome class.
   */

  function fictioneer_get_icon_field( $field, $post_id = null ) {
    // Setup
    $icon = fictioneer_get_field( $field, $post_id );
    $icon_object = json_decode( $icon ); // Check for ACF Font Awesome

    // Valid?
    if ( ! $icon_object && ( empty( $icon ) || ! str_contains( $icon, 'fa-' ) ) ) {
      return 'fa-solid fa-book';
    }

    // Return
    if ( $icon_object && property_exists( $icon_object, 'style' ) && property_exists( $icon_object, 'id' ) ) {
      return 'fa-' . $icon_object->style . ' fa-' . $icon_object->id;
    } else {
      return esc_attr( $icon );
    }
  }
}

// =============================================================================
// UPDATE META FIELDS
// =============================================================================

if ( ! function_exists( 'fictioneer_update_comment_meta' ) ) {
  /**
   * Wrapper to update comment meta
   *
   * If the meta value is truthy, the meta field is updated as normal.
   * If not, the meta field is deleted instead to keep the database tidy.
   *
   * @since 5.7.3
   *
   * @param int    $comment_id  The ID of the comment.
   * @param string $meta_key    The meta key to update.
   * @param mixed  $meta_value  The new meta value. If empty, the meta key will be deleted.
   * @param mixed  $prev_value  Optional. If specified, only updates existing metadata with this value.
   *                            Otherwise, update all entries. Default empty.
   *
   * @return int|bool Meta ID if the key didn't exist on update, true on successful update or delete,
   *                  false on failure or if the value passed to the function is the same as the one
   *                  that is already in the database.
   */

  function fictioneer_update_comment_meta( $comment_id, $meta_key, $meta_value, $prev_value = '' ) {
    if ( empty( $meta_value ) ) {
      return delete_comment_meta( $comment_id, $meta_key );
    } else {
      return update_comment_meta( $comment_id, $meta_key, $meta_value, $prev_value );
    }
  }
}

// =============================================================================
// GET COOKIE CONSENT
// =============================================================================

if ( ! function_exists( 'fictioneer_get_consent' ) && get_option( 'fictioneer_cookie_banner' ) ) {
  /**
   * Get cookie consent
   *
   * Checks the current user’s consent cookie for their preferences, either 'full'
   * or 'necessary' by default. Returns false if no consent cookie is set.
   *
   * @since 4.7
   *
   * @return boolean|string Either false or a string describing the level of consent.
   */

  function fictioneer_get_consent() {
    if ( ! isset( $_COOKIE['fcn_cookie_consent'] ) || $_COOKIE['fcn_cookie_consent'] === '' ) {
      return false;
    }

    return strval( $_COOKIE['fcn_cookie_consent'] );
  }
}

// =============================================================================
// SANITIZE INTEGER
// =============================================================================

/**
 * Sanitizes an integer with options for default, minimum, and maximum.
 *
 * @since 4.0
 *
 * @param mixed $value    The value to be sanitized.
 * @param int   $default  Default value if an invalid integer is provided. Default 0.
 * @param int   $min      Optional. Minimum value for the integer. Default is no minimum.
 * @param int   $max      Optional. Maximum value for the integer. Default is no maximum.
 *
 * @return int The sanitized integer.
 */

function fictioneer_sanitize_integer( $value, $default = 0, $min = null, $max = null ) {
  // Ensure $value is numeric in the first place
  if ( ! is_numeric( $value ) ) {
    return $default;
  }

  // Cast to integer
  $value = (int) $value;

  // Apply minimum limit if specified
  if ( $min !== null && $value < $min ) {
    return $min;
  }

  // Apply maximum limit if specified
  if ( $max !== null && $value > $max ) {
    return $max;
  }

  return $value;
}

// =============================================================================
// SANITIZE CHECKBOX
// =============================================================================

/**
 * Sanitizes a checkbox value into true or false
 *
 * @since 4.7
 * @link  https://www.php.net/manual/en/function.filter-var.php
 *
 * @param string|boolean $value  The checkbox value to be sanitized.
 *
 * @return boolean True or false.
 */

function fictioneer_sanitize_checkbox( $value ) {
  return filter_var( $value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
}

// =============================================================================
// SANITIZE ARGUMENTS
// =============================================================================

/**
 * Sanitizes an array of arguments
 *
 * @since 5.7.3
 *
 * @param array $args  Array of arguments to sanitize
 *
 * @return array The sanitized arguments.
 */

function fictioneer_sanitize_args( $args ) {
  $sanitized_args = [];

  foreach ( $args as $key => $value ) {
    if ( is_string( $value ) ) {
      $sanitized_args[ $key ] = sanitize_text_field( $value );
    } elseif ( is_numeric( $value ) ) {
      $sanitized_args[ $key ] = intval( $value );
    } elseif ( is_bool( $value ) ) {
      $sanitized_args[ $key ] = filter_var( $value, FILTER_VALIDATE_BOOLEAN );
    } elseif ( is_array( $value ) ) {
      $sanitized_args[ $key ] = fictioneer_sanitize_args( $value );
    } else {
      $sanitized_args[ $key ] = $value;
    }
  }

  return $sanitized_args;
}

// =============================================================================
// SHOW NON-PUBLIC CONTENT
// =============================================================================

/**
 * Wrapper for is_user_logged_in() with global public cache consideration
 *
 * If public caches are served to all users, including logged-in users, it is
 * necessary to render items that would normally be skipped for logged-out
 * users, such as the profile link. They are hidden via CSS, which works as
 * long as the 'logged-in' class is still set on the <body>.
 *
 * @since 5.0
 *
 * @return boolean True or false.
 */

function fictioneer_show_auth_content() {
  return is_user_logged_in() || ! empty( get_option( 'fictioneer_enable_public_cache_compatibility' ) );
}

// =============================================================================
// AJAX AUTHENTICATION
// =============================================================================

/**
 * Send user authentication status via AJAX
 *
 * @since 5.7.0
 */

function fictioneer_ajax_get_auth() {
  // Enabled?
  if ( ! get_option( 'fictioneer_enable_ajax_authentication' ) ) {
    wp_send_json_error(
      array( 'error' => __( 'Not allowed.', 'fictioneer' ) ),
      403
    );
  }

  // Setup
  $user = wp_get_current_user();
  $nonce = wp_create_nonce( 'fictioneer_nonce' );
  $nonce_html = '<input id="fictioneer-ajax-nonce" name="fictioneer-ajax-nonce" type="hidden" value="' . $nonce . '">';

  // Response
  wp_send_json_success(
    array(
      'loggedIn' => is_user_logged_in(),
      'isAdmin' => fictioneer_is_admin( $user->ID ),
      'isModerator' => fictioneer_is_moderator( $user->ID ),
      'isAuthor' => fictioneer_is_author( $user->ID ),
      'isEditor' => fictioneer_is_editor( $user->ID ),
      'nonce' => $nonce,
      'nonceHtml' => $nonce_html
    )
  );
}

if ( get_option( 'fictioneer_enable_ajax_authentication' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_get_auth', 'fictioneer_ajax_get_auth' );
  add_action( 'wp_ajax_nopriv_fictioneer_ajax_get_auth', 'fictioneer_ajax_get_auth' );
}

// =============================================================================
// FICTIONEER TRANSLATIONS
// =============================================================================

/**
 * Returns selected translations
 *
 * Adding theme options for all possible translations would be a pain,
 * so this is done with a function that can be filtered as needed. For
 * giving your site a personal touch.
 *
 * @since Fictioneer 5.0
 *
 * @param string  $key     Key for requested translation.
 * @param boolean $escape  Optional. Escape the string for safe use in
 *                         attributes. Default false.
 *
 * @return string The translation or an empty string if not found.
 */

function fcntr( $key, $escape = false ) {
  // Define default translations
  $strings = array(
    'account' => __( 'Account', 'fictioneer' ),
    'bookshelf' => __( 'Bookshelf', 'fictioneer' ),
    'admin' => _x( 'Admin', 'Caption for administrator badge label.', 'fictioneer' ),
    'anonymous_guest' => __( 'Anonymous Guest', 'fictioneer' ),
    'author' => _x( 'Author', 'Caption for author badge label.', 'fictioneer' ),
    'bbcodes_modal' => _x( 'BBCodes', 'Heading for BBCodes tutorial modal.', 'fictioneer' ),
    'blog' => _x( 'Blog', 'Blog page name, mainly used in breadcrumbs.', 'fictioneer' ),
    'bookmark' => __( 'Bookmark', 'fictioneer' ),
    'bookmarks' => __( 'Bookmarks', 'fictioneer' ),
    'warning_notes' => __( 'Warning Notes', 'fictioneer' ),
    'deleted_user' => __( 'Deleted User', 'fictioneer' ),
    'follow' => _x( 'Follow', 'Follow a story.', 'fictioneer' ),
    'follows' => _x( 'Follows', 'List of followed stories.', 'fictioneer' ),
    'forget' => _x( 'Forget', 'Forget story set to be read later.', 'fictioneer' ),
    'formatting' => _x( 'Formatting', 'Toggle for chapter formatting modal.', 'fictioneer' ),
    'formatting_modal' => _x( 'Formatting', 'Chapter formatting modal heading.', 'fictioneer' ),
    'frontpage' => _x( 'Home', 'Frontpage page name, mainly used in breadcrumbs.', 'fictioneer' ),
    'is_followed' => _x( 'Followed', 'Story is followed.', 'fictioneer' ),
    'is_read' => _x( 'Read', 'Story or chapter is marked as read.', 'fictioneer' ),
    'is_read_later' => _x( 'Read later', 'Story is marked to be read later.', 'fictioneer' ),
    'jump_to_comments' => __( 'Jump: Comments', 'fictioneer' ),
    'jump_to_bookmark' => __( 'Jump: Bookmark', 'fictioneer' ),
    'login' => __( 'Login', 'fictioneer' ),
    'login_modal' => _x( 'Login', 'Login modal heading.', 'fictioneer' ),
    'login_with' => _x( 'Log in with', 'OAuth 2.0 login option plus appended icon.', 'fictioneer' ),
    'logout' => __( 'Logout', 'fictioneer' ),
    'mark_read' => _x( 'Mark Read', 'Mark story as read.', 'fictioneer' ),
    'mark_unread' => _x( 'Mark Unread', 'Mark story as unread.', 'fictioneer' ),
    'moderator' => _x( 'Mod', 'Caption for moderator badge label', 'fictioneer' ),
    'next' => __( '<span class="on">Next</span><span class="off"><i class="fa-solid fa-caret-right"></i></span>', 'fictioneer' ),
    'no_bookmarks' => __( 'No bookmarks.', 'fictioneer' ),
    'password' => __( 'Password', 'fictioneer' ),
    'previous' => __( '<span class="off"><i class="fa-solid fa-caret-left"></i></span><span class="on">Previous</span>', 'fictioneer' ),
    'read_later' => _x( 'Read Later', 'Remember a story to be read later.', 'fictioneer' ),
    'read_more' => _x( 'Read More', 'Read more of a post.', 'fictioneer' ),
    'reminders' => _x( 'Reminders', 'List of stories to read later.', 'fictioneer' ),
    'site_settings' => __( 'Site Settings', 'fictioneer' ),
    'story_blog' => _x( 'Blog', 'Blog tab of the story.', 'fictioneer' ),
    'subscribe' => _x( 'Subscribe', 'Subscribe to a story.', 'fictioneer' ),
    'unassigned_group' => _x( 'Unassigned', 'Chapters not assigned to group.', 'fictioneer' ),
    'unfollow' => _x( 'Unfollow', 'Stop following a story.', 'fictioneer' ),
    'E' => _x( 'E', 'Age rating E for Everyone.', 'fictioneer' ),
    'T' => _x( 'T', 'Age rating T for Teen.', 'fictioneer' ),
    'M' => _x( 'M', 'Age rating M for Mature.', 'fictioneer' ),
    'A' => _x( 'A', 'Age rating A for Adult.', 'fictioneer' ),
    'Everyone' => _x( 'Everyone', 'Age rating Everyone.', 'fictioneer' ),
    'Teen' => _x( 'Teen', 'Age rating Teen.', 'fictioneer' ),
    'Mature' => _x( 'Mature', 'Age rating Mature.', 'fictioneer' ),
    'Adult' => _x( 'Adult', 'Age rating Adult.', 'fictioneer' ),
    'Completed' => _x( 'Completed', 'Completed story status.', 'fictioneer' ),
    'Ongoing' => _x( 'Ongoing', 'Ongoing story status', 'fictioneer' ),
    'Hiatus' => _x( 'Hiatus', 'Hiatus story status', 'fictioneer' ),
    'Oneshot' => _x( 'Oneshot', 'Oneshot story status', 'fictioneer' ),
    'Canceled' => _x( 'Canceled', 'Canceled story status', 'fictioneer' ),
    'comment_anchor' => _x( '<i class="fa-solid fa-link"></i>', 'Text or icon for paragraph anchor in comments.', 'fictioneer' ),
    'bbcode_b' => _x( '<code>[b]</code><strong>Bold</strong><code>[/b]</code> of you to assume I have a plan.', 'fictioneer' ),
    'bbcode_i' => _x( 'Deathbringer, emphasis on <code>[i]</code><em>death</em><code>[/i]</code>.', 'fictioneer' ),
    'bbcode_s' => _x( 'I’m totally <code>[s]</code><strike>crossed out</strike><code>[/s]</code> by this.', 'fictioneer' ),
    'bbcode_li' => _x( '<ul><li class="comment-list-item">Listless I’m counting my <code>[li]</code>bullets<code>[/li]</code>.</li></ul>', 'fictioneer' ),
    'bbcode_img' => _x( '<code>[img]</code>https://www.agine.this<code>[/img]</code> %s', 'BBCode example.', 'fictioneer' ),
    'bbcode_link' => _x( '<code>[link]</code><a href="http://topwebfiction.com/" target="_blank" class="link">http://topwebfiction.com</a><code>[/link]</code>.', 'BBCode example.', 'fictioneer' ),
    'bbcode_link_name' => _x( '<code>[link=https://www.n.ot]</code><a href="http://topwebfiction.com/" class="link">clickbait</a><code>[/link]</code>.', 'BBCode example.', 'fictioneer' ),
    'bbcode_quote' => _x( '<blockquote><code>[quote]</code>… me like my landlord!<code>[/quote]</code></blockquote>', 'BBCode example.', 'fictioneer' ),
    'bbcode_spoiler' => _x( '<code>[spoiler]</code><span class="spoiler">Spanish Inquisition!</span><code>[/spoiler]</code>', 'BBCode example.', 'fictioneer' ),
    'bbcode_ins' => _x( '<code>[ins]</code><ins>Insert</ins><code>[/ins]</code> more bad puns!', 'BBCode example.', 'fictioneer' ),
    'bbcode_del' => _x( '<code>[del]</code><del>Delete</del><code>[/del]</code> your browser history!', 'BBCode example.', 'fictioneer' ),
    'log_in_with' => _x( 'Enter your details or log in with:', 'Comment form login note.', 'fictioneer' ),
    'logged_in_as' => _x( '<span>Logged in as <strong><a href="%1$s">%2$s</a></strong>. <a class="logout-link" href="%3$s" data-click="logout">Log out?</a></span>', 'Comment form logged-in note.', 'fictioneer' ),
    'accept_privacy_policy' => _x( 'I accept the <b><a class="link" href="%s" target="_blank">privacy policy</a></b>.', 'Comment form privacy checkbox.', 'fictioneer' ),
    'save_in_cookie' => _x( 'Save in cookie for next time.', 'Comment form cookie checkbox.', 'fictioneer' ),
  );

  // Filter translations
  $strings = apply_filters( 'fictioneer_filter_translations', $strings );

  // Return requested translation if defined...
  if ( array_key_exists( $key, $strings ) ) {
    return $escape ? esc_attr( $strings[ $key ] ) : $strings[ $key ];
  }

  // ... otherwise return empty string
  return '';
}

// =============================================================================
// BALANCE PAGINATION ARRAY
// =============================================================================

/**
 * Balances pagination array
 *
 * Takes an number array of pagination pages and balances the items around the
 * current page number, replacing anything above the keep threshold with ellipses.
 * E.g. 1 … 7, 8, [9], 10, 11 … 20.
 *
 * @since 5.0
 *
 * @param array|int $pages     Array of pages to balance. If an integer is provided,
 *                             it is converted to a number array.
 * @param int       $current   Current page number.
 * @param int       $keep      Optional. Balancing factor to each side. Default 2.
 * @param string    $ellipses  Optional. String for skipped numbers. Default '…'.
 *
 * @return array The balanced array.
 */

function fictioneer_balance_pagination_array( $pages, $current, $keep = 2, $ellipses = '…' ) {
  // Setup
  $keep = 2;
  $max_pages = is_array( $pages ) ? count( $pages ) : $pages;
  $steps = is_array( $pages ) ? $pages : [];

  if ( ! is_array( $pages ) ) {
    for ( $i = 1; $i <= $max_pages; $i++ ) {
      $steps[] = $i;
    }
  }

  // You know, I wrote this but don't really get it myself...
  if ( $max_pages - $keep * 2 > $current ) {
    $start = $current + $keep;
    $end = $max_pages - $keep + 1;

    for ( $i = $start; $i < $end; $i++ ) {
      unset( $steps[ $i ] );
    }

    array_splice( $steps, count( $steps ) - $keep + 1, 0, $ellipses );
  }

  // It certainly does math...
  if ( $current - $keep * 2 >= $keep ) {
    $start = $keep - 1;
    $end = $current - $keep - 1;

    for ( $i = $start; $i < $end; $i++ ) {
      unset( $steps[ $i ] );
    }

    array_splice( $steps, $keep - 1, 0, $ellipses );
  }

  return $steps;
}

// =============================================================================
// CHECK WHETHER COMMENTING IS DISABLED
// =============================================================================

if ( ! function_exists( 'fictioneer_is_commenting_disabled' ) ) {
  /**
   * Check whether commenting is disabled
   *
   * Differs from comments_open() in the regard that it does not hide the whole
   * comment section but does not allow new comments to be posted.
   *
   * @since 5.0
   * @see fictioneer_get_field()
   *
   * @param int|null $post_id  Post ID the comments are for. Defaults to current post ID.
   *
   * @return boolean True or false.
   */

  function fictioneer_is_commenting_disabled( $post_id = null ) {
    return fictioneer_get_field( 'fictioneer_disable_commenting', $post_id ) || get_option( 'fictioneer_disable_commenting' );
  }
}

// =============================================================================
// CHECK DISALLOWED KEYS WITH OFFENSES RETURNED
// =============================================================================

if ( ! function_exists( 'fictioneer_check_comment_disallowed_list' ) ) {
  /**
   * Checks whether a comment contains disallowed characters or words and
   * returns the offenders within the comment content
   *
   * @since 5.0
   * @see wp_check_comment_disallowed_list()
   *
   * @param string $author      The author of the comment.
   * @param string $email       The email of the comment.
   * @param string $url         The url used in the comment.
   * @param string $comment     The comment content
   * @param string $user_ip     The comment author's IP address.
   * @param string $user_agent  The author's browser user agent.
   *
   * @return array Tuple of true/false [0] and offenders [1] as array.
   */

  function fictioneer_check_comment_disallowed_list( $author, $email, $url, $comment, $user_ip, $user_agent ) {
    // Implementation is the same as wp_check_comment_disallowed_list(...)
    $mod_keys = trim( get_option( 'disallowed_keys' ) );

    if ( '' === $mod_keys ) {
      return [false, []]; // If moderation keys are empty.
    }

    // Ensure HTML tags are not being used to bypass the list of disallowed characters and words.
    $comment_without_html = wp_strip_all_tags( $comment );

    $words = explode( "\n", $mod_keys );

    foreach ( (array) $words as $word ) {
      $word = trim( $word );

      // Skip empty lines.
      if ( empty( $word ) ) {
        continue; }

      // Do some escaping magic so that '#' chars in the spam words don't break things:
      $word = preg_quote( $word, '#' );
      $matches = false;

      $pattern = "#$word#i";
      if ( preg_match( $pattern, $author )
        || preg_match( $pattern, $email )
        || preg_match( $pattern, $url )
        || preg_match( $pattern, $comment, $matches )
        || preg_match( $pattern, $comment_without_html, $matches )
        || preg_match( $pattern, $user_ip )
        || preg_match( $pattern, $user_agent )
      ) {
        return [true, $matches];
      }
    }

    return [false, []];
  }
}

// =============================================================================
// BBCODES
// =============================================================================

if ( ! function_exists( 'fictioneer_bbcodes' ) ) {
  /**
   * Interprets BBCodes into HTML
   *
   * Note: Spoilers do not work properly if wrapping multiple lines or other codes.
   *
   * @since Fictioneer 4.0
   * @link https://stackoverflow.com/a/17508056/17140970
   *
   * @param string $content  The content.
   *
   * @return string The content with interpreted BBCodes.
   */

  function fictioneer_bbcodes( $content ) {
    // Setup
    $img_search = 'https:[^\"\'|;<>\[\]]+?\.(?:png|jpg|jpeg|gif|webp|svg|avif|tiff).*?';
    $url_search = '(http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?';

    // Deal with some multi-line spoiler issues
    if ( preg_match_all( '/\[spoiler](.+?)\[\/spoiler]/is', $content, $spoilers, PREG_PATTERN_ORDER ) ) {
      foreach ( $spoilers[0] as $spoiler ) {
        $replace = str_replace( '<p></p>', ' ', $spoiler );
        $replace = preg_replace( '/\[quote](.+?)\[\/quote]/is', '<blockquote class="spoiler">$1</blockquote>', $replace );

        $content = str_replace( $spoiler, $replace, $content );
      }
    }

    // Possible patterns
    $patterns = array(
      '/\[spoiler]\[quote](.+?)\[\/quote]\[\/spoiler]/is',
      '/\[spoiler](.+?)\[\/spoiler]/i',
      '/\[spoiler](.+?)\[\/spoiler]/is',
      '/\[b](.+?)\[\/b]/i',
      '/\[i](.+?)\[\/i]/i',
      '/\[s](.+?)\[\/s]/i',
      '/\[quote](.+?)\[\/quote]/is',
      '/\[ins](.+?)\[\/ins]/is',
      '/\[del](.+?)\[\/del]/is',
      '/\[li](.+?)\[\/li]/i',
      "/\[link.*]\[img]($img_search)\[\/img]\[\/link]/i",
      "/\[img]($img_search)\[\/img]/i",
      "/\[link\]($url_search)\[\/link\]/",
      "(\[link\=[\"']?($url_search)[\"']?\](.+?)\[/link\])",
      '/\[anchor]([^\"\'|;<>\[\]]+?)\[\/anchor]/i'
    );

    // HTML replacements
    $replacements = array(
      '<blockquote class="spoiler">$1</blockquote>',
      '<span class="spoiler">$1</span>',
      '<div class="spoiler">$1</div>',
      '<strong>$1</strong>',
      '<em>$1</em>',
      '<strike>$1</strike>',
      '<blockquote>$1</blockquote>',
      '<ins>$1</ins>',
      '<del>$1</del>',
      '<div class="comment-list-item">$1</div>',
      '<span class="comment-image-consent-wrapper"><button type="button" class="button _secondary consent-button" title="$1">' . _x( '<i class="fa-solid fa-image"></i> Show Image', 'Comment image consent wrapper button.', 'fictioneer' ) . '</button><a href="$1" class="comment-image-link" rel="noreferrer noopener nofollow" target="_blank"><img class="comment-image" data-src="$1"></a></span>',
      '<span class="comment-image-consent-wrapper"><button type="button" class="button _secondary consent-button" title="$1">' . _x( '<i class="fa-solid fa-image"></i> Show Image', 'Comment image consent wrapper button.', 'fictioneer' ) . '</button><img class="comment-image" data-src="$1"></span>',
      "<a href=\"$1\" rel=\"noreferrer noopener nofollow\">$1</a>",
      "<a href=\"$1\" rel=\"noreferrer noopener nofollow\">$5</a>",
      '<a href="#$1" data-block="center" class="comment-anchor">:anchor:</a>'
    );

    // Pattern replace
    $content = preg_replace( $patterns, $replacements, $content );

    // Icons
    $content = str_replace( ':anchor:', fcntr( 'comment_anchor' ), $content );

    return $content;
  }
}

// =============================================================================
// GET TAXONOMY NAMES
// =============================================================================

if ( ! function_exists( 'fictioneer_get_taxonomy_names' ) ) {
  /**
   * Get all taxonomies of a post
   *
   * @since 5.0.20
   *
   * @param int     $post_id  ID of the post to get the taxonomies of.
   * @param boolean $flatten  Whether to flatten the result. Default false.
   *
   * @return array Array with all taxonomies.
   */

  function fictioneer_get_taxonomy_names( $post_id, $flatten = false ) {
    // Setup
    $t = [];
    $t['tags'] = get_the_tags( $post_id );
    $t['fandoms'] = get_the_terms( $post_id, 'fcn_fandom' );
    $t['characters'] = get_the_terms( $post_id, 'fcn_character' );
    $t['warnings'] = get_the_terms( $post_id, 'fcn_content_warning' );
    $t['genres'] = get_the_terms( $post_id, 'fcn_genre' );

    // Validate
    foreach ( $t as $key => $tax ) {
      $t[ $key ] = is_array( $t[ $key ] ) ? $t[ $key ] : [];
    }

    // Extract
    foreach ( $t as $key => $tax ) {
      $t[ $key ] = array_map( function( $a ) { return $a->name; }, $t[ $key ] );
    }

    // Return flattened
    if ( $flatten ) {
      return array_merge( $t['tags'], $t['fandoms'], $t['characters'], $t['warnings'], $t['genres'] );
    }

    // Return without empty arrays
    return array_filter( $t, 'count' );
  }
}

// =============================================================================
// GET FONTS
// =============================================================================

if ( ! function_exists( 'fictioneer_get_fonts' ) ) {
  /**
   * Returns array of font items
   *
   * @since 5.1.1
   *
   * @return array Font items (css, name, and alt).
   */

  function fictioneer_get_fonts() {
    // Make sure 'Open Sans' is always in first or second place
    if ( FICTIONEER_PRIMARY_FONT_CSS !== 'Open Sans' ) {
      $fonts = array(
        ['css' => FICTIONEER_PRIMARY_FONT_CSS, 'name' => FICTIONEER_PRIMARY_FONT_NAME],
        ['css' => 'Open Sans', 'name' => _x( 'Open Sans', 'Font name.', 'fictioneer' )]
      );
    } else {
      $fonts = array(
        ['css' => FICTIONEER_PRIMARY_FONT_CSS, 'name' => FICTIONEER_PRIMARY_FONT_NAME]
      );
    }

    // Setup default fonts
    $fonts = array_merge(
      $fonts,
      array(
        ['css' => '', 'name' => _x( 'System Font', 'Font name.', 'fictioneer' )],
        ['css' => 'Lato', 'name' => _x( 'Lato', 'Font name.', 'fictioneer' )],
        ['css' => 'Helvetica Neue', 'name' => _x( 'Helvetica Neue', 'Font name.', 'fictioneer' ), 'alt' => 'Arial'],
        ['css' => 'Georgia', 'name' => _x( 'Georgia', 'Font name.', 'fictioneer' )],
        ['css' => 'Roboto Mono', 'name' => _x( 'Roboto Mono', 'Font name.', 'fictioneer' )],
        ['css' => 'Roboto Serif', 'name' => _x( 'Roboto Serif', 'Font name.', 'fictioneer' )],
        ['css' => 'Cormorant Garamond', 'name' => _x( 'Cormorant Garamond', 'Font name.', 'fictioneer' ), 'alt' => 'Garamond'],
        ['css' => 'Crimson Text', 'name' => _x( 'Crimson Text', 'Font name.', 'fictioneer' )],
        ['css' => 'OpenDyslexic', 'name' => _x( 'Open Dyslexic', 'Font name.', 'fictioneer' )]
      )
    );

    // Apply filters and return
    return apply_filters( 'fictioneer_filter_fonts', $fonts );
  }
}

// =============================================================================
// GET FONT COLORS
// =============================================================================

if ( ! function_exists( 'fictioneer_get_font_colors' ) ) {
  /**
   * Returns array of font color items
   *
   * @since 5.1.1
   *
   * @return array Font items (css and name).
   */

  function fictioneer_get_font_colors() {
    // Setup default font colors
    $colors = array(
      ['css' => 'var(--fg-tinted)', 'name' => _x( 'Tinted', 'Chapter font color name.', 'fictioneer' )],
      ['css' => 'var(--fg-500)', 'name' => _x( 'Baseline', 'Chapter font color name.', 'fictioneer' )],
      ['css' => 'var(--fg-600)', 'name' => _x( 'Low', 'Chapter font color name.', 'fictioneer' )],
      ['css' => 'var(--fg-700)', 'name' => _x( 'Lower', 'Chapter font color name.', 'fictioneer' )],
      ['css' => 'var(--fg-800)', 'name' => _x( 'Lowest', 'Chapter font color name.', 'fictioneer' )],
      ['css' => 'var(--fg-400)', 'name' => _x( 'High', 'Chapter font color name.', 'fictioneer' )],
      ['css' => 'var(--fg-300)', 'name' => _x( 'Higher', 'Chapter font color name.', 'fictioneer' )],
      ['css' => 'var(--fg-200)', 'name' => _x( 'Highest', 'Chapter font color name.', 'fictioneer' )],
      ['css' => '#fff', 'name' => _x( 'White', 'Chapter font color name.', 'fictioneer' )],
      ['css' => '#999', 'name' => _x( 'Gray', 'Chapter font color name.', 'fictioneer' )],
      ['css' => '#000', 'name' => _x( 'Black', 'Chapter font color name.', 'fictioneer' )]
    );

    // Apply filters and return
    return apply_filters( 'fictioneer_filter_font_colors', $colors );
  }
}

// =============================================================================
// ARRAY FROM COMMA SEPARATED STRING
// =============================================================================

/**
 * Explodes string into an array
 *
 * Strips lines breaks, trims whitespaces, and removes empty elements.
 * Values might not be unique.
 *
 * @since 5.1.3
 *
 * @param string $string  The string to explode.
 *
 * @return array The string content as array.
 */

function fictioneer_explode_list( $string ) {
  if ( empty( $string ) ) {
    return [];
  }

  $string = str_replace( ["\n", "\r"], '', $string ); // Remove line breaks
  $array = explode( ',', $string );
  $array = array_map( 'trim', $array ); // Remove extra whitespaces
  $array = array_filter( $array, 'strlen' ); // Remove empty elements
  $array = is_array( $array ) ? $array : [];

  return $array;
}

// =============================================================================
// BUILD FRONTEND PROFILE NOTICE
// =============================================================================

if ( ! function_exists( 'fictioneer_notice' ) ) {
  /**
   * Render or return a frontend notice element
   *
   * @since 5.2.5
   *
   * @param string $message   The notice to show.
   * @param string $type      Optional. The notice type. Default 'warning'.
   * @param bool   $display   Optional. Whether to render or return. Default true.
   *
   * @return void|string The build HTML or nothing if rendered.
   */

  function fictioneer_notice( $message, $type = 'warning', $display = true ) {
    ob_start();
    // Start HTML ---> ?>
    <div class="notice _<?php echo esc_attr( $type ); ?>">
      <?php if ( $type === 'warning' ) : ?>
        <i class="fa-solid fa-triangle-exclamation"></i>
      <?php endif; ?>
      <div><?php echo $message; ?></div>
    </div>
    <?php // <--- End HTML
    $output =  ob_get_clean();

    if ( $display ) {
      echo $output;
    } else {
      return $output;
    }
  }
}

// =============================================================================
// MINIFY HTML
// =============================================================================

if ( ! function_exists( 'fictioneer_minify_html' ) ) {
  /**
   * Minifies a HTML string
   *
   * This is not safe for `<pre>` or `<code>` tags!
   *
   * @since 5.4.0
   *
   * @param string $html  The HTML string to be minified.
   *
   * @return string The minified HTML string.
   */

  function fictioneer_minify_html( $html ) {
    return preg_replace( '/\s+/', ' ', trim( $html ) );
  }
}

// =============================================================================
// GET CLEAN CURRENT URL
// =============================================================================

if ( ! function_exists( 'fictioneer_get_clean_url' ) ) {
  /**
   * Returns URL without query arguments or page number
   *
   * @since 5.4.0
   *
   * @return string The clean URL.
   */

  function fictioneer_get_clean_url() {
    global $wp;

    // Setup
    $url = home_url( $wp->request );

    // Remove page (if any)
    $url = preg_replace( '/\/page\/\d+\/$/', '', $url );
    $url = preg_replace( '/\/page\/\d+$/', '', $url );

    // Return cleaned URL
    return $url;
  }
}

// =============================================================================
// GET AUTHOR IDS OF POST
// =============================================================================

if ( ! function_exists( 'fictioneer_get_post_author_ids' ) ) {
  /**
   * Returns array of author IDs for a post ID
   *
   * @since 5.4.8
   *
   * @param int $post_id  The post ID.
   *
   * @return array The author IDs.
   */

  function fictioneer_get_post_author_ids( $post_id ) {
    $author_ids = fictioneer_get_field( 'fictioneer_story_co_authors', $post_id ) ?? [];
		$author_ids = is_array( $author_ids ) ? $author_ids : [];
		array_unshift( $author_ids, get_post_field( 'post_author', $post_id ) );

    return array_unique( $author_ids );
  }
}

// =============================================================================
// DELETE TRANSIENTS THAT INCLUDE A STRING
// =============================================================================

if ( ! function_exists( 'fictioneer_delete_transients_like' ) ) {
  /**
   * Delete Transients with a like key and return the count deleted
   *
   * @since 5.4.9
   *
   * @param string  $partial_key  String that is part of the key.
   * @param boolean $fast         Optional. Whether to delete with a single SQL query or
   *                              loop each Transient with delete_transient(), which can
   *                              trigger hooked actions (if any). Default true.
   *
   * @return int Count of deleted Transients.
   */

  function fictioneer_delete_transients_like( $partial_key, $fast = true ) {
    // Globals
    global $wpdb;

    // Setup
    $count = 0;

    // Fast?
    if ( $fast ) {
      // Prepare SQL
      $sql = $wpdb->prepare(
        "DELETE FROM $wpdb->options WHERE `option_name` LIKE %s OR `option_name` LIKE %s",
        "%_transient%{$partial_key}%",
        "%_transient_timeout%{$partial_key}%"
      );

      // Query
      $count = $wpdb->query( $sql ) / 2; // Count Transient and Transient timeout as one
    } else {
      // Prepare SQL
      $sql = $wpdb->prepare(
        "SELECT `option_name` AS `name` FROM $wpdb->options WHERE `option_name` LIKE %s",
        "_transient%{$partial_key}%"
      );

      // Query
      $transients = $wpdb->get_col( $sql );

      // Build full keys and delete
      foreach ( $transients as $transient ) {
        $key = str_replace( '_transient_', '', $transient );
        $count += delete_transient( $key ) ? 1 : 0;
      }
    }

    // Return count
    return $count;
  }
}

// =============================================================================
// GET OPTION PAGE LINK
// =============================================================================

if ( ! function_exists( 'fictioneer_get_assigned_page_link' ) ) {
  /**
   * Returns permalink for an assigned page or null
   *
   * @since 5.4.9
   *
   * @param string $option  The option name of the page assignment.
   *
   * @return string|null The permalink or null.
   */

  function fictioneer_get_assigned_page_link( $option ) {
    // Setup
    $page_id = get_option( $option );

    // Null if no page has been selected (null or -1)
    if ( empty( $page_id ) || $page_id < 0 ) {
      return null;
    }

    // Get permalink from options or post
    $link = get_option( "{$option}_link" );

    if ( empty( $link ) ) {
      $link = get_permalink( $page_id );
      update_option( "{$option}_link", $link, true ); // Save for next time
    }

    // Return
    return $link;
  }
}

// =============================================================================
// MULTI SAVE GUARD
// =============================================================================

if ( ! function_exists( 'fictioneer_multi_save_guard' ) ) {
  /**
   * Prevents multi-fire in update hooks
   *
   * Unfortunately, the block editor always fires twice: once as REST request and
   * followed by WP_POST. Only the first will have the correct parameters such as
   * $update set, the second is technically no longer an update. Since blocking
   * the follow-up WP_POST would block programmatically triggered actions, there
   * is no other choice but to block the REST request and live with it.
   *
   * @since 5.5.2
   *
   * @param int $post_id  The ID of the updated post.
   *
   * @return boolean True if NOT allowed, false otherwise.
   */

  function fictioneer_multi_save_guard( $post_id ) {
    if (
      ( defined('REST_REQUEST') && REST_REQUEST ) ||
      wp_is_post_autosave( $post_id ) ||
      wp_is_post_revision( $post_id ) ||
      get_post_status( $post_id ) === 'auto-draft'
    ) {
      return true;
    }

    // Pass
    return false;
  }
}

// =============================================================================
// GET TOTAL WORD COUNT FOR ALL STORIES
// =============================================================================

if ( ! function_exists( 'fictioneer_get_stories_total_word_count' ) ) {
  /**
   * Return the total word count of all published stories
   *
   * @since 4.0
   * @see fictioneer_get_story_data()
   */

  function fictioneer_get_stories_total_word_count() {
    // Look for cached value
    $cached_word_count = get_transient( 'fictioneer_stories_total_word_count' );

    // Return cached value if found
    if ( $cached_word_count ) {
      return $cached_word_count;
    }

    // Setup
  	$word_count = 0;

    // Query all stories
    $stories = get_posts(
      array(
        'numberposts' => -1,
        'post_type' => 'fcn_story',
        'post_status' => 'publish',
        'no_found_rows' => true
      )
    );

    // Sum of all word counts
    foreach( $stories as $story ) {
      $story_data = fictioneer_get_story_data( $story->ID, false ); // Does not refresh comment count!
  		$word_count += $story_data['word_count'];
  	}

    // Cache for next time (24 hours)
    set_transient( 'fictioneer_stories_total_word_count', $word_count );

    // Return newly calculated value
  	return $word_count;
  }
}

// =============================================================================
// USER SELF-DELETE
// =============================================================================

/**
 * Deletes the current user's account and anonymized their comments
 *
 * @since Fictioneer 5.6.0
 *
 * @return bool True if the user was successfully deleted, false otherwise.
 */

function fictioneer_delete_my_account() {
  // Setup
  $current_user = wp_get_current_user();

  // Guard
  if (
    ! $current_user ||
    $current_user->ID === 1 ||
    in_array( 'administrator', $current_user->roles ) ||
    ! current_user_can( 'fcn_allow_self_delete' )
  ) {
    return false;
  }

  // Update comments
  $comments = get_comments( array( 'user_id' => $current_user->ID ) );

  foreach ( $comments as $comment ) {
    wp_update_comment(
      array(
        'comment_ID' => $comment->comment_ID,
        'user_ID' => 0,
        'comment_author' => fcntr( 'deleted_user' ),
        'comment_author_email' => '',
        'comment_author_IP' => '',
        'comment_agent' => '',
        'comment_author_url' => ''
      )
    );

    // Make absolutely sure no comment subscriptions remain
    fictioneer_update_comment_meta( $comment->comment_ID, 'fictioneer_send_notifications', false );

    // Retain some (redacted) data in case there was a mistake
    add_comment_meta( $comment->comment_ID, 'fictioneer_deleted_user_id', $current_user->ID );
    add_comment_meta( $comment->comment_ID, 'fictioneer_deleted_username_substr', substr( $current_user->user_login, 0, 3 ) );
    add_comment_meta( $comment->comment_ID, 'fictioneer_deleted_email_substr', substr( $comment->comment_author_email, 0, 3 ) );
  }

  // Delete user
  if ( wp_delete_user( $current_user->ID ) ) {
    return true;
  }

  // Failure
  return false;
}

// =============================================================================
// RESCUE EMPTY ARRAY AS [0]
// =============================================================================

/**
 * Returns the given array or an array with a single 0 if empty
 *
 * Prevents an array from being empty, which is useful for 'post__in' query
 * arguments that cannot deal with empty arrays.
 *
 * @since Fictioneer 5.6.0
 *
 * @param array $array  The input array to check.
 *
 * @return array The original array or [0].
 */

function fictioneer_rescue_array_zero( $array ) {
  return empty( $array ) ? [0] : $array;
}

// =============================================================================
// REDIRECT TO 404
// =============================================================================

if ( ! function_exists( 'fictioneer_redirect_to_404' ) ) {
  /**
   * Redirects the current request to the WordPress 404 page
   *
   * @since Fictioneer 5.6.0
   *
   * @global WP_Query $wp_query  The main WP_Query instance.
   */

  function fictioneer_redirect_to_404() {
    global $wp_query;

    // Remove scripts to avoid errors
    add_action( 'wp_print_scripts', function() {
      wp_dequeue_script( 'fictioneer-chapter-scripts' );
      wp_dequeue_script( 'fictioneer-suggestion-scripts' );
      wp_dequeue_script( 'fictioneer-tts-scripts' );
      wp_dequeue_script( 'fictioneer-story-scripts' );
    }, 99 );

    // Set query to 404
    $wp_query->set_404();
    status_header( 404 );
    nocache_headers();
    get_template_part( 404 );

    // Terminate
    exit();
  }
}

// =============================================================================
// PREVIEW ACCESS VERIFICATION
// =============================================================================

if ( ! function_exists( 'fictioneer_verify_unpublish_access' ) ) {
  /**
   * Verifies access to unpublished posts (not drafts)
   *
   * @since Fictioneer 5.6.0
   *
   * @return boolean True if access granted, false otherwise.
   */

  function fictioneer_verify_unpublish_access( $post_id ) {
    // Setup
    $post = get_post( $post_id );

    // Always let owner to pass
    if ( get_current_user_id() === absint( $post->post_author ) ) {
      return true;
    }

    // Always let administrators pass
    if ( current_user_can( 'manage_options' ) ) {
      return true;
    }

    // Check capability for post type
    if ( $post->post_status === 'private' ) {
      switch ( $post->post_type ) {
        case 'post':
          return current_user_can( 'edit_private_posts' );
          break;
        case 'page':
          return current_user_can( 'edit_private_pages' );
          break;
        case 'fcn_chapter':
          return current_user_can( 'read_private_fcn_chapters' );
          break;
        case 'fcn_story':
          return current_user_can( 'read_private_fcn_stories' );
          break;
        case 'fcn_recommendation':
          return current_user_can( 'read_private_fcn_recommendations' );
          break;
        case 'fcn_collection':
          return current_user_can( 'read_private_fcn_collections' );
          break;
        default:
          return current_user_can( 'edit_others_posts' );
      }
    }

    // Drafts are handled by WordPress
    return false;
  }
}

// =============================================================================
// ADD ACTION TO SAVE/TRASH/UNTRASH/DELETE HOOKS WITH POST ID
// =============================================================================

/**
 * Adds callback to save, trash, untrash, and delete hooks (1 argument)
 *
 * This helper saves some time/space adding a callback action to all four
 * default post operations. But only with the first argument: post_id.
 *
 * @since Fictioneer 5.6.3
 *
 * @param callable $function  The callback function to be added.
 * @param int      $priority  Optional. Used to specify the order in which the
 *                            functions associated with a particular action are
 *                            executed. Default 10. Lower numbers correspond with
 *                            earlier execution, and functions with the same
 *                            priority are executed in the order in which they
 *                            were added to the action.
 *
 * @return true Will always return true.
 */

function fictioneer_add_stud_post_actions( $function, $priority = 10 ) {
  $hooks = ['save_post', 'trashed_post', 'delete_post', 'untrash_post'];

  foreach ( $hooks as $hook ) {
    add_action( $hook, $function, $priority );
  }

  return true;
}

?>
