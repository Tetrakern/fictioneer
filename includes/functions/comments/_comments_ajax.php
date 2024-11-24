<?php

// =============================================================================
// REQUEST COMMENT FORM - AJAX
// =============================================================================

/**
 * Sends the comment form HTML via AJAX
 *
 * @since 5.0.0
 */

function fictioneer_ajax_get_comment_form() {
  // Enabled?
  if (
    ! get_option( 'fictioneer_enable_ajax_comment_form' ) &&
    ! get_option( 'fictioneer_enable_ajax_comments' )
  ) {
    wp_send_json_error( null, 403 );
  }

  // Validations
  if ( empty( $_GET['post_id'] ) || intval( $_GET['post_id'] ) < 1 ) {
    wp_send_json_error( array( 'error' => 'Missing or invalid ID. Comment form could not be loaded.' ) );
  }

  // Setup
  $post_id = absint( $_GET['post_id'] );
  $must_login = get_option( 'comment_registration' ) && ! is_user_logged_in();
  $nonce = wp_create_nonce( 'fictioneer_nonce' );
  $nonce_html = '<input id="fictioneer-ajax-nonce" name="fictioneer-ajax-nonce" type="hidden" value="' . $nonce . '">';

  // Get buffered form
  ob_start();

  if ( get_option( 'fictioneer_disable_comment_form' ) ) {
    comment_form( [], $post_id );
  } else {
    fictioneer_comment_form( fictioneer_comment_form_args( [], $post_id ), $post_id );
  }

  // Get buffer
  $output = ob_get_clean();

  // Send form
  wp_send_json_success(
    array( 'html' => $output, 'postId' => $post_id, 'mustLogin' => $must_login, 'nonceHtml' => $nonce_html )
  );
}

if ( get_option( 'fictioneer_enable_ajax_comment_form' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_get_comment_form', 'fictioneer_ajax_get_comment_form' );
  add_action( 'wp_ajax_nopriv_fictioneer_ajax_get_comment_form', 'fictioneer_ajax_get_comment_form' );
}

// =============================================================================
// REQUEST COMMENT SECTION - AJAX
// =============================================================================

/**
 * Sends the comment section HTML via AJAX
 *
 * @since 5.0.0
 */

function fictioneer_ajax_get_comment_section() {
  // Enabled?
  if ( ! get_option( 'fictioneer_enable_ajax_comments' ) ) {
    wp_send_json_error( null, 403 );
  }

  // Validations
  if ( ! isset( $_GET['post_id'] ) || intval( $_GET['post_id'] ) < 1 ) {
    wp_send_json_error( array( 'error' => 'Missing or invalid ID. Comments could not be loaded.' ) );
  }

  // Setup
  $post_id = absint( $_GET['post_id'] );
  $post = get_post( $post_id ); // Called later anyway; no performance loss
  $page = absint( $_GET['page'] ?? 1 ) ?: 1;
  $order = fictioneer_sanitize_query_var( $_GET['corder'] ?? 0, ['desc', 'asc'], get_option( 'comment_order' ) );
  $commentcode = ( $_GET['commentcode'] ?? 0 ) ?: false;
  $must_login = get_option( 'comment_registration' ) && ! is_user_logged_in();

  // Abort if post not found
  if ( empty( $post ) ) {
    wp_send_json_error( array( 'error' => 'Invalid ID. Comments could not be loaded.' ) );
  }

  // Abort if password required
  if ( post_password_required( $post ) ) {
    wp_send_json_error( array( 'error' => 'Password required. Comments could not be loaded.' ) );
  }

  // Abort if comments are closed
  if ( ! comments_open( $post ) ) {
    wp_send_json_error( array( 'error' => 'Comments are closed and could not be loaded.' ) );
  }

  // Query arguments
  $query_args = array( 'post_id' => $post_id );

  if ( ! get_option( 'fictioneer_disable_comment_query' ) ) {
    $query_args['type'] = ['comment', 'private', 'user_deleted'];
    $query_args['order'] = $order;
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
      fictioneer_comment_form( fictioneer_comment_form_args( [], $post_id ), $post_id );
    }
  } else {
    echo '<div class="fictioneer-comments__disabled">' . __( 'Commenting is disabled.', 'fictioneer' ) . '</div>';
  }

  // List
  fictioneer_ajax_list_comments(
    $comments,
    $page,
    array(
      'commentcode' => $commentcode,
      'post_author_id' => $post->post_author,
      'post_id' => $post_id,
      'order' => $order
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
              ?><button class="page-numbers dots" data-page-jump><?php echo $step; ?></button><?php
              break;
            default:
              ?><button class="page-numbers" data-page="<?php echo $step; ?>"><?php echo $step; ?></button><?php
          }
        }
      ?>
    </nav>
    <?php // <--- End HTML
  }

  // Get buffer
  $output = ob_get_clean();

  // Return buffer
  wp_send_json_success(
    array(
      'html' => $output,
      'postId' => $post_id,
      'page' => $page,
      'mustLogin' => $must_login,
      'disabled' => fictioneer_is_commenting_disabled( $post_id )
    )
  );
}

