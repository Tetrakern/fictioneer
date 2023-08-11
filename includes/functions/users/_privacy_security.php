<?php

// =============================================================================
// HIDE PRIVACY SENSITIVE DATA FROM NON-ADMINISTRATORS
// =============================================================================

/**
 * Remove email and name columns from user table
 *
 * @since Fictioneer 4.7
 *
 * @param array $column_headers Columns to show in the user table.
 *
 * @return array Reduced columns to show in the user table.
 */

function fictioneer_hide_users_columns( $column_headers ) {
  unset( $column_headers['email'] );
  unset( $column_headers['name'] );
  return $column_headers;
}

/**
 * Remove quick edit from comments table
 *
 * The quick edit form for comments shows unfortunately private data that
 * we want to hide if that setting is enabled.
 *
 * @since Fictioneer 4.7
 *
 * @param array $actions Actions per row in the comments table.
 *
 * @return array Restricted actions per row in the comments table.
 */

function fictioneer_remove_quick_edit( $actions ) {
  unset( $actions['quickedit'] );
  return $actions;
}

/**
 * Remove URL and email fields from comment edit page
 *
 * Since these are not normally accessible, we need to quickly hide them
 * with JavaScript. This is not a great solution but better than nothing.
 *
 * @since Fictioneer 4.7
 */

function fictioneer_hide_private_data() {
  wp_add_inline_script(
    'fictioneer-admin-script',
    "jQuery(function($) {
      $('.editcomment tr:nth-child(3)').remove();
      $('.editcomment tr:nth-child(2)').remove();
    });"
  );
}

/**
 * Add filters and action depending on security settings
 */

if ( get_option( 'fictioneer_admin_restrict_private_data' ) && ! current_user_can( 'administrator' ) ) {
  add_filter( 'manage_users_columns', 'fictioneer_hide_users_columns', 99 );
  add_filter( 'comment_email', '__return_false', 99 );
  add_filter( 'get_comment_author_IP', '__return_empty_string', 99 );
  add_filter( 'comment_row_actions', 'fictioneer_remove_quick_edit', 99 );
  add_action( 'admin_enqueue_scripts', 'fictioneer_hide_private_data', 99 );
}

?>
