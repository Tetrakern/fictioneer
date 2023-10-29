<?php

/**
 * This is a discard pile for functions and snippets that are no longer in use
 * but may prove useful in the future (or were removed by mistake). Keeping them
 * in a searchable file is easier that sorting through my poorly written commits.
*/

// =============================================================================
// SHOW PATREON TIERS
// =============================================================================

/**
 * Adds HTML for the Patreon tiers section to the wp-admin user profile
 *
 * DISABLED!
 *
 * @since Fictioneer 5.0
 *
 * @param WP_User $profile_user The profile user object. Not necessarily the one
 *                              currently editing the profile!
 */

function fictioneer_admin_profile_patreon_tiers( $profile_user ) {
  // Setup
  $editing_user_is_admin = fictioneer_is_admin( get_current_user_id() );

  // Abort conditions...
  if ( ! $editing_user_is_admin ) return;

  // Start HTML ---> ?>
  <tr class="user-patreon-tiers-wrap">
    <th><label for="fictioneer_patreon_tiers"><?php _e( 'Patreon Tiers', 'fictioneer' ); ?></label></th>
    <td>
      <input name="fictioneer_patreon_tiers" type="text" id="fictioneer_patreon_tiers" value="<?php echo esc_attr( json_encode( get_the_author_meta( 'fictioneer_patreon_tiers', $profile_user->ID ) ) ); ?>" class="regular-text" readonly>
      <p class="description"><?php _e( 'Patreon tiers for the linked Patreon client. Valid for two weeks after last login.', 'fictioneer' ); ?></p>
    </td>
  </tr>
  <?php // <--- End HTML
}
// add_action( 'fictioneer_admin_user_sections', 'fictioneer_admin_profile_patreon_tiers', 60 );

// =============================================================================
// SHOW BOOKMARKS SECTION
// =============================================================================

/**
 * Adds HTML for the bookmarks section to the wp-admin user profile
 *
 * DISABLED!
 *
 * @since Fictioneer 5.0
 *
 * @param WP_User $profile_user The profile user object. Not necessarily the one
 *                              currently editing the profile!
 */

function fictioneer_admin_profile_bookmarks( $profile_user ) {
  // Setup
  $editing_user_is_admin = fictioneer_is_admin( get_current_user_id() );

  // Abort conditions...
  if ( ! $editing_user_is_admin ) return;

  // Start HTML ---> ?>
  <tr class="user-bookmarks-wrap">
    <th><label for="user_bookmarks"><?php _e( 'Bookmarks JSON', 'fictioneer' ); ?></label></th>
    <td>
      <textarea name="user_bookmarks" id="user_bookmarks" rows="12" cols="30" style="font-family: monospace; font-size: 12px; word-break: break-all;" disabled><?php echo esc_attr( get_the_author_meta( 'fictioneer_bookmarks', $profile_user->ID ) ); ?></textarea>
    </td>
  </tr>
  <?php // <--- End HTML
}
// add_action( 'fictioneer_admin_user_sections', 'fictioneer_admin_profile_bookmarks', 70 );

/**
 * Restrict access to admin pages/actions for moderators
 *
 * Prevent moderators from editing the comment content, which is rarely a good action.
 * Doing this via JS is a terrible solution but better than nothing.
 *
 * @since Fictioneer 5.0
 * @todo Do this server-side to prevent savvy moderators from circumventing it.
 */

function fictioneer_disable_moderator_comment_edit() {
  wp_add_inline_script(
    'fictioneer-admin-script',
    "jQuery(function($) {
      $('#qt_content_toolbar').remove();
      $('.wp-editor-area').prop('disabled', true);
      $('.wp-editor-area').prop('name', '');
    });"
  );
}

// =============================================================================
// REQUEST COMMENT FORM - AJAX (DOES NOT WORK WITH CURRENT USER!)
// > Issue: The current user requires a specific nonce to be present, which will
// > cause a lot of headache with caching plugins.
// =============================================================================

/**
 * Register REST API endpoint to fetch comment form
 *
 * Endpoint URL: /wp-json/fictioneer/v1/get_comment_form
 *
 * Expected Parameters:
 * - post_id (required): The ID of the story post.
 *
 * @since Fictioneer 5.6.3
 */

