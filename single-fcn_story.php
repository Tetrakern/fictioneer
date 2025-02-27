<?php
/**
 * Custom Post Type: Story
 *
 * Shows the details a single story.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 1.0
 * @see partials/_story-header.php
 * @see partials/_story-footer.php
 */


// Setup
$post_id = $args['post_id'] ?? get_the_ID();
$post = get_post( $post_id );
$password_required = post_password_required();
$cover_position = get_theme_mod( 'story_cover_position', 'top-left-overflow' );
$show_thumbnail = has_post_thumbnail( $post_id ) && ! get_post_meta( $post_id, 'fictioneer_story_no_thumbnail', true );

// Wrapper classes
$wrapper_classes = [];

if ( $cover_position === 'top-left-overflow' && $show_thumbnail ) {
  $wrapper_classes[] = '_no-padding-top';
}

// Header
get_header(
  null,
  array(
    'type' => 'fcn_story',
    'no_index' => get_post_meta( $post_id, 'fictioneer_story_hidden', true ) ? 1 : 0
  )
);

?>

<main id="main" class="main story <?php echo get_option( 'fictioneer_enable_checkmarks' ) ? '' : '_no-checkmarks'; ?>" data-controller="fictioneer-story" data-fictioneer-story-id-value="<?php echo $post_id; ?>">

  <?php do_action( 'fictioneer_main', 'story' ); ?>

  <div class="main__wrapper <?php echo implode( ' ', $wrapper_classes ); ?>">

    <?php
      do_action( 'fictioneer_main_wrapper' );

      // Setup
      $story = fictioneer_get_story_data( $post_id );
      $epub_name = sanitize_file_name( strtolower( get_the_title() ) );
      $this_breadcrumb = [ $story['title'], get_the_permalink() ];
      $password_note = fictioneer_get_content_field( 'fictioneer_story_password_note', $post_id );
      $password_note = $password_note ? str_replace( '[!global]', '', $password_note ) : '';
      $cover_position = get_theme_mod( 'story_cover_position', 'top-left-overflow' );

      // Arguments for hooks and templates/etc.
      $hook_args = array(
        'story_data' => $story,
        'story_id' => $post_id,
        'password_required' => $password_required
      );
    ?>

    <article id="post-<?php echo $post_id; ?>" class="story__article" data-id="<?php echo $post_id; ?>" data-age-rating="<?php echo strtolower( $story['rating'] ); ?>">

      <?php
        // Render article header
        get_template_part( 'partials/_story-header', null, $hook_args );

        // Hook after header
        do_action( 'fictioneer_story_after_header', $hook_args );
      ?>

      <section class="story__summary content-section"><?php
        if ( $password_required ) {
          if ( $password_note ) {
            echo '<div class="story__password-note infobox">' . $password_note . '</div>';
          }

          if ( get_option( 'fictioneer_show_protected_excerpt' ) ) {
            echo '<p class="story__forced-excerpt">' . fictioneer_get_forced_excerpt( $post_id, 512 ) . '</p>';
          }
        }

        if ( in_array( $cover_position, ['float-left', 'float-right'] ) && $show_thumbnail ) {
          echo fictioneer_get_story_page_cover(
            $hook_args['story_data'],
            array( 'classes' => '_in-content _' . $cover_position )
          );
        }

        the_content();
      ?></section>

      <?php
        // Renders copyright notice, tags, actions, and chapters
        do_action( 'fictioneer_story_after_content', $hook_args );

        // Render footer partial
        get_template_part( 'partials/_story-footer', null, $hook_args );
      ?>

    </article>

    <?php do_action( 'fictioneer_story_after_article', $hook_args ); ?>

  </div>

  <?php do_action( 'fictioneer_main_end', 'story' ); ?>

</main>

<?php
  // Footer arguments
  $footer_args = array(
    'post_type' => 'fcn_story',
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

  // Add current breadcrumb
  $footer_args['breadcrumbs'][] = $this_breadcrumb;

  // Get footer with breadcrumbs
  get_footer( null, $footer_args );
?>
