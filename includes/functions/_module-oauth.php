<?php

// =============================================================================
// CONSTANTS
// =============================================================================

define(
  'FCN_OAUTH2_API_ENDPOINTS',
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
 * @since 4.0.0
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
   * @since 4.7.0
   *
   * @param string       $channel   The channel (discord, google, twitch, or patreon).
   * @param string       $content   Content of the link.
   * @param string|false $anchor    Optional. An anchor for the return page. Default false.
   * @param boolean      $merge     Optional. Whether to link the account to another.
   * @param string       $classes   Optional. Additional CSS classes.
   *
   * @return string OAuth login link or empty string if disabled.
   */

  function fictioneer_get_oauth_login_link( $channel, $content, $anchor = false, $merge = false, $classes = '', $post_id = 0, $return_to = false ) {
    // Return empty string if...
    if (
      ! get_option( 'fictioneer_enable_oauth' ) ||
      ! get_option( 'fictioneer_' . $channel . '_client_id' ) ||
      ! get_option( 'fictioneer_' . $channel . '_client_secret' )
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
   * @since 4.7.0
   *
   * @param boolean|string $label    Optional. Whether to show the channel as
   *                                 label and the text before that (if any).
   *                                 Can be false, true, or 'some string'.
   * @param string         $classes  Optional. Additional CSS classes.
   * @param boolean|string $anchor   Optional. Anchor to append to the URL.
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
 * the action (GET) and Transient store. Each provider differs a bit, but the
 * protocol is the same. First, you have to request a code with the pre-defined
 * permissions (scope) which requires user confirmation. This is secured with a
 * cryptic key (state). Once you got the code, you need to make another request
 * for a token, which is then used to make yet another request for the user data
 * to authenticate the user. The connection is severed after that.
 *
 * @since 4.0.0
 * @since 5.7.5 - Refactored.
 * @link https://developer.wordpress.org/reference/hooks/template_redirect/
 * @link https://dev.twitch.tv/docs/authentication
 * @link https://discord.com/developers/docs/topics/oauth2
 * @link https://docs.patreon.com/#step-1-registering-your-client
 * @link https://developers.google.com/identity/protocols/oauth2
 */

function fictioneer_handle_oauth() {
  // Check whether this is the OAuth2 route
  if ( is_null( get_query_var( FICTIONEER_OAUTH_ENDPOINT, null ) ) ) {
    return;
  }

  // Setup
  fictioneer_set_oauth_constants();
  $return_url = FCN_OAUTH2_RETURN_URL;
  $note = '';

  // Check nonce for initial call but not once a state is set
  if (
    empty( FCN_OAUTH2_STATE ) &&
    ! wp_verify_nonce( $_GET['oauth_nonce'] ?? '', 'authenticate' )
  ) {
    fictioneer_oauth2_exit_and_return();
  }

  // Error?
  if ( FCN_OAUTH2_ERROR ) {
    fictioneer_oauth2_exit_and_return();
  }

  // Logout?
  if ( FCN_OAUTH2_ACTION === 'logout' ) {
    wp_logout();
    fictioneer_oauth2_exit_and_return();
  }

  // OAuth working?
  if ( ! get_option( 'fictioneer_enable_oauth' ) || ! FCN_OAUTH2_CLIENT_ID || ! FCN_OAUTH2_CLIENT_SECRET ) {
    fictioneer_oauth2_exit_and_return();
  }

  // Check for merge request if user is NOT logged in
  if ( ! is_user_logged_in() && FCN_OAUTH2_MERGE == '1' ) {
    fictioneer_oauth2_exit_and_return();
  }

  // Check for merge request if user is logged in
  if ( is_user_logged_in() && ( FCN_OAUTH2_MERGE != '1' && ! defined( 'FCN_OAUTH2_MERGE_ID' ) ) ) {
    fictioneer_oauth2_exit_and_return();
  }

  // Get code (and exit)
  if ( FCN_OAUTH2_ACTION === 'login' ) {
    fictioneer_get_oauth_code(); // Redirects and exits
  }

  // Security checks
  if ( empty( FCN_OAUTH2_STATE ) || ! defined( 'FCN_OAUTH2_TRANSIENT_STATE' ) || empty( FCN_OAUTH2_TRANSIENT_STATE ) ) {
    fictioneer_oauth2_exit_and_return();
  }

  if ( FCN_OAUTH2_STATE !== FCN_OAUTH2_TRANSIENT_STATE ) {
    fictioneer_oauth2_exit_and_return( null, FCN_OAUTH2_TRANSIENT_STATE );
  }

  if ( empty( FCN_OAUTH2_CODE ) ) {
    fictioneer_oauth2_exit_and_return( null, FCN_OAUTH2_TRANSIENT_STATE );
  }

  if ( ! defined( 'FCN_OAUTH2_COOKIE' ) || empty( FCN_OAUTH2_COOKIE ) || FCN_OAUTH2_COOKIE !== FCN_OAUTH2_TRANSIENT_COOKIE ) {
    fictioneer_oauth2_exit_and_return( null, FCN_OAUTH2_TRANSIENT_STATE );
  }

  // Get access token
  $token = fictioneer_get_oauth_token(
    FCN_OAUTH2_API_ENDPOINTS[FCN_OAUTH2_CHANNEL]['token'],
    array(
      'grant_type' => 'authorization_code',
      'client_id' => FCN_OAUTH2_CLIENT_ID,
      'client_secret' => FCN_OAUTH2_CLIENT_SECRET,
      'redirect_uri' => FCN_OAUTH2_REDIRECT_URL,
      'code' => FCN_OAUTH2_CODE,
      'scope' => FCN_OAUTH2_API_ENDPOINTS[FCN_OAUTH2_CHANNEL]['scope']
    )
  );

  // Token successfully retrieved?
  if ( is_wp_error( $token ) ) {
    fictioneer_oauth2_exit_and_return( null, FCN_OAUTH2_TRANSIENT_STATE );
  }

  // Extract token
  $access_token = isset( $token->access_token ) ? $token->access_token : null;

  if ( ! $access_token ) {
    fictioneer_oauth2_exit_and_return( null, FCN_OAUTH2_TRANSIENT_STATE );
  }

  // Delegate to respective channel function
  switch ( FCN_OAUTH2_CHANNEL ) {
    case 'discord':
      $note = fictioneer_process_oauth_discord( FCN_OAUTH2_API_ENDPOINTS['discord']['user'], $access_token );
      break;
    case 'twitch':
      $note = fictioneer_process_oauth_twitch( FCN_OAUTH2_API_ENDPOINTS['twitch']['user'], $access_token );
      break;
    case 'google':
      $note = fictioneer_process_oauth_google( FCN_OAUTH2_API_ENDPOINTS['google']['user'], $access_token );
      break;
    case 'patreon':
      $note = fictioneer_process_oauth_patreon( FCN_OAUTH2_API_ENDPOINTS['patreon']['user'], $access_token );
      break;
  }

  // Error
  if ( ! is_user_logged_in() ) {
    $return_url = add_query_arg( 'failure', $note, $return_url );
  }

  // Success: Merged
  if ( $note == 'merged' ) {
    $return_url = add_query_arg( 'success', 'oauth_merged_' . FCN_OAUTH2_CHANNEL, $return_url );
  }

  // Success: New
  if ( $note == 'new' ) {
    $return_url = add_query_arg( 'success', 'oauth_new', $return_url );
  }

  // Finish
  fictioneer_oauth2_exit_and_return( $return_url, FCN_OAUTH2_TRANSIENT_STATE );
}
add_action( 'template_redirect', 'fictioneer_handle_oauth', 10 );

/**
 * Handle OAuth2 requests to get campaign tiers
 *
 * @since 5.15.0
 */

function fictioneer_handle_get_patreon_tiers_oauth() {
  // Check whether this is the OAuth2 route
  if ( is_null( get_query_var( FICTIONEER_OAUTH_ENDPOINT, null ) ) ) {
    return;
  }

  // Setup
  $client_id = fictioneer_get_oauth_client_credentials( 'patreon' );
  $client_secret = fictioneer_get_oauth_client_credentials( 'patreon', 'secret' );
  $state = sanitize_text_field( $_GET['state'] ?? '' );
  $code = sanitize_text_field( $_GET['code'] ?? '' );
  $transient = get_transient( "fictioneer_oauth2_state_{$state}" );

  // Error?
  if ( $_GET['error_description'] ?? '' ) {
    return;
  }

  // Validate
  if (
    ( $transient['action'] ?? 0 ) !== 'fictioneer_connection_get_patreon_tiers' ||
    ( $transient['state'] ?? 0 ) !== $state ||
    ! current_user_can( 'manage_options' )
  ) {
    return;
  }

  // OAuth working?
  if ( ! get_option( 'fictioneer_enable_oauth' ) || ! $client_id || ! $client_secret ) {
    fictioneer_oauth2_exit_and_return();
  }

  // Get access token
  $token_response = fictioneer_get_oauth_token(
    FCN_OAUTH2_API_ENDPOINTS['patreon']['token'],
    array(
      'grant_type' => 'authorization_code',
      'client_id' => $client_id,
      'client_secret' => $client_secret,
      'redirect_uri' => get_site_url( null, FICTIONEER_OAUTH_ENDPOINT ),
      'code' => $code,
      'scope' => urlencode( 'campaigns' )
    )
  );

  // Token successfully retrieved?
  if ( is_wp_error( $token_response ) ) {
    fictioneer_oauth_die( $token_response->get_error_message() );
  }

  // Extract token
  $access_token = isset( $token_response->access_token ) ? $token_response->access_token : null;

  if ( ! $access_token ) {
    fictioneer_oauth2_exit_and_return();
  }

  // Build params
  $params = '?fields' . urlencode('[campaign]') . '=created_at';
  $params .= '&fields' . urlencode('[tier]') . '=title,amount_cents,published,description';
  $params .= '&include=tiers';

  // Retrieve campaign data from Patreon
  $campaign_response = wp_remote_get(
    'https://www.patreon.com/api/oauth2/v2/campaigns' . $params,
    array(
      'headers' => array(
        'Authorization' => 'Bearer ' . $access_token
      )
    )
  );

  // Request successful?
  if ( is_wp_error( $campaign_response ) ) {
    fictioneer_oauth_die( $campaign_response->get_error_message() );
  } else {
    $body = json_decode( wp_remote_retrieve_body( $campaign_response ) );
  }

  // Data successfully retrieved?
  if ( empty( $body ) || json_last_error() !== JSON_ERROR_NONE ) {
    fictioneer_oauth_die( wp_remote_retrieve_body( $campaign_response ) );
  }

  // Data?
  if ( ! isset( $body->data ) ) {
    fictioneer_oauth_die( 'Data node not found.' );
  }

  // Includes?
  if ( ! isset( $body->included ) ) {
    fictioneer_oauth_die( 'Includes node not found.' );
  }

  // Extract tiers
  $tiers = [];

  foreach ( $body->included as $item ) {
    if ( $item->type !== 'tier' || ! isset( $item->attributes ) || $item->attributes->title === 'Free' ) {
      continue;
    }

    $tiers[ $item->id ] = array(
      'id' => absint( $item->id ),
      'title' => sanitize_text_field( $item->attributes->title ?? '' ),
      'description' => wp_kses_post( $item->attributes->description ?? '' ),
      'amount_cents' => absint( $item->attributes->amount_cents ?? 0 ),
      'published' => filter_var( $item->attributes->published ?? 0, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ),
    );
  }

  // Save as autoload option
  update_option( 'fictioneer_connection_patreon_tiers', $tiers );

  // Clean up database
  if ( ! empty( $state ) ) {
    delete_transient( 'fictioneer_oauth2_state_' . $state );
  }

  fictioneer_delete_expired_oauth_transients();

  // Redirect
  wp_safe_redirect(
    add_query_arg(
      array(
        'success' => 'fictioneer-patreon-tiers-pulled'
      ),
      admin_url( 'admin.php?page=fictioneer_connections' )
    )
  );

  exit;
}
add_action( 'template_redirect', 'fictioneer_handle_get_patreon_tiers_oauth', 5 );

// =============================================================================
// DISCORD
// =============================================================================

if ( ! function_exists( 'fictioneer_process_oauth_discord' ) ) {
  /**
   * Retrieve user from Discord
   *
   * @since 4.0.0
   * @see fictioneer_make_oauth_user()
   *
   * @param string $url           The request URL.
   * @param string $access_token  The access token.
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
          'Client-ID' => FCN_OAUTH2_CLIENT_ID,
        )
      )
    );

    if ( is_wp_error( $response ) ) {
      fictioneer_oauth_die( $response->get_error_message() );
    } else {
      $user = json_decode( wp_remote_retrieve_body( $response ) );
    }

    // User data successfully retrieved?
    if ( empty( $user ) || json_last_error() !== JSON_ERROR_NONE ) {
      fictioneer_oauth_die( wp_remote_retrieve_body( $response ) );
    }

    if ( ! isset( $user->verified ) || ! $user->verified ) {
      fictioneer_oauth_die( 'Account not verified.' );
    }

    // Login or register user; returns 'new', 'merged', 'known', or error code
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
      FCN_OAUTH2_API_ENDPOINTS['discord']['revoke'],
      array(
        'token' => $access_token,
        'token_type_hint' => 'access_token',
        'client_id' => FCN_OAUTH2_CLIENT_ID,
        'client_secret' => FCN_OAUTH2_CLIENT_SECRET
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
   * @since 4.0.0
   * @see fictioneer_make_oauth_user()
   *
   * @param string $url           The request URL.
   * @param string $access_token  The access token.
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
          'Client-ID' => FCN_OAUTH2_CLIENT_ID,
        )
      )
    );

    if ( is_wp_error( $response ) ) {
      fictioneer_oauth_die( $response->get_error_message() );
    } else {
      $user = json_decode( wp_remote_retrieve_body( $response ) );
    }

    // User data successfully retrieved?
    if ( empty( $user ) || json_last_error() !== JSON_ERROR_NONE ) {
      fictioneer_oauth_die( wp_remote_retrieve_body( $response ) );
    }

    // Login or register user; returns 'new', 'merged', 'known', or error code
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
      FCN_OAUTH2_API_ENDPOINTS['twitch']['revoke'],
      array(
        'token' => $access_token,
        'token_type_hint' => 'access_token',
        'client_id' => FCN_OAUTH2_CLIENT_ID
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
   * @since 4.0.0
   * @see fictioneer_make_oauth_user()
   *
   * @param string $url           The request URL.
   * @param string $access_token  The access token.
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
          'Client-ID' => FCN_OAUTH2_CLIENT_ID,
        )
      )
    );

    if ( is_wp_error( $response ) ) {
      fictioneer_oauth_die( $response->get_error_message() );
    } else {
      $user = json_decode( wp_remote_retrieve_body( $response ) );
    }

    // User data successfully retrieved?
    if ( empty( $user ) || json_last_error() !== JSON_ERROR_NONE ) {
      fictioneer_oauth_die( wp_remote_retrieve_body( $response ) );
    }

    if ( ! isset( $user->verified_email ) || ! $user->verified_email ) {
      fictioneer_oauth_die( 'Email not verified.' );
    }

    // Login or register user; returns 'new', 'merged', 'known', or error code
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
      FCN_OAUTH2_API_ENDPOINTS['google']['revoke'],
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
   * @since 4.0.0
   * @see fictioneer_make_oauth_user()
   *
   * @param string $url           The request URL.
   * @param string $access_token  The access token.
   *
   * @return string Passed through result of fictioneer_make_oauth_user().
   */

  function fictioneer_process_oauth_patreon( string $url, string $access_token ) {
    // Build params
    $params = '?fields' . urlencode('[user]') . '=email,first_name,image_url,is_email_verified';
    $params .= '&fields' . urlencode('[tier]') . '=title,amount_cents,published,description';
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
    if ( empty( $user ) || json_last_error() !== JSON_ERROR_NONE ) {
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
            'description' => wp_kses_post( $node->attributes->description ?? '' ),
            'published' => filter_var( $node->attributes->published ?? 0, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ),
            'amount_cents' => absint( $node->attributes->amount_cents ?? 0 ),
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

    // Login or register user; returns 'new', 'merged', 'known', or error code
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
   * @since 4.0.0
   *
   * @param array $args  Array of user data.
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
    if ( ! is_a( $wp_user, 'WP_User' ) && username_exists( $username ) ) {
      $alt_username = $username;
      $discriminator = 1;

      // Find alternative name if duplicate exists
      while ( username_exists( $alt_username ) ) {
        $alt_username = $username . $discriminator;
        $discriminator++;
      }
    }

    // When the channel ID is not yet linked and a merge was requested...
    if ( is_user_logged_in() && defined( 'FCN_OAUTH2_MERGE_ID' ) && ! $wp_user ) {
      $wp_user = get_user_by( 'id', FCN_OAUTH2_MERGE_ID );
    }

    // Cannot merge with non-existent user (not found after first try)...
    if ( is_user_logged_in() && defined( 'FCN_OAUTH2_MERGE_ID' ) && ! $wp_user ) {
      fictioneer_oauth2_exit_and_return();
    }

    // When the channel ID is already linked and a merge was requested...
    if ( is_user_logged_in() && defined( 'FCN_OAUTH2_MERGE_ID' ) && FCN_OAUTH2_MERGE_ID != $wp_user->ID ) {
      fictioneer_oauth2_exit_and_return( FCN_OAUTH2_RETURN_URL . '?failure=oauth_already_linked#oauth-connections' );
    }

    // ... if not found, create a new user and get the object if successful
    if ( ! is_a( $wp_user, 'WP_User' ) ) {
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
    if ( is_a( $wp_user, 'WP_User' ) ) {
      // Current avatar
      $current_avatar = empty( $wp_user->fictioneer_external_avatar_url ) ? null : $wp_user->fictioneer_external_avatar_url;

      // Nice name, and hide admin bar for new subscribers
      if ( $new ) {
        fictioneer_update_user_meta( $wp_user->ID, 'nickname', $args['nickname'] );
        wp_update_user( array( 'ID' => $wp_user->ID, 'display_name' => $args['nickname'] ) );
      }

      // Channel ID for new users and merged accounts
      if ( $new || defined( 'FCN_OAUTH2_MERGE_ID' ) ) {
        fictioneer_update_user_meta( $wp_user->ID, "fictioneer_{$args['channel']}_id_hash", $channel_id );
      }

      // Change avatar if updated or empty
      if ( $avatar && $avatar != $current_avatar && ! get_the_author_meta( 'fictioneer_lock_avatar', $wp_user->ID ) ) {
        fictioneer_update_user_meta( $wp_user->ID, 'fictioneer_external_avatar_url', $avatar );
      }

      if ( isset( $args['tiers'] ) && count( $args['tiers'] ) > 0 ) {
        fictioneer_update_user_meta( $wp_user->ID, 'fictioneer_patreon_tiers', $args['tiers'] );
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

      // Action
      do_action(
        'fictioneer_after_oauth_user',
        $wp_user,
        array(
          'channel' => $args['channel'],
          'uid' => $args['uid'],
          'username' => $args['username'],
          'nickname' => $args['nickname'],
          'email' => $args['email'],
          'avatar_url' => $args['avatar'],
          'patreon_tiers' => $args['tiers'] ?? [],
          'new' => $new,
          'merged' => defined( 'FCN_OAUTH2_MERGE_ID' )
        )
      );

      // Return whether this is a new or known user
      if ( is_user_logged_in() ) {
        if ( $new ) {
          return 'new';
        }

        if ( defined( 'FCN_OAUTH2_MERGE_ID' ) ) {
          return 'merged';
        }

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
   * @since 5.5.2
   * @since 5.7.5 - Refactored.
   *
   * @param string $message  The error message.
   * @param string $title    Optional. Title of the error page. Default 'Error'.
   */

  function fictioneer_oauth_die( $message, $title = 'Error' ) {
    if ( ! empty( FCN_OAUTH2_STATE ) ) {
      delete_transient( 'fictioneer_oauth2_state_' . FCN_OAUTH2_STATE );
    }

    fictioneer_delete_expired_oauth_transients();

    // Delete cookie
    setcookie(
      'fictioneer_oauth_id',
      '',
      array(
        'expires' => time() - 3600,
        'path' => '/',
        'domain' => '',
        'secure' => is_ssl(),
        'httponly' => true,
        'samesite' => 'Lax' // Necessary because of redirects
      )
    );

    wp_die(
      '<h1 style="margin-top: 0;">' . $title . '</h1>' .
      '<p><pre>' . print_r( $message, true ) . '</pre></p>' .
      '<p>The good news is, nothing has happened to your account. The bad new is, something is not working. Please try again later or contact an administrator for help. <a href="' . FCN_OAUTH2_RETURN_URL . '">Back to site</a></p>',
      $title
    );
  }
}

if ( ! function_exists( 'fictioneer_set_oauth_constants' ) ) {
  /**
   * Set up all constants
   *
   * @since 4.0.0
   * @since 5.7.5 - Refactored.
   */

  function fictioneer_set_oauth_constants() {
    // Setup
    $state = sanitize_text_field( $_GET['state'] ?? '' );
    $channel = sanitize_text_field( $_GET['channel'] ?? '' ) ?: null;
    $is_valid_return_url = isset( $_GET['return_url'] ) && wp_http_validate_url( $_GET['return_url'] ) ;
    $is_local_return_url = $is_valid_return_url && wp_validate_redirect( $_GET['return_url'], false );
    $return_url = $is_local_return_url ? esc_url_raw( $_GET['return_url'] ) : home_url();
    $anchor = sanitize_text_field( $_GET['anchor'] ?? '' ) ?: null;

    // Transient
    $transient = get_transient( "fictioneer_oauth2_state_{$state}" );

    if ( $transient['channel'] ?? 0 ) {
      $channel = $channel ? $channel : $transient['channel'];
    }

    if ( $transient['return_url'] ?? 0 ) {
      $return_url = urldecode( $transient['return_url'] );
    }

    if ( $transient['anchor'] ?? 0 ) {
      $anchor = $anchor ? $anchor : $transient['anchor'];
    }

    // Define constants
    define( 'FCN_OAUTH2_CLIENT_ID', fictioneer_get_oauth_client_credentials( $channel ) );
    define( 'FCN_OAUTH2_CLIENT_SECRET', fictioneer_get_oauth_client_credentials( $channel, 'secret' ) );
    define( 'FCN_OAUTH2_REDIRECT_URL', get_site_url( null, FICTIONEER_OAUTH_ENDPOINT ) );
    define( 'FCN_OAUTH2_ACTION', sanitize_text_field( $_GET['action'] ?? '' ) ?: null );
    define( 'FCN_OAUTH2_CODE', sanitize_text_field( $_GET['code'] ?? '' ) ?: null );
    define( 'FCN_OAUTH2_STATE', $state );
    define( 'FCN_OAUTH2_RETURN_URL', $return_url );
    define( 'FCN_OAUTH2_ANCHOR', $anchor );
    define( 'FCN_OAUTH2_CHANNEL', $channel );
    define( 'FCN_OAUTH2_ERROR', sanitize_text_field( $_GET['error_description'] ?? '' ) ?: null );
    define( 'FCN_OAUTH2_MERGE', sanitize_text_field( $_GET['merge'] ?? '' ) ?: null );
    define( 'FCN_OAUTH2_COOKIE', sanitize_text_field( $_COOKIE['fictioneer_oauth_id'] ?? '' ) ?: null );

    if ( $transient['user_id'] ?? 0 ) {
      define( 'FCN_OAUTH2_MERGE_ID', $transient['user_id'] );
    }

    if ( $transient['state'] ?? 0 ) {
      define( 'FCN_OAUTH2_TRANSIENT_STATE', $transient['state'] );
    }

    if ( $transient['cookie'] ?? 0 ) {
      define( 'FCN_OAUTH2_TRANSIENT_COOKIE', $transient['cookie'] );
    }
  }
}

if ( ! function_exists( 'fictioneer_oauth2_exit_and_return' ) ) {
  /**
   * Terminate the script and redirect back
   *
   * @since 4.0.0
   * @since 5.7.5 - Refactored.
   *
   * @param string $return_url  Optional. URL to return to.
   * @param string $state       Optional. The state hash.
   */

  function fictioneer_oauth2_exit_and_return( $return_url = null, $state = null ) {
    // Clean up database
    if ( ! empty( $state ) || ! empty( FCN_OAUTH2_STATE ) ) {
      delete_transient( 'fictioneer_oauth2_state_' . ( $state ?? FCN_OAUTH2_STATE ) );
    }

    fictioneer_delete_expired_oauth_transients();

    // Delete cookie
    setcookie(
      'fictioneer_oauth_id',
      '',
      array(
        'expires' => time() - 3600,
        'path' => '/',
        'domain' => '',
        'secure' => is_ssl(),
        'httponly' => true,
        'samesite' => 'Lax' // Necessary because of redirects
      )
    );

    // Prepare return URL
    $return_url = $return_url ?? FCN_OAUTH2_RETURN_URL;
    $return_url = empty( FCN_OAUTH2_ANCHOR ) ? $return_url : $return_url . '#' . FCN_OAUTH2_ANCHOR;

    // Redirect and exit script
    wp_safe_redirect( $return_url );
    exit;
  }
}

if ( ! function_exists( 'fictioneer_get_oauth_client_credentials' ) ) {
  /**
   * Return credentials for the given channel
   *
   * @since 4.0.0
   * @since 5.7.5 - Refactored.
   *
   * @param string $channel  The channel to retrieve the credential for.
   * @param string $type     Optional. The type of credential to retrieve,
   *                         'id' or 'secret'. Default 'id'.
   *
   * @return string|false The client ID or secret, or false if not found.
   */

  function fictioneer_get_oauth_client_credentials( $channel, $type = 'id' ) {
    return get_option( "fictioneer_{$channel}_client_{$type}" );
  }
}

if ( ! function_exists( 'fictioneer_get_oauth_token' ) ) {
  /**
   * Get the OAuth2 access token
   *
   * @since 4.0.0
   * @since 5.7.5 - Refactored.
   *
   * @param string $url      URL to make the API request to.
   * @param array  $post     Post body.
   * @param array  $headers  Array of header options.
   *
   * @return mixed Decoded JSON result or WP_Error on failure.
   */

  function fictioneer_get_oauth_token( $url, $post, $headers = [] ) {
    // Params
    $args = array(
      'headers' => array_merge( array( 'Accept' => 'application/json' ), $headers ),
      'body' => $post,
      'timeout' => 10,
      'blocking' => true,
      'reject_unsafe_urls' => true,
      'data_format' => 'body',
      'limit_response_size' => 4096
    );

    // Request
    $response = wp_remote_post( $url, $args );

    // Error?
    if ( is_wp_error( $response ) ) {
      return $response;
    }

    // Body
    $body = wp_remote_retrieve_body( $response );

    // Return decoded
    return json_decode( $body );
  }
}

if ( ! function_exists( 'fictioneer_revoke_oauth_token' ) ) {
  /**
   * Revoke an OAuth2 access token
   *
   * @since 4.0.0
   * @since 5.7.5 - Refactored.
   *
   * @param string $url   URL to make the API request to.
   * @param array  $post  Post body.
   *
   * @return string HTTP response code.
   */

  function fictioneer_revoke_oauth_token( $url, $post = [] ) {
    // Params
    $args = array(
      'headers' => array( 'Content-Type' => 'application/x-www-form-urlencoded' ),
      'body' => $post
    );

    // Request
    $response = wp_remote_post( $url, $args );

    // Error?
    if ( is_wp_error( $response ) ) {
      return 500; // Internal Server Error
    }

    // Return response code
    return wp_remote_retrieve_response_code( $response );
  }
}

if ( ! function_exists( 'fictioneer_get_oauth_code' ) ) {
  /**
   * Redirect to OAuth provider to retrieve permissions and the code
   *
   * @since 4.0.0
   * @since 5.7.5 - Refactored.
   */

  function fictioneer_get_oauth_code() {
    // Params
    $params = array(
      'response_type' => 'code',
      'client_id' => FCN_OAUTH2_CLIENT_ID,
      'state' => hash( 'sha256', microtime( TRUE ) . random_bytes( 15 ) . $_SERVER['REMOTE_ADDR'] ),
      'scope' => FCN_OAUTH2_API_ENDPOINTS[FCN_OAUTH2_CHANNEL]['scope'],
      'redirect_uri' => FCN_OAUTH2_REDIRECT_URL,
      'force_verify' => 'true',
      'prompt' => 'consent',
      'access_type' => 'offline'
    );

    // Transient
    $transient = array(
      'state' => $params['state'],
      'return_url' => FCN_OAUTH2_RETURN_URL,
      'channel' => FCN_OAUTH2_CHANNEL,
      'anchor' => FCN_OAUTH2_ANCHOR,
      'user_id' => get_current_user_id(),
      'cookie' => hash( 'sha256', microtime( TRUE ) . random_bytes( 15 ) . $_SERVER['REMOTE_ADDR'] )
    );

    set_transient( 'fictioneer_oauth2_state_' . $params['state'], $transient, 60 ); // Expires after 1 minute

    // Cookie
    setcookie(
      'fictioneer_oauth_id',
      $transient['cookie'],
      array(
        'expires' => time() + 60,
        'path' => '/',
        'domain' => '',
        'secure' => is_ssl(),
        'httponly' => true,
        'samesite' => 'Lax' // Necessary because of redirects
      )
    );

    // Redirect
    $url = add_query_arg( $params, FCN_OAUTH2_API_ENDPOINTS[FCN_OAUTH2_CHANNEL]['login'] );
    wp_redirect( $url );

    // Terminate
    exit();
  }
}

if ( ! function_exists( 'fictioneer_delete_expired_oauth_transients' ) ) {
  /**
   * Deletes expires OAuth Transients
   *
   * @since 5.7.5
   */

  function fictioneer_delete_expired_oauth_transients() {
    global $wpdb;

    // Get the names of expired transient timeouts
    $expired_timeouts = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE %s AND option_value < %d",
        $wpdb->esc_like( '_transient_timeout_fictioneer_oauth2_state_' ) . '%',
        time()
      )
    );

    // Delete results...
    if ( $expired_timeouts ) {
      foreach ( $expired_timeouts as $timeout ) {
        $transient_name = str_replace( '_transient_timeout_', '', $timeout->option_name );

        delete_transient( $transient_name );
      }
    }
  }
}
