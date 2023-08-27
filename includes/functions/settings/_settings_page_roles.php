<?php
/**
 * Partial: Role Settings
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.6.0
 */
?>

<?php

global $pagenow;

// Setup
$roles = wp_roles()->roles;
$current_url = add_query_arg( $_GET, admin_url( $pagenow ) );
$current_url = remove_query_arg( array( 'success', 'failure' ), $current_url );

$admin_url = admin_url( 'admin-post.php' );
$update_role_nonce = wp_nonce_field( 'fictioneer_update_role', 'fictioneer_nonce', true, false );
$add_role_nonce = wp_nonce_field( 'fictioneer_add_role', 'fictioneer_nonce', true, false );
$rename_role_nonce = wp_nonce_field( 'fictioneer_rename_role', 'fictioneer_nonce', true, false );

$remove_message = __( 'Are you sure you want to remove the %s role? All current holders will become Subscribers. Enter %s to confirm.', 'fictioneer' );
$remove_confirm = __( 'DELETE', 'fictioneer' );

$protected_roles = ['administrator', 'editor', 'author', 'contributor', 'subscriber'];

$editor_caps = array(
  'fcn_shortcodes',
  'fcn_select_page_template',
  'fcn_custom_page_css',
  'fcn_custom_epub_css',
  'fcn_seo_meta',
  'fcn_make_sticky',
  'fcn_edit_permalink',
  'fcn_all_blocks',
  'fcn_story_pages',
  'fcn_edit_date'
);

$restrictions = array(
  'fcn_reduced_profile',
  'fcn_edit_only_others_comments',
  'fcn_upload_limit',
  'fcn_upload_restrictions',
  'fcn_classic_editor'
);

$advanced_caps = array(
  'fcn_adminbar_access',
  'fcn_admin_panel_access',
  'fcn_dashboard_access',
  'fcn_show_badge',
  'upload_files',
  'edit_files',
  'fcn_allow_self_delete'
);

$admin_caps = array(
  'fcn_privacy_clearance',
  'fcn_read_others_files',
  'fcn_edit_others_files',
  'fcn_delete_others_files',
  'list_users',
  'create_users',
  'edit_users',
  'remove_users',
  'switch_themes',
  'edit_theme_options',
  'edit_themes',
  'unfiltered_html'
);

$taxonomy_caps = array(
  // Categories
  'manage_categories',
  'assign_categories',
  'edit_categories',
  'delete_categories',
  // Tags
  'manage_post_tags',
  'assign_post_tags',
  'edit_post_tags',
  'delete_post_tags',
  // Genres
  'manage_fcn_genres',
  'assign_fcn_genres',
  'edit_fcn_genres',
  'delete_fcn_genres',
  // Fandoms
  'manage_fcn_fandoms',
  'assign_fcn_fandoms',
  'edit_fcn_fandoms',
  'delete_fcn_fandoms',
  // Characters
  'manage_fcn_characters',
  'assign_fcn_characters',
  'edit_fcn_characters',
  'delete_fcn_characters',
  // Warnings
  'manage_fcn_content_warnings',
  'assign_fcn_content_warnings',
  'edit_fcn_content_warnings',
  'delete_fcn_content_warnings'
);

$post_caps = array(
  'publish_posts',
  'edit_posts',
  'delete_posts',
  'edit_published_posts',
  'delete_published_posts',
  'edit_others_posts',
  'delete_others_posts',
  'read_private_posts',
  'edit_private_posts',
  'delete_private_posts'
);

$page_caps = array(
  'publish_pages',
  'edit_pages',
  'delete_pages',
  'edit_published_pages',
  'delete_published_pages',
  'edit_others_pages',
  'delete_others_pages',
  'read_private_pages',
  'edit_private_pages',
  'delete_private_pages'
);

$story_caps = array(
  'publish_fcn_stories',
  'edit_fcn_stories',
  'delete_fcn_stories',
  'edit_published_fcn_stories',
  'delete_published_fcn_stories',
  'edit_others_fcn_stories',
  'delete_others_fcn_stories',
  'read_private_fcn_stories',
  'edit_private_fcn_stories',
  'delete_private_fcn_stories'
);

