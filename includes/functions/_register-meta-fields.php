<?php

// =============================================================================
// AUTH CALLBACKS
// =============================================================================

/**
 * Authenticate REST API request.
 *
 * @since 5.30.0
 *
 * @param int $post_id  Post ID.
 * @param int $user_id  User ID.
 * @param int $type     Post type.
 *
 * @return bool True if allowed, false otherwise.
 */

function fictioneer_rest_auth_callback( $post_id, $user_id, $type ) {
  $GLOBALS['fictioneer_rest_auth_triggered'] = true;

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
 * @since 5.30.0
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
 * @since 5.30.0
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
      'fictioneer_custom_css',
      array(
        'type' => 'string',
        'single' => true,
        'show_in_rest' => array(
          'schema' => array(
            'type' => 'string'
          )
        ),
        'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) use ( $type ) {
          return (
            fictioneer_rest_auth_callback( $object_id, $user_id, $type ) &&
            ( user_can( $user_id, 'fcn_custom_page_css' ) || user_can( $user_id, 'manage_options' ) )
          );
        },
        'sanitize_callback' => 'fictioneer_sanitize_css'
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

    register_post_meta(
      $type,
      'fictioneer_patreon_lock_tiers',
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
        'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) use ( $type ) {
          return (
            fictioneer_rest_auth_callback( $object_id, $user_id, $type ) &&
            ( user_can( $user_id, 'fcn_assign_patreon_tiers' ) || user_can( $user_id, 'manage_options' ) )
          );
        },
        'sanitize_callback' => function( $meta_value ) {
          if ( is_null( $meta_value ) || ! is_array( $meta_value ) ) {
            return [];
          }

          $patreon_tiers = get_option( 'fictioneer_connection_patreon_tiers' );
          $patreon_tiers = is_array( $patreon_tiers ) ? $patreon_tiers : [];

          if ( ! $patreon_tiers ) {
            return [];
          }

          $meta_value = array_map( 'intval', $meta_value );
          $meta_value = array_filter( $meta_value, fn( $value ) => $value > 0 );
          $meta_value = array_unique( $meta_value );
          $meta_value = array_intersect( $meta_value, array_keys( $patreon_tiers ) );
          $meta_value = array_map( 'strval', $meta_value );
          $meta_value = array_values( $meta_value );

          return $meta_value;
        }
      )
    );

    register_post_meta(
      $type,
      'fictioneer_patreon_lock_amount',
      array(
        'type' => 'integer',
        'single' => true,
        'show_in_rest' => array(
          'schema' => array(
            'type' => 'integer'
          )
        ),
        'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) {
          return user_can( $user_id, 'manage_options' );
        },
        'sanitize_callback' => 'absint'
      )
    );

    if ( $type === 'fcn_story' ) {
      register_post_meta(
        'fcn_story',
        'fictioneer_patreon_inheritance',
        array(
          'type' => 'boolean',
          'single' => true,
          'show_in_rest' => array(
            'schema' => array(
              'type' => 'boolean'
            )
          ),
          'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) {
            return user_can( $user_id, 'manage_options' );
          },
          'sanitize_callback' => 'fictioneer_sanitize_checkbox'
        )
      );
    }

    register_post_meta(
      $type,
      'fictioneer_disable_sidebar',
      array(
        'type' => 'boolean',
        'single' => true,
        'show_in_rest' => array(
          'schema' => array(
            'type' => 'boolean'
          )
        ),
        'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) {
          return user_can( $user_id, 'manage_options' );
        },
        'sanitize_callback' => 'fictioneer_sanitize_checkbox'
      )
    );

    register_post_meta(
      $type,
      'fictioneer_disable_page_padding',
      array(
        'type' => 'boolean',
        'single' => true,
        'show_in_rest' => array(
          'schema' => array(
            'type' => 'boolean'
          )
        ),
        'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) {
          return user_can( $user_id, 'manage_options' );
        },
        'sanitize_callback' => 'fictioneer_sanitize_checkbox'
      )
    );

    register_post_meta(
      $type,
      'fictioneer_post_password_expiration_date',
      array(
        'type' => 'string',
        'single' => true,
        'show_in_rest' => array(
          'schema' => array(
            'type' => 'string'
          )
        ),
        'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) use ( $type ) {
          return (
            fictioneer_rest_auth_callback( $object_id, $user_id, $type ) &&
            ( user_can( $user_id, 'fcn_expire_passwords' ) || user_can( $user_id, 'manage_options' ) )
          );
        },
        'sanitize_callback' => function( $meta_value ) {
          if ( ! is_string( $meta_value ) ) {
            return '';
          }

          try {
            $wp_timezone = wp_timezone();

            $now = new DateTime( 'now', $wp_timezone );
            $now->setTimezone( new DateTimeZone( 'UTC' ) );

            $local = new DateTime( $meta_value, $wp_timezone );
            $local->setTimezone( new DateTimeZone( 'UTC' ) );

            if ( $local <= $now ) {
              return '';
            }

            return $local->format( 'Y-m-d H:i:s' );
          } catch ( Exception $e ) {
            return '';
          }
        }
      )
    );
  }

  foreach ( ['post', 'page', 'fcn_story', 'fcn_chapter'] as $type ) {
    register_post_meta(
      $type,
      'fictioneer_disable_commenting',
      array(
        'type' => 'boolean',
        'single' => true,
        'show_in_rest' => array(
          'schema' => array(
            'type' => 'boolean'
          )
        ),
        'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) use ( $type ) {
          return fictioneer_rest_auth_callback( $object_id, $user_id, $type );
        },
        'sanitize_callback' => 'fictioneer_sanitize_checkbox'
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
 * @since 5.30.0
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
      'sanitize_callback' => 'fictioneer_sanitize_editor'
    )
  );

  register_post_meta(
    'fcn_story',
    'fictioneer_story_global_note',
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
      'sanitize_callback' => 'fictioneer_sanitize_editor'
    )
  );

  register_post_meta(
    'fcn_story',
    'fictioneer_story_password_note',
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
      'sanitize_callback' => 'fictioneer_sanitize_editor'
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
        $meta_value = array_filter( $meta_value, fn( $user_id ) => $user_id > 0 );
        $meta_value = array_unique( $meta_value );
        $meta_value = array_filter( $meta_value, fn( $user_id ) => get_userdata( $user_id ) !== false );
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

  register_post_meta(
    'fcn_story',
    'fictioneer_story_css',
    array(
      'type' => 'string',
      'single' => true,
      'show_in_rest' => array(
        'schema' => array(
          'type' => 'string'
        )
      ),
      'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) {
        return (
          fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_story' ) &&
          ( user_can( $user_id, 'manage_options' ) || user_can( $user_id, 'fcn_custom_page_css' ) )
        );
      },
      'sanitize_callback' => 'fictioneer_sanitize_css'
    )
  );
}
add_action( 'init', 'fictioneer_register_story_meta_fields' );

