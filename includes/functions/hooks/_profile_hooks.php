<?php

// =============================================================================
// ACCOUNT MODERATION MESSAGE SECTION
// =============================================================================

if ( ! function_exists( 'fictioneer_account_moderation_message' ) ) {
  /**
   * Outputs the HTML for the account moderation message section
   *
   * @since Fictioneer 5.0
   *
   * @param WP_User $args['user']         Current user.
   * @param boolean $args['is_admin']     True if the user is an administrator.
   * @param boolean $args['is_author']    True if the user is an author (by capabilities).
   * @param boolean $args['is_editor']    True if the user is an editor.
   * @param boolean $args['is_moderator'] True if the user is a moderator (by capabilities).
   */

  function fictioneer_account_moderation_message( $args ) {
    // Setup
    $message = get_the_author_meta( 'fictioneer_admin_moderation_message', $args['user']->ID );

    // Abort conditions
    if ( empty( $message ) ) return;

    // Start HTML ---> ?>
    <div class="profile__moderation-message profile__segment">
      <div class="infobox polygon">
        <p><?php echo $message; ?></p>
      </div>
    </div>
    <?php // <--- End HTML
  }
}
add_action( 'fictioneer_account_content', 'fictioneer_account_moderation_message', 5 );

// =============================================================================
// ACCOUNT PROFILE SECTION
// =============================================================================

if ( ! function_exists( 'fictioneer_account_profile' ) ) {
  /**
   * Outputs the HTML for the account profile section
   *
   * @since Fictioneer 5.0
   *
   * @param WP_User $args['user']         Current user.
   * @param boolean $args['is_admin']     True if the user is an administrator.
   * @param boolean $args['is_author']    True if the user is an author (by capabilities).
   * @param boolean $args['is_editor']    True if the user is an editor.
   * @param boolean $args['is_moderator'] True if the user is a moderator (by capabilities).
   */

  function fictioneer_account_profile( $args ) {
    get_template_part( 'partials/account/_profile', null, $args );
  }
}
add_action( 'fictioneer_account_content', 'fictioneer_account_profile', 10 );

// =============================================================================
// ACCOUNT OAUTH BINDINGS SECTION
// =============================================================================

if ( ! function_exists( 'fictioneer_account_oauth' ) ) {
  /**
   * Outputs the HTML for the OAuth section
   *
   * @since Fictioneer 5.0
   *
   * @param WP_User $args['user']         Current user.
   * @param boolean $args['is_admin']     True if the user is an administrator.
   * @param boolean $args['is_author']    True if the user is an author (by capabilities).
   * @param boolean $args['is_editor']    True if the user is an editor.
   * @param boolean $args['is_moderator'] True if the user is a moderator (by capabilities).
   */

  function fictioneer_account_oauth( $args ) {
    get_template_part( 'partials/account/_oauth', null, $args );
  }
}
add_action( 'fictioneer_account_content', 'fictioneer_account_oauth', 20 );

// =============================================================================
// ACCOUNT DATA SECTION
// =============================================================================

if ( ! function_exists( 'fictioneer_account_data' ) ) {
  /**
   * Outputs the HTML for the data section
   *
   * @since Fictioneer 5.0
   *
   * @param WP_User $args['user']         Current user.
   * @param boolean $args['is_admin']     True if the user is an administrator.
   * @param boolean $args['is_author']    True if the user is an author (by capabilities).
   * @param boolean $args['is_editor']    True if the user is an editor.
   * @param boolean $args['is_moderator'] True if the user is a moderator (by capabilities).
   */

  function fictioneer_account_data( $args ) {
    get_template_part( 'partials/account/_data', null, $args );
  }
}
add_action( 'fictioneer_account_content', 'fictioneer_account_data', 30 );

// =============================================================================
// ACCOUNT DISCUSSION SECTION
// =============================================================================

if ( ! function_exists( 'fictioneer_account_discussions' ) ) {
  /**
   * Outputs the HTML for the discussions section
   *
   * @since Fictioneer 5.0
   *
   * @param WP_User $args['user']         Current user.
   * @param boolean $args['is_admin']     True if the user is an administrator.
   * @param boolean $args['is_author']    True if the user is an author (by capabilities).
   * @param boolean $args['is_editor']    True if the user is an editor.
   * @param boolean $args['is_moderator'] True if the user is a moderator (by capabilities).
   */

  function fictioneer_account_discussions( $args ) {
    get_template_part( 'partials/account/_discussions', null, $args );
  }
}
add_action( 'fictioneer_account_content', 'fictioneer_account_discussions', 40 );

// =============================================================================
// ACCOUNT DANGER ZONE SECTION
// =============================================================================

if ( ! function_exists( 'fictioneer_account_danger_zone' ) ) {
  /**
   * Outputs the HTML for the danger zone section
   *
   * @since Fictioneer 5.0
   *
   * @param WP_User $args['user']         Current user.
   * @param boolean $args['is_admin']     True if the user is an administrator.
   * @param boolean $args['is_author']    True if the user is an author (by capabilities).
   * @param boolean $args['is_editor']    True if the user is an editor.
   * @param boolean $args['is_moderator'] True if the user is a moderator (by capabilities).
   */

  function fictioneer_account_danger_zone( $args ) {
    get_template_part( 'partials/account/_danger-zone', null, $args );
  }
}

if ( get_option( 'fictioneer_enable_subscriber_self_delete' ) ) {
  add_action( 'fictioneer_account_content', 'fictioneer_account_danger_zone', 100 );
}

?>
