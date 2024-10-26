<?php

// =============================================================================
// SEND MESSAGE TO DISCORD WEBHOOK
// =============================================================================

if ( ! function_exists( 'fictioneer_discord_send_message' ) ) {
  /**
   * Sends a message to a Discord channel
   *
   * @since 4.0.0
   * @since 5.6.0 - Refactored with wp_remote_post()
   *
   * @param string $webhook  The webhook for the Discord channel.
   * @param array  $message  The message to be sent.
   *
   * @return array|WP_Error|null Null if not in debug mode, otherwise
   *                             the response or WP_Error on failure.
   */

  function fictioneer_discord_send_message( $webhook, $message ) {
    if ( empty( $message ) ) {
      return;
    }

    return wp_remote_post(
      $webhook,
      array(
        'headers' => array(
          'Content-Type' => 'application/json'
        ),
        'body' => json_encode( $message, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ),
        'method' => 'POST',
        'data_format' => 'body',
        'blocking' => ! WP_DEBUG
      )
    );
  }
}

// =============================================================================
// POST NEW COMMENT TO DISCORD
// =============================================================================

/**
 * Sends a comment as message to a Discord channel
 *
 * @since 4.0.0
 * @see fictioneer_discord_send_message()
 *
 * @param int        $comment_id        The comment ID.
 * @param int|string $comment_approved  1 if the comment is approved, 0 if not,
 *                                      'spam' if spam.
 */

function fictioneer_post_comment_to_discord( $comment_id, $comment_approved ) {
  // Setup
  $comment = get_comment( $comment_id );
  $comment_type = get_comment_type( $comment_id );
  $comment_status = wp_get_comment_status( $comment );
  $comment_avatar_url = get_avatar_url( $comment );
  $post = get_post( $comment->comment_post_ID );
  $user = get_user_by( 'id', $comment->user_id );

  if ( $user && ! empty( $user->fictioneer_external_avatar_url ) ) {
    $comment_avatar_url = $user->fictioneer_external_avatar_url;
  }

  // Message
  $message = array(
    'content' => null,
    'embeds' => array(
      array(
        'title' => html_entity_decode( get_the_title( $post ) ),
        'description' => html_entity_decode( get_comment_excerpt( $comment ) ),
        'url' => get_comment_link( $comment ),
        'color' => $comment_status === 'approved' ? '9692513' : '14112322',
        'fields' => array(
          array(
            'name' => _x( 'Status', 'Discord message "Status" field.', 'fictioneer' ),
            'value' => fcntr( "{$comment_status}_comment_status" ),
            'inline' => true
          ),
          array(
            'name' => _x( 'Comment ID', 'Discord message "Comment ID" field.', 'fictioneer' ),
            'value' => $comment_id,
            'inline' => true
          )
        ),
        'author' => array(
          'name' => $comment->comment_author,
          'icon_url' => $comment_avatar_url ?: ''
        ),
        'timestamp' => date_format( date_create( $comment->comment_date ), 'c' )
      )
    )
  );

  // Has parent comment?
  if ( $comment->comment_parent ) {
    $message['embeds'][0]['fields'][] = array(
      'name' => _x( 'Reply to', 'Discord message "Reply to" field.', 'fictioneer' ),
      'value' => get_comment( $comment->comment_parent )->comment_author,
      'inline' => true
    );
  } else {
    $message['embeds'][0]['fields'][] = array(
      'name' => _x( 'Reply to', 'Discord message "Reply to" field.', 'fictioneer' ),
      'value' => __( 'n/a', 'fictioneer' ),
      'inline' => true
    );
  }

  // Is chapter with story?
  $story_id = fictioneer_get_chapter_story_id( $post->ID );

  if ( $story_id ) {
    $message['embeds'][0]['footer'] = array(
      'text' => sprintf(
        _x( 'Story: %s', 'Discord message story footer note.', 'fictioneer' ),
        html_entity_decode( get_the_title( $story_id ) )
      )
    );
  }

  // Registered user?
  if ( $user ) {
    // Registered
    $role = translate_user_role( wp_roles()->roles[ $user->roles[0] ]['name'] );

    $message['embeds'][0]['fields'][] = array(
      'name' => _x( 'User Role', 'Discord message "User Role" field.', 'fictioneer' ),
      'value' => $role,
      'inline' => true
    );

    $message['embeds'][0]['fields'][] = array(
      'name' => _x( 'User ID', 'Discord message "User ID" field.', 'fictioneer' ),
      'value' => $comment->user_id,
      'inline' => true
    );
  } else {
    // Not registered
    $message['embeds'][0]['fields'][] = array(
      'name' => _x( 'User Role', 'Discord message "User Role" field.', 'fictioneer' ),
      'value' => __( 'Guest', 'fictioneer' ),
      'inline' => true
    );

    $message['embeds'][0]['fields'][] = array(
      'name' => _x( 'User ID', 'Discord message "User ID" field.', 'fictioneer' ),
      'value' => __( 'n/a', 'fictioneer' ),
      'inline' => true
    );
  }

  // Comment type
  $message['embeds'][0]['fields'][] = array(
    'name' => _x( 'Type', 'Discord message comment "Type" field.', 'fictioneer' ),
    'value' => fcntr( "{$comment_type}_comment" ),
    'inline' => true
  );

  // Filter
  $message = apply_filters( 'fictioneer_filter_discord_comment_message', $message, $comment, $post, $user );
  $webhook = apply_filters(
    'fictioneer_filter_discord_comment_webhook',
    get_option( 'fictioneer_discord_channel_comments_webhook' ),
    $comment,
    $post,
    $user
  );

  // Send to Discord
  fictioneer_discord_send_message( $webhook, $message );

  // Unhook if done to avoid additional triggers (if any)
  remove_action( 'comment_post', 'fictioneer_post_comment_to_discord', 99 );
}

