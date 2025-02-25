<?php

// No direct access!
defined( 'ABSPATH' ) OR exit;

// =============================================================================
// REPLACEMENT FUNCTIONS
// =============================================================================

/**
 * Returns the user avatar URL.
 *
 * @since 5.27.0
 *
 * @param stdClass $user  User object.
 * @param int      $size  Optional. Size of the avatar. Default 96.
 *
 * @return string The URL or an empty string.
 */

function ffcnr_get_avatar_url( $user, $size = 96 ) {
  if ( ! $user ) {
    return '';
  }

  $options = ffcnr_load_options( ['avatar_default'] );
  $default = $options['avatar_default'] ?? 'mystery';
  $meta = ffcnr_load_user_meta( $user->ID, 'fictioneer' );
  $email = $user->user_email ?? 'nonexistentemail@example.com';
  $disabled = ( $meta['fictioneer_disable_avatar'] ?? 0 ) || ( $meta['fictioneer_admin_disable_avatar'] ?? 0 );

  $filtered = apply_filters( 'ffcnr_get_avatar_url', '', $user, $size, $meta, $options );

  if ( ! empty( $filtered ) ) {
    return $filtered;
  }

  // Custom avatar
  if (
    ! $disabled &&
    ! ( $meta['fictioneer_enforce_gravatar'] ?? 0 ) &&
    ( $meta['fictioneer_external_avatar_url'] ?? 0 )
  ) {
    return $meta['fictioneer_external_avatar_url'];
  }

  // Gravatar
  if ( $email ) {
    $gravatar_styles = array(
      'mystery' => 'mm',
      'blank' => 'blank',
      'gravatar_default' => '',
      'identicon' => 'identicon',
      'wavatar' => 'wavatar',
      'monsterid' => 'monsterid',
      'retro' => 'retro'
    );

    $default = $gravatar_styles[ $default ];
    $email_hash = $disabled ? 'foobar' : md5( mb_strtolower( trim( $user->user_email ) ) );

    return "https://www.gravatar.com/avatar/{$email_hash}?s={$size}&d={$default}";
  }
}

/**
 * Checks if a user is an administrator.
 *
 * @since 5.27.0
 *
 * @param stdClass $user  User object.
 *
 * @return boolean To be or not to be.
 */

function ffcnr_is_admin( $user ) {
  return $user->caps['manage_options'] ?? false;
}

/**
 * Checks if a user is an author.
 *
 * @since 5.27.0
 *
 * @param stdClass $user  User object.
 *
 * @return boolean To be or not to be.
 */

function ffcnr_is_author( $user ) {
  return ($user->caps['publish_posts'] ?? false) ||
    ($user->caps['publish_fcn_stories'] ?? false) ||
    ($user->caps['publish_fcn_chapters'] ?? false) ||
    ($user->caps['publish_fcn_collections'] ?? false);
}

/**
 * Checks if a user is a moderator.
 *
 * @since 5.27.0
 *
 * @param stdClass $user  User object.
 *
 * @return boolean To be or not to be.
 */

function ffcnr_is_moderator( $user ) {
  return $user->caps['moderate_comments'] ?? false;
}

/**
 * Checks if a user is an editor.
 *
 * @since 5.27.0
 *
 * @param stdClass $user  User object.
 *
 * @return boolean To be or not to be.
 */

function ffcnr_is_editor( $user ) {
  return $user->caps['edit_others_posts'] ?? false;
}

/**
 * Returns a user's Follows
 *
 * @since 5.27.0
 * @see includes/functions/users/_follows.php
 *
 * @param stdClass $user  User to get the Follows for.
 *
 * @return array Follows.
 */

function fictioneer_load_follows( $user ) {
  // Setup
  $follows = ffcnr_get_user_meta( $user->ID, 'fictioneer_user_follows', 'fictioneer' );
  $timestamp = time() * 1000;

  // Validate/Initialize
  if ( empty( $follows ) || ! is_array( $follows ) || ! array_key_exists( 'data', $follows ) ) {
    $follows = array( 'data' => [], 'seen' => $timestamp, 'updated' => $timestamp );
    ffcnr_update_user_meta( $user->ID, 'fictioneer_user_follows', $follows );
  }

  if ( ! array_key_exists( 'updated', $follows ) ) {
    $follows['updated'] = $timestamp;
    ffcnr_update_user_meta( $user->ID, 'fictioneer_user_follows', $follows );
  }

  if ( ! array_key_exists( 'seen', $follows ) ) {
    $follows['seen'] = $timestamp;
    ffcnr_update_user_meta( $user->ID, 'fictioneer_user_follows', $follows );
  }

  // Return
  return $follows;
}

