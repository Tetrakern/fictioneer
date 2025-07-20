<?php

// =============================================================================
// LOAD FOLLOWS
// =============================================================================

if ( ! function_exists( 'fictioneer_load_follows' ) ) {
  /**
   * Returns a user's Follows
   *
   * Get a user's Follows array from the database or creates a new one if it
   * does not yet exist.
   *
   * @since 5.0.0
   *
   * @param WP_User $user  User to get the Follows for.
   *
   * @return array Follows.
   */

  function fictioneer_load_follows( $user ) {
    // Setup
    $follows = get_user_meta( $user->ID, 'fictioneer_user_follows', true );
    $timestamp = time() * 1000;

    // Validate/Initialize
    if ( empty( $follows ) || ! is_array( $follows ) || ! array_key_exists( 'data', $follows ) ) {
      $follows = array( 'data' => [], 'seen' => $timestamp, 'updated' => $timestamp );
      update_user_meta( $user->ID, 'fictioneer_user_follows', $follows );
    }

    if ( ! array_key_exists( 'updated', $follows ) ) {
      $follows['updated'] = $timestamp;
      update_user_meta( $user->ID, 'fictioneer_user_follows', $follows );
    }

    if ( ! array_key_exists( 'seen', $follows ) ) {
      $follows['seen'] = $timestamp;
      update_user_meta( $user->ID, 'fictioneer_user_follows', $follows );
    }

    // Return
    return $follows;
  }
}

// =============================================================================
// QUERY CHAPTERS OF FOLLOWED STORIES
// =============================================================================

if ( ! function_exists( 'fictioneer_query_followed_chapters' ) ) {
  /**
   * Query chapters of followed stories
   *
   * @since 4.3.0
   *
   * @param array       $story_ids   IDs of the followed stories.
   * @param string|null $after_date  Optional. Only return chapters after this date,
   *                                 e.g. wp_date( 'Y-m-d H:i:s', $timestamp, 'gmt' ).
   * @param int         $count       Optional. Maximum number of chapters to be returned. Default 20.
   *
   * @return array Collection of chapters.
   */

  function fictioneer_query_followed_chapters( $story_ids, $after_date = null, $count = 20 ) {
    // Setup
    $query_args = array (
      'post_type' => 'fcn_chapter',
      'post_status' => 'publish',
      'meta_query' => array(
        array(
          'key' => 'fictioneer_chapter_story',
          'value' => $story_ids,
          'compare' => 'IN',
        )
      ),
      'numberposts' => $count,
      'orderby' => 'date',
      'order' => 'DESC',
      'update_post_meta_cache' => true,
      'update_post_term_cache' => false, // Improve performance
      'no_found_rows' => true // Improve performance
    );

    if ( $after_date ) {
      $query_args['date_query'] = array( 'column' => 'post_date_gmt', 'after' => $after_date );
    }

    // Query
    $all_chapters = get_posts( $query_args );

    // Filter out hidden chapters (faster than meta query, highly unlikely to eliminate all)
    $chapters = array_filter( $all_chapters, function ( $candidate ) {
      // Chapter hidden?
      $chapter_hidden = get_post_meta( $candidate->ID, 'fictioneer_chapter_hidden', true );

      // Only keep if not hidden
      return empty( $chapter_hidden ) || $chapter_hidden === '0';
    });

    // Return final results
    return $chapters;
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
// TOGGLE FOLLOW - AJAX
// =============================================================================

/**
 * Toggle Follow for a story via AJAX
 *
 * @since 4.3.0
 */

function fictioneer_ajax_toggle_follow() {
  // Rate limit
  fictioneer_check_rate_limit( 'fictioneer_ajax_toggle_follow' );

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

  // Prepare Follows
  $timestamp = time() * 1000; // Compatible with Date.now() in JavaScript
  $user_follows = fictioneer_load_follows( $user );
  $user_follows['updated'] = $timestamp;

  // Add/Remove story from Follows
  if ( ! array_key_exists( $story_id, $user_follows['data'] ) || $set ) {
    $item = array(
      'story_id' => $story_id,
      'timestamp' => $timestamp
    );

    $user_follows['data'][ $story_id ] = $item;
  } else {
    unset( $user_follows['data'][ $story_id ] );
  }

  // Update database & response
  if ( update_user_meta( $user->ID, 'fictioneer_user_follows', $user_follows ) ) {
    do_action( 'fictioneer_toggled_follow', $story_id, $set );
    wp_send_json_success();
  } else {
    wp_send_json_error( array( 'error' => 'Follows could not be updated.' ) );
  }
}

if ( get_option( 'fictioneer_enable_follows' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_toggle_follow', 'fictioneer_ajax_toggle_follow' );
}

// =============================================================================
// CLEAR MY FOLLOWS - AJAX
// =============================================================================

/**
 * Clears a user's Follows via AJAX
 *
 * @since 5.0.0
 */

function fictioneer_ajax_clear_my_follows() {
  // Rate limit
  fictioneer_check_rate_limit( 'fictioneer_ajax_clear_my_follows' );

  // Setup and validations
  $user = fictioneer_get_validated_ajax_user( 'nonce', 'fictioneer_clear_follows' );

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => 'Request did not pass validation.' ) );
  }

  // Update user
  if ( delete_user_meta( $user->ID, 'fictioneer_user_follows' ) ) {
    wp_send_json_success( array( 'success' => __( 'Data has been cleared.', 'fictioneer' ) ) );
  } else {
    wp_send_json_error( array( 'failure' => __( 'Database error. Follows could not be cleared.', 'fictioneer' ) ) );
  }
}

