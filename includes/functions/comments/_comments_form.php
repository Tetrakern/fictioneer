<?php

// =============================================================================
// COMMENT TOOLBAR
// =============================================================================

if ( ! function_exists( 'fictioneer_get_comment_toolbar' ) ) {
  /**
   * Returns the HTML for the comment toolbar
   *
   * @since 4.7.0
   *
   * @return string Comment toolbar or empty string if it is disabled.
   */

  function fictioneer_get_comment_toolbar() {
    // Abort conditions
    if (
      ! get_option( 'fictioneer_enable_comment_toolbar' ) ||
      get_option( 'fictioneer_disable_comment_bbcodes' )
    ) {
      return '';
    }

    return '<div class="fictioneer-comment-toolbar"><span class="fictioneer-comment-toolbar-bold" data-bbcode="b"><i class="fa-solid fa-bold"></i></span><span class="fictioneer-comment-toolbar-italic" data-bbcode="i"><i class="fa-solid fa-italic"></i></span><span class="fictioneer-comment-toolbar-strike" data-bbcode="s"><i class="fa-solid fa-strikethrough"></i></span><span class="fictioneer-comment-toolbar-image" data-bbcode="img"><i class="fa-solid fa-image"></i></span><span class="fictioneer-comment-toolbar-link" data-bbcode="link"><i class="fa-solid fa-link"></i></span><span class="fictioneer-comment-toolbar-quote" data-bbcode="quote"><i class="fa-solid fa-quote-left"></i></span><span class="fictioneer-comment-toolbar-spoiler" data-bbcode="spoiler"><i class="fa-solid fa-eye-low-vision"></i></span><label class="fictioneer-comment-toolbar-help" for="modal-bbcodes-toggle"><i class="fa-solid fa-circle-question"></i></label></div>';
  }
}

// =============================================================================
// COMMENT FORM FIELDS
// =============================================================================

/**
 * Change default fields of comment form
 *
 * @since 4.7.0
 *
 * @param array $fields  Array of the default comment fields.
 *
 * @return array Modified array of the default comment fields.
 */

function fictioneer_change_comment_fields( $fields ) {
  // Remove URL (website) field
  unset( $fields['url'] );

  // Setup
  $commenter = wp_get_current_commenter();
  $cookie_checked_attribute = empty( $commenter['comment_author_email'] ) ? '' : ' checked';
  $required = get_option( 'require_name_email' );
  $required_attribute = $required ? ' required' : '';
  $name_placeholder = $required ? __( 'Name *', 'fictioneer' ) : __( 'Name', 'fictioneer' );
  $email_placeholder = $required ? __( 'Email (Gravatar) *', 'fictioneer' ) : __( 'Email (optional, Gravatar)', 'fictioneer' );
  $privacy_policy_link = get_option( 'wp_page_for_privacy_policy' ) ? esc_url( get_privacy_policy_url() ) : false;
  $hidden = FICTIONEER_COLLAPSE_COMMENT_FORM ? 'hidden' : '';

  // Rebuild author field
  $fields['author'] = '<div class="comment-form-author"><input type="text" name="author" data-lpignore="true" value="' . esc_attr( $commenter['comment_author'] ) . '"' . $required_attribute . ' maxlength="245" placeholder="' . $name_placeholder . '"></div>';

  // Rebuild email field
  $fields['email'] = '<div class="comment-form-email"><input type="text" name="email" data-lpignore="true" value="' . esc_attr( $commenter['comment_author_email'] ) . '" maxlength="100" aria-describedby="email-notes" placeholder="' . $email_placeholder . '"' . $required_attribute . '></div>';

  // Open .fictioneer-respond__checkboxes wrapper
  $fields['checkboxes'] = '<div class="fictioneer-respond__form-checkboxes fictioneer-respond__form-bottom-block">';

  // Rebuild cookies field (ignores 'show_comments_cookies_opt_in' and is always enabled)
  $fields['cookies'] = '<label class="comment-form-cookies-consent fictioneer-respond__checkbox-label-pair"><input name="wp-comment-cookies-consent" type="checkbox" value="yes"' . $cookie_checked_attribute . '><span>' . fcntr( 'save_in_cookie' ) . '</span></label>';

  // Build new privacy policy field
  $privacy_policy = '';

  if ( $privacy_policy_link ) {
    $privacy_policy = '<label class="comment-form-privacy-policy-consent fictioneer-respond__checkbox-label-pair"><input name="fictioneer-privacy-policy-consent" type="checkbox" value="1" required' . $cookie_checked_attribute . '><span>' . sprintf( fcntr( 'accept_privacy_policy' ), $privacy_policy_link ) . '</span></label>';
  }

  // Append checkboxes and close .fictioneer-respond__checkboxes wrapper
  if ( get_option( 'show_comments_cookies_opt_in' ) ) {
    $fields['checkboxes'] .= $fields['cookies'];
  }

  $fields['checkboxes'] .= $privacy_policy . '</div>';

  // Disable now redundant cookies field (do not unset, this can cause issues with cache plugins apparently)
  $fields['cookies'] = '';

  /**
   * Open the .fictioneer-respond__bottom wrapper around all input fields below
   * the textarea. It will be closed in the fictioneer_change_submit_field
   * method. Also added is the .fictioneer-respond__form-identity wrapper
   * around the author and email fields.
   */

  $fields['author'] = '<div class="fictioneer-respond__form-bottom ' . $hidden . '"><div class="fictioneer-respond__form-identity fictioneer-respond__form-bottom-block">' . $fields['author'];
  $fields['email'] = $fields['email'] . '</div>';

  return $fields;
}

