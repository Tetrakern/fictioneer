<?php

// =============================================================================
// LOAD FOLLOWS
// =============================================================================

if ( ! function_exists( 'fictioneer_load_follows' ) ) {
  /**
   * Returns an user's Follows
   *
   * Get an user's Follows array from the database or creates a new one if it
   * does not yet exist.
   *
   * @since Fictioneer 5.0
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
// GET FOLLOWS - AJAX
// =============================================================================

/**
 * Sends the user's Follows as JSON via Ajax
 *
 * @since Fictioneer 4.3
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
 * @see fictioneer_get_validated_ajax_user()
 */

function fictioneer_ajax_get_follows() {
  // Setup and validations
  $user = fictioneer_get_validated_ajax_user();

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => __( 'Request did not pass validation.', 'fictioneer' ) ) );
  }

  // Prepare Follows
  $user_follows = fictioneer_load_follows( $user );
  $user_follows['timestamp'] = time() * 1000; // Compatible with Date.now() in JavaScript
  $latest = 0;
  $new = false;

  // New notifications?
  if ( count( $user_follows['data'] ) > 0 ) {
    $latest = fictioneer_query_followed_chapters(
      array_keys( $user_follows['data'] ),
      wp_date( 'c', $user_follows['seen'] / 1000 )
    );

    if ( $latest ) $new = count( $latest );
  }

  $user_follows['new'] = $new;

  // Response
  wp_send_json_success( array( 'follows' => json_encode( $user_follows ) ) );
}

if ( get_option( 'fictioneer_enable_follows' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_get_follows', 'fictioneer_ajax_get_follows' );
}

// =============================================================================
// TOGGLE FOLLOW - AJAX
// =============================================================================

/**
 * Toggle Follow for a story via AJAX
 *
 * @since Fictioneer 4.3
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
 * @see fictioneer_get_validated_ajax_user()
 * @see fictioneer_load_follows()
 */

function fictioneer_ajax_toggle_follow() {
  // Setup and validations
  $user = fictioneer_get_validated_ajax_user();

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => __( 'Request did not pass validation.', 'fictioneer' ) ) );
  }

  if ( empty( $_POST['story_id'] ) || empty( $_POST['set'] ) ) {
    wp_send_json_error( array( 'error' => __( 'Missing arguments.', 'fictioneer' ) ) );
  }

  // Valid story ID?
  $story_id = fictioneer_validate_id( $_POST['story_id'], 'fcn_story' );
  if ( ! $story_id ) {
    wp_send_json_error( array( 'error' => __( 'Invalid story ID.', 'fictioneer' ) ) );
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
      'user_id' => $user->ID,
      'story_id' => $story_id,
      'timestamp' => $timestamp
    );

    $user_follows['data'][ $story_id ] = $item;
  } else {
    unset( $user_follows['data'][ $story_id ] );
  }

  // Update database & response
  delete_user_meta( $user->ID, 'fictioneer_user_follows_cache' );

  if ( update_user_meta( $user->ID, 'fictioneer_user_follows', $user_follows ) ) {
    wp_send_json_success();
  } else {
    wp_send_json_error( array( 'error' => __( 'Database error. Follows could not be updated.', 'fictioneer' ) ) );
  }
}

if ( get_option( 'fictioneer_enable_follows' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_toggle_follow', 'fictioneer_ajax_toggle_follow' );
}

// =============================================================================
// CLEAR MY FOLLOWS - AJAX
// =============================================================================

/**
 * Clears an user's Follows via AJAX
 *
 * @since Fictioneer 5.0
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
 * @see fictioneer_get_validated_ajax_user()
 */

function fictioneer_ajax_clear_my_follows() {
  // Setup and validations
  $user = fictioneer_get_validated_ajax_user( 'nonce', 'fictioneer_clear_follows' );

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => __( 'Request did not pass validation.', 'fictioneer' ) ) );
  }

  // Update user
  if ( update_user_meta( $user->ID, 'fictioneer_user_follows', [] ) ) {
    update_user_meta( $user->ID, 'fictioneer_user_follows_cache', false );
    wp_send_json_success( array( 'success' => __( 'Data has been cleared.', 'fictioneer' ) ) );
  } else {
    wp_send_json_error( array( 'error' => __( 'Database error. Follows could not be cleared.', 'fictioneer' ) ) );
  }
}

