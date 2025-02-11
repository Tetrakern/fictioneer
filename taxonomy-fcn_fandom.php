<?php
/**
 * Fandom custom taxonomy archives
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0.0
 * @see partials/_archive-loop.php
 */


// Header
get_header();

// Setup
$term = get_queried_object();
$parent = get_term_by( 'id', $term->parent, get_query_var( 'taxonomy' ) );
$output = [];

// Heading
$output['heading'] = '<h1 class="archive__heading">' . sprintf(
  _n(
    '<span class="archive__heading-number">%1$s</span> Result in the <em>"%2$s"</em> fandom %3$s',
    '<span class="archive__heading-number">%1$s</span> Results in the <em>"%2$s"</em> fandom %3$s',
    $term->count,
    'fictioneer'
  ),
  $term->count,
  single_tag_title( '', false ),
  $parent ? sprintf( _x( '(%s)', 'Taxonomy page parent suffix.', 'fictioneer' ), $parent->name ) : ''
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
    'fictioneer_query_name' => 'fcn_genre',
    'smallest' => .625,
    'largest' => 1.25,
    'unit' => 'rem',
    'number' => 0,
    'taxonomy' => ['fcn_fandom'],
    'exclude' => $term->term_id,
    'show_count' => true,
    'pad_counts' => true,
    'echo' => false
  )
) . '</div>';

?>

<main id="main" class="main archive fandom-archive">

  <?php do_action( 'fictioneer_main', 'fandom-archive' ); ?>

  <div class="main__wrapper">

    <?php do_action( 'fictioneer_main_wrapper' ); ?>

    <article class="archive__article">

      <header class="archive__header"><?php

        echo implode( '', apply_filters(
          'fictioneer_filter_archive_header',
          $output,
          'fcn_fandom',
          $term,
          $parent
        ));

      ?></header>

      <?php get_template_part( 'partials/_archive-loop', null, array( 'taxonomy' => 'fcn_fandom' ) ); ?>

    </article>

  </div>

  <?php do_action( 'fictioneer_main_end', 'fandom-archive' ); ?>

</main>

<?php
  // Footer arguments
  $footer_args = array(
    'post_type' => null,
    'post_id' => null,
    'template' => 'taxonomy-fcn_fandom.php',
    'breadcrumbs' => array(
      [fcntr( 'frontpage' ), get_home_url()],
      [sprintf( __( 'Results for Fandom "%s"', 'fictioneer' ), single_tag_title( '', false ) ), null]
    )
  );

  // Get footer with breadcrumbs
  get_footer( null, $footer_args );
?>