if ( ! get_option( 'fictioneer_disable_comment_form' ) ) {
  add_filter( 'comment_form_default_fields', 'fictioneer_change_comment_fields' );
}

// =============================================================================
// COMMENT FORM SUBMIT
// =============================================================================

/**
 * Change the submit field and add the Cancel Reply link
 *
 * @since 4.7.0
 * @link https://github.com/WordPress/WordPress/blob/master/wp-includes/comment-template.php
 * @link https://stackoverflow.com/a/57080105/17140970
 *
 * @param string $submit_field  HTML markup for the submit field.
 * @param array  $args          Arguments passed to comment_form().
 */

function fictioneer_change_submit_field( $submit_field, $args ) {
  remove_filter( 'cancel_comment_reply_link', '__return_empty_string' );

  // Setup
  $post_id = get_the_ID();
  $order = fictioneer_sanitize_query_var( $_REQUEST['corder'] ?? 0, ['desc', 'asc'], get_option( 'comment_order' ) );
  $close_bottom_container = is_user_logged_in() ? '' : '</div>';
  $is_ajax = get_option( 'fictioneer_enable_ajax_comment_form' ) || get_option( 'fictioneer_enable_ajax_comments' );
  $hidden = FICTIONEER_COLLAPSE_COMMENT_FORM ? 'hidden' : '';

  /**
   * Build the submit button with the comment_form arguments. Markup:
   * <input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />
   */

  $submit_button = sprintf(
    '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" data-enabled="%4$s" data-disabled="Posting…">',
    esc_attr( $args['name_submit'] ),
    esc_attr( $args['id_submit'] ),
    esc_attr( $args['class_submit'] ) . ' button fictioneer-respond__form-submit',
    esc_attr( $args['label_submit'] )
  );

  // Private comment toggle
  $private_toggle = '';

  if ( get_option( 'fictioneer_enable_private_commenting' ) ) {
    $hint = esc_attr_x( 'Toggle to mark as private. Hides the comment from uninvolved viewers.', 'Comment form private toggle.', 'fictioneer' );
    $private_toggle = '<div class="fictioneer-private-comment-toggle fictioneer-respond__form-toggle"><label class="comment-respond-option-toggle" tabindex="0" role="checkbox" aria-checked="false" aria-label="' . $hint . '"><input name="fictioneer-private-comment-toggle" type="checkbox" tabindex="-1" hidden><span class="tooltipped _mobile-tooltip" data-tooltip="' . $hint . '"><i class="fa-solid fa-eye off"></i><i class="fa-solid fa-eye-slash on"></i></span></label></div>';
  }

  // Email subscriptions toggle
  $notification_toggle = '';
  $notifications_blocked = get_the_author_meta( 'fictioneer_admin_disable_comment_notifications', get_current_user_id() );
  $notification_checked = is_user_logged_in() && get_the_author_meta( 'fictioneer_comment_reply_notifications', get_current_user_id() ) && ! $notifications_blocked;
  $notification_checked = $notification_checked && ( ! get_option( 'fictioneer_enable_public_cache_compatibility' ) || $is_ajax );

  if ( get_option( 'fictioneer_enable_comment_notifications' ) && ! $notifications_blocked ) {
    $hint = esc_attr_x( 'Toggle to get email notifications about direct replies.', 'Comment form email notification toggle.', 'fictioneer' );
    $aria_checked = $notification_checked ? 'true' : 'false';
    $notification_toggle = '<div class="fictioneer-comment-notification-toggle fictioneer-respond__form-toggle"><label class="comment-respond-option-toggle" tabindex="0" role="checkbox" aria-checked="' . $aria_checked . '" aria-label="' . $hint . '"><input name="fictioneer-comment-notification-toggle" type="checkbox" ' . checked( 1, $notification_checked, false ) . ' tabindex="-1" hidden><span class="tooltipped _mobile-tooltip" data-tooltip="' . $hint . '"><i class="fa-solid fa-bell on"></i><i class="fa-solid fa-bell-slash off"></i></span></label></div>';
  }

  // Hidden parent_comment field
  $parent_field = '<input type="hidden" name="comment_parent" id="comment_parent" value="0">';

  // Hidden comment post ID field (fixed via JS)
  $comment_post_id = '<input type="hidden" name="comment_post_ID" value="0" id="comment_post_ID">';

  // Hidden order field
  $order_field = '<input type="hidden" name="corder" value="' . $order . '">';

  // Private comment notice for guests
  $private_notice = '';

  if ( ! is_user_logged_in() ) {
    if ( get_option( 'fictioneer_enable_comment_notifications' ) ) {
      $private_notice = __( '<div class="private-comment-notice hide-if-logged-in"><div><strong>Heads up!</strong> Your comment will be invisible to other guests and subscribers (except for replies), including you after a grace period. But if you submit an email address and toggle the bell icon, you will be sent replies until you cancel.</div></div>', 'fictioneer' );
    } else {
      $private_notice = __( '<div class="private-comment-notice hide-if-logged-in"><div><strong>Heads up!</strong> Your comment will be invisible to other guests and subscribers (except for replies), including you after a grace period.</div></div>', 'fictioneer' );
    }
  }

  /**
   * Enclose the submit fields in the .fictioneer-respond__form-actions wrapper,
   * which will also include the Cancel Reply button if visible. Also close the
   * .fictioneer-respond__bottom wrapper opened in fictioneer_change_comment_fields
   * _before_ the submit fields and append the Cancel Reply link.
   */

  return sprintf(
    $close_bottom_container . '<div class="form-submit fictioneer-respond__form-actions ' . $hidden . '"><div class="fictioneer-respond__form-actions-wrapper">' . $private_toggle . $notification_toggle . '%1$s %2$s</div>%3$s</div><div class="fictioneer-respond__notices">' . $private_notice . '</div>',
    $submit_button,
    ( $post_id ? get_comment_id_fields( $post_id ) : ( $parent_field . $comment_post_id ) ) . $order_field,
    preg_replace( '/<a/', '<a class="button _secondary"', get_cancel_comment_reply_link( 'Cancel' ) )
  );
}

