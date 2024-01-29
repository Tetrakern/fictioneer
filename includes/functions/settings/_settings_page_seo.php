<?php
/**
 * Partial: SEO Settings
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.7.0
 */
?>

<?php

class Fictioneer_Seo_Table extends WP_List_Table {
  public $page = 1;
  public $per_page = 25;

  public function __construct() {
    parent::__construct([
      'singular' => 'epub',
      'plural' => 'epubs',
      'ajax' => false
    ]);
  }

  public function prepare_items() {
    // Setup
    $columns = $this->get_columns();
    $hidden = [];
    $sortable = $this->get_sortable_columns();
    $primary = 'title';

    $this->page = absint( $_GET['paged'] ?? 1 );
    $this->per_page = $this->get_items_per_page( 'fictioneer_seo_items_per_page', 25 );

    // Sort
    $orderby = array_intersect( [ strtolower( $_GET['orderby'] ?? 0 ) ], ['title', 'type', 'modified'] );
    $orderby = reset( $orderby ) ?: 'modified'; // Sanitized
    $order = array_intersect( [ strtolower( $_GET['order'] ?? 0 ) ], ['desc', 'asc'] );
    $order = reset( $order ) ?: 'desc'; // Sanitized

    // Query
    $query_args = array(
      'post_type' => ['page', 'post', 'fcn_story', 'fcn_chapter', 'fcn_recommendation', 'fcn_collection'],
      'post_status' => 'publish',
      'orderby' => $orderby,
      'order' => $order,
      'paged' => $this->page,
      'posts_per_page' => $this->per_page,
      'update_post_meta_cache' => false,
      'update_post_term_cache' => false
    );

    $query = new WP_Query( $query_args );

    // Pagination
    $this->set_pagination_args(
      array(
        'total_items' => $query->found_posts,
        'per_page' => $this->per_page,
        'total_pages' => ceil( $query->found_posts / $this->per_page )
      )
    );

    // Data
    $this->_column_headers = [ $columns, $hidden, $sortable, $primary ];
    $this->items = $query->posts;
  }

  public function get_columns() {
    return array(
      'title' => __( 'Title', 'fictioneer' ),
      'description' => __( 'Description', 'fictioneer' ),
      'type' => __( 'Type', 'fictioneer' ),
      'date' => __( 'Modified', 'fictioneer' )
    );
  }

  public function column_default( $item, $column_name ) {
    $schema = get_post_meta( $item->ID, 'fictioneer_schema', true );
    $schema_text = stripslashes( json_encode( json_decode( $schema ), JSON_PRETTY_PRINT ) );

    switch ( $column_name ) {
      case 'title':
        $image = fictioneer_get_seo_image( $item->ID );
        $image_url = $image ? $image['url'] : get_template_directory_uri() . '/img/no_image_placeholder.svg';
        $link = get_the_permalink( $item->ID );
        $title = fictioneer_truncate( fictioneer_get_seo_title( $item->ID ), 48 );
        $template = get_page_template_slug( $item->ID );
        $is_excluded = in_array( $template, ['singular-bookshelf.php', 'singular-bookmarks.php', 'user-profile.php'] );

        $actions = array(
          'edit' => '<a href="' . get_edit_post_link( $item->ID ) . '">' . __( 'Edit', 'fictioneer' ) . '</a>'
        );

        if ( $schema ) {
          $actions['schema'] = '<a data-dialog-target="schema-dialog">' . __( 'Schema', 'fcnl' ) . '</a>';
          $actions['trash'] = "<a href='#' data-purge-schema data-id='{$item->ID}'>" . __( 'Purge', 'fictioneer' ) . '</a>';
        }

        printf(
          _x( '%s<span>%s%s</span> %s<div style="clear: both;"></div>', 'SEO table row item title column.', 'fictioneer' ),
          '<img src="' . $image_url . '" width="31" height="46" class="row-thumbnail">',
          "<a href='{$link}' class='row-title'>{$title}</a></span>",
          $is_excluded ? _x( ' — Excluded', 'SEO table row item title column excluded note.', 'fictioneer' ) : '',
          $this->row_actions( $actions )
        );
        break;
      case 'description':
        echo fictioneer_get_seo_description( $item->ID );

        if ( $schema ) {
          echo "<div data-schema-id='{$item->ID}' hidden>{$schema_text}</div>";
        }

        break;
      case 'type':
        echo get_post_type_object( $item->post_type )->labels->singular_name;
        break;
      case 'date':
        printf(
          _x( '%1$s<br>at %2$s', 'Table date and time column.', 'fictioneer' ),
          get_the_modified_date( get_option( 'date_format' ), $item->ID ),
          get_the_modified_date( get_option( 'time_format' ), $item->ID )
        );
        break;
    }
  }

