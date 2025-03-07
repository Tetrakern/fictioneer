<?php

// =============================================================================
// FIX COMMENT POST REDIRECT
// =============================================================================

/**
 * Fix redirect after posting a comment.
 *
 * Due to the modified query, after posting a comment, the commenter may be
 * redirected to the wrong comment page. This resolves the issue and also adds
 * a query argument with a comment's visibility code to allow guests without
 * email and cookies to preview their comment for a limited time as set in
 * fictioneer_theme_comment().
 *
 * @since 4.7.0
 *
 * @param string $location     Default redirect URI.
 * @param object $commentdata  Comment data.
 *
 * @return string Either $location or a new redirect URI.
 */

function fictioneer_redirect_comment( $location, $commentdata ) {
  if ( ! isset( $commentdata ) || empty( $commentdata->comment_post_ID ) ) {
    return $location;
  }

  // Setup
  $order = fictioneer_sanitize_query_var( $_REQUEST['corder'] ?? 0, ['desc', 'asc'], get_option( 'comment_order' ) );
  $commentcode = wp_hash( $commentdata->comment_date_gmt );

  if ( ( $_REQUEST['for'] ?? 0 ) === 'jetpack' ) {
    $location = get_option( 'default_comments_page' ) === 'oldest' ?
      get_permalink( $commentdata->comment_post_ID ) . '#comments' : $location;

    if ( $commentcode && ! $commentdata->comment_approved ) {
      return add_query_arg(
        array(
          'corder' => $order,
          'commentcode' => $commentcode
        ),
        $location
      );
    } else {
      return add_query_arg(
        array(
          'corder' => $order
        ),
        $location
      );
    }
  }

  // Redirect to last page
  if ( $order === 'asc' && ! $commentdata->comment_parent ) {
    return add_query_arg(
      array(
        'corder' => $order,
        'commentcode' => $commentcode
      ),
      $location
    );
  }

  // Redirect to current page
  if ( wp_get_referer() ) {
    if ( $commentcode && ! $commentdata->comment_approved ) {
      return add_query_arg(
        'commentcode',
        $commentcode,
        wp_get_referer() . '#comment-' . $commentdata->comment_ID
      );
    } else {
      return wp_get_referer() . '#comment-' . $commentdata->comment_ID;
    }
  }

  // Default
  return $location;
}

if ( ! get_option( 'fictioneer_disable_comment_query' ) ) {
  add_filter( 'comment_post_redirect', 'fictioneer_redirect_comment', 10, 2 );
}

// =============================================================================
// DO NOT SAVE COMMENT IP ADDRESS
// =============================================================================

/**
 * Prevents the commenter’s IP address from being stored in the database
 *
 * @since 4.7.0
 */

if ( get_option( 'fictioneer_do_not_save_comment_ip' ) ) {
  add_filter( 'pre_comment_user_ip', '__return_empty_string' );
}

// =============================================================================
// PREPROCESS COMMENTS
// =============================================================================

/**
 * Applies anti-spam measures, checks for flags, filters the content, etc.
 *
 * @since 4.7.0
 *
 * @param array $commentdata  Comment data.
 *
 * @return array The preprocessed comment data.
 */