/**
 * Query count of new chapters for followed stories
 *
 * @since 5.27.0
 * @see includes/functions/users/_follows.php
 *
 * @param array       $story_ids   IDs of the followed stories.
 * @param string|null $after_date  Optional. Only return chapters after this date,
 *                                 e.g. wp_date( 'Y-m-d H:i:s', $timestamp, 'gmt' ).
 * @param int         $count       Optional. Maximum number of chapters. Default 99.
 *
 * @return array Number of new chapters found.
 */

function fictioneer_query_new_followed_chapters_count( $story_ids, $after_date = null, $count = 99 ) {
  global $wpdb;

  $story_ids = array_map( 'absint', $story_ids );

  if ( empty( $story_ids ) ) {
    return 0;
  }

  $story_ids_placeholder = implode( ',', array_fill( 0, count( $story_ids ), '%d' ) );

  $sql = "
    SELECT COUNT(p.ID) as count
    FROM {$wpdb->posts} p
    INNER JOIN {$wpdb->postmeta} pm_story ON p.ID = pm_story.post_id
    LEFT JOIN {$wpdb->postmeta} pm_hidden ON p.ID = pm_hidden.post_id AND pm_hidden.meta_key = 'fictioneer_chapter_hidden'
    WHERE p.post_type = 'fcn_chapter'
      AND p.post_status = 'publish'
      AND pm_story.meta_key = 'fictioneer_chapter_story'
      AND pm_story.meta_value IN ({$story_ids_placeholder})
      AND (pm_hidden.meta_key IS NULL OR pm_hidden.meta_value = '0')
  ";

  if ( $after_date ) {
    $sql .= " AND p.post_date_gmt > %s";
  }

  $query_args = array_merge( $story_ids, $after_date ? [ $after_date ] : [] );

  return min( (int) $wpdb->get_var( $wpdb->prepare( $sql, $query_args ) ), $count );
}

/**
 * Returns a user's Reminders
 *
 * Get a user's Reminders array from the database or creates a new one if it
 * does not yet exist.
 *
 * @since 5.27.0
 * @see includes/functions/users/_reminders.php
 *
 * @param stdClass $user  User to get the Reminders for.
 *
 * @return array Reminders.
 */

function fictioneer_load_reminders( $user ) {
  // Setup
  $reminders = ffcnr_get_user_meta( $user->ID, 'fictioneer_user_reminders', 'fictioneer' );
  $timestamp = time() * 1000; // Compatible with Date.now() in JavaScript

  // Validate/Initialize
  if ( empty( $reminders ) || ! is_array( $reminders ) || ! array_key_exists( 'data', $reminders ) ) {
    $reminders = array( 'data' => [], 'updated' => $timestamp );
    ffcnr_update_user_meta( $user->ID, 'fictioneer_user_reminders', $reminders );
  }

  if ( ! array_key_exists( 'updated', $reminders ) ) {
    $reminders['updated'] = $timestamp;
    ffcnr_update_user_meta( $user->ID, 'fictioneer_user_reminders', $reminders );
  }

  // Return
  return $reminders;
}

/**
 * Returns a user's Checkmarks
 *
 * Get a user's Checkmarks array from the database or creates a new one if it
 * does not yet exist.
 *
 * @since 5.27.0
 * @see includes/functions/users/_checkmarks.php
 *
 * @param stdClass $user  User to get the checkmarks for.
 *
 * @return array Checkmarks.
 */

function fictioneer_load_checkmarks( $user ) {
  // Setup
  $checkmarks = ffcnr_get_user_meta( $user->ID, 'fictioneer_user_checkmarks', 'fictioneer' );
  $timestamp = time() * 1000;

  // Validate/Initialize
  if ( empty( $checkmarks ) || ! is_array( $checkmarks ) || ! array_key_exists( 'data', $checkmarks ) ) {
    $checkmarks = array( 'data' => [], 'updated' => $timestamp );
    ffcnr_update_user_meta( $user->ID, 'fictioneer_user_checkmarks', $checkmarks );
  }

  if ( ! array_key_exists( 'updated', $checkmarks ) ) {
    $checkmarks['updated'] = $timestamp;
    ffcnr_update_user_meta( $user->ID, 'fictioneer_user_checkmarks', $checkmarks );
  }

  // Return
  return $checkmarks;
}

