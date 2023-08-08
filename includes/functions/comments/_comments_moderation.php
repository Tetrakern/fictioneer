<?php

// =============================================================================
// ADD PRIVATE TYPE TO FILTER
// =============================================================================

/**
 * Add private comments to dropdown filter menu
 *
 * @since Fictioneer 5.0
 */

function fictioneer_add_private_to_comment_filter( $comment_types ) {
  $comment_types['private'] = _x( 'Private', 'Private comments filter in comment screen dropdown.', 'fictioneer' );

  return $comment_types;
}

add_filter( 'admin_comment_types_dropdown', 'fictioneer_add_private_to_comment_filter' );

// =============================================================================
// ADD METABOX TO COMMENT EDIT SCREEN
// =============================================================================

/**
 * Adds Fictioneer comment meta box to the wp-admin comment edit screen
 *
 * @since Fictioneer 4.7
 */

function fictioneer_add_comment_meta_box() {
  add_meta_box(
    'title',
    __( 'Fictioneer Comment Moderation', 'fictioneer' ),
    'fictioneer_comment_meta_box',
    'comment',
    'normal',
    'high'
  );
}

/**
 * HTML for the Fictioneer comment meta box
 *
 * @since Fictioneer 4.7
 *
 * @param object $comment The comment object.
 */

