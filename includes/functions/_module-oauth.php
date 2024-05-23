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
 * Add route to OAuth 2.0 script
 *
 * @since 4.0.0
 */

function fictioneer_add_oauth2_endpoint() {
  add_rewrite_endpoint( FICTIONEER_OAUTH_ENDPOINT, EP_ROOT );
}
add_action( 'init', 'fictioneer_add_oauth2_endpoint', 10 );

// =============================================================================
// GET LOGIN LINKS
// =============================================================================

/**
 * Returns an OAuth 2.0 login link for the given channel
 *
 * @since 4.7.0
 * @since 5.19.0 - Refactored.
 *
 * @param string $channel   The channel.
 * @param string $content   Content of the link.
 * @param array  $args {
 *   Array of optional arguments.
 *
 *   @type string $return_url  Optional. Return URL. Defaults to home address.
 *   @type string $classes     Optional. Additional CSS classes for the link.
 *   @type int    $post_id     Optional. Current post ID.
 *   @type string $action      Optional. The action to perform. Defaults to 'login'.
 *   @type bool   $merge       Optional. Whether this is a merge request.
 *   @type string $anchor      Optional. Anchor for the return URL.
 * }
 *
 * @return string OAuth 2.0 login link or empty string if disabled.
 */

function fictioneer_get_oauth2_login_link( $channel, $content, $args = [] ) {
  // Return empty string if...
  if (
    ! get_option( 'fictioneer_enable_oauth' ) ||
    ! get_option( "fictioneer_{$channel}_client_id" ) ||
    ! get_option( "fictioneer_{$channel}_client_secret" )
  ) {
    return '';
  }

  // Setup
  $return_url = isset( $args['return_url'] ) ? $args['return_url'] : get_permalink( $args['post_id'] ?? 0 );
  $classes = $args['classes'] ?? '';

  // Build URL
  $url = add_query_arg(
    array(
      'action' => $args['action'] ?? 'login',
      'return_url' => $return_url,
      'channel' => $channel
    ),
    wp_nonce_url( get_site_url( null, FICTIONEER_OAUTH_ENDPOINT ), 'authenticate', 'oauth_nonce' )
  );

  if ( $args['merge'] ?? 0 ) {
    $url = add_query_arg( 'merge', $args['merge'], $url );
  }

  if ( $args['anchor'] ?? 0 ) {
    $url = add_query_arg( 'anchor', $args['anchor'], $url );
  }

  $url = esc_url( $url );

  // Return HTML link
  return "<a href='{$url}' class='oauth-login-link _{$channel} {$classes}' rel='noopener noreferrer nofollow'>{$content}</a>";
}

/**
 * Returns OAuth 2.0 login links for all channels
 *
 * @since 4.7.0
 * @since 5.19.0 - Refactored.
 *
 * @param boolean|string $label    Optional. Whether to show the channel as
 *                                 label and the text before that (if any).
 *                                 Can be false, true, or any string.
 * @param string         $classes  Optional. Additional CSS classes.
 * @param boolean|string $anchor   Optional. Anchor to append to the URL.
 *
 * @return string Sequence of links or empty string if OAuth 2.0 is disabled.
 */

function fictioneer_get_oauth2_login_links( $label = false, $classes = '', $anchor = false, $post_id = null ) {
  // Abort if...
  if ( ! get_option( 'fictioneer_enable_oauth' ) ) {
    return '';
  }

  // Setup
  $channels = array(
    ['discord', __( 'Discord', 'fictioneer' )],
    ['twitch', __( 'Twitch', 'fictioneer' )],
    ['google', __( 'Google', 'fictioneer' )],
    ['patreon', __( 'Patreon', 'fictioneer' )]
  );
  $output = '';

  // Build link for each channel
  foreach ( $channels as $channel ) {
    // Prefix (if any)
    $prefix = is_string( $label ) ?  "{$label} " : '';

    // Label after the icon (if any)
    $oauth_label = $label ? "<span>{$prefix}{$channel[1]}</span>" : '';

    // Build link
    $output .= fictioneer_get_oauth2_login_link(
      $channel[0],
      '<i class="fa-brands fa-' . $channel[0] . '"></i>' . $oauth_label,
      array(
        'classes' => $classes,
        'anchor' => $anchor ?: '',
        'post_id' => $post_id
      )
    );
  }

  // Return link sequence as HTML
  return $output;
}

