<?php
/**
 * Tag archives
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 3.0
 * @see wp_tag_cloud()
 * @see partials/_archive-loop.php
 */


// Header
get_header();

?>

<main id="main" class="main archive tag-archive">

  <div class="observer main-observer"></div>

  <?php do_action( 'fictioneer_main' ); ?>

  <div class="main__wrapper">

    <?php do_action( 'fictioneer_main_wrapper' ); ?>

    <article class="archive__article padding-left padding-right padding-top padding-bottom">

      <header class="archive__header">
        <div class="tax-cloud">
          <?php
            $current_id = get_queried_object()->term_id;
            $current_count = get_queried_object()->count;
            $current_description = get_queried_object()->description;
          ?>

          <div class="tax-cloud__header">
            <h1 class="tax-cloud__current">
              <?php
                printf(
                  _n(
                    '<span class="tax-cloud__number">%1$s</span> Result with the <em>"%2$s"</em> tag',
                    '<span class="tax-cloud__number">%1$s</span> Results with the <em>"%2$s"</em> tag',
                    $current_count,
                    'fictioneer'
                  ),
                  $current_count,
                  single_tag_title( '', false )
                )
              ?>
            </h1>
            <?php if ( ! empty( $current_description ) ) : ?>
              <p class="tax-cloud__tax-description">
                <?php echo
                  sprintf(
                    __( '<strong>Definition:</strong> %s', 'fictioneer' ),
                    $current_description
                  )
                ?>
              </p>
            <?php endif; ?>
          </div>

          <?php
            wp_tag_cloud(
                array(
                'smallest' => .625,
                'largest' => 1.25,
                'unit' => 'rem',
                'number' => 0,
                'taxonomy' => ['post_tag'],
                'exclude' => $current_id,
                'show_count' => true,
                'pad_counts' => true
              )
            );
          ?>
        </div>
      </header>

      <?php get_template_part( 'partials/_archive-loop' ); ?>

    </article>

  </div>

</main>

<?php
  // Footer arguments
  $footer_args = array(
    'post_type' => null,
    'post_id' => null,
    'template' => 'tag.php',
    'breadcrumbs' => array(
      [fcntr( 'frontpage' ), get_home_url()],
      [sprintf( __( 'Results for Tag "%s"', 'fictioneer' ), single_tag_title( '', false ) ), null]
    )
  );

  // Get footer with breadcrumbs
  get_footer( null, $footer_args );
?>
