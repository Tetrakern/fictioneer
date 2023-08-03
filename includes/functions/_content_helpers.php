<?php

// =============================================================================
// GET LONG EXCERPT
// =============================================================================

if ( ! function_exists( 'fictioneer_get_excerpt' ) ) {
  /**
   * Returns the excerpt with theme adjustments
   *
   * @since Fictioneer 5.0
   *
   * @return string The customized excerpt.
   */

  function fictioneer_get_excerpt() {
    $excerpt = get_the_excerpt();
    $excerpt = trim( preg_replace( '/\s+/', ' ', $excerpt ) );

    // Return result
    return $excerpt;
  }
}

/**
 * Replace excerpt ellipsis
 *
 * @since Fictioneer 5.2.5
 *
 * @return string The ellipsis (…).
 */

function fictioneer_excerpt_ellipsis() {
  return '…';
}
add_filter( 'excerpt_more', 'fictioneer_excerpt_ellipsis' );

// =============================================================================
// GET EXCERPT WITH MAXIMUM LENGTH (CHARACTERS)
// =============================================================================

if ( ! function_exists( 'fictioneer_get_limited_excerpt' ) ) {
  /**
   * Returns excerpt with maximum length in characters
   *
   * @since Fictioneer 3.0
   *
   * @param int    $limit  The character limit.
   * @param string $source Either the 'content' or 'excerpt'. Default 'excerpt'.
   *
   * @return string The customized excerpt.
   */

  function fictioneer_get_limited_excerpt( $limit, $source = 'excerpt' ) {
    $excerpt = $source == 'content' ? get_the_content() : get_the_excerpt();
    $excerpt = preg_replace( ' (\[.*?\])', '', $excerpt );
    $excerpt = strip_shortcodes( $excerpt );
    $excerpt = strip_tags( $excerpt );
    $excerpt = substr( $excerpt, 0, $limit );
    $excerpt = substr( $excerpt, 0, strripos( $excerpt, ' ' ) );
    $excerpt = trim( preg_replace( '/\s+/', ' ', $excerpt ) );
    $excerpt = $excerpt . '…';
    return $excerpt;
  }
}

// =============================================================================
// GET FIRST PARAGRAPH AS EXCERPT
// =============================================================================

if ( ! function_exists( 'fictioneer_first_paragraph_as_excerpt' ) ) {
  /**
   * Returns the first paragraph of the content as excerpt
   *
   * @since Fictioneer 3.0
   *
   * @param string $content The content as HTML.
   *
   * @return string First paragraph as excerpt, stripped of tags.
   */

  function fictioneer_first_paragraph_as_excerpt( $content ) {
    $excerpt = strip_shortcodes( $content );
    $excerpt = substr( $excerpt, 0, strpos( $excerpt, '</p>' ) + 4 );
    return wp_strip_all_tags( $excerpt, true );
  }
}

// =============================================================================
// GET FORCED EXCERPT EVEN IF POST IS PROTECTED
// =============================================================================

if ( ! function_exists( 'fictioneer_get_forced_excerpt' ) ) {
  /**
   * Returns the excerpt even if the post is protected
   *
   * @since Fictioneer 4.5
   *
   * @param int     $post_id Post ID.
   * @param int     $limit   Maximum number of characters.
   * @param boolean $default Whether to return the original excerpt if present.
   *
   * @return string The excerpt stripped of tags.
   */

  function fictioneer_get_forced_excerpt( $post_id, $limit = 256, $default = false ) {
    $post = get_post( $post_id );

    if ( ! $default && $post->post_excerpt != '' ) return $post->post_excerpt;

    $excerpt = $post->post_content;
    $excerpt = preg_replace( ' (\[.*?\])', '', $excerpt );
    $excerpt = strip_shortcodes( $excerpt );
    $excerpt = wp_strip_all_tags( $excerpt );
    $excerpt = substr( $excerpt, 0, $limit );
    $excerpt = substr( $excerpt, 0, strripos( $excerpt, ' ' ) );
    $excerpt = trim( preg_replace( '/\s+/', ' ', $excerpt ) );
    $excerpt = $excerpt . '…';

    return $excerpt;
  }
}

// =============================================================================
// AUTHOR NODE
// =============================================================================

if ( ! function_exists( 'fictioneer_get_author_node' ) ) {
  /**
   * Returns an author's display name as link to public profile
   *
   * @since Fictioneer 4.0
   *
   * @param int    $author_id  The author's user ID. Defaults to current post author.
   * @param string $classes    Optional. String of CSS classes.
   *
   * @return string HTML with author's display name as public profile link.
   */

  function fictioneer_get_author_node( $author_id = null, $classes = '' ) {
    $author_id = $author_id ? $author_id : get_the_author_meta( 'ID' );
    $author_name = get_the_author_meta( 'display_name', $author_id );
    $author_url = get_author_posts_url( $author_id );

    return "<a href='{$author_url}' class='author {$classes}'>{$author_name}</a>";
  }
}

if ( ! function_exists( 'fictioneer_the_author_node' ) ) {
  /**
   * Outputs the author node
   *
   * @since Fictioneer 4.0
   *
   * @param int    $author_id The author's user ID. Defaults to current post author.
   * @param string $classes   Optional. String of CSS classes.
   */

  function fictioneer_the_author_node( $author_id = null, $classes = '' ) {
    echo fictioneer_get_author_node( $author_id, $classes );
  }
}

// =============================================================================
// ICON
// =============================================================================

if ( ! function_exists( 'fictioneer_icon' ) ) {
  /**
   * Outputs the HTML for an inline svg icon
   *
   * @since  Fictioneer 4.0
   *
   * @param string $icon    Name of the icon that matches the svg.
   * @param string $classes Optional. String of CSS classes.
   * @param string $id      Optional. An element ID.
   * @param string $inserts Optional. Additional attributes.
   */

  function fictioneer_icon( $icon, $classes = '', $id = '', $inserts = '' ) {
    echo fictioneer_get_icon( $icon, $classes, $id, $inserts );
  }
}

if ( ! function_exists( 'fictioneer_get_icon' ) ) {
  /**
   * Returns the HTML for an inline svg icon
   *
   * Uses a similar structure to Font Awesome but inserts an external svg
   * via the <use> tag. The version is added as query variable for cache
   * busting purposes.
   *
   * @since  Fictioneer 4.7
   *
   * @param string $icon    Name of the icon that matches the svg.
   * @param string $classes Optional. String of CSS classes.
   * @param string $id      Optional. An element ID.
   * @param string $inserts Optional. Additional attributes.
   */

  function fictioneer_get_icon( $icon, $classes = '', $id = '', $inserts = '' ) {
    ob_start();
		// Start HTML ---> ?>
    <svg id="<?php echo $id; ?>" <?php echo $inserts; ?> class="icon _<?php echo $icon; ?> <?php echo $classes; ?>">
      <use xlink:href="<?php echo get_template_directory_uri(); ?>/img/icon-sprite.svg?ver=<?php echo FICTIONEER_VERSION; ?>#icon-<?php echo $icon; ?>"></use>
    </svg>
		<?php // <--- End HTML
		return ob_get_clean();
  }
}

// =============================================================================
// GET TITLE AND ACCOUNT FOR EMPTY
// =============================================================================

if ( ! function_exists( 'fictioneer_get_safe_title' ) ) {
  /**
   * Returns the cleaned title and accounts for empty strings
   *
   * @since Fictioneer 4.7
   *
   * @param int $post_id The post ID to get the title for.
   *
   * @return string The title, never empty.
   */

  function fictioneer_get_safe_title( $post_id ) {
    // Get title and remove HTML
    $title = wp_strip_all_tags( get_the_title( $post_id ) );

    // If empty, use the datetime as title
    if ( empty( $title ) ) {
      $title = sprintf(
        _x( '%1$s — %2$s', '[Date] — [Time] if post title is missing.', 'fictioneer' ),
        get_the_date( '', $post_id ),
        get_the_time( '', $post_id )
      );
    }

    return $title;
  }
}

