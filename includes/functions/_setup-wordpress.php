<?php

// =============================================================================
// MAINTENANCE MODE
// =============================================================================

/**
 * Toggle maintenance mode from settings with message
 *
 * @since 5.0.0
 * @since 5.12.0 - Exclude Customizer preview.
 */

function fictioneer_maintenance_mode() {
  if ( get_option( 'fictioneer_enable_maintenance_mode' ) && ! is_customize_preview() ) {
    if ( ! current_user_can( 'edit_themes' ) || ! is_user_logged_in() ) {
      $note = get_option( 'fictioneer_phrase_maintenance' );
      $note = ! empty( $note ) ? $note : __( 'Website under planned maintenance. Please check back later.', 'fictioneer' );

      wp_die( __( '<h1>Under Maintenance</h1><br>', 'fictioneer' ) . $note );
    }
  }
}
add_action( 'get_header', 'fictioneer_maintenance_mode' );

// =============================================================================
// SECURITY & CLEANUP
// =============================================================================

/**
 * Remove clutter and don't provide information to potential attackers
 */

if ( get_option( 'fictioneer_remove_head_clutter' ) ) {
  remove_action( 'wp_head', 'rsd_link' );
  remove_action( 'wp_head', 'wlwmanifest_link' );
  remove_action( 'wp_head', 'wp_generator' );
}

// Nobody needs that (or just override this filter in a child theme)
if ( ! get_option( 'fictioneer_enable_xmlrpc' ) ) {
  add_filter( 'xmlrpc_enabled', '__return_false' );
}

// =============================================================================
// SITEMAPS
// =============================================================================

/**
 * Remove default sitemaps if custom sitemaps are enabled
 */

if ( get_option( 'fictioneer_enable_sitemap' ) && ! fictioneer_seo_plugin_active() ) {
  add_filter( 'wp_sitemaps_enabled', '__return_false' );
}

// =============================================================================
// CUSTOMIZE EXCERPTS
// =============================================================================

/**
 * Change excerpt length
 *
 * @since 4.0.0
 *
 * @param int $length The current excerpt length in words.
 */

function fictioneer_custom_excerpt_length( $length ) {
  return 64;
}
add_filter( 'excerpt_length', 'fictioneer_custom_excerpt_length' );

/**
 * Replace excerpt ellipsis
 *
 * @since 5.2.5
 *
 * @return string The ellipsis (…).
 */

function fictioneer_excerpt_ellipsis() {
  return '…';
}
add_filter( 'excerpt_more', 'fictioneer_excerpt_ellipsis' );

// =============================================================================
// CUSTOMIZE ADMIN BAR
// =============================================================================

/**
 * Reduce admin bar based on setting
 *
 * @since 5.0.0
 */

function fictioneer_remove_admin_bar_links() {
  global $wp_admin_bar;

  $wp_admin_bar->remove_menu( 'wp-logo' );
  $wp_admin_bar->remove_menu( 'about' );
  $wp_admin_bar->remove_menu( 'wporg' );
  $wp_admin_bar->remove_menu( 'documentation' );
  $wp_admin_bar->remove_menu( 'support-forums' );
  $wp_admin_bar->remove_menu( 'feedback' );
  $wp_admin_bar->remove_menu( 'view-site' );
  $wp_admin_bar->remove_menu( 'search' );
  $wp_admin_bar->remove_menu( 'customize' );
}

if ( get_option( 'fictioneer_reduce_admin_bar' ) ) {
  add_action( 'wp_before_admin_bar_render', 'fictioneer_remove_admin_bar_links' );
}

// =============================================================================
// LOGOUT REDIRECT
// =============================================================================

/**
 * Change redirect after logout
 *
 * @since 4.0.0
 *
 * @param string $logout_url  The HTML-encoded logout URL.
 * @param string $redirect    Path to redirect to on logout.
 *
 * @return string The updated logout URL.
 */

function fictioneer_logout_redirect( $logout_url, $redirect ) {
  // Setup
  $args = [];

  // Add redirect to args
  if ( empty( $redirect ) ) {
    $args['redirect_to'] = urlencode( get_permalink() );
  } else {
    $args['redirect_to'] = urlencode( $redirect );
  }

  // Avoid login page
  if ( is_admin() || empty( $redirect ) ) {
    $args['redirect_to'] = urlencode( get_home_url() );
  }

  // Rebuild logout URL
  $logout_url = add_query_arg( $args, site_url( 'wp-login.php?action=logout', 'login' ) );
  $logout_url = wp_nonce_url( $logout_url, 'log-out' );

  // Return updated logout URL
  return $logout_url;
}

if ( get_option( 'fictioneer_logout_redirects_home' ) ) {
  add_filter( 'logout_url', 'fictioneer_logout_redirect', 10, 2 );
}

// =============================================================================
// CUSTOM LOGOUT
// =============================================================================

/**
 * Add route to logout script
 *
 * @since 5.0.0
 */

function fictioneer_add_logout_endpoint() {
  add_rewrite_endpoint( FICTIONEER_LOGOUT_ENDPOINT, EP_ROOT );
}

if ( FICTIONEER_LOGOUT_ENDPOINT && ! get_option( 'fictioneer_disable_theme_logout' ) ) {
  add_action( 'init', 'fictioneer_add_logout_endpoint', 10 );
}

/**
 * Logout without _wpnonce and no login screen
 *
 * @since 5.0.0
 */

function fictioneer_logout() {
  global $wp;

  // Abort if not on logout route
  if ( $wp->request != FICTIONEER_LOGOUT_ENDPOINT ) {
    return;
  }

  // Redirect home if not logged-in
  if ( ! is_user_logged_in() ) {
    wp_safe_redirect( get_home_url() );
  }

  // Setup
  $user = wp_get_current_user();

  // Default WP logout
  wp_logout();

  // Redirect URL
  if ( empty( $_REQUEST['redirect_to'] ) ) {
    $redirect_to = get_home_url();
  } else {
    $redirect_to = $_REQUEST['redirect_to'];
  }

  // Apply default filters
  $redirect_to = apply_filters( 'logout_redirect', $redirect_to, $redirect_to, $user );

  // Redirect
  wp_safe_redirect( $redirect_to );
  exit;
}

if ( FICTIONEER_LOGOUT_ENDPOINT && ! get_option( 'fictioneer_disable_theme_logout' ) ) {
  add_action( 'template_redirect', 'fictioneer_logout' );
}

if ( ! function_exists( 'fictioneer_get_logout_url' ) ) {
  /**
   * Fictioneer logout URL with optional redirect
   *
   * @since 5.0.0
   *
   * @param string $redirect  URL to redirect to after logout.
   */

  function fictioneer_get_logout_url( $redirect = null ) {
    // Return default logout URL if endpoint is disabled
    if ( ! FICTIONEER_LOGOUT_ENDPOINT || get_option( 'fictioneer_disable_theme_logout' ) ) {
      return wp_logout_url( $redirect );
    }

    // Setup
    $redirect = $redirect ?? get_permalink();

    // Return logout link
    if ( empty( $redirect ) ) {
      return get_home_url( null, FICTIONEER_LOGOUT_ENDPOINT );
    } else {
      return add_query_arg(
        'redirect_to',
        urlencode( $redirect ),
        get_home_url( null, FICTIONEER_LOGOUT_ENDPOINT )
      );
    }
  }
}