function fictioneer_register_endpoint_get_comment_form() {
  register_rest_route(
    'fictioneer/v1',
    '/get_comment_form',
    array(
      'methods' => 'GET',
      'callback' => 'fictioneer_rest_get_comment_form',
      'args' => array(
        'post_id' => array(
          'validate_callback' => function( $param, $request, $key ) {
            return is_numeric( $param );
          },
          'sanitize_callback' => 'absint',
          'required' => true
        )
      ),
      'permission_callback' => '__return_true' // Public
    )
  );
}

if ( get_option( 'fictioneer_enable_ajax_comment_form' ) ) {
  add_action( 'rest_api_init', 'fictioneer_register_endpoint_get_comment_form' );
}

/**
 * Sends the comment form HTML
 *
 * @since Fictioneer 5.0
 * @since Fictioneer 5.6.3 Refactored for REST API.
 *
 * @param WP_REST_Request $WP_REST_Request  Request object.
 *
 * @return WP_REST_Response|WP_Error Response or error.
 */

function fictioneer_rest_get_comment_form( WP_REST_Request $request ) {
  // Setup
  $post_id = $request->get_param( 'post_id' );
  $must_login = get_option( 'comment_registration' ) && ! is_user_logged_in();

  // Get buffered form
  ob_start();

  if ( get_option( 'fictioneer_disable_comment_form' ) ) {
    comment_form( [], $post_id );
  } else {
    comment_form( fictioneer_comment_form_args( [], $post_id ), $post_id );
  }

  // Get buffer
  $output = ob_get_clean();

  // Response body (compatible to wp_send_json_success())
  $data = array( 'html' => $output, 'postId' => $post_id, 'mustLogin' => $must_login );

  // Return buffer
  return rest_ensure_response( array( 'data' => $data, 'success' => true ) );
}

// =============================================================================
// REQUEST COMMENT SECTION - REST (DOES NOT WORK WITH CURRENT USER!)
// > Issue: The current user requires a specific nonce to be present, which will
// > cause a lot of headache with caching plugins.
// =============================================================================

/**
 * Register REST API endpoint to fetch comment section
 *
 * Endpoint URL: /wp-json/fictioneer/v1/get_comment_section
 *
 * Expected Parameters:
 * - post_id (required): The ID of the story post.
 *
 * @since Fictioneer 5.6.3
 */

function fictioneer_register_endpoint_get_comment_section() {
  register_rest_route(
    'fictioneer/v1',
    '/get_comment_section',
    array(
      'methods' => 'GET',
      'callback' => 'fictioneer_rest_get_comment_section',
      'args' => array(
        'post_id' => array(
          'validate_callback' => function( $param, $request, $key ) {
            return is_numeric( $param );
          },
          'sanitize_callback' => 'absint',
          'required' => true
        ),
        'page' => array(
          'validate_callback' => function( $param, $request, $key ) {
            return is_numeric( $param );
          },
          'sanitize_callback' => 'absint',
          'default' => 1
        ),
        'commentcode' => array(
          'validate_callback' => function( $param, $request, $key ) {
            return empty( $param ) || preg_match( '/^[a-zA-Z0-9]{13,23}$/', $param ) === 1;
          },
          'sanitize_callback' => 'sanitize_text_field',
          'default' => ''
        )
      ),
      'permission_callback' => '__return_true' // Public
    )
  );
}

if ( get_option( 'fictioneer_enable_ajax_comments' ) ) {
  add_action( 'rest_api_init', 'fictioneer_register_endpoint_get_comment_section' );
}

/**
 * Sends the comment section HTML
 *
 * @since Fictioneer 5.0
 * @since Fictioneer 5.6.3 Refactored for REST API.
 *
 * @param WP_REST_Request $WP_REST_Request  Request object.
 *
 * @return WP_REST_Response|WP_Error Response or error.
 */