// =============================================================================
// GET READING TIME NODES
// =============================================================================

if ( ! function_exists( 'fictioneer_get_reading_time_nodes' ) ) {
  /**
   * Returns the HTML for the reading time nodes
   *
   * Returns the estimated reading time for a given amount of words in two nodes,
   * a long one for desktop and a short one for mobile.
   *
   * @since Fictioneer 4.7
   *
   * @param int $word_count The number of words.
   *
   * @return string HTML for the reading time nodes.
   */

  function fictioneer_get_reading_time_nodes( $word_count ) {
    $wpm = absint( get_option( 'fictioneer_words_per_minute', 200 ) );
    $wpm = max( 1, $wpm );
    $t = round( $word_count / $wpm * 60 );
    $long = '';
    $short = '';

    if ( $t / 86400 >= 1 ) {
      $d = floor( ( $t%2592000 ) / 86400 );
      $h = floor( ( $t%86400 ) / 3600 );

      $long = sprintf(
        _x( '%1$s, %2$s', 'Long reading time statistics: [d] days, [h] hours.', 'fictioneer' ),
        sprintf( _n( '%s day', '%s days', $d, 'fictioneer' ), $d ),
        sprintf( _n( '%s hour', '%s hours', $h, 'fictioneer' ), $h )
      );

      $short = sprintf(
        _x( '%1$s, %2$s', 'Shortened reading time statistics: [d] d, [h] h.', 'fictioneer' ),
        sprintf( _n( '%s d', '%s d', $d, 'fictioneer' ), $d ),
        sprintf( _n( '%s h', '%s h', $h, 'fictioneer' ), $h )
      );
    } elseif ( $t / 3600 >= 1 ) {
      $h = floor( ( $t%86400 ) / 3600 );
      $m = floor( ( $t%3600 ) / 60 );

      $long = sprintf(
        _x( '%1$s, %2$s', 'Long reading time statistics: [h] hours, [m] minutes.', 'fictioneer' ),
        sprintf( _n( '%s hour', '%s hours', $h, 'fictioneer' ), $h ),
        sprintf( _n( '%s minute', '%s minutes', $m, 'fictioneer' ), $m )
      );

      $short = sprintf(
        _x( '%1$s, %2$s', 'Shortened reading time statistics: [h] h, [m] m.', 'fictioneer' ),
        sprintf( _n( '%s h', '%s h', $h, 'fictioneer' ), $h ),
        sprintf( _n( '%s m', '%s m', $m, 'fictioneer' ), $m )
      );
    } else {
      $m = floor( ( $t%3600 ) / 60 );

      $long = sprintf( _n( '%s minute', '%s minutes', $m, 'fictioneer' ), $m );

      $short = sprintf( _n( '%s m', '%s m', $m, 'fictioneer' ), $m );
    }

    return '<span class="hide-below-400">' . $long . '</span><span class="show-below-400">' . $short . '</span>';
  }
}

// =============================================================================
// GET FOOTER COPYRIGHT NOTE HTML
// =============================================================================

if ( ! function_exists( 'fictioneer_get_footer_copyright_note' ) ) {
  /**
   * Returns the HTML for the footer copyright note
   *
   * @since Fictioneer 5.0
   *
   * @param array  $args['breadcrumbs'] Breadcrumb tuples with label (0) and link (1).
   * @param string $args['post_type']   Optional. Post type of the current page.
   * @param int    $args['post_id']     Optional. Post ID of the current page.
   *
   * @return string HTML for the copyright note.
   */

  function fictioneer_get_footer_copyright_note( $args ) {
    ob_start();
		// Start HTML ---> ?>
		<span>© <?php echo date( 'Y' ); ?></span>
    <span><?php echo FICTIONEER_SITE_NAME; ?></span>
    <span>|</span>
    <a href="https://github.com/Tetrakern/fictioneer" target="_blank" rel="noreferrer"><?php echo fictioneer_get_version(); ?></a>
		<?php // <--- End HTML
		return ob_get_clean();
  }
}

// =============================================================================
// GET FICTIONEER VERSION
// =============================================================================

/**
 * Returns the current Fictioneer major theme version
 *
 * Only the major version number should be displayed to avoid telling potential
 * attackers which specific version (and weak points) to target.
 *
 * @since Fictioneer 5.0
 *
 * @return string The version.
 */

function fictioneer_get_version() {
  return sprintf(
    _x( 'Fictioneer %s', 'Fictioneer theme major version notice.', 'fictioneer' ),
    FICTIONEER_MAJOR_VERSION
  );
}

// =============================================================================
// GET BREADCRUMBS HTML
// =============================================================================

if ( ! function_exists( 'fictioneer_get_breadcrumbs' ) ) {
  /**
   * Returns the HTML for the breadcrumbs
   *
   * Renders a breadcrumbs trail supporting RDFa to be readable by search engines.
   *
   * @since Fictioneer 5.0
   *
   * @param array  $args['breadcrumbs'] Breadcrumb tuples with label (0) and link (1).
   * @param string $args['post_type']   Optional. Post type of the current page.
   * @param int    $args['post_id']     Optional. Post ID of the current page.
   * @param string $args['template']    Optional. Name of the template file.
   *
   * @return string HTML for the breadcrumbs.
   */

  function fictioneer_get_breadcrumbs( $args ) {
    // Abort if...
    if (
      ! isset( $args['breadcrumbs'] ) ||
      ! is_array( $args['breadcrumbs'] ) ||
      count( $args['breadcrumbs'] ) < 1 ||
      is_front_page()
    ) return '';

    // Setup
    $count = count( $args['breadcrumbs'] );

    // Abort if array does not have two or more items
    if ( $count < 2 ) return '';

    // Filter breadcrumbs array
    $args['breadcrumbs'] = apply_filters( 'fictioneer_filter_breadcrumbs_array', $args['breadcrumbs'], $args );

    ob_start();
		// Start HTML ---> ?>
		<ol vocab="https://schema.org/" typeof="BreadcrumbList" class="breadcrumbs">
      <?php foreach ( $args['breadcrumbs'] as $key => $value ) : ?>
        <li class="breadcrumbs__item" property="itemListElement" typeof="ListItem">
          <?php if ( $count > $key + 1 ): ?>
            <?php if ( $value[1] ): ?>
              <a property="item" typeof="WebPage" href="<?php echo $value[1]; ?>"><span property="name"><?php echo $value[0]; ?></span></a>
            <?php else : ?>
              <span property="name"><?php echo $value[0]; ?></span>
            <?php endif; ?>
          <?php else : ?>
            <span property="name"><?php echo $value[0]; ?></span>
          <?php endif; ?>
          <meta property="position" content="<?php echo $key + 1; ?>">
        </li>
      <?php endforeach; ?>
    </ol>
		<?php // <--- End HTML
		return ob_get_clean();
  }
}

// =============================================================================
// GET AUTHORS HTML
// =============================================================================

if ( ! function_exists( 'fictioneer_get_multi_author_nodes' ) ) {
  /**
   * Returns the HTML for the authors
   *
   * @since Fictioneer 5.0
   *
   * @param array $authors Array of authors to process.
   *
   * @return string HTML for the author nodes.
   */

  function fictioneer_get_multi_author_nodes( $authors ) {
    // Setup
		$author_nodes = [];

    // If no author was found...
    if ( empty( $authors ) ) {
      return __( 'Unknown', 'No story author(s) were found.', 'fictioneer' );
    }

		// The meta field returns an array of IDs
		foreach ( $authors as $author ) {
			$author_nodes[] = fictioneer_get_author_node( $author );
		}

		// Build and return HTML
		return implode( ', ', array_unique( $author_nodes ) );
  }
}

// =============================================================================
// GET STORY AUTHORS HTML
// =============================================================================

