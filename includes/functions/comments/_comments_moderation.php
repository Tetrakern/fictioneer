<?php

// =============================================================================
// CHECK USER COMMENT MODERATION PERMISSION
// =============================================================================

/**
 * Checks whether a user can moderate a comment
 *
 * @since 5.7.3
 *
 * @param WP_Comment $comment  Comment object.
 * @param int|null   $user_id  The user ID to check permission for. Defaults to
 *                             the current user ID.
 *
 * @return boolean True if the user can moderate the comment, false otherwise.
 */

function fictioneer_user_can_moderate( $comment, $user_id = null ) {
  // Capability?
  $user_id = $user_id ? $user_id : get_current_user_id();

  if ( user_can( $user_id, 'moderate_comments' ) ) {
    return true;
  }

  // AJAX comments enabled?
  if ( ! get_option( 'fictioneer_enable_ajax_comment_moderation' ) ) {
    return false;
  }

  // Restricted?
  if ( get_the_author_meta( 'fictioneer_admin_disable_post_comment_moderation', get_current_user_id() ) ) {
    return false;
  }

  // Post author?
  $post = get_post( $comment->comment_post_ID );
  $post_author_id = absint( $post->post_author );

  if ( $post_author_id === get_current_user_id() && current_user_can( 'fcn_moderate_post_comments' ) ) {
    return true;
  }

  // Nope!
  return false;
}

// =============================================================================
// ADD CUSTOM COMMENT TYPES TO FILTER
// =============================================================================

/**
 * Add private comments to dropdown filter menu
 *
 * @since 5.0.0
 */

function fictioneer_add_private_to_comment_filter( $comment_types ) {
  $comment_types['private'] = _x( 'Private', 'Private comments filter in comment screen dropdown.', 'fictioneer' );

  return $comment_types;
}
add_filter( 'admin_comment_types_dropdown', 'fictioneer_add_private_to_comment_filter' );

/**
 * Add user_deleted comments to dropdown filter menu
 *
 * @since 5.24.1
 */

function fictioneer_add_user_deleted_to_comment_filter( $comment_types ) {
  $comment_types['user_deleted'] = _x( 'Deleted', 'Deleted comments filter in comment screen dropdown.', 'fictioneer' );

  return $comment_types;
}
add_filter( 'admin_comment_types_dropdown', 'fictioneer_add_user_deleted_to_comment_filter' );

// =============================================================================
// ADD METABOX TO COMMENT EDIT SCREEN
// =============================================================================

/**
 * Adds Fictioneer comment meta box to the wp-admin comment edit screen
 *
 * @since 4.7.0
 */

function fictioneer_add_comment_meta_box() {
  add_meta_box(
    'title',
    __( 'Comment Moderation', 'fictioneer' ),
    'fictioneer_comment_meta_box',
    'comment',
    'normal',
    'high'
  );
}

/**
 * HTML for the Fictioneer comment meta box
 *
 * @since 4.7.0
 *
 * @param object $comment  The comment object.
 */