if ( get_option( 'fictioneer_enable_ajax_comments' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_get_comment_section', 'fictioneer_ajax_get_comment_section' );
  add_action( 'wp_ajax_nopriv_fictioneer_ajax_get_comment_section', 'fictioneer_ajax_get_comment_section' );
}

// =============================================================================
// COMMENT AJAX SUBMIT
// =============================================================================

/**
 * Creates and sends a new comment via AJAX
 *
 * @since 5.0.0
 * @since 5.20.3 - Use form field names as keys.
 */

function fictioneer_ajax_submit_comment() {
  // Enabled?
  if ( ! get_option( 'fictioneer_enable_ajax_comment_submit' ) ) {
    wp_send_json_error( null, 403 );
  }

  // Nonce plausible?
  if ( ! fictioneer_nonce_plausibility( $_REQUEST['nonce'] ?? 0 ) ) {
    wp_send_json_error(
      array(
        'failure' => __( 'The security token appears to be malformed. Please reload and try again, or contact an administrator if the problem persists.', 'fictioneer' ),
        'error' => sprintf( 'Malformed nonce: %s', esc_html( $_REQUEST['nonce'] ) )
      )
    );
  }

  // Nonce valid?
  if ( ! check_ajax_referer( 'fictioneer_nonce', 'nonce', false ) ) {
    wp_send_json_error(
      array(
        'failure' => __( 'Security token expired or invalid. Please reload and try again.', 'fictioneer' ),
        'error' => 'Invalid nonce.'
      )
    );
  }

  // Validations
  if ( intval( $_POST['comment_post_ID'] ?? 0 ) < 1 || ! isset( $_POST['content'] ) ) {
    wp_send_json_error( array( 'error' => 'Comment did not pass validation.' ) );
  }

  // Setup
  $user = wp_get_current_user();
  $post_id = absint( $_POST['comment_post_ID'] );
  $post = get_post( $post_id ); // Called later anyway; no performance loss
  $private_comment = filter_var( $_POST['fictioneer-private-comment-toggle'] ?? 0, FILTER_VALIDATE_BOOLEAN );
  $notification = filter_var( $_POST['fictioneer-comment-notification-toggle'] ?? 0, FILTER_VALIDATE_BOOLEAN );
  $privacy_consent = filter_var( $_POST['fictioneer-privacy-policy-consent'] ?? 0, FILTER_VALIDATE_BOOLEAN );
  $cookie_consent = filter_var( $_POST['wp-comment-cookies-consent'] ?? 0, FILTER_VALIDATE_BOOLEAN );
  $unfiltered_html = sanitize_text_field( $_POST['_wp_unfiltered_html_comment_disabled'] ?? '' );
  $depth = max( intval( $_POST['depth'] ?? 1 ), 1 );
  $commentcode = false;

  // Abort if post not found
  if ( ! $post ) {
    wp_send_json_error( array( 'error' => 'Invalid post ID.' ) );
  }

  // Check privacy consent early (not checked later for AJAX posts)
  if ( ! is_user_logged_in() && ! $privacy_consent && get_option( 'wp_page_for_privacy_policy' ) ) {
    wp_send_json_error( array( 'failure' => __( 'You did not accept the privacy policy.', 'fictioneer' ) ) );
  }

  // Abort if password required
  if ( post_password_required( $post ) ) {
    wp_send_json_error( array( 'failure' => __( 'Password required.', 'fictioneer' ) ) );
  }

  // Abort if comments are closed
  if ( ! comments_open( $post ) ) {
    wp_send_json_error( array( 'failure' => __( 'Comments are closed.', 'fictioneer' ) ) );
  }

  // Prepare arguments to create comment
  $comment_data = array(
    'comment_post_ID' => $post_id,
    'comment_type' => $private_comment ? 'private' : 'comment',
    'url' => '',
    'comment' => $_POST['content'],
    '_wp_unfiltered_html_comment' => $unfiltered_html,
    'cookie_consent' => $cookie_consent,
    'fictioneer-privacy-policy-consent' => $privacy_consent,
    'post_author_id' => $post->post_author,
    'notification' => $notification
  );

  // Optional arguments
  if ( isset( $_POST['email'] ) ) {
    $comment_data['email'] = sanitize_email( $_POST['email'] );
  }

  if ( isset( $_POST['author'] ) ) {
    $comment_data['author'] = sanitize_text_field( $_POST['author'] );
  }

  if ( intval( $_POST['comment_parent'] ?? 0 ) > 0 ) {
    $comment_data['comment_parent'] = absint( $_POST['comment_parent'] );
  }

  // Check against disallow list (Settings > Discussion) if not admin
  if ( ! fictioneer_is_admin( $user->ID ) ) {
    $offenders = fictioneer_check_comment_disallowed_list(
      $comment_data['author'] ?? '',
      $comment_data['email'] ?? '',
      '',
      $comment_data['comment'],
      $_SERVER['REMOTE_ADDR'] ?? '',
      $_SERVER['HTTP_USER_AGENT'] ?? ''
    );

    // Only show error for keys in content, no need to tell
    // someone his name or email address is blocked, etc.
    if ( FICTIONEER_DISALLOWED_KEY_NOTICE && $offenders[0] && $offenders[1] ) {
      wp_send_json_error(
        array( 'error' => __( 'Disallowed key found: "' . implode( ', ', $offenders[1] )  . '".', 'fictioneer' ) )
      );
    } elseif ( $offenders[0] ) {
      wp_send_json_error( array( 'failure' => __( 'Disallowed keys found.', 'fictioneer' ) ) );
    }
  }

  // Check parent (if any)
  if ( isset( $comment_data['comment_parent'] ) ) {
    $parent = get_comment( $comment_data['comment_parent'] ); // Called later anyway; no performance loss

    // Catch early (checked later again)
    if ( ! $parent->comment_approved ) {
      wp_send_json_error( array( 'failure' => __( 'Parent comment has not been approved yet.', 'fictioneer' ) ) );
    }

    // Catch early (checked later again)
    if ( get_comment_meta( $parent->comment_ID, 'fictioneer_thread_closed', true ) ) {
      wp_send_json_error( array( 'failure' => __( 'Comment thread is closed.', 'fictioneer' ) ) );
    }

    // Catch early (checked later again)
    if ( get_comment_meta( $parent->comment_ID, 'fictioneer_marked_offensive', true ) ) {
      wp_send_json_error( array( 'failure' => __( 'You cannot reply to comments marked as offensive.', 'fictioneer' ) ) );
    }
  }

  // Let WordPress handle the comment data...
  $comment = wp_handle_comment_submission( wp_unslash( $comment_data ) );

  if ( is_wp_error( $comment ) ) {
    wp_send_json_error( array( 'error' => $comment->get_error_message() ) );
  }

  // Mark as private if necessary
  if ( $private_comment ) {
    wp_update_comment( array( 'comment_ID' => $comment->comment_ID, 'comment_type' => 'private' ) );
    $comment = get_comment( $comment->comment_ID );
  }

  // Notification validator determines whether a subscription is active; change the validator
  // and all associated comment reply subscriptions are terminated
  $notification_validator = get_user_meta( get_current_user_id(), 'fictioneer_comment_reply_validator', true );

  if ( empty( $notification_validator ) ) {
    $notification_validator = time();
    fictioneer_update_user_meta( wp_get_current_user(), 'fictioneer_comment_reply_validator', $notification_validator );
  }

  /*
    Marking for notifications happens in fictioneer_comment_post()
  */

  // WordPress' comment cookie hook from wp-comments-post.php
  do_action( 'set_comment_cookies', $comment, wp_get_current_user(), $cookie_consent );

  // Prepare arguments to build HTML
  if ( ! $comment->comment_approved || get_option( 'fictioneer_enable_public_cache_compatibility' ) ) {
    $commentcode = wp_hash( $comment->comment_date_gmt );

    if (
      $commentcode &&
      FICTIONEER_COMMENTCODE_TTL > 0 &&
      time() > strtotime( $comment->comment_date_gmt ) + FICTIONEER_COMMENTCODE_TTL
    ) {
      $commentcode = false;
    }
  }

  $build_args = array(
    'style' => 'li',
    'avatar_size' => 32,
    'post_author_id' => $post->post_author,
    'max_depth' => get_option( 'thread_comments_depth' ),
    'new' => true
  );

  // Build HTML
  ob_start();
  fictioneer_theme_comment( $comment, $build_args, $depth );
  $html = ob_get_clean();
  $html = trim( $html );

  // Purge cache if necessary
  if ( fictioneer_caching_active( 'ajax_comment_submit' ) && ! get_option( 'fictioneer_enable_ajax_comments' ) ) {
    fictioneer_purge_post_cache( $post_id );
  }

  // Prepare arguments to return
  $output = array( 'comment' => $html, 'comment_id' => $comment->comment_ID );

  if ( $commentcode ) {
    $output['commentcode'] = $commentcode;
  }

  // Return comment and arguments
  wp_send_json_success( $output );
}

if ( get_option( 'fictioneer_enable_ajax_comment_submit' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_submit_comment', 'fictioneer_ajax_submit_comment' );
  add_action( 'wp_ajax_nopriv_fictioneer_ajax_submit_comment', 'fictioneer_ajax_submit_comment' );
}

// =============================================================================
// COMMENT INLINE EDIT SUBMIT - AJAX
// =============================================================================

/**
 * Edit comment via AJAX
 *
 * @since 5.0.0
 */

function fictioneer_ajax_edit_comment() {
  // Enabled?
  if ( ! get_option( 'fictioneer_enable_user_comment_editing' ) ) {
    wp_send_json_error( null, 403 );
  }

  // Setup
  $comment_id = isset( $_POST['comment_id'] ) ? fictioneer_validate_id( $_POST['comment_id'] ) : false;
  $user = fictioneer_get_validated_ajax_user();

  // Validations
  if ( ! $user || ! $comment_id || ! isset( $_POST['content'] ) ) {
    wp_send_json_error( array( 'error' => 'Request did not pass validation.' ) );
  }

  // Abort if comment editing capability disabled
  if ( get_user_meta( $user->ID, 'fictioneer_admin_disable_comment_editing', true ) ) {
    wp_send_json_error(
      array( 'failure' => __( 'Comment editing capability disabled.', 'fictioneer' ) )
    );
  }

  // Get comment from database
  $comment = get_comment( $comment_id, ARRAY_A );

  // Abort if comment not found
  if ( empty( $comment ) ) {
    wp_send_json_error( array( 'error' => 'Comment not found in database.' ) );
  }

  // Abort if sender is not comment author
  if ( $comment['user_id'] != $user->ID ) {
    wp_send_json_error( array( 'error' => 'Not the author of the comment.' ) );
  }

  // Abort if comment content is empty
  if ( empty( trim( $_POST['content'] ) ) ) {
    wp_send_json_error(
      array( 'failure' => __( 'Comment cannot be empty.', 'fictioneer' ) )
    );
  }

  // Abort if no changes were made
  if ( $comment['comment_content'] == $_POST['content'] ) {
    wp_send_json_error(); // No changes made, no error message
  }

  // Abort if comment is marked as offensive
  if ( get_comment_meta( $comment_id, 'fictioneer_marked_offensive', true ) ) {
    wp_send_json_error( array( 'failure' => __( 'Offensive comments cannot be edited.', 'fictioneer' ) ) );
  }

  // Abort if comment is closed (ancestors are not considered)
  if ( get_comment_meta( $comment_id, 'fictioneer_thread_closed', true ) ) {
    wp_send_json_error( array( 'failure' => __( 'Closed comments cannot be edited.', 'fictioneer' ) ) );
  }

  // Check if comment can (still) be edited...
  $timestamp = strtotime( "{$comment['comment_date_gmt']} GMT" );
  $edit_time = get_option( 'fictioneer_user_comment_edit_time', 15 );
  $edit_time = empty( $edit_time ) ? -1 : intval( $edit_time ) * 60; // Minutes to seconds
  $can_edit = $edit_time < 0 || time() < $edit_time + $timestamp;

  if ( ! $can_edit ) {
    wp_send_json_error( array( 'failure' => __( 'Editing time has expired.', 'fictioneer' ) ) );
  }

  // Check against disallow list (Settings > Discussion) if not admin
  if ( ! fictioneer_is_admin( $user->ID ) ) {
    $offenders = fictioneer_check_comment_disallowed_list( '', '', '', $_POST['content'], '', '' );

    // Only show error for keys in content, no need to tell
    // someone his name or email address is blocked, etc.
    if ( FICTIONEER_DISALLOWED_KEY_NOTICE && $offenders[0] && $offenders[1] ) {
      wp_send_json_error(
        array( 'error' => __( 'Disallowed key found: "' . implode( ', ', $offenders[1] )  . '".', 'fictioneer' ) )
      );
    } elseif ( $offenders[0] ) {
      wp_send_json_error( array( 'failure' => __( 'Disallowed keys found.', 'fictioneer' ) ) );
    }
  }

  // Update
  $edit_time = time();
  $comment['comment_content'] = $_POST['content'];

  if ( ! user_can( $user, 'unfiltered_html' ) ) {
    $comment['comment_content'] = sanitize_textarea_field( $comment['comment_content'] );
  }

  if ( wp_update_comment( $comment, true ) ) {
    // Get updated comment
    $updated_comment = get_comment( $comment_id );

    // Get formatted content of updated comment
    $updated_content = get_comment_text( $comment_id );

    // Send result
    wp_send_json_success(
      array(
        'comment_id' => $comment_id,
        'content' => apply_filters( 'comment_text', $updated_content, $updated_comment ),
        'raw' => $updated_comment->comment_content,
        'edited' => sprintf(
          _x( 'Last edited on %s.', 'Comment last edited by user on [datetime].', 'fictioneer' ),
          wp_date(
            sprintf(
              _x( '%1$s \a\t %2$s', 'Comment time format string.', 'fictioneer' ),
              get_option( 'fictioneer_subitem_date_format', "M j, 'y" ) ?: "M j, 'y",
              get_option( 'time_format' )
            ),
            $edit_time
          )
        )
      )
    );
  } else {
    // Something went wrong with the update (no details provided to frontend)
    wp_send_json_error( array( 'error' => 'Comment could not be updated.' ) );
  }
}

if ( get_option( 'fictioneer_enable_user_comment_editing' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_edit_comment', 'fictioneer_ajax_edit_comment' );
}

// =============================================================================
// DELETE MY COMMENT - AJAX
// =============================================================================

/**
 * Delete a user's comment on AJAX request
 *
 * @since 5.0.0
 */

function fictioneer_ajax_delete_my_comment() {
  // Enabled?
  if ( get_option( 'fictioneer_disable_comment_callback' ) ) {
    wp_send_json_error( null, 403 );
  }

  // Setup
  $comment_id = isset( $_POST['comment_id'] ) ? intval( $_POST['comment_id'] ) : false;
  $user = fictioneer_get_validated_ajax_user();

  // Validations
  if ( ! $user || ! $comment_id || $comment_id < 1 ) {
    wp_send_json_error( array( 'error' => 'Request did not pass validation.' ) );
  }

  // Find comment
  $comment = get_comment( $comment_id );

  if ( ! $comment ) {
    wp_send_json_error( array( 'error' => 'Comment not found in database.' ) );
  }

  // Match comment user with sender
  if ( $comment->user_id != $user->ID ) {
    wp_send_json_error( array( 'failure' => __( 'Permission denied. This is not your comment.', 'fictioneer' ) ) );
  }

  // Soft-delete comment
  $result = wp_update_comment(
    array(
      'user_ID' => 0,
      'comment_type' => 'user_deleted',
      'comment_author' => _x( 'Deleted', 'Deleted comment author name.', 'fictioneer' ),
      'comment_ID' => $comment->comment_ID,
      'comment_content' => __( 'Comment has been deleted by user.', 'fictioneer' ),
      'comment_author_email' => '',
      'comment_author_IP' => '',
      'comment_agent' => '',
      'comment_author_url' => ''
    )
  );

  // Response
  if ( ! $result ) {
    wp_send_json_error(
      array( 'failure' => __( 'Database error. Comment could not be deleted. Please try again later or contact an administrator.', 'fictioneer' ) )
    );
  } else {
    wp_send_json_success(
      array(
        'html' => '<div class="fictioneer-comment__hidden-notice">' . __( 'Comment has been deleted by user.', 'fictioneer' ) . '</div>'
      )
    );
  }
}

if ( ! get_option( 'fictioneer_disable_comment_callback' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_delete_my_comment', 'fictioneer_ajax_delete_my_comment' );
}
