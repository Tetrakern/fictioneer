<?php

// =============================================================================
// ADMIN INCLUDES
// =============================================================================

/**
 * Remove obsolete data and clean up when the theme is deactivated/deleted.
 */

require_once __DIR__ . '/_cleanup.php';

/**
 * Add setting page for the theme.
 */

require_once __DIR__ . '/settings/_settings.php';

/**
 * Extend user profile in admin panel.
 */

require_once __DIR__ . '/users/_admin_profile.php';

// =============================================================================
// ENQUEUE ADMIN STYLESHEETS
// =============================================================================

/**
 * Enqueues stylesheet for the admin panel
 *
 * @since 3.0
 */

function fictioneer_admin_styles() {
  wp_register_style( 'fictioneer-admin-panel', get_template_directory_uri() . '/css/admin.css' );

  if ( is_admin() ) {
    wp_enqueue_style( 'fictioneer-admin-panel' );
  }
}
add_action( 'admin_enqueue_scripts', 'fictioneer_admin_styles' );

// =============================================================================
// ENQUEUE ADMIN SCRIPTS
// =============================================================================

/**
 * Enqueue scripts for admin panel
 *
 * @since 4.0
 *
 * @param string $hook_suffix The current admin page.
 */

function fictioneer_admin_scripts( $hook_suffix ) {
  wp_enqueue_script(
    'fictioneer-utility-scripts',
    get_template_directory_uri() . '/js/utility.min.js',
    ['jquery'],
    false,
    true
  );

  wp_enqueue_script(
    'fictioneer-admin-script',
    get_template_directory_uri() . '/js/admin.min.js',
    ['jquery', 'fictioneer-utility-scripts'],
    false,
    true
  );

  wp_localize_script(
    'fictioneer-admin-script',
    'fictioneer_ajax',
    array(
      'ajax_url' => admin_url( 'admin-ajax.php' ),
      'fictioneer_nonce' => wp_create_nonce( 'fictioneer_nonce' )
    )
  );
}
add_action( 'admin_enqueue_scripts', 'fictioneer_admin_scripts' );

// =============================================================================
// CHECK FOR UPDATES
// =============================================================================

if ( ! function_exists( 'fictioneer_check_for_updates' ) ) {
  /**
   * Check Github repository for a new release
   *
   * Makes a cURL request to the Github API to check the latest release and,
   * if a newer version tag than what is installed is found, updates the
   * 'fictioneer_latest_version' option. The request can only be made once
   * every 30 minutes, otherwise the stored data is checked.
   *
   * @since Fictioneer 5.0
   *
   * @return boolean True if there is a newer version, false if not.
   */

  function fictioneer_check_for_updates() {
    global $pagenow;

    // Setup
    $last_check = (int) get_option( 'fictioneer_update_check_timestamp', 0 );
    $latest_version = get_option( 'fictioneer_latest_version', FICTIONEER_RELEASE_TAG );
    $is_updates_page = $pagenow == 'update-core.php';

    // Only call API every n seconds, otherwise check database
    if ( ! empty( $latest_version ) && time() < $last_check + FICTIONEER_UPDATE_CHECK_TIMEOUT && ! $is_updates_page ) {
      return version_compare( $latest_version, FICTIONEER_RELEASE_TAG, '>' );
    }

    // Remember this check
    update_option( 'fictioneer_update_check_timestamp', time() );

    // API request to repo
    $ch = curl_init( 'https://api.github.com/repos/Tetrakern/fictioneer/releases/latest' );
    curl_setopt( $ch, CURLOPT_HEADER, 0 );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $ch, CURLOPT_USERAGENT, 'FICTIONEER' );
    $response = curl_exec( $ch );
    curl_close( $ch );

    // Abort if request failed
    if ( empty( $response ) ) return false;

    // Decode JSON to array
    $release = json_decode( $response, true );

    // Abort if request did not return expected data
    if ( ! isset( $release['tag_name'] ) ) return false;

    // Remember latest version
    update_option( 'fictioneer_latest_version', $release['tag_name'] );

    // Compare with currently installed version
    return version_compare( $release['tag_name'], FICTIONEER_RELEASE_TAG, '>' );
  }
}

/**
 * Show notice when a newer version is available
 *
 * @since Fictioneer 5.0
 */

