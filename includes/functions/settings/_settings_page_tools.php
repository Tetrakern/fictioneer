<?php
/**
 * Partial: Tools Settings
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.7
 */
?>

<div class="fictioneer-ui fictioneer-settings">

	<?php fictioneer_settings_header( 'tools' ); ?>

	<div class="fictioneer-settings__content">
    <div class="tab-content">
      <div class="columns-layout two-columns">

        <div class="card">
          <div class="card-wrapper">
            <h3 class="card-header"><?php _e( 'Role Tools', 'fictioneer' ) ?></h3>
            <div class="card-content">
              <p class="description row"><?php _e( '<strong>Add or remove Fictioneer moderator</strong> role that is limited to moderating comments. This role has been stripped of access to most other menus and settings, although it does not necessarily account for changes to the admin interface made by you. <strong>Note:</strong> Removing the moderator role may leave previous owners without a role.', 'fictioneer' ) ?></p>
              <div class="row flex wrap gap-6">
                <a class="button button--secondary" id="button-move-tags-to-genres" href="<?php echo fictioneer_tool_action( 'fictioneer_add_moderator_role' ); ?>"><?php _e( 'Add Moderator Role', 'fictioneer' ) ?></a>
                <a class="button button--secondary" id="button-duplicate-tags-to-genres" href="<?php echo fictioneer_tool_action( 'fictioneer_remove_moderator_role' ); ?>"><?php _e( 'Remove Moderator Role', 'fictioneer' ) ?></a>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-wrapper">
            <h3 class="card-header"><?php _e( 'Story Tools', 'fictioneer' ) ?></h3>
            <div class="card-content">
              <p class="description row"><?php _e( '<strong>Convert or duplicate story tags to genres.</strong> If you want to change your previous tagging choices in bulk, you can start here. Converting will turn all tags into genres, duplicating will copy and append them as genres but also keep the originals. No taxonomies are ever deleted with either operation.', 'fictioneer' ) ?></p>
              <div class="row flex wrap gap-6">
                <a class="button button--secondary" id="button-move-tags-to-genres" href="<?php echo fictioneer_tool_action( 'fictioneer_move_story_tags_to_genres' ); ?>"><?php _e( 'Tags &rarr; Genres', 'fictioneer' ) ?></a>
                <a class="button button--secondary" id="button-duplicate-tags-to-genres" href="<?php echo fictioneer_tool_action( 'fictioneer_duplicate_story_tags_to_genres' ); ?>"><?php _e( 'Duplicate Tags &rarr; Genres', 'fictioneer' ) ?></a>
              </div>
              <hr>
              <p class="description row"><?php _e( '<strong>Purge story data caches.</strong> In order to accelerate story and chapter pages, certain information is collected once and then cached. These caches should get purged whenever you make a relevant update. If that should fail for whatever reason, you can purge them manually here.', 'fictioneer' ) ?></p>
              <div class="row flex wrap gap-6">
                <a class="button button--secondary" id="purge-story-data-caches" href="<?php echo fictioneer_tool_action( 'fictioneer_purge_story_data_caches' ); ?>"><?php _e( 'Purge Story Data Caches', 'fictioneer' ) ?></a>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-wrapper">
            <h3 class="card-header"><?php _e( 'Chapter Tools', 'fictioneer' ) ?></h3>
            <div class="card-content">
              <p class="description row"><?php _e( '<strong>Convert or duplicate chapter tags to genres.</strong> If you want to change your previous tagging choices in bulk, you can start here. Converting will turn all tags into genres, duplicating will copy and append them as genres but also keep the originals. No taxonomies are ever deleted with either operation.', 'fictioneer' ) ?></p>
              <div class="row flex wrap gap-6">
                <a class="button button--secondary" id="button-move-tags-to-genres" href="<?php echo fictioneer_tool_action( 'fictioneer_move_chapter_tags_to_genres' ); ?>"><?php _e( 'Tags &rarr; Genres', 'fictioneer' ) ?></a>
                <a class="button button--secondary" id="button-duplicate-tags-to-genres" href="<?php echo fictioneer_tool_action( 'fictioneer_duplicate_chapter_tags_to_genres' ); ?>"><?php _e( 'Duplicate Tags &rarr; Genres', 'fictioneer' ) ?></a>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-wrapper">
            <h3 class="card-header"><?php _e( 'Genre Tools', 'fictioneer' ) ?></h3>
            <div class="card-content">
              <?php
                $items = (array) fictioneer_get_default_genres();
                $item_count = count( $items );
              ?>
              <p class="description row"><?php
                printf(
                  __( '<strong>Append a pre-defined collection of %1$s genres and subgenres</strong> to the list (English) or update an existing list. They are taken from the <a href="%2$s" target="_blank">Fictioneer Taxonomies Project</a> aimed to make genres more conform. This may be useful to aggregation services that offer searchable lists for stories from different sites, perhaps including yours.', 'fictioneer' ),
                  $item_count,
                  'https://github.com/Tetrakern/fictioneer-taxonomies'
                )
              ?></p>
              <details class="row subtext">
                <summary><?php _e( 'Preview genre list (hover for description)', 'fictioneer' ); ?></summary>
                <div class="fs-xxs mt-4 highlighted-items">
                  <?php
                    $index = 0;

                    foreach ( $items as $key => $value ) {
                      $description = isset( $value->description ) ? $value->description : __( 'No description yet.', 'fictioneer' );
                      echo '<span title="' . $description . '">' . $value->name . '</span>';
                      $index++;
                      if ( $index != $item_count ) echo ', ';
                    }
                  ?>
                </div>
              </details>
              <div class="row flex wrap gap-6">
                <a class="button button--secondary" id="button-append-default-genres" href="<?php echo fictioneer_tool_action( 'fictioneer_append_default_genres' ); ?>"><?php _e( 'Add Genres', 'fictioneer' ) ?></a>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-wrapper">
            <h3 class="card-header"><?php _e( 'Tag Tools', 'fictioneer' ) ?></h3>
            <div class="card-content">
              <?php
                $items = (array) fictioneer_get_default_tags();
                $item_count = count( $items );
              ?>
              <p class="description row"><?php
                printf(
                  __( '<strong>Append a pre-defined collection of %1$s tags</strong> to the list (English) or update an existing list. They are taken from the <a href="%2$s" target="_blank">Fictioneer Taxonomies Project</a> aimed to make tags more conform. This may be useful to aggregation services that offer searchable lists for stories from different sites, perhaps including yours.', 'fictioneer' ),
                  $item_count,
                  'https://github.com/Tetrakern/fictioneer-taxonomies'
                )
              ?></p>
              <details class="row subtext">
                <summary><?php _e( 'Preview tag list (hover for description)', 'fictioneer' ); ?></summary>
                <div class="fs-xxs mt-4 highlighted-items">
                  <?php
                    $index = 0;

                    foreach ( $items as $key => $value ) {
                      $description = isset( $value->description ) ? $value->description : __( 'No description yet.', 'fictioneer' );
                      echo '<span title="' . $description . '">' . $value->name . '</span>';
                      $index++;
                      if ( $index != $item_count ) echo ', ';
                    }
                  ?>
                </div>
              </details>
              <div class="row flex wrap gap-6">
                <a class="button button--secondary" id="button-append-default-tags" href="<?php echo fictioneer_tool_action( 'fictioneer_append_default_tags' ); ?>"><?php _e( 'Add Tags', 'fictioneer' ) ?></a>
                <a class="button button--secondary" id="button-remove-unused-tags" href="<?php echo fictioneer_tool_action( 'fictioneer_remove_unused_tags' ); ?>"><?php _e( 'Remove Unused Tags', 'fictioneer' ) ?></a>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-wrapper">
            <h3 class="card-header"><?php _e( 'Repair Tools', 'fictioneer' ) ?></h3>
            <div class="card-content">
              <p class="description row"><?php _e( '<strong>Repair or migrate legacy data.</strong> Regardless of planning and efforts, there can always be updates with changes so drastic that it may cause older databases to become invalid or outright broken. These actions attempt to fix such issues.', 'fictioneer' ) ?></p>
              <div class="row flex wrap gap-6">
                <a class="button button--secondary disabled" href="<?php echo fictioneer_tool_action( 'fictioneer_fix_users' ); ?>"><?php _e( 'Fix Users', 'fictioneer' ) ?></a>
                <a class="button button--secondary disabled" href="<?php echo fictioneer_tool_action( 'fictioneer_fix_stories' ); ?>"><?php _e( 'Fix Stories', 'fictioneer' ) ?></a>
                <a class="button button--secondary disabled" href="<?php echo fictioneer_tool_action( 'fictioneer_fix_chapters' ); ?>"><?php _e( 'Fix Chapters', 'fictioneer' ) ?></a>
                <a class="button button--secondary disabled" href="<?php echo fictioneer_tool_action( 'fictioneer_fix_recommendations' ); ?>"><?php _e( 'Fix Recommendations', 'fictioneer' ) ?></a>
                <a class="button button--secondary disabled" href="<?php echo fictioneer_tool_action( 'fictioneer_fix_collections' ); ?>"><?php _e( 'Fix Collections', 'fictioneer' ) ?></a>
                <a class="button button--secondary disabled" href="<?php echo fictioneer_tool_action( 'fictioneer_fix_pages' ); ?>"><?php _e( 'Fix Pages', 'fictioneer' ) ?></a>
                <a class="button button--secondary disabled" href="<?php echo fictioneer_tool_action( 'fictioneer_fix_posts' ); ?>"><?php _e( 'Fix Posts', 'fictioneer' ) ?></a>
              </div>
              <?php if ( fictioneer_caching_active() ) : ?>
                <hr>
                <p class="description row"><?php _e( '<strong>Reset post relationship registry.</strong> Warning, this should only ever be done if the registry either causes errors or has become corrupted. Without the registry, the cache purge system cannot find referenced posts.', 'fictioneer' ) ?></p>
                <details class="row subtext">
                  <summary><?php _e( 'Show registry array', 'fictioneer' ); ?></summary>
                  <code class="fs-xxs mt-4" style="height: 256px;">
                    <pre><?php print_r( fictioneer_get_relationship_registry() ); ?></pre>
                  </code>
                </details>
                <div class="row flex wrap gap-6">
                  <a class="button button--secondary" id="reset-post-relationship-registry" data-click="reset-post-relationship-registry" data-prompt="<?php esc_attr_e( 'Are you sure? Repopulating the registry requires re-saving every single post or page you want to be covered. Manually.', 'fictioneer' ) ?>" href="<?php echo fictioneer_tool_action( 'fictioneer_reset_post_relationship_registry' ); ?>"><?php _e( 'Reset Registry', 'fictioneer' ) ?></a>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <?php do_action( 'fictioneer_admin_settings_tools' ); ?>

      </div>
    </div>
  </div>

</div>