// =============================================================================
// PROCESS
// =============================================================================

/**
 * Process any OAuth 2.0 request
 *
 * @since 5.19.0
 * @link https://dev.twitch.tv/docs/authentication
 * @link https://discord.com/developers/docs/topics/oauth2
 * @link https://docs.patreon.com/#step-1-registering-your-client
 * @link https://developers.google.com/identity/protocols/oauth2
 */

function fictioneer_oauth2_process() {
  // Abort if not on the /oauth2 route...
  if ( is_null( get_query_var( FICTIONEER_OAUTH_ENDPOINT, null ) ) ) {
    return;
  }

  // Setup
  $cookie = fictioneer_oauth2_get_cookie();
  $action = sanitize_key( $_GET['action'] ?? '' );
  $state = sanitize_key( $_GET['state'] ?? '' );
  $code = sanitize_text_field( $_GET['code'] ?? '' ); // Do not use sanitize_key()
  $channel = sanitize_key( $_GET['channel'] ?? '' );
  $merge = ( $_GET['merge'] ?? $cookie['merge'] ?? 0 ) ? 1 : 0;
  $anchor = sanitize_key( $_GET['anchor'] ?? $cookie['anchor'] ?? '' );
  $return_url = wp_validate_redirect( $_GET['return_url'] ?? $cookie['return_url'] ?? home_url(), home_url() );
  $result = '';

  // Verify!
  if ( ! $state && ! wp_verify_nonce( $_GET['oauth_nonce'] ?? '', 'authenticate' ) ) {
    fictioneer_oauth2_terminate(
      $return_url,
      array( 'failure' => 'oauth_invalid_nonce' ),
      $anchor
    );
  }

  // Merge?
  if ( ( ! is_user_logged_in() && $merge ) || ( is_user_logged_in() && ! $merge ) ) {
    fictioneer_oauth2_terminate(
      $return_url,
      array( 'failure' => 'oauth_invalid_merge_request' ),
      $anchor
    );
  }

  // Login: Get code!
  if ( $action === 'login' && ! $state ) {
    fictioneer_oauth2_get_code(
      array(
        'channel' => $channel,
        'anchor' => $anchor,
        'return_url' => $return_url,
        'merge' => $merge,
        'merge_id' => get_current_user_id()
      )
    );
  }

  // Cookie?
  if ( ! $cookie || ! ( $cookie['state'] ?? 0 ) || ! ( $cookie['channel'] ?? 0 ) || ! ( $cookie['return_url'] ?? 0 ) ) {
    fictioneer_oauth2_terminate(
      $cookie['return_url'],
      array( 'failure' => 'oauth_invalid_cookie' ),
      $anchor
    );
  }

  // State?
  if ( $cookie['state'] !== $state ) {
    fictioneer_oauth2_terminate(
      $cookie['return_url'],
      array( 'failure' => 'oauth_invalid_state' ),
      $anchor
    );
  }

  // Code?
  if ( ! $code ) {
    fictioneer_oauth2_terminate(
      $cookie['return_url'],
      array( 'failure' => 'oauth_invalid_code' ),
      $anchor
    );
  }

  // Code: Get access token!
  $token_response = fictioneer_oauth2_get_token(
    FCN_OAUTH2_API_ENDPOINTS[ $cookie['channel'] ]['token'],
    array(
      'grant_type' => 'authorization_code',
      'client_id' => get_option( "fictioneer_{$cookie['channel']}_client_id" ),
      'client_secret' => get_option( "fictioneer_{$cookie['channel']}_client_secret" ),
      'redirect_uri' => get_site_url( null, FICTIONEER_OAUTH_ENDPOINT ),
      'code' => $code,
      'scope' => FCN_OAUTH2_API_ENDPOINTS[ $cookie['channel'] ]['scope']
    )
  );

  // Error?
  if ( is_wp_error( $token_response ) ) {
    fictioneer_oauth2_terminate(
      $cookie['return_url'],
      array( 'failure' => $token_response->get_error_code() ),
      $anchor
    );
  }

  // Token?
  $access_token = isset( $token_response->access_token ) ? $token_response->access_token : null;

  if ( ! $access_token ) {
    fictioneer_oauth2_terminate(
      $cookie['return_url'],
      array( 'failure' => 'oauth_token_missing' ),
      $anchor
    );
  }

  // Delegate to respective channel
  switch ( $cookie['channel'] ) {
    case 'discord':
      $result = fictioneer_oauth_discord( $token_response, $cookie );
      break;
    case 'patreon':
      $result = fictioneer_oauth_patreon( $token_response, $cookie );
      break;
    case 'google':
      $result = fictioneer_oauth_google( $token_response, $cookie );
      break;
    case 'twitch':
      $result = fictioneer_oauth_twitch( $token_response, $cookie );
      break;
    // case 'subscribestar':
    //   $result = fictioneer_oauth_subscribestar( $token_response, $cookie );
    //   break;
    default:
      $result = 'oauth_invalid_channel';
  }

  // Error: Any
  if ( ! in_array( $result, ['merged_user', 'new_user', 'known_user'] ) ) {
    $return_url = add_query_arg( 'failure', $result, $return_url );
  }

  // Success: Merged
  if ( $result === 'merged_user' ) {
    $return_url = add_query_arg( 'success', 'oauth_merged_' . $cookie['channel'], $return_url );
  }

  // Success: New
  if ( $result === 'new_user' ) {
    $return_url = add_query_arg( 'success', 'oauth_new', $return_url );
  }

  // Success: Known
  if ( $result === 'known_user' ) {
    $return_url = add_query_arg( 'success', 'oauth_known', $return_url );
  }

  // Finish
  fictioneer_oauth2_delete_cookie();
  wp_safe_redirect( $return_url . ( $anchor ? '#' . $anchor : '' ) );
  exit;
}
add_action( 'template_redirect', 'fictioneer_oauth2_process', 10 );

