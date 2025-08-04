<?php

// =============================================================================
// INITIALIZE ALERTS SYSTEM
// =============================================================================

/**
 * Initialize alerts database table.
 *
 * @since 5.31.0

 * @global wpdb $wpdb  WordPress database object.
 */

function fictioneer_initialize_alerts() {
  global $wpdb;

  $table_name = $wpdb->prefix . 'fcn_alerts';

  $table_exists = $wpdb->get_var(
    $wpdb->prepare(
      "SELECT 1 FROM information_schema.tables WHERE table_schema = %s AND table_name = %s LIMIT 1",
      DB_NAME,
      $table_name
    )
  );

  if ( $table_exists ) {
    return;
  }

  if ( ! function_exists( 'dbDelta' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  }

  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE IF NOT EXISTS $table_name (
    ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    type varchar(64) NOT NULL,
    post_id bigint(20) unsigned DEFAULT NULL,
    story_id bigint(20) unsigned DEFAULT NULL,
    author bigint(20) unsigned DEFAULT NULL,
    content text NOT NULL,
    url TEXT DEFAULT NULL,
    roles longtext DEFAULT NULL,
    users longtext DEFAULT NULL,
    tags longtext DEFAULT NULL,
    date datetime DEFAULT NULL,
    date_gmt datetime DEFAULT NULL,
    PRIMARY KEY  (ID),
    KEY post_id (post_id),
    KEY story_id (story_id),
    KEY author (author),
    KEY type (type),
    KEY date (date),
    KEY date_gmt (date_gmt)
  ) $charset_collate;";

  dbDelta( $sql );
}
add_action( 'after_switch_theme', 'fictioneer_initialize_alerts' );

// =============================================================================
// WP CRON
// =============================================================================

/**
 * Clean up expired alerts.
 *
 * @since 5.31.0
 *
 * @global wpdb $wpdb  WordPress database object.
 */

function fictioneer_alert_cleanup() {
  global $wpdb;

  $table = $wpdb->prefix . 'fcn_alerts';

  if ( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) === $table ) {
    $wpdb->query(
      $wpdb->prepare(
        "DELETE FROM $table WHERE date_gmt < %s",
        gmdate( 'Y-m-d H:i:s', time() - FICTIONEER_ALERT_EXPIRATION )
      )
    );
  }
}

if ( get_option( 'fictioneer_enable_alerts' ) ) {
  add_action( 'wp_scheduled_delete', 'fictioneer_alert_cleanup' );
}

// =============================================================================
// ADD ALERT
// =============================================================================

/**
 * Add alert to database.
 *
 * Note: Tags, users, and roles are used to filter alerts in queries
 * that specifically look for them. Otherwise, they are excluded.
 *
 * @since 5.31.0
 *
 * @global wpdb $wpdb  WordPress database object.
 *
 * @param string   $content           The content of the notice. Can be HTML.
 * @param string   $args              Optional arguments.
 * @param string   $args['type']      Type of the alert ('alert', 'info', 'warning',
 *                                    'new_chapter', 'new_story', 'new_*'). Default 'info'.
 * @param int      $args['post_id']   Post ID relating to the alert. Default null.
 * @param int      $args['story_id']  Story ID relating to the alert. Default null.
 * @param string   $args['date']      Local MYSQL datetime. Default `current_time()`.
 * @param int      $args['author']    Author ID required in the query. Default null.
 * @param string   $args['url']       Link URL for the alert. Default null.
 * @param string[] $args['tags']      Tags required in the query. Default null.
 * @param string[] $args['roles']     Roles required in the query. Default null.
 * @param int[]    $args['users']     User IDs required in the query. Default null.
 */

