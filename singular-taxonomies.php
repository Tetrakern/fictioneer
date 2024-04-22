<?php
/**
 * Template Name: Taxonomies
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.0.0
 */
?>

<?php

// Queries
$tags = get_terms(
  array(
    'taxonomy' => 'post_tag',
    'order' => 'asc',
    'pad_counts' => true,
    'hide_empty' => true
  )
);

$genres = get_terms(
  array(
    'taxonomy' => 'fcn_genre',
    'order' => 'asc',
    'pad_counts' => true,
    'hide_empty' => true
  )
);

$fandoms = get_terms(
  array(
    'taxonomy' => 'fcn_fandom',
    'order' => 'asc',
    'pad_counts' => true,
    'hide_empty' => true
  )
);

$characters = get_terms(
  array(
    'taxonomy' => 'fcn_character',
    'order' => 'asc',
    'pad_counts' => true,
    'hide_empty' => true
  )
);

$terms = array(
  'tags' => [__( 'Tags', 'fictioneer' ), $tags],
  'genres' => [__( 'Genres', 'fictioneer' ), $genres],
  'fandoms' => [__( 'Fandoms', 'fictioneer' ), $fandoms],
  'characters' => [__( 'Characters', 'fictioneer' ), $characters]
);

?>

<?php get_header(); ?>

<main id="main" class="main singular">

  <div class="observer main-observer"></div>

  <?php do_action( 'fictioneer_main' ); ?>

  <div class="main__background polygon polygon--main background-texture"></div>

  <div class="main__wrapper">

    <?php do_action( 'fictioneer_main_wrapper' ); ?>

    <?php while ( have_posts() ) : the_post(); ?>
      <?php
        // Inner setup
        $title = trim( get_the_title() );
        $title = empty( $title ) ? __( 'Taxonomies', 'fictioneer' ) : $title;
        $this_breadcrumb = [$title, get_the_permalink()];
      ?>

      <article id="singular-<?php the_ID(); ?>" class="singular__article padding-left padding-right padding-top padding-bottom">

        <header class="singular__header">
          <h1 class="singular__title"><?php echo $title; ?></h1>
        </header>

        <?php if ( get_the_content() ) : ?>
          <section class="singular__content content-section">
            <?php the_content(); ?>
          </section>
        <?php endif; ?>

        <?php foreach ( $terms as $tuple ) : ?>
          <?php if ( ! is_wp_error( $tuple[1] ) && count( $tuple[1] ) > 0 ) : ?>
            <section class="glossary">
              <h2><?php echo $tuple[0]; ?></h2>
              <div class="glossary__columns">
                <?php foreach ( $tuple[1] as $term ) : ?>
                  <div class="glossary__entry">
                    <a class="glossary__entry-head" href="<?php echo get_term_link( $term ); ?>">
                      <div class="glossary__entry-name"><?php echo $term->name; ?></div>
                      <div class="glossary__entry-count"><?php
                        printf(
                          _nx(
                            '%s Entry',
                            '%s Entries',
                            $term->count,
                            'Taxonomy glossary entry count.',
                            'fictioneer'
                          ),
                          $term->count
                        );
                      ?></div>
                    </a>
                    <div class="glossary__entry-body">
                      <span class="glossary__entry-description"><?php
                        if ( empty( $term->description ) ) {
                          _e( 'No description provided yet.' );
                        } else {
                          echo $term->description;
                        }
                      ?></span>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </section>
          <?php endif; ?>
        <?php endforeach; ?>

      </article>

    <?php endwhile; ?>

  </div>

</main>

<?php
  // Footer arguments
  $footer_args = array(
    'post_type' => 'page',
    'post_id' => get_the_ID(),
    'breadcrumbs' => array(
      [fcntr( 'frontpage' ), get_home_url()]
    )
  );

  // Add current breadcrumb
  $footer_args['breadcrumbs'][] = $this_breadcrumb;

  // Get footer with breadcrumbs
  get_footer( null, $footer_args );
?>