if ( get_option( 'fictioneer_enable_follows' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_clear_my_follows', 'fictioneer_ajax_clear_my_follows' );
}

// =============================================================================
// GET FOLLOWS LIST - AJAX
// =============================================================================

/**
 * Sends the HTML for list of followed stories via AJAX
 *
 * @since 4.3.0
 */

function fictioneer_ajax_get_follows_list() {
  // Validations
  $user = fictioneer_get_validated_ajax_user();

  if ( ! is_user_logged_in() ) {
    wp_send_json_error( array( 'error' => 'You must be logged in.' ) );
  }

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => 'Request did not pass validation.' ) );
  }

  // Setup
  $follows = fictioneer_load_follows( $user );
  $post_ids = array_keys( $follows['data'] );
  $page = absint( $_GET['page'] ?? 1 );
  $order = strtolower( $_GET['order'] ?? 'desc' );
  $order = in_array( $order, ['desc', 'asc'] ) ? $order : 'desc';

  // Query
  $list_items = fictioneer_get_card_list(
    'story',
    array(
      'fictioneer_query_name' => 'bookshelf_follows',
      'post__in' => $post_ids,
      'paged' => $page,
      'order' => $order
    ),
    __( 'You are not following any stories.', 'fictioneer' ),
    array( 'show_latest' => true )
  );

  if ( ! $list_items ) {
    wp_send_json_error( array( 'error' => 'Card list could not be queried.' ) );
  }

  // Total number of pages
  $max_pages = $list_items['query']->max_num_pages ?? 1;

  // Navigation (if any)
  $navigation = '';

  if ( $max_pages > 1 ) {
    $navigation = '<li class="pagination bookshelf-pagination _follows">';

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
      '<li class="pagination bookshelf-pagination _follows"><button class="page-numbers" data-page="1">%s</button></li>',
      __( 'First Page', 'fictioneer' )
    );
  }

  // Send result
  wp_send_json_success(
    array(
      'html' => fictioneer_minify_html( $list_items['html'] ) . $navigation,
      'count' => count( $post_ids ),
      'maxPages' => $max_pages
    )
  );
}

if ( get_option( 'fictioneer_enable_follows' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_get_follows_list', 'fictioneer_ajax_get_follows_list' );
}