/**
 * Returns an unique MD5 hash for the user.
 *
 * @since 5.27.0
 * @see includes/functions/_helpers-users.php
 *
 * @param stdClass $user  User to get the fingerprint for.
 *
 * @return string The unique fingerprint hash or empty string if not found.
 */

function fictioneer_get_user_fingerprint( $user ) {
  if ( ! $user ) {
    return '';
  }

  $fingerprint = ffcnr_get_user_meta( $user->ID, 'fictioneer_user_fingerprint', 'fictioneer' );

  if ( empty( $fingerprint ) ) {
    $fingerprint = md5( $user->user_login . $user->ID );
    ffcnr_update_user_meta( $user->ID, 'fictioneer_user_fingerprint', $fingerprint );
  }

  return $fingerprint;
}

// =============================================================================
// GET USER DATA
// =============================================================================

function ffcnr_get_user_data() {
  global $wpdb;

  // Load options
  $options = ffcnr_load_options([
    'fictioneer_enable_reminders', 'fictioneer_enable_checkmarks',
    'fictioneer_enable_bookmarks', 'fictioneer_enable_follows'
  ]);

  // Setup
  $user = ffcnr_get_current_user( $options );
  $logged_in = !!($user ? $user->ID : 0);
  $nonce = ffcnr_create_nonce( 'fictioneer_nonce', $logged_in ? $user->ID : 0 );
  $data = array(
    'user_id' => $logged_in ? $user->ID : 0,
    'timestamp' => time() * 1000, // Compatible with Date.now() in JavaScript
    'loggedIn' => $logged_in,
    'follows' => false,
    'reminders' => false,
    'checkmarks' => false,
    'bookmarks' => '{}',
    'fingerprint' => fictioneer_get_user_fingerprint( $user ),
    'avatarUrl' => '',
    'isAdmin' => false,
    'isModerator' => false,
    'isAuthor' => false,
    'isEditor' => false,
    'nonce' => $nonce,
    'nonceHtml' => '<input id="fictioneer-ajax-nonce" name="fictioneer-ajax-nonce" type="hidden" value="' . $nonce . '">'
  );

  if ( $logged_in ) {
    $data = array_merge(
      $data,
      array(
        'isAdmin' => ffcnr_is_admin( $user ),
        'isModerator' => ffcnr_is_moderator( $user ),
        'isAuthor' => ffcnr_is_author( $user ),
        'isEditor' => ffcnr_is_editor( $user ),
        'avatarUrl' => ffcnr_get_avatar_url( $user )
      )
    );
  }

  // --- FOLLOWS ---------------------------------------------------------------

  if ( $logged_in && $options['fictioneer_enable_follows'] ) {
    $follows = fictioneer_load_follows( $user );
    $follows['new'] = false;

    // New notifications?
    if ( count( $follows['data'] ) > 0 ) {
      $latest_count = fictioneer_query_new_followed_chapters_count(
        array_keys( $follows['data'] ),
        wp_date( 'Y-m-d H:i:s', $follows['seen'] / 1000, new DateTimeZone( 'UTC' ) )
      );

      if ( $latest_count > 0 ) {
        $follows['new'] = $latest_count;
      }
    }

    $data['follows'] = $follows;
  }

  // --- REMINDERS -------------------------------------------------------------

  if ( $logged_in && $options['fictioneer_enable_reminders'] ) {
    $data['reminders'] = fictioneer_load_reminders( $user );
  }

  // --- CHECKMARKS ------------------------------------------------------------

  if ( $logged_in && $options['fictioneer_enable_checkmarks'] ) {
    $data['checkmarks'] = fictioneer_load_checkmarks( $user );
  }

  // --- BOOKMARKS -------------------------------------------------------------

  if ( $logged_in && $options['fictioneer_enable_bookmarks'] ) {
    $bookmarks = ffcnr_get_user_meta( $user->ID, 'fictioneer_bookmarks', 'fictioneer' );
    $data['bookmarks'] = $bookmarks ? $bookmarks : '{}';
  }

  // --- FILTER ----------------------------------------------------------------

  $data = apply_filters( 'ffcnr_get_user_data', $data, $user );

  // ---------------------------------------------------------------------------

  $wpdb->db_connect();

  // Response
  header( 'Content-Type: application/json; charset=utf-8' );
  header( 'HTTP/1.1 200 OK' );
  echo json_encode( array( 'success' => true, 'data' => $data ) );
  exit;
}

ffcnr_get_user_data();
