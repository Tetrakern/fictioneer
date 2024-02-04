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
   * @param array|WP_Error  The response or WP_Error on failure.
   */

  function fictioneer_discord_send_message( $webhook, $message ) {
    return wp_remote_post(
      $webhook,
      array(
        'headers' => array(
          'Content-Type' => 'application/json'
        ),
        'body' => json_encode( $message, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ),
        'method' => 'POST',
        'data_format' => 'body'
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
  $comment_type = ucfirst( get_comment_type( $comment_id ) );
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
        'color' => $comment_status == 'approved' ? '9692513' : '14112322',
        'fields' => array(
          array(
            'name' => _x( 'Status', 'Discord message "Status" field.', 'fictioneer' ),
            'value' => ucfirst( $comment_status ),
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
          'icon_url' => $comment_avatar_url
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
  $story_id = get_post_meta( $post->ID, 'fictioneer_chapter_story', true );

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
    'value' => $comment_type,
    'inline' => true
  );

  // Send to Discord
  fictioneer_discord_send_message( get_option( 'fictioneer_discord_channel_comments_webhook' ), $message );
}

if ( ! empty( get_option( 'fictioneer_discord_channel_comments_webhook' ) ) ) {
  add_action( 'comment_post', 'fictioneer_post_comment_to_discord', 99, 2 );
}

// =============================================================================
// POST NEW STORY TO DISCORD
// =============================================================================

/**
 * Sends a notification to Discord when a new story is first published
 *
 * @since 5.6.0
 *
 * @param string $post_id  The post ID.
 */

function fictioneer_post_story_to_discord( $post_id ) {
  // Prevent multi-fire
  if ( fictioneer_multi_save_guard( $post_id ) ) {
    return;
  }

  // Setup
  $post = get_post( $post_id );

  // Published chapter?
  if ( $post->post_type !== 'fcn_story' || $post->post_status !== 'publish' ) {
    return;
  }

  // Older than n (30) seconds? The $update param is unreliable with block editor.
  $publishing_time = get_post_time( 'U', true, $post_id );
  $current_time = current_time( 'timestamp', true );

  if ( $current_time - $publishing_time > FICTIONEER_OLD_POST_THRESHOLD ) {
    return;
  }

  // Already triggered once?
  if ( ! empty( get_post_meta( $post_id, 'fictioneer_discord_post_trigger', true ) ) ) {
    return;
  }

  // Remove story webhook to prevent miss-fire
  remove_action( 'save_post', 'fictioneer_post_chapter_to_discord', 99 );

  // Data
  $title = html_entity_decode( get_the_title( $post ) );
  $url = get_permalink( $post_id );

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
        'url' => get_permalink( $post_id ),
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

  // Send to Discord
  fictioneer_discord_send_message( get_option( 'fictioneer_discord_channel_stories_webhook' ), $message );

  // Set trigger true
  update_post_meta( $post->ID, 'fictioneer_discord_post_trigger', true );
}

if ( ! empty( get_option( 'fictioneer_discord_channel_stories_webhook' ) ) ) {
  add_action( 'save_post', 'fictioneer_post_story_to_discord', 99 );
}

// =============================================================================
// POST NEW CHAPTER TO DISCORD
// =============================================================================

/**
 * Sends a notification to Discord when a new chapter is first published
 *
 * @since 5.6.0
 *
 * @param string $post_id  The post ID.
 */

function fictioneer_post_chapter_to_discord( $post_id ) {
  // Prevent multi-fire
  if ( fictioneer_multi_save_guard( $post_id ) ) {
    return;
  }

  // Setup
  $post = get_post( $post_id );

  // Published chapter?
  if ( $post->post_type !== 'fcn_chapter' || $post->post_status !== 'publish' ) {
    return;
  }

  // Older than n (30) seconds? The $update param is unreliable with block editor.
  $publishing_time = get_post_time( 'U', true, $post_id );
  $current_time = current_time( 'timestamp', true );

  if ( $current_time - $publishing_time > FICTIONEER_OLD_POST_THRESHOLD ) {
    return;
  }

  // Already triggered once?
  if ( ! empty( get_post_meta( $post_id, 'fictioneer_discord_post_trigger', true ) ) ) {
    return;
  }

  // Remove story webhook to prevent miss-fire
  remove_action( 'save_post', 'fictioneer_post_story_to_discord', 99 );

  // Message
  $message = array(
    'content' => _x( "New chapter published!\n_ _", 'Discord message for new chapter.', 'fictioneer' ),
    'embeds' => array(
      array(
        'title' => html_entity_decode( get_the_title( $post ) ),
        'description' => html_entity_decode( get_the_excerpt( $post ) ),
        'url' => get_permalink( $post_id ),
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
  $story_id = get_post_meta( $post_id, 'fictioneer_chapter_story', true );
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

  // Send to Discord
  fictioneer_discord_send_message( get_option( 'fictioneer_discord_channel_chapters_webhook' ), $message );

  // Set trigger true
  update_post_meta( $post->ID, 'fictioneer_discord_post_trigger', true );
}

if ( ! empty( get_option( 'fictioneer_discord_channel_chapters_webhook' ) ) ) {
  add_action( 'save_post', 'fictioneer_post_chapter_to_discord', 99 );
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

?>
