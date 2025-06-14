<?php

// =============================================================================
// AUTH CALLBACKS
// =============================================================================

/**
 * Authenticate REST API request.
 *
 * @since 5.29.5
 *
 * @param int $post_id  Post ID.
 * @param int $user_id  User ID.
 * @param int $type     Post type.
 *
 * @return bool True if allowed, false otherwise.
 */

function fictioneer_rest_auth_callback( $post_id, $user_id, $type ) {
  if ( ! $user_id ) {
    return false;
  }

  $post = get_post( $post_id );

  if ( $post ) {
    return (
      (int) $post->post_author === $user_id ||
      user_can( $user_id, "edit_others_{$type}" ) ||
      user_can( $user_id, 'manage_options' )
    );
  }

  return user_can( $user_id, "edit_{$type}" ) || user_can( $user_id, 'manage_options' );
}

/**
 * Authenticate REST API request for stories.
 *
 * @since 5.29.5
 *
 * @param int $post_id  Post ID.
 * @param int $user_id  User ID.
 *
 * @return bool True if allowed, false otherwise.
 */

function fictioneer_rest_story_auth_callback( $post_id, $user_id ) {
  return fictioneer_rest_auth_callback( $post_id, $user_id, 'fcn_stories' );
}

/**
 * Authenticate REST API request for chapters.
 *
 * @since 5.29.5
 *
 * @param int $post_id  Post ID.
 * @param int $user_id  User ID.
 *
 * @return bool True if allowed, false otherwise.
 */

function fictioneer_rest_chapter_auth_callback( $post_id, $user_id ) {
  return fictioneer_rest_auth_callback( $post_id, $user_id, 'fcn_chapters' );
}

/**
 * Authenticate REST API request for collections.
 *
 * @since 5.29.5
 *
 * @param int $post_id  Post ID.
 * @param int $user_id  User ID.
 *
 * @return bool True if allowed, false otherwise.
 */

function fictioneer_rest_collection_auth_callback( $post_id, $user_id ) {
  return fictioneer_rest_auth_callback( $post_id, $user_id, 'fcn_collections' );
}

/**
 * Authenticate REST API request for recommendations.
 *
 * @since 5.29.5
 *
 * @param int $post_id  Post ID.
 * @param int $user_id  User ID.
 *
 * @return bool True if allowed, false otherwise.
 */

function fictioneer_rest_recommendation_auth_callback( $post_id, $user_id ) {
  return fictioneer_rest_auth_callback( $post_id, $user_id, 'fcn_recommendations' );
}

/**
 * Authenticate REST API request for posts.
 *
 * @since 5.29.5
 *
 * @param int $post_id  Post ID.
 * @param int $user_id  User ID.
 *
 * @return bool True if allowed, false otherwise.
 */

function fictioneer_rest_post_auth_callback( $post_id, $user_id ) {
  return fictioneer_rest_auth_callback( $post_id, $user_id, 'posts' );
}

/**
 * Authenticate REST API request for pages.
 *
 * @since 5.29.5
 *
 * @param int $post_id  Post ID.
 * @param int $user_id  User ID.
 *
 * @return bool True if allowed, false otherwise.
 */

function fictioneer_rest_page_auth_callback( $post_id, $user_id ) {
  return fictioneer_rest_auth_callback( $post_id, $user_id, 'pages' );
}

/**
 * Return REST API request authentication callback for post type.
 *
 * @since 5.29.5
 *
 * @param string $type  Post type.
 *
 * @return string The authentication callback.
 */

function fictioneer_get_auth_callback_for_type( $type ) {
  switch ( $type ) {
    case 'fcn_story':
      return function( $allowed, $meta_key, $object_id, $user_id ) {
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_stories' );
      };
    case 'fcn_chapter':
      return function( $allowed, $meta_key, $object_id, $user_id ) {
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_chapters' );
      };
    case 'fcn_collection':
      return function( $allowed, $meta_key, $object_id, $user_id ) {
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_collections' );
      };
    case 'fcn_recommendation':
      return function( $allowed, $meta_key, $object_id, $user_id ) {
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_recommendations' );
      };
    case 'post':
      return function( $allowed, $meta_key, $object_id, $user_id ) {
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'posts' );
      };
    case 'page':
      return function( $allowed, $meta_key, $object_id, $user_id ) {
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'pages' );
      };
    default:
      return function( $allowed, $meta_key, $object_id, $user_id ) {
        return user_can( $user_id, 'edit_post', $object_id );
      };
  }
}

// =============================================================================
// REGISTER GENERAL META FIELDS
// =============================================================================

/**
 * Register general meta fields with WordPress.
 *
 * Note: This may also require you to enable custom field support
 * for custom post types in the settings.
 *
 * @since 5.29.5
 */

