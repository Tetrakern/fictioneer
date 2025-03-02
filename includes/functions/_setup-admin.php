<?php

// =============================================================================
// ADMIN INCLUDES
// =============================================================================

/**
 * Add setting page for the theme.
 */

require_once __DIR__ . '/settings/_settings.php';

/**
 * Add meta fields to editor.
 */

require_once __DIR__ . '/_setup-meta-fields.php';

/**
 * Extend user profile.
 */

require_once __DIR__ . '/users/_admin-profile.php';

// =============================================================================
// FIRST INSTALL
// =============================================================================

/**
 * Redirects to setup menu page after installation
 *
 * @since 5.21.0
 */

function fictioneer_first_install() {
  if ( is_admin() && isset( $_GET['activated'] ) && $GLOBALS['pagenow'] === 'themes.php' ) {
    $theme_info = fictioneer_get_theme_info();

    if ( ! ( $theme_info['setup'] ?? 0 ) ) {
      $theme_info['setup'] = 1;
      update_option( 'fictioneer_theme_info', $theme_info, 'yes' );

      wp_safe_redirect( admin_url( 'admin.php?page=fictioneer_setup' ) );
      exit;
    }
  }
}
add_action( 'after_setup_theme', 'fictioneer_first_install' );

// =============================================================================
// ENQUEUE ADMIN STYLESHEETS
// =============================================================================

/**
 * Enqueues stylesheet for the admin panel
 *
 * @since 3.0
 */

function fictioneer_admin_styles() {
  wp_register_style( 'fictioneer-admin-panel', get_template_directory_uri() . '/css/admin.css', [], FICTIONEER_VERSION );
  wp_enqueue_style( 'fictioneer-admin-panel' );
}
add_action( 'admin_enqueue_scripts', 'fictioneer_admin_styles' );

// =============================================================================
// ENQUEUE ADMIN SCRIPTS
// =============================================================================

/**
 * Enqueue scripts and styles for admin panel
 *
 * @since 4.0.0
 *
 * @param string $hook_suffix  The current admin page.
 */

function fictioneer_admin_scripts( $hook_suffix ) {
  wp_enqueue_script(
    'fictioneer-utility-scripts',
    get_template_directory_uri() . '/js/utility.min.js',
    ['jquery'],
    FICTIONEER_VERSION,
    true
  );

  if ( $hook_suffix === 'post.php' || $hook_suffix === 'post-new.php' ) {
    wp_enqueue_script(
      'fictioneer-sortablejs',
      get_template_directory_uri() . '/js/sortable.min.js',
      ['jquery', 'fictioneer-utility-scripts'],
      FICTIONEER_VERSION,
      true
    );
  }

  wp_enqueue_script(
    'fictioneer-admin-script',
    get_template_directory_uri() . '/js/admin.min.js',
    ['jquery', 'fictioneer-utility-scripts'],
    FICTIONEER_VERSION,
    true
  );

  if ( $hook_suffix === 'profile.php' || $hook_suffix === 'user-edit.php' ) {
    wp_enqueue_script(
      'fictioneer-css-skins',
      get_template_directory_uri() . '/js/css-skins.min.js',
      ['jquery', 'fictioneer-utility-scripts'],
      FICTIONEER_VERSION,
      true
    );
  }

  wp_localize_script(
    'fictioneer-admin-script',
    'fictioneer_ajax',
    array(
      'ajax_url' => admin_url( 'admin-ajax.php' ),
      'rest_url' => get_rest_url( null, 'fictioneer/v1/' ),
      'fictioneer_nonce' => wp_create_nonce( 'fictioneer_nonce' )
    )
  );

  // Admin-wide styles
  wp_enqueue_style(
    'fictioneer-admin-panel',
    get_template_directory_uri() . '/css/admin.css',
    [],
    FICTIONEER_VERSION
  );

  // Theme settings styles
  if ( strpos( $hook_suffix, 'page_fictioneer' ) !== false ) {
    wp_enqueue_style(
      'fictioneer-admin-settings',
      get_template_directory_uri() . '/css/settings.css',
      ['fictioneer-admin-panel'],
      FICTIONEER_VERSION
    );
  }
}
add_action( 'admin_enqueue_scripts', 'fictioneer_admin_scripts' );

// =============================================================================
// CHECK FOR UPDATES
// =============================================================================

/**
 * Check Github repository for a new release
 *
 * @since 5.0.0
 * @since 5.7.5 - Refactored.
 * @since 5.19.1 - Refactored again.
 *
 * @return boolean True if there is a newer version, false if not.
 */

function fictioneer_check_for_updates() {
  global $pagenow;

  // Setup
  $theme_info = fictioneer_get_theme_info();
  $last_check_timestamp = strtotime( $theme_info['last_update_check'] ?? 0 );
  $remote_version = $theme_info['last_update_version'];
  $is_updates_page = $pagenow === 'update-core.php';

  // Only call API every n seconds, otherwise check database
  if (
    ( ! $is_updates_page && current_time( 'timestamp', true ) < $last_check_timestamp + FICTIONEER_UPDATE_CHECK_TIMEOUT ) ||
    ( $_GET['action'] ?? 0 ) === 'do-plugin-upgrade'
  ) {
    if ( ! $remote_version ) {
      return false;
    }

    return version_compare( $remote_version, FICTIONEER_RELEASE_TAG, '>' );
  }

  // Remember this check
  $theme_info['last_update_check'] = current_time( 'mysql', 1 );

  // Request to repository
  $response = wp_remote_get(
    'https://api.github.com/repos/Tetrakern/fictioneer/releases/latest',
    array(
      'headers' => array(
        'User-Agent' => 'FICTIONEER',
        'Accept' => 'application/vnd.github+json',
        'X-GitHub-Api-Version' => '2022-11-28'
      )
    )
  );

  // Abort if request failed or is not a 2xx success status code
  if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) >= 300 ) {
    // Remember check to avoid continuous tries on each page load
    update_option( 'fictioneer_theme_info', $theme_info );

    return false;
  }

  // Decode JSON to array
  $release = json_decode( wp_remote_retrieve_body( $response ), true );
  $release_tag = sanitize_text_field( $release['tag_name'] ?? '' );

  // Abort if request did not return expected data
  if ( ! $release_tag ) {
    return false;
  }

  // Add to theme info
  $theme_info['last_update_version'] = $release_tag;
  $theme_info['last_update_notes'] = sanitize_textarea_field( $release['body'] ?? '' );
  $theme_info['last_update_nag'] = ''; // Reset

  if ( $release['assets'] ?? 0 ) {
    $theme_info['last_version_download_url'] = fictioneer_sanitize_url( $release['assets'][0]['browser_download_url'] ?? '' );
  } else {
    $theme_info['last_version_download_url'] = '';
  }

  // Update info in database
  update_option( 'fictioneer_theme_info', $theme_info );

  // Compare with currently installed version
  return version_compare( $release_tag, FICTIONEER_RELEASE_TAG, '>' );
}

