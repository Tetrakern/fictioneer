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
$per_page = 50;
$page_number = absint( $_GET['paged'] ?? 1 );
$purge_all_url = wp_nonce_url( admin_url( 'admin-post.php?action=fictioneer_purge_all_seo_schemas' ), 'fictioneer_purge_all_seo_schemas', 'fictioneer_nonce' );
$purge_meta_url = wp_nonce_url( admin_url( 'admin-post.php?action=fictioneer_purge_seo_meta_caches' ), 'fictioneer_purge_seo_meta_caches', 'fictioneer_nonce' );

// Query
$query_args = array(
  'post_type' => ['page', 'post', 'fcn_story', 'fcn_chapter', 'fcn_recommendation', 'fcn_collection'],
  'post_status' => 'publish',
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

<div class="fictioneer-settings">

  <?php fictioneer_settings_header( 'seo' ); ?>
  <?php wp_nonce_field( 'fictioneer_settings_actions', 'fictioneer_admin_nonce' ); ?>

  <div class="fictioneer-settings__content">
    <div class="fictioneer-single-column">

      <div class="fictioneer-card">
        <div class="fictioneer-card__wrapper">
          <h3 class="fictioneer-card__header"><?php _e( 'SEO Data', 'fictioneer' ); ?></h3>
          <div class="fictioneer-card__content">

            <div class="fictioneer-card__row">
              <p><?php
                printf(
                  __( 'This is the generated <a href="%s" target="_blank">Open Graph</a> metadata used in rich objects, such as embeds and search engine results. What is actually displayed is entirely up to the external service. Schemas are created when a post is first visited. Note that not all post types get a schema. You can set a default OG image under Site Identity in the <a href="%s" target="_blank">Customizer</a>.', 'fictioneer' ),
                  'https://ogp.me/',
                  wp_customize_url()
                );
              ?></p>
            </div>

            <div class="fictioneer-card__table">
              <table>

                <thead>
                  <tr>
                    <th class="cell-centered"><?php _e( 'OG Image', 'fictioneer' ); ?></th>
                    <th><?php _e( 'OG Title', 'fictioneer' ); ?></th>
                    <th><?php _e( 'OG Description & Schema', 'fictioneer' ); ?></th>
                    <th style="width: 120px;"><?php _e( 'Type', 'fictioneer' ); ?></th>
                    <th style="width: 160px;"><?php _e( 'Modified', 'fictioneer' ); ?></th>
                  </tr>
                </thead>

                <?php if ( fictioneer_seo_plugin_active() ) : ?>

                  <tbody>
                    <tr>
                      <td colspan="99"><?php _e( 'Disabled due to incompatible SEO plugin being active.', 'fictioneer' ); ?></td>
                    </tr>
                  </tbody>

                <?php else : ?>

                  <tbody>

                    <?php foreach ( $post_ids as $post_id ) : ?>

                      <?php
                        $schema = get_post_meta( $post_id, 'fictioneer_schema', true );
                        $schema_text = stripslashes( json_encode( json_decode( $schema ), JSON_PRETTY_PRINT ) );
                        $image = get_post_meta( $post_id, 'fictioneer_seo_og_image_cache', true );
                        $image_url = $image ? $image['url'] : get_template_directory_uri() . '/img/no_image_placeholder.svg';
                        $type = get_post_type( $post_id );
                        $type_label = get_post_type_object( $type )->labels->singular_name;
                        $title = mb_strimwidth( fictioneer_get_seo_title( $post_id ), 0, 48, '…' );
                        $link = get_the_permalink( $post_id );
                        $seo_description = fictioneer_get_seo_description( $post_id );
                      ?>

                      <tr id="schema-<?php echo $post_id; ?>">

                        <td>
                          <img src="<?php echo $image_url ?>" width="72" height="48">
                        </td>

                        <td class="cell-primary">
                          <a href="<?php echo $link; ?>"><?php echo $title; ?></a>
                          <div class="fictioneer-table-actions">
                            <a href="<?php edit_post_link( $post_id ); ?>" class="edit">
                              <?php _e( 'Edit', 'fictioneer' ); ?>
                            </a>
                            <?php if ( $schema ) : ?>
                              <span data-remove-on-delete>|</span>
                              <label class="delete button-purge-schema" data-id="<?php echo $post_id; ?>">
                                <?php _e( 'Purge Schema', 'fictioneer' ); ?>
                              </label>
                            <?php endif; ?>
                          </div>
                        </td>

                        <td class="cell-schema">
                          <?php if ( $schema ) : ?>
                            <details>
                              <summary><?php echo $seo_description; ?></summary>
                              <textarea rows="8" readonly><?php echo $schema_text; ?></textarea>
                            </details>
                          <?php else : ?>
                            <strong><?php _e( '[Empty]', 'fictioneer' ); ?></strong>
                            <?php echo $seo_description; ?>
                          <?php endif; ?>
                        </td>

                        <td class="cell-no-wrap"><?php echo $type_label ?></td>

                        <td class="cell-no-wrap"><?php
                          printf(
                            _x( '%1$s<br>at %2$s', 'Table date and time column.', 'fictioneer' ),
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

      <div class="fictioneer-actions">

        <div class="fictioneer-actions__group">

          <a class="button secondary" id="button-purge-all-schemas" data-click="purge-all-schemas" data-prompt="<?php esc_attr_e( 'Are you sure you want to purge all schemas? They will be regenerated on visit.', 'fictioneer' ); ?>" href="<?php echo $purge_all_url; ?>"><?php _e( 'Purge All Schemas', 'fictioneer' ); ?></a>

          <a class="button secondary" id="button-purge-all-meta" data-click="purge-all-meta" data-prompt="<?php esc_attr_e( 'Are you sure you want to purge all SEO meta caches? They will be regenerated on visit.', 'fictioneer' ); ?>" href="<?php echo $purge_meta_url; ?>"><?php _e( 'Purge SEO Meta Caches', 'fictioneer' ); ?></a>

        </div>

        <div class="fictioneer-actions__group">
          <?php
            if ( $query->have_posts() ) {
              fictioneer_admin_pagination( $per_page, $query->max_num_pages, $query->found_posts );
            }
          ?>
        </div>

      </div>

    </div>
  </div>

</div>