function fictioneer_register_general_meta_fields() {
  $all_post_types = ['post', 'page', 'fcn_story', 'fcn_chapter', 'fcn_recommendation', 'fcn_collection'];

  foreach ( $all_post_types as $type ) {
    register_post_meta(
      $type,
      'fictioneer_landscape_image',
      array(
        'type' => 'integer',
        'single' => true,
        'show_in_rest' => array(
          'schema' => array(
            'type' => 'integer'
          )
        ),
        'auth_callback' => fictioneer_get_auth_callback_for_type( $type ),
        'sanitize_callback' => 'absint'
      )
    );

    register_post_meta(
      $type,
      'fictioneer_custom_header_image',
      array(
        'type' => 'integer',
        'single' => true,
        'show_in_rest' => array(
          'schema' => array(
            'type' => 'integer'
          )
        ),
        'auth_callback' => fictioneer_get_auth_callback_for_type( $type ),
        'sanitize_callback' => 'absint'
      )
    );

    register_post_meta(
      $type,
      'fictioneer_seo_fields',
      array(
        'type' => 'object',
        'single' => true,
        'show_in_rest' => array(
          'schema' => array(
            'type' => 'object',
            'properties' => array(
              'title' => array(
                'type' => 'string',
                'default' => ''
              ),
              'description' => array(
                'type' => 'string',
                'default' => ''
              ),
              'og_image' => array(
                'type' => ['integer', 'string'],
                'default' => 0
              )
            )
          )
        ),
        'auth_callback' => fictioneer_get_auth_callback_for_type( $type ),
        'sanitize_callback' => function( $meta_value ) {
          if ( ! is_array( $meta_value ) ) {
            return [];
          }

          $title = sanitize_text_field( $meta_value['title'] ?? '' );
          $description = sanitize_text_field( $meta_value['description'] ?? '' );
          $og_image = max( intval( $meta_value['og_image'] ?? 0 ), 0 );

          if ( ! $title && ! $description && ! $og_image ) {
            return [];
          }

          return array(
            'title' => $title,
            'description' => $description,
            'og_image' => $og_image
          );
        }
      )
    );
  }
}
add_action( 'init', 'fictioneer_register_general_meta_fields' );

// =============================================================================
// REGISTER STORY META FIELDS
// =============================================================================

/**
 * Register story meta fields with WordPress.
 *
 * Note: This may also require you to enable custom field support
 * for custom post types in the settings.
 *
 * @since 5.29.5
 */