function fictioneer_admin_update_notice() {
  global $pagenow;

  // Setup
  $last_notice = (int) get_option( 'fictioneer_update_notice_timestamp', 0 );
  $is_updates_page = $pagenow == 'update-core.php';

  // Abort if...
  if ( ! current_user_can( 'manage_options' ) ) return;

  // Show only once every n seconds
  if ( $last_notice + FICTIONEER_UPDATE_CHECK_TIMEOUT > time() && ! $is_updates_page ) return;

  // Update?
  if ( ! fictioneer_check_for_updates() ) return;

  // Render notice
  $message = sprintf(
    __( '<strong>Fictioneer %1$s</strong> is available. Please <a href="%2$s" target="_blank">download</a> and install the latest version at your next convenience.', 'fictioneer' ),
    get_option( 'fictioneer_latest_version', FICTIONEER_RELEASE_TAG ),
    'https://github.com/Tetrakern/fictioneer/releases'
  );

  echo "<div class='notice notice-warning is-dismissible'><p>$message</p></div>";

  // Remember notice
  update_option( 'fictioneer_update_notice_timestamp', time() );
}
add_action( 'admin_notices', 'fictioneer_admin_update_notice' );

// =============================================================================
// ADD REMOVABLE QUERY ARGS
// =============================================================================

/**
 * Modifies the list of removable query arguments (admin panel only)
 *
 * @since Fictioneer 5.2.5
 *
 * @param array $args The list of removable query arguments.
 *
 * @return array The modified list of removable query arguments.
 */

function fictioneer_removable_args( $args ) {
  $args[] = 'success';
  $args[] = 'failure';
  $args[] = 'fictioneer_nonce';
  $args[] = 'fictioneer-notice';
  return $args;
}
add_filter( 'removable_query_args', 'fictioneer_removable_args' );

// =============================================================================
// LIMIT DEFAULT BLOCKS
// =============================================================================

/**
 * Limit the available default blocks
 *
 * Fictioneer is has a particular and delicate content section, built around and
 * for the main purpose of displaying prose. Other features, such as the ePUB
 * converter heavily depend on _expected_ input or may break. There are certainly
 * more possible but the initial selection has been chosen carefully.
 *
 * @since 4.0
 */

function fictioneer_allowed_block_types() {
  $allowed = array(
    // WP Core
    'core/image',
    'core/paragraph',
    'core/heading',
    'core/list',
    'core/list-item',
    'core/gallery',
    'core/quote',
    'core/pullquote',
    'core/buttons',
    'core/button',
    'core/audio',
    'core/file',
    'core/video',
    'core/table',
    'core/code',
    'core/preformatted',
    'core/html',
    'core/separator',
    'core/spacer',
    'core/more',
    'core/embed',
    'core-embed/twitter',
    'core-embed/youtube',
    'core-embed/soundcloud',
    'core-embed/spotify',
    'core-embed/vimeo',
    // Known plugins
    'cloudinary/gallery',
    'jetpack/business-hours',
    'jetpack/button',
    'jetpack/calendly',
    'jetpack/contact-form',
    'jetpack/field-text',
    'jetpack/field-name',
    'jetpack/field-email',
    'jetpack/field-url',
    'jetpack/field-date',
    'jetpack/field-telephone',
    'jetpack/field-textarea',
    'jetpack/field-checkbox',
    'jetpack/field-consent',
    'jetpack/field-checkbox-multiple',
    'jetpack/field-radio',
    'jetpack/field-select',
    'jetpack/contact-info',
    'jetpack/address',
    'jetpack/email',
    'jetpack/phone',
    'jetpack/eventbrite',
    'jetpack/gif',
    'jetpack/google-calendar',
    'jetpack/image-compare',
    'jetpack/instagram-gallery',
    'jetpack/mailchimp',
    'jetpack/map',
    'jetpack/markdown',
    'jetpack/opentable',
    'jetpack/pinterest',
    'jetpack/podcast-player',
    'jetpack/rating-star',
    'jetpack/recurring-payments',
    'jetpack/repeat-visitor',
    'jetpack/revue',
    'jetpack/send-a-message',
    'jetpack/whatsapp-button',
    'jetpack/slideshow',
    'jetpack/story',
    'jetpack/subscriptions',
    'jetpack/tiled-gallery',
    'jetpack/payments-intro',
    'jetpack/payment-buttons'
  );

  if (
    ! get_option( 'fictioneer_strip_shortcodes_for_non_administrators' ) ||
    current_user_can( 'administrator' )
  ) {
    $allowed[] = 'core/shortcode';
  }

  return $allowed;
}

if ( ! get_option( 'fictioneer_enable_all_blocks' ) ) {
  add_filter( 'allowed_block_types_all', 'fictioneer_allowed_block_types' );
}

// =============================================================================
// HIDE UPDATE NOTICE FOR NON-ADMINS
// =============================================================================

/**
 * Hide update notice for non-admins
 *
 * @since Fictioneer 5.5.3
 */

function fictioneer_limit_update_notice(){
  if ( ! current_user_can( 'manage_options' ) ) {
    remove_action( 'admin_notices', 'update_nag', 3 );
  }
}
add_action( 'admin_head', 'fictioneer_limit_update_notice' );

// =============================================================================
// REDUCE SUBSCRIBER ADMIN PROFILE
// =============================================================================

