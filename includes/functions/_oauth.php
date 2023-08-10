<?php

// =============================================================================
// CONSTANTS
// =============================================================================

define(
  'FICIONEER_OAUTH_API_ENDPOINTS',
  array(
    'discord' => array(
      'login' => 'https://discord.com/api/oauth2/authorize',
      'token' => 'https://discord.com/api/oauth2/token',
      'user' => 'https://discord.com/api/users/@me',
      'revoke' => 'https://discord.com/api/oauth2/token/revoke',
      'scope' => 'identify email'
    ),
    'twitch' => array(
      'login' => 'https://id.twitch.tv/oauth2/authorize',
      'token' => 'https://id.twitch.tv/oauth2/token',
      'user' => 'https://api.twitch.tv/helix/users',
      'revoke' => 'https://id.twitch.tv/oauth2/revoke',
      'scope' => 'user:read:email'
    ),
    'google' => array(
      'login' => 'https://accounts.google.com/o/oauth2/auth',
      'token' => 'https://oauth2.googleapis.com/token',
      'user' => 'https://www.googleapis.com/oauth2/v1/userinfo',
      'revoke' => 'https://oauth2.googleapis.com/revoke',
      'scope' => 'openid https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile'
    ),
    'patreon' => array(
      'login' => 'https://www.patreon.com/oauth2/authorize',
      'token' => 'https://www.patreon.com/api/oauth2/token',
      'user' => 'https://www.patreon.com/api/oauth2/v2/identity',
      'revoke' => '',
      'scope' => urlencode('identity identity[email]')
    )
  )
);

// =============================================================================
// SETUP
// =============================================================================

/**
 * Add route to OAuth script
 *
 * @since Fictioneer 4.0
 */

function fictioneer_add_oauth2_endpoint() {
  add_rewrite_endpoint( FICTIONEER_OAUTH_ENDPOINT, EP_ROOT );
}

if ( get_option( 'fictioneer_enable_oauth' ) ) {
  add_action( 'init', 'fictioneer_add_oauth2_endpoint', 10 );
}

// =============================================================================
// GET OAUTH LOGIN LINK
// =============================================================================

if ( ! function_exists( 'fictioneer_get_oauth_login_link' ) ) {
  /**
   * Returns an OAuth login link for the given channel
   *
   * The login link will be escaped and returned with nonce, which is only relevant
   * for the first call. Subsequent calls follow the OAuth protocol for security.
   * If the OAuth for the channel is not properly set up or the feature disabled,
   * an empty string will be returned instead.
   *
   * @since Fictioneer 4.7
   *
   * @param string       $channel  The channel (discord, google, twitch, or patreon).
   * @param string       $content  Content of the link.
   * @param string|false $anchor   Optional. An anchor for the return page. Default false.
   * @param boolean      $merge    Optional. Whether to link the account to another.
   * @param string       $classes  Optional. Additional CSS classes.
   *
   * @return string OAuth login link or empty string if disabled.
   */

  function fictioneer_get_oauth_login_link( $channel, $content, $anchor = false, $merge = false, $classes = '', $post_id = 0, $return_to = false ) {
    // Return empty string if...
    if (
      ! get_option( 'fictioneer_' . $channel . '_client_id' ) ||
      ! get_option( 'fictioneer_' . $channel . '_client_secret' ) ||
      ! get_option( 'fictioneer_enable_oauth' )
    ) {
      return '';
    }

    // Setup
    $anchor = $anchor ? '&anchor=' . $anchor : '';
    $merge = $merge ? '&merge=1' : '';
    $return_to = $return_to ? $return_to : get_permalink( $post_id );

    // Build URL
    $url = esc_url( wp_nonce_url( get_site_url() . '/' . FICTIONEER_OAUTH_ENDPOINT . '/?action=login&return_url=' . urlencode( $return_to ) . '&channel=' . $channel . $anchor . $merge, 'authenticate', 'oauth_nonce' ) );

    // Return HTML link
    return '<a href="' . $url . '" class="oauth-login-link _' . $channel . ' ' . $classes . '" rel="noopener noreferrer nofollow">' . $content . '</a>';
  }
}

