<?php

// =============================================================================
// LOAD CHECKMARKS
// =============================================================================

if ( ! function_exists( 'fictioneer_load_checkmarks' ) ) {
  /**
   * Returns an user's Checkmarks
   *
   * Get an user's Checkmarks array from the database or creates a new one if it
   * does not yet exist.
   *
   * @since 5.0.0
   *
   * @param WP_User $user  User to get the checkmarks for.
   *
   * @return array Checkmarks.
   */

  function fictioneer_load_checkmarks( $user ) {
    // Setup
    $checkmarks = get_user_meta( $user->ID, 'fictioneer_user_checkmarks', true );
    $timestamp = time() * 1000;

    // Validate/Initialize
    if ( empty( $checkmarks ) || ! is_array( $checkmarks ) || ! array_key_exists( 'data', $checkmarks ) ) {
      $checkmarks = array( 'data' => [], 'updated' => $timestamp );
      update_user_meta( $user->ID, 'fictioneer_user_checkmarks', $checkmarks );
    }

    if ( ! array_key_exists( 'updated', $checkmarks ) ) {
      $checkmarks['updated'] = $timestamp;
      update_user_meta( $user->ID, 'fictioneer_user_checkmarks', $checkmarks );
    }

    // Return
    return $checkmarks;
  }
}

// =============================================================================
// GET FINISHED CHECKMARKS
// =============================================================================

if ( ! function_exists( 'fictioneer_get_finished_checkmarks' ) ) {
  /**
   * Returns array of story IDs marked as finished
   *
   * @since 5.0.0
   *
   * @param array $checkmarks  The user's checkmarks.
   *
   * @return array IDs of stories marked as finished.
   */

  function fictioneer_get_finished_checkmarks( $checkmarks ) {
    // Setup
    $complete_ids = [];

    // If story ID is inside the story node, the story is marked completed
    foreach ( $checkmarks['data'] as $key => $value ) {
      if ( in_array( $key, $value ) ) {
        $complete_ids[] = $key;
      }
    }

    // Return result
    return $complete_ids;
  }
}

// =============================================================================
// COUNT CHAPTER CHECKMARKS
// =============================================================================

if ( ! function_exists( 'fictioneer_count_chapter_checkmarks' ) ) {
  /**
   * Returns the total number of chapter checkmarks
   *
   * @since 5.0.0
   *
   * @param array $checkmarks  The user's checkmarks.
   *
   * @return int Total number of chapter checkmarks.
   */

  function fictioneer_count_chapter_checkmarks( $checkmarks ) {
    // Setup
    $count = 0;

    // Count all chapter IDs but not story IDs
    foreach ( $checkmarks['data'] as $story_id => $story_checkmarks ) {
      $count += count( $story_checkmarks );

      if ( in_array( $story_id, $story_checkmarks ) ) {
        $count--;
      }
    }

    // Return result
    return $count;
  }
}

// =============================================================================
// SET CHECKMARKS - AJAX
// =============================================================================

/**
 * Set Checkmarks for a story via AJAX
 *
 * @since 4.0.0
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
 * @see fictioneer_get_validated_ajax_user()
 * @see fictioneer_validate_id()
 * @see fictioneer_get_story_data()
 * @see fictioneer_load_checkmarks()
 */

function fictioneer_ajax_set_checkmark() {
  // Rate limit
  fictioneer_check_rate_limit( 'fictioneer_ajax_set_checkmark', 30 );

  // Setup and validations
  $user = fictioneer_get_validated_ajax_user();

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => __( 'Request did not pass validation.', 'fictioneer' ) ) );
  }

  if ( empty( $_POST['story_id'] ) ) {
    wp_send_json_error( array( 'error' => __( 'Missing arguments.', 'fictioneer' ) ) );
  }

  $story_id = fictioneer_validate_id( $_POST['story_id'], 'fcn_story' );

  if ( ! $story_id ) {
    wp_send_json_error( array( 'error' => __( 'Invalid story ID.', 'fictioneer' ) ) );
  }

  $story_data = fictioneer_get_story_data( $story_id, false ); // Does not refresh comment count!

  // Prepare update
  $update = isset( $_POST['update'] ) ? explode( ' ', sanitize_text_field( $_POST['update'] ) ) : [];
  $update_ids = [];

  // Check update...
  if ( in_array( $story_id, $update ) ) {
    // If story ID in update, add all chapters and mark story as read
    $update_ids = array_map( function ( $a ) { return absint( $a ); }, $story_data['chapter_ids'] );
    $update_ids[] = absint( $story_id );
  } else {
    // Check if chapter IDs are part of the story
    foreach ( $update as $chapter_id ) {
      $chapter_id = absint( $chapter_id );

      if ( in_array( $chapter_id, $story_data['chapter_ids'] ) ) {
        $update_ids[] = $chapter_id;
      }
    }
  }

  // Prepare Checkmarks
  $checkmarks = fictioneer_load_checkmarks( $user );
  $checkmarks['updated'] = time() * 1000; // Compatible with Date.now() in JavaScript

  // Update checkmarks
  $checkmarks['data'][ $story_id ] = empty( $_POST['update'] ) ? [] : $update_ids;

  // Update user
  if ( update_user_meta( $user->ID, 'fictioneer_user_checkmarks', $checkmarks ) ) {
    wp_send_json_success();
  } else {
    wp_send_json_error( array( 'error' => __( 'Database error. Checkmarks could not be updated.', 'fictioneer' ) ) );
  }
}

