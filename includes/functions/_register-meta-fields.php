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

  if ( user_can( $user_id, 'manage_options' ) ) {
    return true;
  }

  $cap_base = $type === 'fcn_story' ? 'fcn_stories' : "{$type}s";

  if ( ! user_can( $user_id, "edit_{$cap_base}" ) ) {
    return false;
  }

  $post = get_post( $post_id );

  if ( ! $post ) {
    return true;
  }

  if ( (int) $post->post_author !== $user_id && ! user_can( $user_id, "edit_others_{$cap_base}" ) ) {
    return false;
  }

  if ( $post->post_status === 'publish' && ! user_can( $user_id, "edit_published_{$cap_base}" ) ) {
    return false;
  }

  if ( $post->post_status === 'private' && ! user_can( $user_id, "edit_private_{$cap_base}" ) ) {
    return false;
  }

  return true;
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

function fictioneer_rest_get_auth_callback_for_type( $type ) {
  if ( in_array( $type, ['post', 'page', 'fcn_story', 'fcn_chapter', 'fcn_recommendation', 'fcn_collection'] ) ) {
    return function( $allowed, $meta_key, $object_id, $user_id ) use ( $type ) {
      return fictioneer_rest_auth_callback( $object_id, $user_id, $type );
    };
  }

  return function( $allowed, $meta_key, $object_id, $user_id ) {
    return user_can( $user_id, 'manage_options' ) || user_can( $user_id, 'edit_post', $object_id );
  };
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
  foreach ( ['post', 'page', 'fcn_story', 'fcn_chapter', 'fcn_recommendation', 'fcn_collection'] as $type ) {
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
        'auth_callback' => fictioneer_rest_get_auth_callback_for_type( $type ),
        'sanitize_callback' => 'fictioneer_sanitize_image_id'
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
        'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) use ( $type ) {
          return (
            fictioneer_rest_auth_callback( $object_id, $user_id, $type ) &&
            ( user_can( $user_id, 'fcn_custom_page_header' ) || user_can( $user_id, 'manage_options' ) )
          );
        },
        'sanitize_callback' => 'fictioneer_sanitize_image_id'
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
        'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) use ( $type ) {
          return (
            fictioneer_rest_auth_callback( $object_id, $user_id, $type ) &&
            ( user_can( $user_id, 'fcn_seo_meta' ) || user_can( $user_id, 'manage_options' ) )
          );
        },
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

  foreach ( ['post', 'fcn_story', 'fcn_chapter'] as $type ) {
    register_post_meta(
      $type,
      'fictioneer_patreon_link',
      array(
        'type' => 'string',
        'single' => true,
        'show_in_rest' => array(
          'schema' => array(
            'type' => 'string'
          )
        ),
        'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) use ( $type ) {
          return fictioneer_rest_auth_callback( $object_id, $user_id, $type );
        },
        'sanitize_callback' => function( $meta_value ) {
          return fictioneer_sanitize_url( $meta_value, null, '#^https://(www\.)?patreon\.#' );
        }
      )
    );

    register_post_meta(
      $type,
      'fictioneer_kofi_link',
      array(
        'type' => 'string',
        'single' => true,
        'show_in_rest' => array(
          'schema' => array(
            'type' => 'string'
          )
        ),
        'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) use ( $type ) {
          return fictioneer_rest_auth_callback( $object_id, $user_id, $type );
        },
        'sanitize_callback' => function( $meta_value ) {
          return fictioneer_sanitize_url( $meta_value, null, '#^https://(www\.)?ko-fi\.#' );
        }
      )
    );

    register_post_meta(
      $type,
      'fictioneer_subscribestar_link',
      array(
        'type' => 'string',
        'single' => true,
        'show_in_rest' => array(
          'schema' => array(
            'type' => 'string'
          )
        ),
        'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) use ( $type ) {
          return fictioneer_rest_auth_callback( $object_id, $user_id, $type );
        },
        'sanitize_callback' => function( $meta_value ) {
          return fictioneer_sanitize_url( $meta_value, null, '#^https://(www\.)?subscribestar\.#' );
        }
      )
    );

    register_post_meta(
      $type,
      'fictioneer_paypal_link',
      array(
        'type' => 'string',
        'single' => true,
        'show_in_rest' => array(
          'schema' => array(
            'type' => 'string'
          )
        ),
        'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) use ( $type ) {
          return fictioneer_rest_auth_callback( $object_id, $user_id, $type );
        },
        'sanitize_callback' => function( $meta_value ) {
          return fictioneer_sanitize_url( $meta_value, null, '#^https://(www\.)?paypal\.#' );
        }
      )
    );

    register_post_meta(
      $type,
      'fictioneer_donation_link',
      array(
        'type' => 'string',
        'single' => true,
        'show_in_rest' => array(
          'schema' => array(
            'type' => 'string'
          )
        ),
        'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) use ( $type ) {
          return fictioneer_rest_auth_callback( $object_id, $user_id, $type );
        },
        'sanitize_callback' => function( $meta_value ) {
          return fictioneer_sanitize_url( $meta_value, 'https://' );
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
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_story' );
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
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_story' );
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
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_story' );
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
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_story' );
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
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_story' );
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
        return (
          fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_story' ) &&
          ( user_can( $user_id, 'manage_options' ) || ! user_can( $user_id, 'fcn_no_filters' ) )
        );
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
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_story' );
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
    'fictioneer_story_copyright_notice',
    array(
      'type' => 'string',
      'single' => true,
      'show_in_rest' => array(
        'schema' => array(
          'type' => 'string'
        )
      ),
      'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) {
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_story' );
      },
      'sanitize_callback' => 'sanitize_text_field'
    )
  );

  register_post_meta(
    'fcn_story',
    'fictioneer_story_topwebfiction_link',
    array(
      'type' => 'string',
      'single' => true,
      'show_in_rest' => array(
        'schema' => array(
          'type' => 'string'
        )
      ),
      'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) {
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_story' );
      },
      'sanitize_callback' => function( $meta_value ) {
        return fictioneer_sanitize_url( $meta_value, 'https://topwebfiction.com/' );
      }
    )
  );

  register_post_meta(
    'fcn_story',
    'fictioneer_story_sticky',
    array(
      'type' => 'boolean',
      'single' => true,
      'show_in_rest' => array(
        'schema' => array(
          'type' => 'boolean'
        )
      ),
      'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) {
        return (
          fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_story' ) &&
          ( user_can( $user_id, 'manage_options' ) || user_can( $user_id, 'fcn_make_sticky' ) )
        );
      },
      'sanitize_callback' => 'fictioneer_sanitize_checkbox'
    )
  );

  register_post_meta(
    'fcn_story',
    'fictioneer_story_hidden',
    array(
      'type' => 'boolean',
      'single' => true,
      'show_in_rest' => array(
        'schema' => array(
          'type' => 'boolean'
        )
      ),
      'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) {
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_story' );
      },
      'sanitize_callback' => 'fictioneer_sanitize_checkbox'
    )
  );

  register_post_meta(
    'fcn_story',
    'fictioneer_story_no_thumbnail',
    array(
      'type' => 'boolean',
      'single' => true,
      'show_in_rest' => array(
        'schema' => array(
          'type' => 'boolean'
        )
      ),
      'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) {
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_story' );
      },
      'sanitize_callback' => 'fictioneer_sanitize_checkbox'
    )
  );

  register_post_meta(
    'fcn_story',
    'fictioneer_story_no_tags',
    array(
      'type' => 'boolean',
      'single' => true,
      'show_in_rest' => array(
        'schema' => array(
          'type' => 'boolean'
        )
      ),
      'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) {
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_story' );
      },
      'sanitize_callback' => 'fictioneer_sanitize_checkbox'
    )
  );

  register_post_meta(
    'fcn_story',
    'fictioneer_story_hide_chapter_icons',
    array(
      'type' => 'boolean',
      'single' => true,
      'show_in_rest' => array(
        'schema' => array(
          'type' => 'boolean'
        )
      ),
      'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) {
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_story' );
      },
      'sanitize_callback' => 'fictioneer_sanitize_checkbox'
    )
  );

  register_post_meta(
    'fcn_story',
    'fictioneer_story_disable_collapse',
    array(
      'type' => 'boolean',
      'single' => true,
      'show_in_rest' => array(
        'schema' => array(
          'type' => 'boolean'
        )
      ),
      'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) {
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_story' );
      },
      'sanitize_callback' => 'fictioneer_sanitize_checkbox'
    )
  );

  register_post_meta(
    'fcn_story',
    'fictioneer_story_disable_groups',
    array(
      'type' => 'boolean',
      'single' => true,
      'show_in_rest' => array(
        'schema' => array(
          'type' => 'boolean'
        )
      ),
      'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) {
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_story' );
      },
      'sanitize_callback' => 'fictioneer_sanitize_checkbox'
    )
  );

  register_post_meta(
    'fcn_story',
    'fictioneer_story_no_epub',
    array(
      'type' => 'boolean',
      'single' => true,
      'show_in_rest' => array(
        'schema' => array(
          'type' => 'boolean'
        )
      ),
      'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) {
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_story' );
      },
      'sanitize_callback' => 'fictioneer_sanitize_checkbox'
    )
  );
}
add_action( 'init', 'fictioneer_register_story_meta_fields' );