if ( ! function_exists( 'fictioneer_get_oauth_links' ) ) {
  /**
   * Returns OAuth links for all channels
   *
   * @since Fictioneer 4.7
   *
   * @param boolean|string $label   Optional. Whether to show the channel as
   *                                label and the text before that (if any).
   *                                Can be false, true, or 'some string'.
   * @param string         $classes Optional. Additional CSS classes.
   * @param boolean|string $anchor  Optional. Anchor to append to the URL.
   *
   * @return string Sequence of links or empty string if OAuth is disabled.
   */

  function fictioneer_get_oauth_links( $label = false, $classes = '', $anchor = false, $post_id = null ) {
    // Setup
    $output = '';
    $channels = array(
      ['discord', __( 'Discord', 'fictioneer' )],
      ['twitch', __( 'Twitch', 'fictioneer' )],
      ['google', __( 'Google', 'fictioneer' )],
      ['patreon', __( 'Patreon', 'fictioneer' )]
    );

    // Build link for each channel
    if ( get_option( 'fictioneer_enable_oauth' ) ) {
      foreach ( $channels as $channel ) {
        // Prefixed label text (if any)
        $label_text = is_string( $label ) ? $label . ' ' : '';

        // Label after the icon (if any)
        $oauth_label = $label ? '<span>' . $label_text . $channel[1] . '</span>' : '';

        // Build link
        $output .= fictioneer_get_oauth_login_link(
          $channel[0],
          '<i class="fa-brands fa-' . $channel[0] . '"></i>' . $oauth_label,
          $anchor,
          false,
          $classes,
          $post_id
        );
      }
    }

    // Return link sequence as HTML
    return $output;
  }
}

// =============================================================================
// PROCESS
// =============================================================================

/**
 * Handle OAuth2 requests made to the OAuth2 route
 *
 * After an initial validation, delegates to the current OAuth2 steps based on
 * the action (GET) and session variables. Each provider differs a bit, but the
 * protocol is the same. First, you have to request a CODE with the pre-defined
 * permissions (SCOPE) that requires user confirmation. This is secured with a
 * cryptic key (STATE). Once you got the CODE, you need to make another request
 * for a TOKEN, which is then used to make yet another request for the user data
 * to authenticate the user. The connection is severed after that.
 *
 * @since Fictioneer 4.0
 * @link https://developer.wordpress.org/reference/hooks/template_redirect/
 * @link https://dev.twitch.tv/docs/authentication
 * @link https://discord.com/developers/docs/topics/oauth2
 * @link https://docs.patreon.com/#step-1-registering-your-client
 * @link https://developers.google.com/identity/protocols/oauth2
 */