/**
 * Show notice when a newer version is available
 *
 * @since 5.0.0
 * @since 5.19.1 - Refactored.
 */

function fictioneer_admin_update_notice() {
  // Guard
  if (
    ! current_user_can( 'install_themes' ) ||
    ( $_GET['action'] ?? 0 ) === 'upload-theme' ||
    ! fictioneer_check_for_updates()
  ) {
    return;
  }

  global $pagenow;

  // Setup
  $theme_info = fictioneer_get_theme_info();
  $last_update_nag = strtotime( $theme_info['last_update_nag'] ?? 0 );
  $is_updates_page = $pagenow == 'update-core.php';

  // Show only once every n seconds
  if ( ! $is_updates_page && current_time( 'timestamp', true ) < $last_update_nag + 60 ) {
    return;
  }

  // Render notice
  $notes = fictioneer_prepare_release_notes( $theme_info['last_update_notes'] ?? '' );

  wp_admin_notice(
    sprintf(
      __( '<strong>Fictioneer %1$s</strong> is available. Please <a href="%2$s" target="_blank">download</a> and install the latest version at your next convenience.%3$s', 'fictioneer' ),
      $theme_info['last_update_version'],
      'https://github.com/Tetrakern/fictioneer/releases',
      $notes ? '<br><details><summary>' . __( 'Release Notes', 'fictioneer' ) . '</summary>' . $notes . '</details>' : ''
    ),
    array(
      'type' => 'warning',
      'dismissible' => true,
      'additional_classes' => ['fictioneer-update-notice']
    )
  );

  // Remember notice
  $theme_info['last_update_nag'] = current_time( 'mysql', 1 );

  // Update info in database
  update_option( 'fictioneer_theme_info', $theme_info );
}
add_action( 'admin_notices', 'fictioneer_admin_update_notice' );

/**
 * Extracts the release notes from the update message
 *
 * @since 5.19.1
 *
 * @param string $message  Update message received.
 *
 * @return string The release notes or original message if not found.
 */

function fictioneer_prepare_release_notes( $message ) {
  $pos = strpos( $message, '### Release Notes' );

  if ( $pos !== false ) {
    $message = trim( substr( $message, $pos + strlen( '### Release Notes' ) ) );
    $lines = explode( "\n", $message );
    $notes = '';

    foreach ( $lines as $line ) {
      $line = trim( $line );

      if ( strpos( $line, '* ' ) === 0 ) {
        $notes .= '<li>' . substr( $line, 2 ) . '</li>';
      } else {
        $notes .= $line;
      }
    }

    if ( strpos( $notes, '<li>' ) !== false ) {
      return "<ul>{$notes}</ul>";
    } else {
      return "<p>{$notes}</p>";
    }
  }

  return "<p>{$message}</p>";
}

// =============================================================================
// ADD REMOVABLE QUERY ARGS
// =============================================================================

/**
 * Modifies the list of removable query arguments (admin panel only)
 *
 * @since 5.2.5
 *
 * @param array $args  The list of removable query arguments.
 *
 * @return array The modified list of removable query arguments.
 */