function fictioneer_comment_meta_box( $comment ) {
  // Setup
  $type = get_comment_type( $comment->comment_ID );
  $user_reports = get_comment_meta( $comment->comment_ID, 'fictioneer_user_reports', true );
  $is_offensive = get_comment_meta( $comment->comment_ID, 'fictioneer_marked_offensive', true );
  $is_sticky = get_comment_meta( $comment->comment_ID, 'fictioneer_sticky', true );
  $is_closed = get_comment_meta( $comment->comment_ID, 'fictioneer_thread_closed', true );
  $ignores_reports = get_comment_meta( $comment->comment_ID, 'fictioneer_ignore_reports', true );
  $last_auto_moderation = get_comment_meta( $comment->comment_ID, 'fictioneer_auto_moderation', true );
  $edit_stack = get_comment_meta( $comment->comment_ID, 'fictioneer_user_edit_stack', true );
  $notification_checksum = get_comment_meta( $comment->comment_ID, 'fictioneer_send_notifications', true );
  $notification_validator = get_user_meta( $comment->user_id, 'fictioneer_comment_reply_validator', true );
  $email = $comment->comment_author_email;

  wp_nonce_field( 'fictioneer_comment_update', 'fictioneer_comment_update', false );
?>
  <div style="margin-top: 12px;">
    <?php if ( $type == 'private' ) : ?>
      <div style="margin-bottom: 12px;">
        <input type="checkbox" disabled checked>
        <?php _e( 'Private comment', 'fictioneer' ); ?>
      </div>
    <?php endif; ?>
    <?php if ( ! empty( $notification_checksum ) && ! empty( $email ) && $notification_checksum == $notification_validator ) : ?>
      <div style="margin-bottom: 12px;">
        <input type="checkbox" disabled checked>
        <?php _ex( 'Notification emails', 'Comment moderation reply notification check.', 'fictioneer' ); ?>
      </div>
    <?php endif; ?>
    <?php if ( $last_auto_moderation ) : ?>
      <div style="margin-bottom: 12px;">
        <?php
          printf(
            __( 'Last auto-moderated on <strong>%1$s</strong>. The comment will remain hidden even if re-approved as long as the <strong>report threshold of %2$s or more</strong> is met unless the comment is set to ignore reports.', 'fictioneer' ),
            wp_date( get_option( 'date_format' ) . ', ' . get_option( 'time_format' ), $last_auto_moderation ),
            get_option( 'fictioneer_comment_report_threshold', 10 )
          );
        ?>
      </div>
    <?php endif; ?>
    <?php if ( is_array( $user_reports ) && count( $user_reports ) > 0 ) : ?>
      <div style="margin-bottom: 12px;">
        <details>
          <summary><strong><?php _e( 'User Reports:', 'fictioneer' ); ?></strong> <?php echo count( $user_reports ); ?></summary>
          <div style="margin-top: 6px;"><?php
            if ( $user_reports ) {
              echo implode( ', ', $user_reports );
            }
          ?></div>
        </details>
      </div>
    <?php endif; ?>
    <?php if ( ! empty( $edit_stack ) ) : ?>
      <div style="margin-top: 12px;">
        <div><strong><?php _ex( 'Edit History (Previous Versions):', 'Comment edit history.', 'fictioneer' ); ?></strong></div>
        <?php foreach ( $edit_stack as $edit ) : ?>
          <details style="margin-top: 6px;">
            <summary><?php
              $edit_user = get_user_by( 'ID', $edit['user_id'] ?? 0 );

              printf(
                _x( '%1$s (ID: %2$s): %3$s', 'Comment moderation edit stack item.', 'fictioneer' ),
                empty( $edit_user ) ? _x( 'Unknown', 'Unknown comment editor.', 'fictioneer' ) : $edit_user->user_login,
                $edit['user_id'] ?? 0,
                wp_date(
                  sprintf(
                    _x( '%1$s \a\t %2$s', 'Comment moderation edit stack item [date], [time].', 'fictioneer' ),
                    get_option( 'date_format' ),
                    get_option( 'time_format' )
                  ),
                  $edit['timestamp']
                )
              );
            ?></summary>
            <div style="margin: 6px 0 0 12px;">
              <textarea style="resize: vertical; width: 100%; min-height: 104px;" disabled><?php echo $edit['previous_content']; ?></textarea>
            </div>
          </details>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <div style="margin-top: 12px;">
      <div><strong><?php _e( 'Comment Flags:', 'fictioneer' ); ?></strong></div>
      <?php if ( get_option( 'fictioneer_enable_sticky_comments' ) && empty( $comment->comment_parent ) ) : ?>
        <div style="margin-top: 6px;">
          <label for="fictioneer_sticky">
            <input name="fictioneer_sticky" type="checkbox" id="fictioneer_sticky" <?php echo checked( 1, $is_sticky, false ); ?> value="1">
            <span><?php _e( 'Sticky', 'fictioneer' ); ?></span>
          </label>
        </div>
      <?php endif; ?>
      <div style="margin-top: 6px;">
        <label for="fictioneer_thread_closed">
          <input name="fictioneer_thread_closed" type="checkbox" id="fictioneer_thread_closed" <?php echo checked( 1, $is_closed, false ); ?> value="1">
          <span><?php _e( 'Closed', 'fictioneer' ); ?></span>
        </label>
      </div>
      <div style="margin-top: 6px;">
        <label for="fictioneer_marked_offensive">
          <input name="fictioneer_marked_offensive" type="checkbox" id="fictioneer_marked_offensive" <?php echo checked( 1, $is_offensive, false ); ?> value="1">
          <span><?php _e( 'Offensive', 'fictioneer' ); ?></span>
        </label>
      </div>
      <div style="margin-top: 6px;">
        <label for="fictioneer_ignore_reports">
          <input name="fictioneer_ignore_reports" type="checkbox" id="fictioneer_ignore_reports" <?php echo checked( 1, $ignores_reports, false ); ?> value="1">
          <span><?php _e( 'Ignores reports', 'fictioneer' ); ?></span>
        </label>
      </div>
      <?php if ( user_can( $comment->user_id, 'moderate_comments' ) ) : ?>
        <div style="margin-top: 12px;"><strong><?php _e( 'User Flags:', 'fictioneer' ); ?></strong></div>
        <div style="margin-top: 6px;">
          <label for="fictioneer_admin_disable_avatar" class="checkbox-group">
            <input name="fictioneer_admin_disable_avatar" type="checkbox" id="fictioneer_admin_disable_avatar" <?php echo checked( 1, get_the_author_meta( 'fictioneer_admin_disable_avatar', $comment->user_id ), false ); ?> value="1">
            <span><?php _e( 'Disable user avatar', 'fictioneer' ); ?></span>
          </label>
        </div>
        <div style="margin-top: 6px;">
          <label for="fictioneer_admin_disable_reporting" class="checkbox-group">
            <input name="fictioneer_admin_disable_reporting" type="checkbox" id="fictioneer_admin_disable_reporting" <?php echo checked( 1, get_the_author_meta( 'fictioneer_admin_disable_reporting', $comment->user_id ), false ); ?> value="1">
            <span><?php _e( 'Disable reporting capability', 'fictioneer' ); ?></span>
          </label>
        </div>
        <div style="margin-top: 6px;">
          <label for="fictioneer_admin_disable_renaming" class="checkbox-group">
            <input name="fictioneer_admin_disable_renaming" type="checkbox" id="fictioneer_admin_disable_renaming" <?php echo checked( 1, get_the_author_meta( 'fictioneer_admin_disable_renaming', $comment->user_id ), false ); ?> value="1">
            <span><?php _e( 'Disable renaming capability', 'fictioneer' ); ?></span>
          </label>
        </div>
        <div style="margin-top: 6px;">
          <label for="fictioneer_admin_disable_commenting" class="checkbox-group">
            <input name="fictioneer_admin_disable_commenting" type="checkbox" id="fictioneer_admin_disable_commenting" <?php echo checked( 1, get_the_author_meta( 'fictioneer_admin_disable_commenting', $comment->user_id ), false ); ?> value="1">
            <span><?php _e( 'Disable commenting capability', 'fictioneer' ); ?></span>
          </label>
        </div>
        <div style="margin-top: 6px;">
          <label for="fictioneer_admin_disable_comment_editing" class="checkbox-group">
            <input name="fictioneer_admin_disable_comment_editing" type="checkbox" id="fictioneer_admin_disable_comment_editing" <?php echo checked( 1, get_the_author_meta( 'fictioneer_admin_disable_comment_editing', $comment->user_id ), false ); ?> value="1">
            <span><?php _e( 'Disable comment editing capability', 'fictioneer' ); ?></span>
          </label>
        </div>
        <div style="margin-top: 6px;">
          <label for="fictioneer_admin_disable_comment_notifications" class="checkbox-group">
            <input name="fictioneer_admin_disable_comment_notifications" type="checkbox" id="fictioneer_admin_disable_comment_notifications" <?php echo checked( 1, get_the_author_meta( 'fictioneer_admin_disable_comment_notifications', $comment->user_id ), false ); ?> value="1">
            <span><?php _e( 'Disable comment reply notifications', 'fictioneer' ); ?></span>
          </label>
        </div>
        <div style="margin-top: 6px;">
          <label for="fictioneer_admin_always_moderate_comments" class="checkbox-group">
            <input name="fictioneer_admin_always_moderate_comments" type="checkbox" id="fictioneer_admin_always_moderate_comments" <?php echo checked( 1, get_the_author_meta( 'fictioneer_admin_always_moderate_comments', $comment->user_id ), false ); ?> value="1">
            <span><?php _e( 'Always hold comments for moderation', 'fictioneer' ); ?></span>
          </label>
        </div>
      <?php endif; ?>
    </div>
  </div>
<?php
}