function fictioneer_handle_oauth() {
  // Check whether this is the OAuth2 route
  if ( is_null( get_query_var( FICTIONEER_OAUTH_ENDPOINT, null ) ) ) return;

  // Setup
  fictioneer_set_oauth_constants();
  $note = '';

  // Check nonce for initial call but not once a STATE is set
  if (
    ! STATE &&
    (
      ! isset( $_GET['oauth_nonce'] ) ||
      ! wp_verify_nonce( $_GET['oauth_nonce'], 'authenticate' )
    )
  ) {
    fictioneer_oauth2_exit_and_return();
  }

  // Exit on error
  if ( ERROR ) fictioneer_oauth2_exit_and_return();

  // Logout
  if ( ACTION === 'logout' ) {
    wp_logout();
    fictioneer_oauth2_exit_and_return( ANCHOR ? RETURN_URL . '#' . ANCHOR : RETURN_URL );
  }

  // Check whether oauth is working
  if (
    ! get_option( 'fictioneer_enable_oauth' ) ||
    ! OAUTH2_CLIENT_ID ||
    ! OAUTH2_CLIENT_SECRET
  ) {
    fictioneer_oauth2_exit_and_return();
  }

  // Check for MERGE if user is not logged in
  if ( ! is_user_logged_in() && MERGE == '1' ) {
    fictioneer_oauth2_exit_and_return();
  }

  // Check for MERGE if user is already logged in
  if (
    is_user_logged_in() &&
    (
      MERGE != '1' &&
      ! isset( $_SESSION['current_user_id'] )
    )
  ) {
    fictioneer_oauth2_exit_and_return();
  }

  // Get code
  if ( ACTION === 'login' ) {
    fictioneer_get_oauth_code();
  }

  // Security check
  if ( ! STATE || $_SESSION['state'] != STATE ) fictioneer_oauth2_exit_and_return();

  // Get token and delegate to login/register
  if ( ! empty( CODE ) ) {
    $token = fictioneer_get_oauth_token(
      FICIONEER_OAUTH_API_ENDPOINTS[CHANNEL]['token'],
      array(
        'grant_type' => 'authorization_code',
        'client_id' => OAUTH2_CLIENT_ID,
        'client_secret' => OAUTH2_CLIENT_SECRET,
        'redirect_uri' => REDIRECT_URL,
        'code' => CODE,
        'scope' => FICIONEER_OAUTH_API_ENDPOINTS[CHANNEL]['scope']
      )
    );

    // Access token successfully retrieved?
    $access_token = isset( $token->access_token ) ? $token->access_token : null;
    if ( ! $access_token ) fictioneer_oauth2_exit_and_return();

    // Delegate to respective channel function
    switch ( CHANNEL ) {
      case 'discord':
        $note = fictioneer_process_oauth_discord( FICIONEER_OAUTH_API_ENDPOINTS[CHANNEL]['user'], $access_token );
        break;
      case 'twitch':
        $note = fictioneer_process_oauth_twitch( FICIONEER_OAUTH_API_ENDPOINTS[CHANNEL]['user'], $access_token );
        break;
      case 'google':
        $note = fictioneer_process_oauth_google( FICIONEER_OAUTH_API_ENDPOINTS[CHANNEL]['user'], $access_token );
        break;
      case 'patreon':
        $note = fictioneer_process_oauth_patreon( FICIONEER_OAUTH_API_ENDPOINTS[CHANNEL]['user'], $access_token );
        break;
    }

    // Error: Email already taken by another account
    if ( ! is_user_logged_in() ) {
      $return_url = ( $_SESSION['return_url'] ) ? urldecode( $_SESSION['return_url'] ) : RETURN_URL;
      $return_url .= '?failure=' . $note;
      $return_url = ( $_SESSION['anchor'] ) ? $return_url . '#' . $_SESSION['anchor'] : $return_url;
      fictioneer_oauth2_exit_and_return( $return_url );
    }

    // Success: Merged into account
    if ( $note == 'merged' ) {
      $return_url = ( $_SESSION['return_url'] ) ? urldecode( $_SESSION['return_url'] ) : RETURN_URL;
      $return_url .= '?success=oauth_merged_' . CHANNEL;
      $return_url = ( $_SESSION['anchor'] ) ? $return_url . '#' . $_SESSION['anchor'] : $return_url;
      fictioneer_oauth2_exit_and_return( $return_url );
    }

    // Success: New user registered
    if ( $note == 'new' ) {
      $return_url = ( $_SESSION['return_url'] ) ? urldecode( $_SESSION['return_url'] ) : RETURN_URL;
      $return_url .= '?success=oauth_new_subscriber';
      $return_url = ( $_SESSION['anchor'] ) ? $return_url . '#' . $_SESSION['anchor'] : $return_url;
      fictioneer_oauth2_exit_and_return( $return_url );
    }

    // Finish
    fictioneer_oauth2_exit_and_return();
  }
}
add_action( 'template_redirect', 'fictioneer_handle_oauth', 10 );

// =============================================================================
// DISCORD
// =============================================================================

