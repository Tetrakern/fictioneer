<?php

// =============================================================================
// LOG IN TO COMMENT
// =============================================================================

/**
 * Change default comment reply login link to login modal toggle
 *
 * @since Fictioneer 5.0
 *
 * @param string     $link    The HTML markup for the comment reply link.
 * @param array      $args    An array of arguments overriding the defaults.
 * @param WP_Comment $comment The object of the comment being replied.
 * @param WP_Post    $post    The WP_Post object.
 *
 * @return string Reply link.
 */

function fictioneer_comment_login_to_reply( $link, $args, $comment, $post ) {
  // Return default if OAuth authentication is disabled
  if ( ! get_option( 'fictioneer_enable_oauth' ) ) return $link;

  // Reply link or login modal toggle
  if ( get_option( 'comment_registration' ) && ! is_user_logged_in() ) {
    return $args['before'] . '<label for="modal-login-toggle" class="fictioneer-comment__login-to-reply">' . $args['login_text'] . '</label>' . $args['after'];
  } else {
    return $link;
  }
}
add_filter( 'comment_reply_link', 'fictioneer_comment_login_to_reply', 10, 4 );

// =============================================================================
// SHIFT STICKY COMMENTS
// =============================================================================

if ( ! function_exists( 'fictioneer_shift_sticky_comments' ) ) {
  /**
   * Shift sticky comments to the top
   *
   * So the 'orderby' by 'meta_key' query arguments do not work for some reason.
   * You want to do this the hard way? Fine, let's do this the hard way!
   *
   * @since Fictioneer 5.0
   *
   * @param array $comments Queried comments.
   *
   * @return array Comments with sticky ones shifted to the top.
   */

  function fictioneer_shift_sticky_comments( $comments ) {
    // Setup
    $sticky = [];

    // Loop through all f*cking comments like a madman!
    foreach ( $comments as $key => $comment ) {
      if (
        ! get_comment_meta( $comment->comment_ID, 'fictioneer_sticky', true ) ||
        $comment->comment_parent ||
        get_comment_meta( $comment->comment_ID, 'fictioneer_marked_offensive', true ) ||
        get_comment_meta( $comment->comment_ID, 'fictioneer_deleted_by_user', true )
      ) continue;

      $sticky[] = $comment;
      unset( $comments[ $key ] );
    }

    // Result
    return array_merge( $sticky, $comments );
  }
}

if ( get_option( 'fictioneer_enable_sticky_comments' ) ) {
  add_filter( 'fictioneer_filter_comments', 'fictioneer_shift_sticky_comments' );
}

// =============================================================================
// COMMENT HEADER
// =============================================================================

if ( ! function_exists( 'fictioneer_comment_header' ) ) {
  /**
   * Output Fictioneer themed comment header
   *
   * @since Fictioneer 5.0
   *
   * @param int $comment_count Total number of comments.
   */

  function fictioneer_comment_header( $comment_count ) {
    // Setup
    $notice = trim( get_option( 'fictioneer_comments_notice' ) );

    // Start HTML ---> ?>
    <div class="fictioneer-comments__header">
      <h2 class="fictioneer-comments__title">
        <?php echo
          sprintf(
            __( '<span class="fictioneer-comments__total">%1$s</span> <span>%2$s</span>' ),
            $comment_count,
            _n( 'Comment', 'Comments', $comment_count, 'fictioneer' )
          )
        ?>
      </h2>
      <?php if ( ! empty( $notice ) ) : ?>
        <div class="fictioneer-comments__notice"><?php echo force_balance_tags( $notice ); ?></div>
      <?php endif; ?>
    </div>
    <?php // <--- End HTML
  }
}

// =============================================================================
// COMMENT AJAX SKELETON
// =============================================================================

