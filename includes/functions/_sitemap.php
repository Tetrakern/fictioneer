<?php

// =============================================================================
// (RE-) BUILD SITEMAP WHEN POST/PAGE IS SAVED
// =============================================================================

/**
 * Get <loc> tag helper
 *
 * @since Fictioneer 4.0
 *
 * @param string $content Content for the tag.
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
 * @param string $content Content for the tag.
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
 * @param string $content Content for the tag.
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
 * @see   fictioneer_loc_node()
 * @see   fictioneer_lastmod_node()
 * @see   fictioneer_frequency_node()
 *
 * @param string $loc     URL.
 * @param string $lastmod Optional. Last modified timestamp.
 * @param string $freq    Optional. Refresh frequency, e.g. 'daily' or 'yearly'.
 *
 * @return string $node Node with tags.
 */

function fictioneer_url_node( $loc, $lastmod = null, $freq = null ) {
  $node = "\t" . '<url>' . "\n\t\t" . fictioneer_loc_node( $loc ) . "\n";
  $node = $lastmod ? $node . "\t\t" . fictioneer_lastmod_node( $lastmod ) . "\n" : $node;
  $node = $freq ? $node . "\t\t" . fictioneer_frequency_node( $freq ) . "\n" : $node;
  $node .= "\t" . '</url>' . "\n";

  return $node;
}

if ( ! function_exists( 'fictioneer_create_sitemap' ) ) {
  function fictioneer_create_sitemap( $last_saved_id ) {
    // Prevent multi-fire
    if ( fictioneer_multi_save_guard( $last_saved_id ) ) {
      return;
    }

    // Only rebuild necessary part of the sitemap
    $last_saved_type = get_post_type( $last_saved_id );

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
      $pages = $last_saved_type == 'page' ? false : get_transient( 'fictioneer_sitemap_pages' );

      if ( ! $pages ) {
        $pages = get_posts( array(
          'post_type' => 'page',
          'post_status' => array( 'publish' ),
          'orderby' => 'date',
          'order' => 'DESC',
          'numberposts' => '1000',
          'post__not_in' => [get_option( 'page_on_front' ), get_option( 'page_for_posts' )]
        ));
        set_transient( 'fictioneer_sitemap_pages', $pages );
      }

      foreach( $pages as $post ) {
        $template = get_page_template_slug( $post->ID );
        if ( in_array( $template, ['user-profile.php', 'single-bookmarks.php'] ) ) continue;
        $frequency = 'yearly';
        $lastmod = get_the_modified_date( 'c', $post->ID );
        $sitemap .= fictioneer_url_node( get_permalink( $post->ID ), $lastmod, 'yearly' );
      }

      // Blogposts
      $blogposts = $last_saved_type == 'post' ? false : get_transient( 'fictioneer_sitemap_posts' );

      if ( ! $blogposts ) {
        $blogposts = get_posts( array(
          'post_type' => 'post',
          'post_status' => array( 'publish' ),
          'orderby' => 'date',
          'order' => 'DESC',
          'numberposts' => '1000'
        ));
        set_transient( 'fictioneer_sitemap_posts', $blogposts );
      }

      foreach( $blogposts as $post ) {
        $lastmod = get_the_modified_date( 'c', $post->ID );
        $sitemap .= fictioneer_url_node( get_permalink( $post->ID ), $lastmod, 'never' );
      }

      // Collections
      $collections = $last_saved_type == 'fcn_collection' ? false : get_transient( 'fictioneer_sitemap_collections' );

      if ( ! $collections ) {
        $collections = get_posts( array(
          'post_type' => 'fcn_collection',
          'post_status' => array( 'publish' ),
          'orderby' => 'date',
          'order' => 'DESC',
          'numberposts' => '1000'
        ));
        set_transient( 'fictioneer_sitemap_collections', $collections );
      }

      foreach( $collections as $post ) {
        $lastmod = get_the_modified_date( 'c', $post->ID );
        $sitemap .= fictioneer_url_node( get_permalink( $post->ID ), $lastmod, 'monthly' );
      }

      // Stories
      $stories = $last_saved_type == 'fcn_story' ? false : get_transient( 'fictioneer_sitemap_stories' );

      if ( ! $stories ) {
        $stories = get_posts(
          array(
            'post_type' => 'fcn_story',
            'post_status' => array( 'publish' ),
            'orderby' => 'date',
            'order' => 'DESC',
            'numberposts' => '2000',
            'meta_query' => array(
              'relation' => 'OR',
              array(
                'key' => 'fictioneer_story_hidden',
                'value' => '0'
              ),
              array(
                'key' => 'fictioneer_story_hidden',
                'compare' => 'NOT EXISTS'
              )
            )
          )
        );
        set_transient( 'fictioneer_sitemap_stories', $stories );
      }

      foreach ( $stories as $post ) {
        $lastmod = get_the_modified_date( 'c', $post->ID );
        $status = fictioneer_get_field( 'fictioneer_story_status', $post->ID );
        $frequency = $status == 'Ongoing' ? 'weekly' : 'monthly';
        $sitemap .= fictioneer_url_node( get_permalink( $post->ID ), $lastmod, $frequency );
      }

      // Chapters
      $chapters = $last_saved_type == 'fcn_chapter' ? false : get_transient( 'fictioneer_sitemap_chapters' );

      if ( ! $chapters ) {
        $chapters = get_posts( array(
          'post_type' => 'fcn_chapter',
          'post_status' => array( 'publish' ),
          'orderby' => 'date',
          'order' => 'DESC',
          'numberposts' => '10000',
          'meta_query' => array(
              'relation' => 'OR',
              array(
                'key' => 'fictioneer_chapter_hidden',
                'value' => '0'
              ),
              array(
                'key' => 'fictioneer_chapter_hidden',
                'compare' => 'NOT EXISTS'
              )
            )
        ));
        set_transient( 'fictioneer_sitemap_chapters', $chapters );
      }

      foreach( $chapters as $post ) {
        $lastmod = get_the_modified_date( 'c', $post->ID );
        $sitemap .= fictioneer_url_node( get_permalink( $post->ID ), $lastmod, 'monthly' );
      }

      // Recommendations
      $recommendations = $last_saved_type == 'fcn_recommendation' ? false : get_transient( 'fictioneer_sitemap_recommendations' );

      if ( ! $recommendations ) {
        $recommendations = get_posts( array(
          'post_type' => 'fcn_recommendation',
          'post_status' => array( 'publish' ),
          'orderby' => 'date',
          'order' => 'DESC',
          'numberposts' => '1000'
        ));
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
}

if ( get_option( 'fictioneer_enable_sitemap' ) && ! fictioneer_seo_plugin_active() ) {
  add_action( 'save_post', 'fictioneer_create_sitemap' );
}

?>