function fictioneer_preprocess_comment( $commentdata ) {
  // Setup
  $current_user = wp_get_current_user();
  $is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;
  $jetpack_comments = class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'comments' );

  // Commenting disabled
  if ( fictioneer_is_commenting_disabled( $commentdata['comment_post_ID'] ) ) {
    if ( $is_ajax ) {
      wp_send_json_error( array( 'failure' => __( 'Commenting disabled.', 'fictioneer' ) ) );
    } else {
      wp_die( 'Commenting disabled.' );
    }
  }

  // Require JS to comment unless the commenter is logged-in
  // and has comment moderation capabilities.
  if (
    get_option( 'fictioneer_require_js_to_comment' ) &&
    ! current_user_can( 'moderate_comments' ) &&
    ! $jetpack_comments
  ) {
    if ( ! isset( $_POST['fictioneer_comment_validator'] ) || ! $_POST['fictioneer_comment_validator'] ) {
      if ( $is_ajax ) {
        wp_send_json_error( array( 'error' => 'Comment did not pass validation.' ) );
      } else {
        wp_die( 'Comment did not pass validation.' );
      }
    }
  }

  // Check fictioneer_admin_disable_commenting user flag
  if ( $current_user->fictioneer_admin_disable_commenting ) {
    if ( $is_ajax ) {
      wp_send_json_error( array( 'failure' => __( 'Commenting capability disabled.', 'fictioneer' ) ) );
    } else {
      wp_die( __( 'Commenting capability disabled.', 'fictioneer' ) );
    }
  }

  // Check link limit
  if ( get_option( 'fictioneer_enable_comment_link_limit' ) ) {
    // Count links in comment the non-sophisticated way...
    $link_count = substr_count( $commentdata['comment_content'], 'http' );
    $max_links = get_option( 'fictioneer_comment_link_limit_threshold', 3 );

    // No more than n links allowed (unless you have moderation capabilities)
    if ( $link_count > $max_links && ! current_user_can( 'moderate_comments' ) ) {
      $error = sprintf( __( 'You cannot post more than %s links.', 'fictioneer' ), $max_links );

      if ( $is_ajax ) {
        wp_send_json_error( array( 'failure' => $error ) );
      } else {
        wp_die( $error );
      }
    }
  }

  // Change allowed tags
  global $allowedtags;

  if ( ! current_user_can( 'fcn_simple_comment_html' ) && ! current_user_can( 'unfiltered_html' ) ) {
    $allowedtags = [];
  }

  // Continue filter
  return $commentdata;
}
add_filter( 'preprocess_comment', 'fictioneer_preprocess_comment', 30, 1 );

// =============================================================================
// VALIDATE COMMENT FORM
// =============================================================================

/**
 * Validate comment form submission
 *
 * @since 4.7.0
 * @link  https://developer.wordpress.org/reference/hooks/preprocess_comment/
 *
 * @param array $commentdata  Comment data.
 */

function fictioneer_validate_comment_form( $commentdata ) {
  // Setup
  $parent_id = absint( $commentdata['comment_parent'] ?? 0 );
  $is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;
  $jetpack_comments = class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'comments' );

  // Check privacy consent for guests
  if (
    ! is_user_logged_in() &&
    ! $is_ajax && get_option( 'wp_page_for_privacy_policy' ) &&
    ! $jetpack_comments
  ) {
    // You cannot post a comment without accepting the privacy policy (tested earlier on AJAX submit!)
    if ( ! filter_var( $_POST['fictioneer-privacy-policy-consent'] ?? 0, FILTER_VALIDATE_BOOLEAN ) ) {
      wp_die( __( 'You did not accept the privacy policy.', 'fictioneer' ) );
    }
  }

  // Abort if direct parent comment is deleted
  if ( $parent_id && $commentdata['comment_type'] === 'user_deleted' ) {
    if ( $is_ajax ) {
      wp_send_json_error( array( 'failure' => __( 'You cannot reply to deleted comments.', 'fictioneer' ) ) );
    } else {
      wp_die( __( 'You cannot reply to deleted comments.', 'fictioneer' ) );
    }
  }

  // Abort if direct parent comment is marked as offensive
  if ( $parent_id && get_comment_meta( $parent_id, 'fictioneer_marked_offensive', true ) ) {
    if ( $is_ajax ) {
      wp_send_json_error( array( 'failure' => __( 'You cannot reply to comments marked as offensive.', 'fictioneer' ) ) );
    } else {
      wp_die( __( 'You cannot reply to comments marked as offensive.', 'fictioneer' ) );
    }
  }

  // Abort if parent comment or ancestors are closed
  if ( $parent_id && ! get_option( 'fictioneer_disable_comment_callback' ) ) {
    if ( get_comment_meta( $parent_id, 'fictioneer_thread_closed', true ) ) {
      if ( $is_ajax ) {
        wp_send_json_error( array( 'failure' => __( 'Comment thread is closed.', 'fictioneer' ) ) );
      } else {
        wp_die( __( 'Comment thread is closed.', 'fictioneer' ) );
      }
    }

    $ancestor = get_comment( $parent_id );

    while ( $ancestor ) {
      if ( ! is_object( $ancestor ) ) {
        break;
      }

      // Ancestor closed?
      if ( get_comment_meta( $ancestor->comment_ID, 'fictioneer_thread_closed', true ) ) {
        if ( $is_ajax ) {
          wp_send_json_error( array( 'failure' => __( 'Comment thread is closed.', 'fictioneer' ) ) );
        } else {
          wp_die( __( 'Comment thread is closed.', 'fictioneer' ) );
        }
      }

      // Next ancestor if any
      $ancestor = $ancestor->comment_parent ? get_comment( $ancestor->comment_parent ) : false;
    }
  }

  // Mark comment as private
  if (
    get_option( 'fictioneer_enable_private_commenting' ) &&
    filter_var( $_POST['fictioneer-private-comment-toggle'] ?? 0, FILTER_VALIDATE_BOOLEAN )
  ) {
    $commentdata['comment_type'] = 'private';
  }

  // Mark private if parent is private
  if ( $parent_id && get_comment_type( $parent_id ) == 'private' ) {
    $commentdata['comment_type'] = 'private';
  }

  // Enable email notification for replies
  if (
    get_option( 'fictioneer_enable_comment_notifications' ) &&
    filter_var( $_POST['fictioneer-comment-notification-toggle'] ?? 0, FILTER_VALIDATE_BOOLEAN )
  ) {
    $commentdata['notification'] = true;
  }

  // Continue filter
  return $commentdata;
}