/**
 * Log in or register user
 *
 * @since 5.19.0
 *
 * @param array $user_data  Array of user data.
 * @param array $cookie     The decrypted cookie data.
 */

function fictioneer_oauth2_make_user( $user_data, $cookie ) {
  // Setup
  $channel = $user_data['channel'];
  $meta_key = "fictioneer_{$channel}_id_hash";
  $channel_id = 'fcn_' . hash( 'sha256', sanitize_key( $user_data['uid'] ) );
  $username = sanitize_user( $user_data['username'] );
  $nickname = sanitize_user( $user_data['nickname'] );
  $email = sanitize_email( $user_data['email'] );
  $avatar = fictioneer_url_exists( $user_data['avatar'] ) ? $user_data['avatar'] : null;
  $merge_id = absint( $cookie['merge_id'] ?? 0 );
  $new = false;
  $merged = false;

  // Randomize username?
  if ( get_option( 'fictioneer_randomize_oauth_usernames' ) ) {
    $username = fictioneer_get_random_username();
    $nickname = $username;
  }

  // Look for existing user...
  $wp_user = get_users( array( 'meta_key' => $meta_key, 'meta_value' => $channel_id, 'number' => 1 ) );
  $wp_user = reset( $wp_user );

  // Check for faulty merge request
  if ( ! is_user_logged_in() && $cookie['merge'] ) {
    return 'oauth_invalid_merge_request';
  }

  if ( is_user_logged_in() && ! $cookie['merge'] ) {
    return 'oauth_invalid_merge_request';
  }

  // If a valid merge has been requested...
  if ( is_user_logged_in() && ! $wp_user && $cookie['merge'] ) {
    if ( ! $wp_user && get_current_user_id() === $merge_id ) {
      $wp_user = get_user_by( 'id', $merge_id );
      $merged = true;
    }

    // Cannot merge with non-existent user...
    if ( is_user_logged_in() && ! $wp_user ) {
      return 'oauth_invalid_merge_user';
    }
  }

  // Cannot merged if already linked
  if ( is_user_logged_in() && $wp_user && $merge_id !== $wp_user->ID ) {
    return 'oauth_already_linked';
  }

  // If still no user has been found, create a new one...
  if ( ! is_a( $wp_user, 'WP_User' ) ) {
    // Find free username
    if ( username_exists( $username ) ) {
      $alt_username = $username;
      $discriminator = 1;

      while ( username_exists( $alt_username ) ) {
        $alt_username = $username . $discriminator;
        $discriminator++;
      }

      $username = $alt_username;
    }

    // Create user
    $wp_user_id = wp_create_user( $username, wp_generate_password( 32, true, true ), $email );

    // Failure or success?
    if ( is_wp_error( $wp_user_id ) ) {
      $error_code = $wp_user_id->get_error_code();
      return $error_code === 'existing_user_email' ? 'oauth_email_taken' : $error_code;
    } else {
      $wp_user = get_user_by( 'id', $wp_user_id );
      $new = true;
    }
  }

  // Update user data
  if ( is_a( $wp_user, 'WP_User' ) ) {
    // Current avatar
    $current_avatar = $wp_user->fictioneer_external_avatar_url ?: '';

    // Set nickname and display name
    if ( $new ) {
      fictioneer_update_user_meta( $wp_user->ID, 'nickname', $nickname );
      wp_update_user( array( 'ID' => $wp_user->ID, 'display_name' => $nickname ) );
    }

    // Set channel ID
    if ( $new || $merged ) {
      fictioneer_update_user_meta( $wp_user->ID, "fictioneer_{$channel}_id_hash", $channel_id );
    }

    // Update avatar
    if ( $avatar !== $current_avatar && ! get_the_author_meta( 'fictioneer_lock_avatar', $wp_user->ID ) ) {
      fictioneer_update_user_meta( $wp_user->ID, 'fictioneer_external_avatar_url', $avatar );
    }

    // Handle Patreon
    if ( $channel === 'patreon' ) {
      if ( isset( $user_data['tiers'] ) && count( $user_data['tiers'] ) > 0 ) {
        fictioneer_update_user_meta( $wp_user->ID, 'fictioneer_patreon_tiers', $user_data['tiers'] );
      } else {
        delete_user_meta( $wp_user->ID, 'fictioneer_patreon_tiers' );
      }

      if ( isset( $user_data['membership'] ) && ! empty( $user_data['membership'] ) ) {
        fictioneer_update_user_meta( $wp_user->ID, 'fictioneer_patreon_membership', $user_data['membership'] );
      } else {
        delete_user_meta( $wp_user->ID, 'fictioneer_patreon_membership' );
      }
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
        'channel' => $user_data['channel'],
        'uid' => $user_data['uid'],
        'username' => $user_data['username'],
        'nickname' => $user_data['nickname'],
        'email' => $user_data['email'],
        'avatar_url' => $user_data['avatar'],
        'patreon_tiers' => $user_data['patreon_tiers'] ?? [],
        'new' => $new,
        'merged' => $merged
      )
    );

    // Return result
    if ( is_user_logged_in() ) {
      if ( $new ) {
        return 'new_user';
      }

      if ( $merged ) {
        return 'merged_user';
      }

      return 'known_user';
    }
  }

  // Something went very wrong
  return 'no_user_found_or_created';
}