// =============================================================================
// AFTER LOGOUT CLEANUP
// =============================================================================

/**
 * Make sure local storage is cleared on logout
 *
 * @since 5.0.0
 * @since 5.26.1 - Use wp_print_inline_script_tag().
 */

function fictioneer_after_logout_cleanup() {
  wp_print_inline_script_tag(
    'localStorage.removeItem("fcnUserData"); localStorage.removeItem("fcnBookshelfContent"); localStorage.removeItem("fcnChapterBookmarks");',
    array(
      'id' => 'fictioneer-logout-cleanup',
      'type' => 'text/javascript',
      'data-jetpack-boost' => 'ignore',
      'data-no-optimize' => '1',
      'data-no-defer' => '1',
      'data-no-minify' => '1',
    )
  );
}
add_action( 'login_form', 'fictioneer_after_logout_cleanup' );

// =============================================================================
// SHOW CUSTOM POST TYPES ON TAG/CATEGORY ARCHIVES
// =============================================================================

/**
 * Show custom post types in tag and category archives
 *
 * @since 4.0.0
 * @link https://wordpress.stackexchange.com/a/28147/223620
 *
 * @param WP_Query $query  The query.
 */

function fictioneer_extend_taxonomy_pages( $query ) {
  // Abort if wrong query
  if (
    ! $query->is_main_query() ||
    is_admin() ||
    ! (
      is_tag() ||
      is_category() ||
      is_tax( ['fcn_genre', 'fcn_fandom', 'fcn_character', 'fcn_content_warning'] )
    )
  ) {
    return;
  }

  // Add all post types to taxonomy page query
  $query->set( 'post_type', ['post', 'fcn_story', 'fcn_chapter', 'fcn_recommendation', 'fcn_collection'] );
}
add_action( 'pre_get_posts', 'fictioneer_extend_taxonomy_pages' );

// =============================================================================
// TAX CLOUDS
// =============================================================================

/**
 * Re-queries the term counts for the tax cloud
 *
 * @since 5.26.1
 * @link https://developer.wordpress.org/reference/hooks/get_terms/
 *
 * @param array      $terms       Array of found terms.
 * @param array|null $taxonomies  An array of taxonomies if known.
 *
 * @return array Array of found terms with modified counts.
 */

function fictioneer_exclude_non_stories_from_cloud_counts( $terms, $taxonomies, $args ) {
  if (
    ! ( is_tax() || is_tag() || is_category() ) ||
    is_admin() ||
    empty( $terms ) ||
    empty( $taxonomies ) ||
    ( $args['fictioneer_query_name'] ?? 0 ) !== 'tag_cloud'
  ) {
    return $terms;
  }

  global $wpdb;

  $supported_taxonomies = ['fcn_genre', 'fcn_fandom', 'fcn_character', 'fcn_content_warning', 'post_tag', 'category'];
  $matched_taxonomies = array_intersect( $supported_taxonomies, $taxonomies );

  if ( ! empty( $matched_taxonomies ) ) {
    foreach ( $terms as &$term ) {
      $term_ids = [];

      if ( $args['pad_counts'] ?? 0 ) {
        $term_ids = get_term_children( $term->term_id, $term->taxonomy );
      }

      $term_ids[] = $term->term_id;

      $count = $wpdb->get_var(
        $wpdb->prepare(
          "SELECT COUNT(*)
            FROM {$wpdb->term_relationships} AS tr
            INNER JOIN {$wpdb->posts} AS p ON p.ID = tr.object_id
            WHERE p.post_status = 'publish'
            AND p.post_type = 'fcn_story'
            AND tr.term_taxonomy_id IN (" . implode( ',', array_fill( 0, count( $term_ids ), '%d' ) ) . ")",
          $term_ids
        )
      );

      $term->count = $count;
    }
  }

  return $terms;
}

if ( get_option( 'fictioneer_exclude_non_stories_from_cloud_counts' ) ) {
  add_filter( 'get_terms', 'fictioneer_exclude_non_stories_from_cloud_counts', 10, 3 );
}

// =============================================================================
// MODIFY RSS FEEDS
// =============================================================================

/**
 * Get template for story feed (chapters)
 *
 * @since 4.0.0
 */

function fictioneer_story_rss_template() {
  get_template_part( 'rss', 'rss-story' );
}

/**
 * Add feed for story (chapters)
 *
 * @since 4.0.0
 */

function fictioneer_story_rss() {
  add_feed( 'rss-chapters', 'fictioneer_story_rss_template' );
}

/**
 * Add custom main feed
 *
 * @since 4.0.0
 */

function fictioneer_main_rss_template() {
  get_template_part( 'rss', 'rss-main' );
}

if ( get_option( 'fictioneer_enable_theme_rss' ) ) {
  // Remove default RSS feeds
  add_filter( 'post_comments_feed_link', '__return_empty_string' );
  add_filter( 'feed_links_show_comments_feed', '__return_false' );
  remove_all_actions( 'do_feed_rss2' );
  remove_action( 'wp_head', 'feed_links_extra', 3 );
  remove_action( 'wp_head', 'feed_links', 2 );

  // Add chapter RSS feed
  add_action( 'init', 'fictioneer_story_rss' );

  // Add new main feed
  add_action( 'do_feed_rss2', 'fictioneer_main_rss_template' );
} else {
  // Default feed links
  add_theme_support( 'automatic-feed-links' );
}

/**
 * Ensures RSS excerpts are valid in XML
 *
 * @since 5.21.1
 *
 * @param string $excerpt  The current post excerpt for the RSS feed.
 *
 * @return string The modified excerpt for the RSS feed.
 */

function fictioneer_filter_rss_excerpt( $excerpt ) {
  $excerpt = wp_strip_all_tags( $excerpt );
  $excerpt = esc_html( $excerpt );

  return $excerpt;
}
add_filter( 'the_excerpt_rss', 'fictioneer_filter_rss_excerpt' );

/**
 * Removes protected posts from RSS feeds.
 *
 * @since 5.27.3
 *
 * @param string[] $query_args  Query arguments.
 *
 * @return string[] Updated query arguments.
 */

function fictioneer_exclude_protected_from_rss( $query_args ) {
  $query_args['has_password'] = false;

  return $query_args;
}

if ( get_option( 'fictioneer_exclude_protected_from_rss' ) ) {
  add_filter( 'fictioneer_filter_rss_main_query_args', 'fictioneer_exclude_protected_from_rss' );
  add_filter( 'fictioneer_filter_rss_story_query_args', 'fictioneer_exclude_protected_from_rss' );
}

// =============================================================================
// OUTPUT RSS
// =============================================================================

/**
 * Output RSS feed
 *
 * @since 5.0.0
 *
 * @param int|null $post_id  Optional. The current post ID.
 */