if ( ! function_exists( 'fictioneer_process_oauth_discord' ) ) {
  /**
   * Retrieve user from Discord
   *
   * @since Fictioneer 4.0
   * @see fictioneer_make_oauth_user()
   *
   * @param string $url          The request URL.
   * @param string $access_token The access token.
   *
   * @return string Passed through result of fictioneer_make_oauth_user().
   */

  function fictioneer_process_oauth_discord( string $url, string $access_token ) {
    // Retrieve user data from Discord
    $response = wp_remote_get(
      $url,
      array(
        'headers' => array(
          'Authorization' => 'Bearer ' . $access_token,
          'Client-ID' => OAUTH2_CLIENT_ID,
        )
      )
    );

    if ( is_wp_error( $response ) ) {
      fictioneer_oauth_die( $response->get_error_message() );
    } else {
      $user = json_decode( wp_remote_retrieve_body( $response ) );
    }

    // User data successfully retrieved?
    if ( ! isset( $user ) ) {
      fictioneer_oauth_die( wp_remote_retrieve_body( $response ) );
    }

    if ( ! isset( $user->verified ) || ! $user->verified ) {
      fictioneer_oauth_die( 'Account not verified.' );
    }

    // Login or register user; note may be 'new', 'known', or 'error'
    $note = fictioneer_make_oauth_user(
      array(
        'uid' => $user->id,
        'avatar' => esc_url_raw( "https://cdn.discordapp.com/avatars/{$user->id}/{$user->avatar}.png" ),
        'channel' => 'discord',
        'email' => $user->email,
        'username' => $user->username . ( $user->discriminator ?? ''),
        'nickname' => $user->username
      )
    );

    // Revoke token since it's not needed anymore
    fictioneer_revoke_oauth_token(
      FICIONEER_OAUTH_API_ENDPOINTS['discord']['revoke'],
      array(
        'token' => $access_token,
        'token_type_hint' => 'access_token',
        'client_id' => OAUTH2_CLIENT_ID,
        'client_secret' => OAUTH2_CLIENT_SECRET
      )
    );

    // Return note for notification purposes
    return $note;
  }
}

// =============================================================================
// TWITCH
// =============================================================================

if ( ! function_exists( 'fictioneer_process_oauth_twitch' ) ) {
  /**
   * Retrieve user from Twitch
   *
   * @since Fictioneer 4.0
   * @see fictioneer_make_oauth_user()
   *
   * @param string $url          The request URL.
   * @param string $access_token The access token.
   *
   * @return string Passed through result of fictioneer_make_oauth_user().
   */

  function fictioneer_process_oauth_twitch( string $url, string $access_token ) {
    // Retrieve user data from Twitch
    $response = wp_remote_get(
      $url,
      array(
        'headers' => array(
          'Authorization' => 'Bearer ' . $access_token,
          'Client-ID' => OAUTH2_CLIENT_ID,
        )
      )
    );

    if ( is_wp_error( $response ) ) {
      fictioneer_oauth_die( $response->get_error_message() );
    } else {
      $user = json_decode( wp_remote_retrieve_body( $response ) );
    }

    // User data successfully retrieved?
    if ( empty( $user ) ) {
      fictioneer_oauth_die( wp_remote_retrieve_body( $response ) );
    }

    // Login or register user; note may be 'new', 'known', or 'error'
    $note = fictioneer_make_oauth_user(
      array(
        'uid' => $user->data[0]->id,
        'avatar' => esc_url_raw( $user->data[0]->profile_image_url ?? '' ),
        'channel' => 'twitch',
        'email' => $user->data[0]->email,
        'username' => $user->data[0]->login,
        'nickname' => $user->data[0]->display_name
      )
    );

    // Revoke token since it's not needed anymore
    fictioneer_revoke_oauth_token(
      FICIONEER_OAUTH_API_ENDPOINTS['twitch']['revoke'],
      array(
        'token' => $access_token,
        'token_type_hint' => 'access_token',
        'client_id' => OAUTH2_CLIENT_ID
      )
    );

    // Return note for notification purposes
    return $note;
  }
}

// =============================================================================
// GOOGLE
// =============================================================================