function fictioneer_rest_get_comment_section( WP_REST_Request $request ) {
  // Setup
  $post_id = $request->get_param( 'post_id' );
  $page = $request->get_param( 'page' );
  $commentcode = $request->get_param( 'commentcode' ) ?: false;
  $post = get_post( $post_id );
  $must_login = get_option( 'comment_registration' ) && ! is_user_logged_in();
  $default_error = array( 'error' => __( 'Comments could not be loaded.', 'fictioneer' ) );

  // Abort if post not found
  if ( empty( $post ) ) {
    if ( WP_DEBUG ) {
      $data = array( 'error' => __( 'Provided post ID does not match any post.', 'fictioneer' ) );
    } else {
      $data = $default_error;
    }

    return rest_ensure_response( array( 'data' => $data, 'success' => false ), 400 );
  }

  // Abort if password required
  if ( post_password_required( $post ) ) {
    $data = array( 'error' => __( 'Password required.', 'fictioneer' ) );
    return rest_ensure_response( array( 'data' => $data, 'success' => false ), 403 );
  }

  // Abort if comments are closed
  if ( ! comments_open( $post ) ) {
    $data = array( 'error' => __( 'Commenting is disabled.', 'fictioneer' ) );
    return rest_ensure_response( array( 'data' => $data, 'success' => false ), 403 );
  }

  // Query arguments
  $query_args = array( 'post_id' => $post_id );

  if ( ! get_option( 'fictioneer_disable_comment_query' ) ) {
    $query_args['type'] = ['comment', 'private'];
    $query_args['order'] = get_option( 'comment_order' );
  } else {
    // Still hide private comments but do not limit the types preemptively
    $query_args = array( 'type__not_in' => 'private' );
  }

  // Filter query arguments
  $query_args = apply_filters( 'fictioneer_filter_comments_query', $query_args, $post_id );

  // Query comments
  $comments_query = new WP_Comment_Query( $query_args );
  $comments = $comments_query->comments;

  // Filter comments
  $comments = apply_filters( 'fictioneer_filter_comments', $comments, $post_id );

  // Pagination
  $max_pages = get_comment_pages_count( $comments );
  $page = min( $max_pages, $page );
  $page = max( 1, $page );

  // Start buffer
  ob_start();

  // Header
  fictioneer_comment_header( get_comments_number( $post_id ) );

  // Form
  if ( ! fictioneer_is_commenting_disabled( $post_id ) ) {
    if ( get_option( 'fictioneer_disable_comment_form' ) ) {
      comment_form( [], $post_id );
    } else {
      comment_form( fictioneer_comment_form_args( [], $post_id ), $post_id );
    }
  }

  // List
  fictioneer_ajax_list_comments(
    $comments,
    $page,
    array(
      'commentcode' => $commentcode,
      'post_author_id' => $post->post_author,
      'post_id' => $post_id
    )
  );

  // Navigation
  if ( $max_pages > 1 ) {
    // Start HTML ---> ?>
    <nav class="pagination comments-pagination _padding-top">
      <?php
        $steps = fictioneer_balance_pagination_array( $max_pages, $page );

        foreach ( $steps as $step ) {
          switch ( $step ) {
            case $page:
              ?><span class="page-numbers current" aria-current="page"><?php echo $step; ?></span><?php
              break;
            case '…':
              ?><button type="button" class="page-numbers dots" data-page-jump><?php echo $step; ?></button><?php
              break;
            default:
              ?><button type="button" class="page-numbers" data-page="<?php echo $step; ?>"><?php echo $step; ?></button><?php
          }
        }
      ?>
    </nav>
    <?php // <--- End HTML
  }

  // Get buffer
  $output = ob_get_clean();

  // Response body (compatible to wp_send_json_success())
  $data = array( 'html' => $output, 'postId' => $post_id, 'page' => $page, 'mustLogin' => $must_login );

  // Return buffer
  return rest_ensure_response( array( 'data' => $data, 'success' => true ) );
}

// =============================================================================
// VALIDATE & BALANCE BBCODES
// > Issue: Just not working, too stupid to make it work lol
// > except for the finding functions, they did their job
// =============================================================================

function find_back_closing_tag( $content, $tag ) {
  $pattern = '/\[' . preg_quote( $tag, '/' ) . '[^\]]*\]/';

  if ( preg_match_all( $pattern, $content, $matches, PREG_OFFSET_CAPTURE ) ) {
    $last = end( $matches[1] );
    $offset = $last[1];

    return [$tag, $offset];
  }

  return false;
}