if ( ! function_exists( 'fictioneer_get_story_author_nodes' ) ) {
  /**
   * Returns the HTML for the authors on story pages
   *
   * @since Fictioneer 5.0
   *
   * @param int $story_id The story ID.
   *
   * @return string HTML for the author nodes.
   */

  function fictioneer_get_story_author_nodes( $story_id ) {
    // Setup
    $all_authors = fictioneer_get_post_author_ids( $story_id );

    // Return author nodes
    return fictioneer_get_multi_author_nodes( $all_authors );
  }
}

// =============================================================================
// GET STORY PAGE THUMBNAIL HTML
// =============================================================================

if ( ! function_exists( 'fictioneer_get_story_page_cover' ) ) {
  /**
   * Returns the HTML for thumbnail on story pages
   *
   * @since Fictioneer 5.0
   *
   * @param array $story Collection of story data.
   *
   * @return string HTML for the thumbnail.
   */

  function fictioneer_get_story_page_cover( $story ) {
		ob_start();
		// Start HTML ---> ?>
		<figure class="story__thumbnail">
			<a href="<?php the_post_thumbnail_url( 'full' ); ?>" <?php echo fictioneer_get_lightbox_attribute(); ?>>
				<?php
					the_post_thumbnail(
						array( 200, 300 ),
						array(
							'alt' => sprintf( __( '%s Cover', 'fictioneer' ), $story['title'] ),
							'class' => 'webfeedsFeaturedVisual story__thumbnail-image'
						)
					);
				?>
				<div id="ribbon-read" class="story__thumbnail-ribbon hidden">
					<div class="ribbon _read"><?php _ex( 'Read', 'Caption of the _read_ ribbon.', 'fictioneer' ) ?></div>
				</div>
			</a>
		</figure>
		<?php // <--- End HTML
		return ob_get_clean();
  }
}

// =============================================================================
// GET SUBSCRIBE BUTTONS
// =============================================================================

if ( ! function_exists( 'fictioneer_get_subscribe_options' ) ) {
  /**
   * Returns the HTML for the subscribe buttons
   *
   * @since 5.0
   *
   * @param int|null    $post_id   The post ID the buttons are for. Defaults to current.
   * @param int|null    $author_id The author ID the buttons are for. Defaults to current.
   * @param string|null $feed      RSS feed link. Default determined by post type.
   *
   * @return string HTML for subscribe buttons.
   */

  function fictioneer_get_subscribe_options( $post_id = null, $author_id = null, $feed = null ) {
    // Setup
    $post_id = $post_id ? $post_id : get_the_ID();
    $story_id = fictioneer_get_field( 'fictioneer_chapter_story', $post_id );
    $author_id = $author_id ? $author_id : get_the_author_meta( 'ID' );
    $patreon_link = fictioneer_get_field( 'fictioneer_patreon_link', $post_id );
    $kofi_link = fictioneer_get_field( 'fictioneer_kofi_link', $post_id );
    $subscribestar_link = fictioneer_get_field( 'fictioneer_subscribestar_link', $post_id );
    $output = [];
    $feed = get_option( 'fictioneer_enable_theme_rss' ) ? $feed: null;

    // Look for story support links if none provided by post
    if ( ! empty( $story_id ) ) {
      if ( empty( $patreon_link ) ) $patreon_link = fictioneer_get_field( 'fictioneer_patreon_link', $story_id );
      if ( empty( $kofi_link ) ) $kofi_link = fictioneer_get_field( 'fictioneer_kofi_link', $story_id );
      if ( empty( $subscribestar_link ) ) $subscribestar_link = fictioneer_get_field( 'fictioneer_subscribestar_link', $story_id );
    }

    // Look for author support links if none provided by post or story
    if ( empty( $patreon_link ) ) $patreon_link = get_the_author_meta( 'fictioneer_user_patreon_link', $author_id );
    if ( empty( $kofi_link ) ) $kofi_link = get_the_author_meta( 'fictioneer_user_kofi_link', $author_id );
    if ( empty( $subscribestar_link ) ) $subscribestar_link = get_the_author_meta( 'fictioneer_user_subscribestar_link', $author_id );

    // Get right RSS link if not provided
    if ( ! $feed && get_option( 'fictioneer_enable_theme_rss' ) ) {
      $feed = fictioneer_get_rss_link( get_post_type(), $post_id );
    }

    // Build
    if ( $patreon_link ) {
      ob_start();
      // Start HTML ---> ?>
      <a href="<?php echo $patreon_link; ?>" target="_blank" rel="noopener" class="_align-left">
        <i class="fa-brands fa-patreon"></i> <span><?php _e( 'Follow on Patreon', 'fictioneer' ); ?></span>
      </a>
      <?php // <--- End HTML
      $output['patreon'] = ob_get_clean();
    }

    if ( $kofi_link ) {
      ob_start();
      // Start HTML ---> ?>
      <a href="<?php echo $kofi_link; ?>" target="_blank" rel="noopener" class="_align-left">
        <?php fictioneer_icon( 'kofi' ); ?> <span><?php _e( 'Follow on Ko-Fi', 'fictioneer' ); ?></span>
      </a>
      <?php // <--- End HTML
      $output['kofi'] = ob_get_clean();
    }

    if ( $subscribestar_link ) {
      ob_start();
      // Start HTML ---> ?>
      <a href="<?php echo $subscribestar_link; ?>" target="_blank" rel="noopener" class="_align-left">
        <i class="fa-solid fa-s"></i> <span><?php _e( 'Follow on SubscribeStar', 'fictioneer' ); ?></span>
      </a>
      <?php // <--- End HTML
      $output['subscribestar'] = ob_get_clean();
    }

    if ( $feed ) {
      ob_start();
      // Start HTML ---> ?>
      <a href="https://feedly.com/i/subscription/feed/<?php echo urlencode( $feed ); ?>" target="_blank" rel="noopener" class="_align-left" aria-label="<?php esc_attr_e( 'Follow on Feedly', 'fictioneer' ); ?>">
        <?php fictioneer_icon( 'feedly' ); ?> <span><?php _e( 'Follow on Feedly', 'fictioneer' ); ?></span>
      </a>
      <?php // <--- End HTML
      $output['feedly'] = ob_get_clean();

      ob_start();
      // Start HTML ---> ?>
      <a href="https://www.inoreader.com/?add_feed=<?php echo urlencode( $feed ); ?>" target="_blank" rel="noopener" class="_align-left" aria-label="<?php esc_attr_e( 'Follow on Inoreader', 'fictioneer' ); ?>">
        <?php fictioneer_icon( 'inoreader' ); ?> <span><?php _e( 'Follow on Inoreader', 'fictioneer' ); ?></span>
      </a>
      <?php // <--- End HTML
      $output['inoreader'] = ob_get_clean();
    }

    // Apply filter
    $output = apply_filters( 'fictioneer_filter_subscribe_buttons', $output, $post_id, $author_id, $feed );

    // Return
    return implode( '', $output );
  }
}

// =============================================================================
// GET STORY BUTTONS
// =============================================================================

