<?php

// =============================================================================
// SITEMAP REWRITE RULE & REDIRECT
// =============================================================================

/**
 * Add rewrite rule for custom theme sitemap.
 *
 * @since 5.8.7
 * @since 5.31.1 - Refactored for paginated sitemap.
 */

function fictioneer_add_sitemap_rewrite_rule() {
  add_rewrite_rule( '^sitemap\.xml$', 'index.php?fictioneer_sitemap=index', 'top' );
  add_rewrite_rule( '^sitemap-([0-9]+)\.xml$', 'index.php?fictioneer_sitemap_page=$matches[1]', 'top' );

  add_rewrite_tag( '%fictioneer_sitemap%', '([^&]+)' );
  add_rewrite_tag( '%fictioneer_sitemap_page%', '([0-9]+)' );
}

if ( get_option( 'fictioneer_enable_sitemap' ) && ! fictioneer_seo_plugin_active() ) {
  add_action( 'init', 'fictioneer_add_sitemap_rewrite_rule' );
}

/**
 * Serve sitemap index or page.
 *
 * @since 5.31.1
 */

function fictioneer_serve_paginated_sitemap() {
  if ( get_query_var( 'fictioneer_sitemap' ) === 'index' ) {
    fictioneer_serve_sitemap_index();
    exit;
  }

  $page = intval( get_query_var( 'fictioneer_sitemap_page' ) );
  $total = fictioneer_get_sitemap_total_pages();

  if ( $page > 0 && $page <= $total ) {
    fictioneer_serve_sitemap_page( $page );
    exit;
  }
}

if ( get_option( 'fictioneer_enable_sitemap' ) && ! fictioneer_seo_plugin_active() ) {
  add_action( 'template_redirect', 'fictioneer_serve_paginated_sitemap' );
}

// =============================================================================
// HELPERS
// =============================================================================

/**
 * Get <loc> tag helper.
 *
 * @since 4.0.0
 *
 * @param string $content  Content for the tag.
 *
 * @return string Tag with content.
 */

function fictioneer_loc_node( $content ) {
  return '<loc>' . esc_xml( $content ) . '</loc>';
}

/**
 * Get <lastmod> tag helper.
 *
 * @since 4.0.0
 *
 * @param string $content  Content for the tag.
 *
 * @return string Tag with content.
 */

function fictioneer_lastmod_node( $content ) {
  return '<lastmod>' . esc_xml( $content ) . '</lastmod>';
}

/**
 * Get <changefreq> tag helper.
 *
 * @since 4.0.0
 *
 * @param string $content  Content for the tag.
 *
 * @return string Tag with content.
 */

function fictioneer_frequency_node( $content ) {
  return '<changefreq>' . esc_xml( $content ) . '</changefreq>';
}

/**
 * Get <url> node helper.
 *
 * @since 4.0.0
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
 * Return number of sitemap pages.
 *
 * @since 5.31.1
 *
 * @global wpdb $wpdb  WordPress database abstraction object.
 *
 * @return int Number of sitemap pages.
 */

function fictioneer_get_sitemap_total_pages() {
  global $wpdb;

  static $pages = null;

  if ( $pages ) {
    return $pages;
  }

  $home = (int) get_option( 'page_on_front' );
  $blog = (int) get_option( 'page_for_posts' );

  $sql = "
    SELECT COUNT(1)
    FROM {$wpdb->posts} p
    LEFT JOIN {$wpdb->postmeta} m1
      ON m1.post_id = p.ID AND m1.meta_key = 'fictioneer_chapter_hidden'
    LEFT JOIN {$wpdb->postmeta} m2
      ON m2.post_id = p.ID AND m2.meta_key = 'fictioneer_story_hidden'
    WHERE p.post_status = 'publish'
      AND p.post_type IN ( 'page', 'post', 'fcn_collection', 'fcn_story', 'fcn_chapter', 'fcn_recommendation' )
      AND (
        p.post_type != 'fcn_chapter'
        OR COALESCE(m1.meta_value, '') NOT IN ('1', 'true', 'yes')
      )
      AND (
        p.post_type != 'fcn_story'
        OR COALESCE(m2.meta_value, '') NOT IN ('1', 'true', 'yes')
      )
  ";

  $count = (int) $wpdb->get_var( $sql );

  if ( $home ) {
    $home_exists = $wpdb->get_var(
      $wpdb->prepare(
        "SELECT 1 FROM {$wpdb->posts} WHERE ID = %d AND post_status = 'publish' LIMIT 1",
        $home
      )
    );

    if ( $home_exists ) {
      $count--;
    }
  }

  if ( $blog && $blog !== $home ) {
    $blog_exists = $wpdb->get_var(
      $wpdb->prepare(
        "SELECT 1 FROM {$wpdb->posts} WHERE ID = %d AND post_status = 'publish' LIMIT 1",
        $blog
      )
    );

    if ( $blog_exists ) {
      $count--;
    }
  }

  $pages = (int) ceil( $count / FICTIONEER_SITEMAP_ENTRIES_PER_PAGE );

  return $pages;
}

// =============================================================================
// SITEMAP INDEX
// =============================================================================

/**
 * Serve the virtual sitemap index XML.
 *
 * @since 5.31.1
 */