function fictioneer_output_rss( $post_id = null ) {
  // Setup
  $post_id = $post_id ? $post_id : get_queried_object_id(); // In archives, this is the first post
  $post_type = get_post_type(); // In archives, this is the first post
  $rss_link = '';
  $skip_to_default = is_archive() || is_search() || post_password_required( $post_id );

  // RSS Feed if story...
  if ( $post_type == 'fcn_story' && ! $skip_to_default ) {
    $title = sprintf(
      _x( '%1$s - %2$s Chapters', 'Story Feed: [Site] - [Story] Chapters.', 'fictioneer' ),
      get_bloginfo( 'name' ),
      get_the_title( $post_id )
    );

    $url = esc_url( add_query_arg( 'story_id', $post_id, home_url( 'feed/rss-chapters' ) ) );
    $rss_link = '<link rel="alternate" type="application/rss+xml" title="' . $title . '" href="' . $url . '" />';
  }

  // RSS Feed if chapter...
  $story_id = fictioneer_get_chapter_story_id( $post_id );

  if ( $post_type == 'fcn_chapter' && ! empty( $story_id ) && ! $skip_to_default ) {
    $title = sprintf(
      _x( '%1$s - %2$s Chapters', 'Story Feed: [Site] - [Story] Chapters.', 'fictioneer' ),
      get_bloginfo( 'name' ),
      get_the_title( $story_id )
    );

    $url = esc_url( add_query_arg( 'story_id', $story_id, home_url( 'feed/rss-chapters' ) ) );
    $rss_link = '<link rel="alternate" type="application/rss+xml" title="' . $title . '" href="' . $url . '" />';
  }

  // Default RSS Feed
  if ( empty( $rss_link ) ) {
    $title = sprintf(
      _x( '%s Feed', '[Site] Feed', 'fictioneer' ),
      get_bloginfo( 'name' )
    );

    $url = esc_url( home_url( 'feed' ) );
    $rss_link = '<link rel="alternate" type="application/rss+xml" title="' . $title . '" href="' . $url . '" />';
  }

  // Output
  echo $rss_link;
}

if ( get_option( 'fictioneer_enable_theme_rss' ) ) {
  add_action( 'wp_head', 'fictioneer_output_rss' );
}

// =============================================================================
// REMOVE PROTECTED FROM TITLES
// =============================================================================

/**
 * Remove the "Protected" prefix from titles
 *
 * @since 3.0
 */

function fictioneer_remove_protected_text() {
  return '%s';
}
add_filter( 'protected_title_format', 'fictioneer_remove_protected_text' );

// =============================================================================
// ADD WRAPPER TO DOWNLOAD BLOCK
// =============================================================================

/**
 * Add wrapper to download block
 *
 * @since 4.0.0
 *
 * @param  string $block_content  The block content.
 * @param  array  $block          The full block, including name and attributes.
 *
 * @return string The updated block content.
 */

function fictioneer_download_block_wrapper( $block_content, $block ) {
  if ( 'core/file' === $block['blockName'] ) {
    $block_content = '<div class="wp-block-file-wrapper">' . $block_content . '</div>';
  }

  return $block_content;
}
add_filter( 'render_block', 'fictioneer_download_block_wrapper', 10, 2 );

// =============================================================================
// PASSWORD FORM (AND FIX REDIRECT)
// =============================================================================

/**
 * Changes password form and fixes redirect error
 *
 * @since 4.0.0
 * @license CC BY-SA 4.0
 * @link https://stackoverflow.com/a/67527400/17140970
 *
 * @global object $post  The global post.
 *
 * @return string The custom password form.
 */

function fictioneer_password_form() {
  global $post;

  // Return empty if...
  if ( get_option( 'fictioneer_hide_password_form_with_patreon' ) ) {
    $patreon_post_data = fictioneer_get_post_patreon_data( $post );

    if ( $patreon_post_data['gated'] ) {
      return '';
    }
  }

  // Setup
  $label = 'pwbox-' . ( empty( $post->ID ) ? rand() : $post->ID . '-' . rand() );

  // Default password form
  $form = '<form class="post-password-form" action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" method="post"><div><i class="fa-solid fa-lock icon-password-form"></i><div class="password-wrapper"><input name="post_password" id="' . $label . '" type="password" required size="20" placeholder="' . esc_attr__( 'Password', 'fictioneer' ) . '"></div><div class="password-submit"><input type="submit" name="Submit" value="' . esc_attr__( 'Unlock', 'fictioneer' ) . '" /><input type="hidden" name="_wp_http_referer" value="' . esc_attr( wp_unslash( $_SERVER['REQUEST_URI'] ) ) . '"></div></div></form>';

  // Continue filter
  return $form;
}
add_filter( 'the_password_form', 'fictioneer_password_form' );

/**
 * Append Patreon unlock info after password form
 *
 * @since 5.15.0
 *
 * @global string $form  The password form HTML.
 *
 * @return string The extended password form HTML.
 */

function fictioneer_unlock_with_patreon( $form ) {
  global $post;

  // Setup
  $patreon_tiers = get_option( 'fictioneer_connection_patreon_tiers' );
  $campaign_link = get_option( 'fictioneer_patreon_campaign_link' );

  // Abort if...
  if (
    ! get_option( 'fictioneer_enable_patreon_locks' ) ||
    ! $campaign_link ||
    ! is_array( $patreon_tiers ) ||
    empty( $patreon_tiers )
  ) {
    return $form;
  }

  // Patreon data
  $patreon_post_data = fictioneer_get_post_patreon_data( $post );
  $patreon_user_data = fictioneer_get_user_patreon_data();

  // Any tiers or amounts set up?
  if ( $patreon_post_data['gated'] ) {
    $options = [];
    $patreon_message = get_option( 'fictioneer_patreon_unlock_message' );
    $auth_reminder = '';

    foreach ( $patreon_post_data['gate_tiers'] as $tier_id ) {
      if ( isset( $patreon_tiers[ $tier_id ] ) ) {
        $title = $patreon_tiers[ $tier_id ]['title'] ?? $tier_id;

        $options[] = $title === 'Free' ? fcntr( 'free_patreon_tier' ) : $title;
      }
    }

    if ( $patreon_post_data['gate_cents'] > 0 ) {
      $dollar = '$' . number_format_i18n( (float) $patreon_post_data['gate_cents'] / 100, 2 ) . '+';

      $options[] = sprintf(
        _x( 'with any %s pledge', 'Unlock with Patreon pledge amount', 'fictioneer' ),
        apply_filters( 'fictioneer_filter_patreon_dollars', $dollar, $patreon_post_data['gate_cents'], $post )
      );
    }

    if ( empty( $options ) ) {
      return $form;
    }

    if ( empty( $patreon_message ) ) {
      if ( count( $options ) < 2 && $patreon_post_data['gate_cents'] > 0 ) {
        $patreon_message = sprintf(
          _x( '… %s.', 'Unlock with Patreon monetary threshold only wrapper.', 'fictioneer' ),
          fictioneer_get_human_readable_list( $options )
        );
      } else {
        $patreon_message = sprintf(
          _x( '… as %s.', 'Unlock with Patreon tier options wrapper.', 'fictioneer' ),
          fictioneer_get_human_readable_list( $options )
        );
      }
    }

    $patreon_message = apply_filters( 'fictioneer_filter_patreon_gate_message', $patreon_message, $options, $post );

    if ( ! empty( $patreon_user_data ) ) {
      $auth_reminder = '<details class="unlock-with-patreon__already-member"><summary>' . _x( 'Already a member?', 'Unlock with Patreon help.',  'fictioneer' ) . '</summary><div>' . _x( 'If you are already a member but still do not see anything, log out and log in with Patreon again to refresh your membership information.', 'Unlock with Patreon help.', 'fictioneer' ) . '</div></details>';
    }

    $form .= '<div class="unlock-with-patreon"><a href="' . esc_url( $campaign_link ) . '" target="_blank" rel="noopener" class="unlock-with-patreon__link"><i class="fa-brands fa-patreon"></i><span>' . __( 'Unlock with Patreon', 'fictioneer' ) . '</span></a><div class="unlock-with-patreon__note">' . $patreon_message . '</div>' . $auth_reminder . '</div>';
  }

  // Continue filter
  return $form;
}
add_filter( 'the_password_form', 'fictioneer_unlock_with_patreon', 20 );

