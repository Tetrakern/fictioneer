<?php

// =============================================================================
// LOAD REMINDERS
// =============================================================================

if ( ! function_exists( 'fictioneer_load_reminders' ) ) {
  /**
   * Returns an user's Reminders
   *
   * Get an user's Reminders array from the database or creates a new one if it
   * does not yet exist.
   *
   * @since 5.0.0
   *
   * @param WP_User $user  User to get the Reminders for.
   *
   * @return array Reminders.
   */

  function fictioneer_load_reminders( $user ) {
    // Setup
    $reminders = get_user_meta( $user->ID, 'fictioneer_user_reminders', true );
    $timestamp = time() * 1000; // Compatible with Date.now() in JavaScript

    // Validate/Initialize
    if ( empty( $reminders ) || ! is_array( $reminders ) || ! array_key_exists( 'data', $reminders ) ) {
      $reminders = array( 'data' => [], 'updated' => $timestamp );
      update_user_meta( $user->ID, 'fictioneer_user_reminders', $reminders );
    }

    if ( ! array_key_exists( 'updated', $reminders ) ) {
      $reminders['updated'] = $timestamp;
      update_user_meta( $user->ID, 'fictioneer_user_reminders', $reminders );
    }

    // Return
    return $reminders;
  }
}

// =============================================================================
// AJAX REQUESTS
// > Return early if no AJAX functions are required.
// =============================================================================

if ( ! wp_doing_ajax() ) {
  return;
}

// =============================================================================
// TOGGLE REMINDER - AJAX
// =============================================================================

/**
 * Set Reminder for a story via AJAX
 *
 *
 * @since 5.0.0
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
 * @see fictioneer_get_validated_ajax_user()
 */

function fictioneer_ajax_toggle_reminder() {
  // Rate limit
  fictioneer_check_rate_limit( 'fictioneer_ajax_toggle_reminder' );

  // Setup and validations
  $user = fictioneer_get_validated_ajax_user();

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => 'Request did not pass validation.' ) );
  }

  if ( empty( $_POST['story_id'] ) || empty( $_POST['set'] ) ) {
    wp_send_json_error( array( 'error' => 'Missing arguments.' ) );
  }

  // Valid story ID?
  $story_id = fictioneer_validate_id( $_POST['story_id'], 'fcn_story' );

  if ( ! $story_id ) {
    wp_send_json_error( array( 'error' => 'Invalid story ID.' ) );
  }

  // Set or unset?
  $set = $_POST['set'] == 'true';

  // Prepare Reminders
  $timestamp = time() * 1000; // Compatible with Date.now() in JavaScript
  $user_reminders = fictioneer_load_reminders( $user );
  $user_reminders['updated'] = $timestamp;

  // Add/Remove story from Reminders
  if ( ! array_key_exists( $story_id, $user_reminders['data'] ) || $set ) {
    $item = array(
      'story_id' => $story_id,
      'timestamp' => $timestamp
    );

    $user_reminders['data'][ $story_id ] = $item;
  } else {
    unset( $user_reminders['data'][ $story_id ] );
  }

  // Update database & response
  if ( update_user_meta( $user->ID, 'fictioneer_user_reminders', $user_reminders ) ) {
    do_action( 'fictioneer_toggled_reminder', $story_id, $set );
    wp_send_json_success();
  } {
    wp_send_json_error( array( 'error' => 'Reminders could not be updated.' ) );
  }
}

if ( get_option( 'fictioneer_enable_reminders' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_toggle_reminder', 'fictioneer_ajax_toggle_reminder' );
}

// =============================================================================
// CLEAR MY REMINDERS - AJAX
// =============================================================================

/**
 * Clears an user's Reminders via AJAX
 *
 * @since 5.0.0
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
 * @see fictioneer_get_validated_ajax_user()
 */

function fictioneer_ajax_clear_my_reminders() {
  // Rate limit
  fictioneer_check_rate_limit( 'fictioneer_ajax_clear_my_reminders' );

  // Setup and validations
  $user = fictioneer_get_validated_ajax_user( 'nonce', 'fictioneer_clear_reminders' );

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => 'Request did not pass validation.' ) );
  }

  // Update user
  if ( delete_user_meta( $user->ID, 'fictioneer_user_reminders' ) ) {
    wp_send_json_success( array( 'success' => __( 'Data has been cleared.', 'fictioneer' ) ) );
  } else {
    wp_send_json_error( array( 'failure' => __( 'Database error. Reminders could not be cleared.', 'fictioneer' ) ) );
  }
}

if ( get_option( 'fictioneer_enable_reminders' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_clear_my_reminders', 'fictioneer_ajax_clear_my_reminders' );
}

// =============================================================================
// GET REMINDERS LIST - AJAX
// =============================================================================

/**
 * Sends the HTML for list of remembered stories via AJAX
 *
 * @since 4.3.0
 */

function fictioneer_ajax_get_reminders_list() {
  // Validations
  $user = fictioneer_get_validated_ajax_user();

  if ( ! is_user_logged_in() ) {
    wp_send_json_error( array( 'error' => 'You must be logged in.' ) );
  }

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => 'Request did not pass validation.' ) );
  }

  // Setup
  $reminders = fictioneer_load_reminders( $user );
  $post_ids = array_keys( $reminders['data'] );
  $page = absint( $_GET['page'] ?? 1 );
  $order = strtolower( $_GET['order'] ?? 'desc' );
  $order = in_array( $order, ['desc', 'asc'] ) ? $order : 'desc';

  // Query
  $list_items = fictioneer_get_card_list(
    'story',
    array(
      'fictioneer_query_name' => 'bookshelf_reminders',
      'post__in' => $post_ids,
      'paged' => $page,
      'order' => $order
    ),
    __( 'You have not marked any stories to be read later.', 'fictioneer' ),
    array( 'show_latest' => true )
  );

  // Total number of pages
  $max_pages = $list_items['query']->max_num_pages ?? 1;

  // Navigation (if any)
  $navigation = '';

  if ( $max_pages > 1 ) {
    $navigation = '<li class="pagination bookshelf-pagination _reminders">';

    for ( $i = 1; $i <= $max_pages; $i++ ) {
      if ( $i == $page ) {
        $navigation .= '<span class="page-numbers current" aria-current="page">' . $i . '</span>';
      } else {
        $navigation .= '<button class="page-numbers" data-page="' . $i . '">' . $i . '</button>';
      }
    }

    $navigation .= '</li>';
  } elseif ( $page > 1 ) {
    $navigation = sprintf(
      '<li class="pagination bookshelf-pagination _reminders"><button class="page-numbers" data-page="1">%s</button></li>',
      __( 'First Page', 'fictioneer' )
    );
  }

  // Send result
  wp_send_json_success(
    array(
      'html' => $list_items['html'] . $navigation,
      'count' => count( $post_ids ),
      'maxPages' => $max_pages
    )
  );
}

if ( get_option( 'fictioneer_enable_reminders' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_get_reminders_list', 'fictioneer_ajax_get_reminders_list' );
}
