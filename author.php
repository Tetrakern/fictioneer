<?php
/**
 * Author
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.6.0
 * @see fictioneer_clause_sticky_stories()
 */


// Get queried author
$author_id = get_queried_object_id();
$author = get_userdata( $author_id );

$has_valid_post = (bool) $wpdb->get_var(
  $wpdb->prepare(
    "SELECT 1
     FROM {$wpdb->posts}
     WHERE post_status = 'publish'
       AND post_author = %d
       AND post_type IN ('fcn_story', 'fcn_chapter')
     LIMIT 1",
    $author_id
  )
);

// Return home if not a valid author
if (
  ! FICTIONEER_ENABLE_ALL_AUTHOR_PROFILES &&
  ( ! fictioneer_is_author( $author_id ) || ! $has_valid_post )
) {
  wp_redirect( home_url() );
  exit();
}

// Setup
$current_url = get_author_posts_url( $author_id );
$current_tab = sanitize_key( $_GET['tab'] ?? '' );
$current_page = get_query_var( 'pg', 1 ) ?: 1;
$order = fictioneer_sanitize_query_var( $_GET['order'] ?? 0, ['desc', 'asc'], 'desc' );
$author_page = get_the_author_meta( 'fictioneer_author_page', $author_id );
$author_page = $author_page > 0 ? $author_page : false;
$author_statistics = fictioneer_get_author_statistics( $author_id );
$max_pages = 1;
$tabs = [];

// Stories tab
$tabs['stories'] = array(
  'name' => __( 'Stories', 'fictioneer' ),
  'query_args' => array(
    'fictioneer_query_name' => 'author_stories',
    'post_type' => 'fcn_story',
    'author' => $author_id,
    'orderby' => 'modified',
    'paged' => $current_page,
    'order' => $order
  ),
  'classes' => [],
  'empty' => __( 'No stories published yet.', 'fictioneer' )
);

// Chapters tab
$tabs['chapters'] = array(
  'name' => __( 'Chapters', 'fictioneer' ),
  'query_args' => array(
    'fictioneer_query_name' => 'author_chapters',
    'post_type' => 'fcn_chapter',
    'author' => $author_id,
    'paged' => $current_page,
    'order' => $order
  ),
  'classes' => [],
  'empty' => __( 'No chapters published yet.', 'fictioneer' )
);

// Recommendations tab
if ( user_can( $author_id, 'publish_fcn_recommendations' ) ) {
  $tabs['recommendations'] = array(
    'name' => __( 'Recommendations', 'fictioneer' ),
    'query_args' => array(
      'fictioneer_query_name' => 'author_recommendations',
      'post_type' => 'fcn_recommendation',
      'author' => $author_id,
      'paged' => $current_page,
      'order' => $order
    ),
    'classes' => [],
    'empty' => __( 'No recommendations published yet.', 'fictioneer' )
  );
}

// Use first tab if queried tab is not available
if ( ! array_key_exists( $current_tab, $tabs ) ) {
  $current_tab = array_key_first( $tabs );
}

// Select tab
$tabs[ $current_tab ]['classes'][] = '_current';

// Header
get_header();

?>