if ( get_option( 'fictioneer_enable_follows' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_clear_my_follows', 'fictioneer_ajax_clear_my_follows' );
}

// =============================================================================
// MARK FOLLOWS AS READ - AJAX
// =============================================================================

/**
 * Mark all Follows as read via AJAX
 *
 * Updates the 'seen' timestamp in the 'fictioneer_user_follows' meta data,
 * which is used to determine whether Follows are new. This will mark all
 * of them as read, you cannot mark single items in the list as read.
 *
 * @since Fictioneer 4.3
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
 * @see fictioneer_get_validated_ajax_user()
 */

function fictioneer_ajax_mark_follows_read() {
  // Setup and validations
  $user = fictioneer_get_validated_ajax_user();

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => __( 'Request did not pass validation.', 'fictioneer' ) ) );
  }

  $user_follows = fictioneer_load_follows( $user );

  if ( empty( $user_follows ) ) {
    wp_send_json_error( array( 'error' => __( 'Follows are empty.', 'fictioneer' ) ) );
  }

  // Update 'seen' timestamp to now; compatible with Date.now() in JavaScript
  $user_follows['seen'] = time() * 1000;

  // Update database
  update_user_meta( $user->ID, 'fictioneer_user_follows_cache', false );
  $result = update_user_meta( $user->ID, 'fictioneer_user_follows', $user_follows );

  // Response
  if ( $result ) {
    wp_send_json_success();
  } else {
    wp_send_json_error( array( 'error' => __( 'Database error. Follows could not be updated.', 'fictioneer' ) ) );
  }
}