// =============================================================================
// SAVE CUSTOM DATA IN THE COMMENT EDIT SCREEN
// =============================================================================

/**
 * Save data in the wp-admin comment edit screen
 *
 * @since 4.7.0
 *
 * @param int $comment_id  Comment ID.
 */

function fictioneer_edit_comment( $comment_id ) {
  // Validations
  if (
    ! is_admin() ||
    ! current_user_can( 'moderate_comments' ) ||
    ! isset( $_POST['fictioneer_comment_update'] ) ||
    ! wp_verify_nonce( $_POST['fictioneer_comment_update'], 'fictioneer_comment_update' )
  ) {
    return;
  }

  // Setup
  $comment = get_comment( $comment_id );

  // Always evaluate because checkboxes can be deselected (and thus be empty)
  $is_sticky = fictioneer_sanitize_checkbox( $_POST['fictioneer_sticky'] ?? 0 );
  $is_closed = fictioneer_sanitize_checkbox( $_POST['fictioneer_thread_closed'] ?? 0 );
  $is_offensive = fictioneer_sanitize_checkbox( $_POST['fictioneer_marked_offensive'] ?? 0 );
  $ignores_reports = fictioneer_sanitize_checkbox( $_POST['fictioneer_ignore_reports'] ?? 0 );
  $disable_avatar = fictioneer_sanitize_checkbox( $_POST['fictioneer_admin_disable_avatar'] ?? 0 );
  $disable_reports = fictioneer_sanitize_checkbox( $_POST['fictioneer_admin_disable_reporting'] ?? 0 );
  $disable_renaming = fictioneer_sanitize_checkbox( $_POST['fictioneer_admin_disable_renaming'] ?? 0 );
  $disable_commenting = fictioneer_sanitize_checkbox( $_POST['fictioneer_admin_disable_commenting'] ?? 0 );
  $disable_editing = fictioneer_sanitize_checkbox( $_POST['fictioneer_admin_disable_comment_editing'] ?? 0 );
  $disable_notifications = fictioneer_sanitize_checkbox( $_POST['fictioneer_admin_disable_comment_notifications'] ?? 0 );
  $hold_comments = fictioneer_sanitize_checkbox( $_POST['fictioneer_admin_always_moderate_comments'] ?? 0 );

  // Save to database
  fictioneer_update_comment_meta( $comment_id, 'fictioneer_sticky', $is_sticky );
  fictioneer_update_comment_meta( $comment_id, 'fictioneer_thread_closed', $is_closed );
  fictioneer_update_comment_meta( $comment_id, 'fictioneer_marked_offensive', $is_offensive );
  fictioneer_update_comment_meta( $comment_id, 'fictioneer_ignore_reports', $ignores_reports );
  fictioneer_update_user_meta( $comment->user_id, 'fictioneer_admin_disable_avatar', $disable_avatar );
  fictioneer_update_user_meta( $comment->user_id, 'fictioneer_admin_disable_reporting', $disable_reports );
  fictioneer_update_user_meta( $comment->user_id, 'fictioneer_admin_disable_renaming', $disable_renaming );
  fictioneer_update_user_meta( $comment->user_id, 'fictioneer_admin_disable_commenting', $disable_commenting );
  fictioneer_update_user_meta( $comment->user_id, 'fictioneer_admin_disable_comment_editing', $disable_editing );
  fictioneer_update_user_meta( $comment->user_id, 'fictioneer_admin_disable_comment_notifications', $disable_notifications );
  fictioneer_update_user_meta( $comment->user_id, 'fictioneer_admin_always_moderate_comments', $hold_comments );
}

