<?php
/**
 * Custom Post Type: Collection
 *
 * Shows the content of a collection with a statistics block and several optional
 * partials for customization. It's paginated using the "Blog pages show at most"
 * (posts_per_page) option under Settings > Reading. Collections can hold stories,
 * chapters, recommendations, posts, and even other collections.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0
 * @see partials/_collection-header.php
 * @see partials/_collection-statistics.php
 */
?>

<?php get_header(); ?>

<main id="main" class="main collection">
  <div class="observer main-observer"></div>
  <?php do_action( 'fictioneer_main' ); ?>
  <div class="main__background polygon polygon--main background-texture"></div>
  <div class="main__wrapper">
    <?php do_action( 'fictioneer_main_wrapper' ); ?>

    <?php while ( have_posts() ) : the_post(); ?>

      <?php
        // Setup
        $featured_list = fictioneer_get_field( 'fictioneer_collection_items' );
        $featured_list = empty( $featured_list ) ? [] : $featured_list;
        $title = fictioneer_get_safe_title( $post->ID );
        $this_breadcrumb = [$title, get_the_permalink()];
        $current_page = max( 1, get_query_var( 'pg', 1 ) ?: 1 ); // Paged not available

        // Clean featured list of unwanted entries
        foreach ( $featured_list as $key => $post_id ) {
          if ( get_post_status( $post_id ) != 'publish' ) unset( $featured_list[$key] );
          if ( fictioneer_get_field( 'fictioneer_chapter_hidden', $post_id ) ) unset( $featured_list[$key] );
        }

        // Prepare query
        $query_args = array (
          'post_type' => 'any',
          'post_status' => 'publish',
          'post__in' => $featured_list,
          'ignore_sticky_posts' => 1,
          'orderby' => 'modified',
          'order' => 'DESC',
          'paged' => $current_page,
          'posts_per_page' => get_option( 'posts_per_page', 8 ),
          'meta_query' => array(
            'relation' => 'OR',
            array(
              'key' => 'fictioneer_story_hidden',
              'value' => '0'
            ),
            array(
              'key' => 'fictioneer_story_hidden',
              'compare' => 'NOT EXISTS'
            ),
          )
        );

        // Query featured posts
        $featured_query = new WP_Query( $query_args );

        // Arguments for hooks and templates/etc.
        $hook_args = array(
          'collection' => $post,
          'collection_id' => $post->ID,
          'title' => $title,
          'current_page' => $current_page,
          'max_pages' => $featured_query->max_num_pages,
          'featured_list' => $featured_list,
          'featured_query' => $featured_query
        );
      ?>

      <article id="collection-<?php the_ID(); ?>" class="collection__article padding-left padding-right padding-top padding-bottom">

        <?php
          // Render article header
          get_template_part( 'partials/_collection-header', null, $hook_args );

          // Hook after header
          do_action( 'fictioneer_collection_after_header', $hook_args );
        ?>

        <?php if ( get_the_content() ) : ?>
          <section class="collection__content content-section">
            <?php the_content(); ?>
          </section>
        <?php endif; ?>

        <?php do_action( 'fictioneer_collection_after_content', $hook_args ); ?>

        <footer class="collection__footer"><?php do_action( 'fictioneer_collection_footer', $hook_args ); ?></footer>

      </article>

    <?php endwhile; ?>
  </div>
</main>

<?php
  // Footer arguments
  $footer_args = array(
    'post_type' => 'fcn_collection',
    'post_id' => get_the_ID(),
    'breadcrumbs' => array(
      [fcntr( 'frontpage' ), get_home_url()]
    )
  );

  // Add recommendation list breadcrumb (if set)
  $collections_page = intval( get_option( 'fictioneer_collections_page', -1 ) );

  if ( $collections_page > 0 ) {
    $collections_page_title = trim( get_the_title( $collections_page ) );
    $collections_page_title = empty( $collections_page_title ) ? __( 'Stories', 'fictioneer' ) : $collections_page_title;

    $footer_args['breadcrumbs'][] = array(
      $collections_page_title,
      get_permalink( $collections_page )
    );
  }

  // Add current breadcrumb
  $footer_args['breadcrumbs'][] = $this_breadcrumb;

  // Get footer with breadcrumbs
  get_footer( null, $footer_args );
?>
