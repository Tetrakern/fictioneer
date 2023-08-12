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

// Remove administrators (do not touch them!)
unset( $roles['administrator'] );

// Current selection
$selected_role = ( $_GET['fictioneer-role'] ?? 0 ) ?: array_keys( $roles )[0];

function fictioneer_role_card( $role, $selected_role ) {
  // Setup
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

  // Start HTML ---> ?>
  <form
    method="post"
    class="<?php echo $selected_role == $role['type'] ? '' : 'hidden'; ?>"
    action="<?php echo admin_url( 'admin-post.php' ); ?>"
    data-sidebar-target="<?php echo $role['type']; ?>"
  >

    <input type="hidden" name="action" value="fictioneer_roles_update_role">
    <input type="hidden" name="role" value="<?php echo $role['type']; ?>">
    <?php echo $admin_nonce; ?>

    <div class="card">
      <div class="card-wrapper">
        <h3 class="card-header"><?php echo $role['name']; ?></h3>
        <div class="card-content">
          <input type="hidden" name="action" value="fictioneer_roles_update_role">
          <input type="hidden" name="role" value="<?php echo $role['type']; ?>">
          <?php echo $admin_nonce; ?>

          <h4 class="row"><?php _e( 'Account Capabilities', 'fictioneer' ); ?></h4>

          <div class="row flex wrap gap-8">
            <?php
              foreach ( $account_caps as $cap ) {
                $role_caps = $role['capabilities'];
                $set = in_array( $cap, $role_caps ) && ( $role_caps[ $cap ] ?? 0 );
                $name = ucwords( str_replace( 'fcn ', '', str_replace( '_', ' ', $cap ) ) );

                fictioneer_capability_checkbox( $cap, $name, $set );
              }
            ?>
          </div>

          <h4 class="row"><?php _e( 'Administrative Capabilities', 'fictioneer' ); ?></h4>

          <div class="row flex wrap gap-8">
            <?php
              foreach ( $admin_caps as $cap ) {
                $role_caps = $role['capabilities'];
                $set = in_array( $cap, $role_caps ) && ( $role_caps[ $cap ] ?? 0 );
                $name = ucwords( str_replace( 'fcn ', '', str_replace( '_', ' ', $cap ) ) );

                fictioneer_capability_checkbox( $cap, $name, $set );
              }
            ?>
          </div>

          <h4 class="row"><?php _e( 'Taxonomy Capabilities', 'fictioneer' ); ?></h4>

          <div class="row flex wrap gap-8">
            <?php
              foreach ( $taxonomy_caps as $cap ) {
                $role_caps = $role['capabilities'];
                $set = in_array( $cap, $role_caps ) && ( $role_caps[ $cap ] ?? 0 );
                $name = ucwords( str_replace( 'fcn ', '', str_replace( '_', ' ', $cap ) ) );

                fictioneer_capability_checkbox( $cap, $name, $set );
              }
            ?>
          </div>

          <h4 class="row"><?php _e( 'Post Capabilities', 'fictioneer' ); ?></h4>

          <div class="row flex wrap gap-8">
            <?php
              foreach ( $post_caps as $cap ) {
                $role_caps = $role['capabilities'];
                $set = in_array( $cap, $role_caps ) && ( $role_caps[ $cap ] ?? 0 );
                $name = ucwords( str_replace( 'fcn ', '', str_replace( '_', ' ', $cap ) ) );

                fictioneer_capability_checkbox( $cap, $name, $set );
              }
            ?>
          </div>

          <h4 class="row"><?php _e( 'Story Capabilities', 'fictioneer' ); ?></h4>

          <div class="row flex wrap gap-8">
            <?php
              foreach ( $story_caps as $cap ) {
                $role_caps = $role['capabilities'];
                $set = in_array( $cap, $role_caps ) && ( $role_caps[ $cap ] ?? 0 );
                $name = ucwords( str_replace( 'fcn ', '', str_replace( '_', ' ', $cap ) ) );

                fictioneer_capability_checkbox( $cap, $name, $set );
              }
            ?>
          </div>

          <h4 class="row"><?php _e( 'Chapter Capabilities', 'fictioneer' ); ?></h4>

          <div class="row flex wrap gap-8">
            <?php
              foreach ( $chapter_caps as $cap ) {
                $role_caps = $role['capabilities'];
                $set = in_array( $cap, $role_caps ) && ( $role_caps[ $cap ] ?? 0 );
                $name = ucwords( str_replace( 'fcn ', '', str_replace( '_', ' ', $cap ) ) );

                fictioneer_capability_checkbox( $cap, $name, $set );
              }
            ?>
          </div>

          <h4 class="row"><?php _e( 'Collection Capabilities', 'fictioneer' ); ?></h4>

          <div class="row flex wrap gap-8">
            <?php
              foreach ( $collection_caps as $cap ) {
                $role_caps = $role['capabilities'];
                $set = in_array( $cap, $role_caps ) && ( $role_caps[ $cap ] ?? 0 );
                $name = ucwords( str_replace( 'fcn ', '', str_replace( '_', ' ', $cap ) ) );

                fictioneer_capability_checkbox( $cap, $name, $set );
              }
            ?>
          </div>

          <h4 class="row"><?php _e( 'Recommendation Capabilities', 'fictioneer' ); ?></h4>

          <div class="row flex wrap gap-8">
            <?php
              foreach ( $recommendation_caps as $cap ) {
                $role_caps = $role['capabilities'];
                $set = in_array( $cap, $role_caps ) && ( $role_caps[ $cap ] ?? 0 );
                $name = ucwords( str_replace( 'fcn ', '', str_replace( '_', ' ', $cap ) ) );

                fictioneer_capability_checkbox( $cap, $name, $set );
              }
            ?>
          </div>

        </div>
      </div>
    </div>

    <div class="flex wrap" style="margin-top: var(--32bp);">
      <button type="submit" class="button button--secondary">
        <?php printf( _x( 'Update %s', 'Update {Role}', 'fictioneer' ), $role['name'] ); ?>
      </button>
    </div>

  </form>
  <?php // <--- End HTML
}

function fictioneer_capability_checkbox( $cap, $name, $set = false ) {
  // Start HTML ---> ?>
  <label class="capability-checkbox">
    <input type="hidden" name="caps[<?php echo $cap; ?>]" value="0">
    <input class="capability-checkbox__input" name="caps[<?php echo $cap; ?>]" type="checkbox" <?php echo $set ? 'checked' : ''; ?> value="1">
    <div class="capability-checkbox__name"><?php echo $name; ?></div>
  </label>
  <?php // <--- End HTML
}

?>

<div class="fictioneer-ui fictioneer-settings">

	<?php fictioneer_settings_header( 'roles' ); ?>

	<div class="fictioneer-settings__content">
    <div class="tab-content">
      <div class="sidebar-layout">

        <ul class="sidebar-layout__side">
          <?php
            foreach ( $roles as $key => $role ) {
              $role['type'] = $key;
              $class = $selected_role == $key ? 'class="current"' : '';
              echo '<li ' . $class . ' data-sidebar-click="' . $key . '">' . $role['name'] . '</li>';
            }
          ?>
        </ul>

        <div class="sidebar-layout__content">
          <?php
            foreach ( $roles as $key => $role ) {
              $role['type'] = $key;
              fictioneer_role_card( $role, $selected_role );
            }
          ?>
        </div>

      </div>
    </div>
  </div>

</div>