if ( get_option( 'fictioneer_enable_follows' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_mark_follows_read', 'fictioneer_ajax_mark_follows_read' );
}

// =============================================================================
// QUERY CHAPTERS OF FOLLOWED STORIES
// =============================================================================

if ( ! function_exists( 'fictioneer_query_followed_chapters' ) ) {
  /**
   * Query chapters of followed stories
   *
   * @since Fictioneer 4.3
   *
   * @param array        $story_ids   IDs of the followed stories.
   * @param string|false $after_date  Optional. Only return chapters after this date, e.g. wp_date( 'c', $timestamp ).
   * @param int          $count       Optional. Maximum number of chapters to be returned. Default 16.
   *
   * @return array Collection of chapters.
   */

  function fictioneer_query_followed_chapters( $story_ids, $after_date = false, $count = 16 ) {
    $query_args = array (
      'post_type' => 'fcn_chapter',
      'post_status' => 'publish',
      'meta_query' => array(
        array(
          'key' => 'fictioneer_chapter_story',
          'value' => $story_ids,
          'compare' => 'IN',
        ),
        array(
          'relation' => 'OR',
          array(
            'key' => 'fictioneer_chapter_hidden',
            'value' => '0'
          ),
          array(
            'key' => 'fictioneer_chapter_hidden',
            'compare' => 'NOT EXISTS'
          )
        )
      ),
      'numberposts' => $count,
      'orderby' => 'date',
      'order' => 'DESC',
      'no_found_rows' => true,
      'update_post_meta_cache' => false,
      'update_post_term_cache' => false
    );

    if ( $after_date ) {
      $query_args['date_query'] = array( 'after' => $after_date );
    }

    return get_posts( $query_args );
  }
}

// =============================================================================
// GET FOLLOWS NOTIFICATIONS - AJAX
// =============================================================================

/**
 * Sends the HTML for Follows notifications via AJAX
 *
 * @since Fictioneer 4.3
 * @see fictioneer_get_validated_ajax_user()
 */

function fictioneer_ajax_get_follows_notifications() {
  // Setup and validations
  $user = fictioneer_get_validated_ajax_user();
  if ( ! $user ) wp_send_json_error(
    array(
      'error' => __( 'Not logged in.', 'fictioneer' ),
      'html' => '<div class="follow-item"><div class="follow-wrapper"><div class="follow-placeholder truncate _1-1">' . __( 'Not logged in.', 'fictioneer' ) . '</div></div></div>'
    )
  );

  // Follows
  $user_follows = fictioneer_load_follows( $user );

  // Last story/chapter update on site
  $last_update = fictioneer_get_last_fiction_update();

  // Check for cached HTML
  if ( ! empty( $last_update ) ) {
    $cache = get_user_meta( $user->ID, 'fictioneer_user_follows_cache', true );
    if ( ! empty( $cache ) && array_key_exists( $last_update, $cache ) ) {
      $html = $cache[ $last_update ] . '<!-- Cached on ' . $cache['timestamp'] . ' -->';
      wp_send_json_success( array( 'html' => $html ) );
    }
  }

  // Chapters for notifications
  $chapters = count( $user_follows['data'] ) > 0 ?
    fictioneer_query_followed_chapters( array_keys( $user_follows['data'] ) ) : false;

  // Build notifications
  ob_start();

  if ( $chapters ) {
    foreach ( $chapters as $chapter ) {
      $date = get_the_date(
        sprintf(
          _x( '%1$s \a\t %2$s', 'Date in Follows update list.', 'fictioneer' ),
          get_option('date_format'),
          get_option('time_format')
        ), $chapter->ID
      );
      $chapter_timestamp = get_post_timestamp( $chapter->ID ) * 1000; // Compatible with Date.now() in JavaScript
      $story_id = fictioneer_get_field( 'fictioneer_chapter_story', $chapter->ID );
      $new = $user_follows['seen'] < $chapter_timestamp ? '_new' : '';

      // Start HTML ---> ?>
      <div class="follow-item <?php echo $new; ?>" data-chapter-id="<?php echo $chapter->ID; ?>" data-story-id="<?php echo $story_id; ?>" data-timestamp="<?php echo $chapter_timestamp; ?>">
        <div class="follow-wrapper">
          <div class="follow-title truncate _1-1">
            <a href="<?php echo get_the_permalink( $chapter->ID ); ?>"><?php echo fictioneer_get_safe_title( $chapter->ID ); ?></a>
          </div>
          <div class="follow-meta truncate _1-1"><?php echo $date ; ?> in <?php echo fictioneer_get_safe_title( $story_id ); ?></div>
          <div class="follow-marker">&bull;</div>
        </div>
      </div>
      <?php // <--- End HTML
    }
  } else {
    // Start HTML ---> ?>
    <div class="follow-item">
      <div class="follow-wrapper">
        <div class="follow-placeholder truncate _1-1"><?php _e( 'You are not following any stories.', 'fictioneer' ); ?></div>
      </div>
    </div>
    <?php // <--- End HTML
  }

  $html = fictioneer_minify_html( ob_get_clean() );

  // Cache for next time
  if ( ! empty( $last_update ) ) {
    update_user_meta(
      $user->ID,
      'fictioneer_user_follows_cache',
      array(
        $last_update => $html,
        'timestamp' => time() * 1000
      )
    );
  }

  // Return HTML
  wp_send_json_success( array( 'html' => $html ) );
}

if ( get_option( 'fictioneer_enable_follows' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_get_follows_notifications', 'fictioneer_ajax_get_follows_notifications' );
}

// =============================================================================
// GET FOLLOWS LIST - AJAX
// =============================================================================

/**
 * Sends the HTML for list of followed stories via AJAX
 *
 * @since Fictioneer 4.3
 */

function fictioneer_ajax_get_follows_list() {
  // Validations
  $user = wp_get_current_user();

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => __( 'Not logged in.', 'fictioneer' ) ) );
  }

  if ( ! check_ajax_referer( 'fictioneer_nonce', 'nonce', false ) ) {
    wp_send_json_error( array( 'error' => __( 'Request did not pass validation.', 'fictioneer' ) ) );
  }

  // Setup
  $follows = fictioneer_load_follows( $user );
  $post_ids = array_keys( $follows['data'] );
  $page = absint( $_GET['page'] ?? 1 );
  $order = strtolower( $_GET['order'] ?? 'desc' );

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
    __( 'You are not following any stories.', 'fictioneer' ),
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
      'html' => fictioneer_minify_html( $list_items['html'] ) . $navigation,
      'count' => count( $post_ids ),
      'maxPages' => $max_pages
    )
  );
}

if ( get_option( 'fictioneer_enable_follows' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_get_follows_list', 'fictioneer_ajax_get_follows_list' );
}

?>