if ( ! function_exists( 'fictioneer_process_oauth_google' ) ) {
  /**
   * Retrieve user from Google
   *
   * @since Fictioneer 4.0
   * @see fictioneer_make_oauth_user()
   *
   * @param string $url          The request URL.
   * @param string $access_token The access token.
   *
   * @return string Passed through result of fictioneer_make_oauth_user().
   */

  function fictioneer_process_oauth_google( string $url, string $access_token ) {
    // Retrieve user data from Google
    $response = wp_remote_get(
      $url,
      array(
        'headers' => array(
          'Authorization' => 'Bearer ' . $access_token,
          'Client-ID' => OAUTH2_CLIENT_ID,
        )
      )
    );

    if ( is_wp_error( $response ) ) {
      fictioneer_oauth_die( $response->get_error_message() );
    } else {
      $user = json_decode( wp_remote_retrieve_body( $response ) );
    }

    // User data successfully retrieved?
    if ( ! isset( $user ) ) {
      fictioneer_oauth_die( wp_remote_retrieve_body( $response ) );
    }

    if ( ! isset( $user->verified_email ) || ! $user->verified_email ) {
      fictioneer_oauth_die( 'Email not verified.' );
    }

    // Login or register user; note may be 'new', 'merged', 'known', or an error code
    $note = fictioneer_make_oauth_user(
      array(
        'uid' => $user->id,
        'avatar' => esc_url_raw( $user->picture ?? '' ),
        'channel' => 'google',
        'email' => $user->email,
        'username' => $user->name,
        'nickname' => $user->name
      )
    );

    // Revoke token since it's not needed anymore
    fictioneer_revoke_oauth_token(
      FICIONEER_OAUTH_API_ENDPOINTS['google']['revoke'],
      array(
        'token' => $access_token
      )
    );

    // Return note for notification purposes
    return $note;
  }
}

// =============================================================================
// PATREON
// =============================================================================

if ( ! function_exists( 'fictioneer_process_oauth_patreon' ) ) {
  /**
   * Retrieve user from Patreon
   *
   * @since Fictioneer 4.0
   * @see fictioneer_make_oauth_user()
   *
   * @param string $url          The request URL.
   * @param string $access_token The access token.
   *
   * @return string Passed through result of fictioneer_make_oauth_user().
   */

  function fictioneer_process_oauth_patreon( string $url, string $access_token ) {
    // Build params
    $params = '?fields' . urlencode('[user]') . '=email,first_name,image_url,is_email_verified';
    $params .= '&fields' . urlencode('[tier]') . '=title';
    $params .= '&include=memberships.currently_entitled_tiers';

    // Retrieve user data from Patreon
    $response = wp_remote_get(
      $url . $params,
      array(
        'headers' => array(
          'Authorization' => 'Bearer ' . $access_token
        )
      )
    );

    if ( is_wp_error( $response ) ) {
      fictioneer_oauth_die( $response->get_error_message() );
    } else {
      $user = json_decode( wp_remote_retrieve_body( $response ) );
    }

    // User data successfully retrieved?
    if ( ! isset( $user ) ) {
      fictioneer_oauth_die( wp_remote_retrieve_body( $response ) );
    }

    if ( ! isset( $user->data ) ) {
      fictioneer_oauth_die( 'Data node not found.' );
    }

    if ( ! isset( $user->data->attributes ) ) {
      fictioneer_oauth_die( 'Attributes node not found.' );
    }

    if ( ! isset( $user->data->attributes->is_email_verified ) || ! $user->data->attributes->is_email_verified ) {
      fictioneer_oauth_die( 'Email not verified.' );
    }

    // Find Patreon tiers if any
    $tiers = [];

    if ( isset( $user->included ) ) {
      foreach( $user->included as $node ) {
        if ( isset( $node->type ) && $node->type == 'tier' && isset( $node->attributes->title ) ) {
          $tiers[] = array(
            'tier' => sanitize_text_field( $node->attributes->title ),
            'timestamp' => time(),
            'id' => $node->id
          );
        }
      }
    }

    $args = array(
      'uid' => $user->data->id,
      'avatar' => esc_url_raw( $user->data->attributes->image_url ?? '' ),
      'channel' => 'patreon',
      'email' => $user->data->attributes->email,
      'username' => $user->data->attributes->first_name,
      'nickname' => $user->data->attributes->first_name,
      'tiers' => $tiers
    );

    /* Patreon does not have a function to revoke a token (2022/07/29). */

    // Login or register user; return 'new', 'known', or 'error'
    return fictioneer_make_oauth_user( $args );
  }
}

// =============================================================================
// CREATE/LOG IN USER
// =============================================================================

