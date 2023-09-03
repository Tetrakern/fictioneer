<?php
/**
 * Partial: ePUBs Settings
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.7
 */
?>

<?php

// Setup
$page_number = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
$epub_dir = wp_upload_dir()['basedir'] . '/epubs/';
$files = list_files( $epub_dir, 1 );
$epubs = [];
$purge_all_url = wp_nonce_url( admin_url( 'admin-post.php?action=fictioneer_purge_all_epubs' ), 'fictioneer_purge_all_epubs', 'fictioneer_nonce' );

// Get list of compiled ePUBs if any
foreach ( $files as $file ) {
  if ( is_file( $file ) ) {
    $info = pathinfo( $file );
    $extension = $info['extension'];

    if ( $extension === 'epub' ) {
      $epubs[] = $file;
    }
  }
}

// Prepare
$count = count( $epubs );
$epubs_per_page = 50;
$offset = $epubs_per_page * ( $page_number - 1 );
$current_epubs = array_slice( $epubs, $offset, $epubs_per_page, true );

?>

<div class="fictioneer-settings">

  <?php fictioneer_settings_header( 'epubs' ); ?>
  <?php wp_nonce_field( 'fictioneer_settings_actions', 'fictioneer_admin_nonce' ); ?>

  <div class="fictioneer-settings__content">
    <div class="fictioneer-single-column">

      <div class="fictioneer-card">
        <div class="fictioneer-card__wrapper">
          <h3 class="fictioneer-card__header"><?php _e( 'Generated ePUBs', 'fictioneer' ); ?></h3>
          <div class="fictioneer-card__content" style="padding-top: 0;">

            <div class="fictioneer-card__table">
              <table>

                <thead>
                  <tr>
                    <th><?php _e( 'Story', 'fictioneer' ); ?></th>
                    <th><?php _e( 'File Name (.epub)', 'fictioneer' ); ?></th>
                    <th class="cell-centered" style="width: 120px;"><?php _e( 'Chapters', 'fictioneer' ); ?></th>
                    <th class="cell-centered" style="width: 100px;"><?php _e( 'Words', 'fictioneer' ); ?></th>
                    <th class="cell-centered" style="width: 150px;"><?php _e( 'Downloads', 'fictioneer' ); ?></th>
                    <th><?php _e( 'Size', 'fictioneer' ); ?></th>
                    <th style="width: 160px;"><?php _e( 'Generated', 'fictioneer' ); ?></th>
                  </tr>
                </thead>

                <tbody>
                  <?php
                    $epub_list_id = 0;

                    foreach ( $current_epubs as $epub ) {
                      $info = pathinfo( $epub );
                      $size = size_format( filesize( $epub ) );
                      $name = $info['filename'];
                      $extension = $info['extension'];
                      $date = filectime( $epub );

                      // Find story
                      $stories = get_posts(
                        array(
                          'name' => $name,
                          'posts_per_page' => 1,
                          'post_type' => 'fcn_story'
                        )
                      );

                      $story = $stories ? $stories[0] : false;
                      $story_data = null;

                      if ( $story ) {
                        $story_data = fictioneer_get_story_data( $story->ID, false ); // Does not refresh comment count!
                      }

                      $downloads = $story ? get_post_meta( $story->ID, 'fictioneer_epub_downloads', true ) : 0;
                      $downloads = is_array( $downloads ) ? $downloads : [0];

                      $epub_list_id += 1;
                    ?>
                      <tr id="epub-id-<?php echo $epub_list_id; ?>">

                        <?php if ( $story ) : ?>

                          <td class="cell-primary">

                            <a href="<?php echo get_the_permalink( $story ); ?>"><?php echo get_the_title( $story ); ?></a>

                            <div class="fictioneer-table-actions">
                              <a href="<?php echo get_edit_post_link( $story ); ?>" class="edit"><?php _e( 'Edit Story', 'fictioneer' ); ?></a>
                              |
                              <label class="delete button-delete-epub" data-id="<?php echo $epub_list_id; ?>" data-filename="<?php echo $name; ?>">
                                <?php _e( 'Delete', 'fictioneer' ); ?>
                              </label>
                              |
                              <a href="<?php echo get_site_url(); ?>/download-epub/<?php echo $story->ID ?>" class="download" download><?php _e( 'Download', 'fictioneer' ); ?></a>
                            </div>

                          </td>

                          <td class="cell-no-wrap"><?php echo esc_html( $name ); ?></td>

                          <td class="cell-centered cell-no-wrap"><?php echo $story_data['chapter_count']; ?></td>

                          <td class="cell-centered cell-no-wrap"><?php echo $story_data['word_count_short']; ?></td>

                        <?php else : ?>

                          <td class="cell-no-wrap">

                            <div><?php _e( 'Story not found.', 'fictioneer' ); ?></div>

                            <div class="fictioneer-table-actions">
                              <label class="delete button-delete-epub" data-id="<?php echo $epub_list_id; ?>" data-filename="<?php echo $name; ?>">
                                <?php _e( 'Delete', 'fictioneer' ); ?>
                              </label>
                            </div>

                          </td>

                          <td class="cell-no-wrap"><?php echo esc_html( $name ); ?></td>

                          <td class="cell-centered cell-no-wrap"><?php _e( 'n/a', 'fictioneer' ); ?></td>

                          <td class="cell-centered cell-no-wrap"><?php _e( 'n/a', 'fictioneer' ); ?></td>

                        <?php endif; ?>

                        <td class="cell-centered cell-no-wrap"><?php echo end( $downloads ); ?> (<?php echo array_sum( $downloads ); ?>)</td>

                        <td class="cell-no-wrap"><?php echo esc_html( $size ); ?></td>

                        <td class="cell-no-wrap">
                          <?php
                            printf(
                              _x( '%1$s<br>at %2$s', 'Table date and time column.', 'fictioneer' ),
                              date( get_option( 'date_format' ), $date ),
                              date( get_option( 'time_format' ), $date )
                            );
                          ?>
                        </td>

                      </tr>
                    <?php
                    }
                  ?>
                  <tr class="row-nothing">
                    <td colspan="99"><?php _e( 'Nothing to display.', 'fictioneer' ); ?></td>
                  </tr>
                </tbody>

              </table>
            </div>

          </div>
        </div>
      </div>

      <div class="fictioneer-actions">

        <div class="fictioneer-actions__group">
          <a class="button secondary" id="button-purge-all-epubs" data-click="purge-all-epubs" data-prompt="<?php esc_attr_e( 'Are you sure you want to purge all ePUBs?', 'fictioneer' ); ?>" href="<?php echo $purge_all_url; ?>"><?php _e( 'Purge All', 'fictioneer' ); ?></a>
        </div>

        <div class="fictioneer-actions__group">
          <?php
            if ( $epubs ) {
              fictioneer_admin_pagination( $epubs_per_page, ceil( $count / $epubs_per_page ), $count );
            }
          ?>
        </div>

      </div>

    </div>
  </div>

</div>
