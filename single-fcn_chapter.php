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
?>

<?php

// Header
$header_args = array(
  'type' => 'fcn_chapter'
);

if ( get_post_meta( get_the_ID(), 'fictioneer_chapter_hidden', true ) ) {
  $header_args['no_index'] = true;
}

get_header( null, $header_args );

?>

<div class="progress">
  <div class="progress__bar"></div>
</div>

<main id="main" class="main chapter">
  <div class="observer main-observer"></div>
  <?php do_action( 'fictioneer_main' ); ?>
  <div class="main__background chapter__background polygon polygon--main background-texture"></div>
  <div class="main__wrapper">
    <?php do_action( 'fictioneer_main_wrapper' ); ?>

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

      <?php
        // Setup
        $chapter_ids = [];
        $password_class = ! empty( $post->post_password ) ? 'password' : '';
        $title = fictioneer_get_safe_title( $post->ID, 'single-chapter' );
        $age_rating = get_post_meta( $post->ID, 'fictioneer_chapter_rating', true );
        $this_breadcrumb = [ $title, get_the_permalink() ];

        $story_id = get_post_meta( $post->ID, 'fictioneer_chapter_story', true );
        $story_data = null;
        $story_post = null;

        if ( $story_id && in_array( get_post_status( $story_id ), ['publish', 'private'] ) ) {
          $story_post = empty( $story_id ) ? null : get_post( $story_id );
        }

        // Story data
        if ( $story_post ) {
          $story_data = fictioneer_get_story_data( $story_id, false ); // Does not refresh comment count!
          $chapter_ids = $story_data['chapter_ids'];

          if ( empty( $age_rating ) ) {
            $age_rating = $story_data['rating'];
          }
        }

        // Chapter navigation
        $current_index = array_search( $post->ID, $chapter_ids );
        $prev_index = $current_index - 1;
        $next_index = $current_index + 1;

        // Arguments for hooks and templates/etc. and includes
        $hook_args = array(
          'author' => get_userdata( $post->post_author ),
          'story_post' => $story_post,
          'story_data' => $story_data,
          'chapter_id' => $post->ID,
          'chapter_title' => $title,
          'chapter_password' => $post->post_password,
          'chapter_ids' => $chapter_ids,
          'current_index' => $current_index,
          'prev_index' => $prev_index >= 0 ? $prev_index : false,
          'next_index' => isset( $chapter_ids[ $next_index ] ) ? $next_index : false
        );
      ?>

      <?php if ( $story_post && $chapter_ids ): ?>
        <div id="story-chapter-list" class="hidden" data-story-id="<?php echo $story_id; ?>">
          <ul data-current-id="<?php echo $post->ID; ?>"><?php
            echo fictioneer_get_chapter_list_items( $story_id, $story_data, $current_index );
          ?></ul>
        </div>
      <?php endif; ?>

      <?php
        if ( get_option( 'fictioneer_enable_bookmarks' ) ) {
          // Bookmark data
          $bookmark_story_title = '';
          $bookmark_title = get_post_meta( $post->ID, 'fictioneer_chapter_list_title', true );
          $bookmark_title = trim( wp_strip_all_tags( $bookmark_title ) );
          $bookmark_title = $bookmark_title ?: $title;
          $bookmark_thumbnail = get_the_post_thumbnail_url( null, 'snippet' );
          $bookmark_image = get_the_post_thumbnail_url( null, 'full' );

          // If story is set...
          if ( $story_post ) {
            $bookmark_story_title = $story_data['title'];

            // If chapter has no featured image, look in story...
            if ( ! $bookmark_thumbnail ) {
              $bookmark_thumbnail = get_the_post_thumbnail_url( $story_id, 'snippet' );
              $bookmark_image = get_the_post_thumbnail_url( $story_id, 'full' );
            }
          }
        }
      ?>

      <?php if ( get_option( 'fictioneer_enable_bookmarks' ) ) : ?>
        <div id="chapter-bookmark-data"
          data-thumb="<?php echo esc_url( $bookmark_thumbnail ); ?>"
          data-image="<?php echo esc_url( $bookmark_image ); ?>"
          data-link="<?php the_permalink(); ?>"
          data-title="<?php echo esc_attr( $bookmark_title ); ?>"
          data-story-title="<?php echo esc_attr( $bookmark_story_title ); ?>"
          hidden data-nosnippet>
        </div>
      <?php endif; ?>

      <article id="ch-<?php the_ID(); ?>" data-author-id="<?php echo get_the_author_meta( 'ID' ); ?>" class="chapter__article padding-left padding-right <?php echo post_password_required() ? '_password' : ''; ?>" data-age-rating="<?php echo strtolower( $age_rating ); ?>">

        <div class="chapter__actions chapter__actions--top">
          <div class="chapter__actions-container chapter__actions-left"><?php
            do_action( 'fictioneer_chapter_actions_top_left', $hook_args, 'top' );
          ?></div>
          <div class="chapter__actions-container chapter__actions-center"><?php
            do_action( 'fictioneer_chapter_actions_top_center', $hook_args, 'top' );
          ?></div>
          <div class="chapter__actions-container chapter__actions-right"><?php
            do_action( 'fictioneer_chapter_actions_top_right', $hook_args, 'top' );
          ?></div>
        </div>

        <?php
          // Before chapter article header (includes foreword and warnings)
          do_action( 'fictioneer_chapter_before_header', $hook_args );

          // Render article header
          get_template_part( 'partials/_chapter-header', null, $hook_args );

          // After chapter article header
          do_action( 'fictioneer_chapter_after_header', $hook_args );

          // Password note
          $password_note = fictioneer_get_content_field( 'fictioneer_chapter_password_note', $post->ID );

          if ( $story_post && post_password_required() && empty( $password_note ) ) {
            $password_note = fictioneer_get_content_field( 'fictioneer_story_password_note', $story_id );

            if ( ! empty( $password_note ) && strpos( $password_note, '[!global]' ) !== false ) {
              $password_note = str_replace( '[!global]', '', $password_note );
            } else {
              $password_note = '';
            }
          }
        ?>

        <section id="chapter-content" class="chapter__content content-section">
          <?php if ( post_password_required() && $password_note ) : ?>
            <div class="chapter__password-note infobox"><?php echo $password_note; ?></div>
          <?php endif; ?>
          <div class="resize-font chapter-formatting chapter-font-color chapter-font-family">
            <?php the_content(); ?>
          </div>
        </section>

        <?php
          // After chapter content; includes the afterword and support box
          do_action( 'fictioneer_chapter_after_content', $hook_args );
        ?>

        <footer class="chapter__footer chapter__actions chapter__actions--bottom padding-bottom chapter-end">
          <div class="chapter__actions-container chapter__actions-left"><?php
            do_action( 'fictioneer_chapter_actions_bottom_left', $hook_args, 'bottom' );
          ?></div>
          <div class="chapter__actions-container chapter__actions-center"><?php
            do_action( 'fictioneer_chapter_actions_bottom_center', $hook_args, 'bottom' );
          ?></div>
          <div class="chapter__actions-container chapter__actions-right"><?php
            do_action( 'fictioneer_chapter_actions_bottom_right', $hook_args, 'bottom' );
          ?></div>
        </footer>

      </article>

      <?php do_action( 'fictioneer_chapter_before_comments', $hook_args ); ?>

      <?php do_action( 'fictioneer_before_comments' ); ?>

      <?php if ( comments_open() && ! post_password_required() ) : ?>
        <section class="chapter__comments comment-section padding-left padding-right padding-bottom chapter-comments-hideable">
          <?php comments_template(); ?>
        </section>
      <?php endif; ?>

    <?php endwhile; endif; ?>
  </div>
</main>

<?php
  // After chapter main; includes the micro menu, paragraph tools, and suggestion tools
  if ( ! post_password_required() ) {
    do_action( 'fictioneer_chapter_after_main', $hook_args );
  }
?>

<?php
  // Footer arguments
  $footer_args = array(
    'post_type' => 'fcn_chapter',
    'post_id' => $post->ID,
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