// =============================================================================
// REGISTER CHAPTER META FIELDS
// =============================================================================

/**
 * Remember current story chapter ID.
 *
 * @since 5.30.0
 *
 * @param stdClass $prepared_post  An object representing a single post prepared for inserting or updating the database.
 *
 * @return stdClass The unchanged prepared post.
 */

function fictioneer_rest_pre_chapter_story_change( $prepared_post ) {
  if ( isset( $prepared_post->ID ) ) {
    $current_story_id = get_post_meta( $prepared_post->ID, 'fictioneer_chapter_story', true );

    if ( $current_story_id ) {
      $GLOBALS['fictioneer_rest_previous_chapter_story'] = (int) $current_story_id;
    }
  }

  return $prepared_post;
}
add_filter( 'rest_pre_insert_fcn_chapter', 'fictioneer_rest_pre_chapter_story_change' );

/**
 * Clean up story chapter lists after story chapter update.
 *
 * @since 5.30.0
 *
 * @param WP_Post $post             Post object.
 * @param WP_REST_Request $request  Request object.
 */

function fictioneer_rest_after_chapter_story_change( $post, $request ) {
  global $fictioneer_rest_previous_chapter_story;

  if ( array_key_exists( 'fictioneer_chapter_story', $request['meta'] ?? [] ) ) {
    $new_story_id = (int) get_post_meta( $post->ID, 'fictioneer_chapter_story', true );
    $old_story_id = (int) ( $fictioneer_rest_previous_chapter_story ?? null );

    // Remove chapter from old story
    if ( $old_story_id && $new_story_id && $new_story_id !== $old_story_id ) {
      $old_story_chapters = fictioneer_get_story_chapter_ids( $old_story_id );
      $old_story_updated_chapters = array_diff( $old_story_chapters, [ strval( $post->ID ) ] );

      update_post_meta( $old_story_id, 'fictioneer_story_chapters', $old_story_updated_chapters );
      fictioneer_log_story_chapter_changes( $old_story_id, $old_story_updated_chapters, $old_story_chapters );

      wp_update_post( array( 'ID' => $old_story_id ) );
    }

    // Append to new story
    if ( $new_story_id && get_option( 'fictioneer_enable_chapter_appending' ) ) {
      fictioneer_append_chapter_to_story( $post->ID, $new_story_id );
    }

    // Set post parent
    if ( $new_story_id ) {
      fictioneer_set_chapter_story_parent( $post->ID, $new_story_id );
    }
  }
}
add_action( 'rest_after_insert_fcn_chapter', 'fictioneer_rest_after_chapter_story_change', 10, 2 );

