<?php

// =============================================================================
// PERFORMANCE MEASUREMENT
// =============================================================================

/**
 * Stored current microtime globally
 *
 * @since 5.26.0
 */

function fictioneer_start_micro_time() {
  global $fictioneer_start_time;

  $fictioneer_start_time = microtime( 1 );
}

/**
 * Writes elapses microtime to log
 *
 * @since 5.26.0
 */

function fictioneer_log_micro_time( $context = '' ) {
  global $fictioneer_start_time;

  if ( $fictioneer_start_time ) {
    $prefix = $context ? "({$context}) Duration: " : 'Duration: ';
    error_log( $prefix . ( microtime( 1 ) - $fictioneer_start_time ) );
  }

  $fictioneer_start_time = null;
}

// =============================================================================
// GENERATE TEST CONTENT
// =============================================================================

/**
 * Logs a message to the theme log file
 *
 * @since 5.23.1
 *
 * @return string Lorem ipsum content for posts.
 */

function fictioneer_lorem_ipsum_content() {
  static $content = null;

  if ( $content ) {
    return $content;
  }

  $lorem_ipsum = "<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>";
  $content = '';

  for ( $i = 0; $i < 100; $i++ ) {
    $content .= "\n" . $lorem_ipsum;
  }

  return $content;
}

/**
 * Generates a number of test comments
 *
 * @since 5.23.1
 *
 * @param int $post_id  Post ID the comment belongs to
 * @param int $number   Number of comments to create.
 */

function fictioneer_generate_test_comments( $post_id, $number = 3 ) {
  for ( $i = 0; $i < $number; $i++ ) {
    $comment_data = array(
      'comment_post_ID' => $post_id,
      'comment_author' => 'Dummy Author ' . ( $i + 1 ),
      'comment_content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
      'comment_type' => 'comment',
      'comment_parent' => 0,
      'user_id' => 0,
      'comment_approved' => 1
    );

    // Insert the comment into the database
    wp_insert_comment( $comment_data );
  }
}

/**
 * Generates a test users, stories, and chapters
 *
 * @since 5.23.1
 */

function fictioneer_generate_test_content() {
  if ( ! current_user_can( 'manage_options' ) || get_current_user_id() !== 1 || ! WP_DEBUG ) {
    return;
  }

  // Disable Discord
  remove_action( 'comment_post', 'fictioneer_post_comment_to_discord', 99 );
  remove_action( 'save_post', 'fictioneer_post_story_to_discord', 99 );
  remove_action( 'save_post', 'fictioneer_post_chapter_to_discord', 99 );

  // Setup
  $user_count = absint( $_GET['users'] ?? 1 );
  $chapter_count = absint( $_GET['chapters'] ?? 50 );
  $comment_count = absint( $_GET['comments'] ?? 3 );

  // For a number of times...
  for ( $i = 0; $i < $user_count; $i++ ) {
    // ... create a test user
    $username = fictioneer_get_random_username();
    $password = wp_generate_password();
    $email = $username . '@fictioneer-example.com';
    $user_id = wp_create_user( $username, $password, $email );

    if ( is_wp_error( $user_id ) ) {
      continue;
    }

    $user = get_user_by( 'ID', $user_id );
    $user->set_role( 'author' );

    $taxonomies = [];

    foreach ( ['fcn_genre', 'fcn_character', 'fcn_content_warning', 'post_tag'] as $tax_type ) {
      $terms = get_terms(
        array(
          'taxonomy' => $tax_type,
          'orderby'=> 'rand',
          'number'=> 10,
          'hide_empty' => false
        )
      );

      if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
        $taxonomies[ $tax_type ] = wp_list_pluck( $terms, 'term_id' );
      }
    }

    // ... create a test story for the user
    $story_post = array(
      'post_type' => 'fcn_story',
      'post_status' => 'publish',
      'post_author' => $user_id,
      'post_title' => $username . "'s Story",
      'post_content' => 'This is a test story.',
    );

    $story_id = wp_insert_post( $story_post );

    if ( ! $story_id || is_wp_error( $story_id ) ) {
      continue;
    }

    if ( $taxonomies ) {
      foreach ( $taxonomies as $taxonomy => $term_ids ) {
        wp_set_object_terms( $story_id, $term_ids, $taxonomy );
      }
    }

    update_post_meta( $story_id, 'fictioneer_story_rating', 'Teen' );
    update_post_meta( $story_id, 'fictioneer_story_status', 'Ongoing' );

    // ... create and add test chapters to the story
    $chapter_ids = [];

    for ( $j = 1; $j <= $chapter_count; $j++ ) {
      $chapter_post = array(
        'post_type' => 'fcn_chapter',
        'post_status' => 'publish',
        'post_author' => $user_id,
        'post_title' => "Chapter {$j} of {$username}'s Story",
        'post_content' => fictioneer_lorem_ipsum_content(),
      );

      $chapter_id = wp_insert_post( $chapter_post );

      if ( $chapter_id && ! is_wp_error( $chapter_id ) ) {
        $chapter_ids[] = $chapter_id;

        if ( $taxonomies ) {
          foreach ( $taxonomies as $taxonomy => $term_ids ) {
            wp_set_object_terms( $story_id, $term_ids, $taxonomy );
          }
        }

        fictioneer_generate_test_comments( $chapter_id, $comment_count );

        update_post_meta( $chapter_id, 'fictioneer_chapter_story', $story_id );
        fictioneer_set_chapter_story_parent( $chapter_id, $story_id );
      }
    }

    if ( count( $chapter_ids ) > 0 ) {
      update_post_meta( $story_id, 'fictioneer_story_chapters', array_map( 'strval', $chapter_ids ) );
      update_post_meta( $story_id, 'fictioneer_chapters_modified', current_time( 'mysql' ) );
      update_post_meta( $story_id, 'fictioneer_chapters_added', current_time( 'mysql', true ) );

      wp_update_post( array( 'ID' => $story_id ) );
    }
  }

  // Clean up
  delete_transient( 'fictioneer_stories_statistics' );
  delete_transient( 'fictioneer_stories_total_word_count' );
  delete_transient( 'fictioneer_main_nav_menu_html' );
  delete_transient( 'fictioneer_footer_menu_html' );

  fictioneer_delete_transients_like( 'fictioneer_' );

  fictioneer_clear_all_cached_partials();

  update_option( 'fictioneer_story_or_chapter_updated_timestamp', time() * 1000 );

  // Finish
  wp_die( 'Done' );
}
add_action( 'admin_post_fictioneer_generate_test_content', 'fictioneer_generate_test_content' );

