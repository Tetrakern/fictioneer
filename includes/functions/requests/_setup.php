<?php

// =============================================================================
// REPLACEMENTS FOR WP FUNCTIONS
// =============================================================================

/**
 * Loads specific WordPress options into a global variable.
 *
 * Note: Regardless of the given option names to query, the function
 * always queries a set of default WP options for convenience.
 *
 * @since 5.xx.x
 * @global wpdb $wpdb            WordPress database object.
 * @global array $ffcnr_options  Array of previously loaded options.
 *
 * @param array $option_names  Optional. Array of option names to load.
 *
 * @return array Array of loaded options.
 */

function ffcnr_load_options( $option_names = [] ) {
  global $wpdb, $ffcnr_options;

  if ( ! isset( $ffcnr_options ) ) {
    $ffcnr_options = [];
  }

  $default_options = ['siteurl', 'home', 'blogname', 'blogdescription', 'users_can_register', 'admin_email', 'timezone_string', 'date_format', 'time_format', 'posts_per_page', 'permalink_structure', 'upload_path', 'template', 'blog_charset', 'active_plugins', 'gmt_offset', 'stylesheet', 'default_role', 'avatar_rating', 'show_avatars', 'avatar_default', 'page_for_posts', 'page_on_front', 'site_icon', 'wp_user_roles', 'cron', 'nonce_key', 'nonce_salt', 'current_theme', 'show_on_front', 'blog_public', 'theme_switched'];

  $default_options = apply_filters( 'ffcnr_load_options_defaults', $default_options );

  $query_options = array_unique( array_merge( $option_names, $default_options ) );
  $missing_options = array_diff( $query_options, array_keys( $ffcnr_options ) );

  if ( ! empty( $missing_options ) ) {
    $placeholders = implode( ',', array_fill( 0, count( $missing_options ), '%s' ) );

    $results = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT `option_name`, `option_value` FROM $wpdb->options WHERE `option_name` IN ($placeholders)",
        $missing_options
      ),
      'OBJECT_K'
    );

    foreach ( $results as $option_name => $option ) {
      $ffcnr_options[ $option_name ] = maybe_unserialize( $option->option_value );
    }

    foreach ( $missing_options as $missing_option ) {
      if ( ! isset( $ffcnr_options[ $missing_option ] ) ) {
        $ffcnr_options[ $missing_option ] = null;
      }
    }
  }

  return $ffcnr_options;
}

/**
 * Returns hash of a given string.
 *
 * Note: Reduced alternative to wp_hash().
 *
 * @since 5.xx.x
 *
 * @param string $data    Plain text to hash.
 * @param string $scheme  Authentication scheme (auth, nonce).
 *
 * @return string Hash of $data.
 */

function ffcnr_hash( $data, $scheme = 'auth' ){
  $salts = array(
    'auth' => LOGGED_IN_KEY . LOGGED_IN_SALT,
    'nonce' => NONCE_KEY . NONCE_SALT
  );

  return hash_hmac( 'md5', $data, $salts[ $scheme ] );
}

/**
 * Returns hashed session token.
 *
 * Note: Reduced alternative to WP_Session_Tokens::hash_token().
 *
 * @since 5.xx.x
 *
 * @param string $token  Session token to hash.
 *
 * @return string A hash of the session token (a verifier).
 */

function ffcnr_hash_token( $token ){
  if ( function_exists( 'hash' ) ) {
    return hash( 'sha256', $token );
  } else {
    return sha1( $token );
  }
}

/**
 * Returns authentication cookie.
 *
 * @since 5.xx.x
 *
 * @return string The unprocessed cookie string.
 */

function ffcnr_get_auth_cookie() {
  static $cookie;

  if ( $cookie ) {
    return $cookie;
  }

  $ffcnr_options = ffcnr_load_options( ['siteurl'] );
  $site_url = rtrim( mb_strtolower( $ffcnr_options['siteurl'], 'UTF-8' ), '/' );
  $cookie_hash = apply_filters( 'ffcnr_auth_cookie_hash', md5( $site_url ), $site_url, $ffcnr_options );

  if ( ! isset( $_COOKIE[ "wordpress_logged_in_{$cookie_hash}" ] ) ) {
    return false;
  }

  $cookie = $_COOKIE[ "wordpress_logged_in_{$cookie_hash}" ];

  return $cookie;
}

/**
 * Returns the current session token from the logged_in cookie.
 *
 * Note: Alternative to wp_get_session_token().
 *
 * @since 5.xx.x
 *
 * @return string The session token or empty string.
 */

function ffcnr_get_session_token() {
  $cookie = ffcnr_get_auth_cookie();
  $cookie = explode( '|', $cookie );

  return ! empty( $cookie[2] ) ? $cookie[2] : '';
}