// Add filters and actions if not disabled
if ( ! get_option( 'fictioneer_disable_comment_form' ) ) {
  add_action( 'add_meta_boxes_comment', 'fictioneer_add_comment_meta_box' );
  add_action( 'edit_comment', 'fictioneer_edit_comment' );
}

/**
 * Tracks comment edits and stores a history of changes
 *
 * @since 5.5.3
 *
 * @param array|WP_Error $data     The comment data to be saved.
 * @param array          $comment  The old comment data.
 *
 * @return array The unchanged comment data.
 */

function fictioneer_track_comment_edit( $data, $comment ) {
  // Abort if...
  if ( is_wp_error( $data ) ) {
    return $data;
  }

  // Setup
  $previous = get_comment( $comment['comment_ID'] );
  $edit_stack = get_comment_meta( $comment['comment_ID'], 'fictioneer_user_edit_stack', true );
  $edit_stack = is_array( $edit_stack ) ? $edit_stack : [];

  // Not edited?
  if ( $previous->comment_content === $data['comment_content'] ) {
    return $data;
  }

  // Add new edit
  $edit_stack[] = array(
    'timestamp' => time(),
    'previous_content' => $previous->comment_content,
    'user_id' => get_current_user_id()
  );

  // Prevent loop
  remove_filter( 'wp_update_comment_data', 'fictioneer_track_comment_edit', 10 );

  // Update stack
  fictioneer_update_comment_meta( $comment['comment_ID'], 'fictioneer_user_edit_stack', $edit_stack );

  // Restore filter
  add_filter( 'wp_update_comment_data', 'fictioneer_track_comment_edit', 10, 2 );

  // Continue comment update
  return $data;
}
add_filter( 'wp_update_comment_data', 'fictioneer_track_comment_edit', 10, 2 );

