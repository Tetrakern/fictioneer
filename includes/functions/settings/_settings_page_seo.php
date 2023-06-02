<?php
/**
 * Partial: SEO Settings
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.7
 */
?>

<?php

// Setup
$page_number = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
$purge_all_url = wp_nonce_url( admin_url( 'admin-post.php?action=purge_all_seo_schemas' ), 'purge_all_seo_schemas', 'fictioneer_nonce' );
$purge_meta_url = wp_nonce_url( admin_url( 'admin-post.php?action=purge_seo_meta_caches' ), 'purge_seo_meta_caches', 'fictioneer_nonce' );

?>

<div class="fictioneer-ui fictioneer-settings">

  <?php fictioneer_settings_header( 'seo' ); ?>

  <div class="fictioneer-settings__content">

    <div class="tab-content">

      <?php do_action( 'fictioneer_admin_settings_seo' ); ?>

      <div class="card">
        <div class="card-wrapper relative">

          <h3 class="card-header"><?php _e( 'SEO Data', 'fictioneer' ) ?></h3>

          <div class="card-content">

            <?php if ( $page_number < 2 ) : ?>
              <p class="description row"><?php
                printf(
                  __( 'The following table lists the generated Open Graph metadata used for rich snippets in search engine results and social media embeds. Whether these services actually display the offered data is entirely up to them. You cannot force Google to show your custom description, for example. After all, you could write anything in there. Schemas are generated when a post is first visited and cached until modified or purged. Note that not all post types get a schema. You can set a default OG image under Site Identity in the <a href="%s" target="_blank">customizer</a>.', 'fictioneer' ),
                  wp_customize_url()
                );
              ?></p>
            <?php endif; ?>

            <div class="overflow-horizontal overflow-table">
              <div class="table image-cover">
                <table class="th-no-wrap">

                  <thead>
                    <tr>
                      <th class="text-center"><?php _e( 'OG Image', 'fictioneer' ) ?></th>
                      <th><?php _e( 'OG Title', 'fictioneer' ) ?></th>
                      <th><?php _e( 'OG Description & Schema', 'fictioneer' ) ?></th>
                      <th><?php _e( 'Type', 'fictioneer' ) ?></th>
                      <th><?php _e( 'Modified', 'fictioneer' ) ?></th>
                    </tr>
                  </thead>

                  <?php if ( fictioneer_seo_plugin_active() ) : ?>

                    <tbody class="alternate-rows">
                      <tr>
                        <td colspan="99"><?php _e( 'Disabled due to incompatible SEO plugin being active.', 'fictioneer' ) ?></td>
                      </tr>
                    </tbody>

                  <?php else : ?>

                    <tbody class="alternate-rows">

                      <?php
                        $per_page = 50;

                        $query_args = array(
                          'post_type' => ['page', 'post', 'fcn_story', 'fcn_chapter', 'fcn_recommendation', 'fcn_collection'],
                          'post_status' => array( 'publish' ),
                          'orderby' => 'modified',
                          'order' => 'DESC',
                          'paged' => $page_number,
                          'posts_per_page' => $per_page,
                          'fields' => 'ids',
                          'update_post_meta_cache' => false,
                          'update_post_term_cache' => false
                        );

                        $query = new WP_Query( $query_args );
                        $post_ids = $query->posts;
                      ?>

                      <?php foreach ( $post_ids as $post_id ) : ?>

                        <?php
                          $schema = get_post_meta( $post_id, 'fictioneer_schema', true );
                          $schema_text = stripslashes( json_encode( json_decode( $schema ), JSON_PRETTY_PRINT ) );
                          $image = get_post_meta( $post_id, 'fictioneer_seo_og_image_cache', true );
                          $image_url = $image ? $image['url'] : get_template_directory_uri() . '/img/no_image_placeholder.svg';
                          $type = get_post_type( $post_id );
                          $type_label = get_post_type_object( $type )->labels->singular_name;
                        ?>

                        <tr id="schema-<?php echo $post_id; ?>">

                          <td>
                            <img src="<?php echo $image_url ?>" width="72" height="48">
                          </td>

                          <td class="no-wrap">
                            <div>
                              <a class="bold" href="<?php echo get_the_permalink( $post_id ); ?>"><?php echo fictioneer_get_seo_title( $post_id ); ?></a>
                            </div>
                            <div class="inline-row-actions">
                              <span class="edit">
                                <a href="<?php echo get_edit_post_link( $post_id ) ?>">
                                  <?php printf( __( 'Edit %s', 'fictioneer' ), $type_label ); ?>
                                </a> |
                              </span>
                              <?php if ( $schema ) : ?>
                                <span class="delete">
                                  <label class="button-purge-schema" data-id="<?php echo $post_id; ?>">
                                    <?php _e( 'Purge Schema', 'fictioneer' ) ?>
                                  </label> |
                                </span>
                              <?php endif; ?>
                              <span class="view">
                                <a href="<?php echo get_permalink( $post_id ) ?>"><?php _e( 'View', 'fictioneer' ); ?></a>
                              </span>
                            </div>
                          </td>

                          <td class="text-blob">
                            <?php if ( $schema ) : ?>
                              <details class="details">
                                <summary>
                                  <span class="no-schema-note subtext <?php if ( $schema ) echo 'hidden'; ?>"><?php _e( '[No Schema]', 'fictioneer' ) ?></span>
                                  <?php echo fictioneer_get_seo_description( $post_id ); ?>
                                </summary>
                                <div class="textarea"><textarea class="fs-xxs" rows="8" readonly><?php echo $schema_text; ?></textarea></div>
                              </details>
                            <?php else : ?>
                              <span class="no-schema-note subtext"><?php _e( '[No Schema]', 'fictioneer' ) ?></span>
                              <?php echo fictioneer_get_seo_description( $post_id ); ?>
                            <?php endif; ?>
                          </td>

                          <td><?php echo $type_label ?></td>

                          <td style="min-width: 128px;"><?php
                            printf(
                              _x( '%1$s at %2$s', 'Table date and time column', 'fictioneer' ),
                              get_the_modified_date( get_option( 'date_format' ), $post_id ),
                              get_the_modified_date( get_option( 'time_format' ), $post_id )
                            );
                          ?></td>

                        </tr>

                      <?php endforeach; ?>

                    </tbody>

                  <?php endif; ?>

                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="action-row split mt-32">
        <div class="left-actions">
          <a class="button secondary" id="button-purge-all-schemas" onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to purge all schemas? They will be regenerated on visit.', 'fictioneer' ) ?>' );" href="<?php echo $purge_all_url; ?>"><?php _e( 'Purge All Schemas', 'fictioneer' ); ?></a>
          <a class="button secondary" id="button-purge-all-meta" onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to purge all SEO meta caches? They will be regenerated on visit.', 'fictioneer' ) ?>' );" href="<?php echo $purge_meta_url; ?>"><?php _e( 'Purge SEO Meta Caches', 'fictioneer' ); ?></a>
        </div>
        <div class="right-actions">
          <?php if ( $query->have_posts() ) fictioneer_admin_pagination( $per_page, $query->max_num_pages, $query->found_posts ); ?>
        </div>
      </div>

    </div>

  </div>
</div>