if ( ! function_exists( 'fictioneer_comments_ajax_skeleton' ) ) {
  /**
   * Output skeleton placeholder while comments are loaded via AJAX
   *
   * @since Fictioneer 5.0
   *
   * @param int $comments_count Total number of comments.
   */

  function fictioneer_comments_ajax_skeleton( $comments_count ) {
    // Start HTML ---> ?>
    <div class="comments-skeleton">
      <div class="fictioneer-comments__header">
        <h2 class="fictioneer-comments__title"><div class="shape"></div></h2>
      </div>
      <?php if ( fictioneer_is_commenting_disabled() ) : ?>
        <div class="fictioneer-comments__disabled"><?php _e( 'Comments are disabled.', 'fictioneer' ); ?></div>
      <?php else : ?>
        <?php if ( get_option( 'fictioneer_enable_oauth' ) || is_user_logged_in() ) : ?>
          <div class="comments-skeleton__login"><div class="shape"></div></div>
        <?php endif; ?>
        <div class="shape comments-skeleton__response"></div>
      <?php endif; ?>
      <div class="comments-skeleton__list">
        <?php for ( $i = 1; $i <= $comments_count; $i++ ) : ?>
          <div class="shape"><div class="cutout"></div></div>
          <?php if ( $i > 2 ) break; ?>
        <?php endfor; ?>
      </div>
    </div>
    <?php // <--- End HTML
  }
}

// =============================================================================
// COMMENT AJAX SKELETON
// =============================================================================

if ( ! function_exists( 'fictioneer_comments_ajax_form_skeleton' ) ) {
  /**
   * Output skeleton placeholder while the form is loaded via AJAX
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_comments_ajax_form_skeleton() {
    // Start HTML ---> ?>
    <div id="ajax-comment-form-target" class="comments-skeleton">
      <?php if ( get_option( 'fictioneer_enable_oauth' ) || is_user_logged_in() ) : ?>
        <div class="comments-skeleton__login"><div class="shape"></div></div>
      <?php endif; ?>
      <div class="shape comments-skeleton__response"></div>
    </div>
    <?php // <--- End HTML
  }
}

// =============================================================================
// COMMENTS THREADS
// =============================================================================

if ( ! function_exists( 'fictioneer_ajax_list_comments' ) ) {
  /**
   * Output Fictioneer themed paginated comment threads via AJAX
   *
   * @since Fictioneer 5.0
   *
   * @param array $comments Collection of comments to display.
   * @param int   $page     Current page number of the comments.
   * @param array $args     With 'commentcode', 'post_author_id', and 'post_id'.
   */

  function fictioneer_ajax_list_comments( $comments, $page, $args = [] ) {
    // Abort conditions
    if ( count( $comments ) < 1) return;

    $list_args = array(
      'avatar_size' => 32,
      'style' => 'ol',
      'type' => 'all',
      'per_page' => get_option( 'comments_per_page' ),
      'max_depth' => get_option( 'thread_comments_depth' ),
      'commentcode' => isset( $args['commentcode'] ) ? $args['commentcode'] : false,
      'post_author_id' => isset( $args['post_author_id'] ) ? $args['post_author_id'] : 0
    );

    if ( ! get_option( 'fictioneer_disable_comment_query' ) ) {
      $list_args['page'] = $page;
      $list_args['reverse_top_level'] = false;
      $list_args['reverse_children'] = true;
    }

    if ( ! get_option( 'fictioneer_disable_comment_callback' ) ) {
      $list_args['callback'] = 'fictioneer_theme_comment';
    }

    // Apply filters
    $list_args = apply_filters( 'fictioneer_filter_comment_list_args', $list_args );

    // Start HTML ---> ?>
    <?php if ( fictioneer_is_commenting_disabled( $args['post_id'] ) ) : ?>
      <div class="fictioneer-comments__disabled"><?php _e( 'Comments are disabled.', 'fictioneer' ); ?></div>
    <?php endif; ?>
    <ol class="fictioneer-comments__list commentlist">
      <?php wp_list_comments( $list_args, $comments ); ?>
    </ol>
    <?php // <--- End HTML
  }
}

/**
 * Returns themed comment list arguments
 *
 * @since Fictioneer 5.0
 *
 * @param array $parsed_args Default list arguments.
 *
 * @return array Modified arguments.
 */