/**
 * Delete falsy post meta unless allow-listed.
 *
 * @since 5.30.0
 *
 * @param WP_Post $post             Post object.
 * @param WP_REST_Request $request  Request object.
 */

function fictioneer_rest_after_insert_fcn_chapter_cleanup( $post, $request ) {
  if ( ! isset( $request['meta'] ) || ! $post ) {
    return;
  }

  $keys_to_delete = [];

  foreach ( $request['meta'] as $key => $value ) {
    if ( empty( $value ) && ! in_array( $key, fictioneer_get_falsy_meta_allow_list() ) ) {
      $keys_to_delete[] = $key;
    }
  }

  foreach ( $keys_to_delete as $meta_key ) {
    delete_post_meta( $post->ID, $meta_key );
  }
}
add_action( 'rest_after_insert_fcn_chapter', 'fictioneer_rest_after_insert_fcn_chapter_cleanup', 99, 2 );

/**
 * Register chapter meta fields with WordPress.
 *
 * Note: This may also require you to enable custom field support
 * for custom post types in the settings.
 *
 * @since 5.30.0
 */

function fictioneer_register_chapter_meta_fields() {
  register_post_meta(
    'fcn_chapter',
    'fictioneer_chapter_story',
    array(
      'type' => ['integer', 'string'],
      'single' => true,
      'show_in_rest' => array(
        'schema' => array(
          'type' => 'integer',
          'default' => ''
        )
      ),
      'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) {
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_chapter' );
      },
      'sanitize_callback' => function( $meta_value ) {
        $meta_value = fictioneer_validate_id( $meta_value, 'fcn_story' );

        if ( ! $meta_value ) {
          return '0';
        }

        $story_author_id = get_post_field( 'post_author', $meta_value );

        if ( ! $story_author_id ) {
          return '0';
        }

        if ( get_option( 'fictioneer_limit_chapter_stories_by_author' ) ) {
          $user_id = get_current_user_id();

          if (
            $story_author_id != $user_id &&
            ! ( user_can( $user_id, 'manage_options' ) || user_can( $user_id, 'fcn_crosspost' ) ) &&
            ! in_array( $meta_value, fictioneer_sql_get_co_authored_story_ids( $user_id ) )
          ) {
            return '0';
          }
        }

        return strval( $meta_value );
      }
    )
  );

  register_post_meta(
    'fcn_chapter',
    'fictioneer_chapter_list_title',
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
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_chapter' );
      },
      'sanitize_callback' => 'sanitize_text_field'
    )
  );

  register_post_meta(
    'fcn_chapter',
    'fictioneer_chapter_group',
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
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_chapter' );
      },
      'sanitize_callback' => 'sanitize_text_field'
    )
  );

  register_post_meta(
    'fcn_chapter',
    'fictioneer_chapter_foreword',
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
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_chapter' );
      },
      'sanitize_callback' => 'fictioneer_sanitize_editor'
    )
  );

  register_post_meta(
    'fcn_chapter',
    'fictioneer_chapter_afterword',
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
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_chapter' );
      },
      'sanitize_callback' => 'fictioneer_sanitize_editor'
    )
  );

  register_post_meta(
    'fcn_chapter',
    'fictioneer_chapter_password_note',
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
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_chapter' );
      },
      'sanitize_callback' => 'fictioneer_sanitize_editor'
    )
  );

  register_post_meta(
    'fcn_chapter',
    'fictioneer_chapter_icon',
    array(
      'type' => 'string',
      'single' => true,
      'show_in_rest' => array(
        'schema' => array(
          'type' => 'string',
          'default' => FICTIONEER_DEFAULT_CHAPTER_ICON
        )
      ),
      'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) {
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_chapter' );
      },
      'sanitize_callback' => 'sanitize_text_field'
    )
  );

  register_post_meta(
    'fcn_chapter',
    'fictioneer_chapter_text_icon',
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
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_chapter' );
      },
      'sanitize_callback' => 'sanitize_text_field'
    )
  );

  register_post_meta(
    'fcn_chapter',
    'fictioneer_chapter_short_title',
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
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_chapter' );
      },
      'sanitize_callback' => 'sanitize_text_field'
    )
  );

  register_post_meta(
    'fcn_chapter',
    'fictioneer_chapter_prefix',
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
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_chapter' );
      },
      'sanitize_callback' => 'sanitize_text_field'
    )
  );

  register_post_meta(
    'fcn_chapter',
    'fictioneer_chapter_co_authors',
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
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_chapter' );
      },
      'sanitize_callback' => function( $meta_value ) {
        if ( is_null( $meta_value ) || ! is_array( $meta_value ) ) {
          return [];
        }

        $meta_value = array_map( 'intval', $meta_value );
        $meta_value = array_filter( $meta_value, fn( $user_id ) => $user_id > 0 );
        $meta_value = array_unique( $meta_value );
        $meta_value = array_filter( $meta_value, fn( $user_id ) => get_userdata( $user_id ) !== false );
        $meta_value = array_map( 'strval', $meta_value );
        $meta_value = array_values( $meta_value );

        return $meta_value;
      }
    )
  );

  register_post_meta(
    'fcn_chapter',
    'fictioneer_chapter_rating',
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
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_chapter' );
      },
      'sanitize_callback' => function( $meta_value ) {
        $meta_value = strtolower( trim( $meta_value ) );

        return in_array( $meta_value, ['everyone', 'teen', 'mature', 'adult'] )
          ? ucfirst( $meta_value ) : 'Everyone';
      }
    )
  );

  register_post_meta(
    'fcn_chapter',
    'fictioneer_chapter_warning',
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
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_chapter' );
      },
      'sanitize_callback' => function( $meta_value ) {
        $meta_value = sanitize_text_field( $meta_value );
        $meta_value = fictioneer_truncate( $meta_value, 48 );

        return $meta_value;
      }
    )
  );

  register_post_meta(
    'fcn_chapter',
    'fictioneer_chapter_warning_notes',
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
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_chapter' );
      },
      'sanitize_callback' => 'sanitize_textarea_field'
    )
  );

  register_post_meta(
    'fcn_chapter',
    'fictioneer_chapter_hidden',
    array(
      'type' => 'boolean',
      'single' => true,
      'show_in_rest' => array(
        'schema' => array(
          'type' => 'boolean'
        )
      ),
      'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) {
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_chapter' );
      },
      'sanitize_callback' => 'fictioneer_sanitize_checkbox'
    )
  );

  register_post_meta(
    'fcn_chapter',
    'fictioneer_chapter_no_chapter',
    array(
      'type' => 'boolean',
      'single' => true,
      'show_in_rest' => array(
        'schema' => array(
          'type' => 'boolean'
        )
      ),
      'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) {
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_chapter' );
      },
      'sanitize_callback' => 'fictioneer_sanitize_checkbox'
    )
  );

  register_post_meta(
    'fcn_chapter',
    'fictioneer_chapter_hide_title',
    array(
      'type' => 'boolean',
      'single' => true,
      'show_in_rest' => array(
        'schema' => array(
          'type' => 'boolean'
        )
      ),
      'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) {
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_chapter' );
      },
      'sanitize_callback' => 'fictioneer_sanitize_checkbox'
    )
  );

  register_post_meta(
    'fcn_chapter',
    'fictioneer_chapter_hide_support_links',
    array(
      'type' => 'boolean',
      'single' => true,
      'show_in_rest' => array(
        'schema' => array(
          'type' => 'boolean'
        )
      ),
      'auth_callback' => function( $allowed, $meta_key, $object_id, $user_id ) {
        return fictioneer_rest_auth_callback( $object_id, $user_id, 'fcn_chapter' );
      },
      'sanitize_callback' => 'fictioneer_sanitize_checkbox'
    )
  );
}
add_action( 'init', 'fictioneer_register_chapter_meta_fields' );
