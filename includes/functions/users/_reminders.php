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
   * @since Fictioneer 5.0
   *
   * @param WP_User $user User to get the Reminders for.
   *
   * @return array Reminders.
   */

  function fictioneer_load_reminders( $user ) {
    // Setup
    $reminders = get_user_meta( $user->ID, 'fictioneer_user_reminders', true );
    $timestamp = time() * 1000; // Compatible with Date.now() in JavaScript

    // Validate/Initialize
    if ( empty( $reminders ) || ! is_array( $reminders ) || ! array_key_exists( 'data', $reminders ) ) {
      $reminders = ['data' => [], 'updated' => $timestamp];
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
// GET REMINDERS - AJAX
// =============================================================================

if ( ! function_exists( 'fictioneer_ajax_get_reminders' ) ) {
  /**
   * Sends the user's Reminders as JSON via Ajax
   *
   * @since Fictioneer 5.0
   * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
   * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
   * @see fictioneer_get_validated_ajax_user()
   * @see fictioneer_load_reminders()
   */

  function fictioneer_ajax_get_reminders() {
    // Setup and validations
    $user = fictioneer_get_validated_ajax_user();
    if ( ! $user ) wp_send_json_error( ['error' => __( 'Request did not pass validation.', 'fictioneer' )] );

    // Prepare Reminders
    $reminders = fictioneer_load_reminders( $user );
    $reminders['timestamp'] = time() * 1000; // Compatible with Date.now() in JavaScript

    // Response
    wp_send_json_success( ['reminders' => json_encode( $reminders )] );
  }
}

if ( get_option( 'fictioneer_enable_reminders' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_get_reminders', 'fictioneer_ajax_get_reminders' );
}

// =============================================================================
// TOGGLE REMINDER - AJAX
// =============================================================================

if ( ! function_exists( 'fictioneer_ajax_toggle_reminder' ) ) {
  /**
   * Set Reminder for a story via AJAX
   *
   *
   * @since Fictioneer 5.0
   * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
   * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
   * @see fictioneer_get_validated_ajax_user()
   */

  function fictioneer_ajax_toggle_reminder() {
    // Setup and validations
    $user = fictioneer_get_validated_ajax_user();
    if ( ! $user ) wp_send_json_error( ['error' => __( 'Request did not pass validation.', 'fictioneer' )] );

    if ( empty( $_POST['story_id'] ) || empty( $_POST['set'] ) ) {
      wp_send_json_error( ['error' => __( 'Missing arguments.', 'fictioneer' )] );
    }

    // Valid story ID?
    $story_id = fictioneer_validate_id( $_POST['story_id'], 'fcn_story' );
    if ( ! $story_id ) wp_send_json_error( ['error' => __( 'Invalid story ID.', 'fictioneer' )] );

    // Set or unset?
    $set = $_POST['set'] == 'true';

    // Prepare Reminders
    $timestamp = time() * 1000; // Compatible with Date.now() in JavaScript
    $user_reminders = fictioneer_load_reminders( $user );
    $user_reminders['updated'] = $timestamp;

    // Add/Remove story from Reminders
    if ( ! array_key_exists( $story_id, $user_reminders['data'] ) || $set ) {
      $item = array(
        'user_id' => $user->ID,
        'story_id' => $story_id,
        'timestamp' => $timestamp
      );

      $user_reminders['data'][ $story_id ] = $item;
    } else {
      unset( $user_reminders['data'][ $story_id ] );
    }

    // Update database & response
    if ( update_user_meta( $user->ID, 'fictioneer_user_reminders', $user_reminders ) ) {
      wp_send_json_success();
    } {
      wp_send_json_error( ['error' => __( 'Database error. Reminders could not be updated.', 'fictioneer' )] );
    }
  }
}

if ( get_option( 'fictioneer_enable_reminders' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_toggle_reminder', 'fictioneer_ajax_toggle_reminder' );
}

// =============================================================================
// CLEAR MY REMINDERS - AJAX
// =============================================================================

if ( ! function_exists( 'fictioneer_ajax_clear_my_reminders' ) ) {
  /**
   * Clears an user's Reminders via AJAX
   *
   * @since Fictioneer 5.0
   * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
   * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
   * @see fictioneer_get_validated_ajax_user()
   */

  function fictioneer_ajax_clear_my_reminders() {
    // Setup and validations
    $user = fictioneer_get_validated_ajax_user( 'nonce', 'fictioneer_clear_reminders' );
    if ( ! $user ) wp_send_json_error( ['error' => __( 'Request did not pass validation.', 'fictioneer' )] );

    // Update user
    if ( update_user_meta( $user->ID, 'fictioneer_user_reminders', [] ) ) {
      wp_send_json_success( ['success' => __( 'Data has been cleared.', 'fictioneer' )] );
    } else {
      wp_send_json_error( ['error' => __( 'Database error. Reminders could not be cleared.', 'fictioneer' )] );
    }
  }
}

if ( get_option( 'fictioneer_enable_reminders' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_clear_my_reminders', 'fictioneer_ajax_clear_my_reminders' );
}

// =============================================================================
// GET FOLLOWS LIST - AJAX
// =============================================================================

if ( ! function_exists( 'fictioneer_ajax_get_reminders_list' ) ) {
  function fictioneer_ajax_get_reminders_list() {
    // Validations
    $user = wp_get_current_user();

    if ( ! $user ) wp_send_json_error( ['error' => __( 'Not logged in.', 'fictioneer' )] );

    if ( ! check_ajax_referer( 'fictioneer_nonce', 'nonce', false ) ) {
      wp_send_json_error( ['error' => __( 'Request did not pass validation.', 'fictioneer' )] );
    }

    // Setup
    $reminders = fictioneer_load_reminders( $user );
    $post_ids = array_keys( $reminders['data'] );
    $page = isset( $_GET['page'] ) ? absint( $_GET['page'] ) : 1;
    $order = isset( $_GET['order'] ) ? strtolower( $_GET['order'] ) : 'desc';

    // Sanitize
    $order = in_array( $order, ['desc', 'asc'] ) ? $order : 'desc';

    // Query
    $list_items = fictioneer_get_card_list(
      'story',
      array(
        'post__in' => $post_ids,
        'paged' => $page,
        'order' => $order
      ),
      __( 'You have not marked any stories to be read later.', 'fictioneer' ),
      ['show_latest' => true]
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
              ?><span class="page-numbers current"><?php echo $i; ?></span><?php
            } else {
              ?><button class="page-numbers" onclick="fcn_browseBookshelfPage(<?php echo $i; ?>)"><?php echo $i; ?></button><?php
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
        <button class="page-numbers" onclick="fcn_browseBookshelfPage(1)"><?php _e( 'First Page', 'fictioneer' ); ?></button>
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
}

if ( get_option( 'fictioneer_enable_reminders' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_get_reminders_list', 'fictioneer_ajax_get_reminders_list' );
}

?>