// =============================================================================
// RENDER COMMENT MODERATION MENU
// =============================================================================

/**
 * Renders the comment moderation menu template
 *
 * @since 4.27.0
 */

function fictioneer_comment_moderation_template() {
  // Start HTML ---> ?>
  <template id="template-comment-frontend-moderation-menu">
    <?php if ( get_option( 'fictioneer_enable_ajax_comment_moderation' ) ) : ?>
      <button data-action="click->fictioneer-comment#trash"><?php _e( 'Trash', 'fictioneer' ); ?></button>
      <button data-action="click->fictioneer-comment#spam"><?php _e( 'Spam', 'fictioneer' ); ?></button>
      <button data-action="click->fictioneer-comment#offensive"><?php _e( 'Offensive', 'fictioneer' ); ?></button>
      <button data-action="click->fictioneer-comment#unoffensive"><?php _e( 'Unoffensive', 'fictioneer' ); ?></button>
      <button data-action="click->fictioneer-comment#unapprove"><?php _e( 'Unapprove', 'fictioneer' ); ?></button>
      <button data-action="click->fictioneer-comment#approve"><?php _e( 'Approve', 'fictioneer' ); ?></button>
      <button data-action="click->fictioneer-comment#close"><?php _e( 'Close', 'fictioneer' ); ?></button>
      <button data-action="click->fictioneer-comment#open"><?php _e( 'Open', 'fictioneer' ); ?></button>

      <?php if ( get_option( 'fictioneer_enable_sticky_comments' ) ) : ?>
        <button data-action="click->fictioneer-comment#sticky"><?php _e( 'Sticky', 'fictioneer' ); ?></button>
        <button data-action="click->fictioneer-comment#unsticky"><?php _e( 'Unsticky', 'fictioneer' ); ?></button>
      <?php endif; ?>
    <?php endif; ?>

    <a class="comment-edit-link" data-fictioneer-comment-target="editLink"><?php _e( 'Edit' ); ?></a>

  </template>
  <?php // <--- End HTML
}

if ( ! function_exists( 'fictioneer_comment_moderation' ) ) {
  /**
   * Renders icon and frame for the frontend comment moderation
   *
   * @since 4.7.0
   *
   * @param WP_Comment $comment  Comment object.
   */

  function fictioneer_comment_moderation( $comment ) {
    // Abort conditions...
    if (
      ! current_user_can( 'moderate_comments' ) &&
      ! fictioneer_user_can_moderate( $comment ) &&
      ! get_option( 'fictioneer_enable_public_cache_compatibility' )
    ) {
      return;
    }

    // Setup
    $edit_link = admin_url( 'comment.php?c=' . $comment->comment_ID . '&action=editcomment' );

    // Start HTML ---> ?>
    <div class="popup-menu-toggle comment-quick-button hide-if-logged-out only-moderators only-comment-moderators hide-on-ajax" tabindex="0" data-fictioneer-last-click-target="toggle" data-fictioneer-comment-target="modMenuToggle" data-action="click->fictioneer-comment#toggleMenu click->fictioneer-last-click#toggle">
      <i class="fa-solid fa-gear mod-menu-toggle-icon" data-fictioneer-comment-target="modIcon"></i>
      <div data-fictioneer-comment-target="modMenu" data-edit-link="<?php echo esc_attr( $edit_link ); ?>" class="popup-menu _top _justify-right _fixed-position"></div>
    </div>
    <?php // <--- End HTML
  }
}

// =============================================================================
// PERFORM COMMENT MODERATION ACTION - AJAX
// =============================================================================

/**
 * Performs comment moderation action via AJAX
 *
 * @since 4.7.0
 */

