<?php

// =============================================================================
// CHECK IF URL EXISTS
// =============================================================================

if ( ! function_exists( 'fictioneer_url_exists' ) ) {
  /**
   * Checks whether an URL exists.
   *
   * @since Fictioneer 4.0
   * @link  https://www.geeksforgeeks.org/how-to-check-the-existence-of-url-in-php/
   *
   * @param string $url The URL to check.
   *
   * @return boolean True if the URL exists and false otherwise.
   */

  function fictioneer_url_exists( $url ) {
    if ( ! $url ) return false;

    $curl = curl_init( $url );
    curl_setopt( $curl, CURLOPT_NOBODY, true );

    if ( curl_exec( $curl ) ) {
      $statusCode = curl_getinfo( $curl, CURLINFO_HTTP_CODE );

      if ( $statusCode == 404 ) return false;

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
   * @param string $data JSON string hopeful.
   *
   * @return boolean True if the JSON is valid, false if not.
   */

  function fictioneer_is_valid_json( $data = null ) {
    if ( empty( $data ) ) return false;
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
   * @param string $path Relative path to the plugin.
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
    $bool = fictioneer_is_plugin_active( 'wordpress-seo/wp-seo.php' ) || fictioneer_is_plugin_active( 'wordpress-seo-premium/wp-seo-premium.php' ) || function_exists( 'aioseo' );
    return $bool;
  }
}

// =============================================================================
// CURL HELPER
// =============================================================================

if ( ! function_exists( 'fictioneer_do_curl' ) ) {
  /**
   * Helper to do cURL
   *
   * @since Fictioneer 4.0
   * @link  https://gist.github.com/cp6/aec1e58498d44111c4cbc3606d366367
   * @link  https://www.php.net/manual/en/function.curl-setopt.php
   *
   * @param string  $url         URL string to cURL.
   * @param string  $type        Whether to do a GET or POST request. Default 'GET'.
   * @param array   $headers     CURLOPT_HTTPHEADER
   * @param array   $post_fields CURLOPT_POSTFIELDS
   * @param string  $user_agent  CURLOPT_USERAGENT
   * @param boolean $follow      CURLOPT_FOLLOWLOCATION
   * @param boolean $use_ssl     CURLOPT_SSL_VERIFYHOST, CURLOPT_SSL_VERIFYPEER
   * @param int     $con_timeout CURLOPT_CONNECTTIMEOUT
   * @param int     $timeout     URL CURLOPT_TIMEOUT
   *
   * @return boolean True if successful, false otherwise
   */

  function fictioneer_do_curl( string $url, string $type = 'GET', array $headers = [], array $post_fields = [], string $user_agent = '', string $referrer = '', bool $follow = true, bool $use_ssl = false, int $con_timeout = 10, int $timeout = 40 ) {
    $crl = curl_init( $url );

    curl_setopt( $crl, CURLOPT_CUSTOMREQUEST, $type );
    curl_setopt( $crl, CURLOPT_USERAGENT, $user_agent );
    curl_setopt( $crl, CURLOPT_REFERER, $referrer );

    if ( $type == 'POST' ) {
      curl_setopt( $crl, CURLOPT_POST, true );

      if ( ! empty( $post_fields ) ) {
        curl_setopt( $crl, CURLOPT_POSTFIELDS, $post_fields );
      }
    }

    if ( ! empty( $headers ) ) {
      curl_setopt( $crl, CURLOPT_HTTPHEADER, $headers );
    }

    curl_setopt( $crl, CURLOPT_FOLLOWLOCATION, $follow );
    curl_setopt( $crl, CURLOPT_CONNECTTIMEOUT, $con_timeout );
    curl_setopt( $crl, CURLOPT_TIMEOUT, $timeout );
    curl_setopt( $crl, CURLOPT_SSL_VERIFYHOST, $use_ssl );
    curl_setopt( $crl, CURLOPT_SSL_VERIFYPEER, $use_ssl );
    curl_setopt( $crl, CURLOPT_ENCODING, 'gzip,deflate' );
    curl_setopt( $crl, CURLOPT_RETURNTRANSFER, true );

    $call_response = curl_exec( $crl );

    curl_close( $crl );

    return $call_response;
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
   * @param int|string $id_or_email User ID or email address.
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
// GET STORY DATA
// =============================================================================

if ( ! function_exists( 'fictioneer_get_story_data' ) ) {
  /**
   * Get collection of a story's data
   *
   * @since Fictioneer 4.3
   *
   * @param int $story_id ID of the story.
   *
   * @return array|boolean $result Data of the story or false if invalid.
   */

  function fictioneer_get_story_data( $story_id ) {
    $story_id = fictioneer_validate_id( $story_id, 'fcn_story' );

    if ( ! $story_id ) return false;

    // Check cache
    $old_data = get_post_meta( $story_id, 'fictioneer_story_data_collection', true );

    if ( ! empty( $old_data ) && $old_data['last_modified'] >= get_the_modified_time( 'U', $story_id ) ) {
      // Refresh comment count
      $comment_count = count( $old_data['chapter_ids'] ) < 1 ? 0 : get_comments(
        array(
          'status' => 'approve',
          'post_type' => array( 'fcn_chapter' ),
          'post__in' => $old_data['chapter_ids'],
          'count' => true
        )
      );

      $old_data['comment_count'] = $comment_count;

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

    // Count chapters and words
    if ( $chapters ) {
      foreach ( $chapters as $chapter_id ) {
        if ( ! fictioneer_get_field( 'fictioneer_chapter_hidden', $chapter_id ) && get_post_status( $chapter_id ) === 'publish' ) {
          if ( ! fictioneer_get_field( 'fictioneer_chapter_no_chapter', $chapter_id ) ) {
            $chapter_count += 1;
            $word_count += get_post_meta( $chapter_id, '_word_count', true );
          }
          $chapter_ids[] = $chapter_id;
        }
      }
    }

    $comment_args = array(
      'status' => 'approve',
      'post_type' => array( 'fcn_chapter' ),
      'post__in' => $chapter_ids,
      'count' => true
    );

    $comment_count = $chapter_count > 0 ? get_comments( $comment_args ) : 0;

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
      'comment_count' => $comment_count
    );

    update_post_meta( $story_id, 'fictioneer_story_data_collection', $result );

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
   * @param int $author_id User ID of the author.
   *
   * @return array|boolean Statistics or false if user does not exist.
   */

  function fictioneer_get_author_statistics( $author_id ) {
    // Setup
    $author_id = fictioneer_validate_id( $author_id );
    if ( ! $author_id ) return false;

    $author = get_user_by( 'id', $author_id );
    if ( ! get_user_by( 'id', $author_id ) ) return false;

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
   * @param int $number    The number to be shortened.
   * @param int $precision Precision of the fraction. Default 1.
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
// CONVERT TAXONOMIES
// =============================================================================

if ( ! function_exists( 'fictioneer_convert_taxonomies' ) ) {
  /**
   * Convert taxonomies of post type from one type to another
   *
   * @since Fictioneer 4.7
   *
   * @param string  $post_type Post type the taxonomy is attached to.
   * @param string  $target    Taxonomy to be converted to.
   * @param string  $source    Taxonomy to be converted from. Default 'post_tag'.
   * @param boolean $append    Whether to replace the post type's taxonomies with
   *                           the new ones or append them. Default false.
   * @param boolean $clean_up  Whether to delete the taxonomies from the post type
   *                           after transfer or keep them. Default false.
   */

  function fictioneer_convert_taxonomies( $post_type, $target, $source = 'post_tag', $append = false, $clean_up = false ) {
    $query_args = array(
      'post_type' => $post_type,
      'posts_per_page' => -1,
      'fields' => 'ids',
      'update_post_meta_cache' => false
    );

    $items = get_posts( $query_args );

    function terms_to_array( $n ) {
      return $n->name;
    }

    foreach ( $items as $item ) {
      $source_tax = get_the_terms( $item, $source );

      if ( ! $source_tax ) continue;

      $source_tax = array_map( 'terms_to_array', $source_tax );

      wp_set_object_terms( $item, $source_tax, $target, $append );

      if ( $clean_up ) {
        wp_delete_object_term_relationships( $item, $source );
      }
    }
  }
}

// =============================================================================
// ADD OR UPDATE TERM
// =============================================================================

if ( ! function_exists( 'fictioneer_add_or_update_term' ) ) {
  /**
   * Add or update term
   *
   * @since Fictioneer 4.6
   *
   * @param string $name     Name of the term to add or update.
   * @param string $taxonomy Taxonomy type of the term.
   * @param array  $args     Optional. An array of arguments.
   *
   * @return int|boolean The term ID or false.
   */

  function fictioneer_add_or_update_term( $name, $taxonomy, $args = [] ) {
  	$parent = $args['parent'] ?? 0;
  	$alias_of = $args['alias_of'] ?? '';
  	$description = $args['description'] ?? '';
    $result = false;

    // Does term already exist?
  	$old = get_term_by( 'name', $name, $taxonomy );

    // Get parent or create one if it does not yet exist
  	if ( $parent != 0 ) {
  		$parent = get_term_by( 'name', $parent, $taxonomy );
      $parent = $parent ? $parent->term_id : fictioneer_add_or_update_term( $args['parent'], $taxonomy );
  	}

    // Get alias or create one if it does not yet exist
  	if ( ! empty( $alias_of ) ) {
  		$alias_of = get_term_by( 'name', $alias_of, $taxonomy );

      if ( ! $alias_of ) {
        $alias_of = fictioneer_add_or_update_term( $args['alias_of'], $taxonomy );
        $alias_of = $alias_of ? get_term_by( 'term_id', $alias_of, $taxonomy ) : false;
      }

      $alias_of = $alias_of ? $alias_of->slug : '';
  	}

  	if ( ! $old ) {
      // Create term
  		$result = wp_insert_term(
  			$name,
  			$taxonomy,
  			array(
  				'alias_of' => $alias_of,
  				'parent' => $parent,
  				'description' => $description
  			)
  		);
  	} else {
      // Update term
  		$result = wp_update_term(
  			$old->term_id,
  			$taxonomy,
  			array(
  				'alias_of' => $alias_of,
  				'parent' => $parent,
  				'description' => $description
  			)
  		);
  	}

    if ( ! is_wp_error( $result ) ) {
      return $result['term_id'];
    } else {
      return false;
    }
  }
}

// =============================================================================
// VALIDATE ID
// =============================================================================

if ( ! function_exists( 'fictioneer_validate_id' ) ) {
  /**
   * Ensures an ID is a valid integer and positive; optionally checks whether the
   * associated post is of a certain types (or among an array of types).
   *
   * @since Fictioneer 4.7
   *
   * @param int          $id       The ID to validate.
   * @param string|array $for_type Optional. The expected post type(s).
   *
   * @return int|boolean $safe_id The validated ID or false if invalid.
   */

  function fictioneer_validate_id( $id, $for_type = [] ) {
    $safe_id = intval( $id );
    $types = is_array( $for_type ) ? $for_type : [$for_type];

    if ( empty( $safe_id ) || $safe_id < 0 ) return false;
    if ( $for_type && ! in_array( get_post_type( $safe_id ), $types ) ) return false;

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
   * @param string $nonce_name Optional. The name of the nonce. Default 'nonce'.
   * @param string $nonce_value Optional. The value of the nonce. Default 'fictioneer_nonce'.
   *
   * @return boolean|WP_User False if not valid, the current user object otherwise.
   */

  function fictioneer_get_validated_ajax_user( $nonce_name = 'nonce', $nonce_value = 'fictioneer_nonce' ) {
    // Setup
    $user = wp_get_current_user();

    // Validate
    if (
      ! $user ||
      ! check_ajax_referer( $nonce_value, $nonce_name, false )
    ) {
      return false;
    }

    return $user;
  }
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
   * @param string $text    Text that has key/value pairs to be replaced.
   * @param array  $args    The key/value pairs.
   * @param string $default Optional. To be used if the the $text is empty or
   *                        if any key/value pair is invalid. Default ''.
   *
   * @return string The modified text.
   */

  function fictioneer_replace_key_value( $text, $args, $default = '' ) {
    // Setup
    $invalid = false;

    // Check if text exists
    if ( empty( $text ) ) $text = $default;

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
    if ( $invalid ) return $default;

    // Return modified text
    return trim( $text );
  }
}

// =============================================================================
// CHECK USER CAPABILITIES
// =============================================================================

if ( ! function_exists( 'fictioneer_has_role' ) ) {
  /**
   * Checks if an user has a specific role
   *
   * @since Fictioneer 5.0
   *
   * @param WP_User|int $user The user object or ID to check.
   * @param string      $role The role to check for.
   *
   * @return boolean To be or not to be.
   */

  function fictioneer_has_role( $user, $role ) {
    // Setup
    $user = is_int( $user ) ? get_user_by( 'ID', $user ) : $user;

    // Abort conditions
    if ( ! $user || ! $role ) return false;

    // Check if user has role...
    if ( in_array( $role, (array) $user->roles ) ) return true;

    // Else...
    return false;
  }
}

if ( ! function_exists( 'fictioneer_is_admin' ) ) {
  /**
   * Checks if an user is an administrator
   *
   * @since Fictioneer 5.0
   *
   * @param int $user_id The user ID to check.
   *
   * @return boolean To be or not to be.
   */

  function fictioneer_is_admin( $user_id ) {
    // Abort conditions
    if ( ! $user_id ) return false;

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
   * @param int $user_id The user ID to check.
   *
   * @return boolean To be or not to be.
   */

  function fictioneer_is_author( $user_id ) {
    // Abort conditions
    if ( ! $user_id ) return false;

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
   * @param int $user_id The user ID to check.
   *
   * @return boolean To be or not to be.
   */

  function fictioneer_is_moderator( $user_id ) {
    // Abort conditions
    if ( ! $user_id ) return false;

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
   * @param int $user_id The user ID to check.
   *
   * @return boolean To be or not to be.
   */

  function fictioneer_is_editor( $user_id ) {
    // Abort conditions
    if ( ! $user_id ) return false;

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
   * @param string $field   Name of the meta field to retrieve.
   * @param int    $post_id Optional. The ID of the post the field belongs to.
   *                        Defaults to current post ID.
   *
   * @return mixed The single field value.
   */

  function fictioneer_get_field( $field, $post_id = null ) {
    // Setup
    $post_id = $post_id ? $post_id : get_the_ID();

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
   * @param string $field   Name of the meta field to retrieve.
   * @param int    $post_id Optional. The ID of the post the field belongs to.
   *                        Defaults to current post ID.
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
   * @param string $field   Name of the meta field to retrieve.
   * @param int    $post_id Optional. The ID of the post the field belongs to.
   *                        Defaults to current post ID.
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
    if ( ! isset( $_COOKIE['fcn_cookie_consent'] ) || $_COOKIE['fcn_cookie_consent'] === '' ) return false;
    return strval( $_COOKIE['fcn_cookie_consent'] );
  }
}

// =============================================================================
// SANITIZE INTEGER
// =============================================================================

/**
 * Sanitizes an integer with options for default, minimum, and maximum
 *
 * @since 4.0
 *
 * @param int         $value   The integer to be sanitized.
 * @param int         $default Default value if an invalid integer. Default 0.
 * @param int|boolean $minimum Optional. Minimum value of the integer. Default false.
 * @param int|boolean $maximum Optional. Maximum value of the integer. Default false.
 *
 * @return int The sanitized integer.
 */

function fictioneer_sanitize_integer( $value, $default = 0, $minimum = false, $maximum = false ) {
  $value = (int) $value;
  if ( ! is_int( $value ) ) $value = $default;
  if ( $minimum !== false ) $value = max( $value, $minimum );
  if ( $maximum !== false ) $value = min( $value, $maximum );
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
 * @param string|boolean $value The checkbox value to be sanitized.
 *
 * @return boolean True or false.
 */

function fictioneer_sanitize_checkbox( $value ) {
  return filter_var( $value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
}

/**
 * Wrapper for fictioneer_sanitize_checkbox() to check for array key existence
 *
 * @since 5.0
 * @see fictioneer_sanitize_checkbox_key()
 *
 * @param string $key The array key for $_POST.
 *
 * @return boolean True or false.
 */

function fictioneer_sanitize_checkbox_by_key( $key ) {
  $value = isset( $_POST[ $key ] ) ? $_POST[ $key ] : false;
  return fictioneer_sanitize_checkbox( $value );
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
// GET LOGIN STATUS VIA AJAX
// =============================================================================

/**
 * Sends login status via AJAX
 *
 * @since 5.0
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
 */

function fictioneer_ajax_is_user_logged_in() {
  // Nonce
  check_ajax_referer( 'fictioneer_nonce', 'nonce' );

  // Setup
  $user = wp_get_current_user();

  // Send nonce
  wp_send_json_success(
    array(
      'loggedIn' => is_user_logged_in(),
      'isAdmin' => fictioneer_is_admin( $user->ID ),
      'isModerator' => fictioneer_is_moderator( $user->ID ),
      'isAuthor' => fictioneer_is_author( $user->ID ),
      'isEditor' => fictioneer_is_editor( $user->ID )
    )
  );
}
add_action( 'wp_ajax_fictioneer_ajax_is_user_logged_in', 'fictioneer_ajax_is_user_logged_in' );
add_action( 'wp_ajax_nopriv_fictioneer_ajax_is_user_logged_in', 'fictioneer_ajax_is_user_logged_in' );

// =============================================================================
// GET NONCE VIA AJAX
// =============================================================================

/**
 * Sends valid nonce via AJAX
 *
 * @since 5.0
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
 */

function fictioneer_ajax_get_nonce() {
  $nonce = wp_create_nonce( 'fictioneer_nonce' );
  $nonce_html = '<input id="fictioneer-ajax-nonce" name="fictioneer-ajax-nonce" type="hidden" value="' . $nonce . '">';
  wp_send_json_success( ['nonce' => $nonce, 'nonceHtml' => $nonce_html] );
}
add_action( 'wp_ajax_fictioneer_ajax_get_nonce', 'fictioneer_ajax_get_nonce' );
add_action( 'wp_ajax_nopriv_fictioneer_ajax_get_nonce', 'fictioneer_ajax_get_nonce' );

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
 * @param string  $key    Key for requested translation.
 * @param boolean $escape Optional. Escape the string for safe use in
 *                        attributes. Default false.
 *
 * @return string The translation or an empty string if not found.
 */

function fcntr( $key, $escape = false ) {
  // Define default translations
  $strings = array(
    'account' => __( 'Account', 'fictioneer' ),
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
    'logged_in_as' => _x( '<span>Logged in as <strong><a href="%1$s">%2$s</a></strong>. <a class="logout-link" href="%3$s" data-logout>Log out?</a></span>', 'Comment form logged-in note.', 'fictioneer' ),
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
 * @param array|int $pages    Array of pages to balance. If an integer is provided,
 *                            it is converted to a number array.
 * @param int       $current  Current page number.
 * @param int       $keep     Optional. Balancing factor to each side. Default 2.
 * @param string    $ellipses Optional. String for skipped numbers. Default '…'.
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
   * @param int|null $post_id Post ID the comments are for. Defaults to current post ID.
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
   * @param string $author     The author of the comment.
   * @param string $email      The email of the comment.
   * @param string $url        The url used in the comment.
   * @param string $comment    The comment content
   * @param string $user_ip    The comment author's IP address.
   * @param string $user_agent The author's browser user agent.
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
   * @link  https://stackoverflow.com/a/17508056/17140970
   *
   * @param  string $content The content.
   * @return string $content The content with interpreted BBCodes.
   */

  function fictioneer_bbcodes( $content ) {
    // Setup
    $img_search = 'https:[^\"\'|;<>\[\]]+?\.(?:png|jpg|jpeg|gif|webp|svg|avif|tiff).*?';
    $url_search = '(http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?';

    // Deal with some multi-line spoiler issues
    if ( preg_match_all( '/\[spoiler](.+?)\[\/spoiler]/i', $content, $spoilers, PREG_PATTERN_ORDER ) ) {
      foreach ( $spoilers[0] as $spoiler ) {
        $replace = str_replace( '<p></p>', ' ', $spoiler );
        $replace = preg_replace( '/\[quote](.+?)\[\/quote]/i', '<blockquote class="spoiler">$1</blockquote>', $replace );

        $content = str_replace( $spoiler, $replace, $content );
      }
    }

    // Possible patterns
    $patterns = array(
      '/\[spoiler]\[quote](.+?)\[\/quote]\[\/spoiler]/i',
      '/\[spoiler](.+?)\[\/spoiler]/i',
      '/\[b](.+?)\[\/b]/i',
      '/\[i](.+?)\[\/i]/i',
      '/\[s](.+?)\[\/s]/i',
      '/\[quote](.+?)\[\/quote]/i',
      '/\[ins](.+?)\[\/ins]/i',
      '/\[del](.+?)\[\/del]/i',
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
      '<strong>$1</strong>',
      '<em>$1</em>',
      '<strike>$1</strike>',
      '<blockquote>$1</blockquote>',
      '<ins>$1</ins>',
      '<del>$1</del>',
      '<div class="comment-list-item">$1</div>',
      '<span class="comment-image-consent-wrapper"><button class="button _secondary consent-button" title="$1">' . _x( '<i class="fa-solid fa-image"></i> Show Image', 'Comment image consent wrapper button.', 'fictioneer' ) . '</button><a href="$1" class="comment-image-link" rel="noreferrer noopener nofollow" target="_blank"><img class="comment-image" data-src="$1"></a></span>',
      '<span class="comment-image-consent-wrapper"><button class="button _secondary consent-button" title="$1">' . _x( '<i class="fa-solid fa-image"></i> Show Image', 'Comment image consent wrapper button.', 'fictioneer' ) . '</button><img class="comment-image" data-src="$1"></span>',
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
   * @param int     $post_id ID of the post to get the taxonomies of.
   * @param boolean $flatten Whether to flatten the result. Default false.
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
    // Setup default fonts
    $fonts = array(
      ['css' => FICTIONEER_PRIMARY_FONT_CSS, 'name' => FICTIONEER_PRIMARY_FONT_NAME],
      ['css' => '', 'name' => _x( 'System Font', 'Font name.', 'fictioneer' )],
      ['css' => 'Lato', 'name' => _x( 'Lato', 'Font name.', 'fictioneer' )],
      ['css' => 'Helvetica Neue', 'name' => _x( 'Helvetica Neue', 'Font name.', 'fictioneer' ), 'alt' => 'Arial'],
      ['css' => 'Georgia', 'name' => _x( 'Georgia', 'Font name.', 'fictioneer' )],
      ['css' => 'Roboto Mono', 'name' => _x( 'Roboto Mono', 'Font name.', 'fictioneer' )],
      ['css' => 'Roboto Serif', 'name' => _x( 'Roboto Serif', 'Font name.', 'fictioneer' )],
      ['css' => 'Cormorant Garamond', 'name' => _x( 'Cormorant Garamond', 'Font name.', 'fictioneer' ), 'alt' => 'Garamond'],
      ['css' => 'Crimson Text', 'name' => _x( 'Crimson Text', 'Font name.', 'fictioneer' )],
      ['css' => 'OpenDyslexic', 'name' => _x( 'Open Dyslexic', 'Font name.', 'fictioneer' )]
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
 * @since 5.1.3
 *
 * @param string $string The string to explode.
 *
 * @return array The string content as array.
 */

function fictioneer_explode_list( $string ) {
  if ( empty( $string ) ) return [];

  $array = explode( ',', $string );
  $array = array_map( 'trim', $array ); // Remove extra whitespaces
  $array = array_filter( $array, 'strlen' ); // Remove empty elements
  $array = is_array( $array ) ? $array : [];

  return $array;
}

// =============================================================================
// BUILD NOTICE
// =============================================================================

if ( ! function_exists( 'fictioneer_notice' ) ) {
  /**
   * Render or return a notice element
   *
   * @since 5.2.5
   *
   * @param string $message  The notice to show.
   * @param string $type     Optional. The notice type. Default 'warning'.
   * @param bool   $display  Optional. Whether to render or return. Default true.
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
   * @param string $html The HTML string to be minified.
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

?>
