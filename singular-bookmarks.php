<?php
/**
 * Template Name: Bookmarks
 *
 * Mostly empty page template that always renders the bookmarks partial
 * even without any shortcode. Meant to be used as overview page for all
 * the users bookmarks. You can still add regular content here but the
 * point of that is highly debatable.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.6.0
 */


// Return home if bookmarks are disabled
if ( ! get_option( 'fictioneer_enable_bookmarks' ) ) {
  wp_redirect( home_url() );
  exit();
}

// Header
get_header();

?>

<main id="main" class="main singular bookmarks-page">

  <div class="observer main-observer"></div>

  <?php do_action( 'fictioneer_main' ); ?>

  <div class="main__background polygon polygon--main background-texture"></div>

  <div class="main__wrapper">

    <?php do_action( 'fictioneer_main_wrapper' ); ?>

    <?php while ( have_posts() ) : the_post(); ?>

      <?php
        // Setup
        $title = trim( get_the_title() );
        $breadcrumb_name = empty( $title ) ? fcntr( 'bookmarks' ) : $title;
        $this_breadcrumb = [$breadcrumb_name, get_the_permalink()];
      ?>

      <article id="singular-<?php the_ID(); ?>" class="singular__article padding-left padding-right padding-top padding-bottom">

        <?php if ( ! empty( $title ) ) : ?>
          <header class="singular__header">
            <h1 class="singular__title"><?php echo $title; ?></h1>
          </header>
        <?php endif; ?>

        <?php if ( get_the_content() ) : ?>
          <section class="singular__content content-section">
            <?php the_content(); ?>
          </section>
        <?php endif; ?>

        <section>
          <?php
            get_template_part(
              'partials/_bookmarks',
              null,
              array(
                'show_empty' => true
              )
            );
          ?>
        </section>

        <footer class="singular__footer"><?php do_action( 'fictioneer_singular_footer' ); ?></footer>

      </article>

    <?php endwhile; ?>

  </div>

</main>

<?php
  // Get profile link
  $profile_link = fictioneer_get_assigned_page_link( 'fictioneer_user_profile_page' );
  $profile_link = $profile_link ? $profile_link : get_edit_profile_url();

  // Footer arguments
  $footer_args = array(
    'post_type' => 'page',
    'post_id' => get_the_ID(),
    'breadcrumbs' => array(
      [fcntr( 'frontpage' ), get_home_url()]
    )
  );

  if ( is_user_logged_in() ) {
    $footer_args['breadcrumbs'][] = [fcntr( 'account' ), $profile_link];
  }

  // Add current breadcrumb
  $footer_args['breadcrumbs'][] = $this_breadcrumb;

  // Get footer with breadcrumbs
  get_footer( null, $footer_args );
?>