if ( ! function_exists( 'fictioneer_get_story_buttons' ) ) {
  /**
   * Returns the HTML for the story buttons
   *
   * @since 5.0
   *
   * @param array $args['story_data'] Collection of story data.
   * @param int   $args['story_id']   The story post ID.
   *
   * @return string HTML for the story buttons.
   */

  function fictioneer_get_story_buttons( $args ) {
    // Setup
    $story_data = $args['story_data'];
    $story_id = $args['story_id'];
    $ebook_upload = fictioneer_get_field( 'fictioneer_story_ebook_upload_one', $story_id ); // Attachment ID
    $subscribe_buttons = fictioneer_get_subscribe_options();
    $output = [];

    // Flags
    $show_epub_download = $story_data['chapter_count'] > 0 && fictioneer_get_field( 'fictioneer_story_epub_preface', $story_id ) && get_option( 'fictioneer_enable_epubs' ) && ! fictioneer_get_field( 'fictioneer_story_no_epub', $story_id );
    $can_follows = get_option( 'fictioneer_enable_follows' );
    $can_reminders = get_option( 'fictioneer_enable_reminders' );
    $show_login = get_option( 'fictioneer_enable_oauth' ) && ! is_user_logged_in();

    // Build
    if ( ! empty( $subscribe_buttons ) ) {
      ob_start();
      // Start HTML ---> ?>
      <div class="toggle-last-clicked button _secondary popup-menu-toggle _popup-right-if-last" tabindex="0" role="button" aria-label="<?php echo fcntr( 'subscribe', true ); ?>">
        <div><i class="fa-solid fa-bell"></i> <?php echo fcntr( 'subscribe' ); ?></div>
        <div class="popup-menu _bottom _center"><?php echo $subscribe_buttons; ?></div>
      </div>
      <?php // <--- End HTML
      $output['subscribe'] = ob_get_clean();
    }

    if ( $show_epub_download && ! $ebook_upload ) {
      ob_start();
      // Start HTML ---> ?>
      <a href="<?php echo esc_url( home_url( 'download-epub/' . $args['story_id'] ) ); ?>" class="button _secondary" rel="noreferrer noopener nofollow" aria-label="<?php esc_attr_e( 'Download ePUB', 'fictioneer' ) ?>" download>
        <i class="fa-solid fa-cloud-download-alt"></i>
        <span class="span-epub hide-below-640"><?php _e( 'ePUB', 'fictioneer' ) ?></span>
      </a>
      <?php // <--- End HTML
      $output['epub'] = ob_get_clean();
    } elseif ( wp_get_attachment_url( $ebook_upload ) ) {
      ob_start();
      // Start HTML ---> ?>
      <a href="<?php echo esc_url( wp_get_attachment_url( $ebook_upload ) ); ?>" class="button _secondary" rel="noreferrer noopener nofollow" aria-label="<?php esc_attr_e( 'Download eBook', 'fictioneer' ) ?>" download>
        <i class="fa-solid fa-cloud-download-alt"></i>
        <span class="span-epub hide-below-640"><?php _e( 'eBook', 'fictioneer' ) ?></span>
      </a>
      <?php // <--- End HTML
      $output['ebook'] = ob_get_clean();
    }

    if ( $can_reminders ) {
      ob_start();
      // Start HTML ---> ?>
      <button class="button _secondary button-read-later hide-if-logged-out" data-story-id="<?php echo $story_id; ?>">
        <i class="fa-solid fa-clock"></i>
        <span class="span-follow hide-below-480"><?php echo fcntr( 'read_later' ) ?></span>
      </button>
      <?php if ( $show_login ) : ?>
        <label for="modal-login-toggle" class="button _secondary button-read-later-notice hide-if-logged-in tooltipped" tabindex="0" data-tooltip="<?php esc_attr_e( 'Log in to set Reminders', 'fictioneer' ); ?>">
          <i class="fa-solid fa-clock"></i>
          <span class="span-follow hide-below-480"><?php echo fcntr( 'read_later' ) ?></span>
        </label>
      <?php endif; ?>
      <?php // <--- End HTML
      $output['reminder'] = ob_get_clean();
    }

    if ( $can_follows ) {
      ob_start();
      // Start HTML ---> ?>
      <button class="button _secondary button-follow-story hide-if-logged-out" data-story-id="<?php echo $story_id; ?>">
        <i class="fa-solid fa-star"></i>
        <span class="span-follow hide-below-400"><?php echo fcntr( 'follow' ) ?></span>
      </button>
      <?php if ( $show_login ) : ?>
        <label for="modal-login-toggle" class="button _secondary button-follow-login-notice hide-if-logged-in tooltipped" tabindex="0" data-tooltip="<?php esc_attr_e( 'Log in to Follow', 'fictioneer' ); ?>">
          <i class="fa-regular fa-star off"></i>
          <span class="span-follow hide-below-400"><?php echo fcntr( 'follow' ) ?></span>
        </label>
      <?php endif; ?>
      <?php // <--- End HTML
      $output['follow'] = ob_get_clean();
    }

    // Apply filter
    $output = apply_filters( 'fictioneer_filter_story_buttons', $output, $args );

    // Return
    return implode( '', $output );
  }
}

// =============================================================================
// GET CHAPTER AUTHORS HTML
// =============================================================================

if ( ! function_exists( 'fictioneer_get_chapter_author_nodes' ) ) {
  /**
   * Returns the HTML for the authors on chapter pages
   *
   * @since Fictioneer 5.0
   *
   * @param int $chapter_id The chapter ID.
   *
   * @return string HTML for the author nodes.
   */

  function fictioneer_get_chapter_author_nodes( $chapter_id ) {
    // Setup
    $all_authors = fictioneer_get_field( 'fictioneer_chapter_co_authors', $chapter_id ) ?? [];
		$all_authors = is_array( $all_authors ) ? $all_authors : [];
		array_unshift( $all_authors, get_post_field( 'post_author', $chapter_id ) );

    // Return author nodes
    return fictioneer_get_multi_author_nodes( $all_authors );
  }
}

// =============================================================================
// GET CHAPTER MICRO MENU HTML
// =============================================================================

if ( ! function_exists( 'fictioneer_get_chapter_micro_menu' ) ) {
  /**
   * Returns the HTML for the chapter warning section
   *
   * @since Fictioneer 5.0
   *
   * @param WP_Post|null $args['story_post']  Optional. Post object of the story.
   * @param int          $args['chapter_id']  The chapter ID.
   * @param array        $args['chapter_ids'] IDs of visible chapters in the same story or empty array.
   * @param int|boolean  $args['prev_index']  Index of previous chapter or false if outside bounds.
   * @param int|boolean  $args['next_index']  Index of next chapter or false if outside bounds.
   *
   * @return string The chapter micro menu HTML.
   */

  function fictioneer_get_chapter_micro_menu( $args ) {
    $micro_menu = [];

    if ( ! empty( $args['story_post'] ) ) {
      ob_start();
      // Start HTML ---> ?>
      <label id="micro-menu-label-open-chapter-list" for="mobile-menu-toggle" class="micro-menu__item micro-menu__chapter-list show-below-desktop" tabindex="-1">
        <i class="fa-solid fa-list"></i>
      </label>
      <?php // <--- End HTML
      $micro_menu['chapter_list'] = ob_get_clean();

      ob_start();
      // Start HTML ---> ?>
      <a href="<?php echo get_the_permalink( $args['story_post']->ID ) . '#' . $args['story_post']->ID; ?>" title="<?php echo get_the_title( $args['story_post']->ID ); ?>" class="micro-menu__item" tabindex="-1">
        <i class="fa-solid fa-book"></i>
      </a>
      <?php // <--- End HTML
      $micro_menu['story_link'] = ob_get_clean();
    }

    ob_start();
		// Start HTML ---> ?>
    <label for="modal-formatting-toggle" class="micro-menu__item micro-menu__modal-formatting" tabindex="-1">
      <?php fictioneer_icon( 'font-settings' ); ?>
    </label>
		<?php // <--- End HTML
		$micro_menu['formatting'] = ob_get_clean();

    ob_start();
		// Start HTML ---> ?>
    <button type="button" title="<?php esc_attr_e( 'Enter fullscreen', 'fictioneer' ); ?>" class="micro-menu__item micro-menu__enter-fullscreen open-fullscreen hide-on-iOS hide-on-fullscreen" tabindex="-1">
      <?php fictioneer_icon( 'expand' ); ?>
    </button>
		<?php // <--- End HTML
		$micro_menu['open_fullscreen'] = ob_get_clean();

    ob_start();
		// Start HTML ---> ?>
    <button type="button" title="<?php esc_attr_e( 'Exit fullscreen', 'fictioneer' ); ?>" class="micro-menu__item micro-menu__close-fullscreen close-fullscreen hide-on-iOS show-on-fullscreen hidden" tabindex="-1">
      <?php fictioneer_icon( 'collapse' ); ?>
    </button>
		<?php // <--- End HTML
		$micro_menu['close_fullscreen'] = ob_get_clean();

    ob_start();
		// Start HTML ---> ?>
    <button type="button" title="<?php echo fcntr( 'jump_to_bookmark', true ); ?>" class="micro-menu__item micro-menu__bookmark button--bookmark hidden" tabindex="-1">
      <i class="fa-solid fa-bookmark"></i>
    </button>
		<?php // <--- End HTML
		$micro_menu['bookmark_jump'] = ob_get_clean();

    if ( $args['prev_index'] !== false ) {
      ob_start();
      // Start HTML ---> ?>
      <a href="<?php echo get_permalink( $args['chapter_ids'][ $args['prev_index'] ] ); ?>" title="<?php echo get_the_title( $args['chapter_ids'][ $args['prev_index'] ] ); ?>" class="micro-menu__item micro-menu__previous previous" tabindex="-1">
        <i class="fa-solid fa-caret-left"></i>
      </a>
      <?php // <--- End HTML
      $micro_menu['previous'] = ob_get_clean();
    }

    ob_start();
		// Start HTML ---> ?>
    <a href="#top" data-block="center" aria-label="<?php _e( 'Scroll to top of the chapter', 'fictioneer' ); ?>" class="micro-menu__item micro-menu__up up" tabindex="-1"><i class="fa-solid fa-caret-up"></i></a>
		<?php // <--- End HTML
		$micro_menu['top'] = ob_get_clean();

    if ( $args['next_index'] ) {
      ob_start();
      // Start HTML ---> ?>
      <a href="<?php echo get_permalink( $args['chapter_ids'][ $args['next_index'] ] ); ?>" title="<?php echo get_the_title( $args['chapter_ids'][ $args['next_index'] ] ); ?>" class="micro-menu__item micro-menu__next next" tabindex="-1">
        <i class="fa-solid fa-caret-right"></i>
      </a>
      <?php // <--- End HTML
      $micro_menu['next'] = ob_get_clean();
    }

    // Filter micro menu array
    $micro_menu = apply_filters( 'fictioneer_filter_chapter_micro_menu', $micro_menu, $args );

    ob_start();
		// Start HTML ---> ?>
    <div id="micro-menu" class="micro-menu">
      <?php echo implode( '', $micro_menu ); ?>
    </div>
		<?php // <--- End HTML
		return ob_get_clean();
  }
}