if ( ! function_exists( 'fictioneer_make_oauth_user' ) ) {
  /**
   * Log in or register user
   *
   * @since Fictioneer 4.0
   *
   * @param array $args Array of user data.
   *
   * @return string Returns either 'new', 'merged', 'known', or an error code.
   */

  function fictioneer_make_oauth_user( array $args ) {
    // Setup
    $meta_key = "fictioneer_{$args['channel']}_id_hash";
    $channel_id = 'fcn_' . hash( 'sha256', sanitize_key( $args['uid'] ) ); // Only for obfuscation, not critical data
    $username = sanitize_user( $args['username'] );
    $alt_username = false;
    $email = sanitize_email( $args['email'] );
    $avatar = fictioneer_url_exists( $args['avatar'] ) ? $args['avatar'] : null;
    $new = false;

    // Look if user already exists via channel id...
    $wp_user = get_users(
      array(
        'meta_key' => $meta_key,
        'meta_value' => $channel_id,
        'number' => 1
      )
    );
    $wp_user = reset( $wp_user );

    // If no user has been found, find a valid username
    if ( ! $wp_user && username_exists( $username ) ) {
      $alt_username = $username;
      $discriminator = 1;

      // Find alternative name if duplicate exists
      while ( username_exists( $alt_username ) ) {
        $alt_username = $username . $discriminator;
        $discriminator++;
      }
    }

    // When the channel ID is not yet linked and a merge was requested...
    if ( is_user_logged_in() && defined( 'MERGE_ID' ) && ! $wp_user ) {
      $wp_user = get_user_by( 'id', MERGE_ID );
    }

    // Cannot merge with non-existent user (not found after first try)...
    if ( is_user_logged_in() && defined( 'MERGE_ID' ) && ! $wp_user ) {
      fictioneer_oauth2_exit_and_return();
    }

    // When the channel ID is already linked and a merge was requested...
    if ( is_user_logged_in() && defined( 'MERGE_ID' ) && MERGE_ID != $wp_user->ID ) {
      fictioneer_oauth2_exit_and_return( RETURN_URL . '?failure=oauth_already_linked#oauth-connections' );
    }

    // ... if not found, create a new user and get the object if successful
    if ( ! $wp_user ) {
      $wp_user_id = wp_create_user(
        $alt_username ? $alt_username : $username,
        wp_generate_password( 32, true, true ),
        $email
      );

      if ( is_wp_error( $wp_user_id ) ) {
        $error_key = key( $wp_user_id->errors );
        return $error_key == 'existing_user_email' ? 'oauth_email_taken' : $error_key;
      } else {
        $wp_user = get_user_by( 'id', $wp_user_id );
        $new = true;
      }
    }

    // ... if user has been found or created, update user data
    if ( $wp_user ) {
      // Current avatar
      $current_avatar = empty( $wp_user->fictioneer_external_avatar_url ) ? null : $wp_user->fictioneer_external_avatar_url;

      // Nice name, and hide admin bar for new subscribers
      if ( $new ) {
        update_user_meta( $wp_user->ID, 'nickname', $args['nickname'] );
        wp_update_user( array( 'ID' => $wp_user->ID, 'display_name' => $args['nickname'] ) );
      }

      // Channel ID for new users and merged accounts
      if ( $new || defined( 'MERGE_ID' ) ) {
        update_user_meta( $wp_user->ID, "fictioneer_{$args['channel']}_id_hash", $channel_id );
      }

      // Change avatar if updated or empty
      if ( $avatar && $avatar != $current_avatar ) {
        update_user_meta( $wp_user->ID, 'fictioneer_external_avatar_url', $avatar );
      }

      if ( isset( $args['tiers'] ) && count( $args['tiers'] ) > 0 ) {
        update_user_meta( $wp_user->ID, 'fictioneer_patreon_tiers', $args['tiers'] );
      }

      // Login
      if ( ! is_user_logged_in() ) {
        wp_clear_auth_cookie();
        wp_set_current_user( $wp_user->ID );

        // Allow login to last three days
        add_filter( 'auth_cookie_expiration', function( $length ) {
          return 3 * DAY_IN_SECONDS;
        });

        // Set authentication cookie
        wp_set_auth_cookie( $wp_user->ID, true );
      }

      // Return whether this is a new or known user
      if ( is_user_logged_in() ) {
        if ( $new ) return 'new';
        if ( defined( 'MERGE_ID' ) ) return 'merged';
        return 'known';
      }
    }

    // Something went very wrong
    return 'unknown_error';
  }
}