// =============================================================================
// HELPERS
// =============================================================================

/**
 * Terminate the OAuth 2.0 script and redirect back
 *
 * @since 5.19.0
 *
 * @param string $return_url  Optional. URL to return to.
 * @param array  $query_args  Optional. Additional query arguments for the redirect.
 * @param string $anchor      Optional. Anchor for the return URL.
 */

function fictioneer_oauth2_terminate( $return_url = null, $query_args = [], $anchor = null ) {
  // Delete cookie
  fictioneer_oauth2_delete_cookie();

  // Redirect and terminate
  wp_safe_redirect( add_query_arg( $query_args, $return_url ?? home_url() ) . ( $anchor ? '#' . $anchor : '' ) );
  exit;
}

/**
 * Outputs a formatted error message and stops the script
 *
 * @since 5.5.2
 * @since 5.7.5 - Refactored.
 * @since 5.19.0 - Refactored again.
 *
 * @param string $message  The error message.
 * @param string $title    Optional. Title of the error page. Defaults to 'Error'.
 */

function fictioneer_oauth_die( $message, $title = 'Error' ) {
  // Delete cookie
  fictioneer_oauth2_delete_cookie();

  wp_die(
    '<h1 style="margin-top: 0;">' . $title . '</h1>' .
    '<p><pre>' . print_r( $message, true ) . '</pre></p>' .
    '<p>The good news is, nothing has happened to your account. The bad new is, something is not working. Please try again later or contact an administrator for help. <a href="' . home_url() . '">Back to site</a></p>',
    $title
  );
}

