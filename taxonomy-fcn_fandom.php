<?php
/**
 * Fandom custom taxonomy archives
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0.0
 * @see partials/_archive-loop.php
 */


// Header
get_header();

?>

<main id="main" class="main archive fandom-archive">

  <?php do_action( 'fictioneer_main', 'fandom-archive' ); ?>

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
            $parent = get_term_by( 'id', get_queried_object()->parent, get_query_var( 'taxonomy' ) );
          ?>

          <div class="tax-cloud__header">
            <h1 class="tax-cloud__current">
              <?php
                printf(
                  _n(
                    '<span class="tax-cloud__number">%1$s</span> Result in the <em>"%2$s"</em> fandom %3$s',
                    '<span class="tax-cloud__number">%1$s</span> Results in the <em>"%2$s"</em> fandom %3$s',
                    $current_count,
                    'fictioneer'
                  ),
                  $current_count,
                  single_tag_title( '', false ),
                  $parent ? sprintf( _x( '(%s)', 'Taxonomy page parent suffix.', 'fictioneer' ), $parent->name ) : ''
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
                'fictioneer_query_name' => 'tag_cloud',
                'smallest' => .625,
                'largest' => 1.25,
                'unit' => 'rem',
                'number' => 0,
                'taxonomy' => ['fcn_fandom'],
                'exclude' => $current_id,
                'show_count' => true,
                'pad_counts' => true
              )
            );
          ?>
        </div>
      </header>

      <?php get_template_part( 'partials/_archive-loop', null, array( 'taxonomy' => 'fcn_fandom' ) ); ?>

    </article>

  </div>

  <?php do_action( 'fictioneer_main_end', 'fandom-archive' ); ?>

</main>

<?php
  // Footer arguments
  $footer_args = array(
    'post_type' => null,
    'post_id' => null,
    'template' => 'taxonomy-fcn_fandom.php',
    'breadcrumbs' => array(
      [fcntr( 'frontpage' ), get_home_url()],
      [sprintf( __( 'Results for Fandom "%s"', 'fictioneer' ), single_tag_title( '', false ) ), null]
    )
  );

  // Get footer with breadcrumbs
  get_footer( null, $footer_args );
?>