function find_back_opening_tag( $content, $tag, $closingOffset ) {
  $pattern = '/\[' . preg_quote( $tag, '/' ) . '[^\]]*\]/';
  $sub_content = substr( $content, 0, $closingOffset );

  if ( preg_match_all( $pattern, $sub_content, $matches, PREG_OFFSET_CAPTURE )) {
    $last = end( $matches[0] );
    $tag = $last[0];
    $offset = $last[1];
    $tag_length = strlen( $tag );
    $inner_offset = $offset + $tag_length;
    $inner_content = substr( $sub_content, $inner_offset, strlen( $sub_content ) - $offset - $tag_length - 2 );

    return [$tag, $offset, $inner_content, $inner_offset ];
  }

  return false;
}

function validate_tags($content) {
    $stack = [];
    $validTags = ['b', 'i', 's', 'img']; // List of valid tags
    $maybeValidStack = [];  // A secondary stack to track "maybe valid" tags

    preg_match_all('/\[(\/?)([a-zA-Z0-9]+)([^\]]*)\]/', $content, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

    foreach ($matches as $match) {
        $tag = $match[2][0];
        $offset = $match[0][1];
        $fullTag = $match[0][0];

        if (!in_array($tag, $validTags)) {
            continue; // Skip invalid tags
        }

        $dummyString = str_repeat("#", strlen($fullTag));  // Dynamic-length dummy string

        if ($match[1][0] === '/') {
            // It's a closing tag
            if (!empty($stack) && end($stack)['tag'] === $tag) {
                array_pop($stack); // Remove the last element from the stack
                if (!empty($maybeValidStack)) {
                    array_pop($maybeValidStack);  // Remove the corresponding "maybe valid" element
                }
            } else {
                // Remove this closing tag
                $content = substr_replace($content, $dummyString, $offset, strlen($fullTag));

                // Remove all "maybe valid" tags from the content and clear the secondary stack
                foreach ($maybeValidStack as $unmatched) {
                    $dummyStringForUnmatched = str_repeat("#", strlen($unmatched['fullTag']));
                    $content = substr_replace($content, $dummyStringForUnmatched, $unmatched['offset'], strlen($unmatched['fullTag']));
                }
                $maybeValidStack = [];
            }
        } else {
            // It's an opening tag
            $stack[] = ['tag' => $tag, 'offset' => $offset, 'fullTag' => $fullTag];
            $maybeValidStack[] = ['tag' => $tag, 'offset' => $offset, 'fullTag' => $fullTag];
        }
    }

    // Remove any unmatched opening tags
    foreach (array_reverse($stack) as $unmatched) {
        $dummyStringForUnmatched = str_repeat("#", strlen($unmatched['fullTag']));
        $content = substr_replace($content, $dummyStringForUnmatched, $unmatched['offset'], strlen($unmatched['fullTag']));
    }

    return $content;
}

// =============================================================================
// GET CHECKMARKS - AJAX
// =============================================================================

/**
 * Sends the user's Checkmarks as JSON via Ajax
 *
 * @since Fictioneer 4.0
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
 * @see fictioneer_get_validated_ajax_user()
 * @see fictioneer_load_checkmarks()
 */

function fictioneer_ajax_get_checkmarks() {
  // Setup and validations
  $user = fictioneer_get_validated_ajax_user();

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => __( 'Request did not pass validation.', 'fictioneer' ) ) );
  }

  // Prepare Checkmarks
  $checkmarks = fictioneer_load_checkmarks( $user );
  $checkmarks['timestamp'] = time() * 1000; // Compatible with Date.now() in JavaScript

  // Response
  wp_send_json_success( array( 'checkmarks' => json_encode( $checkmarks ) ) );
}

if ( get_option( 'fictioneer_enable_checkmarks' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_get_checkmarks', 'fictioneer_ajax_get_checkmarks' );
}

// =============================================================================
// GET REMINDERS - AJAX
// =============================================================================

/**
 * Sends the user's Reminders as JSON via Ajax
 *
 * @since Fictioneer 5.0
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
 * @see fictioneer_get_validated_ajax_user()
 * @see fictioneer_load_reminders()
 */

function fictioneer_ajax_get_reminders() {
  // Setup and validations
  $user = fictioneer_get_validated_ajax_user();

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => __( 'Request did not pass validation.', 'fictioneer' ) ) );
  }

  // Prepare Reminders
  $reminders = fictioneer_load_reminders( $user );
  $reminders['timestamp'] = time() * 1000; // Compatible with Date.now() in JavaScript

  // Response
  wp_send_json_success( array( 'reminders' => json_encode( $reminders ) ) );
}

