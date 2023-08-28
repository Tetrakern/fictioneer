<?php

// =============================================================================
// CONTACT FORM SUBMIT - AJAX
// =============================================================================

/**
 * Submit comment form via AJAX
 *
 * @since Fictioneer 5.0
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_error/
 * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
 */

function fictioneer_ajax_submit_contact_form() {
  // Nonce (die on failure)
  check_ajax_referer( 'fictioneer_nonce', 'nonce' );

  // Emergency stop
  if ( get_option( 'fictioneer_disable_contact_forms' ) ) {
    wp_send_json_error( array( 'error' => _x( 'Contact forms have been disabled.', 'Contact form.', 'fictioneer' ) ) );
  }

  // Validations
  if ( empty( $_POST['message'] ) ) {
    wp_send_json_error( array( 'error' => _x( 'Message field empty.', 'Contact form.', 'fictioneer' ) ) );
  }

  if ( ! empty( $_POST['phone'] ) || filter_var( $_POST['terms'] ?? 0, FILTER_VALIDATE_BOOLEAN ) ) {
    // Pretend the submission worked if honeypot is triggered
    wp_send_json_success( array( 'success' => _x( 'Submission received!', 'Contact form.', 'fictioneer' ) ) );
  }

  if (
    $_POST['message'] != wp_strip_all_tags( $_POST['message'] ) ||
    ( ! empty( $_POST['name'] ) && $_POST['name'] != wp_strip_all_tags( $_POST['name'] ) )
  ) {
    // All fields are stripped of HTMl anyway, so this is only for the overly enthusiastic user.
    wp_send_json_error( array( 'error' => _x( 'Illegal HTML detected.', 'Contact form.', 'fictioneer' ) ) );
  }

  if ( ! empty( $_POST['email'] ) && ! is_email( $_POST['email'] ) ) {
    wp_send_json_error( array( 'error' => _x( 'Invalid email address.', 'Contact form.', 'fictioneer' ) ) );
  }

  if (
    filter_var( $_POST['require_privacy_policy'] ?? 0, FILTER_VALIDATE_BOOLEAN ) &&
    ! filter_var( $_POST['privacy_policy'] ?? 0, FILTER_VALIDATE_BOOLEAN )
  ) {
    // If the sender disables these fields, this is essentially acceptance
    // since they went out of their way to manipulate the form.
    wp_send_json_error( array( 'error' => _x( 'You need to accept the privacy policy.', 'Contact form.', 'fictioneer' ) ) );
  }

  // Setup
  $user = wp_get_current_user();
  $title = sanitize_text_field( $_POST['title'] ?? _x( 'Nameless Form', 'Contact form.', 'fictioneer' ) );
  $message = sanitize_textarea_field( $_POST['message'] );
  $email = sanitize_email( $_POST['email'] ?? '' );
  $name = sanitize_text_field( $_POST['name'] ?? '' );
  $headers = ['Content-Type: text/html; charset=UTF-8'];
  $strings = [];

  // Strip HTML for good measure
  $title = wp_strip_all_tags( $title );
  $message = wpautop( wp_strip_all_tags( $message ) );
  $name = wp_strip_all_tags( $name );

  // Add to $strings
  $strings[] = $title;
  $strings[] = $message;
  $strings[] = $name;

  // Email body...
  $html = '<p><strong>' . sprintf( _x( 'Form submission: %s', 'Contact form.', 'fictioneer' ), $title ) . '</strong></p>';
  $html .= '<fieldset>' . $message . '</fieldset>';

  // ... email field
  if ( ! empty( $email ) ) {
    $html .= '<p>' . sprintf( _x( '<strong>Email Address:</strong> %s', 'Contact form.', 'fictioneer' ), $email ) . '</p>';
  }

  // ... name field
  if ( ! empty( $name ) ) {
    $html .= '<p>' . sprintf( _x( '<strong>Name:</strong> %s', 'Contact form.', 'fictioneer' ), $name ) . '</p>';
  }

  // ... text fields
  for ( $i = 1; $i <= 6; $i++ ) {
    // Get label and value (if any)
    $field_value = sanitize_text_field( $_POST[ "text_$i" ] ?? '' );
    $field_label = sanitize_text_field( $_POST[ "text_label_$i" ] ?? '' );

    // Skip if label is missing
    if ( empty( $field_label ) ) {
      continue;
    }

    // Build field
    if ( ! empty( $field_value ) ) {
      // Strip HTML for good measure
      $field_value = wp_strip_all_tags( $field_value );
      $field_label = wp_strip_all_tags( $field_label );

      // Add to $strings
      $strings[] = $field_value;
      $strings[] = $field_value;

      // Add to email body
      $html .= '<p>' . sprintf(
        _x( '<strong>%1$s:</strong> %2$s', 'Contact form.', 'fictioneer' ),
        $field_label,
        $field_value
      ) . '</p>';
    }
  }

  // ... checkboxes
  for ( $i = 1; $i <= 6; $i++ ) {
    // Get label and value (if any)
    $field_value = filter_var( $_POST[ "check_$i" ] ?? 0, FILTER_VALIDATE_BOOLEAN );
    $field_label = sanitize_text_field( $_POST[ "check_label_$i" ] ?? '' );

    // Skip if label is missing
    if ( empty( $field_label ) ) {
      continue;
    }

    // Strip HTML for good measure
    $field_label = wp_strip_all_tags( $field_label );

    // Add to $strings
    $strings[] = $field_label;

    // Build field
    $html .= '<p>' . sprintf(
      _x( '<strong>%1$s:</strong> %2$s', 'Contact form.', 'fictioneer' ),
      $field_label,
      $field_value ? __( 'True', 'fictioneer' ) : __( 'False', 'fictioneer' )
    ) . '</p>';
  }

  // Hook for additional spam protection
  do_action( 'fictioneer_contact_form_validation', $strings );

  // ... IP and user agent if privacy policy acceptance is required
  if ( filter_var( $_POST['require_privacy_policy'] ?? 0, FILTER_VALIDATE_BOOLEAN ) ) {
    // IP (unreliable)
    $html .= '<p>' . sprintf(
      _x( '<strong>%1$s:</strong> %2$s', 'Contact form.', 'fictioneer' ),
      __( 'IP Address', 'fictioneer' ),
      $_SERVER['REMOTE_ADDR'] ?? ''
    ) . '</p>';

    // User agent (unreliable)
    $html .= '<p>' . sprintf(
      _x( '<strong>%1$s:</strong> %2$s', 'Contact form.', 'fictioneer' ),
      __( 'User Agent', 'fictioneer' ),
      $_SERVER['HTTP_USER_AGENT'] ?? ''
    ) . '</p>';
  }

  // ... user if logged in
  if ( is_user_logged_in() ) {
    $user_info = get_userdata( $user->ID );

    $html .= '<p>' . sprintf(
      _x( '<strong>%1$s:</strong> %2$s', 'Contact form.', 'fictioneer' ),
      __( 'User', 'fictioneer' ),
      sprintf( _x( '%1$s (%2$s)', 'Contact form.', 'fictioneer' ), $user_info->user_login, $user->ID )
    ) . '</p>';
  }

  // Check against disallow list (Settings > Discussion)
  $offenders = fictioneer_check_comment_disallowed_list(
    $name,
    $email,
    '',
    $message,
    $_SERVER['REMOTE_ADDR'] ?? '',
    $_SERVER['HTTP_USER_AGENT'] ?? ''
  );

  // Only show error for keys in content, trash anything else as usual later.
  // No need to tell someone his name or email address is blocked, etc.
  if ( FICTIONEER_DISALLOWED_KEY_NOTICE && $offenders[0] && $offenders[1] ) {
    wp_send_json_error(
      array( 'error' => __( 'Disallowed key found: "' . implode( ', ', $offenders[1] )  . '".', 'fictioneer' ) )
    );
  } elseif ( $offenders[0] ) {
    wp_send_json_error( array( 'error' => __( 'Disallowed keys found.', 'fictioneer' ) ) );
  }

  // Addresses
  $to_addresses = array_filter( explode( "\n", get_option( 'fictioneer_contact_email_addresses' ) ) );
  $to_addresses = empty( $to_addresses ) ? [get_bloginfo( 'admin_email' )] : $to_addresses;
  $from_address = get_option( 'fictioneer_system_email_address' );

  // Headers
  if ( ! empty( $from_address ) ) {
    $from_name = get_option( 'fictioneer_system_email_name' );
    $from_name = empty ( $from_name ) ? FICTIONEER_SITE_NAME : $from_name;
    $headers[] = 'From: ' . trim( $from_name )  . ' <'  . trim( $from_address ) . '>';
  }

  // Send email to each recipient
  foreach ( $to_addresses as $to ) {
    $to = sanitize_email( $to );

    // Skip if not valid email address
    if ( ! is_email( $to ) ) {
      continue;
    }

    // Send email
    wp_mail(
      $to,
      sprintf( _x( 'Form submission: %s', 'Contact form.', 'fictioneer' ), $title ),
      $html,
      $headers
    );
  }

  // Response
  wp_send_json_success( array( 'success' => _x( 'Submission received!', 'Contact form.', 'fictioneer' ) ) );
}
add_action( 'wp_ajax_fictioneer_ajax_submit_contact_form', 'fictioneer_ajax_submit_contact_form' );
add_action( 'wp_ajax_nopriv_fictioneer_ajax_submit_contact_form', 'fictioneer_ajax_submit_contact_form' );

?>