// =============================================================================
// GET SIMPLE CHAPTER LIST
// =============================================================================

if ( ! function_exists( 'fictioneer_get_chapter_list_items' ) ) {
  /**
   * Returns the HTML for chapter list items with icon and link
   *
   * @since Fictioneer 5.0
   *
   * @return string List of chapter.
   */

  function fictioneer_get_chapter_list_items( $story_id, $data, $current_index ) {
    // Setup
    $hide_icons = fictioneer_get_field( 'fictioneer_story_hide_chapter_icons', $story_id ) || get_option( 'fictioneer_hide_chapter_icons' );

    $query_args = array(
      'post__in' => $data['chapter_ids'], // Only visible and published
      'post_type' => 'fcn_chapter',
      'orderby' => 'post__in',
      'posts_per_page' => -1,
      'update_post_term_cache' => false,
      'no_found_rows' => true
    );

    $chapters = new WP_Query( $query_args );

    ob_start();

    // Loop found chapters...
    if ( $chapters->have_posts() ) {
      foreach ( $chapters->posts as $post ) {
        // Prepare
        $classes = [];
        $title = trim( $post->post_title );
        $list_title = fictioneer_get_field( 'fictioneer_chapter_list_title', $post->ID );
        $text_icon = fictioneer_get_field( 'fictioneer_chapter_text_icon', $post->ID );
        $parsed_url = wp_parse_url( home_url() );
        $relative_path = isset( $parsed_url['path'] ) ? $parsed_url['path'] : '';

        // Check for empty title
        if ( empty( $title ) && empty( $list_title ) ) {
          $title = sprintf(
            _x( '%1$s — %2$s', '[Date] — [Time] if chapter title is missing.', 'fictioneer' ),
            get_the_date( '', $post->ID ),
            get_the_time( '', $post->ID )
          );
        }

        // CSS classes
        if ( $current_index == array_search( $post->ID, $data['chapter_ids'] ) ) $classes[] = 'current-chapter';
        if ( ! empty( $post->post_password ) ) $classes[] = 'has-password';

        // Start HTML ---> ?>
        <li class="<?php echo implode( ' ', $classes ); ?>">
          <a href="<?php echo "{$relative_path}?p={$post->ID}"; ?>">
            <?php if ( empty( $text_icon ) && ! $hide_icons ) : ?>
              <i class="<?php echo fictioneer_get_icon_field( 'fictioneer_chapter_icon', $post->ID ) ?>"></i>
            <?php elseif ( ! $hide_icons ) : ?>
              <span class="text-icon"><?php echo $text_icon; ?></span>
            <?php endif; ?>
            <span><?php echo wp_strip_all_tags( $list_title ?: $title ); ?></span>
          </a>
        </li>
        <?php // <--- End HTML
      }
    }

    wp_reset_postdata();

		return ob_get_clean();
  }
}

// =============================================================================
// GET RECOMMENDATION PAGE THUMBNAIL HTML
// =============================================================================

if ( ! function_exists( 'fictioneer_get_recommendation_page_cover' ) ) {
  /**
   * Returns the HTML for thumbnail on recommendation pages
   *
   * @since Fictioneer 5.0
   *
   * @param WP_Post $recommendation The post object.
   *
   * @return string HTML for the thumbnail.
   */

  function fictioneer_get_recommendation_page_cover( $recommendation ) {
		ob_start();
		// Start HTML ---> ?>
		<figure class="recommendation__thumbnail">
      <a href="<?php the_post_thumbnail_url( 'full' ); ?>" <?php echo fictioneer_get_lightbox_attribute(); ?>>
        <?php
          the_post_thumbnail(
            array( 200, 300 ),
            array(
              'alt' => sprintf( __( '%s Cover', 'fictioneer' ), fictioneer_get_safe_title( $recommendation->ID ) ),
              'class' => 'webfeedsFeaturedVisual'
            )
          );
        ?>
      </a>
    </figure>
		<?php // <--- End HTML
		return ob_get_clean();
  }
}

// =============================================================================
// GET TAXONOMY PILLS
// =============================================================================

if ( ! function_exists( 'fictioneer_get_taxonomy_pills' ) ) {
  /**
   * Returns the HTML for taxonomy tags
   *
   * @since Fictioneer 5.0
   * @link https://developer.wordpress.org/reference/classes/wp_term/
   *
   * @param array  $taxonomy_groups  Collections of WP_Term objects.
   * @param string $classes          Additional CSS classes.
   *
   * @return string HTML for the taxonomy tags.
   */

  function fictioneer_get_taxonomy_pills( $taxonomy_groups, $classes = '' ) {
    // Abort conditions
    if ( ! is_array( $taxonomy_groups ) || count( $taxonomy_groups ) < 1) return '';

		ob_start();

    // Loop over all groups...
    foreach ( $taxonomy_groups as $group ) {
      // Check for empty group
      if ( ! $group || ! is_array( $group ) || count( $group ) < 1 ) continue;

      // Process group
      foreach ( $group as $taxonomy ) {
        // Start HTML ---> ?>
        <a href="<?php echo get_tag_link( $taxonomy ); ?>" class="tag-pill _taxonomy-<?php echo str_replace( 'fcn_', '', $taxonomy->taxonomy ); ?> _taxonomy-slug-<?php echo $taxonomy->slug; ?> <?php echo $classes; ?>"><?php echo $taxonomy->name; ?></a>
        <?php // <--- End HTML
      }
    }

		return ob_get_clean();
  }
}

// =============================================================================
// GET RSS LINK
// =============================================================================