// =============================================================================
// ADD ID TO CONTENT PARAGRAPHS IN CHAPTERS
// =============================================================================

/**
 * Adds incrementing ID to chapter paragraphs
 *
 * @since 3.0
 * @license CC BY-SA 3.0
 * @link https://wordpress.stackexchange.com/a/152169/223620
 *
 * @param string $content  The content.
 *
 * @return string The modified content.
 */

function fictioneer_add_chapter_paragraph_id( $content ) {
  // Return early if...
  if (
    get_post_type() !== 'fcn_chapter' ||
    ! in_the_loop() ||
    ! is_main_query() ||
    post_password_required()
  ) {
    return $content;
  }

  // Counter
  static $paragraph_id = 0;

  // Account for multiple posts and reset the counter
  if ( did_action( 'the_post' ) === 1 ) {
    $paragraph_id = 0;
  }

  // Return modified content
  return preg_replace_callback(
    '/<p\s*/i',
    function () use ( &$paragraph_id ) {
      return "<p id='paragraph-{$paragraph_id}' data-paragraph-id='" . $paragraph_id++ . "'";
    },
    $content
  );
}
add_filter( 'the_content', 'fictioneer_add_chapter_paragraph_id' );

/**
 * Fixes line breaks before paragraphs are added
 *
 * @since 5.25.0
 *
 * @param string $content  The content.
 *
 * @return string The modified content.
 */

function fictioneer_fix_line_breaks( $content ) {
  // Return early if...
  if (
    get_post_type() !== 'fcn_chapter' ||
    ! in_the_loop() ||
    ! is_main_query() ||
    post_password_required()
  ) {
    return $content;
  }

  // Replace single new lines
  $content = str_replace( ["\r\n", "\r"], "\n", $content );
  $content = preg_replace( '/([^\n])\n([^\n])/', "$1\n\n$2", $content );

  return $content;
}

if ( get_option( 'fictioneer_enable_line_break_fix' ) ) {
  add_filter( 'the_content', 'fictioneer_fix_line_breaks', 1 );
}

// =============================================================================
// ADD LIGHTBOX TO POST IMAGES
// =============================================================================

/**
 * Adds lightbox data attributes to post images
 *
 * @since 3.0
 * @license CC BY-SA 3.0
 * @link https://wordpress.stackexchange.com/a/84542/223620
 * @link https://jhtechservices.com/changing-your-image-markup-in-wordpress/
 *
 * @param string $content  The content.
 *
 * @return string The modified content.
 */

function fictioneer_add_lightbox_to_post_images( $content ) {
  // Return early if...
  if ( empty( $content ) || strpos( $content, '<img' ) === false ) {
    return $content;
  }

  // Setup
  libxml_use_internal_errors( true );
  $doc = new DOMDocument();
  $doc->loadHTML( '<?xml encoding="UTF-8">' . $content );
  libxml_clear_errors();
  $images = $doc->getElementsByTagName( 'img' );

  // Iterate over each img tag
  foreach ( $images as $img ) {
    $classes = $img->getAttribute( 'class' );
    $parent = $img->parentNode;
    $parent_classes = $parent->getAttribute( 'class' );
    $parent_href = strtolower( $parent->getAttribute( 'href' ) );
    $all_classes = $classes . ' ' . $parent_classes;

    // Abort if...
    if (
      str_contains( $all_classes, 'no-auto-lightbox' ) ||
      str_contains( $all_classes, 'avatar' ) ||
      $parent->hasAttribute( 'target' )
    ) {
      continue;
    }

    if (
      $parent->hasAttribute( 'href' ) &&
      ! preg_match( '/(?<=\.jpg|jpeg|png|gif|webp|svg|avif|apng|tiff|ico)(?:$|[#?])/', $parent_href )
    ) {
      continue;
    }

    $id = preg_match( '/wp-image-([0-9]+)/i', $classes, $class_id );

    if ( $class_id ) {
      $id = absint( $class_id[1] );
      $img->setAttribute( 'data-attachment-id', $id );
    }

    $img->setAttribute( 'data-lightbox', '' );
    $img->setAttribute( 'tabindex', '0' );
  };

  // Extract and save body content
  $body = $doc->getElementsByTagName( 'body' )->item( 0 );
  $content = $body ? $doc->saveHTML( $body ) : '';
  $content = preg_replace( '/<\/?body>/', '', $content );

  // Release memory
  unset( $doc );

  // Continue filter
  return $content;
}

if ( get_option( 'fictioneer_enable_lightbox' ) ) {
  add_filter( 'the_content', 'fictioneer_add_lightbox_to_post_images', 15 );
}

/**
 * Returns data-lightbox attribute if enabled
 *
 * @since 4.7.0
 *
 * @return string The attribute or an empty string.
 */

function fictioneer_get_lightbox_attribute() {
  return get_option( 'fictioneer_enable_lightbox' ) ? 'data-lightbox' : '';
}

// =============================================================================
// CUSTOM QUERY VARS
// =============================================================================

/**
 * Add custom query vars
 *
 * @since 4.0.0
 * @since 5.8.7 - Appended 'fictioneer_sitemap'.
 *
 * @param array $qvars  Allowed query variable names.
 *
 * @return array Updated allowed query variable names.
 */

function fictioneer_query_vars( $qvars ) {
  $qvars[] = 'sf_paged';
  $qvars[] = 'commentcode';
  $qvars[] = 'cp'; // e.g. custom/current page
  $qvars[] = 'pg'; // e.g. page
  $qvars[] = 'fictioneer_sitemap';

  return $qvars;
}
add_filter( 'query_vars', 'fictioneer_query_vars' );

// =============================================================================
// DEACTIVATE HEARTBEAT OPTION
// =============================================================================

/**
 * Deactivate heartbeat
 *
 * Heartbeat makes continuous AJAX requests to the server, which can impact the
 * performance and may get you in trouble on a shared host. So it may be better
 * to turn it off and accept that there are no autosaves.
 *
 * @since 4.7.0
 */

function fictioneer_disable_heartbeat() {
  wp_deregister_script( 'heartbeat' );
  wp_deregister_script( 'autosave' );
  remove_action( 'admin_enqueue_scripts', 'wp_auth_check_load' );
}

if ( get_option( 'fictioneer_disable_heartbeat' ) ) {
  add_action( 'init', 'fictioneer_disable_heartbeat', 1 );
}

