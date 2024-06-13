<?php
/**
 * Template Name: Canvas (Main)
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.20.0
 */


// Setup
$post_id = get_the_ID();

// Header
get_header();

?>

<main id="main" class="canvas">

  <?php
    while ( have_posts() ) {
      the_post();

      // Setup
      $title = fictioneer_get_safe_title( $post_id, 'singular-canvas-main' );
      $this_breadcrumb = [ $title, get_the_permalink() ];

      the_content();
    }
  ?>

</main>

<?php
  // Footer arguments
  $footer_args = array(
    'post_type' => 'page',
    'post_id' => $post_id,
    'breadcrumbs' => array(
      [fcntr( 'frontpage' ), get_home_url()]
    )
  );

  // Add current breadcrumb
  $footer_args['breadcrumbs'][] = $this_breadcrumb;

  // Get footer with breadcrumbs
  get_footer( null, $footer_args );
?>
