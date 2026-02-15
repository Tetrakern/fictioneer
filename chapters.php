<?php
/**
 * Template Name: Chapters
 *
 * Shows a list of all published chapters with hooks for customization.
 * It's paginated using the "Blog pages show at most" (posts_per_page)
 * option under Settings > Reading.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 3.0
 */


use Fictioneer\Sanitizer;
use Fictioneer\Utils;

// Setup
$post_id = get_the_ID();
$page = Utils::get_global_page(); // Main query
$order = Sanitizer::sanitize_query_var( $_GET['order'] ?? 0, ['desc', 'asc'], 'desc' );
$orderby = Sanitizer::sanitize_query_var( $_GET['orderby'] ?? 0, fictioneer_allowed_orderby(), 'modified' );
$ago = $_GET['ago'] ?? 0;
$ago = is_numeric( $ago ) ? absint( $ago ) : sanitize_text_field( $ago );

// Prepare query
$query_args = array(
  'fictioneer_query_name' => 'chapters_list',
  'post_type' => 'fcn_chapter',
  'post_status' => 'publish',
  'order' => $order,
  'orderby' => $orderby,
  'paged' => $page,
  'posts_per_page' => get_option( 'posts_per_page', 8 ),
  'update_post_term_cache' => ! get_option( 'fictioneer_hide_taxonomies_on_chapter_cards' )
);

// Order by words?
if ( $orderby === 'words' ) {
  $query_args['orderby'] = 'meta_value_num modified';
  $query_args['meta_key'] = '_word_count';
}

// Append date query (if any)
$query_args = fictioneer_append_date_query( $query_args, $ago, $orderby );

// Filter query arguments
$query_args = apply_filters( 'fictioneer_filter_chapters_query_args', $query_args, $post_id );

// Query chapters
$list_of_chapters = new WP_Query( $query_args );

// Prime thumbnail cache
if ( function_exists( 'update_post_thumbnail_cache' ) ) {
  update_post_thumbnail_cache( $list_of_chapters );
}

// Prime author cache
if ( function_exists( 'update_post_author_caches' ) ) {
  update_post_author_caches( $list_of_chapters->posts );
}

// Header
get_header();

?>

<main id="main" class="main singular chapters">

  <?php do_action( 'fictioneer_main', 'chapters' ); ?>

  <div class="main__wrapper">

    <?php do_action( 'fictioneer_main_wrapper' ); ?>

    <?php while ( have_posts() ) : the_post(); ?>

      <?php
        // Setup
        $title = trim( get_the_title() );
        $breadcrumb_name = empty( $title ) ? __( 'Chapters', 'fictioneer' ) : $title;
        $this_breadcrumb = [ $breadcrumb_name, get_the_permalink() ];

        // Arguments for hooks and templates/etc.
        $hook_args = array(
          'current_page' => $page,
          'post_id' => $post_id,
          'chapters' => $list_of_chapters,
          'queried_type' => 'fcn_chapter',
          'query_args' => $query_args,
          'order' => $order,
          'orderby' => $orderby,
          'ago' => $ago
        );
      ?>

      <article id="singular-<?php echo $post_id; ?>" class="singular__article chapters__article">

        <?php if ( ! empty( $title ) ) : ?>
          <header class="singular__header stories__header">
            <h1 class="singular__title stories__title"><?php echo $title; ?></h1>
          </header>
        <?php endif; ?>

        <?php if ( get_the_content() ) : ?>
          <section class="singular__content chapters__content content-section">
            <?php the_content(); ?>
          </section>
        <?php endif; ?>

        <?php do_action( 'fictioneer_chapters_after_content', $hook_args ); ?>

      </article>

    <?php endwhile; ?>

  </div>

  <?php do_action( 'fictioneer_main_end', 'chapters' ); ?>

</main>

<?php
  // Footer arguments
  $footer_args = array(
    'post_type' => 'page',
    'post_id' => $post_id,
    'current_page' => $page,
    'chapters' => $list_of_chapters,
    'breadcrumbs' => array(
      [fcntr( 'frontpage' ), get_home_url()]
    )
  );

  // Add current breadcrumb
  $footer_args['breadcrumbs'][] = $this_breadcrumb;

  // Get footer with breadcrumbs
  get_footer( null, $footer_args );
?>
