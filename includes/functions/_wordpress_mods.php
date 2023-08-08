<?php

// =============================================================================
// MAINTENANCE MODE
// =============================================================================

if ( ! function_exists( 'fictioneer_maintenance_mode' ) ) {
  /**
   * Toggle maintenance mode from settings with message
   *
   * @since 5.0
   */

  function fictioneer_maintenance_mode() {
    if ( get_option( 'fictioneer_enable_maintenance_mode' ) ) {
      if ( ! current_user_can( 'edit_themes' ) || ! is_user_logged_in() ) {
        $note = get_option( 'fictioneer_phrase_maintenance' );
        $note = ! empty( $note ) ? $note : __( 'Website under planned maintenance. Please check back later.', 'fictioneer' );
        wp_die( __( '<h1>Under Maintenance</h1><br>', 'fictioneer' ) . $note );
      }
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
add_filter( 'xmlrpc_enabled', '__return_false' );

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
// CUSTOM EXCERPT LENGTH
// =============================================================================

if ( ! function_exists( 'fictioneer_custom_excerpt_length' ) ) {
  /**
   * Change excerpt length
   */

  function fictioneer_custom_excerpt_length( $length ) {
    return 64;
  }
}
add_filter( 'excerpt_length', 'fictioneer_custom_excerpt_length', 999 );

// =============================================================================
// FIX EXCERPT FORMATTING
// =============================================================================

if ( ! function_exists( 'fictioneer_fix_excerpt' ) ) {
  /**
   * Fix inconsistent line breaks in excerpts
   *
   * @since 4.0
   * @link https://github.com/WordPress/gutenberg/issues/15117
   *
   * @param string $excerpt The post excerpt.
   */

  function fictioneer_fix_excerpt( $excerpt ) {
    add_filter( 'the_content', 'fictioneer_replace_br_with_whitespace', 6 );
    $excerpt = wp_trim_excerpt( $excerpt );
    remove_filter( 'the_content', 'fictioneer_replace_br_with_whitespace', 6 );
    return $excerpt;
  }
}
remove_filter( 'get_the_excerpt', 'wp_trim_excerpt' );
add_filter( 'get_the_excerpt', 'fictioneer_fix_excerpt' );

/**
 * Replace line breaks with whitespace
 *
 * @since 4.0
 *
 * @param string $text String to process.
 */

function fictioneer_replace_br_with_whitespace( $text ) {
  return str_replace( '<br>', ' ', $text );
}

// =============================================================================
// UPDATE MODIFIED DATE OF STORY WHEN CHAPTER IS UPDATED
// =============================================================================

if ( ! function_exists( 'fictioneer_update_modified_date_on_story_for_chapter' ) ) {
  /**
   * Update modified date of story when chapter is updated
   *
   * @since 3.0
   *
   * @param int $post_id The post ID.
   */

  function fictioneer_update_modified_date_on_story_for_chapter( $post_id ) {
    global $wpdb;

    // Prevent multi-fire
    if ( fictioneer_multi_save_guard( $post_id ) ) {
      return;
    }

    // Chapter updated?
    if ( get_post_type( $post_id ) != 'fcn_chapter' ) return;

    // Setup
    $story_id = fictioneer_get_field( 'fictioneer_chapter_story', $post_id );

    // No linked story found
    if ( empty( $story_id ) ) return;

    // Get current time for update (close enough to the chapter)
    $post_modified = current_time( 'mysql' );
    $post_modified_gmt = current_time( 'mysql', true );

    // Update database
    $wpdb->query( "UPDATE $wpdb->posts SET post_modified = '{$post_modified}', post_modified_gmt = '{$post_modified_gmt}' WHERE ID = {$story_id}" );
  }
}
add_action( 'save_post', 'fictioneer_update_modified_date_on_story_for_chapter' );
add_action( 'trashed_post', 'fictioneer_update_modified_date_on_story_for_chapter' );
add_action( 'delete_post', 'fictioneer_update_modified_date_on_story_for_chapter' );
add_action( 'untrash_post', 'fictioneer_update_modified_date_on_story_for_chapter' );

// =============================================================================
// STORE WORD COUNT AS CUSTOM FIELD
// =============================================================================

if ( ! function_exists( 'fictioneer_save_word_count' ) ) {
  /**
   * Store word count of posts as meta data
   *
   * @since 3.0
   * @see   update_post_meta()
   *
   * @param int $post_id Post ID.
   */

  function fictioneer_save_word_count( $post_id ) {
    // Prevent multi-fire
    if ( fictioneer_multi_save_guard( $post_id ) ) {
      return;
    }

    // Prepare
    $content = get_post_field( 'post_content', $post_id );
    $content = strip_shortcodes( $content );
    $content = strip_tags( $content );

    // Count
    $word_count = str_word_count( $content );

    // Remember
    update_post_meta( $post_id, '_word_count', $word_count );
  }
}
add_action( 'save_post', 'fictioneer_save_word_count' );

// =============================================================================
// CUSTOMIZE ADMIN BAR
// =============================================================================

/**
 * Reduce admin bar based on setting
 *
 * @since 5.0
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

if ( ! function_exists( 'fictioneer_logout_redirect' ) ) {
  /**
   * Change redirect after logout
   *
   * @since 4.0
   *
   * @param string $logout_url The HTML-encoded logout URL.
   * @param string $redirect   Path to redirect to on logout.
   *
   * @return string $logout_url The updated logout URL.
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
}

if ( get_option( 'fictioneer_logout_redirects_home' ) ) {
  add_filter( 'logout_url', 'fictioneer_logout_redirect', 10, 2 );
}

// =============================================================================
// CUSTOM LOGOUT
// =============================================================================

if ( ! function_exists( 'fictioneer_add_logout_endpoint' ) ) {
  /**
   * Add route to logout script
   *
   * @since Fictioneer 5.0
   */

  function fictioneer_add_logout_endpoint() {
    add_rewrite_endpoint( FICTIONEER_LOGOUT_ENDPOINT, EP_ROOT );
  }
}

if ( FICTIONEER_LOGOUT_ENDPOINT && ! get_option( 'fictioneer_disable_theme_logout' ) ) {
  add_action( 'init', 'fictioneer_add_logout_endpoint', 10 );
}

if ( ! function_exists( 'fictioneer_logout' ) ) {
  /**
   * Logout without _wpnonce and no login screen
   *
   * @since 5.0
   */

  function fictioneer_logout() {
    global $wp;

    // Abort if not on logout route
    if ( $wp->request != FICTIONEER_LOGOUT_ENDPOINT ) return;

    // Redirect home if not logged-in
    if ( ! is_user_logged_in() ) wp_safe_redirect( get_home_url() );

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
}

if ( FICTIONEER_LOGOUT_ENDPOINT && ! get_option( 'fictioneer_disable_theme_logout' ) ) {
  add_action( 'template_redirect', 'fictioneer_logout', 10 );
}

if ( ! function_exists( 'fictioneer_get_logout_url' ) ) {
  /**
   * Fictioneer logout URL with optional redirect
   *
   * @since 5.0
   *
   * @param string $redirect URL to redirect to after logout.
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
// SHOW CUSTOM POST TYPES ON TAG/CATEGORY ARCHIVES
// =============================================================================

if ( ! function_exists( 'fictioneer_extend_taxonomy_pages' ) ) {
  /**
   * Show custom post types in tag and category archives
   *
   * @since 4.0
   * @link https://wordpress.stackexchange.com/a/28147/223620
   *
   * @param WP_Query $query The query.
   */

  function fictioneer_extend_taxonomy_pages( $query ) {
    if ( ( is_tag() || is_tax( 'fcn_genre' ) || is_tax( 'fcn_fandom' ) || is_tax( 'fcn_character' ) || is_tax( 'fcn_content_warning' ) || is_category() ) && $query->is_main_query() && ! is_admin() ) {
      $post_types = array( 'post', 'fcn_story', 'fcn_chapter', 'fcn_recommendation', 'fcn_collection' );
      $query->set( 'post_type', $post_types );
    }
  }
}
add_filter( 'pre_get_posts', 'fictioneer_extend_taxonomy_pages' );

// =============================================================================
// DISABLE SELECTED ARCHIVES
// =============================================================================

/**
 * Disable date archives
 *
 * @since 3.0
 */

function fictioneer_disable_date_archives() {
  if ( is_date() ) {
    wp_redirect( home_url(), 301 );
    die();
  }
}
add_action( 'template_redirect', 'fictioneer_disable_date_archives' );

// =============================================================================
// MODIFY RSS FEEDS
// =============================================================================

/**
 * Get template for story feed (chapters)
 *
 * @since 4.0
 */

function fictioneer_story_rss_template() {
  get_template_part( 'rss', 'rss-story' );
}

/**
 * Add feed for story (chapters)
 *
 * @since 4.0
 */

function fictioneer_story_rss() {
  add_feed( 'rss-chapters', 'fictioneer_story_rss_template' );
}

/**
 * Add custom main feed
 *
 * @since 4.0
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
  add_action( 'do_feed_rss2', 'fictioneer_main_rss_template', 10, 1 );
} else {
  // Default feed links
  add_theme_support( 'automatic-feed-links' );
}

// =============================================================================
// OUTPUT RSS
// =============================================================================

if ( ! function_exists( 'fictioneer_output_rss' ) ) {
  /**
   * Output RSS feed
   *
   * @since Fictioneer 5.0
   *
   * @param int|null $post_id Optional. The current post ID.
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
        FICTIONEER_SITE_NAME,
        get_the_title( get_the_ID() )
      );

      $url = esc_url( add_query_arg( 'story_id', get_the_ID(), home_url( 'feed/rss-chapters' ) ) );
      $rss_link = '<link rel="alternate" type="application/rss+xml" title="' . $title . '" href="' . $url . '" />';
    }

    // RSS Feed if chapter...
    $story_id = fictioneer_get_field( 'fictioneer_chapter_story', $post_id );

    if ( $post_type == 'fcn_chapter' && ! empty( $story_id ) && ! $skip_to_default ) {
      $title = sprintf(
        _x( '%1$s - %2$s Chapters', 'Story Feed: [Site] - [Story] Chapters.', 'fictioneer' ),
        FICTIONEER_SITE_NAME,
        get_the_title( $story_id )
      );

      $url = esc_url( add_query_arg( 'story_id', $story_id, home_url( 'feed/rss-chapters' ) ) );
      $rss_link = '<link rel="alternate" type="application/rss+xml" title="' . $title . '" href="' . $url . '" />';
    }

    // Default RSS Feed
    if ( empty( $rss_link ) ) {
      $title = sprintf(
        _x( '%s Feed', '[Site] Feed', 'fictioneer' ),
        FICTIONEER_SITE_NAME
      );

      $url = esc_url( home_url( 'feed' ) );
      $rss_link = '<link rel="alternate" type="application/rss+xml" title="' . $title . '" href="' . $url . '" />';
    }

    // Output
    echo $rss_link;
  }
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
  return __( '%s' );
}
add_filter( 'protected_title_format', 'fictioneer_remove_protected_text' );

// =============================================================================
// ADD WRAPPER TO DOWNLOAD BLOCK
// =============================================================================

/**
 * Add wrapper to download block
 *
 * @since 4.0
 *
 * @param  string $block_content The block content.
 * @param  array  $block         The full block, including name and attributes.
 * @return string $block_content The updated block content.
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
 * @since 4.0
 * @license CC BY-SA 4.0
 * @link https://stackoverflow.com/a/67527400/17140970
 *
 * @global object $post The global post.
 * @return string $form The custom password form.
 */

function fictioneer_password_form() {
  global $post;

  $label = 'pwbox-' . ( empty( $post->ID ) ? rand() : $post->ID );

  $form = '<form class="post-password-form" action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" method="post"><div><i class="fa-solid fa-lock icon-password-form"></i><div class="password-wrapper"><input name="post_password" id="' . $label . '" type="password" data-lpignore="true" required size="20" placeholder="' . esc_attr__( 'Password', 'fictioneer' ) . '"></div><div class="password-submit"><input type="submit" name="Submit" value="' . esc_attr__( 'Unlock' ) . '" /><input type="hidden" name="_wp_http_referer" value="' . esc_attr( wp_unslash( $_SERVER['REQUEST_URI'] ) ) . '"></div></div></form>';

  return $form;
}
add_filter( 'the_password_form', 'fictioneer_password_form', 10, 1 );

// =============================================================================
// ADD ID TO CONTENT PARAGRAPHS IN CHAPTERS
// =============================================================================

if ( ! function_exists( 'fictioneer_add_chapter_paragraph_id' ) ) {
  /**
   * Adds incrementing ID to chapter paragraphs
   *
   * @since 3.0
   * @license CC BY-SA 3.0
   * @link https://wordpress.stackexchange.com/a/152169/223620
   *
   * @param  string $content The content.
   * @return string $content The modified content.
   */

  function fictioneer_add_chapter_paragraph_id( $content ) {
    global $post;

    if (
      get_post_type() != 'fcn_chapter' ||
      ! in_the_loop() ||
      ! is_main_query() ||
      post_password_required()
    ) return $content;

    return preg_replace_callback(
      '|<p|',
      function ( $matches ) {
        static $i = 0;
        return sprintf( '<p id="paragraph-%d" data-paragraph-id="%d" ', $i, $i++ );
      },
      $content
    );
  }
}
add_filter( 'the_content', 'fictioneer_add_chapter_paragraph_id', 10, 1 );

// =============================================================================
// ADD LIGHTBOX TO POST IMAGES
// =============================================================================

if ( ! function_exists( 'fictioneer_add_lightbox_to_post_images' ) ) {
  /**
   * Adds lightbox data attributes to post images
   *
   * @since 3.0
   * @license CC BY-SA 3.0
   * @link https://wordpress.stackexchange.com/a/84542/223620
   * @link https://jhtechservices.com/changing-your-image-markup-in-wordpress/
   *
   * @param  string $content The content.
   * @return string $content The modified content.
   */

  function fictioneer_add_lightbox_to_post_images( $content ) {
    if ( empty( $content ) ) return $content;

    // Setup
    libxml_use_internal_errors( true );
    $doc = new DOMDocument();
    $doc->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ) );
    $images = $doc->getElementsByTagName( 'img' );

    // Iterate over each img tag
    foreach ( $images as $img ) {
      $classes = $img->getAttribute( 'class' );
      $parent = $img->parentNode;
      $parent_classes = $parent->getAttribute( 'class' );
      $parent_href = strtolower( $parent->getAttribute( 'href' ) );

      // Abort if...
      if (
        str_contains( $classes . $parent_classes, 'no-auto-lightbox' ) ||
        $parent->hasAttribute( 'target' )
      ) continue;

      if (
        $parent->hasAttribute( 'href' ) &&
        ! preg_match( '/(?<=\.jpg|jpeg|png|gif|webp|svg|avif|apng|tiff|ico)(?:$|[#?])/', $parent_href )
      ) continue;

      $src = $img->getAttribute( 'src' );
      $id = preg_match( '/wp-image-([0-9]+)/i', $classes, $class_id );

      if ( $class_id ) {
        $id = absint( $class_id[1] );
        $img->setAttribute( 'data-attachment-id', $id );
      }

      if ( wp_get_attachment_image_url( $id, 'full' ) ) {
        $src = wp_get_attachment_image_url( $id, 'full' );
      }

      $img->setAttribute( 'data-src', $src );
      $img->setAttribute( 'data-lightbox', '' );
      $img->setAttribute( 'tabindex', '0' );
    };

    $content = $doc->saveHTML();

    return $content;
  }
}

if ( get_option( 'fictioneer_enable_lightbox' ) ) {
  add_filter( 'the_content', 'fictioneer_add_lightbox_to_post_images', 15 );
}

/**
 * Adds data-lightbox attribute if enabled
 *
 * @since 4.7
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
 */

function fictioneer_query_vars( $qvars ) {
  $qvars[] = 'sf_paged';
  $qvars[] = 'commentcode';
  $qvars[] = 'cp'; // e.g. custom/current page
  $qvars[] = 'pg'; // e.g. page
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
 * @since 4.7
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
// REMOVE GLOBAL SVG FILTERS
// =============================================================================

if ( get_option( 'fictioneer_remove_wp_svg_filters' ) ) {
  remove_filter( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
}

// =============================================================================
// ADD CONSENT WRAPPER TO EMBEDS
// =============================================================================

if ( ! function_exists( 'fictioneer_embed_consent_wrappers' ) ) {
  /**
   * Wraps embeds into a consent box that must be clicked to load the embed.
   *
   * @since Fictioneer 3.0
   *
   * @param  string $content The content.
   *
   * @return string The modified content.
   */

  function fictioneer_embed_consent_wrappers( $content ) {
    if( empty( $content ) ) return $content;

    libxml_use_internal_errors( true );
    $dom = new DOMDocument();
    $dom->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ) );
    $xpath = new DomXPath( $dom );

    $iframes = $dom->getElementsByTagName( 'iframe' );
    $twitter_timelines = $xpath->query( "//a[@class='twitter-timeline']/following-sibling::*[1]" );
    $twitter_tweets = $xpath->query( "//blockquote[@class='twitter-tweet']/following-sibling::*[1]" );

    // Process iframes
    foreach ( $iframes as $iframe ) {
      $src = $iframe->getAttribute( 'src' );
      $title = htmlspecialchars( $iframe->getAttribute( 'title' ) );
      $title = ( ! $title || $title == '') ? 'embedded content' : $title;

      $iframe->removeAttribute('src');

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

    return $dom->saveHTML();
  }
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
	$methods['twitter'] = __( 'Twitter Username' );
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
 * @since 5.0
 */

function fictioneer_disable_attachment_pages() {
  if ( is_attachment() ) {
    $url = wp_get_attachment_url( get_queried_object_id() );
    wp_redirect( $url, 301 );
  }
  return;
}

if ( ! FICTIONEER_ATTACHMENT_PAGES ) {
  add_action( 'template_redirect', 'fictioneer_disable_attachment_pages', 10 );
}

// =============================================================================
// ALLOWED PROTOCOLS
// =============================================================================

/**
 * Extend list of allowed protocols
 *
 * @since 4.0
 *
 * @param array $protocols Array of allowed protocols.
 *
 * @return array $protocols Updated array of allowed protocols.
 */

function fictioneer_extend_allowed_protocols( $protocols ){
	$protocols[] = 's3'; // Amazon S3 bucket
	$protocols[] = 'spotify'; // Load a track, album, artist, search, or playlist in Spotify
	$protocols[] = 'steam'; // Interact with Steam: install apps, purchase games, run games, etc

	return $protocols;
}
add_filter( 'kses_allowed_protocols' , 'fictioneer_extend_allowed_protocols' );

// =============================================================================
// DISABLE REST API
// =============================================================================

/**
 * Restrict default REST API endpoints
 *
 * @since 5.2.4
 * @link https://developer.wordpress.org/reference/hooks/rest_authentication_errors/
 *
 * @param WP_Error|null|true $errors WP_Error if authentication error, null
 *                                   if authentication method wasn't used,
 *                                   true if authentication succeeded.
 *
 * @return WP_Error|null|true $errors The filters errors.
 */

function fictioneer_restrict_rest_api( $errors ) {
  if ( $errors === true || is_wp_error( $errors ) ) {
    return $errors;
  }

  // Restrict default API endpoints to users with 'edit_posts' permission (required for Gutenberg to work)
  if ( preg_match( '/^\/wp-json\/wp\/v2\//', $_SERVER['REQUEST_URI'] ) && ! current_user_can( 'edit_posts' ) ) {
    return new WP_Error(
      'rest_insufficient_permission',
      __( 'You are not authorized to use the API.' ),
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
 * @param WP_Query $query The current WP_Query object.
 */

function fictioneer_add_sof_to_taxonomy_query( $query ) {
  // Abort if...
  if ( is_admin() || ! is_archive() || ! $query->is_main_query() ) return;

  // Use empty array to get date query
  $date_query = fictioneer_append_date_query( [] );

  // If date query set...
  if ( ! empty( $date_query['date_query'] ) ) {
    $query->set( 'date_query', $date_query['date_query'] );
  }

  // Post type?
  $post_type = strtolower( sanitize_text_field( $_GET['post_type'] ?? '' ) );
  $post_type = array_intersect(
    [ $post_type ],
    ['any', 'post', 'fcn_story', 'fcn_chapter', 'fcn_collection', 'fcn_recommendation']
  );
  $post_type = reset( $post_type ) ?: null;

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
// STRIP SHORTCODES FROM NON-ADMIN POSTS
// =============================================================================

/**
 * Strip shortcodes for non-admins
 *
 * @since Fictioneer 5.4.9
 *
 * @param string $content  The content to be saved.
 *
 * @return string The cleaned content.
 */

function fictioneer_strip_shortcodes_for_non_administrators( $content ) {
  if ( ! current_user_can( 'administrator' ) ) {
    $content = strip_shortcodes( $content );
  }

  return $content;
}

if ( get_option( 'fictioneer_strip_shortcodes_for_non_administrators' ) ) {
  add_filter( 'content_save_pre', 'fictioneer_strip_shortcodes_for_non_administrators' );
}

// =============================================================================
// DISABLE WIDGETS
// =============================================================================

/**
 * Disable all widgets and boost performance slightly
 *
 * @since Fictioneer 5.5.3
 */

function fictioneer_disable_widgets() {
  global $wp_widget_factory;

  $wp_widget_factory->widgets = array();
}

if ( get_option( 'fictioneer_disable_all_widgets' ) ) {
  add_action( 'widgets_init', 'fictioneer_disable_widgets', 99 );
}

?>