function fictioneer_comment_list_args( $parsed_args ) {
  // Setup
  $page = get_query_var( 'cpage', 1 );

  // Build arguments
  $list_args = array(
    'avatar_size' => 32,
    'style' => 'ol',
    'type' => 'all',
    'per_page' => get_option( 'comments_per_page' ),
    'max_depth' => get_option( 'thread_comments_depth' ),
    'commentcode' => false,
    'post_author_id' => 0
  );

  if ( ! get_option( 'fictioneer_disable_comment_query' ) ) {
    $list_args['page'] = $page;
    $list_args['reverse_top_level'] = false;
    $list_args['reverse_children'] = true;
  }

  if ( ! get_option( 'fictioneer_disable_comment_callback' ) ) {
    $list_args['callback'] = 'fictioneer_theme_comment';
  }

  // Apply filters
  $list_args = apply_filters( 'fictioneer_filter_comment_list_args', $list_args );

  // Return args merged into default arguments
  return array_merge( $parsed_args, $list_args );
}

if ( ! get_option( 'fictioneer_enable_ajax_comments' ) ) {
  add_filter( 'wp_list_comments_args', 'fictioneer_comment_list_args' );
}

// =============================================================================
// GET COMMENT BADGE
// =============================================================================

if ( ! function_exists( 'fictioneer_get_comment_badge' ) ) {
  /**
   * Get HTML for comment badge
   *
   * @since Fictioneer 5.0
   *
   * @param WP_User    $user           The current user object.
   * @param WP_Comment $comment        The comment object.
   * @param int        $post_author_id ID of the author of the post the comment is for.
   *
   * @return string Badge HTML or empty string.
   */

  function fictioneer_get_comment_badge( $user, $comment, $post_author_id ) {
    // Abort conditions...
    if ( $user && get_the_author_meta( 'fictioneer_hide_badge', $comment->user_id ) ) return '';

    // Setup
    $comment_user = get_user_by( 'id', $comment->user_id );
    $is_post_author = $comment->user_id == $post_author_id;
    $is_moderator = fictioneer_is_moderator( $comment->user_id );
    $is_admin = fictioneer_is_admin( $comment->user_id );
    $badge_body = '<div class="fictioneer-comment__badge %1$s">%2$s</div>';
    $badge_class = '';
    $badge = '';

    // Role badge
    if ( $is_post_author ) {
      $badge = fcntr( 'author' );
      $badge_class = 'is-author';
    } elseif ( $is_admin ) {
      $badge = fcntr( 'admin' );
      $badge_class = 'is-admin';
    } elseif ( $is_moderator ) {
      $badge = fcntr( 'moderator' );
      $badge_class = 'is-moderator';
    }

    // Patreon badge (if no higher badge set yet)
    if ( empty( $badge ) ) {
      $badge = fictioneer_get_patreon_badge( $comment_user );
      $badge_class = 'is-supporter';
    }

    // Custom badge (can override all)
    if (
      get_option( 'fictioneer_enable_custom_badges' ) &&
      ! get_the_author_meta( 'fictioneer_disable_badge_override', $comment->user_id )
    ) {
      $custom_badge = fictioneer_get_override_badge( $comment_user );

      if ( $custom_badge ) {
        $badge = $custom_badge;
        $badge_class = 'badge-override';
      }
    }

    // Apply filters
    $output = empty( $badge ) ? '' : sprintf( $badge_body, $badge_class, $badge );
    $output = apply_filters( 'fictioneer_filter_comment_badge', $output, $badge, $badge_class );

    // Return badge or empty sting
    return $output;
  }
}

// =============================================================================
// THEME COMMENT TEXT
// =============================================================================

if ( ! function_exists( 'fictioneer_replace_comment_line_breaks' ) ) {
  /**
   * Properly wrap empty lines into paragraphs
   *
   * @since Fictioneer 4.0
   * @since Fictioneer 5.2.6 Updated and changed to not mess up some languages.
   *
   * @param string $comment_content The comment content.
   *
   * @return string $comment_content The modified comment content.
   */

  function fictioneer_replace_comment_line_breaks( $comment_content ) {
    $lines = preg_split( '/\R/', $comment_content );

    $wrapped = array_map( function( $line ) {
      if ( trim( $line ) === '' ) {
        return '<p></p>';
      }
      return $line;
    }, $lines );

    return implode( PHP_EOL, $wrapped );
  }
}