// =============================================================================
// ADD CONSENT WRAPPER TO EMBEDS
// =============================================================================

/**
 * Wraps embeds into a consent box that must be clicked to load the embed.
 *
 * @since 3.0.0
 *
 * @param string $content  The content.
 *
 * @return string The modified content.
 */

function fictioneer_embed_consent_wrappers( $content ) {
  // Return early if...
  if ( empty( $content ) ) {
    return $content;
  }

  if (
    strpos( $content, '<iframe' ) === false &&
    strpos( $content, "class='twitter-timeline'" ) === false &&
    strpos( $content, "class='twitter-tweet'" ) === false
  ) {
    return $content;
  }

  // Setup
  libxml_use_internal_errors( true );
  $dom = new DOMDocument();
  $dom->loadHTML( '<?xml encoding="UTF-8">' . $content );
  libxml_clear_errors();
  $xpath = new DomXPath( $dom );

  $iframes = $dom->getElementsByTagName( 'iframe' );
  $twitter_timelines = $xpath->query( "//a[@class='twitter-timeline']/following-sibling::*[1]" );
  $twitter_tweets = $xpath->query( "//blockquote[@class='twitter-tweet']/following-sibling::*[1]" );

  // Process iframes
  foreach ( $iframes as $iframe ) {
    $src = $iframe->getAttribute( 'src' );
    $title = htmlspecialchars( $iframe->getAttribute( 'title' ) );
    $title = ( ! $title || $title == '') ? 'embedded content' : $title;

    $iframe->removeAttribute( 'src' );

    $consent_element = $dom->createElement( 'button' );
    $consent_element->setAttribute( 'type', 'button' );
    $consent_element->setAttribute( 'class', 'iframe-consent' );
    $consent_element->setAttribute( 'data-src', $src );
    $consent_element->nodeValue = sprintf(
      __( 'Click to load %s with third-party consent.', 'fictioneer' ),
      $title
    );

    $embed_logo = $dom->createElement( 'div' );
    $embed_logo->setAttribute( 'class', 'embed-logo' );

    $iframe->parentNode->insertBefore( $consent_element );
    $iframe->parentNode->insertBefore( $embed_logo );
  }

  // Process Twitter timelines
  foreach ( $twitter_timelines as $twitter ) {
    $src = $twitter->getAttribute( 'src' );

    $twitter->removeAttribute( 'src' );
    $twitter->previousSibling->nodeValue = '';

    $consent_element = $dom->createElement( 'div' );
    $consent_element->setAttribute( 'class', 'twitter-consent' );
    $consent_element->setAttribute( 'data-src', $src );
    $consent_element->nodeValue = sprintf(
      __( 'Click to load %s with third-party consent.', 'fictioneer' ),
      __( 'Twitter', 'fictioneer' )
    );

    $twitter->parentNode->insertBefore( $consent_element );
  }

  // Process Twitter tweets
  foreach ( $twitter_tweets as $twitter ) {
    $src = $twitter->getAttribute( 'src' );

    $twitter->removeAttribute( 'src' );

    $consent_element = $dom->createElement( 'div' );
    $consent_element->setAttribute( 'class', 'twitter-consent' );
    $consent_element->setAttribute( 'data-src', $src );
    $consent_element->nodeValue = sprintf(
      __( 'Click to load %s with third-party consent.', 'fictioneer' ),
      __( 'Twitter', 'fictioneer' )
    );

    $twitter->parentNode->insertBefore( $consent_element );
  }

  // Extract and save body content
  $body = $dom->getElementsByTagName( 'body' )->item( 0 );
  $content = $body ? $dom->saveHTML( $body ) : '';
  $content = preg_replace( '/<\/?body>/', '', $content );

  // Release memory
  unset( $dom );

  // Continue filter
  return $content;
}

if ( get_option( 'fictioneer_consent_wrappers' ) ) {
  add_filter( 'the_content', 'fictioneer_embed_consent_wrappers', 20 );
}

// =============================================================================
// ALLOW TWITTER (X LOL) CONTACT METHOD
// =============================================================================

/**
 * Allow Twitter contact info
 *
 * @since 5.2.4
 */

function fictioneer_user_contact_methods( $methods ) {
  $methods['twitter'] = __( 'Twitter Username', 'fictioneer' );
  return $methods;
}
add_filter( 'user_contactmethods', 'fictioneer_user_contact_methods' );

// =============================================================================
// DISABLE APPLICATION PASSWORDS
// =============================================================================

if ( get_option( 'fictioneer_disable_application_passwords' ) ) {
  add_filter( 'wp_is_application_passwords_available', '__return_false' );
}

// =============================================================================
// DISABLE ATTACHMENT PAGES
// =============================================================================

/**
 * Skip attachment pages and redirect to file
 *
 * @since 5.0.0
 */

function fictioneer_disable_attachment_pages() {
  if ( is_attachment() ) {
    $url = wp_get_attachment_url( get_queried_object_id() );
    wp_redirect( $url, 301 );
  }

  return;
}

if ( ! FICTIONEER_ATTACHMENT_PAGES ) {
  add_action( 'template_redirect', 'fictioneer_disable_attachment_pages' );
}

// =============================================================================
// ALLOWED PROTOCOLS
// =============================================================================

/**
 * Extend list of allowed protocols
 *
 * @since 4.0.0
 *
 * @param array $protocols  Array of allowed protocols.
 *
 * @return array Updated array of allowed protocols.
 */

function fictioneer_extend_allowed_protocols( $protocols ){
  $protocols[] = 's3'; // Amazon S3 bucket
  $protocols[] = 'spotify'; // Load a track, album, artist, search, or playlist in Spotify
  $protocols[] = 'steam'; // Interact with Steam: install apps, purchase games, run games, etc

  return $protocols;
}
add_filter( 'kses_allowed_protocols' , 'fictioneer_extend_allowed_protocols' );

// =============================================================================
// RESTRICT REST API
// =============================================================================

/**
 * Restrict default REST API endpoints
 *
 * @since 5.2.4
 * @link https://developer.wordpress.org/reference/hooks/rest_authentication_errors/
 *
 * @param WP_Error|null|true $errors  WP_Error if authentication error, null
 *                                    if authentication method wasn't used,
 *                                    true if authentication succeeded.
 *
 * @return WP_Error|null|true The filters errors.
 */

function fictioneer_restrict_rest_api( $errors ) {
  if ( $errors === true || is_wp_error( $errors ) ) {
    return $errors;
  }

  $can_edit = current_user_can( 'edit_posts' ) || current_user_can( 'edit_fcn_stories' ) ||
    current_user_can( 'edit_fcn_chapters' ) || current_user_can( 'edit_fcn_collections' ) ||
    current_user_can( 'edit_fcn_recommendations' ) || current_user_can( 'edit_pages' );

  // Restrict default API endpoints to users with 'edit_posts' permission (required for Gutenberg to work)
  if ( preg_match( '/^\/wp-json\/wp\/v2\//', $_SERVER['REQUEST_URI'] ) && ! $can_edit ) {
    return new WP_Error(
      'rest_insufficient_permission',
      'You are not authorized to use the API.',
      array( 'status' => 401 )
    );
  }

  return $errors;
}