/**
 * Reduce subscriber profile in admin panel
 *
 * @since 5.0
 */

function fictioneer_reduce_subscriber_profile() {
  // Setup
  $user = wp_get_current_user();

  // Abort if administrator
  if ( fictioneer_is_admin( $user->ID ) ) return;

  // Remove application password
  add_filter( 'wp_is_application_passwords_available', '__return_false' );

  // Abort if not a subscriber (higher role)
  if ( ! in_array( 'subscriber', $user->roles ) ) return;

  // Reduce profile...
  remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
  add_filter( 'user_contactmethods', '__return_empty_array', 20 );
}

/**
 * Hide subscriber profile blocks in admin panel
 *
 * @since 5.0
 */

function fictioneer_hide_subscriber_profile_blocks() {
  // Setup
  $user = wp_get_current_user();

  // Abort if not a subscriber (higher role)
  if ( ! in_array( 'subscriber', $user->roles ) ) return;

  // Add CSS to hide blocks...
  echo '<style>.user-url-wrap, .user-description-wrap, .user-first-name-wrap, .user-last-name-wrap, .user-language-wrap, .user-admin-bar-front-wrap, .user-pass1-wrap, .user-pass2-wrap, .user-generate-reset-link-wrap, #contextual-help-link-wrap, #your-profile > h2:first-of-type { display: none; }</style>';
}

if ( get_option( 'fictioneer_admin_reduce_subscriber_profile' ) ) {
  add_action( 'admin_init', 'fictioneer_reduce_subscriber_profile' );
  add_action( 'admin_head-profile.php', 'fictioneer_hide_subscriber_profile_blocks' );
}

// =============================================================================
// LIMIT AUTHORS TO OWN POSTS/PAGES
// =============================================================================

/**
 * Limit authors to own posts and pages
 *
 * @since 5.0
 */

function fictioneer_limit_authors_to_own_posts_and_pages( $query ) {
  global $pagenow;

  // Abort conditions...
  if ( ! $query->is_admin || 'edit.php' != $pagenow ) return $query;

  // Add author to query unless user is supposed to see other posts/pages
  if ( ! current_user_can( 'edit_others_posts' ) ) {
    $query->set( 'author', get_current_user_id() );
  }

  // Return modified query
  return $query;
}
add_filter( 'pre_get_posts', 'fictioneer_limit_authors_to_own_posts_and_pages' );

// =============================================================================
// ADD OR UPDATE TERM
// =============================================================================

if ( ! function_exists( 'fictioneer_add_or_update_term' ) ) {
  /**
   * Add or update term
   *
   * @since Fictioneer 4.6
   *
   * @param string $name      Name of the term to add or update.
   * @param string $taxonomy  Taxonomy type of the term.
   * @param array  $args      Optional. An array of arguments.
   *
   * @return int|boolean The term ID or false.
   */

  function fictioneer_add_or_update_term( $name, $taxonomy, $args = [] ) {
  	$parent = $args['parent'] ?? 0;
  	$alias_of = $args['alias_of'] ?? '';
  	$description = $args['description'] ?? '';
    $result = false;

    // Does term already exist?
  	$old = get_term_by( 'name', $name, $taxonomy );

    // Get parent or create one if it does not yet exist
  	if ( $parent != 0 ) {
  		$parent = get_term_by( 'name', $parent, $taxonomy );
      $parent = $parent ? $parent->term_id : fictioneer_add_or_update_term( $args['parent'], $taxonomy );
  	}

    // Get alias or create one if it does not yet exist
  	if ( ! empty( $alias_of ) ) {
  		$alias_of = get_term_by( 'name', $alias_of, $taxonomy );

      if ( ! $alias_of ) {
        $alias_of = fictioneer_add_or_update_term( $args['alias_of'], $taxonomy );
        $alias_of = $alias_of ? get_term_by( 'term_id', $alias_of, $taxonomy ) : false;
      }

      $alias_of = $alias_of ? $alias_of->slug : '';
  	}

  	if ( ! $old ) {
      // Create term
  		$result = wp_insert_term(
  			$name,
  			$taxonomy,
  			array(
  				'alias_of' => $alias_of,
  				'parent' => $parent,
  				'description' => $description
  			)
  		);
  	} else {
      // Update term
  		$result = wp_update_term(
  			$old->term_id,
  			$taxonomy,
  			array(
  				'alias_of' => $alias_of,
  				'parent' => $parent,
  				'description' => $description
  			)
  		);
  	}

    if ( ! is_wp_error( $result ) ) {
      return $result['term_id'];
    } else {
      return false;
    }
  }
}

// =============================================================================
// CONVERT TAXONOMIES
// =============================================================================