if ( get_option( 'fictioneer_enable_reminders' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_get_reminders', 'fictioneer_ajax_get_reminders' );
}

// =============================================================================
// GET FOLLOWS - AJAX
// =============================================================================

/**
 * Sends the user's Follows as JSON via Ajax
 *
 * @since Fictioneer 4.3
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
 * @see fictioneer_get_validated_ajax_user()
 */

function fictioneer_ajax_get_follows() {
  // Setup and validations
  $user = fictioneer_get_validated_ajax_user();

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => __( 'Request did not pass validation.', 'fictioneer' ) ) );
  }

  // Prepare Follows
  $user_follows = fictioneer_load_follows( $user );
  $user_follows['timestamp'] = time() * 1000; // Compatible with Date.now() in JavaScript
  $latest = 0;
  $new = false;

  // New notifications?
  if ( count( $user_follows['data'] ) > 0 ) {
    $latest = fictioneer_query_followed_chapters(
      array_keys( $user_follows['data'] ),
      wp_date( 'c', $user_follows['seen'] / 1000 )
    );

    if ( $latest ) {
      $new = count( $latest );
    }
  }

  $user_follows['new'] = $new;

  // Response
  wp_send_json_success( array( 'follows' => json_encode( $user_follows ) ) );
}

if ( get_option( 'fictioneer_enable_follows' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_get_follows', 'fictioneer_ajax_get_follows' );
}

// =============================================================================
// FETCH FINGERPRINT - AJAX
// =============================================================================

/**
 * Sends the user's fingerprint via AJAX
 *
 * @since Fictioneer 5.0
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
 * @see fictioneer_get_validated_ajax_user()
 */

function fictioneer_ajax_get_fingerprint() {
  // Setup and validations
  $user = fictioneer_get_validated_ajax_user();

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => __( 'Request did not pass validation.', 'fictioneer' ) ) );
  }

  // Response
  wp_send_json_success( array( 'fingerprint' => fictioneer_get_user_fingerprint( $user->ID ) ) );
}
add_action( 'wp_ajax_fictioneer_ajax_get_fingerprint', 'fictioneer_ajax_get_fingerprint' );

// =============================================================================
// GET BOOKMARKS FOR USERS - AJAX
// =============================================================================

/**
 * Get an user's bookmarks via AJAX
 *
 * @since Fictioneer 4.0
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
 * @see fictioneer_get_validated_ajax_user()
 */

function fictioneer_ajax_get_bookmarks() {
  // Enabled?
  if ( ! get_option( 'fictioneer_enable_bookmarks' ) ) {
    wp_send_json_error(
      array( 'error' => __( 'Not allowed.', 'fictioneer' ) ),
      403
    );
  }

  // Setup and validations
  $user = fictioneer_get_validated_ajax_user();

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => __( 'Request did not pass validation.', 'fictioneer' ) ) );
  }

  // Look for saved bookmarks on user...
  $bookmarks = get_user_meta( $user->ID, 'fictioneer_bookmarks', true );
  $bookmarks = $bookmarks ? $bookmarks : '{}';

  // Response
  if ( $bookmarks ) {
    wp_send_json_success( array( 'bookmarks' => $bookmarks ) );
  }
}

if ( get_option( 'fictioneer_enable_bookmarks' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_get_bookmarks', 'fictioneer_ajax_get_bookmarks' );
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
   * @param WP_User|int $user  The user object or ID to check.
   * @param string      $role  The role to check for.
   *
   * @return boolean To be or not to be.
   */

  function fictioneer_has_role( $user, $role ) {
    // Setup
    $user = is_int( $user ) ? get_user_by( 'ID', $user ) : $user;

    // Abort conditions
    if ( ! $user || ! $role ) {
      return false;
    }

    // Check if user has role...
    if ( in_array( $role, (array) $user->roles ) ) {
      return true;
    }

    // Else...
    return false;
  }
}

?>
