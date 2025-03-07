<?php

// =============================================================================
// FORMATTING BBCODES (DUPLICATE TO AVOID INCLUDING OTHER FILE)
// =============================================================================

if ( ! get_option( 'fictioneer_disable_comment_bbcodes' ) && ! get_option( 'fictioneer_disable_comment_callback' ) ) {
  remove_filter( 'comment_text', 'fictioneer_bbcodes' ); // Make sure the filter is only added once
  add_filter( 'comment_text', 'fictioneer_bbcodes' );
}

// =============================================================================
// BUILD STORY COMMENT
// =============================================================================

if ( ! function_exists( 'fictioneer_build_story_comment' ) ) {
  /**
   * Outputs a single comment for the story comments list
   *
   * @since 4.0.0
   *
   * @param object $comment       The comment.
   * @param array  $chapter_data  Array of [post_id] tuples with title [0] and
   *                              permalink [1] of the associated chapter.
   * @param int    $depth         The current depth of the comment. Default 0.
   */

  function fictioneer_build_story_comment( $comment, $chapter_data, $depth = 0 ) {
    // Setup
    $post_id = $comment->comment_post_ID;
    $type = $comment->comment_type;
    $post = get_post( $post_id );
    $parent = get_comment( $comment->comment_parent );
    $name = empty( $comment->comment_author ) ? fcntr( 'anonymous_guest' ) : $comment->comment_author;

    if ( $type == 'private' ) {
      echo '<div class="fictioneer-comment__hidden-notice">' . __( 'Comment has been marked as private.', 'fictioneer' ) . '</div>';
      return;
    }

    // Get badge (if any)
    $badge = fictioneer_get_comment_badge( get_user_by( 'id', $comment->user_id ), $comment, $post->post_author );

    // Start HTML ---> ?>
    <div class="fictioneer-comment__header">
      <?php
        $id_or_email = $comment->user_id ?: $comment->comment_author_email;

        echo get_avatar( $id_or_email, 32 ) ?: '';
      ?>
      <div class="fictioneer-comment__meta">
        <div class="fictioneer-comment__author"><span><?php echo $name; ?></span><?php echo $badge; ?></div>
        <div class="fictioneer-comment__info truncate _1-1">
          <?php
            printf(
              _x( '%1$s &bull; %2$s', 'Comment meta: [Date] &bull; [Chapter]', 'fictioneer' ),
              '<span class="fictioneer-comment__date">' . date_format(
                date_create( $comment->comment_date ),
                sprintf(
                  _x( '%1$s \a\t %2$s', 'Comment time format string.', 'fictioneer' ),
                  get_option( 'fictioneer_subitem_date_format', "M j, 'y" ) ?: "M j, 'y",
                  get_option( 'time_format' )
                )
              ) . '</span>',
              '<a href="' . $chapter_data[ $post_id ][1] . '" class="fictioneer-comment__link">' . $chapter_data[ $post_id ][0] . '</a>'
            );
          ?>
        </div>
      </div>
    </div>
    <div class="fictioneer-comment__body clearfix">
      <?php if ( $parent ) : ?>
        <?php $parent_name = empty( $parent->comment_author ) ? fcntr( 'anonymous_guest' ) : $parent->comment_author;  ?>
        <div class="fictioneer-comment__encapsulated">
          <?php if ( $depth > 3 ) : ?>
            <i class="fas fa-reply"></i>&nbsp;<?php printf( __( 'In reply to %s', 'fictioneer' ), $parent_name ); ?>
          <?php else : ?>
            <details>
              <summary>
                <i class="fas fa-reply"></i>&nbsp;<?php printf( __( 'In reply to %s', 'fictioneer' ), $parent_name ); ?>
              </summary>
              <div class="fictioneer-comment__parent-comment"><?php
                fictioneer_build_story_comment( $parent, $chapter_data, $depth + 1 );
              ?></div>
            </details>
          <?php endif; ?>
        </div>
      <?php endif; ?>
      <?php
        if ( $post->post_status !== 'publish' ) {
          printf(
            '<div class="fictioneer-comment__encapsulated"><details><summary><i class="fa-solid fa-calendar-days icon"></i> %s</summary>',
            __( 'Chapter is not yet published. Click to show comment.', 'fictioneer' )
          );
        }

        comment_text( $comment );

        if ( $post->post_status !== 'publish' ) {
          echo '</details></div>';
        }
      ?>
    </div>
    <?php // <--- End HTML
  }
}

// =============================================================================
// GET STORY COMMENTS - REST
// =============================================================================

/**
 * Register REST API endpoint to fetch story comments
 *
 * Endpoint URL: /wp-json/fictioneer/v1/get_story_comments
 *
 * Expected Parameters:
 * - post_id (required): The ID of the story post.
 * - page (optional): The page number for pagination. Default 1.
 *
 * @since 5.6.3
 */

function fictioneer_register_endpoint_get_story_comments() {
  register_rest_route(
    'fictioneer/v1',
    '/get_story_comments',
    array(
      'methods' => 'GET',
      'callback' => 'fictioneer_rest_get_story_comments',
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
        )
      ),
      'permission_callback' => '__return_true' // Public
    )
  );
}
add_action( 'rest_api_init', 'fictioneer_register_endpoint_get_story_comments' );

/**
 * Sends HTML of paginated comments for a given story
 *
 * @since 4.0.0
 * @since 5.6.3 Refactored for REST API.
 *
 * @param WP_REST_Request $request  Request object.
 *
 * @return WP_REST_Response|WP_Error Response or error.
 */

