<?php

// =============================================================================
// LOG IN TO COMMENT
// =============================================================================

/**
 * Change default comment reply login link to login modal toggle
 *
 * @since 5.0.0
 *
 * @param string     $link     The HTML markup for the comment reply link.
 * @param array      $args     An array of arguments overriding the defaults.
 * @param WP_Comment $comment  The object of the comment being replied.
 * @param WP_Post    $post     The WP_Post object.
 *
 * @return string Reply link.
 */

function fictioneer_comment_login_to_reply( $link, $args, $comment, $post ) {
  // Return default if OAuth authentication is disabled
  if ( ! get_option( 'fictioneer_enable_oauth' ) ) {
    return $link;
  }

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

/**
 * Prepend sticky comments
 *
 * @since 5.0.0
 *
 * @param array $comments  Queried comments.
 * @param int   $post_id   The post ID.
 *
 * @return array Comments with sticky ones on top.
 */

function fictioneer_shift_sticky_comments( $comments ) {
  // Extract sticky comments
  $sticky_comments = [];

  foreach ( $comments as $comment ) {
    if (
      get_comment_meta( $comment->comment_ID, 'fictioneer_sticky', true ) &&
      ! get_comment_meta( $comment->comment_ID, 'fictioneer_marked_offensive', true ) &&
      $comment->comment_approved &&
      $comment->comment_type !== 'user_deleted'
    ) {
      $sticky_comments[] = $comment;
    }
  }

  // Remove from original array
  $comments = array_udiff( $comments, $sticky_comments, function( $a, $b ) {
    if ( $a->comment_ID === $b->comment_ID ) {
      return 0;
    }

    return ( $a->comment_ID < $b->comment_ID ) ? -1 : 1;
  });

  // Prepend and return
  return array_merge( $sticky_comments, $comments );
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
   * @since 5.0.0
   *
   * @param int $comment_count  Total number of comments.
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
            number_format_i18n( $comment_count ),
            _n( 'Comment', 'Comments', $comment_count, 'fictioneer' )
          )
        ?>
      </h2>
      <?php if ( ! empty( $notice ) ) : ?>
        <div class="fictioneer-comments__notice"><?php echo $notice; ?></div>
      <?php endif; ?>
    </div>
    <?php // <--- End HTML
  }
}

// =============================================================================
// COMMENTS AJAX SKELETON
// =============================================================================

