<?php
/**
 * Template Name: Author Index
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.23.1
 */


// Setup
$post_id = get_the_ID();
$sorted_authors = [];
$cached = fictioneer_caching_active( 'author_index' );

// Header
get_header();

// Transient cache?
$transient = $cached ? 0 : get_transient( 'fictioneer_author_index_' . $post_id );

if ( $transient ) {
  $sorted_authors = $transient;
}

// ... if not cached
if ( empty( $sorted_authors ) ) {
  // Query authors with published posts
  $authors = fictioneer_get_publishing_authors( array( 'fields' => array( 'ID', 'display_name', 'user_nicename' ) ) );

  // Sort authors
  if ( ! empty( $authors ) ) {
    // Loop through authors...
    foreach ( $authors as $author ) {
      // Relevant data
      $first_char = mb_strtolower( mb_substr( $author->display_name, 0, 1, 'UTF-8' ), 'UTF-8' );

      // Normalize for numbers and other non-alphabetical characters
      if ( ! preg_match( '/\p{L}/u', $first_char ) ) {
        $first_char = '#'; // Group under '#'
      }

      // Add index if necessary
      if ( ! isset( $sorted_authors[ $first_char ] ) ) {
        $sorted_authors[ $first_char ] = [];
      }

      $sorted_authors[ $first_char ][] = array(
        'id' => $author->ID,
        'name' => $author->display_name,
        'link' => get_author_posts_url( $author->ID, $author->user_nicename )
      );
    }

    // Sort by index
    ksort( $sorted_authors );

    // Cache as Transient
    if ( ! $cached ) {
      set_transient( 'fictioneer_author_index_' . $post_id, $sorted_authors, 12 * HOUR_IN_SECONDS );
    }
  }
}

// Last key
end( $sorted_authors );
$last_key = key( $sorted_authors );
reset( $sorted_authors );

?>

<main id="main" class="main singular index">

  <?php do_action( 'fictioneer_main', 'singular-index' ); ?>

  <div class="main__wrapper">

    <?php do_action( 'fictioneer_main_wrapper' ); ?>

    <?php while ( have_posts() ) : the_post(); ?>

      <?php
        // Setup
        $title = fictioneer_get_safe_title( $post_id, 'singular' );
        $this_breadcrumb = [ $title, get_the_permalink() ];
      ?>

      <article id="singular-<?php echo $post_id; ?>" class="singular__article">

        <header class="singular__header hidden">
          <h1 class="singular__title"><?php echo $title; ?></h1>
        </header>

        <section class="singular__content content-section"><?php the_content(); ?></section>

        <section class="index-letters">
          <?php foreach ( $sorted_authors as $index => $authors ) : ?>
            <a href="<?php echo esc_attr( "#letter-{$index}" ); ?>" class="index-letters__letter"><?php echo mb_strtoupper( $index ); ?></a>
            <?php if ( $last_key !== $index ) : ?>
              <span class="index-letters__separator">&bull;</span>
            <?php endif; ?>
          <?php endforeach; ?>
        </section>

        <?php foreach ( $sorted_authors as $index => $authors ) : ?>
          <section class="glossary">

            <h2 id="<?php echo esc_attr( "letter-{$index}" ); ?>"><?php echo mb_strtoupper( $index ); ?></h2>

            <div class="glossary__columns">

              <?php foreach ( $authors as $author ) : ?>
                <div class="glossary__entry">
                  <div class="glossary__entry-head">
                    <a href="<?php echo $author['link']; ?>" class="glossary__entry-name _full"><?php
                      echo $author['name'];
                    ?></a>
                  </div>
                </div>
              <?php endforeach; ?>

            </div>

          </section>
        <?php endforeach; ?>

        <footer class="singular__footer"><?php do_action( 'fictioneer_singular_footer' ); ?></footer>

      </article>

    <?php endwhile; ?>

  </div>

</main>

<?php
  // Footer arguments
  $footer_args = array(
    'post_type' => 'page',
    'post_id' => $post_id,
    'breadcrumbs' => array(
      [fcntr( 'frontpage' ), get_home_url()]
    )
  );

  // Add current breadcrumb
  $footer_args['breadcrumbs'][] = $this_breadcrumb;

  // Get footer with breadcrumbs
  get_footer( null, $footer_args );
?>
