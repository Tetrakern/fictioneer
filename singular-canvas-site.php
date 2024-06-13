<?php
/**
 * Template Name: Canvas (Site)
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.20.0
 */


// Setup
$post_id = get_the_ID();

// Header
get_header( null, array( 'blank' => 1 ) );

?>

<main id="main" class="canvas">

  <?php
    while ( have_posts() ) {
      the_post();

      // Setup
      $title = fictioneer_get_safe_title( $post_id, 'singular-canvas-site' );
      $this_breadcrumb = [ $title, get_the_permalink() ];

      the_content();
    }
  ?>

</main>

<?php
  // Footer arguments
  $footer_args = array(
    'blank' => 1,
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
