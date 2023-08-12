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

// Setup
$roles = wp_roles()->roles;
$admin_url = admin_url( 'admin-post.php' );
$admin_nonce = wp_nonce_field( 'fictioneer_roles_update_role', 'fictioneer_nonce', true, false );

$account_caps = array(
  // Fictioneer
  'fcn_adminbar_access',
  'fcn_admin_panel_access',
  'fcn_dashboard_access',
  'fcn_shortcodes',
  // WordPress
  'upload_files',
  'edit_files',
  'unfiltered_html'
);

$admin_caps = array(
  // Fictioneer
  'fcn_select_page_template',
  'fcn_privacy_clearance',
  'fcn_read_others_files',
  'fcn_edit_others_files',
  'fcn_delete_others_files',
  // WordPress
  'create_users',
  'edit_users',
  'delete_users'
);

$taxonomy_caps = array(
  // Categories & Tags
  'manage_categories',
  // Genres
  'manage_fcn_genres',
  'edit_fcn_genres',
  'delete_fcn_genres',
  'assign_fcn_genres',
  // Fandoms
  'manage_fcn_fandoms',
  'edit_fcn_fandoms',
  'delete_fcn_fandoms',
  'assign_fcn_fandoms',
  // Characters
  'manage_fcn_characters',
  'edit_fcn_characters',
  'delete_fcn_characters',
  'assign_fcn_characters',
  // Warnings
  'manage_fcn_content_warnings',
  'edit_fcn_content_warnings',
  'delete_fcn_content_warnings',
  'assign_fcn_content_warnings'
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
  [ __( 'Account Capabilities', 'fictioneer' ), $account_caps ],
  [ __( 'Admin Capabilities', 'fictioneer' ), $admin_caps ],
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

// Current selection
$selected_role = ( $_GET['fictioneer-subnav'] ?? 0 ) ?: array_keys( $roles )[0];

?>

<div class="fictioneer-ui fictioneer-settings">

	<?php fictioneer_settings_header( 'roles' ); ?>

  <ul class="sub-navigation">
      <?php
      foreach ( $roles as $key => $role ) {
        $role['type'] = $key;
        $class = $selected_role == $key ? 'class="tab active"' : 'class="tab"';
        echo '<li ' . $class . ' data-sidebar-click="' . $key . '">' . $role['name'] . '</li>';
      }
    ?>
  </ul>

	<div class="fictioneer-settings__content">
    <div class="tab-content">
      <?php foreach ( $roles as $key => $role ) : ?>
        <form method="post" action="<?php echo $admin_url; ?>" class="<?php echo $selected_role == $key ? '' : 'hidden'; ?>" data-sidebar-target="<?php echo $key; ?>">

          <input type="hidden" name="action" value="fictioneer_roles_update_role">
          <input type="hidden" name="role" value="<?php echo $key; ?>">
          <?php echo $admin_nonce; ?>

          <div class="columns-layout two-columns">
            <?php
              foreach ( $all_caps as $caps ) {
                fictioneer_admin_capability_card( $caps[0], $caps[1], $role );
              }
            ?>
          </div>

          <div class="flex">
            <button type="submit" class="button button--secondary">
              <?php printf( _x( 'Update %s', 'Update {Role}', 'fictioneer' ), $role['name'] ); ?>
            </button>
          </div>

        </form>
      <?php endforeach; ?>
    </div>
  </div>

</div>