if ( get_option( 'fictioneer_enable_checkmarks' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_set_checkmark', 'fictioneer_ajax_set_checkmark' );
}

// =============================================================================
// CLEAR MY CHECKMARKS - AJAX
// =============================================================================

/**
 * Clears Checkmarks for a story via AJAX
 *
 * @since 5.0.0
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
 * @see fictioneer_get_validated_ajax_user()
 */

function fictioneer_ajax_clear_my_checkmarks() {
  // Rate limit
  fictioneer_check_rate_limit( 'fictioneer_ajax_clear_my_checkmarks' );

  // Setup and validations
  $user = fictioneer_get_validated_ajax_user( 'nonce', 'fictioneer_clear_checkmarks' );

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => __( 'Request did not pass validation.', 'fictioneer' ) ) );
  }

  // Update user
  if ( delete_user_meta( $user->ID, 'fictioneer_user_checkmarks' ) ) {
    wp_send_json_success( array( 'success' => __( 'Data has been cleared.', 'fictioneer' ) ) );
  } else {
    wp_send_json_error( array( 'error' => __( 'Database error. Checkmarks could not be updated.', 'fictioneer' ) ) );
  }
}

if ( get_option( 'fictioneer_enable_checkmarks' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_clear_my_checkmarks', 'fictioneer_ajax_clear_my_checkmarks' );
}

// =============================================================================
// GET FINISHED LIST - AJAX
// =============================================================================

/**
 * Sends the HTML for list of finished stories via AJAX
 *
 * @since 4.3.0
 */

function fictioneer_ajax_get_finished_checkmarks_list() {
  // Validations
  $user = fictioneer_get_validated_ajax_user();

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => __( 'Request did not pass validation.', 'fictioneer' ) ) );
  }

  // Setup
  $checkmarks = fictioneer_load_checkmarks( $user );
  $post_ids = fictioneer_get_finished_checkmarks( $checkmarks );
  $page = absint( $_GET['page'] ?? 1 );
  $order = strtolower( $_GET['order'] ?? 'desc' );
  $order = in_array( $order, ['desc', 'asc'] ) ? $order : 'desc';

  // Query
  $list_items = fictioneer_get_card_list(
    'story',
    array(
      'fictioneer_query_name' => 'bookshelf_finished',
      'post__in' => $post_ids,
      'paged' => $page,
      'order' => $order
    ),
    __( 'You have not marked any stories as finished.', 'fictioneer' ),
    array( 'show_latest' => true )
  );

  // Total number of pages
  $max_pages = $list_items['query']->max_num_pages ?? 1;

  // Navigation (if any)
  $navigation = '';

  if ( $max_pages > 1 ) {
    ob_start();
    // Start HTML ---> ?>
    <li class="pagination bookshelf-pagination">
      <?php
        for ( $i = 1; $i <= $max_pages; $i++ ) {
          if ( $i == $page ) {
            ?><span class="page-numbers current" aria-current="page"><?php echo $i; ?></span><?php
          } else {
            ?><button class="page-numbers" data-page="<?php echo $i; ?>"><?php echo $i; ?></button><?php
          }
        }
      ?>
    </li>
    <?php // <--- End HTML

    // Get buffered navigation
    $navigation = ob_get_clean();
  } elseif ( $page > 1 ) {
    ob_start();
    // Start HTML ---> ?>
    <li class="pagination bookshelf-pagination">
      <button class="page-numbers" data-page="1"><?php _e( 'First Page', 'fictioneer' ); ?></button>
    </li>
    <?php // <--- End HTML

    // Get buffered back button
    $navigation = ob_get_clean();
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

if ( get_option( 'fictioneer_enable_checkmarks' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_get_finished_checkmarks_list', 'fictioneer_ajax_get_finished_checkmarks_list' );
}

?>