if ( ! function_exists( 'fictioneer_get_rss_link' ) ) {
  /**
   * Returns the escaped RSS link
   *
   * @since Fictioneer 5.0
   *
   * @param string|null $post_type The post type. Defaults to current post type.
   * @param int|null    $post_id   The post ID. Defaults to current post ID.
   *
   * @return string|boolean Escaped RSS url or false if no feed found.
   */

  function fictioneer_get_rss_link( $post_type = null, $post_id = null ) {
    // Abort conditions
    if ( ! get_option( 'fictioneer_enable_theme_rss' ) ) return false;

    // Setup
    $post_type = $post_type ? $post_type : get_post_type();
    $post_id = $post_id ? $post_id : get_the_ID();
    $feed = false;

    // Pick correct RSS link
    switch ( $post_type ) {
      case 'fcn_story':
        $feed = esc_url( add_query_arg( 'story_id', $post_id, home_url( 'feed/rss-chapters' ) ) );
        break;
      case 'fcn_chapter':
        $story_id = fictioneer_get_field( 'fictioneer_chapter_story', $post_id );
        if ( $story_id ) {
          $feed = esc_url( add_query_arg( 'story_id', $story_id, home_url( 'feed/rss-chapters' ) ) );
        };
        break;
      case 'post':
        $feed = esc_url( home_url( 'feed' ) );
        break;
    }

    // Apply filter
    $feed = apply_filters( 'fictioneer_filter_rss_link', $feed, $post_type, $post_id );

    // Return result
    return $feed;
  }
}

// =============================================================================
// GET USER SUBMENU ITEMS
// =============================================================================

if ( ! function_exists( 'fictioneer_user_menu_items' ) ) {
  /**
   * Returns the HTML for the user submenu in the navigation bar
   *
   * @since Fictioneer 5.0
   *
   * @return string The HTML for the user submenu.
   */

  function fictioneer_user_menu_items() {
    // Setup
    $bookmarks_link = fictioneer_get_assigned_page_link( 'fictioneer_bookmarks_page' );
    $bookshelf_link = fictioneer_get_assigned_page_link( 'fictioneer_bookshelf_page' );
    $bookshelf_title = trim( get_the_title( get_option( 'fictioneer_bookshelf_page' ) ) );
    $discord_link = get_option( 'fictioneer_discord_invite_link' );
    $can_checkmarks = get_option( 'fictioneer_enable_checkmarks' );
    $can_follows = get_option( 'fictioneer_enable_follows' );
    $can_reminders = get_option( 'fictioneer_enable_reminders' );
    $profile_link = get_edit_profile_url( 0 ); // Make sure this is always the default link
    $profile_page_id = intval( get_option( 'fictioneer_user_profile_page', -1 ) ?: -1 );
    $output = [];

    if ( ! empty( $profile_page_id ) && $profile_page_id > 0 ) {
      $profile_link = fictioneer_get_assigned_page_link( 'fictioneer_user_profile_page' );
    }

    // Build
    if ( ! empty( $profile_link ) && fictioneer_show_auth_content() ) {
      ob_start();
      // Start HTML ---> ?>
      <li class="menu-item hide-if-logged-out">
        <a href="<?php echo esc_url( $profile_link ); ?>" rel="noopener noreferrer nofollow"><?php echo fcntr( 'account' ); ?></a>
      </li>
      <?php // <--- End HTML
      $output['account'] = ob_get_clean();
    }

    ob_start();
    // Start HTML ---> ?>
    <li class="menu-item">
      <label for="modal-site-settings-toggle" tabindex="0"><?php echo fcntr( 'site_settings' ); ?></label>
    </li>
    <?php // <--- End HTML
    $output['site_settings'] = ob_get_clean();

    if ( ! empty( $discord_link ) ) {
      ob_start();
      // Start HTML ---> ?>
      <li class="menu-item">
        <a href="<?php echo esc_url( $discord_link ); ?>" rel="noopener noreferrer nofollow"><?php _e( 'Discord', 'fictioneer' ); ?></a>
      </li>
      <?php // <--- End HTML
      $output['discord'] = ob_get_clean();
    }

    if ( $bookshelf_link && fictioneer_show_auth_content() && ( $can_checkmarks || $can_follows || $can_reminders ) ) {
      ob_start();
      // Start HTML ---> ?>
      <li class="menu-item hide-if-logged-out">
        <a href="<?php echo esc_url( $bookshelf_link ); ?>" rel="noopener noreferrer nofollow"><?php echo empty( $bookshelf_title ) ? __( 'Bookshelf', 'fictioneer' ) : $bookshelf_title; ?></a>
      </li>
      <?php // <--- End HTML
      $output['bookshelf'] = ob_get_clean();
    }

    if ( $bookmarks_link && get_option( 'fictioneer_enable_bookmarks' ) ) {
      ob_start();
      // Start HTML ---> ?>
      <li class="menu-item">
        <a href="<?php echo esc_url( $bookmarks_link ); ?>" rel="noopener noreferrer nofollow"><?php echo fcntr( 'bookmarks' ) ?></a>
      </li>
      <?php // <--- End HTML
      $output['bookmarks'] = ob_get_clean();
    }

    if ( fictioneer_show_auth_content() ) {
      ob_start();
      // Start HTML ---> ?>
      <li class="menu-item hide-if-logged-out">
        <a href="<?php echo fictioneer_get_logout_url(); ?>" data-click="logout" rel="noopener noreferrer nofollow"><?php echo fcntr( 'logout' ) ?></a>
      </li>
      <?php // <--- End HTML
      $output['logout'] = ob_get_clean();
    }

    // Apply filters
    $output = apply_filters( 'fictioneer_filter_user_menu_items', $output );

    // Return
    return implode( '', $output );
  }
}

// =============================================================================
// GET POST META ITEMS
// =============================================================================

if ( ! function_exists( 'fictioneer_get_post_meta_items' ) ) {
  /**
   * Returns the HTML for the post meta row
   *
   * @since Fictioneer 5.0
   *
   * @param array $args Optional arguments.
   *
   * @return string The HTML for the post meta row.
   */

  function fictioneer_get_post_meta_items( $args = [] ) {
    // Setup
    $no_date = isset( $args['no_date'] ) ? $args['no_date'] : false;
    $no_author = isset( $args['no_author'] ) ? $args['no_author'] : false;
    $no_cat = isset( $args['no_cat'] ) ? $args['no_cat'] : false;
    $no_comments = isset( $args['no_comments'] ) ? $args['no_comments'] : false;
    $comments_number = get_comments_number( get_the_ID() );
    $output = [];

    // Date node
    if ( ! $no_date ) {
      ob_start();
      // Start HTML ---> ?>
      <time datetime="<?php the_time( 'c' ); ?>" class="post__date">
        <i class="fa-solid fa-clock" title="<?php esc_attr_e( 'Published', 'fictioneer' ) ?>"></i>
        <?php echo get_the_date(); ?>
      </time>
      <?php // <--- End HTML
      $output['date'] = ob_get_clean();
    }

    // Author node
    if ( ! $no_author && get_option( 'fictioneer_show_authors' ) ) {
      ob_start();
      // Start HTML ---> ?>
      <div class="post__author">
        <i class="fa-solid fa-circle-user"></i>
        <?php fictioneer_the_author_node( get_the_author_meta( 'ID' ) ); ?>
      </div>
      <?php // <--- End HTML
      $output['author'] = ob_get_clean();
    }

    // Category node
    if ( ! $no_cat ) {
      ob_start();
      // Start HTML ---> ?>
      <div class="post__categories">
        <i class="fa-solid fa-tags"></i>
        <?php echo the_category( ', ' ); ?>
      </div>
      <?php // <--- End HTML
      $output['category'] = ob_get_clean();
    }

    // Comments node
    if ( ! $no_comments && $comments_number > 0 ) {
      ob_start();
      // Start HTML ---> ?>
      <div class="post__comments-number">
        <i class="fa-solid fa-message"></i>
        <a href="<?php the_permalink(); ?>#comments"><?php printf( _n( '%s Comment', '%s Comments', $comments_number, 'fictioneer' ), number_format_i18n( $comments_number ) ); ?></a>
      </div>
      <?php // <--- End HTML
      $output['comments'] = ob_get_clean();
    }

    // Apply filters
    $output = apply_filters( 'fictioneer_filter_post_meta_items', $output, $args );

    // Return
    return implode( '', $output );
  }
}

