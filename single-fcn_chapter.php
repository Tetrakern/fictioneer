<?php
/**
 * Custom Post Type: Chapter
 *
 * Shows a single chapter.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 1.0
 * @see comments.php
 */


// Setup
$password_required = post_password_required();
$post_id = get_the_ID();
$story_id = fictioneer_get_chapter_story_id( $post_id );

// Header
get_header(
  null,
  array(
    'type' => 'fcn_chapter',
    'no_index' => get_post_meta( $post_id, 'fictioneer_chapter_hidden', true ) ? 1 : 0
  )
);

?>

<div class="progress">
  <div class="progress__bar"></div>
</div>

<main id="main" class="main chapter" data-controller="fictioneer-chapter fictioneer-suggestion" data-fictioneer-chapter-chapter-id-value="<?php echo $post_id; ?>" data-fictioneer-chapter-story-id-value="<?php echo $story_id; ?>" data-action="fictioneer:bodyClick@window->fictioneer-chapter#clickOutside fictioneer:bodyClick@window->fictioneer-suggestion#clickOutside">

  <?php do_action( 'fictioneer_main', 'chapter' ); ?>

  <div class="main__wrapper">

    <?php do_action( 'fictioneer_main_wrapper' ); ?>

    <?php while ( have_posts() ) : the_post(); ?>

      <?php
        // Setup
        $chapter_ids = [];
        $indexed_chapters = [];
        $password_class = ! empty( $post->post_password ) ? 'password' : '';
        $title = fictioneer_get_safe_title( $post_id, 'single-chapter' );
        $age_rating = get_post_meta( $post_id, 'fictioneer_chapter_rating', true );
        $this_breadcrumb = [ $title, get_the_permalink() ];

        $story_data = null;
        $story_post = null;

        if ( $story_id && in_array( get_post_status( $story_id ), ['publish', 'private'] ) ) {
          $story_post = empty( $story_id ) ? null : get_post( $story_id );
        }

        // Story data
        if ( $story_post ) {
          $story_data = fictioneer_get_story_data( $story_id, false ); // Does not refresh comment count!
          $chapter_ids = $story_data['chapter_ids'];
          $indexed_chapters = $story_data['indexed_chapter_ids'] ?? $chapter_ids;

          if ( empty( $age_rating ) ) {
            $age_rating = $story_data['rating'];
          }
        }

        // Chapter navigation
        $current_index = array_search( $post_id, $indexed_chapters );
        $prev_index = $current_index - 1;
        $next_index = $current_index + 1;

        // Arguments for hooks and templates/etc. and includes
        $hook_args = array(
          'author' => get_userdata( $post->post_author ),
          'story_post' => $story_post,
          'story_data' => $story_data,
          'chapter_id' => $post_id,
          'chapter_title' => $title,
          'chapter_password' => $post->post_password,
          'password_required' => $password_required,
          'chapter_ids' => $chapter_ids,
          'indexed_chapter_ids' => $indexed_chapters,
          'current_index' => $current_index,
          'prev_index' => $prev_index >= 0 ? $prev_index : false,
          'next_index' => isset( $indexed_chapters[ $next_index ] ) ? $next_index : false
        );

        if ( $story_post && $indexed_chapters ) {
          echo fictioneer_render_chapter_index_modal_html( $story_id );
        }
      ?>

      <article id="ch-<?php echo $post_id; ?>" data-author-id="<?php echo get_the_author_meta( 'ID' ); ?>" class="chapter__article <?php echo $password_required ? '_password' : ''; ?>" data-age-rating="<?php echo strtolower( $age_rating ); ?>">

        <?php
          // Before chapter article header; includes the actions row, foreword, and warnings
          do_action( 'fictioneer_chapter_before_header', $hook_args );

          // Render article header
          get_template_part( 'partials/_chapter-header', null, $hook_args );

          // After chapter article header
          do_action( 'fictioneer_chapter_after_header', $hook_args );

          // Password note
          $password_note = fictioneer_get_content_field( 'fictioneer_chapter_password_note', $post_id );

          if ( $story_post && $password_required && empty( $password_note ) ) {
            $password_note = fictioneer_get_content_field( 'fictioneer_story_password_note', $story_id );

            if ( ! empty( $password_note ) && strpos( $password_note, '[!global]' ) !== false ) {
              $password_note = str_replace( '[!global]', '', $password_note );
            } else {
              $password_note = '';
            }
          }
        ?>

        <section id="chapter-content" class="chapter__content content-section" data-fictioneer-chapter-target="content" data-action="mouseup->fictioneer-suggestion#toggleFloatingButton"><?php
          if ( $password_required && $password_note ) {
            echo '<div class="chapter__password-note infobox">' . $password_note . '</div>';
          }

          echo '<div class="resize-font chapter-formatting chapter-font-color chapter-font-family" data-fictioneer-chapter-target="contentWrapper" data-action="mousedown->fictioneer-chapter#fastClick">';

          if ( $password_required && get_option( 'fictioneer_show_protected_excerpt' ) ) {
            echo '<p class="chapter__forced-excerpt">' . fictioneer_get_forced_excerpt( $post_id, 512 ) . '</p>';
          }

          fictioneer_the_static_content();

          echo '</div>';
        ?></section>

        <?php
          // After chapter content; includes the footer, afterword, and support box
          do_action( 'fictioneer_chapter_after_content', $hook_args );
        ?>

      </article>

      <?php
        do_action( 'fictioneer_chapter_before_comments', $hook_args );
        do_action( 'fictioneer_before_comments' );
      ?>

      <?php if ( comments_open() && ! $password_required ) : ?>
        <section class="chapter__comments comment-section chapter-comments-hideable"><?php
          // Allows :empty CSS selector
          comments_template();
        ?></section>
      <?php endif; ?>

    <?php endwhile; ?>

  </div>

  <?php do_action( 'fictioneer_main_end', 'chapter' ); ?>

</main>

<?php
  // After chapter main; includes the micro menu, paragraph tools, and suggestion tools
  if ( ! $password_required ) {
    do_action( 'fictioneer_chapter_after_main', $hook_args );
  }

  // Footer arguments
  $footer_args = array(
    'post_type' => 'fcn_chapter',
    'post_id' => $post_id,
    'breadcrumbs' => array(
      [fcntr( 'frontpage' ), get_home_url()]
    )
  );

  // Add stories list breadcrumb (if set)
  $stories_page_id = intval( get_option( 'fictioneer_stories_page', -1 ) ?: -1 );

  if ( $stories_page_id > 0 ) {
    $stories_page_title = trim( get_the_title( $stories_page_id ) );
    $stories_page_title = empty( $stories_page_title ) ? __( 'Stories', 'fictioneer' ) : $stories_page_title;

    $footer_args['breadcrumbs'][] = array(
      $stories_page_title,
      fictioneer_get_assigned_page_link( 'fictioneer_stories_page' )
    );
  }

  // Add story (if set)
  if ( $story_post ) {
    $footer_args['breadcrumbs'][] = array(
      $story_data['title'],
      get_the_permalink( $story_id )
    );
  }

  // Add current breadcrumb
  $footer_args['breadcrumbs'][] = $this_breadcrumb;

  // Get footer with breadcrumbs
  get_footer( null, $footer_args );
?>