if ( ! get_option( 'fictioneer_disable_comment_form' ) ) {
  add_filter( 'preprocess_comment', 'fictioneer_validate_comment_form', 20, 1 );
}

// =============================================================================
// COMMENT POST ACTION
// =============================================================================

/**
 * Fires after the comment has been inserted into the database
 *
 * @since 4.7.0
 * @link https://developer.wordpress.org/reference/hooks/comment_post/
 * @link https://github.com/WordPress/WordPress/blob/master/wp-includes/comment.php
 *
 * @param int        $comment_id        The comment ID.
 * @param int|string $comment_approved  1 if the comment is approved, 0 if not, 'spam' if spam.
 * @param array      $commentdata       Comment data.
 */

function fictioneer_comment_post( $comment_id, $comment_approved, $commentdata ) {
  // Hold for moderation if user is flagged
  if ( get_user_meta( $commentdata['user_id'], 'fictioneer_admin_always_moderate_comments', true ) ) {
    wp_set_comment_status( $comment_id, 'hold' );
  }

  // Purge cache of post/page the comment is for
  if ( fictioneer_caching_active( 'comment_post' ) && isset( $commentdata['comment_post_ID'] ) ) {
    fictioneer_purge_post_cache( $commentdata['comment_post_ID'] );
  }

  // Notification validator determines whether a subscription is active; change the validator
  // and all associated comment reply subscriptions are terminated
  $notification_validator = get_user_meta( $commentdata['user_id'], 'fictioneer_comment_reply_validator', true );

  if ( empty( $notification_validator ) ) {
    $notification_validator = time();
    fictioneer_update_user_meta( $commentdata['user_id'], 'fictioneer_comment_reply_validator', $notification_validator );
  }

  // If user opted for email notifications
  if ( filter_var( $_POST['fictioneer-comment-notification-toggle'] ?? 0, FILTER_VALIDATE_BOOLEAN ) ) {
    update_comment_meta( $comment_id, 'fictioneer_send_notifications', $notification_validator );
    // Reset code to stop notifications (sufficiently unique for this purpose)
    update_comment_meta( $comment_id, 'fictioneer_notifications_reset', md5( wp_generate_password() ) );
  }

  // Anything related to comment parent (if any)...
  $parent_comment = get_comment( $commentdata['comment_parent'] );

  if ( $parent_comment ) {
    $parent_notification_check = get_comment_meta( $commentdata['comment_parent'], 'fictioneer_send_notifications', true );
    $parent_notification_validator = get_user_meta( $parent_comment->user_id, 'fictioneer_comment_reply_validator', true );

    // Send notification to parent comment author (if wanted)
    if ( get_option( 'fictioneer_enable_comment_notifications' ) && ! empty( $parent_notification_check ) ) {
      // Parent comment user guest or member?
      if ( $parent_comment->user_id == 0 || $parent_notification_check == $parent_notification_validator ) {
        fictioneer_comment_notification( $parent_comment, $comment_id, $commentdata );
      }
    }
  }
}

if ( ! get_option( 'fictioneer_disable_comment_form' ) ) {
  add_action( 'comment_post', 'fictioneer_comment_post', 20, 3 );
}

// =============================================================================
// COMMENT NOTIFICATION EMAIL
// =============================================================================

