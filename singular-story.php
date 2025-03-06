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
$cover_position = get_theme_mod( 'story_cover_position', 'top-left-overflow' );
$render_story_header = get_post_meta( $post_id, 'fictioneer_template_show_story_header', true );
$show_thumbnail = has_post_thumbnail( $story_id ) && ! get_post_meta( $story_id, 'fictioneer_story_no_thumbnail', true );

if ( ! $story_id ) {
  $render_story_header = false;
}

// Wrapper classes
$wrapper_classes = [];

if ( $render_story_header && $cover_position === 'top-left-overflow' && $show_thumbnail ) {
  $wrapper_classes[] = '_no-padding-top';
}

// Header
get_header(
  null,
  array(
    'no_index' => get_post_meta( $story_id, 'fictioneer_story_hidden', true ) ? 1 : 0
  )
);

?>

<main id="main" class="main singular" data-controller="fictioneer-story" data-fictioneer-story-id-value="<?php echo $story_id; ?>">

  <?php do_action( 'fictioneer_main', 'singular-story' ); ?>

  <div class="main__wrapper <?php echo implode( ' ', $wrapper_classes ); ?>">

    <?php do_action( 'fictioneer_main_wrapper' ); ?>

    <?php while ( have_posts() ) : the_post(); ?>

      <?php
        // Setup
        $title = fictioneer_get_safe_title( $post_id, 'singular-story' );
        $this_breadcrumb = [ $title, get_the_permalink() ];
      ?>

      <article id="singular-<?php echo $post_id; ?>" class="singular__article">

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
        <section class="singular__comments comment-section"><?php
          // Allows :empty CSS selector
          comments_template();
        ?></section>
      <?php endif; ?>

    <?php endwhile; ?>
  </div>

  <?php do_action( 'fictioneer_main_end', 'singular-story' ); ?>

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
