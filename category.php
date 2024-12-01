<?php
/**
 * Category archives
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 3.0
 * @see partials/_archive-loop.php
 */


// Header
get_header();

?>

<main id="main" class="main archive category-archive">

  <?php do_action( 'fictioneer_main', 'category-archive' ); ?>

  <div class="main__wrapper">

    <?php do_action( 'fictioneer_main_wrapper' ); ?>

    <article class="archive__article">
      <header class="archive__header">
        <div class="tax-cloud">
          <?php
            // Setup
            $current_id = get_queried_object()->term_id;
            $current_count = get_queried_object()->count;
            $current_description = get_queried_object()->description;
          ?>

          <div class="tax-cloud__header">
            <h1 class="tax-cloud__current">
              <?php
                printf(
                  _n(
                    '<span class="tax-cloud__number">%1$s</span> Result in the <em>"%2$s"</em> category',
                    '<span class="tax-cloud__number">%1$s</span> Results in the <em>"%2$s"</em> category',
                    $current_count,
                    'fictioneer'
                  ),
                  $current_count,
                  single_cat_title( '', false )
                )
              ?>
            </h1>
            <?php if ( ! empty( $current_description ) ) : ?>
              <p class="tax-cloud__tax-description"><strong><?php _e( 'Definition:', 'fictioneer' ); ?></strong> <?php echo $current_description; ?></p>
            <?php endif; ?>
          </div>

          <?php
            wp_tag_cloud(
              array(
                'fictioneer_query_name' => 'tag_cloud',
                'smallest' => 0.625,
                'largest' => 1.25,
                'unit' => 'rem',
                'number' => 0,
                'taxonomy' => ['category'],
                'exclude' => $current_id,
                'show_count' => true,
                'pad_counts' => true
              )
            );
          ?>
        </div>
      </header>

      <?php get_template_part( 'partials/_archive-loop', null, array( 'taxonomy' => 'category' ) ); ?>

    </article>

  </div>

  <?php do_action( 'fictioneer_main_end', 'category-archive' ); ?>

</main>

<?php
  // Footer arguments
  $footer_args = array(
    'post_type' => null,
    'post_id' => null,
    'template' => 'category.php',
    'breadcrumbs' => array(
      [fcntr( 'frontpage' ), get_home_url()],
      [sprintf( __( 'Results for Category "%s"', 'fictioneer' ), single_cat_title( '', false ) ), null]
    )
  );

  // Get footer with breadcrumbs
  get_footer( null, $footer_args );
?>