  public function single_row( $item ) {
    // Default
    $row_class = '';

    // Setup
    $schema = get_post_meta( $item->ID, 'fictioneer_schema', true );

    // Add class or not
    if ( ! $schema ) {
      $row_class .= ' no-schema';
    }

    // Output
    echo "<tr class='{$row_class}'>";
    $this->single_row_columns( $item );
    echo '</tr>';
  }

  protected function extra_tablenav( $which ) {
    // Setup
    $actions = [];

    // Purge All
    if ( current_user_can( 'manage_options' ) ) {
      $actions[] = sprintf(
        '<a href="#" class="button" data-action-purge-all-schemas data-disable-with="%s">%s</a>',
        esc_html( __( 'Purging…', 'fictioneer' ) ),
        __( 'Purge All', 'fictioneer' )
      );
    }

    // Output
    if ( ! empty( $actions ) ) {
      // Start HTML ---> ?>
      <div class="alignleft actions"><?php echo implode( ' ', $actions ); ?></div>
      <?php // <--- End HTML
    }
  }

  protected function get_sortable_columns() {
    return array(
      'title' => ['title', false],
      'type' => ['type', false],
      'date' => ['modified', false]
    );
  }
}

$seo_table = new Fictioneer_Seo_Table();
$seo_table->prepare_items();

?>

<div class="fictioneer-settings">

  <?php fictioneer_settings_header( 'seo' ); ?>
  <?php wp_nonce_field( 'fictioneer_settings_actions', 'fictioneer_admin_nonce' ); ?>

  <div class="fictioneer-settings__content">
    <div class="fictioneer-single-column">

      <div class="fictioneer-card">
        <div class="fictioneer-card__wrapper">
          <div class="fictioneer-card__content">
            <div class="fictioneer-card__row">
              <p><?php
                printf(
                  __( 'This is the generated <a href="%s" target="_blank">Open Graph</a> metadata used in rich objects, such as embeds and search engine results. What is actually displayed is entirely up to the external service. Schemas are created when a post is first visited. Note that not all post types or page templates get a schema. You can set a default OG image under <strong>Site Identity</strong> in the <a href="%s" target="_blank">Customizer</a>.', 'fictioneer' ),
                  'https://ogp.me/',
                  wp_customize_url()
                );
              ?></p>
            </div>
          </div>
        </div>
      </div>

      <form id="seo-table" method="get" class="fictioneer-form">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>">

        <?php $seo_table->display(); ?>
      </form>

    </div>
  </div>

  <dialog class="fictioneer-dialog" id="schema-dialog">
    <div class="fictioneer-dialog__header">
      <span><?php _e( 'Schema', 'fictioneer' ); ?></span>
    </div>
    <div class="fictioneer-dialog__content">
      <form>
        <div class="fictioneer-dialog__row">
          <textarea rows="16" data-target="schema-content" readonly></textarea>
        </div>
        <div class="fictioneer-dialog__actions">
          <button value="cancel" formmethod="dialog" class="button"><?php _e( 'Close', 'fictioneer' ); ?></button>
        </div>
      </form>
    </div>
  </dialog>

</div>