if ( get_option( 'fictioneer_discord_channel_comments_webhook' ) ) {
  add_action( 'comment_post', 'fictioneer_post_comment_to_discord', 99, 2 );
}

// =============================================================================
// POST PUBLISHED STORY TO DISCORD
// =============================================================================

/**
 * Sends a notification to Discord when a story is first published
 *
 * @since 5.6.0
 * @since 5.21.2 - Refactored.
 *
 * @param string  $new_status  New post status.
 * @param string  $new_status  Old post status.
 * @param WP_Post $post        Post object.
 */

function fictioneer_post_story_to_discord( $new_status, $old_status, $post ) {
  // Only if story going from non-publish status to publish
  if ( $post->post_type !== 'fcn_story' || $new_status !== 'publish' || $old_status === 'publish' ) {
    return;
  }

  // Already triggered (this field will eventually be deleted in favor of the time check)?
  if ( get_post_meta( $post->ID, 'fictioneer_discord_post_trigger' ) ) {
    return;
  }

  // Data
  $title = html_entity_decode( get_the_title( $post ) );
  $url = get_permalink( $post->ID );

  // Message
  $message = array(
    'content' => sprintf(
      _x( "New story published: [%s](%s)!\n_ _", 'Discord message for new story.', 'fictioneer' ),
      $title,
      $url
    ),
    'embeds' => array(
      array(
        'title' => $title,
        'description' => html_entity_decode( get_the_excerpt( $post ) ),
        'url' => get_permalink( $post->ID ),
        'color' => FICTIONEER_DISCORD_EMBED_COLOR,
        'author' => array(
          'name' => get_the_author_meta( 'display_name', $post->post_author ),
          'icon_url' => get_avatar_url( $post->post_author )
        ),
        'timestamp' => get_the_date( 'c', $post )
      )
    )
  );

  // Thumbnail?
  $thumbnail_url = get_the_post_thumbnail_url( $post, 'thumbnail' );

  if ( ! empty( $thumbnail_url ) ) {
    $message['embeds'][0]['thumbnail'] = array(
      'url' => $thumbnail_url
    );
  }

  // Filter
  $message = apply_filters( 'fictioneer_filter_discord_story_message', $message, $post );
  $webhook = apply_filters(
    'fictioneer_filter_discord_story_webhook',
    get_option( 'fictioneer_discord_channel_stories_webhook' ),
    $post
  );

  // Send to Discord
  fictioneer_discord_send_message( $webhook, $message );

  // Remember trigger
  update_post_meta( $post->ID, 'fictioneer_discord_post_trigger', true );

  // Unhook if done to avoid additional triggers (if any)
  remove_action( 'transition_post_status', 'fictioneer_post_story_to_discord', 99 );
}

if ( get_option( 'fictioneer_discord_channel_stories_webhook' ) ) {
  add_action( 'transition_post_status', 'fictioneer_post_story_to_discord', 99, 3 );
}

// =============================================================================
// POST PUBLISHED CHAPTER TO DISCORD
// =============================================================================