/**
 * Returns the time-dependent variable for nonce creation.
 *
 * Note: Alternative to wp_nonce_tick().
 *
 * @since 5.xx.x
 *
 * @return float Float value rounded up to the next highest integer.
 */

function ffcnr_nonce_tick( $action = -1 ) {
  $nonce_life = apply_filters( 'ffcnr_nonce_life', DAY_IN_SECONDS, $action );

  return ceil( time() / ( $nonce_life / 2 ) );
}

/**
 * Returns a cryptographic token tied to a specific action,
 * user, user session, and window of time.
 *
 * Note: Alternative to wp_create_nonce().
 *
 * @since 5.xx.x
 *
 * @param string $action  Scalar value to add context to the nonce.
 * @param int    $uid     User Id.
 *
 * @return string The nonce.
 */

function ffcnr_create_nonce( $action, $uid ) {
  $token = ffcnr_get_session_token();
  $i = ffcnr_nonce_tick( $action );

  if ( ! $uid ) {
    $uid = apply_filters( 'ffcnr_nonce_user_logged_out', $uid, $action );
  }

  return substr( ffcnr_hash( $i . '|' . $action . '|' . $uid . '|' . $token, 'nonce' ), -12, 10 );
}

/**
 * Returns current user.
 *
 * @since 5.xx.x
 *
 * @param array    $options           Optional. Pre-queried theme options.
 * @param int|null $blog_id_override  Optional. Override current blog ID.
 *
 * @return stdClass|int User or 0.
 */

function ffcnr_get_current_user( $options = null, $blog_id_override = null ) {
  global $wpdb, $blog_id;

  $_blog_id = $blog_id_override ?? $blog_id ?? 1;
  $site_prefix = $wpdb->get_blog_prefix( $_blog_id );
  $options = $options ?: ffcnr_load_options( ['siteurl', "{$site_prefix}user_roles"] );
  $cookie = ffcnr_get_auth_cookie();
  $cookie_elements = explode( '|', $cookie );

  if ( count( $cookie_elements ) !== 4 ) {
    return 0;
  }

  list( $username, $expiration, $token, $hmac ) = $cookie_elements;

  if ( $expiration < time() ) {
    return 0;
  }

  $user = $wpdb->get_row(
    $wpdb->prepare( "SELECT * FROM $wpdb->users WHERE `user_login`=%s", $username ),
    'OBJECT'
  );

  if ( ! $user ) {
    return 0;
  }

  $pass_frag = substr( $user->user_pass, 8, 4 );
  $key = ffcnr_hash( $username . '|' . $pass_frag . '|' . $expiration . '|' . $token );
  $algo = function_exists( 'hash' ) ? 'sha256' : 'sha1';
  $hash = hash_hmac( $algo, $username . '|' . $expiration . '|' . $token, $key );

  if ( ! hash_equals( $hash, $hmac ) ) {
    return 0;
  }

  $user_options = $wpdb->get_results(
    $wpdb->prepare(
      "SELECT `meta_key`, `meta_value` FROM {$wpdb->usermeta}
      WHERE `user_id` = %d AND `meta_key` IN ( 'session_tokens', %s )",
      $user->ID,
      "{$site_prefix}capabilities"
    ),
    OBJECT_K
  );

  if ( ! $user_options ) {
    return 0;
  }

  $sessions = maybe_unserialize( $user_options['session_tokens']->meta_value );
  $verifier = ffcnr_hash_token( $token );

  if ( ! isset( $sessions[ $verifier ] ) ) {
    return 0;
  }

  if ( $sessions[ $verifier ]['expiration'] < time() ) {
    return 0;
  }

  $role_caps = maybe_unserialize( $options[ "{$site_prefix}user_roles" ] );
  $user_caps = maybe_unserialize( $user_options[ "{$site_prefix}capabilities" ]->meta_value );
  $all_caps = [];
  $roles = [];

  foreach ( $user_caps as $key => $value ) {
    if ( isset( $role_caps[ $key ] ) && $value ) {
      $all_caps = array_merge( $all_caps, $role_caps[ $key ]['capabilities'] );
      $roles[] = $key;
    } else {
      $all_caps[ $key ] = $value;
    }
  }

  $user->caps = $all_caps;
  $user->roles = $roles;

  return apply_filters( 'ffcnr_get_current_user', $user, $cookie, $role_caps, $user_caps, $_blog_id );
}

/**
 * Loads meta fields for a given user ID.
 *
 * @since 5.xx.x
 * @global wpdb $wpdb  WordPress database object.
 *
 * @param int    $user_id   User ID.
 * @param string $filter    Optional. String to filter meta keys. Only keys
 *                          containing this string will be considered.
 * @param bool   $reload    Skip the static cache and query again.
 * @param string $meta_key  Optional. Check whether meta key is cached.
 *
 * @return array Array of meta data.
 */

