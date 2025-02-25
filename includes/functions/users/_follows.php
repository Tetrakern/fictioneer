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

if ( ! function_exists( 'fictioneer_query_new_followed_chapters_count' ) ) {
  /**
   * Query count of new chapters for followed stories
   *
   * @since 5.27.0
   *
   * @param array       $story_ids   IDs of the followed stories.
   * @param string|null $after_date  Optional. Only return chapters after this date,
   *                                 e.g. wp_date( 'Y-m-d H:i:s', $timestamp ).
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
  delete_user_meta( $user->ID, 'fictioneer_user_follows_cache' );

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
    update_user_meta( $user->ID, 'fictioneer_user_follows_cache', false );
    wp_send_json_success( array( 'success' => __( 'Data has been cleared.', 'fictioneer' ) ) );
  } else {
    wp_send_json_error( array( 'failure' => __( 'Database error. Follows could not be cleared.', 'fictioneer' ) ) );
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
 * @since 4.3.0
 */

function fictioneer_ajax_mark_follows_read() {
  // Rate limit
  fictioneer_check_rate_limit( 'fictioneer_ajax_mark_follows_read' );

  // Setup and validations
  $user = fictioneer_get_validated_ajax_user();

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => 'Request did not pass validation.' ) );
  }

  $user_follows = fictioneer_load_follows( $user );

  if ( empty( $user_follows ) ) {
    wp_send_json_error( array( 'failure' => __( 'Follows are empty.', 'fictioneer' ) ) );
  }

  // Update 'seen' timestamp to now; compatible with Date.now() in JavaScript
  $user_follows['seen'] = time() * 1000;

  // Update database
  delete_user_meta( $user->ID, 'fictioneer_user_follows_cache' );
  $result = update_user_meta( $user->ID, 'fictioneer_user_follows', $user_follows );

  // Response
  if ( $result ) {
    wp_send_json_success();
  } else {
    wp_send_json_error( array( 'error' => 'Follows could not be updated.' ) );
  }
}

if ( get_option( 'fictioneer_enable_follows' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_mark_follows_read', 'fictioneer_ajax_mark_follows_read' );
}

// =============================================================================
// GET FOLLOWS NOTIFICATIONS - AJAX
// =============================================================================

/**
 * Sends the HTML for Follows notifications via AJAX
 *
 * @since 4.3.0
 */

function fictioneer_ajax_get_follows_notifications() {
  // Rate limit
  fictioneer_check_rate_limit( 'fictioneer_ajax_get_follows_notifications' );

  // Setup and validations
  $user = fictioneer_get_validated_ajax_user();

  if ( ! $user ) {
    wp_send_json_error(
      array(
        'error' => 'You must be logged in.',
        'html' => '<div class="follow-item"><div class="follow-wrapper"><div class="follow-placeholder truncate _1-1">' . __( 'Not logged in.', 'fictioneer' ) . '</div></div></div>'
      )
    );
  }

  // Follows
  $user_follows = fictioneer_load_follows( $user );

  // Last story/chapter update on site
  $last_update = fictioneer_get_last_fiction_update();

  // Meta cache for HTML?
  if ( ! empty( $last_update ) ) {
    $meta_cache = get_user_meta( $user->ID, 'fictioneer_user_follows_cache', true );

    if ( ! empty( $meta_cache ) && array_key_exists( $last_update, $meta_cache ) ) {
      $html = $meta_cache[ $last_update ] . '<!-- Cached on ' . $meta_cache['timestamp'] . ' -->';

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
          get_option( 'date_format' ),
          get_option( 'time_format' )
        ), $chapter->ID
      );
      $chapter_timestamp = get_post_timestamp( $chapter->ID ) * 1000; // Compatible with Date.now() in JavaScript
      $story_id = fictioneer_get_chapter_story_id( $chapter->ID );
      $new = $user_follows['seen'] < $chapter_timestamp ? '_new' : '';

      // Start HTML ---> ?>
      <div class="follow-item <?php echo $new; ?>" data-chapter-id="<?php echo $chapter->ID; ?>" data-story-id="<?php echo $story_id; ?>" data-timestamp="<?php echo $chapter_timestamp; ?>">
        <div class="follow-wrapper">
          <div class="follow-title truncate _1-1">
            <a class="follow-title-link _no-menu-item-style" href="<?php echo get_the_permalink( $chapter->ID ); ?>"><?php echo fictioneer_get_safe_title( $chapter->ID, 'ajax-get-follows-notifications' ); ?></a>
          </div>
          <div class="follow-meta truncate _1-1"><?php echo $date ; ?> in <?php echo fictioneer_get_safe_title( $story_id, 'ajax-get-follows-notifications' ); ?></div>
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

  // Update meta cache
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