// =============================================================================
// HELPERS
// =============================================================================

if ( ! function_exists( 'fictioneer_oauth_die' ) ) {
  /**
   * Outputs a formatted error message and stops script execution
   *
   * @since Fictioneer 5.5.2
   *
   * @param string $message  The error message.
   * @param string $title    Optional. Title of the error page. Default 'Error'.
   */

  function fictioneer_oauth_die( $message, $title = 'Error' ) {
    wp_die(
      '<h1 style="margin-top: 0;">' . $title . '</h1>' .
      '<p><pre>' . print_r( $message, true ) . '</pre></p>' .
      '<p>The good news is, nothing has happened to your account. The bad new is, something is not working. Please try again later or contact an administrator for help. <a href="' . RETURN_URL . '">Back to site</a></p>',
      $title
    );
  }
}

if ( ! function_exists( 'fictioneer_set_oauth_constants' ) ) {
  /**
   * Set up all constants
   *
   * @since Fictioneer 4.0
   */

  function fictioneer_set_oauth_constants() {
    // Setup
    $redirect_url = get_site_url( null, FICTIONEER_OAUTH_ENDPOINT );
    $action = array_key_exists( 'action', $_GET ) ? sanitize_text_field( $_GET['action'] ) : NULL;
    $code = array_key_exists( 'code', $_GET ) ? sanitize_text_field( $_GET['code'] ) : NULL;
    $state = array_key_exists( 'state', $_GET ) ? sanitize_text_field( $_GET['state'] ) : NULL;
    $return_url = array_key_exists( 'return_url', $_GET ) ? esc_url_raw( $_GET['return_url'] ) : home_url();
    $anchor = array_key_exists( 'anchor', $_GET ) ? sanitize_text_field( $_GET['anchor'] ) : NULL;
    $channel = array_key_exists( 'channel', $_GET ) ? sanitize_text_field( $_GET['channel'] ) : NULL;
    $error = array_key_exists( 'error_description', $_GET ) ? sanitize_text_field( $_GET['error_description'] ) : NULL;
    $merge = array_key_exists( 'merge', $_GET ) ? sanitize_text_field( $_GET['merge'] ) : NULL;

    // Session variables
    if ( ! session_id() ) session_start();

    if ( isset( $_SESSION['channel'] ) ) {
      $channel = $channel ? $channel : $_SESSION['channel'];
    }

    if ( isset( $_SESSION['return_url'] ) && $_SESSION['return_url'] != NULL ) {
      $return_url = urldecode( $_SESSION['return_url'] );
    }

    if ( isset( $_SESSION['anchor'] ) && $_SESSION['anchor'] != NULL ) {
      $return_url = $return_url . '#' . $_SESSION['anchor'];
    }

    if ( isset( $_SESSION['current_user_id'] ) && $_SESSION['current_user_id'] != NULL ) {
      define( 'MERGE_ID', $_SESSION['current_user_id'] );
    }

    // Define constants
    define( 'OAUTH2_CLIENT_ID', fictioneer_get_oauth_client_credentials( $channel ) );
    define( 'OAUTH2_CLIENT_SECRET', fictioneer_get_oauth_client_credentials( $channel, 'secret' ) );
    define( 'REDIRECT_URL', $redirect_url );
    define( 'ACTION', $action );
    define( 'CODE', $code );
    define( 'STATE', $state );
    define( 'RETURN_URL', $return_url );
    define( 'ANCHOR', $anchor );
    define( 'CHANNEL', $channel );
    define( 'ERROR', $error );
    define( 'MERGE', $merge );
  }
}

if ( ! function_exists( 'fictioneer_oauth2_exit_and_return' ) ) {
  /**
   * Terminate the script and redirect back
   *
   * I'm sure there was a reason doing it like this.
   *
   * @since Fictioneer 4.0
   *
   * @param string $return_url Optional. URL to return to.
   */

  function fictioneer_oauth2_exit_and_return( $return_url = RETURN_URL ) {
    if ( ! session_id() ) session_start();
    setcookie( 'PHPSESSID', '', time() - 3600, '/' );
    session_destroy();
    wp_safe_redirect( $return_url );
    die();
  }
}