function fictioneer_register_story_meta_fields() {
  register_post_meta(
    'fcn_story',
    'fictioneer_story_status',
    array(
      'type' => 'string',
      'single' => true,
      'show_in_rest' => array(
        'schema' => array(
          'type' => 'string',
          'default' => 'Ongoing'
        )
      ),
      'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) {
        return fictioneer_rest_story_auth_callback( $object_id, $user_id );
      },
      'sanitize_callback' => function( $meta_value ) {
        $meta_value = strtolower( trim( $meta_value ) );

        return in_array( $meta_value, ['ongoing', 'completed', 'oneshot', 'hiatus', 'canceled'] )
          ? ucfirst( $meta_value ) : 'Ongoing';
      }
    )
  );

  register_post_meta(
    'fcn_story',
    'fictioneer_story_rating',
    array(
      'type' => 'string',
      'single' => true,
      'show_in_rest' => array(
        'schema' => array(
          'type' => 'string',
          'default' => 'Everyone'
        )
      ),
      'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) {
        return fictioneer_rest_story_auth_callback( $object_id, $user_id );
      },
      'sanitize_callback' => function( $meta_value ) {
        $meta_value = strtolower( trim( $meta_value ) );

        return in_array( $meta_value, ['everyone', 'teen', 'mature', 'adult'] )
          ? ucfirst( $meta_value ) : 'Everyone';
      }
    )
  );

  register_post_meta(
    'fcn_story',
    'fictioneer_story_chapters',
    array(
      'type' => 'array',
      'single' => true,
      'show_in_rest' => array(
        'schema' => array(
          'type' => 'array',
          'default' => [],
          'items' => array(
            'type' => ['integer', 'string']
          )
        )
      ),
      'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) {
        return fictioneer_rest_story_auth_callback( $object_id, $user_id );
      },
      'sanitize_callback' => function( $meta_value ) {
        if ( is_null( $meta_value ) || ! is_array( $meta_value ) ) {
          return [];
        }

        $meta_value = array_map( 'intval', $meta_value );
        $meta_value = array_filter( $meta_value, fn( $value ) => $value > 0 );
        $meta_value = array_unique( $meta_value );
        $meta_value = array_map( 'strval', $meta_value );
        $meta_value = array_values( $meta_value );

        return $meta_value;
      }
    )
  );

  register_post_meta(
    'fcn_story',
    'fictioneer_story_custom_pages',
    array(
      'type' => 'array',
      'single' => true,
      'show_in_rest' => array(
        'schema' => array(
          'type' => 'array',
          'default' => [],
          'items' => array(
            'type' => ['integer', 'string']
          )
        )
      ),
      'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) {
        return fictioneer_rest_story_auth_callback( $object_id, $user_id );
      },
      'sanitize_callback' => function( $meta_value ) {
        if ( is_null( $meta_value ) || ! is_array( $meta_value ) ) {
          return [];
        }

        if ( ! defined( 'FICTIONEER_MAX_CUSTOM_PAGES_PER_STORY' ) ) {
          define( 'FICTIONEER_MAX_CUSTOM_PAGES_PER_STORY', 4 );
        }

        $meta_value = array_map( 'intval', $meta_value );
        $meta_value = array_filter( $meta_value, fn( $value ) => $value > 0 );
        $meta_value = array_unique( $meta_value );
        $meta_value = array_slice( $meta_value, 0, FICTIONEER_MAX_CUSTOM_PAGES_PER_STORY );
        $meta_value = array_map( 'strval', $meta_value );
        $meta_value = array_values( $meta_value );

        return $meta_value;
      }
    )
  );

  register_post_meta(
    'fcn_story',
    'fictioneer_story_short_description',
    array(
      'type' => 'string',
      'single' => true,
      'show_in_rest' => array(
        'schema' => array(
          'type' => 'string',
          'default' => ''
        )
      ),
      'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) {
        return fictioneer_rest_story_auth_callback( $object_id, $user_id );
      },
      'sanitize_callback' => function( $meta_value ) {
        if ( is_null( $meta_value ) ) {
          return '';
        }

        $meta_value = wp_kses_post( $meta_value );
        $meta_value = strip_shortcodes( $meta_value );
        $meta_value = preg_replace( '/<!--\s*wp:(.*?)-->(.*?)<!--\s*\/wp:\1\s*-->/s', '', $meta_value );

        return $meta_value;
      }
    )
  );

  register_post_meta(
    'fcn_story',
    'fictioneer_story_filters',
    array(
      'type' => 'array',
      'single' => true,
      'show_in_rest' => array(
        'schema' => array(
          'type' => 'array',
          'default' => [],
          'items' => array(
            'type' => 'object',
            'properties' => array(
              'label' => array(
                'type' => 'string'
              ),
              'image_id' => array(
                'type' => ['integer', 'string']
              ),
              'groups' => array(
                'type' => 'array',
                'items' => array(
                  'type' => 'string'
                )
              ),
              'ids' => array(
                'type' => 'array',
                'items' => array(
                  'type' => ['integer', 'string']
                )
              )
            ),
            'required' => ['label', 'image_id']
          )
        )
      ),
      'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) {
        return fictioneer_rest_story_auth_callback( $object_id, $user_id );
      },
      'sanitize_callback' => function( $meta_value ) {
        if ( is_null( $meta_value ) || ! is_array( $meta_value ) ) {
          return [];
        }

        $meta_value = array_map(
          function( $slide ) {
            if ( ! is_array( $slide ) ) {
              return null;
            }

            $label = sanitize_text_field( $slide['label'] ?? '' );
            $image_id = max( intval( $slide['image_id'] ?? 0 ), 0 );

            $groups = $slide['groups'] ?? [];
            $groups = is_array( $groups ) ? $groups : [];
            $groups = array_map( 'sanitize_title', $groups );
            $groups = array_filter( $groups, fn( $group ) => $group !== '' );
            $groups = array_values( array_unique( $groups ) );

            $chapter_ids = $slide['ids'] ?? [];
            $chapter_ids = is_array( $chapter_ids ) ? $chapter_ids : [];
            $chapter_ids = array_map( 'intval', $chapter_ids );
            $chapter_ids = array_filter( $chapter_ids, fn( $id ) => $id > 0 );
            $chapter_ids = array_values( array_unique( $chapter_ids ) );

            return array(
              'label' => $label,
              'image_id' => $image_id,
              'groups' => $groups,
              'ids' => $chapter_ids
            );
          },
          $meta_value
        );

        return array_values( array_filter( $meta_value ) );
      }
    )
  );

  register_post_meta(
    'fcn_story',
    'fictioneer_story_co_authors',
    array(
      'type' => 'array',
      'single' => true,
      'show_in_rest' => array(
        'schema' => array(
          'type' => 'array',
          'default' => [],
          'items' => array(
            'type' => ['integer', 'string']
          )
        )
      ),
      'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) {
        return fictioneer_rest_story_auth_callback( $object_id, $user_id );
      },
      'sanitize_callback' => function( $meta_value ) {
        if ( is_null( $meta_value ) || ! is_array( $meta_value ) ) {
          return [];
        }

        $meta_value = array_map( 'intval', $meta_value );
        $meta_value = array_filter( $meta_value, fn( $value ) => $value > 0 );
        $meta_value = array_unique( $meta_value );
        $meta_value = array_map( 'strval', $meta_value );
        $meta_value = array_values( $meta_value );

        return $meta_value;
      }
    )
  );
}
add_action( 'init', 'fictioneer_register_story_meta_fields' );
