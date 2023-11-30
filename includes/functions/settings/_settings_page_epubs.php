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

class Fictioneer_Epubs_Table extends WP_List_Table {
  private $epubs = [];
  private $current_epubs = [];

  public $page = 1;
  public $count = 0;
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
    $primary = 'story';

    $this->epubs = glob(wp_upload_dir()['basedir'] . '/epubs/*.epub');
    $this->page = absint( $_GET['paged'] ?? 1 );
    $this->count = count( $this->epubs );
    $this->per_page = $this->get_items_per_page( 'fictioneer_epubs_per_page', 25 );

    // Sort
    $orderby = $_GET['orderby'] ?? 'date';
    $order = $_GET['order'] ?? 'desc';

    switch ( $orderby ) {
      case 'story':
        $this->epubs = $this->sort_by_story( $this->epubs, $order );
        break;
      case 'words':
        $this->epubs = $this->sort_by_words( $this->epubs, $order );
        break;
      case 'size':
        $this->epubs = $this->sort_by_size( $this->epubs, $order );
        break;
      case 'date':
        $this->epubs = $this->sort_by_date( $this->epubs, $order );
        break;
    }

    // Pagination
    $this->current_epubs = array_slice(
      $this->epubs,
      $this->per_page * ( $this->page - 1 ),
      $this->per_page,
      true
    );

    $this->set_pagination_args(
      array(
        'total_items' => $this->count,
        'per_page' => $this->per_page,
        'total_pages' => ceil( $this->count / $this->per_page )
      )
    );