if ( ! function_exists( 'fictioneer_get_oauth_client_credentials' ) ) {
  /**
   * Return credentials for the given channel
   *
   * @since Fictioneer 4.0
   *
   * @param string $channel The channel to retrieve the credential for.
   * @param string $type    Optional. The type of credential to retrieve,
   *                        'id' or 'secret'. Default 'id'.
   *
   * @return string|null The client ID or secret, or null if not found.
   */

  function fictioneer_get_oauth_client_credentials( $channel, $type = 'id' ) {
    if ( ! $channel ) return null;
    $it = get_option( "fictioneer_{$channel}_client_{$type}" );
    return $it ? $it : null;
  }
}

if ( ! function_exists( 'fictioneer_get_oauth_token' ) ) {
  /**
   * Get the OAuth2 access token
   *
   * @since Fictioneer 4.0
   *
   * @param string $url     URL to make the API request to.
   * @param array  $post    Post query.
   * @param array  $headers Array of header options.
   *
   * @return object Decoded JSON result.
   */

  function fictioneer_get_oauth_token( $url, $post, $headers = [] ) {
    $curl = curl_init( $url );
    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $curl, CURLOPT_POST, true );
    curl_setopt( $curl, CURLOPT_POSTFIELDS, http_build_query( $post ) );

    $headers[] = 'Accept: application/json';

    curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );

    $result = curl_exec( $curl );
    curl_close( $curl );

    return json_decode( $result );
  }
}

if ( ! function_exists( 'fictioneer_revoke_oauth_token' ) ) {
  /**
   * Revoke an OAuth2 access token
   *
   * @since Fictioneer 4.0
   *
   * @param string $url     URL to make the API request to.
   * @param array  $post    Post query.
   *
   * @return string HTTP response code.
   */

  function fictioneer_revoke_oauth_token( $url, $post = [] ) {
    $curl = curl_init( $url );
    curl_setopt_array( $curl, array(
      CURLOPT_POST => TRUE,
      CURLOPT_RETURNTRANSFER => TRUE,
      CURLOPT_HTTPHEADER => array( 'Content-Type: application/x-www-form-urlencoded' ),
      CURLOPT_POSTFIELDS => http_build_query( $post ),
      CURLOPT_HEADER => TRUE,
    ));

    $result = curl_exec( $curl );
    $httpcode = curl_getinfo( $curl, CURLINFO_HTTP_CODE );

    curl_close( $curl );

    return $httpcode;
  }
}

if ( ! function_exists( 'fictioneer_get_oauth_code' ) ) {
  /**
   * Redirect to OAuth provider to retrieve permissions and the CODE
   *
   * @since Fictioneer 4.0
   */

  function fictioneer_get_oauth_code() {
    // Store random Hash in session for security
    $_SESSION['state'] = hash( 'sha256', microtime( TRUE ) . rand() . $_SERVER['REMOTE_ADDR'] );

    // Store return url, channel, and anchor
    $_SESSION['return_url'] = RETURN_URL;
    $_SESSION['channel'] = CHANNEL;
    $_SESSION['anchor'] = ANCHOR;

    // Add another OAuth channel to existing account?
    if ( MERGE === '1' && is_user_logged_in() ) {
      $_SESSION['current_user_id'] = get_current_user_id();
    }

    // Redirect to provider
    header( 'Location:' . FICIONEER_OAUTH_API_ENDPOINTS[CHANNEL]['login']
      . '?response_type=code'
      . '&client_id=' . OAUTH2_CLIENT_ID
      . '&redirect_uri=' . REDIRECT_URL
      . '&scope=' . FICIONEER_OAUTH_API_ENDPOINTS[CHANNEL]['scope']
      . '&state=' . $_SESSION['state']
      . '&force_verify=true'
      . '&prompt=consent'
      . '&access_type=offline'
    );

    die();
  }
}

?>