function fictioneer_removable_args( $args ) {
  $new_args = array(
    'success',
    'failure',
    'info',
    'data',
    'fictioneer_nonce',
    'fictioneer-notice'
  );

  return array_merge( $args, $new_args );
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
 * @since 4.0.0
 */

function fictioneer_allowed_block_types() {
  $allowed = array(
    // WordPress
    'core/image',
    'core/paragraph',
    'core/heading',
    'core/list',
    'core/list-item',
    'core/gallery',
    'core/quote',
    'core/pullquote',
    'core/table',
    'core/code',
    'core/preformatted',
    'core/html',
    'core/separator',
    'core/spacer',
    'core/footnotes',
    'core/details',
    'core/more',
    'core/buttons',
    'core/button',
    'core/audio',
    'core/video',
    'core/file',
    'core/embed',
    'core-embed/youtube',
    'core-embed/soundcloud',
    'core-embed/spotify',
    'core-embed/vimeo',
    'core-embed/twitter',
    // Plugins
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

  if ( current_user_can( 'fcn_shortcodes' ) || current_user_can( 'manage_options' ) ) {
    $allowed[] = 'core/shortcode';
  }

  return $allowed;
}

if ( ! get_option( 'fictioneer_enable_all_blocks' ) ) {
  add_filter( 'allowed_block_types_all', 'fictioneer_allowed_block_types' );
}

// =============================================================================
// ADD OR UPDATE TERM
// =============================================================================

if ( ! function_exists( 'fictioneer_add_or_update_term' ) ) {
  /**
   * Add or update term
   *
   * @since 4.6.0
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
   * @since 4.7.0
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
      'update_post_term_cache' => true,
      'update_post_meta_cache' => false, // Improve performance
      'no_found_rows' => true // Improve performance
    );

    $items = get_posts( $query_args );

    function terms_to_array( $n ) {
      return $n->name;
    }

    foreach ( $items as $item ) {
      $source_tax = get_the_terms( $item, $source );

      if ( ! $source_tax ) {
        continue;
      }

      $source_tax = array_map( 'terms_to_array', $source_tax );

      wp_set_object_terms( $item, $source_tax, $target, $append );

      if ( $clean_up ) {
        wp_delete_object_term_relationships( $item, $source );
      }
    }
  }
}

// =============================================================================
// THEME DEACTIVATION
// =============================================================================

/**
 * Clean up when the theme is deactivated
 *
 * Always deletes all theme-related Transients in the database. If the option to
 * delete all settings and theme mods is activated, these will be removed as
 * well but otherwise preserved.
 *
 * @since 4.7.0
 * @since 5.7.4 - Updated to use SQL queries.
 * @since 5.10.0 - Updated for font manager.
 * @since 5.11.0 - Updated for all cached files.
 *
 * @global wpdb $wpdb  WordPress database object.
 */

function fictioneer_theme_deactivation() {
  global $wpdb;

  // Purge theme caches
  fictioneer_purge_theme_caches();

  // Only continue if the user wants to delete all options/mods
  if ( get_option( 'fictioneer_delete_theme_options_on_deactivation', false ) ) {
    // Remove theme mods
    remove_theme_mods();

    // SQL to delete all options starting with "fictioneer_"
    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'fictioneer_%'" );

    // SQL to delete all user meta starting with "fictioneer_"
    $wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'fictioneer_%'" );

    // SQL to delete all comment meta starting with "fictioneer_"
    $wpdb->query( "DELETE FROM {$wpdb->commentmeta} WHERE meta_key LIKE 'fictioneer_%'" );

    // Reset user roles
    remove_role( 'fcn_moderator' );
  }
}
add_action( 'switch_theme', 'fictioneer_theme_deactivation' );

// =============================================================================
// MODALS
// =============================================================================

/**
 * Adds HTML for the sponsor modal
 *
 * @since 5.19.1
 */

function fictioneer_add_sponsor_modal() {
  // Start HTML ---> ?>
  <dialog class="fictioneer-dialog" id="fcn-sponsor-modal">
    <div class="fictioneer-dialog__header">
      <?php _e( 'Support the Development', 'fictioneer' ); ?>
    </div>
    <div class="fictioneer-dialog__content">
      <form>
        <div class="fictioneer-dialog__row"><?php
          _e( 'If you like Fictioneer, please consider supporting the development with a donation or as sponsor. You do not have to and there are no gated premium features, but any help is appreciated!', 'fictioneer' );
        ?></div>
        <div class="fictioneer-dialog__row">
          <ul class="fictioneer-dialog__link-list">
            <li><a href="https://github.com/sponsors/Tetrakern" target="_blank" rel="noopener"><i class="fa-solid fa-link"></i> <?php _e( 'Sponsor me on <strong>GitHub</strong>', 'fictioneer' ); ?></a></li>
            <li><a href="https://ko-fi.com/tetrakern" target="_blank" rel="noopener"><i class="fa-solid fa-link"></i> <?php _e( 'Buy me a coffee on <strong>Ko-fi</strong>', 'fictioneer' ); ?></a></li>
            <li><a href="https://www.patreon.com/tetrakern/" target="_blank" rel="noopener"><i class="fa-solid fa-link"></i> <?php _e( 'Support me on <strong>Patreon</strong>', 'fictioneer' ); ?></a></li>
          </ul>
        </div>
        <div class="fictioneer-dialog__actions">
          <button value="cancel" formmethod="dialog" class="button"><?php _e( 'Close', 'fictioneer' ); ?></button>
        </div>
      </form>
    </div>
  </dialog>
  <?php // <--- End HTML
}
add_action( 'admin_footer', 'fictioneer_add_sponsor_modal' );

// =============================================================================
// TRANSLATIONS
// =============================================================================

/**
 * Returns selected admin translations
 *
 * @since 5.19.1
 *
 * @param string  $key     Key for requested translation.
 * @param boolean $escape  Optional. Escape the string for safe use in
 *                         attributes. Default false.
 *
 * @return string The translation or an empty string if not found.
 */

function fcntr_admin( $key, $escape = false ) {
  static $strings = null;

  // Define default translations
  if ( $strings === null ) {
    $strings = array(
      'Super Admin' => _x( 'Super Admin', 'Role translation', 'fictioneer' ),
      'Administrator' => _x( 'Administrator', 'Role translation', 'fictioneer' ),
      'Editor' => _x( 'Editor', 'Role translation', 'fictioneer' ),
      'Moderator' => _x( 'Moderator', 'Role translation', 'fictioneer' ),
      'Author' => _x( 'Author', 'Role translation', 'fictioneer' ),
      'Contributor' => _x( 'Contributor', 'Role translation', 'fictioneer' ),
      'Subscriber' => _x( 'Subscriber', 'Role translation', 'fictioneer' ),
      'Translator' => _x( 'Translator', 'Role translation', 'fictioneer' ),
      'Developer' => _x( 'Developer', 'Role translation', 'fictioneer' ),
      'Member' => _x( 'Member', 'Role translation', 'fictioneer' ),
      'Supporter' => _x( 'Supporter', 'Role translation', 'fictioneer' ),
      'Tester' => _x( 'Tester', 'Role translation', 'fictioneer' ),
      'fcn_shortcodes' => _x( 'Shortcodes', 'Capability translation', 'fictioneer' ),
      'fcn_select_page_template' => _x( 'Select Page Templates', 'Capability translation', 'fictioneer' ),
      'fcn_custom_page_css' => _x( 'Custom Page CSS', 'Capability translation', 'fictioneer' ),
      'fcn_custom_epub_css' => _x( 'Custom ePUB CSS', 'Capability translation', 'fictioneer' ),
      'fcn_custom_epub_upload' => _x( 'Custom ePUB Upload', 'Capability translation', 'fictioneer' ),
      'fcn_custom_page_header' => _x( 'Custom Page Header', 'Capability translation', 'fictioneer' ),
      'fcn_seo_meta' => _x( 'SEO Meta', 'Capability translation', 'fictioneer' ),
      'fcn_make_sticky' => _x( 'Make Sticky', 'Capability translation', 'fictioneer' ),
      'fcn_edit_permalink' => _x( 'Edit Permalink', 'Capability translation', 'fictioneer' ),
      'fcn_all_blocks' => _x( 'All Blocks', 'Capability translation', 'fictioneer' ),
      'fcn_story_pages' => _x( 'Story Pages', 'Capability translation', 'fictioneer' ),
      'fcn_edit_date' => _x( 'Edit Date', 'Capability translation', 'fictioneer' ),
      'fcn_assign_patreon_tiers' => _x( 'Assign Patreon Tiers', 'Capability translation', 'fictioneer' ),
      'fcn_expire_passwords' => _x( 'Expire Passwords', 'Capability translation', 'fictioneer' ),
      'fcn_crosspost' => _x( 'Crosspost', 'Capability translation', 'fictioneer' ),
      'fcn_reduced_profile' => _x( 'Reduced Profile', 'Capability translation', 'fictioneer' ),
      'fcn_only_moderate_comments' => _x( 'Only Moderate Comments', 'Capability translation', 'fictioneer' ),
      'fcn_upload_limit' => _x( 'Upload Limit', 'Capability translation', 'fictioneer' ),
      'fcn_upload_restrictions' => _x( 'Upload Restrictions', 'Capability translation', 'fictioneer' ),
      'fcn_classic_editor' => _x( 'Classic Editor', 'Capability translation', 'fictioneer' ),
      'fcn_adminbar_access' => _x( 'Adminbar Access', 'Capability translation', 'fictioneer' ),
      'fcn_admin_panel_access' => _x( 'Admin Panel Access', 'Capability translation', 'fictioneer' ),
      'fcn_dashboard_access' => _x( 'Dashboard Access', 'Capability translation', 'fictioneer' ),
      'fcn_show_badge' => _x( 'Show Badge', 'Capability translation', 'fictioneer' ),
      'upload_files' => _x( 'Upload Files', 'Capability translation', 'fictioneer' ),
      'edit_files' => _x( 'Edit Files', 'Capability translation', 'fictioneer' ),
      'fcn_moderate_post_comments' => _x( 'Moderate Post Comments', 'Capability translation', 'fictioneer' ),
      'fcn_allow_self_delete' => _x( 'Allow Self Delete', 'Capability translation', 'fictioneer' ),
      'read' => _x( 'Read', 'Capability translation', 'fictioneer' ),
      'moderate_comments' => _x( 'Moderate Comments', 'Capability translation', 'fictioneer' ),
      'fcn_privacy_clearance' => _x( 'Privacy Clearance', 'Capability translation', 'fictioneer' ),
      'fcn_read_others_files' => _x( 'Read Others Files', 'Capability translation', 'fictioneer' ),
      'fcn_edit_others_files' => _x( 'Edit Others Files', 'Capability translation', 'fictioneer' ),
      'fcn_delete_others_files' => _x( 'Delete Others Files', 'Capability translation', 'fictioneer' ),
      'list_users' => _x( 'List Users', 'Capability translation', 'fictioneer' ),
      'create_users' => _x( 'Create Users', 'Capability translation', 'fictioneer' ),
      'edit_users' => _x( 'Edit Users', 'Capability translation', 'fictioneer' ),
      'remove_users' => _x( 'Remove Users', 'Capability translation', 'fictioneer' ),
      'fcn_unlock_posts' => _x( 'Unlock Posts', 'Capability translation', 'fictioneer' ),
      'switch_themes' => _x( 'Switch Themes', 'Capability translation', 'fictioneer' ),
      'edit_theme_options' => _x( 'Edit Theme Options', 'Capability translation', 'fictioneer' ),
      'edit_themes' => _x( 'Edit Themes', 'Capability translation', 'fictioneer' ),
      'unfiltered_html' => _x( 'Unfiltered HTML', 'Capability translation', 'fictioneer' ),
      'manage_categories' => _x( 'Manage Categories', 'Capability translation', 'fictioneer' ),
      'assign_categories' => _x( 'Assign Categories', 'Capability translation', 'fictioneer' ),
      'edit_categories' => _x( 'Edit Categories', 'Capability translation', 'fictioneer' ),
      'delete_categories' => _x( 'Delete Categories', 'Capability translation', 'fictioneer' ),
      'manage_post_tags' => _x( 'Manage Post Tags', 'Capability translation', 'fictioneer' ),
      'assign_post_tags' => _x( 'Assign Post Tags', 'Capability translation', 'fictioneer' ),
      'edit_post_tags' => _x( 'Edit Post Tags', 'Capability translation', 'fictioneer' ),
      'delete_post_tags' => _x( 'Delete Post Tags', 'Capability translation', 'fictioneer' ),
      'manage_fcn_genres' => _x( 'Manage Genres', 'Capability translation', 'fictioneer' ),
      'assign_fcn_genres' => _x( 'Assign Genres', 'Capability translation', 'fictioneer' ),
      'edit_fcn_genres' => _x( 'Edit Genres', 'Capability translation', 'fictioneer' ),
      'delete_fcn_genres' => _x( 'Delete Genres', 'Capability translation', 'fictioneer' ),
      'manage_fcn_fandoms' => _x( 'Manage Fandoms', 'Capability translation', 'fictioneer' ),
      'assign_fcn_fandoms' => _x( 'Assign Fandoms', 'Capability translation', 'fictioneer' ),
      'edit_fcn_fandoms' => _x( 'Edit Fandoms', 'Capability translation', 'fictioneer' ),
      'delete_fcn_fandoms' => _x( 'Delete Fandoms', 'Capability translation', 'fictioneer' ),
      'manage_fcn_characters' => _x( 'Manage Characters', 'Capability translation', 'fictioneer' ),
      'assign_fcn_characters' => _x( 'Assign Characters', 'Capability translation', 'fictioneer' ),
      'edit_fcn_characters' => _x( 'Edit Characters', 'Capability translation', 'fictioneer' ),
      'delete_fcn_characters' => _x( 'Delete Characters', 'Capability translation', 'fictioneer' ),
      'manage_fcn_content_warnings' => _x( 'Manage Content Warnings', 'Capability translation', 'fictioneer' ),
      'assign_fcn_content_warnings' => _x( 'Assign Content Warnings', 'Capability translation', 'fictioneer' ),
      'edit_fcn_content_warnings' => _x( 'Edit Content Warnings', 'Capability translation', 'fictioneer' ),
      'delete_fcn_content_warnings' => _x( 'Delete Content Warnings', 'Capability translation', 'fictioneer' ),
      'publish_posts' => _x( 'Publish Posts', 'Capability translation', 'fictioneer' ),
      'edit_posts' => _x( 'Edit Posts', 'Capability translation', 'fictioneer' ),
      'delete_posts' => _x( 'Delete Posts', 'Capability translation', 'fictioneer' ),
      'edit_published_posts' => _x( 'Edit Published Posts', 'Capability translation', 'fictioneer' ),
      'delete_published_posts' => _x( 'Delete Published Posts', 'Capability translation', 'fictioneer' ),
      'edit_others_posts' => _x( 'Edit Others Posts', 'Capability translation', 'fictioneer' ),
      'delete_others_posts' => _x( 'Delete Others Posts', 'Capability translation', 'fictioneer' ),
      'read_private_posts' => _x( 'Read Private Posts', 'Capability translation', 'fictioneer' ),
      'edit_private_posts' => _x( 'Edit Private Posts', 'Capability translation', 'fictioneer' ),
      'delete_private_posts' => _x( 'Delete Private Posts', 'Capability translation', 'fictioneer' ),
      'fcn_ignore_post_passwords' => _x( 'Ignore Post Passwords', 'Capability translation', 'fictioneer' ),
      'publish_pages' => _x( 'Publish Pages', 'Capability translation', 'fictioneer' ),
      'edit_pages' => _x( 'Edit Pages', 'Capability translation', 'fictioneer' ),
      'delete_pages' => _x( 'Delete Pages', 'Capability translation', 'fictioneer' ),
      'edit_published_pages' => _x( 'Edit Published Pages', 'Capability translation', 'fictioneer' ),
      'delete_published_pages' => _x( 'Delete Published Pages', 'Capability translation', 'fictioneer' ),
      'edit_others_pages' => _x( 'Edit Others Pages', 'Capability translation', 'fictioneer' ),
      'delete_others_pages' => _x( 'Delete Others Pages', 'Capability translation', 'fictioneer' ),
      'read_private_pages' => _x( 'Read Private Pages', 'Capability translation', 'fictioneer' ),
      'edit_private_pages' => _x( 'Edit Private Pages', 'Capability translation', 'fictioneer' ),
      'delete_private_pages' => _x( 'Delete Private Pages', 'Capability translation', 'fictioneer' ),
      'fcn_ignore_page_passwords' => _x( 'Ignore Page Passwords', 'Capability translation', 'fictioneer' ),
      'publish_fcn_stories' => _x( 'Publish Stories', 'Capability translation', 'fictioneer' ),
      'edit_fcn_stories' => _x( 'Edit Stories', 'Capability translation', 'fictioneer' ),
      'delete_fcn_stories' => _x( 'Delete Stories', 'Capability translation', 'fictioneer' ),
      'edit_published_fcn_stories' => _x( 'Edit Published Stories', 'Capability translation', 'fictioneer' ),
      'delete_published_fcn_stories' => _x( 'Delete Published Stories', 'Capability translation', 'fictioneer' ),
      'edit_others_fcn_stories' => _x( 'Edit Others Stories', 'Capability translation', 'fictioneer' ),
      'delete_others_fcn_stories' => _x( 'Delete Others Stories', 'Capability translation', 'fictioneer' ),
      'read_private_fcn_stories' => _x( 'Read Private Stories', 'Capability translation', 'fictioneer' ),
      'edit_private_fcn_stories' => _x( 'Edit Private Stories', 'Capability translation', 'fictioneer' ),
      'delete_private_fcn_stories' => _x( 'Delete Private Stories', 'Capability translation', 'fictioneer' ),
      'fcn_ignore_fcn_story_passwords' => _x( 'Ignore Story Passwords', 'Capability translation', 'fictioneer' ),
      'publish_fcn_chapters' => _x( 'Publish Chapters', 'Capability translation', 'fictioneer' ),
      'edit_fcn_chapters' => _x( 'Edit Chapters', 'Capability translation', 'fictioneer' ),
      'delete_fcn_chapters' => _x( 'Delete Chapters', 'Capability translation', 'fictioneer' ),
      'edit_published_fcn_chapters' => _x( 'Edit Published Chapters', 'Capability translation', 'fictioneer' ),
      'delete_published_fcn_chapters' => _x( 'Delete Published Chapters', 'Capability translation', 'fictioneer' ),
      'edit_others_fcn_chapters' => _x( 'Edit Others Chapters', 'Capability translation', 'fictioneer' ),
      'delete_others_fcn_chapters' => _x( 'Delete Others Chapters', 'Capability translation', 'fictioneer' ),
      'read_private_fcn_chapters' => _x( 'Read Private Chapters', 'Capability translation', 'fictioneer' ),
      'edit_private_fcn_chapters' => _x( 'Edit Private Chapters', 'Capability translation', 'fictioneer' ),
      'delete_private_fcn_chapters' => _x( 'Delete Private Chapters', 'Capability translation', 'fictioneer' ),
      'fcn_ignore_fcn_chapter_passwords' => _x( 'Ignore Chapter Passwords', 'Capability translation', 'fictioneer' ),
      'publish_fcn_collections' => _x( 'Publish Collections', 'Capability translation', 'fictioneer' ),
      'edit_fcn_collections' => _x( 'Edit Collections', 'Capability translation', 'fictioneer' ),
      'delete_fcn_collections' => _x( 'Delete Collections', 'Capability translation', 'fictioneer' ),
      'edit_published_fcn_collections' => _x( 'Edit Published Collections', 'Capability translation', 'fictioneer' ),
      'delete_published_fcn_collections' => _x( 'Delete Published Collections', 'Capability translation', 'fictioneer' ),
      'edit_others_fcn_collections' => _x( 'Edit Others Collections', 'Capability translation', 'fictioneer' ),
      'delete_others_fcn_collections' => _x( 'Delete Others Collections', 'Capability translation', 'fictioneer' ),
      'read_private_fcn_collections' => _x( 'Read Private Collections', 'Capability translation', 'fictioneer' ),
      'edit_private_fcn_collections' => _x( 'Edit Private Collections', 'Capability translation', 'fictioneer' ),
      'delete_private_fcn_collections' => _x( 'Delete Private Collections', 'Capability translation', 'fictioneer' ),
      'fcn_ignore_fcn_collection_passwords' => _x( 'Ignore Collection Passwords', 'Capability translation', 'fictioneer' ),
      'publish_fcn_recommendations' => _x( 'Publish Recommendations', 'Capability translation', 'fictioneer' ),
      'edit_fcn_recommendations' => _x( 'Edit Recommendations', 'Capability translation', 'fictioneer' ),
      'delete_fcn_recommendations' => _x( 'Delete Recommendations', 'Capability translation', 'fictioneer' ),
      'edit_published_fcn_recommendations' => _x( 'Edit Published Recommendations', 'Capability translation', 'fictioneer' ),
      'delete_published_fcn_recommendations' => _x( 'Delete Published Recommendations', 'Capability translation', 'fictioneer' ),
      'edit_others_fcn_recommendations' => _x( 'Edit Others Recommendations', 'Capability translation', 'fictioneer' ),
      'delete_others_fcn_recommendations' => _x( 'Delete Others Recommendations', 'Capability translation', 'fictioneer' ),
      'read_private_fcn_recommendations' => _x( 'Read Private Recommendations', 'Capability translation', 'fictioneer' ),
      'edit_private_fcn_recommendations' => _x( 'Edit Private Recommendations', 'Capability translation', 'fictioneer' ),
      'delete_private_fcn_recommendations' => _x( 'Delete Private Recommendations', 'Capability translation', 'fictioneer' )
    );
  }

  // Return requested translation if defined...
  if ( array_key_exists( $key, $strings ) ) {
    return $escape ? esc_attr( $strings[ $key ] ) : $strings[ $key ];
  }

  // ... otherwise return empty string
  return '';
}

// =============================================================================
// APPEND MISSING META FIELDS
// =============================================================================

/**
 * Append a missing meta field to selected posts
 *
 * @since 5.7.4
 * @global wpdb $wpdb  WordPress database object.
 *
 * @param string $post_type   The post type to append to.
 * @param string $meta_key    The meta key to append.
 * @param mixed  $meta_value  The value to assign.
 */

function fictioneer_append_meta_fields( $post_type, $meta_key, $meta_value ) {
  global $wpdb;

  // Setup
  $values = [];

  // Get posts with missing meta field
  $posts = $wpdb->get_col("
    SELECT p.ID
    FROM {$wpdb->posts} p
    LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '{$meta_key}'
    WHERE p.post_type = '{$post_type}' AND pm.meta_id IS NULL
  ");

  // Prepare values
  foreach ( $posts as $post_id ) {
    $values[] = $wpdb->prepare( "(%d, %s, %d)", $post_id, $meta_key, $meta_value );
  }

  $chunks = array_chunk( $values, 1000 );

  // Query
  foreach ( $chunks as $chunk ) {
    $values_sql = implode( ', ', $chunk );

    if ( ! empty( $values_sql ) ) {
      $wpdb->query("
        INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value)
        VALUES {$values_sql};
      ");
    }
  }
}

// =============================================================================
// MU-PLUGINS
// =============================================================================

/**
 * Initializes the mu-plugins directory in /wp-content
 *
 * @since 5.20.1
 */

function fictioneer_initialize_mu_plugins() {
  // Make sure directory exists
  if ( ! is_dir( WPMU_PLUGIN_DIR ) ) {
    mkdir( WPMU_PLUGIN_DIR, 0755, true );
  }
}

/**
 * Returns data about theme's MU-Plugins
 *
 * @since 5.20.1
 *
 * @return array Array of plugin data.
 */

function fictioneer_get_mu_plugin_data() {
  // Initialize if necessary
  fictioneer_initialize_mu_plugins();

  // Setup
  $mu_plugins = get_mu_plugins();
  $data = array(
    'fictioneer_fast_requests' => array(
      'key' => 'fictioneer_fast_requests',
      'filename' => 'fictioneer_001_fast_requests.php',
      'name' => _x( 'Fictioneer Fast Requests', 'Theme mu-plugin.', 'fictioneer' ),
      'description' => _x( 'Disables plugins for selected actions to accelerate dynamic requests, such as AJAX comments. If you have many plugins installed, this can significantly reduce loading times.', 'Theme mu-plugin.', 'fictioneer' ),
      'version' => '1.1.0',
      'update' => false,
      'active' => false
    ),
    'fictioneer_elementor_control' => array(
      'key' => 'fictioneer_elementor_control',
      'filename' => 'fictioneer_002_elementor_control.php',
      'name' => _x( 'Fictioneer Elementor Control', 'Theme mu-plugin.', 'fictioneer' ),
      'description' => _x( 'Disables the Elementor plugin on all pages except those with a Canvas page template. Since Elementor consumes a lot of server resources, limiting it to actual use cases is sensible. However, this makes the plugin unavailable anywhere else on the frontend.', 'Theme mu-plugin.', 'fictioneer' ),
      'version' => '1.0.1',
      'update' => false,
      'active' => false
    )
  );

  // Active?
  foreach ( $mu_plugins as $plugin_data ) {
    if ( $plugin_data['Name'] === 'Fictioneer Fast Requests' ) {
      $data['fictioneer_fast_requests']['active'] = true;

      // Check version
      if ( version_compare( $data['fictioneer_fast_requests']['version'], $plugin_data['Version'], '>' ) ) {
        $data['fictioneer_fast_requests']['update'] = true;
      }
    }

    if ( $plugin_data['Name'] === 'Fictioneer Elementor Control' ) {
      $data['fictioneer_elementor_control']['active'] = true;

      // Check version
      if ( version_compare( $data['fictioneer_elementor_control']['version'], $plugin_data['Version'], '>' ) ) {
        $data['fictioneer_elementor_control']['update'] = true;
      }
    }
  }

  // Return
  return $data;
}

// =============================================================================
// LOOK FOR ISSUES
// =============================================================================

/**
 * Looks for issues with the theme
 *
 * @since 5.23.0
 *
 * @return array Array of found issues or empty if none found.
 */

function fictioneer_look_for_issues() {
  global $wpdb;

  // Setup
  $cache_dir = fictioneer_get_theme_cache_dir( 'looking_for_issues' );
  $dynamic_scripts_path = $cache_dir . '/dynamic-scripts.js';
  $bundled_fonts_path = $cache_dir . '/bundled-fonts.css';
  $customize_css_path = $cache_dir . '/customize.css';
  $issues = [];

  // Cache directory set up?
  if ( ! is_dir( $cache_dir ) ) {
    $issues[] = sprintf(
      __( '<code>%s</code> directory could not be found or created.', 'fictioneer' ),
      $cache_dir
    );
  } else {
    $permissions = substr( sprintf( '%o', fileperms( $cache_dir ) ), -4 );

    if ( $permissions !== '0755' ) {
      $issues[] = sprintf(
        __( '<code>%s</code> directory permissions are not 755.', 'fictioneer' ),
        $cache_dir
      );
    }
  }

  // Call build methods if necessary
  if ( ! file_exists( $customize_css_path ) ) {
    fictioneer_build_customize_css();
  }

  if ( ! file_exists( $dynamic_scripts_path ) ) {
    fictioneer_build_dynamic_scripts();
  }

  // Dynamic scripts set up?
  if ( ! file_exists( $dynamic_scripts_path ) ) {
    $issues[] = sprintf(
      __( '<strong>%s</strong> could not be found or created in the <code>%s</code> directory.', 'fictioneer' ),
      'dynamic-scripts.js',
      $cache_dir
    );
  } else {
    $permissions = substr( sprintf( '%o', fileperms( $dynamic_scripts_path ) ), -4 );

    if ( $permissions !== '0644' ) {
      $issues[] = sprintf(
        __( '<strong>%s</strong> file permissions are not 644.', 'fictioneer' ),
        'dynamic-scripts.js'
      );
    }
  }

  // Bundled fonts set up?
  if ( ! file_exists( $bundled_fonts_path ) ) {
    $issues[] = sprintf(
      __( '<strong>%s</strong> could not be found or created in the <code>%s</code> directory.', 'fictioneer' ),
      'bundled-fonts.css',
      $cache_dir
    );
  } else {
    $permissions = substr( sprintf( '%o', fileperms( $bundled_fonts_path ) ), -4 );

    if ( $permissions !== '0644' ) {
      $issues[] = sprintf(
        __( '<strong>%s</strong> file permissions are not 644.', 'fictioneer' ),
        'bundled-fonts.css'
      );
    }
  }

  // Customize CSS set up?
  if ( ! file_exists( $customize_css_path ) ) {
    $issues[] = sprintf(
      __( '<strong>%s</strong> could not be found or created in the <code>%s</code> directory.', 'fictioneer' ),
      'customize.css',
      $cache_dir
    );
  } else {
    $permissions = substr( sprintf( '%o', fileperms( $customize_css_path ) ), -4 );

    if ( $permissions !== '0644' ) {
      $issues[] = sprintf(
        __( '<strong>%s</strong> file permissions are not 644.', 'fictioneer' ),
        'customize.css'
      );
    }
  }

  // Theme folder correct?
  $fictioneer_theme = wp_get_theme();
  $fictioneer_theme = $fictioneer_theme->parent() ? $fictioneer_theme->parent() : $fictioneer_theme;

  if ( basename( $fictioneer_theme->get_stylesheet_directory() ) !== 'fictioneer' ) {
    $issues[] = __( 'The main theme directory is not "fictioneer" and needs to be renamed to match internal paths.', 'fictioneer' );
  }

  // Is mbstring enabled?
  if ( ! extension_loaded( 'mbstring' ) ) {
    $issues[] = __( 'The <strong>mbstring</strong> PHP extension is not enabled.', 'fictioneer' );
  }

  // HTTPS?
  $site_url = get_option( 'siteurl' );
  $home_url = get_option( 'home' );

  if ( strpos( $site_url, 'https://' ) !== 0 && ! fictioneer_is_local_environment() ) {
    $issues[] = sprintf(
      __( 'Your <strong>Site URL</strong> does not start with <code>%s</code>. This can lead to security issues, browsers refusing to access the page, and required scripts not loading.', 'fictioneer' ),
      'https://'
    );
  }

  if ( strpos( $home_url, 'https://' ) !== 0 && ! fictioneer_is_local_environment() ) {
    $issues[] = sprintf(
      __( 'Your <strong>Home URL</strong> does not start with <code>%s</code>. This can lead to security issues, browsers refusing to access the page, and required scripts not loading.', 'fictioneer' ),
      'https://'
    );
  }

  // Posts per page
  if ( get_option( 'posts_per_page' ) >= 15 ) {
    $issues[] = sprintf(
      __( 'You currently show <strong>%s posts per page</strong>. This high number can cause index and list pages to be slow or crash, depending on the number of chapters per story.', 'fictioneer' ),
      get_option( 'posts_per_page' )
    );
  }

  // Revisions
  if ( defined( 'WP_POST_REVISIONS' ) && WP_POST_REVISIONS === true ) {
    $issues[] = sprintf(
      __( 'You currently store <strong>unlimited revisions per post</strong>. This can bloat your database and slow down your site. Better limit or turn them off by adding <code>%s</code> to your wp-config.php. This will not clean up already stored revisions, use a plugin for that.', 'fictioneer' ),
      "define( 'WP_POST_REVISIONS', false )"
    );
  }

  $revision_count = $wpdb->get_var("
    SELECT COUNT(*)
    FROM $wpdb->posts
    WHERE post_type = 'revision'
  ");

  if ( $revision_count > 500 ) {
    $issues[] = sprintf(
      __( 'You currently have <strong>%s revisions stored</strong> in your database. This is a lot ot bloat that can slow down your site if you do not really need them. Use a plugin to clean them up.', 'fictioneer' ),
      $revision_count
    );
  }

  // Orphaned post meta (currently disabled because potentially too slow)
  // $orphaned_post_meta_count = $wpdb->get_var("
  //   SELECT COUNT(*)
  //   FROM $wpdb->postmeta pm
  //   LEFT JOIN $wpdb->posts p ON pm.post_id = p.ID
  //   WHERE p.ID IS NULL
  // ");

  // if ( $orphaned_post_meta_count > 500 ) {
  //   $issues[] = sprintf(
  //     __( 'You currently have <strong>%s orphaned post meta fields stored</strong> in your database. This is obsolete data that can slow down your site. Optimize your database under <strong>Fictioneer > Tools</strong> or use a plugin to clean them up.', 'fictioneer' ),
  //     $orphaned_post_meta_count
  //   );
  // }

  // Results
  return $issues;
}

// =============================================================================
// AJAX REQUESTS
// > Return early if no AJAX functions are required.
// =============================================================================

if ( ! wp_doing_ajax() ) {
  return;
}

// =============================================================================
// AJAX: GET STORY CHAPTER GROUP OPTIONS
// =============================================================================

/**
 * AJAX: Get chapter group options for story
 *
 * @since 5.7.4
 */

function fictioneer_ajax_get_chapter_group_options() {
  // Validate
  $user = fictioneer_get_validated_ajax_user( 'nonce', 'fictioneer_nonce' );
  $story_id = isset( $_GET['story_id'] ) ? fictioneer_validate_id( $_GET['story_id'], 'fcn_story' ) : null;

  if ( ! is_admin() || ! wp_doing_ajax() ) {
    wp_send_json_error( array( 'error' => 'Request did not pass validation.' ) );
  }

  if ( ! $user || ! current_user_can( 'edit_fcn_chapters' ) ) {
    wp_send_json_error( array( 'error' => 'User did not pass validation.' ) );
  }

  if ( ! $story_id ) {
    wp_send_json_error( array( 'error' => 'Story ID did not pass validation.' ) );
  }

  // Setup
  global $wpdb;

  $story = fictioneer_get_story_data( $story_id, false );
  $groups = [];

  // Query groups
  if ( $story && ! empty( $story['chapter_ids'] ) ) {
    $post_ids_format = implode( ', ', array_fill( 0, count( $story['chapter_ids'] ), '%d' ) );

    $sql = $wpdb->prepare(
      "SELECT post_id, meta_value
      FROM $wpdb->postmeta
      WHERE meta_key = 'fictioneer_chapter_group'
      AND meta_value != ''
      AND post_id IN ($post_ids_format)",
      $story['chapter_ids']
    );

    $results = $wpdb->get_results( $sql );

    foreach ( $results as $result ) {
      if ( $result->meta_value ) {
        $groups[] = $result->meta_value;
      }
    }

    $groups = array_unique( $groups );
  }

  // Prepare HTML
  $html = '<option value="0">' . __( '— Select Group —', 'fictioneer' ) . '</option>';
  $html .= '<option value="-1">' . __( '— New Group —', 'fictioneer' ) . '</option>';

  if ( ! empty( $groups ) ) {
    foreach ( $groups as $group ) {
      $html .= "<option value='{$group}'>{$group}</option>";
    }
  }

  // Send
  if ( empty( $html ) ) {
    wp_send_json_error( array( 'failure' => __( 'No groups found.', 'fictioneer' ) ) );
  } else {
    wp_send_json_success( array( 'html' => $html ) );
  }
}
add_action( 'wp_ajax_fictioneer_ajax_get_chapter_group_options', 'fictioneer_ajax_get_chapter_group_options' );

// =============================================================================
// AJAX: RESET THEME COLORS
// =============================================================================

/**
 * AJAX: Reset custom theme colors
 *
 * @since 5.12.0
 */

function fictioneer_ajax_reset_theme_colors() {
  // Validate
  check_ajax_referer( 'fictioneer-reset-colors', 'fictioneer_nonce' );

  if ( ! current_user_can( 'edit_theme_options' ) ) {
    wp_send_json_error( array( 'failure' => __( 'Error: Insufficient permissions.', 'fictioneer' ) ) );
  }

  // Setup
  $mods = get_theme_mods();
  $theme = get_option( 'stylesheet' );
  $fictioneer_colors = fictioneer_get_theme_colors_array();

  // Abort if...
  if ( ! is_array( $fictioneer_colors ) || empty( $fictioneer_colors ) ) {
    wp_send_json_error( array( 'failure' => __( 'Error: Colors not found.', 'fictioneer' ) ) );
  }

  // Unset colors to reset them to default
  foreach ( array_keys( $fictioneer_colors ) as $mod ) {
    unset( $mods[ $mod ] );
  }

  // Save to database
  update_option( "theme_mods_{$theme}", $mods );

  // Refresh custom files
  fictioneer_build_customize_css();
  fictioneer_build_customize_css( 'preview' );
  fictioneer_build_dynamic_scripts();

  // Finish
  wp_send_json_success( array( 'success' => true ) );
}
add_action( 'wp_ajax_fictioneer_ajax_reset_theme_colors', 'fictioneer_ajax_reset_theme_colors' );