if ( ! function_exists( 'fictioneer_comment_notification' ) ) {
  /**
   * Send notification email about replies to commenter
   *
   * @since 5.0.0
   *
   * @param WP_Comment $parent       Comment that was replied to.
   * @param int        $comment_id   ID of the reply comment.
   * @param array      $commentdata  Fields of the reply comment.
   */

  function fictioneer_comment_notification( WP_Comment $parent, int $comment_id, array $commentdata ) {
    // Abort if...
    if ( empty( $parent->comment_author_email ) || ! $comment_id || empty( $commentdata['comment_content'] ) ) {
      return;
    }

    // Abort if notification capability is disabled
    if ( get_the_author_meta( 'fictioneer_admin_disable_comment_notifications', $parent->user_id ) ) {
      return;
    }

    // Setup
    $site_name = get_bloginfo( 'name' );
    $comment_text = apply_filters( 'get_comment_text', $commentdata['comment_content'] );
    $comment_text = apply_filters( 'comment_text', $comment_text );
    $comment_author = empty( $parent->comment_author ) ? fcntr( 'anonymous_guest' ) : $parent->comment_author;
    $reply_author = empty( $commentdata['comment_author'] ) ? fcntr( 'anonymous_guest' ) : $commentdata['comment_author'];
    $template = fictioneer_replace_key_value(
      wp_kses_post( get_option( 'fictioneer_phrase_comment_reply_notification' ) ),
      array(
        '[[post_id]]' => $parent->comment_post_ID,
        '[[post_url]]' => get_permalink( $parent->comment_post_ID ),
        '[[post_title]]' => fictioneer_get_safe_title( $parent->comment_post_ID, 'comment-notification' ),
        '[[comment_id]]' => $parent->comment_ID,
        '[[comment_name]]' => $comment_author,
        '[[comment_excerpt]]' => get_comment_excerpt( $parent->comment_ID ),
        '[[reply_id]]' => $comment_id,
        '[[reply_name]]' => $reply_author,
        '[[reply_date]]' => date_format(
          date_create( $commentdata['comment_date'] ),
          sprintf(
            _x( '%1$s \a\t %2$s', 'Comment time format string.', 'fictioneer' ),
            get_option( 'date_format' ),
            get_option( 'time_format' )
          )
        ),
        '[[reply_excerpt]]' => get_comment_excerpt( $comment_id ),
        '[[reply_content]]' => stripslashes( $comment_text ),
        '[[site_title]]' => $site_name,
        '[[site_url]]' => site_url(),
        '[[unsubscribe_url]]' => add_query_arg(
          'code',
          get_comment_meta( $parent->comment_ID, 'fictioneer_notifications_reset', true ),
          site_url( 'wp-json/fictioneer/v1/unsubscribe/comment/' . $parent->comment_ID )
        )
      ),
      __( 'Hello [[comment_name]],<br><br>you have a new reply to your comment in <a href="[[post_url]]">[[post_title]]</a>.<br><br><br><strong>[[reply_name]] replied on [[reply_date]]:</strong><br><br><fieldset>[[reply_content]]</fieldset><br><br><a href="[[unsubscribe_url]]">Unsubscribe from this comment.</a>', 'fictioneer' )
    );
    $from_address = get_option( 'fictioneer_system_email_address' );
    $from_name = get_option( 'fictioneer_system_email_name' );
    $from_name = empty ( $from_name ) ? $site_name : $from_name;
    $headers = [];
    $headers[] = 'Content-Type: text/html; charset=UTF-8';

    if ( ! empty( $from_address ) ) {
      $headers[] = 'From: ' . trim( $from_name )  . ' <'  . trim( $from_address ) . '>';
    }

    // Prepare arguments
    $mail = array(
      'to' => $parent->comment_author_email,
      'subject' => sprintf(
        _x( 'New reply to your comment on %s', 'Reply notification email subject.', 'fictioneer' ),
        $site_name
      ),
      'message' => $template,
      'headers' => $headers,
      'attachments' => []
    );

    // Apply filters
    $mail = apply_filters( 'fictioneer_filter_comment_reply_mail', $mail, $parent, $comment_id, $commentdata );

    // Send notification email
    wp_mail(
      $mail['to'],
      $mail['subject'],
      $mail['message'],
      $mail['headers'],
      $mail['attachments']
    );
  }
}

// =============================================================================
// UNSUBSCRIBE FROM COMMENT
// =============================================================================