    // Data
    $this->_column_headers = [ $columns, $hidden, $sortable, $primary ];
    $this->items = $this->current_epubs;
  }

  public function get_columns() {
    return array(
      'story' => __( 'Story', 'fictioneer' ),
      'chapters' => __( 'Chapters', 'fictioneer' ),
      'words' => __( 'Words', 'fictioneer' ),
      'downloads' => __( 'Downloads', 'fictioneer' ),
      'size' => __( 'Size', 'fictioneer' ),
      'date' => __( 'Date', 'fictioneer' )
    );
  }

  public function column_default( $item, $column_name ) {
    $item = strval( $item );
    $info = pathinfo( $item );
    $file_name = $info['filename']; // Is the post ID
    $story = get_post( absint( $file_name ) );
    $downloads_version = '—';
    $downloads_total = '—';
    $story_data = array(
      'chapter_count' => '—',
      'word_count_short' => '—',
      'word_count' => '—'
    );

    if ( $story ) {
      $story_data = fictioneer_get_story_data( $story->ID, false );
      $download_stats = fictioneer_get_field( 'fictioneer_epub_downloads', $story->ID );
      $download_stats = is_array( $download_stats ) ? $download_stats : [0];
      $downloads_version = end( $download_stats );
      $downloads_total = array_sum( $download_stats );
    }

    switch ( $column_name ) {
      case 'story':
        $actions = [];
        $download_url = get_site_url() . "/download-epub/{$file_name}";

        if ( $story ) {
          $title = '<a href="' . get_the_permalink( $story ) . '" class="row-title">' . $story->post_title . '</a>';
          $actions['edit'] = '<a href="' . get_edit_post_link( $story ) . '">' . __( 'Edit', 'fictioneer' ) . '</a>';
          $actions['download'] = '<a href="' . $download_url . '" download>' . __( 'Download', 'fictioneer' ) . '</a>';
        } else {
          $title = '<div>' . sprintf( __( 'N/A (%s)', 'fictioneer' ), $file_name ) . '</div>';
        }

        $actions['trash'] = '<a href="#" data-action="delete-epub" data-name="' . $file_name . '">' . __( 'Delete', 'fictioneer' ) . '</a>';

        printf(
          '<span>%s</span> %s',
          $title,
          $this->row_actions( $actions )
        );
        break;
      case 'chapters':
        echo $story_data['chapter_count'];
        break;
      case 'words':
        $words = $story_data['word_count'];
        echo is_numeric( $words ) ? number_format_i18n( $words ) : $words;
        break;
      case 'downloads':
        echo "<span title='" . __( 'Version (Total)', 'fictioneer' ) . "'>{$downloads_version} ({$downloads_total})</span>";
        break;
      case 'size':
        echo esc_html( size_format( filesize( $item ) ) );
        break;
      case 'date':
        $date = filectime( $item );

        printf(
          _x( '%1$s<br>at %2$s', 'Table date and time column.', 'fictioneer' ),
          date( get_option( 'date_format' ), $date ),
          date( get_option( 'time_format' ), $date )
        );
        break;
    }
  }

  protected function extra_tablenav( $which ) {
    // Setup
    $actions = [];

    // Delete All
    if ( current_user_can( 'manage_options' ) ) {
      $actions[] = sprintf(
        '<a href="%s" class="button action" data-click="warning-dialog" data-dialog="%s">%s</a>',
        wp_nonce_url(
          admin_url( 'admin-post.php?action=fictioneer_delete_all_epubs' ),
          'fictioneer_delete_all_epubs',
          'fictioneer_nonce'
        ),
        esc_attr__( 'Are you sure you want to delete all ePUBs?', 'fictioneer' ),
        __( 'Delete All', 'fictioneer' )
      );
    }

    // Output
    if ( ! empty( $actions ) && $this->count > 0 ) {
      // Start HTML ---> ?>
      <div class="alignleft actions"><?php echo implode( ' ', $actions ); ?></div>
      <?php // <--- End HTML
    }
  }

  protected function get_sortable_columns() {
    return array(
      'story' => ['story', false],
      'words' => ['words', false],
      'size' => ['size', false],
      'date' => ['date', false]
    );
  }

  protected function sort_by_story( $data, $order ) {
    usort( $data, function( $a, $b ) use ( $order ) {
      $a_story = get_the_title( absint( pathinfo( $a )['filename'] ) );
      $b_story = get_the_title( absint( pathinfo( $b )['filename'] ) );

      if ( strtolower( $order ) === 'asc' ) {
        return $a_story < $b_story ? -1 : 1;
      } else {
        return $a_story < $b_story ? 1 : -1;
      }
    });

    return $data;
  }

  protected function sort_by_words( $data, $order ) {
    usort( $data, function( $a, $b ) use ( $order ) {
      $a_story = fictioneer_get_story_data( absint( pathinfo( $a )['filename'] ) );
      $b_story = fictioneer_get_story_data( absint( pathinfo( $b )['filename'] ) );

      if ( strtolower( $order ) === 'asc' ) {
        return absint( $a_story['word_count'] ) < absint( $b_story['word_count'] ) ? -1 : 1;
      } else {
        return absint( $a_story['word_count'] ) < absint( $b_story['word_count'] ) ? 1 : -1;
      }
    });

    return $data;
  }

  protected function sort_by_date( $data, $order ) {
    usort( $data, function( $a, $b ) use ( $order ) {
      $a_date = filectime( $a );
      $b_date = filectime( $b );

      if ( strtolower( $order ) === 'asc' ) {
        return $a_date < $b_date ? -1 : 1;
      } else {
        return $a_date < $b_date ? 1 : -1;
      }
    });

    return $data;
  }

  protected function sort_by_size( $data, $order ) {
    usort( $data, function( $a, $b ) use ( $order ) {
      $a_size = filesize( $a );
      $b_size = filesize( $b );

      if ( strtolower( $order ) === 'asc' ) {
          return $a_size < $b_size ? -1 : 1;
      } else {
          return $a_size < $b_size ? 1 : -1;
      }
    });

    return $data;
  }
}

$epubs_table = new Fictioneer_Epubs_Table();
$epubs_table->prepare_items();

?>

<div class="fictioneer-settings">

  <?php fictioneer_settings_header( 'epubs' ); ?>
  <?php wp_nonce_field( 'fictioneer_settings_actions', 'fictioneer_admin_nonce' ); ?>

  <div class="fictioneer-settings__content">
    <div class="fictioneer-single-column">
      <form id="epubs-table" method="get" class="fictioneer-form">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>">

        <?php $epubs_table->display(); ?>
      </form>
    </div>
  </div>

</div>
