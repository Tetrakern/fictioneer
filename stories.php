<?php
/**
 * Template Name: Stories
 *
 * Shows a list of all published stories with a statistics block and hooks
 * for customization. It's paginated using the "Blog pages show at most"
 * (posts_per_page) option under Settings > Reading.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 3.0
 *
 * @internal ('fictioneer_filter_and_search_id') ACF field. You can use this ID for plugins.
 * @internal ('fictioneer_short_name')           ACF field. Meant for an abbreviated page name.
 */
?>

<?php
  // Get current page (of main query, but can be used since open-ended)
  $page = get_query_var( 'paged', 1 );

  // Prepare query
  $query_args = array (
    'post_type' => 'fcn_story',
    'post_status' => 'publish',
    'meta_key' => 'fictioneer_story_sticky',
    'orderby' => 'meta_value modified',
    'order' => 'DESC',
    'paged' => $page,
    'posts_per_page' => get_option( 'posts_per_page', 8 )
  );

  // Filter query arguments
  $query_args = apply_filters( 'fictioneer_filter_stories_query_args', $query_args, get_the_ID() );

  // Query stories
  $list_of_stories = new WP_Query( $query_args );
?>

<?php get_header(); ?>

<main id="main" class="main singular stories">
  <div class="main-observer"></div>
  <?php do_action( 'fictioneer_main' ); ?>
  <div class="main__background polygon polygon--main background-texture"></div>
  <div class="main__wrapper">
    <?php do_action( 'fictioneer_main_wrapper' ); ?>

    <?php while ( have_posts() ) : the_post(); ?>

      <?php
        // Setup
        $title = trim( get_the_title() );
        $breadcrumb_name = empty( $title ) ? __( 'Stories', 'fictioneer' ) : $title;
        $this_breadcrumb = [$breadcrumb_name, get_the_permalink()];

        // Arguments for hooks and templates/etc.
        $hook_args = array(
          'current_page' => $page,
          'post_id' => get_the_ID(),
          'stories' => $list_of_stories,
          'queried_type' => 'fcn_story'
        );
      ?>

      <article id="singular-<?php the_ID(); ?>" class="singular__article stories__article padding-top padding-left padding-right padding-bottom">

        <?php if ( ! empty( $title ) ) : ?>
          <header class="singular__header stories__header">
            <h1 class="singular__title stories__title"><?php echo $title; ?></h1>
          </header>
        <?php endif; ?>

        <?php if ( get_the_content() ) : ?>
          <section class="singular__content stories__content content-section">
            <?php the_content(); ?>
          </section>
        <?php endif; ?>

        <?php
          // Renders statistics and list of stories
          do_action( 'fictioneer_stories_after_content', $hook_args );
        ?>

      </article>

    <?php endwhile; ?>
  </div>
</main>

<?php
  // Footer arguments
  $footer_args = array(
    'post_type' => 'page',
    'post_id' => get_the_ID(),
    'current_page' => $page,
    'stories' => $list_of_stories,
    'breadcrumbs' => array(
      [fcntr( 'frontpage' ), get_home_url()]
    )
  );

  // Add current breadcrumb
  $footer_args['breadcrumbs'][] = $this_breadcrumb;

  // Get footer with breadcrumbs
  get_footer( null, $footer_args );
?>
