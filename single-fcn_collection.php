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
 * @since 4.0.0
 * @see partials/_collection-header.php
 * @see partials/_collection-statistics.php
 */


// Header
get_header( null, array( 'type' => 'fcn_collection' ) );

?>

<main id="main" class="main collection">

  <?php do_action( 'fictioneer_main', 'collection' ); ?>

  <div class="main__wrapper">

    <?php do_action( 'fictioneer_main_wrapper' ); ?>

    <?php while ( have_posts() ) : the_post(); ?>

      <?php
        // Setup
        $title = fictioneer_get_safe_title( $post->ID, 'single-collection' );
        $this_breadcrumb = [$title, get_the_permalink()];
        $current_page = max( 1, get_query_var( 'pg', 1 ) ?: 1 ); // Paged not available
        $featured_list = get_post_meta( $post->ID, 'fictioneer_collection_items', true );
        $featured_list = is_array( $featured_list ) ? $featured_list : [];

        // Query posts (if any)
        if ( ! empty( $featured_list ) ) {
          global $wpdb;

          // Filter out hidden posts
          $placeholders = implode( ',', array_fill( 0, count( $featured_list ), '%d' ) );

          $sql =
            "SELECT p.ID
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} pm_story_hidden
              ON (p.ID = pm_story_hidden.post_id AND pm_story_hidden.meta_key = 'fictioneer_story_hidden')
            LEFT JOIN {$wpdb->postmeta} pm_chapter_hidden
              ON (p.ID = pm_chapter_hidden.post_id AND pm_chapter_hidden.meta_key = 'fictioneer_chapter_hidden')
            WHERE p.ID IN ($placeholders)
              AND p.post_status = 'publish'
              AND (pm_story_hidden.meta_value IS NULL OR pm_story_hidden.meta_value = '' OR pm_story_hidden.meta_value = '0')
              AND (pm_chapter_hidden.meta_value IS NULL OR pm_chapter_hidden.meta_value = '' OR pm_chapter_hidden.meta_value = '0')";

          $featured_list = $wpdb->get_col( $wpdb->prepare( $sql, ...$featured_list ) );
        }

        // Query posts
        $query_args = array (
          'fictioneer_query_name' => 'collection_featured',
          'post_type' => ['post', 'page', 'fcn_story', 'fcn_chapter', 'fcn_collection', 'fcn_recommendation'],
          'post_status' => 'publish',
          'post__in' => $featured_list ?: [0], // Must not be empty!
          'ignore_sticky_posts' => 1,
          'orderby' => 'modified',
          'order' => 'DESC',
          'paged' => $current_page,
          'posts_per_page' => get_option( 'posts_per_page', 8 ),
          'update_post_meta_cache' => true,
          'update_post_term_cache' => true
        );

        $featured_query = new WP_Query( $query_args );

        // Prime author cache
        if ( ! empty( $featured_query->posts ) && function_exists( 'update_post_author_caches' ) ) {
          update_post_author_caches( $featured_query->posts );
        }

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

      <article id="collection-<?php the_ID(); ?>" class="collection__article">

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

  <?php do_action( 'fictioneer_main_end', 'collection' ); ?>

</main>

<?php
  // Footer arguments
  $footer_args = array(
    'post_type' => 'fcn_collection',
    'post_id' => $post->ID,
    'breadcrumbs' => array(
      [fcntr( 'frontpage' ), get_home_url()]
    )
  );

  // Add recommendation list breadcrumb (if set)
  $collections_page_id = intval( get_option( 'fictioneer_collections_page', -1 ) ?: -1 );

  if ( $collections_page_id > 0 ) {
    $collections_page_title = trim( get_the_title( $collections_page_id ) );
    $collections_page_title = empty( $collections_page_title ) ? __( 'Stories', 'fictioneer' ) : $collections_page_title;

    $footer_args['breadcrumbs'][] = array(
      $collections_page_title,
      fictioneer_get_assigned_page_link( 'fictioneer_collections_page' )
    );
  }

  // Add current breadcrumb
  $footer_args['breadcrumbs'][] = $this_breadcrumb;

  // Get footer with breadcrumbs
  get_footer( null, $footer_args );
?>
