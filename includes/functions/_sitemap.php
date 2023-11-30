<?php

// =============================================================================
// (RE-) BUILD SITEMAP WHEN POST/PAGE IS SAVED
// =============================================================================

/**
 * Get <loc> tag helper
 *
 * @since Fictioneer 4.0
 *
 * @param string $content  Content for the tag.
 *
 * @return string Tag with content.
 */

function fictioneer_loc_node( $content ) {
  return '<loc>' . $content . '</loc>';
}

/**
 * Get <lastmod> tag helper
 *
 * @since Fictioneer 4.0
 *
 * @param string $content  Content for the tag.
 *
 * @return string Tag with content.
 */

function fictioneer_lastmod_node( $content ) {
  return '<lastmod>' . $content . '</lastmod>';
}

/**
 * Get <changefreq> tag helper
 *
 * @since Fictioneer 4.0
 *
 * @param string $content  Content for the tag.
 *
 * @return string Tag with content.
 */

function fictioneer_frequency_node( $content ) {
  return '<changefreq>' . $content . '</changefreq>';
}

/**
 * Get <url> node helper
 *
 * @since Fictioneer 4.0
 * @see fictioneer_loc_node()
 * @see fictioneer_lastmod_node()
 * @see fictioneer_frequency_node()
 *
 * @param string $loc      URL.
 * @param string $lastmod  Optional. Last modified timestamp.
 * @param string $freq     Optional. Refresh frequency, e.g. 'daily' or 'yearly'.
 *
 * @return string Node with tags.
 */

function fictioneer_url_node( $loc, $lastmod = null, $freq = null ) {
  $node = "\t" . '<url>' . "\n\t\t" . fictioneer_loc_node( $loc ) . "\n";
  $node = $lastmod ? $node . "\t\t" . fictioneer_lastmod_node( $lastmod ) . "\n" : $node;
  $node = $freq ? $node . "\t\t" . fictioneer_frequency_node( $freq ) . "\n" : $node;
  $node .= "\t" . '</url>' . "\n";

  return $node;
}

/**
 * Generate theme sitemap
 *
 * @since Fictioneer 4.0
 *
 * @param int     $last_saved_id    ID of the last saved post.
 * @param WP_Post $last_saved_post  The last saved post object.
 */