<main id="main" class="main author-page singular">

  <?php do_action( 'fictioneer_main', 'author' ); ?>

  <div class="main__wrapper">

    <?php do_action( 'fictioneer_main_wrapper' ); ?>

    <article class="singular__article author-page__article">

      <?php
        $title = $author_page ? trim( get_the_title( $author_page ) ) : $author->display_name;
        $title = empty( $title ) ? $author->display_name : $title;
        $order_link = add_query_arg(
          array(
            'tab' => $current_tab,
            'order' => $order === 'desc' ? 'asc' : 'desc'
          ),
          $current_url
        ) . '#main';
      ?>

      <header class="singular__header">
        <h1 class="singular__title"><?php echo $title; ?></h1>
      </header>

      <?php if ( $author_page || ! empty( $author->user_description ) ) : ?>
        <section class="singular__content author-page__content content-section">
          <?php
            if ( $author_page ) {
              $post = get_post( $author_page );
              setup_postdata( $post );
              the_content();
              wp_reset_postdata();
            } elseif ( ! empty( $author->user_description ) ) {
              echo $author->user_description;
            }
           ?>
        </section>
      <?php endif; ?>

      <section class="statistics spacing-top">
        <div class="statistics__inline-stat">
          <strong><?php _e( 'Stories', 'fictioneer' ); ?></strong>
          <span><?php echo number_format_i18n( $author_statistics['story_count'] ); ?></span>
        </div>
        <div class="statistics__inline-stat">
          <strong><?php _e( 'Chapters', 'fictioneer' ); ?></strong>
          <span><?php echo number_format_i18n( $author_statistics['chapter_count'] ); ?></span>
        </div>
        <div class="statistics__inline-stat">
          <strong><?php _ex( 'Words', 'Word count caption in statistics.', 'fictioneer' ); ?></strong>
          <span><?php echo fictioneer_shorten_number( $author_statistics['word_count'] ); ?></span>
        </div>
        <div class="statistics__inline-stat">
          <strong><?php _e( 'Comments', 'fictioneer' ); ?></strong>
          <span><?php echo number_format_i18n( $author_statistics['comment_count'] ); ?></span>
        </div>
        <div class="statistics__inline-stat">
          <strong><?php _e( 'Reading', 'fictioneer' ); ?></strong>
          <span><?php echo fictioneer_get_reading_time_nodes( $author_statistics['word_count'] ); ?></span>
        </div>
      </section>

      <section id="tabs" class="scroll-margin-top author-page__tabs tabs-wrapper spacing-top">
        <div class="tabs">
          <?php foreach ( $tabs as $key => $value ) : ?>
            <a
              href="<?php echo esc_url( add_query_arg( array( 'tab' => $key, 'order' => $order ), $current_url ) . '#main'); ?>"
              class="tabs__item <?php echo implode( ' ', $value['classes'] )?>"
            ><?php echo $value['name']; ?></a>
          <?php endforeach; ?>
        </div>
        <div class="author-page__sorting">
          <a class="list-button _order <?php echo $order == 'desc' ? '_on' : '_off'; ?>" href="<?php echo esc_url( $order_link ); ?>">
            <i class="fa-solid fa-arrow-up-short-wide _off"></i>
            <i class="fa-solid fa-arrow-down-wide-short _on"></i>
          </a>
        </div>
      </section>

      <?php
        // Setup pagination (cannot use main pagination due to different sub-queries)
        $pag_args = array(
          'base' => add_query_arg( 'pg', '%#%' ),
          'format' => '?pg=%#%',
          'current' => max( 1, $current_page ),
          'prev_text' => fcntr( 'previous' ),
          'next_text' => fcntr( 'next' ),
          'add_args' => $current_tab ? array( 'tab' => $current_tab ) : null,
          'add_fragment' => '#tabs',
          'total' => 0
        );
      ?>

      <section id="list" class="author-page__list container-inline-size">
        <ul class="card-list _no-mutation-observer">
          <?php
            $list_items = fictioneer_get_card_list(
              $tabs[ $current_tab ]['query_args']['post_type'],
              $tabs[ $current_tab ]['query_args'],
              $tabs[ $current_tab ]['empty'],
              array(
                'show_latest' => true
              )
            );

            // Output list
            echo $list_items['html'];

            // Update pagination arguments
            $pag_args['total'] = $list_items['query']->max_num_pages ?? 1;

            // Output pagination
            if ( $pag_args['total'] > 1 ) {
              echo '<li class="pagination">' . fictioneer_paginate_links( $pag_args ) . '</li>';
            }
          ?>
        </ul>
      </section>

    </article>

  </div>

  <?php do_action( 'fictioneer_main_end', 'author' ); ?>

</main>

<?php
  $author_index_url = intval( get_option( 'fictioneer_404_page', -1 ) ?: -1 );
  $author_slug = sprintf(
    _x( 'Author: %s', 'Author page no-index breadcrumb.', 'fictioneer' ),
    $author->display_name
  );

  // Footer arguments
  $footer_args = array(
    'post_type' => null,
    'post_id' => null,
    'template' => 'author.php',
    'breadcrumbs' => array(
      [fcntr( 'frontpage' ), get_home_url()]
    )
  );

  // Add author index breadcrumb (if set)
  $author_index_page_id = intval( get_option( 'fictioneer_authors_page', -1 ) ?: -1 );

  if ( $author_index_page_id > 0 ) {
    $index_page_title = trim( get_the_title( $author_index_page_id ) );
    $index_page_title = empty( $index_page_title ) ? __( 'Authors', 'fictioneer' ) : $index_page_title;

    $footer_args['breadcrumbs'][] = array(
      $index_page_title,
      fictioneer_get_assigned_page_link( 'fictioneer_authors_page' )
    );

    $author_slug = $author->display_name;
  }

  // Add current breadcrumb
  $footer_args['breadcrumbs'][] = array(
    $author_slug,
    null
  );

  // Get footer with breadcrumbs
  get_footer( null, $footer_args );
?>