/**
 * Get the OAuth 2.0 cookie contents
 *
 * @since 5.19.0
 *
 * @return array|null The cookie as array or null on failure.
 */

function fictioneer_oauth2_get_cookie() {
  if ( isset( $_COOKIE['fictioneer_oauth'] ) ) {
    return fictioneer_decrypt( $_COOKIE['fictioneer_oauth'] ) ?: null;
  }

  return null;
}

/**
 * Deleted the OAuth 2.0 cookie
 *
 * @since 5.19.0
 */

function fictioneer_oauth2_delete_cookie() {
  setcookie(
    'fictioneer_oauth',
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
}

/**
 * Get code from OAuth 2.0 provider
 *
 * @since 5.19.0
 *
 * @param array  $args {
 *   Array of arguments.
 *
 *   @type string $channel     The targeted provider.
 *   @type string $return_url  The return URL.
 *   @type string $anchor      Optional. Anchor for the return URL.
 *   @type bool   $merge       Optional. Whether this is a merge request.
 *   @type int    $merge_id    ID of the current user or 0.
 * }
 */

function fictioneer_oauth2_get_code( $args ) {
  // Setup
  $client_id = get_option( "fictioneer_{$args['channel']}_client_id" );
  $client_secret = get_option( "fictioneer_{$args['channel']}_client_secret" );

  // Abort if...
  if ( ! $client_id || ! $client_secret ) {
    fictioneer_oauth2_terminate( $args['return_url'] );
  }

  // Prepare request
  $params = array(
    'response_type' => 'code',
    'client_id' => $client_id,
    'state' => hash( 'sha256', microtime( TRUE ) . random_bytes( 15 ) . $_SERVER['REMOTE_ADDR'] ),
    'scope' => FCN_OAUTH2_API_ENDPOINTS[ $args['channel'] ]['scope'],
    'redirect_uri' => get_site_url( null, FICTIONEER_OAUTH_ENDPOINT ),
    'force_verify' => 'true',
    'prompt' => 'consent',
    'access_type' => 'offline'
  );

  // Set cookie
  $value = fictioneer_encrypt(
    array(
      'state' => $params['state'],
      'channel' => $args['channel'],
      'return_url' => $args['return_url'],
      'anchor' => $args['anchor'],
      'merge' => $args['merge'],
      'merge_id' => $args['merge_id']
    )
  );

  if ( $value ) {
    setcookie(
      'fictioneer_oauth',
      $value,
      array(
        'expires' => time() + 300,
        'path' => '/',
        'domain' => '',
        'secure' => is_ssl(),
        'httponly' => true,
        'samesite' => 'Lax' // Necessary because of redirects
      )
    );
  }

  // Request code
  wp_redirect( add_query_arg( $params, FCN_OAUTH2_API_ENDPOINTS[ $args['channel'] ]['login'] ) );

  // Terminate
  exit();
}

/**
 * Get the OAuth 2.0 access token
 *
 * @since 5.19.0
 *
 * @param string $url      URL to make the API request to.
 * @param array  $body     Post body.
 * @param array  $headers  Optional. Array of additional header arguments.
 *
 * @return object|WP_Error Decoded JSON result or WP_Error on failure.
 */

function fictioneer_oauth2_get_token( $url, $body, $headers = [] ) {
  // Params
  $args = array(
    'headers' => array_merge( array( 'Accept' => 'application/json' ), $headers ),
    'body' => $body,
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

  // Return decoded body
  return json_decode( wp_remote_retrieve_body( $response ) );
}

/**
 * Revoke an OAuth 2.0 access token
 *
 * @since 4.0.0
 * @since 5.7.5 - Refactored.
 * @since 5.19.0 - Refactored again.
 *
 * @param string $url   URL to make the API request to.
 * @param array  $post  Post body.
 *
 * @return string HTTP response code.
 */

function fictioneer_oauth2_revoke_token( $url, $body ) {
  // Params
  $args = array(
    'headers' => array( 'Content-Type' => 'application/x-www-form-urlencoded' ),
    'body' => $body
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

// =============================================================================
// PROVIDERS
// =============================================================================

/**
 * Perform OAuth 2.0 authentication for Patreon
 *
 * @since 5.19.0
 *
 * @param object $token_response  Token request response.
 * @param array  $cookie          Decrypted data of the cookie.
 *
 * @return string Result code from fictioneer_oauth2_make_user().
 */

function fictioneer_oauth_patreon( $token_response, $cookie ) {
  // Build params
  $params = '?fields' . urlencode('[user]') . '=email,first_name,image_url,is_email_verified';
  $params .= '&fields' . urlencode('[tier]') . '=title,amount_cents,published,description';
  $params .= '&fields' . urlencode('[member]') . '=lifetime_support_cents,is_follower,last_charge_date,last_charge_status,next_charge_date,patron_status';
  $params .= '&include=memberships.currently_entitled_tiers';

  // Retrieve user data from Patreon
  $user_response = wp_remote_get(
    FCN_OAUTH2_API_ENDPOINTS['patreon']['user'] . $params,
    array(
      'headers' => array(
        'Authorization' => 'Bearer ' . $token_response->access_token
      )
    )
  );

  // Check retrieved user response
  if ( is_wp_error( $user_response ) ) {
    fictioneer_oauth_die( $user_response->get_error_message() );
  } else {
    $user = json_decode( wp_remote_retrieve_body( $user_response ) );
  }

  // User data successfully retrieved?
  if ( empty( $user ) || json_last_error() !== JSON_ERROR_NONE ) {
    fictioneer_oauth_die( wp_remote_retrieve_body( $user_response ) );
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

  // Extract membership data
  $tiers = [];
  $tier_ids = [];
  $membership = [];

  if ( isset( $user->included ) ) {
    // Tiers data
    foreach( $user->included as $node ) {
      if ( isset( $node->type ) && $node->type === 'tier' ) {
        $tiers[] = array(
          'tier' => sanitize_text_field( $node->attributes->title ),
          'title' => sanitize_text_field( $node->attributes->title ),
          'description' => wp_kses_post( $node->attributes->description ?? '' ),
          'published' => filter_var( $node->attributes->published ?? 0, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ),
          'amount_cents' => absint( $node->attributes->amount_cents ?? 0 ),
          'timestamp' => time(),
          'id' => $node->id
        );
        $tier_ids[] = $node->id;
      }
    }

    // Membership data
    foreach( $user->included as $node ) {
      if (
        isset( $node->type ) &&
        $node->type === 'member' &&
        isset( $node->attributes ) &&
        isset( $node->attributes->lifetime_support_cents ) &&
        isset( $node->relationships->currently_entitled_tiers->data ) &&
        in_array( $node->relationships->currently_entitled_tiers->data[0]->id, $tier_ids )
      ) {
        $membership['is_follower'] = $node->attributes->is_follower ?? 0;
        $membership['lifetime_support_cents'] = $node->attributes->lifetime_support_cents ?? 0;
        $membership['last_charge_date'] = $node->attributes->last_charge_date ?? null;
        $membership['last_charge_status'] = $node->attributes->last_charge_status ?? null;
        $membership['next_charge_date'] = $node->attributes->next_charge_date ?? null;
        $membership['patron_status'] = $node->attributes->patron_status ?? null;

        break;
      }
    }
  }

  /* Patreon does not have a function to revoke a token (2024/05/21). */

  // Login or register user
  return fictioneer_oauth2_make_user(
    array(
      'channel' => 'patreon',
      'uid' => $user->data->id,
      'email' => $user->data->attributes->email,
      'username' => $user->data->attributes->first_name,
      'nickname' => $user->data->attributes->first_name,
      'avatar' => esc_url_raw( $user->data->attributes->image_url ?? '' ),
      'tiers' => $tiers,
      'membership' => $membership
    ),
    $cookie
  );
}

/**
 * Perform OAuth 2.0 authentication for Discord
 *
 * @since 5.19.0
 *
 * @param object $token_response  Token request response.
 * @param array  $cookie          Decrypted data of the cookie.
 *
 * @return string Result code from fictioneer_oauth2_make_user().
 */

function fictioneer_oauth_discord( $token_response, $cookie ) {
  // Get user data
  $user_response = wp_remote_get(
    FCN_OAUTH2_API_ENDPOINTS['discord']['user'],
    array(
      'headers' => array(
        'Authorization' => 'Bearer ' . $token_response->access_token,
        'Client-ID' => get_option( 'fictioneer_discord_client_id' )
      )
    )
  );

  // Check retrieved user response
  if ( is_wp_error( $user_response ) ) {
    fictioneer_oauth_die( $user_response->get_error_message() );
  } else {
    $user = json_decode( wp_remote_retrieve_body( $user_response ) );
  }

  // User data successfully retrieved?
  if ( empty( $user ) || json_last_error() !== JSON_ERROR_NONE ) {
    fictioneer_oauth_die( wp_remote_retrieve_body( $user_response ) );
  }

  // Account verified?
  if ( ! isset( $user->verified ) || ! $user->verified ) {
    fictioneer_oauth_die( 'Account not verified.' );
  }

  // Revoke token since it's not needed anymore
  fictioneer_oauth2_revoke_token(
    FCN_OAUTH2_API_ENDPOINTS['discord']['revoke'],
    array(
      'token' => $token_response->access_token,
      'token_type_hint' => 'access_token',
      'client_id' => get_option( 'fictioneer_discord_client_id' ),
      'client_secret' => get_option( 'fictioneer_discord_client_secret' )
    )
  );

  // Login or register user
  return fictioneer_oauth2_make_user(
    array(
      'channel' => 'discord',
      'uid' => $user->id,
      'email' => $user->email,
      'username' => $user->username . ( $user->discriminator ?? ''),
      'nickname' => $user->username,
      'avatar' => esc_url_raw( "https://cdn.discordapp.com/avatars/{$user->id}/{$user->avatar}.png" )
    ),
    $cookie
  );
}

/**
 * Perform OAuth 2.0 authentication for Google
 *
 * @since 5.19.0
 *
 * @param object $token_response  Token request response.
 * @param array  $cookie          Decrypted data of the cookie.
 *
 * @return string Result code from fictioneer_oauth2_make_user().
 */

function fictioneer_oauth_google( $token_response, $cookie ) {
  // Retrieve user data from Google
  $user_response = wp_remote_get(
    FCN_OAUTH2_API_ENDPOINTS['google']['user'],
    array(
      'headers' => array(
        'Authorization' => 'Bearer ' . $token_response->access_token,
        'Client-ID' => get_option( 'fictioneer_google_client_id' )
      )
    )
  );

  // Check retrieved user response
  if ( is_wp_error( $user_response ) ) {
    fictioneer_oauth_die( $user_response->get_error_message() );
  } else {
    $user = json_decode( wp_remote_retrieve_body( $user_response ) );
  }

  // User data successfully retrieved?
  if ( empty( $user ) || json_last_error() !== JSON_ERROR_NONE ) {
    fictioneer_oauth_die( wp_remote_retrieve_body( $user_response ) );
  }

  // Account verified?
  if ( ! isset( $user->verified_email ) || ! $user->verified_email ) {
    fictioneer_oauth_die( 'Email not verified.' );
  }

  // Revoke token since it's not needed anymore
  fictioneer_oauth2_revoke_token(
    FCN_OAUTH2_API_ENDPOINTS['google']['revoke'],
    array(
      'token' => $token_response->access_token
    )
  );

  // Generate random username (because Google uses real names)
  $name = fictioneer_get_random_username();

  // Login or register user
  return fictioneer_oauth2_make_user(
    array(
      'channel' => 'google',
      'uid' => $user->id,
      'email' => $user->email,
      'username' => $name,
      'nickname' => $name,
      'avatar' => esc_url_raw( $user->picture ?? '' )
    ),
    $cookie
  );
}

/**
 * Perform OAuth 2.0 authentication for Twitch
 *
 * @since 5.19.0
 *
 * @param object $token_response  Token request response.
 * @param array  $cookie          Decrypted data of the cookie.
 *
 * @return string Result code from fictioneer_oauth2_make_user().
 */

function fictioneer_oauth_twitch( $token_response, $cookie ) {
  // Retrieve user data from Twitch
  $user_response = wp_remote_get(
    FCN_OAUTH2_API_ENDPOINTS['twitch']['user'],
    array(
      'headers' => array(
        'Authorization' => 'Bearer ' . $token_response->access_token,
        'Client-ID' => get_option( 'fictioneer_twitch_client_id' )
      )
    )
  );

  // Check retrieved user response
  if ( is_wp_error( $user_response ) ) {
    fictioneer_oauth_die( $user_response->get_error_message() );
  } else {
    $user = json_decode( wp_remote_retrieve_body( $user_response ) );
  }

  // User data successfully retrieved?
  if ( empty( $user ) || json_last_error() !== JSON_ERROR_NONE ) {
    fictioneer_oauth_die( wp_remote_retrieve_body( $user_response ) );
  }

  // Revoke token since it's not needed anymore
  fictioneer_oauth2_revoke_token(
    FCN_OAUTH2_API_ENDPOINTS['twitch']['revoke'],
    array(
      'token' => $token_response->access_token,
      'token_type_hint' => 'access_token',
      'client_id' => get_option( 'fictioneer_twitch_client_id' )
    )
  );

  // Login or register user
  return fictioneer_oauth2_make_user(
    array(
      'channel' => 'twitch',
      'uid' => $user->data[0]->id,
      'email' => $user->data[0]->email,
      'username' => $user->data[0]->login,
      'nickname' => $user->data[0]->display_name,
      'avatar' => esc_url_raw( $user->data[0]->profile_image_url ?? '' )
    ),
    $cookie
  );
}

/**
 * Perform OAuth 2.0 authentication for SubscribeStar
 *
 * @since 5.19.0
 *
 * @param object $token_response  Token request response.
 * @param array  $cookie          Decrypted data of the cookie.
 *
 * @return string Result code from fictioneer_oauth2_make_user().
 */

function fictioneer_oauth_subscribestar( $token_response, $cookie ) {
  // Placeholder
}