function fictioneer_create_sitemap( $last_saved_id, $last_saved_post ) {
  // Prevent multi-fire
  if ( fictioneer_multi_save_guard( $last_saved_id ) ) {
    return;
  }

  // Only rebuild necessary part of the sitemap
  $last_saved_type = $last_saved_post->post_type;

  // Only consider these post types
  $allowed_types = ['page', 'post', 'fcn_story', 'fcn_chapter', 'fcn_recommendation', 'fcn_collection'];

  if ( in_array( $last_saved_type, $allowed_types ) ) {
    // Open
    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $sitemap .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    // Blog and front page
    $sitemap .= fictioneer_url_node( esc_url( home_url( '/' ) ), current_time( 'c' ), 'daily' );
    $sitemap .= fictioneer_url_node( get_permalink( get_option( 'page_for_posts' ) ), current_time( 'c' ), 'daily' );

    // Pages
    $pages = $last_saved_type == 'page' ? null : get_transient( 'fictioneer_sitemap_pages' );

    if ( ! $pages ) {
      $pages = get_posts(
        array(
          'post_type' => 'page',
          'post_status' => 'publish',
          'orderby' => 'date',
          'order' => 'DESC',
          'numberposts' => '1000',
          'post__not_in' => [get_option( 'page_on_front' ), get_option( 'page_for_posts' )],
          'update_post_meta_cache' => false,
          'update_post_term_cache' => false,
          'no_found_rows' => true
        )
      );

      set_transient( 'fictioneer_sitemap_pages', $pages );
    }

    foreach( $pages as $post ) {
      $template = get_page_template_slug( $post->ID );

      if (
        in_array(
          $template,
          ['user-profile.php', 'singular-bookmarks.php', 'singular-bookshelf.php', 'singular-bookshelf-ajax.php']
        )
      ) {
        continue;
      }

      $frequency = 'yearly';
      $lastmod = get_the_modified_date( 'c', $post->ID );
      $sitemap .= fictioneer_url_node( get_permalink( $post->ID ), $lastmod, 'yearly' );
    }

    // Blogs
    $blogs = $last_saved_type == 'post' ? null : get_transient( 'fictioneer_sitemap_posts' );

    if ( ! $blogs ) {
      $blogs = get_posts(
        array(
          'post_type' => 'post',
          'post_status' => 'publish',
          'orderby' => 'date',
          'order' => 'DESC',
          'numberposts' => '1000',
          'update_post_meta_cache' => false,
          'update_post_term_cache' => false,
          'no_found_rows' => true
        )
      );

      set_transient( 'fictioneer_sitemap_posts', $blogs );
    }

    foreach( $blogs as $post ) {
      $lastmod = get_the_modified_date( 'c', $post->ID );
      $sitemap .= fictioneer_url_node( get_permalink( $post->ID ), $lastmod, 'never' );
    }

    // Collections
    $collections = $last_saved_type == 'fcn_collection' ? null : get_transient( 'fictioneer_sitemap_collections' );

    if ( ! $collections ) {
      $collections = get_posts(
        array(
          'post_type' => 'fcn_collection',
          'post_status' => 'publish',
          'orderby' => 'date',
          'order' => 'DESC',
          'numberposts' => '1000',
          'update_post_meta_cache' => false,
          'update_post_term_cache' => false,
          'no_found_rows' => true
        )
      );

      set_transient( 'fictioneer_sitemap_collections', $collections );
    }

    foreach( $collections as $post ) {
      $lastmod = get_the_modified_date( 'c', $post->ID );
      $sitemap .= fictioneer_url_node( get_permalink( $post->ID ), $lastmod, 'monthly' );
    }

    // Stories
    $stories = $last_saved_type == 'fcn_story' ? null : get_transient( 'fictioneer_sitemap_stories' );

    if ( ! $stories ) {
      $stories = get_posts(
        array(
          'post_type' => 'fcn_story',
          'post_status' => 'publish',
          'orderby' => 'date',
          'order' => 'DESC',
          'numberposts' => '2000',
          'update_post_meta_cache' => true,
          'update_post_term_cache' => false,
          'no_found_rows' => true
        )
      );

      set_transient( 'fictioneer_sitemap_stories', $stories );
    }

    foreach ( $stories as $post ) {
      if ( get_post_meta( $post->ID, 'fictioneer_story_hidden', true ) ) {
        continue;
      }

      $lastmod = get_the_modified_date( 'c', $post->ID );
      $status = get_post_meta( $post->ID, 'fictioneer_story_status', true );
      $frequency = $status == 'Ongoing' ? 'weekly' : 'monthly';
      $sitemap .= fictioneer_url_node( get_permalink( $post->ID ), $lastmod, $frequency );
    }

    // Chapters
    $chapters = $last_saved_type == 'fcn_chapter' ? null : get_transient( 'fictioneer_sitemap_chapters' );

    if ( ! $chapters ) {
      $chapters = get_posts(
        array(
          'post_type' => 'fcn_chapter',
          'post_status' => 'publish',
          'orderby' => 'date',
          'order' => 'DESC',
          'numberposts' => '10000',
          'update_post_meta_cache' => true,
          'update_post_term_cache' => false,
          'no_found_rows' => true
        )
      );

      set_transient( 'fictioneer_sitemap_chapters', $chapters );
    }

    foreach( $chapters as $post ) {
      if ( get_post_meta( $post->ID, 'fictioneer_chapter_hidden', true ) ) {
        continue;
      }

      $lastmod = get_the_modified_date( 'c', $post->ID );
      $sitemap .= fictioneer_url_node( get_permalink( $post->ID ), $lastmod, 'monthly' );
    }

    // Recommendations
    $recommendations = $last_saved_type == 'fcn_recommendation' ? null : get_transient( 'fictioneer_sitemap_recommendations' );

    if ( ! $recommendations ) {
      $recommendations = get_posts(
        array(
          'post_type' => 'fcn_recommendation',
          'post_status' => 'publish',
          'orderby' => 'date',
          'order' => 'DESC',
          'numberposts' => '1000',
          'update_post_meta_cache' => false,
          'update_post_term_cache' => false,
          'no_found_rows' => true
        )
      );

      set_transient( 'fictioneer_sitemap_recommendations', $recommendations );
    }

    foreach( $recommendations as $post ) {
      $lastmod = get_the_modified_date( 'c', $post->ID );
      $sitemap .= fictioneer_url_node( get_permalink( $post->ID ), $lastmod, 'yearly' );
    }

    // End
    $sitemap .= "\n" . '</urlset>';

    // Save
    $file_path = fopen( ABSPATH . '/sitemap.xml', 'w' );
    fwrite( $file_path, $sitemap );
    fclose( $file_path );
  }
}

if ( get_option( 'fictioneer_enable_sitemap' ) && ! fictioneer_seo_plugin_active() ) {
  add_action( 'save_post', 'fictioneer_create_sitemap', 99, 2 );
}

?>
