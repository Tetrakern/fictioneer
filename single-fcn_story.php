<?php
/**
 * Custom Post Type: Story
 *
 * Shows the details a single story.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 1.0
 * @see partials/_story-header.php
 * @see partials/_story-footer.php
 */
?>

<?php get_header( null, array( 'type' => 'fcn_story' ) ); ?>

<main id="main" class="main story">
  <div class="main-observer"></div>
  <?php do_action( 'fictioneer_main' ); ?>
  <div class="main__background polygon polygon--main background-texture"></div>
  <div class="main__wrapper _no-padding">
    <?php do_action( 'fictioneer_main_wrapper' ); ?>

    <?php while ( have_posts() ) : the_post(); ?>

      <?php
        // Setup
        $story_id = $post->ID;
        $story = fictioneer_get_story_data( $post->ID );
        $epub_name = sanitize_file_name( strtolower( get_the_title() ) );
        $this_breadcrumb = [$story['title'], get_the_permalink()];

        // Flags
        $can_checkmarks = get_option( 'fictioneer_enable_checkmarks' );

        // Arguments for hooks and templates/etc.
        $hook_args = array(
          'story_data' => $story,
          'story_id' => $story_id
        );
      ?>

      <article id="<?php the_ID(); ?>" class="story__article <?php if ( ! $can_checkmarks ) echo '_no-checkmarks'; ?>">

        <?php
          // Render article header
          get_template_part( 'partials/_story-header', null, $hook_args );

          // Hook after header
          do_action( 'fictioneer_story_after_header', $hook_args );
        ?>

        <section class="story__summary padding-left padding-right"><?php the_content(); ?></section>

        <?php
          // Renders copyright notice, tags, actions, and chapters
          do_action( 'fictioneer_story_after_content', $hook_args );

          // Render footer partial
          get_template_part( 'partials/_story-footer', null, $hook_args );
        ?>

      </article>

      <?php do_action( 'fictioneer_story_after_article', $hook_args ); ?>

    <?php endwhile; ?>
  </div>
</main>

<?php
  // Footer arguments
  $footer_args = array(
    'post_type' => 'fcn_story',
    'post_id' => $story_id,
    'breadcrumbs' => array(
      [fcntr( 'frontpage' ), get_home_url()]
    )
  );

  // Add stories list breadcrumb (if set)
  $stories_page = intval( get_option( 'fictioneer_stories_page', -1 ) );

  if ( $stories_page > 0 ) {
    $stories_page_title = trim( get_the_title( $stories_page ) );
    $stories_page_title = empty( $stories_page_title ) ? __( 'Stories', 'fictioneer' ) : $stories_page_title;

    $footer_args['breadcrumbs'][] = array(
      $stories_page_title,
      get_permalink( $stories_page )
    );
  }

  // Add current breadcrumb
  $footer_args['breadcrumbs'][] = $this_breadcrumb;

  // Get footer with breadcrumbs
  get_footer( null, $footer_args );
?>