function fictioneer_add_alert( $content, $args = [] ) {
  global $wpdb;

  $table = $wpdb->prefix . 'fcn_alerts';

  $defaults = array(
    'type' => 'info',
    'post_id' => null,
    'story_id' => null,
    'author' => null,
    'url' => null,
    'roles' => null,
    'users' => null,
    'tags' => null,
    'date' => current_time( 'mysql' )
  );

  $args = wp_parse_args( $args, $defaults );

  $data = array(
    'type' => sanitize_key( $args['type'] ?? 'info' ),
    'post_id' => isset( $args['post_id'] ) ? (int) $args['post_id'] : null,
    'story_id' => isset( $args['story_id'] ) ? (int) $args['story_id'] : null,
    'author' => isset( $args['author'] ) ? (int) $args['author'] : null,
    'content' => wp_kses_post( $content ),
    'url' => isset( $args['url'] ) ? sanitize_url( $args['url'] ) : null,
    'roles' => isset( $args['roles'] ) ? maybe_serialize( array_map( 'sanitize_key', (array) $args['roles'] ) ) : null,
    'users' => isset( $args['users'] ) ? maybe_serialize( array_map( 'sanitize_key', (array) $args['users'] ) ) : null,
    'tags' => isset( $args['tags'] ) ? maybe_serialize( array_map( 'sanitize_key', (array) $args['tags'] ) ) : null,
    'date' => $args['date'],
    'date_gmt' => get_gmt_from_date( $args['date'] )
  );

  $format = array(
    '%s', // type
    '%d', // post_id
    '%d', // story_id
    '%d', // author
    '%s', // content
    '%s', // url
    '%s', // roles
    '%s', // users
    '%s', // tags
    '%s', // date
    '%s'  // date_gmt
  );

  $data_clean = [];
  $format_clean = [];

  foreach ( $data as $key => $value ) {
    if ( $value !== null ) {
      $data_clean[ $key ] = $value;
      $format_clean[] = array_shift( $format );
    } else {
      array_shift( $format );
    }
  }

  $result = $wpdb->insert( $table, $data_clean, $format_clean );
  $id = $result ? (int) $wpdb->insert_id : false;

  if ( $id ) {
    update_option( 'fictioneer_last_alert_timestamp', time() * 1000 );
  }

  return $id;
}

// =============================================================================
// ADD CHAPTER ALERT
// =============================================================================

/**
 * Add alert for new chapter.
 *
 * @since 5.31.0
 *
 * @global wpdb $wpdb  WordPress database object.
 *
 * @param int    $post_id  Post ID.
 * @param WP_Pos $post     Post object.
 */

function fictioneer_add_chapter_alert( $post_id, $post ) {
  if (
    $post->post_type !== 'fcn_chapter' ||
    $post->post_status !== 'publish' ||
    strtotime( $post->post_date_gmt ) > time() + 5 ||
    get_post_meta( $post_id, 'fictioneer_chapter_hidden', true )
  ) {
    return;
  }

  $story_id = get_post_meta( $post_id, 'fictioneer_chapter_story', true );

  if ( ! $story_id || ! get_post( $story_id ) ) {
    return;
  }

  global $wpdb;

  $table = $wpdb->prefix . 'fcn_alerts';

  $exists = $wpdb->get_var(
    $wpdb->prepare(
      "SELECT ID FROM $table WHERE type = %s AND post_id = %d LIMIT 1",
      'new_chapter',
      $post_id
    )
  );

  if ( $exists ) {
    return;
  }

  $content = sprintf(
    _x( 'New chapter published for %1$s (%2$s).', 'Alert message (1: Story, 2: Chapter, 3: Author).', 'fictioneer' ),
    fictioneer_get_safe_title( $story_id, 'alert' ),
    fictioneer_get_safe_title( $post_id, 'alert' ),
    get_the_author_meta( 'display_name', $post->post_author )
  );

  fictioneer_add_alert(
    $content,
    array(
      'type' => 'new_chapter',
      'post_id' => $post_id,
      'story_id' => $story_id,
      'author' => $post->post_author,
      'url' => get_permalink( $post )
    )
  );
}

/**
 * Add alert when chapter goes from future to publish.
 *
 * @since 5.31.0
 *
 * @param string  $new_status  New post status.
 * @param string  $old_status  Old post status.
 * @param WP_Post $post        Post object.
 */

 function fictioneer_add_chapter_alert_after_transition( $new_status, $old_status, $post ) {
  // Validate transition...
  if (
    $post->post_type !== 'fcn_chapter' ||
    $old_status !== 'future' ||
    $new_status !== 'publish' ||
    get_post_meta( $post->ID, 'fictioneer_chapter_hidden', true )
  ) {
    return;
  }

  // Add alert
  fictioneer_add_chapter_alert( $post->ID, $post );
}

if ( get_option( 'fictioneer_enable_alerts' ) ) {
  add_action( 'save_post', 'fictioneer_add_chapter_alert', 99, 2 );
  add_action( 'transition_post_status', 'fictioneer_add_chapter_alert_after_transition', 10, 3 );
}

// =============================================================================
// GET ALERTS
// =============================================================================

/**
 * Get alerts.
 *
 * Note: Copy of this is on _auth.php
 *
 * @since 5.31.0
 *
 * @global wpdb $wpdb  WordPress database object.
 *
 * @param array|null $args  Optional. Additional query arguments.
 *
 * @return array Queried alerts.
 */