/**
 * Sends a notification to Discord when a chapter is first published
 *
 * @since 5.6.0
 * @since 5.21.2 - Refactored.
 * @since 5.24.1 - Switch back to save_post hook to ensure the story ID is set.
 *
 * @param int     $post_id  Post ID.
 * @param WP_Post $post     Post object.
 * @param bool    $update   Whether this is an existing post being updated.
 */

function fictioneer_post_chapter_to_discord( $post_id, $post, $update ) {
  // Prevent multi-fire
  if ( fictioneer_multi_save_guard( $post_id ) ) {
    return;
  }

  // Only if published chapter
  if ( $post->post_type !== 'fcn_chapter' || $post->post_status !== 'publish' ) {
    return;
  }

  // Only if published less than 10 minutes ago
  $post_timestamp = get_post_time( 'U', true, $post_id );
  $current_timestamp = current_time( 'U', true );

  if ( $update && ( $current_timestamp - $post_timestamp ) >= 600 ) {
    return;
  }

  // Already triggered (this field will eventually be deleted in favor of the time check)?
  if ( get_post_meta( $post_id, 'fictioneer_discord_post_trigger' ) ) {
    return;
  }

  // Message
  $message = array(
    'content' => _x( "New chapter published!\n_ _", 'Discord message for new chapter.', 'fictioneer' ),
    'embeds' => array(
      array(
        'title' => html_entity_decode( get_the_title( $post ) ),
        'description' => html_entity_decode( get_the_excerpt( $post ) ),
        'url' => get_permalink( $post->ID ),
        'color' => FICTIONEER_DISCORD_EMBED_COLOR,
        'author' => array(
          'name' => get_the_author_meta( 'display_name', $post->post_author ),
          'icon_url' => get_avatar_url( $post->post_author )
        ),
        'timestamp' => get_the_date( 'c', $post )
      )
    )
  );

  // Story?
  $story_id = fictioneer_get_chapter_story_id( $post->ID );
  $story_status = get_post_status( $story_id );
  $story_title = get_the_title( $story_id );
  $story_url = get_permalink( $story_id );

  if ( ! empty( $story_id ) && ! empty( $story_title ) && ! empty( $story_url ) && $story_status === 'publish' ) {
    // Change message to include story
    $message['content'] = sprintf(
      _x( "New chapter published for [%s](%s)!\n_ _", 'Discord message for new chapter.', 'fictioneer' ),
      html_entity_decode( $story_title ),
      $story_url
    );

    // Add footer
    $message['embeds'][0]['footer'] = array(
      'text' => sprintf(
        _x( 'Story: %s', 'Discord message story footer note.', 'fictioneer' ),
        html_entity_decode( $story_title )
      )
    );
  }

  // Thumbnail?
  $thumbnail_url = get_the_post_thumbnail_url( $post, 'thumbnail' );
  $thumbnail_url = empty( $thumbnail_url ) ? get_the_post_thumbnail_url( $story_id, 'thumbnail' ) : $thumbnail_url;

  if ( ! empty( $thumbnail_url ) ) {
    $message['embeds'][0]['thumbnail'] = array(
      'url' => $thumbnail_url
    );
  }

  // Filter
  $message = apply_filters( 'fictioneer_filter_discord_chapter_message', $message, $post, $story_id );
  $webhook = apply_filters(
    'fictioneer_filter_discord_chapter_webhook',
    get_option( 'fictioneer_discord_channel_chapters_webhook' ),
    $post,
    $story_id
  );

  // Send to Discord
  fictioneer_discord_send_message( $webhook, $message );

  // Remember trigger
  update_post_meta( $post_id, 'fictioneer_discord_post_trigger', true );

  // Unhook if done to avoid additional triggers (if any)
  remove_action( 'save_post', 'fictioneer_post_chapter_to_discord', 99 );
}

if ( get_option( 'fictioneer_discord_channel_chapters_webhook' ) ) {
  add_action( 'save_post', 'fictioneer_post_chapter_to_discord', 99, 3 );
}

// =============================================================================
// DEV: DISCORD NOTICE (LOL)
// =============================================================================

// function fictioneer_post_notice_to_discord( $notice, $webhook = null ) {
//   // Message
//   $message = array(
//     'content' => html_entity_decode( $notice )
//   );

//   // Webhook
//   if ( empty( $webhook ) ) {
//     $webhook = get_option( 'fictioneer_discord_channel_comments_webhook' );
//   }

//   // Send to Discord
//   fictioneer_discord_send_message( $webhook, $message );
// }