if ( ! function_exists( 'fictioneer_comments_ajax_skeleton' ) ) {
  /**
   * Output skeleton placeholder while comments are loaded via AJAX
   *
   * @since 5.0.0
   *
   * @param int $comments_count  Total number of comments.
   */

  function fictioneer_comments_ajax_skeleton( $comments_count ) {
    // Start HTML ---> ?>
    <div class="comments-skeleton">
      <div class="fictioneer-comments__header">
        <h2 class="fictioneer-comments__title"><div class="shape"></div></h2>
      </div>
      <?php if ( fictioneer_is_commenting_disabled() ) : ?>
        <div class="fictioneer-comments__disabled"><?php _e( 'Commenting is disabled.', 'fictioneer' ); ?></div>
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
// COMMENT FORM AJAX SKELETON
// =============================================================================

if ( ! function_exists( 'fictioneer_comments_ajax_form_skeleton' ) ) {
  /**
   * Output skeleton placeholder while the form is loaded via AJAX
   *
   * @since 5.0.0
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
   * @since 5.0.0
   *
   * @param array $comments  Collection of comments to display.
   * @param int   $page      Current page number of the comments.
   * @param array $args      With 'commentcode', 'post_author_id', and 'post_id'.
   */

  function fictioneer_ajax_list_comments( $comments, $page, $args = [] ) {
    // Abort conditions
    if ( count( $comments ) < 1) {
      return;
    }

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
      $list_args['reverse_children'] = ( $args['order'] ?? 0 ) === 'desc';
    }

    if ( ! get_option( 'fictioneer_disable_comment_callback' ) ) {
      $list_args['callback'] = 'fictioneer_theme_comment';
    }

    // Apply filters
    $list_args = apply_filters( 'fictioneer_filter_comment_list_args', $list_args );

    // Start HTML ---> ?>
    <div id="sof" class="sort-order-filter _comments">
      <button class="list-button _order <?php echo ( $args['order'] ?? 0 ) === 'desc' ? '_on' : '_off'; ?>" type="button" data-toggle-order aria-label="<?php esc_attr_e( 'Toggle comments between ascending and descending order', 'fictioneer' ); ?>">
        <i class="fa-solid fa-arrow-up-short-wide _off"></i><i class="fa-solid fa-arrow-down-wide-short _on"></i>
      </button>
    </div>

    <?php fictioneer_comment_moderation_template(); ?>

    <ol class="fictioneer-comments__list commentlist">
      <?php wp_list_comments( $list_args, $comments ); ?>
    </ol>
    <?php // <--- End HTML
  }
}

/**
 * Returns themed comment list arguments
 *
 * @since 5.0.0
 *
 * @param array $parsed_args  Default list arguments.
 *
 * @return array Modified arguments.
 */

function fictioneer_comment_list_args( $parsed_args ) {
  // Setup
  $page = get_query_var( 'cpage', 1 );
  $order = fictioneer_sanitize_query_var( $_GET['corder'] ?? 0, ['desc', 'asc'], get_option( 'comment_order' ) );

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
    $list_args['reverse_children'] = $order === 'desc';
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
// FORMATTING BBCODES (DUPLICATE IN STORY COMMENTS!)
// =============================================================================

if ( ! get_option( 'fictioneer_disable_comment_bbcodes' ) && ! get_option( 'fictioneer_disable_comment_callback' ) ) {
  add_filter( 'comment_text', 'fictioneer_bbcodes' );
}

// =============================================================================
// THEME COMMENT TEXT
// =============================================================================

/**
 * Properly wrap empty lines into paragraphs
 *
 * @since 4.0.0
 * @since 5.2.6 Updated and changed to not mess up some languages.
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

if ( ! get_option( 'fictioneer_disable_comment_callback' ) ) {
  add_filter( 'get_comment_text', 'fictioneer_replace_comment_line_breaks' );
  remove_filter( 'comment_text', 'make_clickable', 9 ); // No auto-linking of plain URLs
}

// =============================================================================
// COMMENT PARTS
// =============================================================================

/**
 * Returns the HTML for the comment self-delete button
 *
 * @since 5.23.1
 *
 * @param bool $hidden  Whether the button is (initially) hidden. Default true.
 *
 * @return string The HTML of the delete button.
 */

function fictioneer_get_comment_delete_button( $hidden = true ) {
  static $html_start = null;

  if ( is_null( $html_start ) ) {
    $html_start = '<button class="fictioneer-comment__delete comment-quick-button hide-on-edit tooltipped hide-if-logged-out hide-on-ajax" type="button" data-fictioneer-comment-target="deleteButton" data-action="click->fictioneer-comment#selfDelete" data-dialog-message="' .
      sprintf(
        __( 'Are you sure you want to delete your comment? Enter %s to confirm.', 'fictioneer' ),
        mb_strtoupper( _x( 'delete', 'Prompt confirm deletion string.', 'fictioneer' ) )
      ) .
      '" data-dialog-confirm="' . esc_attr_x( 'delete', 'Prompt confirm deletion string.', 'fictioneer' ) .
      '" data-tooltip="' . esc_attr_x( 'Delete', 'Delete comment inline.', 'fictioneer' ) .
      '" %s><i class="fa-solid fa-eraser"></i></button>';
  }

  return sprintf(
    $html_start,
    $hidden ? 'hidden' : ''
  );
}

/**
 * Returns the HTML for the comment edit button
 *
 * @since 5.23.1
 *
 * @param bool $hidden  Whether the button is (initially) hidden. Default true.
 *
 * @return string The HTML of the edit button.
 */

function fictioneer_get_comment_edit_button( $hidden = true ) {
  static $html_start = null;

  if ( is_null( $html_start ) ) {
    $html_start = '<button class="fictioneer-comment__edit-toggle comment-quick-button hide-on-edit tooltipped hide-if-logged-out hide-on-ajax" type="button" data-fictioneer-comment-target="editButton" data-action="click->fictioneer-comment#startEditInline" data-tooltip="' . esc_attr_x( 'Edit', 'Edit comment inline.', 'fictioneer' ) . '" %s><i class="fa-solid fa-pen"></i></button>';
  }

  return sprintf(
    $html_start,
    $hidden ? 'hidden' : ''
  );
}

// =============================================================================
// THEME COMMENT CALLBACK
// =============================================================================

if ( ! function_exists( 'fictioneer_theme_comment' ) ) {
  /**
   * Callback function for basic theme comment markup
   *
   * @since 4.7.0
   * @global WP_Post|null $post  The post object. May not be available.
   *
   * @param WP_Comment $comment  The comment object.
   * @param array      $args     List of arguments.
   * @param int        $depth    Depth.
   */

  function fictioneer_theme_comment( $comment, $args, $depth ) {
    // Global not available in AJAX request!
    global $post;

    // User data
    $current_user = wp_get_current_user(); // Viewer, not necessarily commenter!
    $is_new = $args['new'] ?? false;

    // Post data
    $post_id = empty( $post ) ? ( $args['post_id'] ?? 0 ) : $post->ID;
    $post_author_id = empty( $post ) ? ( $args['post_author_id'] ?? 0 ) : $post->post_author;

    // Commenter data
    $comment_user_id = $comment->user_id; // User ID of comment author or 0
    $comment_author = empty( $comment->comment_author ) ? fcntr( 'anonymous_guest' ) : $comment->comment_author;
    $fingerprint = $comment_user_id ? fictioneer_get_user_fingerprint( $comment_user_id ) : false;
    $badge = fictioneer_get_comment_badge( get_user_by( 'id', $comment_user_id ), $comment, $post_author_id );

    // Comment data
    $unapproved_comment_email = wp_get_unapproved_comment_author_email();
    $timestamp = strtotime( "{$comment->comment_date_gmt} GMT" );
    $edit_stack = get_comment_meta( $comment->comment_ID, 'fictioneer_user_edit_stack', true );
    $parent_comment = $comment->comment_parent ? get_comment( absint( $comment->comment_parent ) ) : false;
    $reports = get_comment_meta( $comment->comment_ID, 'fictioneer_user_reports', true );
    $commentcode = sanitize_text_field( $_GET['commentcode'] ?? '' ) ?: false;

    if ( wp_doing_ajax() || $is_new ) {
      $commentcode = $args['commentcode'] ?? false;
    }

    // Build helpers
    $can_comment = ! fictioneer_is_commenting_disabled( $post_id );
    $tag = $args['style'] === 'div' ? 'div' : 'li';
    $classes = ['fictioneer-comment'];
    $flag_classes = '';

    // Flags
    $cache_plugin_active = fictioneer_caching_active( 'theme_comment' );
    $is_owner = ! empty( $comment_user_id ) && $comment_user_id == $current_user->ID;
    $is_approved = $comment->comment_approved;
    $is_offensive = get_comment_meta( $comment->comment_ID, 'fictioneer_marked_offensive', true );
    $is_closed = get_comment_meta( $comment->comment_ID, 'fictioneer_thread_closed', true );
    $is_deleted_by_owner = $comment->comment_type === 'user_deleted';
    $is_ignoring_reports = get_comment_meta( $comment->comment_ID, 'fictioneer_ignore_reports', true );
    $is_child_of_private = false;
    $is_report_hidden = false;
    $is_flagged_by_current_user = false;

    $is_reportable = ! get_the_author_meta( 'fictioneer_admin_disable_reporting', $current_user->ID ) &&
      get_option( 'fictioneer_enable_comment_reporting' );

    $is_sticky = get_option( 'fictioneer_enable_sticky_comments' ) &&
      get_comment_meta( $comment->comment_ID, 'fictioneer_sticky', true ) &&
      ! $parent_comment && ! $is_deleted_by_owner && ! $is_offensive;

    $is_hidden = ! is_user_logged_in() ||
      (
        ! current_user_can( 'moderate_comments' ) &&
        $comment->user_id != $current_user->ID &&
        $post_author_id != $current_user->ID &&
        ! ( $parent_comment && $parent_comment->user_id == $current_user->ID )
      );

    $is_editable = get_option( 'fictioneer_enable_user_comment_editing' ) &&
      $is_owner && ! $is_offensive && ! $is_closed;

    // Check reports
    if ( get_option( 'fictioneer_enable_comment_reporting' ) ) {
      $is_report_hidden = empty( $reports ) ? false : count( $reports ) >= get_option( 'fictioneer_comment_report_threshold', 10 );
      $is_flagged_by_current_user = ( $current_user && ! empty( $reports ) ) ? array_key_exists( $current_user->ID, $reports ) : false;
    }

    // No individual flags for cached comments unless loaded via AJAX or caches are per user
    if ( ! wp_doing_ajax() && $cache_plugin_active && ! fictioneer_private_caching_active() ) {
      $is_flagged_by_current_user = false;
      $flag_classes = '_dubious';
    }

    // Ancestors?
    $ancestor = $parent_comment;
    $closed_ancestor = false;

    while ( $ancestor ) {
      if ( ! is_object( $ancestor ) ) {
        break;
      }

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
      if ( $is_child_of_private && $closed_ancestor ) {
        break;
      }

      // Next ancestor if any
      $ancestor = $ancestor->comment_parent ? get_comment( $ancestor->comment_parent ) : false;
    }

    // Additional classes
    $class_candidates = array(
      'parent' => ! empty( $args['has_children'] ),
      '_unapproved' => ! $is_approved,
      '_offensive' => $is_offensive,
      'hidden-by-reports' => $is_report_hidden && ! $is_ignoring_reports,
      '_closed' => $is_closed,
      '_sticky' => $is_sticky,
      '_deleted' => $is_deleted_by_owner
    );

    foreach ( $class_candidates as $class => $condition ) {
      if ( $condition ) {
        $classes[] = $class;
      }
    }

    /**
     * Show or hide comment if not approved, no moderation capabilities, no email has been set
     * or it doesn't match the cookie (if any), and the comment code doesn't match either or
     * is expired.
    */

    // Commentcode will be valid for n seconds (default: 600) after posting (-1 for infinite)
    if ( $commentcode ) {
      $commentcode = hash_equals( $commentcode, wp_hash( $comment->comment_date_gmt ) );

      if (
        $commentcode &&
        FICTIONEER_COMMENTCODE_TTL > 0 &&
        time() > strtotime( $comment->comment_date_gmt ) + FICTIONEER_COMMENTCODE_TTL
      ) {
        $commentcode = false;
      }
    }

    if (
      ! $is_approved &&
      ! $is_report_hidden &&
      ! $commentcode &&
      ! fictioneer_user_can_moderate( $comment ) &&
      ( $unapproved_comment_email != $comment->comment_author_email || empty( $unapproved_comment_email ) ) &&
      ! $is_new
    ) {
      return;
    }

    // Build HTML (tag closed by WordPress!)
    $comment_class = comment_class( $classes, $comment, $comment->comment_post_ID, false );
    $fingerprint_data = empty( $fingerprint ) ? '' : "data-fictioneer-comment-fingerprint-value=\"$fingerprint\"";

    if ( $is_new ) {
      $comment_class = str_replace( 'depth-1', "depth-$depth", $comment_class );
    }

    $open = "<{$tag} id=\"comment-{$comment->comment_ID}\" {$comment_class} data-id=\"{$comment->comment_ID}\" data-depth=\"{$depth}\">";
    $open .= "<div id=\"div-comment-{$comment->comment_ID}\" class=\"fictioneer-comment__container\" data-controller=\"fictioneer-comment\" data-action=\"mouseleave->fictioneer-comment#mouseLeave:stop\" data-fictioneer-comment-id-value=\"{$comment->comment_ID}\" data-fictioneer-comment-timestamp-value=\"" . ( $timestamp * 1000 ) . "\" {$fingerprint_data}>";

    if ( $is_sticky ) {
      $open .= '<i class="fa-solid fa-thumbtack sticky-pin"></i>';
    }

    // Hidden private comments
    if ( $comment->comment_type === 'private' && $is_hidden && ! $commentcode && ! $is_child_of_private && ! $is_new ) {
      // Start HTML ---> ?>
      <?php echo $open; ?>
        <div class="fictioneer-comment__hidden-notice"><?php _e( 'Comment has been marked as private.', 'fictioneer' ); ?></div>
      </div>
      <?php // <--- End HTML
      return;
    }

    // Hidden child of a private comment
    if ( $is_child_of_private && $is_hidden && ! $is_new ) {
      // Start HTML ---> ?>
      <?php echo $open; ?>
        <div class="fictioneer-comment__hidden-notice"><?php _e( 'Reply has been marked as private.', 'fictioneer' ); ?></div>
      </div>
      <?php // <--- End HTML
      return;
    }

    // Hidden because marked as offensive
    if ( $is_offensive && $is_hidden ) {
      // Start HTML ---> ?>
      <?php echo $open; ?>
        <div class="fictioneer-comment__hidden-notice"><?php _e( 'Comment has been marked as offensive.', 'fictioneer' ); ?></div>
      </div>
      <?php // <--- End HTML
      return;
    }

    // Hidden by reports
    if ( $is_report_hidden && ! $is_ignoring_reports && $is_hidden ) {
      // Start HTML ---> ?>
      <?php echo $open; ?>
        <div class="fictioneer-comment__hidden-notice"><?php _e( 'Comment is hidden due to negative reports.', 'fictioneer' ); ?></div>
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
            <?php _e( 'Comment has been deleted by user.', 'fictioneer' ); ?>
          </div>
          <div class="fictioneer-comment__footer-right hide-unless-hover-on-desktop">
            <?php fictioneer_comment_moderation( $comment ); ?>
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
        <?php
          $id_or_email = $comment->user_id ?: $comment->comment_author_email;
          $avatar = get_avatar( $id_or_email, $args['avatar_size'] );

          if ( $avatar && $args['avatar_size'] != 0 ) {
            echo $avatar;
          }
        ?>
        <div class="fictioneer-comment__meta">
          <div class="fictioneer-comment__author"><?php
            if ( fictioneer_is_author( $comment->user_id ) ) {
              echo '<a href="' . get_author_posts_url( $comment->user_id ) . '">' . $comment->comment_author . '</a>';
            } else {
              echo "<span>{$comment_author}</span>";
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
        <div class="fictioneer-comment__content" data-fictioneer-comment-target="content"><?php
          comment_text( $comment );
        ?></div>
        <?php
        // =============================================================================
        // COMMENT EDIT
        // =============================================================================
        ?>
        <?php if ( ( $can_edit && $is_editable ) || ( $cache_plugin_active && ! $is_deleted_by_owner) ) : ?>
          <div class="fictioneer-comment__edit" data-fictioneer-comment-target="inlineEditWrapper" hidden>
            <textarea class="comment-inline-edit-content" data-fictioneer-comment-target="inlineEditTextarea"><?php
              echo $comment->comment_content;
            ?></textarea>
          </div>
        <?php endif; ?>
        <?php
        // =============================================================================
        // EDIT NOTE
        // =============================================================================
        ?>
        <?php if ( ! empty( $edit_stack ) && ! $is_deleted_by_owner ) : ?>
          <div class="fictioneer-comment__edit-note" data-fictioneer-comment-target="editNote"><?php
            $edit_data = $edit_stack[count( $edit_stack ) - 1];
            $edit_user = isset( $edit_data['user_id'] ) && $comment->user_id != $edit_data['user_id'] ?
              get_user_by( 'id', $edit_data['user_id'] ) : false;

            printf(
              _x( 'Last edited on %s%s', 'Comment last edited by user on {datetime}{end}', 'fictioneer' ),
              wp_date(
                sprintf(
                  _x( '%1$s \a\t %2$s', 'Comment time format string.', 'fictioneer' ),
                  get_option( 'fictioneer_subitem_date_format', "M j, 'y" ) ?: "M j, 'y",
                  get_option( 'time_format' )
                ),
                $edit_data['timestamp']
              ),
              ! $edit_user ? '.' : sprintf(
                _x( ' by %s.', 'Comment edited by {user}.', 'fictioneer' ),
                $edit_user->display_name
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
          <?php if ( ! ( $is_report_hidden && ! $is_ignoring_reports ) ) : ?>
            <div class="fictioneer-comment__actions"><?php
              $actions = [];

              if ( ! $is_offensive && $can_comment ) {
                $caption = $comment->comment_type === 'private' || $is_child_of_private ?
                  __( 'Private Reply', 'fictioneer' ) : __( 'Reply', 'fictioneer' );

                $actions['reply'] = get_comment_reply_link(
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
              }

              $actions = apply_filters(
                'fictioneer_filter_comment_actions',
                $actions,
                $comment,
                array_merge(
                  $args,
                  array(
                    'has_private_ancestor' => $is_child_of_private,
                    'has_closed_ancestor' => $closed_ancestor,
                    'sticky' => $is_sticky
                  )
                ),
                $depth
              );

              echo implode( '', $actions );
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

          if ( ( $can_edit && $is_editable ) || $cache_plugin_active ) {
            echo fictioneer_get_comment_edit_button( ! ( $is_new && $is_editable ) );
          }

          // =============================================================================
          // USER-DELETE COMMENT
          // > Render only if the current user is the comment's owner or caching is
          //   active. In either case, the button will be made visible by JS.
          // =============================================================================

          if ( $is_owner || $cache_plugin_active ) {
            echo fictioneer_get_comment_delete_button( ! $is_new );
          }

          // =============================================================================
          // FINGERPRINT
          // =============================================================================
          ?>
          <?php if ( $fingerprint ) : ?>
            <span class="fictioneer-comment__fingerprint comment-quick-button tooltipped _mobile-tooltip hide-on-ajax" data-tooltip="<?php echo esc_attr( $fingerprint ); ?>">
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
            class="fictioneer-report-comment-button comment-quick-button hide-if-logged-out tooltipped <?php echo $flag_classes ?> <?php echo $is_flagged_by_current_user ? 'on' : ''; ?> hide-on-ajax"
            data-fictioneer-comment-target="flagButton"
            data-action="click->fictioneer-comment#flag"
            data-tooltip="<?php echo esc_attr_x( 'Report', 'Report this comment.', 'fictioneer' ); ?>"
          ><i class="fa-solid fa-flag"></i></button>
          <?php endif; ?>
          <?php
          // =============================================================================
          // FRONTEND MODERATION
          // =============================================================================
          ?>
          <?php fictioneer_comment_moderation( $comment ); ?>
        </div>

      </div>

    </div>
    <?php // <--- End HTML
  }
}

// =============================================================================
// THEME COMMENT PAGINATION MARKUP
// =============================================================================

/**
 * Change comment pagination markup
 *
 * @since 4.7.0
 *
 * @param string $template The default comment pagination template.
 *
 * @return string The modified comment pagination template.
 */

function fictioneer_pagination_markup( $template ) {
  return '<nav class="pagination _padding-top %1$s" aria-label="%4$s" role="navigation">%3$s</nav>';
}

if ( ! get_option( 'fictioneer_disable_comment_pagination' ) )  {
  add_filter( 'navigation_markup_template', 'fictioneer_pagination_markup' );
}
