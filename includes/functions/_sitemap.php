<?php

// =============================================================================
// SITEMAP REWRITE RULE & REDIRECT
// =============================================================================

/**
 * Add rewrite rule for custom theme sitemap
 *
 * @since 5.8.7
 */

function fictioneer_add_sitemap_rewrite_rule() {
  add_rewrite_rule( '^sitemap\.xml$', 'index.php?fictioneer_sitemap=1', 'top' );
}

if ( get_option( 'fictioneer_enable_sitemap' ) && ! fictioneer_seo_plugin_active() ) {
  add_action( 'init', 'fictioneer_add_sitemap_rewrite_rule' );
}

/**
 * Serve the custom theme sitemap.xml
 *
 * @since 5.8.7
 */

function fictioneer_serve_sitemap() {
  // Check whether this is the sitemap route
  if ( is_null( get_query_var( 'fictioneer_sitemap', null ) ) ) {
    return;
  }

  // Setup
  $sitemap_file = ABSPATH . '/fictioneer_sitemap.xml';

  // Sitemap missing or older than 24 hours?
  if ( ! file_exists( $sitemap_file ) || ( time() - filemtime( $sitemap_file ) ) > DAY_IN_SECONDS ) {
    fictioneer_create_sitemap();
  }

  // Serve the sitemap file
  header( 'Content-Type: application/xml' );
  readfile( $sitemap_file );
  exit;
}
add_action( 'template_redirect', 'fictioneer_serve_sitemap' );

// =============================================================================
// (RE-)BUILD SITEMAP
// =============================================================================

/**
 * Get <loc> tag helper
 *
 * @since 4.0.0
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
 * @since 4.0.0
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
 * @since 4.0.0
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
 * @since 4.0.0
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
 * @since 4.0.0
 * @since 5.8.7 - Create on demand, not on post save.
 */

function fictioneer_create_sitemap() {
  // Open
  $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
  $sitemap .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

  // Blog and front page
  $sitemap .= fictioneer_url_node( esc_url( home_url( '/' ) ), current_time( 'c' ), 'daily' );
  $sitemap .= fictioneer_url_node( get_permalink( get_option( 'page_for_posts' ) ), current_time( 'c' ), 'daily' );

  // Pages
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

  foreach( $pages as $post ) {
    $template = get_page_template_slug( $post->ID );
    $template_excludes = ['user-profile.php', 'singular-bookmarks.php', 'singular-bookshelf.php', 'singular-bookshelf-ajax.php'];
    $template_excludes = apply_filters( 'fictioneer_filter_sitemap_page_template_excludes', $template_excludes );

    if ( in_array( $template, $template_excludes ) ) {
      continue;
    }

    $lastmod = get_the_modified_date( 'c', $post->ID );
    $sitemap .= fictioneer_url_node( get_permalink( $post->ID ), $lastmod, 'monthly' );
  }

  // Blogs
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

  foreach( $blogs as $post ) {
    $lastmod = get_the_modified_date( 'c', $post->ID );
    $sitemap .= fictioneer_url_node( get_permalink( $post->ID ), $lastmod, 'never' );
  }

  // Collections
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

  foreach( $collections as $post ) {
    $lastmod = get_the_modified_date( 'c', $post->ID );
    $sitemap .= fictioneer_url_node( get_permalink( $post->ID ), $lastmod, 'monthly' );
  }

  // Stories
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

  foreach( $chapters as $post ) {
    if ( get_post_meta( $post->ID, 'fictioneer_chapter_hidden', true ) ) {
      continue;
    }

    $lastmod = get_the_modified_date( 'c', $post->ID );
    $sitemap .= fictioneer_url_node( get_permalink( $post->ID ), $lastmod, 'monthly' );
  }

  // Recommendations
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

  foreach( $recommendations as $post ) {
    $lastmod = get_the_modified_date( 'c', $post->ID );
    $sitemap .= fictioneer_url_node( get_permalink( $post->ID ), $lastmod, 'monthly' );
  }

  // End
  $sitemap .= "\n" . '</urlset>';

  // Save
  $file_path = fopen( ABSPATH . '/fictioneer_sitemap.xml', 'w' );
  fwrite( $file_path, $sitemap );
  fclose( $file_path );
}

?>