if ( get_option( 'fictioneer_restrict_rest_api' ) ) {
  add_filter( 'rest_authentication_errors', 'fictioneer_restrict_rest_api' );
}

// =============================================================================
// ADD SORT, ORDER & FILTER TO TAXONOMY QUERIES
// =============================================================================

/**
 * Modifies the query on archive pages
 *
 * @since 5.4.0
 *
 * @param WP_Query $query  The current WP_Query object.
 */

function fictioneer_add_sof_to_taxonomy_query( $query ) {
  // Abort if...
  if ( is_admin() || ! is_archive() || ! $query->is_main_query() ) {
    return;
  }

  // Use empty array to get date query
  $date_query = fictioneer_append_date_query( [] );

  // If date query set...
  if ( ! empty( $date_query['date_query'] ) ) {
    $query->set( 'date_query', $date_query['date_query'] );
  }

  // Post type?
  $post_type = fictioneer_sanitize_query_var(
    sanitize_key( $_GET['post_type'] ?? '' ),
    ['any', 'post', 'fcn_story', 'fcn_chapter', 'fcn_collection', 'fcn_recommendation']
  );

  // If post type queried...
  if ( ! empty( $post_type ) && $post_type !== 'any' ) {
    $query->set( 'post_type', $post_type );
  }
}
add_action( 'pre_get_posts', 'fictioneer_add_sof_to_taxonomy_query' );

// =============================================================================
// ADD ATTRIBUTES TO MENU ITEMS
// =============================================================================

/**
 * Add target ID as data attribute to menu links
 *
 * @since 5.4.9
 *
 * @param array   $attributes  The HTML attributes applied to the menu item's
 *                             <a> element, empty strings are ignored.
 * @param WP_Post $item        The current menu item object.
 *
 * @return array The modified attributes.
 */

function fictioneer_add_menu_link_attributes( $attributes, $item ) {
  // Assign
  $attributes['data-nav-object-id'] = $item->object_id;

  // Return
  return $attributes;
}
add_filter( 'nav_menu_link_attributes', 'fictioneer_add_menu_link_attributes', 10, 2 );

// =============================================================================
// EXTEND ALLOWED FILE TYPES
// =============================================================================

/**
 * Extend the list of allowed types for file uploads
 *
 * @since 5.6.0
 *
 * @param array $mimes  Key-value pairs of file extensions and their MIME types.
 *
 * @return array Updated MIME types array.
 */

function fictioneer_extend_allowed_upload_types( $mimes ) {
  $mimes['svg'] = 'image/svg+xml';
  $mimes['epub'] = 'application/epub+zip';
  $mimes['avif'] = 'image/avif';

  return $mimes;
}
add_filter( 'upload_mimes', 'fictioneer_extend_allowed_upload_types' );

// =============================================================================
// SEE SOME 3V1L
// =============================================================================

/**
 * Monitors post submissions for potentially malicious content
 *
 * Note: This function only identifies attempts and does not block content submission.
 * Posts are sanitized by WordPress before being saved to the database.
 *
 * @since 5.6.0
 *
 * @param array $data                  An array of slashed post data.
 * @param array $postarr               An array of sanitized, but otherwise unmodified post data.
 * @param array $unsanitized_postarr   An array of unsanitized and unprocessed post data as
 *                                     originally passed to wp_insert_post().
 *
 * @return array Returns the original $data for continued processing.
 */

function fictioneer_see_some_evil( $data, $postarr, $unsanitized_postarr ) {
  // Prevent miss-fire
  if (
    fictioneer_multi_save_guard( $postarr['ID'] ) ||
    empty( $unsanitized_postarr['post_content'] ?? 0 ) ||
    ( $unsanitized_postarr['post_status'] ?? 0 ) === 'auto-draft'
  ) {
    return $data;
  }

  // Setup
  $current_user = wp_get_current_user();
  $admin_email = get_option( 'admin_email' );
  $content = wp_unslash( wp_specialchars_decode( $unsanitized_postarr['post_content'] ?? '' ) );
  $content .= ' ' . wp_unslash( wp_specialchars_decode( $unsanitized_postarr['post_title'] ?? '' ) );
  $post_link = get_permalink( $postarr['ID'] );
  $message = __( '<h3>Potentially Malicious Code Warning</h3><p>There has been a post with suspicious strings in the content or title, please review the results below. This might be an attempted attack or <strong>false positive.</strong> Note that posts are sanitized before being saved to the database and no damage can be caused this way, this is just to let you know that someone <strong>tried.</strong></p>', 'fictioneer' );
  $sender = [];
  $results = [];

  // Check for JavaScript
  $pattern = '/<script[^>]*\bon[a-z]+=[^>]*>.*?<\/script>|<script[^>]*>.*?<\/script>/si';

  if ( preg_match_all( $pattern, $content, $matches ) ) {
    $results['js'] = $matches[0];
  }

  // Suspicious content found?
  if ( empty( $results ) ) {
    return $data;
  }

  // Request?
  if ( isset( $_SERVER ) ) {
    $output = '';

    if ( $current_user ) {
      $sender['user_id'] = '<strong>ID:</strong> ' . $current_user->ID;
      $sender['user_name'] = '<strong>User:</strong> ' . $current_user->user_login;
      $sender['display_name'] = '<strong>Nickname:</strong> ' . $current_user->display_name;
      $sender['email'] = '<strong>Email:</strong> ' . $current_user->user_email;
    }

    $sender['post'] = "<strong>Post:</strong> <a href='{$post_link}'>{$data['post_title']}</a>";

    $sender['ip_address'] = '<strong>IP:</strong> ' . ( $_SERVER['REMOTE_ADDR'] ?? 'n/a' );
    $sender['user_agent'] = '<strong>User Agent:</strong> ' . ( $_SERVER['HTTP_USER_AGENT'] ?? 'n/a' );
    $sender['method'] = '<strong>Method:</strong> ' . ( $_SERVER['REQUEST_METHOD'] ?? 'n/a' );
    $sender['request_uri'] = '<strong>Request URI:</strong> ' . ( $_SERVER['REQUEST_URI'] ?? 'n/a' );
    $sender['referer'] = '<strong>Referer:</strong> ' . ( $_SERVER['HTTP_REFERER'] ?? 'n/a' );

    foreach ( $sender as $item ) {
      $output .= '<p>' . $item . '</p>';
    }

    $message .= sprintf(
      __( '<br><fieldset><legend>Sender</legend>%s</fieldset>', 'fictioneer' ),
      $output
    );
  }

  // JavaScript?
  if ( ! empty( $results['js'] ?? 0 ) ) {
    $output = '';

    foreach ( $results['js'] as $item ) {
      $output .= '<p>' . esc_html( $item ) . '</p>';
    }

    $message .= sprintf(
      __( '<br><fieldset><legend>Potential JavaScript</legend>%s</fieldset>', 'fictioneer' ),
      $output
    );
  }

  // Email to admin (if any)
  if ( ! empty( $admin_email ) ) {
    wp_mail(
      $admin_email,
      sprintf( __( 'Suspicious content on %s', 'fictioneer' ), get_bloginfo( 'name' ) ),
      $message,
      array(
        'Content-Type: text/html; charset=UTF-8'
      )
    );
  }

  // Make sure this only triggers once
  remove_filter( 'wp_insert_post_data', 'fictioneer_see_some_evil', 1 );

  // Continue filter
  return $data;
}