function ffcnr_load_user_meta( $user_id, $filter = '', $reload = false, $meta_key = null ) {
  static $cache = [];

  if ( ! $reload && isset( $cache[ $user_id ] ) ) {
    if ( $meta_key ) {
      if ( isset( $cache[ $user_id ][ $meta_key ] ) ) {
        return $cache[ $user_id ];
      }
    } else {
      return $cache[ $user_id ];
    }
  }

  global $wpdb;

  $query = $wpdb->prepare(
    "SELECT `meta_key`, `meta_value` FROM $wpdb->usermeta WHERE `user_id` = %d",
    $user_id
  );

  if ( ! empty( $filter ) ) {
    $query .= $wpdb->prepare( " AND `meta_key` LIKE %s", '%' . $wpdb->esc_like( $filter ) . '%' );
  }

  $result = $wpdb->get_results( $query, OBJECT_K );

  $meta = [];

  foreach ( $result as $key => $value ) {
    $meta[ $key ] = maybe_unserialize( $value->meta_value ) ?? '';
  }

  $cache[ $user_id ] = $meta;

  return $meta;
}

/**
 * Returns a meta field.
 *
 * Note: Alternative to get_user_meta().
 *
 * @since 5.xx.x
 *
 * @param int    $user_id  User ID.
 * @param string $meta_key Meta key.
 * @param string $filter   Optional. String to filter meta keys. Only keys
 *                         containing this string will be queried. Passed
 *                         on to ffcnr_load_user_meta().
 *
 * @return mixed The meta field value or an empty string if not found.
 */

function ffcnr_get_user_meta( $user_id, $meta_key, $filter = '' ) {
  $meta = ffcnr_load_user_meta( $user_id, $filter, false, $meta_key );
  $value = apply_filters( 'ffcnr_get_user_meta', $meta[ $meta_key ] ?? '', $user_id, $meta_key, $filter );

  return $value;
}

/**
 * Update or insert a meta field.
 *
 * Note: Alternative to update_user_meta().
 *
 * @since 5.xx.x
 * @global wpdb $wpdb  WordPress database object.
 *
 * @param int    $user_id     User ID.
 * @param string $meta_key    Meta key.
 * @param mixed  $meta_value  The value to update or insert.
 *
 * @return bool True if successful, false if not.
 */

function ffcnr_update_user_meta( $user_id, $meta_key, $meta_value ) {
  global $wpdb;

  $user_id = absint( $user_id );
  $meta_key = sanitize_key( $meta_key );
  $meta_value = apply_filters( 'ffcnr_update_user_meta', $meta_value, $user_id, $meta_key );

  $umeta_id = $wpdb->get_var(
    $wpdb->prepare(
      "SELECT `umeta_id` FROM {$wpdb->usermeta} WHERE `user_id` = %d AND `meta_key` = %s",
      $user_id,
      $meta_key
    )
  );

  if ( $umeta_id ) {
    $updated = $wpdb->update(
      $wpdb->usermeta,
      array( 'meta_value' => maybe_serialize( $meta_value ) ),
      array( 'umeta_id' => $umeta_id ),
      ['%s'],
      ['%d']
    );

    return $updated !== false;
  } else {
    $inserted = $wpdb->insert(
      $wpdb->usermeta,
      array(
        'user_id' => $user_id,
        'meta_key' => $meta_key,
        'meta_value' => maybe_serialize( $meta_value )
      ),
      ['%d', '%s', '%s']
    );

    return $inserted !== false;
  }
}

// =============================================================================
// CHILD THEME
// =============================================================================

/**
 * Includes a ffcnr-functions.php file from the active theme.
 *
 * @since 5.xx.x
 *
 * @return bool True if included, false if not.
 */

function ffcnr_load_child_theme_functions() {
  $options = ffcnr_load_options();

  $dir = ABSPATH . 'wp-content/themes/' . $options['stylesheet'];
  $path = $dir . '/ffcnr-functions.php';

  if ( file_exists( $path ) ) {
    include_once $path;

    define( 'CHILD_FUNCTIONS_LOADED', true );
    return true;
  }

  define( 'CHILD_FUNCTIONS_LOADED', false );
  return false;
}

ffcnr_load_child_theme_functions();

// =============================================================================
// DELEGATE TO REQUEST
// =============================================================================

$action = apply_filters( 'ffcnr_request_action', $_REQUEST['action'] ?? 0 );

if ( $action === 'auth' ) {
  require_once __DIR__ . '/_auth.php';
}