$chapter_caps = array(
  'publish_fcn_chapters',
  'edit_fcn_chapters',
  'delete_fcn_chapters',
  'edit_published_fcn_chapters',
  'delete_published_fcn_chapters',
  'edit_others_fcn_chapters',
  'delete_others_fcn_chapters',
  'read_private_fcn_chapters',
  'edit_private_fcn_chapters',
  'delete_private_fcn_chapters'
);

$collection_caps = array(
  'publish_fcn_collections',
  'edit_fcn_collections',
  'delete_fcn_collections',
  'edit_published_fcn_collections',
  'delete_published_fcn_collections',
  'edit_others_fcn_collections',
  'delete_others_fcn_collections',
  'read_private_fcn_collections',
  'edit_private_fcn_collections',
  'delete_private_fcn_collections'
);

$recommendation_caps = array(
  'publish_fcn_recommendations',
  'edit_fcn_recommendations',
  'delete_fcn_recommendations',
  'edit_published_fcn_recommendations',
  'delete_published_fcn_recommendations',
  'edit_others_fcn_recommendations',
  'delete_others_fcn_recommendations',
  'read_private_fcn_recommendations',
  'edit_private_fcn_recommendations',
  'delete_private_fcn_recommendations'
);

$all_caps = array(
  [ __( 'Editor Capabilities', 'fictioneer' ), $editor_caps ],
  [ __( 'Restricted Capabilities', 'fictioneer' ), $restrictions ],
  [ __( 'Advanced Capabilities', 'fictioneer' ), $advanced_caps ],
  [ __( 'Admin Capabilities (Danger)', 'fictioneer' ), $admin_caps ],
  [ __( 'Taxonomy Capabilities', 'fictioneer' ), $taxonomy_caps ],
  [ __( 'Post Capabilities', 'fictioneer' ), $post_caps ],
  [ __( 'Page Capabilities', 'fictioneer' ), $page_caps ],
  [ __( 'Story Capabilities', 'fictioneer' ), $story_caps ],
  [ __( 'Chapter Capabilities', 'fictioneer' ), $chapter_caps ],
  [ __( 'Collection Capabilities', 'fictioneer' ), $collection_caps ],
  [ __( 'Recommendation Capabilities', 'fictioneer' ), $recommendation_caps ]
);

// Remove administrators (do not touch them!)
unset( $roles['administrator'] );

// Order roles
$order = [
  'editor' => 0,
  'fcn_moderator' => 1,
  'author' => 2,
  'contributor' => 3,
  'subscriber' => 99
];

uksort(
  $roles,
  function( $a, $b ) use ( $order ) {
    $aVal = $order[ $a ] ?? PHP_INT_MAX;
    $bVal = $order[ $b ] ?? PHP_INT_MAX;

    return $aVal <=> $bVal;
  }
);

// Current role
$current_role_slug = ( $_GET['fictioneer-subnav'] ?? 0 ) ?: array_keys( $roles )[0];
$current_role = $roles[ $current_role_slug ];

?>