function fictioneer_comment_meta_box ( $comment ) {
  $type = get_comment_type( $comment->comment_ID );
  $comment_author = get_user_by( 'ID', $comment->user_id );
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
        <div><strong><?php _ex( 'User Edit History:', 'Comment user edit history.', 'fictioneer' ); ?></strong></div>
        <?php foreach( $edit_stack as $edit ) : ?>
          <details style="margin-top: 6px;">
            <summary><?php
              printf(
                _x( '%s (show previous version)', 'Comment moderation edit stack item with [datetime].', 'fictioneer' ),
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
          <span><?php _e( 'Closed', 'fictioneer' ) ?></span>
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
          <span><?php _e( 'Ignores reports', 'fictioneer' ) ?></span>
        </label>
      </div>
      <?php if ( user_can( $comment->user_id, 'moderate_comments' ) ) : ?>
        <div style="margin-top: 12px;"><strong><?php _e( 'User Flags:', 'fictioneer' ) ?></strong></div>
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
          <label for="fictioneer_admin_disable_editing" class="checkbox-group">
            <input name="fictioneer_admin_disable_editing" type="checkbox" id="fictioneer_admin_disable_editing" <?php echo checked( 1, get_the_author_meta( 'fictioneer_admin_disable_editing', $comment->user_id ), false ); ?> value="1">
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
 * @since Fictioneer 4.7
 *
 * @param int $comment_id Comment ID.
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
  $is_sticky = fictioneer_sanitize_checkbox_by_key( 'fictioneer_sticky' );
  $is_closed = fictioneer_sanitize_checkbox_by_key( 'fictioneer_thread_closed' );
  $is_offensive = fictioneer_sanitize_checkbox_by_key( 'fictioneer_marked_offensive' );
  $ignores_reports = fictioneer_sanitize_checkbox_by_key( 'fictioneer_ignore_reports' );
  $disable_avatar = fictioneer_sanitize_checkbox_by_key( 'fictioneer_admin_disable_avatar' );
  $disable_reports = fictioneer_sanitize_checkbox_by_key( 'fictioneer_admin_disable_reporting' );
  $disable_renaming = fictioneer_sanitize_checkbox_by_key( 'fictioneer_admin_disable_renaming' );
  $disable_commenting = fictioneer_sanitize_checkbox_by_key( 'fictioneer_admin_disable_commenting' );
  $disable_editing = fictioneer_sanitize_checkbox_by_key( 'fictioneer_admin_disable_editing' );
  $disable_notifications = fictioneer_sanitize_checkbox_by_key( 'fictioneer_admin_disable_comment_notifications' );
  $hold_comments = fictioneer_sanitize_checkbox_by_key( 'fictioneer_admin_always_moderate_comments' );

  // Save to database
  update_comment_meta( $comment_id, 'fictioneer_sticky', $is_sticky );
  update_comment_meta( $comment_id, 'fictioneer_thread_closed', $is_closed );
  update_comment_meta( $comment_id, 'fictioneer_marked_offensive', $is_offensive );
  update_comment_meta( $comment_id, 'fictioneer_ignore_reports', $ignores_reports );
  update_user_meta( $comment->user_id, 'fictioneer_admin_disable_avatar', $disable_avatar );
  update_user_meta( $comment->user_id, 'fictioneer_admin_disable_reporting', $disable_reports );
  update_user_meta( $comment->user_id, 'fictioneer_admin_disable_renaming', $disable_renaming );
  update_user_meta( $comment->user_id, 'fictioneer_admin_disable_commenting', $disable_commenting );
  update_user_meta( $comment->user_id, 'fictioneer_admin_disable_editing', $disable_editing );
  update_user_meta( $comment->user_id, 'fictioneer_admin_disable_comment_notifications', $disable_notifications );
  update_user_meta( $comment->user_id, 'fictioneer_admin_always_moderate_comments', $hold_comments );
}

// Add filters and actions if not disabled
if ( ! get_option( 'fictioneer_disable_comment_form' ) ) {
  add_action( 'add_meta_boxes_comment', 'fictioneer_add_comment_meta_box' );
  add_action( 'edit_comment', 'fictioneer_edit_comment', 10 );
}

// =============================================================================
// GET COMMENT ACTION LINK
// =============================================================================

if ( ! function_exists( 'fictioneer_get_comment_action_link' ) ) {
  /**
   * Returns a link to an admin comment moderation action or false
   *
   * @since Fictioneer 4.7
   * @link https://github.com/WordPress/WordPress/blob/master/wp-admin/comment.php
   *
   * @param int            $comment_id  The ID of the comment.
   * @param string         $action      Optional. The action to perform. Default 'edit'.
   * @param string|boolean $redirect_to Optional. Return URL after action. Default false.
   *
   * @return string|boolean $output The link or false if an invalid call.
   */

  function fictioneer_get_comment_action_link( $comment_id, $action = 'edit', $redirect_to = false ) {
    // Setup
    $comment_id = fictioneer_validate_id( $comment_id );

    // Validation
    if ( ! $comment_id ) return false;

    // Data
    $template = '<a class="comment-' . $action . '-link" href="%1$s">%2$s</a>';
    $admin_url = admin_url( 'comment.php?c=' . $comment_id . '&action=' );
    $redirect_to = $redirect_to ? '&redirect_to=' . $redirect_to : '';
    $output = false;

    switch ( $action ) {
      case 'edit':
        $output = sprintf( $template, $admin_url . 'editcomment', __( 'Edit' ) );
        break;
      case 'spam':
        $nonce_url = esc_url( wp_nonce_url( $admin_url . 'spamcomment', 'delete-comment_' . $comment_id ) );
        $output = sprintf( $template, $nonce_url . $redirect_to, __( 'Spam' ) );
        break;
      case 'trash':
        $nonce_url = esc_url( wp_nonce_url( $admin_url . 'trashcomment', 'delete-comment_' . $comment_id ) );
        $output = sprintf( $template, $nonce_url . $redirect_to, __( 'Trash' ) );
        break;
      case 'approve':
        $nonce_url = esc_url( wp_nonce_url( $admin_url . 'approvecomment', 'approve-comment_' . $comment_id ) );
        $output = sprintf( $template, $nonce_url . $redirect_to, __( 'Approve' ) );
        break;
      case 'unapprove':
        $nonce_url = esc_url( wp_nonce_url( $admin_url . 'unapprovecomment', 'approve-comment_' . $comment_id ) );
        $output = sprintf( $template, $nonce_url . $redirect_to, __( 'Unapprove' ) );
        break;
      case 'delete':
        $nonce_url = esc_url( wp_nonce_url( $admin_url . 'deletecomment', 'delete-comment_' . $comment_id ) );
        $output = sprintf( $template, $nonce_url . $redirect_to, __( 'Delete' ) );
        break;
    }

    return $output;
  }
}

// =============================================================================
// RENDER COMMENT MODERATION MENU
// =============================================================================

if ( ! function_exists( 'fictioneer_comment_mod_menu' ) ) {
  /**
   * Renders HTML for the moderation menu
   *
   * @since Fictioneer 4.7
   *
   * @param object $comment Comment object.
   */

  function fictioneer_comment_mod_menu( $comment ) {
    // Setup
    $comment_id = $comment->comment_ID;

    // Abort conditions...
    if (
      ! current_user_can( 'moderate_comments' ) &&
      ! get_option( 'fictioneer_enable_public_cache_compatibility' )
    ) return;

    // Buffer and return
    ob_start();
    // Start HTML ---> ?>
    <div class="popup-menu-toggle toggle-last-clicked hide-if-logged-out only-moderators" tabindex="0">
      <i class="fa-solid fa-gear mod-menu-toggle-icon"></i>
      <div class="popup-menu hide-if-logged-out only-moderators _top _justify-right _fixed-position">
        <?php if ( get_option( 'fictioneer_enable_ajax_comment_moderation' ) ) : ?>
          <button class="button-ajax-moderate-comment" data-id="<?php echo $comment_id; ?>" data-action="trash"><?php _e( 'Trash', 'fictioneer' ); ?></button>
          <button class="button-ajax-moderate-comment" data-id="<?php echo $comment_id; ?>" data-action="spam"><?php _e( 'Spam', 'fictioneer' ); ?></button>
          <button class="button-ajax-moderate-comment" data-id="<?php echo $comment_id; ?>" data-action="unapprove"><?php _e( 'Unapprove', 'fictioneer' ); ?></button>
          <button class="button-ajax-moderate-comment" data-id="<?php echo $comment_id; ?>" data-action="approve"><?php _e( 'Approve', 'fictioneer' ); ?></button>
          <button class="button-ajax-moderate-comment" data-id="<?php echo $comment_id; ?>" data-action="close"><?php _e( 'Close', 'fictioneer' ); ?></button>
          <button class="button-ajax-moderate-comment" data-id="<?php echo $comment_id; ?>" data-action="open"><?php _e( 'Open', 'fictioneer' ); ?></button>
        <?php else : ?>
          <?php
            echo fictioneer_get_comment_action_link( $comment_id, 'trash', '#comments' );
            echo fictioneer_get_comment_action_link( $comment_id, 'spam', '#comments' );

            if ( $comment->comment_approved == '0' ) {
              echo fictioneer_get_comment_action_link( $comment_id, 'approve', get_comment_link( $comment_id ) );
            } else {
              echo fictioneer_get_comment_action_link( $comment_id, 'unapprove', get_comment_link( $comment_id ) );
            }
          ?>
        <?php endif; ?>
        <?php if ( get_option( 'fictioneer_enable_sticky_comments' ) ) : ?>
          <button class="button-ajax-moderate-comment" data-id="<?php echo $comment_id; ?>" data-action="sticky"><?php _e( 'Sticky', 'fictioneer' ); ?></button>
          <button class="button-ajax-moderate-comment" data-id="<?php echo $comment_id; ?>" data-action="unsticky"><?php _e( 'Unsticky', 'fictioneer' ); ?></button>
        <?php endif; ?>
        <?php echo fictioneer_get_comment_action_link( $comment_id, 'edit' ); ?>
      </div>
    </div>
    <button type="button" class="hidden fictioneer-mod-menu-toggle toggle-last-clicked hide-if-logged-out only-moderators"><i class="fa-solid fa-gear"></i></button>
    <div class="popup-menu hide-if-logged-out only-moderators hidden">
      <?php if ( get_option( 'fictioneer_enable_ajax_comment_moderation' ) ) : ?>
        <button class="button-ajax-moderate-comment" data-id="<?php echo $comment_id; ?>" data-action="trash"><?php _e( 'Trash', 'fictioneer' ); ?></button>
        <button class="button-ajax-moderate-comment" data-id="<?php echo $comment_id; ?>" data-action="spam"><?php _e( 'Spam', 'fictioneer' ); ?></button>
        <button class="button-ajax-moderate-comment" data-id="<?php echo $comment_id; ?>" data-action="unapprove"><?php _e( 'Unapprove', 'fictioneer' ); ?></button>
        <button class="button-ajax-moderate-comment" data-id="<?php echo $comment_id; ?>" data-action="approve"><?php _e( 'Approve', 'fictioneer' ); ?></button>
        <button class="button-ajax-moderate-comment" data-id="<?php echo $comment_id; ?>" data-action="close"><?php _e( 'Close', 'fictioneer' ); ?></button>
        <button class="button-ajax-moderate-comment" data-id="<?php echo $comment_id; ?>" data-action="open"><?php _e( 'Open', 'fictioneer' ); ?></button>
      <?php else : ?>
        <?php
          echo fictioneer_get_comment_action_link( $comment_id, 'trash', '#comments' );
          echo fictioneer_get_comment_action_link( $comment_id, 'spam', '#comments' );

          if ( $comment->comment_approved == '0' ) {
            echo fictioneer_get_comment_action_link( $comment_id, 'approve', get_comment_link( $comment_id ) );
          } else {
            echo fictioneer_get_comment_action_link( $comment_id, 'unapprove', get_comment_link( $comment_id ) );
          }
        ?>
      <?php endif; ?>
      <?php if ( get_option( 'fictioneer_enable_sticky_comments' ) ) : ?>
        <button class="button-ajax-moderate-comment" data-id="<?php echo $comment_id; ?>" data-action="sticky"><?php _e( 'Sticky', 'fictioneer' ); ?></button>
        <button class="button-ajax-moderate-comment" data-id="<?php echo $comment_id; ?>" data-action="unsticky"><?php _e( 'Unsticky', 'fictioneer' ); ?></button>
      <?php endif; ?>
      <?php echo fictioneer_get_comment_action_link( $comment_id, 'edit' ); ?>
    </div>
    <?php // <--- End HTML
    echo ob_get_clean();
  }
}

// =============================================================================
// PERFORM COMMENT MODERATION ACTION - AJAX
// =============================================================================

/**
 * Performs comment moderation action via AJAX
 *
 * @since Fictioneer 4.7
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
 */

function fictioneer_ajax_moderate_comment() {
  // Setup and validations
  $user = fictioneer_get_validated_ajax_user();
  if ( ! $user ) wp_send_json_error( ['error' => __( 'Request did not pass validation.', 'fictioneer' )] );

  if ( ! current_user_can( 'moderate_comments' ) ) {
    wp_send_json_error( ['error' => __( 'Permission denied!', 'fictioneer' )] );
  }

  $comment_id = isset( $_POST['id'] ) ? fictioneer_validate_id( $_POST['id'] ) : false;

  if ( empty( $_POST['operation'] ) || ! $comment_id ) {
    wp_send_json_error( ['error' => __( 'Missing arguments.', 'fictioneer' )] );
  }

  $operation = sanitize_text_field( $_POST['operation'] );

  if ( ! in_array( $operation, ['spam', 'trash', 'approve', 'unapprove', 'close', 'open', 'sticky', 'unsticky'] ) ) {
    wp_send_json_error( ['error' => __( 'Invalid operation.', 'fictioneer' )] );
  }

  $comment = get_comment( $comment_id );

  if ( ! $comment ) {
    wp_send_json_error( ['error' => __( 'Comment not found in database.', 'fictioneer' )] );
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
    case 'approve':
      $result = wp_set_comment_status( $comment_id, 'approve' );
      break;
    case 'unapprove':
      $result = wp_set_comment_status( $comment_id, 'hold' );
      break;
    case 'close':
      $result = update_comment_meta( $comment_id, 'fictioneer_thread_closed', true );
      break;
    case 'open':
      $result = update_comment_meta( $comment_id, 'fictioneer_thread_closed', false );
      break;
    case 'sticky':
      if ( $comment->comment_parent ) {
        wp_send_json_error( ['error' => __( 'Child comments cannot be sticky.', 'fictioneer' )] );
        break;
      }
      if ( get_comment_meta( $comment->comment_ID, 'fictioneer_deleted_by_user', true ) ) {
        wp_send_json_error( ['error' => __( 'Deleted comments cannot be sticky.', 'fictioneer' )] );
        break;
      }
      $result = update_comment_meta( $comment_id, 'fictioneer_sticky', true );
      break;
    case 'unsticky':
      $result = update_comment_meta( $comment_id, 'fictioneer_sticky', false );
      break;
  }

  // Send result
  if ( $result ) {
    // Clear cache if necessary
    if ( fictioneer_caching_active() ) fictioneer_purge_post_cache( $comment->comment_post_ID );

    wp_send_json_success( ['id' => $comment_id, 'operation' => $operation] );
  } else {
    wp_send_json_error( ['error' => __( 'Database error. Comment could not be updated.', 'fictioneer' )] );
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
 * @since Fictioneer 4.7
 * @link  https://developer.wordpress.org/reference/functions/wp_send_json_success/
 * @link  https://developer.wordpress.org/reference/functions/wp_send_json_error/
 */

function fictioneer_ajax_report_comment() {
  // Setup and validations
  $user = fictioneer_get_validated_ajax_user();
  if ( ! $user ) wp_send_json_error( ['error' => __( 'Request did not pass validation.', 'fictioneer' )] );

  if ( empty( $_POST['id'] ) ) {
    wp_send_json_error( ['error' => __( 'Missing arguments.', 'fictioneer' )] );
  }

  $comment_id = fictioneer_validate_id( $_POST['id'] );
  $dubious = empty( $_POST['dubious'] ) ? false : $_POST['dubious'] == 'true';

  // Get comment
  $comment = get_comment( $comment_id );

  if ( ! $comment ) {
    wp_send_json_error( ['error' => __( 'Comment not found.', 'fictioneer' )] );
  }

  // Check if user is allowed to flag comments
  if ( get_the_author_meta( 'fictioneer_admin_disable_reporting', $user->ID ) ) {
    wp_send_json_error( ['error' => __( 'Reporting capability disabled.', 'fictioneer' )] );
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
  $result = update_comment_meta( $comment_id, 'fictioneer_user_reports', $reports );

  if ( ! $result ) {
    wp_send_json_error( ['error' => __( 'Database error. Report could not be saved.', 'fictioneer' )] );
  }

  // Send back to moderation?
  $auto_moderation = get_comment_meta( $comment_id, 'fictioneer_auto_moderation', true );

  if ( empty( $auto_moderation ) && count( $reports ) >= get_option( 'fictioneer_comment_report_threshold', 10 ) ) {
    // Only ever auto-moderate once!
    update_comment_meta( $comment_id, 'fictioneer_auto_moderation', time() );

    // Set back to hold
    wp_set_comment_status( $comment_id, 'hold' );
  }

  // Purge cache
  if ( fictioneer_caching_active() ) fictioneer_purge_post_cache( $comment->comment_post_ID );

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
 * @since Fictioneer 4.7
 * @link  https://rudrastyh.com/wordpress/comments-table-columns.html
 *
 * @param array $cols Default columns.
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
 * @since Fictioneer 4.7
 * @link  https://rudrastyh.com/wordpress/comments-table-columns.html
 * @link  https://developer.wordpress.org/reference/hooks/manage_comments_custom_column/
 *
 * @param string $column     The custom column's name.
 * @param string $comment_id The comment ID as a numeric string.
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

?>