// =============================================================================
// SHOW HOOKS IN HTML
// =============================================================================

add_action(
  'all',
  function ( $tag ) {
    static $actions = [
      'wp_head',
      'wp_body_open',
      'wp_footer',
      'loop_start',
      'loop_end',
      'fictioneer_account_content',
      'fictioneer_account_data_nodes',
      'fictioneer_admin_settings_connections',
      'fictioneer_admin_settings_general',
      'fictioneer_admin_settings_phrases',
      'fictioneer_admin_settings_tools',
      'fictioneer_admin_user_sections',
      'fictioneer_after_main',
      'fictioneer_after_update',
      'fictioneer_archive_loop_after',
      'fictioneer_archive_loop_before',
      'fictioneer_before_comments',
      'fictioneer_body',
      'fictioneer_chapter_actions_bottom_center',
      'fictioneer_chapter_actions_bottom_left',
      'fictioneer_chapter_actions_bottom_right',
      'fictioneer_chapter_actions_top_center',
      'fictioneer_chapter_actions_top_left',
      'fictioneer_chapter_actions_top_right',
      'fictioneer_chapter_after_content',
      'fictioneer_chapter_after_header',
      'fictioneer_chapter_after_main',
      'fictioneer_chapter_before_comments',
      'fictioneer_chapter_before_header',
      'fictioneer_chapters_after_content',
      'fictioneer_chapters_end_of_results',
      'fictioneer_chapters_no_results',
      'fictioneer_collection_after_content',
      'fictioneer_collection_after_header',
      'fictioneer_collection_footer',
      'fictioneer_collections_after_content',
      'fictioneer_collections_end_of_results',
      'fictioneer_collections_no_results',
      'fictioneer_footer',
      'fictioneer_inner_header',
      'fictioneer_large_card_body_chapter',
      'fictioneer_large_card_body_collection',
      'fictioneer_large_card_body_page',
      'fictioneer_large_card_body_post',
      'fictioneer_large_card_body_recommendation',
      'fictioneer_large_card_body_story',
      'fictioneer_main',
      'fictioneer_main_end',
      'fictioneer_main_wrapper',
      'fictioneer_mobile_menu_bottom',
      'fictioneer_mobile_menu_center',
      'fictioneer_mobile_menu_main_frame_panels',
      'fictioneer_mobile_menu_top',
      'fictioneer_modals',
      'fictioneer_modal_login_option',
      'fictioneer_navigation_bottom',
      'fictioneer_navigation_top',
      'fictioneer_navigation_wrapper_end',
      'fictioneer_navigation_wrapper_start',
      'fictioneer_post_after_content',
      'fictioneer_post_article_open',
      'fictioneer_post_footer_left',
      'fictioneer_post_footer_right',
      'fictioneer_recommendation_after_content',
      'fictioneer_recommendation_after_header',
      'fictioneer_recommendation_footer',
      'fictioneer_recommendations_after_content',
      'fictioneer_recommendations_end_of_results',
      'fictioneer_recommendations_no_results',
      'fictioneer_search_footer',
      'fictioneer_search_form_after',
      'fictioneer_search_form_filters',
      'fictioneer_search_no_params',
      'fictioneer_search_no_results',
      'fictioneer_shortcode_latest_chapters_card_body',
      'fictioneer_shortcode_latest_recommendations_card_body',
      'fictioneer_shortcode_latest_stories_card_body',
      'fictioneer_shortcode_latest_updates_card_body',
      'fictioneer_site',
      'fictioneer_site_footer',
      'fictioneer_stories_after_content',
      'fictioneer_stories_end_of_results',
      'fictioneer_stories_no_results',
      'fictioneer_story_after_article',
      'fictioneer_story_after_content',
      'fictioneer_story_after_header',
      'fictioneer_story_before_comments_list',
      'fictioneer_text_center_header',
      'fictioneer_top_header',
    ];

    if ( ! in_array( $tag, $actions, true ) ) {
      return;
    }

    if ( ! is_admin() && ! doing_action( 'wp_ajax' ) && ! headers_sent() ) {
      echo "\n<!-- action: $tag -->\n";
    }
  },
  0
);
