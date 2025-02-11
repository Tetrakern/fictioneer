<?php
/**
 * Category archives
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 3.0
 * @see partials/_archive-loop.php
 */


// Header
get_header();

// Setup
$term = get_queried_object();
$output = [];

// Heading
$output['heading'] = '<h1 class="archive__heading">' . sprintf(
  _n(
    '<span class="archive__heading-number">%1$s</span> Result in the <em>"%2$s"</em> category',
    '<span class="archive__heading-number">%1$s</span> Results in the <em>"%2$s"</em> category',
    $term->count,
    'fictioneer'
  ),
  $term->count,
  single_cat_title( '', false )
) . '</h1>';

// Description
if ( ! empty( $term->description ) ) {
  $output['description'] = '<p class="archive__description">' . sprintf(
    __( '<strong>Definition:</strong> %s', 'fictioneer' ),
    $term->description
  ) . '</p>';
}

// Divider
$output['divider'] = '<hr class="archive__divider">';

// Tax cloud
$output['tax_cloud'] = '<div class="archive__tax-cloud">' . wp_tag_cloud(
  array(
    'fictioneer_query_name' => 'tag_cloud',
    'smallest' => .625,
    'largest' => 1.25,
    'unit' => 'rem',
    'number' => 0,
    'taxonomy' => ['category'],
    'exclude' => $term->term_id,
    'show_count' => true,
    'pad_counts' => true,
    'echo' => false
  )
) . '</div>';

?>

<main id="main" class="main archive category-archive">

  <?php do_action( 'fictioneer_main', 'category-archive' ); ?>

  <div class="main__wrapper">

    <?php do_action( 'fictioneer_main_wrapper' ); ?>

    <article class="archive__article">

      <header class="archive__header"><?php

        echo implode( '', apply_filters(
          'fictioneer_filter_archive_header',
          $output,
          'category',
          $term,
          null
        ));

      ?></header>

      <?php get_template_part( 'partials/_archive-loop', null, array( 'taxonomy' => 'category' ) ); ?>

    </article>

  </div>

  <?php do_action( 'fictioneer_main_end', 'category-archive' ); ?>

</main>

<?php
  // Footer arguments
  $footer_args = array(
    'post_type' => null,
    'post_id' => null,
    'template' => 'category.php',
    'breadcrumbs' => array(
      [fcntr( 'frontpage' ), get_home_url()],
      [sprintf( __( 'Results for Category "%s"', 'fictioneer' ), single_cat_title( '', false ) ), null]
    )
  );

  // Get footer with breadcrumbs
  get_footer( null, $footer_args );
?>