<div class="fictioneer-ui fictioneer-settings">

	<?php fictioneer_settings_header( 'roles' ); ?>

  <ul class="fictioneer-settings__subnav">
    <?php
      foreach ( $roles as $key => $role ) {
        $role['type'] = $key;
        $class = $current_role_slug == $key ? ' class="tab active"' : ' class="tab"';
        $link = add_query_arg( 'fictioneer-subnav', $key, $current_url );

        echo '<a href="' . $link . '" ' . $class . '>' . $role['name'] . '</a>';
      }
    ?>
    <button type="button" class="" data-dialog-target="add-role-dialog"><span class="dashicons dashicons-plus"></span></button>
  </ul>

	<div class="fictioneer-settings__content">
    <div class="tab-content">

      <form method="post" action="<?php echo $admin_url; ?>">

        <input type="hidden" name="action" value="fictioneer_update_role">
        <input type="hidden" name="role" value="<?php echo $current_role_slug; ?>">
        <?php echo $update_role_nonce; ?>

        <div class="columns-layout two-columns">
          <?php
            foreach ( $all_caps as $caps ) {
              fictioneer_admin_capability_card( $caps[0], $caps[1], $current_role );
            }
          ?>
        </div>

        <div class="flex flex-wrap gap-8 space-between">

          <div class="flex flex-wrap gap-8">
            <button type="submit" class="button button-primary">
              <?php printf( _x( 'Update %s', 'Update {Role}', 'fictioneer' ), $current_role['name'] ); ?>
            </button>
            <?php if ( ! in_array( $current_role_slug, $protected_roles ) ) : ?>
              <button type="button" class="button" data-dialog-target="rename-role-dialog">
                <?php _ex( 'Rename', 'Rename role', 'fictioneer' ); ?>
              </button>
            <?php endif; ?>
          </div>

          <div class="flex flex-wrap gap-8">

            <?php if ( ! in_array( $current_role_slug, $protected_roles ) ) : ?>

              <?php
                $remove_action = 'fictioneer_remove_role';
                $remove_link = wp_nonce_url(
                  add_query_arg(
                    'role',
                    $current_role_slug,
                    admin_url( "admin-post.php?action={$remove_action}" )
                  ),
                  $remove_action,
                  'fictioneer_nonce'
                );
              ?>

              <a href="<?php echo $remove_link; ?>" class="button button-secondary confirm-dialog" data-dialog-message="<?php printf( $remove_message, $current_role['name'], $remove_confirm ); ?>" data-dialog-confirm="<?php echo $remove_confirm; ?>">
                <?php printf( _x( 'Remove %s', 'Remove {Role}', 'fictioneer' ), $current_role['name'] ); ?>
              </a>

            <?php endif; ?>

          </div>
        </div>

      </form>

    </div>
  </div>

  <dialog class="fictioneer-dialog" id="add-role-dialog">
    <div class="fictioneer-dialog__header">
      <span><?php _e( 'Add Role', 'fictioneer' ); ?></span>
    </div>
    <div class="fictioneer-dialog__content">
      <form method="post" action="<?php echo $admin_url; ?>">
        <input type="hidden" name="action" value="fictioneer_add_role">
        <?php echo $add_role_nonce; ?>
        <div class="text-input">
          <label for="new_role">
            <input name="new_role" placeholder="<?php _ex( 'Role Name', 'fictioneer' ); ?>" type="text" required>
            <p class="sub-label"><?php _e( 'Enter the name of the new role.', 'fictioneer' ) ?></p>
          </label>
        </div>
        <div class="fictioneer-dialog__actions">
          <button value="cancel" formmethod="dialog" class="button"><?php _e( 'Cancel', 'fictioneer' ); ?></button>
          <button value="" class="button button-primary"><?php _e( 'Add', 'fictioneer' ); ?></button>
        </div>
      </form>
    </div>
  </dialog>

  <dialog class="fictioneer-dialog" id="rename-role-dialog">
    <div class="fictioneer-dialog__header">
      <span><?php printf( __( 'Rename %s Role', 'fictioneer' ), $current_role['name'] ); ?></span>
    </div>
    <div class="fictioneer-dialog__content">
      <form method="post" action="<?php echo $admin_url; ?>">
        <input type="hidden" name="action" value="fictioneer_rename_role">
        <input type="hidden" name="role" value="<?php echo $current_role_slug; ?>">
        <?php echo $rename_role_nonce; ?>
        <div class="text-input">
          <label for="new_name">
            <input name="new_name" placeholder="<?php _ex( 'New Name', 'fictioneer' ); ?>" type="text" required>
            <p class="sub-label"><?php _e( 'Enter the new name for the role.', 'fictioneer' ) ?></p>
          </label>
        </div>
        <div class="text-input mt-12">
          <label for="role_slug">
            <input name="role_slug" placeholder="<?php echo $current_role_slug; ?>" type="text" disabled>
            <p class="sub-label"><?php _e( 'Slug of the role. Must not be changed.', 'fictioneer' ) ?></p>
          </label>
        </div>
        <div class="fictioneer-dialog__actions">
          <button value="cancel" formmethod="dialog" class="button"><?php _e( 'Cancel', 'fictioneer' ); ?></button>
          <button value="" class="button button-primary"><?php _e( 'Rename', 'fictioneer' ); ?></button>
        </div>
      </form>
    </div>
  </dialog>

</div>