if ( get_option( 'fictioneer_see_some_evil' ) ) {
  add_filter( 'wp_insert_post_data', 'fictioneer_see_some_evil', 1, 3 );
}

// =============================================================================
// GATE UNPUBLISHED CONTENT
// =============================================================================

/**
 * Gates access to unpublished posts
 *
 * @since 5.6.0
 * @global WP_Post $post  The current WordPress post object.
 */

function fictioneer_gate_unpublished_content() {
  global $post;

  // Do nothing if...
  if (
    ! $post ||
    ! is_singular() ||
    ( $post->post_status === 'publish' && $post->post_type !== 'fcn_chapter' ) ||
    get_current_user_id() === absint( $post->post_author )
  ) {
    return;
  }

  // 404 if access is not allowed
  if (
    fictioneer_caching_active( 'get_unpublished_content' ) &&
    $post->post_status !== 'publish' &&
    ! fictioneer_verify_unpublish_access( $post->ID )
  ) {
    fictioneer_redirect_to_404();
  }

  // 404 chapter of unpublished story
  if ( $post->post_type === 'fcn_chapter' ) {
    $story_id = fictioneer_get_chapter_story_id( $post->ID );

    if (
      ! empty( $story_id ) &&
      get_post_status( $story_id ) !== 'publish' &&
      ! fictioneer_verify_unpublish_access( $story_id )
    ) {
      // 404
      fictioneer_redirect_to_404();
    }
  }
}
add_action( 'template_redirect', 'fictioneer_gate_unpublished_content' );

// =============================================================================
// PREVENT TRACKBACK AND PINGBACK VALUES FROM BEING UPDATED
// =============================================================================

/**
 * Prevents trackback/pingback from being updated
 *
 * @since 5.7.0
 *
 * @param array $data  An array of slashed, sanitized, and processed post data.
 *
 * @return array The modified post data.
 */

function fictioneer_prevent_track_and_ping_updates( $data ) {
  // Set data to defaults
  $data['to_ping'] = '';
  $data['ping_status'] = 'closed';
  $data['pinged'] = '';

  // Continue filter
  return $data;
}
add_filter( 'wp_insert_post_data', 'fictioneer_prevent_track_and_ping_updates', 1 );

// =============================================================================
// ADD CLASSES TO BLOCKS
// =============================================================================

/**
 * Add default class to list blocks
 *
 * @since 5.12.0
 *
 * @param string $block_content  HTML content of the block being rendered.
 * @param array  $block          The full block array.
 *
 * @return string The modified block content.
 */

function fictioneer_add_class_to_list_blocks( $block_content, $block ) {
  if ( $block['blockName'] === 'core/list' ) {
    $pattern = '/<(ul|ol)([^>]*)>/i';

    $block_content = preg_replace_callback( $pattern, function ( $matches ) {
      $current_attributes = $matches[2];
      $new_class = 'block-list';

      if ( strpos( $current_attributes, 'class="' ) !== false ) {
        $modified_tag = preg_replace(
          '/class="([^"]*)"/i',
          'class="$1 ' . $new_class . '"',
          $current_attributes
        );
      } else {
        $modified_tag = $current_attributes . ' class="' . $new_class . '"';
      }

      return '<' . $matches[1] . $modified_tag . '>';
    }, $block_content );
  }

  return $block_content;
}
add_filter( 'render_block', 'fictioneer_add_class_to_list_blocks', 10, 2 );

// =============================================================================
// ADD WRAPPER TO READ MORE LINKS
// =============================================================================

/**
 * Add wrapper with class to read more link
 *
 * @since 5.12.0
 *
 * @param string $link  The HTML read more link.
 *
 * @return string The wrapped read more link.
 */

function fictioneer_wrap_read_more_link( $link ) {
  return "<div class='more-link-wrapper'>{$link}</div>";
}
add_filter( 'the_content_more_link', 'fictioneer_wrap_read_more_link' );

// =============================================================================
// WP FONT LIBRARY
// =============================================================================

/**
 * Disabled the WordPress font library
 *
 * @since 5.12.4
 *
 * @param array $link  Default editor settings.
 *
 * @return array Updated editor settings.
 */

function fictioneer_disable_font_library( $editor_settings ) {
  $editor_settings['fontLibraryEnabled'] = false;

  return $editor_settings;
}
add_filter( 'block_editor_settings_all', 'fictioneer_disable_font_library' );

// =============================================================================
// FAST AJAX REQUESTS
// =============================================================================

/**
 * Accelerate AJAX requests by exiting early
 *
 * Note: Requests are validated with fictioneer_get_validated_ajax_user(),
 * making sure that AJAX functions without "_nopriv" are not executed for
 * unauthenticated users.
 *
 * @since 5.15.3
 */

function fictioneer_fast_ajax() {
  $action = sanitize_key( $_REQUEST['action'] ?? '' );

  if ( ! $action ) {
    return;
  }

  $functions = array(
    // Bookmarks
    'fictioneer_ajax_save_bookmarks',
    // Follows
    'fictioneer_ajax_toggle_follow',
    'fictioneer_ajax_clear_my_follows',
    'fictioneer_ajax_mark_follows_read',
    'fictioneer_ajax_get_follows_notifications',
    'fictioneer_ajax_get_follows_list',
    // Reminders
    'fictioneer_ajax_toggle_reminder',
    'fictioneer_ajax_clear_my_reminders',
    'fictioneer_ajax_get_reminders_list',
    // Checkmarks
    'fictioneer_ajax_set_checkmark',
    'fictioneer_ajax_clear_my_checkmarks',
    'fictioneer_ajax_get_finished_checkmarks_list',
    // User
    'fictioneer_ajax_get_user_data',
    'fictioneer_ajax_save_skins',
    'fictioneer_ajax_get_skins',
    'fictioneer_ajax_clear_cookies',
    // Admin
    'fictioneer_ajax_query_relationship_posts',
    'fictioneer_ajax_search_posts_to_unlock'
  );

  if ( get_option( 'fictioneer_enable_fast_ajax_comments' ) && isset( $_REQUEST['fcn_fast_comment_ajax'] ) ) {
    $functions = array_merge(
      $functions,
      array(
        // Comments
        'fictioneer_ajax_delete_my_comment',
        'fictioneer_ajax_moderate_comment',
        'fictioneer_ajax_report_comment',
        'fictioneer_ajax_get_comment_form',
        'fictioneer_ajax_get_comment_section',
        'fictioneer_ajax_submit_comment',
        'fictioneer_ajax_edit_comment'
      )
    );
  }

  if ( in_array( $action, $functions ) && function_exists( $action ) ) {
    call_user_func( $action );
    exit;
  }
}

if ( isset( $_REQUEST['fcn_fast_ajax'] ) || isset( $_REQUEST['fcn_fast_comment_ajax'] ) ) {
  add_action( 'init', 'fictioneer_fast_ajax', 99999 );
}

// =============================================================================
// REMOVE EMOJIS
// =============================================================================