// =============================================================================
// GET CARD CONTROLS
// =============================================================================

if ( ! function_exists( 'fictioneer_get_card_controls' ) ) {
  /**
   * Returns the HTML for the card controls
   *
   * @since Fictioneer 5.0
   *
   * @param int $story_id   The story ID.
   * @param int $chapter_id Optional. The chapter ID (use only for chapters).
   *
   * @return string The HTML for the card controls.
   */

  function fictioneer_get_card_controls( $story_id, $chapter_id = null ) {
    // Setup
    $can_login = is_user_logged_in() || get_option( 'fictioneer_enable_oauth' );
    $can_checkmarks = $can_login && get_option( 'fictioneer_enable_checkmarks' );
    $can_follows = $can_login && get_option( 'fictioneer_enable_follows' );
    $can_reminders = $can_login && get_option( 'fictioneer_enable_reminders' );
    $type = $chapter_id ? 'chapter' : 'story';
    $icons = [];
    $menu = [];

    // Build icons
    if ( $can_reminders ) {
      ob_start();
      // Start HTML ---> ?>
      <i
        class="fa-solid fa-clock card__reminder-icon"
        title="<?php echo fcntr( 'is_read_later' ); ?>"
        data-story-id="<?php echo $story_id; ?>"
      ></i>
      <?php // <--- End HTML
      $icons['reminder'] = ob_get_clean();
    }

    if ( $can_follows ) {
      ob_start();
      // Start HTML ---> ?>
      <i
        class="fa-solid fa-star card__followed-icon"
        title="<?php echo fcntr( 'is_followed' ); ?>"
        data-follow-id="<?php echo $story_id; ?>"
      ></i>
      <?php // <--- End HTML
      $icons['follow'] = ob_get_clean();
    }

    if ( $can_checkmarks ) {
      ob_start();
      // Start HTML ---> ?>
      <i
        class="fa-solid fa-<?php echo empty( $chapter_id ) ? 'check-double' : 'check'; ?> card__checkmark-icon"
        title="<?php echo fcntr( 'is_read' ); ?>"
        data-story-id="<?php echo $story_id; ?>"
        data-check-id="<?php echo empty( $chapter_id ) ? $story_id : $chapter_id; ?>"
      ></i>
      <?php // <--- End HTML
      $icons['checkmark'] = ob_get_clean();
    }

    if ( $can_follows ) {
      ob_start();
      // Start HTML ---> ?>
      <button class="popup-action-follow" data-click="card-toggle-follow" data-story-id="<?php echo $story_id; ?>"><?php echo fcntr( 'follow' ); ?></button>
      <button class="popup-action-unfollow" data-click="card-toggle-follow" data-story-id="<?php echo $story_id; ?>"><?php echo fcntr( 'unfollow' ); ?></button>
      <?php // <--- End HTML
      $menu['follow'] = ob_get_clean();
    }

    if ( $can_reminders ) {
      ob_start();
      // Start HTML ---> ?>
      <button class="popup-action-reminder" data-click="card-toggle-reminder" data-story-id="<?php echo $story_id; ?>"><?php echo fcntr( 'read_later' ); ?></button>
      <button class="popup-action-forget" data-click="card-toggle-reminder" data-story-id="<?php echo $story_id; ?>"><?php echo fcntr( 'forget' ); ?></button>
      <?php // <--- End HTML
      $menu['reminder'] = ob_get_clean();
    }

    if ( $can_checkmarks ) {
      ob_start();
      // Start HTML ---> ?>
      <button class="popup-action-mark-read" data-click="card-toggle-checkmarks" data-story-id="<?php echo $story_id; ?>" data-type="<?php echo $type; ?>" data-chapter-id="<?php echo $chapter_id ?: 'null'; ?>" data-mode="set"><?php echo fcntr( 'mark_read' ); ?></button>
      <button class="popup-action-mark-unread" data-click="card-toggle-checkmarks" data-story-id="<?php echo $story_id; ?>" data-type="<?php echo $type; ?>" data-chapter-id="<?php echo $chapter_id ?: 'null'; ?>" data-mode="unset"><?php echo fcntr( 'mark_unread' ); ?></button>
      <?php // <--- End HTML
      $menu['checkmark'] = ob_get_clean();
    }

    // Apply filters
    $icons = apply_filters( 'fictioneer_filter_card_control_icons', $icons, $story_id, $chapter_id );
    $menu = apply_filters( 'fictioneer_filter_card_control_menu', $menu, $story_id, $chapter_id );

    // Abort if...
    if ( count( $icons ) < 1 || count( $menu ) < 1 ) return '';

    ob_start();
    // Start HTML ---> ?>
    <div class="card__controls <?php echo count( $menu ) > 0 ? 'popup-menu-toggle toggle-last-clicked' : ''; ?>">

      <?php if ( count( $icons ) > 0 ) foreach ( $icons as $icon ) echo $icon; ?>

      <?php if ( count( $menu ) > 0 ) : ?>
        <i class="fa-solid fa-ellipsis-vertical card__popup-menu-toggle" tabindex="0"></i>
        <div class="popup-menu _bottom"><?php foreach ( $menu as $item ) echo $item; ?></div>
      <?php endif; ?>

    </div>
    <?php // <--- End HTML
    $output = ob_get_clean();

    // Return HTML
    return $output;
  }
}

// =============================================================================
// ECHO CARD
// =============================================================================

if ( ! function_exists( 'fictioneer_echo_card' ) ) {
  /**
   * Echo large card for post in loop
   *
   * @since Fictioneer 5.0
   *
   * @param array $args Optional. Card arguments.
   */

  function fictioneer_echo_card( $args = [] ) {
    // Setup
    $type = get_post_type();

    // Echo correct card by post type
    switch ( $type ) {
      case 'fcn_chapter':
        get_template_part( 'partials/_card-chapter', null, $args );
        break;
      case 'fcn_story':
        get_template_part( 'partials/_card-story', null, $args );
        break;
      case 'fcn_recommendation':
        get_template_part( 'partials/_card-recommendation', null, $args );
        break;
      case 'fcn_collection':
        get_template_part( 'partials/_card-collection', null, $args );
        break;
      case 'post':
        get_template_part( 'partials/_card-post', null, $args );
        break;
      case 'page':
        get_template_part( 'partials/_card-page', null, $args );
        break;
    }
  }
}

// =============================================================================
// GET SUPPORT LINKS
// =============================================================================

