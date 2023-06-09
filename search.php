<?php
/**
 * Search results
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.0
 */
?>

<?php

global $wp_query;

// Setup
$count = $wp_query->found_posts;

$post_type = $_GET['post_type'] ?? 'any';
$sentence = $_GET['sentence'] ?? '0';
$order = $_GET['order'] ?? 'desc';
$orderby = $_GET['orderby'] ?? 'modified';

$queried_genres = $_GET[ 'genres' ] ?? 0;
$queried_fandoms = $_GET[ 'fandoms' ] ?? 0;
$queried_characters = $_GET[ 'characters' ] ?? 0;
$queried_warnings = $_GET[ 'warnings' ] ?? 0;
$queried_tags = $_GET[ 'tags' ] ?? 0;

$queried_ex_genres = $_GET[ 'ex_genres' ] ?? 0;
$queried_ex_fandoms = $_GET[ 'ex_fandoms' ] ?? 0;
$queried_ex_characters = $_GET[ 'ex_characters' ] ?? 0;
$queried_ex_warnings = $_GET[ 'ex_warnings' ] ?? 0;
$queried_ex_tags = $_GET[ 'ex_tags' ] ?? 0;

$is_advanced_search = $post_type != 'any' || $sentence != '0' || $order != 'desc' || $orderby != 'modified';
$is_advanced_search = $is_advanced_search || $queried_tags || $queried_genres || $queried_fandoms || $queried_characters || $queried_warnings;
$is_advanced_search = $is_advanced_search || $queried_ex_tags || $queried_ex_genres || $queried_ex_fandoms || $queried_ex_characters || $queried_ex_warnings;

$hook_args = array(
  'post_type' => $post_type,
  'sentence' => $sentence,
  'order' => $order,
  'orderby' => $orderby,
  'queried_genres' => $queried_genres,
  'queried_fandoms' => $queried_fandoms,
  'queried_characters' => $queried_characters,
  'queried_warnings' => $queried_warnings,
  'queried_tags' => $queried_tags,
  'queried_ex_genres' => $queried_ex_genres,
  'queried_ex_fandoms' => $queried_ex_fandoms,
  'queried_ex_characters' => $queried_ex_characters,
  'queried_ex_warnings' => $queried_ex_warnings,
  'queried_ex_tags' => $queried_ex_tags,
  'is_advanced_search' => $is_advanced_search,
);

?>

<?php get_header(); ?>

<main id="main" class="main search-results">
  <div class="main-observer"></div>
  <?php do_action( 'fictioneer_main' ); ?>
  <div class="main__background polygon polygon--main background-texture"></div>
  <div class="main__wrapper">
    <?php do_action( 'fictioneer_main_wrapper' ); ?>

    <article id="search-results" class="search-results__article padding-left padding-right padding-top padding-bottom">

      <header class="search-results__header">
        <h1 class="search-results__title">
          <?php
            if ( $is_advanced_search || ! empty( trim( get_search_query() ) ) ) {
              $title_html = sprintf(
                _n(
                  '<span class="search-results__number">%s</span> Search Result',
                  '<span class="search-results__number">%s</span> Search Results',
                  $count,
                  'fictioneer'
                ),
                $count
              );
            } else {
             $title_html = __( 'Search', 'fictioneer' );
            }

            // Apply filters
            $title_html = apply_filters( 'fictioneer_filter_search_title', $title_html, $hook_args );

            // Output
            echo $title_html;
          ?>
        </h1>
      </header>

      <?php
        // Output search form ('pre_get_search_form' action fires here)
        get_search_form();

        // Actions after search form
        do_action( 'fictioneer_search_form_after' );
      ?>

      <?php if ( have_posts() ) : ?>
        <section class="search-results__content">
          <ul class="card-list _no-mutation-observer" id="search-result-list">
            <?php
              while ( have_posts() ) {
                the_post();

                // Setup
                $card_args = ['show_type' => true];

                // Cached?
                if ( fictioneer_caching_active() && ! fictioneer_private_caching_active() ) $card_args['cache'] = true;

                // Echo correct card
                fictioneer_echo_card( $card_args );
              }

              // Pagination
              $pag_args = array(
                'prev_text' => fcntr( 'previous' ),
                'next_text' => fcntr( 'next' ),
                'add_fragment' => '#search-result-list'
              );
            ?>
          </ul>
          <nav class="pagination _padding-top"><?php echo fictioneer_paginate_links( $pag_args ); ?></nav>
        </section>
      <?php else : ?>
        <section class="search-results__no-results"><?php do_action( 'fictioneer_search_no_results' ); ?></section>
      <?php endif; ?>

      <footer class="search-results__footer"><?php do_action( 'fictioneer_search_footer' ); ?></footer>

    </article>

  </div>
</main>

<?php
  // Footer arguments
  $footer_args = array(
    'post_type' => null,
    'post_id' => null,
    'template' => 'search.php',
    'breadcrumbs' => array(
      [fcntr( 'frontpage' ), get_home_url()],
      [__( 'Search Results', 'fictioneer' ), null]
    )
  );

  // Get footer with breadcrumbs
  get_footer( null, $footer_args );
?>