if ( ! function_exists( 'fictioneer_convert_taxonomies' ) ) {
  /**
   * Convert taxonomies of post type from one type to another
   *
   * @since Fictioneer 4.7
   *
   * @param string  $post_type  Post type the taxonomy is attached to.
   * @param string  $target     Taxonomy to be converted to.
   * @param string  $source     Taxonomy to be converted from. Default 'post_tag'.
   * @param boolean $append     Whether to replace the post type's taxonomies with
   *                            the new ones or append them. Default false.
   * @param boolean $clean_up   Whether to delete the taxonomies from the post type
   *                            after transfer or keep them. Default false.
   */

  function fictioneer_convert_taxonomies( $post_type, $target, $source = 'post_tag', $append = false, $clean_up = false ) {
    $query_args = array(
      'post_type' => $post_type,
      'posts_per_page' => -1,
      'fields' => 'ids',
      'update_post_meta_cache' => false
    );

    $items = get_posts( $query_args );

    function terms_to_array( $n ) {
      return $n->name;
    }

    foreach ( $items as $item ) {
      $source_tax = get_the_terms( $item, $source );

      if ( ! $source_tax ) continue;

      $source_tax = array_map( 'terms_to_array', $source_tax );

      wp_set_object_terms( $item, $source_tax, $target, $append );

      if ( $clean_up ) {
        wp_delete_object_term_relationships( $item, $source );
      }
    }
  }
}

// =============================================================================
// ROLE CUSTOMIZATION ACTIONS
// =============================================================================

/**
 * Add custom moderator role
 *
 * @since Fictioneer 5.0
 */

function fictioneer_add_moderator_role() {
	return add_role(
    'fcn_moderator',
    __( 'Moderator', 'fictioneer' ),
    array(
      'read' => true,
      'edit_posts' => true,
      'edit_others_posts' => true,
      'edit_published_posts' => true,
      'moderate_comments' => true,
      'edit_comment' => true,
      'delete_posts' => false,
      'delete_others_posts' => false
    )
  );
}

/**
 * Upgrade author role with additional capabilities
 *
 * @since Fictioneer 5.0
 */

function fictioneer_upgrade_author_role() {
	$role = get_role( 'author' );
  $role->add_cap( 'delete_pages' );
  $role->add_cap( 'delete_published_pages' );
  $role->add_cap( 'edit_pages' );
  $role->add_cap( 'edit_published_pages' );
  $role->add_cap( 'publish_pages' );
}

/**
 * Reset author role to WordPress defaults
 *
 * @since Fictioneer 5.0
 */

function fictioneer_reset_author_role() {
	$role = get_role( 'author' );
  $role->remove_cap( 'delete_pages' );
  $role->remove_cap( 'delete_published_pages' );
  $role->remove_cap( 'edit_pages' );
  $role->remove_cap( 'edit_published_pages' );
  $role->remove_cap( 'publish_pages' );
}

/**
 * Upgrade contributor role with additional capabilities
 *
 * @since Fictioneer 5.0
 */

function fictioneer_upgrade_contributor_role() {
	$role = get_role( 'contributor' );
  $role->add_cap( 'delete_pages' );
  $role->add_cap( 'edit_pages' );
  $role->add_cap( 'edit_published_pages' );
}

/**
 * Reset contributor role to WordPress defaults
 *
 * @since Fictioneer 5.0
 */

function fictioneer_reset_contributor_role() {
	$role = get_role( 'contributor' );
  $role->remove_cap( 'delete_pages' );
  $role->remove_cap( 'edit_pages' );
  $role->remove_cap( 'edit_published_pages' );
}

/**
 * Limit editor role to less capabilities
 *
 * @since Fictioneer 5.0
 */

function fictioneer_limit_editor_role() {
	$role = get_role( 'editor' );
  $role->remove_cap( 'delete_pages' );
  $role->remove_cap( 'delete_published_pages' );
  $role->remove_cap( 'delete_published_posts' );
  $role->remove_cap( 'delete_others_pages' );
  $role->remove_cap( 'delete_others_posts' );
  $role->remove_cap( 'publish_pages' );
  $role->remove_cap( 'publish_posts' );
  $role->remove_cap( 'manage_categories' );
  $role->remove_cap( 'unfiltered_html' );
  $role->remove_cap( 'manage_links' );
}

/**
 * Reset editor role to WordPress defaults
 *
 * @since Fictioneer 5.0
 */

function fictioneer_reset_editor_role() {
	$role = get_role( 'editor' );
  $role->add_cap( 'delete_pages' );
  $role->add_cap( 'delete_published_pages' );
  $role->add_cap( 'delete_published_posts' );
  $role->add_cap( 'delete_others_pages' );
  $role->add_cap( 'delete_others_posts' );
  $role->add_cap( 'publish_pages' );
  $role->add_cap( 'publish_posts' );
  $role->add_cap( 'manage_categories' );
  $role->add_cap( 'unfiltered_html' );
  $role->add_cap( 'manage_links' );
}

?>
