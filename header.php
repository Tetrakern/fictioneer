<?php
/**
 * Template Part: Header
 *
 * Starts the document. The HTML root <html> is used to store default settings and
 * configuration classes since it is the only node available at the beginning.
 * This is necessary for the light/dark switch and site settings to work without
 * causing color flickering or layout shifts.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 3.0
 * @see wp_head()
 * @see wp_body_open()
 * @see fictioneer_output_head_meta()
 * @see fictioneer_output_head_critical_scripts()
 *
 * @internal $args  Array of arguments passed to the template.
 */
?>

<?php

if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
  global $fictioneer_render_start_time;
  $fictioneer_render_start_time = microtime( true );
}

global $post;

// IDs
$page_id = get_queried_object_id();
$post_id = $post ? $post->ID : null;

if ( $page_id != $post_id ) {
  $post_id = $page_id;
}

if ( is_archive() || is_search() || is_404() || FICTIONEER_MU_REGISTRATION ) {
  $post_id = null;
}

// Prevent indexing if required
if ( ( $args['no_index'] ?? 0 ) || FICTIONEER_MU_REGISTRATION ) {
  add_filter( 'wp_robots', 'wp_robots_no_robots' );
}

?>

<!doctype html>

<html <?php language_attributes(); ?> <?php fictioneer_root_attributes(); ?>>

  <head>
    <?php
      // Includes charset, content type, viewport, etc...
      fictioneer_output_head_meta();


      // WordPress <head> hook
      wp_head();

      // Includes critical path scripts that must be executed before the rest
      fictioneer_output_head_critical_scripts();
    ?>
  </head>

  <body <?php body_class( 'site-bg scrolled-to-top' ); ?> data-post-id="<?php echo $post_id ?: -1; ?>">
    <?php wp_body_open(); ?>

    <?php
      // Setup
      $story_id = null;
      $header_image_url = get_header_image();

      // If this is a content page...
      if ( ! empty( $post_id ) ) {
        // Type?
        switch ( $post->post_type ) {
          case 'fcn_story':
            $story_id = $post_id;
            break;
          case 'fcn_chapter':
            $story_id = get_post_meta( get_the_ID(), 'fictioneer_chapter_story', true );
            break;
        }

        // Custom header image?
        if ( get_post_meta( $post_id, 'fictioneer_custom_header_image', true ) ) {
          $header_image_url = get_post_meta( $post_id, 'fictioneer_custom_header_image', true );
          $header_image_url = wp_get_attachment_image_url( $header_image_url, 'full' );
        } elseif ( ! empty( $story_id ) && get_post_meta( $story_id, 'fictioneer_custom_header_image', true ) ) {
          $header_image_url = get_post_meta( $story_id, 'fictioneer_custom_header_image', true );
          $header_image_url = wp_get_attachment_image_url( $header_image_url, 'full' );
        }
      }

      // Filter header image
      if ( ! empty( $post_id ) && $header_image_url ) {
        $header_image_url = apply_filters( 'fictioneer_filter_header_image', $header_image_url, $post_id );
      }

      // Action arguments
      $action_args = array(
        'post_id' => $post_id,
        'story_id' => $story_id,
        'header_image_url' => $header_image_url,
        'header_args' => $args ?? []
      );

      // Includes mobile menu
      do_action( 'fictioneer_body', $action_args );

      // Inline storage tuples
      $inline_storage = array(
        'permalink' => ['data-permalink', esc_url( get_permalink() )],
        'homelink' => ['data-homelink', esc_url( home_url() )],
        'post_id' => ['data-post-id', $post_id],
        'story_id' => ['data-story-id', $story_id]
      );

      // Filter inline storage
      $inline_storage = apply_filters( 'fictioneer_filter_inline_storage', $inline_storage );

      // Prepare inline storage attributes
      $inline_storage = array_map(
        function ( $node ) {
          return $node[0] . '="' . $node[1] . '"';
        },
        $inline_storage
      );
    ?>

    <div id="inline-storage" <?php echo implode( ' ', $inline_storage ); ?> hidden></div>

    <div id="notifications" class="notifications"></div>

    <div id="site" class="site background-texture">

      <?php
        // Includes header background, navigation bar, and site header (with title, logo, etc.)
        do_action( 'fictioneer_site', $action_args );
      ?>