if ( ! function_exists( 'fictioneer_get_support_links' ) ) {
  /**
   * Returns support links for the post or author
   *
   * @since 5.0.19
   *
   * @param int|null $post_id   The post ID. Defaults to global post.
   * @param int|null $parent_id The parent ID. Default null.
   * @param int|null $author_id The author ID. Defaults to post author ID.
   *
   * @return array Array of support links.
   */

  function fictioneer_get_support_links( $post_id = null, $parent_id = null, $author_id = null ) {
    global $post;

    // Setup
    $post_id = $post_id ?? $post->ID;
    $author_id = $author_id ?? get_post_field( 'post_author', $post_id );
    $links = [];

    // Get story ID if chapter and parent ID not given
    if ( $parent_id === null && get_post_type( $post_id ) == 'fcn_chapter' ) {
      $parent_id = fictioneer_get_field( 'fictioneer_chapter_story', $post_id );
    }

    // Post level (e.g. chapter)
    $links['topwebfiction'] = fictioneer_get_field( 'fictioneer_story_topwebfiction_link', $post_id );
    $links['patreon'] = fictioneer_get_field( 'fictioneer_patreon_link', $post_id );
    $links['kofi'] = fictioneer_get_field( 'fictioneer_kofi_link', $post_id );
    $links['subscribestar'] = fictioneer_get_field( 'fictioneer_subscribestar_link', $post_id );
    $links['paypal'] = fictioneer_get_field( 'fictioneer_paypal_link', $post_id );
    $links['donation'] = fictioneer_get_field( 'fictioneer_donation_link', $post_id );

    // Parent level (e.g. story)
    if ( ! empty( $parent_id ) ) {
      if ( empty( $links['topwebfiction'] ) ) {
        $links['topwebfiction'] = fictioneer_get_field( 'fictioneer_story_topwebfiction_link', $parent_id );
      }
      if ( empty( $links['patreon'] ) ) {
        $links['patreon'] = fictioneer_get_field( 'fictioneer_patreon_link', $parent_id );
      }
      if ( empty( $links['kofi'] ) ) {
        $links['kofi'] = fictioneer_get_field( 'fictioneer_kofi_link', $parent_id );
      }
      if ( empty( $links['subscribestar'] ) ) {
        $links['subscribestar'] = fictioneer_get_field( 'fictioneer_subscribestar_link', $parent_id );
      }
      if ( empty( $links['paypal'] ) ) {
        $links['paypal'] = fictioneer_get_field( 'fictioneer_paypal_link', $parent_id );
      }
      if ( empty( $links['donation'] ) ) {
        $links['donation'] = fictioneer_get_field( 'fictioneer_donation_link', $parent_id );
      }
    }

    // Author level
    if ( $author_id ) {
      if ( empty( $links['patreon'] ) ) {
        $links['patreon'] = get_the_author_meta( 'fictioneer_user_patreon_link', $author_id );
      }
      if ( empty( $links['kofi'] ) ) {
        $links['kofi'] = get_the_author_meta( 'fictioneer_user_kofi_link', $author_id );
      }
      if ( empty( $links['subscribestar'] ) ) {
        $links['subscribestar'] = get_the_author_meta( 'fictioneer_user_subscribestar_link', $author_id );
      }
      if ( empty( $links['paypal'] ) ) {
        $links['paypal'] = get_the_author_meta( 'fictioneer_user_paypal_link', $author_id );
      }
      if ( empty( $links['donation'] ) ) {
        $links['donation'] = get_the_author_meta( 'fictioneer_user_donation_link', $author_id );
      }
    }

    // Remove empty strings and return
    return array_filter( $links, 'strlen' );
  }
}

// =============================================================================
// GET CHAPTER LIST SUBROW
// =============================================================================

if ( ! function_exists( 'fictioneer_get_list_chapter_meta_row' ) ) {
  /**
   * Returns HTML for list chapter meta row
   *
   * @since Fictioneer 5.1.2
   *
   * @param array $data Chapter data for the meta row.
   * @param array $args Optional arguments.
   *
   * @return string HTML of the list chapter meta row.
   */

  function fictioneer_get_list_chapter_meta_row( $data, $args = [] ) {
    // Setup
    $output = [];
    $has_grid_view = ! empty( $args['grid'] );

    // Warning
    if ( ! empty( $data['warning'] ) ) {
      ob_start();
      // Start HTML ---> ?>
      <span class="chapter-group__list-item-warning list-view" style="<?php echo $data['warning_color']; ?>"><?php
        printf( __( '<b>Warning:</b> %s', 'fictioneer' ), $data['warning'] );
      ?></span>
      <?php // <--- End HTML
      $output['warning'] = ob_get_clean();
    }

    // Password
    if ( ! empty( $data['password'] ) ) {
      ob_start();
      // Start HTML ---> ?>
      <span class="chapter-group__list-item-password list-view"><?php
        echo fcntr( 'password' );
      ?></span>
      <?php // <--- End HTML
      $output['password'] = ob_get_clean();
    }

    // Date
    ob_start();
    if ( $has_grid_view ) {
      // Start HTML ---> ?>
      <time datetime="<?php echo $data['timestamp']; ?>" class="chapter-group__list-item-date">
        <span class="list-view"><?php echo $data['list_date']; ?></span>
        <span class="grid-view"><?php echo $data['grid_date']; ?></span>
      </time>
      <?php // <--- End HTML
    } else {
      // Start HTML ---> ?>
      <time datetime="<?php echo $data['timestamp']; ?>" class="chapter-group__list-item-date"><?php
        echo $data['list_date'];
      ?></time>
      <?php // <--- End HTML
    }
    $output['date'] = ob_get_clean();

    // Words
    ob_start();
    if ( $has_grid_view ) {
      $short_words = fictioneer_shorten_number( $data['words'] );

      // Start HTML ---> ?>
      <span class="chapter-group__list-item-words" data-number-switch="<?php echo esc_attr( $short_words ); ?>"><?php
        printf( _x( '%s Words', 'Words in chapter list', 'fictioneer' ), number_format_i18n( $data['words'] ) );
      ?></span>
      <?php // <--- End HTML
    } else {
      // Start HTML ---> ?>
      <span class="chapter-group__list-item-words"><?php
        printf( _x( '%s Words', 'Words in chapter list', 'fictioneer' ), number_format_i18n( $data['words'] ) );
      ?></span>
      <?php // <--- End HTML
    }
    $output['words'] = ob_get_clean();

    // Apply filters
    $output = apply_filters( 'fictioneer_filter_list_chapter_meta_row', $output, $data, $args );

    // Implode and return
    return '<div class="chapter-group__list-item-subrow truncate _1-1">' . implode( '', $output ) . '</div>';
  }
}

// =============================================================================
// GET STORY BLOG POSTS
// =============================================================================

if ( ! function_exists( 'fictioneer_get_story_blog_posts' ) ) {
  /**
   * Returns WP_Query with blog posts associated with the story
   *
   * @since 5.4.8
   *
   * @param int $story_id  The story ID.
   *
   * @return WP_Query Queried blog posts.
   */

  function fictioneer_get_story_blog_posts( $story_id ) {
    // Setup
    $category = implode( ', ', wp_get_post_categories( $story_id ) );
    $blog_posts = new WP_Query();

    // Query by category
    $blog_category_query_args = array (
      'ignore_sticky_posts' => 1,
      'author__in'  => fictioneer_get_post_author_ids( $story_id ),
      'nopaging' => false,
      'posts_per_page' => 10,
      'cat' => empty( $category ) ? '99999999' : $category,
      'no_found_rows' => true,
      'update_post_meta_cache' => false,
      'update_post_term_cache' => false
    );

    $blog_category_posts = new WP_Query( $blog_category_query_args );

    // Query by ACF relationship
    $blog_relationship_query_args = array (
      'ignore_sticky_posts' => 1,
      'author__in'  => fictioneer_get_post_author_ids( $story_id ),
      'nopaging' => false,
      'posts_per_page' => 10,
      'no_found_rows' => true,
      'update_post_meta_cache' => false,
      'update_post_term_cache' => false,
      'meta_query' => array(
        array(
          'key' => 'fictioneer_post_story_blogs',
          'value' => '"' . $story_id . '"',
          'compare' => 'LIKE',
        )
      )
    );

    $blog_associated_posts = new WP_Query( $blog_relationship_query_args );

    // Merge results
    $merged_blog_posts = array_merge( $blog_category_posts->posts, $blog_associated_posts->posts );

    // Make sure posts are unique
    $unique_blog_posts = [];

    foreach ( $merged_blog_posts as $blog_post ) {
      if ( ! in_array( $blog_post, $unique_blog_posts ) ) {
        $unique_blog_posts[] = $blog_post;
      }
    }

    // Sort by date
    usort( $unique_blog_posts, function( $a, $b ) {
      return strcmp( $b->post_date, $a->post_date );
    });

    // Limit to 10 posts
    $unique_blog_posts = array_slice( $unique_blog_posts, 0, 10 );

    // Set up query object
    $blog_posts->posts = $unique_blog_posts;
    $blog_posts->post_count = count( $unique_blog_posts );

    // Return merged query
    return $blog_posts;
  }
}

?>
