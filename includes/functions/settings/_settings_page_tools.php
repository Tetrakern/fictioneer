<?php
/**
 * Partial: Tools Settings
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.7
 */
?>

<div class="fictioneer-settings">

	<?php fictioneer_settings_header( 'tools' ); ?>

	<div class="fictioneer-settings__content">
    <div class="fictioneer-columns fictioneer-columns--two-columns">

      <div class="fictioneer-card">
        <div class="fictioneer-card__wrapper">
          <h3 class="fictioneer-card__header"><?php _e( 'Role Tools', 'fictioneer' ); ?></h3>
          <div class="fictioneer-card__content">

            <div class="fictioneer-card__row">
              <p class="row"><?php _e( '<strong>Add or restore moderator role</strong> that is limited to only moderating comments. It has been stripped of access to most other menus and settings, although this does not necessarily account for changes to the admin panel made by you.', 'fictioneer' ); ?></p>
            </div>

            <div class="fictioneer-card__row fictioneer-card__row--buttons">
              <a class="button button--secondary" id="button-move-tags-to-genres" href="<?php echo fictioneer_tool_action( 'fictioneer_add_moderator_role' ); ?>"><?php _e( 'Add Moderator Role', 'fictioneer' ); ?></a>
            </div>

            <hr>

            <div class="fictioneer-card__row">
              <p><?php _e( '<strong>Initialize theme roles and capabilities.</strong> Fictioneer comes with extended capabilities and roles that need to be initialized in order to work correctly. This should happen once when you first activate the theme, but you can do it manually here. Note that this will only reset theme-related capabilities, not undo anything else done by other plugins.', 'fictioneer' ); ?></p>
            </div>

            <div class="fictioneer-card__row fictioneer-card__row--buttons">
              <a class="button button--secondary" id="button-initialize-roles" href="<?php echo fictioneer_tool_action( 'fictioneer_initialize_roles' ); ?>"><?php _e( 'Initialize Roles', 'fictioneer' ); ?></a>
            </div>

          </div>
        </div>
      </div>

      <div class="fictioneer-card">
        <div class="fictioneer-card__wrapper">
          <h3 class="fictioneer-card__header"><?php _e( 'Story Tools', 'fictioneer' ); ?></h3>
          <div class="fictioneer-card__content">

            <div class="fictioneer-card__row">
              <p><?php _e( '<strong>Convert or duplicate story tags to genres.</strong> If you want to change your previous tagging choices in bulk, you can start here. Converting will turn all tags into genres, duplicating will copy and append them as genres but also keep the originals.', 'fictioneer' ); ?></p>
            </div>

            <div class="fictioneer-card__row fictioneer-card__row--buttons">
              <a class="button button--secondary" id="button-move-tags-to-genres" href="<?php echo fictioneer_tool_action( 'fictioneer_move_story_tags_to_genres' ); ?>"><?php _e( 'Tags &rarr; Genres', 'fictioneer' ); ?></a>
              <a class="button button--secondary" id="button-duplicate-tags-to-genres" href="<?php echo fictioneer_tool_action( 'fictioneer_duplicate_story_tags_to_genres' ); ?>"><?php _e( 'Duplicate Tags &rarr; Genres', 'fictioneer' ); ?></a>
            </div>

          </div>
        </div>
      </div>

      <div class="fictioneer-card">
        <div class="fictioneer-card__wrapper">
          <h3 class="fictioneer-card__header"><?php _e( 'Chapter Tools', 'fictioneer' ); ?></h3>
          <div class="fictioneer-card__content">

            <div class="fictioneer-card__row">
              <p><?php _e( '<strong>Convert or duplicate chapter tags to genres.</strong> If you want to change your previous tagging choices in bulk, you can start here. Converting will turn all tags into genres, duplicating will copy and append them as genres but also keep the originals.', 'fictioneer' ); ?></p>
            </div>

            <div class="fictioneer-card__row fictioneer-card__row--buttons">
              <a class="button button--secondary" id="button-move-tags-to-genres" href="<?php echo fictioneer_tool_action( 'fictioneer_move_chapter_tags_to_genres' ); ?>"><?php _e( 'Tags &rarr; Genres', 'fictioneer' ); ?></a>
              <a class="button button--secondary" id="button-duplicate-tags-to-genres" href="<?php echo fictioneer_tool_action( 'fictioneer_duplicate_chapter_tags_to_genres' ); ?>"><?php _e( 'Duplicate Tags &rarr; Genres', 'fictioneer' ); ?></a>
            </div>

          </div>
        </div>
      </div>

      <div class="fictioneer-card">
        <div class="fictioneer-card__wrapper">
          <h3 class="fictioneer-card__header"><?php _e( 'Genre Tools', 'fictioneer' ); ?></h3>
          <div class="fictioneer-card__content">

            <?php
              $items = (array) fictioneer_get_default_genres();
              $item_count = count( $items );
            ?>

            <div class="fictioneer-card__row">
              <p><?php
                printf(
                  __( '<strong>Append a pre-defined collection of %1$s genres and subgenres</strong> to the list (English) or update an existing list, taken from the <a href="%2$s" target="_blank">Fictioneer Taxonomy Project</a>.', 'fictioneer' ),
                  $item_count,
                  'https://github.com/Tetrakern/fictioneer-taxonomies'
                )
              ?></p>
            </div>

            <div class="fictioneer-card__row">
              <details>
                <summary><?php _e( 'Preview genre list (hover for description)', 'fictioneer' ); ?></summary>
                <div class="fictioneer-term-imports">
                  <?php
                    $index = 0;

                    foreach ( $items as $key => $value ) {
                      $description = isset( $value->description ) ? $value->description : __( 'No description yet.', 'fictioneer' );

                      echo "<span title='{$description}'>{$value->name}</span>";

                      $index++;

                      if ( $index != $item_count ) {
                        echo ', ';
                      }
                    }
                  ?>
                </div>
              </details>
            </div>

            <div class="fictioneer-card__row fictioneer-card__row--buttons">
              <a class="button button--secondary" id="button-append-default-genres" href="<?php echo fictioneer_tool_action( 'fictioneer_append_default_genres' ); ?>"><?php _e( 'Add Genres', 'fictioneer' ); ?></a>
            </div>

          </div>
        </div>
      </div>

      <div class="fictioneer-card">
        <div class="fictioneer-card__wrapper">
          <h3 class="fictioneer-card__header"><?php _e( 'Tag Tools', 'fictioneer' ); ?></h3>
          <div class="fictioneer-card__content">

            <?php
              $items = (array) fictioneer_get_default_tags();
              $item_count = count( $items );
            ?>

            <div class="fictioneer-card__row">
              <p><?php
                printf(
                  __( '<strong>Append a pre-defined collection of %1$s tags</strong> to the list (English) or update an existing list, taken from the <a href="%2$s" target="_blank">Fictioneer Taxonomy Project</a>.', 'fictioneer' ),
                  $item_count,
                  'https://github.com/Tetrakern/fictioneer-taxonomies'
                )
              ?></p>
            </div>

            <div class="fictioneer-card__row">
              <details>
                <summary><?php _e( 'Preview tag list (hover for description)', 'fictioneer' ); ?></summary>
                <div class="fictioneer-term-imports">
                  <?php
                    $index = 0;

                    foreach ( $items as $key => $value ) {
                      $description = isset( $value->description ) ? $value->description : __( 'No description yet.', 'fictioneer' );

                      echo "<span title='{$description}'>{$value->name}</span>";

                      $index++;

                      if ( $index != $item_count ) {
                        echo ', ';
                      }
                    }
                  ?>
                </div>
              </details>
            </div>

            <div class="fictioneer-card__row fictioneer-card__row--buttons">
              <a class="button button--secondary" id="button-append-default-tags" href="<?php echo fictioneer_tool_action( 'fictioneer_append_default_tags' ); ?>"><?php _e( 'Add Tags', 'fictioneer' ); ?></a>
              <a class="button button--secondary" id="button-remove-unused-tags" href="<?php echo fictioneer_tool_action( 'fictioneer_remove_unused_tags' ); ?>"><?php _e( 'Remove Unused Tags', 'fictioneer' ); ?></a>
            </div>

          </div>
        </div>
      </div>

      <div class="fictioneer-card">
        <div class="fictioneer-card__wrapper">
          <h3 class="fictioneer-card__header"><?php _e( 'Database Tools', 'fictioneer' ); ?></h3>
          <div class="fictioneer-card__content">

            <div class="fictioneer-card__row">
              <p><?php _e( '<strong>Optimize and clean up the database to boost performance.</strong> This action removes any superfluous rows added by the theme, such as empty or obsolete values. Core, plugin, and custom rows are not affected. While generally considered safe, you should <em>always make a backup</em> before performing database operations.', 'fictioneer' ); ?></p>
            </div>

            <div class="fictioneer-card__row fictioneer-card__row--buttons">
              <a class="button button--secondary" href="<?php echo fictioneer_tool_action( 'fictioneer_tools_optimize_database_preview' ); ?>"><?php _e( 'Scan', 'fictioneer' ); ?></a>
              <a class="button button--secondary" href="<?php echo fictioneer_tool_action( 'fictioneer_tools_optimize_database' ); ?>" data-click="warning-dialog" data-dialog="<?php _e( 'Did you make a database backup?', 'fictioneer' ); ?>"><?php _e( 'Optimize', 'fictioneer' ); ?></a>
            </div>

            <hr>

            <div class="fictioneer-card__row">
              <p><?php _e( '<strong>Add missing database fields.</strong> To save thousands of rows, the theme only stores "truthy" values and deletes "falsy" ones. However, some fields are relevant in queries and that can cause issues, for example if you use search plugins. You can add missing fields here, but make sure to allow-list them in the <code>fictioneer_filter_falsy_meta_allow_list</code> filter too.', 'fictioneer' ); ?></p>
            </div>

            <div class="fictioneer-card__row fictioneer-card__row--buttons">
              <a class="button button--secondary" href="<?php echo fictioneer_tool_action( 'fictioneer_tools_add_story_sticky_fields' ); ?>" data-click="warning-dialog" data-dialog="<?php esc_attr_e( 'You are about to append the "fictioneer_story_sticky" (0) meta field to every story. Are you sure you want that?', 'fictioneer' ); ?>"><?php _e( 'Story Sticky', 'fictioneer' ); ?></a>
              <a class="button button--secondary" href="<?php echo fictioneer_tool_action( 'fictioneer_tools_add_story_hidden_fields' ); ?>" data-click="warning-dialog" data-dialog="<?php esc_attr_e( 'You are about to append the "fictioneer_story_hidden" (0) meta field to every story. Are you sure you want that?', 'fictioneer' ); ?>"><?php _e( 'Story Hidden', 'fictioneer' ); ?></a>
              <a class="button button--secondary" href="<?php echo fictioneer_tool_action( 'fictioneer_tools_add_chapter_hidden_fields' ); ?>" data-click="warning-dialog" data-dialog="<?php esc_attr_e( 'You are about to append the "fictioneer_chapter_hidden" (0) meta field to every chapter. Are you sure you want that?', 'fictioneer' ); ?>"><?php _e( 'Chapter Hidden', 'fictioneer' ); ?></a>
            </div>

            <hr>

            <div class="fictioneer-card__row">
              <p><?php _e( '<strong>Purge theme caches.</strong> In order to accelerate page rendering, certain content is composed once and then cached. These caches get purged whenever you make relevant updates. If you need to purge them manually, you can do so here.', 'fictioneer' ); ?></p>
            </div>

            <div class="fictioneer-card__row fictioneer-card__row--buttons">
              <a class="button button--secondary" id="purge-theme-caches" href="<?php echo fictioneer_tool_action( 'fictioneer_tools_purge_theme_caches' ); ?>"><?php _e( 'Purge Theme Caches', 'fictioneer' ); ?></a>
            </div>

          </div>
        </div>
      </div>

      <div class="fictioneer-card">
        <div class="fictioneer-card__wrapper">
          <h3 class="fictioneer-card__header"><?php _e( 'Migration Tools', 'fictioneer' ); ?></h3>
          <div class="fictioneer-card__content">

            <div class="fictioneer-card__row">
              <p><?php _e( '<strong>Append associated <em>published</em> chapters </strong> that are not already in the story’s chapter list. Enter the ID of the story, which can be found in the URL of the editor page. The chapters must be assigned to the story. Beware, ownership restrictions will be ignored!', 'fictioneer' ); ?></p>
            </div>

            <form class="fictioneer-card__row fictioneer-card__row--single-form-submit" action="<?php echo admin_url( 'admin-post.php' ); ?>" method="get">
              <input type="hidden" name="action" value="fictioneer_tools_append_chapters">
              <?php wp_nonce_field( 'fictioneer_tools_append_chapters', 'fictioneer_nonce' ); ?>
              <input name="story_id" placeholder="<?php _e( 'Story ID', 'fictioneer' ); ?>" type="text" autocomplete="off" style="max-width: 150px !important;" required>
              <input type="submit" name="preview" class="button button--secondary" value="<?php echo esc_attr_x( 'Preview', 'Migration tools chapter preview button.', 'fictioneer' ); ?>">
              <input type="submit" name="perform" class="button button--secondary" value="<?php echo esc_attr_x( 'Append Chapters', 'Migration tools chapter append button.', 'fictioneer' ); ?>">
            </form>

          </div>
        </div>
      </div>

      <div class="fictioneer-card" style="display: none;">
        <div class="fictioneer-card__wrapper">
          <h3 class="fictioneer-card__header"><?php _e( 'Repair Tools', 'fictioneer' ); ?></h3>
          <div class="fictioneer-card__content">

            <div class="fictioneer-card__row">
              <p><?php _e( '<strong>Repair or migrate legacy data.</strong> Regardless of planning and efforts, there can always be updates with changes so drastic that it may cause older databases to become invalid or outright broken. These actions attempt to fix such issues.', 'fictioneer' ); ?></p>
            </div>

            <div class="fictioneer-card__row fictioneer-card__row--buttons">
              <a class="button button--secondary disabled" href="<?php echo fictioneer_tool_action( 'fictioneer_fix_users' ); ?>"><?php _e( 'Fix Users', 'fictioneer' ); ?></a>
              <a class="button button--secondary disabled" href="<?php echo fictioneer_tool_action( 'fictioneer_fix_stories' ); ?>"><?php _e( 'Fix Stories', 'fictioneer' ); ?></a>
              <a class="button button--secondary disabled" href="<?php echo fictioneer_tool_action( 'fictioneer_fix_chapters' ); ?>"><?php _e( 'Fix Chapters', 'fictioneer' ); ?></a>
              <a class="button button--secondary disabled" href="<?php echo fictioneer_tool_action( 'fictioneer_fix_recommendations' ); ?>"><?php _e( 'Fix Recommendations', 'fictioneer' ); ?></a>
              <a class="button button--secondary disabled" href="<?php echo fictioneer_tool_action( 'fictioneer_fix_collections' ); ?>"><?php _e( 'Fix Collections', 'fictioneer' ); ?></a>
              <a class="button button--secondary disabled" href="<?php echo fictioneer_tool_action( 'fictioneer_fix_pages' ); ?>"><?php _e( 'Fix Pages', 'fictioneer' ); ?></a>
              <a class="button button--secondary disabled" href="<?php echo fictioneer_tool_action( 'fictioneer_fix_posts' ); ?>"><?php _e( 'Fix Posts', 'fictioneer' ); ?></a>
            </div>

            <?php if ( fictioneer_caching_active() ) : ?>

              <hr>

              <div class="fictioneer-card__row">
                <p><?php _e( '<strong>Reset post relationship registry.</strong> Warning, this should only ever be done if the registry either causes problems or has become corrupted. Without the registry, the cache purge assistance cannot find referenced posts. You will have to re-save every relevant post to rebuild the registry. Great fun.', 'fictioneer' ); ?></p>
              </div>

              <div class="fictioneer-card__row">
                <details>
                  <summary><?php _e( 'Show registry array', 'fictioneer' ); ?></summary>
                  <code style="font-size: 10px; height: 256px;">
                    <pre><?php print_r( fictioneer_get_relationship_registry() ); ?></pre>
                  </code>
                </details>
              </div>

              <div class="fictioneer-card__row fictioneer-card__row--buttons">
                <a class="button button--secondary" id="reset-post-relationship-registry" data-click="warning-dialog" data-dialog="<?php esc_attr_e( 'Are you sure? Repopulating the registry requires re-saving every single post or page you want to be covered. Manually.', 'fictioneer' ); ?>" href="<?php echo fictioneer_tool_action( 'fictioneer_reset_post_relationship_registry' ); ?>"><?php _e( 'Reset Registry', 'fictioneer' ); ?></a>
              </div>

            <?php endif; ?>

          </div>
        </div>
      </div>

      <?php do_action( 'fictioneer_admin_settings_tools' ); ?>

    </div>
  </div>

</div>