if ( ! get_option( 'fictioneer_disable_comment_callback' ) ) {
  add_filter( 'get_comment_text', 'fictioneer_replace_comment_line_breaks' );
  remove_filter( 'comment_text', 'make_clickable', 9 ); // No auto-linking of plain URLs
}

// =============================================================================
// THEME COMMENT CALLBACK
// =============================================================================

if ( ! function_exists( 'fictioneer_theme_comment' ) ) {
  /**
   * Callback function for basic theme comment markup
   *
   * @since Fictioneer 4.7
   *
   * @global WP_Post|null $post    The post object. May not be available.
   * @param  WP_Comment   $comment The comment object.
   * @param  array        $args    List of arguments.
   * @param  int          $depth   Depth.
   */

  function fictioneer_theme_comment( $comment, $args, $depth ) {
    // Global not available in AJAX request!
    global $post;

    // Setup
    $current_user = wp_get_current_user(); // Viewer, not necessarily commenter!
    $comment_user_id = $comment->user_id; // User ID of comment author or 0
    $post_author_id = $post->post_author; // Of the post the comment is for
    $comment_author = empty( $comment->comment_author ) ? fcntr( 'anonymous_guest' ) : $comment->comment_author;
    $unapproved_comment_email = wp_get_unapproved_comment_author_email();
    $commentcode = isset( $_GET['commentcode'] ) ? sanitize_text_field( $_GET['commentcode'] ) : false;
    $fingerprint = $comment->user_id ? fictioneer_get_user_fingerprint( $comment->user_id ) : false;
    $timestamp = strtotime( "{$comment->comment_date_gmt} GMT" );
    $edit_stack = get_comment_meta( $comment->comment_ID, 'fictioneer_user_edit_stack', true );
    $parent_comment = $comment->comment_parent ? get_comment( absint( $comment->comment_parent ) ) : false;
    $reports = get_comment_meta( $comment->comment_ID, 'fictioneer_user_reports', true );
    $visibility_code = get_comment_meta( $comment->comment_ID, 'fictioneer_visibility_code', true );
    $can_comment = ! fictioneer_is_commenting_disabled( $post->ID );
    $tag = $args['style'] === 'div' ? 'div' : 'li';
    $classes = ['fictioneer-comment'];
    $flag_classes = '';

    // Handle AJAX
    $is_ajax = get_option( 'fictioneer_enable_ajax_comments' );
    $ajax_in_progress = isset( $args['ajax_in_progress'] ) && $args['ajax_in_progress'];

    if ( $is_ajax || $ajax_in_progress ) {
      $post_author_id = isset( $args['post_author_id'] ) ? absint( $args['post_author_id'] ) : 0;
      $commentcode = isset( $args['commentcode'] ) ? $args['commentcode'] : false;
    }

    // Badge
    $badge = fictioneer_get_comment_badge( $current_user, $comment, $post_author_id );

    // Flags
    $is_caching = fictioneer_caching_active();
    $is_private_caching = fictioneer_private_caching_active();
    $is_owner = ! empty( $comment_user_id ) && $comment_user_id == $current_user->ID;
    $is_approved = $comment->comment_approved;
    $is_offensive = get_comment_meta( $comment->comment_ID, 'fictioneer_marked_offensive', true );
    $is_closed = get_comment_meta( $comment->comment_ID, 'fictioneer_thread_closed', true );
    $is_deleted_by_owner = get_comment_meta( $comment->comment_ID, 'fictioneer_deleted_by_user', true );
    $is_ignoring_reports = get_comment_meta( $comment->comment_ID, 'fictioneer_ignore_reports', true );
    $is_reportable = ! get_the_author_meta( 'fictioneer_admin_disable_reporting', $current_user->ID ) && get_option( 'fictioneer_enable_comment_reporting' );
    $is_child_of_private = false;
    $is_report_hidden = false;
    $is_flagged_by_current_user = false;
    $is_sticky = get_option( 'fictioneer_enable_sticky_comments' ) && get_comment_meta( $comment->comment_ID, 'fictioneer_sticky', true ) && ! $parent_comment && ! $is_deleted_by_owner && ! $is_offensive;
    $is_hidden = ! is_user_logged_in() ||
                 (
                   ! fictioneer_is_admin( $current_user->ID ) &&
                   ! fictioneer_is_moderator( $current_user->ID ) &&
                   $comment->user_id != $current_user->ID &&
                   $post_author_id != $current_user->ID &&
                   ! ($parent_comment && $parent_comment->user_id == $current_user->ID)
                 );
    $is_editable = get_option( 'fictioneer_enable_user_comment_editing' ) &&
                   $is_owner &&
                   ! $is_offensive &&
                   ! $is_closed;

    // Check reports
    if ( get_option( 'fictioneer_enable_comment_reporting' ) ) {
      $is_report_hidden = empty( $reports ) ? false : count( $reports ) >= get_option( 'fictioneer_comment_report_threshold', 10 );
      $is_flagged_by_current_user = ( $current_user && ! empty( $reports ) ) ? array_key_exists( $current_user->ID, $reports ) : false;
    }

    // No individual flags for cached comments unless loaded via AJAX or caches are per user
    if ( ! $is_ajax && $is_caching && ! $is_private_caching ) {
      $is_flagged_by_current_user = false;
      $flag_classes = '_dubious';
    }

    // Ancestors?
    $ancestor = $parent_comment;
    $closed_ancestor = false;

    while ( $ancestor ) {
      if ( ! is_object( $ancestor ) ) break;

      // Private ancestor?
      if ( get_comment_type( $ancestor ) === 'private' ) {
        $is_child_of_private = true;
        $classes[] = '_has-private-ancestor';
      }

      // Ancestor closed?
      if ( ! $is_closed && get_comment_meta( $ancestor->comment_ID, 'fictioneer_thread_closed', true ) ) {
        $closed_ancestor = true;
        $classes[] = '_has-closed-ancestor';
      }

      // We can stop if...
      if ( $is_child_of_private && $closed_ancestor ) break;

      // Next ancestor if any
      $ancestor = $ancestor->comment_parent ? get_comment( $ancestor->comment_parent ) : false;
    }

    // Additional classes
    if ( ! empty( $args['has_children'] ) ) $classes[] = 'parent';
    if ( ! $is_approved ) $classes[] = '_unapproved';
    if ( $is_offensive ) $classes[] = '_offensive';
    if ( $is_report_hidden && ! $is_ignoring_reports ) $classes[] = 'hidden-by-reports';
    if ( $is_closed ) $classes[] = '_closed';
    if ( $is_sticky ) $classes[] = '_sticky';
    if ( $is_deleted_by_owner ) $classes[] = '_deleted';

    /**
     * Show or hide comment if not approved, no moderation capabilities, no email has been set
     * or it doesn't match the cookie (if any), and the comment code doesn't match either.
    */

    // Commentcode will be valid for n seconds (default: 600) after posting (-1 for infinite)
    if ( $commentcode && is_array( $visibility_code ) ) {
      $commentcode = $visibility_code['code'] == $commentcode;

      if ( FICTIONEER_COMMENTCODE_TTL > 0 ) {
        $commentcode = $commentcode && $visibility_code['timestamp'] + FICTIONEER_COMMENTCODE_TTL >= time();
      }
    }

    if (
      ! $is_approved &&
      ! $is_report_hidden &&
      ! $commentcode &&
      ! current_user_can( 'moderate_comments' ) &&
      ( $unapproved_comment_email != $comment->comment_author_email || empty( $unapproved_comment_email ) ) &&
      ! $ajax_in_progress
    ) {
      return;
    }

    // Build HTML (tag closed by WordPress!)
    $comment_class = comment_class( $classes, $comment, $comment->comment_post_ID, false );
    $fingerprint_data = empty( $fingerprint ) ? '' : "data-fingerprint=\"$fingerprint\"";
    if ( $ajax_in_progress ) $comment_class = str_replace( 'depth-1', "depth-$depth", $comment_class );
    $timestamp_ms = $timestamp * 1000;
    $open = "<$tag id=\"comment-{$comment->comment_ID}\" {$comment_class} data-id=\"{$comment->comment_ID}\" data-depth=\"$depth\" data-timestamp=\"$timestamp_ms\" $fingerprint_data>";
    $open .= "<div id=\"div-comment-{$comment->comment_ID}\" class=\"fictioneer-comment__container\">";
    if ( $is_sticky ) $open .= fictioneer_get_icon( 'sticky-pin' );

    // Hidden private comments
    if ( $comment->comment_type === 'private' && $is_hidden && ! $commentcode && ! $ajax_in_progress && ! $is_child_of_private ) {
      // Start HTML ---> ?>
      <?php echo $open; ?>
        <div class="fictioneer-comment__hidden-notice"><?php _e( 'Comment has been marked as private.', 'fictioneer' ) ?></div>
      </div>
      <?php // <--- End HTML
      return;
    }

    // Hidden child of a private comment
    if ( $is_child_of_private && $is_hidden ) {
      // Start HTML ---> ?>
      <?php echo $open; ?>
        <div class="fictioneer-comment__hidden-notice"><?php _e( 'Reply has been marked as private.', 'fictioneer' ) ?></div>
      </div>
      <?php // <--- End HTML
      return;
    }

    // Hidden because marked as offensive
    if ( $is_offensive && $is_hidden ) {
      // Start HTML ---> ?>
      <?php echo $open; ?>
        <div class="fictioneer-comment__hidden-notice"><?php _e( 'Comment has been marked as offensive.', 'fictioneer' ) ?></div>
      </div>
      <?php // <--- End HTML
      return;
    }

    // Hidden by reports
    if ( $is_report_hidden && ! $is_ignoring_reports && $is_hidden ) {
      // Start HTML ---> ?>
      <?php echo $open; ?>
        <div class="fictioneer-comment__hidden-notice"><?php _e( 'Comment is hidden due to negative reports.', 'fictioneer' ) ?></div>
      </div>
      <?php // <--- End HTML
      return;
    }

    // Deleted by owner
    if ( $is_deleted_by_owner ) {
      // Start HTML ---> ?>
      <?php echo $open; ?>
        <div class="fictioneer-comment__footer">
          <div class="fictioneer-comment__footer-left fictioneer-comment__hidden-notice">
            <?php _e( 'Comment has been deleted by user.', 'fictioneer' ) ?>
          </div>
          <div class="fictioneer-comment__footer-right hide-unless-hover-on-desktop">
            <?php fictioneer_comment_mod_menu( $comment ); ?>
          </div>
        </div>
      </div>
      <?php // <--- End HTML
      return;
    }

    // Check if comment can (still) be edited
    $edit_time = get_option( 'fictioneer_user_comment_edit_time', 15 );
    $edit_time = empty( $edit_time ) ? -1 : (int) $edit_time * 60; // Minutes to seconds
    $can_edit = $edit_time < 0 || time() < $edit_time + $timestamp;

    // Start visible comment HTML ---> ?>
    <?php echo $open; ?>

      <div class="fictioneer-comment__header">
        <?php echo ( $avatar = get_avatar( $comment->user_id , $args['avatar_size'] ) ) && $args['avatar_size'] != 0 ? $avatar : ''; ?>
        <div class="fictioneer-comment__meta">
          <div class="fictioneer-comment__author truncate _1-1"><?php
            if ( fictioneer_is_author( $comment->user_id ) ) {
              ?><a href="<?php echo get_author_posts_url( $comment->user_id ) ?>"><?php echo $comment->comment_author; ?></a><?php
            } else {
              ?><span><?php echo $comment_author; ?></span><?php
            }

            echo $badge;
          ?></div>
          <div class="fictioneer-comment__info truncate _1-1">
            <?php if ( $parent_comment ) : ?>
              <span class="fictioneer-comment__reply-to"><?php
                printf(
                  _x( '@%s', '@mention in comment replies.', 'fictioneer' ),
                  empty( $parent_comment->comment_author ) ? fcntr( 'anonymous_guest' ) : $parent_comment->comment_author
                );
              ?></span> &bull;
            <?php endif; ?>
            <span class="fictioneer-comment__date"><?php echo
              date_format(
                date_create( $comment->comment_date ),
                sprintf(
                  _x( '%1$s \a\t %2$s', 'Comment time format string.', 'fictioneer' ),
                  get_option( 'fictioneer_subitem_date_format', "M j, 'y" ) ?: "M j, 'y",
                  get_option( 'time_format' )
                )
              );
            ?></span>
          </div>
        </div>
      </div>

      <div class="fictioneer-comment__body clearfix">
        <?php
        // =============================================================================
        // COMMENT CONTENT
        // =============================================================================
        ?>
        <div class="fictioneer-comment__content"><?php comment_text( $comment ); ?></div>
        <?php
        // =============================================================================
        // COMMENT EDIT
        // =============================================================================
        ?>
        <?php if ( ( $can_edit && $is_editable ) || ( $is_caching && ! $is_deleted_by_owner) ) : ?>
          <div class="fictioneer-comment__edit" hidden>
            <textarea class="comment-inline-edit-content"><?php echo $comment->comment_content; ?></textarea>
          </div>
        <?php endif; ?>
        <?php
        // =============================================================================
        // EDIT NOTE
        // =============================================================================
        ?>
        <?php if ( ! empty( $edit_stack ) && ! $is_deleted_by_owner ) : ?>
          <div class="fictioneer-comment__edit-note"><?php
            printf(
              _x( 'Last edited on %s.', 'Comment last edited by user on [datetime].', 'fictioneer' ),
              wp_date(
                sprintf(
                  _x( '%1$s \a\t %2$s', 'Comment time format string.', 'fictioneer' ),
                  get_option( 'fictioneer_subitem_date_format', "M j, 'y" ) ?: "M j, 'y",
                  get_option( 'time_format' )
                ),
                $edit_stack[count( $edit_stack ) - 1]['timestamp']
              )
            );
          ?></div>
        <?php endif; ?>
      </div>

      <div class="fictioneer-comment__footer">

        <div class="fictioneer-comment__footer-left">
          <?php
          // =============================================================================
          // LOCKED COMMENT (THREAD)
          // =============================================================================
          ?>
          <div class="fictioneer-comment__in-moderation moderation-note-closed closed-icon">
            <i class="fa-solid fa-lock"></i>
          </div>
          <?php
          // =============================================================================
          // (PRIVATE) REPLY BUTTON
          // =============================================================================
          ?>
          <?php if ( $can_comment && ! $is_offensive && ! ( $is_report_hidden && ! $is_ignoring_reports ) ) : ?>
            <div class="fictioneer-comment__actions"><?php
              $caption = $comment->comment_type === 'private' || $is_child_of_private ? __( 'Private Reply', 'fictioneer' ) : __( 'Reply', 'fictioneer' );

              comment_reply_link(
                array_merge(
                  $args,
                  array(
                    'reply_text' => '<i class="fa-solid fa-reply"></i> ' . $caption,
                    'add_below' => 'div-comment', // Important for position of reply form
                    'depth' => $depth,
                    'max_depth' => $args['max_depth']
                  )
                ),
                $comment
              );
            ?></div>
          <?php endif; ?>
          <?php
          // =============================================================================
          // MODERATION NOTES
          // =============================================================================
          ?>
          <?php if ( $is_offensive ) : ?>
            <div class="fictioneer-comment__in-moderation moderation-note-offensive"><i class="fa-solid fa-bolt fa-fade" style="--fa-animation-duration: 1.5s; --fa-fade-opacity: 0.5;"></i>&nbsp;&nbsp;<?php _e( 'Marked and hidden as offensive.', 'fictioneer' ); ?></div>
          <?php elseif ( $is_report_hidden && ! $is_ignoring_reports ) : ?>
            <div class="fictioneer-comment__in-moderation moderation-note-reported"><i class="fa-solid fa-flag fa-fade" style="--fa-animation-duration: 2s; --fa-fade-opacity: 0.5;"></i>&nbsp;&nbsp;<?php _e( 'Hidden due to negative reports.', 'fictioneer' ); ?></div>
          <?php endif; ?>
          <div class="fictioneer-comment__in-moderation moderation-note-unapproved"><i class="fas fa-spinner fa-pulse"></i>&nbsp;&nbsp;<?php _e( 'Awaiting moderation.', 'fictioneer' ); ?></div>
        </div>

        <div class="fictioneer-comment__footer-right hide-unless-hover-on-desktop">
          <?php
          // =============================================================================
          // USER-EDIT COMMENT
          // > Render only if either the current user can (still) edit the comment, or if
          //   caching is active. In either case, the button will be made visible by JS.
          // =============================================================================
          ?>
          <?php if ( ( $can_edit && $is_editable ) || $is_caching ) : ?>
            <button
            class="fictioneer-comment__edit-toggle hide-on-edit tooltipped hide-if-logged-out"
            type="button"
            data-tooltip="<?php echo esc_attr_x( 'Edit', 'Edit comment inline.'. 'fictioneer' ); ?>"
            data-click="trigger-inline-comment-edit"
            <?php echo $ajax_in_progress && $is_editable ? '' : 'hidden'; ?>
          ><i class="fa-solid fa-pen"></i></button>
          <?php endif; ?>
          <?php
          // =============================================================================
          // USER-DELETE COMMENT
          // > Render only if the current user is the comment's owner or caching is
          //   active. In either case, the button will be made visible by JS.
          // =============================================================================
          ?>
          <?php if ( $is_owner || $is_caching ) : ?>
            <button
              class="fictioneer-comment__delete hide-on-edit tooltipped hide-if-logged-out"
              type="button"
              data-dialog-message="<?php echo esc_attr(
                sprintf(
                  __( 'Are you sure you want to delete your comment? Enter %s to confirm.', 'fictioneer' ),
                  strtoupper( _x( 'delete', 'Prompt confirm deletion string.', 'fictioneer' ) )
                )
              ); ?>"
              data-dialog-confirm="<?php echo esc_attr_x( 'delete', 'Prompt confirm deletion string.', 'fictioneer' ); ?>"
              data-tooltip="<?php echo esc_attr_x( 'Delete', 'Delete comment inline.'. 'fictioneer' ); ?>"
              data-click="delete-my-comment"
              <?php echo $ajax_in_progress ? '' : 'hidden'; ?>
            ><i class="fa-solid fa-eraser"></i></button>
          <?php endif; ?>
          <?php
          // =============================================================================
          // FINGERPRINT
          // =============================================================================
          ?>
          <?php if ( $fingerprint ) : ?>
            <span class="fictioneer-comment__fingerprint tooltipped _mobile-tooltip" data-tooltip="<?php echo esc_attr( $fingerprint ); ?>">
              <i class="fa-solid fa-fingerprint"></i>
            </span>
          <?php endif; ?>
          <?php
          // =============================================================================
          // AJAX INDICATOR
          // =============================================================================
          ?>
          <i class="fas fa-spinner spinner ajax"></i>
          <?php
          // =============================================================================
          // REPORTING
          // =============================================================================
          ?>
          <?php if ( fictioneer_show_auth_content() && $is_reportable ) : ?>
            <button
            class="fictioneer-report-comment-button hide-if-logged-out tooltipped <?php echo $flag_classes ?> <?php echo $is_flagged_by_current_user ? 'on' : ''; ?>"
            data-click="flag-comment"
            data-tooltip="<?php echo esc_attr_x( 'Report', 'Report this comment.', 'fictioneer' ); ?>"
          ><i class="fa-solid fa-flag"></i></button>
          <?php endif; ?>
          <?php
          // =============================================================================
          // FRONTEND MODERATION
          // =============================================================================
          ?>
          <?php fictioneer_comment_mod_menu( $comment ); ?>
        </div>

      </div>

    </div>
    <?php // <--- End HTML
  }
}

// =============================================================================
// THEME COMMENT PAGINATION MARKUP
// =============================================================================

if ( ! function_exists( 'fictioneer_pagination_markup' ) ) {
  /**
   * Change comment pagination markup
   *
   * @since Fictioneer 4.7
   *
   * @param string $template The default comment pagination template.
   *
   * @return string The modified comment pagination template.
   */

  function fictioneer_pagination_markup( $template ) {
    return '<nav class="pagination _padding-top %1$s" aria-label="%4$s" role="navigation">%3$s</nav>';
  }
}

if ( ! get_option( 'fictioneer_disable_comment_pagination' ) )  {
  add_filter( 'navigation_markup_template', 'fictioneer_pagination_markup', 10 );
}

?>
