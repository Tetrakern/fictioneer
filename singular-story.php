<?php
/**
 * Template Name: Story Page
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.14.0
 * @see comments.php
 */


// Setup
$post_id = get_the_ID();
$story_id = get_post_meta( $post_id, 'fictioneer_template_story_id', true );
$story_id = fictioneer_validate_id( $story_id, 'fcn_story' );
$render_story_header = get_post_meta( $post_id, 'fictioneer_template_show_story_header', true );

if ( ! $story_id ) {
  $render_story_header = false;
}

// Header
get_header();

?>

<main id="main" class="main singular">

  <?php do_action( 'fictioneer_main', 'singular-story' ); ?>

  <div class="main__wrapper <?php echo $render_story_header ? '_no-padding-top' : ''; ?>">

    <?php do_action( 'fictioneer_main_wrapper' ); ?>

    <?php while ( have_posts() ) : the_post(); ?>

      <?php
        // Setup
        $title = fictioneer_get_safe_title( $post_id, 'singular-story' );
        $this_breadcrumb = [ $title, get_the_permalink() ];
      ?>

      <article id="singular-<?php echo $post_id; ?>" class="singular__article padding-left padding-right padding-bottom <?php echo $render_story_header ? '' : 'padding-top'; ?>">

        <?php
          // Render story header
          if ( $render_story_header ) {
            get_template_part(
              'partials/_story-header',
              null,
              array(
                'story_data' => fictioneer_get_story_data( $story_id ),
                'story_id' => $story_id,
                'context' => 'shortcode'
              )
            );
          }
        ?>

        <section class="singular__content content-section"><?php the_content(); ?></section>

        <footer class="singular__footer"><?php do_action( 'fictioneer_singular_footer' ); ?></footer>

      </article>

      <?php do_action( 'fictioneer_before_comments' ); ?>

      <?php if ( comments_open() && ! post_password_required() ) : ?>
        <section class="singular__comments comment-section padding-left padding-right padding-bottom">
          <?php comments_template(); ?>
        </section>
      <?php endif; ?>

    <?php endwhile; ?>
  </div>

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
