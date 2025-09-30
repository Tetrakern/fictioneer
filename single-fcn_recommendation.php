<?php
/**
 * Custom Post Type: Recommendation
 *
 * Shows a single recommendation.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0.0
 * @see partials/_recommendation-header.php
 */


// Setup
$password_required = post_password_required();

// Header
get_header( null, array( 'type' => 'fcn_recommendation' ) );

?>

<main id="main" class="main recommendation">

  <?php do_action( 'fictioneer_main', 'recommendation' ); ?>

  <div class="main__wrapper">

    <?php do_action( 'fictioneer_main_wrapper' ); ?>

    <?php while ( have_posts() ) : the_post(); ?>

      <?php
        // Setup
        $title = fictioneer_get_safe_title( $post->ID, 'single-recommendation' );
        $this_breadcrumb = [ $title, get_the_permalink() ];

        // Arguments for hooks and templates/etc.
        $hook_args = array(
          'recommendation' => $post,
          'recommendation_id' => $post->ID,
          'title' => $title
        );
      ?>

      <article id="recommendation-<?php the_ID(); ?>" class="recommendation__article">

        <?php
          // Render article header
          get_template_part( 'partials/_recommendation-header', null, $hook_args );

          // Hook after header
          if ( ! $password_required ) {
            do_action( 'fictioneer_recommendation_after_header', $hook_args );
          }

          // Thumbnail
          if ( has_post_thumbnail() && ! $password_required ) {
            echo fictioneer_get_recommendation_page_cover( $post );
          }
        ?>

        <section class="recommendation__content content-section"><?php the_content(); ?></section>

        <?php if ( ! $password_required ) : ?>

          <?php do_action( 'fictioneer_recommendation_after_content', $hook_args ); ?>

          <footer class="recommendation__footer"><?php do_action( 'fictioneer_recommendation_footer', $hook_args ); ?></footer>

        <?php endif; ?>

      </article>

    <?php endwhile; ?>

  </div>

  <?php do_action( 'fictioneer_main_end', 'recommendation' ); ?>

</main>

<?php
  // Footer arguments
  $footer_args = array(
    'post_type' => 'fcn_recommendation',
    'post_id' => get_the_ID(),
    'breadcrumbs' => array(
      [fcntr( 'frontpage' ), get_home_url()]
    )
  );

  // Add recommendations list breadcrumb (if set)
  $rec_page_id = (int) ( get_option( 'fictioneer_recommendations_page', -1 ) ?: -1 );

  if ( $rec_page_id > 0 ) {
    $rec_page_title = trim( get_the_title( $rec_page_id ) );
    $rec_page_title = empty( $rec_page_title ) ? __( 'Recommendations', 'fictioneer' ) : $rec_page_title;

    $footer_args['breadcrumbs'][] = array(
      $rec_page_title,
      fictioneer_get_assigned_page_link( 'fictioneer_recommendations_page' )
    );
  }

  // Add current breadcrumb
  $footer_args['breadcrumbs'][] = $this_breadcrumb;

  // Get footer with breadcrumbs
  get_footer( null, $footer_args );