function fictioneer_get_alerts( $args = [] ) {
  global $wpdb;

  $defaults = [
    'types' => [],
    'post_ids' => [],
    'story_ids' => [],
    'author' => null,
    'roles' => [],
    'user_ids' => [],
    'tags' => [],
    'only_ids' => false
  ];

  $args = wp_parse_args( $args, $defaults );

  $table = $wpdb->prefix . 'fcn_alerts';
  $fields = $args['only_ids'] ? 'ID' : 'ID, type, content, url'; // 'date, date_gmt' will be appended
  $global_types = ['info', 'alert', 'warning'];
  $has_filters = false;
  $params = [];
  $filtered_where = [];
  $filtered_params = [];

  if ( ! empty( $args['types'] ) ) {
    $types = array_filter( array_map( 'sanitize_key', $args['types'] ) );

    if ( $types ) {
      $has_filters = true;
      $placeholders = implode( ', ', array_fill( 0, count( $types ), '%s' ) );
      $filtered_where[] = "type IN ({$placeholders})";
      $filtered_params = array_merge( $filtered_params, $types );
    }
  }

  if ( ! empty( $args['post_ids'] ) ) {
    $post_ids = array_filter( array_map( 'intval', $args['post_ids'] ) );
    $post_ids = array_filter( $post_ids, function( $value ) { return $value > 0; } );

    if ( $post_ids ) {
      $has_filters = true;
      $placeholders = implode( ', ', array_fill( 0, count( $post_ids ), '%d' ) );
      $filtered_where[] = "post_id IN ({$placeholders})";
      $filtered_params = array_merge( $filtered_params, $post_ids );
    }
  }

  if ( ! empty( $args['story_ids'] ) ) {
    $story_ids = array_filter( array_map( 'intval', $args['story_ids'] ) );
    $story_ids = array_filter( $story_ids, function( $value ) { return $value > 0; } );

    if ( $story_ids ) {
      $has_filters = true;
      $placeholders = implode( ', ', array_fill( 0, count( $story_ids ), '%d' ) );
      $filtered_where[] = "story_id IN ({$placeholders})";
      $filtered_params = array_merge( $filtered_params, $story_ids );
    }
  }

  if ( isset( $args['author'] ) && is_numeric( $args['author'] ) && $args['author'] > 0 ) {
    $has_filters = true;
    $filtered_where[] = 'author = %d';
    $filtered_params[] = (int) $args['author'];
  }

  if ( ! empty( $args['roles'] ) ) {
    $role_where = ['roles IS NULL'];

    foreach ( $args['roles'] as $role ) {
      $role = sanitize_key( $role );

      if ( $role ) {
        $has_filters = true;
        $role_where[] = 'roles LIKE %s';
        $filtered_params[] = '%"' . $wpdb->esc_like( $role ) . '";%';
      }
    }

    $filtered_where[] = '( ' . implode( ' OR ', $role_where ) . ' )';
  } else {
    $filtered_where[] = 'roles IS NULL';
  }

  if ( ! empty( $args['user_ids'] ) ) {
    $user_where = ['users IS NULL'];

    foreach ( $args['user_ids'] as $user_id ) {
      $user_id = sanitize_key( $user_id );

      if ( intval( $user_id ) > 0 ) {
        $has_filters = true;
        $user_where[] = 'users LIKE %s';
        $filtered_params[] = '%"' . $wpdb->esc_like( $user_id ) . '";%';
      }
    }

    $filtered_where[] = '( ' . implode( ' OR ', $user_where ) . ' )';
  } else {
    $filtered_where[] = 'users IS NULL';
  }

  if ( ! empty( $args['tags'] ) ) {
    $tag_where = ['tags IS NULL'];

    foreach ( $args['tags'] as $tag ) {
      $tag = sanitize_key( $tag );

      if ( $tag ) {
        $has_filters = true;
        $tag_where[] = 'tags LIKE %s';
        $filtered_params[] = '%"' . $wpdb->esc_like( $tag ) . '";%';
      }
    }

    $filtered_where[] = '( ' . implode( ' OR ', $tag_where ) . ' )';
  } else {
    $filtered_where[] = 'tags IS NULL';
  }

  if ( ! empty( $args['since'] ) ) {
    $has_filters = true;

    $since = is_numeric( $args['since'] )
      ? gmdate( 'Y-m-d H:i:s', (int) $args['since'] )
      : sanitize_text_field( $args['since'] );

    $filtered_where[] = 'date_gmt >= %s';
    $filtered_params[] = $since;
  }

  $filtered_where[] = 'date_gmt <= %s';
  $filtered_params[] = gmdate( 'Y-m-d H:i:s' );

  if ( $has_filters ) {
    $sql_filtered = "SELECT {$fields}, date, date_gmt FROM $table";

    if ( $filtered_where ) {
      $sql_filtered .= ' WHERE ' . implode( ' AND ', $filtered_where );
    }

    $global_placeholders = implode( ', ', array_fill( 0, count( $global_types ), '%s' ) );
    $sql_global = "SELECT {$fields}, date, date_gmt FROM $table WHERE type IN ($global_placeholders) AND date_gmt <= %s";

    $sql = "($sql_filtered) UNION ALL ($sql_global) ORDER BY date_gmt DESC LIMIT 69";

    $params = array_merge( $filtered_params, $global_types, [ gmdate( 'Y-m-d H:i:s' ) ] );
  } else {
    $global_placeholders = implode( ', ', array_fill( 0, count( $global_types ), '%s' ) );

    $sql = "SELECT {$fields}, date, date_gmt FROM $table WHERE type IN ($global_placeholders) AND date_gmt <= %s ORDER BY date_gmt DESC LIMIT 99";

    $params = array_merge( $global_types, [ gmdate( 'Y-m-d H:i:s' ) ] );
  }

  $results = $wpdb->get_results( $wpdb->prepare( $sql, ...$params ), ARRAY_A );

  if ( empty( $results ) ) {
    return [];
  }

  $exclude_ids = array_map( 'intval', (array) ( $args['exclude_ids'] ?? [] ) );
  $date_format = get_option( 'fictioneer_alert_date_format', 'Y-m-d H:i' ) ?: 'Y-m-d H:i';
  $filtered_results = [];

  foreach ( $results as &$row ) {
    if ( in_array( (int) $row['ID'], $exclude_ids ) ) {
      continue;
    }

    if ( $args['only_ids'] ) {
      $filtered_results[] = (int) $row['ID'];
    } else {
      $filtered_results[] = array(
        'id' => (int) $row['ID'],
        'type' => $row['type'],
        'content'=> $row['content'],
        'url' => $row['url'],
        'date' => wp_date( $date_format, strtotime( $row['date_gmt'] ) ),
      );
    }
  }

  return $filtered_results;
}

