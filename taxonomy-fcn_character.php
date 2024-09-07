<?php
/**
 * Character custom taxonomy archives
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0.0
 * @see partials/_archive-loop.php
 */


// Header
get_header();

?>

<main id="main" class="main archive character-archive">

  <?php do_action( 'fictioneer_main', 'character-archive' ); ?>

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
            $parent_snippet = ( $parent ) ? " ($parent->name)" : '';
          ?>

          <div class="tax-cloud__header">
            <h1 class="tax-cloud__current">
              <?php
                printf(
                  _n(
                    '<span class="tax-cloud__number">%1$s</span> Result with character <em>"%2$s"</em>' . $parent_snippet,
                    '<span class="tax-cloud__number">%1$s</span> Results with character <em>"%2$s"</em>' . $parent_snippet,
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
                'taxonomy' => ['fcn_character'],
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

  <?php do_action( 'fictioneer_main_end', 'character-archive' ); ?>

</main>

<?php
  // Footer arguments
  $footer_args = array(
    'post_type' => null,
    'post_id' => null,
    'template' => 'taxonomy-fcn_character.php',
    'breadcrumbs' => array(
      [fcntr( 'frontpage' ), get_home_url()],
      [sprintf( __( 'Results for Character "%s"', 'fictioneer' ), single_tag_title( '', false ) ), null]
    )
  );

  // Get footer with breadcrumbs
  get_footer( null, $footer_args );
?>
