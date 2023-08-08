<?php

// =============================================================================
// SEND MESSAGE TO DISCORD WEBHOOK
// =============================================================================

if ( ! function_exists( 'fictioneer_discord_send_message' ) ) {
  /**
   * Sends a message to a Discord channel
   *
   * @since 4.0
   *
   * @param string $webhook The webhook for the Discord channel.
   * @param array  $message The message to be sent.
   */

  function fictioneer_discord_send_message( $webhook, $message ) {
    $message = json_encode( $message, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

    $curl = curl_init( $webhook );
    curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-type: application/json' ) );
    curl_setopt( $curl, CURLOPT_POST, 1 );
    curl_setopt( $curl, CURLOPT_POSTFIELDS, $message );
    curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, 1 );
    curl_setopt( $curl, CURLOPT_HEADER, 0 );
    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );

    $output = curl_exec( $curl );
    curl_close( $curl );
  }
}

// =============================================================================
// POST COMMENT TO DISCORD
// =============================================================================

/**
 * Sends a comment as message to a Discord channel
 *
 * @since 4.0
 * @see fictioneer_discord_send_message()
 *
 * @param int        $comment_id       The comment ID.
 * @param int|string $comment_approved 1 if the comment is approved, 0 if not, 'spam' if spam.
 */

function fictioneer_post_comment_to_discord( $comment_id, $comment_approved ) {
  // Exit conditions
  if ( empty( get_option( 'fictioneer_discord_channel_comments_webhook' ) ) ) return;

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
    'username' => _x( 'Comment Stream', 'Discord message bot name.', 'fictioneer' ),
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
  $story_id = fictioneer_get_field( 'fictioneer_chapter_story', $post->ID );

  if ( $story_id ) {
    $message['embeds'][0]['footer'] = array(
      'text' => sprintf(
        _x( 'Story: %s', 'Discord message footer note.', 'fictioneer' ),
        get_the_title( $story_id )
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
add_action( 'comment_post', 'fictioneer_post_comment_to_discord', 99, 2 );

?>