function fictioneer_ajax_moderate_comment() {
  // Enabled?
  if ( ! get_option( 'fictioneer_enable_ajax_comment_moderation' ) ) {
    wp_send_json_error( null, 403 );
  }

  // Setup and validations
  $user = fictioneer_get_validated_ajax_user();

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => 'Request did not pass validation.' ) );
  }

  $comment_id = isset( $_POST['id'] ) ? fictioneer_validate_id( $_POST['id'] ) : false;

  if ( empty( $_POST['operation'] ) || ! $comment_id ) {
    wp_send_json_error( array( 'error' => 'Missing arguments.' ) );
  }

  $operation = sanitize_text_field( $_POST['operation'] );

  if (
    ! in_array(
      $operation,
      ['spam', 'trash', 'offensive', 'unoffensive', 'approve', 'unapprove', 'close', 'open', 'sticky', 'unsticky']
    )
  ) {
    wp_send_json_error( array( 'error' => 'Invalid operation.' ) );
  }

  $comment = get_comment( $comment_id );

  if ( ! $comment ) {
    wp_send_json_error( array( 'error' => 'Comment not found in database.' ) );
  }

  // Capabilities?
  if ( ! fictioneer_user_can_moderate( $comment ) ) {
    wp_send_json_error( array( 'failure' => __( 'Permission denied!', 'fictioneer' ) ) );
  }

  if ( $operation === 'trash' &&  ! current_user_can( 'moderate_comments' ) ) {
    wp_send_json_error( array( 'failure' => __( 'Permission denied!', 'fictioneer' ) ) );
  }

  // Process and update
  $result = false;

  switch ( $operation ) {
    case 'spam':
      $result = wp_set_comment_status( $comment_id, 'spam' );
      break;
    case 'trash':
      $result = wp_set_comment_status( $comment_id, 'trash' );
      break;
    case 'offensive':
      $result = fictioneer_update_comment_meta( $comment_id, 'fictioneer_marked_offensive', true );
      break;
    case 'unoffensive':
      $result = fictioneer_update_comment_meta( $comment_id, 'fictioneer_marked_offensive', false );
      break;
    case 'approve':
      $result = wp_set_comment_status( $comment_id, 'approve' );
      break;
    case 'unapprove':
      $result = wp_set_comment_status( $comment_id, 'hold' );
      break;
    case 'close':
      $result = fictioneer_update_comment_meta( $comment_id, 'fictioneer_thread_closed', true );
      break;
    case 'open':
      $result = fictioneer_update_comment_meta( $comment_id, 'fictioneer_thread_closed', false );
      break;
    case 'sticky':
      if ( $comment->comment_parent ) {
        wp_send_json_error( array( 'error' => __( 'Child comments cannot be sticky.', 'fictioneer' ) ) );
        break;
      }
      if ( $comment->comment_type === 'user_deleted' ) {
        wp_send_json_error( array( 'error' => __( 'Deleted comments cannot be sticky.', 'fictioneer' ) ) );
        break;
      }
      $result = fictioneer_update_comment_meta( $comment_id, 'fictioneer_sticky', true );
      break;
    case 'unsticky':
      $result = fictioneer_update_comment_meta( $comment_id, 'fictioneer_sticky', false );
      break;
  }

  // Send result
  if ( $result ) {
    // Clear cache if necessary
    if ( fictioneer_caching_active( 'ajax_comment_moderation' ) ) {
      fictioneer_purge_post_cache( $comment->comment_post_ID );
    }

    wp_send_json_success( array( 'id' => $comment_id, 'operation' => $operation ) );
  } else {
    wp_send_json_error( array( 'error' => 'Comment could not be updated.' ) );
  }
}