/**
 * Add GET route to unsubscribe
 *
 * @since 5.0.0
 */

if ( get_option( 'fictioneer_enable_comment_notifications' ) ) {
  add_action(
    'rest_api_init',
    function () {
      register_rest_route(
        'fictioneer/v1',
        '/unsubscribe/comment/(?P<id>\d+)',
        array(
          'methods' => 'GET',
          'callback' => 'fictioneer_unsubscribe_from_comment',
          'permission_callback' => '__return_true'
        )
      );
    }
  );
}

if ( ! function_exists( 'fictioneer_unsubscribe_from_comment' ) ) {
  /**
   * Unsubscribe from comment reply notifications
   *
   * @since 5.0.0
   * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
   *
   * @param WP_REST_Request $request  Request object.
   */

  function fictioneer_unsubscribe_from_comment( WP_REST_Request $request ) {
    header( "Content-Type: text/html" );

    // Cache plugins
    do_action( 'litespeed_control_set_nocache', 'no-cache for unsubscribe api endpoint' );

    // Setup
    $comment_id = absint( $request['id'] );
    $code = $request->get_param( 'code' );

    // Abort if no code given
    if ( ! $code ) {
      _e( 'Invalid request!', 'fictioneer' );
      exit;
    }

    // Find comment
    $comment = get_comment( $comment_id );

    // Abort if no comment found
    if ( ! $comment ) {
      _e( 'Invalid request!', 'fictioneer' );
      exit;
    }

    // Abort if already unsubscribed
    if ( ! get_comment_meta( $comment->comment_ID, 'fictioneer_send_notifications', true ) ) {
      _e( 'Already unsubscribed!', 'fictioneer' );
      exit;
    }

    // Unsubscribe if code matches
    if ( $code == get_comment_meta( $comment->comment_ID, 'fictioneer_notifications_reset', true ) ) {
      // Remove meta
      fictioneer_update_comment_meta( $comment->comment_ID, 'fictioneer_send_notifications', false );
      fictioneer_update_comment_meta( $comment->comment_ID, 'fictioneer_notifications_reset', false );

      // Comment data
      $title = get_the_title( $comment->comment_post_ID );
      $permalink = get_permalink( $comment->comment_post_ID );

      // Output
      printf(
        __( 'Successfully unsubscribed from comment #%1$s in <a href="%2$s">%3$s</a>!', 'fictioneer' ),
        $comment->comment_ID,
        $permalink,
        $title
      );
    } else {
      // Generic error output
      _e( 'Invalid request!', 'fictioneer' );
    }
  }
}

// =============================================================================
// COMMENT EDIT ACTION
// =============================================================================

/**
 * Fires immediately after an edited comment is updated in the database
 *
 * @since 4.7.0
 * @link https://developer.wordpress.org/reference/hooks/edit_comment/
 *
 * @param int   $comment_ID  The comment ID.
 * @param array $data        Comment data.
 */

function fictioneer_comment_edit( $comment_ID, $data ) {
  // Purge cache of post/page the comment is for
  if ( fictioneer_caching_active( 'comment_edit' ) && isset( $data['comment_post_ID'] ) ) {
    fictioneer_purge_post_cache( $data['comment_post_ID'] );
  }
}

if ( ! get_option( 'fictioneer_disable_comment_form' ) ) {
  add_action( 'edit_comment', 'fictioneer_comment_edit', 20, 2 );
}

// =============================================================================
// COMMENTING ON SCHEDULED (FUTURE) POSTS
// =============================================================================

/**
 * Allow commenting on scheduled posts for logged-in users.
 *
 * @since 4.28.0
 */

if ( get_option( 'fictioneer_enable_scheduled_chapter_commenting' ) && is_user_logged_in() ) {
  if ( $_SERVER['REQUEST_METHOD'] === 'POST' && ! empty( $_POST['comment_post_ID'] ) ) {
    $comment_post = get_post( (int) $_POST['comment_post_ID'] );

    if ( $comment_post && $comment_post->post_status === 'future' ) {
      add_filter( 'get_post_status', 'fictioneer__return_publish_status' );
    }
  }

  $remove_filter_function = function() {
    remove_filter( 'get_post_status', 'fictioneer__return_publish_status' );
  };

  foreach ( [ 'wp_insert_comment', 'wp', 'pre_get_posts' ] as $hook ) {
    add_action( $hook, $remove_filter_function, 1 );
  }
}