function fictioneer_serve_sitemap_index() {
  header( 'Content-Type: application/xml; charset=UTF-8' );

  // Render Transient?
  $transient = get_transient( 'fictioneer_sitemap_index' );

  if ( ! WP_DEBUG && $transient ) {
    echo $transient;
    return;
  }

  // Setup
  $total = fictioneer_get_sitemap_total_pages();
  $base_url = home_url( '/' );
  $output = '';

  // Build
  $output .= '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
  $output .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

  for ( $i = 1; $i <= $total; $i++ ) {
    $loc = esc_xml( esc_url( "{$base_url}sitemap-{$i}.xml" ) );
    $lastmod = esc_xml( current_time( 'c', true ) );

    $output .= "\t<sitemap>\n";
    $output .= "\t\t<loc>$loc</loc>\n";
    $output .= "\t\t<lastmod>$lastmod</lastmod>\n";
    $output .= "\t</sitemap>\n";
  }

  $output .= '</sitemapindex>';

  // Cache as Transient
  set_transient( 'fictioneer_sitemap_index', $output, 4 * HOUR_IN_SECONDS );
  set_transient( 'fictioneer_sitemaps_timestamp', time() );

  // Render
  echo $output;
}

// =============================================================================
// PAGINATED SITEMAP
// =============================================================================

/**
 * Serve paginated sitemap.
 *
 * @since 5.31.1
 *
 * @param int $page  Current page.
 */

function fictioneer_serve_sitemap_page( $page ) {
  // Setup
  $offset = ( $page - 1 ) * FICTIONEER_SITEMAP_ENTRIES_PER_PAGE;
  $timestamp = get_transient( 'fictioneer_sitemaps_timestamp' ) ?: time();
  $transient_key = 'fictioneer_sitemap_' . FICTIONEER_SITEMAP_ENTRIES_PER_PAGE . "_{$page}";

  // Header
  header( 'Content-Type: application/xml; charset=UTF-8' );

  // Render Transient?
  $transient = get_transient( $transient_key );

  if ( ! WP_DEBUG && $transient && $transient['timestamp'] === $timestamp ) {
    echo $transient['content'];
    return;
  }

  // Get date and time
  $now = current_time( 'c', true );

  // Compile sitemap
  $output = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
  $output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

  // Static pages
  if ( $page === 1 ) {
    $home = get_option( 'page_on_front' );
    $blog = get_option( 'page_for_posts' );

    $output .= fictioneer_url_node( esc_url( home_url( '/' ) ), $now, 'daily' );

    if ( $blog && $blog !== $home ) {
      $output .= fictioneer_url_node( get_permalink( $blog ), $now, 'daily' );
    }
  }

  // Content
  $entries = fictioneer_get_sitemap_entries( FICTIONEER_SITEMAP_ENTRIES_PER_PAGE, $offset );

  foreach ( $entries as $entry ) {
    $output .= fictioneer_url_node( $entry['loc'], $entry['lastmod'], $entry['freq'] );
  }

  $output .= '</urlset>';

  // Cache as Transient
  set_transient(
    $transient_key,
    array(
      'content' => $output,
      'timestamp' => $timestamp
    ),
    4 * HOUR_IN_SECONDS - 2
  );

  // Render
  echo $output;
}

/**
 * Get sitemap entries.
 *
 * @since 5.31.1
 *
 * @global wpdb $wpdb  WordPress database abstraction object.
 *
 * @param int $limit   Value for 'posts_per_page' argument.
 * @param int $offset  Offset to account for the pagination.
 *
 * @return array Collection of node data ('loc', 'lastmod', 'freq').
 */

function fictioneer_get_sitemap_entries( $limit, $offset ) {
  global $wpdb;

  // Setup
  $home = (int) get_option( 'page_on_front' );
  $blog = (int) get_option( 'page_for_posts' );
  $entries = [];
  $template_excludes = apply_filters(
    'fictioneer_filter_sitemap_page_template_excludes',
    array(
      'user-profile.php',
      'singular-bookmarks.php',
      'singular-bookshelf.php',
      'singular-bookshelf-ajax.php'
    )
  );

  // Query
  $sql = "
    SELECT
      p.ID,
      p.post_type,
      p.post_modified_gmt,
      m3.meta_value AS story_status
    FROM {$wpdb->posts} p
    LEFT JOIN {$wpdb->postmeta} m1 ON m1.post_id = p.ID AND m1.meta_key = 'fictioneer_chapter_hidden'
    LEFT JOIN {$wpdb->postmeta} m2 ON m2.post_id = p.ID AND m2.meta_key = 'fictioneer_story_hidden'
    LEFT JOIN {$wpdb->postmeta} m3 ON m3.post_id = p.ID AND m3.meta_key = 'fictioneer_story_status'
    WHERE p.post_status = 'publish'
      AND p.post_type IN ('page', 'post', 'fcn_collection', 'fcn_story', 'fcn_chapter', 'fcn_recommendation')
      AND (
        p.post_type != 'fcn_chapter'
        OR COALESCE(m1.meta_value, '') NOT IN ('1', 'true', 'yes')
      )
      AND (
        p.post_type != 'fcn_story'
        OR COALESCE(m2.meta_value, '') NOT IN ('1', 'true', 'yes')
      )
    ORDER BY p.post_date DESC
    LIMIT %d OFFSET %d
  ";

  $results = $wpdb->get_results( $wpdb->prepare( $sql, $limit, $offset ) );

  // Collect data
  foreach ( $results as $row ) {
    if ( $row->ID === $home || $row->ID === $blog ) {
      continue;
    }

    if ( $row->post_type === 'page' ) {
      $slug = get_page_template_slug( $row->ID );

      if ( in_array( $slug, $template_excludes ) ) {
        continue;
      }
    }

    $entries[] = array(
      'loc' => get_permalink( $row->ID ),
      'lastmod' => gmdate( 'c', strtotime( $row->post_modified_gmt ) ),
      'freq' => match ( $row->post_type ) {
        'fcn_story' => ( $row->story_status === 'Ongoing' ? 'weekly' : 'monthly' ),
        'post' => 'never',
        'page' => 'monthly',
        default => 'monthly'
      }
    );
  }

  // Result
  return $entries;
}
