<?php

// =============================================================================
// AJAX: SAVE SKINS FOR USERS
// =============================================================================

/**
 * AJAX: Saves skins JSON
 *
 * Note: Skins are not evaluated server-side, only stored as JSON string.
 * Everything else happens client-side.
 *
 * @since 5.26.0
 */

function fictioneer_ajax_save_skins() {
  // Enabled?
  if ( ! get_option( 'fictioneer_enable_css_skins' ) ) {
    wp_send_json_error( null, 403 );
  }

  // Rate limit
  fictioneer_check_rate_limit( 'fictioneer_ajax_save_skins', 5 );

  // Setup and validations
  $user = fictioneer_get_validated_ajax_user();

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => 'Request did not pass validation.' ) );
  }

  if ( empty( $_POST['skins'] ?? 0 ) ) {
    wp_send_json_error( array( 'error' => 'Missing arguments.' ) );
  }

  // Sanitize
  $skins = sanitize_text_field( $_POST['skins'] );

  if ( $skins && fictioneer_is_valid_json( wp_unslash( $skins ) ) ) {
    // Inspect
    $decoded = json_decode( wp_unslash( $skins ), true );
    $fingerprint = fictioneer_get_user_fingerprint( $user->ID );

    if ( ! $decoded || ! isset( $decoded['data'] ) ) {
      wp_send_json_error( array( 'error' => 'Invalid JSON (SKIN-001).' ) );
    }

    if ( ( $decoded['fingerprint'] ?? 0 ) !== $fingerprint ) {
      wp_send_json_error( array( 'error' => 'Invalid JSON (SKIN-002).' ) );
    }

    if ( count( $decoded ) > 3 ) {
      wp_send_json_error( array( 'error' => 'Invalid JSON (SKIN-003).' ) );
    }

    foreach( $decoded['data'] as $sub_array ) {
      if ( ! is_array( $sub_array ) ) {
        wp_send_json_error( array( 'error' => 'Invalid JSON (SKIN-004).' ) );
      }

      if ( count( $sub_array ) > 4 ) {
        wp_send_json_error( array( 'error' => 'Invalid JSON (SKIN-005).' ) );
      }

      $allowed_keys = ['name', 'author', 'version', 'css'];

      foreach ( $sub_array as $sub_key => $value ) {
        if ( ! in_array( $sub_key, $allowed_keys ) ) {
          wp_send_json_error( array( 'error' => 'Invalid JSON (SKIN-006).' ) );
        }

        if ( $sub_key === 'css' && fictioneer_sanitize_css( $value ) === '' ) {
          wp_send_json_error( array( 'error' => 'Invalid CSS (SKIN-007).' ) );
        }
      }
    }

    // Easier than checking whether the skins have changed
    delete_user_meta( $user->ID, 'fictioneer_skins' );

    if ( update_user_meta( $user->ID, 'fictioneer_skins', $skins ) ) {
      wp_send_json_success( array( 'message' => __( 'Skins uploaded successfully.', 'fictioneer' ) ) );
    } else {
      wp_send_json_error( array( 'error' => 'Skins could not be uploaded.' ) );
    }
  }

  // Something went wrong if we end up here...
  wp_send_json_error( array( 'error' => 'An unknown error occurred.' ) );
}

if ( get_option( 'fictioneer_enable_css_skins' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_save_skins', 'fictioneer_ajax_save_skins' );
}

/**
 * AJAX: Sends skins JSON
 *
 * @since 5.26.0
 */

function fictioneer_ajax_get_skins() {
  // Enabled?
  if ( ! get_option( 'fictioneer_enable_css_skins' ) ) {
    wp_send_json_error( null, 403 );
  }

  // Rate limit
  fictioneer_check_rate_limit( 'fictioneer_ajax_get_skins', 5 );

  // Setup and validations
  $user = fictioneer_get_validated_ajax_user();

  if ( ! $user ) {
    wp_send_json_error( array( 'error' => 'Request did not pass validation.' ) );
  }

  // Look for saved skins on user...
  $skins = get_user_meta( $user->ID, 'fictioneer_skins', true );

  // Response
  if ( $skins ) {
    wp_send_json_success(
      array(
        'skins' => $skins,
        'message' => __( 'Skins downloaded and applied successfully.', 'fictioneer' )
      )
    );
  }

  wp_send_json_error();
}

if ( get_option( 'fictioneer_enable_css_skins' ) ) {
  add_action( 'wp_ajax_fictioneer_ajax_get_skins', 'fictioneer_ajax_get_skins' );
}
