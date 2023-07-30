<?php
/**
 * Custom Post Type: Recommendation
 *
 * Shows a single recommendation.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0
 * @see partials/_recommendation-header.php
 */
?>

<?php get_header( null, array( 'type' => 'fcn_recommendation' ) ); ?>

<main id="main" class="main recommendation">
  <div class="observer main-observer"></div>
  <?php do_action( 'fictioneer_main' ); ?>
  <div class="main__background polygon polygon--main background-texture"></div>
  <div class="main__wrapper">
    <?php do_action( 'fictioneer_main_wrapper' ); ?>

    <?php while ( have_posts() ) : the_post(); ?>

      <?php
        // Setup
        $title = fictioneer_get_safe_title( $post->ID );
        $this_breadcrumb = [$title, get_the_permalink()];

        // Arguments for hooks and templates/etc.
        $hook_args = array(
          'recommendation' => $post,
          'recommendation_id' => $post->ID,
          'title' => $title
        );
      ?>

      <article id="recommendation-<?php the_ID(); ?>" class="recommendation__article padding-left padding-right padding-top padding-bottom">

        <?php
          // Render article header
          get_template_part( 'partials/_recommendation-header', null, $hook_args );

          // Hook after header
          do_action( 'fictioneer_recommendation_after_header', $hook_args );
        ?>

        <?php if ( has_post_thumbnail() ) echo fictioneer_get_recommendation_page_cover( $post ); ?>

        <section class="recommendation__content content-section"><?php the_content(); ?></section>

        <?php do_action( 'fictioneer_recommendation_after_content', $hook_args ); ?>

        <footer class="recommendation__footer"><?php do_action( 'fictioneer_recommendation_footer', $hook_args ); ?></footer>

      </article>

    <?php endwhile; ?>
  </div>
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
  $rec_page = intval( get_option( 'fictioneer_recommendations_page', -1 ) );

  if ( $rec_page > 0 ) {
    $rec_page_title = trim( get_the_title( $rec_page ) );
    $rec_page_title = empty( $rec_page_title ) ? __( 'Recommendations', 'fictioneer' ) : $rec_page_title;

    $footer_args['breadcrumbs'][] = array(
      $rec_page_title,
      get_permalink( $rec_page )
    );
  }

  // Add current breadcrumb
  $footer_args['breadcrumbs'][] = $this_breadcrumb;

  // Get footer with breadcrumbs
  get_footer( null, $footer_args );
?>
