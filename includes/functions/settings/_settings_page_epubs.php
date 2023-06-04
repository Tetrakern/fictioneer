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
$purge_all_url = wp_nonce_url( admin_url( 'admin-post.php?action=purge_all_epubs' ), 'purge_all_epubs', 'fictioneer_nonce' );

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

<div class="fictioneer-ui fictioneer-settings">

  <?php fictioneer_settings_header( 'epubs' ); ?>

  <?php wp_nonce_field( 'fictioneer_settings_actions', 'fictioneer_admin_nonce' ); ?>

  <div class="fictioneer-settings__content">

    <div class="tab-content">

      <div class="card">
        <div class="card-wrapper">
          <h3 class="card-header"><?php _e( 'Generated ePUBs', 'fictioneer' ) ?></h3>
          <div class="card-content">
            <div class="overflow-horizontal overflow-table">
              <div class="table image-cover">
                <table class="th-no-wrap">

                  <thead>
                    <tr>
                      <th><?php _e( 'Story', 'fictioneer' ) ?></th>
                      <th><?php _e( 'File Name (.epub)', 'fictioneer' ) ?></th>
                      <th class="text-center"><?php _e( 'Chapters', 'fictioneer' ) ?></th>
                      <th class="text-center"><?php _e( 'Words', 'fictioneer' ) ?></th>
                      <th class="text-center"><?php _e( 'Downloads', 'fictioneer' ) ?></th>
                      <th class="text-center"><?php _e( 'Version', 'fictioneer' ) ?></th>
                      <th><?php _e( 'Generated', 'fictioneer' ) ?></th>
                      <th><?php _e( 'Size', 'fictioneer' ) ?></th>
                    </tr>
                  </thead>

                  <tbody class="alternate-rows">
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
                        $story_data = fictioneer_get_story_data( $story->ID );
                      }

                      $downloads = $story ? get_post_meta( $story->ID, 'fictioneer_epub_downloads', true ) : 0;
                      $downloads = is_array( $downloads ) ? $downloads : [0];
                      $number_of_versions = count( $downloads );

                      $epub_list_id += 1;
                    ?>
                      <tr id="epub-id-<?php echo $epub_list_id; ?>">

                        <?php if ( $story ) : ?>

                          <td class="no-wrap">
                            <div>
                              <a class="bold" href="<?php echo get_the_permalink( $story ); ?>"><?php echo get_the_title( $story ); ?></a>
                            </div>
                            <div class="inline-row-actions">
                              <span class="edit">
                                <a href="<?php echo get_edit_post_link( $story ); ?>"><?php _e( 'Edit Story', 'fictioneer' ) ?></a> |
                              </span>
                              <span class="delete">
                                <label class="button-delete-epub" data-id="<?php echo $epub_list_id; ?>" data-filename="<?php echo $name; ?>">
                                  <?php _e( 'Delete', 'fictioneer' ) ?>
                                </label> |
                              </span>
                              <span class="download">
                                <a href="<?php echo get_site_url(); ?>/download-epub/<?php echo $story->ID ?>" download><?php _e( 'Download', 'fictioneer' ) ?></a>
                              </span>
                            </div>
                          </td>

                          <td class="no-wrap"><?php echo esc_html( $name ); ?></td>

                          <td class="no-wrap text-center"><?php echo $story_data['chapter_count']; ?></td>

                          <td class="no-wrap text-center"><?php echo $story_data['word_count_short']; ?></td>

                        <?php else : ?>

                          <td class="no-wrap">
                            <div><?php _e( 'Story not found.', 'fictioneer' ); ?></div>
                            <div class="inline-row-actions">
                              <span class="delete">
                                <label class="button-delete-epub" data-id="<?php echo $epub_list_id; ?>" data-filename="<?php echo $name; ?>">
                                  <?php _e( 'Delete', 'fictioneer' ) ?>
                                </label>
                              </span>
                            </div>
                          </td>

                          <td class="no-wrap"><?php echo esc_html( $name ); ?></td>

                          <td class="no-wrap text-center"><?php _e( 'n/a', 'fictioneer' ); ?></td>

                          <td class="no-wrap text-center"><?php _e( 'n/a', 'fictioneer' ); ?></td>

                        <?php endif; ?>

                        <td class="no-wrap text-center"><?php echo end( $downloads ); ?> (<?php echo array_sum( $downloads ); ?>)</td>

                        <td class="no-wrap text-center"><?php echo $number_of_versions; ?></td>

                        <td class="no-wrap">
                          <?php
                            printf(
                              _x( '%1$s at %2$s', 'Table date and time column', 'fictioneer' ),
                              date( get_option( 'date_format' ), $date ),
                              date( get_option( 'time_format' ), $date )
                            );
                          ?>
                        </td>

                        <td class="no-wrap"><?php echo esc_html( $size ); ?></td>

                      </tr>
                    <?php
                    }

                    if ( ! $current_epubs ) {
                    ?>
                      <tr>
                        <td colspan="99"><?php _e( 'Nothing to display.', 'fictioneer' ) ?></td>
                      </tr>
                    <?php
                    }
                    ?>
                  </tbody>

                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="action-row split mt-32">
        <div class="left-actions">
          <a class="button secondary" id="button-purge-all-epubs" onclick="return confirm( '<?php esc_attr_e( 'Are you sure you want to purge all ePUBs?', 'fictioneer' ) ?>' );" href="<?php echo $purge_all_url; ?>"><?php _e( 'Purge All', 'fictioneer' ); ?></a>
        </div>
        <div class="right-actions">
          <?php if ( $epubs ) fictioneer_admin_pagination( $epubs_per_page, ceil( $count / $epubs_per_page ), $count ); ?>
        </div>
      </div>

      <?php
        do_action(
          'fictioneer_admin_settings_epubs',
          array(
            'page' => $page_number,
            'epubs_per_page' => $epubs_per_page,
            'epub_dir' => $epub_dir,
            'epubs' => $epubs,
            'current_epubs' => $current_epubs
          )
        );
      ?>

    </div>
  </div>
</div>