function fictioneer_rest_get_story_comments( WP_REST_Request $request ) {
  // Rate limit
  fictioneer_check_rate_limit( 'fictioneer_rest_get_story_comments', 10 );

  // Validations
  $story_id = $request->get_param( 'post_id' );
  $story_id = isset( $story_id ) ? fictioneer_validate_id( $story_id, 'fcn_story' ) : false;
  $default_error = array( 'error' => __( 'Comments could not be loaded.', 'fictioneer' ) );

  // Abort if not a story
  if ( ! $story_id ) {
    if ( WP_DEBUG ) {
      $data = array( 'error' => __( 'Provided post ID is not a story ID.', 'fictioneer' ) );
    } else {
      $data = $default_error;
    }

    return rest_ensure_response( array( 'data' => $data, 'success' => false ), 400 );
  }

  // Setup
  $page = $request->get_param( 'page' );
  $story = fictioneer_get_story_data( $story_id, true, array( 'refresh_comment_count' => true ) );
  $chapter_ids = $story['chapter_ids']; // Only contains publicly visible chapters
  $comments_per_page = get_option( 'comments_per_page' );
  $chapter_data = [];
  $report_threshold = get_option( 'fictioneer_comment_report_threshold', 10 ) ?? 10;

  // Abort if no chapters have been found
  if ( count( $chapter_ids ) < 1 ) {
    if ( WP_DEBUG ) {
      $data = array( 'error' => __( 'There are no valid chapters with comments.', 'fictioneer' ) );
    } else {
      $data = $default_error;
    }

    return rest_ensure_response( array( 'data' => $data, 'success' => false ) );
  }

  // Collect titles and permalinks of all chapters
  foreach ( $chapter_ids as $chapter_id ) {
    $chapter_data[ $chapter_id ] = [get_the_title( $chapter_id ), get_the_permalink( $chapter_id )];
  }

  // Get comments
  $comments = get_comments(
    array(
      'status' => 'approve',
      'post_type' => ['fcn_chapter'],
      'post__in' => $chapter_ids ?: [0], // Must not be empty!
      'number' => $comments_per_page,
      'paged' => $page,
      'meta_query' => array(
        array(
          'relation' => 'OR',
          array(
            'key' => 'fictioneer_marked_offensive',
            'value' => [true, 1, '1'],
            'compare' => 'NOT IN'
          ),
          array(
            'key' => 'fictioneer_marked_offensive',
            'compare' => 'NOT EXISTS'
          )
        )
      ),
      'no_found_rows' => true
    )
  );

  // Calculate how many more comments (if any) are there after this batch
  $remaining = $story['comment_count'] - $page * $comments_per_page;

  // Start buffer
  ob_start();

  // Make sure there are comments to display...
  if ( count( $comments ) > 0 ) {
    foreach ( $comments as $comment ) {
      $reports = get_comment_meta( $comment->comment_ID, 'fictioneer_user_reports', true );

      // Render empty if...
      if ( $comment->comment_type === 'user_deleted' ) {
        // Start HTML ---> ?>
        <li class="fictioneer-comment _deleted">
          <div class="fictioneer-comment__container">
            <div class="fictioneer-comment__hidden-notice"><?php _e( 'Comment has been deleted by user.', 'fictioneer' ); ?></div>
          </div>
        </li>
        <?php // <--- End HTML

        continue;
      }

      // Hidden by reports...
      if ( get_option( 'fictioneer_enable_comment_reporting' ) ) {
        if ( ! empty( $reports ) && count( $reports ) >= $report_threshold ) {
          // Start HTML ---> ?>
          <li class="fictioneer-comment _deleted">
            <div class="fictioneer-comment__container">
              <div class="fictioneer-comment__hidden-notice"><?php _e( 'Comment is hidden due to negative reports.', 'fictioneer' ); ?></div>
            </div>
          </li>
          <?php // <--- End HTML

          continue;
        }
      }

      // Start HTML ---> ?>
      <li class="fictioneer-comment _story-comment <?php echo $comment->comment_type; ?>">
        <div class="fictioneer-comment__container">
          <?php fictioneer_build_story_comment( $comment, $chapter_data ); ?>
        </div>
      </li>
      <?php // <--- End HTML
    }

    // Load more button and loading placeholder if there are still comments left
    if ( $remaining > 0 ) {
      $load_n = $remaining > $comments_per_page ? $comments_per_page : $remaining;

      // Start HTML ---> ?>
      <li class="load-more-list-item">
        <button class="load-more-comments-button" data-action="click->fictioneer-story#loadComments"><?php
          printf(
            _n(
              'Load next comment (may contain spoilers)',
              'Load next %s comments (may contain spoilers)',
              $load_n,
              'fictioneer'
            ),
            $load_n
          );
        ?></button>
      </li>
      <div class="comments-loading-placeholder hidden" data-fictioneer-story-target="commentsPlaceholder"><i class="fas fa-spinner spinner"></i></div>
      <?php // <--- End HTML
    }
  } else {
    // Start HTML ---> ?>
    <li class="fictioneer-comment private">
      <div class="fictioneer-comment__container">
        <div class="fictioneer-comment__hidden-notice"><?php _e( 'No public comments to display.', 'fictioneer' ); ?></div>
      </div>
    </li>
    <?php // <--- End HTML
  }

  // Get buffer
  $output = fictioneer_minify_html( ob_get_clean() );

  // Response body (compatible to wp_send_json_success())
  $data = array( 'html' => $output, 'postId' => $story_id, 'page' => $page );

  // Return buffer
  return rest_ensure_response( array( 'data' => $data, 'success' => true ) );
}
