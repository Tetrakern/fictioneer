<?php

// =============================================================================
// REQUEST COMMENT FORM - AJAX
// =============================================================================

if ( ! function_exists( 'fictioneer_ajax_get_comment_form' ) ) {
  /**
   * Sends the comment form HTML via AJAX
   *
   * @since Fictioneer 5.0
   * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
   * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
   */

  function fictioneer_ajax_get_comment_form() {
    // Nonce (die on failure)
    check_ajax_referer( 'fictioneer_nonce', 'nonce' );

    // Validations
    if ( empty( $_GET['post_id'] ) || intval( $_GET['post_id'] ) < 1 ) {
      wp_send_json_error( ['error' => __( 'Missing or invalid ID. Comment form could not be loaded.', 'fictioneer' )] );
    }

    // Setup
    $post_id = absint( $_GET['post_id'] );
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

    wp_send_json_success( ['html' => $output, 'postId' => $post_id, 'mustLogin' => $must_login] );
  }
}

if ( get_option( 'fictioneer_enable_ajax_comment_form' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_get_comment_form', 'fictioneer_ajax_get_comment_form' );
  add_action( 'wp_ajax_nopriv_fictioneer_ajax_get_comment_form', 'fictioneer_ajax_get_comment_form' );
}

// =============================================================================
// REQUEST COMMENT SECTION - AJAX
// =============================================================================

if ( ! function_exists( 'fictioneer_ajax_get_comment_section' ) ) {
  /**
   * Sends the comment section HTML via AJAX
   *
   * @since Fictioneer 5.0
   * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
   * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
   */

  function fictioneer_ajax_get_comment_section() {
    // Nonce (die on failure)
    check_ajax_referer( 'fictioneer_nonce', 'nonce' );

    // Validations
    if ( ! isset( $_GET['post_id'] ) || intval( $_GET['post_id'] ) < 1 ) {
      wp_send_json_error( ['error' => __( 'Missing or invalid ID. Comments could not be loaded.', 'fictioneer' )] );
    }

    // Setup
    $post_id = absint( $_GET['post_id'] );
    $post = get_post( $post_id ); // Called later anyway; no performance loss
    $page = isset( $_GET['page'] ) ? absint( $_GET['page'] ) : 1;
    $commentcode = isset( $_GET['commentcode'] ) ? $_GET['commentcode'] : false;
    $must_login = get_option( 'comment_registration' ) && ! is_user_logged_in();

    // Abort if post not found
    if ( ! $post ) {
      wp_send_json_error( ['error' => __( 'Invalid ID. Comments could not be loaded.', 'fictioneer' )] );
    }

    // Abort if password required
    if ( post_password_required( $post ) ) {
      wp_send_json_error( ['error' => __( 'Password required. Comments could not be loaded.', 'fictioneer' )] );
    }

    // Abort if comments are closed
    if ( ! comments_open( $post ) ) {
      wp_send_json_error( ['error' => __( 'Comments are closed and could not be loaded.', 'fictioneer' )] );
    }

    // Query arguments
    $query_args = ['post_id' => $post_id];

    if ( ! get_option( 'fictioneer_disable_comment_query' ) ) {
      $query_args['type'] = ['comment', 'private'];
      $query_args['order'] = get_option( 'comment_order' );
    } else {
      // Still hide private comments but do not limit the types preemptively
      $query_args = ['type__not_in' => 'private'];
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
                ?><span class="page-numbers current"><?php echo $step; ?></span><?php
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
    wp_send_json_success( ['html' => $output, 'postId' => $post_id, 'page' => $page, 'mustLogin' => $must_login] );
  }
}

if ( get_option( 'fictioneer_enable_ajax_comments' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_get_comment_section', 'fictioneer_ajax_get_comment_section' );
  add_action( 'wp_ajax_nopriv_fictioneer_ajax_get_comment_section', 'fictioneer_ajax_get_comment_section' );
}

// =============================================================================
// COMMENT AJAX SUBMIT
// =============================================================================

if ( ! function_exists( 'fictioneer_ajax_submit_comment' ) ) {
  /**
   * Creates and sends a new comment via AJAX
   *
   * @since Fictioneer 5.0
   * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
   * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
   */

  function fictioneer_ajax_submit_comment() {
    // Nonce
    check_ajax_referer( 'fictioneer_nonce', 'nonce' );

    // Validations
    if (
      ! isset( $_POST['post_id'] ) ||
      intval( $_POST['post_id'] ) < 1 ||
      ! isset( $_POST['content'] ) ||
      ! isset( $_POST['private_comment'] ) ||
      ! isset( $_POST['notification'] ) ||
      ! isset( $_POST['cookie_consent'] ) ||
      ! isset( $_POST['privacy_consent'] ) ||
      ! isset( $_POST['unfiltered_html'] ) ||
      ! isset( $_POST['depth'] )
    ) {
      wp_send_json_error( ['error' => __( 'Comment did not pass validation.', 'fictioneer' )] );
    }

    // Setup
    $user = wp_get_current_user();
    $post_id = absint( $_POST['post_id'] );
    $post = get_post( $post_id ); // Called later anyway; no performance loss
    $private_comment = filter_var( $_POST['private_comment'], FILTER_VALIDATE_BOOLEAN );
    $notification = filter_var( $_POST['notification'], FILTER_VALIDATE_BOOLEAN );
    $privacy_consent = filter_var( $_POST['privacy_consent'], FILTER_VALIDATE_BOOLEAN );
    $cookie_consent = filter_var( $_POST['cookie_consent'], FILTER_VALIDATE_BOOLEAN );
    $unfiltered_html = sanitize_text_field( $_POST['unfiltered_html'] );
    $depth = max( intval( $_POST['depth'] ), 1 );
    $commentcode = false;

    // Check privacy consent early (not checked later for AJAX posts)
    if ( ! is_user_logged_in() && ! $privacy_consent && get_option( 'wp_page_for_privacy_policy' ) ) {
      wp_send_json_error( ['error' => __( 'You did not accept the privacy policy.', 'fictioneer' )] );
    }

    // Abort if post not found
    if ( ! $post ) {
      wp_send_json_error( ['error' => __( 'Invalid ID.', 'fictioneer' )] );
    }

    // Abort if password required
    if ( post_password_required( $post ) ) {
      wp_send_json_error( ['error' => __( 'Password required.', 'fictioneer' )] );
    }

    // Abort if comments are closed
    if ( ! comments_open( $post ) ) {
      wp_send_json_error( ['error' => __( 'Comments are closed.', 'fictioneer' )] );
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
    if ( isset( $_POST['email'] ) ) $comment_data['email'] = sanitize_email( $_POST['email'] );
    if ( isset( $_POST['author'] ) ) $comment_data['author'] = sanitize_text_field( $_POST['author'] );
    if ( isset( $_POST['parent_id'] ) && intval( $_POST['parent_id'] ) > 0 ) {
      $comment_data['comment_parent'] = absint( $_POST['parent_id'] );
    }

    // Preemptively check for disallowed keys (Settings > Discussion)
    if ( FICTIONEER_DISALLOWED_KEY_NOTICE ) {
      $offenders = fictioneer_check_comment_disallowed_list(
        $comment_data['author'] ?? '',
        $comment_data['email'] ?? '',
        '',
        $comment_data['comment'],
        $_SERVER['REMOTE_ADDR'] ?? '',
        $_SERVER['HTTP_USER_AGENT'] ?? ''
      );

      // Only show error for keys in content, trash anything else as usual later.
      // No need to tell someone his name or email address is blocked, etc.
      if ( $offenders[0] && $offenders[1] && ! fictioneer_is_admin( $user->ID ) ) {
        wp_send_json_error( ['error' => __( 'Disallowed key found: "' . implode( ', ', $offenders[1] )  . '".', 'fictioneer' )] );
      }
    }

    // Check parent (if any)
    if ( isset( $comment_data['comment_parent'] ) ) {
      $parent = get_comment( $comment_data['comment_parent'] ); // Called later anyway; no performance loss

      // Catch early (checked later again)
      if ( ! $parent->comment_approved ) {
        wp_send_json_error( ['error' => __( 'Parent comment has not been approved yet.', 'fictioneer' )] );
      }

      // Catch early (checked later again)
      if ( get_comment_meta( $parent->comment_ID, 'fictioneer_thread_closed', true ) ) {
        wp_send_json_error( ['error' => __( 'Comment thread is closed.', 'fictioneer' )] );
      }

      // Catch early (checked later again)
      if ( get_comment_meta( $parent->comment_ID, 'fictioneer_marked_offensive', true ) ) {
        wp_send_json_error( ['error' => __( 'You cannot reply to comments marked as offensive.', 'fictioneer' )] );
      }
    }

    // Let WordPress handle the comment data...
    $comment = wp_handle_comment_submission( wp_unslash( $comment_data ) );

    if ( is_wp_error( $comment ) ) {
      wp_send_json_error( ['error' => $comment->get_error_message()] );
    }

    // Mark as private if necessary
    if ( $private_comment ) {
      wp_update_comment( ['comment_ID' => $comment->comment_ID, 'comment_type' => 'private'] );
      $comment = get_comment( $comment->comment_ID );
    }

    // Notification validator determines whether a subscription is active; change the validator
    // and all associated comment reply subscriptions are terminated
    $notification_validator = get_user_meta( get_current_user_id(), 'fictioneer_comment_reply_validator', true );

    if ( empty( $notification_validator ) ) {
      $notification_validator = time();
      update_user_meta( wp_get_current_user(), 'fictioneer_comment_reply_validator', $notification_validator );
    }

    // Mark for email notifications if set
    if ( $notification ) {
      add_comment_meta( $comment->comment_ID, 'fictioneer_send_notifications', $notification_validator );
      // Reset code to stop notifications (sufficiently unique for this purpose)
      add_comment_meta( $comment->comment_ID, 'fictioneer_notifications_reset', md5( wp_generate_password() ) );
    }

    // WordPress' comment cookie hook from wp-comments-post.php
    do_action( 'set_comment_cookies', $comment, wp_get_current_user(), $cookie_consent );

    // Prepare arguments to build HTML
    if ( ! $comment->comment_approved || get_option( 'fictioneer_enable_public_cache_compatibility' ) ) {
      $visibility_code = get_comment_meta( $comment->comment_ID, 'fictioneer_visibility_code', true );
      $commentcode = isset( $visibility_code['code'] ) ? $visibility_code['code'] : false;
    }

    $build_args = array(
      'style' => 'li',
      'avatar_size' => 32,
      'post_author_id' => $post->post_author,
      'max_depth' => get_option( 'thread_comments_depth' ),
      'ajax_in_progress' => true
    );

    // Build HTML
    ob_start();
    fictioneer_theme_comment( $comment, $build_args, $depth );
    $html = ob_get_clean();
    $html = trim( $html );

    // Purge cache if necessary
    if ( fictioneer_caching_active() && ! get_option( 'fictioneer_enable_ajax_comments' ) ) {
      fictioneer_purge_post_cache( $post_id );
    }

    // Prepare arguments to return
    $output = ['comment' => $html, 'comment_id' => $comment->comment_ID];
    if ( $commentcode ) $output['commentcode'] = $commentcode;

    // Return comment and arguments
    wp_send_json_success( $output );
  }
}

if ( get_option( 'fictioneer_enable_ajax_comment_submit' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_submit_comment', 'fictioneer_ajax_submit_comment' );
  add_action( 'wp_ajax_nopriv_fictioneer_ajax_submit_comment', 'fictioneer_ajax_submit_comment' );
}

// =============================================================================
// COMMENT INLINE EDIT SUBMIT - AJAX
// =============================================================================

if ( ! function_exists( 'fictioneer_ajax_edit_comment' ) ) {
  /**
   * Edit comment via AJAX
   *
   * @since Fictioneer 5.0
   * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
   * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
   */

  function fictioneer_ajax_edit_comment() {
    // Setup
    $comment_id = isset( $_POST['comment_id'] ) ? fictioneer_validate_id( $_POST['comment_id'] ) : false;
    $user = fictioneer_get_validated_ajax_user();

    // Validations
    if ( ! $user || ! $comment_id || ! isset( $_POST['content'] ) ) {
      wp_send_json_error( ['error' => __( 'Request did not pass validation.', 'fictioneer' )] );
    }

    // Abort if comment editing capability disabled
    if ( get_user_meta( $user->ID, 'fictioneer_admin_disable_editing', true ) ) {
      wp_send_json_error( ['error' => __( 'Comment editing capability disabled.', 'fictioneer' )] );
    }

    // Get comment from database
    $comment = get_comment( $comment_id, ARRAY_A );

    // Abort if comment not found
    if ( empty( $comment ) ) {
      wp_send_json_error( ['error' => __( 'Comment not found in database.', 'fictioneer' )] );
    }

    // Abort if sender is not comment author
    if ( $comment['user_id'] != $user->ID ) {
      wp_send_json_error( ['error' => __( 'Not the author of the comment.', 'fictioneer' )] );
    }

    // Abort if comment content is empty
    if ( empty( trim( $_POST['content'] ) ) ) {
      wp_send_json_error( ['error' => __( 'Comment cannot be empty.', 'fictioneer' )] );
    }

    // Abort if no changes were made
    if ( $comment['comment_content'] == $_POST['content'] ) {
      wp_send_json_error(); // No changes made, no error message
    }

    // Abort if comment is marked as offensive
    if ( get_comment_meta( $comment_id, 'fictioneer_marked_offensive', true ) ) {
      wp_send_json_error( ['error' => __( 'Offensive comments cannot be edited.', 'fictioneer' )] );
    }

    // Abort if comment is closed (ancestors are not considered)
    if ( get_comment_meta( $comment_id, 'fictioneer_thread_closed', true ) ) {
      wp_send_json_error( ['error' => __( 'Closed comments cannot be edited.', 'fictioneer' )] );
    }

    // Check if comment can (still) be edited...
    $timestamp = strtotime( "{$comment['comment_date_gmt']} GMT" );
    $edit_time = get_option( 'fictioneer_user_comment_edit_time', 15 );
    $edit_time = empty( $edit_time ) ? -1 : (int) $edit_time * 60; // Minutes to seconds
    $can_edit = $edit_time < 0 || time() < $edit_time + $timestamp;

    if ( ! $can_edit ) {
      wp_send_json_error( ['error' => __( 'Editing time has expired.', 'fictioneer' )] );
    }

    // Update
    $old_content = $comment['comment_content'];
    $comment['comment_content'] = $_POST['content'];
    $edit_time = time();

    if ( wp_update_comment( $comment, true ) ) {
      // Remember edit time
      if ( $comment['user_id'] == $user->ID ) {
        $edit_stack = get_comment_meta( $comment_id, 'fictioneer_user_edit_stack', true );
        $edit_stack = is_array( $edit_stack ) ? $edit_stack : [];
        $edit_stack[] = ['timestamp' => $edit_time, 'previous_content' => $old_content];
        update_comment_meta( $comment_id, 'fictioneer_user_edit_stack', $edit_stack );
      }

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
      wp_send_json_error( ['error' => __( 'Comment could not be updated.', 'fictioneer' )] );
    }
  }
}

if ( get_option( 'fictioneer_enable_user_comment_editing' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_edit_comment', 'fictioneer_ajax_edit_comment' );
}

// =============================================================================
// REQUEST COMMENT FORM - AJAX
// =============================================================================

if ( ! function_exists( 'fictioneer_ajax_delete_my_comment' ) ) {
  /**
   * Delete a user's comment on AJAX request
   *
   * @since Fictioneer 5.0
   * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
   * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
   */

  function fictioneer_ajax_delete_my_comment() {
    // Setup
    $comment_id = isset( $_POST['comment_id'] ) ? intval( $_POST['comment_id'] ) : false;
    $user = fictioneer_get_validated_ajax_user();

    // Validations
    if ( ! $user || ! $comment_id || $comment_id < 1 ) {
      wp_send_json_error( ['error' => __( 'Request did not pass validation.', 'fictioneer' )] );
    }

    // Find comment
    $comment = get_comment( $comment_id );

    if ( ! $comment ) {
      wp_send_json_error( ['error' => __( 'Database error. Comment not found.', 'fictioneer' )] );
    }

    // Match comment user with sender
    if ( $comment->user_id != $user->ID ) {
      wp_send_json_error( ['error' => __( 'Permission denied. This is not your comment.', 'fictioneer' )] );
    }

    // Soft-delete comment
    update_comment_meta( $comment->comment_ID, 'fictioneer_deleted_by_user', true );
    $result = wp_update_comment(
      array(
        'user_ID' => 0,
        'comment_author' => __( 'Deleted', 'fictioneer' ),
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
      wp_send_json_error( ['error' => __( 'Database error. Comment could not be deleted. Please try again later or contact an administrator.', 'fictioneer' )] );
    } else {
      wp_send_json_success(
        array(
          'html' => '<div class="fictioneer-comment__hidden-notice">' . __( 'Comment has been deleted by user.', 'fictioneer' ) . '</div>'
        )
      );
    }
  }
}

if ( ! get_option( 'fictioneer_disable_comment_callback' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_delete_my_comment', 'fictioneer_ajax_delete_my_comment' );
}

?>