// =============================================================================
// AJAX: MARK ALERT READ
// =============================================================================

/**
 * AJAX: Mark alerts read by ID.
 *
 * @since 5.31.0
 */

function fictioneer_ajax_mark_alert_read() {
  // Rate limit
  fictioneer_check_rate_limit( 'fictioneer_ajax_mark_alert_read' );

  // Setup and validations
  $user = fictioneer_get_validated_ajax_user();

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => 'Request did not pass validation.' ) );
  }

  // Sanitize
  $new_read_alerts = $_POST['ids'] ?? [];
  $new_read_alerts = is_array( $new_read_alerts ) ? $new_read_alerts : fictioneer_explode_list( $new_read_alerts );
  $new_read_alerts = array_map( 'intval', $new_read_alerts );
  $new_read_alerts = array_filter( $new_read_alerts, fn( $value ) => $value > 0 );
  $new_read_alerts = array_values( array_unique( $new_read_alerts ) );

  // Currently read alert IDs
  $read_alerts = get_user_meta( $user->ID, 'fictioneer_read_alerts', true ) ?: [];

  if ( ! is_array( $read_alerts ) ) {
    $read_alerts = [];
  }

  // Add newly read alert IDs
  $updated_read_alerts = array_merge( $read_alerts, $new_read_alerts );
  $updated_read_alerts = array_map( 'intval', $updated_read_alerts );
  $updated_read_alerts = array_filter( $updated_read_alerts, fn( $value ) => $value > 0 );
  $updated_read_alerts = array_values( array_unique( $updated_read_alerts ) );

  // Only remember last 500
  if ( count( $updated_read_alerts ) > FICTIONEER_MAX_READ_ALERTS ) {
    $updated_read_alerts = array_values( array_slice( $updated_read_alerts, -1 * FICTIONEER_MAX_READ_ALERTS ) );
  }

  // Update database
  $result = update_user_meta( $user->ID, 'fictioneer_read_alerts', $updated_read_alerts );

  // Response
  if ( $result ) {
    wp_send_json_success();
  } else {
    wp_send_json_error( array( 'error' => 'Read alerts could not be updated.' ) );
  }
}

if ( get_option( 'fictioneer_enable_alerts' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_mark_alert_read', 'fictioneer_ajax_mark_alert_read' );
}
