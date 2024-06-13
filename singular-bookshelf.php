<?php
/**
 * Template Name: Bookshelf
 *
 * Page template for rendering all the user's custom lists such as Follows,
 * Reminders, and anything else. You can still add regular content here but
 * the point of that is highly debatable.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.0.0
 */


// Only for logged-in users...
if ( ! is_user_logged_in() || get_option( 'fictioneer_enable_public_cache_compatibility' ) ) {
  wp_safe_redirect( home_url() );
  exit();
}

// Setup
$user = wp_get_current_user();
$current_tab = sanitize_key( $_GET['tab'] ?? '' );
$order = fictioneer_sanitize_query_var( $_GET['order'] ?? 0, ['desc', 'asc'], 'desc' );
$current_page = get_query_var( 'pg', 1 ) ?: 1;
$max_pages = 1;
$tabs = [];

// Follows?
if ( get_option( 'fictioneer_enable_follows' ) ) {
  $follows = fictioneer_load_follows( $user );

  // Add to tabs
  $tabs['follows'] = array(
    'name' => fcntr( 'follows' ),
    'post_ids' => array_keys( $follows['data'] ),
    'classes' => [],
    'empty' => __( 'You are not following any stories.', 'fictioneer' )
  );
}

// Reminders?
if ( get_option( 'fictioneer_enable_reminders' ) ) {
  $reminders = fictioneer_load_reminders( $user );

  // Add to tabs
  $tabs['reminders'] = array(
    'name' => fcntr( 'reminders' ),
    'post_ids' => array_keys( $reminders['data'] ),
    'classes' => [],
    'empty' => __( 'You have not marked any stories to be read later.', 'fictioneer' )
  );
}

// Checkmarks?
if ( get_option( 'fictioneer_enable_checkmarks' ) ) {
  $checkmarks = fictioneer_load_checkmarks( $user );

  // Add to tabs
  $tabs['finished'] = array(
    'name' => __( 'Finished', 'fictioneer' ),
    'post_ids' => fictioneer_get_finished_checkmarks( $checkmarks ),
    'classes' => [],
    'empty' => __( 'You have not marked any stories as finished.', 'fictioneer' )
  );
}

// Return home if nothing to display
if ( count( $tabs ) < 1 ) {
  wp_redirect( home_url() );
  exit();
}

// Use first tab if queried tab is not available
if ( ! array_key_exists( $current_tab, $tabs ) ) {
  $current_tab = array_key_first( $tabs );
}

// Select tab
$tabs[ $current_tab ]['classes'][] = '_current';

// Header
get_header( null, array( 'no_index' => 1 ) );

?>

<main id="main" class="main singular bookshelf">

  <?php do_action( 'fictioneer_main', 'singular-bookshelf' ); ?>

  <div class="main__wrapper">

    <?php do_action( 'fictioneer_main_wrapper' ); ?>

    <?php while ( have_posts() ) : the_post(); ?>

      <?php
        // Inner setup
        $title = trim( get_the_title() );
        $title = empty( $title ) ? __( 'Bookshelf', 'fictioneer' ) : $title;
        $this_breadcrumb = [$title, get_the_permalink()];
        $current_url = get_the_permalink();
        $order_link = add_query_arg(
          array(
            'tab' => $current_tab,
            'order' => $order === 'desc' ? 'asc' : 'desc'
          ),
          $current_url
        ) . '#main';
      ?>

      <article id="singular-<?php the_ID(); ?>" class="singular__article padding-left padding-right padding-top padding-bottom">

        <?php if ( ! empty( $title ) ) : ?>
          <header class="singular__header">
            <h1 class="singular__title"><?php echo $title; ?></h1>
          </header>
        <?php endif; ?>

        <?php if ( get_the_content() ) : ?>
          <section class="singular__content content-section">
            <?php the_content(); ?>
          </section>
        <?php endif; ?>

        <?php if ( count( $tabs ) > 1 ) : ?>
          <section id="tabs" class="scroll-margin-top bookshelf__tabs tabs-wrapper spacing-top">
            <div class="tabs">
              <?php foreach ( $tabs as $key => $value ) : ?>
                <a href="<?php echo esc_url( add_query_arg( array( 'tab' => $key, 'order' => $order ), $current_url ) . '#main'); ?>" class="tabs__item <?php echo implode( ' ', $value['classes'] )?>"><?php
                  printf(
                    _x( '%s (%s)', 'Bookshelf tab name pattern: {Tab} ({Count})', 'fictioneer' ),
                    $value['name'],
                    number_format_i18n( count( $value['post_ids'] ) )
                  );
                ?></a>
              <?php endforeach; ?>
            </div>
            <div class="bookshelf__sorting">
              <a class="list-button _order <?php echo $order == 'desc' ? '_on' : '_off'; ?>" href="<?php echo esc_url( $order_link ); ?>">
                <i class="fa-solid fa-arrow-up-short-wide _off"></i>
                <i class="fa-solid fa-arrow-down-wide-short _on"></i>
              </a>
            </div>
          </section>
        <?php endif; ?>

        <?php
          // Setup pagination
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

        <section id="list" class="bookshelf__list container-inline-size">
          <ul class="card-list _no-mutation-observer">
            <?php
              $list_items = fictioneer_get_card_list(
                'story',
                array(
                  'fictioneer_query_name' => "bookshelf_{$current_tab}",
                  'post__in' => $tabs[ $current_tab ]['post_ids'],
                  'paged' => $current_page,
                  'order' => $order
                ),
                $tabs[ $current_tab ]['empty'],
                array( 'show_latest' => true )
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

        <footer class="singular__footer"><?php do_action( 'fictioneer_singular_footer' ); ?></footer>

      </article>

    <?php endwhile; ?>

  </div>

</main>

<?php
  // Get profile link
  $profile_link = fictioneer_get_assigned_page_link( 'fictioneer_user_profile_page' );
  $profile_link = $profile_link ? $profile_link : get_edit_profile_url();

  // Footer arguments
  $footer_args = array(
    'post_type' => 'page',
    'post_id' => get_the_ID(),
    'breadcrumbs' => array(
      [fcntr( 'frontpage' ), get_home_url()],
      [fcntr( 'account' ), $profile_link]
    )
  );

  // Add current breadcrumb
  $footer_args['breadcrumbs'][] = $this_breadcrumb;

  // Get footer with breadcrumbs
  get_footer( null, $footer_args );
?>