if ( ! get_option( 'fictioneer_disable_comment_form' ) ) {
  add_filter( 'cancel_comment_reply_link', '__return_empty_string' );
  add_filter( 'comment_form_submit_field', 'fictioneer_change_submit_field', 10, 2 );
}

// =============================================================================
// COMMENT FORM
// =============================================================================

/**
 * Returns themed comment form arguments
 *
 * @since 5.0.0
 * @link https://developer.wordpress.org/reference/functions/comment_form/
 *
 * @param array    $defaults  Default form arguments. Defaults to empty array.
 * @param int|null $post_id   The post ID. Defaults to current post ID.
 *
 * @return array Modified arguments.
 */

function fictioneer_comment_form_args( $defaults = [], $post_id = null ) {
  // Setup
  if ( $post_id === null ) {
    $post_id = get_the_ID();
  }

  $user = wp_get_current_user();
  $user_name = $user->exists() ? $user->display_name : '';
  $toolbar = fictioneer_get_comment_toolbar();
  $oauth_links = fictioneer_get_oauth2_login_links( false, '', 'comments', $post_id );
  $profile_link = get_edit_profile_url();
  $profile_page_id = intval( get_option( 'fictioneer_user_profile_page', -1 ) ?: -1 );
  $aria_label_textarea = __( 'Leave a comments please', 'fictioneer' );

  if ( ! empty( $profile_page_id ) && $profile_page_id > 0 ) {
    $profile_link = fictioneer_get_assigned_page_link( 'fictioneer_user_profile_page' );
  }

  // Build arguments
  $class_container = 'comment-respond fictioneer-respond';
  $class_form = 'comment-form fictioneer-respond__form' . ( empty( $toolbar ) ? '' : ' _toolbar' );
  $comment_notes_before = empty( $oauth_links ) ? '' : '<div class="fictioneer-respond__form-before-form" data-nosnippet><div class="left">' . fcntr( 'log_in_with' ) . '</div><div class="right">' . $oauth_links . '</div></div>';
  $logged_in_as = sprintf(
    '<div class="logged-in-as fictioneer-respond__form-before-form" data-nosnippet><div class="left">%s</div></div>',
    sprintf(
      fcntr( 'logged_in_as' ),
      $profile_link,
      $user_name,
      fictioneer_get_logout_url( get_permalink( $post_id ) )
    )
  );
  $comment_field = "<div class='comment-form-comment fictioneer-respond__form-comment'><textarea id='comment' class='adaptive-textarea' aria-label='{$aria_label_textarea}' name='comment' maxlength='65525' required></textarea>{$toolbar}</div>";

  // Prepare arguments
  $args = array(
    'class_container' => $class_container,
    'class_form' => $class_form,
    'title_reply' => '',
    'title_reply_before' => '',
    'title_reply_after' => '',
    'title_reply_to' => '',
    'cancel_reply_before' => '',
    'cancel_reply_after' => '',
    'comment_notes_before' => $comment_notes_before,
    'logged_in_as' => $logged_in_as,
    'comment_field' => $comment_field,
    'format' => 'xhtml'
  );

  // Must be logged in to comment?
  if ( get_option( 'comment_registration' ) ) {
    if ( ! get_option( 'fictioneer_show_wp_login_link' ) && empty( $oauth_links ) ) {
      $args['must_log_in'] = sprintf(
        '<div class="fictioneer-respond__must-login">%s</div>',
        sprintf(
          __( 'You must be <a href="%s">logged in</a> to post a comment.' ),
          wp_login_url( apply_filters( 'the_permalink', get_permalink( $post_id ), $post_id ) )
        )
      );
    } else {
      $args['must_log_in'] = '<div class="fictioneer-respond__must-login">' . __( 'You must be <label for="modal-login-toggle">logged in</label> to comment.', 'fictioneer' ) . '</div>';
    }
  }

  // Return arguments which will be merged with missing defaults
  return $args;
}

if ( ! get_option( 'fictioneer_disable_comment_form' ) ) {
  add_filter( 'comment_form_defaults', 'fictioneer_comment_form_args' );
}