if ( get_option( 'fictioneer_enable_ajax_comment_moderation' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_moderate_comment', 'fictioneer_ajax_moderate_comment' );
}

// =============================================================================
// REPORT COMMENTS
// =============================================================================

/**
 * Adds user to a comment's reports list via AJAX
 *
 * @since 4.7.0
 * @link  https://developer.wordpress.org/reference/functions/wp_send_json_success/
 * @link  https://developer.wordpress.org/reference/functions/wp_send_json_error/
 */

function fictioneer_ajax_report_comment() {
  // Enabled?
  if (
    get_option( 'fictioneer_disable_comment_callback' ) ||
    ! get_option( 'fictioneer_enable_comment_reporting' )
  ) {
    wp_send_json_error( null, 403 );
  }

  // Setup and validations
  $user = fictioneer_get_validated_ajax_user();

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => 'Request did not pass validation.' ) );
  }

  if ( empty( $_POST['id'] ) ) {
    wp_send_json_error( array( 'error' => 'Missing arguments.' ) );
  }

  $comment_id = fictioneer_validate_id( $_POST['id'] );
  $dubious = empty( $_POST['dubious'] ) ? false : $_POST['dubious'] == 'true';

  // Get comment
  $comment = get_comment( $comment_id );

  if ( ! $comment ) {
    wp_send_json_error( array( 'error' => 'Comment not found.' ) );
  }

  // Check if user is allowed to flag comments
  if ( get_the_author_meta( 'fictioneer_admin_disable_reporting', $user->ID ) ) {
    wp_send_json_error( array( 'failure' => __( 'Reporting capability disabled.', 'fictioneer' ) ) );
  }

  // Get current reports (array of user IDs)
  $reports = get_comment_meta( $comment_id, 'fictioneer_user_reports', true );
  $reports = $reports ? $reports : [];

  // Dubious if state not clear due to caching
  if ( $dubious && array_key_exists( $user->ID, $reports ) ) {
    wp_send_json_success(
      array(
        'id' => $comment_id,
        'flagged' => true,
        'resync' => __( 'You have already reported this comment. Click the flag again to retract the report.', 'fictioneer' )
      )
    );
  }

  // Check whether to add or remove the user from the array
  if ( array_key_exists( $user->ID, $reports ) ) {
    unset( $reports[ $user->ID ] );
  } else {
    $user_data = get_userdata( $user->ID );
    $reports[ $user->ID ] = '<a href="' . get_edit_profile_url( $user->ID ) . '">' . $user_data->display_name . '</a>';
  }

  // Update comment meta
  $result = fictioneer_update_comment_meta( $comment_id, 'fictioneer_user_reports', $reports );

  if ( ! $result ) {
    wp_send_json_error( array( 'error' => 'Report could not be saved.' ) );
  }

  // Send back to moderation?
  $auto_moderation = get_comment_meta( $comment_id, 'fictioneer_auto_moderation', true );

  if ( empty( $auto_moderation ) && count( $reports ) >= get_option( 'fictioneer_comment_report_threshold', 10 ) ) {
    // Only ever auto-moderate once!
    fictioneer_update_comment_meta( $comment_id, 'fictioneer_auto_moderation', time() );

    // Set back to hold
    wp_set_comment_status( $comment_id, 'hold' );
  }

  // Purge cache
  if ( fictioneer_caching_active( 'ajax_report_comment' ) ) {
    fictioneer_purge_post_cache( $comment->comment_post_ID );
  }

  // Return result
  wp_send_json_success(
    array(
      'id' => $comment_id,
      'flagged' => array_key_exists( $user->ID, $reports )
    )
  );
}

/**
 * Add comments reports column
 *
 * @since 4.7.0
 * @link  https://rudrastyh.com/wordpress/comments-table-columns.html
 *
 * @param array $cols  Default columns.
 *
 * @return array Updated columns.
 */

function fictioneer_add_comments_report_column( $cols ) {
  $new_cols = ['fictioneer_reports' => __( 'Reports', 'fictioneer' )];
  $new_cols = array_slice( $cols, 0, 3, true ) + $new_cols + array_slice( $cols, 3, NULL, true );
  return $new_cols;
}

/**
 * Echo content for comments reports column
 *
 * @since 4.7.0
 * @link  https://rudrastyh.com/wordpress/comments-table-columns.html
 * @link  https://developer.wordpress.org/reference/hooks/manage_comments_custom_column/
 *
 * @param string $column      The custom column's name.
 * @param string $comment_id  The comment ID as a numeric string.
 */

function fictioneer_add_comments_report_column_content( $column, $comment_id ) {
  $reports = get_comment_meta( $comment_id, 'fictioneer_user_reports', true );

  if ( $reports && is_array( $reports ) ) {
    echo count( $reports );
  } else {
    echo 0;
  }
}

if ( ! get_option( 'fictioneer_disable_comment_callback' ) && get_option( 'fictioneer_enable_comment_reporting' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_report_comment', 'fictioneer_ajax_report_comment' );
  add_filter( 'manage_edit-comments_columns', 'fictioneer_add_comments_report_column' );
  add_action( 'manage_comments_custom_column', 'fictioneer_add_comments_report_column_content', 10, 2 );
}