/**
 * Removes the default WordPress emojis
 *
 * @since 5.20.2
 */

function fictioneer_disable_emojis() {
  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
  remove_action( 'wp_print_styles', 'print_emoji_styles' );
  remove_action( 'admin_print_styles', 'print_emoji_styles' );
  remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
  remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
  remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
  add_filter( 'tiny_mce_plugins', 'fictioneer_disable_tiny_mce_emojis' );
  add_filter( 'wp_resource_hints', 'fictioneer_remove_emoji_resource_hint', 10, 2 );
}

if ( get_option( 'fictioneer_disable_emojis' ) ) {
  add_action( 'init', 'fictioneer_disable_emojis' );
}

/**
 * Removes the TinyMCE emoji plugin
 *
 * @since 5.20.2
 * @link https://developer.wordpress.org/reference/hooks/tiny_mce_plugins/
 *
 * @param array $plugins  An array of default TinyMCE plugins.
 *
 * @return array Updated array of plugins.
 */

function fictioneer_disable_tiny_mce_emojis( $plugins ) {
  return array_diff( $plugins, ['wpemoji'] );
}

/**
 * Removes emoji CDN hostname from DNS prefetching hints
 *
 * @since 5.20.2
 * @link https://developer.wordpress.org/reference/hooks/wp_resource_hints/
 *
 * @param array  $urls           Array of resources and their attributes, or URLs to print for resource hints.
 * @param string $relation_type  The relation type the URLs are printed for.
 *
 * @return array Updated array of resource hints.
 */

function fictioneer_remove_emoji_resource_hint( $urls, $relation_type ) {
  if ( $relation_type === 'dns-prefetch' ) {
    $urls = array_diff( $urls, [ apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' ) ] );
  }

  return $urls;
}

// =============================================================================
// REMOVE UNNECESSARY & PERFORMANCE-WASTING FILTERS
// =============================================================================

remove_filter( 'the_title', 'capital_P_dangit', 11 );
remove_filter( 'the_content', 'capital_P_dangit', 11 );
remove_filter( 'comment_text', 'capital_P_dangit', 31 );

// =============================================================================
// FIX EXCERPT FORMATTING
// =============================================================================

/**
 * Fix inconsistent line breaks in excerpts
 *
 * @since 4.0
 * @since 5.22.1 - Re-added since it is still needed.
 * @link https://github.com/WordPress/gutenberg/issues/15117
 *
 * @param string  $excerpt  The post excerpt.
 * @param WP_Post $post     The post object.
 *
 * @return string The fixed excerpt.
 */

function fictioneer_fix_excerpt( $excerpt, $post ) {
  add_filter( 'the_content', 'fictioneer_replace_br_with_whitespace', 6 );
  $excerpt = wp_trim_excerpt( $excerpt, $post );
  remove_filter( 'the_content', 'fictioneer_replace_br_with_whitespace', 6 );

  return $excerpt;
}
remove_filter( 'get_the_excerpt', 'wp_trim_excerpt' );
add_filter( 'get_the_excerpt', 'fictioneer_fix_excerpt', 10, 2 );

/**
 * Replace line breaks with whitespace
 *
 * @since 4.0
 * @since 5.22.1 - Re-added since it is still needed.
 *
 * @param string $text  String to process.
 *
 * @return string The processed string.
 */

function fictioneer_replace_br_with_whitespace( $text ) {
  return str_replace( '<br>', ' ', $text );
}

// =============================================================================
// TRY PREVENTING INDEXING OF INTERNAL URLS
// =============================================================================

/**
 * Adds noindex and nofollow headers to certain requests
 *
 * @since 5.23.1
 */

function fictioneer_block_pages_from_indexing() {
  $params = ['oauth_nonce', 'nonce', 'return_url', 'commentcode', 'moderation-hash'];

  foreach ( $params as $param ) {
    if ( isset( $_GET[ $param ] ) ) {
      header( 'X-Robots-Tag: noindex, nofollow', true );
      break;
    }
  }
}
add_action( 'send_headers', 'fictioneer_block_pages_from_indexing' );

// =============================================================================
// REDIRECT SCHEDULED CHAPTER 404
// =============================================================================

/**
 * Redirects scheduled chapter 404 to story or home
 *
 * @since 5.27.2
 */

function fictioneer_redirect_scheduled_chapter_404() {
  if ( current_user_can( 'manage_options' ) || current_user_can( 'edit_others_fcn_chapters' ) ) {
    return; // Default behavior
  }

  global $wp_query, $post;

  if ( isset( $wp_query->query_vars['p'] ) && ( $wp_query->query_vars['post_type'] ?? 0 ) === 'fcn_chapter' ) {
    $post_id = absint( $wp_query->query_vars['p'] );
    $user_id = get_current_user_id();
    $author_id = (int) get_post_field( 'post_author', $post_id );

    if ( $user_id && $user_id === $author_id ) {
      return; // Default behavior
    }

    if ( ! $post ) {
      $post_status = get_post_status( $post_id );
    } else {
      $post_status = $post->post_status;
    }

    if ( $post_status === 'future' ) {
       $story_id = fictioneer_get_chapter_story_id( $post_id );

      if ( $story_id ) {
        wp_safe_redirect( get_permalink( $story_id ), 307 );
      } else {
        wp_safe_redirect( home_url(), 307 );
      }

      exit;
    }
  }
}

if ( get_option( 'fictioneer_redirect_scheduled_chapter_404' ) ) {
  add_action( 'template_redirect', 'fictioneer_redirect_scheduled_chapter_404' );
}

// =============================================================================
// CACHE LAST POST MODIFIED DATE
// =============================================================================

/**
 * Cache the last post modified date for a given timezone and post type.
 *
 * Hooks into `pre_get_lastpostmodified()` to cache the last modified date
 * of posts in a Transient for 5 minutes.
 *
 * @since 5.27.4
 *
 * @param string|false $lastpostmodified  The last modified date, or false if unknown.
 * @param string       $timezone          The timezone to retrieve the modified date in.
 *                                        Accepts 'gmt' or 'blog' (default WordPress timezone).
 * @param string       $post_type         The post type to check for the last modified post.
 *
 * @return string|false The last modified date as a MySQL datetime string, or false if none found.
 */

function fictioneer_cache_lastpostmodified( $lastpostmodified, $timezone, $post_type ) {
  $transient_key = 'lastpostmodified_' . sanitize_key( $timezone ) . '_' . sanitize_key( $post_type );

  $cached_value = get_transient( $transient_key );

  if ( $cached_value !== false ) {
    return $cached_value;
  }

  remove_filter( 'pre_get_lastpostmodified', 'fictioneer_cache_lastpostmodified' );

  $lastpostmodified = get_lastpostmodified( $timezone, $post_type );

  add_filter( 'pre_get_lastpostmodified', 'fictioneer_cache_lastpostmodified', 10, 3 );

  set_transient( $transient_key, $lastpostmodified, 5 * MINUTE_IN_SECONDS );

  return $lastpostmodified;
}

if ( get_option( 'fictioneer_enable_lastpostmodified_caching' ) ) {
  add_filter( 'pre_get_lastpostmodified', 'fictioneer_cache_lastpostmodified', 10, 3 );
}
